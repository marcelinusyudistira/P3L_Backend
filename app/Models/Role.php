<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $table = 'role';
    protected $primaryKey = 'id_role';

    protected $fillable = [
        'nama_role',
    ];
    
}
