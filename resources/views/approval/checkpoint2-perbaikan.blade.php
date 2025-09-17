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
                                <div class="font-medium text-gray-800">{{ $perbaikan->nomor_tagihan ?? 'N/A' }}</div>
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
    <div class="bg-white border border-gray-200 rounded-lg p-8 mt-6 shadow-md">
        <h3 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Form Approval Perbaikan Kontainer</h3>
        <form action="{{ route('approval.store', $permohonan) }}" method="POST" enctype="multipart/form-data" id="approvalForm">
            @csrf
            <div class="space-y-6">
                <!-- Status Selection -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <label class="block text-base font-medium text-gray-700 mb-3">
                        Status Approval <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-6">
                        <label class="flex items-center cursor-pointer p-3 rounded-lg border-2 border-transparent hover:border-green-300 transition-colors">
                            <input type="radio" name="status_permohonan" value="selesai" class="mr-3 text-green-600 focus:ring-green-500" required>
                            <span class="text-green-700 font-medium text-base">✅ Selesai</span>
                        </label>
                        <label class="flex items-center cursor-pointer p-3 rounded-lg border-2 border-transparent hover:border-red-300 transition-colors">
                            <input type="radio" name="status_permohonan" value="bermasalah" class="mr-3 text-red-600 focus:ring-red-500">
                            <span class="text-red-700 font-medium text-base">⚠️ Bermasalah</span>
                        </label>
                    </div>
                    <div id="statusError" class="text-red-500 text-sm mt-2 hidden">Harap pilih status approval.</div>
                </div>

                <!-- Estimasi Perbaikan -->
                <div>
                    <label class="block text-base font-medium text-gray-700 mb-2">Estimasi Perbaikan</label>
                    <textarea name="estimasi_perbaikan" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base transition-all duration-200" placeholder="Jelaskan estimasi waktu dan jenis perbaikan yang dilakukan..."></textarea>
                </div>

                <!-- Estimasi Total Biaya -->
                <div>
                    <label class="block text-base font-medium text-gray-700 mb-2">Estimasi Total Biaya Perbaikan (Rp)</label>
                    <input type="text" name="total_biaya_perbaikan" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base transition-all duration-200" placeholder="Masukkan estimasi total biaya dalam Rupiah">
                </div>

                <!-- Vendor/Bengkel -->
                <div>
                    <label class="block text-base font-medium text-gray-700 mb-2">Vendor/Bengkel <span class="text-red-500">*</span></label>
                    <input type="text" name="vendor_bengkel" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base transition-all duration-200" placeholder="Masukkan nama vendor atau bengkel yang akan melakukan perbaikan" required>
                    <p class="text-sm text-gray-500 mt-1">Contoh: PT. Container Repair Indonesia, Bengkel ABC, dll.</p>
                </div>

                <!-- Catatan -->
                <div>
                    <label class="block text-base font-medium text-gray-700 mb-2">Catatan Karyawan</label>
                    <textarea name="catatan_karyawan" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base transition-all duration-200" placeholder="Tambahkan catatan tambahan tentang approval perbaikan kontainer..."></textarea>
                </div>

                <!-- Lampiran -->
                <div>
                    <label class="block text-base font-medium text-gray-700 mb-2">Lampiran Kembali (Opsional)</label>
                    <input type="file" name="lampiran_kembali" accept=".pdf,.jpg,.jpeg,.png" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base transition-all duration-200 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="text-sm text-gray-500 mt-2">Format yang didukung: PDF, JPG, JPEG, PNG. Maksimal 2MB.</p>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                    <a href="{{ route('approval.dashboard') }}" class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors text-base font-medium shadow-sm">
                        ← Kembali ke Dashboard
                    </a>
                    <div class="flex gap-4">
                        <button type="button" onclick="window.history.back()" class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors text-base font-medium shadow-sm">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-base font-medium shadow-sm">
                            ✅ Approve & Selesaikan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('approvalForm');
    const statusError = document.getElementById('statusError');
    const submitBtn = form.querySelector('button[type="submit"]');

    // Real-time validation for status
    const statusRadios = form.querySelectorAll('input[name="status_permohonan"]');
    statusRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.checked) {
                statusError.classList.add('hidden');
            }
        });
    });

    // Form submission validation
    form.addEventListener('submit', function(e) {
        const statusSelected = form.querySelector('input[name="status_permohonan"]:checked');
        if (!statusSelected) {
            e.preventDefault();
            statusError.classList.remove('hidden');
            statusError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        // Clean estimasi biaya input before submit (remove thousand separators)
        const estimasiBiayaInput = form.querySelector('input[name="total_biaya_perbaikan"]');
        if (estimasiBiayaInput) {
            estimasiBiayaInput.value = estimasiBiayaInput.value.replace(/\./g, '');
        }

        // Validate vendor/bengkel field
        const vendorBengkelInput = form.querySelector('input[name="vendor_bengkel"]');
        if (vendorBengkelInput && !vendorBengkelInput.value.trim()) {
            e.preventDefault();
            alert('Vendor/Bengkel harus diisi!');
            vendorBengkelInput.focus();
            return;
        }

        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '⏳ Menyimpan...';
    });

    // Format estimasi biaya input with thousand separator
    const estimasiBiayaInput = form.querySelector('input[name="total_biaya_perbaikan"]');
    if (estimasiBiayaInput) {
        // Function to format estimasi biaya number
        function formatNumber(input) {
            // Get current cursor position
            const cursorPosition = input.selectionStart;
            let value = input.value.replace(/[^\d]/g, '');
            if (value) {
                value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }
            input.value = value;

            // Restore cursor position
            const newCursorPosition = cursorPosition + (value.length - input.value.replace(/\./g, '').length);
            input.setSelectionRange(newCursorPosition, newCursorPosition);
        }

        // Format estimasi biaya on keyup (more reliable than input)
        estimasiBiayaInput.addEventListener('keyup', function(e) {
            // Only format if it's a number key or allowed keys
            if (e.key >= '0' && e.key <= '9' || e.key === 'Backspace' || e.key === 'Delete' || e.key === 'Tab') {
                formatNumber(this);
            }
        });

        // Remove formatting on focus for editing estimasi biaya
        estimasiBiayaInput.addEventListener('focus', function() {
            this.value = this.value.replace(/\./g, '');
        });

        // Re-format estimasi biaya on blur
        estimasiBiayaInput.addEventListener('blur', function() {
            formatNumber(this);
        });
    }

    // File size validation
    const fileInput = form.querySelector('input[name="lampiran_kembali"]');
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && file.size > 2 * 1024 * 1024) { // 2MB
                alert('File terlalu besar. Maksimal 2MB.');
                e.target.value = '';
            }
        });
    }

    // Auto-populate repair estimate data
    function populateRepairEstimate() {
        console.log('🔄 Starting populateRepairEstimate function...');

        // Find all repair cost elements
        const repairCostElements = document.querySelectorAll('.text-sm.font-medium.text-green-600');
        console.log('Found repair cost elements:', repairCostElements.length);

        let totalCost = 0;
        let repairDescriptions = [];

        repairCostElements.forEach((element, index) => {
            const costText = element.textContent.trim();
            console.log(`Processing element ${index + 1}:`, costText);

            // Extract number from "Rp 1.234.567" format
            const costMatch = costText.match(/Rp\s+([\d.,]+)/);
            if (costMatch) {
                const costValue = costMatch[1].replace(/\./g, '').replace(',', '.');
                const cost = parseFloat(costValue);
                console.log(`Parsed cost: ${costValue} -> ${cost}`);

                if (!isNaN(cost)) {
                    totalCost += cost;
                }
            }

            // Get repair description from the same repair item
            const repairItem = element.closest('.bg-gray-50.p-3.rounded');
            if (repairItem) {
                const descriptionElement = repairItem.querySelector('.text-sm.text-gray-600');
                if (descriptionElement) {
                    repairDescriptions.push(descriptionElement.textContent.trim());
                }
            }
        });

        console.log('Total cost calculated:', totalCost);
        console.log('Repair descriptions found:', repairDescriptions.length);

        // Format total cost with thousand separators
        const formattedTotal = totalCost.toLocaleString('id-ID');
        console.log('Formatted total:', formattedTotal);

        // Populate estimasi total biaya field
        const estimasiTotalBiayaInput = document.querySelector('input[name="total_biaya_perbaikan"]');
        if (estimasiTotalBiayaInput && totalCost > 0) {
            estimasiTotalBiayaInput.value = formattedTotal;
            console.log('✅ Estimasi total biaya field populated:', formattedTotal);
        } else {
            console.log('❌ Estimasi total biaya field not found or total cost is 0');
        }

        // Update status indicator
        const statusDiv = document.getElementById('autoPopulateStatus');
        if (statusDiv) {
            if (totalCost > 0) {
                statusDiv.textContent = `✅ Data perbaikan berhasil dimuat (${repairDescriptions.length} item, total: Rp ${formattedTotal})`;
                statusDiv.className = 'mt-2 text-sm text-green-600';
            } else {
                statusDiv.textContent = '⚠️ Tidak ada data perbaikan ditemukan';
                statusDiv.className = 'mt-2 text-sm text-orange-600';
            }
        }

        // Generate estimasi description
        const estimasiTextarea = document.querySelector('textarea[name="estimasi_perbaikan"]');
        if (estimasiTextarea && repairDescriptions.length > 0) {
            let estimasiText = `Berdasarkan data perbaikan yang ada:\n\n`;
            estimasiText += `Estimasi total biaya perbaikan: Rp ${formattedTotal}\n`;
            estimasiText += `Jumlah item perbaikan: ${repairDescriptions.length}\n\n`;
            estimasiText += `Detail perbaikan:\n`;
            repairDescriptions.forEach((desc, index) => {
                estimasiText += `${index + 1}. ${desc}\n`;
            });

            // Only populate if textarea is empty or contains only placeholder
            if (!estimasiTextarea.value || estimasiTextarea.value === estimasiTextarea.placeholder) {
                estimasiTextarea.value = estimasiText;
                console.log('✅ Estimasi textarea populated');
            } else {
                console.log('⚠️ Estimasi textarea already has content, not overwriting');
            }
        } else {
            console.log('❌ Estimasi textarea not found or no repair descriptions');
        }
    }

    // Call the function when page loads - with a small delay to ensure all elements are rendered
    setTimeout(() => {
        populateRepairEstimate();
    }, 100);

    // Also try immediately in case the delay isn't needed
    if (document.readyState === 'complete') {
        populateRepairEstimate();
    }

    // Add a button to manually refresh the estimate
    const estimasiField = document.querySelector('textarea[name="estimasi_perbaikan"]').closest('div');
    if (estimasiField) {
        const refreshButton = document.createElement('button');
        refreshButton.type = 'button';
        refreshButton.className = 'mt-2 px-3 py-1 bg-blue-500 text-white text-sm rounded hover:bg-blue-600 transition-colors';
        refreshButton.textContent = '🔄 Refresh dari Data Perbaikan';
        refreshButton.onclick = () => {
            console.log('Manual refresh triggered');
            populateRepairEstimate();
        };
        estimasiField.appendChild(refreshButton);

        // Add status indicator
        const statusDiv = document.createElement('div');
        statusDiv.id = 'autoPopulateStatus';
        statusDiv.className = 'mt-2 text-sm text-gray-600';
        statusDiv.textContent = '⏳ Memuat data perbaikan...';
        estimasiField.appendChild(statusDiv);
    }
</script>
@endsection
