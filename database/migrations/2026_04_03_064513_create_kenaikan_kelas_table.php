<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kenaikan_kelas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user')->comment('FK ke siswa');
            $table->unsignedBigInteger('id_kelas_asal');
            $table->unsignedBigInteger('id_kelas_tujuan')->nullable()->comment('null = lulus');
            $table->unsignedBigInteger('id_tahun_ajaran');
            $table->unsignedBigInteger('id_guru')->comment('wali kelas yang memutuskan');
            $table->enum('status', ['naik', 'tidak_naik', 'lulus'])->default('naik');
            $table->text('catatan')->nullable();
            $table->boolean('is_processed')->default(false)->comment('sudah diproses admin?');
            $table->timestamps();

            $table->unique(['id_user', 'id_tahun_ajaran'], 'uk_siswa_tahun');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kenaikan_kelas');
    }
};
