@extends('layouts.app')

@section('title', 'Biaya Kapal')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Biaya Kapal</h1>
                <p class="text-gray-600 mt-1">Kelola data biaya operasional kapal</p>
            </div>
            <div>
                @can('biaya-kapal-create')
                <a href="{{ route('biaya-kapal.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg shadow-sm transition duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Biaya Kapal
                </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Main Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Daftar Biaya Kapal</h2>
        </div>

        <div class="p-6">
            <!-- Filter & Search -->
            <form method="GET" action="{{ route('biaya-kapal.index') }}" class="mb-6">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <div class="md:col-span-3">
                        <label for="search" class="block text-xs font-medium text-gray-700 mb-1">Cari Data</label>
                        <input type="text"
                               name="search"
                               id="search"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm"
                               placeholder="Kapal, invoice, jenis biaya, ket..."
                               value="{{ request('search') }}">
                    </div>
                    <div class="md:col-span-3">
                        <label for="jenis_biaya_select" class="block text-xs font-medium text-gray-700 mb-1">Jenis Biaya</label>
                        <select name="jenis_biaya" id="jenis_biaya_select" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent select2 text-sm">
                            <option value="">Semua Jenis Biaya</option>
                            @foreach($klasifikasiBiayas as $kb)
                                <option value="{{ $kb->kode }}" {{ request('jenis_biaya') == $kb->kode ? 'selected' : '' }}>
                                    {{ $kb->nama }} ({{ $kb->kode }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label for="start_date" class="block text-xs font-medium text-gray-700 mb-1">Dari Tanggal</label>
                        <input type="date"
                               name="start_date"
                               id="start_date"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm"
                               value="{{ request('start_date') }}">
                    </div>
                    <div class="md:col-span-2">
                        <label for="end_date" class="block text-xs font-medium text-gray-700 mb-1">Ke Tanggal</label>
                        <input type="date"
                               name="end_date"
                               id="end_date"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm"
                               value="{{ request('end_date') }}">
                    </div>
                    <div class="md:col-span-2 flex items-end gap-2">
                        <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg transition duration-200 flex items-center justify-center text-sm font-medium">
                            <i class="fas fa-search mr-1.5"></i> Cari
                        </button>
                        <a href="{{ route('biaya-kapal.index') }}" class="flex-1 text-center bg-gray-500 hover:bg-gray-600 text-white px-3 py-2 rounded-lg transition duration-200 flex items-center justify-center text-sm font-medium">
                            <i class="fas fa-redo mr-1.5"></i> Reset
                        </a>
                    </div>
                </div>
            </form>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Invoice</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kapal</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Voyage</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Biaya</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Nominal</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($biayaKapals as $biaya)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900 text-center">
                                {{ ($biayaKapals->currentPage() - 1) * $biayaKapals->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-600">
                                {{ $biaya->tanggal ? \Carbon\Carbon::parse($biaya->tanggal)->format('d/M/Y') : '-' }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-xs font-medium text-gray-900">
                                {{ $biaya->nomor_invoice }}
                            </td>
                            <td class="px-3 py-2">
                                @php
                                    // Untuk biaya buruh (KB024), ambil kapal dari barangDetails
                                    if ($biaya->jenis_biaya === 'KB024' && $biaya->barangDetails && $biaya->barangDetails->count() > 0) {
                                        $namaKapals = $biaya->barangDetails->pluck('kapal')->unique()->filter()->values()->toArray();
                                    } 
                                    // Untuk biaya air, ambil kapal dari airDetails
                                    elseif ($biaya->airDetails && $biaya->airDetails->count() > 0) {
                                        $namaKapals = $biaya->airDetails->pluck('kapal')->unique()->filter()->values()->toArray();
                                    }
                                    else {
                                        $namaKapals = is_array($biaya->nama_kapal) ? $biaya->nama_kapal : ($biaya->nama_kapal ? [$biaya->nama_kapal] : []);
                                    }
                                @endphp
                                @if(count($namaKapals) > 0)
                                    <span class="text-xs font-semibold text-gray-900">{{ $namaKapals[0] }}</span>
                                    @if(count($namaKapals) > 1)
                                        <span class="text-xs text-blue-600">+{{ count($namaKapals) - 1 }}</span>
                                    @endif
                                @else
                                    <span class="text-xs text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-3 py-2">
                                @php
                                    // Untuk biaya buruh (KB024), ambil voyage dari barangDetails
                                    if ($biaya->jenis_biaya === 'KB024' && $biaya->barangDetails && $biaya->barangDetails->count() > 0) {
                                        $noVoyages = $biaya->barangDetails->pluck('voyage')->unique()->filter()->values()->toArray();
                                    } 
                                    // Untuk biaya air, ambil voyage dari airDetails
                                    elseif ($biaya->airDetails && $biaya->airDetails->count() > 0) {
                                        $noVoyages = $biaya->airDetails->pluck('voyage')->unique()->filter()->values()->toArray();
                                    }
                                    else {
                                        $noVoyages = is_array($biaya->no_voyage) ? $biaya->no_voyage : ($biaya->no_voyage ? [$biaya->no_voyage] : []);
                                    }
                                @endphp
                                @if(count($noVoyages) > 0)
                                    <span class="text-xs text-gray-900">{{ $noVoyages[0] }}</span>
                                    @if(count($noVoyages) > 1)
                                        <span class="text-xs text-blue-600">+{{ count($noVoyages) - 1 }}</span>
                                    @endif
                                @else
                                    <span class="text-xs text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-xs">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $biaya->jenis_biaya_label }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-xs text-gray-600">
                                <div class="max-w-xs truncate" title="{{ $biaya->keterangan }}">
                                    {{ Str::limit($biaya->keterangan ?: '-', 50) }}
                                </div>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900 text-right font-medium">
                                @php
                                    $displayNominal = $biaya->nominal;
                                    if(isset($biaya->operasionalDetails) && $biaya->operasionalDetails->count() > 0) {
                                        $displayNominal = $biaya->operasionalDetails->sum('nominal');
                                    } elseif(isset($biaya->airDetails) && $biaya->airDetails->count() > 0) {
                                        $displayNominal = $biaya->airDetails->sum('grand_total');
                                    }
                                @endphp
                                Rp {{ number_format($displayNominal ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-center text-xs font-medium">
                                <div class="flex items-center justify-center gap-1">
                                    @can('biaya-kapal-view')
                                    <a href="{{ route('biaya-kapal.show', $biaya->id) }}"
                                       class="inline-flex items-center px-2 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded transition duration-150"
                                       title="Lihat Detail">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    @endcan

                                    @if($biaya->jenis_biaya === 'KB024')
                                    <a href="{{ route('biaya-kapal.export-buruh', $biaya->id) }}"
                                       class="inline-flex items-center px-2 py-1 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 rounded transition duration-150"
                                       title="Download Excel"
                                       target="_blank">
                                        <i class="fas fa-file-excel text-xs"></i>
                                    </a>
                                    @endif
                                    
                                    @can('biaya-kapal-view')
                                        @if($biaya->meratusDetails->count() > 0)
                                        <a href="{{ route('biaya-kapal.print-meratus', $biaya->id) }}"
                                           class="inline-flex items-center px-2 py-1 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 rounded transition duration-150"
                                           title="Print Meratus"
                                           target="_blank">
                                            <i class="fas fa-print text-xs"></i>
                                        </a>
                                        @elseif($biaya->temasDetails->count() > 0)
                                        <a href="{{ route('biaya-kapal.print-temas', $biaya->id) }}"
                                           class="inline-flex items-center px-2 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded transition duration-150"
                                           title="Print Temas"
                                           target="_blank">
                                            <i class="fas fa-print text-xs"></i>
                                        </a>
                                        @else
                                        <a href="{{ route('biaya-kapal.print', $biaya->id) }}"
                                           class="inline-flex items-center px-2 py-1 bg-purple-100 hover:bg-purple-200 text-purple-700 rounded transition duration-150"
                                           title="Print"
                                           target="_blank">
                                            <i class="fas fa-print text-xs"></i>
                                        </a>
                                        @endif
                                    @endcan
                                    
                                    @can('biaya-kapal-update')
                                    <a href="{{ route('biaya-kapal.edit', $biaya->id) }}"
                                       class="inline-flex items-center px-2 py-1 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 rounded transition duration-150"
                                       title="Edit">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                    @endcan
                                    
                                    @can('biaya-kapal-delete')
                                    <form action="{{ route('biaya-kapal.destroy', $biaya->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center px-2 py-1 bg-red-100 hover:bg-red-200 text-red-700 rounded transition duration-150"
                                                title="Hapus">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-3 py-8 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-ship text-gray-300 text-4xl mb-3"></i>
                                    <p class="text-gray-500 text-base font-medium">Tidak ada data biaya kapal</p>
                                    <p class="text-gray-400 text-xs mt-1">Silakan tambahkan data biaya kapal terlebih dahulu</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($biayaKapals->hasPages())
            <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6 mt-4">
                <div class="flex flex-1 justify-between sm:hidden">
                    @if($biayaKapals->onFirstPage())
                        <span class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-400">
                            Previous
                        </span>
                    @else
                        <a href="{{ $biayaKapals->previousPageUrl() }}" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Previous
                        </a>
                    @endif

                    @if($biayaKapals->hasMorePages())
                        <a href="{{ $biayaKapals->nextPageUrl() }}" class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Next
                        </a>
                    @else
                        <span class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-400">
                            Next
                        </span>
                    @endif
                </div>
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Menampilkan
                            <span class="font-medium">{{ $biayaKapals->firstItem() ?? 0 }}</span>
                            sampai
                            <span class="font-medium">{{ $biayaKapals->lastItem() ?? 0 }}</span>
                            dari
                            <span class="font-medium">{{ $biayaKapals->total() }}</span>
                            data
                        </p>
                    </div>
                    <div>
                        {{ $biayaKapals->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize Select2 for jenis_biaya
        $('#jenis_biaya_select').select2({
            placeholder: "-- Semua Jenis Biaya --",
            allowClear: true,
            width: '100%'
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.bg-green-50, .bg-red-50');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease-out';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    });
</script>
@endpush

@push('styles')
<style>
    /* Select2 Tweaks for Tailwind */
    .select2-container .select2-selection--single {
        height: 42px !important;
        padding-top: 6px !important;
        border-color: #d1d5db !important;
        border-radius: 0.5rem !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px !important;
        color: #374151 !important;
    }
</style>
@endpush
<!-- Export Modal -->
<div id="export_modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" onclick="closeExportModal()"></div>

        <!-- Modal panel -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div class="sm:flex sm:items-start">
                <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-indigo-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                    <i class="text-indigo-600 fas fa-file-excel"></i>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                    <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                        Export Biaya Buruh
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500 mb-4">
                            Pilih rentang tanggal untuk mengunduh rekapitulasi Excel Biaya Buruh.
                        </p>
                        
                        <form action="{{ route('biaya-kapal.export-buruh-range') }}" method="POST" id="export_form">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                                    <input type="date" name="tanggal_mulai" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                                    <input type="date" name="tanggal_akhir" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="document.getElementById('export_form').submit(); closeExportModal();" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Export Excel
                </button>
                <button type="button" onclick="closeExportModal()" class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function openExportModal() {
        document.getElementById('export_modal').classList.remove('hidden');
    }
    
    function closeExportModal() {
        document.getElementById('export_modal').classList.add('hidden');
    }
</script>
@endsection
