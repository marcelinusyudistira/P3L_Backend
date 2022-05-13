<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Mobil extends Model
{
    use HasFactory;

    protected $table = 'mobils';
    protected $primaryKey = 'id_mobil';

    protected $fillable = [
        'id_mobil','id_mitra','nama_mobil','plat_nomor','tipe_mobil','transmisi','jenis_bbm','volume_bbm','warna_mobil','kapasitas',
        'fasilitas','foto','tanggal_servis','harga_sewa','no_stnk','kategori_aset','kontrak_mulai','kontrak_selesai',
    ];

    protected $appends = ['photo_mobil'];
    public function getPhotoMobilAttribute()
    {
        return asset($this->foto);
    }

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
