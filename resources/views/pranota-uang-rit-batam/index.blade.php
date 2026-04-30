@extends('layouts.app')

@section('title', 'Pranota Uang Rit Batam')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">💰 Pranota Uang Rit Supir Batam</h1>
                <p class="text-gray-600 mt-1">Kelola daftar pranota uang rit supir Batam</p>
            </div>
            <div class="flex items-center space-x-2">
                @can('pranota-uang-rit-batam-view')
                <button onclick="printTable()" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors duration-200">
                    <i class="fas fa-print mr-2"></i> Print
                </button>
                <button onclick="exportToExcel()" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200">
                    <i class="fas fa-file-excel mr-2"></i> Export Excel
                </button>
                @endcan
                @can('pranota-uang-rit-batam-create')
                <a href="{{ route('pranota-uang-rit-batam.select-date') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                    <i class="fas fa-plus mr-2"></i> Tambah Pranota
                </a>
                @endcan
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Filter & Pencarian</h3>
            </div>
            <div class="p-6">
                <form method="GET" action="{{ route('pranota-uang-rit-batam.index') }}">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div class="md:col-span-2">
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                                   id="search" name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="No. Pranota, Nama Supir">
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                                    id="status" name="status">
                                <option value="">Semua Status</option>
                                <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Belum Bayar</option>
                                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Lunas</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                        </div>
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                            <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                                   id="start_date" name="start_date" 
                                   value="{{ request('start_date') }}">
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                            <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                                   id="end_date" name="end_date" 
                                   value="{{ request('end_date') }}">
                        </div>

                    </div>
                    <div class="mt-4">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                            <i class="fas fa-search mr-2"></i> Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Data Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Daftar Pranota Uang Rit Batam</h3>
            </div>
            <div class="p-6">
                @if($pranotaUangRitBatams->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 resizable-table" id="pranotaUangRitBatamTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">No. Pranota<div class="resize-handle"></div></th>
                                <th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Tanggal<div class="resize-handle"></div></th>
                                <th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Supir<div class="resize-handle"></div></th>
                                <th class="resizable-th px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Qty SJ<div class="resize-handle"></div></th>
                                <th class="resizable-th px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Total<div class="resize-handle"></div></th>
                                <th class="resizable-th px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Status<div class="resize-handle"></div></th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($pranotaUangRitBatams as $item)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->nomor_pranota }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->tanggal_pranota->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->supir_nama }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $item->surat_jalan_batams_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">Rp {{ number_format($item->total_amount, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($item->status_pembayaran == 'paid')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Lunas</span>
                                    @elseif($item->status_pembayaran == 'cancelled')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Dibatalkan</span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Belum Bayar</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        @can('pranota-uang-rit-batam-view')
                                        <a href="{{ route('pranota-uang-rit-batam.show', $item) }}" 
                                           class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-100 hover:bg-blue-200 rounded-lg transition-colors duration-200" 
                                           title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan

                                        @can('pranota-uang-rit-batam-delete')
                                            @if($item->status_pembayaran != 'paid')
                                            <form action="{{ route('pranota-uang-rit-batam.destroy', $item) }}" method="POST" 
                                                  class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-600 bg-red-100 hover:bg-red-200 rounded-lg transition-colors duration-200" 
                                                        title="Hapus"
                                                        onclick="return confirm('Yakin ingin menghapus pranota ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex justify-center mt-6">
                    @include('components.modern-pagination', ['paginator' => $pranotaUangRitBatams])
                    @include('components.rows-per-page')
                </div>
                @else
                <div class="text-center py-12">
                    <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-500 mb-2">Tidak ada data pranota uang rit Batam</h3>
                    <p class="text-gray-400 mb-6">Data akan muncul di sini setelah Anda menambahkan pranota uang rit Batam.</p>
                    @can('pranota-uang-rit-batam-create')
                    <a href="{{ route('pranota-uang-rit-batam.select-date') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                        <i class="fas fa-plus mr-2"></i> Tambah Pranota Pertama
                    </a>
                    @endcan
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
function printTable() {
    const printContents = document.querySelector('.max-w-7xl').cloneNode(true);
    
    // Remove action buttons from print
    const actionColumns = printContents.querySelectorAll('td:last-child, th:last-child');
    actionColumns.forEach(col => col.remove());
    
    // Remove filter form
    const filterSection = printContents.querySelector('.bg-white.rounded-lg.shadow-sm.border');
    if (filterSection) {
        filterSection.remove();
    }
    
    // Remove pagination
    const pagination = printContents.querySelector('.flex.justify-center.mt-6');
    if (pagination) {
        pagination.remove();
    }
    
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Pranota Uang Rit Batam - Print</title>
            <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
            <style>
                @media print {
                    body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
                    @page { margin: 1cm; }
                }
                .no-print { display: none; }
            </style>
        </head>
        <body class="p-4">
            ${printContents.innerHTML}
            <script>
                window.onload = function() {
                    window.print();
                    window.onafterprint = function() {
                        window.close();
                    }
                }
            <\/script>
        </body>
        </html>
    `);
    printWindow.document.close();
}

function exportToExcel() {
    const table = document.getElementById('pranotaUangRitBatamTable');
    if (!table) {
        alert('Tidak ada data untuk diekspor');
        return;
    }
    
    // Clone table and remove action column
    const tableClone = table.cloneNode(true);
    const rows = tableClone.querySelectorAll('tr');
    rows.forEach(row => {
        const lastCell = row.querySelector('th:last-child, td:last-child');
        if (lastCell && lastCell.textContent.includes('Aksi')) {
            lastCell.remove();
        } else if (lastCell && row.querySelector('button, a')) {
            lastCell.remove();
        }
    });
    
    // Create workbook
    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.table_to_sheet(tableClone);
    
    // Set column widths
    ws['!cols'] = [
        { wch: 20 }, // No. Pranota
        { wch: 15 }, // Tanggal
        { wch: 20 }, // Supir
        { wch: 10 }, // Qty SJ
        { wch: 20 }, // Total
        { wch: 15 }  // Status
    ];
    
    XLSX.utils.book_append_sheet(wb, ws, 'Pranota Uang Rit Batam');
    
    // Generate filename with current date
    const date = new Date().toISOString().split('T')[0];
    const filename = `Pranota_Uang_Rit_Batam_${date}.xlsx`;
    
    // Save file
    XLSX.writeFile(wb, filename);
}
</script>
@endpush
