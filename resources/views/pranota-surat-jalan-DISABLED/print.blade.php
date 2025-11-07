<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Pranota Surat Jalan - {{ $pranotaSuratJalan->nomor_pranota }}</title>
    <style>
        @media print {
            @page {
                margin: 1cm;
                size: A4;
            }
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .document-title {
            font-size: 16px;
            font-weight: bold;
            margin-top: 15px;
        }
        
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        
        .info-left, .info-right {
            width: 48%;
        }
        
        .info-row {
            margin-bottom: 8px;
        }
        
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }
        
        .table-container {
            margin-bottom: 30px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            border: 1px solid #333;
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
        
        .summary-section {
            margin-top: 30px;
            display: flex;
            justify-content: flex-end;
        }
        
        .summary-table {
            width: 300px;
        }
        
        .summary-table td {
            padding: 10px;
            border: 1px solid #333;
        }
        
        .summary-total {
            font-weight: bold;
            background-color: #f5f5f5;
        }
        
        .footer {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-box {
            width: 200px;
            text-align: center;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 60px;
            padding-top: 5px;
        }
        
        .breakdown-highlight {
            background-color: #f0f8ff;
            border: 2px solid #cce7ff;
        }
        
        .print-info {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
        
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Print Button (hidden when printing) -->
    <div class="no-print" style="text-align: right; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
            🖨️ Print
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px;">
            ✖️ Tutup
        </button>
    </div>

    <!-- Header -->
    <div class="header">
        <div class="company-name">AYPSIS</div>
        <div>Sistem Informasi Pelabuhan</div>
        <div class="document-title">PRANOTA SURAT JALAN</div>
    </div>

    <!-- Pranota Information -->
    <div class="info-section">
        <div class="info-left">
            <div class="info-row">
                <span class="info-label">Nomor Pranota:</span>
                <span>{{ $pranotaSuratJalan->nomor_pranota }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tanggal:</span>
                <span>{{ $pranotaSuratJalan->tanggal_pranota ? $pranotaSuratJalan->tanggal_pranota->format('d/m/Y') : '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Status:</span>
                <span>{{ ucfirst(str_replace('_', ' ', $pranotaSuratJalan->status_pembayaran ?? 'Belum Bayar')) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Dibuat oleh:</span>
                <span>{{ $pranotaSuratJalan->creator->name ?? '-' }}</span>
            </div>
        </div>
        <div class="info-right">
            <div class="info-row">
                <span class="info-label">Nomor SJ:</span>
                <span>{{ optional($pranotaSuratJalan->surat_jalan)->nomor_surat_jalan ?? '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Supir:</span>
                <span>{{ optional(optional($pranotaSuratJalan->surat_jalan)->karyawan)->nama_lengkap ?? optional($pranotaSuratJalan->surat_jalan)->supir ?? '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tujuan:</span>
                <span>{{ optional(optional($pranotaSuratJalan->surat_jalan)->tujuan)->nama_tujuan ?? '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Total Amount:</span>
                <span style="font-weight: bold; color: #007bff;">{{ $pranotaSuratJalan->formatted_total_amount }}</span>
            </div>
        </div>
    </div>

    <!-- Surat Jalan Details -->
    <div class="table-container">
        <h3>Detail Surat Jalan</h3>
        @if($pranotaSuratJalan->surat_jalan)
        <table>
            <thead>
                <tr>
                    <th width="15%">Nomor SJ</th>
                    <th width="12%">Tanggal</th>
                    <th width="20%">Tujuan</th>
                    <th width="20%">Supir</th>
                    <th width="10%">Kontainer</th>
                    <th width="10%">Kegiatan</th>
                    <th width="13%">Uang Jalan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ optional($pranotaSuratJalan->surat_jalan)->nomor_surat_jalan ?? '-' }}</td>
                    <td class="text-center">{{ optional($pranotaSuratJalan->surat_jalan)->tanggal_surat_jalan ? optional($pranotaSuratJalan->surat_jalan)->tanggal_surat_jalan->format('d/m/Y') : '-' }}</td>
                    <td>{{ optional(optional($pranotaSuratJalan->surat_jalan)->tujuan)->nama_tujuan ?? '-' }}</td>
                    <td>{{ optional(optional($pranotaSuratJalan->surat_jalan)->karyawan)->nama_lengkap ?? optional($pranotaSuratJalan->surat_jalan)->supir ?? '-' }}</td>
                    <td class="text-center">{{ optional($pranotaSuratJalan->surat_jalan)->jumlah_kontainer ?? 0 }}</td>
                    <td class="text-center">{{ optional($pranotaSuratJalan->surat_jalan)->kegiatan ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($pranotaSuratJalan->uang_jalan ?? 0, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
        @else
        <div style="text-align: center; padding: 20px; border: 1px solid #ddd; background-color: #f9f9f9;">
            <p style="margin: 0; font-style: italic; color: #666;">Tidak ada surat jalan terkait</p>
        </div>
        @endif
    </div>

    <!-- Breakdown Details -->
    <div class="table-container">
        <h3>Rincian Biaya</h3>
        <table>
            <thead>
                <tr>
                    <th width="20%">Jenis Biaya</th>
                    <th width="20%">Jumlah</th>
                    <th width="60%">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>MEL</td>
                    <td class="text-right">Rp {{ number_format($pranotaSuratJalan->jumlah_mel ?? 0, 0, ',', '.') }}</td>
                    <td>Biaya MEL (Muat, Export, Load)</td>
                </tr>
                <tr>
                    <td>Kawalan</td>
                    <td class="text-right">Rp {{ number_format($pranotaSuratJalan->jumlah_kawalan ?? 0, 0, ',', '.') }}</td>
                    <td>Biaya pengawalan dan pengamanan</td>
                </tr>
                <tr>
                    <td>Pelancar</td>
                    <td class="text-right">Rp {{ number_format($pranotaSuratJalan->jumlah_pelancar ?? 0, 0, ',', '.') }}</td>
                    <td>Biaya pelancar administrasi</td>
                </tr>
                <tr>
                    <td>Parkir</td>
                    <td class="text-right">Rp {{ number_format($pranotaSuratJalan->jumlah_parkir ?? 0, 0, ',', '.') }}</td>
                    <td>Biaya parkir kendaraan</td>
                </tr>
                <tr class="breakdown-highlight">
                    <td><strong>Uang Jalan</strong></td>
                    <td class="text-right"><strong>Rp {{ number_format($pranotaSuratJalan->uang_jalan ?? 0, 0, ',', '.') }}</strong></td>
                    <td><strong>Uang perjalanan supir</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Summary -->
    <div class="summary-section">
        <table class="summary-table">
            <tr>
                <td>MEL:</td>
                <td class="text-right">Rp {{ number_format($pranotaSuratJalan->jumlah_mel ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Kawalan:</td>
                <td class="text-right">Rp {{ number_format($pranotaSuratJalan->jumlah_kawalan ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Pelancar:</td>
                <td class="text-right">Rp {{ number_format($pranotaSuratJalan->jumlah_pelancar ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Parkir:</td>
                <td class="text-right">Rp {{ number_format($pranotaSuratJalan->jumlah_parkir ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Uang Jalan:</td>
                <td class="text-right">Rp {{ number_format($pranotaSuratJalan->uang_jalan ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr class="summary-total">
                <td><strong>Total Keseluruhan:</strong></td>
                <td class="text-right"><strong>{{ $pranotaSuratJalan->formatted_total_amount }}</strong></td>
            </tr>
        </table>
    </div>

    <!-- Signatures -->
    <div class="footer">
        <div class="signature-box">
            <div>Dibuat Oleh:</div>
            <div class="signature-line">{{ $pranotaSuratJalan->creator->name ?? 'Admin' }}</div>
            <div>Bagian Operasional</div>
        </div>
        <div class="signature-box">
            <div>Disetujui Oleh:</div>
            <div class="signature-line">(...........................)</div>
            <div>Supervisor</div>
        </div>
    </div>

    @if($pranotaSuratJalan->catatan)
    <!-- Notes Section -->
    <div style="margin-top: 30px; border: 1px solid #ddd; padding: 15px; background-color: #f9f9f9;">
        <h4 style="margin: 0 0 10px 0;">Catatan:</h4>
        <p style="margin: 0; font-style: italic;">{{ $pranotaSuratJalan->catatan }}</p>
    </div>
    @endif

    <!-- Print Information -->
    <div class="print-info">
        Dicetak pada: {{ now()->format('d/m/Y H:i') }} | Nomor: {{ $pranotaSuratJalan->nomor_pranota }} | Status: {{ ucfirst($pranotaSuratJalan->status_pembayaran ?? 'belum_bayar') }}
    </div>

    <script>
        // Auto print when page loads (optional)
        // window.onload = function() { window.print(); }
        
        // Close window after printing
        window.onafterprint = function() {
            // window.close(); // Uncomment if you want auto close after print
        }
    </script>
</body>
</html>