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
                    <p class="mt-2 text-gray-600">Form untuk membuat realisasi uang muka pembayaran supir</p>
                </div>

                <div class="flex space-x-3">
                    <a href="{{ route('realisasi-uang-muka.index') }}"
                       class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </div>

            <!-- Form Realisasi Uang Muka -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <form id="form-realisasi-uang-muka" action="{{ route('realisasi-uang-muka.store') }}" method="POST" class="space-y-6">
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
                                           placeholder="RM-KBJ-10-25-000001"
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
                                <p class="mt-1 text-sm text-gray-500">Format: Kode_COA-Bulan(2)-Tahun(2)-Urutan(6). Pilih kas/bank untuk generate otomatis.</p>
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

                            <!-- Kas/Bank -->
                            <div>
                                <label for="kas_bank" class="block text-sm font-medium text-gray-700 mb-2">
                                    Kas/Bank <span class="text-red-500">*</span>
                                </label>
                                <select name="kas_bank"
                                        id="kas_bank"
                                        required
                                        onchange="generateNomor()"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('kas_bank') border-red-300 @enderror">
                                    <option value="">Pilih Kas/Bank</option>
                                    @foreach($kasBankList as $kasBank)
                                        <option value="{{ $kasBank->id }}"
                                                {{ old('kas_bank') == $kasBank->id ? 'selected' : '' }}
                                                data-kode="{{ $kasBank->kode_nomor }}">
                                            {{ $kasBank->nomor_akun }} - {{ $kasBank->nama_akun }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kas_bank')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Jenis Transaksi -->
                            <div>
                                <label for="jenis_transaksi" class="block text-sm font-medium text-gray-700 mb-2">
                                    Jenis Transaksi <span class="text-red-500">*</span>
                                </label>
                                <select name="jenis_transaksi"
                                        id="jenis_transaksi"
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('jenis_transaksi') border-red-300 @enderror">
                                    <option value="">Pilih Jenis Transaksi</option>
                                    <option value="debit" {{ old('jenis_transaksi') == 'debit' ? 'selected' : '' }}>Debit (Keluar)</option>
                                    <option value="kredit" {{ old('jenis_transaksi') == 'kredit' ? 'selected' : '' }}>Kredit (Masuk)</option>
                                </select>
                                @error('jenis_transaksi')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Realisasi Pembayaran per Supir (Table Format) -->
                        <div>
                            <label id="section-title" class="block text-sm font-medium text-gray-700 mb-2">
                                Realisasi Uang Muka per Supir <span class="text-red-500">*</span>
                            </label>
                            <p id="section-desc" class="text-sm text-gray-600 mb-4">
                                <i class="fas fa-info-circle mr-1"></i>
                                Pilih supir dan masukkan realisasi uang muka. Sistem akan menggunakan data dari Uang Muka yang dipilih jika tersedia.
                            </p>

                            <!-- Control Buttons -->
                            <div class="mb-4 flex flex-wrap gap-3">
                                <!-- Select All Button -->
                                <div class="p-3 bg-blue-50 border border-blue-200 rounded-md flex-1 min-w-64">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 mr-2">
                                        <span id="select-all-label" class="text-sm font-medium text-gray-700">
                                            <i class="fas fa-users mr-1"></i>Pilih Semua Supir
                                        </span>
                                    </label>
                                </div>

                                <!-- Add Driver Button -->
                                <div class="flex gap-2">
                                    <button type="button" onclick="openSupirModal()"
                                            class="inline-flex items-center px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 transition duration-200">
                                        <i class="fas fa-user-plus mr-2"></i>
                                        <span id="add-button-label">Tambah Supir</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Table for Supir -->
                            <div id="supir-table" class="overflow-x-auto border border-gray-200 rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200 bg-white">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                <input type="checkbox" id="supir-header-checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" disabled>
                                            </th>
                                            <th id="table-header" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supir</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uang Muka</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Realisasi (Rp)</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Selisih</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($supirList as $supir)
                                        <tr class="supir-row hover:bg-gray-50" data-supir-id="{{ $supir->id }}" style="display: none;">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <input type="checkbox" name="supir[]" value="{{ $supir->id }}"
                                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                                                       onchange="toggleSupirRow('{{ $supir->id }}')">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                            <span class="text-sm font-medium text-gray-700">{{ substr($supir->nama_lengkap, 0, 2) }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">{{ $supir->nama_lengkap }}</div>
                                                        <div class="text-sm text-gray-500">
                                                            @if($supir->nama_panggilan)
                                                                {{ $supir->nama_panggilan }} •
                                                            @endif
                                                            NIK: {{ $supir->nik }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div id="dp_{{ $supir->id }}">
                                                    <span class="text-gray-400 italic">-</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-sm text-gray-500">Rp</span>
                                                    <input type="text"
                                                           id="realisasi_display_{{ $supir->id }}"
                                                           placeholder="0"
                                                           disabled
                                                           oninput="hitungSelisih('{{ $supir->id }}')"
                                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                                    <input type="hidden" name="jumlah[{{ $supir->id }}]" id="jumlah_{{ $supir->id }}" value="0">
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div id="selisih_{{ $supir->id }}">
                                                    <span class="text-gray-400 italic text-sm">-</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <input type="text"
                                                       name="keterangan[{{ $supir->id }}]"
                                                       id="keterangan_{{ $supir->id }}"
                                                       placeholder="Keterangan (opsional)"
                                                       readonly
                                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm bg-gray-50">
                                            </td>
                                        </tr>
                                        @endforeach

                                        @foreach($karyawanList as $karyawan)
                                        <tr class="karyawan-row hover:bg-gray-50" data-karyawan-id="{{ $karyawan->id }}" style="display: none;">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <input type="checkbox" name="penerima[]" value="{{ $karyawan->id }}"
                                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                                                       onchange="toggleKaryawanRow('{{ $karyawan->id }}')">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <div class="h-10 w-10 rounded-full bg-green-300 flex items-center justify-center">
                                                            <span class="text-sm font-medium text-gray-700">{{ substr($karyawan->nama_lengkap, 0, 2) }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">{{ $karyawan->nama_lengkap }}</div>
                                                        <div class="text-sm text-gray-500">
                                                            @if($karyawan->nama_panggilan)
                                                                {{ $karyawan->nama_panggilan }} •
                                                            @endif
                                                            NIK: {{ $karyawan->nik }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div id="dp_karyawan_{{ $karyawan->id }}">
                                                    <span class="text-gray-400 italic">-</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-sm text-gray-500">Rp</span>
                                                    <input type="text"
                                                           id="realisasi_display_karyawan_{{ $karyawan->id }}"
                                                           placeholder="0"
                                                           disabled
                                                           oninput="hitungSelisihKaryawan('{{ $karyawan->id }}')"
                                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                                    <input type="hidden" name="jumlah_karyawan[{{ $karyawan->id }}]" id="jumlah_karyawan_{{ $karyawan->id }}" value="0">
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div id="selisih_karyawan_{{ $karyawan->id }}">
                                                    <span class="text-gray-400 italic text-sm">-</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <input type="text"
                                                       name="keterangan_karyawan[{{ $karyawan->id }}]"
                                                       id="keterangan_karyawan_{{ $karyawan->id }}"
                                                       placeholder="Keterangan (opsional)"
                                                       readonly
                                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm bg-gray-50">
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Table for Mobil (KIR & STNK) -->
                            <div id="mobil-table" class="overflow-x-auto border border-gray-200 rounded-lg" style="display: none;">
                                <table class="min-w-full divide-y divide-gray-200 bg-white">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                <input type="checkbox" id="mobil-header-checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" disabled>
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mobil/Kendaraan</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uang Muka</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Realisasi (Rp)</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Selisih</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($mobilList as $mobil)
                                        <tr class="mobil-row hover:bg-gray-50" data-mobil-id="{{ $mobil->id }}" style="display: none;">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <input type="checkbox" name="mobil[]" value="{{ $mobil->id }}"
                                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                                                       onchange="toggleMobilRow('{{ $mobil->id }}')">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <div class="h-10 w-10 rounded-full bg-blue-300 flex items-center justify-center">
                                                            <i class="fas fa-truck text-white text-sm"></i>
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">{{ $mobil->nomor_polisi }}</div>
                                                        <div class="text-sm text-gray-500">
                                                            {{ $mobil->aktiva }}
                                                            @if($mobil->ukuran)
                                                                • {{ $mobil->ukuran }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div id="dp_mobil_{{ $mobil->id }}">
                                                    <span class="text-gray-400 italic">-</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-sm text-gray-500">Rp</span>
                                                    <input type="text"
                                                           id="realisasi_display_mobil_{{ $mobil->id }}"
                                                           placeholder="0"
                                                           disabled
                                                           oninput="hitungSelisihMobil('{{ $mobil->id }}')"
                                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                                    <input type="hidden" name="jumlah_mobil[{{ $mobil->id }}]" id="jumlah_mobil_{{ $mobil->id }}" value="0">
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div id="selisih_mobil_{{ $mobil->id }}">
                                                    <span class="text-gray-400 italic text-sm">-</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <input type="text"
                                                       name="keterangan_mobil[{{ $mobil->id }}]"
                                                       id="keterangan_mobil_{{ $mobil->id }}"
                                                       placeholder="Keterangan (opsional)"
                                                       readonly
                                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm bg-gray-50">
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
                                    <i class="fas fa-calculator mr-2 text-blue-500"></i>
                                    Ringkasan Realisasi
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                                    <div class="bg-white p-3 rounded-lg border border-blue-100">
                                        <div class="text-gray-600">Jumlah Supir</div>
                                        <div class="text-lg font-bold text-blue-600" id="total-supir">0</div>
                                    </div>
                                    <div class="bg-white p-3 rounded-lg border border-green-100">
                                        <div class="text-gray-600">Total Realisasi</div>
                                        <div class="text-lg font-bold text-green-600" id="total-realisasi">Rp 0</div>
                                    </div>
                                    <div class="bg-white p-3 rounded-lg border border-yellow-100">
                                        <div class="text-gray-600">Total Uang Muka</div>
                                        <div class="text-lg font-bold text-yellow-600" id="total-dp">Rp 0</div>
                                    </div>
                                    <div class="bg-white p-3 rounded-lg border border-purple-100">
                                        <div class="text-gray-600">Selisih</div>
                                        <div class="text-lg font-bold text-purple-600" id="total-selisih">Rp 0</div>
                                    </div>
                                </div>

                                <!-- Info Pengembalian Sisa Uang Muka -->
                                <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                    <div class="flex items-start">
                                        <i class="fas fa-info-circle text-blue-400 mt-0.5 mr-2"></i>
                                        <div class="text-xs text-blue-700">
                                            <strong>Informasi Pengembalian:</strong> Jika realisasi lebih kecil dari uang muka,
                                            sisa uang muka akan <strong>otomatis dikembalikan</strong> ke akun kas/bank yang dipilih.
                                            Jika realisasi lebih besar, akan ada tambahan pembayaran.
                                        </div>
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
                                Pilih Uang Muka yang Akan Direalisasi <span class="text-gray-500">(Opsional)</span>
                            </label>
                            <select name="pembayaran_uang_muka_id"
                                    id="pembayaran_uang_muka_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('pembayaran_uang_muka_id') border-red-300 @enderror">
                                <option value="">-- Tidak Menggunakan Uang Muka --</option>
                                @foreach($uangMukaBelumRealisasiList as $uangMuka)
                                    <option value="{{ $uangMuka->id }}"
                                            {{ old('pembayaran_uang_muka_id') == $uangMuka->id ? 'selected' : '' }}>
                                        {{ $uangMuka->nomor_pembayaran }} -
                                        {{ $uangMuka->tanggal_pembayaran ? $uangMuka->tanggal_pembayaran->format('d/m/Y') : 'N/A' }} -
                                        {{ implode(', ', $uangMuka->supir_names) }} -
                                        Rp {{ number_format($uangMuka->total_pembayaran, 0, ',', '.') }}
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
                                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <p class="text-sm font-medium text-green-800">Uang Muka Terpilih:</p>
                                        <div class="text-sm text-green-700 mt-1" id="selected-uang-muka-info">
                                            <!-- Dynamic content will be inserted here -->
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($uangMukaBelumRealisasiList->count() > 0)
                                <p class="mt-1 text-sm text-gray-500">
                                    <i class="fas fa-lightbulb mr-1"></i>
                                    Pilih uang muka untuk otomatis memuat data supir dan jumlah realisasi
                                </p>
                            @else
                                <p class="mt-1 text-sm text-yellow-600">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    Tidak ada uang muka yang tersedia untuk direalisasi
                                </p>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                            <a href="{{ route('realisasi-uang-muka.index') }}"
                               class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                                <i class="fas fa-times mr-1"></i> Batal
                            </a>
                            <button type="submit"
                                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                                <i class="fas fa-save mr-1"></i> Simpan Realisasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
console.log('=== SCRIPT LOADED SUCCESSFULLY ===');
console.log('This confirms JavaScript is being executed');

// Uang Muka Data untuk JavaScript
const uangMukaData = {
    @foreach($uangMukaBelumRealisasiList as $uangMuka)
        '{{ $uangMuka->id }}': {
            nomor: '{{ $uangMuka->nomor_pembayaran }}',
            total: {{ $uangMuka->total_pembayaran }},
            supir_count: {{ is_array($uangMuka->supir_ids) ? count($uangMuka->supir_ids) : 0 }},
            tanggal: '{{ $uangMuka->tanggal_pembayaran ? $uangMuka->tanggal_pembayaran->format('d/m/Y') : 'N/A' }}',
            supir_names: @json($uangMuka->supir_names ?? []),
            supir_ids: @json($uangMuka->supir_ids ?? []),
            jumlah_per_supir: @json($uangMuka->jumlah_per_supir ?? []),
            penerima_id: {{ $uangMuka->penerima_id ?? 'null' }},
            penerima_nama: '{{ $uangMuka->penerima ? $uangMuka->penerima->nama_lengkap : '' }}',
            mobil_id: {{ $uangMuka->mobil_id ?? 'null' }},
            mobil_plat: '{{ $uangMuka->mobil ? $uangMuka->mobil->nomor_polisi : '' }}'
        },
    @endforeach
};

// Master Mobil Data
const masterMobilData = {
    @foreach($mobilList as $mobil)
        '{{ $mobil->id }}': {
            plat: '{{ $mobil->nomor_polisi }}',
            aktiva: '{{ $mobil->aktiva ?? '' }}',
            ukuran: '{{ $mobil->ukuran ?? '' }}'
        },
    @endforeach
};

// Current selected Uang Muka data
let currentUangMukaData = {};

// Current table type (supir or mobil)
let currentTableType = 'supir';

// Auto generate nomor pembayaran
async function generateNomor(forceGenerate = false) {
    // Get references first
    const nomorInput = document.getElementById('nomor_pembayaran');
    const originalValue = nomorInput.value;

    try {
        const kasBankId = document.getElementById('kas_bank').value;
        if (!kasBankId) {
            alert('Pilih akun Kas/Bank terlebih dahulu untuk generate nomor pembayaran');
            return;
        }

        // Show loading indicator
        nomorInput.value = forceGenerate ? 'Force Generating...' : 'Generating...';
        nomorInput.disabled = true;

        // Determine which endpoint to use
        const endpoint = forceGenerate ?
            '{{ route("realisasi-uang-muka.force-generate-nomor") }}' :
            '{{ route("realisasi-uang-muka.generate-nomor") }}';

        let maxRetries = forceGenerate ? 1 : 3; // Force generate only tries once
        let attempt = 0;

        while (attempt < maxRetries) {
            attempt++;

            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        kas_bank_id: kasBankId
                    })
                });

                const data = await response.json();

                if (data.success && data.nomor_pembayaran) {
                    nomorInput.value = data.nomor_pembayaran;
                    nomorInput.disabled = false;
                    console.log('Generated nomor pembayaran:', data.nomor_pembayaran, forceGenerate ? '(forced)' : '(preview)');

                    // Show success message for force generate
                    if (forceGenerate) {
                        showNomorStatus('success', 'Nomor baru berhasil di-generate dan reserved!');
                    }

                    return; // Success, exit the function
                } else {
                    console.warn(`Attempt ${attempt} failed:`, data.message);
                    if (attempt === maxRetries) {
                        throw new Error(data.message || 'Failed to generate unique nomor');
                    }
                    // Wait 500ms before retry
                    await new Promise(resolve => setTimeout(resolve, 500));
                }
            } catch (fetchError) {
                console.warn(`Attempt ${attempt} fetch error:`, fetchError);
                if (attempt === maxRetries) {
                    throw fetchError;
                }
                // Wait 500ms before retry
                await new Promise(resolve => setTimeout(resolve, 500));
            }
        }
    } catch (error) {
        console.error('Error generating nomor:', error);

        // Restore original value and enable input
        nomorInput.value = originalValue;
        nomorInput.disabled = false;

        if (!forceGenerate) {
            // Show advanced options for normal generate failure
            showNomorGenerateDialog(error.message);
        } else {
            // For force generate failure, just show error
            showNomorStatus('error', 'Gagal generate nomor: ' + error.message);
        }
    }
}

// Show nomor generate dialog with options
function showNomorGenerateDialog(errorMessage) {
    const dialog = document.createElement('div');
    dialog.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
    dialog.innerHTML = `
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                </div>
                <div class="mt-3 text-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Gagal Generate Nomor</h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">${errorMessage}</p>
                    </div>
                </div>
            </div>
            <div class="items-center px-4 py-3">
                <div class="space-y-2">
                    <button id="force-generate-btn" class="w-full px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                        <i class="fas fa-bolt mr-2"></i>Force Generate Nomor Baru
                    </button>
                    <button id="manual-input-btn" class="w-full px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        <i class="fas fa-edit mr-2"></i>Input Manual
                    </button>
                    <button id="close-dialog-btn" class="w-full px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
                        <i class="fas fa-times mr-2"></i>Tutup
                    </button>
                </div>
            </div>
        </div>
    `;

    document.body.appendChild(dialog);

    // Add event listeners
    document.getElementById('force-generate-btn').onclick = function() {
        document.body.removeChild(dialog);
        generateNomor(true); // Force generate
    };

    document.getElementById('manual-input-btn').onclick = function() {
        document.body.removeChild(dialog);
        document.getElementById('nomor_pembayaran').focus();
    };

    document.getElementById('close-dialog-btn').onclick = function() {
        document.body.removeChild(dialog);
    };

    // Close on click outside
    dialog.onclick = function(e) {
        if (e.target === dialog) {
            document.body.removeChild(dialog);
        }
    };
}

// Show status message for nomor operations
function showNomorStatus(type, message) {
    const statusDiv = document.createElement('div');
    const bgColor = type === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700';
    const icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';

    statusDiv.className = `border px-4 py-3 rounded mb-4 ${bgColor}`;
    statusDiv.innerHTML = `
        <div class="flex">
            <div class="py-1">
                <i class="${icon}"></i>
            </div>
            <div class="ml-2">
                <p class="text-sm">${message}</p>
            </div>
        </div>
    `;

    // Insert after nomor pembayaran field
    const nomorField = document.getElementById('nomor_pembayaran').parentElement;
    nomorField.parentElement.insertBefore(statusDiv, nomorField.nextSibling);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (statusDiv.parentElement) {
            statusDiv.parentElement.removeChild(statusDiv);
        }
    }, 5000);
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
        // DON'T add required to display field - it has no name attribute
        keteranganInput.readOnly = false;
        keteranganInput.classList.remove('bg-gray-50');
        keteranganInput.classList.add('bg-white');

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
        realisasiInput.value = '';
        keteranganInput.readOnly = true;
        keteranganInput.classList.remove('bg-white');
        keteranganInput.classList.add('bg-gray-50');
        keteranganInput.value = '';
        document.getElementById(`jumlah_${supirId}`).value = '0';

        // Reset selisih display
        document.getElementById(`selisih_${supirId}`).innerHTML = '<span class="text-gray-400 italic text-sm">-</span>';
    }

    updateSummary();
}

