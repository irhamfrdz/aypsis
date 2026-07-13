@extends('email.layout')

@section('email_content')
<div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
    <h2 class="text-lg font-medium text-gray-900">Pengaturan Akun Email</h2>
</div>

<div class="p-6">
    <div class="mb-6 bg-blue-50 border-l-4 border-blue-400 p-4 rounded-md">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    Masukkan detail server IMAP dan SMTP dari penyedia email Anda. Password akan dienkripsi di dalam database.
                    Jika Anda menggunakan Gmail, pastikan IMAP diaktifkan dan gunakan <strong>App Password (Sandi Aplikasi)</strong>, bukan password utama email Anda.
                </p>
            </div>
        </div>
    </div>

    <form action="{{ route('email.settings.store') }}" method="POST">
        @csrf
        
        <h3 class="text-md font-medium text-gray-900 mb-4 border-b pb-2">Kredensial Dasar</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <label for="email_address" class="block text-sm font-medium text-gray-700 mb-1">Alamat Email</label>
                <input type="email" name="email_address" id="email_address" value="{{ old('email_address', $account->email_address ?? '') }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="user@gmail.com" required>
                @error('email_address')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password / Sandi Aplikasi</label>
                <input type="password" name="password" id="password" value="{{ old('password', $account->password ?? '') }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <h3 class="text-md font-medium text-gray-900 mb-4 border-b pb-2">Pengaturan IMAP (Surat Masuk)</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div>
                <label for="imap_host" class="block text-sm font-medium text-gray-700 mb-1">IMAP Host</label>
                <input type="text" name="imap_host" id="imap_host" value="{{ old('imap_host', $account->imap_host ?? 'imap.gmail.com') }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
            </div>
            <div>
                <label for="imap_port" class="block text-sm font-medium text-gray-700 mb-1">IMAP Port</label>
                <input type="number" name="imap_port" id="imap_port" value="{{ old('imap_port', $account->imap_port ?? 993) }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
            </div>
            <div>
                <label for="imap_encryption" class="block text-sm font-medium text-gray-700 mb-1">Enkripsi (ssl/tls)</label>
                <input type="text" name="imap_encryption" id="imap_encryption" value="{{ old('imap_encryption', $account->imap_encryption ?? 'ssl') }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
            </div>
        </div>

        <h3 class="text-md font-medium text-gray-900 mb-4 border-b pb-2">Pengaturan SMTP (Surat Keluar)</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div>
                <label for="smtp_host" class="block text-sm font-medium text-gray-700 mb-1">SMTP Host</label>
                <input type="text" name="smtp_host" id="smtp_host" value="{{ old('smtp_host', $account->smtp_host ?? 'smtp.gmail.com') }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
            </div>
            <div>
                <label for="smtp_port" class="block text-sm font-medium text-gray-700 mb-1">SMTP Port</label>
                <input type="number" name="smtp_port" id="smtp_port" value="{{ old('smtp_port', $account->smtp_port ?? 465) }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
            </div>
            <div>
                <label for="smtp_encryption" class="block text-sm font-medium text-gray-700 mb-1">Enkripsi (ssl/tls)</label>
                <input type="text" name="smtp_encryption" id="smtp_encryption" value="{{ old('smtp_encryption', $account->smtp_encryption ?? 'ssl') }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
            </div>
        </div>

        <div class="flex justify-between items-center pt-4 border-t border-gray-200">
            <div>
                @if($account)
                <button type="button" onclick="document.getElementById('delete-form').submit();" class="text-sm text-red-600 hover:text-red-900 font-medium">
                    <i class="fas fa-trash-alt mr-1"></i> Hapus Akun & Logout
                </button>
                @endif
            </div>
            <button type="submit" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-save mr-2 mt-0.5"></i> Simpan Pengaturan
            </button>
        </div>
    </form>
    
    @if($account)
    <form id="delete-form" action="{{ route('email.settings.destroy') }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
    @endif
</div>
@endsection
