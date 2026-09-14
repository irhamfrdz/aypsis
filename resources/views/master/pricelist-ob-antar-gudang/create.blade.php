@extends('layouts.app')
@section('title', 'Tambah Pricelist OB Antar Gudang')
@section('page_title', 'Tambah Pricelist OB Antar Gudang')
@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <div class="mb-6 overflow-hidden rounded-xl bg-white shadow-lg">
            <div class="bg-gradient-to-r from-teal-600 to-teal-700 px-6 py-5">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-white/20">
                        <i class="fas fa-plus text-xl text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">Tambah Pricelist OB Antar Gudang</h1>
                        <p class="mt-1 text-sm text-teal-100">Atur tarif perpindahan kontainer berdasarkan ukuran dan statusnya.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl bg-white shadow-lg">
            <div class="border-b border-gray-200 px-6 py-5">
                <h2 class="text-lg font-semibold text-gray-800">Informasi Tarif</h2>
                <p class="mt-1 text-sm text-gray-500">Lengkapi seluruh kolom bertanda bintang agar tarif dapat digunakan pada transaksi OB Antar Gudang.</p>
            </div>
            <div class="p-6 sm:p-8">
                @include('master.pricelist-ob-antar-gudang._form', ['action' => route('master.pricelist-ob-antar-gudang.store'), 'method' => 'POST', 'pricelist' => null])
            </div>
        </div>
    </div>
</div>
@endsection
