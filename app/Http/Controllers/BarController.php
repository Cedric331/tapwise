<?php

namespace App\Http\Controllers;

use App\Models\Bar;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $bars = $user->is_admin
            ? Bar::withCount('beers')->get()
            : $user->bars()->withCount('beers')->get();

        return Inertia::render('Bars/Index', [
            'bars' => $bars,
        ]);
    }
}

