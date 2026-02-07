<?php

namespace App\Http\Controllers;

use App\Models\Bar;
use App\Models\BlogPost;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $now = Carbon::now();

        $urls = [
            ['loc' => route('home'), 'lastmod' => $now],
            ['loc' => route('blog.index'), 'lastmod' => $now],
            ['loc' => route('legal.mentions'), 'lastmod' => $now],
            ['loc' => route('legal.confidentialite'), 'lastmod' => $now],
            ['loc' => route('legal.cgu'), 'lastmod' => $now],
            ['loc' => route('legal.contact'), 'lastmod' => $now],
        ];

        try {
            $bars = Bar::where('qr_enabled', true)->get();

            foreach ($bars as $bar) {
                try {
                    if (! $bar->hasActiveAccess()) {
                        continue;
                    }

                    $urls[] = [
                        'loc' => route('public.bar.show', ['slug' => $bar->slug]),
                        'lastmod' => $bar->updated_at ?? $now,
                    ];
                } catch (\Throwable $exception) {
                    Log::warning('Sitemap bar skipped due to error.', [
                        'bar_id' => $bar->id,
                        'error' => $exception->getMessage(),
                    ]);
                }
            }
        } catch (\Throwable $exception) {
            Log::error('Sitemap bar listing failed.', [
                'error' => $exception->getMessage(),
            ]);
        }

        try {
            $posts = BlogPost::query()
                ->where('is_live', true)
                ->orderByDesc('published_at')
                ->get();

            foreach ($posts as $post) {
                $urls[] = [
                    'loc' => route('blog.show', ['slug' => $post->slug]),
                    'lastmod' => $post->updated_at ?? $now,
                ];
            }
        } catch (\Throwable $exception) {
            Log::error('Sitemap blog listing failed.', [
                'error' => $exception->getMessage(),
            ]);
        }

        $xml = $this->buildXml($urls);

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    /**
     * @param  array<int, array{loc: string, lastmod: Carbon}>  $urls
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
