<!DOCTYPE html>
<html>
<head>
    <title>Auth Check</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h1>Authentication Status</h1>
    
    @auth
        <p><strong>✅ User is logged in</strong></p>
        <p>Username: {{ auth()->user()->username }}</p>
        <p>ID: {{ auth()->user()->id }}</p>
        <p>Has audit-log-view permission: {{ auth()->user()->hasPermissionTo('audit-log-view') ? '✅ Yes' : '❌ No' }}</p>
        
        <h2>Test AJAX Request</h2>
        <button onclick="testAuditRequest()">Test Audit Log Request</button>
        <div id="result"></div>
        
        <script>
            function testAuditRequest() {
                const resultDiv = document.getElementById('result');
                resultDiv.innerHTML = 'Loading...';
                
                fetch('/audit-logs/model', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        model_type: 'App\\Models\\Karyawan',
                        model_id: 80
                    })
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    resultDiv.innerHTML = `
                        <h3>✅ Success!</h3>
                        <p>Records found: ${data.data.length}</p>
                        <pre>${JSON.stringify(data, null, 2)}</pre>
                    `;
                })
                .catch(error => {
                    console.error('Error:', error);
                    resultDiv.innerHTML = `
                        <h3>❌ Error</h3>
                        <p>${error.message}</p>
                    `;
                });
            }
        </script>
    @else
        <p><strong>❌ User is NOT logged in</strong></p>
        <p><a href="/login">Please login first</a></p>
    @endauth
</body>
</html>