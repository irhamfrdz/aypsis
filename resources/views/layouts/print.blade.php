<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Print - {{ config('app.name') }}</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; color: #111; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 6px; font-size: 12px; }
        th { background: #f7f7f7; }
        @media print {
            @page { size: auto; margin: 10mm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom:12px;">
        <button onclick="window.print()" style="padding:8px 12px; background:#4f46e5; color:#fff; border:none; border-radius:4px;">Print</button>
        <a href="{{ url()->previous() }}" style="margin-left:8px; padding:8px 12px; background:#e5e7eb; color:#111; text-decoration:none; border-radius:4px;">Kembali</a>
    </div>

    <div>
        @yield('content')
    </div>
</body>
</html>
