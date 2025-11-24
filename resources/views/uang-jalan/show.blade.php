@extends('layouts.app')

@section('page_title', 'Detail Uang Jalan')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Detail Uang Jalan</h1>
                        <p class="text-gray-600 mt-1">
                            {{ $uangJalan->nomor_uang_jalan ?? 'No. Uang Jalan belum digenerate' }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-3">
                        @can('uang-jalan-update')
                        @if(in_array($uangJalan->status, ['belum_dibayar', 'belum_masuk_pranota']))
                        <a href="{{ route('uang-jalan.edit', $uangJalan->id) }}"
                           class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit Uang Jalan
                        </a>
                        @endif
                        @endcan
                        <a href="{{ route('uang-jalan.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Kembali ke Daftar
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Informasi Surat Jalan -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Informasi Surat Jalan</h2>
                    </div>
                    <div class="px-6 py-4">
                        @if($uangJalan->suratJalan)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">No. Surat Jalan</label>
                                <p class="text-sm text-gray-900 font-semibold">{{ $uangJalan->suratJalan->no_surat_jalan }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Surat Jalan</label>
                                <p class="text-sm text-gray-900">
                                    {{ $uangJalan->suratJalan->tanggal_surat_jalan ? $uangJalan->suratJalan->tanggal_surat_jalan->format('d/m/Y') : '-' }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kegiatan</label>
                                <p class="text-sm text-gray-900">{{ $uangJalan->suratJalan->kegiatan ?? '-' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Supir</label>
                                <p class="text-sm text-gray-900">{{ $uangJalan->suratJalan->supir ?? '-' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kenek</label>
                                <p class="text-sm text-gray-900">{{ $uangJalan->suratJalan->kenek ?? '-' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">No. Plat</label>
                                <p class="text-sm text-gray-900">{{ $uangJalan->suratJalan->no_plat ?? '-' }}</p>
                            </div>
                            @if($uangJalan->suratJalan->order)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">No. Order</label>
                                <p class="text-sm text-gray-900">{{ $uangJalan->suratJalan->order->nomor_order ?? '-' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pengirim</label>
                                <p class="text-sm text-gray-900">{{ $uangJalan->suratJalan->order->pengirim->nama_pengirim ?? '-' }}</p>
                            </div>
                            @endif
                        </div>
                        @else
                        <div class="text-center py-8">
                            <p class="text-gray-500">Data surat jalan tidak ditemukan</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Detail Uang Jalan -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Detail Pembayaran</h2>
                    </div>
                    <div class="px-6 py-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Komponen Biaya -->
                            <div class="space-y-4">
                                <h3 class="text-sm font-medium text-gray-900 border-b pb-2">Komponen Biaya</h3>
                                
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="text-sm text-gray-600">Uang Jalan</span>
                                    <span class="text-sm font-medium text-gray-900">
                                        Rp {{ number_format($uangJalan->jumlah_uang_jalan ?? 0, 0, ',', '.') }}
                                    </span>
                                </div>
                                
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="text-sm text-gray-600">MEL</span>
                                    <span class="text-sm font-medium text-gray-900">
                                        Rp {{ number_format($uangJalan->jumlah_mel ?? 0, 0, ',', '.') }}
                                    </span>
                                </div>
                                
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="text-sm text-gray-600">Pelancar</span>
                                    <span class="text-sm font-medium text-gray-900">
                                        Rp {{ number_format($uangJalan->jumlah_pelancar ?? 0, 0, ',', '.') }}
                                    </span>
                                </div>
                                
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="text-sm text-gray-600">Kawalan</span>
                                    <span class="text-sm font-medium text-gray-900">
                                        Rp {{ number_format($uangJalan->jumlah_kawalan ?? 0, 0, ',', '.') }}
                                    </span>
                                </div>
                                
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="text-sm text-gray-600">Parkir</span>
                                    <span class="text-sm font-medium text-gray-900">
                                        Rp {{ number_format($uangJalan->jumlah_parkir ?? 0, 0, ',', '.') }}
                                    </span>
                                </div>
                                
                                <div class="flex justify-between items-center py-2 font-semibold text-gray-900 border-t border-gray-200">
                                    <span class="text-sm">Subtotal</span>
                                    <span class="text-sm">
                                        Rp {{ number_format($uangJalan->subtotal ?? 0, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            <!-- Penyesuaian dan Total -->
                            <div class="space-y-4">
                                <h3 class="text-sm font-medium text-gray-900 border-b pb-2">Penyesuaian</h3>
                                
                                @if($uangJalan->alasan_penyesuaian)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Penyesuaian</label>
                                    <p class="text-sm text-gray-900 bg-gray-50 p-3 rounded-lg">
                                        {{ $uangJalan->alasan_penyesuaian }}
                                    </p>
                                </div>
                                @endif
                                
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="text-sm text-gray-600">Jumlah Penyesuaian</span>
                                    <span class="text-sm font-medium text-gray-900">
                                        Rp {{ number_format($uangJalan->jumlah_penyesuaian ?? 0, 0, ',', '.') }}
                                    </span>
                                </div>
                                
                                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                                    <div class="flex justify-between items-center">
                                        <span class="text-lg font-semibold text-blue-900">Total Uang Jalan</span>
                                        <span class="text-xl font-bold text-blue-900">
                                            Rp {{ number_format($uangJalan->jumlah_total ?? 0, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                                
                                @if($uangJalan->memo)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Memo</label>
                                    <p class="text-sm text-gray-900 bg-gray-50 p-3 rounded-lg">
                                        {{ $uangJalan->memo }}
                                    </p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Status Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Status</h2>
                    </div>
                    <div class="px-6 py-4">
                        @php
                            $statusConfig = [
                                'belum_dibayar' => ['bg-yellow-100', 'text-yellow-800', 'Belum Dibayar'],
                                'belum_masuk_pranota' => ['bg-orange-100', 'text-orange-800', 'Belum Masuk Pranota'],
                                'sudah_masuk_pranota' => ['bg-blue-100', 'text-blue-800', 'Sudah Masuk Pranota'],
                                'lunas' => ['bg-green-100', 'text-green-800', 'Lunas'],
                                'dibatalkan' => ['bg-red-100', 'text-red-800', 'Dibatalkan']
                            ];
                            $config = $statusConfig[$uangJalan->status] ?? ['bg-gray-100', 'text-gray-800', ucfirst($uangJalan->status)];
                        @endphp
                        <span class="inline-flex px-3 py-1 text-sm font-medium rounded-full {{ $config[0] }} {{ $config[1] }}">
                            {{ $config[2] }}
                        </span>
                    </div>
                </div>

                <!-- Informasi Pembuatan -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Informasi</h2>
                    </div>
                    <div class="px-6 py-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Uang Jalan</label>
                            <p class="text-sm text-gray-900">
                                {{ $uangJalan->tanggal_uang_jalan ? $uangJalan->tanggal_uang_jalan->format('d/m/Y') : '-' }}
                            </p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dibuat Oleh</label>
                            <p class="text-sm text-gray-900">
                                {{ $uangJalan->createdBy->name ?? '-' }}
                            </p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Dibuat</label>
                            <p class="text-sm text-gray-900">
                                {{ $uangJalan->created_at ? $uangJalan->created_at->format('d/m/Y H:i') : '-' }}
                            </p>
                        </div>
                        
                        @if($uangJalan->updated_at && $uangJalan->updated_at != $uangJalan->created_at)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Terakhir Diupdate</label>
                            <p class="text-sm text-gray-900">
                                {{ $uangJalan->updated_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                        @endif

                        @if($uangJalan->kegiatan_bongkar_muat)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kegiatan Bongkar Muat</label>
                            <p class="text-sm text-gray-900">{{ $uangJalan->kegiatan_bongkar_muat }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                @can('uang-jalan-delete')
                @if(in_array($uangJalan->status, ['belum_dibayar', 'belum_masuk_pranota']))
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Aksi</h2>
                    </div>
                    <div class="px-6 py-4">
                        <button type="button"
                                onclick="confirmDelete('{{ $uangJalan->id }}', '{{ $uangJalan->nomor_uang_jalan ?? $uangJalan->suratJalan->no_surat_jalan ?? '' }}')"
                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus Uang Jalan
                        </button>
                    </div>
                </div>
                @endif
                @endcan
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Konfirmasi Hapus</h3>
            <p class="text-sm text-gray-500 mb-1">
                Apakah Anda yakin ingin menghapus uang jalan
            </p>
            <p class="text-sm font-semibold text-gray-900 mb-4">
                <span id="deleteItemName"></span>?
            </p>
            <div class="flex items-center text-sm text-red-600 bg-red-50 rounded-lg p-3 mb-6">
                <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                Tindakan ini tidak dapat dibatalkan.
            </div>
            <div class="flex gap-3 justify-center">
                <button type="button" 
                        onclick="closeDeleteModal()"
                        class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 text-sm font-medium rounded-lg transition-colors duration-200">
                    Batal
                </button>
                <form id="deleteForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(id, identifier) {
    document.getElementById('deleteItemName').textContent = identifier;
    document.getElementById('deleteForm').action = '{{ route('uang-jalan.destroy', ':id') }}'.replace(':id', id);
    
    // Show modal
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});
</script>
@endpush