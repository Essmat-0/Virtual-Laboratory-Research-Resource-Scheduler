<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Researcher Terminal | LabSync</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Syne:wght@400;700;800&family=DM+Mono:wght@400;500&display=swap"rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/researcher.css') }}">
</head>

<body>
    <div class="shell">

        <div class="utility-bar">
            <div class="stat-item">
                <span class="stat-label">Active_Sessions</span>
                <span class="stat-value">{{ $activeSessions->count() }}</span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Pending_Reservations</span>
                <span class="stat-value">{{ $reservations->where('status', 'Pending')->count() }}</span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Researcher</span>
                <span class="stat-value">{{ auth()->user()->name }}</span>
            </div>
        </div>

        <header>
            <div>
                <p class="eyebrow">// Lab Access</p>
                <h1>Researcher <span>Dashboard</span></h1>
            </div>
            <div class="header-nav">
                <x-nav-actions />
            </div>
        </header>

        @if (session('success'))
            <div class="alert-success">&gt; {{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert-error">&gt; {{ session('error') }}</div>
        @endif

        <div class="tab-nav">
            <button class="tab-btn active" onclick="showTab('reservations-sec', this)">
                01_My_Reservations
                @if ($reservations->where('status', 'pending')->count() > 0)
                    <span class="tab-count">{{ $reservations->where('status', 'pending')->count() }}</span>
                @endif
            </button>
            <button class="tab-btn" onclick="showTab('sessions-sec', this)">
                02_Active_Sessions
                @if ($activeSessions->count() > 0)
                    <span class="tab-count">{{ $activeSessions->count() }}</span>
                @endif
            </button>
            <button class="tab-btn" onclick="showTab('endedSessions-sec', this)">
                03_Ended_Sessions
            </button>

        </div>


        <div id="reservations-sec" class="tab-content active">
            <section>
                <h2>My Reservations</h2>

                @forelse ($reservations as $reservation)
                    <div class="res-item">
                        <div class="res-data">
                            <p class="res-label">
                                {{ optional($reservation->equipment)->name ?? 'Unknown Equipment' }}
                            </p>

                            <p class="res-sub">
                                From:
                                <span>{{ \Carbon\Carbon::parse($reservation->start_time)->format('d M Y, H:i') }}</span>
                                &rarr;
                                <span>{{ \Carbon\Carbon::parse($reservation->end_time)->format('d M Y, H:i') }}</span>
                                <br>
                                Submitted: <span>{{ $reservation->created_at->diffForHumans() }}</span>
                            </p>
                        </div>

                        <span class="status-pill {{ $reservation->status }}">
                            {{ ucfirst($reservation->status) }}
                        </span>
                    </div>

                @empty
                    <div class="empty-state">// NO_RESERVATIONS — nothing scheduled yet</div>
                @endforelse

            </section>
        </div>



        <div id="sessions-sec" class="tab-content">
            <section>
                <h2>Active Sessions</h2>

                @forelse ($activeSessions as $session)
                    @php
                        $start = \Carbon\Carbon::parse($session->start_time);
                        $hours = $start->diffInMinutes(now()) / 60;
                        $cost = $hours * $session->equipment->hourly_rate;
                    @endphp
                    <div class="session-item">
                        <div class="res-data">
                            <p class="res-label">
                                <span class="live-dot"></span>
                                {{ optional($session->equipment)->name ?? 'Unknown Equipment' }}
                            </p>

                            <p class="res-sub">
                                Started:
                                <span>{{ $start->format('d M Y, H:i') }}</span>
                                <br>
                                Duration so far:
                                <span>{{ $start->diffForHumans(now(), true) }}</span><br>
                                Cost so far:
                                <span>{{ number_format($cost, 2) }}</span>

                            </p>
                        </div>
                        <form method="POST" action="{{ route('researcher.session.checkout', $session->id) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-checkout">Check Out</button>
                        </form>
                    </div>

                @empty
                    <div class="empty-state">// NO_ACTIVE_SESSIONS — no equipment in use</div>
                @endforelse

            </section>
        </div>

        <div id="endedSessions-sec" class="tab-content">
            <h2> Ended Sessions </h2>

            @forelse ($sessionCost as $sc)
                <div class="res-item">
                    <div class="res-data">
                        <p class="res-label">
                            Session ID #{{ optional($sc->equipmentSession)->id ?? 'Unknown' }}
                        </p>
                        <p class="res-sub">
                            From:
                            <span>{{ \Carbon\Carbon::parse($sc->equipmentSession->start_time)->format('d M Y, H:i') }}</span>
                            &rarr;
                            <span>{{ \Carbon\Carbon::parse($sc->equipmentSession->end_time)->format('d M Y, H:i') }}</span>
                            <br>
                            Submitted: <span>{{ $sc->created_at->diffForHumans() }}</span>
                        </p>
                    </div>

                    <span class="status-pill" style="font-size: 2rem">
                        {{ ucfirst($sc->normalized_amount) }}$
                    </span>
                </div>
            @empty
                <div class="empty-state">// NO_ENDED_SESSIONS — no equipment have been used</div>
            @endforelse
        </div>

    </div>

    <script>
        function showTab(tabId, btn) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
            document.getElementById(tabId).classList.add('active');
            btn.classList.add('active');
        }

        // yft7 session tab lw redirected with ?tab=sessions
        if (new URLSearchParams(window.location.search).get('tab') === 'sessions') {
            document.querySelectorAll('.tab-btn')[1]?.click();
        }


        // auto ping to update user activity

        function sendHeartbeat() {
            fetch('{{ route('heartbeat') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                }
            });
        }
        sendHeartbeat();
        setInterval(sendHeartbeat, 240000);
    </script>

</body>

</html>
