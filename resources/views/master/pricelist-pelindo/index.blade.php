@extends('layouts.app')

@section('title', 'Master Pricelist Pelindo')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Master Pricelist Pelindo</h1>
                <p class="text-gray-600 mt-1">Kelola data tarif Pelindo (tidak menggunakan pelabuhan)</p>
            </div>
            <div class="flex items-center space-x-2">
                @can('master-pricelist-pelindo-create')
                <a href="{{ route('master.pricelist-pelindo.create') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg shadow-sm transition duration-200">
                    <i class="fas fa-plus mr-2"></i> Tambah Pricelist Pelindo
                </a>
                @endcan
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6">
            <form method="GET" action="{{ route('master.pricelist-pelindo.index') }}" class="mb-6">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                    <div class="md:col-span-12">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </span>
                            <input type="text" name="search" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="Cari Kegiatan, Ukuran, atau Keterangan..." value="{{ request('search') }}">
                        </div>
                    </div>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-16 text-center">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kegiatan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-32">Ukuran</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-32">Status Kontainer</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider w-40">Tarif</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Keterangan</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-32">Status</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($pricelists as $item)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-900 text-center">
                                {{ ($pricelists->currentPage() - 1) * $pricelists->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-4 py-3 text-xs font-medium text-gray-900 whitespace-normal">
                                {{ $item->kegiatan }}
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-900">
                                {{ $item->ukuran ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-900 capitalize">
                                {{ $item->status_kontainer ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-xs font-semibold text-gray-900 text-right">
                                {{ $item->formatted_tarif }}
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600 whitespace-normal max-w-xs">
                                {{ $item->keterangan ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                <span class="px-3 py-1 inline-flex text-xxs leading-5 font-semibold rounded-full {{ $item->status === 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center text-xs font-medium">
                                <div class="flex items-center justify-center gap-2">
                                    @can('master-pricelist-pelindo-update')
                                    <a href="{{ route('master.pricelist-pelindo.edit', $item->id) }}" class="inline-flex items-center px-2.5 py-1.5 bg-yellow-100 hover:bg-yellow-200 text-yellow-800 rounded-md transition duration-150" title="Edit">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                    @endcan
                                    @can('master-pricelist-pelindo-delete')
                                    <form action="{{ route('master.pricelist-pelindo.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-2.5 py-1.5 bg-red-100 hover:bg-red-200 text-red-800 rounded-md transition duration-150" title="Hapus">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center">
                                <p class="text-gray-500 text-sm">Tidak ada data pricelist pelindo.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $pricelists->appends(request()->input())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
