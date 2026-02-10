<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Support\Facades\Auth;

class GuruKelasController extends Controller
{
    public function show(Kelas $kelas)
    {
        $guru = Auth::user()?->guru;
        if (!$guru || $kelas->id_guru !== $guru->id_user) {
            abort(403);
        }

        return view('guru.kelas.show', [
            'kelas' => $kelas->load('siswas'),
        ]);
    }
}
