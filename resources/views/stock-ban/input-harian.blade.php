@extends('layouts.app')

@push('styles')
<style>
    .activity-log-table th {
        background-color: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
    }
    .change-item {
        display: flex;
        align-items: flex-start;
        padding: 4px 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .change-item:last-child {
        border-bottom: none;
    }
    .change-field {
        min-width: 100px;
        font-weight: 600;
        color: #64748b;
    }
    .change-values {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }
    .old-value {
        color: #94a3b8;
        text-decoration: line-through;
        background-color: #f1f5f9;
        padding: 1px 6px;
        border-radius: 4px;
        font-size: 11px;
    }
    .new-value {
        color: #059669;
        font-weight: 600;
        background-color: #ecfdf5;
        padding: 1px 6px;
        border-radius: 4px;
        font-size: 11px;
    }
    .data-item-box {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 6px 10px;
        display: inline-block;
        min-width: 140px;
    }
</style>
@endpush

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
            <table class="min-w-full divide-y divide-gray-200 activity-log-table">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Waktu</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Data / Item</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Detail Perubahan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($activities as $activity)
                    <tr class="hover:bg-blue-50/30 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $activity->created_at->format('H:i') }}</div>
                            <div class="text-[10px] text-gray-400 font-mono">{{ $activity->created_at->format('s') }}s</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-7 w-7 rounded-full bg-teal-100 flex items-center justify-center text-teal-700 mr-2 border border-teal-200">
                                    <i class="fas fa-user text-[10px]"></i>
                                </div>
                                <span class="text-sm text-gray-700 font-medium">{{ $activity->getUserDisplayName() }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $badgeClass = match($activity->action) {
                                    'created' => 'bg-green-100 text-green-800 border-green-200',
                                    'updated' => 'bg-blue-100 text-blue-800 border-blue-200',
                                    'deleted' => 'bg-red-100 text-red-800 border-red-200',
                                    default => 'bg-gray-100 text-gray-800 border-gray-200'
                                };
                                $icon = match($activity->action) {
                                    'created' => 'fa-plus-circle',
                                    'updated' => 'fa-edit',
                                    'deleted' => 'fa-trash-alt',
                                    default => 'fa-info-circle'
                                };
                            @endphp
                            <span class="px-2.5 py-1 rounded-md text-[10px] font-black border {{ $badgeClass }} flex items-center w-fit">
                                <i class="fas {{ $icon }} mr-1.5 opacity-70"></i>
                                {{ strtoupper($activity->action) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="data-item-box shadow-sm">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded {{ class_basename($activity->auditable_type) == 'StockBan' ? 'bg-indigo-100 text-indigo-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ class_basename($activity->auditable_type) == 'StockBan' ? 'JAKARTA' : 'BATAM' }}
                                    </span>
                                </div>
                                @if($activity->auditable)
                                    <div class="text-xs font-bold text-gray-800 font-mono">{{ $activity->auditable->nomor_seri ?? '-' }}</div>
                                    <div class="text-[10px] text-gray-500 truncate max-w-[150px]">{{ $activity->auditable->merk }} {{ $activity->auditable->ukuran }}</div>
                                @else
                                    <div class="text-xs text-red-500 italic font-medium">Data Terhapus</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($activity->action === 'updated' && $activity->getFormattedChanges())
                                <div class="space-y-0.5 max-w-xl">
                                    @foreach($activity->getFormattedChanges() as $change)
                                        @if(!in_array($change['field'], ['updated_at', 'created_at', 'updated_by', 'created_by']))
                                            <div class="change-item">
                                                <div class="change-field text-[10px] uppercase tracking-tight">{{ str_replace('_', ' ', $change['field']) }}</div>
                                                <div class="change-values text-[11px]">
                                                    <span class="old-value">{{ $change['old'] ?: '(kosong)' }}</span> 
                                                    <i class="fas fa-long-arrow-alt-right text-gray-300 text-xs"></i>
                                                    <span class="new-value">{{ is_array($change['new']) ? json_encode($change['new']) : $change['new'] }}</span>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @elseif($activity->action === 'created')
                                <div class="flex items-center text-green-600 text-xs font-semibold">
                                    <i class="fas fa-check-circle mr-1.5"></i>
                                    Berhasil input ban baru ke sistem
                                </div>
                            @elseif($activity->action === 'deleted')
                                <div class="flex items-center text-red-500 text-xs font-semibold">
                                    <i class="fas fa-exclamation-triangle mr-1.5"></i>
                                    Menghapus data ban dari database
                                </div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-history text-gray-200 text-4xl mb-3"></i>
                                <p class="text-gray-400 font-medium">Belum ada aktivitas tercatat hari ini.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
