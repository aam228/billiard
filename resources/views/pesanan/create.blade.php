@extends('layouts.app')

@section('content')
{{-- Komponen Notifikasi Popup --}}
<div x-data="{ show: false, type: '', message: '' }"
     x-init="
        @if(session('success'))
            show = true; type = 'success'; message = '{{ session('success') }}';
        @elseif(session('error'))
            show = true; type = 'error'; message = '{{ session('error') }}';
        @endif
     "
     x-show="show"
     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4"
     style="display: none;">

    <div @click.away="show = false"
         x-show="show"
         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
         class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-xl text-center p-6">

        <div class="w-16 h-16 rounded-full mx-auto flex items-center justify-center"
             :class="{ 'bg-green-100': type === 'success', 'bg-red-100': type === 'error' }">
            <i class="text-4xl" 
               :class="{ 'fa-solid fa-check text-green-600': type === 'success', 'fa-solid fa-times text-red-600': type === 'error' }"></i>
        </div>

        <h3 class="text-2xl font-bold mt-4 text-gray-800 dark:text-white"
            x-text="type === 'success' ? 'Berhasil!' : 'Terjadi Kesalahan'"></h3>
        
        <p class="text-gray-600 dark:text-gray-300 mt-2" x-text="message"></p>

        <button @click="show = false"
                class="mt-6 w-full px-4 py-2 rounded-lg text-white font-semibold"
                :class="{ 'bg-green-600 hover:bg-green-700': type === 'success', 'bg-red-600 hover:bg-red-700': type === 'error' }">
            Tutup
        </button>
    </div>
</div>

