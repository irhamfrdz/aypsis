import React, { useState, useEffect } from 'react';
import { Coins, CheckCircle2, Sparkles, PlusCircle, Search, Plus, Calendar, X } from 'lucide-react';
import SearchableCombobox from './SearchableCombobox';
import { FormDateInput } from './FormDateInput';

// Fast local input components to prevent focus loss during typing
interface FastNumberInputProps {
  value: number | null | undefined;
  placeholder?: string;
  onChange: (val: number | null) => void;
  className?: string;
}

const FastNumberInput: React.FC<FastNumberInputProps> = ({ value, placeholder, onChange, className }) => {
  const [localVal, setLocalVal] = useState<string>(value !== null && value !== undefined ? String(value) : '');

  useEffect(() => {
    setLocalVal(value !== null && value !== undefined ? String(value) : '');
  }, [value]);

  const handleBlur = () => {
    const trimmed = localVal.trim();
    const parsed = trimmed === '' ? null : Number(trimmed);
    if (parsed !== value) {
      onChange(parsed);
    }
  };

  return (
    <input
      type="number"
      value={localVal}
      placeholder={placeholder}
      onChange={(e) => setLocalVal(e.target.value)}
      onBlur={handleBlur}
      onKeyDown={(e) => {
        if (e.key === 'Enter') {
          (e.target as HTMLInputElement).blur();
        }
      }}
      className={className}
    />
  );
};

interface FastTextInputProps {
  value: string | null | undefined;
  placeholder?: string;
  onChange: (val: string) => void;
  className?: string;
}

const FastTextInput: React.FC<FastTextInputProps> = ({ value, placeholder, onChange, className }) => {
  const [localVal, setLocalVal] = useState<string>(value || '');

  useEffect(() => {
    setLocalVal(value || '');
  }, [value]);

  const handleBlur = () => {
    const trimmed = (localVal || '').trim();
    if (trimmed !== (value || '').trim()) {
      onChange(trimmed);
    }
  };

  return (
    <input
      type="text"
      value={localVal}
      placeholder={placeholder}
      onChange={(e) => setLocalVal(e.target.value)}
      onBlur={handleBlur}
      onKeyDown={(e) => {
        if (e.key === 'Enter') {
          (e.target as HTMLInputElement).blur();
        }
      }}
      className={className}
    />
  );
};

interface PaymentWorkspaceProps {
  state: any;
  onStateChange: (state: any) => void;
  allPeriods: any[];
  selectedVendorCustomer: string;
  setSelectedVendorCustomer: (val: string) => void;
  selectedPaymentNo: string;
  setSelectedPaymentNo: (val: string) => void;
  selectedPaymentDate: string;
  setSelectedPaymentDate: (val: string) => void;
  searchPranotaNo: string;
  setSearchPranotaNo: (val: string) => void;
  getCustomerName: (id: string) => string;
  formatRupiah: (val: number) => string;
  formatIndoDate: (val: any) => string;
  utcTime: string;
  triggerNoti: (type: 'sukses' | 'error' | 'info' | 'warning', msg: string) => void;
  handleBulkUpdate: (ids: string[], updates: any, statusType: any) => void;
  existingDraftPayments: string[];
}

