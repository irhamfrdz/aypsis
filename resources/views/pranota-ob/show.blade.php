@extends('layouts.app')

@section('title', 'Detail Pranota OB')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="bg-blue-600 text-white px-6 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-xl font-semibold flex items-center">
                    <i class="fas fa-file-invoice mr-2"></i>
                    Detail Pranota OB - {{ $pranotaOb->nomor_pranota }}
                </h1>
                <div class="flex space-x-2">
                    <a href="{{ route('pranota-ob.index') }}" class="bg-white text-blue-600 hover:bg-gray-50 px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Kembali
                    </a>
                    @if($pranotaOb->status === 'draft')
                        @can('pranota-ob-update')
                            <a href="{{ route('pranota-ob.edit', $pranotaOb) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                                <i class="fas fa-edit mr-1"></i>
                                Edit
                            </a>
                        @endcan
                    @endif
                    @can('pranota-ob-delete')
                        @if($pranotaOb->status === 'draft')
                            <button type="button" onclick="confirmDelete()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                                <i class="fas fa-trash mr-1"></i>
                                Hapus
                            </button>
                        @endif
                    @endcan
                </div>
            </div>
        </div>

        <div class="p-6">
            <!-- Status Badge -->
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <span class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-full 
                        {{ $pranotaOb->status === 'approved' ? 'bg-green-100 text-green-800' : 
                           ($pranotaOb->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                        <i class="fas {{ $pranotaOb->status === 'approved' ? 'fa-check-circle' : ($pranotaOb->status === 'rejected' ? 'fa-times-circle' : 'fa-clock') }} mr-2"></i>
                        {{ ucfirst($pranotaOb->status) }}
                    </span>

                </div>
                @if($pranotaOb->status === 'approved' && $pranotaOb->approved_at)
                    <div class="text-sm text-gray-600">
                        <i class="fas fa-calendar-check mr-1"></i>
                        Disetujui: {{ $pranotaOb->approved_at->format('d/m/Y H:i') }}
                        @if($pranotaOb->approver)
                            oleh <strong>{{ $pranotaOb->approver->name }}</strong>
                        @endif
                    </div>
                @endif
            </div>
            
            @if($pranotaOb->keterangan)
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-blue-600 mb-4 flex items-center">
                        <i class="fas fa-comment mr-2"></i>
                        Keterangan
                    </h2>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-gray-700 mb-0">{{ $pranotaOb->keterangan }}</p>
                    </div>
                </div>
            @endif

            <!-- Items List -->
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-purple-600 mb-4 flex items-center">
                    <i class="fas fa-list mr-2"></i>
                    Daftar Tagihan OB ({{ $pranotaOb->items->count() }} items)
                </h2>
                
                <form method="POST" action="{{ route('pranota-ob.update-dp', $pranotaOb) }}" id="dpForm">
                    @csrf
                    <div class="overflow-x-auto shadow ring-1 ring-black ring-opacity-5 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kapal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Voyage</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Kontainer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Supir</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barang</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Biaya</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DP Saat Ini</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Input DP</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php
                                // Group items by supir untuk menggabungkan DP
                                $groupedBySupir = $pranotaOb->items->filter(function($item) {
                                    return $item->tagihanOb !== null;
                                })->groupBy(function($item) {
                                    return $item->tagihanOb->nama_supir;
                                });
                            @endphp
                            
                            @forelse ($groupedBySupir as $supirName => $items)
                                @php
                                    $firstTagihan = $items->first()->tagihanOb;
                                    $totalBiaya = $items->sum(function($item) { return $item->tagihanOb->biaya ?? 0; });
                                    $totalDp = $items->sum(function($item) { return $item->tagihanOb->dp ?? 0; });
                                    $totalSisa = $totalBiaya - $totalDp;
                                    $kontainers = $items->pluck('tagihanOb.nomor_kontainer')->join(', ');
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $loop->iteration }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $firstTagihan->kapal }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $firstTagihan->voyage }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        @foreach($items as $item)
                                            <code class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs font-mono inline-block mb-1">{{ $item->tagihanOb->nomor_kontainer }}</code>
                                            @if(!$loop->last)<br>@endif
                                        @endforeach
                                        @if($items->count() > 1)
                                            <span class="text-xs text-gray-500">({{ $items->count() }} kontainer)</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $supirName }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ Str::limit($firstTagihan->barang, 30) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $firstTagihan->status_kontainer === 'full' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($firstTagihan->status_kontainer) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        Rp {{ number_format($totalBiaya, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                                        Rp {{ number_format($totalDp, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @can('pranota-ob-update')
                                            <input type="number" 
                                                   name="dp_values[{{ $supirName }}]" 
                                                   value="{{ $totalDp }}"
                                                   min="0" 
                                                   max="{{ $totalBiaya }}"
                                                   class="w-32 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm"
                                                   placeholder="Masukkan DP">
                                        @else
                                            <span class="text-sm text-gray-500">-</span>
                                        @endcan
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600">
                                        Rp {{ number_format($totalSisa, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @can('tagihan-ob-view')
                                            @if($items->count() === 1)
                                                <a href="{{ route('tagihan-ob.show', $firstTagihan) }}" 
                                                   class="text-blue-600 hover:text-blue-900 bg-blue-100 hover:bg-blue-200 px-2 py-1 rounded" 
                                                   title="Lihat Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @else
                                                <span class="text-xs text-gray-500">{{ $items->count() }} tagihan</span>
                                            @endif
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-inbox text-gray-400 text-4xl mb-4"></i>
                                            <p class="text-gray-500 text-lg">Belum ada item dalam pranota ini</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($pranotaOb->items->count() > 0)
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-right text-sm font-semibold text-gray-700">
                                        Total Biaya:
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                        Rp {{ number_format($pranotaOb->items->sum(function($item) {
                                            return $item->tagihanOb->biaya ?? 0;
                                        }), 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-blue-600">
                                        Rp {{ number_format($pranotaOb->items->sum(function($item) {
                                            return $item->tagihanOb->dp ?? 0;
                                        }), 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @can('pranota-ob-update')
                                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                                                <i class="fas fa-save mr-1"></i>
                                                Simpan DP
                                            </button>
                                        @endcan
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600">
                                        Rp {{ number_format($pranotaOb->items->sum(function($item) {
                                            return ($item->tagihanOb->biaya ?? 0) - ($item->tagihanOb->dp ?? 0);
                                        }), 0, ',', '.') }}
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
                </form>
            </div>

            <!-- Timeline / History -->
            <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                <h3 class="text-md font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-history mr-2"></i>
                    Riwayat
                </h3>
                <div class="space-y-3">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-8 w-8 rounded-full bg-blue-100 text-blue-600">
                                <i class="fas fa-plus text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-gray-700">
                                Pranota dibuat oleh <strong>{{ $pranotaOb->creator->name ?? 'System' }}</strong>
                            </p>
                            <p class="text-xs text-gray-500">{{ $pranotaOb->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                    
                    @if($pranotaOb->status === 'approved' && $pranotaOb->approved_at)
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-8 w-8 rounded-full bg-green-100 text-green-600">
                                    <i class="fas fa-check text-sm"></i>
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-gray-700">
                                    Pranota disetujui oleh <strong>{{ $pranotaOb->approver->name ?? 'System' }}</strong>
                                </p>
                                <p class="text-xs text-gray-500">{{ $pranotaOb->approved_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    @elseif($pranotaOb->status === 'rejected')
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-8 w-8 rounded-full bg-red-100 text-red-600">
                                    <i class="fas fa-times text-sm"></i>
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-gray-700">
                                    Pranota ditolak
                                </p>
                                @if($pranotaOb->updated_at)
                                    <p class="text-xs text-gray-500">{{ $pranotaOb->updated_at->format('d M Y, H:i') }}</p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div class="sm:flex sm:items-start">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Konfirmasi Hapus
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            Apakah Anda yakin ingin menghapus pranota OB ini?
                        </p>
                        <p class="text-sm text-red-600 mt-1">
                            Data yang sudah dihapus tidak dapat dikembalikan.
                        </p>
                    </div>
                </div>
            </div>
            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                <form id="deleteForm" method="POST" action="{{ route('pranota-ob.destroy', $pranotaOb) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Hapus
                    </button>
                </form>
                <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm" onclick="closeDeleteModal()">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete() {
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('deleteModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});

// Close modal with ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('deleteModal');
        if (modal && !modal.classList.contains('hidden')) {
            closeDeleteModal();
        }
    }
});
</script>
@endpush
