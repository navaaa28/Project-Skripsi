@extends('layouts.guru-dashboard')

@section('title', 'Riwayat Analisis')

@section('content')
<style>
    .panel { background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 16px; }
    .title-page { font-size: 18px; font-weight: 700; margin-bottom: 6px; }
    .subtitle { font-size: 13px; color: #6b7280; margin-bottom: 14px; }
    .filters { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 14px; }
    .input, .select {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 8px 10px;
        font-size: 13px;
        min-width: 180px;
    }
    .btn {
        border: none;
        background: #2563eb;
        color: #fff;
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }
    .btn-secondary { background: #64748b; }
    .table-wrap { border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; }
    table { width: 100%; border-collapse: collapse; font-size: 13px; }
    th, td { text-align: left; padding: 10px 12px; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
    thead th { background: #f8fafc; font-weight: 700; color: #334155; }
    tbody tr:hover { background: #f8fafc; }
    .muted { color: #64748b; }
    .empty { text-align: center; padding: 18px; color: #64748b; }
    .pager { display:flex; justify-content:space-between; align-items:center; margin-top: 12px; }
    .pager-links a, .pager-links span {
        padding: 6px 10px;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        text-decoration: none;
        color: #0f172a;
        margin-right: 6px;
        font-size: 12px;
    }
</style>

<div class="panel">
    <div class="title-page">Riwayat Analisis</div>
    <div class="subtitle">Daftar hasil analisis minat dan bakat siswa pada kelas yang Anda ampu.</div>

    <form method="GET" action="{{ route('guru.analisis.index') }}" class="filters">
        <input type="text" name="q" value="{{ request('q') }}" class="input" placeholder="Cari nama / NIPD / NISN">
        <select name="semester" class="select">
            <option value="">Semua Semester</option>
            <option value="1" @selected(request('semester') == '1')>Semester 1</option>
            <option value="2" @selected(request('semester') == '2')>Semester 2</option>
        </select>
        <button type="submit" class="btn">Filter</button>
        <a href="{{ route('guru.analisis.index') }}" class="btn btn-secondary">Reset</a>
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width: 48px;">No</th>
                    <th>Siswa</th>
                    <th>Kelas</th>
                    <th>Semester</th>
                    <th>Minat Utama</th>
                    <th>Bakat Potensial</th>
                    <th>Tanggal Analisis</th>
                    <th style="width: 120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($riwayat as $item)
                    <tr>
                        <td class="muted">{{ $loop->iteration + ($riwayat->currentPage() - 1) * $riwayat->perPage() }}</td>
                        <td>
                            <div>{{ $item->siswa?->nama_siswa ?? '-' }}</div>
                            <div class="muted">{{ $item->siswa?->nipd ?? '-' }}</div>
                        </td>
                        <td>{{ $item->siswa?->kelas?->nama_kelas ?? '-' }}</td>
                        <td>{{ $item->semester }}</td>
                        <td>{{ $item->minat_utama ?? '-' }}</td>
                        <td>{{ $item->bakat_potensial ?? '-' }}</td>
                        <td>{{ $item->tgl_analisis ?? '-' }}</td>
                        <td>
                            @if ($item->siswa)
                                <a class="btn" href="{{ route('guru.siswa.show', $item->siswa) }}">Lihat Detail</a>
                            @else
                                <span class="muted">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="empty">Belum ada data riwayat analisis.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($riwayat->hasPages())
        <div class="pager">
            <div class="muted">
                Menampilkan {{ $riwayat->firstItem() }} - {{ $riwayat->lastItem() }} dari {{ $riwayat->total() }} data
            </div>
            <div class="pager-links">
                {{ $riwayat->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
