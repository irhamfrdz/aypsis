import { Sewa, TagihanBulan } from './types';

// Helper to check for Leap Year
export function isLeapYear(year: number): boolean {
  return (year % 4 === 0 && year % 100 !== 0) || (year % 400 === 0);
}

// Helper to convert date string "YYYY-MM-DD" safely to JS Date
export function parseLocalDate(dateStr: string): Date {
  const [y, m, d] = dateStr.split('-').map(num => parseInt(num, 10));
  return new Date(y, m - 1, d);
}

// Convert ISO date string into Date string "YYYY-MM-DD"
export function dateToIsoStr(date: Date): string {
  const y = date.getFullYear();
  const m = String(date.getMonth() + 1).padStart(2, '0');
  const d = String(date.getDate()).padStart(2, '0');
  return `${y}-${m}-${d}`;
}

// Converts Date to Excel Serial number (e.g. 22/04/2024 -> 45373)
export function dateToExcelSerial(dateString: string): number {
  try {
    const d = parseLocalDate(dateString);
    if (isNaN(d.getTime())) return 0;
    // Difference based on Excel's base date 30 Dec 1899 (taking leap year bug into account)
    const baseDate = Date.UTC(1899, 11, 30);
    const utcDate = Date.UTC(d.getFullYear(), d.getMonth(), d.getDate());
    return Math.floor((utcDate - baseDate) / (24 * 60 * 60 * 1000));
  } catch (e) {
    return 0;
  }
}

// Format date to "dd/mm/yyyy" for input/export
export function formatEntryDate(dateStr: string): string {
  if (!dateStr) return '';
  const parts = dateStr.split('-');
  if (parts.length === 3) {
    return `${parts[2]}/${parts[1]}/${parts[0]}`;
  }
  return dateStr;
}

