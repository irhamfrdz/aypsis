<div class="pelindo-section rounded-lg border-2 border-blue-200 bg-blue-50 p-4">
    <div class="mb-4 flex items-center justify-between">
        <h4 class="pelindo-section-title font-semibold text-gray-800">Kapal (Tagihan Pelindo)</h4>
        <button type="button" class="pelindo-remove-kapal hidden rounded-lg bg-red-500 px-3 py-1 text-sm text-white hover:bg-red-600">Hapus</button>
    </div>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-700">Kapal <span class="text-red-500">*</span></label>
            <select name="pelindo_sections[{{ $index }}][kapal]" class="pelindo-kapal-select w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500" disabled>
                <option value="">-- Pilih Kapal --</option>
                @foreach($pelindoManifestVoyages->pluck('nama_kapal')->unique() as $namaKapal)
                    <option value="{{ $namaKapal }}" @selected(($section['kapal'] ?? '') === $namaKapal)>{{ $namaKapal }}</option>
                @endforeach
            </select>
            @unless($isTemplate) @error('pelindo_sections.'.$index.'.kapal') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror @endunless
        </div>
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-700">Voyage <span class="text-red-500">*</span></label>
            <select name="pelindo_sections[{{ $index }}][voyage]" data-old-value="{{ $section['voyage'] ?? '' }}" class="pelindo-voyage-select w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500" disabled>
                <option value="">-- Pilih Kapal Terlebih Dahulu --</option>
            </select>
            @unless($isTemplate) @error('pelindo_sections.'.$index.'.voyage') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror @endunless
        </div>
    </div>
    <div class="mt-4">
        <label class="mb-2 block text-sm font-medium text-gray-700">Jenis Tagihan <span class="text-red-500">*</span></label>
        <select name="pelindo_sections[{{ $index }}][jenis_tagihan]" class="pelindo-jenis-tagihan-select w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500" disabled>
            <option value="">-- Pilih Jenis Tagihan --</option>
            @foreach($pelindoJenisTagihanOptions as $kegiatan)
                <option value="{{ $kegiatan }}" @selected(($section['jenis_tagihan'] ?? '') === $kegiatan)>{{ $kegiatan }}</option>
            @endforeach
        </select>
        @unless($isTemplate) @error('pelindo_sections.'.$index.'.jenis_tagihan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror @endunless
    </div>
    <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-3">
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-700">Barang <span class="text-red-500">*</span></label>
            <select name="pelindo_sections[{{ $index }}][barang]" data-old-value="{{ $section['barang'] ?? '' }}" class="pelindo-barang-select w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500" disabled>
                <option value="">-- Pilih Jenis Tagihan Terlebih Dahulu --</option>
            </select>
            @unless($isTemplate) @error('pelindo_sections.'.$index.'.barang') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror @endunless
        </div>
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-700">Jumlah <span class="text-red-500">*</span></label>
            <input type="number" name="pelindo_sections[{{ $index }}][jumlah]" value="{{ $section['jumlah'] ?? '' }}" min="1" step="1" class="pelindo-jumlah-input w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500" disabled>
            @unless($isTemplate) @error('pelindo_sections.'.$index.'.jumlah') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror @endunless
        </div>
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-700">Biaya <span class="text-red-500">*</span></label>
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                <input type="number" name="pelindo_sections[{{ $index }}][biaya]" value="{{ $section['biaya'] ?? '' }}" min="0" step="1" class="pelindo-biaya-input w-full rounded-lg border border-gray-300 py-2 pl-12 pr-4 focus:border-blue-500 focus:ring-2 focus:ring-blue-500" disabled>
            </div>
            <p class="mt-1 text-xs text-gray-500">Biaya per barang.</p>
            @unless($isTemplate) @error('pelindo_sections.'.$index.'.biaya') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror @endunless
        </div>
    </div>
</div>
