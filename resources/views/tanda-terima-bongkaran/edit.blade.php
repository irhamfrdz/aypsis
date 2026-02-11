@extends('layouts.app')

@section('title', 'Edit Tanda Terima Bongkaran')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Edit Tanda Terima Bongkaran</h1>
                        <p class="text-gray-600 mt-1">Edit tanda terima untuk surat jalan bongkaran</p>
                    </div>
                    <a href="{{ route('tanda-terima-bongkaran.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <form action="{{ route('tanda-terima-bongkaran.update', $tandaTerimaBongkaran->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <!-- Informasi Dasar -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Dasar</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Nomor Tanda Terima -->
                            <div>
                                <label for="nomor_tanda_terima" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nomor Tanda Terima <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="nomor_tanda_terima" 
                                       id="nomor_tanda_terima" 
                                       value="{{ old('nomor_tanda_terima', $tandaTerimaBongkaran->nomor_tanda_terima) }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 @error('nomor_tanda_terima') border-red-500 @enderror"
                                       required>
                                @error('nomor_tanda_terima')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tanggal Tanda Terima -->
                            <div>
                                <label for="tanggal_tanda_terima" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Tanda Terima <span class="text-red-500">*</span>
                                </label>
                                <input type="date" 
                                       name="tanggal_tanda_terima" 
                                       id="tanggal_tanda_terima" 
                                       value="{{ old('tanggal_tanda_terima', $tandaTerimaBongkaran->tanggal_tanda_terima ? $tandaTerimaBongkaran->tanggal_tanda_terima->format('Y-m-d') : '') }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 @error('tanggal_tanda_terima') border-red-500 @enderror"
                                       required>
                                @error('tanggal_tanda_terima')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Gudang -->
                            <div>
                                <label for="gudang_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Gudang <span class="text-red-500">*</span>
                                </label>
                                <select name="gudang_id" 
                                        id="gudang_id"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 @error('gudang_id') border-red-500 @enderror"
                                        required>
                                    <option value="">Pilih Gudang</option>
                                    @foreach($gudangs as $gudang)
                                        <option value="{{ $gudang->id }}" {{ old('gudang_id', $tandaTerimaBongkaran->gudang_id) == $gudang->id ? 'selected' : '' }}>
                                            {{ $gudang->nama_gudang }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('gudang_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Surat Jalan Bongkaran -->
                            <div>
                                <label for="surat_jalan_bongkaran_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Surat Jalan Bongkaran <span class="text-red-500">*</span>
                                </label>
                                <select name="surat_jalan_bongkaran_id" 
                                        id="surat_jalan_bongkaran_id"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 @error('surat_jalan_bongkaran_id') border-red-500 @enderror"
                                        required>
                                    <option value="">Pilih Surat Jalan Bongkaran</option>
                                    @foreach($suratJalans as $sj)
                                        <option value="{{ $sj->id }}" {{ old('surat_jalan_bongkaran_id', $tandaTerimaBongkaran->surat_jalan_bongkaran_id) == $sj->id ? 'selected' : '' }}>
                                            {{ $sj->nomor_surat_jalan }} - {{ $sj->no_kontainer ?? 'Tanpa Kontainer' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('surat_jalan_bongkaran_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Kontainer -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Kontainer</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- No Kontainer -->
                            <div>
                                <label for="no_kontainer" class="block text-sm font-medium text-gray-700 mb-2">
                                    No Kontainer
                                </label>
                                <input type="text" 
                                       name="no_kontainer" 
                                       id="no_kontainer" 
                                       value="{{ old('no_kontainer', $tandaTerimaBongkaran->no_kontainer) }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 @error('no_kontainer') border-red-500 @enderror">
                                @error('no_kontainer')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- No Seal -->
                            <div>
                                <label for="no_seal" class="block text-sm font-medium text-gray-700 mb-2">
                                    No Seal
                                </label>
                                <input type="text" 
                                       name="no_seal" 
                                       id="no_seal" 
                                       value="{{ old('no_seal', $tandaTerimaBongkaran->no_seal) }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 @error('no_seal') border-red-500 @enderror">
                                @error('no_seal')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Kegiatan -->
                            <div>
                                <label for="kegiatan" class="block text-sm font-medium text-gray-700 mb-2">
                                    Kegiatan <span class="text-red-500">*</span>
                                </label>
                                <select name="kegiatan" 
                                        id="kegiatan"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 @error('kegiatan') border-red-500 @enderror"
                                        required>
                                    <option value="">Pilih Kegiatan</option>
                                    <option value="bongkar" {{ old('kegiatan', $tandaTerimaBongkaran->kegiatan) == 'bongkar' ? 'selected' : '' }}>Bongkar</option>
                                    <option value="muat" {{ old('kegiatan', $tandaTerimaBongkaran->kegiatan) == 'muat' ? 'selected' : '' }}>Muat</option>
                                    <option value="stuffing" {{ old('kegiatan', $tandaTerimaBongkaran->kegiatan) == 'stuffing' ? 'selected' : '' }}>Stuffing</option>
                                    <option value="stripping" {{ old('kegiatan', $tandaTerimaBongkaran->kegiatan) == 'stripping' ? 'selected' : '' }}>Stripping</option>
                                </select>
                                @error('kegiatan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <select name="status" 
                                        id="status"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 @error('status') border-red-500 @enderror"
                                        required>
                                    <option value="pending" {{ old('status', $tandaTerimaBongkaran->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ old('status', $tandaTerimaBongkaran->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="completed" {{ old('status', $tandaTerimaBongkaran->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Checkbox Lembur & Nginap -->
                            <div class="col-span-1 md:col-span-2 flex flex-col md:flex-row md:space-x-6 mt-4 items-start md:items-center bg-yellow-50 p-3 rounded-lg border border-yellow-200">
                                <div class="flex items-center mb-2 md:mb-0">
                                    <input type="checkbox"
                                           name="lembur"
                                           id="lembur"
                                           value="1"
                                           class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                           {{ old('lembur', $tandaTerimaBongkaran->lembur) ? 'checked' : '' }}>
                                    <label for="lembur" class="ml-2 block text-sm font-medium text-gray-900 cursor-pointer select-none">
                                        Lembur
                                    </label>
                                </div>
                                <div class="flex items-center mb-2 md:mb-0">
                                    <input type="checkbox"
                                           name="nginap"
                                           id="nginap"
                                           value="1"
                                           class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                           {{ old('nginap', $tandaTerimaBongkaran->nginap) ? 'checked' : '' }}>
                                    <label for="nginap" class="ml-2 block text-sm font-medium text-gray-900 cursor-pointer select-none">
                                        Nginap
                                    </label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox"
                                           name="tidak_lembur_nginap"
                                           id="tidak_lembur_nginap"
                                           value="1"
                                           class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                           {{ old('tidak_lembur_nginap', $tandaTerimaBongkaran->tidak_lembur_nginap) ? 'checked' : '' }}>
                                    <label for="tidak_lembur_nginap" class="ml-2 block text-sm font-medium text-gray-900 cursor-pointer select-none">
                                        Tidak Lembur & Nginap
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Keterangan -->
                    <div>
                        <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                            Keterangan
                        </label>
                        <textarea name="keterangan" 
                                  id="keterangan" 
                                  rows="4"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 @error('keterangan') border-red-500 @enderror"
                                  placeholder="Masukkan keterangan tambahan...">{{ old('keterangan', $tandaTerimaBongkaran->keterangan) }}</textarea>
                        @error('keterangan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200">
                    <a href="{{ route('tanda-terima-bongkaran.index') }}" 
                       class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition duration-200">
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-teal-600 hover:bg-teal-700 text-white font-medium rounded-lg transition duration-200">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Checkbox Logic
    const cbLembur = document.getElementById('lembur');
    const cbNginap = document.getElementById('nginap');
    const cbTidak = document.getElementById('tidak_lembur_nginap');

    if(cbLembur && cbNginap && cbTidak) {
        cbTidak.addEventListener('change', function() {
            if(this.checked) {
                cbLembur.checked = false;
                cbNginap.checked = false;
            }
        });

        cbLembur.addEventListener('change', function() {
            if(this.checked) {
                cbTidak.checked = false;
            }
        });

        cbNginap.addEventListener('change', function() {
            if(this.checked) {
                cbTidak.checked = false;
            }
        });
    }
</script>
@endpush
