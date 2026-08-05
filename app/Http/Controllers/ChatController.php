<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use Carbon\Carbon;

class ChatController extends Controller
{
    // === Admin Backend Methods ===

    public function index()
    {
        // Get all unique session_ids with their latest message
        $sessions = Chat::select('session_id')
            ->selectRaw('MAX(created_at) as last_activity')
            ->selectRaw('SUM(CASE WHEN is_read = 0 AND is_admin = 0 THEN 1 ELSE 0 END) as unread_count')
            ->groupBy('session_id')
            ->orderBy('last_activity', 'desc')
            ->get();

        $chats = [];
        foreach ($sessions as $session) {
            $latest = Chat::where('session_id', $session->session_id)->orderBy('created_at', 'desc')->first();
            if ($latest) {
                $session->name = $latest->name ?? 'Guest';
                $session->last_message = $latest->message;
                $chats[] = $session;
            }
        }

        return view('chat.index', compact('chats'));
    }

    public function show($sessionId)
    {
        // Mark all messages from user as read
        Chat::where('session_id', $sessionId)
            ->where('is_admin', false)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = Chat::where('session_id', $sessionId)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'messages' => $messages
        ]);
    }

    public function reply(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
            'message' => 'required|string'
        ]);

        $chat = Chat::create([
            'session_id' => $request->session_id,
            'name' => 'Admin',
            'message' => $request->message,
            'is_admin' => true,
            'is_read' => false,
        ]);

        return response()->json([
            'success' => true,
            'chat' => $chat
        ]);
    }

    public function destroy($sessionId)
    {
        Chat::where('session_id', $sessionId)->delete();

        return response()->json([
            'success' => true
        ]);
    }

    // === Frontend API Methods ===

    public function getMessages(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string'
        ]);

        // Mark admin messages as read for this session
        Chat::where('session_id', $request->session_id)
            ->where('is_admin', true)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = Chat::where('session_id', $request->session_id)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'messages' => $messages
        ]);
    }

    public function getFaqs()
    {
        $faqs = \App\Models\ChatFaq::where('is_active', true)->orderBy('id', 'asc')->get();
        return response()->json([
            'faqs' => $faqs
        ]);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
            'message' => 'required|string',
            'is_faq' => 'nullable|boolean',
            'faq_id' => 'nullable|integer'
        ]);

        $chat = Chat::create([
            'session_id' => $request->session_id,
            'name' => $request->name ?? 'Visitor',
            'message' => $request->message,
            'is_admin' => false,
            'is_read' => false,
        ]);

        $autoReply = null;
        
        // Check if this is an FAQ message
        if ($request->has('is_faq') && $request->is_faq && $request->has('faq_id')) {
            $faq = \App\Models\ChatFaq::where('id', $request->faq_id)->where('is_active', true)->first();
            if ($faq) {
                // Instantly send the auto reply
                $autoReply = Chat::create([
                    'session_id' => $request->session_id,
                    'name' => 'Virtual Assistant',
                    'message' => $faq->answer,
                    'is_admin' => true,
                    'is_read' => false,
                ]);
            }
        } else {
            // Auto-responder logic based on keywords
            $userMessage = strtolower($request->message);
            $userMessage = preg_replace('/[^\w\s]/', '', $userMessage); // Remove punctuation
            
            // Get all active FAQs
            $faqs = \App\Models\ChatFaq::where('is_active', true)->get();
            
            $bestMatch = null;
            $highestScore = 0;
            
            // Common Indonesian stop words to ignore
            $stopWords = ['apa', 'apakah', 'bagaimana', 'dimana', 'kapan', 'siapa', 'mengapa', 'kenapa', 'bisa', 'yang', 'di', 'ke', 'dari', 'dan', 'atau', 'untuk', 'saya', 'kami', 'tolong', 'cara', 'halo', 'min', 'admin'];
            
            foreach ($faqs as $faq) {
                $faqQuestion = strtolower($faq->question);
                $faqQuestion = preg_replace('/[^\w\s]/', '', $faqQuestion);
                
                // Split question into words
                $faqWords = array_diff(explode(' ', $faqQuestion), $stopWords);
                $faqWords = array_filter($faqWords); // Remove empty
                
                if (count($faqWords) === 0) continue;
                
                $matchCount = 0;
                foreach ($faqWords as $word) {
                    if (strpos($userMessage, $word) !== false) {
                        $matchCount++;
                    }
                }
                
                $score = $matchCount / count($faqWords);
                
                // Match if at least 50% of the significant words in the FAQ question are present in the user's message
                if ($score >= 0.5 && $score > $highestScore) {
                    $highestScore = $score;
                    $bestMatch = $faq;
                }
            }
            
            if ($bestMatch) {
                $autoReply = Chat::create([
                    'session_id' => $request->session_id,
                    'name' => 'Virtual Assistant',
                    'message' => $bestMatch->answer,
                    'is_admin' => true,
                    'is_read' => false,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'chat' => $chat,
            'auto_reply' => $autoReply
        ]);
    }
}
