<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mapel extends Model
{
    use HasFactory;

    protected $table = 'mapel';
    protected $primaryKey = 'id_mapel';

    protected $fillable = [
        'nama_mapel',
        'kkm',
    ];
}
