@extends('layouts.app')

@section('title', 'Detail Kontainer - ' . $namaGudang)
@section('page_title', 'Detail Kontainer per Gudang')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 py-6 sm:py-12 px-3 sm:px-4 lg:px-8">
    <div class="max-w-6xl mx-auto">
        
        <!-- Header -->
        <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white p-6 rounded-2xl shadow-sm border border-gray-100 gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Daftar Kontainer di {{ $namaGudang }}</h2>
                <p class="text-sm text-gray-500 mt-1">Menampilkan data gabungan dari Kontainer Sewa dan Stock Kontainer</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('master.kontainer.stock-pergudang.export', $id) }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-all shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export Excel
                </a>
                <button type="button" onclick="document.getElementById('uploadModal').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-all shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    Sync Kontainer
                </button>
                <a href="{{ route('master.kontainer.stock-pergudang') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Alerts -->
        @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-xl shadow-md animate-fade-in-down border border-green-100">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 p-1 rounded-full">
                    <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-bold text-green-900">{{ session('success') }}</p>
                </div>
                <button type="button" class="ml-auto bg-green-50 text-green-500 hover:text-green-700 p-1 rounded-lg transition-colors" onclick="this.parentElement.parentElement.remove()">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-xl shadow-md animate-fade-in-down border border-red-100">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-red-100 p-1 rounded-full">
                    <svg class="h-5 w-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-bold text-red-900">{{ session('error') }}</p>
                </div>
                <button type="button" class="ml-auto bg-red-50 text-red-500 hover:text-red-700 p-1 rounded-lg transition-colors" onclick="this.parentElement.parentElement.remove()">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </div>
        @endif

        @if($errors->any())
        <div class="mb-6 bg-orange-50 border-l-4 border-orange-500 p-4 rounded-xl shadow-md animate-fade-in-down border border-orange-100">
            <div class="flex items-start">
                <div class="flex-shrink-0 bg-orange-100 p-1 rounded-full">
                    <svg class="h-5 w-5 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-bold text-orange-900 mb-1">Terjadi kesalahan validasi:</h3>
                    <ul class="text-xs text-orange-800 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button type="button" class="ml-auto bg-orange-50 text-orange-500 hover:text-orange-700 p-1 rounded-lg transition-colors" onclick="this.parentElement.parentElement.remove()">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </div>
        @endif

        <!-- Warehouse Summary Card -->
        <div class="flex justify-center mb-6">
            <div style="background: linear-gradient(135deg, #16A34A 0%, #14532D 100%)" class="px-8 py-5 rounded-2xl shadow-md text-white min-w-[240px] text-center">
                <h3 class="text-xs font-bold uppercase tracking-wider opacity-80">Total Kontainer di Gudang</h3>
                <p class="text-3xl font-black mt-1">{{ $allContainers->count() }}</p>
                <div class="mt-2 h-1 w-12 bg-white/30 rounded mx-auto"></div>
            </div>
        </div>

        <!-- Table Card List -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-16">No.</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No. Kontainer</th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Ukuran</th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Tipe</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($allContainers as $item)
                        <tr class="hover:bg-gray-50/50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-500">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">{{ $item->nomor_seri_gabungan }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600">{{ $item->ukuran ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600">{{ $item->tipe_kontainer ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500 text-sm">
                                <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0l-4 4m-4-4l-4 4m6-12v12"></path>
                                </svg>
                                Tidak ada kontainer yang terdaftar di {{ $namaGudang }}.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- Upload/Sync Modal -->
<div id="uploadModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('uploadModal').classList.add('hidden')"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('master.kontainer.stock-pergudang.upload', $id) }}" method="POST" id="uploadForm" enctype="multipart/form-data">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">Sync Kontainer Gudang</h3>
                            <div class="mt-2 text-sm text-gray-500 italic bg-yellow-50 border-l-4 border-yellow-400 p-2 rounded">
                                * Kontainer yang saat ini terdaftar di gudang ini namun <strong>TIDAK ADA</strong> dalam list di bawah akan dihapus lokasinya.
                            </div>
                            
                            <div class="mt-4">
                                <div class="flex justify-between items-center mb-1">
                                    <label for="file_excel" class="block text-sm font-medium text-gray-700">Upload Excel (.xlsx, .xls, .csv)</label>
                                    <a href="{{ route('master.kontainer.stock-pergudang.template') }}" class="text-[10px] font-bold text-indigo-600 hover:text-indigo-800 bg-indigo-50 px-2 py-1 rounded-md border border-indigo-100 transition-colors">
                                        <i class="fas fa-download mr-1"></i> Download Template
                                    </a>
                                </div>
                                <input type="file" name="file_excel" id="file_excel" accept=".xlsx,.xls,.csv"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-gray-300 rounded-lg p-1">
                                <p class="mt-1 text-[10px] text-gray-400">Pastikan kolom pertama (A) berisi Nomor Kontainer.</p>
                            </div>

                            <div class="relative flex py-4 items-center">
                                <div class="flex-grow border-t border-gray-200"></div>
                                <span class="flex-shrink mx-4 text-gray-400 text-xs font-bold uppercase">ATAU</span>
                                <div class="flex-grow border-t border-gray-200"></div>
                            </div>

                            <div>
                                <label for="container_numbers" class="block text-sm font-medium text-gray-700 mb-1">List Nomor Kontainer (Manual)</label>
                                <textarea name="container_numbers" id="container_numbers" rows="8" 
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-xl"
                                    placeholder="Tempel list nomor kontainer di sini...&#10;Contoh:&#10;AYPU1234567&#10;AYPU7654321"></textarea>
                                <p class="mt-2 text-xs text-gray-400">Pisahkan dengan baris baru, koma, atau spasi.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                    <button type="submit" id="submitBtn" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm transition-all disabled:opacity-50">
                        Proses Sync
                    </button>
                    <button type="button" onclick="document.getElementById('uploadModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-all">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('uploadForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memproses...';
    });
</script>
@endsection
