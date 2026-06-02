<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Terminal | LabSync</title>
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
            /* electric lime */
            --red: #ff4d5a;
            --font-head: 'Syne', sans-serif;
            --font-mono: 'DM Mono', monospace;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: var(--font-head);
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        /* Noise grain overlay from your welcome page */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 0;
        }

        .shell {
            position: relative;
            z-index: 1;
            max-width: 800px;
            margin: 0 auto;
            padding: 4rem 2rem;
        }

        header {
            border-bottom: 1px solid var(--border);
            padding-bottom: 2rem;
            margin-bottom: 3rem;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .eyebrow {
            font-family: var(--font-mono);
            font-size: .7rem;
            letter-spacing: .18em;
            color: var(--accent);
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

        section {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 4px;
            padding: 2.5rem;
            margin-bottom: 2.5rem;
        }

        h2 {
            font-family: var(--font-head);
            font-size: 1.25rem;
            margin-bottom: 2rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        h2::before {
            content: '';
            width: 4px;
            height: 1.2rem;
            background: var(--accent);
            display: inline-block;
        }

        /* ── Terminal Input Style (Delete Section) ── */
        .terminal-input-group {
            display: flex;
            border: 1px solid var(--border);
            background: var(--bg);
            border-radius: 4px;
            overflow: hidden;
            transition: border-color 0.3s;
        }

        .terminal-input-group:focus-within {
            border-color: var(--accent);
        }

        .field-core {
            flex: 1;
            padding: 12px 16px;
        }

        .field-core label {
            display: block;
            font-family: var(--font-mono);
            font-size: 0.65rem;
            color: var(--muted);
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .field-core input {
            width: 100%;
            background: transparent;
            border: none;
            color: var(--text);
            font-family: var(--font-mono);
            font-size: 1rem;
            outline: none;
        }

        .btn-terminate {
            background: var(--border);
            border: none;
            border-left: 1px solid var(--border);
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 30px;
            cursor: pointer;
            transition: all 0.2s ease;
            font-family: var(--font-mono);
            font-weight: 700;
            font-size: 0.8rem;
            letter-spacing: 1px;
        }

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


        .btn-terminate:hover {
            background: var(--red);
            color: #fff;
        }

        /* ── Form Controls (Add Section) ── */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .full-width {
            grid-column: span 2;
        }

        .standard-input {
            width: 100%;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 4px;
            padding: 1rem;
            color: var(--text);
            font-family: var(--font-mono);
            outline: none;
            margin-top: 0.5rem;
        }

        .standard-input:focus {
            border-color: var(--accent);
        }

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

        .btn-primary {
            background: var(--accent);
            color: var(--bg);
            border: none;
            padding: 1.2rem;
            font-family: var(--font-mono);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            margin-top: 2rem;
            transition: transform 0.2s, filter 0.2s;
        }

        .btn-primary:hover {
            filter: brightness(1.1);
            transform: translateY(-2px);
        }

        /* ── Custom Radio Buttons ── */
        .radio-button-container {
            display: flex;
            gap: 24px;
            margin-bottom: 2rem;
        }

        .radio-button {
            position: relative;
            cursor: pointer;
        }

        .radio-button__input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .radio-button__label {
            display: inline-block;
            padding-left: 30px;
            position: relative;
            font-size: 14px;
            color: var(--text);
            font-family: var(--font-mono);
            font-weight: 500;
            cursor: pointer;
            text-transform: uppercase;
        }

        .radio-button__custom {
            position: absolute;
            top: 0;
            left: 0;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 2px solid var(--border);
            transition: 0.3s;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .radio-button__input:checked+.radio-button__label .radio-button__custom {
            background-color: var(--accent);
            border-color: transparent;
            box-shadow: 0 0 15px var(--accent);
        }

        .radio-button__input:checked+.radio-button__label {
            color: var(--accent);
        }

        .alert {
            background: rgba(200, 240, 74, 0.1);
            border: 1px solid var(--accent);
            color: var(--accent);
            padding: 1rem;
            margin-bottom: 2rem;
            font-family: var(--font-mono);
            font-size: 0.85rem;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <div class="shell">
        <header>
            <div>
                <p class="eyebrow">// Root Authority</p>
                <h1>Admin <span>Panel</span></h1>
            </div>

            <x-nav-actions />
        </header>
        <div class="tab-nav">
            <button class="tab-btn active" onclick="showTab('tab-provision', this)">01_USER_MANAGMENT</button>
            <button class="tab-btn" onclick="showTab('tab-showDownTime', this)">02_DOWNTIME_REPORT</button>
            <button class="tab-btn" onclick="showTab('tab-roiReport', this)">03_ROI_REPORT</button>
        </div>
        <div id="tab-provision" class="tab-content active">
            <section>
                <h2>Access Termination</h2>
                @if (session('successDelete'))
                    <div class="alert">
                        STATUS_OK: {{ session('successDelete') }}
                    </div>
                @endif
                <form method="POST" action="/adminDeleteUser">
                    @csrf
                    @method('DELETE')
                    <div class="terminal-input-group">
                        <div class="field-core">
                            <label for="user_id">Target User UID</label>
                            <input type="text" id="user_id" name="user_id" placeholder="SYS_USR_00" required>
                        </div>
                        <button type="submit" class="btn-terminate">
                            <span>TERMINATE</span>
                            <svg width="14" viewBox="0 0 448 512" fill="currentColor">
                                <path
                                    d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z">
                                </path>
                            </svg>
                        </button>
                    </div>
                </form>
            </section>

            <section>
                <h2>Provision User</h2>

                @if (session('success'))
                    <div class="alert">
                        STATUS_OK: {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf

                    <div class="radio-button-container">
                        <div class="radio-button">
                            <input type="radio" class="radio-button__input" id="radioPI" name="user_role"
                                value="PI" onchange="toggleFields('PI')" required>
                            <label class="radio-button__label" for="radioPI">
                                <span class="radio-button__custom"></span>
                                PI
                            </label>
                        </div>
                        <div class="radio-button">
                            <input type="radio" class="radio-button__input" id="radioLabM" name="user_role"
                                value="Lab_Manager" onchange="toggleFields('LabM')" required>
                            <label class="radio-button__label" for="radioLabM">
                                <span class="radio-button__custom"></span>
                                Lab Manager
                            </label>
                        </div>
                        <div class="radio-button">
                            <input type="radio" class="radio-button__input" id="radioAuditor" name="user_role"
                                value="Auditor" onchange="toggleFields('Auditor')" required>
                            <label class="radio-button__label" for="radioAuditor">
                                <span class="radio-button__custom"></span>
                                Auditor
                            </label>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="full-width">
                            <label>User Identity</label>
                            <input type="text" name="user_name" class="standard-input" placeholder="Enter full name"
                                required>
                        </div>

                        <div>
                            <label>Network Email</label>
                            <input type="email" name="user_email" class="standard-input"
                                placeholder="email@labsync.sys" required>
                        </div>

                        <div>
                            <label>Initial Passcode</label>
                            <input type="password" name="user_pass" class="standard-input" placeholder="••••••••"
                                required>
                        </div>
                        <div class="full-width">
                            <label>System Previliges</label>
                            <input type="text" name="system_priviliges" class="standard-input"
                                placeholder="superadmin" required>
                        </div>

                        <div class="full-width">
                            <label>Authorization Expiry</label>
                            <input type="date" name="expiry_date" class="standard-input" required>
                        </div>

                        <div id="pi_fields" style="display:none;" class="full-width">
                            <label>Budget Allocation ($)</label>
                            <input type="number" id="budget_input" name="budget_limit" class="standard-input"
                                placeholder="5000">
                            <label>Affiliation</label>
                            <input type="text" id="aff_input" name="affiliation" class="standard-input"
                                placeholder="Physics Dept">
                        </div>

                        <div id="labm_fields" style="display:none;" class="full-width">
                            <label>Assigned Lab Sectors</label>
                            <input type="text" id="lab_input" name="lab_locations" class="standard-input"
                                placeholder="Sector A, Sector B">
                        </div>
                        <div id="auditor_fields" style="display:none;" class="full-width">
                            <label>Audit Scope</label>
                            <input type="text" id="audit_input" name="audit_scope" class="standard-input"
                                placeholder="m3rfsh">
                        </div>
                    </div>

                    <button type="submit" class="btn-primary">Initialize Credentials</button>
                </form>
            </section>
        </div>

        <div id="tab-showDownTime" class="tab-content">
            @forelse ($impactData as $data)
                <div class="res-item">
                    <div class="res-data">
                        <tr>
                            Equipment Name <td>{{ $data['equipment']->name }}</td> <br>
                            Down Time <td>{{ number_format($data['downTime'], 2) }} hours</td> <br>
                            Impact Score<td>${{ number_format($data['impact'], 2) }}</td><br>
                        </tr>
                        <br>
                    </div>
                </div>
            @empty
                <span> There are no Equipments that has been under maintenance yet. EL7</span>
            @endforelse
        </div>
    </div>
    <div id="tab-roiReport" class="tab-content">
        @php
            $min_threshold = config('app.min_roi');
        @endphp
        <section>
            <table class="table">
                <thead>
                    <tr>
                        <th>Equipment Name</th>
                        <th>Current ROI</th>
                        <th>Min ROI (Threshold)</th>
                        <th>Recommendation</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($roiReport as $report)
                        <tr>
                            <td>{{ $report->equipment->name }}</td>
                            <td>{{ number_format($report->roi_score, 2) }}</td>
                            <td>{{ $min_threshold }}</td>
                            <td>{{ $report->recommendation }}</td>
                            <td>
                                @if ($report->roi_score < $min_threshold)
                                    <span class="badge bg-danger">Alert: Procurement Suggested</span>
                                @else
                                    <span class="badge bg-success">Healthy</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    </div>
    <script>
        function showTab(tabId, btn) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
            document.getElementById(tabId).classList.add('active');
            btn.classList.add('active');
        }

        function toggleFields(role) {
            const piDiv = document.getElementById('pi_fields');
            const labDiv = document.getElementById('labm_fields');
            const bIn = document.getElementById('budget_input');
            const aIn = document.getElementById('aff_input');
            const lIn = document.getElementById('lab_input');
            const AuDiv = document.getElementById('auditor_fields');
            const AuIn = document.getElementById('audit_input');

            if (role === 'PI') {
                piDiv.style.display = 'block';
                labDiv.style.display = 'none';
                AuDiv.style.display = 'none';
                bIn.required = true;
                aIn.required = true;
                lIn.required = false;
                AuIn.required = false;
            } else if (role === 'LabM') {
                piDiv.style.display = 'none';
                labDiv.style.display = 'block';
                AuDiv.style.display = 'none';
                lIn.required = true;
                bIn.required = false;
                aIn.required = false;
                AuIn.required = false;
            } else if (role === 'Auditor') {
                piDiv.style.display = 'none';
                labDiv.style.display = 'none';
                AuDiv.style.display = 'block';
                lIn.required = false;
                bIn.required = false;
                aIn.required = false;
                AuIn.required = true;
            }
        }
    </script>
</body>

</html>
