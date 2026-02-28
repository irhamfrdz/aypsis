@extends('layouts.app')

@section('title', 'Tambah Master Pricelist Freight')
@section('page_title', 'Tambah Master Pricelist Freight')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-5" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('master-pricelist-freight.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-purple-600">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                        Pricelist Freight
                    </a>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Tambah Baru</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="bg-white shadow-xl rounded-lg overflow-hidden">
            <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
                <h3 class="text-xl font-bold text-white">Tambah Pricelist Freight Baru</h3>
            </div>

            <form action="{{ route('master-pricelist-freight.store') }}" method="POST" class="p-6 space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Pelabuhan Asal -->
                    <div>
                        <label for="pelabuhan_asal_id" class="block text-sm font-medium text-gray-700 mb-1">Pelabuhan Asal <span class="text-red-500">*</span></label>
                        <select name="pelabuhan_asal_id" id="pelabuhan_asal_id" class="select2 w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm @error('pelabuhan_asal_id') border-red-500 @enderror" required>
                            <option value="">Pilih Pelabuhan Asal</option>
                            @foreach($pelabuhans as $p)
                                <option value="{{ $p->id }}" {{ old('pelabuhan_asal_id') == $p->id ? 'selected' : '' }}>{{ $p->nama_pelabuhan }}</option>
                            @endforeach
                        </select>
                        @error('pelabuhan_asal_id')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Pelabuhan Tujuan -->
                    <div>
                        <label for="pelabuhan_tujuan_id" class="block text-sm font-medium text-gray-700 mb-1">Pelabuhan Tujuan <span class="text-red-500">*</span></label>
                        <select name="pelabuhan_tujuan_id" id="pelabuhan_tujuan_id" class="select2 w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm @error('pelabuhan_tujuan_id') border-red-500 @enderror" required>
                            <option value="">Pilih Pelabuhan Tujuan</option>
                            @foreach($pelabuhans as $p)
                                <option value="{{ $p->id }}" {{ old('pelabuhan_tujuan_id') == $p->id ? 'selected' : '' }}>{{ $p->nama_pelabuhan }}</option>
                            @endforeach
                        </select>
                        @error('pelabuhan_tujuan_id')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Size Kontainer -->
                    <div>
                        <label for="size_kontainer" class="block text-sm font-medium text-gray-700 mb-1">Size Kontainer <span class="text-red-500">*</span></label>
                        <select name="size_kontainer" id="size_kontainer" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm @error('size_kontainer') border-red-500 @enderror" required>
                            <option value="">Pilih Size</option>
                            @foreach($sizeOptions as $value => $label)
                                <option value="{{ $value }}" {{ old('size_kontainer') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('size_kontainer')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Biaya -->
                    <div>
                        <label for="biaya" class="block text-sm font-medium text-gray-700 mb-1">Biaya <span class="text-red-500">*</span></label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">Rp</span>
                            </div>
                            <input type="number" name="biaya" id="biaya" step="0.01" value="{{ old('biaya') }}"
                                   class="focus:ring-purple-500 focus:border-purple-500 block w-full pl-10 pr-12 sm:text-sm border-gray-300 rounded-md @error('biaya') border-red-500 @enderror" 
                                   placeholder="0.00" required>
                        </div>
                        @error('biaya')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Keterangan -->
                <div>
                    <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                    <textarea name="keterangan" id="keterangan" rows="3" 
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                    <a href="{{ route('master-pricelist-freight.index') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%'
        });
    });
</script>
@endpush
