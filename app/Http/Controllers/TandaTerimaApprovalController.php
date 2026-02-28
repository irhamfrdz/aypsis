<?php

namespace App\Http\Controllers;

use App\Models\TandaTerima;
use App\Models\TandaTerimaTanpaSuratJalan;
use App\Models\TandaTerimaLcl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TandaTerimaApprovalController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $type = $request->get('type', 'all');
        $status = $request->get('status', 'pending'); // pending, approved, all

        $tandaTerimaQuery = TandaTerima::query();
        $ttsjQuery = TandaTerimaTanpaSuratJalan::query();
        $lclQuery = TandaTerimaLcl::query();

        if ($search) {
            $tandaTerimaQuery->where(function($q) use ($search) {
                $q->where('no_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('penerima', 'like', "%{$search}%")
                  ->orWhere('pengirim', 'like', "%{$search}%");
            });
            $ttsjQuery->where(function($q) use ($search) {
                $q->where('no_tanda_terima', 'like', "%{$search}%")
                  ->orWhere('penerima', 'like', "%{$search}%")
                  ->orWhere('pengirim', 'like', "%{$search}%");
            });
            $lclQuery->where(function($q) use ($search) {
                $q->where('nomor_tanda_terima', 'like', "%{$search}%")
                  ->orWhere('nama_penerima', 'like', "%{$search}%")
                  ->orWhere('nama_pengirim', 'like', "%{$search}%");
            });
        }

        if ($status !== 'all') {
            $isApproved = $status === 'approved';
            $tandaTerimaQuery->where('is_asuransi_approved', $isApproved);
            $ttsjQuery->where('is_asuransi_approved', $isApproved);
            $lclQuery->where('is_asuransi_approved', $isApproved);
        }

        $results = collect();

        if ($type === 'all' || $type === 'fcl') {
            $tandaTerimaQuery->latest()->limit(100)->get()->each(function($item) use ($results) {
                $results->push([
                    'id' => $item->id,
                    'type' => 'FCL',
                    'source_type' => 'fcl',
                    'number' => $item->no_surat_jalan ?: ('TT-' . $item->id),
                    'date' => $item->tanggal ?: $item->tanggal_surat_jalan,
                    'penerima' => $item->penerima,
                    'pengirim' => $item->pengirim,
                    'asuransi_paths' => $this->getDocumentsArray($item),
                    'is_approved' => $item->is_asuransi_approved,
                    'approved_at' => $item->asuransi_approved_at,
                    'keterangan' => $item->asuransi_keterangan,
                ]);
            });
        }

        if ($type === 'all' || $type === 'ttsj') {
            $ttsjQuery->latest()->limit(100)->get()->each(function($item) use ($results) {
                $results->push([
                    'id' => $item->id,
                    'type' => 'TTSJ',
                    'source_type' => 'ttsj',
                    'number' => $item->no_tanda_terima ?: $item->nomor_tanda_terima ?: ('TTSJ-' . $item->id),
                    'date' => $item->tanggal_tanda_terima,
                    'penerima' => $item->penerima,
                    'pengirim' => $item->pengirim,
                    'asuransi_paths' => $this->getDocumentsArray($item),
                    'is_approved' => $item->is_asuransi_approved,
                    'approved_at' => $item->asuransi_approved_at,
                    'keterangan' => $item->asuransi_keterangan,
                ]);
            });
        }

        if ($type === 'all' || $type === 'lcl') {
            $lclQuery->latest()->limit(100)->get()->each(function($item) use ($results) {
                $results->push([
                    'id' => $item->id,
                    'type' => 'LCL',
                    'source_type' => 'lcl',
                    'number' => $item->nomor_tanda_terima ?: ('LCL-' . $item->id),
                    'date' => $item->tanggal_tanda_terima,
                    'penerima' => $item->nama_penerima,
                    'pengirim' => $item->nama_pengirim,
                    'asuransi_paths' => $this->getDocumentsArray($item),
                    'is_approved' => $item->is_asuransi_approved,
                    'approved_at' => $item->asuransi_approved_at,
                    'keterangan' => $item->asuransi_keterangan,
                ]);
            });
        }

        $data = $results->sortByDesc(function($item) {
            return $item['date'] ? Carbon::parse($item['date'])->timestamp : 0;
        });

        return view('approval-tanda-terima.index', compact('data', 'type', 'status', 'search'));
    }

    public function upload(Request $request, $sourceType, $id)
    {
        $request->validate([
            'file_ppbj' => 'nullable|array',
            'file_ppbj.*' => 'file|mimes:pdf,jpg,jpeg,png|max:10240',
            'file_packing_list' => 'nullable|array',
            'file_packing_list.*' => 'file|mimes:pdf,jpg,jpeg,png|max:10240',
            'file_invoice' => 'nullable|array',
            'file_invoice.*' => 'file|mimes:pdf,jpg,jpeg,png|max:10240',
            'file_faktur_pajak' => 'nullable|array',
            'file_faktur_pajak.*' => 'file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $model = $this->getModel($sourceType, $id);
        
        $documentColumns = [
            'file_ppbj' => 'dokumen_ppbj', 
            'file_packing_list' => 'dokumen_packing_list', 
            'file_invoice' => 'dokumen_invoice', 
            'file_faktur_pajak' => 'dokumen_faktur_pajak'
        ];

        $uploaded = false;
        $updateData = [];

        foreach ($documentColumns as $inputName => $column) {
            if ($request->hasFile($inputName)) {
                $rawExisting = $model->{$column};
                $existingFiles = [];
                
                if (is_array($rawExisting)) {
                    $existingFiles = $rawExisting;
                } elseif (is_string($rawExisting) && !empty($rawExisting)) {
                    $existingFiles = json_decode($rawExisting, true) ?? [];
                }

                foreach ($request->file($inputName) as $file) {
                    $existingFiles[] = $file->store('asuransi_tanda_terima', 'public');
                    $uploaded = true;
                }
                $updateData[$column] = json_encode($existingFiles);
            }
        }

        if ($uploaded) {
            $updateData['asuransi_uploaded_at'] = now();
            $updateData['asuransi_uploaded_by'] = Auth::id();
            $model->update($updateData);
        }

        return back()->with('success', 'Dokumen berhasil diupload.');
    }

    public function deleteDocument($sourceType, $id, $column, $index)
    {
        $model = $this->getModel($sourceType, $id);
        
        $existingPathArray = [];
        if ($model->{$column}) {
            if (is_array($model->{$column})) {
                $existingPathArray = $model->{$column};
            } elseif (is_string($model->{$column}) && str_starts_with($model->{$column}, '[')) {
                $existingPathArray = json_decode($model->{$column}, true) ?? [];
            } elseif (!empty($model->{$column})) {
                $existingPathArray = [$model->{$column}];
            }
        }

        if (isset($existingPathArray[$index])) {
            $pathData = $existingPathArray[$index];
            $pathToDelete = is_array($pathData) ? ($pathData['path'] ?? $pathData) : $pathData;
            
            if (Storage::disk('public')->exists($pathToDelete)) {
                Storage::disk('public')->delete($pathToDelete);
            }
            array_splice($existingPathArray, $index, 1);
            
            $model->update([
                $column => count($existingPathArray) > 0 ? json_encode($existingPathArray) : null,
            ]);

            return back()->with('success', 'Dokumen berhasil dihapus.');
        }

        return back()->with('error', 'Dokumen tidak ditemukan.');
    }

    public function approve(Request $request, $sourceType, $id)
    {
        $model = $this->getModel($sourceType, $id);
        
        $model->update([
            'is_asuransi_approved' => true,
            'asuransi_approved_at' => now(),
            'asuransi_approved_by' => Auth::id(),
            'asuransi_keterangan' => $request->keterangan,
        ]);

        return back()->with('success', 'Tanda terima berhasil disetujui.');
    }

    public function reject(Request $request, $sourceType, $id)
    {
        $model = $this->getModel($sourceType, $id);
        
        $model->update([
            'is_asuransi_approved' => false,
            'asuransi_approved_at' => null,
            'asuransi_approved_by' => null,
            'asuransi_keterangan' => $request->keterangan,
        ]);

        return back()->with('info', 'Status approval dibatalkan.');
    }

    protected function getDocumentsArray($item)
    {
        $allDocuments = [];

        // Legacy support
        if ($item->asuransi_path) {
            $legacyPaths = is_string($item->asuransi_path) && str_starts_with($item->asuransi_path, '[') ? json_decode($item->asuransi_path, true) : [$item->asuransi_path];
            foreach ($legacyPaths as $idx => $p) {
                $allDocuments[] = [
                    'type' => is_array($p) && isset($p['type']) ? $p['type'] : 'Lainnya',
                    'path' => is_array($p) && isset($p['path']) ? $p['path'] : $p,
                    'column' => 'asuransi_path',
                    'original_index' => $idx
                ];
            }
        }

        // New separate columns
        $cols = [
            'dokumen_ppbj' => 'PPBJ',
            'dokumen_packing_list' => 'Packing List',
            'dokumen_invoice' => 'Invoice',
            'dokumen_faktur_pajak' => 'Faktur Pajak'
        ];

        foreach ($cols as $col => $typeName) {
            if ($item->{$col}) {
                $arr = json_decode($item->{$col}, true) ?? [];
                foreach ($arr as $idx => $p) {
                    $allDocuments[] = [
                        'type' => $typeName,
                        'path' => $p,
                        'column' => $col,
                        'original_index' => $idx
                    ];
                }
            }
        }

        return $allDocuments;
    }

    protected function getModel($sourceType, $id)
    {
        switch ($sourceType) {
            case 'fcl': return TandaTerima::findOrFail($id);
            case 'ttsj': return TandaTerimaTanpaSuratJalan::findOrFail($id);
            case 'lcl': return TandaTerimaLcl::findOrFail($id);
            default: abort(404);
        }
    }
}
