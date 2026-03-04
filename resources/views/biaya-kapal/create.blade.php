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

{{-- ===== FORM FIELDS EXTRA ===== --}}
{{-- Nominal, PPH Dokumen, Grand Total, Materai, DP, Sisa Pembayaran,
     Penerima, PPN, PPH, Total Biaya, Nama Vendor, Rekening,
     Keterangan, Upload Bukti, Info Box, Form Actions --}}
@include('biaya-kapal.create._form-fields-extra')

@endsection

{{-- ===== JAVASCRIPT ===== --}}

{{-- JS Init: pricelist data variables --}}
@include('biaya-kapal.create._js-init')

{{-- JS Jenis Biaya: searchable dropdown + vendor dokumen logic --}}
@include('biaya-kapal.create._js-jenis-biaya')

{{-- JS Toggle: show/hide sections based on jenis biaya --}}
@include('biaya-kapal.create._js-toggle')

{{-- JS Perlengkapan --}}
@include('biaya-kapal.create._js-perlengkapan')

{{-- JS Buruh / Barang --}}
@include('biaya-kapal.create._js-buruh')

{{-- JS OPP/OPT --}}
@include('biaya-kapal.create._js-opp-opt')

{{-- JS TKBM --}}
@include('biaya-kapal.create._js-tkbm')

{{-- JS Air Tawar --}}
@include('biaya-kapal.create._js-air')

{{-- JS Labuh Tambat --}}
@include('biaya-kapal.create._js-labuh-tambat')

{{-- JS Penerima Select2 --}}
@include('biaya-kapal.create._js-penerima')

{{-- JS Kapal Multi-Select --}}
@include('biaya-kapal.create._js-kapal-multi')

{{-- JS Voyage Multi-Select --}}
@include('biaya-kapal.create._js-voyage-multi')

{{-- JS BL Multi-Select --}}
@include('biaya-kapal.create._js-bl-multi')

{{-- JS Operasional --}}
@include('biaya-kapal.create._js-operasional')

{{-- JS Trucking --}}
@include('biaya-kapal.create._js-trucking')

{{-- JS Stuffing --}}
@include('biaya-kapal.create._js-stuffing')

{{-- JS Storage --}}
@include('biaya-kapal.create._js-storage')

{{-- JS THC --}}
@include('biaya-kapal.create._js-thc')

{{-- Styles (CSS) --}}
@include('biaya-kapal.create._styles')

{{-- JS LOLO (separate script block) --}}
@include('biaya-kapal.create._js-lolo')
