<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Transaksi; // <-- Pastikan ini diimpor!

class Meja extends Model
{
    use HasFactory;

    protected $table = 'meja'; // Pastikan ini jika nama tabelnya singular 'meja'

    protected $fillable = [
        'user_id',
        'nama_meja',
        'status',
        'tarif_per_jam'
    ];

    // Relasi ke User (pemilik meja)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // --- TAMBAHKAN RELASI BERIKUT INI ---
    /**
     * Dapatkan semua transaksi yang terkait dengan meja ini.
     */
    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'meja_id');
    }

    // Accessor untuk transaksi yang sedang aktif (opsional, tapi Anda menggunakannya)
    public function getTransaksiAktifAttribute()
    {
        return $this->transaksis()
            ->where(function ($q) {
                $q->whereNull('waktu_selesai')
                  ->orWhere('waktu_selesai', '>', now());
            })
            ->orderBy('waktu_mulai', 'desc')
            ->first();
    }
}