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
                    <p class="mt-2 text-gray-600">Form untuk membuat pembayaran Out Bound (OB) baru</p>
                </div>

                <div class="flex space-x-3">
                    <a href="{{ route('pembayaran-ob.index') }}"
                       class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </div>

            <!-- Form Pembayaran OB -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <form action="{{ route('pembayaran-ob.store') }}" method="POST" class="space-y-6">
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
                                    Nomor Pembayaran <span class="text-red-500">*</span>
                                </label>
                                <div class="flex">
                                    <input type="text"
                                           name="nomor_pembayaran"
                                           id="nomor_pembayaran"
                                           value="{{ old('nomor_pembayaran') }}"
                                           placeholder="KBJ1025000001"
                                           required
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
                                <p class="mt-1 text-sm text-gray-500">Format: Kode_COA + Bulan(2) + Tahun(2) + Urutan(6). Pilih kas/bank untuk generate otomatis.</p>
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
                                <p id="jenis-transaksi-help" class="mt-1 text-sm text-gray-500">Untuk pembayaran biasanya kredit (uang keluar)</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Supir -->
                            <div>
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

                            <!-- Realisasi Pembayaran per Supir -->
                            <div id="jumlah-container">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Realisasi Pembayaran per Supir <span class="text-red-500">*</span>
                                </label>
                                <p class="text-sm text-gray-600 mb-3">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Masukkan jumlah realisasi pembayaran untuk setiap supir. Sistem akan menghitung selisih dengan DP yang dipilih.
                                </p>
                                <div id="jumlah-inputs">
                                    <!-- Dynamic inputs akan ditambahkan di sini -->
                                </div>
                                <div id="no-supir-message" class="text-gray-500 text-sm italic">
                                    Pilih supir terlebih dahulu untuk mengisi realisasi pembayaran
                                </div>

                                <!-- Total Calculation Display -->
                                <div id="total-calculation" class="mt-4 p-4 bg-gradient-to-r from-blue-50 to-green-50 border border-blue-200 rounded-lg hidden">
                                    <h4 class="text-sm font-bold text-gray-800 mb-3 flex items-center">
                                        <i class="fas fa-calculator mr-2"></i>
                                        Ringkasan Realisasi vs DP
                                    </h4>
                                    <div class="space-y-3">
                                        <!-- Total Realisasi -->
                                        <div class="flex items-center justify-between p-2 bg-white rounded border">
                                            <div>
                                                <span class="text-sm text-gray-700">
                                                    <i class="fas fa-receipt mr-1"></i>
                                                    Total Realisasi (<span id="jumlah-supir">0</span> supir)
                                                </span>
                                            </div>
                                            <div>
                                                <span class="text-sm font-semibold text-gray-900">
                                                    Rp <span id="subtotal-pembayaran">0</span>
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <!-- DP Row -->
                                        <div id="dp-reduction-row" class="items-center justify-between p-2 bg-white rounded border hidden">
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm text-green-600">
                                                    <i class="fas fa-money-bill-wave mr-1"></i>
                                                    Total DP: <span id="dp-amount-text">Rp 0</span>
                                                </span>
                                                <span class="text-sm font-medium text-green-600">
                                                    Rp <span id="dp-amount">0</span>
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <!-- Selisih Breakdown -->
                                        <div id="selisih-breakdown" class="hidden">
                                            <div class="text-xs text-gray-600 mb-2 font-medium">Breakdown Selisih:</div>
                                            <div id="breakdown-items" class="space-y-1 text-xs">
                                                <!-- Dynamic breakdown items akan ditambahkan di sini -->
                                            </div>
                                        </div>

                                        <!-- Final Total Row -->
                                        <div class="flex items-center justify-between border-t-2 border-blue-300 pt-3 bg-gradient-to-r from-blue-100 to-green-100 rounded-lg px-3 py-2">
                                            <div>
                                                <span class="text-sm font-bold text-gray-800">
                                                    <i class="fas fa-hand-holding-usd mr-1"></i>
                                                    Total Bayar (Realisasi - DP):
                                                </span>
                                            </div>
                                            <div>
                                                <span class="text-lg font-bold text-blue-900">
                                                    Rp <span id="total-final-pembayaran">0</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
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

                        <!-- DP Selection Field -->
                        <div>
                            <label for="pembayaran_dp_ob_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Pilih DP yang Akan Digunakan <span class="text-gray-500">(Opsional)</span>
                            </label>
                            <select name="pembayaran_dp_ob_id"
                                    id="pembayaran_dp_ob_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('pembayaran_dp_ob_id') border-red-300 @enderror">
                                <option value="">-- Tidak Menggunakan DP --</option>
                                @foreach($dpBelumTerpakaiList as $dp)
                                    <option value="{{ $dp->id }}" {{ old('pembayaran_dp_ob_id') == $dp->id ? 'selected' : '' }}>
                                        {{ $dp->nomor_pembayaran }} - {{ \Carbon\Carbon::parse($dp->tanggal_pembayaran)->format('d/m/Y') }} -
                                        {{ count($dp->supir_ids) }} supir -
                                        Rp {{ number_format($dp->total_pembayaran, 0, ',', '.') }}
                                        @if($dp->keterangan)
                                            - {{ Str::limit($dp->keterangan, 30) }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('pembayaran_dp_ob_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            <!-- DP Selection Info -->
                            <div id="dp-selection-info" class="mt-2 p-3 bg-green-50 border border-green-200 rounded-md hidden">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                                    </div>
                                    <div class="ml-2">
                                        <div class="text-sm font-medium text-green-800">DP Dipilih:</div>
                                        <div class="text-sm text-green-700">
                                            <span id="selected-dp-info">-</span>
                                        </div>
                                        <div class="text-xs text-green-600 mt-1">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            DP ini akan dipotongkan dari total pembayaran dan statusnya akan berubah menjadi "Terpakai"
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($dpBelumTerpakaiList->count() > 0)
                                <p class="mt-1 text-sm text-gray-500">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Pilih DP yang akan digunakan untuk pembayaran ini. DP yang dipilih akan diubah statusnya menjadi "Terpakai"
                                </p>
                            @else
                                <p class="mt-1 text-sm text-yellow-600">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    Tidak ada DP yang tersedia atau semua DP sudah terpakai
                                </p>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                            <a href="{{ route('pembayaran-ob.index') }}"
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
// DP Data untuk JavaScript
const dpData = {
    @foreach($dpBelumTerpakaiList as $dp)
        '{{ $dp->id }}': {
            nomor: '{{ $dp->nomor_pembayaran }}',
            total: {{ $dp->total_pembayaran }},
            supir_count: {{ count($dp->supir_ids) }},
            tanggal: '{{ \Carbon\Carbon::parse($dp->tanggal_pembayaran)->format('d/m/Y') }}',
            supir_names: @json($dp->supir_names ?? []),
            supir_ids: @json($dp->supir_ids ?? []),
            jumlah_per_supir: @json($dp->jumlah_per_supir ?? [])
        },
    @endforeach
};

// Auto generate nomor pembayaran
async function generateNomor() {
    try {
        // Ambil kas_bank_id yang dipilih untuk generate nomor yang sesuai
        const kasBankId = document.getElementById('kas_bank').value;

        if (!kasBankId) {
            alert('Pilih akun Kas/Bank terlebih dahulu untuk generate nomor pembayaran');
            return;
        }

        // Buat URL dengan parameter kas_bank_id
        let url = '{{ route('pembayaran-ob.generate-nomor') }}';
        url += '?kas_bank_id=' + kasBankId;

        const response = await fetch(url);
        const data = await response.json();

        if (data.nomor_pembayaran) {
            document.getElementById('nomor_pembayaran').value = data.nomor_pembayaran;
            console.log('Nomor generated: ' + data.nomor_pembayaran);
        } else if (data.error) {
            console.error('Failed to generate nomor:', data.message || data.error);
            alert('Error: ' + (data.message || data.error));
        } else {
            console.error('Failed to generate nomor: Unexpected response format');
            alert('Error: Unexpected response format');
        }
    } catch (error) {
        console.error('Error generating nomor:', error);
        alert('Terjadi kesalahan saat generate nomor. Menggunakan nomor default.');

        // Fallback generate nomor secara manual dengan format baru
        const today = new Date();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const year = String(today.getFullYear()).slice(-2); // 2 digit tahun
        const random = Math.floor(Math.random() * 1000000).toString().padStart(6, '0');

        // Format: KBJ-MM-YY-NNNNNN (default COA KBJ)
        document.getElementById('nomor_pembayaran').value = `KBJ-${month}-${year}-${random}`;
    }
}



// Auto focus will be set up in DOMContentLoaded

// Multi-select Supir Dropdown functionality
let selectedSupir = [];

// Load previously selected supir from old input (will be initialized in DOMContentLoaded)
@if(old('supir'))
    // Will be loaded in DOMContentLoaded
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

    // Check if elements exist
    if (!tagsContainer || !toggleButton) {
        console.log('Supir display elements not found, skipping update');
        return;
    }

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

                // Get DP info for this supir
                const selectedDpId = document.getElementById('pembayaran_dp_ob_id').value;
                let dpAmount = 0;
                let dpInfo = '';
                
                if (selectedDpId && dpData[selectedDpId] && dpData[selectedDpId].jumlah_per_supir && dpData[selectedDpId].jumlah_per_supir[supirId]) {
                    dpAmount = dpData[selectedDpId].jumlah_per_supir[supirId];
                    dpInfo = `
                        <div class="mb-2 p-2 bg-green-50 border border-green-200 rounded text-xs">
                            <i class="fas fa-money-bill-wave mr-1 text-green-600"></i>
                            <span class="text-green-700">DP: Rp ${formatNumber(dpAmount)}</span>
                        </div>
                    `;
                } else {
                    dpInfo = `
                        <div class="mb-2 p-2 bg-yellow-50 border border-yellow-200 rounded text-xs">
                            <i class="fas fa-user-plus mr-1 text-yellow-600"></i>
                            <span class="text-yellow-700">Supir Tambahan (Tanpa DP)</span>
                        </div>
                    `;
                }

                // Create input field untuk setiap supir
                const inputDiv = document.createElement('div');
                inputDiv.className = 'mb-3 p-3 border border-gray-200 rounded-md bg-gray-50';
                inputDiv.innerHTML = `
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        ${namaLengkap} (${nik}) <span class="text-red-500">*</span>
                    </label>
                    ${dpInfo}
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">Rp</span>
                        </div>
                        <input type="text"
                               name="jumlah_display[${supirId}]"
                               id="jumlah_display_${supirId}"
                               value="${formatNumber(getOldJumlah(supirId))}"
                               placeholder="Masukkan realisasi pembayaran"
                               required
                               class="w-full pl-12 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               oninput="formatCurrency(this, ${supirId})">
                        <input type="hidden"
                               name="jumlah[${supirId}]"
                               id="jumlah_${supirId}"
                               value="${getOldJumlah(supirId)}">
                    </div>
                    <div id="selisih_${supirId}" class="mt-2 text-xs hidden">
                        <!-- Selisih akan ditampilkan di sini -->
                    </div>
                `;
                jumlahInputsContainer.appendChild(inputDiv);
            }
        });
    }

    // Update total calculation
    updateTotalCalculation();
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

