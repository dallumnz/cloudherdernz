<div class="space-y-6" role="region" aria-label="Post search">
    {{-- Search Input --}}
    <div class="relative">
        <flux:input
            wire:model.debounce.300ms="query"
            type="search"
            placeholder="Search posts..."
            aria-label="Search posts"
            aria-describedby="search-help"
            class="w-full"
            icon="magnifying-glass"
        />
        <p id="search-help" class="sr-only">
            Type to search posts by title, excerpt, or content. Results update automatically.
        </p>

        {{-- Loading Indicator --}}
        <div
            wire:loading
            wire:target="query, page, perPage"
            class="absolute right-3 top-1/2 -translate-y-1/2"
            aria-hidden="true"
        >
            <flux:icon name="arrow-path" class="w-5 h-5 animate-spin text-gray-400" />
        </div>
    </div>

    {{-- Results Summary --}}
    @if ($this->totalResults > 0)
        <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400" role="status" aria-live="polite">
            <span>
                Showing {{ count($results) }} of {{ $this->totalResults }} result{{ $this->totalResults !== 1 ? 's' : '' }}
                @if ($query)
                    for "<span class="font-medium">{{ $query }}</span>"
                @endif
            </span>

            {{-- Per Page Selector --}}
            <div class="flex items-center space-x-2">
                <label for="per-page" class="sr-only">Results per page</label>
                <flux:select
                    id="per-page"
                    wire:model="perPage"
                    size="sm"
                    aria-label="Results per page"
                >
                    <option value="6">6 per page</option>
                    <option value="12">12 per page</option>
                    <option value="24">24 per page</option>
                    <option value="48">48 per page</option>
                </flux:select>
            </div>
        </div>
    @endif

    {{-- Error Message --}}
    @if ($errorMessage)
        <flux:callout variant="error" role="alert">
            {{ $errorMessage }}
        </flux:callout>
    @endif

    {{-- Results Grid --}}
    @if ($this->hasResults())
        <div
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
            role="feed"
            aria-label="Search results"
        >
            @foreach ($results as $index => $post)
                <article
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition flex flex-col"
                    aria-labelledby="post-title-{{ $post['id'] }}"
                    aria-posinset="{{ $index + 1 }}"
                    aria-setsize="{{ count($results) }}"
                >
                    {{-- Featured Image --}}
                    @if (! empty($post['featured_image']['thumb']))
                        <a
                            href="{{ route('posts.show', $post['slug']) }}"
                            class="block aspect-video overflow-hidden"
                            aria-hidden="true"
                            tabindex="-1"
                        >
                            <img
                                src="{{ $post['featured_image']['thumb'] }}"
                                alt="{{ $post['featured_image']['alt'] ?? $post['title'] }}"
                                class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                                loading="lazy"
                            >
                        </a>
                    @else
                        <div class="aspect-video bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                            <flux:icon name="document-text" class="w-12 h-12 text-gray-400" aria-hidden="true" />
                        </div>
                    @endif

                    <div class="p-5 flex flex-col flex-grow">
                        {{-- Post Type & Date --}}
                        <div class="flex items-center space-x-2 mb-3">
                            <span class="text-xs font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/50 px-2 py-1 rounded">
                                {{ $post['type']['name'] ?? 'Post' }}
                            </span>
                            @if ($post['published_at'])
                                <time
                                    datetime="{{ $post['published_at'] }}"
                                    class="text-xs text-gray-500"
                                >
                                    {{ \Carbon\Carbon::parse($post['published_at'])->format('M d, Y') }}
                                </time>
                            @endif
                        </div>

                        {{-- Title --}}
                        <h2 id="post-title-{{ $post['id'] }}" class="text-lg font-semibold mb-2">
                            <a
                                href="{{ route('posts.show', $post['slug']) }}"
                                class="text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400"
                            >
                                {{ $post['title'] }}
                            </a>
                        </h2>

                        {{-- Excerpt --}}
                        @if ($post['excerpt'])
                            <p class="text-gray-600 dark:text-gray-400 text-sm line-clamp-3 mb-4 flex-grow">
                                {{ $post['excerpt'] }}
                            </p>
                        @endif

                        {{-- Tags --}}
                        @if (! empty($post['taxonomy_terms']))
                            <div class="flex flex-wrap gap-1 mb-4" role="list" aria-label="Tags">
                                @foreach (array_slice($post['taxonomy_terms'], 0, 3) as $term)
                                    <a
                                        href="{{ route($term['taxonomy']['type'] === 'tag' ? 'tags.show' : 'categories.show', $term['slug']) }}"
                                        class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-600 hover:bg-blue-100 hover:text-blue-700 dark:bg-gray-700 dark:text-gray-400 dark:hover:bg-blue-900 dark:hover:text-blue-300 transition"
                                        role="listitem"
                                    >
                                        {{ $term['name'] }}
                                    </a>
                                @endforeach
                                @if (count($post['taxonomy_terms']) > 3)
                                    <span class="text-xs text-gray-500">
                                        +{{ count($post['taxonomy_terms']) - 3 }} more
                                    </span>
                                @endif
                            </div>
                        @endif

                        {{-- Author & Read More --}}
                        <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700 mt-auto">
                            <div class="flex items-center space-x-2">
                                @if (! empty($post['author']))
                                    <flux:avatar
                                        :name="$post['author']['name']"
                                        :initials="substr($post['author']['name'], 0, 1)"
                                        size="sm"
                                    />
                                    <span class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $post['author']['name'] }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-500">Unknown Author</span>
                                @endif
                            </div>
                            <a
                                href="{{ route('posts.show', $post['slug']) }}"
                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium"
                                aria-label="Read more about {{ $post['title'] }}"
                            >
                                Read More →
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if ($this->totalPages > 1)
            <nav
                class="flex items-center justify-between mt-8"
                aria-label="Pagination"
                role="navigation"
            >
                {{-- Previous Button --}}
                <flux:button
                    wire:click="previousPage"
                    :disabled="! $this->hasPreviousPages()"
                    variant="outline"
                    size="sm"
                    aria-label="Go to previous page"
                >
                    <flux:icon name="chevron-left" class="w-4 h-4 mr-1" aria-hidden="true" />
                    Previous
                </flux:button>

                {{-- Page Info --}}
                <span class="text-sm text-gray-600 dark:text-gray-400" aria-live="polite">
                    Page {{ $this->getCurrentPageProperty() }} of {{ $this->totalPages }}
                </span>

                {{-- Next Button --}}
                <flux:button
                    wire:click="nextPage"
                    :disabled="! $this->hasMorePages()"
                    variant="outline"
                    size="sm"
                    aria-label="Go to next page"
                >
                    Next
                    <flux:icon name="chevron-right" class="w-4 h-4 ml-1" aria-hidden="true" />
                </flux:button>
            </nav>

            {{-- Page Number Links (for larger screens) --}}
            <div class="hidden md:flex justify-center mt-4 space-x-1" role="list" aria-label="Page numbers">
                @for ($pageNum = 1; $pageNum <= min($this->totalPages, 10); $pageNum++)
                    <button
                        wire:click="goToPage({{ $pageNum }})"
                        class="px-3 py-1 text-sm rounded transition-colors
                            {{ $pageNum === $this->getCurrentPageProperty()
                                ? 'bg-blue-600 text-white'
                                : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600'
                            }}"
                        aria-label="Go to page {{ $pageNum }}"
                        aria-current="{{ $pageNum === $this->getCurrentPageProperty() ? 'page' : 'false' }}"
                        role="listitem"
                    >
                        {{ $pageNum }}
                    </button>
                @endfor

                @if ($this->totalPages > 10)
                    <span class="px-2 text-gray-500">...</span>
                    <button
                        wire:click="goToPage({{ $this->totalPages }})"
                        class="px-3 py-1 text-sm rounded bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition-colors"
                        aria-label="Go to last page {{ $this->totalPages }}"
                        role="listitem"
                    >
                        {{ $this->totalPages }}
                    </button>
                @endif
            </div>
        @endif
    @elseif ($query && ! $isLoading)
        {{-- No Results State --}}
        <div class="text-center py-16 bg-gray-50 dark:bg-gray-800 rounded-xl" role="status">
            <flux:icon name="magnifying-glass" class="w-16 h-16 mx-auto mb-4 text-gray-300" aria-hidden="true" />
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                No results found
            </h3>
            <p class="text-gray-500 dark:text-gray-400">
                No posts match "<span class="font-medium">{{ $query }}</span>". Try a different search term.
            </p>
            <flux:button
                wire:click="$set('query', '')"
                variant="outline"
                size="sm"
                class="mt-4"
            >
                Clear Search
            </flux:button>
        </div>
    @elseif (! $isLoading)
        {{-- Empty State (Initial Load) --}}
        <div class="text-center py-16 bg-gray-50 dark:bg-gray-800 rounded-xl" role="status">
            <flux:icon name="document-text" class="w-16 h-16 mx-auto mb-4 text-gray-300" aria-hidden="true" />
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                No posts available
            </h3>
            <p class="text-gray-500 dark:text-gray-400">
                Check back soon for new content!
            </p>
        </div>
    @endif

    {{-- Loading Skeleton (shown during initial load) --}}
    @if ($isLoading && empty($results))
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" aria-hidden="true">
            @for ($i = 0; $i < 6; $i++)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                    <div class="aspect-video bg-gray-200 dark:bg-gray-700 animate-pulse"></div>
                    <div class="p-5 space-y-3">
                        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/4 animate-pulse"></div>
                        <div class="h-6 bg-gray-200 dark:bg-gray-700 rounded w-3/4 animate-pulse"></div>
                        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-full animate-pulse"></div>
                        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-2/3 animate-pulse"></div>
                    </div>
                </div>
            @endfor
        </div>
    @endif
</div>
