@extends('layouts.public')
@section('title', 'Project Cargo & Heavy Lift | ALEXINDO YAKINPRIMA')

@section('content')

<!-- Minimalist Hero (Image Focus) -->
<div class="relative w-full h-[60vh] min-h-[500px]">
    <div class="absolute inset-0">
        <img src="{{ asset('images/project-cargo-bg.jpg') }}" alt="Project Cargo & Heavy Lift" class="w-full h-full object-cover">
        <!-- Subtle dark gradient at the top for navbar visibility, and light gradient at bottom -->
        <div class="absolute inset-0 bg-gradient-to-b from-slate-900/60 via-slate-900/20 to-transparent"></div>
    </div>
    
    <!-- Breadcrumbs over the hero -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-32 lg:pt-40">
        <nav class="flex text-sm text-white/80" aria-label="Breadcrumb" data-aos="fade-down">
            <ol class="inline-flex items-center space-x-2">
                <li><a href="{{ route('home') }}" class="hover:text-white transition-colors">Beranda</a></li>
                <li><i class="fa-solid fa-chevron-right text-[10px] opacity-60"></i></li>
                <li><span class="cursor-default">Layanan</span></li>
                <li><i class="fa-solid fa-chevron-right text-[10px] opacity-60"></i></li>
                <li class="text-white font-bold">Project Cargo</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Overlapping Main Content Card (Sleek Corporate) -->
<div class="relative z-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-32 lg:-mt-48 mb-24" data-aos="fade-up">
    <div class="bg-white rounded-3xl shadow-[0_20px_50px_-12px_rgba(0,0,0,0.1)] p-8 md:p-12 lg:p-16 border border-slate-100 overflow-hidden relative">
        <!-- Decorative subtle background pattern -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-blue-50 rounded-full blur-3xl opacity-50 transform translate-x-1/2 -translate-y-1/2"></div>
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center relative z-10">
            <div class="lg:col-span-8">
                <span class="inline-block py-1 px-3 rounded-full bg-blue-50 text-blue-700 font-bold tracking-wider uppercase text-xs mb-6 border border-blue-100">
                    Layanan Spesialis
                </span>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-slate-900 mb-6 tracking-tight leading-tight">
                    Project Cargo <br>& Heavy Lift
                </h1>
                <p class="text-xl text-slate-600 leading-relaxed mb-8 max-w-2xl font-light">
                    Menembus batas dimensi standar. Kami merekayasa solusi logistik terpadu untuk muatan berskala masif, <em>Over-Dimensional</em>, dan peralatan industri bernilai tinggi.
                </p>
                <div class="flex items-center gap-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-blue-600">
                            <i class="fa-solid fa-ruler-combined"></i>
                        </div>
                        <span class="font-semibold text-slate-700 text-sm">Out of Gauge (OOG)</span>
                    </div>
                    <div class="w-px h-8 bg-slate-200"></div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-blue-600">
                            <i class="fa-solid fa-weight-scale"></i>
                        </div>
                        <span class="font-semibold text-slate-700 text-sm">Extreme Weight</span>
                    </div>
                </div>
            </div>
            
            <div class="lg:col-span-4">
                <div class="bg-slate-50 p-8 rounded-2xl border border-slate-200 shadow-sm">
                    <div class="w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center text-blue-600 mb-4 text-xl">
                        <i class="fa-solid fa-helmet-safety"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Konsultasi Rencana Proyek</h3>
                    <p class="text-slate-600 mb-6 text-sm leading-relaxed">
                        Setiap kargo raksasa membutuhkan kalkulasi khusus. Diskusikan spesifikasi muatan Anda dengan tim <em>engineering</em> kami.
                    </p>
                    <a href="mailto:corporate@alexindoyp.co.id" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3.5 px-6 rounded-xl transition-all shadow-[0_8px_20px_-6px_rgba(37,99,235,0.4)] hover:shadow-[0_12px_25px_-6px_rgba(37,99,235,0.5)] transform hover:-translate-y-0.5">
                        Minta Analisis Teknis
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- What We Handle (Clean 3 Column Grid) -->
<div class="py-12 bg-white border-b border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
            <h2 class="text-3xl font-bold text-slate-900 mb-4 tracking-tight">Kapasitas Penanganan Khusus</h2>
            <p class="text-slate-600 text-lg">Infrastruktur dan keahlian kami dirancang untuk menaklukkan tiga tantangan utama dalam logistik proyek.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Card 1 -->
            <div class="bg-white group" data-aos="fade-up" data-aos-delay="100">
                <div class="h-48 rounded-2xl overflow-hidden mb-6 relative">
                    <img src="{{ asset('images/construction-cargo.jpg') }}" alt="Konstruksi" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700">
                    <div class="absolute inset-0 bg-slate-900/10 group-hover:bg-transparent transition-colors duration-500"></div>
                </div>
                <h4 class="text-xl font-bold text-slate-900 mb-3">Infrastruktur & Konstruksi</h4>
                <p class="text-slate-600 text-sm leading-relaxed">Pengiriman material konstruksi raksasa, tiang pancang, gelagar jembatan (*girder*), dan komponen pembangkit listrik yang tidak muat dalam peti kemas tertutup.</p>
            </div>
            
            <!-- Card 2 -->
            <div class="bg-white group" data-aos="fade-up" data-aos-delay="200">
                <div class="h-48 rounded-2xl overflow-hidden mb-6 relative">
                    <img src="{{ asset('images/heavy-machinery.jpg') }}" alt="Alat Berat" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700">
                    <div class="absolute inset-0 bg-slate-900/10 group-hover:bg-transparent transition-colors duration-500"></div>
                </div>
                <h4 class="text-xl font-bold text-slate-900 mb-3">Heavy Machinery</h4>
                <p class="text-slate-600 text-sm leading-relaxed">Mobilisasi alat berat pertambangan (ekskavator, *dump truck* skala besar), mesin pabrik (boiler, transformer), dan fasilitas produksi minyak & gas.</p>
            </div>

            <!-- Card 3 -->
            <div class="bg-white group" data-aos="fade-up" data-aos-delay="300">
                <div class="h-48 rounded-2xl overflow-hidden mb-6 relative">
                    <img src="{{ asset('images/remote-site.jpg') }}" alt="Remote Site" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700">
                    <div class="absolute inset-0 bg-slate-900/10 group-hover:bg-transparent transition-colors duration-500"></div>
                </div>
                <h4 class="text-xl font-bold text-slate-900 mb-3">Remote Site Delivery</h4>
                <p class="text-slate-600 text-sm leading-relaxed">Distribusi megaproyek ke daerah pelosok atau lepas pantai (*offshore*) menggunakan kapal dangkal (LCT) dan jembatan/infrastruktur jalan sementara.</p>
            </div>
        </div>
    </div>
