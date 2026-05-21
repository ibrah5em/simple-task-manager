<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
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

        [data-bs-theme="dark"] {
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
        html, body {
            height: 100%;
        }
        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Segoe UI', system-ui, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        [data-bs-theme="light"] body {
            background-image:
                radial-gradient(ellipse at 20% 10%, rgba(139,92,246,.18) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 90%, rgba(109,40,217,.12) 0%, transparent 50%);
        }
        [data-bs-theme="dark"] body {
            background-image:
                radial-gradient(ellipse at 20% 10%, rgba(109,40,217,.25) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 90%, rgba(76,29,149,.30) 0%, transparent 50%);
        }

        /* ── App Shell ── */
        .app-shell {
            display: flex;
            flex: 1;
            min-height: 0;
        }

        /* ── Mobile Header ── */
        .mobile-header {
            background: var(--nav-bg);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--border);
            box-shadow: 0 2px 20px rgba(0,0,0,0.2);
            position: sticky;
            top: 0;
            z-index: 1030;
        }
        .mobile-header .brand {
            font-size: 1.1rem;
            font-weight: 700;
            letter-spacing: -.5px;
            color: #fff !important;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: .5rem;
        }
        .mobile-header .hamburger-btn {
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.2);
            color: #fff;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            cursor: pointer;
            flex-shrink: 0;
        }
        .mobile-header .hamburger-btn:hover {
            background: rgba(255,255,255,0.22);
        }
        .mobile-header-dark-btn {
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.2);
            color: #fff;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            cursor: pointer;
            flex-shrink: 0;
        }
        .mobile-header-dark-btn:hover {
            background: rgba(255,255,255,0.22);
        }

        /* ── Brand icon ── */
        .brand-icon {
            width: 30px;
            height: 30px;
            background: rgba(255,255,255,0.2);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .95rem;
            flex-shrink: 0;
        }

        /* ── Sidebar ── */
        .sidebar {
            width: 240px;
            min-height: 100vh;
            background: var(--nav-bg);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            transition: width .25s ease;
            overflow: hidden;
            position: sticky;
            top: 0;
            align-self: flex-start;
            height: 100vh;
        }
        .sidebar.collapsed {
            width: 64px;
        }
        .sidebar-brand {
            padding: 1.25rem 1rem 1rem;
            display: flex;
            align-items: center;
            gap: .6rem;
            color: #fff;
            font-size: 1.1rem;
            font-weight: 700;
            letter-spacing: -.5px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-decoration: none;
            flex-shrink: 0;
            white-space: nowrap;
            overflow: hidden;
        }
        .sidebar-brand:hover {
            color: #fff;
        }
        .sidebar-nav {
            flex: 1;
            padding: .75rem .5rem;
            overflow-y: auto;
            overflow-x: hidden;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,.75);
            border-radius: 10px;
            padding: .6rem .75rem;
            display: flex;
            align-items: center;
            gap: .65rem;
            font-size: .9rem;
            font-weight: 500;
            white-space: nowrap;
            transition: background .15s, color .15s;
            min-height: 44px;
        }
        .sidebar .nav-link.active,
        .sidebar .nav-link:hover {
            background: rgba(255,255,255,.15);
            color: #fff;
        }
        .sidebar .nav-link i {
            font-size: 1.1rem;
            flex-shrink: 0;
            width: 20px;
            text-align: center;
        }
        .sidebar .nav-label {
            transition: opacity .2s, width .2s;
            overflow: hidden;
            white-space: nowrap;
        }
        .sidebar.collapsed .nav-label {
            opacity: 0;
            width: 0;
        }
        .sidebar.collapsed .sidebar-brand .nav-label {
            opacity: 0;
            width: 0;
        }
        .sidebar-footer {
            padding: .75rem .5rem 1rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            flex-shrink: 0;
        }
        .sidebar-footer .user-name {
            color: rgba(255,255,255,.75);
            font-size: .82rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            padding: 0 .25rem .5rem;
            transition: opacity .2s, width .2s;
        }
        .sidebar.collapsed .sidebar-footer .user-name {
            opacity: 0;
            width: 0;
            height: 0;
            padding: 0;
        }
        .sidebar-collapse-btn {
            position: absolute;
            top: 1.25rem;
            right: -13px;
            width: 26px;
            height: 26px;
            background: var(--purple-600);
            border: 2px solid var(--bg);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: .75rem;
            cursor: pointer;
            z-index: 10;
            transition: transform .25s;
        }
        .sidebar.collapsed .sidebar-collapse-btn {
            transform: rotate(180deg);
        }
        .sidebar.collapsed .sidebar-footer .d-flex {
            flex-direction: column;
            align-items: center;
            gap: .5rem;
        }
        .sidebar-wrapper {
            position: relative;
        }
        .dark-toggle-btn {
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.2);
            color: #fff;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            cursor: pointer;
            flex-shrink: 0;
            padding: 0;
        }
        .dark-toggle-btn:hover {
            background: rgba(255,255,255,0.22);
        }
        .logout-circle-btn {
            background: rgba(239,68,68,0.2);
            border: 1px solid rgba(239,68,68,0.3);
            color: rgba(255,255,255,.9);
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            cursor: pointer;
            flex-shrink: 0;
            padding: 0;
            transition: background .15s;
        }
        .logout-circle-btn:hover {
            background: rgba(239,68,68,0.4);
            color: #fff;
        }
        .logout-btn {
            background: rgba(239,68,68,0.2);
            border: 1px solid rgba(239,68,68,0.3);
            color: rgba(255,255,255,.9);
            border-radius: 10px;
            padding: .6rem .75rem;
            width: 100%;
            text-align: left;
            display: flex;
            align-items: center;
            gap: .65rem;
            font-size: .9rem;
            font-weight: 500;
            cursor: pointer;
            transition: background .15s;
            min-height: 44px;
            white-space: nowrap;
        }
        .logout-btn:hover {
            background: rgba(239,68,68,0.35);
            color: #fff;
        }
        .logout-btn i {
            font-size: 1.1rem;
            flex-shrink: 0;
            width: 20px;
            text-align: center;
        }

        /* ── Offcanvas sidebar overrides ── */
        .offcanvas#mobileSidebar {
            background: var(--nav-bg);
            width: 240px !important;
        }
        .offcanvas#mobileSidebar .offcanvas-header {
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding: 1rem;
        }
        .offcanvas#mobileSidebar .offcanvas-header .brand {
            color: #fff;
            font-size: 1.1rem;
            font-weight: 700;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: .6rem;
        }
        .offcanvas#mobileSidebar .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }
        .offcanvas#mobileSidebar .nav-link {
            color: rgba(255,255,255,.75);
            border-radius: 10px;
            padding: .6rem .75rem;
            display: flex;
            align-items: center;
            gap: .65rem;
            font-size: .9rem;
            font-weight: 500;
            min-height: 44px;
        }
        .offcanvas#mobileSidebar .nav-link.active,
        .offcanvas#mobileSidebar .nav-link:hover {
            background: rgba(255,255,255,.15);
            color: #fff;
        }
        .offcanvas#mobileSidebar .nav-link i {
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
        }
        .offcanvas#mobileSidebar .offcanvas-footer {
            padding: .75rem .5rem 1rem;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        .offcanvas#mobileSidebar .user-name {
            color: rgba(255,255,255,.75);
            font-size: .82rem;
            padding: 0 .25rem .5rem;
        }
        .offcanvas#mobileSidebar .logout-btn {
            background: rgba(239,68,68,0.2);
            border: 1px solid rgba(239,68,68,0.3);
            color: rgba(255,255,255,.9);
            border-radius: 10px;
            padding: .6rem .75rem;
            width: 100%;
            text-align: left;
            display: flex;
            align-items: center;
            gap: .65rem;
            font-size: .9rem;
            font-weight: 500;
            cursor: pointer;
            min-height: 44px;
        }
        .offcanvas#mobileSidebar .logout-btn:hover {
            background: rgba(239,68,68,0.35);
            color: #fff;
        }

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
            border: none;
            color: #fff;
            border-radius: 12px;
            padding: .55rem 1.4rem;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(124,58,237,.35);
            position: relative;
            overflow: hidden;
        }
        .btn-purple::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,.15), transparent);
            opacity: 0;
            transition: opacity .2s;
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
        [data-bs-theme="dark"] .btn-ghost:hover {
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
        [data-bs-theme="dark"] .alert-purple-success { background: rgba(124,58,237,.15); }

        /* ── Table ── */
        .task-table { width: 100%; border-collapse: separate; border-spacing: 0 8px; }
        .task-table thead th {
            color: var(--text-muted);
            font-size: .75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .08em;
            padding: .5rem 1.25rem;
            border: none;
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
            color: #fff;
            border-radius: 50px;
            padding: .3rem .8rem;
            font-size: .75rem;
            font-weight: 600;
        }
        .badge-incomplete {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #fff;
            border-radius: 50px;
            padding: .3rem .8rem;
            font-size: .75rem;
            font-weight: 600;
        }

        /* ── Stat Card ── */
        .stat-bubble {
            background: linear-gradient(135deg, var(--purple-600), var(--purple-800));
            border-radius: 20px;
            padding: 1.5rem 2rem;
            color: #fff;
            box-shadow: 0 8px 24px rgba(124,58,237,.4), var(--glow);
            position: relative;
            overflow: hidden;
        }
        .stat-bubble::after {
            content: '';
            position: absolute;
            top: -30%;
            right: -10%;
            width: 120px;
            height: 120px;
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

        /* ── Responsive table wrapper ── */
        .table-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 16px;
        }
        .table-wrap .task-table { min-width: 480px; }

        /* ── Mobile touch targets ── */
        @media (max-width: 767px) {
            /* Bump btn-sm in task table so they're tappable */
            .task-table .btn-sm { min-height: 36px; padding: .35rem .65rem; }
            /* Filter bar inputs: slightly taller on small screens */
            .form-control-sm, .form-select-sm { min-height: 36px; }
            /* Page content padding reduction on very small screens */
            main.flex-grow-1 { padding: 1rem !important; }
        }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--purple-500); border-radius: 99px; }

        /* ── Toast Container ── */
        #toast-container {
            position: fixed;
            bottom: 1.5rem;
            right: 1.5rem;
            z-index: 1100;
            display: flex;
            flex-direction: column;
            gap: .5rem;
            min-width: 280px;
            max-width: 360px;
        }
    </style>
    @stack('head')
