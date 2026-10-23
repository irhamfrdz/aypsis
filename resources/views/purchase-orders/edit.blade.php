@extends('layouts.app')

@section('title', 'Ubah Purchase Order - AYPSIS')
@section('page_title', 'Ubah Purchase Order')

@section('content')
<div class="mx-auto max-w-6xl pb-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Ubah Purchase Order</h1>
        <p class="mt-1 text-sm text-slate-500">{{ $purchaseOrder->nomor_po }}</p>
    </div>
    <form method="POST" action="{{ route('purchase-orders.update', $purchaseOrder) }}">
        @csrf
        @method('PUT')
        @include('purchase-orders._form', ['submitLabel' => 'Simpan Perubahan'])
    </form>
</div>
@endsection
