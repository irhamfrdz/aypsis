@extends('layouts.app')


@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('title', 'Master Kapal')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Master Kapal</h1>
                <p class="text-gray-600 mt-1">Kelola data kapal dalam sistem</p>
            </div>
            <div class="flex gap-2">
                @can('master-kapal.view')
                <a href="{{ route('master-kapal.export') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}" 
                   class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg transition duration-200 inline-flex items-center">
                    <i class="fas fa-file-export mr-2"></i> Export CSV
                </a>
                @endcan
                @can('master-kapal.create')
                <a href="{{ route('master-kapal.import-form') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200 inline-flex items-center">
                    <i class="fas fa-file-import mr-2"></i> Import CSV
                </a>
                <a href="{{ route('master-kapal.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200 inline-flex items-center">
                    <i class="fas fa-plus mr-2"></i> Tambah Kapal
                </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if(session('import_errors'))
    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-orange-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-orange-800">Peringatan: Beberapa baris tidak dapat diimport</h3>
                <ul class="mt-2 text-sm text-orange-700 list-disc pl-5 space-y-1">
                    @foreach(session('import_errors') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <!-- Main Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Daftar Kapal</h2>
        </div>

        <div class="p-6">
            <!-- Filter & Search -->
            <form method="GET" action="{{ route('master-kapal.index') }}" class="mb-4">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                    <div class="md:col-span-3">
                        <input type="text"
                               name="search"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Cari nama kapal, nickname..."
                               value="{{ request('search') }}">
                    </div>
                    <div class="md:col-span-2">
                        <select name="status" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Status</option>
                            <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                    <div class="md:col-span-3">
                        <select name="pemilik" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Pemilik</option>
                            @foreach($pemilikList as $pemilik)
                                <option value="{{ $pemilik }}" {{ request('pemilik') == $pemilik ? 'selected' : '' }}>
                                    {{ $pemilik }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-md transition duration-200 text-sm">
                            <i class="fas fa-search mr-1"></i> Cari
                        </button>
                    </div>
                    <div class="md:col-span-2">
                        <a href="{{ route('master-kapal.index') }}" class="block text-center w-full bg-gray-500 hover:bg-gray-600 text-white px-3 py-2 rounded-md transition duration-200 text-sm">
                            <i class="fas fa-redo mr-1"></i> Reset
                        </a>
                    </div>
                </div>
            </form>

            <!-- Table -->
            <div class="overflow-x-auto">
                <!-- Rows per page selector -->
                <div class="mt-3 flex items-center justify-between text-sm text-gray-600">
                    <div class="flex items-center space-x-2">
                        <span>Tampilkan</span>
                        <form method="GET" action="{{ route('master-kapal.index') }}" class="inline">
                            {{-- Preserve existing search and sort parameters --}}
                            @foreach(request()->query() as $key => $value)
                                @if($key !== 'per_page' && $key !== 'page')
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endif
                            @endforeach

                            <select name="per_page"
                                    onchange="this.form.submit()"
                                    class="mx-1 px-3 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 bg-white">
                                <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page', 10) == 100 ? 'selected' : '' }}>100</option>
                            </select>
                        </form>
                        <span>baris per halaman</span>
                    </div>

                    @if($kapals->total() > 0)
                        <div class="text-sm text-gray-500">
                            Menampilkan {{ $kapals->firstItem() }} - {{ $kapals->lastItem() }} dari {{ $kapals->total() }} total kapal
                        </div>
                    @endif
                </div>
                
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">
                                No
                            </th>
                            <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nama Kapal
                            </th>
                            <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nickname
                            </th>
                            <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Pelayaran
                            </th>
                            <th scope="col" class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                GT
                            </th>
                            <th scope="col" class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kapasitas
                            </th>
                            <th scope="col" class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-20">
                                Status
                            </th>
                            <th scope="col" class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-28">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($kapals as $kapal)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900 text-center font-medium">
                                {{ ($kapals->currentPage() - 1) * $kapals->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $kapal->nama_kapal }}</div>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-600">
                                {{ $kapal->nickname ?? '-' }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-600">
                                {{ $kapal->pelayaran ?? '-' }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-center text-sm text-gray-900">
                                @if($kapal->gross_tonnage)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        {{ number_format($kapal->gross_tonnage, 1) }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-center text-sm text-gray-900">
                                @php
                                    $totalKapasitas = ($kapal->kapasitas_kontainer_palka ?? 0) + ($kapal->kapasitas_kontainer_deck ?? 0);
                                @endphp
                                @if($totalKapasitas > 0)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        <i class="fas fa-calculator mr-1"></i>
                                        {{ number_format($totalKapasitas) }}
                                    </span>
                                    @if($kapal->kapasitas_kontainer_palka && $kapal->kapasitas_kontainer_deck)
                                        <div class="text-xs text-gray-500 mt-1">
                                            Palka: {{ number_format($kapal->kapasitas_kontainer_palka) }} |
                                            Deck: {{ number_format($kapal->kapasitas_kontainer_deck) }}
                                        </div>
                                    @endif
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-center">
                                @if($kapal->status == 'aktif')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-center text-sm font-medium">
                                <div class="flex items-center justify-center gap-1">
                                    @can('master-kapal.view')
                                    <a href="{{ route('master-kapal.show', $kapal->id) }}"
                                       class="inline-flex items-center px-2 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded text-xs transition duration-150"
                                       title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @endcan
                                    @can('master-kapal.edit')
                                    <a href="{{ route('master-kapal.edit', $kapal->id) }}"
                                       class="inline-flex items-center px-2 py-1 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 rounded text-xs transition duration-150"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button"
                                            onclick="showAuditLog('{{ get_class($kapal) }}', '{{ $kapal->id }}', '{{ $kapal->nama_kapal }}')"
                                            class="inline-flex items-center px-2 py-1 bg-purple-100 hover:bg-purple-200 text-purple-700 rounded text-xs transition duration-150"
                                            title="Lihat Riwayat">
                                        <i class="fas fa-history"></i>
                                    </button>
                                    @endcan
                                    @can('master-kapal.delete')
                                    <form action="{{ route('master-kapal.destroy', $kapal->id) }}"
                                          method="POST"
                                          class="inline-block"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus kapal ini?\n\nKode: {{ $kapal->kode }}\nNama: {{ $kapal->nama_kapal }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center px-2 py-1 bg-red-100 hover:bg-red-200 text-red-700 rounded text-xs transition duration-150"
                                                title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-3 py-8 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-ship text-gray-300 text-4xl mb-3"></i>
                                    <p class="text-gray-500 text-base font-medium">Tidak ada data kapal</p>
                                    <p class="text-gray-400 text-sm mt-1">Mulai dengan menambahkan data kapal baru</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($kapals->hasPages())
                @include('components.modern-pagination', ['paginator' => $kapals])
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.bg-green-50, .bg-red-50, .bg-orange-50');
        alerts.forEach(alert => {
            alert.style.transition = 'opacity 0.5s ease-out';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);
</script>
@endpush

<!-- Audit Log Modal -->
@include('components.audit-log-modal')

@endsection
