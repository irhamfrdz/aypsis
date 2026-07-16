@extends('layouts.app')

@section('title', 'Stock Kontainer per Gudang')
@section('page_title', 'Stock Kontainer per Gudang')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 py-6 sm:py-12 px-3 sm:px-4 lg:px-8">
    <div class="max-w-6xl mx-auto">
        
        <!-- Header Section -->
        <div class="text-center mb-8 sm:mb-12">
            <div class="inline-flex items-center justify-center w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-indigo-500 to-indigo-700 rounded-2xl shadow-lg mb-4 sm:mb-6">
                <svg class="w-8 h-8 sm:w-10 sm:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-2 sm:mb-3 px-4">Stock Kontainer per Gudang</h1>
            <p class="text-sm sm:text-base lg:text-lg text-gray-600 px-4">Ringkasan ketersediaan kontainer dan stock di masing-masing gudang</p>
            <div class="mt-5 flex justify-center gap-4">
                <a href="{{ route('kontainer.stock-pergudang.export-all') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-700 hover:from-green-700 hover:to-emerald-800 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all active:scale-95 duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export Laporan Bulanan (Excel)
                </a>

                <button type="button" onclick="openExportBulanModal()" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all active:scale-95 duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export per Bulan
                </button>
            </div>
        </div>

        <!-- Total Summaries Top Panel -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6 mb-8">
            <div style="background: linear-gradient(135deg, #EA580C 0%, #C2410C 100%)" class="p-5 rounded-2xl shadow-lg text-white">
                <h3 class="text-xs font-semibold uppercase tracking-wider opacity-80">Total Kontainer Sewa</h3>
                <p class="text-2xl sm:text-3xl font-extrabold mt-2">{{ $data->sum('total_sewa') }}</p>
            </div>
            <div style="background: linear-gradient(135deg, #2563EB 0%, #1E3A8A 100%)" class="p-5 rounded-2xl shadow-lg text-white">
                <h3 class="text-xs font-semibold uppercase tracking-wider opacity-80">Total Stock Kontainer</h3>
                <p class="text-2xl sm:text-3xl font-extrabold mt-2">{{ $data->sum('total_stock') }}</p>
            </div>
            <div style="background: linear-gradient(135deg, #16A34A 0%, #14532D 100%)" class="p-5 rounded-2xl shadow-lg text-white">
                <h3 class="text-xs font-semibold uppercase tracking-wider opacity-80">Total Gabungan</h3>
                <p class="text-2xl sm:text-3xl font-extrabold mt-2">{{ $data->sum('total_gabungan') }}</p>
            </div>
        </div>

        <!-- Cards Grid For each Gudang -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-8 sm:mb-12">
            @forelse($data as $item)
            <a href="{{ route('master.kontainer.stock-pergudang.show', ['id' => $item['id'] ?? 'none']) }}" class="group relative bg-white rounded-xl sm:rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border-2 border-transparent hover:border-orange-400 active:scale-95 sm:active:scale-100 sm:hover:-translate-y-1 touch-manipulation">
                <div class="absolute top-0 right-0 w-24 h-24 sm:w-32 sm:h-32 bg-orange-50 rounded-bl-full opacity-40"></div>
                <div class="relative p-5 sm:p-8">
                    <div style="background: linear-gradient(135deg, #EA580C 0%, #9A3412 100%)" class="flex items-center justify-center w-12 h-12 sm:w-16 sm:h-16 rounded-xl shadow-lg mb-4 sm:mb-5 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-1 group-hover:text-orange-600 transition-colors">{{ $item['nama_gudang'] }}</h3>
                    <p class="text-gray-500 text-xs sm:text-sm mb-4">{{ $item['lokasi'] ?? 'Lokasi tidak tersedia' }}</p>
                    
                    <div class="space-y-2 text-sm border-t border-gray-100 pt-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Kontainer Sewa:</span>
                            <span class="font-bold text-gray-800">{{ $item['total_sewa'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Stock Kontainer:</span>
                            <span class="font-bold text-gray-800">{{ $item['total_stock'] }}</span>
                        </div>
                        <div class="flex justify-between items-center text-orange-600 font-extrabold border-t border-dashed pt-2 mt-1">
                            <span>Total Gabungan:</span>
                            <span class="text-lg">{{ $item['total_gabungan'] }}</span>
                        </div>
                    </div>
                </div>
            </a>
            @empty
            <div class="col-span-full bg-white rounded-xl p-8 text-center text-gray-500 shadow border border-gray-100">
                <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0l-4 4m-4-4l-4 4m6-12v12"></path>
                </svg>
                Tidak ada data stock per gudang tersedia.
            </div>
            @endforelse
        </div>

        <!-- Info Section -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-xl p-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800 mb-2">Informasi</h3>
                    <div class="text-sm text-blue-700 space-y-1">
                        <p>• Panel di atas menampilkan ringkasan akumulasi seluruh gudang aktif.</p>
                        <p>• Data di-update secara berkala sesuai dengan pergerakan mutasi (checkpoint) kontainer.</p>
                        <p>• Klik menu seksi lainnya untuk manipulasi detail data stock.</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal Export Bulan -->
<div id="export_bulan_modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" onclick="closeExportBulanModal()"></div>

        <!-- Modal panel -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div class="sm:flex sm:items-start">
                <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-indigo-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                    <i class="text-indigo-600 fas fa-calendar-alt"></i>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                    <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                        Export Stock per Bulan
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500 mb-4">
                            Pilih bulan dan tahun untuk mengunduh rekapitulasi Excel stok kontainer.
                        </p>
                        
                        <form action="{{ route('kontainer.stock-pergudang.export-bulan') }}" method="POST" id="export_bulan_form">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Bulan & Tahun</label>
                                <input type="month" name="bulan" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="document.getElementById('export_bulan_form').submit(); closeExportBulanModal();" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Export Excel
                </button>
                <button type="button" onclick="closeExportBulanModal()" class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function openExportBulanModal() {
        document.getElementById('export_bulan_modal').classList.remove('hidden');
    }
    
    function closeExportBulanModal() {
        document.getElementById('export_bulan_modal').classList.add('hidden');
    }
</script>

@endsection
