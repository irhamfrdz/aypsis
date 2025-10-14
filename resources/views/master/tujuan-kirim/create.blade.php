@extends('layouts.app')

@section('title', 'Tambah Tujuan Kirim')
@section('page_title', 'Tambah Tujuan Kirim')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-3xl font-bold text-gray-900">Tambah Tujuan Kirim</h1>
                    <p class="mt-1 text-sm text-gray-600">Buat data tujuan pengiriman kontainer baru</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('tujuan-kirim.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-gray-500 text-white text-sm font-medium rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Form Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Informasi Tujuan Kirim</h3>
                <p class="mt-1 text-sm text-gray-600">Lengkapi form di bawah ini dengan data yang valid</p>
            </div>

            <form action="{{ route('tujuan-kirim.store') }}" method="POST" class="p-6 space-y-6">
                @csrf
                <input type="hidden" name="popup" value="{{ request('popup') }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Kode -->
                    <div>
                        <label for="kode" class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            Kode <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="kode"
                               id="kode"
                               value="{{ old('kode') }}"
                               maxlength="10"
                               required
                               placeholder="Contoh: JKT001"
                               class="w-full px-4 py-3 border rounded-lg focus:ring-2 transition-colors duration-200 @error('kode') border-red-500 focus:ring-red-500 focus:border-red-500 @else border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 @enderror">
                        @error('kode')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Kode unik maksimal 10 karakter untuk identifikasi</p>
                    </div>

                    <!-- Nama Tujuan -->
                    <div>
                        <label for="nama_tujuan" class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Nama Tujuan <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="nama_tujuan"
                               id="nama_tujuan"
                               value="{{ old('nama_tujuan', request('search')) }}"
                               maxlength="100"
                               required
                               placeholder="Contoh: Jakarta Pusat"
                               class="w-full px-4 py-3 border rounded-lg focus:ring-2 transition-colors duration-200 @error('nama_tujuan') border-red-500 focus:ring-red-500 focus:border-red-500 @else border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 @enderror">
                        @error('nama_tujuan')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Nama lengkap tujuan pengiriman maksimal 100 karakter</p>
                    </div>
                </div>

                <!-- Catatan and Status Row -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Catatan -->
                    <div class="md:col-span-2">
                        <label for="catatan" class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Catatan
                        </label>
                        <textarea name="catatan"
                                  id="catatan"
                                  rows="4"
                                  maxlength="500"
                                  placeholder="Masukkan catatan tambahan jika diperlukan..."
                                  class="w-full px-4 py-3 border rounded-lg focus:ring-2 transition-colors duration-200 resize-none @error('catatan') border-red-500 focus:ring-red-500 focus:border-red-500 @else border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 @enderror">{{ old('catatan') }}</textarea>
                        @error('catatan')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Informasi tambahan maksimal 500 karakter (opsional)</p>
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status"
                                id="status"
                                required
                                class="w-full px-4 py-3 border rounded-lg focus:ring-2 transition-colors duration-200 @error('status') border-red-500 focus:ring-red-500 focus:border-red-500 @else border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 @enderror">
                            <option value="">Pilih Status</option>
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>
                                ✅ Aktif
                            </option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                ❌ Tidak Aktif
                            </option>
                        </select>
                        @error('status')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Status aktif/nonaktif untuk tujuan kirim</p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row sm:justify-end gap-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('tujuan-kirim.index') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gray-500 text-white text-sm font-medium rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Auto-generate kode from nama_tujuan
    document.getElementById('nama_tujuan').addEventListener('input', function() {
        const namaTujuan = this.value;
        const kodeField = document.getElementById('kode');

        // Only auto-generate if kode field is empty
        if (!kodeField.value) {
            // Generate kode from first 3 characters of each word, uppercase
            const kode = namaTujuan
                .split(' ')
                .map(word => word.substring(0, 3).toUpperCase())
                .join('')
                .substring(0, 10); // Limit to 10 characters

            kodeField.value = kode;
        }
    });

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const kode = document.getElementById('kode').value.trim();
        const namaTujuan = document.getElementById('nama_tujuan').value.trim();
        const status = document.getElementById('status').value;

        if (!kode || !namaTujuan || !status) {
            e.preventDefault();
            alert('Mohon lengkapi semua field yang wajib diisi (ditandai dengan *)');
            return false;
        }

        if (kode.length < 2) {
            e.preventDefault();
            alert('Kode harus minimal 2 karakter');
            document.getElementById('kode').focus();
            return false;
        }

        return true;
    });

    // Character counter for catatan
    const catatanField = document.getElementById('catatan');
    if (catatanField) {
        const maxLength = 500;

        // Create counter element
        const counter = document.createElement('div');
        counter.className = 'text-right text-xs text-gray-400 mt-1';
        counter.id = 'catatan-counter';
        catatanField.parentNode.appendChild(counter);

        function updateCounter() {
            const currentLength = catatanField.value.length;
            counter.textContent = `${currentLength}/${maxLength} karakter`;

            if (currentLength > maxLength * 0.9) {
                counter.className = 'text-right text-xs text-orange-500 mt-1';
            } else if (currentLength === maxLength) {
                counter.className = 'text-right text-xs text-red-500 mt-1';
            } else {
                counter.className = 'text-right text-xs text-gray-400 mt-1';
            }
        }

        catatanField.addEventListener('input', updateCounter);
        updateCounter(); // Initialize counter

        // Auto-focus on nama_tujuan field if there's a search term
        const namaTujuanField = document.getElementById('nama_tujuan');
        const searchTerm = '{{ request("search") }}';

        if (namaTujuanField && searchTerm) {
            namaTujuanField.focus();
            namaTujuanField.select();
        }
    }
</script>
@endsection
