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
        Schema::create('drivers', function (Blueprint $table) {
            $table->string('id_driver');
            $table->string('nama_driver');
            $table->date('tanggal_lahir');
            $table->string('gender');
            $table->text('alamat');
            $table->string('nomor_telepon');
            $table->string('email')->unique();
            $table->string('foto');
            $table->integer('harga_driver');
            $table->boolean('status_driver');
            $table->boolean('bahasa');
            $table->float('rerata_rating',2,2);
            $table->string('sim_driver');
            $table->string('surat_bebas_napza');
            $table->string('surat_kesehatan_jiwa');
            $table->string('surat_kesehatan_jasmani');
            $table->string('skck');
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
        Schema::dropIfExists('drivers');
    }
};
