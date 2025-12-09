@extends('layouts.app')

@section('title', 'Pembayaran Aktivitas Lainnya')

@section('content')
<div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
    <div class="w-full">
        <div class="bg-white shadow-lg rounded-lg">
            <div class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center rounded-t-lg">
                <h3 class="text-xl font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-money-bill-wave mr-3 text-blue-600"></i>
                    Pembayaran Aktivitas Lainnya
                </h3>
                @can('pembayaran-aktivitas-lainnya-create')
                    <a href="{{ route('pembayaran-aktivitas-lainnya.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm rounded-md transition duration-150 ease-in-out">
                        <i class="fas fa-plus mr-2"></i> Tambah Pembayaran
                    </a>
                @endcan
            </div>

            <div class="p-6">
                <!-- Filters -->
                <form method="GET" action="{{ route('pembayaran-aktivitas-lainnya.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div>
                        <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Dari:</label>
                        <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Sampai:</label>
                        <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status:</label>
                        <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Status</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        </select>
                    </div>
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari:</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Nomor pembayaran, aktivitas...">
                    </div>
                    <div class="flex gap-2 md:col-span-4">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm rounded-md transition duration-150 ease-in-out">
                            <i class="fas fa-search mr-2"></i> Filter
                        </button>
                        <a href="{{ route('pembayaran-aktivitas-lainnya.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium text-sm rounded-md transition duration-150 ease-in-out">
                            <i class="fas fa-redo mr-2"></i> Reset
                        </a>
                    </div>
                </form>

                <!-- Table -->
                <div class="overflow-x-auto shadow-sm border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Pembayaran</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Pembayaran</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aktivitas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat Oleh</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($pembayaran as $index => $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $pembayaran->firstItem() + $index }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="font-semibold text-blue-600">{{ $item->nomor_pembayaran }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->tanggal_pembayaran->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="font-semibold text-green-600">
                                            Rp {{ number_format($item->total_pembayaran, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'draft' => 'gray',
                                                'pending' => 'yellow',
                                                'approved' => 'blue',
                                                'rejected' => 'red',
                                                'paid' => 'green'
                                            ];
                                            $color = $statusColors[$item->status] ?? 'gray';
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $color }}-100 text-{{ $color }}-800">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate" title="{{ $item->aktivitas_pembayaran }}">
                                        {{ Str::limit($item->aktivitas_pembayaran, 50) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $item->creator->name ?? '-' }}
                                        <br><small class="text-gray-500">{{ $item->created_at ? $item->created_at->format('d/m/Y') : '-' }}</small>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            @can('pembayaran-aktivitas-lainnya-view')
                                                <a href="{{ route('pembayaran-aktivitas-lainnya.show', $item) }}" class="text-blue-600 hover:text-blue-900" title="Lihat">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endcan

                                            @can('pembayaran-aktivitas-lainnya-update')
                                                @if($item->status === 'draft')
                                                    <a href="{{ route('pembayaran-aktivitas-lainnya.edit', $item) }}" class="text-amber-600 hover:text-amber-900" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                            @endcan

                                            @can('pembayaran-aktivitas-lainnya-delete')
                                                @if($item->status === 'draft')
                                                    <button type="button" onclick="confirmDelete({{ $item->id }}, '{{ $item->nomor_pembayaran }}')" class="text-red-600 hover:text-red-900" title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            @endcan

                                            @can('pembayaran-aktivitas-lainnya-approve')
                                                @if($item->status === 'pending')
                                                    <form action="{{ route('pembayaran-aktivitas-lainnya.approve', $item) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('POST')
                                                        <button type="submit" class="text-green-600 hover:text-green-900" title="Approve" onclick="return confirm('Apakah Anda yakin ingin menyetujui pembayaran ini?')">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                @elseif($item->status === 'approved')
                                                    <form action="{{ route('pembayaran-aktivitas-lainnya.mark-as-paid', $item) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('POST')
                                                        <button type="submit" class="text-purple-600 hover:text-purple-900" title="Tandai Sudah Bayar" onclick="return confirm('Apakah Anda yakin ingin menandai pembayaran ini sebagai sudah dibayar?')">
                                                            <i class="fas fa-money-bill-wave"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center">
                                        <div class="text-gray-500">
                                            <i class="fas fa-inbox text-4xl mb-4"></i>
                                            <p class="text-lg">Belum ada pembayaran aktivitas lainnya yang dibuat.</p>
                                            @can('pembayaran-aktivitas-lainnya-create')
                                                <p class="mt-2">
                                                    <a href="{{ route('pembayaran-aktivitas-lainnya.create') }}" class="text-blue-600 hover:text-blue-900">Buat pembayaran pertama</a>
                                                </p>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($pembayaran->hasPages())
                    <div class="mt-6">
                        {{ $pembayaran->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-4" id="deleteModalTitle">Konfirmasi Hapus</h3>
            <p class="text-sm text-gray-500 mt-2" id="deleteModalMessage">Apakah Anda yakin ingin menghapus pembayaran ini?</p>
        </div>
        <div class="flex justify-center mt-6 space-x-4">
            <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">Batal</button>
            <form id="deleteForm" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Hapus</button>
            </form>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, nomor) {
    document.getElementById('deleteModalTitle').textContent = 'Hapus Pembayaran ' + nomor;
    document.getElementById('deleteModalMessage').textContent = 'Apakah Anda yakin ingin menghapus pembayaran ' + nomor + '? Tindakan ini tidak dapat dibatalkan.';
    document.getElementById('deleteForm').action = `/pembayaran-aktivitas-lainnya/${id}`;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});
</script>
@endsection