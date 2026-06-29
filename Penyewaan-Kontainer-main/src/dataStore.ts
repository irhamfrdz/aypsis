import { Customer, TipeKontainer, UkuranKontainer, Kontainer, TarifSewa, Sewa, TagihanBulan, InvoiceGrup } from './types';
import { generateBillingPeriodsForSewa } from './utils';

const STORE_KEYS = {
  CUSTOMERS: 'sewa_kontainer_customers',
  TIPES: 'sewa_kontainer_tipes',
  UKURANS: 'sewa_kontainer_ukurans',
  KONTAINERS: 'sewa_kontainer_kontainers',
  TARIFS: 'sewa_kontainer_tarifs',
  SEWAS: 'sewa_kontainer_sewas',
  TAGIHANS_PAYMENT_STATE: 'sewa_kontainer_tagihans_payment_state', // stores overrides for status_bayar, tanggal_bayar, tanggal_tagihan, nomor_invoice_grup
  INVOICES: 'sewa_kontainer_invoices',
};

// Initial Mock Data
const INITIAL_CUSTOMERS: Customer[] = [
  { id_customer: 'cust_1', nama_customer: 'CV. Samudera Raya' },
  { id_customer: 'cust_2', nama_customer: 'PT. Lintas Cargo Jaya' },
  { id_customer: 'cust_3', nama_customer: 'Meratus Line TBK' },
];

const INITIAL_TIPES: TipeKontainer[] = [
  { id_tipe: 'tipe_1', nama_tipe: 'Dry' },
  { id_tipe: 'tipe_2', nama_tipe: 'Reefer' },
  { id_tipe: 'tipe_3', nama_tipe: 'Flat Rack' },
];

const INITIAL_UKURANS: UkuranKontainer[] = [
  { id_ukuran: 'sz_1', deskripsi_ukuran: "20'" },
  { id_ukuran: 'sz_2', deskripsi_ukuran: "40'" },
];

const INITIAL_KONTAINERS: Kontainer[] = [
  { no_kontainer: 'AMFU3153692', id_customer: 'cust_1', id_tipe: 'tipe_1', id_ukuran: 'sz_1', status_aktif: true },
  { no_kontainer: 'GLDU7252828', id_customer: 'cust_2', id_tipe: 'tipe_2', id_ukuran: 'sz_2', status_aktif: true },
  { no_kontainer: 'TEXU9483751', id_customer: 'cust_3', id_tipe: 'tipe_3', id_ukuran: 'sz_2', status_aktif: true },
];

const INITIAL_TARIFS: TarifSewa[] = [
  {
    id_tarif: 'trf_1',
    id_customer: 'cust_1',
    id_tipe: 'tipe_1',
    id_ukuran: 'sz_1',
    tarif_bulanan: 3000000,
    tarif_harian: 150000,
    tanggal_mulai_berlaku: '2022-01-01',
    tanggal_akhir_berlaku: null
  },
  {
    id_tarif: 'trf_2',
    id_customer: 'cust_2',
    id_tipe: 'tipe_2',
    id_ukuran: 'sz_2',
    tarif_bulanan: 6000000,
    tarif_harian: 300000,
    tanggal_mulai_berlaku: '2023-01-01',
    tanggal_akhir_berlaku: null
  }
];

const INITIAL_SEWAS: Sewa[] = [
  {
    id_sewa: 'sewa_1',
    no_kontainer: 'AMFU3153692',
    id_customer: 'cust_1',
    tanggal_sewa: '2022-09-30',
    tanggal_kembali: '2023-05-10', // Matched with the user's image exactly!
    tarif_bulanan: 3000000,
    tarif_harian: 150000,
    jenis_tarif: 'Bulanan',
    status_sewa: 'Selesai',
    catatan: 'Rentang waktu sesuai screenshot gambar user'
  },
  {
    id_sewa: 'sewa_2',
    no_kontainer: 'GLDU7252828',
    id_customer: 'cust_2',
    tanggal_sewa: '2024-04-22', // From the user's description example
    tanggal_kembali: null, // Active
    tarif_bulanan: 6000000,
    tarif_harian: 300000,
    jenis_tarif: 'Bulanan',
    status_sewa: 'Aktif',
    catatan: 'Rental aktif jangka panjang'
  }
];

