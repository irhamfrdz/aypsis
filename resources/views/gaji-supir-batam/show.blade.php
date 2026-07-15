@extends('layouts.app')

@section('title', 'Slip Gaji Supir Batam')
@section('page_title', 'Detail Gaji')

@section('content')
<div class="mb-6 flex justify-between items-center max-w-3xl">
    <a href="{{ route('gaji-supir-batam.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900 flex items-center transition-colors">
        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar
    </a>
    
    <a href="{{ route('gaji-supir-batam.print', $gaji->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
        <i class="fas fa-print mr-2"></i> Cetak Slip Gaji
    </a>
</div>

<!-- Print Styles -->
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #slip-gaji, #slip-gaji * {
            visibility: visible;
        }
        #slip-gaji {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            border: none !important;
            box-shadow: none !important;
        }
        .no-print {
            display: none !important;
        }
    }
</style>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden max-w-3xl" id="slip-gaji">
    <!-- Header Slip -->
    <div class="px-8 py-6 bg-gray-50 border-b border-gray-200 text-center relative">
        <h2 class="text-xl font-bold text-gray-800 tracking-wider">PT. ALEXINDO YAKINPRIMA</h2>
        <p class="text-xs text-gray-500">Shipping and Logistics Management Services</p>
        <div class="border-b-2 border-gray-800 my-3"></div>
        <h3 class="text-lg font-bold text-gray-900">SLIP GAJI SUPIR - BATAM</h3>
        <p class="text-sm text-indigo-600 font-bold uppercase mt-1">
            Periode: {{ $gaji->periode_text }}
        </p>
    </div>

    <!-- Data Karyawan -->
    <div class="px-8 py-5 grid grid-cols-2 gap-4 bg-gray-50/50 border-b border-gray-200 text-sm">
        <div>
            <table class="w-full">
                <tr>
                    <td class="py-1 text-gray-500 w-24">Nama Supir</td>
                    <td class="py-1 font-bold text-gray-900">: {{ $gaji->karyawan->nama_lengkap }}</td>
                </tr>
                <tr>
                    <td class="py-1 text-gray-500">NIK</td>
                    <td class="py-1 text-gray-900">: {{ $gaji->karyawan->nik ?? '-' }}</td>
                </tr>
            </table>
        </div>
        <div>
            <table class="w-full">
                <tr>
                    <td class="py-1 text-gray-500 w-24">No. Plat</td>
                    <td class="py-1 font-semibold text-gray-900">: {{ $gaji->karyawan->plat ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="py-1 text-gray-500">Status Bayar</td>
                    <td class="py-1 font-semibold text-gray-900">
                        : 
                        @if($gaji->status_pembayaran === 'PAID')
                            <span class="text-green-700 font-bold">SUDAH DIBAYAR</span>
                        @elseif($gaji->status_pembayaran === 'PENDING')
                            <span class="text-yellow-700 font-bold">PENDING</span>
                        @else
                            <span class="text-red-700 font-bold">BATAL</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Rincian Slip -->
    <div class="p-8">
        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 pb-1 border-b border-gray-200">
            RINCIAN GAJI
        </h4>
        <table class="w-full text-sm">
            <tr class="hover:bg-gray-50 text-indigo-800">
                <td class="py-2.5">Total Gaji Pokok (Berdasarkan Surat Jalan)</td>
                <td class="py-2.5 text-right font-semibold">Rp {{ number_format($gaji->gaji_pokok, 0, ',', '.') }}</td>
            </tr>
            @if($gaji->uang_malam_libur > 0)
            <tr class="hover:bg-gray-50 text-indigo-800">
                <td class="py-2.5">Uang Berangkat Malam/Libur</td>
                <td class="py-2.5 text-right font-semibold">Rp {{ number_format($gaji->uang_malam_libur, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr class="hover:bg-gray-50 text-red-600">
                <td class="py-2.5">Potongan Biaya Bensin</td>
                <td class="py-2.5 text-right font-semibold">Rp {{ number_format($gaji->biaya_bensin ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr class="border-t border-gray-200 font-semibold bg-green-50/30">
                <td class="py-3 text-green-800">Total Gaji Bersih</td>
                <td class="py-3 text-right text-green-800 font-bold text-lg">
                    Rp {{ number_format($gaji->total_gaji, 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>

    <!-- Total Salary -->
    <div class="mx-8 p-5 bg-gray-800 text-white rounded-lg flex justify-between items-center border border-gray-700 mb-6">
        <div>
            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">GAJI BERSIH</h4>
            <p class="text-[10px] text-gray-500">Diterima oleh supir yang bersangkutan</p>
        </div>
        <div class="text-right">
            <span class="text-2xl font-extrabold text-green-400">Rp {{ number_format($gaji->total_gaji, 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- Waybills List / Breakdown -->
    <div class="mx-8 mb-6">
        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 pb-1 border-b border-gray-200">
            RINCIAN SURAT JALAN YANG DIMASUKKAN
        </h4>
        <div class="overflow-hidden border border-gray-200 rounded-lg">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2.5 text-left font-semibold text-gray-600">Tipe</th>
                        <th class="px-4 py-2.5 text-left font-semibold text-gray-600">No. Surat Jalan</th><th class="px-4 py-2.5 text-left font-semibold text-gray-600">No. Kontainer</th><th class="px-4 py-2.5 text-left font-semibold text-gray-600">Tujuan Pengiriman</th><th class="px-4 py-2.5 text-center font-semibold text-gray-600">Ring</th>
                        <th class="px-4 py-2.5 text-left font-semibold text-gray-600">Tanggal</th>
                        <th class="px-4 py-2.5 text-right font-semibold text-gray-600">Uang Jalan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($waybills as $wb)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-2 text-gray-800 font-medium">{{ $wb['type'] }}</td>
                            <td class="px-4 py-2 text-gray-600 font-mono">{{ $wb['no_surat_jalan'] }}</td><td class="px-4 py-2 text-gray-600 font-mono">{{ $wb['no_kontainer'] }}</td><td class="px-4 py-2 text-gray-600">{{ $wb['tujuan'] }}</td><td class="px-4 py-2 text-center text-gray-600">{{ $wb['ring'] }}</td>
                            <td class="px-4 py-2 text-gray-600">{{ $wb['tanggal'] }}</td>
                            <td class="px-4 py-2 text-right font-semibold text-gray-800">Rp {{ number_format($wb['rit'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-4 text-center text-gray-500">Tidak ada surat jalan yang ditemukan pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Keterangan -->
    @if($gaji->keterangan)
        <div class="mx-8 p-4 bg-gray-50 rounded-lg border border-gray-100 text-sm text-gray-600 mb-6">
            <span class="font-bold text-xs uppercase block text-gray-400 mb-1">Catatan:</span>
            {{ $gaji->keterangan }}
        </div>
    @endif

    <!-- Signature Section -->
    <div class="px-8 py-10 grid grid-cols-2 gap-8 text-center text-sm border-t border-gray-200 bg-gray-50/30">
        <div>
            <p class="text-gray-500 mb-16">Penerima (Supir),</p>
            <p class="font-bold text-gray-900 underline">{{ $gaji->karyawan->nama_lengkap }}</p>
        </div>
        <div>
            <p class="text-gray-500 mb-16">Batam, {{ $gaji->tanggal_dibayar ? $gaji->tanggal_dibayar->format('d F Y') : now()->format('d F Y') }}</p>
            <p class="font-bold text-gray-900">Menejemen Operational</p>
        </div>
    </div>
</div>
@endsection
