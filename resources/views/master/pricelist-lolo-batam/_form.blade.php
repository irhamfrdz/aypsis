@if ($errors->any())
    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
        <p class="font-semibold">Periksa kembali data berikut:</p>
        <ul class="mt-2 list-disc space-y-1 pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@php
    $fieldClass = 'mt-1 block h-11 w-full rounded-lg border-gray-300 bg-white text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500';
    $item = $pricelistLoloBatam ?? null;
@endphp

<div class="grid grid-cols-1 gap-5 md:grid-cols-2">
    <div>
        <label for="vendor" class="block text-sm font-medium text-gray-700">Vendor <span class="text-red-500">*</span></label>
        <input type="text" id="vendor" name="vendor" value="{{ old('vendor', $item?->vendor) }}" class="{{ $fieldClass }}" maxlength="255" required placeholder="Nama vendor LOLO">
    </div>
    <div>
        <label for="nama_biaya" class="block text-sm font-medium text-gray-700">Nama Biaya <span class="text-red-500">*</span></label>
        <input type="text" id="nama_biaya" name="nama_biaya" value="{{ old('nama_biaya', $item?->nama_biaya) }}" class="{{ $fieldClass }}" maxlength="255" required placeholder="Contoh: Lift On / Lift Off">
    </div>
    <div>
        <label for="size" class="block text-sm font-medium text-gray-700">Ukuran Kontainer <span class="text-red-500">*</span></label>
        <select id="size" name="size" class="{{ $fieldClass }}" required>
            <option value="">Pilih ukuran</option>
            @foreach (['20', '40', '45'] as $size)
                <option value="{{ $size }}" @selected((string) old('size', $item?->size) === $size)>{{ $size }} Feet</option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="tarif" class="block text-sm font-medium text-gray-700">Tarif (IDR) <span class="text-red-500">*</span></label>
        <input type="number" id="tarif" name="tarif" value="{{ old('tarif', $item ? (float) $item->tarif : null) }}" class="{{ $fieldClass }}" min="0" step="0.01" required placeholder="0">
    </div>
    <div>
        <label for="status" class="block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
        <select id="status" name="status" class="{{ $fieldClass }}" required>
            <option value="aktif" @selected(old('status', $item?->status ?? 'aktif') === 'aktif')>Aktif</option>
            <option value="non-aktif" @selected(old('status', $item?->status) === 'non-aktif')>Tidak Aktif</option>
        </select>
    </div>
    <div class="md:col-span-2">
        <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
        <textarea id="keterangan" name="keterangan" rows="3" maxlength="2000" class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Catatan tambahan tarif">{{ old('keterangan', $item?->keterangan) }}</textarea>
    </div>
</div>

<div class="mt-6 flex justify-end gap-3 border-t border-gray-100 pt-5">
    <a href="{{ route('master.pricelist-lolo-batam.index') }}" class="inline-flex h-10 items-center rounded-lg border border-gray-300 bg-white px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</a>
    <button type="submit" class="inline-flex h-10 items-center gap-2 rounded-lg bg-indigo-600 px-5 text-sm font-semibold text-white hover:bg-indigo-700">
        <i class="fas fa-save" aria-hidden="true"></i>{{ $submitLabel }}
    </button>
</div>
