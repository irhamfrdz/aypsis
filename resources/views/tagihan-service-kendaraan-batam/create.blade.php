@extends('layouts.app')

@section('title', 'Tambah Tagihan Lain Lain Batam')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-6">
    <div class="mb-6 flex items-center justify-between"><h1 class="text-2xl font-bold text-gray-900">Tambah Tagihan Lain Lain Batam</h1><a href="{{ route('tagihan-service-kendaraan-batam.index') }}" class="text-sm text-gray-600 hover:underline">Kembali</a></div>
    <form action="{{ route('tagihan-service-kendaraan-batam.store') }}" method="POST" class="space-y-5 rounded-lg border bg-white p-6 shadow-sm">
        @csrf
        @include('tagihan-service-kendaraan-batam._form')
        <div class="flex justify-end"><button class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white hover:bg-blue-700">Simpan Tagihan</button></div>
    </form>
</div>
@endsection
