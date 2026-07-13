<?php

namespace App\Http\Controllers;

use App\Models\EmailAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailAccountController extends Controller
{
    public function settings()
    {
        $account = EmailAccount::where('user_id', Auth::id())->first();
        return view('email.settings', compact('account'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'email_address' => 'required|email',
            'password' => 'required|string',
            'imap_host' => 'required|string',
            'imap_port' => 'required|integer',
            'imap_encryption' => 'nullable|string',
            'smtp_host' => 'required|string',
            'smtp_port' => 'required|integer',
            'smtp_encryption' => 'nullable|string',
        ]);
        
        // Membersihkan spasi, tab, enter, dan karakter whitespace lainnya pada sandi aplikasi
        $cleanPassword = preg_replace('/\s+/', '', $request->password);

        $account = EmailAccount::where('user_id', Auth::id())->first();

        $data = $request->all();
        $data['password'] = $cleanPassword;

        if ($account) {
            $account->update($data);
            $msg = 'Pengaturan email berhasil diperbarui!';
        } else {
            $data['user_id'] = Auth::id();
            EmailAccount::create($data);
            $msg = 'Akun email berhasil ditambahkan!';
        }

        return redirect()->route('email.settings')->with('success', $msg);
    }

    public function destroy()
    {
        $account = EmailAccount::where('user_id', Auth::id())->first();
        if ($account) {
            $account->delete();
        }
        return redirect()->route('email.settings')->with('success', 'Akun email berhasil dihapus (Logout).');
    }
}
