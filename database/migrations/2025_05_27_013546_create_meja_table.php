<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('meja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // BARIS BARU: Menambahkan user_id
            $table->string('nama_meja')->unique(); 
            $table->enum('status', ['tersedia', 'digunakan', 'perawatan'])->default('tersedia'); 
            $table->decimal('tarif_per_jam', 8, 2)->default(50000.00); 
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('meja');
    }
};