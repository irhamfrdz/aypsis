@extends('layouts.app')

@section('title', 'Buat Broadcast WA')
@section('page_title', 'Buat Broadcast WhatsApp')

@section('content')
<div class="space-y-5 font-sans max-w-4xl mx-auto pb-10">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center space-x-3.5">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 shadow-sm flex-shrink-0">
                <i class="fab fa-whatsapp text-2xl"></i>
            </div>
            <div>
                <h1 class="text-lg font-bold text-slate-800 tracking-tight">
                    @if(in_array(request('type'), ['status_pengiriman', 'status']))
                        Buat Broadcast Status Pengiriman
                    @elseif(request('type') === 'kendala')
                        Buat Broadcast Kendala & Masalah
                    @elseif(request('type') === 'jadwal')
                        Buat Broadcast Jadwal Kapal
                    @else
                        Buat Broadcast WhatsApp
                    @endif
                </h1>
                <p class="text-xs text-slate-400 mt-0.5">Kirim pesan informasi jadwal kapal, status pengiriman muatan, atau kendala operasional ke shipper</p>
            </div>
        </div>
        <a href="{{ route('master.wa-broadcast.index') }}" class="inline-flex items-center px-4 py-2 text-xs font-semibold text-slate-600 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl transition-all shadow-sm">
            <i class="fas fa-arrow-left mr-2 text-slate-400 text-xs"></i>
            Kembali
        </a>
    </div>

    <form action="{{ route('master.wa-broadcast.store') }}" method="POST" id="broadcastForm">
        @csrf
        <input type="hidden" name="custom_phones_json" id="custom_phones_json">
        <input type="hidden" name="selected_shippers_json" id="selected_shippers_json">

        {{-- Step 1: Target Penerima (Shipper) --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 bg-slate-50/60">
                <div class="flex items-center gap-3">
                    <span class="step-badge w-6 h-6 rounded-full bg-emerald-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">1</span>
                    <div>
                        <h2 class="text-sm font-bold text-slate-800">Pilih Target Penerima Broadcast</h2>
                        <p class="text-[11px] text-slate-400">Variabel & form akan disesuaikan otomatis berdasarkan target yang dipilih</p>
                    </div>
                </div>
                <span id="targetModeBadge" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-sm">
                    <i class="fas fa-users text-emerald-500"></i> Semua Master Shipper
                </span>
            </div>
            <div class="p-5 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Option 1: Semua Data Shipper (Master: pengirims, master_pengirim_penerima, shipper_consignees) --}}
                    <label class="relative flex items-start p-4 border-2 rounded-2xl cursor-pointer transition-all hover:bg-emerald-50/30 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/40 border-slate-200" id="label-target-master">
                        <input type="radio" name="target_penerima" value="all_master_shippers" class="target-radio mt-0.5 text-emerald-600 focus:ring-emerald-500" {{ old('target_penerima', in_array(request('type'), ['kendala', 'status_pengiriman', 'status']) ? '' : 'all_master_shippers') == 'all_master_shippers' ? 'checked' : '' }}>
                        <div class="ml-3">
                            <span class="block text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                <i class="fas fa-address-book text-emerald-600"></i>
                                Semua Master Data Shipper
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-100 text-emerald-800">Mode Master Shipper</span>
                            </span>
                            <span class="block text-[11px] text-slate-500 mt-1 leading-relaxed">
                                Kirim broadcast ke seluruh shipper di database (<strong>Pengirim</strong>, <strong>Master PP</strong>, & <strong>Shipper Consignee</strong>). Input Kapal & Voyage serta Kendala otomatis dihilangkan.
                            </span>
                        </div>
                    </label>

                    {{-- Option 2: Shipper Khusus Manifest Kapal & Voyage --}}
                    <label class="relative flex items-start p-4 border-2 rounded-2xl cursor-pointer transition-all hover:bg-blue-50/30 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50/40 border-slate-200" id="label-target-manifest">
                        <input type="radio" name="target_penerima" value="manifest" class="target-radio mt-0.5 text-blue-600 focus:ring-blue-500" {{ old('target_penerima', in_array(request('type'), ['kendala', 'status_pengiriman', 'status']) ? 'manifest' : '') == 'manifest' ? 'checked' : '' }}>
                        <div class="ml-3">
                            <span class="block text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                <i class="fas fa-file-invoice text-blue-600"></i>
                                Shipper Manifest Kapal & Voyage
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-blue-100 text-blue-800">Mode Manifest Voyage</span>
                            </span>
                            <span class="block text-[11px] text-slate-500 mt-1 leading-relaxed">
                                Hanya shipper yang memiliki muatan / resi / kontainer aktif pada manifest kapal & voyage tertentu (Cocok untuk Status Pengiriman & Kendala).
                            </span>
                        </div>
                    </label>
                </div>

                {{-- Dynamic Active Variables Bar --}}
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                            <i class="fas fa-tags text-emerald-600"></i>
                            Variabel yang Digunakan (<span id="varCountLabel">6 Variabel Aktif</span>):
                        </span>
                        <span class="text-[11px] text-slate-400 italic" id="varSubtitleLabel">Data kapal & jadwal otomatis diambil dari Master Jadwal Kapal Berlabuh</span>
                    </div>
                    <div id="activeVariablesContainer" class="flex flex-wrap gap-1.5">
                        {{-- Injected by JS based on selected target --}}
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 2: Pilih Pelabuhan & Jadwal Kapal Berlabuh (Mode Master Shipper) --}}
        <div id="section-pelabuhan-jadwal" class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden transition-all">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100 bg-slate-50/60">
                <span class="step-badge w-6 h-6 rounded-full bg-emerald-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">2</span>
                <div>
                    <h2 class="text-sm font-bold text-slate-800">Pilih Pelabuhan & Jadwal Kapal Berlabuh</h2>
                    <p class="text-[11px] text-slate-400">Pilih pelabuhan untuk mencocokkan kapal dan jadwal (Closing, ETD, ETA) dari master data</p>
                </div>
            </div>
            <div class="p-5 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Pilih Pelabuhan --}}
                    <div>
                        <label for="pelabuhan" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Pilih Pelabuhan Tujuan / Rute <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
                                <i class="fas fa-anchor text-xs"></i>
                            </span>
                            <select name="pelabuhan" id="pelabuhan"
                                class="w-full pl-8 pr-3 py-2.5 text-xs font-medium text-slate-800 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 bg-white transition-all">
                                <option value="">-- Pilih Pelabuhan --</option>
                                @foreach($pelabuhans as $p)
                                    <option value="{{ $p }}" {{ old('pelabuhan', $selectedPelabuhan ?? '') == $p ? 'selected' : '' }}>{{ $p }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('pelabuhan') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Pilih Kapal & Jadwal Berlabuh --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="jadwal_id" class="block text-xs font-semibold text-slate-700">
                                Pilih Kapal & Jadwal di Pelabuhan Ini <span class="text-rose-500">*</span>
                            </label>
                            <button type="button" id="btn-select-all-ships" class="hidden inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600 hover:text-emerald-800 transition-colors">
                                <i class="fas fa-layer-group text-xs"></i> Kirim Semua Kapal Sekaligus
                            </button>
                        </div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
                                <i class="fas fa-ship text-xs"></i>
                            </span>
                            <select name="jadwal_id" id="jadwal_id"
                                class="w-full pl-8 pr-3 py-2.5 text-xs font-medium text-slate-800 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 bg-white transition-all disabled:bg-slate-50 disabled:text-slate-400"
                                disabled>
                                <option value="">-- Pilih Pelabuhan Terlebih Dahulu --</option>
                            </select>
                        </div>
                        @error('jadwal_id') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Schedule Details Card (Shown when a schedule is chosen) --}}
                <div id="scheduleDetailsCard" class="hidden p-4 rounded-xl bg-emerald-50/50 border border-emerald-200 transition-all">
                    <div class="flex items-center justify-between mb-3 border-b border-emerald-100 pb-2">
                        <span class="text-xs font-bold text-emerald-800 flex items-center gap-1.5">
                            <i class="fas fa-calendar-check text-emerald-600"></i>
                            <span id="label-detail-jadwal">Detail Jadwal Kapal Terpilih:</span>
                        </span>
                        <span id="badge-jadwal-pelabuhan" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">-</span>
                    </div>

                    {{-- Mode 1: Single Ship Details --}}
                    <div id="singleScheduleContainer" class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="bg-white p-2.5 rounded-lg border border-emerald-100 shadow-2xs">
                            <span class="block text-[10px] text-slate-400 font-medium">Nama Kapal</span>
                            <span class="block text-xs font-bold text-slate-800 mt-0.5 truncate" id="info-nama-kapal">-</span>
                        </div>
                        <div class="bg-white p-2.5 rounded-lg border border-emerald-100 shadow-2xs">
                            <span class="block text-[10px] text-amber-600 font-medium flex items-center gap-1">
                                <i class="fas fa-clock text-[9px]"></i> Tgl Closing
                            </span>
                            <span class="block text-xs font-bold text-amber-700 mt-0.5 font-mono" id="info-tgl-closing">-</span>
                        </div>
                        <div class="bg-white p-2.5 rounded-lg border border-emerald-100 shadow-2xs">
                            <span class="block text-[10px] text-blue-600 font-medium flex items-center gap-1">
                                <i class="fas fa-plane-departure text-[9px]"></i> Tgl ETD
                            </span>
                            <span class="block text-xs font-bold text-blue-700 mt-0.5 font-mono" id="info-tgl-etd">-</span>
                        </div>
                        <div class="bg-white p-2.5 rounded-lg border border-emerald-100 shadow-2xs">
                            <span class="block text-[10px] text-emerald-600 font-medium flex items-center gap-1">
                                <i class="fas fa-plane-arrival text-[9px]"></i> Tgl ETA
                            </span>
                            <span class="block text-xs font-bold text-emerald-700 mt-0.5 font-mono" id="info-tgl-eta">-</span>
                        </div>
                    </div>

                    {{-- Mode 2: All Ships List View --}}
                    <div id="allSchedulesContainer" class="hidden space-y-2.5">
                        <div class="flex items-center justify-between text-[11px] text-emerald-800 font-semibold bg-emerald-100/70 px-3 py-1.5 rounded-lg border border-emerald-200">
                            <span class="flex items-center gap-1.5">
                                <i class="fas fa-info-circle text-emerald-600"></i>
                                Format pesan otomatis menyertakan seluruh rincian jadwal kapal di bawah ini:
                            </span>
                            <span id="allSchedulesCountPill" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-600 text-white">0 Kapal</span>
                        </div>
                        <div id="allSchedulesList" class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 max-h-56 overflow-y-auto pr-1">
                            <!-- Injected by JS -->
                        </div>
                    </div>
                </div>

                {{-- Hidden Fields to submit schedule values --}}
                <input type="hidden" name="tanggal_closing" id="tanggal_closing">
                <input type="hidden" name="tanggal_etd" id="tanggal_etd">
                <input type="hidden" name="tanggal_eta" id="tanggal_eta">
            </div>
        </div>

        {{-- Step: Pilih Kapal & Voyage (Khusus Manifest) --}}
        <div id="section-kapal-voyage" class="hidden bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden transition-all">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100 bg-slate-50/60">
                <span class="step-badge w-6 h-6 rounded-full bg-emerald-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0" id="kapalStepBadge">2</span>
                <div>
                    <h2 class="text-sm font-bold text-slate-800">Pilih Kapal & Voyage (Khusus Manifest)</h2>
                    <p class="text-[11px] text-slate-400">Pilih kapal dan voyage manifest yang ingin dibroadcast</p>
                </div>
            </div>
            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Nama Kapal --}}
                <div>
                    <label for="nama_kapal" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Nama Kapal <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
                            <i class="fas fa-ship text-xs"></i>
                        </span>
                        <select name="nama_kapal" id="nama_kapal"
                            class="w-full pl-8 pr-3 py-2.5 text-xs font-medium text-slate-800 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 bg-white transition-all">
                            <option value="">-- Pilih Kapal --</option>
                            @foreach($kapals as $kapal)
                                <option value="{{ $kapal }}" {{ old('nama_kapal', $selectedKapal ?? '') == $kapal ? 'selected' : '' }}>{{ $kapal }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('nama_kapal') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- No Voyage --}}
                <div>
                    <label for="no_voyage" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        No. Voyage <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
                            <i class="fas fa-route text-xs"></i>
                        </span>
                        <select name="no_voyage" id="no_voyage"
                            class="w-full pl-8 pr-3 py-2.5 text-xs font-medium text-slate-800 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 bg-white transition-all disabled:bg-slate-50 disabled:text-slate-400"
                            {{ empty(old('nama_kapal', $selectedKapal ?? '')) ? 'disabled' : '' }}>
                            <option value="">{{ empty(old('nama_kapal', $selectedKapal ?? '')) ? '-- Pilih Kapal Terlebih Dahulu --' : '-- Pilih Voyage --' }}</option>
                            @foreach($voyages as $voyage)
                                <option value="{{ $voyage }}" {{ old('no_voyage', $selectedVoyage ?? '') == $voyage ? 'selected' : '' }}>{{ $voyage }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('no_voyage') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Recipient Preview Panel with Checkbox, Search & Pagination --}}
        <div id="recipient-panel" class="hidden bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-3.5 border-b border-slate-100 bg-slate-50/60">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100/70 text-emerald-700 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-users text-sm"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-xs font-bold text-slate-800">Daftar Shipper Penerima</h3>
                            <span id="selected-shippers-badge" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">0 Dipilih</span>
                        </div>
                        <p id="recipient-summary" class="text-[11px] text-slate-400 mt-0.5"></p>
                    </div>
                </div>

                {{-- Toolbar Actions: Quick Select & Search --}}
                <div class="flex items-center flex-wrap gap-2.5">
                    <div class="flex items-center gap-2 text-xs">
                        <button type="button" id="btn-select-all-shippers" class="text-[11px] font-semibold text-emerald-600 hover:text-emerald-800 underline transition-colors">Pilih Semua</button>
                        <span class="text-slate-300">|</span>
                        <button type="button" id="btn-deselect-all-shippers" class="text-[11px] font-semibold text-slate-500 hover:text-slate-700 underline transition-colors">Batal Semua</button>
                    </div>

                    <span id="recipient-loading" class="hidden text-[11px] text-slate-500">
                        <i class="fas fa-spinner fa-spin mr-1 text-emerald-500"></i> Memuat...
                    </span>
                    <div class="relative w-full sm:w-56">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
                            <i class="fas fa-search text-xs"></i>
                        </span>
                        <input type="text" id="recipient-search-input" placeholder="Cari shipper / no. WA..."
                            class="w-full pl-8 pr-7 py-1.5 text-xs font-medium rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 bg-white placeholder:text-slate-400 transition-all shadow-2xs">
                        <button type="button" id="recipient-search-clear" class="hidden absolute inset-y-0 right-0 flex items-center pr-2.5 text-slate-400 hover:text-slate-600 transition-colors">
                            <i class="fas fa-times-circle text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-semibold text-slate-500 uppercase tracking-wide">
                            <th class="px-4 py-3 text-center w-12">
                                <input type="checkbox" id="select-all-recipients-cb" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer h-4 w-4" title="Pilih Semua / Batal Semua">
                            </th>
                            <th class="px-3 py-3 text-center w-12">No</th>
                            <th class="px-5 py-3 text-left">Nama Shipper</th>
                            <th class="px-5 py-3 text-left min-w-[220px]">
                                <span>No. WhatsApp / Kontak</span>
                                <span class="text-[10px] text-emerald-600 font-normal ml-1">(Bisa diedit manual)</span>
                            </th>
                            <th class="px-5 py-3 text-left">Sumber Tabel</th>
                            <th class="px-5 py-3 text-right">Kontainer</th>
                        </tr>
                    </thead>
                    <tbody id="recipient-list" class="divide-y divide-slate-100">
                        {{-- Dimuat via AJAX, Checkbox & Paginasi --}}
                    </tbody>
                </table>
            </div>

            {{-- Pagination Bar --}}
            <div id="recipient-pagination-bar" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-3 border-t border-slate-100 bg-slate-50/50 text-xs text-slate-600">
                <div class="flex items-center gap-2 flex-wrap">
                    <span id="recipient-pagination-info" class="text-[11px] font-medium text-slate-500">
                        Menampilkan 0 - 0 dari 0 data
                    </span>
                    <div class="flex items-center gap-1.5 ml-2 text-[11px] text-slate-400">
                        <span>| Tampilkan</span>
                        <select id="recipient-page-size" class="py-0.5 px-2 text-[11px] font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg focus:border-emerald-500 focus:ring-1 focus:ring-emerald-200">
                            <option value="10" selected>10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span>per hal</span>
                    </div>
                </div>

                {{-- Page Buttons Container --}}
                <div id="recipient-pagination-controls" class="flex items-center gap-1 flex-wrap justify-center sm:justify-end">
                    {{-- Rendered by JS --}}
                </div>
            </div>
        </div>

        {{-- Step: Info Kendala / Masalah / Status Pengiriman (Dihilangkan jika memilih Semua Master Data Shipper) --}}
        <div id="section-kendala" class="hidden bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden transition-all">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100 bg-slate-50/60">
                <span class="step-badge w-6 h-6 rounded-full bg-emerald-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0" id="kendalaStepBadge">3</span>
                <div>
                    <h2 class="text-sm font-bold text-slate-800" id="kendalaHeaderTitle">Informasi Kendala & Masalah (Mode Manifest)</h2>
                    <p class="text-[11px] text-slate-400" id="kendalaHeaderSubtitle">Variabel masalah untuk pemberitahuan keterlambatan resi/kontainer manifest</p>
                </div>
            </div>
            <div class="p-5 space-y-4">
                {{-- Quick Status Chips (Aktif jika mode Status Pengiriman) --}}
                <div id="quickStatusOptions" class="hidden space-y-1.5">
                    <span class="text-[11px] font-semibold text-slate-500">Pilih Cepat Status Muatan:</span>
                    <div class="flex flex-wrap gap-1.5">
                        <button type="button" class="btn-quick-status px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 transition-colors" data-status="Kapal Berangkat (Sailing)" data-desc="Kapal telah diberangkatkan dari pelabuhan asal menuju pelabuhan tujuan.">
                            🚢 Kapal Berangkat (Sailing)
                        </button>
                        <button type="button" class="btn-quick-status px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 transition-colors" data-status="Kapal Tiba di Pelabuhan Tujuan" data-desc="Kapal telah tiba dan bersandar di pelabuhan tujuan.">
                            ⚓ Kapal Tiba / Sandar
                        </button>
                        <button type="button" class="btn-quick-status px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 transition-colors" data-status="Proses Pembongkaran Muatan" data-desc="Saat ini muatan kontainer sedang dalam proses pembongkaran dari kapal.">
                            🏗️ Proses Pembongkaran
                        </button>
                        <button type="button" class="btn-quick-status px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 transition-colors" data-status="Siap Diambil (Ready for Delivery)" data-desc="Muatan kontainer telah selesai dibongkar di depo/lapangan dan siap untuk diambil.">
                            📦 Siap Diambil (Delivery)
                        </button>
                        <button type="button" class="btn-quick-status px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 transition-colors" data-status="Selesai Diantar / Bongkar" data-desc="Seluruh proses pengiriman kontainer telah selesai dilaksanakan.">
                            ✅ Selesai Bongkar
                        </button>
                    </div>
                </div>

                {{-- Kategori Kendala / Status --}}
                <div>
                    <label for="kategori_masalah" id="kategoriLabel" class="block text-xs font-semibold text-slate-700 mb-1.5">Kategori Masalah / Jenis Kendala</label>
                    <input type="text" name="kategori_masalah" id="kategori_masalah" value="{{ old('kategori_masalah') }}"
                        class="w-full px-3.5 py-2.5 text-xs font-medium text-slate-800 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 transition-all placeholder:text-slate-300"
                        placeholder="Contoh: Cuaca Buruk / Antrian Dermaga / Kerusakan Mesin">
                    @error('kategori_masalah') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label for="deskripsi_masalah" id="deskripsiLabel" class="block text-xs font-semibold text-slate-700 mb-1.5">Deskripsi Masalah & Estimasi</label>
                    <textarea name="deskripsi_masalah" id="deskripsi_masalah" rows="3"
                        class="w-full px-3.5 py-2.5 text-xs font-medium text-slate-800 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 transition-all placeholder:text-slate-300 resize-none"
                        placeholder="Contoh: Keterlambatan diperkirakan sekitar 2 hari karena cuaca buruk di perairan...">{{ old('deskripsi_masalah') }}</textarea>
                    @error('deskripsi_masalah') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Step: Pilih Template WA --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden" id="section-template">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100 bg-slate-50/60">
                <span class="step-badge w-6 h-6 rounded-full bg-emerald-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0" id="templateStepBadge">2</span>
                <div>
                    <h2 class="text-sm font-bold text-slate-800">Pilih Template Pesan WA <span class="text-rose-500">*</span></h2>
                    <p class="text-[11px] text-slate-400">Pilihan template disesuaikan otomatis dengan target penerima</p>
                </div>
            </div>
            <div class="p-5 space-y-3">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
                        <i class="fas fa-file-alt text-xs"></i>
                    </span>
                    <select name="template_id" id="template_id"
                        class="w-full pl-8 pr-3 py-2.5 text-xs font-medium text-slate-800 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 bg-white transition-all" required>
                        <option value="">-- Pilih Template Pesan --</option>
                        {{-- Dimuat & difilter dinamis oleh JS --}}
                    </select>
                </div>
                @error('template_id') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror

                {{-- Template Preview Box --}}
                <div id="templatePreviewContainer" class="mt-3 text-[11px] bg-slate-50 border border-slate-200 rounded-xl p-3.5 space-y-2">
                    <div class="flex items-center justify-between font-bold text-slate-700">
                        <span class="flex items-center gap-1.5 text-emerald-700">
                            <i class="fab fa-whatsapp text-emerald-600"></i>
                            Preview Format Pesan Template:
                        </span>
                        <span id="selectedTemplateNameBadge" class="text-[10px] font-mono px-2 py-0.5 rounded bg-slate-200 text-slate-700">Jadwal Kapal</span>
                    </div>
                    <pre id="templatePreviewText" class="p-3 bg-white border border-slate-200 rounded-xl text-xs font-mono text-slate-700 whitespace-pre-wrap leading-relaxed max-h-36 overflow-y-auto select-all">-</pre>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('master.wa-broadcast.index') }}"
                class="inline-flex items-center px-5 py-2.5 text-xs font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl transition-all shadow-sm">
                <i class="fas fa-times mr-1.5 text-slate-400"></i>
                Batal
            </a>
            <button type="submit"
                class="inline-flex items-center px-6 py-2.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow transition-all">
                <i class="fab fa-whatsapp mr-2"></i>
                Preview & Kirim Pesan
            </button>
        </div>

    </form>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        const rawTemplates = @json($templatesJson ?? []);
        const defaultTemplateId = "{{ old('template_id', $defaultTemplateId ?? '') }}";
        const requestType = "{{ request('type', '') }}";

        const $pelabuhan         = $('#pelabuhan');
        const $jadwalId          = $('#jadwal_id');
        const $sectionPelabuhan  = $('#section-pelabuhan-jadwal');
        const $scheduleDetails   = $('#scheduleDetailsCard');
        const $infoNamaKapal     = $('#info-nama-kapal');
        const $infoTglClosing    = $('#info-tgl-closing');
        const $infoTglEtd        = $('#info-tgl-etd');
        const $infoTglEta        = $('#info-tgl-eta');
        const $badgeJadwalPelabuhan = $('#badge-jadwal-pelabuhan');
        const $tanggalClosing    = $('#tanggal_closing');
        const $tanggalEtd        = $('#tanggal_etd');
        const $tanggalEta        = $('#tanggal_eta');

        const $namaKapal         = $('#nama_kapal');
        const $noVoyage          = $('#no_voyage');
        const $templateId        = $('#template_id');
        const $sectionKapal      = $('#section-kapal-voyage');
        const $sectionKendala    = $('#section-kendala');
        const $kategoriMasalah   = $('#kategori_masalah');
        const $deskripsiMasalah  = $('#deskripsi_masalah');
        const $targetModeBadge   = $('#targetModeBadge');
        const $activeVarsBox     = $('#activeVariablesContainer');
        const $varCountLabel     = $('#varCountLabel');
        const $varSubtitleLabel  = $('#varSubtitleLabel');
        const $templateStepBadge = $('#templateStepBadge');
        const $templatePreviewText = $('#templatePreviewText');
        const $selectedTemplateNameBadge = $('#selectedTemplateNameBadge');

        const $recipientPanel    = $('#recipient-panel');
        const $recipientList     = $('#recipient-list');
        const $recipientSummary  = $('#recipient-summary');
        const $recipientLoading  = $('#recipient-loading');
        const $searchInput       = $('#recipient-search-input');
        const $searchClear       = $('#recipient-search-clear');
        const $pageSizeSelect    = $('#recipient-page-size');
        const $paginationInfo    = $('#recipient-pagination-info');
        const $paginationControls = $('#recipient-pagination-controls');
        const $selectAllCb       = $('#select-all-recipients-cb');
        const $selectedBadge     = $('#selected-shippers-badge');

        let recipientRequestId   = 0;
        let loadedRecipients     = [];
        let selectedShippers     = {};
        let customPhones         = {};
        let phoneOverrides       = {};  // Nomor WA yang sudah disimpan ke DB
        let currentPage          = 1;
        let pageSize             = 10;
        let phoneSaveTimers      = {};  // Debounce timers per shipper
        window.currentPortSchedules = [];

        // URL endpoint save-phone (tanpa CSRF di JS, dikirim via hidden field)
        const savePhoneUrl   = "{{ route('master.wa-broadcast.save-phone') }}";
        const getOverridesUrl = "{{ route('master.wa-broadcast.get-phone-overrides') }}";

        /**
         * Muat semua override nomor WA dari database saat halaman dibuka.
         */
        function loadPhoneOverridesFromDb() {
            $.get(getOverridesUrl, function(response) {
                if (response.success && response.overrides) {
                    phoneOverrides = response.overrides;
                    // Apply ke customPhones agar tampil langsung
                    $.each(phoneOverrides, function(name, phone) {
                        if (customPhones[name] === undefined) {
                            customPhones[name] = phone;
                        }
                    });
                    // Re-render jika recipient sudah dimuat
                    if (loadedRecipients.length > 0) {
                        renderRecipients();
                    }
                }
            });
        }

        function updateSelectionSummary() {
            const selectedList = Object.keys(selectedShippers).filter(k => selectedShippers[k]);
            const selectedCount = selectedList.length;
            const totalLoaded = loadedRecipients.length;

            $selectedBadge.text(selectedCount + ' Dipilih');
            $('#selected_shippers_json').val(JSON.stringify(selectedList));

            if (totalLoaded > 0 && selectedCount === totalLoaded) {
                $selectAllCb.prop('checked', true).prop('indeterminate', false);
            } else if (selectedCount > 0 && selectedCount < totalLoaded) {
                $selectAllCb.prop('checked', false).prop('indeterminate', true);
            } else {
                $selectAllCb.prop('checked', false).prop('indeterminate', false);
            }
        }

        /**
         * Get filtered recipients based on current search input
         */
        function getFilteredRecipients() {
            const query = ($searchInput.val() || '').trim().toLowerCase();
            if (!query) {
                $searchClear.addClass('hidden');
                return loadedRecipients;
            }
            $searchClear.removeClass('hidden');
            return loadedRecipients.filter(r => {
                const name   = (r.shipper_name || '').toLowerCase();
                const phone  = (customPhones[r.shipper_name] !== undefined ? customPhones[r.shipper_name] : (r.telepon || '')).toLowerCase();
                const source = (r.sumber_tabel || '').toLowerCase();
                return name.includes(query) || phone.includes(query) || source.includes(query);
            });
        }

        /**
         * Render filtered & paginated recipients list into table
         */
        function renderRecipients() {
            $recipientList.empty();
            const filtered   = getFilteredRecipients();
            const total      = filtered.length;
            const totalPages = Math.ceil(total / pageSize) || 1;

            if (currentPage > totalPages) {
                currentPage = totalPages;
            }
            if (currentPage < 1) {
                currentPage = 1;
            }

            const startIndex = (currentPage - 1) * pageSize;
            const endIndex   = Math.min(startIndex + pageSize, total);
            const pageData   = filtered.slice(startIndex, endIndex);

            const targetPenerima = $('input[name="target_penerima"]:checked').val() || 'all_master_shippers';
            const query = ($searchInput.val() || '').trim();

            if (targetPenerima === 'all_master_shippers') {
                $recipientSummary.text(total + ' shipper terdaftar (Master: Pengirim, Master Pengirim/Penerima & Shipper Consignee).');
            } else {
                $recipientSummary.text(total + ' shipper terdaftar pada manifest voyage ini.');
            }

            if (total > 0) {
                $paginationInfo.html('Menampilkan <strong class="text-slate-700">' + (startIndex + 1) + '</strong> - <strong class="text-slate-700">' + endIndex + '</strong> dari <strong class="text-slate-700">' + total + '</strong> data');
            } else {
                $paginationInfo.text('Tidak ada data shipper');
            }

            $.each(pageData, function(i, r) {
                const rowNumber    = startIndex + i + 1;
                const safeName     = r.shipper_name || '';
                const isSelected   = selectedShippers[safeName] !== false; // Default true if not explicitly false
                const currentPhone = (customPhones[safeName] !== undefined) ? customPhones[safeName] : (r.telepon || '');
                const hasPhone     = currentPhone && currentPhone !== '-';
                const $row         = $('<tr>').addClass('hover:bg-slate-50/70 transition-colors');

                // Checkbox Column
                $('<td>', { class: 'px-4 py-3 text-center' }).html(
                    $('<input>', {
                        type: 'checkbox',
                        class: 'recipient-row-cb rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer h-4 w-4',
                        'data-shipper-name': safeName,
                        checked: isSelected
                    })
                ).appendTo($row);

                // Row Number
                $('<td>', { class: 'px-3 py-3 text-center text-slate-400 font-mono text-[11px]' }).text(rowNumber).appendTo($row);
                
                // Shipper Name
                $('<td>', { class: 'px-5 py-3 font-semibold text-slate-800' }).text(safeName).appendTo($row);

                // Editable Phone Input Column
                const isOverridden = phoneOverrides.hasOwnProperty(safeName);
                const $phoneCell = $('<td>', { class: 'px-5 py-2.5' });
                const $inputWrapper = $('<div>', { class: 'relative w-full max-w-[240px]' });
                $('<span>', { class: 'absolute inset-y-0 left-0 flex items-center pl-2.5 text-slate-400 pointer-events-none' })
                    .html('<i class="fab fa-whatsapp ' + (hasPhone ? 'text-emerald-500' : 'text-slate-300') + ' text-xs"></i>')
                    .appendTo($inputWrapper);

                $('<input>', {
                    type: 'text',
                    class: 'recipient-phone-input w-full pl-7 pr-8 py-1 text-xs font-mono font-medium rounded-lg border border-slate-200 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-200 transition-all bg-white placeholder:text-slate-300 ' + (hasPhone ? 'text-emerald-800' : 'text-slate-500'),
                    value: currentPhone,
                    placeholder: 'Contoh: 08123456789',
                    'data-shipper-name': safeName
                }).appendTo($inputWrapper);

                // Save indicator (spinner / saved icon)
                $('<span>', {
                    class: 'phone-save-indicator absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none',
                    'data-shipper-name': safeName,
                    html: isOverridden
                        ? '<i class="fas fa-database text-[9px] text-emerald-500" title="Nomor tersimpan di database"></i>'
                        : ''
                }).appendTo($inputWrapper);

                $inputWrapper.appendTo($phoneCell);
                $phoneCell.appendTo($row);

                // Source Table — tambahkan badge override jika ada
                const displaySumber = isOverridden
                    ? '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800 font-medium border border-emerald-200 text-[10px]"><i class="fas fa-database text-[9px]"></i> Override WA</span>'
                    : '<span class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-medium border border-slate-200">' + (r.sumber_tabel || '-') + '</span>';

                $('<td>', { class: 'px-5 py-3 text-slate-500 text-[11px]' }).html(displaySumber).appendTo($row);

                // Container Count
                $('<td>', { class: 'px-5 py-3 text-right font-semibold text-slate-700' }).html(
                    r.jumlah_kontainer > 0
                        ? '<span class="inline-flex items-center justify-center px-2 py-0.5 rounded-lg bg-indigo-50 text-indigo-700 text-[11px] font-bold border border-indigo-100">' + r.jumlah_kontainer + '</span>'
                        : '<span class="text-slate-300 text-xs">-</span>'
                ).appendTo($row);

                $row.appendTo($recipientList);
            });

            if (!pageData.length) {
                const message = query
                    ? 'Tidak ada data shipper yang cocok dengan pencarian "<strong>' + $('<div>').text(query).html() + '</strong>".'
                    : 'Tidak ada data shipper yang ditemukan.';
                $('<tr>').append($('<td>', {
                    colspan: 6,
                    class: 'px-5 py-8 text-center text-slate-400 text-xs',
                    html: '<i class="fas fa-inbox text-2xl mb-2 block text-slate-300"></i>' + message
                })).appendTo($recipientList);
            }

            updateSelectionSummary();
            renderPaginationControls(totalPages);
        }

        // Handle single row checkbox toggle
        $(document).on('change', '.recipient-row-cb', function() {
            const shipperName = $(this).attr('data-shipper-name');
            selectedShippers[shipperName] = $(this).is(':checked');
            updateSelectionSummary();
        });

        // Handle Header Checkbox toggle
        $selectAllCb.on('change', function() {
            const isChecked = $(this).is(':checked');
            $.each(loadedRecipients, function(i, r) {
                selectedShippers[r.shipper_name] = isChecked;
            });
            $('.recipient-row-cb').prop('checked', isChecked);
            updateSelectionSummary();
        });

        // Quick button: Pilih Semua
        $('#btn-select-all-shippers').on('click', function() {
            $.each(loadedRecipients, function(i, r) {
                selectedShippers[r.shipper_name] = true;
            });
            $('.recipient-row-cb').prop('checked', true);
            updateSelectionSummary();
        });

        // Quick button: Batal Semua
        $('#btn-deselect-all-shippers').on('click', function() {
            $.each(loadedRecipients, function(i, r) {
                selectedShippers[r.shipper_name] = false;
            });
            $('.recipient-row-cb').prop('checked', false);
            updateSelectionSummary();
        });

        // Handle user editing the phone number input — dengan auto-save ke database
        $(document).on('input', '.recipient-phone-input', function() {
            const $input     = $(this);
            const shipperName = $input.attr('data-shipper-name');
            const newVal      = $input.val().trim();

            customPhones[shipperName] = newVal;

            const item = loadedRecipients.find(r => r.shipper_name === shipperName);
            if (item) {
                item.telepon = newVal;
            }

            const icon = $input.siblings('span').first().find('i');
            if (newVal) {
                icon.removeClass('text-slate-300').addClass('text-emerald-500');
                $input.removeClass('text-slate-500').addClass('text-emerald-800');
            } else {
                icon.removeClass('text-emerald-500').addClass('text-slate-300');
                $input.removeClass('text-emerald-800').addClass('text-slate-500');
            }

            $('#custom_phones_json').val(JSON.stringify(customPhones));

            // --- Auto-save ke database (debounced 800ms) ---
            const $indicator = $('[data-shipper-name="' + $input.attr('data-shipper-name') + '"].phone-save-indicator');

            // Tampilkan spinner loading
            $indicator.html('<i class="fas fa-circle-notch fa-spin text-[9px] text-slate-400"></i>');

            // Debounce
            if (phoneSaveTimers[shipperName]) {
                clearTimeout(phoneSaveTimers[shipperName]);
            }
            phoneSaveTimers[shipperName] = setTimeout(function() {
                $.ajax({
                    url: savePhoneUrl,
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        shipper_name: shipperName,
                        telepon: newVal
                    },
                    success: function(response) {
                        if (response.success) {
                            if (response.action === 'deleted' || !newVal) {
                                // Nomor dihapus
                                delete phoneOverrides[shipperName];
                                $indicator.html('');
                            } else {
                                // Nomor tersimpan
                                phoneOverrides[shipperName] = newVal;
                                $indicator.html('<i class="fas fa-database text-[9px] text-emerald-500" title="Tersimpan di database"></i>');
                                // Update badge sumber tabel di row ini
                                $input.closest('tr').find('td:nth-child(5) span')
                                    .removeClass('bg-slate-100 text-slate-700 border-slate-200')
                                    .addClass('bg-emerald-50 text-emerald-800 border-emerald-200 gap-1')
                                    .html('<i class="fas fa-database text-[9px]"></i> Override WA');
                            }
                        } else {
                            $indicator.html('<i class="fas fa-exclamation-circle text-[9px] text-rose-400" title="Gagal menyimpan"></i>');
                        }
                    },
                    error: function() {
                        $indicator.html('<i class="fas fa-exclamation-circle text-[9px] text-rose-400" title="Gagal menyimpan"></i>');
                    }
                });
            }, 800);
        });

        $('#broadcastForm').on('submit', function(e) {
            updateSelectionSummary();
            $('#custom_phones_json').val(JSON.stringify(customPhones));

            const selectedCount = Object.keys(selectedShippers).filter(k => selectedShippers[k]).length;
            if (loadedRecipients.length > 0 && selectedCount === 0) {
                e.preventDefault();
                alert('Silakan pilih minimal 1 shipper yang ingin dikirimi broadcast WhatsApp.');
                return false;
            }
        });

        /**
         * Render pagination page buttons
         */
        function renderPaginationControls(totalPages) {
            $paginationControls.empty();
            if (totalPages <= 1) return;

            // Tombol Prev
            const $prevBtn = $('<button>', {
                type: 'button',
                class: 'px-2.5 py-1 text-xs font-semibold rounded-lg border transition-all ' + 
                    (currentPage === 1 
                        ? 'bg-slate-100 text-slate-300 border-slate-200 cursor-not-allowed' 
                        : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100 hover:border-slate-300 shadow-2xs'),
                html: '<i class="fas fa-chevron-left text-[10px]"></i>',
                disabled: currentPage === 1,
                title: 'Halaman Sebelumnya'
            }).on('click', function() {
                if (currentPage > 1) {
                    currentPage--;
                    renderRecipients();
                }
            });
            $paginationControls.append($prevBtn);

            // Page numbers windowing
            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(totalPages, currentPage + 2);

            if (startPage > 1) {
                $paginationControls.append(createPageButton(1));
                if (startPage > 2) {
                    $paginationControls.append($('<span>', { class: 'px-1 py-1 text-slate-400 text-xs select-none' }).text('...'));
                }
            }

            for (let p = startPage; p <= endPage; p++) {
                $paginationControls.append(createPageButton(p));
            }

            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    $paginationControls.append($('<span>', { class: 'px-1 py-1 text-slate-400 text-xs select-none' }).text('...'));
                }
                $paginationControls.append(createPageButton(totalPages));
            }

            // Tombol Next
            const $nextBtn = $('<button>', {
                type: 'button',
                class: 'px-2.5 py-1 text-xs font-semibold rounded-lg border transition-all ' + 
                    (currentPage === totalPages 
                        ? 'bg-slate-100 text-slate-300 border-slate-200 cursor-not-allowed' 
                        : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100 hover:border-slate-300 shadow-2xs'),
                html: '<i class="fas fa-chevron-right text-[10px]"></i>',
                disabled: currentPage === totalPages,
                title: 'Halaman Selanjutnya'
            }).on('click', function() {
                if (currentPage < totalPages) {
                    currentPage++;
                    renderRecipients();
                }
            });
            $paginationControls.append($nextBtn);
        }

        function createPageButton(pageNumber) {
            const isActive = pageNumber === currentPage;
            return $('<button>', {
                type: 'button',
                class: 'px-2.5 py-1 text-xs font-bold rounded-lg border transition-all ' + 
                    (isActive 
                        ? 'bg-emerald-600 text-white border-emerald-600 shadow-2xs' 
                        : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100 shadow-2xs'),
                text: pageNumber
            }).on('click', function() {
                currentPage = pageNumber;
                renderRecipients();
            });
        }

        $searchInput.on('input', function() {
            currentPage = 1;
            renderRecipients();
        });

        $searchClear.on('click', function() {
            $searchInput.val('');
            currentPage = 1;
            renderRecipients();
            $searchInput.focus();
        });

        $pageSizeSelect.on('change', function() {
            pageSize = parseInt($(this).val()) || 10;
            currentPage = 1;
            renderRecipients();
        });

        /**
         * Mengatur filter variabel, form, dan template berdasarkan target penerima terpilih
         */
        function updateTargetMode(targetMode) {
            if (targetMode === 'all_master_shippers') {
                // MODE 1: SEMUA MASTER DATA SHIPPER (JADWAL BROADCAST)
                $targetModeBadge
                    .removeClass('bg-blue-50 text-blue-700 border-blue-200')
                    .addClass('bg-emerald-50 text-emerald-700 border-emerald-200')
                    .html('<i class="fas fa-users text-emerald-500"></i> Mode Semua Master Shipper');

                // Tampilkan Form Pilih Pelabuhan & Jadwal
                $sectionPelabuhan.removeClass('hidden');
                $pelabuhan.prop('disabled', false).prop('required', true);
                $jadwalId.prop('disabled', !$pelabuhan.val()).prop('required', true);

                // Hilangkan Form Pilih Kapal & Voyage (Manifest)
                $sectionKapal.addClass('hidden');
                $namaKapal.prop('disabled', true).prop('required', false).val('');
                $noVoyage.prop('disabled', true).prop('required', false).val('');

                // Hilangkan Form Kendala & Masalah
                $sectionKendala.addClass('hidden');
                $kategoriMasalah.prop('disabled', true).val('');
                $deskripsiMasalah.prop('disabled', true).val('');

                // Update Step Badge nomor template menjadi 3
                $templateStepBadge.text('3');

                // Tampilkan Variabel yang relevan untuk Jadwal Kapal Berlabuh
                $varCountLabel.text('6 Variabel Jadwal Aktif');
                $varSubtitleLabel.text('Data pelabuhan, kapal & jadwal otomatis diambil dari Master Jadwal Kapal Berlabuh');
                $activeVarsBox.html(`
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-800 font-mono text-xs font-semibold border border-emerald-200 shadow-2xs">
                        <i class="fas fa-check text-[10px] text-emerald-600"></i> {shipper_name}
                    </span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-800 font-mono text-xs font-semibold border border-emerald-200 shadow-2xs">
                        <i class="fas fa-check text-[10px] text-emerald-600"></i> {pelabuhan}
                    </span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-800 font-mono text-xs font-semibold border border-emerald-200 shadow-2xs">
                        <i class="fas fa-check text-[10px] text-emerald-600"></i> {nama_kapal}
                    </span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-800 font-mono text-xs font-semibold border border-emerald-200 shadow-2xs">
                        <i class="fas fa-check text-[10px] text-emerald-600"></i> {tanggal_close}
                    </span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-800 font-mono text-xs font-semibold border border-emerald-200 shadow-2xs">
                        <i class="fas fa-check text-[10px] text-emerald-600"></i> {tanggal_etd}
                    </span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-800 font-mono text-xs font-semibold border border-emerald-200 shadow-2xs">
                        <i class="fas fa-check text-[10px] text-emerald-600"></i> {tanggal_eta}
                    </span>
                `);

                // Filter template untuk Jadwal Kapal / General
                populateTemplateOptions('jadwal');

            } else {
                // MODE 2: MANIFEST VOYAGE (Status Pengiriman atau Kendala)
                const isStatusPengiriman = requestType === 'status_pengiriman' || requestType === 'status';

                if (isStatusPengiriman) {
                    $targetModeBadge
                        .removeClass('bg-emerald-50 text-emerald-700 border-emerald-200 bg-blue-50 text-blue-700 border-blue-200')
                        .addClass('bg-indigo-50 text-indigo-700 border-indigo-200')
                        .html('<i class="fas fa-shipping-fast text-indigo-500"></i> Mode Status Pengiriman (Manifest)');

                    $('#kendalaHeaderTitle').text('Informasi Status Pengiriman (Mode Manifest)');
                    $('#kendalaHeaderSubtitle').text('Variabel status pengiriman muatan / kontainer manifest yang akan dikirimkan ke shipper');
                    $('#kategoriLabel').text('Status Pengiriman Saat Ini');
                    $kategoriMasalah.attr('placeholder', 'Contoh: Kapal Sedang Berlayar / Sandar di Pelabuhan / Proses Bongkar / Siap Diambil');
                    $('#deskripsiLabel').text('Keterangan Tambahan / Detail Status');
                    $deskripsiMasalah.attr('placeholder', 'Contoh: Muatan kontainer telah tiba di pelabuhan tujuan dan saat ini sedang dalam proses pembongkaran...');
                    $('#quickStatusOptions').removeClass('hidden');

                    // Tampilkan Variabel yang relevan untuk Status Pengiriman
                    $varCountLabel.text('6 Variabel Status Pengiriman Aktif');
                    $varSubtitleLabel.text('Variabel manifest & status muatan diaktifkan');
                    $activeVarsBox.html(`
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-indigo-100 text-indigo-800 font-mono text-xs font-semibold border border-indigo-200 shadow-2xs">
                            <i class="fas fa-check text-[10px] text-indigo-600"></i> {nama_kapal}
                        </span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-indigo-100 text-indigo-800 font-mono text-xs font-semibold border border-indigo-200 shadow-2xs">
                            <i class="fas fa-check text-[10px] text-indigo-600"></i> {no_voyage}
                        </span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-indigo-100 text-indigo-800 font-mono text-xs font-semibold border border-indigo-200 shadow-2xs">
                            <i class="fas fa-check text-[10px] text-indigo-600"></i> {kategori_masalah} / {status}
                        </span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-indigo-100 text-indigo-800 font-mono text-xs font-semibold border border-indigo-200 shadow-2xs">
                            <i class="fas fa-check text-[10px] text-indigo-600"></i> {deskripsi_masalah} / {keterangan}
                        </span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-indigo-100 text-indigo-800 font-mono text-xs font-semibold border border-indigo-200 shadow-2xs">
                            <i class="fas fa-check text-[10px] text-indigo-600"></i> {daftar_resi}
                        </span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-indigo-100 text-indigo-800 font-mono text-xs font-semibold border border-indigo-200 shadow-2xs">
                            <i class="fas fa-check text-[10px] text-indigo-600"></i> {shipper_name}
                        </span>
                    `);

                    populateTemplateOptions('status_pengiriman');
                } else {
                    $targetModeBadge
                        .removeClass('bg-emerald-50 text-emerald-700 border-emerald-200 bg-indigo-50 text-indigo-700 border-indigo-200')
                        .addClass('bg-blue-50 text-blue-700 border-blue-200')
                        .html('<i class="fas fa-file-invoice text-blue-500"></i> Mode Manifest Voyage');

                    $('#kendalaHeaderTitle').text('Informasi Kendala & Masalah (Mode Manifest)');
                    $('#kendalaHeaderSubtitle').text('Variabel masalah untuk pemberitahuan keterlambatan resi/kontainer manifest');
                    $('#kategoriLabel').text('Kategori Masalah / Jenis Kendala');
                    $kategoriMasalah.attr('placeholder', 'Contoh: Cuaca Buruk / Antrian Dermaga / Kerusakan Mesin');
                    $('#deskripsiLabel').text('Deskripsi Masalah & Estimasi');
                    $deskripsiMasalah.attr('placeholder', 'Contoh: Keterlambatan diperkirakan sekitar 2 hari karena cuaca buruk di perairan...');
                    $('#quickStatusOptions').addClass('hidden');

                    // Tampilkan Variabel yang relevan untuk Kendala Manifest
                    $varCountLabel.text('6 Variabel Kendala Aktif');
                    $varSubtitleLabel.text('Variabel manifest & kendala diaktifkan');
                    $activeVarsBox.html(`
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-blue-100 text-blue-800 font-mono text-xs font-semibold border border-blue-200 shadow-2xs">
                            <i class="fas fa-check text-[10px] text-blue-600"></i> {nama_kapal}
                        </span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-blue-100 text-blue-800 font-mono text-xs font-semibold border border-blue-200 shadow-2xs">
                            <i class="fas fa-check text-[10px] text-blue-600"></i> {no_voyage}
                        </span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-blue-100 text-blue-800 font-mono text-xs font-semibold border border-blue-200 shadow-2xs">
                            <i class="fas fa-check text-[10px] text-blue-600"></i> {kategori_masalah}
                        </span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-blue-100 text-blue-800 font-mono text-xs font-semibold border border-blue-200 shadow-2xs">
                            <i class="fas fa-check text-[10px] text-blue-600"></i> {deskripsi_masalah}
                        </span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-blue-100 text-blue-800 font-mono text-xs font-semibold border border-blue-200 shadow-2xs">
                            <i class="fas fa-check text-[10px] text-blue-600"></i> {daftar_resi}
                        </span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-blue-100 text-blue-800 font-mono text-xs font-semibold border border-blue-200 shadow-2xs">
                            <i class="fas fa-check text-[10px] text-blue-600"></i> {shipper_name}
                        </span>
                    `);

                    populateTemplateOptions('kendala');
                }

                // Hilangkan Form Pilih Pelabuhan & Jadwal
                $sectionPelabuhan.addClass('hidden');
                $pelabuhan.prop('disabled', true).prop('required', false);
                $jadwalId.prop('disabled', true).prop('required', false);
                $scheduleDetails.addClass('hidden');

                // Tampilkan Form Pilih Kapal & Voyage
                $sectionKapal.removeClass('hidden');
                $namaKapal.prop('disabled', false).prop('required', true);
                $noVoyage.prop('disabled', false).prop('required', true);

                // Tampilkan Form Kendala & Masalah
                $sectionKendala.removeClass('hidden');
                $kategoriMasalah.prop('disabled', false);
                $deskripsiMasalah.prop('disabled', false);

                // Update Step Badge nomor template menjadi 4
                $templateStepBadge.text('4');
            }
        }

        function populateTemplateOptions(preferredType) {
            $templateId.empty();

            let filtered = rawTemplates.filter(t => t.type === preferredType);
            if (filtered.length === 0) {
                filtered = rawTemplates;
            }

            $templateId.append('<option value="">-- Pilih Template Pesan --</option>');
            let selectedId = null;

            $.each(filtered, function(i, t) {
                const isSelected = (defaultTemplateId && defaultTemplateId == t.id) || (i === 0 && !defaultTemplateId);
                if (isSelected && !selectedId) {
                    selectedId = t.id;
                }
                $templateId.append($('<option>', {
                    value: t.id,
                    text: t.nama,
                    selected: isSelected
                }));
            });

            if (selectedId) {
                $templateId.val(selectedId);
            }

            if ($.fn.select2) {
                $templateId.select2({ width: '100%' });
            }

            renderTemplatePreview();
        }

        function renderTemplatePreview() {
            const currentId = $templateId.val();
            const templateObj = rawTemplates.find(t => t.id == currentId);

            if (templateObj) {
                $selectedTemplateNameBadge.text(templateObj.nama);
                let text = templateObj.isi;

                const targetPenerima = $('input[name="target_penerima"]:checked').val() || 'all_master_shippers';
                if (targetPenerima === 'all_master_shippers') {
                    const selectedJadwalId = $jadwalId.val();
                    const portName = $pelabuhan.val() || '{pelabuhan}';

                    if (selectedJadwalId === 'all' && window.currentPortSchedules && window.currentPortSchedules.length > 0) {
                        // Multi-ship schedule dynamic block repetition
                        const lines = text.split(/\r\n|\r|\n/);
                        let startLine = null;
                        let endLine = null;
                        const vars = ['{nama_kapal}', '{no_voyage}', '{tanggal_close}', '{tanggal_closing}', '{close}', '{tanggal_etd}', '{etd}', '{tanggal_eta}', '{eta}'];

                        for (let i = 0; i < lines.length; i++) {
                            for (let v of vars) {
                                if (lines[i].includes(v)) {
                                    if (startLine === null) startLine = i;
                                    endLine = i;
                                    break;
                                }
                            }
                        }

                        if (startLine !== null && endLine !== null) {
                            const blockPattern = lines.slice(startLine, endLine + 1).join('\n');
                            const repeatedBlocks = window.currentPortSchedules.map(s => {
                                let b = blockPattern;
                                const cVal = s.tanggal_closing || '-';
                                const etVal = s.tanggal_etd || '-';
                                const eaVal = s.tanggal_eta || '-';
                                const vVal = s.no_voyage || '-';

                                return b.replace(/{nama_kapal}/g, s.nama_kapal)
                                        .replace(/{no_voyage}/g, vVal)
                                        .replace(/{tanggal_close}/g, cVal)
                                        .replace(/{tanggal_closing}/g, cVal)
                                        .replace(/{close}/g, cVal)
                                        .replace(/{tanggal_etd}/g, etVal)
                                        .replace(/{etd}/g, etVal)
                                        .replace(/{tanggal_eta}/g, eaVal)
                                        .replace(/{eta}/g, eaVal);
                            });

                            const before = lines.slice(0, startLine);
                            const after = lines.slice(endLine + 1);
                            text = before.concat([repeatedBlocks.join('\n\n')], after).join('\n');
                        }

                        text = text.replace(/{pelabuhan}/g, portName)
                                   .replace(/{shipper_name}/g, 'CONTOH SHIPPER');
                    } else {
                        const selectedSchedule = (window.currentPortSchedules || []).find(s => s.id == selectedJadwalId);
                        const kapalName = selectedSchedule ? selectedSchedule.nama_kapal : '{nama_kapal}';
                        const closingDate = selectedSchedule ? selectedSchedule.tanggal_closing : '{tanggal_close}';
                        const etdDate = selectedSchedule ? selectedSchedule.tanggal_etd : '{tanggal_etd}';
                        const etaDate = selectedSchedule ? selectedSchedule.tanggal_eta : '{tanggal_eta}';

                        text = text.replace(/{pelabuhan}/g, portName)
                                   .replace(/{nama_kapal}/g, kapalName)
                                   .replace(/{tanggal_close}/g, closingDate)
                                   .replace(/{tanggal_closing}/g, closingDate)
                                   .replace(/{close}/g, closingDate)
                                   .replace(/{tanggal_etd}/g, etdDate)
                                   .replace(/{etd}/g, etdDate)
                                   .replace(/{tanggal_eta}/g, etaDate)
                                   .replace(/{eta}/g, etaDate)
                                   .replace(/{shipper_name}/g, 'CONTOH SHIPPER');
                    }
                } else {
                    const kapalName = $namaKapal.val() || '{nama_kapal}';
                    const voyageVal = $noVoyage.val() || '{no_voyage}';
                    const katMasalah = $kategoriMasalah.val() || '{kategori_masalah}';
                    const deskMasalah = $deskripsiMasalah.val() || '{deskripsi_masalah}';

                    text = text.replace(/{nama_kapal}/g, kapalName)
                               .replace(/{no_voyage}/g, voyageVal)
                               .replace(/{kategori_masalah}/g, katMasalah)
                               .replace(/{status_pengiriman}/g, katMasalah)
                               .replace(/{status}/g, katMasalah)
                               .replace(/{deskripsi_masalah}/g, deskMasalah)
                               .replace(/{keterangan}/g, deskMasalah)
                               .replace(/{estimasi_keterlambatan}/g, '')
                               .replace(/{daftar_resi}/g, '- BL: BL/SUB-BTM/001 / Kontainer: AYPU1234567\n- BL: BL/SUB-BTM/002 / Kontainer: AYPU7654321')
                               .replace(/{shipper_name}/g, 'CONTOH SHIPPER');
                }

                $templatePreviewText.text(text);
            } else {
                $selectedTemplateNameBadge.text('-');
                $templatePreviewText.text('Pilih salah satu template untuk melihat format isi pesan.');
            }
        }

        $templateId.on('change', renderTemplatePreview);
        $kategoriMasalah.on('input change', renderTemplatePreview);
        $deskripsiMasalah.on('input change', renderTemplatePreview);
        $namaKapal.on('change', renderTemplatePreview);
        $noVoyage.on('change', renderTemplatePreview);

        $(document).on('click', '.btn-quick-status', function() {
            const status = $(this).attr('data-status');
            const desc = $(this).attr('data-desc');
            $kategoriMasalah.val(status);
            $deskripsiMasalah.val(desc);
            renderTemplatePreview();
        });

        function clearRecipients() {
            recipientRequestId++;
            loadedRecipients = [];
            selectedShippers = {};
            currentPage = 1;
            $recipientList.empty();
            $recipientSummary.empty();
            $paginationInfo.empty();
            $paginationControls.empty();
            $searchInput.val('');
            $searchClear.addClass('hidden');
            $recipientPanel.addClass('hidden');
            $recipientLoading.addClass('hidden');
            $selectAllCb.prop('checked', false).prop('indeterminate', false);
            $selectedBadge.text('0 Dipilih');
        }

        function loadRecipients() {
            const targetPenerima = $('input[name="target_penerima"]:checked').val() || 'all_master_shippers';
            const namaKapal      = $namaKapal.val();
            const noVoyage       = $noVoyage.val();

            if (targetPenerima === 'manifest' && (!namaKapal || !noVoyage)) {
                clearRecipients();
                return;
            }

            const requestId = ++recipientRequestId;
            $recipientPanel.removeClass('hidden');
            $recipientList.empty();
            $recipientSummary.text(targetPenerima === 'all_master_shippers' ? 'Membaca seluruh data master shipper...' : 'Membaca data shipper dari manifest...');
            $recipientLoading.removeClass('hidden');

            $.ajax({
                url: "{{ route('master.wa-broadcast.get-recipients') }}",
                type: 'GET',
                data: {
                    nama_kapal: namaKapal,
                    no_voyage: noVoyage,
                    source: targetPenerima
                },
                dataType: 'json',
                success: function(response) {
                    if (requestId !== recipientRequestId) return;

                    loadedRecipients = response.success ? response.recipients : [];
                    selectedShippers = {};
                    $.each(loadedRecipients, function(i, r) {
                        selectedShippers[r.shipper_name] = true; // Default all selected
                    });

                    currentPage = 1;
                    renderRecipients();
                },
                error: function() {
                    if (requestId !== recipientRequestId) return;
                    loadedRecipients = [];
                    selectedShippers = {};
                    currentPage = 1;
                    $recipientList.empty();
                    $('<tr>').append($('<td>', {
                        colspan: 6,
                        class: 'px-5 py-6 text-center text-rose-500 text-xs',
                        html: '<i class="fas fa-exclamation-triangle mr-1"></i> Gagal memuat data penerima broadcast.'
                    })).appendTo($recipientList);
                    $recipientSummary.text('');
                    $paginationInfo.empty();
                    $paginationControls.empty();
                    $selectedBadge.text('0 Dipilih');
                },
                complete: function() {
                    if (requestId === recipientRequestId) $recipientLoading.addClass('hidden');
                }
            });
        }

        // Listener pergantian Target Penerima
        $('input[name="target_penerima"]').on('change', function() {
            const selectedMode = $(this).val();
            $searchInput.val('');
            updateTargetMode(selectedMode);
            loadRecipients();
            renderTemplatePreview();
        });

        // Initialize Select2 for Ship, Port, and Schedule
        if ($.fn.select2) {
            $('#pelabuhan, #jadwal_id, #nama_kapal, #no_voyage').select2({ width: '100%' });
        }

        // Pelabuhan Change Listener
        $pelabuhan.on('change', function() {
            const selectedPort = $(this).val();
            $scheduleDetails.addClass('hidden');
            $tanggalClosing.val('');
            $tanggalEtd.val('');
            $tanggalEta.val('');

            if (!selectedPort) {
                $('#btn-select-all-ships').addClass('hidden');
                $jadwalId.empty().append('<option value="">-- Pilih Pelabuhan Terlebih Dahulu --</option>').prop('disabled', true);
                if ($.fn.select2) $jadwalId.trigger('change.select2');
                renderTemplatePreview();
                return;
            }

            $jadwalId.empty().append('<option value="">Memeriksa & memuat jadwal kapal di ' + selectedPort + '...</option>').prop('disabled', true);
            if ($.fn.select2) $jadwalId.trigger('change.select2');

            $.ajax({
                url: "{{ route('master.wa-broadcast.get-schedules-by-port') }}",
                type: 'GET',
                data: { pelabuhan: selectedPort },
                dataType: 'json',
                success: function(response) {
                    $jadwalId.empty();
                    window.currentPortSchedules = response.success ? response.schedules : [];

                    if (window.currentPortSchedules.length > 0) {
                        const totalCount = window.currentPortSchedules.length;
                        $('#btn-select-all-ships').removeClass('hidden');

                        $jadwalId.append('<option value="">-- Pilih Kapal (' + totalCount + ' Kapal di ' + selectedPort + ') --</option>');
                        $jadwalId.append($('<option>', {
                            value: 'all',
                            text: '⭐ SEMUA KAPAL (Kirim Sekaligus ' + totalCount + ' Jadwal Kapal di ' + selectedPort + ')'
                        }));

                        $.each(window.currentPortSchedules, function(i, s) {
                            $jadwalId.append($('<option>', {
                                value: s.id,
                                text: s.nama_kapal + ' (Closing: ' + s.tanggal_closing + ' | ETD: ' + s.tanggal_etd + ' | ETA: ' + s.tanggal_eta + ')'
                            }));
                        });
                        $jadwalId.prop('disabled', false);

                        // Auto-select 'all' or previously selected schedule
                        const oldJadwal = "{{ old('jadwal_id') }}";
                        if (oldJadwal && (oldJadwal === 'all' || window.currentPortSchedules.some(s => s.id == oldJadwal))) {
                            $jadwalId.val(oldJadwal).trigger('change');
                        } else {
                            $jadwalId.val('all').trigger('change');
                        }
                    } else {
                        $('#btn-select-all-ships').addClass('hidden');
                        $jadwalId.append('<option value="">-- Tidak ada jadwal kapal aktif di pelabuhan ini --</option>').prop('disabled', false);
                        $scheduleDetails.addClass('hidden');
                    }

                    if ($.fn.select2) $jadwalId.trigger('change.select2');
                    renderTemplatePreview();
                },
                error: function() {
                    $('#btn-select-all-ships').addClass('hidden');
                    $jadwalId.empty().append('<option value="">-- Gagal memuat jadwal kapal --</option>').prop('disabled', false);
                    if ($.fn.select2) $jadwalId.trigger('change.select2');
                    renderTemplatePreview();
                }
            });
        });

        // Tombol cepat pilih semua kapal
        $('#btn-select-all-ships').on('click', function(e) {
            e.preventDefault();
            if (window.currentPortSchedules && window.currentPortSchedules.length > 0) {
                $jadwalId.val('all').trigger('change');
                if ($.fn.select2) $jadwalId.trigger('change.select2');
            }
        });

        // Jadwal ID Change Listener
        $jadwalId.on('change', function() {
            const selectedId = $(this).val();
            const portName = $pelabuhan.val() || '-';

            if (selectedId === 'all') {
                const schedules = window.currentPortSchedules || [];
                $('#label-detail-jadwal').text('Detail Semua Jadwal Kapal Terpilih:');
                $badgeJadwalPelabuhan.text(portName + ' (' + schedules.length + ' Kapal)');
                $('#allSchedulesCountPill').text(schedules.length + ' Kapal');

                const $list = $('#allSchedulesList');
                $list.empty();
                $.each(schedules, function(i, s) {
                    $list.append(`
                        <div class="bg-white p-3 rounded-xl border border-emerald-100 shadow-2xs space-y-2">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-1.5">
                                <span class="font-bold text-xs text-slate-800 flex items-center gap-1.5">
                                    <i class="fas fa-ship text-emerald-600"></i> ${s.nama_kapal}
                                </span>
                                <span class="text-[10px] text-slate-400 font-mono">Voy: ${s.no_voyage || '-'}</span>
                            </div>
                            <div class="grid grid-cols-3 gap-1.5 text-center text-[10px]">
                                <div class="bg-amber-50/70 p-1.5 rounded-lg border border-amber-100">
                                    <span class="block text-slate-400 text-[9px]">Closing</span>
                                    <span class="block font-bold text-amber-700 font-mono">${s.tanggal_closing || '-'}</span>
                                </div>
                                <div class="bg-blue-50/70 p-1.5 rounded-lg border border-blue-100">
                                    <span class="block text-slate-400 text-[9px]">ETD</span>
                                    <span class="block font-bold text-blue-700 font-mono">${s.tanggal_etd || '-'}</span>
                                </div>
                                <div class="bg-emerald-50/70 p-1.5 rounded-lg border border-emerald-100">
                                    <span class="block text-slate-400 text-[9px]">ETA</span>
                                    <span class="block font-bold text-emerald-700 font-mono">${s.tanggal_eta || '-'}</span>
                                </div>
                            </div>
                        </div>
                    `);
                });

                $('#singleScheduleContainer').addClass('hidden');
                $('#allSchedulesContainer').removeClass('hidden');
                $scheduleDetails.removeClass('hidden');

                $tanggalClosing.val(schedules.map(s => s.tanggal_closing).filter(Boolean).join(', '));
                $tanggalEtd.val(schedules.map(s => s.tanggal_etd).filter(Boolean).join(', '));
                $tanggalEta.val(schedules.map(s => s.tanggal_eta).filter(Boolean).join(', '));
            } else {
                const schedule = (window.currentPortSchedules || []).find(s => s.id == selectedId);
                if (schedule) {
                    $('#label-detail-jadwal').text('Detail Jadwal Kapal Terpilih:');
                    $infoNamaKapal.text(schedule.nama_kapal);
                    $infoTglClosing.text(schedule.tanggal_closing);
                    $infoTglEtd.text(schedule.tanggal_etd);
                    $infoTglEta.text(schedule.tanggal_eta);
                    $badgeJadwalPelabuhan.text(schedule.pelabuhan);

                    $tanggalClosing.val(schedule.tanggal_closing);
                    $tanggalEtd.val(schedule.tanggal_etd);
                    $tanggalEta.val(schedule.tanggal_eta);

                    $('#allSchedulesContainer').addClass('hidden');
                    $('#singleScheduleContainer').removeClass('hidden');
                    $scheduleDetails.removeClass('hidden');
                } else {
                    $scheduleDetails.addClass('hidden');
                    $('#singleScheduleContainer').addClass('hidden');
                    $('#allSchedulesContainer').addClass('hidden');
                    $tanggalClosing.val('');
                    $tanggalEtd.val('');
                    $tanggalEta.val('');
                }
            }

            renderTemplatePreview();
        });

        // Kapal (Manifest) Change Listener
        $namaKapal.on('change', function() {
            const namaKapal = $(this).val();
            if (!namaKapal) {
                if ($('input[name="target_penerima"]:checked').val() === 'manifest') {
                    clearRecipients();
                }
                $noVoyage.empty().append('<option value="">-- Pilih Kapal Terlebih Dahulu --</option>');
                $noVoyage.prop('disabled', true).trigger('change');
                return;
            }

            if ($('input[name="target_penerima"]:checked').val() === 'manifest') {
                clearRecipients();
            }
            $noVoyage.empty().append('<option value="">Memuat voyage...</option>').prop('disabled', true).trigger('change');

            $.ajax({
                url: "{{ route('master.wa-broadcast.get-voyages') }}",
                type: 'GET',
                data: { nama_kapal: namaKapal },
                dataType: 'json',
                success: function(response) {
                    $noVoyage.empty();
                    if (response.success && response.voyages && response.voyages.length > 0) {
                        $noVoyage.append('<option value="">-- Pilih Voyage --</option>');
                        $.each(response.voyages, function(i, voyage) {
                            $noVoyage.append($('<option>', { value: voyage, text: voyage }));
                        });
                        $noVoyage.prop('disabled', false);
                    } else {
                        $noVoyage.append('<option value="">-- Tidak ada voyage tersedia --</option>').prop('disabled', false);
                    }
                    $noVoyage.trigger('change');
                },
                error: function() {
                    $noVoyage.empty().append('<option value="">-- Gagal memuat voyage --</option>').prop('disabled', false).trigger('change');
                }
            });
        });

        $noVoyage.on('change', loadRecipients);

        // Inisialisasi awal
        const initialTargetMode = $('input[name="target_penerima"]:checked').val() || 'all_master_shippers';
        updateTargetMode(initialTargetMode);
        loadRecipients();

        // Load phone overrides dari database
        loadPhoneOverridesFromDb();

        // Trigger pelabuhan jika sudah terpilih sebelumnya
        if ($pelabuhan.val()) {
            $pelabuhan.trigger('change');
        }
    });
</script>
@endpush
@endsection
