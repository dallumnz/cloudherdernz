<?php

namespace App\Livewire;

use Livewire\Component;

/**
 * SEO Meta Box Component
 *
 * Provides a Livewire component for editing SEO metadata
 * in the admin post edit/create forms.
 */
class SeoMetaBox extends Component
{
    public array $seo = [
        'title' => '',
        'description' => '',
        'image' => '',
        'robots' => 'index,follow',
    ];

    public function mount(?array $seoData = null): void
    {
        if ($seoData) {
            $this->seo = array_merge($this->seo, $seoData);
        }
    }

    public function render()
    {
        return view('livewire.seo-meta-box');
    }
}