@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Manajemen Kwitansi</h2>
        <a href="{{ route('kwitansi.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors shadow-sm flex items-center">
            <i class="fas fa-plus mr-2"></i> Tambah Kwitansi Manual
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm flex justify-between items-center">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm flex justify-between items-center">
            <span>{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()" class="text-red-700 hover:text-red-900">&times;</button>
        </div>
    @endif

    <!-- Tabs Container -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex border-b border-gray-100 bg-gray-50/50">
            <button onclick="switchTab('kwitansi')" id="tab-kwitansi" class="px-6 py-4 text-sm font-medium border-b-2 transition-all duration-200 focus:outline-none border-blue-600 text-blue-600 bg-white">
                <i class="fas fa-file-invoice-dollar mr-2"></i> Daftar Kwitansi
            </button>
            <button onclick="switchTab('manifest')" id="tab-manifest" class="px-6 py-4 text-sm font-medium border-b-2 transition-all duration-200 focus:outline-none border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-100">
                <i class="fas fa-ship mr-2"></i> Siap Ditagih (Manifest)
            </button>
        </div>

        <!-- Tab: Daftar Kwitansi -->
        <div id="content-kwitansi" class="tab-content">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No Kwitansi</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pelanggan</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($kwitansis as $kwitansi)
                            <tr class="hover:bg-blue-50/30 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-blue-700">#{{ $kwitansi->kwt_no }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <div class="flex items-center">
                                        <i class="far fa-calendar-alt mr-2 text-gray-400"></i>
                                        {{ $kwitansi->tgl_inv ? \Carbon\Carbon::parse($kwitansi->tgl_inv)->format('d M Y') : '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    <div class="font-medium">{{ $kwitansi->pelanggan_nama ?: 'No Name' }}</div>
                                    <div class="text-xs text-gray-400">{{ $kwitansi->pelanggan_kode }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">
                                    Rp {{ number_format($kwitansi->total_invoice, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-3">
                                        <a href="{{ route('kwitansi.show', $kwitansi->id) }}" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('kwitansi.edit', $kwitansi->id) }}" class="p-2 bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-600 hover:text-white transition-all" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('kwitansi.destroy', $kwitansi->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kwitansi ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-all" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-inbox text-4xl text-gray-200 mb-3"></i>
                                        <p class="text-gray-500 font-medium">Belum ada data kwitansi.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab: Siap Ditagih (Manifest) -->
        <div id="content-manifest" class="tab-content hidden">
            <div class="p-4 bg-blue-50 border-b border-blue-100 flex justify-between items-center">
                <div>
                    <p class="text-sm text-blue-700">
                        <i class="fas fa-info-circle mr-1"></i> Data di bawah ini adalah Manifest yang siap untuk dibuatkan Kwitansi.
                    </p>
                    @if($namaKapal && $noVoyage)
                        <div class="mt-2 flex items-center gap-2">
                            <span class="px-2 py-1 bg-indigo-600 text-white text-xs rounded-full font-bold">
                                <i class="fas fa-ship mr-1"></i> {{ $namaKapal }}
                            </span>
                            <span class="px-2 py-1 bg-indigo-600 text-white text-xs rounded-full font-bold">
                                <i class="fas fa-route mr-1"></i> {{ $noVoyage }}
                            </span>
                            <a href="{{ route('kwitansi.index') }}#manifest" class="text-xs text-red-600 hover:underline font-bold ml-2">
                                <i class="fas fa-times-circle"></i> Hapus Filter
                            </a>
                        </div>
                    @endif
                </div>
                <div>
                    <a href="{{ route('kwitansi.select-ship') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors shadow-sm flex items-center text-xs font-bold">
                        <i class="fas fa-filter mr-2"></i> Pilih Kapal & Voyage
                    </a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No Manifest</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kontainer</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pengirim / Penerima</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($manifests as $manifest)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <div class="flex flex-col">
                                        <span class="font-bold">{{ $manifest->nomor_manifest }}</span>
                                        <span class="text-xs text-gray-400">BL: {{ $manifest->nomor_bl ?: '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $manifest->tanggal_berangkat ? \Carbon\Carbon::parse($manifest->tanggal_berangkat)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    <span class="px-2 py-1 bg-gray-100 rounded text-xs font-mono">{{ $manifest->nomor_kontainer }}</span>
                                    <span class="text-xs text-gray-400 ml-1">{{ $manifest->size_kontainer }}' {{ $manifest->tipe_kontainer }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <div class="text-xs uppercase font-semibold text-gray-400">Pengirim:</div>
                                    <div class="mb-1 truncate max-w-xs">{{ $manifest->pengirim }}</div>
                                    <div class="text-xs uppercase font-semibold text-gray-400">Penerima:</div>
                                    <div class="truncate max-w-xs">{{ $manifest->penerima }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('kwitansi.create', ['manifest_id' => $manifest->id]) }}" class="inline-flex items-center px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-xs font-bold">
                                        <i class="fas fa-file-invoice mr-1"></i> Buat Kwitansi
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-check-circle text-4xl text-gray-200 mb-3"></i>
                                        <p class="text-gray-500 font-medium">Tidak ada manifest yang tersedia untuk ditagih.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function switchTab(tab) {
        // Hide all contents
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        // Show selected content
        document.getElementById('content-' + tab).classList.remove('hidden');

        // Reset all tabs styles
        const tabs = ['kwitansi', 'manifest'];
        tabs.forEach(t => {
            const el = document.getElementById('tab-' + t);
            el.classList.remove('border-blue-600', 'text-blue-600', 'bg-white');
            el.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:bg-gray-100');
        });

        // Set active tab style
        const activeTab = document.getElementById('tab-' + tab);
        activeTab.classList.add('border-blue-600', 'text-blue-600', 'bg-white');
        activeTab.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:bg-gray-100');

        // Update URL hash without jumping
        window.history.replaceState(null, null, '#' + tab);
    }

    // Auto-select tab from hash
    window.addEventListener('DOMContentLoaded', () => {
        const hash = window.location.hash.substring(1);
        if (['kwitansi', 'manifest'].includes(hash)) {
            switchTab(hash);
        }
    });
</script>
@endsection
