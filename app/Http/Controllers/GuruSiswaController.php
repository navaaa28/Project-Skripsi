<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Nilai;
use App\Models\Rekomendasi;
use App\Models\DokumenSiswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GuruSiswaController extends Controller
{
    public function index(Request $request)
    {
        $guru = Auth::user()?->guru;
        $kelasOptions = $guru ? $guru->kelasWali()->orderBy('nama_kelas')->get() : collect();
        $kelasIds = $kelasOptions->pluck('id_kelas');

        // Auto-select first kelas if guru has classes and none is chosen yet
        $selectedKelas = $request->input('kelas');
        if (!$request->filled('kelas') && !$request->filled('q') && $kelasOptions->isNotEmpty()) {
            $selectedKelas = $kelasOptions->first()->id_kelas;
        }

        $query = Siswa::with('kelas')->whereIn('id_kelas', $kelasIds)->orderBy('nama_siswa');

        if ($selectedKelas) {
            $kelasId = (int) $selectedKelas;
            if ($kelasIds->contains($kelasId)) {
                $query->where('id_kelas', $kelasId);
            }
        }

        if ($request->filled('q')) {
            $q = $request->string('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('nama_siswa', 'like', "%{$q}%")
                    ->orWhere('nipd', 'like', "%{$q}%")
                    ->orWhere('nisn', 'like', "%{$q}%");
            });
        }

        // When showing all classes (via "Semua Kelas"), group by kelas
        $showAll = $request->input('kelas') === 'all';
        $groupedSiswas = null;

        if ($showAll) {
            $query = Siswa::with('kelas')->whereIn('id_kelas', $kelasIds)->orderBy('nama_siswa');
            if ($request->filled('q')) {
                $q = $request->string('q');
                $query->where(function ($sub) use ($q) {
                    $sub->where('nama_siswa', 'like', "%{$q}%")
                        ->orWhere('nipd', 'like', "%{$q}%")
                        ->orWhere('nisn', 'like', "%{$q}%");
                });
            }
            $selectedKelas = 'all';

            $allSiswas = $query->get();
            $groupedSiswas = $allSiswas->groupBy(fn ($s) => $s->kelas?->nama_kelas ?? 'Tanpa Kelas');
        }

        return view('guru.siswa.index', [
            'siswas' => $groupedSiswas ? null : $query->paginate(15)->appends($request->only(['q', 'kelas'])),
            'groupedSiswas' => $groupedSiswas,
            'kelasOptions' => $kelasOptions,
            'selectedKelas' => $selectedKelas,
        ]);
    }

    public function show(Request $request, Siswa $siswa)
    {
        $guru = Auth::user()?->guru;
        $kelasIds = $guru ? $guru->kelasWali()->pluck('id_kelas') : collect();

        if (!$kelasIds->contains($siswa->id_kelas)) {
            abort(403);
        }

        $selectedKelas = null;
        if ($request->filled('kelas')) {
            $kelasId = (int) $request->input('kelas');
            if ($kelasIds->contains($kelasId)) {
                $selectedKelas = $kelasId;
            }
        }

        $selectedSemester = null;
        if ($request->filled('semester')) {
            $semester = (int) $request->input('semester');
            if ($semester > 0) {
                $selectedSemester = $semester;
            }
        }

        $selectedTahunAjaran = null;
        if ($request->filled('tahun_ajaran')) {
            $taId = (int) $request->input('tahun_ajaran');
            if ($taId > 0) {
                $selectedTahunAjaran = $taId;
            }
        }

        $nilaiBase = Nilai::where('id_user', $siswa->id_user)
            ->whereIn('id_kelas', $kelasIds);

        if ($selectedKelas !== null) {
            $nilaiBase->where('id_kelas', $selectedKelas);
        }

        if ($selectedTahunAjaran !== null) {
            $nilaiBase->where('id_tahun_ajaran', $selectedTahunAjaran);
        }

        $nilai = (clone $nilaiBase)
            ->with(['mapel', 'tahunAjaran'])
            ->when($selectedSemester !== null, fn($q) => $q->where('semester', $selectedSemester))
            ->orderBy('semester')
            ->orderBy('id_mapel')
            ->get();

        $nilaiSemesters = (clone $nilaiBase)
            ->select('semester')
            ->distinct()
            ->pluck('semester');

        $rekomSemesters = Rekomendasi::where('id_user', $siswa->id_user)
            ->whereIn('id_kelas', $kelasIds)
            ->when($selectedKelas !== null, fn($q) => $q->where('id_kelas', $selectedKelas))
            ->select('semester')
            ->distinct()
            ->pluck('semester');

        $semesterOptions = $nilaiSemesters
            ->merge($rekomSemesters)
            ->unique()
            ->sort()
            ->values();

        $nilaiKelasIds = Nilai::where('id_user', $siswa->id_user)
            ->whereIn('id_kelas', $kelasIds)
            ->select('id_kelas')
            ->distinct()
            ->pluck('id_kelas');

        $rekomKelasIds = Rekomendasi::where('id_user', $siswa->id_user)
            ->whereIn('id_kelas', $kelasIds)
            ->select('id_kelas')
            ->distinct()
            ->pluck('id_kelas');

        $availableKelasIds = $nilaiKelasIds->merge($rekomKelasIds)->unique()->values();
        if ($availableKelasIds->isEmpty() && $kelasIds->contains($siswa->id_kelas)) {
            $availableKelasIds = collect([$siswa->id_kelas]);
        }

        $kelasOptions = Kelas::whereIn('id_kelas', $availableKelasIds)
            ->orderBy('nama_kelas')
            ->get();

        $rekomendasi = Rekomendasi::where('id_user', $siswa->id_user)
            ->whereIn('id_kelas', $kelasIds)
            ->when($selectedKelas !== null, fn($q) => $q->where('id_kelas', $selectedKelas))
            ->when($selectedSemester !== null, fn($q) => $q->where('semester', $selectedSemester))
            ->when($selectedTahunAjaran !== null, fn($q) => $q->where('id_tahun_ajaran', $selectedTahunAjaran))
            ->orderByDesc('semester')
            ->orderByDesc('tgl_analisis')
            ->first();

        $tahunAjaranOptions = TahunAjaran::orderByDesc('id_tahun_ajaran')->get();

        return view('guru.siswa.show', [
            'siswa' => $siswa->load('kelas'),
            'nilai' => $nilai,
            'rekomendasi' => $rekomendasi,
            'kelasOptions' => $kelasOptions,
            'semesterOptions' => $semesterOptions,
            'selectedKelas' => $selectedKelas,
            'selectedSemester' => $selectedSemester,
            'tahunAjaranOptions' => $tahunAjaranOptions,
            'selectedTahunAjaran' => $selectedTahunAjaran,
        ]);
    }

    public function dokumen(Siswa $siswa)
    {
        $guru = Auth::user()?->guru;
        $kelasIds = $guru ? $guru->kelasWali()->pluck('id_kelas') : collect();

        if (!$kelasIds->contains($siswa->id_kelas)) {
            abort(403);
        }

        $dokumen = DokumenSiswa::where('id_user', $siswa->id_user)
            ->orderByDesc('created_at')
            ->get();

        return view('guru.siswa.dokumen', [
            'siswa' => $siswa->load('kelas'),
            'dokumen' => $dokumen,
        ]);
    }

    public function downloadDokumen(Siswa $siswa, DokumenSiswa $dokumen)
    {
        $guru = Auth::user()?->guru;
        $kelasIds = $guru ? $guru->kelasWali()->pluck('id_kelas') : collect();

        if (!$kelasIds->contains($siswa->id_kelas)) {
            abort(403);
        }

        if ($dokumen->id_user !== $siswa->id_user) {
            abort(403);
        }

        return Storage::disk('public')->download($dokumen->path, $dokumen->nama_file);
    }
}
