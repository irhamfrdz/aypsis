@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-6">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h2 class="text-lg font-semibold text-gray-900 mb-2">Tujuan Kirim Berhasil Ditambahkan</h2>
            <p class="text-sm text-gray-600 mb-4">Tujuan Kirim baru telah berhasil dibuat dan akan muncul di dropdown.</p>
            <div class="text-sm text-gray-500">
                Halaman ini akan tertutup otomatis dalam <span id="countdown">2</span> detik...
            </div>
        </div>
    </div>
</div>

<script>
    let countdown = 2;
    const countdownElement = document.getElementById('countdown');

    // Send message to parent window immediately
    function sendMessage() {
        if (window.opener) {
            const tujuanKirimData = @json($tujuanKirim);
            console.log('Sending tujuan kirim data:', tujuanKirimData);

            try {
                window.opener.postMessage({
                    type: 'tujuan-kirim-added',
                    windowName: window.name, // Include window name for distinction
                    data: tujuanKirimData
                }, '*');
                console.log('Message sent successfully');
            } catch (error) {
                console.error('Error sending message:', error);
            }
        } else {
            console.log('No opener window found');
        }
    }

    // Send message immediately on load
    sendMessage();

    // Auto close countdown
    const timer = setInterval(() => {
        countdown--;
        countdownElement.textContent = countdown;

        if (countdown <= 0) {
            clearInterval(timer);
            // Send message again before closing just in case
            sendMessage();
            setTimeout(() => {
                window.close();
            }, 500);
        }
    }, 1000);
</script>
@endsection
