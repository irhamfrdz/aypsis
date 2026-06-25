@extends('layouts.app')

@section('title', 'Tambah Gaji Supir Batam')
@section('page_title', 'Tambah Gaji Supir Batam')

@section('content')
<div class="mb-6">
    <a href="{{ route('gaji-supir-batam.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900 flex items-center transition-colors">
        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar
    </a>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden max-w-4xl">
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
        <h3 class="text-lg font-bold text-gray-900">Form Input Gaji Supir Batam</h3>
        <p class="text-xs text-gray-500 mt-1">Pilih supir, tentukan rentang tanggal, dan pilih surat jalan yang ingin dimasukkan dalam perhitungan gaji.</p>
    </div>

    @if (session('error'))
        <div class="m-6 rounded-md bg-red-50 p-4 border border-red-200">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-times-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">
                        {{ session('error') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('gaji-supir-batam.store') }}" class="p-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Supir -->
            <div>
                <label for="karyawan_id" class="block text-sm font-semibold text-gray-700 mb-2">Nama Supir <span class="text-red-500">*</span></label>
                <select name="karyawan_id" id="karyawan_id" class="block w-full py-2.5 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm @error('karyawan_id') border-red-500 @enderror" required>
                    <option value="">-- Pilih Supir --</option>
                    @foreach($supirList as $s)
                        <option value="{{ $s->id }}" {{ old('karyawan_id') == $s->id ? 'selected' : '' }}>
                            {{ $s->nama_lengkap }} (NIK: {{ $s->nik ?? '-' }} | {{ $s->plat ?? '-' }})
                        </option>
                    @endforeach
                </select>
                @error('karyawan_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tanggal Mulai -->
            <div>
                <label for="tanggal_mulai" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Mulai <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ old('tanggal_mulai') }}" class="block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm @error('tanggal_mulai') border-red-500 @enderror" required>
                @error('tanggal_mulai')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tanggal Selesai -->
            <div>
                <label for="tanggal_selesai" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Selesai <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_selesai" id="tanggal_selesai" value="{{ old('tanggal_selesai') }}" class="block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm @error('tanggal_selesai') border-red-500 @enderror" required>
                @error('tanggal_selesai')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="border-t border-gray-200 my-6"></div>

        <div class="bg-indigo-50/50 rounded-lg p-5 border border-indigo-100 mb-6">
            <h4 class="text-sm font-bold text-indigo-800 uppercase tracking-wider mb-4 flex items-center">
                <i class="fas fa-wallet mr-2"></i> Detail Gaji Supir
            </h4>
            
            <div class="max-w-md">
                <label for="gaji_pokok" class="block text-xs font-semibold text-gray-700 mb-1">Gaji Pokok (Total Rit SJ Terpilih) <span class="text-red-500">*</span></label>
                <div class="relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 text-sm">Rp</span>
                    </div>
                    <input type="number" name="gaji_pokok" id="gaji_pokok" value="{{ old('gaji_pokok', 0) }}" min="0" class="block w-full pl-9 pr-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 text-sm bg-gray-50 font-semibold" required readonly>
                </div>
            </div>
        </div>

        <!-- Live Salary Calculation Box -->
        <div class="bg-gray-800 text-white rounded-lg p-6 mb-6 flex flex-col md:flex-row justify-between items-center border border-gray-700 shadow-inner">
            <div class="mb-4 md:mb-0">
                <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wider">Estimasi Gaji Bersih</h4>
                <p class="text-xs text-gray-500">Hasil: Total Uang Rit dari Surat Jalan Terpilih</p>
            </div>
            <div class="text-right">
                <span class="text-2xl md:text-3xl font-extrabold text-green-400" id="total_salary_display">Rp 0</span>
            </div>
        </div>

        <!-- Waybill Breakdown Table -->
        <div class="bg-gray-50 rounded-lg p-5 border border-gray-200 mb-6" id="waybill_breakdown_section">
            <div class="flex justify-between items-center mb-4">
                <h4 class="text-sm font-bold text-gray-800 uppercase tracking-wider flex items-center">
                    <i class="fas fa-route mr-2 text-indigo-600"></i> Pilih Surat Jalan untuk Dimasukkan
                </h4>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-center text-gray-600 w-10">
                                <input type="checkbox" id="check_all_waybills" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" checked>
                            </th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Tipe</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">No. Surat Jalan</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Tanggal</th>
                            <th class="px-4 py-2 text-right font-semibold text-gray-600">Uang Rit (Rp)</th>
                        </tr>
                    </thead>
                    <tbody id="waybill_breakdown_rows" class="divide-y divide-gray-200 bg-white">
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-500 font-medium">
                                <i class="fas fa-info-circle mr-2 text-indigo-500"></i> Silakan pilih Supir, Tanggal Mulai, dan Tanggal Selesai untuk memuat daftar surat jalan.
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-gray-50 font-bold border-t border-gray-200">
                        <tr>
                            <td colspan="4" class="px-4 py-2 text-right text-gray-700">Total Uang Rit (Gaji Pokok):</td>
                            <td class="px-4 py-2 text-right text-indigo-600" id="waybill_total_display">Rp 0</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Status Pembayaran -->
            <div>
                <label for="status_pembayaran" class="block text-sm font-semibold text-gray-700 mb-2">Status Pembayaran <span class="text-red-500">*</span></label>
                <select name="status_pembayaran" id="status_pembayaran" class="block w-full py-2.5 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm" required>
                    <option value="PENDING" {{ old('status_pembayaran') == 'PENDING' ? 'selected' : '' }}>PENDING</option>
                    <option value="PAID" {{ old('status_pembayaran') == 'PAID' ? 'selected' : '' }}>PAID (SUDAH DIBAYAR)</option>
                    <option value="CANCELLED" {{ old('status_pembayaran') == 'CANCELLED' ? 'selected' : '' }}>CANCELLED (BATAL)</option>
                </select>
            </div>

            <!-- Tanggal Dibayar -->
            <div id="payment_date_wrapper" class="hidden">
                <label for="tanggal_dibayar" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Dibayar</label>
                <input type="date" name="tanggal_dibayar" id="tanggal_dibayar" value="{{ old('tanggal_dibayar', date('Y-m-d')) }}" class="block w-full py-2 px-3 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 text-sm">
            </div>
        </div>

        <!-- Keterangan -->
        <div class="mb-6">
            <label for="keterangan" class="block text-sm font-semibold text-gray-700 mb-2">Keterangan / Catatan</label>
            <textarea name="keterangan" id="keterangan" rows="3" placeholder="Tambahkan catatan jika ada..." class="block w-full py-2 px-3 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 text-sm">{{ old('keterangan') }}</textarea>
        </div>

        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
            <a href="{{ route('gaji-supir-batam.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                Batal
            </a>
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                Simpan Gaji
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const totalDisplay = document.getElementById('total_salary_display');
        const statusSelect = document.getElementById('status_pembayaran');
        const dateWrapper = document.getElementById('payment_date_wrapper');

        const supirSelect = document.getElementById('karyawan_id');
        const startInput = document.getElementById('tanggal_mulai');
        const endInput = document.getElementById('tanggal_selesai');
        const breakdownSection = document.getElementById('waybill_breakdown_section');
        const breakdownRows = document.getElementById('waybill_breakdown_rows');
        const breakdownTotal = document.getElementById('waybill_total_display');
        const checkAll = document.getElementById('check_all_waybills');

        // Salary calculator function
        function calculateSalary() {
            const gajiPokok = parseFloat(document.getElementById('gaji_pokok').value) || 0;
            totalDisplay.textContent = 'Rp ' + gajiPokok.toLocaleString('id-ID');
        }

        function updateGajiPokok() {
            let sum = 0;
            document.querySelectorAll('.waybill-checkbox:checked').forEach(cb => {
                const row = cb.closest('tr');
                sum += parseFloat(row.dataset.rit) || 0;
            });
            
            document.getElementById('gaji_pokok').value = sum;
            breakdownTotal.textContent = 'Rp ' + sum.toLocaleString('id-ID');
            calculateSalary();
        }

        function fetchWaybills() {
            const supirId = supirSelect.value;
            const start = startInput.value;
            const end = endInput.value;

            if (!supirId || !start || !end) {
                breakdownRows.innerHTML = `<tr><td colspan="5" class="px-4 py-6 text-center text-gray-500 font-medium"><i class="fas fa-info-circle mr-2 text-indigo-500"></i> Silakan pilih Supir, Tanggal Mulai, dan Tanggal Selesai untuk memuat daftar surat jalan.</td></tr>`;
                document.getElementById('gaji_pokok').value = 0;
                breakdownTotal.textContent = 'Rp 0';
                calculateSalary();
                return;
            }

            fetch(`{{ route('gaji-supir-batam.calculate') }}?karyawan_id=${supirId}&tanggal_mulai=${start}&tanggal_selesai=${end}`)
                .then(response => response.json())
                .then(data => {
                    breakdownRows.innerHTML = '';
                    if (data.waybills.length === 0) {
                        breakdownRows.innerHTML = `<tr><td colspan="5" class="px-4 py-3 text-center text-gray-500">Tidak ada surat jalan yang ditemukan pada periode ini.</td></tr>`;
                    } else {
                        data.waybills.forEach(wb => {
                            const row = document.createElement('tr');
                            row.className = 'hover:bg-gray-50 transition-colors waybill-row';
                            row.dataset.rit = wb.rit;
                            row.innerHTML = `
                                <td class="px-4 py-2.5 text-center">
                                    <input type="checkbox" name="selected_waybills[]" value="${wb.id}" class="waybill-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" checked>
                                </td>
                                <td class="px-4 py-2.5 text-gray-800 font-medium">${wb.type}</td>
                                <td class="px-4 py-2.5 text-gray-600 font-mono">${wb.no_surat_jalan}</td>
                                <td class="px-4 py-2.5 text-gray-600">${wb.tanggal}</td>
                                <td class="px-4 py-2.5 text-right font-semibold text-gray-800">Rp ${wb.rit.toLocaleString('id-ID')}</td>
                            `;
                            breakdownRows.appendChild(row);
                        });
                        
                        // Attach change listeners to dynamically added checkboxes
                        document.querySelectorAll('.waybill-checkbox').forEach(cb => {
                            cb.addEventListener('change', updateGajiPokok);
                        });
                    }

                    checkAll.checked = true;
                    updateGajiPokok();
                })
                .catch(error => {
                    console.error('Error fetching waybills:', error);
                });
        }

        // Attach waybill fetch listeners
        [supirSelect, startInput, endInput].forEach(el => {
            el.addEventListener('change', fetchWaybills);
        });

        // Toggle payment date field wrapper based on status
        function togglePaymentDate() {
            if (statusSelect.value === 'PAID') {
                dateWrapper.classList.remove('hidden');
            } else {
                dateWrapper.classList.add('hidden');
            }
        }

        statusSelect.addEventListener('change', togglePaymentDate);

        // Check/uncheck all waybills
        checkAll.addEventListener('change', function() {
            const checkedStatus = this.checked;
            document.querySelectorAll('.waybill-checkbox').forEach(cb => {
                cb.checked = checkedStatus;
            });
            updateGajiPokok();
        });

        // Trigger on load if values are present
        if (supirSelect.value && startInput.value && endInput.value) {
            fetchWaybills();
        } else {
            calculateSalary();
        }
        togglePaymentDate();
    });
</script>
@endpush
@endsection
