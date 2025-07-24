@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pesan.css') }}"> 
@endpush

@section('content')
<div class="container-fluid py-4">
    <h4 class="fw-bold border-bottom pb-2 mb-4">Pesan Makanan - Meja {{ $transaksi->meja->nama_meja }}</h4>

    @foreach (['success', 'error'] as $msg)
        @if (session($msg))
            <div class="alert alert-{{ $msg === 'success' ? 'success' : 'danger' }} alert-dismissible fade show" role="alert">
                {{ session($msg) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    @endforeach

    <form id="pesananForm" action="{{ route('pesanan.store') }}" method="POST">
        @csrf
        <input type="hidden" name="transaksi_id" value="{{ $transaksi->id }}">

        <div class="row g-4 mb-4">
            @forelse ($produk as $item)
                <div class="col-6 col-md-4 col-lg-3 col-xl-20percent">
                    <div class="product-card">
                        <div class="product-image-wrapper">
                            @if($item->gambar)
                                <img src="{{ asset('storage/' . $item->gambar) }}" alt="{{ $item->nama_produk }}" class="product-image">
                            @else
                                <div class="image-placeholder">
                                    <span class="bi bi-image"></span>
                                    <p>Tidak ada gambar</p>
                                </div>
                            @endif
                        </div>

                        <div class="product-info">
                            <h6 class="product-name">{{ $item->nama_produk }}</h6>
                            <p class="product-price">Rp {{ number_format($item->harga, 0, ',', '.') }}</p>
                        </div>

                        <div class="product-actions-footer">
                            <div class="input-group input-group-sm justify-content-center" style="width: 100%;">
                                <button type="button" class="btn btn-outline-secondary change-btn px-2" data-id="{{ $item->id }}" data-change="-1">
                                    <i class="bi bi-dash"></i>
                                </button>
                                <input type="number" class="form-control jumlah-input text-center" 
                                    name="produk[{{ $item->id }}]" id="jumlah-{{ $item->id }}"
                                    data-harga="{{ $item->harga }}" min="0" value="0">
                                <button type="button" class="btn btn-outline-secondary change-btn px-2" data-id="{{ $item->id }}" data-change="1">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-warning text-center">❌ Tidak ada produk tersedia.</div>
                </div>
            @endforelse
        </div>

        <div class="card mb-4 shadow-sm border">
            <div class="card-body d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-secondary">Total Pesanan</h5>
                <h4 class="mb-0 text-success fw-bold" id="totalHarga">Rp 0</h4>
            </div>
        </div>

        <div class="text-end">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary rounded-pill px-4 py-2 me-2">
                <i class="bi bi-house-door me-2"></i>Dashboard
            </a>
            <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 me-2">
                <i class="bi bi-cart-plus me-2"></i>Konfirmasi
            </button>
            <button type="button" class="btn btn-dark rounded-pill px-4 py-2" data-bs-toggle="modal" data-bs-target="#simplePopup">
                <i class="bi bi-list-check me-2"></i>Daftar Pesanan
            </button>
        </div>
    </form>
</div>

{{-- Modal --}}
<div class="modal fade" id="simplePopup" tabindex="-1" aria-labelledby="popupTitle" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border shadow-sm">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-semibold" id="popupTitle"><i class="bi bi-list-check me-2"></i>Daftar Pesanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                @if($pesananMakanan->count())
                    @php $totalHarga = 0; @endphp
                    <ul class="list-group mb-3">
                        @foreach($pesananMakanan as $pesanan)
                            @php
                                $subtotal = $pesanan->produk->harga * $pesanan->jumlah;
                                $totalHarga += $subtotal;
                            @endphp
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="me-auto">
                                    <div class="fw-semibold">{{ $pesanan->produk->nama_produk }}</div>
                                    <small class="text-muted">Rp {{ number_format($pesanan->produk->harga, 0, ',', '.') }} x {{ $pesanan->jumlah }}</small>
                                </div>
                                <span class="badge bg-dark text-white rounded-pill">{{ $pesanan->jumlah }} pcs</span>
                            </li>
                        @endforeach
                    </ul>
                    <div class="text-end border-top pt-2">
                        <strong>Total: Rp {{ number_format($totalHarga, 0, ',', '.') }}</strong>
                    </div>
                @else
                    <p class="text-muted fst-italic text-center">❌ Belum ada produk yang dipesan.</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Script --}}
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
            input.value = Math.max(0, parseInt(input.value) || 0);
            updateTotal();
        });
    });
});
</script>
@endsection
