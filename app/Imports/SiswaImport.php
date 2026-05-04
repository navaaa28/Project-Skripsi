<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class SiswaImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    public int $created = 0;
    public array $errors = [];

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $line = $index + 2;
            $nama = trim((string) ($row['nama'] ?? ''));
            $nipd = $this->normalizeId($row['nipd'] ?? '');
            $nisn = $this->normalizeId($row['nisn'] ?? '');
            $jk = strtoupper(trim((string) ($row['jk'] ?? '')));
            $tempatLahir = trim((string) ($row['tempat_lahir'] ?? ''));
            $tglLahirRaw = $row['tanggal_lahir'] ?? null;
            $rombel = trim((string) ($row['rombel_saat_ini'] ?? ''));

            if ($nama === '') {
                $this->errors[] = "Baris {$line}: nama kosong.";
                continue;
            }

            $tglLahir = $this->parseTanggalLahir($tglLahirRaw);
            if (!$tglLahir) {
                $this->errors[] = "Baris {$line}: tanggal lahir tidak valid.";
                continue;
            }

            // ── Cek duplikasi berlapis ─────────────────────────────────────
            // 1. Cek berdasarkan NIPD (jika ada)
            if ($nipd !== '' && Siswa::where('nipd', $nipd)->exists()) {
                $this->errors[] = "Baris {$line}: NIPD {$nipd} sudah terdaftar — siswa dilewati.";
                continue;
            }

            // 2. Cek berdasarkan NISN (jika ada)
            if ($nisn !== '' && Siswa::where('nisn', $nisn)->exists()) {
                $this->errors[] = "Baris {$line}: NISN {$nisn} sudah terdaftar — siswa dilewati.";
                continue;
            }

            // 3. Fallback: cek berdasarkan nama + tanggal lahir (untuk kasus NIPD/NISN kosong)
            $namaExact = $nama;
            $tglStr    = $tglLahir->toDateString();
            if (Siswa::where('nama_siswa', $namaExact)->where('tgl_lahir', $tglStr)->exists()) {
                $this->errors[] = "Baris {$line}: Siswa '{$nama}' (lahir {$tglStr}) sudah terdaftar — siswa dilewati.";
                continue;
            }
            // ──────────────────────────────────────────────────────────────

            $username = $this->generateUsername($nama);
            $password = $tglLahir->format('dmY');

            DB::transaction(function () use ($username, $password, $nama, $nipd, $nisn, $jk, $tempatLahir, $tglLahir, $rombel) {
                $user = User::create([
                    'username' => $username,
                    'email'    => null,
                    'password' => Hash::make($password),
                    'role'     => 'siswa',
                ]);

                $kelasId = null;
                if ($rombel !== '') {
                    $kelas = \App\Models\Kelas::firstOrCreate(
                        ['nama_kelas' => $rombel],
                        ['id_guru'    => null]
                    );
                    $kelasId = $kelas->id_kelas;
                }

                Siswa::create([
                    'id_user'       => $user->id_user,
                    'nipd'          => $nipd !== '' ? $nipd : null,
                    'nisn'          => $nisn !== '' ? $nisn : null,
                    'nama_siswa'    => $nama,
                    'jenis_kelamin' => $jk !== '' ? $jk : null,
                    'tempat_lahir'  => $tempatLahir !== '' ? $tempatLahir : null,
                    'tgl_lahir'     => $tglLahir->toDateString(),
                    'rombel_saat_ini' => $rombel !== '' ? $rombel : null,
                    'id_kelas'      => $kelasId,
                ]);
            });

            $this->created++;
        }
    }

    private function parseTanggalLahir(mixed $value): ?Carbon
    {
        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value);
        }

        if (is_numeric($value)) {
            return Carbon::instance(ExcelDate::excelToDateTimeObject((float) $value));
        }

        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        return Carbon::createFromFormat('d-m-Y', $value) ?: null;
    }

    private function generateUsername(string $nama): string
    {
        $first = trim(strtok($nama, ' '));
        $base = Str::slug($first, '_');
        if ($base === '') {
            $base = 'siswa';
        }
        $base = Str::limit($base, 20, '');
        $username = $base . '_cicadas';
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $counter++;
            $suffix = '_' . $counter;
            $username = Str::limit($base, 40 - strlen($suffix), '') . '_cicadas' . $suffix;
        }

        return $username;
    }

    private function normalizeId(mixed $value): string
    {
        if (is_numeric($value)) {
            return number_format((float) $value, 0, '', '');
        }

        return trim((string) $value);
    }
}
