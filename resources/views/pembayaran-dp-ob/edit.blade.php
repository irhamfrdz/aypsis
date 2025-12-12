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
                    <p class="mt-2 text-gray-600">Form untuk mengubah pembayaran Down Payment (DP) Out Bound (OB)</p>
                </div>

                <div class="flex space-x-3">
                    <a href="{{ route('pembayaran-ob.show', $pembayaran->id) }}"
                       class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </div>

            <!-- Form Pembayaran DP OB -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <form action="{{ route('pembayaran-ob.update', $pembayaran->id) }}" method="POST" class="space-y-6">
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
                                    <div class="ml-3 flex-1">
                                        <h3 class="text-sm font-medium text-red-800">Gagal menyimpan data pembayaran</h3>
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

                        <!-- Alert Error from Session -->
                        @if (session('error'))
                            <div class="bg-red-50 border-l-4 border-red-500 rounded-md p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-bold text-red-800 mb-1">Error!</h3>
                                        <p class="text-sm text-red-700">{{ session('error') }}</p>
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
                            <!-- Nomor Pembayaran (Readonly) -->
                            <div>
                                <label for="nomor_pembayaran" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nomor Pembayaran <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       name="nomor_pembayaran"
                                       id="nomor_pembayaran"
                                       value="{{ old('nomor_pembayaran', $pembayaran->nomor_pembayaran) }}"
                                       readonly
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 cursor-not-allowed">
                                <p class="mt-1 text-sm text-gray-500">Nomor pembayaran tidak dapat diubah</p>
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
                                        <option value="{{ $kasBank->id }}" 
                                            {{ old('kas_bank', $pembayaran->kas_bank_akun_id) == $kasBank->id ? 'selected' : '' }}>
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
                                <p id="jenis-transaksi-help" class="mt-1 text-sm text-gray-500">Untuk pembayaran DP OB biasanya kredit (uang keluar)</p>
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
                                        onchange="loadNomorVoyage()"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('kegiatan') border-red-300 @enderror">
                                    <option value="">-- Pilih Kegiatan --</option>
                                    <option value="Bongkar" {{ old('kegiatan', $pembayaran->kegiatan) == 'Bongkar' ? 'selected' : '' }}>Bongkar</option>
                                    <option value="Muat" {{ old('kegiatan', $pembayaran->kegiatan) == 'Muat' ? 'selected' : '' }}>Muat</option>
                                </select>
                                @error('kegiatan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nomor Voyage Field -->
                            <div>
                                <label for="nomor_voyage" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nomor Voyage <span class="text-red-500">*</span>
                                </label>
                                <select name="nomor_voyage"
                                        id="nomor_voyage"
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nomor_voyage') border-red-300 @enderror"
                                        {{ !old('kegiatan', $pembayaran->kegiatan) ? 'disabled' : '' }}>
                                    <option value="">-- Pilih Kegiatan Terlebih Dahulu --</option>
                                </select>
                                @error('nomor_voyage')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500" id="voyage-help-text">Pilih nomor voyage dari data yang tersedia</p>
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
                                @error('supir')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Jumlah Pembayaran per Supir -->
                            <div id="jumlah-container">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Jumlah DP per Supir <span class="text-red-500">*</span>
                                </label>
                                <div id="jumlah-inputs">
                                    <!-- Dynamic inputs akan ditambahkan di sini -->
                                </div>
                                <div id="no-supir-message" class="text-gray-500 text-sm italic">
                                    Pilih supir terlebih dahulu untuk mengisi jumlah DP
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
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('keterangan') border-red-300 @enderror">{{ old('keterangan', $pembayaran->keterangan) }}</textarea>
                            @error('keterangan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                            <a href="{{ route('pembayaran-ob.show', $pembayaran->id) }}"
                               class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                                <i class="fas fa-times mr-1"></i> Batal
                            </a>
                            <button type="submit"
                                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                                <i class="fas fa-save mr-1"></i> Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Multi-select Supir Dropdown functionality
let selectedSupir = [];

// Load previously selected supir from database or old input
@if(old('supir'))
    selectedSupir = @json(old('supir'));
@elseif($pembayaran->supir_ids)
    selectedSupir = @json($pembayaran->supir_ids);
@endif

// Existing jumlah per supir
const existingJumlah = @json($pembayaran->jumlah_per_supir ?? []);

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
                               value="${formatNumber(getJumlah(supirId))}"
                               placeholder="0"
                               required
                               class="w-full pl-12 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               oninput="formatCurrency(this, ${supirId})">
                        <input type="hidden"
                               name="jumlah[${supirId}]"
                               id="jumlah_${supirId}"
                               value="${getJumlah(supirId)}">
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
        const checkbox = document.querySelector(`input[value="${supirId}"]`);
        if (checkbox) checkbox.checked = false;
        updateSupirDisplay();
    }
}

