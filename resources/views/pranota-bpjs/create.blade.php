@extends('layouts.app')

@section('title', 'Buat Pranota BPJS')
@section('page_title', 'Buat Pranota BPJS')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-6 w-full max-w-[96rem] mx-auto space-y-6">

    {{-- ── Page Header ──────────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-gray-200">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-teal-600 text-white flex items-center justify-center shadow-sm">
                <i class="fas fa-file-invoice-dollar text-lg"></i>
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Buat Pranota BPJS</h1>
                <p class="text-xs sm:text-sm text-gray-500">Form pembuatan rincian tagihan & iuran BPJS karyawan</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('pranota-bpjs.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs font-semibold rounded-lg shadow-2xs transition-all">
                <i class="fas fa-arrow-left text-xs text-gray-500"></i>
                Kembali ke Daftar
            </a>
        </div>
    </div>

    {{-- ── Validation / Session Alerts ─────────────────────────────────────── --}}
    @if ($errors->any())
        <div class="bg-rose-50 border border-rose-200 rounded-xl p-4 shadow-xs">
            <div class="flex items-start gap-3">
                <div class="w-7 h-7 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <i class="fas fa-exclamation-circle text-sm"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-rose-900">Terdapat kesalahan input:</h3>
                    <ul class="list-disc list-inside text-xs text-rose-700 mt-1 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-rose-50 border border-rose-200 rounded-xl p-4 shadow-xs">
            <div class="flex items-center gap-3">
                <div class="w-7 h-7 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-sm"></i>
                </div>
                <p class="text-sm font-semibold text-rose-900">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <form id="pranota-form" action="{{ route('pranota-bpjs.store') }}" method="POST">
        @csrf

        {{-- ── Section 1: Informasi Periode Pranota ────────────────────────── --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-xs overflow-hidden">
            <div class="flex items-center justify-between px-6 py-3.5 border-b border-gray-100 bg-gray-50/80">
                <div class="flex items-center gap-2.5">
                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-teal-100 text-teal-800">
                        <i class="fas fa-calendar-check text-xs"></i>
                    </span>
                    <h2 class="text-sm font-bold text-gray-800">1. Informasi Pranota</h2>
                </div>
                <span class="text-[11px] text-gray-400 font-medium">* Wajib diisi</span>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5" for="tanggal_pranota">
                            Tanggal Pranota <span class="text-rose-500">*</span>
                        </label>
                        <input id="tanggal_pranota" name="tanggal_pranota" type="date"
                            class="w-full text-xs sm:text-sm rounded-lg border-gray-300 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 shadow-2xs transition"
                            value="{{ date('Y-m-d') }}" required />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5" for="periode_bulan">
                            Bulan Periode <span class="text-rose-500">*</span>
                        </label>
                        <select id="periode_bulan" name="periode_bulan"
                            class="w-full text-xs sm:text-sm rounded-lg border-gray-300 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 shadow-2xs transition font-medium" required>
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ date('n') == $i ? 'selected' : '' }}>
                                    {{ str_pad($i, 2, '0', STR_PAD_LEFT) }} — {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5" for="periode_tahun">
                            Tahun Periode <span class="text-rose-500">*</span>
                        </label>
                        <input id="periode_tahun" name="periode_tahun" type="number"
                            class="w-full text-xs sm:text-sm rounded-lg border-gray-300 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 shadow-2xs transition font-medium"
                            value="{{ date('Y') }}" required />
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5" for="keterangan">
                        Catatan / Keterangan <span class="text-gray-400 font-normal">(Opsional)</span>
                    </label>
                    <textarea id="keterangan" name="keterangan" rows="2"
                        class="w-full text-xs sm:text-sm rounded-lg border-gray-300 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 shadow-2xs transition placeholder:text-gray-400"
                        placeholder="Tambahkan catatan khusus pranota ini jika diperlukan..."></textarea>
                </div>
            </div>
        </div>

        {{-- ── Summary Cards ────────────────────────────────────────────────── --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6">
            <div class="bg-white rounded-xl p-4 border border-gray-200/80 shadow-2xs flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-users text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Total Karyawan</p>
                    <p class="text-base sm:text-lg font-bold text-gray-900 font-mono mt-0.5" id="card_total_karyawan">0 Orang</p>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 border border-indigo-100 shadow-2xs flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-heart-pulse text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold text-indigo-700 uppercase tracking-wide">Total JKN</p>
                    <p class="text-base sm:text-lg font-bold text-indigo-700 font-mono mt-0.5" id="card_total_jkn">Rp 0</p>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 border border-emerald-100 shadow-2xs flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-shield-alt text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold text-emerald-700 uppercase tracking-wide">Total Jamsostek</p>
                    <p class="text-base sm:text-lg font-bold text-emerald-700 font-mono mt-0.5" id="card_total_jamsostek">Rp 0</p>
                </div>
            </div>
            <div class="bg-teal-50/70 rounded-xl p-4 border border-teal-200 shadow-2xs flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-teal-600 text-white flex items-center justify-center flex-shrink-0 shadow-2xs">
                    <i class="fas fa-money-bill-wave text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] font-bold text-teal-800 uppercase tracking-wide">Grand Total</p>
                    <p class="text-base sm:text-lg font-bold text-teal-900 font-mono mt-0.5" id="card_grand_total">Rp 0</p>
                </div>
            </div>
        </div>

        {{-- ── Section 2: Detail Karyawan & Tabel Kalkulasi ─────────────────── --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-xs overflow-hidden mt-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-6 py-4 border-b border-gray-100 bg-gray-50/80">
                <div class="flex items-center gap-2.5">
                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-indigo-100 text-indigo-800">
                        <i class="fas fa-table-list text-xs"></i>
                    </span>
                    <div>
                        <h2 class="text-sm font-bold text-gray-800">2. Rincian Iuran BPJS Karyawan</h2>
                        <p class="text-[11px] text-gray-500">Tabel perhitungan rinci iuran JKN, Jamsostek PPU & BPU</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" id="btn-generate-all"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-2xs hover:shadow-xs transition-all">
                        <i class="fas fa-wand-magic-sparkles text-xs"></i>
                        Hitung Semua Karyawan
                    </button>
                    <button type="button" id="btn-add-karyawan"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-2xs hover:shadow-xs transition-all">
                        <i class="fas fa-plus text-xs"></i>
                        Tambah Baris
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left border-collapse" id="tabel-detail">
                    <thead>
                        {{-- ── Group Header Row ── --}}
                        <tr class="text-[11px] uppercase tracking-wider font-bold border-b border-gray-200 text-gray-700">
                            <th colspan="5" class="px-3 py-2 text-center border-r border-gray-200 bg-gray-100">
                                <i class="fas fa-id-card-clip text-gray-500 mr-1"></i> Informasi Karyawan
                            </th>
                            <th colspan="2" class="px-3 py-2 text-center border-r border-indigo-200 bg-indigo-50 text-indigo-900">
                                <i class="fas fa-heart-pulse text-indigo-600 mr-1"></i> BPJS Kesehatan (JKN)
                            </th>
                            <th colspan="4" class="px-3 py-2 text-center border-r border-emerald-200 bg-emerald-50 text-emerald-900">
                                <i class="fas fa-building text-emerald-600 mr-1"></i> BP Jamsostek (PPU)
                            </th>
                            <th colspan="3" class="px-3 py-2 text-center border-r border-sky-200 bg-sky-50 text-sky-900">
                                <i class="fas fa-user-gear text-sky-600 mr-1"></i> BP Jamsostek (BPU)
                            </th>
                            <th colspan="2" class="px-3 py-2 text-center border-r border-amber-200 bg-amber-50 text-amber-900">
                                <i class="fas fa-piggy-bank text-amber-600 mr-1"></i> Jaminan Pensiun (JP)
                            </th>
                            <th rowspan="2" class="px-4 py-3 text-right bg-teal-50 text-teal-900 border-l border-teal-200 font-bold whitespace-nowrap" style="min-width:130px">
                                Subtotal (Rp)
                            </th>
                            <th rowspan="2" class="px-2 py-3 w-10 text-center bg-gray-100 text-gray-500 font-bold">
                                Aksi
                            </th>
                        </tr>
                        {{-- ── Sub Header Row ── --}}
                        <tr class="text-[10px] font-semibold uppercase tracking-wider text-gray-500 border-b-2 border-gray-200 bg-gray-50">
                            {{-- Info --}}
                            <th class="px-3 py-2.5 w-10 text-center font-bold text-gray-400">#</th>
                            <th class="px-3 py-2.5 text-left font-bold text-gray-700" style="min-width:200px">Nama Karyawan</th>
                            <th class="px-3 py-2.5 text-center font-bold text-gray-600" style="min-width:120px">Group BPJS</th>
                            <th class="px-3 py-2.5 text-center font-bold text-gray-600" style="min-width:130px">Tipe JKN</th>
                            <th class="px-3 py-2.5 text-center font-bold text-gray-600 border-r border-gray-200" style="min-width:140px">Tipe Jamsostek</th>
                            
                            {{-- JKN (2) --}}
                            <th class="px-3 py-2.5 text-right font-bold text-indigo-700 bg-indigo-50/30" style="min-width:105px" title="BPJS Kesehatan 4% Tunjangan">KIS 4% (Tunj)</th>
                            <th class="px-3 py-2.5 text-right font-bold text-indigo-700 bg-indigo-50/30 border-r border-indigo-100" style="min-width:105px" title="BPJS Kesehatan 1% Hutang">KIS 1% (Hutang)</th>
                            
                            {{-- PPU (4) --}}
                            <th class="px-3 py-2.5 text-right font-bold text-emerald-700 bg-emerald-50/30" style="min-width:100px" title="JHT Biaya Perusahaan 3.7%">PPU JHT 3.7%</th>
                            <th class="px-3 py-2.5 text-right font-bold text-emerald-700 bg-emerald-50/30" style="min-width:90px" title="JHT Hutang Karyawan 2%">PPU JHT 2%</th>
                            <th class="px-3 py-2.5 text-right font-bold text-emerald-700 bg-emerald-50/30" style="min-width:95px" title="JKK Tunjangan 0.24%">PPU JKK 0.24%</th>
                            <th class="px-3 py-2.5 text-right font-bold text-emerald-700 bg-emerald-50/30 border-r border-emerald-100" style="min-width:95px" title="JKM Tunjangan 0.3%">PPU JKM 0.3%</th>

                            {{-- BPU (3) --}}
                            <th class="px-3 py-2.5 text-right font-bold text-rose-600 bg-rose-50/30" style="min-width:115px" title="BPU JHT Karyawan (Hutang Potongan)">BPU JHT (Kary)</th>
                            <th class="px-3 py-2.5 text-right font-bold text-sky-700 bg-sky-50/30" style="min-width:110px" title="BPU JKK 1% (Tunjangan)">BPU JKK 1%</th>
                            <th class="px-3 py-2.5 text-right font-bold text-sky-700 bg-sky-50/30 border-r border-sky-100" style="min-width:100px" title="BPU JKM Biaya">BPU JKM</th>
                            
                            {{-- JP (2) --}}
                            <th class="px-3 py-2.5 text-right font-bold text-amber-700 bg-amber-50/30" style="min-width:90px" title="Jaminan Pensiun Biaya Perusahaan 2%">PPU JP 2%</th>
                            <th class="px-3 py-2.5 text-right font-bold text-amber-700 bg-amber-50/30 border-r border-gray-200" style="min-width:90px" title="Jaminan Pensiun Hutang Karyawan 1%">PPU JP 1%</th>
                        </tr>
                    </thead>
                    <tbody id="detail-container" class="divide-y divide-gray-100 bg-white font-medium">
                        <!-- Dynamic Rows Generated via JS -->
                    </tbody>
                    <tfoot class="bg-gray-50/95 border-t-2 border-gray-300 shadow-xs text-xs">
                        <tr class="divide-x divide-gray-200/60 font-semibold">
                            <td colspan="5" class="px-4 py-3.5 text-right font-bold text-gray-700 uppercase tracking-wide bg-gray-100/90">
                                Total Keseluruhan:
                            </td>
                            {{-- JKN --}}
                            <td class="px-3 py-3.5 text-right font-mono font-bold text-indigo-700 bg-indigo-50/50" id="total_kes">Rp 0</td>
                            <td class="px-3 py-3.5 text-right font-mono font-bold text-indigo-700 bg-indigo-50/50" id="total_ket">Rp 0</td>
                            
                            {{-- PPU --}}
                            <td class="px-3 py-3.5 text-right font-mono font-bold text-emerald-700 bg-emerald-50/50" id="total_jht_biaya">Rp 0</td>
                            <td class="px-3 py-3.5 text-right font-mono font-bold text-emerald-700 bg-emerald-50/50" id="total_jht_hutang">Rp 0</td>
                            <td class="px-3 py-3.5 text-right font-mono font-bold text-emerald-700 bg-emerald-50/50" id="total_jkk">Rp 0</td>
                            <td class="px-3 py-3.5 text-right font-mono font-bold text-emerald-700 bg-emerald-50/50" id="total_jkm">Rp 0</td>
                            
                            {{-- BPU --}}
                            <td class="px-3 py-3.5 text-right font-mono font-bold text-rose-600 bg-rose-50/50" id="total_jkk_hutang">Rp 0</td>
                            <td class="px-3 py-3.5 text-right font-mono font-bold text-sky-700 bg-sky-50/50" id="total_bpu_jkk">Rp 0</td>
                            <td class="px-3 py-3.5 text-right font-mono font-bold text-sky-700 bg-sky-50/50" id="total_bpu_jkm">Rp 0</td>
                            
                            {{-- JP --}}
                            <td class="px-3 py-3.5 text-right font-mono font-bold text-amber-700 bg-amber-50/50" id="total_jp_biaya">Rp 0</td>
                            <td class="px-3 py-3.5 text-right font-mono font-bold text-amber-700 bg-amber-50/50" id="total_jp_hutang">Rp 0</td>
                            
                            {{-- Grand Total --}}
                            <td class="px-4 py-3.5 text-right font-mono font-bold text-teal-800 text-sm bg-teal-100/70" id="grand_total">Rp 0</td>
                            <td class="bg-gray-100/90"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Empty State (Shown when 0 rows) --}}
            <div id="table-empty-state" class="py-12 px-4 text-center border-t border-gray-100">
                <div class="w-12 h-12 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-users-slash text-xl"></i>
                </div>
                <h3 class="text-sm font-semibold text-gray-700">Belum ada data karyawan</h3>
                <p class="text-xs text-gray-400 mt-1 max-w-sm mx-auto">Klik tombol <strong>Hitung Semua Karyawan</strong> untuk memuat data otomatis atau <strong>Tambah Baris</strong> untuk mengisi manual.</p>
            </div>
        </div>

        {{-- ── Bottom Actions & Confirmation ────────────────────────────────── --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-4 border-t border-gray-200">
            <div class="flex items-center gap-2 text-xs text-gray-500">
                <i class="fas fa-shield-halved text-teal-600 text-sm"></i>
                <span>Pastikan data rincian iuran sudah sesuai sebelum menyimpan pranota.</span>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('pranota-bpjs.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs font-semibold rounded-lg shadow-2xs transition-colors">
                    <i class="fas fa-times text-xs text-gray-400"></i>
                    Batal
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-lg shadow-sm hover:shadow transition-all">
                    <i class="fas fa-save text-xs"></i>
                    Simpan Pranota
                </button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('detail-container');
    const emptyState = document.getElementById('table-empty-state');
    const btnAdd = document.getElementById('btn-add-karyawan');
    
    // Convert karyawans to JSON for select options
    const karyawans = @json($karyawans);
    const rumusBpjs = @json($rumusBpjs);
    let rowCount = 0;

    function formatNumber(num) {
        return new Intl.NumberFormat('id-ID').format(num || 0);
    }

    /**
     * Parse angka format Indonesia ke float
     */
    function parseIdNumber(val) {
        if (val === null || val === undefined || val === '') return 0;
        if (typeof val === 'number') return val;
        const cleaned = String(val).replace(/\./g, '').replace(',', '.');
        return parseFloat(cleaned) || 0;
    }

    function updateEmptyState() {
        const rows = container.querySelectorAll('tr.detail-row');
        if (rows.length === 0) {
            emptyState.classList.remove('hidden');
        } else {
            emptyState.classList.add('hidden');
        }
        document.getElementById('card_total_karyawan').innerText = rows.length + ' Orang';
    }

    function calculateTotals() {
        let sumKes = 0, sumKet = 0;
        let sumJhtB = 0, sumJhtH = 0, sumJkk = 0, sumJkm = 0;
        let sumBpuJht = 0, sumBpuJkk = 0, sumBpuJkm = 0;
        let sumJpB = 0, sumJpH = 0;
        
        document.querySelectorAll('.input-kes').forEach(i => sumKes += parseIdNumber(i.value));
        document.querySelectorAll('.input-ket').forEach(i => sumKet += parseIdNumber(i.value));
        document.querySelectorAll('.input-jht-biaya').forEach(i => sumJhtB += parseIdNumber(i.value));
        document.querySelectorAll('.input-jht-hutang').forEach(i => sumJhtH += parseIdNumber(i.value));
        document.querySelectorAll('.input-jkk').forEach(i => sumJkk += parseIdNumber(i.value));
        document.querySelectorAll('.input-jkm').forEach(i => sumJkm += parseIdNumber(i.value));
        document.querySelectorAll('.input-jkk-hutang').forEach(i => sumBpuJht += parseIdNumber(i.value));
        document.querySelectorAll('.input-bpu-jkk').forEach(i => sumBpuJkk += parseIdNumber(i.value));
        document.querySelectorAll('.input-bpu-jkm').forEach(i => sumBpuJkm += parseIdNumber(i.value));
        document.querySelectorAll('.input-jp-biaya').forEach(i => sumJpB += parseIdNumber(i.value));
        document.querySelectorAll('.input-jp-hutang').forEach(i => sumJpH += parseIdNumber(i.value));

        document.getElementById('total_kes').innerText        = 'Rp ' + formatNumber(sumKes);
        document.getElementById('total_ket').innerText        = 'Rp ' + formatNumber(sumKet);
        document.getElementById('total_jht_biaya').innerText  = 'Rp ' + formatNumber(sumJhtB);
        document.getElementById('total_jht_hutang').innerText = 'Rp ' + formatNumber(sumJhtH);
        document.getElementById('total_jkk').innerText        = 'Rp ' + formatNumber(sumJkk);
        document.getElementById('total_jkm').innerText        = 'Rp ' + formatNumber(sumJkm);
        document.getElementById('total_jkk_hutang').innerText = 'Rp ' + formatNumber(sumBpuJht);
        document.getElementById('total_bpu_jkk').innerText    = 'Rp ' + formatNumber(sumBpuJkk);
        document.getElementById('total_bpu_jkm').innerText    = 'Rp ' + formatNumber(sumBpuJkm);
        document.getElementById('total_jp_biaya').innerText   = 'Rp ' + formatNumber(sumJpB);
        document.getElementById('total_jp_hutang').innerText  = 'Rp ' + formatNumber(sumJpH);
        
        const totalJkn = sumKes + sumKet;
        const totalJamsostek = sumJhtB + sumJhtH + sumJkk + sumJkm + sumBpuJht + sumBpuJkk + sumBpuJkm + sumJpB + sumJpH;
        const grandTotal = totalJkn + totalJamsostek;
        
        document.getElementById('grand_total').innerText = 'Rp ' + formatNumber(grandTotal);

        // Update Summary Cards
        document.getElementById('card_total_jkn').innerText = 'Rp ' + formatNumber(totalJkn);
        document.getElementById('card_total_jamsostek').innerText = 'Rp ' + formatNumber(totalJamsostek);
        document.getElementById('card_grand_total').innerText = 'Rp ' + formatNumber(grandTotal);
        updateEmptyState();
    }

    function addRow(karyawanId = null, autoCalculate = false) {
        rowCount++;
        
        let karyawanInputHTML = '';
        if (karyawanId) {
            const k = karyawans.find(k => k.id == karyawanId);
            karyawanInputHTML = `
                <input type="hidden" name="details[${rowCount}][karyawan_id]" value="${karyawanId}" class="karyawan-hidden-input">
                <div class="font-semibold text-gray-800 text-xs truncate" style="max-width: 200px;" title="${k ? k.nama_lengkap : ''}">
                    ${k ? k.nama_lengkap : ''}
                </div>
            `;
        } else {
            let options = '<option value="">-- Pilih Karyawan --</option>';
            karyawans.forEach(k => {
                options += `<option value="${k.id}">${k.nama_lengkap}</option>`;
            });
            karyawanInputHTML = `
                <select name="details[${rowCount}][karyawan_id]" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 select2" required>
                    ${options}
                </select>
            `;
        }

        const tr = document.createElement('tr');
        tr.className = "border-b border-gray-100 detail-row hover:bg-gray-50/70 transition-colors";
        tr.innerHTML = `
            <td class="px-3 py-2.5 text-center align-middle row-number text-gray-400 font-mono font-medium">${rowCount}</td>
            <td class="px-3 py-2.5 align-middle">
                ${karyawanInputHTML}
                <div class="info-jkn mt-1 hidden"></div>
            </td>
            <td class="px-3 py-2.5 align-middle text-center text-[11px] group-text whitespace-nowrap">-</td>
            <td class="px-2 py-2.5 align-middle text-center">
                <select class="w-full text-[11px] py-1 px-1.5 text-center select-tipe-jkn border-gray-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-md bg-gray-50/60 shadow-2xs font-medium" name="details[${rowCount}][tipe_jkn]">
                    <option value="tunjangan_hutang">TOTAL JKN</option>
                </select>
            </td>
            <td class="px-2 py-2.5 align-middle text-center border-r border-gray-200">
                <select class="w-full text-[11px] py-1 px-1.5 text-center select-tipe-bp-jamsostek border-gray-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-md bg-gray-50/60 shadow-2xs font-medium" name="details[${rowCount}][tipe_bp_jamsostek]">
                    <option value="total">TOTAL BP JAMSOSTEK</option>
                </select>
            </td>
            
            {{-- JKN (2) --}}
            <td class="px-1.5 py-2 align-middle bg-indigo-50/10">
                <input type="text" name="details[${rowCount}][bpjs_kesehatan]" class="w-full text-right font-mono text-xs input-kes font-semibold text-indigo-700 bg-gray-50/60 hover:bg-white focus:bg-white border border-transparent hover:border-gray-200 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-200 rounded px-2 py-1 transition" value="0" autocomplete="off">
            </td>
            <td class="px-1.5 py-2 align-middle bg-indigo-50/10 border-r border-indigo-100">
                <input type="text" name="details[${rowCount}][bpjs_ketenagakerjaan]" class="w-full text-right font-mono text-xs input-ket font-semibold text-indigo-700 bg-gray-50/60 hover:bg-white focus:bg-white border border-transparent hover:border-gray-200 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-200 rounded px-2 py-1 transition" value="0" autocomplete="off">
            </td>
            
            {{-- PPU (4) --}}
            <td class="px-1.5 py-2 align-middle bg-emerald-50/10">
                <input type="text" name="details[${rowCount}][jht_biaya]" class="w-full text-right font-mono text-xs input-jht-biaya font-semibold text-emerald-700 bg-gray-50/60 hover:bg-white focus:bg-white border border-transparent hover:border-gray-200 focus:border-emerald-400 focus:ring-1 focus:ring-emerald-200 rounded px-2 py-1 transition" value="0" autocomplete="off">
            </td>
            <td class="px-1.5 py-2 align-middle bg-emerald-50/10">
                <input type="text" name="details[${rowCount}][jht_hutang]" class="w-full text-right font-mono text-xs input-jht-hutang font-semibold text-emerald-700 bg-gray-50/60 hover:bg-white focus:bg-white border border-transparent hover:border-gray-200 focus:border-emerald-400 focus:ring-1 focus:ring-emerald-200 rounded px-2 py-1 transition" value="0" autocomplete="off">
            </td>
            <td class="px-1.5 py-2 align-middle bg-emerald-50/10">
                <input type="text" name="details[${rowCount}][jkk_tunjangan]" class="w-full text-right font-mono text-xs input-jkk font-semibold text-emerald-700 bg-gray-50/60 hover:bg-white focus:bg-white border border-transparent hover:border-gray-200 focus:border-emerald-400 focus:ring-1 focus:ring-emerald-200 rounded px-2 py-1 transition" value="0" autocomplete="off">
            </td>
            <td class="px-1.5 py-2 align-middle bg-emerald-50/10 border-r border-emerald-100">
                <input type="text" name="details[${rowCount}][jkm_tunjangan]" class="w-full text-right font-mono text-xs input-jkm font-semibold text-emerald-700 bg-gray-50/60 hover:bg-white focus:bg-white border border-transparent hover:border-gray-200 focus:border-emerald-400 focus:ring-1 focus:ring-emerald-200 rounded px-2 py-1 transition" value="0" autocomplete="off">
            </td>

            {{-- BPU (3) --}}
            <td class="px-1.5 py-2 align-middle bg-rose-50/10">
                <input type="text" name="details[${rowCount}][jkk_hutang]" class="w-full text-right font-mono text-xs input-jkk-hutang font-semibold text-rose-600 bg-gray-50/60 hover:bg-white focus:bg-white border border-transparent hover:border-gray-200 focus:border-rose-400 focus:ring-1 focus:ring-rose-200 rounded px-2 py-1 transition" value="0" autocomplete="off">
            </td>
            <td class="px-1.5 py-2 align-middle bg-sky-50/10">
                <input type="text" name="details[${rowCount}][bpu_jkk_tunjangan]" class="w-full text-right font-mono text-xs input-bpu-jkk font-semibold text-sky-700 bg-gray-50/60 hover:bg-white focus:bg-white border border-transparent hover:border-gray-200 focus:border-sky-400 focus:ring-1 focus:ring-sky-200 rounded px-2 py-1 transition" value="0" autocomplete="off">
            </td>
            <td class="px-1.5 py-2 align-middle bg-sky-50/10 border-r border-sky-100">
                <input type="text" name="details[${rowCount}][bpu_jkm]" class="w-full text-right font-mono text-xs input-bpu-jkm font-semibold text-sky-700 bg-gray-50/60 hover:bg-white focus:bg-white border border-transparent hover:border-gray-200 focus:border-sky-400 focus:ring-1 focus:ring-sky-200 rounded px-2 py-1 transition" value="0" autocomplete="off">
            </td>
            
            {{-- JP (2) --}}
            <td class="px-1.5 py-2 align-middle bg-amber-50/10">
                <input type="text" name="details[${rowCount}][jp_biaya]" class="w-full text-right font-mono text-xs input-jp-biaya font-semibold text-amber-700 bg-gray-50/60 hover:bg-white focus:bg-white border border-transparent hover:border-gray-200 focus:border-amber-400 focus:ring-1 focus:ring-amber-200 rounded px-2 py-1 transition" value="0" autocomplete="off">
            </td>
            <td class="px-1.5 py-2 align-middle bg-amber-50/10 border-r border-gray-200">
                <input type="text" name="details[${rowCount}][jp_hutang]" class="w-full text-right font-mono text-xs input-jp-hutang font-semibold text-amber-700 bg-gray-50/60 hover:bg-white focus:bg-white border border-transparent hover:border-gray-200 focus:border-amber-400 focus:ring-1 focus:ring-amber-200 rounded px-2 py-1 transition" value="0" autocomplete="off">
            </td>
            
            {{-- Subtotal & Action --}}
            <td class="px-3 py-2 text-right font-mono font-bold align-middle subtotal-text text-teal-800 bg-teal-50/40 whitespace-nowrap">Rp 0</td>
            <td class="px-2 py-2 text-center align-middle">
                <button type="button" class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-rose-400 hover:text-rose-600 hover:bg-rose-50 transition-all btn-remove" title="Hapus Baris">
                    <i class="fas fa-trash-alt text-xs"></i>
                </button>
            </td>
        `;

        container.appendChild(tr);

        const inputKes     = tr.querySelector('.input-kes');
        const inputKet     = tr.querySelector('.input-ket');
        const subtotalText = tr.querySelector('.subtotal-text');
        const infoJkn      = tr.querySelector('.info-jkn');
        const groupText    = tr.querySelector('.group-text');
        const selectTipe   = tr.querySelector('.select-tipe-jkn');
        const selectTipeJamsostek = tr.querySelector('.select-tipe-bp-jamsostek');
        const inputJhtBiaya  = tr.querySelector('.input-jht-biaya');
        const inputJhtHutang = tr.querySelector('.input-jht-hutang');
        const inputJkk       = tr.querySelector('.input-jkk');
        const inputJkm       = tr.querySelector('.input-jkm');
        const inputBpuJht    = tr.querySelector('.input-jkk-hutang');
        const inputBpuJkk    = tr.querySelector('.input-bpu-jkk');
        const inputBpuJkm    = tr.querySelector('.input-bpu-jkm');
        const inputJpBiaya   = tr.querySelector('.input-jp-biaya');
        const inputJpHutang  = tr.querySelector('.input-jp-hutang');
        
        const updateSubtotal = () => {
            const kes       = parseIdNumber(inputKes.value);
            const ket       = parseIdNumber(inputKet.value);
            const jhtB      = parseIdNumber(inputJhtBiaya.value);
            const jhtH      = parseIdNumber(inputJhtHutang.value);
            const jkk       = parseIdNumber(inputJkk.value);
            const jkm       = parseIdNumber(inputJkm.value);
            const bpuJht    = parseIdNumber(inputBpuJht.value);
            const bpuJkk    = parseIdNumber(inputBpuJkk.value);
            const bpuJkm    = parseIdNumber(inputBpuJkm.value);
            const jpB       = parseIdNumber(inputJpBiaya.value);
            const jpH       = parseIdNumber(inputJpHutang.value);
            subtotalText.innerText = 'Rp ' + formatNumber(kes + ket + jhtB + jhtH + jkk + jkm + bpuJht + bpuJkk + bpuJkm + jpB + jpH);
            calculateTotals();
        };

        // Format saat kehilangan fokus atau nilai berubah (oleh sistem / user)
        [inputKes, inputKet, inputJhtBiaya, inputJhtHutang, inputJkk, inputJkm, inputBpuJht, inputBpuJkk, inputBpuJkm, inputJpBiaya, inputJpHutang].forEach(function(input) {
            if (!input) return;
            input.addEventListener('change', function() {
                this.value = formatNumber(parseIdNumber(this.value));
                updateSubtotal();
            });
            input.addEventListener('input', updateSubtotal);
        });

        tr.querySelector('.btn-remove').addEventListener('click', function() {
            tr.remove();
            updateRowNumbers();
            calculateTotals();
        });
        
        let $select = null;
        if (!karyawanId && typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
            $select = jQuery(tr.querySelector('.select2')).select2({
                placeholder: "-- Pilih Karyawan --",
                width: '100%'
            });
        }

        function updateInfoBadgeJkn(kId) {
            const karyawan = karyawans.find(k => k.id == kId);
            if (!karyawan) { groupText.innerHTML = '-'; return; }

            let badges = [];

            if (karyawan.group_jkn) {
                badges.push(`<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-800" title="Group JKN">${karyawan.group_jkn}</span>`);
                selectTipe.options[0].text = `TOTAL JKN`;
            }

            if (karyawan.group_bp_jamsostek) {
                const isPpu = karyawan.group_bp_jamsostek.toUpperCase().includes('PPU');
                const badgeClass = isPpu ? 'bg-emerald-100 text-emerald-800' : 'bg-sky-100 text-sky-800';
                badges.push(`<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold ${badgeClass}" title="Group Jamsostek">${karyawan.group_bp_jamsostek}</span>`);
            }

            groupText.innerHTML = badges.length > 0 ? badges.join('<br>') : '-';
        }

        function calculateBpjsForKaryawan(kId, tipeJkn) {
            if (!kId) {
                inputKes.value = 0;
                inputKet.value = 0;
                updateSubtotal();
                return;
            }

            const karyawan = karyawans.find(k => k.id == kId);
            if (!karyawan) return;

            let nominalKes = 0;
            let nominalKet = 0;

            if (karyawan.group_jkn && tipeJkn !== 'manual') {
                const rumus = rumusBpjs.find(r => r.jenis === 'jkn' && r.group_name === karyawan.group_jkn);
                if (rumus) {
                    const dpp = parseIdNumber(karyawan.dpp_jkn);
                    const tunjPersen = parseFloat(rumus.tunjangan_persen || 0);
                    const hutangPersen = parseFloat(rumus.hutang_persen || 0);
                    let persen = 0;

                    if (tipeJkn === 'tunjangan_hutang') {
                        persen = tunjPersen;
                        nominalKet += (hutangPersen / 100) * dpp;
                    }
                    nominalKes = (persen / 100) * dpp;
                }
            } else if (tipeJkn === 'manual') {
                nominalKes = parseIdNumber(inputKes.value);
            }

            if (karyawan.group_bp_jamsostek && tipeJkn !== 'manual') {
                const karyawanCabang = (karyawan.cabang_bpjs || '').trim().toLowerCase();
                const rumus = rumusBpjs.find(r => {
                    if (r.jenis !== 'jamsostek') return false;
                    if (r.group_name !== karyawan.group_bp_jamsostek) return false;
                    if (karyawanCabang) {
                        const rumusCabang = (r.cabang_bpjs || '').trim().toLowerCase();
                        return rumusCabang === karyawanCabang;
                    }
                    return true;
                });

                if (rumus) {
                    const dppJamsostek = parseIdNumber(karyawan.dpp_bp_jamsostek);
                    let jhtBiaya = 0, jhtHutang = 0, jkkTunj = 0, jkmTunj = 0;
                    let bpuJhtVal = 0, bpuJkkTunjVal = 0, bpuJkmVal = 0;
                    let jpBiaya = 0, jpHutang = 0;
                    
                    if (rumus.group_name.toUpperCase().includes('PPU')) {
                        const jhtBiayaMaster  = parseFloat(rumus.jht_biaya   || 0);
                        const jhtHutangMaster = parseFloat(rumus.jht_hutang  || 0);
                        
                        jhtHutang = (jhtHutangMaster / 100) * dppJamsostek;
                        jhtBiaya  = ((jhtBiayaMaster  / 100) * dppJamsostek) - jhtHutang;
                        
                        const jpBiayaMaster  = parseFloat(rumus.jp_biaya  || 0);
                        const jpHutangMaster = parseFloat(rumus.jp_hutang || 0);
                        
                        jpHutang = (jpHutangMaster / 100) * dppJamsostek;
                        jpBiaya  = ((jpBiayaMaster  / 100) * dppJamsostek) - jpHutang;

                        jkkTunj = (parseFloat(rumus.jkk_tunjangan || 0) / 100) * dppJamsostek;
                        jkmTunj = (parseFloat(rumus.jkm_tunjangan || 0) / 100) * dppJamsostek;

                    } else {
                        // ── BPU (non-PPU) ────────────────────────────────────────────
                        const hutangPersen = parseFloat(rumus.hutang_persen || 0);
                        if (tipeJkn === 'tunjangan_hutang') {
                            nominalKet += (hutangPersen / 100) * dppJamsostek;
                        }

                        let tiers = rumus.hutang_tiers;
                        if (typeof tiers === 'string') {
                            try { tiers = JSON.parse(tiers); } catch(e) { tiers = []; }
                        }
                        if (Array.isArray(tiers) && tiers.length > 0) {
                            const matchedTier = tiers.find(t => parseFloat(t.dpp || 0) === dppJamsostek);
                            if (matchedTier) {
                                bpuJhtVal = parseFloat(matchedTier.potongan || 0);
                            } else {
                                bpuJhtVal = tiers.reduce((sum, t) => sum + parseFloat(t.potongan || 0), 0);
                            }
                        }

                        // BPU JKK 1% (Tunjangan)
                        const tunjPersenMaster = parseFloat(rumus.tunjangan_persen || 0);
                        let baseTunjangan = (tunjPersenMaster / 100) * dppJamsostek;

                        if (rumus.diskon_status === 'ada' && parseFloat(rumus.diskon_nilai || 0) > 0) {
                            const diskonNilai = parseFloat(rumus.diskon_nilai || 0);
                            const diskonTipe  = (rumus.diskon_tipe || 'persen').toLowerCase();
                            if (diskonTipe === 'persen') {
                                baseTunjangan = baseTunjangan * (diskonNilai / 100);
                            } else {
                                baseTunjangan = baseTunjangan * diskonNilai;
                            }
                        }

                        bpuJkkTunjVal = Math.max(0, baseTunjangan - bpuJhtVal);

                        // BPU JKM
                        const biayaMaster = parseFloat(rumus.biaya_persen || 0);
                        bpuJkmVal = biayaMaster;

                        if (rumus.diskon_status === 'ada' && parseFloat(rumus.diskon_nilai || 0) > 0) {
                            const diskonNilai = parseFloat(rumus.diskon_nilai || 0);
                            const diskonTipe  = (rumus.diskon_tipe || 'persen').toLowerCase();
                            if (diskonTipe === 'persen') {
                                bpuJkmVal = biayaMaster * (diskonNilai / 100);
                            } else {
                                bpuJkmVal = biayaMaster * diskonNilai;
                            }
                        }
                    }
                    
                    inputJhtBiaya.value  = formatNumber(jhtBiaya);
                    inputJhtHutang.value = formatNumber(jhtHutang);
                    inputJkk.value       = formatNumber(jkkTunj);
                    inputJkm.value       = formatNumber(jkmTunj);
                    inputBpuJht.value    = formatNumber(Math.round(bpuJhtVal));
                    inputBpuJkk.value    = formatNumber(Math.round(bpuJkkTunjVal));
                    inputBpuJkm.value    = formatNumber(Math.round(bpuJkmVal));
                    inputJpBiaya.value   = formatNumber(jpBiaya);
                    inputJpHutang.value  = formatNumber(jpHutang);
                }
            } else {
                inputJhtBiaya.value  = 0;
                inputJhtHutang.value = 0;
                inputJkk.value       = 0;
                inputJkm.value       = 0;
                inputBpuJht.value    = 0;
                inputBpuJkk.value    = 0;
                inputBpuJkm.value    = 0;
                inputJpBiaya.value   = 0;
                inputJpHutang.value  = 0;
            }

            if (tipeJkn !== 'manual') {
                inputKes.value = formatNumber(Math.round(nominalKes));
            }
            
            inputKet.value = formatNumber(Math.round(nominalKet));
            updateSubtotal();
        }

        const handleKaryawanChange = function(kId) { calculateBpjsForKaryawan(kId, selectTipe.value); };

        if ($select) { 
            $select.on('change', function() { handleKaryawanChange($select.val()); }); 
        } else if (!karyawanId) { 
            tr.querySelector('.select2').addEventListener('change', function(e) { handleKaryawanChange(e.target.value); }); 
        }

        selectTipe.addEventListener('change', function() {
            const kId = karyawanId ? karyawanId : ($select ? $select.val() : tr.querySelector('.select2').value);
            calculateBpjsForKaryawan(kId, this.value);
        });

        if (karyawanId) {
            setTimeout(() => {
                selectTipe.value = 'tunjangan_hutang';
                updateInfoBadgeJkn(karyawanId);
                if (autoCalculate) {
                    calculateBpjsForKaryawan(karyawanId, 'tunjangan_hutang');
                }
            }, 100);
        }

        updateEmptyState();
    }

    function updateRowNumbers() {
        document.querySelectorAll('.row-number').forEach((td, index) => {
            td.innerText = index + 1;
        });
        updateEmptyState();
    }

    btnAdd.addEventListener('click', () => addRow(null));

    // Bersihkan format angka Indonesia sebelum submit
    document.getElementById('pranota-form').addEventListener('submit', function(e) {
        document.querySelectorAll(
            '.input-kes, .input-ket, .input-jht-biaya, .input-jht-hutang, ' +
            '.input-jkk, .input-jkk-hutang, .input-jkm, .input-bpu-jkk, .input-bpu-jkm, ' +
            '.input-jp-biaya, .input-jp-hutang'
        ).forEach(function(input) {
            input.value = parseIdNumber(input.value);
        });
    });
    
    document.getElementById('btn-generate-all').addEventListener('click', () => {
        container.innerHTML = '';
        rowCount = 0;

        let count = 0;
        karyawans.forEach(k => {
            if (k.group_jkn || k.group_bp_jamsostek) {
                addRow(k.id, true);
                count++;
            }
        });
        
        if (count === 0) {
            addRow(null);
        }
        
        updateRowNumbers();
    });

    // Inisialisasi awal
    updateEmptyState();
});
</script>
@endsection