</head>
<body>

{{-- Mobile sticky header (visible only on < md) --}}
<header class="mobile-header d-md-none py-2 px-3 d-flex align-items-center gap-2">
    <button class="hamburger-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
        <i class="bi bi-list"></i>
    </button>
    <a href="{{ route('dashboard') }}" class="brand me-auto">
        <span class="brand-icon"><i class="bi bi-check2-all"></i></span>
        Task Manager
    </a>
    <button class="mobile-header-dark-btn" id="mobileQuickAdd"
            data-bs-toggle="modal" data-bs-target="#quickAddModal"
            title="Quick add (c)" style="margin-right:.25rem;">
        <i class="bi bi-plus-lg"></i>
    </button>
    <button class="mobile-header-dark-btn" id="mobileThemeToggle" title="Toggle dark mode">
        <i class="bi bi-moon-stars-fill" id="mobileThemeIcon"></i>
    </button>
</header>

@auth
@php
    $overdueCount = auth()->user()->tasks()
        ->where('is_completed', false)
        ->whereDate('due_date', '<', today())
        ->count();
    $todayCount = auth()->user()->tasks()
        ->where('is_completed', false)
        ->whereDate('due_date', '<=', today())
        ->count();
    $inboxCount = auth()->user()->tasks()
        ->where('is_completed', false)
        ->whereNull('due_date')
        ->count();
