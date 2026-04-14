<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KelasController extends Controller
{
    public function index()
    {
        return view('kelas.index', [
            'kelas' => Kelas::with('waliGuru')->withCount('siswas')->latest()->paginate(10),
        ]);
    }

    public function create()
    {
        return view('kelas.create', [
            'gurus' => Guru::orderBy('nama_guru')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_kelas' => ['required', 'string', 'max:20', 'unique:kelas,nama_kelas'],
            'id_guru' => ['nullable', 'exists:gurus,id_user'],
        ]);

        Kelas::create($data);

        return redirect()->route('admin.kelas.index');
    }

    public function show(Kelas $kela)
    {
        return view('kelas.show', ['kelas' => $kela->load(['waliGuru', 'siswas'])]);
    }

    public function edit(Kelas $kela)
    {
        return view('kelas.edit', [
            'kelas' => $kela,
            'gurus' => Guru::orderBy('nama_guru')->get(),
        ]);
    }

    public function update(Request $request, Kelas $kela)
    {
        $data = $request->validate([
            'nama_kelas' => ['required', 'string', 'max:20', Rule::unique('kelas', 'nama_kelas')->ignore($kela->id_kelas, 'id_kelas')],
            'id_guru' => ['nullable', 'exists:gurus,id_user'],
        ]);

        $kela->update($data);

        return redirect()->route('admin.kelas.index');
    }

    public function destroy(Kelas $kela)
    {
        $kela->delete();

        return redirect()->route('admin.kelas.index');
    }
}
