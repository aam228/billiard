@extends('layouts.app')

@section('content')
<div class="app-container"> {{-- Main container for the page content --}}
    <div class="d-flex justify-content-between align-items-center mb-4"> {{-- Header section with Bootstrap flex utilities --}}
        <h4 class="section-title">Kelola Makanan</h4> {{-- Using custom section-title for consistent heading style --}}
        {{-- Button to trigger the Create Product modal --}}
        <button type="button" class="btn btn-primary custom-btn" data-bs-toggle="modal" data-bs-target="#createProductModal">
            <i class="bi bi-plus-lg me-1"></i> Tambah Makanan
        </button>
    </div>

    @if($produks->isEmpty())
        <div class="custom-alert">Belum ada makanan.</div> {{-- Custom alert for empty state --}}
    @else
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4"> {{-- Bootstrap grid for responsive product cards --}}
            @foreach($produks as $produk)
            <div class="col"> {{-- Column for each product card --}}
                <div class="card product-card h-100"> {{-- Bootstrap card with custom product-card styling and full height --}}
                    <div class="product-image-wrapper"> {{-- Custom wrapper for consistent image sizing --}}
                        @if($produk->gambar)
                            <img src="{{ asset('storage/' . $produk->gambar) }}" alt="{{ $produk->nama_produk }}" class="product-image"> {{-- Custom image class --}}
                        @else
                            <div class="image-placeholder"> {{-- Custom placeholder for no image --}}
                                <span class="bi bi-image"></span> {{-- Bootstrap icon for placeholder --}}
                                <p>Tidak ada gambar</p>
                            </div>
                        @endif
                    </div>

                    <div class="card-body product-info"> {{-- Bootstrap card-body with custom product-info styling --}}
                        <h6 class="card-title product-name">{{ $produk->nama_produk }}</h6> {{-- Bootstrap card-title with custom product-name styling --}}
                        <p class="card-text product-price">Rp {{ number_format($produk->harga, 0, ',', '.') }}</p> {{-- Bootstrap card-text with custom product-price styling --}}
                    </div>

                    <div class="product-actions-footer"> {{-- Custom footer for action buttons --}}
                        {{-- Button to trigger the Edit Product modal --}}
                        {{-- REMOVED onclick="window.location.href='...'" --}}
                        <button type="button"
                            class="btn btn-sm custom-edit-btn"
                            data-bs-toggle="modal"
                            data-bs-target="#editProductModal"
                            data-id="{{ $produk->id }}"
                            data-nama="{{ $produk->nama_produk }}"
                            data-harga="{{ $produk->harga }}"
                            data-gambar="{{ $produk->gambar }}">
                            <i class="bi bi-pencil-square me-1"></i> Edit
                        </button>
                        <form action="{{ route('produk.destroy', $produk->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm custom-delete-btn"> {{-- Bootstrap small button with custom delete styling --}}
                                <i class="bi bi-trash me-1"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

<!-- Create Product Modal -->
<div class="modal fade" id="createProductModal" tabindex="-1" aria-labelledby="createProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 600px;">
        <div class="modal-content">
            <div class="modal-header custom-modal-header">
                <h5 class="modal-title" id="createProductModalLabel">Tambah Makanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="createProductForm" action="{{ route('produk.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="create_nama_produk" class="form-label">Nama Makanan</label>
                        <input type="text" name="nama_produk" id="create_nama_produk" class="form-control" required>
                        @error('nama_produk')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="create_harga" class="form-label">Harga (Rp)</label>
                        <input type="text" name="harga" id="create_harga" class="form-control" required> {{-- Changed to type="text" for currency formatting --}}
                        @error('harga')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="create_gambar" class="form-label">Gambar Produk</label>
                        <input type="file" name="gambar" id="create_gambar" class="form-control">
                        @error('gambar')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-arrow-left me-1"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save2 me-1"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 600px;">
        <div class="modal-content">
            <div class="modal-header custom-modal-header">
                <h5 class="modal-title" id="editProductModalLabel">Edit Makanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="editProductForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT') {{-- This will be dynamically controlled by JS if needed --}}

                    <div class="mb-3">
                        <label for="edit_nama_produk" class="form-label">Nama Makanan</label>
                        <input type="text" name="nama_produk" id="edit_nama_produk" class="form-control" required>
                        @error('nama_produk')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="edit_harga" class="form-label">Harga (Rp)</label>
                        <input type="text" name="harga" id="edit_harga" class="form-control" required> {{-- Changed to type="text" for currency formatting --}}
                        @error('harga')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="edit_gambar" class="form-label">Gambar Produk</label>
                        <input type="file" name="gambar" id="edit_gambar" class="form-control">
                        <div id="current_gambar_preview" class="image-preview-wrapper d-none">
                            <img src="" alt="Gambar Produk" class="image-preview w-50 mt-3">
                        </div>
                        @error('gambar')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-arrow-left me-1"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save2 me-1"></i>Perbarui
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('styles')
<link rel="stylesheet" href="{{ asset('css/produk.css') }}">
@endpush

@push('scripts')
<script>
    function formatRupiah(input) {
        let value = input.value.replace(/\D/g, '');
        if (value) {
            input.value = new Intl.NumberFormat('id-ID').format(value);
        } else {
            input.value = '';
        }
    }

    function prepareCurrencyInputs() {
        const inputs = document.querySelectorAll('input[name="harga"]');

        inputs.forEach(input => {
            input.addEventListener('input', () => formatRupiah(input));

            // Bersihkan titik saat submit agar tidak error di backend
            input.closest('form').addEventListener('submit', () => {
                input.value = input.value.replace(/\./g, '');
            });
        });
    }

    document.addEventListener('DOMContentLoaded', prepareCurrencyInputs);
    document.addEventListener('DOMContentLoaded', function () {
        // Ketika tombol edit ditekan
        document.querySelectorAll('.custom-edit-btn').forEach(button => {
            button.addEventListener('click', function () {
                const id = this.dataset.id;
                const nama = this.dataset.nama;
                const harga = this.dataset.harga;
                const gambar = this.dataset.gambar;

                // Isi form edit
                document.getElementById('edit_nama_produk').value = nama;
                document.getElementById('edit_harga').value = new Intl.NumberFormat('id-ID').format(harga);

                // Preview gambar jika ada
                const preview = document.getElementById('current_gambar_preview');
                const img = preview.querySelector('img');

                if (gambar) {
                    img.src = `/storage/${gambar}`;
                    preview.classList.remove('d-none');
                } else {
                    preview.classList.add('d-none');
                    img.src = '';
                }

                // Ubah action form
                const form = document.getElementById('editProductForm');
                form.action = `/produk/${id}`;
            });
        });
    });

    document.getElementById('editProductForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const form = this;
        const formData = new FormData(form);
        const id = form.action.split('/').pop();
        formData.set('harga', formData.get('harga').replace(/\./g, ''));

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Tutup modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('editProductModal'));
                modal.hide();

                // Reload atau update UI langsung
                window.location.reload(); // bisa diganti update kartu produk langsung kalau mau
            }
        })
        .catch(err => console.error(err));
    });
</script>
@endpush
