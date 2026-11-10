<?php

namespace App\Http\Controllers;

use App\Models\GpsDailyDistance;
use App\Services\RitSuratJalanService;
use App\Services\VehicleAmprahanUsageService;
use App\Services\VehicleTireReplacementService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DashboardPerjalananKendaraanController extends Controller
{
    public function explainRanking(Request $request)
    {
        $validated = $request->validate([
            'vehicle' => ['required', 'string', 'max:50'],
            'driver' => ['nullable', 'string', 'max:100'],
            'period' => ['required', 'string', 'max:50'],
            'metrics' => ['required', 'array', 'min:1', 'max:6'],
            'metrics.*.name' => ['required', 'string', 'max:60'],
            'metrics.*.rank' => ['required', 'integer', 'min:1'],
            'metrics.*.total' => ['required', 'integer', 'min:1'],
            'metrics.*.value' => ['required', 'string', 'max:40'],
            'metrics.*.unit' => ['required', 'string', 'max:20'],
            'metrics.*.better' => ['required', 'in:higher,lower'],
        ]);

        $metrics = collect($validated['metrics'])->map(fn ($metric) => sprintf(
            '- %s: peringkat %d dari %d, nilai %s %s. %s',
            $metric['name'], $metric['rank'], $metric['total'], $metric['value'], $metric['unit'],
            $metric['better'] === 'lower' ? 'Nilai lebih rendah lebih baik.' : 'Nilai lebih tinggi lebih baik.'
        ))->implode("\n");

        try {
            $response = Http::timeout(60)
                ->post(rtrim(config('services.ollama.url'), '/').'/api/chat', [
                    'model' => config('services.ollama.model', 'llama3.2:1b'),
                    'stream' => false,
                    'keep_alive' => '30m',
                    'options' => ['num_predict' => 110, 'temperature' => 0.35],
                    'messages' => [
                        ['role' => 'system', 'content' => 'Jelaskan ranking kendaraan dalam satu paragraf Bahasa Indonesia yang alami, maksimal 55 kata. Gunakan hanya data yang diberikan dan sebutkan nilai serta posisi. Peringkat 1 adalah terbaik: jarak, hari bergerak, dan rit lebih tinggi lebih baik; idle, pergantian ban, dan pemakaian barang/servis lebih rendah lebih baik. Jangan mengarang sebab.'],
                        ['role' => 'user', 'content' => "Jelaskan hasil ranking kendaraan {$validated['vehicle']} dengan supir ".($validated['driver'] ?: 'tidak tercatat')." pada periode {$validated['period']}. Data berikut sudah diurutkan sesuai kondisi terbaik ke terburuk:\n{$metrics}"],
                    ],
                ]);

            if (! $response->successful()) {
                $apiError = (string) data_get($response->json(), 'error', 'No error message returned');
                Log::warning('Ollama ranking explanation request failed.', [
                    'http_status' => $response->status(),
                    'api_error' => mb_substr($apiError, 0, 500),
                ]);

                return response()->json(['message' => 'Ollama menolak permintaan (HTTP '.$response->status().'). Pastikan Ollama aktif dan model tersedia.'], 502);
            }

            $text = $response->json('message.content');

            if (! is_string($text) || trim($text) === '') {
            return response()->json(['message' => 'Ollama mengirim jawaban kosong. Silakan coba lagi.'], 502);
            }

            return response()->json(['explanation' => trim($text)]);
        } catch (\Throwable $exception) {
            Log::warning('Ollama ranking explanation request failed.', ['message' => $exception->getMessage()]);

            return response()->json(['message' => 'Ollama tidak bisa dihubungi. Pastikan service aktif di server.'], 502);
        }
    }

    public function index(Request $request, RitSuratJalanService $ritService)
    {
        $hasReport = $request->hasAny(['tanggal_dari', 'tanggal_sampai']);
        $tanggalDari = (string) $request->query('tanggal_dari', '');
        $tanggalSampai = (string) $request->query('tanggal_sampai', '');
        $vehicleStats = collect();
        $dailyTotals = collect();
        $totalDistance = 0.0;
        $maxVehicleDistance = 0.0;
        $maxDailyDistance = 0.0;
        $rankings = collect();

        if ($hasReport) {
            $dates = $request->validate([
                'tanggal_dari' => ['required', 'date_format:Y-m-d'],
                'tanggal_sampai' => ['required', 'date_format:Y-m-d', 'after_or_equal:tanggal_dari'],
            ]);

            $tanggalDari = $dates['tanggal_dari'];
            $tanggalSampai = $dates['tanggal_sampai'];

            $vehicleStats = GpsDailyDistance::query()
                ->select('mobil_id')
                ->selectRaw('SUM(distance_km) as total_km, COUNT(*) as days_with_data, SUM(CASE WHEN distance_km > 0 THEN 1 ELSE 0 END) as moving_days, SUM(idle_seconds) as idle_seconds, COUNT(idle_seconds) as days_with_idle_data')
                ->whereBetween('date', [$tanggalDari, $tanggalSampai])
                ->where('point_count', '>', 0)
                ->groupBy('mobil_id')
                ->with('mobil.karyawan')
                ->get()
                ->sortByDesc(fn ($row) => (float) $row->total_km)
                ->values();

            if ($vehicleStats->isNotEmpty()) {
                $amprahanUsages = app(VehicleAmprahanUsageService::class)->forVehicles(
                    $vehicleStats->pluck('mobil_id')->all(), $tanggalDari, $tanggalSampai
                );
                $tireReplacements = app(VehicleTireReplacementService::class)->summarize(
                    $vehicleStats->pluck('mobil_id')->all(), $tanggalDari, $tanggalSampai
                );
                $start = Carbon::parse($tanggalDari)->startOfDay();
                $end = Carbon::parse($tanggalSampai)->endOfDay();
                $ritCounts = [];
                $plateVariants = [];

                foreach ([$ritService->regular($start, $end), $ritService->bongkaran($start, $end)] as $query) {
                    $counts = $query
                        ->whereNotNull('no_plat')
                        ->where('no_plat', '!=', '')
                        ->selectRaw('no_plat, COUNT(*) as rit_count')
                        ->groupBy('no_plat')
                        ->get();

                    foreach ($counts as $count) {
                        $plate = preg_replace('/[^A-Z0-9]/', '', strtoupper($count->no_plat));
                        if ($plate !== '') {
                            $ritCounts[$plate] = ($ritCounts[$plate] ?? 0) + (int) $count->rit_count;
                            $plateVariants[$plate][] = $count->no_plat;
                        }
                    }
                }

                $vehiclePlates = $vehicleStats
                    ->map(fn ($row) => preg_replace('/[^A-Z0-9]/', '', strtoupper($row->mobil?->nomor_polisi ?? '')))
                    ->filter()
                    ->unique();
                $matchingPlates = $vehiclePlates
                    ->flatMap(fn ($plate) => $plateVariants[$plate] ?? [])
                    ->unique()
                    ->values()
                    ->all();
                $ritDetails = [];

                if ($matchingPlates !== []) {
                    foreach (['regular', 'bongkaran'] as $type) {
                        $suratJalans = $ritService->{$type}($start, $end)
                            ->whereIn('no_plat', $matchingPlates)
                            ->select($type === 'regular'
                                ? ['id', 'no_plat', 'tanggal_checkpoint', 'tanggal_tanda_terima', 'tanggal_surat_jalan', 'no_surat_jalan', 'kegiatan', 'status', 'supir', 'supir2', 'pengirim', 'tujuan_pengambilan']
                                : ['id', 'no_plat', 'tanggal_checkpoint', 'nomor_surat_jalan', 'kegiatan', 'supir', 'supir2', 'pengirim', 'tujuan_pengambilan'])
                            ->with($type === 'regular' ? ['tandaTerima', 'pengirimRelation'] : ['tandaTerima'])
                            ->get();

                        foreach ($suratJalans as $suratJalan) {
                            $plate = preg_replace('/[^A-Z0-9]/', '', strtoupper($suratJalan->no_plat));
                            $dateCandidates = [
                                $suratJalan->tanggal_checkpoint,
                                $type === 'regular' ? $suratJalan->tandaTerima?->tanggal : $suratJalan->tandaTerima?->tanggal_tanda_terima,
                                $type === 'regular' && $suratJalan->kegiatan === 'bongkaran' ? $suratJalan->tanggal_tanda_terima : null,
                                $type === 'regular' && $suratJalan->status === 'approved' ? $suratJalan->tanggal_surat_jalan : null,
                            ];
                            $date = collect($dateCandidates)->first(fn ($candidate) => $candidate
                                && Carbon::parse($candidate)->betweenIncluded($start, $end));

                            $ritDetails[$plate][] = [
                                'tanggal' => $date ? Carbon::parse($date)->format('d/m/Y') : '-',
                                'tanggal_sort' => $date ? Carbon::parse($date)->toDateString() : '',
                                'nomor' => $type === 'regular' ? $suratJalan->no_surat_jalan : $suratJalan->nomor_surat_jalan,
                                'jenis' => $type === 'regular' ? 'Surat jalan' : 'Bongkaran',
                                'kegiatan' => $suratJalan->kegiatan,
                                'supir' => $suratJalan->supir ?: $suratJalan->supir2,
                                'pengirim' => $type === 'regular'
                                    ? ($suratJalan->pengirimRelation?->nama_pengirim ?: $suratJalan->pengirim)
                                    : $suratJalan->pengirim,
                                'tujuan_ambil' => $suratJalan->tujuan_pengambilan,
                            ];
                        }
                    }
                }

                foreach ($vehicleStats as $row) {
                    $row->amprahan_usages = $amprahanUsages->get($row->mobil_id, collect());
                    $tires = $tireReplacements->get($row->mobil_id, ['replacement_count' => 0, 'tire_count' => 0]);
                    $row->tire_replacement_count = $tires['replacement_count'];
                    $row->tire_count = $tires['tire_count'];
                    $row->tire_details = $tires['details'] ?? collect();
                    $plate = preg_replace('/[^A-Z0-9]/', '', strtoupper($row->mobil?->nomor_polisi ?? ''));
                    $row->rit_count = $ritCounts[$plate] ?? 0;
                    $row->rit_details = collect($ritDetails[$plate] ?? [])
                        ->sortByDesc('tanggal_sort')
                        ->values();
                }
            }

            $dailyTotals = GpsDailyDistance::query()
                ->select('date')
                ->selectRaw('SUM(distance_km) as total_km, COUNT(*) as vehicle_count')
                ->whereBetween('date', [$tanggalDari, $tanggalSampai])
                ->where('point_count', '>', 0)
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            $totalDistance = (float) $vehicleStats->sum('total_km');
            $maxVehicleDistance = (float) $vehicleStats->max('total_km');
            $maxDailyDistance = (float) $dailyTotals->max('total_km');

            $rankingMetrics = [
                'total_km' => ['label' => 'Jarak tempuh', 'unit' => 'km', 'direction' => 'desc', 'better' => 'higher'],
                'moving_days' => ['label' => 'Hari bergerak', 'unit' => 'hari', 'direction' => 'desc', 'better' => 'higher'],
                'idle_seconds' => ['label' => 'Durasi idle', 'unit' => 'jam', 'direction' => 'asc', 'better' => 'lower'],
                'rit_count' => ['label' => 'Jumlah rit', 'unit' => 'rit', 'direction' => 'desc', 'better' => 'higher'],
                'tire_replacement_count' => ['label' => 'Pergantian ban & velg', 'unit' => 'kali', 'direction' => 'asc', 'better' => 'lower'],
                'usage_count' => ['label' => 'Pemakaian barang / servis', 'unit' => 'kali', 'direction' => 'asc', 'better' => 'lower'],
            ];

            foreach ($rankingMetrics as $key => $metric) {
                $rankings->put($key, $vehicleStats
                    ->sortBy(fn ($row) => (float) ($key === 'usage_count' ? $row->amprahan_usages->count() : $row->{$key}), SORT_REGULAR, $metric['direction'] === 'desc')
                    ->values()
                    ->map(fn ($row, $index) => [
                        'rank' => $index + 1,
                        'vehicle' => $row->mobil?->nomor_polisi ?: 'Kendaraan #'.$row->mobil_id,
                        'value' => (float) match ($key) {
                            'usage_count' => $row->amprahan_usages->count(),
                            'idle_seconds' => ((float) $row->idle_seconds) / 3600,
                            default => $row->{$key},
                        },
                    ])->all());
            }
            $rankings = collect($rankingMetrics)->map(fn ($metric, $key) => [
                'label' => $metric['label'],
                'unit' => $metric['unit'],
                'better' => $metric['better'],
                'rows' => collect($rankings->get($key, [])),
            ]);
        }

        return view('dashboard-perjalanan-kendaraan.index', compact(
            'hasReport', 'tanggalDari', 'tanggalSampai', 'vehicleStats', 'dailyTotals',
            'totalDistance', 'maxVehicleDistance', 'maxDailyDistance', 'rankings'
        ));
    }
}
