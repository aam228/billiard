<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';
    // Add 'user_id' to the fillable array
    protected $fillable = ['user_id', 'meja_id', 'nama_pelanggan', 'durasi', 'total_harga', 'waktu_mulai', 'waktu_selesai'];

    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
    ];

    public function meja()
    {
        return $this->belongsTo(Meja::class);
    }

    public function pesananMakanan()
    {
        return $this->hasMany(PesananMakanan::class);
    }

    /**
     * Get the user that owns the transaction.
     */
    public function user() // <-- ENSURE THIS RELATIONSHIP IS PRESENT
    {
        return $this->belongsTo(User::class);
    }
}