@extends('layouts.app')

@section('title', 'Daftar Perbaikan Kontainer')
@section('page_title', 'Perbaikan Kontainer')

@section('content')
<div class="container mx-auto px-4 py-4 overflow-y-auto h-full pb-24">
    <!-- Breadcrumbs / Header -->
    <div class="mb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Daftar Perbaikan Kontainer</h1>
            <p class="text-xs text-gray-600">Kelola data perbaikan kontainer yang rusak dan dalam perawatan.</p>
        </div>
        <div>
            @can('perbaikan-kontainer-update')
            <a href="{{ route('perbaikan-kontainer.create') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors shadow-sm">
                <i class="fas fa-plus mr-2"></i>
                Tambah Perbaikan
            </a>
            @endcan
        </div>
    </div>

    <!-- Session Alert Message -->
    @if(session('success'))
        <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md shadow-sm" role="alert">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2 text-green-500 text-lg"></i>
                <span class="font-medium text-sm">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Filters Section -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 mb-6">
        <form action="{{ route('perbaikan-kontainer.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-7 gap-4">
                <!-- Search Input -->
                <div>
                    <label for="search" class="block text-xs font-semibold text-gray-500 mb-1">Cari</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </span>
                        <input type="text" name="search" id="search" 
                               value="{{ request('search') }}"
                               class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none" 
                               placeholder="No. Perbaikan / Kontainer">
                    </div>
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-xs font-semibold text-gray-500 mb-1">Status</label>
                    <select name="status" id="status" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending (Draft)</option>
                        <option value="proses" {{ request('status') === 'proses' ? 'selected' : '' }}>Proses Perbaikan</option>
                        <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="batal" {{ request('status') === 'batal' ? 'selected' : '' }}>Batal</option>
                    </select>
                </div>

                <!-- Status Pranota Filter -->
                <div>
                    <label for="status_pranota" class="block text-xs font-semibold text-gray-500 mb-1">Status Pranota</label>
                    <select name="status_pranota" id="status_pranota" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none">
                        <option value="all" {{ request('status_pranota') === 'all' ? 'selected' : '' }}>Semua</option>
                        <option value="Belum" {{ request('status_pranota', 'Belum') === 'Belum' ? 'selected' : '' }}>Belum Pranota</option>
                        <option value="Sudah" {{ request('status_pranota') === 'Sudah' ? 'selected' : '' }}>Sudah Pranota</option>
                    </select>
                </div>

                <!-- Vendor Filter -->
                <div>
                    <label for="vendor_bengkel_id" class="block text-xs font-semibold text-gray-500 mb-1">Bengkel / Vendor</label>
                    <select name="vendor_bengkel_id" id="vendor_bengkel_id" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none">
                        <option value="">Semua Bengkel</option>
                        @foreach($bengkels as $bengkel)
                            <option value="{{ $bengkel->id }}" {{ request('vendor_bengkel_id') == $bengkel->id ? 'selected' : '' }}>
                                {{ $bengkel->nama_bengkel }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Date Range Start -->
                <div>
                    <label for="tanggal_masuk_start" class="block text-xs font-semibold text-gray-500 mb-1">Tgl Masuk Mulai</label>
                    <input type="date" name="tanggal_masuk_start" id="tanggal_masuk_start" 
                           value="{{ request('tanggal_masuk_start') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none">
                </div>

                <!-- Date Range End -->
                <div>
                    <label for="tanggal_masuk_end" class="block text-xs font-semibold text-gray-500 mb-1">Tgl Masuk Selesai</label>
                    <input type="date" name="tanggal_masuk_end" id="tanggal_masuk_end" 
                           value="{{ request('tanggal_masuk_end') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none">
                </div>

                <!-- Per Page -->
                <div>
                    <label for="per_page" class="block text-xs font-semibold text-gray-500 mb-1">Baris per Halaman</label>
                    <select name="per_page" id="per_page" onchange="this.form.submit()"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none">
                        <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
            </div>

            <!-- Filter Buttons -->
            <div class="flex justify-end gap-2 border-t border-gray-100 pt-3">
                <a href="{{ route('perbaikan-kontainer.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <i class="fas fa-sync-alt mr-2"></i> Reset
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <i class="fas fa-filter mr-2"></i> Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Bulk Actions -->
    <div id="bulkActions" class="hidden mb-4 bg-indigo-50 border border-indigo-200 rounded-lg shadow-sm p-4 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="p-2 bg-indigo-100 rounded-full text-indigo-600">
                    <i class="fas fa-list text-lg"></i>
                </div>
                <div>
                    <span class="text-sm font-bold text-indigo-900"><span id="selected-count">0</span> Item Terpilih</span>
                    <p class="text-xs text-indigo-600">Pilih item untuk dimasukkan ke dalam pranota</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" id="btnBulkPranota" onclick="openPranotaModal()"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-all duration-200 flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Masukan ke Pranota
                </button>
                <button type="button" id="btnCancelSelection" onclick="clearSelection()"
                        class="px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-semibold rounded-lg shadow-sm transition-all duration-200">
                    Batal
                </button>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-4 py-3 text-center">
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)"
                                   class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        </th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">No. Perbaikan</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">No. Kontainer</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Ukuran & Tipe</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Bengkel</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tgl Masuk</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tgl Selesai</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Estimasi Biaya</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Biaya Riil</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Status</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($perbaikanKontainers as $perbaikan)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-4 whitespace-nowrap text-center">
                                <input type="checkbox" class="row-checkbox w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 disabled:opacity-50"
                                       value="{{ $perbaikan->id }}"
                                       data-no-perbaikan="{{ $perbaikan->no_perbaikan }}"
                                       data-no-kontainer="{{ $perbaikan->no_kontainer }}"
                                       data-ukuran="{{ $perbaikan->ukuran }}"
                                       data-tipe="{{ $perbaikan->tipe_kontainer }}"
                                       data-bengkel="{{ $perbaikan->bengkel->nama_bengkel ?? '-' }}"
                                       data-estimasi="{{ $perbaikan->estimasi_biaya }}"
                                       data-biaya-riil="{{ $perbaikan->biaya_riil }}"
                                       data-status="{{ $perbaikan->status }}"
                                       data-keterangan-kerusakan="{{ $perbaikan->keterangan_kerusakan }}"
                                       data-is-cat="{{ $perbaikan->is_cat ? 1 : 0 }}"
                                       data-biaya-cat="{{ $perbaikan->biaya_cat }}"
                                       data-vendor-cat="{{ $perbaikan->vendor_cat }}"
                                       data-jenis-cat="{{ $perbaikan->jenis_cat }}"
                                       data-status-pranota="{{ $perbaikan->status_pranota }}"
                                       onchange="updateBulkActions()">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-blue-600">
                                {{ $perbaikan->no_perbaikan }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                {{ $perbaikan->no_kontainer }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($perbaikan->ukuran)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $perbaikan->ukuran }}FT
                                    </span>
                                @endif
                                @if($perbaikan->tipe_kontainer)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-800 ml-1">
                                        {{ $perbaikan->tipe_kontainer }}
                                    </span>
                                @endif
                                @if(!$perbaikan->ukuran && !$perbaikan->tipe_kontainer)
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $perbaikan->bengkel->nama_bengkel ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $perbaikan->tanggal_masuk ? $perbaikan->tanggal_masuk->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $perbaikan->tanggal_keluar ? $perbaikan->tanggal_keluar->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                Rp {{ number_format($perbaikan->estimasi_biaya, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                                @if($perbaikan->biaya_riil > 0)
                                    Rp {{ number_format($perbaikan->biaya_riil, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex flex-col items-center gap-1">
                                    @php
                                        $badgeColor = match($perbaikan->status) {
                                            'pending' => 'bg-gray-100 text-gray-800',
                                            'proses' => 'bg-yellow-100 text-yellow-800',
                                            'selesai' => 'bg-green-100 text-green-800',
                                            'batal' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800'
                                        };
                                        $statusLabel = match($perbaikan->status) {
                                            'pending' => 'Pending',
                                            'proses' => 'Proses',
                                            'selesai' => 'Selesai',
                                            'batal' => 'Batal',
                                            default => ucfirst($perbaikan->status)
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $badgeColor }}">
                                        {{ $statusLabel }}
                                    </span>
                                    @if($perbaikan->status_pranota === 'Sudah')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800 tracking-wide uppercase">
                                            Sudah Pranota
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-600 tracking-wide uppercase">
                                            Belum Pranota
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-1">
                                <a href="{{ route('perbaikan-kontainer.show', $perbaikan->id) }}" 
                                   class="text-blue-600 hover:text-blue-900 inline-flex items-center p-1" 
                                   title="Detail">
                                    <i class="fas fa-eye text-base"></i>
                                </a>
                                @can('perbaikan-kontainer-update')
                                <a href="{{ route('perbaikan-kontainer.edit', $perbaikan->id) }}" 
                                   class="text-indigo-600 hover:text-indigo-900 inline-flex items-center p-1" 
                                   title="Edit">
                                    <i class="fas fa-edit text-base"></i>
                                </a>
                                <button type="button" onclick="openBiayaRiilModal('{{ $perbaikan->id }}', '{{ $perbaikan->no_perbaikan }}', '{{ $perbaikan->biaya_riil }}')"
                                        class="text-emerald-600 hover:text-emerald-900 inline-flex items-center p-1" title="Input Biaya Riil">
                                    <i class="fas fa-money-bill-wave text-base"></i>
                                </button>
                                @endcan
                                @can('perbaikan-kontainer-delete')
                                <form action="{{ route('perbaikan-kontainer.destroy', $perbaikan->id) }}" 
                                      method="POST" 
                                      class="inline-block"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus data perbaikan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 p-1" title="Hapus">
                                        <i class="fas fa-trash-alt text-base"></i>
                                    </button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-6 py-10 text-center text-sm text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-tools text-gray-300 text-4xl mb-3"></i>
                                    <span class="font-medium text-gray-500">Tidak ada data perbaikan kontainer ditemukan.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($perbaikanKontainers->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $perbaikanKontainers->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Masuk Pranota -->
<div id="pranotaModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 transition-opacity duration-300">
    <div class="relative top-20 mx-auto p-8 border w-11/12 max-w-2xl shadow-2xl rounded-2xl bg-white">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                <h3 class="text-xl font-bold text-gray-800">Masukan ke Pranota</h3>
                <button type="button" onclick="closePranotaModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="mt-6">
                <p class="text-sm text-gray-600 mb-6">Berikut adalah data perbaikan kontainer yang akan dimasukkan ke pranota.</p>

                <form id="pranotaForm">
                    <div class="mb-6">
                        <label for="nomor_pranota" class="block text-sm font-semibold text-gray-700 mb-2">
                            Nomor Pranota <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-3">
                            <div class="relative flex-1">
                                <input type="text" id="nomor_pranota" name="nomor_pranota" required readonly
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-700 font-medium"
                                       placeholder="Loading nomor pranota...">
                            </div>
                            <button type="button" onclick="generateNomorPranota()"
                                    class="px-5 py-3 bg-green-500 hover:bg-green-600 text-white rounded-xl transition-all shadow-sm hover:shadow-md"
                                    title="Generate nomor baru">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Format: PTP-BL-TH-000001 (auto-generate)</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="tanggal_pranota" class="block text-sm font-semibold text-gray-700 mb-2">
                                Tanggal <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="tanggal_pranota" name="tanggal_pranota" required
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-700"
                                   value="{{ date('Y-m-d') }}">
                        </div>
                        <div>
                            <label for="vendor_pranota" class="block text-sm font-semibold text-gray-700 mb-2">
                                Vendor
                            </label>
                            <input type="text" id="vendor_pranota" name="vendor"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-700"
                                   placeholder="Nama vendor...">
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="keterangan_pranota" class="block text-sm font-semibold text-gray-700 mb-2">
                            Keterangan
                        </label>
                        <textarea id="keterangan_pranota" name="keterangan" rows="2"
                                  class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-700"
                                  placeholder="Keterangan..."></textarea>
                    </div>

                    <div class="overflow-x-auto rounded-xl border border-gray-200 mb-6">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase">NO</th>
                                    <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase">No. Perbaikan</th>
                                    <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase">No. Kontainer</th>
                                    <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase">Bengkel</th>
                                    <th class="px-4 py-3 text-right font-bold text-gray-500 uppercase">Biaya Perbaikan</th>
                                    <th class="px-4 py-3 text-right font-bold text-gray-500 uppercase">Biaya Cat</th>
                                    <th class="px-4 py-3 text-right font-bold text-gray-500 uppercase">Total Biaya</th>
                                </tr>
                            </thead>
                            <tbody id="pranota-items" class="bg-white divide-y divide-gray-100"></tbody>
                        </table>
                    </div>

                    <div class="flex flex-col gap-2 mb-6 border-t border-gray-100 pt-4">
                        <div class="flex justify-between text-sm font-medium text-gray-600">
                            <span>Total item dipilih:</span>
                            <span id="total-count-display" class="font-bold text-gray-900">0</span>
                        </div>
                        <div class="flex justify-between text-sm font-medium text-gray-600">
                            <span>Total Biaya Perbaikan:</span>
                            <span id="total-biaya-perbaikan-display" class="font-bold text-gray-900">Rp 0</span>
                        </div>
                        <div class="flex justify-between text-sm font-medium text-gray-600">
                            <span>Total Biaya Cat:</span>
                            <span id="total-biaya-cat-display" class="font-bold text-gray-900">Rp 0</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold text-gray-900 mt-2 border-t border-gray-100 pt-2">
                            <span>Total Biaya Gabungan:</span>
                            <span id="total-biaya-display" class="text-indigo-600">Rp 0</span>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="adjustment" class="block text-sm font-semibold text-gray-700 mb-2">
                            Adjustment (Opsional)
                        </label>
                        <input type="number" id="adjustment" name="adjustment"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-700"
                               placeholder="Nilai adjustment (bisa negatif)...">
                    </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="flex justify-end gap-3 pt-6 border-t border-gray-100 mt-8">
                <button type="button" onclick="closePranotaModal()"
                        class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-xl transition-all">
                    Batal
                </button>
                <button type="button" id="btnConfirmPranota"
                        class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition-all shadow-md hover:shadow-lg flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Konfirmasi Masuk Pranota
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Input Biaya Riil -->
<div id="biayaRiilModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 transition-opacity duration-300">
    <div class="relative top-40 mx-auto p-6 border w-full max-w-md shadow-2xl rounded-2xl bg-white">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-800">Input Biaya Riil</h3>
            <button type="button" onclick="closeBiayaRiilModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="mt-4">
            <p class="text-sm text-gray-600 mb-4">No. Perbaikan: <strong id="biayaRiilNoPerbaikan" class="text-gray-900"></strong></p>
            <form id="biayaRiilForm">
                <div class="mb-4">
                    <label for="biaya_riil_input" class="block text-sm font-semibold text-gray-700 mb-2">Biaya Riil <span class="text-red-500">*</span></label>
                    <input type="number" id="biaya_riil_input" name="biaya_riil" required min="0" step="0.01"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-gray-700"
                           placeholder="Masukkan biaya riil...">
                </div>
            </form>
        </div>
        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 mt-4">
            <button type="button" onclick="closeBiayaRiilModal()"
                    class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold rounded-xl transition-all">
                Batal
            </button>
            <button type="button" id="btnSimpanBiayaRiil"
                    class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl transition-all shadow-sm flex items-center">
                <i class="fas fa-save mr-2"></i>
                Simpan
            </button>
        </div>
    </div>
</div>

<script>
    function getSelectedCheckboxes() {
        return document.querySelectorAll('.row-checkbox:checked');
    }

    function updateBulkActions() {
        const checked = getSelectedCheckboxes();
        const bulkEl = document.getElementById('bulkActions');
        const countEl = document.getElementById('selected-count');

        if (checked.length > 0) {
            bulkEl.classList.remove('hidden');
            countEl.textContent = checked.length;
        } else {
            bulkEl.classList.add('hidden');
        }
    }

    function toggleSelectAll(master) {
        document.querySelectorAll('.row-checkbox:not(:disabled)').forEach(cb => cb.checked = master.checked);
        updateBulkActions();
    }

    function clearSelection() {
        document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = false);
        document.getElementById('selectAll').checked = false;
        updateBulkActions();
    }

    function openPranotaModal() {
        const tbody = document.getElementById('pranota-items');
        tbody.innerHTML = '';

        const checked = getSelectedCheckboxes();
        let totalBiaya = 0;
        let totalBiayaPerbaikan = 0;
        let totalBiayaCat = 0;
        let count = 0;
        const bengkels = new Set();

        checked.forEach(cb => {
            if (cb.dataset.bengkel) bengkels.add(cb.dataset.bengkel);
            if (cb.dataset.isCat === '1' && cb.dataset.vendorCat) bengkels.add(cb.dataset.vendorCat);
            count++;
            const no_perbaikan = cb.dataset.noPerbaikan;
            const no_kontainer = cb.dataset.noKontainer;
            const bengkel = cb.dataset.bengkel;
            const estimasi = parseFloat(cb.dataset.estimasi) || 0;
            const biayaRiil = parseFloat(cb.dataset.biayaRiil) || 0;
            
            const isCat = cb.dataset.isCat === '1';
            const biayaCat = isCat ? (parseFloat(cb.dataset.biayaCat) || 0) : 0;
            const vendorCat = cb.dataset.vendorCat;
            const jenisCat = cb.dataset.jenisCat;

            const biayaPerbaikan = (biayaRiil > 0 ? biayaRiil : estimasi);
            const biaya = biayaPerbaikan + biayaCat;
            
            totalBiayaPerbaikan += biayaPerbaikan;
            totalBiayaCat += biayaCat;
            totalBiaya += biaya;

            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="px-4 py-3 whitespace-nowrap text-gray-500">${count}</td>
                <td class="px-4 py-3 whitespace-nowrap font-semibold text-gray-900">
                    ${no_perbaikan}
                    ${isCat ? `<span class="block text-[10px] text-blue-600 font-semibold mt-0.5"><i class="fas fa-paint-roller mr-1"></i>Cat: ${jenisCat === 'cat_full' ? 'Full' : 'Sebagian'} (${vendorCat})</span>` : ''}
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-gray-700">${no_kontainer}</td>
                <td class="px-4 py-3 whitespace-nowrap text-gray-700">${bengkel}</td>
                <td class="px-4 py-3 whitespace-nowrap text-right text-gray-700">Rp ${(biayaRiil > 0 ? biayaRiil : estimasi).toLocaleString('id-ID')}</td>
                <td class="px-4 py-3 whitespace-nowrap text-right text-gray-700">Rp ${biayaCat.toLocaleString('id-ID')}</td>
                <td class="px-4 py-3 whitespace-nowrap text-right font-semibold text-indigo-600">Rp ${biaya.toLocaleString('id-ID')}</td>
            `;
            tbody.appendChild(row);
        });

        document.getElementById('vendor_pranota').value = Array.from(bengkels).join(', ');

        document.getElementById('total-count-display').textContent = count;
        const totalDisplay = document.getElementById('total-biaya-display');
        totalDisplay.dataset.original = totalBiaya;
        totalDisplay.dataset.originalPerbaikan = totalBiayaPerbaikan;
        totalDisplay.dataset.originalCat = totalBiayaCat;
        updateTotalBiayaDisplay();
        document.getElementById('pranotaModal').classList.remove('hidden');
        generateNomorPranota();
    }

    function closePranotaModal() {
        document.getElementById('pranotaModal').classList.add('hidden');
    }

    function updateTotalBiayaDisplay() {
        const display = document.getElementById('total-biaya-display');
        const original = parseFloat(display.dataset.original || 0);
        const originalPerbaikan = parseFloat(display.dataset.originalPerbaikan || 0);
        const originalCat = parseFloat(display.dataset.originalCat || 0);
        const adj = parseFloat(document.getElementById('adjustment').value || 0);
        
        document.getElementById('total-biaya-perbaikan-display').textContent = `Rp ${originalPerbaikan.toLocaleString('id-ID')}`;
        document.getElementById('total-biaya-cat-display').textContent = `Rp ${originalCat.toLocaleString('id-ID')}`;
        
        const total = original + adj;
        display.textContent = `Rp ${total.toLocaleString('id-ID')}`;
    }

    document.getElementById('adjustment')?.addEventListener('input', updateTotalBiayaDisplay);

    function generateNomorPranota() {
        const input = document.getElementById('nomor_pranota');
        input.value = 'Generating...';

        fetch("{{ route('perbaikan-kontainer.generate-nomor-pranota') }}", {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    input.value = data.nomor_pranota;
                } else {
                    alert(data.message || 'Gagal generate nomor');
                    input.value = '';
                }
            })
            .catch(() => { input.value = 'Error'; });
    }

     document.getElementById('btnConfirmPranota')?.addEventListener('click', function() {
        const nomor = document.getElementById('nomor_pranota').value;
        const tanggal = document.getElementById('tanggal_pranota').value;
        const vendor = document.getElementById('vendor_pranota').value;
        const keterangan = document.getElementById('keterangan_pranota').value;
        const adj = document.getElementById('adjustment').value;

        if (!nomor || nomor === 'Generating...' || nomor === 'Error') {
            alert('Nomor pranota belum tersedia');
            return;
        }

        const checkedBoxes = getSelectedCheckboxes();
        let hasSudahPranota = false;
        checkedBoxes.forEach(cb => {
            if (cb.dataset.statusPranota === 'Sudah') {
                hasSudahPranota = true;
            }
        });

        if (hasSudahPranota) {
            alert('Transaksi sudah masuk pranota');
            return;
        }

        const items = [];
        checkedBoxes.forEach(cb => {
            items.push({
                id: cb.value,
                no_perbaikan: cb.dataset.noPerbaikan,
                no_kontainer: cb.dataset.noKontainer,
                ukuran: cb.dataset.ukuran,
                tipe: cb.dataset.tipe,
                bengkel: cb.dataset.bengkel,
                estimasi_biaya: cb.dataset.estimasi,
                biaya_riil: cb.dataset.biayaRiil,
                status: cb.dataset.status,
                keterangan_kerusakan: cb.dataset.keteranganKerusakan,
                is_cat: cb.dataset.isCat,
                biaya_cat: cb.dataset.biayaCat,
                vendor_cat: cb.dataset.vendorCat,
                jenis_cat: cb.dataset.jenisCat,
            });
        });

        const btn = this;
        btn.disabled = true;
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';

        fetch("{{ route('perbaikan-kontainer.masuk-pranota') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                nomor_pranota: nomor,
                tanggal_pranota: tanggal,
                vendor: vendor,
                keterangan: keterangan,
                adjustment: adj,
                items: items
            })
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert(data.message || 'Terjadi kesalahan');
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            })
            .catch(() => {
                alert('Terjadi kesalahan saat menghubungi server');
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            });
    });

    let biayaRiilEditId = null;

    function openBiayaRiilModal(id, noPerbaikan, currentBiaya) {
        biayaRiilEditId = id;
        document.getElementById('biayaRiilNoPerbaikan').textContent = noPerbaikan;
        document.getElementById('biaya_riil_input').value = currentBiaya || '';
        document.getElementById('biayaRiilModal').classList.remove('hidden');
    }

    function closeBiayaRiilModal() {
        document.getElementById('biayaRiilModal').classList.add('hidden');
        biayaRiilEditId = null;
    }

    document.getElementById('btnSimpanBiayaRiil')?.addEventListener('click', function() {
        const biaya = document.getElementById('biaya_riil_input').value;
        if (biaya === '' || parseFloat(biaya) < 0) {
            alert('Masukkan biaya riil yang valid');
            return;
        }

        const btn = this;
        btn.disabled = true;
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';

        fetch("{{ route('perbaikan-kontainer.update-biaya-riil') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                id: biayaRiilEditId,
                biaya_riil: biaya
            })
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Gagal menyimpan');
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            })
            .catch(() => {
                alert('Terjadi kesalahan');
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            });
    });

    window.addEventListener('click', function(event) {
        const pranotaModal = document.getElementById('pranotaModal');
        if (event.target === pranotaModal) {
            closePranotaModal();
        }
        const biayaModal = document.getElementById('biayaRiilModal');
        if (event.target === biayaModal) {
            closeBiayaRiilModal();
        }
    });
</script>
@endsection
