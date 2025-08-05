@extends('layouts.app')

@section('content')
<div x-data="manageTablesPage()">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header Halaman --}}
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center border-b pb-4 mb-6 border-zinc-200 dark:border-zinc-700">
            <div>
                <h2 class="text-2xl font-bold text-zinc-800 dark:text-white">Manage Tables</h2>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Home / Manage Tables</p>
            </div>
            <button type="button" @click="isCreateModalOpen = true" class="btn-primary mt-4 sm:mt-0">
                <i class="fa-solid fa-plus mr-2"></i> Add Table
            </button>
        </div>

        {{-- Notifikasi --}}
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md flex justify-between items-start" role="alert">
                <p>{{ session('success') }}</p>
                <button @click="show = false" class="ml-4 text-xl font-semibold">&times;</button>
            </div>
        @endif
        @if ($errors->any())
            <div x-data="{ show: true }" x-show="show" x-transition class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md" role="alert">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button @click="show = false" class="absolute top-2 right-3 text-xl font-semibold">&times;</button>
            </div>
        @endif

        {{-- Grid Kartu Meja --}}
        @if($mejas->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach ($mejas as $meja)
            <div class="bg-white dark:bg-zinc-900/50 dark:ring-1 dark:ring-white/10 rounded-lg shadow-md flex flex-col">
                <div class="p-4 border-b dark:border-zinc-700 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-circle text-xs {{ $meja->status == 'digunakan' ? 'text-orange-500 animate-pulse' : 'text-green-500' }}"></i>
                        <span class="font-semibold text-zinc-800 dark:text-white">{{ $meja->nama_meja }}</span>
                    </div>
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 focus:outline-none">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div x-show="open" x-cloak @click.away="open = false" x-transition class="absolute right-0 mt-2 w-40 bg-white dark:bg-zinc-900 rounded-md shadow-lg py-1 z-20 ring-1 ring-black ring-opacity-5 dark:ring-white dark:ring-opacity-10">
                            <button @click="openEditModal({{ json_encode($meja) }}); open = false" class="w-full text-left block px-4 py-2 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800">Edit Meja</button>
                            <div class="border-t my-1 border-zinc-100 dark:border-zinc-700"></div>
                            <form action="{{ route('meja.destroy', $meja->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus meja ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/50">Hapus Meja</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="p-4 flex-grow flex flex-col">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-xs font-bold uppercase text-zinc-400 dark:text-zinc-500">Status</span>
                        @if($meja->status == 'digunakan')
                            <span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full dark:bg-yellow-900/50 dark:text-yellow-300">{{ ucfirst($meja->status) }}</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full dark:bg-green-900/50 dark:text-green-300">{{ ucfirst($meja->status) }}</span>
                        @endif
                    </div>
                    <div class="border-t dark:border-zinc-700 pt-4 mt-auto flex justify-between items-center text-sm">
                        <span class="text-zinc-500 dark:text-zinc-400 font-medium"><i class="fas fa-money-bill-wave mr-2"></i>Tarif/Jam</span>
                        <span class="font-bold text-green-600 dark:text-green-500">Rp {{ number_format($meja->tarif_per_jam, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-16 bg-white dark:bg-zinc-800 rounded-lg shadow-md">
            <i class="fas fa-table text-6xl text-zinc-300 dark:text-zinc-600 mb-4"></i>
            <h3 class="text-xl font-semibold text-zinc-700 dark:text-zinc-300">Belum Ada Meja</h3>
            <p class="text-zinc-500 dark:text-zinc-400 mt-2">Silakan tambahkan meja untuk mulai mengelola.</p>
        </div>
        @endif
    </div>

    {{-- Modal Tambah Meja --}}
    <div x-show="isCreateModalOpen" x-cloak x-transition class="fixed inset-0 bg-black bg-opacity-50 z-40 flex items-center justify-center p-4">
        <div @click.away="isCreateModalOpen = false" class="bg-white dark:bg-zinc-800 rounded-lg shadow-xl w-full max-w-md">
            <form action="{{ route('meja.store') }}" method="POST">
                @csrf
                <div class="p-4 border-b dark:border-zinc-700 flex justify-between items-center">
                    <h5 class="text-lg font-bold"><i class="fa-solid fa-plus-circle mr-2"></i>Add Table</h5>
                    <button type="button" @click="isCreateModalOpen = false" class="text-2xl" aria-label="Tutup">&times;</button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label for="nama_meja" class="block text-sm font-medium mb-1">Nama Meja</label>
                        <input type="text" name="nama_meja" id="nama_meja" class="form-input" required>
                    </div>
                    <div>
                        <label for="tarif_per_jam" class="block text-sm font-medium mb-1">Tarif per Jam (Rp)</label>
                        <input type="text" name="tarif_per_jam" class="form-input format-rupiah" required>
                    </div>
                </div>
                <div class="px-6 py-4 bg-zinc-50 dark:bg-zinc-800/50 flex justify-end gap-3 rounded-b-lg">
                    <button type="button" @click="isCreateModalOpen = false" class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
    
    {{-- Modal Edit Meja --}}
    <div x-show="isEditModalOpen" x-cloak x-transition class="fixed inset-0 bg-black bg-opacity-50 z-40 flex items-center justify-center p-4">
        <div @click.away="isEditModalOpen = false" class="bg-white dark:bg-zinc-800 rounded-lg shadow-xl w-full max-w-md">
            <form :action="editTable.id ? '/meja/' + editTable.id : '#'" method="POST">
                @csrf
                @method('PUT')
                <div class="p-4 border-b dark:border-zinc-700 flex justify-between items-center">
                    <h5 class="text-lg font-bold"><i class="fa-solid fa-pencil mr-2"></i>Edit Table</h5>
                    <button type="button" @click="isEditModalOpen = false" class="text-2xl" aria-label="Tutup">&times;</button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label for="edit_nama_meja" class="block text-sm font-medium mb-1">Nama Meja</label>
                        <input type="text" name="nama_meja" id="edit_nama_meja" class="form-input" required x-model="editTable.nama_meja">
                    </div>
                    <div>
                        <label for="edit_tarif_per_jam" class="block text-sm font-medium mb-1">Tarif per Jam (Rp)</label>
                        <input type="text" name="tarif_per_jam" id="edit_tarif_per_jam" class="form-input format-rupiah" required x-model="editTable.tarif_per_jam">
                    </div>
                </div>
                <div class="px-6 py-4 bg-zinc-50 dark:bg-zinc-800/50 flex justify-end gap-3 rounded-b-lg">
                    <button type="button" @click="isEditModalOpen = false" class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function manageTablesPage() {
    return {
        isCreateModalOpen: false,
        isEditModalOpen: false,
        editTable: {},

        openEditModal(meja) {
            this.editTable = {
                id: meja.id,
                nama_meja: meja.nama_meja,
                tarif_per_jam: new Intl.NumberFormat('id-ID').format(meja.tarif_per_jam)
            };
            this.isEditModalOpen = true;
        }
    };
}

// Skrip format Rupiah
document.addEventListener('DOMContentLoaded', function () {
    const setupRupiahFormatting = (container) => {
        container.querySelectorAll('.format-rupiah').forEach(input => {
            const new_input = input.cloneNode(true);
            input.parentNode.replaceChild(new_input, input);

            new_input.addEventListener('input', function () {
                let value = this.value.replace(/\D/g, '');
                this.value = value ? new Intl.NumberFormat('id-ID').format(value) : '';
            });
            new_input.closest('form').addEventListener('submit', function () {
                new_input.value = new_input.value.replace(/\./g, '');
            });
        });
    };
    setupRupiahFormatting(document);
});
</script>
@endpush