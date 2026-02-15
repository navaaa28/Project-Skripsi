<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Nilai extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'nilais';
    protected $primaryKey = 'id_nilai';

    protected $fillable = [
        'id_user',
        'id_mapel',
        'id_kelas',
        'semester',
        'nilai_tugas',
        'nilai_uts',
        'nilai_uas',
        'nilai_akhir',
    ];

    public function user()
    {
        return $this->belongsTo(User::class , 'id_user', 'id_user');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class , 'id_user', 'id_user');
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class , 'id_mapel', 'id_mapel');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class , 'id_kelas', 'id_kelas');
    }
}
