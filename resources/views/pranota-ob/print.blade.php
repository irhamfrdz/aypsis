@extends('layouts.print')

@section('title', 'Print Pranota OB - ' . ($pranota->nomor_pranota ?? '-'))

@push('styles')
<style>
    @page {
        size: 21.5cm 16.5cm; /* Half folio size (setengah folio) */
        margin: 1cm;
    }
    
    @media print {
        body {
            width: 21.5cm;
            height: 16.5cm;
        }
        .print-container {
            width: 100%;
            height: 100%;
        }
    }
    
    /* Adjust font sizes for smaller paper */
    .print-container h2 {
        font-size: 14px;
    }
    .print-container p, .print-container td, .print-container th {
        font-size: 10px;
    }
    .print-container table {
        font-size: 11px;
    }
    
    /* Pertebal border table */
    .print-container table,
    .print-container table th,
    .print-container table td {
        border: 2px solid #000 !important;
        border-collapse: collapse;
    }
    .print-container table {
        border: 3px solid #000 !important;
    }
    
    /* Pertebal border untuk semua elemen table */
    table.border-collapse,
    table.border-collapse th,
    table.border-collapse td {
        border: 2px solid #000 !important;
    }
</style>
@endpush

@section('content')
    <div class="p-2 bg-white print-container">
        <div style="margin-bottom: 4px;">
            <h2 class="font-bold" style="margin: 0 0 2px 0; font-size: 24px;">Pranota OB</h2>
            <p class="font-bold" style="margin: 0; font-size: 14px; line-height: 1.3;">Nomor: {{ $pranota->nomor_pranota ?? '-' }}</p>
            <p class="font-bold" style="margin: 0; font-size: 14px; line-height: 1.3;">Voyage: {{ $pranota->no_voyage ?? '-' }}</p>
            <p class="font-bold" style="margin: 0; font-size: 14px; line-height: 1.3;">Tanggal OB: {{ $pranota->tanggal_ob ? \Carbon\Carbon::parse($pranota->tanggal_ob)->format('d/m/Y') : '-' }}</p>
            @if($pranota->nomor_accurate)
                <p class="font-bold" style="margin: 0; font-size: 14px; line-height: 1.3;">Nomor Accurate: {{ $pranota->nomor_accurate }}</p>
            @endif
        </div>

        <div class="mb-2">
            <h4 class="font-medium" style="margin: 0 0 2px 0; font-size: 10px;">Ringkasan Per Supir</h4>
            @php
                    // Hanya tampilkan supir yang memiliki nama (exclude TL / Perusahaan / kosong / '-' dll)
                    $normalizeName = function($n) {
                        $k = strtolower(trim($n ?? ''));
                        // remove any non-alphanumeric characters so placeholders like '-' normalize to empty
                        $k = preg_replace('/[^a-z0-9]/', '', $k);
                        return $k;
                    };

                    $filteredPerSupirCounts = collect($perSupirCounts)->filter(function($counts, $name) use($normalizeName) {
                        $k = $normalizeName($name);
                        return $k !== '' && $k !== 'perusahaan';
                    })->toArray();

                    $filteredPerSupir = collect($perSupir)->filter(function($sum, $name) use($normalizeName) {
                        $k = $normalizeName($name);
                        return $k !== '' && $k !== 'perusahaan';
                    })->toArray();

                    // Calculate totals from filtered data before rendering table
                    $totalFull20 = 0;
                    $totalEmpty20 = 0;
                    $totalFull40 = 0;
                    $totalEmpty40 = 0;

                    foreach($filteredPerSupirCounts as $supirName => $counts) {
                        $totalFull20 += $counts['sizes']['20']['full'] ?? 0;
                        $totalEmpty20 += $counts['sizes']['20']['empty'] ?? 0;
                        $totalFull40 += $counts['sizes']['40']['full'] ?? 0;
                        $totalEmpty40 += $counts['sizes']['40']['empty'] ?? 0;
                    }

                    $grandTotalKontainer = $totalFull20 + $totalEmpty20 + $totalFull40 + $totalEmpty40;
                @endphp
            <table class="min-w-full table-auto border-collapse" style="font-size: 10px;">
                <thead>
                    <tr>
                        <th class="border px-2 py-1 text-center" rowspan="2"></th>
                        @foreach($filteredPerSupirCounts as $supirName => $counts)
                            <th class="border px-2 py-1 text-center" colspan="2">{{ $supirName }}</th>
                        @endforeach
                        <th class="border px-2 py-1 text-center" colspan="2">TOTAL</th>
                        <th class="border px-2 py-1 text-center" rowspan="2">JUMLAH</th>
                    </tr>
                    <tr>
                        @foreach($filteredPerSupirCounts as $supirName => $counts)
                            <th class="border px-2 py-1 text-center">FULL</th>
                            <th class="border px-2 py-1 text-center">EMPTY</th>
                        @endforeach
                        <th class="border px-2 py-1 text-center">FULL</th>
                        <th class="border px-2 py-1 text-center">EMPTY</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Baris 20" --}}
                    <tr>
                        <td class="border px-2 py-1 text-center">20"</td>
                        @foreach($filteredPerSupirCounts as $supirName => $counts)
                            @php
                                $full20 = $counts['sizes']['20']['full'] ?? 0;
                                $empty20 = $counts['sizes']['20']['empty'] ?? 0;
                            @endphp
                            <td class="border px-2 py-1 text-center">{{ $full20 > 0 ? $full20 : '-' }}</td>
                            <td class="border px-2 py-1 text-center">{{ $empty20 > 0 ? $empty20 : '-' }}</td>
                        @endforeach
                        <td class="border px-2 py-1 text-center">{{ $totalFull20 > 0 ? $totalFull20 : '-' }}</td>
                        <td class="border px-2 py-1 text-center">{{ $totalEmpty20 > 0 ? $totalEmpty20 : '-' }}</td>
                        <td class="border px-2 py-1 text-right">{{ ($biayaPerSize['20'] ?? 0) > 0 ? number_format($biayaPerSize['20'], 0, ',', '.') : '-' }}</td>
                    </tr>

                    {{-- Baris 40" --}}
                    <tr>
                        <td class="border px-2 py-1 text-center">40"</td>
                        @foreach($filteredPerSupirCounts as $supirName => $counts)
                            @php
                                $full40 = $counts['sizes']['40']['full'] ?? 0;
                                $empty40 = $counts['sizes']['40']['empty'] ?? 0;
                            @endphp
                            <td class="border px-2 py-1 text-center">{{ $full40 > 0 ? $full40 : '-' }}</td>
                            <td class="border px-2 py-1 text-center">{{ $empty40 > 0 ? $empty40 : '-' }}</td>
                        @endforeach
                        <td class="border px-2 py-1 text-center">{{ $totalFull40 > 0 ? $totalFull40 : '-' }}</td>
                        <td class="border px-2 py-1 text-center">{{ $totalEmpty40 > 0 ? $totalEmpty40 : '-' }}</td>
                        <td class="border px-2 py-1 text-right">{{ ($biayaPerSize['40'] ?? 0) > 0 ? number_format($biayaPerSize['40'], 0, ',', '.') : '-' }}</td>
                    </tr>

                    {{-- Baris curah --}}
                    <tr>
                        <td class="border px-2 py-1 text-center">curah</td>
                        @foreach($filteredPerSupirCounts as $supirName => $counts)
                            <td class="border px-2 py-1 text-center" colspan="2">-</td>
                        @endforeach
                        <td class="border px-2 py-1 text-center" colspan="2">-</td>
                        <td class="border px-2 py-1 text-center">-</td>
                    </tr>

                    {{-- Baris curah pipa --}}
                    <tr>
                        <td class="border px-2 py-1 text-center">curah pipa</td>
                        @foreach($filteredPerSupirCounts as $supirName => $counts)
                            <td class="border px-2 py-1 text-center" colspan="2">-</td>
                        @endforeach
                        <td class="border px-2 py-1 text-center" colspan="2">-</td>
                        <td class="border px-2 py-1 text-center">-</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="font-semibold">
                        <td class="border px-2 py-1 text-center">JUMLAH</td>
                        @foreach($filteredPerSupir as $supirName => $sumBiaya)
                            <td class="border px-2 py-1 text-right" colspan="2">{{ number_format($sumBiaya, 0, ',', '.') }}</td>
                        @endforeach
                        <td class="border px-2 py-1 text-center" colspan="2"></td>
                        <td class="border px-2 py-1 text-right">{{ number_format($totalBiaya, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div style="margin-top: 8px;">
            <table class="min-w-full table-auto border-collapse">
                <tbody>
                    <tr>
                        <td class="px-2 py-1 text-xs font-medium">Total Kontainer</td>
                        <td class="px-2 py-1 text-xs">{{ $grandTotalKontainer }}</td>
                    </tr>
                    <tr>
                        <td class="px-2 py-1 text-xs font-medium">Total Kontainer TL</td>
                        <td class="px-2 py-1 text-xs">{{ $totalTlContainers ?? 0 }}</td>
                    </tr>
                    <tr>
                        <td class="px-2 py-1 text-xs font-medium">Total Biaya</td>
                        <td class="px-2 py-1 text-xs">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</td>
                    </tr>
                    @if(isset($pranota->adjustment) && $pranota->adjustment != 0)
                    <tr>
                        <td class="px-2 py-1 text-xs font-medium">Adjustment</td>
                        <td class="px-2 py-1 text-xs">
                            Rp {{ number_format($pranota->adjustment, 0, ',', '.') }}
                            @if(isset($pranota->keterangan) && !empty($pranota->keterangan))
                                <span class="ml-1">({{ $pranota->keterangan }})</span>
                            @endif
                        </td>
                    </tr>
                    <tr class="font-bold border-t border-gray-400">
                        <td class="px-2 py-1 text-xs font-medium">Total Bayar</td>
                        <td class="px-2 py-1 text-xs">Rp {{ number_format($totalBiaya + $pranota->adjustment, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="flex justify-between" style="font-size: 10px; margin-top: 6px;">
            <div>
                <p style="margin: 0;">Catatan: {{ $pranota->catatan ?? '-' }}</p>

            </div>
        </div>

        <!-- Signature Section with 3 signatures -->
        <div style="margin-top: 15px;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 33.33%; text-align: center; vertical-align: top; padding: 5px;">
                        <div style="margin-bottom: 30px; height: 1px;"></div>
                        <div style="font-size: 10px;">
                            (Pemohon)
                        </div>
                    </td>
                    <td style="width: 33.33%; text-align: center; vertical-align: top; padding: 5px;">
                        <div style="margin-bottom: 30px; height: 1px;"></div>
                        <div style="font-size: 10px;">
                            (Pemeriksa)
                        </div>
                    </td>
                    <td style="width: 33.33%; text-align: center; vertical-align: top; padding: 5px;">
                        <div style="margin-bottom: 30px; height: 1px;"></div>
                        <div style="font-size: 10px;">
                            (Kasir)
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    {{-- Page Break untuk halaman 2 --}}
    <div style="page-break-after: always;"></div>

    {{-- Halaman 2: Detail Kontainer Per Supir --}}
    <div class="p-2 bg-white print-container">
        <div style="margin-bottom: 8px;">
            <h2 class="font-semibold" style="margin: 0 0 2px 0; font-size: 11px;">{{ $pranota->tanggal_pranota ? \Carbon\Carbon::parse($pranota->tanggal_pranota)->format('d-m-Y') : date('d-m-Y') }}</h2>
        </div>

        @php
            // Filter out items without supir or with TL/perusahaan or placeholder names (like '-') then sort by supir
            $normalizeName = function($n) {
                $k = strtolower(trim($n ?? ''));
                $k = preg_replace('/[^a-z0-9]/', '', $k);
                return $k;
            };

            $sortedItems = collect($displayItems)->filter(function($item) use($normalizeName) {
                $rawName = $item['supir'] ?? ($item['nama_supir'] ?? '');
                $name = $normalizeName($rawName);
                $isTl = ($item['is_tl'] ?? false) == 1 || 
                        ($item['is_tl'] ?? false) === true || 
                        ($item['is_tl'] ?? false) === '1' ||
                        (($item['biaya'] ?? 0) === null || ($item['biaya'] ?? 0) == 0);
                
                return ($name !== '' && $name !== 'perusahaan') || $isTl;
            })->map(function($item) use($normalizeName) {
                $rawName = $item['supir'] ?? ($item['nama_supir'] ?? '');
                $name = $normalizeName($rawName);
                if ($name === '' || $name === 'perusahaan') {
                    $isTl = ($item['is_tl'] ?? false) == 1 || 
                            ($item['is_tl'] ?? false) === true || 
                            ($item['is_tl'] ?? false) === '1' ||
                            (($item['biaya'] ?? 0) === null || ($item['biaya'] ?? 0) == 0);
                    if ($isTl) {
                        $item['supir'] = 'TL';
                    }
                }
                return $item;
            })->sortBy('supir')->values();
            $totalItems = $sortedItems->count();
            $halfCount = ceil($totalItems / 2);
            $leftItems = $sortedItems->take($halfCount);
            $rightItems = $sortedItems->skip($halfCount);
        @endphp

        <div style="display: flex; gap: 8px;">
            {{-- Kolom Kiri --}}
            <div style="flex: 1;">
                <table class="table-auto border-collapse" style="width: 100%; font-size: 11px;">
                    <thead>
                        <tr style="background-color: #f3f4f6;">
                            <th class="border px-1 py-0.5 text-center" style="width: 5%;">No</th>
                            <th class="border px-1 py-0.5 text-left" style="width: 25%;">No.Container</th>
                            <th class="border px-1 py-0.5 text-center" style="width: 10%;">Size</th>
                            <th class="border px-1 py-0.5 text-left" style="width: 60%;">NamaSupir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @forelse($leftItems as $item)
                            <tr>
                                <td class="border px-1 py-0.5 text-center">{{ $no++ }}</td>
                                <td class="border px-1 py-0.5" style="font-size: 10px;">{{ $item['nomor_kontainer'] ?? '-' }}</td>
                                <td class="border px-1 py-0.5 text-center">
                                    @php
                                        $size = $item['size'] ?? ($item['size_kontainer'] ?? ($item['ukuran_kontainer'] ?? '-'));
                                        if (str_contains($size, '40')) {
                                            echo '40 ft';
                                        } elseif (str_contains($size, '20')) {
                                            echo '20 ft';
                                        } else {
                                            echo $size;
                                        }
                                    @endphp
                                </td>
                                <td class="border px-1 py-0.5" style="font-size: 10px;">{{ $item['supir'] ?? ($item['nama_supir'] ?? '-') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="border px-1 py-0.5 text-center text-gray-500">
                                    Tidak ada data
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Kolom Kanan --}}
            <div style="flex: 1;">
                <table class="table-auto border-collapse" style="width: 100%; font-size: 11px;">
                    <thead>
                        <tr style="background-color: #f3f4f6;">
                            <th class="border px-1 py-0.5 text-center" style="width: 5%;">No</th>
                            <th class="border px-1 py-0.5 text-left" style="width: 25%;">No.Container</th>
                            <th class="border px-1 py-0.5 text-center" style="width: 10%;">Size</th>
                            <th class="border px-1 py-0.5 text-left" style="width: 60%;">NamaSupir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = $halfCount + 1; @endphp
                        @forelse($rightItems as $item)
                            <tr>
                                <td class="border px-1 py-0.5 text-center">{{ $no++ }}</td>
                                <td class="border px-1 py-0.5" style="font-size: 10px;">{{ $item['nomor_kontainer'] ?? '-' }}</td>
                                <td class="border px-1 py-0.5 text-center">
                                    @php
                                        $size = $item['size'] ?? ($item['size_kontainer'] ?? ($item['ukuran_kontainer'] ?? '-'));
                                        if (str_contains($size, '40')) {
                                            echo '40 ft';
                                        } elseif (str_contains($size, '20')) {
                                            echo '20 ft';
                                        } else {
                                            echo $size;
                                        }
                                    @endphp
                                </td>
                                <td class="border px-1 py-0.5" style="font-size: 10px;">{{ $item['supir'] ?? ($item['nama_supir'] ?? '-') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="border px-1 py-0.5 text-center text-gray-500">
                                    Tidak ada data
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
