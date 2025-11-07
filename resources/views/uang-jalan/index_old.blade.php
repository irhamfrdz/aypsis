@extends('layouts.app')

@section('content')
<style>
    .status-badge {
        @apply px-2 py-1 rounded-full text-xs font-medium;
    }
    .status-belum-masuk {
        @apply bg-gray-100 text-gray-800;
    }
    .status-sudah-masuk {
        @apply bg-blue-100 text-blue-800;
    }
    .status-sudah-berangkat {
        @apply bg-green-100 text-green-800;
    }
    .status-approved {
        @apply bg-emerald-100 text-emerald-800;
    }
</style>

<div class="container mx-auto px-4 py-4">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-4 border-b border-gray-200">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Uang Jalan</h1>
                <p class="text-sm text-gray-600 mt-1">Pilih surat jalan untuk dibuat uang jalannya</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <form method="GET" action="{{ route('uang-jalan.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Search -->
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                    <input type="text" 
                           name="search" 
                           value="{{ $search }}"
                           placeholder="Cari berdasarkan nomor surat jalan, supir, kenek, plat, nomor order..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status Surat Jalan</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach($statusOptions as $value => $label)
                            <option value="{{ $value }}" {{ $status == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Submit Button -->
                <div class="col-span-1 md:col-span-3 flex gap-2">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm">
                        <svg class="h-4 w-4 mr-1.5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Cari
                    </button>
                    <a href="{{ route('uang-jalan.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg text-sm">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        @if(session('success'))
            <div class="mx-4 mt-4">
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mx-4 mt-4">
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <!-- Results Info -->
        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
            <div class="flex justify-between items-center">
                <p class="text-sm text-gray-700">
                    Menampilkan <span class="font-medium">{{ $suratJalans->firstItem() ?? 0 }}</span> 
                    sampai <span class="font-medium">{{ $suratJalans->lastItem() ?? 0 }}</span> 
                    dari <span class="font-medium">{{ $suratJalans->total() }}</span> surat jalan
                </p>
                
                @if($search || $status != 'belum_masuk_checkpoint')
                    <p class="text-sm text-gray-500">
                        Filter aktif: 
                        @if($search)
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">{{ $search }}</span>
                        @endif
                        @if($status != 'belum_masuk_checkpoint')
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">{{ $statusOptions[$status] }}</span>
                        @endif
                    </p>
                @endif
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            @if($suratJalans->count() > 0)
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left py-3 px-4 font-medium text-gray-900">No. Surat Jalan</th>
                            <th class="text-left py-3 px-4 font-medium text-gray-900">Tanggal</th>
                            <th class="text-left py-3 px-4 font-medium text-gray-900">Pengirim / Order</th>
                            <th class="text-left py-3 px-4 font-medium text-gray-900">Supir / Kenek</th>
                            <th class="text-left py-3 px-4 font-medium text-gray-900">Plat / Kontainer</th>
                            <th class="text-left py-3 px-4 font-medium text-gray-900">Status</th>
                            <th class="text-left py-3 px-4 font-medium text-gray-900">Status Uang Jalan</th>
                            <th class="text-center py-3 px-4 font-medium text-gray-900">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($suratJalans as $suratJalan)
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4">
                                    <div class="font-medium text-gray-900">{{ $suratJalan->no_surat_jalan }}</div>
                                    <div class="text-sm text-gray-500">{{ $suratJalan->kegiatan }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="text-sm text-gray-900">
                                        {{ $suratJalan->tanggal_surat_jalan ? $suratJalan->tanggal_surat_jalan->format('d/m/Y') : '-' }}
                                    </div>
                                    @if($suratJalan->tanggal_muat)
                                        <div class="text-xs text-gray-500">
                                            Muat: {{ $suratJalan->tanggal_muat->format('d/m/Y') }}
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    @if($suratJalan->order)
                                        <div class="font-medium text-gray-900">{{ $suratJalan->order->pengirim->nama_pengirim ?? '-' }}</div>
                                        <div class="text-sm text-gray-500">{{ $suratJalan->order->nomor_order }}</div>
                                        <div class="text-xs text-gray-500">{{ $suratJalan->order->jenisBarang->nama_barang ?? '-' }}</div>
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <div class="text-sm">
                                        <div class="font-medium text-gray-900">{{ $suratJalan->supir ?? '-' }}</div>
                                        @if($suratJalan->kenek)
                                            <div class="text-gray-500">Kenek: {{ $suratJalan->kenek }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="text-sm">
                                        <div class="font-medium text-gray-900">{{ $suratJalan->no_plat ?? '-' }}</div>
                                        @if($suratJalan->nomor_kontainer)
                                            <div class="text-gray-500">{{ $suratJalan->nomor_kontainer }}</div>
                                        @endif
                                        @if($suratJalan->size)
                                            <div class="text-xs text-gray-500">{{ $suratJalan->size }}ft</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    @php
                                        $statusClass = match($suratJalan->status) {
                                            'belum_masuk_checkpoint' => 'status-belum-masuk',
                                            'sudah_masuk_checkpoint' => 'status-sudah-masuk',
                                            'sudah_berangkat' => 'status-sudah-berangkat',
                                            'approved' => 'status-approved',
                                            default => 'status-belum-masuk'
                                        };
                                        
                                        $statusText = match($suratJalan->status) {
                                            'belum_masuk_checkpoint' => 'Belum Masuk',
                                            'sudah_masuk_checkpoint' => 'Sudah Masuk',
                                            'sudah_berangkat' => 'Sudah Berangkat',
                                            'approved' => 'Approved',
                                            default => $suratJalan->status
                                        };
                                    @endphp
                                    <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                                </td>
                                <td class="py-3 px-4">
                                    @php
                                        $uangJalanStatus = $suratJalan->status_pembayaran_uang_jalan ?? 'belum_ada';
                                        $statusUangJalanClass = match($uangJalanStatus) {
                                            'sudah_masuk_uang_jalan' => 'bg-green-100 text-green-800',
                                            'belum_ada' => 'bg-gray-100 text-gray-800',
                                            default => 'bg-gray-100 text-gray-800'
                                        };
                                        
                                        $statusUangJalanText = match($uangJalanStatus) {
                                            'sudah_masuk_uang_jalan' => 'Sudah Ada',
                                            'belum_ada' => 'Belum Ada',
                                            default => ucfirst(str_replace('_', ' ', $uangJalanStatus))
                                        };
                                    @endphp
                                    <span class="status-badge {{ $statusUangJalanClass }}">{{ $statusUangJalanText }}</span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($suratJalan->status_pembayaran_uang_jalan !== 'sudah_masuk_uang_jalan')
                                        <a href="{{ route('uang-jalan.create', ['surat_jalan_id' => $suratJalan->id]) }}" 
                                           class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded text-sm font-medium transition-colors duration-150">
                                            Buat Uang Jalan
                                        </a>
                                    @else
                                        <span class="bg-gray-100 text-gray-500 px-3 py-1.5 rounded text-sm">
                                            Sudah Dibuat
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada surat jalan</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @if($search || $status != 'belum_masuk_checkpoint')
                            Tidak ada surat jalan yang sesuai dengan kriteria pencarian.
                        @else
                            Belum ada surat jalan yang terdaftar dalam sistem.
                        @endif
                    </p>
                    @if($search || $status != 'belum_masuk_checkpoint')
                        <div class="mt-6">
                            <a href="{{ route('uang-jalan.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg text-sm">
                                Lihat Semua Surat Jalan
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Pagination -->
        @if($suratJalans->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $suratJalans->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection