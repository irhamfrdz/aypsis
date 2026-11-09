<div class="pelindo-item rounded-lg border border-blue-200 bg-white p-3">
    <div class="mb-3 flex items-center justify-between">
        <h5 class="pelindo-item-title text-sm font-semibold text-gray-700">Jenis Tagihan dan Barang</h5>
        <button type="button" class="pelindo-remove-item hidden text-sm font-medium text-red-600 hover:text-red-800">Hapus</button>
    </div>
    <div>
        <label class="mb-2 block text-sm font-medium text-gray-700">Jenis Tagihan <span class="text-red-500">*</span></label>
        <select name="pelindo_sections[{{ $index }}][items][{{ $itemIndex }}][jenis_tagihan]" class="pelindo-jenis-tagihan-select w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500" disabled>
            <option value="">-- Pilih Jenis Tagihan --</option>
            @foreach($pelindoJenisTagihanOptions as $kegiatan)
                <option value="{{ $kegiatan }}" @selected(($item['jenis_tagihan'] ?? '') === $kegiatan)>{{ $kegiatan }}</option>
            @endforeach
        </select>
        @unless($isTemplate) @error('pelindo_sections.'.$index.'.items.'.$itemIndex.'.jenis_tagihan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror @endunless
    </div>
    <div class="mt-3 grid grid-cols-1 gap-4 md:grid-cols-3">
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-700">Barang <span class="text-red-500">*</span></label>
            <select name="pelindo_sections[{{ $index }}][items][{{ $itemIndex }}][barang]" data-old-value="{{ $item['barang'] ?? '' }}" class="pelindo-barang-select w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500" disabled>
                <option value="">-- Pilih Jenis Tagihan Terlebih Dahulu --</option>
            </select>
            @unless($isTemplate) @error('pelindo_sections.'.$index.'.items.'.$itemIndex.'.barang') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror @endunless
        </div>
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-700">Jumlah <span class="text-red-500">*</span></label>
            <input type="number" name="pelindo_sections[{{ $index }}][items][{{ $itemIndex }}][jumlah]" value="{{ $item['jumlah'] ?? '' }}" min="1" step="1" class="pelindo-jumlah-input w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500" disabled>
            @unless($isTemplate) @error('pelindo_sections.'.$index.'.items.'.$itemIndex.'.jumlah') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror @endunless
        </div>
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-700">Biaya <span class="text-red-500">*</span></label>
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                <input type="number" name="pelindo_sections[{{ $index }}][items][{{ $itemIndex }}][biaya]" value="{{ $item['biaya'] ?? '' }}" min="0" step="1" class="pelindo-biaya-input w-full rounded-lg border border-gray-300 py-2 pl-12 pr-4 focus:border-blue-500 focus:ring-2 focus:ring-blue-500" disabled>
            </div>
            <p class="mt-1 text-xs text-gray-500">Biaya per barang.</p>
            @unless($isTemplate) @error('pelindo_sections.'.$index.'.items.'.$itemIndex.'.biaya') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror @endunless
        </div>
    </div>
</div>
