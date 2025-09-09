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
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('master.karyawan.create') }}" class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded transition duration-150">
                        + Tambah Karyawan
                    </a>
                    <a href="{{ route('master.karyawan.print') }}" target="_blank" class="inline-flex items-center px-3 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded transition duration-150">
                        Cetak Semua
                    </a>
                    <a href="{{ route('master.karyawan.export') }}?sep=%3B" class="inline-flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded transition duration-150">
                        Download CSV
                    </a>
                    <a href="{{ route('master.karyawan.import') }}" class="inline-flex items-center px-3 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded transition duration-150">
                        Import CSV
                    </a>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="mx-6 mt-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <!-- Table Section -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIK</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NAMA LENGKAP</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NAMA PANGGILAN</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DIVISI</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PEKERJAAN</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NO HP</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NO KETENAGAKERJAAN</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">AKSI</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($karyawans as $karyawan)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $karyawan->nik }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $karyawan->nama_lengkap }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $karyawan->nama_panggilan }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-md
                                    {{ strtolower($karyawan->divisi) === 'it' ? 'bg-blue-100 text-blue-800' : 
                                       (strtolower($karyawan->divisi) === 'abk' ? 'bg-blue-100 text-blue-800' : 
                                       (strtolower($karyawan->divisi) === 'supir' ? 'bg-gray-100 text-gray-800' : 
                                       'bg-gray-100 text-gray-800')) }}">
                                    {{ $karyawan->divisi }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $karyawan->pekerjaan }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $karyawan->no_hp }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $karyawan->no_ketenagakerjaan ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
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
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
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
@endsection
