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
        <label for="pelindo_jenis_tagihan" class="mb-2 block text-sm font-medium text-gray-700">Jenis Tagihan <span class="text-red-500">*</span></label>
        <select id="pelindo_jenis_tagihan" name="pelindo_jenis_tagihan" class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 @error('pelindo_jenis_tagihan') border-red-500 @enderror" disabled>
            <option value="">-- Pilih Jenis Tagihan --</option>
            @foreach($pelindoJenisTagihanOptions as $kegiatan)
                <option value="{{ $kegiatan }}" @selected(old('pelindo_jenis_tagihan') === $kegiatan)>{{ $kegiatan }}</option>
            @endforeach
        </select>
        @error('pelindo_jenis_tagihan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-3">
        <div>
            <label for="pelindo_barang" class="mb-2 block text-sm font-medium text-gray-700">Barang <span class="text-red-500">*</span></label>
            <select id="pelindo_barang" name="pelindo_barang" class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 @error('pelindo_barang') border-red-500 @enderror" disabled>
                <option value="">-- Pilih Jenis Tagihan Terlebih Dahulu --</option>
            </select>
            @error('pelindo_barang') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="pelindo_jumlah" class="mb-2 block text-sm font-medium text-gray-700">Jumlah <span class="text-red-500">*</span></label>
            <input type="number" id="pelindo_jumlah" name="pelindo_jumlah" value="{{ old('pelindo_jumlah') }}" min="1" step="1" class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 @error('pelindo_jumlah') border-red-500 @enderror" disabled>
            @error('pelindo_jumlah') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="pelindo_biaya" class="mb-2 block text-sm font-medium text-gray-700">Biaya <span class="text-red-500">*</span></label>
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                <input type="number" id="pelindo_biaya" name="pelindo_biaya" value="{{ old('pelindo_biaya') }}" min="0" step="1" class="w-full rounded-lg border border-gray-300 py-2 pl-12 pr-4 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 @error('pelindo_biaya') border-red-500 @enderror" disabled>
            </div>
            <p class="mt-1 text-xs text-gray-500">Biaya per barang.</p>
            @error('pelindo_biaya') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>
</div>