// These event listeners will be set up in DOMContentLoaded

// Function to auto-select supir dari DP yang dipilih
function autoSelectSupirFromDp(dpId) {
    if (!dpId || !dpData[dpId] || !dpData[dpId].supir_ids) {
        return;
    }

    const dpSupirIds = dpData[dpId].supir_ids.map(id => String(id)); // Convert to string for comparison
    const dpJumlahPerSupir = dpData[dpId].jumlah_per_supir || {};

    // Clear existing selections first
    selectedSupir = [];

    // Uncheck all checkboxes first
    document.querySelectorAll('.supir-checkbox').forEach(function(checkbox) {
        checkbox.checked = false;
    });

    // Auto-select supir from DP
    dpSupirIds.forEach(function(supirId) {
        const checkbox = document.querySelector(`input[value="${supirId}"]`);
        if (checkbox) {
            checkbox.checked = true;
            if (!selectedSupir.includes(supirId)) {
                selectedSupir.push(supirId);
            }
        }
    });

    // Update display first to create input fields
    updateSupirDisplay();

    // Auto-fill jumlah berdasarkan data DP setelah delay untuk memastikan input sudah dibuat
    setTimeout(function() {
        dpSupirIds.forEach(function(supirId) {
            const displayInput = document.getElementById(`jumlah_display_${supirId}`);
            const hiddenInput = document.getElementById(`jumlah_${supirId}`);
            const dpAmount = dpJumlahPerSupir[supirId] || 0;
            
            if (displayInput && hiddenInput && dpAmount > 0) {
                hiddenInput.value = dpAmount;
                displayInput.value = formatNumber(dpAmount);
                
                // Update selisih display for this supir
                updateSelisihDisplay(supirId, dpAmount);
                
                console.log(`Auto-filled supir ${supirId} dengan jumlah: Rp ${formatNumber(dpAmount)}`);
            }
        });
        
        // Update calculation after auto-fill
        updateTotalCalculation();
    }, 100);

    console.log(`Auto-selected supir from DP: ${dpSupirIds.join(', ')}`);
}

