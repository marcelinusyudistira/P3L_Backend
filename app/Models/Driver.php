<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Driver extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $table = 'drivers';
    protected $primaryKey = 'id_driver';

    protected $fillable = [
        'id_driver','nama_driver','tanggal_lahir','gender','alamat','nomor_telepon','email','foto','harga_driver','status_driver',
        'bahasa','rerata_rating','sim_driver','surat_bebas_napza','surat_kesehatan_jiwa','surat_kesehatan_jasmani','skck',
    ];

    public function getCreatedAtAttribute(){
        if(!is_null($this->attributes['created_at'])){
            return Carbon::parse($this->attributes['created_at'])->format('Y-m-d H:i:s');
        }
    }

    public function getUpdatedAtAttribute(){
        if(!is_null($this->attributes['updated_at'])){
            return Carbon::parse($this->attributes['updated_at'])->format('Y-m-d H:i:s');
        }
    }
}
