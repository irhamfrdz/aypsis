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
        @endphp
        <div class="rounded-lg border border-blue-100 bg-white overflow-hidden">
            <div class="px-3 py-2 bg-blue-50 flex flex-wrap items-start justify-between gap-2 text-xs">
                <div>
                    <p class="font-bold text-blue-900">
                        @if($nomorBl !== '')
                            BL: {{ $nomorBl }}
                        @elseif($containers->isNotEmpty())
                            Detail per kontainer
                        @else
                            Pembayaran TEMAS tanpa rincian BL / kontainer
                        @endif
                    </p>
                    @if($containers->isNotEmpty())
                        <p class="mt-1 text-gray-600 break-words">Kontainer: {{ $containers->implode(', ') }}</p>
                    @endif
                </div>
                <span class="font-bold text-blue-900 whitespace-nowrap">Total Rp {{ number_format($details->sum('grand_total'), 0, ',', '.') }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-gray-50 text-gray-500">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold">Jenis Biaya</th>
                            <th class="px-3 py-2 text-left font-semibold">Kontainer / Ukuran</th>
                            <th class="px-3 py-2 text-right font-semibold">Qty</th>
                            <th class="px-3 py-2 text-right font-semibold">Tarif</th>
                            <th class="px-3 py-2 text-right font-semibold">Nominal Biaya</th>
                            <th class="px-3 py-2 text-right font-semibold">Total Bersih</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($details as $detail)
                            <tr>
                                <td class="px-3 py-2 text-gray-700">{{ $detail->jenis_biaya ?? '-' }}</td>
                                <td class="px-3 py-2 text-gray-600 break-words">
                                    {{ $detail->nomor_kontainer ?: '-' }}
                                    @if($detail->size)
                                        <span class="block text-gray-400">{{ $detail->size }}</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 text-right text-gray-600">{{ number_format((float) $detail->kuantitas, (float) $detail->kuantitas == floor((float) $detail->kuantitas) ? 0 : 2, ',', '.') }}</td>
                                <td class="px-3 py-2 text-right text-gray-600 whitespace-nowrap">Rp {{ number_format((float) $detail->harga, 0, ',', '.') }}</td>
                                <td class="px-3 py-2 text-right text-gray-700 whitespace-nowrap">Rp {{ number_format((float) $detail->sub_total, 0, ',', '.') }}</td>
                                <td class="px-3 py-2 text-right font-semibold text-gray-900 whitespace-nowrap">Rp {{ number_format((float) $detail->grand_total, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
</div>
