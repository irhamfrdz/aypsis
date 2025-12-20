@extends('layouts.app')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('title', 'Tanda Terima Bongkaran')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Tanda Terima Bongkaran</h1>
                <p class="text-gray-600 mt-1">Kelola dan pantau status pembayaran surat jalan bongkaran</p>
            </div>
            <div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('tanda-terima-bongkaran.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-medium rounded-lg shadow-sm transition duration-200">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Tanda Terima Bongkaran
                    </a>
                </div>
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

    <!-- Filter & Search -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('tanda-terima-bongkaran.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <!-- Tipe -->
                <div class="md:col-span-2">
                    <label for="tipe" class="block text-sm font-medium text-gray-700 mb-2">Tampilkan</label>
                    <select name="tipe" 
                            id="tipe"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        <option value="surat_jalan" {{ request('tipe', 'surat_jalan') == 'surat_jalan' ? 'selected' : '' }}>Surat Jalan Bongkar</option>
                        <option value="tanda_terima" {{ request('tipe') == 'tanda_terima' ? 'selected' : '' }}>Tanda Terima Bongkar</option>
                    </select>
                </div>

                <!-- Search -->
                <div class="md:col-span-3">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                    <input type="text" 
                           name="search" 
                           id="search" 
                           value="{{ request('search') }}"
                           placeholder="Cari nomor, kontainer..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>

                <!-- Kegiatan -->
                <div class="md:col-span-2">
                    <label for="kegiatan" class="block text-sm font-medium text-gray-700 mb-2">Kegiatan</label>
                    <select name="kegiatan" 
                            id="kegiatan"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        <option value="">Semua Kegiatan</option>
                        <option value="bongkar" {{ request('kegiatan') == 'bongkar' ? 'selected' : '' }}>Bongkar</option>
                        <option value="muat" {{ request('kegiatan') == 'muat' ? 'selected' : '' }}>Muat</option>
                        <option value="stuffing" {{ request('kegiatan') == 'stuffing' ? 'selected' : '' }}>Stuffing</option>
                        <option value="stripping" {{ request('kegiatan') == 'stripping' ? 'selected' : '' }}>Stripping</option>
                    </select>
                </div>

                <!-- Status -->
                <div class="md:col-span-2">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" 
                            id="status"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        <option value="">Semua Status</option>
                        <option value="sudah" {{ request('status') == 'sudah' ? 'selected' : '' }}>Sudah Tanda Terima</option>
                        <option value="belum" {{ request('status') == 'belum' ? 'selected' : '' }}>Belum Tanda Terima</option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="md:col-span-3 flex items-end gap-2">
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-medium rounded-lg transition duration-200">
                        <i class="fas fa-search mr-2"></i>
                        Filter
                    </button>
                    <a href="{{ route('tanda-terima-bongkaran.index') }}" 
                       class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition duration-200">
                        <i class="fas fa-redo mr-2"></i>
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            @if(request('tipe', 'surat_jalan') == 'tanda_terima')
                <!-- Table Tanda Terima Bongkaran -->
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor TT</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal TT</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor SJ</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No Kontainer</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kegiatan</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($tandaTerimas ?? [] as $index => $tandaTerima)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                                {{ ($tandaTerimas->currentPage() - 1) * $tandaTerimas->perPage() + $index + 1 }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $tandaTerima->nomor_tanda_terima ?? '-' }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                                {{ $tandaTerima->tanggal_tanda_terima ? $tandaTerima->tanggal_tanda_terima->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                                {{ $tandaTerima->suratJalanBongkaran->nomor_surat_jalan ?? '-' }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                                {{ $tandaTerima->no_kontainer ?? '-' }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                                {{ $tandaTerima->gudang->nama_gudang ?? '-' }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                                <span class="capitalize">{{ $tandaTerima->kegiatan ?? '-' }}</span>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm">
                                @if($tandaTerima->status == 'pending')
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                @elseif($tandaTerima->status == 'approved')
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">Approved</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Completed</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-center text-sm">
                                @can('tanda-terima-bongkaran-view')
                                <a href="{{ route('tanda-terima-bongkaran.show', $tandaTerima->id) }}"
                                   class="inline-flex items-center px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded transition duration-200">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-3 py-8 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-inbox text-gray-300 text-4xl mb-3"></i>
                                    <p class="text-gray-500 font-medium">Tidak ada data tanda terima bongkaran</p>
                                    <p class="text-gray-400 text-sm mt-1">Data tanda terima bongkaran akan muncul di sini</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            @else
                <!-- Table Surat Jalan Bongkaran -->
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor SJ</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal SJ</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No BL</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No Kontainer</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengirim</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supir</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kegiatan</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($suratJalans ?? [] as $index => $suratJalan)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                                {{ ($suratJalans->currentPage() - 1) * $suratJalans->perPage() + $index + 1 }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $suratJalan->nomor_surat_jalan ?? '-' }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                                {{ $suratJalan->tanggal_surat_jalan ? $suratJalan->tanggal_surat_jalan->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                                {{ $suratJalan->no_bl ?? '-' }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                                {{ $suratJalan->no_kontainer ?? '-' }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                                {{ $suratJalan->pengirim ?? '-' }}
                            </td>
                            <td class="px-3 py-2 text-sm text-gray-900">
                                <div>{{ $suratJalan->supir ?? '-' }}</div>
                                @if($suratJalan->supir2)
                                    <div class="text-xs text-gray-500 mt-0.5">{{ $suratJalan->supir2 }}</div>
                                @endif
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                                {{ $suratJalan->kegiatan ?? '-' }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm">
                                @if($suratJalan->tandaTerima)
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Sudah Tanda Terima</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Belum Tanda Terima</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-center text-sm">
                                @can('tanda-terima-bongkaran-create')
                                <button type="button" 
                                        onclick="openTerimaBarangModal({{ $suratJalan->id }}, '{{ $suratJalan->nomor_surat_jalan }}', '{{ $suratJalan->no_kontainer }}')"
                                        class="inline-flex items-center px-3 py-1 bg-teal-600 hover:bg-teal-700 text-white text-xs font-medium rounded transition duration-200"
                                        style="background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%); box-shadow: 0 2px 4px rgba(13, 148, 136, 0.2); border: none; cursor: pointer;">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Terima Barang
                                </button>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="px-3 py-8 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-inbox text-gray-300 text-4xl mb-3"></i>
                                    <p class="text-gray-500 font-medium">Tidak ada data surat jalan bongkaran</p>
                                    <p class="text-gray-400 text-sm mt-1">Data surat jalan bongkaran akan muncul di sini</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            @endif
        </div>

        <!-- Pagination -->
        @php
            $paginationData = request('tipe', 'surat_jalan') == 'tanda_terima' ? ($tandaTerimas ?? null) : ($suratJalans ?? null);
        @endphp
        @if($paginationData && $paginationData->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            <div class="flex items-center justify-between">
                <div class="flex-1 flex justify-between sm:hidden">
                    @if ($paginationData->onFirstPage())
                        <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">
                            Previous
                        </span>
                    @else
                        <a href="{{ $paginationData->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Previous
                        </a>
                    @endif

                    @if ($paginationData->hasMorePages())
                        <a href="{{ $paginationData->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Next
                        </a>
                    @else
                        <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">
                            Next
                        </span>
                    @endif
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Menampilkan
                            <span class="font-medium">{{ $paginationData->firstItem() ?? 0 }}</span>
                            sampai
                            <span class="font-medium">{{ $paginationData->lastItem() ?? 0 }}</span>
                            dari
                            <span class="font-medium">{{ $paginationData->total() }}</span>
                            hasil
                        </p>
                    </div>
                    <div>
                        {{ $paginationData->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal Terima Barang -->
<div id="terimaBarangModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between pb-3 border-b">
            <h3 class="text-xl font-semibold text-gray-900">Terima Barang</h3>
            <button type="button" onclick="closeTerimaBarangModal()" class="text-gray-400 hover:text-gray-500">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form id="terimaBarangForm" method="POST" action="{{ route('tanda-terima-bongkaran.store') }}">
            @csrf
            <input type="hidden" name="surat_jalan_bongkaran_id" id="modal_surat_jalan_id">
            
            <div class="mt-4 space-y-4">
                <!-- Info SJ -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-gray-600">Nomor SJ:</span>
                            <span class="font-medium text-gray-900 ml-2" id="modal_nomor_sj">-</span>
                        </div>
                        <div>
                            <span class="text-gray-600">No Kontainer:</span>
                            <span class="font-medium text-gray-900 ml-2" id="modal_no_kontainer">-</span>
                        </div>
                    </div>
                </div>

                <!-- Nomor Tanda Terima -->
                <div>
                    <label for="nomor_tanda_terima" class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor Tanda Terima <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="nomor_tanda_terima" 
                           id="nomor_tanda_terima"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>

                <!-- Tanggal Terima -->
                <div>
                    <label for="tanggal_tanda_terima" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Terima <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           name="tanggal_tanda_terima" 
                           id="tanggal_tanda_terima"
                           required
                           value="{{ date('Y-m-d') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>

                <!-- Lokasi Gudang -->
                <div>
                    <label for="gudang_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Lokasi <span class="text-red-500">*</span>
                    </label>
                    <select name="gudang_id" 
                            id="gudang_id"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        <option value="">Pilih Lokasi</option>
                        @foreach($gudangs ?? [] as $gudang)
                            <option value="{{ $gudang->id }}">{{ $gudang->nama_gudang }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Keterangan -->
                <div>
                    <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                        Keterangan
                    </label>
                    <textarea name="keterangan" 
                              id="keterangan"
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500"></textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3 pt-3 border-t">
                <button type="button" 
                        onclick="closeTerimaBarangModal()"
                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition duration-200">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-medium rounded-lg transition duration-200"
                        style="background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%); box-shadow: 0 2px 4px rgba(13, 148, 136, 0.2); border: none; cursor: pointer;">
                    <i class="fas fa-save mr-2"></i>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Auto-submit form on filter change
    document.getElementById('tipe')?.addEventListener('change', function() {
        this.form.submit();
    });
    
    document.getElementById('kegiatan')?.addEventListener('change', function() {
        this.form.submit();
    });
    
    document.getElementById('status')?.addEventListener('change', function() {
        this.form.submit();
    });

    // Modal functions
    function openTerimaBarangModal(suratJalanId, nomorSj, noKontainer) {
        document.getElementById('modal_surat_jalan_id').value = suratJalanId;
        document.getElementById('modal_nomor_sj').textContent = nomorSj || '-';
        document.getElementById('modal_no_kontainer').textContent = noKontainer || '-';
        document.getElementById('terimaBarangModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeTerimaBarangModal() {
        document.getElementById('terimaBarangModal').classList.add('hidden');
        document.getElementById('terimaBarangForm').reset();
        document.body.style.overflow = 'auto';
    }

    // Close modal when clicking outside
    document.getElementById('terimaBarangModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeTerimaBarangModal();
        }
    });

    // Close modal with ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeTerimaBarangModal();
        }
    });
</script>
@endpush
