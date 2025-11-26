@extends('layouts.app')

@section('title', 'Tambah Surat Jalan Bongkaran')

@section('content')
<div class="container mx-auto px-4 py-4">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-4 border-b border-gray-200">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Tambah Surat Jalan Bongkaran</h1>
                <p class="text-xs text-gray-600 mt-1">Buat surat jalan bongkaran baru untuk pengiriman barang</p>
            </div>
            <a href="{{ route('surat-jalan-bongkaran.index') }}"
               class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-lg transition-colors duration-150 flex items-center text-sm whitespace-nowrap">
                <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali
            </a>
        </div>

        @if($selectedKapal && $noVoyage)
        <!-- Selected Kapal & Voyage Info -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg mx-4 mt-4 p-4">
            <div class="flex items-start">
                <svg class="h-5 w-5 text-blue-400 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <div class="flex-1">
                    <h4 class="text-sm font-medium text-blue-800">Kapal & Voyage Terpilih</h4>
                    <div class="mt-1 text-sm text-blue-700">
                        <strong>{{ $selectedKapal->nama_kapal }}</strong> | Voyage: <strong>{{ $noVoyage }}</strong>
                        @if(request('no_bl'))
                            | BL: <strong>{{ request('no_bl') }}</strong>
                        @endif
                    </div>
                    <input type="hidden" name="kapal_id" value="{{ $selectedKapal->id }}">
                    <input type="hidden" name="no_voyage" value="{{ $noVoyage }}">
                    @if(request('no_bl'))
                        <input type="hidden" name="no_bl" value="{{ request('no_bl') }}">
                    @endif
                </div>
                <a href="{{ route('surat-jalan-bongkaran.select-kapal') }}"
                   class="text-blue-600 hover:text-blue-800 text-xs font-medium">
                    Ubah Pilihan
                </a>
            </div>
        </div>
        @endif

        @if(isset($selectedContainer) && $selectedContainer->nomor_kontainer)
        <!-- Selected Container Info -->
        <div class="bg-green-50 border border-green-200 rounded-lg mx-4 mt-4 p-4">
            <div class="flex items-center">
                <svg class="h-5 w-5 text-green-400 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <div class="flex-1">
                    <h4 class="text-sm font-medium text-green-800">Kontainer Terpilih</h4>
                    <div class="mt-1 text-sm text-green-700">
                        <strong>{{ $selectedContainer->nomor_kontainer }}</strong>
                        @if($selectedContainer->no_seal)
                            | Seal: <strong>{{ $selectedContainer->no_seal }}</strong>
                        @endif
                        @if(isset($selectedContainer->size_kontainer) && $selectedContainer->size_kontainer)
                            | Size: <strong>{{ strtoupper($selectedContainer->size_kontainer) }}</strong>
                        @elseif(isset($selectedContainer->tipe_kontainer) && $selectedContainer->tipe_kontainer)
                            | Size: <strong>{{ strtoupper($selectedContainer->tipe_kontainer) }}</strong>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Form -->
        <form action="{{ route('surat-jalan-bongkaran.store') }}" method="POST" class="p-4">
            @csrf

    <!-- Alert Messages -->
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
            <div class="flex">
                <svg class="w-5 h-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <h4 class="font-medium">Terdapat kesalahan pada form:</h4>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Basic Information -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Informasi Dasar</h3>
                </div>
                    <!-- Kapal (if not pre-selected) -->
                    @if(!isset($selectedKapal))
                    <div>
                        <label for="kapal_id" class="block text-sm font-medium text-gray-700 mb-1">Kapal</label>
                        <select name="kapal_id" id="kapal_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('kapal_id') border-red-300 @enderror">
                            <option value="">Pilih Kapal</option>
                            @foreach($kapals as $kapal)
                                <option value="{{ $kapal->id }}" {{ old('kapal_id') == $kapal->id ? 'selected' : '' }}>
                                    {{ $kapal->nama_kapal }}
                                </option>
                            @endforeach
                        </select>
                        @error('kapal_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    @endif

                    <!-- Nomor Surat Jalan -->
                    <div>
                        <label for="nomor_surat_jalan" class="block text-sm font-medium text-gray-700 mb-1">
                            Nomor Surat Jalan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nomor_surat_jalan" id="nomor_surat_jalan" required
                               value="{{ old('nomor_surat_jalan') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nomor_surat_jalan') border-red-300 @enderror"
                               placeholder="Masukkan nomor surat jalan">
                        @error('nomor_surat_jalan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Surat Jalan -->
                    <div>
                        <label for="tanggal_surat_jalan" class="block text-sm font-medium text-gray-700 mb-1">
                            Tanggal Surat Jalan <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal_surat_jalan" id="tanggal_surat_jalan" required
                               value="{{ old('tanggal_surat_jalan') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tanggal_surat_jalan') border-red-300 @enderror">
                        @error('tanggal_surat_jalan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Term -->
                    <div>
                        <label for="term" class="block text-sm font-medium text-gray-700 mb-1">Term</label>
                        <select name="term" id="term"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('term') border-red-300 @enderror">
                            <option value="">Pilih Term</option>
                            @foreach($terms as $term)
                                <option value="{{ $term->kode }}" {{ old('term') == $term->kode ? 'selected' : '' }}>
                                    {{ $term->kode }} - {{ $term->nama_status }}
                                </option>
                            @endforeach
                        </select>
                        @error('term')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                <!-- Aktifitas -->
                <div>
                    <label for="aktifitas" class="block text-sm font-medium text-gray-700 mb-1">Aktifitas</label>
                    <select name="aktifitas" id="aktifitas"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('aktifitas') border-red-500 @enderror">
                        <option value="">Pilih aktifitas</option>
                        @foreach($masterKegiatans as $kegiatan)
                            <option value="{{ $kegiatan->nama_kegiatan }}" {{ old('aktifitas') == $kegiatan->nama_kegiatan ? 'selected' : '' }}>
                                {{ $kegiatan->nama_kegiatan }}
                            </option>
                        @endforeach
                    </select>
                    @error('aktifitas')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Informasi Pengiriman -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Informasi Pengiriman</h3>
                </div>

                <!-- Pengirim -->
                <div>
                    <label for="pengirim" class="block text-sm font-medium text-gray-700 mb-1">Pengirim</label>
                    <input type="text" name="pengirim" id="pengirim" readonly
                           value="{{ old('pengirim', isset($selectedContainer) ? $selectedContainer->pengirim : '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 focus:outline-none @error('pengirim') border-red-500 @enderror"
                           placeholder="Pengirim akan terisi otomatis">
                    @error('pengirim')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @if(isset($selectedContainer) && $selectedContainer->pengirim)
                        <p class="mt-1 text-xs text-green-600">
                            Otomatis dari kontainer terpilih
                        </p>
                    @endif
                </div>

                <!-- Tujuan Alamat -->
                <div>
                    <label for="tujuan_alamat" class="block text-sm font-medium text-gray-700 mb-1">Tujuan Alamat</label>
                    <input type="text" name="tujuan_alamat" id="tujuan_alamat"
                           value="{{ old('tujuan_alamat', isset($selectedContainer) ? $selectedContainer->alamat_pengiriman : '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('tujuan_alamat') border-red-500 @enderror"
                           placeholder="Masukkan tujuan alamat">
                    @error('tujuan_alamat')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @if(isset($selectedContainer) && $selectedContainer->alamat_pengiriman)
                        <p class="mt-1 text-xs text-blue-600">
                            Otomatis dari kontainer terpilih
                        </p>
                    @endif
                </div>

                <!-- Tujuan Pengambilan -->
                <div>
                    <label for="tujuan_pengambilan" class="block text-sm font-medium text-gray-700 mb-1">Tujuan Pengambilan</label>
                    <select name="tujuan_pengambilan" id="tujuan_pengambilan"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('tujuan_pengambilan') border-red-500 @enderror">
                        <option value="">Pilih tujuan pengambilan</option>
                        @foreach($tujuanKegiatanUtamas as $tujuan)
                            <option value="{{ $tujuan->ke }}" {{ old('tujuan_pengambilan') == $tujuan->ke ? 'selected' : '' }}>
                                {{ $tujuan->ke }}
                            </option>
                        @endforeach
                    </select>
                    @error('tujuan_pengambilan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tujuan Pengiriman -->
                <div>
                    <label for="tujuan_pengiriman" class="block text-sm font-medium text-gray-700 mb-1">Tujuan Pengiriman</label>
                    <input type="text" name="tujuan_pengiriman" id="tujuan_pengiriman" readonly
                           value="{{ old('tujuan_pengiriman', isset($selectedContainer) ? $selectedContainer->pelabuhan_tujuan : '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 focus:outline-none @error('tujuan_pengiriman') border-red-500 @enderror"
                           placeholder="Tujuan pengiriman akan terisi otomatis">
                    @error('tujuan_pengiriman')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @if(isset($selectedContainer) && $selectedContainer->pelabuhan_tujuan)
                        <p class="mt-1 text-xs text-green-600">
                            Otomatis dari BL: {{ $selectedContainer->pelabuhan_tujuan }}
                        </p>
                    @endif
                </div>

                <!-- Jenis Pengiriman -->
                <div>
                    <label for="jenis_pengiriman" class="block text-sm font-medium text-gray-700 mb-1">Jenis Pengiriman</label>
                    @php
                        $defaultJenisPengiriman = '';
                        if (isset($selectedContainer) && isset($selectedContainer->jenis_pengiriman)) {
                            $defaultJenisPengiriman = $selectedContainer->jenis_pengiriman;
                        }
                        $selectedJenisPengiriman = old('jenis_pengiriman', $defaultJenisPengiriman);
                    @endphp
                    <select name="jenis_pengiriman" id="jenis_pengiriman"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('jenis_pengiriman') border-red-500 @enderror">
                        <option value="">Pilih jenis pengiriman</option>
                        <option value="FCL" {{ $selectedJenisPengiriman == 'FCL' ? 'selected' : '' }}>FCL</option>
                        <option value="LCL" {{ $selectedJenisPengiriman == 'LCL' ? 'selected' : '' }}>LCL</option>
                        <option value="Cargo" {{ $selectedJenisPengiriman == 'Cargo' ? 'selected' : '' }}>Cargo</option>
                    </select>
                    @error('jenis_pengiriman')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @if(isset($selectedContainer) && $selectedContainer->jenis_pengiriman)
                        <p class="mt-1 text-xs text-blue-600">
                            Otomatis dari kontainer terpilih: {{ $selectedContainer->jenis_pengiriman }}
                        </p>
                    @else
                        <p class="mt-1 text-xs text-gray-500">Belum ada jenis pengiriman dari kontainer</p>
                    @endif
                </div>

                <!-- Tanggal Ambil Barang -->
                <div>
                    <label for="tanggal_ambil_barang" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Ambil Barang</label>
                    <input type="date" name="tanggal_ambil_barang" id="tanggal_ambil_barang"
                           value="{{ old('tanggal_ambil_barang') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('tanggal_ambil_barang') border-red-500 @enderror">
                    @error('tanggal_ambil_barang')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Informasi Personal -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Informasi Personal</h3>
                </div>
                <!-- Supir -->
                <div>
                    <label for="supir" class="block text-sm font-medium text-gray-700 mb-1">Supir</label>
                    <select name="supir" id="supir"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('supir') border-red-500 @enderror">
                        <option value="">Pilih Supir</option>
                        @foreach($karyawanSupirs as $supir)
                            <option value="{{ $supir->nama_lengkap }}" 
                                    data-plat="{{ $supir->plat }}"
                                    {{ old('supir') == $supir->nama_lengkap ? 'selected' : '' }}>
                                {{ $supir->nama_panggilan ?? $supir->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-blue-600">
                        Ketik nama supir untuk mencari dengan cepat
                    </p>
                    @error('supir')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- No Plat -->
                <div>
                    <label for="no_plat" class="block text-sm font-medium text-gray-700 mb-1">No Plat</label>
                    <input type="text" name="no_plat" id="no_plat"
                           value="{{ old('no_plat') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('no_plat') border-red-500 @enderror"
                           placeholder="Masukkan nomor plat">
                    @error('no_plat')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kenek -->
                <div>
                    <label for="kenek" class="block text-sm font-medium text-gray-700 mb-1">Kenek</label>
                    <select name="kenek" id="kenek"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('kenek') border-red-500 @enderror">
                        <option value="">Pilih Kenek</option>
                        @foreach($karyawanKranis as $krani)
                            <option value="{{ $krani->nama_lengkap }}" {{ old('kenek') == $krani->nama_lengkap ? 'selected' : '' }}>
                                {{ $krani->nama_panggilan ?? $krani->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-blue-600">
                        Ketik nama kenek untuk mencari dengan cepat
                    </p>
                    @error('kenek')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Krani -->
                <div>
                    <label for="krani" class="block text-sm font-medium text-gray-700 mb-1">Krani</label>
                    <select name="krani" id="krani"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('krani') border-red-500 @enderror">
                        <option value="">Pilih Krani</option>
                        @foreach($karyawanKranis as $krani)
                            <option value="{{ $krani->nama_lengkap }}" {{ old('krani') == $krani->nama_lengkap ? 'selected' : '' }}>
                                {{ $krani->nama_panggilan ?? $krani->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-blue-600">
                        Ketik nama krani untuk mencari dengan cepat
                    </p>
                    @error('krani')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Informasi Container -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Informasi Container</h3>
                </div>

                <!-- No Kontainer -->
                <div>
                    <label for="no_kontainer" class="block text-sm font-medium text-gray-700 mb-1">No Kontainer</label>
                    <input type="text" name="no_kontainer" id="no_kontainer" readonly
                           value="{{ old('no_kontainer', isset($selectedContainer) ? $selectedContainer->nomor_kontainer : '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 focus:outline-none @error('no_kontainer') border-red-500 @enderror"
                           placeholder="Nomor kontainer akan terisi otomatis">
                    @error('no_kontainer')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- No Seal -->
                <div>
                    <label for="no_seal" class="block text-sm font-medium text-gray-700 mb-1">No Seal</label>
                    <input type="text" name="no_seal" id="no_seal" readonly
                           value="{{ old('no_seal', isset($selectedContainer) ? $selectedContainer->no_seal : '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 focus:outline-none @error('no_seal') border-red-500 @enderror"
                           placeholder="Nomor seal akan terisi otomatis">
                    @error('no_seal')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @if(isset($selectedContainer) && $selectedContainer->no_seal)
                        <p class="mt-1 text-xs text-green-600">
                            Seal terisi otomatis dari kontainer: {{ $selectedContainer->no_seal }}
                        </p>
                    @endif
                </div>

                <!-- Size Kontainer -->
                <div>
                    <label for="size" class="block text-sm font-medium text-gray-700 mb-1">Size Kontainer</label>
                    @php
                        $defaultSize = '';
                        $displaySize = '';
                        if (isset($selectedContainer)) {
                            $defaultSize = $selectedContainer->size_kontainer 
                                ?: $selectedContainer->tipe_kontainer 
                                ?: '';
                            
                            $displaySize = $defaultSize;
                            if ($defaultSize == '20' || $defaultSize == '20ft') {
                                $displaySize = '20ft';
                            } elseif ($defaultSize == '40' || $defaultSize == '40ft') {
                                $displaySize = '40ft';
                            } elseif (strtolower($defaultSize) == '40hc' || strtolower($defaultSize) == '40 hc') {
                                $displaySize = '40HC';
                            } elseif ($defaultSize == '45' || $defaultSize == '45ft') {
                                $displaySize = '45ft';
                            }
                        }
                        $selectedSize = old('size', $defaultSize);
                    @endphp
                    <input type="text" name="size_display" id="size_display" readonly
                           value="{{ $displaySize ?: 'Belum dipilih' }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 focus:outline-none"
                           placeholder="Size kontainer akan terisi otomatis">
                    <input type="hidden" name="size" value="{{ $selectedSize }}">
                    @error('size')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @if(isset($selectedContainer) && $defaultSize)
                        <p class="mt-1 text-xs text-green-600">
                            Size terisi otomatis dari kontainer: {{ $defaultSize }}
                        </p>
                    @endif
                </div>

                <!-- Informasi Packaging -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Informasi Packaging</h3>
                </div>

                <!-- Karton -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Karton</label>
                    <div class="flex space-x-4">
                        <label class="flex items-center">
                            <input type="radio" name="karton" value="ya" {{ old('karton') == 'ya' ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">Ya</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="karton" value="tidak" {{ old('karton', 'tidak') == 'tidak' ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">Tidak</span>
                        </label>
                    </div>
                    @error('karton')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Plastik -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Plastik</label>
                    <div class="flex space-x-4">
                        <label class="flex items-center">
                            <input type="radio" name="plastik" value="ya" {{ old('plastik') == 'ya' ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">Ya</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="plastik" value="tidak" {{ old('plastik', 'tidak') == 'tidak' ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">Tidak</span>
                        </label>
                    </div>
                    @error('plastik')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Terpal -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Terpal</label>
                    <div class="flex space-x-4">
                        <label class="flex items-center">
                            <input type="radio" name="terpal" value="ya" {{ old('terpal') == 'ya' ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">Ya</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="terpal" value="tidak" {{ old('terpal', 'tidak') == 'tidak' ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">Tidak</span>
                        </label>
                    </div>
                    @error('terpal')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Informasi Keuangan -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Informasi Keuangan</h3>
                </div>

                <!-- RIT -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">RIT</label>
                    <div class="flex space-x-4">
                        <label class="flex items-center">
                            <input type="radio" name="rit" value="menggunakan_rit" {{ old('rit') == 'menggunakan_rit' ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">Menggunakan RIT</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="rit" value="tidak_menggunakan_rit" {{ old('rit') == 'tidak_menggunakan_rit' ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">Tidak Menggunakan RIT</span>
                        </label>
                    </div>
                    @error('rit')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Uang Jalan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Uang Jalan</label>
                    <div class="space-y-2">
                        <div class="flex space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="uang_jalan_type" value="full" {{ old('uang_jalan_type') == 'full' ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                <span class="ml-2 text-sm text-gray-700">Full</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="uang_jalan_type" value="setengah" {{ old('uang_jalan_type') == 'setengah' ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                <span class="ml-2 text-sm text-gray-700">Setengah</span>
                            </label>
                        </div>
                        <input type="number" name="uang_jalan_nominal" id="uang_jalan_nominal"
                               value="{{ old('uang_jalan_nominal') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('uang_jalan_nominal') border-red-500 @enderror"
                               placeholder="Nominal uang jalan" min="0" step="1000">
                    </div>
                    @error('uang_jalan_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('uang_jalan_nominal')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-md font-medium text-gray-700 mb-2">Tagihan</h3>
                    <div class="grid grid-cols-3 gap-4">
                        <!-- AYP -->
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="tagihan_ayp" value="1" {{ old('tagihan_ayp') ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">AYP</span>
                            </label>
                            @error('tagihan_ayp')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- ATB -->
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="tagihan_atb" value="1" {{ old('tagihan_atb') ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">ATB</span>
                            </label>
                            @error('tagihan_atb')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- PB -->
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="tagihan_pb" value="1" {{ old('tagihan_pb') ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">PB</span>
                            </label>
                            @error('tagihan_pb')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                <a href="{{ route('surat-jalan-bongkaran.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-150 flex items-center text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Batal
                </a>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-150 flex items-center text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<style>
    /* Custom styling for select elements */
    .form-select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.5rem center;
        background-repeat: no-repeat;
        background-size: 1.5em 1.5em;
        padding-right: 2.5rem;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tujuan kegiatan utama data for uang jalan calculation
    const tujuanKegiatanData = @json($tujuanKegiatanUtamas->keyBy('ke'));
    
    // Auto generate nomor surat jalan if needed
    const generateNomor = () => {
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const date = String(today.getDate()).padStart(2, '0');
        const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
        
        return `SJB/${year}${month}${date}/${random}`;
    };
    
    // Set default nomor if empty
    const nomorInput = document.getElementById('nomor_surat_jalan');
    if (nomorInput && !nomorInput.value) {
        nomorInput.value = generateNomor();
    }
    
    // Set default tanggal to today
    const tanggalInput = document.getElementById('tanggal_surat_jalan');
    if (tanggalInput && !tanggalInput.value) {
        const today = new Date().toISOString().split('T')[0];
        tanggalInput.value = today;
    }
    
    // Auto-fill plat nomor when supir is selected
    const supirSelect = document.getElementById('supir');
    const noPlatInput = document.getElementById('no_plat');
    
    if (supirSelect && noPlatInput) {
        supirSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const platNumber = selectedOption.getAttribute('data-plat');
            
            if (platNumber && platNumber.trim() !== '' && !noPlatInput.value) {
                noPlatInput.value = platNumber;
            }
        });
    }
    
    // Auto-calculate uang jalan based on tujuan pengambilan
    const tujuanPengambilanSelect = document.getElementById('tujuan_pengambilan');
    const uangJalanNominalInput = document.getElementById('uang_jalan_nominal');
    const sizeInput = document.querySelector('input[name="size"]');
    const uangJalanTypeRadios = document.querySelectorAll('input[name="uang_jalan_type"]');
    
    function calculateUangJalan() {
        const selectedTujuan = tujuanPengambilanSelect.value;
        const containerSize = sizeInput ? sizeInput.value : '';
        const uangJalanType = document.querySelector('input[name="uang_jalan_type"]:checked');
        
        if (selectedTujuan && tujuanKegiatanData[selectedTujuan]) {
            const tujuanData = tujuanKegiatanData[selectedTujuan];
            let uangJalan = 0;
            
            // Determine uang jalan based on container size
            if (containerSize === '20' || containerSize === '20ft') {
                uangJalan = tujuanData.uang_jalan_20ft || 0;
            } else if (containerSize === '40' || containerSize === '40ft' || containerSize === '40hc' || containerSize === '40 hc') {
                uangJalan = tujuanData.uang_jalan_40ft || 0;
            } else {
                // Default to 20ft if size is not clear
                uangJalan = tujuanData.uang_jalan_20ft || 0;
            }
            
            // Apply half calculation if "setengah" is selected
            if (uangJalanType && uangJalanType.value === 'setengah') {
                uangJalan = uangJalan / 2;
            }
            
            if (uangJalan > 0) {
                uangJalanNominalInput.value = Math.round(uangJalan);
            }
        }
    }
    
    if (tujuanPengambilanSelect && uangJalanNominalInput) {
        tujuanPengambilanSelect.addEventListener('change', calculateUangJalan);
        
        // Add event listeners to uang jalan type radio buttons
        uangJalanTypeRadios.forEach(radio => {
            radio.addEventListener('change', calculateUangJalan);
        });
        
        // Also trigger calculation on page load if values are pre-selected
        if (tujuanPengambilanSelect.value) {
            calculateUangJalan();
        }
    }
});
</script>
@endpush