<?php

namespace App\Http\Controllers;

use App\Models\TahunAjaran;
use App\Services\TahunAjaranService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TahunAjaranController extends Controller
{
    public function __construct(
        protected TahunAjaranService $tahunAjaranService,
    ) {}
    public function index()
    {
        return view('tahun-ajaran.index', [
            'tahunAjarans' => TahunAjaran::latest()->paginate(10),
        ]);
    }

    public function create()
    {
        return view('tahun-ajaran.create', [
            'suggested' => TahunAjaran::generateNext(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_tahun_ajaran' => ['required', 'string', 'max:20', 'unique:tahun_ajarans,nama_tahun_ajaran', 'regex:/^\d{4}\/\d{4}$/'],
        ], [
            'nama_tahun_ajaran.regex' => 'Format harus YYYY/YYYY, contoh: 2025/2026.',
            'nama_tahun_ajaran.unique' => 'Tahun ajaran ini sudah ada.',
        ]);

        $this->tahunAjaranService->create($data);

        return redirect()->route('admin.tahun-ajaran.index')->with('status', 'Tahun ajaran berhasil ditambahkan.');
    }

    public function edit(TahunAjaran $tahun_ajaran)
    {
        return view('tahun-ajaran.edit', [
            'tahunAjaran' => $tahun_ajaran,
        ]);
    }

    public function update(Request $request, TahunAjaran $tahun_ajaran)
    {
        $data = $request->validate([
            'nama_tahun_ajaran' => [
                'required', 'string', 'max:20',
                Rule::unique('tahun_ajarans', 'nama_tahun_ajaran')->ignore($tahun_ajaran->id_tahun_ajaran, 'id_tahun_ajaran'),
                'regex:/^\d{4}\/\d{4}$/',
            ],
        ], [
            'nama_tahun_ajaran.regex' => 'Format harus YYYY/YYYY, contoh: 2025/2026.',
            'nama_tahun_ajaran.unique' => 'Tahun ajaran ini sudah ada.',
        ]);

        $this->tahunAjaranService->update($tahun_ajaran, $data);

        return redirect()->route('admin.tahun-ajaran.index')->with('status', 'Tahun ajaran berhasil diperbarui.');
    }

    public function destroy(TahunAjaran $tahun_ajaran)
    {
        $this->tahunAjaranService->delete($tahun_ajaran);

        return redirect()->route('admin.tahun-ajaran.index')->with('status', 'Tahun ajaran berhasil dihapus.');
    }

    /**
     * Set tahun ajaran tertentu sebagai aktif.
     */
    public function activate(TahunAjaran $tahun_ajaran)
    {
        $this->tahunAjaranService->activate($tahun_ajaran);

        return redirect()->route('admin.tahun-ajaran.index')
            ->with('status', "Tahun ajaran {$tahun_ajaran->nama_tahun_ajaran} diaktifkan.");
    }

    /**
     * Nonaktifkan tahun ajaran tertentu.
     */
    public function deactivate(TahunAjaran $tahun_ajaran)
    {
        $this->tahunAjaranService->deactivate($tahun_ajaran);

        return redirect()->route('admin.tahun-ajaran.index')
            ->with('status', "Tahun ajaran {$tahun_ajaran->nama_tahun_ajaran} dinonaktifkan.");
    }

    /**
     * Ganti semester aktif untuk tahun ajaran tertentu.
     */
    public function toggleSemester(TahunAjaran $tahun_ajaran)
    {
        $this->tahunAjaranService->toggleSemester($tahun_ajaran);

        $semesterName = $tahun_ajaran->semester_aktif == 1 ? 'Ganjil' : 'Genap';
        return redirect()->route('admin.tahun-ajaran.index')
            ->with('status', "Semester aktif untuk {$tahun_ajaran->nama_tahun_ajaran} secara global diubah menjadi {$semesterName}.");
    }
}
