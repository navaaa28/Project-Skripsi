<?php

namespace App\Services;

use App\Models\Kelas;
use App\Models\TahunAjaran;
use Illuminate\Support\Collection;

class TahunAjaranService
{
    /**
     * List semua tahun ajaran.
     */
    public function list(): Collection
    {
        return TahunAjaran::orderByDesc('id_tahun_ajaran')->get();
    }

    /**
     * Ambil tahun ajaran yang aktif.
     */
    public function getActive(): ?TahunAjaran
    {
        return TahunAjaran::getActive();
    }

    /**
     * Buat tahun ajaran baru.
     */
    public function create(array $data): TahunAjaran
    {
        return TahunAjaran::create($data);
    }

    /**
     * Update tahun ajaran.
     */
    public function update(TahunAjaran $tahunAjaran, array $data): TahunAjaran
    {
        $tahunAjaran->update($data);
        return $tahunAjaran->fresh();
    }

    /**
     * Hapus tahun ajaran.
     */
    public function delete(TahunAjaran $tahunAjaran): void
    {
        $tahunAjaran->delete();
    }

    /**
     * Set tahun ajaran tertentu sebagai aktif (nonaktifkan semua yang lain).
     */
    public function activate(TahunAjaran $tahunAjaran): void
    {
        $activeTahunAjaran = TahunAjaran::getActive();

        TahunAjaran::where('is_active', true)->update(['is_active' => false]);
        $tahunAjaran->update(['is_active' => true]);

        if (!$activeTahunAjaran || $activeTahunAjaran->id_tahun_ajaran !== $tahunAjaran->id_tahun_ajaran) {
            Kelas::query()->update(['id_guru' => null]);
        }
    }

    /**
     * Nonaktifkan tahun ajaran tertentu.
     */
    public function deactivate(TahunAjaran $tahunAjaran): void
    {
        if (!$tahunAjaran->is_active) {
            return;
        }

        $tahunAjaran->update(['is_active' => false]);
    }

    /**
     * Ganti semester aktif (1 <-> 2).
     */
    public function toggleSemester(TahunAjaran $tahunAjaran): void
    {
        $tahunAjaran->update([
            'semester_aktif' => $tahunAjaran->semester_aktif == 1 ? 2 : 1
        ]);
    }
}
