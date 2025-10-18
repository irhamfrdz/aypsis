@extends('layouts.app')


@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

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
                    <p class="mt-2 text-gray-600">Daftar pembayaran uang muka untuk kegiatan outbound</p>
                </div>

                <div class="flex space-x-3">
                    <a href="{{ route('pembayaran-uang-muka.create') }}"
                       class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                        <i class="fas fa-plus mr-1"></i> Tambah Pembayaran
                    </a>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="bg-gray-50 p-4 rounded-lg mb-6">
                <form action="{{ route('pembayaran-uang-muka.index') }}" method="GET" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
                        <!-- Nomor Pembayaran -->
                        <div>
                            <label for="nomor_pembayaran" class="block text-sm font-medium text-gray-700 mb-1">
                                Nomor Pembayaran
                            </label>
                            <input type="text"
                                   name="nomor_pembayaran"
                                   id="nomor_pembayaran"
                                   value="{{ request('nomor_pembayaran') }}"
                                   placeholder="Cari nomor..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Kegiatan -->
                        <div>
                            <label for="kegiatan" class="block text-sm font-medium text-gray-700 mb-1">
                                Kegiatan
                            </label>
                            <input type="text"
                                   name="kegiatan"
                                   id="kegiatan"
                                   value="{{ request('kegiatan') }}"
                                   placeholder="Cari kegiatan..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Supir -->
                        <div>
                            <label for="supir" class="block text-sm font-medium text-gray-700 mb-1">
                                Supir
                            </label>
                            <select name="supir"
                                    id="supir"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Semua Supir --</option>
                                @foreach($supirList as $supir)
                                    <option value="{{ $supir->id }}" {{ request('supir') == $supir->id ? 'selected' : '' }}>
                                        {{ $supir->nama_lengkap }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tanggal -->
                        <div>
                            <label for="tanggal_pembayaran" class="block text-sm font-medium text-gray-700 mb-1">
                                Tanggal Pembayaran
                            </label>
                            <input type="date"
                                   name="tanggal_pembayaran"
                                   id="tanggal_pembayaran"
                                   value="{{ request('tanggal_pembayaran') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                                Status
                            </label>
                            <select name="status"
                                    id="status"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Semua Status --</option>
                                <option value="uang_muka_belum_terpakai" {{ request('status') == 'uang_muka_belum_terpakai' ? 'selected' : '' }}>Belum Terpakai</option>
                                <option value="uang_muka_sebagian_terpakai" {{ request('status') == 'uang_muka_sebagian_terpakai' ? 'selected' : '' }}>Sebagian Terpakai</option>
                                <option value="uang_muka_sudah_terpakai" {{ request('status') == 'uang_muka_sudah_terpakai' ? 'selected' : '' }}>Sudah Terpakai</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <a href="{{ route('pembayaran-uang-muka.index') }}"
                           class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                            <i class="fas fa-undo mr-1"></i> Reset
                        </a>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                            <i class="fas fa-search mr-1"></i> Cari
                        </button>
                    </div>
                </form>
            </div>

            <!-- Alert Messages -->
            @if (session('success'))
                <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Table -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    No
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nomor Pembayaran
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kegiatan
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Supir
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total Pembayaran
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($pembayaranList as $index => $pembayaran)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ ($pembayaranList->currentPage() - 1) * $pembayaranList->perPage() + $loop->iteration }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $pembayaran->nomor_pembayaran }}</div>
                                        <div class="text-sm text-gray-500">{{ $pembayaran->kasBankAkun->nama_akun ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $pembayaran->masterKegiatan->nama_kegiatan ?? 'Kegiatan tidak ditemukan' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($pembayaran->supir_ids)
                                            @php
                                                $supirNames = \App\Models\Karyawan::whereIn('id', $pembayaran->supir_ids)->pluck('nama_lengkap')->toArray();
                                            @endphp
                                            <div class="max-w-xs truncate" title="{{ implode(', ', $supirNames) }}">
                                                {{ count($supirNames) > 1 ? count($supirNames) . ' Supir' : ($supirNames[0] ?? '-') }}
                                            </div>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="font-medium">Rp {{ number_format($pembayaran->total_pembayaran, 0, ',', '.') }}</div>
                                        <div class="text-xs text-gray-500">{{ ucfirst($pembayaran->jenis_transaksi) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusClass = '';
                                            $statusText = '';
                                            switch($pembayaran->status) {
                                                case 'uang_muka_belum_terpakai':
                                                    $statusClass = 'bg-blue-100 text-blue-800';
                                                    $statusText = 'Belum Terpakai';
                                                    break;
                                                case 'uang_muka_sebagian_terpakai':
                                                    $statusClass = 'bg-yellow-100 text-yellow-800';
                                                    $statusText = 'Sebagian Terpakai';
                                                    break;
                                                case 'uang_muka_sudah_terpakai':
                                                    $statusClass = 'bg-green-100 text-green-800';
                                                    $statusText = 'Sudah Terpakai';
                                                    break;
                                                default:
                                                    $statusClass = 'bg-gray-100 text-gray-800';
                                                    $statusText = ucfirst($pembayaran->status);
                                            }
                                        @endphp
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                                            {{ $statusText }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('pembayaran-uang-muka.show', $pembayaran->id) }}"
                                               class="text-blue-600 hover:text-blue-900 transition duration-200"
                                               title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            @if(!$pembayaran->isUsed())
                                                <a href="{{ route('pembayaran-uang-muka.edit', $pembayaran->id) }}"
                                                   class="text-yellow-600 hover:text-yellow-900 transition duration-200"
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a><span class="text-gray-300">|</span>
                                    <!-- Audit Log Link -->
                                    <button type="button"
                                            onclick="showAuditLog('{{ get_class($pembayaran) }}', '{{ $pembayaran->id }}', '{{ $pembayaran->nomor_pembayaran }}')"
                                            class="text-purple-600 hover:text-purple-800 hover:underline font-medium cursor-pointer"
                                            title="Lihat Riwayat Perubahan">
                                        Riwayat
                                    </button>
                                    <span class="text-gray-300">|</span>

                                                <form action="{{ route('pembayaran-uang-muka.destroy', $pembayaran->id) }}"
                                                      method="POST"
                                                      class="inline"
                                                      onsubmit="return confirm('Yakin ingin menghapus pembayaran uang muka ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="text-red-600 hover:text-red-900 transition duration-200"
                                                            title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-gray-400" title="Tidak dapat diedit karena sudah digunakan">
                                                    <i class="fas fa-lock"></i>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                        <div class="flex flex-col items-center justify-center py-8">
                                            <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
                                            <p class="text-lg font-medium text-gray-900 mb-2">Tidak ada data pembayaran uang muka</p>
                                            <p class="text-gray-500 mb-4">Mulai dengan menambah pembayaran uang muka baru</p>
                                            <a href="{{ route('pembayaran-uang-muka.create') }}"
                                               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                                                <i class="fas fa-plus mr-1"></i> Tambah Pembayaran
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($pembayaranList->hasPages())
                    <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                        <div class="flex-1 flex justify-between sm:hidden">
                            @if ($pembayaranList->previousPageUrl())
                                <a href="{{ $pembayaranList->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Previous
                                </a>
                            @endif
                            @if ($pembayaranList->nextPageUrl())
                                <a href="{{ $pembayaranList->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Next
                                </a>
                            @endif
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Menampilkan
                                    <span class="font-medium">{{ $pembayaranList->firstItem() ?? 0 }}</span>
                                    sampai
                                    <span class="font-medium">{{ $pembayaranList->lastItem() ?? 0 }}</span>
                                    dari
                                    <span class="font-medium">{{ $pembayaranList->total() }}</span>
                                    hasil
                                </p>
                            </div>
                            <div>
                                {{ $pembayaranList->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Audit Log Modal -->
@include('components.audit-log-modal')

@endsection
