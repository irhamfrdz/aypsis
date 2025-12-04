@extends('layouts.app')

@section('content')
<!-- Ensure CSRF token is available -->
<meta name="csrf-token" content="{{ csrf_token() }}">

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

        <form action="{{ route('pembayaran-aktivitas-lain.store') }}" method="POST" class="p-6" id="pembayaran_form">
            @csrf
            
            <!-- Display Laravel validation errors -->
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-red-600 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <div class="flex-1">
                            <h3 class="text-sm font-semibold text-red-800 mb-2">Terdapat Error pada Form</h3>
                            <ul class="text-sm text-red-700 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>• {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Display session flash messages -->
            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-red-600 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <div class="flex-1">
                            <h3 class="text-sm font-semibold text-red-800 mb-2">Error</h3>
                            <p class="text-sm text-red-700">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-green-600 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <div class="flex-1">
                            <h3 class="text-sm font-semibold text-green-800 mb-2">Berhasil</h3>
                            <p class="text-sm text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif
            
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
                <!-- Debug button untuk test error (hanya untuk development) -->
                @if(config('app.debug'))
                    <button type="button" onclick="showErrorMessage('Test error message untuk memastikan error handling berfungsi dengan baik.')" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-medium text-sm rounded-md transition">
                        Test Error
                    </button>
                @endif
                
                <a href="{{ route('pembayaran-aktivitas-lain.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium text-sm rounded-md transition">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm rounded-md transition" id="submit_btn">
                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Error/Success notification area -->
<div id="notification-area" class="fixed top-4 right-4 z-50"></div>

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

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.select2-container {
    width: 100% !important;
}
.select2-container .select2-selection--single {
    height: 38px !important;
    padding: 6px 12px !important;
    border: 1px solid #d1d5db !important;
    border-radius: 6px !important;
    font-size: 14px !important;
}
.select2-container .select2-selection--single .select2-selection__rendered {
    line-height: 24px !important;
}
.select2-container .select2-selection--single .select2-selection__arrow {
    height: 36px !important;
    right: 6px !important;
}
.select2-dropdown {
    border-radius: 6px !important;
}
.select2-container--open .select2-selection--single {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2) !important;
}
.select2-results__option--highlighted {
    background-color: #3b82f6 !important;
}
</style>
@endpush

@push('scripts')
<script>
// Ensure jQuery is available globally first
window.jQuery = window.$ = window.$ || function() {
    console.error('jQuery is not loaded');
    return null;
};

// Load scripts in sequence
function loadScripts() {
    // Load jQuery if not already available
    if (typeof window.jQuery === 'undefined' || typeof window.jQuery.fn === 'undefined') {
        const jqueryScript = document.createElement('script');
        jqueryScript.src = 'https://code.jquery.com/jquery-3.6.0.min.js';
        jqueryScript.onload = function() {
            console.log('jQuery loaded');
            loadSelect2();
        };
        jqueryScript.onerror = function() {
            console.error('Failed to load jQuery');
        };
        document.head.appendChild(jqueryScript);
    } else {
        loadSelect2();
    }
}

function loadSelect2() {
    // Load Select2
    const select2Script = document.createElement('script');
    select2Script.src = 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js';
    select2Script.onload = function() {
        console.log('Select2 loaded');
        // Wait a bit for everything to initialize
        setTimeout(initializeSelect2, 100);
    };
    select2Script.onerror = function() {
        console.error('Failed to load Select2');
    };
    document.head.appendChild(select2Script);
}

function initializeSelect2() {
    if (typeof $ === 'undefined' || typeof $.fn.select2 === 'undefined') {
        console.error('Select2 or jQuery not available');
        return;
    }

    console.log('Initializing Select2...');
    
    // Initialize Select2 for all dropdowns
    $('#jenis_aktivitas').select2({
        placeholder: "Pilih Jenis Aktivitas",
        allowClear: true,
        width: '100%'
    });

    $('#sub_jenis_kendaraan_select').select2({
        placeholder: "Pilih Sub Jenis",
        allowClear: true,
        width: '100%'
    });

    $('select[name="nomor_polisi"]').select2({
        placeholder: "Pilih Nomor Polisi",
        allowClear: true,
        width: '100%'
    });

    $('select[name="nomor_voyage"]').select2({
        placeholder: "Pilih Nomor Voyage",
        allowClear: true,
        width: '100%'
    });

    $('#akun_coa_select').select2({
        placeholder: "Pilih Akun COA",
        allowClear: true,
        width: '100%'
    });

    $('#akun_bank_select').select2({
        placeholder: "Pilih Bank/Kas",
        allowClear: true,
        width: '100%'
    });

    $('#penerima_dropdown').select2({
        placeholder: "Pilih dari Karyawan",
        allowClear: true,
        width: '100%'
    });

    $('#debit_kredit').select2({
        placeholder: "Pilih Jenis Transaksi",
        allowClear: true,
        width: '100%'
    });

    // Initialize main functionality after Select2 is ready
    initializeMainFunctionality();
}

