<style>
    #ranking-armada .armada-ranking-table { width: 100%; table-layout: fixed; }
    #ranking-armada .armada-ranking-table th,
    #ranking-armada .armada-ranking-table td { overflow-wrap: anywhere; vertical-align: top; }
    #ranking-armada .armada-ranking-label { display: none; }
    @media (max-width: 767px) {
        #ranking-armada .armada-ranking-table,
        #ranking-armada .armada-ranking-table tbody,
        #ranking-armada .armada-ranking-table tr,
        #ranking-armada .armada-ranking-table td { display: block; width: 100%; }
        #ranking-armada .armada-ranking-table thead,
        #ranking-armada .armada-ranking-table colgroup { display: none; }
        #ranking-armada .armada-ranking-label { display: block; font-size: 11px; color: #6b7280; margin-bottom: 4px; }
        #ranking-armada .armada-ranking-table td { box-sizing: border-box; padding: 8px 16px; }
    }
</style>
<table class="armada-ranking-table divide-y divide-gray-200 text-sm">
    <colgroup><col style="width: 9%"><col style="width: 29%"><col style="width: 16%"><col style="width: 23%"><col style="width: 23%"></colgroup>
    <thead class="bg-indigo-50 text-xs uppercase text-gray-600"><tr>
        @foreach(['Peringkat', 'Armada / Karyawan Pengguna', 'Utilisasi', 'Rit & Ketersediaan', 'Data GPS'] as $heading)
            <th scope="col" class="px-3 py-3 text-left">{{ $heading }}</th>
        @endforeach
    </tr></thead>
    <tbody>
        @forelse($rankings as $row)
            <tr class="border-t border-gray-200 hover:bg-gray-50">
                <td class="px-3 pt-4 pb-2">
                    <span class="armada-ranking-label">Peringkat</span>
                    <span class="text-lg font-bold {{ $row['rank'] === 1 ? 'text-amber-600' : 'text-gray-600' }}">{{ $row['rank'] !== null ? '#'.$row['rank'] : '-' }}</span>
                    @if($row['rank_is_provisional'])<div class="mt-1 text-xs text-amber-700">Sementara</div>@endif
                </td>
                <td class="px-3 pt-4 pb-2">
                    <span class="armada-ranking-label">Armada / Karyawan Pengguna</span>
                    <div class="font-semibold text-gray-900">{{ $row['mobil']->nomor_polisi ?: '-' }}</div>
                    <div class="text-xs text-gray-500">{{ $row['group'] }}</div>
                    <div class="mt-2 font-medium text-gray-700">{{ $row['mobil']->karyawan?->nama_lengkap ?: ($row['mobil']->pemakai ?: '-') }}</div>
                    @if($row['mobil']->karyawan?->nik)<div class="text-xs text-gray-500">NIK {{ $row['mobil']->karyawan->nik }}</div>@endif
                </td>
                <td class="px-3 pt-4 pb-2">
                    <span class="armada-ranking-label">Utilisasi</span>
                    <div class="text-lg font-bold text-indigo-700">{{ $row['utilization'] !== null ? number_format($row['utilization'], 2, ',', '.').'%' : '-' }}</div>
                    @if($row['gps_missing_days'] > 0)<div class="mt-1 text-xs text-amber-700">Teramati / belum final</div>@endif
                </td>
                <td class="px-3 pt-4 pb-2">
                    <span class="armada-ranking-label">Rit & Ketersediaan</span>
                    <div class="font-semibold text-gray-900">{{ $row['rit'] ?? '-' }} rit</div>
                    <div class="mt-1 text-xs text-gray-600">{{ $row['plate_valid'] ? $row['productive_days'] : '-' }} dari {{ $row['available_days'] }} hari menghasilkan rit</div>
                    <div class="mt-1 text-xs text-gray-500">{{ $row['average_rit'] !== null ? number_format($row['average_rit'], 2, ',', '.') : '-' }} rit / hari tersedia</div>
                </td>
                <td class="px-3 pt-4 pb-2">
                    <span class="armada-ranking-label">Data GPS</span>
                    <div class="font-semibold text-gray-700">{{ $row['gps_km'] !== null ? number_format($row['gps_km'], 3, ',', '.').' km' : '-' }}</div>
                    <div class="mt-1 text-xs text-gray-600">Cakupan {{ $row['gps_coverage'] !== null ? number_format($row['gps_coverage'], 1, ',', '.').'%' : '-' }}</div>
                    <div class="mt-1 text-xs text-gray-500">{{ $row['gps_missing_days'] }} hari belum ada data</div>
                </td>
            </tr>
            <tr>
                <td colspan="5" class="px-3 pb-4 pt-2">
                    @if($row['unranked_reason'])<p class="mb-2 text-xs text-amber-700">{{ $row['unranked_reason'] }}</p>
                    @elseif($row['data_note'])<p class="mb-2 text-xs text-amber-700">{{ $row['data_note'] }}</p>
                    @elseif($row['rank_is_provisional'])<p class="mb-2 text-xs text-amber-700">Ranking sementara karena data GPS armada lain belum lengkap.</p>@endif
                    <details>
                        <summary class="cursor-pointer text-xs font-medium text-indigo-600">Lihat rincian harian dan sopir</summary>
                        <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
                            <div class="rounded-lg bg-gray-50 p-3"><span class="text-gray-500">Tidak tersedia</span><p class="mt-1 font-semibold">{{ $row['unavailable_days'] }} hari</p></div>
                            <div class="rounded-lg bg-gray-50 p-3"><span class="text-gray-500">Bergerak tanpa rit selesai</span><p class="mt-1 font-semibold">{{ $row['plate_valid'] ? $row['moving_without_rit_days'] : '-' }} hari</p></div>
                            <div class="rounded-lg bg-gray-50 p-3"><span class="text-gray-500">Tanpa aktivitas tercatat</span><p class="mt-1 font-semibold">{{ $row['plate_valid'] ? $row['idle_days'] : '-' }} hari</p></div>
                            <div class="rounded-lg bg-gray-50 p-3"><span class="text-gray-500">Data belum lengkap</span><p class="mt-1 font-semibold">{{ $row['unknown_days'] }} hari</p></div>
                        </div>
                        <div class="mt-3 text-xs text-gray-600">
                            @forelse($row['rit_per_supir'] as $nama => $rit)<p>{{ $nama }}: {{ $rit }} rit</p>@empty<p>Belum ada sopir dengan rit selesai.</p>@endforelse
                        </div>
                        <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-2">
                            @foreach($row['daily'] as $day)
                                <div class="rounded-lg border border-gray-200 p-3 text-xs">
                                    <p class="font-semibold text-gray-900">{{ $day['date'] }}</p><p class="mt-1 text-gray-600">{{ $day['state'] }}</p>
                                    <p class="mt-1 text-gray-500">{{ $day['rit'] }} rit · GPS {{ $day['gps_km'] !== null ? number_format($day['gps_km'], 3, ',', '.').' km' : 'belum tersedia' }}</p>
                                </div>
                            @endforeach
                        </div>
                    </details>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Tidak ada armada yang sesuai dengan filter.</td></tr>
        @endforelse
    </tbody>
</table>
