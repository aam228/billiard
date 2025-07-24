<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth; // <-- TAMBAHKAN INI

class ProdukController extends Controller
{
    /**
     * Menampilkan daftar semua produk yang dimiliki oleh pengguna yang sedang login.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Filter produk berdasarkan user_id pengguna yang sedang login
        $produks = Auth::user()->produks()->get(); // <-- FILTER PRODUK DENGAN AUTH::USER()
        return view('produk.index', compact('produks'));
    }

    /**
     * Menyimpan produk baru ke database, mengaitkannya dengan pengguna yang sedang login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Validasi unique per user untuk nama_produk
            'nama_produk' => 'required|string|max:255|unique:produk,nama_produk,NULL,id,user_id,'.Auth::id(),
            'harga' => 'required|numeric',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('produk-images', 'public');
        }

        // Buat produk baru dan secara otomatis kaitkan dengan user yang sedang login
        // 'user_id' akan otomatis terisi oleh relasi
        Auth::user()->produks()->create($validated); // <-- KUNCI PERUBAHAN DI SINI!

        return redirect()->route('produk.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail produk.
     *
     * @param  \App\Models\Produk  $produk
     * @return \Illuminate\View\View
     */
    public function show(Produk $produk)
    {
        // Otorisasi: Pastikan produk ini milik user yang sedang login
        if ($produk->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk melihat produk ini.');
        }
        return view('produk.show', compact('produk'));
    }

    /**
     * Menampilkan form untuk mengedit produk tertentu.
     *
     * @param  \App\Models\Produk  $produk
     * @return \Illuminate\View\View
     */
    public function edit(Produk $produk)
    {
        // Otorisasi: Pastikan produk ini milik user yang sedang login
        if ($produk->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit produk ini.');
        }
        return view('produk.edit', compact('produk'));
    }

    /**
     * Memperbarui produk yang sudah ada di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id (gunakan Produk $produk untuk Route Model Binding)
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Produk $produk) // Gunakan Route Model Binding
    {
        // Otorisasi: Pastikan produk ini milik user yang sedang login
        if ($produk->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk memperbarui produk ini.');
        }

        $validated = $request->validate([
            // Validasi unique per user untuk nama_produk (mengabaikan ID produk saat ini)
            'nama_produk' => 'required|string|max:255|unique:produk,nama_produk,' . $produk->id . ',id,user_id,'.Auth::id(),
            'harga' => 'required|numeric',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($produk->gambar) {
                Storage::disk('public')->delete($produk->gambar);
            }
            $validated['gambar'] = $request->file('gambar')->store('produk-images', 'public'); // Simpan ke produk-images
        }

        $produk->update($validated); // Update menggunakan validated data

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'produk' => $produk
            ]);
        }

        return redirect()->back()->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Menghapus produk dari database.
     *
     * @param  \App\Models\Produk  $produk
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Produk $produk)
    {
        // Otorisasi: Pastikan produk ini milik user yang sedang login
        if ($produk->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus produk ini.');
        }

        if ($produk->gambar) {
            Storage::disk('public')->delete($produk->gambar);
        }

        $produk->delete();

        return redirect()->route('produk.index')->with('success', 'Produk berhasil dihapus.');
    }

    // Optional: untuk keperluan AJAX (kalau memang dibutuhkan)
    public function editJson(Produk $produk)
    {
        // Otorisasi: Pastikan produk ini milik user yang sedang login
        if ($produk->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk melihat data produk ini.');
        }
        return response()->json($produk);
    }
}