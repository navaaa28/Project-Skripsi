<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rekomendasis', function (Blueprint $table) {
            $table->float('persentase_minat')->nullable()->after('confidence_score');
            $table->float('persentase_bakat')->nullable()->after('persentase_minat');
        });
    }

    public function down(): void
    {
        Schema::table('rekomendasis', function (Blueprint $table) {
            $table->dropColumn(['persentase_minat', 'persentase_bakat']);
        });
    }
};
