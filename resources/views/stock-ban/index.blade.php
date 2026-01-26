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
        
        // Show selected content
        document.getElementById('tab-content-' + tabName).style.display = 'block';
        
        // Update button styles
        const tabs = ['stock-ban', 'stock-ban-dalam', 'stock-ban-perut', 'stock-lock-kontainer'];
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
</script>
@endpush
