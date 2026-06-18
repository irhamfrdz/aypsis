export interface Customer {
  id_customer: string; // UUID/Random string
  nama_customer: string;
}

export interface TipeKontainer {
  id_tipe: string;
  nama_tipe: string; // e.g. "Dry", "Reefer", "Flat Rack"
}

export interface UkuranKontainer {
  id_ukuran: string;
  deskripsi_ukuran: string; // e.g. "20'", "40'"
}

export interface Kontainer {
  no_kontainer: string; // Primary Key (PK), 100% Unique
  id_customer: string; // Terkait ke Master customer
  id_tipe: string; // Terkait ke Master Tipe
  id_ukuran: string; // Terkait ke Master Ukuran
  status_aktif: boolean;
}

export interface TarifSewa {
  id_tarif: string;
  id_customer: string;
  id_tipe: string;
  id_ukuran: string;
  tarif_bulanan: number; // Bisa diisi sekaligus atau salah satu
  tarif_harian: number;
  tanggal_mulai_berlaku: string; // YYYY-MM-DD
  tanggal_akhir_berlaku: string | null; // YYYY-MM-DD atau null jika masih berlaku
}

export interface Sewa {
  id_sewa: string;
  no_kontainer: string;
  id_customer: string;
  tanggal_sewa: string; // YYYY-MM-DD
  tanggal_kembali: string | null; // YYYY-MM-DD atau null jika belum kembali
  tarif_bulanan: number;
  tarif_harian: number;
  jenis_tarif: 'Bulanan' | 'Harian';
  status_sewa: 'Aktif' | 'Selesai';
  catatan?: string;
}

export interface TagihanBulan {
  id_tagihan: string; // format: [NO_KONTAINER][SERIAL_TGL_SEWA][BULAN_KE]
  id_sewa: string;
  bulan_ke: number;
  tanggal_awal: string; // YYYY-MM-DD
  tanggal_akhir: string; // YYYY-MM-DD
  jumlah_hari: number;
  tipe_tarif: 'BULANAN' | 'PRORATE' | 'HARIAN';
  jumlah_tagihan: number; // Ini adalah ESTIMASI (System-generated)
  status_bayar: 'Belum Ditagih' | 'Pranota' | 'Belum Bayar' | 'Lunas';
  tanggal_tagihan: string | null;
  tanggal_bayar: string | null;
  nomor_invoice_grup: string | null; // Nomor Tagihan / Invoice No
  jumlah_tagihan_override?: number | null; // Nominal tagihan asli/aktual yang diimpor/diedit
  jumlah_bayar?: number | null; // Nominal yang dibayar
  selisih_pembayaran?: number | null; // Selisih TAGIHAN - ESTIMASI
  keterangan_selisih?: string | null;
  ppn?: number | null; // Pajak Masukan/Keluaran default 11% tapi bisa diedit
  pph?: number | null; // Pajak PPh 23 default 2% tapi bisa diedit
  nomor_bayar?: string | null; // EBK... No Bukti Bayar
}

export interface InvoiceGrup {
  nomor_invoice: string; // e.g., INV/2026/0612/001
  id_customer: string;
  tanggal_invoice: string; // YYYY-MM-DD
  status_pembayaran: 'Belum Bayar' | 'Lunas';
  deskripsi: string;
  list_id_tagihan: string[]; // Id tagihan yang tergabung
  adjustment_biaya?: number;
  adjustment_keterangan?: string;
}
