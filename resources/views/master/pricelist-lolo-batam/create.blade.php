@extends('layouts.app')

@section('title', 'Tambah Pricelist LOLO Batam')

@section('content')
<div class="mx-auto max-w-4xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Tambah Pricelist LOLO Batam</h1>
        <p class="mt-1 text-sm text-gray-500">Tambahkan tarif lift on/lift off khusus operasional Batam.</p>
    </div>
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('master.pricelist-lolo-batam.store') }}">
            @csrf
            @include('master.pricelist-lolo-batam._form', ['submitLabel' => 'Simpan Pricelist'])
        </form>
    </div>
</div>
@endsection
