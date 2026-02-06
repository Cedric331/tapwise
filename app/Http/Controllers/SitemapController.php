<?php

namespace App\Http\Controllers;

use App\Models\Bar;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $now = Carbon::now();

        $urls = [
            ['loc' => route('home'), 'lastmod' => $now],
            ['loc' => route('legal.mentions'), 'lastmod' => $now],
            ['loc' => route('legal.confidentialite'), 'lastmod' => $now],
            ['loc' => route('legal.cgu'), 'lastmod' => $now],
            ['loc' => route('legal.contact'), 'lastmod' => $now],
        ];

        $bars = Bar::where('qr_enabled', true)->get();

        foreach ($bars as $bar) {
            if (! $bar->hasActiveAccess()) {
                continue;
            }

            $urls[] = [
                'loc' => route('public.bar.show', ['slug' => $bar->slug]),
                'lastmod' => $bar->updated_at ?? $now,
            ];
        }

        $xml = $this->buildXml($urls);

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    /**
     * @param array<int, array{loc: string, lastmod: Carbon}> $urls
     */
    private function buildXml(array $urls): string
    {
        $lines = [
            '<?xml version="1.0" encoding="UTF-8"?>',
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">',
        ];

        foreach ($urls as $url) {
            $loc = htmlspecialchars($url['loc'], ENT_XML1);
            $lastmod = $url['lastmod']->toAtomString();

            $lines[] = '  <url>';
            $lines[] = "    <loc>{$loc}</loc>";
            $lines[] = "    <lastmod>{$lastmod}</lastmod>";
            $lines[] = '  </url>';
        }

        $lines[] = '</urlset>';

        return implode("\n", $lines);
    }
}

