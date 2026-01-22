@extends('layouts.app')

@section('title', 'Edit Stock Ban')
@section('page_title', 'Edit Stock Ban')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Data Stock Ban</h1>
            
            <form action="{{ route('stock-ban.update', $stockBan->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Nomor Seri (Wajib, Unik) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Seri / Kode Ban <span class="text-red-500">*</span></label>
                        <input type="text" name="nomor_seri" value="{{ old('nomor_seri', $stockBan->nomor_seri) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nomor_seri') border-red-500 @enderror" required>
                        @error('nomor_seri')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Merk -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Merk <span class="text-red-500">*</span></label>
                        <input type="text" name="merk" value="{{ old('merk', $stockBan->merk) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('merk') border-red-500 @enderror" required placeholder="Contoh: Bridgestone, Michelin">
                        @error('merk')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Ukuran -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ukuran <span class="text-red-500">*</span></label>
                        <input type="text" name="ukuran" value="{{ old('ukuran', $stockBan->ukuran) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('ukuran') border-red-500 @enderror" required placeholder="Contoh: 1000-20, 11R22.5">
                        @error('ukuran')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kondisi -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kondisi <span class="text-red-500">*</span></label>
                        <select name="kondisi" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('kondisi') border-red-500 @enderror" required>
                            <option value="Baru" {{ old('kondisi', $stockBan->kondisi) == 'Baru' ? 'selected' : '' }}>Baru</option>
                            <option value="Vulkanisir" {{ old('kondisi', $stockBan->kondisi) == 'Vulkanisir' ? 'selected' : '' }}>Vulkanisir</option>
                            <option value="Bekas" {{ old('kondisi', $stockBan->kondisi) == 'Bekas' ? 'selected' : '' }}>Bekas</option>
                            <option value="Afkir" {{ old('kondisi', $stockBan->kondisi) == 'Afkir' ? 'selected' : '' }}>Afkir</option>
                        </select>
                        @error('kondisi')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                        <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror" required>
                            <option value="Stok" {{ old('status', $stockBan->status) == 'Stok' ? 'selected' : '' }}>Stok (Tersedia)</option>
                            <option value="Terpakai" {{ old('status', $stockBan->status) == 'Terpakai' ? 'selected' : '' }}>Terpakai (Dipasang)</option>
                            <option value="Rusak" {{ old('status', $stockBan->status) == 'Rusak' ? 'selected' : '' }}>Rusak</option>
                            <option value="Hilang" {{ old('status', $stockBan->status) == 'Hilang' ? 'selected' : '' }}>Hilang</option>
                        </select>
                        @error('status')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Mobil (Assign to Car) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pasang pada Mobil (Opsional)</label>
                        <select name="mobil_id" id="mobil_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('mobil_id') border-red-500 @enderror select2">
                            <option value="">-- Tidak Dipasang --</option>
                            @foreach($mobils as $mobil)
                                <option value="{{ $mobil->id }}" {{ old('mobil_id', $stockBan->mobil_id) == $mobil->id ? 'selected' : '' }}>
                                    {{ $mobil->nomor_polisi }} ({{ $mobil->merek }} - {{ $mobil->jenis }})
                                </option>
                            @endforeach
                        </select>
                        @error('mobil_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Lokasi -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi <span class="text-red-500">*</span></label>
                        <input type="text" name="lokasi" value="{{ old('lokasi', $stockBan->lokasi) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('lokasi') border-red-500 @enderror" required>
                        @error('lokasi')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Harga Beli -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Harga Beli <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                            <input type="number" name="harga_beli" value="{{ old('harga_beli', $stockBan->harga_beli) }}" class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('harga_beli') border-red-500 @enderror" required min="0">
                        </div>
                        @error('harga_beli')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Masuk -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Masuk <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_masuk" value="{{ old('tanggal_masuk', $stockBan->tanggal_masuk->format('Y-m-d')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('tanggal_masuk') border-red-500 @enderror" required>
                        @error('tanggal_masuk')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Keterangan -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                    <textarea name="keterangan" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('keterangan', $stockBan->keterangan) }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('stock-ban.index') }}" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition duration-200">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-200">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
