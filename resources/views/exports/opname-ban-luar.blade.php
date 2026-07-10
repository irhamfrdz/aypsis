<table>
    <thead>
        <!-- Title Rows -->
        <tr>
            <th colspan="9" style="font-size: 14px; font-weight: bold; text-align: center;">LEMBAR KERJA OPNAME BAN LUAR</th>
        </tr>
        <tr>
            <th colspan="9" style="font-weight: bold; text-align: center;">Periode: {{ $bulan }} / {{ $tahun }}</th>
        </tr>
        
        <!-- Empty Row -->
        <tr>
            <th colspan="9"></th>
        </tr>

        <!-- Summary Table -->
        <tr>
            <th colspan="2" style="background-color: #f3f4f6; font-weight: bold; border: 1px solid #000000;">RANGKUMAN STOCK & PEMAKAIAN</th>
        </tr>
        <tr>
            <td style="background-color: #f3f4f6; font-weight: bold; border: 1px solid #000000;">Kategori / Lokasi</td>
            <td style="background-color: #f3f4f6; font-weight: bold; border: 1px solid #000000; text-align: center;">Jumlah (Unit)</td>
        </tr>
        
        @foreach($stokByLokasi as $lokasi => $total)
        <tr>
            <td style="border: 1px solid #000000;">Stok di {{ $lokasi ?: 'Gudang Utama' }}</td>
            <td style="border: 1px solid #000000; text-align: center;">{{ $total }}</td>
        </tr>
        @endforeach
        
        <tr>
            <td style="border: 1px solid #000000;">Total Terpakai</td>
            <td style="border: 1px solid #000000; text-align: center;">{{ $terpakai }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000000;">Dikirim Ke Batam</td>
            <td style="border: 1px solid #000000; text-align: center;">{{ $keBatam }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000000;">Dikirim Ke Tj. Pinang</td>
            <td style="border: 1px solid #000000; text-align: center;">{{ $kePinang }}</td>
        </tr>
        
        <!-- Empty Row -->
        <tr>
            <th colspan="9"></th>
        </tr>

        <!-- Main Data Headers -->
        <tr>
            <th style="background-color: #1e40af; color: #ffffff; font-weight: bold; border: 1px solid #000000;">No</th>
            <th style="background-color: #1e40af; color: #ffffff; font-weight: bold; border: 1px solid #000000;">No Seri / Kode</th>
            <th style="background-color: #1e40af; color: #ffffff; font-weight: bold; border: 1px solid #000000;">Nama Stock</th>
            <th style="background-color: #1e40af; color: #ffffff; font-weight: bold; border: 1px solid #000000;">Merk</th>
            <th style="background-color: #1e40af; color: #ffffff; font-weight: bold; border: 1px solid #000000;">Ukuran</th>
            <th style="background-color: #1e40af; color: #ffffff; font-weight: bold; border: 1px solid #000000;">Kondisi</th>
            <th style="background-color: #1e40af; color: #ffffff; font-weight: bold; border: 1px solid #000000;">Lokasi</th>
            <th style="background-color: #1e40af; color: #ffffff; font-weight: bold; border: 1px solid #000000;">Status Sistem</th>
            <th style="background-color: #1e40af; color: #ffffff; font-weight: bold; border: 1px solid #000000;">Tanggal Masuk</th>
        </tr>
    </thead>
    <tbody>
    @php $no = 1; @endphp
    @foreach($stockBans as $ban)
        <tr>
            <td style="border: 1px solid #000000;">{{ $no++ }}</td>
            <td style="border: 1px solid #000000;">{{ $ban->nomor_seri ?? '-' }}</td>
            <td style="border: 1px solid #000000;">{{ $ban->namaStockBan?->nama ?? '-' }}</td>
            <td style="border: 1px solid #000000;">{{ $ban->merk ?? '-' }}</td>
            <td style="border: 1px solid #000000;">{{ $ban->ukuran ?? '-' }}</td>
            <td style="border: 1px solid #000000;">{{ strtoupper($ban->kondisi ?? '-') }}</td>
            <td style="border: 1px solid #000000;">{{ $ban->lokasi ?? '-' }}</td>
            <td style="border: 1px solid #000000;">{{ $ban->status ?? '-' }}</td>
            <td style="border: 1px solid #000000;">{{ $ban->tanggal_masuk ? $ban->tanggal_masuk->format('d/m/Y') : '-' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
