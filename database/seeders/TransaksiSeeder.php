<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaksi;
use App\Models\Meja;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TransaksiSeeder extends Seeder
{
    public function run()
    {
        $userId = 2;
        $user = User::find($userId);

        // Ambil hanya meja milik user ID 2
        $mejaList = Meja::where('user_id', $userId)->get();

        if ($mejaList->isEmpty() || !$user) {
            $this->command->warn("Seeder dibatalkan: Tidak ada data meja atau user dengan ID {$userId} tidak ditemukan.");
            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Transaksi::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $faker = \Faker\Factory::create('id_ID');
        $namaPelangganLoyal = collect(range(1, 30))->map(fn () => $faker->name)->toArray();

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
                $durasi = round($waktuSelesai->diffInMinutes($waktuMulai) / 60, 1);
                if ($durasi <= 0) {
                    continue;
                }
            }

            $namaPelanggan = $namaPelangganLoyal[array_rand($namaPelangganLoyal)];
            $totalHarga = $durasi * $meja->tarif_per_jam;

            Transaksi::create([
                'user_id' => $user->id,
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
