<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <style>
        /* ── Reset & Base ── */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #1a1c24;
            background: #fff;
            padding: 0;
        }

        /* ── Cover stripe ── */
        .header-stripe {
            background: #1a1c24;
            color: #fff;
            padding: 28px 40px 22px;
            position: relative;
        }

        .header-top {
            display: flex;
            /* DomPDF doesn't support flex well; use table trick below */
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: top;
        }

        .brand {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: -0.5px;
            color: #c8f04a;
        }

        .brand-sub {
            font-size: 9px;
            color: #6b7080;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-top: 3px;
        }

        .inv-meta {
            text-align: right;
            font-size: 9px;
            color: #8a8f9e;
            line-height: 1.7;
        }

        .inv-meta strong {
            color: #ffffff;
            font-size: 13px;
            display: block;
            margin-bottom: 2px;
        }

        /* ── Accent bar ── */
        .accent-bar {
            background: #c8f04a;
            height: 4px;
        }

        /* ── Party section ── */
        .party-section {
            padding: 20px 40px;
            border-bottom: 1px solid #e8eaf0;
        }

        .party-table {
            width: 100%;
            border-collapse: collapse;
        }

        .party-table td {
            vertical-align: top;
            width: 50%;
            padding-right: 20px;
        }

        .party-label {
            font-size: 8px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #9ca3b0;
            margin-bottom: 5px;
        }

        .party-name {
            font-size: 13px;
            font-weight: 700;
            color: #1a1c24;
            margin-bottom: 2px;
        }

        .party-sub {
            font-size: 9px;
            color: #6b7080;
            line-height: 1.6;
        }

        /* ── Grant info bar ── */
        .grant-bar {
            background: #f4f5f8;
            padding: 12px 40px;
            border-bottom: 1px solid #e8eaf0;
        }

        .grant-bar-table {
            width: 100%;
            border-collapse: collapse;
        }

        .grant-bar-table td {
            vertical-align: middle;
        }

        .grant-chip {
            display: inline-block;
            background: #1a1c24;
            color: #c8f04a;
            font-size: 9px;
            padding: 3px 9px;
            border-radius: 3px;
            letter-spacing: 1px;
            font-weight: 700;
        }

        .grant-name-text {
            font-weight: 700;
            font-size: 12px;
            color: #1a1c24;
            margin-left: 8px;
        }

        .period-text {
            text-align: right;
            font-size: 10px;
            color: #6b7080;
        }

        /* ── Table ── */
        .content {
            padding: 24px 40px;
        }

        .section-heading {
            font-size: 9px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #9ca3b0;
            margin-bottom: 10px;
        }

        .billing-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        .billing-table thead tr {
            background: #1a1c24;
            color: #fff;
        }

        .billing-table thead th {
            padding: 8px 10px;
            text-align: left;
            font-size: 8px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            font-weight: 600;
        }

        .billing-table thead th.right {
            text-align: right;
        }

        .billing-table tbody tr:nth-child(even) {
            background: #f9fafc;
        }

        .billing-table tbody td {
            padding: 9px 10px;
            color: #2a2d3a;
            border-bottom: 1px solid #edeef2;
            vertical-align: middle;
        }

        .billing-table tbody td.right {
            text-align: right;
            font-family: 'DejaVu Sans Mono', monospace;
        }

        .billing-table tbody td.muted {
            color: #9ca3b0;
        }

        .no-records {
            text-align: center;
            padding: 24px;
            color: #9ca3b0;
            font-size: 10px;
            font-style: italic;
        }

        /* ── Totals ── */
        .totals-wrapper {
            margin-top: 16px;
            padding-top: 12px;
        }

        .totals-table {
            width: 40%;
            margin-left: 60%;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 6px 10px;
            font-size: 10px;
        }

        .totals-table .tot-label {
            color: #6b7080;
        }

        .totals-table .tot-val {
            text-align: right;
            font-family: 'DejaVu Sans Mono', monospace;
            color: #1a1c24;
        }

        .totals-table .divider td {
            border-top: 1px solid #e8eaf0;
            padding-top: 8px;
        }

        .grand-row td {
            background: #1a1c24;
            color: #c8f04a !important;
            font-weight: 700;
            font-size: 12px;
            padding: 10px 10px;
        }

        .factor-badge {
            background: rgba(200, 240, 74, 0.15);
            color: #4a7a00;
            border: 1px solid rgba(200, 240, 74, 0.5);
            font-size: 8px;
            padding: 1px 5px;
            border-radius: 2px;
            margin-left: 5px;
        }

        /* ── Footer ── */
        .footer {
            padding: 16px 40px;
            border-top: 1px solid #e8eaf0;
            margin-top: 24px;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-table td {
            vertical-align: bottom;
        }

        .footer-note {
            font-size: 8px;
            color: #b0b4c4;
            line-height: 1.8;
        }

        .footer-stamp {
            text-align: right;
            font-size: 8px;
            color: #c8f04a;
            background: #1a1c24;
            padding: 6px 10px;
            border-radius: 3px;
            font-family: 'DejaVu Sans Mono', monospace;
        }
    </style>
</head>

<body>

    {{-- ── Header stripe ── --}}
    <div class="header-stripe">
        <table class="header-table">
            <tr>
                <td>
                    <div class="brand">LABSYNC</div>
                    <div class="brand-sub">Research Infrastructure Platform</div>
                </td>
                <td>
                    <div class="inv-meta">
                        <strong>INVOICE</strong>
                        INV-{{ str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT) }}<br>
                        Issued: {{ \Carbon\Carbon::now()->format('d M Y') }}<br>
                        Period: {{ $monthLabel }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="accent-bar"></div>

    {{-- ── Party section ── --}}
    <div class="party-section">
        <table class="party-table">
            <tr>
                <td>
                    <div class="party-label">Principal Investigator</div>
                    <div class="party-name">{{ $pi->name }}</div>
                    <div class="party-sub">{{ $pi->email }}<br>LabSync Research System</div>
                </td>
                <td>
                    <div class="party-label">Grant</div>
                    <div class="party-name">{{ $grant->name }}</div>
                    <div class="party-sub">
                        Available Balance: ${{ number_format($grant->balance, 2) }}<br>
                        Report Period: {{ $monthLabel }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- ── Grant bar ── --}}
    <div class="grant-bar">
        <table class="grant-bar-table">
            <tr>
                <td>
                    <span class="grant-chip">GRANT</span>
                    <span class="grant-name-text">{{ $grant->name }}</span>
                </td>
                <td class="period-text">
                    Billing Period: {{ $monthLabel }} &nbsp;|&nbsp; Records: {{ $records->count() }}
                </td>
            </tr>
        </table>
    </div>

    {{-- ── Line items ── --}}
    <div class="content">
        <div class="section-heading">Billing Records — {{ $monthLabel }}</div>

        <table class="billing-table">
            <thead>
                <tr>
                    <th style="width:4%">#</th>
                    <th style="width:26%">Equipment</th>
                    <th style="width:22%">Researcher</th>
                    <th style="width:18%">Session Date</th>
                    <th style="width:15%">Duration</th>
                    <th class="right" style="width:15%">Cost (USD)</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($records as $i => $record)
                    @php
                        $session = $record->equipmentSession;
                        $equip = optional($session?->equipment)->name ?? '—';
                        $researcher = optional($session?->user)->name ?? '—';
                        $date = $record->transaction->created_at?->format('d M Y') ?? '—';

                        $start = $session?->start_time ? \Carbon\Carbon::parse($session->start_time) : null;
                        $end = $session?->end_time ? \Carbon\Carbon::parse($session->end_time) : null;
                        $duration = $start && $end ? $start->diffInMinutes($end) . ' min' : '—';
                    @endphp
                    <tr>
                        <td class="muted">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</td>
                        <td><strong>{{ $equip }}</strong></td>
                        <td>{{ $researcher }}</td>
                        <td class="muted">{{ $date }}</td>
                        <td class="muted">{{ $duration }}</td>
                        <td class="right">${{ number_format($record->total_cost, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="no-records">
                            No billing records found for this grant in {{ $monthLabel }}.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- ── Totals ── --}}
        <div class="totals-wrapper">
            <table class="totals-table">
                <tr>
                    <td class="tot-label">Subtotal</td>
                    <td class="tot-val">${{ number_format($subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td class="tot-label">
                        Normalization Factor
                        <span class="factor-badge">× {{ $normFactor }}</span>
                    </td>
                    <td class="tot-val">${{ number_format($subtotal * $normFactor - $subtotal, 2) }}</td>
                </tr>
                <tr class="divider">
                    <td></td>
                    <td></td>
                </tr>
                <tr class="grand-row">
                    <td class="tot-label">GRAND TOTAL</td>
                    <td class="tot-val">${{ number_format($grandTotal, 2) }}</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- ── Footer ── --}}
    <div class="footer">
        <table class="footer-table">
            <tr>
                <td>
                    <div class="footer-note">
                        This invoice was auto-generated by LabSync on {{ \Carbon\Carbon::now()->format('d M Y, H:i') }}
                        UTC.<br>
                        Normalization Factor {{ $normFactor }} applied per FR-7.2 (University Accounting
                        Standard).<br>
                        For queries contact your lab administrator.
                    </div>
                </td>
                <td style="width:35%; text-align:right;">
                    <div class="footer-stamp">
                        LABSYNC // FR-7.2<br>
                        NORM_FACTOR: {{ $normFactor }}<br>
                        {{ \Carbon\Carbon::now()->format('Y-m-d\TH:i:s') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
