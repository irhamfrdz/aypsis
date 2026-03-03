@extends('layouts.app')

@section('title', 'Pricelist Biaya LOLO')

@section('content')
<div class="flex justify-between items-center mb-4">
    <h2 class="text-xl font-bold text-gray-800">Pricelist Biaya LOLO</h2>
    @can('master-pricelist-lolo-create')
    <a href="{{ route('master.pricelist-lolo.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors">
        <i class="fas fa-plus mr-2"></i> Tambah Tarif LOLO
    </a>
    @endcan
</div>

{{-- Search and Filter Section --}}
<div class="mb-6 bg-white p-4 rounded-lg shadow-sm border">
    <form method="GET" action="{{ route('master.pricelist-lolo.index') }}" class="space-y-4">
        <div class="flex flex-wrap gap-4 items-end">
            {{-- Vendor Filter --}}
            <div class="flex-1 min-w-48">
                <label for="vendor" class="block text-sm font-medium text-gray-700 mb-1">Vendor</label>
                <input type="text" id="vendor" name="vendor" value="{{ request('vendor') }}" placeholder="Cari vendor..." 
                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>

            {{-- Lokasi Filter --}}
            <div class="flex-1 min-w-48">
                <label for="lokasi" class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                <select id="lokasi" name="lokasi" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <option value="">Semua Lokasi</option>
                    <option value="Jakarta" {{ request('lokasi') == 'Jakarta' ? 'selected' : '' }}>Jakarta</option>
                    <option value="Batam" {{ request('lokasi') == 'Batam' ? 'selected' : '' }}>Batam</option>
                    <option value="Pinang" {{ request('lokasi') == 'Pinang' ? 'selected' : '' }}>Pinang</option>
                </select>
            </div>

            {{-- Ukuran Filter --}}
            <div class="min-w-32">
                <label for="size" class="block text-sm font-medium text-gray-700 mb-1">Ukuran</label>
                <select id="size" name="size" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <option value="">Semua Ukuran</option>
                    <option value="20" {{ request('size') == '20' ? 'selected' : '' }}>20'</option>
                    <option value="40" {{ request('size') == '40' ? 'selected' : '' }}>40'</option>
                    <option value="45" {{ request('size') == '45' ? 'selected' : '' }}>45'</option>
                </select>
            </div>



            {{-- Filter Buttons --}}
            <div class="flex space-x-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-search mr-2"></i> Filter
                </button>
                <a href="{{ route('master.pricelist-lolo.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-sync-alt mr-2"></i> Reset
                </a>
            </div>
        </div>
    </form>
</div>

@if(session('success'))
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-md mb-4" role="alert">
    {{ session('success') }}
</div>
@endif

<div class="overflow-x-auto shadow-md sm:rounded-lg">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50 sticky top-0 z-10 shadow-sm">
            <tr>
                <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
                <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Vendor</th>
                <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Lokasi</th>
                <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Ukuran</th>

                <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Tarif</th>
                <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($pricelists as $pricelist)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-4 py-3 whitespace-nowrap text-center text-sm text-gray-500">
                    {{ ($pricelists->currentPage() - 1) * $pricelists->perPage() + $loop->iteration }}
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-center text-sm text-gray-900">{{ $pricelist->vendor ?? '-' }}</td>
                <td class="px-4 py-3 whitespace-nowrap text-center text-sm text-gray-900">{{ $pricelist->lokasi ?? '-' }}</td>
                <td class="px-4 py-3 whitespace-nowrap text-center text-sm text-gray-900">{{ $pricelist->size }}'</td>

                <td class="px-4 py-3 whitespace-nowrap text-center text-sm font-bold text-indigo-600">{{ $pricelist->formatted_tarif }}</td>
                <td class="px-4 py-3 whitespace-nowrap text-center">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $pricelist->status === 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ strtoupper($pricelist->status) }}
                    </span>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex items-center justify-end space-x-3 text-[10px]">
                        @can('master-pricelist-lolo-update')
                        <a href="{{ route('master.pricelist-lolo.edit', $pricelist->id) }}" class="text-blue-600 hover:text-blue-800 hover:underline font-medium">Edit</a>
                        @endcan
                        @can('master-pricelist-lolo-delete')
                        <span class="text-gray-300">|</span>
                        <form action="{{ route('master.pricelist-lolo.destroy', $pricelist->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus data pricelist ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 hover:underline font-medium cursor-pointer border-none bg-transparent p-0">Hapus</button>
                        </form>
                        @endcan
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="px-4 py-10 text-center text-sm text-gray-500">
                    <div class="mb-2"><i class="fas fa-folder-open text-4xl text-gray-200"></i></div>
                    Tidak ada data pricelist LOLO.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $pricelists->links() }}
</div>
@endsection
