<div id="lolo_operator_fields" class="hidden md:col-span-2 rounded-lg border border-indigo-200 bg-indigo-50 p-4">
    <label for="tipe_operator_lolo" class="block text-sm font-medium text-gray-700 mb-2">Operator LOLO <span class="text-red-600">*</span></label>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <select name="tipe_operator_lolo" id="tipe_operator_lolo" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
            <option value="">-- Pilih Jenis Operator --</option>
            <option value="AYP" {{ old('tipe_operator_lolo', $item?->tipe_operator_lolo) === 'AYP' ? 'selected' : '' }}>Operator AYP</option>
            <option value="VENDOR" {{ old('tipe_operator_lolo', $item?->tipe_operator_lolo) === 'VENDOR' ? 'selected' : '' }}>Vendor</option>
        </select>
        <div id="lolo_ayp_fields" class="hidden">
            <select name="operator_lolo_karyawan_id" id="operator_lolo_karyawan_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                <option value="">-- Pilih Nama Operator --</option>
                @foreach($operatorLolos as $operator)
                    <option value="{{ $operator->id }}" {{ old('operator_lolo_karyawan_id', $item?->operator_lolo_karyawan_id) == $operator->id ? 'selected' : '' }}>
                        {{ $operator->nama_lengkap }}{{ $operator->pekerjaan ? ' ('.$operator->pekerjaan.')' : '' }}
                    </option>
                @endforeach
            </select>
        </div>
        <div id="lolo_vendor_fields" class="hidden">
            <input type="text" name="operator_lolo_vendor" id="operator_lolo_vendor" value="{{ old('operator_lolo_vendor', $item?->operator_lolo_vendor) }}" placeholder="Nama vendor operator" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
        </div>
    </div>
    <div class="mt-4">
        <label for="biaya_lolo" class="block text-sm font-medium text-gray-700 mb-1">Biaya LOLO</label>
        <div class="flex">
            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">Rp</span>
            <input type="text" name="biaya_lolo" id="biaya_lolo" inputmode="numeric"
                   value="{{ old('biaya_lolo', $item?->biaya_lolo ? number_format($item->biaya_lolo, 0, ',', '.') : '0') }}"
                   class="currency w-full px-3 py-2 border border-gray-300 rounded-r-lg text-sm" placeholder="0">
        </div>
        <p id="biaya_lolo_hint" class="mt-1 text-xs text-gray-500">Tarif terisi otomatis setelah ukuran kontainer dipilih.</p>
        @error('biaya_lolo')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
</div>
