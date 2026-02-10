@extends('layouts.admin-dashboard')

@section('title', 'Edit User')

@section('content')
<style>
    .page-title { font-size: 16px; font-weight: 700; margin-bottom: 12px; }
    .card { background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 16px; max-width: 600px; }
    .field { margin-bottom: 12px; }
    .label { display: block; font-size: 12px; color: #6b7280; margin-bottom: 6px; }
    .input, .select { width: 100%; border: 1px solid #e5e7eb; border-radius: 6px; padding: 8px 10px; font-size: 12px; }
    .btn { background: #2563eb; color: #fff; border: none; padding: 8px 12px; border-radius: 6px; font-size: 12px; cursor: pointer; }
    .btn-secondary { background: #e5e7eb; color: #111827; text-decoration: none; margin-left: 6px; }
</style>

<div class="page-title">Edit User</div>
<div class="card">
    <form method="POST" action="{{ route('admin.users.update', $user) }}">
        @csrf
        @method('PUT')
        <div class="field">
            <label class="label">Username</label>
            <input name="username" value="{{ $user->username }}" class="input" required>
        </div>
        <div class="field">
            <label class="label">Email (opsional)</label>
            <input type="email" name="email" value="{{ $user->email }}" class="input">
        </div>
        <div class="field">
            <label class="label">Password (kosongkan jika tidak diubah)</label>
            <input type="password" name="password" class="input">
        </div>
        <div class="field">
            <label class="label">Role</label>
            <select name="role" class="select" required>
                <option value="admin" @selected($user->role === 'admin')>admin</option>
                <option value="guru" @selected($user->role === 'guru')>guru</option>
                <option value="siswa" @selected($user->role === 'siswa')>siswa</option>
            </select>
        </div>
        <button class="btn" type="submit">Update</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
