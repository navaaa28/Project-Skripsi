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
        $uh = $data['nilai_uh'] ?? [];
        $tugas = $data['nilai_tugas'] ?? [];
        $uts = $data['nilai_uts'] ?? [];
        $uas = $data['nilai_uas'] ?? [];

        $mapelIds = collect(array_merge(
            array_keys($uh),
            array_keys($tugas),
            array_keys($uts),
            array_keys($uas)
        ))->unique()->filter();

        // Bobot persentase: UH 25%, Tugas 25%, UTS 25%, UAS 25%
        $weights = [
            'uh'    => 25,
            'tugas' => 25,
            'uts'   => 25,
            'uas'   => 25,
        ];

        foreach ($mapelIds as $mapelId) {
            $uhData = $this->parseUhScores($uh[$mapelId] ?? null);
            $nilaiUh = $uhData['average'];
            $detailUh = $uhData['details'];

            $nilaiTugas = $this->normalizeNilai($tugas[$mapelId] ?? null);
            $nilaiUts = $this->normalizeNilai($uts[$mapelId] ?? null);
            $nilaiUas = $this->normalizeNilai($uas[$mapelId] ?? null);

            if ($nilaiUh === null && $nilaiTugas === null && $nilaiUts === null && $nilaiUas === null) {
                continue;
            }

            $components = [
                'uh'    => $nilaiUh,
                'tugas' => $nilaiTugas,
                'uts'   => $nilaiUts,
                'uas'   => $nilaiUas,
            ];
            $totalWeight = 0;
            $weightedSum = 0;
            foreach ($components as $key => $value) {
                if ($value !== null) {
                    $totalWeight += $weights[$key];
                    $weightedSum += $value * $weights[$key];
                }
            }
            $nilaiAkhir = $totalWeight > 0 ? $weightedSum / $totalWeight : null;

            Nilai::updateOrCreate(
                [
                    'id_user' => $data['id_user'],
                    'id_mapel' => $mapelId,
                    'id_kelas' => $data['id_kelas'],
                    'id_tahun_ajaran' => $data['id_tahun_ajaran'],
                    'semester' => $data['semester'],
                ],
                [
                    'nilai_uh' => $nilaiUh,
                    'detail_uh' => $detailUh,
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

    private function parseUhScores(mixed $value): array
    {
        if ($value === null || trim((string)$value) === '') {
            return ['average' => null, 'details' => null];
        }

        $parts = explode(',', (string)$value);
        $validScores = [];
        
        foreach ($parts as $p) {
            $num = $this->normalizeNilai(trim($p));
            if ($num !== null) {
                $validScores[] = $num;
            }
        }

        if (count($validScores) === 0) {
            return ['average' => null, 'details' => null];
        }

        $average = array_sum($validScores) / count($validScores);
        return [
            'average' => $average,
            'details' => $validScores
        ];
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
