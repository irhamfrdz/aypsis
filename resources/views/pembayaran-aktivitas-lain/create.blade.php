@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-6">
    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h1 class="text-xl font-semibold text-gray-800">Tambah Pembayaran Aktivitas Lain</h1>
                <p class="text-sm text-blue-600 mt-1">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        📊 Double Book Accounting
                    </span>
                    Otomatis jurnal akuntansi dengan sistem pembukuan ganda
                </p>
            </div>
            <a href="{{ route('pembayaran-aktivitas-lain.index') }}" class="inline-flex items-center px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium text-xs rounded-md transition duration-150 ease-in-out">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </div>

        <form action="{{ route('pembayaran-aktivitas-lain.store') }}" method="POST" class="p-6">
            @csrf
            
            <!-- Double Book Accounting Info -->
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-blue-800 mb-2">📊 Sistem Double Book Accounting</h3>
                        <div class="text-sm text-blue-700 space-y-1">
                            <p><strong>Otomatis Jurnal Akuntansi:</strong></p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                                <div class="bg-white p-3 rounded border border-blue-200">
                                    <p class="font-medium text-green-700">✅ Jika pilih DEBIT:</p>
                                    <p class="text-xs mt-1">• <strong>Dr.</strong> Akun yang dipilih (Biaya/Beban) <span class="text-green-600">+</span></p>
                                    <p class="text-xs">• <strong>Cr.</strong> Akun Bank yang dipilih <span class="text-red-600">-</span></p>
                                </div>
                                <div class="bg-white p-3 rounded border border-blue-200">
                                    <p class="font-medium text-blue-700">✅ Jika pilih KREDIT:</p>
                                    <p class="text-xs mt-1">• <strong>Dr.</strong> Akun Bank yang dipilih <span class="text-green-600">+</span></p>
                                    <p class="text-xs">• <strong>Cr.</strong> Akun yang dipilih (Biaya/Beban) <span class="text-red-600">-</span></p>
                                </div>
                            </div>
                            <p class="text-xs mt-3 font-medium">💡 <strong>Keuntungan:</strong> Tidak perlu input manual jurnal, otomatis seimbang (Debit = Kredit), akurat & konsisten</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nomor -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nomor</label>
                    <input type="text" value="{{ $nomor }}" readonly class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-sm">
                </div>

                <!-- Tanggal -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm @error('tanggal') border-red-500 @enderror">
                    @error('tanggal')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jenis Aktivitas -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Aktivitas <span class="text-red-500">*</span></label>
                    <select name="jenis_aktivitas" id="jenis_aktivitas" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm @error('jenis_aktivitas') border-red-500 @enderror">
                        <option value="">Pilih Jenis Aktivitas</option>
                        <option value="Pembayaran Kendaraan" {{ old('jenis_aktivitas') == 'Pembayaran Kendaraan' ? 'selected' : '' }}>Pembayaran Kendaraan</option>
                        <option value="Pembayaran Kapal" {{ old('jenis_aktivitas') == 'Pembayaran Kapal' ? 'selected' : '' }}>Pembayaran Kapal</option>
                        <option value="Pembayaran Lain Lain" {{ old('jenis_aktivitas') == 'Pembayaran Lain Lain' ? 'selected' : '' }}>Pembayaran Lain Lain</option>
                    </select>
                    @error('jenis_aktivitas')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Sub Jenis Kendaraan (Hidden by default) -->
                <div id="sub_jenis_kendaraan" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sub Jenis Kendaraan <span class="text-red-500">*</span></label>
                    <select name="sub_jenis_kendaraan" id="sub_jenis_kendaraan_select" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm @error('sub_jenis_kendaraan') border-red-500 @enderror">
                        <option value="">Pilih Sub Jenis</option>
                        <option value="STNK" {{ old('sub_jenis_kendaraan') == 'STNK' ? 'selected' : '' }}>STNK</option>
                        <option value="KIR" {{ old('sub_jenis_kendaraan') == 'KIR' ? 'selected' : '' }}>KIR</option>
                        <option value="Plat" {{ old('sub_jenis_kendaraan') == 'Plat' ? 'selected' : '' }}>Plat</option>
                        <option value="Lain Lain" {{ old('sub_jenis_kendaraan') == 'Lain Lain' ? 'selected' : '' }}>Lain Lain</option>
                    </select>
                    @error('sub_jenis_kendaraan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nomor Polisi (Hidden by default) -->
                <div id="nomor_polisi_field" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Polisi <span class="text-red-500">*</span></label>
                    <select name="nomor_polisi" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm @error('nomor_polisi') border-red-500 @enderror">
                        <option value="">Pilih Nomor Polisi</option>
                        @foreach($mobils as $mobil)
                            <option value="{{ $mobil->nomor_polisi }}" {{ old('nomor_polisi') == $mobil->nomor_polisi ? 'selected' : '' }}>
                                {{ $mobil->nomor_polisi }} - {{ $mobil->merek }} {{ $mobil->jenis }}
                            </option>
                        @endforeach
                    </select>
                    @error('nomor_polisi')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nomor Voyage (Hidden by default) -->
                <div id="nomor_voyage_field" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Voyage <span class="text-red-500">*</span></label>
                    <select name="nomor_voyage" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm @error('nomor_voyage') border-red-500 @enderror">
                        <option value="">Pilih Nomor Voyage</option>
                        @foreach($voyages as $voyage)
                            <option value="{{ $voyage->voyage }}" {{ old('nomor_voyage') == $voyage->voyage ? 'selected' : '' }}>
                                {{ $voyage->voyage }} - {{ $voyage->nama_kapal }} ({{ $voyage->source }})
                            </option>
                        @endforeach
                    </select>
                    @error('nomor_voyage')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jumlah -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="jumlah" value="{{ old('jumlah') }}" required min="0" step="0.01" placeholder="0" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm @error('jumlah') border-red-500 @enderror">
                    @error('jumlah')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Debit/Kredit (Double Book) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Jenis Transaksi (Double Book) <span class="text-red-500">*</span>
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 ml-1">
                            📊 Auto Jurnal
                        </span>
                    </label>
                    <select name="debit_kredit" id="debit_kredit" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm @error('debit_kredit') border-red-500 @enderror">
                        <option value="">Pilih Jenis Transaksi</option>
                        <option value="debit" {{ old('debit_kredit') == 'debit' ? 'selected' : '' }}>DEBIT (Biaya/Beban bertambah, Bank berkurang)</option>
                        <option value="kredit" {{ old('debit_kredit') == 'kredit' ? 'selected' : '' }}>KREDIT (Bank bertambah, Biaya/Beban berkurang)</option>
                    </select>
                    @error('debit_kredit')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    
                    <!-- Dynamic Journal Preview -->
                    <div id="journal_preview" class="mt-2 p-2 bg-gray-50 border border-gray-200 rounded text-xs hidden">
                        <p class="font-medium text-gray-700 mb-1">📋 Preview Jurnal Akuntansi:</p>
                        <div id="journal_content" class="text-gray-600">
                            <!-- Content will be populated by JavaScript -->
                        </div>
                    </div>
                </div>

                <!-- Akun COA -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Akun COA <span class="text-red-500">*</span></label>
                    <select name="akun_coa_id" id="akun_coa_select" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm @error('akun_coa_id') border-red-500 @enderror">
                        <option value="">Pilih Akun COA</option>
                        @foreach($akunBiaya as $akun)
                            <option value="{{ $akun->id }}" data-nama="{{ $akun->nama_akun }}" data-kode="{{ $akun->kode_nomor }}" {{ old('akun_coa_id') == $akun->id ? 'selected' : '' }}>
                                {{ $akun->kode_nomor }} - {{ $akun->nama_akun }}
                            </option>
                        @endforeach
                    </select>
                    @error('akun_coa_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Akun Bank -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Bank/Kas <span class="text-red-500">*</span></label>
                    <select name="akun_bank_id" id="akun_bank_select" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm @error('akun_bank_id') border-red-500 @enderror">
                        <option value="">Pilih Bank/Kas</option>
                        @foreach($akunBank as $akun)
                            <option value="{{ $akun->id }}" data-nama="{{ $akun->nama_akun }}" data-kode="{{ $akun->kode_nomor }}" {{ old('akun_bank_id') == $akun->id ? 'selected' : '' }}>
                                {{ $akun->kode_nomor }} - {{ $akun->nama_akun }}
                            </option>
                        @endforeach
                    </select>
                    @error('akun_bank_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Penerima -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Penerima <span class="text-red-500">*</span></label>
                    <div class="space-y-2">
                        <select id="penerima_dropdown" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                            <option value="">Pilih dari Karyawan</option>
                            @foreach($karyawans as $karyawan)
                                <option value="{{ $karyawan->nama_lengkap }}">{{ $karyawan->nama_lengkap }} - {{ $karyawan->pekerjaan }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="penerima" id="penerima_input" value="{{ old('penerima') }}" placeholder="Atau ketik nama penerima..." required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm @error('penerima') border-red-500 @enderror">
                    </div>
                    @error('penerima')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Keterangan -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan <span class="text-red-500">*</span></label>
                    <textarea name="keterangan" rows="4" placeholder="Keterangan tambahan..." required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm @error('keterangan') border-red-500 @enderror">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('pembayaran-aktivitas-lain.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium text-sm rounded-md transition">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm rounded-md transition">
                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal for Vehicle Master Data -->
<div id="vehicleMasterModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Edit Data Master Kendaraan</h3>
                <button id="closeModal" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="vehicleMasterForm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Polisi</label>
                        <input type="text" id="modal_nomor_polisi" readonly class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Merek</label>
                        <input type="text" id="modal_merek" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis</label>
                        <input type="text" id="modal_jenis" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                        <input type="number" id="modal_tahun" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Warna</label>
                        <input type="text" id="modal_warna" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select id="modal_status" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                            <option value="aktif">Aktif</option>
                            <option value="tidak_aktif">Tidak Aktif</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                    </div>
                </div>
                
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                    <textarea id="modal_keterangan" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm" placeholder="Keterangan tambahan..."></textarea>
                </div>
            </form>
            
            <div class="flex justify-end gap-3 mt-6">
                <button id="cancelModalBtn" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium text-sm rounded-md transition">
                    Batal
                </button>
                <button id="saveVehicleBtn" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm rounded-md transition">
                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const jenisAktivitas = document.getElementById('jenis_aktivitas');
    const subJenisKendaraan = document.getElementById('sub_jenis_kendaraan');
    const subJenisSelect = document.getElementById('sub_jenis_kendaraan_select');
    const nomorPolisiField = document.getElementById('nomor_polisi_field');
    const nomorPolisiSelect = nomorPolisiField.querySelector('select');
    const nomorVoyageField = document.getElementById('nomor_voyage_field');
    const nomorVoyageSelect = nomorVoyageField.querySelector('select');

    function toggleSubJenisKendaraan() {
        if (jenisAktivitas.value === 'Pembayaran Kendaraan') {
            subJenisKendaraan.classList.remove('hidden');
            subJenisSelect.setAttribute('required', 'required');
        } else {
            subJenisKendaraan.classList.add('hidden');
            subJenisSelect.removeAttribute('required');
            subJenisSelect.value = '';
            // Hide nomor polisi when jenis aktivitas changes
            nomorPolisiField.classList.add('hidden');
            nomorPolisiSelect.removeAttribute('required');
            nomorPolisiSelect.value = '';
        }
        
        // Handle voyage dropdown for kapal
        if (jenisAktivitas.value === 'Pembayaran Kapal') {
            nomorVoyageField.classList.remove('hidden');
            nomorVoyageSelect.setAttribute('required', 'required');
        } else {
            nomorVoyageField.classList.add('hidden');
            nomorVoyageSelect.removeAttribute('required');
            nomorVoyageSelect.value = '';
        }
    }

    function toggleNomorPolisi() {
        const subJenis = subJenisSelect.value;
        if (subJenis === 'Plat' || subJenis === 'STNK' || subJenis === 'KIR') {
            nomorPolisiField.classList.remove('hidden');
            nomorPolisiSelect.setAttribute('required', 'required');
        } else {
            nomorPolisiField.classList.add('hidden');
            nomorPolisiSelect.removeAttribute('required');
            nomorPolisiSelect.value = '';
        }
    }

    // Initial check on page load
    toggleSubJenisKendaraan();
    toggleNomorPolisi();

    // Listen for changes
    jenisAktivitas.addEventListener('change', toggleSubJenisKendaraan);
    subJenisSelect.addEventListener('change', toggleNomorPolisi);
    
    // Handle penerima dropdown
    const penerimaDropdown = document.getElementById('penerima_dropdown');
    const penerimaInput = document.getElementById('penerima_input');
    
    penerimaDropdown.addEventListener('change', function() {
        if (this.value) {
            penerimaInput.value = this.value;
        }
    });
    
    // Double Book Accounting - Dynamic Journal Preview
    const debitKreditSelect = document.getElementById('debit_kredit');
    const akunCoaSelect = document.getElementById('akun_coa_select');
    const akunBankSelect = document.getElementById('akun_bank_select');
    const jumlahInput = document.querySelector('input[name="jumlah"]');
    const journalPreview = document.getElementById('journal_preview');
    const journalContent = document.getElementById('journal_content');
    
    function updateJournalPreview() {
        const jenisTransaksi = debitKreditSelect.value;
        const akunCoa = akunCoaSelect.selectedOptions[0];
        const akunBank = akunBankSelect.selectedOptions[0];
        const jumlah = parseFloat(jumlahInput.value) || 0;
        
        if (!jenisTransaksi || !akunCoa || !akunBank || jumlah <= 0) {
            journalPreview.classList.add('hidden');
            return;
        }
        
        const akunCoaNama = akunCoa.dataset.nama || akunCoa.textContent;
        const akunBankNama = akunBank.dataset.nama || akunBank.textContent;
        const jumlahFormatted = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(jumlah);
        
        let journalHtml = '';
        
        if (jenisTransaksi === 'debit') {
            journalHtml = `
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-green-50 p-2 rounded border border-green-200">
                        <p class="font-medium text-green-700">DEBIT (+)</p>
                        <p class="text-xs text-green-600">${akunCoaNama}</p>
                        <p class="font-bold text-green-700">${jumlahFormatted}</p>
                    </div>
                    <div class="bg-red-50 p-2 rounded border border-red-200">
                        <p class="font-medium text-red-700">KREDIT (-)</p>
                        <p class="text-xs text-red-600">${akunBankNama}</p>
                        <p class="font-bold text-red-700">${jumlahFormatted}</p>
                    </div>
                </div>
                <p class="text-xs text-gray-600 mt-1">💡 <strong>Efek:</strong> ${akunCoaNama} bertambah, ${akunBankNama} berkurang</p>
            `;
        } else {
            journalHtml = `
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-green-50 p-2 rounded border border-green-200">
                        <p class="font-medium text-green-700">DEBIT (+)</p>
                        <p class="text-xs text-green-600">${akunBankNama}</p>
                        <p class="font-bold text-green-700">${jumlahFormatted}</p>
                    </div>
                    <div class="bg-red-50 p-2 rounded border border-red-200">
                        <p class="font-medium text-red-700">KREDIT (-)</p>
                        <p class="text-xs text-red-600">${akunCoaNama}</p>
                        <p class="font-bold text-red-700">${jumlahFormatted}</p>
                    </div>
                </div>
                <p class="text-xs text-gray-600 mt-1">💡 <strong>Efek:</strong> ${akunBankNama} bertambah, ${akunCoaNama} berkurang</p>
            `;
        }
        
        journalContent.innerHTML = journalHtml;
        journalPreview.classList.remove('hidden');
    }
    
    // Add event listeners for real-time preview
    debitKreditSelect.addEventListener('change', updateJournalPreview);
    akunCoaSelect.addEventListener('change', updateJournalPreview);
    akunBankSelect.addEventListener('change', updateJournalPreview);
    jumlahInput.addEventListener('input', updateJournalPreview);
    
    // Initial preview check
    updateJournalPreview();
    
    // Handle form submission with vehicle master data check
    const form = document.querySelector('form');
    const vehicleMasterModal = document.getElementById('vehicleMasterModal');
    const closeModalBtn = document.getElementById('closeModal');
    const cancelModalBtn = document.getElementById('cancelModalBtn');
    const saveVehicleBtn = document.getElementById('saveVehicleBtn');
    
    let isSubmittingAfterModal = false;
    
    form.addEventListener('submit', function(e) {
        if (isSubmittingAfterModal) {
            return; // Allow normal submission
        }
        
        const jenisAktivitas = document.getElementById('jenis_aktivitas').value;
        const nomorPolisi = document.querySelector('select[name="nomor_polisi"]').value;
        
        if (jenisAktivitas === 'Pembayaran Kendaraan' && nomorPolisi) {
            e.preventDefault();
            
            // Show confirmation dialog
            if (confirm('Apakah Anda ingin mengubah data master kendaraan untuk plat nomor: ' + nomorPolisi + '?')) {
                showVehicleMasterModal(nomorPolisi);
            } else {
                // Continue with normal form submission
                isSubmittingAfterModal = true;
                form.submit();
            }
        }
    });
    
    function showVehicleMasterModal(nomorPolisi) {
        // Get vehicle data from the dropdown
        const mobilSelect = document.querySelector('select[name="nomor_polisi"]');
        const selectedOption = mobilSelect.options[mobilSelect.selectedIndex];
        const mobilText = selectedOption.text;
        
        // Parse vehicle data from option text (format: "PLAT - MEREK JENIS")
        const parts = mobilText.split(' - ');
        const plat = parts[0] || '';
        const merekJenis = parts[1] || '';
        const merekJenisParts = merekJenis.split(' ');
        const merek = merekJenisParts[0] || '';
        const jenis = merekJenisParts.slice(1).join(' ') || '';
        
        // Populate modal fields
        document.getElementById('modal_nomor_polisi').value = plat;
        document.getElementById('modal_merek').value = merek;
        document.getElementById('modal_jenis').value = jenis;
        document.getElementById('modal_tahun').value = '';
        document.getElementById('modal_warna').value = '';
        document.getElementById('modal_status').value = 'aktif';
        document.getElementById('modal_keterangan').value = '';
        
        // Show modal
        vehicleMasterModal.classList.remove('hidden');
    }
    
    function closeModal() {
        vehicleMasterModal.classList.add('hidden');
    }
    
    // Modal event listeners
    closeModalBtn.addEventListener('click', closeModal);
    cancelModalBtn.addEventListener('click', closeModal);
    
    saveVehicleBtn.addEventListener('click', function() {
        // Here you can add AJAX call to update vehicle master data
        const vehicleData = {
            nomor_polisi: document.getElementById('modal_nomor_polisi').value,
            merek: document.getElementById('modal_merek').value,
            jenis: document.getElementById('modal_jenis').value,
            tahun: document.getElementById('modal_tahun').value,
            warna: document.getElementById('modal_warna').value,
            status: document.getElementById('modal_status').value,
            keterangan: document.getElementById('modal_keterangan').value
        };
        
        // Show loading state
        saveVehicleBtn.disabled = true;
        saveVehicleBtn.innerHTML = '<svg class="w-4 h-4 inline-block mr-1 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Menyimpan...';
        
        // Simulate API call (replace with actual AJAX call)
        setTimeout(() => {
            alert('Data master kendaraan berhasil diperbarui!');
            closeModal();
            
            // Reset button
            saveVehicleBtn.disabled = false;
            saveVehicleBtn.innerHTML = '<svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Simpan Perubahan';
            
            // Continue with form submission
            isSubmittingAfterModal = true;
            form.submit();
        }, 2000);
        
        // TODO: Replace above setTimeout with actual AJAX call like:
        /*
        fetch('/api/mobils/update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(vehicleData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Data master kendaraan berhasil diperbarui!');
                closeModal();
                isSubmittingAfterModal = true;
                form.submit();
            } else {
                alert('Gagal memperbarui data: ' + data.message);
            }
        })
        .catch(error => {
            alert('Terjadi kesalahan: ' + error.message);
        })
        .finally(() => {
            saveVehicleBtn.disabled = false;
            saveVehicleBtn.innerHTML = 'Simpan Perubahan';
        });
        */
    });
    
    // Close modal when clicking outside
    vehicleMasterModal.addEventListener('click', function(e) {
        if (e.target === vehicleMasterModal) {
            closeModal();
        }
    });
    
    // Handle form submission with vehicle master data check
    const form = document.querySelector('form');
    const vehicleMasterModal = document.getElementById('vehicleMasterModal');
    const closeModalBtn = document.getElementById('closeModal');
    const cancelModalBtn = document.getElementById('cancelModalBtn');
    const saveVehicleBtn = document.getElementById('saveVehicleBtn');
    
    let isSubmittingAfterModal = false;
    
    form.addEventListener('submit', function(e) {
        if (isSubmittingAfterModal) {
            return; // Allow normal submission
        }
        
        const jenisAktivitas = document.getElementById('jenis_aktivitas').value;
        const nomorPolisi = document.querySelector('select[name="nomor_polisi"]').value;
        
        if (jenisAktivitas === 'Pembayaran Kendaraan' && nomorPolisi) {
            e.preventDefault();
            
            // Show confirmation dialog
            if (confirm('Apakah Anda ingin mengubah data master kendaraan untuk plat nomor: ' + nomorPolisi + '?')) {
                showVehicleMasterModal(nomorPolisi);
            } else {
                // Continue with normal form submission
                isSubmittingAfterModal = true;
                form.submit();
            }
        }
    });
    
    function showVehicleMasterModal(nomorPolisi) {
        // Get vehicle data from the dropdown
        const mobilSelect = document.querySelector('select[name="nomor_polisi"]');
        const selectedOption = mobilSelect.options[mobilSelect.selectedIndex];
        const mobilText = selectedOption.text;
        
        // Parse vehicle data from option text (format: "PLAT - MEREK JENIS")
        const parts = mobilText.split(' - ');
        const plat = parts[0] || '';
        const merekJenis = parts[1] || '';
        const merekJenisParts = merekJenis.split(' ');
        const merek = merekJenisParts[0] || '';
        const jenis = merekJenisParts.slice(1).join(' ') || '';
        
        // Populate modal fields
        document.getElementById('modal_nomor_polisi').value = plat;
        document.getElementById('modal_merek').value = merek;
        document.getElementById('modal_jenis').value = jenis;
        document.getElementById('modal_tahun').value = '';
        document.getElementById('modal_warna').value = '';
        document.getElementById('modal_status').value = 'aktif';
        document.getElementById('modal_keterangan').value = '';
        
        // Show modal
        vehicleMasterModal.classList.remove('hidden');
    }
    
    function closeModal() {
        vehicleMasterModal.classList.add('hidden');
    }
    
    // Modal event listeners
    closeModalBtn.addEventListener('click', closeModal);
    cancelModalBtn.addEventListener('click', closeModal);
    
    saveVehicleBtn.addEventListener('click', function() {
        // Here you can add AJAX call to update vehicle master data
        const vehicleData = {
            nomor_polisi: document.getElementById('modal_nomor_polisi').value,
            merek: document.getElementById('modal_merek').value,
            jenis: document.getElementById('modal_jenis').value,
            tahun: document.getElementById('modal_tahun').value,
            warna: document.getElementById('modal_warna').value,
            status: document.getElementById('modal_status').value,
            keterangan: document.getElementById('modal_keterangan').value
        };
        
        // Show loading state
        saveVehicleBtn.disabled = true;
        saveVehicleBtn.innerHTML = '<svg class="w-4 h-4 inline-block mr-1 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Menyimpan...';
        
        // Simulate API call (replace with actual AJAX call)
        setTimeout(() => {
            alert('Data master kendaraan berhasil diperbarui!');
            closeModal();
            
            // Reset button
            saveVehicleBtn.disabled = false;
            saveVehicleBtn.innerHTML = '<svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Simpan Perubahan';
            
            // Continue with form submission
            isSubmittingAfterModal = true;
            form.submit();
        }, 2000);
        
        // TODO: Replace above setTimeout with actual AJAX call like:
        /*
        fetch('/api/mobils/update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(vehicleData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Data master kendaraan berhasil diperbarui!');
                closeModal();
                isSubmittingAfterModal = true;
                form.submit();
            } else {
                alert('Gagal memperbarui data: ' + data.message);
            }
        })
        .catch(error => {
            alert('Terjadi kesalahan: ' + error.message);
        })
        .finally(() => {
            saveVehicleBtn.disabled = false;
            saveVehicleBtn.innerHTML = 'Simpan Perubahan';
        });
        */
    });
    
    // Close modal when clicking outside
    vehicleMasterModal.addEventListener('click', function(e) {
        if (e.target === vehicleMasterModal) {
            closeModal();
        }
    });
});
</script>
@endsection
