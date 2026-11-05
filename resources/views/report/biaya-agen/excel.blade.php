<table>
    <thead>
        <tr><th colspan="16">REPORT BIAYA AGEN</th></tr>
        <tr><td>Mode</td><td>{{ ucfirst($validated['mode']) }}</td></tr>
        <tr><td>Kapal</td><td>{{ $validated['kapal'] ?? 'Semua kapal' }}</td><td>Periode</td><td>{{ isset($validated['start_date']) ? \Carbon\Carbon::parse($validated['start_date'])->format('d/m/Y').' - '.\Carbon\Carbon::parse($validated['end_date'])->format('d/m/Y') : 'Semua tanggal' }}</td></tr>
        <tr><td>Jenis biaya</td><td colspan="15">{{ empty($validated['jenis_biaya']) ? 'Semua jenis biaya' : implode(', ', $validated['jenis_biaya']) }}</td></tr>
        <tr>
            <th>No.</th><th>Tanggal</th><th>Invoice</th><th>Referensi</th><th>Kapal</th><th>Voyage</th><th>Vendor</th><th>Lokasi</th><th>Jenis biaya</th><th>Kuantitas</th><th>Harga</th><th>Jasa air</th><th>Subtotal</th><th>PPh</th><th>Penyesuaian</th><th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ \Carbon\Carbon::parse($row->tanggal_invoice_vendor ?: $row->tanggal_biaya_kapal)->format('d/m/Y') }}</td>
                <td>{{ $row->nomor_invoice ?: '-' }}</td><td>{{ $row->nomor_referensi ?: '-' }}</td>
                <td>{{ $row->kapal ?: '-' }}</td><td>{{ $row->voyage ?: '-' }}</td>
                <td>{{ $row->vendor ?: '-' }}</td><td>{{ $row->lokasi ?: '-' }}</td>
                <td>{{ ($row->type_keterangan ?: '-').($row->is_lumpsum ? ' (Lumpsum)' : '') }}</td>
                <td>{{ $row->is_lumpsum ? '' : (float) $row->kuantitas }}</td>
                <td>{{ (float) $row->harga }}</td><td>{{ (float) $row->jasa_air }}</td>
                <td>{{ (float) $row->sub_total }}</td><td>{{ (float) $row->pph }}</td>
                <td>{{ (float) $row->adjustment }}</td><td>{{ (float) $row->grand_total }}</td>
            </tr>
        @endforeach
        <tr><th colspan="12">TOTAL</th><th>{{ (float) $summary['subtotal'] }}</th><th>{{ (float) $summary['pph'] }}</th><th>{{ (float) $summary['adjustment'] }}</th><th>{{ (float) $summary['grand_total'] }}</th></tr>
    </tbody>
</table>
