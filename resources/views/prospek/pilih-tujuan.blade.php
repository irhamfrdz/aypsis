@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-plus-circle mr-3 text-green-600 text-2xl"></i>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Buat Prospek - Pilih Tujuan</h1>
                    <p class="text-gray-600">Pilih tujuan untuk prospek baru</p>
                </div>
            </div>
            <a href="{{ route('prospek.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition duration-200 inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
    </div>

    {{-- Form Pilih Tujuan --}}
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-6">Pilih Tujuan Pengiriman</h3>
        
        <form action="{{ route('prospek.proses-naik-kapal-batch') }}" method="POST">
            @csrf
            
            {{-- Daftar Tujuan --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($tujuans as $tujuan)
                    @php
                        // Buat mapping yang lebih akurat untuk pencocokan tujuan
                        $tujuanKeywords = [
                            'jakarta' => ['jakarta', 'tanjung priok', 'jkt'],
                            'batam' => ['batam', 'sekupang', 'btm'],
                            'pinang' => ['pinang', 'penang', 'malaysia', 'png'],
                            'surabaya' => ['surabaya', 'tanjung perak', 'sby'],
                            'medan' => ['medan', 'belawan', 'mdn']
                        ];
                        
                        $keywords = $tujuanKeywords[$tujuan->id] ?? [$tujuan->nama];
                        
                        $prospekPerTujuan = $prospeksAktif->filter(function($prospek) use ($keywords) {
                            $tujuanPengiriman = strtolower($prospek->tujuan_pengiriman ?? '');
                            foreach ($keywords as $keyword) {
                                if (stripos($tujuanPengiriman, strtolower($keyword)) !== false) {
                                    return true;
                                }
                            }
                            return false;
                        });
                        $jumlahProspek = $prospekPerTujuan->count();
                        
                        $colorConfig = [
                            'jakarta' => ['bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'text' => 'text-blue-800', 'icon' => 'text-blue-600'],
                            'batam' => ['bg' => 'bg-green-50', 'border' => 'border-green-200', 'text' => 'text-green-800', 'icon' => 'text-green-600'],
                            'pinang' => ['bg' => 'bg-orange-50', 'border' => 'border-orange-200', 'text' => 'text-orange-800', 'icon' => 'text-orange-600'],
                            'surabaya' => ['bg' => 'bg-purple-50', 'border' => 'border-purple-200', 'text' => 'text-purple-800', 'icon' => 'text-purple-600'],
                            'medan' => ['bg' => 'bg-red-50', 'border' => 'border-red-200', 'text' => 'text-red-800', 'icon' => 'text-red-600']
                        ];
                        $config = $colorConfig[$tujuan->id] ?? ['bg' => 'bg-gray-50', 'border' => 'border-gray-200', 'text' => 'text-gray-800', 'icon' => 'text-gray-600'];
                    @endphp
                    
                    <div class="tujuan-item border-2 {{ $config['border'] }} {{ $config['bg'] }} rounded-lg p-6 hover:shadow-md transition duration-200 cursor-pointer"
                         onclick="selectTujuan('{{ $tujuan->id }}')">
                        <label class="flex flex-col cursor-pointer">
                            <input type="radio" 
                                   name="tujuan_id" 
                                   value="{{ $tujuan->id }}" 
                                   class="sr-only tujuan-radio"
                                   required>
                            
                            <div class="text-center">
                                <div class="mb-4">
                                    <i class="fas fa-map-marker-alt text-4xl {{ $config['icon'] }}"></i>
                                </div>
                                
                                <h4 class="text-xl font-bold {{ $config['text'] }} mb-4">
                                    {{ $tujuan->nama }}
                                </h4>
                                
                                <div class="{{ $config['bg'] }} border {{ $config['border'] }} rounded-lg p-3">
                                    <div class="text-2xl font-bold {{ $config['text'] }}">{{ $jumlahProspek }}</div>
                                    <div class="text-xs text-gray-600">Prospek Tersedia</div>
                                </div>
                                
                                @if($jumlahProspek > 0)
                                    @php
                                        // Hitung berdasarkan ukuran dan tipe kontainer
                                        $lcl20ft = $prospekPerTujuan->filter(function($p) {
                                            return strtoupper($p->tipe ?? '') === 'LCL' && 
                                                   (stripos($p->ukuran ?? '', '20') !== false || 
                                                    $p->ukuran == '20' ||
                                                    stripos($p->ukuran ?? '', "20'") !== false);
                                        })->count();
                                        
                                        $fcl20ft = $prospekPerTujuan->filter(function($p) {
                                            return strtoupper($p->tipe ?? '') === 'FCL' && 
                                                   (stripos($p->ukuran ?? '', '20') !== false || 
                                                    $p->ukuran == '20' ||
                                                    stripos($p->ukuran ?? '', "20'") !== false);
                                        })->count();
                                        
                                        $lcl40ft = $prospekPerTujuan->filter(function($p) {
                                            return strtoupper($p->tipe ?? '') === 'LCL' && 
                                                   (stripos($p->ukuran ?? '', '40') !== false || 
                                                    $p->ukuran == '40' ||
                                                    stripos($p->ukuran ?? '', "40'") !== false);
                                        })->count();
                                        
                                        $fcl40ft = $prospekPerTujuan->filter(function($p) {
                                            return strtoupper($p->tipe ?? '') === 'FCL' && 
                                                   (stripos($p->ukuran ?? '', '40') !== false || 
                                                    $p->ukuran == '40' ||
                                                    stripos($p->ukuran ?? '', "40'") !== false);
                                        })->count();
                                        
                                        $cargoCount = $prospekPerTujuan->filter(function($p) {
                                            return strtoupper($p->tipe ?? '') === 'CARGO';
                                        })->count();
                                    @endphp
                                    <div class="mt-3 text-xs text-gray-600 space-y-1">
                                        <div class="font-semibold {{ $config['text'] }} mb-1">Detail Kontainer:</div>
                                        <div class="flex justify-between">
                                            <span>20ft LCL:</span>
                                            <span class="font-semibold">{{ $lcl20ft }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>20ft FCL:</span>
                                            <span class="font-semibold">{{ $fcl20ft }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>40ft LCL:</span>
                                            <span class="font-semibold">{{ $lcl40ft }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>40ft FCL:</span>
                                            <span class="font-semibold">{{ $fcl40ft }}</span>
                                        </div>
                                        @if($cargoCount > 0)
                                        <div class="flex justify-between pt-1 border-t {{ $config['border'] }}">
                                            <span>CARGO:</span>
                                            <span class="font-semibold">{{ $cargoCount }}</span>
                                        </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </label>
                    </div>
                @endforeach
            </div>

            {{-- Submit Button --}}
            <div class="mt-8 flex justify-center">
                <button type="submit" 
                        id="submitBtn"
                        class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-md transition duration-200 inline-flex items-center disabled:opacity-50 disabled:cursor-not-allowed"
                        disabled>
                    <i class="fas fa-arrow-right mr-2"></i>
                    Lanjutkan Proses Buat Prospek
                </button>
            </div>
        </form>
    </div>
</div>

{{-- JavaScript untuk handling selection --}}
<script>
function selectTujuan(tujuanId) {
    // Reset semua tujuan
    document.querySelectorAll('.tujuan-item').forEach(item => {
        item.classList.remove('ring-2', 'ring-green-500', 'ring-offset-2');
        item.classList.add('border-2');
    });
    
    // Highlight tujuan yang dipilih
    event.currentTarget.classList.add('ring-2', 'ring-green-500', 'ring-offset-2');
    event.currentTarget.classList.remove('border-2');
    
    // Set radio button
    document.querySelector(`input[value="${tujuanId}"]`).checked = true;
    
    // Enable submit button
    document.getElementById('submitBtn').disabled = false;
}

// Handle radio change
document.querySelectorAll('.tujuan-radio').forEach(radio => {
    radio.addEventListener('change', function() {
        if (this.checked) {
            document.getElementById('submitBtn').disabled = false;
        }
    });
});
</script>
@endsection