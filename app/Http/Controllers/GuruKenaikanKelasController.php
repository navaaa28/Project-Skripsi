<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\KenaikanKelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuruKenaikanKelasController extends Controller
{
    /**
     * Halaman utama: wali kelas melihat daftar siswa dan menentukan naik/tidak.
     */
    public function index()
    {
        $guru = Auth::user()->guru;
        if (!$guru) {
            abort(403, 'Anda bukan guru.');
        }

        // Cari kelas yang dia jadi wali
        $kelasWali = Kelas::where('id_guru', $guru->id_user)->first();

        if (!$kelasWali) {
            return view('guru.kenaikan-kelas.index', [
                'kelasWali' => null,
                'siswas' => collect(),
                'tahunAjaranAktif' => TahunAjaran::getActive(),
                'existingDecisions' => collect(),
                'allDecided' => false,
            ]);
        }

        $tahunAjaranAktif = TahunAjaran::getActive();
        $siswas = Siswa::where('id_kelas', $kelasWali->id_kelas)
            ->orderBy('nama_siswa')
            ->get();

        // Cari keputusan yang sudah ada untuk tahun ajaran aktif
        $existingDecisions = collect();
        if ($tahunAjaranAktif) {
            $existingDecisions = KenaikanKelas::where('id_tahun_ajaran', $tahunAjaranAktif->id_tahun_ajaran)
                ->where('id_kelas_asal', $kelasWali->id_kelas)
                ->where('id_guru', $guru->id_user)
                ->pluck('status', 'id_user');
        }

        // Cari kelas tujuan (kelas selanjutnya)
        $kelasTujuan = $this->findNextKelas($kelasWali);

        return view('guru.kenaikan-kelas.index', [
            'kelasWali' => $kelasWali,
            'kelasTujuan' => $kelasTujuan,
            'siswas' => $siswas,
            'tahunAjaranAktif' => $tahunAjaranAktif,
            'existingDecisions' => $existingDecisions,
            'allDecided' => $siswas->count() > 0 && $existingDecisions->count() === $siswas->count(),
        ]);
    }

    /**
     * Simpan keputusan naik/tidak naik per siswa.
     */
    public function store(Request $request)
    {
        $guru = Auth::user()->guru;
        $kelasWali = Kelas::where('id_guru', $guru->id_user)->firstOrFail();
        $tahunAjaranAktif = TahunAjaran::getActive();

        if (!$tahunAjaranAktif) {
            return back()->with('error', 'Tidak ada tahun ajaran aktif.');
        }

        $request->validate([
            'keputusan' => ['required', 'array'],
            'keputusan.*' => ['required', 'in:naik,tidak_naik'],
            'catatan' => ['nullable', 'array'],
            'catatan.*' => ['nullable', 'string', 'max:500'],
        ]);

        $kelasTujuan = $this->findNextKelas($kelasWali);
        $isKelasTeringgi = $kelasTujuan === null;

        foreach ($request->keputusan as $idUser => $status) {
            // Pastikan siswa memang di kelas wali ini
            $siswa = Siswa::where('id_user', $idUser)->where('id_kelas', $kelasWali->id_kelas)->first();
            if (!$siswa) continue;

            $finalStatus = $status;
            if ($status === 'naik' && $isKelasTeringgi) {
                $finalStatus = 'lulus';
            }

            KenaikanKelas::updateOrCreate(
                [
                    'id_user' => $idUser,
                    'id_tahun_ajaran' => $tahunAjaranAktif->id_tahun_ajaran,
                ],
                [
                    'id_kelas_asal' => $kelasWali->id_kelas,
                    'id_kelas_tujuan' => ($finalStatus === 'naik') ? $kelasTujuan?->id_kelas : null,
                    'id_guru' => $guru->id_user,
                    'status' => $finalStatus,
                    'catatan' => $request->catatan[$idUser] ?? null,
                    'is_processed' => false,
                ]
            );
        }

        return back()->with('status', 'Keputusan kenaikan kelas berhasil disimpan.');
    }

    /**
     * Cari kelas selanjutnya berdasarkan angka di nama kelas.
     */
    private function findNextKelas(Kelas $currentKelas): ?Kelas
    {
        preg_match('/(\d+)/', $currentKelas->nama_kelas, $matches);
        if (empty($matches[1])) return null;

        $nextNumber = ((int) $matches[1]) + 1;

        return Kelas::where('nama_kelas', 'LIKE', "%{$nextNumber}%")
            ->get()
            ->first(fn (Kelas $kelas) => preg_match("/(^|[^0-9]){$nextNumber}([^0-9]|$)/", $kelas->nama_kelas));
    }
}
