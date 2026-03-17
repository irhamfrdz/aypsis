@extends('layouts.app')

@section('title', 'Tambah Pembatalan Surat Jalan')
@section('page_title', 'Tambah Pembatalan Surat Jalan')

@section('content')
    <div class="bg-white shadow-lg rounded-lg p-4 max-w-6xl mx-auto">
        @php
            $inputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-sm p-2 transition-colors";
            $readonlyInputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm text-sm p-2";
            $labelClasses = "block text-xs font-medium text-gray-700 mb-1";
        @endphp

        <!-- Session Message Triggers -->
        @if(session('success'))
            <div class="mb-3 p-3 rounded-lg bg-green-50 border border-green-200 text-green-800 text-sm">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <strong>Berhasil!</strong> {{ session('success') }}
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-3 p-4 rounded-lg bg-red-50 border border-red-200 text-red-800 text-sm">
                <div class="flex items-start">
                    <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="flex-1">
                        <div class="font-medium mb-1">Gagal Menyimpan Pembatalan!</div>
                        <div>{{ session('error') }}</div>
                    </div>
                </div>
            </div>
        @endif
        {{-- Show validation errors --}}
        @if($errors->any())
            <div class="mb-3 p-3 rounded-lg bg-yellow-50 border border-yellow-200 text-yellow-800 text-sm">
                <strong>Terjadi kesalahan:</strong>
                <ul class="mt-1 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Header Filter -->
        <div class="bg-gray-50 rounded-lg p-3 mb-3 border border-gray-200">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-2">
                <div>
                    <h3 class="text-sm font-semibold text-gray-800">Filter Surat Jalan</h3>
                </div>
                <form action="{{ route('pembatalan-surat-jalan.create') }}" method="GET" class="flex flex-col sm:flex-row gap-2">
                    <div class="flex gap-2">
                        <div class="min-w-0">
                            <label for="search_sj" class="{{ $labelClasses }}">Nomor Surat Jalan</label>
                            <input type="text" name="search_sj" id="search_sj" class="mt-1 block w-full rounded-md border-gray-300 bg-white shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-sm p-2 transition-colors" value="{{ request('search_sj') }}" placeholder="Cari nomer...">
                        </div>
                    </div>
                    <div class="flex gap-1 sm:self-end">
                        <button type="submit" class="inline-flex justify-center py-1.5 px-3 border border-transparent shadow-sm text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            Cari
                        </button>
                        <a href="{{ route('pembatalan-surat-jalan.create') }}" class="inline-flex justify-center py-1.5 px-3 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <form id="pembatalanForm" action="{{ route('pembatalan-surat-jalan.store') }}" method="POST" class="space-y-3">
            @csrf

            <!-- Data Pembayaran & Bank -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-3">
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">Data Pembayaran</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                            <div>
                                <label for="nomor_pembayaran" class="{{ $labelClasses }}">Nomor Pembayaran <span class="text-red-500">*</span></label>
                                <div class="flex gap-1">
                                    <input type="text" name="nomor_pembayaran" id="nomor_pembayaran"
                                        value="{{ old('nomor_pembayaran') }}"
                                        class="{{ $readonlyInputClasses }}" readonly required placeholder="Auto generate">
                                    <button type="button" id="generateNomorBtn" class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded transition-colors" title="Generate nomor pembayaran">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label for="nomor_accurate" class="{{ $labelClasses }}">Nomor Accurate</label>
                                <input type="text" name="nomor_accurate" id="nomor_accurate"
                                    value="{{ old('nomor_accurate') }}"
                                    class="{{ $inputClasses }}" placeholder="Masukkan nomor accurate">
                            </div>
                            <div>
                                <label for="tanggal_kas" class="{{ $labelClasses }}">Tanggal Kas</label>
                                <input type="date" name="tanggal_kas" id="tanggal_kas"
                                    value="{{ old('tanggal_kas', now()->toDateString()) }}"
                                    class="{{ $inputClasses }}" required>
                                <input type="hidden" name="tanggal_pembayaran" id="tanggal_pembayaran" value="{{ old('tanggal_pembayaran', now()->toDateString()) }}">
                            </div>
                        </div>
                        <div class="mt-2 p-2 bg-blue-50 border border-blue-200 rounded text-xs text-blue-800">
                            <div class="flex items-start">
                                <svg class="w-3 h-3 mr-1 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <strong>Format Nomor:</strong> PBL-[2 digit bulan]-[2 digit tahun]-[6 digit running number]<br>
                                    <span class="text-xs">Contoh: PBL-03-26-000001</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">Bank & Transaksi (Double Book Accounting)</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <div class="relative">
                                <label for="bank" class="{{ $labelClasses }}">Pilih Bank</label>
                                <div class="relative">
                                    <input type="text" id="bankSearch" placeholder="Cari bank..."
                                        class="mt-1 block w-full rounded-md border-gray-300 bg-white shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-sm p-2 transition-colors pr-8"
                                        autocomplete="off">
                                    <svg class="absolute right-2 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <select name="bank" id="bank" class="hidden" required>
                                    <option value="">-- Pilih Bank --</option>
                                    @if(isset($akunCoa))
                                        @foreach($akunCoa as $akun)
                                            <option value="{{ $akun->nama_akun }}" data-kode="{{ $akun->kode_nomor ?? '000' }}" {{ old('bank') == $akun->nama_akun ? 'selected' : '' }}>
                                                {{ $akun->nama_akun }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <div id="bankDropdown" class="hidden absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto">
                                    <div id="bankOptions" class="py-1"></div>
                                    <div id="noBankResults" class="hidden px-3 py-2 text-xs text-gray-500 text-center">
                                        Tidak ada bank yang sesuai
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="jenis_transaksi" class="{{ $labelClasses }}">Jenis Transaksi</label>
                                <select name="jenis_transaksi" id="jenis_transaksi" class="{{ $inputClasses }}" required>
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="Debit" {{ old('jenis_transaksi') == 'Debit' ? 'selected' : '' }}>Debit (Bank +, Biaya -)</option>
                                    <option value="Kredit" {{ old('jenis_transaksi', 'Kredit') == 'Kredit' ? 'selected' : '' }}>Kredit (Biaya +, Bank -)</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-2 p-2 bg-blue-50 border border-blue-200 rounded text-xs text-blue-800">
                            <div class="flex items-start">
                                <svg class="w-3 h-3 mr-1 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <strong>Jurnal Akuntansi:</strong><br>
                                    • <strong>Debit:</strong> Bank bertambah (Dr), Biaya Uang Jalan Muat berkurang (Cr)<br>
                                    • <strong>Kredit:</strong> Biaya Uang Jalan Muat bertambah (Dr), Bank berkurang (Cr)
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulir Pembatalan -->
            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                <h4 class="text-sm font-semibold text-gray-800 mb-2">Formulir Pembatalan</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <div>
                        <label for="alasan_batal" class="{{ $labelClasses }}">Alasan Batal <span class="text-red-500">*</span></label>
                        <textarea name="alasan_batal" id="alasan_batal" rows="3"
                            class="{{ $inputClasses }}" placeholder="Berikan alasan pembatalan..." required>{{ old('alasan_batal') }}</textarea>
                    </div>
                    <div>
                        <label class="{{ $labelClasses }}">Surat Jalan Terpilih</label>
                        <input type="text" id="selectedSjText" class="{{ $readonlyInputClasses }} font-medium text-gray-800" readonly placeholder="Belum ada yang dipilih">
                        <input type="hidden" name="surat_jalan_id" id="surat_jalan_id" required>

                        <div class="mt-2 p-2 bg-amber-50 border border-amber-200 rounded text-xs text-amber-800">
                            <div class="flex items-start">
                                <svg class="w-4 h-4 mr-1 text-amber-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <strong class="font-bold">Informasi Side Effects:</strong><br>
                                    • Status Surat Jalan otomatis menjadi <b class="text-red-700">Cancelled</b>.<br>
                                    • Status details di tabel prospek otomatis menjadi <b class="text-red-700">Batal</b>.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pilih Surat Jalan -->
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-3 py-2 border-b border-gray-200">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-sm font-semibold text-gray-800">Pilih Surat Jalan</h4>
                        <div class="flex items-center gap-2">
                            <input type="text" id="searchSjClient" placeholder="Cari no surat, pengirim..." class="px-3 py-1.5 text-xs border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 w-64">
                            <span id="searchCounter" class="text-xs text-gray-600"></span>
                        </div>
                    </div>
                    <p class="text-xs text-blue-600">
                        <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <strong>Info:</strong> Pilih satu surat jalan dari tabel, lalu simpan untuk membatalkan transaksi.
                    </p>
                </div>
                <div class="overflow-x-auto max-h-60">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0 z-20">
                            <tr>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No Surat</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengirim</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Supir</th>
                                <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Uang Jalan</th>
                            </tr>
                        </thead>
                        <tbody id="sjTableBody" class="bg-white divide-y divide-gray-200">
                            @forelse ($suratJalans as $sj)
                                @php
                                    $namaSupir = optional($sj->supirKaryawan)->nama_panggilan
                                        ?? optional($sj->supirKaryawan)->nama_lengkap
                                        ?? $sj->supir
                                        ?? '-';
                                    $uangJalanNominal = optional($sj->uangJalan)->jumlah_total
                                        ?? optional($sj->uangJalan)->subtotal
                                        ?? optional($sj->uangJalan)->jumlah_uang_jalan
                                        ?? 0;
                                @endphp
                                <tr class="sj-row hover:bg-gray-50 cursor-pointer transition-colors" data-id="{{ $sj->id }}" data-no="{{ $sj->no_surat_jalan }}" data-uang-jalan="{{ (float) $uangJalanNominal }}">
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">
                                        <input type="radio" name="sj_radio_btn" value="{{ $sj->id }}" class="sj-radio h-3 w-3 text-indigo-600 border-gray-300 pointer-events-none" data-no="{{ $sj->no_surat_jalan }}">
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs font-medium text-indigo-600">{{ $sj->no_surat_jalan ?? '-' }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">{{ $sj->created_at ? $sj->created_at->format('d/m/Y') : '-' }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">{{ $sj->pengirim ?? '-' }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">{{ $namaSupir }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-right text-xs font-semibold">Rp {{ number_format((float) $uangJalanNominal, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr id="emptyRow">
                                    <td colspan="6" class="px-2 py-4 text-center text-xs text-gray-500">Tidak ada surat jalan yang tersedia.</td>
                                </tr>
                            @endforelse
                            <tr id="noResultsRow" class="hidden">
                                <td colspan="6" class="px-2 py-4 text-center text-xs text-gray-500">Tidak ada hasil yang sesuai dengan pencarian.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="bg-gray-50 px-3 py-2 border-t border-gray-200">
                    <div class="flex items-start gap-2">
                        <p class="text-xs text-gray-600 flex-1">
                            * Gunakan pencarian lokal untuk mempercepat pemilihan surat jalan.
                        </p>
                        <div class="flex flex-col gap-1">
                            <p class="text-xs text-blue-600">
                                <span class="px-1 py-0.5 bg-blue-100 text-blue-600 rounded">Double Book</span> = Otomatis jurnal ke COA
                            </p>
                            <p class="text-xs text-red-600">
                                <span class="px-1 py-0.5 bg-red-100 text-red-700 rounded">Status</span> akan berubah menjadi Cancelled.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-3 py-2 border-t border-gray-200">
                    {{ $suratJalans->appends(request()->query())->links() }}
                </div>
            </div>

            <!-- Total Pembayaran & Informasi Tambahan -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-3">
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">Total Pembayaran</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                            <div>
                                <label for="total_pembayaran" class="{{ $labelClasses }}">Total Tagihan</label>
                                <input type="number" name="total_pembayaran" id="total_pembayaran"
                                    value="{{ old('total_pembayaran', 0) }}"
                                    class="{{ $readonlyInputClasses }}" readonly>
                            </div>
                            <div>
                                <label for="total_tagihan_penyesuaian" class="{{ $labelClasses }}">Penyesuaian</label>
                                <input type="number" name="total_tagihan_penyesuaian" id="total_tagihan_penyesuaian"
                                    value="{{ old('total_tagihan_penyesuaian', 0) }}"
                                    class="{{ $inputClasses }}" step="0.01">
                            </div>
                            <div>
                                <label for="total_tagihan_setelah_penyesuaian" class="{{ $labelClasses }}">Total Akhir</label>
                                <input type="number" name="total_tagihan_setelah_penyesuaian" id="total_tagihan_setelah_penyesuaian"
                                    value="{{ old('total_tagihan_setelah_penyesuaian', 0) }}"
                                    class="{{ $readonlyInputClasses }} font-bold text-gray-800 bg-gray-100" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200 h-full">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">Informasi Tambahan</h4>
                        <div class="space-y-2">
                            <div>
                                <label for="alasan_penyesuaian" class="{{ $labelClasses }}">Alasan Penyesuaian</label>
                                <textarea name="alasan_penyesuaian" id="alasan_penyesuaian" rows="2"
                                    class="{{ $inputClasses }}" placeholder="Jelaskan alasan penyesuaian...">{{ old('alasan_penyesuaian') }}</textarea>
                            </div>
                            <div>
                                <label for="keterangan" class="{{ $labelClasses }}">Keterangan</label>
                                <textarea name="keterangan" id="keterangan" rows="2"
                                    class="{{ $inputClasses }}" placeholder="Tambahkan keterangan...">{{ old('keterangan') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button Form Controls -->
            <div class="flex justify-end gap-2 pt-2">
                <a href="{{ route('pembatalan-surat-jalan.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    Batal
                </a>
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    Simpan Transaksi Pembatalan
                </button>
            </div>
        </form>
    </div>

{{-- Inline Clientside Script Sync Controls layout setup setups --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const rows = document.querySelectorAll('.sj-row');
        const selectedIdInput = document.getElementById('surat_jalan_id');
        const selectedText = document.getElementById('selectedSjText');
        const nomorPembayaranInput = document.getElementById('nomor_pembayaran');
        const generateBtn = document.getElementById('generateNomorBtn');
        const tanggalKasInput = document.getElementById('tanggal_kas');
        const tanggalPembayaranInput = document.getElementById('tanggal_pembayaran');
        const bankSearch = document.getElementById('bankSearch');
        const bankSelect = document.getElementById('bank');
        const bankDropdown = document.getElementById('bankDropdown');
        const bankOptions = document.getElementById('bankOptions');
        const noBankResults = document.getElementById('noBankResults');
        const totalPembayaranInput = document.getElementById('total_pembayaran');
        const totalPenyesuaianInput = document.getElementById('total_tagihan_penyesuaian');
        const totalSetelahInput = document.getElementById('total_tagihan_setelah_penyesuaian');
        const searchInput = document.getElementById('searchSjClient');
        const tableBody = document.getElementById('sjTableBody');
        const searchCounter = document.getElementById('searchCounter');
        const noResultsRow = document.getElementById('noResultsRow');
        const emptyRow = document.getElementById('emptyRow');

        rows.forEach(row => {
            row.addEventListener('click', function () {
                const radio = this.querySelector('.sj-radio');
                const id = this.dataset.id;
                const no = this.dataset.no;
                const uangJalan = parseFloat(this.dataset.uangJalan || 0);

                if (radio) radio.checked = true;

                selectedIdInput.value = id;
                selectedText.value = no;

                // Sync total pembayaran from selected surat jalan's uang jalan
                if (totalPembayaranInput) {
                    totalPembayaranInput.value = uangJalan;
                }
                updateTotalSetelahPenyesuaian();

                rows.forEach(r => r.classList.remove('bg-indigo-50'));
                this.classList.add('bg-indigo-50');
            });
        });

        function generateNomorPembayaranLocal() {
            const now = new Date();
            const mm = String(now.getMonth() + 1).padStart(2, '0');
            const yy = String(now.getFullYear()).slice(-2);
            const running = String(now.getTime() % 1000000).padStart(6, '0');
            if (nomorPembayaranInput) {
                nomorPembayaranInput.value = `PBL-${mm}-${yy}-${running}`;
            }
        }

        if (generateBtn) {
            generateBtn.addEventListener('click', generateNomorPembayaranLocal);
            if (!nomorPembayaranInput?.value) {
                generateNomorPembayaranLocal();
            }
        }

        if (tanggalPembayaranInput) {
            tanggalPembayaranInput.value = tanggalKasInput?.value || new Date().toISOString().split('T')[0];
            if (tanggalKasInput) {
                tanggalKasInput.addEventListener('change', function () {
                    tanggalPembayaranInput.value = this.value || new Date().toISOString().split('T')[0];
                });
            }
        }

        if (bankSearch && bankSelect && bankDropdown && bankOptions && noBankResults) {
            const banks = Array.from(bankSelect.options).slice(1);

            function renderBankOptions(filteredBanks) {
                bankOptions.innerHTML = '';

                if (filteredBanks.length === 0) {
                    bankOptions.classList.add('hidden');
                    noBankResults.classList.remove('hidden');
                    return;
                }

                bankOptions.classList.remove('hidden');
                noBankResults.classList.add('hidden');

                filteredBanks.forEach(option => {
                    const div = document.createElement('div');
                    div.className = 'px-3 py-2 text-sm hover:bg-indigo-50 cursor-pointer transition-colors';
                    div.textContent = option.text;
                    div.dataset.value = option.value;

                    div.addEventListener('click', function () {
                        bankSelect.value = this.dataset.value;
                        bankSearch.value = this.textContent;
                        bankDropdown.classList.add('hidden');
                        bankSelect.dispatchEvent(new Event('change'));
                    });

                    bankOptions.appendChild(div);
                });
            }

            bankSearch.addEventListener('focus', function () {
                const term = this.value.toLowerCase();
                const filtered = banks.filter(option => option.text.toLowerCase().includes(term));
                renderBankOptions(filtered);
                bankDropdown.classList.remove('hidden');
            });

            bankSearch.addEventListener('input', function () {
                const term = this.value.toLowerCase();
                const filtered = banks.filter(option => option.text.toLowerCase().includes(term));
                renderBankOptions(filtered);
                bankDropdown.classList.remove('hidden');
                if (!term) {
                    bankSelect.value = '';
                }
            });

            document.addEventListener('click', function (e) {
                if (!bankSearch.contains(e.target) && !bankDropdown.contains(e.target)) {
                    bankDropdown.classList.add('hidden');
                }
            });

            if (bankSelect.value) {
                const selectedOption = bankSelect.options[bankSelect.selectedIndex];
                if (selectedOption) {
                    bankSearch.value = selectedOption.text;
                }
            }
        }

        function updateTotalSetelahPenyesuaian() {
            const totalPembayaran = parseFloat(totalPembayaranInput?.value) || 0;
            const totalPenyesuaian = parseFloat(totalPenyesuaianInput?.value) || 0;
            if (totalSetelahInput) {
                totalSetelahInput.value = totalPembayaran + totalPenyesuaian;
            }
        }

        if (totalPembayaranInput) {
            totalPembayaranInput.addEventListener('input', updateTotalSetelahPenyesuaian);
        }
        if (totalPenyesuaianInput) {
            totalPenyesuaianInput.addEventListener('input', updateTotalSetelahPenyesuaian);
        }
        updateTotalSetelahPenyesuaian();

        if (searchInput && tableBody) {
            searchInput.addEventListener('input', function () {
                const query = this.value.toLowerCase().trim();
                const listRows = tableBody.querySelectorAll('.sj-row');
                let visibleCount = 0;
                const totalCount = listRows.length;

                listRows.forEach(item => {
                    const noSurat = item.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
                    const pengirim = item.querySelector('td:nth-child(4)')?.textContent.toLowerCase() || '';
                    const supir = item.querySelector('td:nth-child(5)')?.textContent.toLowerCase() || '';
                    const uangJalan = item.querySelector('td:nth-child(6)')?.textContent.toLowerCase() || '';
                    const searchableText = noSurat + ' ' + pengirim + ' ' + supir + ' ' + uangJalan;

                    if (searchableText.includes(query)) {
                        item.style.display = '';
                        visibleCount++;
                    } else {
                        item.style.display = 'none';
                    }
                });

                if (searchCounter) {
                    searchCounter.textContent = query ? `${visibleCount} dari ${totalCount} surat jalan` : '';
                }

                if (visibleCount === 0 && totalCount > 0) {
                    if (noResultsRow) noResultsRow.classList.remove('hidden');
                    if (emptyRow) emptyRow.classList.add('hidden');
                } else {
                    if (noResultsRow) noResultsRow.classList.add('hidden');
                    if (emptyRow && totalCount === 0) emptyRow.classList.remove('hidden');
                }
            });
        }

        const form = document.getElementById('pembatalanForm');
        if (form) {
            form.addEventListener('submit', function (e) {
                if (!nomorPembayaranInput?.value) {
                    e.preventDefault();
                    showWarning('Nomor pembayaran belum digenerate. Silakan klik tombol refresh untuk generate nomor.', 'warning');
                    return false;
                }

                if (!selectedIdInput.value) {
                    e.preventDefault();
                    showWarning('Silakan pilih Surat Jalan terlebih dahulu dari tabel.', 'warning');
                    return false;
                }

                if (!bankSelect?.value) {
                    e.preventDefault();
                    showWarning('Silakan pilih bank terlebih dahulu.', 'warning');
                    return false;
                }

                const jenisTransaksi = document.getElementById('jenis_transaksi');
                if (!jenisTransaksi?.value) {
                    e.preventDefault();
                    showWarning('Silakan pilih jenis transaksi terlebih dahulu.', 'warning');
                    return false;
                }

                const selectedNo = selectedText.value || '-';
                const totalPembayaran = parseFloat(totalSetelahInput?.value) || 0;
                const bankName = bankSelect.options[bankSelect.selectedIndex]?.text || '-';
                const confirmRes = confirm(`Konfirmasi pembatalan:\n\nNo Surat Jalan: ${selectedNo}\nJumlah: Rp ${totalPembayaran.toLocaleString('id-ID')}\nBank: ${bankName}\nJenis: ${jenisTransaksi.value}\n\nStatus akan otomatis berubah menjadi Cancelled.\nLanjutkan pembatalan?`);
                if (!confirmRes) {
                    e.preventDefault();
                    return false;
                }

                showLoadingIndicator();
            });
        }

        function showWarning(message, type = 'error') {
            const warningDiv = document.createElement('div');
            warningDiv.className = `mb-3 p-3 rounded-lg border text-sm ${
                type === 'warning' ? 'bg-yellow-50 border-yellow-200 text-yellow-800' : 'bg-red-50 border-red-200 text-red-800'
            }`;
            warningDiv.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-lg leading-none">&times;</button>
                </div>
            `;

            const wrapper = form?.parentNode;
            if (wrapper) {
                wrapper.insertBefore(warningDiv, form);
            }

            setTimeout(() => {
                if (warningDiv.parentNode) {
                    warningDiv.remove();
                }
            }, 5000);
        }

        if (nomorPembayaranInput) {
            nomorPembayaranInput.addEventListener('blur', function () {
                const value = this.value.trim();
                if (!value) {
                    return;
                }

                const formatRegex = /^[A-Z0-9]{1,3}-\d{2}-\d{2}-\d{6}$/;
                if (!formatRegex.test(value)) {
                    showWarning('Format nomor pembayaran tidak valid. Format yang benar: XXX-MM-YY-NNNNNN', 'warning');
                }
            });
        }

        function showLoadingIndicator() {
            const submitButton = document.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Menyimpan...
                `;
            }
        }
    });
</script>
@endsection
