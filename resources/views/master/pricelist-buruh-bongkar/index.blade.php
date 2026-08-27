@extends('layouts.app')

@section('title', 'Pricelist Buruh Bongkar')
@section('page_title', 'Pricelist Buruh Bongkar')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Pricelist Buruh Bongkar</h2>
            <p class="text-gray-600 text-sm mt-1">Kelola daftar tarif buruh bongkar</p>
        </div>
        <div class="flex gap-2">
            @can('master-pricelist-buruh-bongkar-create')
            <a href="{{ route('master.pricelist-buruh-bongkar.create') }}" 
               class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Pricelist
            </a>
            @endcan
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <!-- Error Message -->
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
        <form method="GET" action="{{ route('master.pricelist-buruh-bongkar.index') }}" class="flex gap-4">
            <div class="flex-1">
                <input type="text" 
                       name="q" 
                       value="{{ request('q') }}" 
                       placeholder="Cari lokasi atau size..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
            </div>
            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                Cari
            </button>
            @if(request()->has('q') || request()->has('status'))
                <a href="{{ route('master.pricelist-buruh-bongkar.index') }}" class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg transition">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nominal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($items as $item)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ ($items->currentPage() - 1) * $items->perPage() + $loop->iteration }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                        {{ $item->lokasi }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $item->size ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        Rp {{ number_format($item->nominal, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($item->status)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Aktif
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Tidak Aktif
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                        {{ $item->keterangan ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex gap-2">
                            @can('master-pricelist-buruh-bongkar-update')
                            <a href="{{ route('master.pricelist-buruh-bongkar.edit', $item) }}" 
                               class="text-blue-600 hover:text-blue-900">
                                Edit
                            </a>
                            @endcan
                            @can('master-pricelist-buruh-bongkar-delete')
                            <form action="{{ route('master.pricelist-buruh-bongkar.destroy', $item) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Yakin ingin menghapus data ini?')" 
                                  class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    Hapus
                                </button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        Tidak ada data pricelist buruh bongkar
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if($items->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $items->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
