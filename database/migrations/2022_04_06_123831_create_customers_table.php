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
        Schema::create('customers', function (Blueprint $table) {
            $table->string('id_customer');
            $table->string('nama_customer');
            $table->date('tanggal_lahir');
            $table->string('gender');
            $table->text('alamat');
            $table->string('nomor_telepon');
            $table->string('foto');
            $table->string('ktp');
            $table->string('sim');
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
        Schema::dropIfExists('customers');
    }
};
