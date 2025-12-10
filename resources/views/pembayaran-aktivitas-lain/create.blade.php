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

                <!-- Nomor Accurate -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Accurate</label>
                    <input type="text" name="nomor_accurate" value="{{ old('nomor_accurate') }}" placeholder="Masukkan nomor accurate" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm @error('nomor_accurate') border-red-500 @enderror">
                    @error('nomor_accurate')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
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
                        <option value="Pembayaran Adjusment Uang Jalan" {{ old('jenis_aktivitas') == 'Pembayaran Adjusment Uang Jalan' ? 'selected' : '' }}>Pembayaran Adjusment Uang Jalan</option>
                        <option value="Pembayaran Lain Lain" {{ old('jenis_aktivitas') == 'Pembayaran Lain Lain' ? 'selected' : '' }}>Pembayaran Lain Lain</option>
                    </select>
                    @error('jenis_aktivitas')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Surat Jalan (Hidden by default) -->
                <div id="surat_jalan_field" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Surat Jalan <span class="text-red-500">*</span></label>
                    <select name="no_surat_jalan" id="surat_jalan_select" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm @error('no_surat_jalan') border-red-500 @enderror">
                        <option value="">Pilih Surat Jalan</option>
                        @foreach($suratJalans as $suratJalan)
                            <option value="{{ $suratJalan->no_surat_jalan }}" data-uang-jalan="{{ $suratJalan->uang_jalan }}" {{ old('no_surat_jalan') == $suratJalan->no_surat_jalan ? 'selected' : '' }}>
                                {{ $suratJalan->no_surat_jalan }} - {{ $suratJalan->tujuan_pengiriman }}
                            </option>
                        @endforeach
                    </select>
                    @error('no_surat_jalan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jenis Penyesuaian (Hidden by default) -->
                <div id="jenis_penyesuaian_field" class="{{ old('jenis_aktivitas') === 'Pembayaran Adjusment Uang Jalan' ? '' : 'hidden' }}" style="{{ old('jenis_aktivitas') === 'Pembayaran Adjusment Uang Jalan' ? '' : 'display:none' }}">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Penyesuaian <span class="text-red-500">*</span></label>
                    <select name="jenis_penyesuaian" id="jenis_penyesuaian_select" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm @error('jenis_penyesuaian') border-red-500 @enderror">
                        <option value="">Pilih Jenis Penyesuaian</option>
                        <option value="pengurangan" {{ old('jenis_penyesuaian') == 'pengurangan' ? 'selected' : '' }}>Pengurangan</option>
                        <option value="penambahan" {{ old('jenis_penyesuaian') == 'penambahan' ? 'selected' : '' }}>Penambahan</option>
                        <option value="pengembalian penuh" {{ old('jenis_penyesuaian') == 'pengembalian penuh' ? 'selected' : '' }}>Pengembalian Penuh</option>
                        <option value="pengembalian sebagian" {{ old('jenis_penyesuaian') == 'pengembalian sebagian' ? 'selected' : '' }}>Pengembalian Sebagian</option>
                    </select>
                    @error('jenis_penyesuaian')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tipe Penyesuaian (Hidden by default) -->
                <div id="tipe_penyesuaian_field" class="{{ old('jenis_aktivitas') === 'Pembayaran Adjusment Uang Jalan' ? '' : 'hidden' }}" style="{{ old('jenis_aktivitas') === 'Pembayaran Adjusment Uang Jalan' ? '' : 'display:none' }}">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Penyesuaian <span class="text-red-500">*</span></label>

                    <!-- Container untuk input dinamis -->
                    <div id="tipe_penyesuaian_container" class="space-y-3">
                        <!-- Template untuk input baru akan ditambahkan di sini -->
                    </div>

                    <!-- Tombol tambah -->
                    <button type="button" id="add_tipe_penyesuaian_btn" class="mt-3 inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Tambah Tipe Penyesuaian
                    </button>

                    @error('tipe_penyesuaian_detail')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    @error('tipe_penyesuaian_detail.*.tipe')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    @error('tipe_penyesuaian_detail.*.nominal')
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
                    <input type="number" name="jumlah" value="{{ old('jumlah') }}" required min="0" step="1" placeholder="0" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm @error('jumlah') border-red-500 @enderror">
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

                <!-- Akun Biaya -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Akun Biaya <span class="text-red-500">*</span></label>
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
        placeholder: "Pilih Akun Biaya",
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

    $('#surat_jalan_select').select2({
        placeholder: "Pilih Surat Jalan",
        allowClear: true,
        width: '100%'
    });

    $('#jenis_penyesuaian_select').select2({
        placeholder: "Pilih Jenis Penyesuaian",
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
        const jenisVal = (jenisAktivitas.value || '').trim();
        console.debug('toggleSubJenisKendaraan called, jenisVal=', jenisVal);
        if (jenisVal === 'Pembayaran Kendaraan') {
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
        
        if (jenisVal === 'Pembayaran Kapal') {
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
            // Hide any adjustment fields if previously visible (Kapal doesn't use penyesuaian)
            var jenisPenyesuaianFieldEl = document.getElementById('jenis_penyesuaian_field');
            var jenisPenyesuaianSelectEl = document.getElementById('jenis_penyesuaian_select');
            var tipePenyesuaianFieldEl = document.getElementById('tipe_penyesuaian_field');
            if (jenisPenyesuaianFieldEl) jenisPenyesuaianFieldEl.classList.add('hidden');
            if (jenisPenyesuaianSelectEl) { jenisPenyesuaianSelectEl.removeAttribute('required'); $(jenisPenyesuaianSelectEl).val('').trigger('change'); }
            if (tipePenyesuaianFieldEl) tipePenyesuaianFieldEl.classList.add('hidden');
            // Clear dynamic tipe penyesuaian inputs, if any
            clearTipePenyesuaianInputs();
            // Ensure jumlah is editable and visual indicators are removed
            const jumlahInputForKapal = document.querySelector('input[name="jumlah"]');
            if (jumlahInputForKapal) {
                jumlahInputForKapal.readOnly = false;
                jumlahInputForKapal.classList.remove('bg-gray-100', 'cursor-not-allowed');
                const indicator = jumlahInputForKapal.parentNode.querySelector('.auto-calc-indicator');
                if (indicator) indicator.remove();
            }
        } else {
            nomorVoyageField.classList.add('hidden');
            nomorVoyageSelect.removeAttribute('required');
            $('select[name="nomor_voyage"]').val('').trigger('change');
        }
        
        const suratJalanField = document.getElementById('surat_jalan_field');
        const suratJalanSelect = document.getElementById('surat_jalan_select');
        
        if (jenisVal === 'Pembayaran Adjusment Uang Jalan') {
            suratJalanField.classList.remove('hidden');
            suratJalanSelect.setAttribute('required', 'required');
            // Reinitialize Select2 after showing
            setTimeout(() => {
                $('#surat_jalan_select').select2({
                    placeholder: "Pilih Surat Jalan",
                    allowClear: true,
                    width: '100%'
                });
            }, 100);
        } else {
            suratJalanField.classList.add('hidden');
            suratJalanSelect.removeAttribute('required');
            $('#surat_jalan_select').val('').trigger('change');
        }
        
        const jenisPenyesuaianField = document.getElementById('jenis_penyesuaian_field');
        const jenisPenyesuaianSelect = document.getElementById('jenis_penyesuaian_select');
        
        const tipePenyesuaianField = document.getElementById('tipe_penyesuaian_field');
        const tipePenyesuaianSelect = document.getElementById('tipe_penyesuaian_select');
        
        if (jenisVal === 'Pembayaran Adjusment Uang Jalan') {
            jenisPenyesuaianField.classList.remove('hidden');
            jenisPenyesuaianSelect.setAttribute('required', 'required');
            // Reinitialize Select2 after showing
            setTimeout(() => {
                $('#jenis_penyesuaian_select').select2({
                    placeholder: "Pilih Jenis Penyesuaian",
                    allowClear: true,
                    width: '100%'
                });
            }, 100);

            // Toggle tipe penyesuaian based on jenis penyesuaian
            toggleTipePenyesuaian();
        } else {
            jenisPenyesuaianField.classList.add('hidden');
            jenisPenyesuaianSelect.removeAttribute('required');
            $('#jenis_penyesuaian_select').val('').trigger('change');
            jenisPenyesuaianField.style.display = 'none';
            if (tipePenyesuaianField) tipePenyesuaianField.style.display = 'none';

            // Clear all dynamic inputs
            clearTipePenyesuaianInputs();
            // Clear jumlah if not adjustment payment
            const jumlahInput = document.querySelector('input[name="jumlah"]');
            if (jumlahInput && !jumlahInput.dataset.manual) {
                jumlahInput.value = '';
                jumlahInput.dispatchEvent(new Event('input', { bubbles: true }));
            }
        }
        
        // Update jumlah field state (read-only for adjustment payments)
        calculateTotalJumlah();
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

    function toggleTipePenyesuaian() {
        const jenisPenyesuaian = document.getElementById('jenis_penyesuaian_select').value;
        const tipePenyesuaianField = document.getElementById('tipe_penyesuaian_field');
        const jumlahInput = document.querySelector('input[name="jumlah"]');
        
        if (jenisPenyesuaian === 'pengembalian penuh') {
            // Sembunyikan tipe penyesuaian
            tipePenyesuaianField.classList.add('hidden');
            // Clear all dynamic inputs
            clearTipePenyesuaianInputs();
            // Set jumlah ke uang jalan dari surat jalan yang dipilih
            const selectedSuratJalan = $('#surat_jalan_select').find('option:selected');
            const uangJalan = selectedSuratJalan.data('uang-jalan');
            if (uangJalan && !isNaN(uangJalan)) {
                jumlahInput.value = uangJalan;
                jumlahInput.readOnly = true;
                jumlahInput.classList.add('bg-gray-100', 'cursor-not-allowed');
                // Tambahkan indicator
                let indicator = jumlahInput.parentNode.querySelector('.auto-calc-indicator');
                if (!indicator) {
                    indicator = document.createElement('p');
                    indicator.className = 'auto-calc-indicator text-xs text-blue-600 mt-1';
                    indicator.innerHTML = '🔄 Jumlah diisi otomatis dari uang jalan surat jalan';
                    jumlahInput.parentNode.appendChild(indicator);
                }
            }
        } else if (jenisPenyesuaian === 'pengembalian sebagian') {
            // Sembunyikan tipe penyesuaian
            tipePenyesuaianField.classList.add('hidden');
            // Clear all dynamic inputs
            clearTipePenyesuaianInputs();
            // Jumlah bisa diinputkan manual, editable
            jumlahInput.readOnly = false;
            jumlahInput.classList.remove('bg-gray-100', 'cursor-not-allowed');
            // Remove indicator if exists
            const indicator = jumlahInput.parentNode.querySelector('.auto-calc-indicator');
            if (indicator) {
                indicator.remove();
            }
        } else {
            // Tampilkan tipe penyesuaian
            tipePenyesuaianField.classList.remove('hidden');
            // Initialize dynamic input fields for tipe penyesuaian
            initializeTipePenyesuaianInputs();
            // Hitung total dari nominal tipe penyesuaian
            calculateTotalJumlah();
        }
    }

    // Functions for dynamic tipe penyesuaian inputs
    function initializeTipePenyesuaianInputs() {
        const container = document.getElementById('tipe_penyesuaian_container');
        const addBtn = document.getElementById('add_tipe_penyesuaian_btn');

        // Clear existing inputs
        container.innerHTML = '';

        // Add event listener to add button
        addBtn.onclick = function() {
            addTipePenyesuaianInput();
        };

        // Add at least one input by default
        addTipePenyesuaianInput();
    }

    function clearTipePenyesuaianInputs() {
        const container = document.getElementById('tipe_penyesuaian_container');
        container.innerHTML = '';
    }

    function addTipePenyesuaianInput(existingTipe = '', existingNominal = '') {
        const container = document.getElementById('tipe_penyesuaian_container');
        const inputCount = container.children.length;

        const inputGroup = document.createElement('div');
        inputGroup.className = 'flex items-end gap-3 p-3 bg-gray-50 rounded-md';
        inputGroup.innerHTML = `
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Penyesuaian</label>
                <select name="tipe_penyesuaian_detail[${inputCount}][tipe]" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm" required>
                    <option value="">Pilih Tipe</option>
                    <option value="mel" ${existingTipe === 'mel' ? 'selected' : ''}>MEL</option>
                    <option value="parkir" ${existingTipe === 'parkir' ? 'selected' : ''}>Parkir</option>
                    <option value="pelancar" ${existingTipe === 'pelancar' ? 'selected' : ''}>Pelancar</option>
                    <option value="kawalan" ${existingTipe === 'kawalan' ? 'selected' : ''}>Kawalan</option>
                </select>
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nominal (Rp)</label>
                <input type="number" name="tipe_penyesuaian_detail[${inputCount}][nominal]" value="${existingNominal}" min="0" step="1" placeholder="0" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm" required>
            </div>
            <div class="flex-shrink-0">
                <button type="button" onclick="removeTipePenyesuaianInput(this)" class="px-3 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>
        `;

        container.appendChild(inputGroup);

        // Reinitialize Select2 for new select elements
        setTimeout(() => {
            $(inputGroup).find('select').select2({
                placeholder: "Pilih Tipe",
                allowClear: true,
                width: '100%'
            });
        }, 100);

        // Add event listener to nominal input for auto-calculation
        const nominalInput = inputGroup.querySelector('input[name*="[nominal]"]');
        if (nominalInput) {
            nominalInput.addEventListener('input', calculateTotalJumlah);
        }
    }

    // Make removeTipePenyesuaianInput available globally
    window.removeTipePenyesuaianInput = function(button) {
        const container = document.getElementById('tipe_penyesuaian_container');
        const inputGroup = button.closest('.flex.items-end.gap-3');

        // Only remove if there's more than one input
        if (container.children.length > 1) {
            inputGroup.remove();
            // Reindex remaining inputs
            reindexTipePenyesuaianInputs();
        } else {
            showErrorMessage('Minimal harus ada satu tipe penyesuaian!');
        }
    };

    function reindexTipePenyesuaianInputs() {
        const container = document.getElementById('tipe_penyesuaian_container');
        const inputs = container.querySelectorAll('.flex.items-end.gap-3');

        inputs.forEach((inputGroup, index) => {
            const select = inputGroup.querySelector('select');
            const input = inputGroup.querySelector('input');

            if (select) {
                select.name = `tipe_penyesuaian_detail[${index}][tipe]`;
            }
            if (input) {
                input.name = `tipe_penyesuaian_detail[${index}][nominal]`;
            }
        });



        // Recalculate total after reindexing
        calculateTotalJumlah();
    }

    function calculateTotalJumlah() {
        const jenisAktivitas = document.getElementById('jenis_aktivitas').value;
        const jumlahInput = document.querySelector('input[name="jumlah"]');
        
        // Only auto-calculate for "Pembayaran Adjusment Uang Jalan"
        if (jenisAktivitas === 'Pembayaran Adjusment Uang Jalan') {
            const jenisPenyesuaian = document.getElementById('jenis_penyesuaian_select').value;
            
            if (jenisPenyesuaian === 'pengembalian penuh') {
                // Jumlah sudah di-set di toggleTipePenyesuaian, pastikan read-only
                jumlahInput.readOnly = true;
                jumlahInput.classList.add('bg-gray-100', 'cursor-not-allowed');
                // Indicator sudah ditambahkan di toggleTipePenyesuaian
            } else if (jenisPenyesuaian === 'pengembalian sebagian') {
                // Jumlah editable, remove read-only
                jumlahInput.readOnly = false;
                jumlahInput.classList.remove('bg-gray-100', 'cursor-not-allowed');
                // Remove indicator
                const indicator = jumlahInput.parentNode.querySelector('.auto-calc-indicator');
                if (indicator) {
                    indicator.remove();
                }
            } else {
                const container = document.getElementById('tipe_penyesuaian_container');
                const nominalInputs = container.querySelectorAll('input[name*="[nominal]"]');
                
                let total = 0;
                nominalInputs.forEach(input => {
                    const value = parseFloat(input.value) || 0;
                    total += value;
                });
                
                // Update jumlah field
                jumlahInput.value = total;
                jumlahInput.readOnly = true;
                jumlahInput.classList.add('bg-gray-100', 'cursor-not-allowed');
                
                // Add visual indicator
                let indicator = jumlahInput.parentNode.querySelector('.auto-calc-indicator');
                if (!indicator) {
                    indicator = document.createElement('p');
                    indicator.className = 'auto-calc-indicator text-xs text-blue-600 mt-1';
                    indicator.innerHTML = '🔄 Jumlah dihitung otomatis dari total nominal tipe penyesuaian';
                    jumlahInput.parentNode.appendChild(indicator);
                }
                
                // Trigger input event to update journal preview
                jumlahInput.dispatchEvent(new Event('input', { bubbles: true }));
            }
        } else {
            // Remove read-only and indicator for other activity types
            jumlahInput.readOnly = false;
            jumlahInput.classList.remove('bg-gray-100', 'cursor-not-allowed');
            
            const indicator = jumlahInput.parentNode.querySelector('.auto-calc-indicator');
            if (indicator) {
                indicator.remove();
            }
        }
    }

    toggleSubJenisKendaraan();
    toggleNomorPolisi();
    
    // Initialize jumlah field state
    calculateTotalJumlah();

    // Use Select2 change events - include select2-specific events for reliability
    $('#jenis_aktivitas').on('change select2:select select2:unselect', function() {
        jenisAktivitas.value = this.value;
        toggleSubJenisKendaraan();
    });
    
    $('#sub_jenis_kendaraan_select').on('change', function() {
        subJenisSelect.value = this.value;
        toggleNomorPolisi();
    });
    
    $('#jenis_penyesuaian_select').on('change', function() {
        toggleTipePenyesuaian();
    });

    // Force hide jenis/tipes penyesuaian on init unless it's adjust money type
    if (jenisAktivitas.value !== 'Pembayaran Adjusment Uang Jalan') {
        const jenisPenyesuaianFieldInit = document.getElementById('jenis_penyesuaian_field');
        const jenisPenyesuaianSelectInit = document.getElementById('jenis_penyesuaian_select');
        const tipePenyesuaianFieldInit = document.getElementById('tipe_penyesuaian_field');
        if (jenisPenyesuaianFieldInit) jenisPenyesuaianFieldInit.classList.add('hidden');
        if (jenisPenyesuaianSelectInit) { jenisPenyesuaianSelectInit.removeAttribute('required'); $(jenisPenyesuaianSelectInit).val('').trigger('change'); }
        if (tipePenyesuaianFieldInit) tipePenyesuaianFieldInit.classList.add('hidden');
        clearTipePenyesuaianInputs();
    }
    
    // Event listener for surat jalan selection to auto-fill jumlah
    $('#surat_jalan_select').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const uangJalan = selectedOption.data('uang-jalan');
        const jumlahInput = $('input[name="jumlah"]');
        const jenisAktivitas = document.getElementById('jenis_aktivitas').value;
        const jenisPenyesuaian = document.getElementById('jenis_penyesuaian_select').value;
        
        if (jenisAktivitas === 'Pembayaran Adjusment Uang Jalan') {
            if (jenisPenyesuaian === 'pengembalian penuh') {
                if (uangJalan && !isNaN(uangJalan)) {
                    jumlahInput.val(uangJalan);
                    // Trigger input event to update journal preview
                    jumlahInput.trigger('input');
                } else {
                    jumlahInput.val('');
                }
            }
            // For other adjustment types, jumlah is calculated from tipe penyesuaian
        } else {
            // For non-adjustment payments, fill jumlah with uang jalan
            if (uangJalan && !isNaN(uangJalan)) {
                jumlahInput.val(uangJalan);
                // Trigger input event to update journal preview
                jumlahInput.trigger('input');
            } else {
                jumlahInput.val('');
            }
        }
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
    const akunBiayaSelect = document.getElementById('akun_coa_select');
    const akunBankSelect = document.getElementById('akun_bank_select');
    const jumlahInput = document.querySelector('input[name="jumlah"]');
    const journalPreview = document.getElementById('journal_preview');
    const journalContent = document.getElementById('journal_content');
    
    function updateJournalPreview() {
        const jenisTransaksi = debitKreditSelect.value;
        const akunBiaya = akunBiayaSelect.selectedOptions[0];
        const akunBank = akunBankSelect.selectedOptions[0];
        const jumlah = parseFloat(jumlahInput.value) || 0;
        
        if (!jenisTransaksi || !akunBiaya || !akunBank || jumlah <= 0) {
            journalPreview.classList.add('hidden');
            return;
        }
        
        const akunBiayaNama = akunBiaya.dataset.nama || akunBiaya.textContent;
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
                        <p class="text-xs text-green-600">${akunBiayaNama}</p>
                        <p class="font-bold text-green-700">${jumlahFormatted}</p>
                    </div>
                    <div class="bg-red-50 p-2 rounded border border-red-200">
                        <p class="font-medium text-red-700">KREDIT (-)</p>
                        <p class="text-xs text-red-600">${akunBankNama}</p>
                        <p class="font-bold text-red-700">${jumlahFormatted}</p>
                    </div>
                </div>
                <p class="text-xs text-gray-600 mt-1">💡 <strong>Efek:</strong> ${akunBiayaNama} bertambah, ${akunBankNama} berkurang</p>
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
                        <p class="text-xs text-red-600">${akunBiayaNama}</p>
                        <p class="font-bold text-red-700">${jumlahFormatted}</p>
                    </div>
                </div>
                <p class="text-xs text-gray-600 mt-1">💡 <strong>Efek:</strong> ${akunBankNama} bertambah, ${akunBiayaNama} berkurang</p>
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
            { id: 'akun_coa_select', name: 'Akun Biaya' },
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
        } else if (jenisAktivitas === 'Pembayaran Adjusment Uang Jalan') {
            requiredFields.push({ id: 'surat_jalan_select', name: 'Surat Jalan' });
            requiredFields.push({ id: 'jenis_penyesuaian_select', name: 'Jenis Penyesuaian' });
            const jenisPenyesuaian = document.getElementById('jenis_penyesuaian_select').value;
            if (jenisPenyesuaian !== 'pengembalian penuh' && jenisPenyesuaian !== 'pengembalian sebagian') {
                // For non-full refund and non-partial refund, require tipe penyesuaian
                const container = document.getElementById('tipe_penyesuaian_container');
                const nominalInputs = container.querySelectorAll('input[name*="[nominal]"]');
                if (nominalInputs.length === 0 || Array.from(nominalInputs).every(input => !input.value.trim())) {
                    showErrorMessage('Minimal harus ada satu tipe penyesuaian dengan nominal!');
                    return false;
                }
            }
        }
        for (const field of requiredFields) {
            let element;
            if (field.id) {
                element = document.getElementById(field.id);
            } else {
                element = document.querySelector(`[name="${field.name.toLowerCase()}"]`) || 
                         document.querySelector(`[name="${field.name}"]`);
            }
            
            let value = element ? element.value.trim() : '';
            
            if (!element || !value) {
                showErrorMessage(`${field.name} harus diisi!`);
                if (element) element.focus();
                return false;
            }
        }
        
        // Validate jumlah (skip for auto-calculated adjustment payments)
        const currentJenisAktivitas = document.getElementById('jenis_aktivitas').value;
        if (currentJenisAktivitas !== 'Pembayaran Adjusment Uang Jalan') {
            const jumlah = parseFloat(document.querySelector('[name="jumlah"]').value);
            if (isNaN(jumlah) || jumlah <= 0 || !Number.isInteger(jumlah)) {
                showErrorMessage('Jumlah harus berupa angka bulat positif!');
                document.querySelector('[name="jumlah"]').focus();
                return false;
            }
        } else {
            const jenisPenyesuaian = document.getElementById('jenis_penyesuaian_select').value;
            if (jenisPenyesuaian === 'pengembalian sebagian') {
                const jumlah = parseFloat(document.querySelector('[name="jumlah"]').value);
                if (isNaN(jumlah) || jumlah <= 0 || !Number.isInteger(jumlah)) {
                    showErrorMessage('Jumlah pengembalian sebagian harus berupa angka bulat positif!');
                    document.querySelector('[name="jumlah"]').focus();
                    return false;
                }
            } else {
                // For other adjustment types, validate that total is > 0
                const totalJumlah = parseFloat(document.querySelector('[name="jumlah"]').value);
                if (isNaN(totalJumlah) || totalJumlah <= 0) {
                    showErrorMessage('Total nominal tipe penyesuaian harus lebih dari 0!');
                    return false;
                }
            }
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

<script>
// This script block is now empty - can be removed
</script>
@endpush
@endsection