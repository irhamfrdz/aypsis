<div id="pelindo_wrapper" class="md:col-span-2 hidden">
    <div class="mb-4 flex items-center justify-between gap-4">
        <div>
            <h3 class="text-base font-semibold text-gray-800">Detail Tagihan Pelindo <span class="text-red-500">*</span></h3>
            <p class="text-sm text-gray-600">Pilih kapal dan voyage yang tercatat pada manifest.</p>
        </div>
        <button type="button" class="pelindo-add-kapal inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
            <i class="fas fa-plus" aria-hidden="true"></i> Tambah Kapal
        </button>
    </div>
    <div id="pelindo_sections_container" class="space-y-4">
        @foreach(old('pelindo_sections', []) ?: [[]] as $index => $section)
            @include('biaya-kapal.create._pelindo-card', ['index' => $index, 'section' => $section, 'isTemplate' => false])
        @endforeach
    </div>
    <button type="button" class="pelindo-add-kapal mt-4 inline-flex w-full items-center justify-center gap-2 rounded-lg border-2 border-dashed border-blue-300 py-2 font-medium text-blue-700 hover:bg-blue-50">
        <i class="fas fa-plus-circle" aria-hidden="true"></i> Tambah Kapal Lainnya
    </button>
    <div class="mt-4 grid grid-cols-1 gap-4 rounded-lg border border-blue-200 bg-blue-50 p-4 md:grid-cols-2">
        <div>
            <label for="pelindo_biaya_admin_nota" class="mb-2 block text-sm font-medium text-gray-700">Biaya Admin Nota</label>
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                <input type="text" id="pelindo_biaya_admin_nota" name="pelindo_biaya_admin_nota" value="10.000" readonly class="pelindo-extra-fee-input w-full rounded-lg border border-gray-300 bg-gray-50 py-2 pl-12 pr-4">
            </div>
            @error('pelindo_biaya_admin_nota') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="pelindo_biaya_kebersihan" class="mb-2 block text-sm font-medium text-gray-700">Biaya Kebersihan</label>
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                <input type="text" id="pelindo_biaya_kebersihan" name="pelindo_biaya_kebersihan" value="" readonly placeholder="Pilih lokasi tagihan" class="pelindo-extra-fee-input w-full rounded-lg border border-gray-300 bg-gray-50 py-2 pl-12 pr-4">
            </div>
            <p id="pelindo_kebersihan_hint" class="mt-1 text-xs text-gray-500">Terisi otomatis dari Pricelist Pelindo sesuai lokasi tagihan.</p>
            @error('pelindo_biaya_kebersihan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>
    <template id="pelindo_card_template">
        @include('biaya-kapal.create._pelindo-card', ['index' => '__INDEX__', 'section' => [], 'isTemplate' => true])
    </template>
    <template id="pelindo_tagihan_template">
        @include('biaya-kapal.create._pelindo-tagihan', ['index' => '__SECTION__', 'tagihanIndex' => '__TAGIHAN__', 'tagihan' => [], 'isTemplate' => true])
    </template>
    <template id="pelindo_barang_template">
        @include('biaya-kapal.create._pelindo-barang', ['index' => '__SECTION__', 'tagihanIndex' => '__TAGIHAN__', 'barangIndex' => '__BARANG__', 'barang' => [], 'isTemplate' => true])
    </template>
</div>
