@extends('layouts.guru-dashboard')

@section('title', 'Dashboard Guru')

@section('content')
<style>
    /* ── Dashboard Layout ── */
    .dash { display: flex; flex-direction: column; gap: 16px; }

    .dash-header { margin-bottom: 2px; }
    .dash-header h1 { font-size: 22px; font-weight: 800; color: #0f172a; margin: 0; }
    .dash-header p { font-size: 13px; color: #64748b; margin: 4px 0 0; }
    .dash-header .badge {
        display: inline-block;
        background: linear-gradient(135deg, #2563eb, #7c3aed);
        color: #fff;
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
        margin-top: 6px;
    }

    /* ── Stat Cards ── */
    .stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
    .stat-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 18px 20px;
        box-shadow: 0 2px 8px rgba(15,23,42,0.04);
        position: relative;
        overflow: hidden;
    }
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
    }
    .stat-card:nth-child(1)::before { background: linear-gradient(90deg, #2563eb, #60a5fa); }
    .stat-card:nth-child(2)::before { background: linear-gradient(90deg, #10b981, #34d399); }
    .stat-card:nth-child(3)::before { background: linear-gradient(90deg, #8b5cf6, #a78bfa); }
    .stat-card:nth-child(4)::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
    .stat-label { font-size: 12px; color: #64748b; margin-bottom: 8px; font-weight: 500; }
    .stat-value { font-size: 28px; font-weight: 800; color: #0f172a; line-height: 1; }
    .stat-note { font-size: 11px; color: #94a3b8; margin-top: 6px; }
    .stat-icon {
        position: absolute;
        top: 16px; right: 16px;
        width: 38px; height: 38px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 17px;
    }
    .stat-icon.blue   { background: #eff6ff; color: #2563eb; }
    .stat-icon.green  { background: #ecfdf5; color: #10b981; }
    .stat-icon.purple { background: #f5f3ff; color: #8b5cf6; }
    .stat-icon.amber  { background: #fffbeb; color: #f59e0b; }

    /* ── Panel ── */
    .panel-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(15,23,42,0.04);
    }
    .panel-title {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .panel-title .emoji { font-size: 18px; }

    /* ── Kelas Grid ── */
    .kelas-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 12px; }
    .kelas-card {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 16px;
        background: #f8fafc;
        transition: all .2s;
    }
    .kelas-card:hover {
        border-color: #2563eb;
        box-shadow: 0 4px 12px rgba(37,99,235,0.12);
        transform: translateY(-2px);
    }
    .kelas-card h4 { margin: 0 0 4px; font-size: 14px; font-weight: 700; color: #0f172a; }
    .kelas-card .kelas-count { font-size: 12px; color: #64748b; margin-bottom: 10px; }
    .kelas-btn {
        display: inline-block;
        background: linear-gradient(135deg, #2563eb, #3b82f6);
        color: #fff;
        border-radius: 6px;
        padding: 6px 12px;
        font-size: 11px;
        font-weight: 600;
        text-decoration: none;
        transition: all .2s;
    }
    .kelas-btn:hover {
        box-shadow: 0 4px 10px rgba(37,99,235,0.3);
        transform: translateY(-1px);
    }
    .kelas-empty {
        text-align: center;
        padding: 24px;
        color: #94a3b8;
        font-size: 13px;
    }

    /* ── Tables ── */
    .dash-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .dash-table th {
        text-align: left;
        padding: 10px 12px;
        font-size: 11px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
    }
    .dash-table td {
        padding: 10px 12px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
    }
    .dash-table tbody tr:hover { background: #f8fafc; }

    /* ── Rank Badge ── */
    .rank-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px; height: 28px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
    }
    .rank-1 { background: linear-gradient(135deg, #fbbf24, #f59e0b); color: #fff; }
    .rank-2 { background: linear-gradient(135deg, #d1d5db, #9ca3af); color: #fff; }
    .rank-3 { background: linear-gradient(135deg, #d97706, #b45309); color: #fff; }
    .rank-n { background: #f1f5f9; color: #64748b; }

    .nilai-badge {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 12px;
    }
    .nilai-high { background: #ecfdf5; color: #059669; }
    .nilai-low  { background: #fef2f2; color: #dc2626; }

    /* ── Alert Rawan ── */
    .alert-rawan {
        background: #fef2f2;
        border: 1px solid #fecaca;
        border-radius: 10px;
        padding: 12px 16px;
        font-size: 13px;
        color: #991b1b;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* ── Grid Layouts ── */
    .grid-2 { display: grid; grid-template-columns: 1.2fr 1fr; gap: 16px; }

    /* ── Chart Container ── */
    .chart-container { position: relative; width: 100%; height: 260px; }

    /* ── Quick Actions ── */
    .quick-actions { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 4px; }
    .qa-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 16px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #334155;
        transition: all .2s;
    }
    .qa-btn:hover {
        background: #2563eb;
        color: #fff;
        border-color: #2563eb;
        box-shadow: 0 4px 12px rgba(37,99,235,0.3);
        transform: translateY(-1px);
    }

    .no-data {
        text-align: center;
        padding: 24px;
        color: #94a3b8;
        font-size: 13px;
    }

    @media (max-width: 1100px) {
        .stat-grid { grid-template-columns: repeat(2, 1fr); }
        .grid-2 { grid-template-columns: 1fr; }
    }
    @media (max-width: 640px) {
        .stat-grid { grid-template-columns: 1fr; }
        .kelas-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="dash">
    {{-- Header --}}
    <div class="dash-header">
        <h1>Selamat Datang, {{ $user?->guru?->nama_guru ?? $user?->username }}</h1>
        <p>Dashboard guru — ringkasan kelas perwalian dan data akademik.</p>
        @if($activeTahunAjaran)
            <span class="badge">{{ $activeTahunAjaran->nama_tahun_ajaran }} — Semester {{ $semesterLabel }}</span>
        @endif
    </div>

    {{-- Profil Sekolah Singkat --}}
    <div class="panel-card" style="padding: 16px 20px; display: flex; flex-wrap: wrap; gap: 20px; align-items: flex-start; justify-content: space-between; margin-bottom: 4px;">
        <div style="flex: 1; min-width: 250px;">
            <div class="panel-title" style="margin-bottom: 10px;"><span class="emoji">🏫</span> Informasi Singkat</div>
            <div style="display: flex; flex-direction: column; gap: 8px; font-size: 12px; color: #475569;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="width:24px;height:24px;background:#f1f5f9;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:14px;color:#4f46e5;">👤</span> 
                    <strong>Kepala Sekolah:</strong> Dayat
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="width:24px;height:24px;background:#f1f5f9;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:14px;color:#4f46e5;">📖</span> 
                    <strong>Kurikulum:</strong> SD Merdeka
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="width:24px;height:24px;background:#f1f5f9;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:14px;color:#4f46e5;">📅</span> 
                    <strong>Berdiri:</strong> 18 Februari 1974
                </div>
            </div>
        </div>
        <div style="flex: 2; min-width: 300px; border-left: 1px solid #e2e8f0; padding-left: 20px;">
            <div class="panel-title" style="margin-bottom: 6px; font-size: 13px;">Visi & Misi SDN Cicaidas</div>
            <p style="margin: 0 0 6px; font-size: 12px; color: #475569; line-height: 1.5;">
                <strong style="color:#0f172a;">Visi:</strong> Menjadi sekolah dasar unggul dalam prestasi akademik & non-akademik, berbasis teknologi, serta membentuk karakter siswa berakhlak mulia.
            </p>
            <p style="margin: 0; font-size: 12px; color: #475569; line-height: 1.5;">
                <strong style="color:#0f172a;">Misi Singkat:</strong> Menyelenggarakan pendidikan berkualitas, mengembangkan potensi minat bakat, memanfaatkan TI, serta menciptakan lingkungan sekolah aman dan kondusif.
            </p>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-icon blue">🏫</div>
            <div class="stat-label">Kelas Perwalian</div>
            <div class="stat-value">{{ $kelas->count() }}</div>
            <div class="stat-note">Sebagai wali kelas</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green">👨‍🎓</div>
            <div class="stat-label">Total Siswa</div>
            <div class="stat-value">{{ $totalSiswaPerwalian }}</div>
            <div class="stat-note">Siswa perwalian</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon purple">🧠</div>
            <div class="stat-label">Sudah Dianalisis</div>
            <div class="stat-value">{{ $totalAnalyzed }} <span style="font-size:14px;color:#94a3b8;font-weight:500;">/ {{ $totalSiswaPerwalian }}</span></div>
            <div class="stat-note">Rekomendasi AI</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon amber">📋</div>
            <div class="stat-label">Observasi Non-Akademik</div>
            <div class="stat-value">{{ $totalObservasi }} <span style="font-size:14px;color:#94a3b8;font-weight:500;">/ {{ $totalSiswaPerwalian }}</span></div>
            <div class="stat-note">Sudah diisi</div>
        </div>
    </div>

    {{-- Daftar Kelas Perwalian --}}
    <div class="panel-card">
        <div class="panel-title"><span class="emoji">📚</span> Daftar Kelas Perwalian</div>
        <div class="kelas-grid">
            @forelse($kelas as $k)
                <div class="kelas-card">
                    <h4>{{ $k->nama_kelas }}</h4>
                    <div class="kelas-count">{{ $k->siswas_count }} Siswa</div>
                    <a class="kelas-btn" href="{{ route('guru.kelas.show', $k) }}">Lihat Detail</a>
                </div>
            @empty
                <div class="kelas-empty">Anda belum ditetapkan sebagai wali kelas.</div>
            @endforelse
        </div>
    </div>

    {{-- Ranking 10 Siswa Terbaik --}}
    <div class="panel-card">
        <div class="panel-title"><span class="emoji">🏆</span> Ranking 10 Siswa Terbaik</div>
        @if($dataPeriodeLabel)
            <div style="font-size:12px;color:#64748b;margin:-8px 0 12px;padding-left:26px;">Data dari: {{ $dataPeriodeLabel }}</div>
        @endif
        @if($topRanking->isNotEmpty())
            <table class="dash-table">
                <thead>
                    <tr>
                        <th style="width:60px;">Rank</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th style="width:120px;">Rata-rata</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topRanking as $i => $row)
                        <tr>
                            <td>
                                <span class="rank-badge {{ $i === 0 ? 'rank-1' : ($i === 1 ? 'rank-2' : ($i === 2 ? 'rank-3' : 'rank-n')) }}">
                                    {{ $i + 1 }}
                                </span>
                            </td>
                            <td style="font-weight:600;">{{ $row->nama_siswa }}</td>
                            <td>{{ $row->nama_kelas }}</td>
                            <td>
                                <span class="nilai-badge nilai-high">{{ $row->rata_rata }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">Belum ada data nilai.</div>
        @endif
    </div>

    {{-- Siswa Rawan --}}
    @if($siswaRawan->isNotEmpty())
        <div class="panel-card">
            <div class="panel-title"><span class="emoji">🚨</span> Siswa Rawan — Nilai di Bawah KKM</div>
        @if($dataPeriodeLabel)
            <div style="font-size:12px;color:#64748b;margin:-8px 0 12px;padding-left:26px;">Data dari: {{ $dataPeriodeLabel }}</div>
        @endif
            <div class="alert-rawan" style="margin-bottom:14px;">
                <span>⚠️</span>
                <span><strong>{{ $siswaRawan->groupBy('nama_siswa')->count() }} siswa</strong> memiliki nilai di bawah KKM. Perlu perhatian segera!</span>
            </div>
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Mata Pelajaran</th>
                        <th style="width:80px;">Nilai</th>
                        <th style="width:80px;">KKM</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($siswaRawan as $row)
                        <tr>
                            <td style="font-weight:600;">{{ $row->nama_siswa }}</td>
                            <td>{{ $row->nama_kelas }}</td>
                            <td>{{ $row->nama_mapel }}</td>
                            <td><span class="nilai-badge nilai-low">{{ $row->nilai_akhir }}</span></td>
                            <td style="color:#64748b;">{{ $row->kkm }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Row: Chart Rata-rata per Mapel + Riwayat Analisis AI --}}
    <div class="grid-2">
        <div class="panel-card">
            <div class="panel-title"><span class="emoji">📊</span> Rata-rata Nilai per Mata Pelajaran</div>
        @if($dataPeriodeLabel)
            <div style="font-size:12px;color:#64748b;margin:-8px 0 12px;padding-left:26px;">Data dari: {{ $dataPeriodeLabel }}</div>
        @endif
            @if($rataMapel->isNotEmpty())
                <div class="chart-container">
                    <canvas id="chartRataMapel"></canvas>
                </div>
            @else
                <div class="no-data">Belum ada data nilai.</div>
            @endif
        </div>
        <div class="panel-card">
            <div class="panel-title"><span class="emoji">🤖</span> Riwayat Analisis AI Terbaru</div>
            @if($riwayatAnalisis->isNotEmpty())
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Nama Siswa</th>
                            <th>Minat Utama</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($riwayatAnalisis as $r)
                            <tr>
                                <td style="font-weight:600;">{{ $r->siswa?->nama_siswa ?? '-' }}</td>
                                <td>{{ $r->minat_utama ?? '-' }}</td>
                                <td style="color:#64748b;">{{ $r->tgl_analisis ? \Carbon\Carbon::parse($r->tgl_analisis)->format('d M Y') : '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="no-data">Belum ada riwayat analisis AI.</div>
            @endif
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="panel-card">
        <div class="panel-title"><span class="emoji">⚡</span> Aksi Cepat</div>
        <div class="quick-actions">
            <a href="{{ route('guru.penilaian.index') }}" class="qa-btn">📝 Input Penilaian</a>
            <a href="{{ route('guru.siswa.index') }}" class="qa-btn">👨‍🎓 Data Siswa</a>
            <a href="{{ route('guru.analisis.index') }}" class="qa-btn">🧠 Riwayat Analisis</a>
            <a href="{{ route('guru.kenaikan-kelas.index') }}" class="qa-btn">🎓 Kenaikan Kelas</a>
        </div>
    </div>
</div>

{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
    @if($rataMapel->isNotEmpty())
    const COLORS = ['#2563eb','#10b981','#f59e0b','#ef4444','#8b5cf6','#ec4899','#06b6d4','#f97316','#14b8a6','#6366f1','#84cc16','#e11d48'];

    new Chart(document.getElementById('chartRataMapel'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($rataMapel->pluck('nama_mapel')->values()) !!},
            datasets: [{
                label: 'Rata-rata Nilai',
                data: {!! json_encode($rataMapel->pluck('rata_rata')->values()) !!},
                backgroundColor: COLORS.slice(0, {{ $rataMapel->count() }}),
                borderRadius: 6,
                maxBarThickness: 44,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, max: 100, ticks: { font: { size: 11 } }, grid: { color: '#f1f5f9' } },
                x: { ticks: { font: { size: 10 }, maxRotation: 45 }, grid: { display: false } }
            }
        }
    });
    @endif
</script>
@endsection
