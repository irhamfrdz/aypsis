@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-medium text-gray-900">
                        {{ $title }}
                    </h1>
                    <p class="mt-2 text-gray-600">Form untuk membuat pembayaran Uang Muka Out Bound (OB) baru</p>
                </div>

                <div class="flex space-x-3">
                    <a href="{{ route('pembayaran-uang-muka.index') }}"
                       class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </div>

            <!-- Form Pembayaran DP OB -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <form action="{{ route('pembayaran-uang-muka.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Alert Error -->
                        @if ($errors->any())
                            <div class="bg-red-50 border border-red-200 rounded-md p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800">Terdapat kesalahan pada input:</h3>
                                        <div class="mt-2 text-sm text-red-700">
                                            <ul class="list-disc list-inside space-y-1">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Alert Success -->
                        @if (session('success'))
                            <div class="bg-green-50 border border-green-200 rounded-md p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nomor Pembayaran -->
                            <div>
                                <label for="nomor_pembayaran" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nomor Pembayaran <span class="text-gray-400">(Opsional - Auto Generate)</span>
                                </label>
                                <div class="flex">
                                    <input type="text"
                                           name="nomor_pembayaran"
                                           id="nomor_pembayaran"
                                           value="{{ old('nomor_pembayaran') }}"
                                           placeholder="MDP-10-25-000001"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nomor_pembayaran') border-red-300 @enderror">
                                    <button type="button"
                                            onclick="generateNomor()"
                                            class="px-4 py-2 bg-blue-500 text-white rounded-r-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <i class="fas fa-sync-alt"></i> Auto
                                    </button>
                                </div>
                                @error('nomor_pembayaran')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500">Format: Kode_COA-Bulan(2)-Tahun(2)-Urutan(6). Pilih kas/bank untuk generate otomatis.</p>
                            </div>

                            <!-- Tanggal Pembayaran -->
                            <div>
                                <label for="tanggal_pembayaran" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Pembayaran <span class="text-red-500">*</span>
                                </label>
                                <input type="date"
                                       name="tanggal_pembayaran"
                                       id="tanggal_pembayaran"
                                       value="{{ old('tanggal_pembayaran', date('Y-m-d')) }}"
                                       required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tanggal_pembayaran') border-red-300 @enderror">
                                @error('tanggal_pembayaran')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Kas/Bank Field -->
                            <div>
                                <label for="kas_bank" class="block text-sm font-medium text-gray-700 mb-2">
                                    Pilih Kas/Bank <span class="text-red-500">*</span>
                                </label>
                                <select name="kas_bank"
                                        id="kas_bank"
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('kas_bank') border-red-300 @enderror">
                                    <option value="">-- Pilih Akun Kas/Bank --</option>
                                    @foreach($kasBankList as $kasBank)
                                        <option value="{{ $kasBank->id }}" {{ old('kas_bank') == $kasBank->id ? 'selected' : '' }}>
                                            {{ $kasBank->nomor_akun }} - {{ $kasBank->nama_akun }}
                                            @if($kasBank->saldo != 0)
                                                (Saldo: Rp {{ number_format($kasBank->saldo, 0, ',', '.') }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('kas_bank')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500">Pilih akun kas atau bank untuk pembayaran ini</p>
                            </div>

                            <!-- Debit/Kredit Field -->
                            <div>
                                <label for="jenis_transaksi" class="block text-sm font-medium text-gray-700 mb-2">
                                    Jenis Transaksi <span class="text-red-500">*</span>
                                </label>
                                <select name="jenis_transaksi"
                                        id="jenis_transaksi"
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('jenis_transaksi') border-red-300 @enderror">
                                    <option value="">-- Pilih Jenis Transaksi --</option>
                                    <option value="debit" {{ old('jenis_transaksi') == 'debit' ? 'selected' : '' }}>
                                        <i class="fas fa-plus-circle"></i> Debit (Menambah Saldo)
                                    </option>
                                    <option value="kredit" {{ old('jenis_transaksi', 'kredit') == 'kredit' ? 'selected' : '' }}>
                                        <i class="fas fa-minus-circle"></i> Kredit (Mengurangi Saldo)
                                    </option>
                                </select>
                                @error('jenis_transaksi')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p id="jenis-transaksi-help" class="mt-1 text-sm text-gray-500">Untuk pembayaran Uang Muka biasanya kredit (uang keluar)</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Kegiatan Field -->
                            <div>
                                <label for="kegiatan" class="block text-sm font-medium text-gray-700 mb-2">
                                    Kegiatan <span class="text-red-500">*</span>
                                </label>
                                <select name="kegiatan"
                                        id="kegiatan"
                                        required
                                        onchange="handleKegiatanChange()"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('kegiatan') border-red-300 @enderror">
                                    <option value="">-- Pilih Kegiatan --</option>
                                    @foreach($kegiatanList as $kegiatan)
                                        <option value="{{ $kegiatan->id }}"
                                                data-nama="{{ $kegiatan->nama_kegiatan }}"
                                                {{ old('kegiatan') == $kegiatan->id ? 'selected' : '' }}>
                                            {{ $kegiatan->kode_kegiatan }} - {{ $kegiatan->nama_kegiatan }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kegiatan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500">Pilih kegiatan untuk pembayaran uang muka ini</p>
                            </div>

                            <!-- Mobil Field (Only for KIR & STNK) -->
                            <div id="mobil-field" style="display: none;">
                                <label for="mobil_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Pilih Mobil <span class="text-red-500">*</span>
                                </label>
                                <select name="mobil_id"
                                        id="mobil_id"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('mobil_id') border-red-300 @enderror">
                                    <option value="">-- Pilih Mobil --</option>
                                    @foreach($mobilList as $mobil)
                                        <option value="{{ $mobil->id }}" {{ old('mobil_id') == $mobil->id ? 'selected' : '' }}>
                                            {{ $mobil->plat }}
                                            @if($mobil->aktiva)
                                                - {{ $mobil->aktiva }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('mobil_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500">Pilih mobil untuk KIR & STNK</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Penerima Field (For KIR & STNK, Amprahan, and others) -->
                            <div id="penerima-field" style="display: none;">
                                <label for="penerima_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Penerima <span class="text-red-500">*</span>
                                </label>
                                <select name="penerima_id"
                                        id="penerima_id"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('penerima_id') border-red-300 @enderror">
                                    <option value="">-- Pilih Penerima --</option>
                                    @foreach($karyawanList as $karyawan)
                                        <option value="{{ $karyawan->id }}" {{ old('penerima_id') == $karyawan->id ? 'selected' : '' }}>
                                            {{ $karyawan->nama_lengkap }}
                                            @if($karyawan->divisi)
                                                - {{ $karyawan->divisi }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('penerima_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500">Pilih karyawan yang menerima uang muka</p>
                            </div>

                            <!-- Jumlah Penerima (Only for Amprahan and other general activities) -->
                            <div id="jumlah-penerima-field" style="display: none;">
                                <label for="jumlah_penerima" class="block text-sm font-medium text-gray-700 mb-2">
                                    Jumlah Uang Muka <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">Rp</span>
                                    </div>
                                    <input type="text"
                                           name="jumlah_penerima_display"
                                           id="jumlah_penerima_display"
                                           value="{{ old('jumlah_penerima') ? number_format(old('jumlah_penerima'), 0, ',', '.') : '' }}"
                                           placeholder="0"
                                           class="w-full pl-12 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('jumlah_penerima') border-red-300 @enderror"
                                           oninput="formatCurrencyPenerima(this)">
                                    <input type="hidden"
                                           name="jumlah_penerima"
                                           id="jumlah_penerima"
                                           value="{{ old('jumlah_penerima', '') }}">
                                </div>
                                @error('jumlah_penerima')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500">Masukkan jumlah uang muka untuk penerima</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Jumlah Mobil (Only for KIR & STNK) -->
                            <div id="jumlah-mobil-field" style="display: none;">
                                <label for="jumlah_mobil" class="block text-sm font-medium text-gray-700 mb-2">
                                    Jumlah Uang Muka untuk Mobil <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">Rp</span>
                                    </div>
                                    <input type="text"
                                           name="jumlah_mobil_display"
                                           id="jumlah_mobil_display"
                                           value="{{ old('jumlah_mobil') ? number_format(old('jumlah_mobil'), 0, ',', '.') : '' }}"
                                           placeholder="0"
                                           class="w-full pl-12 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('jumlah_mobil') border-red-300 @enderror"
                                           oninput="formatCurrencyMobil(this)">
                                    <input type="hidden"
                                           name="jumlah_mobil"
                                           id="jumlah_mobil"
                                           value="{{ old('jumlah_mobil', '') }}">
                                </div>
                                @error('jumlah_mobil')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500">Masukkan jumlah uang muka untuk mobil</p>
                            </div>

                            <!-- Supir (Hidden for KIR & STNK) -->
                            <div id="supir-field">
                                <label for="supir" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Supir <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="border border-gray-300 rounded-md bg-white @error('supir') border-red-300 @enderror">
                                        <div class="p-3">
                                            <div class="flex flex-wrap gap-2 mb-2" id="selected-supir-tags">
                                                <!-- Selected supir tags will appear here -->
                                            </div>
                                            <button type="button"
                                                    id="supir-dropdown-toggle"
                                                    class="w-full text-left text-gray-500 bg-gray-50 hover:bg-gray-100 px-3 py-2 rounded border text-sm">
                                                <i class="fas fa-plus mr-2"></i>Pilih Supir...
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Dropdown menu -->
                                    <div id="supir-dropdown-menu" class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg hidden">
                                        <div class="max-h-48 overflow-y-auto">
                                            @foreach($supirList as $supir)
                                                <label class="flex items-center px-3 py-2 hover:bg-gray-100 cursor-pointer">
                                                    <input type="checkbox"
                                                           name="supir[]"
                                                           value="{{ $supir->id }}"
                                                           class="supir-checkbox mr-3 text-blue-600"
                                                           {{ in_array($supir->id, old('supir', [])) ? 'checked' : '' }}>
                                                    <div class="flex-1">
                                                        <div class="font-medium text-gray-900">{{ $supir->nama_lengkap }}</div>
                                                        <div class="text-sm text-gray-500">NIK: {{ $supir->nik }}</div>
                                                    </div>
                                                </label>
                                            @endforeach

                                            @if($supirList->isEmpty())
                                                <div class="px-3 py-2 text-gray-500 text-sm">
                                                    Tidak ada supir aktif tersedia
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                @error('supir')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                @error('supir.*')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500">Pilih satu atau lebih supir dari daftar karyawan aktif</p>
                            </div>

                            <!-- Jumlah Pembayaran per Supir (Hidden for KIR & STNK) -->
                            <div id="jumlah-container">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Jumlah Uang Muka per Supir <span class="text-red-500">*</span>
                                </label>
                                <div id="jumlah-inputs">
                                    <!-- Dynamic inputs akan ditambahkan di sini -->
                                </div>
                                <div id="no-supir-message" class="text-gray-500 text-sm italic">
                                    Pilih supir terlebih dahulu untuk mengisi jumlah Uang Muka
                                </div>
                            </div>
                        </div>

                        <!-- Keterangan -->
                        <div>
                            <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                                Keterangan
                            </label>
                            <textarea name="keterangan"
                                      id="keterangan"
                                      rows="4"
                                      placeholder="Masukkan keterangan tambahan (opsional)"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('keterangan') border-red-300 @enderror">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                            <a href="{{ route('pembayaran-uang-muka.index') }}"
                               class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                                <i class="fas fa-times mr-1"></i> Batal
                            </a>
                            <button type="submit"
                                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                                <i class="fas fa-save mr-1"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Handle kegiatan change untuk show/hide fields
        function handleKegiatanChange() {
            const kegiatanSelect = document.getElementById('kegiatan');
            const kegiatanText = kegiatanSelect.options[kegiatanSelect.selectedIndex].text.toLowerCase();

            // Get field containers
            const supirField = document.getElementById('supir-field');
            const jumlahContainer = document.getElementById('jumlah-container');
            const mobilField = document.getElementById('mobil-field');
            const penerimaField = document.getElementById('penerima-field');
            const jumlahMobilField = document.getElementById('jumlah-mobil-field');
            const jumlahPenerimaField = document.getElementById('jumlah-penerima-field');

            // Reset field visibility - hide all conditional fields by default
            supirField.style.display = 'none';
            jumlahContainer.style.display = 'none';
            mobilField.style.display = 'none';
            penerimaField.style.display = 'none';
            jumlahMobilField.style.display = 'none';
            jumlahPenerimaField.style.display = 'none';

            // Show/hide fields based on kegiatan
            if (kegiatanText.includes('kir') && kegiatanText.includes('stnk')) {
                // For KIR & STNK, show mobil + penerima + jumlah mobil
                mobilField.style.display = 'block';
                penerimaField.style.display = 'block';
                jumlahMobilField.style.display = 'block';
            } else if (kegiatanText.includes('ob muat') || kegiatanText.includes('ob bongkar') ||
                       kegiatanText.includes('muat') || kegiatanText.includes('bongkar')) {
                // For OB Muat/Bongkar, show supir fields
                supirField.style.display = 'block';
                jumlahContainer.style.display = 'block';
            } else if (kegiatanText.includes('amprahan') ||
                       (!kegiatanText.includes('kir') && !kegiatanText.includes('stnk') &&
                        !kegiatanText.includes('muat') && !kegiatanText.includes('bongkar') &&
                        kegiatanText !== '-- pilih kegiatan --' && kegiatanText !== '')) {
                // For Amprahan and other general activities, show penerima + jumlah penerima
                penerimaField.style.display = 'block';
                jumlahPenerimaField.style.display = 'block';
            }
        }

// Format currency untuk mobil
function formatCurrencyMobil(input) {
    // Ambil nilai tanpa format
    let value = input.value.replace(/[^\d]/g, '');

    // Update hidden input dengan nilai asli
    const hiddenInput = document.getElementById('jumlah_mobil');
    if (hiddenInput) {
        hiddenInput.value = value || '0';
    }

    // Format tampilan dengan pemisah ribuan
    if (value) {
        input.value = formatNumber(value);
    } else {
        input.value = '';
    }
}

// Format currency untuk penerima
function formatCurrencyPenerima(input) {
    // Ambil nilai tanpa format
    let value = input.value.replace(/[^\d]/g, '');

    // Update hidden input dengan nilai asli
    const hiddenInput = document.getElementById('jumlah_penerima');
    if (hiddenInput) {
        hiddenInput.value = value || '0';
    }

    // Format tampilan dengan pemisah ribuan
    if (value) {
        input.value = formatNumber(value);
    } else {
        input.value = '';
    }
}

// Auto generate nomor pembayaran
async function generateNomor() {
    try {
        // Ambil kas_bank_id yang dipilih untuk generate nomor yang sesuai
        const kasBankId = document.getElementById('kas_bank').value;

        console.log('Generate nomor called, kas_bank_id:', kasBankId);

        if (!kasBankId) {
            alert('Pilih akun Kas/Bank terlebih dahulu untuk generate nomor pembayaran');
            throw new Error('Kas/Bank not selected');
        }

        // Buat URL dengan parameter kas_bank_id
        let url = '{{ route('pembayaran-uang-muka.generate-nomor') }}';
        url += '?kas_bank_id=' + kasBankId;

        console.log('Calling URL:', url);

        const response = await fetch(url);
        console.log('Response status:', response.status);

        const data = await response.json();
        console.log('Response data:', data);

        if (data.success && data.nomor_pembayaran) {
            document.getElementById('nomor_pembayaran').value = data.nomor_pembayaran;
            console.log('Nomor generated successfully: ' + data.nomor_pembayaran);
            return data.nomor_pembayaran;
        } else if (data.message) {
            console.error('Failed to generate nomor:', data.message);
            throw new Error(data.message);
        } else {
            console.error('Failed to generate nomor: Unexpected response format', data);
            throw new Error('Unexpected response format');
        }
    } catch (error) {
        console.error('Error generating nomor:', error);
        alert('Error: ' + error.message + '. Menggunakan fallback nomor.');

        // Fallback generate nomor secara manual dengan format baru
        const today = new Date();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const year = String(today.getFullYear()).slice(-2); // 2 digit tahun
        const random = Math.floor(Math.random() * 1000000).toString().padStart(6, '0');

        // Format: KBJ-MM-YY-NNNNNN (default COA KBJ)
        const fallbackNomor = `KBJ-${month}-${year}-${random}`;
        document.getElementById('nomor_pembayaran').value = fallbackNomor;
        console.log('Using fallback nomor: ' + fallbackNomor);
        return fallbackNomor;
    }
}



// Auto focus next field on Enter
document.querySelectorAll('input, textarea').forEach(function(input, index, inputs) {
    input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && e.target.type !== 'textarea') {
            e.preventDefault();
            let nextIndex = index + 1;
            if (nextIndex < inputs.length) {
                inputs[nextIndex].focus();
            } else {
                // Submit form if this is the last input
                document.querySelector('form').submit();
            }
        }
    });
});

// Multi-select Supir Dropdown functionality
let selectedSupir = [];

// Load previously selected supir from old input
@if(old('supir'))
    selectedSupir = @json(old('supir'));
    updateSupirDisplay();
@endif

function toggleSupirDropdown() {
    const menu = document.getElementById('supir-dropdown-menu');
    menu.classList.toggle('hidden');
}

function updateSupirDisplay() {
    const tagsContainer = document.getElementById('selected-supir-tags');
    const toggleButton = document.getElementById('supir-dropdown-toggle');
    const jumlahInputsContainer = document.getElementById('jumlah-inputs');
    const noSupirMessage = document.getElementById('no-supir-message');

    tagsContainer.innerHTML = '';
    jumlahInputsContainer.innerHTML = '';

    if (selectedSupir.length === 0) {
        toggleButton.innerHTML = '<i class="fas fa-plus mr-2"></i>Pilih Supir...';
        toggleButton.className = 'w-full text-left text-gray-500 bg-gray-50 hover:bg-gray-100 px-3 py-2 rounded border text-sm';
        noSupirMessage.style.display = 'block';
    } else {
        toggleButton.innerHTML = '<i class="fas fa-plus mr-2"></i>Tambah Supir...';
        toggleButton.className = 'w-full text-left text-blue-600 bg-blue-50 hover:bg-blue-100 px-3 py-2 rounded border border-blue-200 text-sm';
        noSupirMessage.style.display = 'none';

        // Create tags for selected supir
        selectedSupir.forEach(function(supirId) {
            const checkbox = document.querySelector(`input[value="${supirId}"]`);
            if (checkbox) {
                const label = checkbox.closest('label');
                const namaLengkap = label.querySelector('.font-medium').textContent;
                const nik = label.querySelector('.text-gray-500').textContent.replace('NIK: ', '');

                const tag = document.createElement('div');
                tag.className = 'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800';
                tag.innerHTML = `
                    ${namaLengkap} (${nik})
                    <button type="button" onclick="removeSupir('${supirId}')" class="ml-2 text-blue-600 hover:text-blue-800">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                `;
                tagsContainer.appendChild(tag);

                // Create input field untuk setiap supir
                const inputDiv = document.createElement('div');
                inputDiv.className = 'mb-3 p-3 border border-gray-200 rounded-md bg-gray-50';
                inputDiv.innerHTML = `
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        ${namaLengkap} (${nik}) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">Rp</span>
                        </div>
                        <input type="text"
                               name="jumlah_display[${supirId}]"
                               id="jumlah_display_${supirId}"
                               value="${formatNumber(getOldJumlah(supirId))}"
                               placeholder="0"
                               required
                               class="w-full pl-12 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               oninput="formatCurrency(this, ${supirId})">
                        <input type="hidden"
                               name="jumlah[${supirId}]"
                               id="jumlah_${supirId}"
                               value="${getOldJumlah(supirId)}">
                    </div>
                `;
                jumlahInputsContainer.appendChild(inputDiv);
            }
        });
    }
}

function removeSupir(supirId) {
    const index = selectedSupir.indexOf(supirId);
    if (index > -1) {
        selectedSupir.splice(index, 1);

        // Uncheck the checkbox
        const checkbox = document.querySelector(`input[value="${supirId}"]`);
        if (checkbox) {
            checkbox.checked = false;
        }

        updateSupirDisplay();
    }
}

// Handle supir dropdown toggle
document.getElementById('supir-dropdown-toggle').addEventListener('click', toggleSupirDropdown);

// Handle checkbox changes
document.querySelectorAll('.supir-checkbox').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
        const supirId = this.value;

        if (this.checked) {
            if (!selectedSupir.includes(supirId)) {
                selectedSupir.push(supirId);
            }
        } else {
            const index = selectedSupir.indexOf(supirId);
            if (index > -1) {
                selectedSupir.splice(index, 1);
            }
        }

        updateSupirDisplay();
    });
});

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('supir-dropdown-menu');
    const toggle = document.getElementById('supir-dropdown-toggle');
    const container = dropdown.closest('.relative');

    if (!container.contains(e.target)) {
        dropdown.classList.add('hidden');
    }
});

