<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Cetak Pembayaran - {{ $pembayaran->nomor_pembayaran ?? '' }}</title>
    <style>
        /* Basic print-reset */
        html,body{margin:0;padding:0;font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", "Liberation Sans", sans-serif;color:#111}
        .container{max-width:800px;margin:24px auto;padding:16px}
        .card{border:1px solid #ddd;padding:18px;border-radius:6px}
        h1{font-size:18px;margin:0 0 12px}
        .row{margin-bottom:10px}
        .label{font-weight:600;display:inline-block;width:180px}
        ul.pranotas{margin:6px 0 0 0;padding-left:18px}

        /* Remove shadows/backgrounds on print */
        @media print{
            body, .container{background: #fff}
            .card{box-shadow:none;border: none}
            a, button{display:none}
            @page{margin:10mm}
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>Cetak Pembayaran</h1>

            <div class="row"><span class="label">Nomor Pembayaran:</span> {{ $pembayaran->nomor_pembayaran ?? '-' }}</div>
            <div class="row"><span class="label">Tanggal Pembayaran:</span> {{ $pembayaran->tanggal_pembayaran ? \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('d/m/Y') : '-' }}</div>
            <div class="row"><span class="label">Bank:</span> {{ $pembayaran->bank ?? '-' }}</div>
            <div class="row"><span class="label">Jenis Transaksi:</span> {{ $pembayaran->jenis_transaksi ?? '-' }}</div>
            <div class="row"><span class="label">Total Pembayaran:</span> Rp {{ number_format($pembayaran->total_pembayaran ?? 0, 2, ',', '.') }}</div>

            <div class="row"><span class="label">Pranotas:</span>
                <table style="width:100%;border-collapse:collapse;margin-top:6px">
                    <thead>
                        <tr>
                            <th style="text-align:left;padding:6px;border-bottom:1px solid #ddd">No. Pranota</th>
                            <th style="text-align:left;padding:6px;border-bottom:1px solid #ddd">Supir</th>
                            <th style="text-align:left;padding:6px;border-bottom:1px solid #ddd">Tujuan</th>
                            <th style="text-align:left;padding:6px;border-bottom:1px solid #ddd">Kegiatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pembayaran->pranotas as $pranota)
                            @php
                                // Try to obtain a related permohonan for this pranota to read supir/tujuan/kegiatan.
                                $perm = $pranota->permohonans->first();
                                $supirName = $perm && $perm->supir ? ($perm->supir->nama_lengkap ?? $perm->supir->nama_panggilan) : ($pranota->supir_name ?? '-');
                                $tujuan = $perm->tujuan ?? ($pranota->tujuan ?? '-');
                                $kegiatan = $perm->kegiatan ?? ($pranota->kegiatan ?? '-');
                            @endphp
                            <tr>
                                <td style="padding:6px;border-bottom:1px solid #f3f3f3">{{ $pranota->nomor_pranota }}</td>
                                <td style="padding:6px;border-bottom:1px solid #f3f3f3">{{ $supirName }}</td>
                                <td style="padding:6px;border-bottom:1px solid #f3f3f3">{{ $tujuan }}</td>
                                <td style="padding:6px;border-bottom:1px solid #f3f3f3">{{ $kegiatan }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Signature blocks -->
            <div style="margin-top:40px;display:flex;gap:24px;justify-content:space-between">
                <div style="flex:1;text-align:center">
                    <div style="height:60px;border-bottom:1px dashed #333;margin-bottom:8px"></div>
                    <div style="font-size:12px;color:#555">(Pemohon)</div>
                </div>
                <div style="flex:1;text-align:center">
                    <div style="height:60px;border-bottom:1px dashed #333;margin-bottom:8px"></div>
                    <div style="font-size:12px;color:#555">(Pemeriksa)</div>
                </div>
                <div style="flex:1;text-align:center">
                    <div style="height:60px;border-bottom:1px dashed #333;margin-bottom:8px"></div>
                    <div style="font-size:12px;color:#555">(Supir)</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Optional: auto-open print dialog when page opens in a new tab
        (function(){
            // If opened in a new tab/window, auto-print after a short delay
            if (window.opener || window.performance && window.performance.getEntriesByType('navigation')[0]?.type === 'navigate'){
                // do not auto-print when navigating inside the app; only when opened directly
            }
            // Keep auto-print disabled by default. Uncomment next line to enable automatic print:
            // setTimeout(()=>window.print(), 300);
        })();
    </script>
</body>
</html>
