<table>
    <thead>
        <tr>
            <th colspan="4" style="font-weight: bold; font-size: 14px; text-decoration: underline;">{{ strtoupper($nama_kapal) }}</th>
            <th colspan="5" style="font-weight: bold; font-size: 14px; text-align: center; text-decoration: underline;">VOY.{{ $no_voyage }}</th>
            <th colspan="7" style="font-weight: bold; font-size: 14px; text-align: right; text-decoration: underline;">{{ $date }}</th>
        </tr>
        <tr style="border: 1px solid #000000; text-align: center; font-weight: bold;">
            <th rowspan="2" style="border: 1px solid #000000; vertical-align: middle; width: 50px;">NO URUT</th>
            <th rowspan="2" style="border: 1px solid #000000; vertical-align: middle; width: 80px;">BL</th>
            <th rowspan="2" style="border: 1px solid #000000; vertical-align: middle; width: 150px;">MARK AND NUMBERS</th>
            <th rowspan="2" style="border: 1px solid #000000; vertical-align: middle; width: 200px;">RELASI</th>
            <th rowspan="2" style="border: 1px solid #000000; vertical-align: middle; width: 50px;">FEET</th>
            <th rowspan="2" style="border: 1px solid #000000; vertical-align: middle; width: 80px;">TGL KRM</th>
            <th rowspan="2" style="border: 1px solid #000000; vertical-align: middle; width: 30px;">TR</th>
            <th rowspan="2" style="border: 1px solid #000000; vertical-align: middle; width: 80px;">TGL KBL</th>
            <th rowspan="2" style="border: 1px solid #000000; vertical-align: middle; width: 30px;">TR</th>
            <th colspan="4" style="border: 1px solid #000000; text-align: center;">CHASIS</th>
            <th rowspan="2" style="border: 1px solid #000000; vertical-align: middle; width: 50px;">SPPB</th>
            <th rowspan="2" style="border: 1px solid #000000; vertical-align: middle; width: 150px;">JENIS BARANG</th>
            <th rowspan="2" style="border: 1px solid #000000; vertical-align: middle; width: 200px;">ALAMAT</th>
        </tr>
        <tr style="border: 1px solid #000000; text-align: center; font-weight: bold;">
            <th style="border: 1px solid #000000; width: 30px;">AYP</th>
            <th style="border: 1px solid #000000; width: 30px;">PB</th>
            <th style="border: 1px solid #000000; width: 30px;">20</th>
            <th style="border: 1px solid #000000; width: 30px;">40</th>
        </tr>
    </thead>
    <tbody>
        @php
            $fclItems = $data->reject(function($item) {
                return strtolower($item->tipe_kontainer) === 'lcl';
            })->values();
            
            $lclItems = $data->filter(function($item) {
                return strtolower($item->tipe_kontainer) === 'lcl';
            })->values();
        @endphp

        {{-- FCL Items --}}
        @foreach($fclItems as $index => $item)
            <tr>
                <td style="border: 1px solid #000000; text-align: center;">{{ $index + 1 }}</td>
                <td style="border: 1px solid #000000; text-align: center;">{{ $item->nomor_urut ?? $item->nomor_bl }}</td>
                <td style="border: 1px solid #000000;">{{ $item->nomor_kontainer }}</td>
                <td style="border: 1px solid #000000;">{{ $item->penerima }}</td>
                <td style="border: 1px solid #000000; text-align: center;">{{ $item->size_kontainer }}"</td>
                <td style="border: 1px solid #000000;"></td>
                <td style="border: 1px solid #000000;"></td>
                <td style="border: 1px solid #000000;"></td>
                <td style="border: 1px solid #000000;"></td>
                <td style="border: 1px solid #000000;"></td>
                <td style="border: 1px solid #000000;"></td>
                <td style="border: 1px solid #000000;"></td>
                <td style="border: 1px solid #000000;"></td>
                <td style="border: 1px solid #000000;"></td>
                <td style="border: 1px solid #000000;">{{ $item->nama_barang }}</td>
                <td style="border: 1px solid #000000;">{{ $item->alamat_pengiriman ?? $item->tujuan_pengiriman }}</td>
            </tr>
        @endforeach

        @if($lclItems->count() > 0)
            {{-- Separator --}}
            <tr>
                <td colspan="16" style="border: 1px solid #000000; text-align: center; font-weight: bold; background-color: #f0f0f0;">" LCL BONGKAR GUDANG "</td>
            </tr>

            {{-- LCL Items --}}
            @foreach($lclItems as $index => $item)
                <tr>
                    <td style="border: 1px solid #000000; text-align: center;">{{ $index + 1 }}</td>
                    <td style="border: 1px solid #000000; text-align: center;">{{ $item->nomor_urut ?? $item->nomor_bl }}</td>
                    <td style="border: 1px solid #000000;">{{ $item->nomor_kontainer }}</td>
                    <td style="border: 1px solid #000000;">{{ $item->penerima }}</td>
                    <td style="border: 1px solid #000000; text-align: center;">{{ $item->size_kontainer }}"</td>
                    <td style="border: 1px solid #000000;"></td>
                    <td style="border: 1px solid #000000;"></td>
                    <td style="border: 1px solid #000000;"></td>
                    <td style="border: 1px solid #000000;"></td>
                    <td style="border: 1px solid #000000;"></td>
                    <td style="border: 1px solid #000000;"></td>
                    <td style="border: 1px solid #000000;"></td>
                    <td style="border: 1px solid #000000;"></td>
                    <td style="border: 1px solid #000000;"></td>
                    <td style="border: 1px solid #000000;">{{ $item->nama_barang }}</td>
                    <td style="border: 1px solid #000000;">{{ $item->alamat_pengiriman ?? $item->tujuan_pengiriman }}</td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
