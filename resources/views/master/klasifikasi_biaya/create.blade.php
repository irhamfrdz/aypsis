@extends('layouts.app')

@section('title','Tambah Klasifikasi Biaya')
@section('page_title','Tambah Klasifikasi Biaya')

@section('content')
<div class="p-6 bg-white rounded shadow">
    <h3 class="font-semibold mb-4">Tambah Klasifikasi Biaya</h3>

    <form action="{{ route('klasifikasi-biaya.store') }}" method="POST">
        @include('master.klasifikasi_biaya._form')
    </form>
</div>
@endsection
