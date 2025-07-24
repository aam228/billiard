@extends('layouts.app')

@section('content')
<div class="wadah-histori">
    <h4 class="judul-seksi jarak-bawah-2">Histori Transaksi</h4>

    @if (session('success'))
        <div class="peringatan-sukses">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="peringatan-gagal">{{ session('error') }}</div>
    @endif

    <div class="kartu">
        <div class="pembungkus-tabel">
            <table class="tabel-kustom">
                <thead>
                    <tr>
                        <th>No</th> 
                        <th>Meja</th>
                        <th>Nama Pelanggan</th>
                        <th>Durasi (Jam)</th>
                        <th>Total Harga</th>
                        <th>Waktu Mulai</th>
                        <th>Waktu Selesai</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transaksis as $indeks => $transaksi)
                    <tr>
                        <td>{{ $indeks + 1 }}</td> 
                        <td>{{ $transaksi->meja->nama_meja ?? 'N/A' }}</td>
                        <td>{{ $transaksi->nama_pelanggan }}</td>
                        <td>{{ $transaksi->durasi }}</td>
                        <td class="tampilan-tarif">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                        <td>{{ optional($transaksi->waktu_mulai)->format('Y-m-d H:i:s') }}</td>
                        <td>{{ optional($transaksi->waktu_selesai)->format('Y-m-d H:i:s') }}</td>
                        <td>
                            <button class="tombol garis-luar kecil" data-modal="modalHapus{{ $transaksi->id }}">
                                🗑 Hapus
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="d-flex flex-column align-items-center justify-content-center mt-4">
            {{ $transaksis->links('pagination::bootstrap-5') }}
        </div>
        <a href="{{ route('transaksi.laporan') }}" class="tombol sekunder jarak-atas-2">
            📊 Laporan Transaksi
        </a>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
@foreach ($transaksis as $transaksi)
<div id="modalHapus{{ $transaksi->id }}" class="modal">
    <div class="isi-modal">
        <div class="kepala-modal">
            <h5>Konfirmasi Hapus</h5>
            <button class="tutup-modal" data-tutup="modalHapus{{ $transaksi->id }}">✕</button>
        </div>
        <div class="isi-badan">
            <p>Apakah Anda yakin ingin menghapus transaksi ini?</p>
            <ul>
                <li>Meja: {{ $transaksi->meja->nama_meja ?? 'N/A' }}</li>
                <li>Pelanggan: {{ $transaksi->nama_pelanggan }}</li>
                <li>Total: Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</li>
            </ul>
        </div>
        <div class="kaki-modal">
            <button class="tombol sekunder kecil" data-tutup="modalHapus{{ $transaksi->id }}">
                Batal
            </button>
            <form action="{{ route('transaksi.hapus', $transaksi->id) }}" method="POST" class="formulir-hapus">
                @csrf
                @method('DELETE')
                <button type="submit" class="tombol garis-luar kecil">
                    Hapus
                </button>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/transaksi.css') }}">

<style>
    @media (min-width: 576px) {
        .d-none.flex-sm-fill.d-sm-flex.align-items-sm-center.justify-content-sm-between {
            flex-direction: column !important; /* Forces vertical stacking */
            justify-content: space-between !important; /* Pushes content to top and bottom */
            align-items: center !important; /* Centers content horizontally within the column */
            width: 100%; /* Ensure it spans full width to allow centering */
        }

        nav.d-flex.justify-items-center.justify-content-between {
            flex-direction: column !important; /* Make the outer nav also a column */
            align-items: center !important; /* Center its children horizontally */
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Buka modal
    document.querySelectorAll('[data-modal]').forEach(tombol => {
        tombol.addEventListener('click', () => {
            const id = tombol.getAttribute('data-modal');
            document.getElementById(id)?.classList.add('aktif');
        });
    });

    // Tutup modal
    document.querySelectorAll('[data-tutup]').forEach(tombol => {
        tombol.addEventListener('click', () => {
            const id = tombol.getAttribute('data-tutup');
            document.getElementById(id)?.classList.remove('aktif');
        });
    });

    // Konfirmasi sebelum hapus
    document.querySelectorAll('.formulir-hapus').forEach(formulir => {
        formulir.addEventListener('submit', function(e) {
            if (!confirm('Anda yakin ingin menghapus transaksi ini?')) {
                e.preventDefault();
            }
        });
    });
</script>
@endpush
