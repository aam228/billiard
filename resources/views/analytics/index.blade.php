@extends('layouts.app')

@section('head')
    {{-- Chart.js tetap diperlukan --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('content')
{{-- 
  Komponen Alpine.js utama untuk mengelola state halaman ini:
  - activeTab: Mengontrol tab mana yang sedang aktif.
--}}
<div x-data="{ activeTab: 'ringkasan' }" class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h4 class="text-2xl font-bold text-gray-800 dark:text-white border-b pb-4 mb-6 border-gray-200 dark:border-gray-700">
        <i class="fa-solid fa-chart-pie mr-2"></i> Halaman Analytics
    </h4>

    <!-- Navigasi Tab dengan Alpine.js -->
    <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
        <nav class="-mb-px flex space-x-6" aria-label="Tabs">
            <button @click="activeTab = 'ringkasan'"
                    :class="{
                        'border-blue-500 text-blue-600 dark:text-blue-400': activeTab === 'ringkasan',
                        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-200': activeTab !== 'ringkasan'
                    }"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                Ringkasan Cepat
            </button>
            <button @click="activeTab = 'meja'"
                    :class="{
                        'border-blue-500 text-blue-600 dark:text-blue-400': activeTab === 'meja',
                        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-200': activeTab !== 'meja'
                    }"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                Analisis Meja
            </button>
        </nav>
    </div>

    <!-- Konten Tab -->
    <div>
        <!-- Tab Ringkasan -->
        <div x-show="activeTab === 'ringkasan'" x-transition>
            {{-- Kartu Statistik Cepat --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-green-500 text-white p-5 rounded-lg shadow-lg">
                    <h6 class="text-sm font-medium uppercase">Total Hari Ini</h6>
                    <h4 class="text-2xl font-bold mt-1">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</h4>
                </div>
                <div class="bg-blue-500 text-white p-5 rounded-lg shadow-lg">
                    <h6 class="text-sm font-medium uppercase">Total Bulan Ini</h6>
                    <h4 class="text-2xl font-bold mt-1">Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</h4>
                </div>
                <div class="bg-sky-500 text-white p-5 rounded-lg shadow-lg">
                    <h6 class="text-sm font-medium uppercase">Jumlah Transaksi</h6>
                    <h4 class="text-2xl font-bold mt-1">{{ $jumlahTransaksi }}</h4>
                </div>
                <div class="bg-yellow-400 text-gray-800 p-5 rounded-lg shadow-lg">
                    <h6 class="text-sm font-medium uppercase">Rata-rata Durasi</h6>
                    <h4 class="text-2xl font-bold mt-1">{{ number_format($rataRataDurasi, 1) }} jam</h4>
                </div>
            </div>

            {{-- Grafik Pendapatan Harian --}}
            <div class="bg-white dark:bg-gray-900 dark:ring-1 dark:ring-white/10 rounded-lg shadow-md p-6">
                <h6 class="font-semibold text-gray-800 dark:text-white mb-4"><i class="fa-solid fa-arrow-trend-up mr-2"></i>Grafik Pendapatan Harian</h6>
                <canvas id="chartPendapatan"></canvas>
            </div>
        </div>

        <!-- Tab Analisis Meja -->
        <div x-show="activeTab === 'meja'" x-transition>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white dark:bg-gray-900 dark:ring-1 dark:ring-white/10 rounded-lg shadow-md p-6">
                    <h6 class="font-semibold text-gray-800 dark:text-white mb-4"><i class="fa-solid fa-table-cells mr-2"></i>Pendapatan per Meja</h6>
                    <canvas id="chartMeja"></canvas>
                </div>
                <div class="bg-white dark:bg-gray-900 dark:ring-1 dark:ring-white/10 rounded-lg shadow-md p-6">
                    <h6 class="font-semibold text-gray-800 dark:text-white mb-4"><i class="fa-solid fa-clock mr-2"></i>Jam Sibuk</h6>
                    <canvas id="chartJamSibuk"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Data dari controller (PHP -> JavaScript)
    const pendapatanPerHari = @json($pendapatanPerHari);
    const pendapatanPerMeja = @json($pendapatanPerMeja);
    const jamSibuk = @json($jamSibuk);
    
    // Konfigurasi umum untuk Chart.js agar sesuai dengan dark mode
    const isDarkMode = document.documentElement.classList.contains('dark');
    Chart.defaults.color = isDarkMode ? '#9ca3af' : '#6b7280';
    Chart.defaults.borderColor = isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';

    // Grafik Pendapatan Harian
    const ctxPendapatan = document.getElementById('chartPendapatan').getContext('2d');
    new Chart(ctxPendapatan, {
        type: 'line',
        data: {
            labels: pendapatanPerHari.map(item => item.tanggal),
            datasets: [{
                label: 'Pendapatan',
                data: pendapatanPerHari.map(item => item.total),
                borderColor: '#22c55e', // green-500
                backgroundColor: 'rgba(34, 197, 94, 0.2)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } }
        }
    });

    // Grafik Pendapatan per Meja
    const ctxMeja = document.getElementById('chartMeja').getContext('2d');
    new Chart(ctxMeja, {
        type: 'bar',
        data: {
            labels: pendapatanPerMeja.map(item => item.nama_meja),
            datasets: [{
                label: 'Pendapatan',
                data: pendapatanPerMeja.map(item => item.total),
                backgroundColor: '#3b82f6', // blue-500
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            indexAxis: 'y',
            plugins: { legend: { display: false } }
        }
    });

    // Grafik Jam Sibuk
    const ctxJam = document.getElementById('chartJamSibuk').getContext('2d');
    new Chart(ctxJam, {
        type: 'bar',
        data: {
            labels: jamSibuk.map(item => item.jam + ':00'),
            datasets: [{
                label: 'Jumlah Transaksi',
                data: jamSibuk.map(item => item.jumlah),
                backgroundColor: '#8b5cf6', // violet-500
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } }
        }
    });
});
</script>
@endpush