{{-- Layout utama dikembalikan seperti semula, dengan tambahan x-data untuk modal daftar pesanan --}}
<div x-data="{ isModalOpen: false }">
    <h4 class="text-2xl font-bold text-gray-800 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-3 mb-6">
        Pesan Makanan - Meja {{ $transaksi->meja->nama_meja }}
    </h4>

    {{-- Alert banner lama sudah dihapus --}}

    <form id="pesananForm" action="{{ route('pesanan.store') }}" method="POST">
        @csrf
        <input type="hidden" name="transaksi_id" value="{{ $transaksi->id }}">

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 mb-6">
            @forelse ($produk as $item)
                <div class="bg-white dark:bg-gray-900 rounded-lg shadow-md border dark:border-gray-700 flex flex-col overflow-hidden">
                    <div class="aspect-square w-full">
                        @if($item->gambar)
                            <img src="{{ asset('storage/' . $item->gambar) }}" alt="{{ $item->nama_produk }}" class="w-full h-full object-cover">
                        @else
                            <div class="flex flex-col items-center justify-center h-full bg-gray-100 dark:bg-gray-700 text-gray-400">
                                <i class="fa-solid fa-image text-3xl"></i>
                                <p class="text-xs mt-1">Tidak ada gambar</p>
                            </div>
                        @endif
                    </div>

                    <div class="p-2 text-center">
                        <h6 class="text-sm font-semibold text-gray-800 dark:text-white truncate" title="{{ $item->nama_produk }}">{{ $item->nama_produk }}</h6>
                        <p class="text-xs text-green-700 dark:text-green-400">Rp {{ number_format($item->harga, 0, ',', '.') }}</p>
                    </div>

                    <div class="p-2 border-t dark:border-gray-600 mt-auto">
                        <div class="flex items-center justify-center">
                            <button type="button" class="change-btn px-2 py-1 border bg-gray-100 dark:bg-gray-700 dark:border-gray-600 rounded-l-md hover:bg-gray-200" data-id="{{ $item->id }}" data-change="-1">
                                <i class="fa-solid fa-minus text-xs"></i>
                            </button>
                            <input type="number" name="produk[{{ $item->id }}]" id="jumlah-{{ $item->id }}" data-harga="{{ $item->harga }}" min="0" value="0"
                                   class="jumlah-input w-12 text-center text-sm border-t border-b dark:bg-gray-900 dark:text-white dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                            <button type="button" class="change-btn px-2 py-1 border bg-gray-100 dark:bg-gray-700 dark:border-gray-600 rounded-r-md hover:bg-gray-200" data-id="{{ $item->id }}" data-change="1">
                                <i class="fa-solid fa-plus text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-yellow-100 text-yellow-800 text-center p-4 rounded-md">
                    ❌ Tidak ada produk tersedia.
                </div>
            @endforelse
        </div>

        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-md border dark:border-gray-700 p-4 mb-6 flex justify-between items-center">
            <h5 class="text-lg font-medium text-gray-600 dark:text-gray-300">Total Pesanan</h5>
            <h4 class="text-2xl font-bold text-green-600" id="totalHarga">Rp 0</h4>
        </div>

        <div class="flex justify-end items-center gap-3">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-gray-500 rounded-full text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50">
                <i class="fa-solid fa-house"></i>Dashboard
            </a>
            <button type="button" @click="isModalOpen = true" class="inline-flex items-center gap-2 px-4 py-2 border border-transparent rounded-full text-sm font-medium text-white bg-gray-800 dark:bg-gray-600 hover:bg-gray-900">
                <i class="fa-solid fa-list-check"></i>Daftar Pesanan
            </button>
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 border border-transparent rounded-full text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50">
                <i class="fa-solid fa-cart-plus"></i>Konfirmasi
            </button>
        </div>
    </form>
    
    {{-- Modal Daftar Pesanan --}}
    <div x-show="isModalOpen" x-transition class="fixed inset-0 bg-black bg-opacity-50 z-30" @click="isModalOpen = false"></div>
    <div x-show="isModalOpen" x-transition class="fixed inset-0 z-40 flex items-center justify-center p-4">
        <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-lg shadow-xl">
             <div class="flex items-center justify-between p-4 border-b dark:border-gray-700">
                <h5 class="text-lg font-semibold text-gray-900 dark:text-white"><i class="fa-solid fa-list-check mr-2"></i>Daftar Pesanan</h5>
                <button @click="isModalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-white">&times;</button>
            </div>
            <div class="p-6">
                @if($pesananMakanan->count())
                    @php $totalHarga = 0; @endphp
                    <div class="space-y-3 mb-4">
                        @foreach($pesananMakanan as $pesanan)
                            @php
                                $subtotal = $pesanan->produk->harga * $pesanan->jumlah;
                                $totalHarga += $subtotal;
                            @endphp
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="font-semibold text-gray-800 dark:text-white">{{ $pesanan->produk->nama_produk }}</div>
                                    <small class="text-gray-500 dark:text-gray-400">Rp {{ number_format($pesanan->produk->harga, 0, ',', '.') }} x {{ $pesanan->jumlah }}</small>
                                </div>
                                <span class="px-2 py-0.5 text-xs text-white bg-gray-700 dark:bg-gray-600 rounded-full">{{ $pesanan->jumlah }} pcs</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="text-right border-t dark:border-gray-700 pt-3">
                        <strong class="text-gray-900 dark:text-white">Total: Rp {{ number_format($totalHarga, 0, ',', '.') }}</strong>
                    </div>
                @else
                    <p class="text-gray-500 italic text-center">❌ Belum ada produk yang dipesan.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Script vanilla JS dikembalikan --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const updateTotal = () => {
        let total = 0;
        document.querySelectorAll('.jumlah-input').forEach(input => {
            const jumlah = parseInt(input.value) || 0;
            const harga = parseInt(input.dataset.harga) || 0;
            total += jumlah * harga;
        });
        document.getElementById('totalHarga').textContent = 'Rp ' + total.toLocaleString('id-ID');
    };

    document.querySelectorAll('.change-btn').forEach(button => {
        button.addEventListener('click', () => {
            const id = button.dataset.id;
            const change = parseInt(button.dataset.change);
            const input = document.getElementById(`jumlah-${id}`);
            let current = parseInt(input.value) || 0;
            input.value = Math.max(0, current + change);
            updateTotal();
        });
    });

    document.querySelectorAll('.jumlah-input').forEach(input => {
        input.addEventListener('change', () => {
            if (input.value === '' || parseInt(input.value) < 0) {
                input.value = 0;
            }
            updateTotal();
        });
    });

    // Panggil sekali saat halaman dimuat untuk memastikan totalnya 0
    updateTotal();
});
</script>
@endpush