{{-- Modern Pagination Component --}}
{{-- Usage: @include('components.modern-pagination', ['paginator' => $yourPaginator, 'routeName' => 'your.route.name']) --}}

@if($paginator->hasPages())
    <div class="px-4 py-2 border-t border-gray-200 bg-gradient-to-r from-gray-50 to-white">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-2">
            {{-- Left Section: Page Statistics --}}
            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                <div class="flex items-center space-x-2 text-xs">
                    <span class="font-semibold text-gray-900">{{ $paginator->total() }}</span>
                    <span class="text-gray-600">total data</span>
                </div>

                <div class="flex items-center space-x-2 text-xs">
                    <span class="font-medium text-gray-900">{{ $paginator->firstItem() }}-{{ $paginator->lastItem() }}</span>
                    <span class="text-gray-600">ditampilkan</span>
                </div>

                <div class="flex items-center space-x-2 text-xs">
                    <span class="font-medium text-gray-900">{{ $paginator->currentPage() }}</span>
                    <span class="text-gray-600">dari</span>
                    <span class="font-medium text-gray-900">{{ $paginator->lastPage() }}</span>
                    <span class="text-gray-600">halaman</span>
                </div>
            </div>

            {{-- Right Section: Navigation Controls --}}
            <div class="flex flex-col sm:flex-row items-center gap-2">
                {{-- Page Jump Control --}}
                <div class="flex items-center bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <form id="pageJumpForm" method="GET" action="{{ $routeName ? route($routeName) : url()->current() }}" class="flex items-center">
                        {{-- Preserve existing query parameters --}}
                        @foreach(request()->query() as $key => $value)
                            @if($key !== 'page')
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach

                        <input type="number"
                               id="page_jump"
                               name="page"
                               min="1"
                               max="{{ $paginator->lastPage() }}"
                               value="{{ $paginator->currentPage() }}"
                               class="w-16 px-2 py-1 text-xs border-0 focus:outline-none focus:ring-0 text-center font-medium"
                               placeholder="{{ $paginator->lastPage() }}">
                        <button type="submit"
                                class="px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white transition-colors duration-200 border-l border-blue-500">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </button>
                    </form>
                </div>

                {{-- Enhanced Pagination Links --}}
                <div class="flex items-center bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    {{-- Previous Button --}}
                    @if($paginator->onFirstPage())
                        <div class="px-2 py-1 bg-gray-100 border-r border-gray-200 text-gray-400 text-xs">
                            ‹
                        </div>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}"
                           class="px-2 py-1 bg-gray-50 hover:bg-gray-100 border-r border-gray-200 transition-colors duration-200 text-gray-600 hover:text-gray-900 text-xs">
                            ‹
                        </a>
                    @endif

                    {{-- Page Numbers dengan Smart Range --}}
                    <div class="flex items-center">
                        @php
                            $current = $paginator->currentPage();
                            $last = $paginator->lastPage();
                            $start = max(1, $current - 2);
                            $end = min($last, $current + 2);

                            // Pastikan kita selalu menampilkan 5 link (jika memungkinkan)
                            if ($end - $start < 4) {
                                if ($start == 1) {
                                    $end = min($last, $start + 4);
                                } else {
                                    $start = max(1, $end - 4);
                                }
                            }
                        @endphp

                        {{-- First page link jika current page > 3 --}}
                        @if($start > 1)
                            <a href="{{ $paginator->url(1) }}"
                               class="px-3 py-1 hover:bg-blue-50 text-gray-700 hover:text-blue-600 font-medium border-r border-gray-200 transition-colors duration-200 text-xs">
                                1
                            </a>
                            @if($start > 2)
                                <div class="px-2 py-1 text-gray-400 border-r border-gray-200 text-xs">...</div>
                            @endif
                        @endif

                        {{-- Main page range --}}
                        @for($page = $start; $page <= $end; $page++)
                            @if($page == $current)
                                <div class="px-3 py-1 bg-blue-600 text-white font-semibold border-r border-blue-500 text-xs">
                                    {{ $page }}
                                </div>
                            @else
                                <a href="{{ $paginator->url($page) }}"
                                   class="px-3 py-1 hover:bg-blue-50 text-gray-700 hover:text-blue-600 font-medium border-r border-gray-200 transition-colors duration-200 text-xs">
                                    {{ $page }}
                                </a>
                            @endif
                        @endfor

                        {{-- Last page link jika current page < last-2 --}}
                        @if($end < $last)
                            @if($end < $last - 1)
                                <div class="px-2 py-1 text-gray-400 border-r border-gray-200 text-xs">...</div>
                            @endif
                            <a href="{{ $paginator->url($last) }}"
                               class="px-3 py-1 hover:bg-blue-50 text-gray-700 hover:text-blue-600 font-medium border-r border-gray-200 transition-colors duration-200 text-xs">
                                {{ $last }}
                            </a>
                        @endif
                    </div>

                    {{-- Next Button --}}
                    @if($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}"
                           class="px-2 py-1 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 text-gray-600 hover:text-gray-900 text-xs">
                            ›
                        </a>
                    @else
                        <div class="px-2 py-1 bg-gray-100 text-gray-400 text-xs">
                            ›
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif

