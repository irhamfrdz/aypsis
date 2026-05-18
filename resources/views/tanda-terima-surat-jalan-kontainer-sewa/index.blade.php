@extends('layouts.app')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('title', 'Tanda Terima SJ Kontainer Sewa')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                    <span class="p-2 bg-cyan-50 text-cyan-700 rounded-lg">
                        <i class="fas fa-file-contract"></i>
                    </span>
                    Tanda Terima Surat Jalan Kontainer Sewa
                </h1>
                <p class="text-gray-600 mt-1">Kelola tanda terima untuk surat jalan pengambilan & pengembalian kontainer sewa</p>
            </div>
            <div>
                @if(request('tipe', 'surat_jalan') == 'tanda_terima')
                    <a href="{{ route('tanda-terima-surat-jalan-kontainer-sewa.index', ['tipe' => 'surat_jalan']) }}" 
                       class="inline-flex items-center px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white font-medium rounded-lg shadow-sm transition duration-200">
                        <i class="fas fa-list mr-2"></i>
                        Pending Surat Jalan
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4 mb-6 transition duration-300">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-emerald-500 text-xl"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-emerald-800">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6 transition duration-300">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Filter & Search -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('tanda-terima-surat-jalan-kontainer-sewa.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <!-- Tipe -->
                <div class="md:col-span-3">
                    <label for="tipe" class="block text-sm font-medium text-gray-700 mb-2">Tampilkan Data</label>
                    <select name="tipe" 
                            id="tipe"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-sm">
                        <option value="surat_jalan" {{ request('tipe', 'surat_jalan') == 'surat_jalan' ? 'selected' : '' }}>Pending Surat Jalan Sewa</option>
                        <option value="tanda_terima" {{ request('tipe') == 'tanda_terima' ? 'selected' : '' }}>Tanda Terima Terdaftar</option>
                    </select>
                </div>

                <!-- Search -->
                <div class="md:col-span-4">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                    <input type="text" 
                           name="search" 
                           id="search" 
                           value="{{ request('search') }}"
                           placeholder="Cari nomor, kontainer, supir..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-sm">
                </div>

                <!-- Buttons -->
                <div class="md:col-span-5 flex items-end gap-2">
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white font-medium rounded-lg transition duration-200 text-sm shadow-sm">
                        <i class="fas fa-search mr-2"></i>
                        Filter & Cari
                    </button>
                    <a href="{{ route('tanda-terima-surat-jalan-kontainer-sewa.index') }}" 
                       class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition duration-200 text-sm">
                        <i class="fas fa-redo mr-2"></i>
                        Reset
                    </a>
                </div>

                <!-- Filter Checkboxes (Tanda Terima only) -->
                @if(request('tipe') == 'tanda_terima')
                <div class="md:col-span-12 flex flex-wrap gap-6 pt-3 border-t mt-2">
                    <label class="inline-flex items-center cursor-pointer select-none">
                        <input type="checkbox" name="f_lembur" value="1" {{ request('f_lembur') ? 'checked' : '' }} class="rounded border-gray-300 text-cyan-600 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 h-4 w-4">
                        <span class="ml-2 text-sm font-medium text-gray-700">Lembur</span>
                    </label>
                    <label class="inline-flex items-center cursor-pointer select-none">
                        <input type="checkbox" name="f_nginap" value="1" {{ request('f_nginap') ? 'checked' : '' }} class="rounded border-gray-300 text-cyan-600 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 h-4 w-4">
                        <span class="ml-2 text-sm font-medium text-gray-700">Nginap</span>
                    </label>
                    <label class="inline-flex items-center cursor-pointer select-none">
                        <input type="checkbox" name="f_tidak_lembur_nginap" value="1" {{ request('f_tidak_lembur_nginap') ? 'checked' : '' }} class="rounded border-gray-300 text-cyan-600 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 h-4 w-4">
                        <span class="ml-2 text-sm font-medium text-gray-700">Tidak Lembur & Nginap</span>
                    </label>
                </div>
                @endif
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            @if(request('tipe', 'surat_jalan') == 'tanda_terima')
                <!-- Table Tanda Terima -->
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nomor Tanda Terima</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal Terima</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nomor SJ</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tipe SJ</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No Kontainer</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Supir</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($tandaTerimas ?? [] as $index => $tandaTerima)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                {{ ($tandaTerimas->currentPage() - 1) * $tandaTerimas->perPage() + $index + 1 }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-gray-900">
                                {{ $tandaTerima->nomor_tanda_terima ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                {{ $tandaTerima->tanggal_tanda_terima ? $tandaTerima->tanggal_tanda_terima->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                {{ $tandaTerima->nomor_surat_jalan ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                @if($tandaTerima->kegiatan === 'pengambilan')
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-md bg-emerald-100 text-emerald-800">
                                        Pengambilan
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-md bg-orange-100 text-orange-800">
                                        Pengembalian
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                {{ $tandaTerima->nomor_kontainer ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                {{ $tandaTerima->supir ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-800">
                                    Completed
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center text-sm font-medium">
                                <div class="flex justify-center items-center gap-1.5">
                                    <a href="{{ route('tanda-terima-surat-jalan-kontainer-sewa.show', $tandaTerima->id) }}"
                                       class="inline-flex items-center px-2 py-1 bg-cyan-600 hover:bg-cyan-700 text-white text-xs font-medium rounded shadow-sm transition duration-200"
                                       title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('tanda-terima-surat-jalan-kontainer-sewa.edit', $tandaTerima->id) }}"
                                       class="inline-flex items-center px-2 py-1 bg-amber-500 hover:bg-amber-600 text-white text-xs font-medium rounded shadow-sm transition duration-200"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('tanda-terima-surat-jalan-kontainer-sewa.destroy', $tandaTerima->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tanda terima ini? Status Surat Jalan terkait akan dikembalikan menjadi Aktif.')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="inline-flex items-center px-2 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded shadow-sm transition duration-200"
                                                title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                    <a href="{{ route('tanda-terima-surat-jalan-kontainer-sewa.print', $tandaTerima->id) }}"
                                       target="_blank"
                                       class="inline-flex items-center px-2 py-1 bg-gray-800 hover:bg-gray-900 text-white text-xs font-medium rounded shadow-sm transition duration-200"
                                       title="Cetak">
                                        <i class="fas fa-print"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-folder-open text-gray-300 text-5xl mb-4"></i>
                                    <p class="text-gray-500 font-semibold text-base">Belum ada tanda terima terdaftar</p>
                                    <p class="text-gray-400 text-sm mt-1">Gunakan tab "Pending Surat Jalan Sewa" untuk membuat tanda terima baru</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            @else
                <!-- Table Pending Surat Jalan -->
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nomor Surat Jalan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal SJ</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Vendor</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No Kontainer</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Supir</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No Plat</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($suratJalans ?? [] as $index => $suratJalan)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                {{ ($suratJalans->currentPage() - 1) * $suratJalans->perPage() + $index + 1 }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-gray-900">
                                {{ $suratJalan->nomor_surat_jalan ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                {{ $suratJalan->tanggal ? $suratJalan->tanggal->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                @if($suratJalan->tipe === 'pengambilan')
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-md bg-emerald-100 text-emerald-800">
                                        Pengambilan
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-md bg-orange-100 text-orange-800">
                                        Pengembalian
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                {{ $suratJalan->vendor ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 font-semibold text-cyan-700">
                                {{ $suratJalan->nomor_kontainer ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                {{ $suratJalan->supir ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                {{ $suratJalan->no_plat ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center text-sm font-medium">
                                <button type="button" 
                                        onclick="openTerimaSewaModal({{ $suratJalan->id }}, '{{ $suratJalan->nomor_surat_jalan }}', '{{ $suratJalan->nomor_kontainer }}', '{{ $suratJalan->supir }}', '{{ $suratJalan->no_plat }}')"
                                        class="inline-flex items-center px-3.5 py-1.5 bg-cyan-600 hover:bg-cyan-700 text-white text-xs font-medium rounded-lg transition duration-200 cursor-pointer shadow-sm border-0">
                                    <i class="fas fa-check-circle mr-1.5"></i>
                                    Terima Kontainer
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-check-double text-emerald-300 text-5xl mb-4"></i>
                                    <p class="text-gray-500 font-semibold text-base">Semua Surat Jalan Kontainer Sewa Sudah Selesai</p>
                                    <p class="text-gray-400 text-sm mt-1">Tidak ada data surat jalan aktif yang perlu dibuatkan tanda terima</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            @endif
        </div>

        <!-- Pagination -->
        @php
            $paginationData = request('tipe', 'surat_jalan') == 'tanda_terima' ? ($tandaTerimas ?? null) : ($suratJalans ?? null);
        @endphp
        @if($paginationData && $paginationData->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            <div class="flex items-center justify-between">
                <div class="flex-1 flex justify-between sm:hidden">
                    @if ($paginationData->onFirstPage())
                        <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">
                            Previous
                        </span>
                    @else
                        <a href="{{ $paginationData->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Previous
                        </a>
                    @endif

                    @if ($paginationData->hasMorePages())
                        <a href="{{ $paginationData->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Next
                        </a>
                    @else
                        <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">
                            Next
                        </span>
                    @endif
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Menampilkan
                            <span class="font-medium">{{ $paginationData->firstItem() ?? 0 }}</span>
                            sampai
                            <span class="font-medium">{{ $paginationData->lastItem() ?? 0 }}</span>
                            dari
                            <span class="font-medium">{{ $paginationData->total() }}</span>
                            hasil
                        </p>
                    </div>
                    <div>
                        {{ $paginationData->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal Terima Kontainer -->
<div id="terimaSewaModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 transition-opacity duration-300">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-2xl shadow-xl rounded-xl bg-white border-gray-200">
        <div class="flex items-center justify-between pb-4 border-b">
            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-check-circle text-cyan-600"></i>
                Buat Tanda Terima Kontainer Sewa
            </h3>
            <button type="button" onclick="closeTerimaSewaModal()" class="text-gray-400 hover:text-gray-600 focus:outline-none bg-transparent border-0 cursor-pointer">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form id="terimaSewaForm" method="POST" action="{{ route('tanda-terima-surat-jalan-kontainer-sewa.store') }}">
            @csrf
            <input type="hidden" name="surat_jalan_kontainer_sewa_id" id="modal_surat_jalan_id">
            
            <div class="mt-4 space-y-4">
                <!-- Info SJ Card -->
                <div class="bg-cyan-50 border border-cyan-100 p-4 rounded-xl">
                    <div class="grid grid-cols-2 gap-4 text-xs md:text-sm">
                        <div>
                            <span class="text-cyan-700 block font-medium">Nomor Surat Jalan</span>
                            <span class="font-bold text-cyan-900 mt-0.5 block" id="modal_nomor_sj">-</span>
                        </div>
                        <div>
                            <span class="text-cyan-700 block font-medium">Nomor Kontainer</span>
                            <span class="font-bold text-cyan-900 mt-0.5 block" id="modal_no_kontainer">-</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Nomor Tanda Terima -->
                    <div>
                        <label for="nomor_tanda_terima" class="block text-sm font-semibold text-gray-700 mb-2">
                            Nomor Tanda Terima <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="nomor_tanda_terima" 
                               id="nomor_tanda_terima"
                               required
                               readonly
                               class="w-full px-3.5 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-sm font-bold text-cyan-900"
                               placeholder="Mengenerate nomor otomatis...">
                    </div>

                    <!-- Tanggal Terima -->
                    <div>
                        <label for="tanggal_tanda_terima" class="block text-sm font-semibold text-gray-700 mb-2">
                            Tanggal Terima <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               name="tanggal_tanda_terima" 
                               id="tanggal_tanda_terima"
                               required
                               value="{{ date('Y-m-d') }}"
                               class="w-full px-3.5 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Supir -->
                    <div>
                        <label for="supir_search" class="block text-sm font-semibold text-gray-700 mb-2">
                            Supir
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   id="supir_search"
                                   autocomplete="off"
                                   placeholder="Cari atau ketik nama supir..."
                                   class="w-full px-3.5 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-sm">
                            <input type="hidden" name="supir" id="supir">
                            <div id="supir_dropdown" class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                <div class="py-1">
                                    @foreach($supirs ?? [] as $supir)
                                        <div class="supir-option px-3.5 py-2 hover:bg-cyan-50 hover:text-cyan-700 cursor-pointer text-sm" 
                                             data-value="{{ $supir->nama_panggilan }}"
                                             data-plat="{{ $supir->plat }}">
                                            {{ $supir->nama_panggilan }} ({{ $supir->plat ?? 'No Plat' }})
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- No Plat -->
                    <div>
                        <label for="no_plat" class="block text-sm font-semibold text-gray-700 mb-2">
                            Nomor Plat Kendaraan
                        </label>
                        <input type="text" 
                               name="no_plat" 
                               id="no_plat"
                               placeholder="Contoh: BP 1234 XX"
                               class="w-full px-3.5 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Nomor Seal -->
                    <div>
                        <label for="no_seal" class="block text-sm font-semibold text-gray-700 mb-2">
                            Nomor Seal (Segel)
                        </label>
                        <input type="text" 
                               name="no_seal" 
                               id="no_seal"
                               placeholder="Contoh: SL123456"
                               class="w-full px-3.5 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-sm">
                    </div>
                </div>

                <!-- Keterangan -->
                <div>
                    <label for="keterangan" class="block text-sm font-semibold text-gray-700 mb-2">
                        Keterangan Tambahan
                    </label>
                    <textarea name="keterangan" 
                              id="keterangan"
                              rows="3"
                              placeholder="Masukkan catatan jika ada..."
                              class="w-full px-3.5 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-sm"></textarea>
                </div>

                <!-- Checkbox Lembur & Nginap -->
                <div class="flex flex-col md:flex-row md:space-x-6 space-y-3 md:space-y-0 p-4 rounded-xl bg-amber-50 border border-amber-200">
                    <div class="flex items-center">
                        <input type="checkbox"
                               name="lembur"
                               id="lembur"
                               value="1"
                               class="h-5 w-5 text-cyan-600 focus:ring-cyan-500 border-gray-300 rounded cursor-pointer">
                        <label for="lembur" class="ml-2 block text-sm font-medium text-gray-900 cursor-pointer select-none">
                            Lembur
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox"
                               name="nginap"
                               id="nginap"
                               value="1"
                               class="h-5 w-5 text-cyan-600 focus:ring-cyan-500 border-gray-300 rounded cursor-pointer">
                        <label for="nginap" class="ml-2 block text-sm font-medium text-gray-900 cursor-pointer select-none">
                            Nginap
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox"
                               name="tidak_lembur_nginap"
                               id="tidak_lembur_nginap"
                               value="1"
                               checked
                               class="h-5 w-5 text-cyan-600 focus:ring-cyan-500 border-gray-300 rounded cursor-pointer">
                        <label for="tidak_lembur_nginap" class="ml-2 block text-sm font-medium text-gray-900 cursor-pointer select-none">
                            Tidak Lembur & Nginap
                        </label>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3 pt-4 border-t">
                <button type="button" 
                        onclick="closeTerimaSewaModal()"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition duration-200 text-sm border-0 cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white font-semibold rounded-lg shadow-sm transition duration-200 text-sm border-0 cursor-pointer">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Tanda Terima
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Auto-submit on change
    document.getElementById('tipe')?.addEventListener('change', function() {
        this.form.submit();
    });

    // Generate auto number
    async function fetchNextReceiptNumber() {
        try {
            const response = await fetch('{{ route('tanda-terima-surat-jalan-kontainer-sewa.get-next-number', [], false) }}', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            const now = new Date();
            const bulan = String(now.getMonth() + 1).padStart(2, '0');
            const tahun = String(now.getFullYear()).slice(-2);

            if (response.ok) {
                const data = await response.json();
                const runningNum = String(data.next_number).padStart(6, '0');
                return `TTKS${bulan}${tahun}${runningNum}`;
            } else {
                const timestamp = now.getTime().toString().slice(-6);
                return `TTKS${bulan}${tahun}${timestamp}`;
            }
        } catch (error) {
            const now = new Date();
            const bulan = String(now.getMonth() + 1).padStart(2, '0');
            const tahun = String(now.getFullYear()).slice(-2);
            const timestamp = now.getTime().toString().slice(-6);
            return `TTKS${bulan}${tahun}${timestamp}`;
        }
    }

    // Modal Control
    async function openTerimaSewaModal(suratJalanId, nomorSj, nomorKontainer, defaultSupir, defaultPlat) {
        document.getElementById('modal_surat_jalan_id').value = suratJalanId;
        document.getElementById('modal_nomor_sj').textContent = nomorSj || '-';
        document.getElementById('modal_no_kontainer').textContent = nomorKontainer || '-';
        
        // Pre-fill supir and plat from Surat Jalan
        if (defaultSupir) {
            document.getElementById('supir_search').value = defaultSupir;
            document.getElementById('supir').value = defaultSupir;
        }
        if (defaultPlat) {
            document.getElementById('no_plat').value = defaultPlat;
        }

        // Fetch and show next receipt number
        const receiptNo = await fetchNextReceiptNumber();
        document.getElementById('nomor_tanda_terima').value = receiptNo;

        // Show Modal
        document.getElementById('terimaSewaModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeTerimaSewaModal() {
        document.getElementById('terimaSewaModal').classList.add('hidden');
        document.getElementById('terimaSewaForm').reset();
        document.getElementById('supir').value = '';
        document.body.style.overflow = 'auto';
    }

    // Close on click background
    document.getElementById('terimaSewaModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeTerimaSewaModal();
        }
    });

    // Close on ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeTerimaSewaModal();
        }
    });

    // Searchable Supir dropdown
    const supirSearch = document.getElementById('supir_search');
    const supirDropdown = document.getElementById('supir_dropdown');
    const supirHidden = document.getElementById('supir');
    const supirOptions = document.querySelectorAll('.supir-option');
    const inputPlat = document.getElementById('no_plat');

    supirSearch?.addEventListener('focus', function() {
        supirDropdown.classList.remove('hidden');
        filterSupirs('');
    });

    supirSearch?.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        filterSupirs(query);
    });

    function filterSupirs(query) {
        let hasOptions = false;
        supirOptions.forEach(opt => {
            const text = opt.textContent.toLowerCase();
            if (text.includes(query)) {
                opt.style.display = 'block';
                hasOptions = true;
            } else {
                opt.style.display = 'none';
            }
        });

        if (hasOptions) {
            supirDropdown.classList.remove('hidden');
        } else {
            supirDropdown.classList.add('hidden');
        }
    }

    supirOptions.forEach(opt => {
        opt.addEventListener('click', function() {
            const val = this.getAttribute('data-value');
            const plat = this.getAttribute('data-plat');

            supirSearch.value = val;
            supirHidden.value = val;
            
            if (plat && plat !== 'undefined' && plat !== 'null') {
                inputPlat.value = plat;
            }

            supirDropdown.classList.add('hidden');
        });
    });

    document.addEventListener('click', function(e) {
        if (!supirSearch?.contains(e.target) && !supirDropdown?.contains(e.target)) {
            supirDropdown?.classList.add('hidden');
        }
    });

    // Handle checkboxes exclusivity
    const cbLembur = document.getElementById('lembur');
    const cbNginap = document.getElementById('nginap');
    const cbTidak = document.getElementById('tidak_lembur_nginap');

    if (cbLembur && cbNginap && cbTidak) {
        cbTidak.addEventListener('change', function() {
            if (this.checked) {
                cbLembur.checked = false;
                cbNginap.checked = false;
            }
        });

        cbLembur.addEventListener('change', function() {
            if (this.checked) {
                cbTidak.checked = false;
            }
        });

        cbNginap.addEventListener('change', function() {
            if (this.checked) {
                cbTidak.checked = false;
            }
        });
    }
</script>
@endpush
