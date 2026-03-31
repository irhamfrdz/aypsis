<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Riwayat Stock Amprahan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            @page {
                size: landscape;
                margin: 1cm;
            }
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
            .no-print {
                display: none !important;
            }
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #e5e7eb;
            padding: 8px;
            font-size: 11px;
            text-align: left;
        }
        th {
            background-color: #f9fafb !important;
        }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .bg-green-100 { background-color: #dcfce7 !important; }
        .text-green-800 { color: #166534 !important; }
        .bg-orange-100 { background-color: #ffedd5 !important; }
        .text-orange-800 { color: #9a3412 !important; }
    </style>
</head>
<body class="bg-white p-4">
    <div class="mb-6 text-center">
        <h1 class="text-xl font-bold uppercase tracking-wider">Laporan Riwayat Stock Amprahan</h1>
        @if(isset($item))
            <p class="text-lg font-semibold mt-1">{{ $item->nama_barang ?? ($item->masterNamaBarangAmprahan->nama_barang ?? 'Barang') }}</p>
        @else
            <p class="text-lg font-semibold mt-1">Semua Aktivitas Stock</p>
        @endif
        
        @if(request('from_date') || request('to_date'))
            <p class="text-sm text-gray-600 mt-1">
                Periode: {{ request('from_date') ? date('d/m/Y', strtotime(request('from_date'))) : 'Awal' }} 
                s/d 
                {{ request('to_date') ? date('d/m/Y', strtotime(request('to_date'))) : 'Sekarang' }}
            </p>
        @endif
    </div>

    @if(isset($item))
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="border p-3 rounded">
            <p class="text-xs text-gray-500 uppercase">Sisa Stock</p>
            <p class="text-lg font-bold">{{ number_format($item->jumlah, 0, ',', '.') }} {{ $item->satuan }}</p>
        </div>
        <div class="border p-3 rounded">
            <p class="text-xs text-gray-500 uppercase">Total Masuk</p>
            <p class="text-lg font-bold text-green-600">{{ number_format($history->where('type', 'Masuk')->sum('jumlah'), 0, ',', '.') }} {{ $item->satuan }}</p>
        </div>
        <div class="border p-3 rounded">
            <p class="text-xs text-gray-500 uppercase">Total Keluar</p>
            <p class="text-lg font-bold text-orange-600">{{ number_format($history->where('type', 'Keluar')->sum('jumlah'), 0, ',', '.') }} {{ $item->satuan }}</p>
        </div>
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Tanggal</th>
                <th class="text-center">Tipe</th>
                @if(!isset($item))
                    <th>Nama Barang</th>
                @endif
                <th class="text-center">Jumlah</th>
                <th>Penerima</th>
                <th>Mobil/Kapal/Alat</th>
                <th>KM</th>
                <th>Keterangan</th>
                <th>Dicatat Oleh</th>
            </tr>
        </thead>
        <tbody>
            @forelse($history as $index => $usage)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ date('d/m/Y', strtotime($usage->tanggal_raw)) }}</td>
                <td class="text-center">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $usage->type == 'Masuk' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                        {{ $usage->type }}
                    </span>
                </td>
                @if(!isset($item))
                <td>
                    <div class="font-semibold">{{ $usage->stockAmprahan->nama_barang ?? ($usage->stockAmprahan->masterNamaBarangAmprahan->nama_barang ?? '-') }}</div>
                </td>
                @endif
                <td class="text-center font-bold">
                    {{ $usage->type == 'Masuk' ? '+' : '-' }}{{ number_format($usage->jumlah, 0, ',', '.') }}
                </td>
                <td>{{ $usage->penerima->nama_lengkap ?? '-' }}</td>
                <td>
                    @php $parts = []; @endphp
                    @if($usage->mobil) @php $parts[] = $usage->mobil->nomor_polisi; @endphp @endif
                    @if($usage->buntut) @php $parts[] = ($usage->buntut->no_kir ?? $usage->buntut->nomor_polisi) . ' (Buntut)'; @endphp @endif
                    @if($usage->kapal) @php $parts[] = $usage->kapal->nama_kapal; @endphp @endif
                    @if($usage->alatBerat) @php $parts[] = $usage->alatBerat->kode_alat; @endphp @endif
                    @if($usage->lain_lain) @php $parts[] = $usage->lain_lain; @endphp @endif
                    {{ empty($parts) ? '-' : implode(' / ', $parts) }}
                </td>
                <td>{{ $usage->kilometer ?? '-' }}</td>
                <td>{{ $usage->keterangan }}</td>
                <td>{{ $usage->createdBy->name ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ isset($item) ? 9 : 10 }}" class="text-center py-8">Tidak ada data untuk periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-8 flex justify-between">
        <div class="text-sm">
            <p>Dicetak pada: {{ date('d/m/Y H:i') }}</p>
        </div>
        <div class="text-center min-w-[200px]">
            <p class="mb-16 text-sm italic">Penanggung Jawab,</p>
            <p class="font-bold border-t border-black pt-1">( _________________________ )</p>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
            setTimeout(function() {
                window.close();
            }, 1000);
        }
    </script>
</body>
</html>
