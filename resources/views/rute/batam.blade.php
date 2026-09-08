@extends('layouts.public')
@section('title', 'Rute Batam — FTZ & Sea Freight | ALEXINDO YAKINPRIMA')

@section('additional_styles')
/* ─────────────────────────────────────────
   BATAM ROUTE — Premium Design System
───────────────────────────────────────── */

/* Hero */
.batam-hero {
    background-image:
        linear-gradient(to right, rgba(8,15,35,0.92) 0%, rgba(10,20,50,0.80) 55%, rgba(8,15,35,0.65) 100%),
        url("{{ asset('images/background-batam.png') }}");
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
}

/* Decorative animated line */
@keyframes slide-right {
    from { transform: translateX(-100%); opacity: 0; }
    to   { transform: translateX(0);     opacity: 1; }
}
.hero-line {
    animation: slide-right 1s cubic-bezier(.4,0,.2,1) 0.3s both;
}

/* Stat cards */
.stat-glass {
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.10);
    backdrop-filter: blur(12px);
    transition: all .3s ease;
}
.stat-glass:hover {
    background: rgba(255,255,255,0.10);
    border-color: rgba(255,255,255,0.20);
    transform: translateY(-3px);
}

/* Port cards */
.port-card {
    transition: all .35s cubic-bezier(.4,0,.2,1);
}
.port-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 24px 56px -12px rgba(0,0,0,0.25);
}

/* Arrow pulse */
@keyframes arrow-pulse {
    0%,100% { transform: translateX(0); opacity:1; }
    50%      { transform: translateX(5px); opacity:.5; }
}
.arrow-pulse { animation: arrow-pulse 1.8s ease-in-out infinite; }

/* FTZ badge */
.ftz-badge {
    background: linear-gradient(135deg, #f59e0b 0%, #b45309 100%);
    box-shadow: 0 4px 20px rgba(245,158,11,0.40);
}

/* Schedule table */
.sched-row { transition: background .18s ease; }
.sched-row:hover td { background: #eff6ff !important; }

/* Service card */
.svc-card {
    transition: all .3s cubic-bezier(.4,0,.2,1);
}
.svc-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px -8px rgba(37,99,235,0.15);
}
.svc-card:hover .svc-icon {
    background: #2563eb !important;
}
.svc-card:hover .svc-icon i {
    color: #fff !important;
}

/* Glow dot */
@keyframes glow {
    from { box-shadow: 0 0 4px currentColor; }
    to   { box-shadow: 0 0 14px currentColor, 0 0 28px rgba(255,255,255,.1); }
}
.glow-dot { animation: glow 2s ease-in-out infinite alternate; }

/* Step connector */
.step-connector {
    background: linear-gradient(90deg, #2563eb, #0ea5e9, #f59e0b);
}

/* Count up numbers */
.num { font-variant-numeric: tabular-nums; }
@endsection

@section('content')

<!-- Dynamic Hero Section -->
<div class="relative bg-slate-950 pt-32 pb-24 lg:pt-48 lg:pb-32 overflow-hidden">
    <!-- Background Image -->
    <div class="absolute inset-0 z-0">
        <img src="{{ asset('images/background-batam.png') }}" alt="Batam Route" class="w-full h-full object-cover opacity-40 mix-blend-luminosity">
        <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-900/90 to-transparent"></div>
    </div>
    
    <!-- Accent element -->
    <div class="absolute top-0 right-0 w-1/3 h-full bg-blue-500/5 transform skew-x-12 translate-x-32 hidden lg:block z-0 pointer-events-none"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumbs -->
        <nav class="flex text-sm text-slate-400 mb-8" aria-label="Breadcrumb" data-aos="fade-down">
            <ol class="inline-flex items-center space-x-2">
                <li><a href="{{ route('home') }}" class="hover:text-blue-400 transition-colors">Beranda</a></li>
                <li><i class="fa-solid fa-chevron-right text-[10px] opacity-60"></i></li>
                <li><span class="cursor-default">Rute</span></li>
                <li><i class="fa-solid fa-chevron-right text-[10px] opacity-60"></i></li>
                <li class="text-white font-medium">Batam</li>
            </ol>
        </nav>

        <div class="max-w-3xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 text-blue-400 border border-blue-500/20 text-sm font-semibold mb-6" data-aos="fade-right" data-aos-delay="100">
                <i class="fa-solid fa-star"></i> Kawasan FTZ Batam
            </div>
            
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-white mb-6 leading-tight tracking-tight" data-aos="fade-up" data-aos-delay="200">
                Rute Logistik <br>
                <span class="text-blue-500">Jakarta — Batam</span>
            </h1>
            
            <p class="text-lg md:text-xl text-slate-300 mb-8 leading-relaxed max-w-2xl" data-aos="fade-up" data-aos-delay="300">
                Konektivitas laut langsung 3× seminggu dari Sunda Kelapa ke Pelabuhan Srimas. Nikmati keistimewaan Kawasan Perdagangan Bebas (FTZ) Batam — bebas PPN & bea masuk untuk kelancaran bisnis Anda.
            </p>
            
            <div class="flex flex-wrap gap-4" data-aos="fade-up" data-aos-delay="400">
                <a href="#jadwal" class="inline-flex items-center justify-center px-8 py-3.5 text-base font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-full transition-all shadow-lg hover:shadow-blue-500/30">
                    Cek Jadwal Kapal <i class="fa-solid fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ╔══════════════════════════════════════╗
     ║  2. RINGKASAN RUTE + FTZ INFO        ║
     ╚══════════════════════════════════════╝ --}}
