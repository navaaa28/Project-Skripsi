@extends('layouts.guru-dashboard')

@section('title', 'Detail Siswa')

@section('content')
<style>
    .panel { background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 16px; margin-bottom: 12px; }
    .breadcrumb { font-size: 12px; color: #6b7280; margin-bottom: 10px; }
    .student-box { display: flex; align-items: center; gap: 12px; }
    .avatar { width: 56px; height: 56px; border: 1px solid #e5e7eb; border-radius: 8px; background: #f3f4f6; display: flex; align-items: center; justify-content: center; color: #9ca3af; }
    .tabs { display: flex; gap: 12px; border-bottom: 1px solid #e5e7eb; margin: 8px 0 12px; }
    .tab { background: none; border: none; padding: 8px 0; font-size: 12px; color: #6b7280; border-bottom: 2px solid transparent; cursor: pointer; }
    .tab.active { color: #2563eb; border-color: #2563eb; font-weight: 600; }
    table { width: 100%; border-collapse: collapse; font-size: 12px; }
    th, td { padding: 10px 8px; border-bottom: 1px solid #e5e7eb; text-align: left; }
    th { color: #6b7280; font-weight: 600; text-transform: uppercase; font-size: 11px; }
    .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px; }
    .pill { display: inline-flex; align-items: center; gap: 6px; padding: 6px 10px; border-radius: 999px; background: #f3f4f6; font-size: 12px; color: #374151; margin: 4px 6px 0 0; }
    .pill .pct { font-weight: 700; color: #2563eb; }
    .muted { color: #6b7280; font-size: 12px; }
    .section-title { font-weight: 700; font-size: 13px; margin-bottom: 6px; }
</style>

<div class="topbar">
    <div class="title">Data Siswa » Detail {{ $siswa->nama_siswa }}</div>
    <div class="user">
        <span>{{ auth()->user()?->guru?->nama_guru ?? auth()->user()?->username }}</span>
    </div>
</div>

<div class="panel">
    <div class="student-box">
        <div class="avatar">X</div>
        <div>
            <div style="font-weight:700;">{{ $siswa->nama_siswa }}</div>
            <div class="breadcrumb">NIPD: {{ $siswa->nipd ?? '-' }}</div>
            <div class="breadcrumb">Kelas: {{ $siswa->kelas?->nama_kelas ?? '-' }}</div>
        </div>
    </div>
</div>

<div class="panel">
    <div class="tabs">
        <button type="button" class="tab active" data-tab="riwayat">Riwayat Nilai</button>
        <button type="button" class="tab" data-tab="grafik">Grafik Perkembangan</button>
    </div>

    <div id="tab-riwayat">
        <table>
            <thead>
                <tr>
                    <th>Semester</th>
                    <th>Mapel</th>
                    <th>Nilai Akhir</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($nilai as $n)
                    <tr>
                        <td>{{ $n->semester }}</td>
                        <td>{{ $n->mapel?->nama_mapel ?? '-' }}</td>
                        <td>{{ $n->nilai_akhir !== null ? number_format($n->nilai_akhir, 1) : '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">Belum ada nilai.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div id="tab-grafik" style="display:none;">
        <div style="font-weight:700; font-size:13px; margin-bottom:8px;">Tren Nilai Akademik</div>
        <canvas id="nilaiChart" height="120"></canvas>
    </div>
</div>

<div class="panel">
    <div class="section-title" style="margin-bottom: 4px;">Hasil Analisis Minat & Bakat</div>
    @if (!$rekomendasi)
        <div class="muted">Belum ada hasil analisis. Isi semua nilai mata pelajaran terlebih dahulu.</div>
    @else
        <div class="muted" style="margin-bottom: 14px;">Semester {{ $rekomendasi->semester }} · {{ $rekomendasi->tgl_analisis }}</div>

        <div class="hasil-grid">
            {{-- Minat --}}
            <div class="hasil-box">
                <div class="hasil-label">Minat</div>
                @if ($rekomendasi->minat_utama)
                    <div class="hasil-value">{{ $rekomendasi->minat_utama }}</div>
                @endif
                @if (!empty($rekomendasi->minat_json))
                    <div class="bar-list">
                        @foreach ($rekomendasi->minat_json as $item)
                            <div class="bar-row">
                                <span class="bar-name">{{ $item['nama'] ?? '-' }}</span>
                                <div class="bar-track"><div class="bar-fill bar-blue" style="width: {{ $item['persentase'] ?? 0 }}%"></div></div>
                                <span class="bar-num">{{ $item['persentase'] ?? 0 }}%</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Bakat --}}
            <div class="hasil-box">
                <div class="hasil-label">Bakat</div>
                @if ($rekomendasi->bakat_potensial)
                    <div class="hasil-value">{{ $rekomendasi->bakat_potensial }}</div>
                @endif
                @if (!empty($rekomendasi->bakat_json))
                    <div class="bar-list">
                        @foreach ($rekomendasi->bakat_json as $item)
                            <div class="bar-row">
                                <span class="bar-name">{{ $item['nama'] ?? '-' }}</span>
                                <div class="bar-track"><div class="bar-fill bar-teal" style="width: {{ $item['persentase'] ?? 0 }}%"></div></div>
                                <span class="bar-num">{{ $item['persentase'] ?? 0 }}%</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Text sections --}}
        @if ($rekomendasi->analisis_tren)
            <div class="catatan-block">
                <div class="catatan-title">Analisis Tren</div>
                <ul class="catatan-list">
                    @foreach (array_filter(array_map('trim', preg_split('/(?<=\.)\s+/', $rekomendasi->analisis_tren))) as $sentence)
                        <li>{{ $sentence }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($rekomendasi->ringkasan_non_akademik)
            <div class="catatan-block">
                <div class="catatan-title">Ringkasan Non-Akademik</div>
                <ul class="catatan-list">
                    @foreach (array_filter(array_map('trim', preg_split('/(?<=\.)\s+/', $rekomendasi->ringkasan_non_akademik))) as $sentence)
                        <li>{{ $sentence }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($rekomendasi->saran_pengembangan)
            <div class="catatan-block">
                <div class="catatan-title">Saran Pengembangan</div>
                <ul class="catatan-list">
                    @foreach (array_filter(array_map('trim', preg_split('/(?<=\.)\s+/', $rekomendasi->saran_pengembangan))) as $sentence)
                        <li>{{ $sentence }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    @endif
</div>

<style>
    /* ---- Hasil Analisis ---- */
    .hasil-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
    .hasil-box { border: 1px solid #e5e7eb; border-radius: 8px; padding: 14px; background: #fafafa; }
    .hasil-label { font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: .03em; margin-bottom: 4px; }
    .hasil-value { font-size: 14px; font-weight: 700; color: #1e293b; margin-bottom: 10px; }

    .bar-list { display: flex; flex-direction: column; gap: 8px; }
    .bar-row { display: flex; align-items: center; gap: 8px; }
    .bar-name { font-size: 11px; color: #475569; width: 130px; flex-shrink: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .bar-track { flex: 1; height: 6px; border-radius: 3px; background: #e5e7eb; overflow: hidden; }
    .bar-fill { height: 100%; border-radius: 3px; }
    .bar-blue { background: #3b82f6; }
    .bar-teal { background: #14b8a6; }
    .bar-num { font-size: 11px; font-weight: 600; color: #374151; width: 32px; text-align: right; flex-shrink: 0; }

    .catatan-block { border-top: 1px solid #f0f0f0; padding-top: 12px; margin-top: 12px; }
    .catatan-title { font-size: 12px; font-weight: 700; color: #1e293b; margin-bottom: 6px; }
    .catatan-list {
        list-style: disc; margin: 0; padding-left: 18px;
        display: flex; flex-direction: column; gap: 4px;
    }
    .catatan-list li { font-size: 12px; color: #475569; line-height: 1.55; }

    @media (max-width: 720px) { .hasil-grid { grid-template-columns: 1fr; } }
</style>

@php
    $series = [];
    $semesters = $nilai->pluck('semester')->unique()->sort()->values();
    foreach ($nilai as $n) {
        $mapelName = $n->mapel?->nama_mapel ?? 'Mapel';
        $series[$mapelName][$n->semester] = $n->nilai_akhir;
    }
@endphp

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    (function () {
        const semesters = @json($semesters);
        const series = @json($series);
        const labels = semesters.length ? semesters : [1, 2];

        const colors = [
            '#ef4444', '#2563eb', '#16a34a', '#f59e0b', '#8b5cf6', '#0ea5e9',
            '#14b8a6', '#f97316', '#84cc16', '#ec4899'
        ];

        const datasets = Object.keys(series).map((name, idx) => {
            const data = labels.map(s => (series[name] && series[name][s] !== undefined) ? series[name][s] : null);
            return {
                label: name,
                data,
                borderColor: colors[idx % colors.length],
                backgroundColor: colors[idx % colors.length],
                tension: 0.3,
                spanGaps: true
            };
        });

        const ctx = document.getElementById('nilaiChart');
        if (!ctx) return;
        new Chart(ctx, {
            type: 'line',
            data: { labels: labels.map(s => `Semester ${s}`), datasets },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                },
                scales: {
                    y: { min: 0, max: 100, ticks: { stepSize: 10 } }
                }
            }
        });
        const tabButtons = document.querySelectorAll('.tab[data-tab]');
        const tabRiwayat = document.getElementById('tab-riwayat');
        const tabGrafik = document.getElementById('tab-grafik');

        function setActive(tab) {
            tabButtons.forEach(btn => btn.classList.remove('active'));
            const activeBtn = document.querySelector(`.tab[data-tab="${tab}"]`);
            if (activeBtn) activeBtn.classList.add('active');
            tabRiwayat.style.display = tab === 'riwayat' ? 'block' : 'none';
            tabGrafik.style.display = tab === 'grafik' ? 'block' : 'none';
        }

        tabButtons.forEach(btn => {
            btn.addEventListener('click', () => setActive(btn.dataset.tab));
        });
    })();
</script>
@endsection
