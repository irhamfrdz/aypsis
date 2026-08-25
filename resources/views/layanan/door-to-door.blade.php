@extends('layouts.public')
@section('title', 'Door-to-Door Logistics | ALEXINDO YAKINPRIMA')

@section('content')
<!-- Hero Section (Corporate B2B Style with Integrated Text) -->
<div class="relative bg-slate-950 pt-32 pb-20 lg:pt-40 lg:pb-28">
    <div class="absolute inset-0">
        <!-- Professional dark overlay over the background image (Reduced opacity for clarity) -->
        <img src="{{ asset('images/door-to-door-bg.png') }}" alt="Door to Door Logistics" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-slate-950/50"></div>
    </div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-10">
        <!-- Breadcrumbs (Clean, minimal) -->
        <nav class="flex text-sm text-slate-400 mb-10" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-2">
                <li><a href="{{ route('home') }}" class="hover:text-blue-400 transition-colors">Beranda</a></li>
                <li><i class="fa-solid fa-chevron-right text-[10px] text-slate-600"></i></li>
                <li><span class="text-slate-500 cursor-default">Layanan</span></li>
                <li><i class="fa-solid fa-chevron-right text-[10px] text-slate-600"></i></li>
                <li class="text-white font-medium">Door-to-Door</li>
            </ol>
        </nav>

        <div class="max-w-4xl" data-aos="fade-up" data-aos-delay="100">
            <span class="inline-block px-3 py-1 bg-blue-900/50 border border-blue-500/30 text-blue-300 font-medium text-sm mb-6 tracking-widest uppercase">Layanan Terintegrasi Penuh</span>
            <h1 class="text-5xl md:text-6xl lg:text-7xl font-black text-white tracking-tight mb-8 leading-tight">
                DOOR-TO-DOOR<br><span class="text-slate-400">LOGISTICS</span>
            </h1>
            <p class="text-xl md:text-2xl text-slate-300 font-light mb-12 leading-relaxed max-w-3xl">
                Solusi rantai pasok <em>end-to-end</em> untuk efisiensi korporasi. Kami mengambil alih seluruh kompleksitas logistik, dari pabrik asal hingga gudang tujuan Anda.
            </p>
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="#hubungi" class="bg-blue-600 text-white hover:bg-blue-700 font-semibold py-4 px-8 border border-transparent transition-colors flex items-center justify-center gap-2">
                    MINTA KUOTASI ALL-IN <i class="fa-solid fa-arrow-right"></i>
                </a>
                <a href="#alur" class="bg-transparent text-white border border-slate-600 hover:bg-slate-800 font-semibold py-4 px-8 transition-colors flex items-center justify-center">
                    PELAJARI ALUR KERJA
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Definition Section (Sharp, high contrast) -->
<div class="py-24 bg-white border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-start">
            
            <div class="lg:col-span-5" data-aos="fade-right">
                <h2 class="text-3xl font-black text-slate-900 mb-6 uppercase tracking-tight">Satu Layanan.<br>Satu Tanggung Jawab.</h2>
                <div class="w-16 h-1 bg-blue-600 mb-8"></div>
                <p class="text-slate-600 leading-relaxed mb-6">
                    Layanan <strong>Door-to-Door (D2D)</strong> dirancang untuk perusahaan yang menuntut efisiensi operasional. Kami menjadi importir/eksportir <em>de facto</em> untuk kargo Anda, mengelola seluruh transisi antar moda transportasi dan yurisdiksi pabean.
                </p>
                <p class="text-slate-600 leading-relaxed mb-8">
                    Model layanan ini meminimalisir risiko penundaan <em>(delay)</em> dan pembengkakan biaya <em>(demurrage/storage)</em> yang sering terjadi akibat miskomunikasi antar vendor logistik yang berbeda.
                </p>
                <div class="bg-slate-50 border-l-4 border-blue-600 p-6">
                    <h4 class="font-bold text-slate-900 mb-2">Garansi Harga Transparan</h4>
                    <p class="text-sm text-slate-600">Seluruh biaya mulai dari <em>freight</em>, pajak, asuransi, hingga <em>trucking</em> lokal dikonsolidasikan dalam satu tagihan final yang disepakati di awal.</p>
                </div>
            </div>

            <div class="lg:col-span-7" data-aos="fade-left">
                <div class="grid grid-cols-2 gap-4">
                    <div class="aspect-square bg-slate-100 relative overflow-hidden group">
                        <img src="{{ asset('images/origin-warehouse.jpg') }}" alt="Warehousing" class="w-full h-full object-cover filter grayscale group-hover:grayscale-0 transition-all duration-700">
                        <div class="absolute bottom-4 left-4 bg-white/90 backdrop-blur px-3 py-1 text-xs font-bold uppercase tracking-wider text-slate-900">1. Origin</div>
                    </div>
                    <div class="aspect-square bg-slate-100 relative overflow-hidden group">
                        <img src="https://images.unsplash.com/photo-1494412519320-aa613dfb7738?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Port Operations" class="w-full h-full object-cover filter grayscale group-hover:grayscale-0 transition-all duration-700">
                        <div class="absolute bottom-4 left-4 bg-white/90 backdrop-blur px-3 py-1 text-xs font-bold uppercase tracking-wider text-slate-900">2. Freight & Customs</div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<!-- Why Choose Us (Grid Minimalist) -->
