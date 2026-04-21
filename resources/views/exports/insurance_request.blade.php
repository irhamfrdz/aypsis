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
        @foreach($grouped as $key => $groupItems)
            @php 
                $groupFirst = $groupItems->first(); 
                // Sub-group by container (type and id)
                $byContainer = $groupItems->groupBy(function($item) {
                    return $item->type . '_' . $item->id;
                });
                $isFirstInGroup = true;
            @endphp
            
            @foreach($byContainer as $containerItems)
                @php $first = $containerItems->first(); @endphp
                <tr>
                    <td align="left">
                        @if($first->numbering)
                            {{ str_pad($first->numbering, 2, '0', STR_PAD_LEFT) }}.
                        @else
                            {{ str_pad($itemCount++, 2, '0', STR_PAD_LEFT) }}.
                        @endif
                    </td>
                    <td align="left"><b>{{ $first->no_kontainer ?: $first->number }}</b></td>
                    <td align="right"><b>{{ number_format($first->kuantitas ?: 0) }}</b></td>
                    <td align="left"><b>{{ strtoupper($first->satuan ?: 'UNIT') }}{{ $first->size ? ' CONT ' . $first->size . 'FT' : '' }}</b></td>
                    <td></td><td></td>
                    <td align="left">@if($isFirstInGroup)Rp @endif</td>
                    <td align="right"><b>@if($isFirstInGroup){{ number_format($groupItems->sum('amount') ?: 0, 0, ',', '.') }} @endif</b></td>
                </tr>
                <tr>
                    <td></td><td></td>
                    <td colspan="4" align="left">{{ strtoupper($first->nama_barang) }}</td>
                </tr>
                
                @if($containerItems->count() > 1)
                    @foreach($containerItems->slice(1) as $later)
                    <tr>
                        <td></td><td></td>
                        <td align="right"><b>{{ number_format($later->kuantitas ?: 0) }}</b></td>
                        <td align="left"><b>{{ strtoupper($later->satuan ?: 'UNIT') }}</b></td>
                    </tr>
                    <tr>
                        <td></td><td></td>
                        <td colspan="4" align="left">{{ strtoupper($later->nama_barang) }}</td>
                    </tr>
                    @endforeach
                @endif
                @php $isFirstInGroup = false; @endphp
            @endforeach

            <tr>
                <td></td><td></td>
                <td colspan="5" align="left" style="color: #FF0000;">{{ $groupFirst->pengirim }} - JAKARTA</td>
                <td align="right">@if($groupFirst->rate){{ number_format($groupFirst->rate, 2, ',', '.') }}%@else 0,30% @endif</td>
            </tr>
            <tr>
                <td></td><td></td>
                <td colspan="6" align="left" style="color: #FF0000;">{{ $groupFirst->penerima }} - BATAM</td>
            </tr>
            
            <tr><td colspan="8"></td></tr> <!-- Spacer -->
            @php $grandTotal += $groupItems->sum('amount'); @endphp
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
