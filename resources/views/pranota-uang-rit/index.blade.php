@extends('layouts.app')

@section('title', 'Pranota Uang Rit')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">💰 Pranota Uang Rit</h1>
                <p class="text-gray-600 mt-1">Kelola daftar pranota uang rit untuk supir</p>
            </div>
            <div class="flex items-center space-x-2">
                @can('pranota-uang-rit-view')
                <button onclick="printTable()" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors duration-200">
                    <i class="fas fa-print mr-2"></i> Print
                </button>
                <button onclick="exportToExcel()" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200">
                    <i class="fas fa-file-excel mr-2"></i> Export Excel
                </button>
                @endcan
                @can('pranota-uang-rit-create')
                <a href="{{ route('pranota-uang-rit.select-date') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                    <i class="fas fa-plus mr-2"></i> Tambah Pranota Uang Rit
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
                <form method="GET" action="{{ route('pranota-uang-rit.index') }}">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div class="md:col-span-2">
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                                   id="search" name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="No. Pranota, Surat Jalan">
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                                    id="status" name="status">
                                <option value="">Semua Status</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
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
                <h3 class="text-lg font-medium text-gray-900">Daftar Pranota Uang Rit</h3>
            </div>
            <div class="p-6">
                @if($pranotaUangRits->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 resizable-table" id="pranotaUangRitTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">No. Pranota<div class="resize-handle"></div></th>
                                <th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Tanggal<div class="resize-handle"></div></th>
                                <th class="resizable-th px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Uang Rit<div class="resize-handle"></div></th>
                                <th class="resizable-th px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Status<div class="resize-handle"></div></th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($pranotaUangRits as $item)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->no_pranota }}</div>
                                    @if($item->suratJalan)
                                        <div class="text-xs text-gray-500">Via Surat Jalan</div>
                                    @else
                                        <div class="text-xs text-blue-600">Manual Input</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->tanggal->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">Rp {{ number_format($item->grand_total_bersih, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($item->status === 'draft')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Draft</span>
                                    @elseif($item->status === 'submitted')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Submitted</span>
                                    @elseif($item->status === 'approved')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Approved</span>
                                    @elseif($item->status === 'paid')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Paid</span>
                                    @elseif($item->status === 'cancelled')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Cancelled</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        @can('pranota-uang-rit-view')
                                        <a href="{{ route('pranota-uang-rit.show', $item) }}" 
                                           class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-100 hover:bg-blue-200 rounded-lg transition-colors duration-200" 
                                           title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan

                                        @can('pranota-uang-rit-view')
                                        <button type="button" 
                                                class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-purple-600 bg-purple-100 hover:bg-purple-200 rounded-lg transition-colors duration-200" 
                                                title="Print Ritasi Supir"
                                                onclick="printRitasiSupir({{ $item->id }})">
                                            <i class="fas fa-print"></i>
                                        </button>
                                        @endcan

                                        @can('pranota-uang-rit-view')
                                        <button type="button" 
                                                class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-green-600 bg-green-100 hover:bg-green-200 rounded-lg transition-colors duration-200" 
                                                title="Export Excel"
                                                onclick="exportSinglePranota({{ $item->id }}, '{{ $item->no_pranota }}', '{{ $item->tanggal->format('d/m/Y') }}', {{ $item->grand_total_bersih }}, '{{ $item->status }}')">
                                            <i class="fas fa-file-excel"></i>
                                        </button>
                                        @endcan

                                        @can('pranota-uang-rit-update')
                                            @if(in_array($item->status, ['draft', 'submitted']))
                                            <a href="{{ route('pranota-uang-rit.edit', $item) }}" 
                                               class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-yellow-600 bg-yellow-100 hover:bg-yellow-200 rounded-lg transition-colors duration-200" 
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endif

                                            @if($item->status === 'draft')
                                            <form action="{{ route('pranota-uang-rit.submit', $item) }}" method="POST" 
                                                  class="inline-block">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-green-600 bg-green-100 hover:bg-green-200 rounded-lg transition-colors duration-200" 
                                                        title="Submit untuk Approval"
                                                        onclick="return confirm('Yakin ingin submit pranota ini untuk approval?')">
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>
                                            </form>
                                            @endif
                                        @endcan

                                        @can('pranota-uang-rit-approve')
                                            @if($item->status === 'submitted')
                                            <form action="{{ route('pranota-uang-rit.approve', $item) }}" method="POST" 
                                                  class="inline-block">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-100 hover:bg-blue-200 rounded-lg transition-colors duration-200" 
                                                        title="Approve"
                                                        onclick="return confirm('Yakin ingin approve pranota ini?')">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            @endif
                                        @endcan

                                        @can('pranota-uang-rit-mark-paid')
                                            @if($item->status === 'approved')
                                            <button type="button" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-green-600 bg-green-100 hover:bg-green-200 rounded-lg transition-colors duration-200" 
                                                    title="Mark as Paid"
                                                    onclick="markAsPaid({{ $item->id }})">
                                                <i class="fas fa-money-bill-wave"></i>
                                            </button>
                                            @endif
                                        @endcan

                                        @can('pranota-uang-rit-delete')
                                            @if($item->status === 'draft')
                                            <form action="{{ route('pranota-uang-rit.destroy', $item) }}" method="POST" 
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
                    @include('components.modern-pagination', ['paginator' => $pranotaUangRits])
                    @include('components.rows-per-page')
                </div>
                @else
                <div class="text-center py-12">
                    <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-500 mb-2">Tidak ada data pranota uang rit</h3>
                    <p class="text-gray-400 mb-6">Data akan muncul di sini setelah Anda menambahkan pranota uang rit.</p>
                    @can('pranota-uang-rit-create')
                    <a href="{{ route('pranota-uang-rit.select-date') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                        <i class="fas fa-plus mr-2"></i> Tambah Pranota Uang Rit Pertama
                    </a>
                    @endcan
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal for Mark as Paid -->
<div class="fixed inset-0 z-50 overflow-y-auto hidden" id="markAsPaidModal" aria-labelledby="markAsPaidModalLabel" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        
        <!-- This element is to trick the browser into centering the modal contents. -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-money-bill-wave text-green-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="markAsPaidModalLabel">
                            Mark as Paid
                        </h3>
                        <div class="mt-4">
                            <form id="markAsPaidForm" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label for="tanggal_bayar" class="block text-sm font-medium text-gray-700 mb-2">
                                        Tanggal Pembayaran <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors" 
                                           id="tanggal_bayar" name="tanggal_bayar" 
                                           value="{{ date('Y-m-d') }}" required>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="submit" form="markAsPaidForm" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors duration-200">
                    <i class="fas fa-money-bill-wave mr-2"></i>
                    Mark as Paid
                </button>
                <button type="button" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors duration-200" onclick="closeModal()">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
function markAsPaid(pranotaId) {
    document.getElementById('markAsPaidForm').action = '/pranota-uang-rit/' + pranotaId + '/mark-as-paid';
    document.getElementById('markAsPaidModal').classList.remove('hidden');
}

function printRitasiSupir(pranotaId) {
    // Open print page in new window
    const printUrl = '/pranota-uang-rit/' + pranotaId + '/print';
    const printWindow = window.open(printUrl, '_blank', 'width=800,height=600');
    
    // Auto print when page loads
    if (printWindow) {
        printWindow.addEventListener('load', function() {
            printWindow.print();
        });
    }
}

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
            <title>Pranota Uang Rit - Print</title>
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
    const table = document.getElementById('pranotaUangRitTable');
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
        { wch: 20 }, // Uang Rit
        { wch: 15 }  // Status
    ];
    
    XLSX.utils.book_append_sheet(wb, ws, 'Pranota Uang Rit');
    
    // Generate filename with current date
    const date = new Date().toISOString().split('T')[0];
    const filename = `Pranota_Uang_Rit_${date}.xlsx`;
    
    // Save file
    XLSX.writeFile(wb, filename);
}

function exportSinglePranota(id, noPranota, tanggal, uangRit, status) {
    // Create data array
    const data = [
        ['No. Pranota', 'Tanggal', 'Uang Rit', 'Status'],
        [noPranota, tanggal, 'Rp ' + new Intl.NumberFormat('id-ID').format(uangRit), status]
    ];
    
    // Create workbook
    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.aoa_to_sheet(data);
    
    // Set column widths
    ws['!cols'] = [
        { wch: 20 }, // No. Pranota
        { wch: 15 }, // Tanggal
        { wch: 20 }, // Uang Rit
        { wch: 15 }  // Status
    ];
    
    XLSX.utils.book_append_sheet(wb, ws, 'Pranota Uang Rit');
    
    // Generate filename
    const filename = `${noPranota}.xlsx`;
    
    // Save file
    XLSX.writeFile(wb, filename);
}

function closeModal() {
    document.getElementById('markAsPaidModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('markAsPaidModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});
</script>
@endpush