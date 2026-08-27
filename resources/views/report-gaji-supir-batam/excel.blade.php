<table>
    <thead>
        <tr>
            <th colspan="8" style="font-weight: bold; font-size: 14px; text-align: center;">REPORT GAJI SUPIR BATAM</th>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center;">
                Periode: {{ $startDate ? date('d/m/Y', strtotime($startDate)) : 'Awal' }} s/d {{ $endDate ? date('d/m/Y', strtotime($endDate)) : 'Akhir' }}
            </th>
        </tr>
        <tr>
            <th colspan="8"></th>
        </tr>
        <tr>
            <th style="font-weight: bold; background-color: #f3f4f6; border: 1px solid #000;">Periode</th>
            <th style="font-weight: bold; background-color: #f3f4f6; border: 1px solid #000;">Nama Supir</th>
            <th style="font-weight: bold; background-color: #f3f4f6; border: 1px solid #000;">Plat Kendaraan</th>
            <th style="font-weight: bold; background-color: #f3f4f6; border: 1px solid #000;">Gaji Pokok</th>
            <th style="font-weight: bold; background-color: #f3f4f6; border: 1px solid #000;">Biaya Bensin</th>
            <th style="font-weight: bold; background-color: #f3f4f6; border: 1px solid #000;">Uang Malam/Libur</th>
            <th style="font-weight: bold; background-color: #f3f4f6; border: 1px solid #000;">Potongan 5%</th>
            <th style="font-weight: bold; background-color: #f3f4f6; border: 1px solid #000;">Total Gaji</th>
        </tr>
    </thead>
    <tbody>
        @php 
            $sumGajiPokok = 0;
            $sumBensin = 0;
            $sumMalamLibur = 0;
            $sumPotongan = 0;
            $sumTotal = 0;
        @endphp
        
        @foreach($gajiList as $gaji)
            @php
                $sumGajiPokok += $gaji->gaji_pokok;
                $sumBensin += $gaji->biaya_bensin;
                $sumMalamLibur += $gaji->uang_malam_libur;
                $sumPotongan += $gaji->nominal_potongan_5_persen;
                $sumTotal += $gaji->total_gaji;
            @endphp
            <tr>
                <td style="border: 1px solid #000;">{{ $gaji->periode_text }}</td>
                <td style="border: 1px solid #000;">{{ $gaji->karyawan->nama_lengkap ?? '-' }}</td>
                <td style="border: 1px solid #000;">{{ $gaji->karyawan->plat ?? '-' }}</td>
                <td style="border: 1px solid #000;" data-format="#,##0">{{ $gaji->gaji_pokok }}</td>
                <td style="border: 1px solid #000;" data-format="#,##0">{{ $gaji->biaya_bensin }}</td>
                <td style="border: 1px solid #000;" data-format="#,##0">{{ $gaji->uang_malam_libur }}</td>
                <td style="border: 1px solid #000;" data-format="#,##0">{{ $gaji->nominal_potongan_5_persen }}</td>
                <td style="border: 1px solid #000;" data-format="#,##0">{{ $gaji->total_gaji }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3" style="font-weight: bold; text-align: right; border: 1px solid #000;">TOTAL:</td>
            <td style="font-weight: bold; border: 1px solid #000;" data-format="#,##0">{{ $sumGajiPokok }}</td>
            <td style="font-weight: bold; border: 1px solid #000;" data-format="#,##0">{{ $sumBensin }}</td>
            <td style="font-weight: bold; border: 1px solid #000;" data-format="#,##0">{{ $sumMalamLibur }}</td>
            <td style="font-weight: bold; border: 1px solid #000;" data-format="#,##0">{{ $sumPotongan }}</td>
            <td style="font-weight: bold; border: 1px solid #000;" data-format="#,##0">{{ $sumTotal }}</td>
        </tr>
    </tfoot>
</table>
