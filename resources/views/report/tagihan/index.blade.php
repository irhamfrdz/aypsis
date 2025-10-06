@extends('layouts.app')

@section('title', 'Report Tagihan')
@section('page_title', 'Report Tagihan')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Debug Info (remove after testing) -->
    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    <strong>Data ditemukan:</strong>
                    Sewa: {{ $tagihanSewa->count() }} |
                    CAT: {{ $tagihanCat->count() }} |
                    Perbaikan: {{ $tagihanPerbaikan->count() }} |
                    Total: {{ $totalTagihan }}
                </p>
                <p class="text-xs text-blue-600 mt-1">
                    Periode: {{ $startDate }} s/d {{ $endDate }}
                </p>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Filter Report</h2>
        <form method="GET" action="{{ route('report.tagihan.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Tanggal Mulai -->
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                <input type="date" name="start_date" id="start_date" value="{{ $startDate }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>

            <!-- Tanggal Akhir -->
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                <input type="date" name="end_date" id="end_date" value="{{ $endDate }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>

            <!-- Jenis Tagihan -->
            <div>
                <label for="jenis_tagihan" class="block text-sm font-medium text-gray-700 mb-2">Jenis Tagihan</label>
                <select name="jenis_tagihan" id="jenis_tagihan"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="all" {{ $jenisTagihan === 'all' ? 'selected' : '' }}>Semua</option>
                    <option value="sewa" {{ $jenisTagihan === 'sewa' ? 'selected' : '' }}>Sewa Kontainer</option>
                    <option value="cat" {{ $jenisTagihan === 'cat' ? 'selected' : '' }}>CAT Kontainer</option>
                    <option value="perbaikan" {{ $jenisTagihan === 'perbaikan' ? 'selected' : '' }}>Perbaikan Kontainer</option>
                </select>
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" id="status"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua</option>
                    <option value="unpaid" {{ $status === 'unpaid' ? 'selected' : '' }}>Belum Dibayar</option>
                    <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Sudah Dibayar</option>
                    <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Disetujui</option>
                </select>
            </div>

            <!-- Submit Button -->
            <div class="md:col-span-4 flex gap-2">
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition-colors">
                    <i class="fas fa-filter mr-2"></i>Tampilkan Report
                </button>
                <a href="{{ route('report.tagihan.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                    <i class="fas fa-redo mr-2"></i>Reset
                </a>
                <button type="button" onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors ml-auto">
                    <i class="fas fa-print mr-2"></i>Print
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-file-invoice text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Total Tagihan</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ $totalTagihan }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-money-bill-wave text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Total Nilai</p>
                    <p class="text-2xl font-semibold text-gray-800">Rp {{ number_format($totalNilai, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-check-circle text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Sudah Dibayar</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ $totalPaid }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-clock text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Belum Dibayar</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ $totalUnpaid }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tagihan Sewa Kontainer -->
    @if($jenisTagihan === 'all' || $jenisTagihan === 'sewa')
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="bg-purple-50 px-6 py-4 border-b border-purple-200">
            <h3 class="text-lg font-semibold text-purple-800">
                <i class="fas fa-truck mr-2"></i>Tagihan Sewa Kontainer
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Kontainer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vendor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Size</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Grand Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($tagihanSewa as $tagihan)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $tagihan->nomor_kontainer ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($tagihan->tanggal_awal)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($tagihan->tanggal_akhir)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $tagihan->vendor ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $tagihan->size ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-gray-900">
                            Rp {{ number_format($tagihan->grand_total ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($tagihan->status === 'paid')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Lunas</span>
                            @elseif($tagihan->status === 'approved')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Disetujui</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Belum Bayar</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data tagihan sewa</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Tagihan CAT Kontainer -->
    @if($jenisTagihan === 'all' || $jenisTagihan === 'cat')
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="bg-blue-50 px-6 py-4 border-b border-blue-200">
            <h3 class="text-lg font-semibold text-blue-800">
                <i class="fas fa-paint-brush mr-2"></i>Tagihan CAT Kontainer
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kontainer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal CAT</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vendor</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Biaya</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($tagihanCat as $tagihan)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $tagihan->nomor_kontainer ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($tagihan->tanggal_cat)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $tagihan->vendor ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-gray-900">
                            Rp {{ number_format($tagihan->realisasi_biaya ?? $tagihan->estimasi_biaya ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($tagihan->status === 'paid')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Lunas</span>
                            @elseif($tagihan->status === 'approved')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Disetujui</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Belum Bayar</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data tagihan CAT</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Tagihan Perbaikan Kontainer -->
    @if($jenisTagihan === 'all' || $jenisTagihan === 'perbaikan')
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="bg-orange-50 px-6 py-4 border-b border-orange-200">
            <h3 class="text-lg font-semibold text-orange-800">
                <i class="fas fa-tools mr-2"></i>Tagihan Perbaikan Kontainer
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Pranota</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kontainer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teknisi</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Biaya</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($tagihanPerbaikan as $tagihan)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $tagihan->nomor_pranota ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($tagihan->tanggal_pranota)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($tagihan->perbaikanKontainers->isNotEmpty())
                                {{ $tagihan->perbaikanKontainers->first()->nomor_kontainer ?? '-' }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $tagihan->nama_teknisi ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-gray-900">
                            Rp {{ number_format($tagihan->total_biaya, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($tagihan->status === 'sudah_dibayar')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Lunas</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Belum Bayar</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data tagihan perbaikan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    // Print functionality
    window.addEventListener('beforeprint', function() {
        // Hide filter section when printing
        document.querySelector('.bg-white.rounded-lg.shadow-md.p-6.mb-6').style.display = 'none';
    });

    window.addEventListener('afterprint', function() {
        // Show filter section after printing
        document.querySelector('.bg-white.rounded-lg.shadow-md.p-6.mb-6').style.display = 'block';
    });
</script>
@endpush

@endsection
