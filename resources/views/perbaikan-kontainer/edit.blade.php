@extends('layouts.app')

@section('title', 'Edit Perbaikan Kontainer')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Edit Perbaikan Kontainer</h1>
                    <p class="text-gray-600 mt-1">Ubah data perbaikan kontainer</p>
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
            <form action="{{ route('perbaikan-kontainer.update', $perbaikanKontainer) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Kontainer Selection -->
                <div>
                    <label for="nomor_kontainer" class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor Kontainer <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="nomor_kontainer" name="nomor_kontainer"
                           value="{{ old('nomor_kontainer', $perbaikanKontainer->kontainer->nomor_kontainer ?? '') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Masukkan nomor kontainer..."
                           required>
                    @error('nomor_kontainer')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nomor Memo Perbaikan -->
                <div>
                    <label for="nomor_memo_perbaikan" class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor Memo Perbaikan
                    </label>
                    <input type="text" id="nomor_memo_perbaikan" name="nomor_memo_perbaikan"
                           value="{{ old('nomor_memo_perbaikan', $perbaikanKontainer->nomor_memo_perbaikan ?? '') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50"
                           placeholder="Otomatis diisi sistem..."
                           readonly>
                    <p class="mt-1 text-sm text-gray-500">Format: MP + cetakan + tahun + bulan + running number</p>
                    @error('nomor_memo_perbaikan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jenis Kerusakan -->
                <div>
                    <label for="jenis_perbaikan" class="block text-sm font-medium text-gray-700 mb-2">
                        Jenis Perbaikan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="jenis_perbaikan" name="jenis_perbaikan"
                           value="{{ old('jenis_perbaikan', $perbaikanKontainer->jenis_perbaikan) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Jenis perbaikan..."
                           required>
                    @error('jenis_perbaikan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi Kerusakan -->
                <div>
                    <label for="deskripsi_perbaikan" class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi Perbaikan
                    </label>
                    <textarea id="deskripsi_perbaikan" name="deskripsi_perbaikan" rows="4"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Deskripsi detail perbaikan...">{{ old('deskripsi_perbaikan', $perbaikanKontainer->deskripsi_perbaikan) }}</textarea>
                    @error('deskripsi_perbaikan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select id="status_perbaikan" name="status_perbaikan"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required>
                        <option value="">Pilih Status</option>
                        <option value="belum_masuk_pranota" {{ (old('status_perbaikan') ?? $perbaikanKontainer->status_perbaikan) == 'belum_masuk_pranota' ? 'selected' : '' }}>Belum Masuk Pranota</option>
                        <option value="sudah_masuk_pranota" {{ (old('status_perbaikan') ?? $perbaikanKontainer->status_perbaikan) == 'sudah_masuk_pranota' ? 'selected' : '' }}>Sudah Masuk Pranota</option>
                        <option value="sudah_dibayar" {{ (old('status_perbaikan') ?? $perbaikanKontainer->status_perbaikan) == 'sudah_dibayar' ? 'selected' : '' }}>Sudah Dibayar</option>
                    </select>
                    @error('status_perbaikan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tanggal -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="tanggal_perbaikan" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Perbaikan <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="tanggal_perbaikan" name="tanggal_perbaikan"
                               value="{{ old('tanggal_perbaikan', $perbaikanKontainer->tanggal_perbaikan ? \Carbon\Carbon::parse($perbaikanKontainer->tanggal_perbaikan)->format('Y-m-d') : '') }}"
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
                        <input type="date" id="tanggal_selesai" name="tanggal_selesai"
                               value="{{ old('tanggal_selesai', $perbaikanKontainer->tanggal_selesai ? \Carbon\Carbon::parse($perbaikanKontainer->tanggal_selesai)->format('Y-m-d') : '') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('tanggal_selesai')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Biaya -->
                <div>
                    <label for="biaya_perbaikan" class="block text-sm font-medium text-gray-700 mb-2">
                        Biaya Perbaikan
                    </label>
                    <input type="number" id="biaya_perbaikan" name="biaya_perbaikan"
                           value="{{ old('biaya_perbaikan', $perbaikanKontainer->biaya_perbaikan) }}" step="0.01" min="0"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="0.00">
                    @error('biaya_perbaikan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Teknisi dan Catatan -->
                <div>
                    <label for="catatan" class="block text-sm font-medium text-gray-700 mb-2">
                        Catatan
                    </label>
                    <textarea id="catatan" name="catatan" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Catatan tambahan...">{{ old('catatan', $perbaikanKontainer->catatan) }}</textarea>
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
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
