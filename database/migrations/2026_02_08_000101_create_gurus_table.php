<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gurus', function (Blueprint $table) {
            $table->foreignId('id_user')
                ->primary()
                ->constrained('users', 'id_user')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->string('nip', 30)->nullable()->unique();
            $table->string('nama_guru', 100);
            $table->string('jenis_kelamin', 10)->nullable();
            $table->string('mapel_utama', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gurus');
    }
};
