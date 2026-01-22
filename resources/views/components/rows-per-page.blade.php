{{-- Rows Per Page Selection Component --}}
@php
    $routeName = $routeName ?? Route::currentRouteName() ?? 'master-kapal.index';
    $entityName = $entityName ?? 'data';
    $entityNamePlural = $entityNamePlural ?? 'data';
    // Default to 10 so it matches the available select options
    $currentPerPage = request('per_page', 10);
@endphp

{{-- Rows Per Page Selection --}}
<div class="mt-3 flex items-center justify-between text-sm text-gray-600">
    <div class="flex items-center space-x-2">
        <span>Tampilkan</span>
        <form method="GET" action="{{ route($routeName) }}" class="inline">
            {{-- Preserve existing search and sort parameters --}}
            @foreach(request()->query() as $key => $value)
                @if($key !== 'per_page' && $key !== 'page')
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endif
            @endforeach

            <select name="per_page"
                    onchange="this.form.submit()"
                    class="mx-1 px-3 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 bg-white">
                <option value="10" {{ $currentPerPage == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ $currentPerPage == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ $currentPerPage == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ $currentPerPage == 100 ? 'selected' : '' }}>100</option>
                <option value="200" {{ $currentPerPage == 200 ? 'selected' : '' }}>200</option>
            </select>
        </form>
        <span>baris per halaman</span>
    </div>

    @if(isset($paginator) && $paginator->total() > 0)
        <div class="text-sm text-gray-500">
            Menampilkan {{ $paginator->firstItem() }} - {{ $paginator->lastItem() }} dari {{ $paginator->total() }} total {{ $entityNamePlural }}
        </div>
    @endif
</div>
