<?php

namespace App\Http\Controllers;

use App\Models\ManifestSugeng;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ManifestSugengController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'type' => 'nullable|in:fcl,lcl,ttsj',
            'search' => 'nullable|string|max:255',
        ]);

        $items = ManifestSugeng::query()
            ->with('shipperJb')
            ->when($filters['type'] ?? null, fn ($query, $type) => $query->where('source_type', $type))
            ->when(trim($filters['search'] ?? '') !== '', function ($query) use ($filters) {
                $search = trim($filters['search']);
                $query->where(fn ($query) => $query
                    ->where('nomor_tanda_terima', 'like', '%'.$search.'%')
                    ->orWhere('nomor_kontainer', 'like', '%'.$search.'%')
                    ->orWhere('pengirim', 'like', '%'.$search.'%'));
            })
            ->latest('dikirim_pada')
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        return view('manifest-sugeng.index', compact('items'));
    }

    public function show(ManifestSugeng $manifestSugeng): View
    {
        $manifestSugeng->load(['shipperJb', 'sender']);

        return view('manifest-sugeng.show', compact('manifestSugeng'));
    }

    public function destroy(ManifestSugeng $manifestSugeng): RedirectResponse
    {
        $manifestSugeng->delete();

        return redirect()->route('manifest-sugeng.index')
            ->with('success', 'Data Manifest Sugeng berhasil dihapus.');
    }
}
