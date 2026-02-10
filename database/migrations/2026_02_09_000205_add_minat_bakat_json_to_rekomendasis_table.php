<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rekomendasis', function (Blueprint $table) {
            $table->json('minat_json')->nullable()->after('bakat_potensial');
            $table->json('bakat_json')->nullable()->after('minat_json');
        });
    }

    public function down(): void
    {
        Schema::table('rekomendasis', function (Blueprint $table) {
            $table->dropColumn(['minat_json', 'bakat_json']);
        });
    }
};