// Buka modal untuk tambah supir/penerima
function openSupirModal() {
    const modal = document.getElementById('supir-modal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden'; // Prevent background scrolling

    // Show appropriate options based on currentTableType
    if (currentTableType === 'penerima') {
        // Show karyawan options, hide supir options
        document.querySelectorAll('.supir-option').forEach(option => {
            option.style.display = 'none';
        });
        document.querySelectorAll('.karyawan-option').forEach(option => {
            option.style.display = 'block';
        });
    } else {
        // Show supir options, hide karyawan options
        document.querySelectorAll('.supir-option').forEach(option => {
            option.style.display = 'block';
        });
        document.querySelectorAll('.karyawan-option').forEach(option => {
            option.style.display = 'none';
        });
    }

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

// Pilih karyawan dari modal
function selectKaryawanFromModal(karyawanId) {
    // Cek apakah karyawan sudah dipilih
    const checkbox = document.querySelector(`input[name="penerima[]"][value="${karyawanId}"]`);
    if (checkbox && !checkbox.checked) {
        checkbox.checked = true;
        toggleKaryawanRow(karyawanId);

        // Tutup modal setelah memilih
        closeSupirModal();

        // Scroll ke baris karyawan yang baru ditambah
        const row = document.querySelector(`.karyawan-row[data-karyawan-id="${karyawanId}"]`);
        if (row) {
            row.scrollIntoView({ behavior: 'smooth', block: 'center' });

            // Highlight row sejenak
            row.style.backgroundColor = '#dcfce7';
            setTimeout(function() {
                row.style.backgroundColor = '';
            }, 2000);
        }
    } else {
        alert('Karyawan ini sudah dipilih!');
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
                selisihDiv.innerHTML = `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800" title="Tambahan pembayaran diperlukan">+${formatNumber(selisih)}</span>`;
            } else if (selisih < 0) {
                const sisaUangMuka = Math.abs(selisih);
                selisihDiv.innerHTML = `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800" title="Sisa uang muka akan dikembalikan ke kas/bank">
                    <i class="fas fa-arrow-left mr-1"></i>-${formatNumber(sisaUangMuka)}
                </span>`;
            } else {
                selisihDiv.innerHTML = `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800" title="Pas, tidak ada selisih">0</span>`;
            }
        } else {
            selisihDiv.innerHTML = `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Penuh</span>`;
        }
    } else {
        selisihDiv.innerHTML = '<span class="text-gray-400 italic text-sm">-</span>';
    }

    updateSummary();
}

