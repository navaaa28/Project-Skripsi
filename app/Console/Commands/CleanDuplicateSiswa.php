<?php

namespace App\Console\Commands;

use App\Models\Siswa;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanDuplicateSiswa extends Command
{
    protected $signature   = 'siswa:clean-duplicates {--dry-run : Tampilkan saja tanpa hapus}';
    protected $description = 'Deteksi dan bersihkan siswa duplikat berdasarkan NIPD, NISN, atau Nama+TglLahir';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('🔍 Mode DRY-RUN — tidak ada data yang dihapus.');
        } else {
            $this->warn('⚠️  Mode HAPUS aktif — duplikat akan dihapus dari database!');
            if (!$this->confirm('Lanjutkan?')) {
                $this->info('Dibatalkan.');
                return 0;
            }
        }

        $this->newLine();
        $totalDeleted = 0;

        // ── 1. Duplikat berdasarkan NIPD ───────────────────────────────
        $this->info('📋 Mengecek duplikat berdasarkan NIPD...');
        $nipds = Siswa::whereNotNull('nipd')
            ->select('nipd', DB::raw('count(*) as cnt'))
            ->groupBy('nipd')
            ->havingRaw('count(*) > 1')
            ->pluck('nipd');

        foreach ($nipds as $nipd) {
            $rows = Siswa::where('nipd', $nipd)->orderBy('id_user')->get();
            $keep = $rows->first();
            $dupes = $rows->slice(1);

            $this->line("  NIPD <fg=yellow>{$nipd}</> — simpan id_user={$keep->id_user} ({$keep->nama_siswa}), hapus: " .
                $dupes->pluck('id_user')->implode(', '));

            if (!$dryRun) {
                foreach ($dupes as $d) {
                    User::find($d->id_user)?->delete(); // cascade hapus siswa juga
                    $totalDeleted++;
                }
            }
        }

        // ── 2. Duplikat berdasarkan NISN ───────────────────────────────
        $this->info('📋 Mengecek duplikat berdasarkan NISN...');
        $nisns = Siswa::whereNotNull('nisn')
            ->select('nisn', DB::raw('count(*) as cnt'))
            ->groupBy('nisn')
            ->havingRaw('count(*) > 1')
            ->pluck('nisn');

        foreach ($nisns as $nisn) {
            $rows = Siswa::where('nisn', $nisn)->orderBy('id_user')->get();
            $keep = $rows->first();
            $dupes = $rows->slice(1);

            $this->line("  NISN <fg=yellow>{$nisn}</> — simpan id_user={$keep->id_user} ({$keep->nama_siswa}), hapus: " .
                $dupes->pluck('id_user')->implode(', '));

            if (!$dryRun) {
                foreach ($dupes as $d) {
                    User::find($d->id_user)?->delete();
                    $totalDeleted++;
                }
            }
        }

        // ── 3. Duplikat berdasarkan Nama + Tanggal Lahir ───────────────
        $this->info('📋 Mengecek duplikat berdasarkan Nama + Tanggal Lahir...');
        $dupeGroups = Siswa::select('nama_siswa', 'tgl_lahir', DB::raw('count(*) as cnt'))
            ->groupBy('nama_siswa', 'tgl_lahir')
            ->havingRaw('count(*) > 1')
            ->get();

        foreach ($dupeGroups as $group) {
            $rows = Siswa::where('nama_siswa', $group->nama_siswa)
                ->where('tgl_lahir', $group->tgl_lahir)
                ->orderBy('id_user')
                ->get();
            $keep  = $rows->first();
            $dupes = $rows->slice(1);

            $this->line("  Nama <fg=yellow>{$group->nama_siswa}</> ({$group->tgl_lahir}) — simpan id_user={$keep->id_user}, hapus: " .
                $dupes->pluck('id_user')->implode(', '));

            if (!$dryRun) {
                foreach ($dupes as $d) {
                    // Jika sudah punya nilai/rekomendasi, pindahkan ke id_user yang dipertahankan
                    DB::table('nilai')->where('id_user', $d->id_user)->update(['id_user' => $keep->id_user]);
                    DB::table('non_akademik')->where('id_user', $d->id_user)->update(['id_user' => $keep->id_user]);
                    DB::table('rekomendasi')->where('id_user', $d->id_user)->update(['id_user' => $keep->id_user]);

                    User::find($d->id_user)?->delete();
                    $totalDeleted++;
                }
            }
        }

        $this->newLine();

        if ($nipds->isEmpty() && $nisns->isEmpty() && $dupeGroups->isEmpty()) {
            $this->info('✅ Tidak ada duplikat ditemukan.');
        } elseif ($dryRun) {
            $this->warn('ℹ️  Dry-run selesai. Jalankan tanpa --dry-run untuk menghapus duplikat.');
        } else {
            $this->info("✅ Selesai. Total {$totalDeleted} akun duplikat dihapus.");
        }

        return 0;
    }
}
