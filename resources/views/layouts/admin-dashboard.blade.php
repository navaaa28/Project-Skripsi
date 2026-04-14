<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>
    <link rel="icon" type="image/png" href="{{ asset('storage/icon.png') }}">
    <link rel="shortcut icon" href="{{ asset('storage/icon.png') }}">
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
                <a class="{{ request()->routeIs('admin.tahun-ajaran.*') ? 'active' : '' }}" href="{{ route('admin.tahun-ajaran.index') }}">Tahun Ajaran</a>
                <a class="{{ request()->routeIs('admin.kenaikan-kelas.*') ? 'active' : '' }}" href="{{ route('admin.kenaikan-kelas.index') }}">Kenaikan Kelas</a>
                <form id="logoutForm" method="POST" action="{{ route('logout') }}" style="margin-top: 10px;">
                    @csrf
                    <button type="button" onclick="document.getElementById('logoutModal').style.display='flex'" style="width: 100%; text-align: left; background: none; border: none; color: #d1d5db; padding: 10px 12px; cursor: pointer;">
                        Logout
                    </button>
                </form>
            </nav>
        </aside>
        <main class="content">
            @if ($errors->any())
                <div style="margin-bottom: 12px; background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; border-radius: 8px; padding: 10px 12px; font-size: 12px;">
                    <ul style="margin: 0; padding-left: 16px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @yield('content')
        </main>
    </div>

    {{-- Logout Modal --}}
    <div id="logoutModal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.45); align-items:center; justify-content:center;">
        <div style="background:#fff; border-radius:12px; padding:24px 28px; width:340px; max-width:90%; box-shadow:0 16px 40px rgba(0,0,0,0.15); text-align:center;">
            <div style="font-size:15px; font-weight:700; color:#0f172a; margin-bottom:6px;">Konfirmasi Logout</div>
            <div style="font-size:13px; color:#6b7280; margin-bottom:20px;">Apakah Anda yakin ingin keluar dari sistem?</div>
            <div style="display:flex; gap:10px; justify-content:center;">
                <button onclick="document.getElementById('logoutModal').style.display='none'" style="padding:8px 20px; border-radius:8px; border:1px solid #e5e7eb; background:#fff; color:#374151; font-size:13px; font-weight:600; cursor:pointer;">Batal</button>
                <button onclick="document.getElementById('logoutForm').submit()" style="padding:8px 20px; border-radius:8px; border:none; background:#ef4444; color:#fff; font-size:13px; font-weight:600; cursor:pointer;">Ya, Logout</button>
            </div>
        </div>
    </div>
</body>
</html>
