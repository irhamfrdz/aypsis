@extends('layouts.app')

@section('title', 'Edit Belanja Amprahan')
@section('page_title', 'Edit Belanja Amprahan')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <form action="{{ route('belanja-amprahan.update', $item->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nomor</label>
                    <input type="text" name="nomor" value="{{ old('nomor', $item->nomor) }}" class="w-full px-4 py-2 border rounded" placeholder="Nomor dokumen">
                    @error('nomor')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', $item->tanggal?->format('Y-m-d')) }}" class="w-full px-4 py-2 border rounded">
                    @error('tanggal')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Supplier</label>
                    <input type="text" name="supplier" value="{{ old('supplier', $item->supplier) }}" class="w-full px-4 py-2 border rounded">
                    @error('supplier')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Total</label>
                    <input type="number" step="0.01" name="total" value="{{ old('total', $item->total) }}" class="w-full px-4 py-2 border rounded">
                    @error('total')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                    <textarea name="keterangan" class="w-full px-4 py-2 border rounded" rows="3">{{ old('keterangan', $item->keterangan) }}</textarea>
                    @error('keterangan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                <a href="{{ route('belanja-amprahan.index') }}" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded">Batal</a>
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