// Toggle individual mobil row
function toggleMobilRow(mobilId) {
    const checkbox = document.querySelector(`input[name="mobil[]"][value="${mobilId}"]`);
    const row = document.querySelector(`.mobil-row[data-mobil-id="${mobilId}"]`);
    const realisasiInput = document.getElementById(`realisasi_display_mobil_${mobilId}`);
    const keteranganInput = document.getElementById(`keterangan_mobil_${mobilId}`);

    if (checkbox.checked) {
        row.style.display = 'table-row';
        realisasiInput.disabled = false;
        // DON'T add required to display field - it has no name attribute
        keteranganInput.readOnly = false;
        keteranganInput.classList.remove('bg-gray-50');
        keteranganInput.classList.add('bg-white');

        // Auto-fill dengan Uang Muka jika tersedia
        const selectedUangMukaId = document.getElementById('pembayaran_uang_muka_id').value;
        if (selectedUangMukaId && currentUangMukaData[`mobil_${mobilId}`]) {
            const uangMukaAmount = currentUangMukaData[`mobil_${mobilId}`];
            realisasiInput.value = formatNumber(uangMukaAmount);
            document.getElementById(`jumlah_mobil_${mobilId}`).value = uangMukaAmount;
            hitungSelisihMobil(mobilId);
        }
    } else {
        row.style.display = 'none';
        realisasiInput.disabled = true;
        realisasiInput.value = '';
        keteranganInput.readOnly = true;
        keteranganInput.classList.remove('bg-white');
        keteranganInput.classList.add('bg-gray-50');
        keteranganInput.value = '';
        document.getElementById(`jumlah_mobil_${mobilId}`).value = '0';

        // Reset selisih display
        document.getElementById(`selisih_mobil_${mobilId}`).innerHTML = '<span class="text-gray-400 italic text-sm">-</span>';
    }

    updateSummary();
}