<section class="py-24 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-start">

            {{-- Kiri: Penjelasan --}}
            <div data-aos="fade-right">
                <span class="text-blue-600 font-bold tracking-widest uppercase text-xs mb-3 block">Mengenal Rute Ini</span>
                <h2 class="text-3xl md:text-4xl font-black text-slate-900 mb-3 leading-tight">
                    Koridor Logistik<br>Jakarta — Batam
                </h2>
                <div class="w-14 h-1.5 bg-blue-600 rounded-full mb-7"></div>

                <p class="text-slate-600 leading-relaxed mb-5 text-[15px]">
                    <strong class="text-slate-800">Rute Jakarta–Batam</strong> adalah jalur pelayaran laut reguler yang menghubungkan
                    Pelabuhan Sunda Kelapa (Jakarta Utara) dengan Pelabuhan Srimas (Batam Centre). Rute ini merupakan
                    koridor logistik terpenting di wilayah barat Indonesia — menghubungkan pusat industri terbesar
                    dengan pulau industri ekspor terkemuka sekaligus kawasan FTZ.
                </p>
                <p class="text-slate-600 leading-relaxed mb-8 text-[15px]">
                    Batam ditetapkan sebagai <strong class="text-slate-800">Kawasan Perdagangan Bebas dan Pelabuhan Bebas (KPBPB)</strong>
                    melalui PP No. 46/2007. Barang yang masuk ke Batam <em>bebas dari PPN, PPnBM, dan bea masuk</em>,
                    menjadikannya hub distribusi sangat efisien untuk re-ekspor maupun distribusi ke Kepulauan Riau.
                </p>

                <ul class="space-y-3">
                    @foreach([
                        'Jadwal reguler 3× seminggu — tanpa transit pelabuhan lain',
                        'Bebas bea masuk & PPN di kawasan FTZ Batam',
                        'Pengurusan dokumen kepabeanan terintegrasi (EDI)',
                        'Tracking real-time kargo via sistem AYPSIS',
                        'Layanan Door-to-Door tersedia seluruh area Batam',
                        'Tim PPJK berpengalaman untuk proses impor/ekspor',
                    ] as $item)
                    <li class="flex items-center gap-3">
                        <div class="w-5 h-5 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-check text-emerald-600 text-[10px]"></i>
                        </div>
                        <span class="text-slate-700 text-sm font-medium">{{ $item }}</span>
                    </li>
                    @endforeach
                </ul>

                {{-- Komoditas --}}
                <div class="mt-8 pt-8 border-t border-slate-200">
                    <h4 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-4">
                        <i class="fa-solid fa-boxes-stacked text-blue-500 mr-2"></i>Komoditas Utama
                    </h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach(['FMCG','Elektronik','Mesin Industri','Bahan Bangunan','Makanan & Minuman','Textile','Kimia','Suku Cadang','Material FTZ','Project Cargo'] as $k)
                        <span class="px-3 py-1 bg-white border border-slate-200 rounded-full text-xs font-semibold text-slate-600 hover:border-blue-300 hover:text-blue-700 hover:bg-blue-50 transition-all cursor-default">{{ $k }}</span>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Kanan: FTZ Card --}}
            <div data-aos="fade-left">
                {{-- FTZ Info --}}
                <div class="rounded-3xl overflow-hidden border border-amber-200 shadow-xl shadow-amber-100/60 mb-6"
                     style="background: linear-gradient(145deg, #fffbeb 0%, #fef3c7 60%, #fde68a 100%);">
                    <div class="p-8">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="ftz-badge w-14 h-14 rounded-2xl flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-star text-white text-2xl"></i>
                            </div>
                            <div>
                                <div class="font-black text-slate-900 text-xl leading-tight">Kawasan FTZ Batam</div>
                                <div class="text-amber-700 text-sm font-semibold mt-0.5">Free Trade Zone — Keistimewaan Pajak</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mb-5">
                            @foreach([
                                ['label'=>'PPN',       'val'=>'0%',   'desc'=>'Pajak Pertambahan Nilai'],
                                ['label'=>'Bea Masuk', 'val'=>'0%',   'desc'=>'Untuk barang masuk FTZ'],
                                ['label'=>'PPnBM',     'val'=>'0%',   'desc'=>'Pajak Barang Mewah'],
                                ['label'=>'Re-ekspor', 'val'=>'✓',    'desc'=>'Bebas ke negara ketiga'],
                            ] as $f)
                            <div class="bg-white/70 backdrop-blur rounded-xl p-4 border border-amber-200/60">
                                <div class="text-[10px] font-black text-amber-700 uppercase tracking-widest mb-1">{{ $f['label'] }}</div>
                                <div class="text-2xl font-black text-slate-900">{{ $f['val'] }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">{{ $f['desc'] }}</div>
                            </div>
                            @endforeach
                        </div>

                        <p class="text-xs text-amber-800/70 leading-relaxed">
                            * Keistimewaan FTZ berlaku untuk barang yang masuk & tetap di kawasan FTZ Batam.
                            Hubungi tim kami untuk konsultasi kebutuhan spesifik Anda.
                        </p>
                    </div>
                    <div class="bg-amber-500 px-8 py-3 flex items-center justify-between">
                        <span class="text-white text-sm font-bold">Konsultasi Bebas Pajak FTZ</span>
                        <a href="#konsultasi" class="text-amber-100 hover:text-white transition-colors text-sm flex items-center gap-1 font-semibold">
                            Hubungi Tim <i class="fa-solid fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>

                {{-- Quick Facts --}}
                <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
                    <h4 class="font-bold text-slate-900 text-sm uppercase tracking-wider mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-circle-info text-blue-500"></i> Info Singkat Rute
                    </h4>
                    <div class="space-y-3">
                        @foreach([
                            ['label'=>'Jarak Laut',    'val'=>'±850 km',        'icon'=>'fa-route'],
                            ['label'=>'Transit Time',  'val'=>'36 – 48 jam',   'icon'=>'fa-clock'],
                            ['label'=>'Cutoff Kargo',  'val'=>'H-1 sebelum ETD','icon'=>'fa-triangle-exclamation'],
                            ['label'=>'Port Asal',     'val'=>'Sunda Kelapa, Jakarta','icon'=>'fa-anchor'],
                            ['label'=>'Port Tujuan',   'val'=>'Srimas, Batam Centre','icon'=>'fa-anchor'],
                        ] as $f)
                        <div class="flex items-center gap-3 py-2 border-b border-slate-50 last:border-0">
                            <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid {{ $f['icon'] }} text-blue-500 text-xs"></i>
                            </div>
                            <div class="flex-1 flex items-center justify-between">
                                <span class="text-slate-500 text-sm">{{ $f['label'] }}</span>
                                <span class="text-slate-900 font-bold text-sm">{{ $f['val'] }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ╔══════════════════════════════════════╗
     ║  3. PELABUHAN ASAL & TUJUAN          ║
     ╚══════════════════════════════════════╝ --}}
