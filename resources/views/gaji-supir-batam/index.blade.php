@extends('layouts.app')

@section('title', 'Gaji Supir Batam')
@section('page_title', 'Gaji Supir Batam')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Gaji Supir Batam</h2>
        <p class="mt-1 text-sm text-gray-600">Kelola data gaji supir di wilayah Batam</p>
    </div>
    <div class="flex flex-col sm:flex-row gap-2">
        @can('gaji-supir-batam-create')
            <a href="{{ route('gaji-supir-batam.create') }}" 
               class="inline-flex items-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                <i class="fas fa-plus mr-2"></i>
                Tambah Gaji Supir
            </a>
        @endcan
    </div>
</div>

@if (session('success'))
    <div class="mb-6 rounded-md bg-green-50 p-4 shadow-sm border border-green-200">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">
                    {{ session('success') }}
                </p>
            </div>
        </div>
    </div>
@endif

@if (session('error'))
    <div class="mb-6 rounded-md bg-red-50 p-4 shadow-sm border border-red-200">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-times-circle text-red-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800">
                    {{ session('error') }}
                </p>
            </div>
        </div>
    </div>
@endif

<!-- Search & Filter Form -->
<form method="GET" action="{{ route('gaji-supir-batam.index') }}" class="bg-white rounded-lg shadow-sm p-6 mb-6 border border-gray-100">
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <!-- Search -->
        <div>
            <label for="search" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Cari Supir</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" 
                       name="search" 
                       id="search"
                       value="{{ $search }}" 
                       placeholder="Nama, NIK, Plat..." 
                       class="block w-full pl-9 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
            </div>
        </div>

        <!-- Supir Select -->
        <div>
            <label for="karyawan_id" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Nama Supir</label>
            <select name="karyawan_id" id="karyawan_id" class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                <option value="">Semua Supir</option>
                @foreach($supirList as $s)
                    <option value="{{ $s->id }}" {{ $karyawanId == $s->id ? 'selected' : '' }}>
                        {{ $s->nama_lengkap }} ({{ $s->plat ?? '-' }})
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Start Date -->
        <div>
            <label for="start_date" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Tanggal Mulai</label>
            <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
        </div>

        <!-- End Date -->
        <div>
            <label for="end_date" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Tanggal Selesai</label>
            <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
        </div>

        <!-- Status -->
        <div>
            <label for="status_pembayaran" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Status Bayar</label>
            <select name="status_pembayaran" id="status_pembayaran" class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                <option value="">Semua Status</option>
                <option value="PENDING" {{ $statusPembayaran == 'PENDING' ? 'selected' : '' }}>PENDING</option>
                <option value="PAID" {{ $statusPembayaran == 'PAID' ? 'selected' : '' }}>PAID</option>
                <option value="CANCELLED" {{ $statusPembayaran == 'CANCELLED' ? 'selected' : '' }}>CANCELLED</option>
            </select>
        </div>
    </div>

    <div class="flex justify-end space-x-3 mt-4">
        <button type="submit" 
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
            <i class="fas fa-search mr-2"></i>
            Cari
        </button>
        @if($search || $startDate || $endDate || $karyawanId || $statusPembayaran)
            <a href="{{ route('gaji-supir-batam.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                <i class="fas fa-times mr-2"></i>
                Reset
            </a>
        @endif
    </div>
</form>

<!-- Table -->
<div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Supir</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Periode Tanggal</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Uang Jalan (Gaji Pokok)</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Gaji Bersih</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($gajiList as $gaji)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $gaji->karyawan->nama_lengkap }}</div>
                            <div class="text-xs text-gray-500">NIK: {{ $gaji->karyawan->nik ?? '-' }} | Plat: {{ $gaji->karyawan->plat ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-800">
                                {{ $gaji->periode_text }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900 font-medium">
                            Rp {{ number_format($gaji->gaji_pokok, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-indigo-600">
                            Rp {{ number_format($gaji->total_gaji, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if ($gaji->status_pembayaran === 'PAID')
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    PAID
                                </span>
                                @if($gaji->tanggal_dibayar)
                                    <div class="text-[10px] text-gray-500 mt-1">{{ $gaji->tanggal_dibayar->format('d/m/Y') }}</div>
                                @endif
                            @elseif ($gaji->status_pembayaran === 'PENDING')
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    PENDING
                                </span>
                            @else
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                    CANCELLED
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="{{ route('gaji-supir-batam.show', $gaji->id) }}" 
                                   class="text-indigo-600 hover:text-indigo-900 p-1.5 rounded-full hover:bg-indigo-50 transition-colors" 
                                   title="Detail / Slip Gaji">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                @if($gaji->status_pembayaran === 'PENDING')
                                    @can('gaji-supir-batam-edit')
                                        <form action="{{ route('gaji-supir-batam.bayar', $gaji->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    onclick="return confirm('Apakah Anda yakin ingin menandai gaji supir ini sebagai sudah dibayar?')"
                                                    class="text-green-600 hover:text-green-900 p-1.5 rounded-full hover:bg-green-50 transition-colors" 
                                                    title="Proses Bayar">
                                                <i class="fas fa-check-double"></i>
                                            </button>
                                        </form>
                                        <a href="{{ route('gaji-supir-batam.edit', $gaji->id) }}" 
                                           class="text-amber-600 hover:text-amber-900 p-1.5 rounded-full hover:bg-amber-50 transition-colors" 
                                           title="Ubah Gaji">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endcan
                                @endif

                                @can('gaji-supir-batam-delete')
                                    <form action="{{ route('gaji-supir-batam.destroy', $gaji->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus data gaji ini?')" 
                                                class="text-red-600 hover:text-red-900 p-1.5 rounded-full hover:bg-red-50 transition-colors"
                                                title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">
                            <i class="fas fa-info-circle text-2xl text-gray-350 mb-3 block"></i>
                            Tidak ada data gaji supir ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($gajiList->hasPages())
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            {{ $gajiList->links() }}
        </div>
    @endif
</div>
@endsection
