import React, { useState, useEffect } from 'react';
import { loadAppState, saveAppState, compileAllPeriods, AppState, fetchAppStateFromDB } from './dataStore';
import { formatRupiah, formatIndoDate, formatToWIB } from './utils';
import MasterPanel from './components/MasterPanel';
import TransaksiSewa from './components/TransaksiSewa';
import InvoiceManager from './components/InvoiceManager';
import BulkImportPanel from './components/BulkImportPanel';
import { Anchor, Shield, Landmark, LayoutGrid, Server, ShieldCheck, Database, FileSpreadsheet, Layers, RefreshCw, Layers3, Activity, Download, Upload, CheckCircle } from 'lucide-react';

export default function App() {
  const [state, setState] = useState<AppState>(() => loadAppState());
  const [isSyncing, setIsSyncing] = useState(true);
  const [activeTab, setActiveTab] = useState<'billing' | 'rental' | 'master' | 'import'>('billing');
  const [currentTime, setCurrentTime] = useState('2026-06-12T07:06:12-07:00');

  useEffect(() => {
    fetchAppStateFromDB().then(dbState => {
      if (dbState) {
        setState(dbState);
      }
      setIsSyncing(false);
    });
  }, []);

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
            const validatedState: AppState = {
              customers: parsed.customers || [],
              tipes: parsed.tipes || [],
              ukurans: parsed.ukurans || [],
              kontainers: parsed.kontainers || [],
              tarifs: parsed.tarifs || [],
              sewas: parsed.sewas || [],
              invoices: parsed.invoices || [],
              paymentOverrides: parsed.paymentOverrides || {},
              manualTagihans: parsed.manualTagihans || [],
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

  // Sync state changes to LocalStorage + Database
  const handleStateChange = (updatedState: AppState) => {
    setState(updatedState);
    saveAppState(updatedState).then(synced => {
      if (synced) {
        triggerBackupNoti('sukses', '✅ Data berhasil disinkronkan ke database server.');
      }
    });
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
    <div className="min-h-screen transition-colors duration-300 text-slate-800 font-sans antialiased bg-indigo-50/20" id="main-applet-shell">
      {/* PROFESSIONAL HIGH-CONTRAST HEADER */}
      <header className="transition-colors duration-350 text-white border-b sticky top-0 z-40 shadow-xs bg-indigo-950 border-indigo-900/40" id="navbar-top">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div className="flex items-center gap-3">
            <div className="p-2 rounded-xl text-white border shadow-inner bg-indigo-850 border-indigo-700/55">
              <Anchor className="w-5 h-5 text-indigo-300" />
            </div>
            <div>
              <h1 className="font-bold text-base tracking-tight text-white uppercase flex flex-wrap items-center gap-2">
                <span>PORTAL SEWA KONTAINER</span>
                <span className="text-[9px] px-2.5 py-0.5 rounded-full font-bold uppercase border bg-indigo-900/90 text-indigo-300 border-indigo-800">
                  Pihak Penyewa (Sewa In / Lessee)
                </span>
                <span className="bg-slate-900/60 text-slate-300 border border-slate-800 text-[9px] px-2 py-0.5 rounded-full font-semibold">
                  Offline-First LocalDB
                </span>
              </h1>
              <p className="text-[10px] italic text-indigo-250">
                Sistem kontrol biaya pengeluaran, PPN Masukan, PPh 23, serta rekonsiliasi tagihan dari Vendor
              </p>
            </div>
          </div>

          {/* CLOCK ACCENT */}
          <div className="flex items-center gap-2.5 px-3 py-1.5 rounded-xl border text-xs self-start md:self-center bg-indigo-900/40 border-indigo-800/40">
            <span className="w-2 h-2 rounded-full animate-pulse bg-indigo-400" />
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
        <div className="border p-4 rounded-2xl flex flex-col md:flex-row items-center justify-between gap-4 transition-all duration-350 bg-indigo-950/[0.03] border-indigo-800/10" id="backup-auto-save-console">
          <div className="flex items-center gap-2.5 text-slate-700">
            <span className="relative flex h-2.5 w-2.5">
              <span className="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 bg-indigo-400"></span>
              <span className="relative inline-flex rounded-full h-2.5 w-2.5 bg-indigo-500"></span>
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
              className="flex-1 md:flex-initial inline-flex items-center justify-center px-4 py-2 text-xs font-bold text-white rounded-xl transition-all cursor-pointer shadow-xs gap-1.5 bg-indigo-700 hover:bg-indigo-800"
            >
              <Download className="w-3.5 h-3.5" />
              <span>Unduh Backup JSON</span>
            </button>

            <label className="flex-1 md:flex-initial inline-flex items-center justify-center px-4 py-2 text-xs font-bold bg-white hover:bg-slate-50 border rounded-xl transition-all cursor-pointer shadow-xs gap-1.5 text-indigo-800 border-indigo-600/35">
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

            <button
              id="btn-clear-storage"
              onClick={() => {
                if (window.confirm("Apakah Anda yakin ingin menghapus semua data di halaman ini?")) {
                  const emptyState = {
                    customers: [],
                    tipes: [],
                    ukurans: [],
                    kontainers: [],
                    tarifs: [],
                    sewas: [],
                    invoices: [],
                    paymentOverrides: {},
                    manualTagihans: []
                  };
                  handleStateChange(emptyState);
                  triggerBackupNoti('sukses', 'Semua penyimpanan data di halaman ini berhasil dikosongkan!');
                }
              }}
              className="flex-1 md:flex-initial inline-flex items-center justify-center px-4 py-2 text-xs font-bold text-white rounded-xl transition-all cursor-pointer shadow-xs gap-1.5 bg-rose-600 hover:bg-rose-700"
            >
              <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
              <span>Bersihkan Penyimpanan</span>
            </button>
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
            <div className="p-3 rounded-xl border bg-indigo-50/50 text-indigo-700 border-indigo-100/50">
              <Layers3 className="w-5 h-5" />
            </div>
          </div>

          <div className="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs flex items-center justify-between">
            <div>
              <p className="text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                Penyewaan Kontainer Aktif
              </p>
              <h3 className="text-xl font-bold text-slate-850 mt-1 font-mono">{activeRentals} Siklus</h3>
              <p className="text-[10px] text-slate-500 mt-0.5">Paralel sewa diperbolehkan</p>
            </div>
            <div className="p-3 rounded-xl border bg-indigo-50 text-indigo-700 border-indigo-100">
              <Activity className="w-5 h-5" />
            </div>
          </div>

          <div className="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs flex items-center justify-between">
            <div>
              <p className="text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                Estimasi Biaya Belum Dibayar
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
                Biaya Sewa Terbayar (Lunas)
              </p>
              <h3 className="text-xl font-bold mt-1 font-mono text-indigo-700">{formatRupiah(totalReceived)}</h3>
              <p className="text-[10px] text-slate-500 mt-0.5">Tanpa sistem cicilan/parsial</p>
            </div>
            <div className="p-3 rounded-xl border bg-indigo-50 text-indigo-700 border-indigo-100">
              <ShieldCheck className="w-5 h-5" />
            </div>
          </div>

        </div>

        {/* WORKSPACE NAVIGATION TABS IN INDONESIAN */}
        <div className="flex border-b border-slate-200" id="tabs-navigation-panel">
          {[
            {
              id: 'billing',
              label: '1. Dasbor Pengeluaran &amp; Pembayaran Vendor',
              icon: LayoutGrid
            },
            {
              id: 'rental',
              label: '2. Siklus Sewa In &amp; Kontainer',
              icon: RefreshCw
            },
            {
              id: 'master',
              label: '3. Kelola Database Vendor/Owner',
              icon: Database
            },
            {
              id: 'import',
              label: '4. Impor Excel Cepat (Vendor)',
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
                    ? 'border-indigo-600 text-indigo-700 font-extrabold'
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
            <InvoiceManager state={state} onStateChange={handleStateChange} utcTime={currentTime} />
          </div>

          <div className={activeTab === 'rental' ? 'block' : 'hidden'} id="panel-rental">
            <TransaksiSewa state={state} onStateChange={handleStateChange} utcTime={currentTime} />
          </div>

          <div className={activeTab === 'master' ? 'block' : 'hidden'} id="panel-master">
            <MasterPanel state={state} onStateChange={handleStateChange} utcTime={currentTime} />
          </div>

          <div className={activeTab === 'import' ? 'block' : 'hidden'} id="panel-import">
            <BulkImportPanel state={state} onStateChange={handleStateChange} utcTime={currentTime} />
          </div>
        </div>

      </main>
    </div>
  );
}
