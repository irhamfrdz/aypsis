@extends('layouts.app')

@section('title', $record->exists ? 'Ubah Tanggal Dokumen' : 'Pengisian Tanggal Dokumen')

@section('content')
@php
    $dateFields = [
        'tanggal_terima_dokumen_jkt_customer' => 'Tanggal Terima Dokumen dari JKT/ CUSTOMER',
        'tanggal_terima_dokumen_packinglist' => 'Tanggal Packing List',
        'tanggal_dokumen_invoice_faktur_pajak' => 'Tanggal Dokumen Invoice & Faktur Pajak',
        'tanggal_kirim_dokumen_ppjk' => 'Tanggal Kirim Dokumen PPJK',
        'tanggal_terbit_sppb_ppftz' => 'Tanggal Penerbit SPPB & PPFTZ',
        'tanggal_terima_dokumen_dari_ppjk' => 'Tanggal Terima Dokumen Dari PPJK',
        'tanggal_keluar_gate_tujuan' => 'Tanggal Keluar Gate Tujuan',
        'tanggal_keluar_gudang_ayp' => 'Tanggal Keluar Dari Gudang AYP',
        'tanggal_tiba_gudang_customer' => 'Tanggal Tiba Di Gudang Customer',
        'tanggal_keluar_gudang_customer' => 'Tanggal Keluar Dari Gudang Customer',
        'tanggal_masuk_gate_tujuan' => 'Tanggal Masuk Gate Tujuan',
        'tanggal_kirim_dokumen_jakarta' => 'Tanggal Kirim Dokumen Ke JKT',
        'tanggal_terima_sj_cabang' => 'Tanggal SJ Dari Cabang',
        'tanggal_terima_ppftz_cabang' => 'Tanggal Terima PPFTZ Dari Cabang',
    ];
