<!DOCTYPE html>
<html>
<head>
    <title>Test Approval Permissions</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input[type="text"] { padding: 5px; width: 200px; }
        input[type="checkbox"] { margin-right: 5px; }
        button { padding: 10px 20px; background: blue; color: white; border: none; cursor: pointer; }
        .section { background: #f5f5f5; padding: 15px; margin: 10px 0; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>Test Approval Permissions Form</h1>

    <form method="POST" action="{{ url('/test-update-user/1') }}">
        @csrf


        <div class="section">
            <h3>Basic User Info</h3>
            <div class="form-group">
                <label>Username:</label>
                <input type="text" name="username" value="admin" required>
            </div>
        </div>

        <div class="section">
            <h3>Approval Permissions Test</h3>

            <h4>Approval Tugas 1 (Supervisor/Manager)</h4>
            <div class="form-group">
                <label>
                    <input type="checkbox" name="permissions[approval-tugas-1][view]" value="1" checked>
                    View Permission (approval-tugas-1.view)
                </label>
                <label>
                    <input type="checkbox" name="permissions[approval-tugas-1][approve]" value="1" checked>
                    Approve Permission (approval-tugas-1.approve)
                </label>
            </div>

            <h4>Approval Tugas 2 (General Manager)</h4>
            <div class="form-group">
                <label>
                    <input type="checkbox" name="permissions[approval-tugas-2][view]" value="1" checked>
                    View Permission (approval-tugas-2.view)
                </label>
                <label>
                    <input type="checkbox" name="permissions[approval-tugas-2][approve]" value="1">
                    Approve Permission (approval-tugas-2.approve)
                </label>
            </div>
        </div>

        <button type="submit">Save Permissions</button>
    </form>

    <div class="section">
        <h3>Debug Info</h3>
        <p>This form will send the following data structure:</p>
        <pre>
permissions[approval-tugas-1][view] = 1
permissions[approval-tugas-1][approve] = 1
permissions[approval-tugas-2][view] = 1
        </pre>
        <p>The controller should convert these to permission IDs and sync them to the user.</p>
    </div>

    <script>
        // Add visual feedback
        document.querySelector('form').addEventListener('submit', function(e) {
            const checkedBoxes = document.querySelectorAll('input[type="checkbox"]:checked');
            let message = 'Will submit ' + checkedBoxes.length + ' permissions:\n';
            checkedBoxes.forEach(box => {
                message += '- ' + box.name + '\n';
            });
            alert(message);
        });
    </script>
</body>
</html>
