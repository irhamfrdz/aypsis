<div id="lolo_operator_fields" class="hidden rounded-lg border border-indigo-200 bg-indigo-50 p-4 space-y-3">
    <p class="text-xs font-semibold text-indigo-800 uppercase">Operator LOLO Kontainer</p>
    <div>
        <label for="tipe_operator_lolo" class="block text-xs font-semibold text-gray-700 mb-1">Jenis Operator LOLO <span class="text-red-500">*</span></label>
        <select name="tipe_operator_lolo" id="tipe_operator_lolo" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
            <option value="">Pilih jenis operator</option>
            <option value="AYP" @selected(old('tipe_operator_lolo', $langsir?->tipe_operator_lolo ?: ($langsir?->menggunakan_lolo ? 'AYP' : '')) === 'AYP')>Operator AYP</option>
            <option value="VENDOR" @selected(old('tipe_operator_lolo', $langsir?->tipe_operator_lolo) === 'VENDOR')>Vendor</option>
        </select>
        @error('tipe_operator_lolo')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div id="lolo_ayp_fields" class="space-y-3">
    @foreach([1, 2] as $number)
        @php($field = 'operator_lolo_'.$number.'_id')
        <div>
            <label for="{{ $field }}" class="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wider">
                Operator {{ $number }} <span class="text-red-500">*</span>
            </label>
            <select name="{{ $field }}" id="{{ $field }}" class="lolo-operator-select w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                <option value="">Pilih operator {{ $number }}</option>
                @foreach($operatorLolos as $operator)
                    <option value="{{ $operator->id }}" {{ old($field, $langsir?->$field) == $operator->id ? 'selected' : '' }}>
                        {{ $operator->nama_lengkap }}{{ $operator->pekerjaan ? ' ('.$operator->pekerjaan.')' : '' }}
                    </option>
                @endforeach
            </select>
            @error($field)<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
    @endforeach
    <p class="text-xs text-indigo-700">Pilih dua operator yang berbeda.</p>
    </div>
    <div id="lolo_vendor_fields" class="hidden">
        <label for="operator_lolo_vendor" class="block text-xs font-semibold text-gray-700 mb-1">Nama Vendor <span class="text-red-500">*</span></label>
        <input name="operator_lolo_vendor" id="operator_lolo_vendor" value="{{ old('operator_lolo_vendor', $langsir?->operator_lolo_vendor) }}" maxlength="255" placeholder="Ketik nama vendor" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
        @error('operator_lolo_vendor')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="biaya_lolo" class="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wider">Biaya LOLO</label>
        <div class="flex">
            <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">Rp</span>
            <input type="text" name="biaya_lolo" id="biaya_lolo" inputmode="numeric"
                   value="{{ old('biaya_lolo', $langsir?->biaya_lolo ? number_format($langsir->biaya_lolo, 0, ',', '.') : '0') }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-r-lg text-sm" placeholder="0">
        </div>
        <p id="biaya_lolo_hint" class="mt-1 text-xs text-indigo-700">Total biaya dua operator terisi otomatis berdasarkan ukuran kontainer.</p>
        @error('biaya_lolo')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
</div>
