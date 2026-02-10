@extends('layouts.admin-dashboard')

@section('title', 'Detail Siswa')

@section('content')
<style>
    .page-title { font-size: 16px; font-weight: 700; margin-bottom: 12px; }
    .card { background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 16px; max-width: 760px; }
    .muted { color: #6b7280; font-size: 12px; }
    .btn { display: inline-block; background: #2563eb; color: #fff; border: none; padding: 8px 12px; border-radius: 6px; font-size: 12px; text-decoration: none; }
    .btn-secondary { background: #e5e7eb; color: #111827; margin-left: 6px; }
    .btn-danger { background: #ef4444; color: #fff; margin-left: 6px; }
</style>

<div class="page-title">Detail Siswa</div>
<div class="card">
    <div><span class="muted">User</span>: {{ $siswa->user?->username }} ({{ $siswa->user?->email ?? '-' }})</div>
    <div><span class="muted">NIPD</span>: {{ $siswa->nipd ?? '-' }}</div>
    <div><span class="muted">NISN</span>: {{ $siswa->nisn ?? '-' }}</div>
    <div><span class="muted">Nama Siswa</span>: {{ $siswa->nama_siswa }}</div>
    <div><span class="muted">Jenis Kelamin</span>: {{ $siswa->jenis_kelamin ?? '-' }}</div>
    <div><span class="muted">Tempat Lahir</span>: {{ $siswa->tempat_lahir ?? '-' }}</div>
    <div><span class="muted">Tanggal Lahir</span>: {{ $siswa->tgl_lahir?->format('d-m-Y') }}</div>
    <div><span class="muted">Rombel Saat Ini</span>: {{ $siswa->rombel_saat_ini ?? '-' }}</div>
    <div><span class="muted">Kelas</span>: {{ $siswa->kelas?->nama_kelas ?? '-' }}</div>

    <div style="margin-top: 12px;">
        <a class="btn" href="{{ route('admin.siswa.edit', $siswa) }}">Edit Siswa</a>
        <a class="btn btn-secondary" href="{{ route('admin.siswa.index') }}">Kembali</a>
        <form method="POST" action="{{ route('admin.siswa.destroy', $siswa) }}" style="display:inline;">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger" type="submit">Hapus</button>
        </form>
    </div>
</div>
@endsection
