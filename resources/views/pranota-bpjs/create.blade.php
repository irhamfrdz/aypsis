@extends('layouts.app')

@section('title', 'Buat Pranota BPJS')
@section('page_title', 'Buat Pranota BPJS')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-6 w-full max-w-[96rem] mx-auto space-y-6">

    {{-- ── Page Header ──────────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-gray-200">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-teal-600 text-white flex items-center justify-center shadow-sm">
                <i class="fas fa-file-invoice-dollar text-lg"></i>
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Buat Pranota BPJS</h1>
                <p class="text-xs sm:text-sm text-gray-500">Form pembuatan rincian tagihan & iuran BPJS karyawan</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('pranota-bpjs.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs font-semibold rounded-lg shadow-2xs transition-all">
                <i class="fas fa-arrow-left text-xs text-gray-500"></i>
                Kembali ke Daftar
            </a>
        </div>
    </div>

    {{-- ── Validation / Session Alerts ─────────────────────────────────────── --}}
    @if ($errors->any())
        <div class="bg-rose-50 border border-rose-200 rounded-xl p-4 shadow-xs">
            <div class="flex items-start gap-3">
                <div class="w-7 h-7 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <i class="fas fa-exclamation-circle text-sm"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-rose-900">Terdapat kesalahan input:</h3>
                    <ul class="list-disc list-inside text-xs text-rose-700 mt-1 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-rose-50 border border-rose-200 rounded-xl p-4 shadow-xs">
            <div class="flex items-center gap-3">
                <div class="w-7 h-7 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-sm"></i>
                </div>
                <p class="text-sm font-semibold text-rose-900">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <form id="pranota-form" action="{{ route('pranota-bpjs.store') }}" method="POST">
        @csrf

        {{-- ── Section 1: Informasi Periode Pranota ────────────────────────── --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-xs overflow-hidden">
            <div class="flex items-center justify-between px-6 py-3.5 border-b border-gray-100 bg-gray-50/80">
                <div class="flex items-center gap-2.5">
                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-teal-100 text-teal-800">
                        <i class="fas fa-calendar-check text-xs"></i>
                    </span>
                    <h2 class="text-sm font-bold text-gray-800">1. Informasi Pranota</h2>
                </div>
                <span class="text-[11px] text-gray-400 font-medium">* Wajib diisi</span>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5" for="tanggal_pranota">
                            Tanggal Pranota <span class="text-rose-500">*</span>
                        </label>
                        <input id="tanggal_pranota" name="tanggal_pranota" type="date"
                            class="w-full text-xs sm:text-sm rounded-lg border-gray-300 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 shadow-2xs transition"
                            value="{{ date('Y-m-d') }}" required />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5" for="periode_bulan">
                            Bulan Periode <span class="text-rose-500">*</span>
                        </label>
                        <select id="periode_bulan" name="periode_bulan"
                            class="w-full text-xs sm:text-sm rounded-lg border-gray-300 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 shadow-2xs transition font-medium" required>
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ date('n') == $i ? 'selected' : '' }}>
                                    {{ str_pad($i, 2, '0', STR_PAD_LEFT) }} — {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5" for="periode_tahun">
                            Tahun Periode <span class="text-rose-500">*</span>
                        </label>
                        <input id="periode_tahun" name="periode_tahun" type="number"
                            class="w-full text-xs sm:text-sm rounded-lg border-gray-300 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 shadow-2xs transition font-medium"
                            value="{{ date('Y') }}" required />
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5" for="keterangan">
                        Catatan / Keterangan <span class="text-gray-400 font-normal">(Opsional)</span>
                    </label>
                    <textarea id="keterangan" name="keterangan" rows="2"
                        class="w-full text-xs sm:text-sm rounded-lg border-gray-300 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 shadow-2xs transition placeholder:text-gray-400"
                        placeholder="Tambahkan catatan khusus pranota ini jika diperlukan..."></textarea>
                </div>
            </div>
        </div>

        {{-- ── Summary Cards ────────────────────────────────────────────────── --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6">
            <div class="bg-white rounded-xl p-4 border border-gray-200/80 shadow-2xs flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-users text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Total Karyawan</p>
                    <p class="text-base sm:text-lg font-bold text-gray-900 font-mono mt-0.5" id="card_total_karyawan">0 Orang</p>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 border border-indigo-100 shadow-2xs flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-heart-pulse text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold text-indigo-700 uppercase tracking-wide">Total JKN</p>
                    <p class="text-base sm:text-lg font-bold text-indigo-700 font-mono mt-0.5" id="card_total_jkn">Rp 0</p>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 border border-emerald-100 shadow-2xs flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-shield-alt text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold text-emerald-700 uppercase tracking-wide">Total Jamsostek</p>
                    <p class="text-base sm:text-lg font-bold text-emerald-700 font-mono mt-0.5" id="card_total_jamsostek">Rp 0</p>
                </div>
            </div>
            <div class="bg-teal-50/70 rounded-xl p-4 border border-teal-200 shadow-2xs flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-teal-600 text-white flex items-center justify-center flex-shrink-0 shadow-2xs">
                    <i class="fas fa-money-bill-wave text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] font-bold text-teal-800 uppercase tracking-wide">Grand Total</p>
                    <p class="text-base sm:text-lg font-bold text-teal-900 font-mono mt-0.5" id="card_grand_total">Rp 0</p>
                </div>
            </div>
        </div>

        {{-- ── Section 2: Detail Karyawan & Tabel Kalkulasi ─────────────────── --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-xs overflow-hidden mt-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-6 py-4 border-b border-gray-100 bg-gray-50/80">
                <div class="flex items-center gap-2.5">
                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-indigo-100 text-indigo-800">
                        <i class="fas fa-table-list text-xs"></i>
                    </span>
                    <div>
                        <h2 class="text-sm font-bold text-gray-800">2. Rincian Iuran BPJS Karyawan</h2>
                        <p class="text-[11px] text-gray-500">Tabel perhitungan rinci iuran JKN, Jamsostek PPU & BPU</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" id="btn-export-excel"
                        style="background-color: #0d6938 !important; color: #ffffff !important; border: 1px solid #094c28 !important;"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-white text-xs font-bold rounded-lg shadow-sm hover:opacity-90 transition-all cursor-pointer">
                        <i class="fas fa-file-excel text-sm text-white"></i>
                        <span>Export Excel (BPU)</span>
                    </button>
                    <button type="button" id="btn-generate-all"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-2xs hover:shadow-xs transition-all">
                        <i class="fas fa-wand-magic-sparkles text-xs"></i>
                        Hitung Semua Karyawan
                    </button>
                    <button type="button" id="btn-add-karyawan"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-2xs hover:shadow-xs transition-all">
                        <i class="fas fa-plus text-xs"></i>
                        Tambah Baris
                    </button>
                </div>
            </div>

            {{-- ── Filter & Search Bar ───────────────────────────────────────── --}}
            <div class="px-6 py-3 bg-slate-50/80 border-b border-gray-200 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div class="flex flex-wrap items-center gap-3">
                    {{-- Filter Group BPJS Dropdown --}}
                    <div class="flex items-center gap-2 min-w-[240px]">
                        <label for="filter-group-bpjs" class="text-xs font-semibold text-gray-700 whitespace-nowrap flex items-center gap-1.5">
                            <span class="inline-flex items-center justify-center w-5 h-5 rounded bg-teal-100 text-teal-800">
                                <i class="fas fa-filter text-[10px]"></i>
                            </span>
                            <span>Filter Group BPJS:</span>
                        </label>
                        <select id="filter-group-bpjs" class="text-xs rounded-lg border-gray-300 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 shadow-2xs py-1.5 px-3 bg-white font-medium cursor-pointer min-w-[200px]">
                            <option value="all">Semua Group BPJS</option>
                        </select>
                    </div>

                    {{-- Filter Cabang BPJS Dropdown --}}
                    <div class="flex items-center gap-2 min-w-[220px]">
                        <label for="filter-cabang-bpjs" class="text-xs font-semibold text-gray-700 whitespace-nowrap flex items-center gap-1.5">
                            <span class="inline-flex items-center justify-center w-5 h-5 rounded bg-purple-100 text-purple-800">
                                <i class="fas fa-building text-[10px]"></i>
                            </span>
                            <span>Filter Cabang:</span>
                        </label>
                        <select id="filter-cabang-bpjs" class="text-xs rounded-lg border-gray-300 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 shadow-2xs py-1.5 px-3 bg-white font-medium cursor-pointer min-w-[180px]">
                            <option value="all">Semua Cabang BPJS</option>
                        </select>
                    </div>

                    {{-- Search Karyawan Input --}}
                    <div class="relative min-w-[220px]">
                        <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-gray-400">
                            <i class="fas fa-search text-xs"></i>
                        </div>
                        <input type="text" id="search-karyawan" placeholder="Cari nama karyawan / NIK..."
                            class="w-full pl-8 pr-7 text-xs rounded-lg border-gray-300 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 shadow-2xs py-1.5 bg-white transition">
                        <button type="button" id="btn-clear-search" class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-gray-400 hover:text-gray-600 hidden" title="Hapus pencarian">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>

                    {{-- Reset Filter Button --}}
                    <button type="button" id="btn-reset-filter" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-lg transition-colors font-semibold hidden">
                        <i class="fas fa-rotate-left text-[11px]"></i>
                        Reset Filter
                    </button>
                </div>

                {{-- Counter / Status Badge & Action --}}
                <div class="flex items-center gap-2">
                    <button type="button" id="btn-export-excel-2"
                        style="background-color: #0d6938 !important; color: #ffffff !important; border: 1px solid #094c28 !important;"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-white text-xs font-bold rounded-lg shadow-xs hover:opacity-90 transition-all cursor-pointer">
                        <i class="fas fa-file-excel text-xs text-white"></i>
                        <span>Export Excel</span>
                    </button>
                    <span id="filter-status-badge" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-gray-100 text-gray-600">
                        <i class="fas fa-users text-xs"></i>
                        <span id="filter-count-text">0 Karyawan</span>
                    </span>
                </div>
            </div>

            {{-- ── Table Detail ──────────────────────────────────────────────── --}}
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left border-collapse" id="tabel-detail">
                    <thead>
                        {{-- ── Group Header Row ── --}}
                        <tr class="text-[11px] uppercase tracking-wider font-bold border-b border-gray-200 text-gray-700">
                            <th id="th-group-info" colspan="5" class="px-3 py-2 text-center border-r border-gray-200 bg-gray-100">
                                <i class="fas fa-id-card-clip text-gray-500 mr-1"></i> Informasi Karyawan
                            </th>
                            <th id="th-group-jkn" colspan="2" class="px-3 py-2 text-center border-r border-indigo-200 bg-indigo-50 text-indigo-900 col-group-jkn">
                                <i class="fas fa-heart-pulse text-indigo-600 mr-1"></i> BPJS Kesehatan (JKN)
                            </th>
                            <th id="th-group-ppu" colspan="4" class="px-3 py-2 text-center border-r border-emerald-200 bg-emerald-50 text-emerald-900 col-group-ppu">
                                <i class="fas fa-building text-emerald-600 mr-1"></i> BP Jamsostek (PPU)
                            </th>
                            <th id="th-group-bpu-crew" colspan="3" class="px-3 py-2 text-center border-r border-sky-200 bg-sky-50 text-sky-900 col-group-bpu-crew">
                                <i class="fas fa-ship text-sky-600 mr-1"></i> BP Jamsostek (BPU-CREW)
                            </th>
                            <th id="th-group-bpu-noncrew" colspan="4" class="px-3 py-2 text-center border-r border-purple-200 bg-purple-50 text-purple-900 col-group-bpu-noncrew">
                                <i class="fas fa-user-tag text-purple-600 mr-1"></i> BP Jamsostek (Non BPU-CREW)
                            </th>
                            <th id="th-group-jp" colspan="2" class="px-3 py-2 text-center border-r border-amber-200 bg-amber-50 text-amber-900 col-group-jp">
                                <i class="fas fa-piggy-bank text-amber-600 mr-1"></i> Jaminan Pensiun (JP)
                            </th>
                            <th rowspan="2" class="px-4 py-3 text-right bg-teal-50 text-teal-900 border-l border-teal-200 font-bold whitespace-nowrap" style="min-width:130px">
                                Subtotal (Rp)
                            </th>
                            <th rowspan="2" class="px-2 py-3 w-10 text-center bg-gray-100 text-gray-500 font-bold">
                                Aksi
                            </th>
                        </tr>
                        {{-- ── Sub Header Row ── --}}
                        <tr class="text-[10px] font-semibold uppercase tracking-wider text-gray-500 border-b-2 border-gray-200 bg-gray-50">
                            {{-- Info --}}
                            <th class="px-3 py-2.5 w-10 text-center font-bold text-gray-400 col-info-no">#</th>
                            <th class="px-3 py-2.5 text-left font-bold text-gray-700 col-info-nama" style="min-width:200px">Nama Karyawan</th>
                            <th class="px-3 py-2.5 text-center font-bold text-gray-600 col-info-group" style="min-width:120px">Group BPJS</th>
                            <th class="px-3 py-2.5 text-center font-bold text-gray-600 col-info-tipe-jkn" style="min-width:130px">Tipe JKN</th>
                            <th class="px-3 py-2.5 text-center font-bold text-gray-600 border-r border-gray-200 col-info-tipe-jam" style="min-width:140px">Tipe Jamsostek</th>
                            
                            {{-- JKN (2) --}}
                            <th class="px-3 py-2.5 text-right font-bold text-indigo-700 bg-indigo-50/30 col-group-jkn" style="min-width:105px" title="BPJS Kesehatan 4% Tunjangan">KIS 4% (Tunj)</th>
                            <th class="px-3 py-2.5 text-right font-bold text-indigo-700 bg-indigo-50/30 border-r border-indigo-100 col-group-jkn" style="min-width:105px" title="BPJS Kesehatan 1% Hutang">KIS 1% (Hutang)</th>
                            
                            {{-- PPU (4) --}}
                            <th class="px-3 py-2.5 text-right font-bold text-emerald-700 bg-emerald-50/30 col-group-ppu" style="min-width:100px" title="JHT Biaya Perusahaan 3.7%">PPU JHT 3.7%</th>
                            <th class="px-3 py-2.5 text-right font-bold text-emerald-700 bg-emerald-50/30 col-group-ppu" style="min-width:90px" title="JHT Hutang Karyawan 2%">PPU JHT 2%</th>
                            <th class="px-3 py-2.5 text-right font-bold text-emerald-700 bg-emerald-50/30 col-group-ppu" style="min-width:95px" title="JKK Tunjangan 0.24%">PPU JKK 0.24%</th>
                            <th class="px-3 py-2.5 text-right font-bold text-emerald-700 bg-emerald-50/30 border-r border-emerald-100 col-group-ppu" style="min-width:95px" title="JKM Tunjangan 0.3%">PPU JKM 0.3%</th>

                            {{-- BPU-CREW (3) --}}
                            <th class="px-3 py-2.5 text-right font-bold text-rose-600 bg-rose-50/30 col-group-bpu-crew" style="min-width:115px" title="BPU JHT Karyawan (Hutang Potongan)">BPU JHT (Kary)</th>
                            <th class="px-3 py-2.5 text-right font-bold text-sky-700 bg-sky-50/30 col-group-bpu-crew" style="min-width:110px" title="BPU JKK 1% (Tunjangan)">BPU JKK 1%</th>
                            <th class="px-3 py-2.5 text-right font-bold text-sky-700 bg-sky-50/30 border-r border-sky-100 col-group-bpu-crew" style="min-width:100px" title="BPU JKM Biaya">BPU JKM</th>
                            
                            {{-- Non BPU-CREW (4) --}}
                            <th class="px-3 py-2.5 text-right font-bold text-purple-700 bg-purple-50/30 col-group-bpu-noncrew" style="min-width:100px" title="JHT 2% Biaya Perusahaan">JHT 2% (Biaya)</th>
                            <th class="px-3 py-2.5 text-right font-bold text-purple-700 bg-purple-50/30 col-group-bpu-noncrew" style="min-width:100px" title="JHT 2% Hutang Karyawan Tabel DPP">JHT 2% (Hutang)</th>
                            <th class="px-3 py-2.5 text-right font-bold text-purple-700 bg-purple-50/30 col-group-bpu-noncrew" style="min-width:95px" title="JKK 1% Tunjangan">JKK 1% (Tunj)</th>
                            <th class="px-3 py-2.5 text-right font-bold text-purple-700 bg-purple-50/30 border-r border-purple-100 col-group-bpu-noncrew" style="min-width:95px" title="JKM Tunjangan Nominal (Rp)">JKM (Rp)</th>

                            {{-- JP (2) --}}
                            <th class="px-3 py-2.5 text-right font-bold text-amber-700 bg-amber-50/30 col-group-jp" style="min-width:90px" title="Jaminan Pensiun Biaya Perusahaan 2%">PPU JP 2%</th>
                            <th class="px-3 py-2.5 text-right font-bold text-amber-700 bg-amber-50/30 border-r border-gray-200 col-group-jp" style="min-width:90px" title="Jaminan Pensiun Hutang Karyawan 1%">PPU JP 1%</th>
                        </tr>
                    </thead>
                    <tbody id="detail-container" class="divide-y divide-gray-100 bg-white font-medium">
                        <!-- Dynamic Rows Generated via JS -->
                    </tbody>
                    <tfoot class="bg-gray-50/95 border-t-2 border-gray-300 shadow-xs text-xs">
                        <tr class="divide-x divide-gray-200/60 font-semibold">
                            <td colspan="5" class="px-4 py-3.5 text-right font-bold text-gray-700 uppercase tracking-wide bg-gray-100/90" id="footer-total-label">
                                Total Keseluruhan:
                            </td>
                            {{-- JKN --}}
                            <td class="px-3 py-3.5 text-right font-mono font-bold text-indigo-700 bg-indigo-50/50 col-group-jkn" id="total_kes">Rp 0</td>
                            <td class="px-3 py-3.5 text-right font-mono font-bold text-indigo-700 bg-indigo-50/50 col-group-jkn" id="total_ket">Rp 0</td>
                            
                            {{-- PPU --}}
                            <td class="px-3 py-3.5 text-right font-mono font-bold text-emerald-700 bg-emerald-50/50 col-group-ppu" id="total_jht_biaya">Rp 0</td>
                            <td class="px-3 py-3.5 text-right font-mono font-bold text-emerald-700 bg-emerald-50/50 col-group-ppu" id="total_jht_hutang">Rp 0</td>
                            <td class="px-3 py-3.5 text-right font-mono font-bold text-emerald-700 bg-emerald-50/50 col-group-ppu" id="total_jkk">Rp 0</td>
                            <td class="px-3 py-3.5 text-right font-mono font-bold text-emerald-700 bg-emerald-50/50 col-group-ppu" id="total_jkm">Rp 0</td>
                            
                            {{-- BPU-CREW --}}
                            <td class="px-3 py-3.5 text-right font-mono font-bold text-rose-600 bg-rose-50/50 col-group-bpu-crew" id="total_jkk_hutang">Rp 0</td>
                            <td class="px-3 py-3.5 text-right font-mono font-bold text-sky-700 bg-sky-50/50 col-group-bpu-crew" id="total_bpu_jkk">Rp 0</td>
                            <td class="px-3 py-3.5 text-right font-mono font-bold text-sky-700 bg-sky-50/50 col-group-bpu-crew" id="total_bpu_jkm">Rp 0</td>
                            
                            {{-- Non BPU-CREW --}}
                            <td class="px-3 py-3.5 text-right font-mono font-bold text-purple-700 bg-purple-50/50 col-group-bpu-noncrew" id="total_noncrew_jht_biaya">Rp 0</td>
                            <td class="px-3 py-3.5 text-right font-mono font-bold text-purple-700 bg-purple-50/50 col-group-bpu-noncrew" id="total_noncrew_jht_hutang">Rp 0</td>
                            <td class="px-3 py-3.5 text-right font-mono font-bold text-purple-700 bg-purple-50/50 col-group-bpu-noncrew" id="total_noncrew_jkk">Rp 0</td>
                            <td class="px-3 py-3.5 text-right font-mono font-bold text-purple-700 bg-purple-50/50 col-group-bpu-noncrew" id="total_noncrew_jkm">Rp 0</td>

                            {{-- JP --}}
                            <td class="px-3 py-3.5 text-right font-mono font-bold text-amber-700 bg-amber-50/50 col-group-jp" id="total_jp_biaya">Rp 0</td>
                            <td class="px-3 py-3.5 text-right font-mono font-bold text-amber-700 bg-amber-50/50 col-group-jp" id="total_jp_hutang">Rp 0</td>
                            
                            {{-- Grand Total --}}
                            <td class="px-4 py-3.5 text-right font-mono font-bold text-teal-800 text-sm bg-teal-100/70" id="grand_total">Rp 0</td>
                            <td class="bg-gray-100/90"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Empty State (Shown when 0 rows in table) --}}
            <div id="table-empty-state" class="py-12 px-4 text-center border-t border-gray-100">
                <div class="w-12 h-12 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-users-slash text-xl"></i>
                </div>
                <h3 class="text-sm font-semibold text-gray-700">Belum ada data karyawan</h3>
                <p class="text-xs text-gray-400 mt-1 max-w-sm mx-auto">Klik tombol <strong>Hitung Semua Karyawan</strong> untuk memuat data otomatis atau <strong>Tambah Baris</strong> untuk mengisi manual.</p>
            </div>

            {{-- Filter Empty State (Shown when search/filter yields 0 matches) --}}
            <div id="table-filter-empty-state" class="py-12 px-4 text-center border-t border-gray-100 hidden">
                <div class="w-12 h-12 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-filter-circle-xmark text-xl"></i>
                </div>
                <h3 class="text-sm font-semibold text-gray-700">Tidak ada karyawan yang sesuai filter</h3>
                <p class="text-xs text-gray-400 mt-1 max-w-sm mx-auto">Tidak ditemukan karyawan dengan kriteria Group, Cabang BPJS, atau kata kunci pencarian yang dipilih.</p>
                <button type="button" id="btn-reset-filter-empty" class="mt-3 inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors">
                    <i class="fas fa-rotate-left text-xs"></i>
                    Reset Filter
                </button>
            </div>
        </div>

        {{-- ── Bottom Actions & Confirmation ────────────────────────────────── --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-4 border-t border-gray-200">
            <div class="flex items-center gap-2 text-xs text-gray-500">
                <i class="fas fa-shield-halved text-teal-600 text-sm"></i>
                <span>Pastikan data rincian iuran sudah sesuai sebelum menyimpan pranota.</span>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('pranota-bpjs.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs font-semibold rounded-lg shadow-2xs transition-colors">
                    <i class="fas fa-times text-xs text-gray-400"></i>
                    Batal
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-lg shadow-sm hover:shadow transition-all">
                    <i class="fas fa-save text-xs"></i>
                    Simpan Pranota
                </button>
            </div>
        </div>
    </form>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.4.0/exceljs.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('detail-container');
    const emptyState = document.getElementById('table-empty-state');
    const filterEmptyState = document.getElementById('table-filter-empty-state');
    const btnAdd = document.getElementById('btn-add-karyawan');
    const filterGroupSelect = document.getElementById('filter-group-bpjs');
    const filterCabangSelect = document.getElementById('filter-cabang-bpjs');
    const searchKaryawanInput = document.getElementById('search-karyawan');
    const btnClearSearch = document.getElementById('btn-clear-search');
    const btnResetFilter = document.getElementById('btn-reset-filter');
    const btnResetFilterEmpty = document.getElementById('btn-reset-filter-empty');
    const filterStatusBadge = document.getElementById('filter-status-badge');
    const filterCountText = document.getElementById('filter-count-text');
    
    // Convert karyawans to JSON for select options
    const karyawans = @json($karyawans);
    const rumusBpjs = @json($rumusBpjs);
    let rowCount = 0;

    function formatNumber(num) {
        return new Intl.NumberFormat('id-ID').format(num || 0);
    }

    /**
     * Parse angka format Indonesia ke float
     */
    function parseIdNumber(val) {
        if (val === null || val === undefined || val === '') return 0;
        if (typeof val === 'number') return val;
        const cleaned = String(val).replace(/\./g, '').replace(',', '.');
        return parseFloat(cleaned) || 0;
    }

    /**
     * Helper to toggle class hidden on selectors
     */
    function toggleElements(selector, show) {
        document.querySelectorAll(selector).forEach(el => {
            if (show) {
                el.classList.remove('hidden');
            } else {
                el.classList.add('hidden');
            }
        });
    }

    /**
     * Update column visibility based on selected BPJS group
     */
    function updateColumnVisibility(selectedGroup) {
        const showAll = (selectedGroup === 'all' || !selectedGroup);
        const showJkn = showAll || (selectedGroup === 'JKN-KIS');
        const showPpu = showAll || (selectedGroup === 'PPU');
        const showBpuCrew = showAll || (selectedGroup === 'BPU-CREW');
        const showBpuNonCrew = showAll || (selectedGroup === 'BPU-NON-CREW');
        const showJp = showAll || (selectedGroup === 'PPU'); // JP is part of PPU

        // Tipe selector visibility
        const showTipeJkn = showAll || (selectedGroup === 'JKN-KIS');
        const showTipeJam = showAll || (selectedGroup !== 'JKN-KIS');

        toggleElements('.col-group-jkn', showJkn);
        toggleElements('.col-group-ppu', showPpu);
        toggleElements('.col-group-bpu-crew', showBpuCrew);
        toggleElements('.col-group-bpu-noncrew', showBpuNonCrew);
        toggleElements('.col-group-jp', showJp);

        toggleElements('.col-info-tipe-jkn', showTipeJkn);
        toggleElements('.col-info-tipe-jam', showTipeJam);

        // Group Header Row 1 colspans and visibility
        const thInfo = document.getElementById('th-group-info');
        const thJkn = document.getElementById('th-group-jkn');
        const thPpu = document.getElementById('th-group-ppu');
        const thBpuCrew = document.getElementById('th-group-bpu-crew');
        const thBpuNonCrew = document.getElementById('th-group-bpu-noncrew');
        const thJp = document.getElementById('th-group-jp');
        const footerInfo = document.getElementById('footer-total-label');

        if (thJkn) thJkn.classList.toggle('hidden', !showJkn);
        if (thPpu) thPpu.classList.toggle('hidden', !showPpu);
        if (thBpuCrew) thBpuCrew.classList.toggle('hidden', !showBpuCrew);
        if (thBpuNonCrew) thBpuNonCrew.classList.toggle('hidden', !showBpuNonCrew);
        if (thJp) thJp.classList.toggle('hidden', !showJp);

        let infoColspan = 3; // #, Nama, Group
        if (showTipeJkn) infoColspan++;
        if (showTipeJam) infoColspan++;

        if (thInfo) thInfo.setAttribute('colspan', infoColspan);
        if (footerInfo) footerInfo.setAttribute('colspan', infoColspan);
    }

    /**
     * Populate options for the Group BPJS filter dropdown
     */
    function populateGroupFilterOptions() {
        if (!filterGroupSelect) return;

        filterGroupSelect.innerHTML = `
            <option value="all">Semua Group BPJS</option>
            <option value="JKN-KIS">Group JKN-KIS</option>
            <option value="PPU">Group PPU</option>
            <option value="BPU-CREW">Group BPU-CREW</option>
            <option value="BPU-NON-CREW">Group BPU-NON-CREW</option>
        `;
    }

    /**
     * Populate options for the Cabang BPJS filter dropdown
     */
    function populateCabangFilterOptions() {
        if (!filterCabangSelect) return;

        const cabangs = new Set();
        karyawans.forEach(k => {
            if (k.cabang_bpjs && k.cabang_bpjs.trim() !== '') {
                cabangs.add(k.cabang_bpjs.trim());
            }
        });
        rumusBpjs.forEach(r => {
            if (r.cabang_bpjs && r.cabang_bpjs.trim() !== '') {
                cabangs.add(r.cabang_bpjs.trim());
            }
        });

        const sortedCabangs = Array.from(cabangs).sort((a, b) => a.localeCompare(b, 'id', { sensitivity: 'base' }));

        let html = '<option value="all">Semua Cabang BPJS</option>';
        sortedCabangs.forEach(c => {
            html += `<option value="${c}">${c}</option>`;
        });
        filterCabangSelect.innerHTML = html;
    }

    /**
     * Apply filter and search on table rows and columns
     */
    function applyFilter() {
        const selectedGroup = filterGroupSelect ? filterGroupSelect.value : 'all';
        const selectedCabang = filterCabangSelect ? filterCabangSelect.value : 'all';
        const query = searchKaryawanInput ? (searchKaryawanInput.value || '').trim().toLowerCase() : '';
        const rows = container.querySelectorAll('tr.detail-row');
        
        let totalRows = rows.length;
        let visibleRows = 0;

        rows.forEach(tr => {
            const kName = tr.dataset.namaKaryawan || '';
            const kNik = tr.dataset.nik || '';
            const gJknUpper = (tr.dataset.groupJkn || '').toUpperCase().trim();
            const gJamUpper = (tr.dataset.groupJamsostek || '').toUpperCase().trim();
            const cab = (tr.dataset.cabang || '').trim();
            const dppJkn = parseFloat(tr.dataset.dppJkn || 0);
            const dppJam = parseFloat(tr.dataset.dppJamsostek || 0);

            // Check group match
            let matchGroup = false;
            if (selectedGroup === 'all' || !selectedGroup) {
                matchGroup = true;
            } else if (selectedGroup === 'JKN-KIS') {
                matchGroup = (gJknUpper !== '' || gJknUpper.includes('JKN') || gJknUpper.includes('KIS') || dppJkn > 0);
            } else if (selectedGroup === 'PPU') {
                matchGroup = gJamUpper.includes('PPU');
            } else if (selectedGroup === 'BPU-CREW') {
                matchGroup = (!gJamUpper.includes('PPU') && gJamUpper.includes('BPU-CREW'));
            } else if (selectedGroup === 'BPU-NON-CREW') {
                matchGroup = ((gJamUpper !== '' || cab !== '' || dppJam > 0) && !gJamUpper.includes('PPU') && !gJamUpper.includes('BPU-CREW'));
            }

            // Check cabang match
            let matchCabang = false;
            if (selectedCabang === 'all' || !selectedCabang) {
                matchCabang = true;
            } else {
                matchCabang = cab.toLowerCase() === selectedCabang.toLowerCase();
            }

            // Check search match
            let matchSearch = true;
            if (query) {
                matchSearch = kName.includes(query) ||
                              kNik.toLowerCase().includes(query) ||
                              gJknUpper.toLowerCase().includes(query) ||
                              gJamUpper.toLowerCase().includes(query) ||
                              cab.toLowerCase().includes(query);
            }

            if (matchGroup && matchCabang && matchSearch) {
                tr.classList.remove('hidden');
                visibleRows++;
                const rowNumEl = tr.querySelector('.row-number');
                if (rowNumEl) {
                    rowNumEl.innerText = visibleRows;
                }
            } else {
                tr.classList.add('hidden');
            }
        });

        // Update column visibility for the selected group
        updateColumnVisibility(selectedGroup);

        // Filter status indicators
        const isFiltered = (selectedGroup !== 'all' && selectedGroup !== '') || 
                           (selectedCabang !== 'all' && selectedCabang !== '') || 
                           query.length > 0;
        
        if (btnResetFilter) {
            btnResetFilter.classList.toggle('hidden', !isFiltered);
        }
        if (btnClearSearch) {
            btnClearSearch.classList.toggle('hidden', query.length === 0);
        }

        if (filterStatusBadge && filterCountText) {
            if (isFiltered) {
                filterStatusBadge.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200 shadow-2xs';
                filterCountText.innerText = `Menampilkan ${visibleRows} dari ${totalRows} Karyawan`;
            } else {
                filterStatusBadge.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-gray-100 text-gray-600';
                filterCountText.innerText = `${totalRows} Karyawan`;
            }
        }

        // Empty states logic
        if (totalRows === 0) {
            emptyState.classList.remove('hidden');
            if (filterEmptyState) filterEmptyState.classList.add('hidden');
        } else if (visibleRows === 0) {
            emptyState.classList.add('hidden');
            if (filterEmptyState) filterEmptyState.classList.remove('hidden');
        } else {
            emptyState.classList.add('hidden');
            if (filterEmptyState) filterEmptyState.classList.add('hidden');
        }

        calculateTotals(isFiltered, visibleRows, totalRows, selectedGroup, selectedCabang);
    }

    function calculateTotals(isFiltered = false, visibleCount = 0, totalCount = 0, selectedGroup = 'all', selectedCabang = 'all') {
        // Global sums across all rows
        let globalKes = 0, globalKet = 0;
        let globalJhtB = 0, globalJhtH = 0, globalJkk = 0, globalJkm = 0;
        let globalBpuJht = 0, globalBpuJkk = 0, globalBpuJkm = 0;
        let globalNcJhtB = 0, globalNcJhtH = 0, globalNcJkk = 0, globalNcJkm = 0;
        let globalJpB = 0, globalJpH = 0;

        // Visible sums for table footer
        let sumKes = 0, sumKet = 0;
        let sumJhtB = 0, sumJhtH = 0, sumJkk = 0, sumJkm = 0;
        let sumBpuJht = 0, sumBpuJkk = 0, sumBpuJkm = 0;
        let sumNcJhtB = 0, sumNcJhtH = 0, sumNcJkk = 0, sumNcJkm = 0;
        let sumJpB = 0, sumJpH = 0;

        const allRows = container.querySelectorAll('tr.detail-row');
        
        allRows.forEach(tr => {
            const isVisible = !tr.classList.contains('hidden');

            const valKes   = parseIdNumber(tr.querySelector('.input-kes')?.value);
            const valKet   = parseIdNumber(tr.querySelector('.input-ket')?.value);
            const valJhtB  = parseIdNumber(tr.querySelector('.input-jht-biaya')?.value);
            const valJhtH  = parseIdNumber(tr.querySelector('.input-jht-hutang')?.value);
            const valJkk   = parseIdNumber(tr.querySelector('.input-jkk')?.value);
            const valJkm   = parseIdNumber(tr.querySelector('.input-jkm')?.value);
            const valBpuJht = parseIdNumber(tr.querySelector('.input-jkk-hutang')?.value);
            const valBpuJkk = parseIdNumber(tr.querySelector('.input-bpu-jkk')?.value);
            const valBpuJkm = parseIdNumber(tr.querySelector('.input-bpu-jkm')?.value);
            const valNcJhtB = parseIdNumber(tr.querySelector('.input-noncrew-jht-biaya')?.value);
            const valNcJhtH = parseIdNumber(tr.querySelector('.input-noncrew-jht-hutang')?.value);
            const valNcJkk = parseIdNumber(tr.querySelector('.input-noncrew-jkk')?.value);
            const valNcJkm = parseIdNumber(tr.querySelector('.input-noncrew-jkm')?.value);
            const valJpB   = parseIdNumber(tr.querySelector('.input-jp-biaya')?.value);
            const valJpH   = parseIdNumber(tr.querySelector('.input-jp-hutang')?.value);

            // Update row subtotal based on active group
            let rowSubtotal = 0;
            if (selectedGroup === 'JKN-KIS') {
                rowSubtotal = valKes + valKet;
            } else if (selectedGroup === 'PPU') {
                rowSubtotal = valJhtB + valJhtH + valJkk + valJkm + valJpB + valJpH;
            } else if (selectedGroup === 'BPU-CREW') {
                rowSubtotal = valBpuJht + valBpuJkk + valBpuJkm;
            } else if (selectedGroup === 'BPU-NON-CREW') {
                rowSubtotal = valNcJhtB + valNcJhtH + valNcJkk + valNcJkm;
            } else {
                rowSubtotal = valKes + valKet + valJhtB + valJhtH + valJkk + valJkm + valBpuJht + valBpuJkk + valBpuJkm + valNcJhtB + valNcJhtH + valNcJkk + valNcJkm + valJpB + valJpH;
            }
            const rowSubtotalEl = tr.querySelector('.subtotal-text');
            if (rowSubtotalEl) {
                rowSubtotalEl.innerText = 'Rp ' + formatNumber(rowSubtotal);
            }

            // Global accumulator
            globalKes += valKes;
            globalKet += valKet;
            globalJhtB += valJhtB;
            globalJhtH += valJhtH;
            globalJkk += valJkk;
            globalJkm += valJkm;
            globalBpuJht += valBpuJht;
            globalBpuJkk += valBpuJkk;
            globalBpuJkm += valBpuJkm;
            globalNcJhtB += valNcJhtB;
            globalNcJhtH += valNcJhtH;
            globalNcJkk += valNcJkk;
            globalNcJkm += valNcJkm;
            globalJpB += valJpB;
            globalJpH += valJpH;

            // Visible accumulator
            if (isVisible) {
                sumKes += valKes;
                sumKet += valKet;
                sumJhtB += valJhtB;
                sumJhtH += valJhtH;
                sumJkk += valJkk;
                sumJkm += valJkm;
                sumBpuJht += valBpuJht;
                sumBpuJkk += valBpuJkk;
                sumBpuJkm += valBpuJkm;
                sumNcJhtB += valNcJhtB;
                sumNcJhtH += valNcJhtH;
                sumNcJkk += valNcJkk;
                sumNcJkm += valNcJkm;
                sumJpB += valJpB;
                sumJpH += valJpH;
            }
        });

        // Update footer table cells
        document.getElementById('total_kes').innerText                 = 'Rp ' + formatNumber(sumKes);
        document.getElementById('total_ket').innerText                 = 'Rp ' + formatNumber(sumKet);
        document.getElementById('total_jht_biaya').innerText           = 'Rp ' + formatNumber(sumJhtB);
        document.getElementById('total_jht_hutang').innerText          = 'Rp ' + formatNumber(sumJhtH);
        document.getElementById('total_jkk').innerText                 = 'Rp ' + formatNumber(sumJkk);
        document.getElementById('total_jkm').innerText                 = 'Rp ' + formatNumber(sumJkm);
        document.getElementById('total_jkk_hutang').innerText          = 'Rp ' + formatNumber(sumBpuJht);
        document.getElementById('total_bpu_jkk').innerText             = 'Rp ' + formatNumber(sumBpuJkk);
        document.getElementById('total_bpu_jkm').innerText             = 'Rp ' + formatNumber(sumBpuJkm);
        document.getElementById('total_noncrew_jht_biaya').innerText   = 'Rp ' + formatNumber(sumNcJhtB);
        document.getElementById('total_noncrew_jht_hutang').innerText  = 'Rp ' + formatNumber(sumNcJhtH);
        document.getElementById('total_noncrew_jkk').innerText         = 'Rp ' + formatNumber(sumNcJkk);
        document.getElementById('total_noncrew_jkm').innerText         = 'Rp ' + formatNumber(sumNcJkm);
        document.getElementById('total_jp_biaya').innerText            = 'Rp ' + formatNumber(sumJpB);
        document.getElementById('total_jp_hutang').innerText           = 'Rp ' + formatNumber(sumJpH);
        
        let visibleGrandTotal = 0;
        if (selectedGroup === 'JKN-KIS') {
            visibleGrandTotal = sumKes + sumKet;
        } else if (selectedGroup === 'PPU') {
            visibleGrandTotal = sumJhtB + sumJhtH + sumJkk + sumJkm + sumJpB + sumJpH;
        } else if (selectedGroup === 'BPU-CREW') {
            visibleGrandTotal = sumBpuJht + sumBpuJkk + sumBpuJkm;
        } else if (selectedGroup === 'BPU-NON-CREW') {
            visibleGrandTotal = sumNcJhtB + sumNcJhtH + sumNcJkk + sumNcJkm;
        } else {
            visibleGrandTotal = sumKes + sumKet + sumJhtB + sumJhtH + sumJkk + sumJkm + sumBpuJht + sumBpuJkk + sumBpuJkm + sumNcJhtB + sumNcJhtH + sumNcJkk + sumNcJkm + sumJpB + sumJpH;
        }
        
        document.getElementById('grand_total').innerText = 'Rp ' + formatNumber(visibleGrandTotal);

        // Update Footer Label
        const footerLabel = document.getElementById('footer-total-label');
        if (footerLabel) {
            if (isFiltered) {
                let filterLabels = [];
                if (selectedGroup !== 'all' && selectedGroup !== '') filterLabels.push(selectedGroup);
                if (selectedCabang !== 'all' && selectedCabang !== '') filterLabels.push(selectedCabang);
                const filterText = filterLabels.length > 0 ? filterLabels.join(' — ') : 'Filter';
                footerLabel.innerHTML = `<div class="flex items-center justify-end gap-1.5 text-amber-800 font-bold"><i class="fas fa-filter text-xs text-amber-600"></i> <span>Total ${filterText} (${visibleCount} Karyawan):</span></div>`;
            } else {
                footerLabel.innerText = 'Total Keseluruhan:';
            }
        }

        // Global Grand Totals for summary cards
        const globalTotalJkn = globalKes + globalKet;
        const globalTotalJamsostek = globalJhtB + globalJhtH + globalJkk + globalJkm + globalBpuJht + globalBpuJkk + globalBpuJkm + globalNcJhtB + globalNcJhtH + globalNcJkk + globalNcJkm + globalJpB + globalJpH;
        const globalGrandTotal = globalTotalJkn + globalTotalJamsostek;

        document.getElementById('card_total_karyawan').innerText = allRows.length + ' Orang';
        document.getElementById('card_total_jkn').innerText = 'Rp ' + formatNumber(globalTotalJkn);
        document.getElementById('card_total_jamsostek').innerText = 'Rp ' + formatNumber(globalTotalJamsostek);
        document.getElementById('card_grand_total').innerText = 'Rp ' + formatNumber(globalGrandTotal);
    }

    function addRow(karyawanId = null, autoCalculate = false) {
        rowCount++;
        
        let karyawanInputHTML = '';
        if (karyawanId) {
            const k = karyawans.find(k => k.unique_id == karyawanId || k.id == karyawanId);
            const statusBadge = k && k.tipe_label === 'Tidak Tetap'
                ? `<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-100 text-amber-800 ml-1">Tidak Tetap</span>`
                : '';
            karyawanInputHTML = `
                <input type="hidden" name="details[${rowCount}][karyawan_id]" value="${k ? k.id : karyawanId}" class="karyawan-hidden-input">
                <input type="hidden" name="details[${rowCount}][tipe_karyawan]" value="${k ? k.tipe_karyawan : 'App\\Models\\Karyawan'}" class="tipe-karyawan-hidden-input">
                <div class="font-semibold text-gray-800 text-xs truncate flex items-center gap-1" style="max-width: 220px;" title="${k ? k.nama_lengkap : ''}">
                    <span>${k ? k.nama_lengkap : ''}</span>
                    ${statusBadge}
                </div>
                ${k && k.nik ? `<div class="text-[10px] text-gray-400 font-mono tracking-tight">NIK: ${k.nik}</div>` : ''}
            `;
        } else {
            let options = '<option value="">-- Pilih Karyawan --</option>';
            karyawans.forEach(k => {
                const nikLabel = k.nik ? ` [${k.nik}]` : '';
                const statusLabel = k.tipe_label ? ` (${k.tipe_label})` : '';
                options += `<option value="${k.unique_id}">${k.nama_lengkap}${nikLabel}${statusLabel}</option>`;
            });
            karyawanInputHTML = `
                <input type="hidden" name="details[${rowCount}][karyawan_id]" value="" class="karyawan-hidden-input">
                <input type="hidden" name="details[${rowCount}][tipe_karyawan]" value="App\\Models\\Karyawan" class="tipe-karyawan-hidden-input">
                <select class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 select2 select-karyawan" required>
                    ${options}
                </select>
                <div class="nik-display text-[10px] text-gray-400 font-mono tracking-tight mt-0.5 hidden"></div>
            `;
        }

        const tr = document.createElement('tr');
        tr.className = "border-b border-gray-100 detail-row hover:bg-gray-50/70 transition-colors";
        
        if (karyawanId) {
            const k = karyawans.find(k => k.unique_id == karyawanId || k.id == karyawanId);
            if (k) {
                tr.dataset.karyawanId = k.id;
                tr.dataset.tipeKaryawan = k.tipe_karyawan || 'App\\Models\\Karyawan';
                tr.dataset.uniqueId = k.unique_id || '';
                tr.dataset.namaKaryawan = (k.nama_lengkap || '').toLowerCase();
                tr.dataset.nik = k.nik || '';
                tr.dataset.nikKtp = k.nik_ktp || '';
                tr.dataset.noBpjs = k.no_bpjs || '';
                tr.dataset.groupPosisi = k.group_posisi || 'Crew';
                tr.dataset.lokasi = k.lokasi || 'JKT';
                tr.dataset.groupJkn = k.group_jkn || '';
                tr.dataset.groupJamsostek = k.group_bp_jamsostek || '';
                tr.dataset.cabang = k.cabang_bpjs || '';
                tr.dataset.dppJkn = k.dpp_jkn || 0;
                tr.dataset.dppJamsostek = k.dpp_bp_jamsostek || 0;
            }
        }

        tr.innerHTML = `
            <td class="px-3 py-2.5 text-center align-middle row-number text-gray-400 font-mono font-medium col-info-no">${rowCount}</td>
            <td class="px-3 py-2.5 align-middle col-info-nama">
                ${karyawanInputHTML}
                <div class="info-jkn mt-1 hidden"></div>
            </td>
            <td class="px-3 py-2.5 align-middle text-center text-[11px] group-text whitespace-nowrap col-info-group">-</td>
            <td class="px-2 py-2.5 align-middle text-center col-info-tipe-jkn">
                <select class="w-full text-[11px] py-1 px-1.5 text-center select-tipe-jkn border-gray-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-md bg-gray-50/60 shadow-2xs font-medium" name="details[${rowCount}][tipe_jkn]">
                    <option value="tunjangan_hutang">TOTAL JKN</option>
                </select>
            </td>
            <td class="px-2 py-2.5 align-middle text-center border-r border-gray-200 col-info-tipe-jam">
                <select class="w-full text-[11px] py-1 px-1.5 text-center select-tipe-bp-jamsostek border-gray-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-md bg-gray-50/60 shadow-2xs font-medium" name="details[${rowCount}][tipe_bp_jamsostek]">
                    <option value="total">TOTAL BP JAMSOSTEK</option>
                </select>
            </td>
            
            {{-- JKN (2) --}}
            <td class="px-1.5 py-2 align-middle bg-indigo-50/10 col-group-jkn">
                <input type="text" name="details[${rowCount}][bpjs_kesehatan]" class="w-full text-right font-mono text-xs input-kes font-semibold text-indigo-700 bg-gray-50/60 hover:bg-white focus:bg-white border border-transparent hover:border-gray-200 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-200 rounded px-2 py-1 transition" value="0" autocomplete="off">
            </td>
            <td class="px-1.5 py-2 align-middle bg-indigo-50/10 border-r border-indigo-100 col-group-jkn">
                <input type="text" name="details[${rowCount}][bpjs_ketenagakerjaan]" class="w-full text-right font-mono text-xs input-ket font-semibold text-indigo-700 bg-gray-50/60 hover:bg-white focus:bg-white border border-transparent hover:border-gray-200 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-200 rounded px-2 py-1 transition" value="0" autocomplete="off">
            </td>
            
            {{-- PPU (4) --}}
            <td class="px-1.5 py-2 align-middle bg-emerald-50/10 col-group-ppu">
                <input type="text" name="details[${rowCount}][jht_biaya]" class="w-full text-right font-mono text-xs input-jht-biaya font-semibold text-emerald-700 bg-gray-50/60 hover:bg-white focus:bg-white border border-transparent hover:border-gray-200 focus:border-emerald-400 focus:ring-1 focus:ring-emerald-200 rounded px-2 py-1 transition" value="0" autocomplete="off">
            </td>
            <td class="px-1.5 py-2 align-middle bg-emerald-50/10 col-group-ppu">
                <input type="text" name="details[${rowCount}][jht_hutang]" class="w-full text-right font-mono text-xs input-jht-hutang font-semibold text-emerald-700 bg-gray-50/60 hover:bg-white focus:bg-white border border-transparent hover:border-gray-200 focus:border-emerald-400 focus:ring-1 focus:ring-emerald-200 rounded px-2 py-1 transition" value="0" autocomplete="off">
            </td>
            <td class="px-1.5 py-2 align-middle bg-emerald-50/10 col-group-ppu">
                <input type="text" name="details[${rowCount}][jkk_tunjangan]" class="w-full text-right font-mono text-xs input-jkk font-semibold text-emerald-700 bg-gray-50/60 hover:bg-white focus:bg-white border border-transparent hover:border-gray-200 focus:border-emerald-400 focus:ring-1 focus:ring-emerald-200 rounded px-2 py-1 transition" value="0" autocomplete="off">
            </td>
            <td class="px-1.5 py-2 align-middle bg-emerald-50/10 border-r border-emerald-100 col-group-ppu">
                <input type="text" name="details[${rowCount}][jkm_tunjangan]" class="w-full text-right font-mono text-xs input-jkm font-semibold text-emerald-700 bg-gray-50/60 hover:bg-white focus:bg-white border border-transparent hover:border-gray-200 focus:border-emerald-400 focus:ring-1 focus:ring-emerald-200 rounded px-2 py-1 transition" value="0" autocomplete="off">
            </td>

            {{-- BPU-CREW (3) --}}
            <td class="px-1.5 py-2 align-middle bg-rose-50/10 col-group-bpu-crew">
                <input type="text" name="details[${rowCount}][jkk_hutang]" class="w-full text-right font-mono text-xs input-jkk-hutang font-semibold text-rose-600 bg-gray-50/60 hover:bg-white focus:bg-white border border-transparent hover:border-gray-200 focus:border-rose-400 focus:ring-1 focus:ring-rose-200 rounded px-2 py-1 transition" value="0" autocomplete="off">
            </td>
            <td class="px-1.5 py-2 align-middle bg-sky-50/10 col-group-bpu-crew">
                <input type="text" name="details[${rowCount}][bpu_jkk_tunjangan]" class="w-full text-right font-mono text-xs input-bpu-jkk font-semibold text-sky-700 bg-gray-50/60 hover:bg-white focus:bg-white border border-transparent hover:border-gray-200 focus:border-sky-400 focus:ring-1 focus:ring-sky-200 rounded px-2 py-1 transition" value="0" autocomplete="off">
            </td>
            <td class="px-1.5 py-2 align-middle bg-sky-50/10 border-r border-sky-100 col-group-bpu-crew">
                <input type="text" name="details[${rowCount}][bpu_jkm]" class="w-full text-right font-mono text-xs input-bpu-jkm font-semibold text-sky-700 bg-gray-50/60 hover:bg-white focus:bg-white border border-transparent hover:border-gray-200 focus:border-sky-400 focus:ring-1 focus:ring-sky-200 rounded px-2 py-1 transition" value="0" autocomplete="off">
            </td>

            {{-- Non BPU-CREW (4) --}}
            <td class="px-1.5 py-2 align-middle bg-purple-50/10 col-group-bpu-noncrew">
                <input type="text" name="details[${rowCount}][noncrew_jht_biaya]" class="w-full text-right font-mono text-xs input-noncrew-jht-biaya font-semibold text-purple-700 bg-gray-50/60 hover:bg-white focus:bg-white border border-transparent hover:border-gray-200 focus:border-purple-400 focus:ring-1 focus:ring-purple-200 rounded px-2 py-1 transition" value="0" autocomplete="off">
            </td>
            <td class="px-1.5 py-2 align-middle bg-purple-50/10 col-group-bpu-noncrew">
                <input type="text" name="details[${rowCount}][noncrew_jht_hutang]" class="w-full text-right font-mono text-xs input-noncrew-jht-hutang font-semibold text-purple-700 bg-gray-50/60 hover:bg-white focus:bg-white border border-transparent hover:border-gray-200 focus:border-purple-400 focus:ring-1 focus:ring-purple-200 rounded px-2 py-1 transition" value="0" autocomplete="off">
            </td>
            <td class="px-1.5 py-2 align-middle bg-purple-50/10 col-group-bpu-noncrew">
                <input type="text" name="details[${rowCount}][noncrew_jkk_tunjangan]" class="w-full text-right font-mono text-xs input-noncrew-jkk font-semibold text-purple-700 bg-gray-50/60 hover:bg-white focus:bg-white border border-transparent hover:border-gray-200 focus:border-purple-400 focus:ring-1 focus:ring-purple-200 rounded px-2 py-1 transition" value="0" autocomplete="off">
            </td>
            <td class="px-1.5 py-2 align-middle bg-purple-50/10 border-r border-purple-100 col-group-bpu-noncrew">
                <input type="text" name="details[${rowCount}][noncrew_jkm_tunjangan]" class="w-full text-right font-mono text-xs input-noncrew-jkm font-semibold text-purple-700 bg-gray-50/60 hover:bg-white focus:bg-white border border-transparent hover:border-gray-200 focus:border-purple-400 focus:ring-1 focus:ring-purple-200 rounded px-2 py-1 transition" value="0" autocomplete="off">
            </td>
            
            {{-- JP (2) --}}
            <td class="px-1.5 py-2 align-middle bg-amber-50/10 col-group-jp">
                <input type="text" name="details[${rowCount}][jp_biaya]" class="w-full text-right font-mono text-xs input-jp-biaya font-semibold text-amber-700 bg-gray-50/60 hover:bg-white focus:bg-white border border-transparent hover:border-gray-200 focus:border-amber-400 focus:ring-1 focus:ring-amber-200 rounded px-2 py-1 transition" value="0" autocomplete="off">
            </td>
            <td class="px-1.5 py-2 align-middle bg-amber-50/10 border-r border-gray-200 col-group-jp">
                <input type="text" name="details[${rowCount}][jp_hutang]" class="w-full text-right font-mono text-xs input-jp-hutang font-semibold text-amber-700 bg-gray-50/60 hover:bg-white focus:bg-white border border-transparent hover:border-gray-200 focus:border-amber-400 focus:ring-1 focus:ring-amber-200 rounded px-2 py-1 transition" value="0" autocomplete="off">
            </td>
            
            {{-- Subtotal & Action --}}
            <td class="px-3 py-2 text-right font-mono font-bold align-middle subtotal-text text-teal-800 bg-teal-50/40 whitespace-nowrap">Rp 0</td>
            <td class="px-2 py-2 text-center align-middle">
                <button type="button" class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-rose-400 hover:text-rose-600 hover:bg-rose-50 transition-all btn-remove" title="Hapus Baris">
                    <i class="fas fa-trash-alt text-xs"></i>
                </button>
            </td>
        `;

        container.appendChild(tr);

        const inputKes     = tr.querySelector('.input-kes');
        const inputKet     = tr.querySelector('.input-ket');
        const subtotalText = tr.querySelector('.subtotal-text');
        const infoJkn      = tr.querySelector('.info-jkn');
        const groupText    = tr.querySelector('.group-text');
        const selectTipe   = tr.querySelector('.select-tipe-jkn');
        const selectTipeJamsostek = tr.querySelector('.select-tipe-bp-jamsostek');
        const inputJhtBiaya  = tr.querySelector('.input-jht-biaya');
        const inputJhtHutang = tr.querySelector('.input-jht-hutang');
        const inputJkk       = tr.querySelector('.input-jkk');
        const inputJkm       = tr.querySelector('.input-jkm');
        const inputBpuJht    = tr.querySelector('.input-jkk-hutang');
        const inputBpuJkk    = tr.querySelector('.input-bpu-jkk');
        const inputBpuJkm    = tr.querySelector('.input-bpu-jkm');
        const inputNcJhtBiaya = tr.querySelector('.input-noncrew-jht-biaya');
        const inputNcJhtHutang = tr.querySelector('.input-noncrew-jht-hutang');
        const inputNcJkk     = tr.querySelector('.input-noncrew-jkk');
        const inputNcJkm     = tr.querySelector('.input-noncrew-jkm');
        const inputJpBiaya   = tr.querySelector('.input-jp-biaya');
        const inputJpHutang  = tr.querySelector('.input-jp-hutang');
        
        const updateSubtotal = () => {
            const currentSelectedGroup = filterGroupSelect ? filterGroupSelect.value : 'all';
            const kes       = parseIdNumber(inputKes.value);
            const ket       = parseIdNumber(inputKet.value);
            const jhtB      = parseIdNumber(inputJhtBiaya.value);
            const jhtH      = parseIdNumber(inputJhtHutang.value);
            const jkk       = parseIdNumber(inputJkk.value);
            const jkm       = parseIdNumber(inputJkm.value);
            const bpuJht    = parseIdNumber(inputBpuJht.value);
            const bpuJkk    = parseIdNumber(inputBpuJkk.value);
            const bpuJkm    = parseIdNumber(inputBpuJkm.value);
            const ncJhtB    = parseIdNumber(inputNcJhtBiaya.value);
            const ncJhtH    = parseIdNumber(inputNcJhtHutang.value);
            const ncJkk     = parseIdNumber(inputNcJkk.value);
            const ncJkm     = parseIdNumber(inputNcJkm.value);
            const jpB       = parseIdNumber(inputJpBiaya.value);
            const jpH       = parseIdNumber(inputJpHutang.value);

            let rowTotal = 0;
            if (currentSelectedGroup === 'JKN-KIS') {
                rowTotal = kes + ket;
            } else if (currentSelectedGroup === 'PPU') {
                rowTotal = jhtB + jhtH + jkk + jkm + jpB + jpH;
            } else if (currentSelectedGroup === 'BPU-CREW') {
                rowTotal = bpuJht + bpuJkk + bpuJkm;
            } else if (currentSelectedGroup === 'BPU-NON-CREW') {
                rowTotal = ncJhtB + ncJhtH + ncJkk + ncJkm;
            } else {
                rowTotal = kes + ket + jhtB + jhtH + jkk + jkm + bpuJht + bpuJkk + bpuJkm + ncJhtB + ncJhtH + ncJkk + ncJkm + jpB + jpH;
            }

            subtotalText.innerText = 'Rp ' + formatNumber(rowTotal);
            applyFilter();
        };

        // Format saat kehilangan fokus atau nilai berubah (oleh sistem / user)
        [inputKes, inputKet, inputJhtBiaya, inputJhtHutang, inputJkk, inputJkm, inputBpuJht, inputBpuJkk, inputBpuJkm, inputNcJhtBiaya, inputNcJhtHutang, inputNcJkk, inputNcJkm, inputJpBiaya, inputJpHutang].forEach(function(input) {
            if (!input) return;
            input.addEventListener('change', function() {
                this.value = formatNumber(parseIdNumber(this.value));
                updateSubtotal();
            });
            input.addEventListener('input', updateSubtotal);
        });

        tr.querySelector('.btn-remove').addEventListener('click', function() {
            tr.remove();
            applyFilter();
        });
        
        let $select = null;
        if (!karyawanId && typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
            $select = jQuery(tr.querySelector('.select2')).select2({
                placeholder: "-- Pilih Karyawan --",
                width: '100%'
            });
        }

        function updateInfoBadgeJkn(kId) {
            const karyawan = karyawans.find(k => k.unique_id == kId || k.id == kId);
            if (!karyawan) { groupText.innerHTML = '-'; return; }

            let badges = [];

            if (karyawan.group_jkn) {
                badges.push(`<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-800" title="Group JKN">${karyawan.group_jkn}</span>`);
                selectTipe.options[0].text = `TOTAL JKN`;
            }

            if (karyawan.group_bp_jamsostek) {
                const isPpu = karyawan.group_bp_jamsostek.toUpperCase().includes('PPU');
                const isBpuCrew = karyawan.group_bp_jamsostek.toUpperCase().includes('BPU-CREW');
                let badgeClass = 'bg-purple-100 text-purple-800';
                if (isPpu) badgeClass = 'bg-emerald-100 text-emerald-800';
                else if (isBpuCrew) badgeClass = 'bg-sky-100 text-sky-800';

                let label = karyawan.group_bp_jamsostek;
                if (karyawan.cabang_bpjs) {
                    label += ` — ${karyawan.cabang_bpjs}`;
                }
                badges.push(`<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold ${badgeClass}" title="Group Jamsostek">${label}</span>`);
            } else if (karyawan.cabang_bpjs) {
                badges.push(`<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-800" title="Cabang BPJS">${karyawan.cabang_bpjs}</span>`);
            }

            groupText.innerHTML = badges.length > 0 ? badges.join('<br>') : '-';
        }

        function calculateBpjsForKaryawan(kId, tipeJkn) {
            if (!kId) {
                inputKes.value = 0;
                inputKet.value = 0;
                updateSubtotal();
                return;
            }

            const karyawan = karyawans.find(k => k.unique_id == kId || k.id == kId);
            if (!karyawan) return;

            let nominalKes = 0;
            let nominalKet = 0;

            if (karyawan.group_jkn && tipeJkn !== 'manual') {
                const normalizedKaryawanGroup = (karyawan.group_jkn || '').replace(/[\s\-_]+/g, '').toUpperCase();
                let rumus = rumusBpjs.find(r => r.jenis === 'jkn' && r.group_name === karyawan.group_jkn);
                if (!rumus) {
                    rumus = rumusBpjs.find(r => r.jenis === 'jkn' && (r.group_name || '').replace(/[\s\-_]+/g, '').toUpperCase() === normalizedKaryawanGroup);
                }
                if (!rumus && (normalizedKaryawanGroup.includes('NONKARY') || normalizedKaryawanGroup.includes('NONKARYAWAN'))) {
                    rumus = rumusBpjs.find(r => r.jenis === 'jkn' && (r.group_name || '').replace(/[\s\-_]+/g, '').toUpperCase().includes('NONKARY'));
                }

                if (rumus) {
                    const dpp = parseIdNumber(karyawan.dpp_jkn);
                    const tunjPersen = parseFloat(rumus.tunjangan_persen || 0);
                    const hutangPersen = parseFloat(rumus.hutang_persen || 0);
                    const biayaPersen = parseFloat(rumus.biaya_persen || 0);

                    const groupUpper = (karyawan.group_jkn || '').toUpperCase();
                    if (groupUpper.includes('NON KARY') || groupUpper.includes('NON-KARY') || groupUpper.includes('NONKARY')) {
                        // ── Rumus Group JKN-KIS-NON KARY: DPP Karyawan * Berapa Persen di Master Rumus ──
                        const totalPersen = tunjPersen > 0 ? tunjPersen : (biayaPersen > 0 ? biayaPersen : (hutangPersen > 0 ? hutangPersen : 0));
                        nominalKes = (totalPersen / 100) * dpp;
                        nominalKet = (hutangPersen > 0 && tunjPersen > 0) ? (hutangPersen / 100) * dpp : 0;
                    } else {
                        let persen = 0;
                        if (tipeJkn === 'tunjangan_hutang') {
                            persen = tunjPersen;
                            nominalKet += (hutangPersen / 100) * dpp;
                        }
                        nominalKes = (persen / 100) * dpp;
                    }
                }
            } else if (tipeJkn === 'manual') {
                nominalKes = parseIdNumber(inputKes.value);
            }

            if ((karyawan.group_bp_jamsostek || karyawan.cabang_bpjs) && tipeJkn !== 'manual') {
                const karyawanGroup = (karyawan.group_bp_jamsostek || '').trim().toUpperCase();
                const karyawanCabang = (karyawan.cabang_bpjs || '').trim().toLowerCase();

                const isPpu = karyawanGroup.includes('PPU');
                const isBpuCrew = !isPpu && karyawanGroup.includes('BPU-CREW');

                // Helper normalisasi string (hilangkan spasi, strip, underscore)
                const normalizeStr = str => (str || '').replace(/[\s\-_]+/g, '').toUpperCase();
                const normGroup = normalizeStr(karyawan.group_bp_jamsostek);

                let rumus = null;

                if (isPpu) {
                    if (karyawanGroup && karyawanCabang) {
                        rumus = rumusBpjs.find(r => r.jenis === 'jamsostek' && (r.group_name || '').trim().toUpperCase() === karyawanGroup && (r.cabang_bpjs || '').trim().toLowerCase() === karyawanCabang);
                    }
                    if (!rumus && normGroup && karyawanCabang) {
                        rumus = rumusBpjs.find(r => r.jenis === 'jamsostek' && normalizeStr(r.group_name) === normGroup && (r.cabang_bpjs || '').trim().toLowerCase() === karyawanCabang);
                    }
                    if (!rumus && karyawanGroup) {
                        rumus = rumusBpjs.find(r => r.jenis === 'jamsostek' && (r.group_name || '').trim().toUpperCase() === karyawanGroup);
                    }
                    if (!rumus && normGroup) {
                        rumus = rumusBpjs.find(r => r.jenis === 'jamsostek' && normalizeStr(r.group_name) === normGroup);
                    }
                    if (!rumus && karyawanCabang) {
                        rumus = rumusBpjs.find(r => r.jenis === 'jamsostek' && (r.group_name || '').toUpperCase().includes('PPU') && (r.cabang_bpjs || '').trim().toLowerCase() === karyawanCabang);
                    }
                    if (!rumus) {
                        rumus = rumusBpjs.find(r => r.jenis === 'jamsostek' && (r.group_name || '').toUpperCase().includes('PPU'));
                    }
                } else if (isBpuCrew) {
                    if (karyawanGroup && karyawanCabang) {
                        rumus = rumusBpjs.find(r => r.jenis === 'jamsostek' && (r.group_name || '').trim().toUpperCase() === karyawanGroup && (r.cabang_bpjs || '').trim().toLowerCase() === karyawanCabang);
                    }
                    if (!rumus && normGroup && karyawanCabang) {
                        rumus = rumusBpjs.find(r => r.jenis === 'jamsostek' && normalizeStr(r.group_name) === normGroup && (r.cabang_bpjs || '').trim().toLowerCase() === karyawanCabang);
                    }
                    if (!rumus && karyawanGroup) {
                        rumus = rumusBpjs.find(r => r.jenis === 'jamsostek' && (r.group_name || '').trim().toUpperCase() === karyawanGroup);
                    }
                    if (!rumus && normGroup) {
                        rumus = rumusBpjs.find(r => r.jenis === 'jamsostek' && normalizeStr(r.group_name) === normGroup);
                    }
                    if (!rumus && karyawanCabang) {
                        rumus = rumusBpjs.find(r => r.jenis === 'jamsostek' && (r.group_name || '').toUpperCase().includes('BPU-CREW') && !r.group_name.toUpperCase().includes('PPU') && (r.cabang_bpjs || '').trim().toLowerCase() === karyawanCabang);
                    }
                    if (!rumus) {
                        rumus = rumusBpjs.find(r => r.jenis === 'jamsostek' && (r.group_name || '').toUpperCase().includes('BPU-CREW') && !r.group_name.toUpperCase().includes('PPU'));
                    }
                } else {
                    // ── Non BPU-CREW (Termasuk BPU-NON KARY-UMKM NO PP, BPU-NON KARY-UMKM, BPU-NON KARY-PBM, dll) ──
                    // 1. Prioritas Utama: Cari yang SAMA PERSIS Group DAN Cabang BPJS
                    if (karyawanGroup && karyawanCabang) {
                        rumus = rumusBpjs.find(r => r.jenis === 'jamsostek' && (r.group_name || '').trim().toUpperCase() === karyawanGroup && (r.cabang_bpjs || '').trim().toLowerCase() === karyawanCabang);
                    }
                    // 2. Cari dengan Normalisasi Group DAN Cabang BPJS
                    if (!rumus && normGroup && karyawanCabang) {
                        rumus = rumusBpjs.find(r => r.jenis === 'jamsostek' && normalizeStr(r.group_name) === normGroup && (r.cabang_bpjs || '').trim().toLowerCase() === karyawanCabang);
                    }
                    // 3. Cari berdasarkan Group yang sama persis (jika di rumus cabang umum / kosong)
                    if (!rumus && karyawanGroup) {
                        rumus = rumusBpjs.find(r => r.jenis === 'jamsostek' && (r.group_name || '').trim().toUpperCase() === karyawanGroup);
                    }
                    // 4. Cari berdasarkan Normalisasi Group
                    if (!rumus && normGroup) {
                        rumus = rumusBpjs.find(r => r.jenis === 'jamsostek' && normalizeStr(r.group_name) === normGroup);
                    }
                    // 5. Fallback ke cabang yang sama untuk kategori Non BPU-Crew
                    if (!rumus && karyawanCabang) {
                        rumus = rumusBpjs.find(r => {
                            if (r.jenis !== 'jamsostek') return false;
                            const rGroup = (r.group_name || '').toUpperCase();
                            const isRNonCrew = !rGroup.includes('PPU') && !rGroup.includes('BPU-CREW');
                            if (!isRNonCrew) return false;
                            return (r.cabang_bpjs || '').trim().toLowerCase() === karyawanCabang;
                        });
                    }
                    // 6. Fallback rumus Non BPU-CREW umum
                    if (!rumus) {
                        rumus = rumusBpjs.find(r => {
                            if (r.jenis !== 'jamsostek') return false;
                            const rGroup = (r.group_name || '').toUpperCase();
                            return !rGroup.includes('PPU') && !rGroup.includes('BPU-CREW');
                        });
                    }
                }

                if (rumus) {
                    const dppJamsostek = parseIdNumber(karyawan.dpp_bp_jamsostek);
                    let jhtBiaya = 0, jhtHutang = 0, jkkTunj = 0, jkmTunj = 0;
                    let bpuJhtVal = 0, bpuJkkTunjVal = 0, bpuJkmVal = 0;
                    let ncJhtBiayaVal = 0, ncJhtHutangVal = 0, ncJkkTunjVal = 0, ncJkmTunjVal = 0;
                    let jpBiaya = 0, jpHutang = 0;
                    
                    const groupNameUpper = (rumus.group_name || '').toUpperCase();

                    if (groupNameUpper.includes('PPU')) {
                        const jhtBiayaMaster  = parseFloat(rumus.jht_biaya   || 0);
                        const jhtHutangMaster = parseFloat(rumus.jht_hutang  || 0);
                        
                        jhtHutang = (jhtHutangMaster / 100) * dppJamsostek;
                        jhtBiaya  = ((jhtBiayaMaster  / 100) * dppJamsostek) - jhtHutang;
                        
                        const jpBiayaMaster  = parseFloat(rumus.jp_biaya  || 0);
                        const jpHutangMaster = parseFloat(rumus.jp_hutang || 0);
                        const jpMaxDpp       = parseFloat(rumus.jp_max_dpp || 0);
                        const jpMaxAge       = parseFloat(rumus.jp_max_age || 0);
                        
                        // ── Hitung Usia Karyawan dari Tanggal Lahir ──
                        let usiaKaryawan = null;
                        if (karyawan.tanggal_lahir) {
                            const tglLahir = new Date(karyawan.tanggal_lahir);
                            if (!isNaN(tglLahir.getTime())) {
                                const tglPranotaInput = document.getElementById('tanggal_pranota')?.value;
                                const refDate = tglPranotaInput ? new Date(tglPranotaInput) : new Date();
                                
                                let age = refDate.getFullYear() - tglLahir.getFullYear();
                                const m = refDate.getMonth() - tglLahir.getMonth();
                                if (m < 0 || (m === 0 && refDate.getDate() < tglLahir.getDate())) {
                                    age--;
                                }
                                usiaKaryawan = age;
                            }
                        }

                        // ── Alur Logika Batas Usia & Maksimal DPP JP Group PPU ──
                        // Jika melewati batas umur yang ditentukan di rumus BPJS, PPU JP tidak dihitung (0)
                        if (jpMaxAge > 0 && usiaKaryawan !== null && usiaKaryawan >= jpMaxAge) {
                            jpBiaya  = 0;
                            jpHutang = 0;
                        } else {
                            // 1. Cek DPP BP Jamsostek apakah melebihi Maksimal DPP JP
                            let dppJp = dppJamsostek;
                            if (jpMaxDpp > 0 && dppJamsostek > jpMaxDpp) {
                                dppJp = jpMaxDpp;
                            }

                            // 2. PPU JP 1% (Hutang Karyawan)
                            jpHutang = (jpHutangMaster / 100) * dppJp;

                            // 3. PPU JP 2% (Biaya Perusahaan) = (JP Biaya % * dppJp) - PPU JP 1% (jpHutang)
                            jpBiaya = Math.max(0, ((jpBiayaMaster / 100) * dppJp) - jpHutang);
                        }

                        jkkTunj = (parseFloat(rumus.jkk_tunjangan || 0) / 100) * dppJamsostek;
                        jkmTunj = (parseFloat(rumus.jkm_tunjangan || 0) / 100) * dppJamsostek;

                    } else if (groupNameUpper.includes('BPU-CREW')) {
                        // ── BPU-CREW ────────────────────────────────────────────
                        const hutangPersen = parseFloat(rumus.hutang_persen || 0);
                        if (tipeJkn === 'tunjangan_hutang') {
                            nominalKet += (hutangPersen / 100) * dppJamsostek;
                        }

                        let tiers = rumus.hutang_tiers;
                        if (typeof tiers === 'string') {
                            try { tiers = JSON.parse(tiers); } catch(e) { tiers = []; }
                        }
                        if (Array.isArray(tiers) && tiers.length > 0) {
                            const matchedTier = tiers.find(t => parseFloat(t.dpp || 0) === dppJamsostek);
                            if (matchedTier) {
                                bpuJhtVal = parseFloat(matchedTier.potongan || 0);
                            } else {
                                bpuJhtVal = tiers.reduce((sum, t) => sum + parseFloat(t.potongan || 0), 0);
                            }
                        }

                        // BPU JKK 1% (Tunjangan)
                        const tunjPersenMaster = parseFloat(rumus.tunjangan_persen || 0);
                        let baseTunjangan = (tunjPersenMaster / 100) * dppJamsostek;

                        if (rumus.diskon_status === 'ada' && parseFloat(rumus.diskon_nilai || 0) > 0) {
                            const diskonNilai = parseFloat(rumus.diskon_nilai || 0);
                            const diskonTipe  = (rumus.diskon_tipe || 'persen').toLowerCase();
                            if (diskonTipe === 'persen') {
                                baseTunjangan = baseTunjangan * (diskonNilai / 100);
                            } else {
                                baseTunjangan = baseTunjangan * diskonNilai;
                            }
                        }

                        bpuJkkTunjVal = Math.max(0, baseTunjangan - bpuJhtVal);

                        // BPU JKM
                        const biayaMaster = parseFloat(rumus.biaya_persen || 0);
                        bpuJkmVal = biayaMaster;

                        if (rumus.diskon_status === 'ada' && parseFloat(rumus.diskon_nilai || 0) > 0) {
                            const diskonNilai = parseFloat(rumus.diskon_nilai || 0);
                            const diskonTipe  = (rumus.diskon_tipe || 'persen').toLowerCase();
                            if (diskonTipe === 'persen') {
                                bpuJkmVal = biayaMaster * (diskonNilai / 100);
                            } else {
                                bpuJkmVal = biayaMaster * diskonNilai;
                            }
                        }
                    } else {
                        // ── Non BPU-CREW ─────────────────────────────────────────
                        // 1. JHT 2% Hutang / Potongan (Rp) dari Tabel DPP Tier atau Persen
                        let tiers = rumus.hutang_tiers;
                        if (typeof tiers === 'string') {
                            try { tiers = JSON.parse(tiers); } catch(e) { tiers = []; }
                        }
                        if (Array.isArray(tiers) && tiers.length > 0) {
                            const matchedTier = tiers.find(t => parseFloat(t.dpp || 0) === dppJamsostek);
                            if (matchedTier) {
                                ncJhtHutangVal = parseFloat(matchedTier.potongan || 0);
                            } else {
                                ncJhtHutangVal = tiers.reduce((sum, t) => sum + parseFloat(t.potongan || 0), 0);
                            }
                        } else {
                            const jhtHutangPersen = parseFloat(rumus.jht_hutang || rumus.hutang_persen || 0);
                            ncJhtHutangVal = (jhtHutangPersen / 100) * dppJamsostek;
                        }

                        // 2. JHT 2% Biaya: (DPP * JHT 2% Biaya Master Rumus) - Potongan JHT 2% (Tunjangan/Hutang Karyawan)
                        const jhtBiayaPersen = parseFloat(rumus.jht_biaya || 0);
                        const baseJhtBiaya = (jhtBiayaPersen / 100) * dppJamsostek;
                        ncJhtBiayaVal = Math.max(0, baseJhtBiaya - ncJhtHutangVal);

                        // 3. JKK 1% Tunjangan (%)
                        const jkkPersen = parseFloat(rumus.jkk_tunjangan || rumus.tunjangan_persen || 0);
                        let baseJkk = (jkkPersen / 100) * dppJamsostek;
                        if (rumus.diskon_status === 'ada' && parseFloat(rumus.diskon_nilai || 0) > 0) {
                            const diskonNilai = parseFloat(rumus.diskon_nilai || 0);
                            const diskonTipe  = (rumus.diskon_tipe || 'persen').toLowerCase();
                            if (diskonTipe === 'persen') {
                                baseJkk = baseJkk * (diskonNilai / 100);
                            } else {
                                baseJkk = baseJkk * diskonNilai;
                            }
                        }
                        ncJkkTunjVal = baseJkk;

                        // 4. JKM Tunjangan (Nominal Rp)
                        let baseJkm = parseFloat(rumus.jkm_tunjangan || rumus.biaya_persen || 0);
                        if (rumus.diskon_status === 'ada' && parseFloat(rumus.diskon_nilai || 0) > 0) {
                            const diskonNilai = parseFloat(rumus.diskon_nilai || 0);
                            const diskonTipe  = (rumus.diskon_tipe || 'persen').toLowerCase();
                            if (diskonTipe === 'persen') {
                                baseJkm = baseJkm * (diskonNilai / 100);
                            } else {
                                baseJkm = baseJkm * diskonNilai;
                            }
                        }
                        ncJkmTunjVal = baseJkm;
                    }
                    
                    inputJhtBiaya.value     = formatNumber(jhtBiaya);
                    inputJhtHutang.value    = formatNumber(jhtHutang);
                    inputJkk.value          = formatNumber(jkkTunj);
                    inputJkm.value          = formatNumber(jkmTunj);
                    inputBpuJht.value       = formatNumber(Math.round(bpuJhtVal));
                    inputBpuJkk.value       = formatNumber(Math.round(bpuJkkTunjVal));
                    inputBpuJkm.value       = formatNumber(Math.round(bpuJkmVal));
                    inputNcJhtBiaya.value   = formatNumber(Math.round(ncJhtBiayaVal));
                    inputNcJhtHutang.value  = formatNumber(Math.round(ncJhtHutangVal));
                    inputNcJkk.value        = formatNumber(Math.round(ncJkkTunjVal));
                    inputNcJkm.value        = formatNumber(Math.round(ncJkmTunjVal));
                    inputJpBiaya.value      = formatNumber(jpBiaya);
                    inputJpHutang.value     = formatNumber(jpHutang);
                }
            } else {
                inputJhtBiaya.value     = 0;
                inputJhtHutang.value    = 0;
                inputJkk.value          = 0;
                inputJkm.value          = 0;
                inputBpuJht.value       = 0;
                inputBpuJkk.value       = 0;
                inputBpuJkm.value       = 0;
                inputNcJhtBiaya.value   = 0;
                inputNcJhtHutang.value  = 0;
                inputNcJkk.value        = 0;
                inputNcJkm.value        = 0;
                inputJpBiaya.value      = 0;
                inputJpHutang.value     = 0;
            }

            if (tipeJkn !== 'manual') {
                inputKes.value = formatNumber(Math.round(nominalKes));
            }
            
            inputKet.value = formatNumber(Math.round(nominalKet));
            updateSubtotal();
        }

        const handleKaryawanChange = function(kId) {
            const k = karyawans.find(item => item.unique_id == kId || item.id == kId);
            const hiddenKaryawanId = tr.querySelector('.karyawan-hidden-input');
            const hiddenTipeKaryawan = tr.querySelector('.tipe-karyawan-hidden-input');

            if (k) {
                if (hiddenKaryawanId) hiddenKaryawanId.value = k.id;
                if (hiddenTipeKaryawan) hiddenTipeKaryawan.value = k.tipe_karyawan || 'App\\Models\\Karyawan';
                tr.dataset.karyawanId = k.id;
                tr.dataset.tipeKaryawan = k.tipe_karyawan || 'App\\Models\\Karyawan';
                tr.dataset.uniqueId = k.unique_id || '';
                tr.dataset.namaKaryawan = (k.nama_lengkap || '').toLowerCase();
                tr.dataset.nik = k.nik || '';
                tr.dataset.nikKtp = k.nik_ktp || '';
                tr.dataset.noBpjs = k.no_bpjs || '';
                tr.dataset.groupPosisi = k.group_posisi || 'Crew';
                tr.dataset.lokasi = k.lokasi || 'JKT';
                tr.dataset.groupJkn = k.group_jkn || '';
                tr.dataset.groupJamsostek = k.group_bp_jamsostek || '';
                tr.dataset.cabang = k.cabang_bpjs || '';
                tr.dataset.dppJkn = k.dpp_jkn || 0;
                tr.dataset.dppJamsostek = k.dpp_bp_jamsostek || 0;
            } else {
                if (hiddenKaryawanId) hiddenKaryawanId.value = '';
                if (hiddenTipeKaryawan) hiddenTipeKaryawan.value = 'App\\Models\\Karyawan';
                tr.dataset.karyawanId = '';
                tr.dataset.tipeKaryawan = '';
                tr.dataset.uniqueId = '';
                tr.dataset.namaKaryawan = '';
                tr.dataset.nik = '';
                tr.dataset.nikKtp = '';
                tr.dataset.noBpjs = '';
                tr.dataset.groupPosisi = '';
                tr.dataset.lokasi = '';
                tr.dataset.groupJkn = '';
                tr.dataset.groupJamsostek = '';
                tr.dataset.cabang = '';
                tr.dataset.dppJkn = 0;
                tr.dataset.dppJamsostek = 0;
            }
            const nikDisplay = tr.querySelector('.nik-display');
            if (nikDisplay) {
                if (k && k.nik) {
                    const statusBadge = k.tipe_label === 'Tidak Tetap' ? ' <span class="px-1 py-0.5 rounded text-[9px] font-bold bg-amber-100 text-amber-800">Tidak Tetap</span>' : '';
                    nikDisplay.innerHTML = `NIK: ${k.nik} ${statusBadge}`;
                    nikDisplay.classList.remove('hidden');
                } else if (k && k.tipe_label === 'Tidak Tetap') {
                    nikDisplay.innerHTML = `<span class="px-1 py-0.5 rounded text-[9px] font-bold bg-amber-100 text-amber-800">Tidak Tetap</span>`;
                    nikDisplay.classList.remove('hidden');
                } else {
                    nikDisplay.classList.add('hidden');
                }
            }
            updateInfoBadgeJkn(kId);
            calculateBpjsForKaryawan(kId, selectTipe.value);
            applyFilter();
        };

        if ($select) { 
            $select.on('change', function() { handleKaryawanChange($select.val()); }); 
        } else if (!karyawanId) { 
            tr.querySelector('.select-karyawan')?.addEventListener('change', function(e) { handleKaryawanChange(e.target.value); }); 
        }

        selectTipe.addEventListener('change', function() {
            const kId = karyawanId ? karyawanId : ($select ? $select.val() : tr.querySelector('.select-karyawan')?.value);
            calculateBpjsForKaryawan(kId, this.value);
        });

        if (karyawanId) {
            setTimeout(() => {
                selectTipe.value = 'tunjangan_hutang';
                updateInfoBadgeJkn(karyawanId);
                if (autoCalculate) {
                    calculateBpjsForKaryawan(karyawanId, 'tunjangan_hutang');
                }
            }, 100);
        }

        applyFilter();
    }

    // Filter event listeners
    if (filterGroupSelect) {
        filterGroupSelect.addEventListener('change', applyFilter);
    }
    if (filterCabangSelect) {
        filterCabangSelect.addEventListener('change', applyFilter);
    }
    if (searchKaryawanInput) {
        searchKaryawanInput.addEventListener('input', applyFilter);
    }
    if (btnClearSearch) {
        btnClearSearch.addEventListener('click', () => {
            searchKaryawanInput.value = '';
            applyFilter();
            searchKaryawanInput.focus();
        });
    }

    function resetAllFilters() {
        if (filterGroupSelect) filterGroupSelect.value = 'all';
        if (filterCabangSelect) filterCabangSelect.value = 'all';
        if (searchKaryawanInput) searchKaryawanInput.value = '';
        applyFilter();
    }

    if (btnResetFilter) {
        btnResetFilter.addEventListener('click', resetAllFilters);
    }
    if (btnResetFilterEmpty) {
        btnResetFilterEmpty.addEventListener('click', resetAllFilters);
    }

    btnAdd.addEventListener('click', () => addRow(null));

    // Bersihkan format angka Indonesia sebelum submit
    document.getElementById('pranota-form').addEventListener('submit', function(e) {
        document.querySelectorAll(
            '.input-kes, .input-ket, .input-jht-biaya, .input-jht-hutang, ' +
            '.input-jkk, .input-jkk-hutang, .input-jkm, .input-bpu-jkk, .input-bpu-jkm, ' +
            '.input-noncrew-jht-biaya, .input-noncrew-jht-hutang, .input-noncrew-jkk, .input-noncrew-jkm, ' +
            '.input-jp-biaya, .input-jp-hutang'
        ).forEach(function(input) {
            input.value = parseIdNumber(input.value);
        });
    });
    
    document.getElementById('btn-generate-all').addEventListener('click', () => {
        container.innerHTML = '';
        rowCount = 0;

        let count = 0;
        karyawans.forEach(k => {
            if (k.group_jkn || k.group_bp_jamsostek || k.cabang_bpjs) {
                addRow(k.unique_id, true);
                count++;
            }
        });
        
        if (count === 0) {
            addRow(null);
        }
        
        applyFilter();
    });

    // Export to Excel function using ExcelJS
    async function exportToExcel() {
        const rows = container.querySelectorAll('tr.detail-row');
        if (rows.length === 0) {
            alert('Belum ada data karyawan di tabel untuk diekspor!');
            return;
        }

        const visibleRows = Array.from(rows).filter(tr => !tr.classList.contains('hidden'));
        const exportRows = visibleRows.length > 0 ? visibleRows : Array.from(rows);

        if (typeof ExcelJS === 'undefined') {
            alert('Library ExcelJS sedang dimuat, silakan coba beberapa saat lagi.');
            return;
        }

        const workbook = new ExcelJS.Workbook();
        workbook.creator = 'AYPSIS';
        workbook.created = new Date();

        const worksheet = workbook.addWorksheet('BPU', {
            views: [{ showGridLines: true }]
        });

        // Define columns
        worksheet.columns = [
            { header: 'NIK. KAR', key: 'nik_kar', width: 14 },
            { header: 'NAMA KARYAWAN', key: 'nama_karyawan', width: 34 },
            { header: 'No. BPJS', key: 'no_bpjs', width: 20 },
            { header: 'NIK KTI', key: 'nik_ktp', width: 22 },
            { header: 'Group', key: 'group', width: 14 },
            { header: 'Tipe', key: 'tipe', width: 28 },
            { header: 'Lokasi', key: 'lokasi', width: 12 },
            { header: 'x', key: 'x', width: 6 },
            { header: 'DASAR UPAH', key: 'dasar_upah', width: 18 },
            { header: 'BPU JHT Kary', key: 'bpu_jht', width: 16 },
            { header: 'BPU JKK 1%', key: 'bpu_jkk', width: 16 },
            { header: 'BPU JKM', key: 'bpu_jkm', width: 14 },
            { header: 'TOTAL BPU', key: 'total_bpu', width: 18 }
        ];

        // Format header row
        const headerRow = worksheet.getRow(1);
        headerRow.height = 26;

        headerRow.eachCell((cell, colNumber) => {
            cell.font = { name: 'Calibri', size: 10, bold: true, color: { argb: 'FF000000' } };
            cell.alignment = { vertical: 'middle', horizontal: 'center' };
            cell.border = {
                top: { style: 'thin', color: { argb: 'FF808080' } },
                left: { style: 'thin', color: { argb: 'FF808080' } },
                bottom: { style: 'thin', color: { argb: 'FF808080' } },
                right: { style: 'thin', color: { argb: 'FF808080' } }
            };

            // Colors based on template image
            if (colNumber === 10) { // Column J: BPU JHT Kary (Red background, white bold font)
                cell.fill = {
                    type: 'pattern',
                    pattern: 'solid',
                    fgColor: { argb: 'FFFF0000' }
                };
                cell.font = { name: 'Calibri', size: 10, bold: true, color: { argb: 'FFFFFFFF' } };
            } else if (colNumber === 11) { // Column K: BPU JKK 1% (Light Green background, black bold font)
                cell.fill = {
                    type: 'pattern',
                    pattern: 'solid',
                    fgColor: { argb: 'FF92D050' }
                };
                cell.font = { name: 'Calibri', size: 10, bold: true, color: { argb: 'FF000000' } };
            } else if (colNumber === 12) { // Column L: BPU JKM (Green background, white bold font)
                cell.fill = {
                    type: 'pattern',
                    pattern: 'solid',
                    fgColor: { argb: 'FF70AD47' }
                };
                cell.font = { name: 'Calibri', size: 10, bold: true, color: { argb: 'FFFFFFFF' } };
            } else {
                cell.fill = {
                    type: 'pattern',
                    pattern: 'solid',
                    fgColor: { argb: 'FFF2F2F2' }
                };
            }
        });

        // Add data rows
        exportRows.forEach((tr, index) => {
            const kId = tr.dataset.karyawanId || tr.dataset.uniqueId;
            const k = karyawans.find(item => item.unique_id == kId || item.id == kId) || {};

            const nikKar = k.nik || tr.dataset.nik || '';
            const namaKaryawan = (k.nama_lengkap || tr.dataset.namaKaryawan || '').toUpperCase();
            const noBpjs = k.no_bpjs || tr.dataset.noBpjs || '';
            const nikKtp = k.nik_ktp || tr.dataset.nikKtp || '';
            const groupVal = k.group_posisi || tr.dataset.groupPosisi || 'Crew';
            
            let tipeVal = k.group_bp_jamsostek || tr.dataset.groupJamsostek || '';
            if (!tipeVal) {
                tipeVal = k.cabang_bpjs || tr.dataset.cabang || 'HL ALEXINDO CREW';
            }
            
            const lokasiVal = k.lokasi || tr.dataset.lokasi || 'JKT';

            // Dasar Upah: ambil dari DPP Jamsostek atau DPP JKN karyawan
            let dasarUpah = parseIdNumber(k.dpp_bp_jamsostek || tr.dataset.dppJamsostek || k.dpp_jkn || tr.dataset.dppJkn || 0);

            // BPU Values
            const bpuJhtVal = parseIdNumber(tr.querySelector('.input-jkk-hutang')?.value || tr.querySelector('.input-noncrew-jht-hutang')?.value || tr.querySelector('.input-jht-hutang')?.value || 0);
            const bpuJkkVal = parseIdNumber(tr.querySelector('.input-bpu-jkk')?.value || tr.querySelector('.input-noncrew-jkk')?.value || tr.querySelector('.input-jkk')?.value || 0);
            const bpuJkmVal = parseIdNumber(tr.querySelector('.input-bpu-jkm')?.value || tr.querySelector('.input-noncrew-jkm')?.value || tr.querySelector('.input-jkm')?.value || 0);
            const totalBpuVal = bpuJhtVal + bpuJkkVal + bpuJkmVal;

            const rowNum = index + 2; // Data starts on Excel row 2

            const row = worksheet.addRow({
                nik_kar: String(nikKar),
                nama_karyawan: namaKaryawan,
                no_bpjs: String(noBpjs),
                nik_ktp: String(nikKtp),
                group: groupVal,
                tipe: tipeVal,
                lokasi: lokasiVal,
                x: '',
                dasar_upah: dasarUpah,
                bpu_jht: bpuJhtVal,
                bpu_jkk: bpuJkkVal,
                bpu_jkm: bpuJkmVal,
                total_bpu: { formula: `J${rowNum}+K${rowNum}+L${rowNum}`, result: totalBpuVal }
            });

            row.height = 20;

            const thinBorder = {
                top: { style: 'thin', color: { argb: 'FFD3D3D3' } },
                left: { style: 'thin', color: { argb: 'FFD3D3D3' } },
                bottom: { style: 'thin', color: { argb: 'FFD3D3D3' } },
                right: { style: 'thin', color: { argb: 'FFD3D3D3' } }
            };

            row.eachCell({ includeEmpty: true }, (cell, colNumber) => {
                cell.font = { name: 'Calibri', size: 10 };
                cell.border = thinBorder;

                if (colNumber === 1) { // NIK KAR
                    cell.numFmt = '@';
                    cell.alignment = { vertical: 'middle', horizontal: 'center' };
                } else if (colNumber === 2) { // NAMA KARYAWAN
                    cell.alignment = { vertical: 'middle', horizontal: 'left' };
                } else if (colNumber === 3 || colNumber === 4) { // No BPJS, NIK KTP
                    cell.numFmt = '@';
                    cell.alignment = { vertical: 'middle', horizontal: 'left' };
                } else if (colNumber === 5 || colNumber === 7 || colNumber === 8) { // Group, Lokasi, x
                    cell.alignment = { vertical: 'middle', horizontal: 'center' };
                } else if (colNumber === 6) { // Tipe
                    cell.alignment = { vertical: 'middle', horizontal: 'left' };
                } else if (colNumber >= 9 && colNumber <= 13) { // DASAR UPAH, BPU JHT, BPU JKK, BPU JKM, TOTAL BPU
                    cell.numFmt = '#,##0.00';
                    cell.alignment = { vertical: 'middle', horizontal: 'right' };
                }
            });
        });

        // Set auto filter on header row
        worksheet.autoFilter = {
            from: { row: 1, column: 1 },
            to: { row: 1, column: 13 }
        };

        const bulanInput = document.querySelector('select[name="periode_bulan"]')?.value || (new Date().getMonth() + 1);
        const tahunInput = document.querySelector('input[name="periode_tahun"]')?.value || new Date().getFullYear();
        const tglInput = document.querySelector('input[name="tanggal_pranota"]')?.value || new Date().toISOString().split('T')[0];
        
        const buffer = await workbook.xlsx.writeBuffer();
        const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
        const filename = `Pranota_BPJS_BPU_Periode_${bulanInput}_${tahunInput}_${tglInput}.xlsx`;
        
        if (typeof saveAs !== 'undefined') {
            saveAs(blob, filename);
        } else {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        }
    }

    const btnExport = document.getElementById('btn-export-excel');
    if (btnExport) {
        btnExport.addEventListener('click', exportToExcel);
    }
    const btnExport2 = document.getElementById('btn-export-excel-2');
    if (btnExport2) {
        btnExport2.addEventListener('click', exportToExcel);
    }

    // Inisialisasi awal
    populateGroupFilterOptions();
    populateCabangFilterOptions();
    applyFilter();
});
</script>
@endsection
