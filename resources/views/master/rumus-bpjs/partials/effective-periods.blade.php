@php
    $labels = [
        'nominal_dpp' => 'DPP JKN', 'hutang_tiers' => 'DPP Jamsostek',
        'jp_max_dpp' => 'Maksimal DPP JP', 'jp_max_age' => 'Batas Usia JP',
        'diskon_status' => 'Diskon', 'diskon_tipe' => 'Diskon', 'diskon_nilai' => 'Diskon',
    ];
    $periods = $groupItems->flatMap(function ($formula) use ($labels) {
        return collect($formula->bpjs_effective_values ?? [])
            ->filter(fn ($entry) => !empty($entry['valid_from']) && !empty($entry['valid_until']))
            ->map(function ($entry) use ($formula, $labels) {
                return [
                    'group' => $formula->group_name,
                    'variables' => collect(array_keys($entry['values'] ?? []))
                        ->filter(fn ($field) => isset($labels[$field]))
                        ->map(fn ($field) => $labels[$field])->unique()->implode(', '),
                    'from' => $entry['valid_from'], 'until' => $entry['valid_until'],
                ];
            })->filter(fn ($period) => $period['variables'] !== '');
    })->unique(fn ($period) => json_encode($period))->values();
@endphp
@if($periods->isNotEmpty())
    <table class="text-xs whitespace-nowrap" aria-label="Masa berlaku variabel rumus">
        <thead>
            <tr class="text-gray-500 border-b border-gray-200">
                <th scope="col" class="pr-3 pb-1 text-left font-medium">Variabel</th>
                <th scope="col" class="pr-3 pb-1 text-left font-medium">Tanggal Awal</th>
                <th scope="col" class="pb-1 text-left font-medium">Tanggal Akhir</th>
            </tr>
        </thead>
        <tbody>
            @foreach($periods as $period)
                <tr>
                    <td class="pr-3 py-1 text-gray-700">
                        {{ $period['variables'] }}
                        @if($groupItems->count() > 1)
                            <span class="block text-gray-500">{{ $period['group'] }}</span>
                        @endif
                    </td>
                    <td class="pr-3 py-1 text-gray-600">{{ \Carbon\Carbon::parse($period['from'])->format('d/m/Y') }}</td>
                    <td class="py-1 text-gray-600">{{ \Carbon\Carbon::parse($period['until'])->format('d/m/Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <span class="text-gray-300">—</span>
@endif
