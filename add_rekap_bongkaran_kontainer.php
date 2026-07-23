<?php

$file = 'app/Http/Controllers/BlController.php';
$content = file_get_contents($file);

$selectMethod = <<<PHP
    /**
     * Show the selection form for Rekap Bongkaran Kontainer.
     */
    public function rekapBongkaranKontainerSelect()
    {
        \$masterKapals = \App\Models\MasterKapal::orderBy('nama_kapal')->get();
        return view('bl.rekap_bongkaran_kontainer_select', compact('masterKapals'));
    }

PHP;

$rekapMethod = <<<PHP
    /**
     * Show the Rekap Bongkaran Kontainer report.
     */
    public function rekapBongkaranKontainer(Request \$request)
    {
        \$namaKapal = \$request->get('nama_kapal');
        \$noVoyage = \$request->get('no_voyage');
        \$estimasiTibaInput = \$request->get('estimasi_tiba');
        \$tanggalBerangkatInput = \$request->get('tanggal_berangkat');
        \$jenisTanggal = \$request->get('jenis_tanggal', 'estimasi_tiba');

        if (! \$namaKapal || ! \$noVoyage) {
            return redirect()->route('bl.rekap-bongkaran-kontainer.select')->with('error', 'Silakan pilih kapal dan voyage terlebih dahulu');
        }

        \$kapalClean = preg_replace('/^KM\.?\s*/i', '', \$namaKapal);

        if (\$request->filled('estimasi_tiba')) {
            \App\Models\Manifest::where(function (\$query) use (\$namaKapal, \$kapalClean) {
                \$query->where('nama_kapal', \$namaKapal)
                    ->orWhere('nama_kapal', 'like', '%'.\$kapalClean.'%');
            })
                ->where('no_voyage', \$noVoyage)
                ->update(['estimasi_tiba' => \$estimasiTibaInput]);
        }

        if (\$request->filled('tanggal_berangkat')) {
            \App\Models\Manifest::where(function (\$query) use (\$namaKapal, \$kapalClean) {
                \$query->where('nama_kapal', \$namaKapal)
                    ->orWhere('nama_kapal', 'like', '%'.\$kapalClean.'%');
            })
                ->where('no_voyage', \$noVoyage)
                ->update(['tanggal_berangkat' => \$tanggalBerangkatInput]);
        }

        \$bls = \App\Models\Manifest::where(function (\$query) use (\$namaKapal, \$kapalClean) {
            \$query->where('nama_kapal', \$namaKapal)
                ->orWhere('nama_kapal', 'like', '%'.\$kapalClean.'%');
        })
            ->where('no_voyage', \$noVoyage)
            ->get();

        \$pergerakan = \App\Models\PergerakanKapal::where(function (\$q) use (\$namaKapal, \$kapalClean) {
            \$q->where('nama_kapal', \$namaKapal)
                ->orWhere('nama_kapal', 'like', '%'.\$kapalClean.'%');
        })
            ->where('voyage', \$noVoyage)
            ->first();

        \$estTiba = '-';
        \$labelTanggal = 'ESTIMASI TIBA';

        if (\$jenisTanggal === 'tanggal_berangkat') {
            \$labelTanggal = 'TANGGAL BERANGKAT';
            if (\$bls->count() > 0 && \$bls->first()->tanggal_berangkat) {
                \$estTiba = \Carbon\Carbon::parse(\$bls->first()->tanggal_berangkat)->translatedFormat('d F Y');
            } elseif (\$pergerakan && \$pergerakan->tanggal_berangkat) {
                \$estTiba = \Carbon\Carbon::parse(\$pergerakan->tanggal_berangkat)->translatedFormat('d F Y');
            }
        } else {
            if (\$bls->count() > 0 && \$bls->first()->estimasi_tiba) {
                \$estTiba = \Carbon\Carbon::parse(\$bls->first()->estimasi_tiba)->translatedFormat('d F Y');
            } elseif (\$pergerakan && \$pergerakan->tanggal_sandar) {
                \$estTiba = \$pergerakan->tanggal_sandar->translatedFormat('d F Y');
            } elseif (\$bls->count() > 0) {
                \$firstBl = \$bls->first();
                if (\$firstBl->tanggal_berangkat) {
                    \$estTiba = \Carbon\Carbon::parse(\$firstBl->tanggal_berangkat)->translatedFormat('d F Y');
                }
            }
        }

        \$dari = '-';
        if (\$pergerakan && \$pergerakan->tujuan_asal) {
            \$dari = \$pergerakan->tujuan_asal;
        } elseif (\$bls->count() > 0) {
            \$dari = \$bls->first()->pelabuhan_asal ?: '-';
        }

        \$items = \$bls->groupBy(function (\$item) {
            \$isCargo = (\$item->tipe_kontainer === 'CARGO' || empty(\$item->size_kontainer));
            if (\$isCargo) {
                return 'cargo';
            } else {
                \$barangUpper = strtoupper(\$item->nama_barang ?? '');
                \$isEmpty = str_contains(\$barangUpper, 'EMPTY') || (\$item->tipe_kontainer == 'FCL' && (empty(\$item->nomor_kontainer) || str_starts_with(\$item->nomor_kontainer, 'CARGO-')));
                \$size = trim(str_ireplace(['ft', 'feet', ' '], '', \$item->size_kontainer ?? ''));
                if (empty(\$size)) \$size = '20';
                \$status = \$isEmpty ? 'empty' : 'full';
                return 'container|'.\$size.'|'.\$status;
            }
        })->map(function (\$group, \$key) {
            if (\$key === 'cargo') return null; // We will filter this out

            \$parts = explode('|', \$key);
            \$size = \$parts[1];
            \$status = \$parts[2];

            if (\$status === 'empty') {
                \$totalKuantitas = \$group->count();
            } else {
                \$uniqueContainers = \$group->whereNotNull('nomor_kontainer')->where('nomor_kontainer', '!=', '')->pluck('nomor_kontainer')->unique()->count();
                \$emptyContainers = \$group->filter(function (\$item) {
                    return empty(\$item->nomor_kontainer) || \$item->nomor_kontainer === '-';
                })->count();
                \$totalKuantitas = \$uniqueContainers + \$emptyContainers;
            }

            return [
                'kuantitas' => \$totalKuantitas,
                'satuan' => 'Unit',
                'nama_barang' => (\$status === 'empty') ? "Container Kosong {\$size} feet" : "Container Full {\$size} feet",
                'amount' => null,
                'unit' => '',
            ];
        })->filter()->values();

        \$totalAmount = 0; // Container doesn't have amount

        return view('bl.rekap_bongkaran_kontainer', compact('namaKapal', 'noVoyage', 'dari', 'estTiba', 'labelTanggal', 'items', 'totalAmount'));
    }

