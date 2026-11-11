<div id="buntut-terpasang-wrapper" @if(strtoupper(trim((string) old('jenis', $mobil->jenis ?? ''))) !== 'TRACTOR HEAD') hidden @endif>
    <label for="buntut_id" class="block text-sm font-medium text-gray-700">
        Buntut Terpasang @unless($buntutOptional ?? false)<span class="text-red-500">*</span>@endunless
    </label>
    <select name="buntut_id" id="buntut_id" data-optional="{{ ($buntutOptional ?? false) ? 'true' : 'false' }}" class="{{ $inputClasses }}">
        <option value="">-- Pilih buntut --</option>
        @foreach($buntuts as $buntut)
            <option value="{{ $buntut->id }}" data-search="{{ implode(' ', [$buntut->nomor_polisi, $buntut->no_kir, $buntut->kode_no, $buntut->nickname]) }}" @selected((string) old('buntut_id', $mobil->buntut_id ?? '') === (string) $buntut->id)>
                {{ $buntut->nomor_polisi ?: ($buntut->no_kir ?: $buntut->kode_no) }} · {{ $buntut->kode_no }}{{ $buntut->nickname ? ' · '.$buntut->nickname : '' }}
            </option>
        @endforeach
    </select>
    <p class="mt-1 text-xs text-gray-500">Ketik nomor Plat/KIR, kode kendaraan, atau nickname untuk mencari buntut.</p>
    @error('buntut_id')
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

@push('scripts')
<style>
    #buntut-terpasang-wrapper .select2-container .select2-selection--single {
        min-height: 42px;
        border-color: #d1d5db;
        border-radius: 0.5rem;
    }
    #buntut-terpasang-wrapper .select2-selection__rendered { line-height: 40px; }
    #buntut-terpasang-wrapper .select2-selection__arrow { height: 40px; }
</style>
<script>
$(function () {
    const select = $('#buntut_id');
    if (!select.length || !$.fn.select2) return;
    const normalize = value => String(value || '').toUpperCase().replace(/[^A-Z0-9]/g, '');
    select.select2({
        width: '100%',
        placeholder: '-- Pilih buntut --',
        minimumResultsForSearch: 0,
        matcher: function (params, data) {
            const term = normalize(params.term);
            if (!term) return data;
            if (!data.id) return null;
            return normalize(data.element?.dataset.search || data.text).includes(term) ? data : null;
        },
        language: {
            noResults: () => 'Buntut tidak ditemukan',
            searching: () => 'Mencari buntut...',
        },
    });
    select.on('select2:open', function () {
        select.data('select2').$dropdown.find('.select2-search__field').trigger('focus');
    });
    select.on('invalid', function (event) {
        if (this.disabled) return;
        event.preventDefault();
        select.select2('open');
    });
});
</script>
@endpush
