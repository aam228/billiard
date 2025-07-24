<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaksi;
use App\Models\Meja;
use App\Models\User; // Pastikan User di-import
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; // Untuk foreign key checks

class TransaksiSeeder extends Seeder
{
    public function run()
    {
        $mejaList = Meja::all();
        // User yang akan digunakan adalah user dengan ID 1
        $user = User::find(1); 

        if ($mejaList->isEmpty() || !$user) { // Pastikan user 1 juga ada
            $this->command->warn('Seeder dibatalkan: Tidak ada data meja atau user dengan ID 1 tidak ditemukan.');
            return;
        }

        // Matikan foreign key checks sementara
        DB::statement('SET FOREIGN_KEY_CHECKS=0;'); 
        Transaksi::truncate();
        // Hidupkan kembali foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Bagian untuk simulasi pelanggan berulang (tetap sama)
        $jumlahPelangganLoyal = 30; 
        $namaPelangganLoyal = [];
        for ($i = 0; $i < $jumlahPelangganLoyal; $i++) {
            $namaPelangganLoyal[] = $faker = \Faker\Factory::create('id_ID')->name; // Panggil faker di dalam loop jika perlu
        }

        $jumlahHariRentang = 180; 
        $jumlahTransaksi = 1000; 

        for ($i = 0; $i < $jumlahTransaksi; $i++) {
            $meja = $mejaList->random();
            
            $durasiPilihan = [1, 1.5, 2, 2.5, 3, 3.5, 4];
            $durasi = $durasiPilihan[array_rand($durasiPilihan)];

            $waktuMulai = Carbon::now()
                                ->subDays(rand(0, $jumlahHariRentang))
                                ->subHours(rand(8, 23))
                                ->subMinutes(rand(0, 59));

            $waktuSelesai = (clone $waktuMulai)->addMinutes($durasi * 60);

            if ($waktuSelesai->isFuture()) {
                $waktuSelesai = Carbon::now();
                $durasi = round($waktuSelesai->diffInMinutes($waktuMulai) / 60, 1); // Round durasi
                if ($durasi <= 0) {
                    continue; 
                }
            }
            
            $namaPelanggan = $namaPelangganLoyal[array_rand($namaPelangganLoyal)];
            $totalHarga = $durasi * $meja->tarif_per_jam;

            Transaksi::create([
                'user_id' => $user->id, // Menggunakan user_id = 1
                'meja_id' => $meja->id,
                'nama_pelanggan' => $namaPelanggan,
                'durasi' => $durasi,
                'total_harga' => $totalHarga,
                'waktu_mulai' => $waktuMulai,
                'waktu_selesai' => $waktuSelesai,
            ]);
        }
    }
}