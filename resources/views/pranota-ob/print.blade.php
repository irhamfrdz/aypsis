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
            <table class="min-w-full table-auto border-collapse">
                <thead>
                    <tr>
                        <th class="border px-3 py-2 text-left text-xs">Supir</th>
                        <th class="border px-3 py-2 text-center text-xs">Full (20')</th>
                        <th class="border px-3 py-2 text-center text-xs">Full (40')</th>
                        <th class="border px-3 py-2 text-center text-xs">Total Full</th>
                        <th class="border px-3 py-2 text-center text-xs">Empty (20')</th>
                        <th class="border px-3 py-2 text-center text-xs">Empty (40')</th>
                        <th class="border px-3 py-2 text-center text-xs">Total Empty</th>
                        <th class="border px-3 py-2 text-right text-xs">Total</th>
                        <th class="border px-3 py-2 text-right text-xs">Biaya</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($perSupirCounts as $supirName => $counts)
                        @php
                            $full20 = $counts['sizes']['20']['full'] ?? 0;
                            $full40 = $counts['sizes']['40']['full'] ?? 0;
                            $empty20 = $counts['sizes']['20']['empty'] ?? 0;
                            $empty40 = $counts['sizes']['40']['empty'] ?? 0;
                            $totalFull = $full20 + $full40;
                            $totalEmpty = $empty20 + $empty40;
                            $total = $totalFull + $totalEmpty;
                            $biaya = $perSupir[$supirName] ?? 0;
                        @endphp
                        <tr>
                            <td class="border px-3 py-2 text-sm">{{ $supirName }}</td>
                            <td class="border px-3 py-2 text-center text-sm">{{ $full20 }}</td>
                            <td class="border px-3 py-2 text-center text-sm">{{ $full40 }}</td>
                            <td class="border px-3 py-2 text-center text-sm">{{ $totalFull }}</td>
                            <td class="border px-3 py-2 text-center text-sm">{{ $empty20 }}</td>
                            <td class="border px-3 py-2 text-center text-sm">{{ $empty40 }}</td>
                            <td class="border px-3 py-2 text-center text-sm">{{ $totalEmpty }}</td>
                            <td class="border px-3 py-2 text-right text-sm">{{ $total }}</td>
                            <td class="border px-3 py-2 text-right text-sm">Rp {{ number_format($biaya, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
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
