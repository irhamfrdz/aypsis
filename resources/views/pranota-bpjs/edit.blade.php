@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pranota-bpjs-allowances.css') }}?v={{ filemtime(public_path('css/pranota-bpjs-allowances.css')) }}">
@endpush

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

    {{-- Page Header --}}
    <div class="sm:flex sm:items-center sm:justify-between mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Edit Pranota BPJS</h1>
            <p class="text-sm text-gray-500 mt-1">
                <span class="inline-flex items-center gap-1 bg-amber-100 text-amber-700 text-xs font-semibold px-2 py-0.5 rounded-full">
                    <i class="fas fa-pencil-alt text-amber-500" style="font-size:9px"></i>
                    {{ $pranota_bpj->nomor_pranota }}
                </span>
            </p>
        </div>
        <div>
            <a href="{{ route('pranota-bpjs.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 hover:border-gray-300 hover:bg-gray-50 text-gray-600 text-sm font-medium rounded-lg transition-colors">
                <i class="fas fa-arrow-left text-xs"></i>
                Kembali
            </a>
        </div>
    </div>

    <form id="pranota-form" action="{{ route('pranota-bpjs.update', $pranota_bpj->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Section: Informasi Pranota --}}
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm mb-6">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100">
                <div class="w-8 h-8 rounded-full bg-teal-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-file-invoice text-teal-600 text-sm"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-800">Informasi Pranota</h2>
                    <p class="text-xs text-gray-400">Tanggal dan periode pembayaran BPJS</p>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5" for="tanggal_pranota">
                            Tanggal Pranota <span class="text-red-500">*</span>
                        </label>
                        <input id="tanggal_pranota" name="tanggal_pranota" type="date"
                            class="form-input w-full text-sm"
                            value="{{ $pranota_bpj->tanggal_pranota->format('Y-m-d') }}" required />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5" for="periode_bulan">
                            Bulan Periode <span class="text-red-500">*</span>
                        </label>
                        <select id="periode_bulan" name="periode_bulan" class="form-select w-full text-sm" required>
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ $pranota_bpj->periode_bulan == $i ? 'selected' : '' }}>
                                    {{ str_pad($i, 2, '0', STR_PAD_LEFT) }} — {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5" for="periode_tahun">
                            Tahun Periode <span class="text-red-500">*</span>
                        </label>
                        <input id="periode_tahun" name="periode_tahun" type="number"
                            class="form-input w-full text-sm"
                            value="{{ $pranota_bpj->periode_tahun }}" required />
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5" for="keterangan">
                        Keterangan Tambahan
                    </label>
                    <textarea id="keterangan" name="keterangan" class="form-textarea w-full text-sm" rows="2"
                        placeholder="Opsional...">{{ $pranota_bpj->keterangan }}</textarea>
                </div>
            </div>
        </div>

        {{-- Section: Detail Karyawan --}}
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm mb-6">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-users text-indigo-600 text-sm"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-gray-800">Detail Karyawan</h2>
                        <p class="text-xs text-gray-400">Data karyawan dan nominal BPJS yang akan diperbarui</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" id="btn-generate-all"
                        class="inline-flex items-center gap-2 px-3 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold rounded-lg transition-colors shadow-sm">
                        <i class="fas fa-magic text-xs"></i>
                        Hitung Semua Karyawan
                    </button>
                    <button type="button" id="btn-add-karyawan"
                        class="inline-flex items-center gap-2 px-3 py-2 bg-indigo-500 hover:bg-indigo-600 text-white text-xs font-semibold rounded-lg transition-colors shadow-sm">
                        <i class="fas fa-plus text-xs"></i>
                        Tambah Karyawan
                    </button>
                </div>
            </div>

            <div class="bpjs-table-scroll overflow-x-auto" role="region" aria-label="Rincian iuran BPJS, gulir untuk melihat seluruh data" tabindex="0">
                <table class="table-auto w-full" id="tabel-detail">
                    <thead class="text-xs font-semibold uppercase text-gray-600 bg-gray-50 border-b-2 border-gray-200 tracking-wider">
                        <tr>
                            <th class="px-4 py-3 w-10 text-center">#</th>
                            <th class="px-4 py-3 text-left">Nama Karyawan</th>
                            <th class="px-4 py-3 text-left">Group</th>
                            <th class="px-4 py-3 text-center" style="min-width:180px">Tipe JKN</th>
                            <th class="px-3 py-3 text-right font-bold text-gray-600 col-info-dpp-jkn" style="min-width:150px">DPP JKN</th>
                            <th class="px-4 py-3 text-center" style="min-width:180px">Tipe BP JAMSOSTEK</th>
                            <th class="px-3 py-3 text-right font-bold text-gray-600 col-info-dpp-jam" style="min-width:150px">DPP BP Jamsostek</th>
                            <th class="px-4 py-3 text-right" style="min-width:110px">KIS 4% (TUNJ)</th>
                            <th class="px-4 py-3 text-right" style="min-width:110px">KIS 4% (BIAY)</th>
                            <th class="px-4 py-3 text-right" style="min-width:110px">BPJS-KIS 1%</th>
                            <th class="px-3 py-3 text-right font-bold bpjs-group-total-jkn col-group-jkn" style="min-width:130px">TOTAL JKN</th>
                            <th class="px-4 py-3 text-right" style="min-width:100px">JHT 3.7%</th>
                            <th class="px-4 py-3 text-right" style="min-width:90px">JHT 2%</th>
                            <th class="px-4 py-3 text-right" style="min-width:100px">JKK 0.24%</th>
                            <th class="px-4 py-3 text-right" style="min-width:100px">JKM 0.3%</th>
                            <th class="px-4 py-3 text-right" style="min-width:90px">PPU JP 2%</th>
                            <th class="px-4 py-3 text-right" style="min-width:90px">PPU JP 1%</th>
                            <th class="px-3 py-3 text-right font-bold bpjs-group-total-ppu col-group-ppu" style="min-width:130px">TOTAL PPU</th>
<th class="bpjs-allowance-green px-3 py-3 text-right bg-green-50" style="min-width:220px">Tunjangan JKK 0,24% + JKM 0,3%</th><th class="bpjs-allowance-green px-3 py-3 text-right bg-green-50" style="min-width:220px">Tunjangan BPU JKK 1% + JKM</th><th class="bpjs-allowance-green px-3 py-3 text-right bg-green-50" style="min-width:220px">Tunjangan JKN 4%</th><th class="bpjs-allowance-red px-3 py-3 text-right bg-red-50 text-red-800" style="min-width:220px">Beban KARY. JHT 2%, JP 1% & BPU JHT</th><th class="bpjs-allowance-yellow px-3 py-3 text-right bg-yellow-50 text-yellow-800" style="min-width:220px">Beban KARY. JKN 1%</th><th class="bpjs-allowance-blue px-3 py-3 text-right bg-blue-50 text-blue-800" style="min-width:220px">Beban PT. PPU JHT 3.7%, PPU JP 2% & JHT 2% (BIAYA)</th><th class="bpjs-allowance-yellow px-3 py-3 text-right bg-yellow-50 text-yellow-800" style="min-width:220px">Beban NON KARY. JKN & BPJS</th><th class="px-3 py-3 bg-orange-50 text-orange-800" style="min-width:220px">No Voucher</th>
                            <th class="px-4 py-3 text-right">Subtotal (Rp)</th>
                            <th class="px-4 py-3 w-10 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="detail-container" class="divide-y divide-gray-100 bg-white">
                        <!-- Rows will be added here -->
                    </tbody>
                    <tfoot class="bg-gray-50 border-t-2 border-gray-200 shadow-sm">
                        <tr>
                            <td colspan="7" class="px-4 py-4 text-right text-sm font-bold text-gray-700 uppercase tracking-wide">Total Keseluruhan:</td>
                            <td class="px-4 py-4 text-right font-bold text-indigo-700 text-sm" id="total_kes">Rp 0</td>
                            <td class="px-4 py-4 text-right font-bold text-indigo-700 text-sm" id="total_kes_biaya">Rp 0</td>
                            <td class="px-4 py-4 text-right font-bold text-indigo-700 text-sm" id="total_ket">Rp 0</td>
                            <td class="px-3 py-3 text-right font-mono font-bold bpjs-group-total-jkn col-group-jkn" id="total_group_jkn">Rp 0,00</td>
                            <td class="px-4 py-4 text-right font-bold text-emerald-700 text-sm" id="total_jht_biaya">Rp 0</td>
                            <td class="px-4 py-4 text-right font-bold text-emerald-700 text-sm" id="total_jht_hutang">Rp 0</td>
                            <td class="px-4 py-4 text-right font-bold text-emerald-700 text-sm" id="total_jkk">Rp 0</td>
                            <td class="px-4 py-4 text-right font-bold text-emerald-700 text-sm" id="total_jkm">Rp 0</td>
                            <td class="px-4 py-4 text-right font-bold text-emerald-700 text-sm" id="total_jp_biaya">Rp 0</td>
                            <td class="px-4 py-4 text-right font-bold text-emerald-700 text-sm" id="total_jp_hutang">Rp 0</td>
                            <td class="px-3 py-3 text-right font-mono font-bold bpjs-group-total-ppu col-group-ppu" id="total_group_ppu">Rp 0,00</td>
                            <td class="bpjs-allowance-green px-3 py-3 text-right font-mono bg-green-50" id="total-allowance-ppu">Rp 0,00</td>
<td class="bpjs-allowance-green px-3 py-3 text-right font-mono bg-green-50" id="total-allowance-bpu">Rp 0,00</td>
<td class="bpjs-allowance-green px-3 py-3 text-right font-mono bg-green-50" id="total-allowance-jkn">Rp 0,00</td>
<td class="bpjs-allowance-red px-3 py-3 text-right font-mono bg-red-50" id="total-allowance-kary_jht">Rp 0,00</td>
<td class="bpjs-allowance-yellow px-3 py-3 text-right font-mono bg-yellow-50" id="total-allowance-kary_jkn">Rp 0,00</td>
<td class="bpjs-allowance-blue px-3 py-3 text-right font-mono bg-blue-50" id="total-allowance-pt">Rp 0,00</td>
<td class="bpjs-allowance-yellow px-3 py-3 text-right font-mono bg-yellow-50" id="total-allowance-non_kary">Rp 0,00</td>
<td class="px-3 py-3 bg-orange-50"></td>
<td class="px-4 py-4 text-right font-bold text-teal-600 text-base" id="grand_total">Rp 0</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
                @include('pranota-bpjs.partials.allowance-summary')
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('pranota-bpjs.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 hover:border-gray-300 hover:bg-gray-50 text-gray-600 text-sm font-medium rounded-lg transition-colors">
                <i class="fas fa-times text-xs"></i>
                Batal
            </a>
            <button type="submit"
                class="inline-flex items-center gap-2 px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                <i class="fas fa-save text-xs"></i>
                Update Pranota
            </button>
        </div>
    </form>
</div>


<script src="{{ asset('js/pranota-bpjs-jkn.js') }}?v={{ filemtime(public_path('js/pranota-bpjs-jkn.js')) }}"></script>
<script src="{{ asset('js/pranota-bpjs-performance.js') }}?v={{ filemtime(public_path('js/pranota-bpjs-performance.js')) }}"></script>
<script src="{{ asset('js/pranota-bpjs-allowances.js') }}?v={{ filemtime(public_path('js/pranota-bpjs-allowances.js')) }}"></script>
<script src="{{ asset('js/pranota-bpjs-batch.js') }}?v={{ filemtime(public_path('js/pranota-bpjs-batch.js')) }}"></script>
<script src="{{ asset('js/pranota-bpjs-jamsostek.js') }}?v={{ filemtime(public_path('js/pranota-bpjs-jamsostek.js')) }}"></script>
<script src="{{ asset('js/pranota-bpjs-submit.js') }}?v={{ filemtime(public_path('js/pranota-bpjs-submit.js')) }}"></script>
<script>
document.addEventListener('DOMContentLoaded', async function() {
    const container = document.getElementById('detail-container');
    const btnAdd = document.getElementById('btn-add-karyawan');
    
    const karyawans = @json($karyawans);
    const existingDetails = @json($pranota_bpj->details);
    const rumusBpjs = @json($rumusBpjs);
    let rowCount = 0;
    let isGenerating = false;
    let rowTarget = container;
    const pendingUpdate = BpjsPerformance.scheduler(() => calculateTotals());
    const amountFormatter = new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    function formatNumber(num) {
        return amountFormatter.format(num || 0);
    }

    /**
     * Parse angka format Indonesia ke float
     * Contoh: "5.729.880,00" → 5729880
     *         5200000        → 5200000 (sudah number, langsung return)
     */
    function parseIdNumber(val) {
        return BpjsJamsostek.parseAmount(val);
    }

    function calculateTotals() {
        if (isGenerating) return;
        let sumKes = 0, sumKesBiaya = 0, sumKet = 0;
        let sumJhtB = 0, sumJhtH = 0, sumJkk = 0, sumJkm = 0, sumJpB = 0, sumJpH = 0;
        
        container.querySelectorAll('tr.detail-row').forEach(row => {
            BpjsPerformance.updateGroupTotals(row, parseIdNumber, formatNumber);
            const read = selector => BpjsPerformance.readNumber(row, selector, parseIdNumber);
            const kes = read('.input-kes');
            sumKes += kes;
            sumKesBiaya += BpjsJknColumns.split(kes,
                BpjsPerformance.field(row, '.tipe-karyawan-hidden-input')?.value, row.dataset.isSpecialCase).biaya;
            sumKet += read('.input-ket');
            sumJhtB += read('.input-jht-biaya');
            sumJhtH += read('.input-jht-hutang');
            sumJkk += read('.input-jkk');
            sumJkm += read('.input-jkm');
            sumJpB += read('.input-jp-biaya');
            sumJpH += read('.input-jp-hutang');
        });

        document.getElementById('total_kes').textContent = 'Rp ' + formatNumber(sumKes - sumKesBiaya);
        document.getElementById('total_kes_biaya').textContent = 'Rp ' + formatNumber(sumKesBiaya);
        document.getElementById('total_ket').textContent = 'Rp ' + formatNumber(sumKet);
        document.getElementById('total_jht_biaya').textContent = 'Rp ' + formatNumber(sumJhtB);
        document.getElementById('total_jht_hutang').textContent = 'Rp ' + formatNumber(sumJhtH);
        document.getElementById('total_jkk').textContent = 'Rp ' + formatNumber(sumJkk);
        document.getElementById('total_jkm').textContent = 'Rp ' + formatNumber(sumJkm);
        document.getElementById('total_jp_biaya').textContent = 'Rp ' + formatNumber(sumJpB);
        document.getElementById('total_jp_hutang').textContent = 'Rp ' + formatNumber(sumJpH);
        
        document.getElementById('total_group_jkn').textContent = 'Rp ' + formatNumber(sumKes + sumKet);
        document.getElementById('total_group_ppu').textContent = 'Rp ' + formatNumber(sumJhtB + sumJhtH + sumJkk + sumJkm + sumJpB + sumJpH);
        const grandTotal = sumKes + sumKet + sumJhtB + sumJhtH + sumJkk + sumJkm + sumJpB + sumJpH;
        document.getElementById('grand_total').textContent = 'Rp ' + formatNumber(grandTotal);
        BpjsAllowances.update(container, parseIdNumber, formatNumber);
    }

    function findKaryawan(detailOrId) {
        if (!detailOrId) return null;
        if (typeof detailOrId === 'object') {
            const kId = detailOrId.karyawan_id || detailOrId.id;
            const tipe = detailOrId.tipe_karyawan || (detailOrId.unique_id ? (detailOrId.unique_id.startsWith('KaryawanTidakTetap_') ? 'App\\Models\\KaryawanTidakTetap' : 'App\\Models\\Karyawan') : null);
            if (detailOrId.unique_id) {
                const found = karyawans.find(k => k.unique_id === detailOrId.unique_id);
                if (found) return found;
            }
            if (tipe) {
                const found = karyawans.find(k => k.tipe_karyawan === tipe && k.id == kId);
                if (found) return found;
            }
            return karyawans.find(k => k.id == kId);
        }
        return karyawans.find(k => k.unique_id == detailOrId || k.id == detailOrId);
    }

    /**
     * addRow(detail, isGenerated) — detail: objek existing, atau null untuk baris baru
     * Existing row: tipe default 'manual' agar nilai lama tidak ditimpa
     * New row     : tipe default 'tunjangan_hutang' agar langsung hitung otomatis
     */
    function addRow(detail = null, isGenerated = false) {
        rowCount++;
        
        let karyawanId = detail ? detail.karyawan_id : null;
        let valKes   = detail ? detail.bpjs_kesehatan         : 0;
        let valKet   = detail ? detail.bpjs_ketenagakerjaan   : 0;
        let jhtB     = detail ? detail.jht_biaya : 0;
        let jhtH     = detail ? detail.jht_hutang : 0;
        let jkkT     = detail ? detail.jkk_tunjangan : 0;
        let jkmT     = detail ? detail.jkm_tunjangan : 0;
        let jpB      = detail ? detail.jp_biaya : 0;
        let jpH      = detail ? detail.jp_hutang : 0;
        let subVal   = detail ? detail.total                  : 0;
        let isExistingRow = (detail !== null) && !isGenerated;
        let tipeJkn = isExistingRow ? 'manual' : 'tunjangan_hutang';

        const k = findKaryawan(detail || karyawanId);
        const statusBadge = k && k.tipe_label === 'Tidak Tetap'
            ? `<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-100 text-amber-800 ml-1">Tidak Tetap</span>`
            : '';

        let karyawanInputHTML = '';
        if (karyawanId && k) {
            karyawanInputHTML = `
                <input type="hidden" name="details[${rowCount}][karyawan_id]" value="${k.id}" class="karyawan-hidden-input">
                <input type="hidden" name="details[${rowCount}][tipe_karyawan]" value="${k.tipe_karyawan || 'App\\Models\\Karyawan'}" class="tipe-karyawan-hidden-input">
                <div class="px-2 py-2 text-sm text-gray-700 font-semibold truncate flex items-center gap-1" style="max-width: 250px;" title="${k.nama_lengkap}">
                    <span>${k.nama_lengkap}</span>
                    ${statusBadge}
                </div>
            `;
        } else {
            let options = '<option value="">-- Pilih Karyawan --</option>';
            karyawans.forEach(item => {
                const nikLabel = item.nik ? ` [${item.nik}]` : '';
                const statusLabel = item.tipe_label ? ` (${item.tipe_label})` : '';
                options += `<option value="${item.unique_id}">${item.nama_lengkap}${nikLabel}${statusLabel}</option>`;
            });
            karyawanInputHTML = `
                <input type="hidden" name="details[${rowCount}][karyawan_id]" value="" class="karyawan-hidden-input">
                <input type="hidden" name="details[${rowCount}][tipe_karyawan]" value="App\\Models\\Karyawan" class="tipe-karyawan-hidden-input">
                <select class="form-select w-full text-sm select2 select-karyawan" required>
                    ${options}
                </select>
            `;
        }

        const tr = document.createElement('tr');
        tr.className = "border-b border-gray-100 detail-row hover:bg-indigo-50/30 transition-colors";
        tr.dataset.isSpecialCase = k?.is_special_case ? '1' : '0';
        tr.dataset.groupJkn = k?.group_jkn || '';
        tr.dataset.tipeJkn = k?.tipe_jkn || '';
        tr.dataset.tipeJamsostek = k?.tipe_bp_jamsostek || '';
        tr.dataset.groupJamsostek = k?.group_bp_jamsostek || '';
        tr.innerHTML = `
            <td class="px-4 py-3 text-center align-middle row-number text-gray-500 font-medium">${rowCount}</td>
            <td class="px-4 py-3 align-middle">
                ${karyawanInputHTML}
                <span class="employee-termination-badge hidden mt-1 inline-flex rounded bg-red-100 px-2 py-0.5 text-[10px] font-semibold text-red-800"></span>
                <div class="info-jkn mt-1 hidden"></div>
            </td>
            <td class="px-4 py-3 align-middle text-center text-xs group-text bpjs-employee-group font-semibold text-gray-600">-</td>
            <td class="px-4 py-3 align-middle text-center">
                <span class="employee-tipe-jkn bpjs-employee-type">-</span>
                <input type="hidden" class="select-tipe-jkn" name="details[${rowCount}][tipe_jkn]" value="${tipeJkn}">
            </td>
            <td class="px-3 py-3 text-right align-middle font-mono text-xs col-info-dpp-jkn"><span class="employee-dpp-jkn">-</span></td>
            <td class="px-4 py-3 align-middle text-center">
                <span class="employee-tipe-jamsostek bpjs-employee-type">-</span>
                <input type="hidden" class="select-tipe-bp-jamsostek" name="details[${rowCount}][tipe_bp_jamsostek]" value="total">
            </td>
            <td class="px-3 py-3 text-right align-middle font-mono text-xs col-info-dpp-jam"><span class="employee-dpp-jamsostek">-</span></td>
            <td class="px-4 py-3 align-middle" data-jkn-tunjangan>
                <input type="text" name="details[${rowCount}][bpjs_kesehatan]" class="w-full text-right text-sm input-kes font-semibold text-indigo-700 bg-transparent border-0 p-0 focus:ring-0 focus:outline-none" value="${formatNumber(valKes)}" autocomplete="off">
            </td>
            <td class="px-4 py-3 align-middle" data-jkn-biaya></td>
            <td class="px-4 py-3 align-middle">
                <input type="text" name="details[${rowCount}][bpjs_ketenagakerjaan]" class="w-full text-right text-sm input-ket font-semibold text-indigo-700 bg-transparent border-0 p-0 focus:ring-0 focus:outline-none" value="${formatNumber(valKet)}" autocomplete="off">
            </td>
            <td class="px-3 py-2 text-right font-mono font-bold whitespace-nowrap bpjs-group-total-jkn col-group-jkn row-total-jkn">Rp 0,00</td>
            <td class="px-4 py-3 align-middle">
                <input type="text" name="details[${rowCount}][jht_biaya]" class="w-full text-right text-sm input-jht-biaya font-semibold text-emerald-700 bg-transparent border-0 p-0 focus:ring-0 focus:outline-none" value="${formatNumber(jhtB)}" autocomplete="off">
            </td>
            <td class="px-4 py-3 align-middle">
                <input type="text" name="details[${rowCount}][jht_hutang]" class="w-full text-right text-sm input-jht-hutang font-semibold text-emerald-700 bg-transparent border-0 p-0 focus:ring-0 focus:outline-none" value="${formatNumber(jhtH)}" autocomplete="off">
            </td>
            <td class="px-4 py-3 align-middle">
                <input type="text" name="details[${rowCount}][jkk_tunjangan]" class="w-full text-right text-sm input-jkk font-semibold text-emerald-700 bg-transparent border-0 p-0 focus:ring-0 focus:outline-none" value="${formatNumber(jkkT)}" autocomplete="off">
            </td>
            <td class="px-4 py-3 align-middle">
                <input type="text" name="details[${rowCount}][jkm_tunjangan]" class="w-full text-right text-sm input-jkm font-semibold text-emerald-700 bg-transparent border-0 p-0 focus:ring-0 focus:outline-none" value="${formatNumber(jkmT)}" autocomplete="off">
            </td>
            <td class="px-4 py-3 align-middle">
                <input type="text" name="details[${rowCount}][jp_biaya]" class="w-full text-right text-sm input-jp-biaya font-semibold text-emerald-700 bg-transparent border-0 p-0 focus:ring-0 focus:outline-none" value="${formatNumber(jpB)}" autocomplete="off">
            </td>
            <td class="px-4 py-3 align-middle">
                <input type="text" name="details[${rowCount}][jp_hutang]" class="w-full text-right text-sm input-jp-hutang font-semibold text-emerald-700 bg-transparent border-0 p-0 focus:ring-0 focus:outline-none" value="${formatNumber(jpH)}" autocomplete="off">
            </td>
            <td class="px-3 py-2 text-right font-mono font-bold whitespace-nowrap bpjs-group-total-ppu col-group-ppu row-total-ppu">Rp 0,00</td>
            <td class="bpjs-allowance-green px-3 py-2 text-right font-mono bg-green-50/40 whitespace-nowrap" data-allowance="ppu">Rp 0,00</td>
            <td class="bpjs-allowance-green px-3 py-2 text-right font-mono bg-green-50/40 whitespace-nowrap" data-allowance="bpu">Rp 0,00</td>
            <td class="bpjs-allowance-green px-3 py-2 text-right font-mono bg-green-50/40 whitespace-nowrap" data-allowance="jkn">Rp 0,00</td>
            <td class="bpjs-allowance-red px-3 py-2 text-right font-mono bg-red-50/40 whitespace-nowrap" data-allowance="kary_jht">Rp 0,00</td>
            <td class="bpjs-allowance-yellow px-3 py-2 text-right font-mono bg-yellow-50/40 whitespace-nowrap" data-allowance="kary_jkn">Rp 0,00</td>
            <td class="bpjs-allowance-blue px-3 py-2 text-right font-mono bg-blue-50/40 whitespace-nowrap" data-allowance="pt">Rp 0,00</td>
            <td class="bpjs-allowance-yellow px-3 py-2 text-right font-mono bg-yellow-50/40 whitespace-nowrap" data-allowance="non_kary">Rp 0,00</td>
            <td class="px-3 py-2 bg-orange-50/40"><input type="text" name="details[${rowCount}][no_voucher]" class="bpjs-voucher-input w-full rounded-lg border-gray-200 text-sm" maxlength="100" aria-label="No Voucher" placeholder="No Voucher" autocomplete="off"></td>
            <td class="px-4 py-3 text-right font-bold align-middle subtotal-text text-teal-600 block" style="padding-top: 0.75rem;">Rp ${formatNumber(subVal)}</td>
            <td class="px-4 py-3 text-center align-middle">
                <button type="button" class="text-red-400 hover:text-red-600 hover:bg-red-50 p-2 rounded-md transition-all btn-remove mt-1" title="Hapus Baris">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

        tr.querySelector('.bpjs-voucher-input').value = detail?.no_voucher || '';
        tr.allowanceOverrides = { ...(detail?.tunjangan_manual || {}) };
        if (detail?.rincian_bpjs) {
            const snapshot = document.createElement('input');
            snapshot.type = 'hidden';
            snapshot.name = `details[${rowCount}][rincian_bpjs]`;
            snapshot.value = JSON.stringify(detail.rincian_bpjs);
            tr.querySelector('td').appendChild(snapshot);
        }
        tr.dataset.groupJamsostek = k?.group_bp_jamsostek || '';
        BpjsJknColumns.syncEmploymentStatus(tr, k);
        rowTarget.appendChild(tr);
        BpjsJknColumns.syncRow(tr);

        const inputKes     = tr.querySelector('.input-kes');
        const inputKet     = tr.querySelector('.input-ket');
        const subtotalText = tr.querySelector('.subtotal-text');
        const selectTipe   = tr.querySelector('.select-tipe-jkn');
        const selectTipeJamsostek = tr.querySelector('.select-tipe-bp-jamsostek');
        const inputJhtBiaya = tr.querySelector('.input-jht-biaya');
        const inputJhtHutang = tr.querySelector('.input-jht-hutang');
        const inputJkk = tr.querySelector('.input-jkk');
        const inputJkm = tr.querySelector('.input-jkm');
        const inputJpBiaya = tr.querySelector('.input-jp-biaya');
        const inputJpHutang = tr.querySelector('.input-jp-hutang');
        
        const updateSubtotal = () => {
            BpjsJknColumns.syncRow(tr);
            BpjsPerformance.updateGroupTotals(tr, parseIdNumber, formatNumber);
            const kes = parseIdNumber(inputKes.value);
            const ket = parseIdNumber(inputKet.value);
            const jhtB = parseIdNumber(inputJhtBiaya.value);
            const jhtH = parseIdNumber(inputJhtHutang.value);
            const jkk = parseIdNumber(inputJkk.value);
            const jkm = parseIdNumber(inputJkm.value);
            const jpB = parseIdNumber(inputJpBiaya.value);
            const jpH = parseIdNumber(inputJpHutang.value);
            subtotalText.textContent = 'Rp ' + formatNumber(kes + ket + jhtB + jhtH + jkk + jkm + jpB + jpH);
            if (!isGenerating) pendingUpdate.schedule();
        };

        const inputs = [inputKes, inputKet, inputJhtBiaya, inputJhtHutang, inputJkk, inputJkm, inputJpBiaya, inputJpHutang];
        inputs.forEach(input => {
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
            const karyawan = findKaryawan(kId);
            BpjsPerformance.updateEmployeeInfo(tr, karyawan, parseIdNumber, formatNumber);
        }

        function calculateBpjsForKaryawan(kId, tipeJkn) {
            if (!kId) {
                inputKes.value = 0;
                inputKet.value = 0;
                updateSubtotal();
                return;
            }
            updateInfoBadgeJkn(kId);
            const karyawan = findKaryawan(kId);
            if (!karyawan) return;

            let nominalKes = 0;
            let nominalKet = 0;

            if (karyawan.group_jkn && tipeJkn !== 'manual') {
                const rumus = BpjsJknColumns.findFormula(rumusBpjs, karyawan);

                if ((rumus || BpjsJknColumns.isReimbursement(karyawan)) && tipeJkn === 'tunjangan_hutang') {
                    const amounts = BpjsJknColumns.calculate(rumus, parseIdNumber(karyawan.dpp_jkn), karyawan);
                    nominalKes = amounts.contribution;
                    nominalKet = amounts.debt;
                }
            } else if (tipeJkn === 'manual') {
                nominalKes = parseIdNumber(inputKes.value);
            }

            if (karyawan.group_bp_jamsostek && tipeJkn !== 'manual') {
                const rumus = rumusBpjs.find(r => r.jenis === 'jamsostek' && r.group_name === karyawan.group_bp_jamsostek);
                if (rumus) {
                    const dppJamsostek = parseIdNumber(karyawan.dpp_bp_jamsostek);
                    let jhtBiaya = 0, jhtHutang = 0, jkkTunj = 0, jkmTunj = 0, jpBiaya = 0, jpHutang = 0;
                    
                    if (rumus.group_name.toUpperCase().includes('PPU')) {
                        const jhtBiayaMaster = parseFloat(rumus.jht_biaya || 0);
                        const jhtHutangMaster = parseFloat(rumus.jht_hutang || 0);
                        
                        jhtHutang = (jhtHutangMaster / 100) * dppJamsostek;
                        jhtBiaya = ((jhtBiayaMaster / 100) * dppJamsostek) - jhtHutang;
                        
                        const jpBiayaMaster = parseFloat(rumus.jp_biaya || 0);
                        const jpHutangMaster = parseFloat(rumus.jp_hutang || 0);
                        const jpMaxDpp = parseFloat(rumus.jp_max_dpp || 0);
                        const jpMaxAge = parseFloat(rumus.jp_max_age || 0);
                        
                        // Hitung Usia Karyawan dari Tanggal Lahir
                        let usiaKaryawan = null;
                        if (karyawan.tanggal_lahir) {
                            const tglLahir = new Date(karyawan.tanggal_lahir);
                            if (!isNaN(tglLahir.getTime())) {
                                const tglPranotaInput = document.getElementById('tanggal_pranota')?.value;
                                const refDate = tglPranotaInput ? new Date(tglPranotaInput) : new Date();
                                
                                let age = refDate.getFullYear() - tglLahir.getFullYear();
                                const m = refDate.getMonth() - tglLahir.getMonth();
                                if (m < 0 || (m === 0 && refDate.getDate() < tglLahir.getDate())) {
                                    age--;
                                }
                                usiaKaryawan = age;
                            }
                        }

                        // Alur Logika Batas Usia & Maksimal DPP JP Group PPU
                        if (jpMaxAge > 0 && usiaKaryawan !== null && usiaKaryawan >= jpMaxAge) {
                            jpBiaya = 0;
                            jpHutang = 0;
                        } else {
                            let dppJp = dppJamsostek;
                            if (jpMaxDpp > 0 && dppJamsostek > jpMaxDpp) {
                                dppJp = jpMaxDpp;
                            }

                            // PPU JP 1% (Hutang Karyawan)
                            jpHutang = (jpHutangMaster / 100) * dppJp;

                            // PPU JP 2% (Biaya Perusahaan) = (JP Biaya % * dppJp) - PPU JP 1% (jpHutang)
                            jpBiaya = Math.max(0, ((jpBiayaMaster / 100) * dppJp) - jpHutang);
                        }

                        jkkTunj = (parseFloat(rumus.jkk_tunjangan || 0) / 100) * dppJamsostek;
                        jkmTunj = (parseFloat(rumus.jkm_tunjangan || 0) / 100) * dppJamsostek;
                    } else {
                        const hutangPersen = parseFloat(rumus.hutang_persen || 0);
                        let persen = 0;
                        if (tipeJkn === 'tunjangan_hutang') { persen = hutangPersen; }
                        nominalKet += (persen / 100) * dppJamsostek;
                    }
                    
                    inputJhtBiaya.value = formatNumber(jhtBiaya);
                    inputJhtHutang.value = formatNumber(jhtHutang);
                    inputJkk.value = formatNumber(jkkTunj);
                    inputJkm.value = formatNumber(jkmTunj);
                    inputJpBiaya.value = formatNumber(jpBiaya);
                    inputJpHutang.value = formatNumber(jpHutang);
                }
            } else if (tipeJkn === 'manual') {
                nominalKet = parseIdNumber(inputKet.value);
            } else {
                inputJhtBiaya.value = 0;
                inputJhtHutang.value = 0;
                inputJkk.value = 0;
                inputJkm.value = 0;
                inputJpBiaya.value = 0;
                inputJpHutang.value = 0;
            }

            if (tipeJkn !== 'manual') { 
                inputKes.value = formatNumber(nominalKes);
            }
            
            inputKet.value = formatNumber(nominalKet);
            updateSubtotal();
        }

        const handleKaryawanChange = function(kId) {
            const k = findKaryawan(kId);
            tr.dataset.isSpecialCase = k?.is_special_case ? '1' : '0';
        tr.dataset.groupJkn = k?.group_jkn || '';
        tr.dataset.tipeJkn = k?.tipe_jkn || '';
        tr.dataset.tipeJamsostek = k?.tipe_bp_jamsostek || '';
        tr.dataset.groupJamsostek = k?.group_bp_jamsostek || '';
        BpjsJknColumns.syncEmploymentStatus(tr, k);
            const hiddenKaryawanId = tr.querySelector('.karyawan-hidden-input');
            const hiddenTipeKaryawan = tr.querySelector('.tipe-karyawan-hidden-input');
            if (k) {
                if (hiddenKaryawanId) hiddenKaryawanId.value = k.id;
                if (hiddenTipeKaryawan) hiddenTipeKaryawan.value = k.tipe_karyawan || 'App\\Models\\Karyawan';
            } else {
                if (hiddenKaryawanId) hiddenKaryawanId.value = '';
                if (hiddenTipeKaryawan) hiddenTipeKaryawan.value = 'App\\Models\\Karyawan';
            }
            updateInfoBadgeJkn(k ? k.unique_id : kId);
            calculateBpjsForKaryawan(k ? k.unique_id : kId, selectTipe.value);
        };

        if ($select) { 
            $select.on('change', function() { handleKaryawanChange($select.val()); }); 
        } else if (!karyawanId) { 
            tr.querySelector('.select-karyawan')?.addEventListener('change', function(e) { handleKaryawanChange(e.target.value); }); 
        }

        selectTipe.addEventListener('change', function() {
            const kId = karyawanId ? karyawanId : ($select ? $select.val() : tr.querySelector('.select-karyawan')?.value);
            calculateBpjsForKaryawan(kId, this.value);
        });

        if (isExistingRow) {
                selectTipe.value = 'manual';
                updateInfoBadgeJkn(k ? k.unique_id : karyawanId);
                
                inputKes.value = formatNumber(valKes);
                inputKet.value = formatNumber(valKet);
                
                updateSubtotal();
        } else if (isGenerated && karyawanId) {
                selectTipe.value = 'tunjangan_hutang';
                updateInfoBadgeJkn(k ? k.unique_id : karyawanId);
                calculateBpjsForKaryawan(k ? k.unique_id : karyawanId, 'tunjangan_hutang');
        }
    }

    document.getElementById('pranota-form').addEventListener('submit', function(event) {
        if (isGenerating) { event.preventDefault(); return; }
        pendingUpdate.flush();
        BpjsAllowances.flush(container);
        prepareBpjsSubmission(this, parseIdNumber, rumusBpjs);
    });

    function updateRowNumbers() {
        document.querySelectorAll('.row-number').forEach((td, index) => {
            td.textContent = index + 1;
        });
    }

    async function generateRows(items, createRow) {
        if (isGenerating) return;
        const button = document.getElementById('btn-generate-all');
        const originalLabel = button?.innerHTML;
        const controls = Array.from(document.getElementById('pranota-form').querySelectorAll('button'));
        const previousDisabled = controls.map(control => control.disabled);
        const fragment = document.createDocumentFragment();
        const previousCount = rowCount;
        isGenerating = true;
        container.inert = true;
        controls.forEach(control => { control.disabled = true; });
        button?.setAttribute('aria-busy', 'true');
        rowTarget = fragment;
        rowCount = 0;
        try {
            await processBpjsBatch(items, item => {
                createRow(item);
                const row = rowTarget.lastElementChild;
                if (row) BpjsAllowances.updateRow(row, container, parseIdNumber, formatNumber);
            }, (done, total) => {
                if (button) button.textContent = `Menghitung ${done}/${total} karyawan...`;
            });
            if (rowCount === 0) addRow(null);
            container.replaceChildren(fragment);
        } catch (error) {
            rowCount = previousCount;
            console.error(error);
            alert('Perhitungan gagal. Data sebelumnya tetap tersedia. Silakan coba kembali.');
        } finally {
            rowTarget = container;
            isGenerating = false;
            container.inert = false;
            controls.forEach((control, index) => { control.disabled = previousDisabled[index]; });
            if (button) { button.innerHTML = originalLabel; button.removeAttribute('aria-busy'); }
            updateRowNumbers();
            calculateTotals();
        }
    }

    btnAdd.addEventListener('click', () => addRow(null));
    
    const btnGenerateAll = document.getElementById('btn-generate-all');
    btnGenerateAll?.addEventListener('click', async () => {
        const eligible = karyawans.filter(k => k.group_jkn || k.group_bp_jamsostek || k.cabang_bpjs);
        await generateRows(eligible, k => addRow({ karyawan_id: k.id, tipe_karyawan: k.tipe_karyawan, unique_id: k.unique_id }, true));
    });

    if (existingDetails && existingDetails.length > 0) {
        await generateRows(existingDetails, detail => addRow(detail));
    } else {
        const eligible = karyawans.filter(k => k.group_jkn || k.group_bp_jamsostek || k.cabang_bpjs);
        await generateRows(eligible, k => addRow({ karyawan_id: k.id, tipe_karyawan: k.tipe_karyawan, unique_id: k.unique_id }, true));
    }

});
</script>
@endsection
