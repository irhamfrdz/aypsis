@extends('layouts.app')

@section('title', 'Tambah Biaya Kapal')

@section('content')
{{-- =============================================
    TAMBAH BIAYA KAPAL - MAIN FILE
    File ini dipecah menjadi partial files di folder create/
    untuk memudahkan maintenance kode.
    ============================================= --}}

{{-- Header: alerts, form card header, form opening --}}
@include('biaya-kapal.create._form-header')

{{-- Form Fields Common: tanggal, invoice, referensi, kapal, voyage, BL, jenis biaya --}}
@include('biaya-kapal.create._form-fields-common')

{{-- ===== SECTION PER JENIS BIAYA (HTML Wrappers) ===== --}}

{{-- Biaya Dokumen: vendor dropdown --}}
@include('biaya-kapal.create._section-dokumen')

{{-- Biaya Air Tawar --}}
@include('biaya-kapal.create._section-air')

{{-- Biaya Labuh Tambat --}}
@include('biaya-kapal.create._section-labuh-tambat')

{{-- Biaya Buruh / Barang --}}
@include('biaya-kapal.create._section-buruh')

{{-- Biaya OPP/OPT --}}
@include('biaya-kapal.create._section-opp-opt')

{{-- Biaya TKBM --}}
@include('biaya-kapal.create._section-tkbm')

{{-- Biaya Operasional --}}
@include('biaya-kapal.create._section-operasional')

{{-- Biaya Trucking --}}
@include('biaya-kapal.create._section-trucking')

{{-- Biaya Stuffing --}}
@include('biaya-kapal.create._section-stuffing')

{{-- Biaya THC --}}
@include('biaya-kapal.create._section-thc')

{{-- Biaya LOLO --}}
@include('biaya-kapal.create._section-lolo')

{{-- Biaya Storage --}}
@include('biaya-kapal.create._section-storage')

{{-- Biaya Perlengkapan --}}
@include('biaya-kapal.create._section-perlengkapan')
@include('biaya-kapal.create._section-freight')

{{-- ===== FORM FIELDS EXTRA ===== --}}
@include('biaya-kapal.create._form-fields-extra')

@endsection

{{-- ===== STYLES ===== --}}
@push('styles')
@include('biaya-kapal.create._styles')
@endpush

{{-- ===== JAVASCRIPT (Main Script Block) ===== --}}
@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
@include('biaya-kapal.create._js-init')
@include('biaya-kapal.create._js-jenis-biaya')
@include('biaya-kapal.create._js-toggle')
@include('biaya-kapal.create._js-perlengkapan')
@include('biaya-kapal.create._js-buruh')
@include('biaya-kapal.create._js-opp-opt')
@include('biaya-kapal.create._js-tkbm')
@include('biaya-kapal.create._js-air')
@include('biaya-kapal.create._js-labuh-tambat')
@include('biaya-kapal.create._js-penerima')
@include('biaya-kapal.create._js-kapal-multi')
@include('biaya-kapal.create._js-voyage-multi')
@include('biaya-kapal.create._js-bl-multi')
@include('biaya-kapal.create._js-operasional')
@include('biaya-kapal.create._js-trucking')
@include('biaya-kapal.create._js-stuffing')
@include('biaya-kapal.create._js-storage')
@include('biaya-kapal.create._js-thc')
@include('biaya-kapal.create._js-freight')
</script>
@endpush

{{-- ===== JAVASCRIPT (LOLO - Separate Script Block) ===== --}}
@push('scripts')
<script>
@include('biaya-kapal.create._js-lolo')
</script>
@endpush