// Function to clear auto-selected supir
function clearAutoSelectedSupir() {
    // Only clear if we don't have manual selections
    // This prevents clearing manually selected supir when no DP is selected
    console.log('DP cleared, keeping existing supir selections');
}

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
    
    // Calculate and display selisih untuk supir ini
    updateSelisihDisplay(supirId, parseInt(value) || 0);
    
    // Update total calculation
    updateTotalCalculation();
}

// Function to update selisih display untuk supir tertentu
function updateSelisihDisplay(supirId, realisasiAmount) {
    const selectedDpId = document.getElementById('pembayaran_dp_ob_id').value;
    const selisihDiv = document.getElementById(`selisih_${supirId}`);
    
    if (!selisihDiv) return;
    
    let dpAmount = 0;
    if (selectedDpId && dpData[selectedDpId] && dpData[selectedDpId].jumlah_per_supir && dpData[selectedDpId].jumlah_per_supir[supirId]) {
        dpAmount = dpData[selectedDpId].jumlah_per_supir[supirId];
    }
    
    if (dpAmount > 0 && realisasiAmount > 0) {
        const selisih = realisasiAmount - dpAmount;
        selisihDiv.classList.remove('hidden');
        
        if (selisih > 0) {
            // Realisasi > DP, kurang bayar
            selisihDiv.innerHTML = `
                <div class="flex items-center justify-between p-2 bg-red-50 border border-red-200 rounded">
                    <span class="text-red-700">
                        <i class="fas fa-arrow-up mr-1"></i>Kurang Bayar:
                    </span>
                    <span class="font-semibold text-red-800">Rp ${formatNumber(selisih)}</span>
                </div>
            `;
        } else if (selisih < 0) {
            // Realisasi < DP, sisa DP
            selisihDiv.innerHTML = `
                <div class="flex items-center justify-between p-2 bg-blue-50 border border-blue-200 rounded">
                    <span class="text-blue-700">
                        <i class="fas fa-arrow-down mr-1"></i>Sisa DP:
                    </span>
                    <span class="font-semibold text-blue-800">Rp ${formatNumber(Math.abs(selisih))}</span>
                </div>
            `;
        } else {
            // Pas
            selisihDiv.innerHTML = `
                <div class="flex items-center justify-between p-2 bg-green-50 border border-green-200 rounded">
                    <span class="text-green-700">
                        <i class="fas fa-check mr-1"></i>Pas (Sesuai DP)
                    </span>
                    <span class="font-semibold text-green-800">Rp 0</span>
                </div>
            `;
        }
    } else if (dpAmount === 0 && realisasiAmount > 0) {
        // Supir tambahan tanpa DP
        selisihDiv.classList.remove('hidden');
        selisihDiv.innerHTML = `
            <div class="flex items-center justify-between p-2 bg-yellow-50 border border-yellow-200 rounded">
                <span class="text-yellow-700">
                    <i class="fas fa-user-plus mr-1"></i>Bayar Penuh:
                </span>
                <span class="font-semibold text-yellow-800">Rp ${formatNumber(realisasiAmount)}</span>
            </div>
        `;
    } else {
        selisihDiv.classList.add('hidden');
    }
}

