@extends('layouts.app')
@section('title', 'Edit Pricelist OB Antar Gudang')
@section('page_title', 'Edit Pricelist OB Antar Gudang')
@section('content')
<div class="mx-auto max-w-3xl px-4 py-8"><div class="rounded-lg bg-white p-6 shadow"><h1 class="mb-6 text-xl font-bold text-gray-800">Edit Pricelist OB Antar Gudang</h1>
    @include('master.pricelist-ob-antar-gudang._form', ['action' => route('master.pricelist-ob-antar-gudang.update', $pricelist), 'method' => 'PUT'])
</div></div>
@endsection
