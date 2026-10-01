@extends('layouts.app')

@section('title', 'Master Pricelist LOLO Batam')
@section('page_title', 'Master Pricelist LOLO Batam')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Master Pricelist LOLO Batam</h1>
            <p class="text-sm text-gray-500">Kelola master tarif LOLO untuk operasional di wilayah Batam</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('tagihan-lolo-batam.index') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                <i class="fas fa-file-invoice-dollar mr-2"></i> Menu Tagihan LOLO Batam
            </a>
            @can('master-pricelist-lolo-batam-create')
            <a href="{{ route('master.pricelist-lolo-batam.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                <i class="fas fa-plus mr-2"></i> Tambah Tarif LOLO
            </a>
            @endcan
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-lg text-emerald-800 text-sm flex items-center shadow-sm">
        <i class="fas fa-check-circle mr-3 text-lg text-emerald-500"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    {{-- Search and Filter Section --}}
    <div class="mb-6 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <form method="GET" action="{{ route('master.pricelist-lolo-batam.index') }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Vendor</label>
                    <input type="text" name="vendor" value="{{ request('vendor') }}" placeholder="Cari vendor..." 
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Ukuran</label>
                    <select name="size" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Semua Ukuran</option>
                        <option value="20" {{ request('size') == '20' ? 'selected' : '' }}>20 Feet</option>
                        <option value="40" {{ request('size') == '40' ? 'selected' : '' }}>40 Feet</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Tipe</label>
                    <select name="tipe" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Semua Tipe</option>
                        <option value="FULL" {{ request('tipe') == 'FULL' ? 'selected' : '' }}>FULL</option>
                        <option value="EMPTY" {{ request('tipe') == 'EMPTY' ? 'selected' : '' }}>EMPTY</option>
                        <option value="ALL" {{ request('tipe') == 'ALL' ? 'selected' : '' }}>ALL</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="non-aktif" {{ request('status') == 'non-aktif' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold transition-colors flex items-center justify-center shadow-sm">
                        <i class="fas fa-search mr-1.5"></i> Filter
                    </button>
                    <a href="{{ route('master.pricelist-lolo-batam.index') }}" class="p-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg text-sm transition-colors" title="Reset Filter">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Pricelist Table Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/75">
                    <tr>
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Vendor</th>
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nama Biaya / Kegiatan</th>
                        <th class="px-6 py-3.5 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Size</th>
                        <th class="px-6 py-3.5 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Tipe</th>
                        <th class="px-6 py-3.5 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Tarif (Rp)</th>
                        <th class="px-6 py-3.5 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3.5 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($pricelists as $index => $item)
                    <tr class="hover:bg-gray-50/80 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-medium">
                            {{ $pricelists->firstItem() + $index }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                            {{ $item->vendor ?: '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <div class="font-medium text-gray-800">{{ $item->nama_biaya }}</div>
                            <div class="text-xs text-gray-500">{{ $item->kegiatan }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                {{ $item->size }}'
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $item->tipe === 'FULL' ? 'bg-purple-100 text-purple-700' : ($item->tipe === 'EMPTY' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-700') }}">
                                {{ $item->tipe }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-emerald-700">
                            {{ $item->formatted_tarif }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                            @if($item->status === 'aktif')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                    Non-Aktif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                            <div class="flex justify-center items-center gap-2">
                                @can('master-pricelist-lolo-batam-update')
                                <a href="{{ route('master.pricelist-lolo-batam.edit', $item->id) }}" class="p-1.5 text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 rounded-lg transition-colors" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('master-pricelist-lolo-batam-delete')
                                <form action="{{ route('master.pricelist-lolo-batam.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tarif LOLO ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl text-gray-300 mb-3 block"></i>
                            <p class="text-base font-semibold">Belum ada data Master Pricelist LOLO Batam.</p>
                            <p class="text-xs text-gray-400 mt-1">Tambahkan tarif baru menggunakan tombol Tambah Tarif LOLO di atas.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pricelists->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $pricelists->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
