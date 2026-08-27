@extends('layouts.app')

@section('title', 'Edit Pricelist Buruh Bongkar')
@section('page_title', 'Edit Pricelist Buruh Bongkar')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <a href="{{ route('master.pricelist-buruh-bongkar.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Daftar
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden max-w-2xl mx-auto">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-800">Edit Pricelist Buruh Bongkar</h2>
            <p class="text-sm text-gray-600 mt-1">Ubah data tarif buruh bongkar</p>
        </div>
        <div class="p-6">
            <form action="{{ route('master.pricelist-buruh-bongkar.update', $item) }}" method="POST">
                @method('PUT')
                @include('master.pricelist-buruh-bongkar._form')
            </form>
        </div>
    </div>
</div>
@endsection
