@extends('layouts.app')

@section('title', 'Master Vendor Supir')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Master Vendor Supir</h1>
                <p class="text-gray-600 mt-1">Kelola data vendor supir</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                @can('master-vendor-supir-create')
                <a href="{{ route('master.vendor-supir.create') }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm rounded-lg font-medium transition duration-200 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Tambah Vendor Supir
                </a>
                @endcan
            </div>
        </div>

        <!-- Notifikasi Sukses -->
        @if (session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Filters -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <form method="GET" action="{{ route('master.vendor-supir.index') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cari Vendor</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Nama vendor..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div class="flex items-end space-x-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm rounded-lg font-medium transition duration-200">
                        Cari
                    </button>
                    <a href="{{ route('master.vendor-supir.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 text-sm rounded-lg font-medium transition duration-200">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto bg-white border border-gray-200 rounded-lg">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Vendor</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. HP</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alamat</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($vendorSupirs as $index => $vendor)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $vendorSupirs->firstItem() + $index }}
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $vendor->nama_vendor }}
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $vendor->no_hp ?: '-' }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-900">
                            {{ $vendor->alamat ?: '-' }}
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                @can('master-vendor-supir-view')
                                <a href="{{ route('master.vendor-supir.show', $vendor) }}"
                                   class="text-blue-600 hover:text-blue-900">Lihat</a>
                                @endcan
                                @can('master-vendor-supir-update')
                                <a href="{{ route('master.vendor-supir.edit', $vendor) }}"
                                   class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                @endcan
                                @can('master-vendor-supir-delete')
                                <form method="POST" action="{{ route('master.vendor-supir.destroy', $vendor) }}"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus vendor ini?')"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                            Tidak ada data vendor supir
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($vendorSupirs->hasPages())
        <div class="mt-6">
            {{ $vendorSupirs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
