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
        <button type="submit" class="btn">Simpan</button>
        <div style="margin-top: 10px; font-size: 11px; color: #6b7280;">
            Menyimpan penilaian akademik dan non-akademik untuk siswa yang dipilih.
        </div>
    </div>
</form>
@endsection
