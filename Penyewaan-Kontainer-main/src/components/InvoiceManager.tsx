import React, { useState } from 'react';
import { AppState, compileAllPeriods } from '../dataStore';
import { TagihanBulan, InvoiceGrup } from '../types';
import { formatRupiah, formatIndoDate, parseInputDate, formatEntryDate } from '../utils';
import { Check, Loader, FileText, Printer, CheckCircle2, Circle, AlertCircle, ShoppingBag, ArrowRight, Trash2, Calendar, FileSpreadsheet, Edit3, Sparkles, Coins, Building2, ArrowUpRight, PlusCircle, Search, Plus } from 'lucide-react';
import SearchableSelect from './SearchableSelect';
import SearchableCombobox from './SearchableCombobox';
import { PaymentWorkspace } from './PaymentWorkspace';
import { FormDateInput } from './FormDateInput';

interface InvoiceManagerProps {
  state: AppState;
  onStateChange: (updated: AppState) => void;
  utcTime: string;
}

interface EditableDateCellProps {
  value: string | null | undefined;
  onChange: (val: string) => void;
  placeholder?: string;
  className?: string;
}

const EditableDateCell: React.FC<EditableDateCellProps> = ({ value, onChange, placeholder = 'dd Mmm yy', className }) => {
  const [typedValue, setTypedValue] = useState<string | null>(null);

  const isEditing = typedValue !== null;

  const handleFocus = () => {
    if (value && /^\d{4}-\d{2}-\d{2}$/.test(value)) {
      setTypedValue(formatEntryDate(value));
    } else {
      setTypedValue(value || '');
    }
  };

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setTypedValue(e.target.value);
  };

  const handleBlur = () => {
    if (typedValue === null) return;
    const trimmed = typedValue.trim();
    setTypedValue(null);

    // Only update global state if there is an actual change to prevent unnecessary re-renders
    const currentDisplayVal = value && /^\d{4}-\d{2}-\d{2}$/.test(value) ? formatEntryDate(value) : (value || '');
    if (trimmed !== currentDisplayVal) {
      if (!trimmed) {
        onChange('');
        return;
      }
      const parsed = parseInputDate(trimmed);
      if (parsed) {
        onChange(parsed);
      } else {
        onChange(trimmed);
      }
    }
  };

  const handleKeyDown = (e: React.KeyboardEvent<HTMLInputElement>) => {
    if (e.key === 'Enter') {
      (e.target as HTMLInputElement).blur();
    }
  };

  const displayVal = isEditing
    ? typedValue
    : (value && /^\d{4}-\d{2}-\d{2}$/.test(value) ? formatIndoDate(value) : (value || ''));

  return (
    <input
      type="text"
      value={displayVal}
      onFocus={handleFocus}
      onChange={handleChange}
      onBlur={handleBlur}
      onKeyDown={handleKeyDown}
      placeholder={placeholder}
      className={`${className} text-slate-800 font-medium`}
    />
  );
};

interface FastNumberInputProps {
  value: number | null | undefined;
  placeholder?: string;
  onChange: (val: number | null) => void;
  className?: string;
}

