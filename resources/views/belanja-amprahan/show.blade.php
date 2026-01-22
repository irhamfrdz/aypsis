@extends('layouts.app')

@section('title', 'Detail Belanja Amprahan')
@section('page_title', 'Detail Belanja Amprahan')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h3 class="text-lg font-semibold mb-4">Detail</h3>
        <table class="clean-table w-full">
            <tr><td class="font-medium w-48">Nomor</td><td>{{ $item->nomor }}</td></tr>
            <tr><td class="font-medium">Tanggal</td><td>{{ $item->tanggal?->format('Y-m-d') }}</td></tr>
            <tr><td class="font-medium">Supplier</td><td>{{ $item->supplier }}</td></tr>
            <tr><td class="font-medium">Total</td><td>Rp {{ number_format($item->total,2,',','.') }}</td></tr>
            <tr><td class="font-medium">Keterangan</td><td>{{ $item->keterangan }}</td></tr>
        </table>

        <div class="mt-4">
            <a href="{{ route('belanja-amprahan.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded">Kembali</a>
            <a href="{{ route('belanja-amprahan.edit', $item->id) }}" class="px-4 py-2 bg-blue-600 text-white rounded">Edit</a>
        </div>
    </div>
</div>
@endsection
