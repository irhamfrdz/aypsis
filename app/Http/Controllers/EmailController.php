<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmailAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Pagination\LengthAwarePaginator;
use Webklex\PHPIMAP\ClientManager;

class EmailController extends Controller
{
    private function getClient()
    {
        $account = EmailAccount::where('user_id', Auth::id())->first();

        if (!$account) {
            throw new \Exception('Akun email belum dikonfigurasi.');
        }

        $cm = new ClientManager();
        $client = $cm->make([
            'host'          => $account->imap_host,
            'port'          => $account->imap_port,
            'encryption'    => $account->imap_encryption,
            'validate_cert' => false,
            'username'      => $account->email_address,
            'password'      => $account->password,
            'authentication' => null,
            'protocol'      => 'imap'
        ]);
        
        $client->connect();
        return $client;
    }

    private function setupSmtp()
    {
        $account = EmailAccount::where('user_id', Auth::id())->first();

        if (!$account) {
            throw new \Exception('Akun email belum dikonfigurasi.');
        }

        Config::set('mail.mailers.smtp.host', $account->smtp_host);
        Config::set('mail.mailers.smtp.port', $account->smtp_port);
        Config::set('mail.mailers.smtp.encryption', $account->smtp_encryption);
        Config::set('mail.mailers.smtp.username', $account->email_address);
        Config::set('mail.mailers.smtp.password', $account->password);
        Config::set('mail.from.address', $account->email_address);
        Config::set('mail.from.name', Auth::user()->username);
    }