</div>

<!-- Engineered Workflow (Clean Numbered Grid) -->
<div class="py-24 bg-slate-50 relative overflow-hidden" id="alur">
    <!-- Background element -->
    <div class="absolute right-0 top-0 w-1/3 h-full bg-slate-100/50 skew-x-12 transform translate-x-32 hidden lg:block"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="mb-16" data-aos="fade-right">
            <h2 class="text-3xl font-bold text-slate-900 tracking-tight mb-4">Prosedur Operasional Terstandar</h2>
            <p class="text-slate-600 text-lg max-w-2xl">Pendekatan analitis pada setiap pergerakan. Kami tidak mengandalkan asumsi, melainkan kalkulasi teknis presisi.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-16">
            
            <!-- Step 1 -->
            <div class="relative pl-16" data-aos="fade-up" data-aos-delay="100">
                <div class="absolute left-0 top-0 text-7xl font-black text-slate-200/80 -mt-4 -ml-4 pointer-events-none select-none z-0">01</div>
                <div class="relative z-10">
                    <h3 class="text-xl font-bold text-slate-900 mb-3 flex items-center gap-3">
                        <i class="fa-solid fa-map text-blue-600 text-lg"></i> Technical Route Survey
                    </h3>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Tim lapangan kami menyusuri seluruh rute yang direncanakan. Kami menganalisis kekuatan jembatan, lebar tikungan kritis, hambatan kabel udara, hingga kedalaman draf pelabuhan untuk memastikan rute tersebut laik dan aman bagi dimensi muatan.
                    </p>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="relative pl-16" data-aos="fade-up" data-aos-delay="200">
                <div class="absolute left-0 top-0 text-7xl font-black text-slate-200/80 -mt-4 -ml-4 pointer-events-none select-none z-0">02</div>
                <div class="relative z-10">
                    <h3 class="text-xl font-bold text-slate-900 mb-3 flex items-center gap-3">
                        <i class="fa-solid fa-compass-drafting text-blue-600 text-lg"></i> Engineering & CAD Design
                    </h3>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Penyusunan gambar kerja (CAD) untuk titik berat (*Center of Gravity*). Pembuatan rancangan penempatan kargo (*Stowage Plan*), desain sistem ikat (*Lashing Plan*), dan metode pengangkatan khusus (*Lifting Plan*) oleh insinyur bersertifikat.
                    </p>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="relative pl-16" data-aos="fade-up" data-aos-delay="300">
                <div class="absolute left-0 top-0 text-7xl font-black text-slate-200/80 -mt-4 -ml-4 pointer-events-none select-none z-0">03</div>
                <div class="relative z-10">
                    <h3 class="text-xl font-bold text-slate-900 mb-3 flex items-center gap-3">
                        <i class="fa-solid fa-stamp text-blue-600 text-lg"></i> Permit & Legal Clearances
                    </h3>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Pengurusan proaktif seluruh dokumen perizinan, termasuk dispensasi penggunaan jalan raya, izin khusus pelabuhan, hingga koordinasi pengawalan aparat keamanan (*Voorrijder*) untuk mensterilkan rute.
                    </p>
                </div>
            </div>

            <!-- Step 4 -->
            <div class="relative pl-16" data-aos="fade-up" data-aos-delay="400">
                <div class="absolute left-0 top-0 text-7xl font-black text-slate-200/80 -mt-4 -ml-4 pointer-events-none select-none z-0">04</div>
                <div class="relative z-10">
                    <h3 class="text-xl font-bold text-slate-900 mb-3 flex items-center gap-3">
                        <i class="fa-solid fa-crane text-blue-600 text-lg"></i> Execution & Multi-modal Transport
                    </h3>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Operasi *heavy lifting* ke atas armada darat (Lowbed/Multi-Axle) dan laut (LCT/Barge/Breakbulk). Muatan dikawal secara ketat (*on-site supervision*) hingga tiba dengan sempurna di pondasi tujuan (*job site*).
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Fleet/Equipment Focus -->
<div class="py-24 bg-slate-900 border-t border-slate-800 relative overflow-hidden">
    <!-- Local Texture Pattern Overlay -->
    <div class="absolute inset-0 z-0 pointer-events-none" style="background-image: url('{{ asset('images/background-tekstur.png') }}'); background-size: cover; background-position: center;"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-3xl font-bold text-white mb-4 tracking-tight">Kapasitas Armada Khusus</h2>
            <p class="text-slate-400">Dukungan peralatan spesifik untuk material berat dan di luar batas (*Out of Gauge*).</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 lg:gap-8">
            <div class="text-center" data-aos="zoom-in" data-aos-delay="100">
                <div class="w-20 h-20 mx-auto bg-slate-800 rounded-2xl flex items-center justify-center text-3xl text-blue-500 mb-4 shadow-lg border border-slate-700">
                    <i class="fa-solid fa-truck-moving"></i>
                </div>
                <h4 class="font-bold text-white mb-1">Lowbed Trailer</h4>
                <p class="text-xs text-slate-400">Armada darat ekstra panjang & lebar</p>
            </div>
            <div class="text-center" data-aos="zoom-in" data-aos-delay="200">
                <div class="w-20 h-20 mx-auto bg-slate-800 rounded-2xl flex items-center justify-center text-3xl text-blue-500 mb-4 shadow-lg border border-slate-700">
                    <i class="fa-solid fa-ship"></i>
                </div>
                <h4 class="font-bold text-white mb-1">LCT & Tongkang</h4>
                <p class="text-xs text-slate-400">Transportasi laut rute pelosok</p>
            </div>
            <div class="text-center" data-aos="zoom-in" data-aos-delay="300">
                <div class="w-20 h-20 mx-auto bg-slate-800 rounded-2xl flex items-center justify-center text-3xl text-blue-500 mb-4 shadow-lg border border-slate-700">
                    <i class="fa-solid fa-link"></i>
                </div>
                <h4 class="font-bold text-white mb-1">Flat Rack & Open Top</h4>
                <p class="text-xs text-slate-400">Peti kemas OOG</p>
            </div>
            <div class="text-center" data-aos="zoom-in" data-aos-delay="400">
                <div class="w-20 h-20 mx-auto bg-slate-800 rounded-2xl flex items-center justify-center text-3xl text-blue-500 mb-4 shadow-lg border border-slate-700">
                    <i class="fa-solid fa-screwdriver-wrench"></i>
                </div>
                <h4 class="font-bold text-white mb-1">Lifting Gear</h4>
                <p class="text-xs text-slate-400">Peralatan angkat & lashing standar K3</p>
            </div>
        </div>
    </div>
</div>

<!-- Clean Corporate CTA -->
<div class="bg-blue-600 py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center" data-aos="fade-up">
        <h2 class="text-3xl md:text-4xl font-black text-white mb-6">Punya Proyek Strategis dalam Waktu Dekat?</h2>
        <p class="text-blue-100 text-lg mb-10 max-w-2xl mx-auto">
            Pastikan distribusi logistik berjalan sesuai tenggat waktu (*timeline*) tanpa insiden. Libatkan tim kami sejak fase desain (*early planning*).
        </p>
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="mailto:project@alexindoyp.co.id" class="bg-white text-blue-700 font-bold py-4 px-8 rounded-xl hover:bg-slate-50 transition-colors shadow-lg flex items-center justify-center gap-2">
                Undang Proses Tender
            </a>
            <a href="https://wa.me/622112345678" target="_blank" class="bg-blue-700 text-white border border-blue-500 font-bold py-4 px-8 rounded-xl hover:bg-blue-800 transition-colors flex items-center justify-center gap-2">
                <i class="fa-brands fa-whatsapp text-lg"></i> Diskusi via WhatsApp
            </a>
        </div>
    </div>
</div>

@endsection
