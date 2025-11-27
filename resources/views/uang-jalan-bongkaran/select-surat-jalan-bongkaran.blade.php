@extends('layouts.app')

@section('page_title', 'Tambah Data Uang Jalan Bongkaran')

@section('content')
@include('uang-jalan.select-surat-jalan', [
	'routePrefix' => 'uang-jalan-bongkaran',
	'suratJalans' => $suratJalanBongkarans ?? $suratJalans ?? collect([]),
	'search' => $search ?? '',
	'tanggal_dari' => $tanggal_dari ?? '',
	'tanggal_sampai' => $tanggal_sampai ?? '',
	'status' => $status ?? null,
	'statusOptions' => $statusOptions ?? null,
])
@endsection
