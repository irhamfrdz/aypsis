@extends('layouts.app')

@section('title', 'Master Pricelist CAT')
@section('page_title', 'Master Pricelist CAT')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4 text-gray-800">Daftar Pricelist CAT</h2>

    <!-- Filters -->
    <div class="bg-gray-50 rounded-lg p-4 mb-6">
        <form method="GET" action="{{ route('master.pricelist-cat.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Vendor</label>
                <input type="text" name="vendor" value="{{ request('vendor') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="Cari vendor...">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status CAT</label>
                <select name="jenis_cat" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Semua Status</option>
                    <option value="cat_sebagian" {{ request('jenis_cat') == 'cat_sebagian' ? 'selected' : '' }}>Cat Sebagian</option>
                    <option value="cat_full" {{ request('jenis_cat') == 'cat_full' ? 'selected' : '' }}>Cat Full</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ukuran Kontainer</label>
                <select name="ukuran_kontainer" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Semua Ukuran</option>
                    <option value="20ft" {{ request('ukuran_kontainer') == '20ft' ? 'selected' : '' }}>20ft</option>
                    <option value="40ft" {{ request('ukuran_kontainer') == '40ft' ? 'selected' : '' }}>40ft</option>
                    <option value="40ft HC" {{ request('ukuran_kontainer') == '40ft HC' ? 'selected' : '' }}>40ft HC</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="Cari...">
            </div>
            <div class="flex items-end space-x-2 md:col-span-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                    Filter
                </button>
                <a href="{{ route('master.pricelist-cat.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Action Buttons -->
    <div class="mb-4 flex flex-wrap gap-2">
        @can('master-pricelist-cat-create')
        <a href="{{ route('master.pricelist-cat.create') }}" class="inline-block bg-blue-600 text-white py-2 px-4 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors duration-200">
            + Tambah Pricelist CAT
        </a>
        @endcan
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Table -->
    <div class="overflow-x-auto shadow-md sm:rounded-lg">
        <table class="min-w-full bg-white divide-y divide-gray-200 text-xs">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">No</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Vendor</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Status CAT</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">Ukuran</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Tarif</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($pricelists as $index => $pricelist)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                        {{ $pricelists->firstItem() + $index }}
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $pricelist->vendor }}
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                            {{ $pricelist->jenis_cat == 'cat_full' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ $pricelist->jenis_cat == 'cat_full' ? 'Cat Full' : 'Cat Sebagian' }}
                        </span>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                        {{ $pricelist->ukuran_kontainer }}
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                        {{ $pricelist->tarif ? 'Rp ' . number_format($pricelist->tarif, 0, ',', '.') : '-' }}
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            @can('master-pricelist-cat-update')
                            <a href="{{ route('master.pricelist-cat.edit', $pricelist) }}"
                               class="text-indigo-600 hover:text-indigo-900">Edit</a>
                            @endcan
                            @can('master-pricelist-cat-delete')
                            <form method="POST" action="{{ route('master.pricelist-cat.destroy', $pricelist) }}"
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus pricelist CAT ini?')"
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
                    <td colspan="6" class="px-4 py-4 text-center text-gray-500">
                        Tidak ada data pricelist CAT ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($pricelists->hasPages())
    <div class="mt-6">
        {{ $pricelists->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection
