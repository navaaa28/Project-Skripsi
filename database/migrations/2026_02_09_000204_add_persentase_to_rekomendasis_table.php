<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::table('rekomendasis', function (Blueprint $table) {
        if (!Schema::hasColumn('rekomendasis', 'persentase_minat')) {
            $table->float('persentase_minat')->nullable()->after('confidence_score');
        }

        if (!Schema::hasColumn('rekomendasis', 'persentase_bakat')) {
            $table->float('persentase_bakat')->nullable()->after('persentase_minat');
        }
    });
}


public function down(): void
{
    Schema::table('rekomendasis', function (Blueprint $table) {
        if (Schema::hasColumn('rekomendasis', 'persentase_bakat')) {
            $table->dropColumn('persentase_bakat');
        }

        if (Schema::hasColumn('rekomendasis', 'persentase_minat')) {
            $table->dropColumn('persentase_minat');
        }
    });
}

};
