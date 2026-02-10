@extends('layouts.admin-dashboard')

@section('title', 'Manajemen Data Guru')

@section('content')
<style>
    .page-title { font-size: 16px; font-weight: 700; margin-bottom: 12px; }
    .table-wrap { background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 14px; }
    .toolbar { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; }
    .btn-primary { background: #2563eb; color: #fff; border: none; padding: 8px 12px; border-radius: 6px; font-size: 12px; cursor: pointer; text-decoration: none; }
    table { width: 100%; border-collapse: collapse; font-size: 12px; }
    th, td { padding: 10px 8px; border-bottom: 1px solid #e5e7eb; text-align: left; }
    th { color: #6b7280; font-weight: 600; text-transform: uppercase; font-size: 11px; }
    .aksi a { text-decoration: none; margin-right: 8px; font-size: 12px; }
    .aksi .edit { color: #2563eb; }
    .aksi .del { color: #ef4444; }
    .pagination { margin-top: 12px; display: flex; justify-content: flex-end; gap: 4px; }
    .pagination a, .pagination span { border: 1px solid #e5e7eb; padding: 6px 8px; border-radius: 6px; font-size: 12px; color: #374151; text-decoration: none; }
    .pagination .active span { background: #2563eb; color: #fff; border-color: #2563eb; }
    .muted { color: #6b7280; }
    .import-box { background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 12px 14px; margin-bottom: 12px; }
    .import-box form { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .btn-outline { border: 1px solid #e5e7eb; background: #fff; padding: 7px 10px; border-radius: 6px; font-size: 12px; cursor: pointer; }
    .hint { font-size: 11px; color: #6b7280; }
    @media (max-width: 900px) {
        .toolbar { flex-direction: column; align-items: flex-start; gap: 8px; }
    }
</style>

<div class="toolbar">
    <div class="page-title">Manajemen Data Guru</div>
    <a href="{{ route('admin.guru.create') }}" class="btn-primary">+ Tambah Guru</a>
</div>

<div class="import-box">
    <form method="POST" action="{{ route('admin.guru.import') }}" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" accept=".xlsx,.xls" required>
        <button type="submit" class="btn-outline">Import XLSX</button>
        <span class="hint">Header: Nama, JK, NIP</span>
    </form>
    @if (session('import_errors'))
        <div class="mt-3" style="color:#ef4444; font-size:12px;">
            @foreach (session('import_errors') as $msg)
                <div>{{ $msg }}</div>
            @endforeach
        </div>
    @endif
</div>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th style="width: 60px;">No</th>
                <th>NIP</th>
                <th>Nama Lengkap</th>
                <th>Wali Kelas</th>
                <th style="width: 110px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($gurus as $guru)
                <tr>
                    <td class="muted">{{ $loop->iteration + ($gurus->currentPage() - 1) * $gurus->perPage() }}</td>
                    <td>{{ $guru->nip }}</td>
                    <td>{{ $guru->nama_guru }}</td>
                    <td>
                        @if ($guru->kelasWali && $guru->kelasWali->count())
                            {{ $guru->kelasWali->pluck('nama_kelas')->join(', ') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="aksi">
                        <a class="edit" href="{{ route('admin.guru.edit', $guru) }}">Edit</a>
                        <a class="del" href="{{ route('admin.guru.show', $guru) }}">Detail</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="muted">Belum ada data guru.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pagination">
        {{ $gurus->links() }}
    </div>
</div>
@endsection
