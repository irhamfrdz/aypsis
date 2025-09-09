@extends('layouts.app')

@section('title', 'Master Karyawan')
@section('page_title', 'Master Karyawan')

@section('content')
<div class="max-w-full mx-auto px-4">
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <!-- Header Section -->
        <div class="px-6 py-4 border-b bg-white">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <h2 class="text-xl font-semibold text-gray-900">Daftar Karyawan</h2>
                
                <!-- Search Box -->
                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                    <form method="GET" action="{{ route('master.karyawan.index') }}" class="flex-1 sm:flex-initial">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}" 
                                   class="block w-full sm:w-80 pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm" 
                                   placeholder="Cari nama, NIK, divisi, pekerjaan..."
                                   autocomplete="off">
                            @if(request('search'))
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <a href="{{ route('master.karyawan.index') }}" class="text-gray-400 hover:text-gray-600">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </form>
                    
                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('master.karyawan.create') }}" class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded transition duration-150">
                            <i class="fas fa-plus mr-2"></i>Tambah Karyawan
                        </a>
                        <div class="relative group">
                            <a href="{{ route('master.karyawan.template') }}" class="inline-flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded transition duration-150">
                                <i class="fas fa-download mr-2"></i>Template CSV
                            </a>
                            <!-- Tooltip -->
                            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-64 p-3 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none z-50">
                                <div class="text-center">
                                    <div class="font-semibold mb-1">Template CSV Import</div>
                                    <div>Download file template dengan contoh data untuk memudahkan import karyawan baru</div>
                                </div>
                                <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-l-transparent border-r-transparent border-t-gray-900"></div>
                            </div>
                        </div>
                        <a href="{{ route('master.karyawan.print') }}" target="_blank" class="inline-flex items-center px-3 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded transition duration-150">
                            <i class="fas fa-print mr-2"></i>Cetak Semua
                        </a>
                        <a href="{{ route('master.karyawan.export') }}?sep=%3B" class="inline-flex items-center px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded transition duration-150">
                            <i class="fas fa-file-csv mr-2"></i>Export CSV
                        </a>
                        <a href="{{ route('master.karyawan.import') }}" class="inline-flex items-center px-3 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded transition duration-150">
                            <i class="fas fa-upload mr-2"></i>Import CSV
                        </a>
                    </div>
                </div>
            </div>
            
            @if(request('search'))
                <div class="mt-3 flex items-center text-sm text-gray-600">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Menampilkan hasil pencarian untuk: <strong>"{{ request('search') }}"</strong>
                    <a href="{{ route('master.karyawan.index') }}" class="ml-2 text-blue-600 hover:text-blue-800 underline">
                        Hapus filter
                    </a>
                </div>
            @endif
        </div>

        @if (session('success'))
            <div class="mx-6 mt-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded" role="alert">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-green-400 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <div class="flex-1">
                        {!! nl2br(e(session('success'))) !!}
                    </div>
                </div>
            </div>
        @endif

        @if (session('warning'))
            <div class="mx-6 mt-4 bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded" role="alert">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-yellow-400 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div class="flex-1">
                        <div class="font-medium mb-1">Import Selesai dengan Peringatan</div>
                        <div class="text-sm">
                            {!! nl2br(e(session('warning'))) !!}
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mx-6 mt-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded" role="alert">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-red-400 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <div class="flex-1">
                        <div class="font-medium mb-1">Import Gagal</div>
                        <div class="text-sm">
                            {!! nl2br(e(session('error'))) !!}
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Quick Info for CSV Import -->
        <div class="mx-6 mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-600 mt-0.5"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-medium text-blue-900 mb-1">Cara Import Data Karyawan:</h3>
                    <div class="text-sm text-blue-800">
                        <span class="inline-flex items-center bg-blue-100 text-blue-800 px-2 py-0.5 rounded text-xs font-medium mr-2">1</span>
                        <a href="{{ route('master.karyawan.template') }}" class="text-blue-600 hover:text-blue-800 underline font-medium">Download Template CSV</a>
                        <span class="mx-2">→</span>
                        <span class="inline-flex items-center bg-blue-100 text-blue-800 px-2 py-0.5 rounded text-xs font-medium mr-2">2</span>
                        Isi data karyawan
                        <span class="mx-2">→</span>
                        <span class="inline-flex items-center bg-blue-100 text-blue-800 px-2 py-0.5 rounded text-xs font-medium mr-2">3</span>
                        <a href="{{ route('master.karyawan.import') }}" class="text-blue-600 hover:text-blue-800 underline font-medium">Upload file CSV</a>
                    </div>
                </div>
                <button onclick="this.parentElement.parentElement.style.display='none'" class="flex-shrink-0 text-blue-400 hover:text-blue-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <!-- Table Section -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIK</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NAMA LENGKAP</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NAMA PANGGILAN</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DIVISI</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PEKERJAAN</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">JKN</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">BP JAMSOSTEK</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NO HP</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">EMAIL</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">STATUS PAJAK</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TANGGAL MASUK</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">AKSI</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($karyawans as $karyawan)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ strtoupper($karyawan->nik) }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ strtoupper($karyawan->nama_lengkap) }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ strtoupper($karyawan->nama_panggilan) }}</td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-md
                                    {{ strtolower($karyawan->divisi) === 'it' ? 'bg-blue-100 text-blue-800' : 
                                       (strtolower($karyawan->divisi) === 'abk' ? 'bg-blue-100 text-blue-800' : 
                                       (strtolower($karyawan->divisi) === 'supir' ? 'bg-gray-100 text-gray-800' : 
                                       'bg-gray-100 text-gray-800')) }}">
                                    {{ strtoupper($karyawan->divisi) }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ strtoupper($karyawan->pekerjaan) }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ strtoupper($karyawan->jkn ?? '-') }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ strtoupper($karyawan->no_ketenagakerjaan ?? '-') }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ strtoupper($karyawan->no_hp) }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ $karyawan->email ?? '-' }}</td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-md
                                    {{ 
                                        strtolower($karyawan->status_pajak ?? '') === 'pkp' ? 'bg-red-100 text-red-800' : 
                                        (preg_match('/^(k|tk)/i', $karyawan->status_pajak ?? '') ? 'bg-blue-100 text-blue-800' : 
                                        (strtolower($karyawan->status_pajak ?? '') === 'ptkp' ? 'bg-yellow-100 text-yellow-800' : 
                                        'bg-gray-100 text-gray-800'))
                                    }}">
                                    {{ strtoupper($karyawan->status_pajak ?? '-') }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $karyawan->tanggal_masuk ? \Carbon\Carbon::parse($karyawan->tanggal_masuk)->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center space-x-1">
                                    {{-- Show crew checklist button only for ABK division --}}
                                    @if(strtolower($karyawan->divisi) === 'abk')
                                        <a href="{{ route('master.karyawan.crew-checklist', $karyawan->id) }}" class="inline-flex items-center justify-center w-8 h-8 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-md transition-colors border border-gray-300" title="Checklist Kelengkapan Crew">
                                            <i class="fas fa-tasks text-xs"></i>
                                        </a>
                                    @endif

                                    <a href="{{ route('master.karyawan.show', $karyawan->id) }}" class="inline-flex items-center justify-center w-8 h-8 text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md transition-colors border border-gray-300" title="Lihat">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    
                                    <a href="{{ route('master.karyawan.print.single', $karyawan->id) }}" target="_blank" class="inline-flex items-center justify-center w-8 h-8 text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md transition-colors border border-gray-300" title="Cetak">
                                        <i class="fas fa-print text-xs"></i>
                                    </a>
                                    
                                    <a href="{{ route('master.karyawan.edit', $karyawan->id) }}" class="inline-flex items-center justify-center w-8 h-8 text-yellow-600 hover:text-yellow-900 hover:bg-yellow-50 rounded-md transition-colors border border-gray-300" title="Edit">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                    
                                    <form action="{{ route('master.karyawan.destroy', $karyawan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus karyawan ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center w-8 h-8 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-md transition-colors border border-gray-300" title="Hapus">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                    </svg>
                                    <p class="text-lg font-medium">Belum ada data karyawan</p>
                                    <p class="text-sm mt-1">Tambah karyawan baru untuk memulai</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($karyawans->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $karyawans->links() }}
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="search"]');
    const searchForm = searchInput.closest('form');
    let searchTimeout;
    
    // Auto-submit form setelah user berhenti mengetik selama 500ms
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            if (searchInput.value.length >= 2 || searchInput.value.length === 0) {
                searchForm.submit();
            }
        }, 500);
    });
    
    // Submit langsung saat Enter ditekan
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            clearTimeout(searchTimeout);
            searchForm.submit();
        }
    });
});
</script>
@endsection
