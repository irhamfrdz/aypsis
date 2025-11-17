@extends('layouts.app')

@section('title', 'Tambah Mobil')
@section('page_title', 'Tambah Mobil')

@section('content')
<h2 class="text-xl font-bold text-gray-800 mb-4">Formulir Mobil Baru</h2>

@if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md mb-4">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="bg-white shadow-md rounded-lg p-6">
    <form action="{{ route('master.mobil.store') }}" method="POST">
        @csrf

        @php
            // Definisikan kelas Tailwind untuk input yang lebih besar dan jelas, sama seperti form permohonan
            $inputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5";
        @endphp

        <fieldset class="mb-6">
            <legend class="text-lg font-semibold text-gray-800 mb-4">Informasi Dasar</legend>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Kode No -->
                <div>
                    <label for="kode_no" class="block text-sm font-medium text-gray-700">
                        Kode No <span class="text-red-500">*</span>
                        <span class="text-xs text-blue-600 ml-2">(Auto Generated)</span>
                    </label>
                    <div class="flex gap-2">
                        <input type="text" 
                               name="kode_no" 
                               id="kode_no" 
                               value="{{ old('kode_no', $nextKodeNomor) }}" 
                               class="{{ $inputClasses }} bg-gray-200 cursor-not-allowed flex-1" 
                               readonly 
                               required 
                               maxlength="50" 
                               placeholder="Auto Generated">
                        <button type="button" 
                                id="btn_refresh_kode" 
                                class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded transition duration-150 ease-in-out whitespace-nowrap"
                                title="Refresh Kode No">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Cek
                        </button>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">
                        Format: AT1 + Bulan(2digit) + Tahun(2digit) + Running Number(5digit)
                        <br>
                        Contoh: {{ $nextKodeNomor }}
                    </p>
                    @error('kode_no')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nomor Polisi -->
                <div>
                    <label for="nomor_polisi" class="block text-sm font-medium text-gray-700">Nomor Polisi</label>
                    <input type="text" name="nomor_polisi" id="nomor_polisi" value="{{ old('nomor_polisi') }}" class="{{ $inputClasses }}" maxlength="20" placeholder="Contoh: B 1234 ABC">
                    @error('nomor_polisi')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Lokasi -->
                <div>
                    <label for="lokasi" class="block text-sm font-medium text-gray-700">Lokasi</label>
                    <select name="lokasi" id="lokasi" class="{{ $inputClasses }}">
                        <option value="">-- Pilih Lokasi --</option>
                        <option value="BTM" {{ old('lokasi') == 'BTM' ? 'selected' : '' }}>BTM</option>
                        <option value="JKT" {{ old('lokasi') == 'JKT' ? 'selected' : '' }}>JKT</option>
                        <option value="PNG" {{ old('lokasi') == 'PNG' ? 'selected' : '' }}>PNG</option>
                    </select>
                    @error('lokasi')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Merek -->
                <div>
                    <label for="merek" class="block text-sm font-medium text-gray-700">Merek</label>
                    <input type="text" name="merek" id="merek" value="{{ old('merek') }}" class="{{ $inputClasses }}" maxlength="50" placeholder="Contoh: Toyota, Isuzu">
                    @error('merek')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jenis -->
                <div>
                    <label for="jenis" class="block text-sm font-medium text-gray-700">Jenis</label>
                    <input type="text" name="jenis" id="jenis" value="{{ old('jenis') }}" class="{{ $inputClasses }}" maxlength="50" placeholder="Contoh: Truk, Pick Up, Mobil Box">
                    @error('jenis')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tahun Pembuatan -->
                <div>
                    <label for="tahun_pembuatan" class="block text-sm font-medium text-gray-700">Tahun Pembuatan</label>
                    <input type="number" name="tahun_pembuatan" id="tahun_pembuatan" value="{{ old('tahun_pembuatan') }}" class="{{ $inputClasses }}" min="1990" max="{{ date('Y') + 1 }}" placeholder="Contoh: {{ date('Y') }}">
                    @error('tahun_pembuatan')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </fieldset>

        <fieldset class="mb-6">
            <legend class="text-lg font-semibold text-gray-800 mb-4">Informasi Teknis</legend>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- BPKB -->
                <div>
                    <label for="bpkb" class="block text-sm font-medium text-gray-700">Nomor BPKB</label>
                    <input type="text" name="bpkb" id="bpkb" value="{{ old('bpkb') }}" class="{{ $inputClasses }}" maxlength="50">
                    @error('bpkb')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- No Mesin -->
                <div>
                    <label for="no_mesin" class="block text-sm font-medium text-gray-700">Nomor Mesin</label>
                    <input type="text" name="no_mesin" id="no_mesin" value="{{ old('no_mesin') }}" class="{{ $inputClasses }}" maxlength="50">
                    @error('no_mesin')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nomor Rangka -->
                <div>
                    <label for="nomor_rangka" class="block text-sm font-medium text-gray-700">Nomor Rangka</label>
                    <input type="text" name="nomor_rangka" id="nomor_rangka" value="{{ old('nomor_rangka') }}" class="{{ $inputClasses }}" maxlength="50">
                    @error('nomor_rangka')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Atas Nama -->
                <div>
                    <label for="atas_nama" class="block text-sm font-medium text-gray-700">Atas Nama</label>
                    <input type="text" name="atas_nama" id="atas_nama" value="{{ old('atas_nama') }}" class="{{ $inputClasses }}" maxlength="100" placeholder="Nama pemilik kendaraan">
                    @error('atas_nama')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </fieldset>

        <fieldset class="mb-6">
            <legend class="text-lg font-semibold text-gray-800 mb-4">Informasi Pajak & Dokumen</legend>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Pajak STNK -->
                <div>
                    <label for="pajak_stnk" class="block text-sm font-medium text-gray-700">Pajak STNK</label>
                    <input type="date" name="pajak_stnk" id="pajak_stnk" value="{{ old('pajak_stnk') }}" class="{{ $inputClasses }}">
                    @error('pajak_stnk')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Pajak Plat -->
                <div>
                    <label for="pajak_plat" class="block text-sm font-medium text-gray-700">Pajak Plat</label>
                    <input type="date" name="pajak_plat" id="pajak_plat" value="{{ old('pajak_plat') }}" class="{{ $inputClasses }}">
                    @error('pajak_plat')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- No KIR -->
                <div>
                    <label for="no_kir" class="block text-sm font-medium text-gray-700">Nomor KIR</label>
                    <input type="text" name="no_kir" id="no_kir" value="{{ old('no_kir') }}" class="{{ $inputClasses }}" maxlength="50">
                    @error('no_kir')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Pajak KIR -->
                <div>
                    <label for="pajak_kir" class="block text-sm font-medium text-gray-700">Pajak KIR</label>
                    <input type="date" name="pajak_kir" id="pajak_kir" value="{{ old('pajak_kir') }}" class="{{ $inputClasses }}">
                    @error('pajak_kir')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </fieldset>

        <fieldset class="mb-6">
            <legend class="text-lg font-semibold text-gray-800 mb-4">Informasi Asuransi & Lainnya</legend>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Pemakai -->
                <div>
                    <label for="pemakai" class="block text-sm font-medium text-gray-700">Pemakai</label>
                    <input type="text" name="pemakai" id="pemakai" value="{{ old('pemakai') }}" class="{{ $inputClasses }}" maxlength="100" placeholder="Nama pemakai kendaraan">
                    @error('pemakai')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Asuransi -->
                <div>
                    <label for="asuransi" class="block text-sm font-medium text-gray-700">Asuransi</label>
                    <input type="text" name="asuransi" id="asuransi" value="{{ old('asuransi') }}" class="{{ $inputClasses }}" maxlength="100" placeholder="Nama perusahaan asuransi">
                    @error('asuransi')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jatuh Tempo Asuransi -->
                <div>
                    <label for="tanggal_jatuh_tempo_asuransi" class="block text-sm font-medium text-gray-700">Jatuh Tempo Asuransi</label>
                    <input type="date" name="tanggal_jatuh_tempo_asuransi" id="tanggal_jatuh_tempo_asuransi" value="{{ old('tanggal_jatuh_tempo_asuransi') }}" class="{{ $inputClasses }}">
                    @error('tanggal_jatuh_tempo_asuransi')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Warna Plat -->
                <div>
                    <label for="warna_plat" class="block text-sm font-medium text-gray-700">Warna Plat</label>
                    <select name="warna_plat" id="warna_plat" class="{{ $inputClasses }}">
                        <option value="">-- Pilih Warna Plat --</option>
                        <option value="Hitam" {{ old('warna_plat') == 'Hitam' ? 'selected' : '' }}>Hitam</option>
                        <option value="Kuning" {{ old('warna_plat') == 'Kuning' ? 'selected' : '' }}>Kuning</option>
                        <option value="Merah" {{ old('warna_plat') == 'Merah' ? 'selected' : '' }}>Merah</option>
                        <option value="Putih" {{ old('warna_plat') == 'Putih' ? 'selected' : '' }}>Putih</option>
                    </select>
                    @error('warna_plat')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Catatan -->
                <div class="md:col-span-2">
                    <label for="catatan" class="block text-sm font-medium text-gray-700">Catatan</label>
                    <textarea name="catatan" id="catatan" rows="4" class="{{ str_replace('text-base p-2.5', 'text-base p-3', $inputClasses) }}" placeholder="Catatan tambahan mengenai kendaraan...">{{ old('catatan') }}</textarea>
                    @error('catatan')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </fieldset>

        <fieldset class="mb-6">
            <legend class="text-lg font-semibold text-gray-800 mb-4">Penugasan Karyawan</legend>
            <div class="grid grid-cols-1 gap-4">
                <!-- Karyawan -->
                <div>
                    <label for="karyawan_id" class="block text-sm font-medium text-gray-700">Karyawan Supir</label>
                    <select name="karyawan_id" id="karyawan_id" class="{{ $inputClasses }}">
                        <option value="">-- Pilih Supir --</option>
                        @foreach($karyawans as $karyawan)
                            <option value="{{ $karyawan->id }}" {{ old('karyawan_id') == $karyawan->id ? 'selected' : '' }}>
                                {{ $karyawan->nama_panggilan ?: $karyawan->nama_lengkap }} @if($karyawan->nik)({{ $karyawan->nik }}) - {{ $karyawan->divisi }}@endif
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-blue-600">
                        <svg class="inline h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Menampilkan semua karyawan. Nomor polisi akan otomatis diupdate ke data karyawan yang dipilih.
                    </p>
                    @error('karyawan_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </fieldset>

        <div class="flex justify-end space-x-4">
            <a href="{{ route('master.mobil.index') }}" class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Batal
            </a>
            <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Simpan
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Handle refresh kode no button
    $('#btn_refresh_kode').on('click', function() {
        const btn = $(this);
        const originalText = btn.html();
        
        // Show loading state
        btn.prop('disabled', true);
        btn.html('<svg class="animate-spin h-3 w-3 mr-1 inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Checking...');
        
        $.ajax({
            url: '{{ route("master.mobil.get-next-kode") }}',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    $('#kode_no').val(response.kode_no);
                    
                    // Show success notification
                    const notification = $('<div class="fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow-lg z-50">' +
                        '<div class="flex items-center">' +
                        '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>' +
                        '</svg>' +
                        '<span>Kode No berhasil diperbarui: <strong>' + response.kode_no + '</strong></span>' +
                        '</div>' +
                        '</div>');
                    
                    $('body').append(notification);
                    setTimeout(function() {
                        notification.fadeOut(function() {
                            $(this).remove();
                        });
                    }, 3000);
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('Terjadi kesalahan saat mengambil kode nomor terbaru');
                console.error(xhr);
            },
            complete: function() {
                // Restore button state
                btn.prop('disabled', false);
                btn.html(originalText);
            }
        });
    });
});
</script>
@endpush
@endsection
