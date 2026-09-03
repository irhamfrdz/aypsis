@extends('layouts.app')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <div class="sm:flex sm:items-center sm:justify-between mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Buat Pranota BPJS</h1>
        </div>
        <div>
            <a href="{{ route('pranota-bpjs.index') }}" class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-600">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>

    <form action="{{ route('pranota-bpjs.store') }}" method="POST">
        @csrf
        <div class="bg-white shadow-lg rounded-sm border border-gray-200 mb-8">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium mb-1" for="tanggal_pranota">Tanggal Pranota <span class="text-red-500">*</span></label>
                        <input id="tanggal_pranota" name="tanggal_pranota" type="date" class="form-input w-full" value="{{ date('Y-m-d') }}" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" for="periode_bulan">Bulan <span class="text-red-500">*</span></label>
                        <select id="periode_bulan" name="periode_bulan" class="form-select w-full" required>
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ date('n') == $i ? 'selected' : '' }}>{{ str_pad($i, 2, '0', STR_PAD_LEFT) }} - {{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" for="periode_tahun">Tahun <span class="text-red-500">*</span></label>
                        <input id="periode_tahun" name="periode_tahun" type="number" class="form-input w-full" value="{{ date('Y') }}" required />
                    </div>
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium mb-1" for="keterangan">Keterangan Tambahan</label>
                    <textarea id="keterangan" name="keterangan" class="form-textarea w-full" rows="3" placeholder="Opsional..."></textarea>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-sm border border-gray-200 mb-8">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-bold text-gray-800">Detail Karyawan</h2>
                    <div class="flex space-x-2">
                        <button type="button" id="btn-generate-all" class="btn bg-green-500 hover:bg-green-600 text-white text-sm">
                            <i class="fas fa-magic mr-2"></i> Hitung Semua Karyawan
                        </button>
                        <button type="button" id="btn-add-karyawan" class="btn bg-indigo-500 hover:bg-indigo-600 text-white text-sm">
                            <i class="fas fa-plus mr-2"></i> Tambah Karyawan
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="table-auto w-full" id="tabel-detail">
                        <thead class="text-xs font-semibold uppercase text-gray-500 bg-gray-50 border-t border-b border-gray-200">
                            <tr>
                                <th class="px-2 py-3 w-10 text-center">#</th>
                                <th class="px-2 py-3 text-left">Nama Karyawan</th>
                                <th class="px-2 py-3 text-right">JKN (Rp)</th>
                                <th class="px-2 py-3 text-right">BP Jamsostek (Rp)</th>
                                <th class="px-2 py-3 text-right">Subtotal (Rp)</th>
                                <th class="px-2 py-3 w-10 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="detail-container">
                            <!-- Rows will be added here -->
                        </tbody>
                        <tfoot class="bg-gray-50 border-t border-gray-200 font-semibold">
                            <tr>
                                <td colspan="2" class="px-2 py-3 text-right">Total Keseluruhan:</td>
                                <td class="px-2 py-3 text-right text-indigo-600" id="total_kes">Rp 0</td>
                                <td class="px-2 py-3 text-right text-indigo-600" id="total_ket">Rp 0</td>
                                <td class="px-2 py-3 text-right text-indigo-600" id="grand_total">Rp 0</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('pranota-bpjs.index') }}" class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-600">Batal</a>
            <button type="submit" class="btn bg-teal-600 hover:bg-teal-700 text-white">Simpan Pranota</button>
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

    function addRow(karyawanId = null) {
        rowCount++;
        
        let options = '<option value="">-- Pilih Karyawan --</option>';
        karyawans.forEach(k => {
            const selected = (karyawanId && k.id == karyawanId) ? 'selected' : '';
            options += `<option value="${k.id}" ${selected}>${k.nama_lengkap}</option>`;
        });

        const tr = document.createElement('tr');
        tr.className = "border-b border-gray-100 detail-row";
        tr.innerHTML = `
            <td class="px-2 py-2 text-center align-middle row-number">${rowCount}</td>
            <td class="px-2 py-2">
                <select name="details[${rowCount}][karyawan_id]" class="form-select w-full text-sm select2" required>
                    ${options}
                </select>
            </td>
            <td class="px-2 py-2">
                <input type="number" name="details[${rowCount}][bpjs_kesehatan]" class="form-input w-full text-right text-sm input-kes" min="0" step="1" value="0">
            </td>
            <td class="px-2 py-2">
                <input type="number" name="details[${rowCount}][bpjs_ketenagakerjaan]" class="form-input w-full text-right text-sm input-ket" min="0" step="1" value="0">
            </td>
            <td class="px-2 py-2 text-right font-medium align-middle subtotal-text">Rp 0</td>
            <td class="px-2 py-2 text-center">
                <button type="button" class="text-red-500 hover:text-red-700 btn-remove">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

        container.appendChild(tr);

        // Add event listeners for inputs
        const inputKes = tr.querySelector('.input-kes');
        const inputKet = tr.querySelector('.input-ket');
        const subtotalText = tr.querySelector('.subtotal-text');
        
        const updateSubtotal = () => {
            const kes = parseFloat(inputKes.value || 0);
            const ket = parseFloat(inputKet.value || 0);
            subtotalText.innerText = 'Rp ' + formatNumber(kes + ket);
            calculateTotals();
        };

        inputKes.addEventListener('input', updateSubtotal);
        inputKet.addEventListener('input', updateSubtotal);

        // Remove button
        tr.querySelector('.btn-remove').addEventListener('click', function() {
            tr.remove();
            updateRowNumbers();
            calculateTotals();
        });
        
        // Try to initialize select2 if available
        let $select = null;
        if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
            $select = jQuery(tr.querySelector('.select2')).select2({
                placeholder: "-- Pilih Karyawan --",
                allowClear: true
            });
        }

        // Calculate function separated for reuse
        function calculateBpjsForKaryawan(karyawanId) {
            if (!karyawanId) {
                inputKes.value = 0;
                inputKet.value = 0;
                updateSubtotal();
                return;
            }

            const karyawan = karyawans.find(k => k.id == karyawanId);
            if (!karyawan) return;

            let nominalKes = 0;
            let nominalKet = 0;

            // Hitung JKN: cari rumus berdasarkan group_jkn karyawan
            if (karyawan.group_jkn) {
                const rumus = rumusBpjs.find(r => r.jenis === 'jkn' && r.group_name === karyawan.group_jkn);
                if (rumus) {
                    const tunjangan = parseFloat(rumus.tunjangan_persen || 0);
                    const hutang    = parseFloat(rumus.hutang_persen    || 0);
                    const biaya     = parseFloat(rumus.biaya_persen     || 0);
                    const totalPersen = tunjangan + hutang + biaya;

                    if (totalPersen > 0) {
                        const dpp = parseFloat(karyawan.dpp_jkn || 0);
                        nominalKes = (totalPersen / 100) * dpp;
                    }
                    // jika keterangan_custom dan totalPersen == 0, biarkan 0 (input manual)
                }
            }

            // Hitung BP Jamsostek: cari rumus berdasarkan group_bp_jamsostek karyawan
            if (karyawan.group_bp_jamsostek) {
                const rumus = rumusBpjs.find(r => r.jenis === 'jamsostek' && r.group_name === karyawan.group_bp_jamsostek);
                if (rumus) {
                    const tunjangan = parseFloat(rumus.tunjangan_persen || 0);
                    const hutang    = parseFloat(rumus.hutang_persen    || 0);
                    const biaya     = parseFloat(rumus.biaya_persen     || 0);
                    const totalPersen = tunjangan + hutang + biaya;

                    if (totalPersen > 0) {
                        const dpp = parseFloat(karyawan.dpp_bp_jamsostek || 0);
                        nominalKet = (totalPersen / 100) * dpp;
                    }
                }
            }

            inputKes.value = Math.round(nominalKes);
            inputKet.value = Math.round(nominalKet);
            updateSubtotal();
        }

        const handleKaryawanChange = function(val) {
            // val bisa dari event biasa (e.target.value) atau dari Select2 (val langsung)
            const karyawanId = (typeof val === 'object' && val !== null && val.target) ? val.target.value : val;
            calculateBpjsForKaryawan(karyawanId);
        };

        if ($select) {
            // Select2 mengirimkan event jQuery, ambil value dari $select.val()
            $select.on('change', function() {
                calculateBpjsForKaryawan($select.val());
            });
        } else {
            tr.querySelector('.select2').addEventListener('change', function(e) {
                calculateBpjsForKaryawan(e.target.value);
            });
        }

        // Jika saat pembuatan baris sudah ada karyawanId (dari generate-all), hitung langsung
        if (karyawanId) {
            setTimeout(() => calculateBpjsForKaryawan(karyawanId), 100);
        }
    }

    function updateRowNumbers() {
        document.querySelectorAll('.row-number').forEach((td, index) => {
            td.innerText = index + 1;
        });
    }

    btnAdd.addEventListener('click', () => addRow(null));
    
    document.getElementById('btn-generate-all').addEventListener('click', () => {
        // Hapus baris kosong pertama jika ada
        const existingRows = document.querySelectorAll('.detail-row');
        if (existingRows.length === 1) {
            const firstSelect = existingRows[0].querySelector('select');
            if (!firstSelect.value) {
                existingRows[0].remove();
                rowCount = 0;
            }
        }

        // Tambahkan baris untuk setiap karyawan yang punya Group JKN atau Group BP Jamsostek
        karyawans.forEach(k => {
            if (k.group_jkn || k.group_bp_jamsostek) {
                addRow(k.id); // addRow sekarang otomatis menghitung melalui setTimeout
            }
        });
        
        updateRowNumbers();
    });

    // Tambah 1 baris kosong sebagai default
    addRow(null);
});
</script>
@endsection
