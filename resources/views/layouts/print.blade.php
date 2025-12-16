<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Print - {{ config('app.name') }}</title>
    <style>
        /* Basic page styles */
        body { font-family: Arial, Helvetica, sans-serif; color: #111; margin: 0; padding: 8px; }
        .print-wrapper { width: 100%; }

        /* Table styles for print */
        table { border-collapse: collapse; width: 100%; table-layout: fixed; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; font-size: 11px; vertical-align: middle; }
        th { background: #f7f7f7; font-size: 11px; font-weight: 700; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .numeric { text-align: right; white-space: nowrap; }
        .no-border { border: none !important; }
        .bold { font-weight: 700; }
        .small { font-size: 10px; }
        .table-totals th, .table-totals td { border-top: 2px solid #333; }

        /* Reduce padding for tighter fit when needed */
        .compact th, .compact td { padding: 4px 6px; }

        /* Hide helpers on print */
        .no-print { display: block; }

        /* Print-specific rules */
        @media print {
            /* F4 paper in portrait orientation (210mm x 330mm) */
            @page { size: 210mm 330mm portrait; margin: 8mm; }
            body { padding: 0; }
            .no-print { display: none !important; }
            /* Slightly smaller font for print so content fits well on F4 portrait */
            table { font-size: 10px; }
            th, td { padding: 4px 6px; }
            /* Remove focus outlines on print */
            a, button { -webkit-print-color-adjust: exact; }
        }
    </style>

    {{-- Helpful inline example for producers of print content:
        - Add `class="compact"` to the table to reduce paddings
        - Add `class="text-right numeric"` to numeric columns
        - Add `class="table-totals"` to the totals row(s)
    --}}
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
