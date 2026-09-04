@extends('layouts.app')

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

    <form action="{{ route('pranota-bpjs.update', $pranota_bpj->id) }}" method="POST">
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
                <button type="button" id="btn-add-karyawan"
                    class="inline-flex items-center gap-2 px-3 py-2 bg-indigo-500 hover:bg-indigo-600 text-white text-xs font-semibold rounded-lg transition-colors shadow-sm">
                    <i class="fas fa-plus text-xs"></i>
                    Tambah Karyawan
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="table-auto w-full" id="tabel-detail">
                    <thead class="text-xs font-semibold uppercase text-gray-500 bg-gray-50 border-b border-gray-200 tracking-wider">
                        <tr>
                            <th class="px-3 py-3 w-10 text-center">#</th>
                            <th class="px-3 py-3 text-left">Nama Karyawan</th>
                            <th class="px-3 py-3 text-center" style="min-width:160px">Tipe JKN</th>
                            <th class="px-3 py-3 text-right">JKN (Rp)</th>
                            <th class="px-3 py-3 text-right">BP Jamsostek (Rp)</th>
                            <th class="px-3 py-3 text-right">Subtotal (Rp)</th>
                            <th class="px-3 py-3 w-10 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="detail-container" class="divide-y divide-gray-100">
                        <!-- Existing details will be loaded here via JS -->
                    </tbody>
                    <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                        <tr>
                            <td colspan="3" class="px-3 py-3 text-right text-sm font-bold text-gray-600">Total Keseluruhan:</td>
                            <td class="px-3 py-3 text-right font-bold text-indigo-600" id="total_kes">Rp 0</td>
                            <td class="px-3 py-3 text-right font-bold text-indigo-600" id="total_ket">Rp 0</td>
                            <td class="px-3 py-3 text-right font-bold text-teal-600 text-base" id="grand_total">Rp 0</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
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


