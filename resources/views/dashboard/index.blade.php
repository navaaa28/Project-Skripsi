@extends('layouts.admin-dashboard')

@section('title', 'Dashboard')

@section('content')
<style>
    .admin-index-page { display: flex; flex-direction: column; gap: 14px; }
    .admin-index-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
    .admin-index-title { font-size: 22px; font-weight: 700; color: #0f172a; margin: 0; }
    .admin-index-subtitle { margin: 2px 0 0; font-size: 13px; color: #64748b; }

    .admin-index-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 14px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
    }
    .admin-stat-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
    }
    .admin-stat-card {
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        background: #f8fafc;
        padding: 14px;
    }
    .admin-stat-label { font-size: 12px; color: #64748b; margin-bottom: 6px; }
    .admin-stat-value { font-size: 32px; line-height: 1; font-weight: 700; color: #0f172a; margin-bottom: 6px; }
    .admin-stat-note { font-size: 12px; color: #64748b; }

    .admin-chart-title { margin: 0 0 10px; font-size: 16px; font-weight: 700; color: #0f172a; }
    .admin-chart-wrap {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 14px;
        background: #f8fafc;
        min-height: 220px;
    }
    .admin-chart-grid {
        height: 220px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(72px, 1fr));
        gap: 12px;
        align-items: end;
    }
    .admin-chart-col {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }
    .admin-chart-value {
        font-size: 12px;
        color: #334155;
        font-weight: 600;
    }
    .admin-chart-bar {
        width: 100%;
        max-width: 60px;
        border-radius: 8px 8px 0 0;
        background: linear-gradient(180deg, #3b82f6, #1d4ed8);
        min-height: 8px;
    }
    .admin-chart-label {
        font-size: 12px;
        color: #64748b;
    }

    @media (max-width: 900px) {
        .admin-index-toolbar { align-items: flex-start; flex-direction: column; }
        .admin-index-title { font-size: 20px; }
        .admin-stat-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="admin-index-page">
    <div class="admin-index-toolbar">
        <div>
            <h1 class="admin-index-title">Dashboard Overview</h1>
            <p class="admin-index-subtitle">Ringkasan data utama sekolah.</p>
        </div>
    </div>

    <div class="admin-index-card">
        <div class="admin-stat-grid">
            <div class="admin-stat-card">
                <div class="admin-stat-label">Total Guru</div>
                <div class="admin-stat-value">{{ $totalTeachers }}</div>
                <div class="admin-stat-note">Aktif mengajar</div>
            </div>
            <div class="admin-stat-card">
                <div class="admin-stat-label">Total Siswa</div>
                <div class="admin-stat-value">{{ $totalStudents }}</div>
                <div class="admin-stat-note">Terdaftar</div>
            </div>
            <div class="admin-stat-card">
                <div class="admin-stat-label">Kelas</div>
                <div class="admin-stat-value">{{ $totalClasses }}</div>
                <div class="admin-stat-note">Rombel</div>
            </div>
        </div>
    </div>

    <div class="admin-index-card">
        <h3 class="admin-chart-title">Statistik Siswa per Angkatan</h3>
        <div class="admin-chart-wrap">
            @php
                $maxTotal = max(1, (int) collect($angkatanStats)->max('total'));
            @endphp
            <div class="admin-chart-grid">
                @foreach ($angkatanStats as $item)
                    @php
                        $height = max(8, (int) round(($item['total'] / $maxTotal) * 180));
                    @endphp
                    <div class="admin-chart-col">
                        <div class="admin-chart-value">{{ $item['total'] }}</div>
                        <div class="admin-chart-bar" style="height: {{ $height }}px;"></div>
                        <div class="admin-chart-label">{{ $item['angkatan'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
