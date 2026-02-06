<?php

namespace App\Http\Controllers;

use App\Enums\BeerColor;
use App\Http\Requests\BeerImportRequest;
use App\Http\Requests\BeerStoreRequest;
use App\Http\Requests\BeerUpdateRequest;
use App\Models\Bar;
use App\Models\Beer;
use App\Models\Tag;
use App\Services\BeerImportService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BeerController extends Controller
{
    public function __construct(private readonly BeerImportService $beerImportService) {}

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
            'importReport' => session('importReport'),
            'contactEmail' => config('services.contact_email'),
        ]);
    }

    /**
     * Download a CSV template for bulk import.
     */
    public function template(Bar $bar): StreamedResponse
    {
        $this->authorize('manageBeers', $bar);

        $headers = [
            'name',
            'brewery',
            'style',
            'color',
            'abv',
            'ibu',
            'description',
            'is_on_tap',
            'is_available',
            'price',
        ];

        $example = [
            'Pale Ale',
            'Brasserie Demo',
            'Pale Ale',
            'blonde',
            '5.2',
            '35',
            "Notes d'agrumes",
            'oui',
            'oui',
            '6.50',
        ];

        return response()->streamDownload(function () use ($headers, $example) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, $headers, ';');
            fputcsv($handle, $example, ';');

            fclose($handle);
        }, 'tapwise-import-bieres.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Import beers in bulk from a CSV file.
     */
    public function import(BeerImportRequest $request, Bar $bar): RedirectResponse
    {
        $this->authorize('manageBeers', $bar);

        $file = $request->file('file');
        if (! $file) {
            return redirect()->route('bars.beers.index', $bar)
                ->with('importReport', [
                    'status' => 'error',
                    'message' => 'Aucun fichier détecté.',
                ]);
        }

        $report = $this->beerImportService->importFromPath(
            $bar,
            $file->getRealPath(),
            $request->input('mapping', [])
        );

        return redirect()->route('bars.beers.index', $bar)
            ->with('importReport', $report);
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

    //
}
