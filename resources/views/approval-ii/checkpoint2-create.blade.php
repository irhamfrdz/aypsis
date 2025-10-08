@extends('layouts.app')

@section('title', 'Proses & Selesaikan Permohonan')
@section('page_title', 'Proses & Selesaikan Permohonan')

@section('content')
<div class="max-w-4xl mx-auto bg-white shadow-lg rounded-lg p-6">
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

    <!-- Kontainers Info -->
    @if($permohonan->kontainers && $permohonan->kontainers->count())
    <div class="bg-green-50 rounded-lg p-4 mb-6">
        <h4 class="text-md font-semibold text-green-800 mb-3">Informasi Kontainer</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($permohonan->kontainers as $kontainer)
            <div class="bg-white p-3 rounded border">
                <div class="font-medium text-gray-800">{{ $kontainer->nomor_kontainer }}</div>
                <div class="text-sm text-gray-600">Size: {{ $kontainer->ukuran ?? 'N/A' }}</div>
                <div class="text-sm text-gray-600">Status: {{ $kontainer->status }}</div>
                @if($kontainer->perbaikanKontainers && $kontainer->perbaikanKontainers->count())
                    @foreach($kontainer->perbaikanKontainers as $perbaikan)
                    <div class="mt-2 p-2 bg-orange-50 rounded border-l-4 border-orange-400">
                        <div class="text-xs font-medium text-orange-800">Perbaikan Kontainer</div>
                        <div class="text-xs text-orange-700">Tagihan: {{ $perbaikan->nomor_tagihan ?? 'N/A' }}</div>
                        <div class="text-xs text-orange-600">Status: {{ $perbaikan->status_label }}</div>
                        <div class="text-xs text-orange-600">Biaya: Rp {{ number_format($perbaikan->biaya_perbaikan ?? 0, 0, ',', '.') }}</div>
                    </div>
                    @endforeach
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Approval Form -->
    <form action="{{ route('approval-ii.store', $permohonan) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Status Selection -->
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">Status Penyelesaian</h4>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status Permohonan *</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="status_permohonan" value="selesai" class="mr-2" required>
                            <span class="text-green-700 font-medium">✅ Selesai</span>
                            <span class="text-gray-600 ml-2">- Tugas telah berhasil diselesaikan</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="status_permohonan" value="bermasalah" class="mr-2">
                            <span class="text-red-700 font-medium">⚠️ Bermasalah</span>
                            <span class="text-gray-600 ml-2">- Ada masalah dalam penyelesaian tugas</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sewa Information (Conditional) -->
        <div id="sewa-section" class="bg-white border border-gray-200 rounded-lg p-6" style="display: none;">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">Informasi Sewa Kontainer</h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Masuk Sewa</label>
                    <input type="date" name="tanggal_masuk_sewa" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai Sewa</label>
                    <input type="date" name="tanggal_selesai_sewa" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
        </div>

        <!-- Catatan -->
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">Catatan Penyelesaian</h4>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Karyawan</label>
                    <textarea name="catatan_karyawan" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Tambahkan catatan tentang penyelesaian tugas..."></textarea>
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
            <a href="{{ route('approval-ii.dashboard') }}" class="px-6 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition">
                ← Kembali ke Dashboard
            </a>

            <div class="flex space-x-3">
                <button type="button" onclick="window.history.back()" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                    ✅ Simpan & Selesaikan
                </button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show/hide sewa section based on kegiatan
    const statusRadios = document.querySelectorAll('input[name="status_permohonan"]');
    const sewaSection = document.getElementById('sewa-section');

    function toggleSewaSection() {
        const selectedStatus = document.querySelector('input[name="status_permohonan"]:checked');
        if (selectedStatus && selectedStatus.value === 'selesai') {
            // Check if kegiatan is 'pengiriman'
            const kegiatan = '{{ $permohonan->kegiatan }}';
            if (kegiatan === 'pengiriman') {
                sewaSection.style.display = 'block';
            } else {
                sewaSection.style.display = 'none';
            }
        } else {
            sewaSection.style.display = 'none';
        }
    }

    statusRadios.forEach(radio => {
        radio.addEventListener('change', toggleSewaSection);
    });

    // Initial check
    toggleSewaSection();

    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        console.log('Form submit triggered');

        const statusSelected = document.querySelector('input[name="status_permohonan"]:checked');
        if (!statusSelected) {
            e.preventDefault();
            alert('Silakan pilih status penyelesaian terlebih dahulu.');
            return;
        }

        console.log('Status selected:', statusSelected.value);
        console.log('Form action:', form.action);

        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '⏳ Menyimpan...';

        // Let form submit continue
        console.log('Form submitting...');
    });
});
</script>
@endsection
