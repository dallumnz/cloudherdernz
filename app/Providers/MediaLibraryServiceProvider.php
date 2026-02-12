<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaLibraryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../../config/media-library.php' => config_path('media-library.php'),
        ], 'media-library-config');

        $this->registerMediaConversions();
    }

    /**
     * Register global media conversions.
     */
    private function registerMediaConversions(): void
    {
        // Global conversions can be registered here if needed
        // Model-specific conversions are defined in each model's registerMediaConversions() method
    }
}
