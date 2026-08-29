<!DOCTYPE html>
<html>
<head>
    <title>Data Scan Karyawan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
        }
        .header {
            width: 100%;
            margin-bottom: 10px;
        }
        .header table {
            width: 100%;
            border: none;
        }
        .header td {
            border: none;
            font-size: 14px;
            font-weight: bold;
        }
        .header .title {
            text-align: left;
        }
        .header .center {
            text-align: center;
            font-weight: normal;
            font-size: 12px;
        }
        .header .date {
            text-align: right;
            font-weight: normal;
            font-size: 12px;
        }
        
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            table-layout: fixed;
        }
        table.data-table th, table.data-table td {
            border: 1px solid black;
            padding: 4px;
            text-align: center;
            word-wrap: break-word;
        }
        table.data-table tr.emp-header td {
            text-align: left;
            font-weight: bold;
            border-bottom: 1px solid black;
            font-size: 11px;
        }
        
        .footer {
            width: 100%;
            margin-top: 30px;
            font-size: 12px;
        }
        .footer table {
            width: 100%;
            border: none;
        }
        .footer td {
            border: none;
        }
        .footer .right {
            text-align: right;
        }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                <td class="title">Data Scan Karyawan</td>
                <td class="center">({{ $filterTitle }})</td>
                <td class="date">Dari {{ $startDate->format('d-m-Y') }} s/d {{ $endDate->format('d-m-Y') }}</td>
            </tr>
        </table>
    </div>

    @foreach($pdfData as $data)
        @php
            $karyawan = $data['karyawan'];
            $allLogs = $data['logs'];
            // Chunk logs per 10 days
            $chunks = array_chunk($allLogs, 10);
        @endphp

        @foreach($chunks as $chunk)
            @php
                $columnsCount = count($chunk);
            @endphp
            <table class="data-table">
                <tr class="emp-header">
                    <td colspan="2">{{ $karyawan->penempatan ?: 'Jakarta Kantor' }}</td>
                    <td colspan="2" style="text-align: center;">{{ $karyawan->nik }}</td>
                    <td colspan="6">{{ strtoupper($karyawan->nama_lengkap) }}</td>
                </tr>
                
                <tr>
                    @foreach($chunk as $log)
                        <td>{{ $log['date_label'] }}</td>
                    @endforeach
                    @for($i = $columnsCount; $i < 10; $i++)
                        <td></td>
                    @endfor
                </tr>
                
                <tr>
                    @foreach($chunk as $log)
                        <td>{{ $log['scan'] }}</td>
                    @endforeach
                    @for($i = $columnsCount; $i < 10; $i++)
                        <td></td>
                    @endfor
                </tr>
            </table>
        @endforeach
    @endforeach

    <div class="footer">
        <table>
            <tr>
                <td>Oleh: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Supervisor</td>
                <td class="center">{{ date('d/m/Y') }}</td>
                <td class="right">Hal. <script type="text/php">if (isset($pdf)) { echo $PAGE_NUM; }</script></td>
            </tr>
        </table>
    </div>

</body>
</html>
