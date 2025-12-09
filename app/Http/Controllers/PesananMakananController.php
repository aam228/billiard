<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Produk;
use App\Models\PesananMakanan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PesananMakananController extends Controller
{
    public function create($transaksi_id)
    {
        // Ambil transaksi + relasi
        $transaksi = Transaksi::with(['pesananMakanan.produk', 'meja'])
            ->findOrFail($transaksi_id);

        // Cek kepemilikan transaksi
        if ($transaksi->user_id !== Auth::id()) {
            abort(403, 'Tidak ada akses.');
        }

        // Produk milik user
        $produk = Auth::user()->produks()->get();
        
        // Pesanan terkait transaksi
        $pesananMakanan = $transaksi->pesananMakanan;

        return view('pesanan.create', compact('transaksi', 'produk', 'pesananMakanan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'transaksi_id' => 'required|exists:transaksi,id',
            'produk' => 'required|array',
            'produk.*' => 'integer|min:0',
        ]);

        try {
            $transaksi = Transaksi::findOrFail($request->transaksi_id);
            
            // Cek kepemilikan transaksi
            if ($transaksi->user_id !== Auth::id()) {
                abort(403, 'Tidak ada akses.');
            }

            // Pastikan ada pesanan
            $hasOrder = false;
            foreach ($request->produk as $jumlah) {
                if ($jumlah > 0) {
                    $hasOrder = true;
                    break;
                }
            }
            if (!$hasOrder) {
                return back()->with('error', 'Minimal pilih 1 produk.');
            }

            foreach ($request->produk as $produk_id => $jumlah) {
                if ($jumlah > 0) {
                    $produk = Produk::findOrFail($produk_id);
                    
                    // Cek kepemilikan produk
                    if ($produk->user_id !== Auth::id()) {
                        throw new \Exception('Produk tidak valid.');
                    }

                    $subtotal = (float) $jumlah * (float) $produk->harga;

                    // Simpan pesanan
                    Auth::user()->pesananMakanan()->create([
                        'transaksi_id' => $transaksi->id,
                        'meja_id' => $transaksi->meja_id,
                        'produk_id' => $produk->id,
                        'jumlah' => $jumlah,
                        'subtotal' => $subtotal,
                    ]);
                }
            }

            return redirect()->route('pesanan.create', ['transaksi_id' => $transaksi->id])
                ->with('success', 'Pesanan berhasil ditambahkan!');
                
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function redirectFromTransaksi($transaksi_id)
    {
        $transaksi = Transaksi::findOrFail($transaksi_id);
        // Cek kepemilikan transaksi
        if ($transaksi->user_id !== Auth::id()) {
            abort(403, 'Tidak ada akses.');
        }
        return redirect()->route('pesanan.create', ['transaksi_id' => $transaksi->id]);
    }

    public function getPesananByTransaksi($transaksi_id)
    {
        $transaksi = Transaksi::findOrFail($transaksi_id);
        // Cek kepemilikan transaksi
        if ($transaksi->user_id !== Auth::id()) {
            return response()->json(['error' => 'Tidak ada akses.'], 403);
        }
        
        $pesananMakanan = $transaksi->pesananMakanan()->with('produk')->get();
        return response()->json($pesananMakanan);
    }
}