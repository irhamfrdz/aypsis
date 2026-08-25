@extends('layouts.public')
@section('title', 'Less than Container Load (LCL) | ALEXINDO YAKINPRIMA')

@section('content')
<!-- Hero Section (Prepared for baked-in text image) -->
<div class="relative bg-slate-900 pt-32 pb-12 lg:pt-36 lg:pb-16 min-h-[500px] lg:min-h-[600px] flex flex-col">
    <div class="absolute inset-0">
        <!-- Placeholder that falls back to unsplash if lcl-bg.png doesn't exist yet -->
        <img src="{{ asset('images/lcl-bg.png') }}" onerror="this.src='https://images.unsplash.com/photo-1578575437130-527eed3abbec?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80'" alt="LCL Consolidation" class="w-full h-full object-cover object-left lg:object-center">
    </div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full z-10 flex-grow flex flex-col justify-between">
        <!-- Breadcrumbs -->
        <nav class="flex text-sm text-slate-300" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-2 bg-slate-900/60 px-4 py-1.5 rounded-full backdrop-blur-md">
                <li><a href="{{ route('home') }}" class="hover:text-white transition-colors">Beranda</a></li>
                <li><i class="fa-solid fa-chevron-right text-[10px]"></i></li>
                <li><span class="hover:text-white transition-colors cursor-default">Layanan</span></li>
                <li><i class="fa-solid fa-chevron-right text-[10px]"></i></li>
                <li class="text-white font-medium">LCL</li>
            </ol>
        </nav>

        <!-- CTA positioned at bottom right so it doesn't overlap left-aligned text -->
        <div class="flex justify-start lg:justify-end mt-40 lg:mt-0">
            <a href="#hubungi" class="bg-teal-500 text-white hover:bg-teal-400 font-bold py-3.5 px-8 rounded-full shadow-[0_10px_20px_rgba(20,184,166,0.3)] transition-all hover:-translate-y-1 flex items-center gap-2">
                KONSULTASI LCL SEKARANG <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>

<!-- Fluid / Soft Aesthetic for LCL Definition (Differs from FCL Brutalism) -->
<div class="py-24 bg-gradient-to-b from-white to-teal-50/50 relative overflow-hidden">
    <!-- Decorative Blurs (Glassmorphism feel) -->
    <div class="absolute top-0 right-0 w-96 h-96 bg-teal-200/40 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 bg-cyan-200/40 rounded-full blur-3xl translate-y-1/3 -translate-x-1/3"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="flex flex-col lg:flex-row gap-16 items-center">
            
            <div class="w-full lg:w-1/2">
                <div class="relative w-full h-[500px] rounded-[2.5rem] overflow-hidden shadow-2xl group">
                    <div class="absolute inset-0 bg-gradient-to-t from-teal-900/80 to-transparent z-10"></div>
                    <img src="https://images.unsplash.com/photo-1494412519320-aa613dfb7738?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="LCL Warehouse" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110">
                    
                    <!-- Glass Card over image -->
                    <div class="absolute bottom-8 left-8 right-8 z-20 bg-white/10 backdrop-blur-md border border-white/20 p-6 rounded-2xl">
                        <div class="flex items-center gap-4 text-white">
                            <i class="fa-solid fa-boxes-packing text-4xl text-teal-300"></i>
                            <div>
                                <h4 class="font-bold text-lg">Gudang CFS Resmi</h4>
                                <p class="text-teal-50 text-sm">Pusat Konsolidasi Terpadu</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-full lg:w-1/2">
                <div class="inline-block bg-teal-100 text-teal-700 font-bold px-4 py-1.5 rounded-full text-sm mb-6 uppercase tracking-wider">
                    Pengertian LCL
                </div>
                <h2 class="text-4xl lg:text-5xl font-black text-slate-800 mb-6 leading-tight">Berbagi Ruang,<br><span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-500 to-cyan-500">Berbagi Biaya.</span></h2>
                <p class="text-lg text-slate-600 leading-relaxed mb-6">
                    <strong>Less than Container Load (LCL)</strong> adalah solusi cerdas untuk kargo volume kecil-menengah (1–15 CBM). Kami menggabungkan kargo Anda dengan kargo pengirim lain dalam satu kontainer. 
                </p>
                <p class="text-lg text-slate-600 leading-relaxed mb-8">
                    Mengapa membayar satu kontainer penuh jika Anda hanya butuh setengahnya? LCL mengonversi biaya logistik yang kaku menjadi sangat fleksibel.
                </p>
                
                <div class="grid grid-cols-2 gap-6">
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 border-l-4 border-l-teal-500 hover:shadow-md transition-shadow">
                        <i class="fa-solid fa-ruler-combined text-2xl text-teal-500 mb-3 block"></i>
                        <h4 class="font-bold text-slate-800">Hitung per CBM</h4>
                        <p class="text-xs text-slate-500 mt-1">Biaya proporsional sesuai volume (m³).</p>
                    </div>
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 border-l-4 border-l-cyan-500 hover:shadow-md transition-shadow">
                        <i class="fa-solid fa-ship text-2xl text-cyan-500 mb-3 block"></i>
                        <h4 class="font-bold text-slate-800">Jadwal Reguler</h4>
                        <p class="text-xs text-slate-500 mt-1">Keberangkatan mingguan konsisten.</p>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<!-- Dynamic Horizontal Timeline Workflow (Totally different from FCL) -->
