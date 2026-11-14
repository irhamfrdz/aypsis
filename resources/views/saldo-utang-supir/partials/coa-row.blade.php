<div class="coa-row rounded-xl border border-gray-200 p-4 space-y-3">
    <div class="flex items-end gap-3">
        <div class="flex-1 min-w-0">
            <label for="coa-{{ $index }}" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Akun COA</label>
            <select id="coa-{{ $index }}" name="rincian_coa[{{ $index }}][coa_id]" required class="block w-full text-xs bg-gray-50 border border-gray-200 rounded-xl py-2.5 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">-- Pilih Akun COA --</option>
                @foreach($accounts as $account)
                    <option value="{{ $account->id }}" @selected(($row['coa_id'] ?? '') == $account->id)>{{ $account->nomor_akun }} — {{ $account->nama_akun }}</option>
                @endforeach
            </select>
        </div>
        <button type="button" class="remove-coa inline-flex items-center justify-center w-10 h-10 shrink-0 rounded-xl border border-rose-100 bg-rose-50 text-rose-600 hover:bg-rose-100 transition-colors duration-200" title="Hapus Akun COA" aria-label="Hapus Akun COA"><i class="fas fa-trash text-xs" aria-hidden="true"></i></button>
    </div>
    <div>
        <label for="amount-{{ $index }}" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nominal (Rupiah)</label>
        <div class="relative rounded-xl shadow-sm">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 text-xs font-bold">Rp</span>
            <input type="number" id="amount-{{ $index }}" name="rincian_coa[{{ $index }}][nominal]" value="{{ $row['nominal'] ?? '' }}" required min="0.01" max="9999999999999.99" step="0.01" placeholder="Contoh: 150000" class="coa-amount block w-full pl-9 pr-3 py-2.5 text-xs bg-gray-50 border border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
        </div>
    </div>
    <div>
        <label for="memo-{{ $index }}" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Memo (Opsional)</label>
        <input id="memo-{{ $index }}" name="rincian_coa[{{ $index }}][memo]" value="{{ $row['memo'] ?? '' }}" maxlength="1000" placeholder="Memo untuk akun ini..." class="block w-full text-xs bg-gray-50 border border-gray-200 rounded-xl py-2.5 px-3 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
</div>
