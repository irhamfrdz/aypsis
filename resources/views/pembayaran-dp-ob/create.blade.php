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
                    <a href="{{ route('pembayaran-ob.index') }}"
                       class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </div>

            <!-- Form Pembayaran DP OB -->
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
                                        onchange="loadNomorVoyage()"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('kegiatan') border-red-300 @enderror">
                                    <option value="">-- Pilih Kegiatan --</option>
                                    <option value="Bongkar" {{ old('kegiatan') == 'Bongkar' ? 'selected' : '' }}>Bongkar</option>
                                    <option value="Muat" {{ old('kegiatan') == 'Muat' ? 'selected' : '' }}>Muat</option>
                                </select>
                                @error('kegiatan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500">Pilih jenis kegiatan untuk pembayaran uang muka ini</p>
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
                                        disabled>
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
                                        <!-- Search box -->
                                        <div class="p-2 border-b border-gray-200 bg-gray-50">
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <i class="fas fa-search text-gray-400"></i>
                                                </div>
                                                <input type="text"
                                                       id="supir-search-input"
                                                       class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                                       placeholder="Cari nama atau NIK supir..."
                                                       autocomplete="off">
                                            </div>
                                        </div>
                                        
                                        <!-- Supir list -->
                                        <div class="max-h-48 overflow-y-auto" id="supir-list-container">
                                            @foreach($supirList as $supir)
                                                <label class="supir-item flex items-center px-3 py-2 hover:bg-gray-100 cursor-pointer"
                                                       data-nama="{{ strtolower($supir->nama_lengkap) }}"
                                                       data-nik="{{ strtolower($supir->nik) }}">
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
                                        
                                        <!-- No results message -->
                                        <div id="no-supir-results" class="hidden px-3 py-4 text-center text-gray-500 text-sm">
                                            <i class="fas fa-search mb-2 text-gray-400 text-lg"></i>
                                            <div>Tidak ada supir yang cocok dengan pencarian</div>
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

                            <!-- Jumlah Pembayaran per Supir -->
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

        // Format: UM-KBJ-MM-YY-NNNNNN (default COA KBJ)
        document.getElementById('nomor_pembayaran').value = `UM-KBJ-${month}-${year}-${random}`;
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

// Handle supir search
document.getElementById('supir-search-input').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const supirItems = document.querySelectorAll('.supir-item');
    const noResultsMessage = document.getElementById('no-supir-results');
    let hasVisibleItems = false;

    supirItems.forEach(function(item) {
        const nama = item.getAttribute('data-nama');
        const nik = item.getAttribute('data-nik');
        
        if (nama.includes(searchTerm) || nik.includes(searchTerm)) {
            item.style.display = 'flex';
            hasVisibleItems = true;
        } else {
            item.style.display = 'none';
        }
    });

    // Show/hide no results message
    if (hasVisibleItems || searchTerm === '') {
        noResultsMessage.classList.add('hidden');
    } else {
        noResultsMessage.classList.remove('hidden');
    }
});

// Clear search when dropdown opens
document.getElementById('supir-dropdown-toggle').addEventListener('click', function() {
    const searchInput = document.getElementById('supir-search-input');
    searchInput.value = '';
    
    // Show all items
    document.querySelectorAll('.supir-item').forEach(function(item) {
        item.style.display = 'flex';
    });
    document.getElementById('no-supir-results').classList.add('hidden');
    
    // Focus search input when dropdown opens
    setTimeout(function() {
        if (!document.getElementById('supir-dropdown-menu').classList.contains('hidden')) {
            searchInput.focus();
        }
    }, 100);
});

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
    const searchInput = document.getElementById('supir-search-input');

    if (!container.contains(e.target)) {
        dropdown.classList.add('hidden');
    }
});

