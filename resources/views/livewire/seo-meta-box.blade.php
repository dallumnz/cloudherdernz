<div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">SEO Settings</h3>
    
    <div class="space-y-4">
        {{-- SEO Title --}}
        <flux:input
            wire:model.live="seo.title"
            label="SEO Title ({{ strlen($seo['title'] ?? '') }}/70)"
            placeholder="Custom SEO title (defaults to post title)"
            maxlength="70"
            description="Title shown in search engine results. Keep under 70 characters."
        />

        {{-- Meta Description --}}
        <flux:textarea
            wire:model.live="seo.description"
            label="Meta Description ({{ strlen($seo['description'] ?? '') }}/160)"
            placeholder="Brief description for search results"
            maxlength="160"
            rows="3"
            description="Description shown in search results. Keep under 160 characters."
        />

        {{-- OG Image --}}
        <flux:input
            type="url"
            wire:model.live="seo.image"
            label="Social Media Image"
            placeholder="https://example.com/image.jpg"
            description="Image shown when shared on social media (Facebook, Twitter, etc.)"
        />

        {{-- Robots Meta --}}
        <flux:select
            wire:model.live="seo.robots"
            label="Search Engine Visibility"
            description="Controls how search engines index this page."
        >
            <option value="index,follow">Index and Follow (default)</option>
            <option value="noindex,follow">No Index, Follow</option>
            <option value="index,nofollow">Index, No Follow</option>
            <option value="noindex,nofollow">No Index, No Follow</option>
        </flux:select>
    </div>

    {{-- Hidden inputs for form submission --}}
    <input type="hidden" name="seo[title]" wire:model="seo.title">
    <input type="hidden" name="seo[description]" wire:model="seo.description">
    <input type="hidden" name="seo[image]" wire:model="seo.image">
    <input type="hidden" name="seo[robots]" wire:model="seo.robots">
</div>