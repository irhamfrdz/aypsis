@extends('layouts.app')

@section('title', $record->exists ? 'Ubah Tanggal Dokumen' : 'Pengisian Tanggal Dokumen')

@section('content')
@php
    $dateFields = [
        'tanggal_terima_dokumen_jkt_customer' => 'Tanggal Terima Dokumen Jakarta / Customer',
        'tanggal_terima_dokumen_packinglist' => 'Tanggal Terima Dokumen Packing List',
        'tanggal_dokumen_invoice_faktur_pajak' => 'Tanggal Dokumen Invoice & Faktur Pajak',
        'tanggal_kirim_dokumen_ppjk' => 'Tanggal Kirim Dokumen ke PPJK',
        'tanggal_terbit_sppb_ppftz' => 'Tanggal Penerbitan SPPB & PPFTZ',
        'tanggal_terima_dokumen_dari_ppjk' => 'Tanggal Terima Dokumen dari PPJK',
        'tanggal_kirim_dokumen_jakarta' => 'Tanggal Kirim Dokumen ke Jakarta',
        'tanggal_terima_sj_cabang' => 'Tanggal Terima SJ dari Cabang',
        'tanggal_terima_ppftz_cabang' => 'Tanggal Terima PPFTZ dari Cabang',
    ];
@endphp
<style>
    #nomor_dokumen + .select2-container { width: 100% !important; }
    #nomor_dokumen + .select2-container .select2-selection--single {
        height: 42px;
        border: 1px solid #d1d5db;
        border-radius: .5rem;
        background: #fff;
    }
    #nomor_dokumen + .select2-container .select2-selection__rendered {
        padding-left: .75rem;
        padding-right: 2rem;
        color: #1f2937;
        font-size: .875rem;
        line-height: 40px;
    }
    #nomor_dokumen + .select2-container .select2-selection__arrow { height: 40px; right: .35rem; }
    #nomor_dokumen + .select2-container.select2-container--focus .select2-selection--single,
    #nomor_dokumen + .select2-container.select2-container--open .select2-selection--single {
        border-color: #3b82f6;
        outline: 2px solid transparent;
        box-shadow: 0 0 0 2px rgb(59 130 246 / .2);
    }
    .select2-dropdown { border-color: #d1d5db; border-radius: .5rem; overflow: hidden; }
    .select2-search--dropdown { padding: .5rem; }
    .select2-search--dropdown .select2-search__field {
        border: 1px solid #d1d5db;
        border-radius: .375rem;
        padding: .5rem .625rem;
        outline: none;
    }
    .select2-search--dropdown .select2-search__field:focus { border-color: #3b82f6; box-shadow: 0 0 0 2px rgb(59 130 246 / .15); }
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
        <section class="p-5 sm:p-7">
            <div class="mb-5 flex items-start gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-700"><i class="fa-regular fa-file-lines" aria-hidden="true"></i></span>
                <div>
                    <h2 class="font-semibold text-gray-900">Identitas Dokumen</h2>
                    <p class="mt-0.5 text-sm text-gray-500">Pilih data dokumen yang akan dicatat tanggal prosesnya.</p>
                </div>
            </div>
            <div class="grid gap-x-6 gap-y-5 md:grid-cols-2">
            <div>
                <label for="kapal_id" class="mb-1.5 block text-sm font-medium text-gray-700">Kapal <span class="text-red-500">*</span></label>
                <select id="kapal_id" name="kapal_id" required class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-800 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">-- Pilih Kapal --</option>
                    @foreach($kapals as $kapal)
                        <option value="{{ $kapal->id }}" @selected(old('kapal_id', $record->kapal_id) == $kapal->id)>{{ $kapal->nama_kapal }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="no_voyage" class="mb-1.5 block text-sm font-medium text-gray-700">Voyage <span class="text-red-500">*</span></label>
                <select id="no_voyage" name="no_voyage" required data-selected="{{ old('no_voyage', $record->no_voyage) }}" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-800 shadow-sm focus:border-blue-500 focus:ring-blue-500 disabled:cursor-not-allowed disabled:bg-gray-50 disabled:text-gray-400" disabled><option value="">-- Pilih Kapal Terlebih Dahulu --</option></select>
            </div>
            <div>
                <label for="jenis_dokumen" class="mb-1.5 block text-sm font-medium text-gray-700">Jenis Nomor <span class="text-red-500">*</span></label>
                <select id="jenis_dokumen" name="jenis_dokumen" required class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-800 shadow-sm focus:border-blue-500 focus:ring-blue-500 disabled:cursor-not-allowed disabled:bg-gray-50 disabled:text-gray-400" disabled data-selected="{{ old('jenis_dokumen', $record->jenis_dokumen) }}"><option value="">-- Pilih Voyage Terlebih Dahulu --</option></select>
            </div>
            <div>
                <label for="nomor_dokumen" class="mb-1.5 block text-sm font-medium text-gray-700">Nomor BL / Nomor Tanda Terima <span class="text-red-500">*</span></label>
                <select id="nomor_dokumen" name="nomor_dokumen" required class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-800 shadow-sm focus:border-blue-500 focus:ring-blue-500 disabled:cursor-not-allowed disabled:bg-gray-50 disabled:text-gray-400" disabled data-selected="{{ old('nomor_dokumen', $record->nomor_dokumen) }}"><option value="">-- Pilih Jenis Nomor Terlebih Dahulu --</option></select>
            </div>
        </div>
        </section>

        <section class="border-t border-gray-200 bg-gray-50/70 p-5 sm:p-7">
        <div class="mb-5 flex items-start gap-3">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-700"><i class="fa-regular fa-calendar-days" aria-hidden="true"></i></span>
            <div>
                <h2 class="font-semibold text-gray-900">Tanggal Proses Dokumen</h2>
                <p class="mt-0.5 text-sm text-gray-500">Isi tanggal yang sudah diketahui. Kolom yang belum ada datanya boleh dikosongkan.</p>
            </div>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($dateFields as $field => $label)
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <label for="{{ $field }}" class="mb-2 flex min-h-10 items-start text-sm font-medium leading-5 text-gray-700">{{ $label }}</label>
                    <input type="date" id="{{ $field }}" name="{{ $field }}" value="{{ old($field, $record->$field?->format('Y-m-d')) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm text-gray-800 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            @endforeach
        </div>
        </section>
        <div class="flex flex-col-reverse gap-3 border-t border-gray-200 bg-white px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-7">
            <p class="text-xs text-gray-500"><span class="text-red-500">*</span> Wajib diisi</p>
            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <i class="fa-regular fa-floppy-disk" aria-hidden="true"></i> Simpan Tanggal Dokumen
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const kapal = document.getElementById('kapal_id');
    const voyage = document.getElementById('no_voyage');
    const type = document.getElementById('jenis_dokumen');
    const number = document.getElementById('nomor_dokumen');
    const initialVoyage = voyage.dataset.selected;
    const initialType = type.dataset.selected;
    const initialNumber = number.dataset.selected;
    const endpoint = '{{ url('/pengisian-tanggal-dokumen') }}';

    if (window.jQuery && jQuery.fn.select2) {
        jQuery(number).select2({
            placeholder: '-- Pilih Nomor Dokumen --',
            width: '100%',
            minimumResultsForSearch: 0,
            language: { noResults: () => 'Nomor tidak ditemukan' },
        });
    }

    const reset = (select, label) => {
        select.innerHTML = `<option value="">${label}</option>`;
        select.disabled = true;
        if (select === number && window.jQuery && jQuery.fn.select2) jQuery(select).trigger('change.select2');
    };
    const addOption = (select, value, label, selected = false) => {
        const option = document.createElement('option'); option.value = value; option.textContent = label; option.selected = selected; select.appendChild(option);
    };

    async function loadVoyages(selected = '') {
        reset(voyage, '-- Memuat Voyage --'); reset(type, '-- Pilih Voyage Terlebih Dahulu --'); reset(number, '-- Pilih Jenis Nomor Terlebih Dahulu --');
        if (!kapal.value) { reset(voyage, '-- Pilih Kapal Terlebih Dahulu --'); return; }
        const response = await fetch(`${endpoint}/get-voyages?kapal_id=${encodeURIComponent(kapal.value)}`, {headers: {'Accept': 'application/json'}});
        const data = await response.json();
        reset(voyage, '-- Pilih Voyage --');
        data.voyages.forEach(item => addOption(voyage, item, item, item === selected));
        voyage.disabled = false;
        if (selected) await loadDocuments(initialType, initialNumber);
    }

    async function loadDocuments(selectedType = '', selectedNumber = '') {
        reset(type, '-- Memuat Jenis Nomor --'); reset(number, '-- Pilih Jenis Nomor Terlebih Dahulu --');
        if (!kapal.value || !voyage.value) return;
        const response = await fetch(`${endpoint}/get-documents?kapal_id=${encodeURIComponent(kapal.value)}&no_voyage=${encodeURIComponent(voyage.value)}`, {headers: {'Accept': 'application/json'}});
        const data = await response.json();
        const types = [...new Set(data.documents.map(item => item.type))];
        reset(type, '-- Pilih Jenis Nomor --');
        types.forEach(item => addOption(type, item, item === 'BL' ? 'Nomor BL' : 'Nomor Tanda Terima', item === selectedType));
        type.disabled = false;
        if (selectedType) await loadNumbers(selectedNumber);
    }

    async function loadNumbers(selected = '') {
        reset(number, '-- Pilih Nomor Dokumen --');
        const response = await fetch(`${endpoint}/get-documents?kapal_id=${encodeURIComponent(kapal.value)}&no_voyage=${encodeURIComponent(voyage.value)}`, {headers: {'Accept': 'application/json'}});
        const data = await response.json();
        data.documents.filter(item => item.type === type.value).forEach(item => addOption(number, item.number, item.number, item.number === selected));
        number.disabled = false;
        if (window.jQuery && jQuery.fn.select2) jQuery(number).trigger('change.select2');
    }

    kapal.addEventListener('change', () => loadVoyages());
    voyage.addEventListener('change', () => loadDocuments());
    type.addEventListener('change', () => loadNumbers());
    if (kapal.value) loadVoyages(initialVoyage);
});
</script>
@endsection
