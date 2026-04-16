@extends('layouts.app')

@section('title', 'Edit Data Stock')
@section('page_title', 'Edit Data Stock ' . ucwords(str_replace('-', ' ', $type)))

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6 font-primary">Edit Data Stock: {{ $item->namaStockBan->nama ?? 'Unknown' }}</h1>
            
            <form action="{{ route('stock-ban.update-lain', ['type' => $type, 'id' => $item->id]) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Nama Barang -->
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Barang <span class="text-red-500">*</span></label>
                        <select name="nama_stock_ban_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nama_stock_ban_id') border-red-500 @enderror" required>
                            <option value="">-- Pilih Nama Barang --</option>
                            @foreach($namaStockBans as $nb)
                                <option value="{{ $nb->id }}" {{ old('nama_stock_ban_id', $item->nama_stock_ban_id) == $nb->id ? 'selected' : '' }}>{{ $nb->nama }}</option>
                            @endforeach
                        </select>
                        @error('nama_stock_ban_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kuantitas -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Quantity <span class="text-red-500">*</span></label>
                        <input type="number" name="qty" value="{{ old('qty', $item->qty) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('qty') border-red-500 @enderror" min="0" required>
                        @error('qty')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Satuan / Type -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Satuan / Type</label>
                        <input type="text" name="type" value="{{ old('type', $item->type) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('type') border-red-500 @enderror" placeholder="Contoh: pcs, set, pail, dll">
                        @error('type')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Ukuran -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Ukuran</label>
                        <input type="text" name="ukuran" value="{{ old('ukuran', $item->ukuran) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('ukuran') border-red-500 @enderror" placeholder="Masukkan ukuran jika ada">
                        @error('ukuran')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Harga Beli -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Harga Beli Satuan (Rp)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                            <input type="number" name="harga_beli" value="{{ old('harga_beli', $item->harga_beli) }}" class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('harga_beli') border-red-500 @enderror" min="0" step="0.01">
                        </div>
                        @error('harga_beli')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Masuk -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Masuk <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_masuk" value="{{ old('tanggal_masuk', $item->tanggal_masuk ? $item->tanggal_masuk->format('Y-m-d') : date('Y-m-d')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('tanggal_masuk') border-red-500 @enderror" required>
                        @error('tanggal_masuk')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Lokasi -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Lokasi Gudang</label>
                        <select name="lokasi" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('lokasi') border-red-500 @enderror">
                            <option value="">-- Pilih Lokasi --</option>
                            @foreach($gudangs as $gudang)
                                <option value="{{ $gudang->nama_gudang }}" {{ old('lokasi', $item->lokasi) == $gudang->nama_gudang ? 'selected' : '' }}>{{ $gudang->nama_gudang }}</option>
                            @endforeach
                        </select>
                        @error('lokasi')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                <!-- Keterangan -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Keterangan</label>
                    <textarea name="keterangan" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-gray-400" placeholder="Catatan tambahan...">{{ old('keterangan', $item->keterangan) }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                    <a href="{{ route('stock-ban.index', ['tab' => 'barang-lainnya']) }}" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition duration-200">
                        Batal
                    </a>
                    <button type="submit" class="px-8 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-200 transition duration-200 transform hover:scale-[1.02] active:scale-[0.98]">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
