@extends('layouts.app')

@section('title', 'Tambah Master Pricelist LOLO Batam')
@section('page_title', 'Tambah Master Pricelist LOLO Batam')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Tambah Master Pricelist LOLO Batam</h1>
            <p class="text-sm text-gray-500">Definisikan tarif baru untuk jasa LOLO kontainer di Batam</p>
        </div>
        <a href="{{ route('master.pricelist-lolo-batam.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold transition-colors flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    @if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg text-sm shadow-sm">
        <div class="flex">
            <i class="fas fa-exclamation-triangle text-red-500 mt-0.5 mr-3"></i>
            <div>
                <p class="font-bold text-red-800">Terjadi kesalahan input:</p>
                <ul class="list-disc pl-5 mt-1 text-red-700 text-xs space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8">
        <form action="{{ route('master.pricelist-lolo-batam.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Vendor / Depo / Pelabuhan</label>
                    <input type="text" name="vendor" value="{{ old('vendor') }}" list="vendor_list" placeholder="Contoh: Meratus, Temas, Pelindo Batam..." class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <datalist id="vendor_list">
                        @foreach($existingVendors as $v)
                            <option value="{{ $v }}">
                        @endforeach
                    </datalist>
                    <p class="text-xs text-gray-400 mt-1">Vendor penerbit biaya LOLO</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Nama Biaya / Keterangan Tarif <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_biaya" value="{{ old('nama_biaya') }}" placeholder="Contoh: Biaya LOLO 20ft Full Batam" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Ukuran Kontainer <span class="text-red-500">*</span></label>
                    <select name="size" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="20" {{ old('size') == '20' ? 'selected' : '' }}>20 Feet</option>
                        <option value="40" {{ old('size') == '40' ? 'selected' : '' }}>40 Feet</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Tipe Kontainer <span class="text-red-500">*</span></label>
                    <select name="tipe" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="FULL" {{ old('tipe') == 'FULL' ? 'selected' : '' }}>FULL</option>
                        <option value="EMPTY" {{ old('tipe') == 'EMPTY' ? 'selected' : '' }}>EMPTY</option>
                        <option value="ALL" {{ old('tipe') == 'ALL' ? 'selected' : '' }}>ALL (Semua Tipe)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Tarif LOLO (Rp) <span class="text-red-500">*</span></label>
                    <div class="relative rounded-lg shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500 text-sm font-semibold">
                            Rp
                        </div>
                        <input type="number" step="0.01" min="0" name="tarif" value="{{ old('tarif') }}" placeholder="0" required class="w-full pl-10 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm font-bold text-emerald-700">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Status <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="aktif" {{ old('status', 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="non-aktif" {{ old('status') == 'non-aktif' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Keterangan Tambahan</label>
                <textarea name="keterangan" rows="3" placeholder="Catatan syarat, ketentuan, atau lokasi depo..." class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('keterangan') }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('master.pricelist-lolo-batam.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow-sm transition-colors flex items-center">
                    <i class="fas fa-save mr-2"></i> Simpan Tarif
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
