@extends('layouts.app')

@section('page_title', 'Tambah Pricelist Biaya Trucking')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-3xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Tambah Pricelist</h1>
                <p class="text-gray-600 mt-1">Buat data pricelist biaya trucking baru</p>
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
            <form action="{{ route('master.pricelist-biaya-trucking.store') }}" method="POST" class="p-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Nama Vendor -->
                    <div class="col-span-2 md:col-span-1">
                        <label for="nama_vendor" class="block text-sm font-medium text-gray-700 mb-1">Nama Vendor <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_vendor" id="nama_vendor" value="{{ old('nama_vendor') }}" required
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                               placeholder="Contoh: PT. Maju Jaya">
                    </div>

                    <!-- Size -->
                    <div class="col-span-2 md:col-span-1">
                        <label for="size" class="block text-sm font-medium text-gray-700 mb-1">Size <span class="text-red-500">*</span></label>
                        <input type="text" name="size" id="size" value="{{ old('size') }}" required
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                               placeholder="Contoh: 20ft">
                    </div>

                    <!-- Biaya -->
                    <div class="col-span-2 md:col-span-1">
                        <label for="biaya" class="block text-sm font-medium text-gray-700 mb-1">Biaya (Rp) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">Rp</span>
                            <input type="number" name="biaya" id="biaya" value="{{ old('biaya') }}" required min="0" step="0.01"
                                   class="w-full pl-10 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                   placeholder="0">
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="col-span-2 md:col-span-1">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                        <select name="status" id="status" required
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                            <option value="aktif" {{ old('status', 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="non-aktif" {{ old('status') == 'non-aktif' ? 'selected' : '' }}>Non-Aktif</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                    <button type="reset" class="px-4 py-2 border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-gray-50 transition">
                        Reset
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition font-medium shadow-sm">
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
