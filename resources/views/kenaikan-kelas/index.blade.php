@extends('layouts.admin-dashboard')

@section('title', 'Kenaikan Kelas')

@section('content')
<style>
    .page-header { margin-bottom: 16px; }
    .page-title { font-size: 22px; font-weight: 700; color: #0f172a; margin: 0; }
    .page-subtitle { font-size: 13px; color: #64748b; margin: 4px 0 0; }

    .info-card {
        background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px;
        padding: 14px 16px; margin-bottom: 14px; font-size: 13px; color: #1e40af;
    }
    .warning-card {
        background: #fef3c7; border: 1px solid #fcd34d; border-radius: 10px;
        padding: 14px 16px; margin-bottom: 14px; font-size: 13px; color: #92400e;
    }
    .success-card {
        background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px;
        padding: 14px 16px; margin-bottom: 14px; font-size: 13px; color: #166534;
    }

    .kelas-card {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 14px;
        padding: 16px; margin-bottom: 14px; box-shadow: 0 4px 12px rgba(15,23,42,.04);
    }
    .kelas-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 10px; padding-bottom: 8px; border-bottom: 1px solid #f1f5f9;
    }
    .kelas-title { font-size: 14px; font-weight: 700; color: #0f172a; }
    .kelas-guru { font-size: 12px; color: #64748b; }

    .stats-row { display: flex; gap: 10px; margin-bottom: 10px; flex-wrap: wrap; }
    .stat-chip {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: 600;
    }
    .stat-total { background: #f1f5f9; color: #475569; }
    .stat-decided { background: #eff6ff; color: #1d4ed8; }
    .stat-naik { background: #dcfce7; color: #166534; }
    .stat-tidak { background: #fee2e2; color: #991b1b; }
    .stat-lulus { background: #fef3c7; color: #92400e; }
    .stat-pending { background: #fef3c7; color: #92400e; }

    .decision-table { width: 100%; border-collapse: collapse; font-size: 12px; }
    .decision-table th {
        text-align: left; padding: 8px 6px; font-size: 11px; font-weight: 700;
        color: #64748b; text-transform: uppercase; border-bottom: 1px solid #e5e7eb;
    }
    .decision-table td { padding: 8px 6px; border-bottom: 1px solid #f1f5f9; }

    .badge-naik { display: inline-block; padding: 2px 8px; border-radius: 999px; background: #dcfce7; color: #166534; font-size: 11px; font-weight: 600; }
    .badge-tidak { display: inline-block; padding: 2px 8px; border-radius: 999px; background: #fee2e2; color: #991b1b; font-size: 11px; font-weight: 600; }
    .badge-lulus { display: inline-block; padding: 2px 8px; border-radius: 999px; background: #fef3c7; color: #92400e; font-size: 11px; font-weight: 600; }

    .confirm-section {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 14px;
        padding: 16px; margin-top: 14px;
    }
    .confirm-input {
        border: 2px solid #e5e7eb; border-radius: 8px; padding: 8px 12px;
        font-size: 14px; font-weight: 700; width: 120px; text-align: center;
        text-transform: uppercase; letter-spacing: 2px;
    }
    .confirm-input:focus { border-color: #ef4444; outline: none; }
    .btn-process {
        display: inline-flex; align-items: center; gap: 6px;
        background: #16a34a; color: #fff; border: none; border-radius: 10px;
        font-size: 13px; font-weight: 600; padding: 10px 18px; cursor: pointer;
    }
    .btn-process:hover { background: #15803d; }

    .toggle-btn {
        background: none; border: 1px solid #d1d5db; border-radius: 6px;
        padding: 4px 10px; font-size: 11px; color: #6b7280; cursor: pointer;
    }
    .toggle-btn:hover { background: #f1f5f9; }
</style>

<div class="page-header">
    <h1 class="page-title">Kenaikan Kelas</h1>
    <p class="page-subtitle">Rekap keputusan wali kelas dan proses kenaikan kelas siswa.</p>
</div>

@if (session('status'))
    <div class="success-card">✓ {{ session('status') }}</div>
@endif
@if (session('error'))
    <div class="warning-card">⚠ {{ session('error') }}</div>
@endif

@if ($tahunAjaranProses)
    <div class="info-card">
        <strong>Tahun Ajaran Diproses:</strong> {{ $tahunAjaranProses->nama_tahun_ajaran }}
    </div>
@else
    <div class="warning-card">
        <strong>Belum ada data kenaikan kelas yang bisa diproses.</strong>
        Silakan buat keputusan kenaikan kelas terlebih dahulu di panel guru.
    </div>
@endif

@if ($tahunAjaranAktif && $tahunAjaranProses && $tahunAjaranAktif->id_tahun_ajaran !== $tahunAjaranProses->id_tahun_ajaran)
    <div class="warning-card">
        <strong>Perhatian:</strong> Tahun ajaran aktif saat ini adalah {{ $tahunAjaranAktif->nama_tahun_ajaran }},
        tetapi masih ada keputusan kenaikan kelas yang belum diproses dari tahun ajaran {{ $tahunAjaranProses->nama_tahun_ajaran }}.
    </div>
@endif

@php
    $totalDecisions = 0;
    $totalPending = 0;
    $hasDecisions = false;
@endphp

@foreach ($rekapPerKelas as $rekap)
    @php
        $totalDecisions += $rekap['total_decided'];
        $totalPending += ($rekap['total_siswa'] - $rekap['total_decided']);
        if ($rekap['total_decided'] > 0) $hasDecisions = true;
    @endphp

    <div class="kelas-card">
        <div class="kelas-header">
            <div>
                <div class="kelas-title">{{ $rekap['kelas']->nama_kelas }}</div>
                <div class="kelas-guru">Wali Kelas: {{ $rekap['kelas']->waliGuru?->nama_guru ?? 'Belum ditentukan' }}</div>
            </div>
            @if ($rekap['total_decided'] > 0)
                <button type="button" class="toggle-btn" onclick="toggleDetail({{ $rekap['kelas']->id_kelas }})">
                    Lihat Detail
                </button>
            @endif
        </div>

        <div class="stats-row">
            <span class="stat-chip stat-total">Total: {{ $rekap['total_siswa'] }}</span>
            <span class="stat-chip stat-decided">Sudah Ditentukan: {{ $rekap['total_decided'] }}</span>
            @if ($rekap['total_siswa'] - $rekap['total_decided'] > 0)
                <span class="stat-chip stat-pending">Belum: {{ $rekap['total_siswa'] - $rekap['total_decided'] }}</span>
            @endif
            @if ($rekap['naik'] > 0) <span class="stat-chip stat-naik">Naik: {{ $rekap['naik'] }}</span> @endif
            @if ($rekap['tidak_naik'] > 0) <span class="stat-chip stat-tidak">Tidak Naik: {{ $rekap['tidak_naik'] }}</span> @endif
            @if ($rekap['lulus'] > 0) <span class="stat-chip stat-lulus">Lulus: {{ $rekap['lulus'] }}</span> @endif
        </div>

        @if ($rekap['total_decided'] > 0)
            <div id="detail-{{ $rekap['kelas']->id_kelas }}" style="display: none;">
                <table class="decision-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Siswa</th>
                            <th>Keputusan</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rekap['decisions'] as $i => $dec)
                            <tr>
                                <td style="color:#6b7280;">{{ $i + 1 }}</td>
                                <td style="font-weight:600;">{{ $dec->siswa?->nama_siswa ?? '-' }}</td>
                                <td>
                                    @if ($dec->status === 'naik')
                                        <span class="badge-naik">Naik Kelas</span>
                                    @elseif ($dec->status === 'lulus')
                                        <span class="badge-lulus">Lulus</span>
                                    @else
                                        <span class="badge-tidak">Tidak Naik</span>
                                    @endif
                                </td>
                                <td style="color:#6b7280; font-size:11px;">{{ $dec->catatan ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endforeach

@if ($tahunAjaranProses && $tahunAjaranProses->semester_aktif == 1)
    <div class="warning-card">
        <strong>Saat ini masih Semester Ganjil.</strong><br>
        Proses Kenaikan Kelas hanya dapat dilakukan pada akhir Tahun Ajaran (Semester Genap).
    </div>
@elseif ($hasDecisions)
    <div class="confirm-section">
        <form method="POST" action="{{ route('admin.kenaikan-kelas.process') }}">
            @csrf
            <div style="font-size: 13px; font-weight: 700; color: #0f172a; margin-bottom: 8px;">
                Proses Kenaikan Kelas
            </div>
            <div style="font-size: 12px; color: #6b7280; margin-bottom: 12px;">
                Siswa yang ditandai <strong>"Naik"</strong> oleh wali kelas akan dipindahkan ke kelas berikutnya.
                Siswa <strong>"Tidak Naik"</strong> tetap di kelas saat ini.
                Siswa <strong>"Lulus"</strong> akan ditandai sebagai alumni.
                <br>Ketik <strong style="color: #ef4444;">YA</strong> untuk konfirmasi.
            </div>
            <div style="display: flex; align-items: center; gap: 12px;">
                <input type="text" name="konfirmasi" class="confirm-input" placeholder="YA" autocomplete="off" required>
                <button type="submit" class="btn-process">✓ Proses Kenaikan Kelas</button>
            </div>
        </form>
    </div>
@elseif ($tahunAjaranProses)
    <div class="warning-card">
        Belum ada keputusan dari wali kelas. Minta wali kelas untuk menentukan kenaikan kelas di menu <strong>"Kenaikan Kelas"</strong> pada panel mereka.
    </div>
@endif

<script>
function toggleDetail(kelasId) {
    const el = document.getElementById('detail-' + kelasId);
    if (el) {
        el.style.display = el.style.display === 'none' ? 'block' : 'none';
    }
}
</script>
@endsection