@endphp
@endauth

{{-- Mobile Offcanvas Sidebar --}}
<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
    <div class="offcanvas-header">
        <a href="{{ route('dashboard') }}" class="brand" id="mobileSidebarLabel">
            <span class="brand-icon"><i class="bi bi-check2-all"></i></span>
            Task Manager
        </a>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <nav class="px-2 py-2">
            <a href="{{ route('dashboard') }}"
               class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }} mb-1">
                <i class="bi bi-house"></i> Dashboard
            </a>
            <a href="{{ route('today') }}"
               class="nav-link {{ request()->routeIs('today') ? 'active' : '' }} mb-1 d-flex align-items-center justify-content-between">
                <span><i class="bi bi-calendar-check me-1"></i> Today</span>
                @auth @if(($todayCount ?? 0) > 0)
                <span class="badge rounded-pill" style="background:var(--purple-500);font-size:.65rem;">{{ $todayCount }}</span>
                @endif @endauth
            </a>
            <a href="{{ route('inbox') }}"
               class="nav-link {{ request()->routeIs('inbox') ? 'active' : '' }} mb-1 d-flex align-items-center justify-content-between">
                <span><i class="bi bi-inbox me-1"></i> Inbox</span>
                @auth @if(($inboxCount ?? 0) > 0)
                <span class="badge rounded-pill" style="background:var(--purple-500);font-size:.65rem;">{{ $inboxCount }}</span>
                @endif @endauth
            </a>
            <a href="{{ route('upcoming') }}"
               class="nav-link {{ request()->routeIs('upcoming') ? 'active' : '' }} mb-1">
                <i class="bi bi-calendar-week me-1"></i> Upcoming
            </a>
            <a href="{{ route('calendar') }}"
               class="nav-link {{ request()->routeIs('calendar') ? 'active' : '' }} mb-1">
                <i class="bi bi-calendar3 me-1"></i> Calendar
            </a>
            <a href="{{ route('tasks.index') }}"
               class="nav-link {{ request()->routeIs('tasks.*') ? 'active' : '' }} mb-1 d-flex align-items-center justify-content-between">
                <span><i class="bi bi-list-task me-1"></i> Tasks</span>
                @auth @if(($overdueCount ?? 0) > 0)
                <span class="badge rounded-pill" style="background:#ef4444;font-size:.65rem;">{{ $overdueCount }}</span>
                @endif @endauth
            </a>
            <a href="{{ route('categories.index') }}"
               class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }} mb-1">
                <i class="bi bi-tag"></i> Categories
            </a>
            @auth
            <a href="{{ route('profile.edit') }}"
               class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }} mb-1">
                <i class="bi bi-person"></i> Profile
            </a>
            @if(auth()->user()->role === 'admin')
            <a href="/admin/users"
               class="nav-link {{ request()->is('admin/*') ? 'active' : '' }} mb-1">
                <i class="bi bi-shield"></i> Admin
            </a>
            @endif
            @endauth
        </nav>
    </div>
    <div class="offcanvas-footer px-2 py-3">
        <form method="POST" action="{{ route('logout') }}" class="m-0">
            @csrf
            <button type="submit" class="logout-circle-btn" title="Logout">
                <i class="bi bi-box-arrow-right"></i>
            </button>
        </form>
    </div>
