@extends('layouts.app')

@section('title', 'Edit Pembayaran Biaya Kapal')
@section('page_title', 'Edit Pembayaran Biaya Kapal')

@section('content')
    <div class="bg-white shadow-lg rounded-lg p-4 max-w-6xl mx-auto">
        @php
            $inputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-sm p-2 transition-colors";
            $readonlyInputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm text-sm p-2";
            $labelClasses = "block text-xs font-medium text-gray-700 mb-1";
        @endphp

        <!-- Session Messages -->
        @if(session('error'))
            <div class="mb-3 p-4 rounded-lg bg-red-50 border border-red-200 text-red-800 text-sm">
                <strong>Gagal!</strong> {{ session('error') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mb-3 p-3 rounded-lg bg-yellow-50 border border-yellow-200 text-yellow-800 text-sm">
                <strong>Terjadi kesalahan:</strong>
                <ul class="mt-1 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('pembayaran-biaya-kapal.update', $pembayaran->id) }}" method="POST" class="space-y-3">
            @csrf
            @method('PUT')

            <!-- Header Info Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-3">
                <!-- Data Pembayaran -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">Data Pembayaran</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                            <div>
                                <label for="nomor_pembayaran" class="{{ $labelClasses }}">No. Pembayaran</label>
                                <input type="text" name="nomor_pembayaran" id="nomor_pembayaran" value="{{ $pembayaran->nomor_pembayaran }}" class="{{ $readonlyInputClasses }}" readonly>
                            </div>
                            <div>
                                <label for="nomor_accurate" class="{{ $labelClasses }}">Nomor Accurate</label>
                                <input type="text" name="nomor_accurate" id="nomor_accurate" value="{{ old('nomor_accurate', $pembayaran->nomor_accurate) }}" class="{{ $inputClasses }}" placeholder="Ex: ACC-001">
                            </div>
                            <div>
                                <label for="tanggal_pembayaran" class="{{ $labelClasses }}">Tanggal Pembayaran</label>
                                <input type="date" name="tanggal_pembayaran" id="tanggal_pembayaran" value="{{ old('tanggal_pembayaran', $pembayaran->tanggal_pembayaran ? \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('Y-m-d') : '') }}" class="{{ $inputClasses }}" required>
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
                                <label for="bank" class="{{ $labelClasses }}">Kas / Bank <span class="text-red-500">*</span></label>
                                <select name="bank" id="bank" class="{{ $inputClasses }}" required>
                                    <option value="">Pilih Bank</option>
                                    @foreach($akunCoa as $bank)
                                        <option value="{{ $bank->nama_akun }}" {{ old('bank', $pembayaran->bank) == $bank->nama_akun ? 'selected' : '' }}>
                                            {{ $bank->nama_akun }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="jenis_transaksi" class="{{ $labelClasses }}">Jenis Transaksi <span class="text-red-500">*</span></label>
                                <select name="jenis_transaksi" id="jenis_transaksi" class="{{ $inputClasses }}" required>
                                    <option value="kredit" {{ old('jenis_transaksi', $pembayaran->jenis_transaksi) == 'kredit' ? 'selected' : '' }}>Kredit (Uang Keluar)</option>
                                    <option value="debit" {{ old('jenis_transaksi', $pembayaran->jenis_transaksi) == 'debit' ? 'selected' : '' }}>Debit (Uang Masuk)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoices List (Readonly Info) -->
            <div class="bg-white rounded-lg border border-gray-200 p-3">
                <h4 class="text-sm font-semibold text-gray-800 mb-2">Invoice Terbayar</h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left font-semibold text-gray-700">Nomor Invoice</th>
                                <th class="px-4 py-2 text-left font-semibold text-gray-700">Tanggal</th>
                                <th class="px-4 py-2 text-left font-semibold text-gray-700">Kapal / Vendor</th>
                                <th class="px-4 py-2 text-right font-semibold text-gray-700">Nominal Terbayar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($pembayaran->biayaKapals as $biaya)
                                <tr>
                                    <td class="px-4 py-2 font-medium text-gray-900">{{ $biaya->nomor_invoice }}</td>
                                    <td class="px-4 py-2 text-gray-500">{{ $biaya->tanggal ? \Carbon\Carbon::parse($biaya->tanggal)->format('d/m/Y') : '-' }}</td>
                                    <td class="px-4 py-2 text-gray-600">
                                        {{ $biaya->display_nama_kapal }}<br>
                                        <span class="text-xxs text-gray-400">{{ $biaya->nama_vendor ?? $biaya->penerima }}</span>
                                    </td>
                                    <td class="px-4 py-2 text-right font-semibold text-gray-900">
                                        Rp {{ number_format($biaya->pivot->nominal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Summary & Adjustments Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-800 mb-2">Catatan & Penyesuaian</h4>
                    <div class="space-y-2">
                        <div>
                            <label for="keterangan" class="{{ $labelClasses }}">Keterangan</label>
                            <textarea name="keterangan" id="keterangan" rows="2" class="{{ $inputClasses }}" placeholder="Tambahkan keterangan pembayaran di sini...">{{ old('keterangan', $pembayaran->keterangan) }}</textarea>
                        </div>
                        <div>
                            <label for="alasan_penyesuaian" class="{{ $labelClasses }}">Alasan Penyesuaian (Adjustment)</label>
                            <textarea name="alasan_penyesuaian" id="alasan_penyesuaian" rows="2" class="{{ $inputClasses }}" placeholder="Jelaskan alasan jika ada penyesuaian nominal...">{{ old('alasan_penyesuaian', $pembayaran->alasan_penyesuaian) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-3 border border-gray-200 flex flex-col justify-between">
                    <h4 class="text-sm font-semibold text-gray-800 mb-2">Ringkasan Nominal</h4>
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between py-1 border-b border-gray-200">
                            <span class="text-gray-600">Total Invoice Terpilih:</span>
                            <span class="font-bold text-gray-900" id="displaySubtotal">Rp {{ number_format($pembayaran->total_pembayaran, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center py-1 border-b border-gray-200">
                            <span class="text-gray-600">Penyesuaian (Adjustment):</span>
                            <div class="w-1/2 flex items-center">
                                <span class="mr-1 text-gray-500">Rp</span>
                                <input type="number" name="total_tagihan_penyesuaian" id="inputPenyesuaian" 
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs p-1 text-right"
                                       value="{{ old('total_tagihan_penyesuaian', $pembayaran->total_tagihan_penyesuaian) }}"
                                       placeholder="Ex: -50000 atau 50000">
                            </div>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-200">
                            <span class="text-sm font-bold text-gray-800">Grand Total Pembayaran:</span>
                            <span class="text-sm font-extrabold text-indigo-600" id="displayGrandTotal">Rp 0</span>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 mt-4">
                        <a href="{{ route('pembayaran-biaya-kapal.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-xs font-semibold rounded-md text-gray-700 bg-white hover:bg-gray-50 shadow-sm transition-all duration-200">
                            Batal
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-xs font-semibold rounded-md text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition-all duration-200">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const subtotal = {{ $pembayaran->total_pembayaran }};
        const $inputPenyesuaian = $('#inputPenyesuaian');
        const $displayGrandTotal = $('#displayGrandTotal');

        function formatRupiah(value) {
            const isNegative = value < 0;
            const absValue = Math.abs(value);
            const formatted = 'Rp ' + absValue.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
            return isNegative ? '-' + formatted : formatted;
        }

        function calculateGrandTotal() {
            const penyesuaian = parseFloat($inputPenyesuaian.val()) || 0;
            const grandTotal = subtotal + penyesuaian;
            $displayGrandTotal.text(formatRupiah(grandTotal));
        }

        $inputPenyesuaian.on('input change', calculateGrandTotal);
        calculateGrandTotal();
    });
</script>
@endpush
