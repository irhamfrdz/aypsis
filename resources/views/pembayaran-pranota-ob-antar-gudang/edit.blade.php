@extends('layouts.app')

@section('title', 'Edit Pembayaran Pranota OB Antar Gudang')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-24">
    <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-teal-100">
        <!-- Header -->
        <div class="bg-teal-700 text-white px-6 py-4">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h5 class="text-lg font-bold flex items-center">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Pembayaran Pranota OB Antar Gudang
                    </h5>
                    <p class="text-teal-100 text-xs mt-1">Ubah record pembayaran: {{ $pembayaran->nomor_pembayaran }}</p>
                </div>
                <div>
                    <a href="{{ route('pembayaran-pranota-ob-antar-gudang.index') }}"
                       class="bg-teal-600 hover:bg-teal-500 text-white border border-teal-500 px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>

        <div class="p-6">
            @if (session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-4" role="alert">
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-4" role="alert">
                    <ul class="list-disc list-inside text-xs">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('pembayaran-pranota-ob-antar-gudang.update', $pembayaran->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyimpan perubahan data pembayaran ini?')">
                @csrf
                @method('PUT')

                <!-- Grid Form Info Utama -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label for="nomor_pembayaran" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Nomor Pembayaran</label>
                        <input type="text" id="nomor_pembayaran" value="{{ $pembayaran->nomor_pembayaran }}" class="block w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-500 text-sm font-mono focus:outline-none" readonly>
                    </div>

                    <div>
                        <label for="nomor_accurate" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Nomor Accurate</label>
                        <input type="text" name="nomor_accurate" id="nomor_accurate" value="{{ old('nomor_accurate', $pembayaran->nomor_accurate) }}" class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500" placeholder="Masukkan nomor accurate (opsional)">
                    </div>

                    <div>
                        <label for="tanggal_kas" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Tanggal Kas</label>
                        <input type="date" name="tanggal_kas" id="tanggal_kas" value="{{ old('tanggal_kas', $pembayaran->tanggal_kas ? $pembayaran->tanggal_kas->toDateString() : '') }}" class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500" required>
                    </div>

                    <div>
                        <label for="debit_kredit" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Jenis Transaksi</label>
                        <select name="debit_kredit" id="debit_kredit" class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm bg-white focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500" required>
                            <option value="credit" {{ old('debit_kredit', $pembayaran->jenis_transaksi) === 'credit' ? 'selected' : '' }}>KREDIT (Bank berkurang, Biaya bertambah)</option>
                            <option value="debit" {{ old('debit_kredit', $pembayaran->jenis_transaksi) === 'debit' ? 'selected' : '' }}>DEBIT (Bank bertambah, Biaya berkurang)</option>
                        </select>
                    </div>

                    <div>
                        <label for="akun_bank_id" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Pilih Bank/Kas (COA)</label>
                        <select name="akun_bank_id" id="akun_bank_id" class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm bg-white focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500" required>
                            <option value="">-- Pilih Bank --</option>
                            @foreach ($akunBank as $akun)
                                <option value="{{ $akun->id }}" {{ old('akun_bank_id', $pembayaran->akun_bank_id) == $akun->id ? 'selected' : '' }}>
                                    {{ $akun->nomor_akun }} - {{ $akun->nama_akun }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="akun_coa_id" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Pilih Akun Biaya (COA)</label>
                        <select name="akun_coa_id" id="akun_coa_id" class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm bg-white focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500" required>
                            <option value="">-- Pilih Akun Biaya --</option>
                            @foreach ($akunBiaya as $akun)
                                <option value="{{ $akun->id }}" {{ old('akun_coa_id', $pembayaran->akun_coa_id) == $akun->id ? 'selected' : '' }}>
                                    {{ $akun->nomor_akun }} - {{ $akun->nama_akun }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Total Pembayaran -->
                <div class="bg-teal-50 border border-teal-200 rounded-lg p-5 mb-6">
                    <h4 class="text-sm font-semibold text-teal-900 mb-4">Rincian Pembayaran</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="total_tagihan_display" class="block text-xs font-semibold text-teal-800 uppercase mb-1">Total Tagihan</label>
                            <input type="text" id="total_tagihan_display" class="block w-full px-3 py-2 border border-teal-200 rounded-md bg-white text-teal-950 font-bold text-sm" value="Rp {{ number_format($pembayaran->total_pembayaran, 0, ',', '.') }}" readonly data-amount="{{ $pembayaran->total_pembayaran }}">
                        </div>
                        <div>
                            <label for="penyesuaian" class="block text-xs font-semibold text-teal-800 uppercase mb-1">Penyesuaian (Adjustment)</label>
                            <input type="number" name="penyesuaian" id="penyesuaian" class="block w-full px-3 py-2 border border-teal-200 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-teal-500" value="{{ old('penyesuaian', $pembayaran->penyesuaian) }}" step="any">
                        </div>
                        <div>
                            <label for="total_setelah_penyesuaian_display" class="block text-xs font-semibold text-teal-800 uppercase mb-1">Grand Total Bayar</label>
                            <input type="text" id="total_setelah_penyesuaian_display" class="block w-full px-3 py-2 border border-teal-200 rounded-md bg-teal-100 text-teal-950 font-bold text-sm" value="Rp {{ number_format($pembayaran->total_setelah_penyesuaian, 0, ',', '.') }}" readonly>
                        </div>
                    </div>
                </div>

                <!-- Alasan & Keterangan -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label for="alasan_penyesuaian" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Alasan Penyesuaian</label>
                        <textarea name="alasan_penyesuaian" id="alasan_penyesuaian" rows="3" class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500" placeholder="Jelaskan alasan jika ada penyesuaian/selisih nominal...">{{ old('alasan_penyesuaian', $pembayaran->alasan_penyesuaian) }}</textarea>
                    </div>
                    <div>
                        <label for="keterangan" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" rows="3" class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500" placeholder="Catatan tambahan pembayaran...">{{ old('keterangan', $pembayaran->keterangan) }}</textarea>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="flex justify-end gap-2">
                    <button type="submit" class="bg-teal-600 hover:bg-teal-500 text-white border border-teal-500 px-6 py-2.5 rounded-md text-sm font-semibold transition duration-150 ease-in-out shadow-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const totalTagihanInput = document.getElementById('total_tagihan_display');
        const penyesuaianInput = document.getElementById('penyesuaian');
        const totalSetelahPenyesuaianDisplay = document.getElementById('total_setelah_penyesuaian_display');

        function formatRupiah(amount) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
        }

        function recalculate() {
            const total = parseFloat(totalTagihanInput.getAttribute('data-amount')) || 0;
            const penyesuaian = parseFloat(penyesuaianInput.value) || 0;
            const grandTotal = total + penyesuaian;

            totalSetelahPenyesuaianDisplay.value = formatRupiah(grandTotal);
        }

        penyesuaianInput.addEventListener('input', recalculate);
    });
</script>
@endsection
