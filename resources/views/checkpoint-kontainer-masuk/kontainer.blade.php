@extends('layouts.app')

@section('title', 'Kontainer Masuk - ' . $gudang->nama_gudang)
@section('page_title', 'Checkpoint Kontainer Masuk')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumb -->
        <nav class="mb-6 text-sm">
            <ol class="flex items-center space-x-2 text-gray-600">
                <li><a href="{{ route('checkpoint-kontainer-masuk.index') }}" class="hover:text-gray-900">Pilih Cabang</a></li>
                <li><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg></li>
                <li><a href="{{ route('checkpoint-kontainer-masuk.checkpoint', $cabangSlug) }}" class="hover:text-gray-900">{{ $cabangNama }}</a></li>
                <li><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg></li>
                <li class="text-gray-900 font-medium">{{ $gudang->nama_gudang }}</li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        Kontainer Masuk
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">Gudang: <span class="font-medium text-gray-900">{{ $gudang->nama_gudang }}</span> | {{ $gudang->lokasi }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                        </svg>
                        Total: {{ $kontainersDalamPerjalanan->count() }}
                    </span>
                    <button onclick="openManualModal()" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Tambah Manual
                    </button>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded-r-lg shadow-sm">
                <div class="flex">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <p class="ml-3 text-sm text-green-700 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <!-- Table Desktop -->
        <div class="hidden lg:block bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nomor Surat Jalan</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nomor Kontainer</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe/Ukuran</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supir</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No Plat</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu Keluar</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($kontainersDalamPerjalanan as $index => $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->no_surat_jalan ?? '-' }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-blue-600">{{ $item->no_kontainer }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <div class="flex flex-col">
                                        <span class="font-medium">{{ $item->ukuran ?? '-' }}</span>
                                        <span class="text-xs text-gray-500">{{ $item->tipe_kontainer ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">{{ $item->suratJalan->supir ?? $item->supir ?? '-' }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">{{ $item->suratJalan->no_plat ?? $item->no_plat ?? '-' }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $item->waktu_keluar ? \Carbon\Carbon::parse($item->waktu_keluar)->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-center text-sm">
                                    <button onclick="openMasukModal({{ $item->id }}, '{{ $item->no_kontainer }}')"
                                            class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-xs font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                        </svg>
                                        Checkpoint Masuk
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-12 text-center text-sm text-gray-500">
                                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                    <p class="font-medium text-gray-900">Tidak ada kontainer dalam perjalanan</p>
                                    <p class="text-xs text-gray-500 mt-1">Belum ada kontainer yang menuju gudang ini</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Cards Mobile -->
        <div class="lg:hidden space-y-4">
            @forelse($kontainersDalamPerjalanan as $index => $item)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1">
                            <p class="text-xs text-gray-500 mb-1">Nomor Surat Jalan</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $item->no_surat_jalan ?? '-' }}</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            #{{ $index + 1 }}
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3 mb-3 text-sm">
                        <div>
                            <p class="text-xs text-gray-500 mb-0.5">Nomor Kontainer</p>
                            <p class="font-medium text-blue-600">{{ $item->no_kontainer }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-0.5">Tipe/Ukuran</p>
                            <p class="font-medium text-gray-900">{{ $item->ukuran ?? '-' }}</p>
                            <p class="text-xs text-gray-500">{{ $item->tipe_kontainer ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-0.5">Supir</p>
                            <p class="font-medium text-gray-900">{{ $item->suratJalan->supir ?? $item->supir ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-0.5">No Plat</p>
                            <p class="font-medium text-gray-900">{{ $item->suratJalan->no_plat ?? $item->no_plat ?? '-' }}</p>
                        </div>
                    </div>
                    
                    <div class="mb-3 pb-3 border-b border-gray-200">
                        <p class="text-xs text-gray-500 mb-0.5">Waktu Keluar</p>
                        <p class="text-sm font-medium text-gray-900">{{ $item->waktu_keluar ? \Carbon\Carbon::parse($item->waktu_keluar)->format('d/m/Y H:i') : '-' }}</p>
                    </div>
                    
                    <button onclick="openMasukModal({{ $item->id }}, '{{ $item->no_kontainer }}')"
                            class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 active:scale-95 transition-all">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        Checkpoint Masuk
                    </button>
                </div>
            @empty
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <p class="font-medium text-gray-900 mb-1">Tidak ada kontainer dalam perjalanan</p>
                    <p class="text-xs text-gray-500">Belum ada kontainer yang menuju gudang ini</p>
                </div>
            @endforelse
        </div>

    </div>
</div>

<!-- Modal Checkpoint Masuk -->
<div id="masukModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-md shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Checkpoint Kontainer Masuk</h3>
            <button onclick="closeMasukModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form id="masukForm" method="POST" action="">
            @csrf
            <input type="hidden" id="modalNomorKontainer" name="nomor_kontainer">
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Kontainer</label>
                    <p id="displayNomorKontainer" class="text-sm font-semibold text-gray-900 px-3 py-2 bg-gray-50 rounded-lg"></p>
                </div>
                
                <div>
                    <label for="tanggal_masuk" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Masuk *</label>
                    <input type="date" id="tanggal_masuk" name="tanggal_masuk" required
                           value="{{ date('Y-m-d') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                </div>
                
                <div>
                    <label for="waktu_masuk" class="block text-sm font-medium text-gray-700 mb-1">Waktu Masuk *</label>
                    <input type="time" id="waktu_masuk" name="waktu_masuk" required
                           value="{{ date('H:i') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                </div>
                
                <div>
                    <label for="catatan_masuk" class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                    <textarea id="catatan_masuk" name="catatan_masuk" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"
                              placeholder="Catatan tambahan (opsional)"></textarea>
                </div>
            </div>
            
            <div class="flex gap-3 mt-6">
                <button type="button" onclick="closeMasukModal()"
                        class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah Manual -->
<div id="manualModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-md shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Tambah Manual Checkpoint Masuk</h3>
            <button onclick="closeManualModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form id="manualForm" method="POST" action="{{ route('checkpoint-kontainer-masuk.manual-masuk', ['cabangSlug' => $cabangSlug, 'gudangId' => $gudang->id]) }}">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label for="manual_nomor_kontainer" class="block text-sm font-medium text-gray-700 mb-1">Nomor Kontainer *</label>
                    <select id="manual_nomor_kontainer" name="nomor_kontainer" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">- Pilih Nomor Kontainer -</option>
                        @if(isset($kontainers) && $kontainers->count() > 0)
                            <optgroup label="Kontainers">
                                @foreach($kontainers as $kontainer)
                                    <option value="{{ $kontainer->nomor_seri_gabungan }}" 
                                            data-ukuran="{{ $kontainer->ukuran }}" 
                                            data-tipe="{{ $kontainer->tipe_kontainer }}">
                                        {{ $kontainer->nomor_seri_gabungan }} - {{ $kontainer->ukuran }} - {{ $kontainer->tipe_kontainer }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endif
                        @if(isset($stockKontainers) && $stockKontainers->count() > 0)
                            <optgroup label="Stock Kontainers">
                                @foreach($stockKontainers as $stock)
                                    <option value="{{ $stock->nomor_seri_gabungan }}" 
                                            data-ukuran="{{ $stock->ukuran }}" 
                                            data-tipe="{{ $stock->tipe_kontainer }}">
                                        {{ $stock->nomor_seri_gabungan }} - {{ $stock->ukuran }} - {{ $stock->tipe_kontainer }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endif
                    </select>
                </div>

                <div>
                    <label for="manual_ukuran" class="block text-sm font-medium text-gray-700 mb-1">Ukuran</label>
                    <input type="text" id="manual_ukuran" name="ukuran" readonly
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Otomatis terisi">
                </div>

                <div>
                    <label for="manual_tipe_kontainer" class="block text-sm font-medium text-gray-700 mb-1">Tipe Kontainer</label>
                    <input type="text" id="manual_tipe_kontainer" name="tipe_kontainer" readonly
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Otomatis terisi">
                </div>

                <div>
                    <label for="manual_no_surat_jalan" class="block text-sm font-medium text-gray-700 mb-1">Nomor Surat Jalan</label>
                    <input type="text" id="manual_no_surat_jalan" name="no_surat_jalan"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Opsional">
                </div>
                
                <div>
                    <label for="manual_tanggal_masuk" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Masuk *</label>
                    <input type="date" id="manual_tanggal_masuk" name="tanggal_masuk" required
                           value="{{ date('Y-m-d') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="manual_waktu_masuk" class="block text-sm font-medium text-gray-700 mb-1">Waktu Masuk *</label>
                    <input type="time" id="manual_waktu_masuk" name="waktu_masuk" required
                           value="{{ date('H:i') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="manual_catatan" class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                    <textarea id="manual_catatan" name="catatan_masuk" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Catatan tambahan (opsional)"></textarea>
                </div>
            </div>
            
            <div class="flex gap-3 mt-6">
                <button type="button" onclick="closeManualModal()"
                        class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openMasukModal(suratJalanId, nomorKontainer) {
    document.getElementById('masukModal').classList.remove('hidden');
    document.getElementById('displayNomorKontainer').textContent = nomorKontainer;
    document.getElementById('modalNomorKontainer').value = nomorKontainer;
    document.getElementById('masukForm').action = `/checkpoint-kontainer-masuk/${suratJalanId}/masuk`;
}

function closeMasukModal() {
    document.getElementById('masukModal').classList.add('hidden');
    document.getElementById('masukForm').reset();
}

function openManualModal() {
    document.getElementById('manualModal').classList.remove('hidden');
}

function closeManualModal() {
    document.getElementById('manualModal').classList.add('hidden');
    document.getElementById('manualForm').reset();
}

// Auto-fill ukuran and tipe kontainer when nomor kontainer is selected
document.getElementById('manual_nomor_kontainer')?.addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const ukuran = selectedOption.getAttribute('data-ukuran') || '';
    const tipe = selectedOption.getAttribute('data-tipe') || '';
    
    document.getElementById('manual_ukuran').value = ukuran;
    document.getElementById('manual_tipe_kontainer').value = tipe;
});

// Close modal when clicking outside
document.getElementById('masukModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeMasukModal();
    }
});

document.getElementById('manualModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeManualModal();
    }
});
</script>
@endsection
