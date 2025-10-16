@extends('layouts.app')

@section('title', 'Detail Surat Jalan - Approval')
@section('page_title', 'Detail Surat Jalan - Approval')

@section('content')
<div class="container mx-auto px-4 py-4">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-4 border-b border-gray-200">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">Detail Surat Jalan</h1>
                    <p class="text-xs text-gray-600 mt-1">{{ $suratJalan->no_surat_jalan }} - Level {{ ucfirst(str_replace('-', ' ', $approvalLevel)) }}</p>
                </div>
                <div>
                    <a href="{{ route('approval.surat-jalan.index') }}"
                       class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Detail Surat Jalan -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="border-b border-gray-200 p-4">
                        <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Informasi Surat Jalan
                        </h2>
                    </div>
                    <div class="p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">No. Surat Jalan</label>
                                <p class="text-sm text-gray-900">{{ $suratJalan->no_surat_jalan }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal</label>
                                <p class="text-sm text-gray-900">{{ $suratJalan->tanggal_surat_jalan ? $suratJalan->tanggal_surat_jalan->format('d/m/Y') : '-' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Supir</label>
                                <p class="text-sm text-gray-900 flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    {{ $suratJalan->supir }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Kegiatan</label>
                                @php
                                    $kegiatanName = \App\Models\MasterKegiatan::where('kode_kegiatan', $suratJalan->kegiatan)
                                                    ->value('nama_kegiatan') ?? $suratJalan->kegiatan;
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    {{ $kegiatanName }}
                                </span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Ukuran Kontainer</label>
                                <p class="text-sm text-gray-900">{{ $suratJalan->size }} ft</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Jumlah Kontainer</label>
                                <p class="text-sm text-gray-900">{{ $suratJalan->jumlah_kontainer }} unit</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Nomor Kontainer</label>
                                <code class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $suratJalan->no_kontainer ?: 'Belum diisi' }}</code>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">No. Seal</label>
                                <code class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $suratJalan->no_seal ?: 'Belum diisi' }}</code>
                            </div>
                            @if($suratJalan->tujuan_pengiriman)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Tujuan Pengiriman</label>
                                    <p class="text-sm text-gray-900">{{ $suratJalan->tujuan_pengiriman }}</p>
                                </div>
                            @endif
                            @if($suratJalan->pengirim)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Pengirim</label>
                                    <p class="text-sm text-gray-900">{{ $suratJalan->pengirim }}</p>
                                </div>
                            @endif
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-500 mb-1">Status Saat Ini</label>
                                <p class="text-sm">
                                    @switch($suratJalan->status)
                                        @case('sudah_checkpoint')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Sudah Checkpoint</span>
                                            @break
                                        @case('fully_approved')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Fully Approved</span>
                                            @break
                                        @case('rejected')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Rejected</span>
                                            @break
                                        @default
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ $suratJalan->status }}</span>
                                    @endswitch
                                </p>
                            </div>
                        </div>
                        <!-- Gambar Checkpoint -->
                        @if($suratJalan->gambar_checkpoint)
                            <div class="border-t border-gray-200 pt-4 mt-4">
                                <label class="block text-sm font-medium text-gray-500 mb-2">Gambar Checkpoint</label>
                                <div>
                                    <a href="{{ asset('storage/' . $suratJalan->gambar_checkpoint) }}" target="_blank"
                                       class="inline-flex items-center px-3 py-2 border border-indigo-300 rounded-md text-sm font-medium text-indigo-700 bg-white hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Lihat Gambar
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Panel Approval -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="border-b border-gray-200 p-4">
                        <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Status Approval
                        </h2>
                    </div>
                    <div class="p-4">
                        <!-- Status Semua Level Approval -->
                        <div class="space-y-3 mb-6">
                            @foreach($suratJalan->approvals as $approvalItem)
                                <div class="flex justify-between items-center p-3 rounded-lg {{ $approvalItem->status === 'approved' ? 'bg-green-50 border border-green-200' : ($approvalItem->status === 'rejected' ? 'bg-red-50 border border-red-200' : 'bg-yellow-50 border border-yellow-200') }}">
                                    <span class="font-medium text-sm text-gray-800">{{ ucfirst(str_replace('-', ' ', $approvalItem->approval_level)) }}</span>
                                    @switch($approvalItem->status)
                                        @case('approved')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Approved</span>
                                            @break
                                        @case('rejected')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Rejected</span>
                                            @break
                                        @default
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                    @endswitch
                                </div>
                                @if($approvalItem->approved_at && $approvalItem->approver)
                                    <div class="text-xs text-gray-500 ml-3 mb-3 space-y-1">
                                        <div class="flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            {{ $approvalItem->approver->name }}
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $approvalItem->approved_at->format('d/m/Y H:i') }}
                                        </div>
                                        @if($approvalItem->approval_notes)
                                            <div class="flex items-start">
                                                <svg class="w-3 h-3 mr-1 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                                </svg>
                                                <span class="break-words">{{ $approvalItem->approval_notes }}</span>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <!-- Form Approval jika masih pending -->
                        @if($approval->status === 'pending')
                            <div class="border-t border-gray-200 pt-4">
                                <h3 class="text-base font-semibold text-gray-800 mb-4">Aksi Approval</h3>

                                <!-- Form Approve -->
                                <form action="{{ route('approval.surat-jalan.approve', $suratJalan) }}" method="POST" class="mb-4">
                                    @csrf
                                    <div class="mb-4">
                                        <label for="approval_notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                                        <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                                  id="approval_notes" name="approval_notes" rows="3"
                                                  placeholder="Tambahkan catatan untuk approval..."></textarea>
                                    </div>
                                    <button type="submit" class="w-full mb-3 inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Approve
                                    </button>
                                </form>

                                <!-- Button Reject -->
                                <button type="button" onclick="openRejectModal()" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Reject
                                </button>
                            </div>
                        @else
                            <div class="text-center py-6 border-t border-gray-200">
                                <p class="text-sm text-gray-500">Approval sudah diproses</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Reject -->
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <form action="{{ route('approval.surat-jalan.reject', $suratJalan) }}" method="POST">
            @csrf
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Reject Surat Jalan</h3>
                    <button type="button" onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="mb-4">
                    <label for="reject_reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Alasan Reject <span class="text-red-500">*</span>
                    </label>
                    <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                              id="reject_reason" name="approval_notes" rows="4"
                              placeholder="Jelaskan alasan mengapa surat jalan ini di-reject..." required></textarea>
                </div>

                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                <strong>Peringatan:</strong> Tindakan ini akan membuat surat jalan di-reject dan tidak bisa diproses lebih lanjut.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeRejectModal()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Reject
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@section('scripts')
<script>
function openRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    document.getElementById('reject_reason').value = '';
}

// Close modal when clicking outside
document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRejectModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeRejectModal();
    }
});
</script>
@endsection
@endsection
