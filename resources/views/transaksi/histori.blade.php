@extends('layouts.app')

@section('content')
<div x-data="historyPage()">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h4 class="text-2xl font-bold text-zinc-800 dark:text-white border-b pb-4 mb-6 border-zinc-200 dark:border-zinc-700">
            Histori Transaksi
        </h4>

        <div class="bg-white dark:bg-zinc-900/50 dark:ring-1 dark:ring-white/10 rounded-lg shadow-md p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-zinc-500 dark:text-zinc-400">
                    <thead class="text-xs text-zinc-700 uppercase bg-zinc-50 dark:bg-zinc-800 dark:text-zinc-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">No</th>
                            <th scope="col" class="px-6 py-3">Meja</th>
                            <th scope="col" class="px-6 py-3">Nama Pelanggan</th>
                            <th scope="col" class="px-6 py-3">Durasi</th>
                            <th scope="col" class="px-6 py-3">Total Harga</th>
                            <th scope="col" class="px-6 py-3">Waktu Mulai</th>
                            <th scope="col" class="px-6 py-3">Waktu Selesai</th>
                            <th scope="col" class="px-6 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transaksis as $index => $transaksi)
                        <tr class="border-b dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="px-6 py-4 text-zinc-900 dark:text-white">{{ $transaksis->firstItem() + $index }}</td>
                            <td class="px-6 py-4 text-zinc-900 dark:text-white">{{ $transaksi->meja->nama_meja ?? 'N/A' }}</td>
                            <td class="px-6 py-4 font-medium text-zinc-900 dark:text-white whitespace-nowrap">{{ $transaksi->nama_pelanggan }}</td>
                            <td class="px-6 py-4 text-zinc-900 dark:text-white">{{ $transaksi->durasi }} Jam</td>
                            <td class="px-6 py-4 font-semibold text-green-600 dark:text-green-500">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-zinc-900 dark:text-white">{{ optional($transaksi->waktu_mulai)->format('d/m/y H:i') }}</td>
                            <td class="px-6 py-4 text-zinc-900 dark:text-white">{{ optional($transaksi->waktu_selesai)->format('d/m/y H:i') }}</td>
                            <td class="px-6 py-4">
                                <button type="button" @click="openConfirmModal({{ json_encode($transaksi) }})" class="bg-red-600 hover:bg-red-700 text-white text-xs font-semibold px-3 py-1 rounded-md transition">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-zinc-500">
                                Tidak ada data histori transaksi.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                {{ $transaksis->links() }}
                <a href="{{ route('transaksi.laporan') }}" class="btn-secondary">
                    <i class="fa-solid fa-chart-line mr-2"></i>
                    Laporan Transaksi
                </a>
            </div>
        </div>
    </div>

    {{-- Modal Konfirmasi Hapus --}}
    <div x-show="isConfirmModalOpen" x-cloak x-transition class="fixed inset-0 bg-black bg-opacity-60 z-40 flex items-center justify-center p-4">
        <div @click.away="isConfirmModalOpen = false" class="bg-white dark:bg-zinc-800 rounded-lg shadow-xl w-full max-w-md">
            <div class="p-4 border-b dark:border-zinc-700 flex justify-between items-center">
                <h5 class="text-lg font-bold text-zinc-900 dark:text-white">Konfirmasi Hapus</h5>
                <button @click="isConfirmModalOpen = false" class="text-2xl text-zinc-400 hover:text-white">&times;</button>
            </div>
            <div class="p-6">
                <p class="mb-4 text-zinc-800 dark:text-zinc-300">Anda yakin ingin menghapus transaksi ini?</p>
                <ul class="text-sm space-y-1 bg-zinc-100 dark:bg-zinc-700/50 p-3 rounded-md text-zinc-900 dark:text-zinc-300">
                    <li><strong class="font-semibold">Meja:</strong> <span x-text="deleteInfo.meja ? deleteInfo.meja.nama_meja : 'N/A'"></span></li>
                    <li><strong class="font-semibold">Pelanggan:</strong> <span x-text="deleteInfo.nama_pelanggan"></span></li>
                    <li><strong class="font-semibold">Total:</strong> <span x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(deleteInfo.total_harga)"></span></li>
                </ul>
            </div>
            <div class="px-6 py-4 bg-zinc-50 dark:bg-zinc-800/80 flex justify-end gap-3 rounded-b-lg">
                <button type="button" @click="isConfirmModalOpen = false" class="btn-secondary">Batal</button>
                <form :action="deleteUrl" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 text-white inline-flex items-center gap-2 px-4 py-2 rounded-lg shadow-md hover:bg-red-700 transition">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function historyPage() {
    return {
        isConfirmModalOpen: false,
        deleteUrl: '',
        deleteInfo: {},
        openConfirmModal(transaction) {
            this.deleteInfo = transaction;
            this.deleteUrl = `/transaksi/${transaction.id}`;
            this.isConfirmModalOpen = true;
        }
    }
}
</script>
@endpush