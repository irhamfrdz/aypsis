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
                @if(in_array($pranotaUangJalan->status_pembayaran, ['unpaid', 'approved']))
                    <a href="{{ route('pranota-uang-jalan.edit', $pranotaUangJalan) }}" 
                       class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-1.5 rounded text-sm flex items-center">
                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </a>
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
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="7" class="px-3 py-3 text-right text-sm font-semibold text-gray-900">
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
    </div>
</div>

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