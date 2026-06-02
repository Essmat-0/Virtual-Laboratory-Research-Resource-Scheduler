<div class="nav-actions">
    {{-- Return to Hub Button --}}
    <a href="/" class="terminal-btn">
        <span class="cmd-prefix">~</span>
        <span class="btn-label">HUB</span>
        <span class="status-indicator hub"></span>
    </a>

    {{-- Logout Button --}}
    <form method="POST" action="{{ route('logout') }}" class="m-0">
        @csrf
        <button type="submit" class="terminal-btn logout">
            <span class="cmd-prefix">#</span>
            <span class="btn-label">DISCONNECT</span>
            <span class="status-indicator logout"></span>
        </button>
    </form>
</div>
{{-- resources/views/components/logout-welcome.blade.php --}}

<style>
    /* resources/css/app.css */
.welcome-logout-btn {
    background: transparent;
    border: 1px solid var(--border);
    padding: 0.6rem 1.2rem;
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 4px;
    height: 38px;
}

.welcome-logout-btn .cmd-prefix {
    font-family: var(--font-mono);
    color: var(--accent);
    font-weight: 700;
}

/* ... the rest of the CSS we wrote ... */
    .nav-actions {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .terminal-btn {
        background: transparent;
        border: 1px solid var(--border);
        padding: 0.6rem 1.2rem;
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 4px;
        text-decoration: none;
        height: 38px;
    }

    .terminal-btn .cmd-prefix {
        font-family: var(--font-mono);
        color: var(--accent);
        font-weight: 700;
        font-size: 0.8rem;
    }

    .terminal-btn .btn-label {
        font-family: var(--font-mono);
        font-size: 0.7rem;
        font-weight: 500;
        letter-spacing: 0.1em;
        color: var(--muted);
    }

    .terminal-btn .status-indicator {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--muted);
    }

    /* Hub Button Specifics */
    .terminal-btn:hover {
        border-color: var(--accent);
        background: rgba(200, 240, 74, 0.05);
    }
    .terminal-btn:hover .status-indicator.hub {
        background: var(--accent);
        box-shadow: 0 0 8px var(--accent);
    }

    /* Logout Button Specifics */
    .terminal-btn.logout:hover {
        border-color: var(--red);
        background: rgba(255, 77, 90, 0.05);
    }
    .terminal-btn.logout:hover .status-indicator.logout {
        background: var(--red);
        box-shadow: 0 0 8px var(--red);
    }

    .terminal-btn:hover .btn-label {
        color: var(--text);
    }
</style>
