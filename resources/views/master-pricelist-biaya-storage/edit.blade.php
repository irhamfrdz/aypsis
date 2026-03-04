@extends('layouts.app')

@section('title', 'Edit Tarif Storage')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-800">Edit Tarif Biaya Storage</h2>
        <a href="{{ route('master-pricelist-biaya-storage.index') }}" class="text-sm text-gray-600 hover:text-indigo-600">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md border overflow-hidden">
        <form action="{{ route('master-pricelist-biaya-storage.update', $pricelist->id) }}" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Vendor --}}
                <div class="md:col-span-2">
                    <label for="vendor" class="block text-sm font-medium text-gray-700 mb-1">Vendor / Terminal</label>
                    <input type="text" name="vendor" id="vendor" value="{{ old('vendor', $pricelist->vendor) }}" 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                           placeholder="Nama Terminal / Depo">
                </div>

                {{-- Lokasi --}}
                <div>
                    <label for="lokasi" class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                    <select name="lokasi" id="lokasi" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="Jakarta" {{ old('lokasi', $pricelist->lokasi) == 'Jakarta' ? 'selected' : '' }}>Jakarta</option>
                        <option value="Batam" {{ old('lokasi', $pricelist->lokasi) == 'Batam' ? 'selected' : '' }}>Batam</option>
                        <option value="Pinang" {{ old('lokasi', $pricelist->lokasi) == 'Pinang' ? 'selected' : '' }}>Pinang</option>
                    </select>
                </div>

                {{-- Ukuran --}}
                <div>
                    <label for="size_kontainer" class="block text-sm font-medium text-gray-700 mb-1">Ukuran Kontainer <span class="text-red-500">*</span></label>
                    <select name="size_kontainer" id="size_kontainer" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="20" {{ old('size_kontainer', $pricelist->size_kontainer) == '20' ? 'selected' : '' }}>20'</option>
                        <option value="40" {{ old('size_kontainer', $pricelist->size_kontainer) == '40' ? 'selected' : '' }}>40'</option>
                        <option value="45" {{ old('size_kontainer', $pricelist->size_kontainer) == '45' ? 'selected' : '' }}>45'</option>
                    </select>
                </div>

                {{-- Biaya per Hari --}}
                <div>
                    <label for="biaya_per_hari" class="block text-sm font-medium text-gray-700 mb-1">Tarif Per Hari (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="biaya_per_hari" id="biaya_per_hari" value="{{ old('biaya_per_hari', $pricelist->biaya_per_hari) }}" required
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>

                {{-- Free Time --}}
                <div>
                    <label for="free_time" class="block text-sm font-medium text-gray-700 mb-1">Free Time (Hari) <span class="text-red-500">*</span></label>
                    <input type="number" name="free_time" id="free_time" value="{{ old('free_time', $pricelist->free_time) }}" required
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>

                {{-- Status --}}
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="status" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="aktif" {{ old('status', $pricelist->status) == 'aktif' ? 'selected' : '' }}>AKTIF</option>
                        <option value="non-aktif" {{ old('status', $pricelist->status) == 'non-aktif' ? 'selected' : '' }}>NON-AKTIF</option>
                    </select>
                </div>

                {{-- Keterangan --}}
                <div class="md:col-span-2">
                    <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                    <textarea name="keterangan" id="keterangan" rows="3" 
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                              placeholder="Catatan tambahan...">{{ old('keterangan', $pricelist->keterangan) }}</textarea>
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="px-6 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Update Pricelist
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
