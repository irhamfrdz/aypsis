@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <h1 class="text-2xl font-bold text-gray-900">Pembayaran Pranota Kontainer</h1>
                <div class="mt-4 sm:mt-0">
                    <a href="{{ route('pembayaran-pranota-kontainer.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <i class="fas fa-plus mr-2"></i>
                        Buat Pembayaran Baru
                    </a>
                </div>
            </div>
        </div>

        <!-- Alerts -->
        @if (session('success'))
            <div class="mx-6 mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="mx-6 mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nomor Pembayaran
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tanggal
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Bank
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Jenis Transaksi
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Total Pembayaran
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($pembayaranList as $pembayaran)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $pembayaran->nomor_pembayaran }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    ID: #{{ $pembayaran->id }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('d/m/Y') }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    Kas: {{ \Carbon\Carbon::parse($pembayaran->tanggal_kas)->format('d/m/Y') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $pembayaran->bank }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($pembayaran->jenis_transaksi === 'transfer') bg-blue-100 text-blue-800
                                    @elseif($pembayaran->jenis_transaksi === 'tunai') bg-green-100 text-green-800
                                    @elseif($pembayaran->jenis_transaksi === 'cek') bg-yellow-100 text-yellow-800
                                    @else bg-purple-100 text-purple-800
                                    @endif">
                                    {{ ucfirst($pembayaran->jenis_transaksi) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    Rp {{ number_format($pembayaran->total_tagihan_setelah_penyesuaian ?? $pembayaran->total_pembayaran, 0, ',', '.') }}
                                </div>
                                @if($pembayaran->total_tagihan_penyesuaian != 0)
                                    <div class="text-sm text-gray-500">
                                        ({{ $pembayaran->total_tagihan_penyesuaian > 0 ? '+' : '' }}{{ number_format($pembayaran->total_tagihan_penyesuaian, 0, ',', '.') }})
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <!-- Detail -->
                                    <a href="{{ route('pembayaran-pranota-kontainer.show', $pembayaran->id) }}"
                                       class="text-blue-600 hover:text-blue-900"
                                       title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <!-- Edit -->
                                    <a href="{{ route('pembayaran-pranota-kontainer.edit', $pembayaran->id) }}"
                                       class="text-yellow-600 hover:text-yellow-900"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <!-- Print -->
                                    <a href="{{ route('pembayaran-pranota-kontainer.print', $pembayaran->id) }}"
                                       class="text-purple-600 hover:text-purple-900"
                                       target="_blank"
                                       title="Print">
                                        <i class="fas fa-print"></i>
                                    </a>

                                    <!-- Delete -->
                                    <button onclick="confirmDelete({{ $pembayaran->id }}, '{{ $pembayaran->nomor_pembayaran }}')"
                                            class="text-red-600 hover:text-red-900"
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                Belum ada data pembayaran
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($pembayaranList->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $pembayaranList->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-4">Konfirmasi Hapus</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Apakah Anda yakin ingin menghapus pembayaran <span id="deletePembayaranNumber" class="font-semibold"></span>?
                </p>
                <p class="text-sm text-red-600 mt-2">
                    Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex space-x-3 mt-4">
                    <button type="button" onclick="closeDeleteModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                        Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, nomorPembayaran) {
    document.getElementById('deletePembayaranNumber').textContent = nomorPembayaran;
    document.getElementById('deleteForm').action = `/pembayaran-pranota-kontainer/${id}`;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Close modal when clicking outside
window.onclick = function(event) {
    const deleteModal = document.getElementById('deleteModal');
    if (event.target === deleteModal) {
        closeDeleteModal();
    }
}
</script>
@endsection
