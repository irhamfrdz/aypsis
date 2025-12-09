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
            /* Half folio (setengah folio) - landscape orientation: 210mm x 165mm */
            @page { size: 210mm 165mm landscape; margin: 8mm; }
            html, body { width: 210mm; height: 165mm; }
            .no-print { display: none; }
            /* Reduce font sizes to better fit half folio */
            body { font-size: 12px; }
            table th, table td { font-size: 11px; padding: 4px; }
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
