@extends('layouts.app')

@section('title', 'Detail Prospek Kapal - ' . $prospekKapal->voyage)

@section('content')
<div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <div class="flex items-center space-x-2 text-sm text-gray-500 mb-2">
            <a href="{{ route('prospek-kapal.index') }}" class="hover:text-gray-700">Prospek Kapal</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span>{{ $prospekKapal->voyage }}</span>
        </div>
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $prospekKapal->voyage }} - {{ $prospekKapal->nama_kapal }}</h1>
                <p class="text-gray-600 mt-2">Loading kontainer ke kapal berdasarkan tanda terima</p>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $prospekKapal->status_badge }}">
                {{ $prospekKapal->status_label }}
            </span>
        </div>
    </div>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-6">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Information --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Voyage Information --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Voyage</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Voyage</dt>
                        <dd class="text-sm text-gray-900">{{ $prospekKapal->voyage }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nama Kapal</dt>
                        <dd class="text-sm text-gray-900">{{ $prospekKapal->nama_kapal }}</dd>
                    </div>
                    @if($prospekKapal->pergerakanKapal)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Kapten</dt>
                        <dd class="text-sm text-gray-900">{{ $prospekKapal->pergerakanKapal->kapten }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Rute</dt>
                        <dd class="text-sm text-gray-900">{{ $prospekKapal->pergerakanKapal->pelabuhan_asal }} → {{ $prospekKapal->pergerakanKapal->pelabuhan_tujuan }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Tanggal Loading</dt>
                        <dd class="text-sm text-gray-900">{{ $prospekKapal->tanggal_loading->format('d/m/Y H:i') }}</dd>
                    </div>
                    @if($prospekKapal->estimasi_departure)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Estimasi Berangkat</dt>
                        <dd class="text-sm text-gray-900">{{ $prospekKapal->estimasi_departure->format('d/m/Y H:i') }}</dd>
                    </div>
                    @endif
                </div>
                @if($prospekKapal->keterangan)
                <div class="mt-4">
                    <dt class="text-sm font-medium text-gray-500">Keterangan</dt>
                    <dd class="text-sm text-gray-900">{{ $prospekKapal->keterangan }}</dd>
                </div>
                @endif
            </div>

            {{-- Container List --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Daftar Kontainer</h3>
                    @if($prospekKapal->status != 'completed' && $prospekKapal->status != 'cancelled')
                    <button type="button" onclick="openAddContainerModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition-colors duration-200">
                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Tambah Kontainer
                    </button>
                    @endif
                </div>

                @if($prospekKapal->kontainers->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sequence</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Kontainer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ukuran</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sumber</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($prospekKapal->kontainers->sortBy('loading_sequence') as $kontainer)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        #{{ $kontainer->loading_sequence }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $kontainer->nomor_kontainer }}</div>
                                        @if($kontainer->no_seal)
                                            <div class="text-sm text-gray-500">Seal: {{ $kontainer->no_seal }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $kontainer->ukuran_kontainer }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($kontainer->tanda_terima_id)
                                            <div class="text-sm text-gray-900">Tanda Terima</div>
                                            <div class="text-sm text-gray-500">{{ $kontainer->tandaTerima->no_surat_jalan ?? '-' }}</div>
                                        @elseif($kontainer->tanda_terima_tanpa_sj_id)
                                            <div class="text-sm text-gray-900">TT Tanpa SJ</div>
                                            <div class="text-sm text-gray-500">{{ $kontainer->tandaTerimaTanpaSuratJalan->id ?? '-' }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $kontainer->status_badge }}">
                                            {{ $kontainer->status_label }}
                                        </span>
                                        @if($kontainer->tanggal_loading)
                                            <div class="text-xs text-gray-500 mt-1">
                                                {{ $kontainer->tanggal_loading->format('d/m/Y H:i') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @if($prospekKapal->status != 'completed' && $prospekKapal->status != 'cancelled')
                                        <button type="button" onclick="openUpdateStatusModal({{ $kontainer->id }}, '{{ $kontainer->status_loading }}', '{{ $kontainer->tanggal_loading ? $kontainer->tanggal_loading->format('Y-m-d\TH:i') : '' }}', '{{ $kontainer->keterangan }}')"
                                                class="text-blue-600 hover:text-blue-900">
                                            Update Status
                                        </button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada kontainer</h3>
                        <p class="mt-1 text-sm text-gray-500">Tambahkan kontainer dari tanda terima untuk memulai loading.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Progress Card --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Progress Loading</h3>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Kontainer Loaded</span>
                            <span class="font-medium">{{ $prospekKapal->jumlah_kontainer_loaded }}/{{ $prospekKapal->jumlah_kontainer_terjadwal }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $prospekKapal->progress_percentage }}%"></div>
                        </div>
                        <div class="text-center text-lg font-semibold text-gray-900 mt-2">{{ $prospekKapal->progress_percentage }}%</div>
                    </div>

                    {{-- Status breakdown --}}
                    @php
                        $statusCounts = $prospekKapal->kontainers->groupBy('status_loading')->map->count();
                    @endphp
                    <div class="border-t pt-4">
                        <div class="space-y-2 text-sm">
                            @foreach(['pending' => 'Menunggu', 'ready' => 'Siap', 'loading' => 'Loading', 'loaded' => 'Loaded', 'problem' => 'Bermasalah'] as $status => $label)
                                @if(($statusCounts[$status] ?? 0) > 0)
                                <div class="flex justify-between">
                                    <span class="text-gray-500">{{ $label }}</span>
                                    <span class="font-medium">{{ $statusCounts[$status] }}</span>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            @if($prospekKapal->status != 'completed' && $prospekKapal->status != 'cancelled')
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <button type="button" onclick="markAllReady()" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg text-sm transition-colors duration-200">
                        Mark All Ready
                    </button>
                    <button type="button" onclick="markAllLoaded()" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition-colors duration-200">
                        Mark All Loaded
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Add Container Modal --}}
<div id="addContainerModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Tambah Kontainer</h3>
                <button type="button" onclick="closeAddContainerModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('prospek-kapal.add-kontainers', $prospekKapal) }}">
                @csrf
                <div class="space-y-4">
                    {{-- Tanda Terima --}}
                    @if($availableTandaTerima->count() > 0)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tanda Terima (Estimasi Kapal: {{ $prospekKapal->nama_kapal }})
                        </label>
                        @foreach($availableTandaTerima as $tt)
                        <div class="flex items-center mb-2">
                            <input type="checkbox" name="tanda_terima_ids[]" value="{{ $tt->id }}"
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label class="ml-2 text-sm text-gray-900">
                                {{ $tt->no_surat_jalan }} - {{ $tt->size }}ft ({{ $tt->jumlah_kontainer }} kontainer)
                                <br><span class="text-xs text-gray-500">Kapal: {{ $tt->estimasi_nama_kapal }}</span>
                            </label>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    {{-- Tanda Terima Tanpa Surat Jalan --}}
                    @if($availableTandaTerimaTanpaSJ->count() > 0)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tanda Terima Tanpa Surat Jalan (Estimasi Kapal: {{ $prospekKapal->nama_kapal }})
                        </label>
                        @foreach($availableTandaTerimaTanpaSJ as $tttsj)
                        <div class="flex items-center mb-2">
                            <input type="checkbox" name="tanda_terima_tanpa_sj_ids[]" value="{{ $tttsj->id }}"
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label class="ml-2 text-sm text-gray-900">
                                ID: {{ $tttsj->id }} - {{ $tttsj->size_kontainer }}ft ({{ $tttsj->jumlah_kontainer ?? 1 }} kontainer)
                                <br><span class="text-xs text-gray-500">Kapal: {{ $tttsj->estimasi_naik_kapal }}</span>
                            </label>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    @if($availableTandaTerima->count() == 0 && $availableTandaTerimaTanpaSJ->count() == 0)
                    <div class="text-center py-4">
                        <p class="text-sm text-gray-500">
                            Tidak ada tanda terima yang tersedia untuk kapal "{{ $prospekKapal->nama_kapal }}".
                        </p>
                        <p class="text-xs text-gray-400 mt-2">
                            Pastikan tanda terima sudah disetujui dan memiliki estimasi nama kapal yang sesuai.
                        </p>
                    </div>
                    @endif
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeAddContainerModal()"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Batal
                    </button>
                    @if($availableTandaTerima->count() > 0 || $availableTandaTerimaTanpaSJ->count() > 0)
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Tambah Kontainer
                    </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Update Status Modal --}}
<div id="updateStatusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Update Status Kontainer</h3>
                <button type="button" onclick="closeUpdateStatusModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form id="updateStatusForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status Loading</label>
                        <select name="status_loading" id="status_loading" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="pending">Menunggu</option>
                            <option value="ready">Siap Loading</option>
                            <option value="loading">Sedang Loading</option>
                            <option value="loaded">Sudah Dimuat</option>
                            <option value="problem">Bermasalah</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Loading</label>
                        <input type="datetime-local" name="tanggal_loading" id="tanggal_loading_input"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                        <textarea name="keterangan" id="keterangan_input" rows="3"
                                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeUpdateStatusModal()"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openAddContainerModal() {
    document.getElementById('addContainerModal').classList.remove('hidden');
}

function closeAddContainerModal() {
    document.getElementById('addContainerModal').classList.add('hidden');
}

function openUpdateStatusModal(kontainerId, currentStatus, currentDate, currentKeterangan) {
    const modal = document.getElementById('updateStatusModal');
    const form = document.getElementById('updateStatusForm');

    form.action = `/prospek-kapal/kontainer/${kontainerId}/update-status`;
    document.getElementById('status_loading').value = currentStatus;
    document.getElementById('tanggal_loading_input').value = currentDate;
    document.getElementById('keterangan_input').value = currentKeterangan;

    modal.classList.remove('hidden');
}

function closeUpdateStatusModal() {
    document.getElementById('updateStatusModal').classList.add('hidden');
}

function markAllReady() {
    if (confirm('Tandai semua kontainer sebagai siap loading?')) {
        // Implementation for bulk status update
        alert('Fitur ini akan segera tersedia');
    }
}

function markAllLoaded() {
    if (confirm('Tandai semua kontainer sebagai sudah dimuat?')) {
        // Implementation for bulk status update
        alert('Fitur ini akan segera tersedia');
    }
}

// Close modals when clicking outside
window.onclick = function(event) {
    const addModal = document.getElementById('addContainerModal');
    const updateModal = document.getElementById('updateStatusModal');

    if (event.target === addModal) {
        closeAddContainerModal();
    }
    if (event.target === updateModal) {
        closeUpdateStatusModal();
    }
}
</script>
@endsection