<section class="py-20 bg-white border-y border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-14" data-aos="fade-up">
            <span class="text-blue-600 font-bold tracking-widest uppercase text-xs mb-3 block">Infrastruktur Pelabuhan</span>
            <h2 class="text-3xl md:text-4xl font-black text-slate-900">Titik Keberangkatan & Tujuan</h2>
            <p class="text-slate-500 mt-3 text-[15px] max-w-lg mx-auto">Dua pelabuhan utama yang menjadi jantung rute logistik Jakarta–Batam.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-11 gap-6 items-stretch">

            {{-- Port 1: Sunda Kelapa --}}
            <div class="lg:col-span-5" data-aos="fade-right">
                <div class="port-card h-full rounded-3xl overflow-hidden border border-slate-700"
                     style="background: linear-gradient(145deg,#0f172a 0%,#1e293b 100%);">
                    <div class="p-8">
                        <div class="flex items-start justify-between mb-6">
                            <div>
                                <span class="text-blue-400 text-xs font-bold uppercase tracking-widest mb-2 block">Pelabuhan Keberangkatan</span>
                                <h3 class="text-2xl font-black text-white">Sunda Kelapa</h3>
                                <p class="text-slate-400 text-sm mt-1">Penjaringan, Jakarta Utara</p>
                            </div>
                            <div class="w-13 h-13 bg-blue-600/20 rounded-2xl flex items-center justify-center border border-blue-500/30 w-[52px] h-[52px]">
                                <i class="fa-solid fa-anchor text-blue-400 text-xl"></i>
                            </div>
                        </div>

                        <div class="space-y-2.5 mb-7">
                            @foreach([
                                ['icon'=>'fa-map-pin',        'label'=>'Koordinat',   'val'=>'6°7\'S, 106°48\'E'],
                                ['icon'=>'fa-warehouse',      'label'=>'Fasilitas',   'val'=>'CFS · CY · Crane · Forklift'],
                                ['icon'=>'fa-clock',          'label'=>'Operasional', 'val'=>'Senin–Sabtu, 07.00–17.00 WIB'],
                                ['icon'=>'fa-truck-ramp-box', 'label'=>'Akses Darat', 'val'=>'Depo kontainer mandiri'],
                            ] as $d)
                            <div class="flex items-center gap-3 bg-white/5 rounded-xl px-4 py-2.5">
                                <i class="fa-solid {{ $d['icon'] }} text-blue-400 text-xs w-4 text-center flex-shrink-0"></i>
                                <div class="flex-1 min-w-0 flex items-center justify-between gap-2">
                                    <span class="text-slate-500 text-xs whitespace-nowrap">{{ $d['label'] }}</span>
                                    <span class="text-white text-xs font-semibold text-right">{{ $d['val'] }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="flex items-center gap-2.5 px-4 py-2.5 bg-blue-500/15 rounded-full w-fit border border-blue-400/20">
                            <span class="glow-dot w-2 h-2 rounded-full bg-blue-400 flex-shrink-0" style="color:#60a5fa;"></span>
                            <span class="text-blue-300 text-xs font-bold">Aktif — 3 Pelayaran / Minggu</span>
                        </div>
                    </div>

                    <div class="bg-blue-600/20 border-t border-blue-500/20 px-8 py-4 flex items-center justify-between">
                        <div>
                            <div class="text-blue-300 text-xs">ETD Reguler</div>
                            <div class="text-white font-bold text-sm">Sen / Rab / Jum — 08.00 WIB</div>
                        </div>
                        <i class="fa-solid fa-ship text-blue-400/60 text-2xl"></i>
                    </div>
                </div>
            </div>

            {{-- Center Arrow --}}
            <div class="lg:col-span-1 flex flex-col items-center justify-center gap-4 py-6" data-aos="zoom-in">
                <div class="hidden lg:flex flex-col items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                        <i class="fa-solid fa-arrow-right text-blue-600 arrow-pulse"></i>
                    </div>
                    <div class="w-0.5 h-20 bg-gradient-to-b from-blue-300 to-amber-400 rounded-full"></div>
                    <div class="bg-slate-100 rounded-xl px-3 py-2 text-center">
                        <div class="text-xs font-black text-slate-700">±850 km</div>
                        <div class="text-[10px] text-slate-400">36–48 jam</div>
                    </div>
                </div>
                <div class="lg:hidden flex items-center gap-2 text-slate-400">
                    <div class="flex-1 h-px bg-slate-300"></div>
                    <i class="fa-solid fa-arrow-down text-slate-400"></i>
                    <div class="flex-1 h-px bg-slate-300"></div>
                </div>
            </div>

            {{-- Port 2: Srimas Batam --}}
            <div class="lg:col-span-5" data-aos="fade-left">
                <div class="port-card h-full rounded-3xl overflow-hidden border-2 border-amber-400"
                     style="background: linear-gradient(145deg,#fffbeb 0%,#fef9ee 100%); box-shadow: 0 8px 40px rgba(245,158,11,.15);">
                    <div class="p-8">
                        <div class="flex items-start justify-between mb-6">
                            <div>
                                <span class="text-amber-600 text-xs font-bold uppercase tracking-widest mb-2 block">Pelabuhan Tujuan ★ FTZ</span>
                                <h3 class="text-2xl font-black text-slate-900">Srimas Batam</h3>
                                <p class="text-amber-700 text-sm mt-1">Batam Centre, Kepulauan Riau</p>
                            </div>
                            <div class="ftz-badge w-[52px] h-[52px] rounded-2xl flex items-center justify-center">
                                <i class="fa-solid fa-anchor text-white text-xl"></i>
                            </div>
                        </div>

                        <div class="space-y-2.5 mb-7">
                            @foreach([
                                ['icon'=>'fa-map-pin',  'label'=>'Koordinat',    'val'=>'1°7\'N, 104°3\'E'],
                                ['icon'=>'fa-warehouse','label'=>'Fasilitas',    'val'=>'CFS · CY · Bonded Logistics'],
                                ['icon'=>'fa-clock',    'label'=>'Operasional',  'val'=>'Senin–Sabtu, 07.00–17.00 WIB'],
                                ['icon'=>'fa-star',     'label'=>'Status Kawasan','val'=>'KPBPB — Free Trade Zone'],
                            ] as $d)
                            <div class="flex items-center gap-3 bg-amber-100/60 rounded-xl px-4 py-2.5 border border-amber-200/50">
                                <i class="fa-solid {{ $d['icon'] }} text-amber-600 text-xs w-4 text-center flex-shrink-0"></i>
                                <div class="flex-1 min-w-0 flex items-center justify-between gap-2">
                                    <span class="text-amber-700 text-xs whitespace-nowrap">{{ $d['label'] }}</span>
                                    <span class="text-slate-900 text-xs font-semibold text-right">{{ $d['val'] }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="flex items-center gap-2.5 px-4 py-2.5 bg-amber-500/15 rounded-full w-fit border border-amber-400/30">
                            <span class="glow-dot w-2 h-2 rounded-full bg-amber-500 flex-shrink-0" style="color:#f59e0b;"></span>
                            <span class="text-amber-700 text-xs font-bold">FTZ Aktif — Bebas PPN & Bea Masuk</span>
                        </div>
                    </div>

                    <div class="bg-amber-500 px-8 py-4 flex items-center justify-between">
                        <div>
                            <div class="text-amber-100 text-xs">ETA dari Jakarta</div>
                            <div class="text-white font-bold text-sm">Sen / Rab / Jum — ~20.00 WIB</div>
                        </div>
                        <i class="fa-solid fa-ship text-white/40 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ╔══════════════════════════════════════╗
     ║  4. ALUR SOP PENGIRIMAN              ║
     ╚══════════════════════════════════════╝ --}}
