import React, { useState } from 'react';
import { AppState, saveAppState } from '../dataStore';
import { Customer, TipeKontainer, UkuranKontainer, Kontainer, TarifSewa } from '../types';
import { formatRupiah, formatIndoDate, parseInputDate, formatEntryDate } from '../utils';
import { Plus, Trash2, Search, Edit2, Upload, AlertTriangle, Check, BookOpen, Save } from 'lucide-react';
import SearchableSelect from './SearchableSelect';
import { FormDateInput } from './FormDateInput';

interface MasterPanelProps {
  state: AppState;
  onStateChange: (updated: AppState) => void;
  utcTime: string;
}

export default function MasterPanel({ state, onStateChange, utcTime }: MasterPanelProps) {
  const isSewaIn = true;
  const [activeSubTab, setActiveSubTab] = useState<'customer' | 'tipe' | 'ukuran' | 'kontainer' | 'tarif'>('customer');
  const [searchQuery, setSearchQuery] = useState('');

  // Container Pagination
  const [kontPage, setKontPage] = useState(1);
  const kontPageSize = 20;

  // Reset page when switching views or search
  React.useEffect(() => {
    setKontPage(1);
    setSearchQuery('');
  }, [activeSubTab]);

  React.useEffect(() => {
    setKontPage(1);
  }, [searchQuery]);

  // Form states
  const [custName, setCustName] = useState('');
  const [tipeName, setTipeName] = useState('');
  const [ukuranDesc, setUkuranDesc] = useState('');
  
  // Kontainer Form
  const [kontNo, setKontNo] = useState('');
  const [kontCustId, setKontCustId] = useState('');
  const [kontTipeId, setKontTipeId] = useState('');
  const [kontUkuranId, setKontUkuranId] = useState('');
  
  // Tarif Form
  const [tarifCustId, setTarifCustId] = useState('');
  const [tarifTipeId, setTarifTipeId] = useState('');
  const [tarifUkuranId, setTarifUkuranId] = useState('');
  const [tarifBulan, setTarifBulan] = useState('');
  const [tarifHari, setTarifHari] = useState('');
  const [tarifStart, setTarifStart] = useState('');
  const [tarifEnd, setTarifEnd] = useState('');

  // Notification state
  const [noti, setNoti] = useState<{ type: 'sukses' | 'error'; msg: string } | null>(null);

  const triggerNoti = (type: 'sukses' | 'error', msg: string) => {
    setNoti({ type, msg });
    setTimeout(() => setNoti(null), 4000);
  };

  // 1. Customer Handlers
  const handleAddCustomer = (e: React.FormEvent) => {
    e.preventDefault();
    if (!custName.trim()) {
      triggerNoti('error', 'Nama Customer tidak boleh kosong');
      return;
    }
    const newCust: Customer = {
      id_customer: 'cust_' + Date.now(),
      nama_customer: custName.trim(),
      status_aktif: true
    };
    const updated = { ...state, customers: [...state.customers, newCust] };
    onStateChange(updated);
    setCustName('');
    triggerNoti('sukses', `Customer "${newCust.nama_customer}" berhasil ditambahkan`);
  };

  const handleToggleCustomerStatus = (id: string) => {
    const updated = state.customers.map(c =>
      c.id_customer === id
        ? { ...c, status_aktif: c.status_aktif === false ? true : false }
        : c
    );
    onStateChange({ ...state, customers: updated });
    const c = state.customers.find(x => x.id_customer === id);
    const nextVal = c?.status_aktif === false ? 'diaktifkan' : 'dinonaktifkan';
    triggerNoti('sukses', `Customer "${c?.nama_customer}" berhasil ${nextVal}`);
  };

  // 2. Tipe Handlers
  const handleAddTipe = (e: React.FormEvent) => {
    e.preventDefault();
    if (!tipeName.trim()) {
      triggerNoti('error', 'Nama Tipe tidak boleh kosong');
      return;
    }
    const newTipe: TipeKontainer = {
      id_tipe: 'tipe_' + Date.now(),
      nama_tipe: tipeName.trim(),
      status_aktif: true
    };
    const updated = { ...state, tipes: [...state.tipes, newTipe] };
    onStateChange(updated);
    setTipeName('');
    triggerNoti('sukses', `Tipe "${newTipe.nama_tipe}" berhasil ditambahkan`);
  };

  const handleToggleTipeStatus = (id: string) => {
    const updated = state.tipes.map(t =>
      t.id_tipe === id
        ? { ...t, status_aktif: t.status_aktif === false ? true : false }
        : t
    );
    onStateChange({ ...state, tipes: updated });
    const t = state.tipes.find(x => x.id_tipe === id);
    const nextVal = t?.status_aktif === false ? 'diaktifkan' : 'dinonaktifkan';
    triggerNoti('sukses', `Tipe "${t?.nama_tipe}" berhasil ${nextVal}`);
  };

  // 3. Ukuran Handlers (Automatic Formatting 20 -> 20', 40 -> 40')
  const handleAddUkuran = (e: React.FormEvent) => {
    e.preventDefault();
    const rawVal = ukuranDesc.trim();
    if (!rawVal) {
      triggerNoti('error', 'Ukuran tidak boleh kosong');
      return;
    }
    // Auto convert numbers like 20 -> 20', 40 -> 40'
    let formatted = rawVal;
    if (/^\d+$/.test(rawVal)) {
      formatted = `${rawVal}'`;
    }
    
    // Check duplicate
    if (state.ukurans.some(u => u.deskripsi_ukuran === formatted)) {
      triggerNoti('error', `Ukuran "${formatted}" sudah ada`);
      return;
    }

    const newSize: UkuranKontainer = {
      id_ukuran: 'sz_' + Date.now(),
      deskripsi_ukuran: formatted,
      status_aktif: true
    };
    const updated = { ...state, ukurans: [...state.ukurans, newSize] };
    onStateChange(updated);
    setUkuranDesc('');
    triggerNoti('sukses', `Ukuran "${newSize.deskripsi_ukuran}" berhasil disimpan`);
  };

  const handleToggleUkuranStatus = (id: string) => {
    const updated = state.ukurans.map(u =>
      u.id_ukuran === id
        ? { ...u, status_aktif: u.status_aktif === false ? true : false }
        : u
    );
    onStateChange({ ...state, ukurans: updated });
    const u = state.ukurans.find(x => x.id_ukuran === id);
    const nextVal = u?.status_aktif === false ? 'diaktifkan' : 'dinonaktifkan';
    triggerNoti('sukses', `Ukuran "${u?.deskripsi_ukuran}" berhasil ${nextVal}`);
  };

  // 4. Kontainer Handlers
  const handleAddKontainer = (e: React.FormEvent) => {
    e.preventDefault();
    const cleanNo = kontNo.trim().toUpperCase().replace(/\s+/g, '');
    if (!cleanNo) {
      triggerNoti('error', 'No Kontainer tidak boleh kosong');
      return;
    }
    if (!kontCustId || !kontTipeId || !kontUkuranId) {
      triggerNoti('error', 'Semua asosiasi Customer, Tipe, dan Ukuran wajib dipilih');
      return;
    }

    // Checking 100% uniqueness of No Kontainer
    const duplicates = state.kontainers.some(k => k.no_kontainer.toUpperCase() === cleanNo);
    if (duplicates) {
      triggerNoti('error', `Nomor Kontainer "${cleanNo}" sudah terdaftar dalam sistem (Wajib Unik 100%)`);
      return;
    }

    const newKont: Kontainer = {
      no_kontainer: cleanNo,
      id_customer: kontCustId,
      id_tipe: kontTipeId,
      id_ukuran: kontUkuranId,
      status_aktif: true
    };

    const updated = { ...state, kontainers: [...state.kontainers, newKont] };
    onStateChange(updated);
    setKontNo('');
    triggerNoti('sukses', `Kontainer "${cleanNo}" berhasil terdaftar`);
  };

  const handleToggleKontainerStatus = (no: string) => {
    const updated = state.kontainers.map(k =>
      k.no_kontainer === no
        ? { ...k, status_aktif: !k.status_aktif }
        : k
    );
    onStateChange({ ...state, kontainers: updated });
    const k = state.kontainers.find(x => x.no_kontainer === no);
    const nextVal = k?.status_aktif ? 'dinonaktifkan' : 'diaktifkan';
    triggerNoti('sukses', `Kontainer "${no}" berhasil ${nextVal}`);
  };

  // 5. Tarif Handlers
  const handleAddTarif = (e: React.FormEvent) => {
    e.preventDefault();
    if (!tarifCustId || !tarifTipeId || !tarifUkuranId) {
      triggerNoti('error', 'Wajib memilih Customer, Tipe, dan Ukuran Kontainer');
      return;
    }
    
    const monthlyRate = parseFloat(tarifBulan) || 0;
    const dailyRate = parseFloat(tarifHari) || 0;
    
    if (monthlyRate <= 0 && dailyRate <= 0) {
      triggerNoti('error', 'Minimal salah satu dari Tarif Bulanan atau Tarif Harian harus diisi (> 0)');
      return;
    }

    const validDate = parseInputDate(tarifStart) || utcTime.split('T')[0];
    const parsedEnd = tarifEnd.trim() ? parseInputDate(tarifEnd) : null;

    // Check duplicate active tarif (overlapping starting dates with open tanggal_akhir_berlaku)
    const existingIdx = state.tarifs.findIndex(t => 
      t.id_customer === tarifCustId && 
      t.id_tipe === tarifTipeId && 
      t.id_ukuran === tarifUkuranId && 
      t.tanggal_akhir_berlaku === null
    );

    let updatedTarifs = [...state.tarifs];
    if (existingIdx !== -1) {
      // Auto close previous active tariff
      const prev = updatedTarifs[existingIdx];
      // Close it yesterday relative to new starting date
      const d = new Date(validDate);
      d.setDate(d.getDate() - 1);
      const prevEnd = d.toISOString().split('T')[0];
      
      updatedTarifs[existingIdx] = {
        ...prev,
        tanggal_akhir_berlaku: prevEnd
      };
    }

    const newTarif: TarifSewa = {
      id_tarif: 'trf_' + Date.now(),
      id_customer: tarifCustId,
      id_tipe: tarifTipeId,
      id_ukuran: tarifUkuranId,
      tarif_bulanan: monthlyRate,
      tarif_harian: dailyRate,
      tanggal_mulai_berlaku: validDate,
      tanggal_akhir_berlaku: parsedEnd,
      status_aktif: true
    };

    const updated = { ...state, tarifs: [...updatedTarifs, newTarif] };
    onStateChange(updated);
    
    // reset form
    setTarifBulan('');
    setTarifHari('');
    setTarifEnd('');
    triggerNoti('sukses', 'Tarif berhasil disimpan dalam sistem database');
  };

  const handleToggleTarifStatus = (id: string) => {
    const updated = state.tarifs.map(t =>
      t.id_tarif === id
        ? { ...t, status_aktif: t.status_aktif === false ? true : false }
        : t
    );
    onStateChange({ ...state, tarifs: updated });
    const t = state.tarifs.find(x => x.id_tarif === id);
    const nextVal = t?.status_aktif === false ? 'diaktifkan' : 'dinonaktifkan';
    triggerNoti('sukses', `Tarif berhasil ${nextVal}`);
  };

  // Helper getters for display names (ID disembunyikan!)
  const getCustomerName = (id: string) => {
    const c = state.customers.find(x => x.id_customer === id);
    return c ? c.nama_customer : '-';
  };
  const getTipeName = (id: string) => {
    const t = state.tipes.find(x => x.id_tipe === id);
    return t ? t.nama_tipe : '-';
  };
  const getUkuranDesc = (id: string) => {
    const u = state.ukurans.find(x => x.id_ukuran === id);
    return u ? u.deskripsi_ukuran : '-';
  };

  return (
    <div className="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden" id="master-panel-container">
      {/* Mini tabs */}
      <div className="flex border-b border-slate-100 bg-slate-50/75 p-1 gap-1">
        {[
          { id: 'customer', label: isSewaIn ? '1. Master Vendor / Owner' : '1. Master Customer' },
          { id: 'tipe', label: '2. Master Tipe Kontainer' },
          { id: 'ukuran', label: '3. Master Ukuran' },
          { id: 'tarif', label: isSewaIn ? '4. Master Tarif Sewa In' : '4. Master Tarif Sewa' },
          { id: 'kontainer', label: '5. Master Kontainer' },
        ].map((tab) => (
          <button
            key={tab.id}
            id={`tab-sub-${tab.id}`}
            onClick={() => {
              setActiveSubTab(tab.id as any);
              setSearchQuery('');
            }}
            className={`px-4 py-2 text-sm font-medium rounded-lg transition-all cursor-pointer ${
              activeSubTab === tab.id
                ? 'bg-indigo-600 text-white shadow-xs ring-1 ring-black/5'
                : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/50'
            }`}
          >
            {tab.label}
          </button>
        ))}
      </div>

      <div className="p-6">
        {/* Simple Notification Banner */}
        {noti && (
          <div
            id="master-notification-banner"
            className={`mb-4 p-3 rounded-xl flex items-center gap-2 border text-sm ${
              noti.type === 'sukses'
                ? 'bg-emerald-50 border-emerald-200 text-emerald-800'
                : 'bg-rose-50 border-rose-100 text-rose-800'
            }`}
          >
            <Check className="w-4 h-4 shrink-0" />
            <span className="font-medium text-xs">{noti.msg}</span>
          </div>
        )}

        {/* 1. MASTER CUSTOMER */}
        {activeSubTab === 'customer' && (
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-8" id="master-customer-section">
            <div className="lg:col-span-1 bg-slate-50/70 p-5 rounded-2xl border border-slate-100 h-fit">
              <h3 className="font-semibold text-slate-800 text-sm mb-4">{isSewaIn ? 'Input Vendor / Owner Baru' : 'Input Customer Baru'}</h3>
              <form onSubmit={handleAddCustomer} className="space-y-4">
                <div>
                  <label className="block text-xs font-medium text-slate-600 mb-1">{isSewaIn ? 'Nama Vendor / Owner' : 'Nama Customer'}</label>
                  <input
                    id="input-cust-name"
                    type="text"
                    required
                    value={custName}
                    onChange={(e) => setCustName(e.target.value)}
                    placeholder={isSewaIn ? 'Contoh: PT. Temas Line' : 'Contoh: CV. Samudera Raya'}
                    className="w-full text-sm border border-slate-200 rounded-xl px-3.5 py-2 focus:ring-2 bg-white focus:ring-indigo-500/20 focus:border-indigo-500"
                  />
                </div>
                <button
                  id="btn-submit-customer"
                  type="submit"
                  className="w-full inline-flex items-center justify-center text-white font-medium text-sm px-4 py-2 rounded-xl transition-colors cursor-pointer bg-indigo-600 hover:bg-indigo-700"
                >
                  <Save className="w-4 h-4 mr-1.5" /> Simpan {isSewaIn ? 'Vendor / Owner' : 'Customer'}
                </button>
              </form>
            </div>

            <div className="lg:col-span-2 space-y-4">
              <div className="flex items-center gap-2 border border-slate-150 rounded-xl px-3 py-1.5 bg-slate-50/30 max-w-md">
                <Search className="w-4 h-4 text-slate-400" />
                <input
                  id="search-customer"
                  type="text"
                  placeholder={isSewaIn ? 'Cari nama vendor / owner...' : 'Cari nama customer (FreetextSearch)...'}
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  className="bg-transparent border-none outline-none text-sm w-full text-slate-800 font-medium focus:text-slate-900"
                />
              </div>

              <div className="border border-slate-100 rounded-xl overflow-hidden bg-white">
                <table className="w-full text-left border-collapse text-xs" id="table-customer">
                  <thead>
                    <tr className="bg-slate-50 border-b border-slate-100 text-slate-600 font-medium">
                      <th className="p-3">{isSewaIn ? 'Nama Vendor / Owner' : 'Nama Customer'}</th>
                      <th className="p-3 text-right">Status / Aksi</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-slate-100">
                    {state.customers
                      .filter(c => c.nama_customer.toLowerCase().includes(searchQuery.toLowerCase()))
                      .map((cust) => (
                        <tr key={cust.id_customer} className="hover:bg-slate-50 text-slate-700">
                          <td className="p-3 font-medium text-sm">{cust.nama_customer}</td>
                          <td className="p-3 text-right">
                            <span className={`inline-flex items-center px-2 py-0.5 rounded-full text-2xs font-semibold ${
                              cust.status_aktif !== false
                                ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
                                : 'bg-slate-100 text-slate-500 border border-slate-200'
                            }`}>
                              {cust.status_aktif !== false ? 'Aktif' : 'Non-Aktif'}
                            </span>
                            <button
                              onClick={() => handleToggleCustomerStatus(cust.id_customer)}
                              className={`ml-2 text-2xs px-2 py-1 rounded border transition-all font-medium cursor-pointer ${
                                cust.status_aktif !== false
                                  ? 'text-amber-600 hover:text-amber-700 hover:bg-amber-50 border-amber-200 bg-white'
                                  : 'text-indigo-600 hover:text-indigo-700 hover:bg-indigo-50 border-indigo-200 bg-white'
                              }`}
                            >
                              {cust.status_aktif !== false ? 'Nonaktifkan' : 'Aktifkan'}
                            </button>
                          </td>
                        </tr>
                      ))}
                    {state.customers.length === 0 && (
                      <tr>
                        <td colSpan={2} className="p-8 text-center text-slate-400">Tidak ada data customer</td>
                      </tr>
                    )}
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        )}

        {/* 2. MASTER TIPE */}
        {activeSubTab === 'tipe' && (
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-8" id="master-tipe-section">
            <div className="lg:col-span-1 bg-slate-50/70 p-5 rounded-2xl border border-slate-100 h-fit">
              <h3 className="font-semibold text-slate-800 text-sm mb-4">Input Tipe Baru</h3>
              <form onSubmit={handleAddTipe} className="space-y-4">
                <div>
                  <label className="block text-xs font-medium text-slate-600 mb-1">Nama Tipe Kontainer</label>
                  <input
                    id="input-tipe-name"
                    type="text"
                    required
                    value={tipeName}
                    onChange={(e) => setTipeName(e.target.value)}
                    placeholder="Contoh: Dry, Reefer, Flat Rack"
                    className="w-full text-sm border border-slate-200 rounded-xl px-3.5 py-2 focus:ring-2 bg-white focus:ring-indigo-500/20 focus:border-indigo-500"
                  />
                </div>
                <button
                  id="btn-submit-tipe"
                  type="submit"
                  className="w-full inline-flex items-center justify-center text-white font-medium text-sm px-4 py-2 rounded-xl transition-colors cursor-pointer bg-indigo-600 hover:bg-indigo-700"
                >
                  <Save className="w-4 h-4 mr-1.5" /> Simpan Tipe
                </button>
              </form>
            </div>

            <div className="lg:col-span-2 space-y-4">
              <div className="flex items-center gap-2 border border-slate-150 rounded-xl px-3 py-1.5 bg-slate-50/30 max-w-md">
                <Search className="w-4 h-4 text-slate-400" />
                <input
                  id="search-tipe"
                  type="text"
                  placeholder="Cari tipe..."
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  className="bg-transparent border-none outline-none text-sm w-full text-slate-800 font-medium focus:text-slate-900"
                />
              </div>

              <div className="border border-slate-100 rounded-xl overflow-hidden bg-white">
                <table className="w-full text-left border-collapse text-xs" id="table-tipe">
                  <thead>
                    <tr className="bg-slate-50 border-b border-slate-100 text-slate-600 font-medium">
                      <th className="p-3">Nama Tipe</th>
                      <th className="p-3 text-right">Status / Aksi</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-slate-100">
                    {state.tipes
                      .filter(t => t.nama_tipe.toLowerCase().includes(searchQuery.toLowerCase()))
                      .map((tipe) => (
                        <tr key={tipe.id_tipe} className="hover:bg-slate-50 text-slate-700">
                          <td className="p-3 font-medium text-sm">{tipe.nama_tipe}</td>
                          <td className="p-3 text-right">
                            <span className={`inline-flex items-center px-2 py-0.5 rounded-full text-2xs font-semibold ${
                              tipe.status_aktif !== false
                                ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
                                : 'bg-slate-100 text-slate-500 border border-slate-200'
                            }`}>
                              {tipe.status_aktif !== false ? 'Aktif' : 'Non-Aktif'}
                            </span>
                            <button
                              onClick={() => handleToggleTipeStatus(tipe.id_tipe)}
                              className={`ml-2 text-2xs px-2 py-1 rounded border transition-all font-medium cursor-pointer ${
                                tipe.status_aktif !== false
                                  ? 'text-amber-600 hover:text-amber-700 hover:bg-amber-50 border-amber-200 bg-white'
                                  : 'text-indigo-600 hover:text-indigo-700 hover:bg-indigo-50 border-indigo-200 bg-white'
                              }`}
                            >
                              {tipe.status_aktif !== false ? 'Nonaktifkan' : 'Aktifkan'}
                            </button>
                          </td>
                        </tr>
                      ))}
                    {state.tipes.length === 0 && (
                      <tr>
                        <td colSpan={2} className="p-8 text-center text-slate-400">Tidak ada data tipe</td>
                      </tr>
                    )}
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        )}

        {/* 3. MASTER UKURAN */}
        {activeSubTab === 'ukuran' && (
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-8" id="master-ukuran-section">
            <div className="lg:col-span-1 bg-slate-50/70 p-5 rounded-2xl border border-slate-100 h-fit">
              <h3 className="font-semibold text-slate-800 text-sm mb-4">Input Ukuran</h3>
              <form onSubmit={handleAddUkuran} className="space-y-4">
                <div>
                  <label className="block text-xs font-medium text-slate-600 mb-1">Ukuran (Cukup Angka)</label>
                  <input
                    id="input-ukuran-desc"
                    type="text"
                    required
                    value={ukuranDesc}
                    onChange={(e) => setUkuranDesc(e.target.value)}
                    placeholder="Ketik 20 atau 40 (Sistem otomatis ubah ke 20' / 40')"
                    className="w-full text-sm border border-slate-200 rounded-xl px-3.5 py-2 focus:ring-2 bg-white focus:ring-indigo-500/20 focus:border-indigo-500"
                  />
                  <span className="text-[10px] text-slate-500 mt-1 block font-mono">
                    Sistem otomatis menambahkan petik tunggal (&#39;) agar langsung tersaji sebagai format 20&#39; / 40&#39;.
                  </span>
                </div>
                <button
                  id="btn-submit-ukuran"
                  type="submit"
                  className="w-full inline-flex items-center justify-center text-white font-medium text-sm px-4 py-2 rounded-xl transition-colors cursor-pointer bg-indigo-600 hover:bg-indigo-700"
                >
                  <Save className="w-4 h-4 mr-1.5" /> Simpan Ukuran
                </button>
              </form>
            </div>

            <div className="lg:col-span-2 space-y-4">
              <div className="flex items-center gap-2 border border-slate-150 rounded-xl px-3 py-1.5 bg-slate-50/30 max-w-md">
                <Search className="w-4 h-4 text-slate-400" />
                <input
                  id="search-ukuran"
                  type="text"
                  placeholder="Cari ukuran kontainer..."
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  className="bg-transparent border-none outline-none text-sm w-full text-slate-800 font-medium focus:text-slate-900"
                />
              </div>

              <div className="border border-slate-100 rounded-xl overflow-hidden bg-white">
                <table className="w-full text-left border-collapse text-xs" id="table-ukuran">
                  <thead>
                    <tr className="bg-slate-50 border-b border-slate-100 text-slate-600 font-medium">
                      <th className="p-3">Deskripsi Ukuran</th>
                      <th className="p-3 text-right">Status / Aksi</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-slate-100">
                    {state.ukurans
                      .filter(u => u.deskripsi_ukuran.toLowerCase().includes(searchQuery.toLowerCase()))
                      .map((sz) => (
                        <tr key={sz.id_ukuran} className="hover:bg-slate-50 text-slate-700">
                          <td className="p-3 font-mono text-sm font-semibold text-emerald-800">{sz.deskripsi_ukuran}</td>
                          <td className="p-3 text-right">
                            <span className={`inline-flex items-center px-2 py-0.5 rounded-full text-2xs font-semibold ${
                              sz.status_aktif !== false
                                ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
                                : 'bg-slate-100 text-slate-500 border border-slate-200'
                            }`}>
                              {sz.status_aktif !== false ? 'Aktif' : 'Non-Aktif'}
                            </span>
                            <button
                              onClick={() => handleToggleUkuranStatus(sz.id_ukuran)}
                              className={`ml-2 text-2xs px-2 py-1 rounded border transition-all font-medium cursor-pointer ${
                                sz.status_aktif !== false
                                  ? 'text-amber-600 hover:text-amber-700 hover:bg-amber-50 border-amber-200 bg-white'
                                  : 'text-indigo-600 hover:text-indigo-700 hover:bg-indigo-50 border-indigo-200 bg-white'
                              }`}
                            >
                              {sz.status_aktif !== false ? 'Nonaktifkan' : 'Aktifkan'}
                            </button>
                          </td>
                        </tr>
                      ))}
                    {state.ukurans.length === 0 && (
                      <tr>
                        <td colSpan={2} className="p-8 text-center text-slate-400">Tidak ada data ukuran</td>
                      </tr>
                    )}
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        )}

        {/* 4. MASTER KONTAINER */}
        {activeSubTab === 'kontainer' && (
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-8" id="master-kontainer-section">
            <div className="lg:col-span-1 bg-slate-50/70 p-5 rounded-2xl border border-slate-100 h-fit">
              <h3 className="font-semibold text-slate-800 text-sm mb-4">Daftar Kontainer Baru</h3>
              <form onSubmit={handleAddKontainer} className="space-y-4">
                <div>
                  <label className="block text-xs font-medium text-slate-600 mb-1">Nomor Kontainer (100% Unik)</label>
                  <input
                    id="input-kontainer-no"
                    type="text"
                    required
                    value={kontNo}
                    onChange={(e) => setKontNo(e.target.value)}
                    placeholder="Contoh: GLDU7252828"
                    className="w-full text-sm border border-slate-200 rounded-xl px-3.5 py-2 bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-mono focus:text-slate-900 focus:outline-none"
                  />
                </div>

                <div>
                  <label className="block text-xs font-medium text-slate-600 mb-1">Vendor Terkait (Pemilik)</label>
                  <SearchableSelect
                    id="select-kontainer-customer"
                    placeholder={isSewaIn ? '-- Pilih Vendor --' : '-- Pilih Customer --'}
                    searchPlaceholder={isSewaIn ? "Ketik nama vendor..." : "Ketik nama customer..."}
                    value={kontCustId}
                    onChange={(val) => setKontCustId(val)}
                    options={state.customers.filter(c => c.status_aktif !== false).map(c => ({
                      value: c.id_customer,
                      label: c.nama_customer
                    }))}
                  />
                </div>

                <div className="grid grid-cols-2 gap-3">
                  <div>
                    <label className="block text-xs font-medium text-slate-600 mb-1">Tipe</label>
                    <SearchableSelect
                      id="select-kontainer-tipe"
                      placeholder="-- Pilih Tipe --"
                      searchPlaceholder="Cari tipe..."
                      value={kontTipeId}
                      onChange={(val) => setKontTipeId(val)}
                      options={state.tipes.filter(t => t.status_aktif !== false).map(t => ({
                        value: t.id_tipe,
                        label: t.nama_tipe
                      }))}
                    />
                  </div>
                  <div>
                    <label className="block text-xs font-medium text-slate-600 mb-1">Ukuran</label>
                    <SearchableSelect
                      id="select-kontainer-ukuran"
                      placeholder="-- Pilih Ukuran --"
                      searchPlaceholder="Cari ukuran..."
                      value={kontUkuranId}
                      onChange={(val) => setKontUkuranId(val)}
                      options={state.ukurans.filter(u => u.status_aktif !== false).map(u => ({
                        value: u.id_ukuran,
                        label: u.deskripsi_ukuran
                      }))}
                    />
                  </div>
                </div>

                <button
                  id="btn-submit-kontainer"
                  type="submit"
                  className="w-full inline-flex items-center justify-center text-white font-medium text-sm px-4 py-2 rounded-xl transition-colors cursor-pointer bg-indigo-600 hover:bg-indigo-700"
                >
                  <Save className="w-4 h-4 mr-1.5" /> Simpan Kontainer
                </button>
              </form>
            </div>

            <div className="lg:col-span-2 space-y-4">
              <div className="flex items-center gap-2 border border-slate-150 rounded-xl px-3 py-1.5 bg-slate-50/30 max-w-md">
                <Search className="w-4 h-4 text-slate-400" />
                <input
                  id="search-kontainer"
                  type="text"
                  placeholder={isSewaIn ? 'Cari No Kontainer atau Vendor...' : 'Cari No Kontainer atau Customer...'}
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  className="bg-transparent border-none outline-none text-sm w-full text-slate-800 font-medium focus:text-slate-900"
                />
              </div>

              <div className="border border-slate-100 rounded-xl overflow-hidden bg-white">
                <table className="w-full text-left border-collapse text-xs" id="table-kontainer">
                  <thead>
                    <tr className="bg-slate-50 border-b border-slate-100 text-slate-600 font-medium font-mono">
                      <th className="p-3">NO. KONTAINER</th>
                      <th className="p-3">{isSewaIn ? 'VENDOR / OWNER' : 'CUSTOMER ASOSIASI'}</th>
                      <th className="p-3">TIPE</th>
                      <th className="p-3">UKURAN</th>
                      <th className="p-3">STATUS</th>
                      <th className="p-3 text-right">AKSI</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-slate-100">
                    {(() => {
                      const filteredKontainers = state.kontainers.filter(k => 
                        k.no_kontainer.toLowerCase().includes(searchQuery.toLowerCase()) ||
                        getCustomerName(k.id_customer).toLowerCase().includes(searchQuery.toLowerCase())
                      );
                      const totalKontPages = Math.ceil(filteredKontainers.length / kontPageSize);
                      const paginatedKontainers = filteredKontainers.slice((kontPage - 1) * kontPageSize, kontPage * kontPageSize);

                      if (filteredKontainers.length === 0) {
                        return (
                          <tr>
                            <td colSpan={6} className="p-8 text-center text-slate-400">Tidak ada data kontainer</td>
                          </tr>
                        );
                      }

                      return (
                        <>
                          {paginatedKontainers.map((kont) => (
                            <tr key={kont.no_kontainer} className="hover:bg-slate-50 text-slate-700">
                              <td className="p-3 font-mono font-bold text-sm tracking-wide text-slate-900">{kont.no_kontainer}</td>
                              <td className="p-3 text-slate-600">{getCustomerName(kont.id_customer)}</td>
                              <td className="p-3 font-medium">{getTipeName(kont.id_tipe)}</td>
                              <td className="p-3 font-mono font-semibold text-emerald-800">{getUkuranDesc(kont.id_ukuran)}</td>
                              <td className="p-3">
                                <span className={`inline-flex items-center px-2 py-0.5 rounded-full text-2xs font-semibold ${
                                  kont.status_aktif !== false
                                    ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
                                    : 'bg-slate-100 text-slate-500 border border-slate-200'
                                }`}>
                                  {kont.status_aktif !== false ? 'Aktif' : 'Non-Aktif'}
                                </span>
                              </td>
                              <td className="p-3 text-right">
                                <button
                                  onClick={() => handleToggleKontainerStatus(kont.no_kontainer)}
                                  className={`text-2xs px-2 py-1 rounded border transition-all font-medium cursor-pointer ${
                                    kont.status_aktif !== false
                                      ? 'text-amber-600 hover:text-amber-700 hover:bg-amber-50 border-amber-200 bg-white'
                                      : 'text-indigo-600 hover:text-indigo-700 hover:bg-indigo-50 border-indigo-200 bg-white'
                                  }`}
                                >
                                  {kont.status_aktif !== false ? 'Nonaktifkan' : 'Aktifkan'}
                                </button>
                              </td>
                            </tr>
                          ))}
                        </>
                      );
                    })()}
                  </tbody>
                </table>
              </div>

              {/* KONTAINER PAGINATION CONTROLS */}
              {(() => {
                const filtered = state.kontainers.filter(k => 
                  k.no_kontainer.toLowerCase().includes(searchQuery.toLowerCase()) ||
                  getCustomerName(k.id_customer).toLowerCase().includes(searchQuery.toLowerCase())
                );
                const totalPages = Math.ceil(filtered.length / kontPageSize);
                if (totalPages <= 1) return null;

                return (
                  <div className="flex flex-wrap items-center justify-between gap-3 p-3 bg-slate-50 border border-slate-100 rounded-xl mt-3 text-xs text-slate-600 font-sans">
                    <span>Menampilkan <strong>{Math.min(filtered.length, (kontPage - 1) * kontPageSize + 1)}-{Math.min(filtered.length, kontPage * kontPageSize)}</strong> dari <strong>{filtered.length}</strong> kontainer</span>
                    <div className="flex items-center gap-1 font-mono">
                      <button
                        type="button"
                        disabled={kontPage === 1}
                        onClick={() => setKontPage(1)}
                        className="px-2 py-1 rounded bg-white border border-slate-200 hover:bg-slate-100 disabled:opacity-50 disabled:hover:bg-white cursor-pointer"
                      >
                        ⏮ First
                      </button>
                      <button
                        type="button"
                        disabled={kontPage === 1}
                        onClick={() => setKontPage(p => Math.max(1, p - 1))}
                        className="px-2.5 py-1 rounded bg-white border border-slate-200 hover:bg-slate-100 disabled:opacity-50 disabled:hover:bg-white cursor-pointer font-semibold"
                      >
                        Sebelumnya
                      </button>
                      <span className="px-3 py-1 font-bold text-slate-800 bg-slate-100 rounded-lg font-sans">
                        Hal {kontPage} / {totalPages}
                      </span>
                      <button
                        type="button"
                        disabled={kontPage === totalPages}
                        onClick={() => setKontPage(p => Math.min(totalPages, p + 1))}
                        className="px-2.5 py-1 rounded bg-white border border-slate-200 hover:bg-slate-100 disabled:opacity-50 disabled:hover:bg-white cursor-pointer font-semibold"
                      >
                        Berikutnya
                      </button>
                      <button
                        type="button"
                        disabled={kontPage === totalPages}
                        onClick={() => setKontPage(totalPages)}
                        className="px-2 py-1 rounded bg-white border border-slate-200 hover:bg-slate-100 disabled:opacity-50 disabled:hover:bg-white cursor-pointer"
                      >
                        ⏭ Last
                      </button>
                    </div>
                  </div>
                );
              })()}
            </div>
          </div>
        )}

        {/* 5. MASTER TARIF SEWA */}
        {activeSubTab === 'tarif' && (
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-8" id="master-tarif-section">
            <div className="lg:col-span-1 bg-slate-50/70 p-5 rounded-2xl border border-slate-100 h-fit">
              <h3 className="font-semibold text-slate-800 text-sm mb-4">Set Master Tarif</h3>
              <form onSubmit={handleAddTarif} className="space-y-4">
                <div>
                  <label className="block text-xs font-medium text-slate-600 mb-1">{isSewaIn ? 'Vendor' : 'Customer'}</label>
                  <SearchableSelect
                    id="select-tarif-customer"
                    placeholder={isSewaIn ? '-- Pilih Vendor --' : '-- Pilih Customer --'}
                    searchPlaceholder="Cari..."
                    value={tarifCustId}
                    onChange={(val) => setTarifCustId(val)}
                    options={state.customers.filter(c => c.status_aktif !== false).map(c => ({
                      value: c.id_customer,
                      label: c.nama_customer
                    }))}
                  />
                </div>

                <div className="grid grid-cols-2 gap-3">
                  <div>
                    <label className="block text-xs font-medium text-slate-600 mb-1">Tipe</label>
                    <SearchableSelect
                      id="select-tarif-tipe"
                      placeholder="-- Pilih Tipe --"
                      searchPlaceholder="Cari..."
                      value={tarifTipeId}
                      onChange={(val) => setTarifTipeId(val)}
                      options={state.tipes.filter(t => t.status_aktif !== false).map(t => ({
                        value: t.id_tipe,
                        label: t.nama_tipe
                      }))}
                    />
                  </div>
                  <div>
                    <label className="block text-xs font-medium text-slate-600 mb-1">Ukuran</label>
                    <SearchableSelect
                      id="select-tarif-ukuran"
                      placeholder="-- Pilih Ukuran --"
                      searchPlaceholder="Cari..."
                      value={tarifUkuranId}
                      onChange={(val) => setTarifUkuranId(val)}
                      options={state.ukurans.filter(u => u.status_aktif !== false).map(u => ({
                        value: u.id_ukuran,
                        label: u.deskripsi_ukuran
                      }))}
                    />
                  </div>
                </div>

                <div className="grid grid-cols-2 gap-3" id="tariff-prices">
                  <div>
                    <label className="block text-xs font-medium text-slate-600 mb-1">Tarif Bulanan (Rp)</label>
                    <input
                      id="input-tarif-bulan"
                      type="number"
                      value={tarifBulan}
                      onChange={(e) => setTarifBulan(e.target.value)}
                      placeholder="Contoh: 1500000"
                      className="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-mono text-slate-800"
                    />
                  </div>
                  <div>
                    <label className="block text-xs font-medium text-slate-600 mb-1">Tarif Harian (Rp)</label>
                    <input
                      id="input-tarif-hari"
                      type="number"
                      value={tarifHari}
                      onChange={(e) => setTarifHari(e.target.value)}
                      placeholder="Contoh: 75000"
                      className="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-mono text-slate-800"
                    />
                  </div>
                </div>

                <div className="grid grid-cols-2 gap-3" id="tariff-dates">
                  <div>
                    <label className="block text-xs font-medium text-slate-600 mb-1">Tanggal Mulai Berlaku</label>
                    <FormDateInput
                      id="input-tarif-tanggal-mulai"
                      value={tarifStart}
                      onChange={(val) => setTarifStart(val)}
                      placeholder="dd/mm/yyyy"
                      className="w-full text-sm border border-slate-200 rounded-xl px-3.5 py-2 bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-800"
                    />
                  </div>
                  <div>
                    <label className="block text-xs font-medium text-slate-600 mb-1">Berlaku Sampai Tanggal</label>
                    <FormDateInput
                      id="input-tarif-tanggal-akhir"
                      value={tarifEnd}
                      onChange={(val) => setTarifEnd(val)}
                      placeholder="dd/mm/yyyy"
                      className="w-full text-sm border border-slate-200 rounded-xl px-3.5 py-2 bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-800"
                    />
                  </div>
                </div>

                <button
                  id="btn-submit-tarif"
                  type="submit"
                  className="w-full inline-flex items-center justify-center text-white font-medium text-sm px-4 py-2 rounded-xl transition-colors cursor-pointer bg-indigo-600 hover:bg-indigo-700"
                >
                  <Save className="w-4 h-4 mr-1.5" /> Simpan Tarif
                </button>
              </form>
            </div>

            <div className="lg:col-span-2 space-y-4">
              <div className="flex items-center gap-2 border border-slate-150 rounded-xl px-3 py-1.5 bg-slate-50/30 max-w-md">
                <Search className="w-4 h-4 text-slate-400" />
                <input
                  id="search-tarif"
                  type="text"
                  placeholder="Cari tarif vendor..."
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  className="bg-transparent border-none outline-none text-sm w-full text-slate-800 font-medium focus:text-slate-900"
                />
              </div>

              <div className="border border-slate-100 rounded-xl overflow-hidden bg-white">
                <table className="w-full text-left border-collapse text-xs" id="table-tarif">
                  <thead>
                    <tr className="bg-slate-50 border-b border-slate-100 text-slate-600 font-medium">
                      <th className="p-3">Vendor / Owner</th>
                      <th className="p-3">Tipe &amp; Ukuran</th>
                      <th className="p-3 text-right">Tarif Bulanan</th>
                      <th className="p-3 text-right">Tarif Harian</th>
                      <th className="p-3 text-center">Berlaku Mulai</th>
                      <th className="p-3 text-center">Berlaku Selesai</th>
                      <th className="p-3 text-right">Status / Aksi</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-slate-100">
                    {state.tarifs
                      .filter(t => getCustomerName(t.id_customer).toLowerCase().includes(searchQuery.toLowerCase()))
                      .map((tarif) => (
                        <tr key={tarif.id_tarif} className="hover:bg-slate-50 text-slate-700">
                          <td className="p-3 font-semibold text-slate-900">{getCustomerName(tarif.id_customer)}</td>
                          <td className="p-3 font-medium text-slate-600">
                            {getTipeName(tarif.id_tipe)} / <span className="font-mono text-emerald-800">{getUkuranDesc(tarif.id_ukuran)}</span>
                          </td>
                          <td className="p-3 text-right text-slate-800 font-mono font-medium">
                            {tarif.tarif_bulanan ? formatRupiah(tarif.tarif_bulanan) : '-'}
                          </td>
                          <td className="p-3 text-right text-slate-800 font-mono font-medium">
                            {tarif.tarif_harian ? formatRupiah(tarif.tarif_harian) : '-'}
                          </td>
                          <td className="p-3 text-center text-slate-500 font-mono italic">{formatIndoDate(tarif.tanggal_mulai_berlaku)}</td>
                          <td className="p-3 text-center text-slate-500 font-mono italic">
                            {tarif.tanggal_akhir_berlaku ? (
                              <span className="line-through text-slate-400">{formatIndoDate(tarif.tanggal_akhir_berlaku)}</span>
                            ) : (
                              <span className="bg-emerald-50 text-emerald-700 px-1.5 py-0.5 rounded-sm text-[10px] font-bold">Saat Ini</span>
                            )}
                          </td>
                          <td className="p-3 text-right">
                            <div className="flex flex-col items-end gap-1">
                              <span className={`inline-flex items-center px-2 py-0.5 rounded-full text-2xs font-semibold ${
                                tarif.status_aktif !== false
                                  ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
                                  : 'bg-slate-100 text-slate-500 border border-slate-200'
                              }`}>
                                {tarif.status_aktif !== false ? 'Aktif' : 'Non-Aktif'}
                              </span>
                              <button
                                onClick={() => handleToggleTarifStatus(tarif.id_tarif)}
                                className={`text-2xs px-1.5 py-0.5 rounded border transition-all font-medium cursor-pointer ${
                                  tarif.status_aktif !== false
                                    ? 'text-amber-600 hover:text-amber-700 hover:bg-amber-50 border-amber-200 bg-white'
                                    : 'text-indigo-600 hover:text-indigo-700 hover:bg-indigo-50 border-indigo-200 bg-white'
                                }`}
                              >
                                {tarif.status_aktif !== false ? 'Nonaktifkan' : 'Aktifkan'}
                              </button>
                            </div>
                          </td>
                        </tr>
                      ))}
                    {state.tarifs.length === 0 && (
                      <tr>
                        <td colSpan={7} className="p-8 text-center text-slate-400">Belum ada pengaturan tarif sewa bulanan atau harian</td>
                      </tr>
                    )}
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
