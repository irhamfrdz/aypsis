@extends('layouts.app')

@section('title', 'Edit Pembatalan Surat Jalan')
@section('page_title', 'Edit Pembatalan Surat Jalan')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Transaksi Pembatalan</h1>
                <p class="mt-1 text-sm text-gray-600">Perbarui alasan pembatalan untuk surat jalan {{ $pembatalanSuratJalan->no_surat_jalan }}</p>
            </div>
            <a href="{{ route('pembatalan-surat-jalan.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg shadow-sm transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </a>
        </div>

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4 text-sm text-red-800">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <form action="{{ route('pembatalan-surat-jalan.update', $pembatalanSuratJalan->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">No. Surat Jalan</label>
                        <input type="text" value="{{ $pembatalanSuratJalan->no_surat_jalan }}" class="w-full px-4 py-2 border border-gray-200 rounded-lg bg-gray-100 font-medium text-gray-800" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nomor Pembayaran</label>
                        <input type="text" value="{{ $pembatalanSuratJalan->nomor_pembayaran }}" class="w-full px-4 py-2 border border-gray-200 rounded-lg bg-gray-100 font-medium text-gray-800" disabled>
                    </div>
                    <div>
                        <label for="nomor_accurate" class="block text-sm font-semibold text-gray-700 mb-1">Nomor Accurate</label>
                        <input type="text" id="nomor_accurate" name="nomor_accurate" value="{{ old('nomor_accurate', $pembatalanSuratJalan->nomor_accurate) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" placeholder="Masukkan nomor accurate">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
                    <div>
                        <label for="tanggal_kas" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Kas</label>
                        <input type="date" id="tanggal_kas" name="tanggal_kas" value="{{ old('tanggal_kas', optional($pembatalanSuratJalan->tanggal_kas)->format('Y-m-d')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" required>
                    </div>
                    <div>
                        <label for="tanggal_pembayaran" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Pembayaran</label>
                        <input type="date" id="tanggal_pembayaran" name="tanggal_pembayaran" value="{{ old('tanggal_pembayaran', optional($pembatalanSuratJalan->tanggal_pembayaran)->format('Y-m-d')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" required>
                    </div>
                    <div>
                        <label for="bank" class="block text-sm font-semibold text-gray-700 mb-1">Bank</label>
                        <select id="bank" name="bank" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" required>
                            <option value="">-- Pilih Bank --</option>
                            @foreach($akunCoa as $akun)
                                <option value="{{ $akun->nama_akun }}" {{ old('bank', $pembatalanSuratJalan->bank) == $akun->nama_akun ? 'selected' : '' }}>
                                    {{ $akun->nama_akun }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
                    <div>
                        <label for="jenis_transaksi" class="block text-sm font-semibold text-gray-700 mb-1">Jenis Transaksi</label>
                        <select id="jenis_transaksi" name="jenis_transaksi" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" required>
                            <option value="">-- Pilih Jenis --</option>
                            <option value="Debit" {{ old('jenis_transaksi', $pembatalanSuratJalan->jenis_transaksi) == 'Debit' ? 'selected' : '' }}>Debit</option>
                            <option value="Kredit" {{ old('jenis_transaksi', $pembatalanSuratJalan->jenis_transaksi) == 'Kredit' ? 'selected' : '' }}>Kredit</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Total Tagihan</label>
                        <input type="number" id="total_pembayaran" value="{{ old('total_pembayaran', $pembatalanSuratJalan->total_pembayaran) }}" class="w-full px-4 py-2 border border-gray-200 rounded-lg bg-gray-100 font-medium text-gray-800" readonly>
                    </div>
                    <div>
                        <label for="total_tagihan_penyesuaian" class="block text-sm font-semibold text-gray-700 mb-1">Penyesuaian</label>
                        <input type="number" id="total_tagihan_penyesuaian" name="total_tagihan_penyesuaian" value="{{ old('total_tagihan_penyesuaian', $pembatalanSuratJalan->total_tagihan_penyesuaian) }}" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                    <div>
                        <label for="total_tagihan_setelah_penyesuaian" class="block text-sm font-semibold text-gray-700 mb-1">Total Akhir</label>
                        <input type="number" id="total_tagihan_setelah_penyesuaian" name="total_tagihan_setelah_penyesuaian" value="{{ old('total_tagihan_setelah_penyesuaian', $pembatalanSuratJalan->total_tagihan_setelah_penyesuaian) }}" step="0.01" class="w-full px-4 py-2 border border-gray-200 rounded-lg bg-gray-100 font-bold text-gray-800" readonly>
                    </div>
                    <div>
                        <label for="alasan_penyesuaian" class="block text-sm font-semibold text-gray-700 mb-1">Alasan Penyesuaian</label>
                        <textarea id="alasan_penyesuaian" name="alasan_penyesuaian" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">{{ old('alasan_penyesuaian', $pembatalanSuratJalan->alasan_penyesuaian) }}</textarea>
                    </div>
                </div>

                <div class="mb-5">
                    <label for="keterangan" class="block text-sm font-semibold text-gray-700 mb-2">Keterangan</label>
                    <textarea id="keterangan" name="keterangan" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">{{ old('keterangan', $pembatalanSuratJalan->keterangan) }}</textarea>
                </div>

                <div class="mb-5">
                    <label for="alasan_batal" class="block text-sm font-semibold text-gray-700 mb-2">Alasan Batal <span class="text-red-500">*</span></label>
                    <textarea id="alasan_batal" name="alasan_batal" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" placeholder="Berikan alasan pembatalan" required>{{ old('alasan_batal', $pembatalanSuratJalan->alasan_batal) }}</textarea>
                    @error('alasan_batal')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end">
                    <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow-sm transition">
                         Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const totalPembayaranInput = document.getElementById('total_pembayaran');
        const totalPenyesuaianInput = document.getElementById('total_tagihan_penyesuaian');
        const totalAkhirInput = document.getElementById('total_tagihan_setelah_penyesuaian');

        function hitungTotalAkhir() {
            const totalPembayaran = parseFloat(totalPembayaranInput?.value) || 0;
            const totalPenyesuaian = parseFloat(totalPenyesuaianInput?.value) || 0;
            if (totalAkhirInput) {
                totalAkhirInput.value = totalPembayaran + totalPenyesuaian;
            }
        }

        if (totalPenyesuaianInput) {
            totalPenyesuaianInput.addEventListener('input', hitungTotalAkhir);
        }
        hitungTotalAkhir();
    });
</script>
@endsection