<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('detail-container');
    const btnAdd = document.getElementById('btn-add-karyawan');
    
    const karyawans = @json($karyawans);
    const existingDetails = @json($pranota_bpj->details);
    const rumusBpjs = @json($rumusBpjs);
    let rowCount = 0;

    function formatNumber(num) {
        return new Intl.NumberFormat('id-ID').format(num || 0);
    }

    /**
     * Parse angka format Indonesia ke float
     * Contoh: "5.729.880,00" → 5729880
     *         5200000        → 5200000 (sudah number, langsung return)
     */
    function parseIdNumber(val) {
        if (val === null || val === undefined || val === '') return 0;
        if (typeof val === 'number') return val;
        const cleaned = String(val).replace(/\./g, '').replace(',', '.');
        return parseFloat(cleaned) || 0;
    }

    function calculateTotals() {
        let sumKes = 0;
        let sumKet = 0;
        
        document.querySelectorAll('.input-kes').forEach(input => {
            sumKes += parseFloat(input.value || 0);
        });
        
        document.querySelectorAll('.input-ket').forEach(input => {
            sumKet += parseFloat(input.value || 0);
        });

        document.getElementById('total_kes').innerText = 'Rp ' + formatNumber(sumKes);
        document.getElementById('total_ket').innerText = 'Rp ' + formatNumber(sumKet);
        document.getElementById('grand_total').innerText = 'Rp ' + formatNumber(sumKes + sumKet);
    }

    /**
     * addRow(detail) — detail: objek existing, atau null untuk baris baru
     * Existing row: tipe default 'manual' agar nilai lama tidak ditimpa
     * New row     : tipe default 'tunjangan' agar langsung hitung otomatis
     */
    function addRow(detail = null) {
        rowCount++;
        
        let selectedKaryawanId = detail ? detail.karyawan_id : '';
        let valKes   = detail ? detail.bpjs_kesehatan         : 0;
        let valKet   = detail ? detail.bpjs_ketenagakerjaan   : 0;
        let subVal   = detail ? detail.total                  : 0;
        let isExistingRow = detail !== null;

        let options = '<option value="">-- Pilih Karyawan --</option>';
        karyawans.forEach(k => {
            options += `<option value="${k.id}" ${k.id == selectedKaryawanId ? 'selected' : ''}>${k.nama_lengkap}</option>`;
        });

        const tr = document.createElement('tr');
        tr.className = "border-b border-gray-100 detail-row";
        tr.innerHTML = `
            <td class="px-2 py-2 text-center align-top row-number">${rowCount}</td>
            <td class="px-2 py-2">
                <select name="details[${rowCount}][karyawan_id]" class="form-select w-full text-sm select2" required>
                    ${options}
                </select>
                <div class="info-jkn mt-1"></div>
            </td>
            <td class="px-2 py-2 align-top">
                <select class="form-select w-full text-sm select-tipe-jkn" name="details[${rowCount}][tipe_jkn]">
                    <option value="tunjangan">Tunjangan</option>
                    <option value="hutang">Hutang</option>
                    <option value="manual" ${isExistingRow ? 'selected' : ''}>Manual</option>
                </select>
            </td>
            <td class="px-2 py-2 align-top">
                <input type="number" name="details[${rowCount}][bpjs_kesehatan]" class="form-input w-full text-right text-sm input-kes" min="0" step="1" value="${valKes}">
            </td>
            <td class="px-2 py-2 align-top">
                <input type="number" name="details[${rowCount}][bpjs_ketenagakerjaan]" class="form-input w-full text-right text-sm input-ket" min="0" step="1" value="${valKet}">
            </td>
            <td class="px-2 py-2 text-right font-medium align-top subtotal-text">Rp ${formatNumber(subVal)}</td>
            <td class="px-2 py-2 text-center align-top">
                <button type="button" class="text-red-500 hover:text-red-700 btn-remove">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

        container.appendChild(tr);

        const inputKes     = tr.querySelector('.input-kes');
        const inputKet     = tr.querySelector('.input-ket');
        const subtotalText = tr.querySelector('.subtotal-text');
        const infoJkn      = tr.querySelector('.info-jkn');
        const selectTipe   = tr.querySelector('.select-tipe-jkn');
        
        const updateSubtotal = () => {
            const kes = parseFloat(inputKes.value || 0);
            const ket = parseFloat(inputKet.value || 0);
            subtotalText.innerText = 'Rp ' + formatNumber(kes + ket);
            calculateTotals();
        };

        inputKes.addEventListener('input', updateSubtotal);
        inputKet.addEventListener('input', updateSubtotal);

        tr.querySelector('.btn-remove').addEventListener('click', function() {
            tr.remove();
            updateRowNumbers();
            calculateTotals();
        });
        
        let $select = null;
        if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
            $select = jQuery(tr.querySelector('.select2')).select2({
                placeholder: "-- Pilih Karyawan --",
                allowClear: true
            });
        }

        /**
         * Tampilkan info badge Group JKN + DPP + persen
         * dan perbarui label opsi Tipe JKN
         */
        function updateInfoBadgeJkn(kId) {
            const karyawan = karyawans.find(k => k.id == kId);
            if (!karyawan) { infoJkn.innerHTML = ''; return; }

            if (karyawan.group_jkn) {
                const rumus = rumusBpjs.find(r => r.jenis === 'jkn' && r.group_name === karyawan.group_jkn);
                if (rumus) {
                    const tunjPersen   = parseFloat(rumus.tunjangan_persen || 0);
                    const hutangPersen = parseFloat(rumus.hutang_persen    || 0);
                    const dpp          = parseIdNumber(karyawan.dpp_jkn);

                    infoJkn.innerHTML = `
                        <span class="inline-flex flex-wrap gap-1 mt-1">
                            <span class="bg-indigo-100 text-indigo-700 rounded px-1 py-0.5 text-xs">Group: <b>${karyawan.group_jkn}</b></span>
                            <span class="bg-gray-100 text-gray-600 rounded px-1 py-0.5 text-xs">DPP: <b>Rp ${formatNumber(dpp)}</b></span>
                            <span class="bg-green-100 text-green-700 rounded px-1 py-0.5 text-xs">Tunj: <b>${tunjPersen}%</b></span>
                            <span class="bg-yellow-100 text-yellow-700 rounded px-1 py-0.5 text-xs">Hutang: <b>${hutangPersen}%</b></span>
                        </span>
                    `;

                    selectTipe.options[0].text = `Tunjangan (${tunjPersen}%)`;
                    selectTipe.options[1].text = `Hutang (${hutangPersen}%)`;
                } else {
                    infoJkn.innerHTML = `<span class="text-xs text-orange-500">&#9888; Rumus JKN untuk Group "${karyawan.group_jkn}" tidak ditemukan</span>`;
                    selectTipe.options[0].text = 'Tunjangan';
                    selectTipe.options[1].text = 'Hutang';
                }
            } else {
                infoJkn.innerHTML = `<span class="text-xs text-gray-400">Tidak ada Group JKN</span>`;
                selectTipe.options[0].text = 'Tunjangan';
                selectTipe.options[1].text = 'Hutang';
            }
        }

        /**
         * Hitung nominal BPJS
         * Alur JKN:
         * 1. Cari Group JKN karyawan
         * 2. Ambil DPP JKN karyawan
         * 3. Sesuai tipe (tunjangan/hutang): ambil persen dari rumus
         * 4. nominalKes = (persen / 100) * DPP
         */
        function calculateBpjsForKaryawan(kId, tipeJkn) {
            if (!kId) {
                inputKes.value = 0;
                inputKet.value = 0;
                infoJkn.innerHTML = '';
                selectTipe.options[0].text = 'Tunjangan';
                selectTipe.options[1].text = 'Hutang';
                updateSubtotal();
                return;
            }

            updateInfoBadgeJkn(kId);

            const karyawan = karyawans.find(k => k.id == kId);
            if (!karyawan) return;

            let nominalKet = 0;

            // --- Hitung JKN berdasarkan tipe ---
            if (karyawan.group_jkn && tipeJkn !== 'manual') {
                const rumus = rumusBpjs.find(r => r.jenis === 'jkn' && r.group_name === karyawan.group_jkn);
                if (rumus) {
                    const tunjPersen   = parseFloat(rumus.tunjangan_persen || 0);
                    const hutangPersen = parseFloat(rumus.hutang_persen    || 0);
                    const dpp          = parseIdNumber(karyawan.dpp_jkn);

                    let persen = 0;
                    if (tipeJkn === 'tunjangan') {
                        persen = tunjPersen;
                    } else if (tipeJkn === 'hutang') {
                        persen = hutangPersen;
                    }

                    inputKes.value = Math.round((persen / 100) * dpp);
                }
            }
            // 'manual': jangan ubah inputKes, biarkan user isi sendiri

            // --- Hitung BP Jamsostek (semua komponen) ---
            if (karyawan.group_bp_jamsostek) {
                const rumus = rumusBpjs.find(r => r.jenis === 'jamsostek' && r.group_name === karyawan.group_bp_jamsostek);
                if (rumus) {
                    const tunjangan   = parseFloat(rumus.tunjangan_persen || 0);
                    const hutang      = parseFloat(rumus.hutang_persen    || 0);
                    const biaya       = parseFloat(rumus.biaya_persen     || 0);
                    const totalPersen = tunjangan + hutang + biaya;
                    if (totalPersen > 0) {
                        const dpp  = parseIdNumber(karyawan.dpp_bp_jamsostek);
                        nominalKet = (totalPersen / 100) * dpp;
                    }
                }
            }

            inputKet.value = Math.round(nominalKet);
            updateSubtotal();
        }

        // Handler: karyawan berubah
        const handleKaryawanChange = function(kId) {
            const tipe = selectTipe.value;
            calculateBpjsForKaryawan(kId, tipe);
        };

        if ($select) {
            $select.on('change', function() {
                handleKaryawanChange($select.val());
            });
        } else {
            tr.querySelector('.select2').addEventListener('change', function(e) {
                handleKaryawanChange(e.target.value);
            });
        }

        // Handler: tipe JKN berubah → hitung ulang
        selectTipe.addEventListener('change', function() {
            const kId = $select ? $select.val() : tr.querySelector('.select2').value;
            calculateBpjsForKaryawan(kId, this.value);
        });

        // Existing rows: tampilkan info badge saja, jangan hitung ulang
        if (isExistingRow && selectedKaryawanId) {
            setTimeout(() => updateInfoBadgeJkn(selectedKaryawanId), 100);
        }
    }

    function updateRowNumbers() {
        document.querySelectorAll('.row-number').forEach((td, index) => {
            td.innerText = index + 1;
        });
    }

    btnAdd.addEventListener('click', () => addRow(null));
    
    // Load existing details
    if (existingDetails && existingDetails.length > 0) {
        existingDetails.forEach(detail => addRow(detail));
    } else {
        addRow();
    }
    
    calculateTotals();
});
</script>
@endsection
