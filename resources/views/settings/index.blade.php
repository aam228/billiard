@extends('layouts.app') {{-- Menggunakan layout utama Anda --}}

@section('head')
    {{-- Anda bisa menambahkan CSS spesifik untuk halaman pengaturan di sini --}}
    {{-- <link rel="stylesheet" href="{{ asset('css/settings.css') }}"> --}}
@endsection

@section('content')
<div class="container py-4">
    <h1 class="retro-title mb-4">Pengaturan Akun</h1>

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

    <div class="row">
        <div class="col-md-8">
            {{-- Bagian Edit Profil --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-light fw-bold">
                    <i class="bi bi-person-circle me-2"></i>Edit Profil
                </div>
                <div class="card-body">
                    <form action="{{ route('settings.updateProfile') }}" method="POST">
                        @csrf
                        @method('patch')

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </form>
                </div>
            </div>

            {{-- Bagian Ubah Kata Sandi --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-light fw-bold">
                    <i class="bi bi-key-fill me-2"></i>Ubah Kata Sandi
                </div>
                <div class="card-body">
                    <form action="{{ route('settings.updatePassword') }}" method="POST">
                        @csrf
                        @method('patch')

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Kata Sandi Saat Ini</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Kata Sandi Baru</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi Baru</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Ubah Kata Sandi</button>
                    </form>
                </div>
            </div>

            {{-- Bagian Pengaturan Tema --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-light fw-bold">
                    <i class="bi bi-palette-fill me-2"></i>Pengaturan Tampilan
                </div>
                <div class="card-body">
                    <form action="{{ route('settings.updateTheme') }}" method="POST">
                        @csrf
                        @method('patch')

                        <div class="mb-3">
                            <label for="theme" class="form-label">Pilih Tema</label>
                            <select class="form-select" id="theme" name="theme">
                                <option value="light" {{ $user->theme == 'light' ? 'selected' : '' }}>Terang</option>
                                <option value="dark" {{ $user->theme == 'dark' ? 'selected' : '' }}>Gelap</option>
                                <option value="system" {{ $user->theme == 'system' ? 'selected' : '' }}>Sesuai Sistem</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan Tema</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const themeSelect = document.getElementById('theme');
            if (themeSelect) {
                // Fungsi untuk menerapkan tema
                const applyTheme = (theme) => {
                    document.documentElement.setAttribute('data-theme', theme); // Menggunakan data-theme attribute
                    // Atau, jika Anda lebih suka, Anda bisa menambahkan/menghapus kelas di body
                    // document.body.classList.remove('theme-light', 'theme-dark', 'theme-system');
                    // document.body.classList.add('theme-' + theme);
                };

                // Terapkan tema saat halaman dimuat (dari nilai di database)
                const initialTheme = themeSelect.value;
                applyTheme(initialTheme);

                // Perbarui tema secara dinamis saat pilihan berubah
                themeSelect.addEventListener('change', function() {
                    const selectedTheme = this.value;
                    applyTheme(selectedTheme);
                    console.log('Tema diubah menjadi:', selectedTheme);
                });
            }
        });
    </script>
@endpush