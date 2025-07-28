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
        // Filter transactions by the logged-in user's ID for security and data isolation
        $transaksis = Auth::user()->transaksis()
                            ->with('meja')
                            ->orderBy('created_at', 'desc')
                            ->paginate(10);
        return view('transaksi.histori', compact('transaksis'));
    }

    public function create($meja_id)
    {
        $meja = Meja::findOrFail($meja_id);

        // Authorization: Ensure the table belongs to the logged-in user before showing the form
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
                    // tambahan pencegah double insert
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
    
        // Create the transaction and automatically assign the user_id via the relationship
        $transaksi = Auth::user()->transaksis()->create([
            'meja_id' => $meja->id,
            'nama_pelanggan' => $request->nama_pelanggan,
            'durasi' => $durasi,
            'total_harga' => $total_harga,
            'waktu_mulai' => $waktu_mulai,
            'waktu_selesai' => $waktu_selesai,
        ]);
    
        // Update the table status. Authorization was already confirmed when validating $meja_id.
        $meja->update([
            'status' => 'digunakan',
        ]);
    
        return redirect()->route('pesanan.create', ['transaksi_id' => $transaksi->id])
            ->with('success', 'Transaksi berhasil dimulai, silakan tambah pesanan.');
    }    

    public function selesai($transaksi_id)
    {
        $transaksi = Transaksi::findOrFail($transaksi_id);

        // Authorization: Ensure this transaction belongs to the logged-in user
        if ($transaksi->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk menyelesaikan transaksi ini.');
        }

        $transaksi->update([
            'waktu_selesai' => now(),
            // Add 'status' => 'selesai' if you have a status column in your transaksi table
        ]);

        // Ensure the associated table also belongs to the logged-in user before updating its status.
        // This check is redundant if the transaction already belongs to the user and the meja relationship is correctly set up,
        // but it adds an extra layer of safety.
        if ($transaksi->meja->user_id !== Auth::id()) {
             abort(403, 'Anda tidak memiliki akses untuk mengubah status meja terkait.');
        }

        $transaksi->meja->update([
            'status' => 'tersedia',
            'waktu_mulai' => null, // Reset these fields on the table if they exist there
            'waktu_selesai' => null,
        ]);

        return redirect()->route('dashboard')->with('success', 'Transaksi berhasil diselesaikan.');
    }

    public function hapus(Transaksi $transaksi)
    {
        // Authorization: Ensure this transaction belongs to the logged-in user
        if ($transaksi->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus transaksi ini.');
        }

        $transaksi->delete();
        return redirect()->route('transaksi.histori')->with('success', 'Transaksi berhasil dihapus.');
    }

    public function laporan(Request $request)
    {
        // Filter report data by the logged-in user's ID for security and data isolation
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
            // Validation: Ensure the selected meja for filtering belongs to the current user
            $mejaIdsOwnedByUser = Auth::user()->mejas()->pluck('id');
            if (!$mejaIdsOwnedByUser->contains($request->meja_id)) {
                 abort(403, 'Meja yang dipilih tidak valid atau bukan milik Anda.');
            }
            $query->where('meja_id', $request->meja_id);
        }

        $transaksis = $query->orderBy('created_at', 'desc')->get();
        $totalPendapatan = $transaksis->sum('total_harga');
        
        // Tables displayed in the report also need to be filtered per user
        $mejas = Auth::user()->mejas()->get();

        return view('transaksi.laporan', compact('transaksis', 'totalPendapatan', 'mejas'));
    }

    public function cetakLaporan(Request $request)
    {
        // Filter report data by the logged-in user's ID for security and data isolation
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
            // Validation: Ensure the selected meja for filtering belongs to the current user
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
        
        // Authorization: Ensure this transaction belongs to the logged-in user
        if ($transaksi->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk menyelesaikan transaksi ini.');
        }

        // Check if the transaction is actually due to finish
        if (now()->greaterThanOrEqualTo($transaksi->waktu_selesai)) {
            $transaksi->update([
                // Add 'status' => 'selesai' if you have a status column in your transaksi table
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