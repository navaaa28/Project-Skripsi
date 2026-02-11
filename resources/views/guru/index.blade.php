@extends('layouts.admin-dashboard')

@section('title', 'Manajemen Data Guru')

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
    .admin-btn-outline {
        border: 1px solid #d1d5db; background: #fff; color: #374151;
        padding: 8px 12px; border-radius: 8px; font-size: 12px; cursor: pointer;
    }

    .admin-index-card {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 14px;
        padding: 14px; box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
    }
    .admin-import-form { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .admin-hint { font-size: 12px; color: #64748b; }
    .admin-error-list { margin-top: 10px; color: #ef4444; font-size: 12px; }

    .admin-table-shell { width: 100%; overflow-x: auto; border: 1px solid #e5e7eb; border-radius: 10px; }
    .admin-table { width: 100%; min-width: 760px; border-collapse: collapse; font-size: 13px; background: #fff; }
    .admin-table th {
        text-align: left; padding: 12px 10px; font-size: 11px; font-weight: 700; color: #64748b;
        text-transform: uppercase; letter-spacing: .02em; border-bottom: 1px solid #e5e7eb; background: #f8fafc;
    }
    .admin-table td { padding: 12px 10px; border-bottom: 1px solid #edf2f7; color: #1f2937; vertical-align: middle; }
    .admin-table tr:last-child td { border-bottom: none; }
    .admin-muted { color: #64748b; }

    .wali-chip {
        display: inline-block; padding: 4px 9px; border-radius: 999px;
        background: #ecfeff; color: #155e75; font-size: 11px; font-weight: 700;
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
            <h1 class="admin-index-title">Manajemen Data Guru</h1>
            <p class="admin-index-subtitle">Kelola profil guru dan relasi wali kelas.</p>
        </div>
        <a href="{{ route('admin.guru.create') }}" class="admin-btn-primary">+ Tambah Guru</a>
    </div>

    <div class="admin-index-card">
        <form method="POST" action="{{ route('admin.guru.import') }}" enctype="multipart/form-data" class="admin-import-form">
            @csrf
            <input type="file" name="file" accept=".xlsx,.xls" required>
            <button type="submit" class="admin-btn-outline">Import XLSX</button>
            <span class="admin-hint">Header: Nama, JK, NIP</span>
        </form>
        @if (session('import_errors'))
            <div class="admin-error-list">
                @foreach (session('import_errors') as $msg)
                    <div>{{ $msg }}</div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="admin-index-card">
        <div class="admin-table-shell">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 70px;">No</th>
                        <th>NIP</th>
                        <th>Nama Lengkap</th>
                        <th>Wali Kelas</th>
                        <th style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($gurus as $guru)
                        <tr>
                            <td class="admin-muted">{{ $loop->iteration + ($gurus->currentPage() - 1) * $gurus->perPage() }}</td>
                            <td>{{ $guru->nip }}</td>
                            <td>{{ $guru->nama_guru }}</td>
                            <td>
                                @if ($guru->kelasWali && $guru->kelasWali->count())
                                    <span class="wali-chip">{{ $guru->kelasWali->pluck('nama_kelas')->join(', ') }}</span>
                                @else
                                    <span class="admin-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="admin-actions">
                                    <a class="admin-action-link edit" href="{{ route('admin.guru.edit', $guru) }}">Edit</a>
                                    <a class="admin-action-link detail" href="{{ route('admin.guru.show', $guru) }}">Detail</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="admin-empty">Belum ada data guru.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($gurus->hasPages())
            <div class="pager">
                <div class="pager-info">Menampilkan {{ $gurus->firstItem() }} - {{ $gurus->lastItem() }} dari {{ $gurus->total() }} data</div>
                <div class="pager-links">
                    <a class="pager-btn {{ $gurus->onFirstPage() ? 'disabled' : '' }}" href="{{ $gurus->previousPageUrl() ?? '#' }}">Prev</a>
                    @foreach ($gurus->getUrlRange(1, $gurus->lastPage()) as $page => $url)
                        <a class="pager-btn {{ $page === $gurus->currentPage() ? 'active' : '' }}" href="{{ $url }}">{{ $page }}</a>
                    @endforeach
                    <a class="pager-btn {{ $gurus->hasMorePages() ? '' : 'disabled' }}" href="{{ $gurus->nextPageUrl() ?? '#' }}">Next</a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
