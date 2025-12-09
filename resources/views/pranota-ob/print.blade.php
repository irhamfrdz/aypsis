@extends('layouts.print')

@section('title', 'Print Pranota OB - ' . ($pranota->nomor_pranota ?? '-'))

@section('content')
    <div class="p-6 bg-white">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h2 class="text-2xl font-semibold">Pranota OB</h2>
                <p class="text-sm">Nomor: {{ $pranota->nomor_pranota ?? '-' }}</p>
                <!-- Kapal / Voyage removed as per request -->
            </div>
            <!-- Right column removed as per request (no print of Tanggal Cetak / Pembuat) -->
        </div>

        <div class="mb-4">
            <h4 class="text-sm font-medium mb-1">Ringkasan Per Supir</h4>
            <table class="min-w-full table-auto border-collapse text-xs">
                <thead>
                    <tr>
                        <th class="border px-2 py-1" rowspan="2">Keterangan</th>
                        @foreach($perSupirCounts as $supirName => $counts)
                            <th class="border px-2 py-1 text-center" colspan="2">{{ $supirName }}</th>
                        @endforeach
                        <th class="border px-2 py-1 text-center" rowspan="2">Total</th>
                    </tr>
                    <tr>
                        @foreach($perSupirCounts as $supirName => $counts)
                            <th class="border px-2 py-1 text-center">20'</th>
                            <th class="border px-2 py-1 text-center">40'</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @php
                        $rows = [
                            ['label' => "Full", 'status' => 'full'],
                            ['label' => "Empty", 'status' => 'empty'],
                        ];
                    @endphp

                    @foreach($rows as $r)
                        <tr>
                            <td class="border px-2 py-1">{{ $r['label'] }}</td>
                            @php $sumRow = 0; @endphp
                            @foreach($perSupirCounts as $supirName => $counts)
                                @php
                                    $val20 = $counts['sizes']['20'][$r['status']] ?? 0;
                                    $val40 = $counts['sizes']['40'][$r['status']] ?? 0;
                                    $sumRow += ($val20 + $val40);
                                @endphp
                                <td class="border px-2 py-1 text-center">{{ $val20 }}</td>
                                <td class="border px-2 py-1 text-center">{{ $val40 }}</td>
                            @endforeach
                            <td class="border px-2 py-1 text-center">{{ $sumRow }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td class="border px-2 py-1">JUMLAH</td>
                        @php $grand = 0; @endphp
                        @foreach($perSupirCounts as $supirName => $counts)
                            @php
                                $val20 = $counts['sizes']['20']['full'] ?? 0 + ($counts['sizes']['20']['empty'] ?? 0);
                                $val40 = $counts['sizes']['40']['full'] ?? 0 + ($counts['sizes']['40']['empty'] ?? 0);
                                $colTotal = $val20 + $val40;
                                $grand += $colTotal;
                            @endphp
                            <td class="border px-2 py-1 text-center">{{ $val20 }}</td>
                            <td class="border px-2 py-1 text-center">{{ $val40 }}</td>
                        @endforeach
                        <td class="border px-2 py-1 text-center">{{ $grand }}</td>
                    </tr>
                    <tr>
                        <td class="border px-2 py-1">BIAYA</td>
                        @php $totalBiayaCol = 0; @endphp
                        @foreach($perSupir as $supirName => $sumBiaya)
                            @php $totalBiayaCol += $sumBiaya; @endphp
                            <td class="border px-2 py-1 text-right" colspan="2">Rp {{ number_format($sumBiaya, 0, ',', '.') }}</td>
                        @endforeach
                        <td class="border px-2 py-1 text-right">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="mt-4">
            <table class="min-w-full table-auto border-collapse">
                <tbody>
                    <tr>
                        <td class="px-3 py-2 text-sm font-medium">Total Kontainer</td>
                        <td class="px-3 py-2 text-sm">{{ array_sum(array_map(function($c){ return array_sum(array_map('array_sum', array_column($c['sizes'], null))); }, $perSupirCounts)) ?? 0 }}</td>
                    </tr>
                    <tr>
                        <td class="px-3 py-2 text-sm font-medium">Total Biaya</td>
                        <td class="px-3 py-2 text-sm">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex justify-between text-sm">
            <div>
                <p>Catatan:</p>
                <p>{{ $pranota->catatan ?? '-' }}</p>
            </div>
            <div class="text-right">
                <p>Disiapkan oleh:</p>
                <p class="mt-12">____________________</p>
            </div>
        </div>
    </div>
@endsection
