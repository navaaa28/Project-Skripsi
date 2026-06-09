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
    .filter-form {
        display: flex;
        flex-wrap: wrap;
        align-items: end;
        gap: 10px;
        margin: 2px 0 12px;
    }
    .filter-group { display: flex; flex-direction: column; gap: 4px; min-width: 160px; }
    .filter-group label { font-size: 11px; color: #6b7280; font-weight: 600; text-transform: uppercase; }
    .filter-group select {
        border: 1px solid #d1d5db;
        border-radius: 6px;
        padding: 6px 8px;
        font-size: 12px;
        background: #fff;
        color: #1f2937;
    }
    .filter-actions { display: flex; gap: 8px; }
    .btn-filter {
        padding: 6px 10px;
        border-radius: 6px;
        border: 1px solid #2563eb;
        background: #2563eb;
        color: #fff;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
    }
    .btn-filter-reset {
        padding: 6px 10px;
        border-radius: 6px;
        border: 1px solid #d1d5db;
        background: #fff;
        color: #374151;
        font-size: 12px;
        text-decoration: none;
    }
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
        <a href="{{ route('guru.siswa.dokumen', $siswa->id_user) }}" style="margin-left:auto; text-decoration:none; background:#2563eb; color:white; padding:8px 12px; border-radius:6px; font-size:12px; font-weight:600;">
            Lihat Dokumen
        </a>
    </div>
</div>

<div class="panel">
    <form method="GET" action="{{ route('guru.siswa.show', $siswa) }}" class="filter-form">
        <div class="filter-group">
            <label for="kelas">Kelas</label>
            <select name="kelas" id="kelas">
                <option value="">Semua kelas</option>
                @foreach ($kelasOptions as $kelas)
                    <option value="{{ $kelas->id_kelas }}" {{ (string) $selectedKelas === (string) $kelas->id_kelas ? 'selected' : '' }}>
                        {{ $kelas->nama_kelas }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <label for="semester">Semester</label>
            <select name="semester" id="semester">
                <option value="">Semua semester</option>
                @foreach ($semesterOptions as $semester)
                    <option value="{{ $semester }}" {{ (string) $selectedSemester === (string) $semester ? 'selected' : '' }}>
                        Semester {{ $semester }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <label for="tahun_ajaran">Tahun Ajaran</label>
            <select name="tahun_ajaran" id="tahun_ajaran">
                <option value="">Semua tahun ajaran</option>
                @foreach ($tahunAjaranOptions as $ta)
                    <option value="{{ $ta->id_tahun_ajaran }}" {{ (string) $selectedTahunAjaran === (string) $ta->id_tahun_ajaran ? 'selected' : '' }}>
                        {{ $ta->nama_tahun_ajaran }} @if($ta->is_active) (Aktif) @endif
                    </option>
                @endforeach
            </select>
        </div>
        <div class="filter-actions">
            <button type="submit" class="btn-filter">Terapkan</button>
            <a href="{{ route('guru.siswa.show', $siswa) }}" class="btn-filter-reset">Reset</a>
        </div>
    </form>

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
                    <th style="width:80px;">KKM</th>
                    <th>Rincian UH</th>
                    <th>Nilai Akhir</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($nilai as $n)
                    @php
                        $kkm = $n->mapel?->kkm ?? 75;
                        $belowKkm = $n->nilai_akhir !== null && $n->nilai_akhir < $kkm;
                    @endphp
                    <tr class="{{ $belowKkm ? 'row-below-kkm' : '' }}">
                        <td>{{ $n->semester }}</td>
                        <td>{{ $n->mapel?->nama_mapel ?? '-' }}</td>
                        <td style="color:#6b7280;">{{ $kkm }}</td>
                        <td>
                            @if(!empty($n->detail_uh))
                                <span style="font-size:11px; color:#6b7280;">{{ implode(', ', $n->detail_uh) }}</span>
                                <br>
                                <span style="font-size:10px; font-weight:600; color:#374151;">Rata-rata: {{ round($n->nilai_uh, 1) }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <span class="nilai-num {{ $belowKkm ? 'nilai-below' : 'nilai-ok' }}">
                                {{ $n->nilai_akhir !== null ? number_format($n->nilai_akhir, 1) : '-' }}
                            </span>
                            @if ($belowKkm)
                                <span class="badge-kkm">Di bawah KKM</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">Belum ada nilai.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div id="tab-grafik" style="display:none;">
        <div style="font-weight:700; font-size:13px; margin-bottom:8px;">Tren Nilai Akademik</div>
        <div class="muted" style="margin-bottom: 8px;">
            Ringkasan ini membantu guru menjelaskan poin utama perkembangan nilai kepada orang tua.
        </div>
        <div class="chart-toolbar">
            <label for="focusMapel">Fokus mapel:</label>
            <select id="focusMapel" class="chart-select">
                <option value="__all__">Semua mapel</option>
            </select>
            <span class="muted">Arahkan kursor ke titik nilai untuk melihat perubahan per semester.</span>
        </div>
        <div id="grafikSummary" class="chart-summary"></div>
        <canvas id="nilaiChart" height="120"></canvas>
        <div class="muted" style="margin-top: 6px;">
            Skala nilai menyesuaikan rentang data siswa agar perubahan kecil lebih mudah terbaca.
        </div>
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

        @if ($rekomendasi->tips_peningkatan)
            <div class="catatan-block" style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 14px; margin-top: 14px;">
                <div class="catatan-title" style="color: #166534;">💡 Tips Peningkatan</div>
                <ul class="catatan-list">
                    @foreach (array_filter(array_map('trim', preg_split('/(?<=\.)\s+/', $rekomendasi->tips_peningkatan))) as $sentence)
                        <li>{{ $sentence }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    @endif
</div>

<style>
    /* ---- KKM Indicators ---- */
    .row-below-kkm { background: #fff5f5; }
    .row-below-kkm:hover { background: #fee2e2 !important; }
    .nilai-num { font-weight: 700; }
    .nilai-below { color: #dc2626; }
    .nilai-ok { color: #16a34a; }
    .badge-kkm {
        display: inline-block;
        margin-left: 6px;
        padding: 1px 7px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 700;
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fca5a5;
        vertical-align: middle;
        white-space: nowrap;
    }

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
    .chart-toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px 10px;
        margin-bottom: 10px;
        font-size: 12px;
    }
    .chart-select {
        border: 1px solid #d1d5db;
        border-radius: 6px;
        padding: 5px 8px;
        font-size: 12px;
        background: #fff;
        color: #1f2937;
    }
    .chart-summary {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: #fafafa;
        padding: 10px 12px;
        margin-bottom: 10px;
        font-size: 12px;
        color: #374151;
        display: grid;
        gap: 4px;
    }
    .summary-badge {
        display: inline-flex;
        align-items: center;
        padding: 2px 8px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        width: fit-content;
    }
    .summary-up { background: #dcfce7; color: #166534; }
    .summary-down { background: #fee2e2; color: #991b1b; }
    .summary-stable { background: #e5e7eb; color: #374151; }

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
        const focusMapel = document.getElementById('focusMapel');
        const grafikSummary = document.getElementById('grafikSummary');

        const colors = [
            '#ef4444', '#2563eb', '#16a34a', '#f59e0b', '#8b5cf6', '#0ea5e9',
            '#14b8a6', '#f97316', '#84cc16', '#ec4899'
        ];

        function toNumber(value) {
            const n = Number(value);
            return Number.isFinite(n) ? n : null;
        }

        function pickFirstLast(arr) {
            const valid = arr.map(toNumber).filter(v => v !== null);
            if (!valid.length) return { first: null, last: null };
            return { first: valid[0], last: valid[valid.length - 1] };
        }

        function deltaLabel(delta) {
            if (delta > 0) return `naik +${delta.toFixed(1)}`;
            if (delta < 0) return `turun ${delta.toFixed(1)}`;
            return 'stabil';
        }

        function rgbaFromHex(hex, alpha) {
            const m = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
            if (!m) return hex;
            const r = parseInt(m[1], 16);
            const g = parseInt(m[2], 16);
            const b = parseInt(m[3], 16);
            return `rgba(${r}, ${g}, ${b}, ${alpha})`;
        }

        const datasets = Object.keys(series).map((name, idx) => {
            const data = labels.map(s => (series[name] && series[name][s] !== undefined) ? series[name][s] : null);
            return {
                label: name,
                data,
                borderColor: colors[idx % colors.length],
                backgroundColor: colors[idx % colors.length],
                pointRadius: 4,
                pointHoverRadius: 5,
                borderWidth: 2,
                tension: 0.3,
                spanGaps: true,
                originalColor: colors[idx % colors.length]
            };
        });

        const allValues = datasets.flatMap(ds => ds.data).map(toNumber).filter(v => v !== null);
        const minValue = allValues.length ? Math.min(...allValues) : 0;
        const maxValue = allValues.length ? Math.max(...allValues) : 100;
        const yMin = Math.max(0, Math.floor((minValue - 5) / 5) * 5);
        const yMax = Math.min(100, Math.ceil((maxValue + 5) / 5) * 5);

        function renderSummary() {
            if (!grafikSummary) return;
            if (labels.length < 2 || !datasets.length) {
                grafikSummary.innerHTML = '<div class="muted">Butuh minimal 2 semester untuk membaca tren naik/turun.</div>';
                return;
            }

            let up = 0;
            let down = 0;
            let stable = 0;
            let best = null;
            let worst = null;

            datasets.forEach(ds => {
                const { first, last } = pickFirstLast(ds.data);
                if (first === null || last === null) return;
                const delta = Number((last - first).toFixed(1));
                if (delta > 0) up++;
                else if (delta < 0) down++;
                else stable++;

                if (!best || delta > best.delta) best = { label: ds.label, delta };
                if (!worst || delta < worst.delta) worst = { label: ds.label, delta };
            });

            const parts = [];
            parts.push(`<div><span class="summary-badge summary-up">Naik: ${up}</span> <span class="summary-badge summary-down">Turun: ${down}</span> <span class="summary-badge summary-stable">Stabil: ${stable}</span></div>`);
            if (best) {
                parts.push(`<div>Peningkatan terbaik: <strong>${best.label}</strong> (${deltaLabel(best.delta)}).</div>`);
            }
            if (worst) {
                parts.push(`<div>Perlu perhatian: <strong>${worst.label}</strong> (${deltaLabel(worst.delta)}).</div>`);
            }

            grafikSummary.innerHTML = parts.join('');
        }

        function applyFocus(value, chart) {
            chart.data.datasets.forEach(ds => {
                const focused = value === '__all__' || ds.label === value;
                ds.borderColor = focused ? ds.originalColor : rgbaFromHex(ds.originalColor, 0.2);
                ds.backgroundColor = focused ? ds.originalColor : rgbaFromHex(ds.originalColor, 0.2);
                ds.borderWidth = focused ? 3 : 1.5;
                ds.pointRadius = focused ? 4 : 2;
            });
            chart.update();
        }

        const kkmValue = 75; // KKM default
        const kkmDataset = {
            label: 'KKM (75)',
            data: labels.map(() => kkmValue),
            borderColor: '#ef4444',
            backgroundColor: 'transparent',
            borderWidth: 1.5,
            borderDash: [6, 4],
            pointRadius: 0,
            pointHoverRadius: 0,
            tension: 0,
            spanGaps: true,
            originalColor: '#ef4444',
        };

        const ctx = document.getElementById('nilaiChart');
        if (!ctx) return;
        const chart = new Chart(ctx, {
            type: 'line',
            data: { labels: labels.map(s => `Semester ${s}`), datasets: [...datasets, kkmDataset] },
            options: {
                responsive: true,
                interaction: { mode: 'nearest', intersect: false },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'line',
                            boxWidth: 18,
                            font: { size: 11 },
                            filter: (item) => item.text !== 'KKM (75)' || true,
                        }
                    },
                    tooltip: {
                        callbacks: {
                            afterLabel: function (context) {
                                if (context.dataset.label === 'KKM (75)') return '';
                                const dataset = context.dataset;
                                const { first, last } = pickFirstLast(dataset.data);
                                if (first === null || last === null || labels.length < 2) return '';
                                const delta = Number((last - first).toFixed(1));
                                return `Tren ${labels[0]} ke ${labels[labels.length - 1]}: ${deltaLabel(delta)}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        min: yMin,
                        max: yMax,
                        ticks: { stepSize: 5 },
                        grid: { color: '#f1f5f9' }
                    }
                }
            }
        });

        if (focusMapel) {
            Object.keys(series).forEach(name => {
                const option = document.createElement('option');
                option.value = name;
                option.textContent = name;
                focusMapel.appendChild(option);
            });
            focusMapel.addEventListener('change', function () {
                applyFocus(this.value, chart);
            });
        }

        renderSummary();

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
