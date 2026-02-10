<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->foreignId('id_user')
                ->primary()
                ->constrained('users', 'id_user')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->string('nama_admin', 100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
