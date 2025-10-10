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
                    <p class="mt-2 text-gray-600">Form untuk membuat pembayaran DP Out Bound (OB) baru</p>
                </div>

                <div class="flex space-x-3">
                    <a href="{{ route('pembayaran-dp-ob.index') }}"
                       class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </div>

            <!-- Form Pembayaran DP OB -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <form action="{{ route('pembayaran-dp-ob.store') }}" method="POST" class="space-y-6">
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
                                <p id="jenis-transaksi-help" class="mt-1 text-sm text-gray-500">Untuk pembayaran DP biasanya kredit (uang keluar)</p>
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
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('keterangan') border-red-300 @enderror">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                            <a href="{{ route('pembayaran-dp-ob.index') }}"
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
        let url = '{{ route('pembayaran-dp-ob.generate-nomor') }}';
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
                        <input type="number"
                               name="jumlah[${supirId}]"
                               id="jumlah_${supirId}"
                               value="${getOldJumlah(supirId)}"
                               placeholder="0"
                               min="0"
                               step="1000"
                               required
                               class="w-full pl-12 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
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
            helpText.textContent = 'Untuk pembayaran DP biasanya kredit (uang keluar)';
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
});// Auto generate nomor on page load if field is empty
document.addEventListener('DOMContentLoaded', function() {
    const nomorField = document.getElementById('nomor_pembayaran');
    if (!nomorField.value.trim()) {
        generateNomor();
    }

    // Initialize supir display
    updateSupirDisplay();
});
</script>

@endsection
