@extends('layouts.app')

@section('title', 'Edit Tanda Terima SJ Kontainer Sewa')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                    <span class="p-2 bg-amber-50 text-amber-700 rounded-lg">
                        <i class="fas fa-edit"></i>
                    </span>
                    Edit Tanda Terima SJ Kontainer Sewa
                </h1>
                <p class="text-gray-600 mt-1">Ubah rincian informasi tanda terima kontainer sewa</p>
            </div>
            <div>
                <a href="{{ route('tanda-terima-surat-jalan-kontainer-sewa.index', ['tipe' => 'tanda_terima']) }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition duration-200 text-sm">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Error Alerts -->
    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
            </div>
            <div class="ml-3">
                <ul class="list-disc pl-5 text-sm text-red-800 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <!-- Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <form method="POST" action="{{ route('tanda-terima-surat-jalan-kontainer-sewa.update', $tandaTerima->id) }}" class="p-6 md:p-8 space-y-6">
            @csrf
            @method('PUT')

            <!-- Info SJ Card (Read-Only) -->
            <div class="bg-cyan-50 border border-cyan-100 p-5 rounded-xl">
                <h3 class="text-sm font-bold text-cyan-800 uppercase tracking-wider mb-3">Informasi Asal Surat Jalan</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
                    <div>
                        <span class="text-cyan-700 block font-medium">Nomor Surat Jalan</span>
                        <span class="font-bold text-cyan-900 mt-1 block">{{ $tandaTerima->nomor_surat_jalan ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-cyan-700 block font-medium">Nomor Kontainer</span>
                        <span class="font-bold text-cyan-900 mt-1 block">{{ $tandaTerima->nomor_kontainer ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-cyan-700 block font-medium">Tipe / Ukuran</span>
                        <span class="font-bold text-cyan-900 mt-1 block">{{ $tandaTerima->tipe_kontainer ?? '-' }} / {{ $tandaTerima->ukuran ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- Form Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nomor Tanda Terima -->
                <div>
                    <label for="nomor_tanda_terima" class="block text-sm font-semibold text-gray-700 mb-2">
                        Nomor Tanda Terima <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="nomor_tanda_terima" 
                           id="nomor_tanda_terima"
                           required
                           value="{{ old('nomor_tanda_terima', $tandaTerima->nomor_tanda_terima) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-sm font-bold text-cyan-900"
                           placeholder="Masukkan nomor tanda terima">
                </div>

                <!-- Nomor Kontainer -->
                <div>
                    <label for="nomor_kontainer" class="block text-sm font-semibold text-gray-700 mb-2">
                        Nomor Kontainer <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="nomor_kontainer" 
                           id="nomor_kontainer"
                           list="kontainer-list"
                           required
                           minlength="11"
                           maxlength="11"
                           value="{{ old('nomor_kontainer', $tandaTerima->nomor_kontainer) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-sm font-semibold"
                           placeholder="Masukkan nomor kontainer">
                    <datalist id="kontainer-list">
                        @foreach($kontainers as $k)
                            <option value="{{ $k->nomor_seri_gabungan }}">
                                {{ $k->vendor }} - {{ $k->ukuran }}
                            </option>
                        @endforeach
                    </datalist>
                </div>

                <!-- Tanggal Terima -->
                <div>
                    <label for="tanggal_tanda_terima" class="block text-sm font-semibold text-gray-700 mb-2">
                        Tanggal Terima <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           name="tanggal_tanda_terima" 
                           id="tanggal_tanda_terima"
                           required
                           value="{{ old('tanggal_tanda_terima', $tandaTerima->tanggal_tanda_terima ? $tandaTerima->tanggal_tanda_terima->format('Y-m-d') : '') }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-sm">
                </div>

                <!-- Tanggal Mulai Sewa -->
                <div>
                    <label for="tanggal_mulai_sewa" class="block text-sm font-semibold text-gray-700 mb-2">
                        Tanggal Mulai Sewa <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           name="tanggal_mulai_sewa" 
                           id="tanggal_mulai_sewa"
                           required
                           value="{{ old('tanggal_mulai_sewa', $tandaTerima->tanggal_mulai_sewa ? $tandaTerima->tanggal_mulai_sewa->format('Y-m-d') : '') }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-sm">
                </div>

                <!-- Supir -->
                <div>
                    <label for="supir_search" class="block text-sm font-semibold text-gray-700 mb-2">
                        Supir
                    </label>
                    <div class="relative">
                        <input type="text" 
                               id="supir_search"
                               autocomplete="off"
                               value="{{ old('supir', $tandaTerima->supir) }}"
                               placeholder="Cari atau ketik nama supir..."
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-sm">
                        <input type="hidden" name="supir" id="supir" value="{{ old('supir', $tandaTerima->supir) }}">
                        <div id="supir_dropdown" class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                            <div class="py-1">
                                @foreach($supirs ?? [] as $supir)
                                    <div class="supir-option px-4 py-2 hover:bg-cyan-50 hover:text-cyan-700 cursor-pointer text-sm" 
                                         data-value="{{ $supir->nama_lengkap }}"
                                         data-plat="{{ $supir->plat }}">
                                        {{ $supir->nama_lengkap }} ({{ $supir->plat ?? 'No Plat' }})
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- No Plat -->
                <div>
                    <label for="no_plat" class="block text-sm font-semibold text-gray-700 mb-2">
                        Nomor Plat Kendaraan
                    </label>
                    <input type="text" 
                           name="no_plat" 
                           id="no_plat"
                           value="{{ old('no_plat', $tandaTerima->no_plat) }}"
                           placeholder="Contoh: BP 1234 XX"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-sm">
                </div>
            </div>

            <!-- Keterangan -->
            <div>
                <label for="keterangan" class="block text-sm font-semibold text-gray-700 mb-2">
                    Keterangan Tambahan
                </label>
                <textarea name="keterangan" 
                          id="keterangan"
                          rows="4"
                          placeholder="Masukkan catatan jika ada..."
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-sm">{{ old('keterangan', $tandaTerima->keterangan) }}</textarea>
            </div>

            <!-- Checkbox Lembur & Nginap -->
            <div class="flex flex-col md:flex-row md:space-x-6 space-y-3 md:space-y-0 p-5 rounded-xl bg-amber-50 border border-amber-200">
                <div class="flex items-center">
                    <input type="checkbox"
                           name="lembur"
                           id="lembur"
                           value="1"
                           {{ old('lembur', $tandaTerima->lembur) ? 'checked' : '' }}
                           class="h-5 w-5 text-cyan-600 focus:ring-cyan-500 border-gray-300 rounded cursor-pointer">
                    <label for="lembur" class="ml-2 block text-sm font-medium text-gray-900 cursor-pointer select-none">
                        Lembur
                    </label>
                </div>
                <div class="flex items-center">
                    <input type="checkbox"
                           name="nginap"
                           id="nginap"
                           value="1"
                           {{ old('nginap', $tandaTerima->nginap) ? 'checked' : '' }}
                           class="h-5 w-5 text-cyan-600 focus:ring-cyan-500 border-gray-300 rounded cursor-pointer">
                    <label for="nginap" class="ml-2 block text-sm font-medium text-gray-900 cursor-pointer select-none">
                        Nginap
                    </label>
                </div>
                <div class="flex items-center">
                    <input type="checkbox"
                           name="tidak_lembur_nginap"
                           id="tidak_lembur_nginap"
                           value="1"
                           {{ old('tidak_lembur_nginap', $tandaTerima->tidak_lembur_nginap) ? 'checked' : '' }}
                           class="h-5 w-5 text-cyan-600 focus:ring-cyan-500 border-gray-300 rounded cursor-pointer">
                    <label for="tidak_lembur_nginap" class="ml-2 block text-sm font-medium text-gray-900 cursor-pointer select-none">
                        Tidak Lembur & Nginap
                    </label>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-3 pt-6 border-t">
                <a href="{{ route('tanda-terima-surat-jalan-kontainer-sewa.index', ['tipe' => 'tanda_terima']) }}" 
                   class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition duration-200 text-sm">
                    Batal
                </a>
                <button type="submit"
                        class="px-5 py-2.5 bg-cyan-600 hover:bg-cyan-700 text-white font-semibold rounded-lg shadow-sm transition duration-200 text-sm border-0 cursor-pointer">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Searchable Supir dropdown
    const supirSearch = document.getElementById('supir_search');
    const supirDropdown = document.getElementById('supir_dropdown');
    const supirHidden = document.getElementById('supir');
    const supirOptions = document.querySelectorAll('.supir-option');
    const inputPlat = document.getElementById('no_plat');

    supirSearch?.addEventListener('focus', function() {
        supirDropdown.classList.remove('hidden');
        filterSupirs('');
    });

    supirSearch?.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        filterSupirs(query);
    });

    function filterSupirs(query) {
        let hasOptions = false;
        supirOptions.forEach(opt => {
            const text = opt.textContent.toLowerCase();
            if (text.includes(query)) {
                opt.style.display = 'block';
                hasOptions = true;
            } else {
                opt.style.display = 'none';
            }
        });

        if (hasOptions) {
            supirDropdown.classList.remove('hidden');
        } else {
            supirDropdown.classList.add('hidden');
        }
    }

    supirOptions.forEach(opt => {
        opt.addEventListener('click', function() {
            const val = this.getAttribute('data-value');
            const plat = this.getAttribute('data-plat');

            supirSearch.value = val;
            supirHidden.value = val;
            
            if (plat && plat !== 'undefined' && plat !== 'null') {
                inputPlat.value = plat;
            }

            supirDropdown.classList.add('hidden');
        });
    });

    document.addEventListener('click', function(e) {
        if (!supirSearch?.contains(e.target) && !supirDropdown?.contains(e.target)) {
            supirDropdown?.classList.add('hidden');
        }
    });

    // Handle checkboxes exclusivity
    const cbLembur = document.getElementById('lembur');
    const cbNginap = document.getElementById('nginap');
    const cbTidak = document.getElementById('tidak_lembur_nginap');

    if (cbLembur && cbNginap && cbTidak) {
        cbTidak.addEventListener('change', function() {
            if (this.checked) {
                cbLembur.checked = false;
                cbNginap.checked = false;
            }
        });

        cbLembur.addEventListener('change', function() {
            if (this.checked) {
                cbTidak.checked = false;
            }
        });

        cbNginap.addEventListener('change', function() {
            if (this.checked) {
                cbTidak.checked = false;
            }
        });
    }
</script>
@endpush
