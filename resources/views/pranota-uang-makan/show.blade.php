@extends('layouts.app')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <!-- Page header -->
    <div class="sm:flex sm:items-center sm:justify-between mb-8">
        <div class="mb-4 sm:mb-0 flex items-center gap-4">
            <a href="{{ route('pranota-uang-makan.index') }}" class="p-2 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Detail Pranota Uang Makan</h1>
        </div>
        <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
            <!-- Print Button -->
            <button class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-600" onclick="window.print()">
                <svg class="w-4 h-4 fill-current text-gray-400 shrink-0 mr-2" viewBox="0 0 16 16">
                    <path d="M12.9 6h-9.8c-.5 0-.9-.4-.9-.9v-3.2c0-.5.4-.9.9-.9h9.8c.5 0 .9.4.9.9v3.2c0 .5-.4.9-.9.9zM3.8 2.8v2.4h8.4v-2.4h-8.4z" />
                    <path d="M16 8.5v5.5c0 .6-.4 1-1 1h-14c-.6 0-1-.4-1-1v-5.5c0-.6.4-1 1-1h14c.6 0 1 .4 1 1zM2 13h12v-3.5h-12v3.5z" />
                </svg>
                <span>Cetak Pranota</span>
            </button>
            <form action="{{ route('pranota-uang-makan.destroy', $pranota->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pranota ini?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn bg-red-500 hover:bg-red-600 text-white">
                    <svg class="w-4 h-4 fill-current opacity-50 shrink-0 mr-2" viewBox="0 0 16 16">
                        <path d="M5 7h2v6H5V7zm4 0h2v6H9V7zm3-6v2h4v2h-1v10c0 .6-.4 1-1 1H2c-.6 0-1-.4-1-1V5H0V3h4V1c0-.6.4-1 1-1h6c.6 0 1 .4 1 1zM6 2v1h4V2H6zm7 3H3v9h10V5z" />
                    </svg>
                    <span>Hapus</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Details -->
    <div class="bg-white p-6 shadow-lg rounded-sm border border-gray-200 mb-8">
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <div class="text-sm text-gray-500">Nomor Pranota</div>
                <div class="text-lg font-bold text-gray-800">{{ $pranota->nomor_pranota }}</div>
            </div>
            <div class="text-right">
                <div class="text-sm text-gray-500">Tanggal</div>
                <div class="text-lg font-bold text-gray-800">{{ $pranota->tanggal_pranota->format('d/m/Y') }}</div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="table-auto w-full border-collapse">
                <thead class="text-xs font-semibold uppercase text-gray-500 bg-gray-50 border-y border-gray-200">
                    <tr>
                        <th class="px-4 py-3 whitespace-nowrap text-left">Karyawan</th>
                        <th class="px-4 py-3 whitespace-nowrap text-center">Kehadiran</th>
                        <th class="px-4 py-3 whitespace-nowrap text-right">Nominal Awal</th>
                        <th class="px-4 py-3 whitespace-nowrap text-right">Adjustment</th>
                        <th class="px-4 py-3 whitespace-nowrap text-left">Catatan</th>
                        <th class="px-4 py-3 whitespace-nowrap text-right">Total Akhir</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    @foreach($pranota->details as $detail)
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="font-medium text-gray-800">{{ $detail->karyawan->nama_lengkap ?? '-' }}</div>
                                <div class="text-[10px] font-mono text-gray-500">{{ $detail->karyawan->nik ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                <div class="font-medium text-gray-600">{{ $detail->kehadiran }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right">
                                <div class="text-gray-600">Rp {{ number_format($detail->nominal_awal, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right">
                                <div class="text-gray-600">Rp {{ number_format($detail->adjustment, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-left">
                                <div class="text-gray-600">{{ $detail->catatan ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right">
                                <div class="font-medium text-blue-600">Rp {{ number_format($detail->total_akhir, 0, ',', '.') }}</div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-gray-200 bg-gray-50">
                        <td colspan="5" class="px-4 py-3 text-right font-bold text-gray-700">Total Keseluruhan</td>
                        <td class="px-4 py-3 text-right font-bold text-blue-600 text-lg">Rp {{ number_format($pranota->total_nominal, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('print') && urlParams.get('print') === 'true') {
            // Beri sedikit delay agar halaman render sepenuhnya sebelum print dialog muncul
            setTimeout(() => {
                window.print();
            }, 500);
        }
    });
</script>
@endpush
@endsection
