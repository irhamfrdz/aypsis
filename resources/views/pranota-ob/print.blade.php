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
        font-size: 16px;
    }
    .print-container p, .print-container td, .print-container th {
        font-size: 9px;
    }
    .print-container table {
        font-size: 9px;
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
    <div class="p-4 bg-white print-container">
        <div class="flex justify-between items-start mb-2">
            <div style="line-height: 1.2;">
                <h2 class="text-xl font-semibold" style="margin-bottom: 2px;">Pranota OB</h2>
                <p class="text-xs" style="margin-bottom: 1px;">Nomor: {{ $pranota->nomor_pranota ?? '-' }}</p>
                <p class="text-xs" style="margin-bottom: 1px;">Kapal: {{ $pranota->nama_kapal ?? '-' }}</p>
                <p class="text-xs" style="margin-bottom: 0;">Voyage: {{ $pranota->no_voyage ?? '-' }}</p>
            </div>
            <!-- Right column removed as per request (no print of Tanggal Cetak / Pembuat) -->
        </div>

        <div class="mb-3">
            <h4 class="text-xs font-medium mb-1">Ringkasan Per Supir</h4>
            @php
                // Calculate totals before rendering table
                $totalFull20 = 0;
                $totalEmpty20 = 0;
                $totalFull40 = 0;
                $totalEmpty40 = 0;
                
                foreach($perSupirCounts as $supirName => $counts) {
                    $totalFull20 += $counts['sizes']['20']['full'] ?? 0;
                    $totalEmpty20 += $counts['sizes']['20']['empty'] ?? 0;
                    $totalFull40 += $counts['sizes']['40']['full'] ?? 0;
                    $totalEmpty40 += $counts['sizes']['40']['empty'] ?? 0;
                }
                
                $grandTotalKontainer = $totalFull20 + $totalEmpty20 + $totalFull40 + $totalEmpty40;
            @endphp
            <table class="min-w-full table-auto border-collapse" style="font-size: 8px;">
                <thead>
                    <tr>
                        <th class="border px-2 py-1 text-center" rowspan="2"></th>
                        @foreach($perSupirCounts as $supirName => $counts)
                            <th class="border px-2 py-1 text-center" colspan="2">{{ $supirName }}</th>
                        @endforeach
                        <th class="border px-2 py-1 text-center" colspan="2">TOTAL</th>
                        <th class="border px-2 py-1 text-center" rowspan="2">JUMLAH</th>
                    </tr>
                    <tr>
                        @foreach($perSupirCounts as $supirName => $counts)
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
                        @foreach($perSupirCounts as $supirName => $counts)
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
                        @foreach($perSupirCounts as $supirName => $counts)
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
                        @foreach($perSupirCounts as $supirName => $counts)
                            <td class="border px-2 py-1 text-center" colspan="2">-</td>
                        @endforeach
                        <td class="border px-2 py-1 text-center" colspan="2">-</td>
                        <td class="border px-2 py-1 text-center">-</td>
                    </tr>

                    {{-- Baris curah pipa --}}
                    <tr>
                        <td class="border px-2 py-1 text-center">curah pipa</td>
                        @foreach($perSupirCounts as $supirName => $counts)
                            <td class="border px-2 py-1 text-center" colspan="2">-</td>
                        @endforeach
                        <td class="border px-2 py-1 text-center" colspan="2">-</td>
                        <td class="border px-2 py-1 text-center">-</td>
                    </tr>

                    {{-- Baris TL (Tanda Langsung) --}}
                    <tr>
                        <td class="border px-2 py-1 text-center">TL</td>
                        @foreach($perSupirCounts as $supirName => $counts)
                            <td class="border px-2 py-1 text-center" colspan="2">-</td>
                        @endforeach
                        <td class="border px-2 py-1 text-center" colspan="2">{{ $totalTlContainers ?? 0 }}</td>
                        <td class="border px-2 py-1 text-center">-</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="font-semibold">
                        <td class="border px-2 py-1 text-center">JUMLAH</td>
                        @foreach($perSupir as $supirName => $sumBiaya)
                            <td class="border px-2 py-1 text-right" colspan="2">{{ number_format($sumBiaya, 0, ',', '.') }}</td>
                        @endforeach
                        <td class="border px-2 py-1 text-center" colspan="2"></td>
                        <td class="border px-2 py-1 text-right">{{ number_format($totalBiaya, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="mt-3">
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
                </tbody>
            </table>
        </div>

        <div class="mt-4 flex justify-between" style="font-size: 9px;">
            <div>
                <p>Catatan:</p>
                <p>{{ $pranota->catatan ?? '-' }}</p>
            </div>
        </div>

        <!-- Signature Section with 3 signatures -->
        <div style="margin-top: 30px;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 33.33%; text-align: center; vertical-align: top; padding: 10px;">
                        <div style="margin-bottom: 40px; height: 1px;"></div>
                        <div style="font-size: 9px;">
                            (Pemohon)
                        </div>
                    </td>
                    <td style="width: 33.33%; text-align: center; vertical-align: top; padding: 10px;">
                        <div style="margin-bottom: 40px; height: 1px;"></div>
                        <div style="font-size: 9px;">
                            (Pemeriksa)
                        </div>
                    </td>
                    <td style="width: 33.33%; text-align: center; vertical-align: top; padding: 10px;">
                        <div style="margin-bottom: 40px; height: 1px;"></div>
                        <div style="font-size: 9px;">
                            (Kasir)
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
@endsection
