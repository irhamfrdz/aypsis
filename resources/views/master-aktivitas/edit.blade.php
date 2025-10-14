@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-4">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-4 border-b border-gray-200">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Edit Aktivitas</h1>
                <p class="text-xs text-gray-600 mt-1">Edit data aktivitas: {{ $aktivitas->nama_aktivitas }}</p>
            </div>
            <a href="{{ route('master-aktivitas.index') }}"
               class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-lg transition-colors duration-150 flex items-center text-sm whitespace-nowrap">
                <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>

        <!-- Form -->
        <form action="{{ route('master-aktivitas.update', $aktivitas) }}" method="POST" class="p-4">
            @csrf
            @method('PUT')

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <div class="space-y-4">
                <!-- Kode -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kode <span class="text-red-600">*</span></label>
                    <input type="text"
                           name="kode"
                           value="{{ old('kode', $aktivitas->kode) }}"
                           required
                           placeholder="Masukkan kode aktivitas"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('kode') border-red-500 @enderror">
                    @error('kode')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nama Aktivitas -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Aktivitas <span class="text-red-600">*</span></label>
                    <input type="text"
                           name="nama_aktivitas"
                           value="{{ old('nama_aktivitas', $aktivitas->nama_aktivitas) }}"
                           required
                           placeholder="Masukkan nama aktivitas"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('nama_aktivitas') border-red-500 @enderror">
                    @error('nama_aktivitas')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Catatan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                    <textarea name="catatan"
                              rows="3"
                              placeholder="Masukkan catatan (opsional)"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('catatan') border-red-500 @enderror">{{ old('catatan', $aktivitas->catatan) }}</textarea>
                    @error('catatan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-600">*</span></label>
                    <select name="status"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('status') border-red-500 @enderror">
                        <option value="">Pilih Status</option>
                        <option value="active" {{ old('status', $aktivitas->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $aktivitas->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                <a href="{{ route('master-aktivitas.index') }}"
                   class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg transition-colors duration-150">
                    Batal
                </a>
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg transition-colors duration-150">
                    Perbarui
                </button>
            </div>
        </form>
    </div>
</div>
@endsection