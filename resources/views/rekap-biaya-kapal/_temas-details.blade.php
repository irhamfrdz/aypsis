@php
    $temasGroups = $temasDetails->groupBy(function ($detail) {
        $bl = trim($detail->nomor_bl ?? '');

        return $bl !== '' ? 'bl:' . $bl : 'kontainer:' . trim($detail->nomor_kontainer ?? '');
    });
@endphp

<div class="space-y-3">
    <p class="text-xs font-semibold text-blue-800">Rincian TEMAS per BL / Kontainer</p>
    @foreach($temasGroups as $details)
        @php
            $nomorBl = trim($details->first()->nomor_bl ?? '');
            $containers = $details->flatMap(fn ($detail) => explode(',', $detail->nomor_kontainer ?? ''))
                ->map(fn ($number) => strtoupper(trim($number)))->filter()->unique()->values();
            $sizes = $details->pluck('size')->filter()->unique()->values();
            $normalizeBl = fn ($value) => strtoupper(preg_replace('/-\d+$/', '', trim((string) $value)));
            $shippers = ($temasManifests ?? collect())
                ->filter(fn ($manifest) => $containers->contains(strtoupper(trim($manifest->nomor_kontainer ?? '')))
                    && ($nomorBl === '' || $normalizeBl($manifest->nomor_bl) === $normalizeBl($nomorBl)))
                ->flatMap(function ($manifest) {
                    $names = collect([$manifest->shipperJb?->shipper ?: ($manifest->shipperConsignee?->shipper ?: $manifest->pengirim)]);
                    foreach ($manifest->shipperDetails as $shipperDetail) {
                        $names->push($shipperDetail->shipperConsignee?->shipper ?: $shipperDetail->pengirim);
                    }

                    return $names;
                })->map(fn ($name) => trim((string) $name))->filter()->unique()->values();
        @endphp
        <div class="rounded-xl border border-blue-100 bg-white overflow-hidden">
            <div class="px-4 py-3 bg-blue-50 flex flex-wrap items-start justify-between gap-3 text-xs">
                <div class="min-w-0 flex-1">
                    <p class="font-bold text-blue-900 text-sm">
                        @if($nomorBl !== '')
                            BL: {{ $nomorBl }}
                        @elseif($containers->isNotEmpty())
                            Detail per kontainer
                        @else
                            Pembayaran TEMAS tanpa rincian BL / kontainer
                        @endif
                    </p>
                    @if($nomorBl !== '' || $containers->isNotEmpty())
                        <p class="mt-1 text-gray-600"><span class="font-semibold">Shipper:</span> {{ $shippers->isNotEmpty() ? $shippers->implode(', ') : 'Belum tersedia' }}</p>
                    @endif
                    @if($containers->isNotEmpty())
                        <div class="mt-2 flex flex-wrap gap-2">
                            <span class="rounded-full bg-white border border-blue-100 px-2 py-0.5 font-medium text-blue-800">{{ $containers->count() }} kontainer</span>
                            @foreach($sizes as $size)
                                <span class="rounded-full bg-white border border-blue-100 px-2 py-0.5 text-gray-600">{{ $size }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="text-right whitespace-nowrap">
                    <p class="text-gray-500">Total biaya BL / kontainer</p>
                    <p class="mt-1 text-sm font-bold text-blue-900">Rp {{ number_format($details->sum(fn ($detail) => $detail->rekap_bl_total ?? $detail->rekap_total), 0, ',', '.') }}</p>
                </div>
            </div>
            @if($containers->isNotEmpty())
                <details class="temas-container-list px-4 py-2 border-b border-gray-100">
                    <summary class="cursor-pointer text-xs font-medium text-blue-700">Daftar nomor kontainer ({{ $containers->count() }})</summary>
                    <div class="mt-2 flex flex-wrap gap-1.5 pb-1">
                        @foreach($containers as $container)
                            <span class="rounded border border-gray-200 bg-gray-50 px-2 py-1 text-xs font-mono text-gray-600">{{ $container }}</span>
                        @endforeach
                    </div>
                </details>
            @endif
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-gray-50 text-gray-500">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold">Jenis Biaya</th>
                            <th class="px-3 py-2 text-left font-semibold">Ukuran</th>
                            <th class="px-3 py-2 text-right font-semibold">Qty</th>
                            <th class="px-3 py-2 text-right font-semibold">Tarif</th>
                            <th class="px-3 py-2 text-right font-semibold">Nominal Biaya</th>
                            <th class="px-3 py-2 text-right font-semibold">Total Biaya</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($details as $detail)
                            <tr>
                                <td class="px-3 py-2 text-gray-700">{{ $detail->jenis_biaya ?? '-' }}</td>
                                <td class="px-3 py-2 text-gray-600 whitespace-nowrap">{{ $detail->size ?: '-' }}</td>
                                <td class="px-3 py-2 text-right text-gray-600">{{ number_format((float) $detail->kuantitas, (float) $detail->kuantitas == floor((float) $detail->kuantitas) ? 0 : 2, ',', '.') }}</td>
                                <td class="px-3 py-2 text-right text-gray-600 whitespace-nowrap">Rp {{ number_format((float) $detail->harga, 0, ',', '.') }}</td>
                                <td class="px-3 py-2 text-right text-gray-700 whitespace-nowrap">Rp {{ number_format((float) $detail->sub_total, 0, ',', '.') }}</td>
                                <td class="px-3 py-2 text-right font-semibold text-gray-900 whitespace-nowrap">Rp {{ number_format((float) ($detail->rekap_bl_total ?? $detail->rekap_total), 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
</div>
