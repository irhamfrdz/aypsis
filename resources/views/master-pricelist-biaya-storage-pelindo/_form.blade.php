<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div>
        <label for="vendor" class="mb-1 block text-sm font-medium text-gray-700">Vendor / Terminal <span class="text-red-500">*</span></label>
        <input id="vendor" name="vendor" value="{{ old('vendor', $pricelist->vendor ?? '') }}" required maxlength="255" class="w-full rounded-lg border-gray-300 text-sm">
        @error('vendor')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="lokasi" class="mb-1 block text-sm font-medium text-gray-700">Lokasi <span class="text-red-500">*</span></label>
        <input id="lokasi" name="lokasi" value="{{ old('lokasi', $pricelist->lokasi ?? '') }}" required maxlength="255" class="w-full rounded-lg border-gray-300 text-sm">
        @error('lokasi')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="size_kontainer" class="mb-1 block text-sm font-medium text-gray-700">Ukuran Kontainer <span class="text-red-500">*</span></label>
        <select id="size_kontainer" name="size_kontainer" required class="w-full rounded-lg border-gray-300 text-sm">
            @foreach (['20', '40', '45'] as $size)
                <option value="{{ $size }}" @selected(old('size_kontainer', $pricelist->size_kontainer ?? '20') === $size)>{{ $size }}'</option>
            @endforeach
        </select>
        @error('size_kontainer')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="status" class="mb-1 block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
        <select id="status" name="status" required class="w-full rounded-lg border-gray-300 text-sm">
            <option value="aktif" @selected(old('status', $pricelist->status ?? 'aktif') === 'aktif')>Aktif</option>
            <option value="non-aktif" @selected(old('status', $pricelist->status ?? 'aktif') === 'non-aktif')>Nonaktif</option>
        </select>
    </div>
    <div>
        <label for="tarif_massa_1" class="mb-1 block text-sm font-medium text-gray-700">Tarif Massa 1 (Rp) <span class="text-red-500">*</span></label>
        <input id="tarif_massa_1" type="number" name="tarif_massa_1" value="{{ old('tarif_massa_1', $pricelist->tarif_massa_1 ?? 0) }}" required min="0" step="0.01" class="w-full rounded-lg border-gray-300 text-sm">
        @error('tarif_massa_1')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="tarif_massa_2" class="mb-1 block text-sm font-medium text-gray-700">Tarif Massa 2 (Rp)</label>
        <input id="tarif_massa_2" type="number" name="tarif_massa_2" value="{{ old('tarif_massa_2', $pricelist->tarif_massa_2 ?? '') }}" min="0" step="0.01" class="w-full rounded-lg border-gray-300 text-sm">
        @error('tarif_massa_2')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div class="md:col-span-2">
        <label for="keterangan" class="mb-1 block text-sm font-medium text-gray-700">Keterangan</label>
        <textarea id="keterangan" name="keterangan" rows="3" maxlength="2000" class="w-full rounded-lg border-gray-300 text-sm">{{ old('keterangan', $pricelist->keterangan ?? '') }}</textarea>
        @error('keterangan')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
</div>
