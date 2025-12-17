@extends('layouts.app')

@section('title', 'Detail Pembayaran Pranota OB')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <h1 class="text-2xl font-bold text-gray-900">Detail Pembayaran Pranota OB</h1>
                <div class="mt-4 sm:mt-0">
                    <a href="{{ route('pembayaran-pranota-ob.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Payment Details -->
        <div class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Pembayaran</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nomor Pembayaran</dt>
                            <dd class="text-sm text-gray-900">{{ $pembayaran->nomor_pembayaran }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tanggal Kas</dt>
                            <dd class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($pembayaran->tanggal_kas)->format('d/m/Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Bank</dt>
                            <dd class="text-sm text-gray-900">{{ $pembayaran->bank }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Jenis Transaksi</dt>
                            <dd class="text-sm text-gray-900">{{ ucfirst($pembayaran->jenis_transaksi) }}</dd>
                        </div>
                        @if($pembayaran->kapal)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Kapal / Voyage</dt>
                            <dd class="text-sm text-gray-900">{{ $pembayaran->kapal }} / {{ $pembayaran->voyage }}</dd>
                        </div>
                        @endif
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="text-sm">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($pembayaran->status === 'approved') bg-green-100 text-green-800
                                    @elseif($pembayaran->status === 'pending') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($pembayaran->status) }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>

                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Total Pembayaran</h3>
                    <dl class="space-y-3">
                        @if($pembayaran->pembayaranOb)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">DP ({{ $pembayaran->pembayaranOb->nomor_pembayaran }})</dt>
                            <dd class="text-sm font-semibold text-green-600">Rp {{ number_format($pembayaran->dp_amount, 0, ',', '.') }}</dd>
                        </div>
                        @endif
                        @if($pembayaran->total_biaya_pranota)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Total Biaya Pranota</dt>
                            <dd class="text-sm text-gray-900">Rp {{ number_format($pembayaran->total_biaya_pranota, 0, ',', '.') }}</dd>
                        </div>
                        @endif
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Total Pembayaran (Sisa)</dt>
                            <dd class="text-lg font-semibold text-gray-900">Rp {{ number_format($pembayaran->total_pembayaran, 0, ',', '.') }}</dd>
                        </div>
                        @if($pembayaran->penyesuaian != 0)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Penyesuaian</dt>
                            <dd class="text-sm text-gray-900">{{ $pembayaran->penyesuaian > 0 ? '+' : '' }}Rp {{ number_format($pembayaran->penyesuaian, 0, ',', '.') }}</dd>
                        </div>
                        @endif
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Total Setelah Penyesuaian</dt>
                            <dd class="text-lg font-semibold text-blue-600">Rp {{ number_format($pembayaran->total_setelah_penyesuaian ?? $pembayaran->total_pembayaran, 0, ',', '.') }}</dd>
                        </div>
                        @if($pembayaran->alasan_penyesuaian)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Alasan Penyesuaian</dt>
                            <dd class="text-sm text-gray-900">{{ $pembayaran->alasan_penyesuaian }}</dd>
                        </div>
                        @endif
                        @if($pembayaran->keterangan)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Keterangan</dt>
                            <dd class="text-sm text-gray-900">{{ $pembayaran->keterangan }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        <!-- Breakdown Per Supir -->
        @if($pembayaran->breakdown_supir && is_array($pembayaran->breakdown_supir) && count($pembayaran->breakdown_supir) > 0)
        <div class="px-6 py-4 border-t border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Breakdown Per Supir</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Supir</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Item</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Biaya</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">DP</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Sisa</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Pot. Utang</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Pot. Tabungan</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Pot. BPJS</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Grand Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($pembayaran->breakdown_supir as $breakdown)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <i class="fas fa-user text-purple-500 mr-2"></i>
                                        <span class="text-sm font-medium text-gray-900">{{ $breakdown['nama_supir'] }}</span>
                                        @if($breakdown['dp'] > 0)
                                            <span class="ml-2 text-xs text-green-600">(Ada DP)</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $breakdown['jumlah_item'] }} item
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                    Rp {{ number_format($breakdown['total_biaya'], 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-right {{ $breakdown['dp'] > 0 ? 'text-green-700' : 'text-gray-400' }}">
                                    Rp {{ number_format($breakdown['dp'], 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-right {{ $breakdown['sisa'] > 0 ? 'text-red-700' : 'text-gray-500' }}">
                                    Rp {{ number_format($breakdown['sisa'], 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right {{ isset($breakdown['potongan_utang']) && $breakdown['potongan_utang'] > 0 ? 'text-orange-700' : 'text-gray-400' }}">
                                    Rp {{ number_format($breakdown['potongan_utang'] ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right {{ isset($breakdown['potongan_tabungan']) && $breakdown['potongan_tabungan'] > 0 ? 'text-orange-700' : 'text-gray-400' }}">
                                    Rp {{ number_format($breakdown['potongan_tabungan'] ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right {{ isset($breakdown['potongan_bpjs']) && $breakdown['potongan_bpjs'] > 0 ? 'text-orange-700' : 'text-gray-400' }}">
                                    Rp {{ number_format($breakdown['potongan_bpjs'] ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-right text-blue-700">
                                    Rp {{ number_format($breakdown['grand_total'] ?? $breakdown['sisa'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td class="px-6 py-3 text-left text-xs font-bold text-gray-800">Total</td>
                            <td class="px-6 py-3 text-center text-xs font-bold text-gray-800">
                                {{ array_sum(array_column($pembayaran->breakdown_supir, 'jumlah_item')) }} item
                            </td>
                            <td class="px-6 py-3 text-right text-xs font-bold text-gray-800">
                                Rp {{ number_format(array_sum(array_column($pembayaran->breakdown_supir, 'total_biaya')), 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-3 text-right text-xs font-bold text-green-800">
                                Rp {{ number_format(array_sum(array_column($pembayaran->breakdown_supir, 'dp')), 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-3 text-right text-xs font-bold text-red-800">
                                Rp {{ number_format(array_sum(array_column($pembayaran->breakdown_supir, 'sisa')), 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-3 text-right text-xs font-bold text-orange-800">
                                Rp {{ number_format(array_sum(array_column($pembayaran->breakdown_supir, 'potongan_utang')), 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-3 text-right text-xs font-bold text-orange-800">
                                Rp {{ number_format(array_sum(array_column($pembayaran->breakdown_supir, 'potongan_tabungan')), 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-3 text-right text-xs font-bold text-orange-800">
                                Rp {{ number_format(array_sum(array_column($pembayaran->breakdown_supir, 'potongan_bpjs')), 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-3 text-right text-xs font-bold text-blue-800">
                                Rp {{ number_format(array_sum(array_column($pembayaran->breakdown_supir, 'grand_total')), 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        @endif

        <!-- Pranota Items -->
        <div class="px-6 py-4 border-t border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Pranota OB yang Dibayar</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nomor Pranota
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kapal / Voyage
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jumlah Item
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total Tagihan
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jumlah Dibayar
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            $pranotaObs = $pembayaran->pranota_obs ?? collect([]);
                        @endphp
                        @forelse($pranotaObs as $pranota)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $pranota->nomor_pranota }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $pranota->nama_kapal }} / {{ $pranota->no_voyage }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                    @php
                                        $itemsCount = ($pranota->itemsPivot && $pranota->itemsPivot->count()) ? $pranota->itemsPivot->count() : (is_array($pranota->items) ? count($pranota->items) : 0);
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ $itemsCount }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($pranota->created_at)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    Rp {{ number_format($pranota->calculateTotalAmount(), 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    Rp {{ number_format($pranota->calculateTotalAmount(), 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Tidak ada pranota OB
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Actions -->
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="flex justify-end space-x-3">
                <a href="{{ route('pembayaran-pranota-ob.print', $pembayaran->id) }}"
                   class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150"
                   target="_blank">
                    <i class="fas fa-print mr-2"></i>
                    Cetak
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
