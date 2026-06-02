<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Safety Briefing | LabSync</title>
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
            max-width: 720px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* ── Header ── */
        header {
            border-bottom: 1px solid var(--border);
            padding-bottom: 2rem;
            margin-bottom: 2rem;
        }

        .eyebrow {
            font-family: var(--font-mono);
            font-size: .7rem;
            color: var(--red);
            text-transform: uppercase;
            letter-spacing: .15em;
            margin: 0;
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin: .5rem 0 0 0;
            text-transform: uppercase;
        }

        h1 span {
            color: var(--muted);
            font-weight: 400;
        }

        .category-tag {
            display: inline-block;
            font-family: var(--font-mono);
            font-size: .65rem;
            color: var(--accent);
            border: 1px solid rgba(200, 240, 74, .3);
            background: rgba(200, 240, 74, .06);
            padding: .25rem .7rem;
            border-radius: 2px;
            margin-top: .75rem;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        /* ── Scroll Box ── */
        .briefing-box {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 4px;
            height: 380px;
            overflow-y: scroll;
            padding: 2rem;
            font-family: var(--font-mono);
            font-size: .78rem;
            line-height: 1.9;
            color: var(--text);
            margin-bottom: 1rem;
            scroll-behavior: smooth;
        }

        /* Thin accent scrollbar */
        .briefing-box::-webkit-scrollbar {
            width: 4px;
        }

        .briefing-box::-webkit-scrollbar-track {
            background: var(--bg);
        }

        .briefing-box::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 2px;
        }

        .briefing-box::-webkit-scrollbar-thumb:hover {
            background: var(--muted);
        }

        .briefing-box h3 {
            font-family: var(--font-head);
            font-size: .85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--accent);
            margin: 1.5rem 0 .5rem 0;
        }

        .briefing-box h3:first-child {
            margin-top: 0;
        }

        .briefing-box p {
            margin: 0 0 .75rem 0;
            color: var(--muted);
        }

        .briefing-box p span {
            color: var(--text);
        }

        .briefing-box ul {
            margin: 0 0 1rem 0;
            padding-left: 1.2rem;
            color: var(--muted);
        }

        .briefing-box ul li {
            margin-bottom: .4rem;
        }

        /* Scroll progress hint */
        .scroll-hint {
            font-family: var(--font-mono);
            font-size: .65rem;
            color: var(--muted);
            text-align: center;
            margin-bottom: 1.5rem;
            transition: opacity .3s;
        }

        .scroll-hint.hidden {
            opacity: 0;
            pointer-events: none;
        }

        /* ── Accept Form ── */
        .accept-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 4px;
            padding: 1.5rem 2rem;
        }

        .accept-card p {
            font-family: var(--font-mono);
            font-size: .72rem;
            color: var(--muted);
            margin: 0 0 1.25rem 0;
            line-height: 1.7;
        }

        .accept-card p span {
            color: var(--text);
        }

        .btn-accept {
            background: var(--accent);
            color: #0b0c0f;
            border: none;
            padding: 1rem 2rem;
            font-family: var(--font-mono);
            font-weight: 700;
            font-size: .8rem;
            text-transform: uppercase;
            cursor: pointer;
            border-radius: 3px;
            width: 100%;
            transition: .2s;
            opacity: .4;
            pointer-events: none;
        }

        .btn-accept.enabled {
            opacity: 1;
            pointer-events: all;
        }

        .btn-accept.enabled:hover {
            filter: brightness(1.08);
        }

        .btn-decline {
            display: block;
            text-align: center;
            margin-top: 1rem;
            font-family: var(--font-mono);
            font-size: .65rem;
            color: var(--muted);
            text-decoration: none;
            transition: color .15s;
        }

        .btn-decline:hover {
            color: var(--red);
        }
    </style>
</head>

