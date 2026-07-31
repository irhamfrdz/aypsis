@extends('layouts.app')

@section('title', 'Data Uang Makan Karyawan')
@section('page_title', 'Data Uang Makan Karyawan')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-3xl font-bold text-gray-900">Data Uang Makan</h1>
                    <p class="mt-1 text-sm text-gray-600">Daftar pencatatan uang makan harian karyawan</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    @can('data-uang-makan-create')
                    <a href="{{ route('uang-makan.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors duration-200 shadow-sm">
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
            <form action="{{ route('uang-makan.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-4">
                <div class="w-full sm:w-1/3">
                    <label for="penempatan" class="sr-only">Filter Penempatan</label>
                    <select id="penempatan" name="penempatan" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md" onchange="this.form.submit()">
                        <option value="">Semua Penempatan</option>
                        @foreach($penempatans as $pen)
                            <option value="{{ $pen }}" {{ request('penempatan') == $pen ? 'selected' : '' }}>{{ $pen }}</option>
                        @endforeach
                    </select>
                </div>
                @if(request('penempatan'))
                <div>
                    <a href="{{ route('uang-makan.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">Reset Filter</a>
                </div>
                @endif
            </form>
        </div>

        <!-- Table Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Karyawan</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nominal</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($uangMakans as $index => $uangMakan)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $uangMakans->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $uangMakan->karyawan->nama_lengkap ?? '-' }}</div>
                                    <div class="text-sm text-gray-500">{{ $uangMakan->karyawan->nik ?? '-' }}</div>
                                    <div class="text-xs text-gray-400 mt-1">
                                        @if($uangMakan->karyawan && $uangMakan->karyawan->akun_bank)
                                            {{ $uangMakan->karyawan->nama_bank ?? 'Bank' }} - {{ $uangMakan->karyawan->akun_bank }}
                                        @else
                                            No Rek: -
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $uangMakan->tanggal->format('d M Y') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">Rp {{ number_format($uangMakan->nominal, 0, ',', '.') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 line-clamp-2">{{ $uangMakan->keterangan ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @can('data-uang-makan-edit')
                                    <a href="{{ route('uang-makan.edit', $uangMakan->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                    @endcan
                                    @can('data-uang-makan-delete')
                                    <form action="{{ route('uang-makan.destroy', $uangMakan->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                    </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Belum ada data uang makan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($uangMakans->hasPages())
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $uangMakans->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
