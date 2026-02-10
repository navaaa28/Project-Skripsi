<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rekomendasis', function (Blueprint $table) {
            $table->id('id_rekom');
            $table->foreignId('id_user')->constrained('users', 'id_user')->cascadeOnUpdate()->restrictOnDelete();
            $table->tinyInteger('semester');
            $table->string('minat_utama', 50)->nullable();
            $table->string('bakat_potensial', 50)->nullable();
            $table->float('confidence_score')->nullable();
            $table->float('persentase_minat')->nullable();
            $table->float('persentase_bakat')->nullable();
            $table->text('analisis_tren')->nullable();
            $table->text('ringkasan_non_akademik')->nullable();
            $table->text('saran_pengembangan')->nullable();
            $table->date('tgl_analisis')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekomendasis');
    }
};
