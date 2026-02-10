<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>
    <style>
        :root {
            --bg: #f3f4f6;
            --sidebar: #1f2937;
            --sidebar-2: #111827;
            --card: #ffffff;
            --muted: #9ca3af;
            --text: #111827;
            --accent: #3b82f6;
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
        .brand { font-weight: 700; font-size: 14px; letter-spacing: .5px; margin: 4px 8px 16px; }
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
        .cards { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; margin-bottom: 14px; }
        .card {
            background: var(--card);
            border-radius: 10px;
            padding: 14px 16px;
            border: 1px solid #e5e7eb;
        }
        .card small { color: #6b7280; font-size: 11px; display: block; margin-bottom: 6px; }
        .card .value { font-size: 22px; font-weight: 700; }
        .panel {
            background: var(--card);
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            padding: 14px 16px;
        }
        .panel h3 { margin: 0 0 10px; font-size: 13px; }
        .placeholder {
            height: 220px;
            background: #eef0f4;
            border-radius: 8px;
            border: 1px dashed #d1d5db;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6b7280;
            font-size: 12px;
        }
        @media (max-width: 900px) {
            .cards { grid-template-columns: 1fr; }
            .sidebar { width: 180px; }
        }
    </style>
</head>
<body>
    <div class="layout">
        <aside class="sidebar">
            <div class="brand">ADMIN PANEL</div>
            <nav class="nav">
                <a class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
                <a class="{{ request()->routeIs('admin.guru.*') ? 'active' : '' }}" href="{{ route('admin.guru.index') }}">Data Guru</a>
                <a class="{{ request()->routeIs('admin.siswa.*') ? 'active' : '' }}" href="{{ route('admin.siswa.index') }}">Data Siswa</a>
                <a class="{{ request()->routeIs('admin.kelas.*') ? 'active' : '' }}" href="{{ route('admin.kelas.index') }}">Kelas</a>
                <a class="{{ request()->routeIs('admin.mapel.*') ? 'active' : '' }}" href="{{ route('admin.mapel.index') }}">Mapel</a>
                <a class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">Users</a>
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
