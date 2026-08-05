<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ChatFaqController extends Controller
{
    public function index()
    {
        $faqs = \App\Models\ChatFaq::orderBy('id', 'desc')->get();
        return view('chat.faq', compact('faqs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'is_active' => 'nullable|boolean'
        ]);

        \App\Models\ChatFaq::create([
            'question' => $request->question,
            'answer' => $request->answer,
            'is_active' => $request->has('is_active') ? true : false
        ]);

        return redirect()->back()->with('success', 'FAQ berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'is_active' => 'nullable|boolean'
        ]);

        $faq = \App\Models\ChatFaq::findOrFail($id);
        $faq->update([
            'question' => $request->question,
            'answer' => $request->answer,
            'is_active' => $request->has('is_active') ? true : false
        ]);

        return redirect()->back()->with('success', 'FAQ berhasil diupdate!');
    }

    public function destroy($id)
    {
        $faq = \App\Models\ChatFaq::findOrFail($id);
        $faq->delete();

        return redirect()->back()->with('success', 'FAQ berhasil dihapus!');
    }
}
