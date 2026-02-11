@extends('layouts.admin-dashboard')

@section('title', 'Manajemen Mapel')

@section('content')
<style>
    .admin-index-page { display: flex; flex-direction: column; gap: 14px; }
    .admin-index-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
    .admin-index-title { font-size: 22px; font-weight: 700; color: #0f172a; margin: 0; }
    .admin-index-subtitle { margin: 2px 0 0; font-size: 13px; color: #64748b; }

    .admin-btn-primary {
        display: inline-flex; align-items: center; justify-content: center; gap: 6px;
        background: #2563eb; color: #fff; border: none; border-radius: 10px;
        font-size: 13px; font-weight: 600; padding: 10px 14px; text-decoration: none; white-space: nowrap;
    }
    .admin-btn-primary:hover { background: #1d4ed8; }

    .admin-index-card {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 14px;
        padding: 14px; box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
    }

    .admin-table-shell { width: 100%; overflow-x: auto; border: 1px solid #e5e7eb; border-radius: 10px; }
    .admin-table { width: 100%; min-width: 720px; border-collapse: collapse; font-size: 13px; background: #fff; }
    .admin-table th {
        text-align: left; padding: 12px 10px; font-size: 11px; font-weight: 700; color: #64748b;
        text-transform: uppercase; letter-spacing: .02em; border-bottom: 1px solid #e5e7eb; background: #f8fafc;
    }
    .admin-table td { padding: 12px 10px; border-bottom: 1px solid #edf2f7; color: #1f2937; vertical-align: middle; }
    .admin-table tr:last-child td { border-bottom: none; }
    .admin-muted { color: #64748b; }

    .mapel-chip {
        display: inline-block; padding: 4px 9px; border-radius: 999px;
        background: #f0fdf4; color: #166534; font-size: 11px; font-weight: 700;
    }
    .kkm-chip {
        display: inline-block; padding: 4px 9px; border-radius: 999px;
        background: #fef3c7; color: #92400e; font-size: 11px; font-weight: 700;
    }

    .admin-actions { display: flex; align-items: center; gap: 10px; }
    .admin-action-link { font-size: 12px; font-weight: 600; text-decoration: none; }
    .admin-action-link.edit { color: #2563eb; }
    .admin-action-link.detail { color: #ef4444; }

    .admin-empty { text-align: center; padding: 20px 10px; color: #64748b; font-size: 13px; }

    .pager {
        margin-top: 12px; display: flex; align-items: center; justify-content: space-between;
        gap: 12px; flex-wrap: wrap;
    }
    .pager-info { font-size: 12px; color: #64748b; }
    .pager-links { display: flex; align-items: center; gap: 6px; }
    .pager-btn {
        border: 1px solid #d1d5db; background: #fff; color: #374151;
        border-radius: 8px; padding: 6px 10px; font-size: 12px; text-decoration: none; line-height: 1;
    }
    .pager-btn.active { background: #2563eb; border-color: #2563eb; color: #fff; }
    .pager-btn.disabled { opacity: .5; pointer-events: none; }

    @media (max-width: 900px) {
        .admin-index-toolbar { align-items: flex-start; flex-direction: column; }
        .admin-index-title { font-size: 20px; }
        .admin-index-card { padding: 10px; }
    }
</style>

<div class="admin-index-page">
    <div class="admin-index-toolbar">
        <div>
            <h1 class="admin-index-title">Manajemen Mapel</h1>
            <p class="admin-index-subtitle">Kelola mata pelajaran dan nilai KKM.</p>
        </div>
        <a href="{{ route('admin.mapel.create') }}" class="admin-btn-primary">+ Tambah Mapel</a>
    </div>

    <div class="admin-index-card">
        <div class="admin-table-shell">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 70px;">No</th>
                        <th>Nama Mapel</th>
                        <th style="width: 150px;">KKM</th>
                        <th style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($mapel as $m)
                        <tr>
                            <td class="admin-muted">{{ $loop->iteration + ($mapel->currentPage() - 1) * $mapel->perPage() }}</td>
                            <td><span class="mapel-chip">{{ $m->nama_mapel }}</span></td>
                            <td><span class="kkm-chip">{{ $m->kkm }}</span></td>
                            <td>
                                <div class="admin-actions">
                                    <a class="admin-action-link edit" href="{{ route('admin.mapel.edit', $m) }}">Edit</a>
                                    <a class="admin-action-link detail" href="{{ route('admin.mapel.show', $m) }}">Detail</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="admin-empty">Belum ada data mapel.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($mapel->hasPages())
            <div class="pager">
                <div class="pager-info">Menampilkan {{ $mapel->firstItem() }} - {{ $mapel->lastItem() }} dari {{ $mapel->total() }} data</div>
                <div class="pager-links">
                    <a class="pager-btn {{ $mapel->onFirstPage() ? 'disabled' : '' }}" href="{{ $mapel->previousPageUrl() ?? '#' }}">Prev</a>
                    @foreach ($mapel->getUrlRange(1, $mapel->lastPage()) as $page => $url)
                        <a class="pager-btn {{ $page === $mapel->currentPage() ? 'active' : '' }}" href="{{ $url }}">{{ $page }}</a>
                    @endforeach
                    <a class="pager-btn {{ $mapel->hasMorePages() ? '' : 'disabled' }}" href="{{ $mapel->nextPageUrl() ?? '#' }}">Next</a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
