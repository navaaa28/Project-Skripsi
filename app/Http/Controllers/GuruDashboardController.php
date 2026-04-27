<?php

namespace App\Http\Controllers;

use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Auth;

class GuruDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $guru = $user?->guru;
        $kelas = $guru ? $guru->kelasWali()->withCount('siswas')->orderBy('nama_kelas')->get() : collect();
        $activeTahunAjaran = TahunAjaran::getActive();
        $semesterAktifLabel = $activeTahunAjaran?->semester_aktif == 2 ? 'Genap' : 'Ganjil';

        return view('guru.dashboard.index', [
            'user' => $user,
            'kelas' => $kelas,
            'activeTahunAjaran' => $activeTahunAjaran,
            'semesterAktifLabel' => $semesterAktifLabel,
        ]);
    }
}
