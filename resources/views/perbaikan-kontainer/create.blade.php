@extends('layouts.app')

@section('title', 'Tambah Perbaikan Kontainer')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Tambah Perbaikan Kontainer</h1>
                    <p class="text-gray-600 mt-1">Masukkan data perbaikan kontainer baru</p>
                </div>
                <a href="{{ route('perbaikan-kontainer.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition duration-200 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>
            </div>

            <!-- Form -->
            <form action="{{ route('perbaikan-kontainer.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Kontainer Selection -->
                <div>
                    <label for="nomor_kontainer" class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor Kontainer <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="nomor_kontainer" name="nomor_kontainer"
                           value="{{ old('nomor_kontainer') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Masukkan nomor kontainer..."
                           required>
                    @error('nomor_kontainer')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Estimasi Kerusakan Kontainer -->
                <div>
                    <label for="estimasi_kerusakan_kontainer" class="block text-sm font-medium text-gray-700 mb-2">
                        Estimasi Kerusakan Kontainer <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="estimasi_kerusakan_kontainer" name="estimasi_kerusakan_kontainer"
                           value="{{ old('estimasi_kerusakan_kontainer') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Estimasi kerusakan kontainer..."
                           required>
                    @error('estimasi_kerusakan_kontainer')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi Kerusakan -->
                <div>
                    <label for="deskripsi_perbaikan" class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi Perbaikan <span class="text-red-500">*</span>
                    </label>
                    <textarea id="deskripsi_perbaikan" name="deskripsi_perbaikan" rows="4"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Deskripsi detail perbaikan..."
                              required>{{ old('deskripsi_perbaikan') }}</textarea>
                    @error('deskripsi_perbaikan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Realisasi Kerusakan Kontainer -->
                <div>
                    <label for="realisasi_kerusakan" class="block text-sm font-medium text-gray-700 mb-2">
                        Realisasi Kerusakan Kontainer
                    </label>
                    <textarea id="realisasi_kerusakan" name="realisasi_kerusakan" rows="4"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Deskripsikan realisasi kerusakan yang ditemukan saat perbaikan...">{{ old('realisasi_kerusakan') }}</textarea>
                    <p class="mt-1 text-sm text-gray-500">Isi dengan kondisi kerusakan yang sebenarnya ditemukan saat proses perbaikan</p>
                    @error('realisasi_kerusakan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Biaya Perbaikan -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="estimasi_biaya_perbaikan" class="block text-sm font-medium text-gray-700 mb-2">
                            Estimasi Biaya Perbaikan
                        </label>
                        <input type="number" id="estimasi_biaya_perbaikan" name="estimasi_biaya_perbaikan"
                               value="{{ old('estimasi_biaya_perbaikan') }}" step="0.01" min="0"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="0.00">
                        <p class="mt-1 text-sm text-gray-500">Estimasi biaya yang diperlukan untuk perbaikan</p>
                        @error('estimasi_biaya_perbaikan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="realisasi_biaya_perbaikan" class="block text-sm font-medium text-gray-700 mb-2">
                            Realisasi Biaya Perbaikan
                        </label>
                        <input type="number" id="realisasi_biaya_perbaikan" name="realisasi_biaya_perbaikan"
                               value="{{ old('realisasi_biaya_perbaikan') }}" step="0.01" min="0"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="0.00">
                        <p class="mt-1 text-sm text-gray-500">Biaya aktual yang dikeluarkan untuk perbaikan</p>
                        @error('realisasi_biaya_perbaikan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Tanggal -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="tanggal_perbaikan" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Perbaikan <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="tanggal_perbaikan" name="tanggal_perbaikan" value="{{ old('tanggal_perbaikan') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               required>
                        @error('tanggal_perbaikan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Selesai
                        </label>
                        <input type="date" id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('tanggal_selesai')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Teknisi dan Catatan -->
                <div>
                    <label for="catatan" class="block text-sm font-medium text-gray-700 mb-2">
                        Catatan
                    </label>
                    <textarea id="catatan" name="catatan" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Catatan tambahan...">{{ old('catatan') }}</textarea>
                    @error('catatan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-4 pt-6 border-t">
                    <a href="{{ route('perbaikan-kontainer.index') }}"
                       class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-medium transition duration-200">
                        Batal
                    </a>
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
