<?php

namespace App\Console\Commands;

use App\Models\Siswa;
use App\Models\Nilai;
use App\Models\NonAkademik;
use App\Services\GeminiService;
use Illuminate\Console\Command;

class TestAiAnalysis extends Command
{
    protected $signature = 'ai:test {--siswa= : ID siswa (opsional, default: siswa pertama yang punya nilai)}';
    protected $description = 'Test analisis AI dengan data siswa nyata dari database';

    public function handle(GeminiService $gemini): int
    {
        // Cari siswa
        $idUser = $this->option('siswa');

        if ($idUser) {
            $siswa = Siswa::with('kelas')->where('id_user', $idUser)->first();
        } else {
            // Cari siswa yang punya data nilai terbanyak
            $idUser = Nilai::whereNotNull('nilai_akhir')
                ->selectRaw('id_user, count(*) as cnt')
                ->groupBy('id_user')
                ->orderByDesc('cnt')
                ->value('id_user');

            $siswa = $idUser ? Siswa::with('kelas')->where('id_user', $idUser)->first() : null;
        }

        if (!$siswa) {
            $this->error('❌ Tidak ada siswa dengan data nilai yang ditemukan.');
            return 1;
        }

        $this->info("👤 Siswa  : {$siswa->nama_siswa}");
        $this->info("🏫 Kelas  : " . ($siswa->kelas?->nama_kelas ?? '-'));
        $this->newLine();

        // Build payload
        $payload = $this->buildPayload($siswa, (int) $siswa->id_user);

        $this->line('📦 <fg=cyan>Payload yang dikirim ke AI:</>');
        $this->line(json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        $this->newLine();

        $this->info('⏳ Menghubungi AI...');
        $start = microtime(true);
        $result = $gemini->analyze($payload);
        $elapsed = round(microtime(true) - $start, 2);

        if (!$result) {
            $this->error("❌ AI tidak mengembalikan hasil. Cek log Laravel untuk detail.");
            return 1;
        }

        $this->info("✅ Berhasil! ({$elapsed}s)");
        $this->newLine();

        // Tampilkan hasil dengan format rapi
        $this->line('<fg=yellow>═══════════ HASIL ANALISIS AI ═══════════</>');

        $this->line('<fg=green>📌 MINAT:</>');
        foreach ($result['minat'] ?? [] as $i => $m) {
            $no = $i + 1;
            $this->line("  {$no}. {$m['nama']} — {$m['persentase']}% (confidence: {$m['confidence']}%)");
        }

        $this->newLine();
        $this->line('<fg=green>🌟 BAKAT POTENSIAL:</>');
        foreach ($result['bakat'] ?? [] as $i => $b) {
            $no = $i + 1;
            $this->line("  {$no}. {$b['nama']} — {$b['persentase']}% (confidence: {$b['confidence']}%)");
        }

        $this->newLine();
        $this->line('<fg=cyan>📈 ANALISIS TREN:</>');
        $this->line("  " . wordwrap($result['analisis_tren'] ?? '-', 90, "\n  "));

        $this->newLine();
        $this->line('<fg=cyan>📝 RINGKASAN NON-AKADEMIK:</>');
        $this->line("  " . wordwrap($result['ringkasan_non_akademik'] ?? '-', 90, "\n  "));

        $this->newLine();
        $this->line('<fg=magenta>💡 SARAN PENGEMBANGAN (+ Proyeksi Profesi):</>');
        $this->line("  " . wordwrap($result['saran_pengembangan'] ?? '-', 90, "\n  "));

        $this->newLine();
        $this->line('<fg=green>📝 TIPS PENINGKATAN:</>');
        $this->line("  " . wordwrap($result['tips_peningkatan'] ?? '-', 90, "\n  "));

        $this->newLine();
        $this->line('<fg=yellow>═════════════════════════════════════════</>');

        return 0;
    }

    private function buildPayload(Siswa $siswa, int $idUser): array
    {
        $nilai = Nilai::with('mapel')
            ->where('id_user', $idUser)
            ->orderBy('semester')
            ->get();

        $riwayatAkademik = [];
        foreach ($nilai->groupBy('semester') as $semester => $rows) {
            $mapel = [];
            foreach ($rows as $row) {
                if ($row->mapel && $row->nilai_akhir !== null) {
                    $mapel[$row->mapel->nama_mapel] = [
                        'nilai_akhir' => round($row->nilai_akhir, 1),
                        'kkm' => $row->mapel->kkm ?? 75,
                    ];
                }
            }
            if (!empty($mapel)) {
                $riwayatAkademik[] = [
                    'kelas'         => $siswa->kelas?->nama_kelas,
                    'semester'      => (int) $semester,
                    'mata_pelajaran' => $mapel,
                ];
            }
        }

        $nonAkademikRows = NonAkademik::where('id_user', $idUser)
            ->orderBy('semester')
            ->get();

        $riwayatNonAkademik = $nonAkademikRows->map(function ($row) use ($siswa) {
            return [
                'kelas'    => $siswa->kelas?->nama_kelas,
                'semester' => (int) $row->semester,
                'indikator' => [
                    'sikap_belajar'       => $row->sikap_belajar,
                    'keaktifan'           => $row->keaktifan,
                    'minat_ekstrakurikuler' => $row->minat_ekstrakurikuler,
                    'catatan_guru'        => $row->catatan_guru,
                ],
            ];
        })->values()->all();

        return [
            'nama_siswa'          => $siswa->nama_siswa,
            'riwayat_akademik'    => $riwayatAkademik,
            'riwayat_non_akademik' => $riwayatNonAkademik,
        ];
    }
}