<div class="py-24 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-16" data-aos="fade-up">
            <h2 class="text-2xl font-black text-slate-900 uppercase tracking-tight mb-2">Nilai Tambah Korporasi</h2>
            <p class="text-slate-500">Keuntungan strategis memilih layanan Door-to-Door kami.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white p-8 border border-slate-200 shadow-sm hover:shadow-md transition-shadow" data-aos="fade-up" data-aos-delay="100">
                <div class="w-12 h-12 bg-slate-100 flex items-center justify-center text-blue-600 mb-6 text-xl">
                    <i class="fa-solid fa-user-shield"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Single Point of Contact</h3>
                <p class="text-slate-600 text-sm leading-relaxed">Satu manajer akun khusus (Key Account Manager) yang akan merespons kebutuhan Anda dan memberikan laporan proaktif mengenai status kargo.</p>
            </div>
            
            <div class="bg-white p-8 border border-slate-200 shadow-sm hover:shadow-md transition-shadow" data-aos="fade-up" data-aos-delay="200">
                <div class="w-12 h-12 bg-slate-100 flex items-center justify-center text-blue-600 mb-6 text-xl">
                    <i class="fa-solid fa-file-signature"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Legal & Customs Expertise</h3>
                <p class="text-slate-600 text-sm leading-relaxed">Dukungan tim ahli kepabeanan (PPJK) yang memastikan pengurusan PIB/PEB dan dokumen izin lartas mematuhi regulasi secara presisi.</p>
            </div>

            <div class="bg-white p-8 border border-slate-200 shadow-sm hover:shadow-md transition-shadow" data-aos="fade-up" data-aos-delay="300">
                <div class="w-12 h-12 bg-slate-100 flex items-center justify-center text-blue-600 mb-6 text-xl">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Efisiensi Man-Hours</h3>
                <p class="text-slate-600 text-sm leading-relaxed">Tim pengadaan dan logistik internal Anda dapat berfokus pada strategi bisnis alih-alih menghabiskan waktu menangani kendala operasional harian.</p>
            </div>
        </div>
    </div>
</div>

