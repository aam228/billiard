@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 py-8">
    {{-- Komponen Modal Notifikasi --}}
    <div x-data="{ show: false, type: '', message: '', errors: [] }"
         x-init="
            @if (session('success'))
                show = true; type = 'success'; message = `{{ session('success') }}`;
            @elseif ($errors->any())
                show = true; type = 'error'; errors = {{ json_encode($errors->all()) }};
            @endif
         "
         x-show="show" x-cloak x-transition class="fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4">
        <div @click.away="show = false" x-show="show" x-transition class="relative w-full max-w-md bg-white dark:bg-zinc-800 rounded-2xl shadow-xl p-6">
            <div class="w-16 h-16 rounded-full mx-auto flex items-center justify-center" :class="{ 'bg-green-100 dark:bg-green-900/50': type === 'success', 'bg-red-100 dark:bg-red-900/50': type === 'error' }">
                <i class="text-4xl" :class="{ 'fa-solid fa-check text-green-600': type === 'success', 'fa-solid fa-times text-red-600': type === 'error' }"></i>
            </div>
            <h3 class="text-2xl font-bold mt-4 text-center text-zinc-800 dark:text-white" x-text="type === 'success' ? 'Berhasil!' : 'Terjadi Kesalahan'"></h3>
            <div class="text-zinc-600 dark:text-zinc-300 mt-2">
                <template x-if="type === 'success'"><p class="text-center" x-text="message"></p></template>
                <template x-if="type === 'error'"><ul class="list-disc list-inside space-y-1 text-left"><template x-for="error in errors"><li x-text="error"></li></template></ul></template>
            </div>
            <button @click="show = false" class="mt-6 w-full px-4 py-2 rounded-lg text-white font-semibold" :class="{ 'bg-green-600 hover:bg-green-700': type === 'success', 'bg-red-600 hover:bg-red-700': type === 'error' }">Tutup</button>
        </div>
    </div>

    <h1 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white mb-8">
        Pengaturan Akun
    </h1>

    <div class="space-y-8">
        {{-- Bagian Upload Foto Profil --}}
        <div class="bg-white dark:bg-zinc-900/50 dark:ring-1 dark:ring-white/10 rounded-lg shadow-md">
            <div class="p-4 border-b dark:border-zinc-700 flex items-center gap-3">
                <i class="fa-solid fa-image text-zinc-600 dark:text-zinc-300"></i>
                <h3 class="font-semibold text-zinc-800 dark:text-white">Foto Profil</h3>
            </div>
            <div class="p-6 text-center">
                <img src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('images/default_profile.png') }}"
                     alt="Foto Profil" class="w-32 h-32 rounded-full object-cover mx-auto mb-4 ring-2 ring-offset-4 ring-offset-white dark:ring-offset-zinc-800 ring-green-500 profile-image-preview">
                <form action="{{ route('settings.updateProfileImage') }}" method="POST" enctype="multipart/form-data" class="max-w-sm mx-auto">
                    @csrf
                    @method('patch')
                    <div class="mb-4">
                        <label for="profile_image" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Unggah Foto Baru</label>
                        <input type="file" id="profile_image" name="profile_image" accept="image/*" class="file-input">
                        @error('profile_image')
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="btn-primary bg-green-600 hover:bg-green-700 focus:ring-green-500">Unggah Foto</button>
                </form>
            </div>
        </div>

        {{-- Bagian Edit Profil --}}
        <div class="bg-white dark:bg-zinc-900/50 dark:ring-1 dark:ring-white/10 rounded-lg shadow-md">
            <div class="p-4 border-b dark:border-zinc-700 flex items-center gap-3">
                <i class="fa-solid fa-user-circle text-zinc-600 dark:text-zinc-300"></i>
                <h3 class="font-semibold text-zinc-800 dark:text-white">Edit Profil</h3>
            </div>
            <form action="{{ route('settings.updateProfile') }}" method="POST">
                @csrf
                @method('patch')
                <div class="p-6 space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Nama</label>
                        <input type="text" class="form-input" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Email</label>
                        <input type="email" class="form-input" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                    </div>
                </div>
                <div class="px-6 py-4 bg-zinc-50 dark:bg-zinc-900/80 flex justify-end rounded-b-lg">
                    <button type="submit" class="btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>

        {{-- Bagian Ubah Kata Sandi --}}
        <div class="bg-white dark:bg-zinc-900/50 dark:ring-1 dark:ring-white/10 rounded-lg shadow-md">
            <div class="p-4 border-b dark:border-zinc-700 flex items-center gap-3">
                <i class="fa-solid fa-key text-zinc-600 dark:text-zinc-300"></i>
                <h3 class="font-semibold text-zinc-800 dark:text-white">Ubah Kata Sandi</h3>
            </div>
            <form action="{{ route('settings.updatePassword') }}" method="POST">
                @csrf
                @method('patch')
                <div class="p-6 space-y-4">
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Kata Sandi Saat Ini</label>
                        <input type="password" class="form-input" id="current_password" name="current_password" required>
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Kata Sandi Baru</label>
                        <input type="password" class="form-input" id="password" name="password" required>
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" class="form-input" id="password_confirmation" name="password_confirmation" required>
                    </div>
                </div>
                <div class="px-6 py-4 bg-zinc-50 dark:bg-zinc-900/80 flex justify-end rounded-b-lg">
                    <button type="submit" class="btn-primary">Ubah Kata Sandi</button>
                </div>
            </form>
        </div>

        {{-- Bagian Pengaturan Tema --}}
        <div class="bg-white dark:bg-zinc-900/50 dark:ring-1 dark:ring-white/10 rounded-lg shadow-md">
            <div class="p-4 border-b dark:border-zinc-700 flex items-center gap-3">
                <i class="fa-solid fa-palette text-zinc-600 dark:text-zinc-300"></i>
                <h3 class="font-semibold text-zinc-800 dark:text-white">Pengaturan Tampilan</h3>
            </div>
            <form action="{{ route('settings.updateTheme') }}" method="POST">
                @csrf
                @method('patch')
                <div class="p-6">
                    <label for="theme" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Pilih Tema</label>
                    <select class="form-input" id="theme" name="theme">
                        <option value="light" {{ $user->theme == 'light' ? 'selected' : '' }}>Terang</option>
                        <option value="dark" {{ $user->theme == 'dark' ? 'selected' : '' }}>Gelap</option>
                    </select>
                </div>
                <div class="px-6 py-4 bg-zinc-50 dark:bg-zinc-900/80 flex justify-end rounded-b-lg">
                    <button type="submit" class="btn-primary">Simpan Tema</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="cropperModal" class="fixed inset-0 bg-black bg-opacity-70 z-50 flex items-center justify-center hidden min-h-screen">
    <div class="bg-white dark:bg-zinc-800 p-6 rounded-lg shadow-lg max-w-lg w-full">
        <div>
            <img id="imageToCrop" class="max-h-[400px] mx-auto" src="" alt="To Crop">
        </div>
        <div class="mt-4 flex justify-end gap-3">
            <button id="cancelCrop" class="btn-secondary">Batal</button>
            <button id="cropAndUpload" class="btn-primary bg-green-600 hover:bg-green-700 focus:ring-green-500">Crop & Upload</button>
        </div>
    </div>
