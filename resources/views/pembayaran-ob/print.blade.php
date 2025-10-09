<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - {{ $pembayaran->nomor_pembayaran }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            background: white;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .document-title {
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
        }
        
        .info-section {
            margin-bottom: 20px;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        
        .info-label {
            width: 150px;
            font-weight: bold;
        }
        
        .info-value {
            flex: 1;
        }
        
        .table-container {
            margin-bottom: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        
        th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: center;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .calculation-section {
            margin-top: 20px;
            border: 1px solid #000;
            padding: 15px;
        }
        
        .calculation-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 3px 0;
        }
        
        .calculation-row.total {
            border-top: 1px solid #000;
            padding-top: 8px;
            margin-top: 10px;
            font-weight: bold;
        }
        
        .signatures {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-box {
            width: 200px;
            text-align: center;
        }
        
        .signature-line {
            border-bottom: 1px solid #000;
            height: 60px;
            margin-bottom: 5px;
        }
        
        .dp-info {
            background-color: #f0f8ff;
            border: 1px solid #0066cc;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 3px;
        }
        
        .supir-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }
        
        .supir-tag {
            background-color: #e3f2fd;
            border: 1px solid #1976d2;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 11px;
        }
        
        @media print {
            .container {
                padding: 0;
            }
            
            .no-print {
                display: none;
            }
        }
        
        .print-button {
            margin-bottom: 20px;
            text-align: center;
        }
        
        .btn {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin: 0 5px;
        }
        
        .btn:hover {
            background-color: #0056b3;
        }
        
        .btn-secondary {
            background-color: #6c757d;
        }
        
        .btn-secondary:hover {
            background-color: #545b62;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Print Button -->
        <div class="print-button no-print">
            <button onclick="window.print()" class="btn">
                <i class="fas fa-print"></i> Print
            </button>
            <button onclick="window.close()" class="btn btn-secondary">
                <i class="fas fa-times"></i> Tutup
            </button>
        </div>

        <!-- Header -->
        <div class="header">
            <div class="company-name">PT. AYPSIS LOGISTICS</div>
            <div>Jl. Contoh Alamat No. 123, Jakarta</div>
            <div>Telp: (021) 1234567 | Email: info@aypsis.com</div>
            <div class="document-title">BUKTI PEMBAYARAN OUT BOUND (OB)</div>
        </div>

        <!-- Document Info -->
        <div class="info-section">
            <div class="info-row">
                <span class="info-label">Nomor Pembayaran:</span>
                <span class="info-value">{{ $pembayaran->nomor_pembayaran }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tanggal Pembayaran:</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('d F Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Akun Kas/Bank:</span>
                <span class="info-value">{{ $pembayaran->kasBankAkun ? $pembayaran->kasBankAkun->nomor_akun . ' - ' . $pembayaran->kasBankAkun->nama_akun : '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Jenis Transaksi:</span>
                <span class="info-value">{{ ucfirst($pembayaran->jenis_transaksi) }}</span>
            </div>
            @if($pembayaran->keterangan)
            <div class="info-row">
                <span class="info-label">Keterangan:</span>
                <span class="info-value">{{ $pembayaran->keterangan }}</span>
            </div>
            @endif
        </div>

        <!-- DP Information (if exists) -->
        @if($dpData)
        <div class="dp-info">
            <h4>Informasi Down Payment (DP)</h4>
            <div class="info-row">
                <span class="info-label">Nomor DP:</span>
                <span class="info-value">{{ $dpData->nomor_pembayaran }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tanggal DP:</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($dpData->tanggal_pembayaran)->format('d F Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Nilai DP:</span>
                <span class="info-value">Rp {{ number_format($dpData->total_pembayaran, 0, ',', '.') }}</span>
            </div>
        </div>
        @endif

        <!-- Supir Table -->
        <div class="table-container">
            <h4>Daftar Supir</h4>
            <table>
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="15%">NIK</th>
                        <th width="30%">Nama Lengkap</th>
                        <th width="20%">Divisi</th>
                        <th width="15%">Status</th>
                        <th width="15%">Pembayaran per Supir</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($supirList as $index => $supir)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $supir->nik }}</td>
                        <td>{{ $supir->nama_lengkap }}</td>
                        <td>{{ $supir->divisi }}</td>
                        <td class="text-center">{{ ucfirst($supir->status) }}</td>
                        <td class="text-right">Rp {{ number_format($pembayaran->jumlah_per_supir, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Calculation Section -->
        <div class="calculation-section">
            <h4>Rincian Pembayaran</h4>
            
            @if($pembayaran->subtotal_pembayaran && $pembayaran->dp_amount > 0)
                <!-- With DP calculation -->
                <div class="calculation-row">
                    <span>Subtotal Pembayaran ({{ count($supirList) }} supir):</span>
                    <span>Rp {{ number_format($pembayaran->subtotal_pembayaran, 0, ',', '.') }}</span>
                </div>
                <div class="calculation-row">
                    <span>Penggunaan DP:</span>
                    <span>- Rp {{ number_format($pembayaran->dp_amount, 0, ',', '.') }}</span>
                </div>
                <div class="calculation-row total">
                    <span>Total yang Dibayarkan:</span>
                    <span>Rp {{ number_format($pembayaran->total_pembayaran, 0, ',', '.') }}</span>
                </div>
                <div class="calculation-row">
                    <span>Pembayaran per Supir:</span>
                    <span>Rp {{ number_format($pembayaran->jumlah_per_supir, 0, ',', '.') }}</span>
                </div>
            @else
                <!-- Without DP calculation -->
                <div class="calculation-row">
                    <span>Jumlah Supir:</span>
                    <span>{{ count($supirList) }} orang</span>
                </div>
                <div class="calculation-row">
                    <span>Pembayaran per Supir:</span>
                    <span>Rp {{ number_format($pembayaran->jumlah_per_supir, 0, ',', '.') }}</span>
                </div>
                <div class="calculation-row total">
                    <span>Total Pembayaran:</span>
                    <span>Rp {{ number_format($pembayaran->total_pembayaran, 0, ',', '.') }}</span>
                </div>
            @endif
        </div>

        <!-- Signatures -->
        <div class="signatures">
            <div class="signature-box">
                <div>Dibuat Oleh</div>
                <div class="signature-line"></div>
                <div>{{ $pembayaran->pembuatPembayaran ? $pembayaran->pembuatPembayaran->name : '-' }}</div>
                <div>{{ $pembayaran->created_at ? $pembayaran->created_at->format('d/m/Y H:i') : '-' }}</div>
            </div>
            
            <div class="signature-box">
                <div>Disetujui Oleh</div>
                <div class="signature-line"></div>
                <div>{{ $pembayaran->penyetujuPembayaran ? $pembayaran->penyetujuPembayaran->name : '-' }}</div>
                <div>{{ $pembayaran->tanggal_persetujuan ? $pembayaran->tanggal_persetujuan->format('d/m/Y H:i') : '-' }}</div>
            </div>
        </div>

        <!-- Footer -->
        <div style="margin-top: 30px; text-align: center; font-size: 10px; color: #666;">
            <p>Dokumen ini digenerate secara otomatis pada {{ now()->format('d F Y H:i:s') }}</p>
            <p>PT. AYPSIS LOGISTICS - Sistem Manajemen Pembayaran</p>
        </div>
    </div>

    <script>
        // Auto print when page loads (optional)
        // window.onload = function() {
        //     window.print();
        // }
    </script>
</body>
</html>