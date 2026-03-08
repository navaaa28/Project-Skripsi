<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DokumenSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MobileDokumenController extends Controller
{
    /**
     * List dokumen milik siswa yang login.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $dokumen = DokumenSiswa::where('id_user', $user->id_user)
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($d) {
            return [
            'id' => $d->id,
            'jenis_dokumen' => $d->jenis_dokumen,
            'label' => $d->label,
            'nama_file' => $d->nama_file,
            'mime_type' => $d->mime_type,
            'size' => $d->size,
            'url' => Storage::disk('public')->url($d->path), // Local URL
            'created_at' => $d->created_at->toIso8601String(),
            ];
        });

        return response()->json([
            'dokumen' => $dokumen,
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
            'file' => 'required|file|max:2048|mimes:pdf,jpg,jpeg,png', // Max 2MB
        ]);

        $user = $request->user();
        $file = $request->file('file');

        $path = $file->store("dokumen_siswa/{$user->id_user}", 'public');

        $dokumen = DokumenSiswa::create([
            'id_user' => $user->id_user,
            'jenis_dokumen' => $request->jenis_dokumen,
            'nama_file' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ]);

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
     * Hapus dokumen milik sendiri.
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $dokumen = DokumenSiswa::where('id_user', $user->id_user)->findOrFail($id);

        Storage::disk('public')->delete($dokumen->path);
        $dokumen->delete();

        return response()->json(['message' => 'Dokumen berhasil dihapus.']);
    }
}
