@extends('layouts.public')
@section('title', 'Customs Clearance & Batam FTZ | ALEXINDO YAKINPRIMA')

@section('content')

<!-- Dynamic Hero Section -->
<div class="relative bg-slate-950 pt-32 pb-24 lg:pt-48 lg:pb-32 overflow-hidden">
    <!-- Background Image -->
    <div class="absolute inset-0 z-0">
        <img src="{{ asset('images/customs-hero.jpg') }}" alt="Customs Command Center" class="w-full h-full object-cover opacity-40 mix-blend-luminosity">
        <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-900/95 to-transparent"></div>
    </div>
    
    <!-- Gold accent element -->
    <div class="absolute top-0 right-0 w-1/3 h-full bg-amber-500/5 transform skew-x-12 translate-x-32 hidden lg:block z-0 pointer-events-none"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumbs -->
        <nav class="flex text-sm text-slate-400 mb-8" aria-label="Breadcrumb" data-aos="fade-down">
            <ol class="inline-flex items-center space-x-2">
                <li><a href="{{ route('home') }}" class="hover:text-amber-400 transition-colors">Beranda</a></li>
                <li><i class="fa-solid fa-chevron-right text-[10px] opacity-60"></i></li>
                <li><span class="cursor-default">Layanan</span></li>
                <li><i class="fa-solid fa-chevron-right text-[10px] opacity-60"></i></li>
                <li class="text-white font-medium">Customs & FTZ</li>
            </ol>
        </nav>

        <div class="max-w-3xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20 text-sm font-semibold mb-6" data-aos="fade-right" data-aos-delay="100">
                <i class="fa-solid fa-stamp"></i> Kepabeanan Resmi & Terlisensi
            </div>
            
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-white mb-6 leading-tight tracking-tight" data-aos="fade-up" data-aos-delay="200">
                Customs Clearance & <br>
                <span class="text-amber-400">Batam FTZ (Kawasan Bebas)</span>
            </h1>
            
            <p class="text-lg md:text-xl text-slate-300 mb-8 leading-relaxed max-w-2xl" data-aos="fade-up" data-aos-delay="300">
                Layanan pengurusan dokumen pabean tanpa hambatan. Kami menjembatani regulasi pemerintah dan kebutuhan bisnis Anda, memaksimalkan fasilitas fiskal di zona perdagangan bebas (FTZ) Batam.
            </p>
            
            <div class="flex flex-wrap gap-4" data-aos="fade-up" data-aos-delay="400">
                <a href="#konsultasi" class="inline-flex items-center justify-center px-8 py-3.5 text-base font-bold text-slate-900 bg-amber-400 hover:bg-amber-300 rounded-full transition-all shadow-lg shadow-amber-500/30">
                    Konsultasi PPJK <i class="fa-solid fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- What is Customs & FTZ (Concept Section) -->
<div class="py-20 bg-white relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div data-aos="fade-right">
                <h2 class="text-3xl font-bold text-slate-900 mb-6 tracking-tight">Apa itu Customs Clearance & Batam FTZ?</h2>
                <div class="w-20 h-1.5 bg-amber-500 mb-8"></div>
                
                <p class="text-slate-600 text-lg mb-6 leading-relaxed">
                    <strong>Customs Clearance</strong> adalah proses krusial penyelesaian administrasi kepabeanan—termasuk penentuan kode HS, perhitungan bea masuk, dan pajak—agar kargo Anda dapat masuk atau keluar wilayah pabean secara legal.
                </p>
                <p class="text-slate-600 text-lg mb-8 leading-relaxed">
                    Sebagai nilai tambah strategis, kami beroperasi di <strong>Kawasan Bebas (FTZ) Batam</strong>. Status FTZ memberikan hak istimewa berupa pembebasan Bea Masuk, PPN, dan PPnBM untuk barang industri dan komersial yang masuk ke Batam dari luar negeri, menurunkan biaya operasional perusahaan Anda secara signifikan.
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center text-amber-600 flex-shrink-0 border border-slate-100 shadow-sm">
                            <i class="fa-solid fa-file-invoice text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900 mb-1">PIB & PEB</h4>
                            <p class="text-sm text-slate-500">Pemberitahuan Impor & Ekspor Barang reguler.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center text-amber-600 flex-shrink-0 border border-slate-100 shadow-sm">
                            <i class="fa-solid fa-map-location-dot text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900 mb-1">PPFTZ-01 s/d 03</h4>
                            <p class="text-sm text-slate-500">Dokumen pabean khusus Kawasan Bebas Batam.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="relative" data-aos="fade-left">
                <div class="absolute -inset-4 bg-slate-100 transform rotate-3 rounded-2xl z-0"></div>
                <img src="{{ asset('images/ftz-batam.jpg') }}" alt="Batam Free Trade Zone" class="relative z-10 w-full h-auto rounded-xl shadow-2xl border border-white/50">
                <!-- Floating badge -->
                <div class="absolute -bottom-6 -left-6 bg-white p-5 rounded-2xl shadow-xl z-20 border border-slate-100 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-check-double"></i>
                    </div>
                    <div>
                        <div class="text-sm text-slate-500 font-medium">Status Izin</div>
                        <div class="text-lg font-bold text-slate-900">PPJK Resmi</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Workflow / Alur Kepabeanan -->
