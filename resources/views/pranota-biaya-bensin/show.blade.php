@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-file-invoice-dollar mr-2 text-indigo-600"></i>
                Detail Pranota: {{ $pranota->nomor_pranota }}
            </h1>
            <p class="text-gray-600 mt-1">Dibuat pada {{ $pranota->created_at->format('d/m/Y H:i') }} oleh {{ $pranota->creator->name ?? '-' }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('pranota-biaya-bensin.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
            <a href="{{ route('pranota-biaya-bensin.print', $pranota->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow-sm transition-colors duration-200">
                <i class="fas fa-print mr-2"></i> Cetak Pranota
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-center shadow-sm">
            <i class="fas fa-check-circle mr-3 text-green-500"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="lg:col-span-1 space-y-6">
            <!-- Pranota Info Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Informasi Pranota</h2>
                
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-500">Nomor Pranota</p>
                        <p class="font-bold text-gray-900">{{ $pranota->nomor_pranota }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500">Tanggal Pranota</p>
                        <p class="font-bold text-gray-900">{{ $pranota->tanggal_pranota->format('d/m/Y') }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Status</p>
                        <span class="mt-1 inline-flex px-2.5 py-1 text-xs font-bold rounded-full {{ $pranota->status_badge }}">
                            {{ $pranota->status_label }}
                        </span>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Total Biaya</p>
                        <p class="font-bold text-amber-700 text-xl">{{ $pranota->formatted_total_biaya }}</p>
                    </div>

                    @if($pranota->catatan)
                    <div>
                        <p class="text-sm text-gray-500">Catatan</p>
                        <p class="text-sm text-gray-800 bg-gray-50 p-3 rounded mt-1">{{ $pranota->catatan }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="lg:col-span-2">
            <!-- Biaya Bensin Items Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-800">Rincian Biaya Bensin ({{ $pranota->biayaBensins->count() }} Data)</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tgl Bensin</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kendaraan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supir</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Liter</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Biaya</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($pranota->biayaBensins as $index => $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $item->tanggal->format('d/m/Y') }}
                                        <div class="text-xs text-blue-600">
                                            <a href="{{ route('biaya-bensin.show', $item->id) }}" target="_blank">Lihat Detail</a>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($item->mobil_id)
                                            {{ $item->mobil->nomor_polisi ?? '-' }}
                                        @elseif($item->alat_berat_id)
                                            {{ $item->alatBerat->nama ?? '-' }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $item->supir ? ($item->supir->nama_panggilan ?: $item->supir->nama_lengkap) : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                        {{ number_format($item->liter, 2, ',', '.') }} L
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium text-right">
                                        Rp {{ number_format($item->biaya, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-right text-sm font-bold text-gray-900 uppercase">Total</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-amber-700 text-lg">
                                    {{ $pranota->formatted_total_biaya }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
