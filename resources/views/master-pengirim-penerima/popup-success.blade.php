<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penerima Berhasil Ditambahkan</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .success-container {
            text-align: center;
            background: white;
            padding: 3rem;
            border-radius: 1rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 400px;
        }
        .success-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #10b981;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            animation: scaleIn 0.5s ease-out;
        }
        .success-icon svg {
            width: 50px;
            height: 50px;
            stroke: white;
            stroke-width: 3;
            stroke-linecap: round;
            stroke-linejoin: round;
            fill: none;
        }
        h1 {
            color: #1f2937;
            margin: 0 0 0.5rem;
            font-size: 1.5rem;
        }
        p {
            color: #6b7280;
            margin: 0 0 1.5rem;
        }
        .penerima-info {
            background: #f3f4f6;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }
        .penerima-name {
            font-weight: 600;
            color: #1f2937;
            font-size: 1.1rem;
        }
        .countdown {
            color: #9ca3af;
            font-size: 0.875rem;
        }
        @keyframes scaleIn {
            from {
                transform: scale(0);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">
            <svg viewBox="0 0 24 24">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
        </div>
        <h1>Penerima Berhasil Ditambahkan!</h1>
        <p>{{ $message }}</p>
        <div class="penerima-info">
            <div class="penerima-name">{{ $penerima->nama }}</div>
            <div style="color: #6b7280; font-size: 0.875rem; margin-top: 0.25rem;">{{ $penerima->kode }}</div>
        </div>
        <p class="countdown">Window akan otomatis tertutup...</p>
    </div>

    <script>
        // Send message to parent window
        if (window.opener) {
            window.opener.postMessage({
                type: 'penerima-added',
                data: {
                    id: {{ $penerima->id }},
                    nama: '{{ addslashes($penerima->nama) }}',
                    kode: '{{ addslashes($penerima->kode) }}',
                    alamat: '{{ addslashes($penerima->alamat ?? '') }}'
                }
            }, '*');
        }

        // Auto close after 2 seconds
        setTimeout(function() {
            window.close();
        }, 2000);
    </script>
</body>
</html>
