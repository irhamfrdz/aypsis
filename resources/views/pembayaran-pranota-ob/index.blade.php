@extends('layouts.app')

@section('title', 'Pembayaran Pranota OB')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <h1 class="text-2xl font-bold text-gray-900">Pembayaran Pranota OB</h1>
                <div class="mt-4 sm:mt-0 flex space-x-3">
                    <a href="{{ route('pembayaran-pranota-ob.select-criteria') }}"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <i class="fas fa-plus mr-2"></i>
                        Buat Pembayaran Baru
                    </a>
                    <a href="{{ route('pranota-ob.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali ke Pranota OB
                    </a>
                </div>
            </div>
        </div>

        <!-- Alerts -->
        @if (session('success'))
            <div class="mx-6 mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="mx-6 mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 resizable-table" id="pembayaranPranotaObTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">
                            Nomor Pembayaran
                            <div class="resize-handle"></div>
                        </th>
                        <th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">
                            Tanggal
                            <div class="resize-handle"></div>
                        </th>
                        <th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">
                            Kapal
                            <div class="resize-handle"></div>
                        </th>
                        <th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">
                            Voyage
                            <div class="resize-handle"></div>
                        </th>
                        <th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">
                            Total Pembayaran
                            <div class="resize-handle"></div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($pembayaranList as $pembayaran)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $pembayaran->nomor_pembayaran }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    ID: #{{ $pembayaran->id }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($pembayaran->tanggal_kas)->format('d/m/Y') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $pembayaran->kapal ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $pembayaran->voyage ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    // Get grand total from breakdown_supir
                                    $breakdownSupir = $pembayaran->breakdown_supir;
                                    if (is_string($breakdownSupir)) {
                                        $breakdownSupir = json_decode($breakdownSupir, true) ?? [];
                                    }
                                    $breakdownSupir = is_array($breakdownSupir) ? $breakdownSupir : [];
                                    
                                    $grandTotal = 0;
                                    if (!empty($breakdownSupir)) {
                                        foreach ($breakdownSupir as $breakdown) {
                                            $grandTotal += (float)($breakdown['grand_total'] ?? $breakdown['sisa'] ?? 0);
                                        }
                                    } else {
                                        // Fallback to total_setelah_penyesuaian if no breakdown
                                        $grandTotal = $pembayaran->total_setelah_penyesuaian ?? $pembayaran->total_pembayaran;
                                    }
                                @endphp
                                <div class="text-sm font-medium text-gray-900">
                                    Rp {{ number_format($grandTotal, 0, ',', '.') }}
                                </div>
                                @if($pembayaran->penyesuaian != 0)
                                    <div class="text-sm text-gray-500">
                                        Penyesuaian: {{ $pembayaran->penyesuaian > 0 ? '+' : '' }}{{ number_format($pembayaran->penyesuaian, 0, ',', '.') }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('pembayaran-pranota-ob.show', $pembayaran->id) }}"
                                   class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i class="fas fa-eye"></i> Lihat Detail
                                </a>
                                <span class="text-gray-300">|</span>
                                <a href="{{ route('pembayaran-pranota-ob.edit', $pembayaran->id) }}"
                                   class="text-yellow-600 hover:text-yellow-900 mr-3">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <span class="text-gray-300">|</span>
                                <a href="{{ route('pembayaran-pranota-ob.print', $pembayaran->id) }}"
                                   class="text-green-600 hover:text-green-900 mr-3" target="_blank">
                                    <i class="fas fa-print"></i> Print
                                </a>
                                @can('audit-log-view')
                                <span class="text-gray-300">|</span>
                                <button type="button"
                                        class="audit-log-btn text-purple-600 hover:text-purple-900 bg-transparent border-none cursor-pointer"
                                        data-model-type="App\Models\PembayaranPranotaOb"
                                        data-model-id="{{ $pembayaran->id }}"
                                        data-item-name="{{ $pembayaran->nomor_pembayaran ?? 'Pembayaran OB' }}"
                                        title="Lihat Riwayat">
                                    <i class="fas fa-history"></i> Riwayat
                                </button>
                                @endcan

                                @can('pembayaran-pranota-ob-delete')
                                <span class="text-gray-300">|</span>
                                <form action="{{ route('pembayaran-pranota-ob.destroy', $pembayaran->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pembayaran ini? Status pranota akan dikembalikan menjadi belum dibayar dan transaksi COA akan dihapus.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 bg-transparent border-none cursor-pointer">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                Tidak ada data pembayaran pranota OB.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($pembayaranList->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                @include('components.modern-pagination', ['paginator' => $pembayaranList])
                @include('components.rows-per-page')
            </div>
        @endif
    </div>
</div>

<!-- Audit Log Modal -->
@include('components.audit-log-modal')

@endsection

@include('components.resizable-table')

@push('scripts')
<script>
$(document).ready(function() {
    initResizableTable('pembayaranPranotaObTable');
});
</script>
@endpush
