<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DokumenSiswa;
use App\Services\DokumenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MobileDokumenController extends Controller
{
    public function __construct(
        protected DokumenService $dokumenService,
    ) {}

    /**
     * List dokumen milik siswa yang login.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $dokumen = $this->dokumenService->listForUser($user->id_user);

        $mapped = $dokumen->map(function ($d) {
            return [
                'id' => $d->id,
                'jenis_dokumen' => $d->jenis_dokumen,
                'label' => $d->label,
                'nama_file' => $d->nama_file,
                'mime_type' => $d->mime_type,
                'size' => $d->size,
                'url' => Storage::disk('public')->url($d->path),
                'created_at' => $d->created_at->toIso8601String(),
            ];
        });

        return response()->json([
            'dokumen' => $mapped,
            'jenis_tersedia' => DokumenSiswa::JENIS,
        ]);
    }

    /**
     * Upload dokumen baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis_dokumen' => 'required|string|in:' . implode(',', array_keys(DokumenSiswa::JENIS)),
            'file' => 'required|file|max:2048|mimes:pdf,jpg,jpeg,png',
        ]);

        $user = $request->user();
        $dokumen = $this->dokumenService->store(
            $user->id_user,
            $request->jenis_dokumen,
            $request->file('file')
        );

        return response()->json([
            'message' => 'Dokumen berhasil diupload.',
            'dokumen' => [
                'id' => $dokumen->id,
                'jenis_dokumen' => $dokumen->jenis_dokumen,
                'label' => $dokumen->label,
                'nama_file' => $dokumen->nama_file,
                'created_at' => $dokumen->created_at->toDateTimeString(),
            ],
        ], 201);
    }

    /**
     * Update dokumen milik sendiri (PUT /api/mobile/dokumen/{id}).
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $dokumen = DokumenSiswa::where('id_user', $user->id_user)->findOrFail($id);

        $request->validate([
            'jenis_dokumen' => 'sometimes|string|in:' . implode(',', array_keys(DokumenSiswa::JENIS)),
            'file' => 'sometimes|file|max:2048|mimes:pdf,jpg,jpeg,png',
        ]);

        $updated = $this->dokumenService->update(
            $dokumen,
            $request->only('jenis_dokumen'),
            $request->file('file')
        );

        return response()->json([
            'message' => 'Dokumen berhasil diperbarui.',
            'dokumen' => [
                'id' => $updated->id,
                'jenis_dokumen' => $updated->jenis_dokumen,
                'label' => $updated->label,
                'nama_file' => $updated->nama_file,
                'mime_type' => $updated->mime_type,
                'size' => $updated->size,
                'url' => Storage::disk('public')->url($updated->path),
                'created_at' => $updated->created_at->toDateTimeString(),
            ],
        ]);
    }

    /**
     * Hapus dokumen milik sendiri.
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $dokumen = DokumenSiswa::where('id_user', $user->id_user)->findOrFail($id);

        $this->dokumenService->destroy($dokumen);

        return response()->json(['message' => 'Dokumen berhasil dihapus.']);
    }
}
