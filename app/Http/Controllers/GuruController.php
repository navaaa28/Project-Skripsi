<?php

namespace App\Http\Controllers;

use App\Imports\GuruImport;
use App\Models\Guru;
use App\Models\Mapel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class GuruController extends Controller
{
    public function index()
    {
        return view('guru.index', [
            'gurus' => Guru::with(['user', 'kelasWali'])->latest()->paginate(10),
        ]);
    }

    public function create()
    {
        return view('guru.create', [
            'users' => User::where('role', 'guru')->orderBy('username')->get(),
            'mapelOptions' => Mapel::orderBy('nama_mapel')->get(),
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls'],
        ]);

        $import = new GuruImport();
        Excel::import($import, $request->file('file'));

        return back()->with([
            'status' => "Import selesai. Berhasil: {$import->created}, Gagal: " . count($import->errors),
            'import_errors' => $import->errors,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nip' => ['nullable', 'string', 'max:30', 'unique:gurus,nip'],
            'nama_guru' => ['required', 'string', 'max:100'],
            'jenis_kelamin' => ['nullable', Rule::in(['L', 'P'])],
            'mapel_utama' => ['nullable', 'string', 'max:50'],
        ], [
            'nip.unique' => 'NIP sudah terdaftar, gunakan NIP lain.',
        ]);

        $username = $this->generateUsername($data['nama_guru']);

        \Illuminate\Support\Facades\DB::transaction(function () use ($username, $data) {
            $user = User::create([
                'username' => $username,
                'email' => null,
                'password' => \Illuminate\Support\Facades\Hash::make('guru12345'),
                'role' => 'guru',
            ]);
            
            $data['id_user'] = $user->id_user;
            Guru::create($data);
        });

        return redirect()->route('admin.guru.index');
    }

    private function generateUsername(string $nama): string
    {
        $first = trim(strtok($nama, ' '));
        $base = \Illuminate\Support\Str::slug($first, '_');
        if ($base === '') {
            $base = 'guru';
        }
        $base = \Illuminate\Support\Str::limit($base, 20, '');
        $username = $base . '_cicadas';
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $counter++;
            $suffix = '_' . $counter;
            $username = \Illuminate\Support\Str::limit($base, 20 - strlen($suffix), '') . '_cicadas' . $suffix;
        }

        return $username;
    }

    public function show(Guru $guru)
    {
        return view('guru.show', compact('guru'));
    }

    public function edit(Guru $guru)
    {
        return view('guru.edit', [
            'guru' => $guru,
            'users' => User::where('role', 'guru')->orderBy('username')->get(),
            'mapelOptions' => Mapel::orderBy('nama_mapel')->get(),
        ]);
    }

    public function update(Request $request, Guru $guru)
    {
        $data = $request->validate([
            'id_user' => ['required', 'exists:users,id_user', Rule::unique('gurus', 'id_user')->ignore($guru->id_user, 'id_user')],
            'nip' => ['nullable', 'string', 'max:30', Rule::unique('gurus', 'nip')->ignore($guru->id_user, 'id_user')],
            'nama_guru' => ['required', 'string', 'max:100'],
            'jenis_kelamin' => ['nullable', Rule::in(['L', 'P'])],
            'mapel_utama' => ['nullable', 'string', 'max:50'],
        ], [
            'id_user.unique' => 'User guru sudah dipakai oleh data guru lain.',
            'nip.unique' => 'NIP sudah dipakai oleh data guru lain.',
        ]);

        $guru->update($data);

        return redirect()->route('admin.guru.index');
    }

    public function destroy(Guru $guru)
    {
        $guru->delete();

        return redirect()->route('admin.guru.index');
    }
}
