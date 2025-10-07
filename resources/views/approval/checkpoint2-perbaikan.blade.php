@extends('layouts.app')

@section('title', 'Approval Tugas II - Perbaikan Kontainer')
@section('page_title', 'Approval Tugas II - Perbaikan Kontainer')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-4 space-y-4">

    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-400 p-3 rounded-r-lg">
            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-400 p-3 rounded-r-lg">
            <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
        </div>
    @endif

    {{-- Info Panel --}}
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">
                    <strong>Perhatian:</strong> Approval Tugas II sekarang hanya melakukan persetujuan sederhana dan tidak lagi menangani perbaikan kontainer. Perbaikan kontainer akan ditangani secara terpisah.
                </p>
            </div>
        </div>
    </div>

    {{-- Header --}}
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Approval Tugas II - Persetujuan Sederhana</h1>
                <p class="text-sm text-gray-600">{{ $permohonan->nomor_memo }}</p>
            </div>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                Approval Sederhana
            </span>
        </div>
    </div>

    {{-- Info Permohonan --}}
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-4">
        <h3 class="text-sm font-semibold text-gray-800 mb-3">Informasi Permohonan</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
            <div>
                <span class="text-gray-600">Supir:</span>
                <div class="font-medium">{{ $permohonan->supir->nama_panggilan ?? 'N/A' }}</div>
            </div>
            <div>
                <span class="text-gray-600">Kegiatan:</span>
                <div class="font-medium">{{ \App\Models\MasterKegiatan::where('kode_kegiatan', $permohonan->kegiatan)->value('nama_kegiatan') ?? $permohonan->kegiatan }}</div>
            </div>
            <div>
                <span class="text-gray-600">Vendor:</span>
                <div class="font-medium">{{ $permohonan->vendor_perusahaan ?? 'N/A' }}</div>
            </div>
            <div>
                <span class="text-gray-600">Kontainer:</span>
                <div class="font-medium">{{ $kontainerPerbaikan ? $kontainerPerbaikan->count() : 0 }} unit</div>
            </div>
            @if($permohonan->kontainers && $permohonan->kontainers->count())
            <div class="col-span-4 mt-2">
                <span class="text-gray-600">Nomor Kontainer:</span>
                <div class="mt-1">
                    @foreach($permohonan->kontainers as $kontainer)
                        <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-1 mb-1">
                            {{ $kontainer->nomor_kontainer }}
                        </span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Checkpoints Info --}}
    @if($permohonan->checkpoints && $permohonan->checkpoints->count())
    <div class="bg-blue-50 rounded-lg p-4 mb-6">
        <h4 class="text-md font-semibold text-blue-800 mb-3">Riwayat Checkpoint</h4>
        <div class="space-y-2">
            @foreach($permohonan->checkpoints->sortBy('tanggal_checkpoint') as $checkpoint)
            <div class="flex items-center justify-between bg-white p-3 rounded border">
                <div>
                    <span class="font-medium">{{ \Carbon\Carbon::parse($checkpoint->tanggal_checkpoint)->format('d M Y H:i') }}</span>
                    <span class="text-gray-600 ml-2">{{ $checkpoint->keterangan ?? 'Checkpoint' }}</span>
                </div>
                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded">{{ $checkpoint->status }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Approval Form --}}
    <form action="{{ route('approval-ii.store', $permohonan) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Status Selection --}}
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">Persetujuan Approval Tugas II</h4>
            <p class="text-sm text-gray-600 mb-4">Approval ini hanya akan mengubah status memo menjadi "Selesai" dan menandai sebagai approved oleh system 2, sehingga memo dapat diproses untuk pembuatan pranota.</p>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status Persetujuan *</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="status_permohonan" value="selesai" class="mr-2" required checked>
                            <span class="text-green-700 font-medium">✅ Setujui & Selesaikan</span>
                            <span class="text-gray-600 ml-2">- Setujui permohonan dan ubah status menjadi "Selesai"</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- Catatan --}}
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">Catatan Persetujuan (Opsional)</h4>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                    <textarea name="catatan_karyawan" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                </div>
            </div>
        </div>

        {{-- Submit Buttons --}}
        <div class="flex justify-between items-center pt-6 border-t border-gray-200">
            <a href="{{ route('approval-ii.dashboard') }}" class="px-6 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition">
                ← Kembali ke Dashboard
            </a>

            <div class="flex space-x-3">
                <button type="button" onclick="window.history.back()" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                    ✅ Setujui & Selesaikan
                </button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '⏳ Memproses...';
    });
});
</script>
@endsection