</div>

{{-- App Shell: sidebar + main --}}
<div class="app-shell">

    {{-- Desktop Sidebar (hidden on mobile) --}}
    <div class="sidebar-wrapper d-none d-md-block">
        <div class="sidebar" id="desktopSidebar">

            <a href="{{ route('dashboard') }}" class="sidebar-brand">
                <span class="brand-icon"><i class="bi bi-check2-all"></i></span>
                <span class="nav-label">Task Manager</span>
            </a>

            <nav class="sidebar-nav">
                <a href="{{ route('dashboard') }}"
                   class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }} mb-1">
                    <i class="bi bi-house"></i>
                    <span class="nav-label">Dashboard</span>
                </a>
                <a href="{{ route('today') }}"
                   class="nav-link {{ request()->routeIs('today') ? 'active' : '' }} mb-1">
                    <i class="bi bi-calendar-check" style="flex-shrink:0;"></i>
                    <span class="nav-label flex-grow-1">Today</span>
                    @auth @if(($todayCount ?? 0) > 0)
                    <span class="nav-label badge rounded-pill ms-auto" style="background:var(--purple-500);font-size:.65rem;">{{ $todayCount }}</span>
                    @endif @endauth
                </a>
                <a href="{{ route('inbox') }}"
                   class="nav-link {{ request()->routeIs('inbox') ? 'active' : '' }} mb-1">
                    <i class="bi bi-inbox" style="flex-shrink:0;"></i>
                    <span class="nav-label flex-grow-1">Inbox</span>
                    @auth @if(($inboxCount ?? 0) > 0)
                    <span class="nav-label badge rounded-pill ms-auto" style="background:var(--purple-500);font-size:.65rem;">{{ $inboxCount }}</span>
                    @endif @endauth
                </a>
                <a href="{{ route('upcoming') }}"
                   class="nav-link {{ request()->routeIs('upcoming') ? 'active' : '' }} mb-1">
                    <i class="bi bi-calendar-week" style="flex-shrink:0;"></i>
                    <span class="nav-label">Upcoming</span>
                </a>
                <a href="{{ route('calendar') }}"
                   class="nav-link {{ request()->routeIs('calendar') ? 'active' : '' }} mb-1">
                    <i class="bi bi-calendar3" style="flex-shrink:0;"></i>
                    <span class="nav-label">Calendar</span>
                </a>
                <a href="{{ route('tasks.index') }}"
                   class="nav-link {{ request()->routeIs('tasks.*') ? 'active' : '' }} mb-1">
                    <i class="bi bi-list-task" style="flex-shrink:0;"></i>
                    <span class="nav-label flex-grow-1">Tasks</span>
                    @auth @if(($overdueCount ?? 0) > 0)
                    <span class="nav-label badge rounded-pill ms-auto" style="background:#ef4444;font-size:.65rem;">{{ $overdueCount }}</span>
                    @endif @endauth
                </a>
                <a href="{{ route('categories.index') }}"
                   class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }} mb-1">
                    <i class="bi bi-tag"></i>
                    <span class="nav-label">Categories</span>
                </a>
                @auth
                <a href="{{ route('profile.edit') }}"
                   class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }} mb-1">
                    <i class="bi bi-person"></i>
                    <span class="nav-label">Profile</span>
                </a>
                @if(auth()->user()->role === 'admin')
                <a href="/admin/users"
                   class="nav-link {{ request()->is('admin/*') ? 'active' : '' }} mb-1">
                    <i class="bi bi-shield"></i>
                    <span class="nav-label">Admin</span>
                </a>
                @endif
                @endauth
            </nav>

            <div class="sidebar-footer">
                <div class="d-flex align-items-center gap-2">
                    <button class="dark-toggle-btn" id="desktopThemeToggle" title="Toggle dark mode">
                        <i class="bi bi-moon-stars-fill" id="desktopThemeIcon"></i>
                    </button>
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="logout-circle-btn" title="Logout">
                            <i class="bi bi-box-arrow-right"></i>
                        </button>
                    </form>
                </div>
            </div>

        </div>
        {{-- Collapse toggle button on sidebar edge --}}
        <button class="sidebar-collapse-btn" id="sidebarCollapseBtn" title="Toggle sidebar">
            <i class="bi bi-chevron-left"></i>
        </button>
    </div>

    {{-- Main content --}}
    <main class="flex-grow-1 p-3 p-md-4" style="min-width: 0;">

        {{-- Flash data for JS toast system --}}
        @if(session('success'))
            <div class="d-none" data-flash-type="success" data-flash-msg="{{ session('success') }}"></div>
        @endif
        @if(session('error'))
            <div class="d-none" data-flash-type="error" data-flash-msg="{{ session('error') }}"></div>
        @endif
        @if(session('info'))
            <div class="d-none" data-flash-type="info" data-flash-msg="{{ session('info') }}"></div>
        @endif

        @yield('content')
    </main>

