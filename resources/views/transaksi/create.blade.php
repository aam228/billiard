@extends('layouts.app')
@section('head')
    <link rel="stylesheet" href="{{ asset('css/create-transaksi.css') }}">
@endsection

@section('content')
<div class="form-wrapper">
    <div class="form-header">
        <i></i>
        <h4>Mulai Transaksi</h4>
        <p>{{ $meja->nama_meja }}</p>
    </div>

    <form action="{{ route('transaksi.store') }}" method="POST">
        @csrf
        <input type="hidden" name="meja_id" value="{{ $meja->id }}">

        <div class="form-group">
            <label for="nama_pelanggan">Nama Pelanggan</label>
            <input type="text" name="nama_pelanggan" id="nama_pelanggan" placeholder="Nama pelanggan" required>
        </div>

        <div class="form-group">
            <label for="durasi">Durasi</label>
            <div style="display: flex; align-items: center;">
                <input type="number" name="durasi" id="durasi" min="1" placeholder="Jam" required style="max-width: 70px;">
                <span class="badge">
                    Rp {{ number_format($meja->tarif_per_jam, 0, ',', '.') }}/jam
                </span>
            </div>
        </div>

        <div class="button-group">
            <button type="submit" id="btnSubmit" class="btn btn-primary">Mulai Transaksi</button>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    const btn  = document.querySelector('button[type="submit"]');

    form.addEventListener('submit', function () {
        btn.disabled = true;
        btn.textContent = 'Memproses...';
    });
});
</script>
@endpush