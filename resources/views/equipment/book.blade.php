<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Book — {{ $equipment->name }}</title>
    <link rel="stylesheet" href="{{ asset('css/equipment-book.css') }}">
</head>

<body>

    @php
        $badgeClass = match ($equipment->status) {
            'Idle' => 'badge-available',
            'Active' => 'badge-in-use',
            'Maintenance' => 'badge-maintenance',
            'Locked' => 'badge-locked',
            default => 'badge-unavailable',
        };
        $isIdle = $equipment->status === 'Idle';
        $canBeBooked = $equipment->status === 'Idle' || $equipment->status === 'Active';
        $hasClearance = auth()->user()->clearance_level >= $equipment->required_clearance;
        $canInteract = $canBeBooked && $hasClearance;
    @endphp

    <div class="shell">

        <nav class="breadcrumb">
            <a href="{{ route('equipment.index') }}">Equipment</a>
            <span class="sep">/</span>
            <a href="{{ route('equipment.show', $equipment->id) }}">{{ $equipment->name }}</a>
            <span class="sep">/</span>
            <span>Book</span>
        </nav>

        <div class="hero">
            <div class="hero-tag">EQ-{{ str_pad($equipment->id, 4, '0', STR_PAD_LEFT) }} · Reservation Request</div>
            <h1 class="hero-name">{{ $equipment->name }}</h1>
            <div class="hero-meta">
                <span class="badge {{ $badgeClass }}">{{ $equipment->status }}</span>
                <span class="hero-rate">${{ number_format($equipment->hourly_rate, 2) }}/hr</span>
                <span>Clearance Lv.{{ $equipment->required_clearance }} required</span>
            </div>
        </div>

        {{-- 3shan lw el user estns7 w 7awl yd5ol el book bel url bdl el book button (disabled) --}}
        @if (!$canBeBooked)
            <div class="blocked-card">
                <div class="blocked-title">Equipment Unavailable</div>
                <p class="blocked-msg">
                    This equipment is currently <strong>{{ $equipment->status }}</strong>
                    and cannot be reserved at this time.
                    Check back later or contact the lab administrator.
                </p>
                <a href="{{ route('equipment.show', $equipment->id) }}" class="btn-back">← Back to Details</a>
            </div>

            {{-- 3shan lw el user estns7 w 7awl yd5ol el book bel url bdl el book button (disabled) --}}
        @elseif (!$hasClearance)
            <div class="blocked-card">
                <div class="blocked-title">Insufficient Clearance</div>
                <p class="blocked-msg">
                    This equipment requires clearance level <strong>{{ $equipment->required_clearance }}</strong>.
                    Your current level is <strong>{{ auth()->user()->clearance_level }}</strong>.
                    Please contact your PI to request an upgrade.
                </p>
                <a href="{{ route('equipment.show', $equipment->id) }}" class="btn-back">← Back to Details</a>
            </div>
        @else
            @if ($errors->any())
                <div class="alert-error">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            <div class="option-row">

                @if ($isIdle)
                    <button class="option-card option-card--active" id="opt-now" onclick="switchMode('now')">
                        <div class="option-label">Start Now</div>
                        <div class="option-desc">Begin a session immediately.<br>Check out when done to stop billing.
                        </div>
                    </button>
                @endif

                <button class="option-card {{ !$isIdle ? 'option-card--active' : '' }}" id="opt-reserve"
                    onclick="switchMode('reserve')">
                    <div class="option-label">Schedule Reservation</div>
                    <div class="option-desc">Book a future time window.<br>Requires PI approval before the session.
                    </div>
                </button>
            </div>

            @if ($isIdle)
                <div id="panel-now">

                    <form method="POST" action="{{ route('equipment.session.start', $equipment->id) }}">
                        @csrf

                        <div class="form-card">
                            <div class="form-card-header">Immediate Session</div>
                            <div class="form-body">

                                <div class="instant-info">
                                    <div class="instant-row">
                                        <span class="instant-key">Equipment</span>
                                        <span class="instant-val">{{ $equipment->name }}</span>
                                    </div>
                                    <div class="instant-row">
                                        <span class="instant-key">Start Time</span>
                                        <span class="instant-val" id="live-clock">—</span>
                                    </div>
                                    <div class="instant-row">
                                        <span class="instant-key">Rate</span>
                                        <span
                                            class="instant-val accent">${{ number_format($equipment->hourly_rate, 2) }}/hr</span>
                                    </div>
                                    <div class="instant-row">
                                        <span class="instant-key">End Time</span>
                                        <span class="instant-val muted">Recorded on checkout</span>
                                    </div>
                                </div>

                                <div class="divider"></div>

                                <div class="status-note">
                                    The session starts immediately. A <strong style="color:var(--text)">Check
                                        Out</strong>
                                    button will appear on your dashboard — clicking it records the end time and
                                    finalises
                                    your bill.
                                </div>

                                <button type="submit" class="btn-submit btn-submit--accent">Start Session Now
                                    →</button>

                            </div>
                        </div>

                    </form>
                </div>
            @endif


            <div id="panel-reserve" style="{{ !$isIdle ? 'display:block;' : 'display:none;' }}">
                <form method="POST" action="{{ route('equipment.book.store', $equipment->id) }}">
                    @csrf

                    <div class="form-card">
                        <div class="form-card-header">Reservation Details</div>
                        <div class="form-body">

                            <div class="field-row">
                                <div class="field">
                                    <label for="start_time">Start Time</label>
                                    <input type="datetime-local" id="start_time" name="start_time"
                                        value="{{ old('start_time') }}" min="{{ now()->format('Y-m-d\TH:i') }}"
                                        required />
                                </div>
                                <div class="field">
                                    <label for="end_time">End Time</label>
                                    <input type="datetime-local" id="end_time" name="end_time"
                                        value="{{ old('end_time') }}"
                                        min="{{ now()->addHour()->format('Y-m-d\TH:i') }}" required />
                                </div>
                            </div>
                            <div class="field">
                                <label for="quantity">Quantity</label>
                                <input type="number" id="quantity" name="quantity" min="1"
                                    max="{{ $equipment->quantity - 1 == 0 ? 1 : $equipment->quantity - 1 }}"
                                    required />
                            </div>

                            {{-- Live cost preview --}}
                            <div class="cost-preview">
                                <div class="cost-left">
                                    <span class="cost-label">Estimated Cost</span>
                                    <span class="cost-value" id="cost-display">—</span>
                                    <span class="cost-breakdown" id="cost-breakdown">Select start and end time</span>
                                </div>
                                <div
                                    style="font-family:var(--mono); font-size:.68rem; color:var(--muted); text-align:right; line-height:1.6;">
                                    Rate<br>
                                    <span
                                        style="color:var(--accent); font-size:.82rem;">${{ number_format($equipment->hourly_rate, 2) }}/hr</span>
                                </div>
                            </div>

                            <div class="divider"></div>

                            <div class="status-note">
                                Reservations are submitted with <strong style="color:var(--text)">Pending</strong>
                                status
                                and require PI approval before the session begins.
                            </div>

                            <button type="submit" class="btn-submit">Submit Reservation →</button>

                        </div>
                    </div>

                </form>
            </div>

        @endif

    </div>

    <script>
        // tab swicth
        function switchMode(mode) {
            const isNow = mode === 'now';

            const panelNow = document.getElementById('panel-now');
            const optNow = document.getElementById('opt-now');
            const panelReserve = document.getElementById('panel-reserve');
            const optReserve = document.getElementById('opt-reserve');

            if (panelNow) panelNow.style.display = isNow ? 'block' : 'none';
            if (optNow) optNow.classList.toggle('option-card--active', isNow);

            if (panelReserve) panelReserve.style.display = isNow ? 'none' : 'block';
            if (optReserve) optReserve.classList.toggle('option-card--active', !isNow);
        }

        //=====================================

        // live clocjk
        function updateClock() {
            const el = document.getElementById('live-clock');
            if (!el) return;
            const now = new Date();
            el.textContent = now.toLocaleString('en-GB', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
        }
        updateClock();
        setInterval(updateClock, 1000);

        //=====================================

        // cost bta3 el reservation 3la asas el choosen time
        const ratePerHour = {{ $equipment->hourly_rate }};
        const startInput = document.getElementById('start_time');
        const endInput = document.getElementById('end_time');
        const qtyInput = document.getElementById('quantity');
        const costDisplay = document.getElementById('cost-display');
        const breakdown = document.getElementById('cost-breakdown');

        function updateCost() {
            if (!startInput || !endInput || !qtyInput) return;

            const start = new Date(startInput.value);
            const end = new Date(endInput.value);
            const qty = parseInt(qtyInput.value) || 0;

            if (!startInput.value || !endInput.value || end <= start) {
                costDisplay.textContent = '—';
                breakdown.textContent = (end <= start && startInput.value && endInput.value) ?
                    'End time must be after start time' :
                    'Select start and end time';
                return;
            }
            const hours = (end - start) / 36e5;
            const totalCost = hours * ratePerHour * qty;

            costDisplay.textContent = '$' + totalCost.toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            breakdown.textContent =
                `${hours.toFixed(1)} hr × $${ratePerHour.toFixed(2)}/hr × ${qty} ${qty === 1 ? 'unit' : 'units'}`;

            endInput.min = startInput.value;
        }

        startInput?.addEventListener('change', updateCost);
        endInput?.addEventListener('change', updateCost);
        qtyInput?.addEventListener('input', updateCost);

        @if ($errors->any())
            switchMode('reserve');
        @endif
        //==========================================================================================


    </script>

</body>

</html>