</div>

{{-- Toast container --}}
<div id="toast-container" aria-live="polite" aria-atomic="true"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    /* ── Dark Mode ── */
    (function () {
        const html = document.documentElement;

        function getPreferredTheme() {
            const saved = localStorage.getItem('theme');
            if (saved) return saved;
            return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }

        function applyTheme(t) {
            html.setAttribute('data-bs-theme', t);
            const isDark = t === 'dark';
            // Desktop icons
            const di = document.getElementById('desktopThemeIcon');
            if (di) di.className = isDark ? 'bi bi-sun-fill' : 'bi bi-moon-stars-fill';
            // Mobile icons
            const mi = document.getElementById('mobileThemeIcon');
            if (mi) mi.className = isDark ? 'bi bi-sun-fill' : 'bi bi-moon-stars-fill';
        }

        function toggleTheme() {
            const current = html.getAttribute('data-bs-theme') || 'light';
            const next = current === 'dark' ? 'light' : 'dark';
            localStorage.setItem('theme', next);
            applyTheme(next);
        }

        // Apply on load
        applyTheme(getPreferredTheme());

        // Wire up toggle buttons after DOM ready
        document.addEventListener('DOMContentLoaded', function () {
            const desktopBtn = document.getElementById('desktopThemeToggle');
            const mobileBtn  = document.getElementById('mobileThemeToggle');
            if (desktopBtn) desktopBtn.addEventListener('click', toggleTheme);
            if (mobileBtn)  mobileBtn.addEventListener('click', toggleTheme);
        });
    })();

    /* ── Toast System ── */
    window.toast = function (message, type) {
        type = type || 'success';
        const typeMap = {
            success: 'bg-success',
            error:   'bg-danger',
            danger:  'bg-danger',
            info:    'bg-info',
            warning: 'bg-warning text-dark'
        };
        const bgClass = typeMap[type] || 'bg-secondary';

        const el = document.createElement('div');
        el.className = 'toast align-items-center text-white border-0 show ' + bgClass;
        el.setAttribute('role', 'alert');
        el.setAttribute('aria-live', 'assertive');
        el.setAttribute('aria-atomic', 'true');
        el.innerHTML =
            '<div class="d-flex">' +
            '<div class="toast-body">' + message + '</div>' +
            '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>' +
            '</div>';

        const container = document.getElementById('toast-container');
        if (container) container.appendChild(el);

        // Auto-dismiss after 4s
        setTimeout(function () {
            el.classList.remove('show');
            setTimeout(function () { el.remove(); }, 300);
        }, 4000);
    };

    /* ── Flash messages from session ── */
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-flash-type]').forEach(function (el) {
            const type = el.getAttribute('data-flash-type');
            const msg  = el.getAttribute('data-flash-msg');
            if (msg) window.toast(msg, type);
        });
    });

    /* ── Button loading states ── */
    document.querySelectorAll('form').forEach(function (form) {
        form.addEventListener('submit', function () {
            form.querySelectorAll('[type=submit]').forEach(function (btn) {
                btn.disabled = true;
                btn.dataset.originalText = btn.innerHTML;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving…';
            });
        });
    });

    /* ── Sidebar Collapse (desktop) ── */
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar    = document.getElementById('desktopSidebar');
        const collapseBtn = document.getElementById('sidebarCollapseBtn');
        if (!sidebar || !collapseBtn) return;

        function applyCollapsed(collapsed) {
            if (collapsed) {
                sidebar.classList.add('collapsed');
            } else {
                sidebar.classList.remove('collapsed');
            }
        }

        const savedCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        applyCollapsed(savedCollapsed);

        collapseBtn.addEventListener('click', function () {
            const isCollapsed = sidebar.classList.contains('collapsed');
            const next = !isCollapsed;
            localStorage.setItem('sidebarCollapsed', next ? 'true' : 'false');
            applyCollapsed(next);
        });
    });
