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
                            {{-- MENGURANGI PADDING (px-6 -> px-4) --}}
                            <th scope="col" class="px-4 py-3 w-10">No</th> 
                            <th scope="col" class="px-4 py-3 whitespace-nowrap">Meja</th>
                            <th scope="col" class="px-4 py-3">Nama Pelanggan</th> {{-- Hapus whitespace-nowrap --}}
                            <th scope="col" class="px-4 py-3 whitespace-nowrap">Durasi</th>
                            <th scope="col" class="px-4 py-3 whitespace-nowrap">Total Harga</th>
                            {{-- MENGGANTI FORMAT TANGGAL PADA HEADER --}}
                            <th scope="col" class="px-3 py-3 whitespace-nowrap">W. Mulai</th> 
                            <th scope="col" class="px-3 py-3 whitespace-nowrap">W. Selesai</th>
                            <th scope="col" class="px-4 py-3 w-12">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transaksis as $index => $transaksi)
                        <tr id="transaksi-{{ $transaksi->id }}" {{-- <- Tambahkan ID di sini --}}
                            class="border-b dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            {{-- MENGURANGI PADDING DI ISI TABEL --}}
                            <td class="px-4 py-4 text-zinc-900 dark:text-white">{{ $transaksis->firstItem() + $index }}</td>
                            <td class="px-4 py-4 text-zinc-900 dark:text-white whitespace-nowrap">{{ $transaksi->meja->nama_meja ?? 'N/A' }}</td>
                            {{-- MEMBIARKAN NAMA PELANGGAN MEMBUNGKUS JIKA PERLU --}}
                            <td class="px-4 py-4 font-medium text-zinc-900 dark:text-white">{{ $transaksi->nama_pelanggan }}</td>
                            <td class="px-4 py-4 text-zinc-900 dark:text-white whitespace-nowrap">{{ $transaksi->durasi }} Jam</td>
                            <td class="px-4 py-4 font-semibold text-green-600 dark:text-green-500 whitespace-nowrap">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                            
                            {{-- MENGGANTI FORMAT TANGGAL MENJADI LEBIH PENDEK (H:i) --}}
                            <td class="px-3 py-4 text-zinc-900 dark:text-white whitespace-nowrap">{{ optional($transaksi->waktu_mulai)->format('d/m H:i') }}</td>
                            <td class="px-3 py-4 text-zinc-900 dark:text-white whitespace-nowrap">{{ optional($transaksi->waktu_selesai)->format('d/m H:i') }}</td>
                            
                            <td class="px-4 py-4 whitespace-nowrap">
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

            {{-- Pagination + Tombol Laporan --}}
            <div class="mt-6 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div class="w-full sm:w-auto flex justify-center">
                    {{ $transaksis->onEachSide(1)->links('vendor.pagination.tailwind-custom') }}
                </div>

                <a href="{{ route('transaksi.laporan') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-medium transition shadow-sm">
                    <i class="fa-solid fa-chart-line"></i>
                    Laporan Transaksi
                </a>
            </div>
        </div>
    </div>

    {{-- Modal Konfirmasi Hapus --}}
    {{-- ... (Modal dan Script tidak berubah karena tidak mempengaruhi masalah lebar tabel) ... --}}
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
                {{-- Ganti form submit dengan button yang memanggil fungsi AJAX deleteTransaction() --}}
                <button 
                    type="button" 
                    @click="deleteTransaction(deleteInfo.id)" {{-- Panggil fungsi AJAX baru, kirim ID transaksi --}}
                    class="bg-red-600 text-white inline-flex items-center gap-2 px-4 py-2 rounded-lg shadow-md hover:bg-red-700 transition"
                >
                    Hapus
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function historyPage() {
    // Pastikan Anda memiliki meta tag CSRF token di <head> layout Anda
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    return {
        isConfirmModalOpen: false,
        deleteUrl: '',
        deleteInfo: {},
        
        openConfirmModal(transaction) {
            this.deleteInfo = transaction;
            // Kita tidak lagi butuh deleteUrl, tapi kita simpan ID-nya
            this.isConfirmModalOpen = true;
        },

        async deleteTransaction(id) {
            this.isConfirmModalOpen = false; // Tutup modal segera
            const url = `/transaksi/${id}`; 

            try {
                const response = await fetch(url, {
                    method: 'DELETE', // Menggunakan method DELETE
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                    },
                });

                if (response.ok) {
                    // Berhasil dihapus di backend, sekarang hapus baris di frontend
                    const row = document.getElementById(`transaksi-${id}`);
                    if (row) {
                        row.remove(); // Hapus elemen baris dari DOM
                        
                        // Opsional: Logika untuk menangani jika tabel kosong
                        const tbody = row.closest('tbody');
                        if (tbody && tbody.children.length === 0) {
                            location.reload(); 
                        }
                    }
                    alert('✅ Transaksi berhasil dihapus!'); 

                } else {
                    alert('❌ Gagal menghapus transaksi. Silakan coba lagi.');
                    console.error('Delete failed:', response);
                }

            } catch (error) {
                alert('❌ Terjadi kesalahan jaringan.');
                console.error('Network error:', error);
            }
        },
    }
}
</script>
@endpush