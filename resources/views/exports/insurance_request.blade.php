<table>
    <thead>
        <tr><td colspan="8" align="left">Jakarta, {{ $requestDate }}</td></tr>
        <tr><th></th></tr>
        <tr><th></th></tr>
        <tr><td colspan="8" align="left">Kepada Yth.</td></tr>
        <tr><td colspan="4" align="left"><b>{{ $vendor->nama_asuransi ?? 'PT. ARTA PRIMA TUNGGAL' }}</b></td></tr>
        <tr><td colspan="4" align="left">Up. Ibu LeNNY</td><td colspan="4" align="left">F A C. 5 8 8 3 8 8 2</td></tr>
        <tr><td colspan="4" align="left">di</td><td colspan="4" align="left">lie1@cbn.net.id</td></tr>
        <tr><td colspan="1" align="left"></td><td colspan="3" align="left" style="text-decoration: underline;">Tempat</td><td colspan="4" align="left">apt928.mc@gmail.com</td></tr>
        <tr><th></th></tr>
        <tr><td colspan="8" align="left">Dengan Hormat,</td></tr>
        <tr><th></th></tr>
        <tr><td colspan="8" align="left">Mohon dibuat polis asuransi <b>{{ $shipName }}</b>, tanggal <b>{{ $requestDate }}</b> sbb :</td></tr>
        <tr><th></th></tr>
    </thead>
    <tbody>
        @php $itemCount = 1; $grandTotal = 0; @endphp
        @foreach($grouped as $key => $groupItems)
            @php 
                // Sub-group by container (type and id)
                $byContainer = $groupItems->groupBy(function($item) {
                    return $item->type . '_' . $item->id;
                });
            @endphp
            
            @foreach($byContainer as $containerItems)
                @php $first = $containerItems->first(); @endphp
                <tr>
                    <td align="left" valign="top">
                        @if($first->numbering)
                            {{ str_pad($first->numbering, 2, '0', STR_PAD_LEFT) }}.
                        @else
                            {{ str_pad($itemCount++, 2, '0', STR_PAD_LEFT) }}.
                        @endif
                    </td>
                    <td align="left" valign="top"><b>{{ $first->no_kontainer ?: $first->number }}</b></td>
                    <td align="left" valign="top" colspan="3"><b>1 UNIT&nbsp;</b>{{ $first->container_size_label ?? 'CONT.20" ISI' }}</td>
                    <td align="right" valign="top"></td>
                    <td align="left" valign="top">Rp</td>
                    <td align="right" valign="top"><b>{{ number_format($containerItems->sum('amount') ?: 0, 0, ',', '.') }}</b></td>
                </tr>
                
                @php
                    $summedItems = $containerItems->groupBy(function($item) {
                        return strtoupper($item->nama_barang) . '_' . strtoupper($item->satuan ?: 'UNIT');
                    })->map(function($group) {
                        $first = $group->first();
                        return (object)[
                            'nama_barang' => $first->nama_barang,
                            'satuan' => $first->satuan,
                            'kuantitas' => $group->sum('kuantitas')
                        ];
                    });
                @endphp
                
                @foreach($summedItems as $index => $item)
                    <tr>
                        <td></td>
                        <td></td>
                        <td align="left" valign="top" colspan="3"><b>{{ number_format($item->kuantitas ?: 0) }} {{ strtoupper($item->satuan ?: 'UNIT') }}&nbsp;</b>{{ strtoupper($item->nama_barang) }}</td>
                        <td></td>
                        <td></td>
                        <td align="right" valign="top">
                            @if($index === 0 || count($summedItems) === 1)
                                @if($first->rate){{ number_format($first->rate, 2, ',', '.') }}%@else 0,30% @endif
                            @endif
                        </td>
                    </tr>
                @endforeach
                
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td align="left" style="color: #FF0000;">PT. AYP QQ {{ $first->pengirim }} - JAKARTA</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td align="left" style="color: #FF0000;">{{ $first->penerima }} - BATAM</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                
                <tr><td colspan="8"></td></tr> <!-- Spacer -->
            @endforeach
            @php $grandTotal += $groupItems->sum('amount'); @endphp
        @endforeach
    </tbody>
    <tfoot>
        <tr><td></td></tr>
        <tr>
            <td colspan="6" align="left">Demikianlah pemberitahuan ini kami sampaikan, atas perhatian serta kerjasama yang baik</td>
            <td align="left"></td>
            <td align="right"><b>{{ number_format($grandTotal, 0, ',', '.') }}</b></td>
        </tr>
        <tr>
            <td colspan="8" align="left">kami mengucapkan banyak terima kasih.</td>
        </tr>
        <tr><td></td></tr>
        <tr>
            <td colspan="8" align="left">Hormat kami,</td>
        </tr>
        <tr><td></td></tr>
        <tr><td></td></tr>
        <tr><td></td></tr>
        <tr>
            <td colspan="8" align="left"><b>PT. AYP (Antar Ya Perdana)</b></td>
        </tr>
    </tfoot>
</table>
