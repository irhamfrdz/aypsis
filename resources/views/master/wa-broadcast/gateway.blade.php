@extends('layouts.app')

@section('title', 'Koneksi WhatsApp Gateway')
@section('page_title', 'Koneksi WhatsApp Gateway')

@section('content')
<div class="space-y-6 font-sans max-w-5xl mx-auto pb-12">
    
    <!-- Top Bar Navigation & Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center space-x-3.5">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 shadow-sm">
                <i class="fab fa-whatsapp text-2xl"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-slate-800 tracking-tight">Koneksi WhatsApp Gateway</h1>
                <p class="text-xs text-slate-500 mt-0.5">Kelola sesi login WhatsApp untuk pengiriman broadcast otomatis 1-klik</p>
            </div>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('master.wa-broadcast.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-600 bg-slate-50 hover:bg-slate-100 hover:text-slate-900 border border-slate-200 rounded-xl transition-all shadow-sm">
                <i class="fas fa-arrow-left mr-2 text-xs"></i>
                Riwayat Broadcast
            </a>
        </div>
    </div>

    <!-- Status Banner Alert -->
    <div id="statusBanner" class="p-5 rounded-2xl border transition-all duration-300 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50 border-slate-200">
        <div class="flex items-center space-x-3.5">
            <div id="statusIconWrap" class="w-10 h-10 rounded-xl bg-slate-200 text-slate-500 flex items-center justify-center text-lg">
                <i class="fas fa-circle-notch fa-spin" id="statusIcon"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-base font-bold text-slate-800" id="statusTitle">Memeriksa Koneksi...</h3>
                    <span id="statusBadge" class="text-xs px-2.5 py-0.5 rounded-full font-semibold bg-slate-200 text-slate-600">Checking</span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5" id="statusDesc">Menghubungkan ke microservice Baileys di server...</p>
            </div>
        </div>
        <div class="flex items-center gap-2" id="statusActions">
            <!-- Dynamic Actions -->
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left Column: QR Code / Connected Info (8 cols) -->
        <div class="lg:col-span-7 space-y-6">
            
            <!-- Card Scan QR (Shown when NOT connected) -->
            <div id="qrCard" class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm text-center">
                <div class="max-w-md mx-auto">
                    <div class="inline-flex items-center justify-center p-2 rounded-2xl bg-slate-50 border border-slate-200 mb-4 shadow-2xs">
                        <div id="qrImageContainer" class="w-64 h-64 flex items-center justify-center bg-white rounded-xl overflow-hidden relative">
                            <div id="qrLoadingSpinner" class="flex flex-col items-center justify-center text-slate-400 p-4">
                                <i class="fas fa-spinner fa-spin text-3xl mb-2 text-emerald-500"></i>
                                <span class="text-xs font-medium">Membuat QR Code WhatsApp...</span>
                            </div>
                            <img id="qrImage" src="" alt="QR Code WhatsApp" class="hidden w-full h-full object-contain p-2" />
                        </div>
                    </div>

                    <h4 class="text-base font-bold text-slate-800">Scan QR Code dengan WhatsApp HP</h4>
                    <p class="text-xs text-slate-500 mt-1">Arahkan kamera WhatsApp pada HP nomor pengirim kantor ke kode QR di atas.</p>

                    <!-- Steps Instructions -->
                    <div class="mt-6 text-left bg-slate-50 p-4 rounded-xl border border-slate-200/80 space-y-2.5 text-xs text-slate-600">
                        <div class="flex items-start space-x-2.5">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center flex-shrink-0 text-[11px]">1</span>
                            <span>Buka aplikasi <strong>WhatsApp</strong> di HP Anda.</span>
                        </div>
                        <div class="flex items-start space-x-2.5">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center flex-shrink-0 text-[11px]">2</span>
                            <span>Ketuk <strong>Menu Titik Tiga (Android)</strong> atau <strong>Pengaturan (iPhone)</strong> &rarr; <strong>Perangkat Tertaut (Linked Devices)</strong>.</span>
                        </div>
                        <div class="flex items-start space-x-2.5">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center flex-shrink-0 text-[11px]">3</span>
                            <span>Ketuk <strong>Tautkan Perangkat</strong> lalu scan kode QR di layar ini.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Connected Device Info (Shown when connected) -->
            <div id="connectedCard" class="hidden bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <div class="flex items-center space-x-4 border-b border-slate-100 pb-5 mb-5">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-600 flex items-center justify-center text-3xl shadow-sm">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg font-bold text-slate-800" id="connectedNumber">-</h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span>
                                Terkoneksi
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Sesi login aktif & siap digunakan untuk pengiriman broadcast otomatis</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200">
                        <span class="text-slate-400 font-medium">Gateway Service:</span>
                        <p class="font-bold text-slate-700 text-sm mt-0.5">Microservice Baileys</p>
                    </div>
                    <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200">
                        <span class="text-slate-400 font-medium">Port Server:</span>
                        <p class="font-bold text-slate-700 text-sm mt-0.5">3000 (Local / VPS)</p>
                    </div>
                </div>

                <div class="mt-6 pt-5 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs text-slate-400">Ingin mengganti nomor WhatsApp pengirim?</span>
                    <button type="button" id="btnLogoutSession" class="inline-flex items-center px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-xl text-xs font-bold transition-all shadow-2xs">
                        <i class="fas fa-sign-out-alt mr-1.5"></i> Putuskan Sesi (Logout)
                    </button>
                </div>
            </div>
        </div>

        <!-- Right Column: Test Send & Help (5 cols) -->
        <div class="lg:col-span-5 space-y-6">
            
            <!-- Test Send Message Card -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <div class="flex items-center space-x-2.5 border-b border-slate-100 pb-3.5 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-800">Uji Coba Kirim Pesan</h4>
                        <p class="text-[11px] text-slate-400">Kirim pesan tes ke nomor Anda</p>
                    </div>
                </div>

                <form id="testSendForm" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor WhatsApp Tujuan:</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 text-xs font-bold">+62</span>
                            <input type="text" id="testPhone" name="phone" class="block w-full rounded-xl border border-slate-200 pl-12 pr-3 py-2 text-xs font-medium text-slate-800 placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100" placeholder="8123456789" required />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Isi Pesan:</label>
                        <textarea id="testMessage" name="message" rows="3" class="block w-full rounded-xl border border-slate-200 p-3 text-xs text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-100" placeholder="Halo, ini adalah pesan uji coba broadcast AYPSIS...">Halo! Ini adalah pesan uji coba dari sistem AYPSIS WhatsApp Gateway.</textarea>
                    </div>

                    <button type="submit" id="btnTestSend" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow transition-all cursor-pointer disabled:opacity-50">
                        <i class="fas fa-paper-plane mr-2"></i>
                        <span>Kirim Pesan Uji Coba</span>
                    </button>
                </form>

                <div id="testSendResult" class="hidden mt-3 p-3 rounded-xl text-xs"></div>
            </div>

            <!-- Server Info & Quick Command -->
            <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200 text-xs text-slate-600 space-y-3">
                <div class="flex items-center space-x-2 text-slate-800 font-bold">
                    <i class="fas fa-terminal text-slate-500"></i>
                    <span>Petunjuk Menjalankan di Server:</span>
                </div>
                <p class="text-[11px] text-slate-500 leading-relaxed">
                    Pastikan microservice Node.js berjalan di background server pada port 3000:
                </p>
                <div class="bg-slate-800 text-emerald-400 p-3 rounded-xl font-mono text-[11px] overflow-x-auto select-all">
                    cd wa-gateway<br>
                    pm2 start server.js --name "aypsis-wa-gateway"
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        let isConnected = false;
        let pollTimer = null;

        function fetchStatusAndQr() {
            $.get("{{ route('master.wa-broadcast.gateway-qr-data') }}")
                .done(function(res) {
                    if (res.isReady) {
                        // Status: TERKONEKSI
                        isConnected = true;
                        $('#statusBanner')
                            .removeClass('bg-slate-50 bg-amber-50 border-slate-200 border-amber-200')
                            .addClass('bg-emerald-50 border-emerald-200');
                        $('#statusIconWrap')
                            .removeClass('bg-slate-200 bg-amber-100 text-slate-500 text-amber-600')
                            .addClass('bg-emerald-100 text-emerald-600');
                        $('#statusIcon').attr('class', 'fas fa-check');
                        $('#statusTitle').text('WhatsApp Gateway Terkoneksi');
                        $('#statusBadge')
                            .removeClass('bg-slate-200 bg-amber-200 text-slate-600 text-amber-800')
                            .addClass('bg-emerald-100 text-emerald-800')
                            .text('Online');
                        $('#statusDesc').text('Nomor Aktif: ' + (res.user ? res.user.split('@')[0].split(':')[0] : 'Terkoneksi'));

                        $('#qrCard').addClass('hidden');
                        $('#connectedCard').removeClass('hidden');
                        $('#connectedNumber').text(res.user ? '+' + res.user.split('@')[0].split(':')[0] : 'WhatsApp Terhubung');
                    } else if (res.qrImage) {
                        // Status: MENUNGGU SCAN QR
                        isConnected = false;
                        $('#statusBanner')
                            .removeClass('bg-slate-50 bg-emerald-50 border-slate-200 border-emerald-200')
                            .addClass('bg-amber-50 border-amber-200');
                        $('#statusIconWrap')
                            .removeClass('bg-slate-200 bg-emerald-100 text-slate-500 text-emerald-600')
                            .addClass('bg-amber-100 text-amber-600');
                        $('#statusIcon').attr('class', 'fas fa-qrcode');
                        $('#statusTitle').text('Menunggu Scan QR Code');
                        $('#statusBadge')
                            .removeClass('bg-slate-200 bg-emerald-100 text-slate-600 text-emerald-800')
                            .addClass('bg-amber-100 text-amber-800')
                            .text('Scan QR');
                        $('#statusDesc').text('Buka WhatsApp di HP dan scan kode QR di bawah.');

                        $('#connectedCard').addClass('hidden');
                        $('#qrCard').removeClass('hidden');
                        $('#qrLoadingSpinner').addClass('hidden');
                        $('#qrImage').attr('src', res.qrImage).removeClass('hidden');
                    } else {
                        // Status: GENERATING QR
                        isConnected = false;
                        $('#statusBanner')
                            .removeClass('bg-emerald-50 bg-amber-50 border-emerald-200 border-amber-200')
                            .addClass('bg-slate-50 border-slate-200');
                        $('#statusIconWrap')
                            .removeClass('bg-emerald-100 bg-amber-100 text-emerald-600 text-amber-600')
                            .addClass('bg-slate-200 text-slate-500');
                        $('#statusIcon').attr('class', 'fas fa-spinner fa-spin');
                        $('#statusTitle').text('Menyiapkan Sesi WhatsApp...');
                        $('#statusBadge')
                            .removeClass('bg-emerald-100 bg-amber-100 text-emerald-800 text-amber-800')
                            .addClass('bg-slate-200 text-slate-600')
                            .text('Connecting');
                        $('#statusDesc').text('Sedang memuat socket Baileys...');

                        $('#connectedCard').addClass('hidden');
                        $('#qrCard').removeClass('hidden');
                        $('#qrImage').addClass('hidden');
                        $('#qrLoadingSpinner').removeClass('hidden');
                    }
                })
                .fail(function() {
                    // Status: OFFLINE
                    isConnected = false;
                    $('#statusBanner')
                        .removeClass('bg-emerald-50 bg-amber-50 border-emerald-200 border-amber-200')
                        .addClass('bg-rose-50 border-rose-200');
                    $('#statusIconWrap')
                        .removeClass('bg-emerald-100 bg-amber-100 text-emerald-600 text-amber-600')
                        .addClass('bg-rose-100 text-rose-600');
                    $('#statusIcon').attr('class', 'fas fa-power-off');
                    $('#statusTitle').text('Microservice WA Gateway Offline');
                    $('#statusBadge')
                        .removeClass('bg-emerald-100 bg-amber-100 text-emerald-800 text-amber-800')
                        .addClass('bg-rose-100 text-rose-800')
                        .text('Offline');
                    $('#statusDesc').text('Jalankan "node server.js" di folder wa-gateway.');

                    $('#connectedCard').addClass('hidden');
                    $('#qrCard').removeClass('hidden');
                    $('#qrImage').addClass('hidden');
                    $('#qrLoadingSpinner').removeClass('hidden').html(`
                        <i class="fas fa-exclamation-triangle text-3xl mb-2 text-rose-500"></i>
                        <span class="text-xs font-semibold text-rose-700">Microservice Belum Aktif</span>
                        <span class="text-[11px] text-slate-400 mt-1">Jalankan service di port 3000</span>
                    `);
                });
        }

        // Jalankan polling setiap 4 detik
        fetchStatusAndQr();
        pollTimer = setInterval(fetchStatusAndQr, 4000);

        // Handler Logout
        $('#btnLogoutSession').on('click', function() {
            if (!confirm('Apakah Anda yakin ingin memutuskan sesi WhatsApp ini? Anda perlu scan QR ulang untuk menghubungkan nomor baru.')) {
                return;
            }

            $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Memproses Logout...');

            $.post("{{ route('master.wa-broadcast.gateway-logout') }}", { _token: "{{ csrf_token() }}" })
                .done(function(res) {
                    alert('Sesi WhatsApp berhasil diputuskan.');
                    fetchStatusAndQr();
                })
                .fail(function() {
                    alert('Gagal melakukan logout di gateway server.');
                })
                .always(function() {
                    $('#btnLogoutSession').prop('disabled', false).html('<i class="fas fa-sign-out-alt mr-1.5"></i> Putuskan Sesi (Logout)');
                });
        });

        // Handler Test Send
        $('#testSendForm').on('submit', function(e) {
            e.preventDefault();
            const phone = $('#testPhone').val().trim();
            const message = $('#testMessage').val().trim();

            if (!phone || !message) {
                alert('Silakan isi nomor telepon dan pesan.');
                return;
            }

            $('#btnTestSend').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Mengirim...');
            $('#testSendResult').addClass('hidden').removeClass('bg-emerald-50 text-emerald-800 border-emerald-200 bg-rose-50 text-rose-800 border-rose-200 border');

            $.post("{{ route('master.wa-broadcast.gateway-test-send') }}", {
                _token: "{{ csrf_token() }}",
                phone: phone,
                message: message
            })
            .done(function(res) {
                if (res.status) {
                    $('#testSendResult')
                        .removeClass('hidden')
                        .addClass('bg-emerald-50 text-emerald-800 border border-emerald-200')
                        .html('<i class="fas fa-check-circle mr-1 text-emerald-600"></i> Pesan uji coba berhasil dikirim ke nomor <b>' + phone + '</b>!');
                } else {
                    $('#testSendResult')
                        .removeClass('hidden')
                        .addClass('bg-rose-50 text-rose-800 border border-rose-200')
                        .html('<i class="fas fa-times-circle mr-1 text-rose-600"></i> Gagal: ' + (res.error || 'Terjadi kesalahan saat mengirim pesan.'));
                }
            })
            .fail(function(xhr) {
                $('#testSendResult')
                    .removeClass('hidden')
                    .addClass('bg-rose-50 text-rose-800 border border-rose-200')
                    .html('<i class="fas fa-times-circle mr-1 text-rose-600"></i> Gagal terhubung ke server WhatsApp Gateway.');
            })
            .always(function() {
                $('#btnTestSend').prop('disabled', false).html('<i class="fas fa-paper-plane mr-2"></i> Kirim Pesan Uji Coba');
            });
        });
    });
</script>
@endpush
@endsection
