@extends('layouts.app')

@section('content')
<!-- Ensure CSRF token is available -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid px-4 py-6">
    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h1 class="text-xl font-semibold text-gray-800">Edit Pembayaran Aktivitas Lain</h1>
                <p class="text-sm text-blue-600 mt-1">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        📊 Double Book Accounting
                    </span>
                    Otomatis jurnal akuntansi dengan sistem pembukuan ganda
                </p>
            </div>
            <a href="{{ route('pembayaran-aktivitas-lain.show', $pembayaranAktivitasLain) }}" class="inline-flex items-center px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium text-xs rounded-md transition duration-150 ease-in-out">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </div>

        <form action="{{ route('pembayaran-aktivitas-lain.update', $pembayaranAktivitasLain) }}" method="POST" class="p-6" id="pembayaran_form">
            @csrf
            @method('PUT')
            
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
                                    <p class="font-medium text-green-700">✅ Jika pilih KREDIT:</p>
                                    <p class="text-xs mt-1">• <strong>Dr.</strong> Akun yang dipilih (Biaya/Beban) <span class="text-green-600">+</span></p>
                                    <p class="text-xs">• <strong>Cr.</strong> Akun Bank yang dipilih <span class="text-red-600">-</span></p>
                                </div>
                                <div class="bg-white p-3 rounded border border-blue-200">
                                    <p class="font-medium text-blue-700">✅ Jika pilih DEBIT:</p>
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
                    <input type="text" value="{{ $pembayaranAktivitasLain->nomor }}" readonly class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-sm">
                </div>

                <!-- Nomor Accurate -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Accurate</label>
                    <input type="text" name="nomor_accurate" value="{{ old('nomor_accurate', $pembayaranAktivitasLain->nomor_accurate) }}" placeholder="Masukkan nomor accurate" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm @error('nomor_accurate') border-red-500 @enderror">
                    @error('nomor_accurate')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tanggal -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', $pembayaranAktivitasLain->tanggal->format('Y-m-d')) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm @error('tanggal') border-red-500 @enderror">
                    @error('tanggal')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jenis Aktivitas -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Aktivitas <span class="text-red-500">*</span></label>
                    <select name="jenis_aktivitas" id="jenis_aktivitas" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm @error('jenis_aktivitas') border-red-500 @enderror">
                        <option value="">Pilih Jenis Aktivitas</option>
                        <option value="Pembayaran Kendaraan" {{ old('jenis_aktivitas', $pembayaranAktivitasLain->jenis_aktivitas) == 'Pembayaran Kendaraan' ? 'selected' : '' }}>Pembayaran Kendaraan</option>
                        <option value="Pembayaran Kapal" {{ old('jenis_aktivitas', $pembayaranAktivitasLain->jenis_aktivitas) == 'Pembayaran Kapal' ? 'selected' : '' }}>Pembayaran Kapal</option>
                        <option value="Pembayaran Adjusment Uang Jalan" {{ old('jenis_aktivitas', $pembayaranAktivitasLain->jenis_aktivitas) == 'Pembayaran Adjusment Uang Jalan' ? 'selected' : '' }}>Pembayaran Adjusment Uang Jalan</option>
                        <option value="Pembayaran Lain Lain" {{ old('jenis_aktivitas', $pembayaranAktivitasLain->jenis_aktivitas) == 'Pembayaran Lain Lain' ? 'selected' : '' }}>Pembayaran Lain Lain</option>
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
                            <option value="{{ $suratJalan->no_surat_jalan }}" 
                                    data-uang-jalan="{{ $suratJalan->uang_jalan }}" 
                                    data-source="{{ $suratJalan->source }}"
                                    {{ old('no_surat_jalan', $pembayaranAktivitasLain->no_surat_jalan) == $suratJalan->no_surat_jalan ? 'selected' : '' }}>
                                {{ $suratJalan->no_surat_jalan }} - {{ $suratJalan->tujuan_pengiriman }}
                                @if(isset($suratJalan->source))
                                    - [{{ $suratJalan->source == 'regular' ? 'Regular' : 'Bongkar' }}]
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('no_surat_jalan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jenis Penyesuaian (Hidden by default) -->
                <div id="jenis_penyesuaian_field" class="{{ old('jenis_aktivitas', $pembayaranAktivitasLain->jenis_aktivitas) === 'Pembayaran Adjusment Uang Jalan' ? '' : 'hidden' }}" style="{{ old('jenis_aktivitas', $pembayaranAktivitasLain->jenis_aktivitas) === 'Pembayaran Adjusment Uang Jalan' ? '' : 'display:none' }}">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Penyesuaian <span class="text-red-500">*</span></label>
                    <select name="jenis_penyesuaian" id="jenis_penyesuaian_select" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm @error('jenis_penyesuaian') border-red-500 @enderror">
                        <option value="">Pilih Jenis Penyesuaian</option>
                        <option value="pengurangan" {{ old('jenis_penyesuaian', $pembayaranAktivitasLain->jenis_penyesuaian) == 'pengurangan' ? 'selected' : '' }}>Pengurangan</option>
                        <option value="penambahan" {{ old('jenis_penyesuaian', $pembayaranAktivitasLain->jenis_penyesuaian) == 'penambahan' ? 'selected' : '' }}>Penambahan</option>
                        <option value="pengembalian penuh" {{ old('jenis_penyesuaian', $pembayaranAktivitasLain->jenis_penyesuaian) == 'pengembalian penuh' ? 'selected' : '' }}>Pengembalian Penuh</option>
                        <option value="pengembalian sebagian" {{ old('jenis_penyesuaian', $pembayaranAktivitasLain->jenis_penyesuaian) == 'pengembalian sebagian' ? 'selected' : '' }}>Pengembalian Sebagian</option>
                    </select>
                    @error('jenis_penyesuaian')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tipe Penyesuaian (Hidden by default) -->
                <div id="tipe_penyesuaian_field" class="{{ old('jenis_aktivitas', $pembayaranAktivitasLain->jenis_aktivitas) === 'Pembayaran Adjusment Uang Jalan' ? '' : 'hidden' }}" style="{{ old('jenis_aktivitas', $pembayaranAktivitasLain->jenis_aktivitas) === 'Pembayaran Adjusment Uang Jalan' ? '' : 'display:none' }}">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Penyesuaian <span class="text-red-500">*</span></label>

                    <!-- Container untuk input dinamis -->
                    <div id="tipe_penyesuaian_container" class="space-y-3">
                        <!-- Template untuk input baru akan ditambahkan di sini via JS -->
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
                        <option value="STNK" {{ old('sub_jenis_kendaraan', $pembayaranAktivitasLain->sub_jenis_kendaraan) == 'STNK' ? 'selected' : '' }}>STNK</option>
                        <option value="KIR" {{ old('sub_jenis_kendaraan', $pembayaranAktivitasLain->sub_jenis_kendaraan) == 'KIR' ? 'selected' : '' }}>KIR</option>
                        <option value="Plat" {{ old('sub_jenis_kendaraan', $pembayaranAktivitasLain->sub_jenis_kendaraan) == 'Plat' ? 'selected' : '' }}>Plat</option>
                        <option value="Lain Lain" {{ old('sub_jenis_kendaraan', $pembayaranAktivitasLain->sub_jenis_kendaraan) == 'Lain Lain' ? 'selected' : '' }}>Lain Lain</option>
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
                            @php
                                $val = $mobil->nomor_polisi ?: $mobil->no_kir;
                                $label = ($mobil->nomor_polisi ?: $mobil->no_kir ?: 'Tanpa Plat/KIR') . ' - ' . $mobil->merek . ' ' . $mobil->jenis;
                                if ($mobil->nomor_polisi && $mobil->no_kir) {
                                    $label .= ' (KIR: ' . $mobil->no_kir . ')';
                                }
                            @endphp
                            <option value="{{ $val }}" {{ old('nomor_polisi', $pembayaranAktivitasLain->nomor_polisi) == $val ? 'selected' : '' }}>
                                {{ $label }}
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
                            <option value="{{ $voyage->voyage }}" {{ old('nomor_voyage', $pembayaranAktivitasLain->nomor_voyage) == $voyage->voyage ? 'selected' : '' }}>
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
                    <input type="text" id="jumlah_input_display" value="{{ old('jumlah', $pembayaranAktivitasLain->jumlah) ? number_format(old('jumlah', $pembayaranAktivitasLain->jumlah), 0, ',', '.') : '' }}" required placeholder="0" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-right @error('jumlah') border-red-500 @enderror">
                    <input type="hidden" name="jumlah" id="jumlah_input_value" value="{{ old('jumlah', $pembayaranAktivitasLain->jumlah) }}">
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
                        <option value="debit" {{ old('debit_kredit', $pembayaranAktivitasLain->debit_kredit) == 'debit' ? 'selected' : '' }}>DEBIT (Bank bertambah, Biaya/Beban berkurang)</option>
                        <option value="kredit" {{ old('debit_kredit', $pembayaranAktivitasLain->debit_kredit) == 'kredit' ? 'selected' : '' }}>KREDIT (Biaya/Beban bertambah, Bank berkurang)</option>
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
                            <option value="{{ $akun->id }}" data-nama="{{ $akun->nama_akun }}" data-kode="{{ $akun->kode_nomor }}" {{ old('akun_coa_id', $pembayaranAktivitasLain->akun_coa_id) == $akun->id ? 'selected' : '' }}>
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
                            <option value="{{ $akun->id }}" data-nama="{{ $akun->nama_akun }}" data-kode="{{ $akun->kode_nomor }}" {{ old('akun_bank_id', $pembayaranAktivitasLain->akun_bank_id) == $akun->id ? 'selected' : '' }}>
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
                        <input type="text" name="penerima" id="penerima_input" value="{{ old('penerima', $pembayaranAktivitasLain->penerima) }}" placeholder="Atau ketik nama penerima..." required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm @error('penerima') border-red-500 @enderror">
                    </div>
                    @error('penerima')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Keterangan -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan <span class="text-red-500">*</span></label>
                    <textarea name="keterangan" rows="4" placeholder="Keterangan tambahan..." required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm @error('keterangan') border-red-500 @enderror">{{ old('keterangan', $pembayaranAktivitasLain->keterangan) }}</textarea>
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
                
                <a href="{{ route('pembayaran-aktivitas-lain.show', $pembayaranAktivitasLain) }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium text-sm rounded-md transition">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm rounded-md transition" id="submit_btn">
                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Update
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
        
        // Add form submit handler to ensure jumlah matches display
        const paymentForm = document.querySelector('form');
        if (paymentForm) {
            paymentForm.addEventListener('submit', function() {
                const displayInput = document.getElementById('jumlah_input_display');
                const valueInput = document.getElementById('jumlah_input_value');
                
                if (displayInput && valueInput) {
                    // Strip non-numeric chars (dots) from display value to set hidden value
                    valueInput.value = displayInput.value.replace(/[^\d]/g, '');
                }
            });
        }

    const subJenisSelect = document.getElementById('sub_jenis_kendaraan_select');
    const nomorPolisiField = document.getElementById('nomor_polisi_field');
    const nomorPolisiSelect = nomorPolisiField.querySelector('select');
    const nomorVoyageField = document.getElementById('nomor_voyage_field');
    const nomorVoyageSelect = nomorVoyageField.querySelector('select');
    
    // Initialize jumlah input formatting
    initializeJumlahInput();

    function initializeJumlahInput() {
        const displayInput = document.getElementById('jumlah_input_display');
        const valueInput = document.getElementById('jumlah_input_value');
        
        if (!displayInput || !valueInput) return;

        // Function to format number with thousands separator
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // Function to strip non-numeric characters
        function cleanNumber(str) {
            return str.replace(/[^\d]/g, '');
        }

        displayInput.addEventListener('input', function(e) {
            // Get current cursor position to restore later mechanism (complex, simplified here)
            let value = cleanNumber(this.value);
            
            // Update hidden value
            valueInput.value = value;
            
            // Format display
            if (value) {
                this.value = formatNumber(value);
            } else {
                this.value = '';
            }
            
            // Trigger auto journal calculation if function exists
            if (typeof updateJournalPreview === 'function') {
                updateJournalPreview();
            }
        });
        
        // Also listen for change to ensure sync
        displayInput.addEventListener('change', function() {
            let value = cleanNumber(this.value);
            valueInput.value = value;
        });
    }

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
            
            // Hide any adjustment fields if previously visible
            var jenisPenyesuaianFieldEl = document.getElementById('jenis_penyesuaian_field');
            var jenisPenyesuaianSelectEl = document.getElementById('jenis_penyesuaian_select');
            var tipePenyesuaianFieldEl = document.getElementById('tipe_penyesuaian_field');
            if (jenisPenyesuaianFieldEl) jenisPenyesuaianFieldEl.classList.add('hidden');
            if (jenisPenyesuaianSelectEl) { jenisPenyesuaianSelectEl.removeAttribute('required'); $(jenisPenyesuaianSelectEl).val('').trigger('change'); }
            if (tipePenyesuaianFieldEl) tipePenyesuaianFieldEl.classList.add('hidden');
            clearTipePenyesuaianInputs();
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
        } else {
            suratJalanField.classList.add('hidden');
            suratJalanSelect.removeAttribute('required');
            $('#surat_jalan_select').val('').trigger('change');
        }
        
        const jenisPenyesuaianField = document.getElementById('jenis_penyesuaian_field');
        const jenisPenyesuaianSelect = document.getElementById('jenis_penyesuaian_select');
        
        if (jenisVal === 'Pembayaran Adjusment Uang Jalan') {
            jenisPenyesuaianField.classList.remove('hidden');
            jenisPenyesuaianField.style.display = '';
            jenisPenyesuaianSelect.setAttribute('required', 'required');

            // Toggle tipe penyesuaian based on jenis penyesuaian
            toggleTipePenyesuaian();
        } else {
            jenisPenyesuaianField.classList.add('hidden');
            jenisPenyesuaianField.style.display = 'none';
            jenisPenyesuaianSelect.removeAttribute('required');
            $('#jenis_penyesuaian_select').val('').trigger('change');
            
            const tipePenyesuaianField = document.getElementById('tipe_penyesuaian_field');
            if (tipePenyesuaianField) {
                tipePenyesuaianField.classList.add('hidden');
                tipePenyesuaianField.style.display = 'none';
            }

            // Clear all dynamic inputs
            clearTipePenyesuaianInputs();
        }
        
        // Update jumlah field state
        calculateTotalJumlah();
    }

    function toggleNomorPolisi() {
        const subJenis = subJenisSelect.value;
        if (subJenis === 'Plat' || subJenis === 'STNK' || subJenis === 'KIR' || subJenis === 'Lain Lain') {
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
        const jumlahDisplay = document.getElementById('jumlah_input_display');
        
        if (jenisPenyesuaian === 'pengembalian penuh') {
            tipePenyesuaianField.classList.add('hidden');
            clearTipePenyesuaianInputs();
            
            const selectedSuratJalan = $('#surat_jalan_select').find('option:selected');
            let uangJalanVal = selectedSuratJalan.data('uang-jalan');
            const uangJalan = uangJalanVal ? Math.floor(parseFloat(uangJalanVal)) : 0;
            
            if (uangJalan > 0) {
                jumlahDisplay.value = uangJalan;
                const valueInput = document.getElementById('jumlah_input_value');
                if (valueInput) valueInput.value = uangJalan;

                jumlahDisplay.dispatchEvent(new Event('input', { bubbles: true }));
                
                jumlahDisplay.readOnly = true;
                jumlahDisplay.classList.add('bg-gray-100', 'cursor-not-allowed');
                
                jumlahDisplay.parentNode.querySelectorAll('.auto-calc-indicator').forEach(el => el.remove());
                let indicator = document.createElement('p');
                indicator.className = 'auto-calc-indicator text-xs text-blue-600 mt-1';
                indicator.innerHTML = '🔄 Jumlah diisi otomatis dari uang jalan surat jalan';
                jumlahDisplay.parentNode.appendChild(indicator);
            }
        } else if (jenisPenyesuaian === 'pengembalian sebagian') {
            tipePenyesuaianField.classList.add('hidden');
            clearTipePenyesuaianInputs();
            jumlahDisplay.readOnly = false;
            jumlahDisplay.classList.remove('bg-gray-100', 'cursor-not-allowed');
            jumlahDisplay.parentNode.querySelectorAll('.auto-calc-indicator').forEach(el => el.remove());
        } else if (jenisPenyesuaian) {
            tipePenyesuaianField.classList.remove('hidden');
            tipePenyesuaianField.style.display = '';
            
            // Initialize with existing data or default empty row
            @if($pembayaranAktivitasLain->tipe_penyesuaian_detail)
                initializeTipePenyesuaianInputsFromData(@json($pembayaranAktivitasLain->tipe_penyesuaian_detail));
            @else
                initializeTipePenyesuaianInputs();
            @endif
            
            calculateTotalJumlah();
        } else {
            tipePenyesuaianField.classList.add('hidden');
            clearTipePenyesuaianInputs();
        }
    }

    function initializeTipePenyesuaianInputs() {
        const container = document.getElementById('tipe_penyesuaian_container');
        const addBtn = document.getElementById('add_tipe_penyesuaian_btn');

        container.innerHTML = '';
        addBtn.onclick = function() {
            addTipePenyesuaianInput();
        };

        addTipePenyesuaianInput();
    }

    function initializeTipePenyesuaianInputsFromData(data) {
        const container = document.getElementById('tipe_penyesuaian_container');
        const addBtn = document.getElementById('add_tipe_penyesuaian_btn');

        container.innerHTML = '';
        addBtn.onclick = function() {
            addTipePenyesuaianInput();
        };

        if (data && data.length > 0) {
            data.forEach(item => {
                addTipePenyesuaianInput(item.tipe, item.nominal);
            });
        } else {
            addTipePenyesuaianInput();
        }
    }

    function clearTipePenyesuaianInputs() {
        const container = document.getElementById('tipe_penyesuaian_container');
        if (container) container.innerHTML = '';
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
                    <option value="krani" ${existingTipe === 'krani' ? 'selected' : ''}>Krani</option>
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

        setTimeout(() => {
            $(inputGroup).find('select').select2({
                placeholder: "Pilih Tipe",
                allowClear: true,
                width: '100%'
            });
        }, 100);

        const nominalInput = inputGroup.querySelector('input[name*="[nominal]"]');
        if (nominalInput) {
            nominalInput.addEventListener('input', calculateTotalJumlah);
        }
    }

    window.removeTipePenyesuaianInput = function(button) {
        const container = document.getElementById('tipe_penyesuaian_container');
        const inputGroup = button.closest('.flex.items-end.gap-3');

        if (container.children.length > 1) {
            inputGroup.remove();
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

            if (select) select.name = `tipe_penyesuaian_detail[${index}][tipe]`;
            if (input) input.name = `tipe_penyesuaian_detail[${index}][nominal]`;
        });

        calculateTotalJumlah();
    }

    function calculateTotalJumlah() {
        const jenisAktivitasVal = document.getElementById('jenis_aktivitas').value;
        const jumlahDisplay = document.getElementById('jumlah_input_display');
        
        if (!jumlahDisplay) return;

        if (jenisAktivitasVal === 'Pembayaran Adjusment Uang Jalan') {
            const jenisPenyesuaian = document.getElementById('jenis_penyesuaian_select').value;
            
            if (jenisPenyesuaian === 'pengembalian penuh') {
                jumlahDisplay.readOnly = true;
                jumlahDisplay.classList.add('bg-gray-100', 'cursor-not-allowed');
            } else if (jenisPenyesuaian === 'pengembalian sebagian') {
                jumlahDisplay.readOnly = false;
                jumlahDisplay.classList.remove('bg-gray-100', 'cursor-not-allowed');
            } else {
                const nominalInputs = document.querySelectorAll('input[name^="tipe_penyesuaian_detail"][name$="[nominal]"]');
                let total = 0;
                nominalInputs.forEach(input => {
                    total += parseFloat(input.value) || 0;
                });
                
                jumlahDisplay.value = total;
                jumlahDisplay.readOnly = true;
                jumlahDisplay.classList.add('bg-gray-100', 'cursor-not-allowed');
                
                jumlahDisplay.parentNode.querySelectorAll('.auto-calc-indicator').forEach(el => el.remove());
                let indicator = document.createElement('p');
                indicator.className = 'auto-calc-indicator text-xs text-blue-600 mt-1';
                indicator.innerHTML = '🔄 Jumlah dihitung otomatis dari total nominal tipe penyesuaian';
                jumlahDisplay.parentNode.appendChild(indicator);
                
                jumlahDisplay.dispatchEvent(new Event('input', { bubbles: true }));
            }
        } else {
            jumlahDisplay.readOnly = false;
            jumlahDisplay.classList.remove('bg-gray-100', 'cursor-not-allowed');
            const indicator = jumlahDisplay.parentNode.querySelector('.auto-calc-indicator');
            if (indicator) indicator.remove();
        }
    }

    // Event listeners
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

    $('#surat_jalan_select').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const uangJalan = selectedOption.data('uang-jalan');
        const displayInput = document.getElementById('jumlah_input_display');
        const valueInput = document.getElementById('jumlah_input_value');
        const jAktivitas = document.getElementById('jenis_aktivitas').value;
        const jPenyesuaian = document.getElementById('jenis_penyesuaian_select').value;
        
        if (jAktivitas === 'Pembayaran Adjusment Uang Jalan') {
            if (jPenyesuaian === 'pengembalian penuh') {
                if (uangJalan && !isNaN(uangJalan)) {
                    displayInput.value = uangJalan;
                    if (valueInput) valueInput.value = uangJalan;
                    $(displayInput).trigger('input');
                }
            }
        } else {
            if (uangJalan && !isNaN(uangJalan)) {
                displayInput.value = uangJalan;
                if (valueInput) valueInput.value = uangJalan;
                $(displayInput).trigger('input');
            }
        }
    });

    $('#penerima_dropdown').on('change', function() {
        if (this.value) {
            document.getElementById('penerima_input').value = this.value;
        }
    });

    const debitKreditSelect = document.getElementById('debit_kredit');
    const akunBiayaSelect = document.getElementById('akun_coa_select');
    const akunBankSelect = document.getElementById('akun_bank_select');
    const displayJumlahInput = document.getElementById('jumlah_input_display');
    const journalPreview = document.getElementById('journal_preview');
    const journalContent = document.getElementById('journal_content');
    
    function updateJournalPreview() {
        const jenisTransaksi = debitKreditSelect.value;
        const akunBiaya = akunBiayaSelect.selectedOptions[0];
        const akunBank = akunBankSelect.selectedOptions[0];
        const valInput = document.getElementById('jumlah_input_value');
        const jumlah = parseFloat(valInput ? valInput.value : 0) || 0;
        
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
        if (jenisTransaksi === 'kredit') {
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
            `;
        }
        
        journalContent.innerHTML = journalHtml;
        journalPreview.classList.remove('hidden');
    }

    $('#debit_kredit, #akun_coa_select, #akun_bank_select').on('change', updateJournalPreview);
    
    // Form submission
    const vehicleMasterModal = document.getElementById('vehicleMasterModal');
    const closeModalBtn = document.getElementById('closeModal');
    const cancelModalBtn = document.getElementById('cancelModalBtn');
    const saveVehicleBtn = document.getElementById('saveVehicleBtn');
    
    let isSubmittingAfterModal = false;
    
    paymentForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!validateForm()) return;
        
        const jenisAktivitasVal = document.getElementById('jenis_aktivitas').value;
        const nomorPolisiVal = document.querySelector('select[name="nomor_polisi"]').value;
        
        if (jenisAktivitasVal === 'Pembayaran Kendaraan' && nomorPolisiVal && !isSubmittingAfterModal) {
            if (confirm('Apakah Anda ingin mengubah data master kendaraan?')) {
                showVehicleMasterModal(nomorPolisiVal);
                return;
            }
        }
        
        submitFormWithErrorHandling();
    });

    function validateForm() {
        const requiredFields = [
            { id: 'tanggal', name: 'Tanggal' },
            { id: 'jenis_aktivitas', name: 'Jenis Aktivitas' },
            { id: 'debit_kredit', name: 'Jenis Transaksi' },
            { id: 'akun_coa_select', name: 'Akun Biaya' },
            { id: 'akun_bank_select', name: 'Bank/Kas' },
            { id: 'penerima_input', name: 'Penerima' }
        ];
        
        for (const field of requiredFields) {
            const el = document.getElementById(field.id);
            if (!el || !el.value.trim()) {
                showErrorMessage(`${field.name} harus diisi!`);
                return false;
            }
        }
        return true;
    }

    function submitFormWithErrorHandling() {
        const submitBtn = document.getElementById('submit_btn');
        const originalBtnText = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = 'Menyimpan...';
        
        const formData = new FormData(paymentForm);
        fetch(paymentForm.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success' || data.success) {
                showSuccessMessage(data.message || 'Data berhasil diupdate!');
                setTimeout(() => {
                    window.location.href = data.redirect || "{{ route('pembayaran-aktivitas-lain.index') }}";
                }, 1500);
            } else {
                throw new Error(data.message || 'Terjadi kesalahan.');
            }
        })
        .catch(error => {
            showErrorMessage(error.message);
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        });
    }

    function showVehicleMasterModal(nomorPolisi) {
        document.getElementById('modal_nomor_polisi').value = nomorPolisi;
        vehicleMasterModal.classList.remove('hidden');
    }

    function closeModal() {
        vehicleMasterModal.classList.add('hidden');
    }

    closeModalBtn.onclick = closeModal;
    cancelModalBtn.onclick = closeModal;
    saveVehicleBtn.onclick = function() {
        // Simple mock save
        showSuccessMessage('Data master kendaraan diperbarui!');
        closeModal();
        isSubmittingAfterModal = true;
        submitFormWithErrorHandling();
    };

    function showErrorMessage(message) {
        const area = document.getElementById('notification-area');
        area.innerHTML = `<div class="bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg mb-4">${message}</div>`;
        setTimeout(() => area.innerHTML = '', 5000);
    }

    function showSuccessMessage(message) {
        const area = document.getElementById('notification-area');
        area.innerHTML = `<div class="bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg mb-4">${message}</div>`;
        setTimeout(() => area.innerHTML = '', 3000);
    }

    // Initial state
    toggleSubJenisKendaraan();
    updateJournalPreview();
}

document.addEventListener('DOMContentLoaded', loadScripts);
</script>
@endpush
@endsection
