<?php

function patchController() {
    $file = 'd:\\kerjaan\\aypsis\\aypsis\\aypsis\\app\\Http\\Controllers\\AbsensiController.php';
    $content = file_get_contents($file);

    $oldBlock = <<<EOT
        \$absensis = \$query->orderBy('tanggal', 'desc')->get();

        \$titleParts = [];
EOT;

    $newBlock = <<<EOT
        \$absensis = \$query->orderBy('tanggal', 'asc')->get();

        \$hariIndo = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        \$pdfData = [];
        \$grouped = \$absensis->groupBy('karyawan_id');

        foreach (\$grouped as \$karyawanId => \$logs) {
            \$karyawan = \$logs->first()->karyawan;
            if (!\$karyawan) continue;

            \$dayLogs = [];
            foreach (\$logs as \$log) {
                \$date = \Carbon\Carbon::parse(\$log->tanggal);
                \$dayStr = \$date->format('m/d') . ' ' . \$hariIndo[\$date->dayOfWeek];
                
                \$masuk = \$log->waktu_masuk ? \Carbon\Carbon::parse(\$log->waktu_masuk)->format('H.i') : '';
                \$pulang = \$log->waktu_pulang ? \Carbon\Carbon::parse(\$log->waktu_pulang)->format('H.i') : '';
                
                \$scanStr = '-';
                if (\$masuk || \$pulang) {
                    \$scanStr = (\$masuk ?: '') . '-' . (\$pulang ?: '');
                }
                
                \$dayLogs[] = [
                    'date_label' => \$dayStr,
                    'scan' => \$scanStr
                ];
            }
            
            \$pdfData[] = [
                'karyawan' => \$karyawan,
                'logs' => \$dayLogs
            ];
        }

        \$titleParts = [];
EOT;

    $content = str_replace($oldBlock, $newBlock, $content);
    
    $oldPdf = <<<EOT
        \$pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('absensi.pdf', [
            'startDate' => \$startDateObj,
            'endDate' => \$endDateObj,
            'absensis' => \$absensis,
            'filterTitle' => \$filterTitle,
        ])->setPaper('A4', 'landscape');
EOT;

    $newPdf = <<<EOT
        \$pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('absensi.pdf', [
            'startDate' => \$startDateObj,
            'endDate' => \$endDateObj,
            'pdfData' => \$pdfData,
            'filterTitle' => \$filterTitle,
        ])->setPaper('A4', 'portrait');
EOT;

    $content = str_replace($oldPdf, $newPdf, $content);

    file_put_contents($file, $content);
    echo "Patched successfully\n";
}

patchController();