// Hitung selisih untuk mobil tertentu
function hitungSelisihMobil(mobilId) {
    const realisasiInput = document.getElementById(`realisasi_display_mobil_${mobilId}`);
    const selisihDiv = document.getElementById(`selisih_mobil_${mobilId}`);

    // Format currency
    formatCurrencyMobil(realisasiInput, mobilId);

    const realisasiAmount = parseInt(document.getElementById(`jumlah_mobil_${mobilId}`).value) || 0;
    const uangMukaAmount = currentUangMukaData[`mobil_${mobilId}`] || 0;

    if (realisasiAmount > 0) {
        if (uangMukaAmount > 0) {
            const selisih = realisasiAmount - uangMukaAmount;
            if (selisih > 0) {
                selisihDiv.innerHTML = `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800" title="Tambahan pembayaran diperlukan">+${formatNumber(selisih)}</span>`;
            } else if (selisih < 0) {
                const sisaUangMuka = Math.abs(selisih);
                selisihDiv.innerHTML = `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800" title="Sisa uang muka akan dikembalikan ke kas/bank">
                    <i class="fas fa-arrow-left mr-1"></i>-${formatNumber(sisaUangMuka)}
                </span>`;
            } else {
                selisihDiv.innerHTML = `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800" title="Pas, tidak ada selisih">0</span>`;
            }
        } else {
            selisihDiv.innerHTML = `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Penuh</span>`;
        }
    } else {
        selisihDiv.innerHTML = '<span class="text-gray-400 italic text-sm">-</span>';
    }

    updateSummary();
}

// Format currency input untuk mobil
function formatCurrencyMobil(input, mobilId) {
    let value = input.value.replace(/[^\d]/g, '');
    document.getElementById(`jumlah_mobil_${mobilId}`).value = value || '0';

    if (value) {
        input.value = formatNumber(value);
    } else {
        input.value = '';
    }
}

// Toggle individual karyawan row
function toggleKaryawanRow(karyawanId) {
    const checkbox = document.querySelector(`input[name="penerima[]"][value="${karyawanId}"]`);
    const row = document.querySelector(`.karyawan-row[data-karyawan-id="${karyawanId}"]`);
    const realisasiInput = document.getElementById(`realisasi_display_karyawan_${karyawanId}`);
    const keteranganInput = document.getElementById(`keterangan_karyawan_${karyawanId}`);

    if (checkbox.checked) {
        row.style.display = 'table-row';
        realisasiInput.disabled = false;
        // DON'T add required to display field - it has no name attribute
        keteranganInput.readOnly = false;
        keteranganInput.classList.remove('bg-gray-50');
        keteranganInput.classList.add('bg-white');

        // Auto-fill dengan Uang Muka jika tersedia
        const selectedUangMukaId = document.getElementById('pembayaran_uang_muka_id').value;
        if (selectedUangMukaId && currentUangMukaData[`karyawan_${karyawanId}`]) {
            const uangMukaAmount = currentUangMukaData[`karyawan_${karyawanId}`];
            realisasiInput.value = formatNumber(uangMukaAmount);
            document.getElementById(`jumlah_karyawan_${karyawanId}`).value = uangMukaAmount;
            hitungSelisihKaryawan(karyawanId);
        }
    } else {
        row.style.display = 'none';
        realisasiInput.disabled = true;
        realisasiInput.value = '';
        keteranganInput.readOnly = true;
        keteranganInput.classList.remove('bg-white');
        keteranganInput.classList.add('bg-gray-50');
        keteranganInput.value = '';
        document.getElementById(`jumlah_karyawan_${karyawanId}`).value = '0';

        // Reset selisih display
        document.getElementById(`selisih_karyawan_${karyawanId}`).innerHTML = '<span class="text-gray-400 italic text-sm">-</span>';
    }

    updateSummary();
}

// Hitung selisih untuk karyawan tertentu
function hitungSelisihKaryawan(karyawanId) {
    const realisasiInput = document.getElementById(`realisasi_display_karyawan_${karyawanId}`);
    const selisihDiv = document.getElementById(`selisih_karyawan_${karyawanId}`);

    // Format currency
    formatCurrencyKaryawan(realisasiInput, karyawanId);

    const realisasiAmount = parseInt(document.getElementById(`jumlah_karyawan_${karyawanId}`).value) || 0;
    const uangMukaAmount = currentUangMukaData[`karyawan_${karyawanId}`] || 0;

    if (realisasiAmount > 0) {
        if (uangMukaAmount > 0) {
            const selisih = realisasiAmount - uangMukaAmount;
            if (selisih > 0) {
                selisihDiv.innerHTML = `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800" title="Tambahan pembayaran diperlukan">+${formatNumber(selisih)}</span>`;
            } else if (selisih < 0) {
                const sisaUangMuka = Math.abs(selisih);
                selisihDiv.innerHTML = `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800" title="Sisa uang muka akan dikembalikan ke kas/bank">
                    <i class="fas fa-arrow-left mr-1"></i>-${formatNumber(sisaUangMuka)}
                </span>`;
            } else {
                selisihDiv.innerHTML = `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800" title="Pas, tidak ada selisih">0</span>`;
            }
        } else {
            selisihDiv.innerHTML = `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Penuh</span>`;
        }
    } else {
        selisihDiv.innerHTML = '<span class="text-gray-400 italic text-sm">-</span>';
    }

    updateSummary();
}

// Format currency input untuk karyawan
function formatCurrencyKaryawan(input, karyawanId) {
    let value = input.value.replace(/[^\d]/g, '');
    document.getElementById(`jumlah_karyawan_${karyawanId}`).value = value || '0';

    if (value) {
        input.value = formatNumber(value);
    } else {
        input.value = '';
    }
}

