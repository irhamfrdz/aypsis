@extends('layouts.app')

@section('title', 'Daftar Pranota Lembur')
@section('page_title', 'Daftar Pranota Lembur')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
                <i class="fas fa-bed mr-3 text-blue-600 text-2xl"></i>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Daftar Pranota Lembur/Nginap</h1>
                    <p class="text-gray-600">Kelola pranota lembur/nginap</p>
                </div>
            </div>
            <div class="flex space-x-2">
                @can('pranota-lembur-create')
                <a href="{{ route('pranota-lembur.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition duration-200 inline-flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Buat Pranota
                </a>
                @endcan
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Filter Form -->
        <form method="GET" class="mb-6 bg-gray-50 p-4 rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Nomor Pranota..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Status</option>
                        @foreach(\App\Models\PranotaLembur::getStatusOptions() as $key => $label)
                            <option value="{{ $key }}" {{ $status == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
            <div class="flex items-center justify-end mt-4 space-x-2">
                <a href="{{ route('pranota-lembur.list') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md transition duration-200">
                    Reset
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition duration-200 inline-flex items-center">
                    <i class="fas fa-search mr-2"></i>
                    Filter
                </button>
            </div>
        </form>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nomor Pranota</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah Item</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Biaya</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dibuat Oleh</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pranotas as $pranota)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm">{{ $pranotas->firstItem() + $loop->index }}</td>
                        <td class="px-4 py-3 text-sm font-semibold">{{ $pranota->nomor_pranota }}</td>
                        <td class="px-4 py-3 text-sm">{{ $pranota->tanggal_pranota->format('d/M/Y') }}</td>
                        <td class="px-4 py-3 text-sm">{{ $pranota->suratJalans->count() + $pranota->suratJalanBongkarans->count() }} item</td>
                        <td class="px-4 py-3 text-sm font-semibold">{{ $pranota->formatted_total_setelah_adjustment }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $pranota->status_badge }}">
                                {{ $pranota->status_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm">{{ $pranota->creator->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm">
                            <div class="flex items-center space-x-2">
                                @can('pranota-lembur-view')
                                <a href="{{ route('pranota-lembur.show', $pranota->id) }}" class="text-blue-600 hover:text-blue-800" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endcan
                                
                                @can('pranota-lembur-print')
                                <a href="{{ route('pranota-lembur.print', $pranota->id) }}" target="_blank" class="text-green-600 hover:text-green-800" title="Cetak">
                                    <i class="fas fa-print"></i>
                                </a>
                                @endcan
                                
                                @can('pranota-lembur-delete')
                                @if($pranota->status !== 'paid')
                                <form method="POST" action="{{ route('pranota-lembur.destroy', $pranota->id) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus pranota ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                            <p>Belum ada data pranota lembur</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($pranotas->hasPages())
        <div class="mt-6">
            {{ $pranotas->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
