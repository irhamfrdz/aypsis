@extends('layouts.app')

@section('title', 'Tambah Pembayaran Aktivitas Lainnya')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow-lg rounded-lg">
        <div class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center rounded-t-lg">
            <h3 class="text-xl font-semibold text-gray-800 flex items-center">
                <i class="fas fa-plus mr-3 text-blue-600"></i>
                Tambah Pembayaran Aktivitas Lainnya
            </h3>
            <a href="{{ route('pembayaran-aktivitas-lainnya.index') }}" class="inline-flex items-center px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium text-xs rounded-md transition duration-150 ease-in-out">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>

        <form action="{{ route('pembayaran-aktivitas-lainnya.store') }}" method="POST" class="p-6" id="pembayaran_form">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tanggal Pembayaran -->
                <div>
                    <label for="tanggal_pembayaran" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Pembayaran <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="tanggal_pembayaran" id="tanggal_pembayaran"
                           value="{{ old('tanggal_pembayaran', date('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tanggal_pembayaran') border-red-500 @enderror" required>
                    @error('tanggal_pembayaran')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nomor Accurate -->
                <div>
                    <label for="nomor_accurate" class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor Accurate
                    </label>
                    <input type="text" name="nomor_accurate" id="nomor_accurate"
                           value="{{ old('nomor_accurate') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nomor_accurate') border-red-500 @enderror"
                           placeholder="Nomor referensi Accurate">
                    @error('nomor_accurate')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Pilih Bank -->
                <div>
                    <label for="pilih_bank" class="block text-sm font-medium text-gray-700 mb-2">
                        Bank/Kas <span class="text-red-500">*</span>
                    </label>
                    <select name="pilih_bank" id="pilih_bank"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('pilih_bank') border-red-500 @enderror" required>
                        <option value="">Pilih Bank/Kas</option>
                        @foreach($bankAccounts as $bank)
                            <option value="{{ $bank->id }}" {{ old('pilih_bank') == $bank->id ? 'selected' : '' }}>
                                {{ $bank->nama_akun }} ({{ $bank->nomor_akun }})
                            </option>
                        @endforeach
                    </select>
                    @error('pilih_bank')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Akun Biaya -->
                <div>
                    <label for="akun_biaya_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Akun Biaya <span class="text-red-500">*</span>
                    </label>
                    <select name="akun_biaya_id" id="akun_biaya_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('akun_biaya_id') border-red-500 @enderror" required>
                        <option value="">Pilih Akun Biaya</option>
                        @foreach($coaBiaya as $coa)
                            <option value="{{ $coa->id }}" {{ old('akun_biaya_id') == $coa->id ? 'selected' : '' }}>
                                {{ $coa->nama_akun }} ({{ $coa->nomor_akun }})
                            </option>
                        @endforeach
                    </select>
                    @error('akun_biaya_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jenis Transaksi -->
                <div>
                    <label for="jenis_transaksi" class="block text-sm font-medium text-gray-700 mb-2">
                        Jenis Transaksi <span class="text-red-500">*</span>
                    </label>
                    <select name="jenis_transaksi" id="jenis_transaksi"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('jenis_transaksi') border-red-500 @enderror" required>
                        <option value="kredit" {{ old('jenis_transaksi', 'kredit') == 'kredit' ? 'selected' : '' }}>Kredit (Pengeluaran)</option>
                        <option value="debit" {{ old('jenis_transaksi') == 'debit' ? 'selected' : '' }}>Debit (Pemasukan)</option>
                    </select>
                    @error('jenis_transaksi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Total Pembayaran -->
                <div>
                    <label for="total_pembayaran" class="block text-sm font-medium text-gray-700 mb-2">
                        Total Pembayaran <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                        <input type="text" name="total_pembayaran" id="total_pembayaran"
                               value="{{ old('total_pembayaran') }}"
                               class="w-full pl-12 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('total_pembayaran') border-red-500 @enderror"
                               placeholder="0" required>
                    </div>
                    @error('total_pembayaran')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Aktivitas Pembayaran -->
            <div class="mt-6">
                <label for="aktivitas_pembayaran" class="block text-sm font-medium text-gray-700 mb-2">
                    Aktivitas Pembayaran <span class="text-red-500">*</span>
                </label>
                <textarea name="aktivitas_pembayaran" id="aktivitas_pembayaran" rows="4"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('aktivitas_pembayaran') border-red-500 @enderror"
                          placeholder="Jelaskan aktivitas pembayaran yang dilakukan..." required>{{ old('aktivitas_pembayaran') }}</textarea>
                @error('aktivitas_pembayaran')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                <!-- Nomor Voyage -->
                <div>
                    <label for="nomor_voyage" class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor Voyage
                    </label>
                    <input type="text" name="nomor_voyage" id="nomor_voyage"
                           value="{{ old('nomor_voyage') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nomor_voyage') border-red-500 @enderror"
                           placeholder="Contoh: V001">
                    @error('nomor_voyage')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nama Kapal -->
                <div>
                    <label for="nama_kapal" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Kapal
                    </label>
                    <input type="text" name="nama_kapal" id="nama_kapal"
                           value="{{ old('nama_kapal') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nama_kapal') border-red-500 @enderror"
                           placeholder="Nama kapal">
                    @error('nama_kapal')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Plat Nomor -->
                <div>
                    <label for="plat_nomor" class="block text-sm font-medium text-gray-700 mb-2">
                        Plat Nomor
                    </label>
                    <input type="text" name="plat_nomor" id="plat_nomor"
                           value="{{ old('plat_nomor') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('plat_nomor') border-red-500 @enderror"
                           placeholder="Contoh: B 1234 ABC">
                    @error('plat_nomor')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            <!-- Is DP Checkbox -->
            <div class="mt-6">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_dp" value="1" {{ old('is_dp') ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-700">Ini adalah pembayaran DP/Uang Muka</span>
                </label>
            </div>

            <!-- Uang Muka Supir Section -->
            <div class="mt-6" id="supir_section" style="{{ old('is_dp') ? '' : 'display: none;' }}">
                <h4 class="text-lg font-medium text-gray-900 mb-4">Detail Uang Muka Supir</h4>
                <div id="supir_container">
                    <!-- Template for supir rows will be added here -->
                </div>
                <button type="button" id="add_supir_btn" class="mt-4 inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium text-sm rounded-md transition duration-150 ease-in-out">
                    <i class="fas fa-plus mr-2"></i> Tambah Supir
                </button>
            </div>
            <div class="mt-8 flex justify-end space-x-4">
                <a href="{{ route('pembayaran-aktivitas-lainnya.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium text-sm rounded-md transition duration-150 ease-in-out">
                    <i class="fas fa-times mr-2"></i> Batal
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm rounded-md transition duration-150 ease-in-out">
                    <i class="fas fa-save mr-2"></i> Simpan Pembayaran
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Format number input
function formatNumber(input) {
    let value = input.value.replace(/[^\d]/g, '');
    input.value = new Intl.NumberFormat('id-ID').format(value);
}

// Show/hide supir section based on DP checkbox
document.querySelector('input[name="is_dp"]').addEventListener('change', function() {
    const supirSection = document.getElementById('supir_section');
    if (this.checked) {
        supirSection.style.display = 'block';
        addSupirRow(); // Add first row
    } else {
        supirSection.style.display = 'none';
        document.getElementById('supir_container').innerHTML = '';
    }
});

// Supir management
let supirRowCount = 0;

function addSupirRow() {
    supirRowCount++;
    const container = document.getElementById('supir_container');

    const row = document.createElement('div');
    row.className = 'supir-row grid grid-cols-1 md:grid-cols-4 gap-4 mb-4 p-4 border border-gray-200 rounded-md';
    row.id = `supir_row_${supirRowCount}`;

    row.innerHTML = `
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Supir</label>
            <select name="supir_id[]" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                <option value="">Pilih Supir</option>
                @foreach($masterSupir as $supir)
                    <option value="{{ $supir->id }}">{{ $supir->nama_lengkap }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Uang Muka</label>
            <div class="relative">
                <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                <input type="text" name="jumlah_uang_muka[]" class="w-full pl-12 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="0" oninput="formatNumber(this)" required>
            </div>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
            <div class="flex space-x-2">
                <input type="text" name="keterangan_supir[]" class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Keterangan tambahan">
                <button type="button" onclick="removeSupirRow(${supirRowCount})" class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;

    container.appendChild(row);
}

function removeSupirRow(rowId) {
    const row = document.getElementById(`supir_row_${rowId}`);
    if (row) {
        row.remove();
    }
}

document.getElementById('add_supir_btn').addEventListener('click', addSupirRow);

// Initialize if DP is checked
if (document.querySelector('input[name="is_dp"]').checked) {
    document.getElementById('supir_section').style.display = 'block';
    addSupirRow();
}
</script>
@endsection