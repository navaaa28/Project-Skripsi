@extends('layouts.guru-dashboard')

@section('title', 'Kenaikan Kelas')

@section('content')
<style>
    .panel { background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 16px; margin-bottom: 12px; }
    .info-box {
        background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px;
        padding: 12px 14px; font-size: 12px; color: #1e40af; margin-bottom: 12px;
    }
    .warn-box {
        background: #fef3c7; border: 1px solid #fcd34d; border-radius: 8px;
        padding: 12px 14px; font-size: 12px; color: #92400e; margin-bottom: 12px;
    }
    .success-box {
        background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px;
        padding: 12px 14px; font-size: 12px; color: #166534; margin-bottom: 12px;
    }

    .student-table { width: 100%; border-collapse: collapse; font-size: 12px; }
    .student-table th {
        text-align: left; padding: 10px 8px; font-size: 11px; font-weight: 700; color: #64748b;
        text-transform: uppercase; border-bottom: 2px solid #e5e7eb; background: #f8fafc;
    }
    .student-table td { padding: 10px 8px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }

    .radio-group { display: flex; gap: 12px; }
    .radio-label {
        display: inline-flex; align-items: center; gap: 4px; font-size: 12px;
        cursor: pointer; padding: 4px 8px; border-radius: 6px; border: 1px solid #e5e7eb;
        transition: all 0.15s;
    }
    .radio-label:has(input:checked) { border-color: transparent; }
    .radio-label.naik:has(input:checked) { background: #dcfce7; color: #166534; font-weight: 600; }
    .radio-label.tidak:has(input:checked) { background: #fee2e2; color: #991b1b; font-weight: 600; }

    .catatan-input {
        width: 100%; border: 1px solid #e5e7eb; border-radius: 6px; padding: 6px 8px;
        font-size: 11px; resize: none;
    }

    .btn-save {
        background: #16a34a; color: #fff; border: none; border-radius: 8px;
        padding: 10px 18px; font-size: 13px; font-weight: 700; cursor: pointer;
    }
    .btn-save:hover { background: #15803d; }

    .badge-naik {
        display: inline-block; padding: 2px 8px; border-radius: 999px;
        background: #dcfce7; color: #166534; font-size: 11px; font-weight: 600;
    }
    .badge-tidak {
        display: inline-block; padding: 2px 8px; border-radius: 999px;
        background: #fee2e2; color: #991b1b; font-size: 11px; font-weight: 600;
    }
    .badge-lulus {
        display: inline-block; padding: 2px 8px; border-radius: 999px;
        background: #fef3c7; color: #92400e; font-size: 11px; font-weight: 600;
    }
</style>

<div class="topbar">
    <div class="title">Kenaikan Kelas</div>
    <div class="user">
        <span>{{ auth()->user()?->guru?->nama_guru ?? auth()->user()?->username }}</span>
    </div>
</div>

@if (session('status'))
    <div class="success-box">✓ {{ session('status') }}</div>
@endif
@if (session('error'))
    <div class="warn-box">⚠ {{ session('error') }}</div>
@endif
@if ($errors->any())
    <div class="warn-box">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

@if (!$kelasWali)
    <div class="warn-box">
        <strong>Anda belum ditugaskan sebagai wali kelas.</strong><br>
        Hubungi admin untuk menetapkan Anda sebagai wali kelas.
    </div>
@elseif (!$tahunAjaranAktif)
    <div class="warn-box">
        <strong>Belum ada Tahun Ajaran aktif.</strong><br>
        Hubungi admin untuk mengaktifkan Tahun Ajaran.
    </div>
@else
    <div class="info-box">
        <strong>Tahun Ajaran:</strong> {{ $tahunAjaranAktif->nama_tahun_ajaran }} &nbsp;|&nbsp;
        <strong>Kelas:</strong> {{ $kelasWali->nama_kelas }} &nbsp;|&nbsp;
        <strong>Jumlah Siswa:</strong> {{ $siswas->count() }}
        @if (isset($kelasTujuan))
            &nbsp;|&nbsp; <strong>Tujuan:</strong> {{ $kelasTujuan->nama_kelas }}
        @else
            &nbsp;|&nbsp; <strong>Tujuan:</strong> <span style="color:#92400e;">Lulus / Alumni</span>
        @endif
    </div>

    @if ($allDecided)
        <div class="success-box">
            ✓ Anda sudah menentukan keputusan untuk semua siswa. Keputusan dapat diubah sebelum admin memproses.
        </div>
    @endif

    @if ($siswas->isEmpty())
        <div class="panel" style="text-align:center; color:#6b7280;">
            Tidak ada siswa di kelas Anda.
        </div>
    @else
        <form method="POST" action="{{ route('guru.kenaikan-kelas.store') }}">
            @csrf
            <div class="panel">
                <table class="student-table">
                    <thead>
                        <tr>
                            <th style="width:40px;">No</th>
                            <th>Nama Siswa</th>
                            <th>NIPD</th>
                            <th style="width:180px;">Keputusan</th>
                            <th style="width:200px;">Catatan (opsional)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($siswas as $i => $siswa)
                            @php $existing = $existingDecisions[$siswa->id_user] ?? null; @endphp
                            <tr>
                                <td style="color:#6b7280;">{{ $i + 1 }}</td>
                                <td style="font-weight:600;">{{ $siswa->nama_siswa }}</td>
                                <td style="color:#6b7280;">{{ $siswa->nipd ?? '-' }}</td>
                                <td>
                                    <div class="radio-group">
                                        <label class="radio-label naik">
                                            <input type="radio" name="keputusan[{{ $siswa->id_user }}]" value="naik"
                                                {{ ($existing === 'naik' || $existing === 'lulus') ? 'checked' : '' }} required>
                                            {{ isset($kelasTujuan) ? 'Naik' : 'Lulus' }}
                                        </label>
                                        <label class="radio-label tidak">
                                            <input type="radio" name="keputusan[{{ $siswa->id_user }}]" value="tidak_naik"
                                                {{ $existing === 'tidak_naik' ? 'checked' : '' }}>
                                            Tidak Naik
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <textarea name="catatan[{{ $siswa->id_user }}]" class="catatan-input" rows="1" placeholder="Catatan...">{{ old("catatan.{$siswa->id_user}") }}</textarea>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <button type="submit" class="btn-save">💾 Simpan Keputusan</button>
        </form>
    @endif
@endif

@endsection
