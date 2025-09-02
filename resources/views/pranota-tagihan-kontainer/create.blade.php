@extends('layouts.app')

@section('title', 'Buat Pranota Tagihan Kontainer')
@section('page_title', 'Buat Pranota Tagihan Kontainer')

@section('content')
<div class="bg-white shadow rounded p-6">
    <form action="{{ route('pranota-tagihan-kontainer.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label class="block text-sm font-medium">Nomor</label>
                <input type="text" name="nomor" class="mt-1 block w-full rounded border-gray-300" required>
            </div>
            <div>
                <label class="block text-sm font-medium">Tanggal</label>
                <input type="date" name="tanggal" class="mt-1 block w-full rounded border-gray-300" required>
            </div>
            <div>
                <label class="block text-sm font-medium">Periode</label>
                <input type="text" name="periode" placeholder="2025-08" class="mt-1 block w-full rounded border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-medium">Vendor</label>
                <input type="text" name="vendor" class="mt-1 block w-full rounded border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-medium">Keterangan</label>
                <textarea name="keterangan" class="mt-1 block w-full rounded border-gray-300"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium">Total</label>
                <input type="number" step="0.01" name="total" class="mt-1 block w-full rounded border-gray-300" value="0">
            </div>
            <div class="text-right">
                <button class="px-4 py-2 bg-indigo-600 text-white rounded">Simpan</button>
            </div>
        </div>
    </form>
</div>
@endsection
