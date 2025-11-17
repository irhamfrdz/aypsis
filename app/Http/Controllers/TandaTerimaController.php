<?php

namespace App\Http\Controllers;

use App\Models\TandaTerima;
use App\Models\Prospek;
use App\Models\MasterKapal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class TandaTerimaController extends Controller
{
    /**
     * Display a listing of tanda terima
     */
    public function index(Request $request)
    {
        $query = TandaTerima::with(['suratJalan', 'creator', 'updater']);

        // Filter by search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('no_kontainer', 'like', "%{$search}%")
                  ->orWhere('estimasi_nama_kapal', 'like', "%{$search}%")
                  ->orWhere('pengirim', 'like', "%{$search}%")
                  ->orWhere('tujuan_pengiriman', 'like', "%{$search}%");
            });
        }

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal_surat_jalan', [$request->start_date, $request->end_date]);
        }

        // Order by newest
        $tandaTerimas = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('tanda-terima.index', compact('tandaTerimas'));
    }



    /**
     * Show the form for editing the specified tanda terima
     */
    public function edit(TandaTerima $tandaTerima)
    {
        // Load relations
        $tandaTerima->load('suratJalan');

        // Get master kapal for dropdown
        $masterKapals = MasterKapal::where('status', 'aktif')->orderBy('nama_kapal')->get();

        return view('tanda-terima.edit', compact('tandaTerima', 'masterKapals'));
    }

    /**
     * Update the specified tanda terima in storage
     */
    public function update(Request $request, TandaTerima $tandaTerima)
    {
        $request->validate([
            'estimasi_nama_kapal' => 'nullable|string|max:255',
            'tanggal_ambil_kontainer' => 'nullable|date',
            'tanggal_terima_pelabuhan' => 'nullable|date',
            'tanggal_garasi' => 'nullable|date',
            'jumlah' => 'nullable|integer|min:0',
            'satuan' => 'nullable|string|max:50',
            'panjang' => 'nullable|numeric|min:0',
            'lebar' => 'nullable|numeric|min:0',
            'tinggi' => 'nullable|numeric|min:0',
            'meter_kubik' => 'nullable|numeric|min:0',
            'tonase' => 'nullable|numeric|min:0',
            'tujuan_pengiriman' => 'nullable|string|max:255',
            'catatan' => 'nullable|string',
            'status' => 'nullable|in:draft,submitted,approved,completed,cancelled',
            'nomor_kontainer' => 'nullable|array',
            'nomor_kontainer.*' => 'nullable|string|max:255',
            'no_seal' => 'nullable|array',
            'no_seal.*' => 'nullable|string|max:255',
            'jumlah_kontainer' => 'nullable|array',
            'jumlah_kontainer.*' => 'nullable|integer|min:0',
            'satuan_kontainer' => 'nullable|array',
            'satuan_kontainer.*' => 'nullable|string|max:50',
            'panjang_kontainer' => 'nullable|array',
            'panjang_kontainer.*' => 'nullable|numeric|min:0',
            'lebar_kontainer' => 'nullable|array',
            'lebar_kontainer.*' => 'nullable|numeric|min:0',
            'tinggi_kontainer' => 'nullable|array',
            'tinggi_kontainer.*' => 'nullable|numeric|min:0',
            'meter_kubik_kontainer' => 'nullable|array',
            'meter_kubik_kontainer.*' => 'nullable|numeric|min:0',
            'tonase_kontainer' => 'nullable|array',
            'tonase_kontainer.*' => 'nullable|numeric|min:0',
            'dimensi_items' => 'nullable|array',
            'dimensi_items.*.panjang' => 'nullable|numeric|min:0',
            'dimensi_items.*.lebar' => 'nullable|numeric|min:0',
            'dimensi_items.*.tinggi' => 'nullable|numeric|min:0',
            'dimensi_items.*.meter_kubik' => 'nullable|numeric|min:0',
            'dimensi_items.*.tonase' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $updateData = [
                'estimasi_nama_kapal' => $request->estimasi_nama_kapal,
                'tanggal_ambil_kontainer' => $request->tanggal_ambil_kontainer,
                'tanggal_terima_pelabuhan' => $request->tanggal_terima_pelabuhan,
                'tanggal_garasi' => $request->tanggal_garasi,
                'jumlah' => $request->jumlah,
                'satuan' => $request->satuan,
                // Format numeric fields to avoid excessive decimals
                'panjang' => $request->panjang ? round((float) $request->panjang, 3) : null,
                'lebar' => $request->lebar ? round((float) $request->lebar, 3) : null,
                'tinggi' => $request->tinggi ? round((float) $request->tinggi, 3) : null,
                'meter_kubik' => $request->meter_kubik ? round((float) $request->meter_kubik, 3) : null,
                'tonase' => $request->tonase ? round((float) $request->tonase, 3) : null,
                'tujuan_pengiriman' => $request->tujuan_pengiriman,
                'catatan' => $request->catatan,
                'updated_by' => Auth::id(),
            ];

            // Handle multiple container numbers
            if ($request->has('nomor_kontainer') && is_array($request->nomor_kontainer)) {
                $nomorKontainers = array_filter($request->nomor_kontainer, function($value) {
                    return !empty(trim($value));
                });
                if (!empty($nomorKontainers)) {
                    $updateData['no_kontainer'] = implode(',', $nomorKontainers);
                }
            }

            // Handle multiple seal numbers
            if ($request->has('no_seal') && is_array($request->no_seal)) {
                $noSeals = array_filter($request->no_seal, function($value) {
                    return !empty(trim($value));
                });
                if (!empty($noSeals)) {
                    $updateData['no_seal'] = implode(',', $noSeals);
                }
            }

            // Handle multiple jumlah per kontainer
            if ($request->has('jumlah_kontainer') && is_array($request->jumlah_kontainer)) {
                $jumlahKontainers = array_filter($request->jumlah_kontainer, function($value) {
                    return !empty(trim($value)) && is_numeric($value);
                });
                if (!empty($jumlahKontainers)) {
                    $updateData['jumlah'] = implode(',', $jumlahKontainers);
                }
            }

            // Handle multiple satuan per kontainer
            if ($request->has('satuan_kontainer') && is_array($request->satuan_kontainer)) {
                $satuanKontainers = array_filter($request->satuan_kontainer, function($value) {
                    return !empty(trim($value));
                });
                if (!empty($satuanKontainers)) {
                    $updateData['satuan'] = implode(',', $satuanKontainers);
                }
            }

            // Handle multiple panjang per kontainer
            if ($request->has('panjang_kontainer') && is_array($request->panjang_kontainer)) {
                $panjangKontainers = array_filter($request->panjang_kontainer, function($value) {
                    return !empty(trim($value)) && is_numeric($value);
                });
                if (!empty($panjangKontainers)) {
                    $updateData['panjang'] = implode(',', $panjangKontainers);
                }
            }

            // Handle multiple lebar per kontainer
            if ($request->has('lebar_kontainer') && is_array($request->lebar_kontainer)) {
                $lebarKontainers = array_filter($request->lebar_kontainer, function($value) {
                    return !empty(trim($value)) && is_numeric($value);
                });
                if (!empty($lebarKontainers)) {
                    $updateData['lebar'] = implode(',', $lebarKontainers);
                }
            }

            // Handle multiple tinggi per kontainer
            if ($request->has('tinggi_kontainer') && is_array($request->tinggi_kontainer)) {
                $tinggiKontainers = array_filter($request->tinggi_kontainer, function($value) {
                    return !empty(trim($value)) && is_numeric($value);
                });
                if (!empty($tinggiKontainers)) {
                    $updateData['tinggi'] = implode(',', $tinggiKontainers);
                }
            }

            // Handle multiple meter_kubik per kontainer
            if ($request->has('meter_kubik_kontainer') && is_array($request->meter_kubik_kontainer)) {
                $meterKubikKontainers = array_filter($request->meter_kubik_kontainer, function($value) {
                    return !empty(trim($value)) && is_numeric($value);
                });
                if (!empty($meterKubikKontainers)) {
                    $updateData['meter_kubik'] = implode(',', $meterKubikKontainers);
                }
            }

            // Handle multiple tonase per kontainer
            if ($request->has('tonase_kontainer') && is_array($request->tonase_kontainer)) {
                $tonaseKontainers = array_filter($request->tonase_kontainer, function($value) {
                    return !empty(trim($value)) && is_numeric($value);
                });
                if (!empty($tonaseKontainers)) {
                    $updateData['tonase'] = implode(',', $tonaseKontainers);
                }
            }

            // Only include status if the column exists and request has status
            if ($request->has('status') && Schema::hasColumn('tanda_terimas', 'status')) {
                $updateData['status'] = $request->status;
            }

            // If dimensi_items is present, format numeric values and store as JSON
            if ($request->has('dimensi_items') && is_array($request->dimensi_items)) {
                $formattedDimensiItems = [];
                foreach ($request->dimensi_items as $item) {
                    $formattedItem = [];
                    foreach ($item as $key => $value) {
                        if (in_array($key, ['panjang', 'lebar', 'tinggi', 'meter_kubik', 'tonase']) && is_numeric($value)) {
                            // Round to 3 decimal places to avoid excessive precision
                            $formattedItem[$key] = round((float) $value, 3);
                        } else {
                            $formattedItem[$key] = $value;
                        }
                    }
                    $formattedDimensiItems[] = $formattedItem;
                }
                $updateData['dimensi_items'] = json_encode($formattedDimensiItems);
            }

            $tandaTerima->update($updateData);

            // Sync nomor kontainer dan seal kembali ke Surat Jalan terkait
            if ($tandaTerima->surat_jalan_id) {
                $suratJalan = \App\Models\SuratJalan::find($tandaTerima->surat_jalan_id);
                if ($suratJalan) {
                    $suratJalanUpdateData = [];
                    
                    // Update nomor kontainer jika ada perubahan
                    if (isset($updateData['no_kontainer']) && $updateData['no_kontainer'] != $suratJalan->no_kontainer) {
                        $suratJalanUpdateData['no_kontainer'] = $updateData['no_kontainer'];
                    }
                    
                    // Update nomor seal jika ada perubahan
                    if (isset($updateData['no_seal']) && $updateData['no_seal'] != $suratJalan->no_seal) {
                        $suratJalanUpdateData['no_seal'] = $updateData['no_seal'];
                    }
                    
                    // Lakukan update jika ada perubahan
                    if (!empty($suratJalanUpdateData)) {
                        $suratJalan->update($suratJalanUpdateData);
                        
                        Log::info('Surat Jalan synced from Tanda Terima', [
                            'surat_jalan_id' => $suratJalan->id,
                            'no_surat_jalan' => $suratJalan->no_surat_jalan,
                            'updated_fields' => array_keys($suratJalanUpdateData),
                            'no_kontainer' => $suratJalanUpdateData['no_kontainer'] ?? null,
                            'no_seal' => $suratJalanUpdateData['no_seal'] ?? null,
                        ]);
                    }
                }
            }

            // Update related Prospek data and get count of updated prospeks
            $updatedProspekCount = $this->updateRelatedProspekData($tandaTerima, $request);

            Log::info('Tanda terima updated', [
                'tanda_terima_id' => $tandaTerima->id,
                'no_surat_jalan' => $tandaTerima->no_surat_jalan,
                'updated_by' => Auth::user()->name,
                'prospeks_updated' => $updatedProspekCount,
            ]);

            DB::commit();
            
            // Create success message with prospek update info
            $successMessage = 'Tanda terima berhasil diperbarui!';
            if ($updatedProspekCount > 0) {
                $successMessage .= " Berhasil mengupdate {$updatedProspekCount} prospek terkait dengan data volume, tonase, dan kuantitas terbaru.";
            }
            
            return redirect()->route('tanda-terima.index')
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating tanda terima: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui tanda terima: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified tanda terima
     */
    public function show(TandaTerima $tandaTerima)
    {
        $tandaTerima->load(['suratJalan', 'creator', 'updater']);

        return view('tanda-terima.show', compact('tandaTerima'));
    }

    /**
     * Remove the specified tanda terima from storage
     */
    public function destroy(TandaTerima $tandaTerima)
    {
        DB::beginTransaction();
        try {
            $noSuratJalan = $tandaTerima->no_surat_jalan;
            $tandaTerima->delete();

            Log::info('Tanda terima deleted', [
                'tanda_terima_id' => $tandaTerima->id,
                'no_surat_jalan' => $noSuratJalan,
                'deleted_by' => Auth::user()->name,
            ]);

            DB::commit();
            return redirect()->route('tanda-terima.index')
                ->with('success', 'Tanda terima berhasil dihapus!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting tanda terima: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus tanda terima: ' . $e->getMessage());
        }
    }

    /**
     * Add cargo container to prospek
     */
    public function addToProspek(TandaTerima $tandaTerima)
    {
        try {
            // Validasi no kontainer harus CARGO
            if (strtoupper($tandaTerima->no_kontainer) !== 'CARGO') {
                return back()->with('error', 'Hanya kontainer dengan no. kontainer CARGO yang dapat dimasukkan ke prospek!');
            }

            // Tentukan ukuran kontainer yang valid (hanya 20 atau 40)
            $ukuran = null;
            if ($tandaTerima->size) {
                // Jika size adalah '20' atau '40', gunakan langsung
                if (in_array($tandaTerima->size, ['20', '40'])) {
                    $ukuran = $tandaTerima->size;
                }
            }

            // Buat data prospek dari tanda terima
            $prospekData = [
                'tanggal' => $tandaTerima->tanggal_surat_jalan,
                'nama_supir' => $tandaTerima->supir ?: 'Tidak ada supir',
                'barang' => $tandaTerima->jenis_barang ?: 'CARGO',
                'pt_pengirim' => $tandaTerima->pengirim ?: 'Tidak ada pengirim',
                'ukuran' => $ukuran, // Hanya '20', '40', atau null
                'tipe' => 'CARGO', // Set tipe sebagai CARGO untuk kontainer cargo
                'nomor_kontainer' => $tandaTerima->no_kontainer,
                'no_seal' => $tandaTerima->no_seal ?: 'Tidak ada seal',
                'tujuan_pengiriman' => $tandaTerima->tujuan_pengiriman ?: 'Tidak ada tujuan',
                'nama_kapal' => $tandaTerima->estimasi_nama_kapal ?: 'Tidak ada nama kapal',
                'keterangan' => "Data dari tanda terima: {$tandaTerima->no_surat_jalan}. Kegiatan: {$tandaTerima->kegiatan}",
                'status' => 'aktif',
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ];

            $createdProspek = Prospek::create($prospekData);

            return back()->with('success', "Kontainer CARGO dari surat jalan {$tandaTerima->no_surat_jalan} berhasil dimasukkan ke prospek (ID: {$createdProspek->id})!");

        } catch (\Exception $e) {
            Log::error('Error adding cargo to prospek: ' . $e->getMessage());
            Log::error('TandaTerima data: ' . json_encode($tandaTerima->toArray()));
            return back()->with('error', 'Gagal memasukkan kontainer ke prospek: ' . $e->getMessage());
        }
    }

    /**
     * Update related Prospek data when TandaTerima is updated
     */
    private function updateRelatedProspekData(TandaTerima $tandaTerima, Request $request)
    {
        try {
            // Calculate totals from dimensi items or fallback to single values
            $totalVolume = 0;
            $totalTonase = 0;
            $kuantitas = 0;

            // Priority 1: Calculate from dimensi_items if available
            if ($request->has('dimensi_items') && is_array($request->dimensi_items)) {
                foreach ($request->dimensi_items as $item) {
                    if (isset($item['meter_kubik']) && is_numeric($item['meter_kubik'])) {
                        // Round to 3 decimal places to avoid excessive precision
                        $volume = round((float) $item['meter_kubik'], 3);
                        $totalVolume += $volume;
                    }
                    if (isset($item['tonase']) && is_numeric($item['tonase'])) {
                        // Round to 3 decimal places to avoid excessive precision
                        $tonase = round((float) $item['tonase'], 3);
                        $totalTonase += $tonase;
                    }
                }
            }

            // Priority 2: Use single meter_kubik and tonase values if dimensi_items not available
            if ($totalVolume == 0 && $request->filled('meter_kubik')) {
                // Round to 3 decimal places to avoid excessive precision
                $totalVolume = round((float) $request->meter_kubik, 3);
            }
            if ($totalTonase == 0 && $request->filled('tonase')) {
                // Round to 3 decimal places to avoid excessive precision
                $totalTonase = round((float) $request->tonase, 3);
            }

            // Calculate kuantitas from jumlah_kontainer array or single jumlah
            if ($request->has('jumlah_kontainer') && is_array($request->jumlah_kontainer)) {
                foreach ($request->jumlah_kontainer as $jumlah) {
                    if (is_numeric($jumlah)) {
                        $kuantitas += (int) $jumlah;
                    }
                }
            } elseif ($request->filled('jumlah')) {
                // Handle comma-separated values in jumlah field
                $jumlahArray = explode(',', $request->jumlah);
                foreach ($jumlahArray as $jumlah) {
                    if (is_numeric(trim($jumlah))) {
                        $kuantitas += (int) trim($jumlah);
                    }
                }
            }

            // Find related prospek records using multiple methods
            $prospeksToUpdate = collect();
            
            // Method 1: Find by surat_jalan_id (most reliable)
            if ($tandaTerima->surat_jalan_id) {
                $prospeksBySuratJalan = \App\Models\Prospek::where('surat_jalan_id', $tandaTerima->surat_jalan_id)->get();
                $prospeksToUpdate = $prospeksToUpdate->merge($prospeksBySuratJalan);
            }
            
            // Method 2: Find by no_surat_jalan if surat_jalan_id didn't yield results
            if ($prospeksToUpdate->isEmpty() && $tandaTerima->no_surat_jalan) {
                $prospeksByNoSuratJalan = \App\Models\Prospek::where('no_surat_jalan', $tandaTerima->no_surat_jalan)->get();
                $prospeksToUpdate = $prospeksToUpdate->merge($prospeksByNoSuratJalan);
            }
            
            // Method 3: Find by nomor_kontainer (fallback)
            if ($request->has('nomor_kontainer') && is_array($request->nomor_kontainer)) {
                $nomorKontainers = array_filter($request->nomor_kontainer, function($value) {
                    return !empty(trim($value));
                });
                
                if (!empty($nomorKontainers)) {
                    $prospeksByKontainer = \App\Models\Prospek::whereIn('nomor_kontainer', $nomorKontainers)->get();
                    $prospeksToUpdate = $prospeksToUpdate->merge($prospeksByKontainer);
                }
            }

            // Remove duplicates based on ID
            $prospeksToUpdate = $prospeksToUpdate->unique('id');

            // Update each related prospek
            $updatedCount = 0;
            foreach ($prospeksToUpdate as $prospek) {
                $updateFields = [
                    'tanda_terima_id' => $tandaTerima->id,
                    'updated_by' => Auth::id(),
                ];

                // Only update volume, tonase, kuantitas if we have calculated values
                if ($totalVolume > 0) {
                    $updateFields['total_volume'] = $totalVolume;
                }
                if ($totalTonase > 0) {
                    $updateFields['total_ton'] = $totalTonase;
                }
                if ($kuantitas > 0) {
                    $updateFields['kuantitas'] = $kuantitas;
                }

                $prospek->update($updateFields);
                $updatedCount++;

                Log::info('Prospek updated from TandaTerima', [
                    'prospek_id' => $prospek->id,
                    'tanda_terima_id' => $tandaTerima->id,
                    'nomor_kontainer' => $prospek->nomor_kontainer,
                    'surat_jalan_id' => $prospek->surat_jalan_id,
                    'no_surat_jalan' => $prospek->no_surat_jalan,
                    'total_volume' => $totalVolume,
                    'total_ton' => $totalTonase,
                    'kuantitas' => $kuantitas,
                    'update_method' => $prospek->surat_jalan_id == $tandaTerima->surat_jalan_id ? 'surat_jalan_id' : 
                                     ($prospek->no_surat_jalan == $tandaTerima->no_surat_jalan ? 'no_surat_jalan' : 'nomor_kontainer')
                ]);
            }

            Log::info('Updated related prospek data', [
                'tanda_terima_id' => $tandaTerima->id,
                'prospeks_found_total' => $prospeksToUpdate->count(),
                'prospeks_updated' => $updatedCount,
                'total_volume' => $totalVolume,
                'total_tonase' => $totalTonase,
                'kuantitas' => $kuantitas,
                'search_methods_used' => [
                    'surat_jalan_id' => $tandaTerima->surat_jalan_id ? true : false,
                    'no_surat_jalan' => $tandaTerima->no_surat_jalan ? true : false,
                    'nomor_kontainer' => $request->has('nomor_kontainer') ? true : false
                ]
            ]);

            return $updatedCount;

        } catch (\Exception $e) {
            Log::error('Error updating related prospek data: ' . $e->getMessage(), [
                'tanda_terima_id' => $tandaTerima->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // Don't throw exception to avoid breaking the main update process
            return 0;
        }
    }
}

