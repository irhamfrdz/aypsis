<div id="lolo_operator_fields" class="hidden rounded-lg border border-indigo-200 bg-indigo-50 p-4 space-y-3">
    <p class="text-xs font-semibold text-indigo-800 uppercase">Operator LOLO Kontainer</p>
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
