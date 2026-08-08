@extends('layouts.admin-dashboard')

@section('title', 'Manajemen Data Siswa')

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
    .admin-filter-form, .admin-import-form { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .admin-hint { font-size: 12px; color: #64748b; }
    .admin-muted { color: #64748b; }
    .admin-input, .admin-select {
        border: 1px solid #d1d5db; border-radius: 8px;
        padding: 8px 10px; font-size: 12px; background: #fff;
    }
    .admin-input { min-width: 220px; }
    .admin-error-list { margin-top: 10px; color: #ef4444; font-size: 12px; }

    /* ── Kelas Tabs ── */
    .kelas-tabs {
        display: flex; gap: 6px; flex-wrap: wrap;
        background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
        padding: 8px 10px;
    }
    .kelas-tab {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 7px 13px; border-radius: 8px;
        font-size: 12px; font-weight: 600; color: #64748b;
        text-decoration: none; border: 1px solid transparent;
        transition: all .2s ease;
    }
    .kelas-tab:hover { background: #f1f5f9; color: #334155; }
    .kelas-tab.active {
        background: #2563eb; color: #fff; border-color: #2563eb;
        box-shadow: 0 2px 8px rgba(37,99,235,0.25);
    }
    .kelas-tab .tab-count {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 20px; height: 18px; padding: 0 5px;
        border-radius: 10px; font-size: 10px; font-weight: 700;
        background: rgba(0,0,0,0.07);
    }
    .kelas-tab.active .tab-count { background: rgba(255,255,255,0.25); color: #fff; }

    /* ── Group Header ── */
    .kelas-group-header {
        display: flex; align-items: center; gap: 10px;
        padding: 12px 16px;
        background: linear-gradient(135deg, #eff6ff 0%, #f0fdf4 100%);
        border-bottom: 1px solid #e2e8f0;
    }
    .kelas-group-header .kelas-badge {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 5px 12px; border-radius: 8px;
        background: #2563eb; color: #fff;
        font-size: 13px; font-weight: 700;
    }
    .kelas-group-header .kelas-count { font-size: 12px; color: #64748b; }

    /* ── Table ── */
    .admin-table-shell { width: 100%; overflow-x: auto; border: 1px solid #e5e7eb; border-radius: 10px; }
    .admin-table { width: 100%; min-width: 820px; border-collapse: collapse; font-size: 13px; background: #fff; }
    .admin-table th {
        text-align: left; padding: 12px 10px; font-size: 11px; font-weight: 700; color: #64748b;
        text-transform: uppercase; letter-spacing: .02em; border-bottom: 1px solid #e5e7eb; background: #f8fafc;
    }
    .admin-table td { padding: 12px 10px; border-bottom: 1px solid #edf2f7; color: #1f2937; vertical-align: middle; }
    .admin-table tr:last-child td { border-bottom: none; }
    .admin-table tbody tr { transition: background .15s ease; }
    .admin-table tbody tr:hover { background: #f8fafc; }

    .kelas-chip {
        display: inline-block; padding: 4px 9px; border-radius: 999px;
        background: #eff6ff; color: #1d4ed8; font-size: 11px; font-weight: 700;
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

    /* ── Search ── */
    .search-panel {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
        padding: 10px 14px; display: flex; align-items: center; gap: 10px;
    }
    .search-icon { color: #94a3b8; flex-shrink: 0; }
    .search-input {
        flex: 1; border: none; outline: none; font-size: 13px; color: #1e293b;
        background: transparent; padding: 6px 0;
    }
    .search-input::placeholder { color: #94a3b8; }
    .search-result-count { font-size: 11px; color: #94a3b8; white-space: nowrap; }

    .fade-in { animation: fadeIn .25s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }

    @media (max-width: 900px) {
        .admin-index-toolbar { align-items: flex-start; flex-direction: column; }
        .admin-index-title { font-size: 20px; }
        .admin-index-card { padding: 10px; }
    }
</style>

<div class="admin-index-page fade-in">
    <div class="admin-index-toolbar">
        <div>
            <h1 class="admin-index-title">Manajemen Data Siswa</h1>
            <p class="admin-index-subtitle">Kelola data siswa, kelas aktif, dan import massal.</p>
        </div>
        <a href="{{ route('admin.siswa.create') }}" class="admin-btn-primary">+ Tambah Siswa</a>
    </div>

    {{-- Kelas Tab Navigation --}}
    <div class="kelas-tabs">
        <a class="kelas-tab {{ !request('kelas') && !request('q') ? 'active' : '' }}"
           href="{{ route('admin.siswa.index') }}">
            Semua
            <span class="tab-count">{{ \App\Models\Siswa::count() }}</span>
        </a>
        @foreach ($kelasOptions as $k)
            @php $count = \App\Models\Siswa::where('id_kelas', $k->id_kelas)->count(); @endphp
            <a class="kelas-tab {{ request('kelas') == $k->id_kelas ? 'active' : '' }}"
               href="{{ route('admin.siswa.index', ['kelas' => $k->id_kelas]) }}">
                {{ $k->nama_kelas }}
                <span class="tab-count">{{ $count }}</span>
            </a>
        @endforeach
        <a class="kelas-tab {{ request('kelas') === 'all' ? 'active' : '' }}"
           href="{{ route('admin.siswa.index', ['kelas' => 'all']) }}">
            Tampilan Grup
            <span class="tab-count">👁</span>
        </a>
    </div>

    {{-- Search --}}
    <div class="search-panel">
        <svg class="search-icon" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
        </svg>
        <form method="GET" action="{{ route('admin.siswa.index') }}" style="display:contents;">
            @if(request('kelas'))
                <input type="hidden" name="kelas" value="{{ request('kelas') }}">
            @endif
            <input type="text" name="q" class="search-input" id="liveSearch"
                   placeholder="Cari nama siswa, NIPD, atau NISN..."
                   value="{{ request('q') }}" autocomplete="off">
        </form>
        <span class="search-result-count" id="searchCount"></span>
    </div>

    {{-- Import --}}
    <div class="admin-index-card">
        <form method="POST" action="{{ route('admin.siswa.import') }}" enctype="multipart/form-data" class="admin-import-form">
            @csrf
            <input type="file" name="file" accept=".xlsx,.xls,.csv" required style="font-size: 13px;">
            <button type="submit" class="admin-btn-outline">Import XLSX</button>
            <a href="{{ asset('templates/template_import_siswa.xlsx') }}" download class="admin-btn-outline" style="background:#f1f5f9; color:#475569; text-decoration:none; display:inline-block; margin-left: 10px;">Download Template</a>
        </form>
        <div style="margin-top: 8px;">
            <span class="admin-hint">Header: Nama, NIPD, JK, NISN, Tempat Lahir, Tanggal Lahir, Rombel Saat Ini</span>
        </div>
        @if (session('import_errors'))
            <div class="admin-error-list">
                @foreach (session('import_errors') as $msg)
                    <div>{{ $msg }}</div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ===== GROUPED VIEW ===== --}}
    @if ($groupedSiswas)
        @forelse ($groupedSiswas->sortKeys() as $namaKelas => $students)
            <div class="admin-index-card fade-in kelas-group" style="padding: 0; overflow: hidden;">
                <div class="kelas-group-header">
                    <span class="kelas-badge">📚 {{ $namaKelas }}</span>
                    <span class="kelas-count">{{ $students->count() }} siswa</span>
                </div>
                <div class="admin-table-shell" style="border: none; border-radius: 0;">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>NIPD</th>
                                <th>Nama Siswa</th>
                                <th>Tgl Lahir</th>
                                <th style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($students->sortBy('nama_siswa')->values() as $siswa)
                                <tr class="siswa-row" data-search="{{ strtolower($siswa->nama_siswa . ' ' . $siswa->nipd . ' ' . $siswa->nisn) }}">
                                    <td class="admin-muted">{{ $loop->iteration }}</td>
                                    <td>{{ $siswa->nipd }}</td>
                                    <td style="font-weight: 600;">{{ $siswa->nama_siswa }}</td>
                                    <td>{{ $siswa->tgl_lahir?->format('d-m-Y') }}</td>
                                    <td>
                                        <div class="admin-actions">
                                            <a class="admin-action-link edit" href="{{ route('admin.siswa.edit', $siswa) }}">Edit</a>
                                            <a class="admin-action-link detail" href="{{ route('admin.siswa.show', $siswa) }}">Detail</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="admin-index-card">
                <div class="admin-empty">Belum ada data siswa.</div>
            </div>
        @endforelse

    {{-- ===== FLAT PAGINATED VIEW ===== --}}
    @elseif ($siswas)
        <div class="admin-index-card">
            <div class="admin-table-shell">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 70px;">No</th>
                            <th>NIPD</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Tgl Lahir</th>
                            <th style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($siswas as $siswa)
                            <tr class="siswa-row" data-search="{{ strtolower($siswa->nama_siswa . ' ' . $siswa->nipd . ' ' . $siswa->nisn) }}">
                                <td class="admin-muted">{{ $loop->iteration + ($siswas->currentPage() - 1) * $siswas->perPage() }}</td>
                                <td>{{ $siswa->nipd }}</td>
                                <td style="font-weight: 600;">{{ $siswa->nama_siswa }}</td>
                                <td>
                                    <span class="kelas-chip">{{ $siswa->kelas?->nama_kelas ?? $siswa->rombel_saat_ini ?? '-' }}</span>
                                </td>
                                <td>{{ $siswa->tgl_lahir?->format('d-m-Y') }}</td>
                                <td>
                                    <div class="admin-actions">
                                        <a class="admin-action-link edit" href="{{ route('admin.siswa.edit', $siswa) }}">Edit</a>
                                        <a class="admin-action-link detail" href="{{ route('admin.siswa.show', $siswa) }}">Detail</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="admin-empty">Belum ada data siswa.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($siswas->hasPages())
                <div class="pager">
                    <div class="pager-info">Menampilkan {{ $siswas->firstItem() }} - {{ $siswas->lastItem() }} dari {{ $siswas->total() }} data</div>
                    <div class="pager-links">
                        <a class="pager-btn {{ $siswas->onFirstPage() ? 'disabled' : '' }}" href="{{ $siswas->previousPageUrl() ?? '#' }}">Prev</a>
                        @foreach ($siswas->getUrlRange(1, $siswas->lastPage()) as $page => $url)
                            <a class="pager-btn {{ $page === $siswas->currentPage() ? 'active' : '' }}" href="{{ $url }}">{{ $page }}</a>
                        @endforeach
                        <a class="pager-btn {{ $siswas->hasMorePages() ? '' : 'disabled' }}" href="{{ $siswas->nextPageUrl() ?? '#' }}">Next</a>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('liveSearch');
    const searchCount = document.getElementById('searchCount');
    const rows = document.querySelectorAll('.siswa-row');
    const groups = document.querySelectorAll('.kelas-group');

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const term = this.value.toLowerCase().trim();
            let visible = 0;

            rows.forEach(function (row) {
                const data = row.getAttribute('data-search') || '';
                const match = !term || data.includes(term);
                row.style.display = match ? '' : 'none';
                if (match) visible++;
            });

            // Hide group cards if all their rows are hidden
            groups.forEach(function (group) {
                const groupRows = group.querySelectorAll('.siswa-row');
                const anyVisible = Array.from(groupRows).some(r => r.style.display !== 'none');
                group.style.display = anyVisible ? '' : 'none';
            });

            if (term) {
                searchCount.textContent = visible + ' ditemukan';
            } else {
                searchCount.textContent = '';
            }
        });
    }
});
</script>
@endsection