<section class="py-24 bg-slate-50 border-b border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-16" data-aos="fade-up">
            <span class="text-blue-600 font-bold tracking-widest uppercase text-xs mb-3 block">SOP Pengiriman</span>
            <h2 class="text-3xl md:text-4xl font-black text-slate-900 mb-3">Alur 5 Tahap Pengiriman</h2>
            <p class="text-slate-500 text-[15px] max-w-xl mx-auto">Dari gudang Anda di Jakarta hingga tiba di Batam — setiap tahap terdokumentasi dan dapat dipantau.</p>
        </div>

        <div class="relative">
            {{-- Connector line desktop --}}
            <div class="hidden lg:block absolute top-[27px] left-[10%] right-[10%] h-0.5 step-connector opacity-30 rounded-full z-0"></div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 relative z-10">
                @foreach([
                    ['n'=>1,'icon'=>'fa-boxes-packing',  'c'=>'blue',   'title'=>'Pick-up & Konsolidasi',       'desc'=>'Kargo dijemput dari gudang/pabrik Anda di Jakarta & Jabodetabek. LCL dikonsolidasikan di depo.'],
                    ['n'=>2,'icon'=>'fa-file-invoice',   'c'=>'indigo', 'title'=>'Administrasi & Dokumen',       'desc'=>'Pengurusan B/L, manifest, dan dokumen bea cukai. Untuk FTZ, kami urus PIBK & NPE terintegrasi.'],
                    ['n'=>3,'icon'=>'fa-anchor',         'c'=>'blue',   'title'=>'Loading Sunda Kelapa',         'desc'=>'Stuffing & pemuatan peti kemas ke kapal di Pelabuhan Sunda Kelapa dengan pengawasan ketat.'],
                    ['n'=>4,'icon'=>'fa-ship',           'c'=>'cyan',   'title'=>'Pelayaran 36–48 Jam',          'desc'=>'Kapal berlayar melewati Selat Bangka. Posisi kapal dapat dipantau real-time via sistem AYPSIS.'],
                    ['n'=>5,'icon'=>'fa-check-double',   'c'=>'amber',  'title'=>'Bongkar & Delivery Batam',    'desc'=>'Kargo dibongkar di Srimas, proses FTZ, lalu distribusi last-mile ke seluruh area Batam.'],
                ] as $s)
                <div class="group" data-aos="fade-up" data-aos-delay="{{ $loop->index * 70 }}">
                    <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm hover:shadow-lg hover:border-{{ $s['c'] }}-100 transition-all duration-300 h-full">
                        <div class="relative mb-5">
                            <div class="w-14 h-14 bg-{{ $s['c'] }}-50 group-hover:bg-{{ $s['c'] }}-600 rounded-2xl flex items-center justify-center transition-all duration-300 mx-auto lg:mx-0">
                                <i class="fa-solid {{ $s['icon'] }} text-{{ $s['c'] }}-600 group-hover:text-white text-xl transition-colors duration-300"></i>
                            </div>
                            <div class="absolute -top-2 -right-2 lg:right-auto lg:-left-2 w-6 h-6 bg-slate-900 text-white rounded-full flex items-center justify-center text-xs font-black shadow">
                                {{ $s['n'] }}
                            </div>
                        </div>
                        <h3 class="font-bold text-slate-900 text-sm mb-2 leading-snug text-center lg:text-left">{{ $s['title'] }}</h3>
                        <p class="text-slate-500 text-xs leading-relaxed text-center lg:text-left">{{ $s['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ╔══════════════════════════════════════╗
     ║  5. JADWAL KAPAL                     ║
     ╚══════════════════════════════════════╝ --}}
<section class="py-24 bg-white" id="jadwal">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6 mb-12">
            <div data-aos="fade-right">
                <span class="text-blue-600 font-bold tracking-widest uppercase text-xs mb-3 block">Jadwal Reguler</span>
                <h2 class="text-3xl md:text-4xl font-black text-slate-900">Jadwal Kapal Mingguan</h2>
                <p class="text-slate-500 mt-2 text-[15px]">3 keberangkatan per minggu. Cutoff kargo: H-1 sebelum ETD.</p>
            </div>
            <div class="flex items-center gap-2 flex-wrap" data-aos="fade-left">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold rounded-full">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> On Time
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 border border-amber-200 text-amber-700 text-xs font-bold rounded-full">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span> Segera Berangkat
                </span>
                <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 px-4 py-1.5 bg-blue-600 text-white text-xs font-bold rounded-full hover:bg-blue-700 transition-colors">
                    <i class="fa-solid fa-lock text-[10px]"></i> Jadwal Lengkap
                </a>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm" data-aos="fade-up">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-900 text-white text-left">
                            <th class="px-6 py-4 font-semibold text-[11px] uppercase tracking-widest">Kapal</th>
                            <th class="px-6 py-4 font-semibold text-[11px] uppercase tracking-widest">Hari</th>
                            <th class="px-6 py-4 font-semibold text-[11px] uppercase tracking-widest">ETD — Sunda Kelapa</th>
                            <th class="px-6 py-4 font-semibold text-[11px] uppercase tracking-widest">ETA — Srimas Batam</th>
                            <th class="px-6 py-4 font-semibold text-[11px] uppercase tracking-widest">Transit</th>
                            <th class="px-6 py-4 font-semibold text-[11px] uppercase tracking-widest">Tipe</th>
                            <th class="px-6 py-4 font-semibold text-[11px] uppercase tracking-widest">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach([
                            ['kapal'=>'MV Srikandi Nusantara','hari'=>'Senin',  'etd'=>'08.00 WIB','eta'=>'Selasa, 20.00 WIB','tr'=>'36 jam',     'tipe'=>'FCL / LCL','st'=>'on-time','sl'=>'On Time'],
                            ['kapal'=>'MV Batam Express',      'hari'=>'Rabu',   'etd'=>'10.00 WIB','eta'=>'Kamis, 22.00 WIB', 'tr'=>'36 jam',     'tipe'=>'FCL',      'st'=>'soon',   'sl'=>'Segera'],
                            ['kapal'=>'MV Kepri Jaya',         'hari'=>'Jumat',  'etd'=>'09.00 WIB','eta'=>'Sabtu, 21.00 WIB', 'tr'=>'36–48 jam', 'tipe'=>'FCL / LCL','st'=>'on-time','sl'=>'On Time'],
                        ] as $r)
                        <tr class="sched-row">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid fa-ship text-blue-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 text-sm">{{ $r['kapal'] }}</div>
                                        <div class="text-[11px] text-slate-400">General Cargo</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-800">{{ $r['hari'] }}</td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $r['etd'] }}</div>
                                <div class="text-[11px] text-slate-400">Sunda Kelapa, Jakarta</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $r['eta'] }}</div>
                                <div class="text-[11px] text-slate-400">Srimas, Batam FTZ</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg text-xs font-bold">{{ $r['tr'] }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold">{{ $r['tipe'] }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($r['st']==='on-time')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-full text-xs font-bold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>{{ $r['sl'] }}
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-50 border border-amber-200 text-amber-700 rounded-full text-xs font-bold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>{{ $r['sl'] }}
                                </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="bg-slate-50 border-t border-slate-100 px-6 py-3 flex items-center justify-between gap-4">
                <p class="text-xs text-slate-400 flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-info text-slate-400"></i>
                    Jadwal dapat berubah sesuai kondisi cuaca/operasional. Cutoff cargo: H-1 sebelum ETD.
                </p>
                <a href="{{ route('login') }}" class="text-xs text-blue-600 font-bold hover:underline flex items-center gap-1 whitespace-nowrap">
                    Booking online <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>
    </div>
</section>

{{-- ╔══════════════════════════════════════╗
     ║  6. PETA INTERAKTIF                  ║
     ╚══════════════════════════════════════╝ --}}
<section class="py-20 bg-slate-900 relative overflow-hidden">
    <div class="absolute inset-0 opacity-30" style="background-image:url('{{ asset('images/backgorund-tekstur 10.png') }}'); background-size: cover; background-position: center;"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

        <div class="text-center mb-10" data-aos="fade-up">
            <span class="text-blue-400 font-bold tracking-widest uppercase text-xs mb-3 block">Visualisasi Rute</span>
            <h2 class="text-3xl font-black text-white">Peta Jalur Pelayaran</h2>
            <p class="text-slate-400 mt-2 text-[15px]">Jalur laut dari Sunda Kelapa (Jakarta) menuju Srimas (Batam) — ±850 km.</p>
        </div>

        <div class="relative rounded-3xl overflow-hidden border border-slate-700 shadow-2xl" data-aos="zoom-in-up" style="height:460px;">
            <div id="batam-map" class="w-full h-full"></div>
            <div class="absolute top-4 left-4 bg-slate-900/90 backdrop-blur border border-slate-700 rounded-xl px-4 py-3 shadow-lg z-[1000]">
                <div class="font-bold text-white text-sm mb-2 flex items-center gap-2">
                    <i class="fa-solid fa-route text-blue-400"></i> Rute Aktif
                </div>
                <div class="space-y-1 text-xs text-slate-400">
                    <div class="flex items-center gap-2"><span class="w-5 border-t-2 border-dashed border-slate-500 inline-block"></span> Jalur Laut</div>
                    <div class="flex items-center gap-2"><span class="w-5 border-t-2 border-red-500 inline-block"></span> Trail Kapal</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ╔══════════════════════════════════════╗
     ║  7. LAYANAN DI RUTE INI              ║
     ╚══════════════════════════════════════╝ --}}
