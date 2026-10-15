<div align="center">

# LabSync

### Virtual Laboratory & Research Resource Scheduler (VLRRS)

**A university-grade lab operations platform where every booking is a safety decision, a financial transaction, and a compliance event — not a database row.**

[![Tests](https://github.com/Essmat-0/Virtual-Laboratory-Research-Resource-Scheduler-VLRRS-/actions/workflows/tests.yml/badge.svg)](https://github.com/Essmat-0/Virtual-Laboratory-Research-Resource-Scheduler-VLRRS-/actions/workflows/tests.yml)
[![Lint](https://github.com/Essmat-0/Virtual-Laboratory-Research-Resource-Scheduler-VLRRS-/actions/workflows/lint.yml/badge.svg)](https://github.com/Essmat-0/Virtual-Laboratory-Research-Resource-Scheduler-VLRRS-/actions/workflows/lint.yml)

![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php&logoColor=white)
![Livewire](https://img.shields.io/badge/Livewire-Volt%20%2B%20Flux-4e56a6)
![MySQL](https://img.shields.io/badge/MySQL-8-4479A1?logo=mysql&logoColor=white)
![Tailwind](https://img.shields.io/badge/Tailwind-4-38BDF8?logo=tailwindcss&logoColor=white)

</div>

---

## Why this system is hard

Most scheduling software asks one question: *is the slot free?*

LabSync asks seven, before it will write a single row:

1. Is the hardware in a legal state? (`Idle` / `Active` / `Maintenance` / `Locked`)
2. Has the researcher acknowledged the safety briefing for this equipment category?
3. Does the researcher's clearance level meet the equipment's required clearance?
4. Has the equipment finished its post-session **cool-down buffer**?
5. Does an **unexpired** funding grant back this session?
6. Does that grant actually hold enough remaining balance?
7. Is a Principal Investigator willing to sign off on it?

And that's only *before* the booking. After the session ends, the system keeps working: it stops the billing clock, allocates cost across multiple grants, recomputes equipment ROI, draws down consumable stock, writes a tamper-evident audit entry, and returns the asset to service.

This README focuses on that logic — the parts that were genuinely difficult to build.

---

## Core Business Logic

### 1. The Ghost-Session Reaper — a self-healing resource lifecycle

Equipment left running by an absent researcher is money and availability leaking away. LabSync runs a **minute-level scheduler** (`routes/console.php`) that closes the loop without any human intervention:

- The browser sends a **heartbeat** (`POST /heartbeat`) on an interval while the researcher is active, refreshing their `updated_at`.
- If a user goes silent past the inactivity cutoff, the scheduler:
  - finds every still-open session they own,
  - stamps an end time, computes the real duration,
  - **bills it automatically** (raw cost + normalized amount),
  - flips the equipment back to `Idle` and restores its available quantity,
  - deactivates the stale account.

The same sweep also converts due `Approved` reservations into live sessions, and bills any completed session that never produced a transaction — so **billing can never silently fall through the cracks**.

```php
// routes/console.php — runs every minute
Schedule::call(function () {
    app(ConsumableService::class)->checkStock();

    $cutoff = now()->subMinutes(15);
    $inactiveUserIds = User::where('is_active', true)->where('updated_at', '<', $cutoff)->pluck('id');

    EquipmentSession::whereIn('user_id', $inactiveUserIds)
        ->whereNull('end_time')
        ->each(function ($session) {
            $session->update(['end_time' => now()]);
            $durationInHours = $session->start_time->diffInMinutes(now()) / 60;
            $cost = $durationInHours * $session->equipment->hourly_rate;

            $session->equipment->update(['status' => 'Idle']);
            Transaction::create([
                'session_id'        => $session->id,
                'user_id'           => $session->user_id,
                'amount'            => $cost,
                'normalized_amount' => $cost * config('app.normalization_factor'),
            ]);
        });
})->everyMinute();
```

### 2. Atomic Multi-Grant Allocation — splitting one bill across many funders

Real research is co-funded. A single session may be paid 60% by Grant A and 40% by Grant B. `TransactionService::allocate()` enforces an entire accounting ruleset **inside a single database transaction**:

- percentages must sum to exactly **100%**,
- the same grant may not appear twice in one split,
- every grant must be **unexpired**,
- every grant must hold **enough remaining balance** for its share,
- prior allocation attempts are wiped before re-applying (idempotent re-allocation),
- each grant's balance is decremented atomically.

If any rule fails, the whole operation rolls back — no partial charges, ever.

```php
public function allocate(Transaction $transaction, array $allocations): void
{
    if (round(array_sum(array_column($allocations, 'percentage')), 2) !== 100.00) {
        throw new \Exception("Grant allocations must sum to 100%.");
    }

    // ... uniqueness + expiry + balance hard-cap validation ...

    DB::transaction(function () use ($transaction, $allocations) {
        TransactionGrant::where('transaction_id', $transaction->id)->delete();
        foreach ($allocations as $allocation) {
            $amount = round(($allocation['percentage'] / 100) * $transaction->amount, 2);
            TransactionGrant::create([...]);
            Grant::findOrFail($allocation['grant_id'])->decrement('balance', $amount);
        }
    });
}
```

### 3. Normalization Engine — one domain constant, one source of truth

Every financial figure in the system is scaled by a **domain-mandated normalization factor**, resolved once from `config('app.normalization_factor')` rather than hard-coded in each formula. Costs are stored as both the raw amount and the normalized amount, so aggregate reporting (`monthlyGrantSummary`) stays consistent across the entire platform.

### 4. Observer Cascade — behavior that happens *to* your data

The system uses Laravel's observer pattern to keep derived state truthful. Write one thing; dependent state updates itself:

| Observer | Trigger | Cascading effect |
|---|---|---|
| `EquipmentSessionObserver` | session created / updated / deleted | recalculates the equipment's **total usage hours** from the full session history |
| `EquipmentObserver` | equipment's usage hours change | recomputes **ROI** = usage-hours ÷ maintenance-cost × factor, then writes a `KEEP` / `REVIEW` recommendation against a minimum-ROI threshold |
| `TransactionObserver` | transaction created / amount altered | writes an **audit trail** entry; an altered amount is flagged `CRITICAL` with the before/after values |

Usage hours become the single trigger that drives **both** maintenance signaling and procurement insight — no manual recalculation anywhere.

### 5. Hardware Interlock & Safety Gate

Equipment moves through a real state machine — `Idle`, `Active`, `Maintenance`, `Locked` — and the state is *enforced*, not decorative:

- **Cool-down guard** — `EquipmentService::canEquipmentBeUsed()` blocks a session start until the equipment's configured cool-down buffer has elapsed since its last session.
- **Safety-briefing gate** — the `EnsureSafetyLog` middleware intercepts a booking attempt, and if the user hasn't acknowledged that category's briefing, redirects them to it with their intended destination preserved.
- **Clearance gate** — a user's `clearance_level` is checked against the equipment's `required_clearance` before access.

### 6. Emergency Kill-Switch

One action, total lockdown. `LabMService::emergencyLockout()` is the safety valve for a real lab incident (gas leak, fire): it locks **all** equipment to `Maintenance`, force-ends **every** open session, and cancels all future reservations in a single sweep.

### 7. Principal Investigator Approval Workflow

PI approval isn't a checkbox. Before a reservation can leave `Pending`, `PiService::approve()` verifies the PI still has budget headroom **and** at least one unexpired grant. Rejections restore the held equipment quantity. Approval decisions are driven through dedicated, role-gated endpoints.

### 8. Tamper-Evident Billing Audit

The `TransactionObserver` mirrors billing activity into an immutable `Audit_Trails` table capturing user, action, timestamp, and IP. Changing a transaction amount after the fact produces a `CRITICAL` record — the system is built so that *non-repudiation is structural, not optional*.

### 9. Consumable Auto-Deduction

Completing a session automatically draws down the equipment's associated consumables (glue, targets, pellets) from stock, guarded by a `stock_reduced` flag so a session can never be charged twice — even if the scheduler runs repeatedly.

---

## Architecture

A **layered multi-tier MVC** design, with business logic deliberately pushed out of controllers into a dedicated service layer — so the hard rules live in one testable place.

```
app/
├── Http/
│   ├── Controllers/          # thin controllers — orchestration only
│   └── Middleware/           # RoleMiddleware, EnsureSafetyLog, TrackSessionStatus
├── Services/                 # THE BUSINESS LOGIC
│   ├── ReservationService    # booking + cost + reservation→session
│   ├── TransactionService    # multi-grant allocation + normalization
│   ├── EquipmentService      # cool-down / usability guard
│   ├── LabMService           # inventory + emergency lockout
│   ├── PiService             # approval workflow, researchers, publications
│   ├── ConsumableService     # stock auto-deduction
│   └── AdminService / AuthService / GrantService
├── Observers/                # Equipment, EquipmentSession, Transaction
├── Models/                   # 19 Eloquent models
└── Livewire/                 # Volt settings (2FA, profile, password)
```

**Middleware pipeline** — `role:Admin`, `role:PI`, `role:Lab_Manager`, `role:Auditor`, `role:Researcher`, `safety.acknowledged`, plus global session tracking.

**Design patterns applied:** MVC, Service Layer, Observer, Singleton (centralized DB connection), Proxy-style access gating for interlocks, and State Machine for equipment lifecycle.

---

## Roles & Dashboards

The system ships **five distinct, permission-scoped personas**:

| Role | Responsibility |
|---|---|
| **Admin** | User provisioning, system oversight, RBAC management |
| **Principal Investigator (PI)** | Approves reservations, manages researchers, links publications, allocates grant spending, generates invoices |
| **Researcher** | Books equipment, acknowledges safety briefings, runs sessions, heartbeats activity |
| **Lab Manager** | Equipment inventory & status, maintenance, utilization heatmap, emergency lockout |
| **Auditor** | Reviews the audit trail and exports compliance PDFs |

---

## Feature Map

- **Equipment & Safety** — multi-state interlocks, cool-down buffers, clearance levels, forced safety briefings
- **Resource Lifecycle** — session heartbeat, ghost-session reaping, usage-hours tracking, health portfolios
- **Financials** — consumption-based billing, normalization engine, multi-grant partitioning, balance hard-caps, monthly grant summaries, PDF invoices
- **Compliance & Authorization** — PI approval state machine, certification-expiry notifications, tamper-evident audit trails
- **Operational Analytics** — utilization heatmap, equipment ROI + procurement recommendations, publication linkage
- **System Administration** — RBAC, emergency kill-switch, audit logging
- **Workflow & Procurement** — consumable auto-deduction, invoicing, grant lifecycle enforcement

> **Roadmap (designed in the SRS, being finalized):** sequential (parent/child) booking dependencies, downtime impact reporting, cross-facility aggregation, and proactive procurement alerting.

---

## Non-Functional & Security Posture

- **Credentials** — hashed via `password_hash()` / `password_verify()`; plain-text never stored.
- **Access control** — unauthorized role access returns `403`, enforced at the routing layer, not the UI.
- **Automation reliability** — billing, reaping, and reservation conversion run on a resilient every-minute schedule with idempotency guards.
- **Two-factor authentication** — available on user accounts via Fortify.
- **Performance targets** — analytics dashboard `TTFB` under 800 ms; multi-table reporting queries optimized under 500 ms.

---

## Tech Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 12 |
| Language | PHP 8.2+ |
| Auth | Laravel Fortify (2FA + recovery codes) |
| UI | Livewire Volt + Flux, Blade |
| Styling | Tailwind CSS 4 + Vite |
| Database | MySQL |
| PDF | barryvdh/laravel-dompdf |
| Quality | PHPUnit, Laravel Pint, GitHub Actions (tests + lint) |

---

## Getting Started

```bash
# 1. Clone
git clone https://github.com/Essmat-0/Virtual-Laboratory-Research-Resource-Scheduler-VLRRS-.git
cd Virtual-Laboratory-Research-Resource-Scheduler-VLRRS-

# 2. One-shot setup (install deps, env, key, migrate, build assets)
composer setup

# 3. Seed rich demo data (equipment, grants, sessions, transactions, audit logs)
php artisan db:seed

# 4. Run the app + queue + scheduler
php artisan serve
php artisan schedule:work   # REQUIRED — drives reaping, billing, expiry mail
```

> ⚠️ The scheduler is not optional. It runs the ghost-session reaper, the reservation→session converter, automatic billing, consumable deduction, and the daily certification-expiry emails.

### Demo Credentials

All seeded accounts use the password **`password`**.

| Role | Email |
|---|---|
| Admin | `alice@lab.com` |
| Principal Investigator | `bob@lab.com` |
| Principal Investigator | `pi@lab.com` |
| Researcher | `carol@lab.com` |
| Lab Manager | `dan@lab.com` |
| Auditor | `auditor@lab.com` |

---

## Testing & CI

```bash
php artisan test      # PHPUnit feature & unit tests
vendor/bin/pint       # code style
```

Two GitHub Actions workflows run on every push and pull request:
- **tests** — spins up PHP 8.4 + Node 22, builds assets, runs the full suite.
- **linter** — enforces code style with Laravel Pint.

---

<div align="center">

**Built as a Software Engineering capstone — designed to a formal IEEE 830 SRS, implemented as a production-shaped Laravel application.**

</div>
