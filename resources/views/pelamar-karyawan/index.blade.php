@extends('layouts.app')

@section('title', 'Data Pelamar Karyawan')
@section('page_title', 'Data Pelamar')

@section('content')
<div class="max-w-full mx-auto px-4">
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <!-- Header Section -->
        <div class="px-5 py-4 border-b bg-white">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <h1 class="text-xl font-bold text-gray-900 leading-tight">Daftar Pelamar Karyawan</h1>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="mx-6 mt-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded" role="alert">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-green-400 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <div class="flex-1">
                        {{ session('success') }}
                    </div>
                </div>
            </div>
        @endif

        <!-- Table Section -->
        <div class="table-container overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-center text-[10px] font-medium text-gray-500 uppercase tracking-wider">No.</th>
                        <th class="px-4 py-3 text-center text-[10px] font-medium text-gray-500 uppercase tracking-wider">Tgl Daftar</th>
                        <th class="px-4 py-3 text-center text-[10px] font-medium text-gray-500 uppercase tracking-wider">Nama Lengkap</th>
                        <th class="px-4 py-3 text-center text-[10px] font-medium text-gray-500 uppercase tracking-wider">NIK</th>
                        <th class="px-4 py-3 text-center text-[10px] font-medium text-gray-500 uppercase tracking-wider">Jenis Kelamin</th>
                        <th class="px-4 py-3 text-center text-[10px] font-medium text-gray-500 uppercase tracking-wider">Agama</th>
                        <th class="px-4 py-3 text-center text-[10px] font-medium text-gray-500 uppercase tracking-wider">No. HP</th>
                        <th class="px-4 py-3 text-center text-[10px] font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-4 py-3 text-center text-[10px] font-medium text-gray-500 uppercase tracking-wider">CV</th>
                        <th class="px-4 py-3 text-center text-[10px] font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($pelamars as $pelamar)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap text-center text-[10px] text-gray-900 font-medium">
                                {{ ($pelamars->currentPage() - 1) * $pelamars->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center text-[10px] text-gray-900">
                                {{ $pelamar->created_at->format('d/M/Y H:i') }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center text-[10px] text-gray-900 font-bold">
                                {{ strtoupper($pelamar->nama_lengkap) }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center text-[10px] text-gray-900 uppercase">
                                {{ $pelamar->no_nik }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center text-[10px] text-gray-900">
                                {{ $pelamar->jenis_kelamin }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center text-[10px] text-gray-900">
                                {{ $pelamar->agama ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center text-[10px] text-gray-900">
                                {{ $pelamar->no_handphone }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center text-[10px] text-gray-900">
                                {{ $pelamar->email ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center text-[10px]">
                                @if($pelamar->cv_path)
                                    <a href="{{ asset('storage/' . $pelamar->cv_path) }}" target="_blank" class="inline-flex items-center px-2 py-1 bg-indigo-50 text-indigo-700 rounded-md hover:bg-indigo-100 transition-colors">
                                        <i class="fas fa-file-pdf mr-1"></i> Lihat CV
                                    </a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center space-x-3 text-[10px]">
                                    <a href="{{ route('master.pelamar-karyawan.show', $pelamar->id) }}" class="text-blue-600 hover:text-blue-800 font-bold uppercase tracking-wider">
                                        <i class="fas fa-eye mr-1"></i> Detail
                                    </a>
                                    <span class="text-gray-300">|</span>
                                    <a href="{{ route('master.karyawan.create', ['pelamar_id' => $pelamar->id]) }}" class="text-green-600 hover:text-green-800 font-bold uppercase tracking-wider">
                                        <i class="fas fa-user-plus mr-1"></i> Daftarkan
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-10 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-user-clock text-4xl text-gray-200 mb-3"></i>
                                    <p class="text-xs font-medium">Belum ada data pelamar</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-5 py-4 border-t bg-gray-50">
            {{ $pelamars->links() }}
        </div>
    </div>
</div>
@endsection
