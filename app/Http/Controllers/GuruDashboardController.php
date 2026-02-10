<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class GuruDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $guru = $user?->guru;
        $kelas = $guru ? $guru->kelasWali()->withCount('siswas')->orderBy('nama_kelas')->get() : collect();

        return view('guru.dashboard.index', [
            'user' => $user,
            'kelas' => $kelas,
        ]);
    }
}
