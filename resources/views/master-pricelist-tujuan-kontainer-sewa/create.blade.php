@extends('layouts.app')

@section('title', 'Tambah Pricelist Tujuan Kontainer Sewa')
@section('page_title', 'Tambah Pricelist Tujuan')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-plus-circle mr-3"></i>
                    Tambah Pricelist Baru
                </h3>
            </div>

            <form action="{{ route('master-pricelist-tujuan-kontainer-sewa.store') }}" method="POST" class="p-6">
                @csrf

                <div class="grid grid-cols-1 gap-6">
                    <!-- Tujuan -->
                    <div>
                        <label for="tujuan" class="block text-sm font-bold text-gray-700 mb-2">Tujuan <span class="text-red-500">*</span></label>
                        <input type="text" name="tujuan" id="tujuan" value="{{ old('tujuan') }}" placeholder="Contoh: JAKARTA - SURABAYA" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition duration-200 @error('tujuan') border-red-500 @enderror">
                        @error('tujuan')
                            <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Ongkos 20ft -->
                        <div>
                            <label for="ongkos_truk_20ft" class="block text-sm font-bold text-gray-700 mb-2">Ongkos Truk 20ft (Rp) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 font-bold bg-gray-50 border-r border-gray-300 rounded-l-lg px-3">
                                    Rp
                                </span>
                                <input type="number" name="ongkos_truk_20ft" id="ongkos_truk_20ft" value="{{ old('ongkos_truk_20ft', 0) }}" min="0" required
                                    class="w-full pl-16 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition duration-200 @error('ongkos_truk_20ft') border-red-500 @enderror">
                            </div>
                            @error('ongkos_truk_20ft')
                                <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Ongkos 40ft -->
                        <div>
                            <label for="ongkos_truk_40ft" class="block text-sm font-bold text-gray-700 mb-2">Ongkos Truk 40ft (Rp) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 font-bold bg-gray-50 border-r border-gray-300 rounded-l-lg px-3">
                                    Rp
                                </span>
                                <input type="number" name="ongkos_truk_40ft" id="ongkos_truk_40ft" value="{{ old('ongkos_truk_40ft', 0) }}" min="0" required
                                    class="w-full pl-16 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition duration-200 @error('ongkos_truk_40ft') border-red-500 @enderror">
                            </div>
                            @error('ongkos_truk_40ft')
                                <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-bold text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                        <select name="status" id="status" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition duration-200">
                            <option value="aktif" {{ old('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ old('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>

                    <!-- Keterangan -->
                    <div>
                        <label for="keterangan" class="block text-sm font-bold text-gray-700 mb-2">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" rows="3" placeholder="Informasi tambahan (opsional)"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition duration-200">{{ old('keterangan') }}</textarea>
                    </div>
                </div>

                <div class="mt-8 flex items-center justify-end space-x-3">
                    <a href="{{ route('master-pricelist-tujuan-kontainer-sewa.index') }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200 font-bold text-sm">
                        Batal
                    </a>
                    <button type="submit" class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 shadow-md font-bold text-sm flex items-center">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
