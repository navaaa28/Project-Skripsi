<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('siswas', function (Blueprint $table) {
            $table->foreignId('id_user')
                ->primary()
                ->constrained('users', 'id_user')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->string('nipd', 20)->nullable()->unique();
            $table->string('nisn', 20)->nullable()->unique();
            $table->string('nama_siswa', 100);
            $table->string('jenis_kelamin', 10)->nullable();
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->string('rombel_saat_ini', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};
