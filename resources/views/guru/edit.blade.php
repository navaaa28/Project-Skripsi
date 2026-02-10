@extends('layouts.admin-dashboard')

@section('title', 'Edit Guru')

@section('content')
<style>
    .page-title { font-size: 16px; font-weight: 700; margin-bottom: 12px; }
    .card { background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 16px; max-width: 720px; }
    .field { margin-bottom: 12px; }
    .label { display: block; font-size: 12px; color: #6b7280; margin-bottom: 6px; }
    .input, .select { width: 100%; border: 1px solid #e5e7eb; border-radius: 6px; padding: 8px 10px; font-size: 12px; }
    .btn { background: #2563eb; color: #fff; border: none; padding: 8px 12px; border-radius: 6px; font-size: 12px; cursor: pointer; }
    .btn-secondary { background: #e5e7eb; color: #111827; text-decoration: none; margin-left: 6px; }
</style>

<div class="page-title">Edit Guru</div>
<div class="card">
    <form method="POST" action="{{ route('admin.guru.update', $guru) }}">
        @csrf
        @method('PUT')
        <div class="field">
            <label class="label">User (role guru)</label>
            <select name="id_user" class="select" required>
                @foreach ($users as $user)
                    <option value="{{ $user->id_user }}" @selected($guru->id_user == $user->id_user)>{{ $user->username }} ({{ $user->email ?? '-' }})</option>
                @endforeach
            </select>
        </div>
        <div class="field">
            <label class="label">NIP</label>
            <input name="nip" value="{{ $guru->nip }}" class="input">
        </div>
        <div class="field">
            <label class="label">Nama Guru</label>
            <input name="nama_guru" value="{{ $guru->nama_guru }}" class="input" required>
        </div>
        <div class="field">
            <label class="label">Jenis Kelamin (L/P)</label>
            <input name="jenis_kelamin" value="{{ $guru->jenis_kelamin }}" class="input">
        </div>
        <div class="field">
            <label class="label">Mapel Utama</label>
            <input name="mapel_utama" value="{{ $guru->mapel_utama }}" class="input">
        </div>
        <button class="btn" type="submit">Update</button>
        <a href="{{ route('admin.guru.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
