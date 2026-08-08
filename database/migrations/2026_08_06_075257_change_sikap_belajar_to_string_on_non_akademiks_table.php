<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('non_akademiks', function (Blueprint $table) {
            $table->string('sikap_belajar', 255)->nullable()->change();
            $table->string('keaktifan', 255)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('non_akademiks', function (Blueprint $table) {
            // we don't necessarily have to reverse back to tinyInteger for this test DB, 
            // but we can try to restore it
            $table->tinyInteger('sikap_belajar')->nullable()->change();
            $table->tinyInteger('keaktifan')->nullable()->change();
        });
    }
};
