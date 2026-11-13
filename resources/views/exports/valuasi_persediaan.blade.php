<table>
    <thead>
        <tr>
            <th colspan="{{ $gunakanNominal ? 11 : 8 }}" style="font-weight: bold; font-size: 14px; text-align: center;">AYPSIS</th>
        </tr>
        <tr>
            <th colspan="{{ $gunakanNominal ? 11 : 8 }}" style="font-weight: bold; font-size: 12px; text-align: center;">Rincian Valuasi Persediaan</th>
        </tr>
        <tr>
            <th colspan="{{ $gunakanNominal ? 11 : 8 }}" style="font-weight: bold; font-size: 10px; text-align: center;">Periode: {{ $fromDate->format('d M Y') }} s/d {{ $toDate->format('d M Y') }}</th>
        </tr>
        <tr></tr>
        <tr>
            <th colspan="2" style="font-weight: bold;">No. Barang:</th>
            <th colspan="3">{{ $masterItem->id }}</th>
            <th colspan="{{ $gunakanNominal ? 3 : 2 }}" style="font-weight: bold; text-align: right;">Kts. Saldo Awal:</th>
            <th colspan="{{ $gunakanNominal ? 3 : 1 }}">: {{ number_format($saldoAwalQty, 0, ',', '.') }}</th>
        </tr>
        <tr>
            <th colspan="2" style="font-weight: bold;">Deskripsi Barang:</th>
            <th colspan="{{ $gunakanNominal ? 3 : 6 }}">{{ $masterItem->nama_barang }}</th>
            @if($gunakanNominal)
                <th colspan="3" style="font-weight: bold; text-align: right;">Nilai Saldo Awal:</th>
                <th colspan="3">: {{ number_format($saldoAwalNilai, 0, ',', '.') }}</th>
            @endif
        </tr>
        <tr></tr>
        <tr>
            <th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6;">Tanggal</th>
            <th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6;">Tipe</th>
            <th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6;">Nama Barang</th>
            <th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6;">No. Bukti</th>
            <th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6;">Dikeluarkan Ke</th>
            <th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6; text-align: right;">Kts. Masuk</th>
            @if($gunakanNominal)<th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6; text-align: right;">Nilai masuk</th>@endif
            <th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6; text-align: right;">Kts. Keluar</th>
            @if($gunakanNominal)<th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6; text-align: right;">Nilai keluar</th>@endif
            <th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6; text-align: right;">Kuantitas</th>
            @if($gunakanNominal)<th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6; text-align: right;">Nilai Akhir</th>@endif
        </tr>
    </thead>
    <tbody>
        @php
            $totalKtsMasuk = 0;
            $totalNilaiMasuk = 0;
            $totalKtsKeluar = 0;
            $totalNilaiKeluar = 0;
        @endphp
        @foreach($transaksi as $trx)
            @php
                $totalKtsMasuk += $trx->kts_masuk;
                $totalNilaiMasuk += $trx->nilai_masuk;
                $totalKtsKeluar += $trx->kts_keluar;
                $totalNilaiKeluar += $trx->nilai_keluar;
            @endphp
            <tr>
                <td style="border: 1px solid #000;">{{ $trx->tanggal }}</td>
                <td style="border: 1px solid #000;">{{ $trx->tipe }}</td>
                <td style="border: 1px solid #000;">{{ $trx->nama_barang }}</td>
                <td style="border: 1px solid #000;">{{ $trx->no_faktur }}</td>
                <td style="border: 1px solid #000;">{{ $trx->kts_masuk > 0 ? '' : $trx->referensi }}</td>
                <td style="border: 1px solid #000; text-align: right;">{{ $trx->kts_masuk }}</td>
                @if($gunakanNominal)<td style="border: 1px solid #000; text-align: right;">{{ $trx->nilai_masuk }}</td>@endif
                <td style="border: 1px solid #000; text-align: right;">{{ $trx->kts_keluar }}</td>
                @if($gunakanNominal)<td style="border: 1px solid #000; text-align: right;">{{ $trx->nilai_keluar }}</td>@endif
                <td style="border: 1px solid #000; text-align: right;">{{ $trx->kuantitas }}</td>
                @if($gunakanNominal)<td style="border: 1px solid #000; text-align: right;">{{ $trx->nilai_akhir }}</td>@endif
            </tr>
        @endforeach
        <tr>
            <td colspan="5" style="font-weight: bold; border: 1px solid #000; text-align: right;">TOTAL</td>
            <td style="font-weight: bold; border: 1px solid #000; text-align: right;">{{ $totalKtsMasuk }}</td>
            @if($gunakanNominal)<td style="font-weight: bold; border: 1px solid #000; text-align: right;">{{ $totalNilaiMasuk }}</td>@endif
            <td style="font-weight: bold; border: 1px solid #000; text-align: right;">{{ $totalKtsKeluar }}</td>
            @if($gunakanNominal)<td style="font-weight: bold; border: 1px solid #000; text-align: right;">{{ $totalNilaiKeluar }}</td>@endif
            <td colspan="{{ $gunakanNominal ? 2 : 1 }}" style="border: 1px solid #000;"></td>
        </tr>
    </tbody>
</table>