<section class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-14" data-aos="fade-up">
            <span class="text-blue-600 font-bold tracking-widest uppercase text-xs mb-3 block">Layanan Kami</span>
            <h2 class="text-3xl md:text-4xl font-black text-slate-900">Solusi Logistik Rute Batam</h2>
            <p class="text-slate-500 mt-3 text-[15px] max-w-xl mx-auto">Pilih layanan yang sesuai dengan kebutuhan pengiriman ke dan dari Batam Anda.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach([
                ['icon'=>'fa-box',           'c'=>'blue',  'title'=>'FCL — Full Container',     'desc'=>'Sewa kontainer penuh (20ft & 40ft) untuk kargo bervolume besar. Privasi maksimal — kargo Anda tidak bercampur.',       'link'=>route('public.layanan.fcl'),                'badge'=>'20ft / 40ft'],
                ['icon'=>'fa-boxes-stacked', 'c'=>'blue',  'title'=>'LCL — Less Container',     'desc'=>'Konsolidasi hemat untuk kargo kecil. Hanya bayar ruang yang Anda gunakan. Ideal untuk UMKM & pengiriman periodik.',    'link'=>route('public.layanan.lcl'),                'badge'=>'Hemat Biaya'],
                ['icon'=>'fa-house-chimney', 'c'=>'blue',  'title'=>'Door-to-Door Batam',       'desc'=>'Dari gudang di Jakarta langsung ke alamat penerima di Batam. Kami urus semua moda & dokumen dari A ke Z.',             'link'=>route('public.layanan.door-to-door'),       'badge'=>'All-in-One'],
                ['icon'=>'fa-stamp',         'c'=>'amber', 'title'=>'Customs & FTZ Clearance',  'desc'=>'Kepengurusan kepabeanan FTZ resmi & terintegrasi EDI. Tim PPJK berpengalaman untuk impor & ekspor Batam.',            'link'=>route('public.layanan.customs-ftz'),        'badge'=>'★ Unggulan Batam'],
                ['icon'=>'fa-truck-fast',    'c'=>'blue',  'title'=>'Inland Transport Batam',   'desc'=>'Distribusi darat (truk/pickup) ke seluruh kawasan industri & area Batam setelah tiba di Pelabuhan Srimas.',            'link'=>route('public.layanan.inland-transportation'),'badge'=>'Se-Pulau Batam'],
                ['icon'=>'fa-ship',          'c'=>'blue',  'title'=>'Sea Freight Reguler',      'desc'=>'Pengiriman kargo laut jadwal pasti 3× seminggu dengan monitoring real-time — selalu tahu posisi kargo Anda.',          'link'=>route('public.layanan.sea-freight'),        'badge'=>'3× / Minggu'],
            ] as $l)
            <div class="svc-card group bg-white border border-slate-100 rounded-2xl p-7 flex flex-col"
                 data-aos="fade-up" data-aos-delay="{{ $loop->index * 60 }}">
                <div class="flex items-start justify-between mb-5">
                    <div class="svc-icon w-[52px] h-[52px] bg-{{ $l['c'] }}-50 rounded-xl flex items-center justify-center transition-all duration-300">
                        <i class="fa-solid {{ $l['icon'] }} text-xl text-{{ $l['c'] }}-600 transition-colors duration-300"></i>
                    </div>
                    <span class="text-[11px] font-bold px-2.5 py-1 rounded-full {{ $l['c']==='amber' ? 'bg-amber-100 text-amber-700' : 'bg-blue-50 text-blue-600' }}">
                        {{ $l['badge'] }}
                    </span>
                </div>
                <h4 class="text-base font-black text-slate-900 mb-2">{{ $l['title'] }}</h4>
                <p class="text-slate-500 text-sm leading-relaxed flex-1 mb-5">{{ $l['desc'] }}</p>
                <a href="{{ $l['link'] }}"
                   class="inline-flex items-center gap-2 text-{{ $l['c']==='amber' ? 'amber' : 'blue' }}-600 font-bold text-sm hover:text-{{ $l['c']==='amber' ? 'amber' : 'blue' }}-800 transition-colors group/link">
                    Pelajari Lebih Lanjut
                    <i class="fa-solid fa-arrow-right text-xs group-hover/link:translate-x-1 transition-transform"></i>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ╔══════════════════════════════════════╗
     ║  8. STATISTIK                        ║
     ╚══════════════════════════════════════╝ --}}
