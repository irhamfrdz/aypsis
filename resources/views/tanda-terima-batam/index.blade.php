@extends('layouts.app')

@section('title', 'Tanda Terima Batam')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Tanda Terima Batam</h1>
                <p class="text-gray-600 mt-1">Kelola tanda terima kontainer dari surat jalan batam yang sudah di-approve</p>
                <div class="mt-3 text-[10px] text-gray-500 font-medium bg-gray-50 border border-gray-200 px-3 py-1 rounded-full flex items-center gap-2 inline-flex">
                    <span class="flex h-2 w-2 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                    </span>
                    <span>Waktu: <span class="font-bold text-blue-600">{{ $lastUpdateStr }}</span></span>
                </div>
            </div>
            <div>
                <a href="{{ route('tanda-terima-batam.select-surat-jalan') }}" 
                   class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg shadow-sm transition duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Tanda Terima Batam
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
            <h2 class="text-lg font-semibold text-gray-900">Daftar Tanda Terima Batam</h2>
        </div>

        <div class="p-6">
            <!-- Filter & Search -->
            <form method="GET" action="{{ route('tanda-terima-batam.index') }}" class="mb-6">
                <div class="flex gap-3">
                    <div class="flex-grow">
                        <input type="text" name="search" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Cari surat jalan, kontainer, supir..." value="{{ request('search') }}">
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">Cari</button>
                    <a href="{{ route('tanda-terima-batam.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg">Reset</a>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. SJ</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal TT</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supir / No Plat</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kontainer / Seal</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vessel / Voyage</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($tandaTerimas as $tt)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-4 whitespace-nowrap text-gray-500">
                                {{ ($tandaTerimas->currentPage() - 1) * $tandaTerimas->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap font-medium text-gray-900">
                                {{ $tt->no_surat_jalan }}
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap text-gray-600">
                                {{ $tt->tanggal ? $tt->tanggal->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap text-gray-600">
                                <div>{{ $tt->supir ?: '-' }}</div>
                                <div class="text-xs text-gray-400">{{ $tt->no_plat ?: '-' }}</div>
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap text-gray-600">
                                <div>{{ $tt->no_kontainer ?: '-' }}</div>
                                <div class="text-xs text-gray-400">{{ $tt->no_seal ?: '-' }}</div>
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap text-gray-600">
                                <div>{{ $tt->nama_kapal ?: '-' }}</div>
                                <div class="text-xs text-gray-400">{{ $tt->no_voyage ?: '-' }}</div>
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full {{ $tt->status == 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($tt->status) }}
                                </span>
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap text-center space-x-2">
                                <a href="{{ route('tanda-terima-batam.show', $tt->id) }}" class="text-blue-600 hover:text-blue-800"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('tanda-terima-batam.edit', $tt->id) }}" class="text-amber-600 hover:text-amber-800"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('tanda-terima-batam.destroy', $tt->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus tanda terima ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-3 py-8 text-center text-gray-500 font-medium">Tidak ada data</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $tandaTerimas->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
