<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Task Manager') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        /* ── CSS Variables ── */
        :root {
            --purple-50:  #f5f3ff;
            --purple-100: #ede9fe;
            --purple-400: #a78bfa;
            --purple-500: #8b5cf6;
            --purple-600: #7c3aed;
            --purple-700: #6d28d9;
            --purple-800: #5b21b6;
            --purple-900: #4c1d95;

            --bg:         #f5f3ff;
            --surface:    rgba(255,255,255,0.75);
            --surface-2:  rgba(255,255,255,0.55);
            --border:     rgba(124,58,237,0.15);
            --text:       #1e1b4b;
            --text-muted: #6b7280;
            --nav-bg:     rgba(109,40,217,0.92);
            --shadow:     0 8px 32px rgba(124,58,237,0.12);
            --glow:       0 0 20px rgba(139,92,246,0.25);
        }

        [data-theme="dark"] {
            --bg:         #0f0a1e;
            --surface:    rgba(30,20,60,0.80);
            --surface-2:  rgba(20,14,45,0.70);
            --border:     rgba(167,139,250,0.18);
            --text:       #ede9fe;
            --text-muted: #a78bfa;
            --nav-bg:     rgba(20,10,50,0.95);
            --shadow:     0 8px 32px rgba(0,0,0,0.45);
            --glow:       0 0 24px rgba(139,92,246,0.35);
        }

        /* ── Base ── */
        *, *::before, *::after { transition: background-color .3s ease, color .2s ease, border-color .2s ease; }
        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Segoe UI', system-ui, sans-serif;
            min-height: 100vh;
        }
        [data-theme="light"] body {
            background-image:
                radial-gradient(ellipse at 20% 10%, rgba(139,92,246,.18) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 90%, rgba(109,40,217,.12) 0%, transparent 50%);
        }
        [data-theme="dark"] body {
            background-image:
                radial-gradient(ellipse at 20% 10%, rgba(109,40,217,.25) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 90%, rgba(76,29,149,.30) 0%, transparent 50%);
        }

        /* ── Navbar ── */
        .app-nav {
            background: var(--nav-bg);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--border);
            box-shadow: 0 2px 20px rgba(0,0,0,0.2);
            position: sticky; top: 0; z-index: 1030;
        }
        .app-nav .brand {
            font-size: 1.25rem; font-weight: 700; letter-spacing: -.5px;
            color: #fff !important;
            text-decoration: none;
            display: flex; align-items: center; gap: .5rem;
        }
        .brand-icon {
            width: 32px; height: 32px;
            background: rgba(255,255,255,0.2);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem;
        }
        .nav-user {
            color: rgba(255,255,255,.8);
            font-size: .875rem;
        }
        .nav-pill {
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.2);
            color: #fff !important;
            border-radius: 50px;
            padding: .35rem .9rem;
            font-size: .82rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex; align-items: center; gap: .4rem;
            backdrop-filter: blur(4px);
        }
        .nav-pill:hover { background: rgba(255,255,255,0.22); color: #fff !important; }

        /* ── Theme Toggle ── */
        #themeToggle {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.2);
            color: #fff;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; font-size: 1rem;
            flex-shrink: 0;
        }
        #themeToggle:hover { background: rgba(255,255,255,0.22); }

        /* ── Cards ── */
        .glass-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 20px;
            box-shadow: var(--shadow);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        .glass-card-inner {
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: 16px;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        /* ── Buttons ── */
        .btn-purple {
            background: linear-gradient(135deg, var(--purple-600), var(--purple-500));
            border: none; color: #fff;
            border-radius: 12px;
            padding: .55rem 1.4rem;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(124,58,237,.35);
            position: relative; overflow: hidden;
        }
        .btn-purple::before {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,.15), transparent);
            opacity: 0; transition: opacity .2s;
        }
        .btn-purple:hover::before { opacity: 1; }
        .btn-purple:hover {
            color: #fff;
            box-shadow: 0 6px 20px rgba(124,58,237,.5);
            transform: translateY(-1px);
        }
        .btn-purple:active { transform: translateY(0); }
        .btn-ghost {
            background: var(--surface-2);
            border: 1px solid var(--border);
            color: var(--text);
            border-radius: 12px;
            padding: .55rem 1.4rem;
            font-weight: 500;
        }
        .btn-ghost:hover {
            background: var(--purple-100);
            color: var(--purple-700);
            border-color: var(--purple-400);
        }
        [data-theme="dark"] .btn-ghost:hover {
            background: rgba(124,58,237,.2);
            color: var(--purple-400);
        }

        /* ── Form Controls ── */
        .form-control, .form-select {
            background: var(--surface-2) !important;
            border: 1px solid var(--border) !important;
            color: var(--text) !important;
            border-radius: 12px !important;
            padding: .65rem 1rem !important;
        }
        .form-control::placeholder { color: var(--text-muted) !important; }
        .form-control:focus, .form-select:focus {
            border-color: var(--purple-500) !important;
            box-shadow: 0 0 0 3px rgba(139,92,246,.2) !important;
            outline: none !important;
        }
        .form-label { color: var(--text); font-weight: 500; font-size: .9rem; }

        /* ── Alerts ── */
        .alert-purple-success {
            background: linear-gradient(135deg, rgba(124,58,237,.12), rgba(139,92,246,.08));
            border: 1px solid rgba(124,58,237,.3);
            border-left: 4px solid var(--purple-500);
            color: var(--text);
            border-radius: 14px;
        }
        [data-theme="dark"] .alert-purple-success { background: rgba(124,58,237,.15); }

        /* ── Table ── */
        .task-table { width: 100%; border-collapse: separate; border-spacing: 0 8px; }
        .task-table thead th {
            color: var(--text-muted); font-size: .75rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: .08em;
            padding: .5rem 1.25rem; border: none;
        }
        .task-table tbody tr {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: var(--shadow);
            transition: transform .2s ease, box-shadow .2s ease;
        }
        .task-table tbody tr:hover {
            transform: translateY(-2px);
            box-shadow: var(--glow);
        }
        .task-table tbody td {
            padding: 1rem 1.25rem;
            color: var(--text);
            border: none;
        }
        .task-table tbody td:first-child { border-radius: 14px 0 0 14px; }
        .task-table tbody td:last-child  { border-radius: 0 14px 14px 0; }

        /* ── Badges ── */
        .badge-completed {
            background: linear-gradient(135deg, #10b981, #059669);
            color: #fff; border-radius: 50px;
            padding: .3rem .8rem; font-size: .75rem; font-weight: 600;
        }
        .badge-incomplete {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #fff; border-radius: 50px;
            padding: .3rem .8rem; font-size: .75rem; font-weight: 600;
        }

        /* ── Stat Card ── */
        .stat-bubble {
            background: linear-gradient(135deg, var(--purple-600), var(--purple-800));
            border-radius: 20px;
            padding: 1.5rem 2rem;
            color: #fff;
            box-shadow: 0 8px 24px rgba(124,58,237,.4), var(--glow);
            position: relative; overflow: hidden;
        }
        .stat-bubble::after {
            content: '';
            position: absolute; top: -30%; right: -10%;
            width: 120px; height: 120px;
            background: rgba(255,255,255,.08);
            border-radius: 50%;
        }

        /* ── Animations ── */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }
        @keyframes slideRight {
            from { opacity: 0; transform: translateX(-20px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 10px rgba(139,92,246,.3); }
            50%       { box-shadow: 0 0 25px rgba(139,92,246,.6); }
        }

        .anim-fade-up  { animation: fadeUp  .5s ease both; }
        .anim-fade-in  { animation: fadeIn  .4s ease both; }
        .anim-slide-r  { animation: slideRight .4s ease both; }

        .anim-delay-1 { animation-delay: .08s; }
        .anim-delay-2 { animation-delay: .16s; }
        .anim-delay-3 { animation-delay: .24s; }
        .anim-delay-4 { animation-delay: .32s; }

        /* ── Page wrapper ── */
        .page-content { padding: 2rem 0; min-height: calc(100vh - 70px); }
        @media (max-width: 575px) {
            .page-content { padding: 1.25rem 0; }
        }

        /* ── Responsive table wrapper ── */
        .table-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 16px;
        }
        .table-wrap .task-table { min-width: 480px; }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--purple-500); border-radius: 99px; }
    </style>
