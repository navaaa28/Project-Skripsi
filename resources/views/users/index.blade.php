@extends('layouts.admin-dashboard')

@section('title', 'Manajemen Users')

@section('content')
<style>
    .page-title { font-size: 16px; font-weight: 700; margin-bottom: 12px; }
    .table-wrap { background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 14px; }
    .toolbar { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; }
    .btn-primary { background: #2563eb; color: #fff; border: none; padding: 8px 12px; border-radius: 6px; font-size: 12px; cursor: pointer; text-decoration: none; }
    table { width: 100%; border-collapse: collapse; font-size: 12px; }
    th, td { padding: 10px 8px; border-bottom: 1px solid #e5e7eb; text-align: left; }
    th { color: #6b7280; font-weight: 600; text-transform: uppercase; font-size: 11px; }
    .aksi a { text-decoration: none; margin-right: 8px; font-size: 12px; }
    .aksi .edit { color: #2563eb; }
    .aksi .del { color: #ef4444; }
    .pagination { margin-top: 12px; display: flex; justify-content: flex-end; gap: 4px; }
    .pagination a, .pagination span { border: 1px solid #e5e7eb; padding: 6px 8px; border-radius: 6px; font-size: 12px; color: #374151; text-decoration: none; }
    .pagination .active span { background: #2563eb; color: #fff; border-color: #2563eb; }
    .muted { color: #6b7280; }
</style>

<div class="toolbar">
    <div class="page-title">Manajemen Users</div>
    <a href="{{ route('admin.users.create') }}" class="btn-primary">+ Tambah User</a>
</div>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th style="width: 60px;">No</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th style="width: 120px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                <tr>
                    <td class="muted">{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                    <td>{{ $user->username }}</td>
                    <td>{{ $user->email ?? '-' }}</td>
                    <td>{{ $user->role }}</td>
                    <td class="aksi">
                        <a class="edit" href="{{ route('admin.users.edit', $user) }}">Edit</a>
                        <a class="del" href="{{ route('admin.users.show', $user) }}">Detail</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="muted">Belum ada data user.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pagination">
        {{ $users->links() }}
    </div>
</div>
@endsection
