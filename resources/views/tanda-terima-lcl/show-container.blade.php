@extends('layouts.app')

@section('title', 'Detail Kontainer - ' . $containerData['nomor_kontainer'])
@section('page_title', 'Detail Kontainer')

@section('content')
<div class="container mx-auto px-4 py-4">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-4 border-b border-gray-200">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">Detail Kontainer: {{ $containerData['nomor_kontainer'] }}</h1>
                    <p class="text-xs text-gray-600 mt-1">Informasi lengkap barang dalam kontainer</p>
                </div>
                <a href="{{ route('tanda-terima-lcl.stuffing') }}" 
                   style="display: inline-flex; align-items: center; padding: 0.5rem 1rem; background-color: #4b5563; color: #ffffff; border-radius: 0.5rem; font-size: 0.875rem; text-decoration: none; transition: all 0.2s; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);"
                   onmouseover="this.style.backgroundColor='#374151'" 
                   onmouseout="this.style.backgroundColor='#4b5563'"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Kembali
                </a>
            </div>

            <!-- Container Info Summary -->
            <div class="p-4 bg-gradient-to-r from-blue-50 to-indigo-50">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div class="text-center">
                        <div class="text-sm text-gray-600 mb-1">Nomor Kontainer</div>
                        <div class="text-lg font-bold text-gray-900">{{ $containerData['nomor_kontainer'] }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-sm text-gray-600 mb-1">Size / Tipe</div>
                        <div class="text-lg font-semibold text-gray-900">
                            {{ $containerData['size_kontainer'] ?? '-' }}
                            @if($containerData['tipe_kontainer'])
                                <span class="text-sm text-gray-500">/ {{ $containerData['tipe_kontainer'] }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="text-sm text-gray-600 mb-1">Total LCL</div>
                        <div class="text-lg font-bold text-blue-600">{{ $containerData['total_lcl'] }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-sm text-gray-600 mb-1">Volume Total</div>
                        <div class="text-lg font-semibold text-purple-600">{{ number_format($containerData['total_volume'], 2) }} m³</div>
                    </div>
                    <div class="text-center">
                        <div class="text-sm text-gray-600 mb-1">Berat Total</div>
                        <div class="text-lg font-semibold text-orange-600">{{ number_format($containerData['total_berat'], 2) }} ton</div>
                    </div>
                </div>

                @php
                    $firstPivot = $containerData['items']->first();
                    $hasSealed = $firstPivot && $firstPivot->nomor_seal;
                @endphp

                @if($hasSealed)
                    <div class="mt-4 flex items-center justify-center gap-2">
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Sudah Di-Seal: {{ $firstPivot->nomor_seal }}
                        </span>
                        @if($firstPivot->tanggal_seal)
                            <span class="text-sm text-gray-600">
                                pada {{ $firstPivot->tanggal_seal->format('d/m/Y') }}
                            </span>
                        @endif
                    </div>
                @else
                    <div class="mt-4 flex items-center justify-center">
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            Belum Di-Seal
                        </span>
                    </div>
                @endif
            </div>
        </div>

        <!-- LCL Items Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Daftar LCL dalam Kontainer</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Tanda Terima LCL</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barang</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penerima</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengirim</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Volume (m³)</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Berat (ton)</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Stuffing</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Oleh</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($containerData['items'] as $pivot)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    @if($pivot->tandaTerima)
                                        <div class="text-sm font-medium text-blue-600">
                                            <a href="{{ route('tanda-terima-lcl.show', $pivot->tandaTerima->id) }}" class="hover:text-blue-800">
                                                {{ $pivot->tandaTerima->nomor_tanda_terima ?? 'TT-LCL-' . $pivot->tandaTerima->id }}
                                            </a>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400">Data tidak tersedia</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($pivot->tandaTerima && $pivot->tandaTerima->items->isNotEmpty())
                                        <div class="space-y-1">
                                            @foreach($pivot->tandaTerima->items as $item)
                                                <div class="text-sm {{ !$loop->last ? 'pb-1 border-b border-gray-100' : '' }}">
                                                    <div class="font-medium text-gray-900">{{ $item->nama_barang }}</div>
                                                    <div class="text-xs text-gray-500 mt-0.5">
                                                        @if($item->jumlah)
                                                            {{ $item->jumlah }} {{ $item->satuan ?? 'pcs' }}
                                                        @endif
                                                        @if($item->panjang && $item->lebar && $item->tinggi)
                                                            | {{ $item->panjang }}×{{ $item->lebar }}×{{ $item->tinggi }} m
                                                        @endif
                                                        @if($item->meter_kubik)
                                                            | {{ number_format($item->meter_kubik, 3) }} m³
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($pivot->tandaTerima)
                                        <div class="text-sm text-gray-900">{{ $pivot->tandaTerima->nama_penerima ?? '-' }}</div>
                                        @if($pivot->tandaTerima->alamat_penerima)
                                            <div class="text-xs text-gray-500 mt-1">{{ Str::limit($pivot->tandaTerima->alamat_penerima, 50) }}</div>
                                        @endif
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($pivot->tandaTerima)
                                        <div class="text-sm text-gray-900">{{ $pivot->tandaTerima->nama_pengirim ?? '-' }}</div>
                                        @if($pivot->tandaTerima->alamat_pengirim)
                                            <div class="text-xs text-gray-500 mt-1">{{ Str::limit($pivot->tandaTerima->alamat_pengirim, 50) }}</div>
                                        @endif
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($pivot->tandaTerima && $pivot->tandaTerima->items)
                                        <div class="text-sm font-medium text-purple-600">{{ number_format($pivot->tandaTerima->items->sum('meter_kubik'), 3) }}</div>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($pivot->tandaTerima && $pivot->tandaTerima->items)
                                        <div class="text-sm font-medium text-orange-600">{{ number_format($pivot->tandaTerima->items->sum('tonase'), 3) }}</div>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $pivot->assigned_at ? $pivot->assigned_at->format('d/m/Y H:i') : '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $pivot->assignedByUser->name ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($pivot->tandaTerima)
                                        <button type="button"
                                                onclick="removeFromContainer({{ $pivot->tanda_terima_lcl_id }}, '{{ $containerData['nomor_kontainer'] }}', '{{ $pivot->tandaTerima->nomor_tanda_terima ?? 'TT-LCL-' . $pivot->tanda_terima_lcl_id }}')"
                                                class="inline-flex items-center px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-lg transition-colors shadow-sm"
                                                title="Keluarkan dari kontainer">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            Keluarkan
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 border-t-2 border-gray-300">
                        <tr>
                            <td colspan="4" class="px-6 py-3 text-right text-sm font-bold text-gray-900">Total:</td>
                            <td class="px-6 py-3 text-sm font-bold text-purple-600">{{ number_format($containerData['total_volume'], 3) }} m³</td>
                            <td class="px-6 py-3 text-sm font-bold text-orange-600">{{ number_format($containerData['total_berat'], 3) }} ton</td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function removeFromContainer(lclId, containerNumber, lclNumber) {
    if (confirm(`Apakah Anda yakin ingin mengeluarkan ${lclNumber} dari kontainer ${containerNumber}?\n\nTanda terima akan dikembalikan ke status "Belum Stuffing" dan dapat dimasukkan ke kontainer lain.`)) {
        // Show loading
        const button = event.target.closest('button');
        const originalHTML = button.innerHTML;
        button.innerHTML = '<svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
        button.disabled = true;

        // Send AJAX request
        fetch(`/tanda-terima-lcl/${lclId}/remove-from-container`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                nomor_kontainer: containerNumber
            })
        })
        .then(response => {
            // Parse response as JSON first
            return response.json().then(data => {
                // Return both status and data
                return {
                    ok: response.ok,
                    status: response.status,
                    data: data
                };
            });
        })
        .then(result => {
            if (result.ok && result.data.success) {
                // Show success message
                alert(result.data.message || 'Tanda terima berhasil dikeluarkan dari kontainer');
                
                // Reload page to update the list
                window.location.reload();
            } else {
                // Handle error responses
                let errorMessage = 'Gagal mengeluarkan tanda terima dari kontainer.\n\n';
                
                if (result.status === 404) {
                    errorMessage += 'Data tidak ditemukan atau sudah dihapus sebelumnya.';
                } else if (result.status === 403) {
                    errorMessage += 'Anda tidak memiliki izin untuk melakukan tindakan ini.';
                } else if (result.status === 500) {
                    errorMessage += 'Terjadi kesalahan pada server.\n';
                    errorMessage += result.data.message ? `Detail: ${result.data.message}` : 'Silakan hubungi administrator.';
                } else {
                    errorMessage += result.data.message || 'Terjadi kesalahan yang tidak diketahui.';
                }
                
                throw new Error(errorMessage);
            }
        })
        .catch(error => {
            console.error('Error removing from container:', error);
            
            // Show descriptive error message
            let displayMessage = 'Gagal mengeluarkan tanda terima dari kontainer.\n\n';
            
            if (error.message.includes('Failed to fetch') || error.message.includes('NetworkError')) {
                displayMessage += 'Koneksi ke server terputus. Periksa koneksi internet Anda dan coba lagi.';
            } else if (error.message.includes('Gagal mengeluarkan')) {
                // Error message already formatted from server
                displayMessage = error.message;
            } else {
                displayMessage += `Detail error: ${error.message}`;
            }
            
            alert(displayMessage);
            
            // Reset button
            button.innerHTML = originalHTML;
            button.disabled = false;
        });
    }
}
</script>
@endsection
