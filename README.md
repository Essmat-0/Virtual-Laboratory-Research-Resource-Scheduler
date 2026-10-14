# Virtual Laboratory Research Resource Scheduler - VLRRS

About
A robust, enterprise-grade multi-tiered web application built on the Model-View-Controller + Service, Observers, Facade, Singleton design patterns.
This platform acts as an integrated ecosystem that bridges scientific research administration with institutional resource scheduling, grant-based financials, and strict safety compliance metrics.
---

## Architecture & Design Patterns

The system is engineered for low coupling and high cohesion, dividing core business processes into standalone logic layers backed by robust structural design patterns:

* **Multi-Tiered MVC Architecture:** Separation of concerns between presentation templates (Views), route handling validation (Controllers), core system services (Business Logic), and data schemas (Models) to ensure modular maintenance.
* **Singleton Pattern:** Implemented at the persistent storage layer to manage a centralized, memory-leak-safe database connection pool.
* **Hyper-Static Proxy-Observer Pattern:** A specialized hybrid pattern. **Proxies** evaluate structural safety conditions and interlock states before allowing access to operations, while **Observers** track real-time active state transitions, dispatch safety alerts, and manage runtime heartbeats.

---

## Core Functional Modules

The platform's 35+ core capabilities are systematically isolated into seven modular domains:

1.  **Core Equipment & Safety Control:** Controls physical-to-software resource states (`Idle`, `Active`, `Maintenance`, `Locked`). It manages sequential booking workflows (e.g., sample prep stations must be reserved prior to analytical machinery) and blocks operations unless a user holds an unexpired certification.
2.  **Resource Lifecycle & Automated Logistics:** Monitors real-time asset health portfolios, logs historical failures, and utilizes a **Session Heartbeat** (via periodic browser signals) to automatically terminate ghost bookings. It enforces non-bookable pre- and post-session cool-down/power-up buffers for sensitive high-temperature hardware.
3.  **Basic Financials (The 13.37 Engine):** Features a consumption-based rate calculator mapped to an instructor-mandated `13.37` normalization scaling factor. Supports complex multi-source grant partitioning (splitting a single usage bill across multiple grant balances by custom percentages) and handles hard-cap fund validation.
4.  **Compliance & Authorization Workflows:** Maps student identities to an institutional Principal Investigator (PI) verification state-machine. Bookings exceeding cost or risk thresholds are held in a `Pending` state until explicitly signed off by a faculty PI.
5.  **Operational Analytics & Impact Reporting:** Aggregates background data to compile side-by-side multi-facility uptime reports, CSS-based resource utilization heatmaps, and ROI trackers. It also generates **Downtime Impact Reports** to mathematically calculate lost research value using the scaling engine.
6.  **System Administration & Data Integrity:** Houses the central Role-Based Access Control (RBAC) engine. Employs unalterable database console triggers to enforce non-repudiation across system logs, manages data archiving routines, and exposes an atomic global **Emergency Lockout Kill-Switch**.
7.  **Logistics & Workflow Procurement:** Tracks peripheral equipment assets and consumable stock lists, triggering automated auto-deductions (e.g., sequencing kits) upon session completion. Handles monthly invoice generation and tracks disputed charge adjustment requests.

---

## Non-Functional & Security Metrics

* **Authentication & Privacy:** Zero plain-text credentials. Forced implementation of industry-standard secure cryptography via `password_hash()` and `password_verify()`, storing strings at a minimum of 60 characters.
* **Robust Access Boundaries:** Strict application routing checks. Unauthorized attempts by a `Student` session token to fetch administrative or financial paths yield an absolute `403 Forbidden` response.
* **Performance Benchmarks:** Server Time to First Byte (TTFB) optimized under 800ms for analytical dashboards, with a target threshold where 95% of concurrent queries execute in under 2 seconds. Multi-join asset registry reporting structures are optimized to execute under 500ms.

---

## Tech Stack & Engineering Environment

* **Backend Application Layer:** Laravel Ecosystem (Object-Oriented Programming)
* **Persistent Storage Layer:** MySQL Database Server
* **Presentation / Interface Layer:** Clean, responsive semantic views utilizing Tailwind CSS / Bootstrap grids
* **Asynchronous Communication:** AJAX-driven asynchronous transactions for dynamic session logging
