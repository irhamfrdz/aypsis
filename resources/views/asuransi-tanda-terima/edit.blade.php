@extends('layouts.app')

@section('title', 'Edit Asuransi Tanda Terima')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center mb-6">
                <a href="{{ route('asuransi-tanda-terima.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-800">Edit Asuransi Tanda Terima</h1>
            </div>

            <form action="{{ route('asuransi-tanda-terima.update', $asuransiTandaTerima->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Source Info (Read-only for integrity) -->
                    <div class="md:col-span-2 bg-blue-50 p-4 rounded-lg border border-blue-100">
                        <label class="block text-xs font-bold text-blue-700 uppercase mb-1">Terhubung ke (Kunci)</label>
                        <p class="text-gray-900 font-medium">{{ $asuransiTandaTerima->source_type_name }}: {{ $asuransiTandaTerima->source_number }}</p>
                    </div>

                    <!-- Vendor -->
                    <div class="md:col-span-2">
                        <label for="vendor_asuransi_id" class="block text-sm font-medium text-gray-700 mb-2">Vendor Asuransi <span class="text-red-500">*</span></label>
                        <select id="vendor_asuransi_id" name="vendor_asuransi_id" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent select2">
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}" {{ old('vendor_asuransi_id', $asuransiTandaTerima->vendor_asuransi_id) == $vendor->id ? 'selected' : '' }}>
                                    {{ $vendor->nama_asuransi }} ({{ $vendor->kode }})
                                </option>
                            @endforeach
                        </select>
                        @error('vendor_asuransi_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Polis Number -->
                    <div>
                        <label for="nomor_polis" class="block text-sm font-medium text-gray-700 mb-2">Nomor Polis <span class="text-red-500">*</span></label>
                        <input type="text" id="nomor_polis" name="nomor_polis" value="{{ old('nomor_polis', $asuransiTandaTerima->nomor_polis) }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('nomor_polis') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Polis Date -->
                    <div>
                        <label for="tanggal_polis" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Polis <span class="text-red-500">*</span></label>
                        <input type="date" id="tanggal_polis" name="tanggal_polis" value="{{ old('tanggal_polis', $asuransiTandaTerima->tanggal_polis ? $asuransiTandaTerima->tanggal_polis->format('Y-m-d') : '') }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('tanggal_polis') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Nilai Pertanggungan -->
                    <div>
                        <label for="nilai_pertanggungan" class="block text-sm font-medium text-gray-700 mb-2">Nilai Pertanggungan <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">Rp</span>
                            <input type="number" id="nilai_pertanggungan" name="nilai_pertanggungan" value="{{ old('nilai_pertanggungan', $asuransiTandaTerima->nilai_pertanggungan) }}" required
                                   class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        @error('nilai_pertanggungan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Premi -->
                    <div>
                        <label for="premi" class="block text-sm font-medium text-gray-700 mb-2">Premi <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">Rp</span>
                            <input type="number" id="premi" name="premi" value="{{ old('premi', $asuransiTandaTerima->premi) }}" required
                                   class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        @error('premi') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- File Upload -->
                    <div>
                        <label for="asuransi_file" class="block text-sm font-medium text-gray-700 mb-2">Update Dokumen Asuransi</label>
                        <input type="file" id="asuransi_file" name="asuransi_file"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @if($asuransiTandaTerima->asuransi_path)
                            <p class="mt-1 text-xs text-blue-600 italic flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A1 1 0 0111 2.293l4.707 4.707a1 1 0 01.293.707V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                </svg>
                                Dokumen saat ini sudah ada. Unggah baru untuk mengganti.
                            </p>
                        @endif
                        @error('asuransi_file') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                        <select id="status" name="status" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="Aktif" {{ old('status', $asuransiTandaTerima->status) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Selesai" {{ old('status', $asuransiTandaTerima->status) == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="Batal" {{ old('status', $asuransiTandaTerima->status) == 'Batal' ? 'selected' : '' }}>Batal</option>
                        </select>
                        @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Keterangan -->
                    <div class="md:col-span-2">
                        <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                        <textarea id="keterangan" name="keterangan" rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('keterangan', $asuransiTandaTerima->keterangan) }}</textarea>
                        @error('keterangan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-3">
                    <a href="{{ route('asuransi-tanda-terima.index') }}"
                       class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg font-medium transition duration-200">
                        Batal
                    </a>
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200">
                        Perbarui Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
