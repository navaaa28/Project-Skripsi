<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Siswa;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.index', [
            'totalStudents' => Siswa::count(),
            'totalTeachers' => Guru::count(),
            'totalClasses' => Kelas::count(),
        ]);
    }
}
