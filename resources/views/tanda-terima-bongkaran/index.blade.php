@extends('layouts.app')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('title', 'Tanda Terima Bongkaran')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Tanda Terima Bongkaran</h1>
                <p class="text-gray-600 mt-1">Kelola dan pantau status pembayaran surat jalan bongkaran</p>
            </div>
            <div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('tanda-terima-bongkaran.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-medium rounded-lg shadow-sm transition duration-200">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Tanda Terima Bongkaran
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Filter & Search -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('tanda-terima-bongkaran.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <!-- Search -->
                <div class="md:col-span-3">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                    <input type="text" 
                           name="search" 
                           id="search" 
                           value="{{ request('search') }}"
                           placeholder="Cari nomor TT, kontainer..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>

                <!-- Status Pembayaran -->
                <div class="md:col-span-3">
                    <label for="status_pembayaran" class="block text-sm font-medium text-gray-700 mb-2">Status Pembayaran</label>
                    <select name="status_pembayaran" 
                            id="status_pembayaran"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        <option value="">Semua Status</option>
                        <option value="belum_dibayar" {{ request('status_pembayaran') == 'belum_dibayar' ? 'selected' : '' }}>Belum Dibayar</option>
                        <option value="dibayar_sebagian" {{ request('status_pembayaran') == 'dibayar_sebagian' ? 'selected' : '' }}>Dibayar Sebagian</option>
                        <option value="lunas" {{ request('status_pembayaran') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                    </select>
                </div>

                <!-- Kegiatan -->
                <div class="md:col-span-3">
                    <label for="kegiatan" class="block text-sm font-medium text-gray-700 mb-2">Kegiatan</label>
                    <select name="kegiatan" 
                            id="kegiatan"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        <option value="">Semua Kegiatan</option>
                        <option value="Bongkar" {{ request('kegiatan') == 'Bongkar' ? 'selected' : '' }}>Bongkar</option>
                        <option value="Delivery" {{ request('kegiatan') == 'Delivery' ? 'selected' : '' }}>Delivery</option>
                        <option value="Stripping" {{ request('kegiatan') == 'Stripping' ? 'selected' : '' }}>Stripping</option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="md:col-span-3 flex items-end gap-2">
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-medium rounded-lg transition duration-200">
                        <i class="fas fa-search mr-2"></i>
                        Filter
                    </button>
                    <a href="{{ route('tanda-terima-bongkaran.index') }}" 
                       class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition duration-200">
                        <i class="fas fa-redo mr-2"></i>
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor SJ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal SJ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No BL</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No Kontainer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengirim</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kegiatan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Bayar</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($suratJalans ?? [] as $index => $suratJalan)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ ($suratJalans->currentPage() - 1) * $suratJalans->perPage() + $index + 1 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $suratJalan->nomor_surat_jalan ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $suratJalan->tanggal_surat_jalan ? $suratJalan->tanggal_surat_jalan->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $suratJalan->no_bl ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $suratJalan->no_kontainer ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $suratJalan->pengirim ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $suratJalan->kegiatan ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusPembayaran = $suratJalan->status_pembayaran ?? 'belum_dibayar';
                                $statusClass = [
                                    'belum_dibayar' => 'bg-red-100 text-red-800',
                                    'dibayar_sebagian' => 'bg-yellow-100 text-yellow-800',
                                    'lunas' => 'bg-green-100 text-green-800',
                                ];
                                $statusLabel = [
                                    'belum_dibayar' => 'Belum Dibayar',
                                    'dibayar_sebagian' => 'Dibayar Sebagian',
                                    'lunas' => 'Lunas',
                                ];
                                $class = $statusClass[$statusPembayaran] ?? 'bg-gray-100 text-gray-800';
                                $label = $statusLabel[$statusPembayaran] ?? 'Belum Dibayar';
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $class }}">
                                {{ $label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex items-center justify-center space-x-2">
                                @can('tanda-terima-bongkaran-view')
                                <a href="{{ route('surat-jalan-bongkaran.show', $suratJalan->id) }}" 
                                   class="text-blue-600 hover:text-blue-900"
                                   title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endcan

                                @can('tanda-terima-bongkaran-print')
                                <a href="{{ route('surat-jalan-bongkaran.print', $suratJalan->id) }}" 
                                   class="text-purple-600 hover:text-purple-900"
                                   target="_blank"
                                   title="Print">
                                    <i class="fas fa-print"></i>
                                </a>
                                @endcan

                                @can('tanda-terima-bongkaran-update')
                                <a href="{{ route('tanda-terima-bongkaran.create') }}?surat_jalan_id={{ $suratJalan->id }}" 
                                   class="text-teal-600 hover:text-teal-900"
                                   title="Buat Tanda Terima">
                                    <i class="fas fa-file-signature"></i>
                                </a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-inbox text-gray-300 text-5xl mb-4"></i>
                                <p class="text-gray-500 text-lg font-medium">Tidak ada data surat jalan bongkaran</p>
                                <p class="text-gray-400 text-sm mt-2">Data surat jalan bongkaran akan muncul di sini</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if(isset($suratJalans) && $suratJalans->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            <div class="flex items-center justify-between">
                <div class="flex-1 flex justify-between sm:hidden">
                    @if ($suratJalans->onFirstPage())
                        <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">
                            Previous
                        </span>
                    @else
                        <a href="{{ $suratJalans->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Previous
                        </a>
                    @endif

                    @if ($suratJalans->hasMorePages())
                        <a href="{{ $suratJalans->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Next
                        </a>
                    @else
                        <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">
                            Next
                        </span>
                    @endif
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Menampilkan
                            <span class="font-medium">{{ $suratJalans->firstItem() ?? 0 }}</span>
                            sampai
                            <span class="font-medium">{{ $suratJalans->lastItem() ?? 0 }}</span>
                            dari
                            <span class="font-medium">{{ $suratJalans->total() }}</span>
                            hasil
                        </p>
                    </div>
                    <div>
                        {{ $suratJalans->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-submit form on filter change
    document.getElementById('status_pembayaran')?.addEventListener('change', function() {
        this.form.submit();
    });
    
    document.getElementById('kegiatan')?.addEventListener('change', function() {
        this.form.submit();
    });
</script>
@endpush
