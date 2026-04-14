<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KenaikanKelas extends Model
{
    protected $table = 'kenaikan_kelas';

    protected $fillable = [
        'id_user',
        'id_kelas_asal',
        'id_kelas_tujuan',
        'id_tahun_ajaran',
        'id_guru',
        'status',
        'catatan',
        'is_processed',
    ];

    protected $casts = [
        'is_processed' => 'boolean',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_user', 'id_user');
    }

    public function kelasAsal()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas_asal', 'id_kelas');
    }

    public function kelasTujuan()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas_tujuan', 'id_kelas');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'id_tahun_ajaran', 'id_tahun_ajaran');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'id_user');
    }
}
