@extends('layouts.app')

@section('title', 'Detail Vendor/Bengkel')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Detail Vendor/Bengkel</h1>
                    <p class="text-gray-600 mt-1">Informasi lengkap vendor atau bengkel</p>
                </div>
                <div class="flex space-x-2">
                    @can('master-vendor-bengkel.update')
                    <a href="{{ route('master.vendor-bengkel.edit', $vendorBengkel) }}"
                       class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>
                    @endcan
                    <a href="{{ route('master.vendor-bengkel.index') }}"
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition duration-200 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>

            <!-- Detail Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Dasar</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Nama Bengkel/Vendor</label>
                            <p class="text-sm text-gray-900 mt-1">{{ $vendorBengkel->nama_bengkel }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600">Keterangan</label>
                            <p class="text-sm text-gray-900 mt-1">{{ $vendorBengkel->keterangan ?? '-' }}</p>
                        </div>
                </div>

                <!-- Additional Information -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Tambahan</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Dibuat Oleh</label>
                            <p class="text-sm text-gray-900 mt-1">{{ $vendorBengkel->creator->username ?? '-' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600">Tanggal Dibuat</label>
                            <p class="text-sm text-gray-900 mt-1">{{ $vendorBengkel->created_at->format('d/m/Y H:i') }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600">Terakhir Diperbarui</label>
                            <p class="text-sm text-gray-900 mt-1">{{ $vendorBengkel->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Audit Information -->
            <div class="mt-6 bg-blue-50 rounded-lg p-4">
                <h4 class="text-sm font-medium text-blue-800 mb-2">Informasi Audit</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-blue-600">Dibuat:</span>
                        <span class="text-blue-800">{{ $vendorBengkel->created_at->format('d/m/Y H:i') }}</span>
                        @if($vendorBengkel->creator)
                            <span class="text-blue-600">oleh</span>
                            <span class="text-blue-800">{{ $vendorBengkel->creator->username }}</span>
                        @endif
                    </div>
                    <div>
                        <span class="text-blue-600">Diperbarui:</span>
                        <span class="text-blue-800">{{ $vendorBengkel->updated_at->format('d/m/Y H:i') }}</span>
                        @if($vendorBengkel->updater)
                            <span class="text-blue-600">oleh</span>
                            <span class="text-blue-800">{{ $vendorBengkel->updater->username }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Delete Action -->
            @can('master-vendor-bengkel.delete')
            <div class="mt-6 border-t pt-6">
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-medium text-red-800">Hapus Vendor/Bengkel</h4>
                            <p class="text-sm text-red-600 mt-1">
                                Tindakan ini tidak dapat dibatalkan. Data vendor/bengkel akan dihapus secara permanen.
                            </p>
                        </div>
                        <form method="POST" action="{{ route('master.vendor-bengkel.destroy', $vendorBengkel) }}"
                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus vendor/bengkel ini? Semua data terkait akan hilang.')"
                              class="ml-4">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                                Hapus Vendor/Bengkel
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endcan
        </div>
    </div>
</div>
@endsection
