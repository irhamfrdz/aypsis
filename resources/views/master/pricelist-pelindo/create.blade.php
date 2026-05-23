@extends('layouts.app')

@section('title', 'Tambah Pricelist Pelindo')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="mb-6 border-b border-gray-200 pb-4 flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Tambah Pricelist Pelindo</h1>
                    <p class="text-gray-600 mt-1">Buat data pricelist Pelindo baru</p>
                </div>
                <a href="{{ route('master.pricelist-pelindo.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 transition">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar
                </a>
            </div>

            <form action="{{ route('master.pricelist-pelindo.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="kegiatan" class="block text-sm font-semibold text-gray-700">Kegiatan <span class="text-red-500">*</span></label>
                        <input type="text" name="kegiatan" id="kegiatan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm" placeholder="Masukkan jenis kegiatan Pelindo..." required value="{{ old('kegiatan') }}">
                        @error('kegiatan')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="ukuran" class="block text-sm font-semibold text-gray-700">Ukuran</label>
                            <input type="text" name="ukuran" id="ukuran" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm" placeholder="Contoh: 20 Feet, 40 Feet..." value="{{ old('ukuran') }}">
                            @error('ukuran')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="status_kontainer" class="block text-sm font-semibold text-gray-700">Status Kontainer</label>
                            <select name="status_kontainer" id="status_kontainer" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                                <option value="">Pilih Status...</option>
                                <option value="empty" {{ old('status_kontainer') == 'empty' ? 'selected' : '' }}>Empty</option>
                                <option value="full" {{ old('status_kontainer') == 'full' ? 'selected' : '' }}>Full</option>
                            </select>
                            @error('status_kontainer')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="tarif" class="block text-sm font-semibold text-gray-700">Tarif <span class="text-red-500">*</span></label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">Rp</span>
                                </div>
                                <input type="text" name="tarif" id="tarif" class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm" placeholder="0" required value="{{ old('tarif') }}">
                            </div>
                            @error('tarif')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="keterangan" class="block text-sm font-semibold text-gray-700">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm" placeholder="Masukkan keterangan tambahan jika ada...">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-semibold text-gray-700">Status <span class="text-red-500">*</span></label>
                        <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                            <option value="aktif" {{ old('status', 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-8 flex items-center justify-end space-x-3 border-t border-gray-200 pt-6">
                    <a href="{{ route('master.pricelist-pelindo.index') }}" class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition duration-150">Batal</a>
                    <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-purple-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition duration-150">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tarifInput = document.getElementById('tarif');
        
        function formatInput(input) {
            let rawValue = input.value.replace(/[^0-9]/g, '');
            const numericValue = parseFloat(rawValue) || 0;
            
            if (rawValue) {
                input.value = new Intl.NumberFormat('id-ID').format(numericValue);
            } else {
                input.value = '';
            }
        }

        // Format initial value if exists
        if (tarifInput.value) {
            formatInput(tarifInput);
        }

        tarifInput.addEventListener('input', function() {
            formatInput(this);
        });
    });
</script>
@endsection
