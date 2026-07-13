@extends('email.layout')

@section('email_content')
<div class="border-b border-gray-200 px-6 py-4">
    <h2 class="text-lg font-medium text-gray-900">Tulis Email Baru (SMTP)</h2>
</div>

<div class="p-6">
    <form action="{{ route('email.store') }}" method="POST">
        @csrf
        
        <div class="mb-4">
            <label for="recipient_email" class="block text-sm font-medium text-gray-700 mb-1">Kepada (Alamat Email)</label>
            <input type="email" name="recipient_email" id="recipient_email" value="{{ request('reply_to', old('recipient_email')) }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="contoh@gmail.com" required>
            @error('recipient_email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subjek</label>
            <input type="text" name="subject" id="subject" value="{{ request('subject', old('subject')) }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
            @error('subject')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="body" class="block text-sm font-medium text-gray-700 mb-1">Pesan</label>
            <textarea name="body" id="body" rows="12" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" required>{{ old('body') }}</textarea>
            @error('body')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end">
            <a href="{{ route('email.inbox') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3">
                Batal
            </a>
            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-paper-plane mr-2 mt-0.5"></i> Kirim Email
            </button>
        </div>
    </form>
</div>
@endsection
