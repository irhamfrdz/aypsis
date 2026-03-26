@extends('layouts.app')

@push('styles')
<style>
    /* Limit width for barang and pt_pengirim columns */
    .truncate-cell {
        max-width: 250px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        cursor: help;
    }
    
    .truncate-cell:hover {
        background-color: #f3f4f6;
    }

    .status-dropdown {
        min-width: 150px;
        z-index: 50;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-shipping-fast mr-3 text-teal-600 text-2xl"></i>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Data Prospek Batam</h1>
                    <p class="text-gray-600">Daftar prospek pengiriman kontainer wilayah Batam</p>
                </div>
            </div>
            @if(request('show_duplicates') == '1')
                <div class="bg-red-100 text-red-700 px-4 py-2 rounded-full text-sm font-bold flex items-center shadow-sm border border-red-200 animate-pulse">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    FILTER: NO. SURAT JALAN KEMBAR
                    <a href="{{ route('prospek-batam.index') }}" class="ml-3 text-red-900 hover:text-red-700" title="Matikan Filter">
                        <i class="fas fa-times-circle"></i>
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
        </div>
    @endif

    {{-- Filter Section --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <form method="GET" action="{{ route('prospek-batam.index') }}">
            @if(request()->has('per_page'))
                <input type="hidden" name="per_page" value="{{ request('per_page') }}">
            @endif
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                {{-- Search --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="No. Surat Jalan, Nama supir, kontainer, barang, pengirim..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500">
                </div>

                {{-- Status Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="sudah_muat" {{ request('status') == 'sudah_muat' ? 'selected' : '' }}>Sudah Muat</option>
                        <option value="sudah_muat_no_voyage" {{ request('status') == 'sudah_muat_no_voyage' ? 'selected' : '' }}>Sudah Muat (Tanpa Voyage)</option>
                        <option value="batal" {{ request('status') == 'batal' ? 'selected' : '' }}>Batal</option>
                    </select>
                </div>

                {{-- Tipe Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Kontainer</label>
                    <select name="tipe" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500">
                        <option value="">Semua Tipe</option>
                        <option value="FCL" {{ request('tipe') == 'FCL' ? 'selected' : '' }}>FCL</option>
                        <option value="LCL" {{ request('tipe') == 'LCL' ? 'selected' : '' }}>LCL</option>
                        <option value="CARGO" {{ request('tipe') == 'CARGO' ? 'selected' : '' }}>CARGO</option>
                    </select>
                </div>

                {{-- Ukuran Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ukuran</label>
                    <select name="ukuran" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500">
                        <option value="">Semua Ukuran</option>
                        <option value="20" {{ request('ukuran') == '20' ? 'selected' : '' }}>20 Feet</option>
                        <option value="40" {{ request('ukuran') == '40' ? 'selected' : '' }}>40 Feet</option>
                    </select>
                </div>

                {{-- Tujuan Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tujuan</label>
                    <input type="text"
                           name="tujuan"
                           value="{{ request('tujuan') }}"
                           placeholder="Tujuan pengiriman..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500">
                </div>
            </div>

            <div class="flex justify-between items-center mt-4">
                <div class="flex gap-2">
                    <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-md transition duration-200 inline-flex items-center">
                        <i class="fas fa-search mr-2"></i>
                        Filter
                    </button>
                    <a href="{{ route('prospek-batam.export-excel', request()->query()) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition duration-200 inline-flex items-center">
                        <i class="fas fa-file-excel mr-2"></i>
                        Export Excel
                    </a>
                    <a href="{{ route('prospek-batam.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition duration-200 inline-flex items-center">
                        <i class="fas fa-times mr-2"></i>
                        Reset
                    </a>
                    <a href="{{ route('prospek-batam.index', ['show_duplicates' => 1]) }}" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md transition duration-200 inline-flex items-center shadow-sm">
                        <i class="fas fa-clone mr-2"></i>
                        Cek No. SJ Kembar
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Table Section --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto" id="prospekBatamTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Surat Jalan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Supir</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barang</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PT/Pengirim</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ukuran</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Kontainer</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Seal</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tujuan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($prospeks as $key => $prospek)
                        <tr class="transition duration-150 {{ $prospek->status == 'aktif' ? 'bg-teal-50 hover:bg-teal-100' : ($prospek->status == 'sudah_muat' ? 'bg-green-50 hover:bg-green-100' : 'hover:bg-gray-50') }}">
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $prospeks->firstItem() + $key }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">
                                {{ $prospek->no_surat_jalan ?? '-' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $prospek->tanggal ? $prospek->tanggal->format('d/M/Y') : '-' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $prospek->nama_supir ?? '-' }}
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-900">
                                <div class="truncate-cell" title="{{ $prospek->barang ?? '-' }}">
                                    {{ $prospek->barang ?? '-' }}
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-900">
                                <div class="truncate-cell" title="{{ $prospek->pt_pengirim ?? '-' }}">
                                    {{ $prospek->pt_pengirim ?? '-' }}
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($prospek->tipe)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        {{ strtoupper($prospek->tipe) }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($prospek->ukuran)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $prospek->ukuran }} Feet
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">
                                {{ $prospek->nomor_kontainer ?? '-' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">
                                {{ $prospek->no_seal ?? '-' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $prospek->tujuan_pengiriman ?? '-' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                @php
                                    $statusConfig = [
                                        'aktif' => ['color' => 'bg-green-100 text-green-800', 'icon' => 'fa-check-circle', 'label' => 'Aktif'],
                                        'sudah_muat' => ['color' => 'bg-blue-100 text-blue-800', 'icon' => 'fa-ship', 'label' => 'Sudah Muat'],
                                        'batal' => ['color' => 'bg-red-100 text-red-800', 'icon' => 'fa-times-circle', 'label' => 'Batal']
                                    ];
                                    $config = $statusConfig[$prospek->status] ?? ['color' => 'bg-gray-100 text-gray-800', 'icon' => 'fa-question-circle', 'label' => $prospek->status];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $config['color'] }}">
                                    <i class="fas {{ $config['icon'] }} mr-1"></i>
                                    {{ $config['label'] }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('prospek-batam.show', $prospek->id) }}" class="text-blue-600 hover:text-blue-900" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @can('prospek-batam-edit')
                                        <a href="{{ route('prospek-batam.edit', $prospek->id) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <div class="relative group">
                                            <button type="button" class="text-teal-600 hover:text-teal-900 dropdown-toggle" title="Ubah Status">
                                                <i class="fas fa-exchange-alt"></i>
                                            </button>
                                            <div class="status-dropdown hidden absolute right-0 mt-2 w-40 bg-white rounded-md shadow-lg border border-gray-200 py-1">
                                                <button onclick="updateStatus('{{ $prospek->id }}', 'aktif')" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-teal-50">
                                                    <i class="fas fa-check-circle text-green-600 mr-2"></i> Aktif
                                                </button>
                                                <button onclick="updateStatus('{{ $prospek->id }}', 'sudah_muat')" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-teal-50">
                                                    <i class="fas fa-ship text-blue-600 mr-2"></i> Sudah Muat
                                                </button>
                                                <button onclick="updateStatus('{{ $prospek->id }}', 'batal')" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-teal-50">
                                                    <i class="fas fa-times-circle text-red-600 mr-2"></i> Batal
                                                </button>
                                            </div>
                                        </div>
                                    @endcan
                                    @can('prospek-batam-delete')
                                        <button onclick="deleteProspek('{{ $prospek->id }}', '{{ $prospek->no_surat_jalan }}')" class="text-red-600 hover:text-red-900" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" class="px-4 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-inbox text-4xl mb-3 text-gray-400"></i>
                                    <p class="text-lg font-medium">Tidak ada data prospek batam yang ditemukan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($prospeks->hasPages())
            <div class="bg-gray-50 px-4 py-3 border-t border-gray-200">
                {{ $prospeks->links() }}
            </div>
        @endif
    </div>

    {{-- Summary Cards --}}
    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-100 rounded-full p-3">
                    <i class="fas fa-hourglass-half text-2xl text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Prospek Aktif</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalBelumMuat }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-full p-3">
                    <i class="fas fa-ship text-2xl text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Sudah Muat</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalSudahMuat }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-red-500">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-red-100 rounded-full p-3">
                    <i class="fas fa-times-circle text-2xl text-red-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Batal</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalBatal }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle dropdown logic
        document.querySelectorAll('.dropdown-toggle').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const dropdown = this.nextElementSibling;
                document.querySelectorAll('.status-dropdown').forEach(d => {
                    if (d !== dropdown) d.classList.add('hidden');
                });
                dropdown.classList.toggle('hidden');
            });
        });

        document.addEventListener('click', function() {
            document.querySelectorAll('.status-dropdown').forEach(d => d.classList.add('hidden'));
        });
    });

    async function updateStatus(id, status) {
        if (!confirm(`Ubah status prospek ini menjadi ${status}?`)) return;

        try {
            const response = await fetch(`{{ url('prospek-batam') }}/${id}/update-status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status })
            });

            const result = await response.json();
            if (result.success) {
                location.reload();
            } else {
                alert(result.error || 'Gagal mengubah status');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan koneksi');
        }
    }

    async function deleteProspek(id, noSj) {
        if (!confirm(`Hapus data prospek dengan No. SJ: ${noSj}? Tindakan ini tidak dapat dibatalkan.`)) return;

        try {
            const response = await fetch(`{{ url('prospek-batam') }}/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();
            if (result.success) {
                location.reload();
            } else {
                alert(result.error || 'Gagal menghapus data');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan koneksi');
        }
    }
</script>
@endsection
