<?php

namespace App\Http\Controllers;

use App\Http\Requests\BarStoreRequest;
use App\Models\Bar;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class BarController extends Controller
{
    /**
     * Display the bar dashboard.
     */
    public function show(Bar $bar): Response
    {
        $this->authorize('view', $bar);

        $bar->load(['beers.tags', 'users']);

        $stats = [
            'total_beers' => $bar->beers()->count(),
            'available_beers' => $bar->beers()->where('is_available', true)->count(),
            'on_tap_beers' => $bar->beers()->where('is_on_tap', true)->count(),
            'total_tags' => $bar->beers()->with('tags')->get()->pluck('tags')->flatten()->unique('id')->count(),
        ];

        $recentBeers = $bar->beers()
            ->with('tags')
            ->latest()
            ->take(5)
            ->get();

        return Inertia::render('Bars/Show', [
            'bar' => $bar,
            'stats' => $stats,
            'recentBeers' => $recentBeers,
            'publicUrl' => $bar->public_url,
        ]);
    }

    /**
     * Display the list of bars for the current user.
     */
    public function index(): Response
    {
        $user = Auth::user();

        $bars = ($user->is_admin
            ? Bar::query()
            : $user->bars()
        )->withCount('beers')
            ->get()
            ->map(fn (Bar $bar) => [
                'id' => $bar->id,
                'name' => $bar->name,
                'slug' => $bar->slug,
                'beers_count' => $bar->beers_count,
                'is_demo' => $bar->is_demo,
                'can_delete' => $user->can('delete', $bar),
            ]);

        return Inertia::render('Bars/Index', [
            'bars' => $bars,
            'canCreate' => $user->can('create', Bar::class),
        ]);
    }

    /**
     * Store a newly created bar.
     */
    public function store(BarStoreRequest $request): RedirectResponse
    {
        $this->authorize('create', Bar::class);

        $validated = $request->validated();
        $user = $request->user();

        DB::transaction(function () use ($validated, $user) {
            $baseSlug = Str::slug($validated['name']);
            if ($baseSlug === '') {
                $baseSlug = 'bar';
            }

            $slug = $baseSlug;
            $suffix = 2;
            while (Bar::where('slug', $slug)->exists()) {
                $slug = "{$baseSlug}-{$suffix}";
                $suffix++;
            }

            $bar = Bar::create([
                'name' => $validated['name'],
                'slug' => $slug,
                'qr_enabled' => true,
                'is_demo' => false,
            ]);

            $bar->users()->attach($user->id, ['role' => 'owner']);
        });

        return redirect()->route('bars.index');
    }

    /**
     * Remove the specified bar.
     */
    public function destroy(Bar $bar): RedirectResponse
    {
        $this->authorize('delete', $bar);

        $bar->delete();

        return redirect()->route('bars.index');
    }
}

