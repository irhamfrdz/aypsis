@extends('layouts.app')

@section('title', 'Stock Ban')
@section('page_title', 'Stock Ban')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-sm p-4 md:p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Daftar Stock Ban</h1>
                <p class="text-sm text-gray-600 mt-1">Kelola data stock ban di gudang (Individual per Serial Number)</p>
            </div>
            <a href="{{ route('stock-ban.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-200 flex items-center">
                <i class="fas fa-plus mr-2"></i> Tambah Stock Ban
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Seri</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Merk / Ukuran</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kondisi / Status</th>
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
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div class="font-medium text-gray-900">{{ $ban->merk }}</div>
                            <div class="text-xs">{{ $ban->ukuran }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $ban->kondisi === 'Baru' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $ban->kondisi === 'Vulkanisir' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $ban->kondisi === 'Bekas' ? 'bg-gray-100 text-gray-800' : '' }}
                                {{ $ban->kondisi === 'Afkir' ? 'bg-red-100 text-red-800' : '' }}">
                                {{ $ban->kondisi }}
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
                                <a href="{{ route('stock-ban.edit', $ban->id) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('stock-ban.destroy', $ban->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
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
</div>
@endsection
