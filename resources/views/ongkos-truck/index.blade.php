@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-truck mr-3 text-purple-600 text-2xl"></i>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Ongkos Truck</h1>
                    <p class="text-gray-600">Filter dan Kelola Data Ongkos Truck</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
        </div>
    @endif

    {{-- Filter Form --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="border-b border-gray-200 mb-4">
            <h3 class="text-lg font-semibold text-gray-700 pb-2 flex items-center">
                <i class="fas fa-filter mr-2 text-purple-600"></i>
                Filter Data Ongkos Truck
            </h3>
        </div>

        <form action="{{ route('ongkos-truck.show-data') }}" method="GET" id="filterForm">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Tanggal Dari --}}
                <div>
                    <label for="tanggal_dari" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Dari <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           id="tanggal_dari" 
                           name="tanggal_dari"
                           value="{{ request('tanggal_dari', date('Y-m-01')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
                           required>
                </div>

                {{-- Tanggal Sampai --}}
                <div>
                    <label for="tanggal_sampai" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Sampai <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           id="tanggal_sampai" 
                           name="tanggal_sampai"
                           value="{{ request('tanggal_sampai', date('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
                           required>
                </div>

                {{-- Nomor Mobil/Polisi --}}
                <div>
                    <label for="mobil_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor Mobil <span class="text-red-500">*</span>
                    </label>
                    <select id="mobil_id" 
                            name="mobil_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
                            required>
                        <option value="">--Pilih Nomor Mobil--</option>
                        @foreach($mobils as $mobil)
                            <option value="{{ $mobil->id }}" {{ request('mobil_id') == $mobil->id ? 'selected' : '' }}>
                                {{ $mobil->nomor_polisi }} - {{ $mobil->merek }} {{ $mobil->jenis }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button type="submit" 
                        class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-md transition duration-200 inline-flex items-center">
                    <i class="fas fa-search mr-2"></i>
                    Tampilkan Data
                </button>
                <a href="{{ route('ongkos-truck.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-md transition duration-200 inline-flex items-center">
                    <i class="fas fa-redo mr-2"></i>
                    Reset Filter
                </a>
            </div>
        </form>
    </div>

    {{-- Results Table (only show when filtered) --}}
    @if(isset($ongkosTrucks))
    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 bg-purple-500 text-white rounded-t-lg flex justify-between items-center">
            <h3 class="text-lg font-semibold">Data Ongkos Truck</h3>
            <div class="flex gap-2">
                <button onclick="window.print()" class="bg-white text-purple-600 px-4 py-1 rounded text-sm hover:bg-purple-50 transition">
                    <i class="fas fa-print mr-1"></i> Print
                </button>
                <a href="{{ route('ongkos-truck.export-excel', request()->query()) }}" 
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-1 rounded text-sm transition">
                    <i class="fas fa-file-excel mr-1"></i> Export Excel
                </a>
            </div>
        </div>
        
        <div class="p-6">
            {{-- Summary Info --}}
            <div class="mb-4 p-4 bg-purple-50 rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600">Periode:</span>
                        <span class="font-semibold ml-2">{{ date('d/m/Y', strtotime(request('tanggal_dari'))) }} - {{ date('d/m/Y', strtotime(request('tanggal_sampai'))) }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Nomor Mobil:</span>
                        <span class="font-semibold ml-2">{{ $selectedMobil->nomor_polisi ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Total Perjalanan:</span>
                        <span class="font-semibold ml-2">{{ $ongkosTrucks->count() }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Total Ongkos:</span>
                        <span class="font-semibold ml-2 text-purple-600">Rp {{ number_format($ongkosTrucks->sum('total_ongkos'), 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            @if($ongkosTrucks->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Nomor Mobil</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Rute</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Nama Supir</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Total Ongkos</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($ongkosTrucks as $index => $ongkos)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ date('d/m/Y', strtotime($ongkos->tanggal)) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 font-medium">{{ $ongkos->mobil->nomor_polisi ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $ongkos->rute }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $ongkos->nama_supir }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 text-right font-semibold">Rp {{ number_format($ongkos->total_ongkos, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-sm text-center">
                                <a href="{{ route('ongkos-truck.show', $ongkos->id) }}" 
                                   class="text-blue-600 hover:text-blue-800 mr-2" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($user && $user->can('ongkos-truck-update'))
                                <a href="{{ route('ongkos-truck.edit', $ongkos->id) }}" 
                                   class="text-yellow-600 hover:text-yellow-800 mr-2" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endif
                                @if($user && $user->can('ongkos-truck-delete'))
                                <form action="{{ route('ongkos-truck.destroy', $ongkos->id) }}" 
                                      method="POST" 
                                      class="inline-block"
                                      onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 font-bold">
                        <tr>
                            <td colspan="5" class="px-4 py-3 text-sm text-gray-900 text-right">TOTAL:</td>
                            <td class="px-4 py-3 text-sm text-purple-600 text-right">Rp {{ number_format($ongkosTrucks->sum('total_ongkos'), 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @else
            <div class="text-center py-8">
                <i class="fas fa-inbox text-6xl text-gray-400 mb-4"></i>
                <p class="text-gray-600">Tidak ada data ongkos truck untuk filter yang dipilih</p>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2 for mobil dropdown
        $('#mobil_id').select2({
            placeholder: 'Pilih Nomor Mobil',
            allowClear: true,
            width: '100%'
        });

        // Validate date range
        $('#filterForm').on('submit', function(e) {
            const tanggalDari = new Date($('#tanggal_dari').val());
            const tanggalSampai = new Date($('#tanggal_sampai').val());
            
            if (tanggalDari > tanggalSampai) {
                e.preventDefault();
                alert('Tanggal Dari tidak boleh lebih besar dari Tanggal Sampai');
                return false;
            }
        });
    });
</script>
@endpush
@endsection
