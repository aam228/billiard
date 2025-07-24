@extends('layouts.app')

@section('content')
<div class="wadah-histori">
    <h4 class="judul-seksi jarak-bawah-2">Laporan Keuangan</h4>

    <div class="kartu jarak-bawah-2">
        <form method="GET" action="{{ route('transaksi.laporan') }}" class="formulir-filter">
            <div class="grid-filter-empat">
                <div class="group-formulir">
                    <label for="tanggal_mulai" class="label-formulir">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="kontrol-input" value="{{ request('tanggal_mulai') }}">
                </div>

                <div class="group-formulir">
                    <label for="tanggal_selesai" class="label-formulir">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="kontrol-input" value="{{ request('tanggal_selesai') }}">
                </div>

                <div class="group-formulir">
                    <label for="nama_pelanggan" class="label-formulir">Nama Pelanggan</label>
                    <input type="text" name="nama_pelanggan" id="nama_pelanggan" class="kontrol-input" placeholder="Cari nama pelanggan..." value="{{ request('nama_pelanggan') }}">
                </div>

                <div class="group-formulir">
                    <label for="meja_id" class="label-formulir">Meja</label>
                    <select name="meja_id" id="meja_id" class="kontrol-input">
                        <option value="">Semua Meja</option>
                        @foreach($mejas as $meja)
                            <option value="{{ $meja->id }}" {{ request('meja_id') == $meja->id ? 'selected' : '' }}>
                                {{ $meja->nama_meja ?? 'Meja #' . $meja->id }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="jarak-atas-2 aksi-filter">
                <button type="submit" class="tombol garis-luar">
                    🔍 Filter
                </button>
                <a href="{{ route('transaksi.cetak', request()->query()) }}" class="tombol garis-luar" target="_blank">
                    🖨 Cetak PDF
                </a>
            </div>
        </form>
    </div>

    <div class="kartu">
        <div class="jarak-bawah-2">
            <h5>Total Pendapatan: 
                <span style="color: #28a745; font-weight: 600;">
                    Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                </span>
            </h5>
        </div>

        <div class="pembungkus-tabel">
            <table class="tabel-kustom">
                <thead>
                    <tr>
                        <th>Nama Pelanggan</th>
                        <th>Meja</th>
                        <th>Durasi (Jam)</th>
                        <th>Total Harga</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksis as $transaksi)
                        <tr>
                            <td>{{ $transaksi->nama_pelanggan }}</td>
                            <td>{{ $transaksi->meja->nama_meja ?? 'Meja #' . $transaksi->meja_id }}</td>
                            <td>{{ $transaksi->durasi }}</td>
                            <td class="tampilan-tarif">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                            <td>{{ $transaksi->created_at->format('Y-m-d H:i') }} - {{ $transaksi->waktu_selesai->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center;">Tidak ada data transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/transaksi.css') }}">
@endpush
