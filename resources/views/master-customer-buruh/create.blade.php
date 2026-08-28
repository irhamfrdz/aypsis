@extends('layouts.app')

@section('title', 'Tambah Customer Buruh')
@section('page_title', 'Tambah Customer Buruh Bongkar Muat')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6 font-sans">
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <h2 class="text-xl font-bold text-gray-800">Form Tambah Customer Buruh</h2>
        <a href="{{ route('master-customer-buruh.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded transition duration-300 text-sm">
            Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('master-customer-buruh.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="nama_customer" class="block text-sm font-medium text-gray-700 mb-1">Nama Customer <span class="text-red-500">*</span></label>
                <input type="text" name="nama_customer" id="nama_customer" value="{{ old('nama_customer') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
            </div>

            <div>
                <label for="pic" class="block text-sm font-medium text-gray-700 mb-1">PIC (Penanggung Jawab)</label>
                <input type="text" name="pic" id="pic" value="{{ old('pic') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>

            <div>
                <label for="no_telp" class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                <input type="text" name="no_telp" id="no_telp" value="{{ old('no_telp') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>

            <div>
                <label for="is_active" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <div class="mt-2">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-offset-0 focus:ring-indigo-200 focus:ring-opacity-50" checked>
                        <span class="ml-2">Aktif</span>
                    </label>
                </div>
            </div>

            <div class="md:col-span-2">
                <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                <textarea name="alamat" id="alamat" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('alamat') }}</textarea>
            </div>

            <div class="md:col-span-2">
                <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                <textarea name="keterangan" id="keterangan" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('keterangan') }}</textarea>
            </div>
        </div>

        <div class="flex justify-end pt-4 border-t">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded transition duration-300">
                Simpan
            </button>
        </div>
    </form>
</div>
@endsection