<style>
/* Modern Page Jump Form Styles */
#pageJumpForm {
    @apply flex items-center;
}

#pageJumpForm input {
    @apply w-12 px-2 py-1 text-xs border-0 focus:outline-none focus:ring-0 text-center font-medium bg-transparent;
}

#pageJumpForm input:focus {
    @apply bg-white;
}

#pageJumpForm button {
    @apply px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white transition-colors duration-200 border-l border-blue-500;
}

#pageJumpForm button:hover {
    @apply bg-blue-700;
}

/* Modern Pagination Container */
.modern-pagination {
    @apply bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden;
}

/* Statistics Section */
.pagination-stats {
    @apply flex items-center space-x-1;
}

.pagination-stats svg {
    @apply flex-shrink-0;
}

/* Page Jump Container */
.page-jump-container {
    @apply flex items-center bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden;
}

.page-jump-container .jump-input {
    @apply w-16 px-2 py-1 text-xs border-0 focus:outline-none focus:ring-0 text-center font-medium;
}

.page-jump-container .jump-button {
    @apply px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white transition-colors duration-200 border-l border-blue-500;
}

/* Custom Pagination Links */
.custom-pagination {
    @apply flex items-center bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden;
}

.custom-pagination .nav-button {
    @apply px-2 py-1 transition-colors duration-200 text-xs;
}

.custom-pagination .nav-button.disabled {
    @apply bg-gray-100 cursor-not-allowed text-gray-400;
}

.custom-pagination .nav-button:not(.disabled):hover {
    @apply bg-gray-100 text-gray-900;
}

.custom-pagination .page-numbers {
    @apply flex items-center;
}

.custom-pagination .page-number {
    @apply px-3 py-1 border-r border-gray-200 transition-colors duration-200 font-medium text-xs;
}

.custom-pagination .page-number.active {
    @apply bg-blue-600 text-white border-blue-500;
}

.custom-pagination .page-number:not(.active):hover {
    @apply bg-blue-50 text-blue-600;
}

.custom-pagination .page-number:not(.active) {
    @apply text-gray-700;
}

/* Responsive Design */
@media (max-width: 640px) {
    .pagination-stats {
        @apply flex-col space-x-0 space-y-2;
    }

    .page-jump-container .jump-input {
        @apply w-14;
    }

    .custom-pagination .page-number {
        @apply px-2 py-1 text-xs;
    }
}
</style>

<script>
// Page Jump Functionality
document.addEventListener('DOMContentLoaded', function() {
    const pageJumpForm = document.getElementById('pageJumpForm');
    const pageJumpInput = document.getElementById('page_jump');

    if (pageJumpForm && pageJumpInput) {
        const maxPage = {{ $paginator->lastPage() }};

        // Validate input on change
        pageJumpInput.addEventListener('input', function() {
            let value = parseInt(this.value);
            if (isNaN(value) || value < 1) {
                this.value = 1;
            } else if (value > maxPage) {
                this.value = maxPage;
            }
        });

        // Handle form submission
        pageJumpForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const pageValue = parseInt(pageJumpInput.value);

            if (pageValue >= 1 && pageValue <= maxPage) {
                // Update the form action with the page parameter
                const url = new URL(this.action);
                url.searchParams.set('page', pageValue);

                // Preserve other parameters
                const currentUrl = new URL(window.location);
                ['search', 'sort', 'direction', 'per_page'].forEach(param => {
                    if (currentUrl.searchParams.has(param)) {
                        url.searchParams.set(param, currentUrl.searchParams.get(param));
                    }
                });

                // Navigate to the new page
                window.location.href = url.toString();
            }
        });

        // Handle Enter key in input field
        pageJumpInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                pageJumpForm.dispatchEvent(new Event('submit'));
            }
        });

        // Auto-focus and select on click
        pageJumpInput.addEventListener('focus', function() {
            this.select();
        });
    }
});
</script>
