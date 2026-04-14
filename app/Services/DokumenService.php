<?php

namespace App\Services;

use App\Models\DokumenSiswa;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class DokumenService
{
    /**
     * List dokumen milik siswa.
     */
    public function listForUser(int $idUser): Collection
    {
        return DokumenSiswa::where('id_user', $idUser)
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Upload dokumen baru.
     */
    public function store(int $idUser, string $jenisDokumen, UploadedFile $file): DokumenSiswa
    {
        $path = $file->store("dokumen_siswa/{$idUser}", 'public');

        return DokumenSiswa::create([
            'id_user' => $idUser,
            'jenis_dokumen' => $jenisDokumen,
            'nama_file' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ]);
    }

    /**
     * Update dokumen (ganti file / jenis).
     */
    public function update(DokumenSiswa $dokumen, array $data, ?UploadedFile $file = null): DokumenSiswa
    {
        if ($file) {
            // Hapus file lama
            Storage::disk('public')->delete($dokumen->path);

            $path = $file->store("dokumen_siswa/{$dokumen->id_user}", 'public');
            $dokumen->update([
                'jenis_dokumen' => $data['jenis_dokumen'] ?? $dokumen->jenis_dokumen,
                'nama_file' => $file->getClientOriginalName(),
                'path' => $path,
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
            ]);
        } else {
            $dokumen->update(array_filter([
                'jenis_dokumen' => $data['jenis_dokumen'] ?? null,
            ]));
        }

        return $dokumen->fresh();
    }

    /**
     * Hapus dokumen.
     */
    public function destroy(DokumenSiswa $dokumen): void
    {
        Storage::disk('public')->delete($dokumen->path);
        $dokumen->delete();
    }
}
