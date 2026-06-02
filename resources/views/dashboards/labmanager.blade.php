<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>LabManager Terminal | LabSync</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;700;800&family=DM+Mono:wght@400;500&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/labm-dashboard.css') }}">
</head>

<body>

    <div class="shell">

        {{-- ── Header ── --}}
        <header>
            <div>
                <p class="eyebrow">// Inventory Logistics</p>
                <h1>Lab <span>Manager</span></h1>
            </div>
            <x-nav-actions />
        </header>

        {{-- ── Flash Messages ── --}}
        @if (session('success'))
            <div class="alert-success">&gt; ASSET_SYNC_COMPLETE: {{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert-error">&gt; ERROR: {{ session('error') }}</div>
        @endif

        {{-- ── Tabs ── --}}
        <div class="tab-nav">
            <button class="tab-btn active" onclick="showTab('catalog-sec', this)">01_Catalog_Asset</button>
            <button class="tab-btn" onclick="showTab('inventory-sec', this)">
                02_Inventory
                @php $maintenanceCount = $equipments->filter(fn($e) => $e->needsMaintenance())->count(); @endphp
                @if ($maintenanceCount > 0)
                    <span class="tab-count">{{ $maintenanceCount }}</span>
                @endif
            </button>
            <button class="tab-btn " onclick="showTab('roi-sec', this)">03_ROI_REVIEW</button>
            <button class="tab-btn " onclick="showTab('maintenance-sec', this)">04_Emergency_Maintenance</button>
        </div>


        <div id="catalog-sec" class="tab-content active">
            <section>
                <h2>Catalog New Asset</h2>

                <form method="POST" action="{{ route('LabM.equipment.store') }}">
                    @csrf

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label>Operational Status</label>
                        <div class="status-container">
                            @foreach (['Idle', 'Active', 'Maintenance', 'Locked'] as $s)
                                <label class="status-option">
                                    <input type="radio" name="equipment_status" value="{{ $s }}"
                                        {{ old('equipment_status') === $s ? 'checked' : '' }} required>
                                    {{ $s }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label>Asset Identity</label>
                            <input type="text" name="equipment_name" class="standard-input"
                                placeholder="e.g., Spectrometer X-10" value="{{ old('equipment_name') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Hourly Credit Rate ($)</label>
                            <input type="number" step="0.01" name="hourly_rate" class="standard-input"
                                placeholder="200.50" value="{{ old('hourly_rate') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Required Clearance Level</label>
                            <input type="number" name="required_clearance" class="standard-input" min="0"
                                max="3" value="{{ old('required_clearance') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Category Identifier</label>
                            <input type="number" name="category_id" class="standard-input" placeholder="10"
                                value="{{ old('category_id') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Sector / Location Code</label>
                            <input type="text" name="location_code" class="standard-input" placeholder="B-104"
                                value="{{ old('location_code') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Calibration Threshold (Hrs)</label>
                            <input type="number" step="0.1" name="calibration_threshold" class="standard-input"
                                placeholder="500" value="{{ old('calibration_threshold') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Maintenance Cost ($)</label>
                            <input type="number" step="0.01" name="maintenance_cost" class="standard-input"
                                placeholder="500" value="{{ old('maintenance_cost') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Cooldown Buffer (Min)</label>
                            <input type="number" name="cooldown_buffer" class="standard-input" placeholder="15"
                                value="{{ old('cooldown_buffer') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Quantity</label>
                            <input type="number" name="quantity" class="standard-input" placeholder="2" min="1"
                                value="{{ old('quantity') }}" required>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">Initialize Asset Protocol</button>
                </form>
            </section>
        </div>


        <div id="inventory-sec" class="tab-content">
            <section>
                <h2>Equipment Inventory</h2>

                @forelse ($equipments as $equipment)
                    @if ($equipment->needsMaintenance())
                        <p class="maintenance-warn">
                            {{ $equipment->name }} has exceeded its calibration threshold and requires maintenance.
                        </p>
                    @endif

                    <div class="eq-item {{ $equipment->needsMaintenance() ? 'eq-item--warn' : '' }}">

                        <div class="eq-data">
                            <p class="eq-id">EQ-{{ str_pad($equipment->id, 4, '0', STR_PAD_LEFT) }}</p>
                            <p class="eq-name">{{ $equipment->name }}</p>
                            <p class="eq-meta">
                                {{ $equipment->location_code }} &middot;
                                ${{ number_format($equipment->hourly_rate, 2) }}/hr &middot;
                                Clearance Lv.{{ $equipment->required_clearance }}
                            </p>
                        </div>

                        @php
                            $badgeClass = match ($equipment->status) {
                                'Idle' => 'badge-idle',
                                'Active' => 'badge-active',
                                'Maintenance' => 'badge-maintenance',
                                'Locked' => 'badge-locked',
                                default => '',
                            };
                        @endphp
                        <span class="status-badge {{ $badgeClass }}">{{ $equipment->status }}</span>

                        <div class="eq-actions">

                            <a href="{{ route('equipment.show', $equipment->id) }}" class="action-btn">Details</a>

                            @if ($equipment->status === 'Idle')
                                <form method="POST"
                                    action="{{ route('LabM.equipment.setMaintenance', $equipment->id) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="action-btn action-btn--warn">Maintenance</button>
                                </form>
                            @endif
                            @if ($equipment->status === 'Maintenance')
                                <form method="POST" action="{{ route('LabM.equipment.setIdle', $equipment->id) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="action-btn action-btn--warn">Equipment Is Now
                                        Idle</button>
                                </form>
                            @endif

                            <form method="POST" action="{{ route('LabM.equipment.destroy', $equipment->id) }}"
                                onsubmit="return confirm('Permanently decommission {{ addslashes($equipment->name) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="action-btn action-btn--danger">Delete</button>
                            </form>

                        </div>
                    </div>

                @empty
                    <div class="empty-state">// NO_ASSETS_FOUND — inventory is empty</div>
                @endforelse

            </section>
        </div>
        <div id="roi-sec" class="tab-content active">
            <section>
                <h2>ROI Review</h2>

                @forelse ($equipments as $eq)
                    @php
                        $roi =
                            $eq->maintenance_cost > 0
                                ? ($eq->total_usage_hours / $eq->maintenance_cost) * config('app.normalization_factor')
                                : 0;

                        $roiColor = $roi >= 1 ? 'var(--accent)' : 'var(--red)';
                    @endphp

                    <div class="roi-item">
                        <div class="roi-data">
                            <p class="roi-name">{{ $eq->name }}</p>
                            <p class="roi-meta">
                                Total Usage: <span>{{ number_format($eq->total_usage_hours, 1) }} hrs</span>
                                &nbsp;&middot;&nbsp;
                                Maintenance Cost: <span>${{ number_format($eq->maintenance_cost, 2) }}</span>
                            </p>
                        </div>

                        <div class="roi-score">
                            <span class="roi-label">ROI Index</span>
                            <span class="roi-value" style="color: {{ $roiColor }};">
                                {{ number_format($roi, 2) }}
                            </span>
                        </div>
                    </div>

                @empty
                    <div class="res-empty">// NO_EQUIPMENT_DATA — nothing to review</div>
                @endforelse

            </section>
        </div>
        <div id="maintenance-sec" class="tab-content">
            <section>
                <h2> Emergency Maintenance </h2>
                <form method="POST" action="{{ route('emergency.maintenance') }}">
                    @csrf
                    <button type="submit" class="action-btn"
                        style="width:100% ;color:black; background-color:red; font-size:2rem">MAINTENANCE MODE</button>
                </form>
            </section>
        </div>
        <section>
            <h2>Utilization Heatmap</h2>
            <div class="heatmap-container">
                <div class="heatmap-wrap">
                    <div id="heatmap" class="heatmap-grid">

                    </div>
                </div>

                <div class="heatmap-legend">
                    <div class="legend-swatch" aria-hidden="true"></div>
                    <div class="legend-label">Low</div>
                    <div style="flex:1"></div>
                    <div class="legend-label">High</div>
                </div>
            </div>
        </section>
    </div>

    <script>
        function showTab(tabId, btn) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
            document.getElementById(tabId).classList.add('active');
            btn.classList.add('active');
        }

        if (new URLSearchParams(window.location.search).get('tab') === 'inventory') {
            document.querySelectorAll('.tab-btn')[1]?.click();
        }

        (async function renderHeatmap() {
            try {
                const resp = await fetch('/labmanager/heatmap');
                if (!resp.ok) return;
                const data = await resp.json();
                const container = document.getElementById('heatmap');
                container.innerHTML = '';

                data.days.forEach((day, dIdx) => {
                    const label = document.createElement('div');
                    label.className = 'heatmap-label';
                    label.textContent = day;
                    container.appendChild(label);

                    for (let h = 0; h < 24; h++) {
                        const cell = document.createElement('div');
                        cell.className = 'heatmap-cell';
                        const percent = (data.percents[dIdx] && data.percents[dIdx][h]) ? data.percents[
                            dIdx][h] : 0;
                        const count = (data.counts[dIdx] && data.counts[dIdx][h]) ? data.counts[dIdx][h] :
                            0;

                        const alpha = Math.min(0.95, Math.max(0.03, percent / 100));
                        cell.style.background = `rgba(200,240,74, ${alpha})`;

                        const hint = document.createElement('div');
                        hint.className = 'hint';
                        hint.textContent = `${Math.round(percent)}% (${count})`;
                        cell.appendChild(hint);

                        cell.title =
                            `${day} ${h}:00 — ${Math.round(percent)}% utilization (${count} sessions)`;
                        container.appendChild(cell);
                    }
                });

            } catch (err) {
                console.error('Heatmap load failed', err);
            }
        })();
    </script>

</body>

</html>
