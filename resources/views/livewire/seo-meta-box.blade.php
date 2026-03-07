<div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">SEO Settings</h3>
    
    <div class="space-y-4">
        {{-- SEO Title --}}
        <div>
            <label for="seo_title" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                SEO Title <span class="text-xs text-zinc-500">({{ strlen($seo['title'] ?? '') }}/70)</span>
            </label>
            <input 
                type="text" 
                id="seo_title"
                wire:model.live="seo.title"
                class="w-full rounded-md border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="Custom SEO title (defaults to post title)"
                maxlength="70"
            >
            <p class="mt-1 text-xs text-zinc-500">Title shown in search engine results. Keep under 70 characters.</p>
        </div>

        {{-- Meta Description --}}
        <div>
            <label for="seo_description" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                Meta Description <span class="text-xs text-zinc-500">({{ strlen($seo['description'] ?? '') }}/160)</span>
            </label>
            <textarea 
                id="seo_description"
                wire:model.live="seo.description"
                rows="3"
                class="w-full rounded-md border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="Brief description for search results"
                maxlength="160"
            ></textarea>
            <p class="mt-1 text-xs text-zinc-500">Description shown in search results. Keep under 160 characters.</p>
        </div>

        {{-- OG Image --}}
        <div>
            <label for="seo_image" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                Social Media Image
            </label>
            <input 
                type="url" 
                id="seo_image"
                wire:model.live="seo.image"
                class="w-full rounded-md border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="https://example.com/image.jpg"
            >
            <p class="mt-1 text-xs text-zinc-500">Image shown when shared on social media (Facebook, Twitter, etc.)</p>
        </div>

        {{-- Robots Meta --}}
        <div>
            <label for="seo_robots" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                Search Engine Visibility
            </label>
            <select 
                id="seo_robots"
                wire:model.live="seo.robots"
                class="w-full rounded-md border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:border-indigo-500 focus:ring-indigo-500"
            >
                <option value="index,follow">Index and Follow (default)</option>
                <option value="noindex,follow">No Index, Follow</option>
                <option value="index,nofollow">Index, No Follow</option>
                <option value="noindex,nofollow">No Index, No Follow</option>
            </select>
            <p class="mt-1 text-xs text-zinc-500">Controls how search engines index this page.</p>
        </div>
    </div>

    {{-- Hidden inputs for form submission --}}
    <input type="hidden" name="seo[title]" wire:model="seo.title">
    <input type="hidden" name="seo[description]" wire:model="seo.description">
    <input type="hidden" name="seo[image]" wire:model="seo.image">
    <input type="hidden" name="seo[robots]" wire:model="seo.robots">
</div>