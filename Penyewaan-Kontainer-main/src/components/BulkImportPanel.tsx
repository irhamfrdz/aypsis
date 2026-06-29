import React, { useState } from 'react';
import { AppState, compileAllPeriods, getEmptyAppState, getDemoAppState } from '../dataStore';
import { Customer, TipeKontainer, UkuranKontainer, Kontainer, TarifSewa, Sewa } from '../types';
import { parseInputDate, isLeapYear, formatIndoDate } from '../utils';
import { Upload, AlertTriangle, Check, BookOpen, CircleAlert, CheckCircle, Info, Trash2, RotateCcw, Copy } from 'lucide-react';

function CopyButton({ textValue, label }: { textValue: string; label: string }) {
  const [copied, setCopied] = useState(false);

  const handleCopy = async () => {
    try {
      await navigator.clipboard.writeText(textValue);
      setCopied(true);
      setTimeout(() => setCopied(false), 2000);
    } catch (e) {
      // Fallback for iframe sandbox environments
      const textarea = document.createElement('textarea');
      textarea.value = textValue;
      textarea.style.position = 'fixed';
      textarea.style.opacity = '0';
      document.body.appendChild(textarea);
      textarea.select();
      try {
        document.execCommand('copy');
        setCopied(true);
        setTimeout(() => setCopied(false), 2000);
      } catch (err) {
        console.error('Copy fallback failed', err);
      }
      document.body.removeChild(textarea);
    }
  };

  return (
    <button
      type="button"
      onClick={handleCopy}
      className={`inline-flex items-center gap-1 px-2 py-0.5 sm:px-2.5 sm:py-1 text-[10px] font-bold rounded-lg transition-all border cursor-pointer shadow-3xs shrink-0 ${
        copied 
          ? 'bg-emerald-50 border-emerald-200 text-emerald-700 hover:bg-emerald-100' 
          : 'bg-white border-slate-200 text-slate-650 hover:bg-slate-50 hover:text-slate-800'
      }`}
      title={label}
    >
      {copied ? (
        <>
          <Check className="w-3 h-3 text-emerald-600 shrink-0" />
          <span>Tersalin!</span>
        </>
      ) : (
        <>
          <Copy className="w-3 h-3 text-slate-400 shrink-0" />
          <span>{label}</span>
        </>
      )}
    </button>
  );
}

interface BulkImportPanelProps {
  state: AppState;
  onStateChange: (updated: AppState) => void;
  utcTime: string;
}

type ImportType = 'customer' | 'tipe' | 'ukuran' | 'kontainer' | 'tarif' | 'sewa' | 'pembayaran' | 'pranota' | 'pelunasan';