<section class="py-20 bg-slate-50 border-y border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12" data-aos="fade-up">
            <span class="text-blue-600 font-bold tracking-widest uppercase text-xs mb-3 block">Track Record</span>
            <h2 class="text-3xl font-black text-slate-900">Angka Bicara Kepercayaan</h2>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach([
                ['icon'=>'fa-calendar-check','c'=>'blue',   'val'=>'10+',    'unit'=>'Tahun',    'label'=>'Melayani Rute Batam'],
                ['icon'=>'fa-boxes-stacked', 'c'=>'cyan',   'val'=>'5.000+', 'unit'=>'Container','label'=>'Dikirim Per Tahun'],
                ['icon'=>'fa-percent',       'c'=>'emerald','val'=>'98',     'unit'=>'%',        'label'=>'On-Time Delivery Rate'],
                ['icon'=>'fa-building',      'c'=>'amber',  'val'=>'200+',   'unit'=>'Mitra',    'label'=>'Bisnis Aktif di Batam'],
            ] as $s)
            <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all text-center"
                 data-aos="fade-up" data-aos-delay="{{ $loop->index * 70 }}">
                <div class="w-12 h-12 bg-{{ $s['c'] }}-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid {{ $s['icon'] }} text-{{ $s['c'] }}-600 text-lg"></i>
                </div>
                <div class="num text-4xl font-black text-slate-900 leading-none mb-1">{{ $s['val'] }}<span class="text-lg font-bold text-slate-400 ml-1">{{ $s['unit'] }}</span></div>
                <div class="text-slate-500 text-sm font-medium mt-1">{{ $s['label'] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ╔══════════════════════════════════════╗
     ║  9. CTA KONSULTASI                   ║
     ╚══════════════════════════════════════╝ --}}
