<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ManifestController;
use App\Models\Manifest;
use App\Models\WaBroadcast;
use App\Models\WaPhoneOverride;
use App\Models\WaTemplate;
use App\Services\WaBroadcastRecipientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WaBroadcastController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type', 'all');

        $query = WaBroadcast::with('template')->orderBy('id', 'desc');

        if ($type === 'jadwal') {
            $query->where(function ($q) {
                $q->whereHas('template', function ($t) {
                    $t->where('nama_template', 'like', '%jadwal%');
                })->orWhere('kategori_masalah', 'like', '%jadwal%')
                  ->orWhereNull('kategori_masalah')
                  ->orWhere('kategori_masalah', '');
            });
        } elseif ($type === 'status_pengiriman' || $type === 'status') {
            $query->where(function ($q) {
                $q->whereHas('template', function ($t) {
                    $t->where('nama_template', 'like', '%status%')
                      ->orWhere('nama_template', 'like', '%pengiriman%');
                })->orWhere('kategori_masalah', 'like', '%status%')
                  ->orWhere('kategori_masalah', 'like', '%pengiriman%');
            });
        } elseif ($type === 'kendala') {
            $query->whereNotNull('kategori_masalah')
                ->where('kategori_masalah', '!=', '')
                ->where('kategori_masalah', 'not like', '%jadwal%')
                ->where('kategori_masalah', 'not like', '%status%')
                ->where('kategori_masalah', 'not like', '%pengiriman%')
                ->whereDoesntHave('template', function ($t) {
                    $t->where('nama_template', 'like', '%status%')
                      ->orWhere('nama_template', 'like', '%pengiriman%');
                });
        }

        $broadcasts = $query->get();

        // Statistics
        $totalAll = WaBroadcast::count();
        $totalJadwal = WaBroadcast::where(function ($q) {
            $q->whereHas('template', function ($t) {
                $t->where('nama_template', 'like', '%jadwal%');
            })->orWhere('kategori_masalah', 'like', '%jadwal%')
              ->orWhereNull('kategori_masalah')
              ->orWhere('kategori_masalah', '');
        })->count();
        $totalStatusPengiriman = WaBroadcast::where(function ($q) {
            $q->whereHas('template', function ($t) {
                $t->where('nama_template', 'like', '%status%')
                  ->orWhere('nama_template', 'like', '%pengiriman%');
            })->orWhere('kategori_masalah', 'like', '%status%')
              ->orWhere('kategori_masalah', 'like', '%pengiriman%');
        })->count();
        $totalKendala = WaBroadcast::whereNotNull('kategori_masalah')
            ->where('kategori_masalah', '!=', '')
            ->where('kategori_masalah', 'not like', '%jadwal%')
            ->where('kategori_masalah', 'not like', '%status%')
            ->where('kategori_masalah', 'not like', '%pengiriman%')
            ->whereDoesntHave('template', function ($t) {
                $t->where('nama_template', 'like', '%status%')
                  ->orWhere('nama_template', 'like', '%pengiriman%');
            })
            ->count();
        $totalShipper = $broadcasts->sum('total_shipper');

        return view('master.wa-broadcast.index', compact('broadcasts', 'type', 'totalAll', 'totalJadwal', 'totalStatusPengiriman', 'totalKendala', 'totalShipper'));
    }

    public function create(Request $request)
    {
        $templates = WaTemplate::where('is_active', true)->orderBy('nama_template')->get();

        // Get unique ships from Manifest and MasterJadwalKapalBerlabuh
        $kapalsFromManifest = Manifest::select('nama_kapal')
            ->distinct()
            ->whereNotNull('nama_kapal')
            ->where('nama_kapal', '!=', '')
            ->pluck('nama_kapal');

        $kapalsFromJadwal = \App\Models\MasterJadwalKapalBerlabuh::select('nama_kapal')
            ->distinct()
            ->whereNotNull('nama_kapal')
            ->where('nama_kapal', '!=', '')
            ->pluck('nama_kapal');

        $kapals = $kapalsFromManifest->merge($kapalsFromJadwal)
            ->map(fn ($k) => trim($k))
            ->unique()
            ->filter()
            ->sort()
            ->values();

        // Get unique ports from MasterJadwalKapalBerlabuh and MasterPelabuhan
        $pelabuhansFromJadwal = \App\Models\MasterJadwalKapalBerlabuh::select('pelabuhan')
            ->distinct()
            ->whereNotNull('pelabuhan')
            ->where('pelabuhan', '!=', '')
            ->pluck('pelabuhan');

        $pelabuhansFromMaster = \App\Models\MasterPelabuhan::where('status', 'aktif')
            ->pluck('nama_pelabuhan');

        $pelabuhans = $pelabuhansFromJadwal->merge($pelabuhansFromMaster)
            ->map(fn ($p) => trim($p))
            ->unique()
            ->filter()
            ->sort()
            ->values();

        $selectedKapal = $request->query('nama_kapal', old('nama_kapal'));
        $selectedVoyage = $request->query('no_voyage', old('no_voyage'));
        $selectedPelabuhan = $request->query('pelabuhan', old('pelabuhan'));
        $defaultTemplateId = $request->query('template_id', old('template_id'));

        // If no template selected, auto-select appropriate template based on type
        if (!$defaultTemplateId) {
            if ($request->query('type') === 'jadwal' || $request->has('pelabuhan')) {
                $jadwalTemplate = WaTemplate::where('is_active', true)->where('nama_template', 'like', '%jadwal%')->first();
                if ($jadwalTemplate) {
                    $defaultTemplateId = $jadwalTemplate->id;
                }
            } elseif ($request->query('type') === 'status_pengiriman' || $request->query('type') === 'status') {
                $statusTemplate = WaTemplate::where('is_active', true)
                    ->where(function ($q) {
                        $q->where('nama_template', 'like', '%status%')
                          ->orWhere('nama_template', 'like', '%pengiriman%');
                    })->first();
                if ($statusTemplate) {
                    $defaultTemplateId = $statusTemplate->id;
                }
            } elseif ($request->query('type') === 'kendala') {
                $kendalaTemplate = WaTemplate::where('is_active', true)
                    ->where('nama_template', 'not like', '%jadwal%')
                    ->where('nama_template', 'not like', '%status%')
                    ->where('nama_template', 'not like', '%pengiriman%')
                    ->first();
                if ($kendalaTemplate) {
                    $defaultTemplateId = $kendalaTemplate->id;
                }
            }
        }

        $voyages = collect();
        if ($selectedKapal) {
            $voyagesFromManifest = Manifest::where('nama_kapal', $selectedKapal)
                ->whereNotNull('no_voyage')
                ->where('no_voyage', '!=', '')
                ->pluck('no_voyage');

            $voyagesFromJadwal = \App\Models\MasterJadwalKapalBerlabuh::where('nama_kapal', $selectedKapal)
                ->whereNotNull('no_voyage')
                ->where('no_voyage', '!=', '')
                ->pluck('no_voyage');

            $voyages = $voyagesFromManifest->merge($voyagesFromJadwal)
                ->map(fn ($v) => trim($v))
                ->unique()
                ->filter()
                ->values();
        }

        $templatesJson = $templates->map(fn ($t) => [
            'id' => $t->id,
            'nama' => $t->nama_template,
            'type' => str_contains(strtolower($t->nama_template), 'jadwal')
                ? 'jadwal'
                : (str_contains(strtolower($t->nama_template), 'status') || str_contains(strtolower($t->nama_template), 'pengiriman')
                    ? 'status_pengiriman'
                    : 'kendala'),
            'isi' => $t->isi_template,
        ])->values();

        return view('master.wa-broadcast.create', compact('templates', 'templatesJson', 'kapals', 'pelabuhans', 'voyages', 'selectedKapal', 'selectedVoyage', 'selectedPelabuhan', 'defaultTemplateId'));
    }

    public function getSchedulesByPort(Request $request)
    {
        $pelabuhan = $request->query('pelabuhan');

        if (! $pelabuhan) {
            return response()->json([
                'success' => true,
                'schedules' => [],
            ]);
        }

        $schedules = \App\Models\MasterJadwalKapalBerlabuh::where('pelabuhan', $pelabuhan)
            ->where('status', 'aktif')
            ->orderBy('tanggal_closing', 'asc')
            ->orderBy('tanggal_etd', 'asc')
            ->get();

        if ($schedules->isEmpty()) {
            $normalizedPort = strtoupper(trim(str_replace('.', '', $pelabuhan)));
            $normalizedPort = preg_replace('/\s+/', ' ', $normalizedPort);

            $schedules = \App\Models\MasterJadwalKapalBerlabuh::whereRaw("UPPER(REPLACE(REPLACE(pelabuhan, '.', ''), '  ', ' ')) = ?", [$normalizedPort])
                ->where('status', 'aktif')
                ->orderBy('tanggal_closing', 'asc')
                ->orderBy('tanggal_etd', 'asc')
                ->get();
        }

        $schedulesData = $schedules->map(function ($s) {
            return [
                'id' => $s->id,
                'nama_kapal' => $s->nama_kapal,
                'no_voyage' => $s->no_voyage ?: '-',
                'pelabuhan' => $s->pelabuhan,
                'tanggal_closing' => $s->tanggal_closing ? \Carbon\Carbon::parse($s->tanggal_closing)->format('d-M-Y') : '-',
                'tanggal_closing_raw' => $s->tanggal_closing ? \Carbon\Carbon::parse($s->tanggal_closing)->format('Y-m-d') : '',
                'tanggal_etd' => $s->tanggal_etd ? \Carbon\Carbon::parse($s->tanggal_etd)->format('d-M-Y') : '-',
                'tanggal_etd_raw' => $s->tanggal_etd ? \Carbon\Carbon::parse($s->tanggal_etd)->format('Y-m-d') : '',
                'tanggal_eta' => $s->tanggal_eta ? \Carbon\Carbon::parse($s->tanggal_eta)->format('d-M-Y') : '-',
                'tanggal_eta_raw' => $s->tanggal_eta ? \Carbon\Carbon::parse($s->tanggal_eta)->format('Y-m-d') : '',
                'keterangan' => $s->keterangan ?: '',
            ];
        })->values();

        return response()->json([
            'success' => true,
            'schedules' => $schedulesData,
        ]);
    }

    public function getVoyages(Request $request)
    {
        $namaKapal = $request->query('nama_kapal');

        if (! $namaKapal) {
            return response()->json([
                'success' => true,
                'voyages' => [],
            ]);
        }

        $voyagesFromManifest = Manifest::where('nama_kapal', $namaKapal)
            ->whereNotNull('no_voyage')
            ->where('no_voyage', '!=', '')
            ->pluck('no_voyage');

        $voyagesFromJadwal = \App\Models\MasterJadwalKapalBerlabuh::where('nama_kapal', $namaKapal)
            ->whereNotNull('no_voyage')
            ->where('no_voyage', '!=', '')
            ->pluck('no_voyage');

        $voyages = $voyagesFromManifest->merge($voyagesFromJadwal);

        if ($voyages->isEmpty()) {
            $normalizedKapal = strtoupper(trim(str_replace('.', '', $namaKapal)));
            $normalizedKapal = preg_replace('/\s+/', ' ', $normalizedKapal);

            $voyagesFromManifest = Manifest::whereRaw("UPPER(REPLACE(REPLACE(nama_kapal, '.', ''), '  ', ' ')) = ?", [$normalizedKapal])
                ->whereNotNull('no_voyage')
                ->where('no_voyage', '!=', '')
                ->pluck('no_voyage');

            $voyagesFromJadwal = \App\Models\MasterJadwalKapalBerlabuh::whereRaw("UPPER(REPLACE(REPLACE(nama_kapal, '.', ''), '  ', ' ')) = ?", [$normalizedKapal])
                ->whereNotNull('no_voyage')
                ->where('no_voyage', '!=', '')
                ->pluck('no_voyage');

            $voyages = $voyagesFromManifest->merge($voyagesFromJadwal);
        }

        $voyages = $voyages->map(fn ($v) => trim($v))
            ->unique()
            ->filter()
            ->values();

        return response()->json([
            'success' => true,
            'voyages' => $voyages,
        ]);
    }

    public function getRecipients(Request $request, WaBroadcastRecipientService $recipientService)
    {
        $namaKapal = $request->input('nama_kapal', '');
        $noVoyage = $request->input('no_voyage', '');
        $source = $request->input('source', 'all_master_shippers');

        if (!$request->has('source') && $request->input('type') === 'jadwal') {
            $source = 'all_master_shippers';
        }

        $recipients = $recipientService->recipients((string) $namaKapal, (string) $noVoyage, (string) $source)
            ->map(fn (array $recipient) => [
                'shipper_name' => $recipient['shipper_name'],
                'telepon' => $recipient['telepon'],
                'sumber_tabel' => $recipient['sumber_tabel'],
                'jumlah_kontainer' => $recipient['jumlah_kontainer'],
            ]);

        return response()->json([
            'success' => true,
            'recipients' => $recipients,
        ]);
    }

    public function store(Request $request, WaBroadcastRecipientService $recipientService)
    {
        $isAllShipper = $request->input('target_penerima', 'all_master_shippers') === 'all_master_shippers';

        $request->validate([
            'nama_kapal' => $isAllShipper ? 'nullable|string' : 'required|string',
            'no_voyage' => $isAllShipper ? 'nullable|string' : 'required|string',
            'pelabuhan' => 'nullable|string',
            'jadwal_id' => 'nullable|string',
            'kategori_masalah' => 'nullable|string',
            'deskripsi_masalah' => 'nullable|string',
            'template_id' => 'required|exists:wa_templates,id',
            'target_penerima' => 'nullable|string',
        ]);

        $targetPenerima = $request->input('target_penerima', 'all_master_shippers');
        $namaKapal = $request->input('nama_kapal');
        $noVoyage = $request->input('no_voyage') ?: '-';
        $pelabuhan = $request->input('pelabuhan');

        if ($request->input('jadwal_id') === 'all') {
            $namaKapal = "Semua Kapal" . ($pelabuhan ? " ({$pelabuhan})" : '');
            $noVoyage = '-';
        } elseif ($request->filled('jadwal_id') && is_numeric($request->input('jadwal_id'))) {
            $jadwal = \App\Models\MasterJadwalKapalBerlabuh::find($request->input('jadwal_id'));
            if ($jadwal) {
                $namaKapal = $jadwal->nama_kapal;
                $noVoyage = $jadwal->no_voyage ?: ($noVoyage ?: '-');
                $pelabuhan = $jadwal->pelabuhan;
            }
        }

        if (!$namaKapal) {
            $namaKapal = $pelabuhan ? "Jadwal {$pelabuhan}" : 'Semua Master Shipper';
        }

        $selectedShippers = $request->input('selected_shippers', []);
        if (is_string($selectedShippers)) {
            $selectedShippers = json_decode($selectedShippers, true) ?: [];
        }
        if ($request->filled('selected_shippers_json')) {
            $decoded = json_decode($request->input('selected_shippers_json'), true);
            if (is_array($decoded)) {
                $selectedShippers = array_merge($selectedShippers, $decoded);
            }
        }

        $allRecipients = $recipientService->recipients((string) $namaKapal, (string) $noVoyage, $targetPenerima);
        if (!empty($selectedShippers)) {
            $allRecipients = $allRecipients->filter(fn ($r) => in_array($r['shipper_name'], $selectedShippers));
        }
        $totalShipper = $allRecipients->count();

        $template = WaTemplate::find($request->template_id);
        $kategoriMasalah = $request->input('kategori_masalah');
        if (!$kategoriMasalah) {
            if ($isAllShipper) {
                $kategoriMasalah = $pelabuhan ? "Jadwal Kapal {$pelabuhan}" : 'Jadwal Kapal Berlabuh';
            } elseif ($request->input('type') === 'status_pengiriman' || ($template && (str_contains(strtolower($template->nama_template), 'status') || str_contains(strtolower($template->nama_template), 'pengiriman')))) {
                $kategoriMasalah = 'Status OB';
            } else {
                $kategoriMasalah = '';
            }
        }

        // Save broadcast history
        WaBroadcast::create([
            'nama_kapal' => $namaKapal,
            'no_voyage' => $noVoyage,
            'kategori_masalah' => $kategoriMasalah,
            'deskripsi_masalah' => $request->deskripsi_masalah,
            'wa_template_id' => $request->template_id,
            'total_shipper' => $totalShipper,
        ]);

        // Forward the request to ManifestController's broadcastPreview method
        // so we don't have to duplicate the complex preview logic
        $manifestController = app(ManifestController::class);

        return $manifestController->broadcastPreview($request, $recipientService);
    }

    public function destroy(WaBroadcast $waBroadcast)
    {
        $waBroadcast->delete();

        return redirect()->route('master.wa-broadcast.index')->with('success', 'Riwayat broadcast berhasil dihapus.');
    }

    public function gatewayIndex(\App\Services\WhatsAppGatewayService $gateway)
    {
        $status = $gateway->getStatus();

        return view('master.wa-broadcast.gateway', compact('status'));
    }

    public function gatewayQrData(\App\Services\WhatsAppGatewayService $gateway)
    {
        return response()->json($gateway->getQrData());
    }

    public function gatewayLogout(\App\Services\WhatsAppGatewayService $gateway)
    {
        $result = $gateway->logout();

        return response()->json($result);
    }

    public function gatewayReset(\App\Services\WhatsAppGatewayService $gateway)
    {
        $result = $gateway->reset();

        return response()->json($result);
    }

    public function gatewayStatus(\App\Services\WhatsAppGatewayService $gateway)
    {
        return response()->json($gateway->getStatus());
    }

    public function gatewaySendSingle(Request $request, \App\Services\WhatsAppGatewayService $gateway)
    {
        $validated = $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string',
        ]);

        $result = $gateway->sendMessage($validated['phone'], $validated['message']);

        return response()->json($result);
    }

    public function gatewayTestSend(Request $request, \App\Services\WhatsAppGatewayService $gateway)
    {
        return $this->gatewaySendSingle($request, $gateway);
    }

    /**
     * Simpan nomor WA shipper yang diinput manual ke database wa_phone_overrides.
     * Dipanggil secara AJAX (auto-save) setiap kali user mengedit input nomor.
     */
    public function savePhone(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'shipper_name' => 'required|string|max:500',
            'telepon'      => 'nullable|string|max:30',
        ]);

        $shipperName = trim($validated['shipper_name']);
        $telepon     = isset($validated['telepon']) ? trim($validated['telepon']) : null;

        if (empty($telepon)) {
            // Hapus override jika nomor dikosongkan
            WaPhoneOverride::where('shipper_name', $shipperName)->delete();
            return response()->json(['success' => true, 'action' => 'deleted']);
        }

        WaPhoneOverride::updateOrCreate(
            ['shipper_name' => $shipperName],
            ['telepon' => $telepon, 'updated_by' => auth()->id()]
        );

        return response()->json(['success' => true, 'action' => 'saved']);
    }

    /**
     * Ambil semua wa_phone_overrides sebagai JSON (untuk inisialisasi form).
     */
    public function getPhoneOverrides(): JsonResponse
    {
        try {
            $overrides = WaPhoneOverride::select('shipper_name', 'telepon')
                ->whereNotNull('telepon')
                ->where('telepon', '!=', '')
                ->get()
                ->pluck('telepon', 'shipper_name');

            return response()->json(['success' => true, 'overrides' => $overrides]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'overrides' => []]);
        }
    }
}
