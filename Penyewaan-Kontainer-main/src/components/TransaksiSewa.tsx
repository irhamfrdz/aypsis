import React, { useState, useEffect } from 'react';
import { AppState, compileAllPeriods } from '../dataStore';
import { Sewa } from '../types';
import { formatRupiah, formatIndoDate, parseInputDate, formatEntryDate, generateBillingPeriodsForSewa } from '../utils';
import { Plus, CheckCircle, RefreshCcw, Calendar, DollarSign, Archive, Save, Search, AlertCircle, Trash2, Edit } from 'lucide-react';
import SearchableSelect from './SearchableSelect';

interface TransaksiSewaProps {
  state: AppState;
  onStateChange: (updated: AppState) => void;
  utcTime: string;
}

export default function TransaksiSewa({ state, onStateChange, utcTime }: TransaksiSewaProps) {
  const isSewaIn = true;
  // Rent form
  const [selectedNoKontainer, setSelectedNoKontainer] = useState('');
  const [selectedCustomerId, setSelectedCustomerId] = useState('');
  const [tanggalSewaInput, setTanggalSewaInput] = useState('');
  const [jenisTarif, setJenisTarif] = useState<'Bulanan' | 'Harian'>('Bulanan');
  const [customTarifBulanan, setCustomTarifBulanan] = useState(0);
  const [customTarifHarian, setCustomTarifHarian] = useState(0);
  const [rentalNotes, setRentalNotes] = useState('');
  const [usePpn, setUsePpn] = useState(true);

  // Search
  const [searchQuery, setSearchQuery] = useState('');

  // Pagination support
  const [sewaPage, setSewaPage] = useState(1);
  const sewaPageSize = 20;

  // Reset page when search term changes
  useEffect(() => {
    setSewaPage(1);
  }, [searchQuery]);

  // Return Container modal/state
  const [activeReturnSewaId, setActiveReturnSewaId] = useState<string | null>(null);
  const [tanggalKembaliInput, setTanggalKembaliInput] = useState('');

  // Delete transaction confirmation state
  const [sewaIdToDeleteConfirm, setSewaIdToDeleteConfirm] = useState<string | null>(null);

  // Notification state
  const [noti, setNoti] = useState<{ type: 'sukses' | 'error'; msg: string } | null>(null);

  const triggerNoti = (type: 'sukses' | 'error', msg: string) => {
    setNoti({ type, msg });
    setTimeout(() => setNoti(null), 4000);
  };

  const checkHasInvoices = (sewaId: string): boolean => {
    const periodsForSewa = compileAllPeriods(state, utcTime).filter(p => p.id_sewa === sewaId);
    return periodsForSewa.some(p => p.status_bayar !== 'Belum Ditagih' || p.nomor_invoice_grup);
  };

  // Editing Sewa modals and states
  const [editingSewa, setEditingSewa] = useState<Sewa | null>(null);
  const [editTanggalSewa, setEditTanggalSewa] = useState('');
  const [editTanggalKembali, setEditTanggalKembali] = useState('');
  const [editJenisTarif, setEditJenisTarif] = useState<'Bulanan' | 'Harian'>('Bulanan');
  const [editTarifBulanan, setEditTarifBulanan] = useState(0);
  const [editTarifHarian, setEditTarifHarian] = useState(0);
  const [editRentalNotes, setEditRentalNotes] = useState('');
  const [editUsePpn, setEditUsePpn] = useState(true);

  // Pre-load default tarif if container changes
  useEffect(() => {
    if (!selectedNoKontainer) return;
    const kont = state.kontainers.find(k => k.no_kontainer === selectedNoKontainer);
    if (!kont) return;

    // Set matching customer
    setSelectedCustomerId(kont.id_customer);

    // Look for active rate for this customer, container type, container size
    const activeTarif = state.tarifs.find(t => 
      t.id_customer === kont.id_customer &&
      t.id_tipe === kont.id_tipe &&
      t.id_ukuran === kont.id_ukuran &&
      t.tanggal_akhir_berlaku === null
    );

    if (activeTarif) {
      setCustomTarifBulanan(activeTarif.tarif_bulanan);
      setCustomTarifHarian(activeTarif.tarif_harian);
      if (activeTarif.tarif_bulanan > 0) {
        setJenisTarif('Bulanan');
      } else if (activeTarif.tarif_harian > 0) {
        setJenisTarif('Harian');
      }
    } else {
      setCustomTarifBulanan(0);
      setCustomTarifHarian(0);
    }
  }, [selectedNoKontainer, state.tarifs, state.kontainers]);

  // Submit Rent Transaction
  const handleCreateSewa = (e: React.FormEvent) => {
    e.preventDefault();
    if (!selectedNoKontainer) {
      triggerNoti('error', 'Wajib memilih No Kontainer');
      return;
    }

    const validTglSewa = parseInputDate(tanggalSewaInput);
    if (!validTglSewa) {
      triggerNoti('error', 'Format Tanggal Sewa tidak valid (Wajib dd/mm/yyyy)');
      return;
    }

    // Checking if container is currently already RENTED/ACTIVE in another Sewa
    const curActive = state.sewas.find(s => s.no_kontainer === selectedNoKontainer && (s.status_sewa === 'Aktif' || !s.tanggal_kembali));
    if (curActive) {
      triggerNoti('error', `Kontainer "${selectedNoKontainer}" belum dikembalikan dari sewa sebelumnya (masih aktif). Harap isi tanggal kembali transaksi sebelumnya terlebih dahulu sebelum merekam sewa baru!`);
      return;
    }

    // Duplicate check: same container and same start date
    const isDup = state.sewas.some(s => s.no_kontainer === selectedNoKontainer && s.tanggal_sewa === validTglSewa);
    if (isDup) {
      triggerNoti('error', `Duplikat Dikunci: Transaksi sewa untuk Kontainer "${selectedNoKontainer}" dengan Tanggal Mulai "${formatIndoDate(validTglSewa)}" sudah pernah direkam sebelumnya.`);
      return;
    }

    const priceBulan = customTarifBulanan;
    const priceHari = customTarifHarian;

    if (jenisTarif === 'Bulanan' && priceBulan <= 0) {
      triggerNoti('error', 'Tarif Bulanan tidak boleh kosong/nol untuk jenis tarif bulanan');
      return;
    }
    if (jenisTarif === 'Harian' && priceHari <= 0) {
      triggerNoti('error', 'Tarif Harian tidak boleh kosong/nol untuk jenis tarif harian');
      return;
    }

    const listExisting = state.sewas.filter(s => s.no_kontainer === selectedNoKontainer);
    const cycleNum = listExisting.length + 1;
    const cycleNumStr = String(cycleNum).padStart(2, '0');
    
    const getExcelSerialDate = (isoStr: string): number => {
      const d = new Date(isoStr);
      const epoch = new Date('1899-12-30');
      const diffMs = d.getTime() - epoch.getTime();
      return Math.floor(diffMs / (24 * 60 * 60 * 1000));
    };
    const excelDateSerial = getExcelSerialDate(validTglSewa);
    const uniqueIdSewa = `${selectedNoKontainer}${excelDateSerial}${cycleNumStr}`;

    const newSewa: Sewa = {
      id_sewa: uniqueIdSewa,
      no_kontainer: selectedNoKontainer,
      id_customer: selectedCustomerId,
      tanggal_sewa: validTglSewa,
      tanggal_kembali: null,
      tarif_bulanan: priceBulan,
      tarif_harian: priceHari,
      jenis_tarif: jenisTarif,
      status_sewa: 'Aktif',
      catatan: rentalNotes.trim(),
      non_ppn: !usePpn
    };

    const updated = {
      ...state,
      sewas: [...state.sewas, newSewa]
    };
    onStateChange(updated);

    // Reset Form
    setSelectedNoKontainer('');
    setTanggalSewaInput('');
    setRentalNotes('');
    setUsePpn(true);
    triggerNoti('sukses', `Transaksi Sewa Kontainer "${selectedNoKontainer}" sukses dibuat`);
  };

  // Submit Return Container
  const handleReturnSewa = (e: React.FormEvent) => {
    e.preventDefault();
    if (!activeReturnSewaId) return;

    const sewaObj = state.sewas.find(s => s.id_sewa === activeReturnSewaId);
    if (!sewaObj) return;

    if (checkHasInvoices(activeReturnSewaId)) {
      triggerNoti('error', 'Siklus transaksi & pengembalian tidak boleh di-edit/proses karena sudah ada tagihannya. Harus lepas tagihannya dulu baru bisa edit tgl ambil atau harga dll.');
      return;
    }

    const validTglKembali = parseInputDate(tanggalKembaliInput);
    if (!validTglKembali) {
      triggerNoti('error', 'Format Tanggal Kembali tidak valid (Wajib dd/mm/yyyy)');
      return;
    }

    // Validation: Return date must be >= start date
    if (validTglKembali < sewaObj.tanggal_sewa) {
      triggerNoti('error', `Tanggal Kembali (${formatIndoDate(validTglKembali)}) tidak boleh mendahului Tanggal Sewa (${formatIndoDate(sewaObj.tanggal_sewa)})`);
      return;
    }

    // Close the rentals
    const updatedSewas = state.sewas.map(s => {
      if (s.id_sewa === activeReturnSewaId) {
        return {
          ...s,
          tanggal_kembali: validTglKembali,
          status_sewa: 'Selesai' as const
        };
      }
      return s;
    });

    const updated = {
      ...state,
      sewas: updatedSewas
    };
    onStateChange(updated);

    setActiveReturnSewaId(null);
    setTanggalKembaliInput('');
    triggerNoti('sukses', `Kontainer ${sewaObj.no_kontainer} berhasil dikembalikan pada tanggal ${formatIndoDate(validTglKembali)}! Tagihan prorata akhir otomatis dikalkulasikan.`);
  };

  // Delete transaction logic
  const handleDeleteSewa = (idSewa: string) => {
    const sewaObj = state.sewas.find(s => s.id_sewa === idSewa);
    if (!sewaObj) return;

    if (checkHasInvoices(idSewa)) {
      triggerNoti('error', 'Siklus transaksi tidak boleh dihapus karena sudah ada tagihannya. Harus lepas tagihannya dulu.');
      return;
    }

    // Generate billing periods for this sewa to clear corresponding payment state override keys if any
    const periods = generateBillingPeriodsForSewa(sewaObj, utcTime);
    const updatedOverrides = { ...state.paymentOverrides };
    periods.forEach(p => {
      if (updatedOverrides[p.id_tagihan]) {
        delete updatedOverrides[p.id_tagihan];
      }
    });

    const updatedSewas = state.sewas.filter(s => s.id_sewa !== idSewa);

    onStateChange({
      ...state,
      sewas: updatedSewas,
      paymentOverrides: updatedOverrides
    });

    setSewaIdToDeleteConfirm(null);
    triggerNoti('sukses', `Transaksi sewa untuk Kontainer ${sewaObj.no_kontainer} berhasil dihapus beserta seluruh status tagihannya.`);
  };

  const handleOpenEdit = (sewa: Sewa) => {
    if (checkHasInvoices(sewa.id_sewa)) {
      triggerNoti('error', 'Siklus transaksi & pengembalian tidak boleh di-edit karena sudah ada tagihannya. Harus lepas tagihannya dulu baru bisa edit tgl ambil atau harga dll.');
      return;
    }
    setEditingSewa(sewa);
    setEditTanggalSewa(formatEntryDate(sewa.tanggal_sewa));
    setEditTanggalKembali(sewa.tanggal_kembali ? formatEntryDate(sewa.tanggal_kembali) : '');
    setEditJenisTarif(sewa.jenis_tarif);
    setEditTarifBulanan(sewa.tarif_bulanan);
    setEditTarifHarian(sewa.tarif_harian);
    setEditRentalNotes(sewa.catatan || '');
    setEditUsePpn(sewa.non_ppn !== true);
  };

  const handleSaveEditSewa = (e: React.FormEvent) => {
    e.preventDefault();
    if (!editingSewa) return;

    if (checkHasInvoices(editingSewa.id_sewa)) {
      triggerNoti('error', 'Siklus transaksi & pengembalian tidak boleh di-edit karena sudah ada tagihannya. Harus lepas tagihannya dulu baru bisa edit tgl ambil atau harga dll.');
      return;
    }

    const validTglSewa = parseInputDate(editTanggalSewa);
    if (!validTglSewa) {
      triggerNoti('error', 'Format Tanggal Sewa tidak valid (Wajib dd/mm/yyyy)');
      return;
    }

    let validTglKembali: string | null = null;
    if (editTanggalKembali.trim()) {
      validTglKembali = parseInputDate(editTanggalKembali);
      if (!validTglKembali) {
        triggerNoti('error', 'Format Tanggal Kembali tidak valid (Wajib dd/mm/yyyy atau kosong)');
        return;
      }
      if (validTglKembali < validTglSewa) {
        triggerNoti('error', `Tanggal Kembali (${editTanggalKembali}) tidak boleh mendahului Tanggal Sewa (${editTanggalSewa})`);
        return;
      }
    }

    const updatedStatus: 'Selesai' | 'Aktif' = validTglKembali ? 'Selesai' : 'Aktif';

    const updatedSewas = state.sewas.map(s => {
      if (s.id_sewa === editingSewa.id_sewa) {
        return {
          ...s,
          tanggal_sewa: validTglSewa,
          tanggal_kembali: validTglKembali,
          jenis_tarif: editJenisTarif,
          tarif_bulanan: editTarifBulanan,
          tarif_harian: editTarifHarian,
          status_sewa: updatedStatus,
          catatan: editRentalNotes.trim(),
          non_ppn: !editUsePpn
        };
      }
      return s;
    });

    onStateChange({
      ...state,
      sewas: updatedSewas
    });

    setEditingSewa(null);
    triggerNoti('sukses', `Kontainer ${editingSewa.no_kontainer} berhasil diperbarui.`);
  };

  // Helper getters
  const getCustomerName = (id: string) => {
    const c = state.customers.find(x => x.id_customer === id);
    return c ? c.nama_customer : '-';
  };

  const getContainerDetails = (no: string) => {
    const k = state.kontainers.find(x => x.no_kontainer === no);
    if (!k) return '-';
    const t = state.tipes.find(x => x.id_tipe === k.id_tipe)?.nama_tipe || '-';
    const s = state.ukurans.find(x => x.id_ukuran === k.id_ukuran)?.deskripsi_ukuran || '-';
    return `${t} (${s})`;
  };

  const getRentDurationText = (sewa: Sewa) => {
    const periods = generateBillingPeriodsForSewa(sewa, utcTime);
    if (periods.length === 0) return '0 Bulan';
    const completedCount = periods.filter(p => p.tipe_tarif === 'BULANAN' || (p.tipe_tarif === 'HARIAN' && sewa.status_sewa === 'Selesai')).length;
    const prorateCount = periods.filter(p => p.tipe_tarif === 'PRORATE').length;
    
    let text = `${periods.length} Siklus`;
    if (sewa.jenis_tarif === 'Bulanan') {
      text = `${completedCount} Bulan Lumpsum`;
      if (prorateCount > 0) {
        const lastP = periods[periods.length - 1];
        text += ` + Prorate ${lastP.jumlah_hari} Hari`;
      }
    } else {
      const totalDays = periods.reduce((sum, p) => sum + p.jumlah_hari, 0);
      text = `${totalDays} Hari`;
    }
    return text;
  };

  const calculateTotalRentAmount = (sewa: Sewa) => {
    const periods = generateBillingPeriodsForSewa(sewa, utcTime);
    return periods.reduce((acc, p) => acc + p.jumlah_tagihan, 0);
  };

  return (
    <div className="space-y-6" id="transaksi-sewa-container">
      {/* System Warning / Notification */}
      {noti && (
        <div
          id="sewa-noti-banner"
          className={`p-3 rounded-xl flex items-center gap-2 border text-sm ${
            noti.type === 'sukses'
              ? 'bg-emerald-50 border-emerald-200 text-emerald-800'
              : 'bg-rose-50 border-rose-100 text-rose-800'
          }`}
        >
          <CheckCircle className="w-4 h-4 shrink-0 text-emerald-600" />
          <span className="font-semibold text-xs">{noti.msg}</span>
        </div>
      )}

      <div className="grid grid-cols-1 xl:grid-cols-3 gap-6">
        
        {/* CREATE SEWA FORM */}
        <div className="xl:col-span-1 bg-white p-6 rounded-2xl border border-slate-100 shadow-xs h-fit">
          <div className="flex items-center gap-2 mb-4">
            <Archive className="w-5 h-5 text-emerald-600" />
            <h3 className="font-bold text-slate-800 text-sm">{isSewaIn ? 'Pencatatan Sewa In Baru' : 'Sewa Kontainer Baru'}</h3>
          </div>

          <form onSubmit={handleCreateSewa} className="space-y-4">
            <div>
              <label className="block text-xs font-semibold text-slate-600 mb-1">Mulai Cari &amp; Pilih Kontainer</label>
              <SearchableSelect
                id="select-sewa-kontainer"
                placeholder="-- Cari No Kontainer --"
                searchPlaceholder="Ketik No Kontainer untuk mencari..."
                value={selectedNoKontainer}
                onChange={(val) => setSelectedNoKontainer(val)}
                options={state.kontainers
                  .filter(k => k.status_aktif !== false)
                  .map(k => {
                    const isActiveRent = state.sewas.some(s => s.no_kontainer === k.no_kontainer && s.status_sewa === 'Aktif');
                    return {
                      value: k.no_kontainer,
                      label: `${k.no_kontainer} ${isActiveRent ? '(SEDANG DISEWA)' : `[${isSewaIn ? 'Vendor' : 'Owner'}: ${getCustomerName(k.id_customer)}]`}`,
                      disabled: isActiveRent
                    };
                  })}
              />
            </div>

            {selectedNoKontainer && (
              <div className="p-3 bg-emerald-50/50 rounded-xl border border-emerald-100 text-xs space-y-1 text-slate-700">
                <p><strong>Spesifikasi:</strong> {getContainerDetails(selectedNoKontainer)}</p>
                <p><strong>{isSewaIn ? 'Vendor Asosiasi:' : 'Customer Asosiasi:'}</strong> {getCustomerName(selectedCustomerId)}</p>
              </div>
            )}

            <div>
              <label className="block text-xs font-semibold text-slate-600 mb-1">Tanggal Mulai Sewa</label>
              <div className="relative">
                <input
                  id="input-sewa-tanggal"
                  type="text"
                  required
                  value={tanggalSewaInput}
                  onChange={(e) => setTanggalSewaInput(e.target.value)}
                  placeholder="dd/mm/yyyy (Cth: 30/09/2022)"
                  className="w-full text-sm border border-slate-200 rounded-xl pl-9 pr-3 py-2 bg-white text-slate-800 font-mono"
                />
                <Calendar className="w-4 h-4 text-slate-400 absolute left-3 top-2.5" />
              </div>
            </div>

            <div className="bg-slate-50 p-3 rounded-xl border border-slate-100 space-y-3">
              <div>
                <label className="block text-xs font-semibold text-slate-600 mb-1">Jenis Tarif Dasar</label>
                <div className="grid grid-cols-2 gap-2 text-xs">
                  <label className="flex items-center gap-1.5 p-2 bg-white border border-slate-200 rounded-lg cursor-pointer">
                    <input
                      id="radio-tarif-bulanan"
                      type="radio"
                      name="jenisTarif"
                      checked={jenisTarif === 'Bulanan'}
                      onChange={() => setJenisTarif('Bulanan')}
                      className="text-emerald-600 focus:ring-emerald-500"
                    />
                    <span>Bulanan</span>
                  </label>
                  <label className="flex items-center gap-1.5 p-2 bg-white border border-slate-200 rounded-lg cursor-pointer">
                    <input
                      id="radio-tarif-harian"
                      type="radio"
                      name="jenisTarif"
                      checked={jenisTarif === 'Harian'}
                      onChange={() => setJenisTarif('Harian')}
                      className="text-emerald-600 focus:ring-emerald-500"
                    />
                    <span>Harian</span>
                  </label>
                </div>
              </div>

              <div className="grid grid-cols-2 gap-2">
                <div>
                  <label className="block text-[10px] font-semibold text-slate-600 mb-0.5">Bulanan (Rp)</label>
                  <input
                    id="input-sewa-tarif-bulan"
                    type="number"
                    value={customTarifBulanan}
                    onChange={(e) => setCustomTarifBulanan(Math.max(0, parseInt(e.target.value) || 0))}
                    className="w-full text-xs border border-slate-200 rounded-lg p-1.5 bg-white font-mono"
                  />
                </div>
                <div>
                  <label className="block text-[10px] font-semibold text-slate-600 mb-0.5">Harian (Rp)</label>
                  <input
                    id="input-sewa-tarif-hari"
                    type="number"
                    value={customTarifHarian}
                    onChange={(e) => setCustomTarifHarian(Math.max(0, parseInt(e.target.value) || 0))}
                    className="w-full text-xs border border-slate-200 rounded-lg p-1.5 bg-white font-mono"
                  />
                </div>
              </div>
            </div>

            <div>
              <label className="block text-xs font-semibold text-slate-600 mb-1">Catatan Sewa (Opsional)</label>
              <textarea
                id="input-sewa-catatan"
                rows={2}
                value={rentalNotes}
                onChange={(e) => setRentalNotes(e.target.value)}
                placeholder="Tulis slip jalan atau catatan di sini..."
                className="w-full text-sm border border-slate-200 rounded-xl px-3 py-1.5 bg-white text-slate-800"
              />
            </div>

            <div className="flex items-center gap-2 py-1.5 bg-slate-50 p-3 rounded-xl border border-slate-100">
              <input
                id="checkbox-sewa-ppn"
                type="checkbox"
                checked={usePpn}
                onChange={(e) => setUsePpn(e.target.checked)}
                className="w-4 h-4 text-emerald-600 focus:ring-emerald-500 rounded border-slate-300"
              />
              <div className="flex flex-col">
                <label htmlFor="checkbox-sewa-ppn" className="text-xs font-semibold text-slate-700 cursor-pointer select-none">
                  Default Pakai PPN (11%)
                </label>
                <span className="text-[10px] text-slate-500">Bisa di-uncheck jika transaksi Non-PPN</span>
              </div>
            </div>

            <button
              id="btn-save-sewa"
              type="submit"
              className="w-full inline-flex items-center justify-center text-white font-semibold text-sm px-4 py-2.5 rounded-xl transition-colors cursor-pointer shadow-xs bg-indigo-600 hover:bg-indigo-700"
            >
              <Save className="w-4 h-4 mr-1.5" /> Konfirmasi Sewa Kontainer
            </button>
          </form>
        </div>

        {/* LIST SEWA TRANSACTIONS */}
        <div className="xl:col-span-2 space-y-4">
          <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h3 className="font-bold text-slate-800 text-sm flex items-center gap-1.5">
              <span>Siklus Transaksi &amp; Pengembalian</span>
              <span className="text-[10px] font-mono px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">{state.sewas.length} Rekor</span>
            </h3>

            <div className="flex items-center gap-2 border border-slate-150 rounded-xl px-3 py-1.5 bg-white w-full sm:max-w-xs">
              <Search className="w-4 h-4 text-slate-400" />
              <input
                id="search-rentals"
                type="text"
                placeholder="Cari No Kontainer/Customer..."
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                className="bg-transparent border-none outline-none text-xs w-full text-slate-800 font-medium focus:text-slate-900"
              />
            </div>
          </div>

          <div className="space-y-3">
            {(() => {
              const filteredSewas = state.sewas.filter(s => 
                s.no_kontainer.toLowerCase().includes(searchQuery.toLowerCase()) ||
                getCustomerName(s.id_customer).toLowerCase().includes(searchQuery.toLowerCase())
              );
              
              const totalSewaPages = Math.ceil(filteredSewas.length / sewaPageSize);
              const paginatedSewas = filteredSewas.slice((sewaPage - 1) * sewaPageSize, sewaPage * sewaPageSize);

              return (
                <>
                  {paginatedSewas.map((sewa) => {
                    const periodsForSewa = compileAllPeriods(state, utcTime).filter(p => p.id_sewa === sewa.id_sewa);
                    
                    // Is there any bill created?
                    const hasInvoices = periodsForSewa.some(p => p.status_bayar !== 'Belum Ditagih');
                    // Are all periods fully paid?
                    const isFullyPaid = periodsForSewa.length > 0 && periodsForSewa.every(p => p.status_bayar === 'Lunas');
                    // Are there unpaid invoices?
                    const hasUnpaidInvoice = periodsForSewa.some(p => p.status_bayar === 'Belum Bayar' || p.status_bayar === 'Pranota');

                    let billingBadge = (
                      <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                        Belum Ditagih
                      </span>
                    );

                    if (isFullyPaid) {
                      billingBadge = (
                        <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                          ✓ Lunas
                        </span>
                      );
                    } else if (hasUnpaidInvoice) {
                      billingBadge = (
                        <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-sky-100 text-sky-800 border border-sky-200">
                          ● Sudah Ditagih
                        </span>
                      );
                    } else if (hasInvoices) {
                      billingBadge = (
                        <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                          ● Bayar Parsial
                        </span>
                      );
                    }

                    // Calculate accurate Rent Totals with Overrides if present, falls back to Estimates
                    const totalCalculated = periodsForSewa.reduce((sum, p) => {
                      return sum + (p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : p.jumlah_tagihan);
                    }, 0);

                    // Calculate outstanding as only the unbilled periods (Belum Ditagih), since invoiced ones are transferred/deducted.
                    const totalOutstanding = periodsForSewa.reduce((sum, p) => {
                      if (p.status_bayar === 'Belum Ditagih') {
                        return sum + (p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : p.jumlah_tagihan);
                      }
                      return sum;
                    }, 0);
                    const isAktif = sewa.status_sewa === 'Aktif';

                    return (
                      <div
                        key={sewa.id_sewa}
                        className={`bg-white rounded-2xl p-5 border transition-all ${
                          isAktif ? 'border-amber-100 bg-amber-50/5' : 'border-slate-100 bg-white'
                        }`}
                        id={`sewa-card-${sewa.no_kontainer}`}
                      >
                        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-dashed border-slate-100 pb-3 mb-3">
                          <div className="flex items-center gap-2 flex-wrap">
                            <span className="font-mono font-bold text-base tracking-wide text-slate-900">{sewa.no_kontainer}</span>
                            <span className="text-slate-300">|</span>
                            <span className="text-xs font-medium text-slate-500">{getContainerDetails(sewa.no_kontainer)}</span>
                            <span className="text-slate-300">|</span>
                            {billingBadge}
                          </div>

                          <div className="flex items-center gap-1.5 flex-wrap">
                            {isAktif ? (
                              <>
                                <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-800">
                                  ● Sedang Disewa
                                </span>
                                <button
                                  id={`btn-return-${sewa.id_sewa}`}
                                  onClick={() => {
                                    if (checkHasInvoices(sewa.id_sewa)) {
                                      triggerNoti('error', 'Siklus transaksi & pengembalian tidak boleh di-proses pengembalian karena sudah ada tagihannya. Harus lepas tagihannya dulu baru bisa edit tgl ambil atau harga dll.');
                                      return;
                                    }
                                    setActiveReturnSewaId(sewa.id_sewa);
                                    setTanggalKembaliInput(formatEntryDate(utcTime.split('T')[0]));
                                  }}
                                  className="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white transition-colors cursor-pointer"
                                >
                                  <RefreshCcw className="w-3.5 h-3.5 mr-1" /> Kembalikkan
                                </button>
                              </>
                            ) : (
                              <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-800">
                                ✓ Selesai
                              </span>
                            )}

                            <button
                              id={`btn-edit-sewa-${sewa.id_sewa}`}
                              onClick={() => {
                                if (checkHasInvoices(sewa.id_sewa)) {
                                  triggerNoti('error', 'Siklus transaksi & pengembalian tidak boleh di-edit karena sudah ada tagihannya. Harus lepas tagihannya dulu baru bisa edit tgl ambil atau harga dll.');
                                  return;
                                }
                                handleOpenEdit(sewa);
                              }}
                              className="inline-flex items-center p-1.5 text-xs font-semibold rounded-lg bg-sky-50 hover:bg-sky-100 text-sky-700 hover:text-sky-800 border border-sky-100 transition-colors cursor-pointer"
                              title="Edit Transaksi Sewa"
                            >
                              <Edit className="w-3.5 h-3.5" />
                            </button>

                            <button
                              id={`btn-delete-sewa-${sewa.id_sewa}`}
                              onClick={() => {
                                if (checkHasInvoices(sewa.id_sewa)) {
                                  triggerNoti('error', 'Siklus transaksi tidak boleh dihapus karena sudah ada tagihannya. Harus lepas tagihannya dulu.');
                                  return;
                                }
                                setSewaIdToDeleteConfirm(sewa.id_sewa);
                              }}
                              className="inline-flex items-center p-1.5 text-xs font-semibold rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 hover:text-rose-800 border border-rose-100 transition-colors cursor-pointer"
                              title="Hapus Transaksi Sewa"
                            >
                              <Trash2 className="w-3.5 h-3.5" />
                            </button>
                          </div>
                        </div>

                        <div className="grid grid-cols-2 sm:grid-cols-5 gap-4 text-xs">
                          <div>
                            <p className="text-slate-500">{isSewaIn ? 'Vendor / Owner' : 'Penyewa / Customer'}</p>
                            <p className="font-semibold text-slate-800">{getCustomerName(sewa.id_customer)}</p>
                          </div>

                          <div>
                            <p className="text-slate-500">Rentang Sewa</p>
                            <p className="font-mono font-medium text-slate-800">
                              {formatIndoDate(sewa.tanggal_sewa)} - {sewa.tanggal_kembali ? formatIndoDate(sewa.tanggal_kembali) : 'Saat Ini'}
                            </p>
                          </div>

                          <div>
                            <p className="text-slate-500">Hitungan Durasi</p>
                            <p className="font-semibold text-slate-700">{getRentDurationText(sewa)}</p>
                          </div>

                          <div>
                            <p className="text-slate-500 text-right">Akumulasi Tarif</p>
                            <p className="font-mono font-bold text-slate-900 text-right text-sm">
                              {formatRupiah(totalCalculated)}
                            </p>
                          </div>

                          <div>
                            <p className="text-rose-500 text-right font-semibold">Outstanding</p>
                            <p className="font-mono font-extrabold text-rose-700 text-right text-sm">
                              {formatRupiah(totalOutstanding)}
                            </p>
                          </div>
                        </div>

                        {sewa.catatan && (
                          <div className="mt-3 text-[11px] text-slate-500 bg-slate-50 p-2 rounded-lg italic">
                            Catatan: {sewa.catatan}
                          </div>
                        )}

                        {/* Breakdown micro-table of periods for transparency! */}
                        <div className="mt-4 border border-slate-100 rounded-lg overflow-hidden bg-slate-50/30">
                          <div className="bg-slate-100 p-2 text-[10px] font-bold text-slate-600 font-mono">
                            PERINCIAN PERIODE AUTO-BILLING:
                          </div>
                          <table className="w-full text-xs text-left text-slate-600">
                            <thead className="bg-slate-50 text-[10px] border-b border-slate-100 font-mono">
                              <tr>
                                <th className="p-1.5 pl-3">PERIODE</th>
                                <th className="p-1.5">MASA (DD MMM YY)</th>
                                <th className="p-1.5 pl-2">STATUS &amp; NO. TAGIHAN / BUKTI LACAK</th>
                                <th className="p-1.5 text-center font-semibold">HARI</th>
                                <th className="p-1.5 text-right font-semibold">TARIF</th>
                                <th className="p-1.5 text-right pr-3 font-semibold font-mono">TOTAL</th>
                              </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 font-mono text-[11px]">
                              {periodsForSewa.map((p) => (
                                <tr key={p.id_tagihan}>
                                  <td className="p-1.5 pl-3 font-semibold text-[10px] text-slate-700">BULAN KE-{p.bulan_ke}</td>
                                  <td className="p-1.5 text-slate-500">{formatIndoDate(p.tanggal_awal)} - {formatIndoDate(p.tanggal_akhir)}</td>
                                  <td className="p-1.5 pl-2 font-sans select-all">
                                    {p.status_bayar === 'Belum Ditagih' && (
                                      <div className="flex flex-col gap-0.5">
                                        <span className="inline-flex items-center self-start px-1.5 py-0.5 rounded text-[9px] font-bold bg-slate-100 text-slate-500 border border-slate-200 uppercase">
                                          Belum Ditagih
                                        </span>
                                        {p.nomor_invoice_grup && (
                                          <span className="text-[10px] text-indigo-700 font-mono font-bold mt-0.5" title="Draf Tagihan Terkait">Draf: {p.nomor_invoice_grup}</span>
                                        )}
                                      </div>
                                    )}
                                    {p.status_bayar === 'Pranota' && (
                                      <div className="flex flex-col gap-0.5">
                                        <span className="inline-flex items-center self-start px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-200 uppercase">
                                          Pranota (Draft)
                                        </span>
                                        {p.nomor_invoice_grup && (
                                          <span className="text-[10px] text-slate-600 font-mono font-bold mt-0.5">INV: {p.nomor_invoice_grup}</span>
                                        )}
                                        {p.nomor_pranota && (
                                          <span className="text-[10px] text-slate-600 font-mono font-bold">Pranota: {p.nomor_pranota}</span>
                                        )}
                                      </div>
                                    )}
                                    {p.status_bayar === 'Belum Bayar' && (
                                      <div className="flex flex-col gap-0.5">
                                        <span className="inline-flex items-center self-start px-1.5 py-0.5 rounded text-[9px] font-bold bg-sky-50 text-sky-700 border border-sky-200 uppercase animate-pulse">
                                          Sudah Ditagih
                                        </span>
                                        <div className="text-[9.5px] text-slate-705 font-mono mt-0.5 space-y-0.5 leading-normal">
                                          {p.nomor_invoice_grup && <div className="font-bold text-sky-900">INV: {p.nomor_invoice_grup}</div>}
                                          {p.nomor_pranota && <div className="text-slate-655 font-semibold">Pranota: {p.nomor_pranota}</div>}
                                        </div>
                                      </div>
                                    )}
                                    {p.status_bayar === 'Lunas' && (
                                      <div className="flex flex-col gap-0.5">
                                        <span className="inline-flex items-center self-start px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-250 uppercase">
                                          Sudah Dibayar
                                        </span>
                                        <div className="text-[9.5px] text-slate-705 font-mono mt-0.5 space-y-0.5 leading-normal">
                                          {p.nomor_invoice_grup && <div className="font-bold text-emerald-900">INV: {p.nomor_invoice_grup}</div>}
                                          {p.nomor_pranota && <div className="text-slate-655 font-semibold">Pranota: {p.nomor_pranota}</div>}
                                          {p.nomor_bayar && <div className="text-slate-655 font-semibold">Bukti: {p.nomor_bayar}</div>}
                                          {p.tanggal_bayar && <div className="text-slate-500 text-[9px]">Bayar: {formatIndoDate(p.tanggal_bayar)}</div>}
                                        </div>
                                      </div>
                                    )}
                                  </td>
                                  <td className="p-1.5 text-center text-slate-600 font-mono">{p.jumlah_hari}</td>
                                  <td className="p-1.5 text-right font-bold text-slate-500 text-[10px] font-mono">{p.tipe_tarif}</td>
                                  <td className="p-1.5 text-right font-bold text-slate-800 pr-3 font-mono">
                                    {p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? (
                                      <span className="flex flex-col items-end">
                                        <span className="text-[9px] text-slate-400 font-normal line-through">{formatRupiah(p.jumlah_tagihan)}</span>
                                        <span>{formatRupiah(p.jumlah_tagihan_override)}</span>
                                      </span>
                                    ) : (
                                      formatRupiah(p.jumlah_tagihan)
                                    )}
                                  </td>
                                </tr>
                              ))}
                            </tbody>
                          </table>
                        </div>
                      </div>
                    );
                  })}

                  {filteredSewas.length === 0 && (
                    <div className="p-12 text-center text-slate-400 border border-dashed border-slate-100 rounded-2xl bg-white">
                      Belum ada transaksi sewa kontainer aktif yang sesuai pencarian.
                    </div>
                  )}

                  {/* SEWA PAGINATION CONTROLS */}
                  {totalSewaPages > 1 && (
                    <div className="flex flex-wrap items-center justify-between gap-3 p-4 bg-white border border-slate-150 rounded-2xl shadow-xs text-xs text-slate-600 mt-4">
                      <span>Menampilkan <strong>{Math.min(filteredSewas.length, (sewaPage - 1) * sewaPageSize + 1)}-{Math.min(filteredSewas.length, sewaPage * sewaPageSize)}</strong> dari <strong>{filteredSewas.length}</strong> kontrak sewa</span>
                      <div className="flex items-center gap-1 font-mono">
                        <button
                          disabled={sewaPage === 1}
                          onClick={() => setSewaPage(1)}
                          className="px-2 py-1 rounded bg-slate-50 border border-slate-150 hover:bg-slate-100 disabled:opacity-50 disabled:hover:bg-slate-50 cursor-pointer"
                        >
                          ⏮ First
                        </button>
                        <button
                          disabled={sewaPage === 1}
                          onClick={() => setSewaPage(p => Math.max(1, p - 1))}
                          className="px-2.5 py-1 rounded bg-slate-50 border border-slate-150 hover:bg-slate-100 disabled:opacity-50 disabled:hover:bg-slate-50 cursor-pointer font-semibold"
                        >
                          Sebelumnya
                        </button>
                        <span className="px-3 py-1 font-bold text-slate-800 bg-slate-100 rounded-lg">
                          Hal {sewaPage} / {totalSewaPages}
                        </span>
                        <button
                          disabled={sewaPage === totalSewaPages}
                          onClick={() => setSewaPage(p => Math.min(totalSewaPages, p + 1))}
                          className="px-2.5 py-1 rounded bg-slate-50 border border-slate-150 hover:bg-slate-100 disabled:opacity-50 disabled:hover:bg-slate-50 cursor-pointer font-semibold"
                        >
                          Berikutnya
                        </button>
                        <button
                          disabled={sewaPage === totalSewaPages}
                          onClick={() => setSewaPage(totalSewaPages)}
                          className="px-2 py-1 rounded bg-slate-50 border border-slate-150 hover:bg-slate-100 disabled:opacity-50 disabled:hover:bg-slate-50 cursor-pointer"
                        >
                          ⏭ Last
                        </button>
                      </div>
                    </div>
                  )}
                </>
              );
            })()}
            {state.sewas.length === 0 && (
              <div className="p-12 text-center text-slate-400 border border-dashed border-slate-100 rounded-2xl bg-white">
                Belum ada transaksi sewa kontainer aktif. Wajib daftar dan pasang tarif di Master dahulu.
              </div>
            )}
          </div>
        </div>
      </div>

      {/* MODAL RETURN CONTAINER */}
      {activeReturnSewaId && (
        <div className="fixed inset-0 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 z-50 animate-fade-in" id="modal-return-container">
          <div className="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 border border-slate-100 shadow-xl">
            <div className="flex items-center gap-2 text-emerald-800">
              <CheckCircle className="w-5 h-5" />
              <h3 className="font-bold text-slate-800 text-base">Konfirmasi Pengembalian</h3>
            </div>

            <p className="text-slate-600 text-xs leading-relaxed">
              Silakan masukkan tanggal kontainer benar-benar diterima kembali untuk memicu kalkulasi final hari sewa serta proris bulanan berjalan.
            </p>

            <form onSubmit={handleReturnSewa} className="space-y-4">
              <div>
                <label className="block text-xs font-semibold text-slate-600 mb-1">Tanggal Pengembalian (dd/mm/yyyy)</label>
                <div className="relative">
                  <input
                    id="input-return-date"
                    type="text"
                    required
                    value={tanggalKembaliInput}
                    onChange={(e) => setTanggalKembaliInput(e.target.value)}
                    placeholder="Contoh: 10/05/2023"
                    className="w-full text-sm border border-slate-200 rounded-xl pl-9 pr-3 py-2 bg-slate-50"
                  />
                  <Calendar className="w-4 h-4 text-slate-400 absolute left-3 top-2.5" />
                </div>
              </div>

              <div className="flex items-center gap-2 justify-end pt-2">
                <button
                  id="btn-cancel-return"
                  type="button"
                  onClick={() => setActiveReturnSewaId(null)}
                  className="px-4 py-2 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors cursor-pointer"
                >
                  Batal
                </button>
                <button
                  id="btn-confirm-return"
                  type="submit"
                  className="px-4 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition-colors cursor-pointer"
                >
                  Proses Pengembalian
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      {/* MODAL CONFIRM DELETE TRANSACTION */}
      {sewaIdToDeleteConfirm && (
        <div className="fixed inset-0 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 z-50 animate-fade-in" id="modal-delete-sewa-confirm">
          <div className="bg-white rounded-2xl max-w-sm w-full p-6 space-y-4 border border-slate-100 shadow-xl">
            <div className="flex items-center gap-2 text-rose-600">
              <AlertCircle className="w-5 h-5" />
              <h3 className="font-bold text-slate-800 text-base">Hapus Transaksi Sewa?</h3>
            </div>

            <div className="text-slate-600 text-xs space-y-2 leading-relaxed">
              <p>
                Apakah Anda yakin ingin menghapus transaksi sewa untuk kontainer ini?
              </p>
              {(() => {
                const targetSewa = state.sewas.find(s => s.id_sewa === sewaIdToDeleteConfirm);
                if (!targetSewa) return null;
                return (
                  <div className="bg-rose-50/50 p-2.5 rounded-xl border border-rose-100 font-mono text-[10px] space-y-1">
                    <p><strong>Kontainer:</strong> {targetSewa.no_kontainer}</p>
                    <p><strong>Customer:</strong> {getCustomerName(targetSewa.id_customer)}</p>
                    <p><strong>Masa Sewa:</strong> {formatIndoDate(targetSewa.tanggal_sewa)} - {targetSewa.tanggal_kembali ? formatIndoDate(targetSewa.tanggal_kembali) : 'Aktif'}</p>
                  </div>
                );
              })()}
              <p className="text-rose-600 font-semibold text-[11px]">
                Tindakan ini tidak dapat dibatalkan. Seluruh daftar tagihan periode bulanan/harian terkait yang sudah digenerate juga akan dibersihkan!
              </p>
            </div>

            <div className="flex items-center gap-2 justify-end pt-2">
              <button
                id="btn-cancel-delete"
                type="button"
                onClick={() => setSewaIdToDeleteConfirm(null)}
                className="px-4 py-2 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors cursor-pointer"
              >
                Batal
              </button>
              <button
                id="btn-confirm-delete"
                type="button"
                onClick={() => handleDeleteSewa(sewaIdToDeleteConfirm)}
                className="px-4 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition-colors cursor-pointer"
              >
                Ya, Hapus Permanen
              </button>
            </div>
          </div>
        </div>
      )}

      {/* MODAL EDIT TRANSACTION */}
      {editingSewa && (
        <div className="fixed inset-0 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 z-50 animate-fade-in" id="modal-edit-sewa">
          <div className="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 border border-slate-100 shadow-xl overflow-y-auto max-h-[90vh]">
            <div className="flex items-center gap-2 text-sky-800 border-b border-slate-150 pb-3">
              <Edit className="w-5 h-5 text-sky-600" />
              <div>
                <h3 className="font-bold text-slate-800 text-base">Edit Transaksi Sewa</h3>
                <p className="text-[10px] text-slate-500 font-mono font-semibold">{editingSewa.no_kontainer} &bull; {getCustomerName(editingSewa.id_customer)}</p>
              </div>
            </div>

            <form onSubmit={handleSaveEditSewa} className="space-y-4 pt-1">
              <div>
                <label className="block text-xs font-semibold text-slate-600 mb-1">Tanggal Mulai Sewa (dd/mm/yyyy)</label>
                <div className="relative">
                  <input
                    id="edit-sewa-tanggal-sewa"
                    type="text"
                    required
                    value={editTanggalSewa}
                    onChange={(e) => setEditTanggalSewa(e.target.value)}
                    placeholder="Contoh: 30/09/2022"
                    className="w-full text-sm border border-slate-200 rounded-xl pl-9 pr-3 py-2 bg-slate-50 font-mono focus:bg-white"
                  />
                  <Calendar className="w-4 h-4 text-slate-400 absolute left-3 top-2.5" />
                </div>
              </div>

              <div>
                <label className="block text-xs font-semibold text-slate-600 mb-1">
                  Tanggal Pengembalian (dd/mm/yyyy)
                </label>
                <div className="relative">
                  <input
                    id="edit-sewa-tanggal-kembali"
                    type="text"
                    value={editTanggalKembali}
                    onChange={(e) => setEditTanggalKembali(e.target.value)}
                    placeholder="dd/mm/yyyy ATAU KOSONG jika masih disewa"
                    className="w-full text-sm border border-slate-200 rounded-xl pl-9 pr-3 py-2 bg-slate-50 font-mono focus:bg-white"
                  />
                  <Calendar className="w-4 h-4 text-slate-400 absolute left-3 top-2.5" />
                </div>
                <p className="text-[10px] text-slate-500 mt-1 italic">
                  * Tips: Kosongkan kolom ini jika ingin status sewa kembali &quot;Aktif&quot;. Jika diisi tanggal, status otomatis menjadi &quot;Selesai&quot;.
                </p>
              </div>

              <div className="bg-slate-50 p-3 rounded-xl border border-slate-100 space-y-3">
                <div>
                  <label className="block text-xs font-semibold text-slate-600 mb-1">Jenis Tarif Dasar</label>
                  <div className="grid grid-cols-2 gap-2 text-xs font-medium">
                    <label className="flex items-center gap-1.5 p-2 bg-white border border-slate-200 rounded-lg cursor-pointer select-none">
                      <input
                        id="edit-radio-tarif-bulanan"
                        type="radio"
                        name="editJenisTarif"
                        checked={editJenisTarif === 'Bulanan'}
                        onChange={() => setEditJenisTarif('Bulanan')}
                        className="text-sky-600 focus:ring-sky-500"
                      />
                      <span>Bulanan</span>
                    </label>
                    <label className="flex items-center gap-1.5 p-2 bg-white border border-slate-200 rounded-lg cursor-pointer select-none">
                      <input
                        id="edit-radio-tarif-harian"
                        type="radio"
                        name="editJenisTarif"
                        checked={editJenisTarif === 'Harian'}
                        onChange={() => setEditJenisTarif('Harian')}
                        className="text-sky-600 focus:ring-sky-500"
                      />
                      <span>Harian</span>
                    </label>
                  </div>
                </div>

                <div className="grid grid-cols-2 gap-2">
                  <div>
                    <label className="block text-[10px] font-semibold text-slate-600 mb-0.5">Bulanan (Rp)</label>
                    <input
                      id="edit-sewa-tarif-bulan"
                      type="number"
                      value={editTarifBulanan}
                      onChange={(e) => setEditTarifBulanan(Math.max(0, parseInt(e.target.value) || 0))}
                      className="w-full text-xs border border-slate-200 rounded-lg p-1.5 bg-white font-mono focus:border-sky-400 focus:ring-sky-400"
                    />
                  </div>
                  <div>
                    <label className="block text-[10px] font-semibold text-slate-600 mb-0.5">Harian (Rp)</label>
                    <input
                      id="edit-sewa-tarif-hari"
                      type="number"
                      value={editTarifHarian}
                      onChange={(e) => setEditTarifHarian(Math.max(0, parseInt(e.target.value) || 0))}
                      className="w-full text-xs border border-slate-200 rounded-lg p-1.5 bg-white font-mono focus:border-sky-400 focus:ring-sky-400"
                    />
                  </div>
                </div>
              </div>

              <div>
                <label className="block text-xs font-semibold text-slate-600 mb-1">Catatan Sewa (Opsional)</label>
                <textarea
                  id="edit-sewa-catatan"
                  rows={2}
                  value={editRentalNotes}
                  onChange={(e) => setEditRentalNotes(e.target.value)}
                  placeholder="Misal: Info PO, slip jalan, dll..."
                  className="w-full text-xs border border-slate-200 rounded-xl px-3 py-2 bg-slate-50 text-slate-850"
                />
              </div>

              <div className="flex items-center gap-2 py-1.5 bg-slate-50 p-3 rounded-xl border border-slate-100">
                <input
                  id="edit-sewa-use-ppn"
                  type="checkbox"
                  checked={editUsePpn}
                  onChange={(e) => setEditUsePpn(e.target.checked)}
                  className="w-4 h-4 text-sky-600 focus:ring-sky-500 rounded border-slate-300"
                />
                <div className="flex flex-col">
                  <label htmlFor="edit-sewa-use-ppn" className="text-xs font-semibold text-slate-700 cursor-pointer select-none">
                    Default Pakai PPN (11%)
                  </label>
                  <span className="text-[10px] text-slate-500">Bisa di-uncheck jika transaksi Non-PPN</span>
                </div>
              </div>

              <div className="flex items-center gap-2 justify-end pt-2 border-t border-slate-150">
                <button
                  id="btn-cancel-edit-sewa"
                  type="button"
                  onClick={() => setEditingSewa(null)}
                  className="px-4 py-2 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors cursor-pointer"
                >
                  Batal
                </button>
                <button
                  id="btn-confirm-edit-sewa"
                  type="submit"
                  className="px-4 py-2 text-xs font-semibold text-white bg-sky-600 hover:bg-sky-700 rounded-xl transition-colors cursor-pointer shadow-xs"
                >
                  Simpan Perubahan
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}
