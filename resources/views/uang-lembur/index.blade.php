@extends('layouts.app')

@section('title', 'Master Data Uang Lembur')
@section('page_title', 'Master Data Uang Lembur')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-3xl font-bold text-gray-900">Master Data Uang Lembur</h1>
                    <p class="mt-1 text-sm text-gray-600">Pengaturan tarif lembur dengan Multi-Aturan Jam per Grup</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    @can('payroll-uang-karyawan-create')
                    <a href="{{ route('uang-lembur.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors duration-200 shadow-sm">
                        Tambah Master Tarif
                    </a>
                    @endcan
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Filter Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
            <form action="{{ route('uang-lembur.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-4">
                <div class="w-full sm:w-1/3">
                    <label for="search" class="sr-only">Cari Grup/Sub Grup</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Cari Grup atau Sub Grup..." class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                </div>
                <div>
                    <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm">Cari</button>
                    @if(request('search'))
                        <a href="{{ route('uang-lembur.index') }}" class="ml-2 text-sm text-indigo-600 hover:text-indigo-900">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Cards Section -->
        <div class="grid grid-cols-1 gap-6">
            @forelse ($lemburs as $lembur)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">{{ $lembur->group }} <span class="text-gray-500 text-sm font-normal">/ {{ $lembur->sub_group }}</span></h3>
                    </div>
                    <div class="flex gap-3">
                        @can('payroll-uang-karyawan-edit')
                        <a href="{{ route('uang-lembur.edit', $lembur->id) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">Edit Aturan</a>
                        @endcan
                        
                        @can('payroll-uang-karyawan-delete')
                        <form action="{{ route('uang-lembur.destroy', $lembur->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus master tarif ini berserta semua aturannya?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-900">Hapus</button>
                        </form>
                        @endcan
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-white">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Tipe Hari</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Jam Berlaku</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Satuan</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Nominal</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($lembur->rules as $rule)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 whitespace-nowrap">
                                    @if($rule->tipe_hari == 'Hari Biasa')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">Hari Biasa</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">Hari Libur</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 font-mono">
                                        @if($rule->jam_mulai)
                                            {{ \Carbon\Carbon::parse($rule->jam_mulai)->format('H:i') }} 
                                            - 
                                            {{ $rule->is_sampai_selesai ? 'Selesai' : ($rule->jam_selesai ? \Carbon\Carbon::parse($rule->jam_selesai)->format('H:i') : '') }}
                                        @else
                                            <span class="text-gray-400 italic">Tanpa batas jam</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">Per {{ $rule->satuan }}</div>
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">Rp {{ number_format($rule->nominal, 0, ',', '.') }}</div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-3 text-center text-sm text-gray-500">Tidak ada aturan yang ditambahkan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @empty
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak Ada Data</h3>
                <p class="mt-1 text-sm text-gray-500">Belum ada master tarif lembur yang dibuat.</p>
            </div>
            @endforelse
        </div>
        
        @if($lemburs->hasPages())
            <div class="mt-6">
                {{ $lemburs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
