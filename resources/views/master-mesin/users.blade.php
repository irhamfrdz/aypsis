@extends('layouts.app')

@section('title', 'Data User Mesin ADMS')
@section('page_title', 'Data User Mesin')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-3xl font-bold text-gray-900">Data User Mesin</h1>
                    <p class="mt-1 text-sm text-gray-600">Daftar Karyawan yang ditarik (tersinkronisasi) dari Mesin Fingerprint/Wajah</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('master.mesin.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-white text-gray-700 text-sm font-medium rounded-lg border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200 shadow-sm">
                        <i class="fa-solid fa-arrow-left mr-2"></i> Kembali ke Kelola Mesin
                    </a>
                    
                    <a href="{{ route('mesin-users.export', request()->all()) }}" class="inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200 shadow-sm">
                        <i class="fa-solid fa-file-excel mr-2"></i> Ekspor ke Excel
                    </a>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <form action="{{ route('mesin-users.index') }}" method="GET" class="flex flex-col lg:flex-row gap-4 items-end">
                <div class="flex-1 w-full">
                    <label for="sn" class="block text-sm font-medium text-gray-700 mb-2">Filter Berdasarkan Mesin</label>
                    <select name="sn" id="sn" class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200">
                        <option value="">Semua Mesin</option>
                        @foreach($mesins as $mesin)
                            <option value="{{ $mesin->kode_mesin }}" {{ request('sn') == $mesin->kode_mesin ? 'selected' : '' }}>
                                {{ $mesin->nama_mesin }} ({{ $mesin->kode_mesin }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                        Filter
                    </button>
                    @if(request('sn'))
                        <a href="{{ route('mesin-users.index') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gray-500 text-white text-sm font-medium rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serial Number</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PIN / NIK</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama di Mesin</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hak Akses</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Terakhir Sinkron</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 text-sm">
                        @forelse ($users as $index => $user)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                    {{ $users->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                                    {{ $user->sn }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-bold text-indigo-600">
                                    {{ $user->pin }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-900">
                                    {{ $user->name ?: '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                    @if($user->privilege == '14')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Admin</span>
                                    @elseif($user->privilege == '2')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Enroller</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">User Biasa</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500 text-xs">
                                    {{ $user->updated_at->format('d M Y, H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="fa-solid fa-users-slash text-4xl text-gray-300 mb-4"></i>
                                        <h3 class="text-sm font-medium text-gray-900 mb-1">Belum Ada Data</h3>
                                        <p class="text-sm text-gray-500">Belum ada user mesin yang ditarik. Silakan klik 'Tarik User' pada menu Kelola Mesin.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $users->links() }}
            </div>
            @endif
        </div>

    </div>
</div>
@endsection
