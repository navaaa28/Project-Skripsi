<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Nilai;
use App\Models\NonAkademik;
use App\Models\Siswa;
use App\Models\Rekomendasi;
use App\Models\KenaikanKelas;
use App\Models\TahunAjaran;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GuruPenilaianController extends Controller
{
    public function index()
    {
        $guru = Auth::user()?->guru;
        $kelas = $guru ? $guru->kelasWali()->orderBy('nama_kelas')->get() : collect();
        $kelasIds = $kelas->pluck('id_kelas');
        $siswas = $kelasIds->isNotEmpty()
            ? Siswa::whereIn('id_kelas', $kelasIds)->orderBy('nama_siswa')->get()
            : collect();
        $mapel = Mapel::orderBy('nama_mapel')->get();
        $tahunAjarans = TahunAjaran::orderByDesc('id_tahun_ajaran')->get();
        $activeTahunAjaran = TahunAjaran::getActive();

        return view('guru.penilaian.index', [
            'kelas' => $kelas,
            'siswas' => $siswas,
            'mapel' => $mapel,
            'tahunAjarans' => $tahunAjarans,
            'activeTahunAjaran' => $activeTahunAjaran,
        ]);
    }

    public function store(Request $request)
    {
        $guru = Auth::user()?->guru;
        abort_unless($guru, 403);

        $activeTahunAjaran = TahunAjaran::getActive();
        if (!$activeTahunAjaran) {
            return back()->withErrors(['id_tahun_ajaran' => 'Tidak ada tahun ajaran aktif. Hubungi admin.']);
        }

        $request->merge([
            'id_tahun_ajaran' => $activeTahunAjaran->id_tahun_ajaran,
            'semester' => $activeTahunAjaran->semester_aktif,
        ]);

        $data = $request->validate([
            'id_kelas' => ['required', 'exists:kelas,id_kelas'],
            'id_user' => ['required', 'exists:users,id_user'],
            'id_tahun_ajaran' => ['required', 'exists:tahun_ajarans,id_tahun_ajaran'],
            'semester' => ['required', 'integer', 'min:1', 'max:2'],
            'nilai_uh' => ['nullable', 'array'],
            'nilai_tugas' => ['nullable', 'array'],
            'nilai_uts' => ['nullable', 'array'],
            'nilai_uas' => ['nullable', 'array'],
            'sikap_belajar' => ['nullable', 'integer', 'min:1', 'max:5'],
            'keaktifan' => ['nullable', 'integer', 'min:1', 'max:5'],
            'minat_ekstrakurikuler' => ['nullable', 'string', 'max:100'],
            'catatan_guru' => ['nullable', 'string'],
            'keputusan_kenaikan' => ['nullable', 'in:naik,tidak_naik'],
            'catatan_kenaikan' => ['nullable', 'string', 'max:500'],
        ]);

        // Pastikan kelas milik guru
        if (!$guru->kelasWali()->where('id_kelas', $data['id_kelas'])->exists()) {
            abort(403);
        }

        // Pastikan siswa berada di kelas yang dipilih
        if (!Siswa::where('id_user', $data['id_user'])->where('id_kelas', $data['id_kelas'])->exists()) {
            return back()->withErrors(['id_user' => 'Siswa tidak berada di kelas yang dipilih.']);
        }

        $uh = $request->input('nilai_uh', []);
        $tugas = $request->input('nilai_tugas', []);
        $uts = $request->input('nilai_uts', []);
        $uas = $request->input('nilai_uas', []);

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

            // Hitung nilai akhir berbobot (redistribusi proporsional jika ada komponen kosong)
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

        if (
            $data['sikap_belajar'] !== null ||
            $data['keaktifan'] !== null ||
            !empty($data['minat_ekstrakurikuler']) ||
            !empty($data['catatan_guru'])
        ) {
            NonAkademik::updateOrCreate(
                [
                    'id_user' => $data['id_user'],
                    'id_guru' => $guru->id_user,
                    'id_kelas' => $data['id_kelas'],
                    'id_tahun_ajaran' => $data['id_tahun_ajaran'],
                    'semester' => $data['semester'],
                ],
                [
                    'sikap_belajar' => $data['sikap_belajar'],
                    'keaktifan' => $data['keaktifan'],
                    'minat_ekstrakurikuler' => $data['minat_ekstrakurikuler'],
                    'catatan_guru' => $data['catatan_guru'],
                ]
            );
        }

        $this->tryAnalyzeWithAI($data['id_user'], $data['id_kelas'], $data['semester'], $data['id_tahun_ajaran']);

        // Simpan keputusan kenaikan kelas jika semester 2
        if ((int) $data['semester'] === 2 && !empty($data['keputusan_kenaikan'])) {
            $this->saveKenaikanKelas($data, $guru);
        }

        return back()->with('status', 'Penilaian berhasil disimpan.');
    }

    public function getNilaiSiswa($id_user)
    {
        $guru = Auth::user()?->guru;
        abort_unless($guru, 403);

        $activeTahunAjaran = TahunAjaran::getActive();
        if (!$activeTahunAjaran) {
            return response()->json(['error' => 'Tidak ada tahun ajaran aktif.'], 400);
        }

        $semester = $activeTahunAjaran->semester_aktif;
        $id_tahun_ajaran = $activeTahunAjaran->id_tahun_ajaran;

        $nilais = Nilai::where('id_user', $id_user)
            ->where('id_tahun_ajaran', $id_tahun_ajaran)
            ->where('semester', $semester)
            ->get();

        $nonAkademik = NonAkademik::where('id_user', $id_user)
            ->where('id_tahun_ajaran', $id_tahun_ajaran)
            ->where('semester', $semester)
            ->first();

        $mapel_data = [];
        foreach ($nilais as $n) {
            $uhValue = '';
            if (!empty($n->detail_uh)) {
                $uhValue = implode(', ', $n->detail_uh);
            } elseif ($n->nilai_uh !== null) {
                $uhValue = $n->nilai_uh;
            }

            $mapel_data[$n->id_mapel] = [
                'nilai_uh' => $uhValue,
                'nilai_tugas' => $n->nilai_tugas,
                'nilai_uts' => $n->nilai_uts,
                'nilai_uas' => $n->nilai_uas,
            ];
        }

        return response()->json([
            'nilai' => $mapel_data,
            'non_akademik' => $nonAkademik,
        ]);
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

    private function tryAnalyzeWithAI(int $idUser, int $idKelas, int $semester, int $idTahunAjaran): void
    {
        $mapelCount = Mapel::count();
        if ($mapelCount === 0) {
            return;
        }

        $nilaiCount = Nilai::where('id_user', $idUser)
            ->where('id_kelas', $idKelas)
            ->where('id_tahun_ajaran', $idTahunAjaran)
            ->where('semester', $semester)
            ->whereNotNull('nilai_akhir')
            ->count();

        if ($nilaiCount < $mapelCount) {
            Log::info('AI skipped: nilai belum lengkap', [
                'id_user' => $idUser,
                'id_kelas' => $idKelas,
                'semester' => $semester,
                'nilai_count' => $nilaiCount,
                'mapel_count' => $mapelCount,
            ]);
            return;
        }

        $payload = $this->buildPayload($idUser);
        if (!$payload) {
            return;
        }

        Log::info('AI request prepared', [
            'id_user' => $idUser,
            'id_kelas' => $idKelas,
            'semester' => $semester,
        ]);

        $result = app(GeminiService::class)->analyze($payload);
        if (!$result) {
            Log::warning('AI result null', [
                'id_user' => $idUser,
                'semester' => $semester,
            ]);
            return;
        }

        $minatList = $this->normalizeList(data_get($result, 'minat'));
        $bakatList = $this->normalizeList(data_get($result, 'bakat'));
        $minatUtama = data_get($result, 'minat_utama')
            ?? data_get($minatList, '0.nama')
            ?? data_get($minatList, '0.label')
            ?? data_get($minatList, '0.minat');
        $bakatPotensial = data_get($result, 'bakat_potensial')
            ?? data_get($bakatList, '0.nama')
            ?? data_get($bakatList, '0.label')
            ?? data_get($bakatList, '0.bakat');

        Rekomendasi::updateOrCreate(
            [
                'id_user' => $idUser,
                'id_kelas' => $idKelas,
                'id_tahun_ajaran' => $idTahunAjaran,
                'semester' => $semester,
            ],
            [
                'id_kelas' => $idKelas,
                'minat_utama' => $minatUtama,
                'bakat_potensial' => $bakatPotensial,
                'confidence_score' => data_get($result, 'confidence_score'),
                'persentase_minat' => data_get($result, 'persentase_minat'),
                'persentase_bakat' => data_get($result, 'persentase_bakat'),
                'minat_json' => $minatList ?: data_get($result, 'minat'),
                'bakat_json' => $bakatList ?: data_get($result, 'bakat'),
                'analisis_tren' => data_get($result, 'analisis_tren'),
                'ringkasan_non_akademik' => data_get($result, 'ringkasan_non_akademik'),
                'saran_pengembangan' => data_get($result, 'saran_pengembangan'),
                'tips_peningkatan' => data_get($result, 'tips_peningkatan'),
                'tgl_analisis' => now()->toDateString(),
            ]
        );

        Log::info('AI result saved', [
            'id_user' => $idUser,
            'semester' => $semester,
            'has_minat' => (bool) data_get($result, 'minat'),
            'has_bakat' => (bool) data_get($result, 'bakat'),
        ]);
    }

    private function normalizeList(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }
        return array_values(array_filter($value, fn ($item) => is_array($item)));
    }

    private function buildPayload(int $idUser): ?array
    {
        $siswa = Siswa::with('kelas')->where('id_user', $idUser)->first();
        if (!$siswa) {
            return null;
        }

        $nilai = Nilai::with('mapel')
            ->where('id_user', $idUser)
            ->orderBy('semester')
            ->get();

        $riwayatAkademik = [];
        foreach ($nilai->groupBy('semester') as $semester => $rows) {
            $mapel = [];
            foreach ($rows as $row) {
                if ($row->mapel && $row->nilai_akhir !== null) {
                    $mapel[$row->mapel->nama_mapel] = [
                        'nilai_uh' => $row->nilai_uh !== null ? round($row->nilai_uh, 1) : null,
                        'detail_uh' => $row->detail_uh,
                        'nilai_tugas' => $row->nilai_tugas !== null ? round($row->nilai_tugas, 1) : null,
                        'nilai_uts' => $row->nilai_uts !== null ? round($row->nilai_uts, 1) : null,
                        'nilai_uas' => $row->nilai_uas !== null ? round($row->nilai_uas, 1) : null,
                        'nilai_akhir' => round($row->nilai_akhir, 1),
                        'kkm' => $row->mapel->kkm ?? 75,
                    ];
                }
            }
            $riwayatAkademik[] = [
                'kelas' => $siswa->kelas?->nama_kelas,
                'semester' => (int) $semester,
                'mata_pelajaran' => $mapel,
            ];
        }

        $nonAkademikRows = NonAkademik::where('id_user', $idUser)
            ->orderBy('semester')
            ->get();

        $riwayatNonAkademik = $nonAkademikRows->map(function ($row) use ($siswa) {
            return [
                'kelas' => $siswa->kelas?->nama_kelas,
                'semester' => (int) $row->semester,
                'indikator' => [
                    'sikap_belajar' => $row->sikap_belajar,
                    'keaktifan' => $row->keaktifan,
                    'minat_ekstrakurikuler' => $row->minat_ekstrakurikuler,
                    'catatan_guru' => $row->catatan_guru,
                ],
            ];
        })->values()->all();

        return [
            'nama_siswa' => $siswa->nama_siswa,
            'riwayat_akademik' => $riwayatAkademik,
            'riwayat_non_akademik' => $riwayatNonAkademik,
        ];
    }

    /**
     * Simpan keputusan kenaikan kelas ke tabel kenaikan_kelas.
     */
    private function saveKenaikanKelas(array $data, $guru): void
    {
        $currentKelas = Kelas::find($data['id_kelas']);
        if (!$currentKelas) return;

        // Cari kelas tujuan (kelas selanjutnya)
        $kelasTujuan = null;
        preg_match('/(\d+)/', $currentKelas->nama_kelas, $matches);
        if (!empty($matches[1])) {
            $nextNumber = ((int) $matches[1]) + 1;
            $kelasTujuan = Kelas::where('nama_kelas', 'LIKE', "%{$nextNumber}%")
                ->get()
                ->first(fn (Kelas $kelas) => preg_match("/(^|[^0-9]){$nextNumber}([^0-9]|$)/", $kelas->nama_kelas));
        }

        $status = $data['keputusan_kenaikan'];
        if ($status === 'naik' && !$kelasTujuan) {
            $status = 'lulus'; // Kelas tertinggi → lulus
        }

        KenaikanKelas::updateOrCreate(
            [
                'id_user' => $data['id_user'],
                'id_tahun_ajaran' => $data['id_tahun_ajaran'],
            ],
            [
                'id_kelas_asal' => $data['id_kelas'],
                'id_kelas_tujuan' => ($status === 'naik') ? $kelasTujuan?->id_kelas : null,
                'id_guru' => $guru->id_user,
                'status' => $status,
                'catatan' => $data['catatan_kenaikan'] ?? null,
                'is_processed' => false,
            ]
        );
    }
}
