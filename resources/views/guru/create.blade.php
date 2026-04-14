@extends('layouts.admin-dashboard')

@section('title', 'Tambah Guru')

@section('content')
<style>
    .page-title { font-size: 16px; font-weight: 700; margin-bottom: 12px; }
    .card { background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 16px; max-width: 720px; }
    .field { margin-bottom: 12px; }
    .label { display: block; font-size: 12px; color: #6b7280; margin-bottom: 6px; }
    .input, .select { width: 100%; border: 1px solid #e5e7eb; border-radius: 6px; padding: 8px 10px; font-size: 12px; }
    .btn { background: #2563eb; color: #fff; border: none; padding: 8px 12px; border-radius: 6px; font-size: 12px; cursor: pointer; }
    .btn-secondary { background: #e5e7eb; color: #111827; text-decoration: none; margin-left: 6px; }
    .radio-group { display: flex; gap: 18px; align-items: center; }
    .radio-item { display: flex; align-items: center; gap: 6px; font-size: 12px; color: #374151; }
</style>

<div class="page-title">Tambah Guru</div>
<div class="card">
    <form method="POST" action="{{ route('admin.guru.store') }}">
        @csrf
        <div class="field">
            <label class="label">User (role guru)</label>
            <select name="id_user" class="select" required>
                <option value="">-</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id_user }}" @selected(old('id_user') == $user->id_user)>{{ $user->username }} ({{ $user->email ?? '-' }})</option>
                @endforeach
            </select>
        </div>
        <div class="field">
            <label class="label">NIP</label>
            <input name="nip" value="{{ old('nip') }}" class="input">
        </div>
        <div class="field">
            <label class="label">Nama Guru</label>
            <input name="nama_guru" value="{{ old('nama_guru') }}" class="input" required>
        </div>
        <div class="field">
            <label class="label">Jenis Kelamin (L/P)</label>
            <div class="radio-group">
                <label class="radio-item">
                    <input type="radio" name="jenis_kelamin" value="L" @checked(old('jenis_kelamin') === 'L')>
                    L
                </label>
                <label class="radio-item">
                    <input type="radio" name="jenis_kelamin" value="P" @checked(old('jenis_kelamin') === 'P')>
                    P
                </label>
            </div>
        </div>
        <div class="field">
            <label class="label">Mapel Utama</label>
            <select name="mapel_utama" class="select">
                <option value="">- Pilih Mapel -</option>
                @foreach ($mapelOptions as $mapel)
                    <option value="{{ $mapel->nama_mapel }}" @selected(old('mapel_utama') === $mapel->nama_mapel)>{{ $mapel->nama_mapel }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn" type="submit">Simpan</button>
        <a href="{{ route('admin.guru.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
