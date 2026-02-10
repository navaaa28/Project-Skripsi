@extends('layouts.admin-dashboard')

@section('title', 'Manajemen Data Siswa')

@section('content')
<style>
    .page-title { font-size: 16px; font-weight: 700; margin-bottom: 12px; }
    .table-wrap { background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 14px; }
    .toolbar { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; gap: 10px; }
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
    .filter-box { background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 12px 14px; margin-bottom: 12px; display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .input, .select { border: 1px solid #e5e7eb; border-radius: 6px; padding: 7px 10px; font-size: 12px; }
    @media (max-width: 900px) {
        .toolbar { flex-direction: column; align-items: flex-start; }
    }
</style>

<div class="toolbar">
    <div class="page-title">Manajemen Data Siswa</div>
    <a href="{{ route('admin.siswa.create') }}" class="btn-primary">+ Tambah Siswa</a>
</div>

<div class="filter-box">
    <form method="GET" action="{{ route('admin.siswa.index') }}" style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
        <label class="muted">Filter Kelas</label>
        <select name="kelas" class="select">
            <option value="">Semua Kelas</option>
            @foreach ($kelasOptions as $k)
                <option value="{{ $k->id_kelas }}" @selected(request('kelas') == $k->id_kelas)>{{ $k->nama_kelas }}</option>
            @endforeach
        </select>
        <input type="text" name="q" value="{{ request('q') }}" class="input" placeholder="Cari Nama/NIPD/NISN">
        <button class="btn-outline" type="submit">Cari</button>
    </form>
</div>

<div class="import-box">
    <form method="POST" action="{{ route('admin.siswa.import') }}" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" accept=".xlsx,.xls" required>
        <button type="submit" class="btn-outline">Import XLSX</button>
        <span class="hint">Header: Nama, NIPD, JK, NISN, Tempat Lahir, Tanggal Lahir, Rombel Saat Ini</span>
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
                <th>NIPD</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Tgl Lahir</th>
                <th style="width: 110px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($siswas as $siswa)
                <tr>
                    <td class="muted">{{ $loop->iteration + ($siswas->currentPage() - 1) * $siswas->perPage() }}</td>
                    <td>{{ $siswa->nipd }}</td>
                    <td>{{ $siswa->nama_siswa }}</td>
                    <td>{{ $siswa->kelas?->nama_kelas ?? $siswa->rombel_saat_ini ?? '-' }}</td>
                    <td>{{ $siswa->tgl_lahir?->format('d-m-Y') }}</td>
                    <td class="aksi">
                        <a class="edit" href="{{ route('admin.siswa.edit', $siswa) }}">Edit</a>
                        <a class="del" href="{{ route('admin.siswa.show', $siswa) }}">Detail</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="muted">Belum ada data siswa.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pagination">
        {{ $siswas->links() }}
    </div>
</div>
@endsection
