@extends('layouts.app')

@section('title', 'Detail Pranota Uang Supir')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow-lg rounded-lg p-4">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl font-bold text-gray-900">📋 Detail Pranota Uang Supir</h1>
                <p class="text-sm text-gray-600 mt-1">{{ $pranotaUangRit->no_pranota }}</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2">
                @can('pranota-uang-rit-update')
                    @if(in_array($pranotaUangRit->status, ['draft', 'submitted']))
                    <a href="{{ route('pranota-uang-rit.edit', $pranotaUangRit) }}" 
                       class="inline-flex items-center px-3 py-2 border border-yellow-300 rounded-md text-sm text-yellow-700 bg-white hover:bg-yellow-50 focus:outline-none focus:ring-2 focus:ring-yellow-500 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit
                    </a>
                    @endif
                @endcan
                <a href="{{ route('pranota-uang-rit.index') }}" 
                   class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        @php
            // Definisikan kelas Tailwind untuk input yang lebih compact
            $inputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm text-sm p-2";
            $labelClasses = "block text-xs font-medium text-gray-700 mb-1";
        @endphp

        <!-- Data Pranota & Total Uang dalam satu baris -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-3">
            <!-- Data Pranota -->
            <div class="lg:col-span-2">
                <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-800 mb-2">💰 Data Pranota Uang Supir</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        <div>
                            <label for="nomor_pranota" class="{{ $labelClasses }}">Nomor Pranota</label>
                            <input type="text" class="{{ $inputClasses }} font-medium text-indigo-600" 
                                   value="{{ $pranotaUangRit->no_pranota }}" readonly>
                        </div>
                        <div>
                            <label for="tanggal" class="{{ $labelClasses }}">Tanggal</label>
                            <input type="text" class="{{ $inputClasses }}" 
                                   value="{{ $pranotaUangRit->tanggal ? $pranotaUangRit->tanggal->format('d/m/Y') : '-' }}" readonly>
                        </div>
                        <div>
                            <label for="status" class="{{ $labelClasses }}">Status</label>
                            <span class="mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                {{ $pranotaUangRit->status === 'draft' ? 'bg-gray-100 text-gray-800' : 
                                   ($pranotaUangRit->status === 'submitted' ? 'bg-blue-100 text-blue-800' : 
                                   ($pranotaUangRit->status === 'approved' ? 'bg-green-100 text-green-800' : 
                                   ($pranotaUangRit->status === 'paid' ? 'bg-purple-100 text-purple-800' : 'bg-red-100 text-red-800'))) }}">
                                {{ $pranotaUangRit->status_label }}
                            </span>
                        </div>
                        <div>
                            <label for="created_by" class="{{ $labelClasses }}">Dibuat Oleh</label>
                            <input type="text" class="{{ $inputClasses }}" 
                                   value="{{ $pranotaUangRit->creator->name ?? '-' }}" readonly>
                        </div>
                    </div>
                    @if($pranotaUangRit->keterangan)
                    <div class="mt-2">
                        <label for="keterangan" class="{{ $labelClasses }}">Keterangan</label>
                        <textarea class="{{ $inputClasses }}" rows="2" readonly>{{ $pranotaUangRit->keterangan }}</textarea>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Total Uang -->
            <div class="lg:col-span-2">
                <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-800 mb-2">📊 Total Keseluruhan</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                        <div>
                            <label for="jumlah_surat_jalan" class="{{ $labelClasses }}">Jumlah Surat Jalan</label>
                            <input type="text" class="{{ $inputClasses }}" value="{{ $groupedPranota->count() }}" readonly>
                        </div>
                        <div>
                            <label for="jumlah_supir" class="{{ $labelClasses }}">Jumlah Supir</label>
                            <input type="text" class="{{ $inputClasses }}" value="{{ $supirDetails->count() ?? 1 }}" readonly>
                        </div>
                        <div>
                            <label for="total_uang_rit" class="{{ $labelClasses }}">Total Uang Supir</label>
                            <input type="text" class="{{ $inputClasses }} font-bold text-indigo-600" 
                                   value="Rp {{ number_format($pranotaUangRit->total_uang, 0, ',', '.') }}" readonly>
                        </div>
                        <div>
                            <label for="total_utang" class="{{ $labelClasses }}">Total Utang</label>
                            <input type="text" class="{{ $inputClasses }} font-bold text-red-600" 
                                   value="Rp {{ number_format($pranotaUangRit->total_hutang, 0, ',', '.') }}" readonly>
                        </div>
                        <div>
                            <label for="total_tabungan" class="{{ $labelClasses }}">Total Tabungan</label>
                            <input type="text" class="{{ $inputClasses }} font-bold text-green-600" 
                                   value="Rp {{ number_format($pranotaUangRit->total_tabungan, 0, ',', '.') }}" readonly>
                        </div>
                        <div>
                            <label for="total_bpjs" class="{{ $labelClasses }}">Total BPJS</label>
                            <input type="text" class="{{ $inputClasses }} font-bold text-yellow-600" 
                                   value="Rp {{ number_format($pranotaUangRit->total_bpjs, 0, ',', '.') }}" readonly>
                        </div>
                        <div>
                            <label for="grand_total" class="{{ $labelClasses }}">Grand Total</label>
                            <input type="text" class="{{ $inputClasses }} font-bold text-purple-600" 
                                   value="Rp {{ number_format($pranotaUangRit->grand_total_bersih, 0, ',', '.') }}" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Surat Jalan -->
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden mt-4">
            <div class="bg-gray-50 px-3 py-2 border-b border-gray-200">
                <h4 class="text-sm font-semibold text-gray-800">🚚 Detail Surat Jalan</h4>
            </div>

            <div class="overflow-x-auto max-h-60">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0 z-20">
                        <tr>
                            <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Surat Jalan</th>
                            <th class="px-2 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supir</th>
                            <th class="px-2 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jml SJ</th>
                            <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Uang Supir</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @if($groupedPranota && $groupedPranota->count() > 0)
                            @foreach($groupedPranota as $pranota)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-2 py-2 whitespace-nowrap text-xs font-medium">{{ $pranota->no_surat_jalan ?? '-' }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs text-center">{{ $pranota->tanggal ? $pranota->tanggal->format('d/m/Y') : '-' }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">{{ $pranota->supir_nama ?? '-' }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs text-center">1</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-right text-xs font-semibold text-indigo-600">
                                        Rp {{ number_format($pranota->uang_rit_supir, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            {{-- Fallback: show the main pranota data if no grouped data --}}
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-2 py-2 whitespace-nowrap text-xs font-medium">{{ $pranotaUangRit->no_surat_jalan ?? '-' }}</td>
                                <td class="px-2 py-2 whitespace-nowrap text-xs text-center">{{ $pranotaUangRit->tanggal ? $pranotaUangRit->tanggal->format('d/m/Y') : '-' }}</td>
                                <td class="px-2 py-2 whitespace-nowrap text-xs">{{ $pranotaUangRit->supir_nama ?? '-' }}</td>
                                <td class="px-2 py-2 whitespace-nowrap text-xs text-center">1</td>
                                <td class="px-2 py-2 whitespace-nowrap text-right text-xs font-semibold text-indigo-600">
                                    Rp {{ number_format($pranotaUangRit->uang_rit_supir, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endif
                    </tbody>
                    <tfoot class="bg-gray-100 border-t-2 border-gray-300">
                        <!-- Detail Per Supir -->
                        @if($supirDetails && $supirDetails->count() > 0)
                            <tbody class="bg-yellow-50 border-t-2 border-yellow-300">
                                <tr class="bg-yellow-100 font-semibold text-gray-700">
                                    <td class="px-2 py-2 text-xs font-bold" colspan="5">
                                        📊 DETAIL PER SUPIR
                                    </td>
                                </tr>
                                @foreach($supirDetails as $detail)
                                    <tr class="bg-yellow-50 text-gray-700 border-t border-yellow-200">
                                        <td class="px-2 py-2 text-xs font-medium" colspan="2">
                                            👤 {{ $detail->supir_nama }}
                                        </td>
                                        <td class="px-2 py-2 text-xs text-center">
                                            {{ $groupedPranota->where('supir_nama', $detail->supir_nama)->count() }} surat jalan
                                        </td>
                                        <td class="px-2 py-2 text-right text-xs font-semibold text-indigo-600">
                                            Rp {{ number_format($detail->total_uang_supir, 0, ',', '.') }}
                                        </td>
                                        <td class="px-2 py-2 text-right text-xs">
                                            <div class="flex gap-1 text-xs">
                                                <div class="bg-red-50 border border-red-200 rounded px-2 py-1 text-red-700">
                                                    Hutang: Rp {{ number_format($detail->hutang, 0, ',', '.') }}
                                                </div>
                                                <div class="bg-green-50 border border-green-200 rounded px-2 py-1 text-green-700">
                                                    Tabungan: Rp {{ number_format($detail->tabungan, 0, ',', '.') }}
                                                </div>
                                                <div class="bg-yellow-50 border border-yellow-200 rounded px-2 py-1 text-yellow-700">
                                                    BPJS: Rp {{ number_format($detail->bpjs, 0, ',', '.') }}
                                                </div>
                                                <div class="bg-purple-50 border border-purple-200 rounded px-2 py-1 font-semibold text-purple-700">
                                                    Total: Rp {{ number_format($detail->grand_total, 0, ',', '.') }}
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        @endif
                        
                        <!-- Overall Grand Total -->
                        <tr class="font-semibold text-gray-800 bg-gray-200">
                            <td class="px-2 py-3 text-xs font-bold" colspan="4">
                                GRAND TOTAL KESELURUHAN
                            </td>
                            <td class="px-2 py-3 text-right text-xs font-bold text-indigo-600">
                                Rp {{ number_format($pranotaUangRit->total_uang, 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr class="font-semibold text-gray-800 bg-gray-200">
                            <td class="px-2 py-3 text-xs font-bold" colspan="4">
                                TOTAL HUTANG
                            </td>
                            <td class="px-2 py-3 text-right text-xs font-bold text-red-600">
                                Rp {{ number_format($pranotaUangRit->total_hutang, 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr class="font-semibold text-gray-800 bg-gray-200">
                            <td class="px-2 py-3 text-xs font-bold" colspan="4">
                                TOTAL TABUNGAN
                            </td>
                            <td class="px-2 py-3 text-right text-xs font-bold text-green-600">
                                Rp {{ number_format($pranotaUangRit->total_tabungan, 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr class="font-semibold text-gray-800 bg-gray-200">
                            <td class="px-2 py-3 text-xs font-bold" colspan="4">
                                TOTAL BPJS
                            </td>
                            <td class="px-2 py-3 text-right text-xs font-bold text-yellow-600">
                                Rp {{ number_format($pranotaUangRit->total_bpjs, 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr class="font-semibold text-gray-800 bg-purple-200">
                            <td class="px-2 py-3 text-xs font-bold" colspan="4">
                                GRAND TOTAL BERSIH
                            </td>
                            <td class="px-2 py-3 text-right text-xs font-bold text-purple-600">
                                Rp {{ number_format($pranotaUangRit->grand_total_bersih, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Timeline / History (optional) -->
        @if($pranotaUangRit->created_at || $pranotaUangRit->approved_at)
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden mt-4">
            <div class="bg-gray-50 px-3 py-2 border-b border-gray-200">
                <h4 class="text-sm font-semibold text-gray-800">📅 Timeline</h4>
            </div>
            <div class="p-3">
                <div class="space-y-2">
                    @if($pranotaUangRit->created_at)
                    <div class="flex items-center text-xs text-gray-600">
                        <div class="w-2 h-2 bg-blue-400 rounded-full mr-2"></div>
                        <span class="font-medium">Dibuat:</span>
                        <span class="ml-1">{{ $pranotaUangRit->created_at->format('d/m/Y H:i') }}</span>
                        @if($pranotaUangRit->creator)
                        <span class="ml-1">oleh {{ $pranotaUangRit->creator->name }}</span>
                        @endif
                    </div>
                    @endif
                    
                    @if($pranotaUangRit->approved_at)
                    <div class="flex items-center text-xs text-gray-600">
                        <div class="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
                        <span class="font-medium">Disetujui:</span>
                        <span class="ml-1">{{ $pranotaUangRit->approved_at ? $pranotaUangRit->approved_at->format('d/m/Y H:i') : '-' }}</span>
                        @if($pranotaUangRit->approver)
                        <span class="ml-1">oleh {{ $pranotaUangRit->approver->name }}</span>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection