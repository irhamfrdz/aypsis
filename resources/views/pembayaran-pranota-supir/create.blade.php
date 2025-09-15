@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-6 py-8">
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-8 border-b pb-4">
            Form Pembayaran Pranota Supir
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

    <form method="POST" action="{{ route('pembayaran-pranota-supir.store') }}" id="pembayaranForm" class="space-y-8">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div class="space-y-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label for="nomor_pembayaran" class="block text-sm font-semibold text-gray-700 mb-1">Nomor Pembayaran</label>
                            <input type="text" name="nomor_pembayaran" id="nomor_pembayaran"
                                value="{{ 'BMS-' . (request('nomor_cetakan', 1)) . '-' . now()->format('y') . '-' . now()->format('m') . '-' . str_pad((\App\Models\PembayaranPranotaSupir::count() + 1), 6, '0', STR_PAD_LEFT) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5" readonly>
                        </div>
                        <div>
                            <label for="nomor_cetakan" class="block text-sm font-semibold text-gray-700 mb-1">Nomor Cetakan</label>
                            <input type="number" name="nomor_cetakan" id="nomor_cetakan" min="1" max="9" value="{{ request('nomor_cetakan', 1) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5">
                        </div>
                    </div>
                    <div>
                        <label for="tanggal_kas" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Kas</label>
                        <input type="text" name="tanggal_kas" id="tanggal_kas"
                            value="{{ now()->format('d/M/Y') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5" readonly required>
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
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const nomorCetakanInput = document.getElementById('nomor_cetakan');
                    const nomorPembayaranInput = document.getElementById('nomor_pembayaran');
                    nomorCetakanInput.addEventListener('input', function () {
                        const cetakan = nomorCetakanInput.value || 1;
                        const now = new Date();
                        const tahun = String(now.getFullYear()).slice(-2);
                        const bulan = String(now.getMonth() + 1).padStart(2, '0');
                        const running = nomorPembayaranInput.value.split('-').pop();
                        nomorPembayaranInput.value = `BMS-${cetakan}-${tahun}-${bulan}-${running}`;
                    });
                });
            </script>

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
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Pilih Pranota Supir</h3>
                <div class="overflow-x-auto rounded-xl border shadow-sm">
                    <table class="min-w-full text-sm text-left text-gray-700">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-3">
                                    <input type="checkbox" id="select-all" class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                </th>
                                <th class="px-4 py-3">Nomor Pranota</th>
                                <th class="px-4 py-3">Tanggal</th>
                                <th class="px-4 py-3">Supir</th>
                                <th class="px-4 py-3 text-right">Total Biaya</th>
                                <th class="px-4 py-3">Status Pembayaran</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($pranotas as $pranota)
                                <tr class="hover:bg-indigo-50 transition-colors">
                                    <td class="px-4 py-3">
                                        <input type="checkbox" name="pranota_ids[]" value="{{ $pranota->id }}" class="pranota-checkbox h-4 w-4 text-indigo-600 border-gray-300 rounded" checked>
                                    </td>
                                    <td class="px-4 py-3">{{ $pranota->nomor_pranota }}</td>
                                    <td class="px-4 py-3">
                                        @if ($pranota->tanggal_pranota)
                                            {{ \Carbon\Carbon::parse($pranota->tanggal_pranota)->format('d/M/Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($pranota->permohonans->isNotEmpty())
                                            @php
                                                $supirs = $pranota->permohonans->pluck('supir')->filter()->unique('id');
                                            @endphp
                                            @if ($supirs->isNotEmpty())
                                                {{ $supirs->map(function($supir) { return $supir->nama_lengkap ?? $supir->nama_panggilan; })->implode(', ') }}
                                            @else
                                                <div class="text-gray-500">-</div>
                                            @endif
                                        @else
                                            <div class="text-gray-500">-</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right font-medium">Rp {{ number_format($pranota->total_biaya_pranota, 2, ',', '.') }}</td>
                                    <td class="px-4 py-3">
                                        @if ($pranota->status_pembayaran == 'Lunas')
                                            <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-green-100 text-green-800 border border-green-300">Lunas</span>
                                        @else
                                            <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 border border-yellow-300">Belum Lunas</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-4 text-center text-gray-500">Tidak ada pranota supir yang tersedia.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <p class="mt-2 text-sm text-gray-500">* Anda dapat memilih satu atau lebih pranota supir untuk dibayar.</p>
            </div>

            {{-- Penyesuaian Total & Nominal Pembayaran --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                <div>
                    <label for="total_pembayaran" class="block text-sm font-semibold text-gray-700 mb-1">Total Pembayaran</label>
                    <input type="number" name="total_pembayaran" id="total_pembayaran"
                        value="{{ $total_tagihan ?? 0 }}"
                        class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5" required>
                </div>
                <div>
                    <label for="total_tagihan_penyesuaian" class="block text-sm font-semibold text-gray-700 mb-1">Penyesuaian</label>
                    <input type="number" name="total_tagihan_penyesuaian" id="total_tagihan_penyesuaian"
                        class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5" value="0">
                </div>
                <div>
                    <label for="total_tagihan_setelah_penyesuaian" class="block text-sm font-semibold text-gray-700 mb-1">Total Pembayaran setelah Penyesuaian</label>
                    <input type="number" name="total_tagihan_setelah_penyesuaian" id="total_tagihan_setelah_penyesuaian"
                        class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm font-semibold text-gray-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5" readonly value="0">
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
