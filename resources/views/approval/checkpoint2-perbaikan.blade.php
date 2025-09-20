@extends('layouts.app')

@section('title', 'Approval Perbaikan Kontainer')
@section('page_title', 'Approval Perbaikan Kontainer')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white shadow-lg rounded-lg p-4">
                <h3 class="font-semibold text-gray-800 border-b pb-2 mb-3">Informasi Umum</h3>
                <div class="text-sm">
                    <div class="mb-1"><span class="font-medium text-gray-600">Nomor Memo:</span> <span class="text-gray-800 font-mono">{{ $permohonan->nomor_memo }}</span></div>
                    <div class="mb-1"><span class="font-medium text-gray-600">Supir:</span> <span class="text-gray-800">{{ $permohonan->supir->nama_panggilan ?? 'N/A' }}</span></div>
                    <div class="mb-1"><span class="font-medium text-gray-600">Kegiatan:</span> <span class="text-gray-800">{{ \App\Models\MasterKegiatan::where('kode_kegiatan', $permohonan->kegiatan)->value('nama_kegiatan') ?? $permohonan->kegiatan }}</span></div>
                    <div><span class="font-medium text-gray-600">Vendor:</span> <span class="text-gray-800">{{ $permohonan->vendor_perusahaan ?? 'N/A' }}</span></div>
                </div>
            </div>

            @if($permohonan->checkpoints && $permohonan->checkpoints->count())
            <div class="bg-white shadow-lg rounded-lg p-4">
                <h4 class="font-semibold text-blue-800 border-b pb-2 mb-3">Riwayat Checkpoint</h4>
                <div class="space-y-3 text-sm max-h-48 overflow-y-auto">
                    @foreach($permohonan->checkpoints->sortBy('tanggal_checkpoint') as $checkpoint)
                    <div class="flex items-start bg-blue-50 p-3 rounded-lg border-l-4 border-blue-200">
                        <div class="flex-1">
                            <div class="font-medium text-gray-800">{{ $checkpoint->keterangan ?? 'Checkpoint' }}</div>
                            <div class="text-xs text-gray-500 mt-1">{{ \Carbon\Carbon::parse($checkpoint->tanggal_checkpoint)->format('d M Y H:i') }}</div>
                        </div>
                        <span class="px-2 py-0.5 bg-blue-200 text-blue-800 text-xs rounded-full font-medium">{{ $checkpoint->status }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <div class="lg:col-span-2">
            @if($kontainerPerbaikan && $kontainerPerbaikan->count())
            <div class="bg-white shadow-lg rounded-lg p-4 h-full flex flex-col">
                <h4 class="font-semibold text-green-800 border-b pb-2 mb-3">Detail Kontainer Perbaikan</h4>
                <div class="flex-grow space-y-4 overflow-y-auto">
                    @foreach($kontainerPerbaikan as $kontainer)
                    <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-200">
                        <div class="flex justify-between items-center mb-2">
                            <div class="flex-1">
                                <div class="font-bold text-gray-800 text-lg">{{ $kontainer->nomor_kontainer }}</div>
                                <div class="text-sm text-gray-600">Size: {{ $kontainer->ukuran ?? 'N/A' }} | Status: {{ $kontainer->status }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-gray-600">Jumlah Perbaikan:</div>
                                <div class="font-semibold text-orange-600 text-lg">{{ $kontainer->perbaikanKontainers->count() }}</div>
                            </div>
                        </div>

                        @if($kontainer->perbaikanKontainers && $kontainer->perbaikanKontainers->count())
                        <div class="space-y-2 mt-4">
                            @foreach($kontainer->perbaikanKontainers as $perbaikan)
                            <div class="bg-white p-3 rounded border-l-4 border-orange-400">
                                <div class="flex justify-between items-center">
                                    <div class="flex-1">
                                        <div class="font-medium text-gray-800">{{ $perbaikan->nomor_tagihan ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-600 truncate max-w-xs">{{ $perbaikan->deskripsi_perbaikan }}</div>
                                    </div>
                                    <div class="text-right flex-shrink-0 ml-4">
                                        <div class="text-sm font-bold text-green-600">Rp {{ number_format($perbaikan->biaya_perbaikan ?? 0, 0, ',', '.') }}</div>
                                        <span class="px-2 py-0.5 {{ $perbaikan->status_perbaikan === 'sudah_dibayar' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }} text-xs rounded-full font-medium">
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
        </div>
    </div>

    <div class="bg-white shadow-lg rounded-lg p-6">
        <h3 class="text-xl font-semibold text-gray-800 mb-4 text-center">Form Approval Perbaikan Kontainer</h3>
        <form action="{{ route('approval.store', $permohonan) }}" method="POST" enctype="multipart/form-data" id="approvalForm" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Status Approval <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-4">
                        <label class="flex items-center cursor-pointer p-2 rounded-lg border-2 border-transparent hover:border-green-300 transition-colors">
                            <input type="radio" name="status_permohonan" value="selesai" class="mr-2 text-green-600 focus:ring-green-500" required>
                            <span class="text-green-700 font-medium text-sm">Selesai</span>
                        </label>
                        <label class="flex items-center cursor-pointer p-2 rounded-lg border-2 border-transparent hover:border-red-300 transition-colors">
                            <input type="radio" name="status_permohonan" value="bermasalah" class="mr-2 text-red-600 focus:ring-red-500">
                            <span class="text-red-700 font-medium text-sm">Bermasalah</span>
                        </label>
                    </div>
                    <div id="statusError" class="text-red-500 text-sm mt-1 hidden">Harap pilih status approval.</div>

                    <!-- Checklist Butuh Cat -->
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Checklist Tambahan
                        </label>
                        <div class="flex items-center">
                            <input type="checkbox" name="butuh_cat" value="1" id="butuhCat" class="mr-2 text-blue-600 focus:ring-blue-500">
                            <label for="butuhCat" class="text-sm text-gray-700 cursor-pointer">
                                Butuh Cat (Perlu Penyemprotan Cat)
                            </label>
                            <span id="catIndicator" class="ml-2 text-xs text-blue-600 opacity-0 transition-opacity">✓ Dicentang</span>
                        </div>

                        <!-- Dropdown Status Cat -->
                        <div id="statusCatContainer" class="mt-3 opacity-0 max-h-0 min-h-0 overflow-hidden transition-all duration-300">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Status Cat <span class="text-red-500">*</span>
                            </label>
                            <select name="status_cat" id="statusCat" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white">
                                <option value="">Pilih Status Cat</option>
                                <option value="full">Full (Seluruh Kontainer)</option>
                                <option value="sebagian">Sebagian (Area Tertentu)</option>
                            </select>
                            <div id="statusCatError" class="text-red-500 text-sm mt-1 hidden">Harap pilih status cat.</div>

                            <!-- Estimasi Biaya Cat -->
                            <div id="estimasiCatContainer" class="mt-3 p-4 bg-gray-50 rounded-lg opacity-0 max-h-0 min-h-0 overflow-hidden transition-all duration-300">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Estimasi Biaya Cat (Rp) <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="estimasi_biaya_cat" id="estimasiBiayaCat" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="Contoh: 1.000.000">
                                <div id="estimasiCatError" class="text-red-500 text-sm mt-2 hidden">Harap isi estimasi biaya cat.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estimasi Total Biaya Perbaikan (Rp)</label>
                    <input type="text" name="total_biaya_perbaikan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="Contoh: 1.000.000">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vendor/Bengkel <span class="text-red-500">*</span></label>
                    <input type="text" name="vendor_bengkel" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="Nama vendor atau bengkel" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estimasi Perbaikan</label>
                    <textarea name="estimasi_perbaikan" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="Jelaskan estimasi waktu dan jenis perbaikan..."></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Karyawan</label>
                    <textarea name="catatan_karyawan" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="Tambahkan catatan tambahan..."></textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lampiran Kembali (Opsional)</label>
                    <input type="file" name="lampiran_kembali" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-sm text-gray-500 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="text-xs text-gray-500 mt-1">Format: PDF, JPG, JPEG, PNG. Maksimal 2MB.</p>
                </div>
            </div>

            <div class="flex justify-between items-center pt-4 border-t border-gray-200 mt-6">
                <a href="{{ route('approval.dashboard') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors text-sm font-medium shadow-sm">
                    ← Kembali
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium shadow-sm">
                    Approve & Selesaikan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('approvalForm');
    const statusError = document.getElementById('statusError');
    const submitBtn = form.querySelector('button[type="submit"]');

    const statusRadios = form.querySelectorAll('input[name="status_permohonan"]');
    const butuhCatCheckbox = document.getElementById('butuhCat');
    statusRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.checked) {
                statusError.classList.add('hidden');
            }
        });
    });

    // Handle butuh cat checkbox
    if (butuhCatCheckbox) {
        const catIndicator = document.getElementById('catIndicator');
        const statusCatContainer = document.getElementById('statusCatContainer');
        const statusCatSelect = document.getElementById('statusCat');
        const statusCatError = document.getElementById('statusCatError');

        butuhCatCheckbox.addEventListener('change', function() {
            const catatanTextarea = document.querySelector('textarea[name="catatan_karyawan"]');
            if (this.checked) {
                catIndicator.classList.remove('opacity-0');
                catIndicator.classList.add('opacity-100');

                // Show status cat dropdown
                statusCatContainer.classList.remove('opacity-0', 'max-h-0');
                statusCatContainer.classList.add('opacity-100', 'max-h-48');

                // Auto-add note about needing paint
                if (catatanTextarea && (!catatanTextarea.value || catatanTextarea.value === catatanTextarea.placeholder)) {
                    catatanTextarea.value = "Kontainer memerlukan penyemprotan cat setelah perbaikan.";
                } else if (catatanTextarea && !catatanTextarea.value.includes("penyemprotan cat")) {
                    catatanTextarea.value += "\n\nKontainer memerlukan penyemprotan cat setelah perbaikan.";
                }
            } else {
                catIndicator.classList.remove('opacity-100');
                catIndicator.classList.add('opacity-0');

                // Hide status cat dropdown
                statusCatContainer.classList.remove('opacity-100', 'max-h-48');
                statusCatContainer.classList.add('opacity-0', 'max-h-0');

                // Reset dropdown value
                if (statusCatSelect) {
                    statusCatSelect.value = '';
                }
                if (statusCatError) {
                    statusCatError.classList.add('hidden');
                }

                // Reset estimasi biaya cat
                const estimasiCatContainer = document.getElementById('estimasiCatContainer');
                const estimasiBiayaCat = document.getElementById('estimasiBiayaCat');
                const estimasiCatError = document.getElementById('estimasiCatError');

                if (estimasiCatContainer) {
                    estimasiCatContainer.classList.remove('opacity-100', 'max-h-40');
                    estimasiCatContainer.classList.add('opacity-0', 'max-h-0');
                }
                if (estimasiBiayaCat) {
                    estimasiBiayaCat.value = '';
                }
                if (estimasiCatError) {
                    estimasiCatError.classList.add('hidden');
                }
            }
        });

        // Handle status cat selection
        if (statusCatSelect) {
            statusCatSelect.addEventListener('change', function() {
                const estimasiCatContainer = document.getElementById('estimasiCatContainer');
                const estimasiBiayaCat = document.getElementById('estimasiBiayaCat');
                const estimasiCatError = document.getElementById('estimasiCatError');

                if (this.value) {
                    statusCatError.classList.add('hidden');

                    // Show estimasi biaya cat field
                    estimasiCatContainer.classList.remove('opacity-0', 'max-h-0');
                    estimasiCatContainer.classList.add('opacity-100', 'max-h-40');

                    // Set default value based on status
                    if (this.value === 'full' && (!estimasiBiayaCat.value || estimasiBiayaCat.value === estimasiBiayaCat.placeholder)) {
                        estimasiBiayaCat.value = '2.500.000'; // Default untuk full
                    } else if (this.value === 'sebagian' && (!estimasiBiayaCat.value || estimasiBiayaCat.value === estimasiBiayaCat.placeholder)) {
                        estimasiBiayaCat.value = '1.000.000'; // Default untuk sebagian
                    }
                } else {
                    // Hide estimasi biaya cat field
                    estimasiCatContainer.classList.remove('opacity-100', 'max-h-40');
                    estimasiCatContainer.classList.add('opacity-0', 'max-h-0');

                    // Reset value
                    if (estimasiBiayaCat) {
                        estimasiBiayaCat.value = '';
                    }
                    if (estimasiCatError) {
                        estimasiCatError.classList.add('hidden');
                    }
                }
            });

            // Handle estimasi biaya cat input
            const estimasiBiayaCat = document.getElementById('estimasiBiayaCat');
            if (estimasiBiayaCat) {
                estimasiBiayaCat.addEventListener('input', function() {
                    const estimasiCatError = document.getElementById('estimasiCatError');
                    if (this.value.trim()) {
                        estimasiCatError.classList.add('hidden');
                    }
                    formatRupiahInput(this);
                });

                // Format number input
                estimasiBiayaCat.addEventListener('focus', function() {
                    this.value = this.value.replace(/\./g, '');
                });

                estimasiBiayaCat.addEventListener('blur', function() {
                    if (this.value) {
                        this.value = formatNumber(this.value);
                    }
                });
            }
        }
    }

    form.addEventListener('submit', function(e) {
        const statusSelected = form.querySelector('input[name="status_permohonan"]:checked');
        if (!statusSelected) {
            e.preventDefault();
            statusError.classList.remove('hidden');
            statusError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        const estimasiBiayaInput = form.querySelector('input[name="total_biaya_perbaikan"]');
        if (estimasiBiayaInput) {
            estimasiBiayaInput.value = estimasiBiayaInput.value.replace(/\./g, '');
        }

        const vendorBengkelInput = form.querySelector('input[name="vendor_bengkel"]');
        if (vendorBengkelInput && !vendorBengkelInput.value.trim()) {
            e.preventDefault();
            alert('Vendor/Bengkel harus diisi!');
            vendorBengkelInput.focus();
            return;
        }

        // Validate status cat if butuh cat is checked
        const butuhCatChecked = form.querySelector('input[name="butuh_cat"]:checked');
        if (butuhCatChecked) {
            const statusCatSelect = form.querySelector('select[name="status_cat"]');
            const statusCatError = document.getElementById('statusCatError');
            if (statusCatSelect && !statusCatSelect.value) {
                e.preventDefault();
                statusCatError.classList.remove('hidden');
                statusCatSelect.focus();
                return;
            }

            // Validate estimasi biaya cat if status cat is selected
            if (statusCatSelect && statusCatSelect.value) {
                const estimasiBiayaCat = form.querySelector('input[name="estimasi_biaya_cat"]');
                const estimasiCatError = document.getElementById('estimasiCatError');
                if (estimasiBiayaCat && !estimasiBiayaCat.value.trim()) {
                    e.preventDefault();
                    estimasiCatError.classList.remove('hidden');
                    estimasiBiayaCat.focus();
                    return;
                }

                // Clean estimasi biaya cat input before submit
                if (estimasiBiayaCat) {
                    estimasiBiayaCat.value = estimasiBiayaCat.value.replace(/\./g, '');
                }
            }
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '⏳ Menyimpan...';
    });

    const estimasiBiayaInput = form.querySelector('input[name="total_biaya_perbaikan"]');
    const estimasiTextarea = document.querySelector('textarea[name="estimasi_perbaikan"]');
    const vendorBengkelInput = document.querySelector('input[name="vendor_bengkel"]');

    function formatNumber(value) {
        let cleaned = ('' + value).replace(/[^\d]/g, '');
        if (cleaned) {
            return cleaned.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }
        return '';
    }

    function formatRupiahInput(input) {
        let value = input.value;
        let caret = input.selectionStart;
        let number = value.replace(/\./g, '');
        if (number) {
            let formatted = formatNumber(number);
            input.value = formatted;
            // Adjust caret position
            let diff = formatted.replace(/\./g, '').length - number.length;
            input.setSelectionRange(caret + diff, caret + diff);
        }
    }

    function populateRepairEstimate() {
        const repairCostElements = document.querySelectorAll('.font-bold.text-green-600');
        let totalCost = 0;
        let repairDescriptions = [];

        repairCostElements.forEach(element => {
            const costText = element.textContent.trim();
            const costMatch = costText.match(/Rp\s+([\d.,]+)/);
            if (costMatch) {
                const costValue = costMatch[1].replace(/\./g, '').replace(',', '.');
                const cost = parseFloat(costValue);
                if (!isNaN(cost)) {
                    totalCost += cost;
                }
            }

            const repairItem = element.closest('.bg-white.p-3.rounded');
            if (repairItem) {
                const descriptionElement = repairItem.querySelector('.text-xs.text-gray-600');
                if (descriptionElement) {
                    repairDescriptions.push(descriptionElement.textContent.trim());
                }
            }
        });

        if (totalCost > 0) {
            estimasiBiayaInput.value = formatNumber(totalCost);
            const vendorElement = document.querySelector('.text-gray-800');
            if (vendorElement) {
                vendorBengkelInput.value = vendorElement.textContent;
            }

            let estimasiText = `Berdasarkan data perbaikan yang ada:\n\n`;
            estimasiText += `Estimasi total biaya perbaikan: Rp ${formatNumber(totalCost)}\n`;
            estimasiText += `Jumlah item perbaikan: ${repairDescriptions.length}\n\n`;
            estimasiText += `Detail perbaikan:\n`;
            repairDescriptions.forEach((desc, index) => {
                estimasiText += `${index + 1}. ${desc}\n`;
            });

            if (!estimasiTextarea.value || estimasiTextarea.value === estimasiTextarea.placeholder) {
                estimasiTextarea.value = estimasiText;
            }
        }
    }

    populateRepairEstimate();

    if (estimasiBiayaInput) {
        estimasiBiayaInput.addEventListener('input', function() {
            formatRupiahInput(this);
        });
        estimasiBiayaInput.addEventListener('focus', function() { this.value = this.value.replace(/\./g, ''); });
        estimasiBiayaInput.addEventListener('blur', function() { this.value = formatNumber(this.value); });
    }
});
</script>
@endsection
