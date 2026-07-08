@extends('layouts.app')

@section('title', 'Outstanding Surat Jalan Bongkaran')

@section('content')
<div class="flex-1 p-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Outstanding Surat Jalan Bongkaran</h1>
            <nav class="flex text-sm text-gray-600 mt-1">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                <span class="mx-2">/</span>
                <span class="text-gray-500">Surat Jalan Bongkaran</span>
                <span class="mx-2">/</span>
                <span class="text-gray-500">Outstanding</span>
            </nav>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span>{{ session('success') }}</span>
            <button type="button" class="ml-auto text-green-600 hover:text-green-800" onclick="this.parentElement.remove()">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    @endif

    <!-- Main Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Card Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Daftar Surat Jalan Bongkaran</h2>
                    @if(request('view_all'))
                        <p class="text-sm text-gray-500 mt-1">Menampilkan semua data</p>
                    @else
                        <div class="flex flex-wrap items-center gap-4 mt-2 text-sm text-gray-600">
                            <span>
                                <span class="font-medium text-gray-900">Kapal:</span> {{ request('nama_kapal') }}
                            </span>
                            <span class="hidden sm:inline text-gray-300">|</span>
                            <span>
                                <span class="font-medium text-gray-900">Voyage:</span> {{ request('no_voyage') }}
                            </span>
                        </div>
                    @endif
                </div>
                <div>
                    <a href="{{ route('surat-jalan-bongkaran.outstanding') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Ganti Kapal/Voyage
                    </a>
                </div>
            </div>
        </div>

        <!-- Card Body -->
        <div class="p-6">
            <!-- Search & Filter Form -->
            <form method="GET" action="{{ route('surat-jalan-bongkaran.outstanding') }}" class="mb-6">
                <input type="hidden" name="nama_kapal" value="{{ request('nama_kapal') }}">
                <input type="hidden" name="no_voyage" value="{{ request('no_voyage') }}">
                <input type="hidden" name="view_all" value="{{ request('view_all') }}">

                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <!-- Search Input -->
                    <div class="md:col-span-2">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                        <input type="text" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               id="search" 
                               name="search" 
                               placeholder="Cari nomor surat jalan, container, seal, supir, pengirim..." 
                               value="{{ request('search') }}">
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label for="status_tanda_terima" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status_tanda_terima" id="status_tanda_terima" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                onchange="this.form.submit()">
                            <option value="belum" {{ request('status_tanda_terima', 'belum') == 'belum' ? 'selected' : '' }}>Belum Tanda Terima</option>
                            <option value="belum_sj" {{ request('status_tanda_terima') == 'belum_sj' ? 'selected' : '' }}>Belum Surat Jalan</option>
                            <option value="sudah" {{ request('status_tanda_terima') == 'sudah' ? 'selected' : '' }}>Sudah Tanda Terima</option>
                            <option value="semua" {{ request('status_tanda_terima') == 'semua' ? 'selected' : '' }}>Semua</option>
                        </select>
                    </div>

                    <!-- Lokasi Filter -->
                    <div>
                        <label for="lokasi" class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                        <select name="lokasi" id="lokasi" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                onchange="this.form.submit()">
                            <option value="">Semua Lokasi</option>
                            <option value="jakarta" {{ request('lokasi') == 'jakarta' ? 'selected' : '' }}>Jakarta</option>
                            <option value="batam" {{ request('lokasi') == 'batam' ? 'selected' : '' }}>Batam</option>
                        </select>
                    </div>

                    <!-- Date Range Filters -->
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                            <input type="date" name="start_date" id="start_date" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   value="{{ request('start_date') }}">
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                            <input type="date" name="end_date" id="end_date" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   value="{{ request('end_date') }}">
                        </div>
                    </div>
                </div>

                <!-- Form Action Buttons -->
                <div class="flex justify-end gap-2 mt-4">
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Filter
                    </button>
                    <a href="{{ route('surat-jalan-bongkaran.outstanding') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Reset
                    </a>
                </div>
            </form>

            <!-- Table -->
            <div class="relative overflow-x-auto border border-gray-200 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200 text-left">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-2 py-2.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                            <th class="px-2 py-2.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-2 py-2.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">No. Surat Jalan</th>
                            <th class="px-2 py-2.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-2 py-2.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kapal / Voyage</th>
                            <th class="px-2 py-2.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">No. BL</th>
                            <th class="px-2 py-2.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Term</th>
                            <th class="px-2 py-2.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kontainer / Seal</th>
                            <th class="px-2 py-2.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Supir / No Plat</th>
                            <th class="px-2 py-2.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Lokasi</th>
                            <th class="px-2 py-2.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Barang</th>
                            <th class="px-2 py-2.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Jenis Barang</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($suratJalans as $sj)
                            <tr class="hover:bg-gray-50 transition-colors {{ $sj->tandaTerima ? 'bg-green-50/50' : '' }}">
                                <td class="px-2 py-2 whitespace-nowrap text-xs font-medium">
                                    @if(isset($sj->is_manifest_only) && $sj->is_manifest_only)
                                        @if($sj->lokasi == 'batam')
                                            <a href="{{ route('surat-jalan-bongkaran-batam.create', ['bl_id' => $sj->id, 'nama_kapal' => $sj->nama_kapal, 'no_voyage' => $sj->no_voyage]) }}" 
                                               class="inline-flex items-center px-2 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-medium rounded transition-colors duration-200 shadow-sm">
                                                Buat SJ
                                            </a>
                                        @else
                                            <a href="{{ route('surat-jalan-bongkaran.create', ['bl_id' => $sj->id, 'nama_kapal' => $sj->nama_kapal, 'no_voyage' => $sj->no_voyage]) }}" 
                                               class="inline-flex items-center px-2 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-medium rounded transition-colors duration-200 shadow-sm">
                                                Buat SJ
                                            </a>
                                        @endif
                                    @elseif($sj->tandaTerima)
                                        <a href="{{ route('tanda-terima-bongkaran.index', ['search' => $sj->nomor_surat_jalan]) }}" 
                                           target="_blank" 
                                           class="inline-flex items-center px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-medium rounded transition-colors duration-200 shadow-sm">
                                            Lihat
                                        </a>
                                    @else
                                        <a href="{{ route('tanda-terima-bongkaran.index', ['status' => 'belum', 'search' => $sj->nomor_surat_jalan]) }}" 
                                           target="_blank" 
                                           class="inline-flex items-center px-2 py-1 bg-teal-600 hover:bg-teal-700 text-white text-[10px] font-medium rounded transition-colors duration-200 shadow-sm">
                                            Tanda Terima
                                        </a>
                                    @endif
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap text-xs">
                                    @if(isset($sj->is_manifest_only) && $sj->is_manifest_only)
                                        <span class="px-1.5 py-0.5 text-[10px] font-semibold rounded bg-red-100 text-red-800">
                                            Belum SJ
                                        </span>
                                    @elseif($sj->tandaTerima)
                                        <span class="px-1.5 py-0.5 text-[10px] font-semibold rounded bg-green-100 text-green-800">
                                            Sudah
                                        </span>
                                    @else
                                        <span class="px-1.5 py-0.5 text-[10px] font-semibold rounded bg-amber-100 text-amber-800">
                                            Belum
                                        </span>
                                    @endif
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap text-xs">
                                    <span class="font-semibold text-gray-900">{{ $sj->nomor_surat_jalan ?: '-' }}</span>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-600">
                                    {{ $sj->tanggal_surat_jalan ? $sj->tanggal_surat_jalan->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-600">
                                    {{ $sj->nama_kapal ?: '-' }} {{ $sj->no_voyage ? ' / ' . $sj->no_voyage : '' }}
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-600">
                                    {{ $sj->no_bl ?: '-' }}
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-600">
                                    @if($sj->term)
                                        <span class="px-1.5 py-0.5 text-[10px] font-medium rounded bg-gray-100 text-gray-800">{{ $sj->term }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-2 py-2 text-xs text-gray-900 font-medium">
                                    <div>{{ $sj->no_kontainer ?: '-' }}</div>
                                    @if($sj->no_seal && $sj->no_seal !== '-')
                                        <div class="text-[10px] text-gray-500 font-normal">Seal: {{ $sj->no_seal }}</div>
                                    @endif
                                </td>
                                <td class="px-2 py-2 text-xs text-gray-600">
                                    <div>{{ $sj->supir ?: '-' }}</div>
                                    @if($sj->no_plat && $sj->no_plat !== '-')
                                        <div class="text-[10px] text-gray-500">{{ $sj->no_plat }}</div>
                                    @endif
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap text-xs">
                                    @if($sj->lokasi == 'batam')
                                        <span class="px-1.5 py-0.5 text-[10px] font-semibold rounded bg-cyan-100 text-cyan-800">Batam</span>
                                    @else
                                        <span class="px-1.5 py-0.5 text-[10px] font-semibold rounded bg-blue-100 text-blue-800">Jakarta</span>
                                    @endif
                                </td>
                                <td class="px-2 py-2 text-xs text-gray-600 max-w-[150px] whitespace-normal break-words" title="{{ $sj->manifest->nama_barang ?? ($sj->nama_barang ?? '') }}">
                                    {{ $sj->manifest->nama_barang ?? ($sj->nama_barang ?? '-') }}
                                </td>
                                <td class="px-2 py-2 text-xs text-gray-600 max-w-[150px] whitespace-normal break-words" title="{{ $sj->jenis_barang }}">
                                    {{ $sj->jenis_barang ?: '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="px-6 py-10 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                        </svg>
                                        <h3 class="text-sm font-medium text-gray-900">Tidak Ada Data</h3>
                                        <p class="text-xs text-gray-500 mt-1">Tidak ada surat jalan bongkaran yang sesuai dengan filter yang dipilih.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($suratJalans->hasPages())
                <div class="flex flex-col sm:flex-row justify-between items-center mt-6 pt-4 border-t border-gray-200">
                    <div class="text-sm text-gray-700 mb-4 sm:mb-0">
                        Menampilkan {{ $suratJalans->firstItem() }} sampai {{ $suratJalans->lastItem() }} dari {{ $suratJalans->total() }} data
                    </div>
                    <div>
                        {{ $suratJalans->appends(request()->query())->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
