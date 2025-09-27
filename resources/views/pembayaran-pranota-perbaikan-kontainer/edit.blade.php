@extends('layouts.app')

@section('title', 'Edit Pembayaran Pranota Perbaikan Kontainer')
@section('page_title', 'Edit Pembayaran Pranota Perbaikan Kontainer')

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

        <form id="pembayaranForm" action="{{ route('pembayaran-pranota-perbaikan-kontainer.update', $pembayaran) }}" method="POST" class="space-y-3">
            @csrf
            @method('PUT')

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
                                        value="{{ old('nomor_pembayaran', $pembayaran->nomor_pembayaran ?? $pembayaran->nomor_invoice) }}"
                                        class="{{ $readonlyInputClasses }}" readonly>
                                </div>
                                <div class="w-16">
                                    <label for="nomor_cetakan" class="{{ $labelClasses }}">Cetak</label>
                                    <input type="number" name="nomor_cetakan" id="nomor_cetakan" min="1" max="9" value="1"
                                        class="{{ $inputClasses }}">
                                </div>
                            </div>
                            <div>
                                <label for="tanggal_kas" class="{{ $labelClasses }}">Tanggal Kas</label>
                                <input type="text" name="tanggal_kas" id="tanggal_kas"
                                    value="{{ old('tanggal_kas', $pembayaran->tanggal_pembayaran ? \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('d/M/Y') : now()->format('d/M/Y')) }}"
                                    class="{{ $readonlyInputClasses }}" readonly required>
                                <input type="hidden" name="tanggal_pembayaran" id="tanggal_pembayaran"
                                    value="{{ old('tanggal_pembayaran', $pembayaran->tanggal_pembayaran ?? now()->toDateString()) }}">
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
                                        <option value="{{ $akun->nama_akun }}"
                                                data-kode="{{ $akun->kode_nomor ?? '000' }}"
                                                {{ old('bank', $pembayaran->bank ?? '') == $akun->nama_akun ? 'selected' : '' }}>
                                            {{ $akun->nama_akun }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="jenis_transaksi" class="{{ $labelClasses }}">Jenis Transaksi</label>
                                <select name="jenis_transaksi" id="jenis_transaksi" class="{{ $inputClasses }}" required>
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="Debit" {{ old('jenis_transaksi', $pembayaran->jenis_transaksi ?? '') == 'Debit' ? 'selected' : '' }}>Debit</option>
                                    <option value="Kredit" {{ old('jenis_transaksi', $pembayaran->jenis_transaksi ?? '') == 'Kredit' ? 'selected' : '' }}>Kredit</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Detail Pembayaran --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                <!-- Informasi Pembayaran -->
                <div class="bg-white border border-gray-200 rounded-lg p-3">
                    <h4 class="text-sm font-semibold text-gray-800 mb-2">Informasi Pembayaran</h4>
                    <div class="space-y-2">
                        <div>
                            <label for="nominal_pembayaran" class="{{ $labelClasses }}">Nominal Pembayaran</label>
                            <input type="number" name="nominal_pembayaran" id="nominal_pembayaran"
                                value="{{ old('nominal_pembayaran', $pembayaran->nominal_pembayaran) }}"
                                class="{{ $inputClasses }}" step="0.01" min="0" required>
                        </div>
                        <div>
                            <label for="metode_pembayaran" class="{{ $labelClasses }}">Metode Pembayaran</label>
                            <select name="metode_pembayaran" id="metode_pembayaran" class="{{ $inputClasses }}" required>
                                <option value="">-- Pilih Metode --</option>
                                <option value="transfer" {{ old('metode_pembayaran', $pembayaran->metode_pembayaran) == 'transfer' ? 'selected' : '' }}>Transfer</option>
                                <option value="cash" {{ old('metode_pembayaran', $pembayaran->metode_pembayaran) == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="check" {{ old('metode_pembayaran', $pembayaran->metode_pembayaran) == 'check' ? 'selected' : '' }}>Check</option>
                            </select>
                        </div>
                        <div>
                            <label for="status_pembayaran" class="{{ $labelClasses }}">Status Pembayaran</label>
                            <select name="status_pembayaran" id="status_pembayaran" class="{{ $inputClasses }}" required>
                                <option value="">-- Pilih Status --</option>
                                <option value="pending" {{ old('status_pembayaran', $pembayaran->status_pembayaran) == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="completed" {{ old('status_pembayaran', $pembayaran->status_pembayaran) == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('status_pembayaran', $pembayaran->status_pembayaran) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Informasi Tambahan -->
                <div class="bg-white border border-gray-200 rounded-lg p-3">
                    <h4 class="text-sm font-semibold text-gray-800 mb-2">Informasi Tambahan</h4>
                    <div class="space-y-2">
                        <div>
                            <label for="nomor_invoice" class="{{ $labelClasses }}">Nomor Invoice</label>
                            <input type="text" name="nomor_invoice" id="nomor_invoice"
                                value="{{ old('nomor_invoice', $pembayaran->nomor_invoice) }}"
                                class="{{ $inputClasses }}">
                        </div>
                        <div>
                            <label for="keterangan" class="{{ $labelClasses }}">Keterangan</label>
                            <textarea name="keterangan" id="keterangan" rows="3"
                                class="{{ $inputClasses }}">{{ old('keterangan', $pembayaran->keterangan) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Detail Pranota --}}
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-3 py-2 border-b border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-800">Detail Pranota Perbaikan Kontainer</h4>
                </div>
                <div class="p-3">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="{{ $labelClasses }}">Nomor Pranota</label>
                            <input type="text" value="{{ $pembayaran->pranotaPerbaikanKontainer->nomor_pranota ?? 'Belum ada' }}"
                                class="{{ $readonlyInputClasses }}" readonly>
                        </div>
                        <div>
                            <label class="{{ $labelClasses }}">Kontainer</label>
                            <input type="text" value="{{ $pembayaran->pranotaPerbaikanKontainer->perbaikanKontainers->first()->kontainer->nomor_kontainer ?? 'N/A' }}"
                                class="{{ $readonlyInputClasses }}" readonly>
                        </div>
                        <div>
                            <label class="{{ $labelClasses }}">Tanggal Pranota</label>
                            <input type="text" value="{{ $pembayaran->pranotaPerbaikanKontainer->tanggal_pranota ? \Carbon\Carbon::parse($pembayaran->pranotaPerbaikanKontainer->tanggal_pranota)->format('d F Y') : '-' }}"
                                class="{{ $readonlyInputClasses }}" readonly>
                        </div>
                        <div>
                            <label class="{{ $labelClasses }}">Teknisi</label>
                            <input type="text" value="{{ $pembayaran->pranotaPerbaikanKontainer->nama_teknisi ?? '-' }}"
                                class="{{ $readonlyInputClasses }}" readonly>
                        </div>
                        <div class="md:col-span-2">
                            <label class="{{ $labelClasses }}">Deskripsi Pekerjaan</label>
                            <textarea rows="2" class="{{ $readonlyInputClasses }}" readonly>{{ $pembayaran->pranotaPerbaikanKontainer->deskripsi_pekerjaan ?? '' }}</textarea>
                        </div>
                        <div>
                            <label class="{{ $labelClasses }}">Total Biaya Pranota</label>
                            <input type="text" value="Rp {{ number_format($pembayaran->pranotaPerbaikanKontainer->total_biaya ?? 0, 0, ',', '.') }}"
                                class="{{ $readonlyInputClasses }}" readonly>
                        </div>
                        <div>
                            <label class="{{ $labelClasses }}">Status Pranota</label>
                            <input type="text" value="@if($pembayaran->pranotaPerbaikanKontainer->status == 'belum_dibayar') Belum Dibayar @else {{ ucfirst($pembayaran->pranotaPerbaikanKontainer->status ?? 'Unknown') }} @endif"
                                class="{{ $readonlyInputClasses }}" readonly>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                <a href="{{ route('pembayaran-pranota-perbaikan-kontainer.show', $pembayaran) }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors text-sm">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors text-sm font-medium">
                    <i class="fas fa-save mr-1"></i> Update Pembayaran
                </button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Form validation
    $('#pembayaranForm').on('submit', function(e) {
        var nominal = $('#nominal_pembayaran').val();
        if (!nominal || nominal <= 0) {
            e.preventDefault();
            alert('Nominal pembayaran harus diisi dan lebih besar dari 0');
            $('#nominal_pembayaran').focus();
            return false;
        }

        var bank = $('#bank').val();
        if (!bank) {
            e.preventDefault();
            alert('Pilih bank terlebih dahulu');
            $('#bank').focus();
            return false;
        }

        var metode = $('#metode_pembayaran').val();
        if (!metode) {
            e.preventDefault();
            alert('Pilih metode pembayaran terlebih dahulu');
            $('#metode_pembayaran').focus();
            return false;
        }

        var status = $('#status_pembayaran').val();
        if (!status) {
            e.preventDefault();
            alert('Pilih status pembayaran terlebih dahulu');
            $('#status_pembayaran').focus();
            return false;
        }

        return confirm('Apakah Anda yakin ingin mengupdate data pembayaran ini?');
    });
});
</script>
@endsection
