@extends('layouts.app')

@push('styles')
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
        <div>
            <h2 class="fw-semibold text-dark mb-1">@yield('page-title', 'Dashboard')</h2>
            <small class="text-muted">@yield('breadcrumb', 'Home / Dashboard')</small>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100 position-relative">
                <div class="position-absolute top-0 start-0 w-100 stat-card-border-green"></div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h3 class="fw-semibold text-dark mb-1 stat-value-lg">
                                {{ $mejas->where('status', 'tersedia')->count() }}
                            </h3>
                            <p class="text-muted fw-medium mb-0 stat-label-sm">Meja Tersedia</p>
                        </div>
                        <div class="d-flex align-items-center justify-content-center rounded stat-icon-green">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100 position-relative">
                <div class="position-absolute top-0 start-0 w-100 stat-card-border-orange"></div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h3 class="fw-semibold text-dark mb-1 stat-value-lg">
                                {{ $mejas->where('status', 'digunakan')->count() }}
                            </h3>
                            <p class="text-muted fw-medium mb-0 stat-label-sm">Meja Digunakan</p>
                        </div>
                        <div class="d-flex align-items-center justify-content-center rounded stat-icon-orange">
                            <i class="fas fa-play"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100 position-relative">
                <div class="position-absolute top-0 start-0 w-100 stat-card-border-blue"></div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h3 class="fw-semibold text-dark mb-1 stat-value-lg">
                                {{ $mejas->count() }}
                            </h3>
                            <p class="text-muted fw-medium mb-0 stat-label-sm">Total Meja</p>
                        </div>
                        <div class="d-flex align-items-center justify-content-center rounded stat-icon-blue">
                            <i class="fas fa-table"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($mejas->count() > 0)
    <div class="row g-3">
        @foreach ($mejas as $meja)
        <div class="col-12 col-md-6 col-lg-4 col-xl-3">
            <div class="card border shadow-sm h-100">
                <div class="card-header bg-light border-bottom d-flex justify-content-between align-items-center py-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-circle table-indicator-icon"></i>
                        <span class="fw-semibold text-dark table-name-text">{{ $meja->nama_meja }}</span>
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

                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <strong class="text-muted status-label-sm">STATUS</strong>
                            <div class="mt-1">
                                @if($meja->status == 'digunakan')
                                    <span class="badge rounded-pill px-2 py-1 badge-status-used">
                                        {{ ucfirst($meja->status) }}
                                    </span>
                                @else
                                    <span class="badge rounded-pill px-2 py-1 badge-status-available">
                                        {{ ucfirst($meja->status) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if ($meja->status == 'digunakan' && $meja->transaksi_aktif)
                    <div class="border-top pt-3">
                        <div class="d-flex justify-content-between align-items-center py-2 table-info-text-sm">
                            <div class="d-flex align-items-center gap-2 text-muted fw-medium">
                                <i class="fas fa-user"></i>
                                <span>Nama</span>
                            </div>
                            <div class="text-dark fw-semibold text-end table-info-value-truncate">
                                {{ $meja->transaksi_aktif->nama_pelanggan ?? '-' }}
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-2 table-info-text-sm">
                            <div class="d-flex align-items-center gap-2 text-muted fw-medium">
                                <i class="fas fa-clock"></i>
                                <span>Mulai</span>
                            </div>
                            <div class="text-dark fw-semibold">
                                {{ $meja->transaksi_aktif->waktu_mulai->format('H:i') }}
                            </div>
                        </div>

                        <div class="d-flex justify-content-center align-items-center gap-2 mt-3 p-2 rounded countdown-display-style">
                            <i class="fas fa-stopwatch"></i>
                            <span id="countdown-{{ $meja->id }}"
                                    data-waktuselesai="{{ optional($meja->transaksi_aktif->waktu_selesai)->toIso8601String() }}"></span>
                        </div>
                    </div>
                    @else
                    <div class="border-top pt-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center py-2 table-info-text-sm">
                            <div class="d-flex align-items-center gap-2 text-muted fw-medium">
                                <i class="fas fa-money-bill-wave"></i>
                                <span>Tarif/Jam</span>
                            </div>
                            <div class="fw-bold nominal">
                                Rp {{ number_format($meja->tarif_per_jam, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>

                    <div class="border-top pt-3">
                        <a href="{{ route('transaksi.create', $meja->id) }}"
                        class="btn d-flex align-items-center justify-content-center gap-2 w-100 btn-order-table-style">
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
    <div class="row">
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fas fa-table empty-state-icon-style"></i>
                <h3 class="fw-medium text-secondary mb-2 empty-state-title-style">Belum Ada Meja</h3>
                <p class="text-muted mb-3 empty-state-text-style">
                    Silakan tambahkan meja untuk mulai mengelola bisnis biliar Anda.
                </p>
                <a href="{{ route('meja.index') }}"
                   class="btn btn-primary d-inline-flex align-items-center gap-2 btn-add-table-style">
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