<?php

namespace App\Http\Controllers;

use App\Models\Rekomendasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuruAnalisisController extends Controller
{
    public function index(Request $request)
    {
        $guru = Auth::user()?->guru;
        $kelasIds = $guru ? $guru->kelasWali()->pluck('id_kelas') : collect();

        $query = Rekomendasi::with(['siswa.kelas'])
            ->whereHas('siswa', function ($sub) use ($kelasIds) {
                $sub->whereIn('id_kelas', $kelasIds);
            })
            ->orderByDesc('tgl_analisis')
            ->orderByDesc('id_rekom');

        if ($request->filled('q')) {
            $q = $request->string('q');
            $query->whereHas('siswa', function ($sub) use ($q) {
                $sub->where('nama_siswa', 'like', "%{$q}%")
                    ->orWhere('nipd', 'like', "%{$q}%")
                    ->orWhere('nisn', 'like', "%{$q}%");
            });
        }

        if ($request->filled('semester')) {
            $query->where('semester', (int) $request->input('semester'));
        }

        return view('guru.analisis.index', [
            'riwayat' => $query->paginate(10)->appends($request->only(['q', 'semester'])),
        ]);
    }
}
