@extends('layouts.guru-dashboard')

@section('title', 'Kelola Penilaian')

@section('content')
<style>
    .panel { background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 16px; margin-bottom: 12px; }
    .panel h3 { margin: 0 0 10px; font-size: 13px; font-weight: 700; }
    .grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
    .grid-4 { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 10px; }
    .field { margin-bottom: 10px; }
    .label { display: block; font-size: 12px; color: #6b7280; margin-bottom: 6px; }
    .input, .select, .textarea {
        width: 100%;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 8px 10px;
        font-size: 12px;
        background: #fff;
    }
    .textarea { min-height: 80px; resize: vertical; }
    .btn {
        background: #16a34a;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
    }
    .info {
        background: #e0edff;
        border: 1px solid #c7dcff;
        color: #1d4ed8;
        padding: 10px 12px;
        border-radius: 8px;
        font-size: 12px;
        margin-bottom: 12px;
    }
    @media (max-width: 900px) {
        .grid { grid-template-columns: 1fr; }
        .grid-4 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
</style>

<div class="topbar">
    <div class="title">Kelola Penilaian Siswa</div>
    <div class="user">
        <span>{{ auth()->user()?->guru?->nama_guru ?? auth()->user()?->username }}</span>
    </div>
</div>

<div class="info">
    Informasi Penting<br>
    Periode Input Nilai Semester Ganjil Telah Dibuka!
</div>

<form method="POST" action="{{ route('guru.penilaian.store') }}">
    @csrf
    <div class="panel">
        <h3>Student Selection</h3>
        <div class="grid">
            <div class="field">
                <label class="label">Kelas</label>
                <select name="id_kelas" class="select" required>
                    <option value="">Pilih Kelas</option>
                    @foreach ($kelas as $k)
                        <option value="{{ $k->id_kelas }}">{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label class="label">Siswa</label>
                <select name="id_user" class="select" required>
                    <option value="">Pilih Siswa</option>
                    @foreach ($siswas as $s)
                        <option value="{{ $s->id_user }}">{{ $s->nama_siswa }} ({{ $s->nipd }})</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label class="label">Semester</label>
                <select name="semester" class="select" required>
                    <option value="">Pilih Semester</option>
                    <option value="1">1 (Ganjil)</option>
                    <option value="2">2 (Genap)</option>
                </select>
            </div>
        </div>
    </div>

    <div class="panel">
        <h3>Academic Assessment</h3>
        <div class="table-wrap" style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 8px;">
            <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                <thead>
                    <tr>
                        <th style="text-align:left; padding: 8px; border-bottom: 1px solid #e5e7eb;">Mata Pelajaran</th>
                        <th style="text-align:left; padding: 8px; border-bottom: 1px solid #e5e7eb; width: 140px;">Nilai Tugas</th>
                        <th style="text-align:left; padding: 8px; border-bottom: 1px solid #e5e7eb; width: 140px;">Nilai UTS</th>
                        <th style="text-align:left; padding: 8px; border-bottom: 1px solid #e5e7eb; width: 140px;">Nilai UAS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($mapel as $m)
                        <tr>
                            <td style="padding: 8px; border-bottom: 1px solid #f1f5f9;">{{ $m->nama_mapel }}</td>
                            <td style="padding: 8px; border-bottom: 1px solid #f1f5f9;">
                                <input type="number" step="0.01" min="0" max="100" name="nilai_tugas[{{ $m->id_mapel }}]" class="input">
                            </td>
                            <td style="padding: 8px; border-bottom: 1px solid #f1f5f9;">
                                <input type="number" step="0.01" min="0" max="100" name="nilai_uts[{{ $m->id_mapel }}]" class="input">
                            </td>
                            <td style="padding: 8px; border-bottom: 1px solid #f1f5f9;">
                                <input type="number" step="0.01" min="0" max="100" name="nilai_uas[{{ $m->id_mapel }}]" class="input">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel">
        <h3>Non-Academic Observation</h3>
        <div class="grid">
            <div class="field">
                <label class="label">Sikap Belajar</label>
                <select name="sikap_belajar" class="select">
                    <option value="">Pilih Skala</option>
                    <option value="5">Sangat Baik</option>
                    <option value="4">Baik</option>
                    <option value="3">Cukup</option>
                    <option value="2">Kurang</option>
                    <option value="1">Buruk</option>
                </select>
            </div>
            <div class="field">
                <label class="label">Keaktifan</label>
                <select name="keaktifan" class="select">
                    <option value="">Pilih Skala</option>
                    <option value="5">Sangat Aktif</option>
                    <option value="4">Aktif</option>
                    <option value="3">Cukup</option>
                    <option value="2">Kurang</option>
                    <option value="1">Pasif</option>
                </select>
            </div>
            <div class="field">
                <label class="label">Minat Ekstrakurikuler</label>
                <input type="text" name="minat_ekstrakurikuler" class="input" placeholder="Contoh: Pramuka, Pencak Silat">
            </div>
            <div class="field" style="grid-column: 1 / -1;">
                <label class="label">Catatan Guru</label>
                <textarea name="catatan_guru" class="textarea" placeholder="Observasi singkat..."></textarea>
            </div>
        </div>
    </div>

    <div class="panel">
        <h3>Actions</h3>
        <button type="submit" class="btn" id="btnSimpan">Simpan</button>
        <div style="margin-top: 10px; font-size: 11px; color: #6b7280;">
            Menyimpan penilaian akademik dan non-akademik untuk siswa yang dipilih.
        </div>
    </div>
</form>

{{-- ====== Status Flash ====== --}}
@if (session('status'))
    <div class="flash-success" id="flashMsg">
        <span class="flash-icon">✓</span> {{ session('status') }}
    </div>
@endif
@if ($errors->any())
    <div class="flash-error" id="flashMsg">
        <span class="flash-icon">!</span>
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

{{-- ====== Loading Overlay ====== --}}
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-card">
        <div class="spinner"></div>
        <div class="loading-title" id="loadingTitle">Menyimpan Data Nilai...</div>
        <div class="loading-steps">
            <div class="step active" id="step1"><span class="step-dot"></span> Menyimpan nilai akademik</div>
            <div class="step" id="step2"><span class="step-dot"></span> Menyimpan data non-akademik</div>
            <div class="step" id="step3"><span class="step-dot"></span> Menganalisis dengan AI</div>
            <div class="step" id="step4"><span class="step-dot"></span> Menyelesaikan</div>
        </div>
        <div class="loading-hint">Mohon tunggu, jangan menutup halaman ini.</div>
    </div>
</div>

<style>
    /* ---- Flash Messages ---- */
    .flash-success, .flash-error {
        position: fixed; top: 16px; right: 16px; z-index: 9000;
        padding: 12px 18px; border-radius: 10px; font-size: 13px; font-weight: 600;
        display: flex; align-items: flex-start; gap: 8px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        animation: slideIn .35s ease-out;
        max-width: 380px;
    }
    .flash-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
    .flash-error   { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
    .flash-icon { flex-shrink: 0; width: 22px; height: 22px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; }
    .flash-success .flash-icon { background: #22c55e; color: #fff; }
    .flash-error .flash-icon   { background: #ef4444; color: #fff; }

    @keyframes slideIn {
        from { opacity: 0; transform: translateX(40px); }
        to   { opacity: 1; transform: translateX(0); }
    }

    /* ---- Loading Overlay ---- */
    .loading-overlay {
        display: none;
        position: fixed; inset: 0; z-index: 10000;
        background: rgba(15, 23, 42, 0.55);
        backdrop-filter: blur(6px);
        align-items: center; justify-content: center;
    }
    .loading-overlay.show { display: flex; }

    .loading-card {
        background: #fff; border-radius: 16px; padding: 32px 36px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.18);
        text-align: center; max-width: 360px; width: 90%;
        animation: popIn .3s ease-out;
    }
    @keyframes popIn {
        from { opacity: 0; transform: scale(.92); }
        to   { opacity: 1; transform: scale(1); }
    }

    .spinner {
        width: 44px; height: 44px; margin: 0 auto 18px;
        border: 4px solid #e2e8f0; border-top-color: #2563eb;
        border-radius: 50%;
        animation: spin .8s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    .loading-title {
        font-size: 16px; font-weight: 700; color: #0f172a; margin-bottom: 18px;
    }

    .loading-steps { text-align: left; margin: 0 auto; display: inline-block; }
    .step {
        display: flex; align-items: center; gap: 8px;
        font-size: 12px; color: #94a3b8; padding: 5px 0;
        transition: color .3s ease;
    }
    .step.active { color: #2563eb; font-weight: 600; }
    .step.done   { color: #16a34a; }
    .step-dot {
        width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
        background: #cbd5e1; transition: background .3s ease;
    }
    .step.active .step-dot { background: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.2); }
    .step.done .step-dot   { background: #16a34a; }

    .loading-hint {
        margin-top: 16px; font-size: 11px; color: #94a3b8;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form[action*="penilaian"]');
    const overlay = document.getElementById('loadingOverlay');
    const title = document.getElementById('loadingTitle');
    const steps = [
        document.getElementById('step1'),
        document.getElementById('step2'),
        document.getElementById('step3'),
        document.getElementById('step4'),
    ];

    if (form) {
        form.addEventListener('submit', function () {
            overlay.classList.add('show');

            // Progressive step animation
            const messages = [
                { title: 'Menyimpan Data Nilai...', step: 0, delay: 0 },
                { title: 'Menyimpan Non-Akademik...', step: 1, delay: 1500 },
                { title: 'Menganalisis dengan AI...', step: 2, delay: 3000 },
                { title: 'Menyelesaikan proses...', step: 3, delay: 6000 },
            ];

            messages.forEach(function (msg) {
                setTimeout(function () {
                    title.textContent = msg.title;
                    steps.forEach(function (s, i) {
                        s.classList.remove('active', 'done');
                        if (i < msg.step) s.classList.add('done');
                        if (i === msg.step) s.classList.add('active');
                    });
                }, msg.delay);
            });
        });
    }

    // Auto-hide flash after 5 seconds
    const flash = document.getElementById('flashMsg');
    if (flash) {
        setTimeout(function () {
            flash.style.transition = 'opacity .4s ease';
            flash.style.opacity = '0';
            setTimeout(function () { flash.remove(); }, 400);
        }, 5000);
    }
});
</script>
@endsection
