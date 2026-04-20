@extends('layouts.app')

@section('title', 'Detail Vendor Amprahan')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Detail Vendor Amprahan</h1>
            <div class="flex space-x-2">
                @can('master-vendor-amprahan-update')
                <a href="{{ route('master.vendor-amprahan.edit', $vendorAmprahan) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-200">
                    Edit
                </a>
                @endcan
                <a href="{{ route('master.vendor-amprahan.index') }}" class="text-gray-600 hover:text-gray-800 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>
        </div>

        <div class="space-y-6">
            <div class="border-b border-gray-100 pb-4">
                <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Nama Toko</h4>
                <p class="text-lg text-gray-900 font-medium">{{ $vendorAmprahan->nama_toko }}</p>
            </div>

            <div class="border-b border-gray-100 pb-4">
                <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Alamat Toko</h4>
                <p class="text-gray-700 whitespace-pre-wrap">{{ $vendorAmprahan->alamat_toko ?: 'Tidak ada alamat' }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Dibuat Oleh</h4>
                    <p class="text-sm text-gray-900">{{ $vendorAmprahan->creator->karyawan->nama_lengkap ?? 'System' }}</p>
                    <p class="text-xs text-gray-500">{{ $vendorAmprahan->created_at->format('d M Y H:i') }}</p>
                </div>
                <div>
                    <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Diperbarui Oleh</h4>
                    <p class="text-sm text-gray-900">{{ $vendorAmprahan->updater->karyawan->nama_lengkap ?? 'System' }}</p>
                    <p class="text-xs text-gray-500">{{ $vendorAmprahan->updated_at->format('d M Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
