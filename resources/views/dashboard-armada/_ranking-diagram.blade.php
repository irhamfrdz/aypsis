<figure class="p-4 sm:p-6" aria-labelledby="ranking-diagram-title">
    <figcaption id="ranking-diagram-title" class="font-semibold text-gray-900">Diagram Utilisasi Armada</figcaption>
    <p class="mt-1 text-sm text-gray-500">Panjang batang menunjukkan persentase hari menghasilkan rit terhadap hari tersedia. Menampilkan armada pada halaman ini, sesuai urutan peringkat.</p>
    <div class="mt-3 flex flex-wrap gap-4 text-xs text-gray-600">
        <span class="inline-flex items-center gap-2"><span aria-hidden="true" class="h-3 w-3 rounded bg-indigo-600"></span>Data lengkap</span>
        <span class="inline-flex items-center gap-2"><span aria-hidden="true" class="h-3 w-3 rounded bg-amber-500"></span>Ranking sementara</span>
        <span class="inline-flex items-center gap-2"><span aria-hidden="true" class="h-3 w-3 rounded bg-gray-400"></span>Belum mendapat ranking</span>
    </div>
    <div class="mt-5 space-y-5">
        @forelse($rankings as $row)
            @php
                $utilization = $row['utilization'];
                $percent = $utilization !== null ? max(0, min(100, $utilization)) : null;
                $employee = $row['mobil']->karyawan?->nama_lengkap ?: ($row['mobil']->pemakai ?: '-');
                $barColor = $row['rank'] === null ? 'bg-gray-400' : ($row['rank_is_provisional'] ? 'bg-amber-500' : 'bg-indigo-600');
            @endphp
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-2 lg:gap-4 border-b border-gray-100 pb-4 last:border-0">
                <div class="text-sm">
                    <div class="flex items-center gap-2"><span class="font-bold text-indigo-700">{{ $row['rank'] !== null ? '#'.$row['rank'] : '-' }}</span><span class="font-semibold text-gray-900">{{ $row['mobil']->nomor_polisi ?: '-' }}</span></div>
                    <div class="mt-1 text-xs text-gray-600">{{ $employee }}</div>
                    <div class="text-xs text-gray-400">{{ $row['group'] }}</div>
                </div>
                <div class="lg:col-span-3">
                    <div class="mb-1 flex items-center justify-between gap-3 text-xs">
                        <span class="text-gray-500">{{ $row['plate_valid'] ? $row['productive_days'] : '-' }} / {{ $row['available_days'] }} hari menghasilkan rit · {{ $row['rit'] ?? '-' }} rit</span>
                        <span class="font-semibold text-gray-900">{{ $utilization !== null ? number_format($utilization, 2, ',', '.').'%' : 'Belum dapat dihitung' }}@if($utilization !== null && $row['gps_missing_days'] > 0) <span class="font-normal text-amber-700">(teramati)</span>@endif</span>
                    </div>
                    @if($percent !== null)
                        <div role="meter" aria-label="Utilisasi {{ $row['mobil']->nomor_polisi }}" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ round($percent, 2) }}" aria-valuetext="{{ number_format($percent, 2, ',', '.') }} persen" class="h-6 overflow-hidden rounded-md bg-gray-100" title="{{ $row['mobil']->nomor_polisi }}: {{ number_format($percent, 2, ',', '.') }}%">
                            <div class="h-full rounded-md {{ $barColor }}" style="width: {{ $percent }}%"></div>
                        </div>
                    @else
                        <div class="rounded-md border border-dashed border-gray-300 px-3 py-1 text-xs text-gray-500">Data belum memadai untuk menghitung utilisasi.</div>
                    @endif
                    <div aria-hidden="true" class="mt-1 flex justify-between text-[10px] text-gray-400"><span>0%</span><span>25%</span><span>50%</span><span>75%</span><span>100%</span></div>
                    @if($row['unranked_reason'])<p class="mt-1 text-xs text-gray-500">{{ $row['unranked_reason'] }}</p>
                    @elseif($row['rank_is_provisional'])
                        <p class="mt-1 text-xs text-amber-700">Sementara · {{ $row['gps_missing_days'] > 0 ? $row['gps_missing_days'].' hari data GPS armada ini belum tersedia.' : 'Data GPS armada lain belum lengkap.' }}</p>
                    @endif
                </div>
            </div>
        @empty
            <p class="py-8 text-center text-sm text-gray-500">Tidak ada armada yang sesuai dengan filter.</p>
        @endforelse
    </div>
</figure>
