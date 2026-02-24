@extends('layouts.app')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Tagihan Supir Vendor</h2>
            <p class="text-sm text-gray-500">Daftar tagihan pembayaran untuk supir vendor</p>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <form action="{{ route('tagihan-supir-vendor.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            
            <!-- Search Input -->
            <div class="col-span-1 md:col-span-2 relative">
                <input type="text" name="search" value="{{ request('search') }}" 
                    class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" 
                    placeholder="Cari No Surat Jalan, Nama, atau Rute...">
                <div class="absolute left-3 top-2.5 text-gray-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Status Pembayaran Filter -->
            <div class="col-span-1">
                <select name="status" class="w-full border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 py-2 px-3 text-sm">
                    <option value="">Semua Status Pembayaran</option>
                    <option value="belum_dibayar" {{ request('status') == 'belum_dibayar' ? 'selected' : '' }}>Belum Dibayar</option>
                    <option value="sebagian" {{ request('status') == 'sebagian' ? 'selected' : '' }}>Sebagian</option>
                    <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="col-span-1 flex space-x-2">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg text-sm transition-colors flex justify-center items-center">
                    Terapkan Filter
                </button>
                <a href="{{ route('tagihan-supir-vendor.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg text-sm transition-colors text-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow-sm">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Data Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 font-medium border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 whitespace-nowrap">No Surat Jalan</th>
                        <th class="px-6 py-4">Nama Supir</th>
                        <th class="px-6 py-4">Rute & Kontainer</th>
                        <th class="px-6 py-4">Nominal</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($tagihanSupirVendors as $tagihan)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 font-medium text-blue-600">
                            {{ $tagihan->suratJalan->no_surat_jalan ?? '-' }}
                            <div class="text-xs text-gray-500 font-normal mt-1">{{ optional($tagihan->created_at)->format('d/m/Y H:i') }}</div>
                        </td>
                        <td class="px-6 py-4 font-medium">{{ $tagihan->nama_supir }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center text-gray-600">
                                <span class="truncate max-w-[100px]">{{ $tagihan->dari }}</span>
                                <svg class="w-4 h-4 mx-2 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                                <span class="truncate max-w-[100px]">{{ $tagihan->ke }}</span>
                            </div>
                            <div class="text-xs text-gray-500 mt-1 bg-gray-100 px-2 py-0.5 rounded inline-block">
                                {{ $tagihan->jenis_kontainer ?? '-' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 font-medium">
                            <div class="text-gray-900">Rp {{ number_format($tagihan->nominal + $tagihan->adjustment, 0, ',', '.') }}</div>
                            @if($tagihan->adjustment != 0)
                                <div class="text-[10px] text-gray-500 mt-0.5">
                                    {{ number_format($tagihan->nominal, 0, ',', '.') }} 
                                    <span class="{{ $tagihan->adjustment > 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $tagihan->adjustment > 0 ? '+' : '' }}{{ number_format($tagihan->adjustment, 0, ',', '.') }}
                                    </span>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($tagihan->status_pembayaran == 'lunas')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Lunas
                                </span>
                            @elseif($tagihan->status_pembayaran == 'sebagian')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Sebagian
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Belum Dibayar
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center space-x-3">
                                @if(auth()->user()->can('tagihan-supir-vendor-view'))
                                <a href="{{ route('tagihan-supir-vendor.show', $tagihan->id) }}" class="text-blue-600 hover:text-blue-800 transition-colors" title="Lihat Detail">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                                @endif
                                
                                @if(auth()->user()->can('tagihan-supir-vendor-update'))
                                <a href="{{ route('tagihan-supir-vendor.edit', $tagihan->id) }}" class="text-amber-500 hover:text-amber-700 transition-colors" title="Edit Tagihan">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                @endif

                                @if(auth()->user()->can('tagihan-supir-vendor-delete'))
                                <button type="button" onclick="confirmDelete('{{ $tagihan->id }}')" class="text-red-500 hover:text-red-700 transition-colors" title="Hapus Tagihan">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                                <form id="delete-form-{{ $tagihan->id }}" action="{{ route('tagihan-supir-vendor.destroy', $tagihan->id) }}" method="POST" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center">
                            <div class="flex flex-col flex-auto items-center justify-center p-4">
                                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span class="text-sm text-gray-500 font-medium">Belum ada data Tagihan Supir Vendor!</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($tagihanSupirVendors->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $tagihanSupirVendors->links() }}
        </div>
        @endif
    </div>
</div>

<script>
    function confirmDelete(id) {
        if(confirm('Apakah Anda yakin ingin menghapus tagihan supir vendor ini?')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>
@endsection
