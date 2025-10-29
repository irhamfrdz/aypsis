@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-file-contract mr-3 text-blue-600 text-2xl"></i>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Bill of Lading (BL)</h1>
                    <p class="text-gray-600">Kelola data Bill of Lading</p>
                    @if(request('nama_kapal') || request('no_voyage'))
                        <div class="mt-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-filter mr-1"></i>
                                Filter aktif: 
                                @if(request('nama_kapal'))
                                    {{ request('nama_kapal') }}
                                @endif
                                @if(request('nama_kapal') && request('no_voyage'))
                                    | 
                                @endif
                                @if(request('no_voyage'))
                                    Voyage {{ request('no_voyage') }}
                                @endif
                            </span>
                        </div>
                    @endif
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('bl.select') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition duration-200 inline-flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Buat BL Baru
                </a>
            </div>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
        </div>
    @endif

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <form method="GET" action="{{ route('bl.index') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Search --}}
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" 
                       id="search" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Cari nomor BL, kontainer, voyage, kapal, barang..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Actions --}}
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition duration-200">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="{{ route('bl.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition duration-200">
                    <i class="fas fa-undo mr-2"></i>Reset
                </a>
            </div>
        </form>
    </div>

    {{-- BL Table --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">Data BL</h3>
                <div class="text-sm text-gray-600">
                    Total: {{ $bls->total() }} BL
                </div>
            </div>
        </div>

        @if($bls->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'nomor_bl', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                   class="hover:text-gray-700">
                                    Nomor BL
                                    @if(request('sort') === 'nomor_bl')
                                        <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'nomor_kontainer', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                   class="hover:text-gray-700">
                                    Nomor Kontainer
                                    @if(request('sort') === 'nomor_kontainer')
                                        <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                No Seal
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'nama_kapal', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                   class="hover:text-gray-700">
                                    Kapal
                                    @if(request('sort') === 'nama_kapal')
                                        <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'no_voyage', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                   class="hover:text-gray-700">
                                    Voyage
                                    @if(request('sort') === 'no_voyage')
                                        <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'nama_barang', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                   class="hover:text-gray-700">
                                    Nama Barang
                                    @if(request('sort') === 'nama_barang')
                                        <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tipe Kontainer
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tonnage
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                   class="hover:text-gray-700">
                                    Tanggal Dibuat
                                    @if(request('sort') === 'created_at')
                                        <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($bls as $bl)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="nomor-bl-container" data-bl-id="{{ $bl->id }}">
                                        <div class="nomor-bl-display cursor-pointer" title="Klik untuk edit">
                                            <span class="text-sm font-medium text-gray-900">
                                                {{ $bl->nomor_bl ?: '-' }}
                                            </span>
                                            <i class="fas fa-edit ml-1 text-gray-400 text-xs"></i>
                                        </div>
                                        <div class="nomor-bl-edit hidden">
                                            <div class="flex items-center space-x-2">
                                                <input type="text" 
                                                       class="nomor-bl-input w-32 px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                       value="{{ $bl->nomor_bl }}"
                                                       placeholder="Nomor BL">
                                                <button class="save-nomor-bl bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded text-xs">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button class="cancel-nomor-bl bg-gray-500 hover:bg-gray-600 text-white px-2 py-1 rounded text-xs">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $bl->nomor_kontainer ?: '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $bl->no_seal ?: '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $bl->nama_kapal }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $bl->no_voyage }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        {{ Str::limit($bl->nama_barang, 30) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $bl->tipe_kontainer ?: '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $bl->tonnage ? number_format($bl->tonnage, 2) . ' Ton' : '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $bl->created_at->format('d/m/Y H:i') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('bl.show', $bl) }}" 
                                           class="text-blue-600 hover:text-blue-900 transition duration-200">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($bl->prospek)
                                            <a href="{{ route('prospek.show', $bl->prospek) }}" 
                                               class="text-green-600 hover:text-green-900 transition duration-200"
                                               title="Lihat Prospek">
                                                <i class="fas fa-link"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($bls->hasPages())
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    {{ $bls->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <i class="fas fa-file-contract text-6xl text-gray-400 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Data BL</h3>
                <p class="text-gray-600 mb-6">Belum ada Bill of Lading yang dibuat.</p>
                <a href="{{ route('bl.select') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md transition duration-200 inline-flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Buat BL Pertama
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle click on nomor BL display to enable editing
    document.querySelectorAll('.nomor-bl-display').forEach(function(element) {
        element.addEventListener('click', function() {
            const container = this.closest('.nomor-bl-container');
            const display = container.querySelector('.nomor-bl-display');
            const edit = container.querySelector('.nomor-bl-edit');
            const input = container.querySelector('.nomor-bl-input');
            
            // Hide display, show edit
            display.classList.add('hidden');
            edit.classList.remove('hidden');
            
            // Focus on input
            input.focus();
            input.select();
        });
    });

    // Handle save button click
    document.querySelectorAll('.save-nomor-bl').forEach(function(button) {
        button.addEventListener('click', function() {
            const container = this.closest('.nomor-bl-container');
            const blId = container.dataset.blId;
            const input = container.querySelector('.nomor-bl-input');
            const nomorBl = input.value;
            
            // Disable button during request
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            
            // Send AJAX request
            fetch(`/bl/${blId}/nomor-bl`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    nomor_bl: nomorBl
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update display
                    const display = container.querySelector('.nomor-bl-display span');
                    display.textContent = data.nomor_bl;
                    
                    // Hide edit, show display
                    container.querySelector('.nomor-bl-edit').classList.add('hidden');
                    container.querySelector('.nomor-bl-display').classList.remove('hidden');
                    
                    // Show success message
                    showNotification('Nomor BL berhasil diupdate', 'success');
                } else {
                    showNotification(data.message || 'Gagal mengupdate nomor BL', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Terjadi kesalahan saat mengupdate nomor BL', 'error');
            })
            .finally(() => {
                // Re-enable button
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-check"></i>';
            });
        });
    });

    // Handle cancel button click
    document.querySelectorAll('.cancel-nomor-bl').forEach(function(button) {
        button.addEventListener('click', function() {
            const container = this.closest('.nomor-bl-container');
            const display = container.querySelector('.nomor-bl-display');
            const edit = container.querySelector('.nomor-bl-edit');
            const input = container.querySelector('.nomor-bl-input');
            const originalValue = display.querySelector('span').textContent;
            
            // Reset input value
            input.value = originalValue === '-' ? '' : originalValue;
            
            // Hide edit, show display
            edit.classList.add('hidden');
            display.classList.remove('hidden');
        });
    });

    // Handle Enter key to save, Escape key to cancel
    document.querySelectorAll('.nomor-bl-input').forEach(function(input) {
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.closest('.nomor-bl-container').querySelector('.save-nomor-bl').click();
            } else if (e.key === 'Escape') {
                e.preventDefault();
                this.closest('.nomor-bl-container').querySelector('.cancel-nomor-bl').click();
            }
        });
    });

    // Notification function
    function showNotification(message, type) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white`;
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>
                <span>${message}</span>
            </div>
        `;
        
        // Add to page
        document.body.appendChild(notification);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
});
</script>
@endpush