<!-- Linear Workflow Section (Straight, Professional) -->
<div class="py-24 bg-slate-900 text-slate-300" id="alur">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-20 text-center" data-aos="fade-up">
            <h2 class="text-3xl font-black text-white uppercase tracking-tight mb-4">Proses Kerja Terstruktur</h2>
            <p class="text-slate-400">Rantai operasional yang sistematis, dari hulu ke hilir.</p>
        </div>

        <div class="relative">
            <!-- Straight connecting line -->
            <div class="hidden lg:block absolute top-6 left-0 w-full h-px bg-slate-700"></div>
            
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
                <!-- Step 1 -->
                <div class="relative" data-aos="zoom-in" data-aos-delay="100">
                    <div class="w-16 h-16 bg-slate-800 border-2 border-slate-600 text-white flex items-center justify-center text-2xl mb-6 relative z-10 mx-auto lg:mx-0 transition-colors hover:border-blue-500 hover:text-blue-400">
                        <i class="fa-solid fa-truck-fast"></i>
                    </div>
                    <div class="text-center lg:text-left">
                        <h3 class="text-lg font-bold text-white mb-2 uppercase tracking-wide">Pick-up Origin</h3>
                        <p class="text-sm text-slate-400">Penjemputan kargo menggunakan armada darat dari pabrik atau gudang asal ke pelabuhan.</p>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="relative" data-aos="zoom-in" data-aos-delay="200">
                    <div class="w-16 h-16 bg-slate-800 border-2 border-slate-600 text-white flex items-center justify-center text-2xl mb-6 relative z-10 mx-auto lg:mx-0 transition-colors hover:border-blue-500 hover:text-blue-400">
                        <i class="fa-solid fa-clipboard-check"></i>
                    </div>
                    <div class="text-center lg:text-left">
                        <h3 class="text-lg font-bold text-white mb-2 uppercase tracking-wide">Export Customs</h3>
                        <p class="text-sm text-slate-400">Penyelesaian administrasi pabean ekspor di negara/kota asal secara legal.</p>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="relative" data-aos="zoom-in" data-aos-delay="300">
                    <div class="w-16 h-16 bg-blue-600 text-white flex items-center justify-center text-2xl mb-6 relative z-10 shadow-[0_0_15px_rgba(37,99,235,0.5)] mx-auto lg:mx-0 transition-transform hover:scale-110">
                        <i class="fa-solid fa-ship"></i>
                    </div>
                    <div class="text-center lg:text-left">
                        <h3 class="text-lg font-bold text-white mb-2 uppercase tracking-wide text-blue-400">Ocean Freight</h3>
                        <p class="text-sm text-slate-400">Pengiriman utama menggunakan kapal kargo menuju pelabuhan bongkar.</p>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="relative" data-aos="zoom-in" data-aos-delay="400">
                    <div class="w-16 h-16 bg-slate-800 border-2 border-slate-600 text-white flex items-center justify-center text-2xl mb-6 relative z-10 mx-auto lg:mx-0 transition-colors hover:border-blue-500 hover:text-blue-400">
                        <i class="fa-solid fa-file-shield"></i>
                    </div>
                    <div class="text-center lg:text-left">
                        <h3 class="text-lg font-bold text-white mb-2 uppercase tracking-wide">Import Customs</h3>
                        <p class="text-sm text-slate-400">Pengurusan perizinan impor, pembayaran pajak, dan penerbitan SPPB.</p>
                    </div>
                </div>

                <!-- Step 5 -->
                <div class="relative" data-aos="zoom-in" data-aos-delay="500">
                    <div class="w-16 h-16 bg-slate-800 border-2 border-slate-600 text-white flex items-center justify-center text-2xl mb-6 relative z-10 mx-auto lg:mx-0 transition-colors hover:border-blue-500 hover:text-blue-400">
                        <i class="fa-solid fa-dolly"></i>
                    </div>
                    <div class="text-center lg:text-left">
                        <h3 class="text-lg font-bold text-white mb-2 uppercase tracking-wide">Final Delivery</h3>
                        <p class="text-sm text-slate-400">Distribusi kargo dari pelabuhan bongkar langsung ke pintu gudang penerima.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Corporate CTA -->
<div class="py-24 bg-slate-950 border-t border-slate-800" id="hubungi">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center" data-aos="zoom-in-up">
        <h2 class="text-3xl md:text-4xl font-black text-white mb-6 uppercase tracking-tight">Kemitraan Logistik Jangka Panjang</h2>
        <p class="text-slate-400 text-lg mb-10">
            Mari diskusikan kebutuhan suplai jaringan perusahaan Anda bersama perwakilan sales kami. Dapatkan penawaran tarif yang terprediksi dan andal.
        </p>
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="mailto:corporate@alexindoyp.co.id" class="bg-blue-600 text-white font-bold py-4 px-8 hover:bg-blue-700 transition-colors flex items-center justify-center gap-3">
                <i class="fa-solid fa-envelope"></i> HUBUNGI SALES KORPORAT
            </a>
            <a href="https://wa.me/622112345678" target="_blank" class="bg-slate-800 border border-slate-600 text-white font-bold py-4 px-8 hover:bg-slate-700 transition-colors flex items-center justify-center gap-3">
                <i class="fa-brands fa-whatsapp"></i> HOTLINE WA
            </a>
        </div>
    </div>
</div>
@endsection
