<!DOCTYPE html>
<html>
<head>
    <title>Test Gate In AJAX</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>Test Gate In Kontainer AJAX</h1>

    <label>Terminal:</label>
    <select id="terminal_id">
        <option value="">Pilih Terminal</option>
        <option value="5">Terminal Belawan</option>
        <option value="4">Terminal Makassar</option>
        <option value="3">Terminal Semarang</option>
    </select>

    <label>Kapal:</label>
    <select id="kapal_id">
        <option value="">Pilih Kapal</option>
        <option value="37">KM ALEXINDO 1</option>
        <option value="38">KM ALEXINDO 8</option>
        <option value="39">KM ALKEN PRINCESS</option>
    </select>

    <button onclick="testAjax()">Test AJAX</button>

    <div id="result" style="margin-top: 20px; padding: 20px; border: 1px solid #ccc;"></div>

    <script>
    function testAjax() {
        const terminalId = $('#terminal_id').val();
        const kapalId = $('#kapal_id').val();

        console.log('Testing AJAX with:', terminalId, kapalId);

        $.ajax({
            url: '/gate-in/get-kontainers-surat-jalan',
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            data: {
                terminal_id: terminalId,
                kapal_id: kapalId
            },
            success: function(data) {
                console.log('Success:', data);
                $('#result').html('<h3>Success!</h3><pre>' + JSON.stringify(data, null, 2) + '</pre>');
            },
            error: function(xhr, status, error) {
                console.log('Error:', xhr.status, xhr.responseText, error);
                $('#result').html('<h3>Error!</h3><p>Status: ' + xhr.status + '</p><p>Response: ' + xhr.responseText + '</p>');
            }
        });
    }
    </script>
</body>
</html>
