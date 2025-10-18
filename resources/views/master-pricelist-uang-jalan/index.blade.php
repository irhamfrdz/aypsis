@extends('layouts.app')

@section('title', 'Master Pricelist Uang Jalan')
@section('page_title', 'Master Pricelist Uang Jalan')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <h1 class="text-2xl font-bold text-gray-900">Master Pricelist Uang Jalan</h1>
                <div class="mt-4 sm:mt-0 flex space-x-3">
                    <a href="{{ route('master-pricelist-uang-jalan.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Pricelist
                    </a>
                    <button onclick="openImportModal()" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <i class="fas fa-upload mr-2"></i>
                        Import CSV
                    </button>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <form method="GET" action="{{ route('master-pricelist-uang-jalan.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Kode, dari, ke, wilayah..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cabang</label>
                    <select name="cabang" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">Semua Cabang</option>
                        @foreach($cabangList as $cabang)
                            <option value="{{ $cabang }}" {{ request('cabang') == $cabang ? 'selected' : '' }}>
                                {{ $cabang }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Wilayah</label>
                    <select name="wilayah" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">Semua Wilayah</option>
                        @foreach($wilayahList as $wilayah)
                            <option value="{{ $wilayah }}" {{ request('wilayah') == $wilayah ? 'selected' : '' }}>
                                {{ $wilayah }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cabang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rute</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wilayah</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Uang Jalan 20ft</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Uang Jalan 40ft</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jarak (km)</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pricelistData as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $item->kode }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                    {{ $item->cabang }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="max-w-xs">
                                    <div class="font-medium">{{ $item->dari }}</div>
                                    <div class="text-gray-500 flex items-center">
                                        <i class="fas fa-arrow-right mx-2"></i>
                                        {{ $item->ke }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <div class="max-w-xs truncate" title="{{ $item->wilayah }}">
                                    {{ $item->wilayah }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-right">
                                @if($item->uang_jalan_20ft > 0)
                                    <span class="text-green-600">Rp {{ number_format($item->uang_jalan_20ft, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-right">
                                @if($item->uang_jalan_40ft > 0)
                                    <span class="text-green-600">Rp {{ number_format($item->uang_jalan_40ft, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                {{ $item->jarak_km }} km
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($item->status == 'active')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        Tidak Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                <div class="flex justify-center space-x-2">
                                    <a href="{{ route('master-pricelist-uang-jalan.show', $item) }}"
                                       class="text-blue-600 hover:text-blue-900" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('master-pricelist-uang-jalan.edit', $item) }}"
                                       class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('master-pricelist-uang-jalan.destroy', $item) }}"
                                          class="inline-block" onsubmit="return confirm('Yakin ingin menonaktifkan pricelist ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Nonaktifkan">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        
                                    <td>
                                        @can('audit-log-view')
                                            <button type="button" class="btn btn-info btn-sm" 
                                                    onclick="showAuditLog(get_class($cabang), {{ $cabang->id }})"
                                                    title="Lihat Riwayat">
                                                <i class="fas fa-history"></i>
                                            </button>
                                        @endcan
                                    </td></tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-inbox text-4xl mb-4 text-gray-300"></i>
                                    <p>Tidak ada data pricelist uang jalan.</p>
                                    <a href="{{ route('master-pricelist-uang-jalan.create') }}"
                                       class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                                        <i class="fas fa-plus mr-2"></i>
                                        Tambah Pricelist Pertama
                                    </a>
                                </div>
                            </td>
                        
                                    <td>
                                        @can('audit-log-view')
                                            <button type="button" class="btn btn-info btn-sm" 
                                                    onclick="showAuditLog(get_class($cabang), {{ $cabang->id }})"
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
        @if($pricelistData->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $pricelistData->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Import Modal -->
<div id="importModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Import CSV Pricelist</h3>
                <button onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form action="{{ route('master-pricelist-uang-jalan.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">File CSV</label>
                    <input type="file" name="csv_file" accept=".csv,.txt" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-xs text-gray-500">
                        Format: Kode;Cabang;Wilayah;Dari;Ke;Uang Jalan 20ft;Uang Jalan 40ft;Keterangan;Liter;Jarak;Mel 20;Mel 40;Ongkos Truk 20ft;Antar Lokasi 20ft;Antar Lokasi 40ft
                    </p>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeImportModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow-lg z-50" id="successAlert">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    </div>
@endif

@if(session('error'))
    <div class="fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow-lg z-50" id="errorAlert">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
        </div>
    </div>
@endif

<script>
    function openImportModal() {
        document.getElementById('importModal').classList.remove('hidden');
    }

    function closeImportModal() {
        document.getElementById('importModal').classList.add('hidden');
    }

    // Auto hide alerts
    setTimeout(function() {
        const alerts = document.querySelectorAll('#successAlert, #errorAlert');
        alerts.forEach(alert => {
            if (alert) {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.5s ease-out';
                setTimeout(() => alert.remove(), 500);
            }
        });
    }, 5000);
</script>
@endsection
