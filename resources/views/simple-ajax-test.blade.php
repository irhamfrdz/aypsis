<!DOCTYPE html>
<html>
<head>
    <title>Simple AJAX Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h1>Simple AJAX Test for Audit Log</h1>
    <button onclick="testRequest()">Test Request</button>
    <div id="result"></div>

    <script>
        function testRequest() {
            const resultDiv = document.getElementById('result');
            resultDiv.innerHTML = 'Loading...';

            // Log to console for debugging
            console.log('Starting AJAX request...');
            console.log('CSRF Token:', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            const requestData = {
                model_type: 'App\\Models\\Karyawan',
                model_id: 80
            };

            console.log('Request data:', requestData);

            fetch('/audit-logs/model', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(requestData)
            })
            .then(response => {
                console.log('Response received:', response);
                console.log('Status:', response.status);
                console.log('Headers:', response.headers);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Data received:', data);
                resultDiv.innerHTML = `
                    <h2>Success!</h2>
                    <p>Data count: ${data.data ? data.data.length : 0}</p>
                    <pre>${JSON.stringify(data, null, 2)}</pre>
                `;
            })
            .catch(error => {
                console.error('Error:', error);
                resultDiv.innerHTML = `
                    <h2>Error!</h2>
                    <p>${error.message}</p>
                `;
            });
        }
    </script>
</body>
</html>
