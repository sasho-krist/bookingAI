<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $lastmod = Carbon::now()->startOfDay()->toAtomString();

        $routes = [
            ['name' => 'landing', 'changefreq' => 'weekly', 'priority' => '1.0'],
            ['name' => 'legal.privacy', 'changefreq' => 'monthly', 'priority' => '0.6'],
            ['name' => 'legal.terms', 'changefreq' => 'monthly', 'priority' => '0.6'],
            ['name' => 'legal.faq', 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['name' => 'login', 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['name' => 'register', 'changefreq' => 'monthly', 'priority' => '0.5'],
        ];

        $urls = [];
        foreach ($routes as $item) {
            $urls[] = [
                'loc' => route($item['name']),
                'changefreq' => $item['changefreq'],
                'priority' => $item['priority'],
                'lastmod' => $lastmod,
            ];
        }

        return response()
            ->view('seo.sitemap', compact('urls'))
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