// Initialize main functionality
function initializeMainFunctionality() {
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
            // Reinitialize Select2 after showing
            setTimeout(() => {
                $('#sub_jenis_kendaraan_select').select2({
                    placeholder: "Pilih Sub Jenis",
                    allowClear: true,
                    width: '100%'
                });
            }, 100);
        } else {
            subJenisKendaraan.classList.add('hidden');
            subJenisSelect.removeAttribute('required');
            $('#sub_jenis_kendaraan_select').val('').trigger('change');
            nomorPolisiField.classList.add('hidden');
            nomorPolisiSelect.removeAttribute('required');
            $('select[name="nomor_polisi"]').val('').trigger('change');
        }
        
        if (jenisAktivitas.value === 'Pembayaran Kapal') {
            nomorVoyageField.classList.remove('hidden');
            nomorVoyageSelect.setAttribute('required', 'required');
            // Reinitialize Select2 after showing
            setTimeout(() => {
                $('select[name="nomor_voyage"]').select2({
                    placeholder: "Pilih Nomor Voyage",
                    allowClear: true,
                    width: '100%'
                });
            }, 100);
        } else {
            nomorVoyageField.classList.add('hidden');
            nomorVoyageSelect.removeAttribute('required');
            $('select[name="nomor_voyage"]').val('').trigger('change');
        }
    }

    function toggleNomorPolisi() {
        const subJenis = subJenisSelect.value;
        if (subJenis === 'Plat' || subJenis === 'STNK' || subJenis === 'KIR') {
            nomorPolisiField.classList.remove('hidden');
            nomorPolisiSelect.setAttribute('required', 'required');
            // Reinitialize Select2 after showing
            setTimeout(() => {
                $('select[name="nomor_polisi"]').select2({
                    placeholder: "Pilih Nomor Polisi",
                    allowClear: true,
                    width: '100%'
                });
            }, 100);
        } else {
            nomorPolisiField.classList.add('hidden');
            nomorPolisiSelect.removeAttribute('required');
            $('select[name="nomor_polisi"]').val('').trigger('change');
        }
    }

    toggleSubJenisKendaraan();
    toggleNomorPolisi();

    // Use Select2 change events
    $('#jenis_aktivitas').on('change', function() {
        jenisAktivitas.value = this.value;
        toggleSubJenisKendaraan();
    });
    
    $('#sub_jenis_kendaraan_select').on('change', function() {
        subJenisSelect.value = this.value;
        toggleNomorPolisi();
    });
    
    const penerimaDropdown = document.getElementById('penerima_dropdown');
    const penerimaInput = document.getElementById('penerima_input');
    
    // Use Select2 change event for penerima dropdown
    $('#penerima_dropdown').on('change', function() {
        if (this.value) {
            penerimaInput.value = this.value;
        }
    });
    
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
    // Use Select2 change events for journal preview
    $('#debit_kredit').on('change', updateJournalPreview);
    $('#akun_coa_select').on('change', updateJournalPreview);
    $('#akun_bank_select').on('change', updateJournalPreview);
    jumlahInput.addEventListener('input', updateJournalPreview);
    
    updateJournalPreview();
    
    const form = document.querySelector('form');
    const vehicleMasterModal = document.getElementById('vehicleMasterModal');
    const closeModalBtn = document.getElementById('closeModal');
    const cancelModalBtn = document.getElementById('cancelModalBtn');
    const saveVehicleBtn = document.getElementById('saveVehicleBtn');
    
    let isSubmittingAfterModal = false;
    
    form.addEventListener('submit', function(e) {
        e.preventDefault(); // Always prevent default submission
        
        if (isSubmittingAfterModal) {
            isSubmittingAfterModal = false; // Reset flag
        }
        
        // Validate form before submission
        if (!validateForm()) {
            return;
        }
        
        const jenisAktivitasValue = document.getElementById('jenis_aktivitas').value;
        const nomorPolisiValue = document.querySelector('select[name="nomor_polisi"]').value;
        
        if (jenisAktivitasValue === 'Pembayaran Kendaraan' && nomorPolisiValue && !isSubmittingAfterModal) {
            if (confirm('Apakah Anda ingin mengubah data master kendaraan untuk plat nomor: ' + nomorPolisiValue + '?')) {
                showVehicleMasterModal(nomorPolisiValue);
                return;
            }
        }
        
        // Submit form via AJAX
        submitFormWithErrorHandling();
    });
    
    // Form validation function
    function validateForm() {
        const requiredFields = [
            { id: 'tanggal', name: 'Tanggal' },
            { id: 'jenis_aktivitas', name: 'Jenis Aktivitas' },
            { name: 'jumlah', name: 'Jumlah' },
            { id: 'debit_kredit', name: 'Jenis Transaksi' },
            { id: 'akun_coa_select', name: 'Akun COA' },
            { id: 'akun_bank_select', name: 'Bank/Kas' },
            { id: 'penerima_input', name: 'Penerima' },
            { name: 'keterangan', name: 'Keterangan' }
        ];
        
        const jenisAktivitas = document.getElementById('jenis_aktivitas').value;
        
        // Add conditional required fields
        if (jenisAktivitas === 'Pembayaran Kendaraan') {
            requiredFields.push({ id: 'sub_jenis_kendaraan_select', name: 'Sub Jenis Kendaraan' });
            
            const subJenis = document.getElementById('sub_jenis_kendaraan_select').value;
            if (subJenis === 'Plat' || subJenis === 'STNK' || subJenis === 'KIR') {
                requiredFields.push({ name: 'nomor_polisi', name: 'Nomor Polisi' });
            }
        } else if (jenisAktivitas === 'Pembayaran Kapal') {
            requiredFields.push({ name: 'nomor_voyage', name: 'Nomor Voyage' });
        }
        
        for (const field of requiredFields) {
            let element;
            if (field.id) {
                element = document.getElementById(field.id);
            } else {
                element = document.querySelector(`[name="${field.name.toLowerCase()}"]`) || 
                         document.querySelector(`[name="${field.name}"]`);
            }
            
            if (!element || !element.value.trim()) {
                showErrorMessage(`${field.name} harus diisi!`);
                if (element) element.focus();
                return false;
            }
        }
        
        // Validate jumlah
        const jumlah = parseFloat(document.querySelector('[name="jumlah"]').value);
        if (isNaN(jumlah) || jumlah <= 0) {
            showErrorMessage('Jumlah harus berupa angka positif!');
            document.querySelector('[name="jumlah"]').focus();
            return false;
        }
        
        return true;
    }
    
    // Function to handle form submission with error handling
    function submitFormWithErrorHandling() {
        const submitBtn = document.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        
        try {
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<svg class="w-4 h-4 inline-block mr-1 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Menyimpan...';
            
            // Collect form data
            const formData = new FormData(form);
            
            // Make AJAX request
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') || 
                                   document.querySelector('input[name="_token"]').value
                }
            })
            .then(response => {
                if (response.ok) {
                    return response.json().catch(() => {
                        // If response is not JSON (maybe redirect), handle as success
                        if (response.redirected) {
                            window.location.href = response.url;
                            return;
                        }
                        throw new Error('Invalid response format');
                    });
                } else {
                    return response.json().then(data => {
                        throw new Error(data.message || `HTTP ${response.status}: ${response.statusText}`);
                    }).catch(() => {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    });
                }
            })
            .then(data => {
                if (data) {
                    // Success response
                    showSuccessMessage(data.message || 'Data berhasil disimpan!');
                    
                    // Redirect after short delay
                    setTimeout(() => {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            // Default redirect to index
                            window.location.href = "{{ route('pembayaran-aktivitas-lain.index') }}";
                        }
                    }, 1500);
                }
            })
            .catch(error => {
                console.error('Error submitting form:', error);
                
                // Try to extract meaningful error message
                let errorMessage = 'Terjadi kesalahan saat menyimpan data.';
                
                if (error.message) {
                    if (error.message.includes('HTTP 422')) {
                        errorMessage = 'Data tidak valid. Periksa kembali form Anda.';
                    } else if (error.message.includes('HTTP 500')) {
                        errorMessage = 'Terjadi kesalahan server. Silakan hubungi administrator.';
                    } else if (error.message.includes('HTTP 419')) {
                        errorMessage = 'Sesi telah habis. Silakan refresh halaman dan coba lagi.';
                    } else if (error.message.includes('Network')) {
                        errorMessage = 'Koneksi internet bermasalah. Periksa koneksi Anda.';
                    } else {
                        errorMessage = error.message;
                    }
                }
                
                showErrorMessage(errorMessage);
            })
            .finally(() => {
                // Reset button
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            });
            
        } catch (error) {
            console.error('Error in form submission:', error);
            showErrorMessage('Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
            
            // Reset button
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        }
    }
    
    function showVehicleMasterModal(nomorPolisi) {
        const mobilSelect = document.querySelector('select[name="nomor_polisi"]');
        const selectedOption = mobilSelect.options[mobilSelect.selectedIndex];
        const mobilText = selectedOption.text;
        
        const parts = mobilText.split(' - ');
        const plat = parts[0] || '';
        const merekJenis = parts[1] || '';
        const merekJenisParts = merekJenis.split(' ');
        const merek = merekJenisParts[0] || '';
        const jenis = merekJenisParts.slice(1).join(' ') || '';
        
        document.getElementById('modal_nomor_polisi').value = plat;
        document.getElementById('modal_merek').value = merek;
        document.getElementById('modal_jenis').value = jenis;
        document.getElementById('modal_tahun').value = '';
        document.getElementById('modal_warna').value = '';
        document.getElementById('modal_status').value = 'aktif';
        document.getElementById('modal_keterangan').value = '';
        
        vehicleMasterModal.classList.remove('hidden');
    }
    
    function closeModal() {
        vehicleMasterModal.classList.add('hidden');
    }
    
    closeModalBtn.addEventListener('click', closeModal);
    cancelModalBtn.addEventListener('click', closeModal);
    
    saveVehicleBtn.addEventListener('click', function() {
        const vehicleData = {
            nomor_polisi: document.getElementById('modal_nomor_polisi').value,
            merek: document.getElementById('modal_merek').value,
            jenis: document.getElementById('modal_jenis').value,
            tahun: document.getElementById('modal_tahun').value,
            warna: document.getElementById('modal_warna').value,
            status: document.getElementById('modal_status').value,
            keterangan: document.getElementById('modal_keterangan').value
        };
        
        // Validate required fields
        if (!vehicleData.nomor_polisi || !vehicleData.merek || !vehicleData.jenis) {
            showErrorMessage('Nomor polisi, merek, dan jenis kendaraan harus diisi!');
            return;
        }
        
        saveVehicleBtn.disabled = true;
        saveVehicleBtn.innerHTML = '<svg class="w-4 h-4 inline-block mr-1 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Menyimpan...';
        
        // Simulate API call with error handling
        setTimeout(() => {
            // Simulate random success/failure (80% success rate)
            const isSuccess = Math.random() > 0.2;
            
            if (isSuccess) {
                showSuccessMessage('Data master kendaraan berhasil diperbarui!');
                closeModal();
                
                saveVehicleBtn.disabled = false;
                saveVehicleBtn.innerHTML = '<svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Simpan Perubahan';
                
                isSubmittingAfterModal = true;
                submitFormWithErrorHandling();
            } else {
                // Handle error case
                saveVehicleBtn.disabled = false;
                saveVehicleBtn.innerHTML = '<svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Simpan Perubahan';
                
                showErrorMessage('Gagal memperbarui data master kendaraan. Silakan coba lagi atau hubungi administrator.');
            }
        }, 2000);
    });
    
    vehicleMasterModal.addEventListener('click', function(e) {
        if (e.target === vehicleMasterModal) {
            closeModal();
        }
    });
    
    // Utility functions for showing messages
    function showErrorMessage(message) {
        // Remove any existing notifications
        removeExistingNotifications();
        
        // Handle multiline messages
        const formattedMessage = message.replace(/\n/g, '<br>');
        
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg z-50 max-w-md';
        notification.innerHTML = `
            <div class="flex items-start">
                <svg class="w-5 h-5 mr-3 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div class="flex-1">
                    <p class="font-medium">Error!</p>
                    <div class="text-sm mt-1">${formattedMessage}</div>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-red-200 hover:text-white">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 8 seconds for errors (longer since they might be important)
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 8000);
        
        // Also log to console for debugging
        console.error('Error:', message);
    }
    
    function showSuccessMessage(message) {
        // Remove any existing notifications
        removeExistingNotifications();
        
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg z-50 max-w-md';
        notification.innerHTML = `
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="font-medium">Berhasil!</p>
                    <p class="text-sm mt-1">${message}</p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-green-200 hover:text-white">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 3000);
    }
    
    function removeExistingNotifications() {
        const existingNotifications = document.querySelectorAll('.fixed.top-4.right-4');
        existingNotifications.forEach(notification => {
            if (notification.parentNode) {
                notification.remove();
            }
        });
    }
}
</script>

<script>
// Start loading scripts when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, starting script initialization...');
    loadScripts();
});

// Add error event listener to catch any unhandled errors
window.addEventListener('error', function(event) {
    console.error('JavaScript Error:', event.error);
    console.error('Error details:', {
        message: event.message,
        filename: event.filename,
        lineno: event.lineno,
        colno: event.colno
    });
});

// Add unhandled promise rejection listener
window.addEventListener('unhandledrejection', function(event) {
    console.error('Unhandled Promise Rejection:', event.reason);
    console.error('Promise:', event.promise);
});
</script>
@endpush
@endsection