</script>

{{-- Quick-Add Modal (global, available on every page) --}}
@auth
<div class="modal fade" id="quickAddModal" tabindex="-1" aria-labelledby="quickAddModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:540px;">
        <div class="modal-content glass-card border-0" style="background:var(--surface);backdrop-filter:blur(20px);">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h5 class="modal-title fw-bold mb-0" id="quickAddModalLabel" style="color:var(--text)">
                        <i class="bi bi-lightning-charge-fill me-1" style="color:var(--purple-500)"></i>Quick Add
                    </h5>
                    <p class="mb-0 mt-1" style="color:var(--text-muted);font-size:.8rem;">
                        Try: <code style="color:var(--purple-500);background:transparent;">Buy milk tomorrow !high #shopping</code>
                    </p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-2">
                <div style="position:relative;">
                    <input type="text" id="quickAddInput" class="form-control"
                           placeholder="Task title… + date, !priority, #category"
                           autocomplete="off" maxlength="500"
                           style="padding-right:3rem;font-size:1rem;">
                    <div id="quickAddSpinner" class="d-none" style="position:absolute;right:.85rem;top:50%;transform:translateY(-50%);">
                        <span class="spinner-border spinner-border-sm" style="color:var(--purple-500)"></span>
                    </div>
                </div>

                {{-- Live preview --}}
                <div id="quickAddPreview" class="mt-3 d-none">
                    <div class="glass-card-inner p-3" style="border-radius:12px;">
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <span id="qaPreviewTitle" class="fw-semibold" style="color:var(--text)"></span>
                            <span id="qaPreviewPriority" class="badge rounded-pill" style="font-size:.7rem;"></span>
                            <span id="qaPreviewDate" class="badge rounded-pill" style="background:var(--purple-500)20;color:var(--purple-500);border:1px solid var(--purple-500)40;font-size:.7rem;"></span>
                        </div>
                        <div id="qaPreviewCategories" class="d-flex flex-wrap gap-1 mt-2"></div>
                        <div id="qaPreviewUnknown" class="mt-2 d-none">
                            <small style="color:#f59e0b;"><i class="bi bi-exclamation-triangle me-1"></i>New categories will be created: <span id="qaUnknownList"></span></small>
                        </div>
                    </div>
                </div>

                {{-- Validation error --}}
                <div id="quickAddError" class="mt-2 d-none">
                    <small class="text-danger" id="quickAddErrorMsg"></small>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 d-flex justify-content-between align-items-center">
                <small style="color:var(--text-muted);font-size:.75rem;">
                    <kbd>Enter</kbd> to save &nbsp;·&nbsp; <kbd>Esc</kbd> to close
                </small>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-ghost btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="quickAddSave" class="btn btn-purple btn-sm">
                        <i class="bi bi-plus-circle me-1"></i>Add Task
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endauth

