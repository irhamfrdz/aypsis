@extends('layouts.app')

@section('title', 'Detail Vendor Supir')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Detail Vendor Supir</h1>
            <div class="flex space-x-2">
                <a href="{{ route('master.vendor-supir.edit', $vendorSupir) }}"
                   class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 text-sm rounded-lg font-medium transition duration-200">
                    Edit
                </a>
                <a href="{{ route('master.vendor-supir.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 text-sm rounded-lg font-medium transition duration-200">
                    Kembali
                </a>
            </div>
        </div>

        <div class="border-t border-gray-200 py-4">
            <dl class="grid grid-cols-1 gap-y-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Nama Vendor</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $vendorSupir->nama_vendor }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">No. HP</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $vendorSupir->no_hp ?: '-' }}</dd>
                </div>

                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Alamat</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $vendorSupir->alamat ?: '-' }}</dd>
                </div>

                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Keterangan</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $vendorSupir->keterangan ?: '-' }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Terakhir Diperbarui</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $vendorSupir->updated_at->format('d M Y H:i') }}</dd>
                </div>
            </dl>
        </div>
    </div>
</div>
@endsection