// Update tabel berdasarkan jenis kegiatan
function updateTableByKegiatanType(tableType) {
    const supirTable = document.getElementById('supir-table');
    const mobilTable = document.getElementById('mobil-table');
    const sectionTitle = document.getElementById('section-title');
    const sectionDesc = document.getElementById('section-desc');
    const tableHeader = document.getElementById('table-header');

    if (tableType === 'mobil') {
        // Show mobil table, hide supir table
        supirTable.style.display = 'none';
        mobilTable.style.display = 'block';
        currentTableType = 'mobil';

        // Update title and description for KIR & STNK
        if (sectionTitle) {
            sectionTitle.innerHTML = 'Realisasi Uang Muka per Mobil/Kendaraan <span class="text-red-500">*</span>';
        }
        if (sectionDesc) {
            sectionDesc.innerHTML = '<i class="fas fa-info-circle mr-1"></i>Pilih kendaraan dan masukkan realisasi uang muka untuk KIR & STNK.';
        }

        // Reset supir selections
        document.querySelectorAll('input[name="supir[]"]').forEach(cb => {
            cb.checked = false;
            const supirId = cb.value;
            const row = document.querySelector(`.supir-row[data-supir-id="${supirId}"]`);
            if (row) row.style.display = 'none';
        });
    } else if (tableType === 'supir') {
        // Show supir table for OB Muat/Bongkar (multiple supir)
        supirTable.style.display = 'block';
        mobilTable.style.display = 'none';
        currentTableType = 'supir';

        // Update title and description for OB activities
        if (sectionTitle) {
            sectionTitle.innerHTML = 'Realisasi Uang Muka per Supir <span class="text-red-500">*</span>';
        }
        if (sectionDesc) {
            sectionDesc.innerHTML = '<i class="fas fa-info-circle mr-1"></i>Pilih supir dan masukkan realisasi uang muka untuk kegiatan OB Muat/Bongkar.';
        }
        if (tableHeader) {
            tableHeader.textContent = 'Supir';
        }

        // Update button labels
        const selectAllLabel = document.getElementById('select-all-label');
        const addButtonLabel = document.getElementById('add-button-label');
        const modalTitle = document.getElementById('modal-title');
        if (selectAllLabel) {
            selectAllLabel.innerHTML = '<i class="fas fa-users mr-1"></i>Pilih Semua Supir';
        }
        if (addButtonLabel) {
            addButtonLabel.textContent = 'Tambah Supir';
        }
        if (modalTitle) {
            modalTitle.innerHTML = '<i class="fas fa-user-plus mr-2 text-green-500"></i>Pilih Supir untuk Realisasi';
        }

        // Show supir rows, hide karyawan rows for supir activities
        document.querySelectorAll('.karyawan-row').forEach(row => {
            row.style.display = 'none';
        });

        // Reset mobil selections
        document.querySelectorAll('input[name="mobil[]"]').forEach(cb => {
            cb.checked = false;
            const mobilId = cb.value;
            const row = document.querySelector(`.mobil-row[data-mobil-id="${mobilId}"]`);
            if (row) row.style.display = 'none';
        });

        // Reset penerima selections
        document.querySelectorAll('input[name="penerima[]"]').forEach(cb => {
            cb.checked = false;
        });
    } else {
        // Show supir table for penerima-based activities (Amprahan, Solar, Lain-lain)
        // But limit to single selection since these are penerima-based
        supirTable.style.display = 'block';
        mobilTable.style.display = 'none';
        currentTableType = 'penerima';

        // Update title and description for penerima activities
        if (sectionTitle) {
            sectionTitle.innerHTML = 'Realisasi Uang Muka per Penerima <span class="text-red-500">*</span>';
        }
        if (sectionDesc) {
            sectionDesc.innerHTML = '<i class="fas fa-info-circle mr-1"></i>Pilih penerima dan masukkan realisasi uang muka untuk kegiatan Amprahan, Solar, atau lainnya.';
        }
        if (tableHeader) {
            tableHeader.textContent = 'Penerima';
        }

        // Update button labels for penerima
        const selectAllLabel = document.getElementById('select-all-label');
        const addButtonLabel = document.getElementById('add-button-label');
        const modalTitle = document.getElementById('modal-title');
        if (selectAllLabel) {
            selectAllLabel.innerHTML = '<i class="fas fa-users mr-1"></i>Pilih Semua Penerima';
        }
        if (addButtonLabel) {
            addButtonLabel.textContent = 'Tambah Penerima';
        }
        if (modalTitle) {
            modalTitle.innerHTML = '<i class="fas fa-user-plus mr-2 text-green-500"></i>Pilih Penerima untuk Realisasi';
        }

        // Hide supir rows, show karyawan rows for penerima activities
        document.querySelectorAll('.supir-row').forEach(row => {
            row.style.display = 'none';
        });

        // Reset mobil selections
        document.querySelectorAll('input[name="mobil[]"]').forEach(cb => {
            cb.checked = false;
            const mobilId = cb.value;
            const row = document.querySelector(`.mobil-row[data-mobil-id="${mobilId}"]`);
            if (row) row.style.display = 'none';
        });

        // Reset supir selections
        document.querySelectorAll('input[name="supir[]"]').forEach(cb => {
            cb.checked = false;
        });

        // Reset karyawan selections
        document.querySelectorAll('input[name="penerima[]"]').forEach(cb => {
            cb.checked = false;
        });
    }

    // Reset current uang muka data
    currentUangMukaData = {};
    updateSummary();
}

// Load supir data based on selected voyage
function loadSupirByVoyage() {
    const selectedVoyage = $('#nomor_voyage').val();
    
    if (!selectedVoyage) {
        console.log('Voyage not selected');
        return;
    }
    
    console.log('Loading supir for voyage:', selectedVoyage);
    
    // Call API to get supir by voyage
    $.ajax({
        url: '/realisasi-uang-muka/api/get-supir-by-voyage',
        type: 'GET',
        data: {
            voyage: selectedVoyage
        },
        dataType: 'json',
        success: function(response) {
            console.log('=== SUPIR BY VOYAGE RESPONSE ===');
            console.log('Full response:', response);
            console.log('Response success:', response.success);
            console.log('Response data:', response.data);
            console.log('Data length:', response.data ? response.data.length : 0);
            
            if (response.success && response.data && response.data.length > 0) {
                console.log('Starting to process', response.data.length, 'supir');
                
                // Reset all supir selections first
                document.querySelectorAll('input[name="supir[]"]').forEach(cb => {
                    cb.checked = false;
                    const supirId = cb.value;
                    const row = document.querySelector(`.supir-row[data-supir-id="${supirId}"]`);
                    if (row) row.style.display = 'none';
                });
                console.log('All supir selections reset');
                
                // Auto-check and populate supir from response
                let processedCount = 0;
                response.data.forEach(function(supirData) {
                    console.log('--- Processing supir:', supirData.nama_supir, '(ID:', supirData.supir_id, ')');
                    
                    const supirCheckbox = document.querySelector(`input[name="supir[]"][value="${supirData.supir_id}"]`);
                    console.log('Checkbox found:', supirCheckbox ? 'YES' : 'NO');
                    
                    if (supirCheckbox) {
                        // Check the checkbox
                        supirCheckbox.checked = true;
                        
                        // Show the row
                        const row = document.querySelector(`.supir-row[data-supir-id="${supirData.supir_id}"]`);
                        if (row) {
                            row.style.display = 'table-row';
                            console.log('Row displayed for supir:', supirData.supir_id);
                        }
                        
                        // Enable inputs
                        const realisasiInput = document.getElementById(`realisasi_display_${supirData.supir_id}`);
                        const keteranganInput = document.getElementById(`keterangan_${supirData.supir_id}`);
                        
                        if (realisasiInput) {
                            realisasiInput.disabled = false;
                        }
                        if (keteranganInput) {
                            keteranganInput.readOnly = false;
                            keteranganInput.classList.remove('bg-gray-50');
                            keteranganInput.classList.add('bg-white');
                        }
                        
                        // Set the DP yang sudah dibayar sebagai uang muka data
                        currentUangMukaData[supirData.supir_id] = supirData.dp_dibayar || 0;
                        console.log('Set currentUangMukaData for', supirData.supir_id, '= DP:', supirData.dp_dibayar);
                        
                        // Update DP display - tampilkan DP yang sudah dibayar
                        const dpDiv = document.getElementById(`dp_${supirData.supir_id}`);
                        if (dpDiv) {
                            if (supirData.dp_dibayar > 0) {
                                dpDiv.innerHTML = `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    DP: Rp ${formatNumber(supirData.dp_dibayar)}
                                </span>`;
                            } else {
                                dpDiv.innerHTML = `<span class="text-gray-400 italic text-xs">Belum ada DP</span>`;
                            }
                            console.log('DP display updated for supir:', supirData.supir_id, 'DP:', supirData.dp_dibayar);
                        }
                        
                        // Auto-fill realisasi with total tagihan
                        if (realisasiInput) {
                            realisasiInput.value = formatNumber(supirData.total_tagihan);
                            const jumlahInput = document.getElementById(`jumlah_${supirData.supir_id}`);
                            if (jumlahInput) {
                                jumlahInput.value = supirData.total_tagihan;
                            }
                            hitungSelisih(supirData.supir_id);
                            console.log('Realisasi filled for supir:', supirData.supir_id, 'Tagihan:', supirData.total_tagihan, 'Kontainer:', supirData.jumlah_kontainer);
                        }
                        
                        processedCount++;
                    } else {
                        console.warn('Supir checkbox not found for ID:', supirData.supir_id);
                        console.warn('Available checkboxes:', document.querySelectorAll('input[name="supir[]"]').length);
                    }
                });
                
                console.log('Total supir processed:', processedCount, 'out of', response.data.length);
                
                // Show info message
                const infoDiv = document.getElementById('kegiatan-info');
                if (infoDiv) {
                    const supirCount = response.data.length;
                    const totalKontainer = response.data.reduce((sum, item) => sum + parseInt(item.jumlah_kontainer || 0), 0);
                    
                    infoDiv.innerHTML = `
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">Voyage ${selectedVoyage} - ${kegiatanFilter.toUpperCase()}</p>
                                <p class="text-xs text-green-600 mt-1">
                                    ${supirCount} supir dengan ${totalKontainer} kontainer telah dipilih dan diisi otomatis
                                </p>
                            </div>
                        </div>
                    `;
                    infoDiv.className = 'mt-2 p-3 bg-green-50 border border-green-200 rounded-md';
                    infoDiv.classList.remove('hidden');
                }
                
                updateSummary();
            } else {
                console.warn('No supir data or empty response');
                console.warn('Success flag:', response.success);
                console.warn('Data:', response.data);
                alert('Tidak ada data supir untuk voyage ' + selectedVoyage);
            }
        },
        error: function(xhr, status, error) {
            console.error('=== AJAX ERROR ===');
            console.error('Status:', status);
            console.error('Error:', error);
            console.error('XHR Status:', xhr.status);
            console.error('XHR Response:', xhr.responseText);
            console.error('XHR Full:', xhr);
            alert('Gagal memuat data supir untuk voyage ini. Error: ' + error + ' (Status: ' + xhr.status + ')');
        }
    });
}

