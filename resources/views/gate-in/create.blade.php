@extends('layouts.app')

@section('title', 'Buat Gate In Baru')
@section('page_title', 'Buat Gate In Baru')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-4xl mx-auto px-4">

        <!-- Header -->
        <div class="bg-white rounded-lg border border-gray-200 mb-6">
            <div class="bg-blue-600 px-6 py-4 rounded-t-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-semibold text-white">Buat Gate In Baru</h1>
                        <p class="text-blue-100 text-sm mt-1">Entri gate in untuk kontainer dari checkpoint supir</p>
                    </div>
                    <a href="{{ route('gate-in.index') }}" class="inline-flex items-center px-3 py-2 bg-white bg-opacity-10 hover:bg-opacity-20 text-white text-sm rounded-md transition">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Alert Notifications -->
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-md mb-4">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-md mb-4">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-md mb-4">
                <div class="flex items-start">
                    <svg class="w-4 h-4 mr-2 mt-0.5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium mb-1">Periksa input berikut:</p>
                        <ul class="text-sm space-y-1">
                            @foreach($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Form -->
        <div class="bg-white rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Informasi Gate In</h2>
            </div>

            <div class="p-6">
                <form action="{{ route('gate-in.store') }}" method="POST" id="gate-in-form" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Nomor Gate In -->
                        <div class="space-y-1">
                            <label for="nomor_gate_in" class="block text-sm font-medium text-gray-700">
                                Nomor Gate In <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nomor_gate_in" id="nomor_gate_in"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Masukkan nomor gate in" value="{{ old('nomor_gate_in') }}" required maxlength="20">
                            @error('nomor_gate_in')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tanggal Gate In -->
                        <div class="space-y-1">
                            <label for="tanggal_gate_in" class="block text-sm font-medium text-gray-700">
                                Tanggal Gate In <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" name="tanggal_gate_in" id="tanggal_gate_in"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   value="{{ old('tanggal_gate_in', now()->format('Y-m-d\TH:i')) }}" required>
                            @error('tanggal_gate_in')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Pelabuhan -->
                        <div class="space-y-1">
                            <label for="pelabuhan" class="block text-sm font-medium text-gray-700">
                                Pelabuhan <span class="text-red-500">*</span>
                            </label>
                            <select name="pelabuhan" id="pelabuhan" class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">Pilih Pelabuhan</option>
                                @foreach($pelabuhans as $pelabuhan)
                                    <option value="{{ $pelabuhan }}" {{ old('pelabuhan') == $pelabuhan ? 'selected' : '' }}>
                                        {{ $pelabuhan }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pelabuhan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Kegiatan -->
                        <div class="space-y-1">
                            <label for="kegiatan" class="block text-sm font-medium text-gray-700">
                                Kegiatan <span class="text-red-500">*</span>
                            </label>
                            <select name="kegiatan" id="kegiatan" class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">Pilih Kegiatan</option>
                                @foreach($kegiatans as $kegiatan)
                                    <option value="{{ $kegiatan }}" {{ old('kegiatan') == $kegiatan ? 'selected' : '' }}>
                                        {{ $kegiatan }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kegiatan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Gudang -->
                        <div class="space-y-1">
                            <label for="gudang" class="block text-sm font-medium text-gray-700">
                                Gudang <span class="text-red-500">*</span>
                            </label>
                            <select name="gudang" id="gudang" class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">Pilih Gudang</option>
                                @foreach($gudangs as $gudang)
                                    <option value="{{ $gudang }}" {{ old('gudang') == $gudang ? 'selected' : '' }}>
                                        {{ $gudang }}
                                    </option>
                                @endforeach
                            </select>
                            @error('gudang')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Kontainer -->
                        <div class="space-y-1">
                            <label for="kontainer" class="block text-sm font-medium text-gray-700">
                                Kontainer <span class="text-red-500">*</span>
                            </label>
                            <select name="kontainer" id="kontainer" class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">Pilih Kontainer</option>
                                @foreach($kontainerOptions as $kontainer)
                                    <option value="{{ $kontainer }}" {{ old('kontainer') == $kontainer ? 'selected' : '' }}>
                                        {{ $kontainer }}ft
                                    </option>
                                @endforeach
                            </select>
                            @error('kontainer')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Muatan -->
                        <div class="space-y-1">
                            <label for="muatan" class="block text-sm font-medium text-gray-700">
                                Muatan <span class="text-red-500">*</span>
                            </label>
                            <select name="muatan" id="muatan" class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">Pilih Muatan</option>
                                @foreach($muatans as $muatan)
                                    <option value="{{ $muatan }}" {{ old('muatan') == $muatan ? 'selected' : '' }}>
                                        {{ $muatan }}
                                    </option>
                                @endforeach
                            </select>
                            @error('muatan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Kapal -->
                        <div class="space-y-1 md:col-span-2">
                            <label for="kapal_id" class="block text-sm font-medium text-gray-700">
                                Kapal <span class="text-red-500">*</span>
                            </label>
                            <select name="kapal_id" id="kapal_id" class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
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
                    </div>

                    <!-- Kontainer Section -->
                    <div class="space-y-3 border-t border-gray-200 pt-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Kontainer dari Checkpoint Supir <span class="text-red-500">*</span>
                            </label>
                            <p class="text-sm text-gray-500">Pilih kontainer yang sudah melalui checkpoint supir</p>
                        </div>

                        <!-- Search Input -->
                        <div class="relative" id="search-container" style="display: none;">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="text" id="kontainer-search" placeholder="Cari kontainer..."
                                   class="block w-full pl-10 pr-10 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <button type="button" id="clear-search" class="text-gray-400 hover:text-gray-600 focus:outline-none" style="display: none;">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Search Results Info -->
                        <div id="search-info" class="text-xs text-gray-500" style="display: none;">
                            <span id="search-results-count"></span>
                        </div>

                        <!-- Loading State -->
                        <div id="loading-kontainer" class="border border-gray-200 rounded-md p-4 bg-gray-50">
                            <div class="flex items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="text-sm text-gray-600">Memuat data kontainer...</span>
                            </div>
                        </div>

                        <!-- Kontainer Container -->
                        <div id="kontainer-container" class="hidden border border-gray-200 rounded-md bg-gray-50 min-h-[100px]">
                            <!-- Data kontainer akan dimuat di sini -->
                        </div>

                        <!-- Error State -->
                        <div id="error-kontainer" class="hidden border border-red-200 rounded-md p-4 bg-red-50">
                            <div class="text-center">
                                <svg class="mx-auto h-8 w-8 text-red-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <h3 class="text-sm font-medium text-red-800 mb-1">Gagal Memuat Data</h3>
                                <p class="text-sm text-red-600 mb-3" id="error-message"></p>
                                <button type="button" onclick="loadKontainerData()" class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    Coba Lagi
                                </button>
                            </div>
                        </div>

                        @error('kontainer_ids')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>                    <!-- Keterangan -->
                    <div class="space-y-1">
                        <label for="keterangan" class="block text-sm font-medium text-gray-700">
                            Keterangan
                        </label>
                        <textarea name="keterangan" id="keterangan" rows="3"
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                  placeholder="Masukkan keterangan tambahan (opsional)..." maxlength="500">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Total Calculation Section -->
                    <div id="total-section" class="space-y-3 p-4 bg-gray-50 border border-gray-200 rounded-md hidden">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-medium text-gray-700">Ringkasan Tarif</h4>
                            <button type="button" id="refresh-total" class="text-blue-600 hover:text-blue-800 text-sm">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Refresh
                            </button>
                        </div>

                        <!-- Single Biaya Display -->
                        <div id="single-biaya-section" class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Jumlah Kontainer:</span>
                                <span id="kontainer-count" class="font-medium">0</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Harga per Kontainer:</span>
                                <span id="unit-price" class="font-medium">-</span>
                            </div>
                            <div class="border-t pt-2 flex justify-between">
                                <span class="font-medium text-gray-900">Total Tarif:</span>
                                <span id="total-price" class="font-bold text-lg text-blue-600">Rp 0</span>
                            </div>
                        </div>

                        <!-- Multiple Biaya Display (Proforma Style) -->
                        <div id="multiple-biaya-section" class="space-y-3 hidden">
                            <!-- Aktivitas Table -->
                            <div>
                                <h5 class="text-sm font-medium text-gray-800 mb-2">Aktivitas</h5>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full text-xs border border-gray-300">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th class="px-2 py-1 text-left border-r border-gray-300">Aktivitas</th>
                                                <th class="px-2 py-1 text-center border-r border-gray-300">S/T/S</th>
                                                <th class="px-2 py-1 text-center border-r border-gray-300">Box</th>
                                                <th class="px-2 py-1 text-center border-r border-gray-300">Itm</th>
                                                <th class="px-2 py-1 text-right border-r border-gray-300">Tarif</th>
                                                <th class="px-2 py-1 text-right">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody id="aktivitas-table-body">
                                            <!-- Data will be populated by JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Petikemas Summary -->
                            <div>
                                <h5 class="text-sm font-medium text-gray-800 mb-2">Detail Petikemas</h5>
                                <div class="text-xs text-gray-600 mb-2">
                                    <span>No. Petikemas: <span id="petikemas-count">0</span> | </span>
                                    <span>Total Petikemas: <span id="petikemas-total-count">0</span></span>
                                </div>
                            </div>

                            <!-- Grand Total -->
                            <div class="border-t pt-2 space-y-1">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Sub - Total:</span>
                                    <span id="subtotal-price" class="font-medium">Rp 0</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">PPN (11%):</span>
                                    <span id="ppn-price" class="font-medium">Rp 0</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">PPH (2%):</span>
                                    <span id="pph-price" class="font-medium text-red-600">-Rp 0</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Materai:</span>
                                    <span id="materai-price" class="font-medium">Rp 0</span>
                                </div>
                                <div class="border-t pt-1 flex justify-between">
                                    <span class="font-bold text-gray-900">Grand Total:</span>
                                    <span id="grand-total-price" class="font-bold text-lg text-blue-600">Rp 0</span>
                                </div>
                            </div>
                        </div>

                        <!-- Breakdown Details -->
                        <div id="breakdown-details" class="text-xs text-gray-500 border-t pt-2 hidden">
                            <div class="grid grid-cols-2 gap-2">
                                <div>Tanggal: <span id="breakdown-tanggal">-</span></div>
                                <div>Pelabuhan: <span id="breakdown-pelabuhan">-</span></div>
                                <div>Kegiatan: <span id="breakdown-kegiatan">-</span></div>
                                <div>Gudang: <span id="breakdown-gudang">-</span></div>
                                <div>Kontainer: <span id="breakdown-kontainer">-</span></div>
                                <div>Muatan: <span id="breakdown-muatan">-</span></div>
                            </div>
                        </div>

                        <!-- Loading State -->
                        <div id="total-loading" class="hidden">
                            <div class="flex items-center justify-center py-2">
                                <svg class="animate-spin h-4 w-4 text-blue-600 mr-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span class="text-sm text-gray-600">Menghitung total...</span>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                        <button type="button" onclick="resetForm()" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Reset
                        </button>
                        <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed" id="submit-button">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span id="submit-text">Simpan Gate In</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// State management
let isLoading = false;
let kontainerData = [];
let filteredKontainerData = [];
let currentSearchTerm = '';
let currentSizeFilter = '';

// DOM Ready
$(document).ready(function() {
    // Setup CSRF token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Handle kegiatan dropdown change to filter dropdown options
    $('#kegiatan').on('change', function() {
        const selectedKegiatan = $(this).val();
        const gudangDropdown = $('#gudang');
        const kontainerDropdown = $('#kontainer');
        const muatanDropdown = $('#muatan');

        if (!selectedKegiatan) {
            // Reset all dropdowns if no kegiatan selected
            gudangDropdown.html('<option value="">Pilih Gudang</option>');
            kontainerDropdown.html('<option value="">Pilih Kontainer</option>');
            muatanDropdown.html('<option value="">Pilih Muatan</option>');
            return;
        }

        // Show loading state for gudang
        gudangDropdown.prop('disabled', true);
        gudangDropdown.html('<option value="">Memuat gudang...</option>');

        // Fetch gudang options based on selected kegiatan
        $.ajax({
            url: '{{ route("gate-in.get-gudang-by-kegiatan") }}',
            method: 'GET',
            data: { kegiatan: selectedKegiatan },
            success: function(response) {
                let options = '<option value="">Pilih Gudang</option>';

                if (response && response.gudangs && response.gudangs.length > 0) {
                    response.gudangs.forEach(function(gudang) {
                        options += `<option value="${gudang}">${gudang}</option>`;
                    });
                }

                gudangDropdown.html(options);
                gudangDropdown.prop('disabled', false);
            },
            error: function(xhr, status, error) {
                console.error('Error loading gudang options:', xhr.status, error);
                gudangDropdown.html('<option value="">Error memuat gudang</option>');
                gudangDropdown.prop('disabled', false);
                showAlert('error', 'Gagal memuat opsi gudang. Silakan coba lagi.');
            }
        });

        // Show loading state for kontainer
        kontainerDropdown.prop('disabled', true);
        kontainerDropdown.html('<option value="">Memuat kontainer...</option>');

        // Fetch kontainer options based on selected kegiatan
        $.ajax({
            url: '{{ route("gate-in.get-kontainer-by-kegiatan") }}',
            method: 'GET',
            data: { kegiatan: selectedKegiatan },
            success: function(response) {
                let options = '<option value="">Pilih Kontainer</option>';

                if (response && response.kontainers && response.kontainers.length > 0) {
                    response.kontainers.forEach(function(kontainer) {
                        options += `<option value="${kontainer}">${kontainer}ft</option>`;
                    });
                }

                kontainerDropdown.html(options);
                kontainerDropdown.prop('disabled', false);
            },
            error: function(xhr, status, error) {
                console.error('Error loading kontainer options:', xhr.status, error);
                kontainerDropdown.html('<option value="">Error memuat kontainer</option>');
                kontainerDropdown.prop('disabled', false);
                showAlert('error', 'Gagal memuat opsi kontainer. Silakan coba lagi.');
            }
        });

        // Show loading state for muatan
        muatanDropdown.prop('disabled', true);
        muatanDropdown.html('<option value="">Memuat muatan...</option>');

        // Fetch muatan options based on selected kegiatan
        $.ajax({
            url: '{{ route("gate-in.get-muatan-by-kegiatan") }}',
            method: 'GET',
            data: { kegiatan: selectedKegiatan },
            success: function(response) {
                let options = '<option value="">Pilih Muatan</option>';

                if (response && response.muatans && response.muatans.length > 0) {
                    response.muatans.forEach(function(muatan) {
                        options += `<option value="${muatan}">${muatan}</option>`;
                    });
                }

                muatanDropdown.html(options);
                muatanDropdown.prop('disabled', false);
            },
            error: function(xhr, status, error) {
                console.error('Error loading muatan options:', xhr.status, error);
                muatanDropdown.html('<option value="">Error memuat muatan</option>');
                muatanDropdown.prop('disabled', false);
                showAlert('error', 'Gagal memuat opsi muatan. Silakan coba lagi.');
            }
        });
    });

    // Handle kontainer dropdown change to filter kontainer list
    $('#kontainer').on('change', function() {
        const selectedKontainerSize = $(this).val();
        console.log('Kontainer size selected:', selectedKontainerSize);
        currentSizeFilter = selectedKontainerSize;

        if (selectedKontainerSize) {
            // Reload kontainer data with size filter
            loadKontainerData(selectedKontainerSize);
        } else {
            // Load all kontainer data
            loadKontainerData();
        }
    });

    // Handle muatan dropdown change to filter kontainer list
    $('#muatan').on('change', function() {
        const selectedMuatan = $(this).val();
        console.log('Muatan selected:', selectedMuatan);

        // Filter kontainer data based on muatan selection
        filterKontainerByMuatan(selectedMuatan);
    });

    // Handle search input events
    $('#kontainer-search').on('input', function() {
        currentSearchTerm = $(this).val().toLowerCase();
        console.log('Search term:', currentSearchTerm);

        // Show/hide clear button
        if (currentSearchTerm.length > 0) {
            $('#clear-search').show();
        } else {
            $('#clear-search').hide();
        }

        // Filter and render data
        filterAndRenderKontainerData();
    });

    // Handle clear search button
    $('#clear-search').on('click', function() {
        $('#kontainer-search').val('');
        currentSearchTerm = '';
        $(this).hide();
        filterAndRenderKontainerData();
    });

    // Handle search input focus/blur for better UX
    $('#kontainer-search').on('focus', function() {
        $(this).parent().addClass('ring-2 ring-blue-500');
    }).on('blur', function() {
        $(this).parent().removeClass('ring-2 ring-blue-500');
    });

    // Load kontainer data on page load
    loadKontainerData();

    // Form validation on submit
    $('#gate-in-form').on('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
            return false;
        }

        // Show loading state
        const submitBtn = $('#submit-button');
        const submitText = $('#submit-text');

        submitBtn.prop('disabled', true);
        submitText.html('<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Menyimpan...');

        // Re-enable after timeout as fallback
        setTimeout(() => {
            submitBtn.prop('disabled', false);
            submitText.text('Simpan Gate In');
        }, 15000);
    });
});

// Load kontainer data from checkpoint supir
function loadKontainerData(kontainerSize = null) {
    if (isLoading) return;

    isLoading = true;

    // Show loading state
    $('#loading-kontainer').removeClass('hidden');
    $('#kontainer-container').addClass('hidden');
    $('#error-kontainer').addClass('hidden');

    const logMessage = kontainerSize ?
        `Loading kontainer data from checkpoint supir with size filter: ${kontainerSize}` :
        'Loading kontainer data from checkpoint supir...';

    console.log(logMessage);

    const ajaxData = {};
    if (kontainerSize) {
        ajaxData.kontainer_size = kontainerSize;
    }

    $.ajax({
        url: '{{ route("gate-in.get-kontainers-surat-jalan") }}',
        method: 'GET',
        data: ajaxData,
        timeout: 30000,
        success: function(response) {
            const filterText = kontainerSize ? ` (filtered by size ${kontainerSize})` : '';
            console.log(`Kontainer data loaded: ${response.length} items${filterText}`);

            // Debug: Check if kegiatan_surat_jalan field is available
            if (response.length > 0) {
                console.log('Sample kontainer data:', response[0]);
                console.log('Available fields:', Object.keys(response[0]));

                // Count items with kegiatan_surat_jalan
                const withKegiatan = response.filter(k => k.kegiatan_surat_jalan);
                console.log(`Items with kegiatan_surat_jalan: ${withKegiatan.length}`);

                // Show specific kegiatan values
                withKegiatan.forEach(k => {
                    console.log(`  - ${k.no_surat_jalan}: kegiatan = '${k.kegiatan_surat_jalan}'`);
                });
            }

            kontainerData = response;
            filterAndRenderKontainerData();

            // Show search container if data is available
            if (response.length > 0) {
                $('#search-container').show();
            } else {
                $('#search-container').hide();
            }

            // Hide loading, show content
            $('#loading-kontainer').addClass('hidden');
            $('#kontainer-container').removeClass('hidden');

            isLoading = false;
        },
        error: function(xhr, status, error) {
            console.error('Error loading kontainer data:', xhr.status, error);

            let errorMessage = 'Terjadi kesalahan saat memuat data kontainer.';

            if (xhr.status === 0) {
                errorMessage = status === 'timeout' ?
                    'Request timeout. Server membutuhkan waktu terlalu lama.' :
                    'Koneksi terputus. Periksa koneksi internet Anda.';
            } else if (xhr.status === 404) {
                errorMessage = 'Endpoint tidak ditemukan (404).';
            } else if (xhr.status === 500) {
                errorMessage = 'Terjadi kesalahan server (500).';
            } else if (xhr.status === 403) {
                errorMessage = 'Akses ditolak. Anda tidak memiliki permission.';
            }

            // Show error state
            $('#loading-kontainer').addClass('hidden');
            $('#kontainer-container').addClass('hidden');
            $('#error-kontainer').removeClass('hidden');
            $('#error-message').text(errorMessage);
            $('#search-container').hide();
            $('#search-info').hide();

            showAlert('error', errorMessage);
            isLoading = false;
        }
    });
}

// Render kontainer data to UI
function renderKontainerData(data) {
    const container = $('#kontainer-container');
    const selectedKontainerSize = $('#kontainer').val();

    if (data.length === 0) {
        let emptyMessage;
        let actionButtons = '';

        if (currentSearchTerm && currentSearchTerm.trim() !== '') {
            emptyMessage = `Tidak ditemukan kontainer yang sesuai dengan pencarian "${currentSearchTerm}".`;
            actionButtons = `
                <button type="button" onclick="$('#clear-search').click()" class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 mr-2">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Hapus Pencarian
                </button>`;
        } else if ($('#muatan').val() === 'FULL') {
            emptyMessage = `Tidak ada kontainer dengan muatan FULL (ANTAR ISI & TARIK ISI) yang tersedia untuk gate in.`;
            actionButtons = `
                <button type="button" onclick="$('#muatan').val('').trigger('change')" class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 mr-2">
                    Reset Filter Muatan
                </button>`;
        } else if ($('#muatan').val() === 'EMPTY') {
            emptyMessage = `Tidak ada kontainer dengan muatan EMPTY (ANTAR KOSONG & TARIK KOSONG) yang tersedia untuk gate in.`;
            actionButtons = `
                <button type="button" onclick="$('#muatan').val('').trigger('change')" class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 mr-2">
                    Reset Filter Muatan
                </button>`;
        } else if (selectedKontainerSize) {
            emptyMessage = `Tidak ada kontainer dengan size ${selectedKontainerSize}ft yang tersedia untuk gate in.`;
            actionButtons = `
                <button type="button" onclick="$('#kontainer').val('').trigger('change')" class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 mr-2">
                    Reset Filter
                </button>`;
        } else {
            emptyMessage = 'Belum ada kontainer dari checkpoint supir yang tersedia untuk gate in.';
        }

        container.html(`
            <div class="text-center py-6">
                <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <h3 class="text-sm font-medium text-gray-900 mb-1">Tidak Ada Data</h3>
                <p class="text-sm text-gray-500 mb-3">${emptyMessage}</p>
                <div>
                    ${actionButtons}
                    <button type="button" onclick="loadKontainerData(${selectedKontainerSize ? `'${selectedKontainerSize}'` : ''})" class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Refresh
                    </button>
                </div>
            </div>
        `);
        return;
    }    let html = '<div class="p-3">';

    // Show filter info if filtering is active
    if (selectedKontainerSize) {
        html += `<div class="mb-3 p-2 bg-blue-50 border border-blue-200 rounded-md">
            <div class="flex items-center justify-between">
                <span class="text-sm text-blue-700">Filter: Kontainer ${selectedKontainerSize}ft (${data.length} item${data.length > 1 ? 's' : ''})</span>
                <button type="button" onclick="$('#kontainer').val('').trigger('change')" class="text-blue-600 hover:text-blue-800 text-sm">
                    Reset
                </button>
            </div>
        </div>`;
    }

    html += '<div class="space-y-2">';

    data.forEach((kontainer, index) => {
        // Highlight search terms if search is active
        const nomorKontainer = currentSearchTerm ?
            highlightSearchTerm(kontainer.nomor_kontainer || 'N/A', currentSearchTerm) :
            (kontainer.nomor_kontainer || 'N/A');
        const noSuratJalan = currentSearchTerm ?
            highlightSearchTerm(kontainer.no_surat_jalan, currentSearchTerm) :
            kontainer.no_surat_jalan;
        const supirNama = currentSearchTerm ?
            highlightSearchTerm(kontainer.supir_nama || 'N/A', currentSearchTerm) :
            (kontainer.supir_nama || 'N/A');
        const noPlat = kontainer.no_plat ?
            (currentSearchTerm ? highlightSearchTerm(kontainer.no_plat, currentSearchTerm) : kontainer.no_plat) : '';
        const tujuanPengiriman = kontainer.tujuan_pengiriman ?
            (currentSearchTerm ? highlightSearchTerm(kontainer.tujuan_pengiriman, currentSearchTerm) : kontainer.tujuan_pengiriman) : '';
        const kegiatanSuratJalan = kontainer.kegiatan_surat_jalan ?
            (currentSearchTerm ? highlightSearchTerm(kontainer.kegiatan_surat_jalan, currentSearchTerm) : kontainer.kegiatan_surat_jalan) : '';

        html += `
            <div class="flex items-start p-3 border border-gray-200 rounded-md bg-white hover:bg-gray-50">
                <input type="checkbox" name="kontainer_ids[]" value="${kontainer.id}" id="kontainer_${index}"
                       class="mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <label for="kontainer_${index}" class="ml-3 flex-1 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div class="font-medium text-gray-900">${nomorKontainer}</div>
                        <div class="flex items-center space-x-2">
                            ${kegiatanSuratJalan ? `<span class="text-xs px-2 py-1 rounded-full ${
                                kegiatanSuratJalan.includes('ANTAR ISI') || kegiatanSuratJalan.includes('TARIK ISI') ?
                                'bg-green-100 text-green-800' :
                                kegiatanSuratJalan.includes('ANTAR KOSONG') || kegiatanSuratJalan.includes('TARIK KOSONG') ?
                                'bg-blue-100 text-blue-800' :
                                'bg-gray-100 text-gray-800'
                            }">${kegiatanSuratJalan}</span>` : ''}
                            <div class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">${kontainer.size || '20'}ft</div>
                        </div>
                    </div>
                    <div class="mt-1 text-sm text-gray-600">
                        <div class="grid grid-cols-2 gap-2">
                            <span><strong>Surat Jalan:</strong> ${noSuratJalan}</span>
                            <span><strong>Supir:</strong> ${supirNama}</span>
                            ${kontainer.no_plat ? `<span><strong>Plat:</strong> ${noPlat}</span>` : ''}
                            ${kontainer.tujuan_pengiriman ? `<span><strong>Tujuan:</strong> ${tujuanPengiriman}</span>` : ''}
                        </div>
                    </div>
                </label>
            </div>
        `;
    });

    html += '</div></div>';
    container.html(html);
}

// Filter and render kontainer data based on search term
function filterAndRenderKontainerData() {
    let dataToRender = kontainerData;

    // Apply muatan filter first if FULL or EMPTY is selected
    const selectedMuatan = $('#muatan').val();
    if (selectedMuatan === 'FULL') {
        dataToRender = kontainerData.filter(function(kontainer) {
            if (kontainer.kegiatan_surat_jalan) {
                const kegiatan = kontainer.kegiatan_surat_jalan.toUpperCase();
                return kegiatan === 'ANTAR ISI' || kegiatan === 'TARIK ISI';
            }
            return false;
        });
    } else if (selectedMuatan === 'EMPTY') {
        dataToRender = kontainerData.filter(function(kontainer) {
            if (kontainer.kegiatan_surat_jalan) {
                const kegiatan = kontainer.kegiatan_surat_jalan.toUpperCase();
                return kegiatan === 'ANTAR KOSONG' || kegiatan === 'TARIK KOSONG';
            }
            return false;
        });
    }

    // Apply search filter if search term exists
    if (currentSearchTerm && currentSearchTerm.trim() !== '') {
        dataToRender = dataToRender.filter(function(kontainer) {
            const searchTerm = currentSearchTerm.toLowerCase();

            // Search in multiple fields
            const nomorKontainer = (kontainer.nomor_kontainer || '').toLowerCase();
            const noSuratJalan = (kontainer.no_surat_jalan || '').toLowerCase();
            const supirNama = (kontainer.supir_nama || '').toLowerCase();
            const noPlat = (kontainer.no_plat || '').toLowerCase();
            const tujuanPengiriman = (kontainer.tujuan_pengiriman || '').toLowerCase();
            const size = (kontainer.size || '').toString().toLowerCase();
            const kegiatanSuratJalan = (kontainer.kegiatan_surat_jalan || '').toLowerCase();

            return nomorKontainer.includes(searchTerm) ||
                   noSuratJalan.includes(searchTerm) ||
                   supirNama.includes(searchTerm) ||
                   noPlat.includes(searchTerm) ||
                   tujuanPengiriman.includes(searchTerm) ||
                   size.includes(searchTerm) ||
                   kegiatanSuratJalan.includes(searchTerm);
        });
    }

    // Store filtered data
    filteredKontainerData = dataToRender;

    // Update search info
    updateSearchInfo(dataToRender.length);

    // Render the filtered data
    renderKontainerData(dataToRender);
}// Update search information display
function updateSearchInfo(resultCount) {
    const searchInfo = $('#search-info');
    const selectedMuatan = $('#muatan').val();

    let infoText = '';

    if (selectedMuatan === 'FULL') {
        const totalCount = kontainerData.length;
        infoText = `Filter: FULL muatan (ANTAR ISI & TARIK ISI) - ${resultCount} dari ${totalCount} kontainer`;

        if (currentSearchTerm && currentSearchTerm.trim() !== '') {
            infoText += ` | Pencarian: "${currentSearchTerm}"`;
        }

        $('#search-results-count').text(infoText);
        searchInfo.show();
    } else if (currentSearchTerm && currentSearchTerm.trim() !== '') {
        const totalCount = kontainerData.length;
        const searchText = resultCount === totalCount ?
            `Menampilkan semua ${totalCount} kontainer` :
            `Menampilkan ${resultCount} dari ${totalCount} kontainer`;

        $('#search-results-count').text(searchText);
        searchInfo.show();
    } else {
        searchInfo.hide();
    }
}

// Highlight search terms in text
function highlightSearchTerm(text, searchTerm) {
    if (!searchTerm || searchTerm.trim() === '') {
        return text;
    }

    const regex = new RegExp(`(${searchTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
    return text.replace(regex, '<span class="bg-yellow-200 font-medium">$1</span>');
}

// Filter kontainer by muatan selection
function filterKontainerByMuatan(selectedMuatan) {
    console.log('Filtering kontainer by muatan:', selectedMuatan);
    console.log('Current kontainerData count:', kontainerData.length);

    if (selectedMuatan === 'FULL') {
        // Debug: Check kegiatan data before filtering
        console.log('Checking kegiatan data:');
        kontainerData.forEach(function(kontainer, index) {
            console.log(`  [${index}] ${kontainer.no_surat_jalan}: kegiatan_surat_jalan = '${kontainer.kegiatan_surat_jalan}'`);
        });

        // Filter kontainer that have surat jalan with ANTAR ISI or TARIK ISI activities
        const filteredData = kontainerData.filter(function(kontainer) {
            // Check if kontainer has kegiatan_surat_jalan property
            if (kontainer.kegiatan_surat_jalan) {
                const kegiatan = kontainer.kegiatan_surat_jalan.toUpperCase();
                const matches = kegiatan === 'ANTAR ISI' || kegiatan === 'TARIK ISI';
                console.log(`    - ${kontainer.no_surat_jalan}: '${kegiatan}' matches FULL filter: ${matches}`);
                return matches;
            }
            console.log(`    - ${kontainer.no_surat_jalan}: no kegiatan_surat_jalan field`);
            return false;
        });

        console.log(`Filtered ${filteredData.length} kontainer with FULL muatan (ANTAR ISI/TARIK ISI activities)`);

        // Update the display with filtered data
        updateKontainerDisplay(filteredData, 'FULL muatan (ANTAR ISI & TARIK ISI)');
    } else if (selectedMuatan === 'EMPTY') {
        // Debug: Check kegiatan data before filtering
        console.log('Checking kegiatan data for EMPTY:');
        kontainerData.forEach(function(kontainer, index) {
            console.log(`  [${index}] ${kontainer.no_surat_jalan}: kegiatan_surat_jalan = '${kontainer.kegiatan_surat_jalan}'`);
        });

        // Filter kontainer that have surat jalan with ANTAR KOSONG or TARIK KOSONG activities
        const filteredData = kontainerData.filter(function(kontainer) {
            // Check if kontainer has kegiatan_surat_jalan property
            if (kontainer.kegiatan_surat_jalan) {
                const kegiatan = kontainer.kegiatan_surat_jalan.toUpperCase();
                const matches = kegiatan === 'ANTAR KOSONG' || kegiatan === 'TARIK KOSONG';
                console.log(`    - ${kontainer.no_surat_jalan}: '${kegiatan}' matches EMPTY filter: ${matches}`);
                return matches;
            }
            console.log(`    - ${kontainer.no_surat_jalan}: no kegiatan_surat_jalan field`);
            return false;
        });

        console.log(`Filtered ${filteredData.length} kontainer with EMPTY muatan (ANTAR KOSONG/TARIK KOSONG activities)`);

        // Update the display with filtered data
        updateKontainerDisplay(filteredData, 'EMPTY muatan (ANTAR KOSONG & TARIK KOSONG)');
    } else {
        // Show all kontainer for other muatan types or when no muatan selected
        console.log('Showing all kontainer (no FULL/EMPTY filter applied)');
        filterAndRenderKontainerData();
    }
}

// Update kontainer display with filtered data
function updateKontainerDisplay(data, filterDescription) {
    // Store the filtered data temporarily
    const originalData = kontainerData;
    kontainerData = data;

    // Apply search filter if active
    filterAndRenderKontainerData();

    // Show filter info
    if (data.length === 0) {
        $('#kontainer-container').html(`
            <div class="text-center py-6">
                <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <h3 class="text-sm font-medium text-gray-900 mb-1">Tidak Ada Data</h3>
                <p class="text-sm text-gray-500 mb-3">Tidak ada kontainer dengan ${filterDescription} yang tersedia.</p>
                <div>
                    <button type="button" onclick="$('#muatan').val('').trigger('change')" class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 mr-2">
                        Reset Filter Muatan
                    </button>
                    <button type="button" onclick="loadKontainerData()" class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Refresh
                    </button>
                </div>
            </div>
        `);
    } else {
        // Update search info to show filter applied
        updateSearchInfoWithFilter(data.length, originalData.length, filterDescription);
    }

    // Restore original data for other operations
    kontainerData = originalData;
}

// Update search info with filter information
function updateSearchInfoWithFilter(filteredCount, totalCount, filterDescription) {
    const searchInfo = $('#search-info');
    let infoText = `Filter: ${filterDescription} - ${filteredCount} dari ${totalCount} kontainer`;

    if (currentSearchTerm && currentSearchTerm.trim() !== '') {
        infoText += ` | Pencarian: "${currentSearchTerm}"`;
    }

    $('#search-results-count').text(infoText);
    searchInfo.show();
}// Form validation
function validateForm() {
    const errors = [];

    // Check nomor gate in
    const nomorGateIn = $('#nomor_gate_in').val().trim();
    if (!nomorGateIn) {
        errors.push('Nomor Gate In harus diisi');
    } else if (nomorGateIn.length > 20) {
        errors.push('Nomor Gate In maksimal 20 karakter');
    }

    // Check tanggal gate in
    if (!$('#tanggal_gate_in').val()) {
        errors.push('Tanggal Gate In harus diisi');
    }

    // Check pelabuhan
    if (!$('#pelabuhan').val()) {
        errors.push('Pelabuhan harus dipilih');
    }

    // Check kegiatan
    if (!$('#kegiatan').val()) {
        errors.push('Kegiatan harus dipilih');
    }

    // Check gudang
    if (!$('#gudang').val()) {
        errors.push('Gudang harus dipilih');
    }

    // Check kontainer
    if (!$('#kontainer').val()) {
        errors.push('Kontainer harus dipilih');
    }

    // Check muatan
    if (!$('#muatan').val()) {
        errors.push('Muatan harus dipilih');
    }

    // Check kapal
    if (!$('#kapal_id').val()) {
        errors.push('Kapal harus dipilih');
    }

    // Check kontainer selection
    const selectedKontainers = $('input[name="kontainer_ids[]"]:checked').length;
    if (selectedKontainers === 0) {
        errors.push('Pilih minimal satu kontainer');
    }

    // Check keterangan length
    const keterangan = $('#keterangan').val();
    if (keterangan.length > 500) {
        errors.push('Keterangan maksimal 500 karakter');
    }

    if (errors.length > 0) {
        showAlert('error', errors.join(', '));
        return false;
    }

    return true;
}

// Reset form
function resetForm() {
    if (confirm('Apakah Anda yakin ingin mereset form? Semua data yang sudah diisi akan hilang.')) {
        $('#gate-in-form')[0].reset();

        // Reset search state
        currentSearchTerm = '';
        currentSizeFilter = '';
        $('#kontainer-search').val('');
        $('#clear-search').hide();
        $('#search-info').hide();

        // Reset all dropdown filters
        $('#gudang').html('<option value="">Pilih Gudang</option>');
        $('#kontainer').html('<option value="">Pilih Kontainer</option>');
        $('#muatan').html('<option value="">Pilih Muatan</option>');

        loadKontainerData();
        showAlert('info', 'Form telah direset');
    }
}

// Show alert messages
function showAlert(type, message) {
    // Remove existing alerts
    $('.custom-alert').remove();

    const alertClass = {
        'success': 'bg-green-100 border-green-500 text-green-700',
        'error': 'bg-red-100 border-red-500 text-red-700',
        'warning': 'bg-yellow-100 border-yellow-500 text-yellow-700',
        'info': 'bg-blue-100 border-blue-500 text-blue-700'
    };

    const iconSvg = {
        'success': '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>',
        'error': '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>',
        'warning': '<path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>',
        'info': '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>'
    };

    const alertDiv = $(`
        <div class="custom-alert fixed top-4 right-4 z-50 p-4 rounded-md shadow-lg max-w-md border-l-4 ${alertClass[type] || alertClass.info}">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        ${iconSvg[type] || iconSvg.info}
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium">${message}</p>
                </div>
                <div class="ml-auto pl-3">
                    <button type="button" class="inline-flex rounded-md p-1.5 hover:bg-opacity-20 focus:outline-none" onclick="$(this).closest('.custom-alert').remove()">
                        <span class="sr-only">Tutup</span>
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    `);

    $('body').append(alertDiv);

    // Auto remove after 5 seconds
    setTimeout(() => {
        alertDiv.fadeOut(300, function() {
            $(this).remove();
        });
    }, 5000);
}

// Calculate total based on form inputs and selected kontainers
function calculateTotal() {
    const pelabuhan = $('#pelabuhan').val();
    const kegiatan = $('#kegiatan').val();
    const gudang = $('#gudang').val();
    const kontainer = $('#kontainer').val();
    const muatan = $('#muatan').val();

    // Get selected kontainer IDs
    const selectedKontainers = [];
    $('input[name="kontainer_ids[]"]:checked').each(function() {
        selectedKontainers.push($(this).val());
    });

    // If no required fields or kontainers selected, hide total section
    if (!pelabuhan || !kegiatan || selectedKontainers.length === 0) {
        $('#total-section').addClass('hidden');
        return;
    }

    // Show loading state
    $('#total-loading').removeClass('hidden');
    $('#total-section').removeClass('hidden');

    $.ajax({
        url: '{{ route("gate-in.calculate-total") }}',
        method: 'GET',
        data: {
            pelabuhan: pelabuhan,
            kegiatan: kegiatan,
            gudang: gudang,
            kontainer: kontainer,
            muatan: muatan,
            kontainer_ids: selectedKontainers
        },
        success: function(response) {
            console.log('Calculate total response:', response);

            // Check if this is multiple biaya response
            if (response.is_multiple_biaya) {
                // Hide single biaya section, show multiple biaya section
                $('#single-biaya-section').addClass('hidden');
                $('#multiple-biaya-section').removeClass('hidden');

                // Update basic counts
                $('#kontainer-count').text(response.kontainer_count);
                $('#petikemas-count').text(response.kontainer_count);
                $('#petikemas-total-count').text(response.kontainer_count);

                // Populate aktivitas table
                populateAktivitasTable(response.aktivitas_details || []);

                // Update totals
                $('#subtotal-price').text(response.formatted_subtotal || 'Rp 0');
                $('#ppn-price').text(response.formatted_ppn || 'Rp 0');
                $('#pph-price').text('-' + (response.formatted_pph || 'Rp 0'));
                $('#materai-price').text(response.formatted_materai || 'Rp 0');
                $('#grand-total-price').text(response.formatted_total);

            } else {
                // Show single biaya section, hide multiple biaya section
                $('#single-biaya-section').removeClass('hidden');
                $('#multiple-biaya-section').addClass('hidden');

                // Update single biaya display
                $('#kontainer-count').text(response.kontainer_count);
                $('#unit-price').text(response.formatted_unit_price || 'Rp 0');
                $('#total-price').text(response.formatted_total);
            }

            // Update breakdown (common for both)
            if (response.breakdown) {
                $('#breakdown-tanggal').text($('#tanggal_gate_in').val() ? new Date($('#tanggal_gate_in').val()).toLocaleString('id-ID') : '-');
                $('#breakdown-pelabuhan').text(response.breakdown.pelabuhan || '-');
                $('#breakdown-kegiatan').text(response.breakdown.kegiatan || '-');
                $('#breakdown-gudang').text(response.breakdown.gudang || '-');
                $('#breakdown-kontainer').text(response.breakdown.kontainer || '-');
                $('#breakdown-muatan').text(response.breakdown.muatan || '-');
                $('#breakdown-details').removeClass('hidden');
            }

            // Hide loading
            $('#total-loading').addClass('hidden');

            // Show error if any
            if (response.error) {
                console.warn('Total calculation warning:', response.error);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error calculating total:', error);

            // Show error state
            $('#total-price').text('Error dalam perhitungan');
            $('#unit-price').text('-');
            $('#kontainer-count').text(selectedKontainers.length);
            $('#breakdown-details').addClass('hidden');
            $('#total-loading').addClass('hidden');
        }
    });
}

// Event handlers for total calculation
$(document).ready(function() {
    // Calculate total when form inputs change
    $('#tanggal_gate_in, #pelabuhan, #kegiatan, #gudang, #kontainer, #muatan').on('change', function() {
        calculateTotal();
    });

    // Calculate total when kontainer selection changes
    $(document).on('change', 'input[name="kontainer_ids[]"]', function() {
        calculateTotal();
    });

    // Refresh total button
    $('#refresh-total').on('click', function() {
        calculateTotal();
    });
});

// Function to populate aktivitas table for multiple biaya
function populateAktivitasTable(aktivitasDetails) {
    const tbody = $('#aktivitas-table-body');
    tbody.empty();

    if (!aktivitasDetails || aktivitasDetails.length === 0) {
        tbody.append(`
            <tr>
                <td colspan="6" class="px-2 py-2 text-center text-gray-500 text-xs">
                    Tidak ada data aktivitas
                </td>
            </tr>
        `);
        return;
    }

    aktivitasDetails.forEach(function(aktivitas) {
        const row = `
            <tr class="border-b border-gray-200">
                <td class="px-2 py-1 text-left border-r border-gray-200 text-xs font-medium">${aktivitas.aktivitas}</td>
                <td class="px-2 py-1 text-center border-r border-gray-200 text-xs">${aktivitas.s_t_s}</td>
                <td class="px-2 py-1 text-center border-r border-gray-200 text-xs">${aktivitas.box}</td>
                <td class="px-2 py-1 text-center border-r border-gray-200 text-xs">${aktivitas.itm}</td>
                <td class="px-2 py-1 text-right border-r border-gray-200 text-xs font-mono">${aktivitas.formatted_tarif}</td>
                <td class="px-2 py-1 text-right text-xs font-mono font-medium">${aktivitas.formatted_total}</td>
            </tr>
        `;
        tbody.append(row);
    });
}
</script>
@endpush
