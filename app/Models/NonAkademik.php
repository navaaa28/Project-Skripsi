<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class NonAkademik extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'non_akademiks';
    protected $primaryKey = 'id_observasi';

    protected $fillable = [
        'id_user',
        'id_guru',
        'id_kelas',
        'id_tahun_ajaran',
        'semester',
        'sikap_belajar',
        'keaktifan',
        'minat_ekstrakurikuler',
        'catatan_guru',
    ];

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'id_tahun_ajaran', 'id_tahun_ajaran');
    }

    public function user()
    {
        return $this->belongsTo(User::class , 'id_user', 'id_user');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class , 'id_user', 'id_user');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class , 'id_guru', 'id_user');
    }
}
