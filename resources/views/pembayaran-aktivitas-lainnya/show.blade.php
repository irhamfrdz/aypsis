@extends('layouts.app')

@section('title', 'Detail Pembayaran Aktivitas Lain-lain')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="w-full">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <h3 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-file-invoice-dollar mr-3"></i>
                        Detail Pembayaran Aktivitas Lain-lain
                    </h3>
                    <div class="flex flex-wrap gap-2 mt-3 sm:mt-0">
                        @can('pembayaran-aktivitas-lainnya-update')
                            @if($pembayaran->status !== 'paid')
                                <a href="{{ route('pembayaran-aktivitas-lainnya.edit', $pembayaran->id) }}" class="inline-flex items-center px-3 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                                    <i class="fas fa-edit mr-2"></i> Edit
                                </a>
                            @endif
                        @endcan
                        @can('pembayaran-aktivitas-lainnya-print')
                            <a href="{{ route('pembayaran-aktivitas-lainnya.print', $pembayaran->id) }}" target="_blank" class="inline-flex items-center px-3 py-2 bg-cyan-500 hover:bg-cyan-600 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                                <i class="fas fa-print mr-2"></i> Print
                            </a>
                        @endcan
                        @can('pembayaran-aktivitas-lainnya-delete')
                            @if($pembayaran->status === 'draft')
                                <button onclick="confirmDelete()" class="inline-flex items-center px-3 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                                    <i class="fas fa-trash mr-2"></i> Hapus
                                </button>
                            @endif
                        @endcan
                        <a href="{{ route('pembayaran-aktivitas-lainnya.index') }}" class="inline-flex items-center px-3 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                            <i class="fas fa-arrow-left mr-2"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>

            <div class="px-6 py-6">
                <!-- Status Badge -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 p-4 bg-gray-50 rounded-lg">
                    <div class="mb-3 sm:mb-0">
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-lg font-semibold {{ $pembayaran->status === 'paid' ? 'bg-green-100 text-green-800' : ($pembayaran->status === 'approved' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                            <i class="fas fa-{{ $pembayaran->status === 'paid' ? 'check-circle' : ($pembayaran->status === 'approved' ? 'clock' : 'edit') }} mr-2"></i>
                            {{ ucfirst($pembayaran->status) }}
                        </span>
                    </div>
                    <div class="text-right text-sm text-gray-600">
                        <div>Dibuat: {{ $pembayaran->created_at->format('d/m/Y H:i') }}</div>
                        <div>Diupdate: {{ $pembayaran->updated_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>

                <div class="border-t border-gray-200 mb-6"></div>

                <!-- Informasi Pembayaran -->
                <div class="mb-8">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-info-circle mr-3 text-blue-600"></i>
                        Informasi Pembayaran
                    </h2>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="bg-gray-50 rounded-lg p-6">
                            <div class="space-y-4">
                                <div class="flex justify-between">
                                    <span class="font-semibold text-gray-700">Nomor Pembayaran:</span>
                                    <span class="text-gray-900">{{ $pembayaran->nomor_pembayaran ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-semibold text-gray-700">Tanggal Pembayaran:</span>
                                    <span class="text-gray-900">{{ $pembayaran->tanggal_pembayaran ? $pembayaran->tanggal_pembayaran->format('d/m/Y') : '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-semibold text-gray-700">Metode Pembayaran:</span>
                                    <div>
                                        @switch($pembayaran->metode_pembayaran)
                                            @case('cash')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Cash</span>
                                                @break
                                            @case('transfer')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Transfer</span>
                                                @break
                                            @case('check')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Check</span>
                                                @break
                                            @case('credit_card')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Credit Card</span>
                                                @break
                                            @default
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">-</span>
                                        @endswitch
                                    </div>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-semibold text-gray-700">Referensi:</span>
                                    <span class="text-gray-900">{{ $pembayaran->referensi_pembayaran ?? '-' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-6 border border-green-200">
                            <div class="space-y-4">
                                <div class="flex justify-between">
                                    <span class="font-semibold text-gray-700">Total Nominal:</span>
                                    <span class="text-2xl font-bold text-green-600">
                                        Rp {{ number_format($pembayaran->total_nominal, 0, ',', '.') }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-semibold text-gray-700">Jumlah Aktivitas:</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $pembayaran->detailPembayaran ? $pembayaran->detailPembayaran->count() : 0 }} aktivitas
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-semibold text-gray-700">Dibuat Oleh:</span>
                                    <span class="text-gray-900">{{ $pembayaran->createdBy->name ?? '-' }}</span>
                                </div>
                                @if($pembayaran->approved_by)
                                    <div class="flex justify-between">
                                        <span class="font-semibold text-gray-700">Disetujui Oleh:</span>
                                        <span class="text-gray-900">{{ $pembayaran->approvedBy->name ?? '-' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="font-semibold text-gray-700">Tanggal Persetujuan:</span>
                                        <span class="text-gray-900">{{ $pembayaran->approved_at ? $pembayaran->approved_at->format('d/m/Y H:i') : '-' }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if($pembayaran->keterangan)
                    <div class="mb-8">
                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                            <h3 class="text-lg font-semibold text-amber-800 mb-2 flex items-center">
                                <i class="fas fa-comment-alt mr-2"></i>
                                Keterangan
                            </h3>
                            <p class="text-amber-700 whitespace-pre-line">{{ $pembayaran->keterangan }}</p>
                        </div>
                    </div>
                @endif

                <div class="border-t border-gray-200 mb-6"></div>

                <!-- Detail Aktivitas -->
                <div class="mb-8">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
                        <h2 class="text-xl font-bold text-gray-800 flex items-center mb-2 sm:mb-0">
                            <i class="fas fa-list mr-3 text-blue-600"></i>
                            Detail Aktivitas yang Dibayar
                        </h2>
                        <button class="inline-flex items-center px-4 py-2 border border-blue-300 text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-md text-sm font-medium transition duration-150 ease-in-out" id="exportBtn">
                            <i class="fas fa-download mr-2"></i>
                            Export
                        </button>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm">
                        <table class="min-w-full divide-y divide-gray-200" id="aktivitas_detail_table">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">#</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Aktivitas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Nominal Asli</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Nominal Dibayar</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Selisih</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($pembayaran->detailPembayaran ?? [] as $index => $detail)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="#" class="text-blue-600 hover:text-blue-900 font-medium" data-toggle="modal" data-target="#aktivitasModal{{ $detail->id }}">
                                                {{ $detail->aktivitasLain->nomor_aktivitas ?? '-' }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ ($detail->aktivitasLain && $detail->aktivitasLain->tanggal_aktivitas) ? $detail->aktivitasLain->tanggal_aktivitas->format('d/m/Y') : '-' }}</td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $detail->aktivitasLain->deskripsi_aktivitas ?? '' }}">
                                                {{ $detail->aktivitasLain->deskripsi_aktivitas ?? '-' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ ($detail->aktivitasLain && $detail->aktivitasLain->vendor) ? $detail->aktivitasLain->vendor->nama : '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">Rp {{ $detail->aktivitasLain ? number_format($detail->aktivitasLain->nominal, 0, ',', '.') : '0' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600 text-right">
                                            Rp {{ number_format($detail->nominal_dibayar, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                            @php
                                                $nominalAsli = $detail->aktivitasLain ? $detail->aktivitasLain->nominal : 0;
                                                $selisih = $nominalAsli - $detail->nominal_dibayar;
                                            @endphp
                                            @if($selisih > 0)
                                                <span class="text-amber-600 font-medium">-Rp {{ number_format($selisih, 0, ',', '.') }}</span>
                                            @elseif($selisih < 0)
                                                <span class="text-blue-600 font-medium">+Rp {{ number_format(abs($selisih), 0, ',', '.') }}</span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                    </tr>

                                    <!-- Modal untuk detail aktivitas -->
                                    @if($detail->aktivitasLain)
                                    <div class="modal fade" id="aktivitasModal{{ $detail->id }}" tabindex="-1" role="dialog">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Detail Aktivitas {{ $detail->aktivitasLain->nomor_aktivitas ?? '-' }}</h5>
                                                    <button type="button" class="close" data-dismiss="modal">
                                                        <span>&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <table class="table table-borderless">
                                                                <tr>
                                                                    <td class="font-weight-bold">Nomor:</td>
                                                                    <td>{{ $detail->aktivitasLain->nomor_aktivitas ?? '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="font-weight-bold">Tanggal:</td>
                                                                    <td>{{ ($detail->aktivitasLain && $detail->aktivitasLain->tanggal_aktivitas) ? $detail->aktivitasLain->tanggal_aktivitas->format('d/m/Y') : '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="font-weight-bold">Vendor:</td>
                                                                    <td>{{ ($detail->aktivitasLain && $detail->aktivitasLain->vendor) ? $detail->aktivitasLain->vendor->nama : '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="font-weight-bold">Nominal:</td>
                                                                    <td>Rp {{ $detail->aktivitasLain ? number_format($detail->aktivitasLain->nominal, 0, ',', '.') : '0' }}</td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <table class="table table-borderless">
                                                                <tr>
                                                                    <td class="font-weight-bold">Dibayar:</td>
                                                                    <td class="text-success font-weight-bold">
                                                                        Rp {{ number_format($detail->nominal_dibayar, 0, ',', '.') }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="font-weight-bold">Status:</td>
                                                                    <td>
                                                                        <span class="badge badge-success">Dibayar</span>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label class="font-weight-bold">Deskripsi:</label>
                                                                <div class="border rounded p-3 bg-light">
                                                                    {{ $detail->aktivitasLain->deskripsi_aktivitas ?? '-' }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center">
                                                <i class="fas fa-info-circle text-4xl text-gray-400 mb-4"></i>
                                                <p class="text-gray-500 text-lg">Tidak ada detail aktivitas.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($pembayaran->detailPembayaran && $pembayaran->detailPembayaran->count() > 0)
                                <tfoot class="bg-gray-50">
                                    <tr class="border-t-2 border-gray-200">
                                        <th colspan="6" class="px-6 py-4 text-right text-sm font-semibold text-gray-700 uppercase">Total:</th>
                                        <th class="px-6 py-4 text-right text-lg font-bold text-green-600">
                                            Rp {{ number_format($pembayaran->detailPembayaran->sum('nominal_dibayar'), 0, ',', '.') }}
                                        </th>
                                        <th class="px-6 py-4"></th>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>

                <!-- Action Buttons -->
                @if($pembayaran->status !== 'paid')
                    <div class="border-t border-gray-200 mt-8 pt-6">
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-cogs mr-2 text-blue-600"></i>
                                Aksi Tersedia
                            </h3>
                            <div class="flex flex-wrap gap-3">
                                @if($pembayaran->status === 'draft')
                                    @can('pembayaran-aktivitas-lainnya-update')
                                        <a href="{{ route('pembayaran-aktivitas-lainnya.edit', $pembayaran->id) }}" class="inline-flex items-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-medium rounded-lg transition duration-150 ease-in-out transform hover:scale-105">
                                            <i class="fas fa-edit mr-2"></i>
                                            Edit Pembayaran
                                        </a>
                                    @endcan
                                    @can('pembayaran-aktivitas-lainnya-approve')
                                        <form action="{{ route('pembayaran-aktivitas-lainnya.approve', $pembayaran->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-medium rounded-lg transition duration-150 ease-in-out transform hover:scale-105" onclick="return confirm('Apakah Anda yakin ingin menyetujui pembayaran ini?')">
                                                <i class="fas fa-check mr-2"></i>
                                                Setujui Pembayaran
                                            </button>
                                        </form>
                                    @endcan
                                @elseif($pembayaran->status === 'approved')
                                    @can('pembayaran-aktivitas-lainnya-approve')
                                        <form action="{{ route('pembayaran-aktivitas-lainnya.pay', $pembayaran->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-500 hover:bg-green-600 text-white font-medium rounded-lg transition duration-150 ease-in-out transform hover:scale-105" onclick="return confirm('Apakah Anda yakin pembayaran ini sudah dilakukan?')">
                                                <i class="fas fa-credit-card mr-2"></i>
                                                Tandai sebagai Dibayar
                                            </button>
                                        </form>
                                    @endcan
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div class="border-t border-gray-200 mt-8 pt-6">
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-lg p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle text-3xl text-green-500"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-green-800">Pembayaran Selesai!</h3>
                                    <p class="text-green-700 mt-1">Pembayaran ini telah ditandai sebagai selesai dan tidak dapat diubah lagi.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form -->
@can('pembayaran-aktivitas-lainnya-delete')
    @if($pembayaran->status === 'draft')
        <form id="deleteForm" action="{{ route('pembayaran-aktivitas-lainnya.destroy', $pembayaran->id) }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    @endif
@endcan
@endsection

@push('styles')
<style>
    /* Custom animations */
    .hover-scale:hover {
        transform: scale(1.02);
        transition: transform 0.2s ease-in-out;
    }

    /* Custom gradients */
    .gradient-text {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* Modal backdrop blur effect */
    .modal-backdrop {
        backdrop-filter: blur(4px);
    }

    /* Smooth transitions for all interactive elements */
    .transition-all {
        transition: all 0.15s ease-in-out;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Export functionality
    $('#exportBtn').on('click', function() {
        // Simple table to CSV export
        let csv = [];
        let headers = [];

        // Get headers
        $('#aktivitas_detail_table thead tr th').each(function() {
            headers.push($(this).text().trim());
        });
        csv.push(headers.join(','));

        // Get data
        $('#aktivitas_detail_table tbody tr').each(function() {
            let row = [];
            $(this).find('td').each(function() {
                let text = $(this).text().trim().replace(/,/g, ';');
                row.push(text);
            });
            if (row.length > 0) {
                csv.push(row.join(','));
            }
        });

        // Download
        let csvContent = csv.join('\n');
        let blob = new Blob([csvContent], { type: 'text/csv' });
        let url = window.URL.createObjectURL(blob);
        let a = document.createElement('a');
        a.href = url;
        a.download = 'pembayaran_aktivitas_{{ $pembayaran->id }}_{{ date("Y-m-d") }}.csv';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    });
});

function confirmDelete() {
    if (confirm('Apakah Anda yakin ingin menghapus pembayaran ini? Tindakan ini tidak dapat dibatalkan.')) {
        document.getElementById('deleteForm').submit();
    }
}
</script>
@endpush
