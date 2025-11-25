<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tujuan Ambil Berhasil Ditambahkan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                        <i class="fas fa-check text-green-600 text-2xl"></i>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">Tujuan Ambil Berhasil Ditambahkan!</h2>
                    <p class="text-sm text-gray-600 mb-4">
                        Tujuan Ambil <strong>{{ $tujuanAmbil->ke }}</strong> telah berhasil dibuat dan akan otomatis dipilih di form order.
                    </p>
                    <div class="bg-gray-50 rounded-md p-3 mb-4">
                        <div class="text-xs text-gray-500 space-y-1">
                            <div><strong>Kode:</strong> {{ $tujuanAmbil->kode }}</div>
                            <div><strong>Cabang:</strong> {{ $tujuanAmbil->cabang }}</div>
                            <div><strong>Dari:</strong> {{ $tujuanAmbil->dari }}</div>
                            <div><strong>Ke:</strong> {{ $tujuanAmbil->ke }}</div>
                            <div><strong>Status:</strong> 
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $tujuanAmbil->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($tujuanAmbil->status) }}
                                </span>
                            </div>
                            @if($tujuanAmbil->keterangan)
                            <div><strong>Keterangan:</strong> {{ $tujuanAmbil->keterangan }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="text-sm text-gray-500 mb-4">
                        Halaman ini akan tertutup otomatis dalam <span id="countdown">3</span> detik...
                    </div>
                    <button type="button" onclick="closeWindow()" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                        <i class="fas fa-times mr-2"></i>
                        Tutup Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let countdown = 3;
        const countdownElement = document.getElementById('countdown');

        function sendMessage() {
            if (window.opener) {
                const tujuanAmbildData = @json($tujuanAmbil);
                console.log('Sending tujuan-ambil data from order form:', tujuanAmbildData);

                try {
                    window.opener.postMessage({
                        type: 'tujuan-ambil-added',
                        data: tujuanAmbildData
                    }, '*');
                    console.log('Message sent successfully to parent window');
                } catch (error) {
                    console.error('Error sending message to parent:', error);
                }
            } else {
                console.log('No opener window found');
            }
        }

        function closeWindow() {
            if (window.opener) {
                window.close();
            } else {
                history.back();
            }
        }

        sendMessage();

        const timer = setInterval(() => {
            countdown--;
            if (countdownElement) {
                countdownElement.textContent = countdown;
            }
            
            if (countdown <= 0) {
                clearInterval(timer);
                closeWindow();
            }
        }, 1000);

        window.addEventListener('beforeunload', function() {
            sendMessage();
        });
    </script>
</body>
</html>