</div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" />
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Skrip Tema
    const themeSelect = document.getElementById('theme');
    if (themeSelect) {
        themeSelect.addEventListener('change', function () {
            const selectedTheme = this.value;
            localStorage.setItem('theme', selectedTheme);
            if (selectedTheme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        });
    }

    // Skrip Cropper
    const fileInput = document.getElementById('profile_image');
    const cropperModal = document.getElementById('cropperModal');
    const imageToCrop = document.getElementById('imageToCrop');
    const cancelCrop = document.getElementById('cancelCrop');
    const cropAndUpload = document.getElementById('cropAndUpload');
    let cropper;

    fileInput.addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (file && /^image\//.test(file.type)) {
            const reader = new FileReader();
            reader.onload = function (event) {
                imageToCrop.src = event.target.result;
                cropperModal.classList.remove('hidden');
                cropper = new Cropper(imageToCrop, {
                    aspectRatio: 1,
                    viewMode: 1,
                    dragMode: 'move',
                    background: false,
                    autoCropArea: 1,
                    guides: false,
                    ready() {
                        const cropBox = document.querySelector('.cropper-crop-box');
                        const viewBox = document.querySelector('.cropper-view-box');
                        if (cropBox) cropBox.style.borderRadius = '50%';
                        if (viewBox) viewBox.style.borderRadius = '50%';
                    }
                });
            };
            reader.readAsDataURL(file);
        }
    });

    cancelCrop.addEventListener('click', function () {
        if (cropper) cropper.destroy();
        cropperModal.classList.add('hidden');
        fileInput.value = '';
    });

    cropAndUpload.addEventListener('click', function () {
        if (!cropper) return;
        cropper.getCroppedCanvas({ width: 400, height: 400 }).toBlob(function (blob) {
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('_method', 'patch');
            formData.append('profile_image', blob, 'cropped.png');

            fetch('{{ route('settings.updateProfileImage') }}', {
                method: 'POST',
                body: formData
            })
            .then(res => {
                if (res.ok && res.redirected) {
                    window.location.href = res.url;
                } else {
                    alert('Gagal mengunggah gambar.');
                }
            })
            .catch(err => {
                console.error('Upload error:', err);
                alert('Terjadi kesalahan saat mengunggah.');
            });
        });
    });
});
</script>
@endpush