@extends('layouts.app')

@section('title', 'Tambah Pekerjaan')
@section('page_title', 'Tambah Pekerjaan')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center">
                <a href="{{ route('master.pekerjaan.index') }}" class="mr-4 p-2 text-gray-400 hover:text-gray-600 transition-colors duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Tambah Pekerjaan Baru</h1>
                    <p class="mt-1 text-sm text-gray-600">Buat pekerjaan atau jabatan baru dalam sistem</p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Informasi Pekerjaan</h3>
                <p class="mt-1 text-sm text-gray-600">Lengkapi informasi pekerjaan yang akan ditambahkan</p>
            </div>

            <form action="{{ route('master.pekerjaan.store') }}" method="POST" class="p-6 space-y-6">
                @csrf

                <!-- Nama Pekerjaan -->
                <div>
                    <label for="nama_pekerjaan" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="text-red-500">*</span> Nama Pekerjaan
                    </label>
                    <input type="text"
                           name="nama_pekerjaan"
                           id="nama_pekerjaan"
                           value="{{ old('nama_pekerjaan') }}"
                           required
                           class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                           placeholder="Masukkan nama pekerjaan">
                    @error('nama_pekerjaan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kode Pekerjaan -->
                <div>
                    <label for="kode_pekerjaan" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="text-red-500">*</span> Kode Pekerjaan
                    </label>
                    <input type="text"
                           name="kode_pekerjaan"
                           id="kode_pekerjaan"
                           value="{{ old('kode_pekerjaan') }}"
                           required
                           maxlength="20"
                           class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                           placeholder="Masukkan kode pekerjaan (maksimal 20 karakter)"
                           style="text-transform: uppercase;">
                    @error('kode_pekerjaan')
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

                <!-- Divisi -->
                <div>
                    <label for="divisi" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="text-red-500">*</span> Divisi
                    </label>
                    <select name="divisi"
                            id="divisi"
                            required
                            class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200">
                        <option value="">Pilih Divisi</option>
                        @foreach($divisis as $divisi)
                            <option value="{{ $divisi->nama_divisi }}" {{ old('divisi') === $divisi->nama_divisi ? 'selected' : '' }}>
                                {{ $divisi->nama_divisi }} ({{ $divisi->kode_divisi }})
                            </option>
                        @endforeach
                    </select>
                    @error('divisi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
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
                    <p class="mt-1 text-xs text-gray-500">Centang untuk mengaktifkan pekerjaan ini</p>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('master.pekerjaan.index') }}"
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
    const kodePekerjaanInput = document.getElementById('kode_pekerjaan');
    if (kodePekerjaanInput) {
        kodePekerjaanInput.addEventListener('input', function() {
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
