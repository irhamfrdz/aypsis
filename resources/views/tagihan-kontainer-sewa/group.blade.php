@extends('layouts.app')

@section('title', 'Detail Grup Tagihan')
@section('page_title', 'Detail Grup Tagihan')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4 text-gray-800">Detail Grup: {{ $vendor }} - {{ $tanggal }}</h2>
    @if(!empty($permohonans) && !empty($permohonans->first()))
        @php
            $sampleTagihan = \DB::table('tagihan_kontainer_sewa')->where('vendor', $vendor)->whereDate('tanggal_harga_awal', $tanggal)->first();
            $periodeValue = $sampleTagihan->periode ?? null;
        @endphp
        @if($periodeValue)
            <div class="text-sm text-gray-600 mb-2">Periode: {{ $periodeValue }}</div>
        @endif
    @endif

    <div class="mb-4">
        @if(Route::has('tagihan-kontainer-sewa.index'))
            <a href="{{ route('tagihan-kontainer-sewa.index') }}" class="inline-block bg-gray-200 text-gray-800 py-2 px-4 rounded-md">&larr; Kembali ke daftar</a>
        @else
            <a href="#" class="inline-block bg-gray-200 text-gray-400 py-2 px-4 rounded-md">&larr; Kembali ke daftar</a>
        @endif
    </div>

    @php
        $permMap = [];
        foreach ($permohonans as $p) {
            $permMap[$p->id] = [
                'nomor_memo' => $p->nomor_memo ?? ('#' . $p->id),
                'tanggal_memo' => isset($p->tanggal_memo) ? (is_string($p->tanggal_memo) ? $p->tanggal_memo : $p->tanggal_memo) : null,
                'approved_at' => isset($p->updated_at) ? $p->updated_at : null,
                'ukuran' => $p->ukuran ?? null,
            ];
        }


        $flatContainers = [];
        $seenIds = [];
        foreach ($kontainers as $permId => $list) {
            foreach ($list as $k) {
                if (in_array($k->id, $seenIds)) continue;
                $seenIds[] = $k->id;
                $flatContainers[] = (object) [
                    'permohonan_id' => $permId,
                    'kontainer' => $k,
                ];
            }
        }

        // Compute returned containers for use after the main detail table
        $physicalReturned = [];
        foreach ($flatContainers as $entry) {
            $k = $entry->kontainer;
            $permId = $entry->permohonan_id;
            $status = isset($k->status) ? strtolower($k->status) : null;
            $isReturned = false;
            if ($status === 'dikembalikan') $isReturned = true;

            if (!$isReturned && !empty($k->tanggal_selesai_sewa)) {
                try {
                    $date = \Carbon\Carbon::parse($k->tanggal_selesai_sewa)->startOfDay();
                    $today = \Carbon\Carbon::today();
                    if ($date->lte($today)) $isReturned = true;
                } catch (\Exception $e) {
                    // ignore parse errors and treat as not returned
                }
            }

            if (!$isReturned) continue;

            $isPaid = false;
            if ($permId && isset($permPaid[$permId])) {
                $isPaid = (bool) $permPaid[$permId];
            }

            $physicalReturned[] = (object) [
                'permohonan_id' => $permId,
                'kontainer' => $k,
                'is_paid' => $isPaid,
            ];
        }

        $returnedMergedMap = [];

        $computeSerial = function($k) {
            if (!empty($k->nomor_seri_gabungan)) return trim($k->nomor_seri_gabungan);
            return trim((($k->awalan_kontainer ?? '') . ($k->nomor_seri_kontainer ?? '') . ($k->akhiran_kontainer ?? '')));
        };

        foreach ($physicalReturned as $entry) {
            $k = $entry->kontainer;
            $serial = $computeSerial($k) ?: ('id:' . ($k->id ?? ''));
            $returnedMergedMap[$serial] = $entry;
        }

    // Note: removed merging of containers from the previous period into
    // the returned list. We want this page to show only containers
    // associated with the current group's permohonans.

        $returnedFinal = array_values($returnedMergedMap);
    @endphp

    {{-- Kontainer yang sudah dikembalikan akan ditampilkan setelah detail Kontainer Grup (dipindahkan ke bawah) --}}

    {{-- Summary: total kontainer for this group (distinct) --}}
    @php
        $controllerTotal = $distinctContainerCount ?? count($flatContainers);

        $physicalReturnedCount = 0;
        foreach ($flatContainers as $entry) {
            $k = $entry->kontainer;
            $status = isset($k->status) ? strtolower($k->status) : null;
            $isReturned = false;
            if ($status === 'dikembalikan') $isReturned = true;
            if (!$isReturned && !empty($k->tanggal_selesai_sewa)) {
                try {
                    $date = \Carbon\Carbon::parse($k->tanggal_selesai_sewa)->startOfDay();
                    $today = \Carbon\Carbon::today();
                    if ($date->lte($today)) $isReturned = true;
                } catch (\Exception $e) {
                }
            }
            if ($isReturned) $physicalReturnedCount++;
        }

        $returnedSerialCount = isset($returnedMergedMap) ? count($returnedMergedMap) : 0;

        $paidReturnedCount = 0;
        if (isset($returnedFinal) && is_array($returnedFinal)) {
            foreach ($returnedFinal as $r) {
                if (isset($r->is_paid) && $r->is_paid) $paidReturnedCount++;
            }
        }

        $totalContainers = max(0, $controllerTotal - $returnedSerialCount);
        $unpaidExcludedCount = max(0, $returnedSerialCount - $paidReturnedCount);
    @endphp

    <div class="mb-4 grid grid-cols-1 gap-4">
        <div class="bg-white p-4 rounded shadow">
            <div class="text-sm text-gray-500">Total Kontainer</div>
            <div class="text-2xl font-bold">{{ $totalContainers }}</div>
            <div class="text-xs text-gray-600 mt-1">
                (Total grup: {{ $controllerTotal }} — sudah dikembalikan dan dibayar: {{ $paidReturnedCount }})
            </div>
            @if($unpaidExcludedCount > 0)
                <div class="mt-2 text-sm text-yellow-700 bg-yellow-50 border border-yellow-100 p-2 rounded">
                    Terdapat {{ $unpaidExcludedCount }} kontainer yang sudah dikembalikan secara fisik tetapi belum dicatat sebagai dibayar, sehingga tidak mengurangi total sampai dibayar.
                </div>
            @endif
        </div>
    </div>

    <h3 class="text-xl font-semibold mt-6 mb-3 text-gray-800">Detail Kontainer Grup</h3>
    @if(isset($groupTotalHarga))
        <div class="mb-4">
            <div class="text-sm text-gray-500">Total Harga Terhitung</div>
            <div class="text-2xl font-bold">Rp {{ number_format($groupTotalHarga ?? 0, 2, ',', '.') }}</div>
        </div>
    @endif

    <div class="overflow-x-auto shadow-md sm:rounded-lg mb-6">
        <table class="min-w-full bg-white divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kontainer (Nomor)</th>
                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ukuran (ft)</th>
                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permohonan</th>
                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pengambilan</th>
                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Approval</th>
                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Satuan</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @php
                    $displayIndex = 0;
                    $returnedSerialKeys = isset($returnedMergedMap) ? array_keys($returnedMergedMap) : [];
                @endphp
                @forelse($flatContainers as $entry)
                    @php
                        $k = $entry->kontainer;
                        $permId = $entry->permohonan_id;
                        $serial = !empty($k->nomor_seri_gabungan) ? trim($k->nomor_seri_gabungan) : trim((($k->awalan_kontainer ?? '') . ($k->nomor_seri_kontainer ?? '') . ($k->akhiran_kontainer ?? '')));
                        if ($serial !== '' && in_array($serial, $returnedSerialKeys)) {
                            continue;
                        }
                        $displayIndex++;
                        $permInfo = $permMap[$permId] ?? null;
                        $fullNumber = trim((($k->awalan_kontainer ?? '') . ($k->nomor_seri_kontainer ?? '') . ($k->akhiran_kontainer ?? '')));
                        $ukuran = $k->ukuran ?? ($permInfo['ukuran'] ?? '-');
                        $nomorMemo = $permInfo['nomor_memo'] ?? ('#' . $permId);
                        $checkpointRaw = $permCheckpointMap[$permId] ?? null;
                        $tanggalMemo = $checkpointRaw ? (method_exists($checkpointRaw, 'format') ? $checkpointRaw->format('d/m/Y') : \Carbon\Carbon::parse($checkpointRaw)->format('d/m/Y')) : (isset($permInfo['tanggal_memo']) && $permInfo['tanggal_memo'] ? (method_exists($permInfo['tanggal_memo'], 'format') ? $permInfo['tanggal_memo']->format('d/m/Y') : \Carbon\Carbon::parse($permInfo['tanggal_memo'])->format('d/m/Y')) : '-');
                        $approvedAt = $permInfo['approved_at'] ? (method_exists($permInfo['approved_at'], 'format') ? $permInfo['approved_at']->format('d/m/Y') : \Carbon\Carbon::parse($permInfo['approved_at'])->format('d/m/Y')) : '-';
                        $unit = isset($perContainerPrices[$k->id]) ? $perContainerPrices[$k->id] : null;
                        $unitRaw = $unit !== null ? $unit : (isset($k->harga_satuan) ? $k->harga_satuan : '');
                        $unitDisplay = $unitRaw !== '' && $unitRaw !== null ? 'Rp ' . number_format($unitRaw, 2, ',', '.') : '-';
                    @endphp
                    <tr>
                        <td class="py-4 px-6">{{ $displayIndex }}</td>
                        <td class="py-4 px-6">{{ $fullNumber ?: '-' }}</td>
                        <td class="py-4 px-6">{{ $ukuran }}</td>
                        <td class="py-4 px-6">{{ $nomorMemo }}</td>
                        <td class="py-4 px-6">{{ $tanggalMemo }}</td>
                        <td class="py-4 px-6">{{ $approvedAt }}</td>
                        <td class="py-4 px-6" data-kontainer-id="{{ $k->id }}" data-kontainer-serial="{{ $serial }}" @if($unitRaw !== '' && $unitRaw !== null) data-original-price="{{ $unitRaw }}" @endif>{{ $unitDisplay }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-4 px-6 text-center text-gray-500">Tidak ada kontainer untuk grup ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <h3 class="text-xl font-semibold mt-8 mb-3 text-green-800">Kontainer Dikembalikan</h3>
    <div class="mb-4 flex items-center justify-between">
        <div class="text-sm text-gray-600">Pilih kontainer yang akan dimasukkan ke Pranota</div>
        <div class="flex items-center space-x-3">
            <span id="selected-count" class="text-sm text-gray-700">0 dipilih</span>
            @if(Route::has('pranota-tagihan-kontainer.store'))
                <form id="pranota-create-form" action="{{ route('pranota-tagihan-kontainer.store') }}" method="POST">
            @else
                <form id="pranota-create-form" action="#" method="POST">
            @endif
                @csrf
                <input type="hidden" name="kontainer_ids" id="pranota-kontainer-ids" />
                <input type="hidden" name="vendor" value="{{ $vendor }}" />
                {{-- ensure the group tanggal is sent so pranota belongs to the same group date and doesn't create a new group row --}}
                <input type="hidden" name="tanggal" value="{{ $tanggal }}" />
                    {{-- pass source group and periode from the Tagihan Kontainer Sewa menu so pranota can reference them --}}
                    @php
                        // Prefer an explicit group value passed from the controller (e.g. $group).
                        // Fall back to a sample tagihan lookup when the controller didn't provide it.
                        $sampleTagihan = isset($sampleTagihan) ? $sampleTagihan : \DB::table('tagihan_kontainer_sewa')->where('vendor', $vendor)->whereDate('tanggal_harga_awal', $tanggal)->first();
                        $viewGroup = isset($group) && $group ? $group : ($sampleTagihan->group ?? null);
                        $viewPeriode = $periodeValue ?? ($sampleTagihan->periode ?? null);
                    @endphp
                    @if(!empty($viewGroup))
                        <input type="hidden" name="source_group" value="{{ $viewGroup }}" />
                    @endif
                    @if(!empty($viewPeriode))
                        <input type="hidden" name="source_periode" value="{{ $viewPeriode }}" />
                    @endif
                <button id="btn-create-pranota" type="submit" class="bg-green-600 text-white px-3 py-1 rounded disabled:opacity-50" disabled>Buat Pranota</button>
            </form>
        </div>
    </div>
    <div class="overflow-x-auto shadow-md sm:rounded-lg mb-8 border border-green-100 bg-green-50">
        <table class="min-w-full bg-transparent divide-y divide-green-200">
            <thead class="bg-green-100">
                <tr>
                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <label for="chk-all-returned" class="inline-flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" id="chk-all-returned" class="form-checkbox h-5 w-5 text-green-600 border-gray-300 rounded focus:ring-2 focus:ring-green-200" />
                            <span class="text-xs font-medium text-gray-700">Pilih semua</span>
                        </label>
                    </th>
                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kontainer (Nomor)</th>
                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ukuran (ft)</th>
                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permohonan</th>
                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Selesai Sewa</th>
                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="py-3 px-6 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Biaya Sewa</th>
                    <th class="py-3 px-6 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Adjust Harga</th>
                    <th class="py-3 px-6 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">DPP Nilai Lain</th>
                    <th class="py-3 px-6 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">PPN (12%)</th>
                    <th class="py-3 px-6 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">PPH (2%)</th>
                    <th class="py-3 px-6 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Grand Total</th>
                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Pembayaran</th>
                </tr>
            </thead>
            <tbody class="bg-green-50 divide-y divide-green-200">
                @forelse($returnedFinal as $i => $entry)
                    @php
                        $k = $entry->kontainer;
                        $permId = $entry->permohonan_id;
                        $permInfo = $permMap[$permId] ?? null;
                        $fullNumber = trim((($k->awalan_kontainer ?? '') . ($k->nomor_seri_kontainer ?? '') . ($k->akhiran_kontainer ?? '')));
                        $ukuran = $k->ukuran ?? ($permInfo['ukuran'] ?? '-');
                        $nomorMemo = $permInfo['nomor_memo'] ?? ('#' . $permId);
                        $tanggalSelesai = isset($k->tanggal_selesai_sewa) ? (method_exists($k->tanggal_selesai_sewa, 'format') ? $k->tanggal_selesai_sewa->format('d/m/Y') : \Carbon\Carbon::parse($k->tanggal_selesai_sewa)->format('d/m/Y')) : '-';
                        $isPaid = isset($entry->is_paid) ? (bool) $entry->is_paid : (isset($permPaid[$permId]) ? (bool)$permPaid[$permId] : false);
                        $unitPrice = isset($perContainerPrices[$k->id]) ? $perContainerPrices[$k->id] : null;
                        $unitDisplay = $unitPrice !== null ? 'Rp ' . number_format($unitPrice, 2, ',', '.') : '-';
                        $adjustValue = isset($adjustedPrices) && isset($adjustedPrices[$k->id]) ? $adjustedPrices[$k->id] : '';
                        $syncSerial = !empty($k->nomor_seri_gabungan) ? trim($k->nomor_seri_gabungan) : trim((($k->awalan_kontainer ?? '') . ($k->nomor_seri_kontainer ?? '') . ($k->akhiran_kontainer ?? '')));
                    @endphp
                    @php
                        // ensure DPP/PPN/PPH values exist for this returned row
                        $dppValue = null;
                        $ppnValue = null;
                        $pphValue = null;
                        $basePrice = null;
                        if (isset($unitPrice) && $unitPrice !== null && is_numeric($unitPrice)) {
                            $basePrice = (float) $unitPrice;
                        } elseif (isset($k->harga_satuan) && $k->harga_satuan !== null && $k->harga_satuan !== '' && is_numeric($k->harga_satuan)) {
                            $basePrice = (float) $k->harga_satuan;
                        }
                        if (is_numeric($basePrice)) {
                            $dppValue = round(($basePrice * 11) / 12, 2);
                            $ppnValue = round($dppValue * 0.12, 2);
                            $pphValue = round($basePrice * 0.02, 2);
                        }
                        $dppDisplay = is_numeric($dppValue) ? 'Rp ' . number_format($dppValue, 2, ',', '.') : '-';
                        $ppnDisplay = is_numeric($ppnValue) ? 'Rp ' . number_format($ppnValue, 2, ',', '.') : '-';
                        if (is_numeric($pphValue)) {
                            $pphDisplay = '- Rp ' . number_format(abs($pphValue), 2, ',', '.');
                            $pphClass = 'text-lg font-semibold text-red-600';
                        } else {
                            $pphDisplay = '-';
                            $pphClass = 'text-lg';
                        }
                        // Grand total = harga satuan + PPN - PPH
                        $grandValue = null;
                        if (is_numeric($basePrice)) {
                            $grandValue = $basePrice + ($ppnValue ?? 0) - ($pphValue ?? 0);
                        }
                        $grandDisplay = is_numeric($grandValue) ? 'Rp ' . number_format($grandValue, 2, ',', '.') : '-';
                    @endphp
                    <tr class="hover:bg-green-100 align-middle">
                            <td class="py-2 px-3 align-middle whitespace-nowrap">
                                @php $already = isset($kontainersAlreadyInPranotaMap) && isset($kontainersAlreadyInPranotaMap[$k->id]); @endphp
                                <input type="checkbox" class="returned-checkbox" value="{{ $k->id }}" id="chk-{{ $k->id }}" @if($already) disabled @endif />
                                @if($already)
                                    <div class="text-xs text-gray-600 mt-1">Sudah ada di pranota @if(!empty($kontainersAlreadyInPranotaMap[$k->id]['group_code'])) ({{ $kontainersAlreadyInPranotaMap[$k->id]['group_code'] }}) @endif</div>
                                @endif
                            </td>
                            <td class="py-2 px-3 align-middle whitespace-nowrap">{{ $i + 1 }}</td>
                        <td class="py-2 px-3 align-middle whitespace-nowrap">{{ $fullNumber ?: '-' }}</td>
                        <td class="py-2 px-3 align-middle whitespace-nowrap">{{ $ukuran }}</td>
                        <td class="py-2 px-3 align-middle whitespace-nowrap">{{ $nomorMemo }}</td>
                        <td class="py-2 px-3 align-middle whitespace-nowrap">{{ $tanggalSelesai }}</td>
                        <td class="py-2 px-3 align-middle whitespace-nowrap">{{ $k->status ?? '-' }}</td>
                        <td class="py-2 px-3 text-right numeric-cell whitespace-nowrap" data-kontainer-id="{{ $k->id }}" data-kontainer-serial="{{ $syncSerial }}" @if($unitPrice !== null) data-original-price="{{ $unitPrice }}" @endif>
                            <div class="text-lg font-semibold">{{ $unitDisplay }}</div>
                        </td>
                        <td class="py-2 px-3 text-center align-middle whitespace-nowrap">
                            @if(Route::has('tagihan-kontainer-sewa.adjust_price'))
                                <form action="{{ route('tagihan-kontainer-sewa.adjust_price') }}" method="POST" class="inline-flex items-center space-x-2 adjust-price-form">
                            @else
                                <form action="#" method="POST" class="inline-flex items-center space-x-2 adjust-price-form">
                            @endif
                                @csrf
                                <input type="hidden" name="kontainer_id" value="{{ $k->id }}" />
                                <input type="hidden" name="nomor_kontainer" value="{{ $fullNumber }}" />
                                <input type="hidden" name="dpp" value="{{ $dppValue }}" />
                                <input type="hidden" name="ppn" value="{{ $ppnValue }}" />
                                <input type="hidden" name="pph" value="{{ $pphValue }}" />
                                <input type="hidden" name="grand_total" value="{{ $grandValue }}" />
                                <input type="number" step="0.01" name="adjust_price" data-kontainer-id="{{ $k->id }}" data-kontainer-serial="{{ $syncSerial }}" value="{{ old('adjust_price', $adjustValue !== '' ? $adjustValue : (isset($k->harga_satuan) ? $k->harga_satuan : '')) }}" class="border rounded px-2 py-1 w-24 text-base text-right" placeholder="0.00" />
                                <button type="submit" name="action" value="adjust_price" class="bg-blue-600 text-white px-2 py-1 rounded text-sm">Simpan</button>
                                <div class="ml-2 text-base text-gray-600 adjust-preview" data-for-id="{{ $k->id }}" data-for-serial="{{ $syncSerial }}">@if($adjustValue !== '') Rp {{ number_format($adjustValue, 2, ',', '.') }} @endif</div>
                            </form>
                        </td>
                        <td class="py-2 px-3 text-right numeric-cell whitespace-nowrap" data-dpp-for-id="{{ $k->id }}" data-dpp-for-serial="{{ $syncSerial }}" @if($dppValue !== null) data-original-dpp="{{ $dppValue }}" @endif>
                            <div class="text-lg font-semibold">{{ $dppDisplay }}</div>
                        </td>
                        <td class="py-2 px-3 text-right numeric-cell whitespace-nowrap" data-ppn-for-id="{{ $k->id }}" data-ppn-for-serial="{{ $syncSerial }}" @if($ppnValue !== null) data-original-ppn="{{ $ppnValue }}" @endif>
                            <div class="text-lg">{{ $ppnDisplay }}</div>
                        </td>
                        <td class="py-2 px-3 text-right numeric-cell whitespace-nowrap" data-pph-for-id="{{ $k->id }}" data-pph-for-serial="{{ $syncSerial }}" @if(isset($pphValue) && $pphValue !== null) data-original-pph="{{ $pphValue }}" @endif>
                            <div class="text-lg font-semibold text-red-600">{{ $pphDisplay ?? '-' }}</div>
                        </td>
                        <td class="py-2 px-3 text-right numeric-cell whitespace-nowrap" data-grand-for-id="{{ $k->id }}" data-grand-for-serial="{{ $syncSerial }}" @if(isset($grandValue) && $grandValue !== null) data-original-grand="{{ $grandValue }}" @endif>
                            <div class="text-lg font-semibold">{{ $grandDisplay }}</div>
                        </td>
                        @php
                            // try to find the most recent tagihan row associated with this kontainer
                            $tagihanRecord = \DB::table('tagihan_kontainer_sewa')
                                ->join('tagihan_kontainer_sewa_kontainers', 'tagihan_kontainer_sewa.id', '=', 'tagihan_kontainer_sewa_kontainers.tagihan_id')
                                ->where('tagihan_kontainer_sewa_kontainers.kontainer_id', $k->id)
                                ->orderBy('tagihan_kontainer_sewa.id', 'desc')
                                ->select('tagihan_kontainer_sewa.status_pembayaran', 'tagihan_kontainer_sewa.keterangan as tagihan_keterangan')
                                ->first();
                            $statusPembayaran = $tagihanRecord->status_pembayaran ?? null;
                            $tagihanKeterangan = $tagihanRecord->tagihan_keterangan ?? null;

                            // If controller reported this kontainer is already in a pranota, prefer to show
                            // the payment status if it's already paid ('Lunas'). Only fall back to the
                            // 'Sudah Masuk Pranota' badge when there is no explicit paid status, so that
                            // a paid pranota doesn't get masked by the generic pranota badge.
                            if (isset($kontainersAlreadyInPranotaMap) && is_array($kontainersAlreadyInPranotaMap) && isset($kontainersAlreadyInPranotaMap[$k->id])) {
                                if (empty($statusPembayaran) || strtolower(trim($statusPembayaran)) !== 'lunas') {
                                    $statusPembayaran = 'Sudah Masuk Pranota';
                                }
                                // Prefer showing the pranota nomor in tooltip if available
                                if (!empty($kontainersAlreadyInPranotaMap[$k->id]['nomor_pranota'])) {
                                    $tagihanKeterangan = 'Pranota: ' . $kontainersAlreadyInPranotaMap[$k->id]['nomor_pranota'];
                                }
                            }
                        @endphp
                        <td class="py-2 px-3 align-middle whitespace-nowrap">
                            <div class="inline-flex items-center space-x-3">
                                @if($statusPembayaran === 'Sudah Masuk Pranota')
                                    <span @if($tagihanKeterangan) title="{{ $tagihanKeterangan }}" @endif class="inline-flex items-center px-2 py-0.5 rounded text-sm font-medium bg-yellow-100 text-yellow-800">Sudah Masuk Pranota</span>
                                @elseif($statusPembayaran === 'Lunas')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-sm font-medium bg-blue-100 text-blue-800">Lunas</span>
                                @elseif($statusPembayaran === 'Belum Pembayaran' || !$statusPembayaran)
                                    {{-- default to showing unpaid / fallback --}}
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-sm font-medium bg-red-100 text-red-800">Belum Pembayaran</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-sm font-medium bg-gray-100 text-gray-800">{{ $statusPembayaran }}</span>
                                @endif
                                {{-- display only the status badge; remove adjacent descriptive keterangan per UX request --}}
                            </div>
                        </td>
                    </tr>
                 @empty
                     <tr>
                         <td colspan="14" class="py-4 px-6 text-center text-gray-500">Tidak ada kontainer yang dikembalikan untuk grup ini.</td>
                     </tr>
                 @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
    function initAdjustSync(){
        // selection helpers for 'Kontainer Dikembalikan' checklist
        function updateSelectionUI(){
            var checks = Array.from(document.querySelectorAll('.returned-checkbox'));
            // ignore disabled checkboxes
            var enabledChecks = checks.filter(c => !c.disabled);
            var selected = enabledChecks.filter(c => c.checked).map(c => c.value);
            document.getElementById('selected-count').textContent = selected.length + ' dipilih';
            var idsInput = document.getElementById('pranota-kontainer-ids');
            if (idsInput) idsInput.value = selected.join(',');
            var btn = document.getElementById('btn-create-pranota');
            if (btn) btn.disabled = selected.length === 0;
        }
        document.addEventListener('change', function(e){ if (e.target && e.target.classList && e.target.classList.contains('returned-checkbox')) updateSelectionUI(); });
        // select-all header checkbox with indeterminate state
        var chkAll = document.getElementById('chk-all-returned');
        function refreshHeaderState(){
            if (!chkAll) return;
            // consider only enabled checkboxes for header state
            var checks = Array.from(document.querySelectorAll('.returned-checkbox')).filter(c => !c.disabled);
            var total = checks.length;
            var checked = checks.filter(c=>c.checked).length;
            chkAll.indeterminate = checked > 0 && checked < total;
            chkAll.checked = total > 0 && checked === total;
        }
        if (chkAll) {
            chkAll.addEventListener('change', function(e){
                var checks = Array.from(document.querySelectorAll('.returned-checkbox')).filter(c => !c.disabled);
                checks.forEach(function(c){ c.checked = chkAll.checked; });
                updateSelectionUI();
                refreshHeaderState();
            });
            // when individual toggles change, refresh header state
            document.addEventListener('change', function(e){ if (e.target && e.target.classList && e.target.classList.contains('returned-checkbox')) { refreshHeaderState(); } });
        }
        // quick select-all behavior on header click (if header checkbox added later)
        // initialize selection UI on load
        try { updateSelectionUI(); } catch (ee) {}

        const inputs = document.querySelectorAll('input[name="adjust_price"][data-kontainer-id]');
        const groups = {};
        inputs.forEach(i => {
            const serial = (i.getAttribute('data-kontainer-serial') || '').trim().toLowerCase();
            const id = i.getAttribute('data-kontainer-id');
            const key = serial !== '' ? ('serial:' + serial) : ('id:' + id);
            if (!groups[key]) groups[key] = [];
            groups[key].push(i);
        });

        function parseNumericInput(s){
            if (s === null || s === undefined) return NaN;
            var str = String(s).trim();
            if (str === '') return NaN;
            var hasDot = str.indexOf('.') !== -1;
            var hasComma = str.indexOf(',') !== -1;
            if (hasDot && hasComma) {
                str = str.replace(/\./g, '');
                str = str.replace(/,/g, '.');
            } else if (!hasDot && hasComma) {
                str = str.replace(/,/g, '.');
            }
            var n = parseFloat(str);
            return isFinite(n) ? n : NaN;
        }

        function formatRp(n){
            if (!isFinite(n)) return '-';
            try {
                return 'Rp ' + n.toLocaleString('id-ID', {minimumFractionDigits:2, maximumFractionDigits:2});
            } catch (err) {
                // fallback
                return 'Rp ' + n.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }
        }

        // helper to get an immutable base price for a TD (cached on data-base-price)
        function getBasePriceFromTd(td){
            if (!td) return NaN;
            var baseAttr = td.getAttribute('data-base-price');
            if (baseAttr !== null && baseAttr !== undefined && String(baseAttr).trim() !== '') {
                var bn = parseNumericInput(baseAttr);
                if (isFinite(bn)) return bn;
            }
            var orig = td.getAttribute('data-original-price');
            if (orig !== null && orig !== undefined && String(orig).trim() !== '') {
                var on = parseNumericInput(orig);
                if (isFinite(on)) {
                    td.setAttribute('data-base-price', String(on));
                    return on;
                }
            }
            var txt = (td.textContent || '').replace(/Rp\s*/i, '').trim();
            var pn = parseNumericInput(txt);
            if (isFinite(pn)) {
                td.setAttribute('data-base-price', String(pn));
                return pn;
            }
            return NaN;
        }

        // write text into cell but preserve existing inner element's classes (div/span) if present
        function setCellText(td, text) {
            if (!td) return;
            try {
                var inner = td.querySelector && (td.querySelector('div') || td.querySelector('span'));
                if (inner) {
                    inner.textContent = text;
                } else {
                    td.textContent = text;
                }
            } catch (e) {
                try { td.textContent = text; } catch (er) {}
            }
        }

        function updateDisplayForInput(inputEl){
            if (!inputEl) return;
            var id = inputEl.getAttribute('data-kontainer-id');
            var serial = inputEl.getAttribute('data-kontainer-serial');
            var raw = inputEl.value;

            // Always treat the adjust field as a delta (add/subtract to Harga Satuan).
            var rawTrim = String(raw === undefined || raw === null ? '' : raw).trim();
            var parseForDelta = rawTrim.replace(/^[+]/, '');
            var delta = parseNumericInput(parseForDelta);

            function applyToTd(td){
                var base = getBasePriceFromTd(td);
                if (!isFinite(base) && !isFinite(delta)) {
                    setCellText(td, '-');
                    return;
                }
                if (!isFinite(delta)) {
                    setCellText(td, isFinite(base) ? formatRp(base) : '-');
                    return;
                }
                var newPrice = (isFinite(base) ? base : 0) + Number(delta);
                setCellText(td, formatRp(newPrice));
                td.setAttribute('data-last-displayed-price', String(newPrice));
            }

                if (id) {
                    var tds = document.querySelectorAll('td[data-kontainer-id="' + id + '"]');
                    tds.forEach(function(td){ applyToTd(td); });
                }
                if (serial) {
                    var tds2 = document.querySelectorAll('td[data-kontainer-serial="' + serial + '"]');
                    tds2.forEach(function(td){ applyToTd(td); });
                }

                // Also update DPP Nilai Lain cells linked to this kontainer id/serial
                function updateDppForElement(elem, displayedPrice) {
                    if (!elem) return;
                    var dppCells = [];
                    var idAttr = elem.getAttribute('data-kontainer-id');
                    var serialAttr = elem.getAttribute('data-kontainer-serial');
                    if (idAttr) dppCells = dppCells.concat(Array.from(document.querySelectorAll('td[data-dpp-for-id="' + idAttr + '"]')));
                    if (serialAttr) dppCells = dppCells.concat(Array.from(document.querySelectorAll('td[data-dpp-for-serial="' + serialAttr + '"]')));
                    dppCells.forEach(function(dcell){
                        try {
                            var base = displayedPrice;
                            if (!isFinite(base)) {
                                // try to obtain base from td attributes
                                base = getBasePriceFromTd(elem);
                            }
                            if (!isFinite(base)) {
                                dcell.textContent = '-';
                                return;
                            }
                            var dpp = (Number(base) * 11) / 12;
                            setCellText(dcell, formatRp(dpp));
                            dcell.setAttribute('data-last-displayed-dpp', String(dpp));
                            // update linked PPN and PPH cells
                            var idAttr = dcell.getAttribute('data-dpp-for-id');
                            var serialAttr = dcell.getAttribute('data-dpp-for-serial');
                            var ppnCells = [];
                            var pphCells = [];
                            if (idAttr) {
                                ppnCells = ppnCells.concat(Array.from(document.querySelectorAll('td[data-ppn-for-id="' + idAttr + '"]')));
                                pphCells = pphCells.concat(Array.from(document.querySelectorAll('td[data-pph-for-id="' + idAttr + '"]')));
                            }
                            if (serialAttr) {
                                ppnCells = ppnCells.concat(Array.from(document.querySelectorAll('td[data-ppn-for-serial="' + serialAttr + '"]')));
                                pphCells = pphCells.concat(Array.from(document.querySelectorAll('td[data-pph-for-serial="' + serialAttr + '"]')));
                            }
                            ppnCells.forEach(function(pcell){
                                try {
                                    var ppn = (Number(dpp) * 12) / 100;
                                    setCellText(pcell, formatRp(ppn));
                                    pcell.setAttribute('data-last-displayed-ppn', String(ppn));
                                } catch (ee) {}
                            });
                            // compute and update PPH (2% of harga satuan)
                            pphCells.forEach(function(phcell){
                                try {
                                    var baseForPph = displayedPrice;
                                    if (!isFinite(baseForPph)) baseForPph = getBasePriceFromTd(elem);
                                    if (!isFinite(baseForPph)) {
                                        setCellText(phcell, '-');
                                        phcell.removeAttribute('data-last-displayed-pph');
                                        return;
                                    }
                                    var pph = (Number(baseForPph) * 0.02);
                                    // always prefix PPH with a leading minus
                                    setCellText(phcell, '- ' + formatRp(Math.abs(pph)));
                                    phcell.setAttribute('data-last-displayed-pph', String(pph));
                                } catch (ee) {}
                            });
                                // update grand total cells linked to this element
                                var grandCells = [];
                                if (idAttr) grandCells = grandCells.concat(Array.from(document.querySelectorAll('td[data-grand-for-id="' + idAttr + '"]')));
                                if (serialAttr) grandCells = grandCells.concat(Array.from(document.querySelectorAll('td[data-grand-for-serial="' + serialAttr + '"]')));
                                grandCells.forEach(function(gcell){
                                    try {
                                        var baseForGrand = displayedPrice;
                                        if (!isFinite(baseForGrand)) baseForGrand = getBasePriceFromTd(elem);
                                        if (!isFinite(baseForGrand)) { setCellText(gcell, '-'); return; }
                                        var ppnForGrand = (Number(dpp) * 12) / 100;
                                        var pphForGrand = (Number(baseForGrand) * 0.02);
                                        var grand = Number(baseForGrand) + Number(ppnForGrand) - Number(pphForGrand);
                                        setCellText(gcell, formatRp(grand));
                                        gcell.setAttribute('data-last-displayed-grand', String(grand));
                                    } catch (err) {}
                                });
                        } catch (e) {
                            // ignore
                        }
                    });
                }

                // pick one td to derive displayed price from (id preferred)
                if (id) {
                    var someTd = document.querySelector('td[data-kontainer-id="' + id + '"]');
                    var displayed = NaN;
                    if (someTd) {
                        var last = someTd.getAttribute('data-last-displayed-price');
                        if (last) displayed = parseNumericInput(last);
                        if (!isFinite(displayed)) {
                            var txt = (someTd.textContent || '').replace(/Rp\s*/i, '').trim();
                            displayed = parseNumericInput(txt);
                        }
                    }
                    updateDppForElement(someTd, displayed);
                } else if (serial) {
                    var someTd2 = document.querySelector('td[data-kontainer-serial="' + serial + '"]');
                    var displayed2 = NaN;
                    if (someTd2) {
                        var last2 = someTd2.getAttribute('data-last-displayed-price');
                        if (last2) displayed2 = parseNumericInput(last2);
                        if (!isFinite(displayed2)) {
                            var txt2 = (someTd2.textContent || '').replace(/Rp\s*/i, '').trim();
                            displayed2 = parseNumericInput(txt2);
                        }
                    }
                    updateDppForElement(someTd2, displayed2);
                }
        }

        // initialize immutable base prices for all relevant TDs so deltas are always applied against the true base
        var allTds = Array.from(document.querySelectorAll('td[data-kontainer-id], td[data-kontainer-serial]'));
        allTds.forEach(function(td){ try { getBasePriceFromTd(td); } catch (err) { /* ignore */ } });

        // initialize DPP cells based on the initialized base/displayed prices
        try {
            allTds.forEach(function(td){
                try {
                    var last = td.getAttribute('data-last-displayed-price');
                    var displayed = NaN;
                    if (last) displayed = parseFloat(last);
                    if (!isFinite(displayed)) {
                        var txt = (td.textContent || '').replace(/Rp\s*/i, '').trim();
                        displayed = parseNumericInput(txt);
                    }
                    if (!isFinite(displayed)) return;
                    // find linked DPP cells and set
                    var idAttr = td.getAttribute('data-kontainer-id');
                    var serialAttr = td.getAttribute('data-kontainer-serial');
                    var dppCells = [];
                    if (idAttr) dppCells = dppCells.concat(Array.from(document.querySelectorAll('td[data-dpp-for-id="' + idAttr + '"]')));
                    if (serialAttr) dppCells = dppCells.concat(Array.from(document.querySelectorAll('td[data-dpp-for-serial="' + serialAttr + '"]')));
                        dppCells.forEach(function(dcell){
                        var dpp = (Number(displayed) * 11) / 12;
                        setCellText(dcell, formatRp(dpp));
                        dcell.setAttribute('data-last-displayed-dpp', String(dpp));
                        // initialize corresponding PPN cell
                        var idAttr = dcell.getAttribute('data-dpp-for-id');
                        var serialAttr = dcell.getAttribute('data-dpp-for-serial');
                        var ppnCellsInit = [];
                        if (idAttr) ppnCellsInit = ppnCellsInit.concat(Array.from(document.querySelectorAll('td[data-ppn-for-id="' + idAttr + '"]')));
                        if (serialAttr) ppnCellsInit = ppnCellsInit.concat(Array.from(document.querySelectorAll('td[data-ppn-for-serial="' + serialAttr + '"]')));
                        ppnCellsInit.forEach(function(pcell){
                            try {
                                var ppnInit = (Number(dpp) * 12) / 100;
                                setCellText(pcell, formatRp(ppnInit));
                                pcell.setAttribute('data-last-displayed-ppn', String(ppnInit));
                            } catch (ee) {}
                        });
                        // initialize corresponding PPH cell(s) (2% of harga satuan)
                        var pphCellsInit = [];
                        if (idAttr) pphCellsInit = pphCellsInit.concat(Array.from(document.querySelectorAll('td[data-pph-for-id="' + idAttr + '"]')));
                        if (serialAttr) pphCellsInit = pphCellsInit.concat(Array.from(document.querySelectorAll('td[data-pph-for-serial="' + serialAttr + '"]')));
                        pphCellsInit.forEach(function(phcell){
                            try {
                                var pphInit = (Number(displayed) * 0.02);
                                // always prefix PPH with a leading minus
                                setCellText(phcell, '- ' + formatRp(Math.abs(pphInit)));
                                phcell.setAttribute('data-last-displayed-pph', String(pphInit));
                            } catch (ee) {}
                        });
                        // initialize corresponding grand cell(s)
                        var grandInitCells = [];
                        if (idAttr) grandInitCells = grandInitCells.concat(Array.from(document.querySelectorAll('td[data-grand-for-id="' + idAttr + '"]')));
                        if (serialAttr) grandInitCells = grandInitCells.concat(Array.from(document.querySelectorAll('td[data-grand-for-serial="' + serialAttr + '"]')));
                        grandInitCells.forEach(function(gcell){
                            try {
                                var baseForGrandInit = Number(displayed);
                                var ppnInit = (Number(dpp) * 12) / 100;
                                var pphInit = (Number(displayed) * 0.02);
                                var grandInit = baseForGrandInit + ppnInit - pphInit;
                                setCellText(gcell, formatRp(grandInit));
                                gcell.setAttribute('data-last-displayed-grand', String(grandInit));
                            } catch (ee) {}
                        });
                    });
                } catch (e) {
                    // ignore per-td errors
                }
            });
        } catch (e) {}

        Object.keys(groups).forEach(key => {
            groups[key].forEach(i => {
                const handler = (e) => {
                    const v = e.target.value;
                    groups[key].forEach(other => {
                        if (other === e.target) return;
                        other.value = v;
                        try {
                            other.style.transition = 'background-color 0.2s ease';
                            const prev = other.style.backgroundColor;
                            other.style.backgroundColor = '#fff7d6';
                            setTimeout(() => { other.style.backgroundColor = prev; }, 350);
                        } catch (err) {}
                        try {
                            const ev = new Event('input', { bubbles: true });
                            other.dispatchEvent(ev);
                        } catch (err) {}
                    });

                    // update Harga Satuan display for this input (and mirrored inputs)
                    try { updateDisplayForInput(e.target); } catch (err) { /* silent */ }
                    // update preview span(s)
                    try {
                        var id = e.target.getAttribute('data-kontainer-id');
                        var serial = e.target.getAttribute('data-kontainer-serial');
                        var previews = [];
                        if (id) previews = previews.concat(Array.from(document.querySelectorAll('span.adjust-preview[data-for-id="' + id + '"]')));
                        if (serial) previews = previews.concat(Array.from(document.querySelectorAll('span.adjust-preview[data-for-serial="' + serial + '"]')));
                        var val = e.target.value;
                        var rawTrim = String(val === undefined || val === null ? '' : val).trim();
                        var parseForDelta = rawTrim.replace(/^[+]/, '');
                        var n = parseNumericInput(parseForDelta);
                        var txt = '';
                        if (isFinite(n)) {
                            txt = (String(rawTrim).trim().startsWith('-') ? '-' : '+') + formatRp(Math.abs(n));
                        }
                        previews.forEach(function(p){ p.textContent = txt; });
                    } catch (err) { /* silent */ }
                };
                i.addEventListener('input', handler);
                i.addEventListener('change', handler);
            });
        });

        function normalizeForSubmit(val) {
            if (val === null || val === undefined) return val;
            var s = String(val).trim();
            if (s === '') return s;
            // strip leading + if present, keep leading -
            s = s.replace(/^\+/, '');
            var hasDot = s.indexOf('.') !== -1;
            var hasComma = s.indexOf(',') !== -1;
            // If both present assume dot as thousands and comma as decimal
            if (hasDot && hasComma) {
                s = s.replace(/\./g, '');
                s = s.replace(/,/g, '.');
                return s;
            }
            // If only comma present, replace with dot
            if (!hasDot && hasComma) {
                s = s.replace(/,/g, '.');
                return s;
            }
            // otherwise return cleaned string (with leading - if present)
            return s;
        }

        var adjustForms = document.querySelectorAll('form.adjust-price-form');
        adjustForms.forEach(function(f){
            f.addEventListener('submit', function(e){
                try {
                    var fld = f.querySelector('input[name="adjust_price"]');
                    if (fld) {
                        fld.value = normalizeForSubmit(fld.value);
                    }
                } catch (err) {
                    console.warn('adjust-price normalize error', err);
                }
            });
        });

        // Safety: prevent automatic programmatic submission of the pranota form.
        // The pranota form will only be allowed to submit when the user clicks
        // the "Buat Pranota" button which sets a short-lived flag.
        try {
            var pranotaForm = document.getElementById('pranota-create-form');
            var pranotaBtn = document.getElementById('btn-create-pranota');
            if (pranotaForm && pranotaBtn) {
                // mark form as user-initiated only when the user clicks the button
                pranotaBtn.addEventListener('click', function(){
                    pranotaForm.dataset.userInitiated = '1';
                });

                // block submits that are not user-initiated
                pranotaForm.addEventListener('submit', function(e){
                    if (pranotaForm.dataset.userInitiated !== '1') {
                        e.preventDefault();
                        try { alert('Harap pilih kontainer yang ingin dimasukkan, lalu klik tombol "Buat Pranota". Pranota tidak akan dibuat otomatis.'); } catch (ex) {}
                        return false;
                    }
                    // clear the flag so programmatic submits after user action remain blocked
                    delete pranotaForm.dataset.userInitiated;
                });
            }
        } catch (err) {
            // silent fail — don't block page load if something goes wrong
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAdjustSync);
    } else {
        initAdjustSync();
    }
})();
</script>
<style>
/* debug overlay styles (temporary) */
#adjust-sync-debug {
    position: fixed;
    right: 12px;
    bottom: 12px;
    width: 340px;
    max-height: 50vh;
    overflow: auto;
    background: rgba(0,0,0,0.75);
    color: #fff;
    font-size: 12px;
    padding: 8px;
    border-radius: 6px;
    z-index: 99999;
    box-shadow: 0 6px 18px rgba(0,0,0,0.4);
}
#adjust-sync-debug h4{ margin:0 0 6px 0; font-size:13px }
#adjust-sync-debug pre{ white-space:pre-wrap; color:#fff; margin:0 }
</style>
@endpush

{{-- NOTE: The above script/style block is the single, intentionally placed @push('scripts') section.
    Removed duplicated/stray push/endpush and extra fragments that caused Blade parse errors.
--}}

