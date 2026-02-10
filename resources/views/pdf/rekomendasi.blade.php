<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan AI</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111827; }
        h1 { font-size: 18px; margin: 0 0 6px; }
        h2 { font-size: 14px; margin: 16px 0 6px; }
        .muted { color: #6b7280; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #e5e7eb; padding: 6px 8px; text-align: left; }
        th { background: #f3f4f6; font-weight: 700; }
        .chip { display: inline-block; padding: 4px 8px; background: #eef2ff; border-radius: 999px; margin: 2px 4px 2px 0; }
    </style>
</head>
<body>
    <h1>Laporan Analisis Minat & Bakat</h1>
    <div class="muted">Semester: {{ $semester }} | Tanggal: {{ $rekomendasi->tgl_analisis }}</div>

    <h2>Data Siswa</h2>
    <div>Nama: {{ $siswa->nama_siswa ?? '-' }}</div>
    <div>NIPD: {{ $siswa->nipd ?? '-' }}</div>
    <div>Kelas: {{ $siswa->kelas->nama_kelas ?? '-' }}</div>

    <h2>Minat & Bakat (Top 3)</h2>
    <div><strong>Minat:</strong>
        @foreach(($rekomendasi->minat_json ?? []) as $m)
            <span class="chip">{{ $m['nama'] ?? '-' }} ({{ $m['persentase'] ?? '-' }}%)</span>
        @endforeach
    </div>
    <div><strong>Bakat:</strong>
        @foreach(($rekomendasi->bakat_json ?? []) as $b)
            <span class="chip">{{ $b['nama'] ?? '-' }} ({{ $b['persentase'] ?? '-' }}%)</span>
        @endforeach
    </div>

    <h2>Nilai Semester {{ $semester }}</h2>
    <table>
        <thead>
            <tr>
                <th>Mapel</th>
                <th>Tugas</th>
                <th>UTS</th>
                <th>UAS</th>
                <th>Nilai Akhir</th>
            </tr>
        </thead>
        <tbody>
            @forelse($nilai as $n)
                <tr>
                    <td>{{ $n->mapel->nama_mapel ?? '-' }}</td>
                    <td>{{ $n->nilai_tugas ?? '-' }}</td>
                    <td>{{ $n->nilai_uts ?? '-' }}</td>
                    <td>{{ $n->nilai_uas ?? '-' }}</td>
                    <td>{{ $n->nilai_akhir ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Belum ada nilai.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="muted" style="margin-top:6px;">
        Rata-rata: {{ $avg ? number_format($avg, 2) : '-' }}
    </div>

    <h2>Ringkasan Non-Akademik</h2>
    <div>{{ $rekomendasi->ringkasan_non_akademik ?? '-' }}</div>

    <h2>Analisis Tren</h2>
    <div>{{ $rekomendasi->analisis_tren ?? '-' }}</div>

    <h2>Saran Pengembangan</h2>
    <div>{{ $rekomendasi->saran_pengembangan ?? '-' }}</div>
</body>
</html>
