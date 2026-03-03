@extends('layouts.app')

@section('title', 'Dokumen Kapal Alexindo')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Dokumen Kapal Alexindo</h1>
                <p class="text-gray-600 mt-1">Kelola data dokumen kapal</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('master-dokumen-kapal-alexindo.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200 inline-flex items-center">
                    <i class="fas fa-plus mr-2"></i> Tambah Dokumen
                </a>
            </div>
        </div>
    </div>

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

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">No</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kapal</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dokumen</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor</th>
                            <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Terbit</th>
                            <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Berakhir</th>
                            <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">File</th>
                            <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($dokumens as $index => $dokumen)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-3 whitespace-nowrap text-center">{{ $index + 1 }}</td>
                            <td class="px-3 py-3 whitespace-nowrap">{{ $dokumen->kapal->nama_kapal ?? '-' }}</td>
                            <td class="px-3 py-3 whitespace-nowrap">
                                <div class="font-medium text-gray-900">{{ $dokumen->nama_dokumen }}</div>
                                @if($dokumen->keterangan)
                                <div class="text-xs text-gray-500 mt-0.5">{{ Str::limit($dokumen->keterangan, 30) }}</div>
                                @endif
                            </td>
                            <td class="px-3 py-3 whitespace-nowrap text-gray-600">{{ $dokumen->nomor_dokumen ?? '-' }}</td>
                            <td class="px-3 py-3 whitespace-nowrap text-center text-gray-600">
                                {{ $dokumen->tanggal_terbit ? \Carbon\Carbon::parse($dokumen->tanggal_terbit)->format('d M Y') : '-' }}
                            </td>
                            <td class="px-3 py-3 whitespace-nowrap text-center">
                                @if($dokumen->tanggal_berakhir)
                                    @php
                                        $isExpired = \Carbon\Carbon::parse($dokumen->tanggal_berakhir)->isPast();
                                        $isExpiringSoon = \Carbon\Carbon::parse($dokumen->tanggal_berakhir)->isSameMonth(\Carbon\Carbon::now());
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $isExpired ? 'bg-red-100 text-red-800' : ($isExpiringSoon ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                        {{ \Carbon\Carbon::parse($dokumen->tanggal_berakhir)->format('d M Y') }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-3 py-3 whitespace-nowrap text-center">
                                @if($dokumen->file_dokumen)
                                <a href="{{ asset($dokumen->file_dokumen) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-xs">
                                    <i class="fas fa-download"></i> Unduh
                                </a>
                                @else
                                <span class="text-gray-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="px-3 py-3 whitespace-nowrap text-center text-sm font-medium">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('master-dokumen-kapal-alexindo.edit', $dokumen->id) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('master-dokumen-kapal-alexindo.destroy', $dokumen->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-3 py-8 text-center text-gray-500">Tidak ada dokumen</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
