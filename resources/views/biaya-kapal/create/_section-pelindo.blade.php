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
        @foreach(old('pelindo_sections', [[]]) as $index => $section)
            @include('biaya-kapal.create._pelindo-card', ['index' => $index, 'section' => $section, 'isTemplate' => false])
        @endforeach
    </div>
    <button type="button" class="pelindo-add-kapal mt-4 inline-flex w-full items-center justify-center gap-2 rounded-lg border-2 border-dashed border-blue-300 py-2 font-medium text-blue-700 hover:bg-blue-50">
        <i class="fas fa-plus-circle" aria-hidden="true"></i> Tambah Kapal Lainnya
    </button>
    <template id="pelindo_card_template">
        @include('biaya-kapal.create._pelindo-card', ['index' => '__INDEX__', 'section' => [], 'isTemplate' => true])
    </template>
</div>
