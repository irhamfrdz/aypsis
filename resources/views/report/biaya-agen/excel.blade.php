<table>
    <thead>
        <tr><th colspan="{{ $validated['gunakan_pph'] === '1' ? 16 : 15 }}">REPORT BIAYA AGEN</th></tr>
        <tr><td>Mode</td><td>{{ ucfirst($validated['mode']) }}</td></tr>
        <tr><td>Kapal</td><td>{{ $validated['kapal'] ?? 'Semua kapal' }}</td><td>Periode</td><td>{{ isset($validated['start_date']) ? \Carbon\Carbon::parse($validated['start_date'])->format('d/m/Y').' - '.\Carbon\Carbon::parse($validated['end_date'])->format('d/m/Y') : 'Semua tanggal' }}</td></tr>
        <tr><td>Jenis biaya</td><td colspan="{{ $validated['gunakan_pph'] === '1' ? 15 : 14 }}">{{ empty($validated['jenis_biaya']) ? 'Semua jenis biaya' : implode(', ', $validated['jenis_biaya']) }}</td></tr>
        <tr><td>PPh</td><td colspan="{{ $validated['gunakan_pph'] === '1' ? 15 : 14 }}">{{ $validated['gunakan_pph'] === '1' ? 'Digunakan' : 'Tidak digunakan' }}</td></tr>
    </thead>
    <tbody>
        <tr><th>Kapal</th><th>Item</th><th>Trip</th><th>Subtotal</th>@if($validated['gunakan_pph'] === '1')<th>PPh</th>@endif<th>Penyesuaian</th><th>Total biaya agen</th><td colspan="9"></td></tr>
        @foreach($groups as $group)
            <tr><td>{{ $group['kapal'] }}</td><td>{{ $group['rows']->count() }}</td><td>{{ $group['trip_count'] }}</td><td>{{ (float) $group['subtotal'] }}</td>@if($validated['gunakan_pph'] === '1')<td>{{ (float) $group['pph'] }}</td>@endif<td>{{ (float) $group['adjustment'] }}</td><td>{{ (float) $group['grand_total'] }}</td><td colspan="9"></td></tr>
        @endforeach
        <tr><th>No.</th><th>Tanggal</th><th>Invoice</th><th>Referensi</th><th>Kapal</th><th>Voyage</th><th>Vendor</th><th>Lokasi</th><th>Jenis biaya</th><th>Kuantitas</th><th>Harga</th><th>Jasa air</th><th>Subtotal</th>@if($validated['gunakan_pph'] === '1')<th>PPh</th>@endif<th>Penyesuaian</th><th>Total</th></tr>
        @php($line = 0)
        @foreach($groups as $group)
            <tr><th colspan="{{ $validated['gunakan_pph'] === '1' ? 16 : 15 }}">{{ $group['kapal'] }} — {{ $group['trip_count'] }} trip</th></tr>
        @foreach($group['rows'] as $row)
            <tr>
                <td>{{ ++$line }}</td>
                <td>{{ \Carbon\Carbon::parse($row->tanggal_invoice_vendor ?: $row->tanggal_biaya_kapal)->format('d/m/Y') }}</td>
                <td>{{ $row->nomor_invoice ?: '-' }}</td><td>{{ $row->nomor_referensi ?: '-' }}</td>
                <td>{{ $row->kapal ?: '-' }}</td><td>{{ $row->voyage ?: '-' }}</td>
                <td>{{ $row->vendor ?: '-' }}</td><td>{{ $row->lokasi ?: '-' }}</td>
                <td>{{ ($row->type_keterangan ?: '-').($row->is_lumpsum ? ' (Lumpsum)' : '') }}</td>
                <td>{{ $row->is_lumpsum ? '' : (float) $row->kuantitas }}</td>
                <td>{{ (float) $row->harga }}</td><td>{{ (float) $row->jasa_air }}</td>
                <td>{{ (float) $row->sub_total }}</td>@if($validated['gunakan_pph'] === '1')<td>{{ (float) $row->pph }}</td>@endif
                <td>{{ (float) $row->adjustment }}</td><td>{{ (float) $row->grand_total }}</td>
            </tr>
        @endforeach
        @endforeach
        <tr><th colspan="12">TOTAL</th><th>{{ (float) $summary['subtotal'] }}</th>@if($validated['gunakan_pph'] === '1')<th>{{ (float) $summary['pph'] }}</th>@endif<th>{{ (float) $summary['adjustment'] }}</th><th>{{ (float) $summary['grand_total'] }}</th></tr>
    </tbody>
</table>
