<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';
    protected $primaryKey = 'id_kelas';

    protected $fillable = [
        'nama_kelas',
        'id_guru',
    ];

    public function waliGuru()
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'id_user');
    }

    public function siswas()
    {
        return $this->hasMany(Siswa::class, 'id_kelas', 'id_kelas');
    }
}
