@extends('layouts.app')

@section('title', 'Edit Pembayaran Pranota Kontainer')

@push('styles')
<style>
.form-container {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    min-h: 100vh;
}

.card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
}

.form-section {
    background: #f9fafb;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

.form-input {
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    transition: all 0.2s;
}

.form-input:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    outline: none;
}

.btn-primary {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    border: none;
    border-radius: 8px;
    transition: all 0.2s;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
}

.btn-secondary {
    background: #6b7280;
    border: none;
    border-radius: 8px;
    transition: all 0.2s;
}

.btn-secondary:hover {
    background: #4b5563;
    transform: translateY(-1px);
}
</style>
@endpush

@section('content')
<div class="form-container py-8">
    <div class="max-w-6xl mx-auto px-4">

        <!-- Header -->
        <div class="card p-6 mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">
                        <i class="fas fa-edit text-blue-500 mr-2"></i>
                        Edit Pembayaran Pranota Kontainer
                    </h1>
                    <div class="flex space-x-4">
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                            {{ $pembayaran->nomor_pembayaran }}
                        </span>
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                            {{ \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('d/m/Y') }}
                        </span>
                    </div>
                </div>
                <a href="{{ route('pembayaran-pranota-kontainer.index') }}"
                   class="btn-secondary text-white px-6 py-3 font-semibold">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Form Section -->
            <div class="lg:col-span-2">
                <div class="card p-8">
                    <h2 class="text-xl font-semibold mb-6 text-gray-900">Form Edit Pembayaran</h2>

                    <form action="{{ route('pembayaran-pranota-kontainer.update', $pembayaran->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Informasi Dasar -->
                        <div class="form-section p-6 mb-6">
                            <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                Informasi Dasar
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Nomor Pembayaran -->
                                <div>
                                    <label for="nomor_pembayaran" class="block text-sm font-medium text-gray-700 mb-2">
                                        Nomor Pembayaran <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text"
                                           name="nomor_pembayaran"
                                           id="nomor_pembayaran"
                                           value="{{ old('nomor_pembayaran', $pembayaran->nomor_pembayaran) }}"
                                           class="form-input w-full px-4 py-3"
                                           required>
                                    @error('nomor_pembayaran')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Bank -->
                                <div>
                                    <label for="bank" class="block text-sm font-medium text-gray-700 mb-2">
                                        Bank <span class="text-red-500">*</span>
                                    </label>
                                    <select name="bank" id="bank" class="form-input w-full px-4 py-3" required>
                                        <option value="">Pilih Bank</option>
                                        <option value="BCA" {{ old('bank', $pembayaran->bank) == 'BCA' ? 'selected' : '' }}>BCA</option>
                                        <option value="BNI" {{ old('bank', $pembayaran->bank) == 'BNI' ? 'selected' : '' }}>BNI</option>
                                        <option value="BRI" {{ old('bank', $pembayaran->bank) == 'BRI' ? 'selected' : '' }}>BRI</option>
                                        <option value="Mandiri" {{ old('bank', $pembayaran->bank) == 'Mandiri' ? 'selected' : '' }}>Mandiri</option>
                                        <option value="BTN" {{ old('bank', $pembayaran->bank) == 'BTN' ? 'selected' : '' }}>BTN</option>
                                        <option value="CIMB Niaga" {{ old('bank', $pembayaran->bank) == 'CIMB Niaga' ? 'selected' : '' }}>CIMB Niaga</option>
                                        <option value="Danamon" {{ old('bank', $pembayaran->bank) == 'Danamon' ? 'selected' : '' }}>Danamon</option>
                                        <option value="Lainnya" {{ old('bank', $pembayaran->bank) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                    @error('bank')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Tanggal -->
                        <div class="form-section p-6 mb-6">
                            <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-calendar text-blue-500 mr-2"></i>
                                Informasi Tanggal
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Tanggal Pembayaran -->
                                <div>
                                    <label for="tanggal_pembayaran" class="block text-sm font-medium text-gray-700 mb-2">
                                        Tanggal Pembayaran <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date"
                                           name="tanggal_pembayaran"
                                           id="tanggal_pembayaran"
                                           value="{{ old('tanggal_pembayaran', $pembayaran->tanggal_pembayaran ? \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('Y-m-d') : '') }}"
                                           class="form-input w-full px-4 py-3"
                                           required>
                                    @error('tanggal_pembayaran')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Tanggal Kas -->
                                <div>
                                    <label for="tanggal_kas" class="block text-sm font-medium text-gray-700 mb-2">
                                        Tanggal Kas <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date"
                                           name="tanggal_kas"
                                           id="tanggal_kas"
                                           value="{{ old('tanggal_kas', $pembayaran->tanggal_kas ? \Carbon\Carbon::parse($pembayaran->tanggal_kas)->format('Y-m-d') : '') }}"
                                           class="form-input w-full px-4 py-3"
                                           required>
                                    @error('tanggal_kas')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Transaksi -->
                        <div class="form-section p-6 mb-6">
                            <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-money-bill-wave text-blue-500 mr-2"></i>
                                Informasi Transaksi
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Jenis Transaksi -->
                                <div>
                                    <label for="jenis_transaksi" class="block text-sm font-medium text-gray-700 mb-2">
                                        Jenis Transaksi <span class="text-red-500">*</span>
                                    </label>
                                    <select name="jenis_transaksi" id="jenis_transaksi" class="form-input w-full px-4 py-3" required>
                                        <option value="">Pilih Jenis Transaksi</option>
                                        <option value="Debit" {{ old('jenis_transaksi', $pembayaran->jenis_transaksi) == 'Debit' ? 'selected' : '' }}>Debit</option>
                                        <option value="Kredit" {{ old('jenis_transaksi', $pembayaran->jenis_transaksi) == 'Kredit' ? 'selected' : '' }}>Kredit</option>
                                    </select>
                                    @error('jenis_transaksi')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Total Pembayaran -->
                                <div>
                                    <label for="total_pembayaran" class="block text-sm font-medium text-gray-700 mb-2">
                                        Total Pembayaran <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-3 text-gray-500">Rp</span>
                                        <input type="text"
                                               name="total_pembayaran_display"
                                               id="total_pembayaran_display"
                                               value="{{ number_format(old('total_pembayaran', $pembayaran->total_pembayaran), 0, ',', '.') }}"
                                               class="form-input w-full pl-12 pr-4 py-3"
                                               placeholder="0"
                                               oninput="formatCurrency(this)"
                                               required>
                                        <input type="hidden"
                                               name="total_pembayaran"
                                               id="total_pembayaran"
                                               value="{{ old('total_pembayaran', $pembayaran->total_pembayaran) }}">
                                    </div>
                                    @error('total_pembayaran')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Penyesuaian -->
                                <div class="md:col-span-2">
                                    <label for="total_tagihan_penyesuaian" class="block text-sm font-medium text-gray-700 mb-2">
                                        Penyesuaian (Opsional)
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-3 text-gray-500">Rp</span>
                                        <input type="text"
                                               name="total_tagihan_penyesuaian_display"
                                               id="total_tagihan_penyesuaian_display"
                                               value="{{ $pembayaran->total_tagihan_penyesuaian ? number_format($pembayaran->total_tagihan_penyesuaian, 0, ',', '.') : '' }}"
                                               class="form-input w-full pl-12 pr-4 py-3"
                                               placeholder="0 (opsional)"
                                               oninput="formatCurrencyWithNegative(this)">
                                        <input type="hidden"
                                               name="total_tagihan_penyesuaian"
                                               id="total_tagihan_penyesuaian"
                                               value="{{ old('total_tagihan_penyesuaian', $pembayaran->total_tagihan_penyesuaian) }}">
                                    </div>
                                    <p class="text-gray-600 text-xs mt-1">
                                        Masukkan nilai positif untuk penambahan, negatif untuk pengurangan
                                    </p>
                                    @error('total_tagihan_penyesuaian')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Keterangan -->
                        <div class="form-section p-6 mb-6">
                            <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-comment text-blue-500 mr-2"></i>
                                Keterangan
                            </h3>

                            <div>
                                <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                                    Keterangan (Opsional)
                                </label>
                                <textarea name="keterangan"
                                          id="keterangan"
                                          rows="4"
                                          class="form-input w-full px-4 py-3 resize-none"
                                          placeholder="Tambahkan keterangan jika diperlukan...">{{ old('keterangan', $pembayaran->keterangan) }}</textarea>
                                @error('keterangan')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('pembayaran-pranota-kontainer.index') }}"
                               class="btn-secondary text-white px-6 py-3 font-semibold">
                                <i class="fas fa-times mr-2"></i>
                                Batal
                            </a>
                            <button type="submit" class="btn-primary text-white px-8 py-3 font-semibold">
                                <i class="fas fa-save mr-2"></i>
                                Update Pembayaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Summary Panel -->
            <div class="lg:col-span-1">
                <!-- Ringkasan Pembayaran -->
                <div class="card p-6 mb-6">
                    <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-chart-bar text-green-500 mr-2"></i>
                        Ringkasan Pembayaran
                    </h3>

                    <div class="space-y-4">
                        <!-- Total Pembayaran -->
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-blue-700">Total Pembayaran</span>
                                <span class="text-lg font-bold text-blue-900 total-pembayaran-preview">
                                    Rp {{ number_format($pembayaran->total_pembayaran ?? 0, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="mt-2 text-xs text-blue-600">
                                <i class="fas fa-sync-alt mr-1"></i>
                                <span>Live preview - akan update otomatis</span>
                            </div>
                        </div>

                        <!-- Penyesuaian Preview (Always show for editing) -->
                        <div class="bg-gray-50 rounded-lg p-4" id="penyesuaian-preview-container">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-700">Penyesuaian</span>
                                <span class="text-lg font-bold text-gray-900 penyesuaian-preview">
                                    {{ $pembayaran->total_tagihan_penyesuaian > 0 ? '+' : '' }}Rp {{ number_format($pembayaran->total_tagihan_penyesuaian ?? 0, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="mt-1 text-xs text-gray-600">
                                <i class="fas fa-info-circle mr-1"></i>
                                <span>Kosongkan jika tidak ada penyesuaian</span>
                            </div>
                        </div>

                        <!-- Total Akhir -->
                        <div class="bg-gray-900 rounded-lg p-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-white">Total Akhir</span>
                                <span class="text-xl font-bold text-white final-total-amount final-total-preview">
                                    Rp {{ number_format(($pembayaran->total_tagihan_setelah_penyesuaian ?? $pembayaran->total_pembayaran) ?? 0, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="mt-2 text-xs text-gray-300">
                                <i class="fas fa-calculator mr-1"></i>
                                <span>Total pembayaran + penyesuaian</span>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="bg-green-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                                <div>
                                    <p class="text-sm font-medium text-green-700">Pembayaran Tercatat</p>
                                    <p class="text-xs text-green-600">{{ \Carbon\Carbon::parse($pembayaran->created_at)->format('d M Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pranota Details -->
                <div class="card p-6">
                    <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-file-invoice text-purple-500 mr-2"></i>
                        Pranota yang Dibayar
                    </h3>

                    @if($pembayaran->items && $pembayaran->items->count() > 0)
                        <div class="space-y-3">
                            @foreach($pembayaran->items as $index => $item)
                                <div class="pranota-item bg-gray-50 rounded-lg p-4 border-l-4 border-purple-500 hover:bg-purple-50 transition-colors duration-200"
                                     data-pranota-id="{{ $item->pranota->id ?? 'N/A' }}"
                                     id="pranota-item-{{ $item->pranota->id ?? 'null' }}">
                                    <div class="flex justify-between items-center">
                                        <div class="flex items-center space-x-3 cursor-pointer flex-1"
                                             onclick="viewPranotaDetail({{ $item->pranota->id ?? 'null' }})">
                                            <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-medium">
                                                #{{ $index + 1 }}
                                            </span>
                                            <div>
                                                <h4 class="text-sm font-semibold text-gray-900">
                                                    {{ $item->pranota->no_invoice ?? 'N/A' }}
                                                </h4>
                                                @if($item->pranota)
                                                    @php
                                                        $tagihan = $item->pranota->getTagihanItems();
                                                    @endphp
                                                    <p class="text-xs text-gray-600">
                                                        {{ $tagihan->count() }} kontainer • {{ $item->pranota->jumlah_tagihan }} tagihan
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span class="text-sm font-bold text-purple-900">
                                                Rp {{ number_format($item->amount ?? 0, 0, ',', '.') }}
                                            </span>
                                            <div class="flex items-center space-x-1">
                                                <button type="button"
                                                        onclick="viewPranotaDetail({{ $item->pranota->id ?? 'null' }})"
                                                        class="text-blue-500 hover:text-blue-700 p-1 rounded"
                                                        title="Lihat Detail Pranota">
                                                    <i class="fas fa-external-link-alt text-sm"></i>
                                                </button>
                                                <button type="button"
                                                        onclick="removePranotaFromPayment({{ $pembayaran->id }}, {{ $item->pranota->id ?? 'null' }}, '{{ $item->pranota->no_invoice ?? 'N/A' }}')"
                                                        class="text-red-500 hover:text-red-700 p-1 rounded"
                                                        title="Hapus Pranota dari Pembayaran">
                                                    <i class="fas fa-trash text-sm"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Total Pranota -->
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-700">
                                    Total {{ $pembayaran->items->count() }} Pranota:
                                </span>
                                <span class="text-lg font-bold text-gray-900 pranota-total-amount">
                                    Rp {{ number_format($pembayaran->items->sum('amount'), 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-file-excel text-gray-400 text-3xl mb-3"></i>
                            <p class="text-gray-500 text-sm">Tidak ada data pranota</p>
                            <p class="text-gray-400 text-xs">Data mungkin telah dihapus atau belum tersinkronisasi</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('title', 'Edit Pembayaran Pranota Kontainer')

@push('styles')
<style>
/* === CLEAN FORM STYLES === */
.form-container {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    min-height: 100vh;
}

.card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
}

.form-section {
    background: #f9fafb;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

.form-input {
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    transition: all 0.2s;
}

.form-input:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    outline: none;
}

.btn-primary {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    border: none;
    border-radius: 8px;
    transition: all 0.2s;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
}

.btn-secondary {
    background: #6b7280;
    border: none;
    border-radius: 8px;
    transition: all 0.2s;
}

.btn-secondary:hover {
    background: #4b5563;
    transform: translateY(-1px);
}
</style>
@endpush

@section('content')
<div class="form-container py-8">
    <div class="max-w-6xl mx-auto px-4">

        <!-- Header -->
        <div class="card p-6 mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">
                        <i class="fas fa-edit text-blue-500 mr-2"></i>
                        Edit Pembayaran Pranota Kontainer
                    </h1>
                    <div class="flex space-x-4">
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                            {{ $pembayaran->nomor_pembayaran }}
                        </span>
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                            {{ \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('d/m/Y') }}
                        </span>
                    </div>
                </div>
                <a href="{{ route('pembayaran-pranota-kontainer.index') }}"
                   class="btn-secondary text-white px-6 py-3 font-semibold">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex flex-col gap-8 w-full">
            <!-- Form Section -->
            <div class="w-full">
                <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
                    <!-- Form Header -->
                    <div class="section-header">
                        <h2 class="text-xl font-bold flex items-center">
                            <i class="fas fa-edit mr-3"></i>
                            Form Edit Pembayaran
                        </h2>
                        <p class="text-blue-100 mt-1 text-sm">Ubah informasi pembayaran sesuai kebutuhan</p>
                    </div>

                    <!-- Form Content -->
                    <div class="p-8">

                    <!-- Form Content -->
                    <div class="p-8">
                        <form action="{{ route('pembayaran-pranota-kontainer.update', $pembayaran->id) }}" method="POST" class="space-y-8">
                            @csrf
                            @method('PUT')

                            <!-- Section 1: Informasi Dasar -->
                            <div class="form-section rounded-xl p-6">
                                <div class="section-header">
                                    <h3 class="text-lg font-semibold flex items-center">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        Informasi Dasar
                                    </h3>
                                </div>
                                <div class="section-content">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- Nomor Pembayaran -->
                                        <div class="input-group">
                                            <label for="nomor_pembayaran" class="block text-sm font-semibold text-gray-700 mb-3">
                                                <i class="fas fa-hashtag text-blue-500 mr-2"></i>
                                                Nomor Pembayaran <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text"
                                                   name="nomor_pembayaran"
                                                   id="nomor_pembayaran"
                                                   value="{{ old('nomor_pembayaran', $pembayaran->nomor_pembayaran) }}"
                                                   class="form-input block w-full px-4 py-3 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 transition duration-200"
                                                   required>
                                            @error('nomor_pembayaran')
                                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                                    {{ $message }}
                                                </p>
                                            @enderror
                                        </div>

                                        <!-- Bank -->
                                        <div class="input-group">
                                            <label for="bank" class="block text-sm font-semibold text-gray-700 mb-3">
                                                <i class="fas fa-university text-blue-500 mr-2"></i>
                                                Bank <span class="text-red-500">*</span>
                                            </label>
                                            <select name="bank"
                                                    id="bank"
                                                    class="form-input block w-full px-4 py-3 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 transition duration-200"
                                                    required>
                                                <option value="">Pilih Bank</option>
                                                <option value="BCA" {{ old('bank', $pembayaran->bank) == 'BCA' ? 'selected' : '' }}>BCA</option>
                                                <option value="BNI" {{ old('bank', $pembayaran->bank) == 'BNI' ? 'selected' : '' }}>BNI</option>
                                                <option value="BRI" {{ old('bank', $pembayaran->bank) == 'BRI' ? 'selected' : '' }}>BRI</option>
                                                <option value="Mandiri" {{ old('bank', $pembayaran->bank) == 'Mandiri' ? 'selected' : '' }}>Mandiri</option>
                                                <option value="BTN" {{ old('bank', $pembayaran->bank) == 'BTN' ? 'selected' : '' }}>BTN</option>
                                                <option value="CIMB Niaga" {{ old('bank', $pembayaran->bank) == 'CIMB Niaga' ? 'selected' : '' }}>CIMB Niaga</option>
                                                <option value="Danamon" {{ old('bank', $pembayaran->bank) == 'Danamon' ? 'selected' : '' }}>Danamon</option>
                                                <option value="Lainnya" {{ old('bank', $pembayaran->bank) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                            </select>
                                            @error('bank')
                                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                                    {{ $message }}
                                                </p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 2: Informasi Tanggal -->
                            <div class="form-section rounded-xl p-6">
                                <div class="section-header">
                                    <h3 class="text-lg font-semibold flex items-center">
                                        <i class="fas fa-calendar-alt mr-2"></i>
                                        Informasi Tanggal
                                    </h3>
                                </div>
                                <div class="section-content">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- Tanggal Pembayaran -->
                                        <div class="input-group">
                                            <label for="tanggal_pembayaran" class="block text-sm font-semibold text-gray-700 mb-3">
                                                <i class="fas fa-calendar text-blue-500 mr-2"></i>
                                                Tanggal Pembayaran <span class="text-red-500">*</span>
                                            </label>
                                            <input type="date"
                                                   name="tanggal_pembayaran"
                                                   id="tanggal_pembayaran"
                                                   value="{{ old('tanggal_pembayaran', $pembayaran->tanggal_pembayaran ? \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('Y-m-d') : '') }}"
                                                   class="form-input block w-full px-4 py-3 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 transition duration-200"
                                                   required>
                                            @error('tanggal_pembayaran')
                                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                                    {{ $message }}
                                                </p>
                                            @enderror
                                        </div>

                                        <!-- Tanggal Kas -->
                                        <div class="input-group">
                                            <label for="tanggal_kas" class="block text-sm font-semibold text-gray-700 mb-3">
                                                <i class="fas fa-calendar-check text-blue-500 mr-2"></i>
                                                Tanggal Kas <span class="text-red-500">*</span>
                                            </label>
                                            <input type="date"
                                                   name="tanggal_kas"
                                                   id="tanggal_kas"
                                                   value="{{ old('tanggal_kas', $pembayaran->tanggal_kas ? \Carbon\Carbon::parse($pembayaran->tanggal_kas)->format('Y-m-d') : '') }}"
                                                   class="form-input block w-full px-4 py-3 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 transition duration-200"
                                                   required>
                                            @error('tanggal_kas')
                                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                                    {{ $message }}
                                                </p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 3: Informasi Transaksi -->
                            <div class="form-section rounded-xl p-6">
                                <div class="section-header">
                                    <h3 class="text-lg font-semibold flex items-center">
                                        <i class="fas fa-money-bill-wave mr-2"></i>
                                        Informasi Transaksi
                                    </h3>
                                </div>
                                <div class="section-content">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- Jenis Transaksi -->
                                        <div class="input-group">
                                            <label for="jenis_transaksi" class="block text-sm font-semibold text-gray-700 mb-3">
                                                <i class="fas fa-exchange-alt text-blue-500 mr-2"></i>
                                                Jenis Transaksi <span class="text-red-500">*</span>
                                            </label>
                                            <select name="jenis_transaksi"
                                                    id="jenis_transaksi"
                                                    class="form-input block w-full px-4 py-3 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 transition duration-200"
                                                    required>
                                                <option value="">Pilih Jenis Transaksi</option>
                                                <option value="transfer" {{ old('jenis_transaksi', $pembayaran->jenis_transaksi) == 'transfer' ? 'selected' : '' }}>Transfer</option>
                                                <option value="tunai" {{ old('jenis_transaksi', $pembayaran->jenis_transaksi) == 'tunai' ? 'selected' : '' }}>Tunai</option>
                                                <option value="cek" {{ old('jenis_transaksi', $pembayaran->jenis_transaksi) == 'cek' ? 'selected' : '' }}>Cek</option>
                                                <option value="debit" {{ old('jenis_transaksi', $pembayaran->jenis_transaksi) == 'debit' ? 'selected' : '' }}>Debit</option>
                                                <option value="Debit" {{ old('jenis_transaksi', $pembayaran->jenis_transaksi) == 'Debit' ? 'selected' : '' }}>Debit (Legacy)</option>
                                                <option value="Kredit" {{ old('jenis_transaksi', $pembayaran->jenis_transaksi) == 'Kredit' ? 'selected' : '' }}>Kredit (Legacy)</option>
                                            </select>
                                            @error('jenis_transaksi')
                                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                                    {{ $message }}
                                                </p>
                                            @enderror
                                        </div>

                                        <!-- Total Pembayaran -->
                                        <div class="input-group">
                                            <label for="total_pembayaran" class="block text-sm font-semibold text-gray-700 mb-3">
                                                <i class="fas fa-dollar-sign text-blue-500 mr-2"></i>
                                                Total Pembayaran <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                                    <span class="text-gray-500 font-medium">Rp</span>
                                                </div>
                                                <input type="number"
                                                       name="total_pembayaran"
                                                       id="total_pembayaran"
                                                       value="{{ old('total_pembayaran', $pembayaran->total_pembayaran) }}"
                                                       class="form-input block w-full pl-12 pr-4 py-3 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 transition duration-200"
                                                       step="0.01"
                                                       min="0"
                                                       required>
                                            </div>
                                            @error('total_pembayaran')
                                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                                    {{ $message }}
                                                </p>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Penyesuaian (Full Width) -->
                                    <div class="input-group mt-6">
                                        <label for="total_tagihan_penyesuaian" class="block text-sm font-semibold text-gray-700 mb-3">
                                            <i class="fas fa-adjust text-blue-500 mr-2"></i>
                                            Penyesuaian (Opsional)
                                        </label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                                <span class="text-gray-500 font-medium">Rp</span>
                                            </div>
                                            <input type="number"
                                                   name="total_tagihan_penyesuaian"
                                                   id="total_tagihan_penyesuaian"
                                                   value="{{ old('total_tagihan_penyesuaian', $pembayaran->total_tagihan_penyesuaian) }}"
                                                   class="form-input block w-full pl-12 pr-4 py-3 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 transition duration-200"
                                                   step="0.01">
                                        </div>
                                        <p class="mt-2 text-sm text-gray-600 flex items-center">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            Masukkan nilai positif untuk penambahan, negatif untuk pengurangan
                                        </p>
                                        @error('total_tagihan_penyesuaian')
                                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                                <i class="fas fa-exclamation-circle mr-1"></i>
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Section 4: Keterangan -->
                            <div class="form-section rounded-xl p-6">
                                <div class="section-header">
                                    <h3 class="text-lg font-semibold flex items-center">
                                        <i class="fas fa-sticky-note mr-2"></i>
                                        Keterangan Tambahan
                                    </h3>
                                </div>
                                <div class="section-content">
                                    <div class="input-group">
                                        <label for="keterangan" class="block text-sm font-semibold text-gray-700 mb-3">
                                            <i class="fas fa-comment text-blue-500 mr-2"></i>
                                            Keterangan (Opsional)
                                        </label>
                                        <textarea name="keterangan"
                                                  id="keterangan"
                                                  rows="4"
                                                  class="form-input block w-full px-4 py-3 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 transition duration-200 resize-none"
                                                  placeholder="Tambahkan keterangan jika diperlukan...">{{ old('keterangan', $pembayaran->keterangan) }}</textarea>
                                        @error('keterangan')
                                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                                <i class="fas fa-exclamation-circle mr-1"></i>
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4 pt-6">
                                <a href="{{ route('pembayaran-pranota-kontainer.index') }}"
                                   class="inline-flex items-center justify-center px-8 py-4 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-xl shadow-lg transition duration-200 ease-in-out transform hover:scale-105 hover:shadow-xl">
                                    <i class="fas fa-times mr-2"></i>
                                    Batal
                                </a>
                                <button type="submit"
                                        class="inline-flex items-center justify-center px-10 py-4 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-xl shadow-lg transition duration-200 ease-in-out transform hover:scale-105 hover:shadow-xl">
                                    <i class="fas fa-save mr-2"></i>
                                    Update Pembayaran
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Summary Section -->
            <div class="w-full">
                <!-- Summary Card -->
                <div class="bg-gradient-to-br from-white to-gray-50 shadow-xl rounded-2xl overflow-hidden border border-gray-100">
                    <div class="section-header">
                        <h3 class="text-lg font-bold flex items-center">
                            <i class="fas fa-chart-bar mr-2"></i>
                            Ringkasan Pembayaran
                        </h3>
                    </div>

                    <div class="p-6 space-y-4">
                        <!-- Total Pembayaran -->
                        <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl p-4 border border-blue-200">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-semibold text-blue-700 flex items-center">
                                    <i class="fas fa-wallet mr-2"></i>
                                    Total Pembayaran
                                </span>
                                <span class="text-xl font-bold text-blue-900">
                                    Rp {{ number_format($pembayaran->total_pembayaran ?? 0, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        @if($pembayaran->total_tagihan_penyesuaian != 0)
                        <!-- Penyesuaian -->
                        <div class="bg-gradient-to-r from-{{ $pembayaran->total_tagihan_penyesuaian > 0 ? 'green' : 'red' }}-50 to-{{ $pembayaran->total_tagihan_penyesuaian > 0 ? 'green' : 'red' }}-100 rounded-xl p-4 border border-{{ $pembayaran->total_tagihan_penyesuaian > 0 ? 'green' : 'red' }}-200">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-semibold text-{{ $pembayaran->total_tagihan_penyesuaian > 0 ? 'green' : 'red' }}-700 flex items-center">
                                    <i class="fas fa-adjust mr-2"></i>
                                    Penyesuaian
                                </span>
                                <span class="text-lg font-bold text-{{ $pembayaran->total_tagihan_penyesuaian > 0 ? 'green' : 'red' }}-900">
                                    {{ $pembayaran->total_tagihan_penyesuaian > 0 ? '+' : '' }}Rp {{ number_format($pembayaran->total_tagihan_penyesuaian ?? 0, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                        @endif

                        <!-- Total Akhir -->
                        <div class="bg-gradient-to-r from-gray-800 to-gray-900 rounded-xl p-4 border border-gray-700">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-semibold text-white flex items-center">
                                    <i class="fas fa-calculator mr-2"></i>
                                    Total Akhir
                                </span>
                                <span class="text-xl font-bold text-white">
                                    Rp {{ number_format(($pembayaran->total_tagihan_setelah_penyesuaian ?? $pembayaran->total_pembayaran) ?? 0, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <!-- Status Info -->
                        <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-xl p-4 border border-green-200">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle text-green-500 text-lg"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-semibold text-green-700">Pembayaran Tercatat</p>
                                    <p class="text-xs text-green-600">{{ \Carbon\Carbon::parse($pembayaran->created_at)->format('d M Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pranota Details Card -->
                <div class="bg-gradient-to-br from-white to-gray-50 shadow-xl rounded-2xl overflow-hidden border border-gray-100 mt-6">
                    <div class="section-header">
                        <h3 class="text-lg font-bold flex items-center">
                            <i class="fas fa-file-invoice mr-2"></i>
                            Pranota yang Dibayar
                        </h3>
                        <p class="text-blue-100 mt-1 text-sm">Detail pranota dalam pembayaran ini</p>
                    </div>

                    <div class="p-6">
                        @if($pembayaran->items && $pembayaran->items->count() > 0)
                            <div class="space-y-4">
                                @foreach($pembayaran->items as $index => $item)
                                    <div class="pranota-item-detailed bg-gradient-to-r from-purple-50 to-purple-100 rounded-xl p-4 border-l-4 border-purple-500 hover:shadow-md hover:from-purple-100 hover:to-purple-200 transition duration-200"
                                         data-pranota-id="{{ $item->pranota->id ?? 'N/A' }}"
                                         id="pranota-item-enhanced-{{ $item->pranota->id ?? 'null' }}">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3 cursor-pointer flex-1"
                                                 onclick="viewPranotaDetail({{ $item->pranota->id ?? 'null' }})">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-purple-200 text-purple-800">
                                                    #{{ $index + 1 }}
                                                </span>
                                                <div>
                                                    <h4 class="text-sm font-bold text-gray-900">
                                                        {{ $item->pranota->no_invoice ?? 'N/A' }}
                                                    </h4>
                                                    @if($item->pranota)
                                                        @php
                                                            $tagihan = $item->pranota->getTagihanItems();
                                                        @endphp
                                                        <p class="text-xs text-gray-600">
                                                            {{ $tagihan->count() }} kontainer • Total: Rp {{ number_format($item->amount ?? 0, 0, ',', '.') }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <span class="inline-flex items-center px-3 py-1 rounded-lg bg-purple-600 text-white font-bold text-sm">
                                                    Rp {{ number_format($item->amount ?? 0, 0, ',', '.') }}
                                                </span>
                                                <div class="flex items-center space-x-1">
                                                    <button type="button"
                                                            onclick="viewPranotaDetail({{ $item->pranota->id ?? 'null' }})"
                                                            class="text-blue-500 hover:text-blue-700 p-1 rounded bg-white shadow-sm"
                                                            title="Lihat Detail Pranota">
                                                        <i class="fas fa-external-link-alt text-sm"></i>
                                                    </button>
                                                    <button type="button"
                                                            onclick="removePranotaFromPayment({{ $pembayaran->id }}, {{ $item->pranota->id ?? 'null' }}, '{{ $item->pranota->no_invoice ?? 'N/A' }}')"
                                                            class="text-red-500 hover:text-red-700 p-1 rounded bg-white shadow-sm"
                                                            title="Hapus Pranota dari Pembayaran">
                                                        <i class="fas fa-trash text-sm"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Total Pranota -->
                            <div class="mt-6 pt-4 border-t border-gray-200">
                                <div class="flex justify-between items-center bg-gradient-to-r from-purple-100 to-purple-200 rounded-lg p-4">
                                    <span class="text-sm font-bold text-purple-800 flex items-center">
                                        <i class="fas fa-calculator mr-2"></i>
                                        Total {{ $pembayaran->items->count() }} Pranota:
                                    </span>
                                    <span class="text-lg font-bold text-purple-900">
                                        Rp {{ number_format($pembayaran->items->sum('amount'), 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-12">
                                <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                                    <i class="fas fa-file-excel text-gray-400 text-2xl"></i>
                                </div>
                                <p class="text-gray-500 text-sm font-medium">Tidak ada data pranota ditemukan</p>
                                <p class="text-gray-400 text-xs mt-1">Data pranota mungkin telah dihapus atau belum tersinkronisasi</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🎨 Enhanced form layout initialized');

    // Form enhancement features
    const formInputs = document.querySelectorAll('.form-input');
    const inputGroups = document.querySelectorAll('.input-group');

    // Add focus effects to form inputs
    formInputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.closest('.input-group')?.classList.add('focused');
        });

        input.addEventListener('blur', function() {
            this.closest('.input-group')?.classList.remove('focused');
        });
    });

    // Add validation styling
    const requiredInputs = document.querySelectorAll('input[required], select[required]');
    requiredInputs.forEach(input => {
        input.addEventListener('invalid', function() {
            this.style.borderColor = '#ef4444';
            this.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.1)';
        });

        input.addEventListener('input', function() {
            if (this.validity.valid) {
                this.style.borderColor = '#10b981';
                this.style.boxShadow = '0 0 0 3px rgba(16, 185, 129, 0.1)';
            }
        });
    });

    // Real-time total calculation preview
    const totalPembayaranInput = document.getElementById('total_pembayaran');
    const penyesuaianInput = document.getElementById('total_tagihan_penyesuaian');

    if (totalPembayaranInput && penyesuaianInput) {
        function updateTotal() {
            const total = parseFloat(totalPembayaranInput.value) || 0;
            const penyesuaian = parseFloat(penyesuaianInput.value) || 0;
            const finalTotal = total + penyesuaian;

            // Update preview if exists
            const preview = document.querySelector('.total-preview');
            if (preview) {
                preview.textContent = `Rp ${finalTotal.toLocaleString('id-ID')}`;
            }
        }

        totalPembayaranInput.addEventListener('input', updateTotal);
        penyesuaianInput.addEventListener('input', updateTotal);
    }

    console.log('✅ Enhanced form features activated');
});

// Currency formatting functions
function formatCurrency(input) {
    // Remove all non-numeric characters except decimal point
    let value = input.value.replace(/[^\d]/g, '');

    // Convert to number and format with thousand separators
    if (value) {
        let numericValue = parseInt(value);
        input.value = numericValue.toLocaleString('id-ID');
        // Update hidden input with raw numeric value
        document.getElementById('total_pembayaran').value = numericValue;
    } else {
        input.value = '';
        document.getElementById('total_pembayaran').value = '';
    }

    // Trigger real-time calculation
    updateTotalCalculation();
}

function formatCurrencyWithNegative(input) {
    // Check if it starts with minus sign
    let isNegative = input.value.startsWith('-');

    // Remove all non-numeric characters except decimal point
    let value = input.value.replace(/[^\d]/g, '');

    // Convert to number and format with thousand separators
    if (value) {
        let numericValue = parseInt(value);
        if (isNegative) {
            numericValue = -numericValue;
            input.value = '-' + Math.abs(numericValue).toLocaleString('id-ID');
        } else {
            input.value = numericValue.toLocaleString('id-ID');
        }
        // Update hidden input with raw numeric value
        document.getElementById('total_tagihan_penyesuaian').value = numericValue;
    } else {
        input.value = isNegative ? '-' : '';
        document.getElementById('total_tagihan_penyesuaian').value = '';
    }

    // Trigger real-time calculation
    updateTotalCalculation();
}

function updateTotalCalculation() {
    const totalPembayaran = parseFloat(document.getElementById('total_pembayaran').value) || 0;
    const penyesuaian = parseFloat(document.getElementById('total_tagihan_penyesuaian').value) || 0;
    const finalTotal = totalPembayaran + penyesuaian;

    // Update preview displays if they exist
    const totalPembayaranDisplay = document.querySelector('.total-pembayaran-preview');
    if (totalPembayaranDisplay) {
        totalPembayaranDisplay.textContent = `Rp ${totalPembayaran.toLocaleString('id-ID')}`;
    }

    const penyesuaianDisplay = document.querySelector('.penyesuaian-preview');
    if (penyesuaianDisplay) {
        penyesuaianDisplay.textContent = `${penyesuaian >= 0 ? '+' : ''}Rp ${penyesuaian.toLocaleString('id-ID')}`;
    }

    const finalTotalDisplay = document.querySelector('.final-total-preview');
    if (finalTotalDisplay) {
        finalTotalDisplay.textContent = `Rp ${finalTotal.toLocaleString('id-ID')}`;
    }
}

// Initialize currency formatting on page load
document.addEventListener('DOMContentLoaded', function() {
    // Format existing values
    const totalPembayaranDisplay = document.getElementById('total_pembayaran_display');
    const penyesuaianDisplay = document.getElementById('total_tagihan_penyesuaian_display');

    if (totalPembayaranDisplay && totalPembayaranDisplay.value) {
        formatCurrency(totalPembayaranDisplay);
    }

    if (penyesuaianDisplay && penyesuaianDisplay.value) {
        formatCurrencyWithNegative(penyesuaianDisplay);
    }
});

// Function to redirect to pranota detail page
function viewPranotaDetail(pranotaId) {
    if (pranotaId && pranotaId !== 'null') {
        // Redirect to pranota detail page
        window.location.href = `{{ url('/pranota') }}/${pranotaId}`;
    } else {
        console.error('Invalid pranota ID');
    }
}

// Function to remove pranota from payment
function removePranotaFromPayment(pembayaranId, pranotaId, pranotaNo) {
    if (!pranotaId || pranotaId === 'null') {
        alert('ID Pranota tidak valid');
        return;
    }

    if (confirm(`Apakah Anda yakin ingin menghapus pranota "${pranotaNo}" dari pembayaran ini?\n\nPranota akan dikembalikan ke status "unpaid" dan total pembayaran akan diperbarui.`)) {
        // Show loading state
        const buttons = document.querySelectorAll(`[onclick*="removePranotaFromPayment(${pembayaranId}, ${pranotaId}"]`);
        buttons.forEach(btn => {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin text-sm"></i>';
        });

        // Send AJAX request
        fetch(`{{ url('/pembayaran-pranota-kontainer') }}/${pembayaranId}/pranota/${pranotaId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove the pranota item from DOM
                const itemElements = [
                    document.getElementById(`pranota-item-${pranotaId}`),
                    document.getElementById(`pranota-item-enhanced-${pranotaId}`)
                ];

                itemElements.forEach(element => {
                    if (element) {
                        element.style.transition = 'all 0.3s ease';
                        element.style.opacity = '0';
                        element.style.transform = 'translateX(-100%)';
                        setTimeout(() => element.remove(), 300);
                    }
                });

                // Update total displays
                updateTotalDisplays(data.new_total, data.new_final_total);

                // Show success message
                showNotification('success', data.message);

                // Reload page after short delay to ensure consistency
                setTimeout(() => {
                    window.location.reload();
                }, 1500);

            } else {
                showNotification('error', data.error || 'Gagal menghapus pranota');
                // Restore buttons
                buttons.forEach(btn => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-trash text-sm"></i>';
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('error', 'Terjadi kesalahan saat menghapus pranota');
            // Restore buttons
            buttons.forEach(btn => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-trash text-sm"></i>';
            });
        });
    }
}

// Function to update total displays
function updateTotalDisplays(newTotal, newFinalTotal) {
    // Update total pranota display
    const totalElements = document.querySelectorAll('.pranota-total-amount');
    totalElements.forEach(element => {
        element.textContent = `Rp ${newTotal}`;
    });

    // Update final total display
    const finalTotalElements = document.querySelectorAll('.final-total-amount');
    finalTotalElements.forEach(element => {
        element.textContent = `Rp ${newFinalTotal}`;
    });
}

// Function to show notifications
function showNotification(type, message) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg text-white ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    } transition-all duration-300 transform translate-x-full`;
    notification.innerHTML = `
        <div class="flex items-center space-x-2">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
            <span>${message}</span>
        </div>
    `;

    document.body.appendChild(notification);

    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);

    // Remove after 3 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}
</script>
@endpush

@endsection
