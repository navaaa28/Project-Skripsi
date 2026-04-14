<?php

namespace App\Services;

use App\Models\Siswa;
use App\Models\User;
use App\Models\Kelas;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class SiswaService
{
    /**
     * List siswa dengan filter dan pagination.
     */
    public function list(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Siswa::with(['user', 'kelas'])
            ->leftJoin('kelas', 'siswas.id_kelas', '=', 'kelas.id_kelas')
            ->select('siswas.*')
            ->orderByRaw("
                CASE
                    WHEN kelas.nama_kelas REGEXP '[0-9]+' THEN CAST(REGEXP_SUBSTR(kelas.nama_kelas, '[0-9]+') AS UNSIGNED)
                    ELSE 999
                END ASC
            ")
            ->orderBy('kelas.nama_kelas')
            ->orderBy('siswas.nama_siswa');

        if (!empty($filters['kelas'])) {
            $query->where('siswas.id_kelas', $filters['kelas']);
        }

        if (!empty($filters['q'])) {
            $q = $filters['q'];
            $query->where(function ($sub) use ($q) {
                $sub->where('siswas.nama_siswa', 'like', "%{$q}%")
                    ->orWhere('siswas.nipd', 'like', "%{$q}%")
                    ->orWhere('siswas.nisn', 'like', "%{$q}%");
            });
        }

        return $query->paginate($perPage);
    }

    /**
     * Cari siswa berdasarkan id_user.
     */
    public function find(int $idUser): ?Siswa
    {
        return Siswa::with(['user', 'kelas'])->where('id_user', $idUser)->first();
    }

    /**
     * Buat data siswa baru.
     */
    public function create(array $data): Siswa
    {
        return Siswa::create($data);
    }

    /**
     * Update data siswa.
     */
    public function update(Siswa $siswa, array $data): Siswa
    {
        $siswa->update($data);
        return $siswa->fresh();
    }

    /**
     * Hapus siswa.
     */
    public function delete(Siswa $siswa): void
    {
        $siswa->delete();
    }

    /**
     * Update profil siswa (untuk mobile app).
     * Hanya field yang diizinkan untuk diedit sendiri oleh siswa.
     */
    public function updateProfil(int $idUser, array $data): ?Siswa
    {
        $siswa = Siswa::where('id_user', $idUser)->first();
        if (!$siswa) {
            return null;
        }

        $allowedFields = ['nama_siswa', 'jenis_kelamin', 'tempat_lahir', 'tgl_lahir'];
        $filtered = array_intersect_key($data, array_flip($allowedFields));

        $siswa->update($filtered);
        return $siswa->fresh();
    }
}
