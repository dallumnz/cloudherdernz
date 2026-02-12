<?php

namespace App\Http\Controllers;

use App\Services\SitemapGenerator;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate and return the sitemap XML.
     */
    public function index(SitemapGenerator $generator): Response
    {
        $xml = $generator->generate();

        return response($xml, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }
}
