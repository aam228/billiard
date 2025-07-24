@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
        <div>
            <h2 class="fw-semibold text-dark mb-1">@yield('page-title', 'Dashboard')</h2>
            <small class="text-muted">@yield('breadcrumb', 'Home / Dashboard')</small>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="row g-3 mb-4">
        <!-- Meja Tersedia -->
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100 position-relative">
                <div class="position-absolute top-0 start-0 w-100" style="height: 3px; background: #059669;"></div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h3 class="fw-semibold text-dark mb-1" style="font-size: 1.875rem;">
                                {{ $mejas->where('status', 'tersedia')->count() }}
                            </h3>
                            <p class="text-muted fw-medium mb-0" style="font-size: 0.875rem;">Meja Tersedia</p>
                        </div>
                        <div class="d-flex align-items-center justify-content-center rounded" 
                             style="width: 40px; height: 40px; background: #059669; color: white;">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Meja Digunakan -->
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100 position-relative">
                <div class="position-absolute top-0 start-0 w-100" style="height: 3px; background: #d97706;"></div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h3 class="fw-semibold text-dark mb-1" style="font-size: 1.875rem;">
                                {{ $mejas->where('status', 'digunakan')->count() }}
                            </h3>
                            <p class="text-muted fw-medium mb-0" style="font-size: 0.875rem;">Meja Digunakan</p>
                        </div>
                        <div class="d-flex align-items-center justify-content-center rounded" 
                             style="width: 40px; height: 40px; background: #d97706; color: white;">
                            <i class="fas fa-play"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Meja -->
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100 position-relative">
                <div class="position-absolute top-0 start-0 w-100" style="height: 3px; background: #2563eb;"></div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h3 class="fw-semibold text-dark mb-1" style="font-size: 1.875rem;">
                                {{ $mejas->count() }}
                            </h3>
                            <p class="text-muted fw-medium mb-0" style="font-size: 0.875rem;">Total Meja</p>
                        </div>
                        <div class="d-flex align-items-center justify-content-center rounded" 
                             style="width: 40px; height: 40px; background: #2563eb; color: white;">
                            <i class="fas fa-table"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($mejas->count() > 0)
    <!-- Tables Grid -->
    <div class="row g-3">
        @foreach ($mejas as $meja)
        <div class="col-12 col-md-6 col-lg-4 col-xl-3">
            <div class="card border shadow-sm h-100">
                <!-- Card Header -->
                <div class="card-header bg-light border-bottom d-flex justify-content-between align-items-center py-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-circle text-primary" style="font-size: 1.2rem;"></i>
                        <span class="fw-semibold text-dark" style="font-size: 0.875rem;">{{ $meja->nama_meja }}</span>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary border-0" type="button" 
                                data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border">
                            @if ($meja->status == 'digunakan' && $meja->transaksi_aktif)
                                <li><a class="dropdown-item" href="{{ route('transaksi.histori', $meja->transaksi_aktif->id) }}">
                                    <small>Lihat Detail</small></a></li>
                                <li><a class="dropdown-item" href="{{ route('pesanan.create', $meja->transaksi_aktif->id) }}">
                                    <small>Pesan Makanan</small></a></li>
                                <li><a class="dropdown-item text-danger" href="{{ route('transaksi.selesai', $meja->transaksi_aktif->id) }}">
                                    <small>Selesaikan</small></a></li>
                            @else
                                <li><a class="dropdown-item text-success" href="{{ route('transaksi.create', $meja->id) }}">
                                    <small>Mulai Transaksi</small></a></li>
                            @endif
                        </ul>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="card-body p-3">
                    <!-- Status Section -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <strong class="text-muted" style="font-size: 0.75rem;">STATUS</strong>
                            <div class="mt-1">
                                @if($meja->status == 'digunakan')
                                    <span class="badge rounded-pill px-2 py-1" 
                                          style="background-color: #fef3c7; color: #92400e; font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">
                                        {{ ucfirst($meja->status) }}
                                    </span>
                                @else
                                    <span class="badge rounded-pill px-2 py-1" 
                                          style="background-color: #dcfce7; color: #166534; font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">
                                        {{ ucfirst($meja->status) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if ($meja->status == 'digunakan' && $meja->transaksi_aktif)
                    <!-- Table Info - Active Transaction -->
                    <div class="border-top pt-3">
                        <div class="d-flex justify-content-between align-items-center py-2" style="font-size: 0.8125rem;">
                            <div class="d-flex align-items-center gap-2 text-muted fw-medium">
                                <i class="fas fa-user"></i>
                                <span>Nama</span>
                            </div>
                            <div class="text-dark fw-semibold text-end" style="max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ $meja->transaksi_aktif->nama_pelanggan ?? '-' }}
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-2" style="font-size: 0.8125rem;">
                            <div class="d-flex align-items-center gap-2 text-muted fw-medium">
                                <i class="fas fa-clock"></i>
                                <span>Mulai</span>
                            </div>
                            <div class="text-dark fw-semibold">
                                {{ $meja->transaksi_aktif->waktu_mulai->format('H:i') }}
                            </div>
                        </div>
                        
                        <!-- Countdown Display -->
                        <div class="d-flex justify-content-center align-items-center gap-2 mt-3 p-2 rounded"
                             style="background-color: #fef2f2; color: #dc2626; font-weight: 600; font-size: 0.8125rem;">
                            <i class="fas fa-stopwatch"></i>
                            <span id="countdown-{{ $meja->id }}" 
                                  data-waktuselesai="{{ optional($meja->transaksi_aktif->waktu_selesai)->toIso8601String() }}"></span>
                        </div>
                    </div>
                    @else
                    <!-- Table Info - Available -->
                    <div class="border-top pt-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center py-2" style="font-size: 0.8125rem;">
                            <div class="d-flex align-items-center gap-2 text-muted fw-medium">
                                <i class="fas fa-money-bill-wave"></i>
                                <span>Tarif/Jam</span>
                            </div>
                            <div class="fw-bold">
                                Rp {{ number_format($meja->tarif_per_jam, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>

                    <!-- Action Section -->
                    <div class="border-top pt-3">
                        <a href="{{ route('transaksi.create', $meja->id) }}" 
                           class="btn btn-primary btn-sm d-flex align-items-center justify-content-center gap-2 w-100"
                           style="font-size: 0.8125rem; font-weight: 500; min-height: 36px;">
                            <i class="fas fa-play"></i>
                            Pesan Meja
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <!-- Empty State -->
    <div class="row">
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fas fa-table text-muted mb-3" style="font-size: 2.5rem; opacity: 0.5;"></i>
                <h3 class="fw-medium text-secondary mb-2" style="font-size: 1.125rem;">Belum Ada Meja</h3>
                <p class="text-muted mb-3" style="font-size: 0.875rem;">
                    Silakan tambahkan meja untuk mulai mengelola bisnis biliar Anda.
                </p>
                <a href="{{ route('meja.index') }}" 
                   class="btn btn-primary d-inline-flex align-items-center gap-2"
                   style="font-size: 0.8125rem; font-weight: 500;">
                    <i class="fas fa-plus"></i>
                    Tambah Meja
                </a>
            </div>
        </div>
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

            const countdownFunction = setInterval(function() {
                const now = new Date().getTime();
                const distance = endTime - now;

                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                const formattedHours = String(hours).padStart(2, '0');
                const formattedMinutes = String(minutes).padStart(2, '0');
                const formattedSeconds = String(seconds).padStart(2, '0');

                element.innerHTML = formattedHours + ":" + formattedMinutes + ":" + formattedSeconds;

                if (distance < 0) {
                    clearInterval(countdownFunction);
                    element.innerHTML = "Waktu Habis";

                    const tableCard = element.closest('.card');
                    const dropdownToggle = tableCard?.querySelector('.dropdown-toggle');
                    
                    const selesaiLink = tableCard?.querySelector('a.dropdown-item.text-danger[href*="/transaksi/"][href*="/selesai"]');

                    if (dropdownToggle && selesaiLink) {
                        if (dropdownToggle.getAttribute('aria-expanded') !== 'true') {
                            const handler = function() {
                                selesaiLink.click();
                                dropdownToggle.removeEventListener('shown.bs.dropdown', handler);
                            };
                            dropdownToggle.addEventListener('shown.bs.dropdown', handler);
                            dropdownToggle.click();
                        } else {
                            selesaiLink.click();
                        }
                    } else {
                        console.warn('Tombol dropdown atau link "Selesaikan" tidak ditemukan untuk meja dengan countdown:', element.id);
                        location.reload(); 
                    }
                }
            }, 1000);
        }
    });

    // Auto-refresh setiap 30 detik untuk update real-time
    setInterval(function() {
        location.reload();
    }, 30000);
});
</script>
@endpush