@extends('layouts.app')

@section('title', 'Approval Perbaikan Kontainer')
@section('page_title', 'Approval Perbaikan Kontainer')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-r-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-r-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Header Section --}}
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Approval Perbaikan Kontainer</h1>
                <p class="mt-1 text-sm text-gray-600">Kelola dan approve proses perbaikan kontainer</p>
            </div>
            <div class="flex items-center space-x-3">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Approval
                </span>
            </div>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left Sidebar - Information Panel --}}
        <div class="lg:col-span-1 space-y-6">

            {{-- Basic Information Card --}}
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-4 py-3">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 000 16z" />
                        </svg>
                        Informasi Permohonan
                    </h3>
                </div>
                <div class="p-4 space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm font-medium text-gray-600">Nomor Memo</span>
                        <span class="text-sm font-mono font-semibold text-gray-900">{{ $permohonan->nomor_memo }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm font-medium text-gray-600">Supir</span>
                        <span class="text-sm text-gray-900">{{ $permohonan->supir->nama_panggilan ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm font-medium text-gray-600">Kegiatan</span>
                        <span class="text-sm text-gray-900">{{ \App\Models\MasterKegiatan::where('kode_kegiatan', $permohonan->kegiatan)->value('nama_kegiatan') ?? $permohonan->kegiatan }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm font-medium text-gray-600">Vendor</span>
                        <span class="text-sm text-gray-900">{{ $permohonan->vendor_perusahaan ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            {{-- Checkpoint History --}}
            @if($permohonan->checkpoints && $permohonan->checkpoints->count())
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-green-500 to-green-600 px-4 py-3">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                        </svg>
                        Riwayat Checkpoint
                    </h3>
                </div>
                <div class="p-4">
                    <div class="space-y-3 max-h-64 overflow-y-auto">
                        @foreach($permohonan->checkpoints->sortBy('tanggal_checkpoint') as $checkpoint)
                        <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg border border-gray-100">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">{{ $checkpoint->keterangan ?? 'Checkpoint' }}</p>
                                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($checkpoint->tanggal_checkpoint)->format('d M Y H:i') }}</p>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($checkpoint->status == 'completed') bg-green-100 text-green-800
                                    @elseif($checkpoint->status == 'pending') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $checkpoint->status }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

        </div>

        {{-- Right Content - Container Details --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Container Repair Details --}}
            @if($kontainerPerbaikan && $kontainerPerbaikan->count())
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-green-500 to-green-600 px-4 py-3">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" />
                        </svg>
                        Detail Kontainer Perbaikan
                    </h3>
                </div>
                <div class="p-4">
                    <div class="space-y-4 max-h-96 overflow-y-auto">
                        @foreach($kontainerPerbaikan as $kontainer)
                        <div class="bg-gradient-to-r from-green-50 to-white border border-green-200 rounded-lg p-4 shadow-sm">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-bold text-gray-900">{{ $kontainer->nomor_kontainer }}</h4>
                                        <p class="text-sm text-gray-600">Size: {{ $kontainer->ukuran ?? 'N/A' }} | Status: {{ $kontainer->status }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm text-gray-600">Jumlah Perbaikan</div>
                                    <div class="text-2xl font-bold text-orange-600">{{ $kontainer->perbaikanKontainers->count() }}</div>
                                </div>
                            </div>

                            @if($kontainer->perbaikanKontainers && $kontainer->perbaikanKontainers->count())
                            <div class="space-y-3 mt-4">
                                @foreach($kontainer->perbaikanKontainers as $perbaikan)
                                <div class="bg-white border border-orange-200 rounded-lg p-3 shadow-sm">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2 mb-1">
                                                <span class="text-sm font-semibold text-gray-900">{{ $perbaikan->nomor_tagihan ?? 'N/A' }}</span>
                                                <span class="px-2 py-1 text-xs rounded-full font-medium
                                                    @if($perbaikan->status_perbaikan === 'sudah_dibayar') bg-green-100 text-green-800
                                                    @else bg-yellow-100 text-yellow-800 @endif">
                                                    {{ $perbaikan->status_label }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-700">{{ $perbaikan->deskripsi_perbaikan }}</p>
                                        </div>
                                        <div class="text-right ml-4">
                                            <div class="text-lg font-bold text-green-600">
                                                Rp {{ number_format($perbaikan->biaya_perbaikan ?? 0, 0, ',', '.') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- Approval Form --}}
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-4 py-3">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 000 16zm-8-6v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Form Approval
                    </h3>
                </div>
                <form action="{{ route('approval.store', $permohonan) }}" method="POST" enctype="multipart/form-data" id="approvalForm" class="p-6 space-y-6">
                    @csrf

                    {{-- Status Approval --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Status Approval <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <label class="relative flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-300 hover:bg-green-50 transition-all">
                                <input type="radio" name="status_permohonan" value="selesai" class="sr-only" required>
                                <div class="flex items-center">
                                    <div class="w-4 h-4 border-2 border-gray-300 rounded-full mr-3 flex items-center justify-center">
                                        <div class="w-2 h-2 bg-green-500 rounded-full opacity-0 transition-opacity"></div>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">Selesai</div>
                                        <div class="text-sm text-gray-600">Perbaikan telah selesai</div>
                                    </div>
                                </div>
                                <div class="absolute top-2 right-2">
                                    <svg class="w-5 h-5 text-green-500 opacity-0 transition-opacity" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </label>

                            <label class="relative flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-red-300 hover:bg-red-50 transition-all">
                                <input type="radio" name="status_permohonan" value="bermasalah" class="sr-only">
                                <div class="flex items-center">
                                    <div class="w-4 h-4 border-2 border-gray-300 rounded-full mr-3 flex items-center justify-center">
                                        <div class="w-2 h-2 bg-red-500 rounded-full opacity-0 transition-opacity"></div>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">Bermasalah</div>
                                        <div class="text-sm text-gray-600">Ada masalah dalam perbaikan</div>
                                    </div>
                                </div>
                                <div class="absolute top-2 right-2">
                                    <svg class="w-5 h-5 text-red-500 opacity-0 transition-opacity" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </label>
                        </div>
                        <div id="statusError" class="mt-2 text-sm text-red-600 hidden">
                            <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            Harap pilih status approval.
                        </div>
                    </div>

                    {{-- Additional Notes --}}
                    <div>
                        <label for="catatan_karyawan" class="block text-sm font-medium text-gray-700 mb-2">
                            Catatan Karyawan
                        </label>
                        <textarea id="catatan_karyawan" name="catatan_karyawan" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 resize-none"
                            placeholder="Tambahkan catatan tambahan jika diperlukan..."></textarea>
                    </div>

                    {{-- File Upload --}}
                    <div>
                        <label for="lampiran_kembali" class="block text-sm font-medium text-gray-700 mb-2">
                            Lampiran Kembali <span class="text-gray-500">(Opsional)</span>
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-purple-400 transition-colors">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="lampiran_kembali" class="relative cursor-pointer bg-white rounded-md font-medium text-purple-600 hover:text-purple-500 focus-within:outline-none">
                                        <span>Upload file</span>
                                        <input id="lampiran_kembali" name="lampiran_kembali" type="file" accept=".pdf,.jpg,.jpeg,.png" class="sr-only">
                                    </label>
                                    <p class="pl-1">atau drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PDF, JPG, JPEG, PNG hingga 2MB</p>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <a href="{{ route('approval.dashboard') }}"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                            </svg>
                            Kembali
                        </a>
                        <button type="submit"
                            class="inline-flex items-center px-6 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Approve & Selesaikan
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('approvalForm');
    const statusError = document.getElementById('statusError');
    const submitBtn = form.querySelector('button[type="submit"]');
    const radioLabels = form.querySelectorAll('input[name="status_permohonan"]');

    // Handle radio button selection
    radioLabels.forEach(radio => {
        radio.addEventListener('change', function() {
            // Remove selected state from all labels
            radioLabels.forEach(r => {
                const label = r.closest('label');
                const dot = label.querySelector('.w-2');
                const icon = label.querySelector('svg');
                dot.classList.add('opacity-0');
                icon.classList.add('opacity-0');
                label.classList.remove('border-purple-500', 'bg-purple-50');
                label.classList.add('border-gray-200');
            });

            // Add selected state to current label
            if (this.checked) {
                const label = this.closest('label');
                const dot = label.querySelector('.w-2');
                const icon = label.querySelector('svg');
                dot.classList.remove('opacity-0');
                icon.classList.remove('opacity-0');
                label.classList.remove('border-gray-200');
                label.classList.add('border-purple-500', 'bg-purple-50');
                statusError.classList.add('hidden');
            }
        });
    });

    // Form validation
    form.addEventListener('submit', function(e) {
        const statusSelected = form.querySelector('input[name="status_permohonan"]:checked');
        if (!statusSelected) {
            e.preventDefault();
            statusError.classList.remove('hidden');
            statusError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Menyimpan...';
    });
});
</script>
@endsection
