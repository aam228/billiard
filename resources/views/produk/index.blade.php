@extends('layouts.app')

@section('content')
<div x-data="productPage()" class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6 border-b pb-4 border-gray-200 dark:border-gray-700">
        <h4 class="text-2xl font-bold text-gray-800 dark:text-white">Kelola Makanan</h4>
        <button type="button" @click="isCreateModalOpen = true" class="btn-primary">
            <i class="fa-solid fa-plus mr-2"></i>
            <span>Tambah Makanan</span>
        </button>
    </div>

    @if($produks->isEmpty())
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-md" role="alert">
            <p>Belum ada makanan yang ditambahkan.</p>
        </div>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
            @foreach($produks as $produk)
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow-md overflow-hidden flex flex-col group">
                <div class="aspect-square w-full overflow-hidden">
                    @if($produk->gambar)
                        <img src="{{ asset('storage/' . $produk->gambar) }}" alt="{{ $produk->nama_produk }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                    @else
                        <div class="flex flex-col items-center justify-center h-full bg-gray-100 dark:bg-gray-700 text-gray-400">
                            <i class="fa-solid fa-image text-4xl"></i>
                        </div>
                    @endif
                </div>
                <div class="p-3 mt-auto flex justify-between items-start">
                    <div>
                        <h6 class="font-semibold text-sm text-gray-800 dark:text-white truncate" title="{{ $produk->nama_produk }}">{{ $produk->nama_produk }}</h6>
                        <p class="text-sm font-semibold text-green-500 dark:text-green-400">Rp {{ number_format($produk->harga, 0, ',', '.') }}</p>
                    </div>
                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <button type="button" @click="openEditModal({{ json_encode($produk) }})" 
                                class="w-7 h-7 flex items-center justify-center text-xs text-white bg-yellow-500 rounded-full hover:bg-yellow-600">
                            <i class="fa-solid fa-pencil"></i>
                        </button>
                        <form action="{{ route('produk.destroy', $produk->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-7 h-7 flex items-center justify-center text-xs text-white bg-red-600 rounded-full hover:bg-red-700">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif

    {{-- Modal Tambah --}}
    <div x-show="isCreateModalOpen" x-cloak x-transition class="fixed inset-0 bg-black bg-opacity-50 z-40 flex items-center justify-center p-4">
        <div @click.away="isCreateModalOpen = false" class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-lg">
            <div class="p-4 border-b dark:border-gray-700 flex justify-between items-center">
                <h5 class="text-lg font-bold">Tambah Makanan</h5>
                <button @click="isCreateModalOpen = false" class="text-2xl">&times;</button>
            </div>
            <div class="p-6">
                <form action="{{ route('produk.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="create_nama_produk" class="block text-sm font-medium mb-1">Nama Makanan</label>
                            <input type="text" name="nama_produk" id="create_nama_produk" class="w-full form-input" required>
                        </div>
                        <div>
                            <label for="create_harga" class="block text-sm font-medium mb-1">Harga (Rp)</label>
                            {{-- Pakai Alpine Mask --}}
                            <input type="text" name="harga" id="create_harga" 
                                   class="w-full form-input format-rupiah" required 
                                   x-mask:dynamic="$money($input, ',', '.', 0)">
                        </div>
                        <div>
                            <label for="create_gambar" class="block text-sm font-medium mb-1">Gambar Produk</label>
                            <input type="file" name="gambar" id="create_gambar" class="w-full file-input">
                        </div>
                        <div class="flex justify-end gap-3 pt-4">
                            <button type="button" @click="isCreateModalOpen = false" class="btn-secondary">Batal</button>
                            <button type="submit" class="btn-primary">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    {{-- Modal Edit --}}
    <div x-show="isEditModalOpen" x-cloak x-transition class="fixed inset-0 bg-black bg-opacity-50 z-40 flex items-center justify-center p-4">
        <div @click.away="isEditModalOpen = false" class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-lg">
            <div class="p-4 border-b dark:border-gray-700 flex justify-between items-center">
                <h5 class="text-lg font-bold">Edit Makanan</h5>
                <button @click="isEditModalOpen = false" class="text-2xl">&times;</button>
            </div>
            <div class="p-6">
                <form @submit.prevent="submitEditForm" enctype="multipart/form-data" id="editProductForm">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label for="edit_nama_produk" class="block text-sm font-medium mb-1">Nama Makanan</label>
                            <input type="text" name="nama_produk" id="edit_nama_produk" class="w-full form-input" required x-model="editProduct.nama_produk">
                        </div>
                        <div>
                            <label for="edit_harga" class="block text-sm font-medium mb-1">Harga (Rp)</label>
                            {{-- Pakai Alpine Mask --}}
                            <input type="text" name="harga" id="edit_harga" 
                                   class="w-full form-input format-rupiah" required 
                                   x-model="editProduct.harga" 
                                   x-mask:dynamic="$money($input, ',', '.', 0)">
                        </div>
                        <div>
                            <label for="edit_gambar" class="block text-sm font-medium mb-1">Gambar Baru (Opsional)</label>
                            <input type="file" name="gambar" id="edit_gambar" class="w-full file-input">
                            <div x-show="editProduct.gambar" class="mt-4">
                                <p class="text-sm mb-2">Gambar saat ini:</p>
                                <img :src="'/storage/' + editProduct.gambar" class="w-32 h-32 object-cover rounded-md">
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 pt-4">
                            <button type="button" @click="isEditModalOpen = false" class="btn-secondary">Batal</button>
                            <button type="submit" class="btn-primary">Perbarui</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function productPage() {
    return {
        isCreateModalOpen: false,
        isEditModalOpen: false,
        editProduct: { id: null, nama_produk: '', harga: '', gambar: null },

        openEditModal(product) {
            this.editProduct = { ...product };
            this.isEditModalOpen = true;
        },

        submitEditForm(event) {
            const form = event.target;
            const formData = new FormData(form);
            const url = `/produk/${this.editProduct.id}`;

            // Bersihkan titik sebelum submit
            formData.set('harga', this.editProduct.harga.toString().replace(/\./g, ''));

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.isEditModalOpen = false;
                    window.location.reload();
                }
            })
            .catch(() => alert('Gagal memperbarui produk.'));
        }
    };
}

document.addEventListener('DOMContentLoaded', function () {
    const inputs = document.querySelectorAll('.format-rupiah');
    inputs.forEach(input => {
        input.addEventListener('input', function () {
            let value = this.value.replace(/\D/g, '');
            this.value = value ? new Intl.NumberFormat('id-ID').format(value) : '';
        });
        input.closest('form').addEventListener('submit', function () {
            input.value = input.value.replace(/\./g, '');
        });
    });
});
</script>
@endpush


@push('styles')
<style>
    .form-input {
        @apply block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white;
    }
    .file-input {
        @apply block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400;
    }
    .btn-primary {
        @apply inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 transition;
    }
    .btn-secondary {
        @apply inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 text-gray-700 dark:text-gray-200 rounded-lg shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition;
    }
</style>
@endpush
