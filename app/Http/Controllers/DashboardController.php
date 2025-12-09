<?php

namespace App\Http\Controllers;

use App\Models\Meja;
use Illuminate\Support\Facades\Auth; 

class DashboardController extends Controller
{
    public function index()
    {
        $mejas = Auth::user()->mejas()->with('transaksis')->get();

        foreach ($mejas as $meja) {
            $transaksiAktif = $meja->transaksiAktif; 

            if (!$transaksiAktif && $meja->status == 'digunakan') {
                $meja->update(['status' => 'tersedia']);
            }
        }

        return view('dashboard', compact('mejas'));
    }
}