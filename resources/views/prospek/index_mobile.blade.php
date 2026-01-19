@extends('layouts.app')

@section('content')
<script src="//unpkg.com/alpinejs" defer></script>
<div class="container mx-auto px-4 py-4 pb-20">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Data Prospek</h1>
            <p class="text-xs text-gray-600">Mobile View</p>
        </div>
        <a href="{{ route('prospek.pilih-tujuan') }}" class="bg-blue-600 hover:bg-blue-700 text-white p-2 rounded-full shadow-lg">
            <i class="fas fa-plus"></i>
        </a>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-sm">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- Search & Filter Toggle --}}
    <div class="mb-4" x-data="{ showFilters: {{ request()->hasAny(['status', 'tipe', 'ukuran', 'tujuan']) ? 'true' : 'false' }} }">
        <form method="GET" action="{{ route('prospek.index') }}">
            @if(request()->has('per_page'))
                <input type="hidden" name="per_page" value="{{ request('per_page') }}">
            @endif
            
            <div class="flex gap-2 mb-2">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Cari surat jalan, supir, dll..." 
                       class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm">
                
                <button type="button" @click="showFilters = !showFilters" 
                        class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg border border-gray-300 hover:bg-gray-200">
                    <i class="fas fa-filter"></i>
                </button>
                
                <button type="submit" class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-sm">
                    <i class="fas fa-search"></i>
                </button>
            </div>

            {{-- Expanded Filters --}}
            <div x-show="showFilters" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 class="bg-white p-3 rounded-lg shadow-sm border border-gray-100 space-y-3 mb-2">
                
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full text-sm border-gray-300 rounded-md">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="sudah_muat" {{ request('status') == 'sudah_muat' ? 'selected' : '' }}>Sudah Muat</option>
                        <option value="batal" {{ request('status') == 'batal' ? 'selected' : '' }}>Batal</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Tipe</label>
                        <select name="tipe" class="w-full text-sm border-gray-300 rounded-md">
                            <option value="">Semua</option>
                            <option value="FCL" {{ request('tipe') == 'FCL' ? 'selected' : '' }}>FCL</option>
                            <option value="LCL" {{ request('tipe') == 'LCL' ? 'selected' : '' }}>LCL</option>
                            <option value="CARGO" {{ request('tipe') == 'CARGO' ? 'selected' : '' }}>CARGO</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Ukuran</label>
                        <select name="ukuran" class="w-full text-sm border-gray-300 rounded-md">
                            <option value="">Semua</option>
                            <option value="20" {{ request('ukuran') == '20' ? 'selected' : '' }}>20 Ft</option>
                            <option value="40" {{ request('ukuran') == '40' ? 'selected' : '' }}>40 Ft</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <a href="{{ route('prospek.index') }}" class="text-xs text-gray-500 hover:text-gray-700 underline">Reset Filter</a>
                </div>
            </div>
        </form>
    </div>

    {{-- Cards List --}}
    <div class="space-y-4">
        @forelse($prospeks as $prospek)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden relative">
                {{-- Card Status Stripe --}}
                <div class="absolute left-0 top-0 bottom-0 w-1 
                    {{ $prospek->status == 'aktif' ? 'bg-green-500' : ($prospek->status == 'sudah_muat' ? 'bg-blue-500' : ($prospek->status == 'batal' ? 'bg-red-500' : 'bg-gray-300')) }}">
                </div>

                <div class="p-4 pl-5">
                    {{-- Header Row --}}
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-600 mb-1">
                                {{ $prospek->no_surat_jalan ?? 'No SJ -' }}
                            </span>
                            <div class="font-bold text-gray-900 text-lg leading-tight">
                                {{ $prospek->nomor_kontainer ?? '-' }}
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-1">
                            @php
                                $statusColors = [
                                    'aktif' => 'bg-green-100 text-green-700',
                                    'sudah_muat' => 'bg-blue-100 text-blue-700',
                                    'batal' => 'bg-red-100 text-red-700',
                                ];
                                $statusColor = $statusColors[$prospek->status] ?? 'bg-gray-100 text-gray-700';
                            @endphp
                            <span class="px-2 py-1 rounded-md text-xs font-semibold {{ $statusColor }}">
                                {{ ucfirst(str_replace('_', ' ', $prospek->status)) }}
                            </span>
                        </div>
                    </div>

                    {{-- Main Info --}}
                    <div class="grid grid-cols-2 gap-y-2 gap-x-4 text-sm text-gray-600 mb-3">
                        <div class="col-span-2 flex items-center gap-2">
                            <i class="fas fa-user-tie text-gray-400 w-4"></i>
                            <span class="font-medium text-gray-800">{{ $prospek->nama_supir ?? '-' }}</span>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <i class="fas fa-box text-gray-400 w-4"></i>
                            <span class="truncate">{{ $prospek->barang ?? '-' }}</span>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <i class="fas fa-building text-gray-400 w-4"></i>
                            <span class="truncate">{{ $prospek->pt_pengirim ?? '-' }}</span>
                        </div>

                        <div class="flex items-center gap-2">
                            <i class="fas fa-ruler-combined text-gray-400 w-4"></i>
                            <span>{{ $prospek->ukuran }}' {{ $prospek->tipe }}</span>
                        </div>

                        <div class="flex items-center gap-2">
                            <i class="fas fa-map-marker-alt text-gray-400 w-4"></i>
                            <span>{{ $prospek->tujuan_pengiriman ?? '-' }}</span>
                        </div>
                    </div>

                    {{-- Seal Info --}}
                    <div class="bg-gray-50 rounded-lg p-2 mb-3 flex justify-between items-center text-sm border border-gray-100">
                        <span class="text-gray-500">Seal:</span>
                        <span class="font-mono font-medium {{ !$prospek->no_seal ? 'text-red-400' : 'text-gray-800' }}">
                            {{ $prospek->no_seal ?? 'Belum ada' }}
                        </span>
                    </div>

                    {{-- Actions Footer --}}
                    <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                        <a href="{{ route('prospek.show', $prospek->id) }}" class="text-gray-500 text-sm flex items-center gap-1 hover:text-blue-600">
                            <i class="fas fa-eye"></i> Detail
                        </a>

                        <div class="flex gap-3">
                            <a href="tel:{{ $prospek->supir?->no_telp }}" class="text-green-600 p-2 bg-green-50 rounded-full hover:bg-green-100">
                                <i class="fas fa-phone"></i>
                            </a>
                            
                            @can('prospek-edit')
                                <a href="{{ route('prospek.edit', $prospek->id) }}" class="text-yellow-600 p-2 bg-yellow-50 rounded-full hover:bg-yellow-100">
                                    <i class="fas fa-edit"></i>
                                </a>
                            @endcan

                            <!-- More Actions Dropdown -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="text-gray-600 p-2 bg-gray-50 rounded-full hover:bg-gray-100">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div x-show="open" @click.away="open = false" 
                                     class="absolute right-0 bottom-full mb-2 w-48 bg-white rounded-lg shadow-lg border border-gray-100 py-1 z-10">
                                     
                                    @can('prospek-edit')
                                    <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Ubah Status</div>
                                    <button onclick="updateStatusMobile({{ $prospek->id }}, 'aktif')" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 text-green-600">
                                        <i class="fas fa-check-circle mr-2"></i> Aktif
                                    </button>
                                    <button onclick="updateStatusMobile({{ $prospek->id }}, 'sudah_muat')" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 text-blue-600">
                                        <i class="fas fa-ship mr-2"></i> Sudah Muat
                                    </button>
                                    <button onclick="updateStatusMobile({{ $prospek->id }}, 'batal')" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 text-red-600">
                                        <i class="fas fa-times-circle mr-2"></i> Batal
                                    </button>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-10">
                <div class="bg-gray-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-inbox text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-gray-900 font-medium">Tidak ada data</h3>
                <p class="text-gray-500 text-sm">Coba ubah filter pencarian Anda</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        @if($prospeks->hasPages())
            {{ $prospeks->links('pagination::tailwind') }}
        @endif
    </div>

    {{-- Bottom Floating Stats --}}
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg px-4 py-3 flex justify-around text-center text-xs z-20">
        <div>
            <div class="font-bold text-yellow-600 text-lg">{{ $totalBelumMuat }}</div>
            <div class="text-gray-500">Prospek</div>
        </div>
        <div class="w-px bg-gray-200"></div>
        <div>
            <div class="font-bold text-green-600 text-lg">{{ $totalSudahMuat }}</div>
            <div class="text-gray-500">Dimuat</div>
        </div>
        <div class="w-px bg-gray-200"></div>
        <div>
            <div class="font-bold text-red-600 text-lg">{{ $totalBatal }}</div>
            <div class="text-gray-500">Batal</div>
        </div>
    </div>
</div>

<script>
function updateStatusMobile(id, status) {
    if(!confirm('Ubah status menjadi ' + status + '?')) return;
    
    fetch(`/prospek/${id}/update-status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: status })
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            window.location.reload();
        } else {
            alert('Gagal mengubah status');
        }
    })
    .catch(err => alert('Terjadi kesalahan'));
}
</script>
@endsection
