@extends('layouts.app')

@section('content')
<div>
    {{-- Header Halaman --}}
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center border-b border-zinc-200 dark:border-zinc-700 pb-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-zinc-800 dark:text-white">
                @yield('page-title', 'Dashboard')
            </h2>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                @yield('breadcrumb', 'Home / Dashboard')
            </p>
        </div>
    </div>

    {{-- Kartu Statistik --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6 mb-6">
        <div class="bg-white dark:bg-zinc-900/50 dark:ring-1 dark:ring-white/10 rounded-lg shadow-md p-5 border-t-4 border-green-500">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-3xl font-semibold text-zinc-900 dark:text-white">
                        {{ $mejas->where('status', 'tersedia')->count() }}
                    </h3>
                    <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Meja Tersedia</p>
                </div>
                <div class="flex items-center justify-center w-12 h-12 rounded-full bg-green-100 dark:bg-green-900/20 text-green-600 dark:text-green-400">
                    <i class="fas fa-check text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900/50 dark:ring-1 dark:ring-white/10 rounded-lg shadow-md p-5 border-t-4 border-orange-500">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-3xl font-semibold text-zinc-900 dark:text-white">
                        {{ $mejas->where('status', 'digunakan')->count() }}
                    </h3>
                    <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Meja Digunakan</p>
                </div>
                <div class="flex items-center justify-center w-12 h-12 rounded-full bg-orange-100 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400">
                    <i class="fas fa-play text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900/50 dark:ring-1 dark:ring-white/10 rounded-lg shadow-md p-5 border-t-4 border-blue-500">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-3xl font-semibold text-zinc-900 dark:text-white">
                        {{ $mejas->count() }}
                    </h3>
                    <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Total Meja</p>
                </div>
                <div class="flex items-center justify-center w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400">
                    <i class="fas fa-table text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    @if($mejas->count() > 0)
    {{-- Kartu Meja --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    @foreach ($mejas as $meja)
        <div 
            class="bg-white dark:bg-zinc-900/50 dark:ring-1 dark:ring-white/10 rounded-lg shadow-md flex flex-col h-full"
            data-meja-id="{{ $meja->id }}"
            data-tarif-per-jam="Rp {{ number_format($meja->tarif_per_jam, 0, ',', '.') }}"
        >
            <div class="flex justify-between items-center p-4 border-b dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/50 rounded-t-lg">
                <div class="flex items-center gap-3">
                    <i class="fas fa-circle text-xs {{ $meja->status == 'digunakan' ? 'text-orange-500 animate-pulse' : 'text-green-500' }}"></i>
                    <span class="font-semibold text-zinc-800 dark:text-white">{{ $meja->nama_meja }}</span>
                </div>
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-white focus:outline-none">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white dark:bg-zinc-900 rounded-md shadow-lg py-1 z-20 ring-1 ring-black ring-opacity-5 dark:ring-white dark:ring-opacity-10">
                        @if ($meja->status == 'digunakan' && $meja->transaksi_aktif)
                            <a href="{{ route('transaksi.histori', $meja->transaksi_aktif->id) }}" class="block px-4 py-2 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800">Lihat Detail</a>
                            <a href="{{ route('pesanan.create', $meja->transaksi_aktif->id) }}" class="block px-4 py-2 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800">Pesan Makanan</a>
                            <a href="{{ route('transaksi.selesai', $meja->transaksi_aktif->id) }}" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/50">Selesaikan</a>
                        @else
                            <a href="{{ route('transaksi.create', $meja->id) }}" class="block px-4 py-2 text-sm text-green-600 hover:bg-green-50 dark:hover:bg-green-900/50">Mulai Transaksi</a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="p-4 flex-grow flex flex-col">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <strong class="text-xs uppercase text-zinc-400 dark:text-zinc-500">Status</strong>
                        <div class="mt-1">
                            @if($meja->status == 'digunakan')
                                <span class="px-2 py-1 text-xs font-semibold text-orange-800 bg-orange-100 rounded-full dark:bg-orange-900/50 dark:text-orange-300">{{ ucfirst($meja->status) }}</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full dark:bg-green-900/50 dark:text-green-300">{{ ucfirst($meja->status) }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="border-t dark:border-zinc-700 pt-4 mt-auto">
                @if ($meja->status == 'digunakan' && $meja->transaksi_aktif)
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between items-center">
                            <span class="text-zinc-500 dark:text-zinc-400 font-medium"><i class="fas fa-user w-4 mr-2"></i>Nama</span>
                            <span class="font-semibold text-zinc-800 dark:text-white truncate">{{ $meja->transaksi_aktif->nama_pelanggan ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-zinc-500 dark:text-zinc-400 font-medium"><i class="fas fa-clock w-4 mr-2"></i>Mulai</span>
                            <span class="font-semibold text-zinc-800 dark:text-white">{{ $meja->transaksi_aktif->waktu_mulai->format('H:i') }}</span>
                        </div>
                    </div>
                    <div class="mt-4 p-2 text-center rounded-md bg-zinc-100 dark:bg-zinc-700/50 font-mono text-lg font-bold text-red-600 dark:text-red-400">
                        <i class="fas fa-stopwatch text-base mr-2"></i>
                        <span id="countdown-{{ $meja->id }}" data-waktuselesai="{{ optional($meja->transaksi_aktif->waktu_selesai)->toIso8601String() }}"></span>
                    </div>
                @else
                    <div class="space-y-3 text-sm mb-4">
                        <div class="flex justify-between items-center">
                            <span class="text-zinc-500 dark:text-zinc-400 font-medium"><i class="fas fa-money-bill-wave w-4 mr-2"></i>Tarif/Jam</span>
                            <span class="font-bold text-green-600 dark:text-green-500">Rp {{ number_format($meja->tarif_per_jam, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <a href="{{ route('transaksi.create', $meja->id) }}"
                    class="w-full flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition">
                    <i class="fas fa-play"></i>
                    Pesan Meja
                    </a>
                @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-16">
        <i class="fas fa-table text-6xl text-zinc-300 dark:text-zinc-600 mb-4"></i>
        <h3 class="text-xl font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Belum Ada Meja</h3>
        <p class="text-zinc-500 dark:text-zinc-400 mb-4">
            Silakan tambahkan meja untuk mulai mengelola bisnis biliar Anda.
        </p>
        <a href="{{ route('meja.index') }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
            <i class="fas fa-plus"></i>
            Tambah Meja
        </a>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const countdownElements = document.querySelectorAll('[id^="countdown-"]');

    countdownElements.forEach(function(element) {
        const waktuSelesai = element.getAttribute('data-waktuselesai');
        if (waktuSelesai) {
            const endTime = new Date(waktuSelesai).getTime();
            const card = element.closest('.bg-white'); // Kartu Meja

            // Ambil URL Selesaikan dari dropdown sebelum interval dimulai
            const selesaiLink = card.querySelector('a[href*="/transaksi/"][href*="/selesai"]');
            const finishUrl = selesaiLink ? selesaiLink.href : null;

            const interval = setInterval(async function() {
                const now = new Date().getTime();
                const distance = endTime - now;

                if (distance < 0) {
                    clearInterval(interval);
                    element.innerHTML = "Waktu Habis";
                    
                    if (finishUrl) {
                        try {
                            const response = await fetch(finishUrl, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Content-Type': 'application/json'
                                }
                            });

                            if (response.ok) {
                                console.log('Transaksi selesai di backend. Memperbarui UI.');

                                updateMejaCard(card);
                            } else {
                                console.error('Gagal menyelesaikan transaksi di backend:', response.statusText);
                                location.reload();
                            }

                        } catch (error) {
                            console.error('Error saat memanggil endpoint selesai:', error);
                            location.reload();
                        }
                    } else {
                        location.reload();
                    }
                    return;
                }

                const hours = String(Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))).padStart(2, '0');
                const minutes = String(Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
                const seconds = String(Math.floor((distance % (1000 * 60)) / 1000)).padStart(2, '0');
                element.innerHTML = `${hours}:${minutes}:${seconds}`;
            }, 1000);
        }
    });
    
    function updateMejaCard(card) {
        const tarifPerJam = 'Rp 50.000';

        const statusCircle = card.querySelector('.fa-circle');
        if (statusCircle) {
            statusCircle.classList.remove('text-orange-500', 'animate-pulse');
            statusCircle.classList.add('text-green-500');
        }

        const statusBadge = card.querySelector('.px-2.py-1');
        if (statusBadge) {
             statusBadge.classList.remove('text-orange-800', 'bg-orange-100', 'dark:bg-orange-900/50', 'dark:text-orange-300');
             statusBadge.classList.add('text-green-800', 'bg-green-100', 'dark:bg-green-900/50', 'dark:text-green-300');
             statusBadge.textContent = 'Tersedia';
        }

        const dropdownContent = card.querySelector('.absolute.right-0.mt-2');
        if (dropdownContent) {
            dropdownContent.innerHTML = `
                <a href="/transaksi/create/${getMejaId(card)}" class="block px-4 py-2 text-sm text-green-600 hover:bg-green-50 dark:hover:bg-green-900/50">Mulai Transaksi</a>
            `;
        }

        const contentArea = card.querySelector('.border-t.dark\\:border-zinc-700.pt-4');
        if (contentArea) {
            contentArea.innerHTML = `
                <div class="space-y-3 text-sm mb-4">
                    <div class="flex justify-between items-center">
                        <span class="text-zinc-500 dark:text-zinc-400 font-medium"><i class="fas fa-money-bill-wave w-4 mr-2"></i>Tarif/Jam</span>
                        <span class="font-bold text-green-600 dark:text-green-500">${tarifPerJam}</span>
                    </div>
                </div>
                <a href="/transaksi/create/${getMejaId(card)}"
                class="w-full flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition">
                    <i class="fas fa-play"></i>
                    Pesan Meja
                </a>
            `;
        }
        
    }

    function getMejaId(card) {
        return card.getAttribute('data-meja-id'); 
    }
});
</script>
@endpush