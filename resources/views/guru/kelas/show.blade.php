@extends('layouts.guru-dashboard')

@section('title', 'Detail Kelas')

@section('content')
<style>
    .page-title { font-size: 16px; font-weight: 700; margin-bottom: 12px; }
    .card { background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 16px; margin-bottom: 12px; }
    table { width: 100%; border-collapse: collapse; font-size: 12px; }
    th, td { padding: 10px 8px; border-bottom: 1px solid #e5e7eb; text-align: left; }
    th { color: #6b7280; font-weight: 600; text-transform: uppercase; font-size: 11px; }
    .muted { color: #6b7280; font-size: 12px; }
</style>

<div class="page-title">Detail Kelas</div>

<div class="card">
    <div><span class="muted">Nama Kelas</span>: {{ $kelas->nama_kelas }}</div>
    <div><span class="muted">Jumlah Siswa</span>: {{ $kelas->siswas->count() }}</div>
</div>

<div class="card">
    <div class="page-title" style="font-size: 13px; margin-bottom: 8px;">Daftar Siswa</div>
    <table>
        <thead>
            <tr>
                <th style="width: 60px;">No</th>
                <th>NIPD</th>
                <th>Nama Siswa</th>
                <th>JK</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($kelas->siswas as $siswa)
                <tr>
                    <td class="muted">{{ $loop->iteration }}</td>
                    <td>{{ $siswa->nipd }}</td>
                    <td>{{ $siswa->nama_siswa }}</td>
                    <td>{{ $siswa->jenis_kelamin }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="muted">Belum ada siswa.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
