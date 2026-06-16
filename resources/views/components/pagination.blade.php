@props(['paginator'])

@if ($paginator->hasPages())
    @php
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();
        $separatorShown = false;
        $mobileSeparatorShown = false;
        $arrowClasses =
            'inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white text-base text-black shadow-sm ring-1 ring-gray-100 transition hover:bg-gray-50 sm:h-11 sm:w-16';
        $disabledArrowClasses =
            'inline-flex h-10 w-10 shrink-0 cursor-not-allowed items-center justify-center rounded-lg bg-white text-base text-gray-400 shadow-sm ring-1 ring-gray-100 sm:h-11 sm:w-16';
        $pageClasses =
            'inline-flex h-10 w-10 shrink-0 items-center justify-center rounded border border-gray-300 bg-white text-sm text-black transition hover:border-primary hover:bg-primary-soft sm:text-base';
        $activePageClasses =
            'inline-flex h-10 w-10 shrink-0 items-center justify-center rounded bg-secondary text-sm font-semibold text-black sm:text-base';
        $separatorClasses =
            'inline-flex h-10 w-10 shrink-0 items-center justify-center rounded border border-gray-300 bg-white text-sm font-semibold text-black sm:text-base';
    @endphp

    <nav {{ $attributes->merge(['class' => 'overflow-x-auto sm:overflow-visible']) }} aria-label="Pagination">
        <div class="flex w-full items-center justify-center gap-2 px-1 py-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="{{ $disabledArrowClasses }}" aria-disabled="true" aria-label="Halaman sebelumnya">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5m0 0 6 6m-6-6 6-6" />
                    </svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="{{ $arrowClasses }}" rel="prev"
                    aria-label="Halaman sebelumnya">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5m0 0 6 6m-6-6 6-6" />
                    </svg>
                </a>
            @endif

            <div class="flex shrink-0 items-center justify-center gap-1">
                @for ($page = 1; $page <= $lastPage; $page++)
                    @php
                        $showPage =
                            $page === 1 ||
                            $page === $lastPage ||
                            $page === $currentPage ||
                            ($currentPage <= 2 && $page <= 3) ||
                            ($currentPage >= $lastPage - 1 && $page >= $lastPage - 2);
                    @endphp

                    @if ($showPage)
                        @php $mobileSeparatorShown = false; @endphp
                        @if ($page === $currentPage)
                            <span class="{{ $activePageClasses }}" aria-current="page">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $paginator->url($page) }}" class="{{ $pageClasses }}"
                                aria-label="Halaman {{ $page }}">
                                {{ $page }}
                            </a>
                        @endif
                    @elseif (!$mobileSeparatorShown)
                        @php $mobileSeparatorShown = true; @endphp
                        <span class="{{ $separatorClasses }}" aria-hidden="true">...</span>
                    @endif
                @endfor
            </div>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="{{ $arrowClasses }}" rel="next"
                    aria-label="Halaman berikutnya">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m0 0-6-6m6 6-6 6" />
                    </svg>
                </a>
            @else
                <span class="{{ $disabledArrowClasses }}" aria-disabled="true" aria-label="Halaman berikutnya">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m0 0-6-6m6 6-6 6" />
                    </svg>
                </span>
            @endif
        </div>

        <div class="hidden w-full items-center justify-center gap-3 px-1 py-1 sm:flex">
            @if ($paginator->onFirstPage())
                <span class="{{ $disabledArrowClasses }}" aria-disabled="true" aria-label="Halaman sebelumnya">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5m0 0 6 6m-6-6 6-6" />
                    </svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="{{ $arrowClasses }}" rel="prev"
                    aria-label="Halaman sebelumnya">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5m0 0 6 6m-6-6 6-6" />
                    </svg>
                </a>
            @endif

            <div class="flex shrink-0 items-center justify-center gap-2">
                @for ($page = 1; $page <= $lastPage; $page++)
                    @php
                        $showPage =
                            $page === 1 ||
                            $page === $lastPage ||
                            abs($page - $currentPage) <= 1 ||
                            ($currentPage <= 3 && $page <= 5) ||
                            ($currentPage >= $lastPage - 2 && $page >= $lastPage - 4);
                    @endphp

                    @if ($showPage)
                        @php $separatorShown = false; @endphp
                        @if ($page === $currentPage)
                            <span class="{{ $activePageClasses }}" aria-current="page">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $paginator->url($page) }}" class="{{ $pageClasses }}"
                                aria-label="Halaman {{ $page }}">
                                {{ $page }}
                            </a>
                        @endif
                    @elseif (!$separatorShown)
                        @php $separatorShown = true; @endphp
                        <span class="{{ $separatorClasses }}" aria-hidden="true">...</span>
                    @endif
                @endfor
            </div>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="{{ $arrowClasses }}" rel="next"
                    aria-label="Halaman berikutnya">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m0 0-6-6m6 6-6 6" />
                    </svg>
                </a>
            @else
                <span class="{{ $disabledArrowClasses }}" aria-disabled="true" aria-label="Halaman berikutnya">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m0 0-6-6m6 6-6 6" />
                    </svg>
                </span>
            @endif
        </div>
    </nav>
@endif
