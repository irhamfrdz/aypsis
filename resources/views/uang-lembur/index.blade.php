@extends('layouts.app')

@section('title', 'Data Uang Lembur')
@section('page_title', 'Data Uang Lembur')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-3xl font-bold text-gray-900">Data Uang Lembur</h1>
                    <p class="mt-1 text-sm text-gray-600">Daftar riwayat lembur karyawan</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    @can('data-cuti-create')
                    <a href="{{ route('uang-lembur.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors duration-200 shadow-sm">
                        Tambah Data
                    </a>
                    @endcan
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Filter Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
            <form action="{{ route('uang-lembur.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-4">
                <div class="w-full sm:w-1/3">
                    <label for="search" class="sr-only">Cari Karyawan</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Cari nama atau NIK..." class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                </div>
                <div>
                    <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm">Cari</button>
                    @if(request('search'))
                        <a href="{{ route('uang-lembur.index') }}" class="ml-2 text-sm text-indigo-600 hover:text-indigo-900">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Karyawan</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal & Tipe</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nominal</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($lemburs as $lembur)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $lembur->karyawan->nama_lengkap ?? '-' }}</div>
                                    <div class="text-sm text-gray-500">{{ $lembur->karyawan->nik ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($lembur->tanggal)->format('d M Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $lembur->tipe_hari }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($lembur->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($lembur->jam_selesai)->format('H:i') }}</div>
                                    <div class="text-xs text-gray-500">{{ $lembur->total_jam }} Jam</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">Rp {{ number_format($lembur->nominal_uang, 0, ',', '.') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $lembur->status === 'approved' ? 'green' : ($lembur->status === 'rejected' ? 'red' : 'yellow') }}-100 text-{{ $lembur->status === 'approved' ? 'green' : ($lembur->status === 'rejected' ? 'red' : 'yellow') }}-800">
                                        {{ ucfirst($lembur->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @can('data-cuti-edit')
                                    <a href="{{ route('uang-lembur.edit', $lembur->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                    @endcan
                                    @can('data-cuti-delete')
                                    <form action="{{ route('uang-lembur.destroy', $lembur->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                    </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Belum ada data uang lembur.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($lemburs->hasPages())
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $lemburs->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
