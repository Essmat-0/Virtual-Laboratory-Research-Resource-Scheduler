<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Auditor Dashboard — Lab Management</title>
    <link rel="stylesheet" href="{{ asset('css/auditor-dashboard.css') }}">
</head>

<body>

    @php
        // ── Derived stats from equipment table only
        $totalEquipment = $equipment->count();
        $available = $equipment->where('status', 'Available')->count();
        $inMaintenance = $equipment->where('status', 'Maintenance')->count();
        $inUse = $equipment->where('status', 'In Use')->count();

        $totalHourlyPool = $equipment->sum('hourly_rate'); // sum of all rates
        $avgRate = $totalEquipment ? $equipment->avg('hourly_rate') : 0;
        $maxRate = $equipment->max('hourly_rate');
        $minRate = $equipment->min('hourly_rate');

        // Clearance distribution
        $clr1 = $equipment->where('required_clearance', 1)->count();
        $clr2 = $equipment->where('required_clearance', 2)->count();
        $clr3 = $equipment->where('required_clearance', 3)->count();

        // Top / bottom earners
        $topEarners = $equipment->sortByDesc('hourly_rate')->take(5);
        $bottomEarners = $equipment->sortBy('hourly_rate')->take(5);
    @endphp

    <div class="layout">

        {{-- ══ Sidebar ══ --}}
        <aside class="sidebar">
            <div class="sidebar-brand">
                <span class="brand-label">Lab Management</span>
                <span class="brand-name">Finance Audit</span>
            </div>

            <div class="sidebar-section">
                <span class="sidebar-section-label">Overview</span>
                <a href="#overview" class="nav-item active"><span class="nav-dot"></span>Dashboard</a>
                <a href="#equipment" class="nav-item"><span class="nav-dot"></span>Equipment Rates</a>
                <a href="#analytics" class="nav-item"><span class="nav-dot"></span>Rate Analytics</a>
                <a href="#audit-trail" class="nav-item"><span class="nav-dot"></span>Audit Trail</a>
            </div>

            <div class="sidebar-footer">
                Logged in as<br>{{ auth()->user()->name ?? 'Auditor' }}
            </div>

        </aside>

        {{-- ══ Main ══ --}}
        <div class="main">

            {{-- Topbar --}}
            <div class="topbar">
                <div class="topbar-left">
                    <span class="topbar-title">Auditor Dashboard</span>
                    <span class="topbar-sub">{{ now()->format('l, d F Y — H:i') }} · Read-only view</span>
                </div>
                <div class="topbar-right">
                    <span class="topbar-badge">AUDITOR</span>
                    <div class="avatar">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}

                    </div>
                    <x-nav-actions />

                </div>
            </div>

            <div class="content">

             
                <div id="overview">
                    <div class="section-head">
                        <span class="section-title">Financial Overview</span>
                        <span style="font-family:var(--mono); font-size:.62rem; color:var(--text-3)">
                            Data source: equipment table
                        </span>
                    </div>

                    <div class="kpi-strip">
                        <div class="kpi">
                            <span class="kpi-label">Total Equipment</span>
                            <span class="kpi-value">{{ $totalEquipment }}</span>
                            <span class="kpi-delta">{{ $available }} available now</span>
                        </div>
                        <div class="kpi">
                            <span class="kpi-label">Combined Rate Pool</span>
                            <span class="kpi-value green">${{ number_format($totalHourlyPool, 2) }}</span>
                            <span class="kpi-delta">sum of all hourly rates</span>
                        </div>
                        <div class="kpi">
                            <span class="kpi-label">Average Rate</span>
                            <span class="kpi-value blue">${{ number_format($avgRate, 2) }}</span>
                            <span class="kpi-delta">per equipment per hour</span>
                        </div>
                        <div class="kpi">
                            <span class="kpi-label">Under Maintenance</span>
                            <span class="kpi-value {{ $inMaintenance > 0 ? 'amber' : '' }}">{{ $inMaintenance }}</span>
                            <span class="kpi-delta">{{ $inUse }} currently in use</span>
                        </div>
                    </div>
                </div>

                <div id="analytics">
                    <div class="section-head">
                        <span class="section-title">Rate Analytics</span>
                    </div>

                    <div class="two-col">

                        {{-- Highest rates --}}
                        <div class="panel">
                            <div class="panel-header">
                                <span class="section-title" style="margin-bottom:0">Highest Hourly Rates</span>
                            </div>
                            <div class="spotlights">
                                @forelse ($topEarners as $item)
                                    <div class="spotlight">
                                        <div>
                                            <div class="spotlight-name">{{ $item->name }}</div>
                                            <div class="spotlight-id">EQ-{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}
                                            </div>
                                        </div>
                                        <div class="spotlight-rate top">${{ number_format($item->hourly_rate, 2) }}/hr
                                        </div>
                                    </div>
                                @empty
                                    <div
                                        style="padding:1.5rem; font-family:var(--mono); font-size:.75rem; color:var(--text-3)">
                                        No equipment found.
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- Lowest rates --}}
                        <div class="panel">
                            <div class="panel-header">
                                <span class="section-title" style="margin-bottom:0">Lowest Hourly Rates</span>
                            </div>
                            <div class="spotlights">
                                @forelse ($bottomEarners as $item)
                                    <div class="spotlight">
                                        <div>
                                            <div class="spotlight-name">{{ $item->name }}</div>
                                            <div class="spotlight-id">EQ-{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}
                                            </div>
                                        </div>
                                        <div class="spotlight-rate bottom">
                                            ${{ number_format($item->hourly_rate, 2) }}/hr</div>
                                    </div>
                                @empty
                                    <div
                                        style="padding:1.5rem; font-family:var(--mono); font-size:.75rem; color:var(--text-3)">
                                        No equipment found.
                                    </div>
                                @endforelse
                            </div>
                        </div>

                    </div>

                    {{-- Clearance distribution + rate range ── --}}
                    <div class="two-col" style="margin-top:1.5rem;">

                        <div class="panel">
                            <div class="panel-header">
                                <span class="section-title" style="margin-bottom:0">Clearance Distribution</span>
                            </div>
                            <div class="panel-body">
                                <div class="clearance-bars">
                                    <div class="clr-row">
                                        <div class="clr-meta">
                                            <span>Level 1 — Open Access</span>
                                            <span>{{ $clr1 }} units</span>
                                        </div>
                                        <div class="bar-track">
                                            <div class="bar-fill l1"
                                                style="width:{{ $totalEquipment ? ($clr1 / $totalEquipment) * 100 : 0 }}%">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clr-row">
                                        <div class="clr-meta">
                                            <span>Level 2 — Restricted</span>
                                            <span>{{ $clr2 }} units</span>
                                        </div>
                                        <div class="bar-track">
                                            <div class="bar-fill l2"
                                                style="width:{{ $totalEquipment ? ($clr2 / $totalEquipment) * 100 : 0 }}%">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clr-row">
                                        <div class="clr-meta">
                                            <span>Level 3 — High Security</span>
                                            <span>{{ $clr3 }} units</span>
                                        </div>
                                        <div class="bar-track">
                                            <div class="bar-fill l3"
                                                style="width:{{ $totalEquipment ? ($clr3 / $totalEquipment) * 100 : 0 }}%">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="panel">
                            <div class="panel-header">
                                <span class="section-title" style="margin-bottom:0">Rate Range Summary</span>
                            </div>
                            <div class="panel-body" style="display:flex; flex-direction:column; gap:1rem;">
                                @foreach ([['label' => 'Highest Rate', 'val' => '$' . number_format($maxRate, 2) . '/hr', 'color' => 'var(--accent-mid)'], ['label' => 'Average Rate', 'val' => '$' . number_format($avgRate, 2) . '/hr', 'color' => 'var(--blue)'], ['label' => 'Lowest Rate', 'val' => '$' . number_format($minRate, 2) . '/hr', 'color' => 'var(--red)'], ['label' => 'Rate Pool Sum', 'val' => '$' . number_format($totalHourlyPool, 2) . '/hr', 'color' => 'var(--ink)']] as $row)
                                    <div
                                        style="display:flex; justify-content:space-between; align-items:center; padding:.6rem 0; border-bottom:1px solid var(--border);">
                                        <span
                                            style="font-family:var(--mono); font-size:.65rem; color:var(--text-3); letter-spacing:.08em; text-transform:uppercase;">
                                            {{ $row['label'] }}
                                        </span>
                                        <span
                                            style="font-family:var(--mono); font-size:.85rem; font-weight:500; color:{{ $row['color'] }};">
                                            {{ $row['val'] }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                    </div>
                </div>

                
                <div id="equipment">
                    <div class="section-head">
                        <span class="section-title">All Equipment — Rate Registry</span>
                        <span style="font-family:var(--mono); font-size:.62rem; color:var(--text-3)">
                            {{ $totalEquipment }} records
                        </span>
                    </div>

                    <div class="panel">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Equipment Name</th>
                                    <th>Status</th>
                                    <th>Clearance Req.</th>
                                    <th class="right">Hourly Rate</th>
                                    <th>Added</th>
                                    <th>Last Updated</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($equipment as $item)
                                    @php
                                        $pillClass = match ($item->status) {
                                            'Available' => 'pill-available',
                                            'In Use' => 'pill-inuse',
                                            'Maintenance' => 'pill-maintenance',
                                            default => 'pill-unavailable',
                                        };
                                    @endphp
                                    <tr>
                                        <td class="mono muted">{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}</td>
                                        <td style="font-weight:500; color:var(--ink)">{{ $item->name }}</td>
                                        <td><span class="pill {{ $pillClass }}">{{ $item->status }}</span></td>
                                        <td>
                                            <div class="pips">
                                                @for ($i = 1; $i <= 3; $i++)
                                                    <div
                                                        class="pip {{ $i <= $item->required_clearance ? 'on' : '' }}">
                                                    </div>
                                                @endfor
                                            </div>
                                        </td>
                                        <td class="mono right" style="font-weight:500; color:var(--accent-mid)">
                                            ${{ number_format($item->hourly_rate, 2) }}
                                        </td>
                                        <td class="mono muted">{{ $item->created_at->format('d M Y') }}</td>
                                        <td class="mono muted">{{ $item->updated_at->format('d M Y') }}</td>
                                    </tr>
                                @empty
                                    <tr class="empty-row">
                                        <td colspan="7">No equipment records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

             
                <div id="audit-trail" class="audit-section">
                    <div class="section-head">
                        <span class="section-title">Audit Trail</span>
                        <span style="font-family:var(--mono); font-size:.62rem; color:var(--text-3)">
                            Last 50 actions · Showing {{ $auditLogs->count() }} records
                        </span>
                    </div>

                    <div class="panel">
                        <table class="data-table audit-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Action</th>
                                    <th>Performed By</th>
                                    <th>Timestamp</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($auditLogs as $log)
                                    <tr>
                                        <td class="audit-id">{{ str_pad($log->id, 5, '0', STR_PAD_LEFT) }}</td>
                                        <td class="audit-action">{{ $log->action }}</td>
                                        <td class="audit-user">{{ $log->user_name }}</td>
                                        <td class="audit-time">
                                            {{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, H:i:s') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="empty-row">
                                        <td colspan="4">No audit records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <a href="{{ route('export-pdf') }}"> Export All Audit Logs</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
    
    <script>
        // Animate progress bars on load
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.bar-fill').forEach(bar => {
                const w = bar.style.width;
                bar.style.width = '0';
                requestAnimationFrame(() => setTimeout(() => bar.style.width = w, 80));
            });

            // Smooth scroll for sidebar nav
            document.querySelectorAll('.nav-item[href^="#"]').forEach(link => {
                link.addEventListener('click', e => {
                    e.preventDefault();
                    document.querySelectorAll('.nav-item').forEach(n => n.classList.remove(
                        'active'));
                    link.classList.add('active');
                    const target = document.querySelector(link.getAttribute('href'));
                    if (target) target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                });
            });
        });
    </script>

</body>

</html>