document.getElementById('supir-dropdown-toggle').addEventListener('click', toggleSupirDropdown);

document.querySelectorAll('.supir-checkbox').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
        const supirId = this.value;
        if (this.checked) {
            if (!selectedSupir.includes(supirId)) selectedSupir.push(supirId);
        } else {
            const index = selectedSupir.indexOf(supirId);
            if (index > -1) selectedSupir.splice(index, 1);
        }
        updateSupirDisplay();
    });
});

document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('supir-dropdown-menu');
    const container = dropdown.closest('.relative');
    if (!container.contains(e.target)) dropdown.classList.add('hidden');
});

function getJumlah(supirId) {
    @if(old('jumlah'))
        const oldJumlah = @json(old('jumlah'));
        return oldJumlah[supirId] || existingJumlah[supirId] || '';
    @else
        return existingJumlah[supirId] || '';
    @endif
}

function formatNumber(num) {
    if (!num || num === '') return '';
    return new Intl.NumberFormat('id-ID').format(num);
}

function formatCurrency(input, supirId) {
    let value = input.value.replace(/[^\d]/g, '');
    const hiddenInput = document.getElementById(`jumlah_${supirId}`);
    if (hiddenInput) hiddenInput.value = value || '0';
    if (value) input.value = formatNumber(value);
    else input.value = '';
}

document.getElementById('jenis_transaksi').addEventListener('change', function(e) {
    const helpText = document.getElementById('jenis-transaksi-help');
    if (helpText) {
        if (e.target.value === 'debit') {
            helpText.textContent = 'Debit: Uang masuk ke akun kas/bank (menambah saldo)';
            helpText.className = 'mt-1 text-sm text-green-600';
        } else if (e.target.value === 'kredit') {
            helpText.textContent = 'Kredit: Uang keluar dari akun kas/bank (mengurangi saldo)';
            helpText.className = 'mt-1 text-sm text-red-600';
        }
    }
});

// Load Nomor Voyage based on Kegiatan selection
async function loadNomorVoyage() {
    const kegiatanSelect = document.getElementById('kegiatan');
    const voyageSelect = document.getElementById('nomor_voyage');
    const helpText = document.getElementById('voyage-help-text');
    const kegiatan = kegiatanSelect.value;
    const currentVoyage = '{{ old('nomor_voyage', $pembayaran->nomor_voyage ?? '') }}';

    // Reset voyage select
    voyageSelect.innerHTML = '<option value="">-- Loading... --</option>';
    voyageSelect.disabled = true;

    if (!kegiatan) {
        voyageSelect.innerHTML = '<option value="">-- Pilih Kegiatan Terlebih Dahulu --</option>';
        helpText.textContent = 'Pilih nomor voyage dari data yang tersedia';
        helpText.className = 'mt-1 text-sm text-gray-500';
        return;
    }

    try {
        const response = await fetch(`{{ route('pembayaran-ob.get-voyage-list') }}?kegiatan=${kegiatan}`);
        const data = await response.json();

        if (data.success && data.voyages) {
            voyageSelect.innerHTML = '<option value="">-- Pilih Nomor Voyage --</option>';
            
            data.voyages.forEach(voyage => {
                const option = document.createElement('option');
                option.value = voyage.no_voyage;
                option.textContent = `${voyage.no_voyage} - ${voyage.nama_kapal}`;
                if (currentVoyage == voyage.no_voyage) {
                    option.selected = true;
                }
                voyageSelect.appendChild(option);
            });

            voyageSelect.disabled = false;
            
            // Update help text
            if (kegiatan === 'Muat') {
                helpText.textContent = 'Data dari Naik Kapal';
                helpText.className = 'mt-1 text-sm text-blue-600';
            } else if (kegiatan === 'Bongkar') {
                helpText.textContent = 'Data dari BL';
                helpText.className = 'mt-1 text-sm text-green-600';
            }
        } else {
            voyageSelect.innerHTML = '<option value="">-- Tidak Ada Data --</option>';
            helpText.textContent = data.message || 'Tidak ada data voyage tersedia';
            helpText.className = 'mt-1 text-sm text-red-600';
        }
    } catch (error) {
        console.error('Error loading voyage data:', error);
        voyageSelect.innerHTML = '<option value="">-- Error Loading Data --</option>';
        helpText.textContent = 'Terjadi kesalahan saat memuat data voyage';
        helpText.className = 'mt-1 text-sm text-red-600';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    updateSupirDisplay();
    
    // Load voyage if kegiatan already selected (for edit mode)
    const kegiatanValue = document.getElementById('kegiatan').value;
    if (kegiatanValue) {
        loadNomorVoyage();
    }
});
</script>

@endsection
