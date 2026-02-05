<?php

namespace App\Http\Controllers;

use App\Enums\BeerColor;
use App\Http\Requests\BeerStoreRequest;
use App\Http\Requests\BeerUpdateRequest;
use App\Models\Bar;
use App\Models\Beer;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BeerController extends Controller
{
    /**
     * Display a listing of beers for a bar.
     */
    public function index(Bar $bar): Response
    {
        $this->authorize('manageBeers', $bar);

        $beers = $bar->beers()->with('tags')->get();
        $tags = Tag::all();

        return Inertia::render('Beers/Index', [
            'bar' => $bar,
            'beers' => $beers,
            'tags' => $tags,
        ]);
    }

    /**
     * Show the form for creating a new beer.
     */
    public function create(Bar $bar): Response
    {
        $this->authorize('manageBeers', $bar);

        $tags = Tag::all();

        return Inertia::render('Beers/Create', [
            'bar' => $bar,
            'tags' => $tags,
            'colorOptions' => BeerColor::options(),
        ]);
    }

    /**
     * Store a newly created beer.
     */
    public function store(BeerStoreRequest $request, Bar $bar): RedirectResponse
    {
        $this->authorize('manageBeers', $bar);

        $beer = $bar->beers()->create($request->validated());

        if ($request->has('tags')) {
            $beer->tags()->sync($request->input('tags', []));
        }

        return redirect()->route('bars.beers.index', $bar)
            ->with('success', 'Bière créée avec succès.');
    }

    /**
     * Show the form for editing a beer.
     */
    public function edit(Bar $bar, Beer $beer): Response
    {
        $this->authorize('manageBeers', $bar);

        $beer->load('tags');
        $tags = Tag::all();

        return Inertia::render('Beers/Edit', [
            'bar' => $bar,
            'beer' => $beer,
            'tags' => $tags,
            'colorOptions' => BeerColor::options(),
        ]);
    }

    /**
     * Update the specified beer.
     */
    public function update(BeerUpdateRequest $request, Bar $bar, Beer $beer): RedirectResponse
    {
        $this->authorize('manageBeers', $bar);

        $beer->update($request->validated());

        if ($request->has('tags')) {
            $beer->tags()->sync($request->input('tags', []));
        }

        return redirect()->route('bars.beers.index', $bar)
            ->with('success', 'Bière mise à jour avec succès.');
    }

    /**
     * Remove the specified beer.
     */
    public function destroy(Bar $bar, Beer $beer): RedirectResponse
    {
        $this->authorize('manageBeers', $bar);

        $beer->delete();

        return redirect()->route('bars.beers.index', $bar)
            ->with('success', 'Bière supprimée avec succès.');
    }
}

