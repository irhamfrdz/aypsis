@extends('layouts.app')

@section('title', 'Report History Cuti Karyawan')
@section('page_title', 'Report History Cuti Karyawan')

@section('content')
<div class="bg-white shadow-lg rounded-lg p-6 max-w-6xl mx-auto printable-area">
    <div class="mb-6 flex justify-between items-center no-print">
        <div>
            <h3 class="text-xl font-bold text-gray-800">History Cuti: {{ $karyawan->nama_lengkap }}</h3>
            <p class="text-sm text-gray-600">NIK: {{ $karyawan->nik ?? '-' }} | Posisi: {{ $karyawan->posisi ?? '-' }}</p>
        </div>
        <div>
            <a href="{{ route('report-history-cuti.select-karyawan') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Kembali</a>
            <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 ml-2">Cetak / PDF</button>
        </div>
    </div>
    
    <div class="hidden print-header mb-6">
        <h2 class="text-2xl font-bold text-center">REPORT HISTORY CUTI</h2>
        <p class="text-center mt-2"><strong>Nama Karyawan:</strong> {{ $karyawan->nama_lengkap }} ({{ $karyawan->nik ?? '-' }})</p>
        <p class="text-center"><strong>Posisi:</strong> {{ $karyawan->posisi ?? '-' }}</p>
        @if(request('start_date') && request('end_date'))
            <p class="text-center mt-1">Periode: {{ \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') }} - {{ \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') }}</p>
        @endif
        <hr class="mt-4 border-gray-400">
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 border">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border">No</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border">Tanggal Mulai</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border">Tanggal Selesai</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border">Jenis Cuti</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border">Keterangan</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($cutis as $index => $cuti)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border">{{ $index + 1 }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border">{{ $cuti->tanggal_mulai ? $cuti->tanggal_mulai->format('d/m/Y') : '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border">{{ $cuti->tanggal_selesai ? $cuti->tanggal_selesai->format('d/m/Y') : '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border">{{ $cuti->jenis_cuti ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-900 border">{{ $cuti->keterangan ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border">
                        @if($cuti->status == 'Approved' || $cuti->status == 'Disetujui')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 print-status">{{ $cuti->status }}</span>
                        @elseif($cuti->status == 'Pending' || $cuti->status == 'Menunggu')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 print-status">{{ $cuti->status }}</span>
                        @elseif($cuti->status == 'Rejected' || $cuti->status == 'Ditolak')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 print-status">{{ $cuti->status }}</span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 print-status">{{ $cuti->status ?? 'Unknown' }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center border">Belum ada history cuti untuk karyawan ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
.print-header {
    display: none;
}
@media print {
    body * {
        visibility: hidden;
    }
    .printable-area, .printable-area * {
        visibility: visible;
    }
    .printable-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        box-shadow: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .no-print {
        display: none !important;
    }
    .print-header {
        display: block !important;
    }
    .print-status {
        background-color: transparent !important;
        color: #000 !important;
        border: 1px solid #000;
        padding: 2px 5px;
    }
    table th, table td {
        border-color: #000 !important;
    }
}
</style>
@endsection
