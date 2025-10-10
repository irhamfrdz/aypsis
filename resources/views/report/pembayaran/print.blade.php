<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Pembayaran - {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: white;
        }

        .container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }

        .header h1 {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .header h2 {
            font-size: 18px;
            color: #666;
            margin-bottom: 10px;
        }

        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .info-left, .info-right {
            width: 48%;
        }

        .info-item {
            margin-bottom: 8px;
        }

        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }

        .summary-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }

        .summary-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            background-color: #f9fafb;
        }

        .summary-card h3 {
            font-size: 14px;
            color: #666;
            margin-bottom: 8px;
        }

        .summary-card .value {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }

        .summary-card .sub-value {
            font-size: 10px;
            color: #666;
            margin-top: 4px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 8px 6px;
            text-align: left;
            vertical-align: middle;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 10px;
            text-align: center;
            white-space: nowrap;
        }

        .table td {
            font-size: 9px;
        }

        .table .text-right {
            text-align: right;
        }

        .table .text-center {
            text-align: center;
        }

        .table tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .badge {
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
        }

        .badge-ob {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .badge-dp {
            background-color: #dcfce7;
            color: #166534;
        }

        .badge-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-approved {
            background-color: #dcfce7;
            color: #166534;
        }

        .badge-rejected {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }

        .total-row {
            background-color: #f8f9fa;
            font-weight: bold;
            border-top: 2px solid #333;
        }

        @media print {
            @page {
                size: A4 landscape;
                margin: 10mm;
            }

            body {
                font-size: 8px;
            }

            .container {
                padding: 0;
                max-width: 100%;
            }

            .header {
                margin-bottom: 15px;
                padding-bottom: 10px;
            }

            .header h1 {
                font-size: 18px;
                margin-bottom: 3px;
            }

            .header h2 {
                font-size: 14px;
                margin-bottom: 5px;
            }

            .info-section {
                margin-bottom: 15px;
                font-size: 7px;
            }

            .summary-cards {
                margin-bottom: 15px;
                gap: 10px;
            }

            .summary-card {
                padding: 8px;
            }

            .summary-card .value {
                font-size: 12px;
            }

            .table {
                page-break-inside: avoid;
                margin-bottom: 15px;
            }

            .table th,
            .table td {
                padding: 3px 2px;
                font-size: 7px;
            }

            .table th {
                font-size: 7px;
            }

            .footer {
                margin-top: 15px;
                padding-top: 8px;
                font-size: 6px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div style="text-align: left; margin-bottom: 15px;">
                <strong style="font-size: 14px;">PT. ALEXINDO YAKINPRIMA</strong><br>
                <span style="font-size: 11px;">Jalan Pluit Raya No.8 Blok B No.12</span>
            </div>
            <h1>REPORT PEMBAYARAN</h1>
            <h2>Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</h2>
        </div>

        <!-- Info Section -->
        <div class="info-section">
            <div class="info-left">
                <div class="info-item">
                    <span class="info-label">Tanggal Print:</span>
                    <span>{{ now()->format('d/m/Y H:i:s') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Jenis Pembayaran:</span>
                    <span>{{ $jenisPembayaran == 'all' ? 'Semua' : ($jenisPembayaran == 'ob' ? 'Pembayaran OB' : 'Pembayaran DP') }}</span>
                </div>
            </div>
            <div class="info-right">
                <div class="info-item">
                    <span class="info-label">Status:</span>
                    <span>{{ $status == 'all' ? 'Semua' : ucfirst($status) }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Total Transaksi:</span>
                    <span>{{ number_format($summary['total_transaksi']) }} transaksi</span>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="summary-cards">
            <div class="summary-card">
                <h3>Total Pembayaran</h3>
                <div class="value">Rp {{ number_format($summary['total_pembayaran'], 0, ',', '.') }}</div>
                <div class="sub-value">{{ $summary['total_transaksi'] }} transaksi</div>
            </div>

            @if(isset($summary['breakdown']) && count($summary['breakdown']) > 0)
                @php $displayedCards = 0; @endphp
                @foreach($summary['breakdown'] as $key => $breakdown)
                    @if($displayedCards < 3)
                        <div class="summary-card">
                            <h3>{{ $breakdown['label'] }}</h3>
                            <div class="value">Rp {{ number_format($breakdown['total'], 0, ',', '.') }}</div>
                            <div class="sub-value">{{ $breakdown['count'] }} transaksi</div>
                        </div>
                        @php $displayedCards++; @endphp
                    @endif
                @endforeach
            @endif

            @if($displayedCards < 3)
                <div class="summary-card">
                    <h3>Rata-rata per Transaksi</h3>
                    <div class="value">Rp {{ $summary['total_transaksi'] > 0 ? number_format($summary['total_pembayaran'] / $summary['total_transaksi'], 0, ',', '.') : '0' }}</div>
                </div>
            @endif
        </div>

        @if(isset($summary['breakdown']) && count($summary['breakdown']) > 3)
            <!-- Additional Breakdown -->
            <div style="margin-bottom: 20px;">
                <h3 style="font-size: 12px; margin-bottom: 10px; font-weight: bold;">Rincian per Jenis Pembayaran:</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 8px;">
                    @foreach($summary['breakdown'] as $key => $breakdown)
                        <div style="background-color: #f8f9fa; padding: 8px; border-radius: 4px; text-align: center; border: 1px solid #e9ecef;">
                            <div style="font-size: 10px; color: #666; margin-bottom: 2px;">{{ $breakdown['label'] }}</div>
                            <div style="font-size: 11px; font-weight: bold; color: #333;">{{ $breakdown['count'] }}</div>
                            <div style="font-size: 9px; color: #666;">Rp {{ number_format($breakdown['total'], 0, ',', '.') }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Table -->
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 4%;">No</th>
                    <th style="width: 10%;">Tanggal</th>
                    <th style="width: 18%;">Nomor Pembayaran</th>
                    <th style="width: 12%;">Jenis</th>
                    <th style="width: 15%;">Total Pembayaran</th>
                    <th style="width: 20%;">Akun Kas/Bank</th>
                    <th style="width: 8%;">Status</th>
                    <th style="width: 13%;">Dibuat Oleh</th>
                </tr>
            </thead>
            <tbody>
                @forelse($allPembayaran as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">
                        @if(isset($item->tanggal_pembayaran))
                            {{ \Carbon\Carbon::parse($item->tanggal_pembayaran)->format('d/m/Y') }}
                        @elseif(isset($item->tanggal_kas))
                            {{ \Carbon\Carbon::parse($item->tanggal_kas)->format('d/m/Y') }}
                        @else
                            -
                        @endif
                    </td>
                    <td style="font-family: monospace;">{{ $item->nomor_pembayaran }}</td>
                    <td class="text-center">
                        @php
                            $className = get_class($item);
                            $badges = [
                                'PembayaranOb' => ['label' => 'OB', 'class' => 'badge-ob'],
                                'PembayaranDpOb' => ['label' => 'DP', 'class' => 'badge-dp'],
                                'PembayaranPranotaSupir' => ['label' => 'P.Supir', 'class' => 'badge-ob'],
                                'PembayaranPranotaKontainer' => ['label' => 'P.Kontainer', 'class' => 'badge-dp'],
                                'PembayaranPranotaCat' => ['label' => 'P.CAT', 'class' => 'badge-ob'],
                                'PembayaranPranotaPerbaikanKontainer' => ['label' => 'P.Perbaikan', 'class' => 'badge-dp'],
                                'PembayaranAktivitasLainnya' => ['label' => 'Aktivitas', 'class' => 'badge-ob']
                            ];

                            $currentBadge = null;
                            foreach ($badges as $class => $badge) {
                                if (strpos($className, $class) !== false) {
                                    $currentBadge = $badge;
                                    break;
                                }
                            }
                            $currentBadge = $currentBadge ?? ['label' => 'Unknown', 'class' => 'badge-pending'];
                        @endphp
                        <span class="badge {{ $currentBadge['class'] }}">{{ $currentBadge['label'] }}</span>
                    </td>
                    <td class="text-right">
                        Rp {{ number_format(($item->total_pembayaran ?? $item->nominal_pembayaran ?? 0), 0, ',', '.') }}
                    </td>
                    <td>
                        @if(isset($item->kasBankAkun) && $item->kasBankAkun)
                            {{ $item->kasBankAkun->nomor_akun . ' - ' . $item->kasBankAkun->nama_akun }}
                        @elseif(isset($item->bank) && is_object($item->bank) && $item->bank)
                            {{ $item->bank->nomor_akun . ' - ' . $item->bank->nama_akun }}
                        @elseif(isset($item->bank) && is_string($item->bank))
                            {{ $item->bank }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge badge-{{ $item->status ?? 'pending' }}">
                            {{ ucfirst($item->status ?? 'pending') }}
                        </span>
                    </td>
                    <td>{{ isset($item->pembuatPembayaran) && $item->pembuatPembayaran ? $item->pembuatPembayaran->name : '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px;">Tidak ada data pembayaran ditemukan</td>
                </tr>
                @endforelse
                @if($allPembayaran->count() > 0)
                <tr class="total-row">
                    <td colspan="4" class="text-center">TOTAL</td>
                    <td class="text-right">Rp {{ number_format($summary['total_pembayaran'], 0, ',', '.') }}</td>
                    <td colspan="3" class="text-center">{{ $summary['total_transaksi'] }} transaksi</td>
                </tr>
                @endif
            </tbody>
        </table>

        <!-- Footer -->
        <div class="footer">
            <p>Dokumen ini digenerate secara otomatis pada {{ now()->format('d F Y H:i:s') }}</p>
            <p>PT. ALEXINDO YAKINPRIMA - Sistem Manajemen Pembayaran</p>
        </div>
    </div>

    <!-- Print Script -->
    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
