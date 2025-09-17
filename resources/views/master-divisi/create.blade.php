@extends('layouts.app')

@section('title', 'Tambah Divisi')
@section('page_title', 'Tambah Divisi')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center">
                <a href="{{ route('master.divisi.index') }}" class="mr-4 p-2 text-gray-400 hover:text-gray-600 transition-colors duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Tambah Divisi Baru</h1>
                    <p class="mt-1 text-sm text-gray-600">Buat divisi atau departemen baru dalam sistem</p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Informasi Divisi</h3>
                <p class="mt-1 text-sm text-gray-600">Lengkapi informasi divisi yang akan ditambahkan</p>
            </div>

            <form action="{{ route('master.divisi.store') }}" method="POST" class="p-6 space-y-6">
                @csrf

                <!-- Nama Divisi -->
                <div>
                    <label for="nama_divisi" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="text-red-500">*</span> Nama Divisi
                    </label>
                    <input type="text"
                           name="nama_divisi"
                           id="nama_divisi"
                           value="{{ old('nama_divisi') }}"
                           required
                           class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                           placeholder="Masukkan nama divisi">
                    @error('nama_divisi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kode Divisi -->
                <div>
                    <label for="kode_divisi" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="text-red-500">*</span> Kode Divisi
                    </label>
                    <input type="text"
                           name="kode_divisi"
                           id="kode_divisi"
                           value="{{ old('kode_divisi') }}"
                           required
                           maxlength="20"
                           class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                           placeholder="Masukkan kode divisi (maksimal 20 karakter)"
                           style="text-transform: uppercase;">
                    @error('kode_divisi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Kode akan otomatis dikonversi ke huruf besar</p>
                </div>

                <!-- Deskripsi -->
                <div>
                    <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi
                    </label>
                    <textarea name="deskripsi"
                              id="deskripsi"
                              rows="4"
                              maxlength="500"
                              class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                              placeholder="Masukkan deskripsi divisi (opsional)">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Maksimal 500 karakter</p>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Status Divisi
                    </label>
                    <div class="flex items-center space-x-4">
                        <label class="flex items-center">
                            <input type="checkbox"
                                   name="is_active"
                                   value="1"
                                   {{ old('is_active', true) ? 'checked' : '' }}
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700">Aktif</span>
                        </label>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Centang untuk mengaktifkan divisi ini</p>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('master.divisi.index') }}"
                       class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Batal
                    </a>
                    <button type="submit"
                            class="inline-flex items-center justify-center px-6 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Simpan Divisi
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto uppercase untuk kode divisi
    const kodeDivisiInput = document.getElementById('kode_divisi');
    if (kodeDivisiInput) {
        kodeDivisiInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    }

    // Character counter untuk deskripsi
    const deskripsiTextarea = document.getElementById('deskripsi');
    if (deskripsiTextarea) {
        const maxLength = 500;
        const counter = document.createElement('div');
        counter.className = 'text-xs text-gray-500 mt-1 text-right';
        deskripsiTextarea.parentNode.appendChild(counter);

        function updateCounter() {
            const remaining = maxLength - deskripsiTextarea.value.length;
            counter.textContent = `${deskripsiTextarea.value.length}/${maxLength} karakter`;
            counter.className = `text-xs mt-1 text-right ${remaining < 50 ? 'text-orange-600' : 'text-gray-500'}`;
        }

        deskripsiTextarea.addEventListener('input', updateCounter);
        updateCounter(); // Initial count
    }
});
</script>
@endpush
@endsection
