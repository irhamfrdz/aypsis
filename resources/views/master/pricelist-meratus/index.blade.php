@extends('layouts.app')

@section('title', 'Pricelist Meratus')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Pricelist Meratus</h1>
                <p class="text-gray-600 mt-1">Kelola data tarif Meratus (Jenis Biaya, Size, Harga)</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('master.pricelist-meratus.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg shadow-sm transition duration-200">
                    <i class="fas fa-plus mr-2"></i> Tambah Pricelist
                </a>
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

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6">
            <form method="GET" action="{{ route('master.pricelist-meratus.index') }}" class="mb-6">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                    <div class="md:col-span-12">
                        <input type="text" name="search" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Cari Jenis Biaya, Lokasi, atau Size..." value="{{ request('search') }}">
                    </div>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Biaya</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($pricelistMeratus as $item)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900 text-center">
                                {{ ($pricelistMeratus->currentPage() - 1) * $pricelistMeratus->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-3 py-2 text-xs font-medium text-gray-900">{{ $item->jenis_biaya }}</td>
                            <td class="px-3 py-2 text-xs text-gray-900">{{ $item->lokasi ?? '-' }}</td>
                            <td class="px-3 py-2 text-xs text-gray-900">{{ $item->size ?? '-' }}</td>
                            <td class="px-3 py-2 text-xs text-gray-900 text-right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                            <td class="px-3 py-2 whitespace-nowrap text-center">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->status === 'Aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-center text-xs font-medium">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('master.pricelist-meratus.edit', $item->id) }}" class="inline-flex items-center px-2 py-1 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 rounded transition duration-150" title="Edit">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                    <form action="{{ route('master.pricelist-meratus.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-2 py-1 bg-red-100 hover:bg-red-200 text-red-700 rounded transition duration-150" title="Hapus">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-3 py-8 text-center">
                                <p class="text-gray-500 text-sm">Tidak ada data pricelist Meratus.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $pricelistMeratus->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