<div class="py-24 bg-slate-900 overflow-hidden relative">
    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-20"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center max-w-3xl mx-auto mb-20">
            <h2 class="text-4xl lg:text-5xl font-black text-white tracking-tight mb-4">Alur Dinamis LCL</h2>
            <p class="text-slate-400 text-lg">Dari pintu pabrik hingga ke tangan penerima, proses konsolidasi kami transparan di setiap titik.</p>
        </div>

        <!-- Desktop Horizontal Stepper -->
        <div class="hidden lg:block relative mt-32">
            <!-- Connecting Line -->
            <div class="absolute top-12 left-0 right-0 h-1 bg-gradient-to-r from-teal-500 via-cyan-500 to-blue-500 opacity-30 transform -translate-y-1/2"></div>
            
            <div class="grid grid-cols-5 gap-4">
                <!-- Step 1 -->
                <div class="relative group cursor-pointer text-center">
                    <div class="w-24 h-24 mx-auto bg-slate-800 rounded-full border-4 border-slate-700 flex items-center justify-center text-3xl text-teal-400 group-hover:bg-teal-500 group-hover:border-teal-400 group-hover:text-white transition-all duration-500 shadow-xl group-hover:shadow-[0_0_30px_rgba(20,184,166,0.6)] group-hover:-translate-y-4">
                        <i class="fa-solid fa-truck-pickup"></i>
                    </div>
                    <div class="mt-8 transition-transform group-hover:-translate-y-2">
                        <span class="text-teal-400 font-bold text-sm tracking-widest uppercase block mb-1">Tahap 1</span>
                        <h3 class="text-white font-bold text-lg mb-2">Penjemputan</h3>
                        <p class="text-slate-400 text-sm">Pengambilan barang dari lokasi Anda ke gudang CFS kami.</p>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="relative group cursor-pointer text-center mt-12">
                    <div class="w-24 h-24 mx-auto bg-slate-800 rounded-full border-4 border-slate-700 flex items-center justify-center text-3xl text-teal-400 group-hover:bg-teal-500 group-hover:border-teal-400 group-hover:text-white transition-all duration-500 shadow-xl group-hover:shadow-[0_0_30px_rgba(20,184,166,0.6)] group-hover:-translate-y-4">
                        <i class="fa-solid fa-cubes-stacked"></i>
                    </div>
                    <div class="mt-8 transition-transform group-hover:-translate-y-2">
                        <span class="text-teal-400 font-bold text-sm tracking-widest uppercase block mb-1">Tahap 2</span>
                        <h3 class="text-white font-bold text-lg mb-2">Konsolidasi CFS</h3>
                        <p class="text-slate-400 text-sm">Barang diukur & digabungkan ke dalam kontainer ekspor.</p>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="relative group cursor-pointer text-center">
                    <div class="w-24 h-24 mx-auto bg-slate-800 rounded-full border-4 border-slate-700 flex items-center justify-center text-3xl text-cyan-400 group-hover:bg-cyan-500 group-hover:border-cyan-400 group-hover:text-white transition-all duration-500 shadow-xl group-hover:shadow-[0_0_30px_rgba(6,182,212,0.6)] group-hover:-translate-y-4">
                        <i class="fa-solid fa-ship"></i>
                    </div>
                    <div class="mt-8 transition-transform group-hover:-translate-y-2">
                        <span class="text-cyan-400 font-bold text-sm tracking-widest uppercase block mb-1">Tahap 3</span>
                        <h3 class="text-white font-bold text-lg mb-2">Ocean Freight</h3>
                        <p class="text-slate-400 text-sm">Pelayaran menuju pelabuhan tujuan (Port of Destination).</p>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="relative group cursor-pointer text-center mt-12">
                    <div class="w-24 h-24 mx-auto bg-slate-800 rounded-full border-4 border-slate-700 flex items-center justify-center text-3xl text-blue-400 group-hover:bg-blue-500 group-hover:border-blue-400 group-hover:text-white transition-all duration-500 shadow-xl group-hover:shadow-[0_0_30px_rgba(59,130,246,0.6)] group-hover:-translate-y-4">
                        <i class="fa-solid fa-boxes-packing"></i>
                    </div>
                    <div class="mt-8 transition-transform group-hover:-translate-y-2">
                        <span class="text-blue-400 font-bold text-sm tracking-widest uppercase block mb-1">Tahap 4</span>
                        <h3 class="text-white font-bold text-lg mb-2">Dekonsolidasi</h3>
                        <p class="text-slate-400 text-sm">Kontainer dibongkar di CFS tujuan & barang disortir kembali.</p>
                    </div>
                </div>

                <!-- Step 5 -->
                <div class="relative group cursor-pointer text-center">
                    <div class="w-24 h-24 mx-auto bg-slate-800 rounded-full border-4 border-slate-700 flex items-center justify-center text-3xl text-blue-400 group-hover:bg-blue-500 group-hover:border-blue-400 group-hover:text-white transition-all duration-500 shadow-xl group-hover:shadow-[0_0_30px_rgba(59,130,246,0.6)] group-hover:-translate-y-4">
                        <i class="fa-solid fa-people-carry-box"></i>
                    </div>
                    <div class="mt-8 transition-transform group-hover:-translate-y-2">
                        <span class="text-blue-400 font-bold text-sm tracking-widest uppercase block mb-1">Tahap 5</span>
                        <h3 class="text-white font-bold text-lg mb-2">Delivery</h3>
                        <p class="text-slate-400 text-sm">Pengiriman ke tangan penerima akhir pasca bea cukai.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Vertical Stepper -->
        <div class="lg:hidden space-y-8 relative before:absolute before:inset-0 before:ml-8 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-teal-500 before:via-cyan-500 before:to-blue-500">
            <!-- Step 1 -->
            <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                <div class="flex items-center justify-center w-16 h-16 rounded-full border-4 border-slate-900 bg-teal-500 text-white shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 z-10 text-2xl">
                    <i class="fa-solid fa-truck-pickup"></i>
                </div>
                <div class="w-[calc(100%-5rem)] md:w-[calc(50%-2.5rem)] bg-slate-800 p-6 rounded-2xl shadow-lg ml-4 md:ml-0 md:group-odd:text-right">
                    <span class="text-teal-400 font-bold text-xs uppercase mb-1 block">Tahap 1</span>
                    <h3 class="font-bold text-white text-lg mb-2">Penjemputan</h3>
                    <p class="text-slate-400 text-sm">Pengambilan barang dari lokasi Anda ke gudang CFS kami.</p>
                </div>
            </div>
            <!-- Step 2 -->
            <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                <div class="flex items-center justify-center w-16 h-16 rounded-full border-4 border-slate-900 bg-teal-500 text-white shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 z-10 text-2xl">
                    <i class="fa-solid fa-cubes-stacked"></i>
                </div>
                <div class="w-[calc(100%-5rem)] md:w-[calc(50%-2.5rem)] bg-slate-800 p-6 rounded-2xl shadow-lg ml-4 md:ml-0 md:group-odd:text-right">
                    <span class="text-teal-400 font-bold text-xs uppercase mb-1 block">Tahap 2</span>
                    <h3 class="font-bold text-white text-lg mb-2">Konsolidasi CFS</h3>
                    <p class="text-slate-400 text-sm">Barang diukur & digabungkan ke dalam kontainer ekspor.</p>
                </div>
            </div>
            <!-- Step 3 -->
            <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                <div class="flex items-center justify-center w-16 h-16 rounded-full border-4 border-slate-900 bg-cyan-500 text-white shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 z-10 text-2xl">
                    <i class="fa-solid fa-ship"></i>
                </div>
                <div class="w-[calc(100%-5rem)] md:w-[calc(50%-2.5rem)] bg-slate-800 p-6 rounded-2xl shadow-lg ml-4 md:ml-0 md:group-odd:text-right">
                    <span class="text-cyan-400 font-bold text-xs uppercase mb-1 block">Tahap 3</span>
                    <h3 class="font-bold text-white text-lg mb-2">Ocean Freight</h3>
                    <p class="text-slate-400 text-sm">Pelayaran menuju pelabuhan tujuan (Port of Destination).</p>
                </div>
            </div>
            <!-- Step 4 -->
            <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                <div class="flex items-center justify-center w-16 h-16 rounded-full border-4 border-slate-900 bg-blue-500 text-white shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 z-10 text-2xl">
                    <i class="fa-solid fa-boxes-packing"></i>
                </div>
                <div class="w-[calc(100%-5rem)] md:w-[calc(50%-2.5rem)] bg-slate-800 p-6 rounded-2xl shadow-lg ml-4 md:ml-0 md:group-odd:text-right">
                    <span class="text-blue-400 font-bold text-xs uppercase mb-1 block">Tahap 4</span>
                    <h3 class="font-bold text-white text-lg mb-2">Dekonsolidasi</h3>
                    <p class="text-slate-400 text-sm">Kontainer dibongkar di CFS tujuan & barang disortir kembali.</p>
                </div>
            </div>
            <!-- Step 5 -->
            <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                <div class="flex items-center justify-center w-16 h-16 rounded-full border-4 border-slate-900 bg-blue-500 text-white shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 z-10 text-2xl">
                    <i class="fa-solid fa-people-carry-box"></i>
                </div>
                <div class="w-[calc(100%-5rem)] md:w-[calc(50%-2.5rem)] bg-slate-800 p-6 rounded-2xl shadow-lg ml-4 md:ml-0 md:group-odd:text-right">
                    <span class="text-blue-400 font-bold text-xs uppercase mb-1 block">Tahap 5</span>
                    <h3 class="font-bold text-white text-lg mb-2">Delivery</h3>
                    <p class="text-slate-400 text-sm">Pengiriman ke tangan penerima akhir pasca bea cukai.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- LCL specific CTA -->
