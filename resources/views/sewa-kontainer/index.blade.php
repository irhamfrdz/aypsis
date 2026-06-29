@extends('layouts.app')

@section('title', 'Portal Sewa Kontainer')
@section('page_title', 'Portal Sewa Kontainer')

@push('styles')
<style>
    .sk-tab-btn { transition: all 0.2s; cursor: pointer; }
    .sk-tab-btn.active { border-bottom: 2px solid #4f46e5; color: #4338ca; font-weight: 700; }
    .sk-tab-btn:not(.active) { border-bottom: 2px solid transparent; color: #64748b; }
    .sk-tab-btn:not(.active):hover { color: #1e293b; border-color: #cbd5e1; }
    .sk-sub-tab { transition: all 0.15s; cursor: pointer; }
    .sk-sub-tab.active { background: #4f46e5; color: #fff; box-shadow: 0 1px 2px rgba(0,0,0,0.1); }
    .sk-sub-tab:not(.active) { color: #475569; }
    .sk-sub-tab:not(.active):hover { background: #f1f5f9; color: #1e293b; }
    .sk-panel { display: none; }
    .sk-panel.active { display: block; }
    .sk-card { background: #fff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 1px 2px rgba(0,0,0,0.04); }
    .sk-noti { animation: slideIn 0.3s ease-out; }
    @keyframes slideIn { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:translateY(0); } }
    .sk-badge { display: inline-flex; align-items: center; padding: 2px 8px; border-radius: 9999px; font-size: 10px; font-weight: 600; }
    .sk-input { width: 100%; font-size: 0.875rem; border: 1px solid #e2e8f0; border-radius: 12px; padding: 8px 14px; background: #fff; }
    .sk-input:focus { outline: none; ring: 2px; border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.1); }
    .sk-btn-primary { display: inline-flex; align-items: center; justify-content: center; padding: 8px 16px; font-size: 0.875rem; font-weight: 500; color: #fff; background: #4f46e5; border-radius: 12px; transition: all 0.15s; cursor: pointer; border: none; }
    .sk-btn-primary:hover { background: #4338ca; }
    .sk-btn-secondary { display: inline-flex; align-items: center; justify-content: center; padding: 4px 10px; font-size: 11px; font-weight: 500; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; transition: all 0.15s; cursor: pointer; }
    .sk-table { width: 100%; text-align: left; border-collapse: collapse; font-size: 0.75rem; }
    .sk-table thead { background: #f8fafc; }
    .sk-table th { padding: 10px 12px; border-bottom: 1px solid #e2e8f0; font-weight: 600; color: #475569; }
    .sk-table td { padding: 10px 12px; border-bottom: 1px solid #f1f5f9; color: #334155; }
    .sk-table tbody tr:hover { background: #f8fafc; }
    .sk-sewa-card { background: #fff; border-radius: 16px; padding: 20px; border: 1px solid #e2e8f0; margin-bottom: 12px; }
    .sk-period-table { width: 100%; font-size: 11px; }
    .sk-period-table th { padding: 6px 8px; background: #f1f5f9; font-weight: 600; color: #475569; border-bottom: 1px solid #e2e8f0; }
    .sk-period-table td { padding: 6px 8px; border-bottom: 1px solid #f1f5f9; font-family: monospace; }
    .sk-modal-overlay { position: fixed; top:0; left:0; right:0; bottom:0; background: rgba(0,0,0,0.4); z-index: 9999; display: flex; align-items: center; justify-content: center; }
    .sk-modal { background: #fff; border-radius: 16px; padding: 24px; max-width: 500px; width: 90%; max-height: 80vh; overflow-y: auto; }
</style>
@endpush

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Notification Banner --}}
        <div id="sk-noti" class="hidden sk-noti"></div>

        {{-- KPI Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4" id="sk-kpi-grid">
            <div class="sk-card p-5 flex items-center justify-between">
                <div>
                    <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Total Kontainer Terdaftar</p>
                    <h3 class="text-xl font-bold text-gray-800 mt-1 font-mono" id="kpi-total-kontainer">0 Unit</h3>
                    <p class="text-[10px] text-gray-500 mt-0.5">Semua tipe & ukuran</p>
                </div>
                <div class="p-3 rounded-xl bg-indigo-50 text-indigo-700 border border-indigo-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
            </div>
            <div class="sk-card p-5 flex items-center justify-between">
                <div>
                    <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Penyewaan Kontainer Aktif</p>
                    <h3 class="text-xl font-bold text-gray-800 mt-1 font-mono" id="kpi-active-rentals">0 Siklus</h3>
                    <p class="text-[10px] text-gray-500 mt-0.5">Paralel sewa diperbolehkan</p>
                </div>
                <div class="p-3 rounded-xl bg-indigo-50 text-indigo-700 border border-indigo-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
            </div>
            <div class="sk-card p-5 flex items-center justify-between">
                <div>
                    <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Estimasi Biaya Belum Dibayar</p>
                    <h3 class="text-xl font-bold text-red-700 mt-1 font-mono" id="kpi-total-unpaid">Rp 0</h3>
                    <p class="text-[10px] text-red-500 mt-0.5">Siklus outstanding bulanan</p>
                </div>
                <div class="bg-red-50 text-red-700 p-3 rounded-xl border border-red-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="sk-card p-5 flex items-center justify-between">
                <div>
                    <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Biaya Sewa Terbayar (Lunas)</p>
                    <h3 class="text-xl font-bold mt-1 font-mono text-indigo-700" id="kpi-total-paid">Rp 0</h3>
                    <p class="text-[10px] text-gray-500 mt-0.5">Tanpa sistem cicilan/parsial</p>
                </div>
                <div class="p-3 rounded-xl bg-indigo-50 text-indigo-700 border border-indigo-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
            </div>
        </div>

        {{-- Tab Navigation --}}
        <div class="flex border-b border-gray-200 gap-1">
            <button class="sk-tab-btn active py-3 px-5 text-xs font-bold inline-flex items-center gap-2" data-tab="billing">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                1. Dasbor Pengeluaran & Pembayaran
            </button>
            <button class="sk-tab-btn py-3 px-5 text-xs font-bold inline-flex items-center gap-2" data-tab="rental">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                2. Siklus Sewa In & Kontainer
            </button>
            <button class="sk-tab-btn py-3 px-5 text-xs font-bold inline-flex items-center gap-2" data-tab="master">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                3. Kelola Database Vendor/Owner
            </button>
            <button class="sk-tab-btn py-3 px-5 text-xs font-bold inline-flex items-center gap-2" data-tab="import">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                4. Impor Excel Cepat
            </button>
        </div>

        {{-- TAB 1: Billing Dashboard --}}
        <div class="sk-panel active" id="panel-billing">
            <div class="sk-card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-gray-800 text-sm">Daftar Tagihan per Periode</h3>
                    <div class="flex items-center gap-2">
                        <select id="billing-vendor-filter" class="sk-input" style="max-width:200px;font-size:12px;">
                            <option value="">Semua Vendor</option>
                        </select>
                        <select id="billing-status-filter" class="sk-input" style="max-width:160px;font-size:12px;">
                            <option value="">Semua Status</option>
                            <option value="Belum Ditagih">Belum Ditagih</option>
                            <option value="Pranota">Pranota</option>
                            <option value="Belum Bayar">Belum Bayar</option>
                            <option value="Lunas">Lunas</option>
                        </select>
                        <input type="text" id="billing-search" class="sk-input" style="max-width:200px;font-size:12px;" placeholder="Cari kontainer/invoice...">
                    </div>
                </div>
                <div class="overflow-x-auto border border-gray-100 rounded-xl">
                    <table class="sk-table" id="billing-table">
                        <thead>
                            <tr>
                                <th>Kontainer</th>
                                <th>Vendor</th>
                                <th>Periode</th>
                                <th>Masa</th>
                                <th class="text-center">Hari</th>
                                <th>Tipe</th>
                                <th class="text-right">Estimasi</th>
                                <th>Status</th>
                                <th>No Invoice</th>
                                <th class="text-right">Override</th>
                                <th>Aksi</th>
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
                <div class="xl:col-span-1 sk-card p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        <h3 class="font-bold text-gray-800 text-sm">Pencatatan Sewa In Baru</h3>
                    </div>
                    <form id="form-create-sewa" class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Pilih Kontainer</label>
                            <select id="sewa-kontainer-select" class="sk-input" required>
                                <option value="">-- Cari No Kontainer --</option>
                            </select>
                        </div>
                        <div id="sewa-kontainer-info" class="hidden p-3 bg-green-50 rounded-xl border border-green-100 text-xs space-y-1 text-gray-700"></div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Mulai Sewa</label>
                            <input type="text" id="sewa-tanggal" class="sk-input font-mono" placeholder="dd/mm/yyyy" required>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-xl border border-gray-100 space-y-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Jenis Tarif</label>
                                <div class="grid grid-cols-2 gap-2 text-xs">
                                    <label class="flex items-center gap-1.5 p-2 bg-white border border-gray-200 rounded-lg cursor-pointer">
                                        <input type="radio" name="sewa_jenis_tarif" value="Bulanan" checked> Bulanan
                                    </label>
                                    <label class="flex items-center gap-1.5 p-2 bg-white border border-gray-200 rounded-lg cursor-pointer">
                                        <input type="radio" name="sewa_jenis_tarif" value="Harian"> Harian
                                    </label>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-[10px] font-semibold text-gray-600 mb-0.5">Bulanan (Rp)</label>
                                    <input type="number" id="sewa-tarif-bulanan" class="sk-input font-mono" style="font-size:12px;padding:6px 10px;" value="0">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-semibold text-gray-600 mb-0.5">Harian (Rp)</label>
                                    <input type="number" id="sewa-tarif-harian" class="sk-input font-mono" style="font-size:12px;padding:6px 10px;" value="0">
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Catatan (Opsional)</label>
                            <textarea id="sewa-catatan" rows="2" class="sk-input" placeholder="Tulis catatan di sini..."></textarea>
                        </div>
                        <div class="flex items-center gap-2 py-1.5 bg-gray-50 p-3 rounded-xl border border-gray-100">
                            <input type="checkbox" id="sewa-ppn" checked class="w-4 h-4 rounded">
                            <label for="sewa-ppn" class="text-xs font-semibold text-gray-700 cursor-pointer">Default Pakai PPN (11%)</label>
                        </div>
                        <button type="submit" class="sk-btn-primary w-full">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                            Konfirmasi Sewa Kontainer
                        </button>
                    </form>
                </div>

                {{-- List Sewa --}}
                <div class="xl:col-span-2 space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-gray-800 text-sm">Siklus Transaksi & Pengembalian</h3>
                        <input type="text" id="sewa-search" class="sk-input" style="max-width:250px;font-size:12px;" placeholder="Cari No Kontainer/Vendor...">
                    </div>
                    <div id="sewa-list" class="space-y-3">
                        <p class="text-gray-400 text-sm text-center py-8">Memuat data...</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 3: Master Data --}}
        <div class="sk-panel" id="panel-master">
            <div class="sk-card overflow-hidden">
                <div class="flex border-b border-gray-100 bg-gray-50 p-1 gap-1 flex-wrap">
                    <button class="sk-sub-tab active px-4 py-2 text-sm font-medium rounded-lg" data-subtab="vendor">1. Master Vendor</button>
                    <button class="sk-sub-tab px-4 py-2 text-sm font-medium rounded-lg" data-subtab="tipe">2. Master Tipe</button>
                    <button class="sk-sub-tab px-4 py-2 text-sm font-medium rounded-lg" data-subtab="ukuran">3. Master Ukuran</button>
                    <button class="sk-sub-tab px-4 py-2 text-sm font-medium rounded-lg" data-subtab="tarif">4. Master Tarif</button>
                    <button class="sk-sub-tab px-4 py-2 text-sm font-medium rounded-lg" data-subtab="kontainer">5. Master Kontainer</button>
                </div>
                <div class="p-6">
                    {{-- Vendor Sub-Panel --}}
                    <div class="sk-panel active" id="subpanel-vendor">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                            <div class="lg:col-span-1 bg-gray-50 p-5 rounded-2xl border border-gray-100">
                                <h3 class="font-semibold text-gray-800 text-sm mb-4">Input Vendor / Owner Baru</h3>
                                <form id="form-add-vendor" class="space-y-4">
                                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Nama Vendor / Owner</label><input type="text" id="vendor-name" class="sk-input" placeholder="Contoh: PT. Temas Line" required></div>
                                    <button type="submit" class="sk-btn-primary w-full">Simpan Vendor</button>
                                </form>
                            </div>
                            <div class="lg:col-span-2 space-y-4">
                                <input type="text" id="search-vendor" class="sk-input" style="max-width:300px" placeholder="Cari nama vendor...">
                                <div class="border border-gray-100 rounded-xl overflow-hidden"><table class="sk-table"><thead><tr><th>Nama Vendor / Owner</th><th class="text-right">Status / Aksi</th></tr></thead><tbody id="vendor-tbody"></tbody></table></div>
                            </div>
                        </div>
                    </div>

                    {{-- Tipe Sub-Panel --}}
                    <div class="sk-panel" id="subpanel-tipe">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                            <div class="lg:col-span-1 bg-gray-50 p-5 rounded-2xl border border-gray-100">
                                <h3 class="font-semibold text-gray-800 text-sm mb-4">Input Tipe Baru</h3>
                                <form id="form-add-tipe" class="space-y-4">
                                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Nama Tipe Kontainer</label><input type="text" id="tipe-name" class="sk-input" placeholder="Contoh: Dry, Reefer, Flat Rack" required></div>
                                    <button type="submit" class="sk-btn-primary w-full">Simpan Tipe</button>
                                </form>
                            </div>
                            <div class="lg:col-span-2 space-y-4">
                                <input type="text" id="search-tipe" class="sk-input" style="max-width:300px" placeholder="Cari tipe...">
                                <div class="border border-gray-100 rounded-xl overflow-hidden"><table class="sk-table"><thead><tr><th>Nama Tipe</th><th class="text-right">Status / Aksi</th></tr></thead><tbody id="tipe-tbody"></tbody></table></div>
                            </div>
                        </div>
                    </div>

                    {{-- Ukuran Sub-Panel --}}
                    <div class="sk-panel" id="subpanel-ukuran">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                            <div class="lg:col-span-1 bg-gray-50 p-5 rounded-2xl border border-gray-100">
                                <h3 class="font-semibold text-gray-800 text-sm mb-4">Input Ukuran</h3>
                                <form id="form-add-ukuran" class="space-y-4">
                                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Ukuran (Cukup Angka)</label><input type="text" id="ukuran-desc" class="sk-input" placeholder="Ketik 20 atau 40" required><span class="text-[10px] text-gray-500 mt-1 block">Sistem otomatis menambahkan petik tunggal (') → 20' / 40'</span></div>
                                    <button type="submit" class="sk-btn-primary w-full">Simpan Ukuran</button>
                                </form>
                            </div>
                            <div class="lg:col-span-2 space-y-4">
                                <input type="text" id="search-ukuran" class="sk-input" style="max-width:300px" placeholder="Cari ukuran...">
                                <div class="border border-gray-100 rounded-xl overflow-hidden"><table class="sk-table"><thead><tr><th>Deskripsi Ukuran</th><th class="text-right">Status / Aksi</th></tr></thead><tbody id="ukuran-tbody"></tbody></table></div>
                            </div>
                        </div>
                    </div>

                    {{-- Tarif Sub-Panel --}}
                    <div class="sk-panel" id="subpanel-tarif">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                            <div class="lg:col-span-1 bg-gray-50 p-5 rounded-2xl border border-gray-100">
                                <h3 class="font-semibold text-gray-800 text-sm mb-4">Input Tarif Sewa In</h3>
                                <form id="form-add-tarif" class="space-y-4">
                                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Vendor</label><select id="tarif-vendor" class="sk-input" required><option value="">-- Pilih Vendor --</option></select></div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Tipe</label><select id="tarif-tipe" class="sk-input" required><option value="">-- Pilih --</option></select></div>
                                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Ukuran</label><select id="tarif-ukuran" class="sk-input" required><option value="">-- Pilih --</option></select></div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Tarif Bulanan</label><input type="number" id="tarif-bulanan" class="sk-input font-mono" value="0"></div>
                                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Tarif Harian</label><input type="number" id="tarif-harian" class="sk-input font-mono" value="0"></div>
                                    </div>
                                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Mulai Berlaku</label><input type="text" id="tarif-start" class="sk-input font-mono" placeholder="dd/mm/yyyy"></div>
                                    <button type="submit" class="sk-btn-primary w-full">Simpan Tarif</button>
                                </form>
                            </div>
                            <div class="lg:col-span-2 space-y-4">
                                <input type="text" id="search-tarif" class="sk-input" style="max-width:300px" placeholder="Cari vendor tarif...">
                                <div class="border border-gray-100 rounded-xl overflow-hidden"><table class="sk-table"><thead><tr><th>Vendor</th><th>Tipe</th><th>Ukuran</th><th class="text-right">Bulanan</th><th class="text-right">Harian</th><th>Berlaku</th><th class="text-right">Aksi</th></tr></thead><tbody id="tarif-tbody"></tbody></table></div>
                            </div>
                        </div>
                    </div>

                    {{-- Kontainer Sub-Panel --}}
                    <div class="sk-panel" id="subpanel-kontainer">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                            <div class="lg:col-span-1 bg-gray-50 p-5 rounded-2xl border border-gray-100">
                                <h3 class="font-semibold text-gray-800 text-sm mb-4">Daftar Kontainer Baru</h3>
                                <form id="form-add-kontainer" class="space-y-4">
                                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Nomor Kontainer (Unik)</label><input type="text" id="kontainer-no" class="sk-input font-mono" placeholder="Contoh: GLDU7252828" required></div>
                                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Vendor Terkait</label><select id="kontainer-vendor" class="sk-input" required><option value="">-- Pilih Vendor --</option></select></div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Tipe</label><select id="kontainer-tipe" class="sk-input" required><option value="">-- Pilih --</option></select></div>
                                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Ukuran</label><select id="kontainer-ukuran" class="sk-input" required><option value="">-- Pilih --</option></select></div>
                                    </div>
                                    <button type="submit" class="sk-btn-primary w-full">Simpan Kontainer</button>
                                </form>
                            </div>
                            <div class="lg:col-span-2 space-y-4">
                                <input type="text" id="search-kontainer" class="sk-input" style="max-width:300px" placeholder="Cari kontainer/vendor...">
                                <div class="border border-gray-100 rounded-xl overflow-hidden"><table class="sk-table"><thead><tr class="font-mono"><th>NO. KONTAINER</th><th>VENDOR</th><th>TIPE</th><th>UKURAN</th><th>STATUS</th><th class="text-right">AKSI</th></tr></thead><tbody id="kontainer-tbody"></tbody></table></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 4: Bulk Import --}}
        <div class="sk-panel" id="panel-import">
            <div class="sk-card p-6">
                <h3 class="font-bold text-gray-800 text-sm mb-4">Impor Data Cepat (Format Text / Excel Paste)</h3>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Tipe Data Impor</label>
                            <select id="import-type" class="sk-input">
                                <option value="vendor">Vendor / Owner</option>
                                <option value="tipe">Tipe Kontainer</option>
                                <option value="ukuran">Ukuran Kontainer</option>
                                <option value="kontainer">Kontainer (NO;VENDOR;TIPE;UKURAN)</option>
                                <option value="tarif">Tarif Sewa</option>
                                <option value="sewa">Transaksi Sewa</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Data (satu per baris)</label>
                            <textarea id="import-text" rows="12" class="sk-input font-mono" style="font-size:11px;" placeholder="Paste data di sini..."></textarea>
                        </div>
                        <button type="button" id="btn-bulk-import" class="sk-btn-primary w-full">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            Proses Impor
                        </button>
                    </div>
                    <div class="lg:col-span-2">
                        <div id="import-results" class="bg-gray-50 rounded-xl border border-gray-100 p-4 min-h-[300px]">
                            <p class="text-gray-400 text-sm">Hasil impor akan tampil di sini...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Modal Container --}}
<div id="sk-modal-container"></div>
@endsection

@push('styles')
@endpush

@section('scripts')
<script>
$(document).ready(function() {
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    const BASE = '/sewa-kontainer';

    // ============================================================
    //  UTILITIES
    // ============================================================
    function formatRupiah(num) {
        const isNeg = num < 0;
        const abs = Math.abs(num);
        const formatted = abs.toLocaleString('id-ID');
        return (isNeg ? '-' : '') + 'Rp ' + formatted;
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
            sukses: 'bg-green-50 border-green-200 text-green-800',
            error: 'bg-red-50 border-red-100 text-red-800',
            info: 'bg-blue-50 border-blue-200 text-blue-800'
        };
        $('#sk-noti').html(`<div class="p-3 rounded-xl flex items-center gap-2 border text-sm ${colors[type] || colors.info}"><svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span class="font-semibold text-xs">${msg}</span></div>`).removeClass('hidden');
        setTimeout(() => $('#sk-noti').addClass('hidden'), 5000);
    }

    function ajaxPost(url, data) {
        return $.ajax({ url: BASE + url, type: 'POST', data: JSON.stringify(data), contentType: 'application/json', headers: {'X-CSRF-TOKEN': csrfToken} });
    }
    function ajaxPut(url, data) {
        return $.ajax({ url: BASE + url, type: 'PUT', data: JSON.stringify(data), contentType: 'application/json', headers: {'X-CSRF-TOKEN': csrfToken} });
    }
    function ajaxDelete(url) {
        return $.ajax({ url: BASE + url, type: 'DELETE', headers: {'X-CSRF-TOKEN': csrfToken} });
    }
    function ajaxGet(url, params) {
        return $.get(BASE + url, params);
    }

    function statusBadge(status) {
        const map = {
            'Belum Ditagih': 'bg-gray-100 text-gray-600 border-gray-200',
            'Pranota': 'bg-yellow-50 text-yellow-700 border-yellow-200',
            'Belum Bayar': 'bg-blue-50 text-blue-700 border-blue-200',
            'Lunas': 'bg-green-50 text-green-800 border-green-200'
        };
        return `<span class="sk-badge border ${map[status] || map['Belum Ditagih']}">${status}</span>`;
    }

    // ============================================================
    //  TAB NAVIGATION
    // ============================================================
    $('.sk-tab-btn').on('click', function() {
        const tab = $(this).data('tab');
        $('.sk-tab-btn').removeClass('active');
        $(this).addClass('active');
        $('.sk-panel').removeClass('active');
        $(`#panel-${tab}`).addClass('active');

        if (tab === 'billing') loadBillingData();
        if (tab === 'rental') loadSewaData();
        if (tab === 'master') loadCurrentMasterSubTab();
    });

    // Sub-tab navigation for Master
    $('.sk-sub-tab').on('click', function() {
        const subtab = $(this).data('subtab');
        $('.sk-sub-tab').removeClass('active');
        $(this).addClass('active');
        $('[id^=subpanel-]').removeClass('active');
        $(`#subpanel-${subtab}`).addClass('active');
        loadCurrentMasterSubTab();
    });

    function loadCurrentMasterSubTab() {
        const active = $('.sk-sub-tab.active').data('subtab');
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
                html += `<tr><td class="font-medium text-sm">${v.name}</td><td class="text-right">
                    <span class="sk-badge border ${isActive ? 'bg-green-50 text-green-700 border-green-200' : 'bg-gray-100 text-gray-500 border-gray-200'}">${isActive ? 'Aktif' : 'Non-Aktif'}</span>
                    <button onclick="toggleVendor(${v.id})" class="ml-2 sk-btn-secondary ${isActive ? 'text-yellow-600 border-yellow-200' : 'text-indigo-600 border-indigo-200'}">${isActive ? 'Nonaktifkan' : 'Aktifkan'}</button>
                </td></tr>`;
            });
            if (!data.length) html = '<tr><td colspan="2" class="p-8 text-center text-gray-400">Tidak ada data vendor</td></tr>';
            $('#vendor-tbody').html(html);
        });
    }

    $('#form-add-vendor').on('submit', function(e) {
        e.preventDefault();
        ajaxPost('/api/vendors', {name: $('#vendor-name').val()}).done(r => { showNoti('sukses', r.message); $('#vendor-name').val(''); loadVendors(); loadStats(); }).fail(r => showNoti('error', r.responseJSON?.message || 'Error'));
    });

    window.toggleVendor = function(id) {
        ajaxPost(`/api/vendors/${id}/toggle`, {}).done(r => { showNoti('sukses', r.message); loadVendors(); });
    };

    $('#search-vendor').on('input', () => loadVendors());

    // ============================================================
    //  MASTER: TIPE
    // ============================================================
    function loadTipes() {
        ajaxGet('/api/tipes').done(function(data) {
            const search = ($('#search-tipe').val() || '').toLowerCase();
            let filtered = data.filter(t => t.nama_tipe.toLowerCase().includes(search));
            let html = '';
            filtered.forEach(t => {
                const isActive = t.status_aktif !== false && t.status_aktif !== 0;
                html += `<tr><td class="font-medium text-sm">${t.nama_tipe}</td><td class="text-right">
                    <span class="sk-badge border ${isActive ? 'bg-green-50 text-green-700 border-green-200' : 'bg-gray-100 text-gray-500 border-gray-200'}">${isActive ? 'Aktif' : 'Non-Aktif'}</span>
                    <button onclick="toggleTipe(${t.id})" class="ml-2 sk-btn-secondary ${isActive ? 'text-yellow-600 border-yellow-200' : 'text-indigo-600 border-indigo-200'}">${isActive ? 'Nonaktifkan' : 'Aktifkan'}</button>
                </td></tr>`;
            });
            if (!filtered.length) html = '<tr><td colspan="2" class="p-8 text-center text-gray-400">Tidak ada data tipe</td></tr>';
            $('#tipe-tbody').html(html);
        });
    }

    $('#form-add-tipe').on('submit', function(e) {
        e.preventDefault();
        ajaxPost('/api/tipes', {nama_tipe: $('#tipe-name').val()}).done(r => { showNoti('sukses', r.message); $('#tipe-name').val(''); loadTipes(); }).fail(r => showNoti('error', r.responseJSON?.message || 'Error'));
    });
    window.toggleTipe = id => ajaxPost(`/api/tipes/${id}/toggle`, {}).done(r => { showNoti('sukses', r.message); loadTipes(); });
    $('#search-tipe').on('input', () => loadTipes());

    // ============================================================
    //  MASTER: UKURAN
    // ============================================================
    function loadUkurans() {
        ajaxGet('/api/ukurans').done(function(data) {
            const search = ($('#search-ukuran').val() || '').toLowerCase();
            let filtered = data.filter(u => u.deskripsi_ukuran.toLowerCase().includes(search));
            let html = '';
            filtered.forEach(u => {
                const isActive = u.status_aktif !== false && u.status_aktif !== 0;
                html += `<tr><td class="font-mono text-sm font-semibold text-green-800">${u.deskripsi_ukuran}</td><td class="text-right">
                    <span class="sk-badge border ${isActive ? 'bg-green-50 text-green-700 border-green-200' : 'bg-gray-100 text-gray-500 border-gray-200'}">${isActive ? 'Aktif' : 'Non-Aktif'}</span>
                    <button onclick="toggleUkuran(${u.id})" class="ml-2 sk-btn-secondary ${isActive ? 'text-yellow-600 border-yellow-200' : 'text-indigo-600 border-indigo-200'}">${isActive ? 'Nonaktifkan' : 'Aktifkan'}</button>
                </td></tr>`;
            });
            if (!filtered.length) html = '<tr><td colspan="2" class="p-8 text-center text-gray-400">Tidak ada data ukuran</td></tr>';
            $('#ukuran-tbody').html(html);
        });
    }

    $('#form-add-ukuran').on('submit', function(e) {
        e.preventDefault();
        ajaxPost('/api/ukurans', {deskripsi_ukuran: $('#ukuran-desc').val()}).done(r => { showNoti('sukses', r.message); $('#ukuran-desc').val(''); loadUkurans(); }).fail(r => showNoti('error', r.responseJSON?.message || 'Error'));
    });
    window.toggleUkuran = id => ajaxPost(`/api/ukurans/${id}/toggle`, {}).done(r => { showNoti('sukses', r.message); loadUkurans(); });
    $('#search-ukuran').on('input', () => loadUkurans());

    // ============================================================
    //  MASTER: KONTAINER
    // ============================================================
    function loadKontainers() {
        const search = $('#search-kontainer').val() || '';
        ajaxGet('/api/kontainers', {search}).done(function(data) {
            let html = '';
            data.forEach(k => {
                const isActive = k.status_aktif !== false && k.status_aktif !== 0;
                html += `<tr>
                    <td class="font-mono font-bold text-sm tracking-wide text-gray-900">${k.no_kontainer}</td>
                    <td>${k.vendor?.name || '-'}</td>
                    <td class="font-medium">${k.tipe?.nama_tipe || '-'}</td>
                    <td class="font-mono font-semibold text-green-800">${k.ukuran?.deskripsi_ukuran || '-'}</td>
                    <td><span class="sk-badge border ${isActive ? 'bg-green-50 text-green-700 border-green-200' : 'bg-gray-100 text-gray-500 border-gray-200'}">${isActive ? 'Aktif' : 'Non-Aktif'}</span></td>
                    <td class="text-right"><button onclick="toggleKontainer(${k.id})" class="sk-btn-secondary ${isActive ? 'text-yellow-600 border-yellow-200' : 'text-indigo-600 border-indigo-200'}">${isActive ? 'Nonaktifkan' : 'Aktifkan'}</button></td>
                </tr>`;
            });
            if (!data.length) html = '<tr><td colspan="6" class="p-8 text-center text-gray-400">Tidak ada data kontainer</td></tr>';
            $('#kontainer-tbody').html(html);
        });
    }

    function populateKontainerDropdowns() {
        ajaxGet('/api/vendors').done(d => { let h = '<option value="">-- Pilih Vendor --</option>'; d.filter(v=>v.status_aktif!==false&&v.status_aktif!==0).forEach(v => h += `<option value="${v.id}">${v.name}</option>`); $('#kontainer-vendor').html(h); });
        ajaxGet('/api/tipes').done(d => { let h = '<option value="">-- Pilih --</option>'; d.filter(t=>t.status_aktif!==false&&t.status_aktif!==0).forEach(t => h += `<option value="${t.id}">${t.nama_tipe}</option>`); $('#kontainer-tipe').html(h); });
        ajaxGet('/api/ukurans').done(d => { let h = '<option value="">-- Pilih --</option>'; d.filter(u=>u.status_aktif!==false&&u.status_aktif!==0).forEach(u => h += `<option value="${u.id}">${u.deskripsi_ukuran}</option>`); $('#kontainer-ukuran').html(h); });
    }

    $('#form-add-kontainer').on('submit', function(e) {
        e.preventDefault();
        ajaxPost('/api/kontainers', {no_kontainer: $('#kontainer-no').val(), vendor_id: $('#kontainer-vendor').val(), tipe_id: $('#kontainer-tipe').val(), ukuran_id: $('#kontainer-ukuran').val()})
        .done(r => { showNoti('sukses', r.message); $('#kontainer-no').val(''); loadKontainers(); loadStats(); }).fail(r => showNoti('error', r.responseJSON?.message || 'Error'));
    });
    window.toggleKontainer = id => ajaxPost(`/api/kontainers/${id}/toggle`, {}).done(r => { showNoti('sukses', r.message); loadKontainers(); });
    $('#search-kontainer').on('input', () => loadKontainers());

    // ============================================================
    //  MASTER: TARIF
    // ============================================================
    function loadTarifs() {
        const search = $('#search-tarif').val() || '';
        ajaxGet('/api/tarifs', {search}).done(function(data) {
            let html = '';
            data.forEach(t => {
                const isActive = t.status_aktif !== false && t.status_aktif !== 0;
                html += `<tr>
                    <td>${t.vendor?.name || '-'}</td><td>${t.tipe?.nama_tipe || '-'}</td><td class="font-mono">${t.ukuran?.deskripsi_ukuran || '-'}</td>
                    <td class="text-right font-mono">${formatRupiah(t.tarif_bulanan)}</td><td class="text-right font-mono">${formatRupiah(t.tarif_harian)}</td>
                    <td class="font-mono text-xs">${formatIndoDate(t.tanggal_mulai_berlaku)} ${t.tanggal_akhir_berlaku ? '→ ' + formatIndoDate(t.tanggal_akhir_berlaku) : '→ Sekarang'}</td>
                    <td class="text-right"><button onclick="toggleTarif(${t.id})" class="sk-btn-secondary ${isActive ? 'text-yellow-600 border-yellow-200' : 'text-indigo-600 border-indigo-200'}">${isActive ? 'Nonaktifkan' : 'Aktifkan'}</button></td>
                </tr>`;
            });
            if (!data.length) html = '<tr><td colspan="7" class="p-8 text-center text-gray-400">Tidak ada data tarif</td></tr>';
            $('#tarif-tbody').html(html);
        });
    }

    function populateTarifDropdowns() {
        ajaxGet('/api/vendors').done(d => { let h = '<option value="">-- Pilih Vendor --</option>'; d.filter(v=>v.status_aktif!==false&&v.status_aktif!==0).forEach(v => h += `<option value="${v.id}">${v.name}</option>`); $('#tarif-vendor').html(h); });
        ajaxGet('/api/tipes').done(d => { let h = '<option value="">-- Pilih --</option>'; d.filter(t=>t.status_aktif!==false&&t.status_aktif!==0).forEach(t => h += `<option value="${t.id}">${t.nama_tipe}</option>`); $('#tarif-tipe').html(h); });
        ajaxGet('/api/ukurans').done(d => { let h = '<option value="">-- Pilih --</option>'; d.filter(u=>u.status_aktif!==false&&u.status_aktif!==0).forEach(u => h += `<option value="${u.id}">${u.deskripsi_ukuran}</option>`); $('#tarif-ukuran').html(h); });
    }

    $('#form-add-tarif').on('submit', function(e) {
        e.preventDefault();
        ajaxPost('/api/tarifs', {vendor_id: $('#tarif-vendor').val(), tipe_id: $('#tarif-tipe').val(), ukuran_id: $('#tarif-ukuran').val(), tarif_bulanan: $('#tarif-bulanan').val(), tarif_harian: $('#tarif-harian').val(), tanggal_mulai_berlaku: $('#tarif-start').val()})
        .done(r => { showNoti('sukses', r.message); loadTarifs(); }).fail(r => showNoti('error', r.responseJSON?.message || 'Error'));
    });
    window.toggleTarif = id => ajaxPost(`/api/tarifs/${id}/toggle`, {}).done(r => { showNoti('sukses', r.message); loadTarifs(); });
    $('#search-tarif').on('input', () => loadTarifs());

    // ============================================================
    //  SEWA TRANSACTIONS
    // ============================================================
    function loadSewaData() {
        // Populate kontainer dropdown
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
            if (!sewas.length) {
                $('#sewa-list').html('<p class="text-gray-400 text-sm text-center py-8">Tidak ada data transaksi sewa</p>');
                return;
            }
            let html = '';
            sewas.forEach(s => {
                const isAktif = s.status_sewa === 'Aktif';
                const tagihans = s.tagihans || [];

                let billingBadgeHtml = statusBadge(s.billing_status || 'Belum Ditagih');

                html += `<div class="sk-sewa-card ${isAktif ? 'border-yellow-100 bg-yellow-50/30' : ''}">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-dashed border-gray-100 pb-3 mb-3">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-mono font-bold text-base tracking-wide text-gray-900">${s.no_kontainer}</span>
                            <span class="text-gray-300">|</span>
                            <span class="text-xs font-medium text-gray-500">${s.kontainer?.tipe?.nama_tipe||'-'} (${s.kontainer?.ukuran?.deskripsi_ukuran||'-'})</span>
                            <span class="text-gray-300">|</span>
                            ${billingBadgeHtml}
                        </div>
                        <div class="flex items-center gap-1.5 flex-wrap">
                            ${isAktif ? `<span class="sk-badge bg-yellow-100 text-yellow-800 border border-yellow-200">● Sedang Disewa</span>
                            <button onclick="showReturnModal(${s.id}, '${s.no_kontainer}')" class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-lg bg-green-600 hover:bg-green-700 text-white cursor-pointer">Kembalikan</button>` :
                            `<span class="sk-badge bg-gray-100 text-gray-800 border border-gray-200">✓ Selesai</span>`}
                            <button onclick="deleteSewa(${s.id})" class="inline-flex items-center p-1.5 text-xs rounded-lg bg-red-50 hover:bg-red-100 text-red-700 border border-red-100 cursor-pointer" title="Hapus">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 text-xs">
                        <div><p class="text-gray-500">Vendor / Owner</p><p class="font-semibold text-gray-800">${s.vendor?.name||'-'}</p></div>
                        <div><p class="text-gray-500">Rentang Sewa</p><p class="font-mono font-medium text-gray-800">${formatIndoDate(s.tanggal_sewa)} - ${s.tanggal_kembali ? formatIndoDate(s.tanggal_kembali) : 'Saat Ini'}</p></div>
                        <div><p class="text-gray-500">Jenis Tarif</p><p class="font-semibold text-gray-700">${s.jenis_tarif} (${formatRupiah(s.jenis_tarif==='Bulanan'?s.tarif_bulanan:s.tarif_harian)})</p></div>
                        <div><p class="text-gray-500 text-right">Akumulasi</p><p class="font-mono font-bold text-gray-900 text-right text-sm">${formatRupiah(s.total_estimasi||0)}</p></div>
                        <div><p class="text-red-500 text-right font-semibold">Outstanding</p><p class="font-mono font-extrabold text-red-700 text-right text-sm">${formatRupiah(s.total_outstanding||0)}</p></div>
                    </div>
                    ${s.catatan ? `<div class="mt-3 text-[11px] text-gray-500 bg-gray-50 p-2 rounded-lg italic">Catatan: ${s.catatan}</div>` : ''}
                    ${tagihans.length ? `<div class="mt-4 border border-gray-100 rounded-lg overflow-hidden bg-gray-50/30">
                        <div class="bg-gray-100 p-2 text-[10px] font-bold text-gray-600 font-mono">PERINCIAN PERIODE BILLING:</div>
                        <table class="sk-period-table"><thead><tr><th>PERIODE</th><th>MASA</th><th>STATUS</th><th class="text-center">HARI</th><th class="text-right">TARIF</th><th class="text-right">TOTAL</th></tr></thead><tbody>
                        ${tagihans.map(t => `<tr>
                            <td class="font-semibold text-[10px] text-gray-700">BULAN KE-${t.bulan_ke}</td>
                            <td class="text-gray-500">${formatIndoDate(t.tanggal_awal)} - ${formatIndoDate(t.tanggal_akhir)}</td>
                            <td>${statusBadge(t.status_bayar)}</td>
                            <td class="text-center text-gray-600">${t.jumlah_hari}</td>
                            <td class="text-right font-bold text-gray-500 text-[10px]">${t.tipe_tarif}</td>
                            <td class="text-right font-bold text-gray-800">${t.jumlah_tagihan_override !== null ? `<span class="text-[9px] text-gray-400 line-through">${formatRupiah(t.jumlah_tagihan_estimasi)}</span> ${formatRupiah(t.jumlah_tagihan_override)}` : formatRupiah(t.jumlah_tagihan_estimasi)}</td>
                        </tr>`).join('')}
                        </tbody></table>
                    </div>` : ''}
                </div>`;
            });
            $('#sewa-list').html(html);
        });
    }

    // Kontainer select change → show info
    $('#sewa-kontainer-select').on('change', function() {
        const opt = $(this).find(':selected');
        if (opt.val()) {
            $('#sewa-kontainer-info').removeClass('hidden').html(`<p><strong>Spesifikasi:</strong> ${opt.data('tipe')} (${opt.data('ukuran')})</p><p><strong>Vendor:</strong> ${opt.data('vendor')}</p>`);
        } else {
            $('#sewa-kontainer-info').addClass('hidden');
        }
    });

    // Create sewa
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
        ajaxPost('/api/sewas', data).done(r => {
            showNoti('sukses', r.message);
            $('#sewa-tanggal').val(''); $('#sewa-catatan').val('');
            loadSewaData(); loadStats();
        }).fail(r => showNoti('error', r.responseJSON?.message || 'Error'));
    });

    // Return modal
    window.showReturnModal = function(sewaId, noKontainer) {
        const html = `<div class="sk-modal-overlay" id="return-modal">
            <div class="sk-modal">
                <h3 class="font-bold text-gray-800 mb-4">Kembalikan Kontainer ${noKontainer}</h3>
                <form id="form-return-sewa">
                    <div class="mb-4"><label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Kembali</label><input type="text" id="return-tanggal" class="sk-input font-mono" placeholder="dd/mm/yyyy" required></div>
                    <div class="flex gap-2">
                        <button type="submit" class="sk-btn-primary flex-1">Konfirmasi Pengembalian</button>
                        <button type="button" onclick="$('#return-modal').remove()" class="sk-btn-secondary flex-1">Batal</button>
                    </div>
                </form>
            </div>
        </div>`;
        $('#sk-modal-container').html(html);
        $('#form-return-sewa').on('submit', function(e) {
            e.preventDefault();
            ajaxPut(`/api/sewas/${sewaId}/return`, {tanggal_kembali: $('#return-tanggal').val()}).done(r => {
                showNoti('sukses', r.message); $('#return-modal').remove(); loadSewaData(); loadStats();
            }).fail(r => showNoti('error', r.responseJSON?.message || 'Error'));
        });
    };

    // Delete sewa
    window.deleteSewa = function(sewaId) {
        if (!confirm('Yakin ingin menghapus transaksi sewa ini beserta seluruh data tagihannya?')) return;
        ajaxDelete(`/api/sewas/${sewaId}`).done(r => { showNoti('sukses', r.message); loadSewaData(); loadStats(); }).fail(r => showNoti('error', r.responseJSON?.message || 'Error'));
    };

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
                    <td class="font-mono font-bold">${sewa.no_kontainer || '-'}</td>
                    <td>${sewa.vendor?.name || '-'}</td>
                    <td class="font-mono text-[10px]">BLN ${t.bulan_ke}</td>
                    <td class="text-xs">${formatIndoDate(t.tanggal_awal)} - ${formatIndoDate(t.tanggal_akhir)}</td>
                    <td class="text-center">${t.jumlah_hari}</td>
                    <td class="text-[10px] font-bold text-gray-500">${t.tipe_tarif}</td>
                    <td class="text-right font-mono font-bold">${formatRupiah(t.jumlah_tagihan_estimasi)}</td>
                    <td>${statusBadge(t.status_bayar)}</td>
                    <td class="text-xs">${t.nomor_invoice || '-'}</td>
                    <td class="text-right font-mono">${t.jumlah_tagihan_override !== null ? formatRupiah(t.jumlah_tagihan_override) : '-'}</td>
                    <td>
                        <select onchange="updateTagihanStatus(${t.id}, this.value)" class="text-[10px] border border-gray-200 rounded px-1 py-0.5">
                            <option value="Belum Ditagih" ${t.status_bayar==='Belum Ditagih'?'selected':''}>Belum Ditagih</option>
                            <option value="Pranota" ${t.status_bayar==='Pranota'?'selected':''}>Pranota</option>
                            <option value="Belum Bayar" ${t.status_bayar==='Belum Bayar'?'selected':''}>Belum Bayar</option>
                            <option value="Lunas" ${t.status_bayar==='Lunas'?'selected':''}>Lunas</option>
                        </select>
                    </td>
                </tr>`;
            });
            if (!data.length) html = '<tr><td colspan="11" class="p-8 text-center text-gray-400">Tidak ada data tagihan</td></tr>';
            $('#billing-tbody').html(html);
        });

        // Populate vendor filter
        ajaxGet('/api/vendors').done(function(vendors) {
            let h = '<option value="">Semua Vendor</option>';
            vendors.forEach(v => h += `<option value="${v.id}">${v.name}</option>`);
            const current = $('#billing-vendor-filter').val();
            $('#billing-vendor-filter').html(h).val(current);
        });
    }

    window.updateTagihanStatus = function(id, status) {
        ajaxPut(`/api/tagihans/${id}`, {status_bayar: status}).done(r => {
            showNoti('sukses', r.message); loadStats();
        }).fail(r => showNoti('error', r.responseJSON?.message || 'Error'));
    };

    $('#billing-vendor-filter, #billing-status-filter').on('change', () => loadBillingData());
    $('#billing-search').on('input', () => loadBillingData());

    // ============================================================
    //  BULK IMPORT
    // ============================================================
    $('#btn-bulk-import').on('click', function() {
        const type = $('#import-type').val();
        const text = $('#import-text').val();
        if (!text.trim()) { showNoti('error', 'Data tidak boleh kosong'); return; }

        ajaxPost('/api/bulk-import', {type, text}).done(function(r) {
            let html = `<div class="mb-3 p-3 rounded-lg ${r.success_count > 0 ? 'bg-green-50 border border-green-200 text-green-800' : 'bg-yellow-50 border border-yellow-200 text-yellow-800'}">
                <p class="font-bold text-sm">${r.message}</p>
            </div>`;

            if (r.errors && r.errors.length > 0) {
                html += '<div class="space-y-1">';
                r.errors.forEach(e => {
                    html += `<div class="p-2 rounded bg-red-50 border border-red-100 text-xs text-red-800">
                        <strong>Baris ${e.line}:</strong> ${e.error}<br><code class="text-[10px] text-gray-600">${e.raw}</code>
                    </div>`;
                });
                html += '</div>';
            }

            $('#import-results').html(html);
            loadStats();
        }).fail(r => {
            $('#import-results').html(`<div class="p-3 bg-red-50 border border-red-100 rounded-lg text-red-800 text-sm">${r.responseJSON?.message || 'Gagal mengimpor data'}</div>`);
        });
    });

    // ============================================================
    //  INITIAL LOAD
    // ============================================================
    loadStats();
    loadBillingData();
});
</script>
@endsection
