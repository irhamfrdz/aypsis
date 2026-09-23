@extends('layouts.app')

@section('title', 'Kelola Lokasi Absensi')
@section('page_title', 'Kelola Lokasi Absensi')

@push('styles')
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        #map {
            height: 530px;
            width: 100%;
            border-radius: 14px;
            z-index: 1;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f8fafc;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        /* Leaflet popup styling override */
        .leaflet-popup-content-wrapper {
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.12), 0 8px 10px -6px rgba(15, 23, 42, 0.05);
            border: 1px solid #e2e8f0;
            padding: 2px;
        }
        .leaflet-popup-content {
            margin: 10px 14px;
            font-family: inherit;
        }
        .location-item.active-item {
            background-color: #eff6ff !important;
            border-left: 3px solid #3b82f6 !important;
        }
    </style>
@endpush

@section('content')
<div class="space-y-6 pb-8">

    <!-- Header Banner Card -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs relative overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 relative z-10">
            <div class="space-y-1.5">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200 shadow-2xs">
                        <i class="fas fa-map-marked-alt text-[11px] text-blue-600"></i>
                        Master Data Geofence
                    </span>
                    <span class="text-xs text-slate-400 font-medium">• Presensi GPS Karyawan</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900 flex items-center gap-2.5">
                    Kelola Lokasi Absensi
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 max-w-2xl leading-relaxed">
                    Atur titik koordinat GPS dan radius jangkauan aman absensi masuk serta pulang karyawan secara presisi.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <div class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 flex items-center gap-3 shadow-2xs">
                    <div class="w-10 h-10 rounded-xl bg-blue-100/80 text-blue-600 flex items-center justify-center font-bold text-base shadow-2xs">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Terdaftar</span>
                        <span id="header-total-count" class="text-lg font-black text-slate-900 leading-none">0 Lokasi</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Layout Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left Panel: Form & List (5 Cols) -->
        <div class="lg:col-span-5 flex flex-col gap-6">
            
            <!-- Form Card -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs flex flex-col gap-4">
                
                <!-- Form Header -->
                <div class="flex items-center justify-between pb-3.5 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div id="form-icon-wrap" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-bold shadow-2xs transition-colors">
                            <i id="form-icon" class="fas fa-plus"></i>
                        </div>
                        <div>
                            <h3 id="form-title" class="text-sm font-bold text-slate-900 leading-tight">Tambah Lokasi Absensi</h3>
                            <p id="form-subtitle" class="text-[11px] text-slate-400 mt-0.5">Tentukan nama dan koordinat titik lokasi baru</p>
                        </div>
                    </div>
                    <span id="mode-badge" class="hidden text-[10px] font-bold px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 flex items-center gap-1 shadow-2xs">
                        <i class="fas fa-pen text-[9px]"></i> Mode Edit
                    </span>
                </div>
                
                <form id="location-form" class="space-y-4">
                    <input type="hidden" id="location-id" value="">
                    
                    <!-- Nama Lokasi -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                            <i class="fas fa-building text-slate-400 text-[10px]"></i>
                            <span>Nama Lokasi</span>
                            <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="nama-lokasi" placeholder="Contoh: Kantor Utama, Gudang Logistik" required
                            class="w-full bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition shadow-2xs placeholder-slate-400">
                    </div>
                    
                    <!-- Latitude & Longitude -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                <i class="fas fa-map-pin text-slate-400 text-[10px]"></i>
                                <span>Latitude</span>
                                <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" step="any" id="latitude" placeholder="-6.2000000" required
                                class="w-full font-mono bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition shadow-2xs placeholder-slate-400">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                <i class="fas fa-map-pin text-slate-400 text-[10px]"></i>
                                <span>Longitude</span>
                                <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" step="any" id="longitude" placeholder="106.8166000" required
                                class="w-full font-mono bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition shadow-2xs placeholder-slate-400">
                        </div>
                    </div>
                    
                    <!-- Radius Jangkauan -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                            <i class="fas fa-bullseye text-slate-400 text-[10px]"></i>
                            <span>Radius Jangkauan</span>
                            <span class="text-rose-500">*</span>
                        </label>

                        <!-- Input angka + satuan -->
                        <div class="relative flex items-center mb-2.5">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <div class="w-6 h-6 rounded-lg bg-orange-50 text-orange-500 flex items-center justify-center shadow-2xs">
                                    <i class="fas fa-circle-dot text-[10px]"></i>
                                </div>
                            </div>
                            <input type="number" id="radius" min="10" max="5000" placeholder="100" value="100" required
                                oninput="syncRadiusSlider(this.value)"
                                class="w-full bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl py-2.5 pl-11 pr-24 text-sm font-bold text-slate-800 focus:outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-400/20 transition shadow-2xs placeholder-slate-400">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="px-2.5 py-1 text-[10px] font-bold bg-orange-100 text-orange-600 rounded-lg select-none tracking-wider">
                                    METER
                                </span>
                            </div>
                        </div>

                        <!-- Slider range -->
                        <div class="mb-2.5 px-0.5">
                            <input type="range" id="radius-slider" min="10" max="1000" step="10" value="100"
                                oninput="syncRadiusInput(this.value)"
                                class="w-full h-1.5 rounded-full appearance-none cursor-pointer accent-orange-500 bg-slate-200">
                            <div class="flex justify-between text-[9px] text-slate-400 font-medium mt-1 px-0.5 select-none">
                                <span>10 m</span>
                                <span>500 m</span>
                                <span>1.000 m</span>
                            </div>
                        </div>

                        <!-- Preset cepat -->
                        <div class="flex flex-wrap items-center gap-1.5">
                            <span class="text-[10px] font-bold text-slate-400 shrink-0">Preset:</span>
                            <button type="button" onclick="setRadiusPreset(25)" data-value="25"
                                class="radius-preset-btn px-2.5 py-1 text-[10px] font-bold rounded-lg border border-slate-200 bg-slate-50 hover:bg-orange-50 hover:border-orange-300 hover:text-orange-700 text-slate-600 transition shadow-2xs">25 m</button>
                            <button type="button" onclick="setRadiusPreset(50)" data-value="50"
                                class="radius-preset-btn px-2.5 py-1 text-[10px] font-bold rounded-lg border border-slate-200 bg-slate-50 hover:bg-orange-50 hover:border-orange-300 hover:text-orange-700 text-slate-600 transition shadow-2xs">50 m</button>
                            <button type="button" onclick="setRadiusPreset(100)" data-value="100"
                                class="radius-preset-btn px-2.5 py-1 text-[10px] font-bold rounded-lg border border-slate-200 bg-slate-50 hover:bg-orange-50 hover:border-orange-300 hover:text-orange-700 text-slate-600 transition shadow-2xs">100 m</button>
                            <button type="button" onclick="setRadiusPreset(200)" data-value="200"
                                class="radius-preset-btn px-2.5 py-1 text-[10px] font-bold rounded-lg border border-slate-200 bg-slate-50 hover:bg-orange-50 hover:border-orange-300 hover:text-orange-700 text-slate-600 transition shadow-2xs">200 m</button>
                            <button type="button" onclick="setRadiusPreset(500)" data-value="500"
                                class="radius-preset-btn px-2.5 py-1 text-[10px] font-bold rounded-lg border border-slate-200 bg-slate-50 hover:bg-orange-50 hover:border-orange-300 hover:text-orange-700 text-slate-600 transition shadow-2xs">500 m</button>
                        </div>
                    </div>

                    
                    <!-- Keterangan / Catatan -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                            <i class="fas fa-align-left text-slate-400 text-[10px]"></i>
                            <span>Keterangan / Catatan</span>
                        </label>
                        <textarea id="keterangan" rows="2" placeholder="Catatan opsional atau alamat singkat lokasi..."
                            class="w-full bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition shadow-2xs placeholder-slate-400"></textarea>
                    </div>

                    <!-- Penugasan Karyawan Wajib Absen -->
                    <div class="space-y-2 pt-1 border-t border-slate-100">
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider flex items-center justify-between">
                            <span class="flex items-center gap-1.5">
                                <i class="fas fa-users-cog text-slate-400 text-[10px]"></i>
                                <span>Karyawan Wajib Absen di Titik Ini</span>
                                <span class="text-rose-500">*</span>
                            </span>
                            <span id="selected-karyawan-count-badge" class="hidden text-[10px] font-bold px-2 py-0.5 rounded-full bg-purple-50 text-purple-700 border border-purple-200">
                                0 Karyawan Dipilih
                            </span>
                        </label>

                        <!-- Pilihan Penugasan: Semua vs Khusus -->
                        <div class="grid grid-cols-2 gap-2.5">
                            <label class="flex items-center gap-2.5 p-2.5 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-slate-50 cursor-pointer transition select-none has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50/50 has-[:checked]:ring-1 has-[:checked]:ring-blue-500/20">
                                <input type="radio" name="tipe-penugasan" id="penugasan-semua" value="semua" checked onchange="togglePenugasanType('semua')"
                                    class="w-3.5 h-3.5 text-blue-600 border-slate-300 focus:ring-blue-500">
                                <div class="min-w-0 flex-1">
                                    <span class="block text-xs font-bold text-slate-800">Semua Karyawan</span>
                                    <span class="block text-[10px] text-slate-400 truncate">Berlaku untuk seluruh staf</span>
                                </div>
                            </label>
                            <label class="flex items-center gap-2.5 p-2.5 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-slate-50 cursor-pointer transition select-none has-[:checked]:border-purple-500 has-[:checked]:bg-purple-50/50 has-[:checked]:ring-1 has-[:checked]:ring-purple-500/20">
                                <input type="radio" name="tipe-penugasan" id="penugasan-khusus" value="khusus" onchange="togglePenugasanType('khusus')"
                                    class="w-3.5 h-3.5 text-purple-600 border-slate-300 focus:ring-purple-500">
                                <div class="min-w-0 flex-1">
                                    <span class="block text-xs font-bold text-slate-800">Karyawan Tertentu</span>
                                    <span class="block text-[10px] text-slate-400 truncate">Pilih daftar staf wajib</span>
                                </div>
                            </label>
                        </div>

                        <!-- Container Checklist Karyawan (muncul jika khusus dipilih) -->
                        <div id="karyawan-picker-container" class="hidden space-y-2 p-3 rounded-xl border border-purple-200 bg-purple-50/30">
                            <!-- Quick search & Select/Deselect All buttons -->
                            <div class="flex items-center gap-2">
                                <div class="relative flex-1">
                                    <i class="fas fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[10px]"></i>
                                    <input type="text" id="karyawan-search-input" oninput="filterKaryawanChecklist(this.value)" placeholder="Cari nama, NIK, divisi..." 
                                        class="w-full pl-7 pr-2.5 py-1.5 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500/20 placeholder-slate-400">
                                </div>
                                <button type="button" onclick="selectAllKaryawan(true)" class="px-2 py-1.5 text-[10px] font-bold text-purple-700 bg-white hover:bg-purple-50 border border-purple-200 rounded-lg transition shrink-0 shadow-2xs">
                                    Pilih Semua
                                </button>
                                <button type="button" onclick="selectAllKaryawan(false)" class="px-2 py-1.5 text-[10px] font-bold text-slate-600 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg transition shrink-0 shadow-2xs">
                                    Reset
                                </button>
                            </div>

                            <!-- Scrollable Employee Checklist -->
                            <div id="karyawan-checklist-list" class="max-h-48 overflow-y-auto custom-scrollbar border border-slate-200 rounded-lg bg-white divide-y divide-slate-100">
                                <div class="p-4 text-center text-slate-400 text-xs">
                                    <i class="fas fa-circle-notch fa-spin text-purple-500 mr-1"></i> Memuat daftar karyawan...
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Aktif Toggle Card -->
                    <label for="is-active" class="flex items-center justify-between p-3 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-slate-50 transition cursor-pointer select-none">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div>
                                <span class="block text-xs font-bold text-slate-800">Status Aktif</span>
                                <span class="block text-[10px] text-slate-400">Gunakan lokasi ini untuk absensi karyawan</span>
                            </div>
                        </div>
                        <input type="checkbox" id="is-active" checked value="1"
                            class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500 cursor-pointer">
                    </label>

                    <!-- Submit & Cancel Buttons -->
                    <div class="flex gap-2.5 pt-1.5">
                        <button type="button" id="cancel-edit-btn" class="hidden flex-1 bg-slate-100 hover:bg-slate-200 active:scale-[0.98] text-slate-700 font-semibold py-2.5 rounded-xl text-xs transition flex items-center justify-center gap-1.5">
                            <i class="fas fa-times text-[10px]"></i>
                            <span>Batal</span>
                        </button>
                        <button type="submit" id="submit-btn" class="flex-[2] bg-blue-600 hover:bg-blue-700 active:scale-[0.98] text-white font-bold py-2.5 rounded-xl text-xs transition shadow-xs flex items-center justify-center gap-2">
                            <i class="fas fa-save text-[11px]"></i>
                            <span id="submit-btn-text">Simpan Lokasi</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- List Card -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs flex flex-col overflow-hidden">
                <div class="px-5 py-3.5 border-b border-slate-100 flex justify-between items-center bg-slate-50/40">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-list-ul text-slate-400 text-xs"></i>
                        <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Daftar Lokasi</h4>
                    </div>
                    <span id="location-count" class="text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200 px-2.5 py-0.5 rounded-full shadow-2xs">0 Lokasi</span>
                </div>

                <!-- Live Search Box in List -->
                <div class="px-4 py-2.5 bg-white border-b border-slate-100">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[10px] pointer-events-none"></i>
                        <input type="text" id="filter-locations-input" oninput="filterLocationsList(this.value)" placeholder="Cari nama lokasi terdaftar..."
                            class="w-full pl-8 pr-3 py-1.5 text-xs rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-slate-800 transition">
                    </div>
                </div>
                
                <div id="locations-list" class="max-h-[360px] overflow-y-auto custom-scrollbar divide-y divide-slate-100">
                    <!-- Dinamis diisi js -->
                    <div class="p-8 text-center text-slate-400 text-xs flex flex-col items-center justify-center gap-2">
                        <i class="fas fa-circle-notch fa-spin text-lg text-blue-500"></i>
                        <span>Memuat data lokasi...</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Panel: Map (7 Cols) -->
        <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs flex flex-col gap-4 h-fit">
            
            <!-- Map Search Bar & Controls -->
            <div class="flex items-center justify-between gap-3 flex-wrap">
                <div class="relative flex-1 max-w-md">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                    <input type="text" id="map-search" placeholder="Cari nama jalan, gedung, atau daerah..." 
                        class="w-full pl-8 pr-20 py-2 bg-slate-50 focus:bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition shadow-2xs placeholder-slate-400">
                    <button id="search-btn" class="absolute right-1 top-1 bottom-1 px-3 bg-blue-600 hover:bg-blue-700 active:scale-[0.98] text-white font-semibold rounded-lg text-xs transition flex items-center gap-1.5 shadow-xs">
                        <span>Cari</span>
                    </button>
                </div>

                <div class="flex items-center gap-1.5 text-[11px] font-semibold text-slate-500 bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-200 shadow-2xs">
                    <i class="fas fa-crosshairs text-blue-500"></i>
                    <span>Peta Interaktif GPS</span>
                </div>
            </div>

            <!-- Map Container -->
            <div class="relative w-full rounded-2xl overflow-hidden border border-slate-200 shadow-inner">
                <div id="map"></div>
                
                <!-- Floating Info Badge on Map -->
                <div class="absolute bottom-3 left-3 z-[500] bg-white/95 backdrop-blur-md border border-slate-200/80 rounded-xl px-3.5 py-2 text-[11px] text-slate-700 font-medium shadow-md flex items-center gap-2 select-none">
                    <span class="w-2 h-2 rounded-full bg-orange-500 animate-ping"></span>
                    <i class="fas fa-arrows-alt text-orange-500 text-xs"></i>
                    <span>Geser pin (drag) atau klik peta untuk memindahkan titik lokasi</span>
                </div>
            </div>
            
        </div>

    <!-- Modal Detail Karyawan Wajib Absen -->
    <div id="assigned-modal" class="fixed inset-0 z-[9999] hidden items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-md w-full overflow-hidden flex flex-col max-h-[85vh] animate-scale-in">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center text-xs font-bold">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div>
                        <h3 id="modal-loc-name" class="text-sm font-bold text-slate-900 leading-tight">Daftar Karyawan Wajib</h3>
                        <p id="modal-loc-subtitle" class="text-[11px] text-slate-400">Titik Lokasi: -</p>
                    </div>
                </div>
                <button type="button" onclick="closeAssignedModal()" class="w-7 h-7 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center transition">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
            <div class="p-3 border-b border-slate-100 bg-white">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" id="modal-search-input" oninput="filterModalKaryawan(this.value)" placeholder="Cari nama atau NIK..."
                        class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 text-slate-800">
                </div>
            </div>
            <div id="modal-karyawan-list" class="flex-1 overflow-y-auto custom-scrollbar p-3 space-y-1.5 divide-y divide-slate-100">
                <!-- Dynamic list -->
            </div>
            <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/50 flex justify-between items-center">
                <span id="modal-count-info" class="text-[11px] font-bold text-purple-700 bg-purple-50 px-2 py-0.5 rounded-md border border-purple-100">0 Karyawan</span>
                <button type="button" onclick="closeAssignedModal()" class="px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-200/70 bg-slate-100 rounded-xl transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    
    <script>
        // API Base URL mapping to backend
        const API_BASE_URL = "{{ url('') }}";

        // Inisialisasi Map ke Indonesia
        const map = L.map('map', { zoomControl: false }).setView([-2.548926, 118.0148634], 5);
        L.control.zoom({ position: 'bottomright' }).addTo(map);

        // Google Maps Tile Layer
        L.tileLayer('https://mt1.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
            attribution: '&copy; Google Maps',
            maxZoom: 20
        }).addTo(map);

        // State variables
        let currentMarker = null;
        let currentCircle = null;
        let mapMarkers = [];
        let isEditing = false;
        let allLocationsData = [];
        let activeLocationId = null;
        let allKaryawansData = [];
        let selectedKaryawanIds = new Set();
        let currentPenugasanType = 'semua';
        let currentModalKaryawans = [];

        // Elements
        const form = document.getElementById('location-form');
        const formTitle = document.getElementById('form-title');
        const formSubtitle = document.getElementById('form-subtitle');
        const formIcon = document.getElementById('form-icon');
        const formIconWrap = document.getElementById('form-icon-wrap');
        const modeBadge = document.getElementById('mode-badge');
        const submitBtnText = document.getElementById('submit-btn-text');
        const locationIdInput = document.getElementById('location-id');
        const namaInput = document.getElementById('nama-lokasi');
        const latInput = document.getElementById('latitude');
        const lonInput = document.getElementById('longitude');
        const radiusInput = document.getElementById('radius');
        const ketInput = document.getElementById('keterangan');
        const isActiveInput = document.getElementById('is-active');
        const cancelEditBtn = document.getElementById('cancel-edit-btn');
        const locationsList = document.getElementById('locations-list');
        const countBadge = document.getElementById('location-count');
        const headerTotalCount = document.getElementById('header-total-count');
        const searchInput = document.getElementById('map-search');
        const searchBtn = document.getElementById('search-btn');

        // Elements Penugasan Karyawan
        const penugasanSemuaRadio = document.getElementById('penugasan-semua');
        const penugasanKhususRadio = document.getElementById('penugasan-khusus');
        const karyawanPickerContainer = document.getElementById('karyawan-picker-container');
        const karyawanChecklistList = document.getElementById('karyawan-checklist-list');
        const selectedKaryawanBadge = document.getElementById('selected-karyawan-count-badge');
        const karyawanSearchInput = document.getElementById('karyawan-search-input');

        // Load Karyawan List dari Backend API
        async function loadKaryawans() {
            try {
                const response = await fetch(`${API_BASE_URL}/api/lokasi-absensi/karyawans`);
                allKaryawansData = await response.json();
                renderKaryawanChecklist(allKaryawansData);
            } catch (err) {
                console.error('Failed to load karyawans:', err);
                karyawanChecklistList.innerHTML = `
                    <div class="p-3 text-center text-rose-500 text-xs">
                        Gagal memuat data karyawan.
                    </div>
                `;
            }
        }

        // Render Karyawan Checklist
        function renderKaryawanChecklist(karyawans) {
            if (!karyawanChecklistList) return;
            if (karyawans.length === 0) {
                karyawanChecklistList.innerHTML = `<div class="p-4 text-center text-slate-400 text-xs">Tidak ada karyawan yang cocok.</div>`;
                return;
            }

            karyawanChecklistList.innerHTML = karyawans.map(k => {
                const isChecked = selectedKaryawanIds.has(Number(k.id));
                const initial = (k.nama_lengkap || 'K').charAt(0).toUpperCase();
                return `
                    <label class="flex items-center gap-2.5 p-2 hover:bg-purple-50/40 cursor-pointer select-none transition">
                        <input type="checkbox" value="${k.id}" ${isChecked ? 'checked' : ''} onchange="toggleKaryawanSelected(${k.id}, this.checked)"
                            class="karyawan-checkbox w-3.5 h-3.5 text-purple-600 rounded border-slate-300 focus:ring-purple-500">
                        <div class="w-6 h-6 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-[10px] shrink-0">
                            ${initial}
                        </div>
                        <div class="min-w-0 flex-1 leading-tight">
                            <span class="block text-xs font-semibold text-slate-800 truncate">${k.nama_lengkap}</span>
                            <span class="block text-[10px] text-slate-400 truncate">NIK: ${k.nik || '-'} &bull; ${k.divisi || 'Umum'}</span>
                        </div>
                    </label>
                `;
            }).join('');
        }

        // Filter Karyawan Checklist saat mengetik pencarian
        function filterKaryawanChecklist(query) {
            query = (query || '').toLowerCase().trim();
            if (!query) {
                renderKaryawanChecklist(allKaryawansData);
                return;
            }
            const filtered = allKaryawansData.filter(k => {
                const nama = (k.nama_lengkap || '').toLowerCase();
                const nik = (k.nik || '').toLowerCase();
                const div = (k.divisi || '').toLowerCase();
                return nama.includes(query) || nik.includes(query) || div.includes(query);
            });
            renderKaryawanChecklist(filtered);
        }

        function toggleKaryawanSelected(id, checked) {
            id = Number(id);
            if (checked) {
                selectedKaryawanIds.add(id);
            } else {
                selectedKaryawanIds.delete(id);
            }
            updateSelectedKaryawanBadge();
        }

        function selectAllKaryawan(selectAll) {
            const query = (karyawanSearchInput.value || '').toLowerCase().trim();
            const targets = query 
                ? allKaryawansData.filter(k => (k.nama_lengkap || '').toLowerCase().includes(query) || (k.nik || '').toLowerCase().includes(query))
                : allKaryawansData;

            targets.forEach(k => {
                if (selectAll) selectedKaryawanIds.add(Number(k.id));
                else selectedKaryawanIds.delete(Number(k.id));
            });

            filterKaryawanChecklist(karyawanSearchInput.value);
            updateSelectedKaryawanBadge();
        }

        function updateSelectedKaryawanBadge() {
            const count = selectedKaryawanIds.size;
            if (currentPenugasanType === 'khusus') {
                selectedKaryawanBadge.classList.remove('hidden');
                selectedKaryawanBadge.innerText = `${count} Karyawan Dipilih`;
            } else {
                selectedKaryawanBadge.classList.add('hidden');
            }
        }

        function togglePenugasanType(type) {
            currentPenugasanType = type;
            if (type === 'khusus') {
                karyawanPickerContainer.classList.remove('hidden');
                updateSelectedKaryawanBadge();
            } else {
                karyawanPickerContainer.classList.add('hidden');
                selectedKaryawanBadge.classList.add('hidden');
            }
        }

        // Fetch lokasi dari backend
        async function loadLocations() {
            try {
                const response = await fetch(`${API_BASE_URL}/api/lokasi-absensi`);
                const locations = await response.json();
                allLocationsData = locations;
                
                // Clear previous markers
                mapMarkers.forEach(m => map.removeLayer(m));
                mapMarkers = [];
                
                renderLocationsList(locations);

                // Draw markers and boundaries on map
                locations.forEach(loc => {
                    const marker = L.marker([loc.latitude, loc.longitude]).addTo(map);
                    marker.locationId = loc.id;
                    const isActive = loc.is_active == 1;
                    const penugasanBadge = loc.tipe_penugasan === 'khusus'
                        ? `<span class="font-medium text-purple-700 bg-purple-50 px-1.5 py-0.5 rounded border border-purple-100">${(loc.assigned_karyawans?.length || 0)} Karyawan</span>`
                        : `<span class="font-medium text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-100">Semua Karyawan</span>`;

                    marker.bindPopup(`
                        <div class="p-1 space-y-1">
                            <div class="font-bold text-xs text-slate-900 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full ${isActive ? 'bg-emerald-500' : 'bg-rose-500'}"></span>
                                <span>${loc.nama_lokasi}</span>
                            </div>
                            <p class="text-[10px] text-slate-500 leading-tight">${loc.keterangan || 'Tidak ada catatan'}</p>
                            <div class="pt-1.5 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-500 gap-2">
                                <span class="font-semibold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-100">R: ${loc.radius}m</span>
                                ${penugasanBadge}
                                <span class="font-mono text-slate-400">${loc.latitude.toFixed(4)}, ${loc.longitude.toFixed(4)}</span>
                            </div>
                        </div>
                    `);
                    
                    const circleColor = isActive ? '#2563eb' : '#ef4444';
                    const circle = L.circle([loc.latitude, loc.longitude], {
                        color: circleColor,
                        fillColor: circleColor,
                        fillOpacity: isActive ? 0.08 : 0.04,
                        radius: loc.radius,
                        interactive: false // Clicks pass through to map
                    }).addTo(map);
                    circle.locationId = loc.id;

                    mapMarkers.push(marker);
                    mapMarkers.push(circle);
                });

            } catch (err) {
                console.error('Failed to load locations:', err);
                locationsList.innerHTML = `
                    <div class="p-8 text-center text-rose-500 text-xs flex flex-col items-center gap-1.5">
                        <i class="fas fa-exclamation-triangle text-base"></i>
                        <span>Gagal memuat data dari API.</span>
                    </div>
                `;
            }
        }

        // Render card list
        function renderLocationsList(locations) {
            if (locations.length === 0) {
                locationsList.innerHTML = `
                    <div class="p-8 text-center text-slate-400 text-xs flex flex-col items-center justify-center gap-2">
                        <i class="fas fa-map-marker-alt text-2xl text-slate-300"></i>
                        <p class="font-medium text-slate-600">Belum ada lokasi absensi</p>
                        <p class="text-[11px] text-slate-400">Klik pada peta atau isi formulir di atas untuk mendaftarkan titik lokasi baru.</p>
                    </div>
                `;
                countBadge.innerText = '0 Lokasi';
                if (headerTotalCount) headerTotalCount.innerText = '0 Lokasi';
                return;
            }

            countBadge.innerText = `${locations.length} Lokasi`;
            if (headerTotalCount) headerTotalCount.innerText = `${locations.length} Lokasi`;

            locationsList.innerHTML = locations.map(loc => {
                const locJson = JSON.stringify(loc).replace(/"/g, '&quot;');
                const isActive = loc.is_active == 1;
                const isSelected = (activeLocationId === loc.id);
                const assignedCount = (loc.assigned_karyawans || []).length;
                const penugasanTag = loc.tipe_penugasan === 'khusus'
                    ? `<button type="button" onclick="event.stopPropagation(); openAssignedModal(${loc.id})" 
                            class="inline-flex items-center gap-1 text-[10px] font-semibold bg-purple-50 hover:bg-purple-100 text-purple-700 px-2 py-0.5 rounded-md border border-purple-200 transition shadow-2xs" 
                            title="Klik untuk melihat ${assignedCount} karyawan yang wajib absen">
                            <i class="fas fa-user-check text-[8px]"></i> ${assignedCount} Karyawan Wajib
                       </button>`
                    : `<span class="inline-flex items-center gap-1 text-[10px] bg-sky-50 text-sky-700 font-semibold px-2 py-0.5 rounded-md border border-sky-200">
                            <i class="fas fa-users text-[8px]"></i> Semua Karyawan
                       </span>`;

                return `
                    <div id="loc-card-${loc.id}" 
                         class="location-item p-3.5 hover:bg-slate-50/80 transition cursor-pointer flex justify-between items-start gap-3 ${isSelected ? 'active-item' : ''}" 
                         onclick="focusLocation(${loc.latitude}, ${loc.longitude}, ${loc.radius}, ${loc.id})">
                        <div class="flex items-start gap-3 min-w-0 flex-1">
                            <div class="w-8 h-8 rounded-xl ${isActive ? 'bg-blue-50 text-blue-600 border border-blue-100' : 'bg-slate-100 text-slate-400'} flex items-center justify-center text-xs flex-shrink-0 mt-0.5 shadow-2xs">
                                <i class="fas fa-map-pin"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h5 class="font-bold text-slate-800 text-xs truncate leading-snug">${loc.nama_lokasi}</h5>
                                <p class="text-[11px] text-slate-400 truncate mt-0.5">${loc.keterangan || 'Tidak ada catatan'}</p>
                                <div class="flex flex-wrap gap-1.5 items-center mt-2">
                                    <span class="inline-flex items-center gap-1 text-[10px] font-semibold bg-slate-100 text-slate-600 px-2 py-0.5 rounded-md border border-slate-200">
                                        <i class="fas fa-bullseye text-[8px] text-slate-400"></i> ${loc.radius} m
                                    </span>
                                    ${penugasanTag}
                                    ${isActive ? 
                                        `<span class="inline-flex items-center gap-1 text-[10px] bg-emerald-50 text-emerald-700 font-semibold px-2 py-0.5 rounded-md border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                        </span>` : 
                                        `<span class="inline-flex items-center gap-1 text-[10px] bg-slate-100 text-slate-500 font-semibold px-2 py-0.5 rounded-md border border-slate-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Nonaktif
                                        </span>`
                                    }
                                    <span class="font-mono text-[10px] text-slate-400 tracking-tight">${loc.latitude.toFixed(5)}, ${loc.longitude.toFixed(5)}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0 pt-0.5" onclick="event.stopPropagation()">
                            <button type="button" onclick="editLocation(${locJson})" title="Edit Lokasi"
                                class="w-7 h-7 flex items-center justify-center rounded-lg bg-slate-100 hover:bg-blue-50 text-slate-600 hover:text-blue-600 transition border border-slate-200 hover:border-blue-200 shadow-2xs">
                                <i class="fas fa-pen text-[10px]"></i>
                            </button>
                            <button type="button" onclick="deleteLocation(${loc.id})" title="Hapus Lokasi"
                                class="w-7 h-7 flex items-center justify-center rounded-lg bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-600 transition border border-slate-200 hover:border-rose-200 shadow-2xs">
                                <i class="fas fa-trash-alt text-[10px]"></i>
                            </button>
                        </div>
                    </div>
                `;
            }).join('');
        }

        // Filter list input handler
        function filterLocationsList(query) {
            query = (query || '').toLowerCase().trim();
            if (!query) {
                renderLocationsList(allLocationsData);
                return;
            }
            const filtered = allLocationsData.filter(loc => {
                const name = (loc.nama_lokasi || '').toLowerCase();
                const ket = (loc.keterangan || '').toLowerCase();
                return name.includes(query) || ket.includes(query);
            });
            renderLocationsList(filtered);
        }

        function focusLocation(lat, lon, radius, id) {
            activeLocationId = id;
            document.querySelectorAll('.location-item').forEach(el => el.classList.remove('active-item'));
            const card = document.getElementById('loc-card-' + id);
            if (card) card.classList.add('active-item');

            map.setView([lat, lon], 17);
            if (!isEditing) {
                updateTempVisuals(lat, lon, radius);
            }
        }

        function updateTempVisuals(lat, lon, radius) {
            if (currentMarker) map.removeLayer(currentMarker);
            if (currentCircle) map.removeLayer(currentCircle);

            const r = parseInt(radius) || 100;

            // Marker draggable
            currentMarker = L.marker([lat, lon], {
                draggable: true,
                autoPan: true
            }).addTo(map);

            currentCircle = L.circle([lat, lon], {
                color: '#ea580c', // Orange 600
                fillColor: '#ea580c',
                fillOpacity: 0.15,
                radius: r,
                interactive: false // Allows click to pass to map
            }).addTo(map);

            currentMarker.bindTooltip('Geser pin untuk memindahkan titik', {
                direction: 'top',
                offset: [0, -32]
            });

            // Dragging events
            currentMarker.on('drag', function(e) {
                const pos = e.target.getLatLng();
                if (currentCircle) {
                    currentCircle.setLatLng(pos);
                }
                latInput.value = pos.lat.toFixed(7);
                lonInput.value = pos.lng.toFixed(7);
            });

            currentMarker.on('dragend', function(e) {
                const pos = e.target.getLatLng();
                latInput.value = pos.lat.toFixed(7);
                lonInput.value = pos.lng.toFixed(7);
                if (currentCircle) {
                    currentCircle.setLatLng(pos);
                }
            });
        }

        // Map Click Action (memindahkan pin & lingkaran)
        map.on('click', function(e) {
            const lat = e.latlng.lat;
            const lon = e.latlng.lng;
            const radius = radiusInput.value || 100;

            latInput.value = lat.toFixed(7);
            lonInput.value = lon.toFixed(7);

            updateTempVisuals(lat, lon, radius);
        });

        // ---- Radius helpers (slider, preset, sync) ----
        const radiusSlider = document.getElementById('radius-slider');

        function syncRadiusSlider(val) {
            const v = Math.min(Math.max(parseInt(val) || 10, 10), 5000);
            if (radiusSlider) radiusSlider.value = Math.min(v, 1000);
            updateActivePresetBtn(v);
            const lat = parseFloat(latInput.value);
            const lon = parseFloat(lonInput.value);
            if (!isNaN(lat) && !isNaN(lon)) updateTempVisuals(lat, lon, v);
        }

        function syncRadiusInput(val) {
            const v = parseInt(val) || 10;
            radiusInput.value = v;
            updateActivePresetBtn(v);
            const lat = parseFloat(latInput.value);
            const lon = parseFloat(lonInput.value);
            if (!isNaN(lat) && !isNaN(lon)) updateTempVisuals(lat, lon, v);
        }

        function setRadiusPreset(val) {
            radiusInput.value = val;
            if (radiusSlider) radiusSlider.value = Math.min(val, 1000);
            updateActivePresetBtn(val);
            const lat = parseFloat(latInput.value);
            const lon = parseFloat(lonInput.value);
            if (!isNaN(lat) && !isNaN(lon)) updateTempVisuals(lat, lon, val);
        }

        function updateActivePresetBtn(val) {
            document.querySelectorAll('.radius-preset-btn').forEach(btn => {
                const bv = parseInt(btn.dataset.value);
                if (bv === val) {
                    btn.classList.add('bg-orange-100', 'border-orange-400', 'text-orange-700');
                    btn.classList.remove('bg-slate-50', 'border-slate-200', 'text-slate-600');
                } else {
                    btn.classList.remove('bg-orange-100', 'border-orange-400', 'text-orange-700');
                    btn.classList.add('bg-slate-50', 'border-slate-200', 'text-slate-600');
                }
            });
        }

        // Init preset state
        updateActivePresetBtn(parseInt(radiusInput.value) || 100);



        // Manual Lat/Lon Input Dynamic Update
        function handleManualCoordChange() {
            const lat = parseFloat(latInput.value);
            const lon = parseFloat(lonInput.value);
            const radius = radiusInput.value || 100;

            if (!isNaN(lat) && !isNaN(lon)) {
                updateTempVisuals(lat, lon, radius);
                map.panTo([lat, lon]);
            }
        }
        latInput.addEventListener('change', handleManualCoordChange);
        lonInput.addEventListener('change', handleManualCoordChange);

        // Save (POST / PUT) Location
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            if (currentPenugasanType === 'khusus' && selectedKaryawanIds.size === 0) {
                alert('Silakan pilih minimal 1 karyawan wajib absen untuk opsi "Karyawan Tertentu".');
                return;
            }

            const id = locationIdInput.value;
            const payload = {
                nama_lokasi: namaInput.value,
                latitude: parseFloat(latInput.value),
                longitude: parseFloat(lonInput.value),
                radius: parseInt(radiusInput.value) || 100,
                keterangan: ketInput.value,
                is_active: isActiveInput.checked ? 1 : 0,
                tipe_penugasan: currentPenugasanType,
                karyawan_ids: currentPenugasanType === 'khusus' ? Array.from(selectedKaryawanIds) : []
            };

            const url = id ? `${API_BASE_URL}/api/lokasi-absensi/${id}` : `${API_BASE_URL}/api/lokasi-absensi`;
            const method = id ? 'PUT' : 'POST';

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();
                if (!response.ok) throw new Error(data.error || 'Terjadi kesalahan');

                alert(data.message || 'Lokasi berhasil disimpan!');
                resetForm();
                loadLocations();
            } catch (err) {
                alert('Gagal menyimpan: ' + err.message);
            }
        });

        function editLocation(loc) {
            isEditing = true;
            activeLocationId = loc.id;
            
            // Switch form to edit styling
            formTitle.textContent = 'Edit Lokasi: ' + loc.nama_lokasi;
            formSubtitle.textContent = 'Sesuaikan posisi pin di peta atau form koordinat';
            formIcon.className = 'fas fa-pen';
            formIconWrap.className = 'w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-xs font-bold shadow-2xs transition-colors';
            modeBadge.classList.remove('hidden');
            submitBtnText.textContent = 'Perbarui Lokasi';

            locationIdInput.value = loc.id;
            namaInput.value = loc.nama_lokasi;
            latInput.value = loc.latitude;
            lonInput.value = loc.longitude;
            radiusInput.value = loc.radius;
            if (radiusSlider) radiusSlider.value = Math.min(parseInt(loc.radius) || 100, 1000);
            updateActivePresetBtn(parseInt(loc.radius) || 100);
            ketInput.value = loc.keterangan || '';
            isActiveInput.checked = loc.is_active == 1;

            // Set tipe penugasan & checklist karyawan
            currentPenugasanType = loc.tipe_penugasan || 'semua';
            if (currentPenugasanType === 'khusus') {
                penugasanKhususRadio.checked = true;
                penugasanSemuaRadio.checked = false;
                karyawanPickerContainer.classList.remove('hidden');
                selectedKaryawanIds = new Set((loc.assigned_karyawan_ids || []).map(Number));
            } else {
                penugasanSemuaRadio.checked = true;
                penugasanKhususRadio.checked = false;
                karyawanPickerContainer.classList.add('hidden');
                selectedKaryawanIds = new Set();
            }

            if (karyawanSearchInput) karyawanSearchInput.value = '';
            filterKaryawanChecklist('');
            updateSelectedKaryawanBadge();

            // Sembunyikan marker lama milik lokasi ini saat sedang diedit agar tidak bertumpuk
            mapMarkers.forEach(layer => {
                if (layer.locationId === loc.id) {
                    map.removeLayer(layer);
                } else if (!map.hasLayer(layer)) {
                    layer.addTo(map);
                }
            });
            
            cancelEditBtn.classList.remove('hidden');
            updateTempVisuals(loc.latitude, loc.longitude, loc.radius);
            map.setView([loc.latitude, loc.longitude], 17);

            // Buka tooltip sejenak sebagai panduan visual
            if (currentMarker) {
                currentMarker.openTooltip();
                setTimeout(() => {
                    if (currentMarker) currentMarker.closeTooltip();
                }, 2500);
            }

            // Highlight card in list
            document.querySelectorAll('.location-item').forEach(el => el.classList.remove('active-item'));
            const card = document.getElementById('loc-card-' + loc.id);
            if (card) {
                card.classList.add('active-item');
                card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }

        cancelEditBtn.addEventListener('click', resetForm);

        function resetForm() {
            isEditing = false;
            activeLocationId = null;
            
            // Switch form back to add styling
            formTitle.textContent = 'Tambah Lokasi Absensi';
            formSubtitle.textContent = 'Tentukan nama dan koordinat titik lokasi baru';
            formIcon.className = 'fas fa-plus';
            formIconWrap.className = 'w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-bold shadow-2xs transition-colors';
            modeBadge.classList.add('hidden');
            submitBtnText.textContent = 'Simpan Lokasi';

            locationIdInput.value = '';
            form.reset();
            isActiveInput.checked = true;

            // Reset Penugasan
            currentPenugasanType = 'semua';
            penugasanSemuaRadio.checked = true;
            penugasanKhususRadio.checked = false;
            karyawanPickerContainer.classList.add('hidden');
            selectedKaryawanIds.clear();
            if (karyawanSearchInput) karyawanSearchInput.value = '';
            filterKaryawanChecklist('');
            updateSelectedKaryawanBadge();

            // Kembalikan marker yang sempat disembunyikan saat mode edit
            mapMarkers.forEach(layer => {
                if (!map.hasLayer(layer)) {
                    layer.addTo(map);
                }
            });

            document.querySelectorAll('.location-item').forEach(el => el.classList.remove('active-item'));
            
            cancelEditBtn.classList.add('hidden');
            if (currentMarker) map.removeLayer(currentMarker);
            if (currentCircle) map.removeLayer(currentCircle);
            currentMarker = null;
            currentCircle = null;
        }

        // Delete Location
        async function deleteLocation(id) {
            if (!confirm('Apakah Anda yakin ingin menghapus lokasi absensi ini?')) return;

            try {
                const response = await fetch(`${API_BASE_URL}/api/lokasi-absensi/${id}`, { method: 'DELETE' });
                const data = await response.json();
                if (!response.ok) throw new Error(data.error || 'Gagal menghapus');

                alert('Lokasi berhasil dihapus.');
                if (locationIdInput.value == id) {
                    resetForm();
                }
                loadLocations();
            } catch (err) {
                alert('Gagal menghapus: ' + err.message);
            }
        }

        // Modal Karyawan Wajib Absen
        function openAssignedModal(locId) {
            const loc = allLocationsData.find(l => l.id === locId);
            if (!loc) return;

            document.getElementById('modal-loc-name').innerText = `Karyawan Wajib: ${loc.nama_lokasi}`;
            document.getElementById('modal-loc-subtitle').innerText = `Radius: ${loc.radius}m • Koordinat: ${loc.latitude.toFixed(4)}, ${loc.longitude.toFixed(4)}`;
            
            currentModalKaryawans = loc.assigned_karyawans || [];
            renderModalKaryawanList(currentModalKaryawans);
            
            const modal = document.getElementById('assigned-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeAssignedModal() {
            const modal = document.getElementById('assigned-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function renderModalKaryawanList(karyawans) {
            const listEl = document.getElementById('modal-karyawan-list');
            const countInfo = document.getElementById('modal-count-info');
            countInfo.innerText = `${karyawans.length} Karyawan Wajib`;

            if (karyawans.length === 0) {
                listEl.innerHTML = `<div class="p-6 text-center text-slate-400 text-xs">Belum ada karyawan yang ditugaskan pada titik ini.</div>`;
                return;
            }

            listEl.innerHTML = karyawans.map((k, idx) => `
                <div class="p-2.5 flex items-center justify-between gap-3 hover:bg-slate-50 rounded-xl transition">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-7 h-7 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-xs shrink-0">
                            ${idx + 1}
                        </div>
                        <div class="min-w-0">
                            <h6 class="text-xs font-bold text-slate-800 truncate">${k.nama_lengkap}</h6>
                            <span class="text-[10px] text-slate-400 truncate block">NIK: ${k.nik || '-'} &bull; ${k.divisi || 'Umum'}</span>
                        </div>
                    </div>
                    <span class="text-[10px] font-semibold bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded border border-emerald-200 shrink-0">
                        Wajib
                    </span>
                </div>
            `).join('');
        }

        function filterModalKaryawan(query) {
            query = (query || '').toLowerCase().trim();
            if (!query) {
                renderModalKaryawanList(currentModalKaryawans);
                return;
            }
            const filtered = currentModalKaryawans.filter(k => 
                (k.nama_lengkap || '').toLowerCase().includes(query) || 
                (k.nik || '').toLowerCase().includes(query) ||
                (k.divisi || '').toLowerCase().includes(query)
            );
            renderModalKaryawanList(filtered);
        }

        // Close modal on click outside backdrop
        document.getElementById('assigned-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeAssignedModal();
            }
        });

        // OSM Nominatim Search
        async function performSearch() {
            const query = searchInput.value.trim();
            if (!query) return;

            searchBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin text-xs"></i>';
            searchBtn.disabled = true;

            try {
                let response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&limit=1&countrycodes=id&q=${encodeURIComponent(query)}`);
                let results = await response.json();
                
                // Fallback: If not found, simplify the address automatically
                if (results.length === 0) {
                    const cleanedQuery = query
                        .replace(/rt\s*\.?\s*\d+\s*[\/\-]?\s*rw\s*\.?\s*\d+/gi, '')
                        .replace(/blok\s+[a-z0-9]+/gi, '')
                        .replace(/no\s*\.?\s*\d+/gi, '')
                        .replace(/\s+/g, ' ')
                        .trim();
                        
                    if (cleanedQuery && cleanedQuery !== query) {
                        response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&limit=1&countrycodes=id&q=${encodeURIComponent(cleanedQuery)}`);
                        results = await response.json();
                    }
                }

                if (results.length === 0) {
                    alert('Lokasi tidak ditemukan. Coba masukkan nama jalan atau daerah yang lebih umum.');
                    return;
                }

                const result = results[0];
                const lat = parseFloat(result.lat);
                const lon = parseFloat(result.lon);
                const radius = radiusInput.value || 100;

                latInput.value = lat.toFixed(7);
                lonInput.value = lon.toFixed(7);

                map.setView([lat, lon], 17);
                updateTempVisuals(lat, lon, radius);
            } catch (err) {
                console.error('Search failed:', err);
                alert('Gagal melakukan pencarian.');
            } finally {
                searchBtn.innerHTML = '<span>Cari</span>';
                searchBtn.disabled = false;
            }
        }

        searchBtn.addEventListener('click', performSearch);
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') performSearch();
        });

        // Load data on startup
        loadKaryawans();
        loadLocations();
    </script>
@endpush
