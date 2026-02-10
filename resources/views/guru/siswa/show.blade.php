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
                        <td>{{ $n->nilai_akhir ?? '-' }}</td>
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
    <div class="section-title">Hasil Analisis Minat & Bakat</div>
    @if (!$rekomendasi)
        <div class="muted">Belum ada hasil analisis untuk siswa ini.</div>
    @else
        <div class="muted" style="margin-bottom:10px;">Semester: {{ $rekomendasi->semester }} · Tanggal: {{ $rekomendasi->tgl_analisis }}</div>
        <div class="info-grid">
            <div>
                <div class="section-title">Minat (Top 3)</div>
                @if (!empty($rekomendasi->minat_json))
                    @foreach ($rekomendasi->minat_json as $item)
                        <span class="pill">
                            {{ $item['nama'] ?? '-' }}
                            <span class="pct">{{ $item['persentase'] ?? '-' }}%</span>
                        </span>
                    @endforeach
                @elseif ($rekomendasi->minat_utama)
                    <span class="pill">{{ $rekomendasi->minat_utama }}</span>
                @else
                    <div class="muted">Tidak ada data.</div>
                @endif
            </div>
            <div>
                <div class="section-title">Bakat (Top 3)</div>
                @if (!empty($rekomendasi->bakat_json))
                    @foreach ($rekomendasi->bakat_json as $item)
                        <span class="pill">
                            {{ $item['nama'] ?? '-' }}
                            <span class="pct">{{ $item['persentase'] ?? '-' }}%</span>
                        </span>
                    @endforeach
                @elseif ($rekomendasi->bakat_potensial)
                    <span class="pill">{{ $rekomendasi->bakat_potensial }}</span>
                @else
                    <div class="muted">Tidak ada data.</div>
                @endif
            </div>
        </div>
        <div style="margin-top:10px;">
            <div class="section-title">Analisis Tren</div>
            <div class="muted">{{ $rekomendasi->analisis_tren ?? '-' }}</div>
        </div>
        <div style="margin-top:10px;">
            <div class="section-title">Ringkasan Non-Akademik</div>
            <div class="muted">{{ $rekomendasi->ringkasan_non_akademik ?? '-' }}</div>
        </div>
        <div style="margin-top:10px;">
            <div class="section-title">Saran Pengembangan</div>
            <div class="muted">{{ $rekomendasi->saran_pengembangan ?? '-' }}</div>
        </div>
    @endif
</div>

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
