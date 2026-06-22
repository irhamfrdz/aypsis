@extends('layouts.app', ['hideSidebar' => true])

@section('title', 'Portal Sewa Kontainer')

@section('content')
<style>
/* ── Variables ── */
:root {
    --sewa-primary: #059669; /* emerald-600 */
    --sewa-primary-light: #d1fae5;
    --sewa-primary-ring: rgba(5,150,105,.15);
}
body.mode-sewa-in {
    --sewa-primary: #4f46e5; /* indigo-600 */
    --sewa-primary-light: #e0e7ff;
    --sewa-primary-ring: rgba(79,70,229,.15);
}

/* ── App Shell & Header Dynamic Colors ── */
.sk-app-shell { background-color: #f8fafc; } /* slate-50 */
body.mode-sewa-in .sk-app-shell { background-color: rgba(238,242,255,0.2); } /* indigo-50/20 */

.sk-header { background-color: #022c22; border-color: rgba(6,78,59,.4); } /* emerald-950 */
body.mode-sewa-in .sk-header { background-color: #1e1b4b; border-color: rgba(49,46,129,.4); } /* indigo-950 */

.sk-header-icon-box { background-color: #065f46; border-color: rgba(4,120,87,.55); color: #6ee7b7; } /* emerald-800, text-emerald-300 */
body.mode-sewa-in .sk-header-icon-box { background-color: rgba(49,46,129,0.85); border-color: rgba(67,56,202,.55); color: #a5b4fc; }

.sk-header-badge { background-color: rgba(6,78,59,.9); border-color: #065f46; color: #6ee7b7; }
body.mode-sewa-in .sk-header-badge { background-color: rgba(49,46,129,.9); border-color: #3730a3; color: #a5b4fc; }

.sk-header-desc { color: #a7f3d0; }
body.mode-sewa-in .sk-header-desc { color: #c7d2fe; }

.sk-mode-toggle-box { background-color: rgba(6,78,59,.5); border-color: rgba(2,44,34,.6); }
body.mode-sewa-in .sk-mode-toggle-box { background-color: rgba(49,46,129,.7); border-color: rgba(30,27,75,.6); }

.sk-clock-box { background-color: rgba(6,78,59,.4); border-color: rgba(6,78,59,.4); }
body.mode-sewa-in .sk-clock-box { background-color: rgba(49,46,129,.4); border-color: rgba(49,46,129,.4); }
.sk-clock-dot { background-color: #34d399; }
body.mode-sewa-in .sk-clock-dot { background-color: #818cf8; }

/* ── Tabs (React Style) ── */
.sk-main-tab { padding: .75rem 1.5rem; font-size: .75rem; font-weight: 700; border-bottom: 2px solid transparent; color: #64748b; transition: all .2s; display: inline-flex; align-items: center; gap: .5rem; cursor: pointer; white-space: nowrap; }
.sk-main-tab.active { border-color: var(--sewa-primary); color: var(--sewa-primary); font-weight: 800; }
.sk-main-tab:hover:not(.active) { color: #1e293b; border-color: #cbd5e1; }
.sk-tab-content { display: none; }
.sk-tab-content.active { display: block; }

/* ── Sub-tabs ── */
.sk-sub-tab { padding:.45rem .9rem; font-size:.75rem; font-weight:500; border-radius:.6rem; cursor:pointer; color:#64748b; transition:all .15s; }
.sk-sub-tab.active { background:white; color:var(--sewa-primary); box-shadow:0 1px 3px rgba(0,0,0,.1); }
.sk-sub-tab:hover:not(.active) { background:#f1f5f9; }

/* ── Bento Box KPI ── */
.bento-kpi { background: white; padding: 1.25rem; border-radius: 1rem; border: 1px solid #f1f5f9; box-shadow: 0 1px 2px rgba(0,0,0,.05); display: flex; align-items: center; justify-content: space-between; }
.bento-kpi-icon { padding: .75rem; border-radius: .75rem; border: 1px solid transparent; }
.bento-icon-layers { background: #fffbeb; color: #b45309; border-color: #fef3c7; } /* amber */
.bento-icon-activity { background: #eef2ff; color: #4338ca; border-color: #e0e7ff; } /* indigo */
body.mode-sewa-in .bento-icon-layers { background: #eef2ff; color: #4338ca; border-color: #e0e7ff; }

/* ── Table ── */
.sk-table { width:100%; border-collapse:collapse; font-size:.8rem; }
.sk-table thead th { background:#f8fafc; padding:.7rem .9rem; text-align:left; font-weight:700; font-size:.68rem; text-transform:uppercase; letter-spacing:.04em; color:#64748b; white-space:nowrap; border-bottom:1px solid #e2e8f0; }
.sk-table tbody td { padding:.6rem .9rem; border-bottom:1px solid #f1f5f9; color:#374151; vertical-align:middle; }
.sk-table tbody tr:hover { background:#f8fafc; }
/* ── Status Badges ── */
.badge { display:inline-flex; align-items:center; padding:.18rem .55rem; border-radius:9999px; font-size:.67rem; font-weight:700; text-transform:uppercase; white-space:nowrap; }
.badge-belum-ditagih { background:#f1f5f9; color:#64748b; }
.badge-pranota  { background:#ede9fe; color:#6d28d9; }
.badge-belum-bayar { background:#fee2e2; color:#991b1b; }
.badge-lunas    { background:#d1fae5; color:#065f46; }
.badge-aktif    { background:#dcfce7; color:#14532d; }
.badge-selesai  { background:#f1f5f9; color:#475569; }
.badge-harian   { background:#fef3c7; color:#78350f; }
.badge-prorate  { background:#fce7f3; color:#831843; }
.badge-bulanan  { background:#dbeafe; color:#1e3a8a; }
/* ── Buttons ── */
.sk-btn-primary { display:inline-flex; align-items:center; gap:.35rem; padding:.45rem 1rem; background:var(--sewa-primary); color:white; border-radius:.6rem; font-size:.78rem; font-weight:600; cursor:pointer; border:none; transition:filter .15s; }
.sk-btn-primary:hover { filter:brightness(.9); }
.sk-btn-sm { padding:.3rem .65rem; font-size:.72rem; font-weight:600; border-radius:.45rem; cursor:pointer; border:none; transition:all .15s; display:inline-flex; align-items:center; gap:.25rem; }
.sk-btn-ghost { background:#f1f5f9; color:#475569; }
.sk-btn-ghost:hover { background:#e2e8f0; }
.sk-btn-red  { background:#fee2e2; color:#991b1b; }
.sk-btn-red:hover  { background:#fecaca; }
.sk-btn-green { background:#d1fae5; color:#065f46; }
.sk-btn-green:hover { background:#a7f3d0; }
/* ── Forms ── */
.sk-form-group { margin-bottom:.8rem; }
.sk-label { display:block; font-size:.72rem; font-weight:600; color:#475569; margin-bottom:.25rem; }
.sk-input { width:100%; border:1px solid #e2e8f0; border-radius:.6rem; padding:.45rem .75rem; font-size:.8rem; outline:none; transition:border-color .15s, box-shadow .15s; background:white; }
.sk-input:focus { border-color:var(--sewa-primary); box-shadow:0 0 0 3px var(--sewa-primary-ring); }
/* ── Cards ── */
.sk-card { background:white; border:1px solid #e5e7eb; border-radius:1.1rem; overflow:hidden; }
.sk-card-header { padding:.9rem 1.2rem; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; justify-content:space-between; gap:.5rem; flex-wrap:wrap; }
/* ── Sewa Cards ── */
.sewa-card { background:white; border:1px solid #e5e7eb; border-radius:1rem; overflow:hidden; transition:box-shadow .2s; }
.sewa-card:hover { box-shadow:0 4px 20px rgba(0,0,0,.08); }
.sewa-card-periods td { padding:.3rem .5rem; font-size:.72rem; }
/* ── Notification ── */
#sk-notification { position:fixed; top:1.2rem; right:1.2rem; z-index:9999; min-width:280px; max-width:420px; padding:.7rem 1rem; border-radius:.75rem; font-size:.8rem; font-weight:600; display:none; align-items:center; gap:.5rem; box-shadow:0 4px 20px rgba(0,0,0,.15); }
#sk-notification.success { background:#d1fae5; color:#065f46; border:1px solid #a7f3d0; }
#sk-notification.error   { background:#fee2e2; color:#991b1b; border:1px solid #fecaca; }
/* ── Pagination ── */
.sk-pagination { display:flex; align-items:center; gap:.3rem; flex-wrap:wrap; }
.sk-page-btn { padding:.3rem .55rem; border:1px solid #e2e8f0; border-radius:.4rem; font-size:.72rem; cursor:pointer; background:white; transition:all .15s; }
.sk-page-btn:hover:not(:disabled) { background:#f1f5f9; }
.sk-page-btn.active { background:var(--sewa-primary); color:white; border-color:var(--sewa-primary); }
.sk-page-btn:disabled { opacity:.4; cursor:not-allowed; }
/* ── Inline edit ── */
.editable-cell { cursor:pointer; padding:.15rem .3rem; border-radius:.3rem; transition:background .15s; min-width:60px; display:inline-block; }
.editable-cell:hover { background:#f1f5f9; }
/* ── Modal ── */
.sk-modal-overlay { position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:1000; display:none; align-items:center; justify-content:center; padding:1rem; }
.sk-modal-overlay.active { display:flex; }
.sk-modal { background:white; border-radius:1.2rem; padding:1.5rem; width:100%; max-width:540px; max-height:90vh; overflow-y:auto; position:relative; box-shadow:0 20px 60px rgba(0,0,0,.2); }
.sk-modal-lg { max-width:780px; }
/* ── Searchable Select ── */
.sk-searchable { position:relative; }
.sk-searchable-input { width:100%; border:1px solid #e2e8f0; border-radius:.6rem; padding:.45rem .75rem; font-size:.8rem; outline:none; transition:border-color .15s; background:white; cursor:pointer; }
.sk-searchable-input:focus { border-color:var(--sewa-primary); box-shadow:0 0 0 3px var(--sewa-primary-ring); }
.sk-searchable-dropdown { position:absolute; top:100%; left:0; right:0; z-index:200; background:white; border:1px solid #e2e8f0; border-radius:.6rem; box-shadow:0 4px 20px rgba(0,0,0,.12); max-height:220px; overflow-y:auto; display:none; }
.sk-searchable-dropdown.open { display:block; }
.sk-searchable-option { padding:.4rem .75rem; font-size:.78rem; cursor:pointer; }
.sk-searchable-option:hover { background:#f1f5f9; }
.sk-searchable-option.disabled { opacity:.4; cursor:not-allowed; color:#9ca3af; }
.sk-searchable-option.selected { background:var(--sewa-primary-light); color:var(--sewa-primary); font-weight:600; }
/* ── Sort indicators ── */
th .sort-icon { font-size:.65rem; margin-left:.2rem; color:#cbd5e1; }
th.sort-asc .sort-icon { color:var(--sewa-primary); }
th.sort-desc .sort-icon { color:var(--sewa-primary); }
/* ── Sewa billing badge ── */
.sewa-billing-badge { display:inline-flex; align-items:center; gap:.2rem; padding:.15rem .5rem; border-radius:9999px; font-size:.65rem; font-weight:700; }
/* ── Import bulk log ── */
.import-log-ok { color:#059669; background:#d1fae5; padding:.15rem .4rem; border-radius:.3rem; font-size:.72rem; }
.import-log-err { color:#b91c1c; background:#fee2e2; padding:.15rem .4rem; border-radius:.3rem; font-size:.72rem; }
/* ── Print ── */
@media print { .sk-modal-overlay,.no-print { display:none!important; } .print-area { display:block!important; } }
.print-area { display:none; }
</style>

<div id="sk-notification" role="alert"></div>

<div class="sk-app-shell transition-colors duration-300 font-sans antialiased min-h-screen -mt-6 -mx-6">
  
  {{-- ══════════════════════════════════════════════════
       PROFESSIONAL HIGH-CONTRAST HEADER
  ══════════════════════════════════════════════════ --}}
  <header class="sk-header transition-colors duration-350 text-white border-b sticky top-0 z-40 shadow-sm" id="navbar-top">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div class="flex items-center gap-3">
        <div class="sk-header-icon-box p-2 rounded-xl border shadow-inner transition-colors duration-350">
          <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="5" r="3"></circle><line x1="12" y1="22" x2="12" y2="8"></line><path d="M5 12H2a10 10 0 0 0 20 0h-3"></path></svg>
        </div>
        <div>
          <h1 class="font-bold text-base tracking-tight text-white uppercase flex flex-wrap items-center gap-2">
            <span>PORTAL SEWA KONTAINER</span>
            <span class="sk-header-badge text-[9px] px-2.5 py-0.5 rounded-full font-bold uppercase transition-all duration-350 border" id="header-badge-text">
              Pihak Pemilik (Sewa Out)
            </span>
            <span class="bg-slate-900/60 text-slate-300 border border-slate-800 text-[9px] px-2 py-0.5 rounded-full font-semibold">
              Live Database
            </span>
          </h1>
          <p class="sk-header-desc text-[10px] italic transition-colors duration-350" id="header-desc-text">
            Sistem kalkulasi proris maret ke januari (30 hari) & tahun kabisat februari (28/29 hari)
          </p>
        </div>
      </div>

      {{-- MODE TOGGLE --}}
      <div class="sk-mode-toggle-box flex p-1 rounded-xl border self-start md:self-center transition-colors duration-350">
        <button id="btn-mode-in" onclick="setMode('in')" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all duration-300 flex items-center gap-1.5 cursor-pointer select-none bg-indigo-600 text-white shadow-sm font-extrabold">
          <span>Sewa In (Lessee)</span>
        </button>
      </div>

      {{-- CLOCK ACCENT --}}
      <div class="sk-clock-box flex items-center gap-2.5 px-3 py-1.5 rounded-xl border text-xs self-start md:self-center transition-colors duration-350">
        <span class="sk-clock-dot w-2 h-2 rounded-full animate-pulse"></span>
        <span class="text-slate-300 uppercase font-bold text-[9px] tracking-wider font-mono">WAKTU AKTIF WIB:</span>
        <span class="font-mono font-medium text-slate-100" id="sk-clock"></span>
      </div>
    </div>
  </header>

  <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    {{-- ══════════════════════════════════════════════════
         KPI METRIC CARDS GRID (BENTO BOX)
    ══════════════════════════════════════════════════ --}}
    @php
        $totalKontainer  = $kontainers->count();
        $sewaAktif       = $sewas->where('status_sewa', 'Aktif')->count();
        $belumLunas      = $tagihans->whereIn('status_bayar', ['Belum Bayar', 'Pranota'])->sum('jumlah_tagihan');
        $totalRealisasi  = $tagihans->where('status_bayar', 'Lunas')->sum('jumlah_tagihan');
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <div class="bento-kpi">
            <div>
                <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Total Kontainer Terdaftar</p>
                <h3 class="text-xl font-bold text-slate-800 mt-1 font-mono">{{ $totalKontainer }} Unit</h3>
                <p class="text-[10px] text-slate-500 mt-0.5">Semua tipe & ukuran</p>
            </div>
            <div class="bento-kpi-icon bento-icon-layers">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83Z"/><path d="m22 17.65-9.17 4.16a2 2 0 0 1-1.66 0L2 17.65"/><path d="m22 12.65-9.17 4.16a2 2 0 0 1-1.66 0L2 12.65"/></svg>
            </div>
        </div>

        <div class="bento-kpi">
            <div>
                <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider" id="kpi-aktif-label">Sewa Berjalan Aktif</p>
                <h3 class="text-xl font-bold mt-1 font-mono" style="color:var(--sewa-primary)">{{ $sewaAktif }} Siklus</h3>
                <p class="text-[10px] text-slate-500 mt-0.5">Paralel sewa diperbolehkan</p>
            </div>
            <div class="bento-kpi-icon bento-icon-activity">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-2.48a2 2 0 0 0-1.93 1.46l-2.35 8.36a.25.25 0 0 1-.48 0L9.24 2.18a.25.25 0 0 0-.48 0l-2.35 8.36A2 2 0 0 1 4.48 12H2"/></svg>
            </div>
        </div>

        <div class="bento-kpi">
            <div>
                <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider" id="kpi-belum-label">Total Belum Tertagih/Bayar</p>
                <h3 class="text-xl font-bold text-rose-700 mt-1 font-mono">Rp {{ number_format($belumLunas, 0, ',', '.') }}</h3>
                <p class="text-[10px] text-rose-500 mt-0.5">Siklus outstanding bulanan</p>
            </div>
            <div class="bento-kpi-icon" style="background: #fff1f2; color: #be123c; border-color: #ffe4e6;">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" x2="21" y1="22" y2="22"/><line x1="6" x2="6" y1="18" y2="11"/><line x1="10" x2="10" y1="18" y2="11"/><line x1="14" x2="14" y1="18" y2="11"/><line x1="18" x2="18" y1="18" y2="11"/><polygon points="12 2 20 7 4 7"/></svg>
            </div>
        </div>

        <div class="bento-kpi">
            <div>
                <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider" id="kpi-lunas-label">Pendapatan Diterima (Lunas)</p>
                <h3 class="text-xl font-bold mt-1 font-mono" style="color:var(--sewa-primary)">Rp {{ number_format($totalRealisasi, 0, ',', '.') }}</h3>
                <p class="text-[10px] text-slate-500 mt-0.5">Tanpa sistem cicilan/parsial</p>
            </div>
            <div class="bento-kpi-icon" style="background: var(--sewa-primary-light); color: var(--sewa-primary); border-color: rgba(5,150,105,.2);" id="kpi-lunas-icon">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/><path d="m9 12 2 2 4-4"/></svg>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════
         WORKSPACE NAVIGATION TABS IN INDONESIAN
    ══════════════════════════════════════════════════ --}}
    <div class="flex border-b border-slate-200 overflow-x-auto">
        <button class="sk-main-tab active" onclick="switchMainTab('billing', this)">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>
            <span id="tab-label-billing">1. Dasbor Tagihan & Pembayaran</span>
        </button>
        <button class="sk-main-tab" onclick="switchMainTab('contracts', this)">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M21 12a9 9 0 1 0-9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"/><path d="M16 21v-5h5"/></svg>
            <span id="tab-label-contracts">2. Siklus Sewa & Pengembalian</span>
        </button>
        <button class="sk-main-tab" onclick="switchMainTab('master', this)">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5V19A9 3 0 0 0 21 19V5"/><path d="M3 12A9 3 0 0 0 21 12"/></svg>
            <span id="tab-label-master">3. Kelola Database Master</span>
        </button>
        <button class="sk-main-tab" onclick="switchMainTab('import', this)">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M8 13h2"/><path d="M14 13h2"/><path d="M8 17h2"/><path d="M14 17h2"/></svg>
            <span id="tab-label-import">4. Peluncur Impor Excel Cepat</span>
        </button>
    </div>

    {{-- ══════════════════════════════════════════════════
         TAB 1: BILLING & PEMBAYARAN
    ══════════════════════════════════════════════════ --}}
<div id="tab-billing" class="sk-tab-content active pt-5">
    {{-- Sub-tabs --}}
    <div class="flex bg-slate-50 border border-slate-200 rounded-xl p-1 gap-1 mb-5 overflow-x-auto">
        <button class="sk-sub-tab active" onclick="switchBillingTab('sheet', this)">📋 Sheet</button>
        <button class="sk-sub-tab" onclick="switchBillingTab('group', this)">📁 Group Invoice</button>
        <button class="sk-sub-tab" onclick="switchBillingTab('collective', this)">📑 Collective</button>
        <button class="sk-sub-tab" onclick="switchBillingTab('report', this)">📊 Laporan</button>
    </div>

    {{-- ─── Sheet View ─── --}}
    <div id="billing-sheet" class="billing-sub">
        {{-- Filters --}}
        <div class="bg-white border border-gray-200 rounded-xl p-4 mb-4 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-3">
            <div>
                <label class="sk-label" id="lbl-filter-customer">Customer / Vendor</label>
                <select id="filter-customer" class="sk-input text-sm" onchange="applyFilters()">
                    <option value="">Semua</option>
                    @foreach($customers as $c)
                    <option value="{{ $c->id_customer }}">{{ $c->nama_customer }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="sk-label">Status Bayar</label>
                <select id="filter-status" class="sk-input text-sm" onchange="applyFilters()">
                    <option value="">Semua</option>
                    <option value="Belum Ditagih">Belum Ditagih</option>
                    <option value="Pranota">Pranota</option>
                    <option value="Belum Bayar">Belum Bayar</option>
                    <option value="Lunas">Lunas</option>
                </select>
            </div>
            <div>
                <label class="sk-label">Cari No Kontainer</label>
                <input id="filter-kontainer" type="text" class="sk-input text-sm" placeholder="AMFU..." oninput="applyFilters()">
            </div>
            <div>
                <label class="sk-label">Rentang Sewa</label>
                <select id="filter-rentang-sewa" class="sk-input text-sm" onchange="applyFilters()">
                    <option value="">Semua Sewa</option>
                    @foreach($sewas as $sw)
                    <option value="{{ $sw->id_sewa }}">
                        {{ $sw->no_kontainer }}: {{ date('d/m/y', strtotime($sw->tanggal_sewa)) }} – {{ $sw->tanggal_kembali ? date('d/m/y', strtotime($sw->tanggal_kembali)) : 'Skrg' }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button onclick="selectAllVisible()" class="sk-btn-sm sk-btn-ghost flex-1"><i class="fas fa-check-square mr-1"></i>Pilih Semua</button>
                <button onclick="clearFilters()" class="sk-btn-sm sk-btn-ghost"><i class="fas fa-undo"></i></button>
            </div>
        </div>
        {{-- Bulk actions --}}
        <div id="bulk-actions" class="hidden bg-emerald-50 border border-emerald-200 rounded-xl p-3 mb-4 flex items-center gap-3 flex-wrap">
            <span class="text-sm font-bold text-emerald-800" id="bulk-count">0 dipilih</span>
            <button onclick="openInvoiceModal()" class="sk-btn-sm" style="background:var(--sewa-primary);color:white"><i class="fas fa-file-invoice mr-1"></i>Buat Invoice Grup</button>
            <button onclick="bulkSetStatus('Pranota')" class="sk-btn-sm" style="background:#ede9fe;color:#5b21b6"><i class="fas fa-stamp mr-1"></i>Set Pranota</button>
            <button onclick="clearSelection()" class="sk-btn-sm sk-btn-ghost ml-auto"><i class="fas fa-times"></i></button>
        </div>
        {{-- Table --}}
        <div class="sk-card">
            <div class="overflow-x-auto">
                <table class="sk-table" id="billing-table">
                    <thead>
                        <tr>
                            <th class="w-8"><input type="checkbox" id="chk-all" onchange="toggleAllCheckboxes(this)" class="rounded"></th>
                            <th id="th-sort-id" onclick="sortTable('id_tagihan')" class="cursor-pointer hover:bg-gray-100">Tagihan ID <span class="sort-icon">↕</span></th>
                            <th id="th-sort-customer" onclick="sortTable('customer')" class="cursor-pointer hover:bg-gray-100" id="th-customer">Customer <span class="sort-icon">↕</span></th>
                            <th>No Kontainer</th>
                            <th id="th-sort-bulan" onclick="sortTable('bulan_ke')" class="cursor-pointer hover:bg-gray-100">Bln <span class="sort-icon">↕</span></th>
                            <th>Rentang Periode</th>
                            <th>Tarif</th>
                            <th id="th-sort-jumlah" onclick="sortTable('jumlah_tagihan')" class="cursor-pointer hover:bg-gray-100">Estimasi Tagihan <span class="sort-icon">↕</span></th>
                            <th>Override</th>
                            <th>PPN (11%)</th>
                            <th>PPh (2%)</th>
                            <th>Tgl Tagihan</th>
                            <th>Tgl Bayar</th>
                            <th>No Bayar</th>
                            <th id="th-sort-status" onclick="sortTable('status_bayar')" class="cursor-pointer hover:bg-gray-100">Status <span class="sort-icon">↕</span></th>
                            <th>No Invoice</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="billing-tbody">
                        @forelse($tagihans as $t)
                        @php
                            $statusClass = match($t->status_bayar) {
                                'Belum Ditagih' => 'badge-belum-ditagih',
                                'Pranota' => 'badge-pranota',
                                'Belum Bayar' => 'badge-belum-bayar',
                                'Lunas' => 'badge-lunas',
                                default => 'badge-belum-ditagih'
                            };
                            $tarifClass = match($t->tipe_tarif) {
                                'BULANAN' => 'badge-bulanan',
                                'HARIAN' => 'badge-harian',
                                'PRORATE' => 'badge-prorate',
                                default => 'badge-belum-ditagih'
                            };
                        @endphp
                        <tr class="billing-row"
                            data-id="{{ $t->id_tagihan }}"
                            data-status="{{ $t->status_bayar }}"
                            data-customer="{{ $t->transaksi->id_customer ?? '' }}"
                            data-kontainer="{{ $t->transaksi->no_kontainer ?? '' }}"
                            data-id-sewa="{{ $t->id_sewa }}"
                            data-bulan-ke="{{ $t->bulan_ke }}"
                            data-jumlah="{{ $t->jumlah_tagihan }}">
                            <td><input type="checkbox" class="row-chk rounded" value="{{ $t->id_tagihan }}" onchange="onRowCheck()"></td>
                            <td class="font-mono text-xs text-gray-400">{{ $t->id_tagihan }}</td>
                            <td class="font-semibold">{{ $t->transaksi->customer->nama_customer ?? '-' }}</td>
                            <td class="font-mono font-bold text-xs">{{ $t->transaksi->no_kontainer ?? '-' }}</td>
                            <td class="text-center">{{ $t->bulan_ke }}</td>
                            <td class="text-xs">
                                {{ date('d/m/y', strtotime($t->tanggal_awal)) }} — {{ date('d/m/y', strtotime($t->tanggal_akhir)) }}
                                <br><span class="text-gray-400">({{ $t->jumlah_hari }} hr)</span>
                            </td>
                            <td><span class="badge {{ $tarifClass }}">{{ $t->tipe_tarif }}</span></td>
                            <td class="font-bold">Rp {{ number_format($t->jumlah_tagihan, 0, ',', '.') }}</td>
                            <td>
                                <span class="editable-cell text-xs font-mono" onclick="inlineEdit(this, '{{ $t->id_tagihan }}', 'jumlah_tagihan_override')">
                                    {{ $t->jumlah_tagihan_override ? 'Rp '.number_format($t->jumlah_tagihan_override,0,',','.') : '—' }}
                                </span>
                            </td>
                            <td class="text-xs font-mono">{{ $t->ppn ? number_format($t->ppn,0,',','.') : '—' }}</td>
                            <td class="text-xs font-mono">{{ $t->pph ? number_format($t->pph,0,',','.') : '—' }}</td>
                            <td>
                                <span class="editable-cell text-xs" onclick="inlineEdit(this, '{{ $t->id_tagihan }}', 'tanggal_tagihan')">
                                    {{ $t->tanggal_tagihan ? date('d/m/y', strtotime($t->tanggal_tagihan)) : '—' }}
                                </span>
                            </td>
                            <td>
                                <span class="editable-cell text-xs" onclick="inlineEdit(this, '{{ $t->id_tagihan }}', 'tanggal_bayar')">
                                    {{ $t->tanggal_bayar ? date('d/m/y', strtotime($t->tanggal_bayar)) : '—' }}
                                </span>
                            </td>
                            <td>
                                <span class="editable-cell text-xs font-mono" onclick="inlineEdit(this, '{{ $t->id_tagihan }}', 'nomor_bayar')">
                                    {{ $t->nomor_bayar ?: '—' }}
                                </span>
                            </td>
                            <td><span class="badge {{ $statusClass }}">{{ $t->status_bayar }}</span></td>
                            <td class="text-xs font-mono text-blue-600">{{ $t->nomor_invoice_grup ?: '—' }}</td>
                            <td>
                                <button onclick="openTagihanModal('{{ $t->id_tagihan }}')" class="sk-btn-sm sk-btn-ghost">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="17" class="py-10 text-center text-gray-400">Belum ada tagihan periodik.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-t border-gray-100 flex items-center justify-between flex-wrap gap-3">
                <span class="text-xs text-gray-400" id="billing-info">Menampilkan <span id="billing-count">0</span> tagihan</span>
                <div class="sk-pagination" id="billing-pagination"></div>
            </div>
        </div>
    </div>

    {{-- ─── Group Invoice View ─── --}}
    <div id="billing-group" class="billing-sub hidden">
        <div class="space-y-4">
        @forelse($invoices as $inv)
        @php
            $invTagihans  = $inv->tagihans;
            $totalEstimasi= $invTagihans->sum('jumlah_tagihan');
            $totalBayar   = ($totalEstimasi + $inv->adjustment_biaya);
        @endphp
        <div class="sk-card">
            <div class="sk-card-header">
                <div>
                    <div class="font-bold text-gray-800 font-mono">{{ $inv->nomor_invoice }}</div>
                    <div class="text-xs text-gray-400 mt-0.5">{{ $inv->customer->nama_customer ?? '-' }} · {{ date('d/m/Y', strtotime($inv->tanggal_invoice)) }}</div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="badge {{ $inv->status_pembayaran === 'Lunas' ? 'badge-lunas' : 'badge-belum-bayar' }}">{{ $inv->status_pembayaran }}</span>
                    <button onclick="openPrintInvoiceModal({{ json_encode(['nomor'=>$inv->nomor_invoice,'customer'=>$inv->customer->nama_customer??'-','tanggal'=>date('d/m/Y',strtotime($inv->tanggal_invoice)),'status'=>$inv->status_pembayaran,'grand_total'=>$totalBayar,'adjustment'=>$inv->adjustment_biaya,'tagihans'=>$invTagihans->map(fn($it)=>['id_tagihan'=>$it->id_tagihan,'no_kontainer'=>$it->transaksi->no_kontainer??'-','bulan_ke'=>$it->bulan_ke,'tanggal_awal'=>date('d/m/y',strtotime($it->tanggal_awal)),'tanggal_akhir'=>date('d/m/y',strtotime($it->tanggal_akhir)),'jumlah_tagihan'=>$it->jumlah_tagihan,'jumlah_tagihan_override'=>$it->jumlah_tagihan_override,'tipe_tarif'=>$it->tipe_tarif,'status_bayar'=>$it->status_bayar])->values()->toArray()]) }})" class="sk-btn-sm" style="background:#e0f2fe;color:#0369a1" title="Cetak Invoice"><i class="fas fa-print"></i></button>
                    <button onclick="openEditInvoiceModal('{{ $inv->nomor_invoice }}', '{{ addslashes($inv->deskripsi) }}', {{ $inv->adjustment_biaya }}, '{{ addslashes($inv->adjustment_keterangan) }}')" class="sk-btn-sm sk-btn-ghost"><i class="fas fa-edit"></i></button>
                    <button onclick="deleteInvoice('{{ $inv->nomor_invoice }}')" class="sk-btn-sm sk-btn-red"><i class="fas fa-trash"></i></button>
                </div>
            </div>
            <div class="p-4 overflow-x-auto">
                <table class="sk-table text-xs">
                    <thead><tr><th>Tagihan ID</th><th>No Kontainer</th><th>Bulan</th><th>Periode</th><th>Tarif</th><th>Estimasi</th><th>Status</th></tr></thead>
                    <tbody>
                    @foreach($invTagihans as $it)
                    <tr>
                        <td class="font-mono text-gray-400 text-xs">{{ $it->id_tagihan }}</td>
                        <td class="font-mono font-bold">{{ $it->transaksi->no_kontainer ?? '-' }}</td>
                        <td class="text-center">{{ $it->bulan_ke }}</td>
                        <td>{{ date('d/m/y', strtotime($it->tanggal_awal)) }} – {{ date('d/m/y', strtotime($it->tanggal_akhir)) }}</td>
                        <td><span class="badge badge-{{ strtolower($it->tipe_tarif) }}">{{ $it->tipe_tarif }}</span></td>
                        <td class="font-bold">Rp {{ number_format($it->jumlah_tagihan, 0, ',', '.') }}</td>
                        <td><span class="badge {{ match($it->status_bayar){ 'Lunas'=>'badge-lunas','Belum Bayar'=>'badge-belum-bayar','Pranota'=>'badge-pranota',default=>'badge-belum-ditagih'} }}">{{ $it->status_bayar }}</span></td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-4 pb-4 flex flex-wrap gap-4 text-xs">
                <div class="flex-1">
                    @if($inv->deskripsi)<p class="text-gray-500"><i class="fas fa-note-sticky mr-1"></i>{{ $inv->deskripsi }}</p>@endif
                    @if($inv->adjustment_keterangan)<p class="text-amber-600"><i class="fas fa-exclamation-circle mr-1"></i>Adj: {{ $inv->adjustment_keterangan }}</p>@endif
                </div>
                <div class="text-right space-y-0.5">
                    <div class="text-gray-500">Estimasi: <strong class="text-gray-800">Rp {{ number_format($totalEstimasi, 0, ',', '.') }}</strong></div>
                    @if($inv->adjustment_biaya != 0)
                    <div class="text-amber-600">Adjustment: <strong>Rp {{ number_format($inv->adjustment_biaya, 0, ',', '.') }}</strong></div>
                    @endif
                    <div class="text-base font-bold" style="color:var(--sewa-primary)">Total: Rp {{ number_format($totalBayar, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-12 text-gray-400"><i class="fas fa-folder-open text-4xl mb-3 block"></i>Belum ada invoice grup.</div>
        @endforelse
        </div>
    </div>

    {{-- ─── Collective View ─── --}}
    <div id="billing-collective" class="billing-sub hidden">
        <div class="sk-card">
            <div class="sk-card-header">
                <h4 class="font-bold text-gray-800">Collective Invoice View — Satu Baris per Nota</h4>
                <div class="text-xs text-gray-400">Klik baris untuk update status massal</div>
            </div>
            <div class="overflow-x-auto">
                <table class="sk-table">
                    <thead><tr><th>No Invoice</th><th id="th-customer-coll">Customer</th><th>Tgl Invoice</th><th>Jml Tagihan</th><th>Adj</th><th>Total</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                    @forelse($invoices as $inv)
                    <tr>
                        <td class="font-mono font-bold text-blue-700 text-xs">{{ $inv->nomor_invoice }}</td>
                        <td class="font-semibold">{{ $inv->customer->nama_customer ?? '-' }}</td>
                        <td class="text-xs">{{ date('d/m/Y', strtotime($inv->tanggal_invoice)) }}</td>
                        <td>Rp {{ number_format($inv->tagihans->sum('jumlah_tagihan'), 0, ',', '.') }}</td>
                        <td class="{{ $inv->adjustment_biaya >= 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ $inv->adjustment_biaya != 0 ? number_format($inv->adjustment_biaya, 0, ',', '.') : '—' }}</td>
                        <td class="font-bold">Rp {{ number_format($inv->tagihans->sum('jumlah_tagihan') + $inv->adjustment_biaya, 0, ',', '.') }}</td>
                        <td><span class="badge {{ $inv->status_pembayaran === 'Lunas' ? 'badge-lunas' : 'badge-belum-bayar' }}">{{ $inv->status_pembayaran }}</span></td>
                        <td>
                            @if($inv->status_pembayaran !== 'Lunas')
                            <button onclick="quickLunasInvoice('{{ $inv->nomor_invoice }}')" class="sk-btn-sm sk-btn-green"><i class="fas fa-check"></i> Lunas</button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="py-8 text-center text-gray-400">Belum ada invoice.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ─── Laporan View ─── --}}
    <div id="billing-report" class="billing-sub hidden">
        @php
            $reportByCustomer = $tagihans->groupBy(fn($t) => $t->transaksi->customer->nama_customer ?? 'Unknown');
        @endphp
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        @foreach($reportByCustomer as $custName => $custTagihans)
        <div class="sk-card">
            <div class="sk-card-header">
                <h5 class="font-bold text-gray-800">{{ $custName }}</h5>
                <span class="text-xs text-gray-400">{{ $custTagihans->count() }} tagihan</span>
            </div>
            <div class="p-4 space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Belum Ditagih</span><span class="font-bold">Rp {{ number_format($custTagihans->where('status_bayar','Belum Ditagih')->sum('jumlah_tagihan'), 0, ',', '.') }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Pranota/Draft</span><span class="font-bold text-purple-600">Rp {{ number_format($custTagihans->where('status_bayar','Pranota')->sum('jumlah_tagihan'), 0, ',', '.') }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Belum Bayar</span><span class="font-bold text-red-600">Rp {{ number_format($custTagihans->where('status_bayar','Belum Bayar')->sum('jumlah_tagihan'), 0, ',', '.') }}</span></div>
                <div class="flex justify-between border-t pt-2"><span class="font-bold text-gray-700">Lunas</span><span class="font-bold text-emerald-600">Rp {{ number_format($custTagihans->where('status_bayar','Lunas')->sum('jumlah_tagihan'), 0, ',', '.') }}</span></div>
            </div>
        </div>
        @endforeach
        @if($reportByCustomer->isEmpty())
        <div class="text-center py-12 text-gray-400 col-span-2">Belum ada data tagihan.</div>
        @endif
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════
     TAB 2: TRANSAKSI SEWA
══════════════════════════════════════════════════ --}}
<div id="tab-contracts" class="sk-tab-content pt-5">
    {{-- Form buat sewa --}}
    <div class="sk-card mb-5">
        <div class="sk-card-header">
            <h4 class="font-bold text-gray-800" id="contracts-form-title">Buat Kontrak Sewa Baru</h4>
            <button onclick="toggleNewSewaForm()" id="btn-toggle-sewa-form" class="sk-btn-primary">
                <i class="fas fa-plus"></i><span>Buat Kontrak</span>
            </button>
        </div>
        <div id="new-sewa-form" class="hidden p-5 border-t border-gray-100">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="lg:col-span-1">
                    <label class="sk-label">No Kontainer</label>
                    {{-- SearchableSelect --}}
                    <div class="sk-searchable" id="searchable-kontainer">
                        <input type="hidden" id="sewa-no-kontainer" name="sewa-no-kontainer">
                        <input type="text" id="sewa-no-kontainer-search" class="sk-searchable-input" placeholder="Ketik untuk cari kontainer..." autocomplete="off"
                               oninput="filterSearchableKontainer(this.value)"
                               onblur="closeSearchableKontainerDelayed()"
                               onfocus="openSearchableKontainer()">
                        <div id="sewa-no-kontainer-dropdown" class="sk-searchable-dropdown">
                            @php
                                $activeSewaKontainers = $sewas->where('status_sewa','Aktif')->pluck('no_kontainer')->toArray();
                            @endphp
                            @foreach($kontainers as $k)
                            @php $isActive = in_array($k->no_kontainer, $activeSewaKontainers); @endphp
                            <div class="sk-searchable-option {{ $isActive ? 'disabled' : '' }}"
                                 data-value="{{ $k->no_kontainer }}"
                                 data-label="{{ $k->no_kontainer }} [{{ $k->customer->nama_customer ?? '-' }}]{{ $isActive ? ' (SEDANG DISEWA)' : '' }}"
                                 data-customer="{{ $k->id_customer }}"
                                 onclick="{{ $isActive ? '' : 'selectSearchableKontainer(this)' }}">
                                {{ $k->no_kontainer }}
                                <span class="text-gray-400 text-xs ml-1">[{{ $k->customer->nama_customer ?? '-' }}]{{ $isActive ? ' <span style="color:#dc2626">(SEDANG DISEWA)</span>' : '' }}</span>
                            </div>
                            @endforeach
                        </div>
                        <div id="sewa-tarif-info" class="hidden text-xs mt-1 text-blue-600 bg-blue-50 rounded-lg p-2 border border-blue-100"></div>
                    </div>
                </div>
                <div>
                    <label class="sk-label" id="lbl-sewa-customer">Customer</label>
                    <select id="sewa-id-customer" class="sk-input text-sm">
                        <option value="">— Pilih Customer —</option>
                        @foreach($customers as $c)
                        <option value="{{ $c->id_customer }}">{{ $c->nama_customer }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="sk-label">Tanggal Mulai Sewa</label>
                    <input id="sewa-tanggal-sewa" type="date" class="sk-input" value="{{ date('Y-m-d') }}">
                </div>
                <div>
                    <label class="sk-label">Jenis Tarif</label>
                    <select id="sewa-jenis-tarif" class="sk-input text-sm" onchange="onJenisTarifChange()">
                        <option value="Bulanan">Bulanan</option>
                        <option value="Harian">Harian</option>
                    </select>
                </div>
                <div>
                    <label class="sk-label">Tarif Bulanan (Rp)</label>
                    <input id="sewa-tarif-bulanan" type="number" class="sk-input" placeholder="0">
                </div>
                <div>
                    <label class="sk-label">Tarif Harian (Rp)</label>
                    <input id="sewa-tarif-harian" type="number" class="sk-input" placeholder="0">
                </div>
                <div class="lg:col-span-3">
                    <label class="sk-label">Catatan (Opsional)</label>
                    <textarea id="sewa-catatan" rows="2" class="sk-input resize-none" placeholder="Catatan tambahan..."></textarea>
                </div>
            </div>
            <div class="flex gap-3 mt-4">
                <button onclick="submitNewSewa()" class="sk-btn-primary"><i class="fas fa-save"></i>Simpan Kontrak</button>
                <button onclick="toggleNewSewaForm()" class="sk-btn-sm sk-btn-ghost px-4 py-2">Batal</button>
            </div>
        </div>
    </div>

    {{-- Search --}}
    <div class="mb-4 flex gap-3">
        <input id="contracts-search" type="text" class="sk-input max-w-sm text-sm" placeholder="Cari No Kontainer / Customer..." oninput="filterContracts()">
        <select id="contracts-status-filter" class="sk-input max-w-xs text-sm" onchange="filterContracts()">
            <option value="">Semua Status</option>
            <option value="Aktif">Aktif</option>
            <option value="Selesai">Selesai</option>
        </select>
    </div>

    {{-- Pagination state --}}
    <div id="sewa-pagination-controls" class="hidden bg-white border border-slate-100 rounded-2xl p-3 flex flex-wrap items-center justify-between gap-3 text-xs text-slate-600 mb-3">
        <span>Menampilkan <strong id="sewa-page-start">1</strong>–<strong id="sewa-page-end">20</strong> dari <strong id="sewa-page-total">0</strong> kontrak sewa</span>
        <div class="flex items-center gap-1 font-mono" id="sewa-pagination-btns"></div>
    </div>

    {{-- Cards --}}
    <div class="space-y-4" id="contracts-container">
    @forelse($sewas as $sewa)
    @php
        $durasi = '';
        $tglSewa = \Carbon\Carbon::parse($sewa->tanggal_sewa);
        $tglKembali = $sewa->tanggal_kembali ? \Carbon\Carbon::parse($sewa->tanggal_kembali) : null;
        $endDate = $tglKembali ?? \Carbon\Carbon::now();
        $months = (int)$tglSewa->diffInMonths($endDate);
        $days = (int)$tglSewa->copy()->addMonths($months)->diffInDays($endDate);
        if ($months > 0 && $days > 0) $durasi = "{$months} Bln + {$days} Hr";
        elseif ($months > 0) $durasi = "{$months} Bulan";
        else $durasi = "{$days} Hari";

        $sewaTagihans = $sewa->tagihans ?? collect();
        $lunas = $sewaTagihans->where('status_bayar', 'Lunas')->count();
        $total = $sewaTagihans->count();
        $outstanding = $sewaTagihans->whereIn('status_bayar', ['Belum Bayar', 'Pranota'])->sum('jumlah_tagihan');

        // Billing badge
        $allLunas = $total > 0 && $lunas === $total;
        $hasUnpaid = $sewaTagihans->whereIn('status_bayar', ['Belum Bayar','Pranota'])->count() > 0;
        $hasPartial = $lunas > 0 && !$allLunas && !$hasUnpaid;
        $billingBadgeClass = $allLunas ? 'background:#d1fae5;color:#065f46' : ($hasUnpaid ? 'background:#dbeafe;color:#1e40af' : ($hasPartial ? 'background:#fef3c7;color:#92400e' : 'background:#f1f5f9;color:#6b7280'));
        $billingBadgeText = $allLunas ? '✓ Lunas' : ($hasUnpaid ? '● Sudah Ditagih' : ($hasPartial ? '● Bayar Parsial' : 'Belum Ditagih'));
    @endphp
    <div class="sewa-card"
         data-search="{{ strtolower($sewa->no_kontainer . ' ' . ($sewa->customer->nama_customer ?? '')) }}"
         data-status="{{ $sewa->status_sewa }}">
        {{-- Card Header --}}
        <div class="p-4 border-b border-gray-100 flex flex-wrap items-start justify-between gap-3">
            <div class="flex-1">
                <div class="flex items-center gap-2 flex-wrap mb-1">
                    <span class="font-mono font-black text-gray-900 text-base">{{ $sewa->no_kontainer }}</span>
                    <span class="badge {{ $sewa->status_sewa === 'Aktif' ? 'badge-aktif' : 'badge-selesai' }}">{{ $sewa->status_sewa }}</span>
                    @if($sewa->jenis_tarif === 'Bulanan')
                    <span class="badge badge-bulanan">Bulanan</span>
                    @else
                    <span class="badge badge-harian">Harian</span>
                    @endif
                    <span class="sewa-billing-badge" style="{{ $billingBadgeClass }};font-size:.65rem;border-radius:9999px;padding:.15rem .5rem">{{ $billingBadgeText }}</span>
                </div>
                <div class="text-sm font-semibold text-gray-700">{{ $sewa->customer->nama_customer ?? '-' }}</div>
                <div class="text-xs text-gray-400 mt-0.5">
                    {{ date('d/m/Y', strtotime($sewa->tanggal_sewa)) }}
                    @if($sewa->tanggal_kembali) → {{ date('d/m/Y', strtotime($sewa->tanggal_kembali)) }} @else → Sekarang @endif
                    · <strong>{{ $durasi }}</strong>
                </div>
                @if($sewa->catatan)
                <div class="text-xs text-amber-600 mt-1"><i class="fas fa-sticky-note mr-1"></i>{{ $sewa->catatan }}</div>
                @endif
            </div>
            <div class="text-right shrink-0">
                <div class="text-xs text-gray-400">Tarif Bulanan</div>
                <div class="font-bold text-gray-800">Rp {{ number_format($sewa->tarif_bulanan, 0, ',', '.') }}</div>
                <div class="text-xs text-gray-400 mt-1">Tarif Harian</div>
                <div class="font-bold text-gray-800">Rp {{ number_format($sewa->tarif_harian, 0, ',', '.') }}</div>
            </div>
        </div>
        {{-- Billing Summary --}}
        <div class="px-4 py-2 bg-gray-50 border-b border-gray-100 flex flex-wrap gap-4 text-xs">
            <span>Periode: <strong>{{ $total }}</strong></span>
            <span class="text-emerald-600">Lunas: <strong>{{ $lunas }}</strong></span>
            @if($outstanding > 0)
            <span class="text-red-600">Outstanding: <strong>Rp {{ number_format($outstanding, 0, ',', '.') }}</strong></span>
            @endif
        </div>
        {{-- Periods mini table --}}
        @if($sewaTagihans->count() > 0)
        <div class="px-4 pb-3 pt-2 overflow-x-auto">
            <table class="sk-table text-xs sewa-card-periods">
                <thead><tr><th>Bln</th><th>Periode</th><th>Hari</th><th>Tarif</th><th>Tagihan</th><th>Status</th><th>No Invoice</th></tr></thead>
                <tbody>
                @foreach($sewaTagihans->sortBy('bulan_ke') as $p)
                <tr>
                    <td class="text-center font-bold">{{ $p->bulan_ke }}</td>
                    <td>{{ date('d/m/y', strtotime($p->tanggal_awal)) }} – {{ date('d/m/y', strtotime($p->tanggal_akhir)) }}</td>
                    <td class="text-center">{{ $p->jumlah_hari }}</td>
                    <td><span class="badge badge-{{ strtolower($p->tipe_tarif) }}">{{ $p->tipe_tarif }}</span></td>
                    <td class="font-bold">
                        @if($p->jumlah_tagihan_override !== null)
                            <span class="line-through text-gray-400 text-xs">Rp {{ number_format($p->jumlah_tagihan, 0, ',', '.') }}</span><br>
                            <span class="text-emerald-700">Rp {{ number_format($p->jumlah_tagihan_override, 0, ',', '.') }}</span>
                        @else
                            Rp {{ number_format($p->jumlah_tagihan, 0, ',', '.') }}
                        @endif
                    </td>
                    <td><span class="badge {{ match($p->status_bayar){ 'Lunas'=>'badge-lunas','Belum Bayar'=>'badge-belum-bayar','Pranota'=>'badge-pranota',default=>'badge-belum-ditagih'} }}">{{ $p->status_bayar }}</span></td>
                    <td class="font-mono text-blue-600 text-xs">{{ $p->nomor_invoice_grup ?: '—' }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @endif
        {{-- Actions --}}
        <div class="px-4 pb-4 flex gap-2 flex-wrap">
            <button onclick="openEditSewaModal({{ $sewa->id_sewa ? "'".$sewa->id_sewa."'" : 'null' }})" class="sk-btn-sm sk-btn-ghost"><i class="fas fa-edit mr-1"></i>Edit</button>
            @if($sewa->status_sewa === 'Aktif')
            <button onclick="openTerminateModal('{{ $sewa->id_sewa }}')" class="sk-btn-sm" style="background:#fef3c7;color:#92400e"><i class="fas fa-calendar-times mr-1"></i>Kembalikan</button>
            @endif
            <button onclick="deleteSewa('{{ $sewa->id_sewa }}')" class="sk-btn-sm sk-btn-red"><i class="fas fa-trash mr-1"></i>Hapus</button>
        </div>
    </div>
    @empty
    <div class="text-center py-16 text-gray-400"><i class="fas fa-box-open text-5xl mb-4 block"></i>Belum ada transaksi sewa.</div>
    @endforelse
    </div>
</div>

{{-- ══════════════════════════════════════════════════
     TAB 3: MASTER DATABASE
══════════════════════════════════════════════════ --}}
<div id="tab-master" class="sk-tab-content pt-5">
    {{-- Sub-tab nav --}}
    <div class="flex bg-slate-50 border border-slate-200 rounded-xl p-1 gap-1 mb-5 overflow-x-auto">
        <button class="sk-sub-tab active" onclick="switchMasterTab('customer', this)"><span id="lbl-master-customer">1. Customer</span></button>
        <button class="sk-sub-tab" onclick="switchMasterTab('tipe', this)">2. Tipe Kontainer</button>
        <button class="sk-sub-tab" onclick="switchMasterTab('ukuran', this)">3. Ukuran</button>
        <button class="sk-sub-tab" onclick="switchMasterTab('kontainer', this)">4. Kontainer</button>
        <button class="sk-sub-tab" onclick="switchMasterTab('tarif', this)">5. Tarif Sewa</button>
    </div>

    {{-- ─── Master Customer ─── --}}
    <div id="master-customer" class="master-sub">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-slate-50 p-5 rounded-xl border border-slate-200">
                <h3 class="font-semibold text-slate-800 text-sm mb-4" id="lbl-form-customer">Input Customer Baru</h3>
                <div class="sk-form-group">
                    <label class="sk-label" id="lbl-input-customer">Nama Customer</label>
                    <input id="input-customer-name" type="text" class="sk-input" placeholder="CV. Samudera Raya">
                </div>
                <button onclick="submitCustomer()" class="sk-btn-primary w-full justify-center"><i class="fas fa-save mr-1.5"></i>Simpan</button>
            </div>
            <div class="lg:col-span-2">
                <input type="text" id="search-customer" class="sk-input mb-3" placeholder="Cari nama customer..." oninput="filterMasterTable('customer', this.value)">
                <div class="sk-card">
                    <table class="sk-table" id="table-customer">
                        <thead><tr><th id="th-cust">Nama Customer</th><th class="text-right w-20">Aksi</th></tr></thead>
                        <tbody>
                        @foreach($customers as $c)
                        <tr data-search="{{ strtolower($c->nama_customer) }}">
                            <td class="font-semibold">{{ $c->nama_customer }}</td>
                            <td class="text-right"><button onclick="deleteMaster('customer','{{ $c->id_customer }}','{{ addslashes($c->nama_customer) }}')" class="text-red-400 hover:text-red-600 transition-colors p-1"><i class="fas fa-trash-alt"></i></button></td>
                        </tr>
                        @endforeach
                        @if($customers->isEmpty())<tr><td colspan="2" class="py-6 text-center text-gray-400">Tidak ada data</td></tr>@endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Master Tipe ─── --}}
    <div id="master-tipe" class="master-sub hidden">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-slate-50 p-5 rounded-xl border border-slate-200">
                <h3 class="font-semibold text-slate-800 text-sm mb-4">Input Tipe Baru</h3>
                <div class="sk-form-group">
                    <label class="sk-label">Nama Tipe Kontainer</label>
                    <input id="input-tipe-name" type="text" class="sk-input" placeholder="Dry, Reefer, Flat Rack">
                </div>
                <button onclick="submitTipe()" class="sk-btn-primary w-full justify-center"><i class="fas fa-save mr-1.5"></i>Simpan</button>
            </div>
            <div class="lg:col-span-2">
                <input type="text" id="search-tipe" class="sk-input mb-3" placeholder="Cari tipe..." oninput="filterMasterTable('tipe', this.value)">
                <div class="sk-card">
                    <table class="sk-table" id="table-tipe">
                        <thead><tr><th>Nama Tipe</th><th class="text-right w-20">Aksi</th></tr></thead>
                        <tbody>
                        @foreach($tipes as $t)
                        <tr data-search="{{ strtolower($t->nama_tipe) }}">
                            <td class="font-semibold">{{ $t->nama_tipe }}</td>
                            <td class="text-right"><button onclick="deleteMaster('tipe','{{ $t->id_tipe }}','{{ addslashes($t->nama_tipe) }}')" class="text-red-400 hover:text-red-600 transition-colors p-1"><i class="fas fa-trash-alt"></i></button></td>
                        </tr>
                        @endforeach
                        @if($tipes->isEmpty())<tr><td colspan="2" class="py-6 text-center text-gray-400">Tidak ada data</td></tr>@endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Master Ukuran ─── --}}
    <div id="master-ukuran" class="master-sub hidden">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-slate-50 p-5 rounded-xl border border-slate-200">
                <h3 class="font-semibold text-slate-800 text-sm mb-4">Input Ukuran</h3>
                <div class="sk-form-group">
                    <label class="sk-label">Ukuran (Cukup Angka)</label>
                    <input id="input-ukuran-desc" type="text" class="sk-input" placeholder="Ketik 20 atau 40 (auto format ke 20')">
                    <p class="text-xs text-gray-400 mt-1">Sistem otomatis tambahkan petik tunggal (')</p>
                </div>
                <button onclick="submitUkuran()" class="sk-btn-primary w-full justify-center"><i class="fas fa-save mr-1.5"></i>Simpan</button>
            </div>
            <div class="lg:col-span-2">
                <input type="text" id="search-ukuran" class="sk-input mb-3" placeholder="Cari ukuran..." oninput="filterMasterTable('ukuran', this.value)">
                <div class="sk-card">
                    <table class="sk-table" id="table-ukuran">
                        <thead><tr><th>Ukuran</th><th class="text-right w-20">Aksi</th></tr></thead>
                        <tbody>
                        @foreach($ukurans as $u)
                        <tr data-search="{{ strtolower($u->deskripsi_ukuran) }}">
                            <td class="font-mono font-bold text-emerald-800">{{ $u->deskripsi_ukuran }}</td>
                            <td class="text-right"><button onclick="deleteMaster('ukuran','{{ $u->id_ukuran }}','{{ addslashes($u->deskripsi_ukuran) }}')" class="text-red-400 hover:text-red-600 transition-colors p-1"><i class="fas fa-trash-alt"></i></button></td>
                        </tr>
                        @endforeach
                        @if($ukurans->isEmpty())<tr><td colspan="2" class="py-6 text-center text-gray-400">Tidak ada data</td></tr>@endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Master Kontainer ─── --}}
    <div id="master-kontainer" class="master-sub hidden">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-slate-50 p-5 rounded-xl border border-slate-200">
                <h3 class="font-semibold text-slate-800 text-sm mb-4">Daftarkan Kontainer</h3>
                <div class="sk-form-group">
                    <label class="sk-label">Nomor Kontainer (Wajib Unik)</label>
                    <input id="input-kontainer-no" type="text" class="sk-input font-mono uppercase" placeholder="AMFU3153692">
                </div>
                <div class="sk-form-group">
                    <label class="sk-label" id="lbl-kontainer-customer">Customer Terkait</label>
                    <select id="input-kontainer-customer" class="sk-input text-sm">
                        <option value="">— Pilih Customer —</option>
                        @foreach($customers as $c)<option value="{{ $c->id_customer }}">{{ $c->nama_customer }}</option>@endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div class="sk-form-group mb-0">
                        <label class="sk-label">Tipe</label>
                        <select id="input-kontainer-tipe" class="sk-input text-sm">
                            <option value="">— Tipe —</option>
                            @foreach($tipes as $t)<option value="{{ $t->id_tipe }}">{{ $t->nama_tipe }}</option>@endforeach
                        </select>
                    </div>
                    <div class="sk-form-group mb-0">
                        <label class="sk-label">Ukuran</label>
                        <select id="input-kontainer-ukuran" class="sk-input text-sm">
                            <option value="">— Ukuran —</option>
                            @foreach($ukurans as $u)<option value="{{ $u->id_ukuran }}">{{ $u->deskripsi_ukuran }}</option>@endforeach
                        </select>
                    </div>
                </div>
                <button onclick="submitKontainer()" class="sk-btn-primary w-full justify-center"><i class="fas fa-save mr-1.5"></i>Simpan</button>
            </div>
            <div class="lg:col-span-2">
                <input type="text" id="search-kontainer" class="sk-input mb-3" placeholder="Cari No Kontainer / Customer..." oninput="filterMasterTable('kontainer', this.value)">
                <div class="sk-card overflow-x-auto">
                    <table class="sk-table" id="table-kontainer">
                        <thead><tr><th>NO KONTAINER</th><th id="th-kont-customer">CUSTOMER</th><th>TIPE</th><th>UKURAN</th><th>STATUS</th><th class="text-right w-16">AKSI</th></tr></thead>
                        <tbody>
                        @foreach($kontainers as $k)
                        <tr data-search="{{ strtolower($k->no_kontainer . ' ' . ($k->customer->nama_customer ?? '')) }}">
                            <td class="font-mono font-black text-gray-900">{{ $k->no_kontainer }}</td>
                            <td>{{ $k->customer->nama_customer ?? '-' }}</td>
                            <td class="font-medium">{{ $k->tipe->nama_tipe ?? '-' }}</td>
                            <td class="font-mono font-semibold text-emerald-700">{{ $k->ukuran->deskripsi_ukuran ?? '-' }}</td>
                            <td><span class="badge badge-aktif">Aktif</span></td>
                            <td class="text-right"><button onclick="deleteMaster('kontainer','{{ $k->no_kontainer }}','{{ $k->no_kontainer }}')" class="text-red-400 hover:text-red-600 transition-colors p-1"><i class="fas fa-trash-alt"></i></button></td>
                        </tr>
                        @endforeach
                        @if($kontainers->isEmpty())<tr><td colspan="6" class="py-6 text-center text-gray-400">Tidak ada data</td></tr>@endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Master Tarif ─── --}}
    <div id="master-tarif" class="master-sub hidden">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-slate-50 p-5 rounded-xl border border-slate-200">
                <h3 class="font-semibold text-slate-800 text-sm mb-4">Set Master Tarif</h3>
                <div class="sk-form-group">
                    <label class="sk-label" id="lbl-tarif-customer">Customer</label>
                    <select id="input-tarif-customer" class="sk-input text-sm">
                        <option value="">— Pilih Customer —</option>
                        @foreach($customers as $c)<option value="{{ $c->id_customer }}">{{ $c->nama_customer }}</option>@endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="sk-form-group">
                        <label class="sk-label">Tipe</label>
                        <select id="input-tarif-tipe" class="sk-input text-sm">
                            <option value="">— Tipe —</option>
                            @foreach($tipes as $t)<option value="{{ $t->id_tipe }}">{{ $t->nama_tipe }}</option>@endforeach
                        </select>
                    </div>
                    <div class="sk-form-group">
                        <label class="sk-label">Ukuran</label>
                        <select id="input-tarif-ukuran" class="sk-input text-sm">
                            <option value="">— Ukuran —</option>
                            @foreach($ukurans as $u)<option value="{{ $u->id_ukuran }}">{{ $u->deskripsi_ukuran }}</option>@endforeach
                        </select>
                    </div>
                </div>
                <div class="sk-form-group">
                    <label class="sk-label">Tarif Bulanan (Rp)</label>
                    <input id="input-tarif-bulanan" type="number" class="sk-input" placeholder="0">
                </div>
                <div class="sk-form-group">
                    <label class="sk-label">Tarif Harian (Rp)</label>
                    <input id="input-tarif-harian" type="number" class="sk-input" placeholder="0">
                </div>
                <div class="sk-form-group">
                    <label class="sk-label">Berlaku Mulai</label>
                    <input id="input-tarif-mulai" type="date" class="sk-input" value="{{ date('Y-m-d') }}">
                </div>
                <p class="text-xs text-amber-600 mb-3"><i class="fas fa-info-circle mr-1"></i>Tarif aktif sebelumnya akan otomatis ditutup</p>
                <button onclick="submitTarif()" class="sk-btn-primary w-full justify-center"><i class="fas fa-save mr-1.5"></i>Simpan Tarif</button>
            </div>
            <div class="lg:col-span-2">
                <div class="sk-card overflow-x-auto">
                    <table class="sk-table">
                        <thead><tr><th id="th-tarif-customer">Customer</th><th>Tipe</th><th>Ukuran</th><th>Tarif Bulanan</th><th>Tarif Harian</th><th>Mulai</th><th>Akhir</th><th class="text-right w-16">Aksi</th></tr></thead>
                        <tbody>
                        @foreach($tarifs as $trf)
                        <tr class="{{ is_null($trf->tanggal_akhir_berlaku) ? '' : 'opacity-60' }}">
                            <td class="font-semibold">{{ $trf->customer->nama_customer ?? '-' }}</td>
                            <td>{{ $trf->tipe->nama_tipe ?? '-' }}</td>
                            <td class="font-mono text-emerald-700 font-semibold">{{ $trf->ukuran->deskripsi_ukuran ?? '-' }}</td>
                            <td class="font-bold">Rp {{ number_format($trf->tarif_bulanan, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($trf->tarif_harian, 0, ',', '.') }}</td>
                            <td class="text-xs">{{ date('d/m/y', strtotime($trf->tanggal_mulai_berlaku)) }}</td>
                            <td class="text-xs">{{ $trf->tanggal_akhir_berlaku ? date('d/m/y', strtotime($trf->tanggal_akhir_berlaku)) : '<span class="badge badge-aktif">Aktif</span>' }}</td>
                            <td class="text-right"><button onclick="deleteMaster('tarif','{{ $trf->id_tarif }}','tarif ini')" class="text-red-400 hover:text-red-600 transition-colors p-1"><i class="fas fa-trash-alt"></i></button></td>
                        </tr>
                        @endforeach
                        @if($tarifs->isEmpty())<tr><td colspan="8" class="py-6 text-center text-gray-400">Tidak ada data</td></tr>@endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════
     TAB 4: IMPORT / BACKUP
══════════════════════════════════════════════════ --}}
<div id="tab-import" class="sk-tab-content pt-5">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ─── BULK IMPORT PANEL ─── --}}
        <div class="lg:col-span-2 sk-card">
            <div class="sk-card-header">
                <h4 class="font-bold text-gray-800"><i class="fas fa-file-import mr-2 text-indigo-500"></i>Bulk Import (Copy-Paste dari Excel)</h4>
            </div>
            <div class="p-5 space-y-4">
                {{-- Tipe Selector --}}
                <div>
                    <label class="sk-label">Tipe Data yang Di-Import</label>
                    <select id="bulk-import-type" class="sk-input text-sm" onchange="onBulkTypeChange()">
                        <option value="customer">1. Master Customer / Vendor</option>
                        <option value="tipe">2. Master Tipe Kontainer</option>
                        <option value="ukuran">3. Master Ukuran</option>
                        <option value="kontainer">4. Master Kontainer</option>
                        <option value="tarif">5. Master Tarif Sewa</option>
                        <option value="sewa">6. Transaksi Sewa &amp; Kembali</option>
                        <option value="pembayaran">7. Import Pranota/Tagihan</option>
                        <option value="pelunasan">8. Import Pelunasan Massal</option>
                    </select>
                </div>

                {{-- Template info --}}
                <div id="bulk-import-template-info" class="bg-slate-50 border border-slate-200 rounded-lg p-3 text-xs font-mono text-slate-600 space-y-1">
                    <div class="font-bold text-slate-700 mb-1"><i class="fas fa-info-circle mr-1 text-blue-400"></i><span id="bulk-tpl-title">Format:</span></div>
                    <div id="bulk-tpl-body">Satu nama per baris.</div>
                </div>

                {{-- Actions row --}}
                <div class="flex gap-2">
                    <button onclick="loadBulkTemplate()" class="sk-btn-sm sk-btn-ghost"><i class="fas fa-clipboard-list mr-1"></i>Muat Contoh</button>
                    <button onclick="copyBulkTemplate()" class="sk-btn-sm sk-btn-ghost"><i class="fas fa-copy mr-1"></i>Salin Format</button>
                    <button onclick="clearBulkTextarea()" class="sk-btn-sm sk-btn-ghost ml-auto"><i class="fas fa-eraser mr-1"></i>Bersihkan</button>
                </div>

                <textarea id="bulk-import-textarea" rows="10" class="sk-input resize-none font-mono text-xs" placeholder="# Paste data dari Excel di sini..."></textarea>

                {{-- Preview area (for pelunasan 2-stage) --}}
                <div id="bulk-preview-area" class="hidden">
                    <div class="font-semibold text-sm text-slate-700 mb-2"><i class="fas fa-eye mr-1 text-blue-500"></i>Preview Pelunasan</div>
                    <div class="overflow-x-auto">
                        <table class="sk-table text-xs" id="bulk-preview-table">
                            <thead><tr><th>#</th><th>Nomor Nota</th><th>Customer</th><th>No Bukti Bayar</th><th>Tgl Bayar</th><th>Jml Tagihan</th><th>Grand Total</th><th>Valid?</th></tr></thead>
                            <tbody id="bulk-preview-tbody"></tbody>
                        </table>
                    </div>
                    <div class="flex gap-2 mt-3">
                        <button onclick="applyBulkImport()" id="btn-apply-import" class="sk-btn-primary"><i class="fas fa-check mr-1"></i>Terapkan Semua yang Valid</button>
                        <button onclick="hideBulkPreview()" class="sk-btn-sm sk-btn-ghost">Batal</button>
                    </div>
                </div>

                {{-- Process button (for non-pelunasan) --}}
                <button id="btn-bulk-process" onclick="processBulkImport()" class="sk-btn-primary w-full justify-center">
                    <i class="fas fa-upload mr-1.5"></i>Proses Import
                </button>

                {{-- Log result --}}
                <div id="bulk-import-log" class="hidden space-y-1 max-h-60 overflow-y-auto text-xs"></div>
            </div>
        </div>

        {{-- ─── Backup / Restore ─── --}}
        <div class="space-y-5">
            <div class="sk-card">
                <div class="sk-card-header"><h4 class="font-bold text-gray-800"><i class="fas fa-download mr-2 text-green-500"></i>Export Backup JSON</h4></div>
                <div class="p-5">
                    <p class="text-xs text-gray-500 mb-4">Download semua data (Customer, Tipe, Ukuran, Kontainer, Tarif, Sewa, Tagihan, Invoice) sebagai file JSON untuk backup.</p>
                    <a href="{{ route('sewa-kontainer.export.json') }}" class="sk-btn-primary w-full justify-center" target="_blank">
                        <i class="fas fa-file-arrow-down mr-1.5"></i>Download Backup JSON
                    </a>
                </div>
            </div>
            <div class="sk-card">
                <div class="sk-card-header"><h4 class="font-bold text-gray-800"><i class="fas fa-upload mr-2 text-amber-500"></i>Restore dari JSON Backup</h4></div>
                <div class="p-5 space-y-3">
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 text-xs text-amber-700">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        <strong>Peringatan:</strong> Restore akan menghapus &amp; mengganti SEMUA data yang ada sekarang.
                    </div>
                    <input type="file" id="backup-file-input" accept=".json" class="sk-input text-xs">
                    <button onclick="submitRestoreJson()" class="w-full py-2 px-4 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-sm font-bold transition-all">
                        <i class="fas fa-rotate-right mr-1.5"></i>Restore Data
                    </button>
                </div>
            </div>
            <div class="sk-card">
                <div class="sk-card-header"><h4 class="font-bold text-gray-800"><i class="fas fa-trash-can mr-2 text-red-500"></i>Bersihkan Semua Data</h4></div>
                <div class="p-5 space-y-3">
                    <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-xs text-red-700">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        <strong>Peringatan:</strong> Tindakan ini akan menghapus permanen SEMUA data (Customer, Tipe, Ukuran, Kontainer, Tarif, Sewa, Tagihan, Invoice).
                    </div>
                    <button onclick="wipeAllData()" class="w-full py-2 px-4 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-bold transition-all">
                        <i class="fas fa-trash-alt mr-1.5"></i>Bersihkan Semua Data
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

</div>{{-- end tabs --}}
</div>{{-- end space-y-5 --}}

{{-- ══════════════════════════════════════════════════
     MODALS
══════════════════════════════════════════════════ --}}

{{-- Modal: Edit Tagihan --}}
<div id="modal-tagihan" class="sk-modal-overlay">
    <div class="sk-modal sk-modal-lg">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-bold text-gray-800">Edit Tagihan Periodik</h3>
            <button onclick="closeModal('modal-tagihan')" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-lg"></i></button>
        </div>
        <input type="hidden" id="edit-tagihan-id">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="sk-form-group">
                <label class="sk-label">Status Bayar</label>
                <select id="edit-status-bayar" class="sk-input">
                    <option value="Belum Ditagih">Belum Ditagih</option>
                    <option value="Pranota">Pranota (Draft)</option>
                    <option value="Belum Bayar">Belum Bayar</option>
                    <option value="Lunas">Lunas</option>
                </select>
            </div>
            <div class="sk-form-group">
                <label class="sk-label">No Invoice Grup</label>
                <input id="edit-nomor-invoice" type="text" class="sk-input font-mono" placeholder="INV-202506-01">
            </div>
            <div class="sk-form-group">
                <label class="sk-label">Tanggal Tagihan</label>
                <input id="edit-tgl-tagihan" type="date" class="sk-input">
            </div>
            <div class="sk-form-group">
                <label class="sk-label">Tanggal Bayar</label>
                <input id="edit-tgl-bayar" type="date" class="sk-input">
            </div>
            <div class="sk-form-group">
                <label class="sk-label">Jumlah Override (Rp)</label>
                <input id="edit-override" type="number" class="sk-input" placeholder="Kosong = gunakan estimasi">
            </div>
            <div class="sk-form-group">
                <label class="sk-label">Nomor Bukti Bayar</label>
                <input id="edit-nomor-bayar" type="text" class="sk-input font-mono" placeholder="TRF-001">
            </div>
            <div class="sk-form-group">
                <label class="sk-label">PPN 11% (Rp, auto-calc)</label>
                <input id="edit-ppn" type="number" class="sk-input">
            </div>
            <div class="sk-form-group">
                <label class="sk-label">PPh 2% (Rp, auto-calc)</label>
                <input id="edit-pph" type="number" class="sk-input">
            </div>
            <div class="md:col-span-2 sk-form-group">
                <label class="sk-label">Keterangan Selisih</label>
                <input id="edit-keterangan-selisih" type="text" class="sk-input" placeholder="Keterangan jika ada selisih...">
            </div>
        </div>
        <div class="flex gap-3 mt-5">
            <button onclick="submitTagihanEdit()" class="sk-btn-primary flex-1 justify-center"><i class="fas fa-save mr-1.5"></i>Simpan Perubahan</button>
            <button onclick="closeModal('modal-tagihan')" class="sk-btn-sm sk-btn-ghost px-6 py-2">Batal</button>
        </div>
    </div>
</div>

{{-- Modal: Buat Invoice Grup --}}
<div id="modal-invoice" class="sk-modal-overlay">
    <div class="sk-modal">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-bold text-gray-800">Buat Invoice Grup</h3>
            <button onclick="closeModal('modal-invoice')" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-lg"></i></button>
        </div>
        <div id="invoice-selection-info" class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-xs text-blue-800 mb-4"></div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="sk-form-group">
                <label class="sk-label">Nomor Invoice</label>
                <input id="inv-nomor" type="text" class="sk-input font-mono" placeholder="INV-202506-01">
            </div>
            <div class="sk-form-group">
                <label class="sk-label">Customer</label>
                <select id="inv-customer" class="sk-input text-sm">
                    @foreach($customers as $c)<option value="{{ $c->id_customer }}">{{ $c->nama_customer }}</option>@endforeach
                </select>
            </div>
            <div class="sk-form-group">
                <label class="sk-label">Tanggal Invoice</label>
                <input id="inv-tanggal" type="date" class="sk-input" value="{{ date('Y-m-d') }}">
            </div>
            <div class="sk-form-group">
                <label class="sk-label">Status</label>
                <select id="inv-status" class="sk-input text-sm">
                    <option value="Belum Bayar">Belum Bayar</option>
                    <option value="Lunas">Lunas</option>
                </select>
            </div>
            <div class="sk-form-group">
                <label class="sk-label">Adjustment Biaya (+ / -)</label>
                <input id="inv-adjustment" type="number" class="sk-input" placeholder="0">
            </div>
            <div class="sk-form-group">
                <label class="sk-label">Keterangan Adjustment</label>
                <input id="inv-adj-ket" type="text" class="sk-input" placeholder="Biaya admin, diskon, dll">
            </div>
            <div class="md:col-span-2 sk-form-group">
                <label class="sk-label">Deskripsi Invoice</label>
                <input id="inv-deskripsi" type="text" class="sk-input" placeholder="Tagihan Sewa Kontainer Bulan...">
            </div>
        </div>
        <div class="flex gap-3 mt-5">
            <button onclick="submitNewInvoice()" class="sk-btn-primary flex-1 justify-center"><i class="fas fa-file-invoice mr-1.5"></i>Buat Invoice</button>
            <button onclick="closeModal('modal-invoice')" class="sk-btn-sm sk-btn-ghost px-6 py-2">Batal</button>
        </div>
    </div>
</div>

{{-- Modal: Edit Invoice --}}
<div id="modal-edit-invoice" class="sk-modal-overlay">
    <div class="sk-modal">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-bold text-gray-800">Edit Invoice Grup</h3>
            <button onclick="closeModal('modal-edit-invoice')" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-lg"></i></button>
        </div>
        <input type="hidden" id="edit-invoice-nomor">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="sk-form-group">
                <label class="sk-label">Status Pembayaran</label>
                <select id="edit-inv-status" class="sk-input text-sm">
                    <option value="Belum Bayar">Belum Bayar</option>
                    <option value="Lunas">Lunas</option>
                </select>
            </div>
            <div class="sk-form-group">
                <label class="sk-label">Tanggal Bayar</label>
                <input id="edit-inv-tgl-bayar" type="date" class="sk-input">
            </div>
            <div class="sk-form-group">
                <label class="sk-label">Nomor Bayar</label>
                <input id="edit-inv-nomor-bayar" type="text" class="sk-input font-mono" placeholder="TRF-001">
            </div>
            <div class="sk-form-group">
                <label class="sk-label">Adjustment Biaya</label>
                <input id="edit-inv-adjustment" type="number" class="sk-input" placeholder="0">
            </div>
            <div class="md:col-span-2 sk-form-group">
                <label class="sk-label">Keterangan Adjustment</label>
                <input id="edit-inv-adj-ket" type="text" class="sk-input">
            </div>
        </div>
        <div class="flex gap-3 mt-5">
            <button onclick="submitEditInvoice()" class="sk-btn-primary flex-1 justify-center"><i class="fas fa-save mr-1.5"></i>Simpan</button>
            <button onclick="closeModal('modal-edit-invoice')" class="sk-btn-sm sk-btn-ghost px-6 py-2">Batal</button>
        </div>
    </div>
</div>

{{-- Modal: Edit Sewa --}}
<div id="modal-edit-sewa" class="sk-modal-overlay">
    <div class="sk-modal">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-bold text-gray-800">Edit Transaksi Sewa</h3>
            <button onclick="closeModal('modal-edit-sewa')" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-lg"></i></button>
        </div>
        <input type="hidden" id="edit-sewa-id">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="sk-form-group">
                <label class="sk-label">Tanggal Sewa</label>
                <input id="edit-sewa-tanggal" type="date" class="sk-input">
            </div>
            <div class="sk-form-group">
                <label class="sk-label">Tanggal Kembali (isi untuk selesai)</label>
                <input id="edit-sewa-kembali" type="date" class="sk-input">
            </div>
            <div class="sk-form-group">
                <label class="sk-label">Jenis Tarif</label>
                <select id="edit-sewa-jenis" class="sk-input text-sm">
                    <option value="Bulanan">Bulanan</option>
                    <option value="Harian">Harian</option>
                </select>
            </div>
            <div class="sk-form-group">
                <label class="sk-label">Tarif Bulanan (Rp)</label>
                <input id="edit-sewa-bulanan" type="number" class="sk-input">
            </div>
            <div class="sk-form-group">
                <label class="sk-label">Tarif Harian (Rp)</label>
                <input id="edit-sewa-harian" type="number" class="sk-input">
            </div>
            <div class="sk-form-group">
                <label class="sk-label">Catatan</label>
                <input id="edit-sewa-catatan" type="text" class="sk-input">
            </div>
        </div>
        <div class="flex gap-3 mt-5">
            <button onclick="submitEditSewa()" class="sk-btn-primary flex-1 justify-center"><i class="fas fa-save mr-1.5"></i>Simpan</button>
            <button onclick="closeModal('modal-edit-sewa')" class="sk-btn-sm sk-btn-ghost px-6 py-2">Batal</button>
        </div>
    </div>
</div>

{{-- Modal: Terminate Sewa --}}
<div id="modal-terminate" class="sk-modal-overlay">
    <div class="sk-modal">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-bold text-gray-800">Kembalikan Kontainer</h3>
            <button onclick="closeModal('modal-terminate')" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-lg"></i></button>
        </div>
        <input type="hidden" id="terminate-sewa-id">
        <div class="sk-form-group">
            <label class="sk-label">Tanggal Kembali</label>
            <input id="terminate-tgl" type="date" class="sk-input" value="{{ date('Y-m-d') }}">
        </div>
        <p class="text-xs text-amber-600 mt-2"><i class="fas fa-info-circle mr-1"></i>Status sewa akan berubah menjadi Selesai dan periode akhir akan di-prorate.</p>
        <div class="flex gap-3 mt-5">
            <button onclick="submitTerminate()" class="w-full py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-sm font-bold"><i class="fas fa-calendar-check mr-1.5"></i>Konfirmasi Kembalikan</button>
            <button onclick="closeModal('modal-terminate')" class="sk-btn-sm sk-btn-ghost px-6 py-2">Batal</button>
        </div>
    </div>
</div>

{{-- Modal: Print Invoice --}}
<div id="modal-print-invoice" class="sk-modal-overlay">
    <div class="sk-modal sk-modal-lg no-print">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-gray-800" id="print-inv-nomor">Invoice</h3>
            <div class="flex gap-2">
                <button onclick="doPrintInvoice()" class="sk-btn-sm" style="background:#0369a1;color:white"><i class="fas fa-print mr-1"></i>Cetak</button>
                <button onclick="closeModal('modal-print-invoice')" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-lg"></i></button>
            </div>
        </div>
        <div id="print-invoice-body" class="print-area">
            <!-- filled by JS -->
        </div>
    </div>
</div>

<script>
// ══════════════════════════════════════════════════
// CONSTANTS & STATE
// ══════════════════════════════════════════════════
const ROUTES = {
    customer:   { store: '{{ route("sewa-kontainer.customer.store") }}',  del: '{{ url("master/sewa-kontainer/master/customer") }}/' },
    tipe:       { store: '{{ route("sewa-kontainer.tipe.store") }}',      del: '{{ url("master/sewa-kontainer/master/tipe") }}/' },
    ukuran:     { store: '{{ route("sewa-kontainer.ukuran.store") }}',    del: '{{ url("master/sewa-kontainer/master/ukuran") }}/' },
    kontainer:  { store: '{{ route("sewa-kontainer.kontainer.store") }}', del: '{{ url("master/sewa-kontainer/master/kontainer") }}/' },
    tarif:      { store: '{{ route("sewa-kontainer.tarif.store") }}',     del: '{{ url("master/sewa-kontainer/master/tarif") }}/' },
    sewa:       { store: '{{ route("sewa-kontainer.sewa.store") }}',      base: '{{ url("master/sewa-kontainer/sewa") }}/' },
    tagihan:    { base: '{{ url("master/sewa-kontainer/tagihan") }}/' },
    invoice:    { store: '{{ route("sewa-kontainer.invoice.store") }}',   base: '{{ url("master/sewa-kontainer/invoice") }}/' },
    importPayment: '{{ route("sewa-kontainer.import.payment") }}',
    bulkImport:    '{{ route("sewa-kontainer.bulk.import") }}',
    importPreview: '{{ route("sewa-kontainer.import.preview") }}',
    importJson:    '{{ route("sewa-kontainer.import.json") }}',
    wipeData:      '{{ route("sewa-kontainer.wipe.data") }}',
    kontainerInfo: '{{ url("master/sewa-kontainer/kontainer-info") }}/',
};
const CSRF = '{{ csrf_token() }}';
let appMode = 'out'; // 'out' | 'in'
let selectedTagihanIds = [];
let billingPage = 1;
const BILLING_PAGE_SIZE = 20;
let billingSort = { col: 'bulan_ke', dir: 'asc' };
let allBillingRows = [];
let filteredBillingRows = [];

// Sewa pagination state
let sewaPage = 1;
const SEWA_PAGE_SIZE = 20;
let allSewaCards = [];
let filteredSewaCards = [];

// ══════════════════════════════════════════════════
// UTILITIES
// ══════════════════════════════════════════════════
function showNotif(msg, type = 'success') {
    const el = document.getElementById('sk-notification');
    el.className = 'sk-notification ' + type;
    el.innerHTML = `<i class="fas fa-${type==='success'?'check-circle':'exclamation-circle'}"></i> ${msg}`;
    el.style.display = 'flex';
    setTimeout(() => el.style.display = 'none', 4500);
}

async function apiPost(url, data = {}, method = 'POST') {
    try {
        const res = await fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify(data),
        });
        const json = await res.json();
        if (!res.ok || !json.success) {
            throw new Error(json.message || json.errors ? Object.values(json.errors || {})[0] : 'Terjadi kesalahan');
        }
        return json;
    } catch(e) {
        showNotif(e.message || 'Error jaringan', 'error');
        throw e;
    }
}

async function apiDelete(url) {
    return apiPost(url, {}, 'DELETE');
}

function openModal(id)  { document.getElementById(id).classList.add('active'); }
function closeModal(id) { document.getElementById(id).classList.remove('active'); }
window.addEventListener('click', e => { if(e.target.classList.contains('sk-modal-overlay')) closeModal(e.target.id); });

// ══════════════════════════════════════════════════
// CLOCK
// ══════════════════════════════════════════════════
function updateClock() {
    const now = new Date();
    const wib = new Date(now.toLocaleString('en-US', { timeZone: 'Asia/Jakarta' }));
    const pad = n => String(n).padStart(2, '0');
    document.getElementById('sk-clock').textContent =
        `${pad(wib.getDate())}/${pad(wib.getMonth()+1)}/${wib.getFullYear()} ${pad(wib.getHours())}:${pad(wib.getMinutes())}:${pad(wib.getSeconds())} WIB`;
}
updateClock(); setInterval(updateClock, 1000);

// ══════════════════════════════════════════════════
// MODE TOGGLE (Sewa Out / Sewa In)
// ══════════════════════════════════════════════════
function setMode(mode) {
    appMode = mode;
    const isIn = mode === 'in';
    document.body.classList.toggle('mode-sewa-in', isIn);
    
    const btnOut = document.getElementById('btn-mode-out');
    const btnIn  = document.getElementById('btn-mode-in');
    if (isIn) {
        btnOut.className = 'px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all duration-300 flex items-center gap-1.5 cursor-pointer select-none text-emerald-300 hover:text-white';
        btnIn.className  = 'px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all duration-300 flex items-center gap-1.5 cursor-pointer select-none bg-indigo-600 text-white shadow-sm font-extrabold';
        document.getElementById('header-badge-text').textContent = 'Pihak Penyewa (Sewa In)';
        document.getElementById('kpi-aktif-label').textContent = 'Sewa Disewa Aktif';
        document.getElementById('kpi-belum-label').textContent = 'Total Tagihan Diterima';
        document.getElementById('kpi-lunas-label').textContent = 'Total Pembayaran Keluar';
    } else {
        btnOut.className = 'px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all duration-300 flex items-center gap-1.5 cursor-pointer select-none bg-emerald-600 text-white shadow-sm font-extrabold';
        btnIn.className  = 'px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all duration-300 flex items-center gap-1.5 cursor-pointer select-none text-emerald-300 hover:text-white';
        document.getElementById('header-badge-text').textContent = 'Pihak Pemilik (Sewa Out)';
        document.getElementById('kpi-aktif-label').textContent = 'Sewa Berjalan Aktif';
        document.getElementById('kpi-belum-label').textContent = 'Total Belum Tertagih/Bayar';
        document.getElementById('kpi-lunas-label').textContent = 'Pendapatan Diterima (Lunas)';
    }

    const custLabel = isIn ? 'Vendor / Owner' : 'Customer';
    const custLabels = ['lbl-filter-customer','th-customer','th-customer-coll','lbl-sewa-customer','lbl-master-customer','lbl-form-customer','lbl-input-customer','lbl-kontainer-customer','lbl-tarif-customer','th-cust','th-kont-customer','th-tarif-customer'];
    custLabels.forEach(id => { const el = document.getElementById(id); if(el) el.textContent = custLabel; });
    
    const masterLabel = document.getElementById('lbl-master-customer');
    if (masterLabel) masterLabel.textContent = `1. ${custLabel}`;
}

// ══════════════════════════════════════════════════
// TAB NAVIGATION
// ══════════════════════════════════════════════════
function switchMainTab(name, btn) {
    document.querySelectorAll('.sk-tab-content').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.sk-main-tab').forEach(el => el.classList.remove('active'));
    document.getElementById('tab-' + name).classList.add('active');
    btn.classList.add('active');
}

function switchBillingTab(name, btn) {
    document.querySelectorAll('.billing-sub').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.sk-sub-tab').forEach(el => el.classList.remove('active'));
    document.getElementById('billing-' + name).classList.remove('hidden');
    btn.classList.add('active');
    if (name === 'sheet') initBillingTable();
}

function switchMasterTab(name, btn) {
    document.querySelectorAll('.master-sub').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('#tab-master .sk-sub-tab').forEach(el => el.classList.remove('active'));
    document.getElementById('master-' + name).classList.remove('hidden');
    btn.classList.add('active');
}

// ══════════════════════════════════════════════════
// BILLING TABLE — Filter, Sort, Paginate
// ══════════════════════════════════════════════════
function initBillingTable() {
    allBillingRows = Array.from(document.querySelectorAll('.billing-row'));
    applyFilters();
}

function applyFilters() {
    const custFilter    = document.getElementById('filter-customer').value;
    const statusFilter  = document.getElementById('filter-status').value;
    const kontFilter    = document.getElementById('filter-kontainer').value.toLowerCase();
    const sewaFilter    = document.getElementById('filter-rentang-sewa') ? document.getElementById('filter-rentang-sewa').value : '';

    filteredBillingRows = allBillingRows.filter(row => {
        if (custFilter   && row.dataset.customer !== custFilter) return false;
        if (statusFilter && row.dataset.status   !== statusFilter) return false;
        if (kontFilter   && !row.dataset.kontainer.toLowerCase().includes(kontFilter)) return false;
        if (sewaFilter   && row.dataset.idSewa   !== sewaFilter) return false;
        return true;
    });

    sortFilteredRows();
    billingPage = 1;
    renderBillingPage();
}

function sortTable(col) {
    if (billingSort.col === col) billingSort.dir = billingSort.dir === 'asc' ? 'desc' : 'asc';
    else { billingSort.col = col; billingSort.dir = 'asc'; }
    
    // Update DOM sort indicators
    document.querySelectorAll('th[id^="th-sort-"]').forEach(th => {
        th.classList.remove('sort-asc', 'sort-desc');
        th.querySelector('.sort-icon').textContent = '↕';
    });
    
    const th = document.getElementById('th-sort-' + (col === 'id_tagihan' ? 'id' : (col === 'bulan_ke' ? 'bulan' : (col === 'jumlah_tagihan' ? 'jumlah' : col))));
    if (th) {
        th.classList.add('sort-' + billingSort.dir);
        th.querySelector('.sort-icon').textContent = billingSort.dir === 'asc' ? '▲' : '▼';
    }
    
    sortFilteredRows();
    renderBillingPage();
}

function sortFilteredRows() {
    filteredBillingRows.sort((a, b) => {
        let aVal, bVal;
        if (billingSort.col === 'jumlah_tagihan') { aVal = parseFloat(a.dataset.jumlah); bVal = parseFloat(b.dataset.jumlah); }
        else if (billingSort.col === 'bulan_ke')  { aVal = parseInt(a.dataset.bulanKe);  bVal = parseInt(b.dataset.bulanKe); }
        else if (billingSort.col === 'customer')  { aVal = a.dataset.customer; bVal = b.dataset.customer; }
        else if (billingSort.col === 'status_bayar') { aVal = a.dataset.status; bVal = b.dataset.status; }
        else { aVal = a.dataset.id; bVal = b.dataset.id; }
        if (aVal < bVal) return billingSort.dir === 'asc' ? -1 : 1;
        if (aVal > bVal) return billingSort.dir === 'asc' ? 1 : -1;
        return 0;
    });
}

function renderBillingPage() {
    const tbody  = document.getElementById('billing-tbody');
    const total  = filteredBillingRows.length;
    const pages  = Math.max(1, Math.ceil(total / BILLING_PAGE_SIZE));
    billingPage  = Math.min(billingPage, pages);
    const start  = (billingPage - 1) * BILLING_PAGE_SIZE;
    const slice  = filteredBillingRows.slice(start, start + BILLING_PAGE_SIZE);

    // Show/hide rows
    allBillingRows.forEach(row => { row.style.display = 'none'; });
    slice.forEach(row => { row.style.display = ''; });

    document.getElementById('billing-count').textContent = total;
    renderPagination(pages);
}

function renderPagination(pages) {
    const pg = document.getElementById('billing-pagination');
    pg.innerHTML = '';
    if (pages <= 1) return;

    const makeBtn = (label, page, disabled = false, active = false) => {
        const b = document.createElement('button');
        b.className = 'sk-page-btn' + (active ? ' active' : '');
        b.textContent = label;
        b.disabled = disabled;
        b.onclick = () => { billingPage = page; renderBillingPage(); };
        return b;
    };

    pg.appendChild(makeBtn('«', 1, billingPage === 1));
    pg.appendChild(makeBtn('‹', billingPage - 1, billingPage === 1));

    const range = [billingPage - 1, billingPage, billingPage + 1].filter(p => p >= 1 && p <= pages);
    range.forEach(p => pg.appendChild(makeBtn(p, p, false, p === billingPage)));

    pg.appendChild(makeBtn('›', billingPage + 1, billingPage === pages));
    pg.appendChild(makeBtn('»', pages, billingPage === pages));
}

function clearFilters() {
    document.getElementById('filter-customer').value  = '';
    document.getElementById('filter-status').value    = '';
    document.getElementById('filter-kontainer').value = '';
    applyFilters();
}

// ══════════════════════════════════════════════════
// CHECKBOX SELECTION
// ══════════════════════════════════════════════════
function toggleAllCheckboxes(master) {
    const visible = Array.from(document.querySelectorAll('.billing-row')).filter(r => r.style.display !== 'none');
    visible.forEach(r => r.querySelector('.row-chk').checked = master.checked);
    onRowCheck();
}

function selectAllVisible() {
    const visible = Array.from(document.querySelectorAll('.billing-row')).filter(r => r.style.display !== 'none');
    visible.forEach(r => r.querySelector('.row-chk').checked = true);
    onRowCheck();
}

function clearSelection() {
    document.querySelectorAll('.row-chk').forEach(c => c.checked = false);
    document.getElementById('chk-all').checked = false;
    onRowCheck();
}

function onRowCheck() {
    selectedTagihanIds = Array.from(document.querySelectorAll('.row-chk:checked')).map(c => c.value);
    const bulk = document.getElementById('bulk-actions');
    const count = document.getElementById('bulk-count');
    if (selectedTagihanIds.length > 0) {
        bulk.classList.remove('hidden');
        count.textContent = selectedTagihanIds.length + ' dipilih';
    } else {
        bulk.classList.add('hidden');
    }
}

// ══════════════════════════════════════════════════
// INLINE EDIT
// ══════════════════════════════════════════════════
function inlineEdit(el, tagihanId, field) {
    const oldText = el.textContent.trim();
    const input = document.createElement('input');
    input.type  = field.includes('tgl') || field.includes('tanggal') ? 'date' : 'text';
    input.className = 'sk-input py-0.5 text-xs w-32';
    input.value = oldText === '—' ? '' : oldText.replace(/[Rp\s\.]/g, '');
    el.replaceWith(input);
    input.focus();

    const save = async () => {
        try {
            await apiPost(ROUTES.tagihan.base + tagihanId + '/update', { [field]: input.value || null });
            showNotif('Tersimpan!');
            setTimeout(() => location.reload(), 800);
        } catch (e) {
            const span = document.createElement('span');
            span.className = 'editable-cell text-xs';
            span.textContent = oldText;
            span.onclick = () => inlineEdit(span, tagihanId, field);
            input.replaceWith(span);
        }
    };
    input.addEventListener('blur', save);
    input.addEventListener('keydown', e => { if(e.key === 'Enter') save(); if(e.key === 'Escape') { input.replaceWith(el); } });
}

// ══════════════════════════════════════════════════
// TAGIHAN MODAL
// ══════════════════════════════════════════════════
function openTagihanModal(id) {
    document.getElementById('edit-tagihan-id').value = id;
    const row = document.querySelector(`.billing-row[data-id="${id}"]`);
    if (row) {
        const cells = row.querySelectorAll('td');
        // Pre-fill from data attributes (rough)
        document.getElementById('edit-status-bayar').value = row.dataset.status;
    }
    openModal('modal-tagihan');
}

async function submitTagihanEdit() {
    const id = document.getElementById('edit-tagihan-id').value;
    const data = {
        status_bayar:            document.getElementById('edit-status-bayar').value,
        nomor_invoice_grup:      document.getElementById('edit-nomor-invoice').value || null,
        tanggal_tagihan:         document.getElementById('edit-tgl-tagihan').value || null,
        tanggal_bayar:           document.getElementById('edit-tgl-bayar').value || null,
        jumlah_tagihan_override: document.getElementById('edit-override').value || null,
        nomor_bayar:             document.getElementById('edit-nomor-bayar').value || null,
        ppn:                     document.getElementById('edit-ppn').value || null,
        pph:                     document.getElementById('edit-pph').value || null,
        keterangan_selisih:      document.getElementById('edit-keterangan-selisih').value || null,
    };
    await apiPost(ROUTES.tagihan.base + id + '/update', data);
    showNotif('Tagihan berhasil diupdate!');
    closeModal('modal-tagihan');
    setTimeout(() => location.reload(), 800);
}

// Auto-calc PPN/PPh when override changes
document.getElementById('edit-override').addEventListener('input', function() {
    const ov = parseFloat(this.value) || 0;
    document.getElementById('edit-ppn').value = ov > 0 ? Math.round(ov * 0.11) : '';
    document.getElementById('edit-pph').value = ov > 0 ? Math.round(ov * 0.02) : '';
});

// ══════════════════════════════════════════════════
// INVOICE MODAL
// ══════════════════════════════════════════════════
function openInvoiceModal() {
    const count = selectedTagihanIds.length;
    if (count === 0) { showNotif('Pilih minimal 1 tagihan terlebih dahulu', 'error'); return; }
    document.getElementById('invoice-selection-info').innerHTML =
        `<i class="fas fa-info-circle mr-1"></i> <strong>${count}</strong> tagihan dipilih akan dimasukkan ke invoice ini.`;
    const now = new Date();
    const ym  = `${now.getFullYear()}${String(now.getMonth()+1).padStart(2,'0')}`;
    document.getElementById('inv-nomor').value = `INV-${ym}-01`;
    openModal('modal-invoice');
}

async function submitNewInvoice() {
    const data = {
        nomor_invoice:         document.getElementById('inv-nomor').value,
        id_customer:           document.getElementById('inv-customer').value,
        tanggal_invoice:       document.getElementById('inv-tanggal').value,
        status_pembayaran:     document.getElementById('inv-status').value,
        adjustment_biaya:      parseFloat(document.getElementById('inv-adjustment').value) || 0,
        adjustment_keterangan: document.getElementById('inv-adj-ket').value || null,
        deskripsi:             document.getElementById('inv-deskripsi').value || null,
        list_id_tagihan:       selectedTagihanIds,
    };
    await apiPost(ROUTES.invoice.store, data);
    showNotif('Invoice berhasil dibuat!');
    closeModal('modal-invoice');
    setTimeout(() => location.reload(), 800);
}

function openEditInvoiceModal(nomor, deskripsi, adjustment, adjKet) {
    document.getElementById('edit-invoice-nomor').value    = nomor;
    document.getElementById('edit-inv-adjustment').value   = adjustment;
    document.getElementById('edit-inv-adj-ket').value      = adjKet;
    openModal('modal-edit-invoice');
}

async function submitEditInvoice() {
    const nomor = document.getElementById('edit-invoice-nomor').value;
    const data  = {
        status_pembayaran:     document.getElementById('edit-inv-status').value,
        tanggal_bayar:         document.getElementById('edit-inv-tgl-bayar').value || null,
        nomor_bayar:           document.getElementById('edit-inv-nomor-bayar').value || null,
        adjustment_biaya:      parseFloat(document.getElementById('edit-inv-adjustment').value) || 0,
        adjustment_keterangan: document.getElementById('edit-inv-adj-ket').value || null,
    };
    await apiPost(ROUTES.invoice.base + nomor, data, 'PUT');
    showNotif('Invoice berhasil diupdate!');
    closeModal('modal-edit-invoice');
    setTimeout(() => location.reload(), 800);
}

async function deleteInvoice(nomor) {
    if (!confirm(`Hapus invoice ${nomor}? Tagihan yang terkait akan kembali ke status Belum Ditagih.`)) return;
    await apiDelete(ROUTES.invoice.base + nomor);
    showNotif('Invoice dihapus!');
    setTimeout(() => location.reload(), 800);
}

async function quickLunasInvoice(nomor) {
    if (!confirm(`Tandai invoice ${nomor} sebagai Lunas?`)) return;
    await apiPost(ROUTES.invoice.base + nomor, { status_pembayaran: 'Lunas' }, 'PUT');
    showNotif('Invoice ditandai Lunas!');
    setTimeout(() => location.reload(), 800);
}

// ══════════════════════════════════════════════════
// BULK STATUS UPDATE
// ══════════════════════════════════════════════════
async function bulkSetStatus(status) {
    if (selectedTagihanIds.length === 0) return;
    for (const id of selectedTagihanIds) {
        await apiPost(ROUTES.tagihan.base + id + '/update', { status_bayar: status });
    }
    showNotif(`${selectedTagihanIds.length} tagihan diubah ke ${status}`);
    setTimeout(() => location.reload(), 800);
}

// ══════════════════════════════════════════════════
// SEWA FORM
// ══════════════════════════════════════════════════
function toggleNewSewaForm() {
    const form = document.getElementById('new-sewa-form');
    form.classList.toggle('hidden');
}

async function onKontainerSelect(noKontainer) {
    if (!noKontainer) return;
    try {
        const res = await fetch(ROUTES.kontainerInfo + noKontainer, { headers: { Accept: 'application/json' } });
        const json = await res.json();
        const info = document.getElementById('sewa-tarif-info');
        if (json.kontainer) {
            // Auto-set customer
            document.getElementById('sewa-id-customer').value = json.kontainer.id_customer;
        }
        if (json.activeTarif) {
            const t = json.activeTarif;
            info.classList.remove('hidden');
            info.innerHTML = `<i class="fas fa-tag mr-1 text-blue-500"></i>Tarif aktif: <strong>Rp ${Number(t.tarif_bulanan).toLocaleString('id-ID')}/bln</strong>, <strong>Rp ${Number(t.tarif_harian).toLocaleString('id-ID')}/hari</strong>`;
            document.getElementById('sewa-tarif-bulanan').value = t.tarif_bulanan;
            document.getElementById('sewa-tarif-harian').value  = t.tarif_harian;
        } else {
            info.classList.remove('hidden');
            info.innerHTML = '<i class="fas fa-exclamation-triangle mr-1 text-amber-500"></i>Tidak ada tarif aktif untuk kombinasi ini. Isi manual.';
        }
    } catch(e) {}
}

async function submitNewSewa() {
    const data = {
        no_kontainer:  document.getElementById('sewa-no-kontainer').value,
        id_customer:   document.getElementById('sewa-id-customer').value,
        tanggal_sewa:  document.getElementById('sewa-tanggal-sewa').value,
        jenis_tarif:   document.getElementById('sewa-jenis-tarif').value,
        tarif_bulanan: parseFloat(document.getElementById('sewa-tarif-bulanan').value) || 0,
        tarif_harian:  parseFloat(document.getElementById('sewa-tarif-harian').value) || 0,
        catatan:       document.getElementById('sewa-catatan').value || null,
    };
    await apiPost(ROUTES.sewa.store, data);
    showNotif('Kontrak sewa berhasil dibuat! Periode tagihan di-generate otomatis.');
    setTimeout(() => location.reload(), 1000);
}

function openEditSewaModal(id) {
    // Find card data — we pass id to the modal
    document.getElementById('edit-sewa-id').value = id;
    openModal('modal-edit-sewa');
}

async function submitEditSewa() {
    const id   = document.getElementById('edit-sewa-id').value;
    const data = {
        tanggal_sewa:    document.getElementById('edit-sewa-tanggal').value,
        tanggal_kembali: document.getElementById('edit-sewa-kembali').value || null,
        jenis_tarif:     document.getElementById('edit-sewa-jenis').value,
        tarif_bulanan:   parseFloat(document.getElementById('edit-sewa-bulanan').value) || 0,
        tarif_harian:    parseFloat(document.getElementById('edit-sewa-harian').value) || 0,
        catatan:         document.getElementById('edit-sewa-catatan').value || null,
    };
    await apiPost(ROUTES.sewa.base + id, data, 'PUT');
    showNotif('Sewa berhasil diupdate!');
    closeModal('modal-edit-sewa');
    setTimeout(() => location.reload(), 800);
}

function openTerminateModal(id) {
    document.getElementById('terminate-sewa-id').value = id;
    openModal('modal-terminate');
}

async function submitTerminate() {
    const id  = document.getElementById('terminate-sewa-id').value;
    const tgl = document.getElementById('terminate-tgl').value;
    await apiPost(ROUTES.sewa.base + id + '/terminate', { tanggal_kembali: tgl });
    showNotif('Kontainer berhasil dikembalikan!');
    closeModal('modal-terminate');
    setTimeout(() => location.reload(), 800);
}

async function deleteSewa(id) {
    if (!confirm('Hapus transaksi sewa ini beserta semua periode tagihan terkait?')) return;
    await apiDelete(ROUTES.sewa.base + id);
    showNotif('Transaksi sewa dihapus!');
    setTimeout(() => location.reload(), 800);
}

// ══════════════════════════════════════════════════
// FILTER CONTRACTS
// ══════════════════════════════════════════════════
function filterContracts() {
    const q   = document.getElementById('contracts-search').value.toLowerCase();
    const st  = document.getElementById('contracts-status-filter').value;
    document.querySelectorAll('.sewa-card').forEach(card => {
        const matchQ  = !q  || card.dataset.search.includes(q);
        const matchSt = !st || card.dataset.status === st;
        card.style.display = (matchQ && matchSt) ? '' : 'none';
    });
}

// ══════════════════════════════════════════════════
// MASTER CRUD
// ══════════════════════════════════════════════════
async function submitCustomer() {
    const name = document.getElementById('input-customer-name').value.trim();
    if (!name) { showNotif('Nama customer wajib diisi', 'error'); return; }
    await apiPost(ROUTES.customer.store, { nama_customer: name });
    showNotif('Customer berhasil ditambahkan!');
    setTimeout(() => location.reload(), 800);
}

async function submitTipe() {
    const name = document.getElementById('input-tipe-name').value.trim();
    if (!name) { showNotif('Nama tipe wajib diisi', 'error'); return; }
    await apiPost(ROUTES.tipe.store, { nama_tipe: name });
    showNotif('Tipe berhasil ditambahkan!');
    setTimeout(() => location.reload(), 800);
}

async function submitUkuran() {
    const desc = document.getElementById('input-ukuran-desc').value.trim();
    if (!desc) { showNotif('Ukuran wajib diisi', 'error'); return; }
    await apiPost(ROUTES.ukuran.store, { deskripsi_ukuran: desc });
    showNotif('Ukuran berhasil ditambahkan!');
    setTimeout(() => location.reload(), 800);
}

async function submitKontainer() {
    const data = {
        no_kontainer: document.getElementById('input-kontainer-no').value.trim().toUpperCase(),
        id_customer:  document.getElementById('input-kontainer-customer').value,
        id_tipe:      document.getElementById('input-kontainer-tipe').value,
        id_ukuran:    document.getElementById('input-kontainer-ukuran').value,
    };
    if (!data.no_kontainer || !data.id_customer || !data.id_tipe || !data.id_ukuran) {
        showNotif('Semua field wajib diisi', 'error'); return;
    }
    await apiPost(ROUTES.kontainer.store, data);
    showNotif('Kontainer berhasil didaftarkan!');
    setTimeout(() => location.reload(), 800);
}

async function submitTarif() {
    const data = {
        id_customer:           document.getElementById('input-tarif-customer').value,
        id_tipe:               document.getElementById('input-tarif-tipe').value,
        id_ukuran:             document.getElementById('input-tarif-ukuran').value,
        tarif_bulanan:         parseFloat(document.getElementById('input-tarif-bulanan').value) || 0,
        tarif_harian:          parseFloat(document.getElementById('input-tarif-harian').value) || 0,
        tanggal_mulai_berlaku: document.getElementById('input-tarif-mulai').value,
    };
    if (!data.id_customer || !data.id_tipe || !data.id_ukuran) {
        showNotif('Customer, Tipe, dan Ukuran wajib dipilih', 'error'); return;
    }
    await apiPost(ROUTES.tarif.store, data);
    showNotif('Tarif berhasil disimpan!');
    setTimeout(() => location.reload(), 800);
}

async function deleteMaster(type, id, name) {
    if (!confirm(`Hapus "${name}"? Tindakan ini tidak bisa dibatalkan.`)) return;
    await apiDelete(ROUTES[type].del + id);
    showNotif(`"${name}" berhasil dihapus!`);
    setTimeout(() => location.reload(), 800);
}

// ══════════════════════════════════════════════════
// MASTER TABLE FILTER
// ══════════════════════════════════════════════════
function filterMasterTable(type, query) {
    const q     = query.toLowerCase();
    const tbody = document.querySelector(`#table-${type} tbody`);
    if (!tbody) return;
    tbody.querySelectorAll('tr[data-search]').forEach(row => {
        row.style.display = !q || row.dataset.search.includes(q) ? '' : 'none';
    });
}

// ══════════════════════════════════════════════════
// IMPORT PAYMENT
// ══════════════════════════════════════════════════
async function submitImportPayment() {
    const payload = document.getElementById('import-payment-text').value.trim();
    if (!payload) { showNotif('Payload kosong', 'error'); return; }
    const res  = await apiPost(ROUTES.importPayment, { payload });
    const div  = document.getElementById('import-payment-result');
    div.classList.remove('hidden');
    div.innerHTML = res.results.map(r =>
        `<div class="${r.status==='ok'?'text-emerald-600':'text-red-600'}">
         <i class="fas fa-${r.status==='ok'?'check':'times'}-circle mr-1"></i>
         Baris ${r.line}: ${r.msg}
         </div>`
    ).join('');
    showNotif(`${res.applied} pembayaran berhasil diproses!`);
    setTimeout(() => location.reload(), 2000);
}

// ══════════════════════════════════════════════════
// RESTORE JSON
// ══════════════════════════════════════════════════
async function submitRestoreJson() {
    const fileInput = document.getElementById('backup-file-input');
    if (!fileInput.files.length) { showNotif('Pilih file JSON terlebih dahulu', 'error'); return; }
    if (!confirm('PERINGATAN: Semua data saat ini akan dihapus dan diganti dengan data dari file backup. Lanjutkan?')) return;

    const formData = new FormData();
    formData.append('backup_file', fileInput.files[0]);
    formData.append('_token', CSRF);

    const res = await fetch(ROUTES.importJson, { method: 'POST', body: formData });
    const json = await res.json();
    if (json.success) {
        showNotif(json.message || 'Data berhasil dipulihkan!');
        setTimeout(() => location.reload(), 1000);
    } else {
        showNotif(json.message || 'Gagal memulihkan data', 'error');
    }
}

// ══════════════════════════════════════════════════
// WIPE ALL DATA
// ══════════════════════════════════════════════════
async function wipeAllData() {
    if (!confirm('PERINGATAN KERAS: Tindakan ini akan menghapus permanen seluruh data Sewa Kontainer (Customer, Tipe, Ukuran, Kontainer, Tarif, Transaksi Sewa, Tagihan, Invoice). Apakah Anda yakin?')) return;
    if (!confirm('Apakah Anda benar-benar yakin ingin membersihkan data? Tindakan ini tidak dapat dibatalkan.')) return;

    try {
        const json = await apiPost(ROUTES.wipeData);
        showNotif(json.message || 'Semua data berhasil dibersihkan!');
        setTimeout(() => location.reload(), 1000);
    } catch(e) {}
}

// ══════════════════════════════════════════════════
// BULK IMPORT
// ══════════════════════════════════════════════════
const BULK_TEMPLATES = {
    customer:   { title: 'Format: NAMA_CUSTOMER', body: 'Satu nama customer/vendor per baris.\nContoh:\nPT. Maju Bersama\nCV. Sejahtera' },
    tipe:       { title: 'Format: NAMA_TIPE', body: 'Satu nama tipe kontainer per baris.\nContoh:\nDry\nReefer\nOpen Top' },
    ukuran:     { title: 'Format: DESKRIPSI_UKURAN', body: 'Satu ukuran per baris.\nContoh:\n20 Feet\n40 Feet\n45 Feet HC' },
    kontainer:  { title: 'Format: NO_KONTAINER\tID_CUSTOMER\tID_TIPE\tID_UKURAN', body: 'Kolom dipisah TAB. Baris pertama boleh diisi header atau langsung data.\nContoh:\nABCU1234567\t1\t1\t1\nXYZU9876543\t2\t1\t2' },
    tarif:      { title: 'Format: ID_CUSTOMER\tID_TIPE\tID_UKURAN\tTARIF_BULANAN\tTARIF_HARIAN\tTGL_MULAI', body: 'Kolom dipisah TAB.\nContoh:\n1\t1\t1\t2500000\t85000\t2024-01-01' },
    sewa:       { title: 'Format: NO_KONTAINER\tID_CUSTOMER\tTGL_KELUAR\tTGL_KEMBALI\tKET', body: 'Kolom dipisah TAB. TGL_KEMBALI boleh kosong (kontainer masih keluar).\nContoh:\nABCU1234567\t1\t2024-01-10\t2024-02-10\tSewa reguler' },
    pembayaran: { title: 'Format: NO_NOTA\tID_CUSTOMER\tTOTAL\tTGL_JATUH_TEMPO', body: 'Kolom dipisah TAB.\nContoh:\nINV-2024-001\t1\t5000000\t2024-03-01' },
    pelunasan:  { title: 'Format: NO_NOTA\tNO_BUKTI_BAYAR\tTGL_BAYAR\tJML_BAYAR', body: 'Kolom dipisah TAB.\nContoh:\nINV-2024-001\tBKT-001\t2024-03-05\t5000000' },
};

function onBulkTypeChange() {
    const type = document.getElementById('bulk-import-type').value;
    const tpl  = BULK_TEMPLATES[type] || {};
    document.getElementById('bulk-tpl-title').textContent = tpl.title || 'Format:';
    document.getElementById('bulk-tpl-body').textContent  = tpl.body  || '-';

    // toggle preview vs process button
    const isPelunasan = type === 'pelunasan';
    document.getElementById('btn-bulk-process').classList.toggle('hidden', isPelunasan);
    document.getElementById('bulk-preview-area').classList.add('hidden');
}

function loadBulkTemplate() {
    const type = document.getElementById('bulk-import-type').value;
    const tpl  = BULK_TEMPLATES[type] || {};
    document.getElementById('bulk-import-textarea').value = tpl.body || '';
}

function copyBulkTemplate() {
    const type = document.getElementById('bulk-import-type').value;
    const tpl  = BULK_TEMPLATES[type] || {};
    navigator.clipboard.writeText(tpl.body || '').then(() => showNotif('Format disalin ke clipboard!'));
}

function clearBulkTextarea() {
    document.getElementById('bulk-import-textarea').value = '';
    document.getElementById('bulk-import-log').classList.add('hidden');
    document.getElementById('bulk-import-log').innerHTML = '';
    document.getElementById('bulk-preview-area').classList.add('hidden');
}

async function processBulkImport() {
    const type    = document.getElementById('bulk-import-type').value;
    const payload = document.getElementById('bulk-import-textarea').value.trim();
    const logDiv  = document.getElementById('bulk-import-log');

    if (!payload) { showNotif('Data masih kosong', 'error'); return; }

    if (type === 'pelunasan') {
        // 2-stage: preview first
        await previewBulkPelunasan(payload);
        return;
    }

    logDiv.classList.add('hidden');
    logDiv.innerHTML = '';

    try {
        const res = await apiPost(ROUTES.bulkImport || `/sewa-kontainer/bulk-import`, { type, payload });
        logDiv.classList.remove('hidden');

        if (res.results && res.results.length) {
            logDiv.innerHTML = res.results.map(r =>
                `<div class="${r.status === 'ok' ? 'text-emerald-600' : 'text-red-600'}">
                    <i class="fas fa-${r.status === 'ok' ? 'check' : 'times'}-circle mr-1"></i>
                    Baris ${r.line}: ${r.msg}
                 </div>`
            ).join('');
        } else {
            logDiv.innerHTML = `<div class="text-emerald-600"><i class="fas fa-check-circle mr-1"></i>${res.message || 'Import berhasil!'}</div>`;
        }
        showNotif(res.message || 'Import berhasil!');
        setTimeout(() => location.reload(), 1500);
    } catch(e) {
        logDiv.classList.remove('hidden');
        logDiv.innerHTML = `<div class="text-red-600"><i class="fas fa-times-circle mr-1"></i>Error: ${e.message || 'Gagal melakukan import'}</div>`;
    }
}

async function previewBulkPelunasan(payload) {
    try {
        const res = await apiPost(ROUTES.bulkPreview || `/sewa-kontainer/bulk-preview`, { payload });
        const tbody = document.getElementById('bulk-preview-tbody');
        tbody.innerHTML = '';

        (res.rows || []).forEach((r, i) => {
            const valid = r.valid ? 'text-emerald-600' : 'text-red-500';
            tbody.innerHTML += `
                <tr>
                    <td>${i + 1}</td>
                    <td>${r.no_nota || '-'}</td>
                    <td>${r.customer || '-'}</td>
                    <td>${r.no_bukti || '-'}</td>
                    <td>${r.tgl_bayar || '-'}</td>
                    <td>${r.jml_tagihan || '-'}</td>
                    <td>${r.grand_total || '-'}</td>
                    <td class="${valid} font-bold">${r.valid ? '✓ Valid' : '✗ ' + r.error}</td>
                </tr>`;
        });

        document.getElementById('bulk-preview-area').classList.remove('hidden');
        document.getElementById('btn-bulk-process').classList.add('hidden');
    } catch(e) {
        showNotif('Gagal memuat preview: ' + (e.message || ''), 'error');
    }
}

async function applyBulkImport() {
    const payload = document.getElementById('bulk-import-textarea').value.trim();
    if (!payload) { showNotif('Data kosong', 'error'); return; }

    try {
        const res = await apiPost(ROUTES.bulkImport || `/sewa-kontainer/bulk-import`, { type: 'pelunasan', payload, apply: true });
        showNotif(res.message || `${res.applied || 0} pelunasan berhasil diproses!`);
        setTimeout(() => location.reload(), 1200);
    } catch(e) {
        showNotif('Gagal: ' + (e.message || ''), 'error');
    }
}

function hideBulkPreview() {
    document.getElementById('bulk-preview-area').classList.add('hidden');
    document.getElementById('btn-bulk-process').classList.remove('hidden');
}

// ══════════════════════════════════════════════════
// INIT
// ══════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', () => {
    initBillingTable();
    onBulkTypeChange(); // init template info & button state
});
</script>

  </main>
</div>
@endsection
