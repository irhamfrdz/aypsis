@extends('layouts.app')

@section('title', 'Pembatalan Surat Jalan')
@section('page_title', 'Pembatalan Surat Jalan')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Pembatalan Surat Jalan</h1>
                    <p class="mt-1 text-sm text-gray-600">Daftar surat jalan aktif yang dapat dibatalkan</p>
                </div>
            </div>
        </div>

        <!-- Search Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <form method="GET" action="{{ route('surat-jalan.pembatalan') }}">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <!-- Pencarian -->
                    <div class="md:col-span-2">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Pencarian
                        </label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                               placeholder="No. Surat Jalan, Pengirim, Supir, Plat..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Mulai</label>
                            <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">Selesai</label>
                            <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-2">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'start_date', 'end_date']))
                        <a href="{{ route('surat-jalan.pembatalan') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Table Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Daftar Pembatalan</h3>
                <p class="mt-1 text-sm text-gray-600">Total: {{ $suratJalans->total() }} surat jalan</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-xs resizable-table" id="suratJalanTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">No. SJ</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Pengirim</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tujuan Ambil</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tujuan Kirim</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Barang</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Supir</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">No. Plat</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($suratJalans as $suratJalan)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center space-x-1">
                                    {{-- Tombol Detail --}}
                                    <a href="{{ route('surat-jalan.show', $suratJalan->id) }}" class="text-blue-600 hover:text-blue-900" title="Lihat Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>

                                    {{-- Tombol Cancel Direct --}}
                                    <button type="button" onclick="cancelSuratJalan('{{ $suratJalan->id }}', '{{ $suratJalan->no_surat_jalan }}')" class="inline-flex items-center px-2 py-1 bg-red-600 hover:bg-red-700 text-white text-xs rounded shadow-sm">
                                        Batal
                                    </button>
                                </div>
                            </td>
                            <td class="px-3 py-2 font-medium">
                                <a href="{{ route('surat-jalan.show', $suratJalan->id) }}" class="text-indigo-600 hover:underline">{{ $suratJalan->no_surat_jalan }}</a>
                            </td>
                            <td class="px-3 py-2">{{ date('d/m/Y', strtotime($suratJalan->tanggal_surat_jalan)) }}</td>
                            <td class="px-3 py-2">{{ $suratJalan->pengirim ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $suratJalan->tujuan_pengambilan ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $suratJalan->tujuan_pengiriman ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $suratJalan->jenis_barang ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $suratJalan->supir ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $suratJalan->no_plat ?? '-' }}</td>
                            <td class="px-3 py-2">
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium {{ $suratJalan->status_badge }}">
                                    {{ ucfirst($suratJalan->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="px-6 py-8 text-center text-sm text-gray-500">Belum ada data surat jalan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($suratJalans->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                @include('components.modern-pagination', ['paginator' => $suratJalans])
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function cancelSuratJalan(suratJalanId, noSj) {
    if (confirm('Yakin ingin MEMBATALKAN Surat Jalan ' + noSj + '?')) {
        fetch(`/surat-jalan/${suratJalanId}/update-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status: 'cancelled' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Surat Jalan ' + noSj + ' berhasil dibatalkan.');
                location.reload();
            } else {
                alert('Gagal membatalkan: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat membatalkan surat jalan');
        });
    }
}
</script>
@endsection
