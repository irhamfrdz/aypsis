@extends('layouts.app')

@section('content')
<div class="container mx-auto px-3 py-2">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 mb-4">
            <div>
                <h1 class="text-lg font-semibold text-gray-900">Detail Pranota Uang Jalan</h1>
                <p class="text-xs text-gray-600 mt-0.5">{{ $pranotaUangJalan->nomor_pranota }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('pranota-uang-jalan.print', $pranotaUangJalan) }}" 
                   class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded text-sm flex items-center" target="_blank">
                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Cetak
                </a>
                @if(in_array($pranotaUangJalan->status_pembayaran, ['unpaid', 'approved', 'paid']))
                    <form action="{{ route('pranota-uang-jalan.update-total', $pranotaUangJalan) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded text-sm flex items-center" onclick="return confirm('Apakah Anda yakin ingin memperbarui total pranota ini? Total akan dihitung ulang berdasarkan uang jalan yang tersisa.')">
                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Update Total
                        </button>
                    </form>
                @endif
                @if(in_array($pranotaUangJalan->status_pembayaran, ['unpaid', 'approved']))
                    <a href="{{ route('pranota-uang-jalan.edit', $pranotaUangJalan) }}" 
                       class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-1.5 rounded text-sm flex items-center">
                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </a>
                    <button type="button" onclick="openAddUangJalanModal()"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded text-sm flex items-center">
                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Tambah Uang Jalan
                    </button>
                @endif
                <a href="{{ route('pranota-uang-jalan.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1.5 rounded text-sm flex items-center">
                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Pranota Information -->
        <div class="bg-white rounded border border-gray-200 p-4 mb-4">
            <h3 class="text-sm font-medium text-gray-900 mb-3 border-b border-gray-200 pb-2">Informasi Pranota</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500">Nomor Pranota</label>
                    <div class="text-sm font-semibold text-gray-900 mt-1">{{ $pranotaUangJalan->nomor_pranota }}</div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500">Tanggal Pranota</label>
                    <div class="text-sm text-gray-900 mt-1">{{ $pranotaUangJalan->tanggal_pranota->format('d/m/Y') }}</div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500">Periode Tagihan</label>
                    <div class="text-sm text-gray-900 mt-1">{{ $pranotaUangJalan->periode_tagihan }}</div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500">Jumlah Uang Jalan</label>
                    <div class="text-sm text-gray-900 mt-1">{{ $pranotaUangJalan->jumlah_uang_jalan }} item</div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500">Subtotal Uang Jalan</label>
                    <div class="text-sm text-gray-900 mt-1">{{ $pranotaUangJalan->formatted_total }}</div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500">Penyesuaian</label>
                    <div class="text-sm text-gray-900 mt-1">{{ $pranotaUangJalan->formatted_penyesuaian }}</div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500">Total Akhir</label>
                    <div class="text-lg font-bold text-gray-900 mt-1">{{ $pranotaUangJalan->formatted_total_with_penyesuaian }}</div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500">Status Pembayaran</label>
                    <div class="mt-1">
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $pranotaUangJalan->status_badge }}">
                            {{ $pranotaUangJalan->status_text }}
                        </span>
                    </div>
                </div>
                @if($pranotaUangJalan->catatan)
                <div class="md:col-span-3">
                    <label class="block text-xs font-medium text-gray-500">Catatan</label>
                    <div class="text-sm text-gray-900 mt-1">{{ $pranotaUangJalan->catatan }}</div>
                </div>
                @endif
                @if($pranotaUangJalan->keterangan_penyesuaian)
                <div class="md:col-span-3">
                    <label class="block text-xs font-medium text-gray-500">Keterangan Penyesuaian</label>
                    <div class="text-sm text-gray-900 mt-1">{{ $pranotaUangJalan->keterangan_penyesuaian }}</div>
                </div>
                @endif
                <div class="md:col-span-3 text-xs text-gray-500 border-t border-gray-200 pt-2">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="font-medium">Dibuat oleh:</span> {{ $pranotaUangJalan->creator->name ?? 'N/A' }}
                            <br><span class="font-medium">Pada:</span> {{ $pranotaUangJalan->created_at->format('d/m/Y H:i') }}
                        </div>
                        @if($pranotaUangJalan->updated_at != $pranotaUangJalan->created_at)
                        <div>
                            <span class="font-medium">Diperbarui oleh:</span> {{ $pranotaUangJalan->updater->name ?? 'N/A' }}
                            <br><span class="font-medium">Pada:</span> {{ $pranotaUangJalan->updated_at->format('d/m/Y H:i') }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Uang Jalan List -->
        <div class="bg-white rounded border border-gray-200 p-4">
            <h3 class="text-sm font-medium text-gray-900 mb-3 border-b border-gray-200 pb-2">Daftar Uang Jalan</h3>
            
            @if($pranotaUangJalan->uangJalans->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">No. Uang Jalan</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Surat Jalan</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kegiatan</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Supir/Kenek</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Bank</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                @if(in_array($pranotaUangJalan->status_pembayaran, ['unpaid', 'paid']))
                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase scale-90">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($pranotaUangJalan->uangJalans as $index => $uangJalan)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2 text-sm text-gray-900">{{ $index + 1 }}</td>
                                    <td class="px-3 py-2">
                                        <div class="text-sm font-medium text-gray-900">{{ $uangJalan->nomor_uang_jalan }}</div>
                                        <div class="text-xs text-gray-500">{{ $uangJalan->nomor_kas_bank }}</div>
                                    </td>
                                    <td class="px-3 py-2 text-sm text-gray-900">
                                        {{ $uangJalan->tanggal_uang_jalan ? $uangJalan->tanggal_uang_jalan->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="px-3 py-2">
                                        @php
                                            $surat = $uangJalan->suratJalan ?? $uangJalan->suratJalanBongkaran;
                                        @endphp
                                        @if($surat)
                                            <div class="text-sm text-gray-900">{{ $surat->no_surat_jalan ?? $surat->nomor_surat_jalan ?? '-' }}</div>
                                            <div class="text-xs text-gray-500">{{ $surat->tanggal_surat_jalan ? $surat->tanggal_surat_jalan->format('d/m/Y') : '' }}</div>
                                        @else
                                            <div class="text-sm text-gray-500">-</div>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="text-sm text-gray-900">{{ $uangJalan->kegiatan_bongkar_muat }}</div>
                                        <div class="text-xs text-gray-500">{{ $uangJalan->kategori_uang_jalan }}</div>
                                    </td>
                                    <td class="px-3 py-2">
                                        @php
                                            $surat = $uangJalan->suratJalan ?? $uangJalan->suratJalanBongkaran;
                                        @endphp
                                        @if($surat)
                                            <div class="text-xs text-gray-900">
                                                <div><strong>Supir:</strong> {{ $surat->supir ?? '-' }}</div>
                                                <div><strong>Kenek:</strong> {{ $surat->kenek ?? '-' }}</div>
                                                <div><strong>Plat:</strong> {{ $surat->no_plat ?? '-' }}</div>
                                            </div>
                                        @else
                                            <div class="text-xs text-gray-500">-</div>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="text-sm text-gray-900">{{ $uangJalan->bank_kas }}</div>
                                        <div class="text-xs text-gray-500">{{ $uangJalan->tanggal_kas_bank ? $uangJalan->tanggal_kas_bank->format('d/m/Y') : '' }}</div>
                                    </td>
                                    <td class="px-3 py-2 text-right">
                                        <div class="text-sm font-semibold text-gray-900">
                                            Rp {{ number_format($uangJalan->jumlah_total, 0, ',', '.') }}
                                        </div>
                                        @if($uangJalan->subtotal != $uangJalan->jumlah_total)
                                            <div class="text-xs text-gray-500">
                                                Subtotal: Rp {{ number_format($uangJalan->subtotal, 0, ',', '.') }}
                                                @if($uangJalan->jumlah_penyesuaian != 0)
                                                    <br>Penyesuaian: Rp {{ number_format($uangJalan->jumlah_penyesuaian, 0, ',', '.') }}
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    @if(in_array($pranotaUangJalan->status_pembayaran, ['unpaid', 'paid']))
                                        <td class="px-3 py-2 text-center">
                                            <form action="{{ route('pranota-uang-jalan.remove-uang-jalan', [$pranotaUangJalan, $uangJalan]) }}" method="POST" onsubmit="return confirm('Keluarkan uang jalan ini dari pranota?')">
                                                @csrf
                                                <button type="submit" class="text-red-500 hover:text-red-700 p-1 rounded hover:bg-red-50 transition-colors" title="Keluarkan dari Pranota">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="{{ in_array($pranotaUangJalan->status_pembayaran, ['unpaid', 'paid']) ? '8' : '7' }}" class="px-3 py-3 text-right text-sm font-semibold text-gray-900">
                                    Total Keseluruhan:
                                </td>
                                <td class="px-3 py-3 text-right text-lg font-bold text-gray-900">
                                    {{ $pranotaUangJalan->formatted_total }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Summary Card -->
                <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded">
                    <div class="flex items-start">
                        <svg class="h-4 w-4 text-blue-400 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="flex-1">
                            <h4 class="text-xs font-medium text-blue-800 mb-1">Ringkasan Pranota</h4>
                            <div class="text-xs text-blue-700 grid grid-cols-1 md:grid-cols-3 gap-2">
                                <div><strong>Total Item:</strong> {{ $pranotaUangJalan->jumlah_uang_jalan }} uang jalan</div>
                                <div><strong>Total Amount:</strong> {{ $pranotaUangJalan->formatted_total }}</div>
                                <div><strong>Status:</strong> {{ $pranotaUangJalan->status_text }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="h-12 w-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-gray-500 text-sm">Tidak ada uang jalan dalam pranota ini</p>
                </div>
            @endif
        </div>

        <!-- Action Buttons -->
        @if($pranotaUangJalan->status_pembayaran === 'unpaid')
            <div class="flex justify-end gap-2 mt-4">
                <form action="{{ route('pranota-uang-jalan.destroy', $pranotaUangJalan) }}" 
                      method="POST" 
                      onsubmit="return confirm('Yakin ingin menghapus pranota ini? Semua uang jalan akan dikembalikan ke status belum dibayar.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm flex items-center">
                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Hapus Pranota
                    </button>
                </form>
            </div>
        @endif

        <!-- Modal Add Uang Jalan -->
        <div id="addUangJalanModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeAddUangJalanModal()"></div>

                <!-- Center modal content -->
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-middle bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <form action="{{ route('pranota-uang-jalan.add-uang-jalan', $pranotaUangJalan) }}" method="POST">
                        @csrf
                        <div class="bg-white px-6 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="flex justify-between items-center pb-3 border-b border-gray-200 mb-4">
                                <h3 class="text-lg font-semibold text-gray-900" id="modal-title">
                                    Tambah Uang Jalan ke Pranota
                                </h3>
                                <button type="button" class="text-gray-400 hover:text-gray-500" onclick="closeAddUangJalanModal()">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            <!-- Search Input -->
                            <div class="mb-4">
                                <input type="text" id="modalSearchInput" placeholder="Cari No. Uang Jalan, Surat Jalan, Supir..." 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500 bg-white"
                                       onkeyup="filterModalUangJalan()">
                            </div>

                            <!-- Uang Jalan Checkbox List -->
                            <div class="max-h-96 overflow-y-auto border border-gray-200 rounded-md">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50 sticky top-0">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">
                                                <input type="checkbox" id="selectAllUangJalan" onchange="toggleSelectAllUangJalan(this)">
                                            </th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Uang Jalan</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Surat Jalan</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supir</th>
                                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200" id="modalUangJalanList">
                                        @forelse($availableUangJalans as $uj)
                                            @php
                                                $surat = $uj->suratJalan ?? $uj->suratJalanBongkaran;
                                            @endphp
                                            <tr class="hover:bg-gray-50 modal-uj-row" 
                                                data-search="{{ strtolower(e($uj->nomor_uang_jalan . ' ' . ($surat ? ($surat->no_surat_jalan ?? $surat->nomor_surat_jalan) . ' ' . $surat->supir : ''))) }}">
                                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                                    <input type="checkbox" name="uang_jalan_ids[]" value="{{ $uj->id }}" class="uj-checkbox focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">{{ $uj->nomor_uang_jalan }}</div>
                                                    <div class="text-xs text-gray-500">{{ $uj->kegiatan_bongkar_muat }}</div>
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $uj->tanggal_uang_jalan ? $uj->tanggal_uang_jalan->format('d/m/Y') : '-' }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $surat ? ($surat->no_surat_jalan ?? $surat->nomor_surat_jalan ?? '-') : '-' }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $surat->supir ?? '-' }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-gray-900 text-right">
                                                    Rp {{ number_format($uj->jumlah_total, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">
                                                    Tidak ada uang jalan yang tersedia untuk ditambahkan.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-6 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Simpan
                            </button>
                            <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeAddUangJalanModal()">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openAddUangJalanModal() {
    document.getElementById('addUangJalanModal').style.display = 'block';
}

function closeAddUangJalanModal() {
    document.getElementById('addUangJalanModal').style.display = 'none';
}

function filterModalUangJalan() {
    const searchVal = document.getElementById('modalSearchInput').value.toLowerCase();
    const rows = document.querySelectorAll('.modal-uj-row');
    rows.forEach(row => {
        const text = row.getAttribute('data-search');
        if (text.includes(searchVal)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function toggleSelectAllUangJalan(selectAllCheckbox) {
    const checkboxes = document.querySelectorAll('.uj-checkbox');
    checkboxes.forEach(cb => {
        const row = cb.closest('tr');
        if (row && row.style.display !== 'none') {
            cb.checked = selectAllCheckbox.checked;
        }
    });
}

// Auto hide alerts after 3 seconds
setTimeout(function() {
    const successAlert = document.getElementById('success-alert');
    const errorAlert = document.getElementById('error-alert');
    if (successAlert) successAlert.remove();
    if (errorAlert) errorAlert.remove();
}, 3000);
</script>
@endpush

@if(session('success'))
    <div class="fixed bottom-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded" id="success-alert">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="fixed bottom-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" id="error-alert">
        {{ session('error') }}
    </div>
@endif
@endsection