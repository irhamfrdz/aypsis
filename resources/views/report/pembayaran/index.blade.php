@extends('layouts.app')

@section('title', 'Report Pembayaran')
@section('page_title', 'Report Pembayaran')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Report Pembayaran OB & DP</h2>
        <div class="flex items-center space-x-3">
            <button onclick="printReport()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-300 text-sm">
                <i class="fas fa-print"></i> Print
            </button>
            <button onclick="exportReport()" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition duration-300 text-sm">
                <i class="fas fa-download"></i> Export CSV
            </button>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-gray-50 p-4 rounded-lg mb-6">
        <form method="GET" action="{{ route('report.pembayaran.index') }}" id="filterForm">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                    <input type="date" name="start_date" id="start_date" value="{{ $startDate }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                    <input type="date" name="end_date" id="end_date" value="{{ $endDate }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
                <div>
                    <label for="jenis_pembayaran" class="block text-sm font-medium text-gray-700 mb-1">Jenis Pembayaran</label>
                    <select name="jenis_pembayaran" id="jenis_pembayaran"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="all" {{ $jenisPembayaran == 'all' ? 'selected' : '' }}>Semua</option>
                        @foreach($paymentModels as $key => $config)
                            <option value="{{ $key }}" {{ $jenisPembayaran == $key ? 'selected' : '' }}>
                                {{ $config['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="status"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="all" {{ $status == 'all' ? 'selected' : '' }}>Semua</option>
                        <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ $status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition duration-300 text-sm">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="{{ route('report.pembayaran.index') }}" class="ml-2 bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded transition duration-300 text-sm">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i class="fas fa-money-bill text-blue-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Total Pembayaran</p>
                    <p class="text-lg font-bold text-gray-900">Rp {{ number_format($summary['total_pembayaran'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <i class="fas fa-list text-green-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Total Transaksi</p>
                    <p class="text-lg font-bold text-gray-900">{{ number_format($summary['total_transaksi']) }}</p>
                </div>
            </div>
        </div>

        @if($jenisPembayaran == 'all')
            @php
                $colors = ['purple', 'orange', 'indigo', 'pink', 'cyan', 'amber', 'emerald'];
                $icons = ['fas fa-truck', 'fas fa-hand-holding-usd', 'fas fa-users', 'fas fa-box', 'fas fa-paint-brush', 'fas fa-wrench', 'fas fa-cogs'];
            @endphp
            @foreach(array_slice($summary['breakdown'], 0, 2) as $key => $breakdown)
                @php
                    $colorIndex = array_search($key, array_keys($summary['breakdown'])) % count($colors);
                    $color = $colors[$colorIndex];
                    $icon = $icons[$colorIndex] ?? 'fas fa-file';
                @endphp
                <div class="bg-{{ $color }}-50 border border-{{ $color }}-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-{{ $color }}-100 rounded-lg">
                            <i class="{{ $icon }} text-{{ $color }}-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-500">{{ $breakdown['label'] }}</p>
                            <p class="text-lg font-bold text-gray-900">Rp {{ number_format($breakdown['total'], 0, ',', '.') }}</p>
                            <p class="text-xs text-gray-500">{{ $breakdown['count'] }} transaksi</p>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            @php
                $currentBreakdown = $summary['breakdown'][$jenisPembayaran] ?? null;
            @endphp
            @if($currentBreakdown)
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <i class="fas fa-chart-bar text-purple-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-500">{{ $currentBreakdown['label'] }}</p>
                            <p class="text-lg font-bold text-gray-900">Rp {{ number_format($currentBreakdown['total'], 0, ',', '.') }}</p>
                            <p class="text-xs text-gray-500">{{ $currentBreakdown['count'] }} transaksi</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-orange-100 rounded-lg">
                        <i class="fas fa-percentage text-orange-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Rata-rata per Transaksi</p>
                        <p class="text-lg font-bold text-gray-900">
                            Rp {{ $summary['total_transaksi'] > 0 ? number_format($summary['total_pembayaran'] / $summary['total_transaksi'], 0, ',', '.') : '0' }}
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Data Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Pembayaran</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Akun Kas/Bank</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat Oleh</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-sm">
                @forelse ($allPembayaran as $index => $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ $index + 1 }}</td>
                        <td class="px-4 py-3">
                            @if(isset($item->tanggal_pembayaran))
                                {{ \Carbon\Carbon::parse($item->tanggal_pembayaran)->format('d/m/Y') }}
                            @elseif(isset($item->tanggal_kas))
                                {{ \Carbon\Carbon::parse($item->tanggal_kas)->format('d/m/Y') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-4 py-3 font-mono text-xs">{{ $item->nomor_pembayaran }}</td>
                        <td class="px-4 py-3">
                            @php
                                $className = get_class($item);
                                $badges = [
                                    'PembayaranOb' => ['label' => 'Pembayaran OB', 'color' => 'purple'],
                                    'PembayaranDpOb' => ['label' => 'DP OB', 'color' => 'orange'],
                                    'PembayaranPranotaSupir' => ['label' => 'Pranota Supir', 'color' => 'indigo'],
                                    'PembayaranPranotaKontainer' => ['label' => 'Pranota Kontainer', 'color' => 'pink'],
                                    'PembayaranPranotaCat' => ['label' => 'Pranota CAT', 'color' => 'cyan'],
                                    'PembayaranPranotaPerbaikanKontainer' => ['label' => 'Pranota Perbaikan', 'color' => 'amber'],
                                    'PembayaranAktivitasLainnya' => ['label' => 'Aktivitas Lainnya', 'color' => 'emerald']
                                ];

                                $currentBadge = null;
                                foreach ($badges as $class => $badge) {
                                    if (strpos($className, $class) !== false) {
                                        $currentBadge = $badge;
                                        break;
                                    }
                                }
                                $currentBadge = $currentBadge ?? ['label' => 'Unknown', 'color' => 'gray'];
                            @endphp
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-{{ $currentBadge['color'] }}-100 text-{{ $currentBadge['color'] }}-800">
                                {{ $currentBadge['label'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-mono">
                            Rp {{ number_format(($item->total_pembayaran ?? $item->nominal_pembayaran ?? 0), 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-xs">
                            @if(isset($item->kasBankAkun) && $item->kasBankAkun)
                                {{ $item->kasBankAkun->nomor_akun . ' - ' . $item->kasBankAkun->nama_akun }}
                            @elseif(isset($item->bank) && is_object($item->bank) && $item->bank)
                                {{ $item->bank->nomor_akun . ' - ' . $item->bank->nama_akun }}
                            @elseif(isset($item->bank) && is_string($item->bank))
                                {{ $item->bank }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @php $statusClass = match($item->status ?? 'pending') {
                                'approved' => 'bg-green-100 text-green-800',
                                'rejected' => 'bg-red-100 text-red-800',
                                default => 'bg-yellow-100 text-yellow-800'
                            }; @endphp
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                                {{ ucfirst($item->status ?? 'pending') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs">
                            {{ isset($item->pembuatPembayaran) && $item->pembuatPembayaran ? $item->pembuatPembayaran->name : '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                            Tidak ada data pembayaran yang ditemukan untuk periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($allPembayaran->count() > 0)
    <div class="mt-6 border-t pt-4">
        <div class="flex justify-between items-center">
            <div class="text-sm text-gray-600">
                Menampilkan {{ $allPembayaran->count() }} transaksi
            </div>
            <div class="text-sm font-semibold text-gray-900">
                Total: Rp {{ number_format($summary['total_pembayaran'], 0, ',', '.') }}
            </div>
        </div>
    </div>
    @endif
</div>

<script>
function printReport() {
    const params = new URLSearchParams({
        start_date: document.getElementById('start_date').value,
        end_date: document.getElementById('end_date').value,
        jenis_pembayaran: document.getElementById('jenis_pembayaran').value,
        status: document.getElementById('status').value
    });

    window.open(`{{ route('report.pembayaran.print') }}?${params.toString()}`, '_blank');
}

function exportReport() {
    const params = new URLSearchParams({
        start_date: document.getElementById('start_date').value,
        end_date: document.getElementById('end_date').value,
        jenis_pembayaran: document.getElementById('jenis_pembayaran').value,
        status: document.getElementById('status').value
    });

    window.location.href = `{{ route('report.pembayaran.export') }}?${params.toString()}`;
}
</script>
@endsection
