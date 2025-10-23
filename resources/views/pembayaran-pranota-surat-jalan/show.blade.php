@extends('layouts.app')

@section('title', 'Detail Pembayaran Pranota Surat Jalan')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-semibold text-gray-900">Detail Pembayaran</h1>
                        <p class="text-sm text-gray-600 mt-1">{{ $pembayaranPranotaSuratJalan->nomor_pembayaran }}</p>
                    </div>
                    <div class="flex space-x-3">
                        @if(!$pembayaranPranotaSuratJalan->isPaid() && !$pembayaranPranotaSuratJalan->isCancelled())
                            @can('pembayaran-pranota-surat-jalan-edit')
                            <a href="{{ route('pembayaran-pranota-surat-jalan.edit', $pembayaranPranotaSuratJalan->id) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                                Edit
                            </a>
                            @endcan
                        @endif
                        <a href="{{ route('pembayaran-pranota-surat-jalan.index') }}" class="text-gray-600 hover:text-gray-800">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Status Badge -->
                    <div class="md:col-span-2">
                        @php
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                'paid' => 'bg-green-100 text-green-800 border-green-200',
                                'partial' => 'bg-blue-100 text-blue-800 border-blue-200',
                                'cancelled' => 'bg-red-100 text-red-800 border-red-200',
                            ];
                        @endphp
                        <div class="inline-flex px-3 py-1 text-sm font-semibold rounded-full border {{ $statusColors[$pembayaranPranotaSuratJalan->status_pembayaran] ?? 'bg-gray-100 text-gray-800 border-gray-200' }}">
                            {{ $pembayaranPranotaSuratJalan->status_label }}
                        </div>
                    </div>

                    <!-- Basic Information -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Pembayaran</label>
                            <p class="text-sm text-gray-900 font-mono bg-gray-50 px-3 py-2 rounded">{{ $pembayaranPranotaSuratJalan->nomor_pembayaran }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pembayaran</label>
                            <p class="text-sm text-gray-900">{{ $pembayaranPranotaSuratJalan->tanggal_pembayaran->format('d/m/Y H:i') }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran</label>
                            <p class="text-sm text-gray-900">{{ $pembayaranPranotaSuratJalan->method_label }}</p>
                        </div>

                        @if($pembayaranPranotaSuratJalan->nomor_referensi)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Referensi</label>
                            <p class="text-sm text-gray-900 font-mono bg-gray-50 px-3 py-2 rounded">{{ $pembayaranPranotaSuratJalan->nomor_referensi }}</p>
                        </div>
                        @endif
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Pembayaran</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $pembayaranPranotaSuratJalan->formatted_amount }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pranota Surat Jalan</label>
                            @if($pembayaranPranotaSuratJalan->pranotaSuratJalan)
                                <p class="text-sm text-gray-900">{{ $pembayaranPranotaSuratJalan->pranotaSuratJalan->nomor_pranota }}</p>
                                <p class="text-xs text-gray-500">Total: {{ $pembayaranPranotaSuratJalan->pranotaSuratJalan->formatted_total ?? 'N/A' }}</p>
                            @else
                                <p class="text-sm text-red-500">Pranota tidak ditemukan</p>
                            @endif
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status Pembayaran</label>
                            <p class="text-sm text-gray-900">{{ $pembayaranPranotaSuratJalan->status_label }}</p>
                        </div>
                    </div>

                    <!-- Keterangan -->
                    @if($pembayaranPranotaSuratJalan->keterangan)
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                        <div class="bg-gray-50 px-3 py-2 rounded border">
                            <p class="text-sm text-gray-900">{{ $pembayaranPranotaSuratJalan->keterangan }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Bukti Pembayaran -->
                    @if($pembayaranPranotaSuratJalan->bukti_pembayaran)
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bukti Pembayaran</label>
                        <div class="border rounded-lg p-4">
                            @php
                                $fileExtension = pathinfo($pembayaranPranotaSuratJalan->bukti_pembayaran, PATHINFO_EXTENSION);
                                $isPdf = strtolower($fileExtension) === 'pdf';
                            @endphp

                            @if($isPdf)
                                <div class="flex items-center space-x-3">
                                    <svg class="w-8 h-8 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 0v12h8V4H6z" clip-rule="evenodd"/>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Dokumen PDF</p>
                                        <a href="{{ Storage::url($pembayaranPranotaSuratJalan->bukti_pembayaran) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">Lihat Dokumen</a>
                                    </div>
                                </div>
                            @else
                                <img src="{{ Storage::url($pembayaranPranotaSuratJalan->bukti_pembayaran) }}" alt="Bukti Pembayaran" class="max-w-full h-auto rounded cursor-pointer" onclick="openImageModal(this.src)">
                            @endif

                            <div class="mt-2">
                                <a href="{{ Storage::url($pembayaranPranotaSuratJalan->bukti_pembayaran) }}" download class="text-blue-600 hover:text-blue-800 text-sm inline-flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Download
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Audit Information -->
                    <div class="md:col-span-2 border-t pt-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs text-gray-500">
                            <div>
                                <p><strong>Dibuat oleh:</strong> {{ $pembayaranPranotaSuratJalan->creator->name ?? 'N/A' }}</p>
                                <p><strong>Tanggal dibuat:</strong> {{ $pembayaranPranotaSuratJalan->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div>
                                <p><strong>Terakhir diubah:</strong> {{ $pembayaranPranotaSuratJalan->updater->name ?? 'N/A' }}</p>
                                <p><strong>Tanggal diubah:</strong> {{ $pembayaranPranotaSuratJalan->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50 flex items-center justify-center" onclick="closeImageModal()">
    <div class="max-w-4xl max-h-4xl p-4">
        <img id="modalImage" src="" alt="Bukti Pembayaran" class="max-w-full max-h-full rounded">
    </div>
    <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white hover:text-gray-300">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
</div>

@if(session('success'))
<div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
    {{ session('error') }}
</div>
@endif

<script>
function openImageModal(src) {
    document.getElementById('modalImage').src = src;
    document.getElementById('imageModal').classList.remove('hidden');
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
}

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
    }
});

// Auto-hide notifications
setTimeout(function() {
    const notifications = document.querySelectorAll('[class*="fixed bottom-4 right-4"]');
    notifications.forEach(notification => {
        notification.style.display = 'none';
    });
}, 5000);
</script>
@endsection
