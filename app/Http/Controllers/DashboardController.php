<?php

namespace App\Http\Controllers;

use App\Models\Meja;
use Illuminate\Support\Facades\Auth; // Tambahkan ini

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Ambil HANYA meja yang dimiliki oleh pengguna yang sedang login.
        //    Eager load relasi 'transaksi' karena Anda menggunakannya di loop dan accessor.
        $mejas = Auth::user()->mejas()->with('transaksis')->get(); // Menggunakan 'transaksis' untuk koleksi semua transaksi meja

        foreach ($mejas as $meja) {
            // Mengakses accessor getTransaksiAktifAttribute() di model Meja.
            // Pastikan Anda sudah mendefinisikan accessor ini di model Meja.
            $transaksiAktif = $meja->transaksiAktif; 

            // Logika untuk mengupdate status meja jika transaksi aktif sudah berakhir
            if (!$transaksiAktif && $meja->status == 'digunakan') {
                // Pastikan otorisasi ada di metode update MejaController jika ini dipanggil
                // atau pastikan ini adalah proses otomatis yang hanya admin (pemilik meja) yang bisa lakukan.
                // Karena ini di dashboard, dan meja milik user yang login, ini aman.
                $meja->update(['status' => 'tersedia']);
            }
        }

        return view('dashboard', compact('mejas'));
    }
}