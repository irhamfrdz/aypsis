@extends('layouts.app')

@section('title', 'Approval Tugas II')
@section('page_title', 'Dashboard Approval Tugas II - Persetujuan Sederhana')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg p-6">
        <!-- Info Panel -->
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        <strong>Approval Tugas II</strong> adalah langkah persetujuan sederhana yang hanya mengubah status memo menjadi "Selesai" dan menandai sebagai approved oleh system 2, sehingga memo dapat diproses untuk pembuatan pranota.
                    </p>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="mb-6 border-b border-gray-200">
            <nav class="flex space-x-8">
                <a href="{{ route('approval-ii.dashboard') }}" class="text-indigo-600 whitespace-nowrap py-2 px-1 border-b-2 border-indigo-500 font-medium text-sm">
                    📋 Dashboard Approval Tugas II
                </a>
                <a href="{{ route('approval-ii.riwayat') }}" class="text-gray-500 hover:text-gray-700 whitespace-nowrap py-2 px-1 border-b-2 border-transparent font-medium text-sm">
                    📚 Riwayat Approval Tugas II
                </a>
            </nav>
        </div>

            <form method="GET" action="" class="mb-4 flex items-center gap-4">
                <label for="vendor" class="font-semibold text-gray-700">Filter Vendor:</label>
                <select name="vendor" id="vendor" class="border rounded px-3 py-2">
                    <option value="">Semua Vendor</option>
                    <option value="AYP" {{ request('vendor') == 'AYP' ? 'selected' : '' }}>AYP</option>
                    <option value="ZONA" {{ request('vendor') == 'ZONA' ? 'selected' : '' }}>ZONA</option>
                    <option value="SOC" {{ request('vendor') == 'SOC' ? 'selected' : '' }}>SOC</option>
                    <option value="DPE" {{ request('vendor') == 'DPE' ? 'selected' : '' }}>DPE</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Filter</button>
            </form>
            <div class="table-container overflow-x-auto max-h-screen">
            <form method="POST" action="{{ route('approval-ii.mass_process') }}" class="w-full">
                @csrf
                <table class="min-w-full divide-y divide-gray-200">
                <thead class="sticky-table-header bg-gray-50 sticky top-0 z-10 shadow-sm">
                        <tr>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Memo</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Supir</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Kegiatan</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Kontainer</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Checkpoint</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Approval</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($permohonans as $permohonan)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-1 font-medium text-gray-900 text-[10px]">
                                    <div class="flex items-center justify-center gap-2">
                                        @php $hasCheckpoint = $permohonan->checkpoints && $permohonan->checkpoints->count(); @endphp
                                        <input type="checkbox" name="permohonan_ids[]" value="{{ $permohonan->id }}" class="permohonan-checkbox align-middle" {{ $hasCheckpoint ? '' : 'disabled' }}>
                                        <span class="inline-block min-w-[110px] text-center">{{ $permohonan->nomor_memo }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-1 text-[10px]">{{ $permohonan->supir->nama_panggilan ?? '-' }}</td>
                                @php
                                    $kegiatanLabel = \App\Models\MasterKegiatan::where('kode_kegiatan', $permohonan->kegiatan)->value('nama_kegiatan') ?? (isset($permohonan->kegiatan) ? ucfirst($permohonan->kegiatan) : '-');
                                @endphp
                                <td class="px-4 py-1 text-[10px]">{{ $kegiatanLabel }}</td>
                                <td class="px-4 py-1 text-[10px]">
                                    @if ($permohonan->kontainers && $permohonan->kontainers->count())
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($permohonan->kontainers->take(2) as $kontainer)
                                                <span class="inline-block bg-blue-100 text-blue-800 text-[9px] px-1 py-0.5 rounded">
                                                    {{ $kontainer->nomor_kontainer }}
                                                </span>
                                            @endforeach
                                            @if($permohonan->kontainers->count() > 2)
                                                <span class="inline-block bg-gray-100 text-gray-600 text-[9px] px-1 py-0.5 rounded">
                                                    +{{ $permohonan->kontainers->count() - 2 }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-1 text-[10px]">{{ $permohonan->vendor_perusahaan ?? '-' }}</td>
                                <td class="px-4 py-1 text-[10px]">
                                    @php
                                        $lastCheckpoint = $permohonan->checkpoints?->sortByDesc('tanggal_checkpoint')->first();
                                    @endphp
                                    @if ($lastCheckpoint)
                                        <span class="block text-[10px] text-gray-700">{{ \Carbon\Carbon::parse($lastCheckpoint->tanggal_checkpoint)->format('d-m-Y') }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-1 text-[10px]">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        {{ $permohonan->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-1 text-[10px] text-center">
                                    <div class="flex flex-col gap-1">
                                        <span class="inline-flex items-center text-xs">
                                            @if($permohonan->approved_by_system_1)
                                                <span class="w-2 h-2 bg-green-500 rounded-full mr-1"></span>
                                                <span class="text-green-700">Sys 1 ✓</span>
                                            @else
                                                <span class="w-2 h-2 bg-gray-300 rounded-full mr-1"></span>
                                                <span class="text-gray-500">Sys 1 ✗</span>
                                            @endif
                                        </span>
                                        <span class="inline-flex items-center text-xs">
                                            @if($permohonan->approved_by_system_2)
                                                <span class="w-2 h-2 bg-green-500 rounded-full mr-1"></span>
                                                <span class="text-green-700">Sys 2 ✓</span>
                                            @else
                                                <span class="w-2 h-2 bg-gray-300 rounded-full mr-1"></span>
                                                <span class="text-gray-500">Sys 2 ✗</span>
                                            @endif
                                        </span>
                                    </div>
                                </td>
                                <td class="px-2 py-1 text-sm font-medium text-center text-[10px]">
                                    @if($permohonan->approved_by_system_1 && !$permohonan->approved_by_system_2)
                                        <a href="{{ route('approval-ii.create', $permohonan) }}"
                                           class="inline-flex items-center justify-center px-2 py-1 bg-green-50 text-green-700 rounded text-xs font-medium hover:bg-green-100 transition">
                                            Setujui
                                        </a>
                                    @else
                                        <span class="inline-flex items-center justify-center px-2 py-1 bg-gray-100 text-gray-400 rounded text-xs font-medium cursor-not-allowed">
                                            Menunggu
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-1 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <p class="text-xs font-medium">Tidak ada tugas yang perlu disetujui saat ini</p>
                                        <p class="text-xs mt-1">Semua permohonan sudah diproses atau menunggu approval pertama</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="9" class="px-4 py-1 text-right">
                                @if(auth()->user()->can('approval-approve'))
                                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-md shadow transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 00-4-4H5a4 4 0 014-4h2a4 4 0 014 4v2a4 4 0 01-4 4H9z" /></svg>
                                    Setujui Masal
                                </button>
                                @endif
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </form>
        </div>
        <div class="mt-4">
            @include('components.modern-pagination', ['paginator' => $permohonans, 'routeName' => 'approval-ii.dashboard'])
        </div>
    </div>
</div>
@endsection

<style>
/* Sticky Table Header Styles */
.sticky-table-header {
    position: sticky;
    top: 0;
    z-index: 10;
    background-color: rgb(249 250 251); /* bg-gray-50 */
    box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
}

/* Enhanced table container for better scrolling */
.table-container {
    max-height: calc(100vh - 300px); /* Adjust based on your layout */
    overflow-y: auto;
    border: 1px solid rgb(229 231 235); /* border-gray-200 */
    border-radius: 0.5rem;
}

/* Smooth scrolling for better UX */
.table-container {
    scroll-behavior: smooth;
}

/* Table header cells need specific background to avoid transparency issues */
.sticky-table-header th {
    background-color: rgb(249 250 251) !important;
    border-bottom: 1px solid rgb(229 231 235);
}

/* Optional: Add a subtle border when scrolling */
.table-container.scrolled .sticky-table-header {
    border-bottom: 2px solid rgb(59 130 246); /* blue-500 */
}

/* Ensure dropdown menus appear above sticky header */
.relative.group .absolute {
    z-index: 20;
}

/* Enhanced Pagination Styles */
.pagination-links .page-link {
    @apply inline-flex items-center px-2.5 py-1.5 text-sm font-medium transition-colors duration-200 border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 hover:text-gray-900;
}

.pagination-links .page-link.active {
    @apply bg-blue-600 border-blue-600 text-white hover:bg-blue-700 hover:border-blue-700;
}

.pagination-links .page-link.disabled {
    @apply opacity-50 cursor-not-allowed pointer-events-none;
}

.pagination-links .page-item:first-child .page-link {
    @apply rounded-l-md;
}

.pagination-links .page-item:last-child .page-link {
    @apply rounded-r-md;
}

.pagination-links .page-item:not(:first-child):not(:last-child) .page-link {
    @apply border-l-0;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sticky Header Enhancement
    const tableContainer = document.querySelector('.table-container');
    const stickyHeader = document.querySelector('.sticky-table-header');

    if (tableContainer && stickyHeader) {
        // Add scroll event listener for visual feedback
        tableContainer.addEventListener('scroll', function() {
            if (tableContainer.scrollTop > 0) {
                tableContainer.classList.add('scrolled');
            } else {
                tableContainer.classList.remove('scrolled');
            }
        });

        // Optional: Add smooth scroll to top button
        const scrollToTopBtn = document.createElement('button');
        scrollToTopBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
        scrollToTopBtn.className = 'fixed bottom-4 right-4 bg-blue-600 text-white p-3 rounded-full shadow-lg hover:bg-blue-700 transition-all duration-300 opacity-0 invisible z-50';
        scrollToTopBtn.title = 'Scroll ke atas';
        document.body.appendChild(scrollToTopBtn);

        // Show/hide scroll to top button
        tableContainer.addEventListener('scroll', function() {
            if (tableContainer.scrollTop > 200) {
                scrollToTopBtn.classList.remove('opacity-0', 'invisible');
                scrollToTopBtn.classList.add('opacity-100', 'visible');
            } else {
                scrollToTopBtn.classList.add('opacity-0', 'invisible');
                scrollToTopBtn.classList.remove('opacity-100', 'visible');
            }
        });

        // Scroll to top functionality
        scrollToTopBtn.addEventListener('click', function() {
            tableContainer.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
});
</script>
