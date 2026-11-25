<div class="formula-employee-scope rounded-lg border border-gray-200 bg-white p-3 mb-4 space-y-3">
    <label class="block text-xs font-semibold text-gray-600">Berlaku untuk
        <select class="formula-scope-mode block w-full mt-1 rounded-lg border-gray-300 text-sm" onchange="refreshFormulaEmployees(this.closest('.bpjs-row') || document.getElementById('editForm'))">
            <option value="all">Seluruh anggota Group</option>
            <option value="selected">Karyawan tertentu</option>
        </select>
    </label>
    <input class="formula-targets" type="hidden" name="{{ $editing ? 'employee_targets' : 'employee_targets[]' }}" value="[]">
    <div class="formula-employee-picker hidden space-y-2">
        <input type="search" class="formula-employee-search w-full rounded-lg border-gray-300 text-sm" placeholder="Cari nama / NIK" aria-label="Cari karyawan dalam Group" oninput="refreshFormulaEmployees(this.closest('.bpjs-row') || document.getElementById('editForm'))">
        <p class="formula-employee-count text-xs text-gray-500" role="status"></p>
        <div class="formula-employee-list max-h-48 overflow-y-auto space-y-2"></div>
        <p class="text-xs text-gray-500">Karyawan mengikuti Group, tipe dan cabang yang dipilih. Rumus khusus didahulukan dibanding rumus Group.</p>
    </div>
</div>
