<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Valuasi Biaya Kapal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body class="bg-gray-100 p-8 font-sans">
    <div class="max-w-7xl mx-auto bg-white shadow-lg rounded-lg p-8">
        <div class="flex justify-between items-center border-b pb-6 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Valuasi Biaya Kapal</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Periode: {{ \Carbon\Carbon::parse($request->tanggal_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($request->tanggal_akhir)->format('d/m/Y') }}
                </p>
            </div>
            <div class="text-right">
                <button onclick="window.print()" class="no-print bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded shadow">
                    <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak Document
                </button>
            </div>
        </div>

        <div class="mb-6 grid grid-cols-2 gap-4 text-sm text-gray-600">
            <div>
                <span class="font-semibold text-gray-800">Kapal Filter:</span> 
                {{ $request->kapal ? $request->kapal : 'Semua Kapal' }}
            </div>
            <div>
                <span class="font-semibold text-gray-800">Jenis Biaya Filter:</span> 
                {{ $request->jenis_biaya ? $request->jenis_biaya : 'Semua Jenis Biaya' }}
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 border">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider border">No</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider border">Tanggal</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider border">Invoice/Ref</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider border">Kapal & Voyage</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider border">Jenis Biaya</th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider border">Nominal</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider border">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php 
                        $totalNominal = 0; 
                    @endphp
                    @forelse($biayaKapals as $index => $biaya)
                        @php
                            $totalNominal += $biaya->nominal;
                        @endphp
                        <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                            @php
                                $displayKapal = $biaya->display_nama_kapal;
                                $displayVoyage = $biaya->display_no_voyage;
                                $isJoint = false;

                                if ($request->kapal && is_array($biaya->nama_kapal)) {
                                    $kplIdx = array_search($request->kapal, $biaya->nama_kapal);
                                    if ($kplIdx !== false) {
                                        $displayKapal = $biaya->nama_kapal[$kplIdx];
                                        $displayVoyage = is_array($biaya->no_voyage) && isset($biaya->no_voyage[$kplIdx]) ? $biaya->no_voyage[$kplIdx] : '-';
                                        if (count($biaya->nama_kapal) > 1) {
                                            $isJoint = true;
                                        }
                                    }
                                }
                            @endphp
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 border">{{ $index + 1 }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 border">{{ $biaya->tanggal ? $biaya->tanggal->format('d/m/Y') : '-' }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900 border">
                                <div class="font-medium text-purple-600">{{ $biaya->nomor_invoice ?? '-' }}</div>
                                <div class="text-gray-500 text-xs">{{ $biaya->nomor_referensi ?? '' }}</div>
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-900 border">
                                <div class="font-medium">{{ $displayKapal }}
                                    @if($isJoint)
                                        <span class="text-[10px] bg-blue-100 text-blue-700 px-1 py-0.5 rounded ml-1">Gabungan</span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500">Voy: {{ $displayVoyage }}</div>
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-900 border">
                                {{ $biaya->klasifikasiBiaya ? $biaya->klasifikasiBiaya->nama : ($biaya->jenis_biaya ?? '-') }}
                                @if($biaya->vendor)
                                    <div class="text-xs text-gray-500">Vendor: {{ $biaya->vendor->nama }}</div>
                                @elseif($biaya->nama_vendor)
                                    <div class="text-xs text-gray-500">Vendor: {{ $biaya->nama_vendor }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-right font-medium text-gray-900 border">
                                Rp {{ number_format($biaya->nominal, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-center border">
                                @if($biaya->status_pembayaran === 'paid')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Lunas</span>
                                @elseif($biaya->status_pembayaran === 'cancelled')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Dibatalkan</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 text-center border">
                                Tidak ada data yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-100 font-bold">
                    <tr>
                        <td colspan="5" class="px-4 py-3 text-right text-sm text-gray-900 border">Total Keseluruhan</td>
                        <td class="px-4 py-3 text-right text-sm text-purple-700 border">Rp {{ number_format($totalNominal, 0, ',', '.') }}</td>
                        <td class="border"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <div class="mt-8 text-right text-xs text-gray-500 no-print">
            Dicetak pada: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}
        </div>
    </div>

    <!-- Auto print trigger -->
    <script>
        window.onload = function() {
            // Uncomment line below to auto-print on load
            // window.print();
        }
    </script>
</body>
</html>