// Initial Payment States for the mock billing periods of 'sewa_1' to simulate some paid/unpaid jumping states
// We can store customized status_bayar for periods!
// "masalah nya mereka bayar nya tidak berurutan bisa loncat loncat bulan 1 belum bayar bisa bayar bulan 2 atau bahkan bulan 4"
const INITIAL_PAYMENT_OVERRIDES: Record<string, { status_bayar: 'Belum Ditagih' | 'Pranota' | 'Belum Bayar' | 'Lunas', tanggal_bayar: string | null, tanggal_tagihan: string | null, nomor_invoice_grup: string | null }> = {
  // AMFU3153692 starting 2022-09-30 (Serial: 44834)
  // Month 1: 30 Sep 22 - 29 Okt 22
  'AMFU31536924483401': { status_bayar: 'Belum Bayar', tanggal_bayar: null, tanggal_tagihan: '2022-10-31', nomor_invoice_grup: 'INV-202210-01' },
  // Month 2: Payed!
  'AMFU31536924483402': { status_bayar: 'Lunas', tanggal_bayar: '2022-11-20', tanggal_tagihan: '2022-11-20', nomor_invoice_grup: 'INV-202211-04' },
  // Month 3: Unpaid but Billed
  'AMFU31536924483403': { status_bayar: 'Belum Bayar', tanggal_bayar: null, tanggal_tagihan: '2022-12-30', nomor_invoice_grup: 'INV-202212-10' },
  // Month 4: Payed! (Unordered jumpy payment!)
  'AMFU31536924483404': { status_bayar: 'Lunas', tanggal_bayar: '2023-01-15', tanggal_tagihan: '2023-01-15', nomor_invoice_grup: 'INV-202301-12' },
};

const INITIAL_INVOICES: InvoiceGrup[] = [
  {
    nomor_invoice: 'INV-202210-01',
    id_customer: 'cust_1',
    tanggal_invoice: '2022-10-31',
    status_pembayaran: 'Belum Bayar',
    deskripsi: 'Tagihan Kontainer AMFU3153692 Bulan 1',
    list_id_tagihan: ['AMFU31536924483401']
  },
  {
    nomor_invoice: 'INV-202211-04',
    id_customer: 'cust_1',
    tanggal_invoice: '2022-11-20',
    status_pembayaran: 'Lunas',
    deskripsi: 'Tagihan Kontainer AMFU3153692 Bulan 2',
    list_id_tagihan: ['AMFU31536924483402']
  },
  {
    nomor_invoice: 'INV-202212-10',
    id_customer: 'cust_1',
    tanggal_invoice: '2022-12-30',
    status_pembayaran: 'Belum Bayar',
    deskripsi: 'Tagihan Kontainer AMFU3153692 Bulan 3',
    list_id_tagihan: ['AMFU31536924483403']
  },
  {
    nomor_invoice: 'INV-202301-12',
    id_customer: 'cust_1',
    tanggal_invoice: '2023-01-15',
    status_pembayaran: 'Lunas',
    deskripsi: 'Tagihan Kontainer AMFU3153692 Bulan 4',
    list_id_tagihan: ['AMFU31536924483404']
  }
];

export function loadData<T>(key: string, initial: T): T {
  const data = localStorage.getItem(key);
  if (!data) {
    localStorage.setItem(key, JSON.stringify(initial));
    return initial;
  }
  try {
    return JSON.parse(data) as T;
  } catch (e) {
    return initial;
  }
}

export function saveData<T>(key: string, data: T): void {
  localStorage.setItem(key, JSON.stringify(data));
}

