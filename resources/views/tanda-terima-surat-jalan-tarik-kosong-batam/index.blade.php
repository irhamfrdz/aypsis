@extends('layouts.app')

@section('title', 'Tanda Terima SJ Tarik Kosong Batam')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Tanda Terima Surat Jalan Tarik Kosong Batam</h1>
                <p class="text-gray-600 mt-1">Manajemen tanda terima untuk kegiatan penarikan kontainer kosong di Batam</p>
            </div>
            <div>
                <a href="{{ route('tanda-terima-surat-jalan-tarik-kosong-batam.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg shadow-sm transition duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Tanda Terima
                </a>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Main Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Daftar Tanda Terima</h2>
        </div>

        <div class="p-6">
            <!-- Filter & Search -->
            <form method="GET" action="{{ route('tanda-terima-surat-jalan-tarik-kosong-batam.index') }}" class="mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Cari</label>
                        <input type="text" name="search" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="No TT, No SJ, Kontainer, Supir..." value="{{ request('search') }}">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Dari Tanggal</label>
                        <input type="date" name="from_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" value="{{ request('from_date') }}">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                        <input type="date" name="to_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" value="{{ request('to_date') }}">
                    </div>
                </div>
                <div class="flex justify-end mt-4 gap-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">Filter</button>
                    <a href="{{ route('tanda-terima-surat-jalan-tarik-kosong-batam.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg font-medium">Reset</a>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No. TT / Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No. SJ / Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Supir / No Plat</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kontainer</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Penerima</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($items as $item)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-4 py-4 whitespace-nowrap text-gray-500">
                                {{ ($items->currentPage() - 1) * $items->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="font-bold text-blue-600">{{ $item->no_tanda_terima }}</div>
                                <div class="text-xs text-gray-500">{{ $item->tanggal_tanda_terima->format('d/m/Y') }}</div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">{{ $item->no_surat_jalan ?: '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $item->tanggal_surat_jalan ? $item->tanggal_surat_jalan->format('d/m/Y') : '-' }}</div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">{{ $item->supir ?: '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $item->no_plat ?: '-' }}</div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="font-bold text-gray-900">{{ $item->no_kontainer ?: '-' }}</div>
                                <div class="text-xs text-gray-500">Size: {{ $item->size ?: '-' }}</div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-gray-600">
                                {{ $item->penerima ?: '-' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-center space-x-2">
                                <a href="{{ route('tanda-terima-surat-jalan-tarik-kosong-batam.print', $item->id) }}" target="_blank" class="text-emerald-600 hover:text-emerald-800" title="Cetak"><i class="fas fa-print"></i></a>
                                <a href="{{ route('tanda-terima-surat-jalan-tarik-kosong-batam.show', $item->id) }}" class="text-blue-600 hover:text-blue-800" title="Detail"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('tanda-terima-surat-jalan-tarik-kosong-batam.edit', $item->id) }}" class="text-amber-600 hover:text-amber-800" title="Edit"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('tanda-terima-surat-jalan-tarik-kosong-batam.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus tanda terima ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-folder-open text-gray-300 text-5xl mb-4"></i>
                                    <p class="text-gray-500 font-medium">Belum ada data tanda terima</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-6">
                {{ $items->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
