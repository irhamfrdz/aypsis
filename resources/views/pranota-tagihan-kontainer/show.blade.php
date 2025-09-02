@extends('layouts.app')

@section('title', 'Lihat Pranota')
@section('page_title', 'Lihat Pranota')

@section('content')
<div class="bg-white shadow rounded p-6">
    <h3 class="text-lg font-semibold">{{ $pranota->nomor }}</h3>
    <p>Tanggal: {{ $pranota->tanggal }}</p>
    <p>Periode: {{ $pranota->periode }}</p>
    <p>Vendor: {{ $pranota->vendor }}</p>
    <p>Total: {{ number_format($pranota->total, 2) }}</p>
    <p class="mt-4">Keterangan:</p>
    <div class="mt-2 p-4 bg-gray-50 rounded">{!! nl2br(e($pranota->keterangan)) !!}</div>
</div>
@endsection
