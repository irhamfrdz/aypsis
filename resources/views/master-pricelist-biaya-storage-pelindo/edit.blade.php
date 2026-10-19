@extends('layouts.app')

@section('title', 'Edit Pricelist Biaya Storage Pelindo')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-6">
    <div class="mb-6 flex items-center justify-between"><h1 class="text-2xl font-bold text-gray-900">Edit Tarif Storage Pelindo</h1><a href="{{ route('master-pricelist-biaya-storage-pelindo.index') }}" class="text-sm text-gray-600 hover:underline">Kembali</a></div>
    <form action="{{ route('master-pricelist-biaya-storage-pelindo.update', $pricelist) }}" method="POST" class="space-y-5 rounded-lg border bg-white p-6 shadow-sm">
        @csrf @method('PUT')
        @include('master-pricelist-biaya-storage-pelindo._form')
        <div class="flex justify-end"><button class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Simpan Perubahan</button></div>
    </form>
</div>
@endsection
