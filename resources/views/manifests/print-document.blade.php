<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Dokumen - BL: {{ $manifest->nomor_bl }}</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (for modern UI on screen) -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- FontAwesome for Premium Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <style>
        /* Print Styles */
        @media print {
            body {
                background: white !important;
                color: black !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .print-page {
                page-break-after: always;
                page-break-inside: avoid;
                display: flex;
                align-items: center;
                justify-content: center;
                height: 100vh;
                width: 100vw;
                padding: 0;
                margin: 0;
            }
            .print-image {
                max-width: 100% !important;
                max-height: 100% !important;
                object-fit: contain !important;
                width: auto !important;
                height: auto !important;
            }
        }

        /* Screen Scrollbar custom styling */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(15, 23, 42, 0.05);
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(15, 23, 42, 0.2);
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(15, 23, 42, 0.3);
        }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 font-sans min-h-screen flex flex-col antialiased selection:bg-indigo-500 selection:text-white">

    <!-- Header / Control Panel (Screen only) -->
    <header class="no-print bg-slate-950/80 backdrop-blur-md border-b border-slate-800 sticky top-0 z-50 px-6 py-4 shadow-lg">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2.5 py-0.5 bg-indigo-500/20 text-indigo-400 text-xs font-semibold rounded-full border border-indigo-500/30">
                        {{ $manifest->tipe_kontainer ?: 'Dokumen' }}
                    </span>
                    <span class="text-slate-500 text-xs">•</span>
                    <span class="text-slate-400 text-xs font-medium">Voyage: {{ $manifest->no_voyage }}</span>
                </div>
                <h1 class="text-xl md:text-2xl font-bold text-white tracking-tight flex items-center gap-2">
                    <i class="fa-solid fa-file-invoice text-indigo-500"></i>
                    Print Lampiran Dokumen
                </h1>
                <p class="text-sm text-slate-400 mt-0.5">
                    Kapal: <span class="text-slate-200 font-semibold">{{ $manifest->nama_kapal }}</span> | 
                    No. BL: <span class="text-slate-200 font-semibold">{{ $manifest->nomor_bl }}</span> | 
                    Kontainer: <span class="text-slate-200 font-semibold">{{ $manifest->nomor_kontainer }}</span>
                </p>
            </div>
            
            <!-- Actions -->
            <div class="flex items-center gap-3">
                @if(count($imageUrls) > 0)
                <button onclick="window.print()" 
                        class="flex items-center justify-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition-all duration-200 shadow-lg shadow-emerald-900/30 hover:scale-[1.02] cursor-pointer">
                    <i class="fa-solid fa-print"></i>
                    Cetak Gambar
                </button>
                @endif
                <button onclick="window.close()" 
                        class="flex items-center justify-center gap-2 px-5 py-2.5 bg-slate-800 hover:bg-slate-700 active:bg-slate-900 text-slate-200 text-sm font-semibold rounded-xl transition-all duration-200 border border-slate-700 hover:scale-[1.02] cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                    Tutup
                </button>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-grow max-w-7xl mx-auto w-full p-6 md:p-8 flex flex-col items-center justify-center">
        @if(count($imageUrls) > 0)
            <!-- Screen Layout (Styled card preview) -->
            <div class="no-print w-full max-w-4xl space-y-8">
                <div class="bg-slate-950/40 border border-slate-800/80 rounded-2xl p-4 md:p-6 backdrop-blur-sm">
                    <div class="flex items-center justify-between mb-4 border-b border-slate-800 pb-3">
                        <h2 class="text-sm font-semibold uppercase tracking-wider text-slate-400 flex items-center gap-2">
                            <i class="fa-regular fa-images text-indigo-400"></i>
                            Preview Lampiran ({{ count($imageUrls) }} file)
                        </h2>
                        <span class="text-xs text-slate-500">TIPS: Aktifkan opsi "Background graphics" saat print untuk hasil terbaik</span>
                    </div>

                    <div class="space-y-6">
                        @foreach($imageUrls as $index => $url)
                            <div class="bg-slate-900/90 rounded-xl overflow-hidden border border-slate-800 flex flex-col shadow-inner">
                                <div class="px-4 py-2.5 bg-slate-950/60 border-b border-slate-800 flex items-center justify-between text-xs text-slate-400">
                                    <span class="font-medium">Gambar #{{ $index + 1 }}</span>
                                    <a href="{{ $url }}" target="_blank" class="hover:text-indigo-400 transition-colors flex items-center gap-1">
                                        <i class="fa-solid fa-up-right-from-square"></i> Buka Original
                                    </a>
                                </div>
                                <div class="p-4 flex items-center justify-center bg-slate-950/20 min-h-[300px]">
                                    <img src="{{ $url }}" 
                                         alt="Lampiran {{ $index + 1 }}" 
                                         class="max-h-[600px] object-contain rounded shadow-lg transition-transform duration-300 hover:scale-[1.01]"
                                         onerror="this.onerror=null; this.src='/img/no-image.png'; this.parentElement.innerHTML='<div class=\'text-center py-8 text-slate-500\'><i class=\'fa-solid fa-triangle-exclamation text-3xl mb-2 text-amber-500\'></i><p class=\'text-sm font-medium\'>Gagal memuat gambar</p><p class=\'text-xs text-slate-600 mt-1\'>{{ basename($url) }}</p></div>';">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Print Layout (Invisible on screen, styled only for print media) -->
            <div class="hidden print:block">
                @foreach($imageUrls as $url)
                    <div class="print-page">
                        <img src="{{ $url }}" class="print-image" alt="Dokumen Manifest Print">
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16 px-6 max-w-md bg-slate-950/40 rounded-3xl border border-slate-800/80 shadow-2xl backdrop-blur-sm no-print">
                <div class="w-16 h-16 bg-slate-800/80 rounded-2xl flex items-center justify-center mx-auto mb-5 text-slate-400 border border-slate-700/50 shadow-inner">
                    <i class="fa-solid fa-image-slash text-2xl"></i>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Tidak Ada Gambar Dokumen</h3>
                <p class="text-sm text-slate-400 leading-relaxed mb-6">
                    Tidak ditemukan lampiran gambar di menu Tanda Terima, Tanda Terima Tanpa Surat Jalan, atau Tanda Terima LCL untuk data manifest ini.
                </p>
                <button onclick="window.close()" 
                        class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 text-sm font-semibold rounded-xl transition-all duration-200 border border-slate-700 w-full cursor-pointer">
                    Kembali ke Manifest
                </button>
            </div>
        @endif
    </main>

    <!-- Footer (Screen only) -->
    <footer class="no-print bg-slate-950 border-t border-slate-900 py-4 px-6 text-center text-xs text-slate-500">
        <p>&copy; {{ date('Y') }} Aypsis Cargo Logistics. Hak Cipta Dilindungi.</p>
    </footer>

</body>
</html>
