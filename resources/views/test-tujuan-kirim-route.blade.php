Route Tujuan Kirim Test

✅ Route: {{ route('tujuan-kirim.index') }}
✅ User: {{ Auth::user() ? Auth::user()->username : 'Not logged in' }}
✅ Can View: {{ Auth::user() && Auth::user()->can('master-tujuan-kirim-view') ? 'YES' : 'NO' }}

<br><br>

<a href="{{ route('tujuan-kirim.index') }}" class="btn btn-primary">
    Go to Tujuan Kirim Index
</a>

<br><br>

<a href="{{ route('dashboard') }}">Back to Dashboard</a>