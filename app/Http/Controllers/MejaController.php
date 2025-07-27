<?php

namespace App\Http\Controllers;

use App\Models\Meja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MejaController extends Controller
{
    public function index()
    {
        $mejas = Auth::user()->mejas()->get();
        return view('meja.index', compact('mejas'));
    }

    public function create()
    {
        return view('meja.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_meja' => 'required|string|max:255|unique:meja,nama_meja,NULL,id,user_id,' . Auth::id(),
            'tarif_per_jam' => 'required|numeric|min:0',
        ]);

        Auth::user()->mejas()->create([
            'nama_meja' => $request->nama_meja,
            'status' => 'tersedia',
            'tarif_per_jam' => $request->tarif_per_jam,
        ]);

        return redirect()->route('meja.index')->with('success', 'Meja berhasil ditambahkan.');
    }

    public function edit(Meja $meja)
    {
        $this->authorizeMeja($meja);
        return view('meja.edit', compact('meja'));
    }

    public function update(Request $request, Meja $meja)
    {
        $this->authorizeMeja($meja);

        $request->validate([
            'nama_meja' => 'required|string|max:255|unique:meja,nama_meja,' . $meja->id . ',id,user_id,' . Auth::id(),
            'tarif_per_jam' => 'required|numeric|min:0',
        ]);

        $meja->update([
            'nama_meja' => $request->nama_meja,
            'tarif_per_jam' => $request->tarif_per_jam,
        ]);

        return redirect()->route('meja.index')->with('success', 'Meja berhasil diperbarui.');
    }

    public function destroy(Meja $meja)
    {
        $this->authorizeMeja($meja);
        $meja->delete();
        return redirect()->route('meja.index')->with('success', 'Meja berhasil dihapus.');
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'meja_id' => 'required|exists:meja,id',
            'status' => 'required|in:tersedia,digunakan,perawatan',
        ]);

        $meja = Meja::findOrFail($request->meja_id);
        $this->authorizeMeja($meja);

        $meja->update(['status' => $request->status]);

        return response()->json(['success' => true, 'message' => 'Status meja berhasil diperbarui.']);
    }

    public function resetStatus(Meja $meja)
    {
        $this->authorizeMeja($meja);
        $meja->update(['status' => 'tersedia']);

        return redirect()->back()->with('success', 'Status meja berhasil direset.');
    }

    private function authorizeMeja(Meja $meja)
    {
        if ($meja->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
