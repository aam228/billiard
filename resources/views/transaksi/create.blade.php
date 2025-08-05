@extends('layouts.app')

@section('content')
<div class="min-h-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="bg-white dark:bg-zinc-900 shadow-lg rounded-xl p-8 ring-1 ring-zinc-200 dark:ring-white/10">
            <div class="text-center mb-8">
                <i class="fas fa-play-circle text-4xl text-green-500 mb-3"></i>
                <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">
                    Mulai Transaksi
                </h2>
                <p class="mt-1 text-md font-semibold text-zinc-500 dark:text-zinc-400">
                    {{ $meja->nama_meja }}
                </p>
            </div>

            <form action="{{ route('transaksi.store') }}" method="POST">
                @csrf
                <input type="hidden" name="meja_id" value="{{ $meja->id }}">

                <div class="space-y-6">
                    <div>
                        <label for="nama_pelanggan" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Nama Pelanggan
                        </label>
                        <div class="mt-1">
                            <input type="text" name="nama_pelanggan" id="nama_pelanggan" required
                                   placeholder="Masukkan nama pelanggan"
                                   class="form-input">
                        </div>
                    </div>

                    <div>
                        <label for="durasi" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Durasi (Jam)
                        </label>
                        <div class="mt-1 flex items-center gap-4">
                            <input type="number" name="durasi" id="durasi" min="1" required 
                                   placeholder="1"
                                   class="form-input w-24">
                            
                            <span class="px-3 py-1 text-sm font-semibold text-green-800 bg-green-100 rounded-full dark:bg-green-900/50 dark:text-green-300">
                                Rp {{ number_format($meja->tarif_per_jam, 0, ',', '.') }}/jam
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between mt-8">
                    <a href="{{ route('dashboard') }}" class="btn-secondary">
                        Kembali
                    </a>
                    <button type="submit" class="btn-primary bg-green-600 hover:bg-green-700 focus:ring-green-500">
                        Mulai Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    const btn  = document.querySelector('button[type="submit"]');

    form.addEventListener('submit', function () {
        btn.disabled = true;
        btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin mr-2"></i> Memproses...`;
    });
});
</script>
@endpush