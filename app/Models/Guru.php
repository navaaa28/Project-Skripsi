<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Guru extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_user';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'id_user',
        'nip',
        'nama_guru',
        'jenis_kelamin',
        'mapel_utama',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function kelasWali()
    {
        return $this->hasMany(Kelas::class, 'id_guru', 'id_user');
    }
}
