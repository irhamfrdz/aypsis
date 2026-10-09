@php
    $printItems = $pranota->items->values();
    $itemsPerColumn = max(1, (int) ceil($printItems->count() / 2));
    $printColumns = $printItems->chunk($itemsPerColumn);
@endphp
<div class="temas-columns">
    @foreach($printColumns as $columnItems)
        <table class="items-table temas-items">
            <thead>
                <tr>
                    <th style="width: 5%">NO</th>
                    <th style="width: 14%">TGL OB</th>
                    <th style="width: 24%">KONTAINER</th>
                    <th style="width: 19%">SUPIR</th>
                    <th style="width: 18%">KE</th>
                    <th style="width: 20%">BIAYA (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($columnItems as $rowIndex => $item)
                    @php($detail = $item->snapshot)
                    <tr>
                        <td class="text-center">{{ $rowIndex + 1 }}</td>
                        <td class="text-center nowrap">{{ \Carbon\Carbon::parse($detail->tanggal_ob ?? $detail->created_at)->format('d/m/y') }}</td>
                        <td class="font-bold nowrap">{{ $detail->nomor_kontainer }}</td>
                        <td>{{ $detail->nama_supir }}</td>
                        <td>{{ $detail->tujuan_gudang ?? '-' }}</td>
                        <td class="text-right font-bold nowrap">{{ number_format($detail->biaya, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
</div>
<table class="temas-totals">
    <tr>
        <td>Jumlah Kontainer: <strong>{{ $printItems->count() }}</strong></td>
        <td class="text-right">Subtotal: <strong>Rp {{ number_format($pranota->nominal, 0, ',', '.') }}</strong></td>
        <td class="text-right">Adjustment: <strong>Rp {{ number_format($pranota->adjustment, 0, ',', '.') }}</strong></td>
        <td class="text-right grand-total">TOTAL: Rp {{ number_format($pranota->grand_total, 0, ',', '.') }}</td>
    </tr>
</table>