// Function to update total calculation display
function updateTotalCalculation() {
    const jumlahSupir = selectedSupir.length;
    
    // Calculate subtotal from individual inputs
    let subtotalPembayaran = 0;
    let breakdownItems = [];
    let totalKurangBayar = 0;
    let totalSisaDP = 0;
    let totalBayarPenuh = 0;
    
    selectedSupir.forEach(function(supirId) {
        const hiddenInput = document.getElementById(`jumlah_${supirId}`);
        const realisasiAmount = parseInt(hiddenInput.value) || 0;
        subtotalPembayaran += realisasiAmount;
        
        // Get supir info for breakdown
        const checkbox = document.querySelector(`input[value="${supirId}"]`);
        let supirName = 'Unknown';
        if (checkbox) {
            const label = checkbox.closest('label');
            const namaLengkap = label.querySelector('.font-medium').textContent;
            const nik = label.querySelector('.text-gray-500').textContent.replace('NIK: ', '');
            supirName = `${namaLengkap}`;
        }
        
        // Calculate individual breakdown
        const selectedDpId = document.getElementById('pembayaran_dp_ob_id').value;
        let dpAmount = 0;
        if (selectedDpId && dpData[selectedDpId] && dpData[selectedDpId].jumlah_per_supir && dpData[selectedDpId].jumlah_per_supir[supirId]) {
            dpAmount = dpData[selectedDpId].jumlah_per_supir[supirId];
        }
        
        if (dpAmount > 0 && realisasiAmount > 0) {
            const selisih = realisasiAmount - dpAmount;
            if (selisih > 0) {
                totalKurangBayar += selisih;
                breakdownItems.push({
                    type: 'kurang',
                    supir: supirName,
                    amount: selisih
                });
            } else if (selisih < 0) {
                totalSisaDP += Math.abs(selisih);
                breakdownItems.push({
                    type: 'sisa',
                    supir: supirName,
                    amount: Math.abs(selisih)
                });
            } else {
                breakdownItems.push({
                    type: 'pas',
                    supir: supirName,
                    amount: 0
                });
            }
        } else if (dpAmount === 0 && realisasiAmount > 0) {
            totalBayarPenuh += realisasiAmount;
            breakdownItems.push({
                type: 'penuh',
                supir: supirName,
                amount: realisasiAmount
            });
        }
    });

    // Get selected DP
    const selectedDpId = document.getElementById('pembayaran_dp_ob_id').value;
    let dpAmount = 0;
    let finalTotal = subtotalPembayaran;

    if (selectedDpId && dpData[selectedDpId]) {
        dpAmount = dpData[selectedDpId].total;
        finalTotal = subtotalPembayaran - dpAmount;
    }

    const calculationDiv = document.getElementById('total-calculation');
    const jumlahSupirSpan = document.getElementById('jumlah-supir');
    const subtotalPembayaranSpan = document.getElementById('subtotal-pembayaran');
    const dpReductionRow = document.getElementById('dp-reduction-row');
    const dpAmountSpan = document.getElementById('dp-amount');
    const dpAmountTextSpan = document.getElementById('dp-amount-text');
    const totalFinalPembayaranSpan = document.getElementById('total-final-pembayaran');
    const selisihBreakdownDiv = document.getElementById('selisih-breakdown');
    const breakdownItemsDiv = document.getElementById('breakdown-items');

    if (jumlahSupir > 0 && subtotalPembayaran > 0) {
        calculationDiv.classList.remove('hidden');
        jumlahSupirSpan.textContent = jumlahSupir;
        subtotalPembayaranSpan.textContent = new Intl.NumberFormat('id-ID').format(subtotalPembayaran);
        
        // Show breakdown if there are items
        if (breakdownItems.length > 0) {
            selisihBreakdownDiv.classList.remove('hidden');
            breakdownItemsDiv.innerHTML = '';
            
            breakdownItems.forEach(function(item) {
                const itemDiv = document.createElement('div');
                itemDiv.className = 'flex items-center justify-between px-2 py-1 rounded';
                
                let iconClass, colorClass, statusText;
                switch(item.type) {
                    case 'kurang':
                        iconClass = 'fas fa-arrow-up';
                        colorClass = 'bg-red-50 text-red-700';
                        statusText = 'Kurang Bayar';
                        break;
                    case 'sisa':
                        iconClass = 'fas fa-arrow-down';
                        colorClass = 'bg-blue-50 text-blue-700';
                        statusText = 'Sisa DP';
                        break;
                    case 'pas':
                        iconClass = 'fas fa-check';
                        colorClass = 'bg-green-50 text-green-700';
                        statusText = 'Pas';
                        break;
                    case 'penuh':
                        iconClass = 'fas fa-user-plus';
                        colorClass = 'bg-yellow-50 text-yellow-700';
                        statusText = 'Bayar Penuh';
                        break;
                }
                
                itemDiv.className += ` ${colorClass}`;
                itemDiv.innerHTML = `
                    <span class="text-xs">
                        <i class="${iconClass} mr-1"></i>
                        ${item.supir}: ${statusText}
                    </span>
                    <span class="text-xs font-medium">
                        ${item.amount > 0 ? 'Rp ' + formatNumber(item.amount) : '-'}
                    </span>
                `;
                breakdownItemsDiv.appendChild(itemDiv);
            });
        } else {
            selisihBreakdownDiv.classList.add('hidden');
        }

        // Show/hide DP reduction
        if (dpAmount > 0) {
            dpReductionRow.classList.remove('hidden');
            dpAmountSpan.textContent = new Intl.NumberFormat('id-ID').format(dpAmount);
            dpAmountTextSpan.textContent = `Rp ${new Intl.NumberFormat('id-ID').format(dpAmount)} (${dpData[selectedDpId].nomor})`;
        } else {
            dpReductionRow.classList.add('hidden');
        }

        // Update final total
        totalFinalPembayaranSpan.textContent = new Intl.NumberFormat('id-ID').format(Math.max(0, finalTotal));

        // Change color based on final total
        const finalTotalElement = totalFinalPembayaranSpan.parentElement.parentElement;
        const finalTotalLeftSpan = finalTotalElement.querySelector('span');
        
        if (finalTotal < 0) {
            finalTotalElement.className = 'flex items-center justify-between border-t border-red-300 pt-2 bg-red-100 rounded px-2 py-1';
            totalFinalPembayaranSpan.className = 'text-lg font-bold text-red-900';
            finalTotalLeftSpan.className = 'text-sm font-bold text-red-900';
        } else {
            finalTotalElement.className = 'flex items-center justify-between border-t border-blue-300 pt-2 bg-blue-100 rounded px-2 py-1';
            totalFinalPembayaranSpan.className = 'text-lg font-bold text-blue-900';
            finalTotalLeftSpan.className = 'text-sm font-bold text-blue-900';
        }
    } else {
        calculationDiv.classList.add('hidden');
    }
}

