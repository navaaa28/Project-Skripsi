@extends('layouts.guru-dashboard')

@section('title', 'Dashboard Guru')

@section('content')
<style>
    .info {
        background: #e0edff;
        border: 1px solid #c7dcff;
        color: #1d4ed8;
        padding: 10px 12px;
        border-radius: 8px;
        font-size: 12px;
        margin-bottom: 12px;
    }
    .section-title { font-size: 13px; font-weight: 700; margin: 10px 0 8px; }
    .cards { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; }
    .card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 12px;
    }
    .card h4 { margin: 0 0 6px; font-size: 13px; }
    .card small { color: #6b7280; font-size: 11px; display: block; margin-bottom: 8px; }
    .btn {
        display: inline-block;
        background: #2563eb;
        color: #fff;
        border-radius: 6px;
        padding: 6px 10px;
        font-size: 11px;
        text-decoration: none;
    }
    @media (max-width: 1100px) {
        .cards { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 720px) {
        .cards { grid-template-columns: 1fr; }
    }
</style>

<div class="topbar">
    <div class="title">Selamat Datang, {{ $user?->guru?->nama_guru ?? $user?->username }}</div>
    <div class="user">
        <span>{{ $user?->guru?->nama_guru ?? $user?->username }}</span>
    </div>
</div>

<div class="info">
    Informasi Penting<br>
    Periode Input Nilai Semester Ganjil Telah Dibuka!
</div>

<div class="section-title">Daftar Kelas Anda</div>
<div class="cards">
    @forelse ($kelas as $k)
        <div class="card">
            <h4>{{ $k->nama_kelas }}</h4>
            <small>{{ $k->siswas_count }} Siswa</small>
            <a class="btn" href="{{ route('guru.kelas.show', $k) }}">Lihat Detail</a>
        </div>
    @empty
        <div class="card">
            <h4>Belum ada kelas</h4>
            <small>Anda belum ditetapkan sebagai wali kelas.</small>
        </div>
    @endforelse
</div>
@endsection
