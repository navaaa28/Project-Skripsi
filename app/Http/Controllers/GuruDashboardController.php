<?php

namespace App\Http\Controllers;

use App\Models\Nilai;
use App\Models\NonAkademik;
use App\Models\Rekomendasi;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GuruDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $guru = $user?->guru;
        $activeTahunAjaran = TahunAjaran::getActive();
        $semesterAktif = $activeTahunAjaran?->semester_aktif ?? 1;
        $semesterLabel = $semesterAktif == 2 ? 'Genap' : 'Ganjil';

        // ── Logika Periode Data: tampilkan semester terakhir yang sudah selesai ──
        // Semester Genap aktif → tampilkan data Semester Ganjil (tahun ajaran sama)
        // Semester Ganjil aktif → tampilkan data Semester Genap (tahun ajaran sebelumnya)
        if ($semesterAktif == 2 && $activeTahunAjaran) {
            $dataTahunAjaran = $activeTahunAjaran;
            $dataSemester = 1;
            $dataPeriodeLabel = 'Semester Ganjil ' . $activeTahunAjaran->nama_tahun_ajaran;
        } else {
            // Cari tahun ajaran sebelumnya
            $prevTahunAjaran = $activeTahunAjaran
                ? TahunAjaran::where('id_tahun_ajaran', '<', $activeTahunAjaran->id_tahun_ajaran)
                    ->orderByDesc('id_tahun_ajaran')
                    ->first()
                : null;
            $dataTahunAjaran = $prevTahunAjaran;
            $dataSemester = 2;
            $dataPeriodeLabel = $prevTahunAjaran
                ? 'Semester Genap ' . $prevTahunAjaran->nama_tahun_ajaran
                : null;
        }

        // ── Kelas Perwalian ──
        $kelas = $guru
            ? $guru->kelasWali()->withCount('siswas')->orderBy('nama_kelas')->get()
            : collect();

        $kelasIds = $kelas->pluck('id_kelas')->toArray();
        $totalSiswaPerwalian = $kelas->sum('siswas_count');

        // ── Stat: Siswa sudah dianalisis (semester aktif saat ini) ──
        $totalAnalyzed = 0;
        if ($activeTahunAjaran && count($kelasIds) > 0) {
            $totalAnalyzed = Rekomendasi::whereIn('id_kelas', $kelasIds)
                ->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran)
                ->where('semester', $semesterAktif)
                ->distinct('id_user')
                ->count('id_user');
        }

        // ── Stat: Progress Observasi Non-Akademik (semester aktif saat ini) ──
        $totalObservasi = 0;
        if ($activeTahunAjaran && $guru && count($kelasIds) > 0) {
            $totalObservasi = NonAkademik::where('id_guru', $guru->id_user)
                ->whereIn('id_kelas', $kelasIds)
                ->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran)
                ->where('semester', $semesterAktif)
                ->distinct('id_user')
                ->count('id_user');
        }

        // ── Ranking 10 Siswa Terbaik (semester terakhir selesai) ──
        $topRanking = collect();
        if ($dataTahunAjaran && count($kelasIds) > 0) {
            $topRanking = Nilai::join('siswas', 'nilais.id_user', '=', 'siswas.id_user')
                ->join('kelas', 'siswas.id_kelas', '=', 'kelas.id_kelas')
                ->whereIn('nilais.id_kelas', $kelasIds)
                ->where('nilais.id_tahun_ajaran', $dataTahunAjaran->id_tahun_ajaran)
                ->where('nilais.semester', $dataSemester)
                ->select(
                    'siswas.nama_siswa',
                    'kelas.nama_kelas',
                    DB::raw('ROUND(AVG(nilais.nilai_akhir), 2) as rata_rata')
                )
                ->groupBy('siswas.id_user', 'siswas.nama_siswa', 'kelas.nama_kelas')
                ->orderByDesc('rata_rata')
                ->limit(10)
                ->get();
        }

        // ── Siswa Rawan / Nilai di Bawah KKM (semester terakhir selesai) ──
        $siswaRawan = collect();
        if ($dataTahunAjaran && count($kelasIds) > 0) {
            $siswaRawan = Nilai::join('siswas', 'nilais.id_user', '=', 'siswas.id_user')
                ->join('kelas', 'siswas.id_kelas', '=', 'kelas.id_kelas')
                ->join('mapel', 'nilais.id_mapel', '=', 'mapel.id_mapel')
                ->whereIn('nilais.id_kelas', $kelasIds)
                ->where('nilais.id_tahun_ajaran', $dataTahunAjaran->id_tahun_ajaran)
                ->where('nilais.semester', $dataSemester)
                ->whereColumn('nilais.nilai_akhir', '<', 'mapel.kkm')
                ->select(
                    'siswas.nama_siswa',
                    'kelas.nama_kelas',
                    'mapel.nama_mapel',
                    'nilais.nilai_akhir',
                    'mapel.kkm'
                )
                ->orderBy('siswas.nama_siswa')
                ->get();
        }

        // ── Chart: Rata-rata Nilai per Mapel (semester terakhir selesai) ──
        $rataMapel = collect();
        if ($dataTahunAjaran && count($kelasIds) > 0) {
            $rataMapel = Nilai::join('mapel', 'nilais.id_mapel', '=', 'mapel.id_mapel')
                ->whereIn('nilais.id_kelas', $kelasIds)
                ->where('nilais.id_tahun_ajaran', $dataTahunAjaran->id_tahun_ajaran)
                ->where('nilais.semester', $dataSemester)
                ->select('mapel.nama_mapel', DB::raw('ROUND(AVG(nilais.nilai_akhir), 1) as rata_rata'))
                ->groupBy('mapel.nama_mapel')
                ->orderByDesc('rata_rata')
                ->get();
        }

        // ── Riwayat Analisis AI Terbaru (semua periode, kelas saat ini) ──
        $riwayatAnalisis = collect();
        if (count($kelasIds) > 0) {
            $riwayatAnalisis = Rekomendasi::whereIn('id_kelas', $kelasIds)
                ->with(['siswa', 'kelas'])
                ->orderByDesc('tgl_analisis')
                ->limit(5)
                ->get();
        }

        return view('guru.dashboard.index', compact(
            'user',
            'kelas',
            'activeTahunAjaran',
            'semesterLabel',
            'dataPeriodeLabel',
            'totalSiswaPerwalian',
            'totalAnalyzed',
            'totalObservasi',
            'topRanking',
            'siswaRawan',
            'rataMapel',
            'riwayatAnalisis',
        ));
    }
}
