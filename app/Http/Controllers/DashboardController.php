<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Siswa;

class DashboardController extends Controller
{
    public function index()
    {
        $angkatanStats = Siswa::query()
            ->select(['id_user', 'created_at'])
            ->get()
            ->groupBy(function ($siswa) {
                return optional($siswa->created_at)->format('Y') ?? 'Tidak Diketahui';
            })
            ->map(function ($rows, $year) {
                return [
                    'angkatan' => (string) $year,
                    'total' => $rows->count(),
                ];
            })
            ->sortBy('angkatan')
            ->values();

        if ($angkatanStats->isEmpty()) {
            $angkatanStats = collect([
                ['angkatan' => '2023', 'total' => 42],
                ['angkatan' => '2024', 'total' => 51],
                ['angkatan' => '2025', 'total' => 47],
            ]);
        }

        return view('dashboard.index', [
            'totalStudents' => Siswa::count(),
            'totalTeachers' => Guru::count(),
            'totalClasses' => Kelas::count(),
            'angkatanStats' => $angkatanStats,
        ]);
    }
}
