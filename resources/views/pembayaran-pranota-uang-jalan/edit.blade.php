@extends('layouts.app')

@section('title', 'Edit Pembayaran Pranota Uang Jalan')
@section('page_title', 'Edit Pembayaran Pranota Uang Jalan')

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
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <strong>Berhasil!</strong> {{ session('success') }}
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-3 p-4 rounded-lg bg-red-50 border border-red-200 text-red-800 text-sm">
                <div class="flex items-start">
                    <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="flex-1">
                        <div class="font-medium mb-1">Gagal Menyimpan Pembayaran!</div>
                        <div>{{ session('error') }}</div>
                    </div>
                </div>
            </div>
        @endif
        @if($errors->any())
            <div class="mb-3 p-3 rounded-lg bg-yellow-50 border border-yellow-200 text-yellow-800 text-sm">
                <div class="flex items-start">
                    <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="flex-1">
                        <div class="font-medium mb-1">Kesalahan Validasi!</div>
                        <ul class="list-disc list-inside space-y-1 text-xs">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-gray-50 rounded-lg p-3 mb-3 border border-gray-200">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-2">
                <h3 class="text-sm font-semibold text-gray-800">Filter Pranota Uang Jalan</h3>
                <div class="flex flex-wrap items-end gap-2">
                    <div><label for="start_date" class="{{ $labelClasses }}">Dari</label><input type="date" id="start_date" class="{{ $inputClasses }}"></div>
                    <div><label for="end_date" class="{{ $labelClasses }}">Sampai</label><input type="date" id="end_date" class="{{ $inputClasses }}"></div>
                    <button type="button" id="filterPranota" class="py-2 px-3 text-xs rounded-md bg-blue-600 text-white">Cari</button>
                    <button type="button" id="resetFilterPranota" class="py-2 px-3 text-xs rounded-md border border-gray-300 bg-white">Reset</button>
                </div>
            </div>
        </div>

        <form action="{{ route('pembayaran-pranota-uang-jalan.update', $pembayaranPranotaUangJalan->id) }}" method="POST" enctype="multipart/form-data" id="pembayaranForm" class="space-y-3">
            @csrf
            @method('PUT')

            <!-- Data pembayaran -->
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-3">
                    <!-- Info Pembayaran -->
                    <div class="lg:col-span-2">
                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                            <h4 class="text-sm font-semibold text-gray-800 mb-2">Data Pembayaran</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                                <div>
                                    <label for="nomor_pembayaran" class="{{ $labelClasses }}">Nomor Pembayaran</label>
                                    <input type="text" name="nomor_pembayaran" id="nomor_pembayaran"
                                        value="{{ $pembayaranPranotaUangJalan->nomor_pembayaran }}"
                                        class="{{ $readonlyInputClasses }}" readonly>
                                </div>
                                <div>
                                    <label for="nomor_accurate" class="{{ $labelClasses }}">Nomor Accurate</label>
                                    <input type="text" name="nomor_accurate" id="nomor_accurate"
                                        value="{{ old('nomor_accurate', $pembayaranPranotaUangJalan->nomor_accurate) }}"
                                        class="{{ $inputClasses }}" placeholder="Masukkan nomor accurate">
                                </div>
                                 <div>
                                    <label for="tanggal_kas" class="{{ $labelClasses }}">Tanggal Kas <span class="text-red-500">*</span></label>
                                    <input type="date" name="tanggal_kas" id="tanggal_kas"
                                        value="{{ old('tanggal_pembayaran', optional($pembayaranPranotaUangJalan->tanggal_pembayaran)->format('Y-m-d')) }}"
                                        class="{{ $inputClasses }}" required>
                                    <input type="hidden" name="tanggal_pembayaran" id="tanggal_pembayaran"
                                        value="{{ old('tanggal_pembayaran', optional($pembayaranPranotaUangJalan->tanggal_pembayaran)->format('Y-m-d')) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                <!-- Bank & Transaksi -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">Bank & Transaksi (Double Book Accounting)</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <div class="relative">
                                <label for="bank" class="{{ $labelClasses }}">Pilih Bank <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="text" id="bankSearch" placeholder="Cari bank..." 
                                        class="mt-1 block w-full rounded-md border-gray-300 bg-white shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-sm p-2 transition-colors pr-8"
                                        autocomplete="off" value="{{ old('bank', $pembayaranPranotaUangJalan->bank) }}">
                                    <svg class="absolute right-2 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <select name="bank" id="bank" class="hidden" required>
                                    <option value="">-- Pilih Bank --</option>
                                    @if(isset($akunCoa))
                                        @foreach($akunCoa as $akun)
                                            <option value="{{ $akun->nama_akun }}" data-kode="{{ $akun->kode_nomor ?? '000' }}" {{ old('bank', $pembayaranPranotaUangJalan->bank) == $akun->nama_akun ? 'selected' : '' }}>
                                                {{ $akun->nama_akun }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <div id="bankDropdown" class="hidden absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto">
                                    <div id="bankOptions" class="py-1"></div>
                                    <div id="noBankResults" class="hidden px-3 py-2 text-xs text-gray-500 text-center">Tidak ada bank yang sesuai</div>
                                </div>
                            </div>
                            <div>
                                <label for="jenis_transaksi" class="{{ $labelClasses }}">Jenis Transaksi <span class="text-red-500">*</span></label>
                                <select name="jenis_transaksi" id="jenis_transaksi" class="{{ $inputClasses }}" required>
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="Debit" {{ old('jenis_transaksi', $pembayaranPranotaUangJalan->jenis_transaksi) == 'Debit' ? 'selected' : '' }}>Debit (Bank bertambah, Biaya berkurang)</option>
                                    <option value="Kredit" {{ old('jenis_transaksi', $pembayaranPranotaUangJalan->jenis_transaksi) == 'Kredit' ? 'selected' : '' }}>Kredit (Biaya bertambah, Bank berkurang)</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-2 p-2 bg-blue-50 border border-blue-200 rounded text-xs text-blue-800">
                            <div class="flex items-start">
                                <svg class="w-3 h-3 mr-1 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <strong>Jurnal Akuntansi:</strong><br>
                                    • <strong>Debit:</strong> Bank bertambah (Dr), Biaya berkurang (Cr)<br>
                                    • <strong>Kredit:</strong> Biaya bertambah (Dr), Bank berkurang (Cr)
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @php
                $selectedPranotaIds = old('pranota_uang_jalan_ids', $pembayaranPranotaUangJalan->pranotaUangJalans->modelKeys());
                $savedAkunRows = collect(old('akun_items', $pembayaranPranotaUangJalan->akunItems->map(fn ($item) => [
                    'pranota_uang_jalan_id' => $item->pranota_uang_jalan_id,
                    'akun_coa_id' => $item->akun_coa_id,
                    'nominal' => $item->nominal,
                    'memo' => $item->memo,
                ])->all()))->values();
                $akunRows = $pranotaUangJalans->filter(fn ($pranota) => in_array($pranota->id, $selectedPranotaIds))
                    ->values()->map(function ($pranota, $index) use ($savedAkunRows, $defaultAkunBiayaId, $pembayaranPranotaUangJalan) {
                        $saved = $savedAkunRows->first(fn ($item) => (int) ($item['pranota_uang_jalan_id'] ?? 0) === $pranota->id)
                            ?? $savedAkunRows->filter(fn ($item) => empty($item['pranota_uang_jalan_id']))->values()->get($index);
                        $linked = $pembayaranPranotaUangJalan->pranotaUangJalans->firstWhere('id', $pranota->id);
                        return [
                            'pranota_uang_jalan_id' => $pranota->id,
                            'nomor_pranota' => $pranota->nomor_pranota,
                            'akun_coa_id' => $saved['akun_coa_id'] ?? $defaultAkunBiayaId,
                            'nominal' => $saved['nominal'] ?? ($linked ? $linked->pivot->subtotal : $pranota->total_for_payment),
                            'memo' => filled($saved['memo'] ?? null) ? $saved['memo'] : 'Uang jalan No. Pranota : '.$pranota->nomor_pranota,
                        ];
                    });
            @endphp
            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                <div class="flex items-center justify-between gap-3 mb-2">
                    <div>
                        <h4 class="text-sm font-semibold text-gray-800">Rincian Akun Biaya</h4>
                        <p class="text-xs text-gray-500">Satu akun untuk setiap pranota. Nominal mengikuti subtotal pranota; penyesuaian masuk ke baris pertama.</p>
                    </div>
                </div>
                <div id="akun-items-list" class="space-y-3" data-default-akun-id="{{ $defaultAkunBiayaId }}">
                    @foreach($akunRows as $index => $row)
                        <div class="akun-item-row rounded-md border border-gray-200 bg-white p-3" data-pranota-id="{{ $row['pranota_uang_jalan_id'] }}">
                            <div class="text-xs font-semibold text-gray-700">Pranota: <span class="akun-item-pranota-number">{{ $row['nomor_pranota'] }}</span></div>
                            <input type="hidden" name="akun_items[{{ $index }}][pranota_uang_jalan_id]" class="akun-item-pranota-id" value="{{ $row['pranota_uang_jalan_id'] }}">
                            <div class="flex flex-wrap items-center gap-2">
                                <select name="akun_items[{{ $index }}][akun_coa_id]" class="akun-item-select {{ $inputClasses }} flex-1 min-w-48" required>
                                    <option value="">-- Pilih Akun Biaya --</option>
                                    @foreach($akunBiaya as $akun)
                                        <option value="{{ $akun->id }}" @selected((string) ($row['akun_coa_id'] ?? '') === (string) $akun->id)>{{ $akun->nomor_akun ? $akun->nomor_akun.' - ' : '' }}{{ $akun->nama_akun }}</option>
                                    @endforeach
                                </select>
                                <input type="number" name="akun_items[{{ $index }}][nominal]" class="akun-item-nominal {{ $readonlyInputClasses }} w-40" min="0.01" step="0.01" value="{{ $row['nominal'] ?? '' }}" readonly required>
                            </div>
                            <textarea name="akun_items[{{ $index }}][memo]" class="akun-item-memo {{ $inputClasses }} mt-2" rows="2" maxlength="2000" placeholder="Memo/catatan akun ini (opsional)">{{ $row['memo'] ?? '' }}</textarea>
                        </div>
                    @endforeach
                </div>
                <div class="mt-2 text-xs text-gray-700">Total akun biaya: <strong id="akun-items-total">Rp 0</strong></div>
                @error('akun_items')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                <div class="mt-3">
                    <label for="memo_biaya" class="{{ $labelClasses }}">Memo Biaya</label>
                    <textarea name="memo_biaya" id="memo_biaya" class="{{ $inputClasses }}" rows="2" maxlength="2000" placeholder="Memo biaya untuk seluruh pembayaran (opsional)">{{ old('memo_biaya', $pembayaranPranotaUangJalan->memo_biaya) }}</textarea>
                </div>
            </div>

            <!-- Pilih Pranota Uang Jalan -->
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-3 py-2 border-b border-gray-200">
                    <div class="flex items-center justify-between gap-2">
                        <h4 class="text-sm font-semibold text-gray-800">Pilih Pranota Uang Jalan</h4>
                        <input type="text" id="searchPranota" placeholder="Cari nomor pranota, supir, tujuan..." class="px-3 py-1.5 text-xs border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 w-64">
                    </div>
                </div>
                <div class="overflow-x-auto max-h-60">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0 z-20">
                            <tr>
                                <th class="px-2 py-2 text-left"><input type="checkbox" id="select-all" class="h-3 w-3 text-indigo-600 border-gray-300 rounded"></th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">No. Pranota</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Supir</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tujuan Ambil</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jumlah Item</th>
                                <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total Uang Jalan</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody id="pranotaTableBody" class="bg-white divide-y divide-gray-200">
                            @php $selectedPranotaIds = old('pranota_uang_jalan_ids', $pembayaranPranotaUangJalan->pranotaUangJalans->modelKeys()); @endphp
                            @forelse($pranotaUangJalans as $pranota)
                                @php
                                    $isSelected = in_array($pranota->id, $selectedPranotaIds);
                                    $linked = $pembayaranPranotaUangJalan->pranotaUangJalans->firstWhere('id', $pranota->id);
                                    $subtotal = $linked ? $linked->pivot->subtotal : $pranota->total_for_payment;
                                    $supirs = $pranota->uangJalans->map(fn ($item) => $item->suratJalan?->supirKaryawan?->nama_panggilan ?? $item->suratJalan?->supir)->filter()->unique();
                                    $tujuans = $pranota->uangJalans->map(fn ($item) => $item->suratJalan?->tujuanPengambilanRelation?->ke ?? $item->suratJalan?->tujuan_pengambilan)->filter()->unique();
                                    $types = $pranota->uangJalans->pluck('suratJalan.tipe_kontainer')->filter()->unique();
                                @endphp
                                <tr class="pranota-row hover:bg-gray-50" data-date="{{ $pranota->tanggal_pranota ? \Carbon\Carbon::parse($pranota->tanggal_pranota)->format('Y-m-d') : '' }}">
                                    <td class="px-2 py-2"><input type="checkbox" name="pranota_uang_jalan_ids[]" value="{{ $pranota->id }}" data-total="{{ $subtotal }}" data-number="{{ $pranota->nomor_pranota }}" class="pranota-checkbox h-3 w-3 text-indigo-600 border-gray-300 rounded" @checked($isSelected)></td>
                                    <td class="px-2 py-2 text-xs font-medium"><a href="{{ route('pranota-uang-jalan.show', $pranota->id) }}" target="_blank" class="text-indigo-600 hover:underline">{{ $pranota->nomor_pranota }}</a></td>
                                    <td class="px-2 py-2 text-xs">{{ $pranota->tanggal_pranota ? \Carbon\Carbon::parse($pranota->tanggal_pranota)->format('d/M/Y') : '-' }}</td>
                                    <td class="px-2 py-2 text-xs">{{ $supirs->take(2)->implode(', ') ?: '-' }}</td>
                                    <td class="px-2 py-2 text-xs">{{ $tujuans->take(2)->implode(', ') ?: '-' }}</td>
                                    <td class="px-2 py-2 text-xs">{{ $types->take(2)->implode(', ') ?: '-' }}</td>
                                    <td class="px-2 py-2 text-xs">{{ $pranota->uangJalans->count() }} item</td>
                                    <td class="px-2 py-2 text-right text-xs font-semibold">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                                    <td class="px-2 py-2 text-xs">{{ $linked ? 'Sudah Bayar' : 'Belum Bayar' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="px-2 py-4 text-center text-xs text-gray-500">Tidak ada pranota tersedia.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pembayaran & Penyesuaian -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-3">
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-800 mb-2">Total Pembayaran</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                        <div>
                            <label for="total_pembayaran" class="{{ $labelClasses }}">Total Tagihan <span class="text-red-500">*</span></label>
                            <input type="number" name="total_pembayaran" id="total_pembayaran" step="0.01"
                                value="{{ old('total_pembayaran', $pembayaranPranotaUangJalan->total_pembayaran) }}"
                                class="{{ $readonlyInputClasses }}" readonly required>
                        </div>
                        <div>
                            <label for="total_tagihan_penyesuaian" class="{{ $labelClasses }}">Penyesuaian</label>
                            <input type="number" name="total_tagihan_penyesuaian" id="total_tagihan_penyesuaian" step="0.01"
                                value="{{ old('total_tagihan_penyesuaian', $pembayaranPranotaUangJalan->total_tagihan_penyesuaian ?? 0) }}"
                                class="{{ $inputClasses }}" placeholder="0">
                        </div>
                        <div>
                            <label for="total_tagihan_setelah_penyesuaian" class="{{ $labelClasses }}">Total Akhir</label>
                            <input type="number" name="total_tagihan_setelah_penyesuaian" id="total_tagihan_setelah_penyesuaian" step="0.01"
                                value="{{ old('total_tagihan_setelah_penyesuaian', $pembayaranPranotaUangJalan->total_tagihan_setelah_penyesuaian) }}"
                                class="{{ $readonlyInputClasses }}" readonly>
                        </div>
                    </div>
                    </div>
                </div>
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-800 mb-2">Informasi Tambahan</h4>
                    <div class="space-y-2">
                        <div>
                            <label for="alasan_penyesuaian" class="{{ $labelClasses }}">Alasan Penyesuaian</label>
                            <textarea name="alasan_penyesuaian" id="alasan_penyesuaian" rows="2"
                                class="{{ $inputClasses }}" placeholder="Jelaskan alasan penyesuaian">{{ old('alasan_penyesuaian', $pembayaranPranotaUangJalan->alasan_penyesuaian) }}</textarea>
                        </div>
                        <div>
                            <label for="keterangan" class="{{ $labelClasses }}">Keterangan</label>
                            <textarea name="keterangan" id="keterangan" rows="2"
                                class="{{ $inputClasses }}" placeholder="Tambahkan keterangan">{{ old('keterangan', $pembayaranPranotaUangJalan->keterangan) }}</textarea>
                        </div>
                    </div>
                    </div>
                </div>
            </div>

            <!-- Bukti Pembayaran -->
            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                <h4 class="text-sm font-semibold text-gray-800 mb-2">Bukti Pembayaran</h4>
                <div>
                    <label for="bukti_pembayaran" class="{{ $labelClasses }}">Upload Bukti Pembayaran (Opsional)</label>
                    <input type="file" name="bukti_pembayaran" id="bukti_pembayaran" accept="image/*,application/pdf"
                        class="{{ $inputClasses }}">
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, JPEG, PNG, PDF. Maksimal 2MB.</p>
                    @if($pembayaranPranotaUangJalan->bukti_pembayaran)
                        <div class="mt-2">
                            <span class="text-xs text-gray-600">Bukti saat ini:</span>
                            <a href="{{ Storage::url($pembayaranPranotaUangJalan->bukti_pembayaran) }}" target="_blank" class="text-xs text-blue-600 hover:underline">Lihat Bukti</a>
                        </div>
                    @endif
                </div>
            </div>
            <!-- Action Buttons -->
            <div class="flex justify-end gap-2 pt-3 border-t border-gray-200">
                <a href="{{ route('pembayaran-pranota-uang-jalan.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Batal
                </a>
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Update Pembayaran
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
                // Bank search functionality
                const bankSearch = document.getElementById('bankSearch');
                const bankSelect = document.getElementById('bank');
                const bankDropdown = document.getElementById('bankDropdown');
                const bankOptions = document.getElementById('bankOptions');
                const noBankResults = document.getElementById('noBankResults');

                function populateBankOptions(searchTerm = '') {
                    const options = Array.from(bankSelect.options).filter(opt => opt.value !== '');
                    const filtered = options.filter(opt => 
                        opt.text.toLowerCase().includes(searchTerm.toLowerCase())
                    );

                    bankOptions.innerHTML = '';
                    
                    if (filtered.length === 0) {
                        noBankResults.classList.remove('hidden');
                        bankOptions.classList.add('hidden');
                    } else {
                        noBankResults.classList.add('hidden');
                        bankOptions.classList.remove('hidden');
                    
                        filtered.forEach(opt => {
                            const div = document.createElement('div');
                            div.className = 'px-3 py-2 hover:bg-indigo-50 cursor-pointer text-sm';
                            div.textContent = opt.text;
                            div.dataset.value = opt.value;
                            
                            div.addEventListener('click', function() {
                                bankSelect.value = this.dataset.value;
                                bankSearch.value = this.textContent;
                                bankDropdown.classList.add('hidden');
                            });
                            
                            bankOptions.appendChild(div);
                        });
                    }
                    
                    bankDropdown.classList.remove('hidden');
                }

                bankSearch.addEventListener('focus', function() {
                    populateBankOptions(this.value);
                });

                bankSearch.addEventListener('input', function() {
                    populateBankOptions(this.value);
                });

                document.addEventListener('click', function(e) {
                    if (!bankSearch.contains(e.target) && !bankDropdown.contains(e.target)) {
                        bankDropdown.classList.add('hidden');
                    }
                });

                // Set initial bank search value
                if (bankSelect.value) {
                    const selectedOption = bankSelect.options[bankSelect.selectedIndex];
                    if (selectedOption) {
                        bankSearch.value = selectedOption.text;
                    }
                }


                // Calculate total after adjustment
                const totalPembayaran = document.getElementById('total_pembayaran');
                const totalPenyesuaian = document.getElementById('total_tagihan_penyesuaian');
                const totalSetelah = document.getElementById('total_tagihan_setelah_penyesuaian');
                const checkboxes = Array.from(document.querySelectorAll('.pranota-checkbox'));
                const selectAll = document.getElementById('select-all');
                const searchPranota = document.getElementById('searchPranota');
                const tanggalKas = document.getElementById('tanggal_kas');
                const tanggalPembayaran = document.getElementById('tanggal_pembayaran');
                const akunList = document.getElementById('akun-items-list');
                const akunTemplate = akunList.querySelector('.akun-item-row').cloneNode(true);
                const akunRows = new Map([...akunList.querySelectorAll('.akun-item-row')].map(row => [row.dataset.pranotaId, row]));
                const memoBiaya = document.getElementById('memo_biaya');
                let generatedMemo = '';

                function initializeAkunSelect(root) {
                    if (window.jQuery && jQuery.fn.select2) {
                        jQuery(root).find('.akun-item-select').select2({ width: '100%' });
                    }
                }

                function updateAkunRows() {
                    const selected = checkboxes.filter(checkbox => checkbox.checked);
                    [...akunList.querySelectorAll('.akun-item-row')].forEach(row => {
                        if (selected.some(checkbox => checkbox.value === row.dataset.pranotaId)) return;
                        const select = row.querySelector('.akun-item-select');
                        if (window.jQuery && jQuery(select).hasClass('select2-hidden-accessible')) {
                            jQuery(select).select2('destroy');
                        }
                        row.remove();
                    });
                    let sum = 0;
                    const adjustment = Number(totalPenyesuaian.value) || 0;
                    selected.forEach((checkbox, index) => {
                        let row = akunRows.get(checkbox.value);
                        if (!row) {
                            row = akunTemplate.cloneNode(true);
                            row.dataset.pranotaId = checkbox.value;
                            row.querySelector('.akun-item-select').value = akunList.dataset.defaultAkunId;
                            row.querySelector('.akun-item-memo').value = `Uang jalan No. Pranota : ${checkbox.dataset.number}`;
                            akunRows.set(checkbox.value, row);
                        }
                        row.querySelector('.akun-item-pranota-number').textContent = checkbox.dataset.number;
                        row.querySelector('.akun-item-pranota-id').value = checkbox.value;
                        row.querySelector('.akun-item-pranota-id').name = `akun_items[${index}][pranota_uang_jalan_id]`;
                        row.querySelector('.akun-item-select').name = `akun_items[${index}][akun_coa_id]`;
                        row.querySelector('.akun-item-nominal').name = `akun_items[${index}][nominal]`;
                        row.querySelector('.akun-item-memo').name = `akun_items[${index}][memo]`;
                        const nominal = Number(checkbox.dataset.total) + (index === 0 ? adjustment : 0);
                        row.querySelector('.akun-item-nominal').value = nominal.toFixed(2);
                        sum += nominal;
                        if (!row.isConnected) {
                            akunList.appendChild(row);
                            initializeAkunSelect(row);
                        } else {
                            akunList.appendChild(row);
                        }
                    });
                    const nextMemo = `Uang jalan No. Pranota : ${selected.map(checkbox => checkbox.dataset.number).join(' & ')}`;
                    if (!memoBiaya.value.trim() || memoBiaya.value === generatedMemo) memoBiaya.value = selected.length ? nextMemo : '';
                    generatedMemo = nextMemo;
                    const display = document.getElementById('akun-items-total');
                    display.textContent = 'Rp ' + sum.toLocaleString('id-ID');
                    display.classList.toggle('text-red-600', Math.abs(sum - (Number(totalSetelah.value) || 0)) > 0.01);
                }
                initializeAkunSelect(akunList);

                function updateTotalSetelah() {
                    const pembayaran = checkboxes.reduce((sum, checkbox) =>
                        sum + (checkbox.checked ? parseFloat(checkbox.dataset.total) || 0 : 0), 0);
                    const penyesuaian = parseFloat(totalPenyesuaian.value) || 0;
                    totalPembayaran.value = pembayaran;
                    totalSetelah.value = pembayaran + penyesuaian;
                    selectAll.checked = checkboxes.length > 0 && checkboxes.every(checkbox => checkbox.checked);
                    updateAkunRows();
                }

                checkboxes.forEach(checkbox => checkbox.addEventListener('change', updateTotalSetelah));
                selectAll.addEventListener('change', () => {
                    checkboxes.forEach(checkbox => { checkbox.checked = selectAll.checked; });
                    updateTotalSetelah();
                });
                totalPenyesuaian.addEventListener('input', updateTotalSetelah);
                searchPranota.addEventListener('input', () => {
                    filterRows();
                });
                function filterRows() {
                    const term = searchPranota.value.toLowerCase().trim();
                    const start = document.getElementById('start_date').value;
                    const end = document.getElementById('end_date').value;
                    document.querySelectorAll('.pranota-row').forEach(row => {
                        const matchesText = row.textContent.toLowerCase().includes(term);
                        const matchesDate = (!start || row.dataset.date >= start) && (!end || row.dataset.date <= end);
                        row.classList.toggle('hidden', !matchesText || !matchesDate);
                    });
                }
                document.getElementById('filterPranota').addEventListener('click', filterRows);
                document.getElementById('resetFilterPranota').addEventListener('click', () => {
                    document.getElementById('start_date').value = '';
                    document.getElementById('end_date').value = '';
                    searchPranota.value = '';
                    filterRows();
                });
                tanggalKas.addEventListener('change', () => { tanggalPembayaran.value = tanggalKas.value; });
                document.getElementById('pembayaranForm').addEventListener('submit', event => {
                    tanggalPembayaran.value = tanggalKas.value;
                    if (!checkboxes.some(checkbox => checkbox.checked)) {
                        event.preventDefault();
                        alert('Pilih minimal satu pranota uang jalan.');
                    }
                    const rows = [...akunList.querySelectorAll('.akun-item-row')];
                    const sum = rows.reduce((total, row) => total + (Number(row.querySelector('.akun-item-nominal').value) || 0), 0);
                    if (Math.abs(sum - (Number(totalSetelah.value) || 0)) > 0.01
                        || rows.length !== checkboxes.filter(checkbox => checkbox.checked).length
                        || rows.some(row => !row.querySelector('.akun-item-select').value || Number(row.querySelector('.akun-item-nominal').value) <= 0)) {
                        event.preventDefault();
                        alert('Periksa rincian akun: setiap pranota perlu satu akun dengan nominal positif dan jumlah yang sesuai Total Akhir.');
                    }
                });
                updateTotalSetelah();
        });
    </script>
@endsection
