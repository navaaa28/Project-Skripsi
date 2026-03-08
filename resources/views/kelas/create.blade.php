@extends('layouts.admin-dashboard')

@section('title', 'Tambah Kelas')

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

<div class="page-title">Tambah Kelas</div>
<div class="card">
    <form method="POST" action="{{ route('admin.kelas.store') }}">
        @csrf
        <div class="field">
            <label class="label">Nama Kelas</label>
            <input name="nama_kelas" value="{{ old('nama_kelas') }}" class="input" required>
        </div>
        <div class="field">
            <label class="label">Wali Kelas (Guru)</label>
            <select name="id_guru" class="select">
                <option value="">-</option>
                @foreach ($gurus as $guru)
                    <option value="{{ $guru->id_user }}" @selected((string) old('id_guru') === (string) $guru->id_user)>{{ $guru->nama_guru }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn" type="submit">Simpan</button>
        <a href="{{ route('admin.kelas.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
