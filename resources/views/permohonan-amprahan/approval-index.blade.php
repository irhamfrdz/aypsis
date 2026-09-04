@extends('layouts.app')

@section('title', 'Approval Permintaan Amprahan')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-check-double mr-2 text-blue-600"></i>
                Approval Permintaan Amprahan
            </h1>
            <p class="text-gray-600 mt-1">Daftar permintaan amprahan dari ABK yang memerlukan persetujuan</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6">
        <form action="{{ route('approval-permohonan-amprahan.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="w-full md:w-1/4">
                <label for="kapal_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Kapal</label>
                <select name="kapal_id" id="kapal_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 transition-colors">
                    <option value="">-- Semua Kapal --</option>
                    @foreach($kapals as $kapal)
                        <option value="{{ $kapal->id }}" {{ $selectedKapal == $kapal->id ? 'selected' : '' }}>
                            {{ $kapal->nama_kapal }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="w-full md:w-1/4">
                <label for="nomor_voyage" class="block text-sm font-medium text-gray-700 mb-1">Nomor Voyage</label>
                <input type="text" name="nomor_voyage" id="nomor_voyage" value="{{ $selectedVoyage }}" placeholder="Masukkan Nomor Voyage" 
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 transition-colors">
            </div>

            <div class="w-full md:w-1/4">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 transition-colors">
                    <option value="all" {{ $selectedStatus == 'all' ? 'selected' : '' }}>-- Semua Status --</option>
                    <option value="pending" {{ $selectedStatus == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ $selectedStatus == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ $selectedStatus == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <div class="w-full md:w-auto flex space-x-2">
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm transition-colors duration-200 flex items-center">
                    <i class="fas fa-search mr-2"></i> Cari
                </button>
                <a href="{{ route('approval-permohonan-amprahan.index') }}" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors duration-200 flex items-center">
                    <i class="fas fa-undo mr-2"></i> Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-16">No</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kapal / Voyage</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Pemohon</th>
                        <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($permohonans as $index => $item)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                {{ $permohonans->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $item->created_at->format('d M Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $item->created_at->format('H:i') }} WIB</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900">{{ $item->kapal->nama_kapal ?? '-' }}</div>
                                <div class="text-sm text-gray-600">Voyage: <span class="font-medium">{{ $item->nomor_voyage }}</span></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($item->status == 'pending')
                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Pending
                                    </span>
                                @elseif($item->status == 'approved')
                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Disetujui
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $item->user->name ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <div class="flex justify-center space-x-2">
                                    <a href="{{ route('permohonan-amprahan.show', $item->id) }}" class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-colors" title="Lihat Detail">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                    
                                    @if($item->status == 'pending')
                                        <form action="{{ route('approval-permohonan-amprahan.process', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin menyetujui permohonan ini?');">
                                            @csrf
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 px-3 py-1.5 rounded-lg transition-colors" title="Setujui">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        
                                        <form action="{{ route('approval-permohonan-amprahan.process', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin MENOLAK permohonan ini?');">
                                            @csrf
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition-colors" title="Tolak">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                                    <p class="text-lg font-medium text-gray-900">Belum ada data permintaan amprahan</p>
                                    <p class="text-sm mt-1">Gunakan filter di atas untuk mencari data persetujuan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($permohonans->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $permohonans->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
