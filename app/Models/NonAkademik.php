<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NonAkademik extends Model
{
    use HasFactory;

    protected $table = 'non_akademiks';
    protected $primaryKey = 'id_observasi';

    protected $fillable = [
        'id_user',
        'id_guru',
        'id_kelas',
        'semester',
        'sikap_belajar',
        'keaktifan',
        'minat_ekstrakurikuler',
        'catatan_guru',
    ];
}
