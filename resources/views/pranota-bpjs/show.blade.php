@extends('layouts.app')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <div class="sm:flex sm:items-center sm:justify-between mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Detail Pranota BPJS</h1>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('pranota-bpjs.index') }}" class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-600">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
            <button onclick="window.print()" class="btn bg-indigo-500 hover:bg-indigo-600 text-white">
                <i class="fas fa-print mr-2"></i> Cetak
            </button>
        </div>
    </div>

    <!-- Print Area -->
    <div id="print-area" class="bg-white shadow-lg rounded-sm border border-gray-200 mb-8 p-6 print:shadow-none print:border-none print:m-0 print:p-0">
        
        <!-- Header Print -->
        <div class="text-center mb-8 border-b-2 border-gray-800 pb-4">
            <h2 class="text-2xl font-bold uppercase tracking-wider">PRANOTA BPJS</h2>
            <p class="text-gray-600 mt-1">Nomor: <span class="font-bold text-gray-800">{{ $pranota_bpj->nomor_pranota }}</span></p>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-8">
            <div>
                <table class="w-full text-sm">
                    <tr>
                        <td class="py-1 w-1/3 text-gray-600 font-medium">Tanggal Pranota</td>
                        <td class="py-1 w-4 text-center">:</td>
                        <td class="py-1 font-semibold text-gray-800">{{ $pranota_bpj->tanggal_pranota->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 text-gray-600 font-medium">Periode</td>
                        <td class="py-1 text-center">:</td>
                        <td class="py-1 font-semibold text-gray-800">{{ date('F', mktime(0, 0, 0, $pranota_bpj->periode_bulan, 1)) }} {{ $pranota_bpj->periode_tahun }}</td>
                    </tr>
                </table>
            </div>
            <div>
                <table class="w-full text-sm">
                    <tr>
                        <td class="py-1 w-1/3 text-gray-600 font-medium">Dibuat Oleh</td>
                        <td class="py-1 w-4 text-center">:</td>
                        <td class="py-1 font-semibold text-gray-800">{{ $pranota_bpj->createdBy->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 text-gray-600 font-medium">Status</td>
                        <td class="py-1 text-center">:</td>
                        <td class="py-1 font-semibold text-gray-800 uppercase">{{ $pranota_bpj->status }}</td>
                    </tr>
                </table>
            </div>
        </div>

        @if($pranota_bpj->keterangan)
        <div class="mb-8">
            <h3 class="text-sm font-bold text-gray-800 mb-2">Keterangan:</h3>
            <div class="p-3 bg-gray-50 border border-gray-200 rounded text-sm text-gray-700">
                {{ $pranota_bpj->keterangan }}
            </div>
        </div>
        @endif

        <div class="mb-8">
            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Rincian per Karyawan</h3>
            <table class="w-full text-sm border-collapse">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border border-gray-300 px-3 py-2 text-center w-12">No</th>
                        <th class="border border-gray-300 px-3 py-2 text-left">Nama Karyawan</th>
                        <th class="border border-gray-300 px-3 py-2 text-right">JKN</th>
                        <th class="border border-gray-300 px-3 py-2 text-right">BP Jamsostek</th>
                        <th class="border border-gray-300 px-3 py-2 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pranota_bpj->details as $index => $detail)
                        <tr>
                            <td class="border border-gray-300 px-3 py-2 text-center">{{ $index + 1 }}</td>
                            <td class="border border-gray-300 px-3 py-2">{{ $detail->karyawan->nama_lengkap ?? '-' }}</td>
                            <td class="border border-gray-300 px-3 py-2 text-right">Rp {{ number_format($detail->bpjs_kesehatan + $detail->bpjs_ketenagakerjaan, 2, ',', '.') }}</td>
                            <td class="border border-gray-300 px-3 py-2 text-right">Rp {{ number_format($detail->jht_biaya + $detail->jht_hutang + $detail->jkk_tunjangan + $detail->jkm_tunjangan + $detail->jp_biaya + $detail->jp_hutang, 2, ',', '.') }}</td>
                            <td class="border border-gray-300 px-3 py-2 text-right font-medium">Rp {{ number_format($detail->total, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 font-bold">
                    <tr>
                        <td colspan="2" class="border border-gray-300 px-3 py-3 text-right uppercase">Total ({{ $pranota_bpj->total_karyawan }} Karyawan):</td>
                        <td class="border border-gray-300 px-3 py-3 text-right">Rp {{ number_format($pranota_bpj->total_bpjs_kesehatan, 2, ',', '.') }}</td>
                        <td class="border border-gray-300 px-3 py-3 text-right">Rp {{ number_format($pranota_bpj->total_bpjs_ketenagakerjaan, 2, ',', '.') }}</td>
                        <td class="border border-gray-300 px-3 py-3 text-right text-indigo-700">Rp {{ number_format($pranota_bpj->grand_total, 2, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <div class="flex justify-end mt-16 print:mt-10">
            <div class="text-center w-48">
                <p class="mb-16 text-sm text-gray-600">Dibuat Oleh,</p>
                <p class="font-bold border-b border-gray-400 pb-1">{{ $pranota_bpj->createdBy->name ?? '____________________' }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $pranota_bpj->created_at->format('d M Y H:i') }}</p>
            </div>
        </div>

    </div>
</div>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #print-area, #print-area * {
        visibility: visible;
    }
    #print-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .print\:shadow-none { box-shadow: none !important; }
    .print\:border-none { border: none !important; }
    .print\:m-0 { margin: 0 !important; }
    .print\:p-0 { padding: 0 !important; }
}
</style>
@endsection
