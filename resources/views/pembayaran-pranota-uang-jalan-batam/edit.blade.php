@extends('layouts.app')

@section('title', 'Edit Pembayaran Pranota Uang Jalan Batam')
@section('page_title', 'Edit Pembayaran Pranota Uang Jalan Batam')

@section('content')
    <div class="bg-white shadow-lg rounded-lg p-4 max-w-6xl mx-auto">
        @php
            $inputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-sm p-2 transition-colors";
            $readonlyInputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm text-sm p-2";
            $labelClasses = "block text-xs font-medium text-gray-700 mb-1";
        @endphp

        @if(session('success'))
            <div class="mb-3 p-3 rounded-lg bg-green-50 border border-green-200 text-green-800 text-sm">
                <strong>Berhasil!</strong> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-3 p-4 rounded-lg bg-red-50 border border-red-200 text-red-800 text-sm">
                <strong>Gagal!</strong> {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('pembayaran-pranota-uang-jalan-batam.update', $pembayaranPranotaUangJalanBatam->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200 mb-3">
                <h4 class="text-sm font-semibold text-gray-800 mb-2">Edit Data Pembayaran Batam</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="{{ $labelClasses }}">Nomor Accurate</label>
                        <input type="text" name="nomor_accurate" value="{{ old('nomor_accurate', $pembayaranPranotaUangJalanBatam->nomor_accurate) }}" class="{{ $inputClasses }}">
                    </div>
                    <div>
                        <label class="{{ $labelClasses }}">Tanggal Pembayaran</label>
                        <input type="date" name="tanggal_pembayaran" value="{{ old('tanggal_pembayaran', $pembayaranPranotaUangJalanBatam->tanggal_pembayaran ? $pembayaranPranotaUangJalanBatam->tanggal_pembayaran->format('Y-m-d') : date('Y-m-d')) }}" class="{{ $inputClasses }}">
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200 mb-3">
                <h4 class="text-sm font-semibold text-gray-800 mb-2">Informasi (Read Only)</h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
                    <div><span class="text-gray-500">Nomor:</span><p class="font-medium">{{ $pembayaranPranotaUangJalanBatam->nomor_pembayaran }}</p></div>
                    <div><span class="text-gray-500">Bank:</span><p class="font-medium">{{ $pembayaranPranotaUangJalanBatam->bank }}</p></div>
                    <div><span class="text-gray-500">Total:</span><p class="font-medium">Rp {{ number_format($pembayaranPranotaUangJalanBatam->total_tagihan_setelah_penyesuaian, 0, ',', '.') }}</p></div>
                    <div><span class="text-gray-500">Status:</span><p class="font-medium">{{ ucfirst($pembayaranPranotaUangJalanBatam->status_pembayaran) }}</p></div>
                </div>
            </div>

            <div class="flex justify-between items-center">
                <a href="{{ route('pembayaran-pranota-uang-jalan-batam.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded text-sm font-medium">Kembali</a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm font-medium">Update Pembayaran</button>
            </div>
        </form>
    </div>
@endsection
