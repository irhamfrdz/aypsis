@extends('layouts.app')

@section('title', 'Laporan Input Harian Ban')
@section('page_title', 'Laporan Input Harian Ban')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Laporan Input Harian Ban</h1>
            <p class="text-sm text-gray-600">Melihat daftar ban luar dan ban luar batam yang diinput pada tanggal tertentu.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('stock-ban.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white p-4 rounded-lg shadow border border-gray-200 mb-6 w-full max-w-md">
        <form action="{{ route('stock-ban.input-harian') }}" method="GET" class="flex gap-2 items-end">
            <div class="w-full">
                <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Pilih Tanggal Input</label>
                <input type="date" name="date" id="date" value="{{ $date }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">Tampilkan</button>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden mb-8">
        <div class="px-4 py-3 bg-blue-50 border-b border-blue-100 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-blue-800">Ban Luar (Jakarta)</h2>
            <span class="bg-blue-600 text-white text-xs px-2 py-1 rounded-full font-bold">{{ $stockBans->count() }} Data</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No Seri / Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Merk & Ukuran</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kondisi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Masuk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diinput Oleh</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($stockBans as $ban)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $ban->nomor_seri ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div class="font-medium text-gray-800">{{ $ban->merk ?? '-' }}</div>
                            <div class="text-xs">{{ $ban->ukuran ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ ucfirst($ban->kondisi) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $ban->status }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ date('d-m-Y', strtotime($ban->tanggal_masuk)) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $ban->createdBy->name ?? 'System' }} 
                            <div class="text-[10px] text-gray-400 mt-1">{{ $ban->created_at->format('H:i:s') }}</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">Tidak ada input harian ban luar pada hari ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-4 py-3 bg-orange-50 border-b border-orange-100 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-orange-800">Ban Luar Batam</h2>
            <span class="bg-orange-600 text-white text-xs px-2 py-1 rounded-full font-bold">{{ $stockBanLuarBatams->count() }} Data</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No Seri / Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Merk & Ukuran</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kondisi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Masuk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diinput Oleh</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($stockBanLuarBatams as $ban)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $ban->nomor_seri ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div class="font-medium text-gray-800">{{ $ban->merk ?? '-' }}</div>
                            <div class="text-xs">{{ $ban->ukuran ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ ucfirst($ban->kondisi) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $ban->status }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ date('d-m-Y', strtotime($ban->tanggal_masuk)) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $ban->createdBy->name ?? 'System' }} 
                            <div class="text-[10px] text-gray-400 mt-1">{{ $ban->created_at->format('H:i:s') }}</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">Tidak ada input harian ban luar batam pada hari ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <!-- Activities Section -->
    <div class="bg-white rounded-lg shadow overflow-hidden mt-8 border border-gray-200">
        <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Riwayat Perubahan & Aktivitas</h2>
            <span class="bg-gray-600 text-white text-xs px-2 py-1 rounded-full font-bold">{{ $activities->count() }} Aktivitas</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data / Item</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Perubahan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($activities as $activity)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $activity->created_at->format('H:i:s') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">
                            {{ $activity->getUserDisplayName() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @php
                                $badgeClass = match($activity->action) {
                                    'created' => 'bg-green-100 text-green-800',
                                    'updated' => 'bg-blue-100 text-blue-800',
                                    'deleted' => 'bg-red-100 text-red-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-bold {{ $badgeClass }}">
                                {{ strtoupper($activity->action) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <div class="font-medium text-gray-800">
                                {{ class_basename($activity->auditable_type) == 'StockBan' ? 'Jakarta' : 'Batam' }}
                            </div>
                            @if($activity->auditable)
                                <div class="text-xs">{{ $activity->auditable->nomor_seri ?? 'No Seri: -' }}</div>
                                <div class="text-[10px] text-gray-400">{{ $activity->auditable->merk }} {{ $activity->auditable->ukuran }}</div>
                            @else
                                <div class="text-xs text-red-400 italic">Data telah dihapus</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-xs text-gray-500">
                            @if($activity->action === 'updated' && $activity->getFormattedChanges())
                                <ul class="list-disc pl-4 space-y-1">
                                    @foreach($activity->getFormattedChanges() as $change)
                                        @if(!in_array($change['field'], ['updated_at', 'created_at', 'updated_by', 'created_by']))
                                            <li>
                                                <span class="font-medium text-gray-700">{{ $change['field'] }}</span>: 
                                                <span class="text-red-500 line-through">{{ $change['old'] ?: '(kosong)' }}</span> 
                                                <i class="fas fa-arrow-right mx-1 text-gray-400"></i>
                                                <span class="text-green-600 font-bold">{{ $newValue = is_array($change['new']) ? json_encode($change['new']) : $change['new'] }}</span>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            @elseif($activity->action === 'created')
                                <span class="text-green-600 italic">Input data baru</span>
                            @elseif($activity->action === 'deleted')
                                <span class="text-red-600 italic">Menghapus data</span>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">Tidak ada riwayat aktivitas pada hari ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
