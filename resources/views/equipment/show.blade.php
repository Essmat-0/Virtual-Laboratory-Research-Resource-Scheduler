<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $equipment->name }} — Equipment Registry</title>
    <link rel="stylesheet" href="{{ asset('css/equipment-show.css') }}">
</head>

<body>

    <div class="shell">

        {{-- Breadcrumb --}}
        <nav class="breadcrumb">
            <a href="{{ route('equipment.index') }}">← Back to Equipment</a>
            <span class="sep">/</span>
            <span>{{ $equipment->name }}</span>
        </nav>

        {{-- Hero --}}
        @php
            $badgeClass = match ($equipment->status) {
                'Idle' => 'badge-idle',
                'Active' => 'badge-active',
                'Maintenance' => 'badge-maintenance',
                'Locked' => 'badge-locked',
                default => 'badge-unavailable',
            };
        @endphp

        <div class="hero">
            <div class="hero-tag">EQ-{{ str_pad($equipment->id, 4, '0', STR_PAD_LEFT) }}</div>
            <h1 class="hero-name">{{ $equipment->name }}</h1>
            <span class="badge {{ $badgeClass }}">{{ $equipment->status }}</span>
        </div>

        {{-- Detail cards --}}
        <div class="cards">

            <div class="card">
                <span class="card-label">Hourly Rate</span>
                <span class="card-value accent">
                    ${{ number_format($equipment->hourly_rate, 2) }}<small>/hr</small>
                </span>
            </div>

            <div class="card">
                <span class="card-label">Status</span>
                <span class="card-value">{{ $equipment->status }}</span>
            </div>

            <div class="card">
                <span class="card-label">Required Clearance</span>
                <div class="pips">
                    @for ($i = 1; $i <= 3; $i++)
                        <div class="pip {{ $i <= $equipment->required_clearance ? 'on' : '' }}"></div>
                    @endfor
                </div>
                <span class="pip-label">Level {{ $equipment->required_clearance }}</span>
            </div>

            <div class="card">
                <span class="card-label">Date Added</span>
                <span class="card-value">{{ $equipment->created_at->format('d M Y') }}</span>
            </div>

            <div class="card">
                <span class="card-label">Last Updated</span>
                <span class="card-value">{{ $equipment->updated_at->format('d M Y') }}</span>
            </div>

        </div>
        
    </div>

</body>

</html>
