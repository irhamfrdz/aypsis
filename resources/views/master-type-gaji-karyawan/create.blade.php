@extends('layouts.app')
@section('title', 'Tambah Type Gaji Karyawan')
@section('page_title', 'Tambah Type Gaji Karyawan')
@section('content')
<div class="mx-auto max-w-2xl px-4 py-6">
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <h1 class="mb-6 text-xl font-bold text-gray-800">Tambah Type Gaji Karyawan</h1>
        <form method="POST" action="{{ route('master.type-gaji-karyawan.store') }}">
            @csrf
            @include('master-type-gaji-karyawan._form', ['typeGajiKaryawan' => null])
        </form>
    </div>
</div>
@endsection
