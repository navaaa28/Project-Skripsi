<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('non_akademiks', function (Blueprint $table) {
            $table->id('id_observasi');
            $table->foreignId('id_user')->constrained('users', 'id_user')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('id_guru')->constrained('gurus', 'id_user')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('id_kelas')->constrained('kelas', 'id_kelas')->cascadeOnUpdate()->restrictOnDelete();
            $table->tinyInteger('semester');
            $table->tinyInteger('sikap_belajar')->nullable();
            $table->tinyInteger('keaktifan')->nullable();
            $table->text('catatan_guru')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('non_akademiks');
    }
};
