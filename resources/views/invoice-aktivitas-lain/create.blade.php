@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Buat Invoice Aktivitas Lain</h1>
                <p class="text-gray-600 mt-1">Tambah invoice baru untuk aktivitas lain</p>
            </div>
            <a href="{{ route('invoice-aktivitas-lain.index') }}" 
               class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('invoice-aktivitas-lain.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Informasi Umum -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Informasi Umum</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nomor Invoice -->
                <div>
                    <label for="nomor_invoice" class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor Invoice <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="nomor_invoice" 
                           id="nomor_invoice" 
                           value="{{ old('nomor_invoice') }}"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('nomor_invoice') border-red-500 @enderror"
                           placeholder="Masukkan nomor invoice"
                           required>
                    @error('nomor_invoice')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tanggal Invoice -->
                <div>
                    <label for="tanggal_invoice" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Invoice <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           name="tanggal_invoice" 
                           id="tanggal_invoice" 
                           value="{{ old('tanggal_invoice', date('Y-m-d')) }}"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('tanggal_invoice') border-red-500 @enderror"
                           required>
                    @error('tanggal_invoice')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jenis Aktivitas -->
                <div>
                    <label for="jenis_aktivitas" class="block text-sm font-medium text-gray-700 mb-2">
                        Jenis Aktivitas <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="jenis_aktivitas" 
                           id="jenis_aktivitas" 
                           value="{{ old('jenis_aktivitas') }}"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('jenis_aktivitas') border-red-500 @enderror"
                           placeholder="Masukkan jenis aktivitas"
                           required>
                    @error('jenis_aktivitas')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Penerima -->
                <div>
                    <label for="penerima" class="block text-sm font-medium text-gray-700 mb-2">
                        Penerima <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="penerima" 
                           id="penerima" 
                           value="{{ old('penerima') }}"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('penerima') border-red-500 @enderror"
                           placeholder="Masukkan nama penerima"
                           required>
                    @error('penerima')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Total -->
                <div>
                    <label for="total" class="block text-sm font-medium text-gray-700 mb-2">
                        Total <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                        <input type="text" 
                               name="total" 
                               id="total" 
                               value="{{ old('total') }}"
                               class="w-full pl-10 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('total') border-red-500 @enderror"
                               placeholder="0"
                               required>
                    </div>
                    @error('total')
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
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('status') border-red-500 @enderror"
                            required>
                        <option value="">Pilih Status</option>
                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="submitted" {{ old('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                        <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="paid" {{ old('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Deskripsi -->
            <div class="mt-6">
                <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">
                    Deskripsi
                </label>
                <textarea name="deskripsi" 
                          id="deskripsi" 
                          rows="4"
                          class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('deskripsi') border-red-500 @enderror"
                          placeholder="Masukkan deskripsi invoice (opsional)">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Catatan -->
            <div class="mt-6">
                <label for="catatan" class="block text-sm font-medium text-gray-700 mb-2">
                    Catatan
                </label>
                <textarea name="catatan" 
                          id="catatan" 
                          rows="3"
                          class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('catatan') border-red-500 @enderror"
                          placeholder="Masukkan catatan tambahan (opsional)">{{ old('catatan') }}</textarea>
                @error('catatan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end gap-3 bg-white rounded-lg shadow p-6">
            <a href="{{ route('invoice-aktivitas-lain.index') }}" 
               class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium rounded-lg transition">
                Batal
            </a>
            <button type="submit" 
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Simpan Invoice
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Format currency input
    const totalInput = document.getElementById('total');
    
    if (totalInput) {
        totalInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/[^0-9]/g, '');
            if (value) {
                // Format with thousand separators
                value = parseInt(value).toLocaleString('id-ID');
            }
            e.target.value = value;
        });

        // Before form submit, convert formatted number back to plain number
        totalInput.closest('form').addEventListener('submit', function(e) {
            const plainValue = totalInput.value.replace(/\./g, '');
            totalInput.value = plainValue;
        });
    }

    // Set default status to 'draft' if not selected
    const statusSelect = document.getElementById('status');
    if (statusSelect && !statusSelect.value) {
        statusSelect.value = 'draft';
    }
});
</script>
@endsection
