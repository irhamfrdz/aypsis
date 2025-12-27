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
                        @php 
                            $totalFull20 = 0;
                            $totalEmpty20 = 0;
                            $biaya20 = 0;
                        @endphp
                        @foreach($perSupirCounts as $supirName => $counts)
                            @php
                                $full20 = $counts['sizes']['20']['full'] ?? 0;
                                $empty20 = $counts['sizes']['20']['empty'] ?? 0;
                                $totalFull20 += $full20;
                                $totalEmpty20 += $empty20;
                                
                                // Calculate biaya untuk 20" kontainer supir ini
                                $biaya20Supir = 0;
                                if (isset($pranotaItems)) {
                                    foreach ($pranotaItems as $item) {
                                        if (($item['supir'] ?? '') == $supirName && ($item['size'] ?? '') == '20') {
                                            $biaya20Supir += floatval($item['biaya'] ?? 0);
                                        }
                                    }
                                }
                                $biaya20 += $biaya20Supir;
                            @endphp
                            <td class="border px-2 py-1 text-center">{{ $full20 > 0 ? $full20 : '-' }}</td>
                            <td class="border px-2 py-1 text-center">{{ $empty20 > 0 ? $empty20 : '-' }}</td>
                        @endforeach
                        <td class="border px-2 py-1 text-center">{{ $totalFull20 > 0 ? $totalFull20 : '-' }}</td>
                        <td class="border px-2 py-1 text-center">{{ $totalEmpty20 > 0 ? $totalEmpty20 : '-' }}</td>
                        <td class="border px-2 py-1 text-right">{{ $biaya20 > 0 ? number_format($biaya20, 0, ',', '.') : '-' }}</td>
                    </tr>

                    {{-- Baris 40" --}}
                    <tr>
                        <td class="border px-2 py-1 text-center">40"</td>
                        @php 
                            $totalFull40 = 0;
                            $totalEmpty40 = 0;
                            $biaya40 = 0;
                        @endphp
                        @foreach($perSupirCounts as $supirName => $counts)
                            @php
                                $full40 = $counts['sizes']['40']['full'] ?? 0;
                                $empty40 = $counts['sizes']['40']['empty'] ?? 0;
                                $totalFull40 += $full40;
                                $totalEmpty40 += $empty40;
                                
                                // Calculate biaya untuk 40" kontainer supir ini
                                $biaya40Supir = 0;
                                if (isset($pranotaItems)) {
                                    foreach ($pranotaItems as $item) {
                                        if (($item['supir'] ?? '') == $supirName && ($item['size'] ?? '') == '40') {
                                            $biaya40Supir += floatval($item['biaya'] ?? 0);
                                        }
                                    }
                                }
                                $biaya40 += $biaya40Supir;
                            @endphp
                            <td class="border px-2 py-1 text-center">{{ $full40 > 0 ? $full40 : '-' }}</td>
                            <td class="border px-2 py-1 text-center">{{ $empty40 > 0 ? $empty40 : '-' }}</td>
                        @endforeach
                        <td class="border px-2 py-1 text-center">{{ $totalFull40 > 0 ? $totalFull40 : '-' }}</td>
                        <td class="border px-2 py-1 text-center">{{ $totalEmpty40 > 0 ? $totalEmpty40 : '-' }}</td>
                        <td class="border px-2 py-1 text-right">{{ $biaya40 > 0 ? number_format($biaya40, 0, ',', '.') : '-' }}</td>
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
                <!-- Extra space for handwritten signature -->
                <div style="height:80px;"></div>
                <!-- Wider underline for signature -->
                <p style="display:inline-block; width:320px; border-bottom:1px solid #000; margin:0;"></p>
                <p class="mt-2 text-xs" style="margin-top:6px;">(Nama &amp; Tanda Tangan)</p>
            </div>
        </div>
    </div>
@endsection
