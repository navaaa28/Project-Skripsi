<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Guru;
use App\Models\Mapel;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'username' => 'admin',
                'password' => Hash::make('admin12345'),
                'role' => 'admin',
            ]
        );

        Admin::firstOrCreate(
            ['id_user' => $adminUser->id_user],
            ['nama_admin' => 'Administrator']
        );

        $guruUser = User::firstOrCreate(
            ['email' => 'guru@example.com'],
            [
                'username' => 'guru1',
                'password' => Hash::make('guru12345'),
                'role' => 'guru',
            ]
        );

        Guru::firstOrCreate(
            ['id_user' => $guruUser->id_user],
            [
                'nip' => '1987654321',
                'nama_guru' => 'Guru Utama',
                'mapel_utama' => 'Matematika',
            ]
        );

        $mapelList = [
            'Pendidikan Agama dan Budi Pekerti',
            'Pendidikan Pancasila',
            'Bahasa Indonesia',
            'Matematika',
            'Ilmu Pengetahuan Alam dan Sosial',
            'Seni Rupa',
            'Pendidikan Jasmani Olahraga dan Kesehatan',
            'Bahasa dan Sastra Sunda',
            'Bahasa Inggris',
        ];

        foreach ($mapelList as $nama) {
            Mapel::firstOrCreate(
                ['nama_mapel' => $nama],
                ['kkm' => 75]
            );
        }
    }
}
