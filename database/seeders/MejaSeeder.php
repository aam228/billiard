<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Meja;
use App\Models\User; // Pastikan User di-import jika digunakan
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB; // Untuk foreign key checks

class MejaSeeder extends Seeder
{
    public function run()
    {
        // Anda bisa langsung menetapkan user_id = 1, tapi sebaiknya tetap pastikan user dengan ID 1 ada
        // atau buat jika belum ada untuk menghindari error.
        // Opsi 1: Pastikan user_id 1 ada
        $userId = 1;
        $userExists = User::find($userId);

        // Matikan foreign key checks sementara
        DB::statement('SET FOREIGN_KEY_CHECKS=0;'); 
        Meja::truncate(); 
        // Hidupkan kembali foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Daftar tarif per jam yang bulat dan umum
        $tarifBulat = [40000, 50000, 60000, 75000]; 

        foreach (range(1, 15) as $i) { 
            Meja::create([
                'user_id' => $userId, // Menggunakan user_id = 1
                'nama_meja' => 'Meja ' . $i, 
                'status' => 'tersedia',
                'tarif_per_jam' => $tarifBulat[array_rand($tarifBulat)],
            ]);
        }
    }
}