<body>
    <div class="shell">

        {{-- ── Header ── --}}
        <header>
            <p class="eyebrow">// Safety Protocol</p>
            <h1>Safety <span>Briefing</span></h1>
            <span class="category-tag">{{ $category->name }}</span>
        </header>

        {{-- ── Scrollable Briefing ── --}}
        <div class="briefing-box" id="briefing-scroll">

            <h3>01 — General Lab Safety</h3>
            <p>All personnel must adhere to the safety regulations defined by the laboratory. Failure to comply may
                result in immediate suspension of access privileges.</p>
            <ul>
                <li>Always wear appropriate personal protective equipment (PPE) when operating equipment.</li>
                <li>Never operate equipment without prior training or authorization.</li>
                <li>Report any malfunction, damage, or unusual behavior immediately to the lab manager.</li>
            </ul>

            <h3>02 — Equipment-Specific Hazards</h3>
            <p>Equipment under the <span>{{ $category->name }}</span> category may involve one or more of the
                following hazards. You are expected to be aware of and mitigate each:</p>
            <ul>
                <li>High-voltage electrical components — do not open panels while powered.</li>
                <li>High-pressure systems — inspect seals and connections before each session.</li>
                <li>Chemical exposure risk — consult MSDS sheets before handling reagents.</li>
                <li>Thermal hazards — allow adequate cooldown time before handling heated components.</li>
            </ul>

            <h3>03 — Emergency Procedures</h3>
            <p>In the event of an emergency:</p>
            <ul>
                <li>Immediately cease operation and engage the emergency stop if available.</li>
                <li>Evacuate the area if there is risk of fire, chemical spill, or electrical fault.</li>
                <li>Contact the designated lab safety officer and do not re-enter until cleared.</li>
                <li>All incidents must be logged within 24 hours using the incident report system.</li>
            </ul>

            <h3>04 — Data & Session Responsibility</h3>
            <p>You are personally responsible for your session. This includes:</p>
            <ul>
                <li>Ensuring proper checkout so billing is correctly closed.</li>
                <li>Not sharing your credentials or session access with others.</li>
                <li>Leaving equipment in a clean, operational state after use.</li>
            </ul>

            <h3>05 — Acknowledgement & Legal Notice</h3>
            <p>By accepting below, you confirm that you have <span>read and understood</span> all safety requirements
                for equipment in the <span>{{ $category->name }}</span> category. This acknowledgement is
                timestamped and forms part of your legal audit trail within LabSync.</p>
            <p>Misuse of equipment following acknowledgement may result in disciplinary action and removal of lab
                access.</p>

        </div>

        {{-- Scroll progress hint --}}
        <p class="scroll-hint" id="scroll-hint">↓ Scroll to the bottom to enable acceptance</p>

        {{-- ── Accept Form ── --}}
        <div class="accept-card">
            <p>
                Logged in as: <span>{{ auth()->user()->name }}</span><br>
                Category: <span>{{ $category->name }}</span><br>

            </p>

            <form method="POST" action="{{ route('safety.acknowledge', $category->id) }}">
                @csrf
                <input type="hidden" name="intended" value="{{ old('intended') }}">

                <button type="submit" class="btn-accept" id="accept-btn" disabled>
                    I Accept — Proceed to Booking
                </button>
            </form>

            <a href="{{ route('equipment.index') }}" class="btn-decline">
                Decline &amp; return to equipment list
            </a>
        </div>

    </div>

    <script>
        // ── Unlock accept button only after scrolling to the bottom ──
        const box = document.getElementById('briefing-scroll');
        const btn = document.getElementById('accept-btn');
        const hint = document.getElementById('scroll-hint');

        box.addEventListener('scroll', () => {
            // threshold: within 40px of the bottom
            const atBottom = box.scrollTop + box.clientHeight >= box.scrollHeight - 40;
            if (atBottom) {
                btn.disabled = false;
                btn.classList.add('enabled');
                hint.classList.add('hidden');
            }
        });
    </script>

</body>

</html>
