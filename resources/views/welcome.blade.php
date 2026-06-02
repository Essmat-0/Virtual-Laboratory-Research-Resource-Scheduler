<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Equipment Registry</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">

    <link
        href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Mono:wght@300;400;500&display=swap"
        rel="stylesheet">
</head>

<body>
    @guest
        <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
    @endguest

    <div class="shell">

        {{-- ── Header ── --}}
        <header>
            <div class="header-top">
                <div class="header-left">
                    <p class="eyebrow">// Lab Management System</p>
                    <h1>Equipment <span>Registry</span></h1>
                    <p class="header-meta">Last updated &mdash; {{ now()->format('d M Y, H:i') }}</p>
                </div>

                <div class="header-actions">
                    @auth
                        <a href="{{ route(auth()->user()->role->name . '.dashboard') }}" class="terminal-dashboard-btn">
                            <span class="btn-prompt">></span>
                            <span class="btn-text">{{ strtoupper(auth()->user()->role->name) }} Dashboard</span>
                        </a>
                        <x-logout-welcome />
                    @endauth
                </div>
            </div>


            @if (session('success'))
                <div class="alert">
                    STATUS_OK: {{ session('success') }}
                </div>
            @endif
            <div class="controls">
                {{-- Search --}}
                <div class="search-wrap">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.35-4.35" />
                    </svg>
                    <input id="search" class="search-input" type="text" placeholder="Search equipment…"
                        autocomplete="off" />
                </div>

                {{-- Status filter --}}
                <select id="status-filter" class="filter-select">
                    <option value="">All statuses</option>
                    <option value="Idle">Idle</option>
                    <option value="Active">Active</option>
                    <option value="Maintenance">Maintenance</option>
                    <option value="Locked">Unavailable</option>
                </select>


                <select id="category-filter" class="filter-select">
                    <option value="">All Categories</option>

                    @if ($userCertificates->isNotEmpty())
                        @php
                            $myCategoryIds = $userCertificates->pluck('equipment_category_id')->unique()->toArray();
                            $certifiedCategories = \App\Models\Category::whereIn('id', $myCategoryIds)->get();
                        @endphp

                        @foreach ($certifiedCategories as $cat)
                            <option value="{{ strtolower($cat->name) }}">{{ $cat->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </header>
        {{-- ── Stats bar ── --}}
        @php

            $total = $equipment->total();
            $idle = $equipment->getCollection()->where('status', 'Idle')->count();
            $active = $equipment->getCollection()->where('status', 'Active')->count();
            $maintenance = $equipment->getCollection()->where('status', 'Maintenance')->count();
            $locked = $equipment->getCollection()->where('status', 'Locked')->count();
            $isResearcher = auth()->user()->isResearcher();
            $researcherProfile = auth()->user()->researcherProfile;

        @endphp

        <div class="stats-bar">
            <div class="stat">
                <span class="stat-label">Total</span>
                <span class="stat-value">{{ $total }}</span>
            </div>
            <div class="stat">
                <span class="stat-label">Available</span>
                <span class="stat-value" style="color:var(--green)">{{ $idle }}</span>
            </div>
            <div class="stat">
                <span class="stat-label">In Use</span>
                <span class="stat-value" style="color:var(--blue)">{{ $active }}</span>
            </div>
            <div class="stat">
                <span class="stat-label">Maintenance</span>
                <span class="stat-value" style="color:var(--amber)">{{ $maintenance }}</span>
            </div>
            <div class="stat">
                <span class="stat-label">Locked</span>
                <span class="stat-value" style="color:var(--red)">{{ $locked }}</span>
            </div>
        </div>

        {{-- ── Equipment Grid ── --}}
        <div class="grid" id="equipment-grid">

            @forelse ($equipment as $item)
                @if (!$isResearcher || $researcherProfile->canAccess($item))
                    @php
                        $badgeClass = match ($item->status) {
                            'Idle' => 'badge-idle',
                            'Active' => 'badge-active',
                            'Maintenance' => 'badge-maintenance',
                            'Locked' => 'badge-locked',
                            default => 'badge-unavailable',
                        };
                        $category = $item->category->name ?? 'No Category';
                        $availableItem = $item->status === 'Idle' || $item->status === 'Active';

                        $currentCert = $userCertificates->where('equipment_category_id', $item->category_id)->first();
                        $isExpired = $currentCert?->expiry_date < now();
                        $isCertified = $currentCert && !$isExpired;
                    @endphp

                    <div class="card" data-name="{{ strtolower($item->name) }}" data-status="{{ $item->status }}"
                        data-category="{{ strtolower($item->category->name ?? 'uncategorized') }}">
                        {{-- Top row --}}
                        <div class="card-top">
                            <span class="card-id">#{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}</span>
                            <span class="badge {{ $badgeClass }}">{{ $item->status }}</span>
                        </div>

                        {{-- Name --}}
                        <div class="card-name">{{ $item->name }}</div>

                        <div class="card-divider"></div>

                        {{-- Meta --}}
                        <div class="meta-list">
                            <div class="meta-row">
                                <span class="meta-key">Category</span>
                                <span class="meta-val accent">{{ $category }}</span>
                            </div>
                            <div class="meta-row">
                                <span class="meta-key">Hourly Rate</span>
                                <span class="meta-val accent">${{ number_format($item->hourly_rate, 2) }}/hr</span>
                            </div>
                            <div class="meta-row">
                                <span class="meta-key">Clearance Req.</span>
                                <div class="pips">
                                    @for ($i = 1; $i <= 3; $i++)
                                        <div class="pip {{ $i <= $item->required_clearance ? 'on' : '' }}">
                                        </div>
                                    @endfor
                                </div>
                            </div>
                            <div class="meta-row">
                                <span class="meta-key">Added</span>
                                <span class="meta-val">{{ $item->created_at->format('d M Y') }}</span>
                            </div>
                        </div>

                        <div class="card-divider"></div>

                        {{-- Footer actions --}}
                        <div class="card-footer">
                            <a href="{{ route('equipment.specs', $item->id) }}" class="btn btn-ghost">
                                View Details
                            </a>

                            @auth
                                @if ($availableItem && $isResearcher)
                                    @if ($currentCert)
                                        @if ($currentCert->expiry_date >= now())
                                            <a href="{{ route('equipment.book', $item->id) }}" class="btn btn-primary">
                                                Book Session
                                            </a>
                                        @else
                                            <button class="btn btn-danger" disabled>Expired
                                                ({{ $currentCert->expiry_date->format('Y-m-d') }})
                                            </button>
                                        @endif
                                    @else
                                        <button class="btn btn-danger" disabled>No Certificate Found</button>
                                    @endif
                                @else
                                    <span class="btn btn-primary disabled">
                                        {{ auth()->user()->isResearcher() ? ($item->status !== 'Idle' ? 'Unavailable' : 'No Clearance') : 'You aren\'t authorized to Book' }}
                                    </span>
                                @endif
                            @endauth
                        </div>
                    </div>
                @endif
            @empty

                <div class="empty">
                    <div class="empty-icon">🔬</div>
                    <div class="empty-title">No equipment found</div>
                    <p class="empty-sub">No equipment has been registered yet.</p>
                </div>
            @endforelse

            <div id="no-results">No equipment matches your search.</div>

        </div>

        {{-- ── Pagination ── --}}
        @if ($equipment->hasPages())
            <div class="pagination">
                {{-- Previous --}}
                @if ($equipment->onFirstPage())
                    <span class="disabled">&larr;</span>
                @else
                    <a href="{{ $equipment->previousPageUrl() }}">&larr;</a>
                @endif

                {{-- Page numbers --}}
                @foreach ($equipment->getUrlRange(1, $equipment->lastPage()) as $page => $url)
                    @if ($page == $equipment->currentPage())
                        <span class="active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach

                {{-- Next --}}
                @if ($equipment->hasMorePages())
                    <a href="{{ $equipment->nextPageUrl() }}">&rarr;</a>
                @else
                    <span class="disabled">&rarr;</span>
                @endif
            </div>
        @endif

    </div>
    <script>
        const searchInput = document.getElementById('search');
        const statusFilter = document.getElementById('status-filter');
        const categoryFilter = document.getElementById('category-filter'); 
        const cards = document.querySelectorAll('.card[data-name]');
        const noResults = document.getElementById('no-results');

        function filterCards() {
            const query = searchInput.value.toLowerCase().trim();
            const status = statusFilter.value.toLowerCase();
            const category = categoryFilter.value.toLowerCase(); 
            let visible = 0;

            cards.forEach(card => {
                const nameMatch = card.dataset.name.includes(query);
                const statusMatch = !status || card.dataset.status.toLowerCase() === status;

                const categoryMatch = !category || card.dataset.category.toLowerCase() === category;

                const show = nameMatch && statusMatch && categoryMatch;

                card.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            noResults.style.display = visible === 0 ? 'block' : 'none';
        }

        searchInput.addEventListener('input', filterCards);
        statusFilter.addEventListener('change', filterCards);
        categoryFilter.addEventListener('change', filterCards); 
    </script>

</body>

</html>
