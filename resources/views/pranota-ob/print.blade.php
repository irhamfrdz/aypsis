@extends('layouts.print')

@section('title', 'Print Pranota OB - ' . ($pranota->nomor_pranota ?? '-'))

@section('content')
    <div class="p-6 bg-white">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h2 class="text-2xl font-semibold">Pranota OB</h2>
                <p class="text-sm">Nomor: {{ $pranota->nomor_pranota ?? '-' }}</p>
                <p class="text-sm">Kapal / Voyage: {{ $pranota->nama_kapal ?? '-' }} / {{ $pranota->no_voyage ?? '-' }}</p>
            </div>
            <div class="text-sm text-right">
                <p>Tanggal Cetak: {{ now()->format('d/m/Y H:i') }}</p>
                <p>Pembuat: {{ $pranota->creator?->nama_lengkap ?? $pranota->creator?->name ?? '-' }}</p>
            </div>
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

        <table class="min-w-full border-collapse table-auto">
            <thead>
                <tr>
                    <th class="border px-3 py-2 text-left text-xs">No</th>
                    <th class="border px-3 py-2 text-left text-xs">Nomor Kontainer</th>
                    <th class="border px-3 py-2 text-left text-xs">Jenis Barang</th>
                    <th class="border px-3 py-2 text-left text-xs">Supir</th>
                    <th class="border px-3 py-2 text-left text-xs">Size</th>
                    <th class="border px-3 py-2 text-right text-xs">Biaya</th>
                </tr>
            </thead>
            <tbody>
                @forelse($displayItems as $i => $item)
                <tr>
                    <td class="border px-3 py-2 text-sm">{{ $i + 1 }}</td>
                    <td class="border px-3 py-2 text-sm">{{ $item['nomor_kontainer'] ?? '-' }}</td>
                    <td class="border px-3 py-2 text-sm">{{ $item['nama_barang'] ?? '-' }}</td>
                    <td class="border px-3 py-2 text-sm">{{ $item['supir'] ?? '-' }}</td>
                    <td class="border px-3 py-2 text-sm">{{ $item['size'] ?? '-' }}</td>
                    <td class="border px-3 py-2 text-sm text-right">
                        @if($item['biaya'])
                            Rp {{ number_format($item['biaya'], 0, ',', '.') }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td class="border px-3 py-8 text-center text-gray-500" colspan="6">Tidak ada item</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td class="border px-3 py-2 text-sm font-medium" colspan="5">Total</td>
                    <td class="border px-3 py-2 text-sm font-medium text-right">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

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