// Function to get old jumlah value untuk supir tertentu
function getOldJumlah(supirId) {
    @if(old('jumlah'))
        const oldJumlah = @json(old('jumlah'));
        return oldJumlah[supirId] || '';
    @else
        return '';
    @endif
}

// Function untuk format number dengan pemisah ribuan
function formatNumber(num) {
    if (!num || num === '') return '';
    return new Intl.NumberFormat('id-ID').format(num);
}

// Function untuk format currency input
function formatCurrency(input, supirId) {
    // Ambil nilai tanpa format
    let value = input.value.replace(/[^\d]/g, '');

    // Update hidden input dengan nilai asli
    const hiddenInput = document.getElementById(`jumlah_${supirId}`);
    if (hiddenInput) {
        hiddenInput.value = value || '0';
    }

    // Format tampilan dengan pemisah ribuan
    if (value) {
        input.value = formatNumber(value);
    } else {
        input.value = '';
    }
}

// Handle jenis transaksi selection
document.getElementById('jenis_transaksi').addEventListener('change', function(e) {
    const jenisTransaksi = e.target.value;
    const helpText = document.getElementById('jenis-transaksi-help');

    if (helpText) {
        if (jenisTransaksi === 'debit') {
            helpText.textContent = 'Debit: Uang masuk ke akun kas/bank (menambah saldo)';
            helpText.className = 'mt-1 text-sm text-green-600';
        } else if (jenisTransaksi === 'kredit') {
            helpText.textContent = 'Kredit: Uang keluar dari akun kas/bank (mengurangi saldo)';
            helpText.className = 'mt-1 text-sm text-red-600';
        } else {
            helpText.textContent = 'Untuk pembayaran Uang Muka biasanya kredit (uang keluar)';
            helpText.className = 'mt-1 text-sm text-gray-500';
        }
    }
});

