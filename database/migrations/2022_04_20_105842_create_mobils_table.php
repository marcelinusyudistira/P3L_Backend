<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mobils', function (Blueprint $table) {
            $table->id('id_mobil');
            $table->integer('id_mitra')->nullable();
            $table->foreign('id_mitra')
                  ->references('id_mitra')
                  ->on('mitras')
                  ->onDelete('cascade');
            $table->string('nama_mobil');
            $table->string('plat_nomor');
            $table->string('tipe_mobil');
            $table->string('transmisi');
            $table->string('jenis_bbm');
            $table->float('volume_bbm',4,2);
            $table->string('warna_mobil');
            $table->integer('kapasitas');
            $table->text('fasilitas');
            $table->string('foto');
            $table->date('tanggal_servis');
            $table->integer('harga_sewa');
            $table->string('no_stnk');
            $table->boolean('kategori_aset');
            $table->date('kontrak_mulai')->nullable();
            $table->date('kontrak_selesai')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mobils');
    }
};
