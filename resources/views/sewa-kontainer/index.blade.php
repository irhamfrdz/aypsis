@extends('layouts.app')

@section('title', 'Portal Sewa Kontainer')
@section('page_title', 'Portal Sewa Kontainer')

@push('styles')
<style>
    /* Transisi tab dan elemen dasar */
    .sk-tab-btn { transition: all 0.2s; cursor: pointer; }
    .sk-sub-tab { transition: all 0.15s; cursor: pointer; }
    .sk-panel { display: none; }
    .sk-panel.active { display: block; animation: fadeIn 0.3s ease-in-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
    
    /* Input dan Badge */
    .sk-badge { display: inline-flex; align-items: center; padding: 2px 8px; border-radius: 9999px; font-size: 10px; font-weight: 600; }
    .sk-input { width: 100%; font-size: 0.875rem; border: 1px solid #e2e8f0; border-radius: 12px; padding: 8px 14px; background: #fff; transition: all 0.2s; }
    .sk-input:focus { outline: none; border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.1); }
    
    /* Tombol */
    .sk-btn-primary { display: inline-flex; align-items: center; justify-content: center; padding: 8px 16px; font-size: 0.875rem; font-weight: 600; color: #fff; background: #4338ca; border-radius: 12px; transition: all 0.15s; cursor: pointer; border: none; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
    .sk-btn-primary:hover { background: #3730a3; }
    .sk-btn-secondary { display: inline-flex; align-items: center; justify-content: center; padding: 4px 10px; font-size: 11px; font-weight: 600; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; transition: all 0.15s; cursor: pointer; }
    
    /* Tabel Data */
    .sk-table { width: 100%; text-align: left; border-collapse: collapse; font-size: 0.75rem; }
    .sk-table thead { background: #f8fafc; border-bottom: 2px solid #e2e8f0; }
    .sk-table th { padding: 12px 16px; font-weight: 700; color: #475569; text-transform: uppercase; font-size: 10px; letter-spacing: 0.05em; }
    .sk-table td { padding: 12px 16px; border-bottom: 1px solid #f1f5f9; color: #334155; }
    .sk-table tbody tr:hover { background: #f8fafc; }
    
    /* Tabel Periode Tagihan */
    .sk-period-table { width: 100%; font-size: 11px; }
    .sk-period-table th { padding: 8px 12px; background: #f1f5f9; font-weight: 700; color: #475569; border-bottom: 1px solid #e2e8f0; text-transform: uppercase; font-size: 9px; letter-spacing: 0.05em; }
    .sk-period-table td { padding: 8px 12px; border-bottom: 1px dashed #e2e8f0; }
    
    /* Custom Scrollbar untuk bagian overflow */
    .sk-scroll::-webkit-scrollbar { width: 6px; height: 6px; }
    .sk-scroll::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
    .sk-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .sk-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

    /* Fix background untuk layout AYPSIS agar menyatu */
    .content-wrapper { padding: 0 !important; } /* override aypsis padding */
</style>
@endpush

@section('content')
<div class="min-h-screen transition-colors duration-300 text-slate-800 font-sans antialiased bg-indigo-50/20" id="main-applet-shell" style="margin: -24px;">
    
    {{-- PROFESSIONAL HIGH-CONTRAST HEADER --}}
    <header class="transition-colors duration-350 text-white border-b sticky top-0 z-40 shadow-sm bg-indigo-950 border-indigo-900/40" id="navbar-top">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-xl text-white border shadow-inner bg-indigo-800/80 border-indigo-700/55">
                    <svg class="w-6 h-6 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v20m-7-7h14m-10 7a3 3 0 01-6 0v-4a3 3 0 016 0zm14 0a3 3 0 01-6 0v-4a3 3 0 016 0z" /></svg>
                </div>
                <div>
                    <h1 class="font-bold text-base tracking-tight text-white uppercase flex flex-wrap items-center gap-2">
                        <span>PORTAL SEWA KONTAINER</span>
                        <span class="text-[9px] px-2.5 py-0.5 rounded-full font-bold uppercase border bg-indigo-900/90 text-indigo-300 border-indigo-800">
                            Pihak Penyewa (Sewa In / Lessee)
                        </span>
                        <span class="bg-emerald-900/60 text-emerald-300 border border-emerald-800 text-[9px] px-2 py-0.5 rounded-full font-semibold">
                            Tersimpan di Database (Live)
                        </span>
                    </h1>
                    <p class="text-[10px] italic text-indigo-250 mt-0.5">
                        Sistem kontrol biaya pengeluaran, PPN Masukan, PPh 23, serta rekonsiliasi tagihan dari Vendor
                    </p>
                </div>
            </div>
            
            <div class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl border text-xs self-start md:self-center bg-indigo-900/40 border-indigo-800/40">
                <span class="w-2 h-2 rounded-full animate-pulse bg-emerald-400"></span>
                <span class="text-slate-300 uppercase font-bold text-[9px] tracking-wider font-mono">SERVER DATABASE AKTIF</span>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

        {{-- Notifikasi --}}
        <div id="sk-noti" class="hidden shadow-sm transition-all"></div>

        {{-- STATUS DATABASE CONSOLE --}}
        <div class="border p-4 rounded-2xl flex flex-col md:flex-row items-center justify-between gap-4 transition-all duration-300 bg-indigo-950/[0.03] border-indigo-800/10">
            <div class="flex items-center gap-3 text-slate-700">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 bg-emerald-400"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                </span>
                <div>
                    <p class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                        <span>Penyimpanan Otomatis Aktif di Database Cloud (AYPSIS)</span>
                    </p>
                    <p class="text-[10px] text-slate-500 mt-0.5">
                        Semua data Anda otomatis tersimpan saat di-input dan tersinkronisasi secara real-time ke seluruh perangkat.
                    </p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2.5 self-stretch md:self-auto">
                <button type="button" class="inline-flex items-center justify-center px-4 py-2 text-xs font-bold bg-white hover:bg-slate-50 border rounded-xl transition-all shadow-sm gap-1.5 text-indigo-800 border-indigo-600/35">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" /></svg>
                    <span>Log Aktivitas (Segera)</span>
                </button>
            </div>
        </div>

        {{-- KPI METRIC CARDS GRID (BENTO BOX) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4" id="kpi-grids-box">
            
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Total Kontainer Terdaftar</p>
                    <h3 class="text-xl font-bold text-slate-800 mt-1 font-mono" id="kpi-total-kontainer">0 Unit</h3>
                    <p class="text-[10px] text-slate-500 mt-0.5">Semua tipe &amp; ukuran</p>
                </div>
                <div class="p-3 rounded-xl border bg-indigo-50/50 text-indigo-700 border-indigo-100/50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Penyewaan Aktif</p>
                    <h3 class="text-xl font-bold text-slate-800 mt-1 font-mono" id="kpi-active-rentals">0 Siklus</h3>
                    <p class="text-[10px] text-slate-500 mt-0.5">Paralel sewa diperbolehkan</p>
                </div>
                <div class="p-3 rounded-xl border bg-indigo-50 text-indigo-700 border-indigo-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Estimasi Belum Dibayar</p>
                    <h3 class="text-xl font-bold text-rose-700 mt-1 font-mono" id="kpi-total-unpaid">Rp 0</h3>
                    <p class="text-[10px] text-rose-500 mt-0.5">Siklus outstanding bulanan</p>
                </div>
                <div class="bg-rose-50 text-rose-700 p-3 rounded-xl border border-rose-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Terbayar (Lunas)</p>
                    <h3 class="text-xl font-bold mt-1 font-mono text-indigo-700" id="kpi-total-paid">Rp 0</h3>
                    <p class="text-[10px] text-slate-500 mt-0.5">Tanpa sistem cicilan/parsial</p>
                </div>
                <div class="p-3 rounded-xl border bg-indigo-50 text-indigo-700 border-indigo-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                </div>
            </div>

        </div>

        {{-- WORKSPACE NAVIGATION TABS --}}
        <div class="flex border-b border-slate-300" id="tabs-navigation-panel">
            <button class="sk-tab-btn active py-3 px-6 text-xs font-bold border-b-2 transition-all inline-flex items-center gap-2 border-indigo-600 text-indigo-700 font-extrabold" data-tab="billing">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                1. Dasbor Pengeluaran & Pembayaran
            </button>
            <button class="sk-tab-btn py-3 px-6 text-xs font-bold border-b-2 transition-all inline-flex items-center gap-2 border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300" data-tab="rental">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                2. Siklus Sewa In & Kontainer
            </button>
            <button class="sk-tab-btn py-3 px-6 text-xs font-bold border-b-2 transition-all inline-flex items-center gap-2 border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300" data-tab="master">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" /></svg>
                3. Kelola Database Vendor/Owner
            </button>
            <button class="sk-tab-btn py-3 px-6 text-xs font-bold border-b-2 transition-all inline-flex items-center gap-2 border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300" data-tab="import">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                4. Impor Excel Cepat
            </button>
        </div>

        <div class="space-y-6">
            
            {{-- TAB 1: Billing Dashboard --}}
            <div class="sk-panel active" id="panel-billing">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-indigo-100 text-indigo-700 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800 text-sm">Lembar Rekonsiliasi Tagihan Vendor</h3>
                                <p class="text-[10px] text-slate-500">Bandingkan dan cocokkan rincian invoice dari vendor.</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <select id="billing-vendor-filter" class="sk-input py-2" style="max-width:200px;font-size:12px;">
                                <option value="">Semua Vendor</option>
                            </select>
                            <select id="billing-status-filter" class="sk-input py-2" style="max-width:160px;font-size:12px;">
                                <option value="">Semua Status</option>
                                <option value="Belum Ditagih">Belum Ditagih</option>
                                <option value="Pranota">Pranota</option>
                                <option value="Belum Bayar">Belum Bayar</option>
                                <option value="Lunas">Lunas</option>
                            </select>
                            <div class="relative">
                                <svg class="w-4 h-4 absolute left-3 top-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                <input type="text" id="billing-search" class="sk-input py-2 pl-9" style="max-width:200px;font-size:12px;" placeholder="Cari kontainer/invoice...">
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto sk-scroll">
                        <table class="sk-table" id="billing-table">
                            <thead>
                                <tr>
                                    <th>Kontainer</th>
                                    <th>Vendor</th>
                                    <th>Periode</th>
                                    <th>Masa</th>
                                    <th class="text-center">Hari</th>
                                    <th>Tipe</th>
                                    <th class="text-right">Estimasi Sistem</th>
                                    <th>Status</th>
                                    <th>No Invoice</th>
                                    <th class="text-right">Nominal Real (Override)</th>
                                    <th>Aksi Status</th>
                                </tr>
                            </thead>
                            <tbody id="billing-tbody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- TAB 2: Siklus Sewa --}}
            <div class="sk-panel" id="panel-rental">
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                    {{-- Create Sewa Form --}}
                    <div class="xl:col-span-1 bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-2 bg-emerald-100 text-emerald-700 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800 text-sm">Pencatatan Sewa In Baru</h3>
                                <p class="text-[10px] text-slate-500">Mulai kontrak sewa.</p>
                            </div>
                        </div>
                        <form id="form-create-sewa" class="space-y-5">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1.5 uppercase tracking-wide">Pilih Kontainer</label>
                                <select id="sewa-kontainer-select" class="sk-input" required>
                                    <option value="">-- Cari No Kontainer --</option>
                                </select>
                            </div>
                            <div id="sewa-kontainer-info" class="hidden p-3 bg-emerald-50/50 rounded-xl border border-emerald-100 text-xs space-y-1 text-emerald-800"></div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1.5 uppercase tracking-wide">Tanggal Mulai Sewa</label>
                                <div class="relative">
                                    <svg class="w-4 h-4 absolute left-3 top-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    <input type="text" id="sewa-tanggal" class="sk-input pl-9 font-mono" placeholder="YYYY-MM-DD" required>
                                </div>
                            </div>
                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-4">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 mb-1.5 uppercase tracking-wide">Jenis Tarif</label>
                                    <div class="grid grid-cols-2 gap-2 text-xs">
                                        <label class="flex items-center gap-2 p-2.5 bg-white border border-slate-200 rounded-lg cursor-pointer hover:border-indigo-400 transition-colors">
                                            <input type="radio" name="sewa_jenis_tarif" value="Bulanan" checked class="text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                                            <span class="font-medium text-slate-700">Bulanan</span>
                                        </label>
                                        <label class="flex items-center gap-2 p-2.5 bg-white border border-slate-200 rounded-lg cursor-pointer hover:border-indigo-400 transition-colors">
                                            <input type="radio" name="sewa_jenis_tarif" value="Harian" class="text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                                            <span class="font-medium text-slate-700">Harian</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-500 mb-1 uppercase tracking-wide">Bulanan (Rp)</label>
                                        <input type="number" id="sewa-tarif-bulanan" class="sk-input font-mono bg-white" style="font-size:12px;padding:8px 12px;" value="0">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-500 mb-1 uppercase tracking-wide">Harian (Rp)</label>
                                        <input type="number" id="sewa-tarif-harian" class="sk-input font-mono bg-white" style="font-size:12px;padding:8px 12px;" value="0">
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1.5 uppercase tracking-wide">Catatan (Opsional)</label>
                                <textarea id="sewa-catatan" rows="2" class="sk-input resize-none" placeholder="Tulis catatan di sini..."></textarea>
                            </div>
                            <div class="flex items-center gap-2 py-2 bg-indigo-50/50 p-3 rounded-xl border border-indigo-100">
                                <input type="checkbox" id="sewa-ppn" checked class="w-4 h-4 rounded text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                <label for="sewa-ppn" class="text-xs font-semibold text-indigo-900 cursor-pointer">Default Pakai PPN (11%)</label>
                            </div>
                            <button type="submit" class="sk-btn-primary w-full py-2.5 mt-2">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                                Konfirmasi Sewa Kontainer
                            </button>
                        </form>
                    </div>

                    {{-- List Sewa --}}
                    <div class="xl:col-span-2 space-y-4">
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-slate-100 text-slate-700 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-slate-800 text-sm">Siklus Transaksi & Pengembalian</h3>
                                    <p class="text-[10px] text-slate-500">Kelola unit yang sedang dirental dan pengembalian (Off-Hire).</p>
                                </div>
                            </div>
                            <div class="relative w-full sm:w-auto">
                                <svg class="w-4 h-4 absolute left-3 top-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                <input type="text" id="sewa-search" class="sk-input pl-9" style="min-width:250px;" placeholder="Cari No Kontainer/Vendor...">
                            </div>
                        </div>
                        <div id="sewa-list" class="space-y-4">
                            <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center text-slate-400 text-sm">Memuat data...</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TAB 3: Master Data --}}
            <div class="sk-panel" id="panel-master">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="flex border-b border-slate-200 bg-slate-50/80 p-2 gap-1.5 flex-wrap overflow-x-auto sk-scroll">
                        <button class="sk-sub-tab active px-4 py-2.5 text-[11px] font-bold uppercase tracking-wide rounded-xl bg-indigo-600 text-white shadow-sm" data-subtab="vendor">1. Master Vendor</button>
                        <button class="sk-sub-tab px-4 py-2.5 text-[11px] font-bold uppercase tracking-wide rounded-xl text-slate-600 hover:bg-slate-200" data-subtab="tipe">2. Tipe</button>
                        <button class="sk-sub-tab px-4 py-2.5 text-[11px] font-bold uppercase tracking-wide rounded-xl text-slate-600 hover:bg-slate-200" data-subtab="ukuran">3. Ukuran</button>
                        <button class="sk-sub-tab px-4 py-2.5 text-[11px] font-bold uppercase tracking-wide rounded-xl text-slate-600 hover:bg-slate-200" data-subtab="tarif">4. Master Tarif</button>
                        <button class="sk-sub-tab px-4 py-2.5 text-[11px] font-bold uppercase tracking-wide rounded-xl text-slate-600 hover:bg-slate-200" data-subtab="kontainer">5. Daftar Kontainer</button>
                    </div>
                    
                    <div class="p-6">
                        {{-- Vendor Sub-Panel --}}
                        <div class="sk-panel active" id="subpanel-vendor">
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                                <div class="lg:col-span-1 bg-slate-50 p-6 rounded-2xl border border-slate-200 h-fit">
                                    <div class="flex items-center gap-2 mb-5">
                                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                        <h3 class="font-bold text-slate-800 text-sm">Input Vendor / Owner Baru</h3>
                                    </div>
                                    <form id="form-add-vendor" class="space-y-4">
                                        <div><label class="block text-[11px] font-bold text-slate-700 mb-1.5 uppercase tracking-wide">Nama Vendor / Owner</label><input type="text" id="vendor-name" class="sk-input" placeholder="Contoh: PT. Temas Line" required></div>
                                        <button type="submit" class="sk-btn-primary w-full py-2.5 mt-2">Simpan Vendor</button>
                                    </form>
                                </div>
                                <div class="lg:col-span-2 space-y-4">
                                    <div class="relative max-w-sm">
                                        <svg class="w-4 h-4 absolute left-3 top-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                        <input type="text" id="search-vendor" class="sk-input pl-9" placeholder="Cari nama vendor...">
                                    </div>
                                    <div class="border border-slate-200 rounded-xl overflow-hidden shadow-sm"><table class="sk-table"><thead><tr><th>Nama Vendor / Owner</th><th class="text-right">Status / Aksi</th></tr></thead><tbody id="vendor-tbody"></tbody></table></div>
                                </div>
                            </div>
                        </div>

                        {{-- Tipe Sub-Panel --}}
                        <div class="sk-panel" id="subpanel-tipe">
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                                <div class="lg:col-span-1 bg-slate-50 p-6 rounded-2xl border border-slate-200 h-fit">
                                    <div class="flex items-center gap-2 mb-5">
                                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>
                                        <h3 class="font-bold text-slate-800 text-sm">Input Tipe Baru</h3>
                                    </div>
                                    <form id="form-add-tipe" class="space-y-4">
                                        <div><label class="block text-[11px] font-bold text-slate-700 mb-1.5 uppercase tracking-wide">Nama Tipe Kontainer</label><input type="text" id="tipe-name" class="sk-input" placeholder="Contoh: Dry, Reefer, Flat Rack" required></div>
                                        <button type="submit" class="sk-btn-primary w-full py-2.5 mt-2">Simpan Tipe</button>
                                    </form>
                                </div>
                                <div class="lg:col-span-2 space-y-4">
                                    <div class="relative max-w-sm"><svg class="w-4 h-4 absolute left-3 top-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg><input type="text" id="search-tipe" class="sk-input pl-9" placeholder="Cari tipe..."></div>
                                    <div class="border border-slate-200 rounded-xl overflow-hidden shadow-sm"><table class="sk-table"><thead><tr><th>Nama Tipe</th><th class="text-right">Status / Aksi</th></tr></thead><tbody id="tipe-tbody"></tbody></table></div>
                                </div>
                            </div>
                        </div>

                        {{-- Ukuran Sub-Panel --}}
                        <div class="sk-panel" id="subpanel-ukuran">
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                                <div class="lg:col-span-1 bg-slate-50 p-6 rounded-2xl border border-slate-200 h-fit">
                                    <div class="flex items-center gap-2 mb-5">
                                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" /></svg>
                                        <h3 class="font-bold text-slate-800 text-sm">Input Ukuran</h3>
                                    </div>
                                    <form id="form-add-ukuran" class="space-y-4">
                                        <div><label class="block text-[11px] font-bold text-slate-700 mb-1.5 uppercase tracking-wide">Ukuran (Cukup Angka)</label><input type="text" id="ukuran-desc" class="sk-input" placeholder="Ketik 20 atau 40" required><span class="text-[10px] text-slate-500 mt-1.5 block">Sistem otomatis menambahkan petik tunggal (') → 20' / 40'</span></div>
                                        <button type="submit" class="sk-btn-primary w-full py-2.5 mt-2">Simpan Ukuran</button>
                                    </form>
                                </div>
                                <div class="lg:col-span-2 space-y-4">
                                    <div class="relative max-w-sm"><svg class="w-4 h-4 absolute left-3 top-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg><input type="text" id="search-ukuran" class="sk-input pl-9" placeholder="Cari ukuran..."></div>
                                    <div class="border border-slate-200 rounded-xl overflow-hidden shadow-sm"><table class="sk-table"><thead><tr><th>Deskripsi Ukuran</th><th class="text-right">Status / Aksi</th></tr></thead><tbody id="ukuran-tbody"></tbody></table></div>
                                </div>
                            </div>
                        </div>

                        {{-- Tarif Sub-Panel --}}
                        <div class="sk-panel" id="subpanel-tarif">
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                                <div class="lg:col-span-1 bg-slate-50 p-6 rounded-2xl border border-slate-200 h-fit">
                                    <div class="flex items-center gap-2 mb-5">
                                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z" /></svg>
                                        <h3 class="font-bold text-slate-800 text-sm">Input Tarif Sewa In</h3>
                                    </div>
                                    <form id="form-add-tarif" class="space-y-4">
                                        <div><label class="block text-[11px] font-bold text-slate-700 mb-1.5 uppercase tracking-wide">Vendor</label><select id="tarif-vendor" class="sk-input" required><option value="">-- Pilih Vendor --</option></select></div>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div><label class="block text-[11px] font-bold text-slate-700 mb-1.5 uppercase tracking-wide">Tipe</label><select id="tarif-tipe" class="sk-input" required><option value="">-- Pilih --</option></select></div>
                                            <div><label class="block text-[11px] font-bold text-slate-700 mb-1.5 uppercase tracking-wide">Ukuran</label><select id="tarif-ukuran" class="sk-input" required><option value="">-- Pilih --</option></select></div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div><label class="block text-[11px] font-bold text-slate-700 mb-1.5 uppercase tracking-wide">Tarif Bulanan</label><input type="number" id="tarif-bulanan" class="sk-input font-mono" value="0"></div>
                                            <div><label class="block text-[11px] font-bold text-slate-700 mb-1.5 uppercase tracking-wide">Tarif Harian</label><input type="number" id="tarif-harian" class="sk-input font-mono" value="0"></div>
                                        </div>
                                        <div><label class="block text-[11px] font-bold text-slate-700 mb-1.5 uppercase tracking-wide">Mulai Berlaku</label><input type="text" id="tarif-start" class="sk-input font-mono" placeholder="YYYY-MM-DD"></div>
                                        <button type="submit" class="sk-btn-primary w-full py-2.5 mt-2">Simpan Tarif</button>
                                    </form>
                                </div>
                                <div class="lg:col-span-2 space-y-4">
                                    <div class="relative max-w-sm"><svg class="w-4 h-4 absolute left-3 top-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg><input type="text" id="search-tarif" class="sk-input pl-9" placeholder="Cari vendor tarif..."></div>
                                    <div class="border border-slate-200 rounded-xl overflow-x-auto shadow-sm sk-scroll"><table class="sk-table"><thead><tr><th>Vendor</th><th>Tipe</th><th>Ukuran</th><th class="text-right">Bulanan</th><th class="text-right">Harian</th><th>Berlaku</th><th class="text-right">Aksi</th></tr></thead><tbody id="tarif-tbody"></tbody></table></div>
                                </div>
                            </div>
                        </div>

                        {{-- Kontainer Sub-Panel --}}
                        <div class="sk-panel" id="subpanel-kontainer">
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                                <div class="lg:col-span-1 bg-slate-50 p-6 rounded-2xl border border-slate-200 h-fit">
                                    <div class="flex items-center gap-2 mb-5">
                                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                                        <h3 class="font-bold text-slate-800 text-sm">Daftar Kontainer Baru</h3>
                                    </div>
                                    <form id="form-add-kontainer" class="space-y-4">
                                        <div><label class="block text-[11px] font-bold text-slate-700 mb-1.5 uppercase tracking-wide">Nomor Kontainer (Unik)</label><input type="text" id="kontainer-no" class="sk-input font-mono" placeholder="Contoh: GLDU7252828" required></div>
                                        <div><label class="block text-[11px] font-bold text-slate-700 mb-1.5 uppercase tracking-wide">Vendor Terkait</label><select id="kontainer-vendor" class="sk-input" required><option value="">-- Pilih Vendor --</option></select></div>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div><label class="block text-[11px] font-bold text-slate-700 mb-1.5 uppercase tracking-wide">Tipe</label><select id="kontainer-tipe" class="sk-input" required><option value="">-- Pilih --</option></select></div>
                                            <div><label class="block text-[11px] font-bold text-slate-700 mb-1.5 uppercase tracking-wide">Ukuran</label><select id="kontainer-ukuran" class="sk-input" required><option value="">-- Pilih --</option></select></div>
                                        </div>
                                        <button type="submit" class="sk-btn-primary w-full py-2.5 mt-2">Simpan Kontainer</button>
                                    </form>
                                </div>
                                <div class="lg:col-span-2 space-y-4">
                                    <div class="relative max-w-sm"><svg class="w-4 h-4 absolute left-3 top-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg><input type="text" id="search-kontainer" class="sk-input pl-9" placeholder="Cari kontainer/vendor..."></div>
                                    <div class="border border-slate-200 rounded-xl overflow-x-auto shadow-sm sk-scroll"><table class="sk-table"><thead><tr><th>NO. KONTAINER</th><th>VENDOR</th><th>TIPE</th><th>UKURAN</th><th>STATUS</th><th class="text-right">AKSI</th></tr></thead><tbody id="kontainer-tbody"></tbody></table></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TAB 4: Bulk Import --}}
            <div class="sk-panel" id="panel-import">
                <div class="bg-white rounded-2xl border border-slate-100 shadow-xs p-6 space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                        <div>
                            <h3 class="font-bold text-slate-800 text-sm flex items-center gap-1.5">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                                <span>Impor Rekor Master &amp; Transaksi Cepat (Excel Copy-Paste)</span>
                            </h3>
                            <p class="text-[11px] text-slate-500 mt-0.5">
                                Pilih jenis tabel, salin baris tabel dari Excel/Spreadsheet Anda, tempel ke area teks, lalu klik Impor.
                            </p>
                        </div>
                        <select id="import-type" class="text-xs border border-slate-200 rounded-xl px-3 py-1.5 bg-slate-50/50 text-slate-800 font-semibold cursor-pointer">
                            <option value="vendor">1. Master Vendor / Owner</option>
                            <option value="tipe">2. Master Tipe Kontainer</option>
                            <option value="ukuran">3. Master Ukuran</option>
                            <option value="tarif">4. Master Tarif Sewa In</option>
                            <option value="kontainer">5. Master Kontainer</option>
                            <option value="sewa">6. Transaksi Sewa In &amp; Kembali</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="lg:col-span-2 space-y-4">
                            <div class="flex items-center justify-between text-xs font-semibold text-slate-500">
                                <span>Tempel Baris Data Di Bawah Ini:</span>
                                <button type="button" id="btn-load-template" class="text-[10px] text-emerald-600 hover:text-emerald-800 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    Muat Contoh Template Baris
                                </button>
                            </div>
                            
                            <textarea id="import-text" rows="10" class="w-full font-mono text-xs border border-slate-200 rounded-2xl p-4 bg-slate-50/40 focus:ring-2 focus:ring-emerald-500/20" placeholder="Salin/Ketik data di sini..."></textarea>
                            
                            <button type="button" id="btn-bulk-import" class="w-full inline-flex items-center justify-center bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs py-2.5 px-4 rounded-xl transition-all shadow-sm cursor-pointer">
                                Proses &amp; Simpan Otomatis Data Valid
                            </button>

                            <div id="import-error-highlights-box" class="space-y-3 pt-2 hidden">
                                <span class="text-xs font-bold text-red-600 flex items-center gap-1.5 bg-red-50 text-red-700 px-3 py-1.5 rounded-lg border border-red-100">
                                    <svg class="w-4 h-4 shrink-0 text-red-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    <span>Terdeteksi <span id="error-count">0</span> Baris Mengalami Kesalahan:</span>
                                </span>
                                <div id="import-error-list" class="max-h-[380px] overflow-y-auto space-y-3 divide-y divide-red-100 bg-red-50/20 p-4 rounded-2xl border border-red-100">
                                </div>
                            </div>
                        </div>

                        <div class="lg:col-span-1 space-y-4">
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-3">
                                <h4 class="text-xs font-bold text-slate-700 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                                    <span>Panduan Validasi &amp; Aturan</span>
                                </h4>
                                <div class="text-[10px] text-slate-600 space-y-2 leading-relaxed">
                                    <p><strong>✓ Simpan Otomatis:</strong> Baris yang sukses divalidasi akan langsung disimpan ke database (Live) dan dikeluarkan dari layar input.</p>
                                    <p><strong>⚠ Tetap di Layar:</strong> Baris yang salah akan tetap berada di dalam text-area untuk Anda benahi secara manual.</p>
                                    <p><strong>★ Prerrequisite Hub (Ketat):</strong> Kolom nama (vendor, tipe, ukuran) harus sudah terdaftar terlebih dahulu di master. Jika tidak ditemukan, sistem membatalkan impor untuk baris tersebut.</p>
                                    <p><strong>★ Update Pengembalian:</strong> Anda dapat mengimpor Tanggal Kembali saja (kosongkan kolom Tanggal Sewa) untuk memperbarui sewa kontainer aktif tanpa perlu input ulang tanggal sewa.</p>
                                </div>
                            </div>
                            
                            <div id="import-success-box" class="hidden p-3.5 bg-emerald-50 text-emerald-800 border border-emerald-100 rounded-xl text-xs font-semibold flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                <span>Selesai! <span id="success-count">0</span> rekor berhasil disimpan.</span>
                            </div>

                            <div class="p-4 rounded-xl border border-dashed border-indigo-200 bg-indigo-50/50 flex flex-col gap-3">
                                <h4 class="text-xs font-bold text-indigo-700 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                                    Migrasi Cadangan Lama
                                </h4>
                                <p class="text-[10px] text-indigo-600 leading-tight">Unggah file <code class="font-bold">.json</code> dari aplikasi lama untuk di-restore ke Database Live.</p>
                                <input type="file" id="backup-file" accept=".json" class="hidden">
                                <button type="button" id="btn-upload-backup" onclick="document.getElementById('backup-file').click()" class="w-full inline-flex items-center justify-center gap-1.5 bg-white border border-indigo-200 text-indigo-700 hover:bg-indigo-100 font-semibold text-[11px] py-2 px-4 rounded-lg transition-all cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0L8 8m4-4v12" /></svg>
                                    Unggah &amp; Pulihkan Cadangan (.json)
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

{{-- Modal Container --}}
<div id="sk-modal-container"></div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    const BASE = '/sewa-kontainer';

    function formatRupiah(num) {
        const isNeg = num < 0;
        const abs = Math.abs(num);
        return (isNeg ? '-' : '') + 'Rp ' + abs.toLocaleString('id-ID');
    }

    function formatIndoDate(dateStr) {
        if (!dateStr) return '-';
        const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];
        const parts = dateStr.split('-');
        if (parts.length !== 3) return dateStr;
        return parts[2] + ' ' + months[parseInt(parts[1])-1] + ' ' + parts[0].slice(-2);
    }

    function showNoti(type, msg) {
        const colors = {
            sukses: 'bg-emerald-50 border-emerald-200 text-emerald-800',
            error: 'bg-rose-50 border-rose-200 text-rose-800',
            info: 'bg-blue-50 border-blue-200 text-blue-800'
        };
        const icon = type === 'sukses' ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />' : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />';
        
        $('#sk-noti').html(`<div class="p-4 rounded-2xl flex items-center gap-3 border shadow-sm ${colors[type] || colors.info} mb-4"><svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">${icon}</svg><span class="font-semibold text-xs">${msg}</span></div>`).removeClass('hidden');
        setTimeout(() => $('#sk-noti').addClass('hidden'), 5000);
    }

    function ajaxPost(url, data) { return $.ajax({ url: BASE + url, type: 'POST', data: JSON.stringify(data), contentType: 'application/json', headers: {'X-CSRF-TOKEN': csrfToken} }); }
    function ajaxPut(url, data) { return $.ajax({ url: BASE + url, type: 'PUT', data: JSON.stringify(data), contentType: 'application/json', headers: {'X-CSRF-TOKEN': csrfToken} }); }
    function ajaxDelete(url) { return $.ajax({ url: BASE + url, type: 'DELETE', headers: {'X-CSRF-TOKEN': csrfToken} }); }
    function ajaxGet(url, params) { return $.get(BASE + url, params); }

    function statusBadge(status) {
        const map = {
            'Belum Ditagih': 'bg-slate-100 text-slate-600 border-slate-200',
            'Pranota': 'bg-amber-50 text-amber-700 border-amber-200',
            'Belum Bayar': 'bg-rose-50 text-rose-700 border-rose-200',
            'Lunas': 'bg-emerald-50 text-emerald-700 border-emerald-200'
        };
        return `<span class="sk-badge border ${map[status] || map['Belum Ditagih']}">${status}</span>`;
    }

    // ============================================================
    //  TAB NAVIGATION
    // ============================================================
    $('.sk-tab-btn').on('click', function() {
        const tab = $(this).data('tab');
        
        // Tab UI styling update
        $('.sk-tab-btn').removeClass('border-indigo-600 text-indigo-700 font-extrabold').addClass('border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300');
        $(this).removeClass('border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300').addClass('border-indigo-600 text-indigo-700 font-extrabold');
        
        $('.sk-panel').removeClass('active');
        $(`#panel-${tab}`).addClass('active');

        if (tab === 'billing') loadBillingData();
        if (tab === 'rental') loadSewaData();
        if (tab === 'master') loadCurrentMasterSubTab();
    });

    // Sub-tab navigation for Master
    $('.sk-sub-tab').on('click', function() {
        const subtab = $(this).data('subtab');
        
        $('.sk-sub-tab').removeClass('bg-indigo-600 text-white shadow-sm').addClass('text-slate-600 hover:bg-slate-200');
        $(this).removeClass('text-slate-600 hover:bg-slate-200').addClass('bg-indigo-600 text-white shadow-sm');
        
        $('[id^=subpanel-]').removeClass('active');
        $(`#subpanel-${subtab}`).addClass('active');
        loadCurrentMasterSubTab();
    });

    function loadCurrentMasterSubTab() {
        const active = $('.sk-sub-tab.bg-indigo-600').data('subtab');
        if (active === 'vendor') loadVendors();
        if (active === 'tipe') loadTipes();
        if (active === 'ukuran') loadUkurans();
        if (active === 'tarif') { loadTarifs(); populateTarifDropdowns(); }
        if (active === 'kontainer') { loadKontainers(); populateKontainerDropdowns(); }
    }

    // ============================================================
    //  KPI DASHBOARD
    // ============================================================
    function loadStats() {
        ajaxGet('/stats').done(function(data) {
            $('#kpi-total-kontainer').text(data.total_kontainers + ' Unit');
            $('#kpi-active-rentals').text(data.active_rentals + ' Siklus');
            $('#kpi-total-unpaid').text(formatRupiah(data.total_unpaid));
            $('#kpi-total-paid').text(formatRupiah(data.total_paid));
        });
    }

    // ============================================================
    //  MASTER: VENDORS
    // ============================================================
    function loadVendors() {
        const search = $('#search-vendor').val() || '';
        ajaxGet('/api/vendors', {search}).done(function(data) {
            let html = '';
            data.forEach(v => {
                const isActive = v.status_aktif !== false && v.status_aktif !== 0;
                html += `<tr><td class="font-bold text-xs">${v.name}</td><td class="text-right">
                    <span class="sk-badge border ${isActive ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-500 border-slate-200'}">${isActive ? 'Aktif' : 'Non-Aktif'}</span>
                    <button onclick="toggleVendor(${v.id})" class="ml-2 sk-btn-secondary ${isActive ? 'text-amber-600 border-amber-200' : 'text-indigo-600 border-indigo-200'}">${isActive ? 'Nonaktifkan' : 'Aktifkan'}</button>
                </td></tr>`;
            });
            if (!data.length) html = '<tr><td colspan="2" class="p-8 text-center text-slate-400">Tidak ada data vendor</td></tr>';
            $('#vendor-tbody').html(html);
        });
    }
    $('#form-add-vendor').on('submit', function(e) { e.preventDefault(); ajaxPost('/api/vendors', {name: $('#vendor-name').val()}).done(r => { showNoti('sukses', r.message); $('#vendor-name').val(''); loadVendors(); loadStats(); }).fail(r => showNoti('error', r.responseJSON?.message || 'Error')); });
    window.toggleVendor = id => ajaxPost(`/api/vendors/${id}/toggle`, {}).done(r => { showNoti('sukses', r.message); loadVendors(); });
    $('#search-vendor').on('input', () => loadVendors());

    // ============================================================
    //  MASTER: TIPE & UKURAN & KONTAINER & TARIF (Minified to save space, identical behavior)
    // ============================================================
    function loadTipes() {
        ajaxGet('/api/tipes').done(function(data) {
            const search = ($('#search-tipe').val() || '').toLowerCase();
            let filtered = data.filter(t => t.nama_tipe.toLowerCase().includes(search));
            let html = '';
            filtered.forEach(t => { const isActive = t.status_aktif !== false && t.status_aktif !== 0; html += `<tr><td class="font-bold text-xs">${t.nama_tipe}</td><td class="text-right"><span class="sk-badge border ${isActive ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-500 border-slate-200'}">${isActive ? 'Aktif' : 'Non-Aktif'}</span><button onclick="toggleTipe(${t.id})" class="ml-2 sk-btn-secondary ${isActive ? 'text-amber-600 border-amber-200' : 'text-indigo-600 border-indigo-200'}">${isActive ? 'Nonaktifkan' : 'Aktifkan'}</button></td></tr>`; });
            if (!filtered.length) html = '<tr><td colspan="2" class="p-8 text-center text-slate-400">Tidak ada data tipe</td></tr>';
            $('#tipe-tbody').html(html);
        });
    }
    $('#form-add-tipe').on('submit', function(e) { e.preventDefault(); ajaxPost('/api/tipes', {nama_tipe: $('#tipe-name').val()}).done(r => { showNoti('sukses', r.message); $('#tipe-name').val(''); loadTipes(); }).fail(r => showNoti('error', r.responseJSON?.message || 'Error')); });
    window.toggleTipe = id => ajaxPost(`/api/tipes/${id}/toggle`, {}).done(r => { showNoti('sukses', r.message); loadTipes(); });
    $('#search-tipe').on('input', () => loadTipes());

    function loadUkurans() {
        ajaxGet('/api/ukurans').done(function(data) {
            const search = ($('#search-ukuran').val() || '').toLowerCase();
            let filtered = data.filter(u => u.deskripsi_ukuran.toLowerCase().includes(search));
            let html = '';
            filtered.forEach(u => { const isActive = u.status_aktif !== false && u.status_aktif !== 0; html += `<tr><td class="font-mono font-bold text-sm text-indigo-700">${u.deskripsi_ukuran}</td><td class="text-right"><span class="sk-badge border ${isActive ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-500 border-slate-200'}">${isActive ? 'Aktif' : 'Non-Aktif'}</span><button onclick="toggleUkuran(${u.id})" class="ml-2 sk-btn-secondary ${isActive ? 'text-amber-600 border-amber-200' : 'text-indigo-600 border-indigo-200'}">${isActive ? 'Nonaktifkan' : 'Aktifkan'}</button></td></tr>`; });
            if (!filtered.length) html = '<tr><td colspan="2" class="p-8 text-center text-slate-400">Tidak ada data ukuran</td></tr>';
            $('#ukuran-tbody').html(html);
        });
    }
    $('#form-add-ukuran').on('submit', function(e) { e.preventDefault(); ajaxPost('/api/ukurans', {deskripsi_ukuran: $('#ukuran-desc').val()}).done(r => { showNoti('sukses', r.message); $('#ukuran-desc').val(''); loadUkurans(); }).fail(r => showNoti('error', r.responseJSON?.message || 'Error')); });
    window.toggleUkuran = id => ajaxPost(`/api/ukurans/${id}/toggle`, {}).done(r => { showNoti('sukses', r.message); loadUkurans(); });
    $('#search-ukuran').on('input', () => loadUkurans());

    function loadKontainers() {
        const search = $('#search-kontainer').val() || '';
        ajaxGet('/api/kontainers', {search}).done(function(data) {
            let html = '';
            data.forEach(k => { const isActive = k.status_aktif !== false && k.status_aktif !== 0; html += `<tr><td class="font-mono font-bold text-xs tracking-wide text-slate-900">${k.no_kontainer}</td><td class="text-xs font-semibold text-indigo-800">${k.vendor?.name || '-'}</td><td class="text-xs">${k.tipe?.nama_tipe || '-'}</td><td class="font-mono text-xs text-slate-600">${k.ukuran?.deskripsi_ukuran || '-'}</td><td><span class="sk-badge border ${isActive ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-500 border-slate-200'}">${isActive ? 'Aktif' : 'Non-Aktif'}</span></td><td class="text-right"><button onclick="toggleKontainer(${k.id})" class="sk-btn-secondary ${isActive ? 'text-amber-600 border-amber-200' : 'text-indigo-600 border-indigo-200'}">${isActive ? 'Nonaktifkan' : 'Aktifkan'}</button></td></tr>`; });
            if (!data.length) html = '<tr><td colspan="6" class="p-8 text-center text-slate-400">Tidak ada data kontainer</td></tr>';
            $('#kontainer-tbody').html(html);
        });
    }
    function populateKontainerDropdowns() {
        ajaxGet('/api/vendors').done(d => { let h = '<option value="">-- Pilih Vendor --</option>'; d.filter(v=>v.status_aktif!==false&&v.status_aktif!==0).forEach(v => h += `<option value="${v.id}">${v.name}</option>`); $('#kontainer-vendor').html(h); });
        ajaxGet('/api/tipes').done(d => { let h = '<option value="">-- Pilih --</option>'; d.filter(t=>t.status_aktif!==false&&t.status_aktif!==0).forEach(t => h += `<option value="${t.id}">${t.nama_tipe}</option>`); $('#kontainer-tipe').html(h); });
        ajaxGet('/api/ukurans').done(d => { let h = '<option value="">-- Pilih --</option>'; d.filter(u=>u.status_aktif!==false&&u.status_aktif!==0).forEach(u => h += `<option value="${u.id}">${u.deskripsi_ukuran}</option>`); $('#kontainer-ukuran').html(h); });
    }
    $('#form-add-kontainer').on('submit', function(e) { e.preventDefault(); ajaxPost('/api/kontainers', {no_kontainer: $('#kontainer-no').val(), vendor_id: $('#kontainer-vendor').val(), tipe_id: $('#kontainer-tipe').val(), ukuran_id: $('#kontainer-ukuran').val()}).done(r => { showNoti('sukses', r.message); $('#kontainer-no').val(''); loadKontainers(); loadStats(); }).fail(r => showNoti('error', r.responseJSON?.message || 'Error')); });
    window.toggleKontainer = id => ajaxPost(`/api/kontainers/${id}/toggle`, {}).done(r => { showNoti('sukses', r.message); loadKontainers(); });
    $('#search-kontainer').on('input', () => loadKontainers());

    function loadTarifs() {
        const search = $('#search-tarif').val() || '';
        ajaxGet('/api/tarifs', {search}).done(function(data) {
            let html = '';
            data.forEach(t => { const isActive = t.status_aktif !== false && t.status_aktif !== 0; html += `<tr><td class="text-xs font-bold text-indigo-800">${t.vendor?.name || '-'}</td><td class="text-xs">${t.tipe?.nama_tipe || '-'}</td><td class="font-mono text-xs">${t.ukuran?.deskripsi_ukuran || '-'}</td><td class="text-right font-mono text-xs">${formatRupiah(t.tarif_bulanan)}</td><td class="text-right font-mono text-xs">${formatRupiah(t.tarif_harian)}</td><td class="font-mono text-[10px] text-slate-500">${formatIndoDate(t.tanggal_mulai_berlaku)} ${t.tanggal_akhir_berlaku ? '→ ' + formatIndoDate(t.tanggal_akhir_berlaku) : '→ Sekarang'}</td><td class="text-right"><button onclick="toggleTarif(${t.id})" class="sk-btn-secondary ${isActive ? 'text-amber-600 border-amber-200' : 'text-indigo-600 border-indigo-200'}">${isActive ? 'Nonaktifkan' : 'Aktifkan'}</button></td></tr>`; });
            if (!data.length) html = '<tr><td colspan="7" class="p-8 text-center text-slate-400">Tidak ada data tarif</td></tr>';
            $('#tarif-tbody').html(html);
        });
    }
    function populateTarifDropdowns() {
        ajaxGet('/api/vendors').done(d => { let h = '<option value="">-- Pilih Vendor --</option>'; d.filter(v=>v.status_aktif!==false&&v.status_aktif!==0).forEach(v => h += `<option value="${v.id}">${v.name}</option>`); $('#tarif-vendor').html(h); });
        ajaxGet('/api/tipes').done(d => { let h = '<option value="">-- Pilih --</option>'; d.filter(t=>t.status_aktif!==false&&t.status_aktif!==0).forEach(t => h += `<option value="${t.id}">${t.nama_tipe}</option>`); $('#tarif-tipe').html(h); });
        ajaxGet('/api/ukurans').done(d => { let h = '<option value="">-- Pilih --</option>'; d.filter(u=>u.status_aktif!==false&&u.status_aktif!==0).forEach(u => h += `<option value="${u.id}">${u.deskripsi_ukuran}</option>`); $('#tarif-ukuran').html(h); });
    }
    $('#form-add-tarif').on('submit', function(e) { e.preventDefault(); ajaxPost('/api/tarifs', {vendor_id: $('#tarif-vendor').val(), tipe_id: $('#tarif-tipe').val(), ukuran_id: $('#tarif-ukuran').val(), tarif_bulanan: $('#tarif-bulanan').val(), tarif_harian: $('#tarif-harian').val(), tanggal_mulai_berlaku: $('#tarif-start').val()}).done(r => { showNoti('sukses', r.message); loadTarifs(); }).fail(r => showNoti('error', r.responseJSON?.message || 'Error')); });
    window.toggleTarif = id => ajaxPost(`/api/tarifs/${id}/toggle`, {}).done(r => { showNoti('sukses', r.message); loadTarifs(); });
    $('#search-tarif').on('input', () => loadTarifs());

    // ============================================================
    //  SEWA TRANSACTIONS
    // ============================================================
    function loadSewaData() {
        ajaxGet('/api/kontainers').done(function(kontainers) {
            ajaxGet('/api/sewas').done(function(sewas) {
                const activeNos = sewas.filter(s => s.status_sewa === 'Aktif').map(s => s.no_kontainer);
                let h = '<option value="">-- Cari No Kontainer --</option>';
                kontainers.filter(k => k.status_aktif !== false && k.status_aktif !== 0).forEach(k => {
                    const isRented = activeNos.includes(k.no_kontainer);
                    h += `<option value="${k.no_kontainer}" data-vendor-id="${k.vendor_id}" data-vendor="${k.vendor?.name||'-'}" data-tipe="${k.tipe?.nama_tipe||'-'}" data-ukuran="${k.ukuran?.deskripsi_ukuran||'-'}" ${isRented?'disabled':''}>${k.no_kontainer} ${isRented?'(SEDANG DISEWA)':'[Vendor: '+(k.vendor?.name||'-')+']'}</option>`;
                });
                $('#sewa-kontainer-select').html(h);
            });
        });
        loadSewaList();
    }

    function loadSewaList() {
        const search = $('#sewa-search').val() || '';
        ajaxGet('/api/sewas', {search}).done(function(sewas) {
            if (!sewas.length) { $('#sewa-list').html('<div class="bg-white rounded-2xl border border-slate-200 p-12 text-center text-slate-400 text-sm">Tidak ada data transaksi sewa</div>'); return; }
            let html = '';
            sewas.forEach(s => {
                const isAktif = s.status_sewa === 'Aktif';
                const tagihans = s.tagihans || [];
                let billingBadgeHtml = statusBadge(s.billing_status || 'Belum Ditagih');

                html += `<div class="bg-white rounded-2xl shadow-sm border p-5 transition-all ${isAktif ? 'border-amber-200/60 shadow-amber-100/50' : 'border-slate-200'}">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-dashed border-slate-200 pb-4 mb-4">
                        <div class="flex items-center gap-3 flex-wrap">
                            <span class="font-mono font-extrabold text-base tracking-wider text-slate-800">${s.no_kontainer}</span>
                            <span class="text-slate-300">|</span>
                            <span class="text-xs font-bold text-slate-500">${s.kontainer?.tipe?.nama_tipe||'-'} <span class="text-indigo-600 font-mono">${s.kontainer?.ukuran?.deskripsi_ukuran||'-'}</span></span>
                            <span class="text-slate-300">|</span>
                            ${billingBadgeHtml}
                        </div>
                        <div class="flex items-center gap-2 flex-wrap">
                            ${isAktif ? `<span class="sk-badge bg-amber-100 text-amber-800 border border-amber-200 gap-1"><span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Sedang Disewa</span>
                            <button onclick="showReturnModal(${s.id}, '${s.no_kontainer}')" class="inline-flex items-center px-3 py-1.5 text-[11px] font-bold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white cursor-pointer shadow-sm border-b-2 border-emerald-800 active:translate-y-px active:border-b-0">Kembalikan Unit</button>` :
                            `<span class="sk-badge bg-slate-100 text-slate-600 border border-slate-200"><svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Selesai Dikembalikan</span>`}
                            <button onclick="deleteSewa(${s.id})" class="inline-flex items-center p-1.5 text-xs rounded-lg bg-white hover:bg-rose-50 text-rose-600 border border-slate-200 hover:border-rose-200 cursor-pointer transition-colors" title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-6 text-xs">
                        <div><p class="text-[10px] uppercase font-bold text-slate-400 mb-1 tracking-wide">Vendor / Owner</p><p class="font-bold text-slate-700">${s.vendor?.name||'-'}</p></div>
                        <div><p class="text-[10px] uppercase font-bold text-slate-400 mb-1 tracking-wide">Rentang Sewa</p><p class="font-mono font-medium text-slate-700">${formatIndoDate(s.tanggal_sewa)}<br/><span class="text-slate-400 text-[10px]">s/d</span> ${s.tanggal_kembali ? formatIndoDate(s.tanggal_kembali) : 'Saat Ini'}</p></div>
                        <div><p class="text-[10px] uppercase font-bold text-slate-400 mb-1 tracking-wide">Jenis Tarif</p><p class="font-bold text-slate-700">${s.jenis_tarif}<br/><span class="font-mono text-slate-500 font-normal">${formatRupiah(s.jenis_tarif==='Bulanan'?s.tarif_bulanan:s.tarif_harian)}</span></p></div>
                        <div><p class="text-[10px] uppercase font-bold text-slate-400 mb-1 tracking-wide text-right">Akumulasi Biaya</p><p class="font-mono font-extrabold text-slate-800 text-right text-sm">${formatRupiah(s.total_estimasi||0)}</p></div>
                        <div><p class="text-[10px] uppercase font-bold text-rose-500 mb-1 tracking-wide text-right">Outstanding</p><p class="font-mono font-extrabold text-rose-700 text-right text-sm">${formatRupiah(s.total_outstanding||0)}</p></div>
                    </div>
                    ${s.catatan ? `<div class="mt-4 text-[11px] text-slate-500 bg-slate-50 border border-slate-100 p-2.5 rounded-xl italic flex gap-2"><svg class="w-4 h-4 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> ${s.catatan}</div>` : ''}
                    
                    ${tagihans.length ? `<div class="mt-5 border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                        <div class="bg-slate-100 p-2.5 px-4 text-[10px] font-bold text-slate-600 font-mono tracking-widest border-b border-slate-200 flex justify-between items-center">
                            <span>PERINCIAN PERIODE BILLING</span>
                            <span class="text-slate-400">${tagihans.length} Siklus</span>
                        </div>
                        <div class="overflow-x-auto sk-scroll">
                            <table class="sk-period-table"><thead><tr><th>Periode</th><th>Masa Rentang</th><th>Status Bayar</th><th class="text-center">Hari</th><th class="text-right">Tipe Tarif</th><th class="text-right">Total Tagihan</th></tr></thead><tbody>
                            ${tagihans.map(t => `<tr>
                                <td class="font-extrabold text-[10px] text-slate-700">BULAN ${t.bulan_ke}</td>
                                <td class="text-slate-500 font-medium text-[10px]">${formatIndoDate(t.tanggal_awal)} <span class="text-slate-300">→</span> ${formatIndoDate(t.tanggal_akhir)}</td>
                                <td>${statusBadge(t.status_bayar)}</td>
                                <td class="text-center text-slate-600 font-mono font-bold">${t.jumlah_hari}</td>
                                <td class="text-right font-bold text-slate-500 text-[9px] uppercase tracking-wider">${t.tipe_tarif}</td>
                                <td class="text-right font-bold text-slate-800 font-mono text-xs">${t.jumlah_tagihan_override !== null ? `<span class="text-[9px] text-slate-400 line-through mr-1">${formatRupiah(t.jumlah_tagihan_estimasi)}</span> ${formatRupiah(t.jumlah_tagihan_override)}` : formatRupiah(t.jumlah_tagihan_estimasi)}</td>
                            </tr>`).join('')}
                            </tbody></table>
                        </div>
                    </div>` : ''}
                </div>`;
            });
            $('#sewa-list').html(html);
        });
    }

    $('#sewa-kontainer-select').on('change', function() {
        const opt = $(this).find(':selected');
        if (opt.val()) {
            $('#sewa-kontainer-info').removeClass('hidden').html(`<p><strong>Tipe / Ukuran:</strong> ${opt.data('tipe')} <span class="font-mono bg-white px-1 py-0.5 rounded border border-emerald-200">${opt.data('ukuran')}</span></p><p><strong>Vendor Terkait:</strong> ${opt.data('vendor')}</p>`);
        } else { $('#sewa-kontainer-info').addClass('hidden'); }
    });

    $('#form-create-sewa').on('submit', function(e) {
        e.preventDefault();
        const data = {
            no_kontainer: $('#sewa-kontainer-select').val(),
            tanggal_sewa: $('#sewa-tanggal').val(),
            jenis_tarif: $('input[name=sewa_jenis_tarif]:checked').val(),
            tarif_bulanan: parseInt($('#sewa-tarif-bulanan').val()) || 0,
            tarif_harian: parseInt($('#sewa-tarif-harian').val()) || 0,
            catatan: $('#sewa-catatan').val(),
            non_ppn: !$('#sewa-ppn').is(':checked')
        };
        ajaxPost('/api/sewas', data).done(r => { showNoti('sukses', r.message); $('#sewa-tanggal').val(''); $('#sewa-catatan').val(''); loadSewaData(); loadStats(); }).fail(r => showNoti('error', r.responseJSON?.message || 'Error'));
    });

    window.showReturnModal = function(sewaId, noKontainer) {
        const html = `<div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4 transition-all" id="return-modal">
            <div class="bg-white rounded-2xl shadow-xl border border-slate-100 max-w-md w-full overflow-hidden animate-fadeIn">
                <div class="p-5 border-b border-slate-100 bg-slate-50/80 flex items-center gap-3">
                    <div class="p-2 bg-emerald-100 text-emerald-700 rounded-lg"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                    <h3 class="font-bold text-slate-800">Kembalikan Kontainer <span class="font-mono text-indigo-600">${noKontainer}</span></h3>
                </div>
                <div class="p-6">
                    <form id="form-return-sewa">
                        <div class="mb-5">
                            <label class="block text-[11px] font-bold text-slate-700 mb-1.5 uppercase tracking-wide">Tanggal Kembali (Off-Hire)</label>
                            <input type="text" id="return-tanggal" class="sk-input font-mono bg-slate-50" placeholder="YYYY-MM-DD" required>
                            <p class="text-[10px] text-slate-500 mt-2">Tagihan berjalan akan dihitung ulang secara prorata sesuai tanggal pengembalian ini.</p>
                        </div>
                        <div class="flex gap-3">
                            <button type="submit" class="sk-btn-primary flex-1 py-2.5">Konfirmasi Pengembalian</button>
                            <button type="button" onclick="$('#return-modal').remove()" class="sk-btn-secondary flex-1 py-2.5">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>`;
        $('#sk-modal-container').html(html);
        $('#form-return-sewa').on('submit', function(e) { e.preventDefault(); ajaxPut(`/api/sewas/${sewaId}/return`, {tanggal_kembali: $('#return-tanggal').val()}).done(r => { showNoti('sukses', r.message); $('#return-modal').remove(); loadSewaData(); loadStats(); }).fail(r => showNoti('error', r.responseJSON?.message || 'Error')); });
    };

    window.deleteSewa = function(sewaId) { if (!confirm('Yakin ingin menghapus transaksi sewa ini secara permanen? Data tagihan terkait juga akan terhapus.')) return; ajaxDelete(`/api/sewas/${sewaId}`).done(r => { showNoti('sukses', r.message); loadSewaData(); loadStats(); }).fail(r => showNoti('error', r.responseJSON?.message || 'Error')); };
    $('#sewa-search').on('input', () => loadSewaList());

    // ============================================================
    //  BILLING DASHBOARD
    // ============================================================
    function loadBillingData() {
        const params = {};
        if ($('#billing-vendor-filter').val()) params.vendor_id = $('#billing-vendor-filter').val();
        if ($('#billing-status-filter').val()) params.status_bayar = $('#billing-status-filter').val();
        if ($('#billing-search').val()) params.search = $('#billing-search').val();

        ajaxGet('/api/tagihans', params).done(function(data) {
            let html = '';
            data.forEach(t => {
                const sewa = t.sewa || {};
                html += `<tr>
                    <td class="font-mono font-bold text-xs tracking-wide text-slate-800">${sewa.no_kontainer || '-'}</td>
                    <td class="font-bold text-xs text-indigo-800">${sewa.vendor?.name || '-'}</td>
                    <td class="font-mono text-[10px] font-bold text-slate-500">BLN ${t.bulan_ke}</td>
                    <td class="text-[11px] font-medium text-slate-600">${formatIndoDate(t.tanggal_awal)} <span class="text-slate-300">→</span><br/>${formatIndoDate(t.tanggal_akhir)}</td>
                    <td class="text-center font-mono font-bold text-slate-700">${t.jumlah_hari}</td>
                    <td class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">${t.tipe_tarif}</td>
                    <td class="text-right font-mono font-bold text-slate-500 text-xs">${formatRupiah(t.jumlah_tagihan_estimasi)}</td>
                    <td>${statusBadge(t.status_bayar)}</td>
                    <td class="text-xs font-mono font-medium">${t.nomor_invoice || '-'}</td>
                    <td class="text-right font-mono font-extrabold text-slate-800 text-xs">${t.jumlah_tagihan_override !== null ? formatRupiah(t.jumlah_tagihan_override) : '-'}</td>
                    <td>
                        <select onchange="updateTagihanStatus(${t.id}, this.value)" class="text-[11px] font-semibold border border-slate-200 bg-white rounded-lg px-2 py-1.5 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none cursor-pointer">
                            <option value="Belum Ditagih" ${t.status_bayar==='Belum Ditagih'?'selected':''}>Belum Ditagih</option>
                            <option value="Pranota" ${t.status_bayar==='Pranota'?'selected':''}>Pranota</option>
                            <option value="Belum Bayar" ${t.status_bayar==='Belum Bayar'?'selected':''}>Belum Bayar</option>
                            <option value="Lunas" ${t.status_bayar==='Lunas'?'selected':''}>Lunas</option>
                        </select>
                    </td>
                </tr>`;
            });
            if (!data.length) html = '<tr><td colspan="11" class="p-12 text-center text-slate-400 text-sm border-b-0">Tidak ada data tagihan yang sesuai filter</td></tr>';
            $('#billing-tbody').html(html);
        });

        ajaxGet('/api/vendors').done(function(vendors) {
            let h = '<option value="">Semua Vendor</option>';
            vendors.forEach(v => h += `<option value="${v.id}">${v.name}</option>`);
            const current = $('#billing-vendor-filter').val();
            $('#billing-vendor-filter').html(h).val(current);
        });
    }

    window.updateTagihanStatus = function(id, status) { ajaxPut(`/api/tagihans/${id}`, {status_bayar: status}).done(r => { showNoti('sukses', r.message); loadStats(); }).fail(r => showNoti('error', r.responseJSON?.message || 'Error')); };
    $('#billing-vendor-filter, #billing-status-filter').on('change', () => loadBillingData());
    $('#billing-search').on('input', () => loadBillingData());

    // ============================================================
    //  BULK IMPORT
    // ============================================================
    const getTemplatePlaceholder = (importType) => {
        switch (importType) {
            case 'vendor':
                return `# Format: [Nama Vendor / Owner] (Satu per baris)\nPT. Temas Line\nCV. Jayasampurna\nPelayaran Meratus Cargo`;
            case 'tipe':
                return `# Format: [Nama Tipe] (Satu per baris)\nDry\nReefer\nFlat Rack\nOpen Top`;
            case 'ukuran':
                return `# Format: [Ukuran Kontainer] (Cukup angka, sistem otomatis tambah petik)\n20\n40\n45`;
            case 'kontainer':
                return `# Format: NO_KONTAINER ; NAMA_VENDOR_PEMILIK ; NAMA_TIPE ; UKURAN\n# Pemisah menggunakan Titik-Koma ( ; )\nAMFU3153692 ; PT. Temas Line ; Dry ; 20\nGLDU7252828 ; CV. Jayasampurna ; Reefer ; 40`;
            case 'tarif':
                return `# Format: NAMA_VENDOR ; NAMA_TIPE ; UKURAN ; TARIF_BULANAN_VENDOR ; TARIF_HARIAN_VENDOR ; TGL_MULAI_BERLAKU(dd/mm/yyyy)\nPT. Temas Line ; Dry ; 20 ; 3000000 ; 150000 ; 01/01/2022\nCV. Jayasampurna ; Reefer ; 40 ; 6000000 ; 300000 ; 22/04/2024`;
            case 'sewa':
                return `# Format: NO_KONTAINER ; NAMA_VENDOR_PEMILIK ; TGL_SEWA(dd/mm/yyyy atau KOSONG untuk update kembali) ; TGL_KEMBALI(dd/mm/yyyy) ; BULANAN/HARIAN ; PPN (optional)\nAMFU3153692 ; PT. Temas Line ; 30/09/2022 ; 10/05/2023 ; Bulanan\nGLDU7252828 ; CV. Jayasampurna ; ; 14/06/2026 ; Bulanan ; tidak`;
            case 'pembayaran':
                return `# Format Baru: KONTAINER ; PERIODE ; AWAL ; AKHIR ; TAGIHAN_VENDOR ; No. Invoice Vendor ; Tgl. Invoice\n# Contoh:\nBHSU2002332 ; 1 ; 30 Apr 24 ; 29 Mei 24 ; 675.676 ; ZONA260131368 ; 10 Jan 26`;
            case 'pranota':
                return `# Format Impor Pranota (No. Tagihan ; Tgl. Tagihan ; No. Pranota ; Tgl. Pranota ; Nilai Real (sebelum ppn & pph))\n# Contoh:\nZONA260131368 ; 10 Jan 26 ; PRANOTA-001 ; 12 Jan 26 ; 680.000`;
            case 'pelunasan':
                return `# Format Impor Pembayaran (No. Pranota ; Tgl. Pranota ; No. Pembayaran ; Tgl. Pembayaran ; Nilai Real (sebelum ppn & pph))\n# Contoh:\nPRANOTA-001 ; 12 Jan 26 ; BYR-TEMAS-01 ; 15 Jan 26 ; 680.000`;
        }
        return '';
    };

    $('#btn-load-template').on('click', function() {
        $('#import-text').val(getTemplatePlaceholder($('#import-type').val()));
    });

    $('#import-type').on('change', function() {
        $('#import-text').val('');
        $('#import-error-highlights-box').addClass('hidden');
        $('#import-success-box').addClass('hidden');
    });

    $('#btn-bulk-import').on('click', function() {
        const type = $('#import-type').val();
        const text = $('#import-text').val();
        if (!text.trim()) { showNoti('error', 'Data tidak boleh kosong'); return; }

        $(this).html('<svg class="animate-spin w-4 h-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memproses...');
        
        $('#import-error-highlights-box').addClass('hidden');
        $('#import-success-box').addClass('hidden');

        ajaxPost('/api/bulk-import', {type, text}).done(function(r) {
            $('#btn-bulk-import').html('Proses &amp; Simpan Otomatis Data Valid');
            
            if (r.success_count > 0) {
                $('#success-count').text(r.success_count);
                $('#import-success-box').removeClass('hidden');
            }

            if (r.errors && r.errors.length > 0) {
                $('#error-count').text(r.errors.length);
                $('#import-error-highlights-box').removeClass('hidden');
                let errHtml = '';
                r.errors.forEach(e => {
                    errHtml += `<div class="pt-3 first:pt-0 flex flex-col sm:flex-row sm:items-start justify-between gap-3 text-xs">
                        <div class="space-y-1.5 flex-1 select-text">
                            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                                <span class="font-extrabold text-red-800 bg-red-100 px-2 py-0.5 rounded text-[10px] font-mono shadow-sm">ROW ${e.line}</span>
                                <span class="text-slate-300 hidden sm:inline">|</span>
                                <span class="text-[11px] font-mono text-slate-600 bg-slate-100 px-2 py-0.5 rounded border border-slate-200 break-all select-all">"${e.raw}"</span>
                            </div>
                            <p class="font-semibold text-rose-700 font-sans leading-relaxed text-[11px] sm:text-[11.5px]">Penyebab: <span class="text-slate-800 font-normal">${e.error}</span></p>
                        </div>
                    </div>`;
                });
                $('#import-error-list').html(errHtml);

                // Keep only failed lines in textarea
                const failedLines = r.errors.map(e => e.raw).join('\\n');
                $('#import-text').val(failedLines);
            } else {
                $('#import-text').val(''); // Clear if all success
            }
            loadStats();
        }).fail(r => {
            $('#btn-bulk-import').html('Proses &amp; Simpan Otomatis Data Valid');
            showNoti('error', r.responseJSON?.message || 'Gagal mengimpor data');
        });
    });

    $('#backup-file').on('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('backup_file', file);

        const $btn = $('#btn-upload-backup');
        const originalText = $btn.html();
        $btn.html('<svg class="animate-spin w-3.5 h-3.5 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memulihkan Database (Membutuhkan Waktu)...').prop('disabled', true);

        $.ajax({
            url: '/sewa-kontainer/api/restore-backup',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(r) {
                showNoti('sukses', r.message);
                $btn.html(originalText).prop('disabled', false);
                $('#backup-file').val('');
                loadStats();
                loadBillingData();
            },
            error: function(xhr) {
                showNoti('error', xhr.responseJSON?.message || 'Terjadi kesalahan saat memulihkan backup.');
                $btn.html(originalText).prop('disabled', false);
                $('#backup-file').val('');
            }
        });
    });

    // ============================================================
    //  INITIAL LOAD
    // ============================================================
    loadStats();
    loadBillingData();
});
</script>
@endpush
