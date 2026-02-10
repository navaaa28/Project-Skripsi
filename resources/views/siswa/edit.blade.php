@extends('layouts.admin-dashboard')

@section('title', 'Edit Siswa')

@section('content')
<style>
    .page-title { font-size: 16px; font-weight: 700; margin-bottom: 12px; }
    .card { background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 16px; max-width: 760px; }
    .field { margin-bottom: 12px; }
    .label { display: block; font-size: 12px; color: #6b7280; margin-bottom: 6px; }
    .input, .select { width: 100%; border: 1px solid #e5e7eb; border-radius: 6px; padding: 8px 10px; font-size: 12px; }
    .btn { background: #2563eb; color: #fff; border: none; padding: 8px 12px; border-radius: 6px; font-size: 12px; cursor: pointer; }
    .btn-secondary { background: #e5e7eb; color: #111827; text-decoration: none; margin-left: 6px; }
</style>

<div class="page-title">Edit Siswa</div>
<div class="card">
    <form method="POST" action="{{ route('admin.siswa.update', $siswa) }}">
        @csrf
        @method('PUT')
        <div class="field">
            <label class="label">User (role siswa)</label>
            <select name="id_user" class="select" required>
                @foreach ($users as $user)
                    <option value="{{ $user->id_user }}" @selected($siswa->id_user == $user->id_user)>{{ $user->username }} ({{ $user->email ?? '-' }})</option>
                @endforeach
            </select>
        </div>
        <div class="field">
            <label class="label">Nama Siswa</label>
            <input name="nama_siswa" value="{{ $siswa->nama_siswa }}" class="input" required>
        </div>
        <div class="field">
            <label class="label">NIPD</label>
            <input name="nipd" value="{{ $siswa->nipd }}" class="input">
        </div>
        <div class="field">
            <label class="label">NISN</label>
            <input name="nisn" value="{{ $siswa->nisn }}" class="input">
        </div>
        <div class="field">
            <label class="label">Jenis Kelamin (L/P)</label>
            <input name="jenis_kelamin" value="{{ $siswa->jenis_kelamin }}" class="input">
        </div>
        <div class="field">
            <label class="label">Tempat Lahir</label>
            <input name="tempat_lahir" value="{{ $siswa->tempat_lahir }}" class="input">
        </div>
        <div class="field">
            <label class="label">Tanggal Lahir</label>
            <input type="date" name="tgl_lahir" value="{{ $siswa->tgl_lahir?->format('Y-m-d') }}" class="input">
        </div>
        <div class="field">
            <label class="label">Rombel Saat Ini</label>
            <input name="rombel_saat_ini" value="{{ $siswa->rombel_saat_ini }}" class="input">
        </div>
        <div class="field">
            <label class="label">Kelas (opsional)</label>
            <input name="id_kelas" type="number" value="{{ $siswa->id_kelas }}" class="input" placeholder="ID kelas">
        </div>
        <button class="btn" type="submit">Update</button>
        <a href="{{ route('admin.siswa.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
