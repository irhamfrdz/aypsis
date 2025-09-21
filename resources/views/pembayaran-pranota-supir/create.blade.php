@extends('layouts.app')

@section('title', 'Form Pembayaran Pranota Supir')
@section('page_title', 'Form Pembayaran Pranota Supi                        </select>
                    </div>
                </div>
            </div>
@section('content')
    <div class="bg-white shadow-lg rounded-lg p-4 max-w-6xl mx-auto">
        @php
            // Definisikan kelas Tailwind untuk input yang lebih compact
            $inputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-sm p-2 transition-colors";
            $readonlyInputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm text-sm p-2";
            $labelClasses = "block text-xs font-medium text-gray-700 mb-1";
        @endphp

        @if(session('success'))
            <div class="mb-3 p-3 rounded-lg bg-green-50 border border-green-200 text-green-800 text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-3 p-3 rounded-lg bg-red-50 border border-red-200 text-red-800 text-sm">
                <strong>Peringatan:</strong> {{ session('error') }}
            </div>
        @endif
        @if(!empty($errors))
            <div class="mb-3 p-3 rounded-lg bg-yellow-50 border border-yellow-200 text-yellow-800 text-sm">
                <strong>Terjadi kesalahan:</strong>
                <ul class="mt-1 list-disc list-inside">
                    @if(is_array($errors))
                        @foreach($errors as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    @elseif(is_object($errors) && method_exists($errors, 'all'))
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    @endif
                </ul>
            </div>
        @endif

        <!-- Header dengan Filter Tanggal (Compact) -->
        <div class="bg-gray-50 rounded-lg p-3 mb-3 border border-gray-200">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-2">
                <div>
                    <h3 class="text-sm font-semibold text-gray-800">Filter Pranota Supir</h3>
                </div>
                <form action="{{ route('pembayaran-pranota-supir.create') }}" method="GET" class="flex flex-col sm:flex-row gap-2">
                    <div class="flex gap-2">
                        <div class="min-w-0">
                            <label for="start_date" class="{{ $labelClasses }}">Dari</label>
                            <input type="date" name="start_date" id="start_date" class="mt-1 block w-full rounded-md border-gray-300 bg-white shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-sm p-2 transition-colors" value="{{ $start_date ?? '' }}">
                        </div>
                        <div class="min-w-0">
                            <label for="end_date" class="{{ $labelClasses }}">Sampai</label>
                            <input type="date" name="end_date" id="end_date" class="mt-1 block w-full rounded-md border-gray-300 bg-white shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-sm p-2 transition-colors" value="{{ $end_date ?? '' }}">
                        </div>
                    </div>
                    <div class="flex gap-1 sm:self-end">
                        <button type="submit" class="inline-flex justify-center py-1.5 px-3 border border-transparent shadow-sm text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            Cari
                        </button>
                        <a href="{{ route('pembayaran-pranota-supir.create') }}" class="inline-flex justify-center py-1.5 px-3 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <form id="pembayaranForm" action="{{ route('pembayaran-pranota-supir.store') }}" method="POST" class="space-y-3">
            @csrf

            <!-- Data Pembayaran & Bank -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-3">
                <!-- Data Pembayaran -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">Data Pembayaran</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <div class="flex items-end gap-1">
                                <div class="flex-1">
                                    <label for="nomor_pembayaran" class="{{ $labelClasses }}">Nomor Pembayaran</label>
                                    <input type="text" name="nomor_pembayaran" id="nomor_pembayaran"
                                        value="{{ '000-' . (request('nomor_cetakan', 1)) . '-' . now()->format('y') . '-' . now()->format('m') . '-' . str_pad((\App\Models\PembayaranPranotaSupir::count() + 1), 6, '0', STR_PAD_LEFT) }}"
                                        class="{{ $readonlyInputClasses }}" readonly>
                                </div>
                                <div class="w-16">
                                    <label for="nomor_cetakan" class="{{ $labelClasses }}">Cetak</label>
                                    <input type="number" name="nomor_cetakan" id="nomor_cetakan" min="1" max="9" value="{{ request('nomor_cetakan', 1) }}"
                                        class="{{ $inputClasses }}">
                                </div>
                            </div>
                            <div>
                                <label for="tanggal_kas" class="{{ $labelClasses }}">Tanggal Kas</label>
                                <input type="text" name="tanggal_kas" id="tanggal_kas"
                                    value="{{ now()->format('d/M/Y') }}"
                                    class="{{ $readonlyInputClasses }}" readonly required>
                                <input type="hidden" name="tanggal_pembayaran" id="tanggal_pembayaran" value="{{ now()->toDateString() }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bank & Transaksi -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">Bank & Transaksi</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <div>
                                <label for="bank" class="{{ $labelClasses }}">Pilih Bank</label>
                                <select name="bank" id="bank" class="{{ $inputClasses }}" required>
                                    <option value="">-- Pilih Bank --</option>
                                    @foreach($akunCoa as $akun)
                                        <option value="{{ $akun->nama_akun }}" data-kode="{{ $akun->kode_nomor ?? '000' }}" {{ old('bank') == $akun->nama_akun ? 'selected' : '' }}>
                                            {{ $akun->nama_akun }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="jenis_transaksi" class="{{ $labelClasses }}">Jenis Transaksi</label>
                                <select name="jenis_transaksi" id="jenis_transaksi" class="{{ $inputClasses }}" required>
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="Debit">Debit</option>
                                    <option value="Kredit">Kredit</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pilih Pranota Supir -->
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-3 py-2 border-b border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-800">Pilih Pranota Supir</h4>
                </div>
                <div class="overflow-x-auto max-h-60">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0 z-20">
                            <tr>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" id="select-all" class="h-3 w-3 text-indigo-600 border-gray-300 rounded">
                                </th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Pranota</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supir</th>
                                <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Biaya</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($pranotas as $pranota)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">
                                        <input type="checkbox" name="pranota_ids[]" value="{{ $pranota->id }}" class="pranota-checkbox h-3 w-3 text-indigo-600 border-gray-300 rounded" checked>
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs font-medium">{{ $pranota->nomor_pranota }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">
                                        @if ($pranota->tanggal_pranota)
                                            {{ \Carbon\Carbon::parse($pranota->tanggal_pranota)->format('d/M/Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">
                                        @if ($pranota->permohonans->isNotEmpty())
                                            @php
                                                $supirs = $pranota->permohonans->pluck('supir')->filter()->unique('id');
                                            @endphp
                                            @if ($supirs->isNotEmpty())
                                                {{ $supirs->map(function($supir) { return $supir->nama_lengkap ?? $supir->nama_panggilan; })->implode(', ') }}
                                            @else
                                                -
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap text-right text-xs font-semibold">Rp {{ number_format($pranota->total_biaya_pranota, 0, ',', '.') }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">
                                        @if ($pranota->status_pembayaran == 'Lunas')
                                            <span class="px-1.5 py-0.5 inline-flex text-xs font-medium rounded bg-green-100 text-green-800">Lunas</span>
                                        @else
                                            <span class="px-1.5 py-0.5 inline-flex text-xs font-medium rounded bg-yellow-100 text-yellow-800">Belum</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-2 py-4 text-center text-xs text-gray-500">
                                        Tidak ada pranota supir yang tersedia.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="bg-gray-50 px-3 py-2 border-t border-gray-200">
                    <p class="text-xs text-gray-600">
                        * Pilih satu atau lebih pranota supir untuk dibayar.
                    </p>
                </div>
            </div>

            <!-- Total Pembayaran & Informasi Tambahan -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-3">
                <!-- Total Pembayaran -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">Total Pembayaran</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                            <div>
                                <label for="total_pembayaran" class="{{ $labelClasses }}">Total Tagihan</label>
                                <input type="number" name="total_pembayaran" id="total_pembayaran"
                                    value="{{ $total_tagihan ?? 0 }}"
                                    class="{{ $readonlyInputClasses }}" readonly>
                            </div>
                            <div>
                                <label for="total_tagihan_penyesuaian" class="{{ $labelClasses }}">Penyesuaian</label>
                                <input type="number" name="total_tagihan_penyesuaian" id="total_tagihan_penyesuaian"
                                    class="{{ $inputClasses }}" value="0">
                            </div>
                            <div>
                                <label for="total_tagihan_setelah_penyesuaian" class="{{ $labelClasses }}">Total Akhir</label>
                                <input type="number" name="total_tagihan_setelah_penyesuaian" id="total_tagihan_setelah_penyesuaian"
                                    class="{{ $readonlyInputClasses }} font-bold text-gray-800 bg-gray-100" readonly value="0">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Tambahan -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">Informasi Tambahan</h4>
                        <div class="space-y-2">
                            <div>
                                <label for="alasan_penyesuaian" class="{{ $labelClasses }}">Alasan Penyesuaian</label>
                                <textarea name="alasan_penyesuaian" id="alasan_penyesuaian" rows="2"
                                    class="{{ $inputClasses }}" placeholder="Jelaskan alasan penyesuaian..."></textarea>
                            </div>
                            <div>
                                <label for="keterangan" class="{{ $labelClasses }}">Keterangan</label>
                                <textarea name="keterangan" id="keterangan" rows="2"
                                    class="{{ $inputClasses }}" placeholder="Tambahkan keterangan..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    Simpan Pembayaran
                </button>
            </div>
        </form>
    </div>

{{-- Script --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAllCheckbox = document.getElementById('select-all');
    // ...existing code...
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
                alert('Silakan pilih minimal satu pranota supir.');
                return false;
            }
        });

        // Perhitungan otomatis total pembayaran berdasarkan pranota yang dipilih
    const totalPembayaranInput = document.getElementById('total_pembayaran');
    const totalPenyesuaianInput = document.getElementById('total_tagihan_penyesuaian');
    const totalSetelahInput = document.getElementById('total_tagihan_setelah_penyesuaian');
        const pranotaCheckboxes = document.querySelectorAll('.pranota-checkbox');

        // Simpan nilai total_biaya_pranota di data attribute
        const pranotaBiayaMap = {};
        @foreach ($pranotas as $pranota)
            pranotaBiayaMap['{{ $pranota->id }}'] = {{ $pranota->total_biaya_pranota }};
        @endforeach

        function updateTotalPembayaran() {
            let total = 0;
            pranotaCheckboxes.forEach(function(checkbox) {
                if (checkbox.checked) {
                    const id = checkbox.value;
                    total += pranotaBiayaMap[id] || 0;
                }
            });
            totalPembayaranInput.value = total;
            updateTotalSetelahPenyesuaian();
        }

        function updateTotalSetelahPenyesuaian() {
            const totalPembayaran = parseFloat(totalPembayaranInput.value) || 0;
            const totalPenyesuaian = parseFloat(totalPenyesuaianInput.value) || 0;
            totalSetelahInput.value = totalPembayaran + totalPenyesuaian;
        }

        pranotaCheckboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', updateTotalPembayaran);
        });
    totalPembayaranInput.addEventListener('input', updateTotalSetelahPenyesuaian);
    totalPenyesuaianInput.addEventListener('input', updateTotalSetelahPenyesuaian);
        updateTotalPembayaran();
    });

    // Keep tanggal_pembayaran hidden field synced with current date
    document.addEventListener('DOMContentLoaded', function () {
        const tanggalPembayaran = document.getElementById('tanggal_pembayaran');
        if (tanggalPembayaran) {
            // Keep hidden field with today's date for validation
            tanggalPembayaran.value = new Date().toISOString().split('T')[0];
        }
    });
</script>
<script>
    // Script untuk update nomor pembayaran
    document.addEventListener('DOMContentLoaded', function () {
        const nomorCetakanInput = document.getElementById('nomor_cetakan');
        const nomorPembayaranInput = document.getElementById('nomor_pembayaran');
        const bankSelect = document.getElementById('bank');

        // Function to get kode_nomor from selected bank option
        function getBankCode() {
            const selectedOption = bankSelect.options[bankSelect.selectedIndex];
            if (selectedOption && selectedOption.value) {
                return selectedOption.getAttribute('data-kode') || '000';
            }
            return '000';
        }

        // Function to update nomor pembayaran
        function updateNomorPembayaran() {
            const cetakan = nomorCetakanInput.value || 1;
            const bankCode = getBankCode();
            const now = new Date();
            const tahun = String(now.getFullYear()).slice(-2);
            const bulan = String(now.getMonth() + 1).padStart(2, '0');
            const running = nomorPembayaranInput.value.split('-').pop() || '000001';

            nomorPembayaranInput.value = `${bankCode}-${cetakan}-${tahun}-${bulan}-${running}`;
        }

        // Event listeners
        nomorCetakanInput.addEventListener('input', updateNomorPembayaran);
        bankSelect.addEventListener('change', updateNomorPembayaran);

        // Initial update
        updateNomorPembayaran();
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
