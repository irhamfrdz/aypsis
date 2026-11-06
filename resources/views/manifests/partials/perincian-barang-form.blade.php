@php
    $perincianRows = old('perincian_barang', isset($manifest) ? ($manifest->perincian_barang ?: [[]]) : [[]]);
@endphp
<div class="md:col-span-2 rounded-lg border border-gray-200 {{ ($hideTitle ?? false) ? 'bg-white' : 'bg-gray-50' }} p-4">
    @unless($hideTitle ?? false)
        <h3 class="mb-3 text-sm font-semibold text-gray-800">Detail Barang Perincian</h3>
    @endunless
    @foreach($perincianRows as $index => $barang)
        <div class="grid grid-cols-1 gap-3 md:grid-cols-5 {{ !$loop->last ? 'mb-4 border-b border-gray-200 pb-4' : '' }}">
            <div>
                <label class="mb-1 block text-xs font-medium text-gray-600">Nama Barang</label>
                <input type="text" name="perincian_barang[{{ $index }}][nama_barang]" value="{{ $barang['nama_barang'] ?? '' }}" class="w-full rounded-lg border-gray-300 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-gray-600">Jumlah</label>
                <input type="number" min="0" name="perincian_barang[{{ $index }}][jumlah]" value="{{ $barang['jumlah'] ?? '' }}" class="w-full rounded-lg border-gray-300 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-gray-600">Satuan</label>
                <input type="text" name="perincian_barang[{{ $index }}][satuan]" value="{{ $barang['satuan'] ?? '' }}" class="w-full rounded-lg border-gray-300 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-gray-600">Ukuran</label>
                <input type="text" name="perincian_barang[{{ $index }}][ukuran]" value="{{ $barang['ukuran'] ?? '' }}" class="w-full rounded-lg border-gray-300 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-gray-600">Keterangan</label>
                <input type="text" name="perincian_barang[{{ $index }}][keterangan_barang]" value="{{ $barang['keterangan_barang'] ?? '' }}" class="w-full rounded-lg border-gray-300 text-sm">
            </div>
        </div>
    @endforeach
    @error('perincian_barang.*.nama_barang')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
    @error('perincian_barang.*.jumlah')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
