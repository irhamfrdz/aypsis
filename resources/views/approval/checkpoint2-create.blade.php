@extends('layouts.app')

@section('title', 'Approval Tugas I')
@section('page_title', 'Approval Tugas I - Persetujuan Sederhana')

@section('content')
<div class="max-w-2xl mx-auto bg-white shadow-lg rounded-lg p-6">
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md mb-6" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-6" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <!-- Header Info -->
    <div class="bg-gray-50 rounded-lg p-4 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-3">Informasi Permohonan</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <span class="font-medium text-gray-600">Nomor Memo:</span>
                <span class="text-gray-800">{{ $permohonan->nomor_memo }}</span>
            </div>
            <div>
                <span class="font-medium text-gray-600">Supir:</span>
                <span class="text-gray-800">{{ $permohonan->supir->nama_panggilan ?? 'N/A' }}</span>
            </div>
            <div>
                <span class="font-medium text-gray-600">Kegiatan:</span>
                <span class="text-gray-800">{{ \App\Models\MasterKegiatan::where('kode_kegiatan', $permohonan->kegiatan)->value('nama_kegiatan') ?? $permohonan->kegiatan }}</span>
            </div>
            <div>
                <span class="font-medium text-gray-600">Vendor:</span>
                <span class="text-gray-800">{{ $permohonan->vendor_perusahaan ?? 'N/A' }}</span>
            </div>
            @if($permohonan->kontainers && $permohonan->kontainers->count())
            <div class="md:col-span-2">
                <span class="font-medium text-gray-600">Nomor Kontainer:</span>
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

    <!-- Checkpoints Info -->
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

    <!-- Approval Form -->
    <form action="{{ route('approval.store', $permohonan) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Status Selection -->
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">Persetujuan Approval Tugas I</h4>
            <p class="text-sm text-gray-600 mb-4">Approval ini akan mengubah status memo menjadi "Disetujui Sistem 1" dan menandai sebagai approved oleh system 1, sebagai persetujuan sederhana sebelum proses operasional.</p>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status Persetujuan *</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="status_permohonan" value="selesai" class="mr-2" required checked>
                            <span class="text-green-700 font-medium">✅ Setujui</span>
                            <span class="text-gray-600 ml-2">- Setujui permohonan untuk approval sistem 1</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Catatan -->
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">Catatan Persetujuan (Opsional)</h4>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                    <textarea name="catatan_karyawan" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-between items-center pt-6 border-t border-gray-200">
            <a href="{{ route('approval.dashboard') }}" class="px-6 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition">
                ← Kembali ke Dashboard
            </a>

            <div class="flex space-x-3">
                <button type="button" onclick="window.history.back()" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                    ✅ Setujui
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
