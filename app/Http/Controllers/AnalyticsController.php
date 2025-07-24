<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth; // <-- TAMBAHKAN INI

class AnalyticsController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $today = $now->toDateString(); // tanggal hari ini
        $month = $now->format('m'); // bulan sekarang (01-12)
        $year = $now->year; // tahun sekarang

        $loggedInUserId = Auth::id(); // Ambil ID pengguna yang sedang login

        // === RINGKASAN CEPAT ===
        $pendapatanHariIni = Transaksi::where('user_id', $loggedInUserId) // <-- FILTER BY USER_ID
                                ->whereDate('waktu_mulai', $today)
                                ->sum('total_harga');

        $pendapatanBulanIni = Transaksi::where('user_id', $loggedInUserId) // <-- FILTER BY USER_ID
                                ->whereMonth('waktu_mulai', $month)
                                ->whereYear('waktu_mulai', $year)
                                ->sum('total_harga');

        $jumlahTransaksi = Transaksi::where('user_id', $loggedInUserId) // <-- FILTER BY USER_ID
                                ->whereMonth('waktu_mulai', $month)
                                ->whereYear('waktu_mulai', $year)
                                ->count();

        $rataRataDurasi = Transaksi::where('user_id', $loggedInUserId) // <-- FILTER BY USER_ID
                                ->avg('durasi');

        // === GRAFIK: PENDAPATAN PER HARI ===
        $pendapatanPerHari = Transaksi::where('user_id', $loggedInUserId) // <-- FILTER BY USER_ID
                                ->select(
                                    DB::raw('DATE(waktu_mulai) as tanggal'),
                                    DB::raw('SUM(total_harga) as total')
                                )
                                ->groupBy('tanggal')
                                ->orderBy('tanggal', 'ASC')
                                ->get();

        // === GRAFIK: PENDAPATAN PER MEJA ===
        $pendapatanPerMeja = DB::table('transaksi')
                                ->join('meja', 'transaksi.meja_id', '=', 'meja.id')
                                ->where('transaksi.user_id', $loggedInUserId) // <-- FILTER BY USER_ID
                                ->select('meja.nama_meja', DB::raw('SUM(transaksi.total_harga) as total'))
                                ->groupBy('meja.nama_meja')
                                ->get();

        // === GRAFIK: JAM SIBUK (jumlah transaksi per jam) ===
        $jamSibuk = Transaksi::where('user_id', $loggedInUserId) // <-- FILTER BY USER_ID
                            ->select(
                                DB::raw('HOUR(waktu_mulai) as jam'),
                                DB::raw('COUNT(*) as jumlah')
                            )
                            ->groupBy('jam')
                            ->orderBy('jam', 'ASC')
                            ->get();

        return view('analytics.index', compact(
            'pendapatanHariIni',
            'pendapatanBulanIni',
            'jumlahTransaksi',
            'rataRataDurasi',
            'pendapatanPerHari',
            'pendapatanPerMeja',
            'jamSibuk'
        ));
    }
}