// Handle kas/bank selection - no longer auto-generate nomor
document.getElementById('kas_bank').addEventListener('change', function(e) {
    // No auto-generation, user can manually click Auto button if needed
    console.log('Kas/Bank changed to:', e.target.value);
});

// Handle paste event untuk input jumlah
document.addEventListener('paste', function(e) {
    if (e.target.name && e.target.name.includes('jumlah_display')) {
        setTimeout(function() {
            const supirId = e.target.id.replace('jumlah_display_', '');
            formatCurrency(e.target, supirId);
        }, 10);
    }
});

// Validate form before submit
document.querySelector('form').addEventListener('submit', async function(e) {
    console.log('Form submit event triggered');

    // Generate nomor if empty before validation
    const nomorField = document.getElementById('nomor_pembayaran');
    console.log('Current nomor field value:', nomorField.value);

    if (!nomorField.value.trim()) {
        console.log('Nomor field is empty, attempting to generate...');
        e.preventDefault(); // Prevent form submission temporarily

        try {
            const generatedNomor = await generateNomor(); // Generate nomor first
            console.log('Nomor generated successfully:', generatedNomor);

            // After nomor is generated, submit the form again
            setTimeout(() => {
                console.log('Resubmitting form after nomor generation');
                this.submit(); // Resubmit form after nomor is generated
            }, 100);

            return false; // Stop current submission
        } catch (error) {
            console.error('Error generating nomor before submit:', error);
            alert('Gagal generate nomor pembayaran. Silakan isi nomor secara manual atau coba lagi.');
            return false;
        }
    } else {
        console.log('Nomor field has value, continuing with normal validation');
    }

    const kegiatanSelect = document.getElementById('kegiatan');
    const selectedOption = kegiatanSelect.options[kegiatanSelect.selectedIndex];
    const namaKegiatan = selectedOption.getAttribute('data-nama') || '';
    const kegiatanText = namaKegiatan.toLowerCase();

    const isKirStnk = kegiatanText.includes('kir') && kegiatanText.includes('stnk');
    const isObMuatBongkar = kegiatanText.includes('ob muat') || kegiatanText.includes('ob bongkar') ||
                           kegiatanText.includes('muat') || kegiatanText.includes('bongkar');
    const isAmprahanOrOthers = kegiatanText.includes('amprahan') ||
                               (!kegiatanText.includes('kir') && !kegiatanText.includes('stnk') &&
                                !kegiatanText.includes('muat') && !kegiatanText.includes('bongkar') &&
                                kegiatanText !== '-- pilih kegiatan --' && kegiatanText !== '');

    // Clear unused fields before validation
    if (isKirStnk) {
        // Clear supir and jumlah penerima fields for KIR & STNK
        document.querySelectorAll('input[name^="supir"]').forEach(input => input.remove());
        document.querySelectorAll('input[name^="jumlah["]').forEach(input => input.remove());
        const jumlahPenerimaField = document.getElementById('jumlah_penerima');
        if (jumlahPenerimaField) jumlahPenerimaField.value = '';

        // Validate mobil fields for KIR & STNK
        const mobilId = document.getElementById('mobil_id').value;
        const jumlahMobil = document.getElementById('jumlah_mobil').value;
        const penerimaId = document.getElementById('penerima_id').value;

        if (!mobilId) {
            e.preventDefault();
            alert('Harap pilih mobil untuk KIR & STNK');
            return false;
        }

        if (!penerimaId) {
            e.preventDefault();
            alert('Harap pilih penerima untuk KIR & STNK');
            return false;
        }

        if (!jumlahMobil || jumlahMobil === '0') {
            e.preventDefault();
            alert('Harap isi jumlah uang muka untuk mobil');
            return false;
        }
    } else if (isObMuatBongkar) {
        // Clear mobil and penerima fields for OB Muat/Bongkar
        const mobilField = document.getElementById('mobil_id');
        if (mobilField) mobilField.value = '';
        const penerimaField = document.getElementById('penerima_id');
        if (penerimaField) penerimaField.value = '';
        const jumlahMobilField = document.getElementById('jumlah_mobil');
        if (jumlahMobilField) jumlahMobilField.value = '';
        const jumlahPenerimaField = document.getElementById('jumlah_penerima');
        if (jumlahPenerimaField) jumlahPenerimaField.value = '';

        // Validate supir fields for OB Muat/Bongkar
        const hiddenInputs = document.querySelectorAll('input[name^="jumlah["]');
        let hasEmptyAmount = false;

        if (hiddenInputs.length === 0) {
            e.preventDefault();
            alert('Harap pilih minimal satu supir untuk OB Muat/Bongkar');
            return false;
        }

        hiddenInputs.forEach(function(input) {
            if (!input.value || input.value === '0' || input.value === '') {
                hasEmptyAmount = true;
            }
        });

        if (hasEmptyAmount) {
            e.preventDefault();
            alert('Harap isi semua jumlah Uang Muka untuk setiap supir yang dipilih');
            return false;
        }
    } else if (isAmprahanOrOthers) {
        // Clear supir and mobil fields for Amprahan/Others
        console.log('Clearing supir fields for Amprahan/Others');
        document.querySelectorAll('input[name^="supir"]').forEach(input => {
            console.log('Removing supir input:', input.name, input.value);
            input.remove();
        });
        document.querySelectorAll('input[name^="jumlah["]').forEach(input => {
            console.log('Removing jumlah input:', input.name, input.value);
            input.remove();
        });

        // Clear supir checkboxes to make sure they're not checked
        document.querySelectorAll('.supir-checkbox').forEach(checkbox => {
            if (checkbox.checked) {
                console.log('Unchecking supir checkbox:', checkbox.value);
                checkbox.checked = false;
            }
        });

        const mobilField = document.getElementById('mobil_id');
        if (mobilField) {
            console.log('Clearing mobil_id field');
            mobilField.value = '';
        }
        const jumlahMobilField = document.getElementById('jumlah_mobil');
        if (jumlahMobilField) {
            console.log('Clearing jumlah_mobil field');
            jumlahMobilField.value = '';
        }

        // Validate penerima fields for Amprahan and other activities
        const penerimaId = document.getElementById('penerima_id').value;
        const jumlahPenerima = document.getElementById('jumlah_penerima').value;

        if (!penerimaId) {
            e.preventDefault();
            alert('Harap pilih penerima untuk kegiatan ini');
            return false;
        }

        if (!jumlahPenerima || jumlahPenerima === '0') {
            e.preventDefault();
            alert('Harap isi jumlah uang muka untuk penerima');
            return false;
        }
    }

    // Debug: log form data before submit
    console.log('Form data before submit:');
    const formData = new FormData(this);
    for (let [key, value] of formData.entries()) {
        if (key.startsWith('jumlah') || key === 'mobil_id' || key === 'penerima_id') {
            console.log(key + ': ' + value);
        }
    }
});

// Initialize page without auto-generating nomor
document.addEventListener('DOMContentLoaded', function() {
    // Don't auto-generate nomor on page load
    // User should click Auto button manually or nomor will be generated on submit

    // Initialize supir display
    updateSupirDisplay();

    // Handle initial kegiatan selection
    handleKegiatanChange();
});
</script>

@endsection