<div class="py-20 bg-white" id="hubungi">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gradient-to-r from-teal-500 to-cyan-600 rounded-[2.5rem] p-10 md:p-16 flex flex-col md:flex-row items-center justify-between gap-12 shadow-2xl relative overflow-hidden">
            <!-- Decorative circle -->
            <div class="absolute -top-24 -right-24 w-64 h-64 border-[30px] border-white/10 rounded-full"></div>
            <div class="absolute -bottom-24 -left-24 w-64 h-64 border-[30px] border-white/10 rounded-full"></div>

            <div class="relative z-10 text-center md:text-left max-w-2xl">
                <h2 class="text-3xl md:text-4xl font-black text-white mb-4">Konsultasikan Volume LCL Anda</h2>
                <p class="text-teal-50 text-lg">Beritahu kami dimensi dan berat barang Anda. Tim Sales kami akan menghitung kalkulasi CBM yang paling menguntungkan untuk margin bisnis Anda.</p>
            </div>

            <div class="relative z-10 flex flex-col gap-4 shrink-0 w-full md:w-auto">
                <a href="mailto:info@alexindoyp.co.id" class="bg-white text-teal-700 font-bold py-4 px-8 rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all text-center flex items-center justify-center gap-2">
                    <i class="fa-solid fa-envelope"></i> Email Sales
                </a>
                <a href="https://wa.me/622112345678" target="_blank" class="bg-transparent border-2 border-white/50 text-white font-bold py-4 px-8 rounded-xl hover:bg-white hover:text-cyan-700 transition-all text-center flex items-center justify-center gap-2">
                    <i class="fa-brands fa-whatsapp"></i> WhatsApp
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
