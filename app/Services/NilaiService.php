<?php

namespace App\Services;

use App\Models\Nilai;
use App\Models\NonAkademik;
use App\Models\Mapel;
use App\Models\Rekomendasi;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Services\GeminiService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class NilaiService
{
    /**
     * Simpan nilai akademik & non-akademik.
     */
    public function storeNilai(array $data, int $idGuru): void
    {
        $tugas = $data['nilai_tugas'] ?? [];
        $uts = $data['nilai_uts'] ?? [];
        $uas = $data['nilai_uas'] ?? [];

        $mapelIds = collect(array_merge(
            array_keys($tugas),
            array_keys($uts),
            array_keys($uas)
        ))->unique()->filter();

        foreach ($mapelIds as $mapelId) {
            $nilaiTugas = $this->normalizeNilai($tugas[$mapelId] ?? null);
            $nilaiUts = $this->normalizeNilai($uts[$mapelId] ?? null);
            $nilaiUas = $this->normalizeNilai($uas[$mapelId] ?? null);

            if ($nilaiTugas === null && $nilaiUts === null && $nilaiUas === null) {
                continue;
            }

            $nilaiParts = array_filter([$nilaiTugas, $nilaiUts, $nilaiUas], fn ($v) => $v !== null);
            $nilaiAkhir = count($nilaiParts) ? array_sum($nilaiParts) / count($nilaiParts) : null;

            Nilai::updateOrCreate(
                [
                    'id_user' => $data['id_user'],
                    'id_mapel' => $mapelId,
                    'id_kelas' => $data['id_kelas'],
                    'id_tahun_ajaran' => $data['id_tahun_ajaran'],
                    'semester' => $data['semester'],
                ],
                [
                    'nilai_tugas' => $nilaiTugas,
                    'nilai_uts' => $nilaiUts,
                    'nilai_uas' => $nilaiUas,
                    'nilai_akhir' => $nilaiAkhir,
                ]
            );
        }

        // Non-akademik
        if (
            ($data['sikap_belajar'] ?? null) !== null ||
            ($data['keaktifan'] ?? null) !== null ||
            !empty($data['minat_ekstrakurikuler']) ||
            !empty($data['catatan_guru'])
        ) {
            NonAkademik::updateOrCreate(
                [
                    'id_user' => $data['id_user'],
                    'id_guru' => $idGuru,
                    'id_kelas' => $data['id_kelas'],
                    'id_tahun_ajaran' => $data['id_tahun_ajaran'],
                    'semester' => $data['semester'],
                ],
                [
                    'sikap_belajar' => $data['sikap_belajar'] ?? null,
                    'keaktifan' => $data['keaktifan'] ?? null,
                    'minat_ekstrakurikuler' => $data['minat_ekstrakurikuler'] ?? null,
                    'catatan_guru' => $data['catatan_guru'] ?? null,
                ]
            );
        }
    }

    /**
     * Ambil nilai untuk siswa tertentu dengan filter.
     */
    public function getNilaiForSiswa(int $idUser, ?int $semester = null, ?int $idTahunAjaran = null): Collection
    {
        $query = Nilai::with(['mapel', 'kelas', 'tahunAjaran'])
            ->where('id_user', $idUser)
            ->orderBy('semester')
            ->orderBy('id_mapel');

        if ($semester) {
            $query->where('semester', $semester);
        }

        if ($idTahunAjaran) {
            $query->where('id_tahun_ajaran', $idTahunAjaran);
        }

        return $query->get();
    }

    private function normalizeNilai(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }
        $num = (float) $value;
        if ($num < 0 || $num > 100) {
            return null;
        }
        return $num;
    }
}
