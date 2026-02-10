@extends('layouts.admin-dashboard')

@section('title', 'Detail Kelas')

@section('content')
<style>
    .page-title { font-size: 16px; font-weight: 700; margin-bottom: 12px; }
    .card { background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 16px; margin-bottom: 14px; }
    .muted { color: #6b7280; font-size: 12px; }
    table { width: 100%; border-collapse: collapse; font-size: 12px; }
    th, td { padding: 10px 8px; border-bottom: 1px solid #e5e7eb; text-align: left; }
    th { color: #6b7280; font-weight: 600; text-transform: uppercase; font-size: 11px; }
    .btn { display: inline-block; background: #2563eb; color: #fff; border: none; padding: 8px 12px; border-radius: 6px; font-size: 12px; text-decoration: none; }
    .btn-secondary { background: #e5e7eb; color: #111827; margin-left: 6px; }
</style>

<div class="page-title">Detail Kelas</div>

<div class="card">
    <div><span class="muted">Nama Kelas</span>: {{ $kelas->nama_kelas }}</div>
    <div><span class="muted">Wali Kelas</span>: {{ $kelas->waliGuru?->nama_guru ?? '-' }}</div>
    <div style="margin-top: 10px;">
        <a class="btn" href="{{ route('admin.kelas.edit', $kelas) }}">Edit Kelas</a>
        <a class="btn btn-secondary" href="{{ route('admin.kelas.index') }}">Kembali</a>
    </div>
</div>

<div class="card">
    <div class="page-title" style="font-size: 13px; margin-bottom: 8px;">Daftar Siswa (Rombel Ini)</div>
    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>NIPD</th>
                <th>NISN</th>
                <th>JK</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($kelas->siswas as $siswa)
                <tr>
                    <td>{{ $siswa->nama_siswa }}</td>
                    <td>{{ $siswa->nipd }}</td>
                    <td>{{ $siswa->nisn }}</td>
                    <td>{{ $siswa->jenis_kelamin }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="muted">Belum ada siswa di kelas ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
