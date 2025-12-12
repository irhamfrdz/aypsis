<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Naik Kapal - {{ $kapal->nama_kapal }} - {{ $voyage }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            padding: 20px;
            color: #000;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
        }
        
        .header h1 {
            font-size: 18pt;
            margin-bottom: 5px;
        }
        
        .header p {
            font-size: 11pt;
            color: #555;
        }
        
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f5f5f5;
            border-radius: 5px;
        }
        
        .info-item {
            display: flex;
            align-items: center;
        }
        
        .info-label {
            font-weight: bold;
            margin-right: 8px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th {
            background-color: #4a5568;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 9pt;
            font-weight: bold;
            border: 1px solid #333;
        }
        
        td {
            padding: 6px 8px;
            border: 1px solid #ddd;
            font-size: 9pt;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .status-badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 8pt;
            font-weight: bold;
            display: inline-block;
        }
        
        .status-sudah {
            background-color: #c6f6d5;
            color: #22543d;
        }
        
        .status-belum {
            background-color: #fef5e7;
            color: #744210;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: right;
            font-size: 9pt;
            color: #666;
        }
        
        @media print {
            body {
                padding: 10px;
            }
            
            .no-print {
                display: none;
            }
            
            table {
                page-break-inside: auto;
            }
            
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            
            thead {
                display: table-header-group;
            }
        }
        
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #3b82f6;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12pt;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .print-button:hover {
            background-color: #2563eb;
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">🖨️ Print</button>
    
    <div class="header">
        <h1>Naik Kapal</h1>
        <p>Daftar kontainer yang naik kapal</p>
    </div>
    
    <div class="info-section">
        <div class="info-item">
            <span class="info-label">🚢 Kapal:</span>
            <span>{{ $kapal->nama_kapal }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Voyage:</span>
            <span>{{ $voyage }}</span>
        </div>
        @if($statusFilter)
        <div class="info-item">
            <span class="info-label">Filter Status:</span>
            <span>{{ $statusFilter === 'sudah_bl' ? 'Sudah BL' : 'Belum BL' }}</span>
        </div>
        @endif
        <div class="info-item">
            <span class="info-label">Total:</span>
            <span>{{ $naikKapals->count() }} kontainer</span>
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">Kontainer</th>
                <th style="width: 20%;">Barang</th>
                <th style="width: 15%;">Tipe</th>
                <th style="width: 10%;">Tgl TT</th>
                <th style="width: 20%;">Kapal & Voyage</th>
                <th style="width: 15%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($naikKapals as $index => $naikKapal)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $naikKapal->nomor_kontainer }}</strong><br>
                    <span style="color: #666;">{{ $naikKapal->ukuran_kontainer }}</span>
                    @if($naikKapal->no_seal)
                    <br><span style="color: #2563eb; font-size: 8pt;">Seal: {{ $naikKapal->no_seal }}</span>
                    @endif
                </td>
                <td>{{ $naikKapal->jenis_barang ?? '-' }}</td>
                <td>
                    {{ $naikKapal->tipe_kontainer }}
                    @if($naikKapal->tipe_kontainer_detail)
                    <br><span style="color: #666; font-size: 8pt;">{{ $naikKapal->tipe_kontainer_detail }}</span>
                    @endif
                </td>
                <td style="text-align: center;">
                    @php
                        $tanggal = $naikKapal->prospek?->tandaTerima?->tanggal 
                                   ?? $naikKapal->prospek?->tandaTerima?->tanggal_checkpoint_supir;
                    @endphp
                    @if($tanggal)
                        {{ \Carbon\Carbon::parse($tanggal)->format('d/m/Y') }}
                    @else
                        <span style="color: #999;">-</span>
                    @endif
                </td>
                <td>
                    <strong>{{ $naikKapal->nama_kapal }}</strong>
                    @if($naikKapal->no_voyage)
                    <br><span style="color: #666;">V: {{ $naikKapal->no_voyage }}</span>
                    @endif
                    @if($naikKapal->pelabuhan_tujuan)
                    <br><span style="color: #16a34a; font-size: 8pt;">→ {{ $naikKapal->pelabuhan_tujuan }}</span>
                    @endif
                </td>
                <td>&nbsp;</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 20px; color: #999;">
                    Tidak ada data kontainer
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i') }} WIB
    </div>
    
    <script>
        // Auto print when page loads (optional)
        // window.onload = function() { window.print(); };
    </script>
</body>
</html>
