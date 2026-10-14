<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Bl;
use App\Models\Manifest;
use App\Models\NaikKapal;
use App\Models\Prospek;
use App\Models\SuratJalan;
use App\Models\SuratJalanBongkaranBatam;
use App\Models\SuratJalanBongkaran;
use App\Models\TandaTerima;
use App\Models\TandaTerimaLcl;
use App\Models\TandaTerimaTanpaSuratJalan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TrackingTransaksiController extends Controller
{
    private const TYPES = [
        'surat-jalan' => [
            'label' => 'Surat Jalan', 'number_label' => 'Nomor Surat Jalan',
            'columns' => ['no_surat_jalan'], 'model' => SuratJalan::class,
            'milestones' => [
                'tanggal_surat_jalan' => 'Surat jalan diterbitkan',
                'waktu_berangkat' => 'Kendaraan berangkat',
                'tanggal_muat' => 'Tanggal muat',
                'tanggal_checkpoint' => 'Checkpoint',
                'tanggal_tanda_terima' => 'Tanda terima diterima',
            ],
            'status' => ['status', 'status_gate_in', 'status_pembayaran'],
            'permission' => 'surat-jalan-view',
        ],
        'surat-jalan-bongkaran' => [
            'label' => 'Surat Jalan Bongkaran', 'number_label' => 'Nomor Surat Jalan Bongkaran',
            'columns' => ['nomor_surat_jalan'], 'model' => SuratJalanBongkaran::class,
            'milestones' => [
                'tanggal_surat_jalan' => 'Surat jalan bongkaran diterbitkan',
                'waktu_berangkat' => 'Kendaraan berangkat',
                'tanggal_muat' => 'Tanggal muat',
                'tanggal_checkpoint' => 'Checkpoint',
            ],
            'status' => ['status', 'status_pembayaran'],
            'permission' => 'surat-jalan-bongkaran-view',
        ],
        'tanda-terima' => [
            'label' => 'Tanda Terima', 'number_label' => 'Nomor Surat Jalan / Tanda Terima',
            'columns' => ['no_surat_jalan'], 'model' => TandaTerima::class,
            'milestones' => [
                'tanggal' => 'Tanda terima dibuat',
                'tanggal_checkpoint_supir' => 'Checkpoint supir',
                'tanggal_terima_pelabuhan' => 'Diterima di pelabuhan',
                'tanggal_garasi' => 'Tiba di garasi',
            ],
            'status' => [], 'permission' => 'tanda-terima-view',
        ],
        'tanda-terima-tanpa-surat-jalan' => [
            'label' => 'Tanda Terima Tanpa Surat Jalan', 'number_label' => 'Nomor Tanda Terima',
            'columns' => ['no_tanda_terima', 'nomor_tanda_terima'],
            'model' => TandaTerimaTanpaSuratJalan::class,
            'milestones' => ['tanggal_tanda_terima' => 'Tanda terima dibuat'],
            'status' => ['status'], 'permission' => 'tanda-terima-tanpa-surat-jalan-view',
        ],
        'tanda-terima-lcl' => [
            'label' => 'Tanda Terima LCL', 'number_label' => 'Nomor Tanda Terima LCL',
            'columns' => ['nomor_tanda_terima'], 'model' => TandaTerimaLcl::class,
            'milestones' => ['tanggal_tanda_terima' => 'Tanda terima dibuat'],
            'status' => ['status'], 'permission' => 'tanda-terima-tanpa-surat-jalan-view',
        ],
    ];

    public function index(Request $request)
    {
        $types = collect(self::TYPES)
            ->filter(fn (array $config) => $request->user()->can($config['permission']))
            ->all();
        abort_if($types === [], 403);

        $number = trim((string) $request->query('number', ''));
        $results = [];
        $matchedSuratJalanNumbers = collect();

        if ($number !== '' && isset($types['surat-jalan'])) {
            $matchedSuratJalanNumbers = SuratJalan::query()
                ->where('no_surat_jalan', 'like', '%'.$number.'%')
                ->pluck('no_surat_jalan')
                ->filter()
                ->map(fn ($value) => mb_strtolower(trim((string) $value)));
        }

        if ($number !== '') {
            foreach ($types as $key => $config) {
                $records = $config['model']::query()
                    ->where(function ($query) use ($config, $number) {
                    foreach ($config['columns'] as $column) {
                            $query->orWhere($column, 'like', '%'.$number.'%');
                        }
                    })
                    ->orderByDesc('created_at')
                    ->limit(20)
                    ->get();

                foreach ($records as $record) {
                    // Surat Jalan dan Tanda Terima yang terhubung adalah satu transaksi.
                    // Timeline Surat Jalan sudah mencakup tahap checkpoint dan penerimaan pelabuhan.
                    if ($key === 'tanda-terima' && $matchedSuratJalanNumbers->contains(
                        mb_strtolower(trim((string) $record->no_surat_jalan))
                    )) {
                        continue;
                    }

                    $displayNumber = collect($config['columns'])
                        ->map(fn (string $column) => $record->getAttribute($column))
                        ->first(fn ($value) => filled($value));

                    $results[] = [
                        'type' => $key,
                        'label' => $config['label'],
                        'number' => $displayNumber ?: $number,
                        'details' => match ($key) {
                            'surat-jalan' => $this->buildSuratJalanDetails($record),
                            'tanda-terima-lcl' => $this->buildTandaTerimaLclDetails($record),
                            default => null,
                        },
                        'timeline' => match ($key) {
                            'surat-jalan' => $this->buildSuratJalanTimeline($record),
                            'tanda-terima-tanpa-surat-jalan' => $this->buildTandaTerimaTanpaSuratJalanTimeline($record),
                            'tanda-terima-lcl' => $this->buildTandaTerimaLclTimeline($record),
                            default => $this->buildTimeline($record, $config),
                        },
                    ];
                }
            }
        }

        return view('tracking-transaksi.index', compact('types', 'number', 'results'));
    }

    public function suggestions(Request $request)
    {
        $term = trim((string) $request->query('q', ''));
        if (mb_strlen($term) < 2) {
            return response()->json([]);
        }

        $types = collect(self::TYPES)
            ->filter(fn (array $config) => $request->user()->can($config['permission']))
            ->all();
        $suggestions = collect();

        foreach ($types as $config) {
            $records = $config['model']::query()
                ->where(function ($query) use ($config, $term) {
                    foreach ($config['columns'] as $column) {
                        $query->orWhere($column, 'like', '%'.$term.'%');
                    }
                })
                ->orderByDesc('created_at')
                ->limit(8)
                ->get();

            foreach ($records as $record) {
                foreach ($config['columns'] as $column) {
                    $number = trim((string) $record->getAttribute($column));
                    if ($number !== '' && mb_stripos($number, $term) !== false) {
                        $suggestions->push([
                            'number' => $number,
                            'type' => $config['label'],
                        ]);
                        break;
                    }
                }
            }
        }

        return response()->json(
            $suggestions
                ->unique(fn (array $suggestion) => mb_strtolower($suggestion['type'].'|'.$suggestion['number']))
                ->take(10)
                ->values()
        );
    }

    private function buildTimeline($record, array $config): array
    {
        $events = [];
        foreach ($config['milestones'] as $column => $label) {
            $value = $record->getAttribute($column);
            if ($value) {
                $events[] = [
                    'at' => $value instanceof Carbon ? $value : Carbon::parse($value),
                    'title' => $label,
                    'description' => null,
                ];
            }
        }

        foreach ($config['status'] as $column) {
            $value = $record->getAttribute($column);
            if ($value !== null && $value !== '') {
                $events[] = [
                    'at' => $record->updated_at ?? $record->created_at ?? now(),
                    'title' => 'Status: '.str($column)->replace('_', ' ')->title(),
                    'description' => (string) $value,
                ];
            }
        }

        $logs = AuditLog::query()
            ->where('auditable_type', $record::class)
            ->where('auditable_id', $record->getKey())
            ->orderBy('created_at')
            ->get();

        foreach ($logs as $log) {
            $oldValues = $log->old_values ?? [];
            $newValues = $log->new_values ?? [];
            $changes = [];
            foreach (array_unique(array_merge(array_keys($oldValues), array_keys($newValues))) as $field) {
                if (! preg_match('/^(status|tanggal_|waktu_)/', $field)) {
                    continue;
                }
                $oldValue = $oldValues[$field] ?? null;
                $newValue = $newValues[$field] ?? null;
                if ($oldValue != $newValue) {
                    $changes[] = str($field)->replace('_', ' ')->title().': '.($oldValue ?: '—').' → '.($newValue ?: '—');
                }
            }
            if ($log->user_name) {
                $changes[] = 'Oleh '.$log->user_name;
            }

            $events[] = [
                'at' => $log->created_at,
                'title' => $log->description ?: match ($log->action) {
                    'created' => 'Transaksi dibuat',
                    'deleted' => 'Transaksi dihapus',
                    default => 'Transaksi diperbarui',
                },
                'description' => $changes ? implode(' · ', $changes) : null,
            ];
        }

        if ($logs->isEmpty() && $record->created_at) {
            $events[] = ['at' => $record->created_at, 'title' => 'Transaksi dibuat', 'description' => null];
        }

        return collect($events)
            ->filter(fn (array $event) => ! empty($event['at']))
            ->sortBy('at')
            ->values()
            ->map(fn (array $event) => [
                ...$event,
                'complete' => true,
                'formatted_at' => Carbon::parse($event['at'])->format('d M Y H:i'),
            ])
            ->all();
    }

    private function buildSuratJalanTimeline(SuratJalan $suratJalan): array
    {
        $noSuratJalan = $suratJalan->no_surat_jalan;
        $tandaTerima = TandaTerima::query()
            ->where(function ($query) use ($suratJalan, $noSuratJalan) {
                $query->where('surat_jalan_id', $suratJalan->id)
                    ->orWhere('no_surat_jalan', $noSuratJalan);
            })
            ->latest('created_at')
            ->first();

        $prospeks = Prospek::query()
            ->where(function ($query) use ($suratJalan, $noSuratJalan) {
                $query->where('surat_jalan_id', $suratJalan->id)
                    ->orWhere('no_surat_jalan', $noSuratJalan)
                    ->orWhere('no_surat_jalan', 'like', $noSuratJalan.'-%');
            })
            ->when($tandaTerima, fn ($query) => $query->orWhere('tanda_terima_id', $tandaTerima->id))
            ->get(['id']);
        $prospekIds = $prospeks->pluck('id');

        $paid = $suratJalan->status_pembayaran === 'sudah_dibayar'
            || $suratJalan->status_pembayaran_uang_jalan === 'dibayar';
        $paymentLog = $paid
            ? AuditLog::query()
                ->where('auditable_type', SuratJalan::class)
                ->where('auditable_id', $suratJalan->id)
                ->orderBy('created_at')
                ->get()
                ->first(function (AuditLog $log) {
                    $changes = $log->new_values ?? [];

                    return ($changes['status_pembayaran'] ?? null) === 'sudah_dibayar'
                        || ($changes['status_pembayaran_uang_jalan'] ?? null) === 'dibayar';
                })
            : null;

        $naikKapal = $prospekIds->isNotEmpty()
            ? NaikKapal::query()->whereIn('prospek_id', $prospekIds)->where('sudah_ob', true)->latest('tanggal_ob')->first()
            : null;
        $bl = $prospekIds->isNotEmpty()
            ? Bl::query()->whereIn('prospek_id', $prospekIds)->where('sudah_ob', true)->latest('tanggal_ob')->first()
            : null;
        $obDate = $naikKapal?->tanggal_ob ?? $bl?->tanggal_ob;

        $manifestIds = $prospekIds->isNotEmpty()
            ? Manifest::query()->whereIn('prospek_id', $prospekIds)->pluck('id')
            : collect();
        $suratJalanBongkaranBatam = $manifestIds->isNotEmpty()
            ? SuratJalanBongkaranBatam::query()->whereIn('manifest_id', $manifestIds)->latest('created_at')->first()
            : null;

        $steps = [
            ['title' => 'Surat jalan dibuat', 'at' => $suratJalan->created_at, 'complete' => (bool) $suratJalan->created_at],
            ['title' => 'Surat jalan dibayar', 'at' => $paid ? ($paymentLog?->created_at ?? $suratJalan->updated_at) : null, 'complete' => $paid],
            ['title' => 'Supir melakukan checkpoint', 'at' => $suratJalan->tanggal_checkpoint, 'complete' => (bool) $suratJalan->tanggal_checkpoint],
            ['title' => 'Surat jalan diterima di pelabuhan (tanda terima dibuat)', 'at' => $tandaTerima?->created_at, 'complete' => (bool) $tandaTerima],
            ['title' => 'Surat jalan dimuat di kapal (OB Muat)', 'at' => $obDate, 'complete' => (bool) ($naikKapal || $bl)],
            ['title' => 'Surat jalan dilakukan dooring di Batam (Surat Jalan Bongkaran Batam dibuat)', 'at' => $suratJalanBongkaranBatam?->created_at, 'complete' => (bool) $suratJalanBongkaranBatam],
        ];

        return array_map(function (array $step) {
            $step['description'] = $step['complete'] ? 'Selesai' : 'Belum dilakukan';
            $step['formatted_at'] = $step['at']
                ? Carbon::parse($step['at'])->format('d M Y'.(Carbon::parse($step['at'])->format('H:i:s') === '00:00:00' ? '' : ' H:i'))
                : 'Belum tercatat';

            return $step;
        }, $steps);
    }

    private function buildSuratJalanDetails(SuratJalan $suratJalan): array
    {
        $tandaTerima = TandaTerima::query()
            ->with('shipperJb')
            ->where(function ($query) use ($suratJalan) {
                $query->where('surat_jalan_id', $suratJalan->id)
                    ->orWhere('no_surat_jalan', $suratJalan->no_surat_jalan);
            })
            ->latest('created_at')
            ->first();

        $containerNumbers = collect($tandaTerima?->kontainer_details ?? [])
            ->pluck('nomor_kontainer')
            ->filter()
            ->unique()
            ->values();

        if ($containerNumbers->isEmpty()) {
            $containerNumbers = collect(preg_split('/[,\n]+/', (string) ($tandaTerima?->no_kontainer ?: $suratJalan->no_kontainer)))
                ->map(fn ($number) => trim($number))
                ->filter()
                ->unique()
                ->values();
        }

        $shipper = $tandaTerima?->shipperJb?->shipper
            ?: $tandaTerima?->pengirim
            ?: $suratJalan->pengirimRelation?->nama_pengirim;

        return [
            'container' => $containerNumbers->implode(', '),
            'shipper' => $shipper,
        ];
    }

    private function buildTandaTerimaLclDetails(TandaTerimaLcl $tandaTerima): array
    {
        $tandaTerima->loadMissing(['shipperJb', 'kontainerPivot']);

        $containerNumbers = $tandaTerima->kontainerPivot
            ->pluck('nomor_kontainer')
            ->filter()
            ->unique()
            ->values();

        if ($containerNumbers->isEmpty() && filled($tandaTerima->nomor_kontainer)) {
            $containerNumbers = collect(preg_split('/[,\n]+/', (string) $tandaTerima->nomor_kontainer))
                ->map(fn ($number) => trim($number))
                ->filter()
                ->unique()
                ->values();
        }

        return [
            'container' => $containerNumbers->implode(', '),
            'shipper' => $tandaTerima->shipperJb?->shipper ?: $tandaTerima->nama_pengirim,
        ];
    }

    private function buildTandaTerimaTanpaSuratJalanTimeline(TandaTerimaTanpaSuratJalan $tandaTerima): array
    {
        $nomorTandaTerima = collect([$tandaTerima->no_tanda_terima, $tandaTerima->nomor_tanda_terima])
            ->filter()
            ->unique()
            ->values();

        $prospekIds = Prospek::query()
            ->where(function ($query) use ($nomorTandaTerima) {
                foreach ($nomorTandaTerima as $number) {
                    $query->orWhere('no_surat_jalan', $number)
                        ->orWhere('no_surat_jalan', 'like', $number.'-%');
                }
            })
            ->pluck('id');

        $manifests = Manifest::query()
            ->where(function ($query) use ($nomorTandaTerima, $prospekIds) {
                if ($prospekIds->isNotEmpty()) {
                    $query->whereIn('prospek_id', $prospekIds)
                        ->orWhereIn('nomor_tanda_terima', $nomorTandaTerima);
                } else {
                    $query->whereIn('nomor_tanda_terima', $nomorTandaTerima);
                }
            })
            ->get(['id', 'prospek_id']);

        $relatedProspekIds = $prospekIds->merge($manifests->pluck('prospek_id'))->filter()->unique();
        $naikKapal = $relatedProspekIds->isNotEmpty()
            ? NaikKapal::query()->whereIn('prospek_id', $relatedProspekIds)->where('sudah_ob', true)->latest('tanggal_ob')->first()
            : null;
        $bl = $relatedProspekIds->isNotEmpty()
            ? Bl::query()->whereIn('prospek_id', $relatedProspekIds)->where('sudah_ob', true)->latest('tanggal_ob')->first()
            : null;
        $obDate = $naikKapal?->tanggal_ob ?? $naikKapal?->updated_at ?? $bl?->tanggal_ob ?? $bl?->updated_at;

        $manifestIds = $manifests->pluck('id');
        $suratJalanBongkaranBatam = $manifestIds->isNotEmpty()
            ? SuratJalanBongkaranBatam::query()->whereIn('manifest_id', $manifestIds)->latest('created_at')->first()
            : null;

        $steps = [
            [
                'title' => 'Tanda terima diterima di pelabuhan',
                'at' => $tandaTerima->tanggal_tanda_terima ?? $tandaTerima->created_at,
                'complete' => true,
            ],
            [
                'title' => 'Tanda terima sudah dimuat di kapal (OB Muat)',
                'at' => $obDate,
                'complete' => (bool) ($naikKapal || $bl),
            ],
            [
                'title' => 'Tanda terima sudah dilakukan dooring di Batam (Surat Jalan Bongkaran Batam dibuat)',
                'at' => $suratJalanBongkaranBatam?->created_at,
                'complete' => (bool) $suratJalanBongkaranBatam,
            ],
        ];

        return array_map(function (array $step) {
            $step['description'] = $step['complete'] ? 'Selesai' : 'Belum dilakukan';
            $step['formatted_at'] = $step['at']
                ? Carbon::parse($step['at'])->format('d M Y'.(Carbon::parse($step['at'])->format('H:i:s') === '00:00:00' ? '' : ' H:i'))
                : 'Belum tercatat';

            return $step;
        }, $steps);
    }

    private function buildTandaTerimaLclTimeline(TandaTerimaLcl $tandaTerima): array
    {
        $nomorTandaTerima = $tandaTerima->nomor_tanda_terima;
        $stuffing = $tandaTerima->kontainerPivot()
            ->whereNotNull('nomor_kontainer')
            ->where('nomor_kontainer', '!=', '')
            ->orderBy('assigned_at')
            ->first();

        $prospekIds = Prospek::query()
            ->where(function ($query) use ($nomorTandaTerima) {
                $query->where('no_surat_jalan', $nomorTandaTerima)
                    ->orWhere('no_surat_jalan', 'like', $nomorTandaTerima.'-%')
                    ->orWhere('no_surat_jalan', 'like', '%'.$nomorTandaTerima.'%');
            })
            ->pluck('id');

        $manifests = Manifest::query()
            ->where(function ($query) use ($nomorTandaTerima, $prospekIds) {
                if ($prospekIds->isNotEmpty()) {
                    $query->whereIn('prospek_id', $prospekIds)
                        ->orWhere('nomor_tanda_terima', 'like', '%'.$nomorTandaTerima.'%');
                } else {
                    $query->where('nomor_tanda_terima', 'like', '%'.$nomorTandaTerima.'%');
                }
            })
            ->get(['id', 'prospek_id']);

        $relatedProspekIds = $prospekIds->merge($manifests->pluck('prospek_id'))->filter()->unique();
        $naikKapal = $relatedProspekIds->isNotEmpty()
            ? NaikKapal::query()->whereIn('prospek_id', $relatedProspekIds)->where('sudah_ob', true)->latest('tanggal_ob')->first()
            : null;
        $bl = $relatedProspekIds->isNotEmpty()
            ? Bl::query()->whereIn('prospek_id', $relatedProspekIds)->where('sudah_ob', true)->latest('tanggal_ob')->first()
            : null;
        $obDate = $naikKapal?->tanggal_ob ?? $naikKapal?->updated_at ?? $bl?->tanggal_ob ?? $bl?->updated_at;

        $manifestIds = $manifests->pluck('id');
        $suratJalanBongkaranBatam = $manifestIds->isNotEmpty()
            ? SuratJalanBongkaranBatam::query()->whereIn('manifest_id', $manifestIds)->latest('created_at')->first()
            : null;

        $steps = [
            [
                'title' => 'Tanda terima LCL dibuat',
                'at' => $tandaTerima->created_at,
                'complete' => (bool) $tandaTerima->created_at,
            ],
            [
                'title' => 'Tanda terima LCL sudah di-stuffing ke kontainer',
                'at' => $stuffing?->assigned_at ?? $stuffing?->created_at,
                'complete' => (bool) $stuffing,
            ],
            [
                'title' => 'Tanda terima LCL sudah dimuat di kapal (OB Muat)',
                'at' => $obDate,
                'complete' => (bool) ($naikKapal || $bl),
            ],
            [
                'title' => 'Tanda terima LCL sudah dilakukan dooring (Surat Jalan Bongkaran Batam dibuat)',
                'at' => $suratJalanBongkaranBatam?->created_at,
                'complete' => (bool) $suratJalanBongkaranBatam,
            ],
        ];

        return array_map(function (array $step) {
            $step['description'] = $step['complete'] ? 'Selesai' : 'Belum dilakukan';
            $step['formatted_at'] = $step['at']
                ? Carbon::parse($step['at'])->format('d M Y'.(Carbon::parse($step['at'])->format('H:i:s') === '00:00:00' ? '' : ' H:i'))
                : 'Belum tercatat';

            return $step;
        }, $steps);
    }
}