<section class="py-24 bg-blue-700 relative overflow-hidden" id="konsultasi">
    <div class="absolute inset-0 opacity-20" style="background-image:url('{{ asset('images/background-tekstur06.png') }}'); background-size: cover; background-position: center;"></div>
    <div class="absolute -top-40 -right-40 w-[600px] h-[600px] rounded-full bg-blue-500/20 blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -left-40 w-[500px] h-[500px] rounded-full bg-cyan-500/15 blur-3xl pointer-events-none"></div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-12" data-aos="zoom-in-up">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-white/10 border border-white/20 rounded-full text-white/80 text-xs font-bold uppercase tracking-widest mb-6">
                <i class="fa-solid fa-headset text-blue-200"></i> Tim Siap Membantu
            </div>
            <h2 class="text-4xl md:text-5xl font-black text-white mb-5 leading-tight">
                Siap Kirim Kargo<br>ke Batam?
            </h2>
            <p class="text-blue-100 text-lg max-w-xl mx-auto leading-relaxed">
                Dapatkan penawaran terbaik dan konsultasi gratis. Kami bantu Anda memaksimalkan efisiensi logistik via kawasan FTZ Batam.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-12" data-aos="fade-up" data-aos-delay="80">
            <a href="https://wa.me/6281234567890?text=Halo%20AYPSIS%2C%20saya%20ingin%20konsultasi%20pengiriman%20rute%20Batam" target="_blank"
               class="flex items-center gap-4 px-6 py-5 bg-white rounded-2xl hover:bg-slate-50 transition-all shadow-xl hover:shadow-2xl hover:-translate-y-1 group">
                <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-emerald-500 transition-colors">
                    <i class="fa-brands fa-whatsapp text-emerald-500 group-hover:text-white text-2xl transition-colors"></i>
                </div>
                <div>
                    <div class="font-black text-slate-900">WhatsApp</div>
                    <div class="text-slate-400 text-xs">Respon < 5 menit</div>
                </div>
            </a>
            <a href="mailto:info@alexindoyp.co.id?subject=Inquiry%20Rute%20Batam"
               class="flex items-center gap-4 px-6 py-5 bg-white/10 border border-white/20 rounded-2xl hover:bg-white/18 hover:border-white/35 transition-all hover:-translate-y-1 group">
                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-envelope text-white text-xl"></i>
                </div>
                <div>
                    <div class="font-black text-white">Email Kami</div>
                    <div class="text-blue-200 text-xs">info@alexindoyp.co.id</div>
                </div>
            </a>
            <a href="{{ route('login') }}"
               class="flex items-center gap-4 px-6 py-5 bg-white/10 border border-white/20 rounded-2xl hover:bg-white/18 hover:border-white/35 transition-all hover:-translate-y-1 group">
                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-arrow-right-to-bracket text-white text-xl"></i>
                </div>
                <div>
                    <div class="font-black text-white">Login & Booking</div>
                    <div class="text-blue-200 text-xs">Portal Customer</div>
                </div>
            </a>
        </div>

        {{-- Rute Lain --}}
        <div class="border-t border-white/15 pt-10 text-center" data-aos="fade-up">
            <p class="text-white/50 text-xs font-bold uppercase tracking-widest mb-5">Rute Lain yang Kami Layani</p>
            <div class="flex flex-wrap justify-center gap-2.5">
                @foreach([
                    ['label'=>'Jakarta & Jabodetabek',   'route'=>'public.rute.jakarta-jabodetabek'],
                    ['label'=>'Tanjung Pinang & Bintan', 'route'=>'public.rute.tanjung-pinang-bintan'],
                    ['label'=>'Karimun',                 'route'=>'public.rute.karimun'],
                    ['label'=>'Lingga',                  'route'=>'public.rute.lingga'],
                    ['label'=>'Anambas',                 'route'=>'public.rute.anambas'],
                    ['label'=>'Natuna',                  'route'=>'public.rute.natuna'],
                ] as $r)
                <a href="{{ route($r['route']) }}"
                   class="px-4 py-2 bg-white/8 border border-white/15 hover:bg-white/18 hover:border-white/30 text-white/80 hover:text-white text-sm font-semibold rounded-full transition-all">
                    {{ $r['label'] }}
                </a>
                @endforeach
            </div>
        </div>
    </div>