    /**
     * Mengambil email dari folder IMAP secara efisien (hanya header, tanpa body).
     */
    private function fetchFolder($folderNames, Request $request, $viewName)
    {
        set_time_limit(120);
        
        if (!EmailAccount::where('user_id', Auth::id())->exists()) {
            return redirect()->route('email.settings')->with('error', 'Silakan konfigurasikan email Anda terlebih dahulu.');
        }

        $client = $this->getClient();
        $folder = null;

        // Coba satu per satu nama folder
        foreach ((array) $folderNames as $name) {
            try {
                $folder = $client->getFolder($name);
                if ($folder) break;
            } catch (\Exception $e) {
                continue;
            }
        }

        if (!$folder) {
            return view($viewName, ['emails' => new LengthAwarePaginator([], 0, 15)]);
        }

        $page = $request->get('page', 1);
        $perPage = 15;

        // Ambil pesan dengan setFetchBody(false) agar hanya header
        $query = $folder->messages()->all()
            ->setFetchBody(false)
            ->setFetchFlags(true)
            ->limit($perPage, ($page - 1) * $perPage);

        $emails = $query->get();

        // Hitung total email di folder untuk paginasi
        $totalMessages = $folder->examine()['exists'] ?? $emails->count();

        $paginatedEmails = new LengthAwarePaginator(
            $emails,
            $totalMessages,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view($viewName, ['emails' => $paginatedEmails]);
    }

    public function inbox(Request $request)
    {
        try {
            return $this->fetchFolder(['INBOX'], $request, 'email.inbox');
        } catch (\Exception $e) {
            \Log::error('Email inbox error', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->route('email.settings')->with('error', 'Koneksi IMAP gagal. Pastikan pengaturan email Anda benar. Pesan error: ' . $e->getMessage());
        }
    }

    public function sent(Request $request)
    {
        try {
            return $this->fetchFolder(['[Gmail]/Sent Mail', 'Sent', 'Sent Messages', 'Sent Items'], $request, 'email.sent');
        } catch (\Exception $e) {
            \Log::error('Email sent error', ['message' => $e->getMessage()]);
            return redirect()->route('email.settings')->with('error', 'Koneksi IMAP gagal: ' . $e->getMessage());
        }
    }

    public function spam(Request $request)
    {
        try {
            return $this->fetchFolder(['[Gmail]/Spam', 'Spam', 'Junk'], $request, 'email.spam');
        } catch (\Exception $e) {
            \Log::error('Email spam error', ['message' => $e->getMessage()]);
            return redirect()->route('email.settings')->with('error', 'Koneksi IMAP gagal: ' . $e->getMessage());
        }
    }

    public function trash(Request $request)
    {
        try {
            return $this->fetchFolder(['[Gmail]/Trash', 'Trash', 'Deleted Items'], $request, 'email.trash');
        } catch (\Exception $e) {
            \Log::error('Email trash error', ['message' => $e->getMessage()]);
            return redirect()->route('email.settings')->with('error', 'Koneksi IMAP gagal: ' . $e->getMessage());
        }
    }

    public function create()
    {
        return view('email.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'recipient_email' => 'required|email',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        try {
            $this->setupSmtp();

            Mail::raw($request->body, function ($message) use ($request) {
                $message->to($request->recipient_email)
                        ->subject($request->subject);
            });
            return redirect()->route('email.sent')->with('success', 'Email berhasil dikirim!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengirim email: ' . $e->getMessage());
        }
    }

    public function show($message_uid)
    {
        try {
            set_time_limit(60);
            $client = $this->getClient();
            $folderNames = [
                'INBOX',
                '[Gmail]/Sent Mail', 'Sent', 'Sent Messages', 'Sent Items',
                '[Gmail]/Spam', 'Spam', 'Junk',
                '[Gmail]/Trash', 'Trash', 'Deleted Items',
            ];
            $email = null;

            foreach ($folderNames as $folderName) {
                try {
                    $folder = $client->getFolder($folderName);
                    if (!$folder) continue;
                    $email = $folder->messages()->getMessageByUid($message_uid);
                    if ($email) break;
                } catch (\Exception $e) {
                    continue;
                }
            }

            if (!$email) {
                return redirect()->route('email.inbox')->with('error', 'Email tidak ditemukan.');
            }

            // Mark as read
            try {
                $email->setFlag('Seen');
            } catch (\Exception $e) {
                // Ignore flag errors
            }

            return view('email.show', compact('email'));
        } catch (\Exception $e) {
            return redirect()->route('email.inbox')->with('error', 'Gagal memuat email: ' . $e->getMessage());
        }
    }

    public function moveToTrash($message_uid)
    {
        try {
            set_time_limit(60);
            $client = $this->getClient();
            $folder = $client->getFolder('INBOX');
            $email = $folder->messages()->getMessageByUid($message_uid);
            if ($email) {
                $email->move('[Gmail]/Trash');
                return redirect()->back()->with('success', 'Email berhasil dipindahkan ke Terhapus.');
            }
            return redirect()->back()->with('error', 'Email tidak ditemukan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memindahkan email: ' . $e->getMessage());
        }
    }

    public function markAsSpam($message_uid)
    {
        try {
            set_time_limit(60);
            $client = $this->getClient();
            $folder = $client->getFolder('INBOX');
            $email = $folder->messages()->getMessageByUid($message_uid);
            if ($email) {
                $email->move('[Gmail]/Spam');
                return redirect()->back()->with('success', 'Email berhasil ditandai sebagai Spam.');
            }
            return redirect()->back()->with('error', 'Email tidak ditemukan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menandai spam: ' . $e->getMessage());
        }
    }

    public function restore($message_uid)
    {
        try {
            set_time_limit(60);
            $client = $this->getClient();
            foreach (['[Gmail]/Trash', '[Gmail]/Spam'] as $folderName) {
                try {
                    $folder = $client->getFolder($folderName);
                    if (!$folder) continue;
                    $email = $folder->messages()->getMessageByUid($message_uid);
                    if ($email) {
                        $email->move('INBOX');
                        return redirect()->back()->with('success', 'Email berhasil dikembalikan ke Kotak Masuk.');
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
            return redirect()->back()->with('error', 'Email tidak ditemukan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengembalikan email: ' . $e->getMessage());
        }
    }

    public function forceDelete($message_uid)
    {
        try {
            set_time_limit(60);
            $client = $this->getClient();
            $folder = $client->getFolder('[Gmail]/Trash') ?? $client->getFolder('Trash');
            if ($folder) {
                $email = $folder->messages()->getMessageByUid($message_uid);
                if ($email) {
                    $email->delete(true);
                    return redirect()->back()->with('success', 'Email berhasil dihapus permanen.');
                }
            }
            return redirect()->back()->with('error', 'Email tidak ditemukan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus email: ' . $e->getMessage());
        }
    }
}
