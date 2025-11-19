@extends('layouts.app')

@section('title', 'Tambah Surat Jalan Bongkaran')

@section('content')
<div class="flex-1 p-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tambah Surat Jalan Bongkaran</h1>
            <nav class="flex text-sm text-gray-600 mt-1">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                <span class="mx-2">/</span>
                <a href="{{ route('surat-jalan-bongkaran.index') }}" class="hover:text-blue-600">Surat Jalan Bongkaran</a>
                <span class="mx-2">/</span>
                <span class="text-gray-500">Tambah</span>
            </nav>
        </div>
    </div>

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

    <!-- Main Form -->
    <form action="{{ route('surat-jalan-bongkaran.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- Basic Information Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Informasi Dasar</h2>
            </div>
            <div class="p-6">
                <!-- Selected Kapal & Voyage Info (Read-only) -->
                @if(isset($selectedKapal) && isset($noVoyage))
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-blue-700 mb-1">Kapal Terpilih</label>
                                <p class="text-sm font-semibold text-blue-900">{{ $selectedKapal->nama_kapal }}</p>
                                <input type="hidden" name="kapal_id" value="{{ $selectedKapal->id }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-blue-700 mb-1">No Voyage</label>
                                <p class="text-sm font-semibold text-blue-900">{{ $noVoyage }}</p>
                                <input type="hidden" name="no_voyage" value="{{ $noVoyage }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-blue-700 mb-1">No BL</label>
                                <p class="text-sm font-semibold text-blue-900">{{ request('no_bl', '-') }}</p>
                                @if(request('no_bl'))
                                    <input type="hidden" name="no_bl" value="{{ request('no_bl') }}">
                                @endif
                            </div>
                        </div>
                        <div class="mt-3 text-right">
                            <a href="{{ route('surat-jalan-bongkaran.select-kapal') }}" 
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Ubah Pilihan
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Container Selection Info -->
                @if(isset($selectedContainer) && $selectedContainer->nomor_kontainer)
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <h4 class="font-medium text-green-900">Kontainer Terpilih</h4>
                                <p class="text-sm text-green-700 mt-1">
                                    Data kontainer telah diisi otomatis: <strong>{{ $selectedContainer->nomor_kontainer }}</strong>
                                    @if($selectedContainer->no_seal)
                                        | Seal: <strong>{{ $selectedContainer->no_seal }}</strong>
                                    @endif
                                    @if(isset($selectedContainer->size_kontainer) && $selectedContainer->size_kontainer)
                                        | Size: <strong>{{ strtoupper($selectedContainer->size_kontainer) }}</strong>
                                    @elseif(isset($selectedContainer->tipe_kontainer) && $selectedContainer->tipe_kontainer)
                                        | Size: <strong>{{ strtoupper($selectedContainer->tipe_kontainer) }}</strong>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
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
                        <input type="text" name="term" id="term"
                               value="{{ old('term') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('term') border-red-300 @enderror"
                               placeholder="Masukkan term">
                        @error('term')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Aktifitas -->
                    <div class="md:col-span-2 lg:col-span-3">
                        <label for="aktifitas" class="block text-sm font-medium text-gray-700 mb-1">Aktifitas</label>
                        <textarea name="aktifitas" id="aktifitas" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('aktifitas') border-red-300 @enderror"
                                  placeholder="Masukkan aktifitas">{{ old('aktifitas') }}</textarea>
                        @error('aktifitas')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>


                </div>
            </div>
        </div>

        <!-- Informasi Pengiriman Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Informasi Pengiriman</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Pengirim -->
                    <div>
                        <label for="pengirim" class="block text-sm font-medium text-gray-700 mb-1">Pengirim</label>
                        <input type="text" name="pengirim" id="pengirim"
                               value="{{ old('pengirim') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('pengirim') border-red-300 @enderror"
                               placeholder="Masukkan nama pengirim">
                        @error('pengirim')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tujuan Alamat -->
                    <div>
                        <label for="tujuan_alamat" class="block text-sm font-medium text-gray-700 mb-1">Tujuan Alamat</label>
                        <input type="text" name="tujuan_alamat" id="tujuan_alamat"
                               value="{{ old('tujuan_alamat') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tujuan_alamat') border-red-300 @enderror"
                               placeholder="Masukkan tujuan alamat">
                        @error('tujuan_alamat')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tujuan Pengambilan -->
                    <div>
                        <label for="tujuan_pengambilan" class="block text-sm font-medium text-gray-700 mb-1">Tujuan Pengambilan</label>
                        <input type="text" name="tujuan_pengambilan" id="tujuan_pengambilan"
                               value="{{ old('tujuan_pengambilan') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tujuan_pengambilan') border-red-300 @enderror"
                               placeholder="Masukkan tujuan pengambilan">
                        @error('tujuan_pengambilan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tujuan Pengiriman -->
                    <div>
                        <label for="tujuan_pengiriman" class="block text-sm font-medium text-gray-700 mb-1">Tujuan Pengiriman</label>
                        <input type="text" name="tujuan_pengiriman" id="tujuan_pengiriman"
                               value="{{ old('tujuan_pengiriman') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tujuan_pengiriman') border-red-300 @enderror"
                               placeholder="Masukkan tujuan pengiriman">
                        @error('tujuan_pengiriman')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jenis Pengiriman -->
                    <div>
                        <label for="jenis_pengiriman" class="block text-sm font-medium text-gray-700 mb-1">Jenis Pengiriman</label>
                        <select name="jenis_pengiriman" id="jenis_pengiriman"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('jenis_pengiriman') border-red-300 @enderror">
                            <option value="">Pilih jenis pengiriman</option>
                            <option value="ekspor" {{ old('jenis_pengiriman') == 'ekspor' ? 'selected' : '' }}>Ekspor</option>
                            <option value="impor" {{ old('jenis_pengiriman') == 'impor' ? 'selected' : '' }}>Impor</option>
                            <option value="domestik" {{ old('jenis_pengiriman') == 'domestik' ? 'selected' : '' }}>Domestik</option>
                        </select>
                        @error('jenis_pengiriman')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Ambil Barang -->
                    <div>
                        <label for="tanggal_ambil_barang" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Ambil Barang</label>
                        <input type="date" name="tanggal_ambil_barang" id="tanggal_ambil_barang"
                               value="{{ old('tanggal_ambil_barang') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tanggal_ambil_barang') border-red-300 @enderror">
                        @error('tanggal_ambil_barang')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Informasi Personal Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Informasi Personal</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Supir -->
                    <div>
                        <label for="supir" class="block text-sm font-medium text-gray-700 mb-1">Supir</label>
                        <select name="supir" id="supir"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('supir') border-red-300 @enderror">
                            <option value="">Pilih Supir</option>
                            @foreach($karyawanSupirs as $supir)
                                <option value="{{ $supir->nama_lengkap }}" 
                                        data-plat="{{ $supir->plat }}"
                                        {{ old('supir') == $supir->nama_lengkap ? 'selected' : '' }}>
                                    {{ $supir->nama_lengkap }}{{ $supir->nama_panggilan ? ' (' . $supir->nama_panggilan . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('supir')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- No Plat -->
                    <div>
                        <label for="no_plat" class="block text-sm font-medium text-gray-700 mb-1">No Plat</label>
                        <input type="text" name="no_plat" id="no_plat"
                               value="{{ old('no_plat') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('no_plat') border-red-300 @enderror"
                               placeholder="Masukkan nomor plat">
                        @error('no_plat')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kenek -->
                    <div>
                        <label for="kenek" class="block text-sm font-medium text-gray-700 mb-1">Kenek</label>
                        <input type="text" name="kenek" id="kenek"
                               value="{{ old('kenek') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('kenek') border-red-300 @enderror"
                               placeholder="Masukkan nama kenek">
                        @error('kenek')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Krani -->
                    <div>
                        <label for="krani" class="block text-sm font-medium text-gray-700 mb-1">Krani</label>
                        <input type="text" name="krani" id="krani"
                               value="{{ old('krani') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('krani') border-red-300 @enderror"
                               placeholder="Masukkan nama krani">
                        @error('krani')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Informasi Container Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Informasi Container</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- No Kontainer -->
                    <div>
                        <label for="no_kontainer" class="block text-sm font-medium text-gray-700 mb-1">No Kontainer</label>
                        <input type="text" name="no_kontainer" id="no_kontainer" readonly
                               value="{{ old('no_kontainer', isset($selectedContainer) ? $selectedContainer->nomor_kontainer : '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-50 text-gray-700 @error('no_kontainer') border-red-300 @enderror"
                               placeholder="Nomor kontainer akan terisi otomatis">
                        @error('no_kontainer')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- No Seal -->
                    <div>
                        <label for="no_seal" class="block text-sm font-medium text-gray-700 mb-1">No Seal</label>
                        <input type="text" name="no_seal" id="no_seal"
                               value="{{ old('no_seal', isset($selectedContainer) ? $selectedContainer->no_seal : '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('no_seal') border-red-300 @enderror"
                               placeholder="Masukkan nomor seal">
                        @error('no_seal')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Size Kontainer -->
                    <div>
                        <label for="size" class="block text-sm font-medium text-gray-700 mb-1">Size Kontainer</label>
                        <select name="size" id="size"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('size') border-red-300 @enderror">
                            <option value="">Pilih size kontainer</option>
                            <option value="20ft" {{ old('size', isset($selectedContainer) ? ($selectedContainer->size_kontainer ?: $selectedContainer->tipe_kontainer) : '') == '20ft' ? 'selected' : '' }}>20ft</option>
                            <option value="40ft" {{ old('size', isset($selectedContainer) ? ($selectedContainer->size_kontainer ?: $selectedContainer->tipe_kontainer) : '') == '40ft' ? 'selected' : '' }}>40ft</option>
                            <option value="40hc" {{ old('size', isset($selectedContainer) ? ($selectedContainer->size_kontainer ?: $selectedContainer->tipe_kontainer) : '') == '40hc' ? 'selected' : '' }}>40HC</option>
                            <option value="45ft" {{ old('size', isset($selectedContainer) ? ($selectedContainer->size_kontainer ?: $selectedContainer->tipe_kontainer) : '') == '45ft' ? 'selected' : '' }}>45ft</option>
                        </select>
                        @error('size')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Informasi Packaging Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Informasi Packaging</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Karton -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Karton</label>
                        <div class="space-y-2">
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
                        <label class="block text-sm font-medium text-gray-700 mb-3">Plastik</label>
                        <div class="space-y-2">
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
                        <label class="block text-sm font-medium text-gray-700 mb-3">Terpal</label>
                        <div class="space-y-2">
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
                </div>
            </div>
        </div>

        <!-- Informasi Keuangan Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Informasi Keuangan</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- RIT -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">RIT</label>
                        <div class="space-y-2">
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
                        <label class="block text-sm font-medium text-gray-700 mb-3">Uang Jalan</label>
                        <div class="space-y-2">
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
                        @error('uang_jalan_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <h3 class="text-md font-medium text-gray-700 mb-4">Tagihan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- AYP -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">AYP</label>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="checkbox" name="tagihan_ayp" value="1" {{ old('tagihan_ayp') ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-700">Ya</span>
                                </label>
                            </div>
                            @error('tagihan_ayp')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- ATB -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">ATB</label>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="checkbox" name="tagihan_atb" value="1" {{ old('tagihan_atb') ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-700">Ya</span>
                                </label>
                            </div>
                            @error('tagihan_atb')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- PB -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">PB</label>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="checkbox" name="tagihan_pb" value="1" {{ old('tagihan_pb') ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-700">Ya</span>
                                </label>
                            </div>
                            @error('tagihan_pb')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!-- Action Buttons -->
        <div class="flex justify-end space-x-4 pt-6">
            <a href="{{ route('surat-jalan-bongkaran.index') }}" 
               class="inline-flex items-center px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Batal
            </a>
            <button type="submit" 
                    class="inline-flex items-center px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
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
    if (!nomorInput.value) {
        nomorInput.value = generateNomor();
    }
    
    // Set default tanggal to today
    const tanggalInput = document.getElementById('tanggal_surat_jalan');
    if (!tanggalInput.value) {
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

});
</script>
@endpush