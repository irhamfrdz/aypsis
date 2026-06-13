@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-check-double mr-2 text-indigo-600"></i>
                Approval Pengisian Bensin
            </h1>
            <p class="text-gray-600 mt-1">Review dan setujui laporan pengisian bensin yang diajukan oleh supir</p>
        </div>
        <a href="{{ route('biaya-bensin.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition-colors shadow-sm">
            <i class="fas fa-list mr-2"></i> Daftar Biaya Bensin
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-center shadow-sm">
            <i class="fas fa-check-circle mr-3 text-green-500"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mobil</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Kartu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supir</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">KM Awal/Akhir</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Liter</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Biaya</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bukti</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($items as $item)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item->tanggal->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="font-medium text-gray-900">{{ $item->mobil->nomor_polisi ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $item->mobil->kode_no ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item->nomor_kartu ?: '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item->supir->nama_panggilan ?: $item->supir->nama_lengkap ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div>Awal: {{ number_format($item->km_awal, 0, ',', '.') }} KM</div>
                                <div class="text-xs">Akhir: {{ number_format($item->km_akhir, 0, ',', '.') }} KM</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($item->liter, 2, ',', '.') }} L
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                Rp {{ number_format($item->biaya, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" title="{{ $item->keterangan }}">
                                {{ $item->keterangan ?: '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($item->bukti_beli)
                                    <a href="{{ asset('storage/' . $item->bukti_beli) }}" target="_blank" 
                                       class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                                        <i class="fas fa-file-invoice mr-1.5 text-blue-500"></i> Lihat Bukti
                                    </a>
                                @else
                                    <span class="text-xs text-gray-400 italic">Tidak ada</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <form action="{{ route('biaya-bensin.approve', $item->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-bold rounded-lg transition-colors shadow-sm">
                                            <i class="fas fa-check mr-1.5"></i> Setujui
                                        </button>
                                    </form>
                                    <form action="{{ route('biaya-bensin.reject', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menolak pengisian bensin ini?');">
                                        @csrf
                                        <button type="submit" 
                                                class="inline-flex items-center px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg transition-colors shadow-sm">
                                            <i class="fas fa-times mr-1.5"></i> Tolak
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-check-circle text-gray-300 text-5xl mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 mb-1">Semua bersih!</h3>
                                    <p class="text-gray-500 text-sm">Tidak ada laporan pengisian bensin pending yang membutuhkan approval saat ini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $items->links() }}
    </div>
</div>
@endsection
