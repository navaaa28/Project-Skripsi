<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DokumenSiswa extends Model
{
    use HasFactory;

    protected $table = 'dokumen_siswas';

    protected $fillable = [
        'id_user',
        'jenis_dokumen',
        'nama_file',
        'path',
        'mime_type',
        'size',
    ];

    public const JENIS = [
        'kk' => 'Kartu Keluarga',
        'akte' => 'Akte Kelahiran',
        'ijazah' => 'Ijazah',
        'ktp_ortu' => 'KTP Orang Tua',
        'foto' => 'Pas Foto',
        'lainnya' => 'Dokumen Lainnya',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class , 'id_user', 'id_user');
    }

    public function getLabelAttribute(): string
    {
        return self::JENIS[$this->jenis_dokumen] ?? $this->jenis_dokumen;
    }
}