const FastNumberInput: React.FC<FastNumberInputProps> = ({ value, placeholder, onChange, className }) => {
  const [localVal, setLocalVal] = useState<string>(value !== null && value !== undefined ? String(value) : '');

  React.useEffect(() => {
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

  React.useEffect(() => {
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

export default function InvoiceManager({ state, onStateChange, utcTime }: InvoiceManagerProps) {
  // Compile billing periods combining generators and localStorage overrides
  const allPeriods = React.useMemo(() => {
    return compileAllPeriods(state, utcTime);
  }, [state, utcTime]);

  const isSewaIn = true;
  const [custFilter, setCustFilter] = useState('');
  const [statusFilter, setStatusFilter] = useState('');
  const [searchNoKontainer, setSearchNoKontainer] = useState('');
  const [filterRentangSewa, setFilterRentangSewa] = useState('');
  const [ledgerPage, setLedgerPage] = useState(1);
  const ledgerPageSize = 20;

  React.useEffect(() => {
    setLedgerPage(1);
  }, [custFilter, statusFilter, searchNoKontainer, filterRentangSewa]);
  const [activeViewTab, setActiveViewTab] = useState<'sheet' | 'group' | 'collective' | 'report'>('sheet');
  const [selectedNota, setSelectedNota] = useState<string>('');
  const [selectedRowIds, setSelectedRowIds] = useState<string[]>([]);
  const [ledgerSortField, setLedgerSortField] = useState<string>('');
  const [ledgerSortAsc, setLedgerSortAsc] = useState<boolean>(true);

  interface RowDraft {
    buktiBayar: string;
    tglBayar: string;
    adjustmentBiaya: string;
    adjustmentKeterangan: string;
    statusPembayaran: 'Belum Bayar' | 'Lunas';
  }
  const [rowDrafts, setRowDrafts] = useState<Record<string, RowDraft>>({});
  const [collectiveSearch, setCollectiveSearch] = useState('');
  const [collectiveStatusFilter, setCollectiveStatusFilter] = useState('Semua');
  const [collectiveCustFilter, setCollectiveCustFilter] = useState('');
  const [selectedCollectiveInvoices, setSelectedCollectiveInvoices] = useState<string[]>([]);
  
  const [bulkInvoiceNo, setBulkInvoiceNo] = useState('');
  const [bulkInvoiceDate, setBulkInvoiceDate] = useState('');

  const [bulkPranotaNo, setBulkPranotaNo] = useState('');
  const [bulkPranotaDate, setBulkPranotaDate] = useState('');
  const [bulkPranotaStatus, setBulkPranotaStatus] = useState<'Pranota' | 'Belum Bayar' | ''>('Pranota');

  const [bulkBuktiBayarNo, setBulkBuktiBayarNo] = useState('');
  const [bulkBuktiBayarDate, setBulkBuktiBayarDate] = useState('');

  // States for Tab 1 Rekonsiliasi Tagihan Vendor
  const [selectedVendorTagihanNo, setSelectedVendorTagihanNo] = useState('');
  const [selectedVendorCustomer, setSelectedVendorCustomer] = useState('');
  const [searchDraftContainer, setSearchDraftContainer] = useState('');
  const [selectedVendorTagihanDate, setSelectedVendorTagihanDate] = useState('');

  // Separated Entry and Search Submodes for Tab 1
  const [sheetSubMode, setSheetSubMode] = useState<'search' | 'create'>('search');
  const [newVendorId, setNewVendorId] = useState('');
  const [newInvoiceNo, setNewInvoiceNo] = useState('');
  const [newInvoiceDate, setNewInvoiceDate] = useState('');

  // States for Tab 2 (Tagihan -> Draft Pembayaran)
  const [selectedVendorPranotaNo, setSelectedVendorPranotaNo] = useState('');
  const [selectedVendorPranotaDate, setSelectedVendorPranotaDate] = useState('');
  const [searchTagihanNo, setSearchTagihanNo] = useState('');
  const [pranotaSubMode, setPranotaSubMode] = useState<'search' | 'create'>('search');
  const [newPranotaVendorId, setNewPranotaVendorId] = useState('');
  const [newPranotaNo, setNewPranotaNo] = useState('');
  const [newPranotaDate, setNewPranotaDate] = useState('');
  const [pranotaSearchStartDate, setPranotaSearchStartDate] = useState('');
  const [pranotaSearchEndDate, setPranotaSearchEndDate] = useState('');

  // States for Tab 3 (Draft Pembayaran -> Lunas)
  const [selectedPaymentNo, setSelectedPaymentNo] = useState('');
  const [selectedPaymentDate, setSelectedPaymentDate] = useState('');
  const [searchPranotaNo, setSearchPranotaNo] = useState('');

  // Local state for Entry Tagihan Manual
  const [manualSewaId, setManualSewaId] = useState('');
  const [manualBulanKe, setManualBulanKe] = useState('1');
  const [manualTglAwal, setManualTglAwal] = useState('');
  const [manualTglAkhir, setManualTglAkhir] = useState('');
  const [manualTipeTarif, setManualTipeTarif] = useState<'BULANAN' | 'PRORATE' | 'HARIAN'>('BULANAN');
  const [manualNominalEstimasi, setManualNominalEstimasi] = useState('');
  const [manualNominalRiil, setManualNominalRiil] = useState('');
  const [manualKeterangan, setManualKeterangan] = useState('');

  React.useEffect(() => {
    if (!manualSewaId) {
      setManualNominalEstimasi('');
      setManualTipeTarif('BULANAN');
      return;
    }
    const sewa = state.sewas.find(s => s.id_sewa === manualSewaId);
    if (sewa) {
      setManualTipeTarif(sewa.jenis_tarif === 'Bulanan' ? 'BULANAN' : 'HARIAN');
      setManualNominalEstimasi(String(sewa.jenis_tarif === 'Bulanan' ? sewa.tarif_bulanan : sewa.tarif_harian));
      
      // Auto pre-fill dates based on sewa.tanggal_sewa
      if (!manualTglAwal) {
        setManualTglAwal(sewa.tanggal_sewa);
        // End date: 30 days later
        const start = new Date(sewa.tanggal_sewa);
        start.setDate(start.getDate() + 30);
        setManualTglAkhir(start.toISOString().split('T')[0]);
      }
    }
  }, [manualSewaId, state.sewas]);

  const [tab2Search, setTab2Search] = useState('');
  const [tab2CustFilter, setTab2CustFilter] = useState('');
  const [tab2StatusFilter, setTab2StatusFilter] = useState('Semua');
  const [selectedTab2Invoices, setSelectedTab2Invoices] = useState<string[]>([]);
  const [expandedInvoices, setExpandedInvoices] = useState<Record<string, boolean>>({});

  interface ParsedPaymentRow {
    lineNum: number;
    raw: string;
    nomorBayar: string;
    tanggalBayar: string;
    parsedTglIso: string | null;
    nomorNota: string;
    isValidNota: boolean;
    customerId: string;
    customerName: string;
    grandTotal: number;
    textStatus: string;
  }
  const [importPaymentOpen, setImportPaymentOpen] = useState(false);
  const [importPaymentText, setImportPaymentText] = useState('');
  const [importPaymentPreview, setImportPaymentPreview] = useState<ParsedPaymentRow[]>([]);
  const [isImportProcessed, setIsImportProcessed] = useState(false);

  const [adjBiaya, setAdjBiaya] = useState<string>('');
  const [adjKet, setAdjKet] = useState<string>('');

  React.useEffect(() => {
    setSelectedRowIds([]);
    const currentInv = state.invoices.find(i => i.nomor_invoice === selectedNota);
    setAdjBiaya(currentInv?.adjustment_biaya !== undefined ? String(currentInv.adjustment_biaya) : '');
    setAdjKet(currentInv?.adjustment_keterangan || '');
  }, [selectedNota, state.invoices]);

  React.useEffect(() => {
    setSelectedCollectiveInvoices([]);
  }, [collectiveSearch, collectiveCustFilter, collectiveStatusFilter, activeViewTab]);

  React.useEffect(() => {
    if (!selectedVendorCustomer && state.customers.length > 0) {
      setSelectedVendorCustomer(state.customers[0].id_customer);
    }
  }, [state.customers, selectedVendorCustomer]);

  // Auto-detect and switch vendor if the typed/selected vendor tagihan matches an existing tagihan
  React.useEffect(() => {
    if (selectedVendorTagihanNo.trim()) {
      const match = allPeriods.find(p => p.nomor_invoice_grup && p.nomor_invoice_grup.toLowerCase() === selectedVendorTagihanNo.toLowerCase().trim());
      if (match) {
        const sewa = state.sewas.find(s => s.id_sewa === match.id_sewa);
        if (sewa && sewa.id_customer !== selectedVendorCustomer) {
          setSelectedVendorCustomer(sewa.id_customer);
        }
      } else {
        const invMatch = state.invoices.find(i => i.nomor_invoice && i.nomor_invoice.toLowerCase() === selectedVendorTagihanNo.toLowerCase().trim());
        if (invMatch && invMatch.id_customer !== selectedVendorCustomer) {
          setSelectedVendorCustomer(invMatch.id_customer);
        }
      }
    }
  }, [selectedVendorTagihanNo, allPeriods, state.sewas, state.invoices, selectedVendorCustomer]);

  // Auto-detect and switch vendor if the typed/selected vendor pranota matches an existing pranota
  React.useEffect(() => {
    if (selectedVendorPranotaNo.trim()) {
      const match = allPeriods.find(p => p.nomor_pranota && p.nomor_pranota.toLowerCase() === selectedVendorPranotaNo.toLowerCase().trim());
      if (match) {
        const sewa = state.sewas.find(s => s.id_sewa === match.id_sewa);
        if (sewa && sewa.id_customer !== selectedVendorCustomer) {
          setSelectedVendorCustomer(sewa.id_customer);
        }
      }
    }
  }, [selectedVendorPranotaNo, allPeriods, state.sewas, selectedVendorCustomer]);

  // Auto-detect and switch vendor if the typed/selected payment matches an existing payment
  React.useEffect(() => {
    if (selectedPaymentNo.trim()) {
      const match = allPeriods.find(p => p.nomor_bayar && p.nomor_bayar.toLowerCase() === selectedPaymentNo.toLowerCase().trim());
      if (match) {
        const sewa = state.sewas.find(s => s.id_sewa === match.id_sewa);
        if (sewa && sewa.id_customer !== selectedVendorCustomer) {
          setSelectedVendorCustomer(sewa.id_customer);
        }
      }
    }
  }, [selectedPaymentNo, allPeriods, state.sewas, selectedVendorCustomer]);

  const toggleLedgerSort = (field: string) => {
    if (ledgerSortField === field) {
      setLedgerSortAsc(!ledgerSortAsc);
    } else {
      setLedgerSortField(field);
      setLedgerSortAsc(true);
    }
  };

  const renderSortableHeader = (field: string, label: string, extraClasses = '') => {
    const isSorted = ledgerSortField === field;
    return (
      <th 
        onClick={() => toggleLedgerSort(field)} 
        className={`cursor-pointer hover:bg-slate-200/80 hover:text-slate-900 select-none transition-all p-2 py-3 border-r border-slate-200 ${extraClasses}`}
        title={`Urutkan berdasarkan ${label}`}
      >
        <div className="flex items-center justify-between gap-1.5">
          <span className="truncate">{label}</span>
          <span className="text-[8px] font-sans text-slate-500 bg-slate-200/40 px-1 py-0.5 rounded-sm shrink-0">
            {isSorted ? (ledgerSortAsc ? '▲' : '▼') : '↕'}
          </span>
        </div>
      </th>
    );
  };

  // Print overlay modal
  const [printInvoice, setPrintInvoice] = useState<InvoiceGrup | null>(null);

  // Notification state
  const [noti, setNoti] = useState<{ type: 'sukses' | 'error'; msg: string } | null>(null);

  const triggerNoti = (type: 'sukses' | 'error', msg: string) => {
    setNoti({ type, msg });
    setTimeout(() => setNoti(null), 4000);
  };

  // Generate unique rentang sewa
  const distinctRanges = (() => {
    const sewasMap = new Map<string, typeof state.sewas[0]>();
    allPeriods.forEach(p => {
      const sObj = state.sewas.find(s => s.id_sewa === p.id_sewa);
      if (sObj) {
        if (searchNoKontainer) {
          if (sObj.no_kontainer.toLowerCase().includes(searchNoKontainer.toLowerCase())) {
            sewasMap.set(sObj.id_sewa, sObj);
          }
        } else {
          sewasMap.set(sObj.id_sewa, sObj);
        }
      }
    });

    const sortedSewas = Array.from(sewasMap.values()).sort((a, b) => {
      return b.tanggal_sewa.localeCompare(a.tanggal_sewa);
    });

    const ranges = sortedSewas.map(sObj => {
      const startStr = formatIndoDate(sObj.tanggal_sewa);
      const endStr = sObj.tanggal_kembali ? formatIndoDate(sObj.tanggal_kembali) : 'Saat Ini';
      return `${startStr} - ${endStr}`;
    });

    return Array.from(new Set(ranges)).filter(Boolean);
  })();

  // Distill existing draft and saved tagihans (all vendor tagihans/invoices) filtered by selected vendor if selected
  const existingDraftVendorTagihans = React.useMemo(() => {
    const set = new Set<string>();
    allPeriods.forEach(p => {
      const sObj = state.sewas.find(s => s.id_sewa === p.id_sewa);
      const isMatch = !selectedVendorCustomer || sObj?.id_customer === selectedVendorCustomer;
      if (p.nomor_invoice_grup && isMatch) {
        set.add(p.nomor_invoice_grup);
      }
    });
    state.invoices.forEach(i => {
      const isMatch = !selectedVendorCustomer || i.id_customer === selectedVendorCustomer;
      if (i.nomor_invoice && isMatch) {
        set.add(i.nomor_invoice);
      }
    });
    return Array.from(set).sort();
  }, [allPeriods, selectedVendorCustomer, state.sewas, state.invoices]);

  // Distill existing pranotas filtered by selected vendor if selected with optional Date Range
  const existingDraftVendorPranotas = React.useMemo(() => {
    const set = new Set<string>();
    allPeriods.forEach(p => {
      const sObj = state.sewas.find(s => s.id_sewa === p.id_sewa);
      const isMatch = !selectedVendorCustomer || sObj?.id_customer === selectedVendorCustomer;
      let isMatchDate = true;
      if (pranotaSearchStartDate && p.tanggal_pranota) {
        if (p.tanggal_pranota < pranotaSearchStartDate) isMatchDate = false;
      }
      if (pranotaSearchEndDate && p.tanggal_pranota) {
        if (p.tanggal_pranota > pranotaSearchEndDate) isMatchDate = false;
      }
      if (p.nomor_pranota && isMatch && isMatchDate) {
        set.add(p.nomor_pranota);
      }
    });
    return Array.from(set).sort();
  }, [allPeriods, selectedVendorCustomer, state.sewas, pranotaSearchStartDate, pranotaSearchEndDate]);

  // Distill existing payments/EBK filtered by selected vendor if selected
  const existingDraftPayments = React.useMemo(() => {
    const set = new Set<string>();
    allPeriods.forEach(p => {
      const sObj = state.sewas.find(s => s.id_sewa === p.id_sewa);
      const isMatch = !selectedVendorCustomer || sObj?.id_customer === selectedVendorCustomer;
      if (p.nomor_bayar && isMatch) {
        set.add(p.nomor_bayar);
      }
    });
    return Array.from(set).sort();
  }, [allPeriods, selectedVendorCustomer, state.sewas]);

  // Autocomplete suggestions lists
  const autocompleteTab1 = React.useMemo(() => {
    const items = new Set<string>();
    state.sewas.forEach(s => { if (s.no_kontainer) items.add(s.no_kontainer); });
    allPeriods.forEach(p => {
      if (p.id_tagihan) items.add(p.id_tagihan);
      if (p.nomor_invoice_grup) items.add(p.nomor_invoice_grup);
    });
    return Array.from(items).sort();
  }, [state.sewas, allPeriods]);

  const autocompleteTab2 = React.useMemo(() => {
    const items = new Set<string>();
    allPeriods.forEach(p => {
      if (p.nomor_invoice_grup) items.add(p.nomor_invoice_grup);
    });
    state.invoices.forEach(i => {
      if (i.nomor_invoice) items.add(i.nomor_invoice);
    });
    return Array.from(items).sort();
  }, [allPeriods, state.invoices]);

  const autocompleteTab3 = React.useMemo(() => {
    const items = new Set<string>();
    allPeriods.forEach(p => {
      if (p.nomor_bayar) items.add(p.nomor_bayar);
      if (p.nomor_invoice_grup) items.add(p.nomor_invoice_grup);
    });
    return Array.from(items).sort();
  }, [allPeriods]);

  // Distil all unique invoice / nota numbers in natural order
  const distinctInvoiceNumbers = Array.from(
    new Set([
      ...allPeriods.map(p => p.nomor_invoice_grup).filter((no): no is string => !!no),
      ...state.invoices.map(i => i.nomor_invoice)
    ])
  );

  React.useEffect(() => {
    if (!selectedNota && distinctInvoiceNumbers.length > 0) {
      setSelectedNota(distinctInvoiceNumbers[0]);
    }
  }, [distinctInvoiceNumbers, selectedNota]);

  React.useEffect(() => {
    if (activeViewTab === 'collective') {
      const updatedDrafts = { ...rowDrafts };
      let hasChanges = false;
      distinctInvoiceNumbers.forEach(no => {
        if (!updatedDrafts[no]) {
          const matched = allPeriods.filter(p => p.nomor_invoice_grup === no);
          const groupInvoice = state.invoices.find(i => i.nomor_invoice === no);
          const firstPeriod = matched[0];
          
          updatedDrafts[no] = {
            buktiBayar: firstPeriod?.nomor_bayar || '',
            tglBayar: firstPeriod?.tanggal_bayar ? formatEntryDate(firstPeriod.tanggal_bayar) : '',
            statusPembayaran: (groupInvoice?.status_pembayaran || (matched.length > 0 && matched.every(p => p.status_bayar === 'Lunas') ? 'Lunas' : 'Belum Bayar')) as 'Belum Bayar' | 'Lunas',
            adjustmentBiaya: groupInvoice?.adjustment_biaya !== undefined ? String(groupInvoice.adjustment_biaya) : '',
            adjustmentKeterangan: groupInvoice?.adjustment_keterangan || ''
          };
          hasChanges = true;
        }
      });
      if (hasChanges) {
        setRowDrafts(updatedDrafts);
      }
    }
  }, [activeViewTab, distinctInvoiceNumbers, state.invoices, allPeriods]);

  // Filters application
  const filteredPeriods = allPeriods.filter(p => {
    const sewa = state.sewas.find(s => s.id_sewa === p.id_sewa);
    if (!sewa) return false;
    
    // Customer filter
    if (custFilter && sewa.id_customer !== custFilter) return false;

    // Status filter
    if (statusFilter && p.status_bayar !== statusFilter) return false;

    // Container, Tagihan, or Pranota search
    if (searchNoKontainer) {
      const q = searchNoKontainer.toLowerCase().trim();
      const matchKontainer = sewa.no_kontainer.toLowerCase().includes(q);
      const matchTagihanId = p.id_tagihan.toLowerCase().includes(q);
      const matchInvoiceNo = p.nomor_invoice_grup && p.nomor_invoice_grup.toLowerCase().includes(q);
      if (!matchKontainer && !matchTagihanId && !matchInvoiceNo) return false;
    }

    // Rentang Sewa filter
    if (filterRentangSewa) {
      const startStr = formatIndoDate(sewa.tanggal_sewa).toLowerCase();
      const endStr = sewa.tanggal_kembali ? formatIndoDate(sewa.tanggal_kembali).toLowerCase() : 'saat ini';
      const rangeStr = `${startStr} - ${endStr}`;
      const query = filterRentangSewa.toLowerCase().trim();
      if (!rangeStr.includes(query) && !startStr.includes(query) && !endStr.includes(query)) {
        return false;
      }
    }

    return true;
  });

  const totalPages = Math.ceil(filteredPeriods.length / ledgerPageSize);
  const paginatedPeriods = filteredPeriods.slice((ledgerPage - 1) * ledgerPageSize, ledgerPage * ledgerPageSize);

  const getCustomerName = (id: string) => {
    const c = state.customers.find(x => x.id_customer === id);
    return c ? c.nama_customer : '-';
  };

  const getSewaContainerNo = (idSewa: string) => {
    const s = state.sewas.find(x => x.id_sewa === idSewa);
    return s ? s.no_kontainer : '-';
  };

  const getSewaContainerSizeDesc = (idSewa: string) => {
    const s = state.sewas.find(x => x.id_sewa === idSewa);
    if (!s) return '-';
    const k = state.kontainers.find(x => x.no_kontainer === s.no_kontainer);
    if (!k) return '-';
    const sz = state.ukurans.find(x => x.id_ukuran === k.id_ukuran);
    return sz ? sz.deskripsi_ukuran : '-';
  };

  // Compile active/outstanding draft tagihans for quick selection dashboard
  const outstandingDraftGroups = React.useMemo(() => {
    const groupsMap = new Map<string, {
      nomor_invoice_grup: string;
      id_customer: string;
      customerName: string;
      status_bayar: string;
      count: number;
      totalEstimasi: number;
      totalAktual: number;
      lastUpdated: string | null;
    }>();

    allPeriods.forEach(p => {
      const groupNo = p.nomor_invoice_grup;
      if (!groupNo || groupNo.trim() === '') return;

      const sObj = state.sewas.find(s => s.id_sewa === p.id_sewa);
      const custId = sObj?.id_customer || '';
      const custName = custId ? getCustomerName(custId) : 'Umum/Campuran';

      const amt = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : p.jumlah_tagihan;

      if (!groupsMap.has(groupNo)) {
        groupsMap.set(groupNo, {
          nomor_invoice_grup: groupNo,
          id_customer: custId,
          customerName: custName,
          status_bayar: p.status_bayar,
          count: 1,
          totalEstimasi: p.jumlah_tagihan,
          totalAktual: amt,
          lastUpdated: p.tanggal_tagihan || p.tanggal_bayar || null
        });
      } else {
        const existing = groupsMap.get(groupNo)!;
        existing.count += 1;
        existing.totalEstimasi += p.jumlah_tagihan;
        existing.totalAktual += amt;
        if (p.status_bayar !== 'Lunas' && existing.status_bayar === 'Lunas') {
          existing.status_bayar = p.status_bayar;
        }
      }
    });

    const list = Array.from(groupsMap.values())
      .filter(g => g.status_bayar !== 'Lunas')
      .sort((a, b) => {
        const statusOrder = { 'Belum Ditagih': 0, 'Pranota': 1, 'Belum Bayar': 2 };
        const orderA = statusOrder[a.status_bayar as keyof typeof statusOrder] ?? 9;
        const orderB = statusOrder[b.status_bayar as keyof typeof statusOrder] ?? 9;
        if (orderA !== orderB) return orderA - orderB;
        return b.nomor_invoice_grup.localeCompare(a.nomor_invoice_grup);
      });

    return list.slice(0, 20);
  }, [allPeriods, state.sewas, state.customers]);

  // Helper to handle all specific inline field corrections smoothly
  const handleUpdateFieldValue = (
    idTagihan: string,
    field: keyof TagihanBulan | string,
    value: any
  ) => {
    const period = allPeriods.find(p => p.id_tagihan === idTagihan);
    if (!period) return;

    const existingOverride = state.paymentOverrides[idTagihan] || {
      status_bayar: 'Belum Ditagih',
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

    let updatedOverride = {
      ...existingOverride,
      [field]: value
    };

    const sewa = state.sewas.find(s => s.id_sewa === period.id_sewa);
    const isNonPpn = sewa?.non_ppn === true;

    if (field === 'nomor_invoice_grup') {
      if (value === null || value === '') {
        // DETACHING/REMOVING from group: reset ALL overrides to revert back to normal/imported state!
        updatedOverride.status_bayar = 'Belum Ditagih';
        updatedOverride.jumlah_tagihan_override = null;
        updatedOverride.ppn = null;
        updatedOverride.pph = null;
        updatedOverride.keterangan_selisih = null;
        updatedOverride.selisih_pembayaran = 0;
        updatedOverride.nomor_invoice_grup = null;
        updatedOverride.tanggal_tagihan = null;
      } else {
        // ATTACHING/ADDING to group: pre-fill the actual nominal with the estimasi so it's ready to edit!
        updatedOverride.status_bayar = 'Belum Bayar';
        if (updatedOverride.jumlah_tagihan_override === null || updatedOverride.jumlah_tagihan_override === undefined) {
          updatedOverride.jumlah_tagihan_override = period.jumlah_tagihan;
          updatedOverride.ppn = isNonPpn ? 0 : Math.round(period.jumlah_tagihan * 0.11);
          updatedOverride.pph = Math.round(period.jumlah_tagihan * 0.02);
        }
      }
    }

    // Calculate core dependencies
    const estimasi = period.jumlah_tagihan; // standard estimated cycle amount
    const currentTagihan = updatedOverride.jumlah_tagihan_override !== undefined && updatedOverride.jumlah_tagihan_override !== null
      ? updatedOverride.jumlah_tagihan_override
      : estimasi;

    // Auto-calculate Difference
    updatedOverride.selisih_pembayaran = currentTagihan - estimasi;

    // Auto-calculate default taxes if they altered the actual Tagihan amount directly
    if (field === 'jumlah_tagihan_override') {
      updatedOverride.ppn = isNonPpn ? 0 : Math.round(currentTagihan * 0.11);
      updatedOverride.pph = Math.round(currentTagihan * 0.02);
    }

    // Set timestamps automatically when moving status to Lunas
    if (field === 'status_bayar') {
      if (value === 'Lunas') {
        if (!updatedOverride.tanggal_bayar) {
          updatedOverride.tanggal_bayar = utcTime.split('T')[0];
        }
        if (!updatedOverride.tanggal_tagihan) {
          updatedOverride.tanggal_tagihan = utcTime.split('T')[0];
        }
      } else if (value === 'Pranota' || value === 'Belum Bayar') {
        if (!updatedOverride.tanggal_tagihan) {
          updatedOverride.tanggal_tagihan = utcTime.split('T')[0];
        }
      }
    }

    onStateChange({
      ...state,
      paymentOverrides: {
        ...state.paymentOverrides,
        [idTagihan]: updatedOverride
      }
    });
  };

  const handleBulkUpdate = (
    idTagihans: string[],
    updates: Record<string, any>,
    newStatus?: 'Belum Ditagih' | 'Belum Bayar' | 'Lunas' | 'Pranota'
  ) => {
    if (idTagihans.length === 0) return;

    const updatedOverrides = { ...state.paymentOverrides };

    idTagihans.forEach(idTagihan => {
      const period = allPeriods.find(p => p.id_tagihan === idTagihan);
      if (!period) return;

      const existingOverride = updatedOverrides[idTagihan] || {
        status_bayar: 'Belum Ditagih',
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

      let updatedOverride = {
        ...existingOverride,
        ...updates
      };

      if (newStatus) {
        updatedOverride.status_bayar = newStatus;
        if (newStatus === 'Lunas') {
          if (!updatedOverride.tanggal_bayar) {
            updatedOverride.tanggal_bayar = utcTime.split('T')[0];
          }
          if (!updatedOverride.tanggal_tagihan) {
            updatedOverride.tanggal_tagihan = utcTime.split('T')[0];
          }
        } else if (newStatus === 'Pranota' || newStatus === 'Belum Bayar') {
          if (!updatedOverride.tanggal_tagihan) {
            updatedOverride.tanggal_tagihan = utcTime.split('T')[0];
          }
        }
      }

      // Calculate core dependencies
      const estimasi = period.jumlah_tagihan;
      const currentTagihan = updatedOverride.jumlah_tagihan_override !== undefined && updatedOverride.jumlah_tagihan_override !== null
        ? updatedOverride.jumlah_tagihan_override
        : estimasi;

      // Auto-calculate Difference
      updatedOverride.selisih_pembayaran = currentTagihan - estimasi;

      updatedOverrides[idTagihan] = updatedOverride;
    });

    onStateChange({
      ...state,
      paymentOverrides: updatedOverrides
    });

    // Clear selection after bulk update
    setSelectedRowIds([]);
  };

  // Helper to update a field across all sirkulasi periods under a specific Invoice Number (propagates down)
  const handleUpdateInvoiceFieldValue = (
    invoiceNo: string,
    field: string,
    value: any
  ) => {
    const matchedPeriods = allPeriods.filter(p => p.nomor_invoice_grup === invoiceNo);
    const updatedOverrides = { ...state.paymentOverrides };
    
    if (field === 'nomor_invoice_grup') {
      const newInvoiceNo = String(value).trim();
      if (!newInvoiceNo) return;
      
      matchedPeriods.forEach(p => {
        const existing = updatedOverrides[p.id_tagihan] || {
          status_bayar: 'Belum Ditagih',
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
          nomor_invoice_grup: newInvoiceNo
        };
      });

      // Update state.invoices
      const updatedInvoices = state.invoices.map(inv => {
        if (inv.nomor_invoice.toLowerCase() === invoiceNo.toLowerCase()) {
          return {
            ...inv,
            nomor_invoice: newInvoiceNo
          };
        }
        return inv;
      });

      onStateChange({
        ...state,
        paymentOverrides: updatedOverrides,
        invoices: updatedInvoices
      });
      return;
    }

    if (field === 'tanggal_tagihan') {
      matchedPeriods.forEach(p => {
        const existing = updatedOverrides[p.id_tagihan] || {
          status_bayar: 'Belum Ditagih',
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
          tanggal_tagihan: value
        };
      });

      const updatedInvoices = state.invoices.map(inv => {
        if (inv.nomor_invoice.toLowerCase() === invoiceNo.toLowerCase()) {
          return {
            ...inv,
            tanggal_invoice: value
          };
        }
        return inv;
      });

      onStateChange({
        ...state,
        paymentOverrides: updatedOverrides,
        invoices: updatedInvoices
      });
      return;
    }

    if (field === 'nomor_bayar') {
      matchedPeriods.forEach(p => {
        const existing = updatedOverrides[p.id_tagihan] || {
          status_bayar: 'Belum Ditagih',
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
          nomor_bayar: value
        };
      });

      onStateChange({
        ...state,
        paymentOverrides: updatedOverrides
      });
      return;
    }

    if (field === 'tanggal_bayar') {
      matchedPeriods.forEach(p => {
        const existing = updatedOverrides[p.id_tagihan] || {
          status_bayar: 'Belum Ditagih',
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
          tanggal_bayar: value
        };
      });

      onStateChange({
        ...state,
        paymentOverrides: updatedOverrides
      });
      return;
    }

    if (field === 'status_bayar') {
      matchedPeriods.forEach(p => {
        const existing = updatedOverrides[p.id_tagihan] || {
          status_bayar: 'Belum Ditagih',
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
        const updated = {
          ...existing,
          status_bayar: value as any
        };
        if (value === 'Lunas') {
          if (!updated.tanggal_bayar) {
            updated.tanggal_bayar = utcTime.split('T')[0];
          }
          if (!updated.tanggal_tagihan) {
            updated.tanggal_tagihan = utcTime.split('T')[0];
          }
        } else if (value === 'Pranota' || value === 'Belum Bayar') {
          if (!updated.tanggal_tagihan) {
            updated.tanggal_tagihan = utcTime.split('T')[0];
          }
        }
        updatedOverrides[p.id_tagihan] = updated;
      });

      const updatedInvoices: InvoiceGrup[] = state.invoices.map(inv => {
        if (inv.nomor_invoice.toLowerCase() === invoiceNo.toLowerCase()) {
          return {
            ...inv,
            status_pembayaran: (value === 'Lunas' ? 'Lunas' : 'Belum Bayar') as 'Belum Bayar' | 'Lunas'
          };
        }
        return inv;
      });

      onStateChange({
        ...state,
        paymentOverrides: updatedOverrides,
        invoices: updatedInvoices
      });
      return;
    }

    if (field === 'jumlah_tagihan_override') {
      const val = value === null ? null : parseFloat(value);
      if (val === null) {
        matchedPeriods.forEach(p => {
          if (updatedOverrides[p.id_tagihan]) {
            updatedOverrides[p.id_tagihan].jumlah_tagihan_override = null;
            updatedOverrides[p.id_tagihan].selisih_pembayaran = 0;
            updatedOverrides[p.id_tagihan].ppn = Math.round(p.jumlah_tagihan * 0.11);
            updatedOverrides[p.id_tagihan].pph = Math.round(p.jumlah_tagihan * 0.02);
          }
        });
      } else {
        const totalEstimasi = matchedPeriods.reduce((sum, p) => sum + p.jumlah_tagihan, 0);
        let remainingVal = val;
        matchedPeriods.forEach((p, idx) => {
          const estimasi = p.jumlah_tagihan;
          let portion = 0;
          if (idx === matchedPeriods.length - 1) {
            portion = remainingVal;
          } else {
            portion = totalEstimasi > 0 ? Math.round((estimasi / totalEstimasi) * val) : Math.round(val / matchedPeriods.length);
            remainingVal -= portion;
          }
          const existing = updatedOverrides[p.id_tagihan] || {
            status_bayar: 'Belum Ditagih' as const,
            tanggal_tagihan: null,
            tanggal_bayar: null,
            nomor_invoice_grup: invoiceNo
          };
          updatedOverrides[p.id_tagihan] = {
            ...existing,
            jumlah_tagihan_override: portion,
            selisih_pembayaran: portion - estimasi,
            ppn: Math.round(portion * 0.11),
            pph: Math.round(portion * 0.02)
          };
        });
      }
      onStateChange({ ...state, paymentOverrides: updatedOverrides });
      return;
    }

    if (field === 'ppn') {
      const val = value === null ? null : parseFloat(value);
      if (val === null) {
        matchedPeriods.forEach(p => {
          if (updatedOverrides[p.id_tagihan]) {
            updatedOverrides[p.id_tagihan].ppn = null;
          }
        });
      } else {
        const totalTagihan = matchedPeriods.reduce((sum, p) => {
          const tagihan = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : p.jumlah_tagihan;
          return sum + tagihan;
        }, 0);
        
        let remainingPPN = val;
        matchedPeriods.forEach((p, idx) => {
          if (idx === matchedPeriods.length - 1) {
            const existing = updatedOverrides[p.id_tagihan] || {
              status_bayar: 'Belum Ditagih' as const,
              tanggal_tagihan: null,
              tanggal_bayar: null,
              nomor_invoice_grup: invoiceNo
            };
            updatedOverrides[p.id_tagihan] = { ...existing, ppn: remainingPPN };
          } else {
            const tagihan = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : p.jumlah_tagihan;
            const portion = totalTagihan > 0 ? Math.round((tagihan / totalTagihan) * val) : Math.round(val / matchedPeriods.length);
            const existing = updatedOverrides[p.id_tagihan] || {
              status_bayar: 'Belum Ditagih' as const,
              tanggal_tagihan: null,
              tanggal_bayar: null,
              nomor_invoice_grup: invoiceNo
            };
            updatedOverrides[p.id_tagihan] = { ...existing, ppn: portion };
            remainingPPN -= portion;
          }
        });
      }
      onStateChange({ ...state, paymentOverrides: updatedOverrides });
      return;
    }

    if (field === 'pph') {
      const val = value === null ? null : parseFloat(value);
      if (val === null) {
        matchedPeriods.forEach(p => {
          if (updatedOverrides[p.id_tagihan]) {
            updatedOverrides[p.id_tagihan].pph = null;
          }
        });
      } else {
        const totalTagihan = matchedPeriods.reduce((sum, p) => {
          const tagihan = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : p.jumlah_tagihan;
          return sum + tagihan;
        }, 0);
        
        let remainingPPh = val;
        matchedPeriods.forEach((p, idx) => {
          if (idx === matchedPeriods.length - 1) {
            const existing = updatedOverrides[p.id_tagihan] || {
              status_bayar: 'Belum Ditagih' as const,
              tanggal_tagihan: null,
              tanggal_bayar: null,
              nomor_invoice_grup: invoiceNo
            };
            updatedOverrides[p.id_tagihan] = { ...existing, pph: remainingPPh };
          } else {
            const tagihan = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : p.jumlah_tagihan;
            const portion = totalTagihan > 0 ? Math.round((tagihan / totalTagihan) * val) : Math.round(val / matchedPeriods.length);
            const existing = updatedOverrides[p.id_tagihan] || {
              status_bayar: 'Belum Ditagih' as const,
              tanggal_tagihan: null,
              tanggal_bayar: null,
              nomor_invoice_grup: invoiceNo
            };
            updatedOverrides[p.id_tagihan] = { ...existing, pph: portion };
            remainingPPh -= portion;
          }
        });
      }
      onStateChange({ ...state, paymentOverrides: updatedOverrides });
      return;
    }
  };

  // Complete grouped invoice checkout lunas state and mark sub-items
  const handleLunasiInvoiceGrup = (inv: InvoiceGrup) => {
    const updatedInvoices = state.invoices.map(i => {
      if (i.nomor_invoice === inv.nomor_invoice) {
        return { ...i, status_pembayaran: 'Lunas' as const };
      }
      return i;
    });

    const updatedOverrides = { ...state.paymentOverrides };
    inv.list_id_tagihan.forEach(id => {
      const existing = updatedOverrides[id] || {
        status_bayar: 'Belum Ditagih',
        tanggal_tagihan: null,
        tanggal_bayar: null,
        nomor_invoice_grup: null
      };
      
      updatedOverrides[id] = {
        ...existing,
        status_bayar: 'Lunas',
        tanggal_bayar: utcTime.split('T')[0],
        nomor_bayar: existing.nomor_bayar || 'EBK-' + Date.now().toString().slice(-6)
      };
    });

    onStateChange({
      ...state,
      invoices: updatedInvoices,
      paymentOverrides: updatedOverrides
    });

    triggerNoti('sukses', `Invoice ${inv.nomor_invoice} berhasil ditandai Lunas.`);
  };

  const handleDeleteInvoiceGrup = (nomorInvoice: string) => {
    const updatedInvoices = state.invoices.filter(i => i.nomor_invoice !== nomorInvoice);
    const updatedOverrides = { ...state.paymentOverrides };
    
    state.invoices.find(i => i.nomor_invoice === nomorInvoice)?.list_id_tagihan.forEach(id => {
      if (updatedOverrides[id]) {
        updatedOverrides[id].nomor_invoice_grup = null;
        updatedOverrides[id].status_bayar = 'Belum Ditagih';
      }
    });

    onStateChange({
      ...state,
      invoices: updatedInvoices,
      paymentOverrides: updatedOverrides
    });

    triggerNoti('sukses', `Invoice ${nomorInvoice} berhasil dihapus.`);
  };

  const handleUpdateInvoiceAdjustment = (nomorInvoice: string, biaya: number | null, keterangan: string) => {
    const exists = state.invoices.some(i => i.nomor_invoice === nomorInvoice);
    let updatedInvoices: InvoiceGrup[] = [];

    if (exists) {
      updatedInvoices = state.invoices.map(i => {
        if (i.nomor_invoice === nomorInvoice) {
          return {
            ...i,
            adjustment_biaya: biaya !== null ? biaya : undefined,
            adjustment_keterangan: keterangan || undefined
          };
        }
        return i;
      });
    } else {
      const matched = allPeriods.filter(p => p.nomor_invoice_grup === nomorInvoice);
      const customerId = matched.length > 0
        ? state.sewas.find(s => s.id_sewa === matched[0].id_sewa)?.id_customer || ''
        : '';
      
      const newInv: InvoiceGrup = {
        nomor_invoice: nomorInvoice,
        id_customer: customerId,
        tanggal_invoice: matched.length > 0 && matched[0].tanggal_tagihan ? matched[0].tanggal_tagihan : utcTime.split('T')[0],
        status_pembayaran: matched.every(p => p.status_bayar === 'Lunas') ? 'Lunas' : 'Belum Bayar',
        deskripsi: 'Virtual Grouping for Nota ' + nomorInvoice,
        list_id_tagihan: matched.map(p => p.id_tagihan),
        adjustment_biaya: biaya !== null ? biaya : undefined,
        adjustment_keterangan: keterangan || undefined
      };
      updatedInvoices = [...state.invoices, newInv];
    }

    onStateChange({
      ...state,
      invoices: updatedInvoices
    });

    triggerNoti('sukses', `Adjustment untuk Nota ${nomorInvoice} berhasil disimpan.`);
  };

  const handleSaveCollectiveChanges = (
    nomorInvoice: string,
    buktiBayar: string,
    tglBayar: string,
    biaya: number | null,
    keterangan: string,
    status: 'Belum Bayar' | 'Lunas'
  ) => {
    const matched = allPeriods.filter(p => p.nomor_invoice_grup === nomorInvoice);
    const updatedOverrides = { ...state.paymentOverrides };
    
    let parsedTglBayar: string | null = null;
    if (status === 'Lunas') {
      if (tglBayar.trim()) {
        const iso = parseInputDate(tglBayar.trim());
        if (!iso) {
          triggerNoti('error', `Format tanggal bayar "${tglBayar}" tidak valid. Harap gunakan format dd/mm/yyyy`);
          return;
        }
        parsedTglBayar = iso;
      } else {
        parsedTglBayar = utcTime.split('T')[0];
      }
    }

    matched.forEach(item => {
      const existing = updatedOverrides[item.id_tagihan] || {
        status_bayar: 'Belum Ditagih',
        tanggal_tagihan: null,
        tanggal_bayar: null,
        nomor_invoice_grup: null,
        nomor_bayar: null
      };

      updatedOverrides[item.id_tagihan] = {
        ...existing,
        status_bayar: status === 'Lunas' ? 'Lunas' : 'Pranota',
        tanggal_bayar: parsedTglBayar,
        nomor_bayar: buktiBayar.trim() || null,
        nomor_invoice_grup: nomorInvoice
      };
    });

    const exists = state.invoices.some(i => i.nomor_invoice === nomorInvoice);
    let updatedInvoices: InvoiceGrup[] = [];

    const customerId = matched.length > 0
      ? state.sewas.find(s => s.id_sewa === matched[0].id_sewa)?.id_customer || ''
      : '';

    if (exists) {
      updatedInvoices = state.invoices.map(i => {
        if (i.nomor_invoice === nomorInvoice) {
          return {
            ...i,
            status_pembayaran: status,
            adjustment_biaya: biaya !== null ? biaya : undefined,
            adjustment_keterangan: keterangan.trim() || undefined
          };
        }
        return i;
      });
    } else {
      const newInv: InvoiceGrup = {
        nomor_invoice: nomorInvoice,
        id_customer: customerId,
        tanggal_invoice: matched.length > 0 && matched[0].tanggal_tagihan ? matched[0].tanggal_tagihan : utcTime.split('T')[0],
        status_pembayaran: status,
        deskripsi: 'Virtual Grouping for Nota ' + nomorInvoice,
        list_id_tagihan: matched.map(p => p.id_tagihan),
        adjustment_biaya: biaya !== null ? biaya : undefined,
        adjustment_keterangan: keterangan.trim() || undefined
      };
      updatedInvoices = [...state.invoices, newInv];
    }

    onStateChange({
      ...state,
      paymentOverrides: updatedOverrides,
      invoices: updatedInvoices
    });

    setRowDrafts(prev => ({
      ...prev,
      [nomorInvoice]: {
        buktiBayar: buktiBayar.trim(),
        tglBayar: parsedTglBayar ? formatEntryDate(parsedTglBayar) : '',
        adjustmentBiaya: biaya !== null ? String(biaya) : '',
        adjustmentKeterangan: keterangan.trim(),
        statusPembayaran: status
      }
    }));

    triggerNoti('sukses', `Sukses menyimpan update untuk Nota ${nomorInvoice}.`);
  };

  const handleProcessImportPayment = () => {
    if (!importPaymentText.trim()) {
      triggerNoti('error', 'Silakan masukkan teks data pembayaran terlebih dahulu.');
      return;
    }

    const lines = importPaymentText.split('\n');
    const parsedRows: ParsedPaymentRow[] = [];

    lines.forEach((line, index) => {
      const lineNum = index + 1;
      const trimmed = line.trim();
      if (!trimmed || trimmed.startsWith('#')) return; // Skip empty and comment lines

      // Detect separator: check for tab, then semicolon, then comma
      let separator = ';';
      if (trimmed.includes('\t')) {
        separator = '\t';
      } else if (trimmed.includes(';')) {
        separator = ';';
      } else if (trimmed.includes(',')) {
        separator = ',';
      }

      const parts = trimmed.split(separator);
      if (parts.length < 3) {
        parsedRows.push({
          lineNum,
          raw: trimmed,
          nomorBayar: '',
          tanggalBayar: '',
          parsedTglIso: null,
          nomorNota: '',
          isValidNota: false,
          customerId: '',
          customerName: '',
          grandTotal: 0,
          textStatus: `❌ Gagal: Format kolom tidak lengkap (harus minimal 3 kolom: no bayar${separator}tgl bayar${separator}no nota)`
        });
        return;
      }

      const nomorBayar = parts[0].trim();
      const tanggalBayarRaw = parts[1].trim();
      const nomorNota = parts[2].trim();

      if (!nomorBayar) {
        parsedRows.push({
          lineNum,
          raw: trimmed,
          nomorBayar: '',
          tanggalBayar: tanggalBayarRaw,
          parsedTglIso: null,
          nomorNota,
          isValidNota: false,
          customerId: '',
          customerName: '',
          grandTotal: 0,
          textStatus: '❌ Gagal: Nomor Bukti Bayar kosong'
        });
        return;
      }

      if (!nomorNota) {
        parsedRows.push({
          lineNum,
          raw: trimmed,
          nomorBayar,
          tanggalBayar: tanggalBayarRaw,
          parsedTglIso: null,
          nomorNota: '',
          isValidNota: false,
          customerId: '',
          customerName: '',
          grandTotal: 0,
          textStatus: '❌ Gagal: Nomor Nota kosong'
        });
        return;
      }

      // Check if nomorNota exists in compiling periods or database
      const invoiceObj = state.invoices.find(i => i.nomor_invoice.toLowerCase() === nomorNota.toLowerCase());
      const matchedPeriods = allPeriods.filter(p => {
        const matchesGrupNo = p.nomor_invoice_grup && p.nomor_invoice_grup.toLowerCase() === nomorNota.toLowerCase();
        const matchesInList = invoiceObj && invoiceObj.list_id_tagihan.includes(p.id_tagihan);
        return matchesGrupNo || matchesInList;
      });

      const matchedNotaNo = matchedPeriods.length > 0 ? (matchedPeriods[0].nomor_invoice_grup || nomorNota) : (invoiceObj?.nomor_invoice || nomorNota);
      const isValidNota = matchedPeriods.length > 0;

      // Parse date
      let parsedTglIso: string | null = null;
      let textStatus = '';
      if (tanggalBayarRaw) {
        const iso = parseInputDate(tanggalBayarRaw);
        if (iso) {
          parsedTglIso = iso;
        } else {
          textStatus = `⚠️ Tgl bayar "${tanggalBayarRaw}" tidak valid (gunakan dd/mm/yyyy atau yyyy-mm-dd). `;
        }
      } else {
        parsedTglIso = utcTime.split('T')[0];
        textStatus = `⚠️ Tgl bayar kosong (otomatis tanggal hari ini). `;
      }

      if (!isValidNota) {
        textStatus += `❌ Nomor Nota "${nomorNota}" tidak ditemukan dalam database tagihan aktif. Silakan cek apakah ada typo atau no nota tsb belum dibuat grupnya.`;
        parsedRows.push({
          lineNum,
          raw: trimmed,
          nomorBayar,
          tanggalBayar: tanggalBayarRaw,
          parsedTglIso,
          nomorNota: matchedNotaNo,
          isValidNota: false,
          customerId: '',
          customerName: '',
          grandTotal: 0,
          textStatus
        });
        return;
      }

      // Matched and valid!
      const customerId = state.sewas.find(s => s.id_sewa === matchedPeriods[0].id_sewa)?.id_customer || '';
      const customerName = getCustomerName(customerId);
      
      const totalRekBayar = matchedPeriods.reduce((sum, p) => {
        const tagihan = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : p.jumlah_tagihan;
        return sum + Math.min(tagihan, p.jumlah_tagihan);
      }, 0);
      const totalPPN = matchedPeriods.reduce((sum, p) => {
        const tagihan = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : p.jumlah_tagihan;
        const ppn = p.ppn !== null && p.ppn !== undefined ? p.ppn : Math.round(tagihan * 0.11);
        return sum + ppn;
      }, 0);
      const totalPPh = matchedPeriods.reduce((sum, p) => {
        const tagihan = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : p.jumlah_tagihan;
        const pph = p.pph !== null && p.pph !== undefined ? p.pph : Math.round(tagihan * 0.02);
        return sum + pph;
      }, 0);

      const groupInvoice = state.invoices.find(i => i.nomor_invoice.toLowerCase() === matchedNotaNo.toLowerCase());
      const adjustmentBiaya = groupInvoice?.adjustment_biaya ?? 0;
      const grandTotal = totalRekBayar + totalPPN - totalPPh + adjustmentBiaya;

      textStatus = textStatus ? textStatus + '✓ Siap Impor' : '✓ Siap Impor';

      parsedRows.push({
        lineNum,
        raw: trimmed,
        nomorBayar,
        tanggalBayar: parsedTglIso ? formatEntryDate(parsedTglIso) : tanggalBayarRaw,
        parsedTglIso,
        nomorNota: matchedNotaNo,
        isValidNota: true,
        customerId,
        customerName,
        grandTotal,
        textStatus
      });
    });

    setImportPaymentPreview(parsedRows);
    setIsImportProcessed(true);
    triggerNoti('sukses', `Berhasil memproses ${parsedRows.length} baris teks. Silakan periksa pratinjau di bawah.`);
  };

  const handleApplyImportPayment = () => {
    const validRows = importPaymentPreview.filter(r => r.isValidNota);
    if (validRows.length === 0) {
      triggerNoti('error', 'Tidak ada data nota yang valid untuk diimpor pembayarannya.');
      return;
    }

    const updatedOverrides = { ...state.paymentOverrides };
    let updatedInvoices = [...state.invoices];
    const newDrafts = { ...rowDrafts };

    validRows.forEach(row => {
      const invoiceObj = updatedInvoices.find(i => i.nomor_invoice.toLowerCase() === row.nomorNota.toLowerCase());
      const matchedPeriods = allPeriods.filter(p => {
        const matchesGrupNo = p.nomor_invoice_grup && p.nomor_invoice_grup.toLowerCase() === row.nomorNota.toLowerCase();
        const matchesInList = invoiceObj && invoiceObj.list_id_tagihan.includes(p.id_tagihan);
        return matchesGrupNo || matchesInList;
      });
      const paymentDate = row.parsedTglIso || utcTime.split('T')[0];

      matchedPeriods.forEach(item => {
        const existing = updatedOverrides[item.id_tagihan] || {
          status_bayar: 'Belum Ditagih',
          tanggal_tagihan: null,
          tanggal_bayar: null,
          nomor_invoice_grup: null,
          nomor_bayar: null
        };

        updatedOverrides[item.id_tagihan] = {
          ...existing,
          status_bayar: 'Lunas',
          tanggal_bayar: paymentDate,
          nomor_bayar: row.nomorBayar,
          nomor_invoice_grup: row.nomorNota
        };
      });

      // Update Group Invoices status in state
      const invoiceIdx = updatedInvoices.findIndex(i => i.nomor_invoice.toLowerCase() === row.nomorNota.toLowerCase());
      if (invoiceIdx !== -1) {
        updatedInvoices[invoiceIdx] = {
          ...updatedInvoices[invoiceIdx],
          status_pembayaran: 'Lunas'
        };
      } else {
        updatedInvoices.push({
          nomor_invoice: row.nomorNota,
          id_customer: row.customerId,
          tanggal_invoice: matchedPeriods.length > 0 && matchedPeriods[0].tanggal_tagihan ? matchedPeriods[0].tanggal_tagihan : utcTime.split('T')[0],
          status_pembayaran: 'Lunas',
          deskripsi: 'Virtual Grouping for Nota ' + row.nomorNota,
          list_id_tagihan: matchedPeriods.map(p => p.id_tagihan),
          adjustment_biaya: 0,
          adjustment_keterangan: ''
        });
      }

      // Sync local row drafts so UI displays paid fields right away
      newDrafts[row.nomorNota] = {
        buktiBayar: row.nomorBayar,
        tglBayar: formatEntryDate(paymentDate),
        statusPembayaran: 'Lunas',
        adjustmentBiaya: newDrafts[row.nomorNota]?.adjustmentBiaya || '',
        adjustmentKeterangan: newDrafts[row.nomorNota]?.adjustmentKeterangan || ''
      };
    });

    onStateChange({
      ...state,
      paymentOverrides: updatedOverrides,
      invoices: updatedInvoices
    });

    setRowDrafts(newDrafts);
    setImportPaymentText('');
    setImportPaymentPreview([]);
    setIsImportProcessed(false);
    setImportPaymentOpen(false);

    triggerNoti('sukses', `Berhasil mengimpor pelunasan & nomor bukti bayar untuk ${validRows.length} nota sekaligus!`);
  };

  const getInvoiceSumAmount = (inv: InvoiceGrup) => {
    return inv.list_id_tagihan.reduce((acc, id) => {
      const period = allPeriods.find(p => p.id_tagihan === id);
      if (!period) return acc;
      const tagihan = period.jumlah_tagihan_override !== null && period.jumlah_tagihan_override !== undefined
        ? period.jumlah_tagihan_override
        : period.jumlah_tagihan;
      return acc + tagihan;
    }, 0);
  };

  // Print PDF helper for active modal
  const handleTriggerPrint = () => {
    window.print();
  };

  return (
    <div className="space-y-6" id="invoice-manager-root">
      
      {/* Dynamic Alerts */}
      {noti && (
        <div
          id="invoice-toast"
          className="fixed top-4 right-4 z-50 p-3.5 rounded-xl flex items-center gap-2 border bg-emerald-600 text-white shadow-xl animate-fade-in"
        >
          <CheckCircle2 className="w-5 h-5 shrink-0 text-emerald-200" />
          <span className="font-semibold text-xs">{noti.msg}</span>
        </div>
      )}

      {/* Header and Overview */}
      <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-100 pb-4">
        <div>
          <h2 className="text-base font-bold text-slate-800 flex items-center gap-2">
            <FileSpreadsheet className="w-5 h-5 text-emerald-600" />
            <span>Kertas Kerja Pranota &amp; Tabel Pembayaran Pajak (Indo-Format)</span>
          </h2>
          <p className="text-xs text-slate-500 mt-0.5">
            Kelola draft Tagihan ("Pranota"), edit nominal, hitung selisih otomatis, serta kelola PPN 11% &amp; PPh 23 (2%) selaras dengan <strong>Waktu WIB</strong>.
          </p>
        </div>

        {/* View Switch Tab */}
        <div className="flex bg-slate-100 p-1 rounded-xl self-start lg:self-center text-xs font-semibold">
          <button
            onClick={() => setActiveViewTab('sheet')}
            className={`px-4 py-1.5 rounded-lg transition-all cursor-pointer ${
              activeViewTab === 'sheet' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-600 hover:text-slate-900'
            }`}
          >
            1. Rekonsiliasi Tagihan Vendor
          </button>
          <button
            onClick={() => setActiveViewTab('group')}
            className={`px-4 py-1.5 rounded-lg transition-all cursor-pointer flex items-center gap-1.5 ${
              activeViewTab === 'group' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-600 hover:text-slate-900'
            }`}
          >
            2. Penerbitan Pranota / Proforma
            <span className={`px-1.5 py-0.5 text-[9px] font-mono rounded-full font-extrabold ${isSewaIn ? 'bg-indigo-100 text-indigo-800' : 'bg-emerald-100 text-emerald-800'}`}>{distinctInvoiceNumbers.length}</span>
          </button>
          <button
            onClick={() => setActiveViewTab('collective')}
            className={`px-4 py-1.5 rounded-lg transition-all cursor-pointer flex items-center gap-1.5 ${
              activeViewTab === 'collective' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-600 hover:text-slate-900'
            }`}
          >
            <span>3. Pembayaran &amp; Pelunasan Pajak</span>
            <span className={`px-1.5 py-0.5 text-[9px] font-mono rounded-full font-extrabold ${isSewaIn ? 'bg-indigo-100 text-indigo-800' : 'bg-emerald-100 text-emerald-800'}`}>DASHBOARD</span>
          </button>
          <button
            onClick={() => setActiveViewTab('report')}
            className={`px-4 py-1.5 rounded-lg transition-all cursor-pointer flex items-center gap-1.5 ${
              activeViewTab === 'report' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-600 hover:text-slate-900'
            }`}
          >
            <span>{isSewaIn ? '4. Laporan Eksekutif & Analisis Sewa In' : '4. Laporan Eksekutif & Analisis Sewa Out'}</span>
            <span className={`px-1.5 py-0.5 text-[9px] font-mono rounded-full font-extrabold ${isSewaIn ? 'bg-indigo-100 text-indigo-800' : 'bg-emerald-100 text-emerald-800'}`}>NEW</span>
          </button>
        </div>
      </div>

      {activeViewTab === 'sheet' && (() => {
        // Filter the active containers inside the selected vendor tagihan group
        const selectedGroupPeriods = allPeriods.filter(p => {
          const sObj = state.sewas.find(s => s.id_sewa === p.id_sewa);
          return (
            p.nomor_invoice_grup === selectedVendorTagihanNo &&
            selectedVendorTagihanNo.trim() !== '' &&
            (!selectedVendorCustomer || sObj?.id_customer === selectedVendorCustomer)
          );
        });

        // Filter the loose draft containers (no tagihan group assigned yet) for the selected vendor
        const looseDraftPeriods = allPeriods.filter(p => {
          const sObj = state.sewas.find(s => s.id_sewa === p.id_sewa);
          const isMatchCustomer = !selectedVendorCustomer || sObj?.id_customer === selectedVendorCustomer;
          const isDraft = p.status_bayar === 'Belum Ditagih';
          const hasNoGroup = !p.nomor_invoice_grup || p.nomor_invoice_grup.trim() === '';
          
          if (!isMatchCustomer || !isDraft || !hasNoGroup) return false;
          
          if (searchDraftContainer.trim()) {
            return sObj?.no_kontainer?.toLowerCase().includes(searchDraftContainer.toLowerCase()) || false;
          }
          return true;
        });

        // Calculate totals for selected group
        const totalEstimasiGroup = selectedGroupPeriods.reduce((sum, p) => sum + p.jumlah_tagihan, 0);
        const totalAktualGroup = selectedGroupPeriods.reduce((sum, p) => {
          const amt = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : p.jumlah_tagihan;
          return sum + amt;
        }, 0);
        const totalPPNGroup = selectedGroupPeriods.reduce((sum, p) => {
          const amt = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : p.jumlah_tagihan;
          const ppn = p.ppn !== null && p.ppn !== undefined ? p.ppn : Math.round(amt * 0.11);
          return sum + ppn;
        }, 0);
        const totalPPhGroup = selectedGroupPeriods.reduce((sum, p) => {
          const amt = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : p.jumlah_tagihan;
          const pph = p.pph !== null && p.pph !== undefined ? p.pph : Math.round(amt * 0.02);
          return sum + pph;
        }, 0);
        const grandNetTotalGroup = totalAktualGroup + totalPPNGroup - totalPPhGroup;

        return (
          <div className="space-y-6" id="spreadsheet-workspace">
            {/* SUB-TABS SELECTOR FOR MANUAL ENTRY MODE */}
            <div className="bg-slate-100/80 p-1.5 rounded-xl border border-slate-200/60 flex flex-wrap gap-1">
              <button
                type="button"
                onClick={() => setSheetSubMode('search')}
                className={`px-4 py-2 text-xs font-bold uppercase tracking-wider flex items-center gap-2 rounded-lg transition-all cursor-pointer ${
                  sheetSubMode === 'search'
                    ? 'bg-indigo-600 text-white shadow-sm'
                    : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50'
                }`}
              >
                <Search className="w-4 h-4" />
                Cari &amp; Kelola Draft Tagihan Vendor (Outstanding)
              </button>
              <button
                type="button"
                onClick={() => {
                  setSheetSubMode('create');
                }}
                className={`px-4 py-2 text-xs font-bold uppercase tracking-wider flex items-center gap-2 rounded-lg transition-all cursor-pointer ${
                  sheetSubMode === 'create'
                    ? 'bg-indigo-600 text-white shadow-sm'
                    : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50'
                }`}
              >
                <PlusCircle className="w-4 h-4" />
                Buat Draft Tagihan Vendor Baru (Manual Entry)
              </button>
            </div>

            {/* VENDOR & GROUP SELECTION CARD (DYNAMIC BASED ON SUB-MODE) */}
            {sheetSubMode === 'search' ? (
              <div className="bg-white p-5 rounded-2xl border border-slate-150 shadow-sm space-y-4">
                <div className="flex items-center gap-3 border-b border-slate-100 pb-3.5">
                  <div className="w-10 h-10 rounded-full bg-indigo-50 border border-indigo-150 flex items-center justify-center shrink-0">
                    <Search className="w-5 h-5 text-indigo-600" />
                  </div>
                  <div>
                    <h3 className="text-sm font-extrabold text-slate-800 uppercase tracking-wider">CARI &amp; KELOLA DRAFT TAGIHAN</h3>
                    <p className="text-[10px] text-slate-400 mt-0.5">Pilih Vendor dan cari No. Tagihan Vendor yang sudah ada/di-impor untuk menambah atau melepas kontainer.</p>
                  </div>
                </div>
                
                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div>
                    <label className="block text-xs font-semibold text-slate-500 mb-1">Vendor</label>
                    <select
                      value={selectedVendorCustomer}
                      onChange={(e) => {
                        setSelectedVendorCustomer(e.target.value);
                        setSelectedVendorTagihanNo(''); // reset group when customer changes
                      }}
                      className="w-full text-xs font-medium border border-slate-200 rounded-xl px-3 py-2 bg-slate-50 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                      <option value="">-- Semua Vendor --</option>
                      {state.customers.map(c => (
                        <option key={c.id_customer} value={c.id_customer}>{c.nama_customer}</option>
                      ))}
                    </select>
                  </div>

                  <div>
                    <label className="block text-xs font-semibold text-slate-500 mb-1">Cari No. Tagihan Vendor</label>
                    <SearchableCombobox
                      id="draft-tagihan-vendor-combobox"
                      value={selectedVendorTagihanNo}
                      onChange={(val) => {
                        setSelectedVendorTagihanNo(val);
                        // Auto detect vendor for this tagihan group if possible
                        if (val) {
                          const matchedPeriod = allPeriods.find(p => p.nomor_invoice_grup === val);
                          if (matchedPeriod) {
                            const sObj = state.sewas.find(s => s.id_sewa === matchedPeriod.id_sewa);
                            if (sObj?.id_customer) {
                              setSelectedVendorCustomer(sObj.id_customer);
                            }
                          }
                        }
                      }}
                      options={existingDraftVendorTagihans}
                      placeholder="Ketik atau pilih No. Tagihan..."
                      inputClassName="bg-slate-50 text-slate-800 focus:outline-none"
                    />
                  </div>

                  <div>
                    <label className="block text-xs font-semibold text-slate-500 mb-1">Tgl. Tagihan Vendor</label>
                    <FormDateInput
                      value={selectedVendorTagihanDate}
                      onChange={(val) => {
                        setSelectedVendorTagihanDate(val);
                        // Bulk update the selectedGroupPeriods' dates in the state automatically!
                        if (selectedVendorTagihanNo) {
                          selectedGroupPeriods.forEach(gp => {
                            handleUpdateFieldValue(gp.id_tagihan, 'tanggal_tagihan', val);
                          });
                        }
                      }}
                      className="w-full text-xs font-mono border border-slate-200 rounded-xl px-3 py-2 bg-slate-50 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    />
                  </div>
                </div>
              </div>
            ) : (
              <div className="bg-white p-5 rounded-2xl border border-slate-150 shadow-sm space-y-4">
                <div className="flex items-center gap-3 border-b border-slate-100 pb-3.5">
                  <div className="w-10 h-10 rounded-full bg-indigo-50 border border-indigo-150 flex items-center justify-center shrink-0">
                    <Plus className="w-5 h-5 text-indigo-600" />
                  </div>
                  <div>
                    <h3 className="text-sm font-extrabold text-slate-800 uppercase tracking-wider">BUAT DRAFT TAGIHAN VENDOR BARU (MANUAL)</h3>
                    <p className="text-[10px] text-slate-400 mt-0.5">Masukkan Nomor Tagihan baru secara manual. Kolom ini bebas hambatan (tanpa loading / autocomplete) sehingga sangat cepat!</p>
                  </div>
                </div>
                
                <div className="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                  <div>
                    <label className="block text-xs font-semibold text-slate-500 mb-1">Vendor <span className="text-rose-500">*</span></label>
                    <select
                      value={newVendorId}
                      onChange={(e) => setNewVendorId(e.target.value)}
                      className="w-full text-xs font-medium border border-slate-200 rounded-xl px-3 py-2 bg-slate-50 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                      <option value="">-- Pilih Vendor --</option>
                      {state.customers.map(c => (
                        <option key={c.id_customer} value={c.id_customer}>{c.nama_customer}</option>
                      ))}
                    </select>
                  </div>

                  <div>
                    <label className="block text-xs font-semibold text-slate-500 mb-1">No. Tagihan Baru <span className="text-rose-500">*</span></label>
                    <input
                      type="text"
                      value={newInvoiceNo}
                      onChange={(e) => setNewInvoiceNo(e.target.value)}
                      placeholder="Contoh: INV-TM-2026-001"
                      className="w-full text-xs font-mono font-semibold border border-slate-200 rounded-xl px-3 py-2 bg-slate-50 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    />
                  </div>

                  <div>
                    <label className="block text-xs font-semibold text-slate-500 mb-1">Tgl. Tagihan</label>
                    <FormDateInput
                      value={newInvoiceDate}
                      onChange={(val) => setNewInvoiceDate(val)}
                      className="w-full text-xs font-mono border border-slate-200 rounded-xl px-3 py-2 bg-slate-50 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    />
                  </div>

                  <div>
                    <button
                      type="button"
                      onClick={() => {
                        if (!newVendorId) {
                          triggerNoti('error', 'Silakan pilih Vendor terlebih dahulu.');
                          return;
                        }
                        if (!newInvoiceNo.trim()) {
                          triggerNoti('error', 'Silakan isi No. Tagihan baru.');
                          return;
                        }
                        
                        const tagihanClean = newInvoiceNo.trim();
                        const tagihanDateClean = newInvoiceDate || utcTime.split('T')[0];

                        // Set active workspace to this new tagihan group
                        setSelectedVendorCustomer(newVendorId);
                        setSelectedVendorTagihanNo(tagihanClean);
                        setSelectedVendorTagihanDate(tagihanDateClean);

                        // Reset fields
                        setNewInvoiceNo('');
                        
                        // Keep on create/entry submode as requested by the user
                        // setSheetSubMode('search');
                        triggerNoti('sukses', `Draft Tagihan "${tagihanClean}" berhasil dibuat! Silakan pilih kontainer di sebelah kanan untuk ditambahkan ke tagihan.`);
                      }}
                      className="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm flex items-center justify-center gap-1.5 cursor-pointer h-[38px]"
                    >
                      <Plus className="w-4 h-4" />
                      Buat &amp; Mulai Susun
                    </button>
                  </div>
                </div>
              </div>
            )}

            {/* TWO PANELS WORKSPACE */}
            <div className="grid grid-cols-1 lg:grid-cols-12 gap-6">
              {/* LEFT PANEL: CONTIANERS IN SELECTED GROUP */}
              <div className="lg:col-span-7 bg-white p-5 rounded-2xl border border-slate-100 shadow-sm space-y-4">
                <div className="flex items-center justify-between border-b border-slate-100 pb-3">
                  <div className="flex items-center gap-2">
                    <CheckCircle2 className="w-5 h-5 text-emerald-600 shrink-0" />
                    <div>
                      <h4 className="text-xs font-extrabold text-slate-800 uppercase tracking-wider">
                        Detail Kontainer dalam Tagihan Ini
                      </h4>
                      <p className="text-[10px] text-slate-400 mt-0.5">
                        Kontainer yang dikelompokkan ke dalam No. Tagihan <span className="font-mono font-bold text-slate-700">{selectedVendorTagihanNo || '-'}</span>
                      </p>
                    </div>
                  </div>
                  <div className="flex items-center gap-2">
                    <span className="text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-1 rounded-md">
                      {selectedGroupPeriods.length} Kontainer
                    </span>
                    {selectedGroupPeriods.length > 0 && (
                      <button
                        type="button"
                        onClick={() => {
                          if (confirm('Apakah Anda yakin ingin melepas semua kontainer dari tagihan ini sekaligus?')) {
                            selectedGroupPeriods.forEach(p => {
                              handleUpdateFieldValue(p.id_tagihan, 'nomor_invoice_grup', null);
                            });
                            triggerNoti('sukses', 'Semua kontainer berhasil dilepas dari tagihan.');
                          }
                        }}
                        className="text-[10px] font-bold text-rose-600 hover:text-white hover:bg-rose-600 border border-rose-200 px-2 py-1 rounded-md transition-all cursor-pointer bg-white"
                        title="Lepas semua kontainer dari tagihan ini sekaligus"
                      >
                        ✕ Lepas Semua
                      </button>
                    )}
                  </div>
                </div>

                {!selectedVendorTagihanNo ? (
                  <div className="space-y-6">
                    <div className="py-8 px-4 text-center bg-slate-50 border border-dashed border-slate-200 rounded-2xl">
                      <Sparkles className="w-6 h-6 text-indigo-500 mx-auto mb-2" />
                      <p className="text-xs font-bold text-slate-700">Silakan masukkan No. Tagihan Vendor di atas untuk mulai menyusun.</p>
                      <p className="text-[10px] text-slate-400 mt-1">Anda dapat memilih draf yang ada di daftar cepat di bawah ini atau mengetik No. Tagihan baru di kolom input.</p>
                    </div>

                    <div className="border border-slate-150 rounded-2xl p-4 bg-white shadow-3xs space-y-3">
                      <div className="flex items-center gap-2 border-b border-slate-100 pb-2.5">
                        <FileText className="w-4 h-4 text-indigo-600" />
                        <h4 className="text-xs font-extrabold text-slate-800 uppercase tracking-wider">
                          Daftar Draft Tagihan Aktif / Outstanding Terbaru (Maks. 20)
                        </h4>
                      </div>

                      {outstandingDraftGroups.length === 0 ? (
                        <div className="text-center py-6 text-xs text-slate-400 italic">
                          Tidak ada draf tagihan aktif / outstanding saat ini. Silakan buat baru!
                        </div>
                      ) : (
                        <div className="overflow-x-auto border border-slate-100 rounded-xl">
                          <table className="w-full text-left border-collapse text-[10px] font-sans">
                            <thead>
                              <tr className="bg-slate-50 text-slate-500 font-bold border-b border-slate-200">
                                <th className="p-2 py-2.5 font-mono">No. Tagihan Vendor</th>
                                <th className="p-2 py-2.5">Nama Vendor/Mitra</th>
                                <th className="p-2 py-2.5 text-center">Jumlah Kontainer</th>
                                <th className="p-2 py-2.5 text-right">Estimasi Tagihan</th>
                                <th className="p-2 py-2.5 text-center">Status</th>
                                <th className="p-2 py-2.5 text-center">Aksi</th>
                              </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100">
                              {outstandingDraftGroups.map((g) => (
                                <tr key={g.nomor_invoice_grup} className="hover:bg-slate-50/60 transition-colors">
                                  <td className="p-2 py-2.5 font-mono font-bold text-slate-800">{g.nomor_invoice_grup}</td>
                                  <td className="p-2 py-2.5 font-semibold text-slate-600">{g.customerName}</td>
                                  <td className="p-2 py-2.5 text-center font-bold text-indigo-700 bg-indigo-50/20">{g.count} Kontainer</td>
                                  <td className="p-2 py-2.5 text-right font-mono font-bold text-slate-800">{formatRupiah(g.totalEstimasi)}</td>
                                  <td className="p-2 py-2.5 text-center">
                                    <span className={`inline-block px-1.5 py-0.5 rounded-[4px] text-[8px] font-extrabold tracking-wide uppercase ${
                                      g.status_bayar === 'Belum Ditagih'
                                        ? 'bg-slate-100 text-slate-700 border border-slate-200/60'
                                        : g.status_bayar === 'Pranota'
                                        ? 'bg-amber-50 text-amber-700 border border-amber-200/60'
                                        : 'bg-sky-50 text-sky-700 border border-sky-200/60'
                                    }`}>
                                      {g.status_bayar === 'Belum Ditagih' ? 'DRAF (Belum Ditagih)' : g.status_bayar === 'Pranota' ? 'PRANOTA (Draft Invoice)' : 'SUDAH DITAGIH'}
                                    </span>
                                  </td>
                                  <td className="p-2 py-2.5 text-center">
                                    <button
                                      type="button"
                                      onClick={() => {
                                        if (g.id_customer) {
                                          setSelectedVendorCustomer(g.id_customer);
                                        }
                                        setSelectedVendorTagihanNo(g.nomor_invoice_grup);
                                        triggerNoti('sukses', `Draft ${g.nomor_invoice_grup} berhasil dimuat.`);
                                      }}
                                      className="px-2 py-1 bg-indigo-50 border border-indigo-200 rounded text-[9px] text-indigo-600 font-extrabold hover:bg-indigo-600 hover:text-white transition-all cursor-pointer shadow-3xs"
                                    >
                                      Buka &amp; Kelola ➜
                                    </button>
                                  </td>
                                </tr>
                              ))}
                            </tbody>
                          </table>
                        </div>
                      )}
                    </div>
                  </div>
                ) : selectedGroupPeriods.length === 0 ? (
                  <div className="py-12 text-center bg-slate-50 border border-dashed border-slate-200 rounded-2xl">
                    <p className="text-xs font-semibold text-slate-500">Tagihan ini masih kosong.</p>
                    <p className="text-[10px] text-slate-400 mt-1">Gunakan panel kanan untuk menambahkan draf kontainer ke tagihan ini.</p>
                  </div>
                ) : (
                  <div className="space-y-4">
                    <div className="overflow-x-auto border border-slate-150 rounded-xl">
                      <table className="w-full text-left border-collapse text-[10px] font-sans">
                        <thead>
                          <tr className="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold font-mono">
                            <th className="p-2 py-2.5">No. Kontainer</th>
                            <th className="p-2 py-2.5">Bulan / Periode</th>
                            <th className="p-2 py-2.5 text-right">Estimasi</th>
                            <th className="p-2 py-2.5 text-right bg-amber-50">Nominal Riil (Edit)</th>
                            <th className="p-2 py-2.5 text-right">PPN (11%)</th>
                            <th className="p-2 py-2.5 text-right">PPh (2%)</th>
                            <th className="p-2 py-2.5 text-center">Selisih</th>
                            <th className="p-2 py-2.5">Keterangan / Alasan Selisih</th>
                            <th className="p-2 py-2.5 text-center">Aksi</th>
                          </tr>
                        </thead>
                        <tbody className="divide-y divide-slate-150">
                          {selectedGroupPeriods.map((p, idx) => {
                            const isSewaObj = state.sewas.find(s => s.id_sewa === p.id_sewa);
                            const nominalEstimasi = p.jumlah_tagihan;
                            const nominalAktual = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : nominalEstimasi;
                            const ppn = p.ppn !== null && p.ppn !== undefined ? p.ppn : Math.round(nominalAktual * 0.11);
                            const pph = p.pph !== null && p.pph !== undefined ? p.pph : Math.round(nominalAktual * 0.02);
                            const selisih = nominalAktual - nominalEstimasi;

                            return (
                              <tr key={p.id_tagihan} className="hover:bg-slate-50/50">
                                <td className="p-2 font-mono font-bold text-slate-800">
                                  <div>{isSewaObj?.no_kontainer || 'Manual'}</div>
                                  {p.status_bayar !== 'Belum Ditagih' && (
                                    <span className={`inline-block text-[8px] font-bold px-1 rounded mt-0.5 border ${
                                      p.status_bayar === 'Lunas'
                                        ? 'bg-emerald-50 border-emerald-200 text-emerald-700'
                                        : p.status_bayar === 'Belum Bayar'
                                        ? 'bg-sky-50 border-sky-200 text-sky-700'
                                        : 'bg-amber-50 border-amber-200 text-amber-700'
                                    }`}>
                                      {p.status_bayar === 'Lunas' ? 'Lunas' : p.status_bayar === 'Belum Bayar' ? 'Sudah Ditagih' : 'Tagihan'}
                                    </span>
                                  )}
                                </td>
                                <td className="p-2 text-slate-500">
                                  Bulan {p.bulan_ke} <span className="text-[8px] font-mono block">({formatIndoDate(p.tanggal_awal)} - {formatIndoDate(p.tanggal_akhir)})</span>
                                </td>
                                <td className="p-2 text-right font-mono text-slate-600">{formatRupiah(nominalEstimasi)}</td>
                                <td className="p-1 text-right bg-amber-50/30">
                                  <FastNumberInput
                                    value={p.jumlah_tagihan_override}
                                    placeholder={String(nominalEstimasi)}
                                    onChange={(val) => {
                                      handleUpdateFieldValue(p.id_tagihan, 'jumlah_tagihan_override', val);
                                    }}
                                    className="w-20 text-[10px] font-mono border border-slate-200 rounded px-1.5 py-1 text-right bg-white focus:ring-1 focus:ring-indigo-500"
                                  />
                                </td>
                                <td className="p-1 text-right">
                                  <FastNumberInput
                                    value={ppn}
                                    onChange={(val) => {
                                      handleUpdateFieldValue(p.id_tagihan, 'ppn', val);
                                    }}
                                    className="w-16 text-[10px] font-mono border border-slate-200 rounded px-1.5 py-1 text-right bg-white focus:ring-1 focus:ring-indigo-500"
                                  />
                                </td>
                                <td className="p-1 text-right">
                                  <FastNumberInput
                                    value={pph}
                                    onChange={(val) => {
                                      handleUpdateFieldValue(p.id_tagihan, 'pph', val);
                                    }}
                                    className="w-14 text-[10px] font-mono border border-slate-200 rounded px-1.5 py-1 text-right bg-white focus:ring-1 focus:ring-indigo-500"
                                  />
                                </td>
                                <td className="p-2 text-center font-mono">
                                  {selisih === 0 ? (
                                    <span className="text-slate-400">-</span>
                                  ) : selisih > 0 ? (
                                    <span className="text-emerald-600 font-bold">+{formatRupiah(selisih)}</span>
                                  ) : (
                                    <span className="text-rose-600 font-bold">{formatRupiah(selisih)}</span>
                                  )}
                                </td>
                                <td className="p-1 min-w-[150px]">
                                  <FastTextInput
                                    value={p.keterangan_selisih}
                                    placeholder={selisih !== 0 ? "Wajib diisi (alasan selisih)..." : "Keterangan tambahan..."}
                                    onChange={(val) => handleUpdateFieldValue(p.id_tagihan, 'keterangan_selisih', val)}
                                    className={`w-full text-[10px] border rounded px-1.5 py-1 text-left ${
                                      selisih !== 0 && (!p.keterangan_selisih || p.keterangan_selisih.trim() === '')
                                        ? 'border-rose-300 bg-rose-50/50 placeholder-rose-400 focus:ring-rose-500 text-rose-900 font-medium'
                                        : 'border-slate-200 bg-white focus:ring-indigo-500 text-slate-700'
                                    }`}
                                  />
                                </td>
                                <td className="p-2 text-center">
                                  <div className="flex items-center justify-center gap-1">
                                    <button
                                      onClick={() => handleUpdateFieldValue(p.id_tagihan, 'nomor_invoice_grup', null)}
                                      className="text-[9px] font-bold text-slate-500 hover:text-white hover:bg-slate-500 border border-slate-200 px-1.5 py-0.5 rounded transition-all cursor-pointer"
                                      title="Keluarkan dari tagihan ini"
                                    >
                                      ✕ Lepas
                                    </button>
                                    {p.id_tagihan.startsWith('manual_tagihan_') && (
                                      <button
                                        onClick={() => {
                                          const updatedManuals = (state.manualTagihans || []).filter(mt => mt.id_tagihan !== p.id_tagihan);
                                          onStateChange({
                                            ...state,
                                            manualTagihans: updatedManuals
                                          });
                                          triggerNoti('sukses', 'Tagihan manual berhasil dihapus.');
                                        }}
                                        className="text-[9px] font-bold text-rose-600 hover:text-white hover:bg-rose-600 border border-rose-200 px-1.5 py-0.5 rounded transition-all cursor-pointer"
                                        title="Hapus tagihan manual secara permanen"
                                      >
                                        ✕ Hapus
                                      </button>
                                    )}
                                  </div>
                                </td>
                              </tr>
                            );
                          })}
                        </tbody>
                      </table>
                    </div>

                    {/* Left panel totals & Save actions */}
                    <div className="bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-3">
                      <div className="grid grid-cols-2 md:grid-cols-4 gap-2 text-center">
                        <div className="bg-white p-2 rounded-lg border border-slate-100 shadow-3xs">
                          <span className="text-[8px] font-bold text-slate-400 block uppercase">Estimasi</span>
                          <span className="text-xs font-mono font-bold text-slate-700">{formatRupiah(totalEstimasiGroup)}</span>
                        </div>
                        <div className="bg-white p-2 rounded-lg border border-slate-100 shadow-3xs">
                          <span className="text-[8px] font-bold text-slate-400 block uppercase">Aktual / Riil</span>
                          <span className="text-xs font-mono font-bold text-slate-800">{formatRupiah(totalAktualGroup)}</span>
                        </div>
                        <div className="bg-white p-2 rounded-lg border border-slate-100 shadow-3xs">
                          <span className="text-[8px] font-bold text-indigo-50 block uppercase">PPN (11%)</span>
                          <span className="text-xs font-mono font-bold text-indigo-700">+{formatRupiah(totalPPNGroup)}</span>
                        </div>
                        <div className="bg-white p-2 rounded-lg border border-slate-100 shadow-3xs">
                          <span className="text-[8px] font-bold text-rose-500 block uppercase">Potongan PPh (2%)</span>
                          <span className="text-xs font-mono font-bold text-rose-600">-{formatRupiah(totalPPhGroup)}</span>
                        </div>
                      </div>

                      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2 border-t border-slate-200">
                        <div>
                          <span className="text-[9px] font-extrabold text-slate-400 uppercase tracking-wider block">Grand Net Total</span>
                          <span className="text-sm font-extrabold text-emerald-800 font-mono">{formatRupiah(grandNetTotalGroup)}</span>
                        </div>
                        
                        <button
                          onClick={() => {
                            // Validation: if there is a discrepancy (selisih !== 0), keterangan_selisih must not be empty!
                            const missingReasonContainer = selectedGroupPeriods.find(p => {
                              const nominalEstimasi = p.jumlah_tagihan;
                              const nominalAktual = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : nominalEstimasi;
                              const selisih = nominalAktual - nominalEstimasi;
                              return selisih !== 0 && (!p.keterangan_selisih || p.keterangan_selisih.trim() === '');
                            });

                            if (missingReasonContainer) {
                              const sObj = state.sewas.find(s => s.id_sewa === missingReasonContainer.id_sewa);
                              triggerNoti('error', `Gagal menyimpan! Kontainer ${sObj?.no_kontainer || 'Manual'} memiliki selisih nominal tetapi Alasan Selisih belum diisi.`);
                              return;
                            }

                            // Change all these periods to 'Pranota' (Tagihan) status
                            const periodIds = selectedGroupPeriods.map(p => p.id_tagihan);
                            const updates = {
                              status_bayar: 'Belum Bayar',
                              tanggal_tagihan: selectedVendorTagihanDate || utcTime.split('T')[0]
                            };
                            handleBulkUpdate(periodIds, updates, 'Belum Bayar');
                            triggerNoti('sukses', `Sukses! Tagihan Vendor "${selectedVendorTagihanNo}" telah disimpan. Silakan lanjut menginput tagihan berikutnya di Tab 1 ini.`);
                            
                            // Reset states so they can input the next one
                            setSelectedVendorTagihanNo('');
                            // NOTE: Do NOT change activeViewTab! Stays on 'sheet' (Tab 1) for higher efficiency!
                          }}
                          className="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 cursor-pointer font-extrabold"
                        >
                          <CheckCircle2 className="w-4 h-4" />
                          <span>✓ Simpan Tagihan (Tetap di Tab 1)</span>
                        </button>
                      </div>
                    </div>
                  </div>
                )}
              </div>

              {/* RIGHT PANEL: AVAILABLE DRAFT CONTAINERS */}
              <div className="lg:col-span-5 space-y-6">
                
                {/* AVAILABLE DRAFT CONTAINERS */}
                <div className="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm space-y-4">
                  <div className="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div className="flex items-center gap-2">
                      <Sparkles className="w-5 h-5 text-indigo-600 shrink-0" />
                      <div>
                        <h4 className="text-xs font-extrabold text-slate-800 uppercase tracking-wider">
                          Daftar Draft Kontainer Tersedia
                        </h4>
                        <p className="text-[10px] text-slate-400 mt-0.5">
                          Daftar draf berjalan yang belum dikelompokkan ke tagihan manapun
                        </p>
                      </div>
                    </div>
                  </div>

                  {/* Filter and Search box inside draft list */}
                  <div className="flex gap-2">
                    <input
                      type="text"
                      placeholder="Cari No. Kontainer draf..."
                      value={searchDraftContainer}
                      onChange={(e) => setSearchDraftContainer(e.target.value)}
                      className="flex-1 text-xs font-mono border border-slate-200 rounded-lg px-2.5 py-1.5 bg-slate-50/50 text-slate-800 focus:bg-white"
                    />
                    {searchDraftContainer && (
                      <button
                        onClick={() => setSearchDraftContainer('')}
                        className="text-[10px] font-bold text-slate-400 hover:text-slate-600"
                      >
                        Batal
                      </button>
                    )}
                  </div>

                  {looseDraftPeriods.length === 0 ? (
                    <div className="py-12 text-center bg-slate-50 border border-dashed border-slate-200 rounded-2xl">
                      <p className="text-xs font-semibold text-slate-500">Tidak ada draf tersedia.</p>
                      <p className="text-[10px] text-slate-400 mt-1">Seluruh siklus sewa aktif untuk vendor ini telah dikelompokkan.</p>
                    </div>
                  ) : (
                    <div className="max-h-[500px] overflow-y-auto border border-slate-150 rounded-xl">
                      <table className="w-full text-left border-collapse text-[10px] font-sans">
                        <thead className="sticky top-0 bg-slate-50 border-b border-slate-200 text-slate-600 font-bold font-mono">
                          <tr>
                            <th className="p-2 py-2">No. Kontainer</th>
                            <th className="p-2 py-2">Bulan / Periode</th>
                            <th className="p-2 py-2 text-right">Estimasi</th>
                            <th className="p-2 py-2 text-center">Aksi</th>
                          </tr>
                        </thead>
                        <tbody className="divide-y divide-slate-150">
                          {looseDraftPeriods.map(p => {
                            const isSewaObj = state.sewas.find(s => s.id_sewa === p.id_sewa);
                            return (
                              <tr key={p.id_tagihan} className="hover:bg-slate-50/50">
                                <td className="p-2 font-mono font-bold text-slate-800">{isSewaObj?.no_kontainer || 'Manual'}</td>
                                <td className="p-2 text-slate-500">
                                  Bulan {p.bulan_ke} <span className="text-[8px] font-mono block">({formatIndoDate(p.tanggal_awal)} - {formatIndoDate(p.tanggal_akhir)})</span>
                                </td>
                                <td className="p-2 text-right font-mono text-slate-600">{formatRupiah(p.jumlah_tagihan)}</td>
                                <td className="p-2 text-center">
                                  <div className="flex items-center justify-center gap-1">
                                    <button
                                      disabled={!selectedVendorTagihanNo}
                                      onClick={() => handleUpdateFieldValue(p.id_tagihan, 'nomor_invoice_grup', selectedVendorTagihanNo)}
                                      className={`text-[9px] font-bold border rounded px-1.5 py-0.5 transition-all cursor-pointer ${
                                        selectedVendorTagihanNo
                                          ? 'bg-indigo-50 border-indigo-200 text-indigo-600 hover:bg-indigo-600 hover:text-white font-extrabold shadow-3xs'
                                          : 'bg-slate-50 border-slate-200 text-slate-400 cursor-not-allowed'
                                      }`}
                                      title={!selectedVendorTagihanNo ? 'Silakan isi No. Tagihan Vendor terlebih dahulu di atas' : 'Tambahkan ke tagihan vendor aktif'}
                                    >
                                      ⊕ Tambahkan
                                    </button>
                                    {p.id_tagihan.startsWith('manual_tagihan_') && (
                                      <button
                                        onClick={() => {
                                          const updatedManuals = (state.manualTagihans || []).filter(mt => mt.id_tagihan !== p.id_tagihan);
                                          onStateChange({
                                            ...state,
                                            manualTagihans: updatedManuals
                                          });
                                          triggerNoti('sukses', 'Tagihan manual berhasil dihapus.');
                                        }}
                                        className="text-[9px] font-bold text-rose-600 hover:text-white hover:bg-rose-600 border border-rose-200 px-1.5 py-0.5 rounded transition-all cursor-pointer"
                                        title="Hapus tagihan manual ini"
                                      >
                                        Hapus
                                      </button>
                                    )}
                                  </div>
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
            </div>
          </div>
        );
      })()}

      {false && (
        <div className="space-y-6" id="spreadsheet-workspace">
          
          {/* SEARCH & FILTERS PANEL */}
          <div className="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs space-y-4">
            <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
              <div>
                <label className="block text-xs font-semibold text-slate-500 mb-1">Cari No. Tagihan / Kontainer</label>
                <input
                  id="search-box-kontainer"
                  type="text"
                  placeholder="Ketik no. tagihan, kontainer, pranota..."
                  value={searchNoKontainer}
                  onChange={(e) => setSearchNoKontainer(e.target.value)}
                  list="autocomplete-tab1-datalist"
                  className="w-full text-xs font-mono border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50"
                />
                <datalist id="autocomplete-tab1-datalist">
                  {autocompleteTab1.map(val => (
                    <option key={val} value={val} />
                  ))}
                </datalist>
              </div>

              <div>
                <label className="block text-xs font-semibold text-slate-500 mb-1">{isSewaIn ? 'Vendor / Pemilik' : 'Penyewa / Customer'}</label>
                <SearchableSelect
                  id="search-select-cust"
                  placeholder={isSewaIn ? '-- Semua --' : '-- Semua --'}
                  searchPlaceholder={isSewaIn ? "Ketik nama vendor..." : "Ketik nama customer..."}
                  value={custFilter}
                  onChange={(val) => setCustFilter(val)}
                  inputClassName="bg-slate-50/50 py-1.5 text-xs h-[34px]"
                  options={[
                    { value: "", label: isSewaIn ? '-- Semua Vendor --' : '-- Semua Customer --' },
                    ...state.customers.map(c => ({
                      value: c.id_customer,
                      label: c.nama_customer
                    }))
                  ]}
                />
              </div>

              <div>
                <label className="block text-xs font-semibold text-slate-500 mb-1">{isSewaIn ? 'Status Pembayaran' : 'Status Tagihan'}</label>
                <select
                  id="search-select-status"
                  value={statusFilter}
                  onChange={(e) => setStatusFilter(e.target.value)}
                  className="w-full text-xs border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 text-slate-800"
                >
                  <option value="">-- Semua Status --</option>
                  <option value="Belum Ditagih">{isSewaIn ? 'Siklus Berjalan (Belum Ditagih)' : 'Belum Ditagih (Siklus Berjalan)'}</option>
                  <option value="Pranota">{isSewaIn ? 'Draft Tagihan' : 'Pranota (Draft Invoice)'}</option>
                  <option value="Belum Bayar">{isSewaIn ? 'Belum Dibayar (Billed)' : 'Belum Bayar (Billed)'}</option>
                  <option value="Lunas">{isSewaIn ? 'Lunas ( Paid )' : 'Lunas (Paid)'}</option>
                </select>
              </div>

              <div>
                <label className="block text-xs font-semibold text-slate-500 mb-1">Rentang Sewa (Pilih/Ketik)</label>
                <input
                  id="search-input-rentang-sewa"
                  list="rentang-sewa-datalist"
                  type="text"
                  placeholder="Ketik atau pilih rentang sewa..."
                  value={filterRentangSewa}
                  onChange={(e) => setFilterRentangSewa(e.target.value)}
                  className="w-full text-xs border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 text-slate-800 font-semibold"
                />
                <datalist id="rentang-sewa-datalist">
                  {distinctRanges.map(rg => (
                    <option key={rg} value={rg} />
                  ))}
                </datalist>
              </div>
            </div>

            {(filterRentangSewa || searchNoKontainer || custFilter || statusFilter) && (
              <div className="flex justify-end">
                <button
                  onClick={() => {
                    setFilterRentangSewa('');
                    setSearchNoKontainer('');
                    setCustFilter('');
                    setStatusFilter('');
                  }}
                  className="text-[10px] text-rose-600 hover:text-rose-800 font-extrabold flex items-center gap-1 cursor-pointer transition-colors"
                >
                  ✕ Reset Semua Filter Pencarian
                </button>
              </div>
            )}
          </div>

          {/* PENGISIAN MASAL / KOLEKTIF (BULK ENTRY PANEL) */}
          <div className="bg-slate-50 border border-slate-200 rounded-2xl p-5 shadow-xs space-y-4" id="bulk-entry-workspace">
            <div className="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-200 pb-3 gap-3">
              <div className="flex items-center gap-2">
                <CheckCircle2 className="w-5 h-5 text-indigo-600 shrink-0" />
                <div>
                  <h4 className="text-xs font-extrabold text-slate-800 uppercase tracking-wider">
                    Panel Pembuatan Pranota / Invoice Masal
                  </h4>
                  <p className="text-[10px] text-slate-500">
                    Beri tanda centang (☑) pada baris tabel di bawah, lalu masukkan No. Pranota / Invoice dan Tanggal untuk mengelompokkan tagihan kontainer terpilih.
                  </p>
                </div>
              </div>
              <div className="flex items-center gap-2">
                <span className="text-xs font-bold text-slate-700 bg-white border border-slate-200 rounded-lg px-3 py-1.5 font-mono shadow-3xs">
                  {selectedRowIds.length} kontainer terpilih
                </span>
                {selectedRowIds.length > 0 && (
                  <button
                    onClick={() => setSelectedRowIds([])}
                    className="text-[10px] text-rose-600 hover:text-rose-800 font-extrabold px-2.5 py-1.5 hover:bg-rose-50 rounded-lg cursor-pointer transition-colors"
                  >
                    Batal Pilih Semua
                  </button>
                )}
              </div>
            </div>

            {/* Quick Helper Selectors */}
            <div className="flex flex-wrap items-center gap-2.5">
              <span className="text-[10px] font-bold text-slate-500 uppercase">Pilih Cepat Halaman Ini:</span>
              <button
                type="button"
                onClick={() => {
                  const targetIds = paginatedPeriods
                    .filter(p => p.status_bayar === 'Belum Ditagih' || p.status_bayar === 'Belum Bayar')
                    .map(p => p.id_tagihan);
                  setSelectedRowIds(prev => Array.from(new Set([...prev, ...targetIds])));
                }}
                className="text-[10px] font-bold bg-white border border-slate-200 text-slate-700 hover:bg-indigo-50 hover:text-indigo-700 hover:border-indigo-300 rounded-lg px-3 py-1.5 transition-all cursor-pointer shadow-3xs"
              >
                ☑ Pilih Semua "Belum Ditagih &amp; Belum Bayar"
              </button>
              <button
                type="button"
                onClick={() => {
                  const targetIds = paginatedPeriods
                    .filter(p => p.status_bayar === 'Pranota')
                    .map(p => p.id_tagihan);
                  setSelectedRowIds(prev => Array.from(new Set([...prev, ...targetIds])));
                }}
                className="text-[10px] font-bold bg-white border border-slate-200 text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-300 rounded-lg px-3 py-1.5 transition-all cursor-pointer shadow-3xs"
              >
                ☑ Pilih Semua "Draft Pembayaran / Pranota"
              </button>
            </div>

            <div className="bg-white p-4 rounded-xl border border-slate-200 shadow-3xs space-y-3 relative overflow-hidden">
              <div className="absolute top-0 left-0 w-1.5 h-full bg-indigo-500" />
              <div className="pl-2">
                <h5 className="text-[11px] font-extrabold text-slate-800 flex items-center gap-1.5">
                  <span>INPUT NOMOR &amp; TANGGAL NOTA (PRANOTA / INVOICE)</span>
                </h5>
                <p className="text-[9px] text-slate-400 mt-0.5">
                  Sistem akan mengelompokkan semua kontainer yang dicentang di bawah ke dalam nomor pranota/invoice yang sama.
                </p>
              </div>

              <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3.5 pt-1 pl-2 items-end">
                <div>
                  <label className="block text-[10px] font-bold text-slate-500 mb-1">No. Pranota / Invoice</label>
                  <input
                    type="text"
                    placeholder="Contoh: INV/2026/..."
                    value={bulkPranotaNo}
                    onChange={(e) => setBulkPranotaNo(e.target.value)}
                    className="w-full text-xs font-mono border border-slate-200 rounded-lg px-2.5 py-1.5 bg-slate-50 focus:bg-white focus:outline-none h-9"
                  />
                </div>
                <div>
                  <label className="block text-[10px] font-bold text-slate-500 mb-1">Tgl. Pranota / Invoice</label>
                  <FormDateInput
                    value={bulkPranotaDate}
                    onChange={(val) => setBulkPranotaDate(val)}
                    className="w-full text-xs font-mono border border-slate-200 rounded-lg px-2.5 py-1.5 bg-slate-50 focus:bg-white focus:outline-none h-9"
                  />
                </div>
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 md:col-span-1">
                  <div className="flex-1">
                    <label className="block text-[10px] font-bold text-slate-500 mb-1">Ubah Status Ke:</label>
                    <select
                      value={bulkPranotaStatus}
                      onChange={(e) => setBulkPranotaStatus(e.target.value as any)}
                      className="text-[10px] font-bold border border-slate-200 rounded-lg px-2 py-1 bg-slate-50 h-9 w-full"
                    >
                      <option value="Pranota">Pranota (Draft Invoice)</option>
                      <option value="Belum Bayar">Belum Bayar (Billed)</option>
                      <option value="">Jangan Ubah Status</option>
                    </select>
                  </div>
                </div>
              </div>

              <div className="flex items-center justify-end pt-2 border-t border-slate-100">
                <button
                  type="button"
                  disabled={selectedRowIds.length === 0 || !bulkPranotaNo}
                  onClick={() => {
                    const updates: Record<string, any> = {
                      nomor_invoice_grup: bulkPranotaNo,
                      tanggal_tagihan: bulkPranotaDate || null
                    };
                    handleBulkUpdate(selectedRowIds, updates, bulkPranotaStatus || undefined);
                    setBulkPranotaNo('');
                    setBulkPranotaDate('');
                  }}
                  className={`text-[10px] font-extrabold px-4 py-2 rounded-lg transition-all flex items-center gap-1 cursor-pointer h-9 ${
                    selectedRowIds.length === 0 || !bulkPranotaNo
                      ? 'bg-slate-100 text-slate-400 border border-slate-200 cursor-not-allowed'
                      : 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-xs'
                  }`}
                >
                  <span>✓ Kelompokkan {selectedRowIds.length} Kontainer</span>
                </button>
              </div>
            </div>
          </div>

          {/* SPREADSHEET LEDGER GRID */}
          <div className="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            
            {/* Legend guide banner */}
            <div className="bg-emerald-50/80 border-b border-emerald-100 p-3 px-4 flex flex-col md:flex-row md:items-center justify-between gap-3 text-xs text-emerald-850">
              <span className="flex items-center gap-1.5 font-medium">
                <Sparkles className="w-4 h-4 text-emerald-600 shrink-0" />
                <span>
                  <strong>Akurasi Pengisian Mandiri (Sistem Excel)</strong>: Semua sel atau kolom berlatar jingga/kuning muda di bawah ini dapat diketik secara langsung. 
                  <span className="text-emerald-700 font-bold ml-1">✓ Tersimpan Otomatis secara Real-Time (Tanpa perlu klik tombol save)</span>.
                </span>
              </span>
              <div className="flex flex-wrap items-center gap-2">
                <span className="text-[10px] bg-emerald-600 text-white font-mono rounded-md px-2 py-0.5 font-bold shadow-xs flex items-center gap-1">
                  <span className="w-1.5 h-1.5 rounded-full bg-emerald-300 animate-pulse"></span>
                  <span>Auto-Save Aktif</span>
                </span>
                <span className="text-[10px] bg-indigo-600 text-white font-mono rounded-md px-2 py-0.5 font-bold shadow-xs">
                  WIB (Asia/Jakarta)
                </span>
              </div>
            </div>

            {/* Grid Container */}
            <div className="overflow-x-auto max-h-[610px] overflow-y-auto border border-slate-200 rounded-xl relative shadow-xs">
              <table className="w-full text-left border-collapse text-[10px] min-w-[1550px]" id="invoice-spreadsheet-table">
                <thead>
                  <tr className="bg-slate-100 border-b border-slate-200 text-slate-600 font-bold font-mono">
                    <th className="p-2 py-3 text-center border-r border-slate-200 w-[40px] min-w-[40px] max-w-[40px] sticky top-0 left-0 z-40 bg-slate-100 shadow-[1px_1px_0_rgba(226,232,240,1)]">
                      <input
                        type="checkbox"
                        className="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 h-3.5 w-3.5 cursor-pointer"
                        checked={paginatedPeriods.length > 0 && paginatedPeriods.every(p => selectedRowIds.includes(p.id_tagihan))}
                        onChange={(e) => {
                          if (e.target.checked) {
                            const pageIds = paginatedPeriods.map(p => p.id_tagihan);
                            setSelectedRowIds(prev => Array.from(new Set([...prev, ...pageIds])));
                          } else {
                            const pageIds = paginatedPeriods.map(p => p.id_tagihan);
                            setSelectedRowIds(prev => prev.filter(id => !pageIds.includes(id)));
                          }
                        }}
                      />
                    </th>
                    <th className="p-2 py-3 text-center border-r border-slate-200 w-[50px] min-w-[50px] max-w-[50px] sticky top-0 left-[40px] z-40 bg-slate-100 shadow-[1px_1px_0_rgba(226,232,240,1)]">NO</th>
                    <th className="p-2 py-3 border-r-2 border-slate-300 w-28 text-center sticky top-0 left-[90px] z-40 bg-slate-100 shadow-[2px_1px_4px_rgba(0,0,0,0.08)]">
                      {isSewaIn ? 'VENDOR / UNIT' : 'KONTAINER / PENYEWA'}
                    </th>
                    <th className="p-2 py-3 border-r border-slate-200 w-16 text-center sticky top-0 z-20 bg-slate-100 shadow-[0_1px_0_rgba(226,232,240,1)]">UKURAN</th>
                    <th className="p-2 py-3 border-r border-slate-200 w-24 text-center sticky top-0 z-20 bg-slate-100 shadow-[0_1px_0_rgba(226,232,240,1)]">PERIODE</th>
                    <th className="p-2 py-3 border-r border-slate-200 w-20 text-center sticky top-0 z-20 bg-slate-100 shadow-[0_1px_0_rgba(226,232,240,1)]">MULAI AWAL</th>
                    <th className="p-2 py-3 border-r border-slate-200 w-20 text-center sticky top-0 z-20 bg-slate-100 shadow-[0_1px_0_rgba(226,232,240,1)]">AKHIR SIKLUS</th>
                    <th className="p-2 py-3 border-r border-slate-200 w-12 text-center sticky top-0 z-20 bg-slate-100 shadow-[0_1px_0_rgba(226,232,240,1)]">HARI</th>
                    <th className="p-2 py-3 border-r border-slate-200 w-16 text-center sticky top-0 z-20 bg-slate-100 shadow-[0_1px_0_rgba(226,232,240,1)]">TARIF</th>
                    <th className="p-2 py-3 border-r border-slate-200 text-right w-24 pr-3 sticky top-0 z-20 bg-slate-100 shadow-[0_1px_0_rgba(226,232,240,1)]">ESTIMASI (SISTEM)</th>
                    <th className="p-2 py-3 border-r border-slate-200 text-right w-28 pr-3 bg-amber-100 sticky top-0 z-20 shadow-[0_1px_0_rgba(226,232,240,1)]">{isSewaIn ? 'BIY VENDOR (AKTUAL)' : 'TAGIHAN (AKTUAL)'}</th>
                    <th className="p-2 py-3 border-r border-slate-200 text-right w-24 pr-3 sticky top-0 z-20 bg-slate-100 shadow-[0_1px_0_rgba(226,232,240,1)]">SELISIH</th>
                    <th className="p-2 py-3 border-r border-slate-200 text-right w-24 pr-3 sticky top-0 z-20 bg-emerald-100 text-emerald-900 shadow-[0_1px_0_rgba(226,232,240,1)]">REK. BAYAR</th>
                    <th className="p-2 py-3 border-r border-slate-200 w-36 bg-amber-100 sticky top-0 z-20 shadow-[0_1px_0_rgba(226,232,240,1)]">KETERANGAN SELISIH</th>
                    <th className="p-2 py-3 border-r border-slate-200 text-right w-24 pr-3 bg-amber-50/50 sticky top-0 z-20 shadow-[0_1px_0_rgba(226,232,240,1)]">{isSewaIn ? 'PPN MASUKAN (11%)' : 'PPN KELUARAN (11%)'}</th>
                    <th className="p-2 py-3 border-r border-slate-200 text-right w-24 pr-3 bg-amber-50/50 sticky top-0 z-20 shadow-[0_1px_0_rgba(226,232,240,1)]">POT. PPh 23 (2%)</th>
                    <th className="p-2 py-3 border-r border-slate-200 text-right w-24 pr-3 text-slate-900 sticky top-0 z-20 bg-slate-100 shadow-[0_1px_0_rgba(226,232,240,1)]">NET TOTAL</th>
                    <th className="p-2 py-3 border-r border-slate-200 w-28 bg-amber-50/50 text-center sticky top-0 z-20 shadow-[0_1px_0_rgba(226,232,240,1)]">{isSewaIn ? 'NO. INVOICE VENDOR' : 'NO. TAGIHAN / INVOICE'}</th>
                    <th className="p-2 py-3 border-r border-slate-200 w-24 bg-amber-50/50 text-center sticky top-0 z-20 shadow-[0_1px_0_rgba(226,232,240,1)]">{isSewaIn ? 'TGL. INVOICE VENDOR' : 'TGL. TAGIHAN'}</th>
                    <th className="p-2 py-3 border-r border-slate-200 w-28 bg-amber-50/50 text-center sticky top-0 z-20 shadow-[0_1px_0_rgba(226,232,240,1)]">NO. BUKTI BAYAR</th>
                    <th className="p-2 py-3 border-r border-slate-200 w-24 bg-amber-50/50 text-center sticky top-0 z-20 shadow-[0_1px_0_rgba(226,232,240,1)]">TGL. BAYAR</th>
                    <th className="p-2 py-3 w-32 text-center bg-indigo-50/30 sticky top-0 z-20 shadow-[0_1px_0_rgba(226,232,240,1)]">STATUS SIKLUS</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-slate-150 text-[10px] font-sans">
                  {paginatedPeriods.map((p, indexInPage) => {
                    const indexRow = (ledgerPage - 1) * ledgerPageSize + indexInPage;
                    // Calculated fields:
                    const estimasi = p.jumlah_tagihan;
                    const tagihan = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined
                      ? p.jumlah_tagihan_override
                      : estimasi;
                    const selisih = p.selisih_pembayaran !== null && p.selisih_pembayaran !== undefined
                      ? p.selisih_pembayaran
                      : 0;
                    
                    const ppn = p.ppn !== null && p.ppn !== undefined
                      ? p.ppn
                      : Math.round(tagihan * 0.11);
                    const pph = p.pph !== null && p.pph !== undefined
                      ? p.pph
                      : Math.round(tagihan * 0.02);
                    const netTotal = tagihan + ppn - pph;
                    return (
                      <tr key={p.id_tagihan} className="hover:bg-slate-50 select-text align-middle group">
                        {/* CHECKBOX CELL */}
                        <td className="p-2 text-center border-r border-slate-200 sticky left-0 z-30 bg-white group-hover:bg-slate-100 shadow-[1px_0_0_rgba(226,232,240,1)] w-[40px] min-w-[40px] max-w-[40px]">
                          <input
                            type="checkbox"
                            className="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 h-3.5 w-3.5 cursor-pointer"
                            checked={selectedRowIds.includes(p.id_tagihan)}
                            onChange={(e) => {
                              if (e.target.checked) {
                                setSelectedRowIds(prev => [...prev, p.id_tagihan]);
                              } else {
                                setSelectedRowIds(prev => prev.filter(id => id !== p.id_tagihan));
                              }
                            }}
                          />
                        </td>
                        {/* 1. NO */}
                        <td className="p-2 text-center font-mono text-slate-400 border-r border-slate-200 sticky left-[40px] z-30 bg-white group-hover:bg-slate-100 shadow-[1px_0_0_rgba(226,232,240,1)] w-[50px] min-w-[50px] max-w-[50px]">{indexRow + 1}</td>
                        {/* 2. KONTAINER */}
                        <td className="p-2 font-mono font-bold text-slate-900 text-center border-r-2 border-slate-300 sticky left-[90px] z-30 bg-white group-hover:bg-slate-100 shadow-[2px_0_4px_rgba(0,0,0,0.08)]">
                          {getSewaContainerNo(p.id_sewa)}
                          <p className="text-[8px] font-sans text-slate-400 font-normal truncate max-w-[100px] mx-auto">
                            {getCustomerName(state.sewas.find(s => s.id_sewa === p.id_sewa)?.id_customer || '')}
                          </p>
                        </td>
                        {/* 4. UKURAN */}
                        <td className="p-2 text-center text-slate-700 border-r border-slate-100 font-mono font-medium">{getSewaContainerSizeDesc(p.id_sewa)}</td>
                        {/* 5. PERIODE */}
                        <td className="p-2 text-center border-r border-slate-100 font-semibold text-emerald-850">
                          <div className="font-bold">Bulan ke-{p.bulan_ke}</div>
                          {(() => {
                            const sObj = state.sewas.find(s => s.id_sewa === p.id_sewa);
                            return sObj ? (
                              <div className="text-[9px] text-slate-400 font-normal mt-0.5 whitespace-nowrap" title={`Tanggal Mulai Sewa: ${formatIndoDate(sObj.tanggal_sewa)}`}>
                                Kontrak: {formatIndoDate(sObj.tanggal_sewa)}
                              </div>
                            ) : null;
                          })()}
                        </td>
                        {/* 6. MULAI AWAL */}
                        <td className="p-2 text-center text-slate-600 border-r border-slate-100 font-mono">{formatIndoDate(p.tanggal_awal)}</td>
                        {/* 7. AKHIR SIKLUS */}
                        <td className="p-2 text-center text-slate-600 border-r border-slate-100 font-mono">{formatIndoDate(p.tanggal_akhir)}</td>
                        {/* 8. HARI */}
                        <td className="p-2 text-center font-mono font-bold text-slate-700 border-r border-slate-100 bg-slate-100">{p.jumlah_hari}</td>
                        {/* 9. TARIF TYPE */}
                        <td className="p-2 text-center border-r border-slate-100 font-mono text-[9px] font-semibold text-indigo-700">
                          <span className={`px-1.5 py-0.5 rounded-md ${
                            p.tipe_tarif === 'BULANAN' ? 'bg-indigo-50 text-indigo-700' :
                            p.tipe_tarif === 'HARIAN' ? 'bg-amber-50 text-amber-800' : 'bg-teal-50 text-teal-800'
                          }`}>
                            {p.tipe_tarif}
                          </span>
                        </td>
                        {/* 10. ESTIMASI */}
                        <td className="p-2 text-right pr-3 font-mono font-bold text-slate-500 border-r border-slate-100">{formatRupiah(estimasi)}</td>
                        
                        {/* 11. TAGIHAN (EDITABLE) IMMERSED IN YELLOW */}
                        <td className="p-1 px-2 border-r border-slate-150 bg-amber-50">
                          <FastNumberInput
                            value={p.jumlah_tagihan_override}
                            placeholder={String(estimasi)}
                            onChange={(val) => {
                              handleUpdateFieldValue(p.id_tagihan, 'jumlah_tagihan_override', val);
                            }}
                            className="w-full text-right font-mono font-bold bg-amber-50 text-slate-800 border-b border-dashed border-amber-300 focus:border-amber-500 focus:bg-white focus:outline-none p-1 rounded-sm text-[11px]"
                          />
                        </td>

                        {/* 12. SELISIH (AUTOMATED COMPLETED) */}
                        <td className={`p-2 text-right pr-3 font-mono font-bold border-r border-slate-100 ${
                          selisih < 0 ? 'text-rose-600' :
                          selisih > 0 ? 'text-emerald-700' : 'text-slate-400'
                        }`}>
                          {selisih > 0 ? '+' : ''}{formatRupiah(selisih)}
                        </td>

                        {/* REK. BAYAR (Sesuai Aturan Selisih) */}
                        <td className="p-2 text-right pr-3 font-mono font-bold text-emerald-800 bg-emerald-50 border-r border-slate-100 font-extrabold shadow-[inset_0_-2px_0_rgba(16,185,129,0.2)]">
                          {formatRupiah(Math.min(tagihan, estimasi))}
                        </td>

                        {/* 13. KETERANGAN SELISIH (EDITABLE) */}
                        <td className="p-1 px-2 border-r border-slate-150 bg-amber-50">
                          <FastTextInput
                            value={p.keterangan_selisih}
                            placeholder="Klaim, Denda, Diskon, dll..."
                            onChange={(val) => handleUpdateFieldValue(p.id_tagihan, 'keterangan_selisih', val)}
                            className="w-full text-left font-sans text-[10px] bg-transparent border-b border-dashed border-amber-300 focus:border-amber-500 focus:bg-white focus:outline-none p-1 rounded-sm text-slate-800 font-medium"
                          />
                        </td>

                        {/* 14. PPN (EDITABLE BUT DEFAULT 11%) */}
                        <td className="p-1 px-2 border-r border-slate-150 bg-amber-50/30">
                          <FastNumberInput
                            value={p.ppn}
                            placeholder={String(Math.round(tagihan * 0.11))}
                            onChange={(val) => {
                              handleUpdateFieldValue(p.id_tagihan, 'ppn', val);
                            }}
                            className="w-full text-right font-mono text-slate-800 border-b border-dashed border-amber-300 focus:border-amber-500 focus:bg-white focus:outline-none p-1 rounded-sm"
                          />
                        </td>

                        {/* 15. PPh (EDITABLE BUT DEFAULT 2%) */}
                        <td className="p-1 px-2 border-r border-slate-150 bg-amber-50/30">
                          <FastNumberInput
                            value={p.pph}
                            placeholder={String(Math.round(tagihan * 0.02))}
                            onChange={(val) => {
                              handleUpdateFieldValue(p.id_tagihan, 'pph', val);
                            }}
                            className="w-full text-right font-mono text-slate-800 border-b border-dashed border-amber-300 focus:border-amber-500 focus:bg-white focus:outline-none p-1 rounded-sm"
                          />
                        </td>

                        {/* 16. NET TOTAL (AUTOMATED DISPLAY) */}
                        <td className="p-2 text-right pr-3 font-mono font-bold text-teal-850 bg-teal-50/30 border-r border-slate-100 text-[11px]">
                          {formatRupiah(netTotal)}
                        </td>

                        {/* 17. NO. TAGIHAN / INVOICE REF */}
                        <td className="p-1 px-2 border-r border-slate-150 bg-amber-50/30">
                          <FastTextInput
                            value={p.nomor_invoice_grup}
                            onChange={(val) => handleUpdateFieldValue(p.id_tagihan, 'nomor_invoice_grup', val)}
                            placeholder="ZONA25052..."
                            className="w-full text-center font-mono text-[10px] bg-transparent border-b border-dashed border-amber-300 focus:border-amber-500 focus:bg-white focus:outline-none p-1 rounded-sm text-slate-800 font-medium"
                          />
                        </td>

                        {/* 18. TANGGAL TAGIHAN */}
                        <td className="p-1 px-2 border-r border-slate-150 bg-amber-50/30">
                          <EditableDateCell
                            value={p.tanggal_tagihan}
                            onChange={(val) => handleUpdateFieldValue(p.id_tagihan, 'tanggal_tagihan', val)}
                            placeholder="dd Mmm yy"
                            className="w-full text-center font-mono text-[10px] bg-transparent border-b border-dashed border-amber-300 focus:border-amber-500 focus:bg-white focus:outline-none p-1 rounded-sm"
                          />
                        </td>

                        {/* 19. NO. BUKTI BAYAR */}
                        <td className="p-1 px-2 border-r border-slate-150 bg-amber-50/30">
                          <FastTextInput
                            value={p.nomor_bayar}
                            onChange={(val) => handleUpdateFieldValue(p.id_tagihan, 'nomor_bayar', val)}
                            placeholder="EBK2506002..."
                            className="w-full text-center font-mono text-[10px] bg-transparent border-b border-dashed border-amber-300 focus:border-amber-500 focus:bg-white focus:outline-none p-1 rounded-sm text-slate-800 font-medium"
                          />
                        </td>

                        {/* 20. TANGGAL BAYAR */}
                        <td className="p-1 px-2 border-r border-slate-150 bg-amber-50/30">
                          <EditableDateCell
                            value={p.tanggal_bayar}
                            onChange={(val) => handleUpdateFieldValue(p.id_tagihan, 'tanggal_bayar', val)}
                            placeholder="dd Mmm yy"
                            className="w-full text-center font-mono text-[10px] bg-transparent border-b border-dashed border-amber-300 focus:border-amber-500 focus:bg-white focus:outline-none p-1 rounded-sm"
                          />
                        </td>

                        {/* 21. STATUS SELECTOR */}
                        <td className="p-2 text-center bg-indigo-50/20 align-middle">
                          <select
                            value={p.status_bayar}
                            onChange={(e) => handleUpdateFieldValue(p.id_tagihan, 'status_bayar', e.target.value)}
                            className={`text-[10px] font-bold p-1 rounded-md border w-full text-center cursor-pointer ${
                              p.status_bayar === 'Lunas' ? 'bg-emerald-50 text-emerald-800 border-emerald-300' :
                              p.status_bayar === 'Belum Bayar' ? 'bg-rose-50 text-rose-800 border-rose-200' :
                              p.status_bayar === 'Pranota' ? 'bg-blue-50 text-blue-800 border-blue-200' :
                              'bg-slate-50 text-slate-700 border-slate-250'
                            }`}
                          >
                            <option value="Belum Ditagih">1. Draft Tagihan</option>
                            <option value="Belum Bayar">2. Belum Bayar</option>
                            <option value="Pranota">3. Draft Pembayaran (Pranota)</option>
                            <option value="Lunas">4. Lunas / Bayar</option>
                          </select>
                        </td>
                      </tr>
                    );
                  })}
                  {filteredPeriods.length === 0 && (
                    <tr>
                      <td colSpan={21} className="p-10 text-center font-semibold text-slate-400 bg-slate-50/50">
                        Tidak ada record siklus sewa yang memenuhi kriteria filter.
                      </td>
                    </tr>
                  )}
                </tbody>
              </table>
            </div>

            {/* SPREADSHEET PAGINATION CONTROLS */}
            {filteredPeriods.length > ledgerPageSize && (
              <div className="flex flex-wrap items-center justify-between gap-3 p-3.5 bg-white border-b border-x border-slate-150 rounded-b-2xl shadow-xs text-xs text-slate-600">
                <div className="flex items-center gap-2">
                  <span>Menampilkan <strong>{Math.min(filteredPeriods.length, (ledgerPage - 1) * ledgerPageSize + 1)}-{Math.min(filteredPeriods.length, ledgerPage * ledgerPageSize)}</strong> dari <strong>{filteredPeriods.length}</strong> rekor tagihan</span>
                </div>
                <div className="flex items-center gap-1 font-mono">
                  <button
                    disabled={ledgerPage === 1}
                    onClick={() => setLedgerPage(1)}
                    className="px-2 py-1 rounded bg-slate-50 border border-slate-200 hover:bg-slate-100 disabled:opacity-50 disabled:hover:bg-slate-50 cursor-pointer"
                  >
                    ⏮ First
                  </button>
                  <button
                    disabled={ledgerPage === 1}
                    onClick={() => setLedgerPage(p => Math.max(1, p - 1))}
                    className="px-2.5 py-1 rounded bg-slate-50 border border-slate-200 hover:bg-slate-100 disabled:opacity-50 disabled:hover:bg-slate-50 cursor-pointer font-semibold"
                  >
                    Sebelumnya
                  </button>
                  <span className="px-3 py-1 font-bold text-slate-800 bg-slate-100 rounded-lg">
                    Halaman {ledgerPage} / {totalPages}
                  </span>
                  <button
                    disabled={ledgerPage === totalPages}
                    onClick={() => setLedgerPage(p => Math.min(totalPages, p + 1))}
                    className="px-2.5 py-1 rounded bg-slate-50 border border-slate-200 hover:bg-slate-100 disabled:opacity-50 disabled:hover:bg-slate-50 cursor-pointer font-semibold"
                  >
                    Berikutnya
                  </button>
                  <button
                    disabled={ledgerPage === totalPages}
                    onClick={() => setLedgerPage(totalPages)}
                    className="px-2 py-1 rounded bg-slate-50 border border-slate-200 hover:bg-slate-100 disabled:opacity-50 disabled:hover:bg-slate-50 cursor-pointer"
                  >
                    ⏭ Last
                  </button>
                </div>
              </div>
            )}

            {/* SPREADSHEET FOOTER AGGREGATES */}
            {filteredPeriods.length > 0 && (
              <div className="bg-slate-100 p-4 border-t border-slate-200 text-xs font-mono grid grid-cols-2 md:grid-cols-6 gap-4 text-slate-700">
                <div>
                  <span className="text-[9px] uppercase font-bold text-slate-500 tracking-wider block">Total Estimasi</span>
                  <span className="font-bold text-slate-800">{formatRupiah(filteredPeriods.reduce((sum, p) => sum + p.jumlah_tagihan, 0))}</span>
                </div>
                <div>
                  <span className="text-[9px] uppercase font-bold text-slate-500 tracking-wider block">Total Tagihan (Aktual)</span>
                  <span className="font-bold text-slate-900">{formatRupiah(filteredPeriods.reduce((sum, p) => {
                    const tagihan = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : p.jumlah_tagihan;
                    return sum + tagihan;
                  }, 0))}</span>
                </div>
                <div>
                  <span className="text-[9px] uppercase font-bold text-slate-500 tracking-wider block">Total Selisih</span>
                  {(() => {
                    const sum = filteredPeriods.reduce((sumVal, p) => {
                      const tagihan = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : p.jumlah_tagihan;
                      return sumVal + (tagihan - p.jumlah_tagihan);
                    }, 0);
                    return (
                      <span className={`font-bold ${sum < 0 ? 'text-rose-600' : sum > 0 ? 'text-emerald-700' : 'text-slate-600'}`}>
                        {sum > 0 ? '+' : ''}{formatRupiah(sum)}
                      </span>
                    );
                  })()}
                </div>
                <div>
                  <span className="text-[9px] uppercase font-bold text-slate-500 tracking-wider block">Total Rek. Bayar</span>
                  <span className="font-bold text-emerald-800">
                    {formatRupiah(filteredPeriods.reduce((sum, p) => {
                      const estimasi = p.jumlah_tagihan;
                      const tagihan = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : estimasi;
                      return sum + Math.min(tagihan, estimasi);
                    }, 0))}
                  </span>
                </div>
                <div>
                  <span className="text-[9px] uppercase font-bold text-slate-500 tracking-wider block">Total PPN (11%)</span>
                  <span className="font-bold text-indigo-700">{formatRupiah(filteredPeriods.reduce((sum, p) => {
                    const tagihan = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : p.jumlah_tagihan;
                    const ppn = p.ppn !== null && p.ppn !== undefined ? p.ppn : Math.round(tagihan * 0.11);
                    return sum + ppn;
                  }, 0))}</span>
                </div>
                <div>
                  <span className="text-[9px] uppercase font-bold text-slate-500 tracking-wider block">Total Potongan PPh (2%)</span>
                  <span className="font-bold text-rose-600">{formatRupiah(filteredPeriods.reduce((sum, p) => {
                    const tagihan = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : p.jumlah_tagihan;
                    const pph = p.pph !== null && p.pph !== undefined ? p.pph : Math.round(tagihan * 0.02);
                    return sum + pph;
                  }, 0))}</span>
                </div>
                <div className="bg-emerald-50 p-2 rounded-lg border border-emerald-200">
                  <span className="text-[9px] uppercase font-extrabold text-emerald-800 tracking-wider block">Grand Net Total</span>
                  <span className="font-extrabold text-emerald-950 block text-sm">{formatRupiah(filteredPeriods.reduce((sum, p) => {
                    const tagihan = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : p.jumlah_tagihan;
                    const ppn = p.ppn !== null && p.ppn !== undefined ? p.ppn : Math.round(tagihan * 0.11);
                    const pph = p.pph !== null && p.pph !== undefined ? p.pph : Math.round(tagihan * 0.02);
                    return sum + (tagihan + ppn - pph);
                  }, 0))}</span>
                </div>
              </div>
            )}
          </div>
        </div>
      )}
        
        {/* VIEW TAB 2: PEMBAYARAN & TAX SETTLEMENT VERIFICATION VIEW (DETAIL TAGIHAN - 5.png) */}
        {activeViewTab === 'group' && (() => {
          // Filter the active tagihans inside the selected vendor proforma/pranota group
          const selectedGroupPeriods = allPeriods.filter(p => {
            const sObj = state.sewas.find(s => s.id_sewa === p.id_sewa);
            return (
              p.nomor_pranota === selectedVendorPranotaNo &&
              selectedVendorPranotaNo.trim() !== '' &&
              (!selectedVendorCustomer || sObj?.id_customer === selectedVendorCustomer)
            );
          });

          // Group the selectedGroupPeriods by nomor_invoice_grup (No. Tagihan) for display on the left side
          interface GroupedSelectedInvoice {
            nomor_invoice_grup: string;
            tanggal_tagihan: string | null;
            total_estimasi: number;
            total_aktual: number;
            selisih: number;
            keterangan_selisih: string;
            id_tagihan_list: string[];
          }

          const leftGroupsMap = new Map<string, GroupedSelectedInvoice>();
          selectedGroupPeriods.forEach(p => {
            const groupNo = p.nomor_invoice_grup || '-';
            const baseAmt = p.jumlah_tagihan;
            const actAmt = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : baseAmt;
            const selisih = actAmt - baseAmt;
            const ket = p.keterangan_selisih || '';

            if (!leftGroupsMap.has(groupNo)) {
              leftGroupsMap.set(groupNo, {
                nomor_invoice_grup: groupNo,
                tanggal_tagihan: p.tanggal_tagihan || null,
                total_estimasi: baseAmt,
                total_aktual: actAmt,
                selisih: selisih,
                keterangan_selisih: ket,
                id_tagihan_list: [p.id_tagihan]
              });
            } else {
              const existing = leftGroupsMap.get(groupNo)!;
              existing.total_estimasi += baseAmt;
              existing.total_aktual += actAmt;
              existing.selisih += selisih;
              if (ket && !existing.keterangan_selisih.includes(ket)) {
                existing.keterangan_selisih = existing.keterangan_selisih ? existing.keterangan_selisih + '; ' + ket : ket;
              }
              if (!existing.id_tagihan_list.includes(p.id_tagihan)) {
                existing.id_tagihan_list.push(p.id_tagihan);
              }
            }
          });

          const leftGroupedInvoices = Array.from(leftGroupsMap.values());

          // Filter loose Tagihans and group them by No. Tagihan (nomor_invoice_grup)
          interface GroupedLooseTagihan {
            nomor_invoice_grup: string;
            tanggal_tagihan: string | null;
            total_estimasi: number;
            id_tagihan_list: string[];
            container_list: string[];
          }

          const rawLoosePeriods = allPeriods.filter(p => {
            const sObj = state.sewas.find(s => s.id_sewa === p.id_sewa);
            const isMatchCustomer = !selectedVendorCustomer || sObj?.id_customer === selectedVendorCustomer;
            const isOutstanding = p.status_bayar === 'Belum Bayar';
            const hasNoPranota = !p.nomor_pranota || p.nomor_pranota.trim() === '';
            
            return isMatchCustomer && isOutstanding && hasNoPranota;
          });

          const looseGroupsMap = new Map<string, GroupedLooseTagihan>();
          rawLoosePeriods.forEach(p => {
            const groupNo = p.nomor_invoice_grup || '-';
            const sObj = state.sewas.find(s => s.id_sewa === p.id_sewa);
            const containerNo = sObj?.no_kontainer || '';

            // Calculate grand total nett after PPN and PPh
            const baseAmt = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : p.jumlah_tagihan;
            const ppnAmt = p.ppn !== null && p.ppn !== undefined ? p.ppn : Math.round(baseAmt * 0.11);
            const pphAmt = p.pph !== null && p.pph !== undefined ? p.pph : Math.round(baseAmt * 0.02);
            const netTotal = baseAmt + ppnAmt - pphAmt;

            if (!looseGroupsMap.has(groupNo)) {
              looseGroupsMap.set(groupNo, {
                nomor_invoice_grup: groupNo,
                tanggal_tagihan: p.tanggal_tagihan || null,
                total_estimasi: netTotal,
                id_tagihan_list: [p.id_tagihan],
                container_list: containerNo ? [containerNo] : []
              });
            } else {
              const existing = looseGroupsMap.get(groupNo)!;
              existing.total_estimasi += netTotal;
              if (!existing.id_tagihan_list.includes(p.id_tagihan)) {
                existing.id_tagihan_list.push(p.id_tagihan);
              }
              if (containerNo && !existing.container_list.includes(containerNo)) {
                existing.container_list.push(containerNo);
              }
            }
          });

          let looseTagihanGroups = Array.from(looseGroupsMap.values());
          if (searchTagihanNo.trim()) {
            const q = searchTagihanNo.toLowerCase();
            looseTagihanGroups = looseTagihanGroups.filter(g =>
              g.nomor_invoice_grup.toLowerCase().includes(q) ||
              g.container_list.some(c => c.toLowerCase().includes(q))
            );
          }

          // Compile outstanding proforma/pranotas draft groups for quick selection
          const outstandingDraftPranotas = (() => {
            const groupsMap = new Map<string, {
              nomor_pranota: string;
              id_customer: string;
              customerName: string;
              status_bayar: string;
              count: number;
              totalEstimasi: number;
              totalAktual: number;
              tanggal_pranota: string | null;
            }>();

            allPeriods.forEach(p => {
              const pranotaNo = p.nomor_pranota;
              if (!pranotaNo || pranotaNo.trim() === '') return;

              const sObj = state.sewas.find(s => s.id_sewa === p.id_sewa);
              const custId = sObj?.id_customer || '';
              const custName = custId ? getCustomerName(custId) : 'Umum/Campuran';

              const amt = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : p.jumlah_tagihan;

              if (!groupsMap.has(pranotaNo)) {
                groupsMap.set(pranotaNo, {
                  nomor_pranota: pranotaNo,
                  id_customer: custId,
                  customerName: custName,
                  status_bayar: p.status_bayar,
                  count: 1,
                  totalEstimasi: p.jumlah_tagihan,
                  totalAktual: amt,
                  tanggal_pranota: p.tanggal_pranota || null
                });
              } else {
                const existing = groupsMap.get(pranotaNo)!;
                existing.count += 1;
                existing.totalEstimasi += p.jumlah_tagihan;
                existing.totalAktual += amt;
              }
            });

            return Array.from(groupsMap.values())
              .filter(g => g.status_bayar === 'Belum Bayar')
              .sort((a, b) => b.nomor_pranota.localeCompare(a.nomor_pranota))
              .slice(0, 20);
          })();

          // Local state handlers
          const handleAttachPranota = (idTagihan: string | string[]) => {
            if (!selectedVendorPranotaNo) {
              triggerNoti('error', 'Masukkan atau pilih No. Pranota terlebih dahulu.');
              return;
            }
            const idList = Array.isArray(idTagihan) ? idTagihan : [idTagihan];
            const updatedOverrides = { ...state.paymentOverrides };
            
            idList.forEach(id => {
              const existing = updatedOverrides[id] || {
                status_bayar: 'Belum Bayar',
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
                nomor_pranota: selectedVendorPranotaNo,
                tanggal_pranota: selectedVendorPranotaDate || utcTime.split('T')[0]
              };
            });

            onStateChange({
              ...state,
              paymentOverrides: updatedOverrides
            });
          };

          const handleDetachPranota = (idTagihan: string | string[]) => {
            const idList = Array.isArray(idTagihan) ? idTagihan : [idTagihan];
            const updatedOverrides = { ...state.paymentOverrides };
            idList.forEach(id => {
              const existing = updatedOverrides[id] || {
                status_bayar: 'Belum Bayar',
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
                nomor_pranota: null,
                tanggal_pranota: null
              };
            });
            onStateChange({
              ...state,
              paymentOverrides: updatedOverrides
            });
          };

          const handleUpdateGroupFieldValue = (
            idTagihanList: string[],
            field: string,
            value: any
          ) => {
            const updatedOverrides = { ...state.paymentOverrides };
            
            if (field === 'jumlah_tagihan_override') {
              if (idTagihanList.length === 1) {
                const id = idTagihanList[0];
                const periodObj = allPeriods.find(x => x.id_tagihan === id);
                const sewaObj = state.sewas.find(s => s.id_sewa === periodObj?.id_sewa);
                const isNonPpn = sewaObj?.non_ppn === true;

                const existing = updatedOverrides[id] || {
                  status_bayar: 'Belum Bayar',
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
                const numVal = value === '' || value === null ? null : Number(value);
                updatedOverrides[id] = {
                  ...existing,
                  jumlah_tagihan_override: numVal,
                  ppn: numVal !== null ? (isNonPpn ? 0 : Math.round(numVal * 0.11)) : null,
                  pph: numVal !== null ? Math.round(numVal * 0.02) : null
                };
              } else {
                const totalEstimasi = idTagihanList.reduce((sum, id) => {
                  const p = allPeriods.find(x => x.id_tagihan === id);
                  return sum + (p?.jumlah_tagihan || 0);
                }, 0);

                const numVal = value === '' || value === null ? null : Number(value);
                if (numVal === null) {
                  idTagihanList.forEach(id => {
                    const existing = updatedOverrides[id] || {
                      status_bayar: 'Belum Bayar',
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
                      status_bayar: 'Belum Bayar',
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
            } else {
              idTagihanList.forEach(id => {
                const existing = updatedOverrides[id] || {
                  status_bayar: 'Belum Bayar',
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
                  [field]: value
                };
              });
            }

            onStateChange({
              ...state,
              paymentOverrides: updatedOverrides
            });
          };

          // Calculate totals for selected group
          const totalEstimasiGroup = selectedGroupPeriods.reduce((sum, p) => sum + p.jumlah_tagihan, 0);
          const totalAktualGroup = selectedGroupPeriods.reduce((sum, p) => {
            const amt = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : p.jumlah_tagihan;
            return sum + amt;
          }, 0);
          const totalPPNGroup = selectedGroupPeriods.reduce((sum, p) => {
            const amt = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : p.jumlah_tagihan;
            const ppn = p.ppn !== null && p.ppn !== undefined ? p.ppn : Math.round(amt * 0.11);
            return sum + ppn;
          }, 0);
          const totalPPhGroup = selectedGroupPeriods.reduce((sum, p) => {
            const amt = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : p.jumlah_tagihan;
            const pph = p.pph !== null && p.pph !== undefined ? p.pph : Math.round(amt * 0.02);
            return sum + pph;
          }, 0);
          const grandNetTotalGroup = totalAktualGroup + totalPPNGroup - totalPPhGroup;

        return (
          <div className="space-y-6" id="pranota-group-workspace">
            {/* SUB-TABS SELECTOR FOR MANUAL PRANOTA ENTRY MODE */}
            <div className="bg-slate-100/80 p-1.5 rounded-xl border border-slate-200/60 flex flex-wrap gap-1">
              <button
                type="button"
                onClick={() => setPranotaSubMode('search')}
                className={`px-4 py-2 text-xs font-bold uppercase tracking-wider flex items-center gap-2 rounded-lg transition-all cursor-pointer ${
                  pranotaSubMode === 'search'
                    ? 'bg-indigo-600 text-white shadow-sm'
                    : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50'
                }`}
              >
                <Search className="w-4 h-4" />
                Cari &amp; Kelola Draft Pranota (Outstanding)
              </button>
              <button
                type="button"
                onClick={() => {
                  setPranotaSubMode('create');
                }}
                className={`px-4 py-2 text-xs font-bold uppercase tracking-wider flex items-center gap-2 rounded-lg transition-all cursor-pointer ${
                  pranotaSubMode === 'create'
                    ? 'bg-indigo-600 text-white shadow-sm'
                    : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50'
                }`}
              >
                <PlusCircle className="w-4 h-4" />
                Buat Draft Pranota Baru (Manual Entry)
              </button>
            </div>

            {/* VENDOR & GROUP SELECTION CARD (DYNAMIC BASED ON SUB-MODE) */}
            {pranotaSubMode === 'search' ? (
              <div className="bg-white p-5 rounded-2xl border border-slate-150 shadow-sm space-y-4">
                <div className="flex items-center gap-3 border-b border-slate-100 pb-3.5">
                  <div className="w-10 h-10 rounded-full bg-indigo-50 border border-indigo-150 flex items-center justify-center shrink-0">
                    <Search className="w-5 h-5 text-indigo-600" />
                  </div>
                  <div>
                    <h3 className="text-sm font-extrabold text-slate-800 uppercase tracking-wider">CARI &amp; KELOLA DRAFT PRANOTA</h3>
                    <p className="text-[10px] text-slate-400 mt-0.5">Pilih Vendor, Tgl Pencarian (Optional) dan cari No. Pranota Vendor yang sudah ada/di-impor.</p>
                  </div>
                </div>
                
                <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
                  <div>
                    <label className="block text-xs font-semibold text-slate-500 mb-1">Vendor</label>
                    <select
                      value={selectedVendorCustomer}
                      onChange={(e) => {
                        setSelectedVendorCustomer(e.target.value);
                        setSelectedVendorPranotaNo(''); // reset group when customer changes
                      }}
                      className="w-full text-xs font-medium border border-slate-200 rounded-xl px-3 py-2 bg-slate-50 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                      <option value="">-- Semua Vendor --</option>
                      {state.customers.map(c => (
                        <option key={c.id_customer} value={c.id_customer}>{c.nama_customer}</option>
                      ))}
                    </select>
                  </div>

                  <div>
                    <label className="block text-xs font-semibold text-slate-500 mb-1">Pilih Range Tgl (Awal)</label>
                    <input
                      type="date"
                      value={pranotaSearchStartDate}
                      onChange={(e) => setPranotaSearchStartDate(e.target.value)}
                      className="w-full text-xs font-mono border border-slate-200 rounded-xl px-3 py-2 bg-slate-50 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    />
                  </div>

                  <div>
                    <label className="block text-xs font-semibold text-slate-500 mb-1">Pilih Range Tgl (Akhir)</label>
                    <input
                      type="date"
                      value={pranotaSearchEndDate}
                      onChange={(e) => setPranotaSearchEndDate(e.target.value)}
                      className="w-full text-xs font-mono border border-slate-200 rounded-xl px-3 py-2 bg-slate-50 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    />
                  </div>

                  <div>
                    <label className="block text-xs font-semibold text-slate-500 mb-1">Cari No. Pranota</label>
                    <SearchableCombobox
                      id="draft-pranota-vendor-combobox"
                      value={selectedVendorPranotaNo}
                      onChange={(val) => {
                        setSelectedVendorPranotaNo(val);
                        if (val) {
                          const matchedPeriod = allPeriods.find(p => p.nomor_pranota === val);
                          if (matchedPeriod) {
                            const sObj = state.sewas.find(s => s.id_sewa === matchedPeriod.id_sewa);
                            if (sObj?.id_customer) {
                              setSelectedVendorCustomer(sObj.id_customer);
                            }
                            if (matchedPeriod.tanggal_pranota) {
                              setSelectedVendorPranotaDate(matchedPeriod.tanggal_pranota);
                            }
                          }
                        }
                      }}
                      options={existingDraftVendorPranotas}
                      placeholder="Ketik atau pilih No. Pranota..."
                      inputClassName="bg-slate-50 text-slate-800 focus:outline-none"
                    />
                  </div>
                </div>
              </div>
            ) : (
              <div className="bg-white p-5 rounded-2xl border border-slate-150 shadow-sm space-y-4">
                <div className="flex items-center gap-3 border-b border-slate-100 pb-3.5">
                  <div className="w-10 h-10 rounded-full bg-indigo-50 border border-indigo-150 flex items-center justify-center shrink-0">
                    <Plus className="w-5 h-5 text-indigo-600" />
                  </div>
                  <div>
                    <h3 className="text-sm font-extrabold text-slate-800 uppercase tracking-wider">BUAT DRAFT PRANOTA VENDOR BARU (MANUAL)</h3>
                    <p className="text-[10px] text-slate-400 mt-0.5">Masukkan Nomor Pranota baru secara manual. Kolom ini bebas hambatan (tanpa loading / autocomplete) sehingga sangat cepat!</p>
                  </div>
                </div>
                
                <div className="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                  <div>
                    <label className="block text-xs font-semibold text-slate-500 mb-1">Vendor <span className="text-rose-500">*</span></label>
                    <select
                      value={newPranotaVendorId}
                      onChange={(e) => setNewPranotaVendorId(e.target.value)}
                      className="w-full text-xs font-medium border border-slate-200 rounded-xl px-3 py-2 bg-slate-50 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                      <option value="">-- Pilih Vendor --</option>
                      {state.customers.map(c => (
                        <option key={c.id_customer} value={c.id_customer}>{c.nama_customer}</option>
                      ))}
                    </select>
                  </div>

                  <div>
                    <label className="block text-xs font-semibold text-slate-500 mb-1">No. Pranota Baru <span className="text-rose-500">*</span></label>
                    <input
                      type="text"
                      value={newPranotaNo}
                      onChange={(e) => setNewPranotaNo(e.target.value)}
                      placeholder="Contoh: PRANOTA-TM-2026-001"
                      className="w-full text-xs font-mono font-semibold border border-slate-200 rounded-xl px-3 py-2 bg-slate-50 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    />
                  </div>

                  <div>
                    <label className="block text-xs font-semibold text-slate-500 mb-1">Tgl. Pranota</label>
                    <FormDateInput
                      value={newPranotaDate}
                      onChange={(val) => setNewPranotaDate(val)}
                      className="w-full text-xs font-mono border border-slate-200 rounded-xl px-3 py-2 bg-slate-50 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    />
                  </div>

                  <div>
                    <button
                      type="button"
                      onClick={() => {
                        if (!newPranotaVendorId) {
                          triggerNoti('error', 'Silakan pilih Vendor terlebih dahulu.');
                          return;
                        }
                        if (!newPranotaNo.trim()) {
                          triggerNoti('error', 'Silakan isi No. Pranota baru.');
                          return;
                        }
                        
                        const pranotaClean = newPranotaNo.trim();
                        
                        // Validasi No. Pranota tidak boleh sama dengan No. Pembayaran
                        const isSameAsPayment = allPeriods.some(p => p.nomor_bayar && p.nomor_bayar.toLowerCase().trim() === pranotaClean.toLowerCase());
                        if (isSameAsPayment) {
                          triggerNoti('error', `Nomor Pranota tidak boleh sama dengan Nomor Pembayaran yang sudah ada (${pranotaClean})!`);
                          return;
                        }

                        const pranotaDateClean = newPranotaDate || utcTime.split('T')[0];

                        // Set active workspace to this new pranota group
                        setSelectedVendorCustomer(newPranotaVendorId);
                        setSelectedVendorPranotaNo(pranotaClean);
                        setSelectedVendorPranotaDate(pranotaDateClean);

                        // Reset fields
                        setNewPranotaNo('');
                        
                        // Switch back to search/manage view to immediately show workspace!
                        // Keep on create/entry submode as requested by the user
                        // setPranotaSubMode('search');
                        triggerNoti('sukses', `Draft Pranota "${pranotaClean}" berhasil dibuat! Silakan pilih tagihan di sebelah kanan untuk ditambahkan ke pranota.`);
                      }}
                      className="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm flex items-center justify-center gap-1.5 cursor-pointer h-[38px]"
                    >
                      <Plus className="w-4 h-4" />
                      Buat &amp; Mulai Susun
                    </button>
                  </div>
                </div>
              </div>
            )}

            {/* TWO PANELS WORKSPACE */}
            <div className="grid grid-cols-1 lg:grid-cols-12 gap-6">
              
              {/* LEFT PANEL: TAGIHANS IN SELECTED PRANOTA */}
              <div className="lg:col-span-7 bg-white p-5 rounded-2xl border border-slate-100 shadow-sm space-y-4">
                <div className="flex items-center justify-between border-b border-slate-100 pb-3">
                  <div className="flex items-center gap-2">
                    <CheckCircle2 className="w-5 h-5 text-indigo-600 shrink-0" />
                    <div>
                      <h4 className="text-xs font-extrabold text-slate-800 uppercase tracking-wider">
                        Detail Tagihan dalam Pranota Ini
                      </h4>
                      <p className="text-[10px] text-slate-400 mt-0.5">
                        Tagihan yang dikelompokkan ke dalam No. Pranota <span className="font-mono font-bold text-slate-700">{selectedVendorPranotaNo || '-'}</span>
                      </p>
                    </div>
                  </div>
                  <div className="flex items-center gap-2">
                    <span className="text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 px-2 py-1 rounded-md">
                      {selectedGroupPeriods.length} Tagihan
                    </span>
                    {selectedGroupPeriods.length > 0 && (
                      <button
                        type="button"
                        onClick={() => {
                          if (confirm('Apakah Anda yakin ingin melepas semua tagihan dari pranota ini sekaligus?')) {
                            const allIds = selectedGroupPeriods.map(p => p.id_tagihan);
                            handleDetachPranota(allIds);
                            triggerNoti('sukses', 'Semua tagihan berhasil dilepas dari pranota.');
                          }
                        }}
                        className="text-[10px] font-bold text-rose-600 hover:text-white hover:bg-rose-600 border border-rose-200 px-2 py-1 rounded-md transition-all cursor-pointer bg-white"
                        title="Lepas semua tagihan dari pranota ini sekaligus"
                      >
                        ✕ Lepas Semua
                      </button>
                    )}
                  </div>
                </div>

                {!selectedVendorPranotaNo ? (
                  <div className="space-y-6">
                    <div className="py-8 px-4 text-center bg-slate-50 border border-dashed border-slate-200 rounded-2xl">
                      <Sparkles className="w-6 h-6 text-indigo-500 mx-auto mb-2" />
                      <p className="text-xs font-bold text-slate-700">Silakan masukkan atau pilih No. Pranota di atas untuk mulai menyusun.</p>
                      <p className="text-[10px] text-slate-400 mt-1">Anda dapat memuat draf yang ada dari dashboard di bawah ini.</p>
                    </div>

                    <div className="border border-slate-150 rounded-2xl p-4 bg-white shadow-3xs space-y-3">
                      <div className="flex items-center gap-2 border-b border-slate-100 pb-2.5">
                        <FileText className="w-4 h-4 text-indigo-600" />
                        <h4 className="text-xs font-extrabold text-slate-800 uppercase tracking-wider">
                          Daftar Draft Pranota Aktif / Outstanding Terbaru (Maks. 20)
                        </h4>
                      </div>

                      {outstandingDraftPranotas.length === 0 ? (
                        <div className="text-center py-6 text-xs text-slate-400 italic">
                          Tidak ada draf pranota aktif saat ini. Silakan buat baru!
                        </div>
                      ) : (
                        <div className="overflow-x-auto border border-slate-100 rounded-xl">
                          <table className="w-full text-left border-collapse text-[10px] font-sans">
                            <thead>
                              <tr className="bg-slate-50 text-slate-500 font-bold border-b border-slate-200">
                                <th className="p-2 py-2.5 font-mono">No. Pranota / Proforma</th>
                                <th className="p-2 py-2.5">Nama Vendor/Mitra</th>
                                <th className="p-2 py-2.5 text-center">Jumlah Tagihan</th>
                                <th className="p-2 py-2.5 text-right">Total Tagihan</th>
                                <th className="p-2 py-2.5 text-center">Aksi</th>
                              </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100">
                              {outstandingDraftPranotas.map((g) => (
                                <tr key={g.nomor_pranota} className="hover:bg-slate-50/60 transition-colors">
                                  <td className="p-2 py-2.5 font-mono font-bold text-slate-800">{g.nomor_pranota}</td>
                                  <td className="p-2 py-2.5 font-semibold text-slate-600">{g.customerName}</td>
                                  <td className="p-2 py-2.5 text-center font-bold text-indigo-700 bg-indigo-50/20">{g.count} Tagihan</td>
                                  <td className="p-2 py-2.5 text-right font-mono font-bold text-slate-800">{formatRupiah(g.totalAktual)}</td>
                                  <td className="p-2 py-2.5 text-center">
                                    <div className="flex items-center justify-center gap-2">
                                      <button
                                        onClick={() => {
                                          setSelectedVendorPranotaNo(g.nomor_pranota);
                                          setSelectedVendorPranotaDate(g.tanggal_pranota || '');
                                          const matchingSewa = allPeriods.find(p => p.nomor_pranota === g.nomor_pranota);
                                          if (matchingSewa) {
                                            const sObj = state.sewas.find(s => s.id_sewa === matchingSewa.id_sewa);
                                            if (sObj?.id_customer) {
                                              setSelectedVendorCustomer(sObj.id_customer);
                                            }
                                          }
                                        }}
                                        className="px-2 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-[9px] font-extrabold cursor-pointer transition-colors shadow-3xs shrink-0"
                                      >
                                        Buka &amp; Kelola
                                      </button>
                                      <button
                                        onClick={() => {
                                          const pranotaNoClean = g.nomor_pranota.trim();
                                          const isSameAsPayment = allPeriods.some(p => p.nomor_bayar && p.nomor_bayar.toLowerCase().trim() === pranotaNoClean.toLowerCase());
                                          if (isSameAsPayment) {
                                            triggerNoti('error', `Nomor Pranota tidak boleh sama dengan Nomor Pembayaran yang sudah ada (${pranotaNoClean})!`);
                                            return;
                                          }

                                          const periodIds = allPeriods
                                            .filter(p => p.nomor_pranota === g.nomor_pranota)
                                            .map(p => p.id_tagihan);
                                          
                                          if (periodIds.length === 0) {
                                            triggerNoti('error', 'Grup Pranota ini kosong!');
                                            return;
                                          }

                                          const updates = {
                                            status_bayar: 'Pranota',
                                            nomor_pranota: g.nomor_pranota,
                                            tanggal_pranota: g.tanggal_pranota || utcTime.split('T')[0]
                                          };
                                          handleBulkUpdate(periodIds, updates, 'Pranota');
                                          triggerNoti('sukses', `Sukses! Status Pranota "${g.nomor_pranota}" berhasil diperbarui menjadi "Pranota" (sejajar menu entry)!`);
                                        }}
                                        className="px-2 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-[9px] font-extrabold cursor-pointer transition-colors shadow-3xs shrink-0"
                                      >
                                        Set Status Pranota
                                      </button>
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
                  <div className="space-y-4">
                    {/* TABLE OF MATCHED PERIODS */}
                    <div className="overflow-x-auto border border-slate-150 rounded-xl bg-white shadow-3xs">
                      <table className="w-full text-left border-collapse text-[10px] font-sans">
                        <thead>
                          <tr className="bg-slate-50 text-slate-500 font-bold border-b border-slate-200">
                            <th className="p-2.5 py-3">No. Tagihan</th>
                            <th className="p-2.5 py-3">Tgl. Tagihan</th>
                            <th className="p-2.5 py-3 text-right">Estimasi</th>
                            <th className="p-2.5 py-3 text-right">Real</th>
                            <th className="p-2.5 py-3 text-right">Selisih</th>
                            <th className="p-2.5 py-3 text-left">Keterangan</th>
                            <th className="p-2.5 py-3 text-center">Aksi</th>
                          </tr>
                        </thead>
                        <tbody className="divide-y divide-slate-100">
                          {leftGroupedInvoices.length === 0 ? (
                            <tr>
                              <td colSpan={7} className="p-12 text-center text-slate-400 italic">
                                Grup Pranota ini masih kosong. Silakan cari dan tambahkan "Tagihan" dari panel kanan!
                              </td>
                            </tr>
                          ) : (
                            leftGroupedInvoices.map(g => {
                              return (
                                <tr key={g.nomor_invoice_grup} className="hover:bg-slate-50/50 transition-colors">
                                  <td className="p-2.5 font-bold text-slate-800">
                                    {g.nomor_invoice_grup}
                                  </td>
                                  <td className="p-2.5 font-mono text-slate-500">
                                    {formatIndoDate(g.tanggal_tagihan)}
                                  </td>
                                  <td className="p-2.5 text-right font-mono text-slate-600">
                                    {formatRupiah(g.total_estimasi)}
                                  </td>
                                  <td className="p-1 text-right bg-amber-50/30">
                                    <FastNumberInput
                                      value={g.total_aktual === g.total_estimasi && selectedGroupPeriods.filter(p => g.id_tagihan_list.includes(p.id_tagihan)).every(p => p.jumlah_tagihan_override === null) ? null : g.total_aktual}
                                      placeholder={String(g.total_estimasi)}
                                      onChange={(val) => {
                                        handleUpdateGroupFieldValue(g.id_tagihan_list, 'jumlah_tagihan_override', val);
                                      }}
                                      className="w-24 text-[10px] font-mono border border-slate-200 rounded px-1.5 py-1 text-right bg-white focus:ring-1 focus:ring-indigo-500"
                                    />
                                  </td>
                                  <td className="p-2.5 text-right">
                                    {g.selisih === 0 ? (
                                      <span className="text-[9px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded-sm border border-emerald-100">Pas</span>
                                    ) : (
                                      <span className={`text-[9px] font-mono font-bold px-1.5 py-0.5 rounded-sm border ${g.selisih > 0 ? 'text-amber-700 bg-amber-50 border-amber-200' : 'text-rose-700 bg-rose-50 border-rose-200'}`}>
                                        {g.selisih > 0 ? '+' : ''}{formatRupiah(g.selisih)}
                                      </span>
                                    )}
                                  </td>
                                  <td className="p-1 min-w-[150px]">
                                    <FastTextInput
                                      value={g.keterangan_selisih}
                                      placeholder={g.selisih !== 0 ? "Wajib diisi..." : "Keterangan..."}
                                      onChange={(val) => handleUpdateGroupFieldValue(g.id_tagihan_list, 'keterangan_selisih', val)}
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
                                      onClick={() => handleDetachPranota(g.id_tagihan_list)}
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
                          <span className="text-xs font-mono font-bold text-slate-700">{formatRupiah(totalEstimasiGroup)}</span>
                        </div>
                        <div className="bg-white p-2 rounded-lg border border-slate-100 shadow-3xs">
                          <span className="text-[8px] font-bold text-slate-400 block uppercase">Aktual / Riil</span>
                          <span className="text-xs font-mono font-bold text-slate-800">{formatRupiah(totalAktualGroup)}</span>
                        </div>
                        <div className="bg-white p-2 rounded-lg border border-slate-100 shadow-3xs">
                          <span className="text-[8px] font-bold text-indigo-500 block uppercase">PPN (11%)</span>
                          <span className="text-xs font-mono font-bold text-indigo-700">+{formatRupiah(totalPPNGroup)}</span>
                        </div>
                        <div className="bg-white p-2 rounded-lg border border-slate-100 shadow-3xs">
                          <span className="text-[8px] font-bold text-rose-500 block uppercase">PPh (2%)</span>
                          <span className="text-xs font-mono font-bold text-rose-600">-{formatRupiah(totalPPhGroup)}</span>
                        </div>
                      </div>

                      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2 border-t border-slate-200">
                        <div>
                          <span className="text-[9px] font-extrabold text-slate-400 uppercase tracking-wider block">Grand Net Total</span>
                          <span className="text-sm font-extrabold text-emerald-800 font-mono">{formatRupiah(grandNetTotalGroup)}</span>
                        </div>
                        
                        <div className="flex items-center gap-2">
                          <button
                            type="button"
                            onClick={() => {
                              setSelectedVendorPranotaNo('');
                              setSelectedVendorPranotaDate('');
                            }}
                            className="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-all cursor-pointer"
                          >
                            Batal
                          </button>
                          <button
                            type="button"
                            onClick={() => {
                              if (!selectedVendorPranotaNo.trim()) {
                                triggerNoti('error', 'Silakan isi No. Pranota / Proforma terlebih dahulu.');
                                  return;
                              }
                              
                              const pranotaNo = selectedVendorPranotaNo.trim();
                              
                              // Validasi No. Pranota tidak boleh sama dengan No. Pembayaran
                              const isSameAsPayment = allPeriods.some(p => p.nomor_bayar && p.nomor_bayar.toLowerCase().trim() === pranotaNo.toLowerCase());
                              if (isSameAsPayment) {
                                triggerNoti('error', `Nomor Pranota tidak boleh sama dengan Nomor Pembayaran yang sudah ada (${pranotaNo})!`);
                                return;
                              }

                              const periodIds = selectedGroupPeriods.map(p => p.id_tagihan);
                              if (periodIds.length === 0) {
                                triggerNoti('error', 'Grup pranota ini kosong! Harap tambah minimal 1 tagihan dari panel kanan.');
                                return;
                              }

                              const updates = {
                                status_bayar: 'Pranota',
                                nomor_pranota: selectedVendorPranotaNo,
                                tanggal_pranota: selectedVendorPranotaDate || utcTime.split('T')[0]
                              };
                              handleBulkUpdate(periodIds, updates, 'Pranota');
                              triggerNoti('sukses', `Sukses! Draft Pembayaran "${selectedVendorPranotaNo}" telah disimpan. Silakan lanjut menginput di Tab 2 ini.`);
                              setSelectedVendorPranotaNo('');
                              setSelectedVendorPranotaDate('');
                            }}
                            className="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 cursor-pointer font-extrabold"
                          >
                            <CheckCircle2 className="w-4 h-4" />
                            <span>Simpan Draft Pembayaran</span>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                )}
              </div>

              {/* RIGHT PANEL: AVAILABLE LOOSE TAGIHANS (FROM TAB 1) */}
              <div className="lg:col-span-5 bg-white p-5 rounded-2xl border border-slate-100 shadow-sm space-y-4">
                <div className="flex items-center justify-between border-b border-slate-100 pb-3">
                  <div className="flex items-center gap-2">
                    <PlusCircle className="w-5 h-5 text-indigo-600 shrink-0" />
                    <div>
                      <h4 className="text-xs font-extrabold text-slate-800 uppercase tracking-wider">
                        Daftar Tagihan Tersedia (Hasil Tab 1)
                      </h4>
                      <p className="text-[10px] text-slate-400 mt-0.5">
                        Tagihan vendor outstanding yang belum memiliki Pranota
                      </p>
                    </div>
                  </div>
                  <span className="text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 px-2 py-1 rounded-md">
                    {looseTagihanGroups.length} Tagihan
                  </span>
                </div>

                {/* Search bar inside Right Panel */}
                <div className="relative">
                  <Search className="w-4 h-4 absolute left-3 top-2.5 text-slate-400" />
                  <input
                    type="text"
                    placeholder="Cari No. Tagihan Vendor..."
                    value={searchTagihanNo}
                    onChange={(e) => setSearchTagihanNo(e.target.value)}
                    className="w-full text-xs border border-slate-200 rounded-xl pl-9 pr-3 py-2 bg-slate-50 text-slate-800 placeholder-slate-400 focus:outline-hidden focus:border-indigo-400 focus:bg-white transition-all font-medium"
                  />
                </div>

                {/* TABLE OF LOOSE TAGIHANS */}
                <div className="overflow-y-auto max-h-[500px] border border-slate-150 rounded-xl bg-white shadow-3xs">
                  <table className="w-full text-left border-collapse text-[10px] font-sans">
                    <thead>
                      <tr className="bg-slate-50 text-slate-500 font-bold border-b border-slate-200">
                        <th className="p-2 py-2.5">No. Tagihan</th>
                        <th className="p-2 py-2.5">Tgl. Tagihan</th>
                        <th className="p-2 py-2.5 text-right">Estimasi</th>
                        <th className="p-2 py-2.5 text-center">Aksi</th>
                      </tr>
                    </thead>
                    <tbody className="divide-y divide-slate-100">
                      {looseTagihanGroups.length === 0 ? (
                        <tr>
                          <td colSpan={4} className="p-10 text-center text-slate-400 italic">
                            Tidak ada tagihan tersedia yang cocok dengan pencarian / filter vendor saat ini.
                          </td>
                        </tr>
                      ) : (
                        looseTagihanGroups.map(g => {
                          const nominalEstimasi = g.total_estimasi;

                          return (
                            <tr key={g.nomor_invoice_grup} className="hover:bg-slate-50/60 transition-colors">
                              <td className="p-2 py-2.5">
                                <div className="font-bold text-slate-800">{g.nomor_invoice_grup}</div>
                              </td>
                              <td className="p-2 py-2.5 font-mono text-slate-500">
                                {formatIndoDate(g.tanggal_tagihan)}
                              </td>
                              <td className="p-2 py-2.5 text-right font-mono font-bold text-slate-700">
                                {formatRupiah(nominalEstimasi)}
                              </td>
                              <td className="p-2 py-2.5 text-center">
                                <button
                                  type="button"
                                  onClick={() => handleAttachPranota(g.id_tagihan_list)}
                                  disabled={!selectedVendorPranotaNo}
                                  className={`px-2 py-1 rounded text-[9px] font-bold cursor-pointer transition-colors shadow-3xs flex items-center gap-0.5 mx-auto ${selectedVendorPranotaNo ? 'bg-indigo-600 hover:bg-indigo-700 text-white' : 'bg-slate-100 text-slate-400 border border-slate-200 cursor-not-allowed'}`}
                                  title={!selectedVendorPranotaNo ? 'Masukkan No. Pranota terlebih dahulu' : 'Tambahkan ke Pranota'}
                                >
                                  <Plus className="w-3 h-3" />
                                  <span>Tambah</span>
                                </button>
                              </td>
                            </tr>
                          );
                        })
                      )}
                    </tbody>
                  </table>
                </div>
              </div>

            </div>
          </div>
        );

              })()}

      {/* VIEW TAB 3: PELUNASAN & ADJUSTMENT KOLEKTIF PER NOTA (6.png) */}
      {activeViewTab === 'collective' && (
        <PaymentWorkspace
          state={state}
          onStateChange={onStateChange}
          allPeriods={allPeriods}
          selectedVendorCustomer={selectedVendorCustomer}
          setSelectedVendorCustomer={setSelectedVendorCustomer}
          selectedPaymentNo={selectedPaymentNo}
          setSelectedPaymentNo={setSelectedPaymentNo}
          selectedPaymentDate={selectedPaymentDate}
          setSelectedPaymentDate={setSelectedPaymentDate}
          searchPranotaNo={searchPranotaNo}
          setSearchPranotaNo={setSearchPranotaNo}
          getCustomerName={getCustomerName}
          formatRupiah={formatRupiah}
          formatIndoDate={formatIndoDate}
          utcTime={utcTime}
          triggerNoti={triggerNoti}
          handleBulkUpdate={handleBulkUpdate}
          existingDraftPayments={existingDraftPayments}
        />
      )}

      {false && (() => {
        // Collect distinct invoices summary
        const invoicesSummaryList = distinctInvoiceNumbers.map(no => {
          const matchedPeriods = allPeriods.filter(p => p.nomor_invoice_grup === no);
          const totalEstimasi = matchedPeriods.reduce((sum, p) => sum + p.jumlah_tagihan, 0);
          const totalAktual = matchedPeriods.reduce((sum, p) => {
            const tagihan = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : p.jumlah_tagihan;
            return sum + tagihan;
          }, 0);
          const totalRekBayar = matchedPeriods.reduce((sum, p) => {
            const tagihan = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : p.jumlah_tagihan;
            return sum + Math.min(tagihan, p.jumlah_tagihan);
          }, 0);
          const totalPPN = matchedPeriods.reduce((sum, p) => {
            const tagihan = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : p.jumlah_tagihan;
            const ppn = p.ppn !== null && p.ppn !== undefined ? p.ppn : Math.round(tagihan * 0.11);
            return sum + ppn;
          }, 0);
          const totalPPh = matchedPeriods.reduce((sum, p) => {
            const tagihan = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : p.jumlah_tagihan;
            const pph = p.pph !== null && p.pph !== undefined ? p.pph : Math.round(tagihan * 0.02);
            return sum + pph;
          }, 0);

          const customerId = matchedPeriods.length > 0
            ? state.sewas.find(s => s.id_sewa === matchedPeriods[0].id_sewa)?.id_customer || ''
            : '';

          const groupInvoice = state.invoices.find(i => i.nomor_invoice === no);
          const adjustmentBiaya = groupInvoice?.adjustment_biaya ?? 0;
          const adjustmentKeterangan = groupInvoice?.adjustment_keterangan ?? '';
          const statusPembayaran = (groupInvoice?.status_pembayaran || (matchedPeriods.length > 0 && matchedPeriods.every(p => p.status_bayar === 'Lunas') ? 'Lunas' : 'Belum Bayar')) as 'Belum Bayar' | 'Lunas';
          const firstPeriod = matchedPeriods[0];
          const buktiBayarMapped = firstPeriod?.nomor_bayar || '';
          const tglBayarMapped = firstPeriod?.tanggal_bayar || '';

          return {
            nomor_invoice: no,
            customerId,
            totalEstimasi,
            totalAktual,
            totalRekBayar,
            totalPPN,
            totalPPh,
            adjustmentBiaya,
            adjustmentKeterangan,
            statusPembayaran,
            buktiBayarMapped,
            tglBayarMapped,
            list_id_tagihan: matchedPeriods.map(p => p.id_tagihan)
          };
        });

        // Filter collective list
        const filteredCollectiveList = invoicesSummaryList.filter(item => {
          if (collectiveSearch.trim()) {
            const q = collectiveSearch.toLowerCase().trim();
            const matchesNota = item.nomor_invoice.toLowerCase().includes(q);
            const matchesBuktiBayar = item.buktiBayarMapped && item.buktiBayarMapped.toLowerCase().includes(q);
            if (!matchesNota && !matchesBuktiBayar) {
              return false;
            }
          }
          if (collectiveCustFilter && item.customerId !== collectiveCustFilter) {
            return false;
          }
          if (collectiveStatusFilter !== 'Semua') {
            if (collectiveStatusFilter === 'Lunas' && item.statusPembayaran !== 'Lunas') return false;
            if (collectiveStatusFilter === 'Belum Bayar' && item.statusPembayaran !== 'Belum Bayar') return false;
          }
          return true;
        });

        // KPI aggregates
        const kpiTotalInvoicesCount = filteredCollectiveList.length;
        const kpiTotalLunasCount = filteredCollectiveList.filter(i => i.statusPembayaran === 'Lunas').length;
        const kpiTotalBelumBayarCount = filteredCollectiveList.filter(i => i.statusPembayaran === 'Belum Bayar').length;
        const kpiTotalAktualSum = filteredCollectiveList.reduce((sum, i) => sum + i.totalAktual, 0);
        const kpiTotalRekBayarSum = filteredCollectiveList.reduce((sum, i) => sum + i.totalRekBayar, 0);
        const kpiTotalPPNSum = filteredCollectiveList.reduce((sum, i) => sum + i.totalPPN, 0);
        const kpiTotalPPhSum = filteredCollectiveList.reduce((sum, i) => sum + i.totalPPh, 0);
        const kpiTotalAdjSum = filteredCollectiveList.reduce((sum, i) => sum + i.adjustmentBiaya, 0);
        const kpiGrandNetSum = kpiTotalRekBayarSum + kpiTotalPPNSum - kpiTotalPPhSum + kpiTotalAdjSum;

        return (
          <div className="space-y-6" id="collective-operations-view">
            {/* Title description bar */}
            <div className="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs">
              <h3 className="text-sm font-extrabold text-slate-800 flex items-center gap-2">
                <Coins className="w-5 h-5 text-amber-600 shrink-0" />
                <span>3. PELUNASAN, BUKTI BAYAR &amp; ADJUSTMENT KOLEKTIF PER NOTA (6.png)</span>
              </h3>
              <p className="text-xs text-slate-500 mt-1">
                Menu tersentralisasi untuk verifikasi, pengisian nomor bukti bayar, tanggal lunas, serta adjustment biaya administrasi atau diskon secara praktis untuk seluruh nota sekaligus, tanpa perlu mengedit rincian kontainer satu per satu.
              </p>
            </div>

            {/* Impor Pembayaran Masal Panel */}
            {importPaymentOpen && (
              <div className="bg-gradient-to-br from-amber-50/70 to-orange-50/40 p-6 rounded-3xl border border-amber-200/80 shadow-xs space-y-4 animate-in fade-in duration-300">
                <div className="flex items-center justify-between">
                  <div className="flex items-center gap-2">
                    <Sparkles className="w-5 h-5 text-amber-600 shrink-0" />
                    <h4 className="text-xs font-black text-amber-900 uppercase tracking-widest">Alat Impor Pelunasan &amp; Bukti Bayar Kolektif</h4>
                  </div>
                  <button
                    onClick={() => {
                      setImportPaymentOpen(false);
                      setIsImportProcessed(false);
                      setImportPaymentPreview([]);
                    }}
                    className="text-xs text-amber-950 font-black hover:opacity-75 cursor-pointer bg-white px-2.5 py-1 rounded-lg border border-amber-200/50"
                  >
                    Tutup [x]
                  </button>
                </div>

                <div className="text-xs text-slate-650 leading-relaxed bg-white p-4 rounded-2xl border border-amber-100 shadow-3xs space-y-2">
                  <span className="font-extrabold text-slate-800 text-[13px] block mb-1">💡 Cara Cepat Melunasi Banyak Nota Sekaligus:</span>
                  <ul className="list-disc pl-5 space-y-1">
                    <li>Copypaste langsung dari Excel atau ketik dengan format per baris: <strong className="font-mono text-amber-950 bg-amber-50 px-1 py-0.5 rounded text-[11px]">No Bukti Bayar ; Tanggal Bayar ; Nomor Nota</strong></li>
                    <li>Mendukung pemisah kolom berupa: <strong>Titik-Koma (;)</strong>, <strong>Koma (,)</strong>, maupun <strong>Karakter Tab (jika disalin dari tabel excel)</strong>!</li>
                    <li>Format Tanggal Bayar: <strong className="font-mono bg-slate-100 px-1 py-0.5 rounded">dd/mm/yyyy</strong> atau <strong className="font-mono bg-slate-100 px-1 py-0.5 rounded">yyyy-mm-dd</strong>. Jika dikosongkan, otomatis memakai tanggal hari ini ({formatEntryDate(utcTime.split('T')[0])}).</li>
                    <li>Satu bukti bayar dapat diaplikasikan ke puluhan nota dengan menuliskan bukti bayar yang sama pada baris-baris nota yang bersangkutan.</li>
                  </ul>
                  <div className="pt-2 flex items-center gap-2 flex-wrap">
                    <span className="text-[10px] font-bold text-amber-800 bg-amber-100 px-2 py-0.5 rounded">Salin data simulasi untuk mencoba:</span>
                    <button
                      onClick={() => {
                        const sampleText = `${distinctInvoiceNumbers[0] ? `TRF-${new Date().getFullYear()}001; ${formatEntryDate(utcTime.split('T')[0])}; ${distinctInvoiceNumbers[0]}` : 'EBK2506002; 12/06/2026; NOTA-CONTOH-01'}\n${distinctInvoiceNumbers[1] ? `TRF-${new Date().getFullYear()}001; ${formatEntryDate(utcTime.split('T')[0])}; ${distinctInvoiceNumbers[1]}` : 'EBK2506002; 12/06/2026; NOTA-CONTOH-02'}`;
                        setImportPaymentText(sampleText);
                        triggerNoti('sukses', 'Contoh teks template telah disiapkan di area input!');
                      }}
                      className="text-[10px] bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 px-2.5 py-1 rounded-lg font-bold transition-all cursor-pointer shadow-3xs"
                    >
                      Tempel Teks Contoh
                    </button>
                  </div>
                </div>

                <div>
                  <label className="block text-[11px] font-bold text-slate-700 mb-1">Area Tempat Tempel / Ketik Data Pembayaran Kolektif:</label>
                  <textarea
                    rows={6}
                    placeholder={`e.g.\nEBK2506002; 12/06/2026; INV-Grup-A\nEBK2506002; 12/06/2026; INV-Grup-B`}
                    value={importPaymentText}
                    onChange={(e) => setImportPaymentText(e.target.value)}
                    className="w-full text-xs font-mono p-4 border border-slate-200 rounded-2xl bg-white shadow-xs focus:ring-2 focus:ring-amber-500 focus:border-amber-500 focus:outline-none leading-relaxed"
                  />
                </div>

                <div className="flex items-center gap-2">
                  <button
                    onClick={handleProcessImportPayment}
                    className="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-extrabold shadow-sm transition-all flex items-center gap-2 cursor-pointer"
                  >
                    <CheckCircle2 className="w-4 h-4 text-emerald-500 shrink-0" />
                    <span>Verifikasi Data &amp; Tampilkan Pratinjau</span>
                  </button>
                  {isImportProcessed && (
                    <button
                      onClick={() => {
                        setImportPaymentText('');
                        setImportPaymentPreview([]);
                        setIsImportProcessed(false);
                      }}
                      className="px-4 py-2 bg-white hover:bg-slate-55 text-slate-700 border border-slate-200 rounded-xl text-xs font-bold transition-all cursor-pointer"
                    >
                      Bersihkan Hasil
                    </button>
                  )}
                </div>

                {/* Live Preview of match results */}
                {isImportProcessed && (
                  <div className="bg-white border text-left border-slate-200 rounded-2xl shadow-xs overflow-hidden animate-in fade-in slide-in-from-top-1">
                    <div className="p-4 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                      <div>
                        <span className="text-[10px] font-extrabold text-slate-500 uppercase tracking-widest block">PRATINJAU VERIFIKASI COCOK-NOTA</span>
                        <p className="text-xs text-slate-500 mt-0.5">Sistem memeriksa kecocokan nomor nota terhadap database tagihan aktif Anda.</p>
                      </div>
                      <span className="text-xs font-bold text-amber-800 bg-amber-50 px-3 py-1 rounded-lg border border-amber-200 shrink-0">
                        {importPaymentPreview.filter(r => r.isValidNota).length} Nota Cocok / Siap Impor
                      </span>
                    </div>

                    <div className="max-h-80 overflow-y-auto text-xs">
                      <table className="w-full text-left border-collapse">
                        <thead className="bg-slate-100/60 text-[10px] uppercase font-bold text-slate-500 border-b border-slate-200 sticky top-0">
                          <tr>
                            <th className="p-3">Baris</th>
                            <th className="p-3">No Bukti Bayar</th>
                            <th className="p-3">Tanggal Lunas</th>
                            <th className="p-3">Nomor Nota</th>
                            <th className="p-3">Pelanggan/Vendor</th>
                            <th className="p-3 text-right">Grand Total</th>
                            <th className="p-3">Status Pencocokan</th>
                          </tr>
                        </thead>
                        <tbody>
                          {importPaymentPreview.length === 0 ? (
                            <tr>
                              <td colSpan={7} className="p-8 text-center text-slate-400">
                                Tidak ada baris valid yang terdeteksi. Silakan periksa format pemisah kolom.
                              </td>
                            </tr>
                          ) : (
                            importPaymentPreview.map((row) => (
                              <tr key={row.lineNum} className={`border-b last:border-0 hover:bg-slate-50/50 ${row.isValidNota ? 'bg-emerald-50/20 text-slate-900' : 'bg-rose-50/20 text-rose-955'}`}>
                                <td className="p-3 font-mono text-[10px] text-slate-400">{row.lineNum}</td>
                                <td className="p-3 font-mono font-bold text-slate-800">{row.nomorBayar}</td>
                                <td className="p-3 font-mono text-slate-700">{row.tanggalBayar}</td>
                                <td className="p-3 font-mono font-bold text-indigo-900 bg-indigo-50/30 px-2 py-0.5 rounded border border-indigo-100/30 w-fit">{row.nomorNota}</td>
                                <td className="p-3 font-semibold max-w-[150px] truncate" title={row.customerName}>{row.customerName || '-'}</td>
                                <td className="p-3 font-mono text-right font-black text-slate-900">{row.isValidNota ? formatRupiah(row.grandTotal) : '-'}</td>
                                <td className="p-3">
                                  <span className={`inline-flex items-center gap-1.5 text-[11px] font-bold ${row.isValidNota ? 'text-emerald-700' : 'text-rose-600'}`}>
                                    {row.isValidNota ? (
                                      <>
                                        <Check className="w-4 h-4 text-emerald-600 shrink-0" />
                                        <span>✓ Cocok</span>
                                      </>
                                    ) : (
                                      <>
                                        <AlertCircle className="w-4 h-4 text-rose-500 shrink-0" />
                                        <span className="max-w-[250px] truncate block" title={row.textStatus}>{row.textStatus}</span>
                                      </>
                                    )}
                                  </span>
                                </td>
                              </tr>
                            ))
                          )}
                        </tbody>
                      </table>
                    </div>

                    <div className="p-4 bg-slate-50/80 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                      <div className="text-xs text-slate-500 max-w-xl">
                        ⚠️ **Penting**: Baris dengan status kesalahan (<span className="text-rose-600 font-bold">Error/Gagal</span>) akan otomatis **diabaikan** oleh sistem. Silakan klik tombol di kanan untuk mengeksekusi pelunasan bagi seluruh baris yang lolos verifikasi.
                      </div>
                      <button
                        onClick={handleApplyImportPayment}
                        disabled={importPaymentPreview.filter(r => r.isValidNota).length === 0}
                        className={`px-5 py-3 text-white rounded-xl text-xs font-extrabold shadow-sm flex items-center justify-center gap-2 transition-all cursor-pointer shrink-0 ${
                          importPaymentPreview.filter(r => r.isValidNota).length > 0
                            ? 'bg-emerald-600 hover:bg-emerald-700 hover:shadow-md'
                            : 'bg-slate-300 opacity-50 cursor-not-allowed'
                        }`}
                      >
                        <Check className="w-4 h-4 text-white shrink-0" />
                        <span>✓ TERAPKAN PELUNASAN ({importPaymentPreview.filter(r => r.isValidNota).length} NOTA)</span>
                      </button>
                    </div>
                  </div>
                )}
              </div>
            )}

            {/* Filters Area for Collective dashboard */}
            <div className="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label className="block text-xs font-semibold text-slate-500 mb-1.5">Cari No. Bukti Bayar / Nota</label>
                <SearchableCombobox
                  id="tab3-search-combobox"
                  value={collectiveSearch}
                  onChange={(val) => setCollectiveSearch(val)}
                  options={autocompleteTab3}
                  placeholder="Ketik no. bukti bayar, EBK, atau no. nota..."
                  inputClassName="bg-slate-50/50 pl-3"
                />
              </div>

              <div>
                <label className="block text-xs font-semibold text-slate-500 mb-1.5">Filter Pelanggan/Vendor</label>
                <SearchableSelect
                  id="coll-select-cust"
                  placeholder={isSewaIn ? '-- Semua Vendor --' : '-- Semua Customer --'}
                  searchPlaceholder={isSewaIn ? "Ketik nama vendor..." : "Ketik nama customer..."}
                  value={collectiveCustFilter}
                  onChange={(val) => setCollectiveCustFilter(val)}
                  inputClassName="bg-slate-50/50 py-1.5 text-xs h-[34px]"
                  options={[
                    { value: "", label: "-- Semua Pelanggan --" },
                    ...state.customers.map(c => ({
                      value: c.id_customer,
                      label: c.nama_customer
                    }))
                  ]}
                />
              </div>

              <div>
                <label className="block text-xs font-semibold text-slate-500 mb-1.5">Filter Status Bayar</label>
                <select
                  value={collectiveStatusFilter}
                  onChange={(e) => setCollectiveStatusFilter(e.target.value)}
                  className="w-full text-xs border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 text-slate-700 h-[34px]"
                >
                  <option value="Semua">Semua Status</option>
                  <option value="Belum Bayar">Belum Lunas / Belum Bayar</option>
                  <option value="Lunas">✓ Lunas</option>
                </select>
              </div>
            </div>

            {/* KPI Summary Cards */}
            <div className="grid grid-cols-2 md:grid-cols-4 gap-4 bg-slate-100 p-1.5 rounded-3xl">
              <div className="bg-white p-4 rounded-2xl border border-slate-100 flex flex-col justify-between">
                <span className="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Nota Terdaftar (Bermatching)</span>
                <div className="mt-2 text-slate-900 flex items-baseline gap-1.5">
                  <span className="text-2xl font-black">{kpiTotalInvoicesCount}</span>
                  <span className="text-xs text-slate-500">Kolektif</span>
                </div>
                <div className="text-[10px] text-slate-500 mt-1 flex gap-2">
                  <span className="text-emerald-600 font-bold">✓ {kpiTotalLunasCount} Lunas</span>
                  <span className="text-rose-500 font-bold">⚠️ {kpiTotalBelumBayarCount} Pending</span>
                </div>
              </div>

              <div className="bg-white p-4 rounded-2xl border border-slate-100 flex flex-col justify-between">
                <span className="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Subtotal Tagihan (Aktual)</span>
                <span className="text-base font-black text-slate-800 mt-2 block">{formatRupiah(kpiTotalAktualSum)}</span>
                <span className="text-[9px] text-slate-500 block mt-1">Estimasi: {formatRupiah(filteredCollectiveList.reduce((acc, i) => acc + i.totalEstimasi, 0))}</span>
              </div>

              <div className="bg-emerald-50/50 p-4 rounded-2xl border border-emerald-100 flex flex-col justify-between">
                <span className="text-[10px] font-bold text-emerald-800 uppercase tracking-wider block">Total Rek. Bayar Pajak</span>
                <span className="text-base font-black text-emerald-950 mt-2 block">{formatRupiah(kpiTotalRekBayarSum)}</span>
                <span className="text-[9px] text-emerald-600 block mt-1">PPN: {formatRupiah(kpiTotalPPNSum)} | PPh: -{formatRupiah(kpiTotalPPhSum)}</span>
              </div>

              <div className="bg-amber-50 p-4 rounded-2xl border border-amber-100 flex flex-col justify-between">
                <span className="text-[10px] font-bold text-amber-800 uppercase tracking-wider block">Grand Net (Dengan Adjustment)</span>
                <span className="text-base font-black text-amber-950 mt-2 block">{formatRupiah(kpiGrandNetSum)}</span>
                <span className="text-[9px] text-amber-700 block mt-1">Total Adjust: {kpiTotalAdjSum >= 0 ? '+' : ''}{formatRupiah(kpiTotalAdjSum)}</span>
              </div>
            </div>

            {/* Quick Broad Bulk Inputs for selected collective invoices */}
            {selectedCollectiveInvoices.length > 0 && (
              <div className="bg-gradient-to-r from-amber-50/70 to-orange-100/60 border border-amber-200 p-4 rounded-3xl shadow-xs space-y-3">
                <div className="flex flex-wrap items-center justify-between gap-3 border-b border-amber-100 pb-2">
                  <div className="flex items-center gap-2">
                    <span className="w-2.5 h-2.5 rounded-full bg-amber-600 animate-pulse shrink-0"></span>
                    <span className="text-[11px] font-extrabold text-amber-950 tracking-tight uppercase">
                      Aksi Massal Pelunasan Nota ({selectedCollectiveInvoices.length} Nota Terpilih)
                    </span>
                  </div>
                  <button
                    onClick={() => setSelectedCollectiveInvoices([])}
                    className="text-[10px] text-slate-500 hover:text-slate-800 font-medium underline flex items-center gap-1 cursor-pointer"
                  >
                    Batal Centang / Bersihkan Pilihan
                  </button>
                </div>
                
                <div className="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                  <div>
                    <label className="block text-[9px] font-extrabold text-slate-500 mb-1.5 uppercase">No. Bukti Bayar Masal</label>
                    <input
                      id="bulk-collective-no-bayar"
                      type="text"
                      placeholder="Contoh: EBK2506002"
                      className="w-full text-xs font-mono border border-slate-200 rounded-xl px-3 py-2 bg-white text-slate-800 focus:outline-none placeholder-slate-400"
                    />
                  </div>
                  <div>
                    <label className="block text-[9px] font-extrabold text-slate-500 mb-1.5 uppercase">Tgl Bayar Masal (dd/mm/yyyy)</label>
                    <input
                      id="bulk-collective-tgl-bayar"
                      type="text"
                      placeholder="dd/mm/yyyy"
                      defaultValue={formatEntryDate(utcTime.split('T')[0])}
                      className="w-full text-xs font-mono border border-slate-200 rounded-xl px-3 py-2 bg-white text-slate-800 focus:outline-none"
                    />
                  </div>
                  <div>
                    <label className="block text-[9px] font-extrabold text-slate-500 mb-1.5 uppercase">Status Pembayaran</label>
                    <select
                      id="bulk-collective-status"
                      className="w-full text-xs font-bold border border-slate-200 rounded-xl px-3 py-2 bg-white text-slate-805 focus:outline-none cursor-pointer"
                      defaultValue="Lunas"
                    >
                      <option value="Lunas">✓ Set LUNAS</option>
                      <option value="Belum Bayar">❌ Set BELUM LUNAS</option>
                    </select>
                  </div>
                  <button
                    onClick={() => {
                      const noBayarVal = (document.getElementById('bulk-collective-no-bayar') as HTMLInputElement)?.value.trim() || '';
                      const tglBayarVal = (document.getElementById('bulk-collective-tgl-bayar') as HTMLInputElement)?.value.trim() || '';
                      const bulkStatus = (document.getElementById('bulk-collective-status') as HTMLSelectElement)?.value as 'Belum Bayar' | 'Lunas';

                      if (bulkStatus === 'Lunas' && !noBayarVal) {
                        triggerNoti('error', 'Silakan isi No. Bukti Bayar Masal untuk melunasi nota terpilih!');
                        return;
                      }

                      if (confirm(`Apakah Anda yakin ingin menetapkan status "${bulkStatus}" dan mengupdate detail pembayaran untuk ${selectedCollectiveInvoices.length} nota terpilih?`)) {
                        let copyOverrides = { ...state.paymentOverrides };
                        let copyInvoices = [...state.invoices];

                        selectedCollectiveInvoices.forEach(noNota => {
                          const matched = allPeriods.filter(p => p.nomor_invoice_grup === noNota);
                          const ids = matched.map(p => p.id_tagihan);

                          const currentDraft = rowDrafts[noNota] || {
                            buktiBayar: noBayarVal || matched[0]?.nomor_bayar || '',
                            tglBayar: tglBayarVal || (matched[0]?.tanggal_bayar ? formatEntryDate(matched[0].tanggal_bayar) : ''),
                            statusPembayaran: bulkStatus,
                            adjustmentBiaya: matched[0] ? String(copyInvoices.find(i => i.nomor_invoice === noNota)?.adjustment_biaya ?? 0) : '',
                            adjustmentKeterangan: copyInvoices.find(i => i.nomor_invoice === noNota)?.adjustment_keterangan || ''
                          };

                          const finalNoBayar = noBayarVal || currentDraft.buktiBayar;
                          const finalTglISO = tglBayarVal ? (parseInputDate(tglBayarVal) || utcTime.split('T')[0]) : (parseInputDate(currentDraft.tglBayar) || utcTime.split('T')[0]);
                          const parsedBiaya = currentDraft.adjustmentBiaya.trim() ? parseFloat(currentDraft.adjustmentBiaya) : 0;

                          setRowDrafts(prev => ({
                            ...prev,
                            [noNota]: {
                              buktiBayar: finalNoBayar,
                              tglBayar: tglBayarVal || currentDraft.tglBayar,
                              statusPembayaran: bulkStatus,
                              adjustmentBiaya: currentDraft.adjustmentBiaya,
                              adjustmentKeterangan: currentDraft.adjustmentKeterangan
                            }
                          }));

                          ids.forEach(id_tagihan => {
                            const existing = copyOverrides[id_tagihan] || {
                              status_bayar: 'Belum Ditagih',
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

                            copyOverrides[id_tagihan] = {
                              ...existing,
                              status_bayar: bulkStatus === 'Lunas' ? 'Lunas' : 'Pranota',
                              tanggal_bayar: bulkStatus === 'Lunas' ? finalTglISO : null,
                              nomor_bayar: bulkStatus === 'Lunas' ? finalNoBayar : null,
                              nomor_invoice_grup: noNota
                            };
                          });

                          const existsIdx = copyInvoices.findIndex(inv => inv.nomor_invoice.toLowerCase() === noNota.toLowerCase());
                          if (existsIdx !== -1) {
                            copyInvoices[existsIdx] = {
                              ...copyInvoices[existsIdx],
                              status_pembayaran: bulkStatus,
                              adjustment_biaya: parsedBiaya,
                              adjustment_keterangan: currentDraft.adjustmentKeterangan
                            };
                          } else {
                            const customerId = matched[0]
                              ? state.sewas.find(s => s.id_sewa === matched[0].id_sewa)?.id_customer || ''
                              : '';
                            copyInvoices.push({
                              nomor_invoice: noNota,
                              id_customer: customerId,
                              tanggal_invoice: matched[0]?.tanggal_tagihan || utcTime.split('T')[0],
                              status_pembayaran: bulkStatus,
                              deskripsi: 'Virtual Grouping for Nota ' + noNota,
                              list_id_tagihan: ids,
                              adjustment_biaya: parsedBiaya,
                              adjustment_keterangan: currentDraft.adjustmentKeterangan
                            });
                          }
                        });

                        onStateChange({
                          ...state,
                          paymentOverrides: copyOverrides,
                          invoices: copyInvoices
                        });

                        setSelectedCollectiveInvoices([]);
                        triggerNoti('sukses', `Berhasil menyimpan status & pelunasan ke ${selectedCollectiveInvoices.length} Nota.`);
                      }
                    }}
                    className="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs py-2 px-4 rounded-xl shadow-md transition-all cursor-pointer flex items-center justify-center gap-1.5 h-10"
                  >
                    <CheckCircle2 className="w-4 h-4" /> Simpan Masal Terpilih
                  </button>
                </div>
              </div>
            )}

            {/* List Table of Invoices */}
            <div className="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
              <div className="p-4 bg-slate-50 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div className="flex flex-col gap-0.5">
                  <h4 className="text-xs font-extrabold text-slate-700 uppercase tracking-wider">RIWAYAT &amp; PENGATURAN SELURUH NOTA TAGIHAN</h4>
                  <span className="text-[10px] text-slate-400 font-medium">Menampilkan {filteredCollectiveList.length} Nota</span>
                </div>
                
                <button
                  onClick={() => setImportPaymentOpen(!importPaymentOpen)}
                  className="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white rounded-lg text-xs font-black shadow-xs transition-all cursor-pointer shrink-0"
                >
                  <FileSpreadsheet className="w-3.5 h-3.5 shrink-0" />
                  <span>IMPOR PEMBAYARAN MASAL VIA TEKS (PASTE EXCEL)</span>
                  <span className="bg-white/20 px-1 py-0.5 rounded text-[8px] font-black leading-none">NEW</span>
                </button>
              </div>

              <div className="overflow-x-auto">
                <table className="w-full text-left border-collapse">
                  <thead>
                    <tr className="bg-slate-100/60 text-[10px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                      <th className="p-3 text-center w-[44px]">
                        <input
                          type="checkbox"
                          className="rounded text-indigo-600 focus:ring-indigo-500 cursor-pointer h-3.5 w-3.5"
                          checked={
                            filteredCollectiveList.length > 0 &&
                            filteredCollectiveList.every(item => selectedCollectiveInvoices.includes(item.nomor_invoice))
                          }
                          onChange={(e) => {
                            if (e.target.checked) {
                              setSelectedCollectiveInvoices(filteredCollectiveList.map(item => item.nomor_invoice));
                            } else {
                              setSelectedCollectiveInvoices([]);
                            }
                          }}
                        />
                      </th>
                      <th className="p-3.5">Nota Tagihan</th>
                      <th className="p-3.5">Pelanggan/Vendor</th>
                      <th className="p-3.5 min-w-[150px]">Rincian Nominal (Rp)</th>
                      <th className="p-3.5">No. Bukti Bayar</th>
                      <th className="p-3.5">Tanggal Lunas</th>
                      <th className="p-3.5">Adjustment (Rp)</th>
                      <th className="p-3.5">Keterangan Adjust</th>
                      <th className="p-3.5">Status</th>
                      <th className="p-3.5 text-right font-black">Grand Final</th>
                      <th className="p-3.5 text-center">Aksi Pelunasan</th>
                    </tr>
                  </thead>
                  <tbody>
                    {filteredCollectiveList.length === 0 ? (
                      <tr>
                        <td colSpan={11} className="p-10 text-center text-xs text-slate-400">
                          Tidak ada data nota tagihan yang sesuai dengan kriteria pencarian.
                        </td>
                      </tr>
                    ) : (
                      filteredCollectiveList.map((item, index) => {
                        const no = item.nomor_invoice;
                        const draft = rowDrafts[no] || {
                          buktiBayar: item.buktiBayarMapped,
                          tglBayar: item.tglBayarMapped ? formatEntryDate(item.tglBayarMapped) : '',
                          statusPembayaran: item.statusPembayaran,
                          adjustmentBiaya: item.adjustmentBiaya !== 0 ? String(item.adjustmentBiaya) : '',
                          adjustmentKeterangan: item.adjustmentKeterangan
                        };

                        const currentAdjNum = draft.adjustmentBiaya.trim() ? parseFloat(draft.adjustmentBiaya) : 0;
                        const finalGrandTotal = item.totalRekBayar + item.totalPPN - item.totalPPh + currentAdjNum;

                        const handleLocalDraftChange = (field: keyof RowDraft, val: any) => {
                          setRowDrafts(prev => ({
                            ...prev,
                            [no]: {
                              ...(prev[no] || draft),
                              [field]: val
                            }
                          }));
                        };

                        return (
                          <tr key={no} className={`border-b border-slate-100 hover:bg-slate-50/50 text-xs transition-colors ${index % 2 === 1 ? 'bg-slate-50/20' : ''}`}>
                            <td className="p-3 text-center">
                              <input
                                type="checkbox"
                                className="rounded text-indigo-600 focus:ring-indigo-500 cursor-pointer h-3.5 w-3.5"
                                checked={selectedCollectiveInvoices.includes(no)}
                                onChange={(e) => {
                                  if (e.target.checked) {
                                    setSelectedCollectiveInvoices(prev => [...prev, no]);
                                  } else {
                                    setSelectedCollectiveInvoices(prev => prev.filter(item => item !== no));
                                  }
                                }}
                              />
                            </td>
                            <td className="p-3 font-mono font-bold text-slate-800">
                              <div className="flex flex-col gap-0.5">
                                <span className="text-indigo-900 bg-indigo-50 px-2 py-0.5 rounded-md border border-indigo-100 font-bold block w-fit text-[11px] max-w-[200px] truncate" title={no}>{no}</span>
                                <span className="text-[9px] text-slate-400 block font-normal">{item.list_id_tagihan.length} Kontainer</span>
                              </div>
                            </td>

                            <td className="p-3 font-semibold text-slate-700 max-w-[150px] truncate" title={getCustomerName(item.customerId)}>
                              {getCustomerName(item.customerId) || '-'}
                            </td>

                            <td className="p-3 font-mono text-[10px] text-slate-600 leading-normal">
                              <div>Actual: <strong>{formatRupiah(item.totalAktual)}</strong></div>
                              <div>Rek. Bayar: <span className="text-emerald-700 font-bold">{formatRupiah(item.totalRekBayar)}</span></div>
                              <div className="text-[9px] opacity-75">PPN(11%): +{formatRupiah(item.totalPPN)}</div>
                              <div className="text-[9px] opacity-75">PPh(2%): -{formatRupiah(item.totalPPh)}</div>
                            </td>

                            <td className="p-3">
                              <input
                                type="text"
                                placeholder="Contoh: EBK2506002"
                                value={draft.buktiBayar}
                                onChange={(e) => handleLocalDraftChange('buktiBayar', e.target.value)}
                                className="w-[125px] text-[11px] font-mono border border-slate-200 rounded-lg px-2 py-1 bg-white"
                              />
                            </td>

                            <td className="p-3">
                              <input
                                type="text"
                                placeholder="dd/mm/yyyy"
                                value={draft.tglBayar}
                                onChange={(e) => handleLocalDraftChange('tglBayar', e.target.value)}
                                className="w-[95px] text-[11px] font-mono border border-slate-200 rounded-lg px-2 py-1 bg-white"
                              />
                            </td>

                            <td className="p-3">
                              <input
                                type="text"
                                placeholder="e.g. 15000"
                                value={draft.adjustmentBiaya}
                                onChange={(e) => {
                                  const val = e.target.value.replace(/[^0-9.-]/g, '');
                                  handleLocalDraftChange('adjustmentBiaya', val);
                                }}
                                className="w-[100px] text-[11px] font-mono font-bold text-amber-800 border border-slate-200 rounded-lg px-2 py-1 bg-white"
                              />
                            </td>

                            <td className="p-3">
                              <input
                                type="text"
                                placeholder="Potongan/Bank dll"
                                value={draft.adjustmentKeterangan}
                                onChange={(e) => handleLocalDraftChange('adjustmentKeterangan', e.target.value)}
                                className="w-[120px] text-[11px] border border-slate-200 rounded-lg px-2 py-1 bg-white text-slate-700"
                              />
                            </td>

                            <td className="p-3">
                              <select
                                value={draft.statusPembayaran}
                                onChange={(e) => handleLocalDraftChange('statusPembayaran', e.target.value as 'Belum Bayar' | 'Lunas')}
                                className={`text-[10px] font-bold border rounded-lg px-1.5 py-1 ${
                                  draft.statusPembayaran === 'Lunas'
                                    ? 'bg-emerald-100 border-emerald-300 text-emerald-800'
                                    : 'bg-rose-100 border-rose-300 text-rose-800'
                                }`}
                              >
                                <option value="Belum Bayar">❌ Belum Lunas</option>
                                <option value="Lunas">✓ Lunas</option>
                              </select>
                            </td>

                            <td className="p-3 text-right font-mono font-bold text-slate-900 pr-5">
                              <div className="flex flex-col items-end">
                                <span className="text-xs text-indigo-950 font-black">{formatRupiah(finalGrandTotal)}</span>
                                {currentAdjNum !== 0 && (
                                  <span className={`text-[9px] font-semibold ${currentAdjNum > 0 ? "text-emerald-700" : "text-rose-500"}`}>
                                    ({currentAdjNum > 0 ? '+' : ''}{formatRupiah(currentAdjNum)})
                                  </span>
                                )}
                              </div>
                            </td>

                            <td className="p-3 text-center">
                              <div className="flex items-center justify-center gap-1.5">
                                <button
                                  onClick={() => {
                                    const parsedBiaya = draft.adjustmentBiaya.trim() ? parseFloat(draft.adjustmentBiaya) : null;
                                    handleSaveCollectiveChanges(
                                      no,
                                      draft.buktiBayar,
                                      draft.tglBayar,
                                      parsedBiaya,
                                      draft.adjustmentKeterangan,
                                      draft.statusPembayaran
                                    );
                                  }}
                                  title="Simpan perubahan nota ini"
                                  className="p-1 px-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-extrabold rounded-lg shadow-xs transition-all flex items-center gap-1 cursor-pointer"
                                >
                                  <Check className="w-3.5 h-3.5 shrink-0" />
                                  Simpan
                                </button>

                                <button
                                  onClick={() => {
                                    setSelectedNota(no);
                                    setActiveViewTab('group');
                                    setTimeout(() => {
                                      const el = document.getElementById('grouped-collection-dashboard');
                                      if (el) el.scrollIntoView({ behavior: 'smooth' });
                                    }, 100);
                                  }}
                                  title="Buka rincian kontainer untuk nota ini"
                                  className="p-1 px-2.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 text-[10px] font-bold rounded-lg transition-all flex items-center gap-1 cursor-pointer"
                                >
                                  Detail
                                </button>

                                <button
                                  onClick={() => {
                                    const invObj = state.invoices.find(i => i.nomor_invoice === no) || {
                                      nomor_invoice: no,
                                      id_customer: item.customerId,
                                      tanggal_invoice: item.tglBayarMapped ? item.tglBayarMapped : utcTime.split('T')[0],
                                      status_pembayaran: draft.statusPembayaran,
                                      deskripsi: 'Virtual Grouping for Nota ' + no,
                                      list_id_tagihan: item.list_id_tagihan,
                                      adjustment_biaya: currentAdjNum,
                                      adjustment_keterangan: draft.adjustmentKeterangan
                                    };
                                    setPrintInvoice(invObj);
                                  }}
                                  title="Cetak kwitansi/invoice PDF resmi"
                                  className="p-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition-all cursor-pointer"
                                >
                                  <Printer className="w-3.5 h-3.5 shrink-0" />
                                </button>
                              </div>
                            </td>
                          </tr>
                        );
                      })
                    )}
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        );
      })()}

      {/* VIEW TAB 3: EXECUTIVE BUSINESS INTELLIGENCE REPORT & AUDIT PANEL */}
      {activeViewTab === 'report' && (() => {
        const activeUnitsCount = state.sewas.filter(s => s.status_sewa === 'Aktif').length;
        const totalContainersRegistered = state.kontainers.length;
        
        // Total Outstanding liabilities which are billed but unpaid ("Belum Bayar")
        const oAmount = allPeriods
          .filter(p => p.status_bayar === 'Belum Bayar')
          .reduce((sum, p) => {
            const act = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : p.jumlah_tagihan;
            return sum + act;
          }, 0);

        // Total Accruals / Current monthly run-rate running but not yet billed ("Belum Ditagih")
        const aAmount = allPeriods
          .filter(p => p.status_bayar === 'Belum Ditagih')
          .reduce((sum, p) => sum + p.jumlah_tagihan, 0);

        // Total paid (Lunas)
        const pAmount = allPeriods
          .filter(p => p.status_bayar === 'Lunas')
          .reduce((sum, p) => {
            const act = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : p.jumlah_tagihan;
            return sum + act;
          }, 0);

        // Total Estimasi vs Real
        const grandEstimasiTotal = allPeriods.reduce((sum, p) => sum + p.jumlah_tagihan, 0);
        const grandAktualTotal = allPeriods.reduce((sum, p) => {
          const act = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : p.jumlah_tagihan;
          return sum + act;
        }, 0);
        const grandSelisihTotal = grandAktualTotal - grandEstimasiTotal;

        // Taxes
        const totalPPNValue = allPeriods.reduce((sum, p) => {
          const act = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : p.jumlah_tagihan;
          const ppn = p.ppn !== null && p.ppn !== undefined ? p.ppn : Math.round(act * 0.11);
          return sum + ppn;
        }, 0);

        const totalPPhValue = allPeriods.reduce((sum, p) => {
          const act = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : p.jumlah_tagihan;
          const pph = p.pph !== null && p.pph !== undefined ? p.pph : Math.round(act * 0.02);
          return sum + pph;
        }, 0);

        // Dynamic Partner summary
        const partnerRecs = state.customers.map(c => {
          const relatedSewas = state.sewas.filter(s => s.id_customer === c.id_customer);
          const activeCnt = relatedSewas.filter(s => s.status_sewa === 'Aktif').length;
          
          const periods = allPeriods.filter(p => {
            const s = state.sewas.find(x => x.id_sewa === p.id_sewa);
            return s && s.id_customer === c.id_customer;
          });
          
          const paidPart = periods.filter(p => p.status_bayar === 'Lunas').reduce((sum, p) => {
            const act = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : p.jumlah_tagihan;
            return sum + act;
          }, 0);

          const outstandingPart = periods.filter(p => p.status_bayar === 'Belum Bayar').reduce((sum, p) => {
            const act = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : p.jumlah_tagihan;
            return sum + act;
          }, 0);

          const accruedPart = periods.filter(p => p.status_bayar === 'Belum Ditagih').reduce((sum, p) => p.jumlah_tagihan + sum, 0);

          return {
            id: c.id_customer,
            name: c.nama_customer,
            activeCnt,
            paidPart,
            outstandingPart,
            accruedPart,
            totalPartSum: paidPart + outstandingPart + accruedPart
          };
        }).filter(p => p.totalPartSum > 0 || p.activeCnt > 0);

        const maxSpentLimit = partnerRecs.length > 0 ? Math.max(...partnerRecs.map(p => p.totalPartSum)) : 1;

        // Discrepancy highlights
        const discrepancyPeriods = allPeriods.filter(p => {
          const act = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : p.jumlah_tagihan;
          return Math.abs(act - p.jumlah_tagihan) > 0;
        });

        return (
          <div className="space-y-8 animate-fade-in" id="executive-analytics-dashboard">
            {/* INSTRUCTIONAL SUMMARY CARD */}
            <div className={`p-6 rounded-2xl border transition-all duration-350 ${
              isSewaIn 
                ? 'bg-indigo-950/[0.02] border-indigo-150/40' 
                : 'bg-emerald-950/[0.02] border-emerald-150/40'
            }`}>
              <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div className="space-y-1">
                  <h3 className="font-bold text-slate-800 text-sm flex items-center gap-2">
                    <Sparkles className={`w-4 h-4 ${isSewaIn ? 'text-indigo-600' : 'text-emerald-600'}`} />
                    <span>MODUL AUDIT & LAPORAN PORTFOLIO ({isSewaIn ? 'SEWA IN / KEWAJIBAN BIAYA' : 'SEWA OUT / PENDAPATAN'})</span>
                  </h3>
                  <p className="text-xs text-slate-500 max-w-3xl">
                    Sistem otomatis menghitung dan mengelompokkan data berdasarkan transaksi sewa yang terserap secara real-time. Memudahkan audit pajak PPN, potongan withholding PPh 23, serta pelacakan cycle accruals berjalan.
                  </p>
                </div>
                <div className="flex gap-2">
                  <button 
                    onClick={handleTriggerPrint}
                    className="inline-flex items-center justify-center px-4 py-2 text-xs font-bold bg-white text-slate-700 hover:bg-slate-50 border border-slate-200 rounded-xl transition-all cursor-pointer shadow-xs gap-1.5"
                  >
                    <Printer className="w-3.5 h-3.5" />
                    <span>Cetak Laporan Lengkap</span>
                  </button>
                </div>
              </div>
            </div>

            {/* KPI METRIC CARDS GRID */}
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
              <div className="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs flex items-center justify-between">
                <div>
                  <span className="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Unit Tersewa Aktif</span>
                  <p className="text-lg font-bold text-slate-850 mt-1 font-mono">{activeUnitsCount} / {totalContainersRegistered} Unit</p>
                  <div className="w-24 bg-slate-100 h-1.5 rounded-full mt-1.5 overflow-hidden">
                    <div 
                      className={`h-full rounded-full ${isSewaIn ? 'bg-indigo-600' : 'bg-emerald-600'}`}
                      style={{ width: `${Math.min(100, (activeUnitsCount / (totalContainersRegistered || 1)) * 100)}%` }}
                    />
                  </div>
                </div>
                <div className={`p-3 rounded-xl border ${isSewaIn ? 'bg-indigo-50 text-indigo-700 border-indigo-100' : 'bg-emerald-50 text-emerald-700 border-emerald-100'}`}>
                  <Building2 className="w-5 h-5" />
                </div>
              </div>

              <div className="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs flex items-center justify-between">
                <div>
                  <span className="text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                    {isSewaIn ? 'Prakiraan Tagihan Berjalan' : 'Estimasi Belum Ditagih'}
                  </span>
                  <p className="text-lg font-bold text-amber-600 mt-1 font-mono">{formatRupiah(aAmount)}</p>
                  <p className="text-[10px] text-amber-500 mt-0.5">Siklus belum diselesaikan vendor</p>
                </div>
                <div className="bg-amber-50 text-amber-700 p-3 rounded-xl border border-amber-100">
                  <Calendar className="w-5 h-5" />
                </div>
              </div>

              <div className="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs flex items-center justify-between">
                <div>
                  <span className="text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                    {isSewaIn ? 'Liabilitas Terhutang (Outstanding)' : 'Piutang Outstanding'}
                  </span>
                  <p className="text-lg font-bold text-rose-600 mt-1 font-mono">{formatRupiah(oAmount)}</p>
                  <p className="text-[10px] text-rose-500 mt-0.5">Sudah diterbitkan invoice/tagihan</p>
                </div>
                <div className="bg-rose-50 text-rose-700 p-3 rounded-xl border border-rose-100">
                  <AlertCircle className="w-5 h-5" />
                </div>
              </div>

              <div className="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs flex items-center justify-between">
                <div>
                  <span className="text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                    {isSewaIn ? 'Kas Terbayarkan (Lunas)' : 'Pendapatan Diterima'}
                  </span>
                  <p className={`text-lg font-bold mt-1 font-mono ${isSewaIn ? 'text-indigo-600' : 'text-emerald-700'}`}>{formatRupiah(pAmount)}</p>
                  <p className="text-[10px] text-slate-500 mt-0.5">Histori transaksi bersih lunas</p>
                </div>
                <div className={`p-3 rounded-xl border ${isSewaIn ? 'bg-indigo-50 text-indigo-750 border-indigo-100' : 'bg-emerald-50 text-emerald-750 border-emerald-100'}`}>
                  <Coins className="w-5 h-5" />
                </div>
              </div>
            </div>

            {/* TWO COLUMNS: PARTNERS & TAX SUMMARY */}
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
              
              {/* TABLE: PARTNER (VENDOR/CUSTOMER) SPEND METRICS */}
              <div className="bg-white p-6 rounded-2xl border border-slate-100 shadow-xs lg:col-span-2">
                <div className="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                  <h4 className="font-bold text-slate-800 text-xs uppercase tracking-wider flex items-center gap-1.5">
                    <Building2 className={`w-4 h-4 ${isSewaIn ? 'text-indigo-600' : 'text-emerald-600'}`} />
                    <span>{isSewaIn ? 'Rekapitulasi Pengeluaran per Vendor / Supplier' : 'Rekapitulasi Penjualan per Customer'}</span>
                  </h4>
                  <span className="text-[10px] bg-slate-100 px-2 py-0.5 rounded-full font-mono font-bold text-slate-600">{partnerRecs.length} Entitas</span>
                </div>

                <div className="overflow-x-auto">
                  <table className="w-full text-left border-collapse text-[11px] leading-tight text-slate-700">
                    <thead>
                      <tr className="bg-slate-50 text-slate-500 border-b border-slate-100 font-bold">
                        <th className="p-2.5">{isSewaIn ? 'NAMA VENDOR / PENYEDIA' : 'NAMA CUSTOMER'}</th>
                        <th className="p-2.5 text-center">AKTIF</th>
                        <th className="p-2.5 text-right font-mono text-slate-500">TELAH BAYAR</th>
                        <th className="p-2.5 text-right font-mono text-rose-500">OUTSTANDING</th>
                        <th className="p-2.5 text-right font-mono text-amber-500">BERJALAN (AKRUAL)</th>
                        <th className="p-2.5 text-right font-mono text-slate-800 bg-slate-50">KOMULATIF TOTAL</th>
                      </tr>
                    </thead>
                    <tbody className="divide-y divide-slate-100">
                      {partnerRecs.map(p => {
                        const pctShare = Math.round((p.totalPartSum / maxSpentLimit) * 100);
                        return (
                          <tr key={p.id} className="hover:bg-slate-50/50">
                            <td className="p-2.5">
                              <span className="font-bold text-slate-900 block">{p.name}</span>
                              <div className="w-full bg-slate-100 h-1 rounded-full mt-1.5 overflow-hidden max-w-xs">
                                <div 
                                  className={`h-full rounded-full ${isSewaIn ? 'bg-indigo-500' : 'bg-emerald-500'}`} 
                                  style={{ width: `${pctShare}%` }} 
                                />
                              </div>
                            </td>
                            <td className="p-2.5 text-center font-bold">
                              <span className="bg-slate-100 text-slate-700 px-2 py-0.5 rounded-full font-semibold text-[10px] font-mono">
                                {p.activeCnt} Unit
                              </span>
                            </td>
                            <td className="p-2.5 text-right font-mono">{formatRupiah(p.paidPart)}</td>
                            <td className="p-2.5 text-right font-mono text-rose-600 font-semibold">{formatRupiah(p.outstandingPart)}</td>
                            <td className="p-2.5 text-right font-mono text-amber-600">{formatRupiah(p.accruedPart)}</td>
                            <td className="p-2.5 text-right font-mono font-bold text-slate-900 bg-slate-50/40">{formatRupiah(p.totalPartSum)}</td>
                          </tr>
                        );
                      })}
                      {partnerRecs.length === 0 && (
                        <tr>
                          <td colSpan={6} className="p-8 text-center text-slate-400 italic">Belum ada sirkulasi data keuangan aktif.</td>
                        </tr>
                      )}
                    </tbody>
                  </table>
                </div>
              </div>

              {/* RETENTION TAX LEDGER & RECON BRIEF */}
              <div className="bg-white p-6 rounded-2xl border border-slate-100 shadow-xs flex flex-col justify-between">
                <div>
                  <div className="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                    <h4 className="font-bold text-slate-800 text-xs uppercase tracking-wider flex items-center gap-1.5">
                      <span className={`w-2 h-2 rounded-full ${isSewaIn ? 'bg-indigo-600' : 'bg-emerald-600'}`} />
                      <span>Rekapitulasi Faktur &amp; Pajak</span>
                    </h4>
                  </div>

                  <div className="space-y-4">
                    <div className="p-3 bg-slate-50/70 border border-slate-150 rounded-xl space-y-1.5">
                      <span className="text-[9px] uppercase font-bold text-slate-500 tracking-wider">Estimasi vs Aktual Billed</span>
                      <div className="flex justify-between text-xs text-slate-700">
                        <span>Estimasi Logis Sistem:</span>
                        <span className="font-mono font-semibold">{formatRupiah(grandEstimasiTotal)}</span>
                      </div>
                      <div className="flex justify-between text-xs text-slate-800">
                        <span>Nominal Aktual (Invoice):</span>
                        <span className="font-mono font-extrabold">{formatRupiah(grandAktualTotal)}</span>
                      </div>
                      <div className="border-t border-dashed border-slate-300 pt-1 flex justify-between text-xs">
                        <span className="font-bold">Total Selisih Tagihan:</span>
                        <span className={`font-mono font-bold ${grandSelisihTotal > 0 ? 'text-rose-600' : grandSelisihTotal < 0 ? 'text-emerald-600' : 'text-slate-500'}`}>
                          {grandSelisihTotal > 0 ? '+' : ''}{formatRupiah(grandSelisihTotal)}
                        </span>
                      </div>
                    </div>

                    <div className="space-y-2.5 pt-2">
                      <div className="flex items-center justify-between text-xs pb-1.5 border-b border-slate-100">
                        <div className="flex items-center gap-1.5">
                          <span className={`w-1.5 h-1.5 rounded-full ${isSewaIn ? 'bg-indigo-500' : 'bg-emerald-500'}`} />
                          <span className="text-slate-600">{isSewaIn ? 'PPN Masukan (11%)' : 'PPN Keluaran (11%)'}</span>
                        </div>
                        <span className="font-mono font-bold text-slate-900">{formatRupiah(totalPPNValue)}</span>
                      </div>

                      <div className="flex items-center justify-between text-xs pb-1.5 border-b border-slate-100">
                        <div className="flex items-center gap-1.5">
                          <span className="w-1.5 h-1.5 rounded-full bg-rose-500" />
                          <span className="text-slate-600">Withholding PPh 23 (2%)</span>
                        </div>
                        <span className="font-mono font-bold text-rose-600">-{formatRupiah(totalPPhValue)}</span>
                      </div>

                      <div className="flex items-center justify-between text-xs pt-1">
                        <span className="font-bold text-slate-900">Estimasi Bersih Pajak Net:</span>
                        <span className={`font-mono font-extrabold ${isSewaIn ? 'text-indigo-600' : 'text-emerald-700'}`}>
                          {formatRupiah(grandAktualTotal + totalPPNValue - totalPPhValue)}
                        </span>
                      </div>
                    </div>
                  </div>
                </div>

                <div className="p-3 bg-indigo-50/20 border border-indigo-100/50 text-[10px] text-indigo-900 rounded-xl mt-4 leading-tight italic">
                  * Withholding PPh Pasal 23 wajib dipotong 2% atas jasa sewa kontainer dan disetorkan ke kas negara paling lambat tanggal 10 bulan berikutnya.
                </div>
              </div>
            </div>

            {/* TABLE: BILLING RECONCILIATION DISCREPANCY AUDIT (LOG AUDIT SELISIH) */}
            <div className="bg-white p-6 rounded-2xl border border-slate-100 shadow-xs">
              <div className="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                <h4 className="font-bold text-rose-750 text-rose-700 text-xs uppercase tracking-wider flex items-center gap-1.5">
                  <AlertCircle className="w-4 h-4 text-rose-600" />
                  <span>Log Audit Selisih &amp; Deviasi Tagihan Vendor</span>
                </h4>
                <span className="text-[10px] bg-rose-50 px-2.5 py-0.5 rounded-full font-bold text-rose-600">{discrepancyPeriods.length} Deviasi Ditemukan</span>
              </div>

              <div className="overflow-x-auto">
                <table className="w-full text-left border-collapse text-[11px] text-slate-700">
                  <thead>
                    <tr className="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold font-mono">
                      <th className="p-3">KONTAINER</th>
                      <th className="p-3 text-center">BULAN KE</th>
                      <th className="p-3">{isSewaIn ? 'VENDOR PARTNER' : 'CUSTOMER'}</th>
                      <th className="p-3 text-right">ESTIMASI TARIF</th>
                      <th className="p-3 text-right">TAGIHAN AKTUAL</th>
                      <th className="p-3 text-right font-bold text-rose-600">SELISIH / DEVIASI</th>
                      <th className="p-3 bg-amber-50/45">KETERANGAN &amp; JUSTIFIKASI AUDIT</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-slate-100">
                    {discrepancyPeriods.map(p => {
                      const sewa = state.sewas.find(s => s.id_sewa === p.id_sewa);
                      const act = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : p.jumlah_tagihan;
                      const diff = act - p.jumlah_tagihan;
                      return (
                        <tr key={p.id_tagihan} className="hover:bg-slate-50/50">
                          <td className="p-3 font-mono font-bold text-slate-900">{getSewaContainerNo(p.id_sewa)}</td>
                          <td className="p-3 text-center font-mono">Bulan ke-{p.bulan_ke}</td>
                          <td className="p-3 font-bold text-slate-750">{sewa ? getCustomerName(sewa.id_customer) : '-'}</td>
                          <td className="p-3 text-right font-mono text-slate-500">{formatRupiah(p.jumlah_tagihan)}</td>
                          <td className="p-3 text-right font-mono font-bold text-slate-800">{formatRupiah(act)}</td>
                          <td className="p-3 text-right font-mono font-extrabold text-rose-600">
                            {diff > 0 ? '+' : ''}{formatRupiah(diff)}
                          </td>
                          <td className="p-3 text-slate-650 bg-amber-50/[0.15] italic">
                            {p.keterangan_selisih || (
                              <span className="text-slate-400">Tidak ada rincian keterangan selisih dimasukkan</span>
                            )}
                          </td>
                        </tr>
                      );
                    })}
                    {discrepancyPeriods.length === 0 && (
                      <tr>
                        <td colSpan={7} className="p-8 text-center text-slate-400 italic">No discrepancy found (100% Match Between Estimations and Actuals). Perfect reconciliations!</td>
                      </tr>
                    )}
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        );
      })()}

      {/* PRINT INVOICE MODAL SHEET */}
      {printInvoice && (
        <div className="fixed inset-0 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 z-50 overflow-y-auto animate-fade-in" id="invoice-print-modal">
          <div className="bg-white rounded-2xl max-w-3xl w-full p-8 border border-slate-200 shadow-2xl relative my-8">
            
            {/* Header controls inside modal */}
            <div className="absolute top-4 right-4 flex items-center gap-2 no-print">
              {!printInvoice.status_pembayaran.includes('Lunas') && (
                <button
                  onClick={() => {
                    handleLunasiInvoiceGrup(printInvoice);
                    setPrintInvoice({ ...printInvoice, status_pembayaran: 'Lunas' });
                  }}
                  className="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-3.5 py-1.5 rounded-xl shadow-xs cursor-pointer flex items-center gap-1"
                >
                  <Check className="w-3.5 h-3.5" /> Bayar Lunas
                </button>
              )}
              <button
                onClick={handleTriggerPrint}
                className="bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs px-3.5 py-1.5 rounded-xl shadow-xs cursor-pointer flex items-center gap-1"
              >
                <Printer className="w-3.5 h-3.5" /> Cetak (PDF / Print)
              </button>
              <button
                onClick={() => setPrintInvoice(null)}
                className="bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs px-3 py-1.5 rounded-xl cursor-pointer"
              >
                Tutup
              </button>
            </div>

            {/* HIGH FIDELITY PRINT SURFACE */}
            <div className="border border-slate-200 p-8 rounded-xl print-container mt-6 text-slate-800" id="print-area-wrapper">
              
              {/* Header Logo & Address */}
              <div className="flex justify-between items-start border-b-2 border-slate-800 pb-5">
                <div>
                  <h3 className="text-xl font-black text-slate-900 tracking-tight">PORTAL CARGO &amp; DEPO KONTAINER</h3>
                  <p className="text-xs text-slate-500 leading-relaxed max-w-md">
                    Jl. Pelabuhan Samudera No. 45, Kawasan Tanjung Priok, DKI Jakarta INDONESIA<br />
                    Telp: (021) 880-9900 | ID Pajak: 01.992.883.1-001.000
                  </p>
                </div>
                <div className="text-right">
                  <h4 className="text-sm font-extrabold text-slate-700 uppercase tracking-widest bg-slate-100 px-3 py-1 rounded-md inline-block">
                    PRANOTA / INVOICE
                  </h4>
                  <p className="text-[11px] font-mono font-bold text-slate-900 mt-2">NO: {printInvoice.nomor_invoice}</p>
                  <p className="text-[10px] text-slate-500">Tanggal: {formatIndoDate(printInvoice.tanggal_invoice)}</p>
                </div>
              </div>

              {/* Bill To Info */}
              <div className="grid grid-cols-2 gap-4 my-6 text-xs">
                <div>
                  <p className="text-[10px] uppercase font-extrabold text-slate-400 tracking-wider">Paten Kepada / Pelanggan:</p>
                  <p className="font-extrabold text-slate-900 text-sm mt-1">{getCustomerName(printInvoice.id_customer)}</p>
                  <p className="text-[11px] text-slate-500 leading-normal mt-0.5">
                    ID Akun Pelanggan: {printInvoice.id_customer}<br />
                    Faktur penagihan sewa dwi-periode kontainer berjalan di depo.
                  </p>
                </div>
                <div className="text-right">
                  <p className="text-[10px] uppercase font-extrabold text-slate-400 tracking-wider">Status Pembayaran:</p>
                  <p className={`text-xs font-bold mt-1 inline-block px-3 py-1 rounded-full uppercase tracking-wider ${
                    printInvoice.status_pembayaran === 'Lunas' ? 'bg-emerald-150 bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'
                  }`}>
                    {printInvoice.status_pembayaran === 'Lunas' ? '✓ LUNAS' : '⚠️ MENUNGGU PEMBAYARAN'}
                  </p>
                  <p className="text-[10px] text-slate-400 mt-2 font-mono">Verified WIB Timezone Engine</p>
                </div>
              </div>

              {/* Items Table */}
              <table className="w-full text-[11px] border-collapse text-left my-4">
                <thead>
                  <tr className="bg-slate-100 border-b-2 border-slate-400 text-slate-700 font-bold">
                    <th className="p-2 w-8 text-center">NO</th>
                    <th className="p-2">ID SIKLUS / KONTAINER</th>
                    <th className="p-2 text-center">MASA PERIODE</th>
                    <th className="p-2 text-right">ESTIMASI</th>
                    <th className="p-2 text-right">NILAI TAGIHAN</th>
                    <th className="p-2 text-right">SELISIH</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-slate-250">
                  {printInvoice.list_id_tagihan.map((id, index) => {
                    const p = allPeriods.find(x => x.id_tagihan === id);
                    if (!p) return null;
                    const tagihan = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : p.jumlah_tagihan;
                    const diff = tagihan - p.jumlah_tagihan;

                    return (
                      <tr key={id} className="align-middle">
                        <td className="p-2 text-center font-mono">{index + 1}</td>
                        <td className="p-2 font-mono">
                          <strong className="text-slate-900">{getSewaContainerNo(p.id_sewa)}</strong> ({getSewaContainerSizeDesc(p.id_sewa)})
                          <span className="block text-[9px] text-slate-400 font-normal break-all">ID: {p.id_tagihan}</span>
                        </td>
                        <td className="p-2 text-center">
                          <strong>Bulan Ke-{p.bulan_ke}</strong>
                          <p className="text-[9px] text-slate-400 font-normal">{formatIndoDate(p.tanggal_awal)} s/d {formatIndoDate(p.tanggal_akhir)}</p>
                        </td>
                        <td className="p-2 text-right font-mono text-slate-500">{formatRupiah(p.jumlah_tagihan)}</td>
                        <td className="p-2 text-right font-mono font-bold text-slate-900">{formatRupiah(tagihan)}</td>
                        <td className={`p-2 text-right font-mono ${diff !== 0 ? 'font-bold text-slate-800' : 'text-slate-400'}`}>
                          {diff > 0 ? '+' : ''}{formatRupiah(diff)}
                        </td>
                      </tr>
                    );
                  })}
                </tbody>
              </table>

              {/* Tax calculations subtotals */}
              <div className="border-t border-slate-400 pt-4 mt-6">
                {(() => {
                  const pdfTotalAktual = printInvoice.list_id_tagihan.reduce((acc, id) => {
                    const p = allPeriods.find(x => x.id_tagihan === id);
                    if (!p) return acc;
                    return acc + (p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : p.jumlah_tagihan);
                  }, 0);

                  const pdfTotalRekBayar = printInvoice.list_id_tagihan.reduce((acc, id) => {
                    const p = allPeriods.find(x => x.id_tagihan === id);
                    if (!p) return acc;
                    const tagihan = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : p.jumlah_tagihan;
                    return acc + Math.min(tagihan, p.jumlah_tagihan);
                  }, 0);

                  const pdfTotalPPN = printInvoice.list_id_tagihan.reduce((acc, id) => {
                    const p = allPeriods.find(x => x.id_tagihan === id);
                    if (!p) return acc;
                    const tagihan = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : p.jumlah_tagihan;
                    const ppn = p.ppn !== null && p.ppn !== undefined ? p.ppn : Math.round(tagihan * 0.11);
                    return acc + ppn;
                  }, 0);

                  const pdfTotalPPh = printInvoice.list_id_tagihan.reduce((acc, id) => {
                    const p = allPeriods.find(x => x.id_tagihan === id);
                    if (!p) return acc;
                    const tagihan = p.jumlah_tagihan_override !== null && p.jumlah_tagihan_override !== undefined ? p.jumlah_tagihan_override : p.jumlah_tagihan;
                    const pph = p.pph !== null && p.pph !== undefined ? p.pph : Math.round(tagihan * 0.02);
                    return acc + pph;
                  }, 0);

                  const pdfGroupInvoice = state.invoices.find(i => i.nomor_invoice === printInvoice.nomor_invoice);
                  const pdfAdjBiaya = pdfGroupInvoice?.adjustment_biaya ?? printInvoice.adjustment_biaya ?? 0;
                  const pdfAdjKet = pdfGroupInvoice?.adjustment_keterangan ?? printInvoice.adjustment_keterangan ?? '';

                  const pdfGrandNetTotal = pdfTotalRekBayar + pdfTotalPPN - pdfTotalPPh + pdfAdjBiaya;

                  return (
                    <div className="w-80 ml-auto space-y-1.5 text-xs text-slate-700">
                      <div className="flex justify-between font-medium">
                        <span>Total Tagihan (Aktual):</span>
                        <span className="font-mono text-slate-900">{formatRupiah(pdfTotalAktual)}</span>
                      </div>

                      <div className="flex justify-between font-semibold text-emerald-700">
                        <span>Subtotal Rek. Bayar:</span>
                        <span className="font-mono">{formatRupiah(pdfTotalRekBayar)}</span>
                      </div>

                      <div className="flex justify-between font-bold text-indigo-700 border-b border-dashed border-slate-200 pb-1.5 flex items-center gap-1 text-[11px]">
                        <span>Pertambahan Nilai PPN (11%):</span>
                        <span className="font-mono">+{formatRupiah(pdfTotalPPN)}</span>
                      </div>

                      <div className="flex justify-between font-bold text-rose-600 border-b border-slate-200 pb-1.5 flex items-center gap-1 text-[11px]">
                        <span>Pengurang Pajak PPh (2%):</span>
                        <span className="font-mono">-{formatRupiah(pdfTotalPPh)}</span>
                      </div>

                      {pdfAdjBiaya !== 0 && (
                        <div className="flex justify-between text-amber-800 font-semibold border-b border-slate-300 pb-2.5">
                          <div>
                            <span>Adjustment Nota:</span>
                            {pdfAdjKet && <span className="block text-[8px] text-amber-600">({pdfAdjKet})</span>}
                          </div>
                          <span className="font-mono">
                            {pdfAdjBiaya > 0 ? '+' : ''}{formatRupiah(pdfAdjBiaya)}
                          </span>
                        </div>
                      )}

                      <div className="flex justify-between font-black text-sm text-slate-950 py-1.5 border-b-2 border-slate-800">
                        <span>GRAND NET TOTAL:</span>
                        <span className="font-mono">{formatRupiah(pdfGrandNetTotal)}</span>
                      </div>
                    </div>
                  );
                })()}
              </div>

              {/* Signatures and WIB Timestamp Statement */}
              <div className="grid grid-cols-2 gap-4 mt-12 text-center text-xs">
                <div>
                  <p className="text-slate-400 font-semibold mb-12 uppercase tracking-widest text-[9px]">Penerima,/ Keuangan Pelanggan,</p>
                  <p className="font-extrabold text-slate-900 border-b border-slate-300 w-44 mx-auto pb-1"></p>
                  <p className="text-[10px] text-slate-400 mt-1">Meterai Lunas Terlampir</p>
                </div>
                <div>
                  <p className="text-slate-400 font-semibold mb-12 uppercase tracking-widest text-[9px]">Hormat Kami, Depo Keuangan,</p>
                  <p className="font-extrabold text-slate-900 border-b border-slate-300 w-44 mx-auto pb-1">ADRIAN MAHARDIKA</p>
                  <p className="text-[10px] text-slate-400 mt-1 font-mono">Dibuat via WIB Portal Sistem</p>
                </div>
              </div>

            </div>
          </div>
        </div>
      )}

    </div>
  );
}
