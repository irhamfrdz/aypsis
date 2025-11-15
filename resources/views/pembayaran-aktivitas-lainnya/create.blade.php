@extends('layouts.app')

@section('title', 'Tambah Pembayaran Aktivitas Lain-lain')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="w-full">
        <div class="bg-white shadow rounded-lg">
            <div class="bg-white border-b border-gray-200 px-4 py-3 flex justify-between items-center rounded-t-lg">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-plus mr-2 text-blue-600"></i>
                    Tambah Pembayaran Aktivitas Lain-lain
                </h3>
                <div>
                    <a href="{{ route('pembayaran-aktivitas-lainnya.index') }}" class="inline-flex items-center px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium text-xs rounded-md transition duration-150 ease-in-out">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </div>

            <form action="{{ route('pembayaran-aktivitas-lainnya.store') }}" method="POST">
                @csrf
                <div class="p-4">
                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-3 py-2 rounded mb-4 text-sm">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if (count($errors) > 0)
                        <div class="bg-red-100 border border-red-400 text-red-700 px-3 py-2 rounded mb-4 text-sm">
                            <strong>Terjadi kesalahan:</strong>
                            <ul class="mt-2 ml-4">
                                @foreach ($errors->all() as $error)
                                    <li class="list-disc">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="nomor_pembayaran" class="block text-xs font-medium text-gray-700 mb-1">
                                Nomor Pembayaran <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded shadow-sm bg-gray-50 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                   id="nomor_pembayaran"
                                   name="nomor_pembayaran"
                                   value="{{ old('nomor_pembayaran') }}"
                                   placeholder="Auto generated dari bank yang dipilih"
                                   readonly>
                        </div>

                        <div>
                            <label for="nomor_accurate" class="block text-xs font-medium text-gray-700 mb-1">
                                Nomor Accurate
                            </label>
                            <input type="text"
                                   class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                   id="nomor_accurate"
                                   name="nomor_accurate"
                                   value="{{ old('nomor_accurate') }}"
                                   placeholder="Masukkan nomor dari sistem Accurate">
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-info-circle mr-1"></i>Opsional - Nomor referensi dari sistem Accurate
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="tanggal_pembayaran" class="block text-xs font-medium text-gray-700 mb-1">
                                Tanggal Pembayaran <span class="text-red-500">*</span>
                            </label>
                            <input type="date"
                                   class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                   id="tanggal_pembayaran"
                                   name="tanggal_pembayaran"
                                   value="{{ old('tanggal_pembayaran', date('Y-m-d')) }}"
                                   required>
                        </div>

                        <div>
                            <label for="kegiatan" class="block text-xs font-medium text-gray-700 mb-1">
                                Kegiatan
                            </label>
                            <select class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                    id="kegiatan"
                                    name="kegiatan">
                                <option value="">Pilih Kegiatan</option>
                                @if(isset($masterKegiatan) && $masterKegiatan->count() > 0)
                                    @foreach($masterKegiatan as $kegiatan)
                                        <option value="{{ $kegiatan->nama_kegiatan }}" {{ old('kegiatan') == $kegiatan->nama_kegiatan ? 'selected' : '' }}>
                                            {{ $kegiatan->nama_kegiatan }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="" disabled>Tidak ada kegiatan uang muka tersedia</option>
                                @endif
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Data dari master kegiatan bertipe "uang muka"</p>
                        </div>
                    </div>

                    <!-- Row for Kapal and Voyage (untuk Uang Muka OB) -->
                    <div id="ob_container" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4 hidden">
                        <div>
                            <label for="nama_kapal" class="block text-xs font-medium text-gray-700 mb-1">
                                Nama Kapal <span class="text-red-500">*</span>
                            </label>
                            <select class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                    id="nama_kapal"
                                    name="nama_kapal">
                                <option value="">Pilih Kapal</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Pilih kapal untuk kegiatan OB</p>
                        </div>
                        <div>
                            <label for="nomor_voyage" class="block text-xs font-medium text-gray-700 mb-1">
                                Nomor Voyage <span class="text-red-500">*</span>
                            </label>
                            <select class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                    id="nomor_voyage"
                                    name="nomor_voyage">
                                <option value="">Pilih Voyage</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Pilih nomor voyage kapal</p>
                        </div>
                    </div>

                    <!-- Plat Nomor Container (untuk kegiatan KIR & STNK) -->
                    <div id="plat_nomor_container" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4 hidden">
                        <div>
                            <label for="plat_nomor" class="block text-xs font-medium text-gray-700 mb-1">
                                Plat Nomor <span class="text-red-500">*</span>
                            </label>
                            <select class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                    id="plat_nomor"
                                    name="plat_nomor">
                                <option value="">Pilih Plat Nomor</option>
                                @if(isset($masterMobil) && $masterMobil->count() > 0)
                                    @foreach($masterMobil as $mobil)
                                        <option value="{{ $mobil->nomor_polisi }}" {{ old('plat_nomor') == $mobil->nomor_polisi ? 'selected' : '' }}>
                                            {{ $mobil->nomor_polisi }}
                                            @if($mobil->kode_no)
                                                - {{ $mobil->kode_no }}
                                            @endif
                                        </option>
                                    @endforeach
                                @else
                                    <option value="" disabled>Tidak ada mobil tersedia</option>
                                @endif
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Wajib dipilih untuk kegiatan KIR & STNK</p>
                        </div>
                        <div>
                            <!-- Placeholder untuk menjaga grid layout -->
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="total_pembayaran" class="block text-xs font-medium text-gray-700 mb-1">
                                Total Pembayaran <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                    <span class="text-gray-500 text-xs">Rp</span>
                                </div>
                                <input type="text"
                                       class="w-full pl-6 pr-2 py-1.5 text-sm border border-gray-300 rounded shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                       id="total_pembayaran"
                                       name="total_pembayaran"
                                       value="{{ old('total_pembayaran') }}"
                                       placeholder="0"
                                       required>
                            </div>
                        </div>
                        <div>
                            <!-- Space for future fields if needed -->
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="pilih_bank" class="block text-xs font-medium text-gray-700 mb-1">
                                Pilih Bank <span class="text-red-500">*</span>
                            </label>
                            <select class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                    id="pilih_bank"
                                    name="pilih_bank"
                                    required>
                                <option value="">Pilih Bank</option>
                                @if(isset($bankAccounts) && $bankAccounts->count() > 0)
                                    @foreach($bankAccounts as $coa)
                                        <option value="{{ $coa->id }}" {{ old('pilih_bank') == $coa->id ? 'selected' : '' }}>
                                            {{ $coa->nomor_akun }} - {{ $coa->nama_akun }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="" disabled>Tidak ada akun bank/kas tersedia</option>
                                @endif
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Pilih dari master COA kategori Bank/Kas</p>
                        </div>

                        <div>
                            <label for="akun_biaya_id" class="block text-xs font-medium text-gray-700 mb-1">
                                Akun Biaya <span class="text-red-500">*</span>
                            </label>
                            <select class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                    id="akun_biaya_id"
                                    name="akun_biaya_id"
                                    required>
                                <option value="">Pilih Akun Biaya</option>
                                @if(isset($coaBiaya) && $coaBiaya->count() > 0)
                                    @foreach($coaBiaya as $coa)
                                        <option value="{{ $coa->id }}" {{ old('akun_biaya_id') == $coa->id ? 'selected' : '' }}>
                                            {{ $coa->nomor_akun }} - {{ $coa->nama_akun }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="" disabled>Tidak ada akun biaya tersedia</option>
                                @endif
                            </select>
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-receipt mr-1"></i>Pilih akun biaya dari COA tipe BIAYA
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">
                                Jenis Pembayaran
                            </label>
                            <div class="space-y-2">
                                <!-- Checkbox Bayar DP -->
                                <div class="flex items-center">
                                    <input type="checkbox"
                                           id="is_dp"
                                           name="is_dp"
                                           value="1"
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                           {{ old('is_dp') ? 'checked' : '' }}>
                                    <label for="is_dp" class="ml-2 block text-sm text-gray-700">
                                        <i class="fas fa-money-bill text-green-600 mr-1"></i>
                                        Bayar DP (Down Payment)
                                    </label>
                                </div>

                                <!-- Info DP -->
                                <div id="dp_info" class="hidden bg-yellow-50 border border-yellow-200 rounded p-2">
                                    <div class="flex items-start">
                                        <i class="fas fa-info-circle text-yellow-600 mt-0.5 mr-2"></i>
                                        <div class="text-xs text-yellow-800">
                                            <strong>Pembayaran DP:</strong> Centang jika ini merupakan pembayaran uang muka/down payment untuk suatu transaksi atau kontrak.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dropdown Debit/Kredit -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="jenis_transaksi" class="block text-xs font-medium text-gray-700 mb-1">
                                Jenis Transaksi <span class="text-red-500">*</span>
                            </label>
                            <select class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                    id="jenis_transaksi"
                                    name="jenis_transaksi"
                                    required>
                                <option value="">Pilih Jenis Transaksi</option>
                                <option value="debit" {{ old('jenis_transaksi') == 'debit' ? 'selected' : '' }}>
                                    <i class="fas fa-plus-circle text-green-600"></i> Debit (Pemasukan)
                                </option>
                                <option value="kredit" {{ old('jenis_transaksi') == 'kredit' ? 'selected' : '' }}>
                                    <i class="fas fa-minus-circle text-red-600"></i> Kredit (Pengeluaran)
                                </option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Pilih jenis transaksi untuk pencatatan akuntansi</p>
                        </div>
                        <div>
                            <!-- Space for future fields if needed -->
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="aktivitas_pembayaran" class="block text-xs font-medium text-gray-700 mb-1">
                            Aktivitas Pembayaran <span class="text-red-500">*</span>
                        </label>
                        <textarea class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                  id="aktivitas_pembayaran"
                                  name="aktivitas_pembayaran"
                                  rows="3"
                                  placeholder="Masukkan deskripsi aktivitas pembayaran (wajib diisi)"
                                  required
                                  minlength="5">{{ old('aktivitas_pembayaran') }}</textarea>
                        <small class="text-xs text-red-600 mt-1">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Field ini wajib diisi - minimal 5 karakter
                        </small>
                    </div>

                    <!-- Summary Display -->
                    <div class="bg-blue-50 border border-blue-200 rounded p-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-blue-800">Total Pembayaran:</span>
                            <span class="text-lg font-bold text-blue-600" id="display_total">Rp 0</span>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 border-t border-gray-200 px-4 py-3 rounded-b-lg">
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-2">
                        <div class="flex gap-2">
                            <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs rounded transition duration-150 ease-in-out">
                                <i class="fas fa-save mr-1"></i> Simpan
                            </button>
                        </div>
                        <div>
                            <a href="{{ route('pembayaran-aktivitas-lainnya.index') }}" class="inline-flex items-center px-3 py-1.5 bg-gray-600 hover:bg-gray-700 text-white font-medium text-xs rounded transition duration-150 ease-in-out">
                                <i class="fas fa-times mr-1"></i> Batal
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Custom styles for enhanced UX */
    .hover-scale:hover {
        transform: scale(1.02);
        transition: transform 0.2s ease-in-out;
    }

    /* Focus styles for better accessibility */
    input:focus, select:focus, textarea:focus {
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Format total pembayaran input
    $('#total_pembayaran').on('input', function() {
        let value = $(this).val().replace(/[^\d]/g, '');
        let formattedValue = new Intl.NumberFormat('id-ID').format(value);
        $(this).val(formattedValue);
        updateDisplay();
    });

    // Update display total
    function updateDisplay() {
        let total = $('#total_pembayaran').val().replace(/[^\d]/g, '') || 0;
        let formattedTotal = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);

        if ($('#is_dp').is(':checked')) {
            $('#display_total').html(formattedTotal + ' <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full ml-2">DP</span>');
        } else {
            $('#display_total').text(formattedTotal);
        }
    }

    // Auto-generate nomor pembayaran if empty (fallback method)
    $('#nomor_pembayaran').on('focus', function() {
        if ($(this).val() === '' || $(this).val().includes('Error') || $(this).val().includes('Login required')) {
            generateFallbackNumber();
        }
    });

    // Form validation
    $('form').on('submit', function(e) {
        let totalValue = $('#total_pembayaran').val().replace(/[^\d]/g, '');

        if (!totalValue || totalValue == '0') {
            e.preventDefault();
            alert('Harap masukkan total pembayaran yang valid.');
            $('#total_pembayaran').focus();
            return false;
        }

        // Validate required fields
        let requiredFields = [
            { field: '#nomor_pembayaran', name: 'Nomor Pembayaran' },
            { field: '#tanggal_pembayaran', name: 'Tanggal Pembayaran' },
            { field: '#pilih_bank', name: 'Pilih Bank' }
        ];

        for (let i = 0; i < requiredFields.length; i++) {
            let field = requiredFields[i];
            if (!$(field.field).val().trim()) {
                e.preventDefault();
                alert('Harap lengkapi field ' + field.name);
                $(field.field).focus();
                return false;
            }
        }

        // Special validation for aktivitas_pembayaran (minimum 5 characters)
        let aktivitasValue = $('#aktivitas_pembayaran').val().trim();
        if (!aktivitasValue) {
            e.preventDefault();
            alert('Field Aktivitas Pembayaran wajib diisi!');
            $('#aktivitas_pembayaran').focus();
            return false;
        }
        if (aktivitasValue.length < 5) {
            e.preventDefault();
            alert('Aktivitas Pembayaran minimal 5 karakter!');
            $('#aktivitas_pembayaran').focus();
            return false;
        }

        // Validation untuk plat nomor jika kegiatan KIR/STNK dipilih
        let selectedKegiatan = $('#kegiatan').val();
        if (selectedKegiatan && (selectedKegiatan.toLowerCase().includes('kir') || selectedKegiatan.toLowerCase().includes('stnk'))) {
            let platNomorValue = $('#plat_nomor').val().trim();
            if (!platNomorValue) {
                e.preventDefault();
                alert('Plat Nomor wajib dipilih untuk kegiatan KIR & STNK!');
                $('#plat_nomor').focus();
                return false;
            }
        }

        // Remove number formatting before submission
        $('#total_pembayaran').val(totalValue);
    });

    // Auto-resize textarea
    $('textarea').on('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    // Real-time validation for aktivitas_pembayaran
    $('#aktivitas_pembayaran').on('input', function() {
        let value = $(this).val().trim();
        let feedback = $(this).siblings('small');

        if (value.length === 0) {
            feedback.removeClass('text-green-600').addClass('text-red-600');
            feedback.html('<i class="fas fa-exclamation-triangle mr-1"></i>Field ini wajib diisi - minimal 5 karakter');
            $(this).removeClass('border-green-300').addClass('border-red-300');
        } else if (value.length < 5) {
            feedback.removeClass('text-green-600').addClass('text-red-600');
            feedback.html('<i class="fas fa-exclamation-triangle mr-1"></i>Minimal 5 karakter (saat ini: ' + value.length + ')');
            $(this).removeClass('border-green-300').addClass('border-red-300');
        } else {
            feedback.removeClass('text-red-600').addClass('text-green-600');
            feedback.html('<i class="fas fa-check-circle mr-1"></i>Aktivitas pembayaran valid (' + value.length + ' karakter)');
            $(this).removeClass('border-red-300').addClass('border-green-300');
        }
    });

    // DP Checkbox functionality
    $('#is_dp').on('change', function() {
        if ($(this).is(':checked')) {
            $('#dp_info').removeClass('hidden').addClass('block');
            // Update display summary to show DP status
            updateDisplayWithDP();
        } else {
            $('#dp_info').removeClass('block').addClass('hidden');
            updateDisplay();
        }
    });

    // Update display total with DP indicator
    function updateDisplayWithDP() {
        let total = $('#total_pembayaran').val().replace(/[^\d]/g, '') || 0;
        let formattedTotal = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);

        if ($('#is_dp').is(':checked')) {
            $('#display_total').html(formattedTotal + ' <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full ml-2">DP</span>');
        } else {
            $('#display_total').text(formattedTotal);
        }
    }

    // Initialize
    updateDisplay();

    // Format number on load if there's old input
    let totalValue = $('#total_pembayaran').val();
    if (totalValue) {
        let cleanValue = totalValue.replace(/[^\d]/g, '');
        let formattedValue = new Intl.NumberFormat('id-ID').format(cleanValue);
        $('#total_pembayaran').val(formattedValue);
        updateDisplay();
    }

    // Check DP status on load
    if ($('#is_dp').is(':checked')) {
        $('#dp_info').removeClass('hidden').addClass('block');
        updateDisplayWithDP();
    }

    // Bank/COA selection change handler - Auto generate nomor pembayaran
    $('#pilih_bank').on('change', function() {
        let selectedOption = $(this).find('option:selected');
        let coaId = selectedOption.val();

        if (coaId) {
            // Generate nomor pembayaran format
            generateNomorPembayaran(coaId, selectedOption.text());
        } else {
            $('#nomor_pembayaran').val('');
        }
    });

    // Function to generate nomor pembayaran from server
    function generateNomorPembayaran(coaId, coaText) {
        // Show loading state
        $('#nomor_pembayaran').val('Generating...');

        // Call API to get actual running number from master nomor terakhir
        const url = `{{ route("pembayaran-aktivitas-lainnya.generate-nomor-preview") }}?coa_id=${coaId}`;

        $.ajax({
            url: url,
            method: 'GET',
            timeout: 10000, // 10 second timeout
            success: function(response) {
                if (response.success) {
                    $('#nomor_pembayaran').val(response.nomor_pembayaran);
                    console.log('✅ Generated Nomor Pembayaran:', response.nomor_pembayaran);
                    console.log('Details:', response.details);
                } else {
                    console.error('❌ Server Error:', response.message);
                    generateFallbackNumber();
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ AJAX Error:', error);
                console.error('Status:', status);
                console.error('Response Status:', xhr.status);

                // Check if it's authentication error (redirect to login)
                if (xhr.status === 401 || xhr.responseText.includes('Login')) {
                    console.error('🔐 Authentication required - please login first');
                    $('#nomor_pembayaran').val('Login required');
                    alert('Silakan login terlebih dahulu untuk generate nomor pembayaran');
                    return;
                }

                // Show detailed error if available
                try {
                    const errorData = JSON.parse(xhr.responseText);
                    console.error('Error details:', errorData);
                    $('#nomor_pembayaran').val('Error: ' + errorData.message);
                } catch (e) {
                    console.error('Could not parse error response');
                    generateFallbackNumber();
                }
            }
        });
    }

    // Fallback function to generate nomor pembayaran locally
    function generateFallbackNumber() {
        console.log('🔄 Using fallback number generation...');

        let today = new Date();
        let year = today.getFullYear().toString().slice(-2); // 2 digit year
        let month = String(today.getMonth() + 1).padStart(2, '0');
        let random = Math.floor(Math.random() * 999999).toString().padStart(6, '0');

        // Use PAL prefix as fallback
        let nomorPembayaran = `PAL-${month}-${year}-${random}`;
        $('#nomor_pembayaran').val(nomorPembayaran);

        console.log('📝 Fallback nomor generated:', nomorPembayaran);
    }

    // Generate initial nomor pembayaran if bank is already selected
    let initialBank = $('#pilih_bank').val();
    if (initialBank) {
        let selectedOption = $('#pilih_bank option:selected');
        generateNomorPembayaran(initialBank, selectedOption.text());
    }

    // Handler untuk dropdown kegiatan - tampilkan plat nomor jika kegiatan mengandung "kir" atau "stnk"
    // atau tampilkan kapal & voyage jika kegiatan adalah "Uang Muka OB Bongkar" atau "Uang Muka OB Muat"
    $('#kegiatan').on('change', function() {
        let selectedKegiatan = $(this).val();
        let lowerKegiatan = selectedKegiatan.toLowerCase();

        // Reset semua container
        $('#plat_nomor_container').addClass('hidden');
        $('#plat_nomor').removeAttr('required').val('');
        $('#ob_container').addClass('hidden');
        $('#nama_kapal, #nomor_voyage').removeAttr('required').val('');

        // Cek apakah kegiatan mengandung kata "kir" atau "stnk"
        if (lowerKegiatan.includes('kir') || lowerKegiatan.includes('stnk')) {
            $('#plat_nomor_container').removeClass('hidden');
            $('#plat_nomor').attr('required', true);
        }
        // Cek apakah kegiatan adalah "Uang Muka OB Bongkar" atau "Uang Muka OB Muat"
        else if (lowerKegiatan.includes('uang muka ob bongkar') || lowerKegiatan.includes('uang muka ob muat')) {
            $('#ob_container').removeClass('hidden');
            $('#nama_kapal, #nomor_voyage').attr('required', true);
            
            // Load data kapal
            loadKapalData();
        }
    });

    // Function to load kapal data from OB Bongkar
    function loadKapalData() {
        $('#nama_kapal').html('<option value="">Loading...</option>');
        
        $.ajax({
            url: '/api/get-kapal-list',
            method: 'GET',
            success: function(response) {
                let options = '<option value="">Pilih Kapal</option>';
                
                if (response.success && response.data.length > 0) {
                    response.data.forEach(function(kapal) {
                        options += `<option value="${kapal.kapal}">${kapal.kapal}</option>`;
                    });
                } else {
                    options = '<option value="">Tidak ada data kapal</option>';
                }
                
                $('#nama_kapal').html(options);
            },
            error: function() {
                $('#nama_kapal').html('<option value="">Error loading data</option>');
            }
        });
    }

    // Handler untuk dropdown kapal - load voyage berdasarkan kapal yang dipilih
    $('#nama_kapal').on('change', function() {
        let kapalName = $(this).val();
        
        if (kapalName) {
            $('#nomor_voyage').html('<option value="">Loading...</option>');
            
            $.ajax({
                url: '/api/get-voyage-list',
                method: 'GET',
                data: { kapal: kapalName },
                success: function(response) {
                    let options = '<option value="">Pilih Voyage</option>';
                    
                    if (response.success && response.data.length > 0) {
                        response.data.forEach(function(voyage) {
                            options += `<option value="${voyage.voyage}">${voyage.voyage}</option>`;
                        });
                    } else {
                        options = '<option value="">Tidak ada data voyage</option>';
                    }
                    
                    $('#nomor_voyage').html(options);
                },
                error: function() {
                    $('#nomor_voyage').html('<option value="">Error loading data</option>');
                }
            });
        } else {
            $('#nomor_voyage').html('<option value="">Pilih Voyage</option>');
        }
    });

    // Cek kegiatan pada saat load (untuk old input)
    let initialKegiatan = $('#kegiatan').val();
    if (initialKegiatan) {
        $('#kegiatan').trigger('change');
    }
});
</script>
@endpush
