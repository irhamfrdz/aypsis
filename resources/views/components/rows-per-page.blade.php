{{-- Rows Per Page Selection Component --}}
{{-- Usage: @include('components.rows-per-page', [
    'routeName' => 'your.route.name',
    'paginator' => $yourPaginator,
    'entityName' => 'nama entity (karyawan, user, dll)',
    'entityNamePlural' => 'nama entity plural (karyawan, user, dll)'
]) --}}

{{-- Rows Per Page Selection --}}
<div class="mt-3 flex items-center justify-between text-sm text-gray-600">
    <div class="flex items-center space-x-2">
        <span>Tampilkan</span>
        <form method="GET" action="{{ route($routeName ?? 'current.route') }}" class="inline">
            {{-- Preserve existing search and sort parameters --}}
            @foreach(request()->query() as $key => $value)
                @if($key !== 'per_page' && $key !== 'page')
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endif
            @endforeach

            <select name="per_page"
                    onchange="this.form.submit()"
                    class="mx-1 px-3 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 bg-white">
                <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                <option value="50" {{ request('per_page', 15) == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ request('per_page', 15) == 100 ? 'selected' : '' }}>100</option>
            </select>
        </form>
        <span>baris per halaman</span>
    </div>

    @if($paginator->total() > 0)
        <div class="text-sm text-gray-500">
            Menampilkan {{ $paginator->firstItem() }} - {{ $paginator->lastItem() }} dari {{ $paginator->total() }} total {{ $entityNamePlural ?? 'data' }}
        </div>
    @endif
</div>
