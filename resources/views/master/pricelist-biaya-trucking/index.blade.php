@extends('layouts.app')

@section('page_title', 'Master Pricelist Biaya Trucking')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Pricelist Biaya Trucking</h1>
                <p class="text-gray-600 mt-1">Kelola data harga biaya trucking</p>
            </div>
            @can('master-pricelist-biaya-trucking-create')
            <a href="{{ route('master.pricelist-biaya-trucking.create') }}" 
               class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Pricelist
            </a>
            @endcan
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    {{-- Filter Section --}}
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form action="{{ route('master.pricelist-biaya-trucking.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="rute" class="block text-sm font-medium text-gray-700 mb-1">Cari Rute</label>
                <input type="text" name="rute" id="rute" value="{{ request('rute') }}" 
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" 
                       placeholder="Contoh: Jakarta - Bandung">
            </div>
            <div>
                <label for="jenis_kendaraan" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kendaraan</label>
                <input type="text" name="jenis_kendaraan" id="jenis_kendaraan" value="{{ request('jenis_kendaraan') }}" 
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" 
                       placeholder="Contoh: Fuso, Tronton">
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="non-aktif" {{ request('status') == 'non-aktif' ? 'selected' : '' }}>Non-Aktif</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-gray-800 text-white px-4 py-2.5 rounded-md hover:bg-gray-700 transition">
                    Filter Data
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rute & Tujuan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kendaraan</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Biaya</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Berlaku</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pricelists as $index => $pricelist)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ ($pricelists->currentPage() - 1) * $pricelists->perPage() + $index + 1 }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $pricelist->rute }}</div>
                            <div class="text-sm text-gray-500">{{ $pricelist->tujuan ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $pricelist->jenis_kendaraan ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-gray-900">
                            Rp {{ number_format($pricelist->biaya, 0, ',', '.') }}
                            <span class="text-xs text-gray-500 font-normal">/ {{ $pricelist->satuan }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div>{{ $pricelist->tanggal_berlaku ? $pricelist->tanggal_berlaku->format('d M Y') : '-' }}</div>
                            <div class="text-xs text-gray-500">
                                s/d {{ $pricelist->tanggal_berakhir ? $pricelist->tanggal_berakhir->format('d M Y') : 'Seterusnya' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($pricelist->status === 'aktif')
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 border border-green-200">Aktif</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800 border border-red-200">Non-Aktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex justify-center gap-2">
                                @can('master-pricelist-biaya-trucking-update')
                                <a href="{{ route('master.pricelist-biaya-trucking.edit', $pricelist) }}" 
                                   class="text-blue-600 hover:text-blue-900 p-1 rounded hover:bg-blue-50" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                @endcan
                                
                                @can('master-pricelist-biaya-trucking-delete')
                                <form action="{{ route('master.pricelist-biaya-trucking.destroy', $pricelist) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50" title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-lg font-medium text-gray-900">Tidak ada data ditemukan</p>
                                <p class="text-sm text-gray-500">Coba ubah filter pencarian atau tambah data baru.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $pricelists->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
