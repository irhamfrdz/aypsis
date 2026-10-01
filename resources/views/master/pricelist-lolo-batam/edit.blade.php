@extends('layouts.app')

@section('title', 'Edit Pricelist LOLO Batam')

@section('content')
<div class="mx-auto max-w-4xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit Pricelist LOLO Batam</h1>
        <p class="mt-1 text-sm text-gray-500">Perbarui tarif untuk kontainer {{ $pricelistLoloBatam->size }} feet.</p>
    </div>
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('master.pricelist-lolo-batam.update', $pricelistLoloBatam) }}">
            @csrf
            @method('PUT')
            @include('master.pricelist-lolo-batam._form', ['submitLabel' => 'Simpan Perubahan'])
        </form>
    </div>
</div>
@endsection
