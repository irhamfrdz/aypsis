<div class="space-y-5">
    <div>
        <label for="type_gaji_id" class="mb-1 block text-sm font-medium text-gray-700">Type Gaji ID</label>
        <input id="type_gaji_id" name="type_gaji_id" type="number" min="1" max="9223372036854775807" step="1" value="{{ old('type_gaji_id', $typeGajiKaryawan?->type_gaji_id) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm @error('type_gaji_id') border-red-500 @enderror">
        @error('type_gaji_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="nama" class="mb-1 block text-sm font-medium text-gray-700">Nama Type Gaji <span class="text-red-500">*</span></label>
        <input id="nama" name="nama" value="{{ old('nama', $typeGajiKaryawan?->nama) }}" maxlength="100" required autofocus placeholder="Contoh: Bulanan" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm @error('nama') border-red-500 @enderror">
        @error('nama')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="keterangan" class="mb-1 block text-sm font-medium text-gray-700">Keterangan</label>
        <textarea id="keterangan" name="keterangan" rows="4" maxlength="2000" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">{{ old('keterangan', $typeGajiKaryawan?->keterangan) }}</textarea>
        @error('keterangan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="status" class="mb-1 block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
        <select id="status" name="status" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
            <option value="active" @selected(old('status', $typeGajiKaryawan?->status ?? 'active') === 'active')>Aktif</option>
            <option value="inactive" @selected(old('status', $typeGajiKaryawan?->status) === 'inactive')>Nonaktif</option>
        </select>
        @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div class="flex justify-end gap-3 border-t pt-5">
        <a href="{{ route('master.type-gaji-karyawan.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700">Batal</a>
        <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Simpan</button>
    </div>
</div>
