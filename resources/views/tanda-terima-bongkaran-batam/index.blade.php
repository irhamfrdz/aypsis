@extends('layouts.app')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('title', 'Tanda Terima Bongkaran Batam')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Tanda Terima Bongkaran Batam</h1>
                <p class="text-gray-600 mt-1">Kelola tanda terima surat jalan bongkaran khusus wilayah Batam</p>
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
        <form method="GET" action="{{ route('tanda-terima-bongkaran-batam.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <!-- Tipe -->
                <div class="md:col-span-3">
                    <label for="tipe" class="block text-sm font-medium text-gray-700 mb-2">Tampilkan</label>
                    <select name="tipe" 
                            id="tipe"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        <option value="surat_jalan" {{ request('tipe', 'surat_jalan') == 'surat_jalan' ? 'selected' : '' }}>Surat Jalan Bongkar Batam</option>
                        <option value="tanda_terima" {{ request('tipe') == 'tanda_terima' ? 'selected' : '' }}>Tanda Terima Bongkar Batam</option>
                    </select>
                </div>

                <!-- Search -->
                <div class="md:col-span-4">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                    <input type="text" 
                           name="search" 
                           id="search" 
                           value="{{ request('search') }}"
                           placeholder="Cari nomor SJ, kontainer..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>

                <!-- Status (if surat_jalan) -->
                @if(request('tipe', 'surat_jalan') == 'surat_jalan')
                <div class="md:col-span-3">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" 
                            id="status"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        <option value="">Semua Status</option>
                        <option value="sudah" {{ request('status') == 'sudah' ? 'selected' : '' }}>Sudah Tanda Terima</option>
                        <option value="belum" {{ request('status') == 'belum' ? 'selected' : '' }}>Belum Tanda Terima</option>
                    </select>
                </div>
                @endif

                <!-- Buttons -->
                <div class="md:col-span-2 flex items-end gap-2">
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-medium rounded-lg transition duration-200">
                        <i class="fas fa-search mr-2"></i>
                        Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            @if(request('tipe', 'surat_jalan') == 'tanda_terima')
                <!-- Table Tanda Terima Bongkaran Batam -->
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor TT</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal TT</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor SJ</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No Kontainer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gudang</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($tandaTerimas ?? [] as $index => $tandaTerima)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ ($tandaTerimas->currentPage() - 1) * $tandaTerimas->perPage() + $index + 1 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-teal-700">
                                {{ $tandaTerima->nomor_tanda_terima }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $tandaTerima->tanggal_tanda_terima->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $tandaTerima->suratJalanBongkaran->nomor_surat_jalan ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $tandaTerima->no_kontainer ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $tandaTerima->gudang->nama_gudang ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <form action="{{ route('tanda-terima-bongkaran-batam.destroy', $tandaTerima->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tanda terima ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 transition duration-200">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-inbox text-gray-300 text-4xl mb-3"></i>
                                    <p class="text-gray-500 font-medium">Tidak ada data tanda terima</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            @else
                <!-- Table Surat Jalan Bongkaran Batam -->
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor SJ</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal SJ</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No Kontainer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supir</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($suratJalans ?? [] as $index => $suratJalan)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ ($suratJalans->currentPage() - 1) * $suratJalans->perPage() + $index + 1 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $suratJalan->nomor_surat_jalan }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $suratJalan->tanggal_surat_jalan->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $suratJalan->no_kontainer ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $suratJalan->supir ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                @if($suratJalan->tandaTerima)
                                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Sudah Selesai</span>
                                @else
                                    <button type="button" 
                                            onclick="openTerimaBarangModal({{ $suratJalan->id }}, '{{ $suratJalan->nomor_surat_jalan }}', '{{ $suratJalan->no_kontainer }}')"
                                            class="inline-flex items-center px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-medium rounded-lg transition duration-200 shadow-sm">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        Terima Barang
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-inbox text-gray-300 text-4xl mb-3"></i>
                                    <p class="text-gray-500 font-medium">Tidak ada data surat jalan bongkaran Batam</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            @endif
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            @php
                $paginationData = request('tipe', 'surat_jalan') == 'tanda_terima' ? ($tandaTerimas ?? null) : ($suratJalans ?? null);
            @endphp
            @if($paginationData)
                {{ $paginationData->appends(request()->query())->links() }}
            @endif
        </div>
    </div>
</div>

<!-- Modal Terima Barang -->
<div id="terimaBarangModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-lg shadow-2xl rounded-xl bg-white">
        <div class="flex items-center justify-between pb-4 border-b">
            <h3 class="text-lg font-bold text-gray-900">Form Terima Barang (Batam)</h3>
            <button type="button" onclick="closeTerimaBarangModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form method="POST" action="{{ route('tanda-terima-bongkaran-batam.store') }}" class="mt-4 space-y-4">
            @csrf
            <input type="hidden" name="surat_jalan_bongkaran_id" id="modal_surat_jalan_id">
            
            <div class="bg-teal-50 p-3 rounded-lg border border-teal-100 flex justify-between items-center">
                <div>
                    <p class="text-xs text-teal-600 font-bold uppercase tracking-wider">Nomor SJ</p>
                    <p class="text-sm font-bold text-teal-900" id="modal_nomor_sj">-</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-teal-600 font-bold uppercase tracking-wider">No Kontainer</p>
                    <p class="text-sm font-bold text-teal-900" id="modal_no_kontainer">-</p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Terima</label>
                <input type="date" name="tanggal_tanda_terima" required value="{{ date('Y-m-d') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Lokasi Gudang</label>
                <select name="gudang_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                    <option value="">Pilih Gudang</option>
                    @foreach($gudangs ?? [] as $gudang)
                        <option value="{{ $gudang->id }}">{{ $gudang->nama_gudang }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex space-x-4 pt-2">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="lembur" value="1" class="rounded text-teal-600 focus:ring-teal-500">
                    <span class="text-sm text-gray-700">Lembur</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="nginap" value="1" class="rounded text-teal-600 focus:ring-teal-500">
                    <span class="text-sm text-gray-700">Nginap</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="tidak_lembur_nginap" value="1" checked class="rounded text-teal-600 focus:ring-teal-500">
                    <span class="text-sm text-gray-700">Normal</span>
                </label>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Keterangan</label>
                <textarea name="keterangan" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent" placeholder="Tambahkan catatan jika ada..."></textarea>
            </div>

            <div class="pt-4 flex gap-3">
                <button type="button" onclick="closeTerimaBarangModal()" class="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-lg transition duration-200">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-lg transition duration-200 shadow-md">
                    Simpan TT
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openTerimaBarangModal(id, sj, kontainer) {
        document.getElementById('modal_surat_jalan_id').value = id;
        document.getElementById('modal_nomor_sj').innerText = sj;
        document.getElementById('modal_no_kontainer').innerText = kontainer || '-';
        document.getElementById('terimaBarangModal').classList.remove('hidden');
    }

    function closeTerimaBarangModal() {
        document.getElementById('terimaBarangModal').classList.add('hidden');
    }

    // Auto-submit filter on select change
    document.getElementById('tipe').onchange = function() {
        this.form.submit();
    };
    if(document.getElementById('status')) {
        document.getElementById('status').onchange = function() {
            this.form.submit();
        };
    }
</script>
@endsection
