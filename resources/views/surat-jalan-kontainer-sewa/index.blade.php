@extends('layouts.app')

@section('title', 'Surat Jalan Pengambilan/Pengembalian Kontainer Sewa')

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-file-alt text-cyan-600 mr-2"></i>
                    Surat Jalan Pengambilan/Pengembalian Kontainer Sewa
                </h1>
                <p class="text-sm text-gray-500 mt-1">Kelola surat jalan untuk pengambilan dan pengembalian kontainer sewa</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('surat-jalan-kontainer-sewa.create', ['tipe' => 'pengambilan']) }}" class="px-4 py-2 bg-emerald-600 text-white text-sm rounded-md hover:bg-emerald-700 transition">
                    <i class="fas fa-truck-loading mr-1"></i> Buat SJ Pengambilan
                </a>
                <a href="{{ route('surat-jalan-kontainer-sewa.create', ['tipe' => 'pengembalian']) }}" class="px-4 py-2 bg-orange-600 text-white text-sm rounded-md hover:bg-orange-700 transition">
                    <i class="fas fa-undo-alt mr-1"></i> Buat SJ Pengembalian
                </a>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" action="{{ route('surat-jalan-kontainer-sewa.index') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[180px]">
                <label class="block text-xs font-medium text-gray-600 mb-1">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nomor SJ, vendor, supir, kontainer..." class="w-full text-sm border border-gray-300 rounded-md px-3 py-2 focus:ring-cyan-500 focus:border-cyan-500">
            </div>
            <div class="min-w-[120px]">
                <label class="block text-xs font-medium text-gray-600 mb-1">Tipe</label>
                <select name="tipe" class="w-full text-sm border border-gray-300 rounded-md px-3 py-2 focus:ring-cyan-500 focus:border-cyan-500">
                    <option value="all">Semua</option>
                    <option value="pengambilan" {{ request('tipe') == 'pengambilan' ? 'selected' : '' }}>Pengambilan</option>
                    <option value="pengembalian" {{ request('tipe') == 'pengembalian' ? 'selected' : '' }}>Pengembalian</option>
                </select>
            </div>
            <div class="min-w-[120px]">
                <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                <select name="status" class="w-full text-sm border border-gray-300 rounded-md px-3 py-2 focus:ring-cyan-500 focus:border-cyan-500">
                    <option value="all">Semua</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="batal" {{ request('status') == 'batal' ? 'selected' : '' }}>Batal</option>
                </select>
            </div>
            <div class="min-w-[140px]">
                <label class="block text-xs font-medium text-gray-600 mb-1">Dari Tanggal SJ</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full text-sm border border-gray-300 rounded-md px-3 py-2 focus:ring-cyan-500 focus:border-cyan-500">
            </div>
            <div class="min-w-[140px]">
                <label class="block text-xs font-medium text-gray-600 mb-1">Sampai Tanggal SJ</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full text-sm border border-gray-300 rounded-md px-3 py-2 focus:ring-cyan-500 focus:border-cyan-500">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-cyan-600 text-white text-sm rounded-md hover:bg-cyan-700 transition">
                    <i class="fas fa-search mr-1"></i> Cari
                </button>
                <a href="{{ route('surat-jalan-kontainer-sewa.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm rounded-md hover:bg-gray-300 transition">
                    <i class="fas fa-undo mr-1"></i> Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md mb-4 text-sm">
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-4 text-sm">
            <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Table --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor SJ</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal SJ</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supir</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jml Kontainer</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($suratJalans as $index => $sj)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $suratJalans->firstItem() + $index }}</td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">
                            <a href="{{ route('surat-jalan-kontainer-sewa.show', $sj->id) }}" class="text-cyan-700 hover:text-cyan-900 hover:underline">
                                {{ $sj->nomor_surat_jalan }}
                            </a>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $sj->tipe_badge }}">
                                <i class="fas {{ $sj->tipe === 'pengambilan' ? 'fa-truck-loading' : 'fa-undo-alt' }} mr-1" style="font-size: 9px;"></i>
                                {{ $sj->tipe_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $sj->tanggal->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $sj->vendor ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $sj->supir ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-center text-gray-600">{{ $sj->items_count }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $sj->status_badge }}">
                                {{ $sj->status_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('surat-jalan-kontainer-sewa.show', $sj->id) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded transition" title="Lihat">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('surat-jalan-kontainer-sewa.print', $sj->id) }}" target="_blank" class="p-1.5 text-gray-600 hover:bg-gray-50 rounded transition" title="Cetak">
                                    <i class="fas fa-print text-xs"></i>
                                </a>
                                @if($sj->status !== 'selesai')
                                <form method="POST" action="{{ route('surat-jalan-kontainer-sewa.destroy', $sj->id) }}" class="inline" onsubmit="return confirm('Hapus surat jalan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded transition" title="Hapus">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-file-alt text-3xl text-gray-300 mb-2"></i>
                                <p>Belum ada surat jalan. Buat surat jalan baru dengan tombol di atas.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
            {{ $suratJalans->links() }}
        </div>
    </div>
</div>
@endsection
