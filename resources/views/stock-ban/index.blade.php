@extends('layouts.app')

@section('title', 'Stock Ban')
@section('page_title', 'Stock Ban')

@push('styles')
<style>
    .custom-select-container {
        position: relative;
        z-index: 50;
    }
    .custom-select-button {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        padding: 0.5rem 1rem;
        background-color: white;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        cursor: pointer;
        text-align: left;
    }
    .custom-select-button:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
    }
    .custom-select-dropdown {
        position: absolute;
        z-index: 9999;
        width: 100%;
        margin-top: 0.25rem;
        background-color: white;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        max-height: 15rem;
        overflow-y: auto;
    }
    .custom-select-search {
        position: sticky;
        top: 0;
        padding: 0.5rem;
        background-color: #f9fafb;
        border-bottom: 1px solid #d1d5db;
    }
    .custom-select-option {
        padding: 0.5rem 1rem;
        cursor: pointer;
    }
    .custom-select-option:hover {
        background-color: #eff6ff;
    }
    .custom-select-option.selected {
        background-color: #dbeafe;
        font-weight: 500;
    }
    .hidden {
        display: none !important;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-sm p-4 md:p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Daftar Stock Ban</h1>
                <p class="text-sm text-gray-600 mt-1">Kelola data stock ban di gudang (Individual per Serial Number)</p>
            </div>
            @can('stock-ban-create')
            <a href="{{ route('stock-ban.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-200 flex items-center">
                <i class="fas fa-plus mr-2"></i> Tambah Stock Ban
            </a>
            @endcan
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    <div>
        <div class="mb-4 border-b border-gray-200">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" role="tablist">
                <li class="mr-2" role="presentation">
                    <button id="tab-btn-stock-ban" onclick="switchTab('stock-ban')" class="inline-block p-4 border-b-2 rounded-t-lg border-blue-600 text-blue-600" type="button" role="tab">
                        Stock Ban Biasa
                    </button>
                </li>
                <li class="mr-2" role="presentation">
                    <button id="tab-btn-stock-ban-dalam" onclick="switchTab('stock-ban-dalam')" class="inline-block p-4 border-b-2 rounded-t-lg border-transparent text-gray-500 hover:text-gray-600 hover:border-gray-300" type="button" role="tab">
                        Stock Ban Dalam
                    </button>
                </li>
                <li class="mr-2" role="presentation">
                    <button id="tab-btn-stock-ban-perut" onclick="switchTab('stock-ban-perut')" class="inline-block p-4 border-b-2 rounded-t-lg border-transparent text-gray-500 hover:text-gray-600 hover:border-gray-300" type="button" role="tab">
                        Stock Ban Perut
                    </button>
                </li>
                <li class="mr-2" role="presentation">
                    <button id="tab-btn-stock-lock-kontainer" onclick="switchTab('stock-lock-kontainer')" class="inline-block p-4 border-b-2 rounded-t-lg border-transparent text-gray-500 hover:text-gray-600 hover:border-gray-300" type="button" role="tab">
                         Lock Kontainer
                    </button>
                </li>
                <li class="mr-2" role="presentation">
                    <button id="tab-btn-stock-ring-velg" onclick="switchTab('stock-ring-velg')" class="inline-block p-4 border-b-2 rounded-t-lg border-transparent text-gray-500 hover:text-gray-600 hover:border-gray-300" type="button" role="tab">
                         Ring Velg
                    </button>
                </li>
                <li class="mr-2" role="presentation">
                    <button id="tab-btn-stock-velg" onclick="switchTab('stock-velg')" class="inline-block p-4 border-b-2 rounded-t-lg border-transparent text-gray-500 hover:text-gray-600 hover:border-gray-300" type="button" role="tab">
                         Stock Velg
                    </button>
                </li>
            </ul>
        </div>

        {{-- Tab 1: Stock Ban Biasa --}}
        <div id="tab-content-stock-ban" class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Seri</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Merk / Ukuran</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type / Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Beli</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Masuk</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($stockBans as $index => $ban)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $loop->iteration }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 font-mono">
                                {{ $ban->nomor_seri }}
                                @if($ban->nomor_bukti)
                                    <div class="text-xs text-gray-500 font-normal mt-0.5">Ref: {{ $ban->nomor_bukti }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div class="font-bold text-blue-800">{{ $ban->namaStockBan ? $ban->namaStockBan->nama : '-' }}</div>
                                <div class="font-medium text-gray-900">{{ $ban->merk }}</div>
                                <div class="text-xs">{{ $ban->ukuran }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $ban->kondisi === 'asli' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $ban->kondisi === 'kanisir' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $ban->kondisi === 'afkir' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ in_array($ban->kondisi, ['kaleng', 'karung', 'liter', 'pail', 'pcs']) ? 'bg-blue-100 text-blue-800' : '' }}">
                                    {{ ucfirst($ban->kondisi) }}
                                </span>
                                <div class="text-xs text-gray-500 mt-1">Status: <span class="font-medium 
                                    {{ $ban->status === 'Stok' ? 'text-blue-600' : '' }}
                                    {{ $ban->status === 'Terpakai' ? 'text-gray-600' : '' }}
                                    {{ $ban->status === 'Rusak' ? 'text-red-600' : '' }}
                                    ">{{ $ban->status }}</span></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $ban->lokasi }}
                                @if($ban->mobil_id)
                                    <div class="text-xs text-blue-600 mt-1 font-medium">
                                        <i class="fas fa-truck mr-1"></i> {{ $ban->mobil->nomor_polisi }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                Rp {{ number_format($ban->harga_beli, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $ban->tanggal_masuk->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                    @can('stock-ban-update')
                                    <a href="{{ route('stock-ban.edit', $ban->id) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                    @can('stock-ban-update')
                                    @if($ban->status === 'Stok')
                                    <button onclick="openPakaiModal('{{ $ban->id }}')" class="text-blue-600 hover:text-blue-900 ml-2" title="Gunakan">
                                        <i class="fas fa-wrench"></i>
                                    </button>
                                    @endif
                                    @endcan
                                    @can('stock-ban-delete')
                                    <form action="{{ route('stock-ban.destroy', $ban->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-gray-500">
                                <i class="fas fa-circle-notch text-4xl mb-3 text-gray-300"></i>
                                <p>Belum ada data stock ban.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tab 2: Stock Ban Dalam --}}
        <div id="tab-content-stock-ban-dalam" class="bg-white rounded-lg shadow-sm overflow-hidden" style="display: none;">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang / Ukuran</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Beli</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Masuk</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($stockBanDalams as $index => $banDalam)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $loop->iteration }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div class="font-bold text-blue-800">{{ $banDalam->namaStockBan ? $banDalam->namaStockBan->nama : '-' }}</div>
                                <div class="text-xs">{{ $banDalam->ukuran }}</div>
                                @if($banDalam->nomor_bukti)
                                    <div class="text-xs text-gray-500 font-normal mt-0.5">Ref: {{ $banDalam->nomor_bukti }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                {{ number_format($banDalam->qty, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ ucfirst($banDalam->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $banDalam->lokasi }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                Rp {{ number_format($banDalam->harga_beli, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($banDalam->tanggal_masuk)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('stock-ban-dalam.show', $banDalam->id) }}" class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-md text-xs font-semibold shadow-sm transition-colors duration-200 border border-blue-200">
                                        <i class="fas fa-eye mr-1"></i> Detail
                                    </a>
                                    <a href="{{ route('stock-ban-dalam.use', $banDalam->id) }}" class="text-white bg-blue-600 hover:bg-blue-700 px-3 py-1 rounded-md text-xs font-semibold shadow-sm transition-colors duration-200">
                                        <i class="fas fa-wrench mr-1"></i> Gunakan
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-gray-500">
                                <i class="fas fa-circle-notch text-4xl mb-3 text-gray-300"></i>
                                <p>Belum ada data stock ban dalam.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tab 3: Stock Ban Perut --}}
        <div id="tab-content-stock-ban-perut" class="bg-white rounded-lg shadow-sm overflow-hidden" style="display: none;">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang / Ukuran</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Beli</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Masuk</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($stockBanPeruts as $index => $banPerut)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $loop->iteration }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div class="font-bold text-blue-800">{{ $banPerut->namaStockBan ? $banPerut->namaStockBan->nama : '-' }}</div>
                                <div class="text-xs">{{ $banPerut->ukuran }}</div>
                                @if($banPerut->nomor_bukti)
                                    <div class="text-xs text-gray-500 font-normal mt-0.5">Ref: {{ $banPerut->nomor_bukti }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                {{ number_format($banPerut->qty, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ ucfirst($banPerut->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $banPerut->lokasi }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                Rp {{ number_format($banPerut->harga_beli, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($banPerut->tanggal_masuk)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('stock-ban-dalam.show', $banPerut->id) }}" class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-md text-xs font-semibold shadow-sm transition-colors duration-200 border border-blue-200">
                                        <i class="fas fa-eye mr-1"></i> Detail
                                    </a>
                                    <a href="{{ route('stock-ban-dalam.use', $banPerut->id) }}" class="text-white bg-blue-600 hover:bg-blue-700 px-3 py-1 rounded-md text-xs font-semibold shadow-sm transition-colors duration-200">
                                        <i class="fas fa-wrench mr-1"></i> Gunakan
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-gray-500">
                                <i class="fas fa-circle-notch text-4xl mb-3 text-gray-300"></i>
                                <p>Belum ada data stock ban perut.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tab 4: Lock Kontainer --}}
        <div id="tab-content-stock-lock-kontainer" class="bg-white rounded-lg shadow-sm overflow-hidden" style="display: none;">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang / Ukuran</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Beli</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Masuk</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($stockLockKontainers as $index => $lock)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $loop->iteration }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div class="font-bold text-blue-800">{{ $lock->namaStockBan ? $lock->namaStockBan->nama : '-' }}</div>
                                <div class="text-xs">{{ $lock->ukuran }}</div>
                                @if($lock->nomor_bukti)
                                    <div class="text-xs text-gray-500 font-normal mt-0.5">Ref: {{ $lock->nomor_bukti }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                {{ number_format($lock->qty, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ ucfirst($lock->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $lock->lokasi }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                Rp {{ number_format($lock->harga_beli, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($lock->tanggal_masuk)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('stock-ban-dalam.show', $lock->id) }}" class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-md text-xs font-semibold shadow-sm transition-colors duration-200 border border-blue-200">
                                        <i class="fas fa-eye mr-1"></i> Detail
                                    </a>
                                    <a href="{{ route('stock-ban-dalam.use', $lock->id) }}" class="text-white bg-blue-600 hover:bg-blue-700 px-3 py-1 rounded-md text-xs font-semibold shadow-sm transition-colors duration-200">
                                        <i class="fas fa-wrench mr-1"></i> Gunakan
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-gray-500">
                                <i class="fas fa-circle-notch text-4xl mb-3 text-gray-300"></i>
                                <p>Belum ada data lock kontainer.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tab 5: Stock Ring Velg --}}
        <div id="tab-content-stock-ring-velg" class="bg-white rounded-lg shadow-sm overflow-hidden" style="display: none;">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang / Ukuran</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Beli</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Masuk</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($stockRingVelgs as $index => $ring)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $loop->iteration }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div class="font-bold text-blue-800">{{ $ring->namaStockBan ? $ring->namaStockBan->nama : '-' }}</div>
                                <div class="text-xs">{{ $ring->ukuran }}</div>
                                @if($ring->nomor_bukti)
                                    <div class="text-xs text-gray-500 font-normal mt-0.5">Ref: {{ $ring->nomor_bukti }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                {{ number_format($ring->qty, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ ucfirst($ring->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $ring->lokasi }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                Rp {{ number_format($ring->harga_beli, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($ring->tanggal_masuk)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                    {{-- Assuming we might want detail/use buttons later, reusing view/route if possible or just placeholders --}}
                                    {{-- Current implementation for StockBanDalam uses specific routes. I might need routes for RingVelg if I want these buttons to work perfectly. For now I'll just show nothing or placeholder. --}}
                                    {{-- Actually better to not show broken buttons. --}}
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-gray-500">
                                <i class="fas fa-circle-notch text-4xl mb-3 text-gray-300"></i>
                                <p>Belum ada data ring velg.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tab 6: Stock Velg --}}
        <div id="tab-content-stock-velg" class="bg-white rounded-lg shadow-sm overflow-hidden" style="display: none;">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang / Ukuran</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Beli</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Masuk</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($stockVelgs as $index => $item)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $loop->iteration }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div class="font-bold text-blue-800">{{ $item->namaStockBan ? $item->namaStockBan->nama : '-' }}</div>
                                <div class="text-xs">{{ $item->ukuran }}</div>
                                @if($item->nomor_bukti)
                                    <div class="text-xs text-gray-500 font-normal mt-0.5">Ref: {{ $item->nomor_bukti }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                {{ number_format($item->qty, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ ucfirst($item->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $item->lokasi }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                Rp {{ number_format($item->harga_beli, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($item->tanggal_masuk)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-gray-500">
                                <i class="fas fa-circle-notch text-4xl mb-3 text-gray-300"></i>
                                <p>Belum ada data stock velg.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>

    {{-- Modal Pakai Ban --}}
    <div id="modal-pakai-ban" class="fixed z-50 inset-0 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="" method="POST" id="form-pakai-ban">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                                    Pakai Ban
                                </h3>
                                <div class="mt-2 space-y-4">
                                    <div class="mb-4">
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Mobil</label>
                                        <div class="custom-select-container">
                                            <input type="hidden" name="mobil_id" id="modal_mobil_id" required>
                                            <button type="button" class="custom-select-button" id="mobil-select-button">
                                                <span class="placeholder">-- Pilih Mobil --</span>
                                                <i class="fas fa-chevron-down text-gray-400"></i>
                                            </button>
                                            <div class="custom-select-dropdown hidden" id="mobil-select-dropdown">
                                                <div class="custom-select-search">
                                                    <input type="text" class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="Cari mobil..." id="mobil-search-input">
                                                </div>
                                                <div class="custom-select-options max-h-60 overflow-y-auto">
                                                    <div class="custom-select-option text-gray-500 italic" data-value="">-- Pilih Mobil --</div>
                                                    @foreach($mobils as $mobil)
                                                        <div class="custom-select-option" data-value="{{ $mobil->id }}" data-text="{{ $mobil->nomor_polisi }}">
                                                            {{ $mobil->nomor_polisi }}
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Penerima</label>
                                        <div class="custom-select-container">
                                            <input type="hidden" name="penerima_id" id="modal_penerima_id" required>
                                            <button type="button" class="custom-select-button" id="penerima-select-button">
                                                <span class="placeholder">-- Pilih Penerima --</span>
                                                <i class="fas fa-chevron-down text-gray-400"></i>
                                            </button>
                                            <div class="custom-select-dropdown hidden" id="penerima-select-dropdown">
                                                <div class="custom-select-search">
                                                    <input type="text" class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="Cari penerima..." id="penerima-search-input">
                                                </div>
                                                <div class="custom-select-options max-h-60 overflow-y-auto">
                                                    <div class="custom-select-option text-gray-500 italic" data-value="">-- Pilih Penerima --</div>
                                                    @foreach($karyawans as $karyawan)
                                                        <div class="custom-select-option" data-value="{{ $karyawan->id }}" data-text="{{ $karyawan->nama_lengkap }}">
                                                            {{ $karyawan->nama_lengkap }}
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Keluar</label>
                                        <input type="date" name="tanggal_keluar" id="modal_tanggal_keluar" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required value="{{ date('Y-m-d') }}">
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Keterangan</label>
                                        <textarea name="keterangan" id="modal_keterangan" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Simpan
                        </button>
                        <button type="button" onclick="closePakaiModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function switchTab(tabName) {
        // Hide all contents
        document.getElementById('tab-content-stock-ban').style.display = 'none';
        document.getElementById('tab-content-stock-ban-dalam').style.display = 'none';
        document.getElementById('tab-content-stock-ban-perut').style.display = 'none';
        document.getElementById('tab-content-stock-lock-kontainer').style.display = 'none';
        document.getElementById('tab-content-stock-ring-velg').style.display = 'none';
        document.getElementById('tab-content-stock-velg').style.display = 'none';
        
        // Show selected content
        document.getElementById('tab-content-' + tabName).style.display = 'block';
        
        // Update button styles
        const tabs = ['stock-ban', 'stock-ban-dalam', 'stock-ban-perut', 'stock-lock-kontainer', 'stock-ring-velg', 'stock-velg'];
        const inactiveClasses = ['border-transparent', 'text-gray-500', 'hover:text-gray-600', 'hover:border-gray-300'];
        const activeClasses = ['border-blue-600', 'text-blue-600'];
        
        tabs.forEach(t => {
            const btn = document.getElementById('tab-btn-' + t);
            if (t === tabName) {
                btn.classList.remove(...inactiveClasses);
                btn.classList.add(...activeClasses);
            } else {
                btn.classList.remove(...activeClasses);
                btn.classList.add(...inactiveClasses);
            }
        });
    }

    function openPakaiModal(id) {
        const form = document.getElementById('form-pakai-ban');
        // Manually construct URL since accessing named route dynamically in JS with ID substitution is tricky without a dedicated library or dummy placeholder
        // Assuming the route is /stock-ban/{id}/use
        // Use Blade to generate base URL
        const baseUrl = "{{ url('/') }}";
        form.action = baseUrl + "/stock-ban/" + id + "/use";
        document.getElementById('modal-pakai-ban').classList.remove('hidden');
    }

    function closePakaiModal() {
        document.getElementById('modal-pakai-ban').classList.add('hidden');
        
        // Reset Mobil
        const mobilInput = document.getElementById('modal_mobil_id');
        const mobilButton = document.getElementById('mobil-select-button');
        const mobilSpan = mobilButton ? mobilButton.querySelector('span') : null;
        const mobilSearch = document.getElementById('mobil-search-input');
        const mobilDropdown = document.getElementById('mobil-select-dropdown');
        
        mobilInput.value = '';
        if (mobilSpan) {
            mobilSpan.textContent = '-- Pilih Mobil --';
            mobilSpan.classList.add('placeholder');
        }
        mobilSearch.value = '';
        mobilDropdown.classList.add('hidden');
        mobilDropdown.querySelectorAll('.custom-select-option').forEach(opt => {
            opt.style.display = 'block';
            opt.classList.remove('selected');
        });

        // Reset Penerima
        const penerimaInput = document.getElementById('modal_penerima_id');
        const penerimaButton = document.getElementById('penerima-select-button');
        const penerimaSpan = penerimaButton ? penerimaButton.querySelector('span') : null;
        const penerimaSearch = document.getElementById('penerima-search-input');
        const penerimaDropdown = document.getElementById('penerima-select-dropdown');
        
        penerimaInput.value = '';
        if (penerimaSpan) {
            penerimaSpan.textContent = '-- Pilih Penerima --';
            penerimaSpan.classList.add('placeholder');
        }
        penerimaSearch.value = '';
        penerimaDropdown.classList.add('hidden');
        penerimaDropdown.querySelectorAll('.custom-select-option').forEach(opt => {
            opt.style.display = 'block';
            opt.classList.remove('selected');
        });
    }

    function initSearchableSelect(prefix, placeholder) {
        const button = document.getElementById(`${prefix}-select-button`);
        if (!button || button.dataset.initialized) return;
        
        console.log('Initializing searchable select for:', prefix);
        const dropdown = document.getElementById(`${prefix}-select-dropdown`);
        const searchInput = document.getElementById(`${prefix}-search-input`);
        const hiddenInput = document.getElementById(`modal_${prefix}_id`);

        if (!button || !dropdown || !searchInput) return;

        button.addEventListener('click', (e) => {
            console.log('Button clicked for:', prefix);
            e.preventDefault();
            e.stopPropagation();
            
            const isHidden = dropdown.classList.contains('hidden');
            
            // Close all other dropdowns
            document.querySelectorAll('.custom-select-container').forEach(c => {
                if (c !== button.closest('.custom-select-container')) {
                    c.style.zIndex = "";
                    c.querySelector('.custom-select-dropdown').classList.add('hidden');
                }
            });

            if (isHidden) {
                console.log('Opening dropdown for:', prefix);
                dropdown.classList.remove('hidden');
                button.closest('.custom-select-container').style.zIndex = "100";
                searchInput.value = '';
                const options = dropdown.querySelectorAll('.custom-select-option');
                options.forEach(opt => opt.style.display = 'block');
                setTimeout(() => searchInput.focus(), 100);
            } else {
                console.log('Closing dropdown for:', prefix);
                dropdown.classList.add('hidden');
                button.closest('.custom-select-container').style.zIndex = "";
            }
        });

        searchInput.addEventListener('click', (e) => e.stopPropagation());

        searchInput.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase().trim();
            const options = dropdown.querySelectorAll('.custom-select-option');
            options.forEach(option => {
                const text = (option.getAttribute('data-text') || '').toLowerCase();
                if (text.includes(searchTerm) || option.getAttribute('data-value') === '') {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
            });
        });

        dropdown.addEventListener('click', (e) => {
            const option = e.target.closest('.custom-select-option');
            if (!option) return;
            
            e.stopPropagation();
            const value = option.getAttribute('data-value');
            const text = option.getAttribute('data-text') || placeholder;

            hiddenInput.value = value;
            const span = button.querySelector('span');
            if (span) {
                span.textContent = text;
                span.classList.remove('placeholder');
            }
            
            dropdown.classList.add('hidden');
            button.closest('.custom-select-container').style.zIndex = "";
            
            dropdown.querySelectorAll('.custom-select-option').forEach(opt => opt.classList.remove('selected'));
            option.classList.add('selected');
        });

        button.dataset.initialized = "true";
    }

    // Global listener for closing dropdowns (only once)
    if (!window.dropdownGlobalListenerAttached) {
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.custom-select-container')) {
                document.querySelectorAll('.custom-select-container').forEach(c => {
                    c.style.zIndex = "";
                    c.querySelector('.custom-select-dropdown').classList.add('hidden');
                });
            }
        });
        window.dropdownGlobalListenerAttached = true;
    }

    // Initialize
    function initAll() {
        initSearchableSelect('mobil', '-- Pilih Mobil --');
        initSearchableSelect('penerima', '-- Pilih Penerima --');
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }
</script>
@endpush
