<!DOCTYPE html>
<html>
<head>
    <title>Debug Audit Log Modal</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Debug Audit Log Modal - ABDUL ROHMAN</h1>
        
        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <h2 class="text-lg font-semibold mb-4">Debug Info</h2>
            <div id="debugInfo" class="bg-gray-100 p-4 rounded font-mono text-sm"></div>
        </div>
        
        <button onclick="testAuditLog()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            Test Audit Log Request
        </button>
        
        <div class="mt-6 bg-white p-6 rounded-lg shadow">
            <h2 class="text-lg font-semibold mb-4">Response Data</h2>
            <div id="responseData" class="bg-gray-100 p-4 rounded font-mono text-sm"></div>
        </div>
    </div>

    <script>
        // Add debug info
        const debugInfo = document.getElementById('debugInfo');
        debugInfo.innerHTML = `
            CSRF Token: ${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}<br>
            Model Type: App\\Models\\Karyawan<br>
            Model ID: 80<br>
            Current URL: ${window.location.href}<br>
            Request URL: ${window.location.origin}/audit-logs/model
        `;

        function testAuditLog() {
            const responseDiv = document.getElementById('responseData');
            responseDiv.innerHTML = 'Loading...';
            
            const requestData = {
                model_type: 'App\\Models\\Karyawan',
                model_id: 80
            };
            
            console.log('Making request with data:', requestData);
            
            fetch('/audit-logs/model', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(requestData)
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                responseDiv.innerHTML = `
                    <strong>Success:</strong> ${data.success}<br>
                    <strong>Data Count:</strong> ${data.data ? data.data.length : 0}<br>
                    <strong>Raw Response:</strong><br>
                    <pre>${JSON.stringify(data, null, 2)}</pre>
                `;
                
                if (data.success && data.data && data.data.length > 0) {
                    console.log('✅ Data received successfully:', data.data.length, 'records');
                } else {
                    console.log('❌ No data or failed request');
                }
            })
            .catch(error => {
                console.error('Request failed:', error);
                responseDiv.innerHTML = `
                    <strong>Error:</strong> ${error.message}<br>
                    <pre>${error.stack}</pre>
                `;
            });
        }
        
        // Auto-run test on page load
        setTimeout(testAuditLog, 1000);
    </script>
</body>
</html>