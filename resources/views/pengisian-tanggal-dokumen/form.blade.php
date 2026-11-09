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
<div class="container mx-auto px-4 py-6 max-w-5xl">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $record->exists ? 'Ubah Tanggal Dokumen' : 'Pengisian Tanggal Dokumen' }}</h1>
            <p class="text-sm text-gray-600 mt-1">Catat tanggal penerimaan dan pengiriman dokumen berdasarkan kapal, voyage, dan nomor dokumen.</p>
        </div>
        <a href="{{ route('pengisian-tanggal-dokumen.index') }}" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm">Kembali</a>
    </div>

    @if($errors->any())
        <div class="mb-5 p-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
            <ul class="list-disc list-inside">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ $formAction }}" class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 md:p-7">
        @csrf
        @if($formMethod !== 'POST') @method($formMethod) @endif
        <h2 class="font-semibold text-gray-800 mb-4">Identitas Dokumen</h2>
        <div class="grid md:grid-cols-2 gap-5">
            <div>
                <label for="kapal_id" class="block text-sm font-medium text-gray-700 mb-1">Kapal <span class="text-red-500">*</span></label>
                <select id="kapal_id" name="kapal_id" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">-- Pilih Kapal --</option>
                    @foreach($kapals as $kapal)
                        <option value="{{ $kapal->id }}" @selected(old('kapal_id', $record->kapal_id) == $kapal->id)>{{ $kapal->nama_kapal }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="no_voyage" class="block text-sm font-medium text-gray-700 mb-1">Voyage <span class="text-red-500">*</span></label>
                <select id="no_voyage" name="no_voyage" required data-selected="{{ old('no_voyage', $record->no_voyage) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" disabled><option value="">-- Pilih Kapal Terlebih Dahulu --</option></select>
            </div>
            <div>
                <label for="jenis_dokumen" class="block text-sm font-medium text-gray-700 mb-1">Jenis Nomor <span class="text-red-500">*</span></label>
                <select id="jenis_dokumen" name="jenis_dokumen" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" disabled data-selected="{{ old('jenis_dokumen', $record->jenis_dokumen) }}"><option value="">-- Pilih Voyage Terlebih Dahulu --</option></select>
            </div>
            <div>
                <label for="nomor_dokumen" class="block text-sm font-medium text-gray-700 mb-1">Nomor BL / Nomor Tanda Terima <span class="text-red-500">*</span></label>
                <select id="nomor_dokumen" name="nomor_dokumen" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" disabled data-selected="{{ old('nomor_dokumen', $record->nomor_dokumen) }}"><option value="">-- Pilih Jenis Nomor Terlebih Dahulu --</option></select>
            </div>
        </div>

        <h2 class="font-semibold text-gray-800 mt-8 mb-4">Tanggal Proses Dokumen</h2>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($dateFields as $field => $label)
                <div>
                    <label for="{{ $field }}" class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>
                    <input type="date" id="{{ $field }}" name="{{ $field }}" value="{{ old($field, $record->$field?->format('Y-m-d')) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>
            @endforeach
        </div>
        <div class="flex justify-end mt-8">
            <button type="submit" class="px-5 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-medium">Simpan Tanggal Dokumen</button>
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

    const reset = (select, label) => { select.innerHTML = `<option value="">${label}</option>`; select.disabled = true; };
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
    }

    kapal.addEventListener('change', () => loadVoyages());
    voyage.addEventListener('change', () => loadDocuments());
    type.addEventListener('change', () => loadNumbers());
    if (kapal.value) loadVoyages(initialVoyage);
});
</script>
@endsection
