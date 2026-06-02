<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Equipment Specs | LabSync</title>
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
            --font-head: 'Syne', sans-serif;
            --font-mono: 'DM Mono', monospace;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: var(--font-head);
            padding: 2rem;
        }

        .shell {
            max-width: 700px;
            margin: 0 auto;
        }

        .eyebrow {
            font-family: var(--font-mono);
            font-size: .7rem;
            color: var(--blue);
            text-transform: uppercase;
            margin-bottom: .5rem;
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 800;
            text-transform: uppercase;
            margin-bottom: .25rem;
        }

        h1 span {
            color: var(--muted);
            font-weight: 400;
        }

        .subtitle {
            font-family: var(--font-mono);
            font-size: .75rem;
            color: var(--muted);
            margin-bottom: 2.5rem;
        }

        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 4px;
            padding: 2rem;
            margin-bottom: 1.5rem;
        }

        .card-title {
            font-size: 1rem;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-title::before {
            content: '';
            width: 4px;
            height: 1rem;
            background: var(--blue);
            flex-shrink: 0;
        }

        .spec-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: .6rem 0;
            border-bottom: 1px solid var(--border);
            font-family: var(--font-mono);
            font-size: .8rem;
        }

        .spec-row:last-child {
            border-bottom: none;
        }

        .spec-key {
            color: var(--muted);
        }

        .spec-val {
            color: var(--text);
            font-weight: 500;
        }

        .spec-val .unit {
            color: var(--muted);
            margin-left: 4px;
            font-size: .7rem;
        }

        .badge {
            display: inline-block;
            background: rgba(200, 240, 74, .12);
            color: var(--accent);
            border: 1px solid rgba(200, 240, 74, .3);
            font-size: .65rem;
            padding: .15rem .5rem;
            border-radius: 2px;
            margin-left: .5rem;
        }

        .back-btn {
            font-family: var(--font-mono);
            font-size: .7rem;
            color: var(--muted);
            text-decoration: none;
            display: inline-block;
            margin-bottom: 2rem;
        }

        .back-btn:hover {
            color: var(--text);
        }
    </style>
</head>

<body>
    <div class="shell">

        <a href="javascript:history.back()" class="back-btn">← Back</a>

        <p class="eyebrow">// OO Metadata — Manufacturer Specifications</p>
        <h1>Equipment <span>Specs</span></h1>
        <p class="subtitle">{{ $equipment->name }} &nbsp;·&nbsp; ID:
            EQ-{{ str_pad($equipment->id, 4, '0', STR_PAD_LEFT) }}</p>

        <div class="card">
            <div class="card-title">Electrical Requirements</div>
            <div class="spec-row">
                <span class="spec-key">Voltage Requirement</span>
                <span class="spec-val">220 <span class="unit">V</span></span>
            </div>
            <div class="spec-row">
                <span class="spec-key">Current Draw (max)</span>
                <span class="spec-val">15 <span class="unit">A</span></span>
            </div>
            <div class="spec-row">
                <span class="spec-key">Power Consumption</span>
                <span class="spec-val">3300 <span class="unit">W</span></span>
            </div>
            <div class="spec-row">
                <span class="spec-key">Frequency</span>
                <span class="spec-val">50 / 60 <span class="unit">Hz</span></span>
            </div>
        </div>

        <div class="card">
            <div class="card-title">Precision & Operating Limits</div>
            <div class="spec-row">
                <span class="spec-key">Measurement Precision</span>
                <span class="spec-val">± 0.001 <span class="unit">mm</span></span>
            </div>
            <div class="spec-row">
                <span class="spec-key">Operating Temperature</span>
                <span class="spec-val">15 – 35 <span class="unit">°C</span></span>
            </div>
            <div class="spec-row">
                <span class="spec-key">Max Operating Pressure</span>
                <span class="spec-val">1013 <span class="unit">hPa</span></span>
            </div>
            <div class="spec-row">
                <span class="spec-key">Humidity Range</span>
                <span class="spec-val">20 – 80 <span class="unit">% RH</span></span>
            </div>
        </div>

        <div class="card">
            <div class="card-title">Manufacturer Metadata <span class="badge">OO Metadata</span></div>
            <div class="spec-row">
                <span class="spec-key">Manufacturer</span>
                <span class="spec-val">LabTech Instruments GmbH</span>
            </div>
            <div class="spec-row">
                <span class="spec-key">Model Number</span>
                <span class="spec-val">LT-{{ strtoupper(substr($equipment->name, 0, 3)) }}-9000</span>
            </div>
            <div class="spec-row">
                <span class="spec-key">Serial Number</span>
                <span class="spec-val">SN-{{ str_pad($equipment->id * 7392, 8, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="spec-row">
                <span class="spec-key">Calibration Due</span>
                <span class="spec-val">{{ now()->addMonths(6)->format('d M Y') }}</span>
            </div>
            <div class="spec-row">
                <span class="spec-key">Firmware Version</span>
                <span class="spec-val">v4.2.1</span>
            </div>
        </div>

    </div>
</body>

</html>
