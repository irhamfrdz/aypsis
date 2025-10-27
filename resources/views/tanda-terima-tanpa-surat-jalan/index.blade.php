@extends('layouts.app')

@section('title', 'Tanda Terima Tanpa Surat Jalan')
@section('page_title', 'Tanda Terima Tanpa Surat Jalan')

@section('content')
<div class="container mx-auto px-4 py-4">
    <div class="max-w-7xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-4 border-b border-gray-200">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Tanda Terima Tanpa Surat Jalan</h1>
                <p class="text-xs text-gray-600 mt-1">Kelola tanda terima yang tidak memerlukan surat jalan</p>
            </div>
            <div class="flex gap-4 text-sm">
                <div class="text-center">
                    <div class="text-lg font-semibold text-blue-600">{{ $stats['total'] ?? 0 }}</div>
                    <div class="text-gray-500 text-xs">Total</div>
                </div>
                <div class="text-center">
                    <div class="text-lg font-semibold text-gray-600">{{ $stats['draft'] ?? 0 }}</div>
                    <div class="text-gray-500 text-xs">Draft</div>
                </div>
                <div class="text-center">
                    <div class="text-lg font-semibold text-yellow-600">{{ $stats['terkirim'] ?? 0 }}</div>
                    <div class="text-gray-500 text-xs">Terkirim</div>
                </div>
                <div class="text-center">
                    <div class="text-lg font-semibold text-green-600">{{ $stats['selesai'] ?? 0 }}</div>
                    <div class="text-gray-500 text-xs">Selesai</div>
                </div>
            </div>
        </div>

        <div class="p-4">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md" role="alert">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="font-medium text-sm">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="font-medium text-sm">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Search and Filter -->
            <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                <form method="GET" action="{{ route('tanda-terima-tanpa-surat-jalan.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Cari nomor, penerima, pengirim..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe</label>
                        <select name="tipe" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Semua Tipe</option>
                            <option value="fcl" {{ request('tipe') == 'fcl' ? 'selected' : '' }}>FCL (Full Container Load)</option>
                            <option value="cargo" {{ request('tipe') == 'cargo' ? 'selected' : '' }}>Cargo</option>
                            <option value="lcl" {{ request('tipe') == 'lcl' ? 'selected' : '' }}>LCL (Less Container Load)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div class="md:col-span-4 flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Cari
                        </button>
                        <a href="{{ route('tanda-terima-tanpa-surat-jalan.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 text-sm">
                            Reset
                        </a>
                        @can('tanda-terima-tanpa-surat-jalan-create')
                            <a href="{{ route('tanda-terima-tanpa-surat-jalan.pilih-tipe') }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm ml-auto">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Tambah Baru
                            </a>
                        @endcan
                    </div>
                </form>
            </div>

            @if($tandaTerimas->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Tanda Terima</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penerima</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengirim</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Barang</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tujuan Ambil</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tujuan Kirim</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($tandaTerimas as $tandaTerima)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if(isset($isLclData) && $isLclData)
                                            <div class="text-sm font-medium text-gray-900">{{ $tandaTerima->nomor_tanda_terima }}</div>
                                        @else
                                            <div class="text-sm font-medium text-gray-900">{{ $tandaTerima->no_tanda_terima }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $tandaTerima->tanggal_tanda_terima->format('d/m/Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if(isset($isLclData) && $isLclData)
                                            <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $tandaTerima->nama_penerima }}">{{ $tandaTerima->nama_penerima }}</div>
                                        @else
                                            <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $tandaTerima->penerima }}">{{ $tandaTerima->penerima }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if(isset($isLclData) && $isLclData)
                                            <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $tandaTerima->nama_pengirim }}">{{ $tandaTerima->nama_pengirim }}</div>
                                        @else
                                            <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $tandaTerima->pengirim }}">{{ $tandaTerima->pengirim }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if(isset($isLclData) && $isLclData)
                                            <div class="text-sm text-gray-900">{{ $tandaTerima->jenisBarang->nama_barang ?? $tandaTerima->nama_barang }}</div>
                                        @else
                                            <div class="text-sm text-gray-900">{{ $tandaTerima->jenis_barang }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if(isset($isLclData) && $isLclData)
                                            <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $tandaTerima->alamat_pengirim }}">{{ $tandaTerima->alamat_pengirim }}</div>
                                        @else
                                            <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $tandaTerima->tujuan_pengambilan }}">{{ $tandaTerima->tujuan_pengambilan }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if(isset($isLclData) && $isLclData)
                                            <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $tandaTerima->tujuanPengiriman->nama_tujuan ?? '' }}">{{ $tandaTerima->tujuanPengiriman->nama_tujuan ?? 'Tidak ada' }}</div>
                                        @else
                                            <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $tandaTerima->tujuan_pengiriman }}">{{ $tandaTerima->tujuan_pengiriman }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if(isset($isLclData) && $isLclData)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                LCL
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ request('tipe') == 'fcl' ? 'FCL' : (request('tipe') == 'cargo' ? 'Cargo' : 'Standard') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex justify-center gap-2">
                                            @if(isset($isLclData) && $isLclData)
                                                <a href="{{ route('tanda-terima-lcl.show', $tandaTerima) }}"
                                                   class="text-indigo-600 hover:text-indigo-900" title="Lihat Detail">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                </a>
                                                @can('tanda-terima-tanpa-surat-jalan-update')
                                                    <a href="{{ route('tanda-terima-lcl.edit', $tandaTerima) }}"
                                                       class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                    </a>
                                                @endcan
                                                @can('tanda-terima-tanpa-surat-jalan-delete')
                                                    <form action="{{ route('tanda-terima-lcl.destroy', $tandaTerima) }}" method="POST" class="inline"
                                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus tanda terima LCL ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endcan
                                            @else
                                                <a href="{{ route('tanda-terima-tanpa-surat-jalan.show', $tandaTerima) }}"
                                                   class="text-indigo-600 hover:text-indigo-900" title="Lihat Detail">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                </a>
                                                @can('tanda-terima-tanpa-surat-jalan-update')
                                                    <a href="{{ route('tanda-terima-tanpa-surat-jalan.edit', $tandaTerima) }}"
                                                       class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                    </a>
                                                @endcan
                                                @can('tanda-terima-tanpa-surat-jalan-delete')
                                                    <form action="{{ route('tanda-terima-tanpa-surat-jalan.destroy', $tandaTerima) }}" method="POST" class="inline"
                                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus tanda terima ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endcan
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-center mt-4">
                    {{ $tandaTerimas->links() }}
                </div>
            @else
                <div class="text-center py-8">
                    <div class="text-gray-400">
                        <svg class="mx-auto h-12 w-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="text-base font-medium text-gray-500 mb-1">Belum ada tanda terima</h3>
                        <p class="text-sm text-gray-400">Mulai dengan membuat tanda terima baru.</p>
                        @can('tanda-terima-tanpa-surat-jalan-create')
                            <a href="{{ route('tanda-terima-tanpa-surat-jalan.pilih-tipe') }}"
                               class="inline-flex items-center px-4 py-2 mt-4 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Buat Tanda Terima Baru
                            </a>
                        @endcan
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
