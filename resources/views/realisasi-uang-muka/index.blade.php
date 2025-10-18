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
                    <p class="mt-2 text-gray-600">Kelola realisasi uang muka pembayaran supir</p>
                </div>

                <div class="flex space-x-3">
                    @can('realisasi-uang-muka-create')
                    <a href="{{ route('realisasi-uang-muka.create') }}"
                       class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                        <i class="fas fa-plus mr-1"></i> Tambah Realisasi Uang Muka
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
                <form method="GET" action="{{ route('realisasi-uang-muka.index') }}">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
                                <option value="">Semua Supir</option>
                                @foreach($supirList as $supir)
                                    <option value="{{ $supir->id }}" {{ request('supir') == $supir->id ? 'selected' : '' }}>
                                        {{ $supir->nama_lengkap }}
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
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Semua Status</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end mt-4 space-x-2">
                        <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                            <i class="fas fa-search mr-1"></i> Filter
                        </button>
                        <a href="{{ route('realisasi-uang-muka.index') }}"
                           class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                            <i class="fas fa-undo mr-1"></i> Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- Tabel Data -->
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                No
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nomor Pembayaran
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Bank/Kas
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Supir
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total Pembayaran
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($realisasiList as $index => $realisasi)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $realisasiList->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $realisasi->nomor_pembayaran }}</div>
                                    <div class="text-sm text-gray-500">{{ $realisasi->jenis_transaksi }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $realisasi->tanggal_pembayaran ? $realisasi->tanggal_pembayaran->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $realisasi->kasBankAkun->nama_akun ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        @php
                                            $supirNames = $realisasi->supirList()->pluck('nama_lengkap')->toArray();
                                        @endphp
                                        @if(count($supirNames) > 2)
                                            {{ implode(', ', array_slice($supirNames, 0, 2)) }}
                                            <span class="text-gray-500">+{{ count($supirNames) - 2 }} lainnya</span>
                                        @else
                                            {{ implode(', ', $supirNames) }}
                                        @endif
                                    </div>
                                    <div class="text-sm text-gray-500">{{ count($supirNames) }} supir</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        Rp {{ number_format($realisasi->total_pembayaran, 0, ',', '.') }}
                                    </div>
                                    @if($realisasi->dp_amount > 0)
                                        <div class="text-xs text-blue-600">
                                            DP: Rp {{ number_format($realisasi->dp_amount, 0, ',', '.') }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($realisasi->status == 'approved')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Disetujui
                                        </span>
                                    @elseif($realisasi->status == 'pending')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Menunggu
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Ditolak
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        @can('realisasi-uang-muka-view')
                                        <a href="{{ route('realisasi-uang-muka.show', $realisasi->id) }}"
                                           class="text-blue-600 hover:text-blue-900" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan

                                        @can('realisasi-uang-muka-view')
                                        <a href="{{ route('realisasi-uang-muka.print', $realisasi->id) }}"
                                           class="text-gray-600 hover:text-gray-900" title="Print" target="_blank">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        @endcan

                                        @can('realisasi-uang-muka-edit')
                                        <a href="{{ route('realisasi-uang-muka.edit', $realisasi->id) }}"
                                           class="text-green-600 hover:text-green-900" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan

                                        @can('realisasi-uang-muka-delete')
                                        <form action="{{ route('realisasi-uang-muka.destroy', $realisasi->id) }}"
                                              method="POST"
                                              class="inline-block"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus realisasi uang muka ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            
                                    <td>
                                        @can('audit-log-view')
                                            <button type="button" class="btn btn-info btn-sm" 
                                                    onclick="showAuditLog({!! json_encode('RealisasiUangMuka') !!}, {!! json_encode($realisasi_uang_muka->id) !!})"
                                                    title="Lihat Riwayat">
                                                <i class="fas fa-history"></i>
                                            </button>
                                        @endcan
                                    </td></tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    Tidak ada data realisasi uang muka
                                </td>
                            
                                    <td>
                                        @can('audit-log-view')
                                            <button type="button" class="btn btn-info btn-sm" 
                                                    onclick="showAuditLog({!! json_encode('RealisasiUangMuka') !!}, {!! json_encode($realisasi_uang_muka->id) !!})"
                                                    title="Lihat Riwayat">
                                                <i class="fas fa-history"></i>
                                            </button>
                                        @endcan
                                    </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $realisasiList->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto submit form when filter changes
    document.querySelectorAll('select[name="supir"], select[name="status"], input[name="tanggal_pembayaran"]').forEach(function(element) {
        element.addEventListener('change', function() {
            // Delay submit untuk UX yang lebih baik
            setTimeout(() => {
                element.closest('form').submit();
            }, 100);
        });
    });

    // Print functionality
    function printPage() {
        window.print();
    }
</script>
@endpush
