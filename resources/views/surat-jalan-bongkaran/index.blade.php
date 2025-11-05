@extends('layouts.app')

@section('title', 'Surat Jalan Bongkaran')

@section('content')
<div class="flex-1 p-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Surat Jalan Bongkaran</h1>
            <nav class="flex text-sm text-gray-600 mt-1">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                <span class="mx-2">/</span>
                <span class="text-gray-500">Surat Jalan Bongkaran</span>
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

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <span>{{ session('error') }}</span>
            <button type="button" class="ml-auto text-red-600 hover:text-red-800" onclick="this.parentElement.remove()">
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
                <h2 class="text-lg font-semibold text-gray-900">Daftar Surat Jalan Bongkaran</h2>
                @can('surat-jalan-bongkaran-create')
                    <a href="{{ route('surat-jalan-bongkaran.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Tambah Surat Jalan Bongkaran
                    </a>
                @endcan
            </div>
        </div>

        <!-- Card Body -->
        <div class="p-6">
            <!-- Filter Form -->
            <form method="GET" action="{{ route('surat-jalan-bongkaran.index') }}" class="mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                        <input type="date" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               id="start_date" 
                               name="start_date" 
                               value="{{ request('start_date') }}">
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                        <input type="date" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               id="end_date" 
                               name="end_date" 
                               value="{{ request('end_date') }}">
                    </div>
                    <div>
                        <label for="order_id" class="block text-sm font-medium text-gray-700 mb-1">Order</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                id="order_id" 
                                name="order_id">
                            <option value="">Semua Order</option>
                            @foreach($orders as $order)
                                <option value="{{ $order->id }}" {{ request('order_id') == $order->id ? 'selected' : '' }}>
                                    {{ $order->nomor_order }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="kapal_id" class="block text-sm font-medium text-gray-700 mb-1">Kapal</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                id="kapal_id" 
                                name="kapal_id">
                            <option value="">Semua Kapal</option>
                            @foreach($kapals as $kapal)
                                <option value="{{ $kapal->id }}" {{ request('kapal_id') == $kapal->id ? 'selected' : '' }}>
                                    {{ $kapal->nama_kapal }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                        <input type="text" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               id="search" 
                               name="search" 
                               placeholder="Cari nomor surat jalan, container, seal, pengirim, penerima..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Cari
                        </button>
                        <a href="{{ route('surat-jalan-bongkaran.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Reset
                        </a>
                    </div>
                </div>
            </form>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Surat Jalan</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Bongkar</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kapal</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Container</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengirim</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penerima</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Pembayaran</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($suratJalanBongkarans as $index => $sjb)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $suratJalanBongkarans->firstItem() + $index }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="font-semibold text-gray-900">{{ $sjb->nomor_surat_jalan }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ $sjb->tanggal_bongkar ? \Carbon\Carbon::parse($sjb->tanggal_bongkar)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $sjb->order ? $sjb->order->nomor_order : '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $sjb->kapal ? $sjb->kapal->nama_kapal : '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    @if($sjb->nomor_container)
                                        <div>{{ $sjb->nomor_container }}</div>
                                        @if($sjb->ukuran_container)
                                            <div class="text-xs text-gray-500">{{ $sjb->ukuran_container }}</div>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $sjb->nama_pengirim ?: '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $sjb->nama_penerima ?: '-' }}</td>
                                <td class="px-4 py-3 text-sm">
                                    @if($sjb->status_pembayaran)
                                        @php
                                            $badgeClass = match($sjb->status_pembayaran) {
                                                'lunas' => 'bg-green-100 text-green-800',
                                                'belum_lunas' => 'bg-yellow-100 text-yellow-800',
                                                'pending' => 'bg-gray-100 text-gray-800',
                                                default => 'bg-gray-100 text-gray-800'
                                            };
                                        @endphp
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $badgeClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $sjb->status_pembayaran)) }}
                                        </span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <div class="flex items-center space-x-2">
                                        @can('surat-jalan-bongkaran-view')
                                            <a href="{{ route('surat-jalan-bongkaran.show', $sjb) }}" 
                                               class="text-blue-600 hover:text-blue-900 p-1 rounded hover:bg-blue-50" 
                                               title="Lihat">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                        @endcan
                                        
                                        @can('surat-jalan-bongkaran-update')
                                            <a href="{{ route('surat-jalan-bongkaran.edit', $sjb) }}" 
                                               class="text-yellow-600 hover:text-yellow-900 p-1 rounded hover:bg-yellow-50" 
                                               title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                        @endcan
                                        
                                        @can('surat-jalan-bongkaran-delete')
                                            <form action="{{ route('surat-jalan-bongkaran.destroy', $sjb) }}" 
                                                  method="POST" 
                                                  class="inline" 
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus surat jalan bongkaran ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50" 
                                                        title="Hapus">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada surat jalan bongkaran</h3>
                                        <p class="text-gray-500 mb-4">Belum ada data surat jalan bongkaran yang tersedia.</p>
                                        @can('surat-jalan-bongkaran-create')
                                            <a href="{{ route('surat-jalan-bongkaran.create') }}" 
                                               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                </svg>
                                                Tambah Surat Jalan Bongkaran Pertama
                                            </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($suratJalanBongkarans->hasPages())
                <div class="flex flex-col sm:flex-row justify-between items-center mt-6 pt-4 border-t border-gray-200">
                    <div class="text-sm text-gray-700 mb-4 sm:mb-0">
                        Menampilkan {{ $suratJalanBongkarans->firstItem() }} sampai {{ $suratJalanBongkarans->lastItem() }} 
                        dari {{ $suratJalanBongkarans->total() }} data
                    </div>
                    <div>
                        {{ $suratJalanBongkarans->appends(request()->query())->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto submit form when filters change
    const filterElements = document.querySelectorAll('#start_date, #end_date, #order_id, #kapal_id');
    filterElements.forEach(element => {
        element.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });
});
</script>
@endpush