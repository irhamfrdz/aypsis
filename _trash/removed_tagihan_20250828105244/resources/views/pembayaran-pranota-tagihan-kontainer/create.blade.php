@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <div class="bg-white rounded shadow p-6">
        <h2 class="text-2xl font-semibold mb-6">Form Pembayaran Pranota Tagihan Kontainer</h2>

        {{-- Flash messages (success / error) and validation summary --}}
        @if(session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded" role="alert">
                {{ session('error') }}
            </div>
        @endif

        @if($errors && $errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded" role="alert">
                <div class="font-semibold">Terdapat beberapa kesalahan:</div>
                <ul class="mt-1 list-disc list-inside text-sm">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('pembayaran-pranota-tagihan-kontainer.store') }}" id="pembayaranForm" class="space-y-6">
            @csrf

            <div class="grid grid-cols-12 gap-6">
                {{-- left: meta fields --}}
                <div class="col-span-8">
                    <div class="grid grid-cols-12 gap-4 items-end">
                        <div class="col-span-5">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Pembayaran</label>
                            @php
                                $running = \App\Models\PembayaranPranotaTagihanKontainer::count() + 1;
                                $cetakanDefault = old('nomor_cetakan', 1);
                                $nomorPembayaran = sprintf('BTK-%s-%s-%s-%s', $cetakanDefault, now()->format('y'), now()->format('m'), str_pad($running, 6, '0', STR_PAD_LEFT));
                            @endphp
                            <input type="text" name="nomor_pembayaran" id="nomor_pembayaran" value="{{ $nomorPembayaran }}" class="w-full p-2 rounded border bg-gray-50" readonly />
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Cetakan</label>
                            <input type="number" min="1" step="1" name="nomor_cetakan" id="nomor_cetakan" class="w-full p-2 rounded border bg-white" value="{{ old('nomor_cetakan', 1) }}" />
                            @error('nomor_cetakan')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="col-span-5">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Bank</label>
                            <select name="bank" id="bank" class="w-full p-2 rounded border bg-white">
                                <option value="">-- Pilih Bank --</option>
                                <option value="BCA" {{ old('bank') == 'BCA' ? 'selected' : '' }}>BCA</option>
                                <option value="Mandiri" {{ old('bank') == 'Mandiri' ? 'selected' : '' }}>Mandiri</option>
                                <option value="BRI" {{ old('bank') == 'BRI' ? 'selected' : '' }}>BRI</option>
                                <option value="BNI" {{ old('bank') == 'BNI' ? 'selected' : '' }}>BNI</option>
                                <option value="CIMB" {{ old('bank') == 'CIMB' ? 'selected' : '' }}>CIMB</option>
                                <option value="Danamon" {{ old('bank') == 'Danamon' ? 'selected' : '' }}>Danamon</option>
                                <option value="Permata" {{ old('bank') == 'Permata' ? 'selected' : '' }}>Permata</option>
                            </select>
                            @error('bank')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-12 gap-4 mt-4">
                        <div class="col-span-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Transaksi</label>
                            <select name="jenis_transaksi" id="jenis_transaksi" class="w-full p-2 rounded border bg-white">
                                <option value="">-- Pilih Jenis --</option>
                                <option value="Debit" {{ old('jenis_transaksi') == 'Debit' ? 'selected' : '' }}>Debit</option>
                                <option value="Kredit" {{ old('jenis_transaksi') == 'Kredit' ? 'selected' : '' }}>Kredit</option>
                            </select>
                            @error('jenis_transaksi')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="col-span-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kas</label>
                            <input type="date" name="tanggal_kas" value="{{ old('tanggal_kas', now()->toDateString()) }}" class="w-full p-2 rounded border bg-white" />
                            @error('tanggal_kas')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="col-span-4">
                            {{-- removed Alasan Penyesuaian (keterangan) per UI decision --}}
                        </div>
                    </div>
                </div>

                {{-- right: totals & actions --}}
                <div class="col-span-4">
                    <div class="border rounded p-4 bg-gray-50">
                        <h4 class="font-medium mb-3">Ringkasan Pembayaran</h4>
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm text-gray-600">Total Pembayaran</label>
                                <div class="mt-1 flex">
                                    <span class="inline-flex items-center px-3 rounded-l border border-r-0 bg-gray-50 text-gray-700">Rp</span>
                                    <input type="text" id="totalPembayaran" class="flex-1 p-2 rounded-r border bg-white text-right font-medium" readonly value="0" />
                                </div>
                            </div>

                            <div>
                                <label class="text-sm text-gray-600">Penyesuaian</label>
                                <div class="mt-1 flex">
                                    <span class="inline-flex items-center px-3 rounded-l border border-r-0 bg-gray-50 text-gray-700">Rp</span>
                                    <input type="number" name="penyesuaian" id="penyesuaian" class="flex-1 p-2 rounded-r border bg-white text-right" value="{{ old('penyesuaian', 0) }}" />
                                </div>
                            </div>

                            <div>
                                <label class="text-sm text-gray-600">Total Setelah Penyesuaian</label>
                                <div class="mt-1 flex">
                                    <span class="inline-flex items-center px-3 rounded-l border border-r-0 bg-gray-50 text-gray-700">Rp</span>
                                    <input type="text" id="totalAfter" class="flex-1 p-2 rounded-r border bg-white text-right font-semibold" readonly value="0" />
                                </div>
                            </div>

                            <div class="pt-4">
                                <button type="submit" id="submitBtn" class="w-full bg-indigo-600 text-white px-4 py-2 rounded disabled:opacity-50" disabled>Simpan Pembayaran</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <h3 class="mt-2 font-semibold">Pilih Pranota Tagihan</h3>

            {{-- Informasi Group pills removed per request --}}

            <div class="bg-white shadow rounded overflow-hidden mt-2">
                <table class="min-w-full divide-y divide-gray-200 text-sm md:text-base font-sans leading-relaxed">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm md:text-base font-semibold text-gray-600 w-12">#</th>
                            <th class="px-4 py-3 text-left text-sm md:text-base font-semibold text-gray-600">Nomor Pranota</th>
                            <th class="px-4 py-3 text-left text-sm md:text-base font-semibold text-gray-600">Vendor</th>
                            <th class="px-4 py-3 text-left text-sm md:text-base font-semibold text-gray-600">Tanggal</th>
                            <th class="px-4 py-3 text-right text-sm md:text-base font-semibold text-gray-600">Jumlah (Rp)</th>
                            {{-- Keterangan column removed --}}
                            <th class="px-4 py-3 text-left text-sm md:text-base font-semibold text-gray-600">
                                <div class="flex items-center">
                                    <label class="mr-2 text-sm text-gray-600">Pilih</label>
                                    <input type="checkbox" id="selectAll" class="h-4 w-4" />
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($pranotas as $i => $pranota)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm md:text-base text-gray-800">{{ $i + 1 }}</td>
                            @php
                                $routeDate = $pranota->tanggal_harga_awal ? (method_exists($pranota->tanggal_harga_awal, 'format') ? $pranota->tanggal_harga_awal->format('Y-m-d') : \Carbon\Carbon::parse($pranota->tanggal_harga_awal)->format('Y-m-d')) : null;
                                $displayId = $pranota->displayId ?? ($pranota->nomor_pranota ?? $pranota->id);
                            @endphp
                            <td class="px-4 py-3 text-sm md:text-base text-gray-800">
                                @if(!empty($routeDate))
                                    <a href="{{ route('tagihan-kontainer-sewa.group.show', ['vendor' => $pranota->vendor, 'tanggal' => $routeDate]) }}" class="text-indigo-600 hover:underline">{{ $displayId }}</a>
                                @else
                                    {{ $displayId }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm md:text-base text-gray-800">{{ $pranota->vendor }}</td>
                            <td class="px-4 py-3 text-sm md:text-base text-gray-800">{{ $pranota->displayTanggal ?? '' }}</td>
                            <td class="px-4 py-3 text-sm md:text-base text-right text-gray-800">{{ number_format($pranota->harga, 2, ',', '.') }}</td>
                            @php
                                $rawKet = $pranota->keterangan ?? null;
                                $gcode = isset($pranota->group_code) ? trim($pranota->group_code) : null;
                                $periode = isset($pranota->periode) ? trim($pranota->periode) : null;
                                // treat '-' as empty
                                if ($gcode === '-') $gcode = null;
                                // If the pranota's group_code was populated with the pranota's own nomor (legacy/fallback),
                                // treat it as empty so we prefer the real group code from sample tagihan rows.
                                try {
                                    if (!empty($gcode) && !empty($pranota->nomor_pranota) && trim($pranota->nomor_pranota) === $gcode) {
                                        $gcode = null;
                                    }
                                    // Also treat obvious pranota identifiers like 'PR' followed by digits as non-group codes
                                    if (!empty($gcode) && preg_match('/^PR\d+/', $gcode)) {
                                        $gcode = null;
                                    }
                                } catch (\Exception $e) {
                                    // ignore pattern checks
                                }
                                // If this pranota row doesn't contain group/periode, try to find a sample tagihan_kontainer_sewa
                                if (empty($gcode) || empty($periode)) {
                                    try {
                                        // First try: strict lookup by vendor (+ date when available) excluding pranota rows
                                        $sampleQuery = \DB::table('tagihan_kontainer_sewa')
                                            ->where('vendor', $pranota->vendor)
                                            ->where('tarif', '!=', 'Pranota');
                                        if (!empty($pranota->tanggal_harga_awal)) {
                                            $sampleQuery = $sampleQuery->whereDate('tanggal_harga_awal', $pranota->tanggal_harga_awal);
                                        }
                                        $sample = $sampleQuery->orderBy('id', 'asc')->first();

                                        // Secondary fallback: if strict lookup returned nothing, find any recent tagihan for same vendor that has a real group_code
                                        if (empty($sample)) {
                                            $secondary = \DB::table('tagihan_kontainer_sewa')
                                                ->where('vendor', $pranota->vendor)
                                                ->where('tarif', '!=', 'Pranota')
                                                ->whereNotNull('group_code')
                                                ->where('group_code', '!=', '-')
                                                ->orderBy('id', 'desc')
                                                ->first();
                                            if ($secondary) {
                                                $sample = $secondary;
                                            }
                                        }

                                        if ($sample) {
                                            if (empty($gcode) && isset($sample->group_code) && trim($sample->group_code) !== '') {
                                                $gcode = trim($sample->group_code);
                                            }
                                            if (empty($periode) && isset($sample->periode) && trim((string)$sample->periode) !== '') {
                                                $periode = trim((string)$sample->periode);
                                            }
                                        }
                                    } catch (\Exception $e) {
                                        // ignore DB fallback failures
                                    }
                                }
                                // server-side computed
                            @endphp
                            {{-- Keterangan cell removed; display handled in server-side summary if needed --}}
                            <td class="px-4 py-3 text-sm md:text-base text-gray-800">
                                <div class="flex items-center justify-center">
                                    <input type="checkbox" class="pranota-checkbox" data-id="{{ $pranota->id }}" data-amount="{{ $pranota->harga }}" />
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-gray-500">Tidak ada pranota ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <input type="hidden" name="pranota_ids" id="pranota_ids" />
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // live elements (query inside DOMContentLoaded)
        const selectAll = document.getElementById('selectAll');
        const pranotaIdsInput = document.getElementById('pranota_ids');
        const totalInput = document.getElementById('totalPembayaran');
        const penyesuaianInput = document.getElementById('penyesuaian');
        const totalAfter = document.getElementById('totalAfter');
        const submitBtn = document.getElementById('submitBtn');
        const nomorCetakanInput = document.getElementById('nomor_cetakan');
        const nomorPembayaranInput = document.getElementById('nomor_pembayaran');
        const runningNumber = '{{ str_pad(\App\Models\PembayaranPranotaTagihanKontainer::count() + 1, 6, "0", STR_PAD_LEFT) }}';

        function formatCurrency(num) {
            return (Number(num) || 0).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function getCheckboxes() {
            return Array.from(document.querySelectorAll('.pranota-checkbox'));
        }

        function updateTotals() {
            const checkboxes = getCheckboxes();
            let ids = [];
            let total = 0;
            checkboxes.forEach(cb => {
                // ignore if parent row is hidden
                const row = cb.closest('tr');
                const visible = row && window.getComputedStyle(row).display !== 'none';
                if (cb.checked && visible) {
                    ids.push(cb.dataset.id);
                    total += parseFloat(cb.dataset.amount) || 0;
                }
            });
            pranotaIdsInput.value = ids.join(',');
            totalInput.value = formatCurrency(total);
            const pen = parseFloat(penyesuaianInput.value) || 0;
            totalAfter.value = formatCurrency(total + pen);

            // enable submit if at least one pranota selected
            submitBtn.disabled = ids.length === 0;
        }

        // selectAll should only toggle visible rows
        if (selectAll) {
            selectAll.addEventListener('change', (e) => {
                const checkboxes = getCheckboxes();
                checkboxes.forEach(cb => {
                    const row = cb.closest('tr');
                    const visible = row && window.getComputedStyle(row).display !== 'none';
                    if (visible) cb.checked = e.target.checked;
                });
                updateTotals();
            });
        }

        // wire checkbox change handlers (delegated attach)
        getCheckboxes().forEach(cb => cb.addEventListener('change', updateTotals));
        penyesuaianInput && penyesuaianInput.addEventListener('input', updateTotals);

        // nomor pembayaran helper
        function updateNomorPembayaran() {
            const cetakan = (nomorCetakanInput && nomorCetakanInput.value) ? nomorCetakanInput.value : '1';
            const tahun = new Date().getFullYear().toString().slice(-2);
            const bulan = (new Date().getMonth() + 1).toString().padStart(2, '0');
            if (nomorPembayaranInput) {
                nomorPembayaranInput.value = `BTK-${cetakan}-${tahun}-${bulan}-${runningNumber}`;
            }
        }
        nomorCetakanInput && nomorCetakanInput.addEventListener('input', updateNomorPembayaran);

        // init totals and nomor on load
        updateNomorPembayaran();
        updateTotals();

    // group filter buttons removed
    });
</script>
@endpush
@endsection
