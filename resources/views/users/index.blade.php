@extends('layouts.admin-dashboard')

@section('title', 'Manajemen Users')

@section('content')
<style>
    .users-page { display: flex; flex-direction: column; gap: 14px; }
    .users-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
    .users-title { font-size: 22px; font-weight: 700; color: #0f172a; margin: 0; }
    .users-subtitle { margin: 2px 0 0; font-size: 13px; color: #64748b; }

    .btn-primary {
        display: inline-flex; align-items: center; justify-content: center; gap: 6px;
        background: #2563eb; color: #fff; border: none; border-radius: 10px;
        font-size: 13px; font-weight: 600; padding: 10px 14px; text-decoration: none; white-space: nowrap;
    }
    .btn-primary:hover { background: #1d4ed8; }

    /* ── Role Tabs ── */
    .role-tabs {
        display: flex; gap: 6px; flex-wrap: wrap;
        background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
        padding: 8px 10px;
    }
    .role-tab {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 14px; border-radius: 8px;
        font-size: 13px; font-weight: 600; color: #64748b;
        text-decoration: none; border: 1px solid transparent;
        transition: all .2s ease;
    }
    .role-tab:hover { background: #f1f5f9; color: #334155; }
    .role-tab.active {
        color: #fff; border-color: transparent;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    .role-tab.active.tab-all    { background: #2563eb; }
    .role-tab.active.tab-admin  { background: #0e7490; }
    .role-tab.active.tab-guru   { background: #16a34a; }
    .role-tab.active.tab-siswa  { background: #d97706; }
    .role-tab .tab-count {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 22px; height: 20px; padding: 0 6px;
        border-radius: 10px; font-size: 11px; font-weight: 700;
        background: rgba(0,0,0,0.07);
    }
    .role-tab.active .tab-count { background: rgba(255,255,255,0.25); color: #fff; }

    /* ── Kelas Sub-Tabs (for siswa) ── */
    .kelas-subtabs {
        display: flex; gap: 5px; flex-wrap: wrap;
        background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px;
        padding: 6px 10px;
    }
    .kelas-subtab {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 6px 12px; border-radius: 7px;
        font-size: 12px; font-weight: 600; color: #92400e;
        text-decoration: none; border: 1px solid transparent;
        transition: all .2s ease;
    }
    .kelas-subtab:hover { background: #fef3c7; }
    .kelas-subtab.active {
        background: #d97706; color: #fff; border-color: #d97706;
        box-shadow: 0 2px 6px rgba(217,119,6,0.25);
    }
    .kelas-subtab .stab-count {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 18px; height: 16px; padding: 0 4px;
        border-radius: 8px; font-size: 10px; font-weight: 700;
        background: rgba(0,0,0,0.08);
    }
    .kelas-subtab.active .stab-count { background: rgba(255,255,255,0.3); color: #fff; }

    /* ── Search ── */
    .search-panel {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
        padding: 10px 14px; display: flex; align-items: center; gap: 10px;
    }
    .search-icon { color: #94a3b8; flex-shrink: 0; }
    .search-input {
        flex: 1; border: none; outline: none; font-size: 13px; color: #1e293b;
        background: transparent; padding: 6px 0;
    }
    .search-input::placeholder { color: #94a3b8; }
    .search-result-count { font-size: 11px; color: #94a3b8; white-space: nowrap; }

    /* ── Card & Table ── */
    .users-card {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 14px;
        overflow: hidden; box-shadow: 0 4px 16px rgba(15,23,42,0.04);
    }

    .group-header {
        display: flex; align-items: center; gap: 10px;
        padding: 12px 16px;
        border-bottom: 1px solid #e2e8f0;
    }
    .group-header.role-admin  { background: linear-gradient(135deg, #ecfeff 0%, #f0f9ff 100%); }
    .group-header.role-guru   { background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%); }
    .group-header.role-siswa,
    .group-header.kelas-header { background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); }
    .group-header .group-badge {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 5px 12px; border-radius: 8px;
        color: #fff; font-size: 13px; font-weight: 700;
    }
    .group-header.role-admin .group-badge { background: #0e7490; }
    .group-header.role-guru  .group-badge { background: #16a34a; }
    .group-header.role-siswa .group-badge { background: #d97706; }
    .group-header.kelas-header .group-badge { background: #2563eb; }
    .group-header .group-count { font-size: 12px; color: #64748b; }

    .users-table { width: 100%; min-width: 700px; border-collapse: collapse; font-size: 13px; background: #fff; }
    .users-table thead th {
        text-align: left; padding: 12px 14px; font-size: 11px; font-weight: 700; color: #64748b;
        text-transform: uppercase; letter-spacing: .02em; border-bottom: 1px solid #e5e7eb; background: #f8fafc;
    }
    .users-table tbody td {
        padding: 11px 14px; border-bottom: 1px solid #f1f5f9; color: #1e293b; vertical-align: middle;
    }
    .users-table tbody tr { transition: background .15s ease; }
    .users-table tbody tr:hover { background: #f8fafc; }
    .users-table tbody tr:last-child td { border-bottom: none; }

    .muted { color: #64748b; }
    .role-chip {
        display: inline-block; padding: 4px 9px; border-radius: 999px;
        font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .02em;
    }
    .role-chip.role-admin { background: #ecfeff; color: #155e75; }
    .role-chip.role-guru  { background: #f0fdf4; color: #166534; }
    .role-chip.role-siswa { background: #fef3c7; color: #92400e; }

    .kelas-chip {
        display: inline-block; padding: 3px 9px; border-radius: 999px;
        background: #eff6ff; color: #1d4ed8; font-size: 11px; font-weight: 700;
    }

    .actions { display: flex; align-items: center; gap: 10px; }
    .action-link { font-size: 12px; font-weight: 600; text-decoration: none; }
    .action-link.edit { color: #2563eb; }
    .action-link.detail { color: #ef4444; }

    .table-empty { text-align: center; padding: 40px 20px; color: #94a3b8; }
    .table-empty .empty-icon { font-size: 36px; margin-bottom: 8px; }
    .table-empty .empty-text { font-size: 13px; }

    .pager {
        padding: 12px 16px; display: flex; align-items: center; justify-content: space-between;
        gap: 12px; flex-wrap: wrap; border-top: 1px solid #f1f5f9;
    }
    .pager-info { font-size: 12px; color: #64748b; }
    .pager-links { display: flex; align-items: center; gap: 4px; }
    .pager-btn {
        border: 1px solid #e2e8f0; background: #fff; color: #374151;
        border-radius: 8px; padding: 6px 11px; font-size: 12px;
        text-decoration: none; line-height: 1; transition: all .15s ease;
    }
    .pager-btn:hover { background: #f1f5f9; }
    .pager-btn.active { background: #2563eb; border-color: #2563eb; color: #fff; }
    .pager-btn.disabled { opacity: .4; pointer-events: none; }

    .fade-in { animation: fadeIn .25s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }

    @media (max-width: 900px) {
        .users-toolbar { align-items: flex-start; flex-direction: column; }
        .users-title { font-size: 20px; }
    }
</style>

@php
    $countAdmin = \App\Models\User::where('role', 'admin')->count();
    $countGuru  = \App\Models\User::where('role', 'guru')->count();
    $countSiswa = \App\Models\User::where('role', 'siswa')->count();
    $countAll   = $countAdmin + $countGuru + $countSiswa;
    $selected   = $selectedRole ?? null;
@endphp

<div class="users-page fade-in">
    {{-- Toolbar --}}
    <div class="users-toolbar">
        <div>
            <h1 class="users-title">Manajemen Users</h1>
            <p class="users-subtitle">Kelola akun admin, guru, dan siswa.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn-primary">+ Tambah User</a>
    </div>

    {{-- Role Tabs --}}
    <div class="role-tabs">
        <a class="role-tab tab-all {{ !$selected ? 'active' : '' }}"
           href="{{ route('admin.users.index') }}">
            Semua
            <span class="tab-count">{{ $countAll }}</span>
        </a>
        <a class="role-tab tab-admin {{ $selected === 'admin' ? 'active' : '' }}"
           href="{{ route('admin.users.index', ['role' => 'admin']) }}">
            🛡️ Admin
            <span class="tab-count">{{ $countAdmin }}</span>
        </a>
        <a class="role-tab tab-guru {{ $selected === 'guru' ? 'active' : '' }}"
           href="{{ route('admin.users.index', ['role' => 'guru']) }}">
            👨‍🏫 Guru
            <span class="tab-count">{{ $countGuru }}</span>
        </a>
        <a class="role-tab tab-siswa {{ $selected === 'siswa' ? 'active' : '' }}"
           href="{{ route('admin.users.index', ['role' => 'siswa']) }}">
            🎓 Siswa
            <span class="tab-count">{{ $countSiswa }}</span>
        </a>
        <a class="role-tab tab-all {{ $selected === 'all' ? 'active' : '' }}"
           href="{{ route('admin.users.index', ['role' => 'all']) }}">
            📂 Tampilan Grup
        </a>
    </div>

    {{-- Kelas Sub-Tabs (only when role=siswa) --}}
    @if ($selected === 'siswa' && $kelasOptions->isNotEmpty())
        <div class="kelas-subtabs">
            <a class="kelas-subtab {{ !($selectedKelas ?? null) ? 'active' : '' }}"
               href="{{ route('admin.users.index', ['role' => 'siswa']) }}">
                Semua Kelas
            </a>
            @foreach ($kelasOptions as $k)
                @php $kCount = \App\Models\User::where('role', 'siswa')->whereHas('siswa', fn($s) => $s->where('id_kelas', $k->id_kelas))->count(); @endphp
                @if ($kCount > 0)
                    <a class="kelas-subtab {{ ($selectedKelas ?? null) == $k->id_kelas ? 'active' : '' }}"
                       href="{{ route('admin.users.index', ['role' => 'siswa', 'kelas' => $k->id_kelas]) }}">
                        {{ $k->nama_kelas }}
                        <span class="stab-count">{{ $kCount }}</span>
                    </a>
                @endif
            @endforeach
        </div>
    @endif

    {{-- Search --}}
    <div class="search-panel">
        <svg class="search-icon" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
        </svg>
        <form method="GET" action="{{ route('admin.users.index') }}" style="display:contents;">
            @if($selected)
                <input type="hidden" name="role" value="{{ $selected }}">
            @endif
            @if($selectedKelas ?? null)
                <input type="hidden" name="kelas" value="{{ $selectedKelas }}">
            @endif
            <input type="text" name="q" class="search-input" id="liveSearch"
                   placeholder="Cari username atau email..."
                   value="{{ request('q') }}" autocomplete="off">
        </form>
        <span class="search-result-count" id="searchCount"></span>
    </div>

    {{-- ===== SISWA BY KELAS VIEW ===== --}}
    @if ($siswaByKelas && !$groupedUsers)
        @forelse ($siswaByKelas->sortKeys() as $namaKelas => $kelasUsers)
            <div class="users-card fade-in data-group" data-group="{{ $namaKelas }}">
                <div class="group-header kelas-header">
                    <span class="group-badge">📚 {{ $namaKelas }}</span>
                    <span class="group-count">{{ $kelasUsers->count() }} siswa</span>
                </div>
                <div style="overflow-x: auto;">
                    <table class="users-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Username</th>
                                <th>Nama Siswa</th>
                                <th>Email</th>
                                <th style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($kelasUsers->sortBy('username')->values() as $user)
                                <tr class="user-row" data-search="{{ strtolower($user->username . ' ' . $user->email . ' ' . ($user->siswa?->nama_siswa ?? '')) }}">
                                    <td class="muted">{{ $loop->iteration }}</td>
                                    <td style="font-weight: 600;">{{ $user->username }}</td>
                                    <td>{{ $user->siswa?->nama_siswa ?? '-' }}</td>
                                    <td>{{ $user->email ?? '-' }}</td>
                                    <td>
                                        <div class="actions">
                                            <a class="action-link edit" href="{{ route('admin.users.edit', $user) }}">Edit</a>
                                            <a class="action-link detail" href="{{ route('admin.users.show', $user) }}">Detail</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="users-card">
                <div class="table-empty">
                    <div class="empty-icon">👤</div>
                    <div class="empty-text">Tidak ada user siswa ditemukan.</div>
                </div>
            </div>
        @endforelse

    {{-- ===== GROUPED VIEW (ALL ROLES) ===== --}}
    @elseif ($groupedUsers)
        @php
            $roleOrder = ['admin', 'guru'];
            $roleLabels = ['admin' => 'Admin', 'guru' => 'Guru', 'siswa' => 'Siswa'];
            $roleIcons  = ['admin' => '🛡️', 'guru' => '👨‍🏫', 'siswa' => '🎓'];
        @endphp

        {{-- Admin & Guru sections --}}
        @foreach ($roleOrder as $role)
            @if ($groupedUsers->has($role))
                @php $roleUsers = $groupedUsers[$role]; @endphp
                <div class="users-card fade-in data-group" data-group="{{ $role }}">
                    <div class="group-header role-{{ $role }}">
                        <span class="group-badge">{{ $roleIcons[$role] }} {{ $roleLabels[$role] }}</span>
                        <span class="group-count">{{ $roleUsers->count() }} user</span>
                    </div>
                    <div style="overflow-x: auto;">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th style="width: 150px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($roleUsers->sortBy('username')->values() as $user)
                                    <tr class="user-row" data-search="{{ strtolower($user->username . ' ' . $user->email) }}">
                                        <td class="muted">{{ $loop->iteration }}</td>
                                        <td style="font-weight: 600;">{{ $user->username }}</td>
                                        <td>{{ $user->email ?? '-' }}</td>
                                        <td>
                                            <div class="actions">
                                                <a class="action-link edit" href="{{ route('admin.users.edit', $user) }}">Edit</a>
                                                <a class="action-link detail" href="{{ route('admin.users.show', $user) }}">Detail</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @endforeach

        {{-- Siswa section: sub-grouped by kelas --}}
        @if ($siswaByKelas && $siswaByKelas->isNotEmpty())
            <div style="margin-top: 4px;">
                <div style="font-size: 14px; font-weight: 700; color: #92400e; margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                    🎓 Siswa <span style="font-weight: 400; color: #a16207; font-size: 12px;">(dikelompokkan per kelas)</span>
                </div>
                @foreach ($siswaByKelas->sortKeys() as $namaKelas => $kelasUsers)
                    <div class="users-card fade-in data-group" data-group="{{ $namaKelas }}" style="margin-bottom: 10px;">
                        <div class="group-header kelas-header">
                            <span class="group-badge">📚 {{ $namaKelas }}</span>
                            <span class="group-count">{{ $kelasUsers->count() }} siswa</span>
                        </div>
                        <div style="overflow-x: auto;">
                            <table class="users-table">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th>Username</th>
                                        <th>Nama Siswa</th>
                                        <th>Email</th>
                                        <th style="width: 150px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($kelasUsers->sortBy('username')->values() as $user)
                                        <tr class="user-row" data-search="{{ strtolower($user->username . ' ' . $user->email . ' ' . ($user->siswa?->nama_siswa ?? '')) }}">
                                            <td class="muted">{{ $loop->iteration }}</td>
                                            <td style="font-weight: 600;">{{ $user->username }}</td>
                                            <td>{{ $user->siswa?->nama_siswa ?? '-' }}</td>
                                            <td>{{ $user->email ?? '-' }}</td>
                                            <td>
                                                <div class="actions">
                                                    <a class="action-link edit" href="{{ route('admin.users.edit', $user) }}">Edit</a>
                                                    <a class="action-link detail" href="{{ route('admin.users.show', $user) }}">Detail</a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        @if ($groupedUsers->isEmpty())
            <div class="users-card">
                <div class="table-empty">
                    <div class="empty-icon">👤</div>
                    <div class="empty-text">Tidak ada user ditemukan.</div>
                </div>
            </div>
        @endif

    {{-- ===== FLAT PAGINATED VIEW (Semua / Admin / Guru) ===== --}}
    @elseif ($users)
        <div class="users-card fade-in">
            <div style="overflow-x: auto;">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th style="width: 120px;">Role</th>
                            <th style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr class="user-row" data-search="{{ strtolower($user->username . ' ' . $user->email) }}">
                                <td class="muted">{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                                <td style="font-weight: 600;">{{ $user->username }}</td>
                                <td>{{ $user->email ?? '-' }}</td>
                                <td>
                                    <span class="role-chip role-{{ $user->role }}">{{ strtoupper($user->role) }}</span>
                                </td>
                                <td>
                                    <div class="actions">
                                        <a class="action-link edit" href="{{ route('admin.users.edit', $user) }}">Edit</a>
                                        <a class="action-link detail" href="{{ route('admin.users.show', $user) }}">Detail</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="table-empty">
                                        <div class="empty-icon">👤</div>
                                        <div class="empty-text">Belum ada data user.</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="pager">
                    <div class="pager-info">Menampilkan {{ $users->firstItem() }} – {{ $users->lastItem() }} dari {{ $users->total() }} data</div>
                    <div class="pager-links">
                        <a class="pager-btn {{ $users->onFirstPage() ? 'disabled' : '' }}" href="{{ $users->previousPageUrl() ?? '#' }}">← Prev</a>
                        @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                            <a class="pager-btn {{ $page === $users->currentPage() ? 'active' : '' }}" href="{{ $url }}">{{ $page }}</a>
                        @endforeach
                        <a class="pager-btn {{ $users->hasMorePages() ? '' : 'disabled' }}" href="{{ $users->nextPageUrl() ?? '#' }}">Next →</a>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('liveSearch');
    const searchCount = document.getElementById('searchCount');
    const rows = document.querySelectorAll('.user-row');
    const groups = document.querySelectorAll('.data-group');

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const term = this.value.toLowerCase().trim();
            let visible = 0;

            rows.forEach(function (row) {
                const data = row.getAttribute('data-search') || '';
                const match = !term || data.includes(term);
                row.style.display = match ? '' : 'none';
                if (match) visible++;
            });

            groups.forEach(function (group) {
                const groupRows = group.querySelectorAll('.user-row');
                const anyVisible = Array.from(groupRows).some(r => r.style.display !== 'none');
                group.style.display = anyVisible ? '' : 'none';
            });

            if (term) {
                searchCount.textContent = visible + ' ditemukan';
            } else {
                searchCount.textContent = '';
            }
        });

        if (searchInput.value) {
            searchInput.dispatchEvent(new Event('input'));
        }
    }
});
</script>
@endsection
