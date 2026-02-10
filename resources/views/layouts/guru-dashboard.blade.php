<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Guru')</title>
    <style>
        :root {
            --sidebar: #1f2937;
            --sidebar-2: #111827;
            --bg: #f3f4f6;
            --card: #ffffff;
            --muted: #6b7280;
            --text: #111827;
            --accent: #2563eb;
        }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: "Segoe UI", Arial, sans-serif; background: var(--bg); color: var(--text); }
        .layout { display: flex; min-height: 100vh; }
        .sidebar {
            width: 220px;
            background: linear-gradient(180deg, var(--sidebar), var(--sidebar-2));
            color: #fff;
            padding: 16px 12px;
        }
        .brand { font-weight: 700; font-size: 13px; letter-spacing: .5px; margin: 4px 8px 16px; }
        .nav a {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #d1d5db;
            text-decoration: none;
            padding: 10px 12px;
            border-radius: 8px;
            font-size: 13px;
        }
        .nav a.active, .nav a:hover { background: rgba(255,255,255,0.08); color: #fff; }
        .content { flex: 1; padding: 20px 28px; }
        .topbar { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
        .title { font-size: 16px; font-weight: 700; }
        .user { display: flex; align-items: center; gap: 8px; font-size: 12px; color: #4b5563; }
        @media (max-width: 900px) {
            .sidebar { width: 180px; }
        }
    </style>
</head>
<body>
    <div class="layout">
        <aside class="sidebar">
            <div class="brand">TEACHER PANEL</div>
            <nav class="nav">
                <a class="{{ request()->routeIs('guru.dashboard') ? 'active' : '' }}" href="{{ route('guru.dashboard') }}">Dashboard</a>
                <a class="{{ request()->routeIs('guru.penilaian.*') ? 'active' : '' }}" href="{{ route('guru.penilaian.index') }}">Kelola Penilaian</a>
                <a class="{{ request()->routeIs('guru.siswa.*') ? 'active' : '' }}" href="{{ route('guru.siswa.index') }}">Data Siswa</a>
                <a href="#" onclick="return false;">Riwayat Analisis</a>
                <form method="POST" action="{{ route('logout') }}" style="margin-top: 10px;">
                    @csrf
                    <button type="submit" style="width: 100%; text-align: left; background: none; border: none; color: #d1d5db; padding: 10px 12px; cursor: pointer;">
                        Logout
                    </button>
                </form>
            </nav>
        </aside>
        <main class="content">
            @yield('content')
        </main>
    </div>
</body>
</html>
