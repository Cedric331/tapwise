<?php

namespace App\Http\Controllers;

use App\Enums\WineColor;
use App\Http\Requests\WineImportRequest;
use App\Http\Requests\WineStoreRequest;
use App\Http\Requests\WineUpdateRequest;
use App\Models\Bar;
use App\Models\Wine;
use App\Models\WineTag;
use App\Services\WineImportService;
use App\Support\WineFoodPairings;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WineController extends Controller
{
    public function __construct(private readonly WineImportService $wineImportService) {}

    /**
     * Display a listing of wines for a bar.
     */
    public function index(Bar $bar): Response
    {
        $this->authorize('manageWines', $bar);

        $wines = $bar->wines()->with('tags')->get();
        $tags = WineTag::all();

        return Inertia::render('Wines/Index', [
            'bar' => $bar,
            'wines' => $wines,
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
        $this->authorize('manageWines', $bar);

        $headers = [
            'name',
            'color',
            'grape',
            'region',
            'food_pairings',
            'abv',
            'description',
            'is_available',
            'price',
        ];

        $example = [
            'Bordeaux Supérieur',
            'red',
            'Merlot',
            'Bordeaux',
            'viande_rouge,fromage',
            '12.5',
            "Fruits rouges et notes boisées",
            'oui',
            '7.50',
        ];

        return response()->streamDownload(function () use ($headers, $example) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, $headers, ';');
            fputcsv($handle, $example, ';');

            fclose($handle);
        }, 'tapwise-import-vins.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Import wines in bulk from a CSV file.
     */
    public function import(WineImportRequest $request, Bar $bar): RedirectResponse
    {
        $this->authorize('manageWines', $bar);

        $file = $request->file('file');
        if (! $file) {
            return redirect()->route('bars.wines.index', $bar)
                ->with('importReport', [
                    'status' => 'error',
                    'message' => 'Aucun fichier détecté.',
                ]);
        }

        $report = $this->wineImportService->importFromPath(
            $bar,
            $file->getRealPath(),
            $request->input('mapping', [])
        );

        return redirect()->route('bars.wines.index', $bar)
            ->with('importReport', $report);
    }

    /**
     * Show the form for creating a new wine.
     */
    public function create(Bar $bar): Response
    {
        $this->authorize('manageWines', $bar);

        $tags = WineTag::all();

        return Inertia::render('Wines/Create', [
            'bar' => $bar,
            'tags' => $tags,
            'colorOptions' => WineColor::options(),
            'foodPairingOptions' => WineFoodPairings::options(),
        ]);
    }

    /**
     * Store a newly created wine.
     */
    public function store(WineStoreRequest $request, Bar $bar): RedirectResponse
    {
        $this->authorize('manageWines', $bar);

        $wine = $bar->wines()->create($request->validated());

        if ($request->has('tags')) {
            $wine->tags()->sync($request->input('tags', []));
        }

        return redirect()->route('bars.wines.index', $bar)
            ->with('success', 'Vin créé avec succès.');
    }

    /**
     * Show the form for editing a wine.
     */
    public function edit(Bar $bar, Wine $wine): Response
    {
        $this->authorize('manageWines', $bar);

        $wine->load('tags');
        $tags = WineTag::all();

        return Inertia::render('Wines/Edit', [
            'bar' => $bar,
            'wine' => $wine,
            'tags' => $tags,
            'colorOptions' => WineColor::options(),
            'foodPairingOptions' => WineFoodPairings::options(),
        ]);
    }

    /**
     * Update the specified wine.
     */
    public function update(WineUpdateRequest $request, Bar $bar, Wine $wine): RedirectResponse
    {
        $this->authorize('manageWines', $bar);

        $wine->update($request->validated());

        if ($request->has('tags')) {
            $wine->tags()->sync($request->input('tags', []));
        }

        return redirect()->route('bars.wines.index', $bar)
            ->with('success', 'Vin mis à jour avec succès.');
    }

    /**
     * Remove the specified wine.
     */
    public function destroy(Bar $bar, Wine $wine): RedirectResponse
    {
        $this->authorize('manageWines', $bar);

        $wine->delete();

        return redirect()->route('bars.wines.index', $bar)
            ->with('success', 'Vin supprimé avec succès.');
    }
}

