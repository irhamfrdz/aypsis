@extends('layouts.app')

@section('title', 'Pricelist Tujuan Kontainer Sewa')
@section('page_title', 'Pricelist Tujuan Kontainer Sewa')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
                <i class="fas fa-map-marked-alt mr-3 text-blue-600 text-2xl"></i>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Master Pricelist Tujuan Kontainer Sewa</h1>
                    <p class="text-gray-600">Kelola tarif berdasarkan tujuan untuk kontainer sewa</p>
                </div>
            </div>
            <div class="flex space-x-2">
                @can('master-pricelist-tujuan-kontainer-sewa-create')
                <a href="{{ route('master-pricelist-tujuan-kontainer-sewa.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center shadow-md">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Pricelist
                </a>
                @endcan
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Filter & Search -->
        <div class="mb-6">
            <form action="{{ route('master-pricelist-tujuan-kontainer-sewa.index') }}" method="GET" class="flex items-center space-x-2">
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari tujuan..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition duration-200">
                </div>
                <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-6 py-2 rounded-lg transition duration-200">
                    Filter
                </button>
                @if(request('search'))
                    <a href="{{ route('master-pricelist-tujuan-kontainer-sewa.index') }}" class="text-red-600 hover:text-red-800 text-sm font-medium">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto bg-gray-50 rounded-xl border border-gray-100">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tujuan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Ongkos 20ft</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Ongkos 40ft</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Keterangan</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($pricelists as $index => $item)
                    <tr class="hover:bg-blue-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $pricelists->firstItem() + $index }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">{{ $item->tujuan }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-blue-600 text-right">Rp {{ number_format($item->ongkos_truk_20ft, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-blue-600 text-right">Rp {{ number_format($item->ongkos_truk_40ft, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($item->status === 'aktif')
                                <span class="px-3 py-1 text-xs font-bold rounded-full bg-green-100 text-green-700 uppercase">Aktif</span>
                            @else
                                <span class="px-3 py-1 text-xs font-bold rounded-full bg-red-100 text-red-700 uppercase">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" title="{{ $item->keterangan }}">
                            {{ $item->keterangan ?: '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex justify-center space-x-2">
                                @can('master-pricelist-tujuan-kontainer-sewa-update')
                                <a href="{{ route('master-pricelist-tujuan-kontainer-sewa.edit', $item->id) }}" class="text-blue-600 hover:text-blue-900 bg-blue-50 p-2 rounded-lg transition duration-200">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan

                                @can('master-pricelist-tujuan-kontainer-sewa-delete')
                                <form action="{{ route('master-pricelist-tujuan-kontainer-sewa.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pricelist ini?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 p-2 rounded-lg transition duration-200">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-gray-500 italic">
                            Belum ada data pricelist tujuan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $pricelists->links() }}
        </div>
    </div>
</div>
@endsection
