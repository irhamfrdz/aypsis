@extends('layouts.print')

@section('content')
<div class="print-wrapper">
    <style>
        .print-wrapper {
            font-family: Arial, Helvetica, sans-serif;
            color: #000;
            font-weight: bold;
        }
        .compact th, .compact td {
            padding: 4px 6px !important;
            font-size: 11px !important;
            line-height: 1.2 !important;
            font-weight: bold !important;
            border: 1px solid #000 !important;
        }
        .compact {
            width: 100%;
            border-collapse: collapse;
        }
        /* Heading title */
        .page-title {
            font-size: 18px !important;
            font-weight: bold !important;
            margin-bottom: 10px;
        }
        .page-date {
            font-size: 12px !important;
            font-weight: bold !important;
        }
    </style>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
        <div class="page-title">
            Pembayaran Aktivitas Lain
        </div>
        <div class="page-date">{{ now()->format('d F Y') }}</div>
    </div>

    <table class="compact">
        <thead>
            <tr>
                <th class="text-center" style="width: 5%;">No</th>
                <th style="width: 15%;">Nomor</th>
                <th style="width: 20%;">Nomor Accurate</th>
                <th style="width: 10%;">Tanggal</th>
                <th style="width: 15%;">Jenis Aktivitas</th>
                <th style="width: 15%;">Penerima</th>
                <th class="numeric" style="width: 15%;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pembayarans as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->nomor }}</td>
                <td>{{ $item->nomor_accurate ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                <td>{{ $item->jenis_aktivitas }}</td>
                <td>{{ $item->penerima }}</td>
                <td class="numeric">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>

        @php
            $total = $pembayarans->sum('jumlah');
        @endphp
        <tfoot class="table-totals">
            <tr>
                <td colspan="6" class="bold text-right">Total</td>
                <td class="numeric bold">Rp {{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</div>
@endsection