</head>
<body>

<nav class="app-nav py-2">
    <div class="container d-flex align-items-center gap-3">
        <a href="{{ route('dashboard') }}" class="brand me-auto">
            <span class="brand-icon"><i class="bi bi-check2-all"></i></span>
            Task Manager
        </a>

        <span class="nav-user d-none d-sm-inline">
            <i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name }}
        </span>

        <a href="{{ route('tasks.index') }}" class="nav-pill d-none d-sm-inline-flex">
            <i class="bi bi-list-task"></i> Tasks
        </a>

        <button id="themeToggle" title="Toggle theme" onclick="toggleTheme()">
            <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
        </button>

        <form method="POST" action="{{ route('logout') }}" class="d-inline">
            @csrf
            <button type="submit" class="nav-pill" style="background:rgba(239,68,68,.2);border-color:rgba(239,68,68,.3);">
                <i class="bi bi-box-arrow-right"></i>
                <span class="d-none d-sm-inline">Logout</span>
            </button>
        </form>
    </div>
</nav>

<div class="page-content">
    <div class="container">
        @if(session('success'))
            <div class="alert alert-purple-success alert-dismissible fade show anim-fade-up mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2" style="color:var(--purple-500)"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const html = document.documentElement;
    const icon = document.getElementById('themeIcon');

    function applyTheme(t) {
        html.setAttribute('data-theme', t);
        icon.className = t === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-stars-fill';
    }

    function toggleTheme() {
        const next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
        localStorage.setItem('theme', next);
        applyTheme(next);
    }

    // Apply saved theme immediately
    applyTheme(localStorage.getItem('theme') || 'light');
</script>
</body>
</html>
