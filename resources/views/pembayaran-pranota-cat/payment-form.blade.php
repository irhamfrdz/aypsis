@extends('layouts.app')

@section('title', 'Form Pembayaran Pranota CAT Kontainer')
@section('page_title', 'Form Pembayaran Pranota CAT Kontainer')

@section('content')
    @php
        // Hitung counter untuk pembayaran pranota CAT
        $catPaymentCounter = \App\Models\PembayaranPranotaCat::count() + 1;
    @endphp
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
        {{-- Only show validation errors if this is a POST request (form submission) --}}
        @if(request()->isMethod('post') && !empty($errors) && (is_object($errors) ? $errors->any() : (!empty($errors) && is_array($errors))))
            <div class="mb-3 p-3 rounded-lg bg-yellow-50 border border-yellow-200 text-yellow-800 text-sm">
                <strong>Terjadi kesalahan:</strong>
                <ul class="mt-1 list-disc list-inside">
                    @if(is_object($errors) && method_exists($errors, 'all'))
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    @elseif(is_array($errors))
                        @foreach($errors as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    @endif
                </ul>
            </div>
        @endif

        <form id="pembayaranForm" action="{{ route('pembayaran-pranota-cat.store') }}" method="POST" class="space-y-3">
            @csrf

            {{-- Hidden inputs for pranota IDs --}}
            @foreach($pranotaList as $pranota)
                <input type="hidden" name="pranota_ids[]" value="{{ $pranota->id }}">
            @endforeach

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
                                        value="{{ $nomorPembayaran }}"
                                        class="{{ $readonlyInputClasses }}" readonly>
                                </div>
                            </div>
                            <div>
                                <label for="tanggal_kas" class="{{ $labelClasses }}">Tanggal Kas</label>
                                <input type="text" name="tanggal_kas_display" id="tanggal_kas_display"
                                    value="{{ now()->format('d/m/Y') }}"
                                    class="{{ $readonlyInputClasses }}" readonly>
                                <input type="hidden" name="tanggal_kas" id="tanggal_kas" value="{{ now()->toDateString() }}">
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
                                    <option value="debit">Debit</option>
                                    <option value="credit">Credit</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pranota CAT yang Dipilih --}}
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-3 py-2 border-b border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-800">Pranota CAT Kontainer yang Akan Dibayar</h4>
                </div>
                <div class="overflow-x-auto max-h-60">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0 z-20">
                            <tr>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Pranota</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kontainer</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal CAT</th>
                                <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Biaya</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($pranotaList as $pranota)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-2 py-2 whitespace-nowrap text-xs font-medium">
                                        {{ $pranota->no_invoice }}
                                        <input type="hidden" name="pranota_ids[]" value="{{ $pranota->id }}">
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">
                                        @foreach($pranota->tagihanCatItems() as $tagihan)
                                            <div>{{ $tagihan->nomor_kontainer }}</div>
                                        @endforeach
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">
                                        @foreach($pranota->tagihanCatItems() as $tagihan)
                                            <div>{{ $tagihan->vendor }}</div>
                                        @endforeach
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">
                                        @foreach($pranota->tagihanCatItems() as $tagihan)
                                            <div>{{ \Carbon\Carbon::parse($tagihan->tanggal_cat)->format('d/M/Y') }}</div>
                                        @endforeach
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap text-right text-xs font-semibold">Rp {{ number_format($pranota->total_amount, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Total Pembayaran & Informasi Tambahan --}}
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-3">
                <!-- Total Pembayaran -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">Total Pembayaran</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                            <div>
                                <label for="total_pembayaran" class="{{ $labelClasses }}">Total Tagihan</label>
                                <input type="number" name="total_pembayaran" id="total_pembayaran"
                                    value="{{ $totalPembayaran }}"
                                    class="{{ $readonlyInputClasses }}" readonly>
                            </div>
                            <div>
                                <label for="penyesuaian" class="{{ $labelClasses }}">Penyesuaian</label>
                                <input type="number" name="penyesuaian" id="penyesuaian"
                                    class="{{ $inputClasses }}" value="0">
                            </div>
                            <div>
                                <label for="total_setelah_penyesuaian" class="{{ $labelClasses }}">Total Akhir</label>
                                <input type="number" name="total_setelah_penyesuaian" id="total_setelah_penyesuaian"
                                    class="{{ $readonlyInputClasses }} font-bold text-gray-800 bg-gray-100" readonly value="{{ $totalPembayaran }}">
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

            {{-- Submit Button --}}
            <div class="flex justify-between">
                <a href="{{ route('pembayaran-pranota-cat.create') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    Kembali
                </a>
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    Simpan Pembayaran
                </button>
            </div>
        </form>
    </div>

{{-- Script --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Perhitungan otomatis total pembayaran berdasarkan penyesuaian
        const totalPembayaranInput = document.getElementById('total_pembayaran');
        const penyesuaianInput = document.getElementById('penyesuaian');
        const totalSetelahInput = document.getElementById('total_setelah_penyesuaian');

        function updateTotalSetelahPenyesuaian() {
            const totalPembayaran = parseFloat(totalPembayaranInput.value) || 0;
            const penyesuaian = parseFloat(penyesuaianInput.value) || 0;
            totalSetelahInput.value = totalPembayaran + penyesuaian;
        }

        penyesuaianInput.addEventListener('input', updateTotalSetelahPenyesuaian);
        updateTotalSetelahPenyesuaian();

        // Update nomor pembayaran berdasarkan pilihan bank
        const bankSelect = document.getElementById('bank');
        const nomorPembayaranInput = document.getElementById('nomor_pembayaran');

        function updateNomorPembayaran() {
            const selectedOption = bankSelect.options[bankSelect.selectedIndex];
            const kode = selectedOption.getAttribute('data-kode') || '000';
            const counter = {{ $catPaymentCounter }};
            const now = new Date();
            const year = now.getFullYear().toString().slice(-2);
            const month = (now.getMonth() + 1).toString().padStart(2, '0');
            const running = counter.toString().padStart(6, '0');
            const print = '1';
            const nomor = kode + print + year + month + running;
            nomorPembayaranInput.value = nomor;
        }

        bankSelect.addEventListener('change', updateNomorPembayaran);
        // Set initial value jika bank sudah dipilih
        if (bankSelect.value) {
            updateNomorPembayaran();
        }

        // Keep tanggal_kas hidden field synced with current date
        const tanggalKas = document.getElementById('tanggal_kas');
        if (tanggalKas) {
            // Keep hidden field with today's date for validation
            tanggalKas.value = new Date().toISOString().split('T')[0];
        }
    });
</script>
@endsection
