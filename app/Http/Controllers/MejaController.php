<?php

namespace App\Http\Controllers;

use App\Models\Meja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import Auth Facade

class MejaController extends Controller
{
    /**
     * Menampilkan daftar semua meja yang dimiliki oleh pengguna yang sedang login.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Mengambil hanya meja yang dimiliki oleh user yang sedang login
        // Pastikan model User Anda memiliki relasi 'mejas()' (hasMany)
        $mejas = Auth::user()->mejas()->get(); 
        
        // Atau, jika Anda tidak ingin menggunakan relasi langsung dari user model:
        // $mejas = Meja::where('user_id', Auth::id())->get();

        return view('meja.index', compact('mejas'));
    }

    /**
     * Menampilkan form untuk membuat meja baru.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Biasanya tidak perlu logika spesifik user di sini,
        // karena form hanya akan mengumpulkan input yang akan dikaitkan nanti.
        return view('meja.create');
    }

    /**
     * Menyimpan meja baru ke database, mengaitkannya dengan pengguna yang sedang login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_meja' => 'required|string|max:255|unique:meja,nama_meja,NULL,id,user_id,'.Auth::id(), // Validasi unique per user
            'tarif_per_jam' => 'required|numeric|min:0',
        ]);

        // Membuat meja baru dan secara otomatis mengisi user_id
        // dengan ID pengguna yang sedang login melalui relasi hasMany.
        Auth::user()->mejas()->create([
            'nama_meja' => $request->nama_meja,
            'status' => 'tersedia', // Default status saat membuat meja baru
            'tarif_per_jam' => $request->tarif_per_jam,
        ]);

        return redirect()->route('meja.index')->with('success', 'Meja berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit meja tertentu.
     *
     * @param  \App\Models\Meja  $meja
     * @return \Illuminate\View\View
     */
    public function edit(Meja $meja)
    {
        // Otorisasi: Memastikan pengguna yang login adalah pemilik meja ini.
        if ($meja->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit meja ini.');
        }

        return view('meja.edit', compact('meja'));
    }

    /**
     * Memperbarui meja yang sudah ada di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Meja  $meja
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Meja $meja)
    {
        // Otorisasi: Memastikan pengguna yang login adalah pemilik meja ini.
        if ($meja->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk memperbarui meja ini.');
        }

        $request->validate([
            // Validasi unique per user untuk nama_meja (mengabaikan ID meja saat ini)
            'nama_meja' => 'required|string|max:255|unique:meja,nama_meja,' . $meja->id . ',id,user_id,'.Auth::id(),
            'tarif_per_jam' => 'required|numeric|min:0',
        ]);

        $meja->update([
            'nama_meja' => $request->nama_meja,
            'tarif_per_jam' => $request->tarif_per_jam,
        ]);

        return redirect()->route('meja.index')->with('success', 'Meja berhasil diperbarui.');
    }

    /**
     * Menghapus meja dari database.
     *
     * @param  \App\Models\Meja  $meja
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Meja $meja)
    {
        // Otorisasi: Memastikan pengguna yang login adalah pemilik meja ini.
        if ($meja->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus meja ini.');
        }

        $meja->delete();
        return redirect()->route('meja.index')->with('success', 'Meja berhasil dihapus.');
    }

    // --- Metode Tambahan (Opsional, dari pertanyaan sebelumnya) ---

    // Contoh metode updateStatus, Anda juga perlu menambahkan otorisasi di sini
    public function updateStatus(Request $request)
    {
        $request->validate([
            'meja_id' => 'required|exists:meja,id',
            'status' => 'required|in:tersedia,digunakan,perawatan',
        ]);

        $meja = Meja::findOrFail($request->meja_id);

        // Otorisasi: Memastikan pengguna yang login adalah pemilik meja ini.
        if ($meja->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk memperbarui status meja ini.');
        }

        $meja->status = $request->status;
        $meja->save();

        return response()->json(['success' => true, 'message' => 'Status meja berhasil diperbarui.']);
    }

    public function resetStatus(Meja $meja)
    {
        if ($meja->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk mereset status meja ini.');
        }

        $meja->status = 'tersedia';
        $meja->save();

        return redirect()->back()->with('success', 'Status meja berhasil direset menjadi tersedia.');
    }
}