@auth
<script>
(function () {
    const CSRF  = document.querySelector('meta[name="csrf-token"]').content;
    const modal = new bootstrap.Modal(document.getElementById('quickAddModal'));
    const input = document.getElementById('quickAddInput');
    let debounceTimer = null;
    let lastParsed    = null;

    // ── Open on 'c' when not in input/textarea ──────────────────────────────
    document.addEventListener('keydown', function (e) {
        if (e.key === 'c' && !e.ctrlKey && !e.metaKey && !e.altKey) {
            const tag = document.activeElement.tagName.toLowerCase();
            if (tag !== 'input' && tag !== 'textarea' && tag !== 'select') {
                e.preventDefault();
                openQuickAdd();
            }
        }
    });

    document.getElementById('quickAddModal').addEventListener('shown.bs.modal', function () {
        input.focus();
        input.select();
    });

    document.getElementById('quickAddModal').addEventListener('hidden.bs.modal', function () {
        resetModal();
    });

    function openQuickAdd() {
        modal.show();
    }

    // ── Live preview via debounced fetch ────────────────────────────────────
    input.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        const val = this.value.trim();
        if (!val) { hidePreview(); return; }
        debounceTimer = setTimeout(() => fetchPreview(val), 350);
    });

    function fetchPreview(val) {
        document.getElementById('quickAddSpinner').classList.remove('d-none');
        fetch('{{ route("tasks.parse") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN':  CSRF,
                'Accept':        'application/json',
            },
            body: JSON.stringify({ input: val }),
        })
        .then(r => r.json())
        .then(data => {
            lastParsed = data;
            renderPreview(data);
        })
        .catch(() => hidePreview())
        .finally(() => document.getElementById('quickAddSpinner').classList.add('d-none'));
    }

    function renderPreview(d) {
        if (!d.title && !d.due_date && d.priority === 'medium' && d.category_names.length === 0) {
            hidePreview(); return;
        }

        document.getElementById('qaPreviewTitle').textContent = d.title || '(no title)';

        // Priority chip
        const pEl = document.getElementById('qaPreviewPriority');
        const pColors = { high: '#ef4444', medium: '#f59e0b', low: '#10b981' };
        const p = d.priority || 'medium';
        pEl.textContent = p.charAt(0).toUpperCase() + p.slice(1);
        pEl.style.background = (pColors[p] || '#8b5cf6') + '20';
        pEl.style.color       = pColors[p] || '#8b5cf6';
        pEl.style.border      = '1px solid ' + (pColors[p] || '#8b5cf6') + '40';

        // Date chip
        const dEl = document.getElementById('qaPreviewDate');
        if (d.due_date_human) {
            dEl.textContent = '📅 ' + d.due_date_human;
            dEl.classList.remove('d-none');
        } else {
            dEl.classList.add('d-none');
        }

        // Category chips
        const catEl = document.getElementById('qaPreviewCategories');
        catEl.innerHTML = '';
        (d.category_names || []).forEach(function (name) {
            const span = document.createElement('span');
            span.className   = 'badge rounded-pill';
            span.textContent = '#' + name;
            span.style.cssText = 'background:var(--purple-500)15;color:var(--purple-500);border:1px solid var(--purple-500)30;font-size:.7rem;';
            catEl.appendChild(span);
        });

        // Unknown categories warning
        const unkEl  = document.getElementById('qaPreviewUnknown');
        const unkList = document.getElementById('qaUnknownList');
        if (d.unknown_categories && d.unknown_categories.length > 0) {
            unkList.textContent = d.unknown_categories.map(s => '#' + s).join(', ');
            unkEl.classList.remove('d-none');
        } else {
            unkEl.classList.add('d-none');
        }

        document.getElementById('quickAddPreview').classList.remove('d-none');
        document.getElementById('quickAddError').classList.add('d-none');
    }

    function hidePreview() {
        document.getElementById('quickAddPreview').classList.add('d-none');
        lastParsed = null;
    }

    // ── Save ────────────────────────────────────────────────────────────────
    document.getElementById('quickAddSave').addEventListener('click', saveTask);

    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); saveTask(); }
    });

    function saveTask() {
        const val = input.value.trim();
        if (!val) {
            showError('Please enter a task title.');
            return;
        }

        const btn = document.getElementById('quickAddSave');
        btn.disabled    = true;
        btn.innerHTML   = '<span class="spinner-border spinner-border-sm me-1"></span>Saving…';

        // Build FormData from lastParsed (if available) or send raw for server-side parse
        const parsed = lastParsed || {};
        const body   = new URLSearchParams();
        body.append('_token', CSRF);
        body.append('title',  parsed.title || val);
        if (parsed.due_date) body.append('due_date', parsed.due_date.substring(0, 10));
        if (parsed.priority)  body.append('priority', parsed.priority);
        (parsed.categories || []).forEach(id => body.append('categories[]', id));
        body.append('quick_add', '1');

        fetch('{{ route("tasks.store") }}', {
            method:  'POST',
            headers: {
                'Content-Type':  'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN':   CSRF,
                'Accept':         'application/json, text/html',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: body.toString(),
            redirect: 'manual',
        })
        .then(r => {
            if (r.ok || r.status === 302 || r.type === 'opaqueredirect') {
                modal.hide();
                window.toast('Task added!', 'success');
                // Reload if on a task list page
                const listPages = ['/tasks', '/today', '/inbox', '/upcoming', '/calendar', '/dashboard'];
                if (listPages.some(p => window.location.pathname.startsWith(p))) {
                    setTimeout(() => window.location.reload(), 600);
                }
            } else {
                return r.json().then(d => {
                    const msgs = Object.values(d.errors || {}).flat();
                    showError(msgs[0] || 'Could not save task.');
                });
            }
        })
        .catch(() => showError('Network error. Please try again.'))
        .finally(() => {
            btn.disabled  = false;
            btn.innerHTML = '<i class="bi bi-plus-circle me-1"></i>Add Task';
        });
    }

    function showError(msg) {
        document.getElementById('quickAddErrorMsg').textContent = msg;
        document.getElementById('quickAddError').classList.remove('d-none');
    }

    function resetModal() {
        input.value = '';
        hidePreview();
        document.getElementById('quickAddError').classList.add('d-none');
        lastParsed = null;
    }
})();
</script>
@endauth

@stack('scripts')
</body>
</html>
