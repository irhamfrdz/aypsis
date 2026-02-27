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
                    'asuransi_paths' => is_string($item->asuransi_path) && str_starts_with($item->asuransi_path, '[') ? json_decode($item->asuransi_path, true) : ($item->asuransi_path ? [$item->asuransi_path] : []),
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
                    'asuransi_paths' => is_string($item->asuransi_path) && str_starts_with($item->asuransi_path, '[') ? json_decode($item->asuransi_path, true) : ($item->asuransi_path ? [$item->asuransi_path] : []),
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
                    'asuransi_paths' => is_string($item->asuransi_path) && str_starts_with($item->asuransi_path, '[') ? json_decode($item->asuransi_path, true) : ($item->asuransi_path ? [$item->asuransi_path] : []),
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
            'asuransi_file' => 'required|array',
            'asuransi_file.*' => 'file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $model = $this->getModel($sourceType, $id);
        
        if ($request->hasFile('asuransi_file')) {
            // Keep existing files
            $existingPathArray = [];
            if ($model->asuransi_path) {
                if (is_string($model->asuransi_path) && str_starts_with($model->asuransi_path, '[')) {
                    $existingPathArray = json_decode($model->asuransi_path, true) ?? [];
                } elseif (!empty($model->asuransi_path)) {
                    $existingPathArray = [$model->asuransi_path];
                }
            }

            $paths = $existingPathArray;
            foreach ($request->file('asuransi_file') as $file) {
                 $paths[] = $file->store('asuransi_tanda_terima', 'public');
            }

            $model->update([
                'asuransi_path' => json_encode($paths),
                'asuransi_uploaded_at' => now(),
                'asuransi_uploaded_by' => Auth::id(),
            ]);
        }

        return back()->with('success', 'Dokumen asuransi berhasil diupload.');
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
