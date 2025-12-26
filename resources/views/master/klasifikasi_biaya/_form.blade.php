@csrf

<div class="space-y-4">
    <div>
        <label class="text-xs font-medium">Kode</label>
        <input type="text" name="kode" value="{{ old('kode', $item->kode ?? '') }}" class="w-full mt-1 px-3 py-2 border rounded">
        @error('kode')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
    </div>

    <div>
        <label class="text-xs font-medium">Nama</label>
        <input type="text" name="nama" value="{{ old('nama', $item->nama ?? '') }}" class="w-full mt-1 px-3 py-2 border rounded">
        @error('nama')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
    </div>

    <div>
        <label class="text-xs font-medium">Deskripsi</label>
        <textarea name="deskripsi" class="w-full mt-1 px-3 py-2 border rounded" rows="3">{{ old('deskripsi', $item->deskripsi ?? '') }}</textarea>
        @error('deskripsi')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
    </div>

    <div class="flex items-center">
        <input type="checkbox" name="is_active" id="is_active" class="mr-2" {{ old('is_active', $item->is_active ?? true) ? 'checked' : '' }}>
        <label for="is_active" class="text-xs">Aktif</label>
    </div>

    <div>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
        <a href="{{ route('klasifikasi-biaya.index') }}" class="ml-2 text-gray-600">Batal</a>
    </div>
</div>
