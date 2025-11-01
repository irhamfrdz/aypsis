@extends('layouts.app')

@section('title', 'Pranota Uang Kenek')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">💰 Pranota Uang Kenek</h1>
                <p class="text-gray-600 mt-1">Kelola daftar pranota uang kenek</p>
            </div>
            @can('pranota-uang-kenek-create')
            <a href="{{ route('pranota-uang-kenek.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                <i class="fas fa-plus mr-2"></i> Tambah Pranota Uang Kenek
            </a>
            @endcan
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Filter & Pencarian</h3>
            </div>
            <div class="p-6">
                <form method="GET" action="{{ route('pranota-uang-kenek.index') }}">
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
                <h3 class="text-lg font-medium text-gray-900">Daftar Pranota Uang Kenek</h3>
            </div>
            <div class="p-6">
                @if($pranotaUangKeneks->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Pranota</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Surat Jalan</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Uang Kenek</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($pranotaUangKeneks as $item)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->no_pranota }}</div>
                                    @if($item->suratJalan)
                                        <div class="text-xs text-gray-500">Via Surat Jalan</div>
                                    @else
                                        <div class="text-xs text-blue-600">Manual Input</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') : '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->no_surat_jalan }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">Rp {{ number_format($item->total_uang, 0, ',', '.') }}</td>
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
                                        @can('pranota-uang-kenek-view')
                                        <a href="{{ route('pranota-uang-kenek.show', $item) }}" 
                                           class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-100 hover:bg-blue-200 rounded-lg transition-colors duration-200" 
                                           title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan

                                        @can('pranota-uang-kenek-view')
                                        <button type="button" 
                                                class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-purple-600 bg-purple-100 hover:bg-purple-200 rounded-lg transition-colors duration-200" 
                                                title="Print Ritasi Kenek"
                                                onclick="printRitasiKenek({{ $item->id }})">
                                            <i class="fas fa-print"></i>
                                        </button>
                                        @endcan

                                        @if($item->status === 'draft')
                                            @can('pranota-uang-kenek-edit')
                                            <a href="{{ route('pranota-uang-kenek.edit', $item) }}" 
                                               class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-yellow-600 bg-yellow-100 hover:bg-yellow-200 rounded-lg transition-colors duration-200" 
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan

                                            @can('pranota-uang-kenek-delete')
                                            <button type="button" 
                                                    class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-600 bg-red-100 hover:bg-red-200 rounded-lg transition-colors duration-200" 
                                                    title="Hapus"
                                                    onclick="deletePranota({{ $item->id }}, '{{ $item->no_pranota }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @endcan
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-12">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                        <i class="fas fa-file-invoice-dollar text-2xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada data pranota uang kenek</h3>
                    <p class="text-gray-500 mb-4">Mulai dengan membuat pranota uang kenek pertama Anda</p>
                    @can('pranota-uang-kenek-create')
                    <a href="{{ route('pranota-uang-kenek.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                        <i class="fas fa-plus mr-2"></i> Tambah Pranota Uang Kenek
                    </a>
                    @endcan
                </div>
                @endif
            </div>
        </div>

        <!-- Pagination -->
        @if($pranotaUangKeneks->hasPages())
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6 rounded-lg shadow-sm">
            <div class="flex-1 flex justify-between sm:hidden">
                @if($pranotaUangKeneks->previousPageUrl())
                    <a href="{{ $pranotaUangKeneks->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Previous</a>
                @endif
                @if($pranotaUangKeneks->nextPageUrl())
                    <a href="{{ $pranotaUangKeneks->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Next</a>
                @endif
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Showing <span class="font-medium">{{ $pranotaUangKeneks->firstItem() }}</span> to <span class="font-medium">{{ $pranotaUangKeneks->lastItem() }}</span> of <span class="font-medium">{{ $pranotaUangKeneks->total() }}</span> results
                    </p>
                </div>
                <div>
                    {{ $pranotaUangKeneks->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
function printRitasiKenek(id) {
    const url = `/pranota-uang-kenek/${id}/print`;
    const printWindow = window.open(url, '_blank', 'width=800,height=600');
    
    printWindow.onload = function() {
        printWindow.print();
        printWindow.onafterprint = function() {
            printWindow.close();
        };
    };
}

function deletePranota(id, noPranota) {
    if (confirm(`Yakin ingin menghapus pranota ${noPranota}?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/pranota-uang-kenek/${id}`;
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

$(document).ready(function() {
    // Mark as Paid Modal handlers (converted to pure JS)
    window.showMarkAsPaidModal = function(pranotaId, pranotaNo) {
        document.getElementById('pranotaNoText').textContent = pranotaNo;
        document.getElementById('markAsPaidForm').action = '/pranota-uang-kenek/' + pranotaId + '/mark-as-paid';
        document.getElementById('markAsPaidModal').classList.remove('hidden');
        document.getElementById('markAsPaidModal').classList.add('flex');
    };
    
    window.hideMarkAsPaidModal = function() {
        document.getElementById('markAsPaidModal').classList.add('hidden');
        document.getElementById('markAsPaidModal').classList.remove('flex');
    };
});
</script>
@endsection

<!-- Mark as Paid Modal - Tailwind CSS -->
<div id="markAsPaidModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <form id="markAsPaidForm" method="POST">
            @csrf
            @method('PATCH')
            <div class="flex items-center justify-between mb-4">
                <h5 class="text-lg font-medium text-gray-900">Tandai Sebagai Dibayar</h5>
                <button type="button" onclick="hideMarkAsPaidModal()" class="text-gray-400 hover:text-gray-600">
                    <span class="sr-only">Close</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="mb-4">
                <div class="mb-4">
                    <label for="tanggal_bayar" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Bayar</label>
                    <input type="date" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                           id="tanggal_bayar" 
                           name="tanggal_bayar" 
                           value="{{ date('Y-m-d') }}" 
                           required>
                </div>
                <p class="text-sm text-gray-600">Yakin tandai pranota <strong id="pranotaNoText"></strong> sebagai dibayar?</p>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" 
                        onclick="hideMarkAsPaidModal()" 
                        class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium rounded-lg transition-colors duration-200">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200">
                    Ya, Tandai Dibayar
                </button>
            </div>
        </form>
    </div>
</div>