<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Pranota Ongkos Truk - {{ $pranota->no_pranota }}</title>
    <style>
        @page { size: A4; margin: 10mm; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 10pt; color: #333; line-height: 1.4; background: #f0f0f0; }
        .container { width: 210mm; margin: 0 auto; background: white; padding: 20mm; min-height: 297mm; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #1a56db; padding-bottom: 15px; position: relative; }
        .header h1 { margin: 0; font-size: 22pt; color: #1a56db; text-transform: uppercase; letter-spacing: 2px; }
        .header p { margin: 5px 0; font-weight: bold; color: #666; }
        
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; }
        .info-box { border: 1px solid #e5e7eb; padding: 15px; rounded: 8px; }
        .info-row { display: flex; margin-bottom: 5px; }
        .info-label { font-weight: bold; width: 120px; color: #4b5563; }
        .info-value { flex: 1; color: #111827; }

        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .items-table th { background-color: #f8fafc; border: 1px solid #e2e8f0; padding: 10px; font-size: 9pt; text-align: left; color: #475569; text-transform: uppercase; }
        .items-table td { border: 1px solid #e2e8f0; padding: 10px; font-size: 9pt; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        .totals-section { margin-left: auto; width: 300px; }
        .total-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9; }
        .total-row.grand-total { border-bottom: none; margin-top: 5px; padding-top: 10px; border-top: 2px solid #1a56db; }
        .total-label { font-weight: bold; color: #64748b; }
        .total-value { font-weight: bold; color: #1e293b; }
        .grand-total .total-label { color: #1a56db; font-size: 12pt; }
        .grand-total .total-value { color: #1a56db; font-size: 14pt; }

        .footer { margin-top: 60px; }
        .signatures { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 40px; }
        .sig-box { text-align: center; }
        .sig-space { height: 80px; border-bottom: 1px dashed #cbd5e1; margin-bottom: 10px; }
        .sig-name { font-weight: bold; color: #1e293b; }
        .sig-title { font-size: 8pt; color: #64748b; font-style: italic; }

        @media print {
            body { background: white; padding: 0; }
            .container { box-shadow: none; margin: 0; width: 100%; padding: 10mm; }
            .no-print { display: none; }
            .header h1 { color: black; }
            .header { border-bottom-color: black; }
            .grand-total .total-label, .grand-total .total-value { color: black; }
            .total-row.grand-total { border-top-color: black; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Pranota Ongkos Truk</h1>
            <p>NOMOR: {{ $pranota->no_pranota }}</p>
        </div>

        <div class="info-grid">
            <div class="info-box">
                <div class="info-row">
                    <div class="info-label">Tanggal</div>
                    <div class="info-value">: {{ $pranota->tanggal_pranota->format('d F Y') }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Dibuat Oleh</div>
                    <div class="info-value">: {{ $pranota->creator->username ?? 'Admin' }}</div>
                </div>
            </div>
            <div class="info-box">
                <div class="info-row">
                    <div class="info-label">Status</div>
                    <div class="info-value">: <span style="font-weight: bold; text-transform: uppercase;">{{ $pranota->status }}</span></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Keterangan</div>
                    <div class="info-value">: {{ $pranota->keterangan ?? '-' }}</div>
                </div>
            </div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th class="text-center" style="width: 40px;">No</th>
                    <th>No. Surat Jalan</th>
                    <th>Tanggal</th>
                    <th>Tujuan</th>
                    <th class="text-right">Nominal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pranota->items as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td style="font-weight: bold;">{{ $item->no_surat_jalan }}</td>
                        <td>{{ $item->tanggal ? $item->tanggal->format('d/m/Y') : '-' }}</td>
                        <td>
                            @php
                                $tujuan = '-';
                                if($item->type === 'SuratJalan' && $item->suratJalan) {
                                    $tujuan = $item->suratJalan->tujuanPengambilanRelation->nama_tujuan ?? '-';
                                } elseif($item->type === 'SuratJalanBongkaran' && $item->suratJalanBongkaran) {
                                    $tujuan = $item->suratJalanBongkaran->tujuanPengambilanRelation->nama_tujuan ?? '-';
                                }
                            @endphp
                            {{ $tujuan }}
                        </td>
                        <td class="text-right">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals-section">
            <div class="total-row">
                <div class="total-label">Subtotal</div>
                <div class="total-value">Rp {{ number_format($pranota->items->sum('nominal'), 0, ',', '.') }}</div>
            </div>
            <div class="total-row">
                <div class="total-label">Adjustment</div>
                <div class="total-value">Rp {{ number_format($pranota->adjustment, 0, ',', '.') }}</div>
            </div>
            <div class="total-row grand-total">
                <div class="total-label">TOTAL AKHIR</div>
                <div class="total-value">Rp {{ number_format($pranota->total_nominal, 0, ',', '.') }}</div>
            </div>
        </div>

        <div class="footer">
            <div class="signatures">
                <div class="sig-box">
                    <p class="sig-title">Dibuat Oleh,</p>
                    <div class="sig-space"></div>
                    <p class="sig-name">{{ $pranota->creator->username ?? 'Admin' }}</p>
                </div>
                <div class="sig-box">
                    <p class="sig-title">Diketahui Oleh,</p>
                    <div class="sig-space"></div>
                    <p class="sig-name">__________________</p>
                    <p class="sig-title">Manager Operasional</p>
                </div>
                <div class="sig-box">
                    <p class="sig-title">Disetujui Oleh,</p>
                    <div class="sig-space"></div>
                    <p class="sig-name">__________________</p>
                    <p class="sig-title">Finance / Accounting</p>
                </div>
            </div>
        </div>

        <div class="no-print" style="margin-top: 50px; display: flex; justify-content: center; gap: 15px;">
            <button onclick="window.print()" style="padding: 12px 25px; background: #1a56db; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; display: flex; items-center: center; gap: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
                    <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a.5.5 0 0 0 0 1h6a.5.5 0 0 0 0-1H5zm0 2a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1H5zm7 0a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-.5.5H4a.5.5 0 0 1-.5-.5v-3a.5.5 0 0 1 .5-.5h8z"/>
                </svg>
                CETAK DOKUMEN
            </button>
            <button onclick="window.close()" style="padding: 12px 25px; background: #94a3b8; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold;">
                TUTUP
            </button>
        </div>
    </div>
</body>
</html>
