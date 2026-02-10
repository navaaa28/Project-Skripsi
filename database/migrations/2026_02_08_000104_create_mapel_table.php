<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mapel', function (Blueprint $table) {
            $table->id('id_mapel');
            $table->string('nama_mapel', 50);
            $table->integer('kkm');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mapel');
    }
};
