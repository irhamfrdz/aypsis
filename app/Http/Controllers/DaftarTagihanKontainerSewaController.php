<?php

namespace App\Http\Controllers;

use App\Models\DaftarTagihanKontainerSewa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Jobs\RunCreateNextPeriode;
use App\Models\MasterPricelistSewaKontainer;

class DaftarTagihanKontainerSewaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Re-enable automatic periode creation with proper logic
        // This will create periods based on container duration like in CSV
        try {
            if (!Cache::has('tagihan:create-next-periode:lock')) {
                // dispatch a queued job so the work runs asynchronously
                \App\Jobs\RunCreateNextPeriode::dispatch();
                // prevent re-dispatch for 60 minutes
                Cache::put('tagihan:create-next-periode:lock', true, now()->addMinutes(60));
            }
        } catch (\Throwable $e) {
            // keep index working even if job dispatch fails; log could be added here
        }

        $query = DaftarTagihanKontainerSewa::query();

        // Handle search functionality with group-based search
        if ($request->filled('q')) {
            $searchTerm = $request->input('q');

            // First, check if search term matches a container number
            $foundContainer = DaftarTagihanKontainerSewa::where('nomor_kontainer', 'LIKE', '%' . $searchTerm . '%')->first();

            if ($foundContainer && $foundContainer->group) {
                // If container found and has a group, search by that group to show all containers in the same group
                $query->where('group', $foundContainer->group);
            } else {
                // Otherwise, do regular search
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('vendor', 'LIKE', '%' . $searchTerm . '%')
                      ->orWhere('nomor_kontainer', 'LIKE', '%' . $searchTerm . '%')
                      ->orWhere('group', 'LIKE', '%' . $searchTerm . '%');
                });
            }
        }

        // Handle vendor filter
        if ($request->filled('vendor')) {
            $query->where('vendor', $request->input('vendor'));
        }

        // Handle size filter
        if ($request->filled('size')) {
            $query->where('size', $request->input('size'));
        }

        // Handle periode filter
        if ($request->filled('periode')) {
            $query->where('periode', $request->input('periode'));
        }

        // Handle status filter (ongoing/selesai)
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'ongoing') {
                $query->whereNull('tanggal_akhir');
            } elseif ($status === 'selesai') {
                $query->whereNotNull('tanggal_akhir');
            }
        }

        // Handle status pranota filter
        if ($request->filled('status_pranota')) {
            $statusPranota = $request->input('status_pranota');
            if ($statusPranota === 'null') {
                // Filter untuk tagihan yang belum masuk pranota
                $query->whereNull('status_pranota');
            } else {
                // Filter untuk status pranota spesifik
                $query->where('status_pranota', $statusPranota);
            }
        }

        // Get all data first untuk grouping logic
        // Apply CSV-like logic: limit periods based on container duration
        $allData = $query->orderBy('nomor_kontainer')
            ->orderBy('periode')
            ->select('*') // Explicitly select all fields including size
            ->get()
            ->filter(function($tagihan) {
                // Filter periods based on CSV logic
                if (!$tagihan->tanggal_awal) return true;

                try {
                    $startDate = \Carbon\Carbon::parse($tagihan->tanggal_awal);
                    $currentDate = \Carbon\Carbon::now();

                    if ($tagihan->tanggal_akhir) {
                        $endDate = \Carbon\Carbon::parse($tagihan->tanggal_akhir);

                        // Jika kontainer sudah selesai (tanggal akhir <= sekarang)
                        if ($endDate->lte($currentDate)) {
                            $totalMonths = intval($startDate->diffInMonths($endDate));
                            $maxPeriode = max(1, $totalMonths + 1);
                        } else {
                            // Kontainer belum selesai tapi ada tanggal akhir
                            $totalMonths = intval($startDate->diffInMonths($currentDate));
                            $maxPeriode = max(1, $totalMonths + 1);

                            // Tapi tidak melebihi periode sampai tanggal akhir
                            $totalMonthsToEnd = intval($startDate->diffInMonths($endDate));
                            $maxPeriodeToEnd = max(1, $totalMonthsToEnd + 1);
                            $maxPeriode = min($maxPeriode, $maxPeriodeToEnd);
                        }
                    } else {
                        // Container ongoing - hitung periode sampai sekarang
                        $totalMonths = intval($startDate->diffInMonths($currentDate));
                        $maxPeriode = max(1, $totalMonths + 1);
                    }

                    // Only show periods within calculated limit
                    return $tagihan->periode <= $maxPeriode;

                } catch (\Exception $e) {
                    return true; // Show if calculation fails
                }
            });

        // Apply grouping logic sama seperti di CSV
        // Logika: vendor berbeda dengan tanggal sama = grup berbeda
        $groupedData = [];
        $globalGroupNumber = 1;

        foreach ($allData as $tagihan) {
            // Generate vendor code - semua menggunakan TK untuk group code
            $vendorCode = 'TK';

            // Parse tanggal mulai untuk format group code
            $year = '00';
            $month = '00';

            if ($tagihan->tanggal_awal) {
                try {
                    $date = \Carbon\Carbon::parse($tagihan->tanggal_awal);
                    $year = $date->format('y'); // 2 digit year (25)
                    $month = $date->format('m'); // 2 digit month (01)
                } catch (\Exception $e) {
                    // Keep default 00-00 jika parsing gagal
                }
            }

            // Create group key berdasarkan vendor ASLI dan tanggal lengkap
            // Contoh: "DPE_2025-01-21", "ZONA_2025-01-21"
            $groupKey = $tagihan->vendor . '_' . $tagihan->tanggal_awal;

            // Initialize group if not exists
            if (!isset($groupedData[$groupKey])) {
                $groupedData[$groupKey] = [
                    'groupNumber' => $globalGroupNumber,
                    'items' => [],
                    'vendor' => $tagihan->vendor,
                    'tanggal' => $tagihan->tanggal_awal
                ];
                $globalGroupNumber++;
            }

            // Generate full group code: TK1YYMMXXXXXXX
            $runningNumber = str_pad($groupedData[$groupKey]['groupNumber'], 7, '0', STR_PAD_LEFT);
            $fullGroupCode = $vendorCode . '1' . $year . $month . $runningNumber;

            // Update group field dengan group code baru
            $tagihan->group = $fullGroupCode;

            // Add to grouped data
            $groupedData[$groupKey]['items'][] = $tagihan;
        }

        // Flatten grouped data untuk pagination
        $processedData = collect([]);
        foreach ($groupedData as $groupKey => $group) {
            foreach ($group['items'] as $item) {
                $processedData->push($item);
            }
        }

        // Debug info (bisa dihapus di production)
        if (request()->has('debug_groups')) {
            $debugInfo = [];
            foreach ($groupedData as $groupKey => $group) {
                $debugInfo[] = [
                    'group_key' => $groupKey,
                    'group_code' => $group['items'][0]->group ?? 'N/A',
                    'vendor' => $group['vendor'],
                    'tanggal' => $group['tanggal'],
                    'count' => count($group['items'])
                ];
            }
            dd($debugInfo);
        }

        // Paginate the processed data
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage();
        $perPage = 15;
        $currentItems = $processedData->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $tagihans = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $processedData->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // Get filter options
        $vendors = DaftarTagihanKontainerSewa::distinct()->pluck('vendor')->filter()->sort()->values();
        $sizes = DaftarTagihanKontainerSewa::distinct()->pluck('size')->filter()->sort()->values();
        $periodes = DaftarTagihanKontainerSewa::distinct()->pluck('periode')->filter()->sort()->values();

        // Status options
        $statusOptions = [
            'ongoing' => 'Container Ongoing',
            'selesai' => 'Container Selesai'
        ];

        return view('daftar-tagihan-kontainer-sewa.index', compact('tagihans', 'vendors', 'sizes', 'periodes', 'statusOptions'));
    }

    /**
     * Download a CSV template for bulk import of tagihan kontainer sewa.
     */
    public function downloadTemplateCsv()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_daftar_tagihan_kontainer_sewa.csv"',
        ];

        // CSV template for bulk import. Include common fields so users can provide
        // tanggal/periode/tarif/dpp if they want to import full rows directly.
        $columns = [
            'vendor',
            'nomor_kontainer',
            'size',
            'group',
            'tanggal_awal',
            'tanggal_akhir',
            'periode',
            'masa',
            'tarif',
            'dpp',
            'dpp_nilai_lain',
            'ppn',
            'pph',
            'grand_total',
            'status',
        ];

        $callback = function() use ($columns) {
            $FH = fopen('php://output', 'w');
            // write UTF-8 BOM so Excel recognizes UTF-8
            fwrite($FH, "\xEF\xBB\xBF");
            // write header row using semicolon delimiter (Excel-friendly in many locales)
            fputcsv($FH, $columns, ';');
            // write an example row so users can see expected formats (dates yyyy-mm-dd, tariff labels 'Bulanan'/'Harian')
            fputcsv($FH, [
                'ZONA',                // vendor
                'ZONA-12345',          // nomor_kontainer
                '40',                  // size
                'Z001',                // group
                '2024-09-01',          // tanggal_awal
                '2024-09-30',          // tanggal_akhir
                '1',                   // periode
                '30',                  // masa (days)
                'Bulanan',             // tarif
                '',                    // dpp (leave empty to let importer lookup pricelist)
                '',                    // dpp_nilai_lain
                '',                    // ppn
                '',                    // pph
                '',                    // grand_total
                'Tersedia',            // status
            ], ';');
            fclose($FH);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import CSV file and create tagihan rows.
     */
    public function importCsv(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

    // optional flags: create_all_periods (boolean) to create rows for every period from start..end
    // and start_period (int) to override starting period (default 1)
    $createAll = (bool) $request->input('create_all_periods', false);
    $startPeriodOverride = $request->input('start_period');
    $startPeriodOverride = is_null($startPeriodOverride) ? null : (int)$startPeriodOverride;

        $file = $request->file('file');
        $path = $file->getRealPath();

        $created = 0;
        if (($FH = fopen($path, 'r')) !== false) {
            // read first line and handle BOM
            $first = fgets($FH);
            // rewind and use fgetcsv with semicolon delimiter
            rewind($FH);
            // If BOM present, strip it in header handling
            $header = fgetcsv($FH, 0, ';');
            if ($header === false) {
                return back()->with('error', 'File CSV kosong atau tidak bisa dibaca.');
            }

            // Normalize header names and build index map
            $header = array_map(function($h){ return trim(strtolower(str_replace("\xEF\xBB\xBF", '', $h))); }, $header);
            $map = [];
            foreach ($header as $i => $h) {
                $map[$h] = $i;
            }

            // helper to find first matching header name among alternatives
            $findIndex = function(array $alternatives) use ($map) {
                foreach ($alternatives as $a) {
                    $a = trim(strtolower($a));
                    if (isset($map[$a])) return $map[$a];
                }
                return null;
            };

            // find likely column indexes (support aliases)
            $vendorIdx = $findIndex(['vendor']);
            $nomorIdx = $findIndex(['nomor_kontainer', 'nomor', 'container_number']);
            $sizeIdx = $findIndex(['size', 'ukuran', 'ukuran_kontainer']);
            $groupIdx = $findIndex(['group']);
            $tanggalAwalIdx = $findIndex(['tanggal_awal', 'tanggal_mulai', 'start_date']);
            $tanggalAkhirIdx = $findIndex(['tanggal_akhir', 'tanggal_selesai', 'end_date']);
            $periodeIdx = $findIndex(['periode']);
            $masaIdx = $findIndex(['masa']);
            $tarifIdx = $findIndex(['tarif']);
            $dppIdx = $findIndex(['dpp']);
            $dppNilaiLainIdx = $findIndex(['dpp_nilai_lain']);
            $ppnIdx = $findIndex(['ppn']);
            $pphIdx = $findIndex(['pph']);
            $grandTotalIdx = $findIndex(['grand_total']);
            $statusIdx = $findIndex(['status']);

            // helper to parse numeric values tolerant to thousand separators and commas
            $parseNumber = function($v) {
                $v = trim((string)($v ?? ''));
                if ($v === '') return 0;
                // remove currency symbols and spaces
                $clean = preg_replace('/[^0-9,\.\-]/', '', $v);
                if ($clean === '') return 0;
                // if contains comma but not dot, treat comma as decimal separator
                if (strpos($clean, ',') !== false && strpos($clean, '.') === false) {
                    $clean = str_replace(',', '.', $clean);
                } else {
                    // remove thousand-separator commas
                    $clean = str_replace(',', '', $clean);
                }
                return (float)$clean;
            };

            // helper to parse dates in flexible formats
            $parseDate = function($v) {
                $v = trim((string)($v ?? ''));
                if ($v === '') return null;
                // normalize Indonesian month names and two-digit years before parsing
                $normalizeIndoMonths = function($s) {
                    $map = [
                        'jan' => 'Jan','januari'=>'Jan','feb'=>'Feb','februari'=>'Feb','mar'=>'Mar','maret'=>'Mar',
                        'apr'=>'Apr','april'=>'Apr','mei'=>'May','jun'=>'Jun','juni'=>'Jun','jul'=>'Jul','juli'=>'Jul',
                        'agu'=>'Aug','agustus'=>'Aug','sep'=>'Sep','sept'=>'Sep','september'=>'Sep','okt'=>'Oct','oktober'=>'Oct',
                        'nov'=>'Nov','november'=>'Nov','des'=>'Dec','desember'=>'Dec'
                    ];
                    foreach ($map as $k => $v2) {
                        $s = preg_replace('/\b' . preg_quote($k, '/') . '\b/i', $v2, $s);
                    }
                    return $s;
                };

                $normalizeTwoDigitYear = function($s) {
                    $s = trim((string)$s);
                    if ($s === '') return $s;
                    // replace last token if it's exactly two digits
                    $parts = preg_split('/([\s\/\-]+)/', $s, -1, PREG_SPLIT_DELIM_CAPTURE);
                    if (!$parts) return $s;
                    $lastIndex = null;
                    for ($i = count($parts)-1; $i >= 0; $i--) {
                        if (preg_match('/^[\s\/\-]+$/', $parts[$i])) continue;
                        $lastIndex = $i;
                        break;
                    }
                    if (!isset($lastIndex)) return $s;
                    $last = $parts[$lastIndex];
                    if (preg_match('/^\d{2}$/', $last)) {
                        $y = (int)$last;
                        $full = ($y <= 49) ? (2000 + $y) : (1900 + $y);
                        $parts[$lastIndex] = (string)$full;
                        return implode('', $parts);
                    }
                    return $s;
                };

                // apply normalizations
                $v = $normalizeIndoMonths($v);
                $v = $normalizeTwoDigitYear($v);

                // try common formats
                $formats = ['d M y','d M Y','d/m/Y','d-m-Y','Y-m-d','j M Y','d F Y','d M Y'];
                foreach ($formats as $f) {
                    try {
                        $dt = \Carbon\Carbon::createFromFormat($f, $v);
                        if ($dt) return $dt->toDateString();
                    } catch (\Exception $e) {
                        // continue
                    }
                }
                try {
                    return \Carbon\Carbon::parse($v)->toDateString();
                } catch (\Exception $e) {
                    return null;
                }
            };

            // helper to compute periode (1-based) from tanggal_awal and cap by tanggal_akhir if present
            $computePeriode = function($tanggal_awal, $tanggal_akhir = null) {
                if (empty($tanggal_awal)) return 1;
                try {
                    $start = \Carbon\Carbon::parse($tanggal_awal)->startOfDay();
                } catch (\Exception $e) {
                    return 1;
                }
                $now = \Carbon\Carbon::now()->startOfDay();

                // if start is in the future, we're in periode 1
                if ($now->lt($start)) {
                    $periode = 1;
                } else {
                    $months = $start->diffInMonths($now);
                    $periode = $months + 1; // periode is 1-based
                }

                // if there's an end date, cap the periode so it doesn't go beyond the container's last period
                if (!empty($tanggal_akhir)) {
                    try {
                        $end = \Carbon\Carbon::parse($tanggal_akhir)->startOfDay();
                        // if end is before start, keep periode at 1
                        if ($end->lt($start)) {
                            $max = 1;
                        } else {
                            $max = $start->diffInMonths($end) + 1;
                        }
                        if ($periode > $max) $periode = $max;
                    } catch (\Exception $e) {
                        // ignore and keep computed periode
                    }
                }

                return (int) max(1, $periode);
            };

            while (($row = fgetcsv($FH, 0, ';')) !== false) {
                // skip empty rows
                if (count(array_filter($row, fn($c)=>trim((string)$c) !== '')) === 0) continue;

                $vendor = $vendorIdx !== null ? ($row[$vendorIdx] ?? null) : null;
                $nomor = $nomorIdx !== null ? ($row[$nomorIdx] ?? null) : null;
                $size = $sizeIdx !== null ? ($row[$sizeIdx] ?? null) : null;

                if (empty($vendor) || empty($nomor)) {
                    // skip invalid rows
                    continue;
                }
                // build data using available columns
                $tanggal_awal = $tanggalAwalIdx !== null ? $parseDate($row[$tanggalAwalIdx] ?? '') : null;
                $tanggal_akhir = $tanggalAkhirIdx !== null ? $parseDate($row[$tanggalAkhirIdx] ?? '') : null;

                $dppVal = $dppIdx !== null ? $parseNumber($row[$dppIdx] ?? '') : 0;
                $dppNilaiLainVal = $dppNilaiLainIdx !== null ? $parseNumber($row[$dppNilaiLainIdx] ?? '') : null;
                $ppnVal = $ppnIdx !== null ? $parseNumber($row[$ppnIdx] ?? '') : null;
                $pphVal = $pphIdx !== null ? $parseNumber($row[$pphIdx] ?? '') : null;
                $grandTotalVal = $grandTotalIdx !== null ? $parseNumber($row[$grandTotalIdx] ?? '') : null;

                // determine group: prefer CSV value; otherwise use vendor-specific prefix without date
                if ($groupIdx !== null && !empty($row[$groupIdx])) {
                    $groupVal = trim($row[$groupIdx]);
                } else {
                    $vendorKey = strtoupper(trim($vendor));
                    if ($vendorKey === 'ZONA') {
                        $groupVal = 'Z001';
                    } elseif ($vendorKey === 'DPE') {
                        $groupVal = 'D002';
                    } else {
                        $groupVal = preg_replace('/\s+/', '', $vendorKey);
                    }
                }

                $data = [
                    'vendor' => trim($vendor),
                    'nomor_kontainer' => trim($nomor),
                    'size' => $size ? trim($size) : null,
                    'group' => $groupVal,
                    'tanggal_awal' => $tanggal_awal ?? now()->toDateString(),
                    'tanggal_akhir' => $tanggal_akhir,
                    // prefer explicit periode column if present and non-empty, otherwise compute
                    'periode' => ($periodeIdx !== null && trim((string)($row[$periodeIdx] ?? '')) !== '') ? (int)trim($row[$periodeIdx]) : $computePeriode($tanggal_awal, $tanggal_akhir),
                    // accept masa as string (e.g. '21 januari 2025 - 20 februari 2025') if provided
                    'masa' => $masaIdx !== null ? trim((string)($row[$masaIdx] ?? '')) : null,
                    'tarif' => $tarifIdx !== null ? trim($row[$tarifIdx] ?? 'Bulanan') : 'Bulanan',
                    'dpp' => $dppVal,
                    'dpp_nilai_lain' => $dppNilaiLainVal ?? round((float)$dppVal * 11 / 12, 2),
                    'ppn' => $ppnVal ?? round((float)($dppNilaiLainVal ?? round((float)$dppVal * 11 / 12, 2)) * 0.12, 2),
                    'pph' => $pphVal ?? round((float)$dppVal * 0.02, 2),
                    'grand_total' => $grandTotalVal ?? round((float)$dppVal + (float)($ppnVal ?? round((float)($dppNilaiLainVal ?? round((float)$dppVal * 11 / 12, 2)) * 0.12, 2)) - (float)($pphVal ?? round((float)$dppVal * 0.02, 2)), 2),
                    'status' => $statusIdx !== null ? trim($row[$statusIdx] ?? 'Tersedia') : 'Tersedia',
                ];

                // Compute monetary defaults if dpp present (not in minimal template)
                if (!empty($data['dpp'])) {
                    $data['dpp_nilai_lain'] = round((float)$data['dpp'] * 11 / 12, 2);
                    $data['ppn'] = round((float)$data['dpp_nilai_lain'] * 0.12, 2);
                    $data['pph'] = round((float)$data['dpp'] * 0.02, 2);
                    $data['grand_total'] = round((float)$data['dpp'] + $data['ppn'] - $data['pph'], 2);
                }

                // Decide creating single row or expanding into monthly periods up to the end date
                $computedStart = (int)($data['periode'] ?? 1);
                if (!is_null($startPeriodOverride) && $startPeriodOverride > 0) {
                    $computedStart = $startPeriodOverride;
                }

                // compute maximum period based on tanggal_akhir (if present) or computePeriode using now
                $maxPeriod = $computePeriode($data['tanggal_awal'], $data['tanggal_akhir']);
                if ($maxPeriod < $computedStart) $maxPeriod = $computedStart;

                // Expand into per-period rows when either createAll flag set, or the computed maxPeriod indicates multiple periods
                if ($createAll || $maxPeriod > $computedStart) {
                    for ($p = $computedStart; $p <= $maxPeriod; $p++) {
                        // compute period-specific start/end preserving day-of-month
                        try {
                            $baseStart = \Carbon\Carbon::parse($data['tanggal_awal'])->startOfDay();
                        } catch (\Exception $e) {
                            $baseStart = \Carbon\Carbon::now()->startOfDay();
                        }
                        // addMonthsNoOverflow preserves day-of-month where possible
                        $periodStart = $baseStart->copy()->addMonthsNoOverflow($p - 1);
                        // period end = periodStart +1 month -1 day, cap by tanggal_akhir
                        $periodEnd = $periodStart->copy()->addMonthsNoOverflow(1)->subDay();
                        if (!empty($data['tanggal_akhir'])) {
                            try {
                                $endCap = \Carbon\Carbon::parse($data['tanggal_akhir'])->startOfDay();
                                if ($periodEnd->gt($endCap)) $periodEnd = $endCap;
                            } catch (\Exception $e) {
                                // ignore
                            }
                        }

                        $daysInPeriod = $periodStart->diffInDays($periodEnd) + 1;
                        $daysInFullMonth = $periodStart->daysInMonth;

                        // determine tarif label (Bulanan if covers full month and pricelist not explicitly harian)
                        $tarifLabel = ($daysInPeriod >= $daysInFullMonth) ? 'Bulanan' : 'Harian';

                        // Pricelist lookup with fallbacks (vendor+size with date, then size-only with date, then ignore-dates vendor+size latest, then size-only latest)
                        $pr = MasterPricelistSewaKontainer::where('ukuran_kontainer', $data['size'])
                                ->where('vendor', $data['vendor'])
                                ->where(function($q) use ($periodStart) {
                                    $q->where('tanggal_harga_awal', '<=', $periodStart->toDateString())
                                      ->where(function($q2) use ($periodStart){
                                          $q2->whereNull('tanggal_harga_akhir')->orWhere('tanggal_harga_akhir', '>=', $periodStart->toDateString());
                                      });
                                })
                                ->orderBy('tanggal_harga_awal', 'desc')
                                ->first();

                        if (!$pr) {
                            $pr = MasterPricelistSewaKontainer::where('ukuran_kontainer', $data['size'])
                                    ->where(function($q) use ($periodStart) {
                                        $q->where('tanggal_harga_awal', '<=', $periodStart->toDateString())
                                          ->where(function($q2) use ($periodStart){
                                              $q2->whereNull('tanggal_harga_akhir')->orWhere('tanggal_harga_akhir', '>=', $periodStart->toDateString());
                                          });
                                    })
                                    ->orderBy('tanggal_harga_awal', 'desc')
                                    ->first();
                        }

                        if (!$pr) {
                            // ignore dates fallback: latest vendor+size
                            $pr = MasterPricelistSewaKontainer::where('ukuran_kontainer', $data['size'])
                                    ->where('vendor', $data['vendor'])
                                    ->orderBy('tanggal_harga_awal', 'desc')
                                    ->first();
                        }
                        if (!$pr) {
                            // size-only latest
                            $pr = MasterPricelistSewaKontainer::where('ukuran_kontainer', $data['size'])
                                    ->orderBy('tanggal_harga_awal', 'desc')
                                    ->first();
                        }

                        $dppComputed = (float)($data['dpp'] ?? 0);
                        $computedTarifFromPricelist = $tarifLabel;
                        if ($pr) {
                            $harga = (float) $pr->harga;
                            $prTarif = strtolower((string)$pr->tarif);
                            if (strpos($prTarif, 'harian') !== false) {
                                // pricelist is daily price
                                $dppComputed = round($harga * $daysInPeriod, 2);
                                $computedTarifFromPricelist = 'Harian';
                            } else {
                                // pricelist is monthly price; if partial month, prorate by days
                                if ($daysInPeriod >= $daysInFullMonth) {
                                    $dppComputed = round($harga, 2);
                                    $computedTarifFromPricelist = 'Bulanan';
                                } else {
                                    $dppComputed = round($harga * ($daysInPeriod / $daysInFullMonth), 2);
                                    $computedTarifFromPricelist = 'Harian';
                                }
                            }
                        }

                        $rowData = array_merge($data, [
                            'periode' => $p,
                            'tanggal_awal' => $periodStart->toDateString(),
                            'tanggal_akhir' => $periodEnd->toDateString(),
                            'masa' => strtolower($periodStart->locale('id')->isoFormat('D MMMM YYYY')) . ' - ' . strtolower($periodEnd->locale('id')->isoFormat('D MMMM YYYY')),
                            'tarif' => $computedTarifFromPricelist,
                            'dpp' => $dppComputed,
                            'dpp_nilai_lain' => round($dppComputed * 11 / 12, 2),
                            'ppn' => round((round($dppComputed * 11 / 12, 2)) * 0.12, 2),
                            'pph' => round($dppComputed * 0.02, 2),
                            'grand_total' => round($dppComputed + round((round($dppComputed * 11 / 12, 2)) * 0.12, 2) - round($dppComputed * 0.02, 2), 2),
                        ]);

                        $attrs = [
                            'vendor' => $data['vendor'],
                            'nomor_kontainer' => $data['nomor_kontainer'],
                            'tanggal_awal' => $rowData['tanggal_awal'],
                            'periode' => $p,
                        ];

                        \App\Models\DaftarTagihanKontainerSewa::firstOrCreate($attrs, array_merge($attrs, $rowData));
                        $created++;
                    }
                } else {
                    // Idempotent create: avoid duplicates by vendor+nomor+tanggal_awal
                    // include periode in uniqueness attrs so imports for different periods create separate rows
                    // compute period start/end for this single periode and fill pricing similarly
                    try {
                        $baseStart = \Carbon\Carbon::parse($data['tanggal_awal'])->startOfDay();
                    } catch (\Exception $e) {
                        $baseStart = \Carbon\Carbon::now()->startOfDay();
                    }
                    $periodStart = $baseStart->copy()->addMonthsNoOverflow($computedStart - 1);
                    $periodEnd = $periodStart->copy()->addMonthsNoOverflow(1)->subDay();
                    if (!empty($data['tanggal_akhir'])) {
                        try {
                            $endCap = \Carbon\Carbon::parse($data['tanggal_akhir'])->startOfDay();
                            if ($periodEnd->gt($endCap)) $periodEnd = $endCap;
                        } catch (\Exception $e) {
                        }
                    }

                    $daysInPeriod = $periodStart->diffInDays($periodEnd) + 1;
                    $daysInFullMonth = $periodStart->daysInMonth;

                    $tarifLabel = ($daysInPeriod >= $daysInFullMonth) ? 'Bulanan' : 'Harian';

                    $pr = MasterPricelistSewaKontainer::where('ukuran_kontainer', $data['size'])
                                ->where('vendor', $data['vendor'])
                                ->where(function($q) use ($periodStart) {
                                    $q->where('tanggal_harga_awal', '<=', $periodStart->toDateString())
                                      ->where(function($q2) use ($periodStart){
                                          $q2->whereNull('tanggal_harga_akhir')->orWhere('tanggal_harga_akhir', '>=', $periodStart->toDateString());
                                      });
                                })
                                ->orderBy('tanggal_harga_awal', 'desc')
                                ->first();
                    if (!$pr) {
                        $pr = MasterPricelistSewaKontainer::where('ukuran_kontainer', $data['size'])
                                    ->where(function($q) use ($periodStart) {
                                        $q->where('tanggal_harga_awal', '<=', $periodStart->toDateString())
                                          ->where(function($q2) use ($periodStart){
                                              $q2->whereNull('tanggal_harga_akhir')->orWhere('tanggal_harga_akhir', '>=', $periodStart->toDateString());
                                          });
                                    })
                                    ->orderBy('tanggal_harga_awal', 'desc')
                                    ->first();
                    }
                    if (!$pr) {
                        $pr = MasterPricelistSewaKontainer::where('ukuran_kontainer', $data['size'])
                                    ->where('vendor', $data['vendor'])
                                    ->orderBy('tanggal_harga_awal', 'desc')
                                    ->first();
                    }
                    if (!$pr) {
                        $pr = MasterPricelistSewaKontainer::where('ukuran_kontainer', $data['size'])
                                    ->orderBy('tanggal_harga_awal', 'desc')
                                    ->first();
                    }

                    $dppComputed = (float)($data['dpp'] ?? 0);
                    $computedTarifFromPricelist = $tarifLabel;
                    if ($pr) {
                        $harga = (float) $pr->harga;
                        $prTarif = strtolower((string)$pr->tarif);
                        if (strpos($prTarif, 'harian') !== false) {
                            $dppComputed = round($harga * $daysInPeriod, 2);
                            $computedTarifFromPricelist = 'Harian';
                        } else {
                            if ($daysInPeriod >= $daysInFullMonth) {
                                $dppComputed = round($harga, 2);
                                $computedTarifFromPricelist = 'Bulanan';
                            } else {
                                $dppComputed = round($harga * ($daysInPeriod / $daysInFullMonth), 2);
                                $computedTarifFromPricelist = 'Harian';
                            }
                        }
                    }

                    $rowData = array_merge($data, [
                        'periode' => $computedStart,
                        'tanggal_awal' => $periodStart->toDateString(),
                        'tanggal_akhir' => $periodEnd->toDateString(),
                        'masa' => strtolower($periodStart->locale('id')->isoFormat('D MMMM YYYY')) . ' - ' . strtolower($periodEnd->locale('id')->isoFormat('D MMMM YYYY')),
                        'tarif' => $computedTarifFromPricelist,
                        'dpp' => $dppComputed,
                        'dpp_nilai_lain' => round($dppComputed * 11 / 12, 2),
                        'ppn' => round((round($dppComputed * 11 / 12, 2)) * 0.12, 2),
                        'pph' => round($dppComputed * 0.02, 2),
                        'grand_total' => round($dppComputed + round((round($dppComputed * 11 / 12, 2)) * 0.12, 2) - round($dppComputed * 0.02, 2), 2),
                    ]);

                    $attrs = [
                        'vendor' => $data['vendor'],
                        'nomor_kontainer' => $data['nomor_kontainer'],
                        'tanggal_awal' => $rowData['tanggal_awal'],
                        'periode' => $computedStart,
                    ];

                    \App\Models\DaftarTagihanKontainerSewa::firstOrCreate($attrs, array_merge($attrs, $rowData));
                    $created++;
                }
            }
            fclose($FH);
        }

        return redirect()->route('daftar-tagihan-kontainer-sewa.index')->with('success', "Import selesai. Baris dibuat: {$created}");
    }

    /**
     * Import CSV with automatic grouping based on vendor and start date
     * Groups follow TK1YYMMXXXXXXX format
     */
    public function importWithGrouping(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240'
        ]);

        try {
            $file = $request->file('csv_file');

            // Ensure imports directory exists
            $importsDir = storage_path('app/imports');
            if (!is_dir($importsDir)) {
                mkdir($importsDir, 0755, true);
            }

            // Generate unique filename
            $filename = 'import_' . time() . '_' . uniqid() . '.csv';
            $fullPath = $importsDir . DIRECTORY_SEPARATOR . $filename;

            // Try to move uploaded file directly
            if (!move_uploaded_file($file->getPathname(), $fullPath)) {
                // Fallback: use Laravel's storage method
                $path = $file->storeAs('imports', $filename);
                $fullPath = storage_path('app/' . $path);

                // Final verification
                if (!file_exists($fullPath)) {
                    throw new \Exception('Failed to store uploaded file. Please check storage permissions.');
                }
            }

            // Read and parse CSV
            $csvData = [];
            if (($handle = fopen($fullPath, 'r')) !== false) {
                $header = fgetcsv($handle, 0, ';'); // Using semicolon delimiter
                if (!$header) {
                    fclose($handle);
                    throw new \Exception('Invalid CSV format');
                }

                while (($row = fgetcsv($handle, 0, ';')) !== false) {
                    if (count($row) === count($header)) {
                        $csvData[] = array_combine($header, $row);
                    }
                }
                fclose($handle);
            }

            if (empty($csvData)) {
                throw new \Exception('No valid data found in CSV');
            }

            // Group data by vendor and start date (same logic as in index method)
            $groups = [];
            foreach ($csvData as $row) {
                $vendor = trim($row['vendor'] ?? '');
                $tanggalAwal = trim($row['tanggal_awal'] ?? '');

                // Create unique group key: vendor + date
                $groupKey = $vendor . '_' . $tanggalAwal;

                if (!isset($groups[$groupKey])) {
                    $groups[$groupKey] = [
                        'vendor' => $vendor,
                        'tanggal_awal' => $tanggalAwal,
                        'items' => []
                    ];
                }

                $groups[$groupKey]['items'][] = $row;
            }

            // Generate group codes and process data
            $groupCounter = 1;
            $created = [];
            $model = config('models.daftar_tagihan_kontainer_sewa', \App\Models\DaftarTagihanKontainerSewa::class);

            foreach ($groups as $groupKey => $groupData) {
                $tanggalAwal = $groupData['tanggal_awal'];

                // Generate group code: TK1YYMMXXXXXXX
                try {
                    $date = \Carbon\Carbon::parse($tanggalAwal);
                    $year = $date->format('y'); // 2-digit year
                    $month = $date->format('m'); // 2-digit month
                } catch (\Exception $e) {
                    // Fallback to current date if parsing fails
                    $year = date('y');
                    $month = date('m');
                }

                $runningNumber = str_pad($groupCounter, 7, '0', STR_PAD_LEFT);
                $groupCode = 'TK1' . $year . $month . $runningNumber;

                // Process all items in this group
                foreach ($groupData['items'] as $row) {
                    $parseNumber = function($v) {
                        $v = trim((string)($v ?? ''));
                        if ($v === '') return 0;
                        $clean = preg_replace('/[^\d.,\-]/', '', $v);
                        if (strpos($clean, ',') !== false && strpos($clean, '.') === false) {
                            $clean = str_replace(',', '.', $clean);
                        } else {
                            $clean = str_replace(',', '', $clean);
                        }
                        return (float)$clean;
                    };

                    $parseDate = function($v) {
                        $v = trim((string)($v ?? ''));
                        if ($v === '') return null;
                        try {
                            return \Carbon\Carbon::parse($v)->toDateString();
                        } catch (\Exception $e) {
                            return null;
                        }
                    };

                    $tanggalAwal = $parseDate($row['tanggal_awal'] ?? '');
                    $tanggalAkhir = $parseDate($row['tanggal_akhir'] ?? '');
                    $dppVal = $parseNumber($row['dpp'] ?? '');

                    $data = [
                        'vendor' => trim($row['vendor'] ?? ''),
                        'nomor_kontainer' => trim($row['nomor_kontainer'] ?? ''),
                        'size' => trim($row['size'] ?? ''),
                        'group' => $groupCode, // Use generated group code
                        'tanggal_awal' => $tanggalAwal ?? now()->toDateString(),
                        'tanggal_akhir' => $tanggalAkhir,
                        'periode' => (int)($row['periode'] ?? 1),
                        'masa' => trim($row['masa'] ?? ''),
                        'tarif' => trim($row['tarif'] ?? 'Bulanan'),
                        'dpp' => $dppVal,
                        'dpp_nilai_lain' => $parseNumber($row['dpp_nilai_lain'] ?? '') ?: round($dppVal * 11 / 12, 2),
                        'ppn' => $parseNumber($row['ppn'] ?? '') ?: round(($parseNumber($row['dpp_nilai_lain'] ?? '') ?: round($dppVal * 11 / 12, 2)) * 0.12, 2),
                        'pph' => $parseNumber($row['pph'] ?? '') ?: round($dppVal * 0.02, 2),
                        'grand_total' => $parseNumber($row['grand_total'] ?? '') ?: 0,
                        'status' => trim($row['status'] ?? 'Tersedia'),
                    ];

                    // Calculate grand total if not provided
                    if (!$data['grand_total']) {
                        $data['grand_total'] = round($data['dpp'] + $data['ppn'] - $data['pph'], 2);
                    }

                    $created[] = $model::create($data);
                }

                $groupCounter++;
            }

            // Clean up uploaded file
            try {
                if (isset($fullPath) && file_exists($fullPath)) {
                    unlink($fullPath);
                }
            } catch (\Exception $cleanupError) {
                // Log cleanup error but don't fail the import
                \Illuminate\Support\Facades\Log::warning('Failed to cleanup temporary import file: ' . $cleanupError->getMessage());
            }

            return redirect()->route('daftar-tagihan-kontainer-sewa.index')
                ->with('success', "Successfully imported " . count($created) . " records in " . count($groups) . " groups");

        } catch (\Exception $e) {
            // Clean up file on error
            try {
                if (isset($fullPath) && file_exists($fullPath)) {
                    unlink($fullPath);
                }
            } catch (\Exception $cleanupError) {
                // Ignore cleanup errors on exception
            }

            return redirect()->route('daftar-tagihan-kontainer-sewa.index')
                ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('daftar-tagihan-kontainer-sewa.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $this->validateData($request);

        // Fill missing numeric fields with 0 to avoid null numeric issues
        foreach (['tarif','dpp','ppn','pph','grand_total'] as $n) {
            if (!isset($data[$n]) || $data[$n] === null || $data[$n] === '') {
                $data[$n] = 0;
            }
        }

        // Compute dpp_nilai_lain from dpp if not provided explicitly
        if (!isset($data['dpp_nilai_lain']) || $data['dpp_nilai_lain'] === null || $data['dpp_nilai_lain'] === '') {
            $data['dpp_nilai_lain'] = round((float)($data['dpp'] ?? 0) * 11 / 12, 2);
        }

        // Compute ppn from dpp_nilai_lain if not provided
        if (!isset($data['ppn']) || $data['ppn'] === null || $data['ppn'] === '') {
            $data['ppn'] = round((float)($data['dpp_nilai_lain'] ?? 0) * 0.12, 2);
        }

        // Compute pph from dpp (2%) if not provided explicitly
        if (!isset($data['pph']) || $data['pph'] === null || $data['pph'] === '') {
            $data['pph'] = round((float)($data['dpp'] ?? 0) * 0.02, 2);
        }

        // Compute grand_total from components if not provided explicitly
        if (!isset($data['grand_total']) || $data['grand_total'] === null || $data['grand_total'] === '') {
            $data['grand_total'] = round((float)($data['dpp'] ?? 0) + (float)($data['ppn'] ?? 0) - (float)($data['pph'] ?? 0), 2);
        }

        DaftarTagihanKontainerSewa::create($data);

        return redirect()->route('daftar-tagihan-kontainer-sewa.index')->with('success', 'Tagihan kontainer berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DaftarTagihanKontainerSewa $daftarTagihanKontainerSewa)
    {
        $item = $daftarTagihanKontainerSewa;
        return view('daftar-tagihan-kontainer-sewa.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DaftarTagihanKontainerSewa $daftarTagihanKontainerSewa)
    {
        $item = $daftarTagihanKontainerSewa;
        return view('daftar-tagihan-kontainer-sewa.edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DaftarTagihanKontainerSewa $daftarTagihanKontainerSewa)
    {
        $data = $this->validateData($request);

        foreach (['tarif','dpp','ppn','pph','grand_total'] as $n) {
            if (!isset($data[$n]) || $data[$n] === null || $data[$n] === '') {
                $data[$n] = 0;
            }
        }

        if (!isset($data['dpp_nilai_lain']) || $data['dpp_nilai_lain'] === null || $data['dpp_nilai_lain'] === '') {
            $data['dpp_nilai_lain'] = round((float)($data['dpp'] ?? 0) * 11 / 12, 2);
        }

        if (!isset($data['ppn']) || $data['ppn'] === null || $data['ppn'] === '') {
            $data['ppn'] = round((float)($data['dpp_nilai_lain'] ?? 0) * 0.12, 2);
        }

        if (!isset($data['pph']) || $data['pph'] === null || $data['pph'] === '') {
            $data['pph'] = round((float)($data['dpp'] ?? 0) * 0.02, 2);
        }

        if (!isset($data['grand_total']) || $data['grand_total'] === null || $data['grand_total'] === '') {
            $data['grand_total'] = round((float)($data['dpp'] ?? 0) + (float)($data['ppn'] ?? 0) - (float)($data['pph'] ?? 0), 2);
        }

        $daftarTagihanKontainerSewa->update($data);

        return redirect()->route('daftar-tagihan-kontainer-sewa.index')->with('success', 'Tagihan kontainer berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DaftarTagihanKontainerSewa $daftarTagihanKontainerSewa)
    {
        $daftarTagihanKontainerSewa->delete();
        return redirect()->route('daftar-tagihan-kontainer-sewa.index')->with('success', 'Tagihan kontainer berhasil dihapus.');
    }

    /**
     * Validate incoming request data.
     */
    protected function validateData(Request $request): array
    {
        return $request->validate([
            'vendor' => 'required|string|max:255',
            'nomor_kontainer' => 'required|string|max:100',
            'size' => 'nullable|string|max:50',
            'group' => 'nullable|string|max:100',
            'tanggal_awal' => 'required|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_awal',
            'periode' => 'nullable|integer|min:1',
            'masa' => 'nullable|string|max:255',
            'tarif' => 'nullable|numeric',
            'dpp' => 'nullable|numeric',
            'dpp_nilai_lain' => 'nullable|numeric',
            'ppn' => 'nullable|numeric',
            'pph' => 'nullable|numeric',
            'grand_total' => 'nullable|numeric',
            'status' => 'nullable|string|max:50',
        ]);
    }

    /**
     * Update adjustment value for specific tagihan.
     */
    public function updateAdjustment(Request $request, $id)
    {
        // Validate the adjustment value
        $request->validate([
            'adjustment' => 'required|numeric|between:-999999999.99,999999999.99',
        ]);

        try {
            $tagihan = DaftarTagihanKontainerSewa::findOrFail($id);

            // Store old values for logging
            $oldAdjustment = $tagihan->adjustment ?? 0;
            $newAdjustment = $request->input('adjustment');

            // Update the adjustment
            $tagihan->adjustment = $newAdjustment;

            // Recalculate related values based on adjusted DPP
            $originalDpp = (float)($tagihan->dpp ?? 0);
            $adjustedDpp = $originalDpp + $newAdjustment;

            // Recalculate PPN (11%)
            $ppnRate = 0.11;
            $tagihan->ppn = $adjustedDpp * $ppnRate;

            // Recalculate PPH (2% - adjust rate as needed)
            $pphRate = 0.02;
            $tagihan->pph = $adjustedDpp * $pphRate;

            // Recalculate Grand Total: DPP + PPN - PPH (tanpa DPP Nilai Lain)
            $tagihan->grand_total = $adjustedDpp + $tagihan->ppn - $tagihan->pph;

            $tagihan->save();

            // Log the change for audit purposes
            Log::info("Adjustment updated for tagihan ID {$id}", [
                'container' => $tagihan->nomor_kontainer,
                'old_adjustment' => $oldAdjustment,
                'new_adjustment' => $newAdjustment,
                'adjusted_dpp' => $adjustedDpp,
                'new_ppn' => $tagihan->ppn,
                'new_pph' => $tagihan->pph,
                'new_grand_total' => $tagihan->grand_total,
                'user_id' => Auth::id(),
                'timestamp' => now()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Adjustment berhasil diperbarui dan nilai terkait telah dihitung ulang',
                    'data' => [
                        'id' => $tagihan->id,
                        'adjustment' => $tagihan->adjustment,
                        'adjusted_dpp' => $adjustedDpp,
                        'ppn' => $tagihan->ppn,
                        'pph' => $tagihan->pph,
                        'grand_total' => $tagihan->grand_total,
                        'formatted_adjustment' => 'Rp ' . number_format((float)$tagihan->adjustment, 0, '.', ','),
                        'formatted_ppn' => 'Rp ' . number_format((float)$tagihan->ppn, 0, '.', ','),
                        'formatted_pph' => 'Rp ' . number_format((float)$tagihan->pph, 0, '.', ','),
                        'formatted_grand_total' => 'Rp ' . number_format((float)$tagihan->grand_total, 0, '.', ','),
                    ]
                ]);
            }

            return redirect()->back()->with('success', 'Adjustment berhasil diperbarui dan nilai terkait telah dihitung ulang');

        } catch (\Exception $e) {
            Log::error("Failed to update adjustment for tagihan ID {$id}", [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'timestamp' => now()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui adjustment: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal memperbarui adjustment: ' . $e->getMessage());
        }
    }
}
