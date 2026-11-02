<div id="buntut-terpasang-wrapper" @if(strtoupper(trim((string) old('jenis', $mobil->jenis ?? ''))) !== 'TRACTOR HEAD') hidden @endif>
    <label for="buntut_id" class="block text-sm font-medium text-gray-700">
        Buntut Terpasang <span class="text-red-500">*</span>
    </label>
    <select name="buntut_id" id="buntut_id" class="{{ $inputClasses }}">
        <option value="">-- Pilih buntut --</option>
        @foreach($buntuts as $buntut)
            <option value="{{ $buntut->id }}" @selected((string) old('buntut_id', $mobil->buntut_id ?? '') === (string) $buntut->id)>
                {{ $buntut->nomor_polisi ?: ($buntut->no_kir ?: $buntut->kode_no) }} · {{ $buntut->kode_no }}{{ $buntut->nickname ? ' · '.$buntut->nickname : '' }}
            </option>
        @endforeach
    </select>
    @error('buntut_id')
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
