
@extends('layouts.app')


@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('title', 'Master Permohonan')
@section('page_title', 'Daftar Permohonan')

@section('content')
<div class="space-y-8">

    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md shadow" role="alert">
            <p class="font-bold">Sukses</p>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-400 text-red-700 p-4 rounded-md shadow" role="alert">
            <p class="font-bold">Terjadi Kesalahan</p>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    @if(session('warning'))
        <div class="bg-yellow-50 border-l-4 border-yellow-400 text-yellow-700 p-4 rounded-md shadow" role="alert">
            <p class="font-bold">Peringatan</p>
            <p>{{ session('warning') }}</p>
        </div>
    @endif

    <!-- Daftar Permohonan -->
    <div class="bg-gradient-to-br from-indigo-50 via-white to-indigo-100 shadow-lg rounded-xl p-8 border border-indigo-200">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-indigo-800">Daftar Permohonan</h2>
            <div class="flex items-center space-x-3">
                <a href="{{ route('permohonan.create') }}" class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-semibold rounded-lg shadow text-white bg-indigo-600 hover:bg-indigo-700 transition">
                    + Tambah Permohonan
                </a>

                <a href="{{ route('permohonan.export') }}" class="inline-flex items-center px-3 py-1 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 text-sm">Download CSV</a>

                <form action="{{ route('permohonan.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label class="inline-flex items-center px-3 py-1 bg-white border rounded cursor-pointer text-xs">
                        <input type="file" name="csv_file" accept=".csv,text/csv" class="hidden" onchange="this.form.submit()">
                        Import CSV
                    </label>
                </form>
            </div>
        </div>

        <!-- Search Form -->
        <div class="mb-6 bg-white p-4 rounded-lg border border-indigo-200 shadow-sm">
            <h3 class="text-lg font-semibold text-indigo-800 mb-3">🔍 Pencarian Permohonan</h3>
            <form method="GET" action="{{ route('permohonan.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Search Query -->
                    <div class="lg:col-span-2">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari (Nomor Memo, Kegiatan, Vendor, Supir, Tujuan)</label>
                        <input type="text"
                               id="search"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Masukkan kata kunci..."
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    </div>

                    <!-- Date From -->
                    <div>
                        <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Dari</label>
                        <input type="date"
                               id="date_from"
                               name="date_from"
                               value="{{ request('date_from') }}"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    </div>

                    <!-- Date To -->
                    <div>
                        <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Sampai</label>
                        <input type="date"
                               id="date_to"
                               name="date_to"
                               value="{{ request('date_to') }}"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Kegiatan Filter -->
                    <div>
                        <label for="kegiatan_filter" class="block text-sm font-medium text-gray-700 mb-1">Kegiatan</label>
                        <select id="kegiatan_filter"
                                name="kegiatan"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            <option value="">Semua Kegiatan</option>
                            @if(isset($kegiatanList))
                                @foreach($kegiatanList as $kode => $nama)
                                    <option value="{{ $kode }}" {{ request('kegiatan') == $kode ? 'selected' : '' }}>
                                        {{ $nama }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label for="status_filter" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select id="status_filter"
                                name="status"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>

                    <!-- Amount Range -->
                    <div>
                        <label for="amount_min" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Minimum (Rp)</label>
                        <input type="number"
                               id="amount_min"
                               name="amount_min"
                               value="{{ request('amount_min') }}"
                               placeholder="0"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap items-center gap-2 pt-2">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition shadow text-sm">
                        🔍 Cari
                    </button>
                    <a href="{{ route('permohonan.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition shadow text-sm">
                        🔄 Reset
                    </a>
                    @if(request()->hasAny(['search', 'date_from', 'date_to', 'kegiatan', 'status', 'amount_min']))
                        <span class="text-sm text-green-600 font-medium">
                            📊 {{ $permohonans->total() }} hasil ditemukan
                        </span>
                    @endif
                </div>
            </form>
        </div>

        <!-- Quick Filters -->
        <div class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
            <h4 class="text-sm font-semibold text-gray-700 mb-3">⚡ Filter Cepat</h4>
            <div class="flex flex-wrap gap-2">
                <button onclick="quickFilterByDateRange(7)" class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs hover:bg-blue-200 transition">
                    📅 7 Hari Terakhir
                </button>
                <button onclick="quickFilterByDateRange(30)" class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs hover:bg-blue-200 transition">
                    📅 30 Hari Terakhir
                </button>
                <button onclick="quickFilterByStatus('pending')" class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs hover:bg-yellow-200 transition">
                    ⏳ Pending
                </button>
                <button onclick="quickFilterByStatus('approved')" class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs hover:bg-green-200 transition">
                    ✅ Approved
                </button>
                <button onclick="quickFilterByStatus('rejected')" class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs hover:bg-red-200 transition">
                    ❌ Rejected
                </button>
                @if(request()->hasAny(['search', 'date_from', 'date_to', 'kegiatan', 'status', 'amount_min']))
                    <a href="{{ route('permohonan.index') }}" class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs hover:bg-gray-200 transition">
                        🔄 Reset Semua
                    </a>
                @endif
            </div>
        </div>

        <!-- Filter Tanggal untuk Print -->
        <div class="mb-6 bg-white p-4 rounded-lg border border-indigo-200 shadow-sm">
            <h3 class="text-lg font-semibold text-indigo-800 mb-3">Print Berdasarkan Tanggal</h3>
            <form action="{{ route('permohonan.print.by-date') }}" method="GET" target="_blank" class="flex flex-wrap items-end gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                    <input type="date" id="start_date" name="start_date" value="{{ request('start_date', date('Y-m-01')) }}"
                           class="border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                    <input type="date" id="end_date" name="end_date" value="{{ request('end_date', date('Y-m-d')) }}"
                           class="border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition shadow">
                        🖨️ Print Berdasarkan Tanggal
                    </button>
                </div>
            </form>
        </div>

        <!-- Bulk Actions -->
        <div id="bulk-actions" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg hidden">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <span class="text-red-700 font-semibold" id="selected-count">0 item dipilih</span>
                    <button type="button" onclick="selectAll()" class="text-sm text-indigo-600 hover:text-indigo-800">Pilih Semua</button>
                    <button type="button" onclick="clearSelection()" class="text-sm text-gray-600 hover:text-gray-800">Batal Pilih</button>
                </div>
                <button type="button" onclick="bulkDelete()" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition shadow">
                    🗑️ Hapus Terpilih
                </button>
            </div>
        </div>

        {{-- Import errors/warnings (jika ada) --}}
        @if(session('import_errors'))
            <div class="mb-4 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                <p class="font-bold text-yellow-800">Beberapa baris gagal diimpor</p>
                <p class="text-sm text-yellow-800">Periksa baris yang dilaporkan di bawah. Pastikan file CSV menggunakan delimiter <strong>;</strong> dan kolom-kolom sesuai format: <code>nomor_memo;kegiatan;supir;tujuan;jumlah_kontainer;total_harga_setelah_adj</code>.</p>
                <ul class="list-disc ml-5 mt-2 text-sm text-yellow-800">
                    @foreach(session('import_errors') as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
                <p class="mt-2 text-xs text-gray-600">Tip: Jika Anda ingin contoh file, beri tahu saya dan saya akan buatkan template CSV.</p>
            </div>
        @endif

        <!-- Search Results Summary -->
        @if(request()->hasAny(['search', 'date_from', 'date_to', 'kegiatan', 'status', 'amount_min']))
            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-sm font-semibold text-blue-800">📊 Hasil Pencarian</h4>
                        <p class="text-xs text-blue-600">
                            Menampilkan {{ $permohonans->count() }} dari {{ $permohonans->total() }} permohonan
                            @if(request('search'))
                                untuk "<strong>{{ request('search') }}</strong>"
                            @endif
                        </p>
                    </div>
                    <div class="text-right">
                        @if($permohonans->total() > 0)
                            <p class="text-xs text-blue-600">
                                Halaman {{ $permohonans->currentPage() }} dari {{ $permohonans->lastPage() }}
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Active Filters Display -->
                <div class="mt-2 flex flex-wrap gap-1">
                    @if(request('search'))
                        <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                            🔍 {{ request('search') }}
                            <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="ml-1 text-blue-600 hover:text-blue-800">×</a>
                        </span>
                    @endif
                    @if(request('date_from') || request('date_to'))
                        <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                            📅
                            @if(request('date_from')){{ \Carbon\Carbon::parse(request('date_from'))->format('d/m/y') }}@endif
                            @if(request('date_from') && request('date_to')) - @endif
                            @if(request('date_to')){{ \Carbon\Carbon::parse(request('date_to'))->format('d/m/y') }}@endif
                            <a href="{{ request()->fullUrlWithQuery(['date_from' => null, 'date_to' => null]) }}" class="ml-1 text-blue-600 hover:text-blue-800">×</a>
                        </span>
                    @endif
                    @if(request('kegiatan'))
                        <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                            🏗️ {{ $kegiatanMap[request('kegiatan')] ?? request('kegiatan') }}
                            <a href="{{ request()->fullUrlWithQuery(['kegiatan' => null]) }}" class="ml-1 text-blue-600 hover:text-blue-800">×</a>
                        </span>
                    @endif
                    @if(request('status'))
                        <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                            📋 {{ ucfirst(request('status')) }}
                            <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" class="ml-1 text-blue-600 hover:text-blue-800">×</a>
                        </span>
                    @endif
                    @if(request('amount_min'))
                        <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                            💰 Min: Rp {{ number_format(request('amount_min'), 0, ',', '.') }}
                            <a href="{{ request()->fullUrlWithQuery(['amount_min' => null]) }}" class="ml-1 text-blue-600 hover:text-blue-800">×</a>
                        </span>
                    @endif
                </div>
            </div>
        @endif

        {{-- Rows Per Page Selection --}}
        @include('components.rows-per-page', [
            'routeName' => 'permohonan.index',
            'paginator' => $permohonans,
            'entityName' => 'permohonan',
            'entityNamePlural' => 'permohonan'
        ])

        <div class="overflow-auto max-h-96 rounded-lg border border-gray-200">
            <form id="bulk-delete-form" action="{{ route('permohonan.bulk-delete') }}" method="POST">
                @csrf
                @method('DELETE')
                <table class="min-w-full divide-y divide-indigo-200 bg-white rounded-lg">
                    <thead class="bg-indigo-100 sticky top-0 z-20 shadow-sm">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">
                                <input type="checkbox" id="select-all" class="rounded border-indigo-300 text-indigo-600 focus:ring-indigo-500" onchange="toggleSelectAll()">
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-indigo-700 uppercase tracking-wider">Nomor Memo</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-indigo-700 uppercase tracking-wider">Tanggal Memo</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-indigo-700 uppercase tracking-wider">Kegiatan</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-indigo-700 uppercase tracking-wider">Vendor</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-indigo-700 uppercase tracking-wider">Supir</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-indigo-700 uppercase tracking-wider">Dari - Ke</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-indigo-700 uppercase tracking-wider">Uang Jalan</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-indigo-700 uppercase tracking-wider">Adjustment</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-indigo-700 uppercase tracking-wider">Alasan Adjustment</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-indigo-700 uppercase tracking-wider">Total Biaya</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-indigo-700 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-indigo-100 text-[10px]">
                        @forelse ($permohonans as $permohonan)
                            <tr class="hover:bg-indigo-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" name="selected_ids[]" value="{{ $permohonan->id }}" class="item-checkbox rounded border-indigo-300 text-indigo-600 focus:ring-indigo-500" onchange="updateBulkActions()">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-indigo-900 font-semibold">{{ $permohonan->nomor_memo }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-indigo-800 text-center">{{ $permohonan->tanggal_memo ? \Carbon\Carbon::parse($permohonan->tanggal_memo)->format('d/m/Y') : '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-indigo-800">{{ $kegiatanMap[$permohonan->kegiatan] ?? $permohonan->kegiatan }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-indigo-800 text-center">{{ $permohonan->vendor_perusahaan ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-indigo-800 text-center">{{ $permohonan->supir->nama_panggilan ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-indigo-800">{{ $permohonan->dari ?? '-' }} - {{ $permohonan->ke ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-indigo-900">Rp. {{ number_format($permohonan->jumlah_uang_jalan, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-indigo-900">{{ $permohonan->adjustment ? 'Rp. ' . number_format($permohonan->adjustment, 0, ',', '.') : '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-indigo-800">{{ $permohonan->alasan_adjustment ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-indigo-900 font-bold">Rp. {{ number_format($permohonan->total_harga_setelah_adj, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-[10px] font-medium space-x-2">
                                    <a href="{{ route('permohonan.show', $permohonan) }}" class="inline-block px-3 py-1 rounded bg-indigo-500 text-white hover:bg-indigo-700 transition shadow">Lihat</a>
                                    <a href="{{ route('permohonan.edit', $permohonan) }}" class="inline-block px-3 py-1 rounded bg-blue-500 text-white hover:bg-blue-700 transition shadow">Edit</a><span class="text-gray-300">|</span>
                                    <!-- Audit Log Link -->
                                    <button type="button"
                                            onclick="showAuditLog('{{ get_class($permohonan) }}', '{{ $permohonan->id }}', '{{ $permohonan->nomor_permohonan }}')"
                                            class="text-purple-600 hover:text-purple-800 hover:underline font-medium cursor-pointer"
                                            title="Lihat Riwayat Perubahan">
                                        Riwayat
                                    </button>
                                    <span class="text-gray-300">|</span>
                                    <a href="{{ route('permohonan.print', $permohonan) }}" target="_blank" class="inline-block px-3 py-1 rounded bg-green-500 text-white hover:bg-green-700 transition shadow" title="Print Memo Surat Jalan">
                                        🖨️ Print
                                    </a>
                                    <form action="{{ route('permohonan.destroy', $permohonan) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-block px-3 py-1 rounded bg-red-500 text-white hover:bg-red-700 transition shadow">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="px-6 py-4 text-center text-[10px] text-gray-500">
                                    Tidak ada data permohonan yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </form>
        </div>
        <div class="mt-6">
            @include('components.modern-pagination', ['paginator' => $permohonans, 'routeName' => 'permohonan.index'])
        </div>
    </div>
</div>

<script>
function updateBulkActions() {
    const checkboxes = document.querySelectorAll('.item-checkbox');
    const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
    const bulkActions = document.getElementById('bulk-actions');
    const selectedCount = document.getElementById('selected-count');
    const selectAllCheckbox = document.getElementById('select-all');

    // Update selected count
    selectedCount.textContent = checkedBoxes.length + ' item dipilih';

    // Show/hide bulk actions
    if (checkedBoxes.length > 0) {
        bulkActions.classList.remove('hidden');
    } else {
        bulkActions.classList.add('hidden');
    }

    // Update select all checkbox state
    if (checkedBoxes.length === checkboxes.length && checkboxes.length > 0) {
        selectAllCheckbox.checked = true;
        selectAllCheckbox.indeterminate = false;
    } else if (checkedBoxes.length > 0) {
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = true;
    } else {
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = false;
    }
}

function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.item-checkbox');

    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });

    updateBulkActions();
}

function selectAll() {
    const checkboxes = document.querySelectorAll('.item-checkbox');
    const selectAllCheckbox = document.getElementById('select-all');

    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
    selectAllCheckbox.checked = true;

    updateBulkActions();
}

function clearSelection() {
    const checkboxes = document.querySelectorAll('.item-checkbox');
    const selectAllCheckbox = document.getElementById('select-all');

    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    selectAllCheckbox.checked = false;

    updateBulkActions();
}

function bulkDelete() {
    const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');

    if (checkedBoxes.length === 0) {
        alert('Pilih item yang akan dihapus terlebih dahulu.');
        return;
    }

    const confirmMsg = `Apakah Anda yakin ingin menghapus ${checkedBoxes.length} memo permohonan yang dipilih?\n\nPerhatian: Aksi ini tidak dapat dibatalkan!`;

    if (confirm(confirmMsg)) {
        // Collect memo numbers for confirmation
        const memoNumbers = [];
        checkedBoxes.forEach(checkbox => {
            const row = checkbox.closest('tr');
            const memoCell = row.querySelector('td:nth-child(2)'); // Nomor memo column
            if (memoCell) {
                memoNumbers.push(memoCell.textContent.trim());
            }
        });

        const finalConfirm = `Memo yang akan dihapus:\n${memoNumbers.join(', ')}\n\nLanjutkan?`;

        if (confirm(finalConfirm)) {
            document.getElementById('bulk-delete-form').submit();
        }
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateBulkActions();
    initializeSearchFeatures();
});

// Search Enhancement Functions
function initializeSearchFeatures() {
    const searchInput = document.getElementById('search');
    const searchForm = searchInput.closest('form');
    let searchTimeout;

    // Auto-submit search with debounce (optional - remove if not wanted)
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                // Only auto-submit if there's a search term or if clearing
                const searchValue = searchInput.value.trim();
                if (searchValue.length >= 3 || searchValue.length === 0) {
                    searchForm.submit();
                }
            }, 500); // Wait 500ms after user stops typing
        });
    }

    // Clear search functionality
    const resetButton = document.querySelector('a[href*="permohonan.index"]:not([href*="?"])');
    if (resetButton) {
        resetButton.addEventListener('click', function(e) {
            e.preventDefault();
            // Clear all form inputs
            const inputs = searchForm.querySelectorAll('input, select');
            inputs.forEach(input => {
                if (input.type === 'text' || input.type === 'date' || input.type === 'number') {
                    input.value = '';
                } else if (input.tagName === 'SELECT') {
                    input.selectedIndex = 0;
                }
            });
            // Redirect to clean URL
            window.location.href = '{{ route("permohonan.index") }}';
        });
    }

    // Highlight search terms in results (optional enhancement)
    highlightSearchTerms();
}

function highlightSearchTerms() {
    const searchTerm = '{{ request("search") }}';
    if (!searchTerm) return;

    const tableRows = document.querySelectorAll('tbody tr');
    tableRows.forEach(row => {
        const cells = row.querySelectorAll('td');
        cells.forEach(cell => {
            if (cell.innerHTML && !cell.querySelector('input, button, form')) {
                const regex = new RegExp(`(${searchTerm})`, 'gi');
                cell.innerHTML = cell.innerHTML.replace(regex, '<mark class="bg-yellow-200 px-1 rounded">$1</mark>');
            }
        });
    });
}

// Quick filter functions
function quickFilterByStatus(status) {
    const statusSelect = document.getElementById('status_filter');
    statusSelect.value = status;
    statusSelect.closest('form').submit();
}

function quickFilterByDateRange(days) {
    const today = new Date();
    const startDate = new Date();
    startDate.setDate(today.getDate() - days);

    document.getElementById('date_from').value = startDate.toISOString().split('T')[0];
    document.getElementById('date_to').value = today.toISOString().split('T')[0];

    document.getElementById('date_from').closest('form').submit();
}
</script>

<!-- Audit Log Modal -->
@include('components.audit-log-modal')

@endsection
