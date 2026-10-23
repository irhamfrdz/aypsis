@php($entry = $jurnalOppOpt ?? null)

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <label for="tanggal" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal <span class="text-red-500">*</span></label>
        <input type="date" id="tanggal" name="tanggal" required
               value="{{ old('tanggal', $entry?->tanggal?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
               class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
        @error('tanggal')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="nomor_referensi" class="block text-sm font-semibold text-gray-700 mb-2">Nomor Referensi</label>
        <input type="text" id="nomor_referensi" name="nomor_referensi" maxlength="100"
               value="{{ old('nomor_referensi', $entry?->nomor_referensi) }}" placeholder="Opsional"
               class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
        @error('nomor_referensi')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="saldo" class="block text-sm font-semibold text-gray-700 mb-2">Saldo (Penambahan)</label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">Rp</span>
            <input type="number" id="saldo" name="saldo" min="0" step="0.01"
                   value="{{ old('saldo', $entry?->saldo ?? 0) }}"
                   class="w-full pl-10 rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
        </div>
        <p class="mt-1 text-xs text-gray-500">Menambah saldo berjalan.</p>
        @error('saldo')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="kredit" class="block text-sm font-semibold text-gray-700 mb-2">Kredit (Pengurangan)</label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">Rp</span>
            <input type="number" id="kredit" name="kredit" min="0" step="0.01"
                   value="{{ old('kredit', $entry?->kredit ?? 0) }}"
                   class="w-full pl-10 rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
        </div>
        <p class="mt-1 text-xs text-gray-500">Mengurangi saldo berjalan.</p>
        @error('kredit')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="md:col-span-2">
        <label for="keterangan" class="block text-sm font-semibold text-gray-700 mb-2">Keterangan <span class="text-red-500">*</span></label>
        <textarea id="keterangan" name="keterangan" rows="4" required maxlength="2000"
                  class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                  placeholder="Tuliskan keterangan transaksi">{{ old('keterangan', $entry?->keterangan) }}</textarea>
        @error('keterangan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>

<div class="mt-8 flex justify-end gap-3 border-t border-gray-100 pt-6">
    <a href="{{ route('jurnal-opp-opt.index') }}" class="px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50">Batal</a>
    <button type="submit" class="px-5 py-2.5 rounded-lg bg-emerald-600 text-sm font-semibold text-white hover:bg-emerald-700 shadow-sm">
        {{ $submitLabel }}
    </button>
</div>