PHP;

$printMethod = <<<PHP
    /**
     * Show the Rekap Bongkaran Kontainer report for print.
     */
    public function rekapBongkaranKontainerPrint(Request \$request)
    {
        \$namaKapal = \$request->get('nama_kapal');
        \$noVoyage = \$request->get('no_voyage');
        \$estimasiTibaInput = \$request->get('estimasi_tiba');
        \$tanggalBerangkatInput = \$request->get('tanggal_berangkat');
        \$jenisTanggal = \$request->get('jenis_tanggal', 'estimasi_tiba');

        if (! \$namaKapal || ! \$noVoyage) {
            return redirect()->route('bl.rekap-bongkaran-kontainer.select')->with('error', 'Silakan pilih kapal dan voyage terlebih dahulu');
        }

        \$kapalClean = preg_replace('/^KM\.?\s*/i', '', \$namaKapal);

        \$bls = \App\Models\Manifest::where(function (\$query) use (\$namaKapal, \$kapalClean) {
            \$query->where('nama_kapal', \$namaKapal)
                ->orWhere('nama_kapal', 'like', '%'.\$kapalClean.'%');
        })
            ->where('no_voyage', \$noVoyage)
            ->get();

        \$pergerakan = \App\Models\PergerakanKapal::where(function (\$q) use (\$namaKapal, \$kapalClean) {
            \$q->where('nama_kapal', \$namaKapal)
                ->orWhere('nama_kapal', 'like', '%'.\$kapalClean.'%');
        })
            ->where('voyage', \$noVoyage)
            ->first();

        \$estTiba = '-';
        \$labelTanggal = 'ESTIMASI TIBA';

        if (\$jenisTanggal === 'tanggal_berangkat') {
            \$labelTanggal = 'TANGGAL BERANGKAT';
            if (\$bls->count() > 0 && \$bls->first()->tanggal_berangkat) {
                \$estTiba = \Carbon\Carbon::parse(\$bls->first()->tanggal_berangkat)->translatedFormat('d F Y');
            } elseif (\$pergerakan && \$pergerakan->tanggal_berangkat) {
                \$estTiba = \Carbon\Carbon::parse(\$pergerakan->tanggal_berangkat)->translatedFormat('d F Y');
            }
        } else {
            if (\$bls->count() > 0 && \$bls->first()->estimasi_tiba) {
                \$estTiba = \Carbon\Carbon::parse(\$bls->first()->estimasi_tiba)->translatedFormat('d F Y');
            } elseif (\$pergerakan && \$pergerakan->tanggal_sandar) {
                \$estTiba = \$pergerakan->tanggal_sandar->translatedFormat('d F Y');
            } elseif (\$bls->count() > 0) {
                \$firstBl = \$bls->first();
                if (\$firstBl->tanggal_berangkat) {
                    \$estTiba = \Carbon\Carbon::parse(\$firstBl->tanggal_berangkat)->translatedFormat('d F Y');
                }
            }
        }

        \$dari = '-';
        if (\$pergerakan && \$pergerakan->tujuan_asal) {
            \$dari = \$pergerakan->tujuan_asal;
        } elseif (\$bls->count() > 0) {
            \$dari = \$bls->first()->pelabuhan_asal ?: '-';
        }

        \$items = \$bls->groupBy(function (\$item) {
            \$isCargo = (\$item->tipe_kontainer === 'CARGO' || empty(\$item->size_kontainer));
            if (\$isCargo) {
                return 'cargo';
            } else {
                \$barangUpper = strtoupper(\$item->nama_barang ?? '');
                \$isEmpty = str_contains(\$barangUpper, 'EMPTY') || (\$item->tipe_kontainer == 'FCL' && (empty(\$item->nomor_kontainer) || str_starts_with(\$item->nomor_kontainer, 'CARGO-')));
                \$size = trim(str_ireplace(['ft', 'feet', ' '], '', \$item->size_kontainer ?? ''));
                if (empty(\$size)) \$size = '20';
                \$status = \$isEmpty ? 'empty' : 'full';
                return 'container|'.\$size.'|'.\$status;
            }
        })->map(function (\$group, \$key) {
            if (\$key === 'cargo') return null; // We will filter this out

            \$parts = explode('|', \$key);
            \$size = \$parts[1];
            \$status = \$parts[2];

            if (\$status === 'empty') {
                \$totalKuantitas = \$group->count();
            } else {
                \$uniqueContainers = \$group->whereNotNull('nomor_kontainer')->where('nomor_kontainer', '!=', '')->pluck('nomor_kontainer')->unique()->count();
                \$emptyContainers = \$group->filter(function (\$item) {
                    return empty(\$item->nomor_kontainer) || \$item->nomor_kontainer === '-';
                })->count();
                \$totalKuantitas = \$uniqueContainers + \$emptyContainers;
            }

            return [
                'kuantitas' => \$totalKuantitas,
                'satuan' => 'Unit',
                'nama_barang' => (\$status === 'empty') ? "Container Kosong {\$size} feet" : "Container Full {\$size} feet",
                'amount' => null,
                'unit' => '',
            ];
        })->filter()->values();

        \$totalAmount = 0; // Container doesn't have amount

        return view('bl.rekap_bongkaran_kontainer_print', compact('namaKapal', 'noVoyage', 'dari', 'estTiba', 'labelTanggal', 'items', 'totalAmount'));
    }

PHP;

$insertIndex = strrpos($content, '}'); // Find the closing brace of the class
if ($insertIndex !== false) {
    $content = substr($content, 0, $insertIndex) . $selectMethod . "\n" . $rekapMethod . "\n" . $printMethod . "\n" . "}\n";
    file_put_contents($file, $content);
    echo "Added rekapBongkaranKontainer methods successfully.";
} else {
    echo "Failed to find the class closing brace.";
}
