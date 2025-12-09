<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Meja;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth; 

class TransaksiController extends Controller
{
    public function histori()
    {
        $transaksis = Auth::user()->transaksis()
                            ->with('meja')
                            ->orderBy('created_at', 'desc')
                            ->paginate(10);
        return view('transaksi.histori', compact('transaksis'));
    }

    public function create($meja_id)
    {
        $meja = Meja::findOrFail($meja_id);

        if ($meja->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk membuat transaksi di meja ini.');
        }

        return view('transaksi.create', compact('meja'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'meja_id' => [
                'required','exists:meja,id',
                function ($attribute, $value, $fail) {
                    $meja = Meja::find($value);
                    if ($meja->user_id !== Auth::id()) {
                        $fail('Meja tidak valid.');
                    }
                    if ($meja->status === 'digunakan') {
                        $fail('Meja sudah digunakan. Silakan refresh halaman.');
                    }
                },
            ],
            'nama_pelanggan' => 'required|string|max:255',
            'durasi' => 'required|integer|min:1',
        ]);
    
        $meja = Meja::findOrFail($request->meja_id);
        $waktu_mulai = now();
        $durasi = (int) $request->durasi;
        $waktu_selesai = $waktu_mulai->copy()->addHours($durasi);
        $total_harga = $meja->tarif_per_jam * $durasi;
    
        $transaksi = Auth::user()->transaksis()->create([
            'meja_id' => $meja->id,
            'nama_pelanggan' => $request->nama_pelanggan,
            'durasi' => $durasi,
            'total_harga' => $total_harga,
            'waktu_mulai' => $waktu_mulai,
            'waktu_selesai' => $waktu_selesai,
        ]);
    
        $meja->update([
            'status' => 'digunakan',
        ]);
    
        return redirect()->route('pesanan.create', ['transaksi_id' => $transaksi->id])
            ->with('success', 'Transaksi berhasil dimulai, silakan tambah pesanan.');
    }    

    public function selesai($transaksi_id)
    {
        $transaksi = Transaksi::findOrFail($transaksi_id);

        if ($transaksi->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk menyelesaikan transaksi ini.');
        }

        $transaksi->update([
            'waktu_selesai' => now(),
        ]);

        if ($transaksi->meja->user_id !== Auth::id()) {
             abort(403, 'Anda tidak memiliki akses untuk mengubah status meja terkait.');
        }

        $transaksi->meja->update([
            'status' => 'tersedia',
            'waktu_mulai' => null,
            'waktu_selesai' => null,
        ]);

        return redirect()->route('dashboard')->with('success', 'Transaksi berhasil diselesaikan.');
    }

    public function destroy(Transaksi $transaksi)
    {
        if ($transaksi->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk menghapus transaksi ini.'
            ], 403);
        }

        try {
            $transaksi->delete();

            return response()->json([
                'message' => 'Transaksi berhasil dihapus.'
            ], 200);

        } catch (\Exception $e) {
            \Log::error("Gagal menghapus Transaksi ID {$transaksi->id}: " . $e->getMessage()); 
            return response()->json([
                'message' => 'Gagal menghapus transaksi. Terjadi kesalahan server.'
            ], 500); 
        }
    }

    public function laporan(Request $request)
    {
        $query = Auth::user()->transaksis()->with('meja');

        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->tanggal_mulai)->startOfDay(),
                Carbon::parse($request->tanggal_selesai)->endOfDay()
            ]);
        }

        if ($request->filled('nama_pelanggan')) {
            $query->where('nama_pelanggan', 'LIKE', '%' . $request->nama_pelanggan . '%');
        }

        if ($request->filled('meja_id')) {
            $mejaIdsOwnedByUser = Auth::user()->mejas()->pluck('id');
            if (!$mejaIdsOwnedByUser->contains($request->meja_id)) {
                 abort(403, 'Meja yang dipilih tidak valid atau bukan milik Anda.');
            }
            $query->where('meja_id', $request->meja_id);
        }

        $transaksis = $query->orderBy('created_at', 'desc')->get();
        $totalPendapatan = $transaksis->sum('total_harga');

        $mejas = Auth::user()->mejas()->get();

        return view('transaksi.laporan', compact('transaksis', 'totalPendapatan', 'mejas'));
    }

    public function cetakLaporan(Request $request)
    {
        $query = Auth::user()->transaksis()->with('meja');
    
        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->tanggal_mulai)->startOfDay(),
                Carbon::parse($request->tanggal_selesai)->endOfDay()
            ]);
        }
    
        if ($request->filled('nama_pelanggan')) {
            $query->where('nama_pelanggan', 'LIKE', '%' . $request->nama_pelanggan . '%');
        }
    
        if ($request->filled('meja_id')) {
            $mejaIdsOwnedByUser = Auth::user()->mejas()->pluck('id');
            if (!$mejaIdsOwnedByUser->contains($request->meja_id)) {
                 abort(403, 'Meja yang dipilih tidak valid atau bukan milik Anda.');
            }
            $query->where('meja_id', $request->meja_id);
        }
    
        $transaksis = $query->orderBy('created_at', 'desc')->get();
        $totalPendapatan = $transaksis->sum('total_harga');
    
        $groupByMeja = $transaksis->groupBy('meja_id');
    
        $dataMeja = [];
        foreach ($groupByMeja as $mejaId => $group) {
            $namaMeja = $group->first()->meja->nama_meja ?? 'Meja #' . $mejaId;
            $totalPendapatanMeja = $group->sum('total_harga');
            $totalDurasiMeja = $group->sum('durasi');
            $persentase = $totalPendapatan > 0 ? ($totalPendapatanMeja / $totalPendapatan) * 100 : 0;
    
            $dataMeja[] = [
                'meja_id' => $mejaId,
                'nama_meja' => $namaMeja,
                'total_durasi' => $totalDurasiMeja,
                'total_pendapatan' => $totalPendapatanMeja,
                'persentase' => $persentase,
            ];
        }
    
        usort($dataMeja, fn($a, $b) => $b['total_pendapatan'] <=> $a['total_pendapatan']);
    
        $pdf = PDF::loadView('transaksi.laporan-pdf', compact(
            'transaksis',
            'totalPendapatan',
            'dataMeja'
        ))->setPaper('A4', 'landscape');
    
        return $pdf->stream('laporan-keuangan.pdf');
    }

    public function autoSelesai($id)
    {
        $transaksi = Transaksi::findOrFail($id);

        if ($transaksi->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk menyelesaikan transaksi ini.');
        }

        if (now()->greaterThanOrEqualTo($transaksi->waktu_selesai)) {
            $transaksi->update([
            ]);
            if ($transaksi->meja->user_id !== Auth::id()) {
                 abort(403, 'Anda tidak memiliki akses untuk mengubah status meja terkait.');
            }
            $transaksi->meja->update([
                'status' => 'tersedia'
            ]);
        
            return response()->json(['success' => true, 'message' => 'Transaksi otomatis diselesaikan.']);
        }
    
        return response()->json(['success' => false, 'message' => 'Belum waktunya selesai.']);
    }
}