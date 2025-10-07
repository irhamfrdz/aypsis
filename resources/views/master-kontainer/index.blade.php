@extends('layouts.app')

@section('title','Master Kontainer Sewa')
@section('page_title','Master Kontainer Sewa')

@section('content')

<h2 class="text-xl font-bold text-gray-800 mb-4">Daftar Kontainer Sewa</h2>

<div class="mb-4 flex justify-between items-center">
    <div class="flex space-x-3">
        <!-- Download Template Button -->
        <a href="{{ route('master.kontainer.download-template') }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Download Template
        </a>

        <!-- Import Button -->
        <button onclick="openImportModal()"
                class="inline-flex items-center px-4 py-2 border border-green-600 text-sm font-medium rounded-md shadow-sm text-green-600 bg-white hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
            </svg>
            Import CSV
        </button>
    </div>

    <div>
        <a href="{{ route('master.kontainer.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Tambah Kontainer Baru
        </a>
    </div>
</div>

@if (session('success'))
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-md mb-4" role="alert">
    {{session('success')}}
</div>
@endif

@if (session('warning'))
<div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-md mb-4" role="alert">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
        </div>
        <div class="ml-3">
            <p class="text-sm font-medium">{{session('warning')}}</p>
        </div>
    </div>
</div>
@endif

{{-- Rows Per Page Selection --}}
@include('components.rows-per-page', [
    'routeName' => 'master.kontainer.index',
    'paginator' => $kontainers,
    'entityName' => 'kontainer',
    'entityNamePlural' => 'kontainer'
])

<div class="overflow-x-auto shadow-md sm:rounded-lg table-container">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="sticky-table-header bg-gray-50 sticky top-0 z-10 shadow-sm">
            <tr>
                <th scope="col" class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Nomor Kontainer
                </th>

                <th scope="col" class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Ukuran
                </th>

                <th scope="col" class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Tipe
                </th>

                <th scope="col" class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status
                </th>

                <th scope="col" class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Aksi
                </th>
            </tr>
        </thead>

        <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($kontainers as $kontainer )
            <tr>
                <td class="px-4 py-2 whitespace-nowrap text-center">
                    <div class="text-sm font-medium text-gray-900">{{$kontainer->nomor_seri_gabungan}}</div>
                </td>

                <td class="px-4 py-2 whitespace-nowrap text-center">
                    <div class="text-sm text-gray-500">{{$kontainer->ukuran}}</div>
                </td>

                <td class="px-4 py-2 whitespace-nowrap text-center">
                    <div class="text-sm text-gray-500">{{$kontainer->tipe_kontainer}}</div>
                </td>

                <td class="px-4 py-2 whitespace-nowrap text-center">
                    @php
                        // Normalize status dengan dukungan untuk 'active'/'inactive'
                        $displayStatus = 'Tersedia'; // Default
                        $statusClass = 'bg-green-100 text-green-800'; // Default: Hijau untuk Tersedia

                        // Jika status menunjukkan sedang digunakan, maka "Disewa"
                        if (in_array($kontainer->status, ['Disewa', 'Digunakan', 'rented'])) {
                            $displayStatus = 'Disewa';
                            $statusClass = 'bg-yellow-100 text-yellow-800'; // Kuning untuk Disewa
                        }
                        // Jika status inactive, maka "Nonaktif"
                        elseif ($kontainer->status === 'inactive') {
                            $displayStatus = 'Nonaktif';
                            $statusClass = 'bg-red-100 text-red-800'; // Merah untuk Nonaktif
                        }
                        // Semua status lainnya dianggap "Tersedia"
                        // (Tersedia, available, active, dikembalikan, dll)
                    @endphp
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                        {{ $displayStatus }}
                    </span>
                </td>

                <td class="px-4 py-2 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex items-center justify-end space-x-3 text-[10px]">
                        <a href="{{route('master.kontainer.edit',$kontainer->id)}}" class="text-blue-600 hover:text-blue-800 hover:underline font-medium" title="Edit Data">Edit</a>
                        <span class="text-gray-300">|</span>
                        <form action="{{route('master.kontainer.destroy',$kontainer->id)}}" method="POST" class="inline-block" onsubmit="return confirm('Apakah anda yakin ingin menghapus kontainer ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 hover:underline font-medium cursor-pointer border-none bg-transparent p-0" title="Hapus Data">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-4 py-2 text-center text-sm text-gray-500">Tidak ada data kontainer.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Modern Pagination Design --}}
