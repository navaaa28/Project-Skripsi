@extends('layouts.guru-dashboard')

@section('title', 'Data Siswa')

@section('content')
<style>
    .page-title { font-size: 16px; font-weight: 700; margin-bottom: 12px; }
    .table-wrap { background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 14px; }
    .filter-box { background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 12px 14px; margin-bottom: 12px; display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .input { border: 1px solid #e5e7eb; border-radius: 6px; padding: 7px 10px; font-size: 12px; }
    .btn-outline { border: 1px solid #e5e7eb; background: #fff; padding: 7px 10px; border-radius: 6px; font-size: 12px; cursor: pointer; }
    table { width: 100%; border-collapse: collapse; font-size: 12px; }
    th, td { padding: 10px 8px; border-bottom: 1px solid #e5e7eb; text-align: left; }
    th { color: #6b7280; font-weight: 600; text-transform: uppercase; font-size: 11px; }
    .pagination { margin-top: 12px; display: flex; justify-content: flex-end; gap: 4px; }
    .pagination a, .pagination span { border: 1px solid #e5e7eb; padding: 6px 8px; border-radius: 6px; font-size: 12px; color: #374151; text-decoration: none; }
    .pagination .active span { background: #2563eb; color: #fff; border-color: #2563eb; }
    .muted { color: #6b7280; }
</style>

<div class="page-title">Data Siswa (Kelas Wali) - {{ auth()->user()?->guru?->nama_guru ?? auth()->user()?->username }}</div>

<div class="filter-box">
    <form method="GET" action="{{ route('guru.siswa.index') }}" style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
        <input type="text" name="q" value="{{ request('q') }}" class="input" placeholder="Cari Nama/NIPD/NISN">
        <button class="btn-outline" type="submit">Cari</button>
    </form>
</div>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th style="width: 60px;">No</th>
                <th>NIPD</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Tgl Lahir</th>
                <th style="width: 90px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($siswas as $siswa)
                <tr>
                    <td class="muted">{{ $loop->iteration + ($siswas->currentPage() - 1) * $siswas->perPage() }}</td>
                    <td>{{ $siswa->nipd }}</td>
                    <td>{{ $siswa->nama_siswa }}</td>
                    <td>{{ $siswa->kelas?->nama_kelas ?? '-' }}</td>
                    <td>{{ $siswa->tgl_lahir?->format('d-m-Y') }}</td>
                    <td><a href="{{ route('guru.siswa.show', $siswa) }}">Detail</a></td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="muted">Belum ada data siswa.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pagination">
        {{ $siswas->links() }}
    </div>
</div>
@endsection
