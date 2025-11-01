@extends('layouts.app')

@section('title', 'Pilih Uang Jalan')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-3xl font-bold text-gray-900">🚛 Pilih Uang Jalan</h1>
            <nav class="flex mt-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li>
                        <a href="{{ route('pranota-uang-rit.index') }}" class="text-blue-600 hover:text-blue-800">
                            Pranota Uang Rit
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-500 ml-1 md:ml-2">Pilih Uang Jalan</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <p class="text-gray-600 mt-2">Pilih surat jalan yang akan diproses untuk pranota uang rit</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('pranota-uang-rit.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-plus mr-2"></i> Input Manual
            </a>
            <a href="{{ route('pranota-uang-rit.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="p-6">
            <form method="GET" action="{{ route('pranota-uang-rit.select-uang-jalan') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Cari</label>
                        <input type="text" id="search" name="search" value="{{ request('search') }}" 
                               placeholder="No. Surat Jalan, Supir, atau No. Plat" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                        <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                        <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="supir" class="block text-sm font-medium text-gray-700 mb-2">Nama Supir</label>
                        <input type="text" id="supir" name="supir" value="{{ request('supir') }}" 
                               placeholder="Nama supir"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <div class="flex justify-between">
                    <div class="flex space-x-3">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fas fa-search mr-2"></i> Filter
                        </button>
                        <a href="{{ route('pranota-uang-rit.select-uang-jalan') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fas fa-undo mr-2"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Selection Form -->
    <form id="selectionForm" method="POST" action="{{ route('pranota-uang-rit.create-from-selection') }}">
        @csrf
        
        <!-- Selection Actions -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="p-6">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center">
                            <input type="checkbox" id="selectAll" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="selectAll" class="ml-2 text-sm font-medium text-gray-700">Pilih Semua</label>
                        </div>
                        <span id="selectedCount" class="text-sm text-gray-500">0 item dipilih</span>
                    </div>
                    <button type="submit" id="processSelected" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                        <i class="fas fa-arrow-right mr-2"></i> Proses Terpilih
                    </button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Daftar Surat Jalan</h3>
                <p class="text-sm text-gray-600 mt-1">Total: {{ $suratJalans->total() }} surat jalan tersedia</p>
            </div>
            
            @if($suratJalans->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">
                                Pilih
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                No. Surat Jalan
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Supir
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                No. Plat
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Uang Jalan
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($suratJalans as $suratJalan)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" name="surat_jalan_ids[]" value="{{ $suratJalan->id }}" 
                                       class="item-checkbox h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $suratJalan->no_surat_jalan }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $suratJalan->tanggal ? $suratJalan->tanggal->format('d/m/Y') : '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $suratJalan->supir_nama }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $suratJalan->no_plat }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    @if($suratJalan->uang_jalan)
                                        Rp {{ number_format($suratJalan->uang_jalan, 0, ',', '.') }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                    Approved
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($suratJalans->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $suratJalans->appends(request()->query())->links() }}
            </div>
            @endif
            @else
            <div class="px-6 py-12 text-center">
                <div class="text-gray-400 text-6xl mb-4">
                    <i class="fas fa-inbox"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Data</h3>
                <p class="text-gray-500 mb-4">Tidak ada surat jalan yang tersedia untuk diproses.</p>
                <a href="{{ route('pranota-uang-rit.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-plus mr-2"></i> Input Manual
                </a>
            </div>
            @endif
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const selectedCountSpan = document.getElementById('selectedCount');
    const processButton = document.getElementById('processSelected');

    function updateSelectedCount() {
        const selectedItems = document.querySelectorAll('.item-checkbox:checked');
        const count = selectedItems.length;
        
        selectedCountSpan.textContent = `${count} item dipilih`;
        processButton.disabled = count === 0;
        
        // Update select all checkbox state
        selectAllCheckbox.indeterminate = count > 0 && count < itemCheckboxes.length;
        selectAllCheckbox.checked = count === itemCheckboxes.length && count > 0;
    }

    // Select all functionality
    selectAllCheckbox.addEventListener('change', function() {
        itemCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelectedCount();
    });

    // Individual checkbox change
    itemCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });

    // Form submission validation
    document.getElementById('selectionForm').addEventListener('submit', function(e) {
        const selectedItems = document.querySelectorAll('.item-checkbox:checked');
        if (selectedItems.length === 0) {
            e.preventDefault();
            alert('Silakan pilih minimal satu surat jalan untuk diproses.');
            return false;
        }
        return true;
    });

    // Initial count update
    updateSelectedCount();
});
</script>
@endpush