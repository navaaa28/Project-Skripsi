<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Nilai;
use App\Models\Rekomendasi;
use App\Models\TahunAjaran;
use App\Services\SiswaService;
use App\Services\NilaiService;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class MobileSiswaController extends Controller
{
    public function __construct(
        protected SiswaService $siswaService,
        protected NilaiService $nilaiService,
    ) {}

    public function me(Request $request)
    {
        $user = $request->user();
        $siswa = $user->siswa?->load('kelas.waliGuru');
        $activeTa = TahunAjaran::getActive();

        $kenaikan = null;
        if ($siswa && $activeTa) {
            $kenaikan = \App\Models\KenaikanKelas::with('kelasTujuan')
                ->where('id_user', $user->id_user)
                ->where('id_tahun_ajaran', $activeTa->id_tahun_ajaran)
                ->orderByDesc('created_at')
                ->first();
        }

        return response()->json([
            'sistem' => [
                'tahun_ajaran_aktif' => $activeTa?->nama_tahun_ajaran,
                'semester_aktif' => $activeTa?->semester_aktif,
            ],
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
                'jenis_kelamin' => $siswa->jenis_kelamin,
                'tempat_lahir' => $siswa->tempat_lahir,
                'tgl_lahir' => $siswa->tgl_lahir?->toDateString(),
                'rombel_saat_ini' => $siswa->rombel_saat_ini,
                'kelas' => $siswa->kelas?->nama_kelas,
                'wali_kelas' => $siswa->kelas?->waliGuru ? [
                    'id_user' => $siswa->kelas->waliGuru->id_user,
                    'nama_guru' => $siswa->kelas->waliGuru->nama_guru,
                    'nip' => $siswa->kelas->waliGuru->nip,
                ] : null,
                'kenaikan_kelas' => $kenaikan ? [
                    'status' => $kenaikan->status, // 'naik', 'tidak_naik', 'lulus'
                    'kelas_tujuan' => $kenaikan->kelasTujuan?->nama_kelas,
                    'catatan' => $kenaikan->catatan,
                ] : null,
            ] : null,
        ]);
    }

    /**
     * Update profil siswa (PUT /api/mobile/profil).
     */
    public function updateProfil(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'nama_siswa' => ['sometimes', 'string', 'max:100'],
            'jenis_kelamin' => ['sometimes', 'nullable', 'string', 'max:10'],
            'tempat_lahir' => ['sometimes', 'nullable', 'string', 'max:100'],
            'tgl_lahir' => ['sometimes', 'nullable', 'date'],
        ]);

        $siswa = $this->siswaService->updateProfil($user->id_user, $data);

        if (!$siswa) {
            return response()->json(['message' => 'Data siswa tidak ditemukan.'], 404);
        }

        return response()->json([
            'message' => 'Profil berhasil diperbarui.',
            'siswa' => [
                'id_user' => $siswa->id_user,
                'nama_siswa' => $siswa->nama_siswa,
                'jenis_kelamin' => $siswa->jenis_kelamin,
                'tempat_lahir' => $siswa->tempat_lahir,
                'tgl_lahir' => $siswa->tgl_lahir?->toDateString(),
                'nipd' => $siswa->nipd,
                'nisn' => $siswa->nisn,
                'rombel_saat_ini' => $siswa->rombel_saat_ini,
            ],
        ]);
    }

    public function nilai(Request $request)
    {
        $user = $request->user();
        $semester = $request->integer('semester');
        $idTahunAjaran = $request->integer('tahun_ajaran');

        $rows = $this->nilaiService->getNilaiForSiswa(
            $user->id_user,
            $semester ?: null,
            $idTahunAjaran ?: null
        );

        $mapped = $rows->map(function ($n) {
            return [
                'semester' => $n->semester,
                'mapel' => $n->mapel?->nama_mapel,
                'kelas' => $n->kelas?->nama_kelas,
                'tahun_ajaran' => $n->tahunAjaran?->nama_tahun_ajaran,
                'nilai_uh' => $n->nilai_uh,
                'detail_uh' => $n->detail_uh,
                'nilai_tugas' => $n->nilai_tugas,
                'nilai_uts' => $n->nilai_uts,
                'nilai_uas' => $n->nilai_uas,
                'nilai_akhir' => $n->nilai_akhir,
            ];
        });

        return response()->json([
            'semester' => $semester,
            'nilai' => $mapped,
        ]);
    }

    public function rekomendasi(Request $request)
    {
        $user = $request->user();
        $semester = $request->integer('semester');
        $kelas = $request->input('kelas');

        $query = Rekomendasi::with(['kelas', 'tahunAjaran'])
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
                'kelas' => optional($rek->kelas)->nama_kelas,
                'tahun_ajaran' => optional($rek->tahunAjaran)->nama_tahun_ajaran,
                'semester' => $rek->semester,
                'minat_utama' => $rek->minat_utama,
                'bakat_potensial' => $rek->bakat_potensial,
                'minat' => $rek->minat_json,
                'bakat' => $rek->bakat_json,
                'analisis_tren' => $rek->analisis_tren,
                'ringkasan_non_akademik' => $rek->ringkasan_non_akademik,
                'saran_pengembangan' => $rek->saran_pengembangan,
                'tips_peningkatan' => $rek->tips_peningkatan,
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
