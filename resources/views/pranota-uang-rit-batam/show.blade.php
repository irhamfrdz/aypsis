@extends('layouts.app')

@section('title', 'Detail Pranota Uang Rit Batam')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow-lg rounded-lg p-4">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl font-bold text-gray-900">📋 Detail Pranota Uang Rit Batam</h1>
                <p class="text-sm text-gray-600 mt-1">{{ $pranota->nomor_pranota }}</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2">
                <a href="{{ route('pranota-uang-rit-batam.index') }}" 
                   class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
                @can('pranota-uang-rit-batam-delete')
                @if($pranota->status_pembayaran != 'paid')
                <form action="{{ route('pranota-uang-rit-batam.destroy', $pranota) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pranota ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-3 py-2 border border-red-300 rounded-md text-sm text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Hapus
                    </button>
                </form>
                @endif
                @endcan
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
                    <h4 class="text-sm font-semibold text-gray-800 mb-2">💰 Data Pranota Uang Rit Batam</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        <div>
                            <label class="{{ $labelClasses }}">Nomor Pranota</label>
                            <input type="text" class="{{ $inputClasses }} font-medium text-indigo-600" 
                                   value="{{ $pranota->nomor_pranota }}" readonly>
                        </div>
                        <div>
                            <label class="{{ $labelClasses }}">Tanggal</label>
                            <input type="text" class="{{ $inputClasses }}" 
                                   value="{{ $pranota->tanggal_pranota ? $pranota->tanggal_pranota->format('d/m/Y') : '-' }}" readonly>
                        </div>
                        <div>
                            <label class="{{ $labelClasses }}">Supir</label>
                            <input type="text" class="{{ $inputClasses }}" 
                                   value="{{ $pranota->supir_nama }}" readonly>
                        </div>
                        <div>
                            <label class="{{ $labelClasses }}">Status</label>
                            <span class="mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                {{ $pranota->status_pembayaran === 'paid' ? 'bg-green-100 text-green-800' : 
                                   ($pranota->status_pembayaran === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ $pranota->status_pembayaran === 'paid' ? 'Lunas' : ($pranota->status_pembayaran === 'cancelled' ? 'Dibatalkan' : 'Belum Bayar') }}
                            </span>
                        </div>
                        <div>
                            <label class="{{ $labelClasses }}">Dibuat Oleh</label>
                            <input type="text" class="{{ $inputClasses }}" 
                                   value="{{ $pranota->creator->name ?? '-' }}" readonly>
                        </div>
                    </div>
                    @if($pranota->catatan)
                    <div class="mt-2">
                        <label class="{{ $labelClasses }}">Catatan</label>
                        <textarea class="{{ $inputClasses }}" rows="2" readonly>{{ $pranota->catatan }}</textarea>
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
                            <label class="{{ $labelClasses }}">Jumlah Surat Jalan</label>
                            <input type="text" class="{{ $inputClasses }}" value="{{ $pranota->suratJalanBatams->count() }}" readonly>
                        </div>
                        <div>
                            <label class="{{ $labelClasses }}">Total Uang Rit</label>
                            <input type="text" class="{{ $inputClasses }} font-bold text-indigo-600" 
                                   value="Rp {{ number_format($pranota->total_rit, 0, ',', '.') }}" readonly>
                        </div>
                        <div>
                            <label class="{{ $labelClasses }}">Penyesuaian</label>
                            <input type="text" class="{{ $inputClasses }} font-bold text-yellow-600" 
                                   value="Rp {{ number_format($pranota->penyesuaian, 0, ',', '.') }}" readonly>
                        </div>
                        <div>
                            <label class="{{ $labelClasses }}">Grand Total</label>
                            <input type="text" class="{{ $inputClasses }} font-bold text-purple-600" 
                                   value="Rp {{ number_format($pranota->total_amount, 0, ',', '.') }}" readonly>
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
                            <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tujuan Ambil</th>
                            <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Uang Rit</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($pranota->suratJalanBatams as $sj)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-2 py-2 whitespace-nowrap text-xs font-medium">{{ $sj->no_surat_jalan }}</td>
                                <td class="px-2 py-2 whitespace-nowrap text-xs text-center">{{ $sj->tanggal_surat_jalan->format('d/m/Y') }}</td>
                                <td class="px-2 py-2 whitespace-nowrap text-xs">{{ $sj->tujuan_pengambilan ?? '-' }}</td>
                                <td class="px-2 py-2 whitespace-nowrap text-right text-xs font-semibold text-indigo-600">
                                    Rp {{ number_format($sj->pivot->uang_rit, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-2 py-4 text-center text-xs text-gray-400">Tidak ada data surat jalan</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-gray-100 border-t-2 border-gray-300">
                        <tr class="font-semibold text-gray-800 bg-gray-200">
                            <td class="px-2 py-3 text-xs font-bold" colspan="3">
                                TOTAL UANG RIT
                            </td>
                            <td class="px-2 py-3 text-right text-xs font-bold text-indigo-600">
                                Rp {{ number_format($pranota->total_rit, 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr class="font-semibold text-gray-800 bg-gray-200">
                            <td class="px-2 py-3 text-xs font-bold" colspan="3">
                                PENYESUAIAN
                            </td>
                            <td class="px-2 py-3 text-right text-xs font-bold text-yellow-600">
                                Rp {{ number_format($pranota->penyesuaian, 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr class="font-semibold text-gray-800 bg-purple-200">
                            <td class="px-2 py-3 text-xs font-bold" colspan="3">
                                GRAND TOTAL
                            </td>
                            <td class="px-2 py-3 text-right text-xs font-bold text-purple-600">
                                Rp {{ number_format($pranota->total_amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Timeline / History -->
        @if($pranota->created_at)
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden mt-4">
            <div class="bg-gray-50 px-3 py-2 border-b border-gray-200">
                <h4 class="text-sm font-semibold text-gray-800">📅 Timeline</h4>
            </div>
            <div class="p-3">
                <div class="space-y-2">
                    @if($pranota->created_at)
                    <div class="flex items-center text-xs text-gray-600">
                        <div class="w-2 h-2 bg-blue-400 rounded-full mr-2"></div>
                        <span class="font-medium">Dibuat:</span>
                        <span class="ml-1">{{ $pranota->created_at->format('d/m/Y H:i') }}</span>
                        @if($pranota->creator)
                        <span class="ml-1">oleh {{ $pranota->creator->name }}</span>
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
