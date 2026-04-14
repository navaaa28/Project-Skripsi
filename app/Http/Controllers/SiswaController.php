<?php

namespace App\Http\Controllers;

use App\Imports\SiswaImport;
use App\Models\Siswa;
use App\Models\User;
use App\Services\SiswaService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class SiswaController extends Controller
{
    public function __construct(
        protected SiswaService $siswaService,
    ) {}
    public function index(Request $request)
    {
        $siswas = $this->siswaService->list(
            $request->only(['kelas', 'q']),
            10
        )->appends($request->only(['kelas', 'q']));

        return view('siswa.index', [
            'siswas' => $siswas,
            'kelasOptions' => \App\Models\Kelas::orderBy('nama_kelas')->get(),
        ]);
    }

    public function create()
    {
        return view('siswa.create', [
            'users' => User::where('role', 'siswa')->orderBy('username')->get(),
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls'],
        ]);

        $import = new SiswaImport();
        Excel::import($import, $request->file('file'));

        return back()->with([
            'status' => "Import selesai. Berhasil: {$import->created}, Gagal: " . count($import->errors),
            'import_errors' => $import->errors,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_user' => ['required', 'exists:users,id_user', 'unique:siswas,id_user'],
            'nipd' => ['nullable', 'string', 'max:20', 'unique:siswas,nipd'],
            'nisn' => ['nullable', 'string', 'max:20', 'unique:siswas,nisn'],
            'nama_siswa' => ['required', 'string', 'max:100'],
            'jenis_kelamin' => ['nullable', 'string', 'max:10'],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tgl_lahir' => ['nullable', 'date'],
            'rombel_saat_ini' => ['nullable', 'string', 'max:50'],
            'id_kelas' => ['nullable', 'exists:kelas,id_kelas'],
        ], [
            'id_user.unique' => 'User siswa sudah terdaftar.',
            'nipd.unique' => 'NIPD sudah terdaftar, tidak boleh duplikat.',
            'nisn.unique' => 'NISN sudah terdaftar, tidak boleh duplikat.',
        ]);

        $this->siswaService->create($data);

        return redirect()->route('admin.siswa.index');
    }

    public function show(Siswa $siswa)
    {
        return view('siswa.show', compact('siswa'));
    }

    public function edit(Siswa $siswa)
    {
        return view('siswa.edit', [
            'siswa' => $siswa,
            'users' => User::where('role', 'siswa')->orderBy('username')->get(),
        ]);
    }

    public function update(Request $request, Siswa $siswa)
    {
        $data = $request->validate([
            'id_user' => ['required', 'exists:users,id_user', Rule::unique('siswas', 'id_user')->ignore($siswa->id_user, 'id_user')],
            'nipd' => ['nullable', 'string', 'max:20', Rule::unique('siswas', 'nipd')->ignore($siswa->id_user, 'id_user')],
            'nisn' => ['nullable', 'string', 'max:20', Rule::unique('siswas', 'nisn')->ignore($siswa->id_user, 'id_user')],
            'nama_siswa' => ['required', 'string', 'max:100'],
            'jenis_kelamin' => ['nullable', 'string', 'max:10'],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tgl_lahir' => ['nullable', 'date'],
            'rombel_saat_ini' => ['nullable', 'string', 'max:50'],
            'id_kelas' => ['nullable', 'exists:kelas,id_kelas'],
        ], [
            'id_user.unique' => 'User siswa sudah dipakai oleh data siswa lain.',
            'nipd.unique' => 'NIPD sudah dipakai oleh data siswa lain.',
            'nisn.unique' => 'NISN sudah dipakai oleh data siswa lain.',
        ]);

        $this->siswaService->update($siswa, $data);

        return redirect()->route('admin.siswa.index');
    }

    public function destroy(Siswa $siswa)
    {
        $this->siswaService->delete($siswa);

        return redirect()->route('admin.siswa.index');
    }
}
