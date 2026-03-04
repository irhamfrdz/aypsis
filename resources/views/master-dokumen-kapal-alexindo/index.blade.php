@extends('layouts.app')

@section('title', 'Dokumen Kapal Alexindo')

@section('content')
<div class="container mx-auto px-4 py-4 md:py-8">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-bold text-gray-900">Dokumen Kapal Alexindo</h1>
                <p class="text-sm md:text-base text-gray-600 mt-1">Kelola data dokumen kapal</p>
            </div>
            <div>
                <a href="{{ route('master-dokumen-kapal-alexindo.create') }}" class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200 inline-flex items-center justify-center text-sm">
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

    <!-- Desktop View (Table) -->
    <div class="hidden md:block bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">No</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kapal</th>
                            <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Dokumen</th>
                            <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($kapals as $index => $kapal)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-3 whitespace-nowrap text-center">{{ $index + 1 }}</td>
                            <td class="px-3 py-3 whitespace-nowrap font-medium text-gray-900">{{ $kapal->nama_kapal }}</td>
                            <td class="px-3 py-3 whitespace-nowrap text-center">
                                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-semibold">
                                    {{ $kapal->dokumen_kapal_alexindos_count }} Dokumen
                                </span>
                            </td>
                            <td class="px-3 py-3 whitespace-nowrap text-center text-sm font-medium">
                                <a href="{{ route('master-dokumen-kapal-alexindo.show', $kapal->id) }}" class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-md transition-colors inline-block" title="Lihat Dokumen">
                                    <i class="fas fa-eye mr-1"></i> Lihat Dokumen
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-3 py-8 text-center text-gray-500">Tidak ada kapal</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Mobile View (Cards) -->
    <div class="md:hidden space-y-4">
        @forelse($kapals as $index => $kapal)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex justify-between items-start mb-3">
                <div class="flex items-center">
                    <span class="bg-gray-100 text-gray-600 text-xs font-bold px-2 py-1 rounded mr-2">{{ $index + 1 }}</span>
                    <h3 class="font-bold text-gray-900">{{ $kapal->nama_kapal }}</h3>
                </div>
            </div>
            
            <div class="flex items-center text-sm text-gray-600 mb-4">
                <i class="fas fa-file-alt mr-2 text-blue-500"></i>
                <span>{{ $kapal->dokumen_kapal_alexindos_count }} Dokumen terdaftar</span>
            </div>

            <div class="pt-3 border-t border-gray-100">
                <a href="{{ route('master-dokumen-kapal-alexindo.show', $kapal->id) }}" class="w-full flex items-center justify-center bg-blue-50 text-blue-600 hover:bg-blue-100 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-eye mr-2"></i> Detail Dokumen
                </a>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center text-gray-500">
            <i class="fas fa-ship text-4xl mb-3 opacity-20"></i>
            <p>Tidak ada data kapal</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
