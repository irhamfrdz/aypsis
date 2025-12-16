@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-3 py-2">
    <div class="mb-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-lg font-semibold text-gray-900">Input DP untuk Pranota OB</h1>
                <p class="text-xs text-gray-600">{{ $pranota->nomor_pranota }} - {{ $pranota->nama_kapal }} / {{ $pranota->no_voyage }}</p>
            </div>
            <a href="{{ route('pranota-ob.index') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <!-- Daftar Supir dari Pranota -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-4">
        <h3 class="text-md font-semibold text-gray-900 mb-3">Daftar Supir dalam Pranota</h3>
        @if(count($supirList) > 0)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                @foreach($supirList as $supir)
                    <div class="flex items-center px-3 py-2 bg-blue-50 border border-blue-200 rounded">
                        <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-900">{{ $supir }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-500 italic">Tidak ada supir dalam pranota ini</p>
        @endif
    </div>

    <!-- Daftar Pembayaran DP -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="border-b border-gray-200 p-4">
            <h3 class="text-md font-semibold text-gray-900">Daftar Pembayaran DP</h3>
            <p class="text-xs text-gray-600 mt-1">Pilih pembayaran DP yang sesuai dengan supir di pranota ini</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Nomor Pembayaran</th>
                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Tanggal</th>
                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Supir</th>
                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Jumlah DP</th>
                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Jumlah per Supir</th>
                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Keterangan</th>
                        <th class="px-3 py-2 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pembayaranDps as $dp)
                        @php
                            $supirIds = is_string($dp->supir_ids) ? json_decode($dp->supir_ids, true) : $dp->supir_ids;
                            $jumlahPerSupir = is_string($dp->jumlah_per_supir) ? json_decode($dp->jumlah_per_supir, true) : $dp->jumlah_per_supir;
                            if (!is_array($supirIds)) $supirIds = [];
                            if (!is_array($jumlahPerSupir)) $jumlahPerSupir = [];
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 text-sm text-gray-900">{{ $dp->nomor_pembayaran }}</td>
                            <td class="px-3 py-2 text-sm text-gray-900">{{ \Carbon\Carbon::parse($dp->tanggal_pembayaran)->format('d/m/Y') }}</td>
                            <td class="px-3 py-2 text-sm text-gray-900">
                                @if(count($supirIds) > 0)
                                    <div class="space-y-1">
                                        @foreach($supirIds as $index => $supirId)
                                            @php
                                                $supirName = \DB::table('karyawans')->where('id', $supirId)->value('nama_lengkap') ?? $supirId;
                                            @endphp
                                            <div class="flex items-center">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ $supirName }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-500 text-xs italic">-</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-sm text-gray-900 font-semibold">
                                Rp {{ number_format($dp->dp_amount ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="px-3 py-2 text-sm text-gray-900">
                                @if(count($jumlahPerSupir) > 0)
                                    <div class="space-y-1">
                                        @foreach($jumlahPerSupir as $jumlah)
                                            <div class="text-xs">Rp {{ number_format($jumlah ?? 0, 0, ',', '.') }}</div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-500 text-xs italic">-</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-sm text-gray-600">{{ $dp->keterangan ?? '-' }}</td>
                            <td class="px-3 py-2 text-center">
                                <button type="button" onclick="viewDetail({{ $dp->id }})" class="text-indigo-600 hover:text-indigo-900" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-8 text-center text-gray-500">
                                Belum ada data pembayaran DP
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Info Section -->
    <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">Informasi</h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <p>Halaman ini menampilkan daftar pembayaran DP yang tersedia. Pastikan untuk memilih pembayaran DP yang sesuai dengan supir yang terdaftar dalam pranota ini.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function viewDetail(dpId) {
    // Implement detail view functionality
    alert('Detail pembayaran DP ID: ' + dpId + '\n\nFungsi ini akan dikembangkan lebih lanjut.');
}
</script>
@endsection