<div class="py-24 bg-slate-50 border-y border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" data-aos="fade-up">
            <span class="text-amber-600 font-semibold tracking-wider uppercase text-sm mb-2 block">Standard Operating Procedure</span>
            <h2 class="text-3xl font-bold text-slate-900 mb-4 tracking-tight">Alur Customs Clearance (Sistem EDI)</h2>
            <p class="text-slate-500 max-w-2xl mx-auto">Kami memastikan setiap tahap terintegrasi dengan sistem Bea Cukai untuk penerbitan izin rilis yang cepat melalui Jalur Hijau (*Green Channel*).</p>
        </div>

        <div class="relative">
            <!-- Connecting Line -->
            <div class="hidden lg:block absolute top-1/2 left-0 w-full h-0.5 bg-slate-200 -translate-y-1/2 z-0"></div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 relative z-10">
                <!-- Step 1 -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 relative group hover:shadow-md transition-shadow" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-14 h-14 bg-slate-900 text-white rounded-full flex items-center justify-center text-xl font-bold mb-6 mx-auto relative shadow-lg group-hover:scale-110 transition-transform">
                        1
                        <div class="absolute -bottom-2 -right-2 w-6 h-6 bg-amber-500 text-white rounded-full flex items-center justify-center text-xs">
                            <i class="fa-solid fa-file-contract"></i>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-3 text-center">Pre-Clearance</h3>
                    <p class="text-sm text-slate-500 text-center">
                        Verifikasi kelengkapan dokumen awal (Invoice, Packing List, B/L, COO) dan penetapan kode HS (Harmonized System).
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 relative group hover:shadow-md transition-shadow" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-14 h-14 bg-slate-900 text-white rounded-full flex items-center justify-center text-xl font-bold mb-6 mx-auto relative shadow-lg group-hover:scale-110 transition-transform">
                        2
                        <div class="absolute -bottom-2 -right-2 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs">
                            <i class="fa-solid fa-network-wired"></i>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-3 text-center">Transfer EDI</h3>
                    <p class="text-sm text-slate-500 text-center">
                        Pembuatan draf Pemberitahuan Pabean (PIB/PEB/PPFTZ) dan transmisi data secara elektronik ke portal CEISA Bea Cukai.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 relative group hover:shadow-md transition-shadow" data-aos="fade-up" data-aos-delay="300">
                    <div class="w-14 h-14 bg-slate-900 text-white rounded-full flex items-center justify-center text-xl font-bold mb-6 mx-auto relative shadow-lg group-hover:scale-110 transition-transform">
                        3
                        <div class="absolute -bottom-2 -right-2 w-6 h-6 bg-rose-500 text-white rounded-full flex items-center justify-center text-xs">
                            <i class="fa-solid fa-money-bill-transfer"></i>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-3 text-center">Pembayaran & Penjaluran</h3>
                    <p class="text-sm text-slate-500 text-center">
                        Penerbitan kode billing pajak (jika ada), pembayaran bea masuk, dan penentuan jalur merah/kuning/hijau oleh sistem.
                    </p>
                </div>

                <!-- Step 4 -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-emerald-500 relative group hover:shadow-lg transition-shadow" data-aos="fade-up" data-aos-delay="400">
                    <div class="w-14 h-14 bg-emerald-500 text-white rounded-full flex items-center justify-center text-xl font-bold mb-6 mx-auto relative shadow-lg group-hover:scale-110 transition-transform">
                        4
                        <div class="absolute -bottom-2 -right-2 w-6 h-6 bg-white text-emerald-600 rounded-full flex items-center justify-center text-xs border border-emerald-200">
                            <i class="fa-solid fa-check"></i>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-emerald-700 mb-3 text-center">SPPB / Release</h3>
                    <p class="text-sm text-slate-600 text-center font-medium">
                        Terbitnya Surat Persetujuan Pengeluaran Barang. Kargo siap ditarik dari pelabuhan/bandara dan diantar ke tujuan.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Compliance & Security Feature Section -->