// Update Uang Muka display
function loadUangMukaList() {
    const uangMukaSelect = document.getElementById('pembayaran_uang_muka_id');
    
    // Reset dropdown uang muka
    uangMukaSelect.innerHTML = '<option value="">-- Tidak Menggunakan Uang Muka --</option>';
    
    // Tampilkan semua uang muka
    Object.keys(uangMukaData).forEach(function(uangMukaId) {
        const uangMuka = uangMukaData[uangMukaId];
        const option = document.createElement('option');
        option.value = uangMukaId;
        option.textContent = `${uangMuka.nomor} - ${uangMuka.tanggal} - ${uangMuka.supir_names.join(', ')} - Rp ${formatNumber(uangMuka.total)}`;
        uangMukaSelect.appendChild(option);
    });
    
    // Reset uang muka selection info
    const uangMukaInfoDiv = document.getElementById('uang-muka-selection-info');
    if (uangMukaInfoDiv) {
        uangMukaInfoDiv.classList.add('hidden');
    }
    
    // Update display
    updateUangMukaDisplay();
}

// Update Uang Muka display untuk semua supir dan mobil
function updateUangMukaDisplay() {
    const selectedUangMukaId = document.getElementById('pembayaran_uang_muka_id').value;
    console.log('updateUangMukaDisplay called - selectedUangMukaId:', selectedUangMukaId);
    console.log('currentTableType:', currentTableType);

    if (selectedUangMukaId && uangMukaData[selectedUangMukaId]) {
        console.log('Selected Uang Muka Full Data:', uangMukaData[selectedUangMukaId]);
    }

    // Reset currentUangMukaData
    currentUangMukaData = {};

    // Show/hide appropriate rows based on currentTableType
    if (currentTableType === 'penerima') {
        // Hide supir rows, karyawan rows visibility will be controlled by data matching below
        document.querySelectorAll('.supir-row').forEach(row => {
            row.style.display = 'none';
        });
        console.log('Preparing karyawan rows for penerima table type');
    } else if (currentTableType === 'supir') {
        // Hide karyawan rows, supir rows visibility controlled by toggleSupirRow
        document.querySelectorAll('.karyawan-row').forEach(row => {
            row.style.display = 'none';
        });
        console.log('Hiding karyawan rows for supir table type');
    }

    if (currentTableType === 'mobil') {
        // Update Uang Muka display untuk semua mobil
        @foreach($mobilList as $mobil)
            const uangMukaDivMobil{{ $mobil->id }} = document.getElementById('dp_mobil_{{ $mobil->id }}');
            let uangMukaAmountMobil{{ $mobil->id }} = 0;

            if (selectedUangMukaId && uangMukaData[selectedUangMukaId]) {
                const selectedUangMuka = uangMukaData[selectedUangMukaId];

                // Untuk KIR & STNK - cek mobil_id
                if (selectedUangMuka.mobil_id == '{{ $mobil->id }}') {
                    uangMukaAmountMobil{{ $mobil->id }} = selectedUangMuka.total;
                    currentUangMukaData['mobil_{{ $mobil->id }}'] = uangMukaAmountMobil{{ $mobil->id }};
                    uangMukaDivMobil{{ $mobil->id }}.innerHTML = `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Rp ${formatNumber(uangMukaAmountMobil{{ $mobil->id }})}</span>`;
                } else {
                    uangMukaDivMobil{{ $mobil->id }}.innerHTML = '<span class="text-gray-400 italic">-</span>';
                }
            } else {
                uangMukaDivMobil{{ $mobil->id }}.innerHTML = '<span class="text-gray-400 italic">-</span>';
            }

            // Update selisih jika mobil sudah dipilih
            const checkboxMobil{{ $mobil->id }} = document.querySelector(`input[name="mobil[]"][value="{{ $mobil->id }}"]`);
            if (checkboxMobil{{ $mobil->id }} && checkboxMobil{{ $mobil->id }}.checked) {
                hitungSelisihMobil('{{ $mobil->id }}');
            }
        @endforeach
    } else {
        // Update Uang Muka display berdasarkan table type
        if (currentTableType === 'supir') {
            // Untuk OB Muat/Bongkar - gunakan supir list
            @foreach($supirList as $supir)
                const uangMukaDiv{{ $supir->id }} = document.getElementById('dp_{{ $supir->id }}');
                let uangMukaAmount{{ $supir->id }} = 0;

                if (selectedUangMukaId && uangMukaData[selectedUangMukaId]) {
                    const selectedUangMuka = uangMukaData[selectedUangMukaId];
                    
                    // ID supir untuk matching (sekarang menggunakan ID, bukan nama)
                    const supirId = '{{ $supir->id }}';
                    const supirIdInt = {{ $supir->id }};

                    // Untuk OB Muat/Bongkar - cek jumlah_per_supir by ID
                    // Check both string and integer keys for compatibility
                    if (selectedUangMuka.jumlah_per_supir && 
                        (selectedUangMuka.jumlah_per_supir[supirId] || selectedUangMuka.jumlah_per_supir[supirIdInt])) {
                        uangMukaAmount{{ $supir->id }} = selectedUangMuka.jumlah_per_supir[supirId] || selectedUangMuka.jumlah_per_supir[supirIdInt];
                        currentUangMukaData['{{ $supir->id }}'] = uangMukaAmount{{ $supir->id }};
                        uangMukaDiv{{ $supir->id }}.innerHTML = `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Rp ${formatNumber(uangMukaAmount{{ $supir->id }})}</span>`;
                        
                        console.log('Matched supir ID:', supirId, 'Amount:', uangMukaAmount{{ $supir->id }});
                    } else {
                        uangMukaDiv{{ $supir->id }}.innerHTML = '<span class="text-gray-400 italic">-</span>';
                        console.log('No match for supir ID:', supirId, 'Available keys:', Object.keys(selectedUangMuka.jumlah_per_supir || {}));
                    }
                } else {
                    uangMukaDiv{{ $supir->id }}.innerHTML = '<span class="text-gray-400 italic">-</span>';
                }

                // Update selisih jika supir sudah dipilih
                const checkbox{{ $supir->id }} = document.querySelector(`input[name="supir[]"][value="{{ $supir->id }}"]`);
                if (checkbox{{ $supir->id }} && checkbox{{ $supir->id }}.checked) {
                    hitungSelisih('{{ $supir->id }}');
                }
            @endforeach
        } else if (currentTableType === 'penerima') {
            // Untuk Amprahan, Solar, dll - gunakan karyawan list
            @foreach($karyawanList as $karyawan)
                const uangMukaDivKaryawan{{ $karyawan->id }} = document.getElementById('dp_karyawan_{{ $karyawan->id }}');
                const karyawanRow{{ $karyawan->id }} = document.querySelector('.karyawan-row[data-karyawan-id="{{ $karyawan->id }}"]');
                let uangMukaAmountKaryawan{{ $karyawan->id }} = 0;

                if (uangMukaDivKaryawan{{ $karyawan->id }} && selectedUangMukaId && uangMukaData[selectedUangMukaId]) {
                    const selectedUangMuka = uangMukaData[selectedUangMukaId];

                    // Untuk Amprahan, Solar, dll - cek penerima_id
                    console.log('Checking penerima for karyawan {{ $karyawan->id }}:', selectedUangMuka.penerima_id, '==' , '{{ $karyawan->id }}');
                    if (selectedUangMuka.penerima_id == '{{ $karyawan->id }}') {
                        // Show this karyawan row since they match the selected uang muka
                        if (karyawanRow{{ $karyawan->id }}) {
                            karyawanRow{{ $karyawan->id }}.style.display = 'table-row';
                        }

                        uangMukaAmountKaryawan{{ $karyawan->id }} = selectedUangMuka.total;
                        console.log('Match found! Setting amount:', uangMukaAmountKaryawan{{ $karyawan->id }});
                        currentUangMukaData['karyawan_{{ $karyawan->id }}'] = uangMukaAmountKaryawan{{ $karyawan->id }};
                        uangMukaDivKaryawan{{ $karyawan->id }}.innerHTML = `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Rp ${formatNumber(uangMukaAmountKaryawan{{ $karyawan->id }})}</span>`;
                    } else {
                        // Hide this karyawan row since they don't match
                        if (karyawanRow{{ $karyawan->id }}) {
                            karyawanRow{{ $karyawan->id }}.style.display = 'none';
                        }
                        uangMukaDivKaryawan{{ $karyawan->id }}.innerHTML = '<span class="text-gray-400 italic">-</span>';
                    }

                    // Update selisih jika karyawan sudah dipilih
                    const checkboxKaryawan{{ $karyawan->id }} = document.querySelector(`input[name="penerima[]"][value="{{ $karyawan->id }}"]`);
                    if (checkboxKaryawan{{ $karyawan->id }} && checkboxKaryawan{{ $karyawan->id }}.checked) {
                        hitungSelisihKaryawan('{{ $karyawan->id }}');
                    }
                } else {
                    // No uang muka selected or no data, hide the row
                    if (karyawanRow{{ $karyawan->id }}) {
                        karyawanRow{{ $karyawan->id }}.style.display = 'none';
                    }
                    if (uangMukaDivKaryawan{{ $karyawan->id }}) {
                        uangMukaDivKaryawan{{ $karyawan->id }}.innerHTML = '<span class="text-gray-400 italic">-</span>';
                    }
                }
            @endforeach
        }
    }

    updateSummary();
}

