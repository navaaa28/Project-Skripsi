<?php

namespace App\Console\Commands;

use App\Services\GeminiService;
use Illuminate\Console\Command;

class TestAiBelowKkm extends Command
{
    protected $signature = 'ai:test-below-kkm';
    protected $description = 'Test AI dengan data siswa dummy yang memiliki nilai di bawah KKM';

    public function handle(GeminiService $gemini): int
    {
        $payload = [
            'nama_siswa' => 'RINA MELATI',
            'riwayat_akademik' => [
                [
                    'kelas' => 'KELAS 4',
                    'semester' => 1,
                    'mata_pelajaran' => [
                        'Bahasa Indonesia' => ['nilai_akhir' => 62.5, 'kkm' => 75],
                        'Matematika' => ['nilai_akhir' => 55.0, 'kkm' => 75],
                        'Ilmu Pengetahuan Alam dan Sosial' => ['nilai_akhir' => 78.0, 'kkm' => 75],
                        'Pendidikan Agama dan Budi Pekerti' => ['nilai_akhir' => 82.0, 'kkm' => 75],
                        'Pendidikan Pancasila' => ['nilai_akhir' => 80.0, 'kkm' => 75],
                        'Bahasa Inggris' => ['nilai_akhir' => 68.0, 'kkm' => 75],
                        'Seni Rupa' => ['nilai_akhir' => 90.0, 'kkm' => 75],
                        'Pendidikan Jasmani Olahraga dan Kesehatan' => ['nilai_akhir' => 85.0, 'kkm' => 75],
                    ],
                ],
                [
                    'kelas' => 'KELAS 4',
                    'semester' => 2,
                    'mata_pelajaran' => [
                        'Bahasa Indonesia' => ['nilai_akhir' => 66.0, 'kkm' => 75],
                        'Matematika' => ['nilai_akhir' => 58.0, 'kkm' => 75],
                        'Ilmu Pengetahuan Alam dan Sosial' => ['nilai_akhir' => 80.5, 'kkm' => 75],
                        'Pendidikan Agama dan Budi Pekerti' => ['nilai_akhir' => 84.0, 'kkm' => 75],
                        'Pendidikan Pancasila' => ['nilai_akhir' => 78.0, 'kkm' => 75],
                        'Bahasa Inggris' => ['nilai_akhir' => 70.0, 'kkm' => 75],
                        'Seni Rupa' => ['nilai_akhir' => 92.0, 'kkm' => 75],
                        'Pendidikan Jasmani Olahraga dan Kesehatan' => ['nilai_akhir' => 87.0, 'kkm' => 75],
                    ],
                ],
            ],
            'riwayat_non_akademik' => [
                [
                    'kelas' => 'KELAS 4',
                    'semester' => 1,
                    'indikator' => [
                        'sikap_belajar' => 3,
                        'keaktifan' => 4,
                        'minat_ekstrakurikuler' => 'Menggambar, Tari',
                        'catatan_guru' => 'Rina sangat kreatif dalam seni, perlu bimbingan lebih di matematika.',
                    ],
                ],
            ],
        ];

        $this->info('👤 Siswa  : RINA MELATI (Data Dummy)');
        $this->info('🏫 Kelas  : KELAS 4');
        $this->newLine();

        // Tampilkan mapel di bawah KKM
        $this->line('<fg=red>⚠ Mata pelajaran di bawah KKM:</>');
        $this->line('  • Matematika: 55.0 → 58.0 (KKM 75) — selisih -17 s/d -20');
        $this->line('  • Bahasa Indonesia: 62.5 → 66.0 (KKM 75) — selisih -9 s/d -12.5');
        $this->line('  • Bahasa Inggris: 68.0 → 70.0 (KKM 75) — selisih -5 s/d -7');
        $this->newLine();

        $this->line('<fg=cyan>📦 Payload yang dikirim ke AI:</>');
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
}
