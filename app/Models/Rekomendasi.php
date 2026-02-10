<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rekomendasi extends Model
{
    use HasFactory;

    protected $table = 'rekomendasis';
    protected $primaryKey = 'id_rekom';

    protected $fillable = [
        'id_user',
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

    protected $casts = [
        'minat_json' => 'array',
        'bakat_json' => 'array',
    ];
}
