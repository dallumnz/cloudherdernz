<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Taxonomy;
use App\Models\TaxonomyTerm;
use Illuminate\Support\Facades\Cache;

class SitemapGenerator
{
    /**
     * Generate the sitemap XML content.
     *
     * Caches the result for 1 hour (60 minutes).
     */
    public function generate(): string
    {
        return Cache::remember('sitemap_xml', 60, function () {
            return $this->buildXml();
        });
    }

    /**
     * Build the XML sitemap content.
     */
    private function buildXml(): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        // Add homepage
        $xml .= $this->buildUrlEntry(
            route('home'),
            now(),
            'daily',
            '1.0'
        );

        // Add posts
        $xml .= $this->getPostsXml();

        // Add categories
        $xml .= $this->getCategoriesXml();

        // Add tags
        $xml .= $this->getTagsXml();

        $xml .= '</urlset>';

        return $xml;
    }

    /**
     * Get XML for all published posts.
     */
    private function getPostsXml(): string
    {
        $xml = '';

        $posts = Post::query()
            ->published()
            ->select('id', 'slug', 'updated_at')
            ->get();

        foreach ($posts as $post) {
            $xml .= $this->buildUrlEntry(
                route('posts.show', $post),
                $post->updated_at,
                'daily',
                '0.8'
            );
        }

        return $xml;
    }

    /**
     * Get XML for all categories.
     */
    private function getCategoriesXml(): string
    {
        $xml = '';

        $categoryTaxonomy = Taxonomy::query()
            ->where('type', 'category')
            ->first();

        if ($categoryTaxonomy) {
            $categories = TaxonomyTerm::query()
                ->where('taxonomy_id', $categoryTaxonomy->id)
                ->select('id', 'slug', 'updated_at')
                ->get();

            foreach ($categories as $category) {
                $xml .= $this->buildUrlEntry(
                    route('categories.show', $category),
                    $category->updated_at,
                    'weekly',
                    '0.6'
                );
            }
        }

        return $xml;
    }

    /**
     * Get XML for all tags.
     */
    private function getTagsXml(): string
    {
        $xml = '';

        $tagTaxonomy = Taxonomy::query()
            ->where('type', 'tag')
            ->first();

        if ($tagTaxonomy) {
            $tags = TaxonomyTerm::query()
                ->where('taxonomy_id', $tagTaxonomy->id)
                ->select('id', 'slug', 'updated_at')
                ->get();

            foreach ($tags as $tag) {
                $xml .= $this->buildUrlEntry(
                    route('tags.show', $tag),
                    $tag->updated_at,
                    'weekly',
                    '0.6'
                );
            }
        }

        return $xml;
    }

    /**
     * Build a single URL entry for the sitemap.
     */
    private function buildUrlEntry(string $loc, ?\Carbon\CarbonInterface $lastmod, string $changefreq, string $priority): string
    {
        $xml = "  <url>\n";
        $xml .= '    <loc>'.htmlspecialchars($loc, ENT_XML1, 'UTF-8')."</loc>\n";

        if ($lastmod) {
            $xml .= '    <lastmod>'.$lastmod->toIso8601String()."</lastmod>\n";
        }

        $xml .= "    <changefreq>{$changefreq}</changefreq>\n";
        $xml .= "    <priority>{$priority}</priority>\n";
        $xml .= "  </url>\n";

        return $xml;
    }

    /**
     * Clear the sitemap cache.
     */
    public function clearCache(): void
    {
        Cache::forget('sitemap_xml');
    }
}
