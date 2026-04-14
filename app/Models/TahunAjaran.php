<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TahunAjaran extends Model
{
    use HasFactory;

    protected $table = 'tahun_ajarans';
    protected $primaryKey = 'id_tahun_ajaran';

    protected $fillable = [
        'nama_tahun_ajaran',
        'is_active',
        'semester_aktif',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope: hanya tahun ajaran yang aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Ambil tahun ajaran yang sedang aktif.
     */
    public static function getActive(): ?self
    {
        return static::active()->first();
    }

    /**
     * Generate nama tahun ajaran berikutnya berdasarkan data terakhir.
     * Misalnya jika terakhir "2025/2026", maka generate "2026/2027".
     */
    public static function generateNext(): string
    {
        $last = static::orderByDesc('id_tahun_ajaran')->first();

        if ($last && preg_match('/^(\d{4})\/(\d{4})$/', $last->nama_tahun_ajaran, $m)) {
            $startYear = (int) $m[2];
            return $startYear . '/' . ($startYear + 1);
        }

        // Default: tahun ini / tahun depan
        $year = (int) date('Y');
        return $year . '/' . ($year + 1);
    }
}
