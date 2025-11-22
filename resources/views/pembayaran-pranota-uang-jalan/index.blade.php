@extends('layouts.app')

@section('title', 'Pembayaran Pranota Uang Jalan')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">Pembayaran Pranota Uang Jalan</h1>
                    <p class="text-sm text-gray-600 mt-1">Kelola pembayaran untuk pranota uang jalan</p>
                </div>
                @can('pembayaran-pranota-uang-jalan-create')
                <a href="{{ route('pembayaran-pranota-uang-jalan.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Tambah Pembayaran
                </a>
                @endcan
            </div>
        </div>

        <!-- Filters -->
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nomor pembayaran, pranota..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Semua Status</option>
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Metode</label>
                    <select name="metode" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Semua Metode</option>
                        @foreach($methods as $value => $label)
                            <option value="{{ $value }}" {{ request('metode') == $value ? 'selected' : '' }}>{{ $label }}</option>
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
                    <tr><th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Nomor Pembayaran<div class="resize-handle"></div></th><th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Tanggal<div class="resize-handle"></div></th><th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Pranota<div class="resize-handle"></div></th><th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Jumlah<div class="resize-handle"></div></th><th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Metode<div class="resize-handle"></div></th><th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Status<div class="resize-handle"></div></th><th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th></tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pembayaran as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <a href="{{ route('pembayaran-pranota-uang-jalan.show', $item->id) }}" class="text-blue-600 hover:text-blue-800">
                                {{ $item->nomor_pembayaran }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $item->tanggal_pembayaran->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            @if($item->pranotaUangJalans->count() > 0)
                                @foreach($item->pranotaUangJalans->take(2) as $pranota)
                                    <div>{{ $pranota->nomor_pranota }}</div>
                                @endforeach
                                @if($item->pranotaUangJalans->count() > 2)
                                    <div class="text-xs text-gray-400">+{{ $item->pranotaUangJalans->count() - 2 }} lainnya</div>
                                @endif
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                            {{ $item->formatted_amount }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $item->method_label }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'paid' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                ];
                            @endphp
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$item->status_pembayaran] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $item->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                <a href="{{ route('pembayaran-pranota-uang-jalan.show', $item->id) }}" class="text-blue-600 hover:text-blue-800 text-xs bg-blue-50 hover:bg-blue-100 px-2 py-1 rounded">Detail</a>
                                @if(!$item->isPaid() && !$item->isCancelled())
                                    @can('pembayaran-pranota-uang-jalan-edit')
                                    <a href="{{ route('pembayaran-pranota-uang-jalan.edit', $item->id) }}" class="text-yellow-600 hover:text-yellow-800 text-xs bg-yellow-50 hover:bg-yellow-100 px-2 py-1 rounded">Edit</a>
                                    @endcan
                                    @can('pembayaran-pranota-uang-jalan-delete')
                                    <form method="POST" action="{{ route('pembayaran-pranota-uang-jalan.destroy', $item->id) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus pembayaran ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-xs bg-red-50 hover:bg-red-100 px-2 py-1 rounded">Hapus</button>
                                    </form>
                                    @endcan
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <h3 class="text-sm font-medium text-gray-900 mb-1">Tidak ada pembayaran</h3>
                                <p class="text-sm text-gray-500">Belum ada data pembayaran pranota uang jalan.</p>
                                @can('pembayaran-pranota-uang-jalan-create')
                                <a href="{{ route('pembayaran-pranota-uang-jalan.create') }}" class="mt-2 text-blue-600 hover:text-blue-500 text-sm">
                                    Buat pembayaran pertama
                                </a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($pembayaran->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $pembayaran->links() }}
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