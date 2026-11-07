<div id="pelindo_wrapper" class="md:col-span-2 hidden rounded-lg border border-blue-200 bg-blue-50 p-4">
    <h3 class="mb-1 text-base font-semibold text-gray-800">Detail Tagihan Pelindo</h3>
    <p class="mb-4 text-sm text-gray-600">Pilih kapal dan voyage yang tercatat pada manifest.</p>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label for="pelindo_kapal" class="mb-2 block text-sm font-medium text-gray-700">Kapal <span class="text-red-500">*</span></label>
            <select id="pelindo_kapal" name="pelindo_kapal" class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 @error('pelindo_kapal') border-red-500 @enderror" disabled>
                <option value="">-- Pilih Kapal --</option>
                @foreach($pelindoManifestVoyages->pluck('nama_kapal')->unique() as $namaKapal)
                    <option value="{{ $namaKapal }}" @selected(old('pelindo_kapal') === $namaKapal)>{{ $namaKapal }}</option>
                @endforeach
            </select>
            @error('pelindo_kapal') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="pelindo_voyage" class="mb-2 block text-sm font-medium text-gray-700">Voyage <span class="text-red-500">*</span></label>
            <select id="pelindo_voyage" name="pelindo_voyage" data-old-value="{{ old('pelindo_voyage') }}" class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 @error('pelindo_voyage') border-red-500 @enderror" disabled>
                <option value="">-- Pilih Kapal Terlebih Dahulu --</option>
            </select>
            @error('pelindo_voyage') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>
    <div class="mt-4">
        <label for="pelindo_barang" class="mb-2 block text-sm font-medium text-gray-700">Barang <span class="text-red-500">*</span></label>
        <select id="pelindo_barang" name="pelindo_barang" class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 @error('pelindo_barang') border-red-500 @enderror" disabled>
            <option value="">-- Pilih Barang --</option>
            @foreach($pelindoBarangOptions as $option)
                <option value="{{ $option['value'] }}" @selected(old('pelindo_barang') === $option['value'])>{{ $option['label'] }}</option>
            @endforeach
        </select>
        @error('pelindo_barang') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
</div>
