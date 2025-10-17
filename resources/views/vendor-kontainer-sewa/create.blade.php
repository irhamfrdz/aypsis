@extends('layouts.app')

@section('title', 'Tambah Vendor Kontainer Sewa')
@section('page_title', 'Tambah Vendor Kontainer Sewa')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 mr-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <div>
                            <h1 class="text-2xl font-bold text-white">Tambah Vendor Kontainer Sewa</h1>
                            <p class="text-blue-100 text-sm">Buat vendor kontainer sewa baru</p>
                        </div>
                    </div>
                    <a href="{{ route('vendor-kontainer-sewa.index') }}" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Alerts -->
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <!-- Form -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Informasi Vendor</h2>
                <p class="text-sm text-gray-600 mt-1">Lengkapi form berikut untuk menambahkan vendor baru</p>
            </div>

            <div class="p-6">
                <form action="{{ route('vendor-kontainer-sewa.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Kode -->
                    <div class="space-y-2">
                        <label for="kode" class="block text-sm font-medium text-gray-700">
                            Kode Vendor <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="kode"
                               id="kode"
                               value="{{ old('kode') }}"
                               required
                               maxlength="50"
                               placeholder="Masukkan kode vendor"
                               class="block w-full px-3 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out @error('kode') border-red-300 @enderror">
                        @error('kode')
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="text-xs text-gray-500">Kode unik untuk mengidentifikasi vendor (maksimal 50 karakter)</p>
                    </div>

                    <!-- Nama Vendor -->
                    <div class="space-y-2">
                        <label for="nama_vendor" class="block text-sm font-medium text-gray-700">
                            Nama Vendor <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="nama_vendor"
                               id="nama_vendor"
                               value="{{ old('nama_vendor') }}"
                               required
                               maxlength="255"
                               placeholder="Masukkan nama vendor"
                               class="block w-full px-3 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out @error('nama_vendor') border-red-300 @enderror">
                        @error('nama_vendor')
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="space-y-2">
                        <label for="status" class="block text-sm font-medium text-gray-700">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select name="status"
                                    id="status"
                                    required
                                    class="block w-full px-3 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out @error('status') border-red-300 @enderror">
                                <option value="">Pilih Status</option>
                                <option value="aktif" {{ old('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="non-aktif" {{ old('status') === 'non-aktif' ? 'selected' : '' }}>Non-Aktif</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Catatan -->
                    <div class="space-y-2">
                        <label for="catatan" class="block text-sm font-medium text-gray-700">
                            Catatan
                        </label>
                        <textarea name="catatan"
                                  id="catatan"
                                  rows="4"
                                  placeholder="Masukkan catatan atau keterangan tambahan (opsional)"
                                  class="block w-full px-3 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out resize-none @error('catatan') border-red-300 @enderror">{{ old('catatan') }}</textarea>
                        @error('catatan')
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="text-xs text-gray-500">Informasi tambahan tentang vendor ini</p>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <a href="{{ route('vendor-kontainer-sewa.index') }}"
                           class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Batal
                        </a>
                        <button type="submit"
                                class="inline-flex items-center px-8 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-lg transition duration-150 ease-in-out">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Simpan Vendor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto generate kode from nama vendor
    $('#nama_vendor').on('input', function() {
        if ($('#kode').val() === '') {
            let namaVendor = $(this).val();
            let kode = namaVendor
                .replace(/[^a-zA-Z0-9\s]/g, '') // Remove special characters
                .replace(/\s+/g, '') // Remove spaces
                .substring(0, 10) // Limit to 10 characters
                .toUpperCase();
            $('#kode').val(kode);
        }
    });

    // Form validation
    $('form').on('submit', function(e) {
        let hasErrors = false;

        // Check required fields
        if ($('#kode').val().trim() === '') {
            hasErrors = true;
        }
        if ($('#nama_vendor').val().trim() === '') {
            hasErrors = true;
        }
        if ($('#status').val() === '') {
            hasErrors = true;
        }

        if (hasErrors) {
            e.preventDefault();
            alert('Mohon lengkapi semua field yang wajib diisi!');
        }
    });
});
</script>
@endpush
