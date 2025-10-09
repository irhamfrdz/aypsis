@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-medium text-gray-900">
                        {{ $title }}
                    </h1>
                    <p class="mt-2 text-gray-600">Kelola pembayaran Down Payment Out Bound (OB)</p>
                </div>

                <div class="flex space-x-3">
                    @can('pembayaran-dp-ob-create')
                    <a href="{{ route('pembayaran-dp-ob.create') }}"
                       class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                        <i class="fas fa-plus mr-1"></i> Tambah Pembayaran DP OB
                    </a>
                    @endcan

                    <button onclick="window.print()"
                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                        <i class="fas fa-print mr-1"></i> Print
                    </button>

                    <a href="#"
                       class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                        <i class="fas fa-download mr-1"></i> Export Excel
                    </a>
                </div>
            </div>

            <!-- Filter dan Search -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <form method="GET" action="{{ route('pembayaran-dp-ob.index') }}">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Pembayaran</label>
                            <input type="text" name="nomor_pembayaran"
                                   value="{{ request('nomor_pembayaran') }}"
                                   placeholder="Cari nomor pembayaran..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Supir</label>
                            <select name="supir" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih Supir --</option>
                                @foreach($supirList as $supir)
                                    <option value="{{ $supir->id }}" {{ request('supir') == $supir->id ? 'selected' : '' }}>
                                        {{ $supir->nama_lengkap }} ({{ $supir->nik }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pembayaran</label>
                            <input type="date" name="tanggal_pembayaran"
                                   value="{{ request('tanggal_pembayaran') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status DP</label>
                            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Semua Status --</option>
                                <option value="dp_belum_terpakai" {{ request('status') == 'dp_belum_terpakai' ? 'selected' : '' }}>
                                    DP Belum Terpakai
                                </option>
                                <option value="dp_terpakai" {{ request('status') == 'dp_terpakai' ? 'selected' : '' }}>
                                    DP Terpakai
                                </option>
                            </select>
                        </div>

                        <div class="flex items-end">
                            <button type="submit"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2 transition duration-200">
                                <i class="fas fa-search mr-1"></i> Cari
                            </button>
                            <a href="{{ route('pembayaran-dp-ob.index') }}"
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                                <i class="fas fa-times mr-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tabel Data -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    No
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nomor Pembayaran
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Supir
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kas/Bank
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Jumlah Pembayaran
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Keterangan
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status DP
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($pembayaranList as $index => $pembayaran)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ ($pembayaranList->currentPage() - 1) * $pembayaranList->perPage() + $loop->iteration }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $pembayaran->nomor_pembayaran }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        <div class="flex flex-wrap gap-1">
                                            @php
                                                $supirList = \App\Models\Karyawan::whereIn('id', $pembayaran->supir_ids ?? [])->get();
                                            @endphp
                                            @foreach($supirList->take(2) as $supir)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ $supir->nama_lengkap }}
                                                </span>
                                            @endforeach
                                            @if($supirList->count() > 2)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    +{{ $supirList->count() - 2 }} lagi
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="font-medium">{{ $pembayaran->kasBankAkun ? $pembayaran->kasBankAkun->nama_akun : '-' }}</div>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $pembayaran->jenis_transaksi == 'debit' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($pembayaran->jenis_transaksi) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="font-medium">Rp {{ number_format($pembayaran->total_pembayaran, 0, ',', '.') }}</div>
                                        <div class="text-xs text-gray-400">
                                            {{ count($pembayaran->supir_ids ?? []) }} supir × Rp {{ number_format($pembayaran->jumlah_per_supir, 0, ',', '.') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        @if($pembayaran->keterangan)
                                            <div class="max-w-xs truncate" title="{{ $pembayaran->keterangan }}">
                                                {{ $pembayaran->keterangan }}
                                            </div>
                                        @else
                                            <span class="text-gray-400 italic">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($pembayaran->status == 'dp_belum_terpakai')
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <i class="fas fa-clock mr-1"></i>
                                                DP Belum Terpakai
                                            </span>
                                        @elseif($pembayaran->status == 'dp_terpakai')
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                DP Terpakai
                                            </span>
                                            @if($pembayaran->pembayaranObs && $pembayaran->pembayaranObs->count() > 0)
                                                <div class="text-xs text-gray-500 mt-1">
                                                    Digunakan untuk: {{ $pembayaran->pembayaranObs->count() }} pembayaran OB
                                                </div>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            @can('pembayaran-dp-ob-view')
                                            <a href="{{ route('pembayaran-dp-ob.show', $pembayaran->id) }}"
                                               class="text-blue-600 hover:text-blue-900" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan

                                            @can('pembayaran-dp-ob-edit')
                                            <a href="{{ route('pembayaran-dp-ob.edit', $pembayaran->id) }}"
                                               class="text-green-600 hover:text-green-900" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan

                                            @can('pembayaran-dp-ob-delete')
                                            <button onclick="confirmDelete('{{ $pembayaran->id }}')"
                                                    class="text-red-600 hover:text-red-900" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center py-12">
                                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                            </svg>
                                            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Data</h3>
                                            <p class="text-gray-500 mb-4">Belum ada data pembayaran DP OB yang tersedia.</p>
                                            @can('pembayaran-dp-ob-create')
                                            <a href="{{ route('pembayaran-dp-ob.create') }}"
                                               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                                                <i class="fas fa-plus mr-1"></i> Tambah Pembayaran DP OB Pertama
                                            </a>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($pembayaranList->hasPages())
                <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                    <div class="flex-1 flex justify-between sm:hidden">
                        @if ($pembayaranList->onFirstPage())
                            <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-white cursor-not-allowed">
                                Sebelumnya
                            </span>
                        @else
                            <a href="{{ $pembayaranList->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Sebelumnya
                            </a>
                        @endif

                        @if ($pembayaranList->hasMorePages())
                            <a href="{{ $pembayaranList->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Selanjutnya
                            </a>
                        @else
                            <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-white cursor-not-allowed">
                                Selanjutnya
                            </span>
                        @endif
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Menampilkan <span class="font-medium">{{ $pembayaranList->firstItem() ?? 0 }}</span>
                                sampai <span class="font-medium">{{ $pembayaranList->lastItem() ?? 0 }}</span>
                                dari <span class="font-medium">{{ $pembayaranList->total() }}</span> hasil
                            </p>
                        </div>
                        <div>
                            {{ $pembayaranList->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Modal Konfirmasi Hapus --}}
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-2">Konfirmasi Hapus</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Apakah Anda yakin ingin menghapus data pembayaran DP OB ini? Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <button id="confirmDeleteBtn" class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-auto mr-2 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300">
                    Ya, Hapus
                </button>
                <button onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-auto hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let deleteItemId = null;

function confirmDelete(id) {
    deleteItemId = id;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    deleteItemId = null;
    document.getElementById('deleteModal').classList.add('hidden');
}

// Handle confirm delete
document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (deleteItemId) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('pembayaran-dp-ob.index') }}/${deleteItemId}`;

        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';

        // Add method override
        const methodOverride = document.createElement('input');
        methodOverride.type = 'hidden';
        methodOverride.name = '_method';
        methodOverride.value = 'DELETE';

        form.appendChild(csrfToken);
        form.appendChild(methodOverride);
        document.body.appendChild(form);

        form.submit();
    }
});

// Close modal when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});
</script>

@endsection
