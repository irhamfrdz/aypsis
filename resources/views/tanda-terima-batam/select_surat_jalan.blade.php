@extends('layouts.app')

@section('title', 'Pilih Surat Jalan Batam')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <nav class="flex mb-4" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3 text-sm text-gray-500">
                        <li class="inline-flex items-center">
                            <a href="{{ route('tanda-terima-batam.index') }}" class="hover:text-blue-600">Tanda Terima Batam</a>
                        </li>
                        <i class="fas fa-chevron-right text-[10px] mx-2"></i>
                        <li class="text-gray-900 font-medium">Pilih Surat Jalan Batam</li>
                    </ol>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900">Pilih Surat Jalan Batam</h1>
                <p class="text-gray-600 mt-1">Langkah 1: Pilih surat jalan batam yang akan dibuatkan tanda terimanya</p>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6">
            <!-- Filter & Search -->
            <form method="GET" action="{{ route('tanda-terima-batam.select-surat-jalan') }}" class="mb-6">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                    <div class="md:col-span-4">
                        <input type="text" name="search" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Cari surat jalan, kontainer, supir..." value="{{ $search }}">
                    </div>
                    <div class="md:col-span-3">
                        <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            @foreach($statusOptions as $val => $label)
                                <option value="{{ $val }}" {{ $status == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                            Cari
                        </button>
                    </div>
                    <div class="md:col-span-2">
                        <a href="{{ route('tanda-terima-batam.select-surat-jalan') }}" class="block text-center w-full bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                            Reset
                        </a>
                    </div>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. SJ</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal SJ</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supir / No Plat</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kontainer / Seal</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kegiatan</th>
                            <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($suratJalans as $sj)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-4 text-gray-500 text-center">
                                {{ ($suratJalans->currentPage() - 1) * $suratJalans->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-3 py-4 font-semibold text-gray-900">
                                {{ $sj->no_surat_jalan }}
                            </td>
                            <td class="px-3 py-4 text-gray-600">
                                {{ $sj->tanggal_surat_jalan ? $sj->tanggal_surat_jalan->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-3 py-4 text-gray-600">
                                <div class="font-medium">{{ $sj->supir ?: '-' }}</div>
                                <div class="text-xs text-gray-400">{{ $sj->no_plat ?: '-' }}</div>
                            </td>
                            <td class="px-3 py-4 text-gray-600">
                                <div class="font-medium">{{ $sj->no_kontainer ?: '-' }}</div>
                                <div class="text-xs text-gray-400 font-mono">{{ $sj->no_seal ?: '-' }}</div>
                            </td>
                            <td class="px-3 py-4 text-gray-600">
                                <span class="px-2 py-1 bg-indigo-50 text-indigo-700 rounded text-xs">
                                    {{ Str::limit($sj->kegiatan ?: '-', 20) }}
                                </span>
                            </td>
                            <td class="px-3 py-4 text-center">
                                <a href="{{ route('tanda-terima-batam.create', ['surat_jalan_id' => $sj->id]) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition duration-200">
                                    <i class="fas fa-plus mr-1"></i> Buat TT
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-3 py-12 text-center text-gray-400 font-medium italic">
                                Tidak ada data
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $suratJalans->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
