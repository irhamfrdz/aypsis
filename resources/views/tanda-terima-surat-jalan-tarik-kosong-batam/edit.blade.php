@extends('layouts.app')

@section('title', 'Edit Tanda Terima SJ Tarik Kosong Batam')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Tanda Terima</h1>
                <p class="text-gray-600">Perbarui informasi tanda terima penarikan kosong</p>
            </div>
            <a href="{{ route('tanda-terima-surat-jalan-tarik-kosong-batam.index') }}" class="text-gray-500 hover:text-gray-700 font-medium flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <form action="{{ route('tanda-terima-surat-jalan-tarik-kosong-batam.update', $item->id) }}" method="POST" class="p-8">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- Basic Info -->
                    <div class="space-y-4">
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Informasi Utama</h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Tanda Terima <span class="text-red-500">*</span></label>
                            <input type="text" name="no_tanda_terima" value="{{ old('no_tanda_terima', $item->no_tanda_terima) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 font-bold text-blue-600" readonly>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Tanda Terima <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_tanda_terima" value="{{ old('tanggal_tanda_terima', $item->tanggal_tanda_terima->format('Y-m-d')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Penerima</label>
                            <input type="text" name="penerima" value="{{ old('penerima', $item->penerima) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Nama penerima barang">
                        </div>
                    </div>

                    <!-- Surat Jalan Reference -->
                    <div class="space-y-4">
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Referensi Surat Jalan</h3>
                        
                        <div class="relative">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ganti Surat Jalan Tarik Kosong</label>
                            <div class="relative">
                                <input type="text" id="sj_search" class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Ketik No SJ atau No Kontainer...">
                                <div class="absolute left-3 top-2.5 text-gray-400">
                                    <i class="fas fa-search"></i>
                                </div>
                            </div>
                            <!-- Search Results -->
                            <div id="sj_results" class="absolute z-10 w-full bg-white border border-gray-200 rounded-lg mt-1 shadow-xl hidden max-h-60 overflow-y-auto">
                                <!-- Results will be injected here -->
                            </div>
                        </div>

                        <input type="hidden" name="surat_jalan_tarik_kosong_batam_id" id="sj_id" value="{{ old('surat_jalan_tarik_kosong_batam_id', $item->surat_jalan_tarik_kosong_batam_id) }}">

                        <div id="sj_details" class="p-4 bg-blue-50 rounded-lg border border-blue-100">
                            <div class="flex items-start gap-3">
                                <div class="bg-blue-600 text-white p-2 rounded-lg">
                                    <i class="fas fa-file-invoice"></i>
                                </div>
                                <div class="flex-grow">
                                    <div class="flex justify-between">
                                        <span class="text-xs font-bold text-blue-400 uppercase">Selected Surat Jalan</span>
                                        <button type="button" onclick="clearSJ()" class="text-xs text-red-500 hover:underline">Hapus</button>
                                    </div>
                                    <div id="sj_info" class="mt-1">
                                        <div class="font-bold text-blue-900">{{ $item->no_surat_jalan }}</div>
                                        <div class="text-sm text-blue-700">Kontainer: {{ $item->no_kontainer }} ({{ $item->size }})</div>
                                        <div class="text-sm text-blue-700">Supir: {{ $item->supir }} / {{ $item->no_plat }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Tambahan</label>
                    <textarea name="catatan" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Catatan tambahan jika ada...">{{ old('catatan', $item->catatan) }}</textarea>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                    <button type="reset" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-600 font-medium hover:bg-gray-50">Reset</button>
                    <button type="submit" class="px-8 py-2 bg-amber-600 text-white rounded-lg font-bold shadow-lg hover:bg-amber-700 transition duration-200">Update Tanda Terima</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const sjSearch = document.getElementById('sj_search');
    const sjResults = document.getElementById('sj_results');
    const sjDetails = document.getElementById('sj_details');
    const sjInfo = document.getElementById('sj_info');
    const sjIdInput = document.getElementById('sj_id');

    let debounceTimer;

    sjSearch.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const search = this.value;
        
        if (search.length < 2) {
            sjResults.classList.add('hidden');
            return;
        }

        debounceTimer = setTimeout(() => {
            fetch(`{{ route('tanda-terima-surat-jalan-tarik-kosong-batam.get-surat-jalan-data') }}?search=${search}`)
                .then(response => response.json())
                .then(data => {
                    sjResults.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(item => {
                            const div = document.createElement('div');
                            div.className = 'p-3 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-0';
                            div.innerHTML = `
                                <div class="font-bold text-gray-900">${item.no_surat_jalan}</div>
                                <div class="text-xs text-gray-500">Kontainer: ${item.no_kontainer} | Supir: ${item.supir}</div>
                            `;
                            div.onclick = () => selectSJ(item);
                            sjResults.appendChild(div);
                        });
                        sjResults.classList.remove('hidden');
                    } else {
                        sjResults.innerHTML = '<div class="p-3 text-center text-gray-500 italic">Data tidak ditemukan</div>';
                        sjResults.classList.remove('hidden');
                    }
                });
        }, 300);
    });

    function selectSJ(item) {
        sjIdInput.value = item.id;
        sjInfo.innerHTML = `
            <div class="font-bold text-blue-900">${item.no_surat_jalan}</div>
            <div class="text-sm text-blue-700">Kontainer: ${item.no_kontainer} (${item.size})</div>
            <div class="text-sm text-blue-700">Supir: ${item.supir} / ${item.no_plat}</div>
        `;
        sjDetails.classList.remove('hidden');
        sjResults.classList.add('hidden');
        sjSearch.value = '';
    }

    function clearSJ() {
        sjIdInput.value = '';
        sjDetails.classList.add('hidden');
    }

    // Close results when clicking outside
    document.addEventListener('click', function(e) {
        if (!sjSearch.contains(e.target) && !sjResults.contains(e.target)) {
            sjResults.classList.add('hidden');
        }
    });
</script>
@endpush
@endsection
