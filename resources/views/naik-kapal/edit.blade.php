@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-edit mr-3 text-purple-600 text-2xl"></i>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Edit Prospek</h1>
                    <p class="text-gray-600">Perbarui data kontainer prospek #{{ $naikKapal->id }}</p>
                </div>
            </div>
            <a href="{{ route('naik-kapal.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
    </div>

    {{-- Error Messages --}}
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <div class="font-bold mb-1">Terjadi kesalahan:</div>
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Edit Form --}}
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form action="{{ route('naik-kapal.update', $naikKapal->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Prospek Selection --}}
                <div class="md:col-span-2">
                    <label for="prospek_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih Prospek Link <span class="text-red-500">*</span>
                    </label>
                    <div id="prospek-select-container">
                        <select id="prospek_id" name="prospek_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500 select2" required>
                            <option value="">--Pilih Prospek--</option>
                            @foreach($prospeks as $prospek)
                                <option value="{{ $prospek->id }}" {{ old('prospek_id', $naikKapal->prospek_id) == $prospek->id ? 'selected' : '' }}>
                                    [ID:{{ $prospek->id }}] {{ $prospek->nama_supir }} - {{ $prospek->pt_pengirim }} ({{ $prospek->no_surat_jalan }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Nomor Kontainer --}}
                <div>
                    <label for="nomor_kontainer" class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor Kontainer <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="nomor_kontainer" name="nomor_kontainer" value="{{ old('nomor_kontainer', $naikKapal->nomor_kontainer) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500" required>
                </div>

                {{-- No Seal --}}
                <div>
                    <label for="no_seal" class="block text-sm font-medium text-gray-700 mb-2">Nomor Seal</label>
                    <input type="text" id="no_seal" name="no_seal" value="{{ old('no_seal', $naikKapal->no_seal) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500">
                </div>

                {{-- Tipe Kontainer --}}
                <div>
                    <label for="tipe_kontainer" class="block text-sm font-medium text-gray-700 mb-2">Tipe Kontainer</label>
                    <select id="tipe_kontainer" name="tipe_kontainer" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500">
                        <option value="">--Pilih--</option>
                        <option value="20 FT" {{ old('tipe_kontainer', $naikKapal->tipe_kontainer) == '20 FT' ? 'selected' : '' }}>20 FT</option>
                        <option value="40 FT" {{ old('tipe_kontainer', $naikKapal->tipe_kontainer) == '40 FT' ? 'selected' : '' }}>40 FT</option>
                        <option value="CURAH" {{ old('tipe_kontainer', $naikKapal->tipe_kontainer) == 'CURAH' ? 'selected' : '' }}>CURAH</option>
                    </select>
                </div>

                {{-- Ukuran Kontainer --}}
                <div>
                    <label for="ukuran_kontainer" class="block text-sm font-medium text-gray-700 mb-2">Ukuran Kontainer</label>
                    <input type="text" id="ukuran_kontainer" name="ukuran_kontainer" value="{{ old('ukuran_kontainer', $naikKapal->ukuran_kontainer) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500" placeholder="Contoh: 20x8x8.6">
                </div>

                {{-- Kapal Selection --}}
                <div>
                    <label for="nama_kapal" class="block text-sm font-medium text-gray-700 mb-2">
                        Kapal <span class="text-red-500">*</span>
                    </label>
                    <select id="nama_kapal" name="nama_kapal" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500" required>
                        <option value="">--Pilih Kapal--</option>
                        @foreach($kapals as $kapal)
                            <option value="{{ $kapal->nama_kapal }}" {{ old('nama_kapal', $naikKapal->nama_kapal) == $kapal->nama_kapal ? 'selected' : '' }}>
                                {{ $kapal->nama_kapal }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- No Voyage --}}
                <div>
                    <label for="no_voyage" class="block text-sm font-medium text-gray-700 mb-2">Nomor Voyage</label>
                    <input type="text" id="no_voyage" name="no_voyage" value="{{ old('no_voyage', $naikKapal->no_voyage) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500">
                </div>

                {{-- Pelabuhan Asal --}}
                <div>
                    <label for="pelabuhan_asal" class="block text-sm font-medium text-gray-700 mb-2">Pelabuhan Asal</label>
                    <input type="text" id="pelabuhan_asal" name="pelabuhan_asal" value="{{ old('pelabuhan_asal', $naikKapal->pelabuhan_asal) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500">
                </div>

                {{-- Pelabuhan Tujuan --}}
                <div>
                    <label for="pelabuhan_tujuan" class="block text-sm font-medium text-gray-700 mb-2">Pelabuhan Tujuan</label>
                    <input type="text" id="pelabuhan_tujuan" name="pelabuhan_tujuan" value="{{ old('pelabuhan_tujuan', $naikKapal->pelabuhan_tujuan) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500">
                </div>

                {{-- Tanggal Muat --}}
                <div>
                    <label for="tanggal_muat" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Muat <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="tanggal_muat" name="tanggal_muat" value="{{ old('tanggal_muat', $naikKapal->tanggal_muat ? $naikKapal->tanggal_muat->format('Y-m-d') : '') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500" required>
                </div>

                {{-- Jam Muat --}}
                <div>
                    <label for="jam_muat" class="block text-sm font-medium text-gray-700 mb-2">Jam Muat</label>
                    <input type="time" id="jam_muat" name="jam_muat" value="{{ old('jam_muat', $naikKapal->jam_muat ? \Carbon\Carbon::parse($naikKapal->jam_muat)->format('H:i') : '') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500">
                </div>

                {{-- Status --}}
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500" required>
                        <option value="menunggu" {{ old('status', $naikKapal->status) == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                        <option value="dimuat" {{ old('status', $naikKapal->status) == 'dimuat' ? 'selected' : '' }}>Dimuat</option>
                        <option value="selesai" {{ old('status', $naikKapal->status) == 'selesai' ? 'selected' : '' }}>Selesai (Sudah Muat)</option>
                        <option value="batal" {{ old('status', $naikKapal->status) == 'batal' ? 'selected' : '' }}>Batal</option>
                    </select>
                </div>

                {{-- Keterangan --}}
                <div class="md:col-span-2">
                    <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                    <textarea id="keterangan" name="keterangan" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500">{{ old('keterangan', $naikKapal->keterangan) }}</textarea>
                </div>
            </div>

            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('naik-kapal.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-md transition duration-200 border border-gray-200 font-medium">
                    Batal
                </a>
                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-8 py-2 rounded-md transition duration-200 shadow-sm font-medium">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        height: 42px;
        padding-top: 6px;
        border-color: #D1D5DB;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
    }
    .select2-container {
        width: 100% !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: '--Pilih Prospek--',
            allowClear: true
        });
    });
</script>
@endpush
