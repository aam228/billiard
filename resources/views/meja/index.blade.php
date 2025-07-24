@extends('layouts.app')

@section('head')
    {{-- <link rel="stylesheet" href="{{ asset('css/meja.css') }}"> --}}
@endsection

@section('content')
<div class="container-fluid py-3"> {{-- Menggunakan container-fluid untuk layout yang lebih luas --}}
    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
        <div>
            <h2 class="fw-semibold text-dark mb-1">Manage Tables</h2> {{-- Menggunakan kelas dari dashboard --}}
            <small class="text-muted">Home / Manage Tables</small> {{-- Breadcrumb sederhana --}}
        </div>
        <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="bi bi-plus-circle me-1"></i> Add Table
        </button>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($mejas->count() > 0)
    <div class="row g-3">
        @foreach ($mejas as $meja)
        <div class="col-12 col-md-6 col-lg-4 col-xl-3"> {{-- Kolom responsif --}}
            <div class="card border shadow-sm h-100"> {{-- Card standar dari dashboard --}}
                <div class="card-header bg-light border-bottom d-flex justify-content-between align-items-center py-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-circle text-primary" style="font-size: 1.2rem;"></i> {{-- Icon dan warna dari tema --}}
                        <span class="fw-semibold text-dark" style="font-size: 0.875rem;">{{ $meja->nama_meja }}</span>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary border-0" type="button" 
                                data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border">
                            <li><button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editModal{{ $meja->id }}">
                                <small>Edit Meja</small></button></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('meja.destroy', $meja->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this table?')">
                                        <small>Delete Meja</small>
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <strong class="text-muted" style="font-size: 0.75rem;">STATUS</strong>
                            <div class="mt-1">
                                @if($meja->status == 'digunakan')
                                    <span class="badge rounded-pill px-2 py-1" 
                                          style="background-color: var(--bs-warning); color: var(--bs-body-bg); font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">
                                        {{ ucfirst($meja->status) }}
                                    </span>
                                @else
                                    <span class="badge rounded-pill px-2 py-1" 
                                          style="background-color: var(--bs-success); color: var(--bs-body-bg); font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">
                                        {{ ucfirst($meja->status) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="border-top pt-3">
                        <div class="d-flex justify-content-between align-items-center py-2" style="font-size: 0.8125rem;">
                            <div class="d-flex align-items-center gap-2 text-muted fw-medium">
                                <i class="fas fa-money-bill-wave"></i>
                                <span>Tarif/Jam</span>
                            </div>
                            <div class="fw-bold text-default"> {{-- Menggunakan text-default untuk warna teks --}}
                                Rp {{ number_format($meja->tarif_per_jam, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="row">
        <div class="col-12">
            <div class="text-center py-5 card border shadow-sm"> {{-- Tambahkan card untuk konsistensi --}}
                <i class="fas fa-table text-muted mb-3" style="font-size: 2.5rem; opacity: 0.5;"></i>
                <h3 class="fw-medium text-secondary mb-2" style="font-size: 1.125rem;">Belum Ada Meja</h3>
                <p class="text-muted mb-3" style="font-size: 0.875rem;">
                    Silakan tambahkan meja untuk mulai mengelola bisnis biliar Anda.
                </p>
            </div>
        </div>
    </div>
    @endif
</div>

<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('meja.store') }}" method="POST">
        @csrf
        <div class="modal-header bg-light border-bottom"> {{-- Menggunakan bg-light dari tema --}}
          <h5 class="modal-title" id="createModalLabel"><i class="bi bi-plus-circle me-2"></i> Add Table</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        
        <div class="modal-body">
          <div class="mb-3">
            <label for="nama_meja" class="form-label"><i class="bi bi-table me-1"></i> Nama Meja</label>
            <input type="text" name="nama_meja" id="nama_meja" class="form-control" required>
          </div>

          <div class="mb-3">
            <label for="tarif_per_jam" class="form-label"><i class="bi bi-cash-coin me-1"></i> Tarif per Jam (Rp)</label>
            <input type="text" name="tarif_per_jam" id="tarif_per_jam" class="form-control format-rupiah" required>
          </div>
        </div>
        
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="bi bi-x-circle me-1"></i> Cancel
          </button>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1"></i> Save
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Karena modal edit membutuhkan data meja spesifik, cara paling umum adalah:
     1. Menjaga modal edit di dalam loop @foreach (jika jumlah meja tidak terlalu banyak)
     2. Menggunakan AJAX untuk mengisi data modal universal saat tombol edit diklik.
     Saya akan pertahankan modal di dalam loop forEach seperti kode asli Anda karena itu paling sederhana.
     Modal di dalam loop @foreach harus ada di sini, di bawah, bukan di luar.
--}}
@foreach ($mejas as $meja)
    <div class="modal fade" id="editModal{{ $meja->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $meja->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('meja.update', $meja->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-light border-bottom">
                        <h5 class="modal-title" id="editModalLabel{{ $meja->id }}">
                            <i class="bi bi-pencil-square me-2"></i> Edit Table
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama_meja_{{ $meja->id }}" class="form-label">
                                <i class="bi bi-table me-1"></i> Nama Meja
                            </label>
                            <input type="text" name="nama_meja" id="nama_meja_{{ $meja->id }}" class="form-control" value="{{ $meja->nama_meja }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="tarif_per_jam_{{ $meja->id }}" class="form-label">
                                <i class="bi bi-cash-coin me-1"></i> Tarif per Jam (Rp)
                            </label>
                            <input type="text" name="tarif_per_jam" id="tarif_per_jam_{{ $meja->id }}" class="form-control format-rupiah" value="{{ number_format($meja->tarif_per_jam, 0, '', '') }}" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-arrow-repeat me-1"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.format-rupiah').forEach(input => {
            input.addEventListener('input', function () {
                let value = this.value.replace(/\D/g, '');
                this.value = new Intl.NumberFormat('id-ID').format(value);
            });

            input.closest('form').addEventListener('submit', function () {
                input.value = input.value.replace(/\./g, '');
            });
        });
    });
</script>
@endpush