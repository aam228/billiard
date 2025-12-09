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
         class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-lg shadow-xl text-center p-6">

        <div class="w-16 h-16 rounded-lg mx-auto flex items-center justify-center"
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

{{-- Layout utama --}}
<div x-data="{ isModalOpen: false }">
    <h4 class="text-2xl font-bold text-gray-800 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-3 mb-6">
        Pesan Makanan - {{ $transaksi->meja->nama_meja }}
    </h4>

    <form id="pesananForm" action="{{ route('pesanan.store') }}" method="POST">
        @csrf
        <input type="hidden" name="transaksi_id" value="{{ $transaksi->id }}">

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 mb-6">
            @forelse ($produk as $item)
            {{-- Kartu Produk yang lebih baik --}}
            <div class="group relative bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-700">
                {{-- Gambar Produk dengan Overlay --}}
                <div class="relative w-full aspect-square overflow-hidden bg-gray-50 dark:bg-gray-900">
                    @if($item->gambar)
                        <img src="{{ asset('storage/' . $item->gambar) }}" 
                             alt="{{ $item->nama_produk }}" 
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                    @else
                        <div class="flex flex-col items-center justify-center h-full bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900">
                            <i class="fa-solid fa-utensils text-4xl text-gray-300 dark:text-gray-600 mb-2"></i>
                            <p class="text-xs text-gray-400 dark:text-gray-500">Tidak ada gambar</p>
                        </div>
                    @endif
                    
                    {{-- Badge jika jumlah > 0 --}}
                    <div class="absolute top-2 right-2 hidden" id="badge-{{ $item->id }}">
                        <span class="inline-flex items-center justify-center w-7 h-7 bg-green-600 text-white text-xs font-bold rounded-full shadow-lg">
                            <span id="badge-count-{{ $item->id }}">0</span>
                        </span>
                    </div>
                </div>

                {{-- Info Produk --}}
                <div class="p-3 flex flex-col">
                    <h6 class="text-sm font-semibold text-gray-800 dark:text-white mb-1.5 line-clamp-2 h-10" title="{{ $item->nama_produk }}">
                        {{ $item->nama_produk }}
                    </h6>
                    <div class="mb-2.5">
                        <span class="text-base font-bold text-green-600 dark:text-green-400">
                            Rp {{ number_format($item->harga, 0, ',', '.') }}
                        </span>
                    </div>
                    {{-- Tombol Kontrol Jumlah --}}
                    <div class="flex items-center w-full mt-auto">
                        <button type="button" 
                                class="change-btn flex-1 h-10 flex items-center justify-center bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-red-500 hover:text-white transition-all duration-200 rounded-l-lg" 
                                data-id="{{ $item->id }}" 
                                data-change="-1">
                            <i class="fa-solid fa-minus text-sm"></i>
                        </button>

                        <input type="number" 
                            name="produk[{{ $item->id }}]" 
                            id="jumlah-{{ $item->id }}" 
                            data-harga="{{ $item->harga }}" 
                            min="0" 
                            value="0"
                            class="jumlah-input h-10 w-16 text-center bg-white dark:bg-gray-900 border-y border-gray-200 dark:border-gray-600 text-sm font-bold text-gray-800 dark:text-white focus:outline-none">

                        <button type="button" 
                                class="change-btn flex-1 h-10 flex items-center justify-center bg-green-600 text-white hover:bg-green-700 transition-all duration-200 rounded-r-lg" 
                                data-id="{{ $item->id }}" 
                                data-change="1">
                            <i class="fa-solid fa-plus text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>
            @empty
                <div class="col-span-full bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 text-yellow-800 dark:text-yellow-200 text-center p-6 rounded-lg">
                    <i class="fa-solid fa-exclamation-circle text-2xl mb-2"></i>
                    <p class="font-medium">Tidak ada produk tersedia.</p>
                </div>
            @endforelse
        </div>

        {{-- Total Pesanan --}}
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-gray-800 dark:to-gray-900 rounded-xl shadow-md border border-green-100 dark:border-gray-700 p-5 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Pesanan</p>
                    <h4 class="text-3xl font-bold text-green-600 dark:text-green-400" id="totalHarga">Rp 0</h4>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Item</p>
                    <h4 class="text-2xl font-bold text-gray-800 dark:text-white" id="totalItem">0</h4>
                </div>
            </div>
        </div>

        {{-- Tombol Aksi --}}
        <div class="flex flex-col sm:flex-row justify-end items-stretch sm:items-center gap-3">
            <a href="{{ route('dashboard') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <i class="fa-solid fa-house"></i>
                <span>Dashboard</span>
            </a>
            <button type="button" 
                    @click="isModalOpen = true" 
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <i class="fa-solid fa-list-check"></i>
                <span>Daftar Pesanan</span>
            </button>
            <button type="submit" 
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 border border-transparent rounded-lg text-sm font-semibold text-white bg-green-600 hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 shadow-lg hover:shadow-xl transition-all duration-200 active:scale-95">
                <i class="fa-solid fa-cart-plus"></i>
                <span>Konfirmasi Pesanan</span>
            </button>
        </div>
    </form>
    
    {{-- Modal Daftar Pesanan --}}
    <div x-show="isModalOpen" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 z-30" 
         @click="isModalOpen = false"></div>
    
    <div x-show="isModalOpen" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0 scale-95" 
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100 scale-100" 
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-40 flex items-center justify-center p-4">
        <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-xl shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between p-5 border-b dark:border-gray-700 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-gray-900 dark:to-gray-800">
                <h5 class="text-xl font-bold text-gray-900 dark:text-white">
                    <i class="fa-solid fa-list-check mr-2 text-green-600"></i>Daftar Pesanan
                </h5>
                <button @click="isModalOpen = false" 
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 hover:text-gray-700 hover:bg-white dark:hover:bg-gray-700 dark:text-gray-400 dark:hover:text-white transition-colors">
                    <i class="fa-solid fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="p-6 max-h-[60vh] overflow-y-auto">
                @if($pesananMakanan->count())
                    @php $totalHarga = 0; @endphp
                    <div class="space-y-3 mb-4">
                        @foreach($pesananMakanan as $pesanan)
                            @php
                                $subtotal = $pesanan->produk->harga * $pesanan->jumlah;
                                $totalHarga += $subtotal;
                            @endphp
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-800 dark:text-white mb-1">
                                        {{ $pesanan->produk->nama_produk }}
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        Rp {{ number_format($pesanan->produk->harga, 0, ',', '.') }} × {{ $pesanan->jumlah }}
                                    </div>
                                </div>
                                <div class="text-right ml-4">
                                    <span class="inline-block px-3 py-1 text-xs font-bold text-white bg-green-600 rounded-full mb-1">
                                        {{ $pesanan->jumlah }} pcs
                                    </span>
                                    <div class="text-sm font-bold text-gray-800 dark:text-white">
                                        Rp {{ number_format($subtotal, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="border-t dark:border-gray-700 pt-4">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-700 dark:text-gray-300">Total Pembayaran</span>
                            <span class="text-2xl font-bold text-green-600 dark:text-green-400">
                                Rp {{ number_format($totalHarga, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="fa-solid fa-shopping-cart text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400 italic">Belum ada produk yang dipesan.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const updateTotal = () => {
        let total = 0;
        let totalItems = 0;
        
        document.querySelectorAll('.jumlah-input').forEach(input => {
            const jumlah = parseInt(input.value) || 0;
            const harga = parseInt(input.dataset.harga) || 0;
            const id = input.id.replace('jumlah-', '');
            
            total += jumlah * harga;
            totalItems += jumlah;
            
            // Update badge
            const badge = document.getElementById(`badge-${id}`);
            const badgeCount = document.getElementById(`badge-count-${id}`);
            
            if (jumlah > 0) {
                badge.classList.remove('hidden');
                badgeCount.textContent = jumlah;
            } else {
                badge.classList.add('hidden');
            }
        });
        
        document.getElementById('totalHarga').textContent = 'Rp ' + total.toLocaleString('id-ID');
        document.getElementById('totalItem').textContent = totalItems;
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
        
        // Prevent typing negative numbers
        input.addEventListener('keydown', (e) => {
            if (e.key === '-' || e.key === 'e' || e.key === '+') {
                e.preventDefault();
            }
        });
    });

    updateTotal();
});
</script>
@endpush