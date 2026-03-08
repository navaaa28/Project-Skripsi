<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Nilai;
use App\Models\Rekomendasi;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class MobileSiswaController extends Controller
{
    public function me(Request $request)
    {
        $user = $request->user();
        $siswa = $user->siswa?->load('kelas.waliGuru');

        return response()->json([
            'user' => [
                'id_user' => $user->id_user,
                'username' => $user->username,
                'role' => $user->role,
            ],
            'siswa' => $siswa ? [
                'id_user' => $siswa->id_user,
                'nama_siswa' => $siswa->nama_siswa,
                'nipd' => $siswa->nipd,
                'nisn' => $siswa->nisn,
                'rombel_saat_ini' => $siswa->rombel_saat_ini,
                'kelas' => $siswa->kelas?->nama_kelas,
                'wali_kelas' => $siswa->kelas?->waliGuru ? [
                    'id_user' => $siswa->kelas->waliGuru->id_user,
                    'nama_guru' => $siswa->kelas->waliGuru->nama_guru,
                    'nip' => $siswa->kelas->waliGuru->nip,
                ] : null,
            ] : null,
        ]);
    }

    public function nilai(Request $request)
    {
        $user = $request->user();
        $semester = $request->integer('semester');

        $query = Nilai::with(['mapel', 'kelas'])
            ->where('id_user', $user->id_user)
            ->orderBy('semester')
            ->orderBy('id_mapel');

        if ($semester) {
            $query->where('semester', $semester);
        }

        $rows = $query->get()->map(function ($n) {
            return [
                'semester' => $n->semester,
                'mapel' => $n->mapel?->nama_mapel,
                'kelas' => $n->kelas?->nama_kelas,
                'nilai_tugas' => $n->nilai_tugas,
                'nilai_uts' => $n->nilai_uts,
                'nilai_uas' => $n->nilai_uas,
                'nilai_akhir' => $n->nilai_akhir,
            ];
        });

        return response()->json([
            'semester' => $semester,
            'nilai' => $rows,
        ]);
    }

    public function rekomendasi(Request $request)
    {
        $user = $request->user();
        $semester = $request->integer('semester');
        $kelas = $request->input('kelas');

        $query = Rekomendasi::with('kelas')
            ->where('id_user', $user->id_user)
            ->orderByDesc('semester')
            ->orderByDesc('tgl_analisis');

        if ($semester) {
            $query->where('semester', $semester);
        }

        if ($request->filled('kelas')) {
            if (is_numeric($kelas)) {
                $query->where('id_kelas', (int) $kelas);
            } else {
                $query->whereHas('kelas', function ($sub) use ($kelas) {
                    $sub->where('nama_kelas', $kelas);
                });
            }
        }

        $rek = $query->first();
        if (!$rek) {
            return response()->json(['rekomendasi' => null]);
        }

        return response()->json([
            'filters' => [
                'kelas' => $kelas,
                'semester' => $semester,
            ],
            'rekomendasi' => [
                'id_kelas' => $rek->id_kelas,
                'kelas' => $rek->kelas?->nama_kelas,
                'semester' => $rek->semester,
                'minat_utama' => $rek->minat_utama,
                'bakat_potensial' => $rek->bakat_potensial,
                'minat' => $rek->minat_json,
                'bakat' => $rek->bakat_json,
                'analisis_tren' => $rek->analisis_tren,
                'ringkasan_non_akademik' => $rek->ringkasan_non_akademik,
                'saran_pengembangan' => $rek->saran_pengembangan,
                'tgl_analisis' => $rek->tgl_analisis,
            ],
        ]);
    }

    public function rekomendasiPdf(Request $request)
    {
        $user = $request->user();
        $semester = $request->integer('semester');
        $kelas = $request->input('kelas');

        $query = Rekomendasi::with('kelas')
            ->where('id_user', $user->id_user)
            ->orderByDesc('semester')
            ->orderByDesc('tgl_analisis');

        if ($semester) {
            $query->where('semester', $semester);
        }

        if ($request->filled('kelas')) {
            if (is_numeric($kelas)) {
                $query->where('id_kelas', (int) $kelas);
            } else {
                $query->whereHas('kelas', function ($sub) use ($kelas) {
                    $sub->where('nama_kelas', $kelas);
                });
            }
        }

        $rek = $query->first();
        if (!$rek) {
            return response()->json(['message' => 'Rekomendasi belum tersedia.'], 404);
        }

        $siswa = $user->siswa?->load('kelas');
        $semester = $rek->semester;

        $nilaiQuery = Nilai::with('mapel')
            ->where('id_user', $user->id_user)
            ->where('semester', $semester)
            ->orderBy('id_mapel');

        if ($rek->id_kelas) {
            $nilaiQuery->where('id_kelas', $rek->id_kelas);
        }

        $nilai = $nilaiQuery->get();

        $avg = $nilai->whereNotNull('nilai_akhir')->avg('nilai_akhir');

        $filename = $this->buildFilename($siswa?->nama_siswa ?? $user->username, $semester);

        $pdf = app('dompdf.wrapper')->loadView('pdf.rekomendasi', [
            'siswa' => $siswa,
            'user' => $user,
            'rekomendasi' => $rek,
            'nilai' => $nilai,
            'avg' => $avg,
            'semester' => $semester,
        ]);

        return $pdf->download($filename);
    }

    private function buildFilename(string $nama, int $semester): string
    {
        $safe = Str::of($nama)->upper()->replaceMatches('/[^A-Z0-9]+/', '_')->trim('_');
        return "Laporan_AI_{$safe}_Semester_{$semester}.pdf";
    }
}