// These event listeners will be moved to DOMContentLoaded

// Auto generate nomor on page load if field is empty
document.addEventListener('DOMContentLoaded', function() {
    const nomorField = document.getElementById('nomor_pembayaran');
    if (!nomorField.value.trim()) {
        generateNomor();
    }

    // Load previously selected supir from old input
    @if(old('supir'))
        selectedSupir = @json(old('supir'));
    @endif

    // Initialize supir display
    updateSupirDisplay();

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
    document.querySelector('form').addEventListener('submit', function(e) {
        // Pastikan semua hidden input jumlah terisi
        const hiddenInputs = document.querySelectorAll('input[name^="jumlah["]');
        let hasEmptyAmount = false;
        
        hiddenInputs.forEach(function(input) {
            if (!input.value || input.value === '0' || input.value === '') {
                hasEmptyAmount = true;
            }
        });
        
        if (hasEmptyAmount) {
            e.preventDefault();
            alert('Harap isi semua jumlah pembayaran untuk setiap supir yang dipilih');
            return false;
        }
        
        // Debug: log form data before submit
        console.log('Form data before submit:');
        const formData = new FormData(this);
        for (let [key, value] of formData.entries()) {
            if (key.startsWith('jumlah[')) {
                console.log(key + ': ' + value);
            }
        }
    });

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
                helpText.textContent = 'Untuk pembayaran biasanya kredit (uang keluar)';
                helpText.className = 'mt-1 text-sm text-gray-500';
            }
        }
    });

    // Handle kas/bank selection - regenerate nomor when changed
    document.getElementById('kas_bank').addEventListener('change', function(e) {
        if (e.target.value) {
            // Re-generate nomor pembayaran with new COA prefix
            generateNomor();
        }
    });

    // Handle DP selection change
    document.getElementById('pembayaran_dp_ob_id').addEventListener('change', function(e) {
        updateTotalCalculation();

        // Show/hide info about selected DP
        const selectedDpId = e.target.value;
        const dpInfoDiv = document.getElementById('dp-selection-info');
        const dpInfoSpan = document.getElementById('selected-dp-info');

        if (selectedDpId && dpData[selectedDpId]) {
            const dp = dpData[selectedDpId];
            dpInfoDiv.classList.remove('hidden');

            // Auto-select supir dari DP yang dipilih
            autoSelectSupirFromDp(selectedDpId);

            // Format daftar nama supir
            let supirNamesText = '';
            if (dp.supir_names && dp.supir_names.length > 0) {
                supirNamesText = `<br><span class="text-xs text-green-600"><strong>Supir:</strong> ${dp.supir_names.join(', ')}</span>`;
            }

            dpInfoSpan.innerHTML = `
                <strong>${dp.nomor}</strong> - ${dp.tanggal} - ${dp.supir_count} supir<br>
                <span class="font-semibold text-green-800">Nilai DP: Rp ${new Intl.NumberFormat('id-ID').format(dp.total)}</span>
                ${supirNamesText}
                <br><span class="text-xs text-blue-600 mt-1"><i class="fas fa-magic mr-1"></i>Supir telah dipilih otomatis dari DP ini</span>
            `;
            console.log(`DP dipilih: ${dp.nomor} - Rp ${new Intl.NumberFormat('id-ID').format(dp.total)} - Supir: ${dp.supir_names.join(', ')}`);
        } else {
            dpInfoDiv.classList.add('hidden');

            // Clear auto-selected supir jika tidak ada DP yang dipilih
            clearAutoSelectedSupir();
        }
    });

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
});
</script>

@endsection
