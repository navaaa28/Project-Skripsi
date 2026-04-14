<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rekomendasi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'rekomendasis';
    protected $primaryKey = 'id_rekom';

    protected $fillable = [
        'id_user',
        'id_kelas',
        'id_tahun_ajaran',
        'semester',
        'minat_utama',
        'bakat_potensial',
        'confidence_score',
        'persentase_minat',
        'persentase_bakat',
        'minat_json',
        'bakat_json',
        'analisis_tren',
        'ringkasan_non_akademik',
        'saran_pengembangan',
        'tgl_analisis',
    ];

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'id_tahun_ajaran', 'id_tahun_ajaran');
    }

    protected $casts = [
        'minat_json' => 'array',
        'bakat_json' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class , 'id_user', 'id_user');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class , 'id_user', 'id_user');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class , 'id_kelas', 'id_kelas');
    }
}