export default function BulkImportPanel({ state, onStateChange, utcTime }: BulkImportPanelProps) {
  const isSewaIn = true;
  const [importType, setImportType] = useState<ImportType>('customer');
  const [importText, setImportText] = useState('');
  const [logs, setLogs] = useState<Array<{ lineNum: number; raw: string; error: string }>>([]);
  const [successCount, setSuccessCount] = useState<number | null>(null);
  const [confirmResetEmpty, setConfirmResetEmpty] = useState(false);
  const [confirmResetDemo, setConfirmResetDemo] = useState(false);
  const [noticeMsg, setNoticeMsg] = useState<string | null>(null);

  const getTemplatePlaceholder = () => {
    switch (importType) {
      case 'customer':
        return isSewaIn 
          ? `# Format: [Nama Vendor / Owner] (Satu per baris)\nPT. Temas Line\nCV. Jayasampurna\nPelayaran Meratus Cargo`
          : `# Format: [Nama Customer] (Satu per baris)\nCV. Samudera Raya\nPT. Lintas Cargo Jaya\nMeratus Line TBK`;
      case 'tipe':
        return `# Format: [Nama Tipe] (Satu per baris)\nDry\nReefer\nFlat Rack\nOpen Top`;
      case 'ukuran':
        return `# Format: [Ukuran Kontainer] (Cukup angka, sistem otomatis tambah petik)\n20\n40\n45`;
      case 'kontainer':
        return isSewaIn
          ? `# Format: NO_KONTAINER ; NAMA_VENDOR_PEMILIK ; NAMA_TIPE ; UKURAN\n# Pemisah menggunakan Titik-Koma ( ; )\nAMFU3153692 ; PT. Temas Line ; Dry ; 20\nGLDU7252828 ; CV. Jayasampurna ; Reefer ; 40`
          : `# Format: NO_KONTAINER ; NAMA_CUSTOMER ; NAMA_TIPE ; UKURAN\n# Pemisah menggunakan Titik-Koma ( ; )\nAMFU3153692 ; CV. Samudera Raya ; Dry ; 20\nGLDU7252828 ; PT. Lintas Cargo Jaya ; Reefer ; 40`;
      case 'tarif':
        return isSewaIn
          ? `# Format: NAMA_VENDOR ; NAMA_TIPE ; UKURAN ; TARIF_BULANAN_VENDOR ; TARIF_HARIAN_VENDOR ; TGL_MULAI_BERLAKU(dd/mm/yyyy)\nPT. Temas Line ; Dry ; 20 ; 3000000 ; 150000 ; 01/01/2022\nCV. Jayasampurna ; Reefer ; 40 ; 6000000 ; 300000 ; 22/04/2024`
          : `# Format: NAMA_CUSTOMER ; NAMA_TIPE ; UKURAN ; TARIF_BULANAN ; TARIF_HARIAN ; TGL_MULAI_BERLAKU(dd/mm/yyyy)\nCV. Samudera Raya ; Dry ; 20 ; 3000000 ; 150000 ; 01/01/2022\nPT. Lintas Cargo Jaya ; Reefer ; 40 ; 6000000 ; 300000 ; 22/04/2024`;
      case 'sewa':
        return isSewaIn
          ? `# Format: NO_KONTAINER ; NAMA_VENDOR_PEMILIK ; TGL_SEWA(dd/mm/yyyy atau KOSONG untuk update kembali) ; TGL_KEMBALI(dd/mm/yyyy) ; BULANAN/HARIAN ; PPN (optional)\nAMFU3153692 ; PT. Temas Line ; 30/09/2022 ; 10/05/2023 ; Bulanan\nGLDU7252828 ; CV. Jayasampurna ; ; 14/06/2026 ; Bulanan ; tidak`
          : `# Format: NO_KONTAINER ; NAMA_CUSTOMER ; TGL_SEWA(dd/mm/yyyy atau KOSONG untuk update kembali) ; TGL_KEMBALI(dd/mm/yyyy) ; BULANAN/HARIAN ; PPN (optional)\n# TIPS: Kosongkan TGL_SEWA jika hanya ingin mengisi TGL_KEMBALI pada transaksi sewa kontainer yang sedang aktif!\nAMFU3153692 ; CV. Samudera Raya ; 30/09/2022 ; 10/05/2023 ; Bulanan\nGLDU7252828 ; PT. Lintas Cargo Jaya ; ; 14/06/2026 ; Bulanan ; tidak`;
      case 'pembayaran':
        return isSewaIn
          ? `# Format Baru: KONTAINER ; PERIODE ; AWAL ; AKHIR ; TAGIHAN_VENDOR ; No. Invoice Vendor ; Tgl. Invoice\n# Contoh:\nBHSU2002332 ; 1 ; 30 Apr 24 ; 29 Mei 24 ; 675.676 ; ZONA260131368 ; 10 Jan 26`
          : `# Format Baru: KONTAINER ; PERIODE ; AWAL ; AKHIR ; TAGIHAN ; No. Tagihan ; Tgl. Tagihan\n# Contoh:\nBHSU2002332 ; 1 ; 30 Apr 24 ; 29 Mei 24 ; 675.676 ; ZONA260131368 ; 10 Jan 26`;
      case 'pranota':
        return `# Format Impor Pranota (No. Tagihan ; Tgl. Tagihan ; No. Pranota ; Tgl. Pranota ; Nilai Real (sebelum ppn & pph))\n# Contoh:\nZONA260131368 ; 10 Jan 26 ; PRANOTA-001 ; 12 Jan 26 ; 680.000`;
      case 'pelunasan':
        return `# Format Impor Pembayaran (No. Pranota ; Tgl. Pranota ; No. Pembayaran ; Tgl. Pembayaran ; Nilai Real (sebelum ppn & pph))\n# Contoh:\nPRANOTA-001 ; 12 Jan 26 ; BYR-TEMAS-01 ; 15 Jan 26 ; 680.000`;
    }
  };

  const handleImport = () => {
    const lines = importText.split('\n');
    const failedLines: string[] = [];
    const newLogs: Array<{ lineNum: number; raw: string; error: string }> = [];
    
    let tempState = { ...state };
    let success = 0;

    // Helper functions for STRICT lookup (canceling auto-creation for safety check as requested)
    const getCustomerStrict = (name: string): string => {
      const cleanName = name.trim();
      const cust = tempState.customers.find(c => c.nama_customer.toLowerCase() === cleanName.toLowerCase());
      if (!cust) {
        throw new Error(`${isSewaIn ? 'Vendor/Owner' : 'Customer'} "${cleanName}" tidak ditemukan di database Master. Harap daftarkan terlebih dahulu di 1. Master ${isSewaIn ? 'Vendor' : 'Customer'} untuk meminimalkan typo.`);
      }
      return cust.id_customer;
    };

    const getTipeStrict = (name: string): string => {
      const cleanName = name.trim();
      const tp = tempState.tipes.find(t => t.nama_tipe.toLowerCase() === cleanName.toLowerCase());
      if (!tp) {
        throw new Error(`Tipe Kontainer "${cleanName}" tidak ditemukan di database Master. Harap daftarkan tipe ini terlebih dahulu di 2. Master Tipe.`);
      }
      return tp.id_tipe;
    };

    const getUkuranStrict = (desc: string): string => {
      let cleanDesc = desc.trim();
      if (/^\d+$/.test(cleanDesc)) {
        cleanDesc = `${cleanDesc}'`; // Auto append tick
      }
      const sz = tempState.ukurans.find(u => u.deskripsi_ukuran === cleanDesc);
      if (!sz) {
        throw new Error(`Ukuran Kontainer "${cleanDesc}" tidak ditemukan di database Master. Harap daftarkan ukuran ini terlebih dahulu di 3. Master Ukuran.`);
      }
      return sz.id_ukuran;
    };

    lines.forEach((line, index) => {
      const lineNum = index + 1;
      const trimmed = line.trim();
      
      // Skip comments or empty lines
      if (!trimmed || trimmed.startsWith('#')) {
        return;
      }

      // Skip header lines
      if (trimmed.toLowerCase().startsWith('kontainer') || trimmed.toLowerCase().startsWith('no buk') || trimmed.toLowerCase().startsWith('no_kontainer')) {
        return;
      }

      try {
        switch (importType) {
          case 'customer': {
            if (tempState.customers.some(c => c.nama_customer.toLowerCase() === trimmed.toLowerCase())) {
              throw new Error(`Customer "${trimmed}" sudah terdaftar`);
            }
            const newCust: Customer = {
              id_customer: 'cust_' + Date.now() + '_' + Math.random().toString(36).substr(2, 5),
              nama_customer: trimmed
            };
            tempState.customers = [...tempState.customers, newCust];
            success++;
            break;
          }

          case 'tipe': {
            if (tempState.tipes.some(t => t.nama_tipe.toLowerCase() === trimmed.toLowerCase())) {
              throw new Error(`Tipe "${trimmed}" sudah terdaftar`);
            }
            const newTipe: TipeKontainer = {
              id_tipe: 'tipe_' + Date.now() + '_' + Math.random().toString(36).substr(2, 5),
              nama_tipe: trimmed
            };
            tempState.tipes = [...tempState.tipes, newTipe];
            success++;
            break;
          }

          case 'ukuran': {
            let formatted = trimmed;
            if (/^\d+$/.test(trimmed)) {
              formatted = `${trimmed}'`;
            }
            if (tempState.ukurans.some(u => u.deskripsi_ukuran === formatted)) {
              throw new Error(`Ukuran "${formatted}" sudah terdaftar`);
            }
            const newSize: UkuranKontainer = {
              id_ukuran: 'sz_' + Date.now() + '_' + Math.random().toString(36).substr(2, 5),
              deskripsi_ukuran: formatted
            };
            tempState.ukurans = [...tempState.ukurans, newSize];
            success++;
            break;
          }

          case 'kontainer': {
            const parts = trimmed.split(';');
            if (parts.length < 4) {
              throw new Error('Format salah. Wajib berisi 4 kolom dipisahkan oleh titik koma (;)');
            }
            const kontNo = parts[0].trim().toUpperCase().replace(/\s+/g, '');
            const custName = parts[1].trim();
            const tipeName = parts[2].trim();
            const ukuranDesc = parts[3].trim();

            if (!kontNo) throw new Error('Nomor Kontainer tidak boleh kosong');
            if (tempState.kontainers.some(k => k.no_kontainer === kontNo)) {
              throw new Error(`Kontainer "${kontNo}" sudah terdaftar dalam sistem`);
            }

            const id_customer = getCustomerStrict(custName);
            const id_tipe = getTipeStrict(tipeName);
            const id_ukuran = getUkuranStrict(ukuranDesc);

            const newKontainer: Kontainer = {
              no_kontainer: kontNo,
              id_customer,
              id_tipe,
              id_ukuran,
              status_aktif: true
            };
            tempState.kontainers = [...tempState.kontainers, newKontainer];
            success++;
            break;
          }

          case 'tarif': {
            const parts = trimmed.split(';');
            if (parts.length < 5) {
              throw new Error('Format salah. Kolom pendaftaran wajib beranggotakan minimal: Customer; Tipe; Ukuran; TarifBulan; TarifHari; [AwalBerlaku]');
            }
            const custName = parts[0].trim();
            const tipeName = parts[1].trim();
            const ukuranDesc = parts[2].trim();
            const monthlyPrice = parseFloat(parts[3].trim()) || 0;
            const dailyPrice = parseFloat(parts[4].trim()) || 0;
            const dateVal = parts[5] ? parts[5].trim() : '';

            if (monthlyPrice <= 0 && dailyPrice <= 0) {
              throw new Error('Tarif Bulanan atau Tarif Harian wajib bernilai lebih dari nol.');
            }

            const id_customer = getCustomerStrict(custName);
            const id_tipe = getTipeStrict(tipeName);
            const id_ukuran = getUkuranStrict(ukuranDesc);

            const startIso = dateVal ? parseInputDate(dateVal) : utcTime.split('T')[0];
            if (!startIso) {
              throw new Error(`Format tanggal "${dateVal}" tidak valid. Wajib menggunakan dd/mm/yyyy`);
            }

            const newTarif: TarifSewa = {
              id_tarif: 'trf_' + Date.now() + '_' + Math.random().toString(36).substr(2, 5),
              id_customer,
              id_tipe,
              id_ukuran,
              tarif_bulanan: monthlyPrice,
              tarif_harian: dailyPrice,
              tanggal_mulai_berlaku: startIso,
              tanggal_akhir_berlaku: null
            };

            tempState.tarifs = [...tempState.tarifs, newTarif];
            success++;
            break;
          }

          case 'sewa': {
            const parts = trimmed.split(';');
            const kontNo = parts[0] ? parts[0].trim().toUpperCase().replace(/\s+/g, '') : '';
            const custName = parts[1] ? parts[1].trim() : '';
            const dateSewaRaw = parts[2] ? parts[2].trim() : '';
            const dateKembaliRaw = parts[3] ? parts[3].trim() : '';
            const rawJenisTarif = parts[4] ? parts[4].trim().toLowerCase() : 'bulanan';
            const rawPpn = parts[5] ? parts[5].trim().toLowerCase() : '';
            const isNonPpn = rawPpn === 'tidak';

            if (!kontNo) throw new Error('No Kontainer kosong');
            
            // Check container presence
            const kontObj = tempState.kontainers.find(k => k.no_kontainer === kontNo);
            if (!kontObj) {
              throw new Error(`Kontainer "${kontNo}" tidak terdaftar di Master. Wajib didaftarkan dahulu!`);
            }

            // SUPPORT BLANK START DATE: If start date is blank but return date is specified, update return date on active rental!
            if (!dateSewaRaw) {
              if (dateKembaliRaw) {
                const activeSewaIndex = tempState.sewas.findIndex(s => s.no_kontainer === kontNo && s.status_sewa === 'Aktif');
                if (activeSewaIndex === -1) {
                  throw new Error(`Kontainer "${kontNo}" tidak memiliki transaksi sewa aktif di sistem untuk diperbarui.`);
                }
                const endIso = parseInputDate(dateKembaliRaw);
                if (!endIso) {
                  throw new Error(`Format tanggal kembali "${dateKembaliRaw}" tidak valid. Wajib dd/mm/yyyy`);
                }
                const startIso = tempState.sewas[activeSewaIndex].tanggal_sewa;
                if (endIso < startIso) {
                  throw new Error(`Tanggal kembali (${dateKembaliRaw}) tidak boleh sebelum tanggal sewa (${startIso}).`);
                }
                
                // Update in place
                const updatedSewas = [...tempState.sewas];
                updatedSewas[activeSewaIndex] = {
                  ...updatedSewas[activeSewaIndex],
                  tanggal_kembali: endIso,
                  status_sewa: 'Selesai',
                  non_ppn: isNonPpn ? true : updatedSewas[activeSewaIndex].non_ppn
                };
                tempState.sewas = updatedSewas;
                success++;
                break;
              } else {
                throw new Error('Kedua tanggal (Sewa & Kembali) kosong.');
              }
            }

            const id_customer = getCustomerStrict(custName);
            const startIso = parseInputDate(dateSewaRaw);
            if (!startIso) {
              throw new Error(`Format tanggal sewa "${dateSewaRaw}" tidak valid. Wajib dd/mm/yyyy`);
            }

            let endIso: string | null = null;
            if (dateKembaliRaw) {
              endIso = parseInputDate(dateKembaliRaw);
              if (!endIso) {
                throw new Error(`Format tanggal kembali "${dateKembaliRaw}" tidak valid. Wajib dd/mm/yyyy`);
              }
              if (endIso < startIso) {
                throw new Error(`Tanggal kembali tidak boleh kurang dari sewa.`);
              }
            }

            const rentMode: 'Bulanan' | 'Harian' = rawJenisTarif === 'harian' ? 'Harian' : 'Bulanan';

            // Find matching Master Tarif
            const matchTarif = tempState.tarifs.find(t => 
              t.id_customer === id_customer &&
              t.id_tipe === kontObj.id_tipe &&
              t.id_ukuran === kontObj.id_ukuran &&
              t.tanggal_akhir_berlaku === null
            );

            const monthlyPrice = matchTarif ? matchTarif.tarif_bulanan : 3000000; // default standard if missing
            const dailyPrice = matchTarif ? matchTarif.tarif_harian : 150000;

            const getExcelSerialDate = (isoStr: string): number => {
              const d = new Date(isoStr);
              const epoch = new Date('1899-12-30');
              const diffMs = d.getTime() - epoch.getTime();
              return Math.floor(diffMs / (24 * 60 * 60 * 1000));
            };
            const listExisting = tempState.sewas.filter(s => s.no_kontainer === kontNo);
            const cycleNum = listExisting.length + 1;
            const cycleNumStr = String(cycleNum).padStart(2, '0');
            const excelDateSerial = getExcelSerialDate(startIso);
            const uniqueIdSewa = `${kontNo}${excelDateSerial}${cycleNumStr}`;

            const newSewa: Sewa = {
              id_sewa: uniqueIdSewa,
              no_kontainer: kontNo,
              id_customer,
              tanggal_sewa: startIso,
              tanggal_kembali: endIso,
              tarif_bulanan: monthlyPrice,
              tarif_harian: dailyPrice,
              jenis_tarif: rentMode,
              status_sewa: endIso ? 'Selesai' : 'Aktif',
              non_ppn: isNonPpn
            };

            // Check parallel rules: Ensure we don't have overlapping active rentals (returned allows renting again day itself!)
            const activeMatch = tempState.sewas.find(s => s.no_kontainer === kontNo && (s.status_sewa === 'Aktif' || !s.tanggal_kembali));
            if (activeMatch) {
              throw new Error(`Kontainer "${kontNo}" belum dikembalikan dari sewa sebelumnya (masih aktif). Isi tanggal kembali transaksi sebelumnya terlebih dahulu sebelum merekam sewa baru.`);
            }

            // Duplicate check: same container and same start date
            const isDup = tempState.sewas.some(s => s.no_kontainer === kontNo && s.tanggal_sewa === startIso);
            if (isDup) {
              throw new Error(`Double / Duplikat: Transaksi sewa untuk Kontainer "${kontNo}" dengan Tanggal Mulai "${dateSewaRaw}" sudah pernah tercatat/diimpor sebelumnya.`);
            }

            tempState.sewas = [...tempState.sewas, newSewa];
            success++;
            break;
          }

          case 'pembayaran': {
            const parts = trimmed.split(';');
            if (parts.length < 3) {
              throw new Error('Format salah. Wajib berisi minimal: KONTAINER ; PERIODE ; TAGIHAN ; [No. Tagihan] ; [Tgl. Tagihan]');
            }

            const kontNo = parts[0].trim().toUpperCase().replace(/\s+/g, '');
            if (!kontNo) throw new Error('No Kontainer tidak boleh kosong');

            // Robust number cleaner for currency input (Indonesian format friendly)
            const cleanNumber = (val: string): number => {
              let s = val.trim().replace(/[Rp$\s]/gi, '');
              if (s.includes('.') && s.includes(',')) {
                s = s.replace(/\./g, '').replace(/,/g, '.');
              } else if (s.includes(',')) {
                const subParts = s.split(',');
                if (subParts.length > 2 || (subParts.length === 2 && subParts[1].length === 3)) {
                  s = s.replace(/,/g, '');
                } else {
                  s = s.replace(/,/g, '.');
                }
              } else if (s.includes('.')) {
                const subParts = s.split('.');
                if (subParts.length > 2 || (subParts.length === 2 && subParts[1].length === 3)) {
                  s = s.replace(/\./g, '');
                }
              }
              return parseFloat(s) || 0;
            };

            // Detect format type:
            // New format has a valid date in column 3 (index 2) e.g. "30 Apr 24"
            const isNewFormat = parseInputDate(parts[2]) !== null;

            let periodNum = NaN;
            let tagihanBilledPrice = 0;
            let noTagihan = '';
            let tglTagihanRaw = '';
            let awalRaw = '';
            let akhirRaw = '';
            let awalIso: string | null = null;

            if (isNewFormat) {
              // New Format: Kontainer ; Periode ; Awal ; Akhir ; Tagihan ; NoTagihan ; Tgl. Tagihan
              if (parts.length < 5) {
                throw new Error('Format baru salah. Wajib minimal 5 kolom: KONTAINER ; PERIODE ; AWAL ; AKHIR ; TAGIHAN ; [No. Tagihan] ; [Tgl. Tagihan]');
              }
              periodNum = parseInt(parts[1].trim(), 10);
              awalRaw = parts[2].trim();
              akhirRaw = parts[3].trim();
              tagihanBilledPrice = cleanNumber(parts[4]);
              noTagihan = parts[5] ? parts[5].trim() : '';
              tglTagihanRaw = parts[6] ? parts[6].trim() : '';
              
              awalIso = parseInputDate(awalRaw);
            } else {
              // Old Format: KONTAINER ; PERIODE ; TAGIHAN ; No. Tagihan ; Tgl. Tagihan ; [SIKLUS_KE]
              periodNum = parseInt(parts[1].trim(), 10);
              tagihanBilledPrice = cleanNumber(parts[2]);
              noTagihan = parts[3] ? parts[3].trim() : '';
              tglTagihanRaw = parts[4] ? parts[4].trim() : '';
            }

            if (isNaN(periodNum)) throw new Error('Periode harus berupa angka');
            const tglTagihanIso = parseInputDate(tglTagihanRaw) || utcTime.split('T')[0];

            // Find all Sewas matching this container
            const matchedSewaList = [...tempState.sewas.filter(s => s.no_kontainer === kontNo)]
              .sort((a, b) => a.tanggal_sewa.localeCompare(b.tanggal_sewa));

            if (matchedSewaList.length === 0) {
              throw new Error(`Kontainer "${kontNo}" tidak memiliki transaksi sewa apa pun.`);
            }

            let selectedSewa = null;
            let matchedPeriod = null;

            if (isNewFormat && awalIso) {
              // 1. Search for exact match across ALL sewas for this container using the New format logic
              for (const sw of matchedSewaList) {
                const swPeriods = compileAllPeriods({
                  ...tempState,
                  sewas: [sw]
                }, utcTime);
                const pFound = swPeriods.find(cp => cp.bulan_ke === periodNum && cp.tanggal_awal === awalIso);
                if (pFound) {
                  selectedSewa = sw;
                  matchedPeriod = pFound;
                  break;
                }
              }
            } else {
              // 2. Old Format / Fallback matching logic
              // Check if user specified cycle selection in parts[5] (6th column)
              const cycleSelectRaw = parts[5] ? parts[5].trim() : '';
              if (cycleSelectRaw) {
                const parsedIdx = parseInt(cycleSelectRaw, 10);
                if (!isNaN(parsedIdx)) {
                  if (parsedIdx < 1 || parsedIdx > matchedSewaList.length) {
                    throw new Error(`Opsi Siklus Ke-${parsedIdx} tidak valid. Kontainer ${kontNo} hanya memiliki ${matchedSewaList.length} siklus sewa.`);
                  }
                  selectedSewa = matchedSewaList[parsedIdx - 1];
                } else {
                  const parsedDate = parseInputDate(cycleSelectRaw);
                  if (parsedDate) {
                    const match = matchedSewaList.find(s => s.tanggal_sewa === parsedDate);
                    if (match) selectedSewa = match;
                    else throw new Error(`Siklus sewa dengan tanggal mulai "${cycleSelectRaw}" (${parsedDate}) tidak ditemukan untuk kontainer ${kontNo}.`);
                  } else {
                    throw new Error(`Format opsi siklus "${cycleSelectRaw}" tidak valid. Masukkan angka atau tanggal mulai sewa (dd/mm/yyyy).`);
                  }
                }
              } else {
                // Auto-match by invoice date falling within the sewa contract date range
                const overlapList = matchedSewaList.filter(s => {
                  const tSewa = new Date(s.tanggal_sewa).getTime();
                  const tKembali = s.tanggal_kembali ? new Date(s.tanggal_kembali).getTime() : new Date('2050-12-31').getTime();
                  const tInvoice = new Date(tglTagihanIso).getTime();
                  return tInvoice >= tSewa && tInvoice <= tKembali;
                });

                if (overlapList.length === 1) {
                  selectedSewa = overlapList[0];
                } else if (matchedSewaList.length > 1) {
                  const candidateSewasWithPeriod = matchedSewaList.filter(sw => {
                    const allPeriods = compileAllPeriods({
                      ...tempState,
                      sewas: [sw]
                    }, utcTime);
                    return allPeriods.some(cp => cp.bulan_ke === periodNum);
                  });

                  if (candidateSewasWithPeriod.length === 1) {
                    selectedSewa = candidateSewasWithPeriod[0];
                  } else if (candidateSewasWithPeriod.length > 1) {
                    const cyclesInfo = candidateSewasWithPeriod.map(sw => {
                      const opIndex = matchedSewaList.indexOf(sw) + 1;
                      const startStr = formatIndoDate(sw.tanggal_sewa);
                      const endStr = sw.tanggal_kembali ? formatIndoDate(sw.tanggal_kembali) : 'Aktif / Berjalan';
                      const partnerName = tempState.customers.find(c => c.id_customer === sw.id_customer)?.nama_customer || 'Tidak diketahui';
                      return `• Siklus ${opIndex}: ${startStr} s.d ${endStr} (${isSewaIn ? 'Vendor' : 'Penyewa'}: ${partnerName})`;
                    }).join(', ');

                    throw new Error(`KONFLIK SIKLUS: Kontainer "${kontNo}" memiliki ${candidateSewasWithPeriod.length} siklus sewa yang memiliki periode ke-${periodNum} (${cyclesInfo}). Harap tentukan siklus sewa di akhir baris Anda dengan menambahkan "; [No_Siklus]" (contoh: "; 2" untuk siklus kedua).`);
                  }
                }
              }

              if (!selectedSewa && matchedSewaList.length === 1) {
                selectedSewa = matchedSewaList[0];
              }
              if (!selectedSewa) {
                selectedSewa = matchedSewaList[matchedSewaList.length - 1];
              }

              // Compile periods for the selected sewa in old format
              const swPeriods = compileAllPeriods({
                ...tempState,
                sewas: [selectedSewa]
              }, utcTime);
              matchedPeriod = swPeriods.find(cp => cp.bulan_ke === periodNum);
            }

            // If matched period wasn't found under the chosen sewa
            if (!matchedPeriod) {
              const allAvailablePeriods = matchedSewaList.flatMap((sw, idx) => {
                const swPeriods = compileAllPeriods({
                  ...tempState,
                  sewas: [sw]
                }, utcTime);
                return swPeriods.map(cp => ({
                  cycleIndex: idx + 1,
                  bulan_ke: cp.bulan_ke,
                  tanggal_awal: cp.tanggal_awal,
                  tanggal_akhir: cp.tanggal_akhir
                }));
              });

              const availableListText = allAvailablePeriods.map(ap => 
                `• Siklus ${ap.cycleIndex}, Periode ${ap.bulan_ke}: ${formatIndoDate(ap.tanggal_awal)} s.d ${formatIndoDate(ap.tanggal_akhir)}`
              ).join('\n');

              throw new Error(`Kombinasi Kontainer "${kontNo}" dengan Periode ke-${periodNum} ${isNewFormat ? `dan Tanggal Awal "${awalRaw}" (${awalIso || 'tidak valid'})` : ''} tidak cocok dengan jadwal tagihan sistem.\n\nJadwal tagihan yang tersedia untuk kontainer ini:\n${availableListText || '• Tidak ada jadwal tagihan terpapar.'}`);
            }

            const idTagihan = matchedPeriod.id_tagihan;
            const originalEstimatedTagihan = matchedPeriod.jumlah_tagihan; // Sistem's estimation
            const selisih = tagihanBilledPrice - originalEstimatedTagihan;

            // Global Duplicate Check: If this invoice number is already recorded for this container & period under any of its cycles
            if (noTagihan) {
              for (const sw of matchedSewaList) {
                const swPeriods = compileAllPeriods({
                  ...tempState,
                  sewas: [sw]
                }, utcTime);
                const pFound = swPeriods.find(cp => cp.bulan_ke === periodNum);
                if (pFound) {
                  const ov = tempState.paymentOverrides[pFound.id_tagihan];
                  if (ov && ov.nomor_invoice_grup && ov.nomor_invoice_grup.toLowerCase() === noTagihan.toLowerCase()) {
                    const swIdx = matchedSewaList.indexOf(sw) + 1;
                    const swStart = formatIndoDate(sw.tanggal_sewa);
                    const swEnd = sw.tanggal_kembali ? formatIndoDate(sw.tanggal_kembali) : 'Aktif / Berjalan';
                    throw new Error(`Double / Duplikat: Tagihan dengan No. Tagihan "${noTagihan}" untuk Kontainer "${kontNo}" periode ke-${periodNum} (Siklus ${swIdx}: ${swStart} s.d ${swEnd}) sudah pernah diimpor/tercatat sebelumnya.`);
                  }
                }
              }
            }

            // Specific duplicate check for the resolved period override status or invoice mapping
            const existingOverrideObj = tempState.paymentOverrides[idTagihan];
            if (existingOverrideObj && (existingOverrideObj.status_bayar !== 'Belum Ditagih' || existingOverrideObj.nomor_invoice_grup)) {
              const swIdx = matchedSewaList.indexOf(selectedSewa || matchedSewaList[0]) + 1;
              const swStart = formatIndoDate((selectedSewa || matchedSewaList[0]).tanggal_sewa);
              const swEnd = (selectedSewa || matchedSewaList[0]).tanggal_kembali ? formatIndoDate((selectedSewa || matchedSewaList[0]).tanggal_kembali) : 'Aktif / Berjalan';
              throw new Error(`Double / Duplikat: Tagihan untuk Kontainer "${kontNo}" periode ke-${periodNum} (Siklus ${swIdx}: ${swStart} s.d ${swEnd}) sudah terisi/tercatat sebelumnya dengan No. Tagihan "${existingOverrideObj.nomor_invoice_grup || '-'}".`);
            }

            const existingOverride = existingOverrideObj || {
              status_bayar: 'Belum Ditagih',
              tanggal_tagihan: null,
              tanggal_bayar: null,
              nomor_invoice_grup: null
            };

            const calculatedPpn = Math.round(tagihanBilledPrice * 0.11);
            const calculatedPph = Math.round(tagihanBilledPrice * 0.02);

            tempState.paymentOverrides[idTagihan] = {
              ...existingOverride,
              status_bayar: 'Belum Bayar', // Change from 'Pranota' to 'Belum Bayar' (Tagihan) so it is correctly treated as "tagihan"
              tanggal_tagihan: tglTagihanIso,
              nomor_invoice_grup: noTagihan || null,
              jumlah_tagihan_override: tagihanBilledPrice,
              selisih_pembayaran: selisih,
              ppn: calculatedPpn,
              pph: calculatedPph,
              keterangan_selisih: selisih !== 0 ? 'Selisih harga dari impor' : null
            };

            success++;
            break;
          }

          case 'pranota': {
            let parts = trimmed.split(/[\t;]+/);
            if (parts.length === 1 && parts[0].includes(',')) {
              parts = parts[0].split(',');
            }

            if (parts.length < 4) {
              throw new Error('Format salah. Wajib berisi minimal: No. Tagihan ; Tgl. Tagihan ; No. Pranota ; Tgl. Pranota [; Nilai Real] [; Keterangan]');
            }

            const noTagihan = parts[0].trim();
            const tglTagihanRaw = parts[1].trim();
            const noPranota = parts[2].trim();
            const tglPranotaRaw = parts[3].trim();
            const nilaiRealRaw = parts[4] ? parts[4].trim() : '';
            const ketSelisih = parts[5] ? parts[5].trim() : '';

            // Skip headers
            const isHeader = 
              /^(no|nomor|no\s*tagihan|tagihan|invoice|status)$/i.test(noTagihan) ||
              /^(tanggal|tgl|format|date)$/i.test(tglTagihanRaw) ||
              /^(no\s*pranota|pranota)$/i.test(noPranota);

            if (isHeader) {
              break;
            }

            if (!noTagihan) throw new Error('Nomor Tagihan tidak boleh kosong');
            if (!noPranota) throw new Error('Nomor Pranota tidak boleh kosong');

            // Find matching periods
            const allPeriods = compileAllPeriods(tempState, utcTime);
            const matchedPeriods = allPeriods.filter(p => p.nomor_invoice_grup && p.nomor_invoice_grup.toLowerCase() === noTagihan.toLowerCase());

            if (matchedPeriods.length === 0) {
              throw new Error(`Nomor Tagihan "${noTagihan}" tidak ditemukan.`);
            }

            // Parse dates
            const parsedTglTagihan = parseInputDate(tglTagihanRaw) || utcTime.split('T')[0];
            const parsedTglPranota = parseInputDate(tglPranotaRaw) || utcTime.split('T')[0];

            // Clean numbers helper
            const cleanNum = (val: string): number => {
              let s = val.trim().replace(/[Rp$\s]/gi, '');
              if (s.includes('.') && s.includes(',')) {
                s = s.replace(/\./g, '').replace(/,/g, '.');
              } else if (s.includes(',')) {
                const subParts = s.split(',');
                if (subParts.length > 2 || (subParts.length === 2 && subParts[1].length === 3)) {
                  s = s.replace(/,/g, '');
                } else {
                  s = s.replace(/,/g, '.');
                }
              } else if (s.includes('.')) {
                const subParts = s.split('.');
                if (subParts.length > 2 || (subParts.length === 2 && subParts[1].length === 3)) {
                  s = s.replace(/\./g, '');
                }
              }
              return s ? parseFloat(s) || 0 : 0;
            };

            const nilaiRealTotal = cleanNum(nilaiRealRaw);
            const valuePerPeriod = matchedPeriods.length > 0 ? Math.round(nilaiRealTotal / matchedPeriods.length) : 0;

            matchedPeriods.forEach(item => {
              const existing = tempState.paymentOverrides[item.id_tagihan] || {
                status_bayar: 'Belum Ditagih',
                tanggal_tagihan: null,
                tanggal_bayar: null,
                nomor_invoice_grup: null,
                nomor_bayar: null
              };

              const diff = valuePerPeriod - item.jumlah_tagihan;

              tempState.paymentOverrides[item.id_tagihan] = {
                ...existing,
                status_bayar: 'Pranota',
                nomor_pranota: noPranota,
                tanggal_pranota: parsedTglPranota,
                nomor_invoice_grup: noTagihan,
                tanggal_tagihan: parsedTglTagihan,
                jumlah_tagihan_override: valuePerPeriod,
                selisih_pembayaran: diff,
                keterangan_selisih: null // Ignored on import, user will finalize via manual entry
              };
            });

            success++;
            break;
          }

          case 'pelunasan': {
            let parts = trimmed.split(/[\t;]+/);
            if (parts.length === 1 && parts[0].includes(',')) {
              parts = parts[0].split(',');
            }

            if (parts.length < 4) {
              throw new Error('Format salah. Wajib berisi minimal: No. Pranota ; Tgl. Pranota ; No. Pembayaran ; Tgl. Pembayaran [; Nilai Real] [; Keterangan]');
            }

            const noPranota = parts[0].trim();
            const tglPranotaRaw = parts[1].trim();
            const noPembayaran = parts[2].trim();
            const tglPembayaranRaw = parts[3].trim();
            const nilaiRealRaw = parts[4] ? parts[4].trim() : '';
            const ketSelisih = parts[5] ? parts[5].trim() : '';

            // Skip headers
            const isHeader = 
              /^(no|nomor|no\s*pranota|pranota)$/i.test(noPranota) ||
              /^(tanggal|tgl|format|date)$/i.test(tglPranotaRaw) ||
              /^(no\s*pembayaran|pembayaran|bukti)$/i.test(noPembayaran);

            if (isHeader) {
              break;
            }

            if (!noPranota) throw new Error('Nomor Pranota tidak boleh kosong');
            if (!noPembayaran) throw new Error('Nomor Pembayaran tidak boleh kosong');

            // Find matching periods
            const allPeriods = compileAllPeriods(tempState, utcTime);
            const matchedPeriods = allPeriods.filter(p => p.nomor_pranota && p.nomor_pranota.toLowerCase() === noPranota.toLowerCase());

            if (matchedPeriods.length === 0) {
              throw new Error(`Nomor Pranota "${noPranota}" tidak ditemukan.`);
            }

            // Parse dates
            const parsedTglPranota = parseInputDate(tglPranotaRaw) || utcTime.split('T')[0];
            const parsedTglPembayaran = parseInputDate(tglPembayaranRaw) || utcTime.split('T')[0];

            // Clean numbers helper
            const cleanNum = (val: string): number => {
              let s = val.trim().replace(/[Rp$\s]/gi, '');
              if (s.includes('.') && s.includes(',')) {
                s = s.replace(/\./g, '').replace(/,/g, '.');
              } else if (s.includes(',')) {
                const subParts = s.split(',');
                if (subParts.length > 2 || (subParts.length === 2 && subParts[1].length === 3)) {
                  s = s.replace(/,/g, '');
                } else {
                  s = s.replace(/,/g, '.');
                }
              } else if (s.includes('.')) {
                const subParts = s.split('.');
                if (subParts.length > 2 || (subParts.length === 2 && subParts[1].length === 3)) {
                  s = s.replace(/\./g, '');
                }
              }
              return s ? parseFloat(s) || 0 : 0;
            };

            const nilaiRealTotal = cleanNum(nilaiRealRaw);
            const valuePerPeriod = matchedPeriods.length > 0 ? Math.round(nilaiRealTotal / matchedPeriods.length) : 0;

            matchedPeriods.forEach(item => {
              const existing = tempState.paymentOverrides[item.id_tagihan] || {
                status_bayar: 'Belum Ditagih',
                tanggal_tagihan: null,
                tanggal_bayar: null,
                nomor_invoice_grup: null,
                nomor_bayar: null
              };

              const baseVal = item.jumlah_tagihan_override !== null && item.jumlah_tagihan_override !== undefined ? item.jumlah_tagihan_override : item.jumlah_tagihan;
              const diff = valuePerPeriod - baseVal;

              tempState.paymentOverrides[item.id_tagihan] = {
                ...existing,
                status_bayar: 'Lunas',
                nomor_bayar: noPembayaran,
                tanggal_bayar: parsedTglPembayaran,
                nomor_pranota: noPranota,
                tanggal_pranota: parsedTglPranota,
                jumlah_bayar: valuePerPeriod,
                selisih_pembayaran: diff,
                keterangan_selisih: null // Ignored on import, user will finalize via manual entry
              };
            });

            // Also ensure associated invoice groups are marked as Lunas if all its items are Lunas
            const distinctInvoices = Array.from(new Set(matchedPeriods.map(p => p.nomor_invoice_grup).filter(Boolean)));
            distinctInvoices.forEach(invNo => {
              const existsIdx = tempState.invoices.findIndex(i => i.nomor_invoice.toLowerCase() === invNo.toLowerCase());
              if (existsIdx !== -1) {
                tempState.invoices[existsIdx] = {
                  ...tempState.invoices[existsIdx],
                  status_pembayaran: 'Lunas'
                };
              }
            });

            success++;
            break;
          }
        }
      } catch (err: any) {
        failedLines.push(line);
        newLogs.push({
          lineNum,
          raw: line,
          error: err.message || 'Error tidak diketahui'
        });
      }
    });

    // Save only successes
    onStateChange(tempState);
    setSuccessCount(success);
    setLogs(newLogs);

    // Keep only failed lines in the textarea as requested!
    setImportText(failedLines.join('\n'));
  };

  return (
    <div className="bg-white rounded-2xl border border-slate-100 shadow-xs p-6 space-y-6" id="bulk-importer-container">
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
        <div>
          <h3 className="font-bold text-slate-800 text-sm flex items-center gap-1.5">
            <Upload className="w-5 h-5 text-emerald-600" />
            <span>Impor Rekor Master &amp; Transaksi Cepat (Excel Copy-Paste)</span>
          </h3>
          <p className="text-[11px] text-slate-500 mt-0.5">
            Pilih jenis tabel, salin baris tabel dari Excel/Spreadsheet Anda, tempel ke area teks, lalu klik Impor.
          </p>
        </div>

        {/* Import Selector */}
        <select
          id="select-import-type"
          value={importType}
          onChange={(e) => {
            setImportType(e.target.value as ImportType);
            setImportText('');
            setLogs([]);
            setSuccessCount(null);
          }}
          className="text-xs border border-slate-200 rounded-xl px-3 py-1.5 bg-slate-50/50 text-slate-800 font-semibold cursor-pointer"
        >
          <option value="customer">{isSewaIn ? '1. Master Vendor / Owner' : '1. Master Customer'}</option>
          <option value="tipe">2. Master Tipe Kontainer</option>
          <option value="ukuran">3. Master Ukuran</option>
          <option value="tarif">{isSewaIn ? '4. Master Tarif Sewa In' : '4. Master Tarif Sewa'}</option>
          <option value="kontainer">5. Master Kontainer</option>
          <option value="sewa">{isSewaIn ? '6. Transaksi Sewa In &amp; Kembali' : '6. Transaksi Sewa &amp; Kembali'}</option>
          <option value="pembayaran">7. Impor Tagihan Vendor (Dari Excel Vendor)</option>
          <option value="pranota">8. Impor Pranota</option>
          <option value="pelunasan">9. Impor Pembayaran</option>
        </select>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
             {/* TEXTAREA AND TRIGGER */}
        <div className="lg:col-span-2 space-y-4">
          <div className="flex items-center justify-between text-xs font-semibold text-slate-655">
            <span>Tempel Baris Data Di Bawah Ini:</span>
            <button
              id="btn-load-template"
              onClick={() => setImportText(getTemplatePlaceholder())}
              className="text-[10px] text-emerald-600 hover:text-emerald-800 flex items-center gap-1"
            >
              <Info className="w-3 h-3" /> Muat Contoh Template Baris
            </button>
          </div>

          <textarea
            id="textarea-bulk-import"
            rows={10}
            value={importText}
            onChange={(e) => setImportText(e.target.value)}
            placeholder="Salin/Ketik data di sini..."
            className="w-full font-mono text-xs border border-slate-200 rounded-2xl p-4 bg-slate-50/40 focus:ring-2 focus:ring-emerald-500/20"
          />

          <button
            id="btn-import-submit"
            onClick={handleImport}
            className="w-full inline-flex items-center justify-center bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs py-2.5 px-4 rounded-xl transition-all shadow-xs cursor-pointer"
          >
            Proses &amp; Simpan Otomatis Data Valid
          </button>

          {/* Expanded, non-truncated error highlight list with Copy buttons */}
          {logs.length > 0 && (
            <div className="space-y-3 pt-2" id="import-error-highlights-box">
              <span className="text-xs font-bold text-red-650 flex items-center gap-1.5 bg-red-50 text-red-750 px-3 py-1.5 rounded-lg border border-red-100">
                <CircleAlert className="w-4 h-4 shrink-0 text-red-600 animate-pulse" />
                <span>Terdeteksi {logs.length} Baris Mengalami Kesalahan:</span>
              </span>
              <div className="max-h-[380px] overflow-y-auto space-y-3 divide-y divide-red-100 bg-red-50/20 p-4 rounded-2xl border border-red-100">
                {logs.map((log, i) => (
                  <div key={i} className="pt-3 first:pt-0 flex flex-col sm:flex-row sm:items-start justify-between gap-3 text-xs">
                    <div className="space-y-1.5 flex-1 select-text">
                      <div className="flex flex-wrap items-center gap-1.5 sm:gap-2">
                        <span className="font-extrabold text-red-800 bg-red-100 px-2 py-0.5 rounded text-[10px] font-mono shadow-3xs">
                          ROW {log.lineNum}
                        </span>
                        <span className="text-slate-300 hidden sm:inline">|</span>
                        <span className="text-[11px] font-mono text-slate-600 bg-slate-100 px-2 py-0.5 rounded border border-slate-200 break-all select-all">
                          &quot;{log.raw}&quot;
                        </span>
                      </div>
                      <p className="font-semibold text-rose-700 font-sans leading-relaxed text-[11px] sm:text-[11.5px]">
                        Penyebab: <span className="text-slate-800 font-normal">{log.error}</span>
                      </p>
                    </div>
                    <div className="flex sm:flex-col items-center sm:items-end gap-1.5 shrink-0">
                      <CopyButton textValue={log.raw} label="Salin Baris" />
                      <CopyButton textValue={log.error} label="Salin Error" />
                    </div>
                  </div>
                ))}
              </div>
            </div>
          )}
        </div>

        {/* FEEDBACK STATUS AND ERROR HIGHLIGHT */}
        <div className="lg:col-span-1 space-y-4">
          <div className="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-3">
            <h4 className="text-xs font-bold text-slate-700 flex items-center gap-1">
              <BookOpen className="w-3.5 h-3.5 text-indigo-600" />
              <span>Panduan Validasi &amp; Aturan</span>
            </h4>
            <div className="text-[10px] text-slate-600 space-y-2 leading-relaxed">
              <p>
                <strong>✓ Simpan Otomatis:</strong> Baris yang sukses divalidasi akan langsung disimpan ke database lokal dan dikeluarkan dari layar input.
              </p>
              <p>
                <strong>⚠ Tetap di Layar:</strong> Baris yang salah akan tetap berada di dalam text-area untuk Anda benahi secara manual.
              </p>
              <p>
                <strong>★ Prerrequisite Hub (Ketat):</strong> Kolom nama (customer/vendor, tipe, ukuran) harus sudah terdaftar terlebih dahulu di master. Jika tidak ditemukan, sistem akan membatalkan impor dan memunculkan notifikasi agar Anda dapat mereview salah ketik sebelum data masuk.
              </p>
              <p>
                <strong>★ Update Pengembalian:</strong> Anda dapat mengimpor Tanggal Kembali saja (kosongkan kolom Tanggal Sewa) untuk memperbarui sewa kontainer aktif tanpa perlu input ulang tanggal sewa.
              </p>
            </div>
          </div>

          {/* Success messages */}
          {successCount !== null && (
            <div
              id="import-success-box"
              className="p-3.5 bg-emerald-50 text-emerald-800 border border-emerald-100 rounded-xl text-xs font-semibold flex items-center gap-2"
            >
              <CheckCircle className="w-4 h-4 text-emerald-600 shrink-0" />
              <span>Selesai! {successCount} rekor berhasil didefinisikan dan disimpan.</span>
            </div>
          )}
        </div>
      </div>

      {/* SEPARATOR */}
      <div className="border-t border-slate-150 pt-5 mt-5">
        <div className="bg-slate-50 rounded-2xl border border-slate-200/60 p-5 space-y-4">
          <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
              <h4 className="font-bold text-slate-800 text-xs flex items-center gap-1.5">
                <Trash2 className="w-4 h-4 text-red-600 animate-pulse" />
                <span>MEMELIHARA &amp; MENGELOLA MEMORI DATABASE (Offline-first)</span>
              </h4>
              <p className="text-[10px] text-slate-500 mt-0.5">
                Aplikasi ini berjalan 100% di browser Anda (LocalStorage). Anda dapat mengosongkan semua data dummy untuk mulai mengimpor data asli milik Anda dengan bersih, atau memulihkan data demo kapan saja.
              </p>
            </div>
          </div>

          {noticeMsg && (
            <div id="maintenance-notice" className="p-2.5 bg-yellow-50 text-yellow-800 border border-yellow-100 rounded-xl text-[10px] font-semibold">
              {noticeMsg}
            </div>
          )}

          <div className="flex flex-wrap items-center gap-3 pt-1">
            {/* BUTTON CLEAN SLATE */}
            {!confirmResetEmpty ? (
              <button
                id="btn-trigger-reset-empty"
                onClick={() => {
                  setConfirmResetEmpty(true);
                  setConfirmResetDemo(false);
                }}
                className="inline-flex items-center gap-1.5 bg-red-50 hover:bg-red-100 text-red-700 hover:text-red-800 border border-red-100 text-[11px] font-bold py-1.5 px-3 rounded-lg transition-colors cursor-pointer"
              >
                <Trash2 className="w-3.5 h-3.5" />
                Kosongkan Semua Data &amp; Mulai Bersih (0 Unit)
              </button>
            ) : (
              <div className="flex items-center gap-2 bg-red-50 border border-red-100 p-2 rounded-xl text-[10px]">
                <span className="text-red-700 font-bold">Yakin ingin menghapus seluruh pelanggan, sewa, master kontainer &amp; tarif?</span>
                <button
                  id="btn-confirm-reset-empty"
                  onClick={() => {
                    onStateChange(getEmptyAppState());
                    setConfirmResetEmpty(false);
                    setNoticeMsg('Database berhasil dikosongkan secara permanen! Sekarang Anda memiliki 0 data (Bersih). Silakan mulai mengimpor master atau transaksi Anda.');
                    setTimeout(() => setNoticeMsg(null), 8000);
                  }}
                  className="bg-red-600 text-white font-bold px-2 py-1 rounded-md text-[9px] hover:bg-red-700 cursor-pointer"
                >
                  Ya, Bersihkan Semua
                </button>
                <button
                  id="btn-cancel-reset-empty"
                  onClick={() => setConfirmResetEmpty(false)}
                  className="bg-slate-200 text-slate-700 font-bold px-2 py-1 rounded-md text-[9px] hover:bg-slate-300 cursor-pointer"
                >
                  Batal
                </button>
              </div>
            )}

            {/* BUTTON EXPORT BACKUP */}
            <button
              onClick={() => {
                try {
                  const dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(state, null, 2));
                  const downloadAnchor = document.createElement('a');
                  downloadAnchor.setAttribute("href", dataStr);
                  downloadAnchor.setAttribute("download", `cadangan_depo_kontainer_${new Date().toISOString().split('T')[0]}.json`);
                  document.body.appendChild(downloadAnchor);
                  downloadAnchor.click();
                  downloadAnchor.remove();
                  setNoticeMsg('Sukses mengunduh file cadangan database (.json)! Simpan file ini di tempat aman sehingga Anda tidak perlu mengetik ulang jika browser ter-reset.');
                  setTimeout(() => setNoticeMsg(null), 10000);
                } catch (e: any) {
                  alert('Gagal mendownload data cadangan: ' + e.message);
                }
              }}
              className="inline-flex items-center gap-1.5 bg-emerald-55 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 hover:text-emerald-800 border border-emerald-100 text-[11px] font-bold py-1.5 px-3 rounded-lg transition-colors cursor-pointer"
            >
              <CheckCircle className="w-3.5 h-3.5 text-emerald-600" />
              Ekspor Cadangan Semua Data (.json)
            </button>

            {/* BUTTON RESTORE BACKUP */}
            <label
              className="inline-flex items-center gap-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 hover:text-indigo-800 border border-indigo-100 text-[11px] font-bold py-1.5 px-3 rounded-lg transition-colors cursor-pointer"
            >
              <Upload className="w-3.5 h-3.5 text-indigo-600" />
              Unggah &amp; Pulihkan Cadangan (.json)
              <input
                type="file"
                accept=".json"
                onChange={(event) => {
                  const fileReader = new FileReader();
                  const file = event.target.files?.[0];
                  if (!file) return;

                  fileReader.onload = (e) => {
                    try {
                      const parsed = JSON.parse(e.target?.result as string);
                      if (parsed && typeof parsed === 'object' && Array.isArray(parsed.customers) && Array.isArray(parsed.kontainers)) {
                        onStateChange(parsed);
                        setNoticeMsg('Sukses besar! File cadangan Anda berhasil diunggah dan dipulihkan sepenuhnya ke browser Anda.');
                        setTimeout(() => setNoticeMsg(null), 10000);
                      } else {
                        throw new Error('Format file JSON cadangan tidak sesuai skema database Rental Depo Kontainer.');
                      }
                    } catch (err: any) {
                      alert('Gagal memulihkan file cadangan: ' + err.message);
                    }
                  };
                  fileReader.readAsText(file);
                  event.target.value = '';
                }}
                className="hidden"
              />
            </label>

            {/* BUTTON RESTORE DEMO */}
            {!confirmResetDemo ? (
              <button
                id="btn-trigger-reset-demo"
                onClick={() => {
                  setConfirmResetDemo(true);
                  setConfirmResetEmpty(false);
                }}
                className="inline-flex items-center gap-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 hover:text-slate-800 border border-slate-200 text-[11px] font-bold py-1.5 px-3 rounded-lg transition-colors cursor-pointer"
              >
                <RotateCcw className="w-3.5 h-3.5" />
                Muat Ulang / Pulihkan Data Contoh Demo
              </button>
            ) : (
              <div className="flex items-center gap-2 bg-slate-100 border border-slate-200 p-2 rounded-xl text-[10px]">
                <span className="text-slate-700 font-bold">Muat ulang data simulasi (akan menimpa data yang ada)?</span>
                <button
                  id="btn-confirm-reset-demo"
                  onClick={() => {
                    onStateChange(getDemoAppState());
                    setConfirmResetDemo(false);
                    setNoticeMsg('Sukses memulihkan database contoh simulasi! (CV. Samudera Raya, PT. Lintas Cargo, dll.).');
                    setTimeout(() => setNoticeMsg(null), 8000);
                  }}
                  className="bg-slate-700 text-white font-bold px-2 py-1 rounded-md text-[9px] hover:bg-slate-800 cursor-pointer"
                >
                  Ya, Muat Contoh
                </button>
                <button
                  id="btn-cancel-reset-demo"
                  onClick={() => setConfirmResetDemo(false)}
                  className="bg-slate-200 text-slate-700 font-bold px-2 py-1 rounded-md text-[9px] hover:bg-slate-300 cursor-pointer"
                >
                  Batal
                </button>
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
