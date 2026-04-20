@extends('layouts.app')

@section('title', 'Master Vendor Amprahan')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Master Vendor Amprahan</h1>
                <p class="text-gray-600 mt-1">Kelola data vendor amprahan</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                @can('master-vendor-amprahan-create')
                <a href="{{ route('master.vendor-amprahan.create') }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm rounded-lg font-medium transition duration-200 flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Vendor Amprahan
                </a>
                @endcan
            </div>
        </div>

        <!-- Notifikasi Sukses -->
        @if (session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Filters -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <form method="GET" action="{{ route('master.vendor-amprahan.index') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cari Vendor</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Nama toko or alamat..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div class="flex items-end space-x-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm rounded-lg font-medium transition duration-200">
                        Cari
                    </button>
                    <a href="{{ route('master.vendor-amprahan.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 text-sm rounded-lg font-medium transition duration-200">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto bg-white border border-gray-200 rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Toko</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alamat Toko</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($vendorAmprahans as $index => $vendor)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $vendorAmprahans->firstItem() + $index }}
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $vendor->nama_toko }}
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm text-gray-700">
                                {{ $vendor->alamat_toko ?: '-' }}
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-3">
                                @can('master-vendor-amprahan-view')
                                <a href="{{ route('master.vendor-amprahan.show', $vendor) }}"
                                   class="text-blue-600 hover:text-blue-900" title="Lihat">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endcan
                                @can('master-vendor-amprahan-update')
                                <a href="{{ route('master.vendor-amprahan.edit', $vendor) }}"
                                   class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('master-vendor-amprahan-delete')
                                <form method="POST" action="{{ route('master.vendor-amprahan.destroy', $vendor) }}"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus vendor ini?')"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-store-slash text-4xl text-gray-300 mb-4"></i>
                                <p class="text-lg font-medium">Tidak ada data vendor amprahan</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($vendorAmprahans->hasPages())
        <div class="mt-6">
            {{ $vendorAmprahans->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
