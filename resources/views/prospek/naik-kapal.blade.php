@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-ship mr-3 text-green-600 text-2xl"></i>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Naik Kapal</h1>
                    <p class="text-gray-600">Pilih tujuan keberangkatan untuk prospek ini</p>
                </div>
            </div>
            <a href="{{ route('prospek.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition duration-200 inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
    </div>

    {{-- Prospek Info --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Detail Prospek</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-500">Nama Supir</label>
                <p class="text-gray-800 font-semibold">{{ $prospek->nama_supir ?? '-' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">Barang</label>
                <p class="text-gray-800 font-semibold">{{ $prospek->barang ?? '-' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">PT/Pengirim</label>
                <p class="text-gray-800 font-semibold">{{ $prospek->pt_pengirim ?? '-' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">Tipe Kontainer</label>
                <p class="text-gray-800 font-semibold">{{ $prospek->tipe ?? '-' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">Ukuran</label>
                <p class="text-gray-800 font-semibold">{{ $prospek->ukuran ? $prospek->ukuran . ' Feet' : '-' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">Tujuan Pengiriman</label>
                <p class="text-gray-800 font-semibold">{{ $prospek->tujuan_pengiriman ?? '-' }}</p>
            </div>
        </div>
    </div>

    {{-- Form Pilih Tujuan --}}
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-6">Pilih Tujuan Keberangkatan</h3>
        
        <form action="{{ route('prospek.proses-naik-kapal', $prospek->id) }}" method="POST">
            @csrf

            {{-- Pilihan Tujuan --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                @foreach($tujuans as $tujuan)
                    <div class="tujuan-item border-2 border-gray-200 rounded-lg p-4 hover:border-green-300 transition duration-200 cursor-pointer" 
                         onclick="selectTujuan('{{ $tujuan->id }}')">
                        <label class="cursor-pointer">
                            <input type="radio" 
                                   name="tujuan" 
                                   value="{{ $tujuan->id }}" 
                                   class="hidden tujuan-radio"
                                   required>
                            <div class="text-center">
                                <div class="mb-3">
                                    @php
                                        $iconConfig = [
                                            'jakarta' => ['icon' => 'fa-building', 'color' => 'text-blue-600'],
                                            'batam' => ['icon' => 'fa-ship', 'color' => 'text-green-600'],
                                            'pinang' => ['icon' => 'fa-globe-asia', 'color' => 'text-orange-600'],
                                            'surabaya' => ['icon' => 'fa-anchor', 'color' => 'text-purple-600'],
                                            'medan' => ['icon' => 'fa-mountain', 'color' => 'text-red-600']
                                        ];
                                        $config = $iconConfig[$tujuan->id] ?? ['icon' => 'fa-map-marker-alt', 'color' => 'text-gray-600'];
                                    @endphp
                                    <i class="fas {{ $config['icon'] }} text-4xl {{ $config['color'] }}"></i>
                                </div>
                                <h4 class="text-lg font-semibold text-gray-800 mb-2">{{ $tujuan->nama }}</h4>
                                <p class="text-sm text-gray-600 mb-2">{{ $tujuan->kode }}</p>
                                <p class="text-xs text-gray-500">{{ $tujuan->deskripsi }}</p>
                            </div>
                        </label>
                    </div>
                @endforeach
            </div>

            {{-- Form Detail --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estimasi Tanggal Keberangkatan *</label>
                    <input type="date" 
                           name="estimasi_keberangkatan" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                           min="{{ date('Y-m-d') }}"
                           required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                    <textarea name="keterangan" 
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                              placeholder="Catatan tambahan (opsional)"></textarea>
                </div>
            </div>

            {{-- Submit Button --}}
            <div class="flex justify-end">
                <button type="submit" 
                        id="submitBtn"
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-md transition duration-200 inline-flex items-center disabled:opacity-50 disabled:cursor-not-allowed"
                        disabled>
                    <i class="fas fa-ship mr-2"></i>
                    Proses Naik Kapal
                </button>
            </div>
        </form>
    </div>
</div>

{{-- JavaScript untuk handling tujuan selection --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tujuanItems = document.querySelectorAll('.tujuan-item');
    const submitBtn = document.getElementById('submitBtn');
    const tujuanRadios = document.querySelectorAll('.tujuan-radio');
    
    // Handle tujuan selection
    window.selectTujuan = function(tujuanId) {
        // Reset all items
        tujuanItems.forEach(item => {
            item.classList.remove('border-green-500', 'bg-green-50');
            item.classList.add('border-gray-200');
        });
        
        // Reset all radio buttons
        tujuanRadios.forEach(radio => {
            radio.checked = false;
        });
        
        // Select clicked item
        const selectedItem = document.querySelector(`[onclick="selectTujuan('${tujuanId}')"]`);
        const selectedRadio = document.querySelector(`input[value="${tujuanId}"]`);
        
        if (selectedItem && selectedRadio) {
            selectedItem.classList.remove('border-gray-200');
            selectedItem.classList.add('border-green-500', 'bg-green-50');
            selectedRadio.checked = true;
            
            // Enable submit button
            submitBtn.disabled = false;
        }
    };
    
    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const selectedTujuan = document.querySelector('input[name="tujuan"]:checked');
        const estimasiKeberangkatan = document.querySelector('input[name="estimasi_keberangkatan"]');
        
        if (!selectedTujuan) {
            e.preventDefault();
            alert('Silakan pilih tujuan keberangkatan');
            return;
        }
        
        if (!estimasiKeberangkatan.value) {
            e.preventDefault();
            alert('Silakan isi estimasi tanggal keberangkatan');
            return;
        }
    });
});
</script>
@endsection