@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-gas-pump mr-2 text-amber-600"></i>
                Biaya Bensin
            </h1>
            <p class="text-gray-600 mt-1">Kelola pencatatan biaya bensin kendaraan</p>
        </div>
        <div class="flex items-center space-x-3">
            @can('biaya-bensin-update')
                <a href="{{ route('biaya-bensin.approval') }}" 
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow-sm transition-colors duration-200">
                    <i class="fas fa-check-double mr-2"></i>
                    Approval
                    @php
                        $pendingCount = \App\Models\BiayaBensin::where('status', 'pending')->count();
                    @endphp
                    @if($pendingCount > 0)
                        <span class="ml-2 px-2 py-0.5 text-xs font-bold bg-red-500 text-white rounded-full">{{ $pendingCount }}</span>
                    @endif
                </a>
            @endcan
            @can('biaya-bensin-create')
                <a href="{{ route('biaya-bensin.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-lg shadow-sm transition-colors duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Catat Biaya Bensin
                </a>
            @endcan
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-center shadow-sm">
            <i class="fas fa-check-circle mr-3 text-green-500"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Search Form -->
    <form method="GET" action="{{ route('biaya-bensin.index') }}" class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-colors duration-200">
                    <i class="fas fa-filter mr-2"></i> Filter
                </button>
                @if(request('start_date') || request('end_date'))
                    <a href="{{ route('biaya-bensin.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-colors duration-200">
                        <i class="fas fa-times mr-2"></i> Reset
                    </a>
                @endif
            </div>
        </div>
    </form>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mobil</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supir</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">KM Awal/Akhir</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Liter</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Biaya</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat Oleh</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($items as $item)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item->tanggal->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="font-medium text-gray-900">{{ $item->mobil->nopol ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $item->mobil->kode_mobil ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item->supir->nama ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div>{{ number_format($item->km_awal, 0, ',', '.') }} KM</div>
                                <div class="text-xs">{{ number_format($item->km_akhir, 0, ',', '.') }} KM</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($item->liter, 2, ',', '.') }} L
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                Rp {{ number_format($item->biaya, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($item->status === 'pending')
                                    <span class="px-2.5 py-1 text-xs font-bold bg-amber-100 text-amber-800 rounded-full">Pending</span>
                                @elseif($item->status === 'approved')
                                    <span class="px-2.5 py-1 text-xs font-bold bg-green-100 text-green-800 rounded-full">Approved</span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-bold bg-red-100 text-red-800 rounded-full">Rejected</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-xs">
                                {{ $item->creator->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    @if($item->bukti_beli)
                                        <a href="{{ asset('storage/' . $item->bukti_beli) }}" target="_blank" class="p-2 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition-colors" title="Lihat Bukti Beli">
                                            <i class="fas fa-file-invoice"></i>
                                        </a>
                                    @endif
                                    @can('biaya-bensin-update')
                                        <a href="{{ route('biaya-bensin.edit', $item) }}" class="p-2 bg-amber-100 text-amber-700 rounded-md hover:bg-amber-200 transition-colors" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endcan
                                    @can('biaya-bensin-delete')
                                        <form action="{{ route('biaya-bensin.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 bg-red-100 text-red-700 rounded-md hover:bg-red-200 transition-colors" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-gas-pump text-gray-300 text-5xl mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 mb-1">Belum ada catatan biaya bensin</h3>
                                    <p class="text-gray-500 mb-4 text-sm">Mulai catat biaya bensin dengan menekan tombol di atas.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $items->appends(request()->query())->links() }}
    </div>
</div>
@endsection
