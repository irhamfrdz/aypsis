@extends('layouts.app')

@section('title', 'Riwayat Pranota Lembur Karyawan')
@section('page_title', 'Riwayat Pranota Lembur Karyawan')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-3xl font-bold text-gray-900">Riwayat Pranota Lembur</h1>
                    <p class="mt-1 text-sm text-gray-600">Daftar rekaman pranota lembur khusus karyawan.</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('payroll.perhitungan-lembur') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        <i class="fas fa-plus mr-2"></i>
                        Buat Baru
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <!-- Filter Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-sm font-bold text-gray-900 flex items-center">
                    <i class="fas fa-filter text-gray-400 mr-2"></i>
                    Filter Data
                </h3>
            </div>
            <div class="p-6">
                <form action="{{ route('pranota-lembur-karyawan.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="nomor_pranota" class="block text-sm font-medium text-gray-700 mb-1">Nomor Pranota</label>
                        <input type="text" name="nomor_pranota" id="nomor_pranota" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" value="{{ request('nomor_pranota') }}" placeholder="Contoh: PML...">
                    </div>
                    <div>
                        <label for="tanggal_pranota" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pranota</label>
                        <input type="date" name="tanggal_pranota" id="tanggal_pranota" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" value="{{ request('tanggal_pranota') }}">
                    </div>
                    <div class="flex items-end space-x-3">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-search mr-2"></i> Cari
                        </button>
                        <a href="{{ route('pranota-lembur-karyawan.index') }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nomor Pranota</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Total Biaya</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Adjustment</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($pranotas as $pranota)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $loop->iteration + $pranotas->firstItem() - 1 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-blue-600 font-mono">{{ $pranota->nomor_pranota }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $pranota->tanggal_pranota->format('d/m/Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $pranota->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-emerald-600">
                                Rp {{ number_format($pranota->total_biaya, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-orange-500">
                                Rp {{ number_format($pranota->adjustment, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($pranota->status == 'draft')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Draft
                                    </span>
                                @elseif($pranota->status == 'approved')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Disetujui
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ ucfirst($pranota->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <a href="{{ route('pranota-lembur-karyawan.show', $pranota->id) }}" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded-md transition-colors">
                                    <i class="fas fa-eye mr-1"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="h-16 w-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-file-invoice text-2xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-sm font-medium text-gray-900">Belum ada pranota lembur karyawan</h3>
                                    <p class="mt-1 text-sm text-gray-500">Buat pranota lembur melalui halaman Perhitungan Lembur.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($pranotas->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $pranotas->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
