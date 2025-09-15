@extends('layouts.app')

@section('title', 'Approval Perbaikan Kontainer')
@section('page_title', 'Approval Perbaikan Kontainer')

@section('content')
<div class="max-w-6xl mx-auto bg-white shadow-lg rounded-lg p-6">
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
        <h3 class="text-lg font-semibold text-gray-800 mb-3">Informasi Permohonan Perbaikan Kontainer</h3>
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

    <!-- Perbaikan Kontainer Summary -->
    <div class="bg-orange-50 rounded-lg p-4 mb-6">
        <h4 class="text-md font-semibold text-orange-800 mb-3">Ringkasan Perbaikan Kontainer</h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white p-4 rounded border">
                <div class="text-2xl font-bold text-orange-600">{{ $totalPerbaikan }}</div>
                <div class="text-sm text-gray-600">Total Perbaikan</div>
            </div>
            <div class="bg-white p-4 rounded border">
                <div class="text-2xl font-bold text-green-600">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</div>
                <div class="text-sm text-gray-600">Total Biaya</div>
            </div>
            <div class="bg-white p-4 rounded border">
                <div class="text-2xl font-bold text-blue-600">{{ $totalSudahDibayar }}</div>
                <div class="text-sm text-gray-600">Sudah Dibayar</div>
            </div>
        </div>
    </div>

    <!-- Kontainers with Perbaikan Info -->
    @if($kontainerPerbaikan && $kontainerPerbaikan->count())
    <div class="bg-green-50 rounded-lg p-4 mb-6">
        <h4 class="text-md font-semibold text-green-800 mb-3">Detail Kontainer Perbaikan</h4>
        <div class="space-y-4">
            @foreach($kontainerPerbaikan as $kontainer)
            <div class="bg-white p-4 rounded border">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <div class="font-medium text-gray-800 text-lg">{{ $kontainer->nomor_kontainer }}</div>
                        <div class="text-sm text-gray-600">Size: {{ $kontainer->ukuran ?? 'N/A' }}</div>
                        <div class="text-sm text-gray-600">Status: {{ $kontainer->status }}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-gray-600">Jumlah Perbaikan:</div>
                        <div class="font-semibold text-orange-600">{{ $kontainer->perbaikanKontainers->count() }}</div>
                    </div>
                </div>

                @if($kontainer->perbaikanKontainers && $kontainer->perbaikanKontainers->count())
                <div class="space-y-2">
                    @foreach($kontainer->perbaikanKontainers as $perbaikan)
                    <div class="bg-gray-50 p-3 rounded border-l-4 border-orange-400">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="font-medium text-gray-800">{{ $perbaikan->nomor_memo_perbaikan ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-600">{{ $perbaikan->deskripsi_perbaikan }}</div>
                                <div class="text-sm text-gray-600">Tanggal: {{ \Carbon\Carbon::parse($perbaikan->tanggal_perbaikan)->format('d M Y') }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium text-green-600">Rp {{ number_format($perbaikan->biaya_perbaikan ?? 0, 0, ',', '.') }}</div>
                                <span class="px-2 py-1 {{ $perbaikan->status_perbaikan === 'sudah_dibayar' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }} text-xs rounded">
                                    {{ $perbaikan->status_label }}
                                </span>
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
    @endif

    <!-- Approval Form -->
    <form action="{{ route('approval.store', $permohonan) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Status Selection -->
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">Status Approval Perbaikan</h4>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status Permohonan *</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="status_permohonan" value="selesai" class="mr-2" required>
                            <span class="text-green-700 font-medium">✅ Selesai</span>
                            <span class="text-gray-600 ml-2">- Perbaikan kontainer telah selesai dan siap digunakan</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="status_permohonan" value="bermasalah" class="mr-2">
                            <span class="text-red-700 font-medium">⚠️ Bermasalah</span>
                            <span class="text-gray-600 ml-2">- Ada masalah dalam proses perbaikan kontainer</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Perbaikan Details -->
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">Detail Perbaikan</h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estimasi Perbaikan</label>
                    <textarea name="estimasi_perbaikan" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Jelaskan estimasi waktu dan jenis perbaikan yang dilakukan..."></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Total Biaya Perbaikan (Rp)</label>
                    <input type="number" name="total_biaya_perbaikan" min="0" step="1000" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="0">
                </div>
            </div>
        </div>

        <!-- Catatan -->
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">Catatan Approval</h4>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Karyawan</label>
                    <textarea name="catatan_karyawan" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Tambahkan catatan tentang approval perbaikan kontainer..."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lampiran Kembali (Opsional)</label>
                    <input type="file" name="lampiran_kembali" accept=".pdf,.jpg,.jpeg,.png" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-sm text-gray-500 mt-1">Format: PDF, JPG, JPEG, PNG. Maksimal 2MB.</p>
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
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                    ✅ Approve & Selesaikan
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
        const statusSelected = document.querySelector('input[name="status_permohonan"]:checked');
        if (!statusSelected) {
            e.preventDefault();
            alert('Silakan pilih status approval terlebih dahulu.');
            return;
        }

        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '⏳ Menyimpan...';
    });

    // Format number input
    const biayaInput = document.querySelector('input[name="total_biaya_perbaikan"]');
    if (biayaInput) {
        biayaInput.addEventListener('input', function(e) {
            // Remove non-numeric characters except decimal point
            let value = e.target.value.replace(/[^\d.]/g, '');
            e.target.value = value;
        });
    }
});
</script>
@endsection
