<div id="pelindo_wrapper" class="md:col-span-2 hidden rounded-lg border border-blue-200 bg-blue-50 p-4">
    <h3 class="mb-1 text-base font-semibold text-gray-800">Detail Tagihan Pelindo</h3>
    <p class="mb-4 text-sm text-gray-600">Pilih kapal dan voyage yang tercatat pada manifest.</p>
    <div id="pelindo_kapal_rows">
    <div class="pelindo-kapal-row grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label for="pelindo_kapal" class="mb-2 block text-sm font-medium text-gray-700">Kapal <span class="text-red-500">*</span></label>
            <select id="pelindo_kapal" name="pelindo_kapal" class="pelindo-kapal-select w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 @error('pelindo_kapal') border-red-500 @enderror" disabled>
                <option value="">-- Pilih Kapal --</option>
                @foreach($pelindoManifestVoyages->pluck('nama_kapal')->unique() as $namaKapal)
                    <option value="{{ $namaKapal }}" @selected(old('pelindo_kapal') === $namaKapal)>{{ $namaKapal }}</option>
                @endforeach
            </select>
            @error('pelindo_kapal') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="pelindo_voyage" class="mb-2 block text-sm font-medium text-gray-700">Voyage <span class="text-red-500">*</span></label>
            <select id="pelindo_voyage" name="pelindo_voyage" data-old-value="{{ old('pelindo_voyage') }}" class="pelindo-voyage-select w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 @error('pelindo_voyage') border-red-500 @enderror" disabled>
                <option value="">-- Pilih Kapal Terlebih Dahulu --</option>
            </select>
            @error('pelindo_voyage') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>
    @foreach(old('pelindo_tambahan', []) as $index => $tambahan)
    <div class="pelindo-kapal-row mt-4 grid grid-cols-1 gap-4 border-t border-blue-200 pt-4 md:grid-cols-2">
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-700">Kapal <span class="text-red-500">*</span></label>
            <select name="pelindo_tambahan[{{ $index }}][kapal]" class="pelindo-kapal-select w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 @error('pelindo_tambahan.'.$index.'.kapal') border-red-500 @enderror" disabled>
                <option value="">-- Pilih Kapal --</option>
                @foreach($pelindoManifestVoyages->pluck('nama_kapal')->unique() as $namaKapal)
                    <option value="{{ $namaKapal }}" @selected(($tambahan['kapal'] ?? '') === $namaKapal)>{{ $namaKapal }}</option>
                @endforeach
            </select>
            @error('pelindo_tambahan.'.$index.'.kapal') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-700">Voyage <span class="text-red-500">*</span></label>
            <select name="pelindo_tambahan[{{ $index }}][voyage]" data-old-value="{{ $tambahan['voyage'] ?? '' }}" class="pelindo-voyage-select w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 @error('pelindo_tambahan.'.$index.'.voyage') border-red-500 @enderror" disabled>
                <option value="">-- Pilih Kapal Terlebih Dahulu --</option>
            </select>
            @error('pelindo_tambahan.'.$index.'.voyage') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <button type="button" class="pelindo-remove-kapal justify-self-start text-sm font-medium text-red-600 hover:text-red-800 md:col-span-2">Hapus kapal</button>
    </div>
    @endforeach
    </div>
    <button type="button" id="pelindo_tambah_kapal" class="mt-4 inline-flex items-center gap-2 rounded-lg border border-blue-300 bg-white px-4 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-100">
        <i class="fas fa-plus" aria-hidden="true"></i> Tambah Kapal
    </button>
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
