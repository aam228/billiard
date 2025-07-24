@extends('layouts.app')

@section('head')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('content')
<div class="container py-4">
    <h4 class="mb-4">📊 Halaman Analytics</h4>

    <!-- Tab Navigasi -->
    <ul class="nav nav-tabs mb-4" id="analyticsTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="ringkasan-tab" data-bs-toggle="tab" href="#ringkasan" role="tab">Ringkasan Cepat</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="meja-tab" data-bs-toggle="tab" href="#meja" role="tab">Analisis Meja</a>
        </li>
    </ul>

    <div class="tab-content" id="analyticsTabContent">
        <!-- Tab Ringkasan -->
        <div class="tab-pane fade show active" id="ringkasan" role="tabpanel">
            <div class="row mb-4">
                <!-- Total Hari Ini -->
                <div class="col-md-3">
                    <div class="card shadow-sm bg-success text-white">
                        <div class="card-body">
                            <h6 class="mb-1">Total Hari Ini</h6>
                            <h4>Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
                <!-- Total Bulan Ini -->
                <div class="col-md-3">
                    <div class="card shadow-sm bg-primary text-white">
                        <div class="card-body">
                            <h6 class="mb-1">Total Bulan Ini</h6>
                            <h4>Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
                <!-- Jumlah Transaksi -->
                <div class="col-md-3">
                    <div class="card shadow-sm bg-info text-white">
                        <div class="card-body">
                            <h6 class="mb-1">Jumlah Transaksi</h6>
                            <h4>{{ $jumlahTransaksi }}</h4>
                        </div>
                    </div>
                </div>
                <!-- Rata-rata Durasi -->
                <div class="col-md-3">
                    <div class="card shadow-sm bg-warning text-dark">
                        <div class="card-body">
                            <h6 class="mb-1">Rata-rata Durasi</h6>
                            <h4>{{ number_format($rataRataDurasi, 1) }} jam</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grafik Pendapatan Harian -->
            <div class="card mb-4">
                <div class="card-body">
                    <h6>📈 Grafik Pendapatan Harian</h6>
                    <canvas id="chartPendapatan"></canvas>
                </div>
            </div>
        </div>

        <!-- Tab Analisis Meja -->
        <div class="tab-pane fade" id="meja" role="tabpanel">
            <div class="row">
                <!-- Pendapatan per Meja -->
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h6>💰 Pendapatan per Meja</h6>
                            <canvas id="chartMeja"></canvas>
                        </div>
                    </div>
                </div>
                <!-- Jam Sibuk -->
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h6>⏰ Jam Sibuk</h6>
                            <canvas id="chartJamSibuk"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Data dari controller (PHP -> JavaScript)
    const pendapatanPerHari = @json($pendapatanPerHari);
    const pendapatanPerMeja = @json($pendapatanPerMeja);
    const jamSibuk = @json($jamSibuk);

    // Grafik Pendapatan Harian
    const ctxPendapatan = document.getElementById('chartPendapatan').getContext('2d');
    new Chart(ctxPendapatan, {
        type: 'line',
        data: {
            labels: pendapatanPerHari.map(item => item.tanggal),
            datasets: [{
                label: 'Pendapatan',
                data: pendapatanPerHari.map(item => item.total),
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.2)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: true } }
        }
    });

    // Grafik Pendapatan per Meja
    const ctxMeja = document.getElementById('chartMeja').getContext('2d');
    new Chart(ctxMeja, {
        type: 'bar',
        data: {
            labels: @json($pendapatanPerMeja->pluck('nama_meja')), // tampilkan nama_meja
            datasets: [{
                label: 'Pendapatan',
                data: @json($pendapatanPerMeja->pluck('total')), // datanya dari SUM(total)
                backgroundColor: '#fd7e14'
            }]
        },
        options: {
            responsive: true,
            indexAxis: 'y' // bikin grafik horizontal
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
                backgroundColor: '#6f42c1'
            }]
        },
        options: {
            responsive: true
        }
    });
</script>
@endpush
