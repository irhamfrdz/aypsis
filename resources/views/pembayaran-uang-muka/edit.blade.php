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
                    <p class="mt-2 text-gray-600">Form untuk mengedit pembayaran Uang Muka Out Bound (OB)</p>
                </div>

                <div class="flex space-x-3">
                    <a href="{{ route('pembayaran-uang-muka.index') }}"
                       class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </div>

            <!-- Form Edit Pembayaran Uang Muka -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <form action="{{ route('pembayaran-uang-muka.update', $pembayaran->id) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

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

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nomor Pembayaran -->
                            <div>
                                <label for="nomor_pembayaran" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nomor Pembayaran <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       name="nomor_pembayaran"
                                       id="nomor_pembayaran"
                                       value="{{ old('nomor_pembayaran', $pembayaran->nomor_pembayaran) }}"
                                       required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nomor_pembayaran') border-red-300 @enderror">
                                @error('nomor_pembayaran')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tanggal Pembayaran -->
                            <div>
                                <label for="tanggal_pembayaran" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Pembayaran <span class="text-red-500">*</span>
                                </label>
                                <input type="date"
                                       name="tanggal_pembayaran"
                                       id="tanggal_pembayaran"
                                       value="{{ old('tanggal_pembayaran', \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('Y-m-d')) }}"
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
                                        <option value="{{ $kasBank->id }}" {{ old('kas_bank', $pembayaran->kas_bank_id) == $kasBank->id ? 'selected' : '' }}>
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
                                    <option value="debit" {{ old('jenis_transaksi', $pembayaran->jenis_transaksi) == 'debit' ? 'selected' : '' }}>
                                        Debit (Menambah Saldo)
                                    </option>
                                    <option value="kredit" {{ old('jenis_transaksi', $pembayaran->jenis_transaksi) == 'kredit' ? 'selected' : '' }}>
                                        Kredit (Mengurangi Saldo)
                                    </option>
                                </select>
                                @error('jenis_transaksi')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
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
                                                {{ old('kegiatan', $pembayaran->kegiatan) == $kegiatan->id ? 'selected' : '' }}>
                                            {{ $kegiatan->kode_kegiatan }} - {{ $kegiatan->nama_kegiatan }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kegiatan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
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
                                        <option value="{{ $mobil->id }}" {{ old('mobil_id', $pembayaran->mobil_id) == $mobil->id ? 'selected' : '' }}>
                                            {{ $mobil->nomor_polisi }}
                                            @if($mobil->merek)
                                                - {{ $mobil->merek }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('mobil_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Penerima Field -->
                            <div id="penerima-field" style="display: none;">
                                <label for="penerima_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Penerima <span class="text-red-500">*</span>
                                </label>
                                <select name="penerima_id"
                                        id="penerima_id"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('penerima_id') border-red-300 @enderror">
                                    <option value="">-- Pilih Penerima --</option>
                                    @foreach($karyawanList as $karyawan)
                                        <option value="{{ $karyawan->id }}" {{ old('penerima_id', $pembayaran->penerima_id) == $karyawan->id ? 'selected' : '' }}>
                                            {{ $karyawan->nama_lengkap }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('penerima_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Jumlah Penerima -->
                            <div id="jumlah-penerima-field" style="display: none;">
                                <label for="jumlah_penerima" class="block text-sm font-medium text-gray-700 mb-2">
                                    Jumlah Uang Muka <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">Rp</span>
                                    </div>
                                    @php
                                        $initialJumlahPenerima = old('jumlah_penerima', $pembayaran->total_pembayaran);
                                    @endphp
                                    <input type="text"
                                           name="jumlah_penerima_display"
                                           id="jumlah_penerima_display"
                                           value="{{ number_format($initialJumlahPenerima, 0, ',', '.') }}"
                                           class="w-full pl-12 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           oninput="formatCurrencyPenerima(this)">
                                    <input type="hidden"
                                           name="jumlah_penerima"
                                           id="jumlah_penerima"
                                           value="{{ $initialJumlahPenerima }}">
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Jumlah Mobil -->
                            <div id="jumlah-mobil-field" style="display: none;">
                                <label for="jumlah_mobil" class="block text-sm font-medium text-gray-700 mb-2">
                                    Jumlah Uang Muka untuk Mobil <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">Rp</span>
                                    </div>
                                    @php
                                        $initialJumlahMobil = old('jumlah_mobil', $pembayaran->total_pembayaran);
                                    @endphp
                                    <input type="text"
                                           name="jumlah_mobil_display"
                                           id="jumlah_mobil_display"
                                           value="{{ number_format($initialJumlahMobil, 0, ',', '.') }}"
                                           class="w-full pl-12 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           oninput="formatCurrencyMobil(this)">
                                    <input type="hidden"
                                           name="jumlah_mobil"
                                           id="jumlah_mobil"
                                           value="{{ $initialJumlahMobil }}">
                                </div>
                            </div>

                            <!-- Supir Multi-select -->
                            <div id="supir-field">
                                <label for="supir" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Supir <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="border border-gray-300 rounded-md bg-white">
                                        <div class="p-3">
                                            <div class="flex flex-wrap gap-2 mb-2" id="selected-supir-tags">
                                                <!-- Tags will be populated by JS -->
                                            </div>
                                            <button type="button"
                                                    id="supir-dropdown-toggle"
                                                    class="w-full text-left text-gray-500 bg-gray-50 hover:bg-gray-100 px-3 py-2 rounded border text-sm">
                                                <i class="fas fa-plus mr-2"></i>Pilih/Edit Supir...
                                            </button>
                                        </div>
                                    </div>

                                    <div id="supir-dropdown-menu" class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg hidden">
                                        <div class="max-h-48 overflow-y-auto">
                                            @foreach($supirList as $supir)
                                                <label class="flex items-center px-3 py-2 hover:bg-gray-100 cursor-pointer">
                                                    <input type="checkbox"
                                                           name="supir[]"
                                                           value="{{ $supir->id }}"
                                                           class="supir-checkbox mr-3 text-blue-600"
                                                           {{ in_array($supir->id, old('supir', $pembayaran->supir_ids ?? [])) ? 'checked' : '' }}>
                                                    <div class="flex-1">
                                                        <div class="font-medium text-gray-900">{{ $supir->nama_lengkap }}</div>
                                                        <div class="text-sm text-gray-500">NIK: {{ $supir->nik }}</div>
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Jumlah Container for Supir -->
                            <div id="jumlah-container">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Jumlah Uang Muka per Supir <span class="text-red-500">*</span>
                                </label>
                                <div id="jumlah-inputs">
                                    <!-- Populated by JS -->
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
                                          placeholder="Masukkan keterangan tambahan"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('keterangan', $pembayaran->keterangan) }}</textarea>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                                <a href="{{ route('pembayaran-uang-muka.index') }}"
                                   class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 transition duration-200">
                                    <i class="fas fa-times mr-1"></i> Batal
                                </a>
                                <button type="submit"
                                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200">
                                    <i class="fas fa-save mr-1"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Data from server
let selectedSupir = @json(old('supir', $pembayaran->supir_ids ?? []));
const oldJumlahPerSupir = @json(old('jumlah', $pembayaran->jumlah_per_supir ?? []));

// Initialize display
document.addEventListener('DOMContentLoaded', function() {
    handleKegiatanChange();
    updateSupirDisplay();
});

function handleKegiatanChange() {
    const kegiatanSelect = document.getElementById('kegiatan');
    const selectedOption = kegiatanSelect.options[kegiatanSelect.selectedIndex];
    const kegiatanText = (selectedOption && selectedOption.getAttribute('data-nama') || '').toLowerCase();

    // Elements
    const supirField = document.getElementById('supir-field');
    const jumlahContainer = document.getElementById('jumlah-container');
    const mobilField = document.getElementById('mobil-field');
    const penerimaField = document.getElementById('penerima-field');
    const jumlahMobilField = document.getElementById('jumlah-mobil-field');
    const jumlahPenerimaField = document.getElementById('jumlah-penerima-field');

    // Reset
    [supirField, jumlahContainer, mobilField, penerimaField, jumlahMobilField, jumlahPenerimaField].forEach(el => el.style.display = 'none');

    if (kegiatanText.includes('kir') && kegiatanText.includes('stnk')) {
        mobilField.style.display = 'block';
        penerimaField.style.display = 'block';
        jumlahMobilField.style.display = 'block';
    } else if (kegiatanText.includes('ob muat') || kegiatanText.includes('ob bongkar') ||
               kegiatanText.includes('muat') || kegiatanText.includes('bongkar')) {
        supirField.style.display = 'block';
        jumlahContainer.style.display = 'block';
    } else if (kegiatanText !== '' && kegiatanText !== '-- pilih kegiatan --') {
        penerimaField.style.display = 'block';
        jumlahPenerimaField.style.display = 'block';
    }
}

function updateSupirDisplay() {
    const tagsContainer = document.getElementById('selected-supir-tags');
    const inputsContainer = document.getElementById('jumlah-inputs');
    tagsContainer.innerHTML = '';
    inputsContainer.innerHTML = '';

    selectedSupir.forEach(supirId => {
        const checkbox = document.querySelector(`input[value="${supirId}"]`);
        if (checkbox) {
            const label = checkbox.closest('label');
            const nama = label.querySelector('.font-medium').textContent;
            const nik = label.querySelector('.text-gray-500').textContent.replace('NIK: ', '');

            // Create Tag
            const tag = document.createElement('div');
            tag.className = 'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800';
            tag.innerHTML = `${nama} (${nik}) <button type="button" onclick="removeSupir('${supirId}')" class="ml-2 text-blue-600">&times;</button>`;
            tagsContainer.appendChild(tag);

            // Create Input
            const amount = oldJumlahPerSupir[supirId] || '';
            const inputDiv = document.createElement('div');
            inputDiv.className = 'mb-3 p-3 border border-gray-200 rounded-md bg-gray-50';
            inputDiv.innerHTML = `
                <label class="block text-sm font-medium text-gray-700 mb-1">${nama}</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">Rp</span>
                    <input type="text" value="${formatNumber(amount)}" class="w-full pl-12 pr-3 py-2 border border-gray-300 rounded-md" oninput="formatCurrency(this, '${supirId}')">
                    <input type="hidden" name="jumlah[${supirId}]" id="jumlah_${supirId}" value="${amount}">
                </div>
            `;
            inputsContainer.appendChild(inputDiv);
        }
    });
}

function removeSupir(id) {
    selectedSupir = selectedSupir.filter(s => s != id);
    const cb = document.querySelector(`input[value="${id}"]`);
    if (cb) cb.checked = false;
    updateSupirDisplay();
}

// Dropdown events
document.getElementById('supir-dropdown-toggle').addEventListener('click', () => {
    document.getElementById('supir-dropdown-menu').classList.toggle('hidden');
});

document.querySelectorAll('.supir-checkbox').forEach(cb => {
    cb.addEventListener('change', function() {
        if (this.checked) {
            if (!selectedSupir.includes(this.value)) selectedSupir.push(this.value);
        } else {
            selectedSupir = selectedSupir.filter(s => s != this.value);
        }
        updateSupirDisplay();
    });
});

// Helper functions (Currency formatting)
function formatNumber(num) {
    if (!num || num === '') return '';
    return new Intl.NumberFormat('id-ID').format(num);
}

function formatCurrency(input, id) {
    let val = input.value.replace(/[^\d]/g, '');
    document.getElementById('jumlah_' + id).value = val || '0';
    input.value = val ? formatNumber(val) : '';
}

function formatCurrencyPenerima(input) {
    let val = input.value.replace(/[^\d]/g, '');
    document.getElementById('jumlah_penerima').value = val || '0';
    input.value = val ? formatNumber(val) : '';
}

function formatCurrencyMobil(input) {
    let val = input.value.replace(/[^\d]/g, '');
    document.getElementById('jumlah_mobil').value = val || '0';
    input.value = val ? formatNumber(val) : '';
}
</script>
@endsection
