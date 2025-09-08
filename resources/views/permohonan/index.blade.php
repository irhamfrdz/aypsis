
@extends('layouts.app')

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
                <a href="{{ route('permohonan.create') }}" class="inline-flex items-center px-5 py-2 border border-transparent text-base font-semibold rounded-lg shadow text-white bg-indigo-600 hover:bg-indigo-700 transition">
                    + Tambah Permohonan
                </a>

                <a href="{{ route('permohonan.export') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Download CSV</a>

                <form action="{{ route('permohonan.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label class="inline-flex items-center px-4 py-2 bg-white border rounded cursor-pointer text-sm">
                        <input type="file" name="csv_file" accept=".csv,text/csv" class="hidden" onchange="this.form.submit()">
                        Import CSV
                    </label>
                </form>
            </div>
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

        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <form id="bulk-delete-form" action="{{ route('permohonan.bulk-delete') }}" method="POST">
                @csrf
                @method('DELETE')
                <table class="min-w-full divide-y divide-indigo-200 bg-white rounded-lg">
                    <thead class="bg-indigo-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">
                                <input type="checkbox" id="select-all" class="rounded border-indigo-300 text-indigo-600 focus:ring-indigo-500" onchange="toggleSelectAll()">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">Nomor Memo</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">Kegiatan</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">Supir</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">Tujuan</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">Total Biaya</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-indigo-700 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-indigo-100">
                        @forelse ($permohonans as $permohonan)
                            <tr class="hover:bg-indigo-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" name="selected_ids[]" value="{{ $permohonan->id }}" class="item-checkbox rounded border-indigo-300 text-indigo-600 focus:ring-indigo-500" onchange="updateBulkActions()">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-indigo-900 font-semibold">{{ $permohonan->nomor_memo }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-indigo-800">{{ $kegiatanMap[$permohonan->kegiatan] ?? $permohonan->kegiatan }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-indigo-800">{{ $permohonan->supir->nama_panggilan ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-indigo-800">{{ $permohonan->tujuan }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-indigo-900 font-bold">Rp. {{ number_format($permohonan->total_harga_setelah_adj, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <a href="{{ route('permohonan.show', $permohonan) }}" class="inline-block px-3 py-1 rounded bg-indigo-500 text-white hover:bg-indigo-700 transition shadow">Lihat</a>
                                    <a href="{{ route('permohonan.edit', $permohonan) }}" class="inline-block px-3 py-1 rounded bg-blue-500 text-white hover:bg-blue-700 transition shadow">Edit</a>
                                    <form action="{{ route('permohonan.destroy', $permohonan) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-block px-3 py-1 rounded bg-red-500 text-white hover:bg-red-700 transition shadow">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Tidak ada data permohonan yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </form>
        </div>
        <div class="mt-6">
            {{ $permohonans->links() }}
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
});
</script>
@endsection
