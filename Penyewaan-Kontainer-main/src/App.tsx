import React, { useState, useEffect } from 'react';
import { loadAppState, saveAppState, compileAllPeriods, AppState } from './dataStore';
import { formatRupiah, formatIndoDate, formatToWIB } from './utils';
import MasterPanel from './components/MasterPanel';
import TransaksiSewa from './components/TransaksiSewa';
import InvoiceManager from './components/InvoiceManager';
import BulkImportPanel from './components/BulkImportPanel';
import { Anchor, Shield, Landmark, LayoutGrid, Server, ShieldCheck, Database, FileSpreadsheet, Layers, RefreshCw, Layers3, Activity, Download, Upload, CheckCircle } from 'lucide-react';

export default function App() {
  const [state, setState] = useState<AppState>(() => loadAppState());
  const [activeTab, setActiveTab] = useState<'billing' | 'rental' | 'master' | 'import'>('billing');
  const [currentTime, setCurrentTime] = useState('2026-06-12T07:06:12-07:00');
  const [appMode, setAppMode] = useState<'sewa_out' | 'sewa_in'>(() => {
    return (localStorage.getItem('sewa_kontainer_app_mode') as 'sewa_out' | 'sewa_in') || 'sewa_out';
  });

  const handleAppModeChange = (mode: 'sewa_out' | 'sewa_in') => {
    setAppMode(mode);
    localStorage.setItem('sewa_kontainer_app_mode', mode);
  };

  // Integration for custom notifications
  const [backupNoti, setBackupNoti] = useState<{ type: 'sukses' | 'error'; msg: string } | null>(null);

  const triggerBackupNoti = (type: 'sukses' | 'error', msg: string) => {
    setBackupNoti({ type, msg });
    setTimeout(() => setBackupNoti(null), 6000);
  };

  // Export app state to custom JSON file for backup/restructuring
  const handleExportBackup = () => {
    try {
      const dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(state, null, 2));
      const downloadAnchor = document.createElement('a');
      downloadAnchor.setAttribute("href", dataStr);
      const dateFormatted = new Date().toISOString().split('T')[0];
      downloadAnchor.setAttribute("download", `backup_sewa_kontainer_${dateFormatted}.json`);
      document.body.appendChild(downloadAnchor);
      downloadAnchor.click();
      downloadAnchor.remove();
      triggerBackupNoti('sukses', 'Backup database berhasil diunduh! Simpan file ini di komputer Anda dengan aman.');
    } catch (err) {
      triggerBackupNoti('error', 'Gagal mengekspor data backup.');
    }
  };

  // Import app state from JSON backup file
  const handleImportBackup = (e: React.ChangeEvent<HTMLInputElement>) => {
    const fileReader = new FileReader();
    if (e.target.files && e.target.files[0]) {
      fileReader.readAsText(e.target.files[0], "UTF-8");
      fileReader.onload = (event) => {
        try {
          const parsed = JSON.parse(event.target?.result as string);
          if (parsed && typeof parsed === 'object') {
            // Check essential keys to minimize corruption
            const validatedState = {
              customers: parsed.customers || [],
              tipes: parsed.tipes || [],
              ukurans: parsed.ukurans || [],
              kontainers: parsed.kontainers || [],
              tarifs: parsed.tarifs || [],
              sewas: parsed.sewas || [],
              invoices: parsed.invoices || [],
              paymentOverrides: parsed.paymentOverrides || {},
            };
            handleStateChange(validatedState);
            triggerBackupNoti('sukses', 'Sistem berhasil memulihkan semua data dari file backup JSON Anda!');
          } else {
            triggerBackupNoti('error', 'Format file backup tidak valid.');
          }
        } catch (err) {
          triggerBackupNoti('error', 'Gagal memuat file cadangan backup. Pastikan berkas berformat JSON.');
        }
      };
    }
  };

  // Sync state changes to LocalStorage
  const handleStateChange = (updatedState: AppState) => {
    setState(updatedState);
    saveAppState(updatedState);
  };

  // Compile calculations for KPIs
  const allPeriods = compileAllPeriods(state, currentTime);
  const totalContainers = state.kontainers.length;
  const activeRentals = state.sewas.filter(s => s.status_sewa === 'Aktif').length;
  
  // Total pending/unbilled/unpaid
  const totalUnpaid = allPeriods
    .filter(p => p.status_bayar === 'Belum Bayar' || p.status_bayar === 'Belum Ditagih')
    .reduce((sum, p) => sum + p.jumlah_tagihan, 0);

  // Total received
  const totalReceived = allPeriods
    .filter(p => p.status_bayar === 'Lunas')
    .reduce((sum, p) => sum + p.jumlah_tagihan, 0);
  
  return (
    <div className={`min-h-screen transition-colors duration-300 text-slate-800 font-sans antialiased ${appMode === 'sewa_in' ? 'bg-indigo-50/20' : 'bg-slate-50/50'}`} id="main-applet-shell">
      {/* PROFESSIONAL HIGH-CONTRAST HEADER */}
      <header className={`transition-colors duration-350 text-white border-b sticky top-0 z-40 shadow-xs ${appMode === 'sewa_in' ? 'bg-indigo-950 border-indigo-900/40' : 'bg-emerald-950 border-emerald-900/40'}`} id="navbar-top">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div className="flex items-center gap-3">
            <div className={`p-2 rounded-xl text-white border shadow-inner transition-colors duration-350 ${appMode === 'sewa_in' ? 'bg-indigo-850 border-indigo-700/55' : 'bg-emerald-800 border-emerald-700/55'}`}>
              <Anchor className={`w-5 h-5 ${appMode === 'sewa_in' ? 'text-indigo-300' : 'text-emerald-300'}`} />
            </div>
            <div>
              <h1 className="font-bold text-base tracking-tight text-white uppercase flex flex-wrap items-center gap-2">
                <span>PORTAL SEWA KONTAINER</span>
                <span className={`text-[9px] px-2.5 py-0.5 rounded-full font-bold uppercase transition-all duration-350 border ${
                  appMode === 'sewa_in'
                    ? 'bg-indigo-900/90 text-indigo-350 border-indigo-800'
                    : 'bg-emerald-900/90 text-emerald-300 border-emerald-800'
                }`}>
                  {appMode === 'sewa_in' ? 'Pihak Penyewa (Sewa In)' : 'Pihak Pemilik (Sewa Out)'}
                </span>
                <span className="bg-slate-900/60 text-slate-300 border border-slate-800 text-[9px] px-2 py-0.5 rounded-full font-semibold">
                  Offline-First LocalDB
                </span>
              </h1>
              <p className={`text-[10px] italic transition-colors duration-350 ${appMode === 'sewa_in' ? 'text-indigo-250' : 'text-emerald-250'}`}>
                {appMode === 'sewa_in'
                  ? 'Sistem kontrol biaya pengeluaran, PPN Masukan, PPh 23, serta rekonsiliasi tagihan dari Vendor'
                  : 'Sistem kalkulasi proris maret ke januari (30 hari) & tahun kabisat februari (28/29 hari)'}
              </p>
            </div>
          </div>

          {/* DUAL PERSONA MODE TOGGLE */}
          <div className={`flex p-1 rounded-xl border self-start md:self-center transition-colors duration-350 ${
            appMode === 'sewa_in' ? 'bg-indigo-900/70 border-indigo-850/60' : 'bg-emerald-900/50 border-emerald-850/60'
          }`}>
            <button
              onClick={() => handleAppModeChange('sewa_out')}
              className={`px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all duration-300 flex items-center gap-1.5 cursor-pointer select-none ${
                appMode === 'sewa_out'
                  ? 'bg-emerald-600 text-white shadow-xs font-extrabold'
                  : 'text-emerald-300 hover:text-white'
              }`}
            >
              <span>Sewa Out (Lessor)</span>
            </button>
            <button
              onClick={() => handleAppModeChange('sewa_in')}
              className={`px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all duration-300 flex items-center gap-1.5 cursor-pointer select-none ${
                appMode === 'sewa_in'
                  ? 'bg-indigo-600 text-white shadow-xs font-extrabold'
                  : 'text-indigo-200 hover:text-white'
              }`}
            >
              <span>Sewa In (Lessee)</span>
            </button>
          </div>

          {/* CLOCK ACCENT */}
          <div className={`flex items-center gap-2.5 px-3 py-1.5 rounded-xl border text-xs self-start md:self-center transition-colors duration-350 ${
            appMode === 'sewa_in' ? 'bg-indigo-900/40 border-indigo-800/40' : 'bg-emerald-900/40 border-emerald-800/40'
          }`}>
            <span className={`w-2 h-2 rounded-full animate-pulse ${appMode === 'sewa_in' ? 'bg-indigo-400' : 'bg-emerald-400'}`} />
            <span className="text-slate-350 uppercase font-bold text-[9px] tracking-wider font-mono">WAKTU AKTIF WIB:</span>
            <span className="font-mono font-medium text-slate-100">{formatToWIB(currentTime)}</span>
          </div>
        </div>
      </header>

      <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        
        {backupNoti && (
          <div
            id="backup-noti-banner"
            className={`p-4 rounded-2xl flex items-center gap-2.5 border text-sm shadow-xs transition-all ${
              backupNoti.type === 'sukses'
                ? 'bg-emerald-50 border-emerald-200 text-emerald-850'
                : 'bg-rose-50 border-rose-100 text-rose-850'
            }`}
          >
            <CheckCircle className={`w-5 h-5 shrink-0 ${backupNoti.type === 'sukses' ? 'text-emerald-650' : 'text-rose-600'}`} />
            <span className="font-semibold text-xs leading-none">{backupNoti.msg}</span>
          </div>
        )}

        {/* BACKUP & AUTO SAVE CONSOLE STATUS */}
        <div className={`border p-4 rounded-2xl flex flex-col md:flex-row items-center justify-between gap-4 transition-all duration-350 ${
          appMode === 'sewa_in' 
            ? 'bg-indigo-950/[0.03] border-indigo-800/10' 
            : 'bg-emerald-900/5 border-emerald-800/10'
        }`} id="backup-auto-save-console">
          <div className="flex items-center gap-2.5 text-slate-700">
            <span className="relative flex h-2.5 w-2.5">
              <span className={`animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 ${appMode === 'sewa_in' ? 'bg-indigo-400' : 'bg-emerald-400'}`}></span>
              <span className={`relative inline-flex rounded-full h-2.5 w-2.5 ${appMode === 'sewa_in' ? 'bg-indigo-500' : 'bg-emerald-500'}`}></span>
            </span>
            <div>
              <p className="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                <span>Penyimpanan Otomatis Aktif di Browser (Local Storage)</span>
              </p>
              <p className="text-[10px] text-slate-500 mt-0.5">
                Semua data Anda otomatis tersimpan saat di-input. Untuk berjaga-jaga hilangnya cache browser, unduh Backup berkas JSON Anda secara periodik!
              </p>
            </div>
          </div>

          <div className="flex flex-wrap items-center gap-2.5 self-stretch md:self-auto">
            <button
              id="btn-export-backup-json"
              onClick={handleExportBackup}
              className={`flex-1 md:flex-initial inline-flex items-center justify-center px-4 py-2 text-xs font-bold text-white rounded-xl transition-all cursor-pointer shadow-xs gap-1.5 ${
                appMode === 'sewa_in' ? 'bg-indigo-700 hover:bg-indigo-800' : 'bg-emerald-700 hover:bg-emerald-800'
              }`}
            >
              <Download className="w-3.5 h-3.5" />
              <span>Unduh Backup JSON</span>
            </button>

            <label className={`flex-1 md:flex-initial inline-flex items-center justify-center px-4 py-2 text-xs font-bold bg-white hover:bg-slate-50 border rounded-xl transition-all cursor-pointer shadow-xs gap-1.5 ${
              appMode === 'sewa_in' ? 'text-indigo-800 border-indigo-600/35' : 'text-emerald-800 border-emerald-600/35'
            }`}>
              <Upload className="w-3.5 h-3.5" />
              <span>Pulihkan dari JSON</span>
              <input
                type="file"
                accept=".json"
                onChange={handleImportBackup}
                className="hidden"
                id="input-restore-backup-file"
              />
            </label>
          </div>
        </div>

        {/* KPI METRIC CARDS GRID (BENTO BOX) */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4" id="kpi-grids-box">
          
          <div className="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs flex items-center justify-between">
            <div>
              <p className="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Total Kontainer Terdaftar</p>
              <h3 className="text-xl font-bold text-slate-850 mt-1 font-mono">{totalContainers} Unit</h3>
              <p className="text-[10px] text-slate-500 mt-0.5">Semua tipe &amp; ukuran</p>
            </div>
            <div className={`p-3 rounded-xl border ${appMode === 'sewa_in' ? 'bg-indigo-50/50 text-indigo-700 border-indigo-100/50' : 'bg-amber-50 text-amber-700 border-amber-100'}`}>
              <Layers3 className="w-5 h-5" />
            </div>
          </div>

          <div className="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs flex items-center justify-between">
            <div>
              <p className="text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                {appMode === 'sewa_in' ? 'Penyewaan Kontainer Aktif' : 'Sewa Berjalan Aktif'}
              </p>
              <h3 className="text-xl font-bold text-slate-850 mt-1 font-mono">{activeRentals} Siklus</h3>
              <p className="text-[10px] text-slate-500 mt-0.5">Paralel sewa diperbolehkan</p>
            </div>
            <div className={`p-3 rounded-xl border ${appMode === 'sewa_in' ? 'bg-indigo-50 text-indigo-700 border-indigo-100' : 'bg-indigo-50 text-indigo-700 border-indigo-100'}`}>
              <Activity className="w-5 h-5" />
            </div>
          </div>

          <div className="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs flex items-center justify-between">
            <div>
              <p className="text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                {appMode === 'sewa_in' ? 'Estimasi Biaya Belum Dibayar' : 'Total Belum Tertagih/Bayar'}
              </p>
              <h3 className="text-xl font-bold text-rose-700 mt-1 font-mono">{formatRupiah(totalUnpaid)}</h3>
              <p className="text-[10px] text-rose-500 mt-0.5">Siklus outstanding bulanan</p>
            </div>
            <div className="bg-rose-50 text-rose-700 p-3 rounded-xl border border-rose-100">
              <Landmark className="w-5 h-5" />
            </div>
          </div>

          <div className="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs flex items-center justify-between">
            <div>
              <p className="text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                {appMode === 'sewa_in' ? 'Biaya Sewa Terbayar (Lunas)' : 'Pendapatan Diterima (Lunas)'}
              </p>
              <h3 className={`text-xl font-bold mt-1 font-mono transition-colors duration-350 ${appMode === 'sewa_in' ? 'text-indigo-700' : 'text-emerald-700'}`}>{formatRupiah(totalReceived)}</h3>
              <p className="text-[10px] text-slate-500 mt-0.5">Tanpa sistem cicilan/parsial</p>
            </div>
            <div className={`p-3 rounded-xl border ${appMode === 'sewa_in' ? 'bg-indigo-50 text-indigo-700 border-indigo-100' : 'bg-emerald-50 text-emerald-705 border-emerald-100'}`}>
              <ShieldCheck className="w-5 h-5" />
            </div>
          </div>

        </div>

        {/* WORKSPACE NAVIGATION TABS IN INDONESIAN */}
        <div className="flex border-b border-slate-200" id="tabs-navigation-panel">
          {[
            {
              id: 'billing',
              label: appMode === 'sewa_in' ? '1. Dasbor Pengeluaran &amp; Pembayaran Vendor' : '1. Dasbor Tagihan &amp; Pembayaran',
              icon: LayoutGrid
            },
            {
              id: 'rental',
              label: appMode === 'sewa_in' ? '2. Siklus Sewa In &amp; Kontainer' : '2. Siklus Sewa &amp; Pengembalian',
              icon: RefreshCw
            },
            {
              id: 'master',
              label: appMode === 'sewa_in' ? '3. Kelola Database Vendor/Owner' : '3. Kelola Database Master',
              icon: Database
            },
            {
              id: 'import',
              label: appMode === 'sewa_in' ? '4. Impor Excel Cepat (Vendor)' : '4. Peluncur Impor Excel Cepat',
              icon: FileSpreadsheet
            },
          ].map((tab) => {
            const Icon = tab.icon;
            const isActive = activeTab === tab.id;
            return (
              <button
                key={tab.id}
                id={`tab-${tab.id}`}
                onClick={() => setActiveTab(tab.id as any)}
                className={`py-3 px-6 text-xs font-bold border-b-2 transition-all inline-flex items-center gap-2 cursor-pointer ${
                  isActive
                    ? appMode === 'sewa_in'
                      ? 'border-indigo-600 text-indigo-700 font-extrabold'
                      : 'border-emerald-600 text-emerald-700 font-extrabold'
                    : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300'
                }`}
              >
                <Icon className="w-4 h-4" />
                <span dangerouslySetInnerHTML={{ __html: tab.label }} />
              </button>
            );
          })}
        </div>

        {/* WORKSPACE PANELS */}
        <div className="space-y-6" id="active-tab-panel">
          <div className={activeTab === 'billing' ? 'block' : 'hidden'} id="panel-billing">
            <InvoiceManager state={state} onStateChange={handleStateChange} utcTime={currentTime} appMode={appMode} />
          </div>

          <div className={activeTab === 'rental' ? 'block' : 'hidden'} id="panel-rental">
            <TransaksiSewa state={state} onStateChange={handleStateChange} utcTime={currentTime} appMode={appMode} />
          </div>

          <div className={activeTab === 'master' ? 'block' : 'hidden'} id="panel-master">
            <MasterPanel state={state} onStateChange={handleStateChange} utcTime={currentTime} appMode={appMode} />
          </div>

          <div className={activeTab === 'import' ? 'block' : 'hidden'} id="panel-import">
            <BulkImportPanel state={state} onStateChange={handleStateChange} utcTime={currentTime} appMode={appMode} />
          </div>
        </div>

      </main>
    </div>
  );
}