</section>

@endsection

@section('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Leaflet Map ────────────────────────────────────────────
    const map = L.map('batam-map', {
        zoomControl: false, dragging: false,
        scrollWheelZoom: false, doubleClickZoom: false,
    }).setView([-2.0, 107.5], 6);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; OpenStreetMap contributors &copy; CARTO',
        subdomains: 'abcd', maxZoom: 20,
    }).addTo(map);

    const P1 = [-6.1219, 106.8080]; // Sunda Kelapa
    const P2 = [1.1301,  104.0531]; // Srimas Batam
    const CP = [-2.5,    108.8];    // Control point

    // Bezier curve
    const coords = [];
    for (let i = 0; i <= 80; i++) {
        const t = i / 80, u = 1 - t;
        coords.push([
            u*u*P1[0] + 2*u*t*CP[0] + t*t*P2[0],
            u*u*P1[1] + 2*u*t*CP[1] + t*t*P2[1],
        ]);
    }

    // Dashed background
    L.polyline(coords, { color:'#94a3b8', weight:2, opacity:.5, dashArray:'6 10', lineCap:'round' }).addTo(map);

    // Red trail
    const trail = L.polyline([], { color:'#ef4444', weight:4, opacity:1, lineCap:'round' }).addTo(map);

    // Port markers
    function portDot(color) {
        return L.divIcon({ html:`<div style="width:13px;height:13px;border-radius:50%;background:${color};border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,.4)"></div>`, className:'', iconSize:[13,13], iconAnchor:[6.5,6.5] });
    }
    L.marker(P1, { icon: portDot('#2563eb') }).addTo(map)
        .bindTooltip('<b>Sunda Kelapa</b><br><small>Jakarta</small>', { permanent:true, direction:'right', className:'text-xs font-semibold border-none shadow' });
    L.marker(P2, { icon: portDot('#f59e0b') }).addTo(map)
        .bindTooltip('<b>Srimas</b><br><small>Batam FTZ</small>', { permanent:true, direction:'left', className:'text-xs font-semibold border-none shadow' });

    // Ship SVG
    const shipHtml = `<div id="btm-ship" style="transform-origin:center;transition:transform .08s linear;">
    <svg width="26" height="65" viewBox="0 0 24 60" xmlns="http://www.w3.org/2000/svg" style="filter:drop-shadow(0 6px 10px rgba(0,0,0,.4))">
      <path d="M12,2 C18,5 22,12 22,20 L22,50 C22,58 18,60 12,60 C6,60 2,58 2,50 L2,20 C2,12 6,5 12,2Z" fill="#cbd5e1" stroke="#64748b" stroke-width="1"/>
      <path d="M12,4 C17,7 20,13 20,20 L20,49 C20,55 17,57 12,57 C7,57 4,55 4,49 L4,20 C4,13 7,7 12,4Z" fill="#f1f5f9"/>
      <rect x="6" y="14" width="5" height="9" fill="#ef4444" stroke="#0f172a" stroke-width=".5"/>
      <rect x="13" y="14" width="5" height="9" fill="#3b82f6" stroke="#0f172a" stroke-width=".5"/>
      <rect x="6" y="24" width="5" height="9" fill="#eab308" stroke="#0f172a" stroke-width=".5"/>
      <rect x="13" y="24" width="5" height="9" fill="#22c55e" stroke="#0f172a" stroke-width=".5"/>
      <rect x="4" y="45" width="16" height="8" fill="#fff" stroke="#94a3b8" stroke-width="1" rx="1"/>
      <rect x="6" y="46" width="12" height="2" fill="#0284c7"/>
    </svg></div>`;

    const shipIcon = L.divIcon({ html: shipHtml, className:'', iconSize:[26,65], iconAnchor:[13,32.5] });
    const ship = L.marker(P1, { icon: shipIcon, zIndexOffset: 1000 }).addTo(map);

    function bearing(a, b) {
        const la=a[0]*Math.PI/180, lb=b[0]*Math.PI/180, dl=(b[1]-a[1])*Math.PI/180;
        return (Math.atan2(Math.sin(dl)*Math.cos(lb), Math.cos(la)*Math.sin(lb)-Math.sin(la)*Math.cos(lb)*Math.cos(dl))*180/Math.PI+360)%360;
    }

    let prog=0, dir=1;
    function animate() {
        prog += 0.0015 * dir;
        if (prog >= 1) { prog=1; dir=-1; trail.setLatLngs([]); }
        else if (prog <= 0) { prog=0; dir=1; trail.setLatLngs([]); }

        const fi = Math.min(Math.floor(prog*(coords.length-1)), coords.length-2);
        const ft = prog*(coords.length-1)-fi;
        const pos = [
            coords[fi][0]+(coords[fi+1][0]-coords[fi][0])*ft,
            coords[fi][1]+(coords[fi+1][1]-coords[fi][1])*ft,
        ];
        ship.setLatLng(pos);

        if (dir===1) { const t=coords.slice(0,fi+1); t.push(pos); trail.setLatLngs(t); }
        else         { const t=coords.slice(fi+1);    t.unshift(pos); trail.setLatLngs(t); }

        const b = bearing(coords[fi], coords[fi+1]);
        const el = document.getElementById('btm-ship');
        if (el) el.style.transform = `rotate(${dir===1?b:(b+180)%360}deg)`;
        requestAnimationFrame(animate);
    }
    animate();
});
</script>
@endsection
