<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('non_akademiks', function (Blueprint $table) {
            $table->string('minat_ekstrakurikuler', 100)->nullable()->after('keaktifan');
        });
    }

    public function down(): void
    {
        Schema::table('non_akademiks', function (Blueprint $table) {
            $table->dropColumn('minat_ekstrakurikuler');
        });
    }
};