@endphp
<style>
    .document-picker { min-width: 0; }
    .document-picker .select2-container { width: 100% !important; }
    .document-picker .select2-selection--multiple {
        min-height: 48px; padding: 6px 10px; border: 1px solid #cbd5e1; border-radius: 10px;
        background: #fff; box-shadow: 0 1px 2px rgb(15 23 42 / .04); transition: border-color .15s, box-shadow .15s;
    }
    .document-picker .select2-container--focus .select2-selection--multiple,
    .document-picker .select2-container--open .select2-selection--multiple {
        border-color: #3b82f6; box-shadow: 0 0 0 3px rgb(59 130 246 / .12);
    }
    .document-picker .select2-container--default .select2-selection--multiple .select2-selection__rendered {
        display: inline; margin: 0; padding: 0; list-style: none;
    }
    .document-picker .select2-container--default .select2-selection--multiple .select2-selection__choice {
        position: relative; display: inline-flex; align-items: center; float: none; max-width: 100%;
        margin: 4px 6px 4px 0; padding: 6px 32px 6px 10px; border: 1px solid #bfdbfe;
        border-radius: 7px; background: #eff6ff; color: #1e40af; font-size: 13px; line-height: 20px;
        vertical-align: middle; white-space: normal; overflow-wrap: anywhere;
    }
    .document-picker .select2-selection__choice__display { padding: 0 !important; }
    .document-picker .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        position: absolute; inset: 0 0 0 auto; width: 27px; margin: 0; padding: 0;
        border: 0; border-left: 1px solid #bfdbfe; border-radius: 0 6px 6px 0;
        color: #3b82f6; font-size: 18px; font-weight: 400; background: transparent;
    }
    .document-picker .select2-selection__choice__remove:hover,
    .document-picker .select2-selection__choice__remove:focus { background: #dbeafe !important; color: #1e40af !important; }
    .document-picker .select2-search--inline { display: inline-block; max-width: 100%; vertical-align: middle; }
    .document-picker .select2-search--inline .select2-search__field {
        min-width: 180px; max-width: 100%; height: 28px; margin: 4px 0; padding: 3px 2px;
        color: #334155; font: inherit; font-size: 13px; line-height: 22px;
    }
    .document-picker .select2-search__field::placeholder { color: #94a3b8; opacity: 1; }
    .document-picker .select2-container--disabled .select2-selection--multiple { background: #f8fafc; border-color: #e2e8f0; cursor: not-allowed; }
    .document-picker-dropdown { border: 1px solid #cbd5e1; border-radius: 10px; overflow: hidden; box-shadow: 0 12px 28px rgb(15 23 42 / .12); }
    .document-picker-dropdown .select2-results__options { max-height: 280px !important; padding: 6px; }
    .document-picker-dropdown .select2-results__option { padding: 10px 12px; border-radius: 6px; font-size: 13px; color: #334155; overflow-wrap: anywhere; }
    .document-picker-dropdown .select2-results__option--selectable::before {
        content: ''; display: inline-block; width: 16px; height: 16px; margin-right: 10px;
        border: 1px solid #cbd5e1; border-radius: 4px; vertical-align: -3px; background: #fff;
    }
    .document-picker-dropdown .select2-results__option--selected { background: #eff6ff; color: #1e40af; }
    .document-picker-dropdown .select2-results__option--selected::before { content: '\2713'; background: #2563eb; border-color: #2563eb; color: #fff; text-align: center; font-size: 12px; line-height: 14px; }
    .document-picker-dropdown .select2-results__option--highlighted.select2-results__option--selectable { background: #f1f5f9; color: #0f172a; }
    .document-picker-help { margin-top: 8px; color: #64748b; font-size: 12px; line-height: 1.6; }
    .document-picker-count { flex-shrink: 0; padding: 2px 9px; border-radius: 20px; background: #eff6ff; color: #2563eb; font-size: 12px; font-weight: 600; }
</style>
<div class="container mx-auto max-w-6xl px-4 py-6 lg:px-6">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="mb-1 text-xs font-semibold uppercase tracking-wider text-blue-700">Administrasi dokumen</p>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">{{ $record->exists ? 'Ubah Tanggal Dokumen' : 'Pengisian Tanggal Dokumen' }}</h1>
            <p class="mt-1 text-sm text-gray-500">Catat proses dokumen berdasarkan kapal, voyage, dan nomor dokumen.</p>
        </div>
        <a href="{{ route('pengisian-tanggal-dokumen.index') }}" class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            <i class="fa-solid fa-arrow-left text-xs" aria-hidden="true"></i> Kembali
        </a>
    </div>

    @if($errors->any())
        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <ul class="list-disc list-inside">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ $formAction }}" class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        @csrf
        @if($formMethod !== 'POST') @method($formMethod) @endif
        <section class="border-b border-gray-200 p-5 sm:p-7">
            <label for="cabang_id" class="mb-2 block font-semibold text-gray-900"><i class="fa-solid fa-building mr-1.5 text-gray-400" aria-hidden="true"></i>1. Cabang yang Menginput <span class="text-red-500">*</span></label>
            <p class="mb-3 text-sm text-gray-500">Pilih cabang terlebih dahulu untuk membuka seluruh kolom data dokumen.</p>
            <select id="cabang_id" name="cabang_id" required class="w-full rounded-lg border-gray-300">
                <option value="">-- Pilih Cabang --</option>
                @foreach($cabangs as $cabang)
                    <option value="{{ $cabang->id }}" data-nama="{{ $cabang->nama_cabang }}" @selected(old('cabang_id', $record->cabang_id) == $cabang->id)>{{ $cabang->nama_cabang }}</option>
                @endforeach
            </select>
        </section>
        <div id="cabang-notice" role="alert" tabindex="-1" hidden class="mx-5 mt-4 rounded-lg border border-amber-300 bg-amber-50 p-4 text-sm font-medium text-amber-800 sm:mx-7">
            Pilih Cabang yang Menginput terlebih dahulu sebelum mengisi data dokumen.
        </div>
        <div id="dokumen-area" class="relative">
        <button id="cabang-guard" type="button" @if(old('cabang_id', $record->cabang_id)) hidden @endif aria-label="Pilih Cabang yang Menginput terlebih dahulu untuk mengisi data dokumen" class="absolute inset-0 z-10 w-full cursor-not-allowed bg-transparent focus:outline-none focus:ring-2 focus:ring-inset focus:ring-amber-400"></button>
        <fieldset id="dokumen-fields" @disabled(!old('cabang_id', $record->cabang_id)) class="min-w-0 border-0 p-0">
        <section class="p-5 sm:p-7">
            <div class="mb-5 flex items-start gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-700"><i class="fa-regular fa-file-lines" aria-hidden="true"></i></span>
                <div>
                    <h2 class="font-semibold text-gray-900">2. Identitas Dokumen</h2>
                    <p class="mt-0.5 text-sm text-gray-500">Pilih satu atau beberapa jenis nomor, lalu pilih nomor dokumennya. Tanggal diterapkan ke semua nomor yang dipilih.</p>
                </div>
            </div>
            <div class="grid gap-x-6 gap-y-5 md:grid-cols-2">
            <div>
                <label for="kapal_id" class="mb-1.5 block text-sm font-medium text-gray-700"><i class="fa-solid fa-ship mr-1.5 text-gray-400" aria-hidden="true"></i>Kapal <span class="text-red-500">*</span></label>
                <select id="kapal_id" name="kapal_id" required class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-800 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">-- Pilih Kapal --</option>
                    @foreach($kapals as $kapal)
                        <option value="{{ $kapal->id }}" @selected(old('kapal_id', $record->kapal_id) == $kapal->id)>{{ $kapal->nama_kapal }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="no_voyage" class="mb-1.5 block text-sm font-medium text-gray-700"><i class="fa-solid fa-route mr-1.5 text-gray-400" aria-hidden="true"></i>Voyage <span class="text-red-500">*</span></label>
                <select id="no_voyage" name="no_voyage" required data-selected="{{ old('no_voyage', $record->no_voyage) }}" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-800 shadow-sm focus:border-blue-500 focus:ring-blue-500 disabled:cursor-not-allowed disabled:bg-gray-50 disabled:text-gray-400" disabled><option value="">-- Pilih Kapal Terlebih Dahulu --</option></select>
            </div>
            <div class="document-picker md:col-span-2">
                <label for="jenis_dokumen" class="mb-1.5 block text-sm font-medium text-gray-700"><i class="fa-solid fa-layer-group mr-1.5 text-gray-400" aria-hidden="true"></i>Jenis Nomor <span class="text-red-500">*</span></label>
                <select id="jenis_dokumen" name="jenis_dokumen[]" multiple required class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-800 shadow-sm focus:border-blue-500 focus:ring-blue-500 disabled:cursor-not-allowed disabled:bg-gray-50 disabled:text-gray-400" disabled data-selected='@json((array) old('jenis_dokumen', $record->jenis_dokumen ? [$record->jenis_dokumen] : []))'><option value="">-- Pilih Voyage Terlebih Dahulu --</option></select>
                <p class="document-picker-help"><i class="fa-solid fa-circle-info mr-1 text-gray-400" aria-hidden="true"></i>Anda dapat memilih Nomor BL dan Nomor Tanda Terima sekaligus.</p>
            </div>
            <div class="document-picker md:col-span-2 rounded-xl border border-gray-200 bg-gray-50/60 p-4 sm:p-5">
                <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                    <label for="nomor_dokumen" class="block text-sm font-semibold text-gray-800"><i class="fa-regular fa-file-lines mr-1.5 text-blue-500" aria-hidden="true"></i>Nomor BL / Nomor Tanda Terima <span class="text-red-500">*</span></label>
                    <span class="document-picker-count"><i class="fa-solid fa-check mr-1" aria-hidden="true"></i><span id="document-count" aria-live="polite">0 nomor dipilih</span></span>
                </div>
                <select id="nomor_dokumen" name="nomor_dokumen[]" multiple required class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-800 shadow-sm focus:border-blue-500 focus:ring-blue-500 disabled:cursor-not-allowed disabled:bg-gray-50 disabled:text-gray-400" disabled data-selected='@json((array) old('nomor_dokumen', $record->nomor_dokumen ? [$record->jenis_dokumen . ":" . $record->nomor_dokumen] : []))'><option value="">-- Pilih Jenis Nomor Terlebih Dahulu --</option></select>
                <p class="document-picker-help"><i class="fa-solid fa-circle-info mr-1 text-gray-400" aria-hidden="true"></i><span id="document-picker-help">Pilih Jenis Nomor terlebih dahulu untuk menampilkan nomor yang tersedia.</span></p>
            </div>
        </div>
        </section>

        <fieldset id="tanggal-fields" @disabled(!old('cabang_id', $record->cabang_id)) class="border-t border-gray-200 bg-gray-50/70 p-5 sm:p-7">
        <div class="mb-5 flex items-start gap-3">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-700"><i class="fa-regular fa-calendar-days" aria-hidden="true"></i></span>
            <div>
                <h2 class="font-semibold text-gray-900">3. Tanggal Proses Dokumen</h2>
                <p class="mt-0.5 text-sm text-gray-500">Isi tanggal yang sudah diketahui. Kolom yang belum ada datanya boleh dikosongkan.</p>
            </div>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($dateFields as $field => $label)
                <div data-date-field="{{ $field }}" class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-colors focus-within:border-blue-300">
                    <label for="{{ $field }}" class="mb-2 flex min-h-10 items-start text-sm font-medium leading-5 text-gray-700">{{ $label }}</label>
                    <input type="date" id="{{ $field }}" name="{{ $field }}" value="{{ old($field, $record->$field?->format('Y-m-d')) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm text-gray-800 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            @endforeach
        </div>
        </fieldset>
        </fieldset>
        </div>
        <div class="flex flex-col-reverse gap-3 border-t border-gray-200 bg-white px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-7">
            <p class="text-xs text-gray-500"><span class="text-red-500">*</span> Wajib diisi</p>
            <button id="simpan-dokumen" type="submit" @disabled(!old('cabang_id', $record->cabang_id)) class="disabled:cursor-not-allowed disabled:opacity-50 inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <i class="fa-regular fa-floppy-disk" aria-hidden="true"></i> Simpan Tanggal Dokumen
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const cabang = document.getElementById('cabang_id');
    const tanggalFields = document.getElementById('tanggal-fields');
    const dokumenFields = document.getElementById('dokumen-fields');
    const simpanDokumen = document.getElementById('simpan-dokumen');
    const cabangGuard = document.getElementById('cabang-guard');
    const cabangNotice = document.getElementById('cabang-notice');
    cabangGuard.addEventListener('click', () => {
        cabangNotice.hidden = false;
        cabang.setAttribute('aria-invalid', 'true');
        cabang.setAttribute('aria-describedby', 'cabang-notice');
        cabang.focus();
    });
    function updateCabangAccess() {
        const locked = !cabang.value;
        dokumenFields.disabled = locked;
        tanggalFields.disabled = locked;
        simpanDokumen.disabled = locked;
        cabangGuard.hidden = !locked;
        const branchName = (cabang.selectedOptions[0]?.dataset.nama || '').trim().toLowerCase();
        const isJakarta = /\b(jakarta|jkt)\b/.test(branchName);
        const isBatam = /\bbatam\b/.test(branchName);
        const movementFields = ["tanggal_keluar_gate_tujuan", "tanggal_keluar_gudang_ayp", "tanggal_tiba_gudang_customer", "tanggal_keluar_gudang_customer", "tanggal_masuk_gate_tujuan"];
        const jakartaFields = ['tanggal_kirim_dokumen_jakarta', 'tanggal_terima_sj_cabang', 'tanggal_terima_ppftz_cabang'];
        document.querySelectorAll('[data-date-field]').forEach(container => {
            const field = container.dataset.dateField;
            const visible = isJakarta ? jakartaFields.includes(field) : (isBatam ? !jakartaFields.includes(field) : !movementFields.includes(field));
            container.hidden = !visible;
            container.querySelector('input').disabled = !visible;
        });
        if (!locked) {
            cabangNotice.hidden = true;
            cabang.removeAttribute('aria-invalid');
            cabang.removeAttribute('aria-describedby');
        }
    }
    cabang.addEventListener('change', updateCabangAccess);
    updateCabangAccess();
    const kapal = document.getElementById('kapal_id');
    const voyage = document.getElementById('no_voyage');
    const type = document.getElementById('jenis_dokumen');
    const number = document.getElementById('nomor_dokumen');
    const initialVoyage = voyage.dataset.selected;
    const initialType = JSON.parse(type.dataset.selected);
    const initialNumber = JSON.parse(number.dataset.selected);
    let documents = [];
    let voyageRequest = 0;
    let documentRequest = 0;
    const endpoint = '{{ url('/pengisian-tanggal-dokumen') }}';

    if (window.jQuery && jQuery.fn.select2) {
        jQuery(type).select2({ placeholder: 'Pilih jenis nomor', width: '100%', closeOnSelect: false, dropdownCssClass: 'document-picker-dropdown' });
        jQuery(number).select2({
            closeOnSelect: false,
            placeholder: 'Cari dan tambahkan nomor dokumen...',
            dropdownCssClass: 'document-picker-dropdown',
            width: '100%',
            minimumResultsForSearch: 0,
            language: { noResults: () => 'Nomor tidak ditemukan' },
        });
    }

    function updateNumberSummary() {
        document.getElementById('document-count').textContent = `${number.selectedOptions.length} nomor dipilih`;
        document.getElementById('document-picker-help').textContent = number.disabled
            ? 'Pilih Jenis Nomor terlebih dahulu untuk menampilkan nomor yang tersedia.'
            : 'Ketik untuk mencari, lalu klik nomor untuk menambah pilihan. Klik tanda silang untuk menghapus nomor.';
    }
    if (window.jQuery && jQuery.fn.select2) jQuery(number).on('change', updateNumberSummary);
    else number.addEventListener('change', updateNumberSummary);

    const reset = (select, label) => {
        select.innerHTML = select.multiple ? '' : `<option value="">${label}</option>`;
        select.dataset.placeholder = label;
        select.disabled = true;
        if (select === number) updateNumberSummary();
        if (select.multiple && window.jQuery && jQuery.fn.select2) jQuery(select).trigger('change.select2');
    };
    const addOption = (select, value, label, selected = false) => {
        const option = document.createElement('option'); option.value = value; option.textContent = label; option.selected = selected; select.appendChild(option);
    };

    async function loadVoyages(selected = '') {
        const requestId = ++voyageRequest;
        ++documentRequest;
        documents = [];
        reset(voyage, '-- Memuat Voyage --'); reset(type, '-- Pilih Voyage Terlebih Dahulu --'); reset(number, '-- Pilih Jenis Nomor Terlebih Dahulu --');
        if (!kapal.value) { reset(voyage, '-- Pilih Kapal Terlebih Dahulu --'); return; }
        const response = await fetch(`${endpoint}/get-voyages?kapal_id=${encodeURIComponent(kapal.value)}`, {headers: {'Accept': 'application/json'}});
        const data = await response.json();
        if (requestId !== voyageRequest) return;
        reset(voyage, '-- Pilih Voyage --');
        data.voyages.forEach(item => addOption(voyage, item, item, item === selected));
        voyage.disabled = false;
        if (selected) await loadDocuments(initialType, initialNumber);
    }

    async function loadDocuments(selectedType = [], selectedNumber = []) {
        const requestId = ++documentRequest;
        documents = [];
        reset(type, '-- Memuat Jenis Nomor --'); reset(number, '-- Pilih Jenis Nomor Terlebih Dahulu --');
        if (!kapal.value || !voyage.value) return;
        const response = await fetch(`${endpoint}/get-documents?kapal_id=${encodeURIComponent(kapal.value)}&no_voyage=${encodeURIComponent(voyage.value)}`, {headers: {'Accept': 'application/json'}});
        const data = await response.json();
        if (requestId !== documentRequest) return;
        documents = data.documents;
        const types = [...new Set(documents.map(item => item.type))];
        reset(type, '-- Pilih Jenis Nomor --');
        types.forEach(item => addOption(type, item, item === 'BL' ? 'Nomor BL' : 'Nomor Tanda Terima', selectedType.includes(item)));
        type.disabled = false;
        if (window.jQuery && jQuery.fn.select2) jQuery(type).trigger('change.select2');
        loadNumbers(selectedNumber);
    }

    function loadNumbers(selected = Array.from(number.selectedOptions, option => option.value)) {
        const selectedTypes = Array.from(type.selectedOptions, option => option.value);
        reset(number, '-- Pilih Jenis Nomor Terlebih Dahulu --');
        if (!selectedTypes.length) return;
        documents.filter(item => selectedTypes.includes(item.type)).forEach(item => {
            const containers = Array.isArray(item.containers) ? item.containers.filter(Boolean).join(', ') : '';
            const typeLabel = item.type === 'BL' ? 'BL' : 'Tanda Terima';
            const label = `${typeLabel}: ${item.number}${containers ? ` - Kontainer: ${containers}` : ''}`;
            const value = `${item.type}:${item.number}`;
            addOption(number, value, label, selected.includes(value));
        });
        number.disabled = false;
        updateNumberSummary();
        if (window.jQuery && jQuery.fn.select2) jQuery(number).trigger('change.select2');
    }

    kapal.addEventListener('change', () => loadVoyages());
    voyage.addEventListener('change', () => loadDocuments());
    if (window.jQuery && jQuery.fn.select2) jQuery(type).on('change', () => loadNumbers());
    else type.addEventListener('change', () => loadNumbers());
    if (kapal.value) loadVoyages(initialVoyage);
});
</script>
@endsection