// Prevent dropdown close when clicking inside (including search input)
document.getElementById('supir-dropdown-menu').addEventListener('click', function(e) {
    e.stopPropagation();
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

// Handle kas/bank selection - regenerate nomor when changed
document.getElementById('kas_bank').addEventListener('change', function(e) {
    if (e.target.value) {
        // Re-generate nomor pembayaran with new COA prefix
        generateNomor();
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
    let errors = [];
    
    // Validasi nomor pembayaran
    const nomorPembayaran = document.getElementById('nomor_pembayaran').value;
    if (!nomorPembayaran || nomorPembayaran.trim() === '') {
        errors.push('Nomor pembayaran harus diisi');
    }
    
    // Validasi tanggal
    const tanggalPembayaran = document.getElementById('tanggal_pembayaran').value;
    if (!tanggalPembayaran) {
        errors.push('Tanggal pembayaran harus diisi');
    }
    
    // Validasi kas/bank
    const kasBank = document.getElementById('kas_bank').value;
    if (!kasBank) {
        errors.push('Akun Kas/Bank harus dipilih');
    }
    
    // Validasi jenis transaksi
    const jenisTransaksi = document.getElementById('jenis_transaksi').value;
    if (!jenisTransaksi) {
        errors.push('Jenis transaksi harus dipilih');
    }
    
    // Validasi kegiatan
    const kegiatan = document.getElementById('kegiatan').value;
    if (!kegiatan) {
        errors.push('Kegiatan harus dipilih (Bongkar/Muat)');
    }
    
    // Validasi nomor voyage
    const nomorVoyage = document.getElementById('nomor_voyage').value;
    if (!nomorVoyage) {
        errors.push('Nomor Voyage harus dipilih');
    }
    
    // Validasi supir
    if (selectedSupir.length === 0) {
        errors.push('Minimal harus memilih 1 supir');
    }
    
    // Pastikan semua hidden input jumlah terisi
    const hiddenInputs = document.querySelectorAll('input[name^="jumlah["]');
    let hasEmptyAmount = false;
    let totalAmount = 0;

    hiddenInputs.forEach(function(input) {
        const amount = parseInt(input.value) || 0;
        if (amount <= 0) {
            hasEmptyAmount = true;
        }
        totalAmount += amount;
    });

    if (hasEmptyAmount) {
        errors.push('Semua jumlah Uang Muka untuk setiap supir harus diisi dan lebih dari 0');
    }
    
    if (totalAmount <= 0) {
        errors.push('Total pembayaran harus lebih dari 0');
    }

    // Jika ada error, tampilkan dan cancel submit
    if (errors.length > 0) {
        e.preventDefault();
        
        let errorMessage = '⚠️ PERINGATAN: Data belum lengkap!\\n\\n';
        errorMessage += 'Mohon perbaiki kesalahan berikut:\\n\\n';
        errors.forEach((error, index) => {
            errorMessage += `${index + 1}. ${error}\\n`;
        });
        errorMessage += '\\nSilakan lengkapi semua field yang diperlukan sebelum menyimpan.';
        
        alert(errorMessage);
        
        // Scroll ke element pertama yang error
        if (!nomorPembayaran) {
            document.getElementById('nomor_pembayaran').focus();
        } else if (!tanggalPembayaran) {
            document.getElementById('tanggal_pembayaran').focus();
        } else if (!kasBank) {
            document.getElementById('kas_bank').focus();
        } else if (!jenisTransaksi) {
            document.getElementById('jenis_transaksi').focus();
        } else if (!kegiatan) {
            document.getElementById('kegiatan').focus();
        } else if (!nomorVoyage) {
            document.getElementById('nomor_voyage').focus();
        }
        
        return false;
    }

    // Konfirmasi sebelum submit
    const confirmMessage = `Konfirmasi Pembayaran DP OB\\n\\n` +
                          `Nomor: ${nomorPembayaran}\\n` +
                          `Tanggal: ${tanggalPembayaran}\\n` +
                          `Kegiatan: ${kegiatan}\\n` +
                          `Voyage: ${nomorVoyage}\\n` +
                          `Jumlah Supir: ${selectedSupir.length}\\n` +
                          `Total: Rp ${formatNumber(totalAmount)}\\n\\n` +
                          `Apakah Anda yakin ingin menyimpan data ini?`;
    
    if (!confirm(confirmMessage)) {
        e.preventDefault();
        return false;
    }

    // Debug: log form data before submit
    console.log('Form data before submit:');
    const formData = new FormData(this);
    for (let [key, value] of formData.entries()) {
        console.log(key + ': ' + value);
    }
});

// Load Nomor Voyage based on Kegiatan selection
async function loadNomorVoyage() {
    const kegiatanSelect = document.getElementById('kegiatan');
    const voyageSelect = document.getElementById('nomor_voyage');
    const helpText = document.getElementById('voyage-help-text');
    const kegiatan = kegiatanSelect.value;

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

// Auto generate nomor on page load if field is empty
document.addEventListener('DOMContentLoaded', function() {
    const nomorField = document.getElementById('nomor_pembayaran');
    if (!nomorField.value.trim()) {
        generateNomor();
    }

    // Initialize supir display
    updateSupirDisplay();

    // Load voyage if kegiatan already selected (for old input)
    const kegiatanValue = document.getElementById('kegiatan').value;
    if (kegiatanValue) {
        loadNomorVoyage();
    }
});
</script>

@endsection
