<!DOCTYPE html>
<html>
<head>
    <title>Test Direct Import</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { background: #f9f9f9; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .error { background: #ffebee; color: #c62828; }
        .success { background: #e8f5e8; color: #2e7d32; }
        .info { background: #e3f2fd; color: #1565c0; }
        button { padding: 10px 20px; margin: 5px; cursor: pointer; }
        .btn-primary { background: #007bff; color: white; border: none; border-radius: 4px; }
        .btn-success { background: #28a745; color: white; border: none; border-radius: 4px; }
        .result { margin-top: 10px; padding: 10px; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>Test Import Tagihan Kontainer Sewa</h1>

    <div class="test-section info">
        <h3>Route Information</h3>
        <p><strong>Standard Import:</strong> POST {{ route('daftar-tagihan-kontainer-sewa.import') }}</p>
        <p><strong>Grouped Import:</strong> POST {{ route('daftar-tagihan-kontainer-sewa.import.grouped') }}</p>
    </div>

    <div class="test-section">
        <h3>Test 1: Upload File - Standard Import</h3>
        <form id="standardForm" enctype="multipart/form-data">
            @csrf
            <input type="file" name="file" accept=".csv" required>
            <button type="button" class="btn-primary" onclick="testStandardImport()">Test Standard Import</button>
        </form>
        <div id="standardResult"></div>
    </div>

    <div class="test-section">
        <h3>Test 2: Upload File - Grouped Import (Auto-grouping)</h3>
        <form id="groupedForm" enctype="multipart/form-data">
            @csrf
            <input type="file" name="csv_file" accept=".csv" required>
            <button type="button" class="btn-success" onclick="testGroupedImport()">Test Grouped Import</button>
        </form>
        <div id="groupedResult"></div>
    </div>

    <div class="test-section">
        <h3>Test 3: Check Permissions</h3>
        <button type="button" class="btn-primary" onclick="checkPermissions()">Check User Permissions</button>
        <div id="permissionResult"></div>
    </div>

    <script>
        function showResult(containerId, message, type = 'info') {
            const container = document.getElementById(containerId);
            container.innerHTML = `<div class="result ${type}">${message}</div>`;
        }

        async function testStandardImport() {
            const form = document.getElementById('standardForm');
            const formData = new FormData(form);

            showResult('standardResult', 'Testing standard import...', 'info');

            try {
                const response = await fetch('{{ route("daftar-tagihan-kontainer-sewa.import") }}', {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const result = await response.text();

                if (response.ok) {
                    showResult('standardResult', `Success! Response: ${result}`, 'success');
                } else {
                    showResult('standardResult', `Error ${response.status}: ${result}`, 'error');
                }
            } catch (error) {
                showResult('standardResult', `Network Error: ${error.message}`, 'error');
            }
        }

        async function testGroupedImport() {
            const form = document.getElementById('groupedForm');
            const formData = new FormData(form);

            showResult('groupedResult', 'Testing grouped import...', 'info');

            try {
                const response = await fetch('{{ route("daftar-tagihan-kontainer-sewa.import.grouped") }}', {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const result = await response.text();

                if (response.ok) {
                    showResult('groupedResult', `Success! Response: ${result}`, 'success');
                } else {
                    showResult('groupedResult', `Error ${response.status}: ${result}`, 'error');
                }
            } catch (error) {
                showResult('groupedResult', `Network Error: ${error.message}`, 'error');
            }
        }

        async function checkPermissions() {
            showResult('permissionResult', 'Checking permissions...', 'info');

            try {
                const response = await fetch('{{ route("daftar-tagihan-kontainer-sewa.index") }}', {
                    method: 'GET',
                    credentials: 'same-origin'
                });

                if (response.ok) {
                    showResult('permissionResult', 'User has access to tagihan kontainer sewa page', 'success');
                } else {
                    showResult('permissionResult', `Access denied: ${response.status}`, 'error');
                }
            } catch (error) {
                showResult('permissionResult', `Error checking permissions: ${error.message}`, 'error');
            }
        }
    </script>
</body>
</html>
