@extends('layouts.admin-dashboard')

@section('title', 'Detail Guru')

@section('content')
<style>
    .page-title { font-size: 16px; font-weight: 700; margin-bottom: 12px; }
    .card { background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 16px; max-width: 720px; }
    .muted { color: #6b7280; font-size: 12px; }
    .btn { display: inline-block; background: #2563eb; color: #fff; border: none; padding: 8px 12px; border-radius: 6px; font-size: 12px; text-decoration: none; }
    .btn-secondary { background: #e5e7eb; color: #111827; margin-left: 6px; }
    .btn-danger { background: #ef4444; color: #fff; margin-left: 6px; }
</style>

<div class="page-title">Detail Guru</div>
<div class="card">
    <div><span class="muted">User</span>: {{ $guru->user?->username }} ({{ $guru->user?->email ?? '-' }})</div>
    <div><span class="muted">NIP</span>: {{ $guru->nip ?? '-' }}</div>
    <div><span class="muted">Nama Guru</span>: {{ $guru->nama_guru }}</div>
    <div><span class="muted">Jenis Kelamin</span>: {{ $guru->jenis_kelamin ?? '-' }}</div>
    <div><span class="muted">Mapel Utama</span>: {{ $guru->mapel_utama ?? '-' }}</div>

    <div style="margin-top: 12px;">
        <a class="btn" href="{{ route('admin.guru.edit', $guru) }}">Edit Guru</a>
        <a class="btn btn-secondary" href="{{ route('admin.guru.index') }}">Kembali</a>
        <form method="POST" action="{{ route('admin.guru.destroy', $guru) }}" style="display:inline;">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger" type="submit">Hapus</button>
        </form>
    </div>
</div>
@endsection
