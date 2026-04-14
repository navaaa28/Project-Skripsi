<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nilais', function (Blueprint $table) {
            $table->foreignId('id_tahun_ajaran')
                ->nullable()
                ->after('id_kelas')
                ->constrained('tahun_ajarans', 'id_tahun_ajaran')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });

        Schema::table('non_akademiks', function (Blueprint $table) {
            $table->foreignId('id_tahun_ajaran')
                ->nullable()
                ->after('id_kelas')
                ->constrained('tahun_ajarans', 'id_tahun_ajaran')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });

        Schema::table('rekomendasis', function (Blueprint $table) {
            $table->foreignId('id_tahun_ajaran')
                ->nullable()
                ->after('id_kelas')
                ->constrained('tahun_ajarans', 'id_tahun_ajaran')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('nilais', function (Blueprint $table) {
            $table->dropForeign(['id_tahun_ajaran']);
            $table->dropColumn('id_tahun_ajaran');
        });

        Schema::table('non_akademiks', function (Blueprint $table) {
            $table->dropForeign(['id_tahun_ajaran']);
            $table->dropColumn('id_tahun_ajaran');
        });

        Schema::table('rekomendasis', function (Blueprint $table) {
            $table->dropForeign(['id_tahun_ajaran']);
            $table->dropColumn('id_tahun_ajaran');
        });
    }
};
