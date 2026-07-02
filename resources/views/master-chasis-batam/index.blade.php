@extends('layouts.app')

@section('title', 'Master Chasis Batam')
@section('page_title', 'Master Chasis Batam')

@section('content')
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Daftar Chasis Batam</h2>
                <p class="text-xs text-gray-500 mt-1">Kelola data master chasis untuk wilayah Batam.</p>
            </div>
            
            <div class="flex space-x-2 w-full md:w-auto justify-end">
                @can('master-chasis-batam-create')
                <!-- Add New Button -->
                <a href="{{ route('master.chasis-batam.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-sm">
                    <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Chasis
                </a>
                @endcan
            </div>
        </div>

        <!-- Search and Filters -->
        <form method="GET" action="{{ route('master.chasis-batam.index') }}" class="bg-gray-50 p-4 rounded-lg mb-6 border border-gray-100">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <!-- Search Input -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Cari Data</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode, tipe, kondisi, lokasi..." class="w-full pl-9 pr-3 py-1.5 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Tipe Filter -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Filter Tipe</label>
                    <select name="tipe" class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Semua Tipe</option>
                        @foreach($types as $type)
                            <option value="{{ $type }}" {{ request('tipe') == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Kondisi Filter -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Filter Kondisi</label>
                    <select name="kondisi" class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Semua Kondisi</option>
                        <option value="baik" {{ request('kondisi') == 'baik' ? 'selected' : '' }}>Baik</option>
                        <option value="rusak" {{ request('kondisi') == 'rusak' ? 'selected' : '' }}>Rusak</option>
                    </select>
                </div>

                <!-- Lokasi Filter -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Filter Lokasi</label>
                    <select name="lokasi" class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Semua Lokasi</option>
                        <option value="sm" {{ request('lokasi') == 'sm' ? 'selected' : '' }}>SM</option>
                        <option value="relasi" {{ request('lokasi') == 'relasi' ? 'selected' : '' }}>Relasi</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end mt-4 gap-2">
                @if(request()->anyFilled(['search', 'tipe', 'kondisi', 'lokasi']))
                    <a href="{{ route('master.chasis-batam.index') }}" class="px-4 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-xs font-medium transition-colors duration-150">Reset</a>
                @endif
                <button type="submit" class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-medium transition-colors duration-150 shadow-sm">Cari</button>
            </div>
        </form>

        @if (session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg relative mb-6 text-xs flex items-center" role="alert">
                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if ($chasisList->isEmpty())
            <div class="text-center py-10 bg-gray-50 rounded-lg border border-dashed border-gray-200">
                <p class="text-gray-500 text-xs">Belum ada data chasis ditemukan.</p>
            </div>
        @else
            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="min-w-full bg-white resizable-table" id="masterChasisTable">
                    <thead>
                        <tr class="bg-gray-50 text-left text-gray-500 text-[10px] uppercase font-semibold border-b border-gray-200">
                            <th class="resizable-th py-3 px-4" style="position: relative; width: 60px;">No<div class="resize-handle"></div></th>
                            <th class="resizable-th py-3 px-4" style="position: relative;">Kode Chasis<div class="resize-handle"></div></th>
                            <th class="resizable-th py-3 px-4" style="position: relative;">Tipe Chasis<div class="resize-handle"></div></th>
                            <th class="resizable-th py-3 px-4" style="position: relative;">Kondisi<div class="resize-handle"></div></th>
                            <th class="resizable-th py-3 px-4" style="position: relative;">Lokasi<div class="resize-handle"></div></th>
                            <th class="resizable-th py-3 px-4" style="position: relative;">Tgl Terakhir Pakai<div class="resize-handle"></div></th>
                            <th class="resizable-th py-3 px-4" style="position: relative;">Catatan<div class="resize-handle"></div></th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-xs divide-y divide-gray-200">
                        @foreach ($chasisList as $chasis)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="py-3 px-4 font-medium text-gray-400">{{ ($chasisList->currentPage() - 1) * $chasisList->perPage() + $loop->iteration }}</td>
                                <td class="py-3 px-4 font-semibold text-indigo-700">
                                    <a href="{{ route('master.chasis-batam.show', $chasis) }}" class="hover:underline">{{ $chasis->kode }}</a>
                                </td>
                                <td class="py-3 px-4">
                                    @if($chasis->tipe)
                                        <span class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded-full text-[10px] font-medium border border-blue-100">{{ $chasis->tipe }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    @if($chasis->kondisi === 'baik')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Baik
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Rusak
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    @if($chasis->lokasi === 'sm')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 uppercase">
                                            SM
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 uppercase">
                                            Relasi
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">{{ $chasis->tanggal_terakhir_pakai ? $chasis->tanggal_terakhir_pakai->format('d-m-Y') : '-' }}</td>
                                <td class="py-3 px-4 max-w-xs truncate text-gray-500" title="{{ $chasis->catatan }}">{{ $chasis->catatan ?? '-' }}</td>
                                <td class="py-3 px-4 text-right space-x-1.5 whitespace-nowrap">
                                    <a href="{{ route('master.chasis-batam.show', $chasis) }}" class="text-gray-500 hover:text-indigo-600 hover:underline">Detail</a>
                                    
                                    @can('master-chasis-batam-update')
                                    <span class="text-gray-300">|</span>
                                    <a href="{{ route('master.chasis-batam.edit', $chasis) }}" class="text-blue-600 hover:text-blue-800 hover:underline">Edit</a>
                                    @endcan

                                    <!-- Audit Log Button -->
                                    @can('audit-log-view')
                                        <span class="text-gray-300">|</span>
                                        <button type="button"
                                                class="audit-log-btn text-purple-600 hover:text-purple-800 hover:underline font-medium cursor-pointer"
                                                data-model-type="{{ get_class($chasis) }}"
                                                data-model-id="{{ $chasis->id }}"
                                                data-item-name="{{ $chasis->kode }} (Tipe: {{ $chasis->tipe ?? '-' }})"
                                                title="Lihat Riwayat Perubahan">
                                            Riwayat
                                        </button>
                                    @endcan

                                    @can('master-chasis-batam-delete')
                                    <span class="text-gray-300">|</span>
                                    <form action="{{ route('master.chasis-batam.destroy', $chasis) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data chasis {{ $chasis->kode }} ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 hover:underline">Hapus</button>
                                    </form>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $chasisList->links() }}
            </div>
        @endif
    </div>

    <!-- Audit Log Modal -->
    @include('components.audit-log-modal')

@endsection

@include('components.resizable-table')

@push('scripts')
<script>
$(document).ready(function() {
    if (typeof initResizableTable === 'function') {
        initResizableTable('masterChasisTable');
    }
});
</script>
@endpush
