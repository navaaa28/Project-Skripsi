<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('rekomendasis', 'id_kelas')) {
            Schema::table('rekomendasis', function (Blueprint $table) {
                $table->foreignId('id_kelas')
                    ->nullable()
                    ->after('id_user')
                    ->constrained('kelas', 'id_kelas')
                    ->cascadeOnUpdate()
                    ->restrictOnDelete();
            });
        }

        // Backfill existing rows so historical data can be filtered by class.
        DB::statement("
            UPDATE rekomendasis r
            SET id_kelas = COALESCE(
                (
                    SELECT n.id_kelas
                    FROM nilais n
                    WHERE n.id_user = r.id_user
                      AND n.semester = r.semester
                      AND n.deleted_at IS NULL
                    LIMIT 1
                ),
                (
                    SELECT s.id_kelas
                    FROM siswas s
                    WHERE s.id_user = r.id_user
                      AND s.deleted_at IS NULL
                    LIMIT 1
                )
            )
            WHERE r.id_kelas IS NULL
        ");

        if (!Schema::hasColumn('rekomendasis', 'id_kelas')) {
            return;
        }

        Schema::table('rekomendasis', function (Blueprint $table) {
            $table->index(['id_user', 'id_kelas', 'semester'], 'rekom_user_kelas_semester_idx');
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('rekomendasis', 'id_kelas')) {
            return;
        }

        Schema::table('rekomendasis', function (Blueprint $table) {
            $table->dropIndex('rekom_user_kelas_semester_idx');
            $table->dropConstrainedForeignId('id_kelas');
        });
    }
};
