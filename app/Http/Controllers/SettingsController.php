<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    /**
     * Menampilkan halaman pengaturan profil dan umum.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user(); // Dapatkan pengguna yang sedang login
        return view('settings.index', compact('user'));
    }

    /**
     * Memperbarui informasi profil pengguna (Nama & Email).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id), // Email harus unik kecuali untuk user itu sendiri
            ],
        ]);

        $user->fill($request->only('name', 'email'));
        // Tidak perlu reset email_verified_at karena Anda tidak ingin verifikasi email ulang
        $user->save();

        return redirect()->route('settings.index')->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Memperbarui kata sandi pengguna.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', 'current_password'], // Memverifikasi password lama
            'password' => ['required', 'confirmed', 'min:8', 'different:current_password'], // Password baru harus berbeda
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('settings.index')->with('success', 'Kata sandi berhasil diperbarui.');
    }

    /**
     * Memperbarui preferensi tema pengguna.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateTheme(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'theme' => ['required', 'string', Rule::in(['light', 'dark', 'system'])],
        ]);

        $user->update([
            'theme' => $request->theme,
        ]);

        return redirect()->route('settings.index')->with('success', 'Preferensi tema berhasil diperbarui.');
    }
}