<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\SuratJalan;
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
            'columns' => ['no_surat_jalan', 'nomor_tanda_terima'], 'model' => TandaTerima::class,
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
                    $displayNumber = collect($config['columns'])
                        ->map(fn (string $column) => $record->getAttribute($column))
                        ->first(fn ($value) => filled($value));

                    $results[] = [
                        'type' => $key,
                        'label' => $config['label'],
                        'number' => $displayNumber ?: $number,
                        'timeline' => $this->buildTimeline($record, $config),
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
                'formatted_at' => Carbon::parse($event['at'])->format('d M Y H:i'),
            ])
            ->all();
    }
}
