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
        Schema::create('pegawais', function (Blueprint $table) {
            $table->string('id_pegawai');
            $table->integer('id_role');
            $table->foreign('id_role')
                  ->references('id_role')
                  ->on('roles')
                  ->onDelete('cascade');
            $table->string('nama_pegawai');
            $table->string('tanggal_lahir');
            $table->string('gender');
            $table->text('alamat');
            $table->string('nomor_telepon');
            $table->string('email')->unique();
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
        Schema::dropIfExists('pegawais');
    }
};
