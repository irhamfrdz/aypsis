<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div>
        <label for="nomor_tagihan" class="mb-1 block text-sm font-medium text-gray-700">Nomor Tagihan</label>
        <input id="nomor_tagihan" name="nomor_tagihan" value="{{ old('nomor_tagihan', $tagihan->nomor_tagihan) }}" maxlength="100" class="w-full rounded-lg border-gray-300 text-sm">
        @error('nomor_tagihan')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="tanggal_service" class="mb-1 block text-sm font-medium text-gray-700">Tanggal Service <span class="text-red-500">*</span></label>
        <input id="tanggal_service" type="date" name="tanggal_service" value="{{ old('tanggal_service', $tagihan->tanggal_service?->format('Y-m-d')) }}" required class="w-full rounded-lg border-gray-300 text-sm">
        @error('tanggal_service')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="nomor_polisi" class="mb-1 block text-sm font-medium text-gray-700">Nomor Polisi <span class="text-red-500">*</span></label>
        <input id="nomor_polisi" name="nomor_polisi" value="{{ old('nomor_polisi', $tagihan->nomor_polisi) }}" required maxlength="30" class="w-full rounded-lg border-gray-300 text-sm">
        @error('nomor_polisi')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="bengkel" class="mb-1 block text-sm font-medium text-gray-700">Bengkel / Vendor <span class="text-red-500">*</span></label>
        <input id="bengkel" name="bengkel" value="{{ old('bengkel', $tagihan->bengkel) }}" required maxlength="255" class="w-full rounded-lg border-gray-300 text-sm">
        @error('bengkel')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="biaya" class="mb-1 block text-sm font-medium text-gray-700">Biaya (Rp) <span class="text-red-500">*</span></label>
        <input id="biaya" type="number" name="biaya" value="{{ old('biaya', $tagihan->biaya ?? 0) }}" required min="0" step="0.01" class="w-full rounded-lg border-gray-300 text-sm">
        @error('biaya')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="status" class="mb-1 block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
        <select id="status" name="status" required class="w-full rounded-lg border-gray-300 text-sm">
            <option value="pending" @selected(old('status', $tagihan->status ?? 'pending') === 'pending')>Pending</option>
            <option value="paid" @selected(old('status', $tagihan->status ?? 'pending') === 'paid')>Dibayar</option>
            <option value="cancelled" @selected(old('status', $tagihan->status ?? 'pending') === 'cancelled')>Dibatalkan</option>
        </select>
        @error('status')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div class="md:col-span-2">
        <label for="rincian_service" class="mb-1 block text-sm font-medium text-gray-700">Rincian Service <span class="text-red-500">*</span></label>
        <textarea id="rincian_service" name="rincian_service" rows="3" maxlength="2000" required class="w-full rounded-lg border-gray-300 text-sm">{{ old('rincian_service', $tagihan->rincian_service) }}</textarea>
        @error('rincian_service')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div class="md:col-span-2">
        <label for="keterangan" class="mb-1 block text-sm font-medium text-gray-700">Keterangan</label>
        <textarea id="keterangan" name="keterangan" rows="2" maxlength="2000" class="w-full rounded-lg border-gray-300 text-sm">{{ old('keterangan', $tagihan->keterangan) }}</textarea>
        @error('keterangan')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
</div>
