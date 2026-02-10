<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Nilai extends Model
{
    use HasFactory;

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

    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'id_mapel', 'id_mapel');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }
}
