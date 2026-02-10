<?php

namespace App\Imports;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GuruImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    public int $created = 0;
    public array $errors = [];

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $line = $index + 2;
            $nama = trim((string) ($row['nama'] ?? ''));
            $jk = strtoupper(trim((string) ($row['jk'] ?? '')));
            $nip = $this->normalizeId($row['nip'] ?? '');

            if ($nama === '') {
                $this->errors[] = "Baris {$line}: nama kosong.";
                continue;
            }

            if ($nip !== '' && Guru::where('nip', $nip)->exists()) {
                $this->errors[] = "Baris {$line}: NIP sudah terdaftar ({$nip}).";
                continue;
            }

            $username = $this->generateUsername($nama);

            DB::transaction(function () use ($username, $nama, $jk, $nip) {
                $user = User::create([
                    'username' => $username,
                    'email' => null,
                    'password' => Hash::make('guru12345'),
                    'role' => 'guru',
                ]);

                Guru::create([
                    'id_user' => $user->id_user,
                    'nip' => $nip !== '' ? $nip : null,
                    'nama_guru' => $nama,
                    'jenis_kelamin' => $jk !== '' ? $jk : null,
                    'mapel_utama' => null,
                ]);
            });

            $this->created++;
        }
    }

    private function generateUsername(string $nama): string
    {
        $first = trim(strtok($nama, ' '));
        $base = Str::slug($first, '_');
        if ($base === '') {
            $base = 'guru';
        }
        $base = Str::limit($base, 20, '');
        $username = $base . '_cicadas';
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $counter++;
            $suffix = '_' . $counter;
            $username = Str::limit($base, 20 - strlen($suffix), '') . '_cicadas' . $suffix;
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
