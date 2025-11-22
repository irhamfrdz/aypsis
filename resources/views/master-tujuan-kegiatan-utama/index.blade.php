@extends('layouts.app')

@section('title', 'Master Data Transportasi')
@section('page_title', 'Master Data Transportasi')

@section('content')
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800">Daftar Data Transportasi</h2>
            <div class="flex space-x-2">
                <!-- Template Download Button -->
                <a href="{{ route('master.tujuan-kegiatan-utama.download-template') }}" class="inline-flex items-center px-3 py-2 border border-purple-600 text-sm font-medium rounded-md shadow-sm text-purple-600 bg-white hover:bg-purple-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Download Template
                </a>

                <!-- Import Button -->
                <a href="{{ route('master.tujuan-kegiatan-utama.import-form') }}" class="inline-flex items-center px-3 py-2 border border-orange-600 text-sm font-medium rounded-md shadow-sm text-orange-600 bg-white hover:bg-orange-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                    </svg>
                    Import Data
                </a>

                <!-- Export Button -->
                <a href="{{ route('master.tujuan-kegiatan-utama.export') }}" class="inline-flex items-center px-3 py-2 border border-green-600 text-sm font-medium rounded-md shadow-sm text-green-600 bg-white hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export CSV
                </a>

                <!-- Print Button -->
                <a href="{{ route('master.tujuan-kegiatan-utama.print') }}" target="_blank" class="inline-flex items-center px-3 py-2 border border-blue-600 text-sm font-medium rounded-md shadow-sm text-blue-600 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print
                </a>

                <!-- Add New Button -->
                <a href="{{ route('master.tujuan-kegiatan-utama.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Data Transportasi
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Search Section -->
        <form method="GET" action="{{ route('master.tujuan-kegiatan-utama.index') }}">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Cari Data Transportasi
                        </label>
                        <div class="relative">
                            <input type="text"
                                   name="search"
                                   id="search"
                                   value="{{ request('search') }}"
                                   placeholder="Cari berdasarkan kode, cabang, wilayah, dari, ke, atau keterangan..."
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2 sm:items-end">
                        <button type="submit" class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Cari
                        </button>
                        @if(request('search'))
                            <a href="{{ route('master.tujuan-kegiatan-utama.index') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gray-500 text-white text-sm font-medium rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Hapus Filter
                            </a>
                        @endif
                    </div>
                </div>
                @if(request('search'))
                    <div class="mt-4 p-3 bg-indigo-50 rounded-lg border border-indigo-200">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm text-indigo-800">
                                Menampilkan <strong>{{ $tujuanKegiatanUtamas->total() }}</strong> hasil pencarian untuk: <strong>"{{ request('search') }}"</strong>
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </form>

        {{-- Rows Per Page Selection --}}
        @include('components.rows-per-page', [
            'routeName' => 'master.tujuan-kegiatan-utama.index',
            'paginator' => $tujuanKegiatanUtamas,
            'entityName' => 'data transportasi',
            'entityNamePlural' => 'data transportasi'
        ])

        @if ($tujuanKegiatanUtamas->isEmpty())
            <p class="text-gray-500">Belum ada data tujuan kegiatan utama.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white rounded-lg shadow-md resizable-table" id="masterTujuanKegiatanUtamaTable">
                    <thead>
                        <tr class="bg-gray-100 text-left text-gray-600 text-[8px] font-semibold"><th class="resizable-th py-2 px-2" style="position: relative;">Kode<div class="resize-handle"></div></th><th class="resizable-th py-2 px-2" style="position: relative;">Cabang<div class="resize-handle"></div></th><th class="resizable-th py-2 px-2" style="position: relative;">Wilayah<div class="resize-handle"></div></th><th class="resizable-th py-2 px-2" style="position: relative;">Dari<div class="resize-handle"></div></th><th class="resizable-th py-2 px-2" style="position: relative;">Ke<div class="resize-handle"></div></th><th class="resizable-th py-2 px-2" style="position: relative;">Uang Jalan 20ft<div class="resize-handle"></div></th><th class="resizable-th py-2 px-2" style="position: relative;">Uang Jalan 40ft<div class="resize-handle"></div></th><th class="resizable-th py-2 px-2" style="position: relative;">Keterangan<div class="resize-handle"></div></th><th class="resizable-th py-2 px-2" style="position: relative;">Liter<div class="resize-handle"></div></th><th class="resizable-th py-2 px-2" style="position: relative;">Jarak Penjaringan (km)<div class="resize-handle"></div></th><th class="resizable-th py-2 px-2" style="position: relative;">MEL 20ft<div class="resize-handle"></div></th><th class="resizable-th py-2 px-2" style="position: relative;">MEL 40ft<div class="resize-handle"></div></th><th class="resizable-th py-2 px-2" style="position: relative;">Ongkos Truk 20ft<div class="resize-handle"></div></th><th class="resizable-th py-2 px-2" style="position: relative;">Ongkos Truk 40ft<div class="resize-handle"></div></th><th class="resizable-th py-2 px-2" style="position: relative;">Antar Lokasi 20ft<div class="resize-handle"></div></th><th class="resizable-th py-2 px-2" style="position: relative;">Antar Lokasi 40ft<div class="resize-handle"></div></th><th class="resizable-th py-2 px-2" style="position: relative;">Status<div class="resize-handle"></div></th><th class="py-2 px-2">Aksi</th></tr>
                    </thead>
                    <tbody class="text-gray-700 text-[8px]">
                        @foreach ($tujuanKegiatanUtamas as $item)
                            <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors duration-150">
                                <td class="py-2 px-2">{{ $item->kode ?? '-' }}</td>
                                <td class="py-2 px-2">{{ $item->cabang ?? '-' }}</td>
                                <td class="py-2 px-2">{{ $item->wilayah ?? '-' }}</td>
                                <td class="py-2 px-2">{{ $item->dari ?? '-' }}</td>
                                <td class="py-2 px-2">{{ $item->ke ?? '-' }}</td>
                                <td class="py-2 px-2">{{ $item->uang_jalan_20ft ? 'Rp ' . number_format($item->uang_jalan_20ft, 0, ',', '.') : '-' }}</td>
                                <td class="py-2 px-2">{{ $item->uang_jalan_40ft ? 'Rp ' . number_format($item->uang_jalan_40ft, 0, ',', '.') : '-' }}</td>
                                <td class="py-2 px-2">{{ $item->keterangan ?? '-' }}</td>
                                <td class="py-2 px-2">{{ $item->liter ? number_format($item->liter, 2, ',', '.') : '-' }}</td>
                                <td class="py-2 px-2">{{ $item->jarak_dari_penjaringan_km ? number_format($item->jarak_dari_penjaringan_km, 2, ',', '.') : '-' }}</td>
                                <td class="py-2 px-2">{{ $item->mel_20ft ? 'Rp ' . number_format($item->mel_20ft, 0, ',', '.') : '-' }}</td>
                                <td class="py-2 px-2">{{ $item->mel_40ft ? 'Rp ' . number_format($item->mel_40ft, 0, ',', '.') : '-' }}</td>
                                <td class="py-2 px-2">{{ $item->ongkos_truk_20ft ? 'Rp ' . number_format($item->ongkos_truk_20ft, 0, ',', '.') : '-' }}</td>
                                <td class="py-2 px-2">{{ $item->ongkos_truk_40ft ? 'Rp ' . number_format($item->ongkos_truk_40ft, 0, ',', '.') : '-' }}</td>
                                <td class="py-2 px-2">{{ $item->antar_lokasi_20ft ? 'Rp ' . number_format($item->antar_lokasi_20ft, 0, ',', '.') : '-' }}</td>
                                <td class="py-2 px-2">{{ $item->antar_lokasi_40ft ? 'Rp ' . number_format($item->antar_lokasi_40ft, 0, ',', '.') : '-' }}</td>
                                <td class="py-2 px-2">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->aktif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $item->aktif ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </td>
                                <td class="py-2 px-2 space-x-2">
                                    <a href="{{ route('master.tujuan-kegiatan-utama.edit', $item) }}" class="text-yellow-600 hover:text-yellow-900 transition-colors duration-150" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <span class="text-gray-300">|</span>

                                    <!-- Audit Log Button -->
                                    @can('audit-log-view')
                                        <button type="button"
                                                class="audit-log-btn text-purple-600 hover:text-purple-800 hover:underline font-medium cursor-pointer"
                                                data-model-type="{{ get_class($item) }}"
                                                data-model-id="{{ $item->id }}"
                                                data-item-name="{{ $item->kode ?? 'ID: ' . $item->id }}"
                                                title="Lihat Riwayat Perubahan">
                                            Riwayat
                                        </button>
                                        <span class="text-gray-300">|</span>
                                    @endcan

                                    <form action="{{ route('master.tujuan-kegiatan-utama.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 transition-colors duration-150" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Modern Pagination -->
            @include('components.modern-pagination', [
                'paginator' => $tujuanKegiatanUtamas,
                'routeName' => 'master.tujuan-kegiatan-utama.index'
            ])
        @endif
    </div>

<!-- Audit Log Modal -->
@include('components.audit-log-modal')

@endsection

@include('components.resizable-table')

@push('scripts')
<script>
$(document).ready(function() {
    initResizableTable('masterTujuanKegiatanUtamaTable');
});
</script>
@endpush