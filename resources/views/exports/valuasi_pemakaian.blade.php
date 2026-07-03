<table>
    <thead>
        <tr>
            <th colspan="10" style="font-weight: bold; font-size: 14px; text-align: center;">AYPSIS</th>
        </tr>
        <tr>
            <th colspan="10" style="font-weight: bold; font-size: 12px; text-align: center;">Rincian Valuasi Pemakaian Stock Amprahan</th>
        </tr>
        <tr>
            <th colspan="10" style="font-weight: bold; font-size: 10px; text-align: center;">Periode: {{ $fromDate->format('d M Y') }} s/d {{ $toDate->format('d M Y') }}</th>
        </tr>
        <tr></tr>
        <tr>
            <th colspan="2" style="font-weight: bold;">Kategori Pemakai:</th>
            <th colspan="8">{{ $kategori }}</th>
        </tr>
        <tr>
            <th colspan="2" style="font-weight: bold;">Nama Pemakai:</th>
            <th colspan="8">{{ $pemakaiName }}</th>
        </tr>
        <tr></tr>
        <tr>
            <th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6;">Tanggal Pakai</th>
            <th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6;">No. Bukti Stock</th>
            <th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6;">Nama Barang</th>
            <th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6;">Toko</th>
            <th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6;">Tipe Amprahan</th>
            <th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6; text-align: right;">Kts. Keluar</th>
            <th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6;">Satuan</th>
            <th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6; text-align: right;">Harga Satuan</th>
            <th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6; text-align: right;">Total Nilai</th>
            <th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6;">Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @php
            $totalKtsKeluar = 0;
            $totalNilaiKeluar = 0;
        @endphp
        @foreach($usages as $usage)
            @php
                $qty = $usage->jumlah;
                $harga = $usage->stockAmprahan->harga_satuan ?? 0;
                $subtotal = $qty * $harga;
                
                $totalKtsKeluar += $qty;
                $totalNilaiKeluar += $subtotal;
            @endphp
            <tr>
                <td style="border: 1px solid #000;">{{ $usage->tanggal_pengambilan ? \Carbon\Carbon::parse($usage->tanggal_pengambilan)->format('d M Y') : '-' }}</td>
                <td style="border: 1px solid #000;">{{ $usage->stockAmprahan->nomor_bukti ?? '-' }}</td>
                <td style="border: 1px solid #000;">{{ $usage->stockAmprahan->nama_barang ?? ($usage->stockAmprahan->masterNamaBarangAmprahan->nama_barang ?? '-') }}</td>
                <td style="border: 1px solid #000;">{{ $usage->stockAmprahan->vendorAmprahan->nama_toko ?? '-' }}</td>
                <td style="border: 1px solid #000;">{{ $usage->stockAmprahan->type_amprahan ?? '-' }}</td>
                <td style="border: 1px solid #000; text-align: right;">{{ $qty }}</td>
                <td style="border: 1px solid #000;">{{ $usage->stockAmprahan->satuan ?? '-' }}</td>
                <td style="border: 1px solid #000; text-align: right;">{{ $harga }}</td>
                <td style="border: 1px solid #000; text-align: right;">{{ $subtotal }}</td>
                <td style="border: 1px solid #000;">{{ $usage->keterangan ?? '-' }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="5" style="font-weight: bold; border: 1px solid #000; text-align: right;">TOTAL</td>
            <td style="font-weight: bold; border: 1px solid #000; text-align: right;">{{ $totalKtsKeluar }}</td>
            <td style="border: 1px solid #000;"></td>
            <td style="border: 1px solid #000;"></td>
            <td style="font-weight: bold; border: 1px solid #000; text-align: right;">{{ $totalNilaiKeluar }}</td>
            <td style="border: 1px solid #000;"></td>
        </tr>
    </tbody>
</table>
