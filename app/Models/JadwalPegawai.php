<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalPegawai extends Model
{
    use HasFactory;

    protected $table = 'jadwal_pegawais';
    protected $primaryKey = 'id_jadwal';

    protected $fillable = [
        'hari','shift',
    ];
}
