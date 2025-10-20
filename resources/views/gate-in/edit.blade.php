@extends('layouts.app')

@section('title', 'Edit Gate In')
@section('page_title', 'Edit Gate In')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h1 class="text-xl font-semibold text-gray-900">Edit Gate In</h1>
                            <p class="text-sm text-gray-500 mt-1">{{ $gateIn->nomor_gate_in }} - {{ $gateIn->tanggal_formatted }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $gateIn->status === 'aktif' ? 'bg-green-100 text-green-800' :
                               ($gateIn->status === 'selesai' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                            {{ ucfirst($gateIn->status) }}
                        </span>
                        <a href="{{ route('gate-in.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>

            <!-- Gate In Info -->
            <div class="px-6 py-4 bg-gray-50">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Pelabuhan</div>
                        <div class="mt-1 text-sm text-gray-900">{{ $gateIn->pelabuhan }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</div>
                        <div class="mt-1 text-sm text-gray-900">{{ $gateIn->tanggal_gate_in ? $gateIn->tanggal_gate_in->format('d/m/Y H:i') : '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Kapal</div>
                        <div class="mt-1 text-sm text-gray-900">{{ $gateIn->kapal->nama_kapal }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Kontainer</div>
                        <div class="mt-1 text-sm text-gray-900">{{ $gateIn->kontainers->count() }} kontainer</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('gate-in.update', $gateIn) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Informasi Dasar</h3>
                    <p class="text-sm text-gray-500 mt-1">Edit informasi dasar gate in</p>
                </div>
                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Pelabuhan -->
                        <div>
                            <label for="pelabuhan" class="block text-sm font-medium text-gray-700 mb-2">Pelabuhan</label>
                            <select name="pelabuhan" id="pelabuhan"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                @foreach($pelabuhans as $pelabuhan)
                                    <option value="{{ $pelabuhan }}" {{ $gateIn->pelabuhan == $pelabuhan ? 'selected' : '' }}>
                                        {{ $pelabuhan }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pelabuhan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tanggal Gate In -->
                        <div>
                            <label for="tanggal_gate_in" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Gate In</label>
                            <input type="datetime-local" name="tanggal_gate_in" id="tanggal_gate_in"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   value="{{ old('tanggal_gate_in', $gateIn->tanggal_gate_in ? $gateIn->tanggal_gate_in->format('Y-m-d\TH:i') : '') }}" required>
                            @error('tanggal_gate_in')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Kapal -->
                        <div>
                            <label for="kapal_id" class="block text-sm font-medium text-gray-700 mb-2">Kapal</label>
                            <select name="kapal_id" id="kapal_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                @foreach($kapals as $kapal)
                                    <option value="{{ $kapal->id }}" {{ $gateIn->kapal_id == $kapal->id ? 'selected' : '' }}>
                                        {{ $kapal->nama_kapal }}{{ $kapal->kode_kapal ? ' - ' . $kapal->kode_kapal : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kapal_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" id="status"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                <option value="aktif" {{ $gateIn->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="selesai" {{ $gateIn->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="dibatalkan" {{ $gateIn->status == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Waktu Masuk -->
                        <div>
                            <label for="waktu_masuk" class="block text-sm font-medium text-gray-700 mb-2">Waktu Masuk</label>
                            <input type="datetime-local" name="waktu_masuk" id="waktu_masuk"
                                   value="{{ $gateIn->waktu_masuk ? $gateIn->waktu_masuk->format('Y-m-d\TH:i') : '' }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            @error('waktu_masuk')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Waktu Keluar -->
                        <div>
                            <label for="waktu_keluar" class="block text-sm font-medium text-gray-700 mb-2">Waktu Keluar</label>
                            <input type="datetime-local" name="waktu_keluar" id="waktu_keluar"
                                   value="{{ $gateIn->waktu_keluar ? $gateIn->waktu_keluar->format('Y-m-d\TH:i') : '' }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            @error('waktu_keluar')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kontainer Management -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Kelola Kontainer</h3>
                            <p class="text-sm text-gray-500 mt-1">Tambah atau hapus kontainer dari gate in ini</p>
                        </div>
                        <button type="button" id="add-kontainer-btn"
                                class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Tambah Kontainer
                        </button>
                    </div>
                </div>
                <div class="px-6 py-4">
                    <!-- Current Kontainers -->
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Kontainer Saat Ini ({{ $gateIn->kontainers->count() }})</h4>
                        @if($gateIn->kontainers->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($gateIn->kontainers as $kontainer)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">{{ $kontainer->nomor_seri_gabungan ?: $kontainer->nomor_kontainer }}</div>
                                                <div class="text-xs text-gray-500">{{ $kontainer->ukuran ?: '-' }} | {{ $kontainer->tipe_kontainer ?: '-' }}</div>
                                                @if($kontainer->status_gate_in)
                                                    <div class="text-xs text-green-600 font-medium mt-1">{{ ucfirst($kontainer->status_gate_in) }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <button type="button" class="remove-kontainer-btn text-red-600 hover:text-red-800"
                                                data-kontainer-id="{{ $kontainer->id }}"
                                                data-kontainer-name="{{ $kontainer->nomor_seri_gabungan ?: $kontainer->nomor_kontainer }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                <p class="text-sm text-gray-500">Belum ada kontainer</p>
                            </div>
                        @endif
                    </div>

                    <!-- Available Kontainers (Hidden by default) -->
                    <div id="available-kontainers" class="hidden">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Kontainer Tersedia</h4>
                        <div id="kontainer-loading" class="text-center py-8">
                            <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow rounded-md text-gray-500 bg-white hover:bg-gray-50 transition ease-in-out duration-150">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Memuat kontainer tersedia...
                            </div>
                        </div>
                        <div id="available-kontainer-list" class="hidden"></div>
                    </div>
                </div>
            </div>

            <!-- Keterangan -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Keterangan</h3>
                </div>
                <div class="px-6 py-4">
                    <textarea name="keterangan" id="keterangan" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none"
                              placeholder="Masukkan keterangan tambahan...">{{ old('keterangan', $gateIn->keterangan) }}</textarea>
                    @error('keterangan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Submit -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Pastikan semua informasi sudah benar sebelum menyimpan perubahan.</p>
                        </div>
                        <div class="flex space-x-3">
                            <a href="{{ route('gate-in.show', $gateIn) }}"
                               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                                Batal
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Add Kontainer Modal -->
<div id="add-kontainer-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Tambah Kontainer</h3>
                <button type="button" id="close-modal" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div id="modal-content">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Remove kontainer buttons
    document.querySelectorAll('.remove-kontainer-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const kontainerName = this.dataset.kontainerName;
            if (confirm(`Apakah Anda yakin ingin menghapus kontainer ${kontainerName} dari gate in ini?`)) {
                removeKontainer(this.dataset.kontainerId);
            }
        });
    });

    // Add kontainer button
    document.getElementById('add-kontainer-btn').addEventListener('click', function() {
        loadAvailableKontainers();
    });

    // Close modal
    document.getElementById('close-modal').addEventListener('click', function() {
        document.getElementById('add-kontainer-modal').classList.add('hidden');
    });

    // Close modal on outside click
    document.getElementById('add-kontainer-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });
});

function loadAvailableKontainers() {
    const modal = document.getElementById('add-kontainer-modal');
    const content = document.getElementById('modal-content');

    modal.classList.remove('hidden');
    content.innerHTML = `
        <div class="text-center py-8">
            <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow rounded-md text-gray-500 bg-white hover:bg-gray-50 transition ease-in-out duration-150">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Memuat kontainer tersedia...
            </div>
        </div>
    `;

    // Get current form values
    const terminalId = document.getElementById('terminal_id').value;
    const kapalId = document.getElementById('kapal_id').value;

    fetch(`/gate-in/get-kontainers?terminal_id=${terminalId}&kapal_id=${kapalId}&exclude_gate_in={{ $gateIn->id }}`)
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                content.innerHTML = `
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <h3 class="text-sm font-medium text-gray-900 mb-2">Tidak ada kontainer tersedia</h3>
                        <p class="text-sm text-gray-500">Semua kontainer sudah ditambahkan atau belum memenuhi kriteria</p>
                    </div>
                `;
            } else {
                let html = '<div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-96 overflow-y-auto">';

                data.forEach(kontainer => {
                    html += `
                        <div class="border border-gray-200 rounded-lg p-4 hover:border-purple-300 hover:bg-purple-50 transition-colors duration-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">${kontainer.nomor_seri_gabungan || kontainer.nomor_kontainer}</div>
                                        <div class="text-xs text-gray-500">${kontainer.ukuran || '-'} | ${kontainer.tipe_kontainer || '-'}</div>
                                    </div>
                                </div>
                                <button type="button" class="add-kontainer-to-gate-in inline-flex items-center px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white text-xs font-medium rounded-md transition-colors duration-200"
                                        data-kontainer-id="${kontainer.id}">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    Tambah
                                </button>
                            </div>
                        </div>
                    `;
                });

                html += '</div>';
                content.innerHTML = html;

                // Add event listeners
                document.querySelectorAll('.add-kontainer-to-gate-in').forEach(btn => {
                    btn.addEventListener('click', function() {
                        addKontainerToGateIn(this.dataset.kontainerId);
                    });
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            content.innerHTML = `
                <div class="text-center py-8">
                    <div class="text-red-600 mb-2">
                        <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.924-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <p class="text-sm text-red-600">Terjadi kesalahan saat memuat kontainer</p>
                </div>
            `;
        });
}

function addKontainerToGateIn(kontainerId) {
    fetch(`/gate-in/{{ $gateIn->id }}/add-kontainer`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            kontainer_id: kontainerId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Terjadi kesalahan');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menambahkan kontainer');
    });
}

function removeKontainer(kontainerId) {
    fetch(`/gate-in/{{ $gateIn->id }}/remove-kontainer`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            kontainer_id: kontainerId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Terjadi kesalahan');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menghapus kontainer');
    });
}
</script>
@endpush
