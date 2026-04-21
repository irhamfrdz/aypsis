@extends('layouts.app')

@section('title', 'Pembayaran Pranota Uang Jalan')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">Daftar Pranota Uang Jalan</h1>
                    <p class="text-sm text-gray-600 mt-1">Lihat status pembayaran pranota uang jalan</p>
                </div>
                @can('pembayaran-pranota-uang-jalan-create')
                <a href="{{ route('pembayaran-pranota-uang-jalan.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Bayar Pranota
                </a>
                @endcan
            </div>
        </div>

        <!-- Filters -->
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nomor pranota, nama supir..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status Pembayaran</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Semua Status</option>
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium mr-2">Filter</button>
                    <a href="{{ route('pembayaran-pranota-uang-jalan.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium">Reset</a>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 resizable-table" id="pembayaranPranotaUangJalanTable">
                <thead class="bg-gray-50">
                    <tr><th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Nomor Pranota<div class="resize-handle"></div></th><th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Tanggal<div class="resize-handle"></div></th><th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Supir<div class="resize-handle"></div></th><th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Total Amount<div class="resize-handle"></div></th><th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Nomor Accurate<div class="resize-handle"></div></th><th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Status Pembayaran<div class="resize-handle"></div></th><th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th></tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pranotaList as $pranota)
                    <tr class="hover:bg-gray-50 {{ $pranota->status_pembayaran == 'unpaid' ? 'bg-yellow-50' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $pranota->nomor_pranota }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $pranota->tanggal_pranota->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            @php
                                $supirList = $pranota->uangJalans->pluck('suratJalan.supir')->filter()->unique();
                            @endphp
                            @if($supirList->count() > 0)
                                @foreach($supirList->take(2) as $supir)
                                    <div>{{ $supir }}</div>
                                @endforeach
                                @if($supirList->count() > 2)
                                    <div class="text-xs text-gray-400">+{{ $supirList->count() - 2 }} lainnya</div>
                                @endif
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                            Rp {{ number_format($pranota->total_amount, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($pranota->pembayaranPranotaUangJalans->count() > 0)
                                @foreach($pranota->pembayaranPranotaUangJalans as $pembayaran)
                                    <div>{{ $pembayaran->nomor_accurate ?? '-' }}</div>
                                @endforeach
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusColors = [
                                    'unpaid' => 'bg-red-100 text-red-800',
                                    'paid' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-gray-100 text-gray-800',
                                ];
                                $statusLabels = [
                                    'unpaid' => 'Belum Dibayar',
                                    'paid' => 'Sudah Dibayar',
                                    'cancelled' => 'Dibatalkan',
                                ];
                            @endphp
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$pranota->status_pembayaran] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $statusLabels[$pranota->status_pembayaran] ?? $pranota->status_pembayaran }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                @if($pranota->status_pembayaran == 'unpaid')
                                    @can('pembayaran-pranota-uang-jalan-create')
                                    <a href="{{ route('pembayaran-pranota-uang-jalan.create', ['pranota_id' => $pranota->id]) }}" class="text-green-600 hover:text-green-800 text-xs bg-green-50 hover:bg-green-100 px-2 py-1 rounded">Bayar</a>
                                    @endcan
                                @endif
                                @if($pranota->pembayaranPranotaUangJalans->count() > 0)
                                    @foreach($pranota->pembayaranPranotaUangJalans as $pembayaran)
                                        <a href="{{ route('pembayaran-pranota-uang-jalan.show', $pembayaran->id) }}" class="text-blue-600 hover:text-blue-800 text-xs bg-blue-50 hover:bg-blue-100 px-2 py-1 rounded">Detail</a>
                                        @can('pembayaran-pranota-uang-jalan-edit')
                                        <a href="{{ route('pembayaran-pranota-uang-jalan.edit', $pembayaran->id) }}" class="text-yellow-600 hover:text-yellow-800 text-xs bg-yellow-50 hover:bg-yellow-100 px-2 py-1 rounded">Edit</a>
                                        <button type="button" 
                                                onclick="openUpdateDateModal('{{ $pembayaran->id }}', '{{ $pembayaran->nomor_pembayaran }}', '{{ $pembayaran->tanggal_pembayaran->format('Y-m-d') }}')" 
                                                class="text-purple-600 hover:text-purple-800 text-xs bg-purple-50 hover:bg-purple-100 px-2 py-1 rounded">
                                            Update Tanggal
                                        </button>
                                        @endcan
                                    @endforeach
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <h3 class="text-sm font-medium text-gray-900 mb-1">Tidak ada pranota</h3>
                                <p class="text-sm text-gray-500">Belum ada data pranota uang jalan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($pranotaList->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $pranotaList->links() }}
        </div>
        @endif
    </div>
</div>

@if(session('success'))
<div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50" id="success-alert">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50" id="error-alert">
    {{ session('error') }}
</div>
@endif

<script>
// Auto hide alerts after 3 seconds
setTimeout(function() {
    const successAlert = document.getElementById('success-alert');
    const errorAlert = document.getElementById('error-alert');
    if (successAlert) successAlert.remove();
    if (errorAlert) errorAlert.remove();
}, 3000);

function openUpdateDateModal(id, nomor, tanggal) {
    document.getElementById('update_pembayaran_id').value = id;
    document.getElementById('modal_nomor_pembayaran').textContent = nomor;
    document.getElementById('tanggal_baru').value = tanggal;
    document.getElementById('updateDateModal').classList.remove('hidden');
}

function closeUpdateDateModal() {
    document.getElementById('updateDateModal').classList.add('hidden');
}
</script>

<!-- Update Date Modal -->
<div id="updateDateModal" class="fixed z-50 inset-0 overflow-y-auto hidden" aria-labelledby="update-modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeUpdateDateModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <form action="{{ route('pembayaran-pranota-uang-jalan.update-date') }}" method="POST">
                @csrf
                <input type="hidden" name="pembayaran_id" id="update_pembayaran_id">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-purple-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="update-modal-title">
                                Update Tanggal Pembayaran
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Memperbarui tanggal untuk pembayaran <span id="modal_nomor_pembayaran" class="font-bold"></span>. 
                                    Tanggal ini akan disinkronkan dengan Laporan Kas Truck.
                                </p>
                            </div>
                            <div class="mt-4">
                                <label for="tanggal_baru" class="block text-sm font-medium text-gray-700">Tanggal Baru</label>
                                <input type="date" name="tanggal_baru" id="tanggal_baru" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Simpan Perubahan
                    </button>
                    <button type="button" onclick="closeUpdateDateModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@include('components.resizable-table')

@push('scripts')
<script>
$(document).ready(function() {
    initResizableTable('pembayaranPranotaUangJalanTable');
});
</script>
@endpush