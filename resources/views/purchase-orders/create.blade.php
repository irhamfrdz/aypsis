@extends('layouts.app')

@section('title', 'Tambah Purchase Order - AYPSIS')
@section('page_title', 'Tambah Purchase Order')

@section('content')
<div class="mx-auto max-w-6xl pb-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Tambah Purchase Order</h1>
        @if($permohonanAmprahan)
            <p class="mt-1 text-sm text-slate-500">Barang dari permohonan amprahan #{{ $permohonanAmprahan->id }} sudah terisi. Lengkapi vendor dan harga; persetujuan baru selesai setelah PO disimpan.</p>
        @else
            <p class="mt-1 text-sm text-slate-500">Isi vendor dan rincian barang atau jasa yang akan dipesan.</p>
        @endif
    </div>
    <form method="POST" action="{{ route('purchase-orders.store') }}">
        @csrf
        @if($permohonanAmprahan)
            <input type="hidden" name="permohonan_amprahan_id" value="{{ $permohonanAmprahan->id }}">
        @endif
        @include('purchase-orders._form', ['submitLabel' => 'Simpan Purchase Order'])
    </form>
</div>
@endsection
