<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumen_siswas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user');
            $table->string('jenis_dokumen');       // kk, akte, ijazah, ktp_ortu, foto, lainnya
            $table->string('nama_file');            // original filename
            $table->string('path');                 // storage path
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->timestamps();

            $table->foreign('id_user')->references('id_user')->on('siswas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumen_siswas');
    }
};
