<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Nilai;
use App\Models\Rekomendasi;
use App\Models\Siswa;
use App\Models\KenaikanKelas;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $activeTahunAjaran = TahunAjaran::getActive();
        $semesterAktif = $activeTahunAjaran?->semester_aktif ?? 1;
        $semesterLabel = $semesterAktif == 2 ? 'Genap' : 'Ganjil';

        // ── Stat Cards ──
        $totalStudents = Siswa::count();
        $totalTeachers = Guru::count();
        $totalClasses  = Kelas::count();
        $totalMapel    = Mapel::count();

        // ── Alert: Kelas tanpa Wali ──
        $kelasTanpaWali = Kelas::whereNull('id_guru')->get();

        // ── Alert: Siswa Rawan (di bawah KKM) ──
        $siswaRawanCount = 0;
        if ($activeTahunAjaran) {
            $siswaRawanCount = Nilai::join('mapel', 'nilais.id_mapel', '=', 'mapel.id_mapel')
                ->where('nilais.id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran)
                ->where('nilais.semester', $semesterAktif)
                ->whereColumn('nilais.nilai_akhir', '<', 'mapel.kkm')
                ->distinct('nilais.id_user')
                ->count('nilais.id_user');
        }

        // ── Chart: Distribusi Siswa per Kelas ──
        $distribusiKelas = Kelas::withCount('siswas')
            ->orderBy('nama_kelas')
            ->get()
            ->map(fn($k) => ['nama' => $k->nama_kelas, 'total' => $k->siswas_count]);

        // ── Chart: Rasio Jenis Kelamin ──
        $rasioJK = Siswa::select('jenis_kelamin', DB::raw('count(*) as total'))
            ->groupBy('jenis_kelamin')
            ->pluck('total', 'jenis_kelamin');

        // ── Chart: Rata-rata Nilai Akhir per Mapel ──
        $rataMapel = collect();
        if ($activeTahunAjaran) {
            $rataMapel = Nilai::join('mapel', 'nilais.id_mapel', '=', 'mapel.id_mapel')
                ->where('nilais.id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran)
                ->where('nilais.semester', $semesterAktif)
                ->select('mapel.nama_mapel', DB::raw('ROUND(AVG(nilais.nilai_akhir), 1) as rata_rata'))
                ->groupBy('mapel.nama_mapel')
                ->orderBy('rata_rata', 'desc')
                ->get();
        }

        // ── Chart: Statistik Siswa per Angkatan ──
        $angkatanStats = Siswa::query()
            ->select(['id_user', 'created_at'])
            ->get()
            ->groupBy(fn($s) => optional($s->created_at)?->format('Y') ?? 'N/A')
            ->map(fn($rows, $year) => ['angkatan' => (string) $year, 'total' => $rows->count()])
            ->sortBy('angkatan')
            ->values();

        // ── Chart: Distribusi Minat (Hasil AI) ──
        $distribusiMinat = Rekomendasi::select('minat_utama', DB::raw('count(*) as total'))
            ->whereNotNull('minat_utama')
            ->groupBy('minat_utama')
            ->orderByDesc('total')
            ->limit(8)
            ->pluck('total', 'minat_utama');

        // ── Stat: Status Analisis AI ──
        $totalAnalyzed = Rekomendasi::distinct('id_user')->count('id_user');
        $analysisPercent = $totalStudents > 0 ? round(($totalAnalyzed / $totalStudents) * 100) : 0;

        // ── Stat: Progress Kenaikan Kelas ──
        $kenaikanProcessed = 0;
        $kenaikanTotal = 0;
        if ($activeTahunAjaran) {
            $kenaikanTotal = KenaikanKelas::where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran)->count();
            $kenaikanProcessed = KenaikanKelas::where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran)
                ->where('is_processed', true)
                ->count();
        }

        return view('dashboard.index', compact(
            'activeTahunAjaran',
            'semesterLabel',
            'totalStudents',
            'totalTeachers',
            'totalClasses',
            'totalMapel',
            'kelasTanpaWali',
            'siswaRawanCount',
            'distribusiKelas',
            'rasioJK',
            'rataMapel',
            'angkatanStats',
            'distribusiMinat',
            'totalAnalyzed',
            'analysisPercent',
            'kenaikanProcessed',
            'kenaikanTotal',
        ));
    }
}
