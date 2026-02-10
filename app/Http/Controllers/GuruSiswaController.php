<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Nilai;
use App\Models\Rekomendasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuruSiswaController extends Controller
{
    public function index(Request $request)
    {
        $guru = Auth::user()?->guru;
        $kelasIds = $guru ? $guru->kelasWali()->pluck('id_kelas') : collect();

        $query = Siswa::with('kelas')->whereIn('id_kelas', $kelasIds)->orderBy('nama_siswa');

        if ($request->filled('q')) {
            $q = $request->string('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('nama_siswa', 'like', "%{$q}%")
                    ->orWhere('nipd', 'like', "%{$q}%")
                    ->orWhere('nisn', 'like', "%{$q}%");
            });
        }

        return view('guru.siswa.index', [
            'siswas' => $query->paginate(10)->appends($request->only(['q'])),
        ]);
    }

    public function show(Siswa $siswa)
    {
        $guru = Auth::user()?->guru;
        $kelasIds = $guru ? $guru->kelasWali()->pluck('id_kelas') : collect();

        if (!$kelasIds->contains($siswa->id_kelas)) {
            abort(403);
        }

        $nilai = Nilai::with('mapel')
            ->where('id_user', $siswa->id_user)
            ->orderBy('semester')
            ->orderBy('id_mapel')
            ->get();

        return view('guru.siswa.show', [
            'siswa' => $siswa->load('kelas'),
            'nilai' => $nilai,
            'rekomendasi' => Rekomendasi::where('id_user', $siswa->id_user)
                ->orderByDesc('semester')
                ->first(),
        ]);
    }
}