// Update summary calculation
function updateSummary() {
    let totalRealisasi = 0;
    let totalDp = 0;
    let totalItems = 0;
    let itemLabel = '';

    if (currentTableType === 'mobil') {
        const selectedMobil = document.querySelectorAll('input[name="mobil[]"]:checked');
        totalItems = selectedMobil.length;
        itemLabel = 'Mobil';

        selectedMobil.forEach(function(checkbox) {
            const mobilId = checkbox.value;
            const realisasiAmount = parseInt(document.getElementById(`jumlah_mobil_${mobilId}`).value) || 0;
            const uangMukaAmount = currentUangMukaData[`mobil_${mobilId}`] || 0;

            totalRealisasi += realisasiAmount;
            totalDp += uangMukaAmount;
        });
    } else if (currentTableType === 'supir') {
        const selectedSupir = document.querySelectorAll('input[name="supir[]"]:checked');
        totalItems = selectedSupir.length;
        itemLabel = 'Supir';

        selectedSupir.forEach(function(checkbox) {
            const supirId = checkbox.value;
            const realisasiAmount = parseInt(document.getElementById(`jumlah_${supirId}`).value) || 0;
            const uangMukaAmount = currentUangMukaData[supirId] || 0;

            totalRealisasi += realisasiAmount;
            totalDp += uangMukaAmount;
        });
    } else if (currentTableType === 'penerima') {
        const selectedPenerima = document.querySelectorAll('input[name="penerima[]"]:checked');
        totalItems = selectedPenerima.length;
        itemLabel = 'Penerima';

        selectedPenerima.forEach(function(checkbox) {
            const penerimaId = checkbox.value;
            const realisasiAmount = parseInt(document.getElementById(`jumlah_karyawan_${penerimaId}`).value) || 0;
            const uangMukaAmount = currentUangMukaData[`karyawan_${penerimaId}`] || 0;

            totalRealisasi += realisasiAmount;
            totalDp += uangMukaAmount;
        });
    }

    const totalSelisih = totalRealisasi - totalDp;

    if (totalItems > 0) {
        document.getElementById('summary-section').classList.remove('hidden');
        document.getElementById('total-supir').textContent = totalItems;

        // Update label sesuai jenis tabel
        const totalSupirLabel = document.querySelector('#summary-section .text-gray-600');
        if (totalSupirLabel) {
            totalSupirLabel.textContent = `Jumlah ${itemLabel}`;
        }

        document.getElementById('total-realisasi').textContent = 'Rp ' + formatNumber(totalRealisasi);
        document.getElementById('total-dp').textContent = 'Rp ' + formatNumber(totalDp);

        // Update selisih dengan informasi tambahan
        const selisihElement = document.getElementById('total-selisih');
        if (totalSelisih > 0) {
            selisihElement.innerHTML = `<span class="text-red-600">+Rp ${formatNumber(totalSelisih)}</span>`;
            selisihElement.parentElement.querySelector('.text-gray-600').textContent = 'Tambahan Bayar';
        } else if (totalSelisih < 0) {
            const sisaUangMuka = Math.abs(totalSelisih);
            selisihElement.innerHTML = `<span class="text-blue-600 flex items-center">
                <i class="fas fa-arrow-left mr-1"></i>Rp ${formatNumber(sisaUangMuka)}
            </span>`;
            selisihElement.parentElement.querySelector('.text-gray-600').textContent = 'Dikembalikan ke Kas';
        } else {
            selisihElement.innerHTML = `<span class="text-green-600">Rp 0</span>`;
            selisihElement.parentElement.querySelector('.text-gray-600').textContent = 'Selisih';
        }
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

    // Setup individual mobil checkboxes
    document.querySelectorAll('input[name="mobil[]"]').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            toggleMobilRow(this.value);
        });
    });

    // Setup individual penerima checkboxes
    document.querySelectorAll('input[name="penerima[]"]').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            toggleKaryawanRow(this.value);
        });
    });

    // Event listener untuk search supir di modal
    if (document.getElementById('supir-search')) {
        document.getElementById('supir-search').addEventListener('input', function() {
            filterSupirOptions(this.value);
        });
    }

    // Event listener untuk click pada supir options
    document.querySelectorAll('.supir-option').forEach(function(option) {
        option.addEventListener('click', function() {
            const supirId = this.getAttribute('data-supir-id');
            selectSupirFromModal(supirId);
        });
    });

    // Close modal when clicking outside
    if (document.getElementById('supir-modal')) {
        document.getElementById('supir-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeSupirModal();
            }
        });
    }

    // Close modal dengan ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('supir-modal');
            if (modal && !modal.classList.contains('hidden')) {
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
            console.log('Selected Uang Muka Data:', uangMuka);
            console.log('Current Table Type:', currentTableType);
            uangMukaInfoDiv.classList.remove('hidden');

            if (currentTableType === 'mobil') {
                // Auto-select some mobil for vehicle-based activities
                const mobilCheckboxes = document.querySelectorAll('input[name="mobil[]"]');
                const totalMobil = mobilCheckboxes.length;

                // Select mobil up to the number of original supir
                let selectedCount = 0;
                const maxSelect = Math.min(uangMuka.supir_count || 1, totalMobil);

                mobilCheckboxes.forEach(function(checkbox) {
                    if (selectedCount < maxSelect && !checkbox.checked) {
                        checkbox.checked = true;
                        toggleMobilRow(checkbox.value);
                        selectedCount++;
                    }
                });

                // Determine activity description based on kegiatan name (only for KIR & STNK)
                let activityDesc = 'KIR & STNK kendaraan';

                uangMukaInfoSpan.innerHTML = `
                    <strong>${uangMuka.nomor}</strong> - ${uangMuka.tanggal} - untuk ${maxSelect} kendaraan<br>
                    <span class="font-semibold text-green-800">Nilai Uang Muka: Rp ${new Intl.NumberFormat('id-ID').format(uangMuka.total)}</span>
                    <br><span class="text-xs text-green-600"><strong>Kendaraan:</strong> ${maxSelect} kendaraan dipilih otomatis</span>
                    <br><span class="text-xs text-blue-600 mt-1"><i class="fas fa-magic mr-1"></i>Uang muka akan dibagi rata untuk ${activityDesc}</span>
                `;
            } else {
                // Handle different table types for auto-selection
                if (currentTableType === 'supir') {
                    // Auto-select supir dari Uang Muka (untuk OB Bongkar)
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
                        <strong>${uangMuka.nomor}</strong> - ${uangMuka.tanggal} - ${uangMuka.supir_count} supir<br>
                        <span class="font-semibold text-green-800">Nilai Uang Muka: Rp ${new Intl.NumberFormat('id-ID').format(uangMuka.total)}</span>
                        ${supirNamesText}
                        <br><span class="text-xs text-blue-600 mt-1"><i class="fas fa-magic mr-1"></i>Supir telah dipilih otomatis dari Uang Muka ini</span>
                    `;
                } else if (currentTableType === 'penerima') {
                    // Auto-select penerima dari Uang Muka (untuk Amprahan, Solar, dll)
                    if (uangMuka.penerima_id) {
                        const checkbox = document.querySelector(`input[name="penerima[]"][value="${uangMuka.penerima_id}"]`);
                        if (checkbox && !checkbox.checked) {
                            checkbox.checked = true;
                            toggleKaryawanRow(uangMuka.penerima_id);
                        }
                    }

                    let penerimaText = '';
                    if (uangMuka.penerima_nama) {
                        penerimaText = `<br><span class="text-xs text-green-600"><strong>Penerima:</strong> ${uangMuka.penerima_nama}</span>`;
                    }

                    uangMukaInfoSpan.innerHTML = `
                        <strong>${uangMuka.nomor}</strong> - ${uangMuka.tanggal} - untuk penerima<br>
                        <span class="font-semibold text-green-800">Nilai Uang Muka: Rp ${new Intl.NumberFormat('id-ID').format(uangMuka.total)}</span>
                        ${penerimaText}
                        <br><span class="text-xs text-blue-600 mt-1"><i class="fas fa-magic mr-1"></i>Penerima telah dipilih otomatis dari Uang Muka ini</span>
                    `;
                }
            }
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
                helpText.textContent = 'Untuk realisasi biasanya kredit (uang keluar)';
                helpText.className = 'mt-1 text-sm text-gray-500';
            }
        }
    });

    // Form validation with detailed logging
    document.getElementById('form-realisasi-uang-muka').addEventListener('submit', function(e) {
        console.log('=== FORM SUBMIT STARTED ===');
        console.log('Current table type:', currentTableType);

        if (currentTableType === 'mobil') {
            const selectedMobil = document.querySelectorAll('input[name="mobil[]"]:checked');
            console.log('Selected mobil count:', selectedMobil.length);

            if (selectedMobil.length === 0) {
                e.preventDefault();
                alert('Harap pilih minimal satu mobil/kendaraan');
                return false;
            }

            let hasEmptyRealisasi = false;
            selectedMobil.forEach(function(checkbox) {
                const mobilId = checkbox.value;
                const jumlahInput = document.getElementById(`jumlah_mobil_${mobilId}`);
                console.log(`Mobil ${mobilId} - jumlah: ${jumlahInput.value}`);
                if (!jumlahInput.value || jumlahInput.value === '0') {
                    hasEmptyRealisasi = true;
                }
            });

            if (hasEmptyRealisasi) {
                e.preventDefault();
                alert('Harap isi semua realisasi untuk mobil yang dipilih');
                return false;
            }
        } else if (currentTableType === 'supir') {
            const selectedSupir = document.querySelectorAll('input[name="supir[]"]:checked');
            console.log('Selected supir count:', selectedSupir.length);

            if (selectedSupir.length === 0) {
                e.preventDefault();
                alert('Harap pilih minimal satu supir');
                return false;
            }

            let hasEmptyRealisasi = false;
            selectedSupir.forEach(function(checkbox) {
                const supirId = checkbox.value;
                const jumlahInput = document.getElementById(`jumlah_${supirId}`);
                console.log(`Supir ${supirId} - jumlah: ${jumlahInput.value}`);
                if (!jumlahInput.value || jumlahInput.value === '0') {
                    hasEmptyRealisasi = true;
                }
            });

            if (hasEmptyRealisasi) {
                e.preventDefault();
                alert('Harap isi semua realisasi untuk supir yang dipilih');
                return false;
            }
        } else if (currentTableType === 'penerima') {
            const selectedPenerima = document.querySelectorAll('input[name="penerima[]"]:checked');
            console.log('Selected penerima count:', selectedPenerima.length);

            if (selectedPenerima.length === 0) {
                e.preventDefault();
                console.log('VALIDATION FAILED: No penerima selected');
                alert('Harap pilih minimal satu penerima');
                return false;
            }

            let hasEmptyRealisasi = false;
            selectedPenerima.forEach(function(checkbox) {
                const penerimaId = checkbox.value;
                const jumlahInput = document.getElementById(`jumlah_karyawan_${penerimaId}`);
                console.log(`Penerima ${penerimaId} - jumlah: ${jumlahInput ? jumlahInput.value : 'NOT FOUND'}`);
                if (!jumlahInput || !jumlahInput.value || jumlahInput.value === '0') {
                    hasEmptyRealisasi = true;
                }
            });

            if (hasEmptyRealisasi) {
                e.preventDefault();
                console.log('VALIDATION FAILED: Empty realisasi found');
                alert('Harap isi semua realisasi untuk penerima yang dipilih');
                return false;
            }
        }

        console.log('VALIDATION PASSED - Form will submit');
        console.log('Form action:', this.action);
        console.log('Form method:', this.method);

        // Log all form data before submission
        const formData = new FormData(this);
        console.log('=== FORM DATA BEING SUBMITTED ===');
        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }
        console.log('=== END FORM DATA ===');
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

    // Load uang muka list on page load
    loadUangMukaList();
});
</script>
@endpush

<!-- Modal Popup untuk Tambah Supir -->
<div id="supir-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-3 border-b">
            <h3 id="modal-title" class="text-lg font-semibold text-gray-900">
                <i class="fas fa-user-plus mr-2 text-green-500"></i>
                Pilih Supir untuk Realisasi
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
                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-700">{{ substr($supir->nama_lengkap, 0, 2) }}</span>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-900">{{ $supir->nama_lengkap }}</div>
                                    <div class="text-sm text-gray-500">
                                        @if($supir->nama_panggilan)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 mr-2">
                                                {{ $supir->nama_panggilan }}
                                            </span>
                                        @endif
                                        NIK: {{ $supir->nik }}
                                    </div>
                                    @if($supir->divisi)
                                        <div class="text-xs text-gray-400 mt-1">
                                            <i class="fas fa-briefcase mr-1"></i>{{ $supir->divisi }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 border border-green-200">
                                    <i class="fas fa-plus mr-1"></i>
                                    Pilih
                                </span>
                                <div class="text-xs text-gray-400 mt-1">
                                    Status: {{ $supir->status ?? 'active' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    @foreach($karyawanList as $karyawan)
                    <div class="karyawan-option p-4 hover:bg-green-50 cursor-pointer border-l-4 border-transparent hover:border-green-400 transition-all duration-200"
                         data-karyawan-id="{{ $karyawan->id }}"
                         data-karyawan-nik="{{ $karyawan->nik }}"
                         data-karyawan-nama="{{ $karyawan->nama_lengkap }}"
                         data-karyawan-panggilan="{{ $karyawan->nama_panggilan }}"
                         onclick="selectKaryawanFromModal('{{ $karyawan->id }}')"
                         style="display: none;">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-green-300 flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-700">{{ substr($karyawan->nama_lengkap, 0, 2) }}</span>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-900">{{ $karyawan->nama_lengkap }}</div>
                                    <div class="text-sm text-gray-500">
                                        @if($karyawan->nama_panggilan)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 mr-2">
                                                {{ $karyawan->nama_panggilan }}
                                            </span>
                                        @endif
                                        NIK: {{ $karyawan->nik }}
                                    </div>
                                    @if($karyawan->divisi)
                                        <div class="text-xs text-gray-400 mt-1">
                                            <i class="fas fa-briefcase mr-1"></i>{{ $karyawan->divisi }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 border border-green-200">
                                    <i class="fas fa-plus mr-1"></i>
                                    Pilih
                                </span>
                                <div class="text-xs text-gray-400 mt-1">
                                    Status: {{ $karyawan->status ?? 'active' }}
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
