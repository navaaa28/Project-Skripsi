<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Siswa extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'id_user';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'id_user',
        'nipd',
        'nisn',
        'nama_siswa',
        'jenis_kelamin',
        'tempat_lahir',
        'tgl_lahir',
        'rombel_saat_ini',
        'id_kelas',
    ];

    protected $casts = [
        'tgl_lahir' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class , 'id_user', 'id_user');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class , 'id_kelas', 'id_kelas');
    }
}