<div class="py-20 bg-emerald-900 text-white relative overflow-hidden">
    <!-- Local Texture Pattern Overlay -->
    <div class="absolute inset-0 z-0 pointer-events-none" style="background-image: url('{{ asset('images/background-tekstur03.png') }}'); background-size: cover; background-position: center;"></div>
    
    <!-- Dark Gradient Overlay for Readability -->
    <div class="absolute inset-0 bg-slate-900/70 z-0"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div data-aos="fade-right">
                <img src="{{ asset('images/customs-inspection.jpg') }}" alt="Customs Inspection Process" class="w-full h-auto rounded-2xl shadow-2xl border border-slate-700">
            </div>
            
            <div data-aos="fade-left">
                <h2 class="text-3xl font-bold mb-6 tracking-tight text-white drop-shadow-md">Kepatuhan & Integritas Data</h2>
                <p class="text-white/90 text-lg mb-8 leading-relaxed drop-shadow-sm font-medium">
                    Kesalahan kecil pada kode HS atau ketidaksesuaian dokumen dapat mengakibatkan penalti *Red Channel* (Jalur Merah), denda pabean, hingga penahanan kargo. Tim deklaran kami yang tersertifikasi menjamin tingkat akurasi 99.8%.
                </p>
                
                <ul class="space-y-6">
                    <li class="flex items-start gap-4 bg-slate-900/40 p-4 rounded-xl backdrop-blur-sm border border-white/10">
                        <i class="fa-solid fa-circle-check text-emerald-400 mt-1 text-xl"></i>
                        <div>
                            <h4 class="font-bold text-white text-lg">Konsultasi HS Code (Tarif Kepabeanan)</h4>
                            <p class="text-sm text-slate-300 mt-1">Penentuan klasifikasi barang yang tepat untuk menghindari salah lapor bea masuk.</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-4 bg-slate-900/40 p-4 rounded-xl backdrop-blur-sm border border-white/10">
                        <i class="fa-solid fa-circle-check text-emerald-400 mt-1 text-xl"></i>
                        <div>
                            <h4 class="font-bold text-white text-lg">Masterlist & IT Inventory</h4>
                            <p class="text-sm text-slate-300 mt-1">Dukungan manajemen fasilitas masterlist BKPM dan pencatatan IT Inventory untuk fasilitas Kawasan Berikat/Bebas.</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-4 bg-slate-900/40 p-4 rounded-xl backdrop-blur-sm border border-white/10">
                        <i class="fa-solid fa-circle-check text-emerald-400 mt-1 text-xl"></i>
                        <div>
                            <h4 class="font-bold text-white text-lg">Lartas (Larangan & Pembatasan)</h4>
                            <p class="text-sm text-slate-300 mt-1">Pengecekan perizinan instansi terkait (Karantina, BPOM, SNI, Postel) sebelum kargo dimuat dari negara asal.</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- CTA Section -->
<div class="py-24 relative overflow-hidden" id="konsultasi">
    <!-- Local Texture Pattern Overlay -->
    <div class="absolute inset-0 z-0" style="background-image: url('{{ asset('images/background-tekstur04.png') }}'); background-size: cover; background-position: center;"></div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10" data-aos="zoom-in-up">
        <h2 class="text-3xl md:text-5xl font-black text-slate-900 mb-6 drop-shadow-sm">Butuh Izin Cepat <span class="text-slate-800">Tanpa Denda?</span></h2>
        <p class="text-slate-800 font-medium text-lg md:text-xl mb-10 max-w-2xl mx-auto leading-relaxed">
            Jangan biarkan kargo Anda tertahan di pelabuhan. Konsultasikan dokumen impor/ekspor dan pemanfaatan fasilitas FTZ Anda dengan PPJK ahli kami hari ini.
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-5">
            <a href="#" class="w-full sm:w-auto px-8 py-4 bg-slate-900 text-white font-bold rounded-full hover:bg-slate-800 transition-all shadow-xl hover:shadow-2xl hover:-translate-y-1 flex items-center justify-center gap-2">
                <i class="fa-brands fa-whatsapp text-xl text-emerald-400"></i> Konsultasi via WhatsApp
            </a>
            <a href="#" class="w-full sm:w-auto px-8 py-4 bg-white/20 backdrop-blur-sm border-2 border-slate-900 text-slate-900 font-bold rounded-full hover:bg-slate-900 hover:text-white transition-all shadow-lg flex items-center justify-center gap-2">
                <i class="fa-solid fa-envelope"></i> Email Tim Customs
            </a>
        </div>
    </div>
</div>

@endsection
