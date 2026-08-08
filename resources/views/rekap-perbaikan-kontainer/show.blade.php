@extends('layouts.app')

@section('title', 'Detail Rekap Perbaikan Kontainer')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Rekap Perbaikan Kontainer: {{ $nomorKontainer }}</h1>
            <p class="text-sm text-gray-500 mt-1">Menampilkan riwayat perbaikan untuk kontainer terpilih.</p>
        </div>
        <a href="{{ route('rekap-perbaikan-kontainer.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-5 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-list text-blue-600 mr-2"></i> Riwayat Perbaikan
            </h2>
        </div>
        
        <div class="p-0">
            @if($riwayatPerbaikan->isEmpty())
                <p class="text-gray-500 text-center py-8">
                    <i class="fas fa-info-circle text-4xl mb-4 text-gray-300"></i><br>
                    Belum ada riwayat perbaikan untuk kontainer <strong>{{ $nomorKontainer }}</strong>.
                </p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left clean-table text-sm">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No. Perbaikan</th>
                                <th>Pranota</th>
                                <th>Bengkel</th>
                                <th>Keterangan</th>
                                <th class="text-right">Estimasi</th>
                                <th class="text-right">Biaya Riil</th>
                                <th class="text-right">Biaya Cat</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($riwayatPerbaikan as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="font-medium text-blue-600">{{ $item->no_perbaikan ?? '-' }}</td>
                                <td>
                                    @if(isset($item->nomor_pranota) && $item->nomor_pranota !== 'Belum ada pranota')
                                        <div class="font-medium">{{ $item->nomor_pranota }}</div>
                                        <div class="text-xs text-gray-500">{{ isset($item->tanggal_pranota) ? \Carbon\Carbon::parse($item->tanggal_pranota)->format('d/m/Y') : '-' }}</div>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-800">Belum ada pranota</span>
                                    @endif
                                </td>
                                <td>{{ $item->bengkel ?? '-' }}</td>
                                <td class="max-w-xs truncate" title="{{ $item->keterangan_kerusakan ?? '-' }}">
                                    {{ $item->keterangan_kerusakan ?? '-' }}
                                </td>
                                <td class="text-right whitespace-nowrap">Rp {{ number_format($item->estimasi_biaya ?? 0, 0, ',', '.') }}</td>
                                <td class="text-right whitespace-nowrap">Rp {{ number_format($item->biaya_riil ?? 0, 0, ',', '.') }}</td>
                                <td class="text-right whitespace-nowrap">Rp {{ number_format($item->biaya_cat ?? 0, 0, ',', '.') }}</td>
                                <td>
                                    @if(isset($item->status) && strtolower($item->status) === 'selesai')
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Selesai</span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">{{ ucfirst($item->status ?? 'Proses') }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 font-bold border-t border-gray-200">
                            <tr>
                                <td colspan="5" class="text-right py-3 px-4">TOTAL KESELURUHAN:</td>
                                <td class="text-right py-3 px-4 text-gray-800">Rp {{ number_format($riwayatPerbaikan->sum('estimasi_biaya'), 0, ',', '.') }}</td>
                                <td class="text-right py-3 px-4 text-blue-700">Rp {{ number_format($riwayatPerbaikan->sum('biaya_riil'), 0, ',', '.') }}</td>
                                <td class="text-right py-3 px-4 text-gray-800">Rp {{ number_format($riwayatPerbaikan->sum('biaya_cat'), 0, ',', '.') }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
