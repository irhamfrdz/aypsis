@extends('layouts.app')

@section('page_title', 'Edit Pricelist Biaya Trucking')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-3xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Edit Pricelist</h1>
                <p class="text-gray-600 mt-1">Perbarui data pricelist biaya trucking</p>
            </div>
            <a href="{{ route('master.pricelist-biaya-trucking.index') }}" 
               class="px-4 py-2 border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-gray-50 transition">
                Kembali
            </a>
        </div>

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <form action="{{ route('master.pricelist-biaya-trucking.update', $pricelistBiayaTrucking) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Rute -->
                    <div class="col-span-2 md:col-span-1">
                        <label for="rute" class="block text-sm font-medium text-gray-700 mb-1">Rute <span class="text-red-500">*</span></label>
                        <input type="text" name="rute" id="rute" value="{{ old('rute', $pricelistBiayaTrucking->rute) }}" required
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                               placeholder="Contoh: Jakarta - Surabaya">
                        <p class="text-xs text-gray-500 mt-1">Origin - Destination</p>
                    </div>

                    <!-- Tujuan -->
                    <div class="col-span-2 md:col-span-1">
                        <label for="tujuan" class="block text-sm font-medium text-gray-700 mb-1">Tujuan Spesifik</label>
                        <input type="text" name="tujuan" id="tujuan" value="{{ old('tujuan', $pricelistBiayaTrucking->tujuan) }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                               placeholder="Contoh: Gudang A, Pelabuhan B">
                    </div>

                    <!-- Jenis Kendaraan -->
                    <div class="col-span-2 md:col-span-1">
                        <label for="jenis_kendaraan" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kendaraan</label>
                        <input type="text" name="jenis_kendaraan" id="jenis_kendaraan" value="{{ old('jenis_kendaraan', $pricelistBiayaTrucking->jenis_kendaraan) }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                               placeholder="Contoh: Fuso, Tronton, Trailer 20'">
                    </div>

                    <!-- Biaya -->
                    <div class="col-span-2 md:col-span-1">
                        <label for="biaya" class="block text-sm font-medium text-gray-700 mb-1">Biaya (Rp) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">Rp</span>
                            <input type="number" name="biaya" id="biaya" value="{{ old('biaya', $pricelistBiayaTrucking->biaya) }}" required min="0" step="0.01"
                                   class="w-full pl-10 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                   placeholder="0">
                        </div>
                    </div>

                    <!-- Satuan -->
                    <div class="col-span-2 md:col-span-1">
                        <label for="satuan" class="block text-sm font-medium text-gray-700 mb-1">Satuan <span class="text-red-500">*</span></label>
                        <select name="satuan" id="satuan" required
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                            <option value="trip" {{ old('satuan', $pricelistBiayaTrucking->satuan) == 'trip' ? 'selected' : '' }}>Trip</option>
                            <option value="rit" {{ old('satuan', $pricelistBiayaTrucking->satuan) == 'rit' ? 'selected' : '' }}>Rit</option>
                            <option value="kg" {{ old('satuan', $pricelistBiayaTrucking->satuan) == 'kg' ? 'selected' : '' }}>Kg</option>
                            <option value="ton" {{ old('satuan', $pricelistBiayaTrucking->satuan) == 'ton' ? 'selected' : '' }}>Ton</option>
                            <option value="unit" {{ old('satuan', $pricelistBiayaTrucking->satuan) == 'unit' ? 'selected' : '' }}>Unit</option>
                            <option value="hari" {{ old('satuan', $pricelistBiayaTrucking->satuan) == 'hari' ? 'selected' : '' }}>Hari</option>
                            <option value="bulan" {{ old('satuan', $pricelistBiayaTrucking->satuan) == 'bulan' ? 'selected' : '' }}>Bulan</option>
                        </select>
                    </div>

                    <!-- Status -->
                    <div class="col-span-2 md:col-span-1">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                        <select name="status" id="status" required
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                            <option value="aktif" {{ old('status', $pricelistBiayaTrucking->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="non-aktif" {{ old('status', $pricelistBiayaTrucking->status) == 'non-aktif' ? 'selected' : '' }}>Non-Aktif</option>
                        </select>
                    </div>

                    <!-- Tanggal Berlaku -->
                    <div class="col-span-2 md:col-span-1">
                        <label for="tanggal_berlaku" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Berlaku</label>
                        <input type="date" name="tanggal_berlaku" id="tanggal_berlaku" value="{{ old('tanggal_berlaku', optional($pricelistBiayaTrucking->tanggal_berlaku)->format('Y-m-d')) }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                    </div>

                    <!-- Tanggal Berakhir -->
                    <div class="col-span-2 md:col-span-1">
                        <label for="tanggal_berakhir" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Berakhir</label>
                        <input type="date" name="tanggal_berakhir" id="tanggal_berakhir" value="{{ old('tanggal_berakhir', optional($pricelistBiayaTrucking->tanggal_berakhir)->format('Y-m-d')) }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        <p class="text-xs text-gray-500 mt-1">Kosongkan jika berlaku selamanya</p>
                    </div>

                    <!-- Keterangan -->
                    <div class="col-span-2">
                        <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" rows="3"
                                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">{{ old('keterangan', $pricelistBiayaTrucking->keterangan) }}</textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                    <button type="reset" class="px-4 py-2 border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-gray-50 transition">
                        Reset
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition font-medium shadow-sm">
                        Perbarui Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
