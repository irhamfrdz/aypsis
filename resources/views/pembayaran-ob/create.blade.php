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

                        <!-- Realisasi Pembayaran per Supir (Table Format) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Realisasi Pembayaran per Supir <span class="text-red-500">*</span>
                            </label>
                            <p class="text-sm text-gray-600 mb-4">
                                <i class="fas fa-info-circle mr-1"></i>
                                Pilih supir dan masukkan realisasi pembayaran. Sistem akan menghitung selisih dengan DP yang dipilih secara otomatis.
                            </p>

                            <!-- Control Buttons -->
                            <div class="mb-4 flex flex-wrap gap-3">
                                <!-- Select All Button -->
                                <div class="p-3 bg-blue-50 border border-blue-200 rounded-md flex-1 min-w-64">
                                    <label class="flex items-center">
                                        <input type="checkbox" id="select-all" class="mr-2 text-blue-600">
                                        <span class="text-sm font-medium text-blue-800">
                                            <i class="fas fa-users mr-1"></i>
                                            Pilih Semua Supir Aktif ({{ $supirList->count() }} supir)
                                        </span>
                                    </label>
                                </div>

                                <!-- Add Driver Button -->
                                <div class="flex gap-2">
                                    <button type="button"
                                            id="add-supir-btn"
                                            onclick="openSupirModal()"
                                            class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-md text-sm font-medium transition duration-200">
                                        <i class="fas fa-plus mr-1"></i>
                                        Tambah Supir
                                    </button>
                                        Sembunyikan
                                    </button>
                                </div>
                            </div>

                            <!-- Table -->
                            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200 bg-white">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">
                                                Pilih
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">
                                                NIK
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Nama Supir
                                            </th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                                Uang Muka
                                            </th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-40">
                                                Realisasi <span class="text-red-500">*</span>
                                            </th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                                Selisih
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-48">
                                                Keterangan
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200" id="supir-table-body">
                                        @foreach($supirList as $index => $supir)
                                        <tr class="supir-row" data-supir-id="{{ $supir->id }}" style="display: none;">
                                            <td class="px-4 py-3">
                                                <input type="checkbox"
                                                       name="supir[]"
                                                       value="{{ $supir->id }}"
                                                       class="supir-checkbox text-blue-600 focus:ring-blue-500"
                                                       onchange="toggleSupirRow({{ $supir->id }})">
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-900 font-medium">
                                                {{ $supir->nik }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-900">
                                                {{ $supir->nama_lengkap }}
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <div class="dp-info text-sm" id="dp_{{ $supir->id }}">
                                                    <span class="text-gray-400 italic">-</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="relative">
                                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                        <span class="text-gray-500 text-sm">Rp</span>
                                                    </div>
                                                    <input type="text"
                                                           name="realisasi_display[{{ $supir->id }}]"
                                                           id="realisasi_display_{{ $supir->id }}"
                                                           class="realisasi-input block w-full pl-12 pr-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm disabled:bg-gray-100"
                                                           placeholder="0"
                                                           oninput="hitungSelisih({{ $supir->id }})"
                                                           disabled>
                                                    <input type="hidden"
                                                           name="jumlah[{{ $supir->id }}]"
                                                           id="jumlah_{{ $supir->id }}"
                                                           value="0">
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <div class="selisih-display" id="selisih_{{ $supir->id }}">
                                                    <span class="text-gray-400 italic text-sm">-</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <input type="text"
                                                       name="keterangan[{{ $supir->id }}]"
                                                       id="keterangan_{{ $supir->id }}"
                                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm disabled:bg-gray-100"
                                                       placeholder="Keterangan..."
                                                       disabled>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @error('supir')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @error('jumlah')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            <!-- Summary Section -->
                            <div id="summary-section" class="mt-6 p-4 bg-gradient-to-r from-blue-50 to-green-50 border border-blue-200 rounded-lg hidden">
                                <h4 class="text-sm font-bold text-gray-800 mb-3 flex items-center">
                                    <i class="fas fa-calculator mr-2"></i>
                                    Ringkasan Pembayaran
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                                    <div class="bg-white p-3 rounded border">
                                        <div class="text-gray-600">Total Supir</div>
                                        <div class="text-lg font-bold text-blue-600" id="total-supir">0</div>
                                    </div>
                                    <div class="bg-white p-3 rounded border">
                                        <div class="text-gray-600">Total Realisasi</div>
                                        <div class="text-lg font-bold text-green-600" id="total-realisasi">Rp 0</div>
                                    </div>
                                    <div class="bg-white p-3 rounded border">
                                        <div class="text-gray-600">Total Uang Muka</div>
                                        <div class="text-lg font-bold text-orange-600" id="total-dp">Rp 0</div>
                                    </div>
                                    <div class="bg-white p-3 rounded border">
                                        <div class="text-gray-600">Total Bayar</div>
                                        <div class="text-lg font-bold text-purple-600" id="total-bayar">Rp 0</div>
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

                        <!-- Uang Muka Selection Field -->
                        <div>
                            <label for="pembayaran_uang_muka_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Pilih Uang Muka yang Akan Digunakan <span class="text-gray-500">(Opsional)</span>
                            </label>
                            <select name="pembayaran_uang_muka_id"
                                    id="pembayaran_uang_muka_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('pembayaran_uang_muka_id') border-red-300 @enderror">
                                <option value="">-- Tidak Menggunakan Uang Muka --</option>
                                @foreach($uangMukaBelumTerpakaiList as $uangMuka)
                                    <option value="{{ $uangMuka->id }}" {{ old('pembayaran_uang_muka_id') == $uangMuka->id ? 'selected' : '' }}>
                                        {{ $uangMuka->nomor_pembayaran }} - {{ \Carbon\Carbon::parse($uangMuka->tanggal_pembayaran)->format('d/m/Y') }} -
                                        {{ $uangMuka->formatted_kegiatan }} -
                                        {{ count($uangMuka->supir_ids) }} supir -
                                        Rp {{ number_format($uangMuka->total_pembayaran, 0, ',', '.') }}
                                        @if($uangMuka->keterangan)
                                            - {{ Str::limit($uangMuka->keterangan, 30) }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('pembayaran_uang_muka_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            <!-- Uang Muka Selection Info -->
                            <div id="uang-muka-selection-info" class="mt-2 p-3 bg-green-50 border border-green-200 rounded-md hidden">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                                    </div>
                                    <div class="ml-2">
                                        <div class="text-sm font-medium text-green-800">Uang Muka Dipilih:</div>
                                        <div class="text-sm text-green-700">
                                            <span id="selected-uang-muka-info">-</span>
                                        </div>
                                        <div class="text-xs text-green-600 mt-1">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            Uang Muka ini akan dipotongkan dari total pembayaran dan statusnya akan berubah menjadi "Terpakai"
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($uangMukaBelumTerpakaiList->count() > 0)
                                <p class="mt-1 text-sm text-gray-500">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Pilih Uang Muka yang akan digunakan untuk pembayaran ini. Uang Muka yang dipilih akan diubah statusnya menjadi "Terpakai"
                                </p>
                            @else
                                <p class="mt-1 text-sm text-yellow-600">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    Tidak ada Uang Muka yang tersedia atau semua Uang Muka sudah terpakai
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
// Uang Muka Data untuk JavaScript
const uangMukaData = {
    @foreach($uangMukaBelumTerpakaiList as $uangMuka)
        '{{ $uangMuka->id }}': {
            nomor: '{{ $uangMuka->nomor_pembayaran }}',
            total: {{ $uangMuka->total_pembayaran }},
            supir_count: {{ count($uangMuka->supir_ids) }},
            tanggal: '{{ \Carbon\Carbon::parse($uangMuka->tanggal_pembayaran)->format('d/m/Y') }}',
            kegiatan: '{{ $uangMuka->formatted_kegiatan }}',
            supir_names: @json($uangMuka->supir_names ?? []),
            supir_ids: @json($uangMuka->supir_ids ?? []),
            jumlah_per_supir: @json($uangMuka->jumlah_per_supir ?? [])
        },
    @endforeach
};

// Current selected Uang Muka data
let currentUangMukaData = {};

// Auto generate nomor pembayaran
async function generateNomor() {
    try {
        const kasBankId = document.getElementById('kas_bank').value;
        if (!kasBankId) {
            alert('Pilih akun Kas/Bank terlebih dahulu untuk generate nomor pembayaran');
            return;
        }

        let url = '{{ route('pembayaran-ob.generate-nomor') }}';
        url += '?kas_bank_id=' + kasBankId;

        const response = await fetch(url);
        const data = await response.json();

        if (data.nomor_pembayaran) {
            document.getElementById('nomor_pembayaran').value = data.nomor_pembayaran;
        } else {
            alert('Error: ' + (data.message || data.error || 'Unexpected response format'));
        }
    } catch (error) {
        console.error('Error generating nomor:', error);
        const today = new Date();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const year = String(today.getFullYear()).slice(-2);
        const random = Math.floor(Math.random() * 1000000).toString().padStart(6, '0');
        document.getElementById('nomor_pembayaran').value = `KBJ-${month}-${year}-${random}`;
    }
}

// Toggle individual supir row
function toggleSupirRow(supirId) {
    const checkbox = document.querySelector(`input[name="supir[]"][value="${supirId}"]`);
    const row = document.querySelector(`.supir-row[data-supir-id="${supirId}"]`);
    const realisasiInput = document.getElementById(`realisasi_display_${supirId}`);
    const keteranganInput = document.getElementById(`keterangan_${supirId}`);

    if (checkbox.checked) {
        row.style.display = 'table-row';
        realisasiInput.disabled = false;
        realisasiInput.required = true;
        keteranganInput.disabled = false;

        // Auto-fill dengan Uang Muka jika tersedia
        const selectedUangMukaId = document.getElementById('pembayaran_uang_muka_id').value;
        if (selectedUangMukaId && currentUangMukaData[supirId]) {
            const uangMukaAmount = currentUangMukaData[supirId];
            realisasiInput.value = formatNumber(uangMukaAmount);
            document.getElementById(`jumlah_${supirId}`).value = uangMukaAmount;
            hitungSelisih(supirId);
        }
    } else {
        row.style.display = 'none';
        realisasiInput.disabled = true;
        realisasiInput.required = false;
        realisasiInput.value = '';
        keteranganInput.disabled = true;
        keteranganInput.value = '';
        document.getElementById(`jumlah_${supirId}`).value = '0';

        // Reset selisih display
        document.getElementById(`selisih_${supirId}`).innerHTML = '<span class="text-gray-400 italic text-sm">-</span>';
    }

    updateSummary();
}

// Buka modal untuk tambah supir
function openSupirModal() {
    const modal = document.getElementById('supir-modal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden'; // Prevent background scrolling

    // Reset search
    document.getElementById('supir-search').value = '';
    filterSupirOptions('');
}

// Tutup modal supir
function closeSupirModal() {
    const modal = document.getElementById('supir-modal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto'; // Re-enable background scrolling
}

// Filter supir options berdasarkan pencarian
function filterSupirOptions(searchTerm) {
    const options = document.querySelectorAll('.supir-option');
    const term = searchTerm.toLowerCase();

    options.forEach(function(option) {
        const namaLengkap = option.getAttribute('data-supir-nama').toLowerCase();
        const namaPanggilan = (option.getAttribute('data-supir-panggilan') || '').toLowerCase();
        const nik = option.getAttribute('data-supir-nik').toLowerCase();

        if (namaLengkap.includes(term) || namaPanggilan.includes(term) || nik.includes(term)) {
            option.style.display = 'block';
        } else {
            option.style.display = 'none';
        }
    });
}

// Pilih supir dari modal
function selectSupirFromModal(supirId) {
    // Cek apakah supir sudah dipilih
    const checkbox = document.querySelector(`input[name="supir[]"][value="${supirId}"]`);
    if (checkbox && !checkbox.checked) {
        checkbox.checked = true;
        toggleSupirRow(supirId);

        // Tutup modal setelah memilih
        closeSupirModal();

        // Scroll ke baris supir yang baru ditambah
        const row = document.querySelector(`.supir-row[data-supir-id="${supirId}"]`);
        if (row) {
            row.scrollIntoView({ behavior: 'smooth', block: 'center' });

            // Highlight row sejenak
            row.style.backgroundColor = '#fef3c7';
            setTimeout(function() {
                row.style.backgroundColor = '';
            }, 2000);
        }
    } else {
        alert('Supir ini sudah dipilih!');
    }
}

// Select/deselect all supir
function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('select-all');
    const supirCheckboxes = document.querySelectorAll('input[name="supir[]"]');

    supirCheckboxes.forEach(function(checkbox) {
        checkbox.checked = selectAllCheckbox.checked;
        toggleSupirRow(checkbox.value);
    });
}

// Format number dengan pemisah ribuan
function formatNumber(num) {
    if (!num || num === '') return '';
    return new Intl.NumberFormat('id-ID').format(num);
}

// Format currency input
function formatCurrency(input, supirId) {
    let value = input.value.replace(/[^\d]/g, '');
    document.getElementById(`jumlah_${supirId}`).value = value || '0';

    if (value) {
        input.value = formatNumber(value);
    } else {
        input.value = '';
    }
}

// Hitung selisih untuk supir tertentu
function hitungSelisih(supirId) {
    const realisasiInput = document.getElementById(`realisasi_display_${supirId}`);
    const selisihDiv = document.getElementById(`selisih_${supirId}`);

    // Format currency
    formatCurrency(realisasiInput, supirId);

    const realisasiAmount = parseInt(document.getElementById(`jumlah_${supirId}`).value) || 0;
    const uangMukaAmount = currentUangMukaData[supirId] || 0;

    if (realisasiAmount > 0) {
        if (uangMukaAmount > 0) {
            const selisih = realisasiAmount - uangMukaAmount;
            if (selisih > 0) {
                selisihDiv.innerHTML = `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">+${formatNumber(selisih)}</span>`;
            } else if (selisih < 0) {
                selisihDiv.innerHTML = `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">${formatNumber(selisih)}</span>`;
            } else {
                selisihDiv.innerHTML = `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">0</span>`;
            }
        } else {
            selisihDiv.innerHTML = `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Penuh</span>`;
        }
    } else {
        selisihDiv.innerHTML = '<span class="text-gray-400 italic text-sm">-</span>';
    }

    updateSummary();
}

// Update Uang Muka display untuk semua supir
function updateUangMukaDisplay() {
    const selectedUangMukaId = document.getElementById('pembayaran_uang_muka_id').value;

    // Reset currentUangMukaData
    currentUangMukaData = {};

    // Update Uang Muka display untuk semua supir
    @foreach($supirList as $supir)
        const uangMukaDiv{{ $supir->id }} = document.getElementById('dp_{{ $supir->id }}');
        let uangMukaAmount{{ $supir->id }} = 0;

        if (selectedUangMukaId && uangMukaData[selectedUangMukaId] && uangMukaData[selectedUangMukaId].jumlah_per_supir && uangMukaData[selectedUangMukaId].jumlah_per_supir['{{ $supir->id }}']) {
            uangMukaAmount{{ $supir->id }} = uangMukaData[selectedUangMukaId].jumlah_per_supir['{{ $supir->id }}'];
            currentUangMukaData['{{ $supir->id }}'] = uangMukaAmount{{ $supir->id }};
            uangMukaDiv{{ $supir->id }}.innerHTML = `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Rp ${formatNumber(uangMukaAmount{{ $supir->id }})}</span>`;
        } else {
            uangMukaDiv{{ $supir->id }}.innerHTML = '<span class="text-gray-400 italic">-</span>';
        }

        // Update selisih jika supir sudah dipilih
        const checkbox{{ $supir->id }} = document.querySelector(`input[name="supir[]"][value="{{ $supir->id }}"]`);
        if (checkbox{{ $supir->id }} && checkbox{{ $supir->id }}.checked) {
            hitungSelisih('{{ $supir->id }}');
        }
    @endforeach

    updateSummary();
}

// Update summary calculation
function updateSummary() {
    const selectedSupir = document.querySelectorAll('input[name="supir[]"]:checked');
    let totalRealisasi = 0;
    let totalDp = 0;

    selectedSupir.forEach(function(checkbox) {
        const supirId = checkbox.value;
        const realisasiAmount = parseInt(document.getElementById(`jumlah_${supirId}`).value) || 0;
        const uangMukaAmount = currentUangMukaData[supirId] || 0;

        totalRealisasi += realisasiAmount;
        totalDp += uangMukaAmount;
    });

    const totalBayar = totalRealisasi - totalDp;

    if (selectedSupir.length > 0) {
        document.getElementById('summary-section').classList.remove('hidden');
        document.getElementById('total-supir').textContent = selectedSupir.length;
        document.getElementById('total-realisasi').textContent = 'Rp ' + formatNumber(totalRealisasi);
        document.getElementById('total-dp').textContent = 'Rp ' + formatNumber(totalDp);
        document.getElementById('total-bayar').textContent = 'Rp ' + formatNumber(Math.max(0, totalBayar));
    } else {
        document.getElementById('summary-section').classList.add('hidden');
    }
}


// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Auto generate nomor on page load
    const nomorField = document.getElementById('nomor_pembayaran');
    if (!nomorField.value.trim()) {
        generateNomor();
    }

    // Setup select all checkbox
    document.getElementById('select-all').addEventListener('change', toggleSelectAll);

    // Setup individual supir checkboxes
    document.querySelectorAll('input[name="supir[]"]').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            toggleSupirRow(this.value);
        });
    });

    // Event listener untuk search supir di modal
    document.getElementById('supir-search').addEventListener('input', function() {
        filterSupirOptions(this.value);
    });

    // Event listener untuk click pada supir options
    document.querySelectorAll('.supir-option').forEach(function(option) {
        option.addEventListener('click', function() {
            const supirId = this.getAttribute('data-supir-id');
            selectSupirFromModal(supirId);
        });
    });

    // Close modal when clicking outside
    document.getElementById('supir-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeSupirModal();
        }
    });

    // Close modal dengan ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('supir-modal');
            if (!modal.classList.contains('hidden')) {
                closeSupirModal();
            }
        }
    });

    // Handle kas/bank selection
    document.getElementById('kas_bank').addEventListener('change', function(e) {
        if (e.target.value) {
            generateNomor();
        }
    });

    // Handle Uang Muka selection change
    document.getElementById('pembayaran_uang_muka_id').addEventListener('change', function(e) {
        const selectedUangMukaId = e.target.value;
        const uangMukaInfoDiv = document.getElementById('uang-muka-selection-info');
        const uangMukaInfoSpan = document.getElementById('selected-uang-muka-info');

        if (selectedUangMukaId && uangMukaData[selectedUangMukaId]) {
            const uangMuka = uangMukaData[selectedUangMukaId];
            uangMukaInfoDiv.classList.remove('hidden');

            // Auto-select supir dari Uang Muka
            if (uangMuka.supir_ids && uangMuka.supir_ids.length > 0) {
                uangMuka.supir_ids.forEach(function(supirId) {
                    const checkbox = document.querySelector(`input[name="supir[]"][value="${supirId}"]`);
                    if (checkbox && !checkbox.checked) {
                        checkbox.checked = true;
                        toggleSupirRow(supirId);
                    }
                });
            }

            let supirNamesText = '';
            if (uangMuka.supir_names && uangMuka.supir_names.length > 0) {
                supirNamesText = `<br><span class="text-xs text-green-600"><strong>Supir:</strong> ${uangMuka.supir_names.join(', ')}</span>`;
            }

            uangMukaInfoSpan.innerHTML = `
                <strong>${uangMuka.nomor}</strong> - ${uangMuka.tanggal} - ${uangMuka.kegiatan} - ${uangMuka.supir_count} supir<br>
                <span class="font-semibold text-green-800">Nilai Uang Muka: Rp ${new Intl.NumberFormat('id-ID').format(uangMuka.total)}</span>
                ${supirNamesText}
                <br><span class="text-xs text-blue-600 mt-1"><i class="fas fa-magic mr-1"></i>Supir telah dipilih otomatis dari Uang Muka ini</span>
            `;
        } else {
            uangMukaInfoDiv.classList.add('hidden');
        }

        updateUangMukaDisplay();
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

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const selectedSupir = document.querySelectorAll('input[name="supir[]"]:checked');
        if (selectedSupir.length === 0) {
            e.preventDefault();
            alert('Harap pilih minimal satu supir');
            return false;
        }

        let hasEmptyRealisasi = false;
        selectedSupir.forEach(function(checkbox) {
            const supirId = checkbox.value;
            const jumlahInput = document.getElementById(`jumlah_${supirId}`);
            if (!jumlahInput.value || jumlahInput.value === '0') {
                hasEmptyRealisasi = true;
            }
        });

        if (hasEmptyRealisasi) {
            e.preventDefault();
            alert('Harap isi semua realisasi pembayaran untuk supir yang dipilih');
            return false;
        }
    });

    // Load old input if any
    @if(old('supir'))
        const oldSupir = @json(old('supir'));
        oldSupir.forEach(function(supirId) {
            const checkbox = document.querySelector(`input[name="supir[]"][value="${supirId}"]`);
            if (checkbox) {
                checkbox.checked = true;
                toggleSupirRow(supirId);
            }
        });
    @endif
});
</script>

