<div class="pelindo-tagihan rounded-lg border border-blue-200 bg-blue-100/50 p-3" data-tagihan-index="{{ $tagihanIndex }}">
    <div class="mb-3 flex items-center justify-between">
        <h5 class="pelindo-tagihan-title text-sm font-semibold text-gray-800">Jenis Tagihan</h5>
        <button type="button" class="pelindo-remove-tagihan hidden text-sm font-medium text-red-600 hover:text-red-800">Hapus Jenis Tagihan</button>
    </div>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
        <label class="mb-2 block text-sm font-medium text-gray-700">Jenis Tagihan <span class="text-red-500">*</span></label>
        <select name="pelindo_sections[{{ $index }}][tagihans][{{ $tagihanIndex }}][jenis_tagihan]" class="pelindo-jenis-tagihan-select w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500" disabled>
            <option value="">-- Pilih Jenis Tagihan --</option>
            @foreach($pelindoJenisTagihanOptions as $kegiatan)
                <option value="{{ $kegiatan }}" @selected(($tagihan['jenis_tagihan'] ?? '') === $kegiatan)>{{ $kegiatan }}</option>
            @endforeach
        </select>
        @unless($isTemplate) @error('pelindo_sections.'.$index.'.tagihans.'.$tagihanIndex.'.jenis_tagihan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror @endunless
        </div>
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-700">Lokasi <span class="text-red-500">*</span></label>
            <select name="pelindo_sections[{{ $index }}][tagihans][{{ $tagihanIndex }}][lokasi]" class="pelindo-lokasi-select w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500" disabled>
                <option value="">-- Pilih Lokasi --</option>
                @foreach($pelindoLokasiOptions as $lokasi)
                    <option value="{{ $lokasi }}" @selected(($tagihan['lokasi'] ?? '') === $lokasi)>{{ $lokasi }}</option>
                @endforeach
            </select>
            @unless($isTemplate) @error('pelindo_sections.'.$index.'.tagihans.'.$tagihanIndex.'.lokasi') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror @endunless
        </div>
    </div>
    <div class="pelindo-barangs mt-3 space-y-3">
        @foreach(($tagihan['barangs'] ?? []) ?: [[]] as $barangIndex => $barang)
            @include('biaya-kapal.create._pelindo-barang', ['index' => $index, 'tagihanIndex' => $tagihanIndex, 'barangIndex' => $barangIndex, 'barang' => $barang, 'isTemplate' => $isTemplate])
        @endforeach
    </div>
    @unless($isTemplate) @error('pelindo_sections.'.$index.'.tagihans.'.$tagihanIndex.'.barangs') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror @endunless
    <button type="button" class="pelindo-add-barang mt-3 inline-flex items-center gap-2 rounded-lg border border-blue-300 bg-white px-3 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-100">
        <i class="fas fa-plus" aria-hidden="true"></i> Tambah Barang
    </button>
</div>
