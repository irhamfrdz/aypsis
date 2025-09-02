@extends('layouts.app')

@section('title', 'Riwayat Approval')
@section('page_title', 'Riwayat Permohonan yang Telah Diproses')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg p-6">
        <!-- Navigation Tabs -->
        <div class="mb-6 border-b border-gray-200">
            <nav class="flex space-x-8">
                <a href="{{ route('approval.dashboard') }}" class="text-gray-500 hover:text-gray-700 whitespace-nowrap py-2 px-1 border-b-2 border-transparent font-medium text-sm">
                    📋 Dashboard Approval
                </a>
                <a href="{{ route('approval.riwayat') }}" class="text-indigo-600 whitespace-nowrap py-2 px-1 border-b-2 border-indigo-500 font-medium text-sm">
                    📚 Riwayat Approval
                </a>
            </nav>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('approval.riwayat') }}" class="mb-6 bg-gray-50 p-4 rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Filter Vendor -->
                <div>
                    <label for="vendor" class="block text-sm font-medium text-gray-700 mb-1">Vendor:</label>
                    <select name="vendor" id="vendor" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Semua Vendor</option>
                        @foreach(($vendors ?? []) as $vendor)
                            <option value="{{ $vendor }}" {{ request('vendor') == $vendor ? 'selected' : '' }}>
                                {{ $vendor }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status:</label>
                    <select name="status" id="status" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Semua Status</option>
                        @foreach(($statusOptions ?? []) as $value => $label)
                            <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Kegiatan -->
                <div>
                    <label for="kegiatan" class="block text-sm font-medium text-gray-700 mb-1">Kegiatan:</label>
                    <select name="kegiatan" id="kegiatan" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Semua Kegiatan</option>
                        @if(isset($kegiatans))
                            @foreach($kegiatans as $kegiatan)
                                <option value="{{ $kegiatan->kode_kegiatan }}" {{ request('kegiatan') == $kegiatan->kode_kegiatan ? 'selected' : '' }}>
                                    {{ $kegiatan->nama_kegiatan }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <!-- Filter Tanggal Dari -->
                <div>
                    <label for="tanggal_dari" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Dari:</label>
                    <input type="date" name="tanggal_dari" id="tanggal_dari" value="{{ request('tanggal_dari') }}"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Filter Tanggal Sampai -->
                <div>
                    <label for="tanggal_sampai" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Sampai:</label>
                    <input type="date" name="tanggal_sampai" id="tanggal_sampai" value="{{ request('tanggal_sampai') }}"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div class="mt-4 flex items-center gap-3">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition text-sm">
                    🔍 Filter
                </button>
                @if(request()->hasAny(['vendor', 'status', 'kegiatan', 'tanggal_dari', 'tanggal_sampai']))
                    <a href="{{ route('approval.riwayat') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition text-sm">
                        ✖️ Reset Filter
                    </a>
                @endif
            </div>
        </form>

        <!-- Active Filters Display -->
        @if(request()->hasAny(['vendor', 'status', 'kegiatan', 'tanggal_dari', 'tanggal_sampai']))
            <div class="mb-4 flex items-center gap-2 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <span class="text-sm font-medium text-blue-800">Filter Aktif:</span>
                @if(request('vendor'))
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        Vendor: {{ request('vendor') }}
                    </span>
                @endif
                @if(request('status'))
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Status: {{ request('status') }}
                    </span>
                @endif
                @if(request('kegiatan'))
                    @php
                        $kegiatanLabel = isset($kegiatans) ? $kegiatans->where('kode_kegiatan', request('kegiatan'))->first()?->nama_kegiatan ?? request('kegiatan') : request('kegiatan');
                    @endphp
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                        Kegiatan: {{ $kegiatanLabel }}
                    </span>
                @endif
                @if(request('tanggal_dari') || request('tanggal_sampai'))
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        Periode: {{ request('tanggal_dari') ?? '...' }} s/d {{ request('tanggal_sampai') ?? '...' }}
                    </span>
                @endif
            </div>
        @endif

        <!-- Summary Stats -->
        <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $permohonans->total() }}</div>
                <div class="text-sm text-blue-700">Total Permohonan</div>
            </div>
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-green-600">
                    {{ $permohonans->where('status', 'Selesai')->count() }}
                </div>
                <div class="text-sm text-green-700">Selesai</div>
            </div>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-yellow-600">
                    {{ $permohonans->where('status', 'Bermasalah')->count() }}
                </div>
                <div class="text-sm text-yellow-700">Bermasalah</div>
            </div>
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-red-600">
                    {{ $permohonans->where('status', 'Dibatalkan')->count() }}
                </div>
                <div class="text-sm text-red-700">Dibatalkan</div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto bg-white rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                            Nomor Memo
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">
                            Supir
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">
                            Kegiatan
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                            Tujuan
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">
                            Vendor
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-28">
                            Nomor Kontainer
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                            Tanggal Selesai
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-24">
                            Status
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($permohonans as $permohonan)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $permohonan->nomor_memo }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $permohonan->supir->nama_panggilan ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $kegiatanLabel = \App\Models\MasterKegiatan::where('kode_kegiatan', $permohonan->kegiatan)->value('nama_kegiatan') ?? (isset($permohonan->kegiatan) ? ucfirst($permohonan->kegiatan) : '-');
                                @endphp
                                <div class="text-sm text-gray-900 max-w-xs">
                                    {{ $kegiatanLabel }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $permohonan->tujuan }}">
                                    {{ $permohonan->tujuan }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $permohonan->vendor_perusahaan ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if ($permohonan->kontainers && $permohonan->kontainers->count())
                                    <div class="text-sm text-gray-900">
                                        @if($permohonan->kontainers->count() > 1)
                                            <div class="space-y-1">
                                                @foreach($permohonan->kontainers->take(2) as $kontainer)
                                                    <div class="bg-gray-100 px-2 py-1 rounded text-xs font-mono">
                                                        {{ $kontainer->nomor_kontainer }}
                                                    </div>
                                                @endforeach
                                                @if($permohonan->kontainers->count() > 2)
                                                    <div class="text-xs text-gray-500">
                                                        +{{ $permohonan->kontainers->count() - 2 }} lainnya
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <div class="bg-gray-100 px-2 py-1 rounded text-xs font-mono inline-block">
                                                {{ $permohonan->kontainers->first()->nomor_kontainer }}
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400 text-sm">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($permohonan->updated_at)->format('d-m-Y') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($permohonan->updated_at)->format('H:i') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($permohonan->status == 'Selesai')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $permohonan->status }}
                                    </span>
                                @elseif($permohonan->status == 'Bermasalah')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $permohonan->status }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $permohonan->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <!-- View Detail Button -->
                                    <button onclick="showDetail({{ $permohonan->id }})" class="inline-flex items-center px-3 py-1.5 border border-blue-300 text-xs font-medium rounded-md text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-150">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        Detail
                                    </button>

                                    @if($permohonan->checkpoints && $permohonan->checkpoints->count())
                                        <!-- Timeline Button -->
                                        <button onclick="showTimeline({{ $permohonan->id }})" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-150">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Timeline
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Data</h3>
                                    <p class="text-sm text-gray-500 mb-4">Tidak ada riwayat permohonan yang ditemukan.</p>
                                    @if(request()->hasAny(['vendor', 'status', 'kegiatan', 'tanggal_dari', 'tanggal_sampai']))
                                        <a href="{{ route('approval.riwayat') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-600 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                            </svg>
                                            Reset Filter
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $permohonans->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div id="detailModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal()"></div>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="flex items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                            Detail Permohonan
                        </h3>
                        <div id="modalContent">
                            <!-- Content will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function showDetail(permohonanId) {
    // Show modal
    document.getElementById('detailModal').classList.remove('hidden');

    // Show loading
    document.getElementById('modalContent').innerHTML = '<div class="text-center py-4"><div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div><p class="mt-2 text-sm text-gray-600">Memuat detail...</p></div>';

    // In a real implementation, you would fetch data via AJAX
    // For now, show placeholder content
    setTimeout(() => {
        document.getElementById('modalContent').innerHTML = `
            <div class="space-y-3">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><span class="font-medium">ID:</span> ${permohonanId}</div>
                    <div><span class="font-medium">Status:</span> <span class="text-green-600">Selesai</span></div>
                </div>
                <div class="text-sm text-gray-600">
                    <p>Detail lengkap permohonan akan ditampilkan di sini.</p>
                    <p class="mt-2 text-xs">Fitur ini dapat diperluas untuk menampilkan informasi lengkap seperti catatan, lampiran, dll.</p>
                </div>
            </div>
        `;
    }, 500);
}

function showTimeline(permohonanId) {
    // Show modal
    document.getElementById('detailModal').classList.remove('hidden');
    document.getElementById('modal-title').textContent = 'Timeline Permohonan';

    // Show loading
    document.getElementById('modalContent').innerHTML = '<div class="text-center py-4"><div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div><p class="mt-2 text-sm text-gray-600">Memuat timeline...</p></div>';

    // Timeline placeholder
    setTimeout(() => {
        document.getElementById('modalContent').innerHTML = `
            <div class="space-y-4">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">Permohonan Selesai</p>
                        <p class="text-xs text-gray-500">01 Sep 2025, 14:30</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">Checkpoint Terakhir</p>
                        <p class="text-xs text-gray-500">01 Sep 2025, 10:15</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0 w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">Permohonan Dibuat</p>
                        <p class="text-xs text-gray-500">31 Agu 2025, 09:00</p>
                    </div>
                </div>
            </div>
        `;
    }, 500);
}

function closeModal() {
    document.getElementById('detailModal').classList.add('hidden');
    document.getElementById('modal-title').textContent = 'Detail Permohonan';
}
</script>
@endsection
