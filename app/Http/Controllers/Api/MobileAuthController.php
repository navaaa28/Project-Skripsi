<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MobileAuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('username', $data['username'])->first();
        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Username atau kata sandi salah.'], 401);
        }

        if ($user->role !== 'siswa') {
            return response()->json(['message' => 'Akun tidak diizinkan untuk aplikasi mobile.'], 403);
        }

        $token = $user->createToken('mobile')->plainTextToken;
        $siswa = $user->siswa?->load('kelas');

        return response()->json([
            'token' => $token,
            'user' => [
                'id_user' => $user->id_user,
                'username' => $user->username,
                'role' => $user->role,
            ],
            'siswa' => $siswa ? [
                'id_user' => $siswa->id_user,
                'nama_siswa' => $siswa->nama_siswa,
                'nipd' => $siswa->nipd,
                'nisn' => $siswa->nisn,
                'rombel_saat_ini' => $siswa->rombel_saat_ini,
                'kelas' => $siswa->kelas?->nama_kelas,
            ] : null,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();
        return response()->json(['message' => 'Logout berhasil.']);
    }
}
