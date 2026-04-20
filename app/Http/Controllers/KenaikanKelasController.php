<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\KenaikanKelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KenaikanKelasController extends Controller
{
    /**
     * Tentukan tahun ajaran yang harus diproses.
     * Prioritas: tahun ajaran yang masih punya keputusan kenaikan belum diproses.
     */
    private function resolveProcessingTahunAjaran(): ?TahunAjaran
    {
        $pendingYearId = KenaikanKelas::where('is_processed', false)->max('id_tahun_ajaran');

        if ($pendingYearId) {
            return TahunAjaran::find($pendingYearId);
        }

        return TahunAjaran::getActive();
    }

    /**
     * Tampilkan rekap keputusan wali kelas.
     */
    public function index()
    {
        $tahunAjaranAktif = TahunAjaran::getActive();
        $tahunAjaranProses = $this->resolveProcessingTahunAjaran();
        $kelasNumberOrder = DB::connection()->getDriverName() === 'pgsql'
            ? "CASE WHEN nama_kelas ~ '[0-9]+' THEN CAST(substring(nama_kelas from '[0-9]+') AS INTEGER) ELSE 999 END ASC"
            : "CASE WHEN nama_kelas REGEXP '[0-9]+' THEN CAST(REGEXP_SUBSTR(nama_kelas, '[0-9]+') AS UNSIGNED) ELSE 999 END ASC";

        $kelasList = Kelas::with('waliGuru')
            ->withCount('siswas')
            ->orderByRaw($kelasNumberOrder)
            ->get();

        // Ambil data keputusan guru per kelas
        $rekapPerKelas = [];
        if ($tahunAjaranProses) {
            foreach ($kelasList as $kelas) {
                $decisions = KenaikanKelas::with('siswa')
                    ->where('id_kelas_asal', $kelas->id_kelas)
                    ->where('id_tahun_ajaran', $tahunAjaranProses->id_tahun_ajaran)
                    ->where('is_processed', false)
                    ->get();

                $rekapPerKelas[$kelas->id_kelas] = [
                    'kelas' => $kelas,
                    'total_siswa' => $kelas->siswas_count,
                    'total_decided' => $decisions->count(),
                    'naik' => $decisions->where('status', 'naik')->count(),
                    'tidak_naik' => $decisions->where('status', 'tidak_naik')->count(),
                    'lulus' => $decisions->where('status', 'lulus')->count(),
                    'decisions' => $decisions,
                ];
            }
        }

        return view('kenaikan-kelas.index', [
            'kelasList' => $kelasList,
            'tahunAjaranAktif' => $tahunAjaranAktif,
            'tahunAjaranProses' => $tahunAjaranProses,
            'rekapPerKelas' => $rekapPerKelas,
        ]);
    }

    /**
     * Proses kenaikan kelas berdasarkan keputusan guru.
     */
    public function process(Request $request)
    {
        $request->validate([
            'konfirmasi' => ['required', 'in:YA'],
        ], [
            'konfirmasi.required' => 'Anda harus mengetik "YA" untuk konfirmasi.',
            'konfirmasi.in' => 'Ketik "YA" (huruf kapital) untuk konfirmasi.',
        ]);

        $tahunAjaranProses = $this->resolveProcessingTahunAjaran();
        if (!$tahunAjaranProses) {
            return back()->with('error', 'Tidak ada data kenaikan kelas yang bisa diproses.');
        }

        $decisions = KenaikanKelas::where('id_tahun_ajaran', $tahunAjaranProses->id_tahun_ajaran)
            ->where('is_processed', false)
            ->get();

        if ($decisions->isEmpty()) {
            return back()->with('error', 'Tidak ada keputusan yang perlu diproses.');
        }

        $naik = 0;
        $tidakNaik = 0;
        $lulus = 0;

        DB::transaction(function () use ($decisions, &$naik, &$tidakNaik, &$lulus) {
            foreach ($decisions as $decision) {
                $siswa = Siswa::where('id_user', $decision->id_user)->first();
                if (!$siswa) continue;

                if ($decision->status === 'naik' && $decision->id_kelas_tujuan) {
                    $siswa->update([
                        'id_kelas' => $decision->id_kelas_tujuan,
                        'rombel_saat_ini' => $decision->kelasTujuan?->nama_kelas ?? $siswa->rombel_saat_ini,
                    ]);
                    $naik++;
                } elseif ($decision->status === 'lulus') {
                    $siswa->update([
                        'id_kelas' => null,
                        'rombel_saat_ini' => 'LULUS',
                    ]);
                    $lulus++;
                } else {
                    // tidak_naik → tetap di kelas yang sama
                    $tidakNaik++;
                }

                $decision->update(['is_processed' => true]);
            }

            // Setelah akhir semester genap / proses kenaikan selesai,
            // penetapan wali kelas direset agar admin dapat menyusun ulang rombel tahun ajaran berikutnya.
            Kelas::query()->update(['id_guru' => null]);
        });

        Log::info('Kenaikan kelas diproses', compact('naik', 'tidakNaik', 'lulus'));

        return redirect()->route('admin.kenaikan-kelas.index')
            ->with('status', "Kenaikan kelas tahun ajaran {$tahunAjaranProses->nama_tahun_ajaran} berhasil diproses! {$naik} naik kelas, {$lulus} lulus, {$tidakNaik} tetap di kelas. Semua wali kelas telah dikosongkan dan perlu diatur ulang.");
    }
}
