<div class="pelindo-section rounded-lg border-2 border-blue-200 bg-blue-50 p-4" data-section-index="{{ $index }}">
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
    <div class="pelindo-items mt-4 space-y-3">
        @foreach(($section['items'] ?? []) ?: [[]] as $itemIndex => $item)
            @include('biaya-kapal.create._pelindo-item', ['index' => $index, 'itemIndex' => $itemIndex, 'item' => $item, 'isTemplate' => $isTemplate])
        @endforeach
    </div>
    @unless($isTemplate) @error('pelindo_sections.'.$index.'.items') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror @endunless
    <button type="button" class="pelindo-add-item mt-3 inline-flex items-center gap-2 rounded-lg border border-blue-300 bg-white px-3 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-100">
        <i class="fas fa-plus" aria-hidden="true"></i> Tambah Jenis Tagihan dan Barang
    </button>
</div>
