<div class="pelindo-barang-row rounded-lg border border-blue-200 bg-white p-3">
    <div class="mb-3 flex items-center justify-between">
        <h5 class="pelindo-barang-title text-sm font-semibold text-gray-700">Barang</h5>
        <button type="button" class="pelindo-remove-barang hidden text-sm font-medium text-red-600 hover:text-red-800">Hapus</button>
    </div>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-700">Barang <span class="text-red-500">*</span></label>
            <select name="pelindo_sections[{{ $index }}][tagihans][{{ $tagihanIndex }}][barangs][{{ $barangIndex }}][barang]" data-old-value="{{ $barang['barang'] ?? '' }}" class="pelindo-barang-select w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500" disabled>
                <option value="">-- Pilih Jenis Tagihan Terlebih Dahulu --</option>
            </select>
            @unless($isTemplate) @error('pelindo_sections.'.$index.'.tagihans.'.$tagihanIndex.'.barangs.'.$barangIndex.'.barang') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror @endunless
        </div>
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-700">Jumlah <span class="text-red-500">*</span></label>
            <input type="number" name="pelindo_sections[{{ $index }}][tagihans][{{ $tagihanIndex }}][barangs][{{ $barangIndex }}][jumlah]" value="{{ $barang['jumlah'] ?? '' }}" min="0.001" step="1" class="pelindo-jumlah-input w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500" disabled>
            <p class="pelindo-jumlah-hint mt-1 text-xs text-gray-500">Terisi otomatis dari manifest kapal dan voyage; dapat disesuaikan.</p>
            @unless($isTemplate) @error('pelindo_sections.'.$index.'.tagihans.'.$tagihanIndex.'.barangs.'.$barangIndex.'.jumlah') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror @endunless
        </div>
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-700">Biaya <span class="text-red-500">*</span></label>
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                <input type="text" name="pelindo_sections[{{ $index }}][tagihans][{{ $tagihanIndex }}][barangs][{{ $barangIndex }}][biaya]" value="{{ $barang['biaya'] ?? '' }}" readonly class="pelindo-biaya-input w-full rounded-lg border border-gray-300 bg-gray-50 py-2 pl-12 pr-4 focus:border-blue-500 focus:ring-2 focus:ring-blue-500" disabled>
            </div>
            <p class="pelindo-biaya-hint mt-1 text-xs text-gray-500">Tarif Pricelist Pelindo × Jumlah.</p>
            @unless($isTemplate) @error('pelindo_sections.'.$index.'.tagihans.'.$tagihanIndex.'.barangs.'.$barangIndex.'.biaya') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror @endunless
        </div>
    </div>
</div>
