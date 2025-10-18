@extends('layouts.app')

@section('title', 'Detail Gate In')
@section('page_title', 'Detail Gate In')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="flex justify-between items-center p-6 border-b border-gray-200">
            <h1 class="text-2xl font-semibold text-gray-900">Detail Gate In: {{ $gateIn->nomor_gate_in }}</h1>
            <div class="flex space-x-2">
                <a href="{{ route('gate-in.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-150 flex items-center">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
                @can('gate-in.edit')
                    <a href="{{ route('gate-in.edit', $gateIn) }}"
                       class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors duration-150 flex items-center">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>
                @endcan
                @if($gateIn->status === 'aktif')
                    <button type="button"
                            class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors duration-150 flex items-center"
                            onclick="openStatusModal()">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Selesaikan
                    </button>
                @endif
            </div>
        </div>

        <!-- Alert Messages -->
        <div class="p-6">
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Informasi Gate In -->
                <div class="bg-gray-50 rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Informasi Gate In</h3>
                    </div>
                    <div class="p-6">
                        <dl class="space-y-4">
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">No. Gate In:</dt>
                                <dd class="text-sm text-gray-900 font-mono">{{ $gateIn->nomor_gate_in }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Tanggal Gate In:</dt>
                                <dd class="text-sm text-gray-900">{{ $gateIn->tanggal_gate_in ? $gateIn->tanggal_gate_in->format('d/m/Y H:i') : '-' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Terminal:</dt>
                                <dd class="text-sm text-gray-900">{{ $gateIn->terminal->nama_terminal ?? '-' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Kapal:</dt>
                                <dd class="text-sm text-gray-900">{{ $gateIn->kapal->nama_kapal ?? '-' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Service:</dt>
                                <dd class="text-sm text-gray-900">{{ $gateIn->service->nama_service ?? '-' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Status:</dt>
                                <dd class="text-sm">
                                    @if($gateIn->status === 'aktif')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Aktif
                                        </span>
                                    @elseif($gateIn->status === 'selesai')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Selesai
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            {{ ucfirst($gateIn->status) }}
                                        </span>
                                    @endif
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Keterangan:</dt>
                                <dd class="text-sm text-gray-900">{{ $gateIn->keterangan ?: '-' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Dibuat oleh:</dt>
                                <dd class="text-sm text-gray-900">{{ $gateIn->user->name ?? '-' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Summary -->
                <div class="bg-blue-50 rounded-lg border border-blue-200">
                    <div class="px-6 py-4 border-b border-blue-200">
                        <h3 class="text-lg font-medium text-blue-900">Ringkasan</h3>
                    </div>
                    <div class="p-6">
                        <dl class="space-y-4">
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-blue-700">Total Kontainer:</dt>
                                <dd class="text-sm text-blue-900 font-semibold">{{ $gateIn->suratJalans->count() }} kontainer</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-blue-700">Sudah Gate In:</dt>
                                <dd class="text-lg font-bold text-green-600">{{ $gateIn->suratJalans->where('status_gate_in', 'selesai')->count() }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-blue-700">Dalam Proses:</dt>
                                <dd class="text-lg font-bold text-yellow-600">{{ $gateIn->suratJalans->where('status_gate_in', 'proses')->count() }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-blue-700">Pending:</dt>
                                <dd class="text-lg font-bold text-gray-600">{{ $gateIn->suratJalans->where('status_gate_in', 'pending')->count() }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-blue-700">Dibuat:</dt>
                                <dd class="text-sm text-blue-900">{{ $gateIn->created_at->format('d/m/Y H:i') }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-blue-700">Diupdate:</dt>
                                <dd class="text-sm text-blue-900">{{ $gateIn->updated_at->format('d/m/Y H:i') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Daftar Kontainer -->
            <div class="bg-white rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">Daftar Kontainer dalam Gate In</h3>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-500">Filter:</span>
                            <select id="status-filter" class="text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Semua Status</option>
                                <option value="pending">Pending</option>
                                <option value="proses">Dalam Proses</option>
                                <option value="selesai">Sudah Gate In</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Surat Jalan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Kontainer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supir</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tujuan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Gate In</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Gate In</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($gateIn->suratJalans as $index => $suratJalan)
                                <tr class="hover:bg-gray-50 kontainer-item" data-status="{{ $suratJalan->status_gate_in ?? 'pending' }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">{{ $suratJalan->no_surat_jalan }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">{{ $suratJalan->no_kontainer ?: '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $suratJalan->size ? $suratJalan->size . 'ft' : '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $suratJalan->supir ?: '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $suratJalan->tujuan_pengiriman ?: '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if(($suratJalan->status_gate_in ?? 'pending') === 'selesai')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Selesai
                                            </span>
                                        @elseif(($suratJalan->status_gate_in ?? 'pending') === 'proses')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Proses
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($suratJalan->tanggal_gate_in)
                                            {{ \Carbon\Carbon::parse($suratJalan->tanggal_gate_in)->format('d/m/Y H:i') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                        Tidak ada kontainer ditemukan
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if($gateIn->suratJalans->count() > 0)
                            <tfoot class="bg-blue-50">
                                <tr>
                                    <th colspan="7" class="px-6 py-3 text-right text-sm font-medium text-blue-900">Total Kontainer:</th>
                                    <th class="px-6 py-3 text-left text-sm font-bold text-green-600">
                                        {{ $gateIn->suratJalans->count() }}
                                    </th>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Modal untuk Selesaikan Gate In -->
@if($gateIn->status === 'aktif')
<div id="statusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Selesaikan Gate In</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeStatusModal()">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <p class="text-sm text-gray-700 mb-6">
                Apakah Anda yakin ingin menyelesaikan Gate In <strong>{{ $gateIn->nomor_gate_in }}</strong>?
            </p>
            <div class="grid grid-cols-2 gap-3">
                <button type="button" onclick="closeStatusModal()"
                        class="w-full bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-150">
                    Batal
                </button>
                <form action="{{ route('gate-in.update-status', $gateIn) }}" method="POST" class="w-full">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="selesai">
                    <button type="submit"
                            class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors duration-150">
                        Ya, Selesaikan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Status filter functionality
    const statusFilter = document.getElementById('status-filter');
    const kontainerItems = document.querySelectorAll('.kontainer-item');

    statusFilter.addEventListener('change', function() {
        const selectedStatus = this.value;

        kontainerItems.forEach(item => {
            const itemStatus = item.dataset.status;

            if (selectedStatus === '' || itemStatus === selectedStatus) {
                item.style.display = 'table-row';
            } else {
                item.style.display = 'none';
            }
        });
    });
});

function openStatusModal() {
    document.getElementById('statusModal').classList.remove('hidden');
}

function closeStatusModal() {
    document.getElementById('statusModal').classList.add('hidden');
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('statusModal');
    if (event.target == modal) {
        closeStatusModal();
    }
}
</script>
@endsection
