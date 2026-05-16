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
        :root {
            --purple-400: #a78bfa;
            --purple-500: #8b5cf6;
            --purple-600: #7c3aed;
            --purple-700: #6d28d9;
            --purple-800: #5b21b6;
            --bg:       #f5f3ff;
            --surface:  rgba(255,255,255,0.80);
            --border:   rgba(124,58,237,0.15);
            --text:     #1e1b4b;
            --text-muted: #6b7280;
        }
        [data-theme="dark"] {
            --bg:       #0f0a1e;
            --surface:  rgba(30,20,60,0.85);
            --border:   rgba(167,139,250,0.2);
            --text:     #ede9fe;
            --text-muted: #a78bfa;
        }

        *, *::before, *::after { transition: background-color .3s ease, color .2s ease; }

        body {
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            font-family: 'Segoe UI', system-ui, sans-serif;
            display: flex; align-items: center; justify-content: center;
        }
        [data-theme="light"] body {
            background-image:
                radial-gradient(ellipse at 15% 15%, rgba(139,92,246,.22) 0%, transparent 50%),
                radial-gradient(ellipse at 85% 85%, rgba(109,40,217,.16) 0%, transparent 50%);
        }
        [data-theme="dark"] body {
            background-image:
                radial-gradient(ellipse at 15% 15%, rgba(109,40,217,.3) 0%, transparent 50%),
                radial-gradient(ellipse at 85% 85%, rgba(76,29,149,.35) 0%, transparent 50%);
        }

        /* Floating orbs */
        .orb {
            position: fixed; border-radius: 50%;
            filter: blur(60px); opacity: .35;
            pointer-events: none; z-index: 0;
            animation: drift 8s ease-in-out infinite alternate;
        }
        .orb-1 { width: 320px; height: 320px; background: var(--purple-600); top: -80px; left: -80px; }
        .orb-2 { width: 260px; height: 260px; background: var(--purple-400); bottom: -60px; right: -60px; animation-delay: -4s; }
        @keyframes drift {
            from { transform: translate(0, 0) scale(1); }
            to   { transform: translate(20px, 20px) scale(1.08); }
        }

        .auth-wrapper { position: relative; z-index: 1; width: 100%; max-width: 440px; padding: 1rem; }

        .auth-logo {
            text-align: center; margin-bottom: 1.8rem;
            animation: fadeDown .6s ease both;
        }
        .logo-circle {
            width: 64px; height: 64px;
            background: linear-gradient(135deg, var(--purple-600), var(--purple-400));
            border-radius: 18px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 1.75rem; color: #fff;
            box-shadow: 0 8px 24px rgba(124,58,237,.45);
            margin-bottom: .75rem;
        }
        .auth-logo h1 { font-size: 1.4rem; font-weight: 700; color: var(--text); margin: 0; }
        .auth-logo p  { color: var(--text-muted); font-size: .875rem; margin: .2rem 0 0; }

        .auth-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 2rem;
            box-shadow: 0 20px 60px rgba(0,0,0,.12);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            animation: fadeUp .55s ease both .1s;
        }

        .form-control {
            background: rgba(124,58,237,.06) !important;
            border: 1px solid var(--border) !important;
            color: var(--text) !important;
            border-radius: 12px !important;
            padding: .7rem 1rem !important;
        }
        [data-theme="dark"] .form-control {
            background: rgba(124,58,237,.15) !important;
        }
        .form-control::placeholder { color: var(--text-muted) !important; }
        .form-control:focus {
            border-color: var(--purple-500) !important;
            box-shadow: 0 0 0 3px rgba(139,92,246,.2) !important;
        }
        .form-label { color: var(--text); font-weight: 500; font-size: .875rem; }

        .input-icon-wrap { position: relative; }
        .input-icon-wrap .bi {
            position: absolute; left: .85rem; top: 50%; transform: translateY(-50%);
            color: var(--text-muted); font-size: .95rem; pointer-events: none;
        }
        .input-icon-wrap .form-control { padding-left: 2.4rem !important; }

        .btn-auth {
            background: linear-gradient(135deg, var(--purple-600), var(--purple-500));
            border: none; color: #fff;
            border-radius: 12px; padding: .75rem;
            font-weight: 600; font-size: 1rem; width: 100%;
            box-shadow: 0 4px 16px rgba(124,58,237,.4);
            position: relative; overflow: hidden;
        }
        .btn-auth::before {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,.15), transparent);
            opacity: 0; transition: opacity .2s;
        }
        .btn-auth:hover::before { opacity: 1; }
        .btn-auth:hover { color: #fff; box-shadow: 0 6px 22px rgba(124,58,237,.55); transform: translateY(-1px); }
        .btn-auth:active { transform: translateY(0); }

        .divider { display: flex; align-items: center; gap: .75rem; color: var(--text-muted); font-size: .8rem; margin: 1.25rem 0; }
        .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: var(--border); }

        .auth-link { color: var(--purple-500); text-decoration: none; font-weight: 500; }
        .auth-link:hover { color: var(--purple-400); text-decoration: underline; }

        .theme-btn-top {
            position: fixed; top: 1rem; right: 1rem;
            width: 40px; height: 40px; border-radius: 50%;
            background: var(--surface); border: 1px solid var(--border);
            color: var(--text); font-size: 1.1rem;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; z-index: 100;
            box-shadow: 0 2px 12px rgba(0,0,0,.12);
        }
        .theme-btn-top:hover { background: rgba(124,58,237,.1); }

        @keyframes fadeUp   { from { opacity:0; transform: translateY(28px); } to { opacity:1; transform:translateY(0); } }
        @keyframes fadeDown { from { opacity:0; transform: translateY(-16px); } to { opacity:1; transform:translateY(0); } }

        .invalid-feedback { font-size: .8rem; }
    </style>
</head>
<body>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>

    <button class="theme-btn-top" onclick="toggleTheme()" title="Toggle theme">
        <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
    </button>

    <div class="auth-wrapper">
        <div class="auth-logo">
            <div class="logo-circle"><i class="bi bi-check2-all"></i></div>
            <h1>Task Manager</h1>
            <p>Stay organized, stay productive</p>
        </div>
        {{ $slot }}
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
        applyTheme(localStorage.getItem('theme') || 'light');
    </script>
</body>
</html>