<!-- Modal Popup untuk Tambah Supir -->
<div id="supir-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-3 border-b">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-user-plus mr-2 text-green-500"></i>
                Pilih Supir untuk Ditambahkan
            </h3>
            <button type="button" onclick="closeSupirModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="py-4">
            <!-- Search Box -->
            <div class="mb-4">
                <div class="relative">
                    <input type="text"
                           id="supir-search"
                           placeholder="Cari berdasarkan nama panggilan, nama lengkap, atau NIK..."
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
            </div>

            <!-- Supir List -->
            <div class="max-h-96 overflow-y-auto border border-gray-200 rounded-lg">
                <div class="divide-y divide-gray-200">
                    @foreach($supirList as $supir)
                    <div class="supir-option p-4 hover:bg-blue-50 cursor-pointer border-l-4 border-transparent hover:border-blue-400 transition-all duration-200"
                         data-supir-id="{{ $supir->id }}"
                         data-supir-nik="{{ $supir->nik }}"
                         data-supir-nama="{{ $supir->nama_lengkap }}"
                         data-supir-panggilan="{{ $supir->nama_panggilan }}">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center shadow-md">
                                        <i class="fas fa-user text-white text-lg"></i>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-1">
                                        <h4 class="text-base font-semibold text-gray-900">{{ $supir->nama_panggilan ?? $supir->nama_lengkap }}</h4>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            <i class="fas fa-id-card mr-1"></i>
                                            Supir
                                        </span>
                                    </div>
                                    <div class="flex items-center text-sm text-gray-600 space-x-3">
                                        <div class="flex items-center">
                                            <i class="fas fa-user mr-1 text-gray-400"></i>
                                            <span class="font-medium">Nama Lengkap:</span>
                                            <span class="ml-1 text-gray-700">{{ $supir->nama_lengkap }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-fingerprint mr-1 text-gray-400"></i>
                                            <span class="font-medium">NIK:</span>
                                            <span class="ml-1 font-mono bg-gray-100 px-2 py-0.5 rounded text-gray-800">{{ $supir->nik }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 border border-green-200">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Tersedia
                                </span>
                                <div class="text-xs text-gray-400 mt-1">
                                    Klik untuk pilih
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex justify-end pt-4 border-t space-x-3">
            <button type="button"
                    onclick="closeSupirModal()"
                    class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-md text-sm font-medium transition duration-200">
                <i class="fas fa-times mr-1"></i>
                Batal
            </button>
        </div>
    </div>
</div>

@endsection
