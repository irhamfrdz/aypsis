@extends('layouts.app')
@section('title', 'Tambah Pricelist OB Antar Gudang')
@section('page_title', 'Tambah Pricelist OB Antar Gudang')
@section('content')
<div class="mx-auto max-w-3xl px-4 py-8"><div class="rounded-lg bg-white p-6 shadow"><h1 class="mb-6 text-xl font-bold text-gray-800">Tambah Pricelist OB Antar Gudang</h1>
    @include('master.pricelist-ob-antar-gudang._form', ['action' => route('master.pricelist-ob-antar-gudang.store'), 'method' => 'POST', 'pricelist' => null])
</div></div>
@endsection
