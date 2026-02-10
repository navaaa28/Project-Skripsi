<?php

namespace App\Http\Controllers;

use App\Imports\GuruImport;
use App\Models\Guru;
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
            'id_user' => ['required', 'exists:users,id_user', 'unique:gurus,id_user'],
            'nip' => ['nullable', 'string', 'max:30', 'unique:gurus,nip'],
            'nama_guru' => ['required', 'string', 'max:100'],
            'jenis_kelamin' => ['nullable', 'string', 'max:10'],
            'mapel_utama' => ['nullable', 'string', 'max:50'],
        ]);

        Guru::create($data);

        return redirect()->route('admin.guru.index');
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
        ]);
    }

    public function update(Request $request, Guru $guru)
    {
        $data = $request->validate([
            'id_user' => ['required', 'exists:users,id_user', Rule::unique('gurus', 'id_user')->ignore($guru->id_user, 'id_user')],
            'nip' => ['nullable', 'string', 'max:30', Rule::unique('gurus', 'nip')->ignore($guru->id_user, 'id_user')],
            'nama_guru' => ['required', 'string', 'max:100'],
            'jenis_kelamin' => ['nullable', 'string', 'max:10'],
            'mapel_utama' => ['nullable', 'string', 'max:50'],
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
