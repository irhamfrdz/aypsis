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
    .service-batam-select2 + .select2-container { width: 100% !important; }
    .service-batam-select2 + .select2-container .select2-selection--single { height: 2.75rem; border: 1px solid #cbd5e1; border-radius: .625rem; }
    .service-batam-select2 + .select2-container .select2-selection__rendered { line-height: 2.625rem; padding-left: .75rem; color: #0f172a; font-size: .875rem; }
    .service-batam-select2 + .select2-container .select2-selection__arrow { height: 2.625rem; right: .4rem; }
    textarea.service-batam-control { min-height: 6rem; resize: vertical; }
    textarea.service-batam-control.service-batam-control--short { min-height: 4.5rem; }
    .service-batam-error { margin-top: .375rem; color: #dc2626; font-size: .75rem; }
    @media (min-width: 768px) { .service-batam-form-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
</style>
@endpush

<div class="service-batam-form-grid">
    <div class="service-batam-field service-batam-field--wide">
        <label for="jenis_tagihan" class="service-batam-label">Jenis Tagihan <span>*</span></label>
        <select id="jenis_tagihan" name="jenis_tagihan" required class="service-batam-control">
            <option value="kendaraan" @selected(old('jenis_tagihan', $tagihan->jenis_tagihan ?? 'kendaraan') === 'kendaraan')>Tagihan Kendaraan</option>
            <option value="lain_lain" @selected(old('jenis_tagihan', $tagihan->jenis_tagihan ?? 'kendaraan') === 'lain_lain')>Tagihan Lain Lain</option>
        </select>
        @error('jenis_tagihan')<p class="service-batam-error">{{ $message }}</p>@enderror
    </div>
    <div class="service-batam-field">
        <label for="nomor_tagihan" class="service-batam-label">Nomor Tagihan</label>
        <input id="nomor_tagihan" name="nomor_tagihan" value="{{ $tagihan->nomor_tagihan ?: ($nomorTagihan ?? '') }}" readonly class="service-batam-control service-batam-control--readonly">
        <p class="mt-1 text-xs text-slate-500">Nomor dibuat otomatis saat tagihan disimpan.</p>
        @error('nomor_tagihan')<p class="service-batam-error">{{ $message }}</p>@enderror
    </div>
    <div class="service-batam-field">
        <label id="tanggal_tagihan_label" for="tanggal_service" class="service-batam-label">Tanggal Service <span>*</span></label>
        <input id="tanggal_service" type="date" name="tanggal_service" value="{{ old('tanggal_service', $tagihan->tanggal_service?->format('Y-m-d')) }}" required class="service-batam-control">
        @error('tanggal_service')<p class="service-batam-error">{{ $message }}</p>@enderror
    </div>
    <div id="mobil_field" class="service-batam-field">
        <label for="mobil_id" class="service-batam-label">Nomor Polisi / KIR <span>*</span></label>
        <select id="mobil_id" name="mobil_id" required class="service-batam-control service-batam-select2">
            <option value="">Pilih nomor polisi atau KIR</option>
            @foreach ($mobils as $mobil)
                @php
                    $mobilNumber = filled(trim((string) $mobil->nomor_polisi)) ? trim((string) $mobil->nomor_polisi) : trim((string) $mobil->no_kir);
                @endphp
                <option value="{{ $mobil->id }}" @selected((string) old('mobil_id', $selectedMobilId ?? $tagihan->mobil_id) === (string) $mobil->id)>
                    {{ $mobilNumber }}{{ $mobil->nickname ? ' — '.$mobil->nickname : '' }}{{ $mobil->kode_no ? ' ('.$mobil->kode_no.')' : '' }}
                </option>
            @endforeach
        </select>
        @error('mobil_id')<p class="service-batam-error">{{ $message }}</p>@enderror
    </div>
    <div class="service-batam-field">
        <label id="bengkel_label" for="bengkel" class="service-batam-label">Bengkel / Vendor <span>*</span></label>
        <input id="bengkel" name="bengkel" value="{{ old('bengkel', $tagihan->bengkel) }}" required maxlength="255" placeholder="Nama bengkel atau vendor" class="service-batam-control">
        @error('bengkel')<p class="service-batam-error">{{ $message }}</p>@enderror
    </div>
    <div class="service-batam-field">
        <label for="biaya" class="service-batam-label">Biaya sebelum PPh (Rp) <span>*</span></label>
        <input id="biaya" type="number" name="biaya" value="{{ old('biaya', $tagihan->biaya ?? 0) }}" required min="0" step="0.01" placeholder="0" class="service-batam-control">
        @error('biaya')<p class="service-batam-error">{{ $message }}</p>@enderror
    </div>
    <div class="service-batam-field">
        <label for="pph_rate" class="service-batam-label">PPh <span>*</span></label>
        <select id="pph_rate" name="pph_rate" required class="service-batam-control">
            <option value="0" @selected((string) old('pph_rate', $tagihan->pph_rate ?? '0') === '0')>Tidak menggunakan PPh (0%)</option>
            <option value="2" @selected((string) old('pph_rate', $tagihan->pph_rate ?? '') === '2')>2%</option>
            <option value="2.5" @selected((string) old('pph_rate', $tagihan->pph_rate ?? '') === '2.5')>2,5%</option>
        </select>
        @error('pph_rate')<p class="service-batam-error">{{ $message }}</p>@enderror
    </div>
    <div class="service-batam-field">
        <label for="total_biaya_preview" class="service-batam-label">Total biaya setelah PPh (Rp)</label>
        <input id="total_biaya_preview" value="{{ old('total_biaya', $tagihan->total_biaya ?? $tagihan->biaya ?? 0) }}" readonly class="service-batam-control service-batam-control--readonly">
    </div>
    <div class="service-batam-field service-batam-field--wide">
        <label id="rincian_label" for="rincian_service" class="service-batam-label">Rincian Service <span>*</span></label>
        <textarea id="rincian_service" name="rincian_service" rows="3" maxlength="2000" required placeholder="Jelaskan pekerjaan atau suku cadang yang diganti" class="service-batam-control">{{ old('rincian_service', $tagihan->rincian_service) }}</textarea>
        @error('rincian_service')<p class="service-batam-error">{{ $message }}</p>@enderror
    </div>
    <div class="service-batam-field service-batam-field--wide">
        <label for="keterangan" class="service-batam-label">Keterangan</label>
        <textarea id="keterangan" name="keterangan" rows="2" maxlength="2000" placeholder="Catatan tambahan (opsional)" class="service-batam-control service-batam-control--short">{{ old('keterangan', $tagihan->keterangan) }}</textarea>
        @error('keterangan')<p class="service-batam-error">{{ $message }}</p>@enderror
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (window.jQuery && jQuery.fn.select2) {
        jQuery('#mobil_id').select2({ placeholder: 'Cari nomor polisi atau KIR', allowClear: true, width: '100%' });
    }
    const biaya = document.getElementById('biaya');
    const pph = document.getElementById('pph_rate');
    const total = document.getElementById('total_biaya_preview');
    const jenis = document.getElementById('jenis_tagihan');
    const mobilField = document.getElementById('mobil_field');
    const mobil = document.getElementById('mobil_id');
    const tanggalLabel = document.getElementById('tanggal_tagihan_label');
    const bengkelLabel = document.getElementById('bengkel_label');
    const rincianLabel = document.getElementById('rincian_label');
    const updateBillType = () => {
        const isVehicle = jenis.value === 'kendaraan';
        mobilField.classList.toggle('hidden', !isVehicle);
        mobil.required = isVehicle;
        tanggalLabel.innerHTML = `${isVehicle ? 'Tanggal Service' : 'Tanggal Tagihan'} <span>*</span>`;
        bengkelLabel.innerHTML = `${isVehicle ? 'Bengkel' : 'Vendor / Penerima'} <span>*</span>`;
        rincianLabel.innerHTML = `${isVehicle ? 'Rincian Service' : 'Rincian Tagihan'} <span>*</span>`;
    };
    const updateTotal = () => {
        total.value = ((Number.parseFloat(biaya.value) || 0) * (1 + (Number.parseFloat(pph.value) || 0) / 100)).toFixed(2);
    };
    biaya.addEventListener('input', updateTotal);
    pph.addEventListener('change', updateTotal);
    jenis.addEventListener('change', updateBillType);
    updateBillType();
    updateTotal();
});
</script>
