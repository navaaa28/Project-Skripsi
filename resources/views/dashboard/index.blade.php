@extends('layouts.admin-dashboard')

@section('title', 'Dashboard')

@section('content')
<style>
    /* ── Dashboard Layout ── */
    .dash { display: flex; flex-direction: column; gap: 16px; }

    .dash-header { margin-bottom: 2px; }
    .dash-header h1 { font-size: 22px; font-weight: 800; color: #0f172a; margin: 0; }
    .dash-header p { font-size: 13px; color: #64748b; margin: 4px 0 0; }
    .dash-header .badge {
        display: inline-block;
        background: linear-gradient(135deg, #3b82f6, #6366f1);
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
    .stat-card:nth-child(1)::before { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
    .stat-card:nth-child(2)::before { background: linear-gradient(90deg, #10b981, #34d399); }
    .stat-card:nth-child(3)::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
    .stat-card:nth-child(4)::before { background: linear-gradient(90deg, #8b5cf6, #a78bfa); }
    .stat-label { font-size: 12px; color: #64748b; margin-bottom: 8px; font-weight: 500; }
    .stat-value { font-size: 30px; font-weight: 800; color: #0f172a; line-height: 1; }
    .stat-note { font-size: 11px; color: #94a3b8; margin-top: 6px; }
    .stat-icon {
        position: absolute;
        top: 16px; right: 16px;
        width: 40px; height: 40px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px;
    }
    .stat-icon.blue   { background: #eff6ff; color: #3b82f6; }
    .stat-icon.green  { background: #ecfdf5; color: #10b981; }
    .stat-icon.amber  { background: #fffbeb; color: #f59e0b; }
    .stat-icon.purple { background: #f5f3ff; color: #8b5cf6; }

    /* ── Alert Banners ── */
    .alert-banner {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 500;
    }
    .alert-banner.warning {
        background: #fffbeb;
        border: 1px solid #fde68a;
        color: #92400e;
    }
    .alert-banner.danger {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
    }
    .alert-banner .alert-icon { font-size: 18px; flex-shrink: 0; }

    /* ── Panels / Cards ── */
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

    /* ── Grid Layouts ── */
    .grid-2 { display: grid; grid-template-columns: 1.4fr 1fr; gap: 16px; }
    .grid-2-equal { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }

    /* ── Chart Container ── */
    .chart-container { position: relative; width: 100%; }
    .chart-container.bar { height: 260px; }
    .chart-container.donut { height: 240px; }
    .chart-container.horizontal { height: 280px; }

    /* ── Bottom Stats Row ── */
    .bottom-stat {
        text-align: center;
        padding: 16px;
    }
    .bottom-stat .bs-label { font-size: 12px; color: #64748b; margin-bottom: 6px; }
    .bottom-stat .bs-value { font-size: 26px; font-weight: 800; color: #0f172a; }
    .bottom-stat .bs-sub { font-size: 11px; color: #94a3b8; margin-top: 4px; }
    .progress-bar-wrap {
        background: #e2e8f0;
        border-radius: 6px;
        height: 8px;
        margin-top: 10px;
        overflow: hidden;
    }
    .progress-bar-fill {
        height: 100%;
        border-radius: 6px;
        background: linear-gradient(90deg, #3b82f6, #6366f1);
        transition: width 1s ease;
    }

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
        background: #3b82f6;
        color: #fff;
        border-color: #3b82f6;
        box-shadow: 0 4px 12px rgba(59,130,246,0.3);
        transform: translateY(-1px);
    }

    @media (max-width: 1100px) {
        .stat-grid { grid-template-columns: repeat(2, 1fr); }
        .grid-2, .grid-2-equal, .grid-3 { grid-template-columns: 1fr; }
    }
    @media (max-width: 640px) {
        .stat-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="dash">
    {{-- Header --}}
    <div class="dash-header">
        <h1>Dashboard Overview</h1>
        <p>Ringkasan data utama sekolah.</p>
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
            <div class="stat-icon blue">👨‍🎓</div>
            <div class="stat-label">Total Siswa</div>
            <div class="stat-value">{{ $totalStudents }}</div>
            <div class="stat-note">Terdaftar aktif</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green">👨‍🏫</div>
            <div class="stat-label">Total Guru</div>
            <div class="stat-value">{{ $totalTeachers }}</div>
            <div class="stat-note">Aktif mengajar</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon amber">🏫</div>
            <div class="stat-label">Total Kelas</div>
            <div class="stat-value">{{ $totalClasses }}</div>
            <div class="stat-note">Rombongan belajar</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon purple">📚</div>
            <div class="stat-label">Total Mapel</div>
            <div class="stat-value">{{ $totalMapel }}</div>
            <div class="stat-note">Mata pelajaran</div>
        </div>
    </div>

    {{-- Alert Banners --}}
    @if($kelasTanpaWali->isNotEmpty())
        <div class="alert-banner warning">
            <span class="alert-icon">⚠️</span>
            <span><strong>{{ $kelasTanpaWali->count() }} Kelas</strong> belum memiliki Wali Kelas: {{ $kelasTanpaWali->pluck('nama_kelas')->join(', ') }}</span>
        </div>
    @endif
    @if($siswaRawanCount > 0)
        <div class="alert-banner danger">
            <span class="alert-icon">🚨</span>
            <span><strong>{{ $siswaRawanCount }} Siswa</strong> memiliki nilai di bawah KKM pada semester ini.</span>
        </div>
    @endif

    {{-- Row 1: Distribusi Kelas + Rasio JK --}}
    <div class="grid-2">
        <div class="panel-card">
            <div class="panel-title"><span class="emoji">📊</span> Distribusi Siswa per Kelas</div>
            <div class="chart-container bar">
                <canvas id="chartDistribusiKelas"></canvas>
            </div>
        </div>
        <div class="panel-card">
            <div class="panel-title"><span class="emoji">👥</span> Rasio Jenis Kelamin</div>
            <div class="chart-container donut">
                <canvas id="chartRasioJK"></canvas>
            </div>
        </div>
    </div>

    {{-- Row 2: Rata-rata Nilai per Mapel --}}
    <div class="panel-card">
        <div class="panel-title"><span class="emoji">📈</span> Rata-rata Nilai Akhir per Mata Pelajaran</div>
        <div class="chart-container horizontal">
            <canvas id="chartRataMapel"></canvas>
        </div>
    </div>

    {{-- Row 3: Distribusi Minat AI + Angkatan --}}
    <div class="grid-2-equal">
        <div class="panel-card">
            <div class="panel-title"><span class="emoji">🧠</span> Distribusi Minat Siswa (Hasil AI)</div>
            <div class="chart-container donut">
                <canvas id="chartMinat"></canvas>
            </div>
        </div>
        <div class="panel-card">
            <div class="panel-title"><span class="emoji">📅</span> Statistik Siswa per Angkatan</div>
            <div class="chart-container bar">
                <canvas id="chartAngkatan"></canvas>
            </div>
        </div>
    </div>

    {{-- Row 4: Bottom Stats + Quick Actions --}}
    <div class="grid-3">
        <div class="panel-card bottom-stat">
            <div class="bs-label">Status Analisis AI</div>
            <div class="bs-value">{{ $analysisPercent }}%</div>
            <div class="bs-sub">{{ $totalAnalyzed }} dari {{ $totalStudents }} siswa sudah dianalisis</div>
            <div class="progress-bar-wrap">
                <div class="progress-bar-fill" style="width: {{ $analysisPercent }}%;"></div>
            </div>
        </div>
        <div class="panel-card bottom-stat">
            <div class="bs-label">Progress Kenaikan Kelas</div>
            <div class="bs-value">{{ $kenaikanProcessed }} / {{ $kenaikanTotal }}</div>
            <div class="bs-sub">Siswa sudah diproses kenaikan kelasnya</div>
            @php $kenaikanPercent = $kenaikanTotal > 0 ? round(($kenaikanProcessed / $kenaikanTotal) * 100) : 0; @endphp
            <div class="progress-bar-wrap">
                <div class="progress-bar-fill" style="width: {{ $kenaikanPercent }}%;"></div>
            </div>
        </div>
        <div class="panel-card">
            <div class="panel-title"><span class="emoji">⚡</span> Aksi Cepat</div>
            <div class="quick-actions">
                <a href="{{ route('admin.tahun-ajaran.index') }}" class="qa-btn">📆 Kelola Tahun Ajaran</a>
                <a href="{{ route('admin.siswa.index') }}" class="qa-btn">👨‍🎓 Kelola Siswa</a>
                <a href="{{ route('admin.guru.index') }}" class="qa-btn">👨‍🏫 Kelola Guru</a>
                <a href="{{ route('admin.kelas.index') }}" class="qa-btn">🏫 Kelola Kelas</a>
                <a href="{{ route('admin.kenaikan-kelas.index') }}" class="qa-btn">🎓 Kenaikan Kelas</a>
            </div>
        </div>
    </div>
</div>

{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
    const COLORS = ['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#ec4899','#06b6d4','#f97316','#14b8a6','#6366f1','#84cc16','#e11d48'];

    // ── Distribusi Siswa per Kelas ──
    new Chart(document.getElementById('chartDistribusiKelas'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($distribusiKelas->pluck('nama')->values()) !!},
            datasets: [{
                label: 'Jumlah Siswa',
                data: {!! json_encode($distribusiKelas->pluck('total')->values()) !!},
                backgroundColor: COLORS.slice(0, {{ $distribusiKelas->count() }}),
                borderRadius: 6,
                maxBarThickness: 48,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0, font: { size: 11 } }, grid: { color: '#f1f5f9' } },
                x: { ticks: { font: { size: 10 } }, grid: { display: false } }
            }
        }
    });

    // ── Rasio Jenis Kelamin ──
    new Chart(document.getElementById('chartRasioJK'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode(array_map(fn($k) => $k === 'L' ? 'Laki-laki' : 'Perempuan', array_keys($rasioJK->toArray()))) !!},
            datasets: [{
                data: {!! json_encode(array_values($rasioJK->toArray())) !!},
                backgroundColor: ['#3b82f6', '#ec4899'],
                borderWidth: 0,
                hoverOffset: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true, font: { size: 12 } } }
            }
        }
    });

    // ── Rata-rata Nilai per Mapel ──
    new Chart(document.getElementById('chartRataMapel'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($rataMapel->pluck('nama_mapel')->values()) !!},
            datasets: [{
                label: 'Rata-rata Nilai',
                data: {!! json_encode($rataMapel->pluck('rata_rata')->values()) !!},
                backgroundColor: '#6366f1',
                borderRadius: 4,
                barThickness: 22,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { beginAtZero: true, max: 100, ticks: { font: { size: 11 } }, grid: { color: '#f1f5f9' } },
                y: { ticks: { font: { size: 11 } }, grid: { display: false } }
            }
        }
    });

    // ── Distribusi Minat AI ──
    @if($distribusiMinat->isNotEmpty())
    new Chart(document.getElementById('chartMinat'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode(array_keys($distribusiMinat->toArray())) !!},
            datasets: [{
                data: {!! json_encode(array_values($distribusiMinat->toArray())) !!},
                backgroundColor: COLORS.slice(0, {{ $distribusiMinat->count() }}),
                borderWidth: 0,
                hoverOffset: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: { position: 'bottom', labels: { padding: 12, usePointStyle: true, font: { size: 11 } } }
            }
        }
    });
    @else
    document.getElementById('chartMinat').parentElement.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;color:#94a3b8;font-size:13px;">Belum ada data analisis AI.</div>';
    @endif

    // ── Statistik Angkatan ──
    new Chart(document.getElementById('chartAngkatan'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($angkatanStats->pluck('angkatan')->values()) !!},
            datasets: [{
                label: 'Jumlah Siswa',
                data: {!! json_encode($angkatanStats->pluck('total')->values()) !!},
                backgroundColor: 'rgba(59,130,246,0.7)',
                borderColor: '#3b82f6',
                borderWidth: 1,
                borderRadius: 6,
                maxBarThickness: 48,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0, font: { size: 11 } }, grid: { color: '#f1f5f9' } },
                x: { ticks: { font: { size: 11 } }, grid: { display: false } }
            }
        }
    });
</script>
@endsection
