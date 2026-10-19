@push('styles')
<style>
    .service-batam-form-grid { display: grid; grid-template-columns: minmax(0, 1fr); gap: 1.25rem 1.5rem; }
    .service-batam-field { min-width: 0; }
    .service-batam-field--wide { grid-column: 1 / -1; }
    .service-batam-label { display: block; margin-bottom: .5rem; color: #334155; font-size: .875rem; font-weight: 600; }
    .service-batam-label span { color: #dc2626; }
    .service-batam-control {
        display: block; width: 100%; min-height: 2.75rem; border: 1px solid #cbd5e1; border-radius: .625rem;
        background-color: #fff; padding: .625rem .75rem; color: #0f172a; font-size: .875rem; line-height: 1.25rem;
        box-shadow: 0 1px 2px rgb(15 23 42 / 5%); transition: border-color .15s ease, box-shadow .15s ease;
    }
    .service-batam-control:hover { border-color: #94a3b8; }
    .service-batam-control:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 3px rgb(37 99 235 / 15%); }
    .service-batam-control--readonly { background-color: #f8fafc; color: #475569; cursor: not-allowed; }
    .service-batam-control::placeholder { color: #94a3b8; }
    textarea.service-batam-control { min-height: 6rem; resize: vertical; }
    textarea.service-batam-control.service-batam-control--short { min-height: 4.5rem; }
    .service-batam-error { margin-top: .375rem; color: #dc2626; font-size: .75rem; }
    @media (min-width: 768px) { .service-batam-form-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
</style>
@endpush

<div class="service-batam-form-grid">
    <div class="service-batam-field">
        <label for="nomor_tagihan" class="service-batam-label">Nomor Tagihan</label>
        <input id="nomor_tagihan" name="nomor_tagihan" value="{{ $tagihan->nomor_tagihan ?: ($nomorTagihan ?? '') }}" readonly class="service-batam-control service-batam-control--readonly">
        <p class="mt-1 text-xs text-slate-500">Nomor dibuat otomatis saat tagihan disimpan.</p>
        @error('nomor_tagihan')<p class="service-batam-error">{{ $message }}</p>@enderror
    </div>
    <div class="service-batam-field">
        <label for="tanggal_service" class="service-batam-label">Tanggal Service <span>*</span></label>
        <input id="tanggal_service" type="date" name="tanggal_service" value="{{ old('tanggal_service', $tagihan->tanggal_service?->format('Y-m-d')) }}" required class="service-batam-control">
        @error('tanggal_service')<p class="service-batam-error">{{ $message }}</p>@enderror
    </div>
    <div class="service-batam-field">
        <label for="nomor_polisi" class="service-batam-label">Nomor Polisi <span>*</span></label>
        <input id="nomor_polisi" name="nomor_polisi" value="{{ old('nomor_polisi', $tagihan->nomor_polisi) }}" required maxlength="30" placeholder="Contoh: BP 1234 XX" class="service-batam-control">
        @error('nomor_polisi')<p class="service-batam-error">{{ $message }}</p>@enderror
    </div>
    <div class="service-batam-field">
        <label for="bengkel" class="service-batam-label">Bengkel / Vendor <span>*</span></label>
        <input id="bengkel" name="bengkel" value="{{ old('bengkel', $tagihan->bengkel) }}" required maxlength="255" placeholder="Nama bengkel atau vendor" class="service-batam-control">
        @error('bengkel')<p class="service-batam-error">{{ $message }}</p>@enderror
    </div>
    <div class="service-batam-field">
        <label for="biaya" class="service-batam-label">Biaya (Rp) <span>*</span></label>
        <input id="biaya" type="number" name="biaya" value="{{ old('biaya', $tagihan->biaya ?? 0) }}" required min="0" step="0.01" placeholder="0" class="service-batam-control">
        @error('biaya')<p class="service-batam-error">{{ $message }}</p>@enderror
    </div>
    <div class="service-batam-field">
        <label for="status" class="service-batam-label">Status <span>*</span></label>
        <select id="status" name="status" required class="service-batam-control">
            <option value="pending" @selected(old('status', $tagihan->status ?? 'pending') === 'pending')>Pending</option>
            <option value="paid" @selected(old('status', $tagihan->status ?? 'pending') === 'paid')>Dibayar</option>
            <option value="cancelled" @selected(old('status', $tagihan->status ?? 'pending') === 'cancelled')>Dibatalkan</option>
        </select>
        @error('status')<p class="service-batam-error">{{ $message }}</p>@enderror
    </div>
    <div class="service-batam-field service-batam-field--wide">
        <label for="rincian_service" class="service-batam-label">Rincian Service <span>*</span></label>
        <textarea id="rincian_service" name="rincian_service" rows="3" maxlength="2000" required placeholder="Jelaskan pekerjaan atau suku cadang yang diganti" class="service-batam-control">{{ old('rincian_service', $tagihan->rincian_service) }}</textarea>
        @error('rincian_service')<p class="service-batam-error">{{ $message }}</p>@enderror
    </div>
    <div class="service-batam-field service-batam-field--wide">
        <label for="keterangan" class="service-batam-label">Keterangan</label>
        <textarea id="keterangan" name="keterangan" rows="2" maxlength="2000" placeholder="Catatan tambahan (opsional)" class="service-batam-control service-batam-control--short">{{ old('keterangan', $tagihan->keterangan) }}</textarea>
        @error('keterangan')<p class="service-batam-error">{{ $message }}</p>@enderror
    </div>
</div>
