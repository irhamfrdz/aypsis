<table>
    <thead>
        <!-- Global Title -->
        <tr>
            <th colspan="5" style="font-weight: bold; font-size: 14px; text-align: center;">REKAP BIAYA BURUH</th>
        </tr>
        <tr>
            <th colspan="5" style="text-align: center;">Periode: {{ \Carbon\Carbon::parse($tanggalMulai)->format('d/M/Y') }} - {{ \Carbon\Carbon::parse($tanggalAkhir)->format('d/M/Y') }}</th>
        </tr>
        <tr></tr> <!-- empty row -->
    </thead>
    <tbody>
        @foreach($processedData as $data)
            @php
                $biayaKapal = $data['biayaKapal'];
                $groupedDetails = $data['groupedDetails'];
                $combinedBarang = $data['combinedBarang'];
                $totalAdjustments = $data['totalAdjustments'];
                $overallTotal = $data['overallTotal'];
                $tenagaKerjaGroups = $data['tenagaKerjaGroups'];
            @endphp
            
            <!-- Info Section -->
            <tr>
                <th colspan="5" style="font-weight: bold; text-align: left; background-color: #d1ecf1;">Permohonan Transfer - {{ $biayaKapal->nomor_invoice }}</th>
            </tr>
            <tr>
                <th style="font-weight: bold;">Tanggal</th>
                <td>: {{ \Carbon\Carbon::parse($biayaKapal->tanggal)->format('d/M/Y') }}</td>
                <td></td>
                <th style="font-weight: bold;">Penerima</th>
                <td>: {{ $biayaKapal->penerima ?? '-' }}</td>
            </tr>
            <tr>
                <th style="font-weight: bold;">Nomor</th>
                <td>: {{ $biayaKapal->nomor_invoice }}</td>
                <td></td>
                <th style="font-weight: bold;">Nama Vendor</th>
                <td>: {{ $biayaKapal->nama_vendor ?? '-' }}</td>
            </tr>
            <tr>
                <th style="font-weight: bold;">Nomor Referensi</th>
                <td>: {{ $biayaKapal->nomor_referensi ?? $biayaKapal->nomor_invoice }}</td>
                <td></td>
                <th style="font-weight: bold;">Bank</th>
                <td>: {{ $biayaKapal->bank->name ?? '-' }}</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <th style="font-weight: bold;">Nomor Rekening</th>
                <td>: {{ $biayaKapal->nomor_rekening ?? '-' }}</td>
            </tr>
            <tr></tr> <!-- empty row -->

            <!-- Detail Biaya Kapal Table Header -->
            <tr>
                <th colspan="5" style="font-weight: bold;">Detail Biaya Kapal:</th>
            </tr>
            <tr style="background-color: #f8f9fa;">
                <th style="border: 1px solid #000; font-weight: bold; text-align: center;">No</th>
                <th style="border: 1px solid #000; font-weight: bold;">Nama Kapal</th>
                <th style="border: 1px solid #000; font-weight: bold; text-align: center;">Tanggal</th>
                <th style="border: 1px solid #000; font-weight: bold; text-align: center;">No. Voyage</th>
                <th style="border: 1px solid #000; font-weight: bold; text-align: right;">Biaya</th>
            </tr>
            
            @php $rowNumber = 0; @endphp
            @foreach($groupedDetails as $groupKey => $details)
                @php
                    $rowNumber++;
                    $parts = explode('|', $groupKey);
                    $groupKapal = $parts[0] ?? '-';
                    $groupVoyage = $parts[1] ?? '-';
                    $firstDetail = $details->first();
                    $groupSubtotal = ($firstDetail && $firstDetail->total_nominal > 0) ? $firstDetail->total_nominal : $details->sum('subtotal');
                @endphp
                <tr>
                    <td style="border: 1px solid #000; text-align: center;">{{ $rowNumber }}</td>
                    <td style="border: 1px solid #000;">{{ $groupKapal }}</td>
                    <td style="border: 1px solid #000; text-align: center;">{{ \Carbon\Carbon::parse($biayaKapal->tanggal)->format('d/M/Y') }}</td>
                    <td style="border: 1px solid #000; text-align: center;">{{ $groupVoyage }}</td>
                    <td style="border: 1px solid #000; text-align: right;">{{ $groupSubtotal }}</td>
                </tr>
            @endforeach
            <tr style="background-color: #e9ecef; font-weight: bold;">
                <td colspan="4" style="border: 1px solid #000; text-align: right; font-weight: bold;">TOTAL PEMBAYARAN</td>
                <td style="border: 1px solid #000; text-align: right; font-weight: bold;">{{ $biayaKapal->nominal }}</td>
            </tr>
            <tr></tr> <!-- empty row -->

            <!-- Detail Barang Table Header -->
            @if($combinedBarang->count() > 0)
                <tr>
                    <th colspan="5" style="font-weight: bold;">Detail Barang (Gabungan Semua Kapal):</th>
                </tr>
                <tr style="background-color: #f8f9fa;">
                    <th style="border: 1px solid #000; font-weight: bold; text-align: center;">No</th>
                    <th colspan="2" style="border: 1px solid #000; font-weight: bold;">Jenis Barang</th>
                    <th style="border: 1px solid #000; font-weight: bold; text-align: center;">Jumlah</th>
                    <th style="border: 1px solid #000; font-weight: bold; text-align: right;">Harga Satuan</th>
                    <th style="border: 1px solid #000; font-weight: bold; text-align: right;">Subtotal</th>
                </tr>
                @foreach($combinedBarang as $index => $item)
                    <tr>
                        <td style="border: 1px solid #000; text-align: center;">{{ $index + 1 }}</td>
                        <td colspan="2" style="border: 1px solid #000;">{{ $item['barang'] }}</td>
                        <td style="border: 1px solid #000; text-align: center;">{{ $item['jumlah'] }}</td>
                        <td style="border: 1px solid #000; text-align: right;">{{ $item['harga_satuan'] }}</td>
                        <td style="border: 1px solid #000; text-align: right;">{{ $item['subtotal'] }}</td>
                    </tr>
                @endforeach
                @if($totalAdjustments != 0)
                    <tr>
                        <td style="border: 1px solid #000; text-align: center;">{{ $combinedBarang->count() + 1 }}</td>
                        <td colspan="2" style="border: 1px solid #000;">Adjustment</td>
                        <td style="border: 1px solid #000; text-align: center;">-</td>
                        <td style="border: 1px solid #000; text-align: right;">-</td>
                        <td style="border: 1px solid #000; text-align: right;">{{ $totalAdjustments }}</td>
                    </tr>
                @endif
                <tr style="background-color: #e9ecef; font-weight: bold;">
                    <td colspan="5" style="border: 1px solid #000; text-align: right; font-weight: bold;">TOTAL</td>
                    <td style="border: 1px solid #000; text-align: right; font-weight: bold;">{{ $overallTotal }}</td>
                </tr>
                <tr></tr> <!-- empty row -->
            @endif

            <!-- Detail Buruh Sections -->
            @if($tenagaKerjaGroups->count() > 0)
                @foreach($tenagaKerjaGroups as $groupName => $details)
                    <tr>
                        <th colspan="4" style="font-weight: bold; text-transform: uppercase;">BONGKAR/MUAT {{ $groupName }}</th>
                    </tr>
                    <tr style="background-color: #f8f9fa;">
                        <th style="border: 1px solid #000; font-weight: bold; text-align: center;">NO</th>
                        <th colspan="2" style="border: 1px solid #000; font-weight: bold;">NAMA</th>
                        <th style="border: 1px solid #000; font-weight: bold; text-align: right;">JUMLAH</th>
                    </tr>
                    @foreach($details as $index => $tk)
                        <tr>
                            <td style="border: 1px solid #000; text-align: center;">{{ $index + 1 }}</td>
                            <td colspan="2" style="border: 1px solid #000; text-transform: uppercase;">{{ $tk->buruh->nama ?? '-' }}</td>
                            <td style="border: 1px solid #000; text-align: right;">{{ $tk->nominal }}</td>
                        </tr>
                    @endforeach
                    <tr style="background-color: #e9ecef; font-weight: bold;">
                        <td colspan="3" style="border: 1px solid #000; text-align: right; font-weight: bold;">Total</td>
                        <td style="border: 1px solid #000; text-align: right; font-weight: bold;">{{ $details->sum('nominal') }}</td>
                    </tr>
                    <tr></tr> <!-- empty row -->
                @endforeach
            @endif

            <!-- Keterangan -->
            <tr>
                <td colspan="5" style="font-weight: bold;">Keterangan:</td>
            </tr>
            <tr>
                <td colspan="5" style="border: 1px solid #333;">{{ $biayaKapal->keterangan }}</td>
            </tr>
            
            <!-- Separator between invoices -->
            <tr></tr>
            <tr></tr>
            <tr></tr>
        @endforeach
    </tbody>
</table>