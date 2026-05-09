@extends('layouts.app')

@section('title', 'Tambah Bill of Lading')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumb -->
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('bl.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">
                        <i class="fas fa-file-contract mr-2"></i> Bill of Lading
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
                        <a href="{{ route('bl.select') }}" class="text-sm font-medium text-gray-700 hover:text-indigo-600">Pilih Kapal</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
                        <span class="text-sm font-medium text-gray-500">Tambah BL Baru</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-10 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-extrabold tracking-tight">Buat BL Baru</h1>
                        <p class="mt-2 text-indigo-100 opacity-90">Masukkan detail Bill of Lading untuk kapal dan voyage yang dipilih.</p>
                    </div>
                    <div class="hidden md:block">
                        <i class="fas fa-plus-circle text-5xl text-white/20"></i>
                    </div>
                </div>
                
                <!-- Context Info -->
                <div class="mt-8 flex flex-wrap gap-4">
                    <div class="bg-white/10 backdrop-blur-md rounded-lg px-4 py-3 flex items-center border border-white/20">
                        <i class="fas fa-ship mr-3 text-indigo-200"></i>
                        <div>
                            <p class="text-xs text-indigo-200 uppercase font-semibold">Kapal</p>
                            <p class="font-bold">{{ $namaKapal }}</p>
                        </div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md rounded-lg px-4 py-3 flex items-center border border-white/20">
                        <i class="fas fa-route mr-3 text-indigo-200"></i>
                        <div>
                            <p class="text-xs text-indigo-200 uppercase font-semibold">No. Voyage</p>
                            <p class="font-bold">{{ $noVoyage }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('bl.store') }}" method="POST" class="p-8">
                @csrf
                <input type="hidden" name="nama_kapal" value="{{ $namaKapal }}">
                <input type="hidden" name="no_voyage" value="{{ $noVoyage }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    
                    <!-- Section 1: Main Info -->
                    <div class="space-y-6">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center">
                            <span class="w-8 h-8 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mr-3 text-sm">1</span>
                            Informasi Utama
                        </h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="nomor_kontainer" class="block text-sm font-semibold text-gray-700 mb-1">Nomor Kontainer <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                        <i class="fas fa-box"></i>
                                    </div>
                                    <input type="text" name="nomor_kontainer" id="nomor_kontainer" required value="{{ old('nomor_kontainer') }}"
                                        class="block w-full pl-10 pr-3 py-3 border-gray-300 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 font-medium transition duration-150"
                                        placeholder="Contoh: CONT1234567">
                                </div>
                                @error('nomor_kontainer') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="nomor_bl" class="block text-sm font-semibold text-gray-700 mb-1">Nomor BL</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                        <i class="fas fa-hashtag"></i>
                                    </div>
                                    <input type="text" name="nomor_bl" id="nomor_bl" value="{{ old('nomor_bl') }}"
                                        class="block w-full pl-10 pr-3 py-3 border-gray-300 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 font-medium transition duration-150"
                                        placeholder="Masukkan nomor BL">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="no_seal" class="block text-sm font-semibold text-gray-700 mb-1">No. Seal</label>
                                    <input type="text" name="no_seal" id="no_seal" value="{{ old('no_seal') }}"
                                        class="block w-full px-3 py-3 border-gray-300 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 font-medium transition duration-150">
                                </div>
                                <div>
                                    <label for="tipe_kontainer" class="block text-sm font-semibold text-gray-700 mb-1">Tipe</label>
                                    <select name="tipe_kontainer" id="tipe_kontainer"
                                        class="block w-full px-3 py-3 border-gray-300 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 font-medium transition duration-150">
                                        <option value="">-- Pilih Tipe --</option>
                                        <option value="Dry">Dry</option>
                                        <option value="Reefer">Reefer</option>
                                        <option value="Flat Rack">Flat Rack</option>
                                        <option value="Open Top">Open Top</option>
                                        <option value="Tank">Tank</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label for="size_kontainer" class="block text-sm font-semibold text-gray-700 mb-1">Ukuran Kontainer</label>
                                <select name="size_kontainer" id="size_kontainer"
                                    class="block w-full px-3 py-3 border-gray-300 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 font-medium transition duration-150">
                                    <option value="">-- Pilih Ukuran --</option>
                                    <option value="20ft">20 Feet</option>
                                    <option value="40ft">40 Feet</option>
                                    <option value="40hc">40 HC</option>
                                    <option value="45ft">45 Feet</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Logistics & Details -->
                    <div class="space-y-6">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center">
                            <span class="w-8 h-8 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center mr-3 text-sm">2</span>
                            Detail Logistik
                        </h3>

                        <div class="space-y-4">
                            <div>
                                <label for="nama_barang" class="block text-sm font-semibold text-gray-700 mb-1">Nama Barang</label>
                                <input type="text" name="nama_barang" id="nama_barang" value="{{ old('nama_barang') }}"
                                    class="block w-full px-3 py-3 border-gray-300 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 font-medium transition duration-150"
                                    placeholder="Jenis barang yang dikirim">
                            </div>

                            <div>
                                <label for="pengirim" class="block text-sm font-semibold text-gray-700 mb-1">Pengirim</label>
                                <input type="text" name="pengirim" id="pengirim" value="{{ old('pengirim') }}"
                                    class="block w-full px-3 py-3 border-gray-300 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 font-medium transition duration-150"
                                    placeholder="Nama perusahaan/individu pengirim">
                            </div>

                            <div>
                                <label for="penerima" class="block text-sm font-semibold text-gray-700 mb-1">Penerima</label>
                                <input type="text" name="penerima" id="penerima" value="{{ old('penerima') }}"
                                    class="block w-full px-3 py-3 border-gray-300 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 font-medium transition duration-150"
                                    placeholder="Nama perusahaan/individu penerima">
                            </div>

                            <div class="grid grid-cols-3 gap-3">
                                <div class="col-span-1">
                                    <label for="tonnage" class="block text-sm font-semibold text-gray-700 mb-1">Tonnage</label>
                                    <input type="number" step="0.001" name="tonnage" id="tonnage" value="{{ old('tonnage') }}"
                                        class="block w-full px-3 py-3 border-gray-300 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 font-medium transition duration-150">
                                </div>
                                <div class="col-span-1">
                                    <label for="volume" class="block text-sm font-semibold text-gray-700 mb-1">Volume</label>
                                    <input type="number" step="0.001" name="volume" id="volume" value="{{ old('volume') }}"
                                        class="block w-full px-3 py-3 border-gray-300 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 font-medium transition duration-150">
                                </div>
                                <div class="col-span-1">
                                    <label for="satuan" class="block text-sm font-semibold text-gray-700 mb-1">Satuan</label>
                                    <input type="text" name="satuan" id="satuan" value="{{ old('satuan', 'M3/TON') }}"
                                        class="block w-full px-3 py-3 border-gray-300 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 font-medium transition duration-150">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="kuantitas" class="block text-sm font-semibold text-gray-700 mb-1">Kuantitas</label>
                                    <input type="number" name="kuantitas" id="kuantitas" value="{{ old('kuantitas') }}"
                                        class="block w-full px-3 py-3 border-gray-300 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 font-medium transition duration-150">
                                </div>
                                <div>
                                    <label for="term" class="block text-sm font-semibold text-gray-700 mb-1">Term</label>
                                    <input type="text" name="term" id="term" value="{{ old('term') }}"
                                        class="block w-full px-3 py-3 border-gray-300 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 font-medium transition duration-150"
                                        placeholder="Contoh: Prepaid">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-12 pt-8 border-t border-gray-100 flex items-center justify-between">
                    <a href="{{ route('bl.select') }}" class="inline-flex items-center text-sm font-bold text-gray-500 hover:text-gray-700 transition duration-150">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Pemilihan
                    </a>
                    <div class="flex space-x-4">
                        <button type="reset" class="px-6 py-3 text-sm font-bold text-gray-600 hover:bg-gray-50 rounded-xl transition duration-150">
                            Reset Form
                        </button>
                        <button type="submit" class="px-10 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-indigo-200 transition duration-200 transform hover:-translate-y-0.5">
                            <i class="fas fa-save mr-2"></i> Simpan Bill of Lading
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Help Card -->
        <div class="mt-8 bg-indigo-50 rounded-xl p-6 border border-indigo-100">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-indigo-500 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h4 class="text-sm font-bold text-indigo-900">Tips Pengisian</h4>
                    <p class="mt-1 text-sm text-indigo-700">
                        Pastikan Nomor Kontainer diisi dengan benar. Jika nomor kontainer sudah ada di database (dari modul Prospek), sistem akan otomatis melakukan sinkronisasi status muat.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
