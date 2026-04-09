<table>
    <thead>
        <tr><th colspan="8" align="left">Jakarta, {{ $requestDate }}</th></tr>
        <tr><th></th></tr>
        <tr><th></th></tr>
        <tr><th colspan="8" align="left">Kepada Yth.</th></tr>
        <tr><th colspan="4" align="left"><b>{{ $vendor->nama_asuransi ?? 'PT. ARTA PRIMA TUNGGAL' }}</b></th></tr>
        <tr><th colspan="4" align="left">Up. Ibu LeNNY</th><th colspan="4" align="left">F A C. 5 8 8 3 8 8 2</th></tr>
        <tr><th colspan="4" align="left">di</th><th colspan="4" align="left">lie1@cbn.net.id</th></tr>
        <tr><th colspan="1"></th><th colspan="3" align="left" style="text-decoration: underline;">Tempat</th><th colspan="4" align="left">apt928.mc@gmail.com</th></tr>
        <tr><th></th></tr>
        <tr><th colspan="8" align="left">Dengan Hormat,</th></tr>
        <tr><th></th></tr>
        <tr><th colspan="8" align="left">Mohon dibuat polis asuransi&nbsp;<b>{{ $shipName }}</b>,&nbsp;tanggal&nbsp;<b>{{ $requestDate }}</b>&nbsp;sbb :</th></tr>
        <tr><th></th></tr>
    </thead>
    <tbody>
        @php $itemCount = 1; $grandTotal = 0; @endphp
        @foreach($grouped as $key => $items)
            @php $first = $items->first(); @endphp
            <tr>
                <td align="left">@if($first->numbering){{ str_pad($first->numbering, 2, '0', STR_PAD_LEFT) }}.@else{{ str_pad($itemCount++, 2, '0', STR_PAD_LEFT) }}.@endif</td>
                <td align="left"><b>{{ $first->no_kontainer ?: $first->number }}</b></td>
                <td align="left"><b>1 {{ $first->satuan ?: 'UNIT/PALET' }}</b> {{ str_replace(',', "\n", str_replace('@', '', strtoupper($first->nama_barang))) }}</td>
                <td></td><td></td><td></td>
                <td align="left">Rp</td>
                <td align="right"><b>{{ number_format($items->sum('amount') ?: 0, 0, ',', '.') }}</b></td>
            </tr>
            
            @if($items->count() > 1)
                @foreach($items->slice(1) as $later)
                <tr>
                    <td></td>
                    <td align="left"><b>{{ $later->no_kontainer ?: $later->number }}</b></td>
                    <td align="left"><b>1 {{ $later->satuan ?: 'UNIT/PALET' }}</b> {{ str_replace(',', "\n", str_replace('@', '', strtoupper($later->nama_barang))) }}</td>
                </tr>
                @endforeach
            @endif

            <tr>
                <td></td><td></td>
                <td align="left" style="color: #666;">{{ $first->pengirim }} - JAKARTA</td>
                <td></td><td></td><td></td>
                <td align="left"></td>
                <td align="right">@if($first->rate){{ number_format($first->rate, 2, ',', '.') }}%@else 0,30% @endif</td>
            </tr>
            <tr>
                <td></td><td></td>
                <td align="left" style="color: #666;">{{ $first->penerima }} - BATAM</td>
            </tr>
            
            <tr><td colspan="8"></td></tr> <!-- Spacer -->
            @php $grandTotal += $items->sum('amount'); @endphp
        @endforeach
    </tbody>
    <tfoot>
        <tr><th></th></tr>
        <tr>
            <td colspan="6" align="left">Demikianlah pemberitahuan ini kami sampaikan, atas perhatian serta kerjasama yang baik</td>
            <td align="left"></td>
            <td align="right"><b>{{ number_format($grandTotal, 0, ',', '.') }}</b></td>
        </tr>
        <tr>
            <td colspan="6" align="left">kami mengucapkan banyak terima kasih.</td>
        </tr>
        <tr><th></th></tr>
        <tr>
            <td colspan="8" align="left">Hormat kami,</td>
        </tr>
        <tr><th></th></tr>
        <tr><th></th></tr>
        <tr><th></th></tr>
        <tr>
            <td colspan="8" align="left"><b>PT. AYP (Antar Ya Perdana)</b></td>
        </tr>
    </tfoot>
</table>
