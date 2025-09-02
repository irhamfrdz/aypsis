@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-6 py-8">
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-8 border-b pb-4">
            Form Pembayaran Pranota Kontainer
        </h2>

    @if(session('success'))
        <div id="flash-message" class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200 text-green-800">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div id="flash-message" class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 text-red-800">
            <strong>Peringatan:</strong> {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div id="flash-message" class="mb-6 p-4 rounded-lg bg-yellow-50 border border-yellow-200 text-yellow-800">
            <strong>Terjadi kesalahan:</strong>
            <ul class="mt-2 list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('pembayaran-pranota-kontainer.store') }}" id="pembayaranForm" class="space-y-8">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div class="space-y-6">
                    <div>
                        <label for="nomor_pembayaran" class="block text-sm font-semibold text-gray-700 mb-1">Nomor Pembayaran</label>
                        <input type="text" name="nomor_pembayaran" id="nomor_pembayaran"
                            value="{{ 'BTK1' . now()->format('y') . now()->format('m') . str_pad(1, 6, '0', STR_PAD_LEFT) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5" readonly>
                    </div>
                    <div>
                        <label for="tanggal_kas" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Kas</label>
                        <input type="date" name="tanggal_kas" id="tanggal_kas"
                            value="{{ now()->toDateString() }}"
                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5" required>
                        {{-- Hidden field required by controller validation: keep in sync with tanggal_kas --}}
                        <input type="hidden" name="tanggal_pembayaran" id="tanggal_pembayaran" value="{{ now()->toDateString() }}">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label for="bank" class="block text-sm font-semibold text-gray-700 mb-1">Pilih Bank</label>
                        <select name="bank" id="bank"
                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5" required>
                            <option value="">-- Pilih Bank --</option>
                            <option value="BCA">BCA</option>
                            <option value="Mandiri">Mandiri</option>
                            <option value="BRI">BRI</option>
                            <option value="BNI">BNI</option>
                            <option value="CIMB">CIMB</option>
                            <option value="Danamon">Danamon</option>
                            <option value="Permata">Permata</option>
                        </select>
                    </div>
                    <div>
                        <label for="jenis_transaksi" class="block text-sm font-semibold text-gray-700 mb-1">Jenis Transaksi</label>
                        <select name="jenis_transaksi" id="jenis_transaksi"
                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5" required>
                            <option value="">-- Pilih Jenis --</option>
                            <option value="Debit">Debit</option>
                            <option value="Kredit">Kredit</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Alasan Penyesuaian --}}
            <div>
                <label for="alasan_penyesuaian" class="block text-sm font-semibold text-gray-700 mb-1">Alasan Penyesuaian</label>
                <textarea name="alasan_penyesuaian" id="alasan_penyesuaian" rows="2"
                    class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5"></textarea>
            </div>

            {{-- Keterangan --}}
            <div>
                <label for="keterangan" class="block text-sm font-semibold text-gray-700 mb-1">Keterangan</label>
                <textarea name="keterangan" id="keterangan" rows="2"
                    class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5"></textarea>
            </div>

            {{-- Daftar Pranota Kontainer --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Pilih Pranota Kontainer</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" id="select-all" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Pranota</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pranota</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Tagihan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Pembayaran</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($pranotaList as $pranota)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" name="pranota_ids[]" value="{{ $pranota->id }}" class="pranota-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-indigo-600 font-medium">{{ $pranota->no_invoice }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if ($pranota->tanggal_pranota)
                                            {{ \Carbon\Carbon::parse($pranota->tanggal_pranota)->format('d/m/Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $pranota->jumlah_tagihan ?? 0 }} item
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        Rp {{ number_format($pranota->total_amount, 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $pranota->getSimplePaymentStatusColor() }}">
                                            {{ $pranota->getSimplePaymentStatus() }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada data pranota kontainer</h3>
                                            <p class="text-gray-500">Tidak ada pranota kontainer yang tersedia untuk dibayar.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <p class="mt-2 text-sm text-gray-500">* Anda dapat memilih satu atau lebih pranota kontainer untuk dibayar.</p>
            </div>

            {{-- Penyesuaian Total & Nominal Pembayaran --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                <div>
                    <label for="total_pembayaran" class="block text-sm font-semibold text-gray-700 mb-1">Total Pembayaran</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-base">Rp</span>
                        <input type="text" name="total_pembayaran" id="total_pembayaran"
                            value="0"
                            class="mt-1 block w-full pl-8 rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5 text-right" required>
                        <input type="hidden" name="total_pembayaran_raw" id="total_pembayaran_raw" value="0">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Format: 35.449,53</p>
                </div>
                <div>
                    <label for="total_tagihan_penyesuaian" class="block text-sm font-semibold text-gray-700 mb-1">Penyesuaian</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-base">Rp</span>
                        <input type="text" name="total_tagihan_penyesuaian" id="total_tagihan_penyesuaian"
                            class="mt-1 block w-full pl-8 pr-20 rounded-md border-gray-300 bg-white shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5 text-right"
                            value="0" placeholder="Ketik nominal penyesuaian...">
                        <input type="hidden" name="total_tagihan_penyesuaian_raw" id="total_tagihan_penyesuaian_raw" value="0">
                        <!-- Plus/Minus buttons -->
                        <div class="absolute right-2 top-1/2 transform -translate-y-1/2 flex space-x-1">
                            <button type="button" id="btn-minus" class="w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded text-xs font-bold flex items-center justify-center" title="Pengurangan">-</button>
                            <button type="button" id="btn-plus" class="w-6 h-6 bg-green-500 hover:bg-green-600 text-white rounded text-xs font-bold flex items-center justify-center" title="Penambahan">+</button>
                        </div>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Ketik langsung: 1000, -500, 1000.50. Format otomatis saat selesai mengetik</p>
                </div>
                <div>
                    <label for="total_tagihan_setelah_penyesuaian" class="block text-sm font-semibold text-gray-700 mb-1">Total Pembayaran setelah Penyesuaian</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-base">Rp</span>
                        <input type="text" name="total_tagihan_setelah_penyesuaian" id="total_tagihan_setelah_penyesuaian"
                            class="mt-1 block w-full pl-8 rounded-md border-gray-300 bg-gray-50 shadow-sm font-semibold text-gray-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5 text-right" readonly value="0">
                        <input type="hidden" name="total_tagihan_setelah_penyesuaian_raw" id="total_tagihan_setelah_penyesuaian_raw" value="0">
                    </div>
                    <p class="mt-1 text-xs text-green-600 font-medium">Total yang akan dibayarkan</p>
                </div>
            </div>

            {{-- Tombol Submit --}}
            <div class="flex justify-end">
                <button type="submit"
                    class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-md transition">
                    Simpan Pembayaran
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Script --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAllCheckbox = document.getElementById('select-all');
        const pranotaCheckboxes = document.querySelectorAll('.pranota-checkbox');

        // Format currency functions
        function formatCurrency(value) {
            if (!value || isNaN(value)) return '0';
            // Convert to Indonesian format: 35.449,53
            return parseFloat(value).toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function parseCurrency(value) {
            if (!value) return 0;
            // Handle negative values
            const isNegative = value.toString().includes('-');
            // Remove all non-numeric characters except comma, dot and minus
            let cleanValue = value.toString().replace(/[^\d,.-]/g, '');
            // Handle Indonesian format (dot as thousand separator, comma as decimal)
            if (cleanValue.includes('.') && cleanValue.includes(',')) {
                // Format like 1.000,50 - remove dots (thousand separator), keep comma (decimal)
                cleanValue = cleanValue.replace(/\./g, '').replace(',', '.');
            } else if (cleanValue.includes(',') && !cleanValue.includes('.')) {
                // Format like 1000,50 - replace comma with dot
                cleanValue = cleanValue.replace(',', '.');
            }
            // Remove any remaining non-numeric except dot and minus
            cleanValue = cleanValue.replace(/[^\d.-]/g, '');
            let result = parseFloat(cleanValue) || 0;
            return isNegative ? -result : result;
        }

        // Setup currency formatting for input fields
        function setupCurrencyInput(inputId, hiddenId) {
            const input = document.getElementById(inputId);
            const hidden = document.getElementById(hiddenId);

            if (!input || !hidden) return;

            // Only format on blur (when user finishes typing)
            input.addEventListener('blur', function() {
                const rawValue = parseCurrency(this.value);
                hidden.value = rawValue;

                if (inputId === 'total_tagihan_penyesuaian') {
                    if (rawValue < 0) {
                        this.value = '-' + formatCurrency(Math.abs(rawValue));
                    } else if (rawValue > 0) {
                        this.value = formatCurrency(rawValue);
                    } else {
                        this.value = '0';
                    }
                } else {
                    this.value = formatCurrency(rawValue);
                }
                updateTotalSetelahPenyesuaian();
            });

            // Update hidden value on input without formatting
            input.addEventListener('input', function() {
                const rawValue = parseCurrency(this.value);
                hidden.value = rawValue;
                updateTotalSetelahPenyesuaian();
            });

            // Allow natural typing for penyesuaian field
            if (inputId === 'total_tagihan_penyesuaian') {
                input.addEventListener('keydown', function(e) {
                    // Allow all normal typing keys
                    const allowedKeys = [
                        'Backspace', 'Delete', 'Tab', 'Escape', 'Enter',
                        'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown',
                        'Home', 'End', 'PageUp', 'PageDown'
                    ];

                    if (allowedKeys.includes(e.key)) {
                        return;
                    }

                    // Allow numbers
                    if (e.key >= '0' && e.key <= '9') {
                        return;
                    }

                    // Allow minus anywhere (will be handled by parsing)
                    if (e.key === '-') {
                        return;
                    }

                    // Allow comma and dot
                    if (e.key === ',' || e.key === '.') {
                        return;
                    }

                    // Allow copy/paste shortcuts
                    if ((e.ctrlKey || e.metaKey) && ['a', 'c', 'v', 'x', 'z', 'y'].includes(e.key.toLowerCase())) {
                        return;
                    }

                    // Block all other keys
                    e.preventDefault();
                });
            }
        }        // Initialize currency inputs
        setupCurrencyInput('total_pembayaran', 'total_pembayaran_raw');
        setupCurrencyInput('total_tagihan_penyesuaian', 'total_tagihan_penyesuaian_raw');

        // Handle plus/minus buttons for penyesuaian
        const btnPlus = document.getElementById('btn-plus');
        const btnMinus = document.getElementById('btn-minus');
        const penyesuaianInput = document.getElementById('total_tagihan_penyesuaian');
        const penyesuaianRaw = document.getElementById('total_tagihan_penyesuaian_raw');

        btnPlus.addEventListener('click', function() {
            const currentValue = Math.abs(parseCurrency(penyesuaianInput.value) || 0);
            if (currentValue === 0) {
                penyesuaianInput.value = '';
                penyesuaianInput.focus();
                return;
            }
            penyesuaianRaw.value = currentValue;
            penyesuaianInput.value = formatCurrency(currentValue);
            updateTotalSetelahPenyesuaian();
        });

        btnMinus.addEventListener('click', function() {
            const currentValue = Math.abs(parseCurrency(penyesuaianInput.value) || 0);
            if (currentValue === 0) {
                penyesuaianInput.value = '-';
                penyesuaianInput.focus();
                return;
            }
            penyesuaianRaw.value = -currentValue;
            penyesuaianInput.value = '-' + formatCurrency(currentValue);
            updateTotalSetelahPenyesuaian();
        });

        selectAllCheckbox.addEventListener('change', function () {
            pranotaCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateTotalPembayaran();
        });

        // Validasi minimal satu pranota
        const pembayaranForm = document.getElementById('pembayaranForm');
        pembayaranForm.addEventListener('submit', function(e) {
            const checkedCheckboxes = document.querySelectorAll('.pranota-checkbox:checked');
            if (checkedCheckboxes.length === 0) {
                e.preventDefault();
                alert('Silakan pilih minimal satu pranota kontainer.');
                return false;
            }

            // Set raw values for submission
            document.getElementById('total_pembayaran_raw').name = 'total_pembayaran';
            document.getElementById('total_tagihan_penyesuaian_raw').name = 'total_tagihan_penyesuaian';
            document.getElementById('total_tagihan_setelah_penyesuaian_raw').name = 'total_tagihan_setelah_penyesuaian';

            // Remove formatted inputs from form submission
            document.getElementById('total_pembayaran').removeAttribute('name');
            document.getElementById('total_tagihan_penyesuaian').removeAttribute('name');
            document.getElementById('total_tagihan_setelah_penyesuaian').removeAttribute('name');
        });

        // Perhitungan otomatis total pembayaran berdasarkan pranota yang dipilih
        const totalPembayaranInput = document.getElementById('total_pembayaran');
        const totalPembayaranRaw = document.getElementById('total_pembayaran_raw');
        const totalPenyesuaianRaw = document.getElementById('total_tagihan_penyesuaian_raw');
        const totalSetelahInput = document.getElementById('total_tagihan_setelah_penyesuaian');
        const totalSetelahRaw = document.getElementById('total_tagihan_setelah_penyesuaian_raw');

        // Simpan nilai total_amount di data attribute
        const pranotaBiayaMap = {};
        @if(isset($pranotaList))
            @foreach ($pranotaList as $pranota)
                pranotaBiayaMap['{{ $pranota->id }}'] = {{ $pranota->total_amount }};
            @endforeach
        @endif

        function updateTotalPembayaran() {
            let total = 0;
            pranotaCheckboxes.forEach(function(checkbox) {
                if (checkbox.checked) {
                    const id = checkbox.value;
                    total += pranotaBiayaMap[id] || 0;
                }
            });
            totalPembayaranRaw.value = total;
            totalPembayaranInput.value = formatCurrency(total);
            updateTotalSetelahPenyesuaian();
        }

        function updateTotalSetelahPenyesuaian() {
            const totalPembayaran = parseFloat(totalPembayaranRaw.value) || 0;
            const totalPenyesuaian = parseFloat(totalPenyesuaianRaw.value) || 0;
            const totalSetelah = totalPembayaran + totalPenyesuaian;

            totalSetelahRaw.value = totalSetelah;
            totalSetelahInput.value = formatCurrency(totalSetelah);
        }

        pranotaCheckboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', updateTotalPembayaran);
        });

        // Initialize display values
        updateTotalPembayaran();

        // Set initial value for penyesuaian field to empty
        penyesuaianInput.value = '0';
        penyesuaianInput.placeholder = 'Ketik nominal penyesuaian...';
    });

    // Keep tanggal_pembayaran hidden field synced with tanggal_kas
    document.addEventListener('DOMContentLoaded', function () {
        const tanggalKas = document.getElementById('tanggal_kas');
        const tanggalPembayaran = document.getElementById('tanggal_pembayaran');
        if (tanggalKas && tanggalPembayaran) {
            tanggalKas.addEventListener('change', function () {
                tanggalPembayaran.value = this.value;
            });
        }
    });
</script>
<script>
    // Scroll to flash message if present and focus it for accessibility
    document.addEventListener('DOMContentLoaded', function () {
        const flash = document.getElementById('flash-message');
        if (flash) {
            flash.scrollIntoView({ behavior: 'smooth', block: 'center' });
            flash.setAttribute('tabindex', '-1');
            flash.focus();
        }
    });
</script>
@endsection



