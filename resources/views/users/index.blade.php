@extends('layouts.admin-dashboard')

@section('title', 'Manajemen Users')

@section('content')
<style>
    .users-page {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }
    .users-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }
    .users-title {
        font-size: 22px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }
    .users-subtitle {
        margin: 2px 0 0;
        font-size: 13px;
        color: #64748b;
    }
    .btn-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        background: #2563eb;
        color: #fff;
        border: none;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        padding: 10px 14px;
        text-decoration: none;
        white-space: nowrap;
    }
    .btn-primary:hover { background: #1d4ed8; }

    .users-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 14px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
    }

    .table-shell {
        width: 100%;
        overflow-x: auto;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
    }
    .users-table {
        width: 100%;
        min-width: 760px;
        border-collapse: collapse;
        font-size: 13px;
        background: #fff;
    }
    .users-table thead th {
        text-align: left;
        padding: 12px 10px;
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        border-bottom: 1px solid #e5e7eb;
        background: #f8fafc;
        letter-spacing: .02em;
    }
    .users-table tbody td {
        padding: 12px 10px;
        border-bottom: 1px solid #edf2f7;
        color: #1f2937;
        vertical-align: middle;
    }
    .users-table tbody tr:last-child td { border-bottom: none; }

    .muted { color: #64748b; }
    .role-chip {
        display: inline-block;
        padding: 4px 9px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .02em;
        background: #eff6ff;
        color: #1d4ed8;
    }
    .role-chip.role-admin { background: #ecfeff; color: #155e75; }
    .role-chip.role-guru { background: #f0fdf4; color: #166534; }
    .role-chip.role-siswa { background: #fef3c7; color: #92400e; }

    .actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .action-link {
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
    }
    .action-link.edit { color: #2563eb; }
    .action-link.detail { color: #ef4444; }

    .table-empty {
        text-align: center;
        padding: 20px 10px;
        color: #64748b;
        font-size: 13px;
    }

    .pager {
        margin-top: 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }
    .pager-info {
        font-size: 12px;
        color: #64748b;
    }
    .pager-links {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .pager-btn {
        border: 1px solid #d1d5db;
        background: #fff;
        color: #374151;
        border-radius: 8px;
        padding: 6px 10px;
        font-size: 12px;
        text-decoration: none;
        line-height: 1;
    }
    .pager-btn.active {
        background: #2563eb;
        border-color: #2563eb;
        color: #fff;
    }
    .pager-btn.disabled {
        opacity: .5;
        pointer-events: none;
    }

    @media (max-width: 900px) {
        .users-toolbar { align-items: flex-start; flex-direction: column; }
        .users-title { font-size: 20px; }
        .users-card { padding: 10px; }
    }
</style>

<div class="users-page">
    <div class="users-toolbar">
        <div>
            <h1 class="users-title">Manajemen Users</h1>
            <p class="users-subtitle">Kelola akun admin, guru, dan siswa.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn-primary">+ Tambah User</a>
    </div>

    <div class="users-card">
        <div class="table-shell">
            <table class="users-table">
                <thead>
                    <tr>
                        <th style="width: 70px;">No</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th style="width: 150px;">Role</th>
                        <th style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td class="muted">{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->email ?? '-' }}</td>
                            <td>
                                <span class="role-chip role-{{ $user->role }}">{{ $user->role }}</span>
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
                            <td colspan="5" class="table-empty">Belum ada data user.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="pager">
                <div class="pager-info">
                    Menampilkan {{ $users->firstItem() }} - {{ $users->lastItem() }} dari {{ $users->total() }} data
                </div>
                <div class="pager-links">
                    <a class="pager-btn {{ $users->onFirstPage() ? 'disabled' : '' }}" href="{{ $users->previousPageUrl() ?? '#' }}">Prev</a>
                    @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                        <a class="pager-btn {{ $page === $users->currentPage() ? 'active' : '' }}" href="{{ $url }}">{{ $page }}</a>
                    @endforeach
                    <a class="pager-btn {{ $users->hasMorePages() ? '' : 'disabled' }}" href="{{ $users->nextPageUrl() ?? '#' }}">Next</a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