@include('components.modern-pagination', ['paginator' => $kontainers, 'routeName' => 'master.kontainer.index'])

{{-- Import Modal --}}
<div id="importModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeImportModal()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="importForm" action="{{ route('master.kontainer.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Import Data Kontainer
                            </h3>
                            <div class="mt-4">
                                <p class="text-sm text-gray-500 mb-4">
                                    Upload file CSV untuk mengimpor data kontainer secara bulk.
                                </p>

                                <div class="mb-4">
                                    <label for="excel_file" class="block text-sm font-medium text-gray-700 mb-2">
                                        Pilih File CSV
                                    </label>
                                    <input type="file"
                                           id="excel_file"
                                           name="excel_file"
                                           accept=".csv"
                                           required
                                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                </div>

                                <div class="bg-blue-50 border border-blue-200 rounded-md p-3">
                                    <div class="flex">
                                        <svg class="h-5 w-5 text-blue-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                        </svg>
                                        <div class="ml-3">
                                            <h4 class="text-sm font-medium text-blue-800">Format File CSV:</h4>
                                            <div class="mt-1 text-sm text-blue-700">
                                                <ul class="list-disc pl-5 space-y-1">
                                                    <li>Kolom 1: Nomor Seri Gabungan</li>
                                                    <li>Kolom 2: Ukuran (20, 40)</li>
                                                    <li>Kolom 3: Tipe Kontainer (DRY, REEFER)</li>
                                                    <li>Kolom 4: Vendor/Pemilik</li>
                                                    <li>Kolom 5: Status (Tersedia/Disewa)</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                        <span class="upload-text">Upload & Import</span>
                        <span class="upload-loading hidden">
                            <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        </span>
                    </button>
                    <button type="button"
                            onclick="closeImportModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

<script>
// Import Modal Functions
function openImportModal() {
    document.getElementById('importModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeImportModal() {
    document.getElementById('importModal').classList.add('hidden');
    document.body.style.overflow = 'auto';

    // Reset form
    document.getElementById('importForm').reset();

    // Reset button state
    const submitBtn = document.querySelector('#importForm button[type="submit"]');
    const uploadText = submitBtn.querySelector('.upload-text');
    const uploadLoading = submitBtn.querySelector('.upload-loading');

    uploadText.classList.remove('hidden');
    uploadLoading.classList.add('hidden');
    submitBtn.disabled = false;
}

// Handle form submission
document.getElementById('importForm').addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    const uploadText = submitBtn.querySelector('.upload-text');
    const uploadLoading = submitBtn.querySelector('.upload-loading');

    // Show loading state
    uploadText.classList.add('hidden');
    uploadLoading.classList.remove('hidden');
    submitBtn.disabled = true;
});

// Close modal when clicking outside
document.getElementById('importModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeImportModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !document.getElementById('importModal').classList.contains('hidden')) {
        closeImportModal();
    }
});

// File input validation
document.getElementById('excel_file').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const fileSize = file.size / 1024 / 1024; // Size in MB
        const allowedTypes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'];

        if (fileSize > 5) {
            alert('File terlalu besar! Maksimal 5MB.');
            e.target.value = '';
            return;
        }

        if (file.type !== 'text/csv' && !file.name.endsWith('.csv')) {
            alert('Format file tidak didukung! Gunakan file .csv');
            e.target.value = '';
            return;
        }
    }
});
</script>

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
</style>
