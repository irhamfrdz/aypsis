<?php

namespace App\Http\Controllers;

use App\Models\TandaTerima;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ApprovalTandaTerimaPelabuhanController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status', 'pending');
        if (! in_array($status, ['pending', 'approved', 'all'], true)) {
            $status = 'pending';
        }

        $search = trim((string) $request->query('search', ''));
        $query = TandaTerima::query()->whereIn('status', ['draft', 'submitted', 'approved']);

        if ($status === 'pending') {
            $query->whereIn('status', ['draft', 'submitted']);
        } elseif ($status === 'approved') {
            $query->where('status', 'approved');
        }

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('no_surat_jalan', 'like', "%{$search}%")
                    ->orWhere('no_kontainer', 'like', "%{$search}%")
                    ->orWhere('supir', 'like', "%{$search}%");
            });
        }

        $tandaTerimas = $query->latest('id')->paginate(20)->withQueryString();

        return view('approval-tanda-terima-pelabuhan.index', compact('tandaTerimas', 'status', 'search'));
    }

    public function approve(Request $request, TandaTerima $tandaTerima): RedirectResponse
    {
        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'tanggal_checkpoint_supir' => ['required', 'date'],
        ]);

        DB::transaction(function () use ($tandaTerima, $validated, $request) {
            $tandaTerima = TandaTerima::whereKey($tandaTerima->id)->lockForUpdate()->firstOrFail();
            if (! in_array($tandaTerima->status, ['draft', 'submitted', 'approved'], true)) {
                abort(422, 'Status tanda terima ini tidak dapat disetujui.');
            }

            $tandaTerima->forceFill([
                'tanggal' => $validated['tanggal'],
                'tanggal_checkpoint_supir' => $validated['tanggal_checkpoint_supir'],
                'status' => 'approved',
                'updated_by' => $request->user()->id,
            ])->save();

            if ($tandaTerima->surat_jalan_id) {
                $tandaTerima->suratJalan()->update([
                    'tanggal_checkpoint' => $validated['tanggal_checkpoint_supir'],
                ]);
            }
        });

        return redirect()->route('approval-tanda-terima-pelabuhan.index', $request->only('status', 'search'))
            ->with('success', 'Tanda terima pelabuhan berhasil disetujui.');
    }
}
