<?php

namespace App\Http\Controllers;

use App\Http\Requests\BarStoreRequest;
use App\Models\Bar;
use App\Models\BarScanEvent;
use App\Models\Beer;
use App\Models\RecommendationEvent;
use App\Models\Wine;
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

        $bar->load(['beers.tags', 'wines.tags', 'users']);
        $billingUser = $bar->billingUser();
        $subscriptionStatus = $bar->subscriptionStatus();
        $trialEndsAt = $subscriptionStatus === 'trial' ? $billingUser?->trial_ends_at : null;
        $trialDaysLeft = $trialEndsAt ? now()->diffInDays($trialEndsAt, false) : null;

        $recentBeers = $bar->beers()
            ->with('tags')
            ->latest()
            ->take(5)
            ->get();
        $recentWines = $bar->wines()
            ->with('tags')
            ->latest()
            ->take(5)
            ->get();

        return Inertia::render('Bars/Show', [
            'bar' => $bar,
            'recentBeers' => $recentBeers,
            'recentWines' => $recentWines,
            'publicUrl' => $bar->public_url,
            'subscription' => [
                'status' => $subscriptionStatus,
                'trialEndsAt' => $trialEndsAt?->toDateString(),
                'trialDaysLeft' => $trialDaysLeft !== null ? max($trialDaysLeft, 0) : null,
                'canAccessPublic' => $bar->hasActiveAccess(),
            ],
        ]);
    }

    /**
     * Display the bar statistics page.
     */
    public function stats(Bar $bar): Response
    {
        $this->authorize('view', $bar);

        $scanStart = now()->subDays(13)->startOfDay();
        $scanRows = BarScanEvent::query()
            ->where('bar_id', $bar->id)
            ->where('created_at', '>=', $scanStart)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total', 'date');
        $scanSeries = collect(range(0, 13))->map(function (int $offset) use ($scanStart, $scanRows) {
            $date = $scanStart->copy()->addDays($offset);
            $key = $date->toDateString();

            return [
                'label' => $date->format('d/m'),
                'value' => (int) ($scanRows[$key] ?? 0),
            ];
        });

        $recStart = now()->subDays(13)->startOfDay();
        $recRows = RecommendationEvent::query()
            ->where('bar_id', $bar->id)
            ->where('created_at', '>=', $recStart)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total', 'date');
        $recommendationSeries = collect(range(0, 13))->map(function (int $offset) use ($recStart, $recRows) {
            $date = $recStart->copy()->addDays($offset);
            $key = $date->toDateString();

            return [
                'label' => $date->format('d/m'),
                'value' => (int) ($recRows[$key] ?? 0),
            ];
        });

        $topStart = now()->subDays(30)->startOfDay();
        $recommendationEvents = RecommendationEvent::query()
            ->where('bar_id', $bar->id)
            ->where('created_at', '>=', $topStart)
            ->get(['drink_type', 'item_ids']);
        $counts = [
            'beer' => [],
            'wine' => [],
        ];

        foreach ($recommendationEvents as $event) {
            $type = $event->drink_type === 'wine' ? 'wine' : 'beer';
            $items = is_array($event->item_ids) ? $event->item_ids : [];
            foreach ($items as $id) {
                if (! is_int($id)) {
                    continue;
                }
                $counts[$type][$id] = ($counts[$type][$id] ?? 0) + 1;
            }
        }

        $topBeers = collect($counts['beer'])->sortDesc()->take(3);
        $topWines = collect($counts['wine'])->sortDesc()->take(3);
        $beerNames = Beer::query()
            ->whereIn('id', $topBeers->keys()->all())
            ->pluck('name', 'id');
        $wineNames = Wine::query()
            ->whereIn('id', $topWines->keys()->all())
            ->pluck('name', 'id');

        $stats = [
            'total_beers' => $bar->beers()->count(),
            'available_beers' => $bar->beers()->where('is_available', true)->count(),
            'on_tap_beers' => $bar->beers()->where('is_on_tap', true)->count(),
            'total_tags' => $bar->beers()->with('tags')->get()->pluck('tags')->flatten()->unique('id')->count(),
            'total_wines' => $bar->wines()->count(),
            'available_wines' => $bar->wines()->where('is_available', true)->count(),
            'total_wine_tags' => $bar->wines()->with('tags')->get()->pluck('tags')->flatten()->unique('id')->count(),
            'total_scans' => $bar->count_scans,
            'scans_last_30_days' => BarScanEvent::query()
                ->where('bar_id', $bar->id)
                ->where('created_at', '>=', now()->subDays(30)->startOfDay())
                ->count(),
            'recommendations_last_30_days' => RecommendationEvent::query()
                ->where('bar_id', $bar->id)
                ->where('created_at', '>=', now()->subDays(30)->startOfDay())
                ->count(),
        ];

        return Inertia::render('Bars/Stats', [
            'bar' => $bar,
            'stats' => $stats,
            'scanSeries' => $scanSeries,
            'recommendationSeries' => $recommendationSeries,
            'topBeers' => $topBeers->map(fn (int $count, int $id) => [
                'id' => $id,
                'name' => $beerNames[$id] ?? 'Bière inconnue',
                'count' => $count,
            ])->values(),
            'topWines' => $topWines->map(fn (int $count, int $id) => [
                'id' => $id,
                'name' => $wineNames[$id] ?? 'Vin inconnu',
                'count' => $count,
            ])->values(),
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
        )->withCount(['beers', 'wines'])
            ->get()
            ->map(fn (Bar $bar) => [
                'id' => $bar->id,
                'name' => $bar->name,
                'slug' => $bar->slug,
                'beers_count' => $bar->beers_count,
                'wines_count' => $bar->wines_count,
                'is_demo' => $bar->is_demo,
                'can_delete' => $user->can('delete', $bar),
                'subscription_status' => $bar->subscriptionStatus(),
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
