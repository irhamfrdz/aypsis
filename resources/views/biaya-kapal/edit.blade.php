@extends('layouts.app')

@section('title', 'Edit Biaya Kapal')

@section('content')
{{-- =============================================
    EDIT BIAYA KAPAL - MAIN FILE
    File ini dipecah menjadi partial files di folder edit/
    untuk memudahkan maintenance kode.
    ============================================= --}}

{{-- Header: alerts, form card header, form opening --}}
@include('biaya-kapal.edit._form-header')

{{-- Form Fields Common: tanggal, invoice, referensi, kapal, voyage, BL, jenis biaya --}}
@include('biaya-kapal.edit._form-fields-common')

{{-- ===== SECTION PER JENIS BIAYA (HTML Wrappers) ===== --}}

{{-- Biaya Dokumen: vendor dropdown --}}
@include('biaya-kapal.edit._section-dokumen')

{{-- Biaya Air Tawar --}}
@include('biaya-kapal.edit._section-air')

{{-- Biaya Buruh / Barang --}}
@include('biaya-kapal.edit._section-buruh')

{{-- Biaya TKBM --}}
@include('biaya-kapal.edit._section-tkbm')

{{-- Biaya Stuffing --}}
@include('biaya-kapal.edit._section-stuffing')

{{-- Biaya Operasional --}}
@include('biaya-kapal.edit._section-operasional')

{{-- Biaya Trucking --}}
@include('biaya-kapal.edit._section-trucking')

{{-- Biaya Labuh Tambat --}}
@include('biaya-kapal.edit._section-labuh-tambat')

{{-- Biaya THC --}}
@include('biaya-kapal.edit._section-thc')

{{-- Biaya LOLO --}}
@include('biaya-kapal.edit._section-lolo')

{{-- Biaya Storage --}}
@include('biaya-kapal.edit._section-storage')
@include('biaya-kapal.edit._section-perijinan')
@include('biaya-kapal.edit._section-meratus')
@include('biaya-kapal.edit._section-temas')

{{-- ===== FORM FIELDS EXTRA ===== --}}
@include('biaya-kapal.edit._form-fields-extra')

@endsection

{{-- ===== STYLES ===== --}}
@push('styles')
@include('biaya-kapal.edit._styles')
@endpush

@push('scripts')
<script>
@include('biaya-kapal.edit._js-init')
@include('biaya-kapal.edit._js-jenis-biaya')
@include('biaya-kapal.edit._js-jenis-biaya-handler')
@include('biaya-kapal.edit._js-kapal-sections')
@include('biaya-kapal.edit._js-tkbm-sections')
@include('biaya-kapal.edit._js-air-sections')
@include('biaya-kapal.edit._js-penerima')
@include('biaya-kapal.edit._js-multi-select')
@include('biaya-kapal.edit._js-operasional-sections')
@include('biaya-kapal.edit._js-stuffing-sections')
@include('biaya-kapal.edit._js-trucking-sections')
@include('biaya-kapal.edit._js-labuh-tambat-sections')
@include('biaya-kapal.edit._js-thc-sections')
@include('biaya-kapal.edit._js-lolo-sections')
@include('biaya-kapal.edit._js-storage-sections')
@include('biaya-kapal.create._js-perijinan')
@include('biaya-kapal.edit._js-meratus')
@include('biaya-kapal.edit._js-temas')

{{-- Init must be last after all functions are defined --}}
@include('biaya-kapal.edit._js-edit-init')
</script>
@endpush
