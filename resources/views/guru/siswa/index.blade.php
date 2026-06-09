@extends('layouts.guru-dashboard')

@section('title', 'Data Siswa')

@section('content')
<style>
    .ds-page { display: flex; flex-direction: column; gap: 14px; }
    .ds-header { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; }
    .ds-title { font-size: 18px; font-weight: 700; color: #0f172a; margin: 0; }
    .ds-subtitle { font-size: 12px; color: #64748b; margin: 2px 0 0; }

    /* ── Kelas Tabs ── */
    .kelas-tabs {
        display: flex; gap: 6px; flex-wrap: wrap;
        background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
        padding: 8px 10px;
    }
    .kelas-tab {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 14px; border-radius: 8px;
        font-size: 13px; font-weight: 600; color: #64748b;
        text-decoration: none; border: 1px solid transparent;
        transition: all .2s ease;
        cursor: pointer;
        background: transparent;
    }
    .kelas-tab:hover { background: #f1f5f9; color: #334155; }
    .kelas-tab.active {
        background: #2563eb; color: #fff; border-color: #2563eb;
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.25);
    }
    .kelas-tab .tab-count {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 22px; height: 20px; padding: 0 5px;
        border-radius: 10px; font-size: 11px; font-weight: 700;
        background: rgba(0,0,0,0.07);
    }
    .kelas-tab.active .tab-count { background: rgba(255,255,255,0.25); color: #fff; }

    /* ── Search Bar ── */
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

    /* ── Table Card ── */
    .table-card {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
        overflow: hidden; box-shadow: 0 4px 16px rgba(15,23,42,0.04);
    }

    /* ── Kelas Group Header ── */
    .kelas-group-header {
        display: flex; align-items: center; gap: 10px;
        padding: 12px 16px;
        background: linear-gradient(135deg, #eff6ff 0%, #f0fdf4 100%);
        border-bottom: 1px solid #e2e8f0;
        cursor: default;
    }
    .kelas-group-header .kelas-badge {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 5px 12px; border-radius: 8px;
        background: #2563eb; color: #fff;
        font-size: 13px; font-weight: 700;
    }
    .kelas-group-header .kelas-count {
        font-size: 12px; color: #64748b;
    }

    /* ── Data Table ── */
    .data-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .data-table th {
        text-align: left; padding: 10px 14px; font-size: 11px; font-weight: 700;
        color: #64748b; text-transform: uppercase; letter-spacing: .03em;
        border-bottom: 1px solid #e5e7eb; background: #f8fafc;
    }
    .data-table td {
        padding: 11px 14px; border-bottom: 1px solid #f1f5f9; color: #1e293b;
        vertical-align: middle;
    }
    .data-table tbody tr { transition: background .15s ease; }
    .data-table tbody tr:hover { background: #f8fafc; }
    .data-table tbody tr:last-child td { border-bottom: none; }

    .data-table .kelas-chip {
        display: inline-block; padding: 3px 9px; border-radius: 999px;
        background: #eff6ff; color: #1d4ed8; font-size: 11px; font-weight: 700;
    }
    .data-table .btn-detail {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 5px 12px; border-radius: 6px;
        background: #f0f9ff; color: #0369a1; font-size: 12px; font-weight: 600;
        text-decoration: none; transition: all .2s ease; border: 1px solid #bae6fd;
    }
    .data-table .btn-detail:hover { background: #0ea5e9; color: #fff; border-color: #0ea5e9; }

    .empty-state {
        text-align: center; padding: 40px 20px; color: #94a3b8;
    }
    .empty-state .empty-icon { font-size: 36px; margin-bottom: 8px; }
    .empty-state .empty-text { font-size: 13px; }

    /* ── Pagination ── */
    .pager {
        padding: 12px 16px;
        display: flex; align-items: center; justify-content: space-between;
        gap: 12px; flex-wrap: wrap; border-top: 1px solid #f1f5f9;
    }
    .pager-info { font-size: 12px; color: #64748b; }
    .pager-links { display: flex; align-items: center; gap: 4px; }
    .pager-btn {
        border: 1px solid #e2e8f0; background: #fff; color: #374151;
        border-radius: 8px; padding: 6px 11px; font-size: 12px;
        text-decoration: none; line-height: 1; transition: all .15s ease;
    }
    .pager-btn:hover { background: #f1f5f9; }
    .pager-btn.active { background: #2563eb; border-color: #2563eb; color: #fff; }
    .pager-btn.disabled { opacity: .4; pointer-events: none; }

    /* ── Animation ── */
    .fade-in { animation: fadeIn .25s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }

    /* ── Responsive ── */
    @media (max-width: 768px) {
        .kelas-tabs { gap: 4px; }
        .kelas-tab { padding: 6px 10px; font-size: 12px; }
        .data-table { font-size: 12px; }
        .data-table th, .data-table td { padding: 8px 10px; }
    }
</style>

<div class="ds-page fade-in">
    {{-- Header --}}
    <div class="ds-header">
        <div>
            <h1 class="ds-title">Data Siswa Kelas Wali</h1>
            <p class="ds-subtitle">{{ auth()->user()?->guru?->nama_guru ?? auth()->user()?->username }} — Daftar siswa berdasarkan kelas perwalian</p>
        </div>
    </div>

    {{-- Kelas Tab Navigation --}}
    <div class="kelas-tabs" id="kelasTabs">
        @foreach ($kelasOptions as $k)
            @php
                $isActive = (string)($selectedKelas ?? '') === (string)$k->id_kelas;
                $count = \App\Models\Siswa::where('id_kelas', $k->id_kelas)->count();
            @endphp
            <a class="kelas-tab {{ $isActive ? 'active' : '' }}"
               href="{{ route('guru.siswa.index', ['kelas' => $k->id_kelas]) }}">
                {{ $k->nama_kelas }}
                <span class="tab-count">{{ $count }}</span>
            </a>
        @endforeach
        <a class="kelas-tab {{ ($selectedKelas ?? '') === 'all' ? 'active' : '' }}"
           href="{{ route('guru.siswa.index', ['kelas' => 'all']) }}">
            Semua Kelas
            <span class="tab-count">{{ \App\Models\Siswa::whereIn('id_kelas', $kelasOptions->pluck('id_kelas'))->count() }}</span>
        </a>
    </div>

    {{-- Search Panel --}}
    <div class="search-panel">
        <svg class="search-icon" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
        </svg>
        <input type="text" class="search-input" id="liveSearch"
               placeholder="Cari nama siswa, NIPD, atau NISN..."
               value="{{ request('q') }}"
               autocomplete="off">
        <span class="search-result-count" id="searchCount"></span>
    </div>

    {{-- ===== GROUPED VIEW (Semua Kelas) ===== --}}
    @if ($groupedSiswas)
        @forelse ($groupedSiswas->sortKeys() as $namaKelas => $students)
            <div class="table-card fade-in kelas-group" data-kelas="{{ $namaKelas }}">
                <div class="kelas-group-header">
                    <span class="kelas-badge">📚 {{ $namaKelas }}</span>
                    <span class="kelas-count">{{ $students->count() }} siswa</span>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>NIPD</th>
                            <th>Nama Siswa</th>
                            <th>Tgl Lahir</th>
                            <th style="width: 90px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($students->sortBy('nama_siswa')->values() as $siswa)
                            <tr class="siswa-row" data-search="{{ strtolower($siswa->nama_siswa . ' ' . $siswa->nipd . ' ' . $siswa->nisn) }}">
                                <td style="color: #94a3b8;">{{ $loop->iteration }}</td>
                                <td>{{ $siswa->nipd ?? '-' }}</td>
                                <td style="font-weight: 600;">{{ $siswa->nama_siswa }}</td>
                                <td>{{ $siswa->tgl_lahir?->format('d-m-Y') ?? '-' }}</td>
                                <td><a class="btn-detail" href="{{ route('guru.siswa.show', $siswa) }}">Detail</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @empty
            <div class="table-card">
                <div class="empty-state">
                    <div class="empty-icon">📋</div>
                    <div class="empty-text">Belum ada data siswa di kelas perwalian Anda.</div>
                </div>
            </div>
        @endforelse

    {{-- ===== SINGLE KELAS VIEW (Paginated) ===== --}}
    @elseif ($siswas)
        <div class="table-card fade-in">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>NIPD</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Tgl Lahir</th>
                        <th style="width: 90px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="siswaTableBody">
                    @forelse ($siswas as $siswa)
                        <tr class="siswa-row" data-search="{{ strtolower($siswa->nama_siswa . ' ' . $siswa->nipd . ' ' . $siswa->nisn) }}">
                            <td style="color: #94a3b8;">{{ $loop->iteration + ($siswas->currentPage() - 1) * $siswas->perPage() }}</td>
                            <td>{{ $siswa->nipd ?? '-' }}</td>
                            <td style="font-weight: 600;">{{ $siswa->nama_siswa }}</td>
                            <td><span class="kelas-chip">{{ $siswa->kelas?->nama_kelas ?? '-' }}</span></td>
                            <td>{{ $siswa->tgl_lahir?->format('d-m-Y') ?? '-' }}</td>
                            <td><a class="btn-detail" href="{{ route('guru.siswa.show', $siswa) }}">Detail</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <div class="empty-icon">📋</div>
                                    <div class="empty-text">Belum ada data siswa di kelas ini.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if ($siswas->hasPages())
                <div class="pager">
                    <div class="pager-info">Menampilkan {{ $siswas->firstItem() }} – {{ $siswas->lastItem() }} dari {{ $siswas->total() }} siswa</div>
                    <div class="pager-links">
                        <a class="pager-btn {{ $siswas->onFirstPage() ? 'disabled' : '' }}" href="{{ $siswas->previousPageUrl() ?? '#' }}">← Prev</a>
                        @foreach ($siswas->getUrlRange(1, $siswas->lastPage()) as $page => $url)
                            <a class="pager-btn {{ $page === $siswas->currentPage() ? 'active' : '' }}" href="{{ $url }}">{{ $page }}</a>
                        @endforeach
                        <a class="pager-btn {{ $siswas->hasMorePages() ? '' : 'disabled' }}" href="{{ $siswas->nextPageUrl() ?? '#' }}">Next →</a>
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

            // Hide group headers if all their rows are hidden
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

        // Trigger on load if there's a search value
        if (searchInput.value) {
            searchInput.dispatchEvent(new Event('input'));
        }
    }
});
</script>
@endsection
