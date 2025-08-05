@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h4 class="text-2xl font-bold text-zinc-800 dark:text-white border-b pb-4 mb-6 border-zinc-200 dark:border-zinc-700">
        Laporan Keuangan
    </h4>

    {{-- Filter Card --}}
    <div class="bg-white dark:bg-zinc-900/50 dark:ring-1 dark:ring-white/10 rounded-lg shadow-md p-6 mb-6">
        <form method="GET" action="{{ route('transaksi.laporan') }}">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <label for="tanggal_mulai" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-input" value="{{ request('tanggal_mulai') }}">
                </div>
                <div>
                    <label for="tanggal_selesai" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-input" value="{{ request('tanggal_selesai') }}">
                </div>
                <div>
                    <label for="nama_pelanggan" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Nama Pelanggan</label>
                    <input type="text" name="nama_pelanggan" id="nama_pelanggan" class="form-input" placeholder="Cari nama..." value="{{ request('nama_pelanggan') }}">
                </div>
                <div>
                    <label for="meja_id" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Meja</label>
                    <select name="meja_id" id="meja_id" class="form-input">
                        <option value="">Semua Meja</option>
                        @foreach($mejas as $meja)
                            <option value="{{ $meja->id }}" {{ request('meja_id') == $meja->id ? 'selected' : '' }}>
                                {{ $meja->nama_meja ?? 'Meja #' . $meja->id }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mt-6 flex items-center justify-end gap-3">
                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-filter mr-2"></i>Filter
                </button>
                <a href="{{ route('transaksi.cetak', request()->query()) }}" class="btn-secondary" target="_blank">
                    <i class="fa-solid fa-print mr-2"></i>Cetak PDF
                </a>
            </div>
        </form>
    </div>

    {{-- Laporan Card --}}
    <div class="bg-white dark:bg-zinc-900/50 dark:ring-1 dark:ring-white/10 rounded-lg shadow-md p-6">
        <div class="mb-4">
            <h5 class="text-lg font-semibold text-zinc-800 dark:text-white">Total Pendapatan: 
                <span class="text-green-600 dark:text-green-500 font-bold">
                    Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                </span>
            </h5>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-zinc-500 dark:text-zinc-400">
                <thead class="text-xs text-zinc-700 uppercase bg-zinc-50 dark:bg-zinc-800 dark:text-zinc-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">Nama Pelanggan</th>
                        <th scope="col" class="px-6 py-3">Meja</th>
                        <th scope="col" class="px-6 py-3">Total Harga</th>
                        <th scope="col" class="px-6 py-3">Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksis as $transaksi)
                    <tr class="border-b dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                        <td class="px-6 py-4 font-medium text-zinc-900 dark:text-white whitespace-nowrap">{{ $transaksi->nama_pelanggan }}</td>
                        <td class="px-6 py-4 text-zinc-900 dark:text-white">{{ $transaksi->meja->nama_meja ?? 'N/A' }}</td>
                        <td class="px-6 py-4 font-semibold text-green-600 dark:text-green-500">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-zinc-900 dark:text-white">{{ optional($transaksi->created_at)->format('d/m/Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-8 text-zinc-500">
                            Tidak ada data transaksi yang sesuai dengan filter.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection