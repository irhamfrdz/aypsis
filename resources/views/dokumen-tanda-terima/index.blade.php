@extends('layouts.app')

@section('title', 'Dokumen Tanda Terima - ' . $selectedKapal->nama_kapal . ' Voyage ' . $noVoyage)

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                        Voyage View
                    </span>
                    <h1 class="text-2xl font-bold text-gray-900">Dokumen Tanda Terima</h1>
                </div>
                <p class="text-gray-600 mt-1 flex items-center gap-2">
                    <i class="fas fa-ship text-purple-500"></i>
                    <span class="font-semibold text-gray-800">{{ $selectedKapal->nama_kapal }}</span>
                    <span class="text-gray-300">|</span>
                    <i class="fas fa-route text-purple-500"></i>
                    <span>Voyage:</span> <span class="font-semibold text-gray-800">{{ $noVoyage }}</span>
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('dokumen-tanda-terima.select') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition duration-200 shadow-sm">
                    <i class="fas fa-exchange-alt mr-2"></i>
                    Ganti Voyage
                </a>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="p-3 bg-blue-50 text-blue-600 rounded-lg">
                <i class="fas fa-file-invoice text-2xl"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Tanda Terima FCL</p>
                <h4 class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_fcl'] }}</h4>
            </div>
        </div>
        <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="p-3 bg-green-50 text-green-600 rounded-lg">
                <i class="fas fa-file-signature text-2xl"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Tanpa Surat Jalan</p>
                <h4 class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_tanpa_sj'] }}</h4>
            </div>
        </div>
        <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="p-3 bg-purple-50 text-purple-600 rounded-lg">
                <i class="fas fa-boxes text-2xl"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Tanda Terima LCL</p>
                <h4 class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_lcl'] }}</h4>
            </div>
        </div>
        <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="p-3 bg-yellow-50 text-yellow-600 rounded-lg">
                <i class="fas fa-weight-hanging text-2xl"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Volume</p>
                <h4 class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_volume'], 3) }} m³</h4>
            </div>
        </div>
        <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="p-3 bg-red-50 text-red-600 rounded-lg">
                <i class="fas fa-balance-scale text-2xl"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Berat</p>
                <h4 class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_weight'], 3) }} Ton</h4>
            </div>
        </div>
    </div>

    {{-- Filter & Search Form --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('dokumen-tanda-terima.index') }}">
            <input type="hidden" name="kapal_id" value="{{ request('kapal_id') }}">
            <input type="hidden" name="no_voyage" value="{{ request('no_voyage') }}">
            <div class="flex flex-col md:flex-row gap-3">
                <div class="flex-grow">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" name="search" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="Cari berdasarkan No. Tanda Terima, Kontainer, Supir, Penerima/Pengirim..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2 rounded-lg transition duration-200 shadow-sm font-medium">
                        Filter
                    </button>
                    <a href="{{ route('dokumen-tanda-terima.index', ['kapal_id' => request('kapal_id'), 'no_voyage' => request('no_voyage')]) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2 rounded-lg transition duration-200 font-medium">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Tabs Section --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        {{-- Navigation Tabs --}}
        <div class="border-b border-gray-200 bg-gray-50 flex overflow-x-auto">
            <button onclick="switchTab('fcl')" id="tab-btn-fcl" class="tab-btn px-6 py-4 border-b-2 font-medium text-sm transition-all duration-200 whitespace-nowrap focus:outline-none border-purple-600 text-purple-600">
                <i class="fas fa-file-invoice mr-2"></i>
                Tanda Terima FCL ({{ count($tandaTerimas) }})
            </button>
            <button onclick="switchTab('tanpa-sj')" id="tab-btn-tanpa-sj" class="tab-btn px-6 py-4 border-b-2 font-medium text-sm transition-all duration-200 whitespace-nowrap focus:outline-none border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                <i class="fas fa-file-signature mr-2"></i>
                Tanda Terima Tanpa Surat Jalan ({{ count($tandaTerimaTanpaSuratJalans) }})
            </button>
            <button onclick="switchTab('lcl')" id="tab-btn-lcl" class="tab-btn px-6 py-4 border-b-2 font-medium text-sm transition-all duration-200 whitespace-nowrap focus:outline-none border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                <i class="fas fa-boxes mr-2"></i>
                Tanda Terima LCL ({{ count($tandaTerimaLcls) }})
            </button>
        </div>

        {{-- Tab Contents --}}
        <div class="p-6">
            {{-- Tab 1: FCL --}}
            <div id="tab-content-fcl" class="tab-content block">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider" style="width: 50px;">No</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">No. Surat Jalan</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">No. BL</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">No. SJ Pabrik</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">No. DN</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Tanggal Terima</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">No. Kontainer</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">No. Seal</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Supir</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Pengirim</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Penerima</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Barang</th>
                                <th class="px-4 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Volume (m³)</th>
                                <th class="px-4 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Berat (Ton)</th>
                                <th class="px-4 py-3 text-center font-medium text-gray-500 uppercase tracking-wider" style="width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($tandaTerimas as $tt)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-4 py-3 text-center text-gray-900">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3 font-semibold text-gray-900">{{ $tt->no_surat_jalan }}</td>
                                <td class="px-4 py-3 text-gray-600 font-semibold">{{ $tt->nomor_bl ?: '-' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $tt->surat_jalan_pabrik ?: '-' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $tt->no_dn ?: '-' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $tt->tanggal ? $tt->tanggal->format('d/M/Y') : '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="bg-gray-100 px-2 py-1 rounded text-gray-700 font-mono text-xs">{{ $tt->no_kontainer ?: '-' }}</span>
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $tt->no_seal ?: '-' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $tt->supir ?: '-' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $tt->pengirim ?: '-' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $tt->penerima ?: '-' }}</td>
                                <td class="px-4 py-3 text-gray-600">
                                    @if(is_array($tt->nama_barang))
                                        {{ implode(', ', $tt->nama_barang) }}
                                    @else
                                        {{ $tt->nama_barang ?: '-' }}
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right font-medium text-gray-700">{{ number_format($tt->meter_kubik, 3) }}</td>
                                <td class="px-4 py-3 text-right font-medium text-gray-700">{{ number_format($tt->tonase, 3) }}</td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('tanda-terima.show', $tt->id) }}" target="_blank" class="p-1.5 bg-blue-50 text-blue-600 rounded-md hover:bg-blue-100 transition" title="Lihat">
                                            <i class="fas fa-eye text-sm"></i>
                                        </a>
                                        @can('tanda-terima-update')
                                        <a href="{{ route('tanda-terima.edit', $tt->id) }}" target="_blank" class="p-1.5 bg-yellow-50 text-yellow-600 rounded-md hover:bg-yellow-100 transition" title="Edit">
                                            <i class="fas fa-edit text-sm"></i>
                                        </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="15" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-info-circle mr-2"></i>Tidak ada data Tanda Terima FCL untuk voyage ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Tab 2: Tanpa SJ --}}
            <div id="tab-content-tanpa-sj" class="tab-content hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider" style="width: 50px;">No</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">No. Tanda Terima</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">No. BL</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">No. SJ Customer</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">SJ Pabrik</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Tanggal Terima</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">No. Kontainer</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">No. Seal</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Supir</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Pengirim</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Penerima</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Barang</th>
                                <th class="px-4 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Volume (m³)</th>
                                <th class="px-4 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Berat (Ton)</th>
                                <th class="px-4 py-3 text-center font-medium text-gray-500 uppercase tracking-wider" style="width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($tandaTerimaTanpaSuratJalans as $tts)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-4 py-3 text-center text-gray-900">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3 font-semibold text-gray-900">{{ $tts->no_tanda_terima }}</td>
                                <td class="px-4 py-3 text-gray-600 font-semibold">{{ $tts->nomor_bl ?: '-' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $tts->nomor_surat_jalan_customer ?: '-' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $tts->surat_jalan_pabrik ?: '-' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $tts->tanggal_tanda_terima ? $tts->tanggal_tanda_terima->format('d/M/Y') : '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="bg-gray-100 px-2 py-1 rounded text-gray-700 font-mono text-xs">{{ $tts->no_kontainer ?: '-' }}</span>
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $tts->no_seal ?: '-' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $tts->supir ?: '-' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $tts->pengirim ?: '-' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $tts->penerima ?: '-' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $tts->nama_barang ?: '-' }}</td>
                                <td class="px-4 py-3 text-right font-medium text-gray-700">{{ number_format($tts->meter_kubik, 3) }}</td>
                                <td class="px-4 py-3 text-right font-medium text-gray-700">{{ number_format($tts->tonase, 3) }}</td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('tanda-terima-tanpa-surat-jalan.show', $tts->id) }}" target="_blank" class="p-1.5 bg-blue-50 text-blue-600 rounded-md hover:bg-blue-100 transition" title="Lihat">
                                            <i class="fas fa-eye text-sm"></i>
                                        </a>
                                        @can('tanda-terima-tanpa-surat-jalan-update')
                                        <a href="{{ route('tanda-terima-tanpa-surat-jalan.edit', $tts->id) }}" target="_blank" class="p-1.5 bg-yellow-50 text-yellow-600 rounded-md hover:bg-yellow-100 transition" title="Edit">
                                            <i class="fas fa-edit text-sm"></i>
                                        </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="15" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-info-circle mr-2"></i>Tidak ada data Tanda Terima Tanpa Surat Jalan untuk voyage ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Tab 3: LCL --}}
            <div id="tab-content-lcl" class="tab-content hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider" style="width: 50px;">No</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">No. Tanda Terima</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">No. BL</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">No. SJ Customer</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Tanggal Terima</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">No. Kontainer</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">No. Seal</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Supir</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Pengirim</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Penerima</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Barang</th>
                                <th class="px-4 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Volume (m³)</th>
                                <th class="px-4 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Berat (Ton)</th>
                                <th class="px-4 py-3 text-center font-medium text-gray-500 uppercase tracking-wider" style="width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($tandaTerimaLcls as $ttl)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-4 py-3 text-center text-gray-900">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3 font-semibold text-gray-900">{{ $ttl->nomor_tanda_terima }}</td>
                                <td class="px-4 py-3 text-gray-600 font-semibold">{{ $ttl->nomor_bl ?: '-' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $ttl->no_surat_jalan_customer ?: '-' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $ttl->tanggal_tanda_terima ? $ttl->tanggal_tanda_terima->format('d/M/Y') : '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="bg-gray-100 px-2 py-1 rounded text-gray-700 font-mono text-xs">{{ $ttl->nomor_kontainer ?: '-' }}</span>
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $ttl->nomor_seal ?: '-' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $ttl->supir ?: '-' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $ttl->nama_pengirim ?: '-' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $ttl->nama_penerima ?: '-' }}</td>
                                <td class="px-4 py-3 text-gray-600">
                                    {{ $ttl->items->pluck('nama_barang')->unique()->implode(', ') ?: '-' }}
                                </td>
                                <td class="px-4 py-3 text-right font-medium text-gray-700">{{ number_format($ttl->total_volume, 3) }}</td>
                                <td class="px-4 py-3 text-right font-medium text-gray-700">{{ number_format($ttl->total_weight, 3) }}</td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('tanda-terima-lcl.show', $ttl->id) }}" target="_blank" class="p-1.5 bg-blue-50 text-blue-600 rounded-md hover:bg-blue-100 transition" title="Lihat">
                                            <i class="fas fa-eye text-sm"></i>
                                        </a>
                                        @can('tanda-terima-tanpa-surat-jalan-update')
                                        <a href="{{ route('tanda-terima-lcl.edit', $ttl->id) }}" target="_blank" class="p-1.5 bg-yellow-50 text-yellow-600 rounded-md hover:bg-yellow-100 transition" title="Edit">
                                            <i class="fas fa-edit text-sm"></i>
                                        </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="14" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-info-circle mr-2"></i>Tidak ada data Tanda Terima LCL untuk voyage ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function switchTab(tabId) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
        content.classList.remove('block');
    });

    // Remove active styles from all tab buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('border-purple-600', 'text-purple-600');
        btn.classList.add('border-transparent', 'text-gray-500');
    });

    // Show active tab content
    const activeContent = document.getElementById('tab-content-' + tabId);
    if (activeContent) {
        activeContent.classList.remove('hidden');
        activeContent.classList.add('block');
    }

    // Add active styles to active tab button
    const activeBtn = document.getElementById('tab-btn-' + tabId);
    if (activeBtn) {
        activeBtn.classList.add('border-purple-600', 'text-purple-600');
        activeBtn.classList.remove('border-transparent', 'text-gray-500');
    }
}
</script>
@endsection
