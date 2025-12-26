@extends('layouts.app')

@section('title','Edit Klasifikasi Biaya')
@section('page_title','Edit Klasifikasi Biaya')

@section('content')
<div class="p-6 bg-white rounded shadow">
    <h3 class="font-semibold mb-4">Edit Klasifikasi Biaya</h3>

    <form action="{{ route('klasifikasi-biaya.update', $item) }}" method="POST">
        @method('PUT')
        @include('master.klasifikasi_biaya._form')
    </form>
</div>
@endsection
