<!DOCTYPE html>
<html>
<head>
    <title>Simple Approval Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h1>Simple Approval Permission Test</h1>

    <p>Current user permissions:</p>
    <ul>
        @php $user = \App\Models\User::find(1); @endphp
        @foreach($user->permissions as $perm)
            <li>{{ $perm->name }}</li>
        @endforeach
    </ul>

    <form method="POST" action="/test-update-user/1">
        @csrf

        <h3>Test Approval Permissions</h3>
        <label>
            <input type="checkbox" name="permissions[approval-tugas-1][view]" value="1" checked>
            Approval Tugas 1 - View
        </label><br>

        <label>
            <input type="checkbox" name="permissions[approval-tugas-1][approve]" value="1" checked>
            Approval Tugas 1 - Approve
        </label><br>

        <label>
            <input type="checkbox" name="permissions[approval-tugas-2][view]" value="1" checked>
            Approval Tugas 2 - View
        </label><br>

        <label>
            <input type="checkbox" name="permissions[approval-tugas-2][approve]" value="1">
            Approval Tugas 2 - Approve
        </label><br>

        <br>
        <button type="submit">Test Save Permissions</button>
    </form>

    <script>
        document.querySelector('form').addEventListener('submit', function() {
            console.log('Form submitted');
        });
    </script>
</body>
</html>
