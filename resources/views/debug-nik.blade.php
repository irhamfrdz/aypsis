<!DOCTYPE html>
<html>
<head>
    <title>Debug NIK Route</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h1>Debug NIK Route Server</h1>
    
    <div>
        <h2>1. Test Route Basic</h2>
        <p>Route URL: {{ route('master.karyawan.get-next-nik') }}</p>
        <button onclick="testRoute()">Test Route</button>
        <div id="result"></div>
    </div>

    <div>
        <h2>2. Authentication Status</h2>
        <p>Logged in: {{ auth()->check() ? 'Yes' : 'No' }}</p>
        @if(auth()->check())
            <p>User: {{ auth()->user()->name ?? 'N/A' }} (ID: {{ auth()->user()->id }})</p>
            <p>Email: {{ auth()->user()->email ?? 'N/A' }}</p>
        @endif
    </div>

    <div>
        <h2>3. Permission Check</h2>
        <p>Has master-karyawan-create permission: 
            @can('master-karyawan-create')
                <span style="color: green;">YES</span>
            @else
                <span style="color: red;">NO</span>
            @endcan
        </p>
    </div>

    <div>
        <h2>4. Database Check</h2>
        <p>Total Karyawan: {{ \App\Models\Karyawan::count() }}</p>
        <p>Next NIK should be: {{ \App\Models\Karyawan::generateNextNik() }}</p>
    </div>

    <script>
        function testRoute() {
            const resultDiv = document.getElementById('result');
            resultDiv.innerHTML = 'Loading...';
            
            fetch('{{ route("master.karyawan.get-next-nik") }}', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                resultDiv.innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
            })
            .catch(error => {
                console.error('Error:', error);
                resultDiv.innerHTML = '<p style="color: red;">Error: ' + error.message + '</p>';
            });
        }
    </script>
</body>
</html>