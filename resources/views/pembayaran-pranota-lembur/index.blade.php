@extends('layouts.app')

@section('title', 'Daftar Pembayaran Pranota Lembur')
@section('page_title', 'Daftar Pembayaran Pranota Lembur')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-2">
            @can('pembayaran-pranota-lembur-create')
                <a href="{{ route('pembayaran-pranota-lembur.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Buat Pembayaran
                </a>
            @endcan
        </div>

        <form action="{{ route('pembayaran-pranota-lembur.index') }}" method="GET" class="flex flex-col md:flex-row gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No. Pranota..." class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Filter
            </button>
        </form>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 rounded-md bg-green-50 border border-green-200 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Pranota</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pembayaran</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($pranotaList as $pranota)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $pranota->nomor_pranota }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $pranota->tanggal_pranota->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold">Rp {{ number_format($pranota->total_setelah_adjustment, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($pranota->pembayaranPranotaLemburs->count() > 0)
                                @foreach($pranota->pembayaranPranotaLemburs as $pembayaran)
                                    <div class="mb-1">
                                        <a href="{{ route('pembayaran-pranota-lembur.show', $pembayaran->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                            {{ $pembayaran->nomor_pembayaran }}
                                        </a>
                                        <span class="text-xs text-gray-400">({{ $pembayaran->tanggal_pembayaran->format('d/m/y') }})</span>
                                    </div>
                                @endforeach
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            @if($pranota->pembayaranPranotaLemburs->count() == 0)
                                <a href="{{ route('pembayaran-pranota-lembur.create', ['pranota_id' => $pranota->id]) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Bayar</a>
                            @endif
                            <a href="{{ route('pranota-lembur.show', $pranota->id) }}" class="text-gray-600 hover:text-gray-900">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Data tidak ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $pranotaList->links() }}
    </div>
</div>
@endsection
