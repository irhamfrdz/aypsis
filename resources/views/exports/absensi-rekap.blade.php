<table>
    <thead>
        <!-- Title Header -->
        <tr>
            <th colspan="{{ 2 + $daysInMonth + 8 }}" style="font-size: 14px; font-weight: bold; text-align: center;">
                REKAPITULASI ABSENSI BULANAN
            </th>
        </tr>
        <tr>
            <th colspan="{{ 2 + $daysInMonth + 8 }}" style="font-size: 11px; font-weight: bold; text-align: center;">
                Periode: {{ $monthName }}
            </th>
        </tr>
        <tr></tr> <!-- Blank Row -->

        <!-- Table Header Row 1 -->
        <tr>
            <th rowspan="2" style="background-color: #f3f4f6; font-weight: bold; border: 1px solid #000000; text-align: center; vertical-align: middle;">Nama</th>
            <th rowspan="2" style="background-color: #f3f4f6; font-weight: bold; border: 1px solid #000000; text-align: center; vertical-align: middle;">No. ID</th>
            @foreach($dayHeaders as $h)
                <th style="background-color: #f3f4f6; font-weight: bold; border: 1px solid #000000; text-align: center;">{{ $h['date'] }}</th>
            @endforeach
            <th rowspan="2" style="background-color: #e0f2fe; font-weight: bold; border: 1px solid #000000; text-align: center; vertical-align: middle;">Normal Hari</th>
            <th rowspan="2" style="background-color: #dcfce7; font-weight: bold; border: 1px solid #000000; text-align: center; vertical-align: middle;">Riil Hari</th>
            <th rowspan="2" style="background-color: #fee2e2; font-weight: bold; border: 1px solid #000000; text-align: center; vertical-align: middle;">Absen Hari</th>
            <th rowspan="2" style="background-color: #fef9c3; font-weight: bold; border: 1px solid #000000; text-align: center; vertical-align: middle;">Trlmbt Menit</th>
            <th rowspan="2" style="background-color: #fef9c3; font-weight: bold; border: 1px solid #000000; text-align: center; vertical-align: middle;">Plg. Cpt Menit</th>
            <th rowspan="2" style="background-color: #fef9c3; font-weight: bold; border: 1px solid #000000; text-align: center; vertical-align: middle;">Lmbr Menit</th>
            <th rowspan="2" style="background-color: #f3f4f6; font-weight: bold; border: 1px solid #000000; text-align: center; vertical-align: middle;">Jml. Ijin</th>
            <th rowspan="2" style="background-color: #f3f4f6; font-weight: bold; border: 1px solid #000000; text-align: center; vertical-align: middle;">D. Luar</th>
        </tr>

        <!-- Table Header Row 2 (Day Names) -->
        <tr>
            @foreach($dayHeaders as $h)
                <th style="background-color: #f9fafb; font-weight: bold; border: 1px solid #000000; text-align: center;">{{ $h['dayName'] }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($rekapData as $row)
            <tr>
                <td style="border: 1px solid #000000; text-align: left; font-weight: bold;">
                    {{ $row['karyawan']->nama_lengkap }} ({{ $row['karyawan']->nik }})
                </td>
                <td style="border: 1px solid #000000; text-align: center;">
                    {{ $row['karyawan']->nik }}
                </td>
                @foreach($row['dailyStatus'] as $day => $status)
                    @php
                        // Check if weekend to shade cells
                        $dayName = $dayHeaders[$day]['dayName'];
                        $isWeekend = in_array(strtoupper($dayName), ['SA', 'SU', 'SAB', 'MIN', 'SABTU', 'MINGGU']);
                        $bgColor = '';
                        if ($isWeekend) {
                            $bgColor = 'background-color: #f3f4f6;';
                        } elseif ($status === 'A') {
                            $bgColor = 'background-color: #fee2e2; color: #dc2626; font-weight: bold;';
                        } elseif ($status === '<' || $status === '>') {
                            $bgColor = 'color: #d97706; font-weight: bold;';
                        }
                    @endphp
                    <td style="border: 1px solid #000000; text-align: center; {{ $bgColor }}">
                        {{ $status }}
                    </td>
                @endforeach
                <td style="border: 1px solid #000000; text-align: center; background-color: #f0f9ff;">
                    {{ $row['normalDays'] }}
                </td>
                <td style="border: 1px solid #000000; text-align: center; background-color: #f0fdf4;">
                    {{ $row['riilDays'] }}
                </td>
                <td style="border: 1px solid #000000; text-align: center; background-color: #fef2f2;">
                    {{ $row['absenDays'] }}
                </td>
                <td style="border: 1px solid #000000; text-align: center;">
                    {{ $row['lateMinutes'] ?: '' }}
                </td>
                <td style="border: 1px solid #000000; text-align: center;">
                    {{ $row['earlyMinutes'] ?: '' }}
                </td>
                <td style="border: 1px solid #000000; text-align: center;">
                    <!-- Lmbr Menit (Placeholder / custom logic if any) -->
                </td>
                <td style="border: 1px solid #000000; text-align: center;">
                    <!-- Jml Ijin -->
                </td>
                <td style="border: 1px solid #000000; text-align: center;">
                    <!-- D. Luar -->
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<!-- Legend Footnote -->
<table>
    <tr></tr>
    <tr>
        <td colspan="5" style="font-size: 8px; color: #4b5563;">
            Keterangan: Normal="", Absent="A", Late="<", Early=">"
        </td>
    </tr>
</table>
