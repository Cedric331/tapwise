<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class BlogController extends Controller
{
    public function index(): Response
    {
        $posts = BlogPost::query()
            ->where('is_live', true)
            ->orderByDesc('published_at')
            ->paginate(6)
            ->withQueryString()
            ->through(function (BlogPost $post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'excerpt' => Str::limit(strip_tags($post->content), 160),
                    'cover_image_url' => $post->cover_image_url,
                    'published_at_iso' => $post->published_at
                        ? $post->published_at->toDateString()
                        : null,
                    'tags' => $post->tags ?? [],
                ];
            });

        return Inertia::render('Blog/Index', [
            'posts' => $posts,
        ]);
    }

    public function show(string $slug): Response
    {
        $post = BlogPost::query()
            ->where('slug', $slug)
            ->where('is_live', true)
            ->firstOrFail();

        return Inertia::render('Blog/Show', [
            'post' => [
                'title' => $post->title,
                'slug' => $post->slug,
                'content' => $post->content,
                'excerpt' => Str::limit(strip_tags($post->content), 160),
                'cover_image_url' => $post->cover_image_url,
                'published_at_iso' => $post->published_at
                    ? $post->published_at->toDateString()
                    : null,
                'tags' => $post->tags ?? [],
            ],
        ]);
    }
}
