@extends('layouts.app')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto print-container">
    <!-- Header Section -->
    <div class="sm:flex sm:items-center sm:justify-between mb-8 pb-4 border-b border-gray-100 no-print">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-gray-800 font-extrabold tracking-tight">Detail PUML</h1>
            <p class="text-sm text-gray-500 mt-1">Nomor Dokumen: <span class="font-semibold text-indigo-600">{{ $puml->nomor_pranota }}</span></p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('pranota-puml.index') }}" class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
            <button onclick="window.print()" class="inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 hover:-translate-y-0.5">
                <i class="fas fa-print mr-2"></i> Cetak PUML
            </button>
        </div>
    </div>

    <!-- Informasi PUML -->
    <div class="bg-white shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] rounded-xl border border-gray-100 p-6 mb-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 divide-x divide-gray-100">
            <div class="pl-2">
                <div class="flex items-center text-sm font-semibold text-gray-500 mb-2">
                    <i class="fas fa-calendar text-gray-400 mr-2"></i> Tanggal Pranota
                </div>
                <p class="text-lg font-bold text-gray-900">{{ $puml->tanggal_pranota->format('d F Y') }}</p>
            </div>
            <div class="pl-6">
                <div class="flex items-center text-sm font-semibold text-gray-500 mb-2">
                    <i class="fas fa-utensils text-emerald-400 mr-2"></i> Total Uang Makan
                </div>
                <p class="text-lg font-bold text-gray-900">Rp {{ number_format($puml->total_uang_makan, 0, ',', '.') }}</p>
            </div>
            <div class="pl-6">
                <div class="flex items-center text-sm font-semibold text-gray-500 mb-2">
                    <i class="fas fa-clock text-orange-400 mr-2"></i> Total Lembur
                </div>
                <p class="text-lg font-bold text-gray-900">Rp {{ number_format($puml->total_lembur, 0, ',', '.') }}</p>
            </div>
            <div class="pl-6 bg-gradient-to-r from-transparent to-indigo-50/30 rounded-r-xl">
                <div class="flex items-center text-sm font-semibold text-indigo-500 mb-2">
                    <i class="fas fa-wallet text-indigo-400 mr-2"></i> Grand Total
                </div>
                <p class="text-xl font-extrabold text-indigo-600" id="grand-total-header">Rp {{ number_format($puml->grand_total, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <!-- Rekap per Karyawan -->
    <div class="bg-white shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] rounded-xl border border-gray-100 overflow-hidden mb-6">
        <form action="{{ route('pranota-puml.store-potongan', $puml->id) }}" method="POST">
            @csrf
            <header class="px-6 py-4 bg-gray-50/80 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-center gap-4">
                <h2 class="text-lg font-bold text-gray-800 whitespace-nowrap">Rincian Penerimaan Karyawan</h2>
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <div class="relative w-full sm:w-64 no-print">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" id="searchKaryawan" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 p-2 shadow-sm transition-colors duration-200" placeholder="Cari nama atau NIK...">
                    </div>
                    <button type="submit" class="no-print inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 shrink-0">
                        <i class="fas fa-save mr-2"></i> Simpan
                    </button>
                </div>
            </header>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th scope="col" class="px-2 py-3 font-semibold text-center">NIK</th>
                            <th scope="col" class="px-2 py-3 font-semibold text-left">Nama Karyawan</th>
                            <th scope="col" class="px-2 py-3 font-semibold text-right">Uang Makan</th>
                            <th scope="col" class="px-2 py-3 font-semibold text-right">Uang Lembur</th>
                            <th scope="col" class="px-1 py-3 font-semibold text-center no-print">Pot. Utang</th>
                            <th scope="col" class="px-1 py-3 font-semibold text-center no-print">Pot. BPJS</th>
                            <th scope="col" class="px-1 py-3 font-semibold text-center no-print">Pot. PPh</th>
                            <th scope="col" class="px-1 py-3 font-semibold text-center no-print">Pot. Terlambat</th>
                            <th scope="col" class="px-3 py-3 font-bold text-right text-indigo-600">Total Terima</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach($karyawanRekap as $kid => $rekap)
                            @php
                                $totalAwal = $rekap['total_uang_makan'] + $rekap['total_lembur'];
                                $potUtang = $rekap['pot_utang'] ?? 0;
                                $potBpjs = $rekap['pot_bpjs'] ?? 0;
                                $potPph = $rekap['pot_pph'] ?? 0;
                                $potTerlambat = $rekap['pot_terlambat'] ?? 0;
                                $totalAkhir = $totalAwal - ($potUtang + $potBpjs + $potPph + $potTerlambat);
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition-colors duration-200 karyawan-row" data-total-awal="{{ $totalAwal }}" data-total-akhir="{{ $totalAkhir }}">
                                <td class="px-2 py-2 whitespace-nowrap text-center">
                                    <div class="font-semibold text-gray-600 text-xs">{{ $rekap['karyawan']->nik ?? '-' }}</div>
                                </td>
                                <td class="px-2 py-2">
                                    <div class="font-bold text-gray-900 text-xs min-w-[120px]">{{ $rekap['karyawan']->nama_lengkap ?? 'Unknown' }}</div>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap text-right">
                                    <div class="text-gray-600 font-medium text-xs">Rp {{ number_format($rekap['total_uang_makan'], 0, ',', '.') }}</div>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap text-right">
                                    <div class="text-gray-600 font-medium text-xs">Rp {{ number_format($rekap['total_lembur'], 0, ',', '.') }}</div>
                                </td>
                                <td class="px-1 py-2 whitespace-nowrap text-center no-print">
                                    <input type="text" name="potongan[{{ $kid }}][pot_utang]" value="{{ number_format($potUtang, 0, ',', '.') }}" class="potongan-input text-right w-20 px-2 py-1 border border-gray-300 rounded text-xs focus:ring-indigo-500 focus:border-indigo-500">
                                </td>
                                <td class="px-1 py-2 whitespace-nowrap text-center no-print">
                                    <input type="text" name="potongan[{{ $kid }}][pot_bpjs]" value="{{ number_format($potBpjs, 0, ',', '.') }}" class="potongan-input text-right w-20 px-2 py-1 border border-gray-300 rounded text-xs focus:ring-indigo-500 focus:border-indigo-500">
                                </td>
                                <td class="px-1 py-2 whitespace-nowrap text-center no-print">
                                    <input type="text" name="potongan[{{ $kid }}][pot_pph]" value="{{ number_format($potPph, 0, ',', '.') }}" class="potongan-input text-right w-20 px-2 py-1 border border-gray-300 rounded text-xs focus:ring-indigo-500 focus:border-indigo-500">
                                </td>
                                <td class="px-1 py-2 whitespace-nowrap text-center no-print">
                                    <input type="text" name="potongan[{{ $kid }}][pot_terlambat]" value="{{ number_format($potTerlambat, 0, ',', '.') }}" class="potongan-input text-right w-20 px-2 py-1 border border-gray-300 rounded text-xs focus:ring-indigo-500 focus:border-indigo-500">
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-right bg-indigo-50/30">
                                    <div class="font-extrabold text-indigo-700 total-akhir-text text-xs">Rp {{ number_format($totalAkhir, 0, ',', '.') }}</div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('.potongan-input');
        
        inputs.forEach(input => {
            input.addEventListener('input', function(e) {
                // Format rupiah
                let value = this.value.replace(/[^,\d]/g, '').toString();
                let split = value.split(',');
                let sisa = split[0].length % 3;
                let rupiah = split[0].substr(0, sisa);
                let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    let separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }

                rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
                this.value = rupiah;
                
                // Kalkulasi ulang total akhir baris ini
                const row = this.closest('.karyawan-row');
                const totalAwal = parseFloat(row.getAttribute('data-total-awal'));
                
                let totalPotongan = 0;
                row.querySelectorAll('.potongan-input').forEach(inp => {
                    const val = parseFloat(inp.value.replace(/\./g, '')) || 0;
                    totalPotongan += val;
                });
                
                const totalAkhir = totalAwal - totalPotongan;
                
                // Format total akhir ke rupiah
                row.querySelector('.total-akhir-text').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalAkhir);
                row.setAttribute('data-total-akhir', totalAkhir);
                
                // Update Grand Total
                let grandTotal = 0;
                document.querySelectorAll('.karyawan-row').forEach(tr => {
                    grandTotal += parseFloat(tr.getAttribute('data-total-akhir')) || 0;
                });
                
                const grandTotalEl = document.getElementById('grand-total-header');
                if (grandTotalEl) {
                    grandTotalEl.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(grandTotal);
                }
            });
        });

        // Fitur pencarian karyawan
        const searchInput = document.getElementById('searchKaryawan');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const rows = document.querySelectorAll('.karyawan-row');
                
                rows.forEach(row => {
                    // Karena struktur tabel, kolom pertama = NIK, kedua = Nama
                    const nik = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
                    const nama = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                    
                    if (nik.includes(searchTerm) || nama.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }
    });
</script>
@endpush

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .print-container, .print-container * {
            visibility: visible;
        }
        .print-container {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            margin: 0;
            padding: 15px;
        }
        .no-print {
            display: none !important;
        }
        .shadow-\[0_4px_20px_-4px_rgba\(0\,0\,0\,0\.05\)\] {
            box-shadow: none !important;
            border: 1px solid #e5e7eb !important;
        }
        .bg-indigo-50\/30 {
            background-color: transparent !important;
        }
    }
</style>
@endsection
