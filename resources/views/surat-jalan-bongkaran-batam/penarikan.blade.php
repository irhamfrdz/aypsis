@extends('layouts.app')

@section('title', 'Penarikan Surat Jalan Batam')

@section('content')
<div class="flex-1 p-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Penarikan Surat Jalan Batam</h1>
            <nav class="flex text-sm text-gray-600 mt-1">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                <span class="mx-2">/</span>
                <span class="text-gray-500">Penarikan Surat Jalan Batam</span>
            </nav>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span>{{ session('success') }}</span>
            <button type="button" class="ml-auto text-green-600 hover:text-green-800" onclick="this.parentElement.remove()">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    @endif

    <!-- Main Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Card Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Daftar Surat Jalan Bongkaran Batam</h2>
                </div>
            </div>
        </div>

        <!-- Card Body -->
        <div class="p-6">
            <!-- Filter & Search Form -->
            <form method="GET" action="{{ route('penarikan-surat-jalan-batam.index') }}" class="mb-6">
                <div class="flex flex-col gap-4">
                    <div class="flex flex-wrap items-center gap-4">
                        <!-- Kapal Filter -->
                        <div class="flex items-center gap-3">
                            <label for="nama_kapal" class="text-sm font-medium text-gray-700 whitespace-nowrap">Kapal:</label>
                            <select name="nama_kapal" id="nama_kapal" 
                                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 bg-white"
                                    onchange="this.form.submit()">
                                <option value="">-- Semua Kapal --</option>
                                @foreach($ships as $ship)
                                    <option value="{{ $ship }}" {{ $selectedKapal == $ship ? 'selected' : '' }}>{{ $ship }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Voyage Filter -->
                        @if($selectedKapal)
                        <div class="flex items-center gap-3">
                            <label for="no_voyage" class="text-sm font-medium text-gray-700 whitespace-nowrap">Voyage:</label>
                            <select name="no_voyage" id="no_voyage" 
                                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 bg-white"
                                    onchange="this.form.submit()">
                                <option value="">-- Semua Voyage --</option>
                                @foreach($voyages as $voyage)
                                    <option value="{{ $voyage }}" {{ $selectedVoyage == $voyage ? 'selected' : '' }}>{{ $voyage }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                    </div>

                    <!-- Search and Reset -->
                    <div class="flex items-center gap-2">
                        <div class="relative flex-1">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Cari nomor surat jalan, container, seal, term, supir, plat..."
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        </div>
                        <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg text-sm font-medium transition-colors duration-200">
                            Cari
                        </button>
                        <a href="{{ route('penarikan-surat-jalan-batam.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition-colors duration-200">
                            Reset
                        </a>
                    </div>
                </div>
            </form>

            <!-- Table -->
            <div class="overflow-x-auto relative">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Urut</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Surat Jalan</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Term</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supir</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No Plat</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Container</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type (Manifest)</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Barang</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($suratJalans as $sj)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <!-- Edit button link -->
                                        <a href="{{ route('surat-jalan-bongkaran-batam.edit', $sj->id) }}" target="_blank"
                                           class="p-1 text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 rounded"
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <!-- Print button link -->
                                        <a href="{{ route('surat-jalan-bongkaran-batam.print', $sj->id) }}" target="_blank"
                                           class="p-1 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded"
                                           title="Print">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $sj->manifest->nomor_urut ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-900">{{ $sj->nomor_surat_jalan ?: '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $sj->tanggal_surat_jalan ? $sj->tanggal_surat_jalan->format('d/m/Y') : '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $sj->term ?: '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $sj->supir ?: '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $sj->no_plat ?: '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $sj->no_kontainer ?: '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ $sj->jenis_pengiriman ?: ($sj->tipe_kontainer ?: ($sj->manifest->tipe_kontainer ?? '-')) }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ $sj->lokasi ? ucfirst($sj->lokasi) : '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ Str::limit($sj->jenis_barang, 30) ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada data Surat Jalan</h3>
                                        <p class="text-gray-500">Belum ada surat jalan bongkaran Batam yang terdaftar.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $suratJalans->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
