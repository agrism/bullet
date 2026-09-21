<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') | Bullet CMS</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #242166;
            --primary-hover: #1b1950;
            --accent: #ff6211;
            --accent-hover: #e0530a;
            --gold: #ffba00;
            --bg-main: #f8fafc;
            --bg-card: #ffffff;
            --border-color: #e2e8f0;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --text-light: #94a3b8;
            --success: #10b981;
            --danger: #ef4444;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg-main);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .admin-nav {
            background-color: var(--primary);
            color: #ffffff;
            padding: 0 24px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .admin-nav__brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #ffffff;
            font-weight: 800;
            font-size: 1.25rem;
            letter-spacing: 0.05rem;
        }

        .admin-nav__badge {
            background: rgba(255, 255, 255, 0.15);
            font-size: 0.75rem;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: 600;
            letter-spacing: normal;
        }

        .admin-nav__links {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .admin-nav__link {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            padding: 6px 12px;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .admin-nav__link:hover, .admin-nav__link.active {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.12);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
            border: none;
            outline: none;
        }

        .btn-primary {
            background-color: var(--primary);
            color: #ffffff;
        }
        .btn-primary:hover {
            background-color: var(--primary-hover);
        }

        .btn-accent {
            background-color: var(--accent);
            color: #ffffff;
        }
        .btn-accent:hover {
            background-color: var(--accent-hover);
        }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--border-color);
            color: var(--text-dark);
        }
        .btn-outline:hover {
            background: #f1f5f9;
        }

        .btn-ghost-light {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .btn-ghost-light:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .btn-danger {
            background-color: #fee2e2;
            color: #dc2626;
        }
        .btn-danger:hover {
            background-color: #fecaca;
        }

        .btn-sm {
            padding: 4px 10px;
            font-size: 0.8rem;
        }

        .admin-container {
            max-width: 1280px;
            width: 100%;
            margin: 0 auto;
            padding: 32px 24px;
            flex: 1;
        }

        .alert {
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.925rem;
            animation: fadeIn 0.3s ease;
        }

        .alert-success {
            background-color: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
        }

        .alert-error {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .card__header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card__title {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--text-dark);
        }

        .card__body {
            padding: 24px;
        }

        .locale-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 4px;
            text-transform: uppercase;
        }

        .locale-badge--lv {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .locale-badge--en {
            background-color: #e0f2fe;
            color: #0369a1;
        }

        .locale-badge--ru {
            background-color: #fef3c7;
            color: #92400e;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-4px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    @stack('styles')
</head>
<body>
    @auth
    <nav class="admin-nav">
        <a href="{{ route('admin.pages.index') }}" class="admin-nav__brand">
            BULLET <span class="admin-nav__badge">CMS</span>
        </a>

        <div class="admin-nav__links">
            <a href="{{ route('admin.pages.index') }}" class="admin-nav__link {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">
                Pages (Content)
            </a>
            <a href="{{ route('admin.password') }}" class="admin-nav__link {{ request()->routeIs('admin.password') ? 'active' : '' }}">
                Account
            </a>
            <a href="{{ url('/') }}" target="_blank" class="btn btn-ghost-light btn-sm">
                View Site &UpperRightArrow;
            </a>
            <form action="{{ route('admin.cache.clear') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-ghost-light btn-sm" title="Purge cached pages from memory and filesystem">
                    &#x21bb; Clear Cache
                </button>
            </form>
            <form action="{{ route('admin.logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-sm btn-accent">
                    Logout
                </button>
            </form>
        </div>
    </nav>
    @endauth

    <div class="admin-container">
        @if(session('success'))
            <div class="alert alert-success">
                <span>{{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;color:inherit;font-weight:bold;">&times;</button>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="alert alert-error">
                <div>
                    <strong>Please check the form for errors:</strong>
                    <ul style="margin-top:4px;padding-left:16px;">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
                <button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;color:inherit;font-weight:bold;">&times;</button>
            </div>
        @endif

        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>
