<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // BARIS BARU: Menambahkan user_id
            $table->unsignedBigInteger('meja_id');
            $table->string('nama_pelanggan');
            $table->integer('durasi');
            $table->decimal('total_harga', 8, 2);
            $table->timestamp('waktu_mulai');
            $table->timestamp('waktu_selesai')->nullable();
            $table->timestamps();
            $table->foreign('meja_id')->references('id')->on('meja')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('transaksi');
    }
};