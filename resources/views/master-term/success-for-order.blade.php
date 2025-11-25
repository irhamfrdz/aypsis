<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Term Berhasil Ditambahkan</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6 text-center">
        <div class="mb-4">
            <svg class="mx-auto h-16 w-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        
        <h2 class="text-xl font-semibold text-gray-900 mb-2">Berhasil!</h2>
        <p class="text-gray-600 mb-6">
            Term "<strong>{{ $term->nama_term }}</strong>" telah berhasil ditambahkan dengan kode <strong>{{ $term->kode_term }}</strong>.
        </p>
        
        <button onclick="closeAndRefresh()" class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded">
            Tutup
        </button>
    </div>

    <script>
        function closeAndRefresh() {
            // Send message to parent window to refresh the dropdown
            if (window.opener) {
                window.opener.postMessage({
                    type: 'term-added',
                    data: {
                        id: '{{ $term->id }}',
                        nama_term: '{{ $term->nama_term }}',
                        kode_term: '{{ $term->kode_term }}'
                    }
                }, '*');
            }
            
            // Close the popup
            window.close();
        }

        // Auto close after 3 seconds
        setTimeout(function() {
            closeAndRefresh();
        }, 3000);
    </script>
</body>
</html>