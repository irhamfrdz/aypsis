@extends('layouts.app')

@section('title', 'Uang Jalan Batam')
@section('page_title', 'Uang Jalan Batam')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h2 class="text-xl font-bold text-gray-800">Data Uang Jalan Batam</h2>
    <a href="{{ route('uang-jalan-batam.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        <i class="fas fa-plus mr-2"></i>Tambah Data
    </a>
</div>

@if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<!-- Search Form -->
<div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
    <form method="GET" action="{{ route('uang-jalan-batam.index') }}" class="flex items-center">
        <div class="flex-grow mr-4">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari wilayah, rute, expedisi, ring, ft, f/e, status..." 
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>
        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            <i class="fas fa-search"></i> Cari
        </button>
        @if($search)
            <a href="{{ route('uang-jalan-batam.index') }}" class="ml-2 bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-times"></i> Reset
            </a>
        @endif
    </form>
</div>

<!-- Table -->
<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wilayah</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rute</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expedisi</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ring</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">FT</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">F/E</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarif</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Berlaku</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($uangJalanBatams as $uangJalan)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $uangJalan->wilayah }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $uangJalan->rute }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $uangJalan->expedisi }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $uangJalan->ring }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $uangJalan->ft }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $uangJalan->f_e }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp {{ number_format($uangJalan->tarif, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        @if($uangJalan->status)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if($uangJalan->status == 'aqua') bg-blue-100 text-blue-800
                                @elseif($uangJalan->status == 'chasis PB') bg-green-100 text-green-800
                                @endif">
                                {{ $uangJalan->status }}
                            </span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $uangJalan->tanggal_berlaku->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('uang-jalan-batam.show', $uangJalan) }}" class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('uang-jalan-batam.edit', $uangJalan) }}" class="text-green-600 hover:text-green-900">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('uang-jalan-batam.destroy', $uangJalan) }}" class="inline" 
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                        Tidak ada data uang jalan Batam.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if($uangJalanBatams->hasPages())
    <div class="mt-6">
        {{ $uangJalanBatams->withQueryString()->links() }}
    </div>
@endif
@endsection