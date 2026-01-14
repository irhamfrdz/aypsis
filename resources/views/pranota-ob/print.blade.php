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
        font-size: 12px;
    }
    .print-container p, .print-container td, .print-container th {
        font-size: 8px;
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
    <div class="p-2 bg-white print-container">
        <div style="margin-bottom: 4px;">
            <h2 class="font-semibold" style="margin: 0 0 1px 0; font-size: 11px;">Pranota OB</h2>
            <p style="margin: 0; font-size: 7px; line-height: 1.3;">Nomor: {{ $pranota->nomor_pranota ?? '-' }}</p>
            <p style="margin: 0; font-size: 7px; line-height: 1.3;">Kapal: {{ $pranota->nama_kapal ?? '-' }}</p>
            <p style="margin: 0; font-size: 7px; line-height: 1.3;">Voyage: {{ $pranota->no_voyage ?? '-' }}</p>
        </div>

        <div class="mb-2">
            <h4 class="font-medium" style="margin: 0 0 2px 0; font-size: 8px;">Ringkasan Per Supir</h4>
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
                </tbody>
            </table>
        </div>

        <div class="flex justify-between" style="font-size: 8px; margin-top: 6px;">
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
                        <div style="font-size: 8px;">
                            (Pemohon)
                        </div>
                    </td>
                    <td style="width: 33.33%; text-align: center; vertical-align: top; padding: 5px;">
                        <div style="margin-bottom: 30px; height: 1px;"></div>
                        <div style="font-size: 8px;">
                            (Pemeriksa)
                        </div>
                    </td>
                    <td style="width: 33.33%; text-align: center; vertical-align: top; padding: 5px;">
                        <div style="margin-bottom: 30px; height: 1px;"></div>
                        <div style="font-size: 8px;">
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
            <h2 class="font-semibold" style="margin: 0 0 2px 0; font-size: 11px;">Detail Kontainer Per Supir</h2>
            <p style="margin: 0; font-size: 7px; line-height: 1.3;">Nomor: {{ $pranota->nomor_pranota ?? '-' }}</p>
            <p style="margin: 0; font-size: 7px; line-height: 1.3;">Kapal: {{ $pranota->nama_kapal ?? '-' }} | Voyage: {{ $pranota->no_voyage ?? '-' }}</p>
        </div>

        @foreach($perSupirCounts as $supirName => $counts)
            <div style="margin-bottom: 10px;">
                <h4 class="font-medium" style="margin: 0 0 3px 0; font-size: 9px; background-color: #f3f4f6; padding: 3px 6px; border-left: 3px solid #6b7280;">
                    {{ $supirName }}
                </h4>
                
                <table class="min-w-full table-auto border-collapse" style="font-size: 7px;">
                    <thead>
                        <tr>
                            <th class="border px-2 py-1 text-center" style="width: 5%;">No</th>
                            <th class="border px-2 py-1 text-left" style="width: 20%;">No. Kontainer</th>
                            <th class="border px-2 py-1 text-center" style="width: 10%;">Size</th>
                            <th class="border px-2 py-1 text-center" style="width: 10%;">Status</th>
                            <th class="border px-2 py-1 text-left" style="width: 25%;">Barang</th>
                            <th class="border px-2 py-1 text-left" style="width: 20%;">PT. Pengirim</th>
                            <th class="border px-2 py-1 text-right" style="width: 10%;">Biaya</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            // Get all gate outs for this supir
                            $gateOuts = $pranota->gateOuts->where('nama_supir', $supirName);
                            $no = 1;
                        @endphp
                        
                        @forelse($gateOuts as $gateOut)
                            <tr>
                                <td class="border px-2 py-1 text-center">{{ $no++ }}</td>
                                <td class="border px-2 py-1">{{ $gateOut->nomor_kontainer ?? '-' }}</td>
                                <td class="border px-2 py-1 text-center">
                                    @if($gateOut->ukuran_kontainer)
                                        {{ $gateOut->ukuran_kontainer }}"
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="border px-2 py-1 text-center">
                                    {{ strtoupper($gateOut->status_kontainer ?? '-') }}
                                </td>
                                <td class="border px-2 py-1">{{ $gateOut->jenis_barang ?? '-' }}</td>
                                <td class="border px-2 py-1">{{ $gateOut->pt_pengirim ?? '-' }}</td>
                                <td class="border px-2 py-1 text-right">
                                    {{ number_format($gateOut->biaya_ob ?? 0, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="border px-2 py-1 text-center text-gray-500">
                                    Tidak ada data kontainer
                                </td>
                            </tr>
                        @endforelse
                        
                        {{-- Subtotal per supir --}}
                        <tr class="font-semibold" style="background-color: #f9fafb;">
                            <td colspan="6" class="border px-2 py-1 text-right">SUBTOTAL {{ strtoupper($supirName) }}:</td>
                            <td class="border px-2 py-1 text-right">
                                {{ number_format($perSupir[$supirName] ?? 0, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endforeach

        {{-- Grand Total di akhir halaman 2 --}}
        <div style="margin-top: 10px; padding: 5px; background-color: #f3f4f6; border: 2px solid #000;">
            <table style="width: 100%; font-size: 9px; font-weight: bold;">
                <tr>
                    <td style="text-align: right; padding-right: 10px;">TOTAL KESELURUHAN:</td>
                    <td style="text-align: right; width: 120px;">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>
    </div>
@endsection
