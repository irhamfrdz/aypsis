@php
    $truckingShipGroups = $biayaKapal->truckingDetails->groupBy(
        fn ($detail) => json_encode([$detail->kapal, $detail->voyage])
    );
    $grandTotal20ft = 0;
    $grandTotal40ft = 0;
    $grandTotalCargo = 0;
    $grandTotalSubtotal = 0;
    $grandTotalAdjustment = 0;
    $grandTotalPph = 0;
    $grandTotalFinal = 0;
@endphp

<table class="detail-table">
    <thead>
        <tr>
            <th style="width: 5%;">No</th>
            <th style="width: 35%;">Kapal / Voyage</th>
            <th style="width: 30%;">Vendor</th>
            <th style="width: 30%;">Kontainer</th>
        </tr>
    </thead>
    <tbody>
        @forelse($truckingShipGroups as $shipDetails)
            @php
                $shipNumber = $loop->iteration;
                $shipTotal20ft = $shipDetails->sum('total_biaya_20ft');
                $shipTotal40ft = $shipDetails->sum('total_biaya_40ft');
                $shipTotalCargo = $shipDetails->filter(
                    fn ($detail) => strtoupper(trim((string) $detail->nama_vendor)) === 'CARGO'
                )->sum('subtotal');
                $shipSubtotal = $shipDetails->sum('subtotal');
                $shipAdjustment = $shipDetails->sum('adjustment');
                $shipPph = $shipDetails->sum('pph');
                $shipTotal = $shipDetails->sum('total_biaya');
                $shipNotes = $shipDetails->pluck('notes_adjustment')->filter()->unique()->join('; ');

                $grandTotal20ft += $shipTotal20ft;
                $grandTotal40ft += $shipTotal40ft;
                $grandTotalCargo += $shipTotalCargo;
                $grandTotalSubtotal += $shipSubtotal;
                $grandTotalAdjustment += $shipAdjustment;
                $grandTotalPph += $shipPph;
                $grandTotalFinal += $shipTotal;
            @endphp
            @foreach($shipDetails as $detail)
                @php
                    $breakdown = $truckingBreakdowns[$detail->id] ?? ['count20' => 0, 'count40' => 0];
                @endphp
                <tr>
                    <td class="center">{{ $loop->first ? $shipNumber : '' }}</td>
                    <td>
                        @if($loop->first)
                            <strong>{{ $detail->kapal }}</strong><br>
                            <span style="color: #555;">Voy: {{ $detail->voyage }}</span>
                        @endif
                    </td>
                    <td>{{ $detail->nama_vendor }}</td>
                    <td>
                        @if(strtoupper(trim((string) $detail->nama_vendor)) === 'CARGO')
                            @if(count($detail->no_bl ?? []) > 0)
                                Cargo: {{ count($detail->no_bl) }} item
                            @else
                                Biaya Cargo manual
                            @endif
                        @else
                            20ft: {{ $breakdown['count20'] }}@if(!empty($breakdown['price20'])) × Rp {{ number_format($breakdown['price20'], 0, ',', '.') }}@endif<br>
                            40ft: {{ $breakdown['count40'] }}@if(!empty($breakdown['price40'])) × Rp {{ number_format($breakdown['price40'], 0, ',', '.') }}@endif
                        @endif
                    </td>
                </tr>
            @endforeach
            <tr style="background-color: #f2f2f2;">
                <td colspan="3"><strong>Rincian biaya {{ $shipDetails->first()->kapal }} / {{ $shipDetails->first()->voyage }}</strong></td>
                <td>
                    20ft: Rp {{ number_format($shipTotal20ft, 0, ',', '.') }}<br>
                    40ft: Rp {{ number_format($shipTotal40ft, 0, ',', '.') }}<br>
                    @if($shipTotalCargo != 0)
                        Cargo: Rp {{ number_format($shipTotalCargo, 0, ',', '.') }}<br>
                    @endif
                    Subtotal: Rp {{ number_format($shipSubtotal, 0, ',', '.') }}<br>
                    @if($shipAdjustment != 0)
                        Adjustment: {{ $shipAdjustment < 0 ? '-' : '+' }}Rp {{ number_format(abs($shipAdjustment), 0, ',', '.') }}<br>
                    @endif
                    @if($shipNotes !== '')
                        Keterangan: {{ $shipNotes }}<br>
                    @endif
                    PPh: (Rp {{ number_format($shipPph, 0, ',', '.') }})<br>
                    <strong>Total Biaya: Rp {{ number_format($shipTotal, 0, ',', '.') }}</strong>
                </td>
            </tr>
        @empty
            <tr><td colspan="4" class="center">Tidak ada detail trucking.</td></tr>
        @endforelse
    </tbody>
</table>

@if($truckingShipGroups->count() > 1)
    <div class="summary-box">
        <table class="summary-table">
            <tr>
                <td class="label">Total Kontainer 20ft:</td>
                <td class="value">Rp {{ number_format($grandTotal20ft, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">Total Kontainer 40ft:</td>
                <td class="value">Rp {{ number_format($grandTotal40ft, 0, ',', '.') }}</td>
            </tr>
            @if($grandTotalCargo != 0)
                <tr>
                    <td class="label">Total Cargo:</td>
                    <td class="value">Rp {{ number_format($grandTotalCargo, 0, ',', '.') }}</td>
                </tr>
            @endif
            <tr>
                <td class="label">Subtotal:</td>
                <td class="value">Rp {{ number_format($grandTotalSubtotal, 0, ',', '.') }}</td>
            </tr>
            @if($grandTotalAdjustment != 0)
                <tr>
                    <td class="label">Adjustment:</td>
                    <td class="value">{{ $grandTotalAdjustment < 0 ? '-' : '+' }}Rp {{ number_format(abs($grandTotalAdjustment), 0, ',', '.') }}</td>
                </tr>
            @endif
            @if($grandTotalPph > 0)
                <tr>
                    <td class="label">Total PPh:</td>
                    <td class="value" style="color: #d00;">(Rp {{ number_format($grandTotalPph, 0, ',', '.') }})</td>
                </tr>
            @endif
            <tr class="total-row">
                <td class="label">TOTAL BIAYA:</td>
                <td class="value">Rp {{ number_format($grandTotalFinal, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>
@endif
