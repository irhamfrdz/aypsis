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
                    <tr><th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Nomor Pranota<div class="resize-handle"></div></th><th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Tanggal<div class="resize-handle"></div></th><th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Supir<div class="resize-handle"></div></th><th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Total Amount<div class="resize-handle"></div></th><th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Status Pembayaran<div class="resize-handle"></div></th><th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th></tr>
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
                                        <a href="{{ route('pembayaran-pranota-uang-jalan.show', $pembayaran->id) }}" class="text-blue-600 hover:text-blue-800 text-xs bg-blue-50 hover:bg-blue-100 px-2 py-1 rounded">Detail Pembayaran</a>
                                    @endforeach
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
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
</script>
@endsection

@include('components.resizable-table')

@push('scripts')
<script>
$(document).ready(function() {
    initResizableTable('pembayaranPranotaUangJalanTable');
});
</script>
@endpush