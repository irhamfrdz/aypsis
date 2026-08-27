<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\WaTemplate;
use Illuminate\Http\Request;

class WaTemplateController extends Controller
{
    public function index()
    {
        $templates = WaTemplate::orderBy('id', 'desc')->get();
        return view('master.wa-templates.index', compact('templates'));
    }

    public function create()
    {
        return view('master.wa-templates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_template' => 'required|string|max:255',
            'isi_template' => 'required|string',
            'is_active' => 'boolean',
        ]);

        WaTemplate::create([
            'nama_template' => $request->nama_template,
            'isi_template' => $request->isi_template,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('master.wa-templates.index')->with('success', 'Template WA berhasil ditambahkan.');
    }

    public function edit(WaTemplate $wa_template)
    {
        return view('master.wa-templates.edit', compact('wa_template'));
    }

    public function update(Request $request, WaTemplate $wa_template)
    {
        $request->validate([
            'nama_template' => 'required|string|max:255',
            'isi_template' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $wa_template->update([
            'nama_template' => $request->nama_template,
            'isi_template' => $request->isi_template,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('master.wa-templates.index')->with('success', 'Template WA berhasil diperbarui.');
    }

    public function destroy(WaTemplate $wa_template)
    {
        $wa_template->delete();
        return redirect()->route('master.wa-templates.index')->with('success', 'Template WA berhasil dihapus.');
    }
}
