<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Nilai;
use App\Models\NonAkademik;
use App\Models\Siswa;
use App\Models\Rekomendasi;
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

        return view('guru.penilaian.index', [
            'kelas' => $kelas,
            'siswas' => $siswas,
            'mapel' => $mapel,
        ]);
    }

    public function store(Request $request)
    {
        $guru = Auth::user()?->guru;
        abort_unless($guru, 403);

        $data = $request->validate([
            'id_kelas' => ['required', 'exists:kelas,id_kelas'],
            'id_user' => ['required', 'exists:users,id_user'],
            'semester' => ['required', 'integer', 'min:1', 'max:2'],
            'nilai_tugas' => ['nullable', 'array'],
            'nilai_uts' => ['nullable', 'array'],
            'nilai_uas' => ['nullable', 'array'],
            'sikap_belajar' => ['nullable', 'integer', 'min:1', 'max:5'],
            'keaktifan' => ['nullable', 'integer', 'min:1', 'max:5'],
            'minat_ekstrakurikuler' => ['nullable', 'string', 'max:100'],
            'catatan_guru' => ['nullable', 'string'],
        ]);

        // Pastikan kelas milik guru
        if (!$guru->kelasWali()->where('id_kelas', $data['id_kelas'])->exists()) {
            abort(403);
        }

        // Pastikan siswa berada di kelas yang dipilih
        if (!Siswa::where('id_user', $data['id_user'])->where('id_kelas', $data['id_kelas'])->exists()) {
            return back()->withErrors(['id_user' => 'Siswa tidak berada di kelas yang dipilih.']);
        }

        $tugas = $request->input('nilai_tugas', []);
        $uts = $request->input('nilai_uts', []);
        $uas = $request->input('nilai_uas', []);

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

        $this->tryAnalyzeWithAI($data['id_user'], $data['id_kelas'], $data['semester']);

        return back()->with('status', 'Penilaian berhasil disimpan.');
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

    private function tryAnalyzeWithAI(int $idUser, int $idKelas, int $semester): void
    {
        $mapelCount = Mapel::count();
        if ($mapelCount === 0) {
            return;
        }

        $nilaiCount = Nilai::where('id_user', $idUser)
            ->where('id_kelas', $idKelas)
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
                    $mapel[$row->mapel->nama_mapel] = $row->nilai_akhir;
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
}
