<form method="POST" action="{{ route('logout') }}" class="welcome-logout-wrapper">
    @csrf
    <button type="submit" class="welcome-terminal-btn">
        <span class="prefix">#</span>
        <span class="label">DISCONNECT</span>
        <span class="indicator"></span>
    </button>
</form>

<style>
    .welcome-logout-wrapper {
        margin: 0;
        display: inline-block;
    }

    .welcome-terminal-btn {
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
        outline: none;
    }

    .welcome-terminal-btn .prefix {
        font-family: var(--font-mono);
        color: var(--accent);
        font-weight: 700;
        font-size: 0.8rem;
    }

    .welcome-terminal-btn .label {
        font-family: var(--font-mono);
        font-size: 0.7rem;
        font-weight: 500;
        letter-spacing: 0.1em;
        color: var(--muted);
        transition: color 0.3s ease;
    }

    .welcome-terminal-btn .indicator {
        width: 6px;
        height: 6px;
        background: var(--muted);
        border-radius: 50%;
        transition: all 0.3s ease;
    }

    /* Hover State - Specific to the Welcome Page */
    .welcome-terminal-btn:hover {
        border-color: var(--red);
        background: rgba(255, 77, 90, 0.05);
        box-shadow: 0 0 15px rgba(255, 77, 90, 0.1);
    }

    .welcome-terminal-btn:hover .label {
        color: var(--text);
    }

    .welcome-terminal-btn:hover .indicator {
        background: var(--red);
        box-shadow: 0 0 8px var(--red);
    }

    /* Active click effect */
    .welcome-terminal-btn:active {
        transform: scale(0.95);
    }
</style>
