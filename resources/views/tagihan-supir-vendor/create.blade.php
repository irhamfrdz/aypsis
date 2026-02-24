@extends('layouts.app')

@section('content')
<div class="space-y-4 max-w-2xl mx-auto">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Buat Tagihan Supir Vendor</h2>
            <p class="text-sm text-gray-500">Input nominal dan status pembayaran atau tambah keterangan</p>
        </div>
        <a href="{{ url()->previous() }}" class="flex items-center text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-lg px-4 py-2 hover:bg-gray-50 hover:text-gray-900 transition-colors shadow-sm">
            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali
        </a>
    </div>

    @if ($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">Terdapat error pada input Anda:</h3>
                <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <form action="{{ route('tagihan-supir-vendor.store') }}" method="POST">
            @csrf
            <input type="hidden" name="surat_jalan_id" value="{{ $suratJalan->id }}">
            
            <div class="p-6 space-y-6">
                
                <!-- Info Section (Readonly) -->
                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <h3 class="text-sm font-semibold text-gray-800 mb-3 pb-2 border-b border-gray-200 border-dashed">Detail Rekod Surat Jalan</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm mt-3">
                        <div>
                            <span class="text-gray-500 block mb-0.5">Nama Supir</span>
                            <span class="font-medium text-gray-900">{{ $suratJalan->supir }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block mb-0.5">No Surat Jalan</span>
                            <span class="font-medium text-gray-900">{{ $suratJalan->no_surat_jalan }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block mb-0.5">Rute</span>
                            <span class="font-medium text-gray-900">
                                {{ $suratJalan->tujuanPengambilanRelation->nama ?? ($suratJalan->order->tujuan_ambil ?? '-') }} 
                                <span class="mx-1 text-gray-400">→</span> 
                                {{ $suratJalan->tujuanPengirimanRelation->nama ?? ($suratJalan->order->tujuan_kirim ?? '-') }}
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-500 block mb-0.5">Kontainer</span>
                            <span class="font-medium text-gray-900">{{ Str::upper($suratJalan->size ?? '-') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Input Section -->
                <div class="space-y-5">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label for="nominal" class="block text-sm font-medium text-gray-700 mb-1.5">Nominal Tagihan<span class="text-red-500 ml-1">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 sm:text-sm font-medium pointer-events-none">Rp</span>
                                <input type="number" name="nominal" id="nominal" required
                                    class="w-full pl-10 pr-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition-shadow @error('nominal') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                                    value="{{ old('nominal', $nominal) }}">
                            </div>
                            @error('nominal')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="adjustment" class="block text-sm font-medium text-gray-700 mb-1.5">Adjustment (Potongan/Tambahan)</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 sm:text-sm font-medium pointer-events-none">Rp</span>
                                <input type="number" name="adjustment" id="adjustment"
                                    class="w-full pl-10 pr-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition-shadow @error('adjustment') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                                    value="{{ old('adjustment', 0) }}"
                                    placeholder="Gunakan minus (-) untuk potongan">
                            </div>
                            @error('adjustment')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Total Display -->
                    <div class="p-3 bg-blue-50 border border-blue-100 rounded-lg flex justify-between items-center px-4">
                        <span class="text-sm font-medium text-blue-800">Total Akhir:</span>
                        <span class="text-lg font-bold text-blue-900" id="total_display">Rp 0</span>
                    </div>

                    <div class="form-group">
                        <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1.5">Keterangan Tambahan</label>
                        <textarea name="keterangan" id="keterangan" rows="3" 
                            class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition-shadow @error('keterangan') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" 
                            placeholder="Catatan tambahan tagihan... (opsional)">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end space-x-3">
                <a href="{{ url()->previous() }}" class="px-5 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">Batal</a>
                <button type="submit" class="px-5 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors shadow-sm focus:bg-blue-700 flex items-center">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Buat Tagihan
                </button>
            </div>
            
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nominalInput = document.getElementById('nominal');
        const adjustmentInput = document.getElementById('adjustment'); // Corrected ID from ajaxInput to adjustmentInput
        const totalDisplay = document.getElementById('total_display');

        function updateTotal() {
            const nominal = parseFloat(nominalInput.value) || 0;
            const adjustment = parseFloat(adjustmentInput.value) || 0; // Corrected ID
            const total = nominal + adjustment;
            
            totalDisplay.textContent = 'Rp ' + total.toLocaleString('id-ID');
        }

        nominalInput.addEventListener('input', updateTotal);
        adjustmentInput.addEventListener('input', updateTotal); // Corrected ID
        
        // Initial calculation
        updateTotal();
    });
</script>
@endsection
