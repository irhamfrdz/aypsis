@extends('layouts.app')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

    {{-- Page Header --}}
    <div class="sm:flex sm:items-center sm:justify-between mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Buat Pranota BPJS</h1>
            <p class="text-sm text-gray-500 mt-1">Isi detail pranota dan data BPJS karyawan</p>
        </div>
        <div>
            <a href="{{ route('pranota-bpjs.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 hover:border-gray-300 hover:bg-gray-50 text-gray-600 text-sm font-medium rounded-lg transition-colors">
                <i class="fas fa-arrow-left text-xs"></i>
                Kembali
            </a>
        </div>
    </div>

    <form action="{{ route('pranota-bpjs.store') }}" method="POST">
        @csrf

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
                            class="form-input w-full text-sm" value="{{ date('Y-m-d') }}" required />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5" for="periode_bulan">
                            Bulan Periode <span class="text-red-500">*</span>
                        </label>
                        <select id="periode_bulan" name="periode_bulan" class="form-select w-full text-sm" required>
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ date('n') == $i ? 'selected' : '' }}>
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
                            class="form-input w-full text-sm" value="{{ date('Y') }}" required />
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5" for="keterangan">
                        Keterangan Tambahan
                    </label>
                    <textarea id="keterangan" name="keterangan" class="form-textarea w-full text-sm" rows="2"
                        placeholder="Opsional..."></textarea>
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
                        <p class="text-xs text-gray-400">Pilih karyawan dan nominal BPJS</p>
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

            <div class="overflow-x-auto">
                <table class="table-auto w-full" id="tabel-detail">
                    <thead class="text-xs font-semibold uppercase text-gray-600 bg-gray-50 border-b-2 border-gray-200 tracking-wider">
                        <tr>
                            <th class="px-4 py-3 w-10 text-center">#</th>
                            <th class="px-4 py-3 text-left">Nama Karyawan</th>
                            <th class="px-4 py-3 text-left">Group</th>
                            <th class="px-4 py-3 text-center" style="min-width:180px">Tipe JKN & BP JAMSOSTEK</th>
                            <th class="px-4 py-3 text-right">JKN (Rp)</th>
                            <th class="px-4 py-3 text-right">BP Jamsostek (Rp)</th>
                            <th class="px-4 py-3 text-right">Subtotal (Rp)</th>
                            <th class="px-4 py-3 w-10 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="detail-container" class="divide-y divide-gray-100 bg-white">
                        <!-- Rows will be added here -->
                    </tbody>
                    <tfoot class="bg-gray-50 border-t-2 border-gray-200 shadow-sm">
                        <tr>
                            <td colspan="4" class="px-4 py-4 text-right text-sm font-bold text-gray-700 uppercase tracking-wide">Total Keseluruhan:</td>
                            <td class="px-4 py-4 text-right font-bold text-indigo-700 text-base" id="total_kes">Rp 0</td>
                            <td class="px-4 py-4 text-right font-bold text-indigo-700 text-base" id="total_ket">Rp 0</td>
                            <td class="px-4 py-4 text-right font-bold text-teal-600 text-lg" id="grand_total">Rp 0</td>
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
                Simpan Pranota
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('detail-container');
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
     * Contoh: "5.729.880,00" → 5729880
     *         "5.200.000"    → 5200000
     *         5200000        → 5200000 (sudah number, langsung return)
     */
    function parseIdNumber(val) {
        if (val === null || val === undefined || val === '') return 0;
        if (typeof val === 'number') return val;
        // Hapus titik pemisah ribuan, ganti koma desimal ke titik
        const cleaned = String(val).replace(/\./g, '').replace(',', '.');
        return parseFloat(cleaned) || 0;
    }

    function calculateTotals() {
        let sumKes = 0;
        let sumKet = 0;
        
        document.querySelectorAll('.input-kes').forEach(input => {
            sumKes += parseIdNumber(input.value);
        });
        
        document.querySelectorAll('.input-ket').forEach(input => {
            sumKet += parseIdNumber(input.value);
        });

        document.getElementById('total_kes').innerText = 'Rp ' + formatNumber(sumKes);
        document.getElementById('total_ket').innerText = 'Rp ' + formatNumber(sumKet);
        document.getElementById('grand_total').innerText = 'Rp ' + formatNumber(sumKes + sumKet);
    }

    function addRow(karyawanId = null, autoCalculate = false) {
        rowCount++;
        
        let karyawanInputHTML = '';
        if (karyawanId) {
            const k = karyawans.find(k => k.id == karyawanId);
            karyawanInputHTML = `
                <input type="hidden" name="details[${rowCount}][karyawan_id]" value="${karyawanId}" class="karyawan-hidden-input">
                <div class="px-2 py-2 text-sm text-gray-700 font-semibold truncate" style="max-width: 250px;">
                    ${k ? k.nama_lengkap : ''}
                </div>
            `;
        } else {
            let options = '<option value="">-- Pilih Karyawan --</option>';
            karyawans.forEach(k => {
                options += `<option value="${k.id}">${k.nama_lengkap}</option>`;
            });
            karyawanInputHTML = `
                <select name="details[${rowCount}][karyawan_id]" class="form-select w-full text-sm select2" required>
                    ${options}
                </select>
            `;
        }

        const tr = document.createElement('tr');
        tr.className = "border-b border-gray-100 detail-row hover:bg-indigo-50/30 transition-colors";
        tr.innerHTML = `
            <td class="px-4 py-3 text-center align-middle row-number text-gray-500 font-medium">${rowCount}</td>
            <td class="px-4 py-3 align-middle">
                ${karyawanInputHTML}
                <div class="info-jkn mt-1 hidden"></div>
            </td>
            <td class="px-4 py-3 align-middle text-center text-xs group-text font-semibold text-gray-600 whitespace-nowrap">-</td>
            <td class="px-4 py-3 align-middle text-center">
                <select class="form-select w-full text-sm text-center select-tipe-jkn border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm transition-colors" name="details[${rowCount}][tipe_jkn]">
                    <option value="tunjangan">Tunjangan</option>
                    <option value="hutang">Hutang</option>
                    <option value="tunjangan_hutang">Tunjangan & Hutang</option>
                </select>
            </td>
            <td class="px-4 py-3 align-middle">
                <input type="text" name="details[${rowCount}][bpjs_kesehatan]" class="w-full text-right text-sm input-kes font-semibold text-indigo-700 bg-transparent border-0 p-0 focus:ring-0 focus:outline-none cursor-default" value="0" autocomplete="off" readonly>
            </td>
            <td class="px-4 py-3 align-middle">
                <input type="text" name="details[${rowCount}][bpjs_ketenagakerjaan]" class="w-full text-right text-sm input-ket font-semibold text-indigo-700 bg-transparent border-0 p-0 focus:ring-0 focus:outline-none cursor-default" value="0" autocomplete="off" readonly>
            </td>
            <td class="px-4 py-3 text-right font-bold align-middle subtotal-text text-teal-600 block" style="padding-top: 0.75rem;">Rp 0</td>
            <td class="px-4 py-3 text-center align-middle">
                <button type="button" class="text-red-400 hover:text-red-600 hover:bg-red-50 p-2 rounded-md transition-all btn-remove mt-1" title="Hapus Baris">
                    <i class="fas fa-trash"></i>
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
        
        const updateSubtotal = () => {
            const kes = parseIdNumber(inputKes.value);
            const ket = parseIdNumber(inputKet.value);
            subtotalText.innerText = 'Rp ' + formatNumber(kes + ket);
            calculateTotals();
        };

        // Format saat kehilangan fokus atau nilai berubah (oleh sistem)
        inputKes.addEventListener('change', function() {
            this.value = formatNumber(parseIdNumber(this.value));
            updateSubtotal();
        });
        inputKet.addEventListener('change', function() {
            this.value = formatNumber(parseIdNumber(this.value));
            updateSubtotal();
        });

        inputKes.addEventListener('input', updateSubtotal);
        inputKet.addEventListener('input', updateSubtotal);

        document.querySelector('form').addEventListener('submit', function() {
            document.querySelectorAll('.input-kes, .input-ket').forEach(input => {
                input.value = parseIdNumber(input.value);
            });
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

            if (karyawan.group_jkn) {
                const rumus = rumusBpjs.find(r => r.jenis === 'jkn' && r.group_name === karyawan.group_jkn);
                if (rumus) {
                    const tunjPersen   = parseFloat(rumus.tunjangan_persen || 0);
                    const hutangPersen = parseFloat(rumus.hutang_persen    || 0);
                    groupText.innerHTML = `<span class="text-indigo-600">${karyawan.group_jkn}</span>`;
                    selectTipe.options[0].text = `Tunjangan (${tunjPersen}%)`;
                    selectTipe.options[1].text = `Hutang (${hutangPersen}%)`;
                    selectTipe.options[2].text = `Tunjangan (${tunjPersen}%) & Hutang (${hutangPersen}%)`;
                }
            }
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

            updateInfoBadgeJkn(kId);

            let nominalKes = 0;
            let nominalKet = 0;

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
                        persen = 0;
                        nominalKet += (hutangPersen / 100) * dpp;
                    } else if (tipeJkn === 'tunjangan_hutang') {
                        persen = tunjPersen;
                        nominalKet += (hutangPersen / 100) * dpp;
                    }
                    nominalKes = (persen / 100) * dpp;
                }
            } else if (tipeJkn === 'manual') {
                nominalKes = parseIdNumber(inputKes.value);
            }

            if (karyawan.group_bp_jamsostek && tipeJkn !== 'manual') {
                const rumus = rumusBpjs.find(r => r.jenis === 'jamsostek' && r.group_name === karyawan.group_bp_jamsostek);
                if (rumus) {
                    const tunjPersen   = parseFloat(rumus.tunjangan_persen || 0);
                    const hutangPersen = parseFloat(rumus.hutang_persen    || 0);
                    const dpp          = parseIdNumber(karyawan.dpp_jkn);
                    let persen = 0;
                    if (tipeJkn === 'tunjangan') {
                        persen = tunjPersen;
                    } else if (tipeJkn === 'hutang') {
                        persen = hutangPersen;
                    } else if (tipeJkn === 'tunjangan_hutang') {
                        persen = hutangPersen;
                    }
                    nominalKet += (persen / 100) * dpp;
                }
            } else if (tipeJkn === 'manual') {
                nominalKet = parseIdNumber(inputKet.value);
            }

            if (tipeJkn !== 'manual') {
                inputKes.value = formatNumber(Math.round(nominalKes));
                inputKes.readOnly = true;
                inputKet.readOnly = true;
                
                // Tambahkan styling agar tidak terlihat seperti input
                inputKes.classList.add('cursor-default');
                inputKet.classList.add('cursor-default');
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
    }

    function updateRowNumbers() {
        document.querySelectorAll('.row-number').forEach((td, index) => {
            td.innerText = index + 1;
        });
    }

    btnAdd.addEventListener('click', () => addRow(null));
    
    document.getElementById('btn-generate-all').addEventListener('click', () => {
        // Kosongkan tabel
        container.innerHTML = '';
        rowCount = 0;

        // Tambahkan baris untuk setiap karyawan yang punya Group JKN atau Group BP Jamsostek
        // autoCalculate diset ke true
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

    // Tampilan awal benar-benar kosong (0 baris), 
    // user bisa menekan "Hitung Semua Karyawan" atau "Tambah Karyawan"
});
</script>
@endsection