export interface OverrideItem {
  status_bayar: 'Belum Ditagih' | 'Pranota' | 'Belum Bayar' | 'Lunas';
  tanggal_bayar: string | null;
  tanggal_tagihan: string | null;
  nomor_invoice_grup: string | null; // NoTagihan / Invoice No
  nomor_pranota?: string | null; // Nomor Pranota / Proforma
  tanggal_pranota?: string | null; // Tanggal Pranota / Proforma
  jumlah_tagihan_override?: number | null; // actual billed
  jumlah_bayar?: number | null;
  selisih_pembayaran?: number | null;
  keterangan_selisih?: string | null;
  ppn?: number | null;
  pph?: number | null;
  nomor_bayar?: string | null;
}

export interface AppState {
  customers: Customer[];
  tipes: TipeKontainer[];
  ukurans: UkuranKontainer[];
  kontainers: Kontainer[];
  tarifs: TarifSewa[];
  sewas: Sewa[];
  invoices: InvoiceGrup[];
  paymentOverrides: Record<string, OverrideItem>;
  manualTagihans?: TagihanBulan[];
}

export function loadAppState(): AppState {
  const customers = loadData<Customer[]>(STORE_KEYS.CUSTOMERS, INITIAL_CUSTOMERS);
  const tipes = loadData<TipeKontainer[]>(STORE_KEYS.TIPES, INITIAL_TIPES);
  const ukurans = loadData<UkuranKontainer[]>(STORE_KEYS.UKURANS, INITIAL_UKURANS);
  const kontainers = loadData<Kontainer[]>(STORE_KEYS.KONTAINERS, INITIAL_KONTAINERS);
  const tarifs = loadData<TarifSewa[]>(STORE_KEYS.TARIFS, INITIAL_TARIFS);
  const sewas = loadData<Sewa[]>(STORE_KEYS.SEWAS, INITIAL_SEWAS);
  const invoices = loadData<InvoiceGrup[]>(STORE_KEYS.INVOICES, INITIAL_INVOICES);
  const paymentOverrides = loadData<Record<string, OverrideItem>>(
    STORE_KEYS.TAGIHANS_PAYMENT_STATE,
    INITIAL_PAYMENT_OVERRIDES
  );
  const manualTagihans = loadData<TagihanBulan[]>('sewa_kontainer_manual_tagihans', []);

  return { customers, tipes, ukurans, kontainers, tarifs, sewas, invoices, paymentOverrides, manualTagihans };
}

export function saveAppState(state: AppState): void {
  saveData(STORE_KEYS.CUSTOMERS, state.customers);
  saveData(STORE_KEYS.TIPES, state.tipes);
  saveData(STORE_KEYS.UKURANS, state.ukurans);
  saveData(STORE_KEYS.KONTAINERS, state.kontainers);
  saveData(STORE_KEYS.TARIFS, state.tarifs);
  saveData(STORE_KEYS.SEWAS, state.sewas);
  saveData(STORE_KEYS.INVOICES, state.invoices);
  saveData(STORE_KEYS.TAGIHANS_PAYMENT_STATE, state.paymentOverrides);
  saveData('sewa_kontainer_manual_tagihans', state.manualTagihans || []);
}