// Parse "dd/mm/yyyy", Excel Serial Numbers (e.g. 45768), or textual dates (e.g., "21 Mei 25", "21/05/2025") into "yyyy-mm-dd" for state/database
export function parseInputDate(inputStr: string): string | null {
  const trim = inputStr.trim();
  if (!trim) return null;

  // Check if it is a pure numeric input (Excel Serial Number, e.g. 45768)
  if (/^\d+(\.\d+)?$/.test(trim)) {
    const serial = parseFloat(trim);
    if (serial > 0) {
      // Excel base date: 30 Dec 1899 because of leap year offset
      const baseEpoch = Date.UTC(1899, 11, 30);
      const ms = baseEpoch + serial * 86400000;
      const d = new Date(ms);
      if (!isNaN(d.getTime())) {
        const y = d.getUTCFullYear();
        const mMonth = String(d.getUTCMonth() + 1).padStart(2, '0');
        const day = String(d.getUTCDate()).padStart(2, '0');
        return `${y}-${mMonth}-${day}`;
      }
    }
  }

  // Support Indonesian and English textual dates e.g. "21 Mei 25", "21 Mei 2025", "21 April 2025"
  const wordMatch = trim.match(/^(\d{1,2})\s+([a-zA-Z]+)\s+(\d{2,4})$/);
  if (wordMatch) {
    const d = parseInt(wordMatch[1], 10);
    const monthWord = wordMatch[2].toLowerCase();
    let y = parseInt(wordMatch[3], 10);
    if (y < 100) {
      y += 2000; // assume 2000s
    }

    const indEnglishMonths: Record<string, number> = {
      jan: 1, januari: 1, january: 1,
      feb: 2, februari: 2, february: 2,
      mar: 3, maret: 3, march: 3,
      apr: 4, april: 4,
      mei: 5, may: 5,
      jun: 6, juni: 6, june: 6,
      jul: 7, juli: 7, july: 7,
      agt: 8, agustus: 8, aug: 8, august: 8,
      sep: 9, september: 9,
      okt: 10, oktober: 10, oct: 10, october: 10,
      nov: 11, november: 11,
      des: 12, desember: 12, dec: 12, december: 12
    };

    const prefix = monthWord.length >= 3 ? monthWord.slice(0, 3) : monthWord;
    const mMonth = indEnglishMonths[prefix] || indEnglishMonths[monthWord];
    if (mMonth && d > 0 && d <= 31) {
      return `${y}-${String(mMonth).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
    }
  }

  const m = trim.match(/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/);
  if (m) {
    const d = parseInt(m[1], 10);
    const mMonth = parseInt(m[2], 10);
    const y = parseInt(m[3], 10);
    if (d > 0 && d <= 31 && mMonth > 0 && mMonth <= 12) {
      return `${y}-${String(mMonth).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
    }
  }
  // Fallback to ISO-ish matching if yyyy-mm-dd is entered
  const isISO = trim.match(/^(\d{4})[\/\-](\d{1,2})[\/\-](\d{1,2})$/);
  if (isISO) {
    const y = parseInt(isISO[1], 10);
    const mMonth = parseInt(isISO[2], 10);
    const d = parseInt(isISO[3], 10);
    return `${y}-${String(mMonth).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
  }
  return null;
}

// Format date to Indonesian screen layout "dd mmm yy"
export function formatIndoDate(dateStr: string): string {
  if (!dateStr) return '-';
  try {
    const parts = dateStr.split('-');
    if (parts.length !== 3) return dateStr;
    const day = parts[2];
    const monthIdx = parseInt(parts[1], 10) - 1;
    const yearAbbr = parts[0].slice(-2);
    
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
    const monthStr = months[monthIdx] || parts[1];
    return `${day} ${monthStr} ${yearAbbr}`;
  } catch (e) {
    return dateStr;
  }
}

// Calculate the next cycle start date according to calendar month shift logic
export function getNextCycleStart(curr: Date): Date {
  const year = curr.getFullYear();
  const month = curr.getMonth();
  const date = curr.getDate();
  
  // Try setting next month with exact same day
  const nextMonthSameDay = new Date(year, month + 1, date);
  // If the day changed (e.g., 30 Jan + 1 month -> 2 Mar), it means it overflowed Feb
  if (nextMonthSameDay.getDate() !== date) {
    // So next cycle should start on 1st of the next-next month (e.g. 1st March)
    return new Date(year, month + 2, 1);
  }
  return nextMonthSameDay;
}

// Format currency in Rupiah (IDR)
export function formatRupiah(num: number): string {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(num);
}

// Generate monthly periods dynamically for a rental (Sewa)
export function generateBillingPeriodsForSewa(
  sewa: Sewa,
  currentUtcTimeStr: string // ISO string timestamp or YYYY-MM-DD
): TagihanBulan[] {
  const periods: TagihanBulan[] = [];
  const startLocal = parseLocalDate(sewa.tanggal_sewa);
  
  // End of generation limit is either when the container is returned (Selesai) OR today's date
  const todayStr = currentUtcTimeStr.split('T')[0];
  const limitStr = sewa.tanggal_kembali ? sewa.tanggal_kembali : todayStr;
  const limitLocal = parseLocalDate(limitStr);

  const containerPart = sewa.no_kontainer.trim().replace(/\s+/g, '');
  const serialPart = dateToExcelSerial(sewa.tanggal_sewa).toString();

  let currStart = new Date(startLocal);
  let index = 1;

  // Ensure we always generate at least the first period, even if today is prior to start
  while (currStart <= limitLocal || index === 1) {
    const nextStart = getNextCycleStart(currStart);
    const normalEndDate = new Date(nextStart);
    normalEndDate.setDate(normalEndDate.getDate() - 1);

    // Format billing period serial ID: [NO_KONTAINER][SERIAL_TGL_SEWA][2-digit-month-index]
    const monthSuffix = String(index).padStart(2, '0');
    const id_tagihan = `${containerPart}${serialPart}${monthSuffix}`;

    // Compare normalEndDate against actual rental limit date
    if (normalEndDate <= limitLocal) {
      // Completed full billing cycle
      const days = Math.round((normalEndDate.getTime() - currStart.getTime()) / (24 * 60 * 60 * 1000)) + 1;
      const amount = sewa.jenis_tarif === 'Bulanan' ? sewa.tarif_bulanan : days * sewa.tarif_harian;
      
      periods.push({
        id_tagihan,
        id_sewa: sewa.id_sewa,
        bulan_ke: index,
        tanggal_awal: dateToIsoStr(currStart),
        tanggal_akhir: dateToIsoStr(normalEndDate),
        jumlah_hari: days,
        tipe_tarif: sewa.jenis_tarif === 'Bulanan' ? 'BULANAN' : 'HARIAN',
        jumlah_tagihan: amount,
        status_bayar: 'Belum Ditagih',
        tanggal_tagihan: null,
        tanggal_bayar: null,
        nomor_invoice_grup: null,
      });

      // Prepare for next loop
      currStart = new Date(nextStart);
      index++;
    } else {
      // Cut short (Prorate or Harian ongoing/last period)
      // Limit to actual end date
      const endLocal = limitLocal < currStart ? currStart : limitLocal;
      const days = Math.round((endLocal.getTime() - currStart.getTime()) / (24 * 60 * 60 * 1000)) + 1;
      
      let amount = 0;
      let tipe_tarif: 'BULANAN' | 'PRORATE' | 'HARIAN' = 'PRORATE';

      if (sewa.jenis_tarif === 'Harian') {
        amount = days * sewa.tarif_harian;
        tipe_tarif = 'HARIAN';
      } else {
        // Prorate monthly:
        // Jan -> Mar: 30 days base size
        // Feb: leap year 29, standard 28
        const sMonth = currStart.getMonth(); // 0-indexed (0=Jan, 1=Feb, 2=Mar...)
        let baseDays = 30;
        if (sMonth === 1) { // February
          baseDays = isLeapYear(currStart.getFullYear()) ? 29 : 28;
        }
        
        // If it turned out to have somehow matching days anyway (e.g. 30 days for March to Jan), standardise lumpsum, otherwise prorated
        if (days === baseDays) {
          amount = sewa.tarif_bulanan;
          tipe_tarif = 'BULANAN';
        } else {
          const dailyRate = sewa.tarif_bulanan / baseDays;
          amount = Math.round(days * dailyRate);
          tipe_tarif = 'PRORATE';
        }
      }

      periods.push({
        id_tagihan,
        id_sewa: sewa.id_sewa,
        bulan_ke: index,
        tanggal_awal: dateToIsoStr(currStart),
        tanggal_akhir: dateToIsoStr(endLocal),
        jumlah_hari: days,
        tipe_tarif,
        jumlah_tagihan: amount,
        status_bayar: 'Belum Ditagih',
        tanggal_tagihan: null,
        tanggal_bayar: null,
        nomor_invoice_grup: null,
      });

      break; // End generation of active cycles
    }
  }

  return periods;
}

export function formatToWIB(isoStr: string): string {
  try {
    const d = new Date(isoStr);
    // WIB is UTC+7. Correct timezone is Asia/Jakarta
    const wibStr = d.toLocaleString('id-ID', {
      timeZone: 'Asia/Jakarta',
      day: 'numeric',
      month: 'short',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit',
      hour12: false
    });
    return wibStr.replace(/\./g, ':') + ' WIB';
  } catch (e) {
    return '12 Jun 2026 21:06:12 WIB';
  }
}

