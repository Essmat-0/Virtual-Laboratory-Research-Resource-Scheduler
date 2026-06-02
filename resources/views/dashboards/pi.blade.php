<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PI Terminal | LabSync</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;700;800&family=DM+Mono:wght@400;500&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --bg: #0b0c0f;
            --surface: #111318;
            --border: #1e2028;
            --text: #e8eaf0;
            --muted: #5a5e72;
            --accent: #c8f04a;
            --blue: #4d9eff;
            --red: #ff4d4d;
            --font-head: 'Syne', sans-serif;
            --font-mono: 'DM Mono', monospace;
        }

        * {
            box-sizing: border-box;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: var(--font-head);
            margin: 0;
            min-height: 100vh;
        }

        .shell {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Utility Bar */
        .utility-bar {
            display: flex;
            justify-content: space-between;
            background: var(--surface);
            border: 1px solid var(--border);
            padding: 1rem 1.5rem;
            border-radius: 4px;
            margin-bottom: 2rem;
            font-family: var(--font-mono);
        }

        .stat-label {
            font-size: 0.6rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 1px;
            display: block;
        }

        .stat-value {
            font-size: 1rem;
            color: var(--accent);
            font-weight: 500;
        }

        /* Header */
        header {
            border-bottom: 1px solid var(--border);
            padding-bottom: 2rem;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .eyebrow {
            font-family: var(--font-mono);
            font-size: .7rem;
            color: var(--blue);
            text-transform: uppercase;
            margin: 0;
        }

        h1 {
            font-size: 3rem;
            font-weight: 800;
            margin: 0.5rem 0;
            text-transform: uppercase;
        }

        h1 span {
            color: var(--muted);
            font-weight: 400;
        }

        /* Tabs */
        .tab-nav {
            display: flex;
            gap: 10px;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .tab-btn {
            background: var(--surface);
            border: 1px solid var(--border);
            color: var(--muted);
            padding: 0.8rem 1.2rem;
            font-family: var(--font-mono);
            font-size: 0.7rem;
            cursor: pointer;
            text-transform: uppercase;
            transition: 0.2s;
        }

        .tab-btn.active {
            border-color: var(--blue);
            color: var(--text);
            background: rgba(77, 158, 255, 0.05);
        }

        .tab-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--red);
            color: #fff;
            border-radius: 2px;
            font-size: .55rem;
            font-weight: 700;
            padding: .1rem .35rem;
            margin-left: .5rem;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* Section Card */
        section {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 4px;
            padding: 2.5rem;
        }

        h2 {
            font-size: 1.25rem;
            margin: 0 0 2rem 0;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        h2::before {
            content: '';
            width: 4px;
            height: 1.2rem;
            background: var(--blue);
            flex-shrink: 0;
        }

        /* Form */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .form-group label {
            display: block;
            font-family: var(--font-mono);
            font-size: 0.65rem;
            color: var(--muted);
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .standard-input {
            width: 100%;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 4px;
            padding: 1rem;
            color: var(--text);
            font-family: var(--font-mono);
        }

        .standard-input:focus {
            border-color: var(--blue);
            outline: none;
        }

        .btn-submit {
            background: var(--blue);
            color: #fff;
            border: none;
            padding: 1.2rem;
            font-family: var(--font-mono);
            font-weight: 700;
            text-transform: uppercase;
            width: 100%;
            margin-top: 2rem;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-submit:hover {
            filter: brightness(1.1);
        }

        /* Reservation Items */
        .res-item {
            background: var(--bg);
            border: 1px solid var(--border);
            padding: 1.5rem;
            margin-bottom: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
        }

        .res-data {
            font-family: var(--font-mono);
            flex: 1;
        }

        .res-label {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--text);
            margin: 0 0 4px 0;
        }

        .res-sub {
            font-size: 0.7rem;
            color: var(--muted);
            margin: 4px 0 0 0;
            line-height: 1.6;
        }

        .res-sub span {
            color: var(--text);
        }

        .res-actions {
            display: flex;
            gap: 10px;
            flex-shrink: 0;
        }

        .action-btn {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text);
            padding: 0.6rem 1rem;
            font-family: var(--font-mono);
            font-size: 0.65rem;
            cursor: pointer;
            text-transform: uppercase;
            transition: .15s;
        }

        .action-btn.approve:hover {
            border-color: var(--accent);
            color: var(--accent);
        }

        .action-btn.reject:hover {
            border-color: var(--red);
            color: var(--red);
        }

        /* Publication Items */
        .pub-item {
            background: var(--bg);
            border: 1px solid var(--border);
            padding: 1.25rem 1.5rem;
            margin-bottom: 1rem;
            font-family: var(--font-mono);
        }

        .pub-doi {
            font-size: .85rem;
            font-weight: 700;
            color: var(--accent);
            margin: 0 0 4px 0;
        }

        .pub-meta {
            font-size: .68rem;
            color: var(--muted);
            margin: 0;
            line-height: 1.6;
        }

        .pub-meta span {
            color: var(--text);
        }

        /* Alerts */
        .alert-success {
            background: rgba(77, 158, 255, .1);
            border: 1px solid var(--blue);
            color: var(--blue);
            padding: 1rem;
            margin-bottom: 2rem;
            font-family: var(--font-mono);
            font-size: .85rem;
        }

        .alert-error {
            background: rgba(255, 77, 77, .1);
            border: 1px solid var(--red);
            color: var(--red);
            padding: 1rem;
            margin-bottom: 2rem;
            font-family: var(--font-mono);
            font-size: .85rem;
        }

        .res-empty {
            font-family: var(--font-mono);
            font-size: .8rem;
            color: var(--muted);
            padding: 2rem;
            text-align: center;
            border: 1px dashed var(--border);
            border-radius: 4px;
        }

        .section-divider {
            border: none;
            border-top: 1px solid var(--border);
            margin: 2rem 0;
        }

        /* Grant Allocation rows */
        .grant-row {
            display: flex;
            gap: 1rem;
            align-items: flex-end;
            margin-bottom: .75rem;
        }

        .grant-row .form-group {
            margin: 0;
        }

        .btn-remove-grant {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--red);
            width: 2.2rem;
            height: 2.2rem;
            font-size: 1rem;
            cursor: pointer;
            border-radius: 3px;
            transition: .15s;
            flex-shrink: 0;
        }

        .btn-remove-grant:hover {
            background: rgba(255, 77, 77, .08);
            border-color: var(--red);
        }
    </style>
</head>

<body>
    <div class="shell">

        @php $budget = auth()->user()->PiProfile->budget_limit; @endphp

        {{-- Utility Bar --}}
        <div class="utility-bar">
            <div>
                <span class="stat-label">Pending_Reservations</span>
                <span class="stat-value">{{ $pendingReservations->count() }}</span>
            </div>
            <div>
                <span class="stat-label">Publications_Linked</span>
                <span class="stat-value">{{ $publicationLinks->count() }}</span>
            </div>
            <div>
                <span class="stat-label">Budget</span>
                <span class="stat-value">{{ $budget }}</span>
            </div>
        </div>

        {{-- Header --}}
        <header>
            <div>
                <p class="eyebrow">// Research Oversight</p>
                <h1>PI <span>Dashboard</span></h1>
            </div>
            <x-nav-actions />
        </header>

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="alert-success">&gt; {{ session('success') }}</div>
        @elseif (session('fail'))
            <div class="alert-error">&gt; {{ session('fail') }}</div>
        @elseif (session('grant_expired'))
            <div class="alert-error">&gt; {{ session('grant_expired') }}</div>
        @elseif (session('error'))
            <div class="alert-error">&gt; {{ session('error') }}</div>
        @endif

        {{-- Tabs --}}
        <div class="tab-nav">
            <button class="tab-btn active" onclick="showTab('tab-provision', this)">01_Provisioning</button>
            <button class="tab-btn" onclick="showTab('tab-pending', this)">
                02_Pending_Reservations
                @if ($pendingReservations->count() > 0)
                    <span class="tab-count">{{ $pendingReservations->count() }}</span>
                @endif
            </button>
            <button class="tab-btn" onclick="showTab('tab-publications', this)">03_Publications</button>
            <button class="tab-btn" onclick="showTab('tab-grants', this)">04_Grant_Allocation</button>
            <button class="tab-btn" onclick="showTab('tab-invoice', this)">05_Invoice_Generator</button>
        </div>


        {{-- ══ TAB 1: Provision Researcher ══ --}}
        <div id="tab-provision" class="tab-content active">
            <section>
                <h2>Provision Researcher</h2>
                <form method="POST" action="{{ route('pi.researcher.store') }}">
                    @csrf
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Researcher Name</label>
                            <input type="text" name="user_name" class="standard-input" placeholder="Full Name"
                                value="{{ old('user_name') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Researcher Email</label>
                            <input type="email" name="user_email" class="standard-input"
                                placeholder="name@labsync.sys" value="{{ old('user_email') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Academic Level</label>
                            <input type="text" name="academic_level" class="standard-input"
                                placeholder="e.g., PhD, Post-Doc" value="{{ old('academic_level') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Clearance Level (0–3)</label>
                            <input type="number" name="clearance_level" class="standard-input" min="0"
                                max="3" value="{{ old('clearance_level', 1) }}">
                        </div>
                        <div class="form-group">
                            <label>Initial Password</label>
                            <input type="password" name="user_pass" class="standard-input" placeholder="••••••••">
                        </div>
                        <div class="form-group">
                            <label>Authorization Expiry</label>
                            <input type="date" name="expiry_date" class="standard-input" required>
                        </div>
                    </div>
                    <button type="submit" class="btn-submit">Initialize Researcher Credentials</button>
                </form>
            </section>
        </div>


        {{-- ══ TAB 2: Pending Reservations ══ --}}
        <div id="tab-pending" class="tab-content">
            <section>
                <h2>Pending Approvals</h2>

                @forelse ($pendingReservations as $reservation)
                    <div class="res-item">
                        <div class="res-data">
                            <p class="res-label">{{ optional($reservation->equipment)->name ?? 'Unknown Equipment' }}
                            </p>
                            <p class="res-label">Total Cost:
                                {{ app('App\Services\ReservationService')->calculateCost($reservation) }}</p>
                            <p class="res-sub">
                                Researcher: <span>{{ optional($reservation->user)->name ?? '—' }}</span><br>
                                From:
                                <span>{{ \Carbon\Carbon::parse($reservation->start_time)->format('d M Y, H:i') }}</span>
                                &rarr;
                                <span>{{ \Carbon\Carbon::parse($reservation->end_time)->format('d M Y, H:i') }}</span><br>
                                Submitted: <span>{{ $reservation->created_at->diffForHumans() }}</span>
                            </p>
                        </div>
                        <div class="res-actions">
                            <form method="POST" action="{{ route('pi.reservation.approve', $reservation->id) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="action-btn approve">Approve</button>
                            </form>
                            <form method="POST" action="{{ route('pi.reservation.reject', $reservation->id) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="action-btn reject">Reject</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="res-empty">// NO_PENDING_RESERVATIONS — queue is clear</div>
                @endforelse
            </section>
        </div>


        {{-- ══ TAB 3: Publications ══ --}}
        <div id="tab-publications" class="tab-content">
            <section>
                <h2>Link Publication</h2>
                <form method="POST" action="{{ route('pi.publication.store') }}">
                    @csrf
                    <div class="form-grid">
                        <div class="form-group">
                            <label>DOI</label>
                            <input type="text" name="doi" class="standard-input" placeholder="10.1000/xyz123"
                                value="{{ old('doi') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Equipment Used</label>
                            <select name="equipment_id" class="standard-input" required>
                                <option value="" disabled selected>— Select equipment —</option>
                                @foreach ($usedEquipments as $eq)
                                    <option value="{{ $eq->id }}"
                                        {{ old('equipment_id') == $eq->id ? 'selected' : '' }}>
                                        {{ $eq->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <input type="hidden" name="pi_id" value="{{ auth()->id() }}">
                    <button type="submit" class="btn-submit">Link Publication</button>
                </form>

                @if ($publicationLinks->isNotEmpty())
                    <hr class="section-divider">
                    <h2>Linked Publications</h2>
                    @foreach ($publicationLinks as $link)
                        <div class="pub-item">
                            <p class="pub-doi">{{ $link->doi }}</p>
                            <p class="pub-meta">
                                Equipment: <span>{{ optional($link->equipment)->name ?? '—' }}</span>
                                &nbsp;&middot;&nbsp;
                                Linked: <span>{{ $link->created_at->format('d M Y') }}</span>
                            </p>
                        </div>
                    @endforeach
                @endif
            </section>
        </div>


        {{-- ══ TAB 4: Grant Allocation ══ --}}
        <div id="tab-grants" class="tab-content">
            <section>
                <h2>Grant Allocation</h2>

                @forelse ($unallocatedTransactions as $transaction)
                    <div class="res-item" style="flex-direction:column; align-items:stretch; gap:1.5rem;">

                        {{-- Transaction info --}}
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <div class="res-data">
                                <p class="res-label">
                                    {{ optional(optional($transaction->equipmentSession)->equipment)->name ?? '—' }}
                                </p>
                                <p class="res-sub">
                                    Researcher:
                                    <span>{{ optional(optional($transaction->equipmentSession)->user)->name ?? '—' }}</span><br>
                                    Session ended:
                                    <span>{{ $transaction->created_at?->format('d M Y, H:i') ?? '—' }}</span><br>
                                    Total Cost: <span>${{ number_format($transaction->amount, 2) }}</span>
                                </p>
                            </div>
                            <span style="font-family:var(--font-mono); font-size:.65rem; color:var(--muted);">
                                TXN-{{ str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }}
                            </span>
                        </div>

                        {{-- Allocation form --}}
                        <form method="POST" action="{{ route('pi.transaction.allocate', $transaction->id) }}">
                            @csrf

                            <div id="grant-rows-{{ $transaction->id }}">
                                {{-- First row (always visible) --}}
                                <div class="grant-row">
                                    <div class="form-group" style="flex:2;">
                                        <label>Grant</label>
                                        <select name="allocations[0][grant_id]" class="standard-input" required>
                                            <option value="" disabled selected>— Select grant —</option>
                                            @foreach ($piGrants as $grant)
                                                <option value="{{ $grant->id }}">
                                                    {{ $grant->name }} (Balance:
                                                    ${{ number_format($grant->balance, 2) }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group" style="flex:1;">
                                        <label>Percentage (%)</label>
                                        <input type="number" name="allocations[0][percentage]"
                                            class="standard-input pct-input" min="1" max="100"
                                            placeholder="100" required>
                                    </div>
                                    <span style="display:inline-block; width:2.2rem;"></span>
                                </div>
                            </div>

                            <div
                                style="display:flex; justify-content:space-between; align-items:center; margin-top:1rem;">
                                <button type="button" class="action-btn"
                                    onclick="addGrantRow({{ $transaction->id }})">
                                    + Add Grant
                                </button>
                                <div style="font-family:var(--font-mono); font-size:.72rem; text-align:right;">
                                    <span style="color:var(--muted);">Allocated: </span>
                                    <span id="pct-total-{{ $transaction->id }}"
                                        style="color:var(--accent);">0%</span>
                                    &nbsp;/&nbsp;
                                    <span style="color:var(--muted);">Remaining: </span>
                                    <span id="pct-remaining-{{ $transaction->id }}"
                                        style="color:var(--text);">100%</span>
                                </div>
                            </div>

                            <button type="submit" class="btn-submit" style="margin-top:1.2rem;">
                                Confirm Allocation
                            </button>
                        </form>

                    </div>
                @empty
                    <div class="res-empty">// NO_PENDING_ALLOCATIONS — all transactions are allocated</div>
                @endforelse
            </section>
        </div>


        {{-- ══ TAB 5: Invoice Generator ══ --}}
        <div id="tab-invoice" class="tab-content">
            <section>
                <h2>Invoice Generator</h2>

                <form method="POST" action="{{ route('pi.invoice.generate') }}">
                    @csrf
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Grant</label>
                            <select name="grant_id" class="standard-input" required>
                                <option value="" disabled selected>— Select grant —</option>
                                @foreach ($piGrants as $grant)
                                    <option value="{{ $grant->id }}">{{ $grant->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Billing Month</label>
                            <input type="month" name="month" class="standard-input"
                                value="{{ now()->format('Y-m') }}" required>
                        </div>
                    </div>
                    <button type="submit" class="btn-submit">↓ Generate &amp; Download PDF</button>
                </form>
            </section>
        </div>


    </div>{{-- /shell --}}

    <script>
        // ── Tab switching ──
        function showTab(tabId, btn) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
            document.getElementById(tabId).classList.add('active');
            btn.classList.add('active');
        }

        // Open tab from URL param e.g. ?tab=pending
        const tabParam = new URLSearchParams(window.location.search).get('tab');
        if (tabParam === 'pending') document.querySelectorAll('.tab-btn')[1]?.click();
        if (tabParam === 'publications') document.querySelectorAll('.tab-btn')[2]?.click();
        if (tabParam === 'grants') document.querySelectorAll('.tab-btn')[3]?.click();
        if (tabParam === 'invoice') document.querySelectorAll('.tab-btn')[4]?.click();

        // ── Grant Allocation: add/remove rows + live % counter ──
        const rowCounters = {};

        // Grant options HTML — rendered once by PHP, reused by JS
        const grantOptions = `
        <option value="" disabled selected>— Select grant —</option>
        @foreach ($piGrants as $grant)
            <option value="{{ $grant->id }}">{{ $grant->name }} (Balance: ${{ number_format($grant->balance, 2) }})</option>
        @endforeach
    `;

        function addGrantRow(txnId) {
            if (!rowCounters[txnId]) rowCounters[txnId] = 1;
            const idx = ++rowCounters[txnId];
            const container = document.getElementById(`grant-rows-${txnId}`);

            const row = document.createElement('div');
            row.className = 'grant-row';
            row.innerHTML = `
            <div class="form-group" style="flex:2;">
                <label>Grant</label>
                <select name="allocations[${idx}][grant_id]" class="standard-input" required>
                    ${grantOptions}
                </select>
            </div>
            <div class="form-group" style="flex:1;">
                <label>Percentage (%)</label>
                <input type="number" name="allocations[${idx}][percentage]"
                       class="standard-input pct-input" min="1" max="100" placeholder="0" required>
            </div>
            <button type="button" class="btn-remove-grant" onclick="removeGrantRow(this, ${txnId})">×</button>
        `;

            container.appendChild(row);
            bindPctInputs(txnId);
        }

        function removeGrantRow(btn, txnId) {
            btn.closest('.grant-row').remove();
            updatePct(txnId);
        }

        function updatePct(txnId) {
            const inputs = document.getElementById(`grant-rows-${txnId}`).querySelectorAll('.pct-input');
            const total = Array.from(inputs).reduce((sum, el) => sum + (parseFloat(el.value) || 0), 0);
            const rem = 100 - total;

            const totalEl = document.getElementById(`pct-total-${txnId}`);
            const remEl = document.getElementById(`pct-remaining-${txnId}`);

            totalEl.textContent = total.toFixed(0) + '%';
            remEl.textContent = rem.toFixed(0) + '%';

            totalEl.style.color = total === 100 ? 'var(--accent)' : total > 100 ? 'var(--red)' : 'var(--muted)';
            remEl.style.color = rem === 0 ? 'var(--accent)' : rem < 0 ? 'var(--red)' : 'var(--text)';
        }

        function bindPctInputs(txnId) {
            document.getElementById(`grant-rows-${txnId}`).querySelectorAll('.pct-input').forEach(input => {
                input.oninput = () => updatePct(txnId);
            });
        }

        // Init on load
        document.querySelectorAll('[id^="grant-rows-"]').forEach(container => {
            bindPctInputs(container.id.replace('grant-rows-', ''));
        });
    </script>

</body>

</html>
