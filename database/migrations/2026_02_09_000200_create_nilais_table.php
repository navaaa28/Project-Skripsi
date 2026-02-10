<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nilais', function (Blueprint $table) {
            $table->id('id_nilai');
            $table->foreignId('id_user')->constrained('users', 'id_user')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('id_mapel')->constrained('mapel', 'id_mapel')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('id_kelas')->constrained('kelas', 'id_kelas')->cascadeOnUpdate()->restrictOnDelete();
            $table->tinyInteger('semester');
            $table->float('nilai_tugas')->nullable();
            $table->float('nilai_uts')->nullable();
            $table->float('nilai_uas')->nullable();
            $table->float('nilai_akhir')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilais');
    }
};