export function getEmptyAppState(): AppState {
  return {
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
}

export function getDemoAppState(): AppState {
  return {
    customers: [...INITIAL_CUSTOMERS],
    tipes: [...INITIAL_TIPES],
    ukurans: [...INITIAL_UKURANS],
    kontainers: [...INITIAL_KONTAINERS],
    tarifs: [...INITIAL_TARIFS],
    sewas: [...INITIAL_SEWAS],
    invoices: [...INITIAL_INVOICES],
    paymentOverrides: { ...INITIAL_PAYMENT_OVERRIDES },
    manualTagihans: []
  };
}

// Cache variables for performance optimization (stops redundant calculations on tab changes/renders)
let lastStateRef: any = null;
let lastCurrentUtcStr: string = "";
let cachedPeriods: TagihanBulan[] = [];

// Dynamically compile all active billing periods across all Sewa contracts, combining local persistence overrides
export function compileAllPeriods(state: AppState, currentUtcStr: string): TagihanBulan[] {
  // If the data state hasn't changed and the time/limit reference is the same, return the cached calculation
  if (state === lastStateRef && currentUtcStr === lastCurrentUtcStr && cachedPeriods.length > 0) {
    return cachedPeriods;
  }

  let allPeriods: TagihanBulan[] = [];
  
  for (const sewa of state.sewas) {
    const rawPeriods = generateBillingPeriodsForSewa(sewa, currentUtcStr);
    
    // Enrich periods with their override states
    const enriched = rawPeriods.map(p => {
      const override = state.paymentOverrides[p.id_tagihan];
      if (override) {
        const hasOverrideAmt = override.jumlah_tagihan_override !== undefined && override.jumlah_tagihan_override !== null;
        const tagihanAmt = hasOverrideAmt ? override.jumlah_tagihan_override! : p.jumlah_tagihan;
        return {
          ...p,
          status_bayar: override.status_bayar,
          tanggal_bayar: override.tanggal_bayar,
          tanggal_tagihan: override.tanggal_tagihan,
          nomor_invoice_grup: override.nomor_invoice_grup,
          nomor_pranota: override.nomor_pranota !== undefined ? override.nomor_pranota : null,
          tanggal_pranota: override.tanggal_pranota !== undefined ? override.tanggal_pranota : null,
          jumlah_tagihan_override: hasOverrideAmt ? override.jumlah_tagihan_override : null,
          jumlah_bayar: override.jumlah_bayar !== undefined ? override.jumlah_bayar : null,
          selisih_pembayaran: tagihanAmt - p.jumlah_tagihan,
          keterangan_selisih: override.keterangan_selisih !== undefined ? override.keterangan_selisih : null,
          ppn: override.ppn !== undefined ? override.ppn : p.ppn,
          pph: override.pph !== undefined ? override.pph : p.pph,
          nomor_bayar: override.nomor_bayar !== undefined ? override.nomor_bayar : null,
        };
      }
      return p;
    });

    allPeriods = allPeriods.concat(enriched);
  }

  // Enrich manual tagihans as well
  const manualEnriched = (state.manualTagihans || []).map(p => {
    const override = state.paymentOverrides[p.id_tagihan];
    if (override) {
      const hasOverrideAmt = override.jumlah_tagihan_override !== undefined && override.jumlah_tagihan_override !== null;
      const tagihanAmt = hasOverrideAmt ? override.jumlah_tagihan_override! : p.jumlah_tagihan;
      return {
        ...p,
        status_bayar: override.status_bayar,
        tanggal_bayar: override.tanggal_bayar,
        tanggal_tagihan: override.tanggal_tagihan,
        nomor_invoice_grup: override.nomor_invoice_grup,
        nomor_pranota: override.nomor_pranota !== undefined ? override.nomor_pranota : null,
        tanggal_pranota: override.tanggal_pranota !== undefined ? override.tanggal_pranota : null,
        jumlah_tagihan_override: hasOverrideAmt ? override.jumlah_tagihan_override : null,
        jumlah_bayar: override.jumlah_bayar !== undefined ? override.jumlah_bayar : null,
        selisih_pembayaran: tagihanAmt - p.jumlah_tagihan,
        keterangan_selisih: override.keterangan_selisih !== undefined ? override.keterangan_selisih : null,
        ppn: override.ppn !== undefined ? override.ppn : null,
        pph: override.pph !== undefined ? override.pph : null,
        nomor_bayar: override.nomor_bayar !== undefined ? override.nomor_bayar : null,
      };
    }
    return p;
  });

  allPeriods = allPeriods.concat(manualEnriched);

  // Update memory cache
  lastStateRef = state;
  lastCurrentUtcStr = currentUtcStr;
  cachedPeriods = allPeriods;

  return allPeriods;
}