export const PaymentWorkspace: React.FC<PaymentWorkspaceProps> = ({
  state,
  onStateChange,
  allPeriods,
  selectedVendorCustomer,
  setSelectedVendorCustomer,
  selectedPaymentNo,
  setSelectedPaymentNo,
  selectedPaymentDate,
  setSelectedPaymentDate,
  searchPranotaNo,
  setSearchPranotaNo,
  getCustomerName,
  formatRupiah,
  formatIndoDate,
  utcTime,
  triggerNoti,
  handleBulkUpdate,
  existingDraftPayments,
}) => {
  // Submode for Entry vs Search (using 'search' | 'create' for consistency with Tab 1 and 2)
  const [paymentSubMode, setPaymentSubMode] = useState<'search' | 'create'>('search');

  // Input states for Manual Entry mode
  const [newPaymentVendorId, setNewPaymentVendorId] = useState('');
  const [newPaymentNo, setNewPaymentNo] = useState('');
  const [newPaymentDate, setNewPaymentDate] = useState('');

  // Search parameters for Search Mode
  const [searchStartDate, setSearchStartDate] = useState('');
  const [searchEndDate, setSearchEndDate] = useState('');
  const [searchQueryPranota, setSearchQueryPranota] = useState('');

  // Reset search fields on mount or view change
  useEffect(() => {
    setSearchStartDate('');
    setSearchEndDate('');
    setSearchQueryPranota('');
  }, [paymentSubMode]);

  // Filter the active tagihans inside the selected vendor payment group
  const selectedPaymentPeriods = allPeriods.filter(p => {
    const sObj = state.sewas.find((s: any) => s.id_sewa === p.id_sewa);
    return (
      p.nomor_bayar === selectedPaymentNo &&
      selectedPaymentNo.trim() !== '' &&
      (!selectedVendorCustomer || sObj?.id_customer === selectedVendorCustomer)
    );
  });

  // Filter loose Pranotas (saved from Tab 2 with status 'Pranota' and no payment assigned yet)
  const loosePranotaPeriods = allPeriods.filter(p => {
    const sObj = state.sewas.find((s: any) => s.id_sewa === p.id_sewa);
    const isMatchCustomer = !selectedVendorCustomer || sObj?.id_customer === selectedVendorCustomer;
    const isOutstanding = p.status_bayar === 'Pranota';
    const hasNoPembayaran = !p.nomor_bayar || p.nomor_bayar.trim() === '';
    
    if (!isMatchCustomer || !isOutstanding || !hasNoPembayaran) return false;
    
    if (searchPranotaNo.trim()) {
      const q = searchPranotaNo.toLowerCase();
      // Only search by nomor_pranota as per rule: "pastikan menu search hanya cari no pranota"
      return p.nomor_pranota?.toLowerCase().includes(q);
    }
    return true;
  });

  // Group loose proforma/pranotas by nomor_pranota for a cleaner Pranota-based selection
  const loosePranotasGrouped = (() => {
    const groupsMap = new Map<string, {
      nomor_pranota: string;
      tanggal_pranota: string | null;
      customerName: string;
      count: number;
      totalEstimasi: number;
      totalAktual: number;
      totalNetto: number;
      periods: any[];
    }>();

    loosePranotaPeriods.forEach(p => {
      const pranotaNo = p.nomor_pranota;
      if (!pranotaNo || pranotaNo.trim() === '') return;

      const sObj = state.sewas.find((s: any) => s.id_sewa === p.id_sewa);
      const custId = sObj?.id_customer || '';
      const custName = custId ? getCustomerName(custId) : 'Umum/Campuran';

      const nominalEstimasi = p.jumlah_tagihan;
      const nominalAktual = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : nominalEstimasi;
      const currentPPN = p.ppn !== null && p.ppn !== undefined ? p.ppn : Math.round(nominalAktual * 0.11);
      const currentPPh = p.pph !== null && p.pph !== undefined ? p.pph : Math.round(nominalAktual * 0.02);
      const netto = nominalAktual + currentPPN - currentPPh;

      if (!groupsMap.has(pranotaNo)) {
        groupsMap.set(pranotaNo, {
          nomor_pranota: pranotaNo,
          tanggal_pranota: p.tanggal_pranota || null,
          customerName: custName,
          count: 1,
          totalEstimasi: nominalEstimasi,
          totalAktual: nominalAktual,
          totalNetto: netto,
          periods: [p]
        });
      } else {
        const existing = groupsMap.get(pranotaNo)!;
        existing.count += 1;
        existing.totalEstimasi += nominalEstimasi;
        existing.totalAktual += nominalAktual;
        existing.totalNetto += netto;
        existing.periods.push(p);
      }
    });

    return Array.from(groupsMap.values());
  })();

  // Group selectedPaymentPeriods by nomor_pranota for the Left Side
  const selectedPranotasGrouped = (() => {
    const groupsMap = new Map<string, {
      nomor_pranota: string;
      tanggal_pranota: string | null;
      customerName: string;
      totalEstimasi: number;
      totalAktual: number;
      selisih: number;
      ppn: number;
      pph: number;
      netto: number;
      keterangan_selisih: string;
      id_tagihan_list: string[];
      periods: any[];
    }>();

    selectedPaymentPeriods.forEach(p => {
      const pranotaNo = p.nomor_pranota || 'Manual';
      const sObj = state.sewas.find((s: any) => s.id_sewa === p.id_sewa);
      const custId = sObj?.id_customer || '';
      const custName = custId ? getCustomerName(custId) : 'Umum/Campuran';

      const estimasi = p.jumlah_tagihan;
      const aktual = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : estimasi;
      const selisih = aktual - estimasi;
      const ppn = p.ppn !== null && p.ppn !== undefined ? p.ppn : Math.round(aktual * 0.11);
      const pph = p.pph !== null && p.pph !== undefined ? p.pph : Math.round(aktual * 0.02);
      const netto = aktual + ppn - pph;
      const ket = p.keterangan_selisih || '';

      if (!groupsMap.has(pranotaNo)) {
        groupsMap.set(pranotaNo, {
          nomor_pranota: pranotaNo,
          tanggal_pranota: p.tanggal_pranota || null,
          customerName: custName,
          totalEstimasi: estimasi,
          totalAktual: aktual,
          selisih: selisih,
          ppn: ppn,
          pph: pph,
          netto: netto,
          keterangan_selisih: ket,
          id_tagihan_list: [p.id_tagihan],
          periods: [p]
        });
      } else {
        const existing = groupsMap.get(pranotaNo)!;
        existing.totalEstimasi += estimasi;
        existing.totalAktual += aktual;
        existing.selisih += selisih;
        existing.ppn += ppn;
        existing.pph += pph;
        existing.netto += netto;
        existing.periods.push(p);
        if (ket && !existing.keterangan_selisih.includes(ket)) {
          existing.keterangan_selisih = existing.keterangan_selisih ? existing.keterangan_selisih + '; ' + ket : ket;
        }
        if (!existing.id_tagihan_list.includes(p.id_tagihan)) {
          existing.id_tagihan_list.push(p.id_tagihan);
        }
      }
    });

    return Array.from(groupsMap.values());
  })();

  // Compile unique payments list across the system
  const allPayments = (() => {
    const paymentMap = new Map<string, {
      nomor_bayar: string;
      tanggal_bayar: string | null;
      id_customer: string;
      customerName: string;
      countPranota: number;
      pranotas: string[];
      totalEstimasi: number;
      totalAktual: number;
      totalNetto: number;
      isLunas: boolean;
    }>();

    allPeriods.forEach(p => {
      const bayarNo = p.nomor_bayar;
      if (!bayarNo || bayarNo.trim() === '') return;

      const sObj = state.sewas.find((s: any) => s.id_sewa === p.id_sewa);
      const custId = sObj?.id_customer || '';
      const custName = custId ? getCustomerName(custId) : 'Umum/Campuran';
      const pranotaNo = p.nomor_pranota || '';

      const nominalEstimasi = p.jumlah_tagihan;
      const nominalAktual = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : nominalEstimasi;
      const ppn = p.ppn !== null && p.ppn !== undefined ? p.ppn : Math.round(nominalAktual * 0.11);
      const pph = p.pph !== null && p.pph !== undefined ? p.pph : Math.round(nominalAktual * 0.02);
      const netto = nominalAktual + ppn - pph;

      if (!paymentMap.has(bayarNo)) {
        paymentMap.set(bayarNo, {
          nomor_bayar: bayarNo,
          tanggal_bayar: p.tanggal_bayar || null,
          id_customer: custId,
          customerName: custName,
          countPranota: pranotaNo ? 1 : 0,
          pranotas: pranotaNo ? [pranotaNo] : [],
          totalEstimasi: nominalEstimasi,
          totalAktual: nominalAktual,
          totalNetto: netto,
          isLunas: p.status_bayar === 'Lunas',
        });
      } else {
        const existing = paymentMap.get(bayarNo)!;
        existing.totalEstimasi += nominalEstimasi;
        existing.totalAktual += nominalAktual;
        existing.totalNetto += netto;
        if (p.status_bayar !== 'Lunas') {
          existing.isLunas = false;
        }
        if (pranotaNo && !existing.pranotas.includes(pranotaNo)) {
          existing.pranotas.push(pranotaNo);
          existing.countPranota = existing.pranotas.length;
        }
      }
    });

    return Array.from(paymentMap.values());
  })();

  // Filter payments list based on Search Menu criteria
  const filteredPayments = allPayments.filter(pay => {
    if (selectedVendorCustomer && pay.id_customer !== selectedVendorCustomer) {
      return false;
    }
    if (pay.tanggal_bayar) {
      if (searchStartDate && pay.tanggal_bayar < searchStartDate) return false;
      if (searchEndDate && pay.tanggal_bayar > searchEndDate) return false;
    } else {
      if (searchStartDate || searchEndDate) return false;
    }
    if (searchQueryPranota.trim()) {
      const q = searchQueryPranota.toLowerCase();
      // Search by No. Pranota inside the payment
      const matchesPranota = pay.pranotas.some(pr => pr.toLowerCase().includes(q)) || pay.nomor_bayar.toLowerCase().includes(q);
      if (!matchesPranota) return false;
    }
    return true;
  });

  // Local state handlers
  const handleAttachPranotaToPayment = (nomorPranota: string) => {
    if (!selectedPaymentNo) {
      triggerNoti('error', 'Masukkan atau pilih No. Pembayaran terlebih dahulu.');
      return;
    }
    const periodsToAttach = loosePranotaPeriods.filter(p => p.nomor_pranota === nomorPranota);
    if (periodsToAttach.length === 0) return;

    const updatedOverrides = { ...state.paymentOverrides };
    periodsToAttach.forEach(p => {
      const existing = updatedOverrides[p.id_tagihan] || {
        status_bayar: 'Pranota',
        tanggal_tagihan: null,
        tanggal_bayar: null,
        nomor_invoice_grup: null,
        jumlah_tagihan_override: null,
        jumlah_bayar: null,
        selisih_pembayaran: null,
        keterangan_selisih: null,
        ppn: null,
        pph: null,
        nomor_bayar: null
      };
      updatedOverrides[p.id_tagihan] = {
        ...existing,
        nomor_bayar: selectedPaymentNo,
        tanggal_bayar: selectedPaymentDate || utcTime.split('T')[0]
      };
    });

    onStateChange({
      ...state,
      paymentOverrides: updatedOverrides
    });
    triggerNoti('sukses', `Berhasil memindahkan Pranota "${nomorPranota}" ke pembayaran.`);
  };

  const handleDetachPranotaFromPayment = (idTagihanList: string[]) => {
    const updatedOverrides = { ...state.paymentOverrides };
    idTagihanList.forEach(id => {
      if (updatedOverrides[id]) {
        updatedOverrides[id] = {
          ...updatedOverrides[id],
          nomor_bayar: null,
          tanggal_bayar: null
        };
      }
    });
    onStateChange({
      ...state,
      paymentOverrides: updatedOverrides
    });
    triggerNoti('sukses', 'Berhasil melepas Pranota dari pembayaran ini.');
  };

  const handleUpdatePranotaRealValue = (idTagihanList: string[], value: any) => {
    const updatedOverrides = { ...state.paymentOverrides };
    const numVal = value === '' || value === null ? null : Number(value);

    if (numVal === null) {
      idTagihanList.forEach(id => {
        const existing = updatedOverrides[id] || {
          status_bayar: 'Pranota',
          tanggal_tagihan: null,
          tanggal_bayar: null,
          nomor_invoice_grup: null,
          jumlah_tagihan_override: null,
          jumlah_bayar: null,
          selisih_pembayaran: null,
          keterangan_selisih: null,
          ppn: null,
          pph: null,
          nomor_bayar: null
        };
        updatedOverrides[id] = {
          ...existing,
          jumlah_tagihan_override: null,
          ppn: null,
          pph: null
        };
      });
    } else {
      if (idTagihanList.length === 1) {
        const id = idTagihanList[0];
        const periodObj = allPeriods.find(x => x.id_tagihan === id);
        const sewaObj = state.sewas.find(s => s.id_sewa === periodObj?.id_sewa);
        const isNonPpn = sewaObj?.non_ppn === true;

        const existing = updatedOverrides[id] || {
          status_bayar: 'Pranota',
          tanggal_tagihan: null,
          tanggal_bayar: null,
          nomor_invoice_grup: null,
          jumlah_tagihan_override: null,
          jumlah_bayar: null,
          selisih_pembayaran: null,
          keterangan_selisih: null,
          ppn: null,
          pph: null,
          nomor_bayar: null
        };
        updatedOverrides[id] = {
          ...existing,
          jumlah_tagihan_override: numVal,
          ppn: isNonPpn ? 0 : Math.round(numVal * 0.11),
          pph: Math.round(numVal * 0.02)
        };
      } else {
        const totalEstimasi = idTagihanList.reduce((sum, id) => {
          const p = allPeriods.find(x => x.id_tagihan === id);
          return sum + (p?.jumlah_tagihan || 0);
        }, 0);

        let distributedSum = 0;
        idTagihanList.forEach((id, idx) => {
          const p = allPeriods.find(x => x.id_tagihan === id);
          const est = p?.jumlah_tagihan || 0;
          let portion = 0;
          if (idx === idTagihanList.length - 1) {
            portion = numVal - distributedSum;
          } else {
            portion = Math.round((est / (totalEstimasi || 1)) * numVal);
            distributedSum += portion;
          }

          const sewaObj = state.sewas.find(s => s.id_sewa === p?.id_sewa);
          const isNonPpn = sewaObj?.non_ppn === true;

          const existing = updatedOverrides[id] || {
            status_bayar: 'Pranota',
            tanggal_tagihan: null,
            tanggal_bayar: null,
            nomor_invoice_grup: null,
            jumlah_tagihan_override: null,
            jumlah_bayar: null,
            selisih_pembayaran: null,
            keterangan_selisih: null,
            ppn: null,
            pph: null,
            nomor_bayar: null
          };
          updatedOverrides[id] = {
            ...existing,
            jumlah_tagihan_override: portion,
            ppn: isNonPpn ? 0 : Math.round(portion * 0.11),
            pph: Math.round(portion * 0.02)
          };
        });
      }
    }

    onStateChange({
      ...state,
      paymentOverrides: updatedOverrides
    });
  };

  const handleUpdatePranotaKeterangan = (idTagihanList: string[], value: string) => {
    const updatedOverrides = { ...state.paymentOverrides };
    idTagihanList.forEach(id => {
      const existing = updatedOverrides[id] || {
        status_bayar: 'Pranota',
        tanggal_tagihan: null,
        tanggal_bayar: null,
        nomor_invoice_grup: null,
        jumlah_tagihan_override: null,
        jumlah_bayar: null,
        selisih_pembayaran: null,
        keterangan_selisih: null,
        ppn: null,
        pph: null,
        nomor_bayar: null
      };
      updatedOverrides[id] = {
        ...existing,
        keterangan_selisih: value
      };
    });
    onStateChange({
      ...state,
      paymentOverrides: updatedOverrides
    });
  };

  // Calculate totals for selected payment group based on the underlying tagihans on the left side
  const totalEstimasiPaymentGroup = selectedPaymentPeriods.reduce((sum, p) => sum + p.jumlah_tagihan, 0);
  const totalAktualPaymentGroup = selectedPaymentPeriods.reduce((sum, p) => {
    const amt = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : p.jumlah_tagihan;
    return sum + amt;
  }, 0);
  const totalPPNPaymentGroup = selectedPaymentPeriods.reduce((sum, p) => {
    const amt = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : p.jumlah_tagihan;
    const ppn = p.ppn !== null && p.ppn !== undefined ? p.ppn : Math.round(amt * 0.11);
    return sum + ppn;
  }, 0);
  const totalPPhPaymentGroup = selectedPaymentPeriods.reduce((sum, p) => {
    const amt = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : p.jumlah_tagihan;
    const pph = p.pph !== null && p.pph !== undefined ? p.pph : Math.round(amt * 0.02);
    return sum + pph;
  }, 0);
  const grandNetTotalPaymentGroup = totalAktualPaymentGroup + totalPPNPaymentGroup - totalPPhPaymentGroup;

  return (
    <div className="space-y-6" id="payment-collective-workspace">
      
      {/* TAB SUBMODE SELECTOR */}
      <div className="bg-slate-100/80 p-1.5 rounded-xl border border-slate-200/60 flex flex-wrap gap-1">
        <button
          type="button"
          onClick={() => setPaymentSubMode('search')}
          className={`px-4 py-2 text-xs font-bold uppercase tracking-wider flex items-center gap-2 rounded-lg transition-all cursor-pointer ${
            paymentSubMode === 'search'
              ? 'bg-indigo-600 text-white shadow-sm'
              : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50'
          }`}
        >
          <Search className="w-4 h-4" />
          Cari &amp; Kelola Pembayaran (Lunas)
        </button>
        <button
          type="button"
          onClick={() => {
            setPaymentSubMode('create');
          }}
          className={`px-4 py-2 text-xs font-bold uppercase tracking-wider flex items-center gap-2 rounded-lg transition-all cursor-pointer ${
            paymentSubMode === 'create'
              ? 'bg-indigo-600 text-white shadow-sm'
              : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50'
          }`}
        >
          <PlusCircle className="w-4 h-4" />
          Buat Pembayaran Baru (Manual Entry)
        </button>
      </div>

      {/* SEARCH / ENTRY CONFIGURATION CARD */}
      {paymentSubMode === 'search' ? (
        <div className="bg-white p-5 rounded-2xl border border-slate-150 shadow-sm space-y-4">
          <div className="flex items-center gap-3 border-b border-slate-100 pb-3.5">
            <div className="w-10 h-10 rounded-full bg-indigo-50 border border-indigo-150 flex items-center justify-center shrink-0">
              <Search className="w-5 h-5 text-indigo-600" />
            </div>
            <div>
              <h3 className="text-sm font-extrabold text-slate-800 uppercase tracking-wider">CARI &amp; KELOLA PEMBAYARAN</h3>
              <p className="text-[10px] text-slate-400 mt-0.5">Pilih Vendor, Tgl Pencarian (Optional) dan cari No. Pranota / Pembayaran yang sudah lunas.</p>
            </div>
          </div>
          
          <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label className="block text-xs font-semibold text-slate-500 mb-1">Pilih Vendor</label>
              <select
                value={selectedVendorCustomer}
                onChange={(e) => {
                  setSelectedVendorCustomer(e.target.value);
                  setSelectedPaymentNo(''); // reset payment when customer changes
                }}
                className="w-full text-xs font-medium border border-slate-200 rounded-xl px-3 py-2 bg-slate-50 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500"
              >
                <option value="">-- Semua Vendor --</option>
                {state.customers.map((c: any) => (
                  <option key={c.id_customer} value={c.id_customer}>{c.nama_customer}</option>
                ))}
              </select>
            </div>

            <div>
              <label className="block text-xs font-semibold text-slate-500 mb-1">Tgl. Mulai Pencarian</label>
              <input
                type="date"
                value={searchStartDate}
                onChange={(e) => setSearchStartDate(e.target.value)}
                className="w-full text-xs font-mono border border-slate-200 rounded-xl px-3 py-2 bg-slate-50 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500"
              />
            </div>

            <div>
              <label className="block text-xs font-semibold text-slate-500 mb-1">Tgl. Akhir Pencarian</label>
              <input
                type="date"
                value={searchEndDate}
                onChange={(e) => setSearchEndDate(e.target.value)}
                className="w-full text-xs font-mono border border-slate-200 rounded-xl px-3 py-2 bg-slate-50 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500"
              />
            </div>

            <div>
              <label className="block text-xs font-semibold text-slate-500 mb-1">Cari No. Pranota / Pembayaran</label>
              <div className="relative">
                <Search className="w-4 h-4 absolute left-3 top-2.5 text-slate-400" />
                <input
                  type="text"
                  placeholder="Ketik No. Pranota..."
                  value={searchQueryPranota}
                  onChange={(e) => setSearchQueryPranota(e.target.value)}
                  className="w-full text-xs border border-slate-200 rounded-xl pl-9 pr-3 py-2 bg-slate-50 focus:bg-white text-slate-800 placeholder-slate-400 font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500"
                />
              </div>
            </div>
          </div>
        </div>
      ) : (
        <div className="bg-white p-5 rounded-2xl border border-slate-150 shadow-sm space-y-4">
          <div className="flex items-center gap-3 border-b border-slate-100 pb-3.5">
            <div className="w-10 h-10 rounded-full bg-indigo-50 border border-indigo-150 flex items-center justify-center shrink-0">
              <PlusCircle className="w-5 h-5 text-indigo-600" />
            </div>
            <div>
              <h3 className="text-sm font-extrabold text-slate-800 uppercase tracking-wider">BUAT PEMBAYARAN BARU (MANUAL)</h3>
              <p className="text-[10px] text-slate-400 mt-0.5">Masukkan Nomor Pembayaran baru secara manual. Kolom ini bebas hambatan sehingga sangat cepat!</p>
            </div>
          </div>
          
          <div className="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
              <label className="block text-xs font-semibold text-slate-500 mb-1">Vendor <span className="text-rose-500">*</span></label>
              <select
                value={newPaymentVendorId}
                onChange={(e) => setNewPaymentVendorId(e.target.value)}
                className="w-full text-xs font-medium border border-slate-200 rounded-xl px-3 py-2 bg-slate-50 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500"
              >
                <option value="">-- Pilih Vendor --</option>
                {state.customers.map((c: any) => (
                  <option key={c.id_customer} value={c.id_customer}>{c.nama_customer}</option>
                ))}
              </select>
            </div>

            <div>
              <label className="block text-xs font-semibold text-slate-500 mb-1">No. Pembayaran Baru <span className="text-rose-500">*</span></label>
              <input
                type="text"
                value={newPaymentNo}
                onChange={(e) => setNewPaymentNo(e.target.value)}
                placeholder="Contoh: EBK2506002"
                className="w-full text-xs font-mono font-semibold border border-slate-200 rounded-xl px-3 py-2 bg-slate-50 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500"
              />
            </div>

            <div>
              <label className="block text-xs font-semibold text-slate-500 mb-1">Tgl. Pembayaran</label>
              <FormDateInput
                value={newPaymentDate}
                onChange={(val) => setNewPaymentDate(val)}
                className="w-full text-xs font-mono border border-slate-200 rounded-xl px-3 py-2 bg-slate-50 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500"
              />
            </div>

            <div>
              <button
                type="button"
                onClick={() => {
                  if (!newPaymentVendorId) {
                    triggerNoti('error', 'Silakan pilih Vendor terlebih dahulu.');
                    return;
                  }
                  if (!newPaymentNo.trim()) {
                    triggerNoti('error', 'Silakan isi No. Pembayaran baru.');
                    return;
                  }
                  
                  const paymentClean = newPaymentNo.trim();
                  
                  // Validasi No. Pembayaran tidak boleh sama dengan No. Pranota
                  const isSameAsPranota = allPeriods.some(p => p.nomor_pranota && p.nomor_pranota.toLowerCase().trim() === paymentClean.toLowerCase());
                  if (isSameAsPranota) {
                    triggerNoti('error', `Nomor Pembayaran tidak boleh sama dengan Nomor Pranota yang sudah ada (${paymentClean})!`);
                    return;
                  }

                  const paymentDateClean = newPaymentDate || utcTime.split('T')[0];

                  // Set active workspace to this new payment
                  setSelectedVendorCustomer(newPaymentVendorId);
                  setSelectedPaymentNo(paymentClean);
                  setSelectedPaymentDate(paymentDateClean);

                  // Reset fields
                  setNewPaymentNo('');
                  
                  triggerNoti('sukses', `Pembayaran "${paymentClean}" berhasil dibuat! Silakan pilih pranota di sebelah kanan untuk ditambahkan.`);
                }}
                className="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm flex items-center justify-center gap-1.5 cursor-pointer h-[38px]"
              >
                <PlusCircle className="w-4 h-4" />
                Buat &amp; Mulai Susun
              </button>
            </div>
          </div>
        </div>
      )}

      {/* TWO PANELS WORKSPACE OR PLACEHOLDER DASHBOARD */}
      {!selectedPaymentNo ? (
        <div className="space-y-6" id="payment-dashboard-placeholder">
          <div className="py-8 px-4 text-center bg-slate-50 border border-dashed border-slate-200 rounded-2xl">
            <Sparkles className="w-6 h-6 text-indigo-500 mx-auto mb-2" />
            <p className="text-xs font-bold text-slate-700">Silakan masukkan atau pilih No. Pembayaran di atas untuk mulai menyusun.</p>
            <p className="text-[10px] text-slate-400 mt-1">Anda dapat memuat data pembayaran yang ada dari draf / hasil pencarian di bawah ini.</p>
          </div>

          <div className="border border-slate-150 rounded-2xl p-4 bg-white shadow-3xs space-y-3">
            <div className="flex items-center gap-2 border-b border-slate-100 pb-2.5">
              <Coins className="w-4 h-4 text-indigo-600" />
              <h4 className="text-xs font-extrabold text-slate-800 uppercase tracking-wider font-bold">
                Daftar Pembayaran Aktif / Outstanding Terbaru (Maks. 20)
              </h4>
            </div>
            
            {filteredPayments.length === 0 ? (
              <div className="text-center py-6 text-xs text-slate-400 italic">
                Tidak ada data pembayaran yang cocok atau tersimpan saat ini.
              </div>
            ) : (
              <div className="overflow-x-auto border border-slate-150 rounded-xl bg-white shadow-3xs">
                <table className="w-full text-left border-collapse text-[10px] font-sans">
                  <thead>
                    <tr className="bg-slate-50 text-slate-500 font-bold border-b border-slate-200">
                      <th className="p-2 py-2.5">No. Pembayaran</th>
                      <th className="p-2 py-2.5">Tgl. Bayar</th>
                      <th className="p-2 py-2.5">Nama Vendor</th>
                      <th className="p-2 py-2.5 text-center">Jumlah Pranota</th>
                      <th className="p-2 py-2.5 text-right">Grand Net Total</th>
                      <th className="p-2 py-2.5 text-center">Status</th>
                      <th className="p-2 py-2.5 text-center">Aksi</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-slate-100">
                    {filteredPayments.map((pay) => (
                      <tr key={pay.nomor_bayar} className="hover:bg-slate-50/60 transition-colors">
                        <td className="p-2 py-2.5 font-mono font-bold text-indigo-700">{pay.nomor_bayar}</td>
                        <td className="p-2 py-2.5 font-mono text-slate-500">{formatIndoDate(pay.tanggal_bayar)}</td>
                        <td className="p-2 py-2.5 font-semibold text-slate-700">{pay.customerName}</td>
                        <td className="p-2 py-2.5 text-center">
                          <span className="bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded font-bold">{pay.countPranota} Pranota</span>
                        </td>
                        <td className="p-2 py-2.5 text-right font-mono font-bold text-slate-800">{formatRupiah(pay.totalNetto)}</td>
                        <td className="p-2 py-2.5 text-center">
                          {pay.isLunas ? (
                            <span className="px-2 py-0.5 text-[9px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-md">
                              Lunas
                            </span>
                          ) : (
                            <span className="px-2 py-0.5 text-[9px] font-bold text-amber-700 bg-amber-50 border border-amber-200 rounded-md">
                              Draft
                            </span>
                          )}
                        </td>
                        <td className="p-2 py-2.5 text-center">
                          <div className="flex items-center justify-center gap-2">
                            <button
                              type="button"
                              onClick={() => {
                                setSelectedPaymentNo(pay.nomor_bayar);
                                setSelectedPaymentDate(pay.tanggal_bayar || '');
                                setSelectedVendorCustomer(pay.id_customer);
                                triggerNoti('info', `Memuat pembayaran "${pay.nomor_bayar}"`);
                              }}
                              className="px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-[9px] font-extrabold cursor-pointer transition-colors shadow-3xs shrink-0"
                            >
                              Buka &amp; Kelola
                            </button>
                            {!pay.isLunas && (
                              <button
                                type="button"
                                onClick={() => {
                                  const paymentNoClean = pay.nomor_bayar.trim();
                                  // Validasi No. Pembayaran tidak boleh sama dengan No. Pranota
                                  const isSameAsPranota = allPeriods.some(p => p.nomor_pranota && p.nomor_pranota.toLowerCase().trim() === paymentNoClean.toLowerCase());
                                  if (isSameAsPranota) {
                                    triggerNoti('error', `Nomor Pembayaran tidak boleh sama dengan Nomor Pranota yang sudah ada (${paymentNoClean})!`);
                                    return;
                                  }

                                  const periodIds = allPeriods
                                    .filter(p => p.nomor_bayar === pay.nomor_bayar)
                                    .map(p => p.id_tagihan);
                                  
                                  if (periodIds.length === 0) {
                                    triggerNoti('error', 'Grup Pembayaran ini kosong!');
                                    return;
                                  }

                                  const updates = {
                                    status_bayar: 'Lunas',
                                    nomor_bayar: pay.nomor_bayar,
                                    tanggal_bayar: pay.tanggal_bayar || utcTime.split('T')[0]
                                  };
                                  handleBulkUpdate(periodIds, updates, 'Lunas');
                                  triggerNoti('sukses', `Sukses! Status Pembayaran "${pay.nomor_bayar}" berhasil disimpan sebagai Lunas (sejajar menu entry)!`);
                                }}
                                className="px-2 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-[9px] font-extrabold cursor-pointer transition-colors shadow-3xs shrink-0"
                              >
                                Set Status Lunas
                              </button>
                            )}
                          </div>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            )}
          </div>
        </div>
      ) : (
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 animate-in fade-in duration-200">
          
          {/* LEFT PANEL: PRANOTAS IN SELECTED PAYMENT */}
          <div className="lg:col-span-7 bg-white p-5 rounded-2xl border border-slate-100 shadow-sm space-y-4">
            <div className="flex items-center justify-between border-b border-slate-100 pb-3">
              <div className="flex items-center gap-2">
                <CheckCircle2 className="w-5 h-5 text-indigo-600 shrink-0" />
                <div>
                  <h4 className="text-xs font-extrabold text-slate-800 uppercase tracking-wider">
                    Detail Pranota dalam Pembayaran Ini
                  </h4>
                  <p className="text-[10px] text-slate-400 mt-0.5">
                    Daftar pranota yang dilunasi dengan No. Pembayaran <span className="font-mono font-black text-indigo-700">{selectedPaymentNo}</span>
                  </p>
                </div>
              </div>
              <div className="flex items-center gap-2">
                <span className="text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 px-2 py-1 rounded-md">
                  {selectedPranotasGrouped.length} Pranota
                </span>
                {selectedPranotasGrouped.length > 0 && (
                  <button
                    type="button"
                    onClick={() => {
                      if (confirm('Apakah Anda yakin ingin melepas semua pranota dari pembayaran ini sekaligus?')) {
                        const allIds = selectedPranotasGrouped.flatMap(g => g.id_tagihan_list);
                        handleDetachPranotaFromPayment(allIds);
                        triggerNoti('sukses', 'Semua pranota berhasil dilepas dari pembayaran.');
                      }
                    }}
                    className="text-[10px] font-bold text-rose-600 hover:text-white hover:bg-rose-600 border border-rose-200 px-2 py-1 rounded-md transition-all cursor-pointer bg-white"
                    title="Lepas semua pranota dari pembayaran ini sekaligus"
                  >
                    ✕ Lepas Semua
                  </button>
                )}
                <button
                  type="button"
                  onClick={() => {
                    setSelectedPaymentNo('');
                    setSelectedPaymentDate('');
                  }}
                  className="p-1 hover:bg-slate-100 rounded-full text-slate-400 hover:text-slate-650"
                  title="Tutup & Selesai"
                >
                  <X className="w-4 h-4" />
                </button>
              </div>
            </div>

            <div className="space-y-4">
              {/* TABLE OF MATCHED PRANOTAS */}
              <div className="overflow-x-auto border border-slate-150 rounded-xl bg-white shadow-3xs">
                <table className="w-full text-left border-collapse text-[10px] font-sans">
                  <thead>
                    <tr className="bg-slate-50 text-slate-500 font-bold border-b border-slate-200">
                      <th className="p-2.5 py-3">No. Pranota</th>
                      <th className="p-2.5 py-3">Tgl. Pranota</th>
                      <th className="p-2.5 py-3 text-right">Estimasi</th>
                      <th className="p-2.5 py-3 text-right">Real</th>
                      <th className="p-2.5 py-3 text-right">Selisih</th>
                      <th className="p-2.5 py-3">Keterangan</th>
                      <th className="p-2.5 py-3 text-center">Aksi</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-slate-100">
                    {selectedPranotasGrouped.length === 0 ? (
                      <tr>
                        <td colSpan={7} className="p-12 text-center text-slate-400 italic">
                          Grup Pembayaran ini masih kosong. Silakan cari dan tambahkan "Pranota" dari panel kanan!
                        </td>
                      </tr>
                    ) : (
                      selectedPranotasGrouped.map(g => {
                        const hasOverride = g.periods.some(p => p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined);
                        const displayReal = hasOverride ? g.totalAktual : null;

                        return (
                          <tr key={g.nomor_pranota} className="hover:bg-slate-50/50 transition-colors">
                            <td className="p-2.5 font-bold text-slate-800">
                              {g.nomor_pranota}
                            </td>
                            <td className="p-2.5 font-mono text-slate-500">
                              {formatIndoDate(g.tanggal_pranota)}
                            </td>
                            <td className="p-2.5 text-right font-mono text-slate-650">
                              {formatRupiah(g.totalEstimasi)}
                            </td>
                            <td className="p-1 text-right bg-amber-50/20">
                              <FastNumberInput
                                value={displayReal}
                                placeholder={String(g.totalEstimasi)}
                                onChange={(val) => {
                                  handleUpdatePranotaRealValue(g.id_tagihan_list, val);
                                }}
                                className="w-24 text-[10px] font-mono border border-slate-200 rounded px-1.5 py-1 text-right bg-white focus:ring-1 focus:ring-indigo-500"
                              />
                            </td>
                            <td className="p-2.5 text-right font-mono">
                              {g.selisih === 0 ? (
                                <span className="text-[9px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded-sm border border-emerald-100">Pas</span>
                              ) : (
                                <span className={`text-[9px] font-mono font-bold px-1.5 py-0.5 rounded-sm border ${g.selisih > 0 ? 'text-amber-700 bg-amber-50 border-amber-200' : 'text-rose-700 bg-rose-50 border-rose-200'}`}>
                                  {g.selisih > 0 ? '+' : ''}{formatRupiah(g.selisih)}
                                </span>
                              )}
                            </td>
                            <td className="p-1">
                              <FastTextInput
                                value={g.keterangan_selisih}
                                placeholder={g.selisih !== 0 ? "Wajib diisi..." : "Keterangan..."}
                                onChange={(val) => handleUpdatePranotaKeterangan(g.id_tagihan_list, val)}
                                className={`w-full text-[10px] border rounded px-1.5 py-1 text-left ${
                                  g.selisih !== 0 && (!g.keterangan_selisih || g.keterangan_selisih.trim() === '')
                                    ? 'border-rose-300 bg-rose-50/50 placeholder-rose-400 focus:ring-rose-500 text-rose-900 font-medium'
                                    : 'border-slate-200 bg-white text-slate-800'
                                }`}
                              />
                            </td>
                            <td className="p-2.5 text-center">
                              <button
                                type="button"
                                onClick={() => handleDetachPranotaFromPayment(g.id_tagihan_list)}
                                className="px-2 py-1 bg-amber-50 hover:bg-rose-50 border border-amber-200 hover:border-rose-200 text-amber-700 hover:text-rose-700 rounded text-[9px] font-bold cursor-pointer transition-colors"
                              >
                                ✕ Lepas
                              </button>
                            </td>
                          </tr>
                        );
                      })
                    )}
                  </tbody>
                </table>
              </div>

              {/* TOTALS & SAVE ACTION BUTTONS */}
              <div className="bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-3">
                <div className="grid grid-cols-2 md:grid-cols-4 gap-2 text-center">
                  <div className="bg-white p-2 rounded-lg border border-slate-100 shadow-3xs">
                    <span className="text-[8px] font-bold text-slate-400 block uppercase">Estimasi</span>
                    <span className="text-xs font-mono font-bold text-slate-700">{formatRupiah(totalEstimasiPaymentGroup)}</span>
                  </div>
                  <div className="bg-white p-2 rounded-lg border border-slate-100 shadow-3xs">
                    <span className="text-[8px] font-bold text-slate-400 block uppercase">Aktual / Riil</span>
                    <span className="text-xs font-mono font-bold text-slate-800">{formatRupiah(totalAktualPaymentGroup)}</span>
                  </div>
                  <div className="bg-white p-2 rounded-lg border border-slate-100 shadow-3xs">
                    <span className="text-[8px] font-bold text-indigo-500 block uppercase">PPN (11%)</span>
                    <span className="text-xs font-mono font-bold text-indigo-700">+{formatRupiah(totalPPNPaymentGroup)}</span>
                  </div>
                  <div className="bg-white p-2 rounded-lg border border-slate-100 shadow-3xs">
                    <span className="text-[8px] font-bold text-rose-500 block uppercase">PPh (2%)</span>
                    <span className="text-xs font-mono font-bold text-rose-600 font-medium">-{formatRupiah(totalPPhPaymentGroup)}</span>
                  </div>
                </div>

                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2 border-t border-slate-200">
                  <div>
                    <span className="text-[9px] font-extrabold text-slate-400 uppercase tracking-wider block">Grand Net Total</span>
                    <span className="text-sm font-extrabold text-emerald-850 font-mono">{formatRupiah(grandNetTotalPaymentGroup)}</span>
                  </div>
                  
                  <div className="flex items-center gap-2">
                    <button
                      type="button"
                      onClick={() => {
                        setSelectedPaymentNo('');
                        setSelectedPaymentDate('');
                      }}
                      className="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-all cursor-pointer"
                    >
                      Batal
                    </button>
                    <button
                      type="button"
                      onClick={() => {
                        if (!selectedPaymentNo.trim()) {
                          triggerNoti('error', 'Silakan isi No. Pembayaran / EBK terlebih dahulu.');
                          return;
                        }
                        
                        const paymentNo = selectedPaymentNo.trim();
                        
                        // Validasi No. Pembayaran tidak boleh sama dengan No. Pranota
                        const isSameAsPranota = allPeriods.some(p => p.nomor_pranota && p.nomor_pranota.toLowerCase().trim() === paymentNo.toLowerCase());
                        if (isSameAsPranota) {
                          triggerNoti('error', `Nomor Pembayaran tidak boleh sama dengan Nomor Pranota yang sudah ada (${paymentNo})!`);
                          return;
                        }

                        const periodIds = selectedPaymentPeriods.map(p => p.id_tagihan);
                        if (periodIds.length === 0) {
                          triggerNoti('error', 'Grup Pembayaran ini kosong! Harap tambah minimal 1 Pranota dari panel kanan.');
                          return;
                        }

                        // Save payment as Lunas
                        const updates = {
                          status_bayar: 'Lunas',
                          nomor_bayar: selectedPaymentNo,
                          tanggal_bayar: selectedPaymentDate || utcTime.split('T')[0]
                        };
                        handleBulkUpdate(periodIds, updates, 'Lunas');
                        triggerNoti('sukses', `Sukses! Pembayaran "${selectedPaymentNo}" telah berhasil disimpan sebagai Lunas.`);
                        setSelectedPaymentNo('');
                        setSelectedPaymentDate('');
                      }}
                      className="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 cursor-pointer font-extrabold"
                    >
                      <CheckCircle2 className="w-4 h-4" />
                      <span>Simpan Pembayaran Lunas</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          {/* RIGHT PANEL: AVAILABLE OUTSTANDING PRANOTAS */}
          <div className="lg:col-span-5 bg-white p-5 rounded-2xl border border-slate-100 shadow-sm space-y-4">
            <div className="flex items-center justify-between border-b border-slate-100 pb-3">
              <div className="flex items-center gap-2">
                <PlusCircle className="w-5 h-5 text-indigo-600 shrink-0" />
                <div>
                  <h4 className="text-xs font-extrabold text-slate-800 uppercase tracking-wider">
                    Daftar Pranota Tersedia (Hasil Tab 2)
                  </h4>
                  <p className="text-[10px] text-slate-400 mt-0.5">
                    Pranota vendor outstanding yang belum memiliki Pembayaran
                  </p>
                </div>
              </div>
              <span className="text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 px-2 py-1 rounded-md">
                {loosePranotasGrouped.length} Pranota
              </span>
            </div>

            {/* Search bar inside Right Panel */}
            <div className="relative">
              <Search className="w-4 h-4 absolute left-3 top-2.5 text-slate-400" />
              <input
                type="text"
                placeholder="Cari No. Pranota..."
                value={searchPranotaNo}
                onChange={(e) => setSearchPranotaNo(e.target.value)}
                className="w-full text-xs border border-slate-200 rounded-xl pl-9 pr-4 py-2 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:outline-none placeholder-slate-400"
              />
            </div>

            {loosePranotasGrouped.length === 0 ? (
              <div className="text-center py-12 text-xs text-slate-400 italic bg-slate-50 border border-dashed border-slate-200 rounded-xl">
                Tidak ada pranota outstanding yang tersedia untuk kriteria vendor &amp; pencarian ini.
              </div>
            ) : (
              <div className="overflow-x-auto border border-slate-150 rounded-xl bg-white shadow-3xs">
                <table className="w-full text-left border-collapse text-[10px] font-sans">
                  <thead>
                    <tr className="bg-slate-50 text-slate-500 font-bold border-b border-slate-200">
                      <th className="p-2 py-2.5">No. Pranota</th>
                      <th className="p-2 py-2.5">Tgl. Pranota</th>
                      <th className="p-2 py-2.5 text-right">Estimasi</th>
                      <th className="p-2 py-2.5 text-center">Aksi</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-slate-100">
                    {loosePranotasGrouped.map(pg => {
                      return (
                        <tr key={pg.nomor_pranota} className="hover:bg-slate-50/60 transition-colors">
                          <td className="p-2 py-2.5">
                            <div className="font-bold text-slate-800">{pg.nomor_pranota}</div>
                            <div className="text-[9px] text-slate-400 font-medium mt-0.5">Vendor: {pg.customerName}</div>
                          </td>
                          <td className="p-2 py-2.5 font-mono text-slate-500">
                            {formatIndoDate(pg.tanggal_pranota)}
                          </td>
                          <td className="p-2 py-2.5 text-right font-mono font-bold text-indigo-700">
                            {formatRupiah(pg.totalEstimasi)}
                          </td>
                          <td className="p-2 py-2.5 text-center">
                            <button
                              type="button"
                              onClick={() => {
                                if (!selectedPaymentNo) {
                                  triggerNoti('error', 'Silakan ketik atau pilih No. Pembayaran terlebih dahulu di kolom kiri atas.');
                                  return;
                                  }
                                handleAttachPranotaToPayment(pg.nomor_pranota);
                              }}
                              className="px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-[9px] font-extrabold flex items-center gap-1 cursor-pointer transition-colors shadow-3xs mx-auto"
                            >
                              <Plus className="w-3 h-3 text-white" />
                              <span>Tambah</span>
                            </button>
                          </td>
                        </tr>
                      );
                    })}
                  </tbody>
                </table>
              </div>
            )}
          </div>

        </div>
      )}
    </div>
  );
};
