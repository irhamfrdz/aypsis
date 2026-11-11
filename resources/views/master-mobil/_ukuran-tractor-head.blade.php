<div id="ukuran-tractor-head-wrapper" @if(strtoupper(trim((string) old('jenis', $mobil->jenis ?? ''))) !== 'TRACTOR HEAD') hidden @endif>
    <label for="ukuran_tractor_head" class="block text-sm font-medium text-gray-700">
        Ukuran Tractor Head <span class="text-red-500">*</span>
    </label>
    <select name="ukuran_tractor_head" id="ukuran_tractor_head" class="{{ $inputClasses }}">
        <option value="">-- Pilih ukuran --</option>
        <option value="panjang" @selected(old('ukuran_tractor_head', $mobil->ukuran_tractor_head ?? null) === 'panjang')>Mobil Panjang</option>
        <option value="pendek" @selected(old('ukuran_tractor_head', $mobil->ukuran_tractor_head ?? null) === 'pendek')>Mobil Pendek</option>
    </select>
    @error('ukuran_tractor_head')
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

@push('scripts')
<script>
(() => {
    const initializeTractorHeadSize = () => {
        const jenis = document.getElementById('jenis');
        const wrapper = document.getElementById('ukuran-tractor-head-wrapper');
        const ukuran = document.getElementById('ukuran_tractor_head');
        if (!jenis || !wrapper || !ukuran) return;

        const toggleSize = () => {
            const isTractorHead = jenis.value.trim().toUpperCase() === 'TRACTOR HEAD';
            wrapper.hidden = !isTractorHead;
            ukuran.disabled = !isTractorHead;
            ukuran.required = isTractorHead;
            const buntutWrapper = document.getElementById('buntut-terpasang-wrapper');
            const buntut = document.getElementById('buntut_id');
            if (buntutWrapper && buntut) {
                buntutWrapper.hidden = !isTractorHead;
                buntut.disabled = !isTractorHead;
                buntut.required = isTractorHead && buntut.dataset.optional !== 'true';
            }
        };

        jenis.addEventListener('input', toggleSize);
        toggleSize();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeTractorHeadSize);
    } else {
        initializeTractorHeadSize();
    }
})();
</script>
@endpush
