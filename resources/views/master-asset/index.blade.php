@extends('layouts.app')

@section('title', 'Master Asset')
@section('page_title', 'Master Asset')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 tracking-tight flex items-center gap-2">
                <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Master Data Asset
            </h1>
            <p class="text-sm text-gray-500 mt-1">Kelola inventaris, spesifikasi, dan kondisi asset perusahaan.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('asset.export', request()->query()) }}" class="inline-flex items-center px-3.5 py-2 text-xs font-semibold rounded-xl border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 shadow-sm transition-all duration-150">
                <svg class="w-4 h-4 mr-1.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export Excel
            </a>
            <button type="button" onclick="openImportModal()" class="inline-flex items-center px-3.5 py-2 text-xs font-semibold rounded-xl border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 shadow-sm transition-all duration-150">
                <svg class="w-4 h-4 mr-1.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Import Excel
            </button>
            @if(Auth::user() && (Auth::user()->hasRole('admin') || Auth::user()->can('asset-create') || Auth::user()->can('master-asset-create')))
            <a href="{{ route('asset.create') }}" class="inline-flex items-center px-4 py-2 text-xs font-semibold rounded-xl text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition-all duration-150">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Tambah Asset
            </a>
            @endif
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-sm font-medium">{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 shadow-sm">
            <p class="font-semibold text-sm mb-1">Terdapat kesalahan:</p>
            <ul class="list-disc list-inside text-xs space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Total Asset</p>
                <h3 class="text-xl font-bold text-gray-800">{{ number_format($stats['total']) }}</h3>
                <span class="text-[11px] text-gray-400">Total Unit Terdaftar</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Asset Tersedia</p>
                <h3 class="text-xl font-bold text-gray-800">{{ number_format($stats['total_tersedia']) }}</h3>
                <span class="text-[11px] text-emerald-600 font-medium">Siap Digunakan</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Asset Digunakan</p>
                <h3 class="text-xl font-bold text-gray-800">{{ number_format($stats['total_digunakan']) }}</h3>
                <span class="text-[11px] text-indigo-600 font-medium">Sedang Beroperasi</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Perhatian Khusus</p>
                <h3 class="text-xl font-bold text-gray-800">{{ number_format($stats['total_maintenance'] + $stats['total_rusak']) }}</h3>
                <span class="text-[11px] text-amber-600">{{ $stats['total_maintenance'] }} Maintenance, {{ $stats['total_rusak'] }} Rusak</span>
            </div>
        </div>
    </div>

    <!-- Filter & Search Section -->
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
        <form method="GET" action="{{ route('asset.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3">
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">Cari Kata Kunci</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Kode, nama, pemegang asset..." class="w-full pl-9 pr-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Kategori</label>
                <select name="kategori" class="w-full py-2 px-3 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $kat)
                        <option value="{{ $kat }}" {{ request('kategori') == $kat ? 'selected' : '' }}>{{ $kat }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full py-2 px-3 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Semua Status</option>
                    @foreach($statuses as $st)
                        <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Pemegang</label>
                <select name="karyawan_id" class="w-full py-2 px-3 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Semua Pemegang</option>
                    @foreach($karyawans as $kry)
                        <option value="{{ $kry->id }}" {{ request('karyawan_id') == $kry->id ? 'selected' : '' }}>{{ $kry->nama_lengkap }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-4 text-xs font-semibold rounded-xl text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition-all duration-150 flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Filter
                </button>
                @if(request()->hasAny(['search', 'kategori', 'status', 'kondisi', 'karyawan_id']))
                    <a href="{{ route('asset.index') }}" class="py-2 px-3 text-xs font-semibold rounded-xl text-gray-600 bg-gray-100 hover:bg-gray-200 transition-all duration-150" title="Reset Filter">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50 text-gray-600 font-semibold uppercase tracking-wider">
                    <tr>
                        <th scope="col" class="py-3 px-4 text-center w-12">No</th>
                        <th scope="col" class="py-3 px-4 text-left">Foto</th>
                        <th scope="col" class="py-3 px-4 text-left">Kode & Nama Asset</th>
                        <th scope="col" class="py-3 px-4 text-left">Kategori</th>
                        <th scope="col" class="py-3 px-4 text-left">Pemegang</th>
                        <th scope="col" class="py-3 px-4 text-center">Kondisi</th>
                        <th scope="col" class="py-3 px-4 text-center">Status</th>
                        <th scope="col" class="py-3 px-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($assets as $index => $asset)
                        <tr class="hover:bg-blue-50/40 transition-colors">
                            <td class="py-3 px-4 text-center text-gray-500 font-medium">
                                {{ $assets->firstItem() + $index }}
                            </td>
                            <td class="py-3 px-4">
                                @if($asset->foto && file_exists(public_path($asset->foto)))
                                    <img src="{{ asset($asset->foto) }}" alt="{{ $asset->nama_asset }}" class="w-10 h-10 object-cover rounded-lg border border-gray-200 shadow-xs">
                                @else
                                    <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 border border-gray-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <a href="{{ route('asset.show', $asset->id) }}" class="font-bold text-blue-600 hover:text-blue-800 block text-xs">
                                    {{ $asset->kode_asset }}
                                </a>
                                <span class="font-semibold text-gray-800 block">{{ $asset->nama_asset }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded-md bg-gray-100 text-gray-700 font-medium">
                                    {{ $asset->kategori }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                @if($asset->karyawan)
                                    <span class="font-semibold text-gray-800 block">{{ $asset->karyawan->nama_lengkap }}</span>
                                    <span class="text-[11px] text-gray-500 block">{{ $asset->karyawan->nik ? 'NIK: ' . $asset->karyawan->nik : ($asset->karyawan->divisi ?? '-') }}</span>
                                    @if($asset->tanggal_tanda_terima)
                                        <span class="text-[10px] text-blue-600 font-medium block">Terima: {{ $asset->tanggal_tanda_terima->format('d/m/Y') }}</span>
                                    @endif
                                @else
                                    @if($asset->tanggal_tanda_terima)
                                        <span class="text-[10px] text-gray-600 block">Terima: {{ $asset->tanggal_tanda_terima->format('d/m/Y') }}</span>
                                    @else
                                        <span class="text-gray-400 italic text-[11px]">-</span>
                                    @endif
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-semibold border {{ $asset->kondisi_badge_class }}">
                                    {{ $asset->kondisi }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-semibold border {{ $asset->status_badge_class }}">
                                    {{ $asset->status }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('asset.show', $asset->id) }}" class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    @if(Auth::user() && (Auth::user()->hasRole('admin') || Auth::user()->can('asset-update') || Auth::user()->can('master-asset-update')))
                                    <a href="{{ route('asset.edit', $asset->id) }}" class="p-1.5 text-gray-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    @endif
                                    @if(Auth::user() && (Auth::user()->hasRole('admin') || Auth::user()->can('asset-delete') || Auth::user()->can('master-asset-delete')))
                                    <button type="button" onclick="confirmDelete('{{ $asset->id }}', '{{ $asset->nama_asset }} ({{ $asset->kode_asset }})')" class="p-1.5 text-gray-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-gray-400">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <p class="text-sm font-medium text-gray-500">Tidak ada data asset yang ditemukan</p>
                                <p class="text-xs text-gray-400 mt-1">Coba ubah kata kunci pencarian atau filter status/kategori.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($assets->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $assets->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Import Modal -->
<div id="importModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeImportModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('asset.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="bg-white px-6 pt-5 pb-4">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                        <h3 class="text-lg font-bold text-gray-800" id="modal-title">Import Data Asset</h3>
                        <button type="button" onclick="closeImportModal()" class="text-gray-400 hover:text-gray-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="mt-4 space-y-4">
                        <div class="p-3 bg-blue-50 rounded-xl border border-blue-100 text-xs text-blue-700 flex items-start gap-2">
                            <svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                Pastikan format file sesuai dengan template. Format yang didukung: <span class="font-semibold">.xlsx, .xls</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Pilih File Excel</label>
                            <input type="file" name="file" required accept=".xlsx, .xls" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-gray-300 rounded-xl cursor-pointer">
                        </div>

                        <div class="pt-2">
                            <a href="{{ route('asset.template') }}" class="inline-flex items-center text-xs font-semibold text-blue-600 hover:text-blue-800 gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Unduh Template Format Excel
                            </a>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-3 flex flex-row-reverse gap-2">
                    <button type="submit" class="px-4 py-2 text-xs font-semibold rounded-xl text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition-all duration-150">
                        Upload & Import
                    </button>
                    <button type="button" onclick="closeImportModal()" class="px-4 py-2 text-xs font-semibold rounded-xl text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition-all duration-150">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Form (Hidden) -->
<form id="deleteForm" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('scripts')
<script>
    function openImportModal() {
        document.getElementById('importModal').classList.remove('hidden');
    }

    function closeImportModal() {
        document.getElementById('importModal').classList.add('hidden');
    }

    function confirmDelete(id, name) {
        if (confirm(`Apakah Anda yakin ingin menghapus data asset: "${name}"? Data akan dipindahkan ke tempat sampah.`)) {
            const form = document.getElementById('deleteForm');
            form.action = `{{ url('master-asset') }}/${id}`;
            form.submit();
        }
    }
</script>
@endpush
