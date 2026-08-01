@extends('layouts.admin-dashboard')

@section('title', 'Tambah Siswa')

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

<div class="page-title">Tambah Siswa</div>
<div class="card">
    <form method="POST" action="{{ route('admin.siswa.store') }}">
        @csrf

        <div class="field">
            <label class="label">Nama Siswa</label>
            <input name="nama_siswa" class="input" required>
        </div>
        <div class="field">
            <label class="label">NIPD</label>
            <input name="nipd" class="input">
        </div>
        <div class="field">
            <label class="label">NISN</label>
            <input name="nisn" class="input">
        </div>
        <div class="field">
            <label class="label">Jenis Kelamin (L/P)</label>
            <div style="display: flex; gap: 18px; align-items: center;">
                <label style="display: flex; align-items: center; gap: 6px; font-size: 12px; color: #374151;">
                    <input type="radio" name="jenis_kelamin" value="L" @checked(old('jenis_kelamin') === 'L')>
                    L
                </label>
                <label style="display: flex; align-items: center; gap: 6px; font-size: 12px; color: #374151;">
                    <input type="radio" name="jenis_kelamin" value="P" @checked(old('jenis_kelamin') === 'P')>
                    P
                </label>
            </div>
        </div>
        <div class="field">
            <label class="label">Tempat Lahir</label>
            <input name="tempat_lahir" class="input">
        </div>
        <div class="field">
            <label class="label">Tanggal Lahir</label>
            <input type="date" name="tgl_lahir" class="input">
        </div>
        <div class="field">
            <label class="label">Rombel Saat Ini</label>
            <input name="rombel_saat_ini" class="input">
        </div>
        <div class="field">
            <label class="label">Kelas (opsional)</label>
            <select name="id_kelas" class="select">
                <option value="">- Pilih Kelas -</option>
                @foreach ($kelasOptions as $kelas)
                    <option value="{{ $kelas->id_kelas }}" @selected(old('id_kelas') == $kelas->id_kelas)>{{ $kelas->nama_kelas }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn" type="submit">Simpan</button>
        <a href="{{ route('admin.siswa.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
