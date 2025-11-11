@extends('layouts.app')

@section('title', 'Detail Tagihan OB')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="bg-blue-600 text-white px-6 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-xl font-semibold flex items-center">
                    <i class="fas fa-eye mr-2"></i>
                    Detail Tagihan OB #{{ $tagihanOb->id }}
                </h1>
                <div class="flex space-x-2">
                    <a href="{{ route('tagihan-ob.index') }}" class="bg-white text-blue-600 hover:bg-gray-50 px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Kembali
                    </a>
                    @can('tagihan-ob-update')
                        <a href="{{ route('tagihan-ob.edit', $tagihanOb) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                            <i class="fas fa-edit mr-1"></i>
                            Edit
                        </a>
                    @endcan
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Left Column - Basic Info -->
                <div>
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold text-blue-600 mb-4 flex items-center">
                            <i class="fas fa-ship mr-2"></i>
                            Informasi Kapal & Kontainer
                        </h2>
                        
                        <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <span class="font-medium text-gray-700">Nama Kapal:</span>
                                <span class="text-gray-900 font-semibold">{{ $tagihanOb->kapal }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <span class="font-medium text-gray-700">Voyage:</span>
                                <span class="text-gray-900 font-semibold">{{ $tagihanOb->voyage }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <span class="font-medium text-gray-700">No. Kontainer:</span>
                                <code class="bg-gray-200 text-gray-800 px-2 py-1 rounded text-sm font-mono">{{ $tagihanOb->nomor_kontainer }}</code>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <span class="font-medium text-gray-700">Status Kontainer:</span>
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $tagihanOb->status_kontainer === 'full' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($tagihanOb->status_kontainer) }}
                                    @if($tagihanOb->status_kontainer === 'full')
                                        (Tarik Isi)
                                    @else
                                        (Tarik Kosong)
                                    @endif
                                </span>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span class="font-medium text-gray-700">Jenis Barang:</span>
                                <span class="text-gray-900 font-semibold">{{ $tagihanOb->barang }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Financial Info -->
                <div>
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold text-green-600 mb-4 flex items-center">
                            <i class="fas fa-money-bill-wave mr-2"></i>
                            Informasi Finansial & Status
                        </h2>
                        
                        <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <span class="font-medium text-gray-700">Nama Supir:</span>
                                <span class="text-gray-900 font-semibold">{{ $tagihanOb->nama_supir }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <span class="font-medium text-gray-700">Biaya OB:</span>
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-green-600">
                                        Rp {{ number_format($tagihanOb->biaya, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <span class="font-medium text-gray-700">Status Pembayaran:</span>
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                                    {{ $tagihanOb->status_pembayaran === 'paid' ? 'bg-green-100 text-green-800' : 
                                       ($tagihanOb->status_pembayaran === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($tagihanOb->status_pembayaran) }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <span class="font-medium text-gray-700">Tanggal Dibuat:</span>
                                <span class="text-gray-900 font-semibold">{{ $tagihanOb->created_at->format('d/m/Y H:i:s') }}</span>
                            </div>
                            @if($tagihanOb->creator)
                                <div class="flex justify-between items-center py-2">
                                    <span class="font-medium text-gray-700">Dibuat Oleh:</span>
                                    <span class="text-gray-900 font-semibold">{{ $tagihanOb->creator->name }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if($tagihanOb->keterangan)
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-blue-600 mb-4 flex items-center">
                        <i class="fas fa-comment mr-2"></i>
                        Keterangan
                    </h2>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-gray-700 mb-0">{{ $tagihanOb->keterangan }}</p>
                    </div>
                </div>
            @endif

            @if($tagihanOb->bl)
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-orange-600 mb-4 flex items-center">
                        <i class="fas fa-file-alt mr-2"></i>
                        Informasi Bill of Lading (BL)
                    </h2>
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div class="space-y-2">
                                <div><span class="font-medium text-gray-700">Nomor BL:</span> <span class="font-semibold">{{ $tagihanOb->bl->nomor_bl }}</span></div>
                                <div><span class="font-medium text-gray-700">Kapal BL:</span> <span class="font-semibold">{{ $tagihanOb->bl->kapal }}</span></div>
                            </div>
                            <div class="space-y-2">
                                @if($tagihanOb->bl->created_at)
                                    <div><span class="font-medium text-gray-700">Tanggal BL:</span> <span class="font-semibold">{{ $tagihanOb->bl->created_at->format('d/m/Y') }}</span></div>
                                @endif
                            </div>
                        </div>
                        @can('bl-view')
                            <div>
                                <a href="{{ route('bl.show', $tagihanOb->bl) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                                    <i class="fas fa-external-link-alt mr-1"></i>
                                    Lihat Detail BL
                                </a>
                            </div>
                        @endcan
                    </div>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="border-t border-gray-200 px-6 py-4 bg-gray-50">
                <div class="flex flex-col sm:flex-row justify-between items-center space-y-3 sm:space-y-0">
                    <div>
                        @can('tagihan-ob-delete')
                            <button type="button" 
                                    class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out" 
                                    onclick="confirmDelete()">
                                <i class="fas fa-trash mr-1"></i>
                                Hapus Tagihan
                            </button>
                        @endcan
                    </div>
                    
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('tagihan-ob.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                            <i class="fas fa-list mr-1"></i>
                            Daftar Tagihan
                        </a>
                        @can('tagihan-ob-update')
                            <a href="{{ route('tagihan-ob.edit', $tagihanOb) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                                <i class="fas fa-edit mr-1"></i>
                                Edit Data
                            </a>
                        @endcan
                        @can('tagihan-ob-create')
                            <a href="{{ route('tagihan-ob.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                                <i class="fas fa-plus mr-1"></i>
                                Tambah Baru
                            </a>
                        @endcan
                    </div>
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
                        <p class="text-sm text-gray-500 mb-4">
                            Apakah Anda yakin ingin menghapus tagihan OB ini?
                        </p>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
                            <div class="text-sm">
                                <p class="font-medium text-yellow-800 mb-2">Data yang akan dihapus:</p>
                                <ul class="text-yellow-700 space-y-1">
                                    <li><strong>Kapal:</strong> {{ $tagihanOb->kapal }} ({{ $tagihanOb->voyage }})</li>
                                    <li><strong>Kontainer:</strong> {{ $tagihanOb->nomor_kontainer }}</li>
                                    <li><strong>Supir:</strong> {{ $tagihanOb->nama_supir }}</li>
                                </ul>
                            </div>
                        </div>
                        <p class="text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Data yang sudah dihapus tidak dapat dikembalikan.
                        </p>
                    </div>
                </div>
            </div>
            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                <form action="{{ route('tagihan-ob.destroy', $tagihanOb) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        <i class="fas fa-trash mr-1"></i>
                        Ya, Hapus
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
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('deleteModal');
        const modalContent = modal.querySelector('.inline-block');
        
        modal.addEventListener('click', function(e) {
            if (!modalContent.contains(e.target)) {
                closeDeleteModal();
            }
        });
        
        // Close modal with ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDeleteModal();
            }
        });
    });
</script>
@endpush